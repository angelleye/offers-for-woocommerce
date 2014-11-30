<?php
/**
 * Admin view
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
<?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_settings';?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?>
    <a class="add-new-h2" href="edit.php?post_type=woocommerce_offer">Manage Offers</a>
    </h2>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=offers-for-woocommerce&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>">General Settings</a>
        <a href="?page=offers-for-woocommerce&tab=email_settings" class="nav-tab <?php echo ($active_tab == 'email_settings' || $active_tab == 'email_settings_new_offer' || $active_tab == 'email_settings_accepted_offer' || $active_tab == 'email_settings_counter_offer' || $active_tab == 'email_settings_declined_offer') ? 'nav-tab-active' : ''; ?>">Email Settings</a>
        <a href="?page=offers-for-woocommerce&tab=display_settings" class="nav-tab <?php echo $active_tab == 'display_settings' ? 'nav-tab-active' : ''; ?>">Display Settings</a>        
    </h2>

    <form method="post" action="options.php" id="woocommerce_offers_options_form">
    	<?php
        if( $active_tab == 'email_settings' )
        {
            settings_fields( 'offers_for_woocommerce_options_email' );
            do_settings_sections( 'offers_for_woocommerce_email_settings' );
        }
        if( $active_tab == 'email_settings_new_offer' )
        {
            settings_fields( 'offers_for_woocommerce_options_email_new_offer' );
            do_settings_sections( 'offers_for_woocommerce_email_settings_new_offer' );
        }
        if( $active_tab == 'email_settings_accepted_offer' )
        {
            settings_fields( 'offers_for_woocommerce_options_email_accepted_offer' );
            do_settings_sections( 'offers_for_woocommerce_email_settings_accepted_offer' );
        }
        if( $active_tab == 'email_settings_counter_offer' )
        {
            settings_fields( 'offers_for_woocommerce_options_email_counter_offer' );
            do_settings_sections( 'offers_for_woocommerce_email_settings_counter_offer' );
        }
        if( $active_tab == 'email_settings_declined_offer' )
        {
            settings_fields( 'offers_for_woocommerce_options_email_declined_offer' );
            do_settings_sections( 'offers_for_woocommerce_email_settings_declined_offer' );
        }
		elseif( $active_tab == 'display_settings' ) 
		{
            settings_fields( 'offers_for_woocommerce_options_display' );
            do_settings_sections( 'offers_for_woocommerce_display_settings' );
        } 
		else 
		{
            settings_fields( 'offers_for_woocommerce_options_general' );
            do_settings_sections( 'offers_for_woocommerce_general_settings' );
        } // end if/else
         
        submit_button();
		?>
</form>
</div>