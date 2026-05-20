<?php
/**
 * Offer expiration enforcement.
 *
 * Single source of truth for "is this offer expired?" and the place where
 * that rule is enforced across the lifecycle:
 *
 *   - Scheduled cron sweep flips past-date offers to `expired-offer` status
 *     so the rest of the codebase can rely on post_status without depending
 *     on an admin loading the offers list (the legacy fallback).
 *   - Cart validation strips line items tied to expired offers on every
 *     cart/checkout render.
 *   - Checkout validation produces a hard error if an expired offer somehow
 *     survives into the order-placement step.
 *
 * Expiration uses end-of-day semantics (23:59:59 in the site timezone) to stay
 * consistent with the legacy admin sweep and the email-link handler — a single
 * cut-off avoids merchants seeing different behavior in different code paths.
 *
 * @package Angelleye_Offers_For_Woocommerce
 * @since   3.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('OFW_Offer_Expiration')) {

    class OFW_Offer_Expiration {

        const CRON_HOOK          = 'ofw_sweep_expired_offers';
        const META_KEY           = 'offer_expiration_date';
        const EXPIRED_STATUS     = 'expired-offer';
        const OFFER_POST_TYPE    = 'woocommerce_offer';
        const NOTICE_GROUP       = 'ofw_expired_offer';

        /**
         * Wire runtime hooks. Called once from the plugin bootstrap.
         */
        public static function register() {
            add_action(self::CRON_HOOK, array(__CLASS__, 'sweep_expired_offers'));

            // WC fires this on cart, checkout, and AJAX cart updates — the right
            // place to keep cart contents in sync with offer validity.
            add_action('woocommerce_check_cart_items', array(__CLASS__, 'remove_expired_cart_items'), 5);

            // WC's documented hook for adding errors that must block checkout.
            add_action('woocommerce_after_checkout_validation', array(__CLASS__, 'validate_checkout_offers'), 10, 2);
        }

        /**
         * Plugin activation — schedule the recurring sweep.
         */
        public static function activate() {
            if (!wp_next_scheduled(self::CRON_HOOK)) {
                wp_schedule_event(time() + MINUTE_IN_SECONDS, 'hourly', self::CRON_HOOK);
            }
        }

        /**
         * Plugin deactivation — unschedule the recurring sweep.
         */
        public static function deactivate() {
            wp_clear_scheduled_hook(self::CRON_HOOK);
        }

        /**
         * Whether the given offer has passed its expiration date.
         *
         * Returns false when no expiration meta is set so open-ended offers
         * continue to behave as they always have.
         *
         * @param int $offer_id
         * @return bool
         */
        public static function is_expired($offer_id) {
            $offer_id = absint($offer_id);
            if (!$offer_id) {
                return false;
            }

            $cutoff = self::get_expiration_cutoff($offer_id);
            if (!$cutoff) {
                return false;
            }

            return $cutoff <= current_time('timestamp', 0);
        }

        /**
         * Site-local Unix timestamp at which the offer becomes invalid (inclusive
         * of the calendar day stored in meta). False when the offer has no
         * expiration set.
         *
         * @param int $offer_id
         * @return int|false
         */
        public static function get_expiration_cutoff($offer_id) {
            $raw = get_post_meta(absint($offer_id), self::META_KEY, true);
            if (empty($raw)) {
                return false;
            }

            $parsed = strtotime($raw);
            if (!$parsed) {
                return false;
            }

            return strtotime(date('Y-m-d 23:59:59', $parsed));
        }

        /**
         * Cron callback: flip every past-date offer that is still in an active
         * status to `expired-offer` in a single pass.
         */
        public static function sweep_expired_offers() {
            global $wpdb;

            $now_mysql = current_time('mysql');

            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT pm.post_id, pm.meta_value
                   FROM {$wpdb->postmeta} pm
                   INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                  WHERE pm.meta_key = %s
                    AND pm.meta_value <> ''
                    AND p.post_type = %s
                    AND p.post_status NOT IN ('trash', %s)",
                self::META_KEY,
                self::OFFER_POST_TYPE,
                self::EXPIRED_STATUS
            ), ARRAY_A);

            if (empty($rows)) {
                return;
            }

            foreach ($rows as $row) {
                $parsed = strtotime($row['meta_value']);
                if (!$parsed) {
                    continue;
                }

                $cutoff_mysql = date('Y-m-d 23:59:59', $parsed);
                if ($cutoff_mysql > $now_mysql) {
                    continue;
                }

                wp_update_post(array(
                    'ID'          => (int) $row['post_id'],
                    'post_status' => self::EXPIRED_STATUS,
                ));

                do_action('ofw_offer_expired', (int) $row['post_id']);
            }
        }

        /**
         * Remove any cart item whose linked offer has expired and surface a
         * single grouped notice. Bail safely if WC isn't fully booted.
         */
        public static function remove_expired_cart_items() {
            if (!function_exists('WC') || is_null(WC()->cart)) {
                return;
            }

            $removed = 0;
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                if (empty($cart_item['woocommerce_offer_id'])) {
                    continue;
                }
                if (!self::is_expired($cart_item['woocommerce_offer_id'])) {
                    continue;
                }

                WC()->cart->remove_cart_item($cart_item_key);
                $removed++;
            }

            if ($removed > 0 && !wc_has_notice(self::expired_notice_text(), 'error')) {
                wc_add_notice(self::expired_notice_text(), 'error');
            }
        }

        /**
         * Checkout-time validation: add a hard error so WC cancels the order.
         *
         * @param array     $posted_data
         * @param WP_Error  $errors
         */
        public static function validate_checkout_offers($posted_data, $errors) {
            if (!function_exists('WC') || is_null(WC()->cart) || !is_wp_error($errors)) {
                return;
            }

            $has_expired = false;
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                if (empty($cart_item['woocommerce_offer_id'])) {
                    continue;
                }
                if (!self::is_expired($cart_item['woocommerce_offer_id'])) {
                    continue;
                }

                WC()->cart->remove_cart_item($cart_item_key);
                $has_expired = true;
            }

            if ($has_expired) {
                $errors->add('ofw_offer_expired', self::expired_notice_text());
            }
        }

        /**
         * Customer-facing copy. Centralized so the same wording appears in
         * cart, checkout, and order-failed paths.
         */
        private static function expired_notice_text() {
            return __(
                'An accepted offer in your cart has expired and was removed. Please submit a new offer to complete this purchase.',
                'offers-for-woocommerce'
            );
        }
    }
}
