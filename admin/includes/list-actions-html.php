<?php
/**
 * Admin List View - Actions Column Html
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @since	  0.1.0
 * @package   Angelleye_Offers_For_Woocommerce_Admin
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 * @copyright 2014 AngellEYE
 */
?>
<div class="woocommerce-offer-list-actions-wrap">
	<a href="<?php if(isset($view_detail_link)) { echo $view_detail_link; } else { echo 'javascript:;'; } ?>" class="button woocommerce-offer-view-details-link"><span class="dashicons dashicons-visibility"></span> View Details</a>
</div>