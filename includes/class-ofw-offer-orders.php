<?php
/**
 * Links between offers and the orders placed for them.
 *
 * An order records its offer as the "Offer ID" meta of the order item. This
 * module reads that link in both directions:
 *
 *   - The orders placed for an offer, whatever their status, for the offer
 *     screens. The offer itself only stores `offer_order_id` once an order
 *     has completed it.
 *   - The offer behind an order item. On the wp-admin and Dokan order screens
 *     the raw "Offer ID" meta row is hidden and "Bought from offer #123" is
 *     shown under the item, linked for users who can manage the offer. Buyer-facing views and emails show "Offer: #123".
 *     The wp-admin orders list gets an "Offer" column.
 *
 * @package Angelleye_Offers_For_Woocommerce
 * @since   3.1.7
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('OFW_Offer_Orders')) {

    class OFW_Offer_Orders {

        const ITEM_META_KEY   = 'Offer ID';
        const OFFER_POST_TYPE = 'woocommerce_offer';

        /**
         * Wire runtime hooks. Called once from the plugin bootstrap.
         */
        public static function register() {
            add_filter('woocommerce_order_item_display_meta_key', array(__CLASS__, 'item_meta_key'), 10, 3);
            add_filter('woocommerce_order_item_display_meta_value', array(__CLASS__, 'item_meta_value'), 10, 3);

            // Order screens (wp-admin, and Dokan Pro's vendor order page) use these.
            add_filter('woocommerce_hidden_order_itemmeta', array(__CLASS__, 'hide_item_meta'));
            add_action('woocommerce_after_order_itemmeta', array(__CLASS__, 'render_item_offer'), 10, 2);

            if (is_admin()) {
                // Orders list, with HPOS and with posts storage.
                add_filter('manage_woocommerce_page_wc-orders_columns', array(__CLASS__, 'add_orders_list_column'));
                add_action('manage_woocommerce_page_wc-orders_custom_column', array(__CLASS__, 'render_orders_list_column'), 10, 2);
                add_filter('manage_edit-shop_order_columns', array(__CLASS__, 'add_orders_list_column'));
                add_action('manage_shop_order_posts_custom_column', array(__CLASS__, 'render_orders_list_column'), 10, 2);
            }
        }

        /**
         * IDs of the orders placed for an offer, newest first.
         *
         * Order items live in the same tables with and without HPOS, so one query
         * covers both. Dokan sub-orders copy the item, so they are included too.
         *
         * @param int $offer_id Offer ID.
         * @return int[]
         */
        public static function get_order_ids($offer_id) {
            global $wpdb;

            $offer_id = absint($offer_id);
            if (!$offer_id) {
                return array();
            }

            $order_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT DISTINCT items.order_id
                   FROM {$wpdb->prefix}woocommerce_order_items items
                   INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta meta ON meta.order_item_id = items.order_item_id
                  WHERE meta.meta_key = %s
                    AND meta.meta_value = %s
                  ORDER BY items.order_id DESC",
                self::ITEM_META_KEY,
                (string) $offer_id
            ));

            return array_map('absint', $order_ids);
        }

        /**
         * The orders placed for an offer, newest first, leaving out trashed orders.
         *
         * @param int $offer_id Offer ID.
         * @return WC_Order[]
         */
        public static function get_orders($offer_id) {
            $orders = array();

            foreach (self::get_order_ids($offer_id) as $order_id) {
                $order = wc_get_order($order_id);
                if ($order instanceof WC_Order && 'trash' !== $order->get_status()) {
                    $orders[] = $order;
                }
            }

            return $orders;
        }

        /**
         * Offer IDs recorded on an order's items.
         *
         * @param WC_Order $order Order.
         * @return int[]
         */
        public static function get_offer_ids_for_order($order) {
            $offer_ids = array();

            if (!$order instanceof WC_Order) {
                return $offer_ids;
            }

            foreach ($order->get_items() as $item) {
                $offer_id = is_object($item) && method_exists($item, 'get_meta') ? absint($item->get_meta(self::ITEM_META_KEY, true)) : 0;
                if ($offer_id) {
                    $offer_ids[] = $offer_id;
                }
            }

            return array_values(array_unique($offer_ids));
        }

        /**
         * Whether the order is a sub-order whose parent also carries the offer.
         *
         * Dokan splits a multi-vendor order into sub-orders and copies the items,
         * so the offer appears on both. The parent is the order that counts.
         *
         * @param WC_Order $order    Order.
         * @param int      $offer_id Offer ID.
         * @return bool
         */
        public static function is_sub_order_of_offer_order($order, $offer_id) {
            if (!$order instanceof WC_Order || !$order->get_parent_id()) {
                return false;
            }

            $parent = wc_get_order($order->get_parent_id());

            return $parent instanceof WC_Order && in_array(absint($offer_id), self::get_offer_ids_for_order($parent), true);
        }

        /**
         * URL to manage an offer for the current user, or '' when they can't.
         *
         * @param int           $offer_id Offer ID.
         * @param WC_Order_Item $item     Order item the offer is shown for, if any.
         * @return string
         */
        public static function get_offer_url($offer_id, $item = null) {
            $url = '';

            if (is_admin() && self::OFFER_POST_TYPE === get_post_type($offer_id) && current_user_can('edit_post', $offer_id)) {
                $url = (string) get_edit_post_link($offer_id, 'raw');
            }

            /**
             * Filter the link to an offer shown with an order item.
             *
             * Return '' to show the offer number without a link.
             *
             * @param string             $url      Link, or ''.
             * @param int                $offer_id Offer ID.
             * @param WC_Order_Item|null $item     Order item.
             */
            return (string) apply_filters('ofw_order_item_offer_url', $url, $offer_id, $item);
        }

        /**
         * Hide the raw "Offer ID" row on the order screens; render_item_offer() shows it instead.
         *
         * @param array $hidden Hidden item meta keys.
         * @return array
         */
        public static function hide_item_meta($hidden) {
            $hidden   = (array) $hidden;
            $hidden[] = self::ITEM_META_KEY;

            return $hidden;
        }

        /**
         * Show the offer under an order item on the order screens.
         *
         * @param int           $item_id Order item ID.
         * @param WC_Order_Item $item    Order item.
         */
        public static function render_item_offer($item_id, $item) {
            if (!is_object($item) || !method_exists($item, 'get_meta')) {
                return;
            }

            $offer_id = absint($item->get_meta(self::ITEM_META_KEY, true));
            if (!$offer_id) {
                return;
            }

            $url    = self::get_offer_url($offer_id, $item);
            $number = '#' . $offer_id;
            ?>
            <div class="ofw-order-item-offer" style="align-items:center;display:flex;flex-wrap:wrap;font-size:12px;gap:6px;line-height:1.5;margin:6px 0 0;">
                <span class="ofw-order-item-offer__label" style="color:#757575;"><?php echo esc_html__('Bought from offer', 'offers-for-woocommerce'); ?></span>
                <?php if ($url) { ?>
                    <a class="ofw-order-item-offer__number" href="<?php echo esc_url($url); ?>" style="color:var(--dokan-link-color, #2271b1);font-weight:600;text-decoration:underline;"><?php echo esc_html($number); ?></a>
                <?php } else { ?>
                    <span class="ofw-order-item-offer__number" style="font-weight:600;"><?php echo esc_html($number); ?></span>
                <?php } ?>
            </div>
            <?php
        }

        /**
         * Label the "Offer ID" item meta as "Offer".
         *
         * WooCommerce adds the colon itself.
         *
         * @param string        $display_key Display key.
         * @param WC_Meta_Data  $meta        Meta.
         * @param WC_Order_Item $item        Order item.
         * @return string
         */
        public static function item_meta_key($display_key, $meta = null, $item = null) {
            if (is_object($meta) && isset($meta->key) && self::ITEM_META_KEY === $meta->key) {
                return __('Offer', 'offers-for-woocommerce');
            }

            return $display_key;
        }

        /**
         * Show the "Offer ID" item meta as "#123", linked when the user can manage the offer.
         *
         * @param string        $display_value Display value.
         * @param WC_Meta_Data  $meta          Meta.
         * @param WC_Order_Item $item          Order item.
         * @return string
         */
        public static function item_meta_value($display_value, $meta = null, $item = null) {
            if (!is_object($meta) || !isset($meta->key) || self::ITEM_META_KEY !== $meta->key) {
                return $display_value;
            }

            $offer_id = absint($meta->value);
            if (!$offer_id) {
                return $display_value;
            }

            $label = '#' . $offer_id;
            $url   = self::get_offer_url($offer_id, $item);

            return $url ? '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>' : esc_html($label);
        }

        /**
         * Add the "Offer" column after the order status.
         *
         * @param array $columns Columns.
         * @return array
         */
        public static function add_orders_list_column($columns) {
            $added = array();

            foreach ($columns as $key => $label) {
                $added[$key] = $label;
                if ('order_status' === $key) {
                    $added['ofw_offer'] = __('Offer', 'offers-for-woocommerce');
                }
            }

            if (!isset($added['ofw_offer'])) {
                $added['ofw_offer'] = __('Offer', 'offers-for-woocommerce');
            }

            return $added;
        }

        /**
         * Render the "Offer" column.
         *
         * @param string       $column           Column key.
         * @param int|WC_Order $order_or_post_id Order (HPOS) or post ID.
         */
        public static function render_orders_list_column($column, $order_or_post_id) {
            if ('ofw_offer' !== $column) {
                return;
            }

            $order     = $order_or_post_id instanceof WC_Order ? $order_or_post_id : wc_get_order($order_or_post_id);
            $offer_ids = self::get_offer_ids_for_order($order);

            if (empty($offer_ids)) {
                echo '<span aria-hidden="true">&ndash;</span>';
                return;
            }

            $links = array();
            foreach ($offer_ids as $offer_id) {
                $url     = self::get_offer_url($offer_id);
                $links[] = $url ? '<a href="' . esc_url($url) . '">#' . esc_html($offer_id) . '</a>' : '#' . esc_html($offer_id);
            }

            echo wp_kses_post(implode(', ', $links));
        }
    }
}
