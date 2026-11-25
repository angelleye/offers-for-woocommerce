<?php
/**
 * Offer completion.
 *
 * Marks an offer completed, and links it to its order, as soon as the order is
 * paid: when it reaches a paid status (wc_get_is_paid_statuses(), by default
 * processing and completed) or its payment completes.
 *
 * This used to run on `woocommerce_order_status_completed` from the admin
 * class, which only loads for administrators and offer managers. Offers stayed
 * countered after they were paid for, and orders that WooCommerce completes by
 * itself (virtual or downloadable products, background updates) never
 * completed their offer at all. These hooks are registered on every request.
 *
 * @package Angelleye_Offers_For_Woocommerce
 * @since   3.1.7
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('OFW_Offer_Completion')) {

    class OFW_Offer_Completion {

        const COMPLETED_STATUS = 'completed-offer';
        const OFFER_POST_TYPE  = 'woocommerce_offer';

        /**
         * Wire runtime hooks. Called once from the plugin bootstrap.
         */
        public static function register() {
            add_action('woocommerce_order_status_changed', array(__CLASS__, 'on_order_status_changed'), 10, 3);
            add_action('woocommerce_payment_complete', array(__CLASS__, 'complete_offers_for_order'));
        }

        /**
         * Complete the order's offers when it moves to a paid status.
         *
         * @param int    $order_id   Order ID.
         * @param string $old_status Previous status, without the wc- prefix.
         * @param string $new_status New status, without the wc- prefix.
         */
        public static function on_order_status_changed($order_id, $old_status, $new_status) {
            if (!in_array($new_status, wc_get_is_paid_statuses(), true)) {
                return;
            }

            self::complete_offers_for_order($order_id);
        }

        /**
         * Mark every offer in the order completed and link it to the order.
         *
         * Runs once per offer and order: an offer already completed with this
         * order is left alone, so a later status change (processing → completed)
         * doesn't add a second history entry.
         *
         * @param int $order_id Order ID.
         */
        public static function complete_offers_for_order($order_id) {
            $order = wc_get_order($order_id);
            if (!$order) {
                return;
            }

            $order_id = $order->get_id();

            foreach ($order->get_items() as $item) {
                if (!is_object($item) || !method_exists($item, 'get_meta')) {
                    continue;
                }

                $offer_id = absint($item->get_meta('Offer ID', true));
                if (!$offer_id || self::OFFER_POST_TYPE !== get_post_type($offer_id)) {
                    continue;
                }

                if (self::COMPLETED_STATUS === get_post_status($offer_id) && (int) get_post_meta($offer_id, 'offer_order_id', true) === $order_id) {
                    continue;
                }

                // Dokan sub-orders copy the parent's items; the parent order completes the offer.
                if (class_exists('OFW_Offer_Orders') && OFW_Offer_Orders::is_sub_order_of_offer_order($order, $offer_id)) {
                    continue;
                }

                $updated = wp_update_post(array(
                    'ID'          => $offer_id,
                    'post_status' => self::COMPLETED_STATUS,
                ));

                if (!$updated || is_wp_error($updated)) {
                    continue;
                }

                // The first order stays linked if a reusable offer is bought again.
                add_post_meta($offer_id, 'offer_order_id', $order_id, true);

                self::add_history_entry($offer_id, $order);

                if (!function_exists('ofw_manage_offer_single_use')) {
                    require_once OFW_PLUGIN_URL . '/includes/angelleye-functions.php';
                }

                ofw_manage_offer_single_use($offer_id);

                /**
                 * Fires after an offer is completed by a paid order.
                 *
                 * @param int      $offer_id Offer ID.
                 * @param WC_Order $order    Order.
                 */
                do_action('ofw_offer_completed', $offer_id, $order);
            }
        }

        /**
         * Add the "Completed" entry, with the related order, to the offer history.
         *
         * @param int      $offer_id Offer ID.
         * @param WC_Order $order    Order.
         */
        private static function add_history_entry($offer_id, $order) {
            $comment_text  = '<span>' . __('Updated - Status:', 'offers-for-woocommerce') . '</span> ' . __('Completed', 'offers-for-woocommerce');
            $comment_text .= '<p>' . __('Related Order', 'offers-for-woocommerce') . ': <a href="' . esc_url($order->get_edit_order_url()) . '">#' . $order->get_order_number() . '</a></p>';

            $comment_id = wp_insert_comment(array(
                'comment_post_ID'      => '',
                'comment_author'       => 'admin',
                'comment_author_email' => '',
                'comment_author_url'   => '',
                'comment_content'      => $comment_text,
                'comment_type'         => 'offers-history',
                'comment_parent'       => 0,
                'user_id'              => 1,
                'comment_author_IP'    => '127.0.0.1',
                'comment_agent'        => '',
                'comment_date'         => date('Y-m-d H:i:s', current_time('timestamp')),
                'comment_approved'     => 'post-trashed',
            ));

            if ($comment_id) {
                add_comment_meta($comment_id, 'angelleye_woocommerce_offer_id', $offer_id, true);
                add_comment_meta($comment_id, 'offer_status', '4', true);
            }
        }
    }
}
