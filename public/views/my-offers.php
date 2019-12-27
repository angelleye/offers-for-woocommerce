<?php
if (!defined('ABSPATH')) {
    exit;
}

$my_offers_columns = apply_filters('ofw_my_account_my_offers_columns', array(
    'offer_product_title' => __('Product', 'offers-for-woocommerce'),
    'offer_amount' => __('Amount', 'offers-for-woocommerce'),
    'offer_price_per' => __('Price Per', 'offers-for-woocommerce'),
    'offer_quantity' => __('Quantity', 'offers-for-woocommerce'),
    'offer-status' => __('Status', 'offers-for-woocommerce'),
    'offer-action' => __('Action', 'offers-for-woocommerce'),
        ));

$customer_offers = get_posts(apply_filters('ofw_my_account_my_offers_query', array(
    'numberposts' => -1,
    'author' => get_current_user_id(),
    'post_type' => 'woocommerce_offer',
    'post_status' => 'any'
        )));

if ($customer_offers) :
    ?>
    <h2><?php echo apply_filters('ofw_my_account_my_offers_title', __('Recent Offers', 'offers-for-woocommerce')); ?></h2>
    <table class="shop_table shop_table_responsive my_account_orders">
        <thead>
            <tr>
                <?php foreach ($my_offers_columns as $column_id => $column_name) : ?>
                    <th class="<?php echo esc_attr($column_id); ?>"><span class="nobr"><?php echo esc_html($column_name); ?></span></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($customer_offers as $customer_order) :
                ?>
                <tr class="order">
                    <?php foreach ($my_offers_columns as $column_id => $column_name) : ?>
                        <td class="<?php echo esc_attr($column_id); ?>" data-title="<?php echo esc_attr($column_name); ?>">
                            <?php
                            $offer_args = array();
                            $post_id = $customer_order->ID;
                            $offer_status = get_post_status($post_id);
                            if (empty($offer_status)) {
                                return false;
                            }
                            $post_status = $offer_status;
                            $product_id = get_post_meta($post_id, 'orig_offer_product_id', true);
                            $variant_id = get_post_meta($post_id, 'orig_offer_variation_id', true);
                            $offer_uid = get_post_meta($post_id, 'offer_uid', true);
                            $offer_final_offer = get_post_meta($post_id, 'offer_final_offer', true);                            
                            if('product' === get_post_type($product_id)){
                                $product = ( $variant_id ) ? wc_get_product($variant_id) : wc_get_product($product_id);                            
                                $product_title = get_the_title($product_id);
                                $offer_args['product_url'] = $product->get_permalink();
                                $offer_args['offer_id'] = $post_id;
                                $offer_args['offer_uid'] = $offer_uid;
                                $offer_args['final_offer'] = $offer_final_offer;
                                $expiration_date = get_post_meta($post_id, 'offer_expiration_date', true);
                                $expiration_date_formatted = ($expiration_date) ? date("Y-m-d 23:59:59", strtotime($expiration_date)) : FALSE;
                                switch ($column_id) {
                                    case 'offer_name' :
                                        $val = get_post_meta($post_id, 'offer_name', true);
                                        echo stripslashes($val);
                                        break;
                                    case 'offer_product_title' :                                  
                                        if ($product_title) {
                                            if(version_compare(WC_VERSION, '3.0', '<')){
                                                $product_type = $product->product_type;
                                                $pproduct_id  = $product->id;
                                            } 
                                            else{
                                                $product_type = $product->get_type();
                                                $pproduct_id  = $product->get_id();
                                            }
                                            if ($product_type == 'variation') {
                                                $_product = new WC_Product_Variation($variant_id);
                                                if(get_post_status($pproduct_id) == 'trash'){
                                                    echo sprintf('%s', $_product->get_title());
                                                } else {
                                                    echo apply_filters('ofw_product_url', sprintf('<a title="%s" target="_blank" href="%s">%s</a>', __('View Product', 'offers-for-woocommerce'), esc_url($_product->get_permalink()), $_product->get_title()));
                                                }
                                            } else {
                                                if(get_post_status($pproduct_id) == 'trash'){
                                                    echo sprintf('%s', get_the_title($product_id));
                                                } else {
                                                    echo apply_filters('ofw_product_url', sprintf('<a title="%s" target="_blank" href="%s">%s</a>', __('View Product', 'offers-for-woocommerce'), esc_url(get_the_permalink($product_id)), get_the_title($product_id)));
                                                }
                                            }
                                        } else {
                                            echo '<em>' . __('Not Found', 'offers-for-woocommerce') . '</em>';
                                        }

                                        break;
                                    case 'offer_quantity' :
                                        if ($post_status == 'buyercountered-offer') {
                                            $val = get_post_meta($post_id, 'offer_buyer_counter_quantity', true);
                                        } else {
                                            $val = get_post_meta($post_id, 'offer_quantity', true);
                                        }
                                        echo ($val != '') ? $val : '0';
                                        break;
                                    case 'offer_price_per' :
                                        if ($post_status == 'buyercountered-offer') {
                                            $val = get_post_meta($post_id, 'offer_buyer_counter_price_per', true);
                                        } else {
                                            $val = get_post_meta($post_id, 'offer_price_per', true);
                                        }
                                        $val = ($val != '') ? $val : '0';
                                        echo wc_price($val);
                                        break;
                                    case 'offer_amount' :
                                        if ($post_status == 'buyercountered-offer') {
                                            $val = get_post_meta($post_id, 'offer_buyer_counter_amount', true);
                                        } else {
                                            $val = get_post_meta($post_id, 'offer_amount', true);
                                        }
                                        $val = ($val != '') ? $val : '0';
                                        echo wc_price($val);
                                        break;
                                    case 'offer-status' :
                                        switch ($post_status) {
                                            case 'publish' :
                                                echo __('Pending', 'offers-for-woocommerce');
                                                break;
                                            case 'countered-offer' :
                                                echo __('Countered', 'offers-for-woocommerce');
                                                break;
                                            case 'accepted-offer' :
                                                echo __('Accepted', 'offers-for-woocommerce');
                                                break;
                                            case 'declined-offer' :
                                                echo __('Declined', 'offers-for-woocommerce');
                                                break;
                                            case 'buyercountered-offer' :
                                                echo __('Buyer Countered', 'offers-for-woocommerce');
                                                break;
                                            case 'trash' :
                                                echo __('Trashed', 'offers-for-woocommerce');
                                                break;
                                            case 'completed-offer' :
                                                echo __('Completed', 'offers-for-woocommerce');
                                                break;
                                            case 'on-hold-offer' :
                                                echo __('On Hold', 'offers-for-woocommerce');
                                                break;
                                            case 'expired-offer' :
                                                echo __('Expired', 'offers-for-woocommerce');
                                                break;
                                            default :
                                                echo $post_status;
                                                break;
                                        }
                                        break;
                                    case 'offer-action' :
                                        if (($expiration_date_formatted) && ($expiration_date_formatted <= (date("Y-m-d H:i:s", current_time('timestamp', 0))) )) {

                                        } else {
                                            $post_status = apply_filters('ofw_admin_created_offer_status', $post_status, $post_id);
                                            switch ($post_status) {
                                                case 'countered-offer' :
                                                    ?>
                                                    <a class="button" href="<?php echo $offer_args['product_url']; ?><?php echo ( strpos($offer_args['product_url'], '?') ) ? '&' : '?'; ?><?php echo '__aewcoapi=1&woocommerce-offer-id=' . $offer_args['offer_id'] . '&woocommerce-offer-uid=' . $offer_args['offer_uid']; ?>"><?php echo __('Click to Pay', 'offers-for-woocommerce'); ?></a>
                                                    <?php
                                                    if (isset($offer_args['final_offer']) && $offer_args['final_offer'] == '1') {
                                                        //echo '<strong>' . __('This is a final offer.', 'offers-for-woocommerce') . '</strong>';
                                                    } else {
                                                        ?>
                                                        <a class="button" href="<?php echo $offer_args['product_url']; ?><?php echo ( strpos($offer_args['product_url'], '?') ) ? '&' : '?'; ?><?php echo 'aewcobtn=1&offer-pid=' . $offer_args['offer_id'] . '&offer-uid=' . $offer_args['offer_uid']; ?>"><?php echo __('Click to Counter', 'offers-for-woocommerce'); ?></a>
                                                    <?php } ?>
                                                    <?php
                                                    break;
                                                case 'accepted-offer' :
                                                    ?>
                                                    <a class="button" href="<?php echo $offer_args['product_url']; ?><?php echo ( strpos($offer_args['product_url'], '?') ) ? '&' : '?'; ?><?php echo '__aewcoapi=1&woocommerce-offer-id=' . $offer_args['offer_id'] . '&woocommerce-offer-uid=' . $offer_args['offer_uid']; ?>"><?php echo __('Click to Pay', 'offers-for-woocommerce'); ?></a>
                                                    <?php
                                                    break;
                                            }
                                    }
                                }
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php
            endforeach;
            ?>
        </tbody>
    </table>
<?php endif; ?>
