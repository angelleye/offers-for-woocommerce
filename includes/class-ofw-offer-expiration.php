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
 * Expiration honors what the admin chose in the "Offer Expires" field
 * (stored as a naive `Y-m-d H:i` string the admin meant in site-local
 * wall-clock time):
 *   - Converted to GMT via WP core's get_gmt_from_date() so the result
 *     is correct regardless of the server's PHP timezone (common
 *     mismatch on managed hosting that runs PHP in UTC while the site
 *     is configured for a regional timezone).
 *   - If a specific time was chosen, that exact minute is the cut-off.
 *   - If no time was chosen (date-only or `00:00` midnight), the cut-off
 *     is end-of-day so the offer remains valid for the calendar day the
 *     customer was promised in the email.
 * Earlier code paths used `strtotime()` + `current_time('timestamp')`,
 * which only happened to align when PHP's TZ matched the site's. This
 * module makes the rule correct by construction.
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

            // Both sides are real (UTC) Unix timestamps, so the comparison
            // is timezone-correct regardless of PHP's default timezone.
            return $cutoff <= time();
        }

        /**
         * Real (UTC) Unix timestamp at which the offer becomes invalid. False
         * when the offer has no expiration set.
         *
         * The admin enters a naive `Y-m-d H:i` value in the "Offer Expires"
         * field that they mean as site-local wall-clock time. We hand it to
         * get_gmt_from_date() — the WP core helper that knows the site
         * timezone (timezone_string or gmt_offset) — so the conversion is
         * correct even when the server's PHP timezone differs from the
         * WordPress one (common on managed hosting that runs PHP in UTC).
         *
         * A date-only entry or an explicit midnight is promoted to end-of-day
         * so the customer gets the full calendar day promised in the email.
         * Any other time is honored exactly.
         *
         * @param int $offer_id
         * @return int|false
         */
        public static function get_expiration_cutoff($offer_id) {
            $raw = get_post_meta(absint($offer_id), self::META_KEY, true);
            if (empty($raw)) {
                return false;
            }

            $normalized = self::normalize_to_mysql(trim($raw));
            if (!$normalized) {
                return false;
            }

            // Date-only or midnight → end of that calendar day in site TZ.
            if (substr($normalized, 11) === '00:00:00') {
                $normalized = substr($normalized, 0, 10) . ' 23:59:59';
            }

            // get_gmt_from_date() converts a site-local datetime string to
            // its GMT equivalent. The trailing ' UTC' tells strtotime to read
            // the result as UTC, yielding a real Unix timestamp.
            $gmt_mysql = get_gmt_from_date($normalized, 'Y-m-d H:i:s');
            $timestamp = strtotime($gmt_mysql . ' UTC');

            return $timestamp ? $timestamp : false;
        }

        /**
         * Coerce the stored value to a canonical `Y-m-d H:i:s` string so
         * get_gmt_from_date() (which is regex-strict) accepts it.
         *
         * Returns false when the value doesn't look like a datetime at all.
         *
         * @param string $value
         * @return string|false
         */
        private static function normalize_to_mysql($value) {
            if (preg_match('/^(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})$/', $value)) {
                return $value;
            }
            if (preg_match('/^(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2})$/', $value, $m)) {
                return $m[1] . ' ' . $m[2] . ':00';
            }
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value . ' 00:00:00';
            }

            // Fallback for legacy or unexpected formats: parse permissively
            // with strtotime() and re-emit in canonical form. PHP's TZ here
            // affects only the string-to-timestamp step; the resulting
            // canonical string is what get_gmt_from_date() will then
            // re-interpret in the site timezone.
            $ts = strtotime($value);
            if (!$ts) {
                return false;
            }
            return gmdate('Y-m-d H:i:s', $ts);
        }

        /**
         * Cron callback: flip every past-date offer that is still in an active
         * status to `expired-offer` in a single pass.
         */
        public static function sweep_expired_offers() {
            global $wpdb;

            $post_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT pm.post_id
                   FROM {$wpdb->postmeta} pm
                   INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                  WHERE pm.meta_key = %s
                    AND pm.meta_value <> ''
                    AND p.post_type = %s
                    AND p.post_status NOT IN ('trash', %s)",
                self::META_KEY,
                self::OFFER_POST_TYPE,
                self::EXPIRED_STATUS
            ));

            if (empty($post_ids)) {
                return;
            }

            // Delegate the cut-off rule to is_expired() so there is exactly
            // one place that decides whether an offer has expired.
            foreach ($post_ids as $post_id) {
                $post_id = (int) $post_id;
                if (!self::is_expired($post_id)) {
                    continue;
                }

                wp_update_post(array(
                    'ID'          => $post_id,
                    'post_status' => self::EXPIRED_STATUS,
                ));

                do_action('ofw_offer_expired', $post_id);
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
