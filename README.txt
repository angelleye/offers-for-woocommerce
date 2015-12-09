=== Offers for WooCommerce ===
Contributors: angelleye
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T962XWAC2HHZN
Tags: woocommerce, offers, negotiation
Requires at least: 3.8
Tested up to: 4.4
Stable tag: 1.1.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds the power of negotiation to your WooCommerce store.

== Description ==

= Video Overview =
[youtube https://www.youtube.com/watch?v=3xb0Tfnx16o]

= Introduction =

Provide the ability for customers to submit offers for items in a WooCommerce store.

 * Adds a “Make an Offer” button to products on your WooCommerce web store.
 * Provides a “Make an Offer” form where users can enter the QTY and price for the item they’re interested in as well as their contact information.
 * Email notifications for new offers, accepted offers, counter offers, and declined offers are sent to both the buyer and the site owner.
 * Manage offers from your WordPress control panel through WooCommerce -> Offers just like you would with your WooCommerce orders.
 * Options to enable/disable offers at the product level as well as options for handling inventory tracked items based on how the WooCommerce settings for back-orders are configured.

= Localization =
Offers for WooCommerce was developed with localization in mind and is ready for translation.

= Get Involved =

Developers can contribute to the source code on the [Offers for WooCommerce GitHub repository](https://github.com/angelleye/offers-for-woocommerce).

== Installation ==

= Minimum Requirements =

* WooCommerce 2.1 or higher

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of Offers for WooCommerce, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type Offers for WooCommerce and click Search Plugins. Once you've found our plugin you can view details about it such as the the rating and description. Most importantly, of course, you can install it by simply clicking Install Now.

= Manual Installation =

1. Unzip the files and upload the folder into your plugins folder (/wp-content/plugins/) overwriting older versions if they exist
2. Activate the plugin in your WordPress admin area.

= Updating =

Automatic updates should work great for you.  As always, though, we recommend backing up your site prior to making any updates just to be sure nothing goes wrong.

== Screenshots ==

1. Make an Offer button displayed on product details page.
2. Make an Offer tab on product details with a form to submit offer details.
3. Manage Offers list view from the WordPress admin panel (WooCommerce -> Offers)
4. Manage Offer details page where you can accept an offer, decline an offer, or submit a counter-offer.
5. New offer email notification.
6. Accepted offer email notification.
7. Counter offer email notification.
8. Declined offer email notification.

== Frequently Asked Questions ==

= Why would I want to allow buyers to submit offers? =

* If you allow your buyers to submit an offer to you, this opens a direct line of communication with an interested buyer.
* Negotiation tactics come into play when people submit offers.  For example, you might be able to sell 20 of an item to somebody that originally requested 15 if they're trying to meet a particular cost.
* People like to feel like they've "won" something.  If you accept an offer from an interested buyer this feeling will entice them to quickly complete checkout for the accepted offer.

= My theme does not use tabs within the products details page, and the Make Offer form is not displaying correctly.  How do I fix this? =

1. In your WordPress admin panel, go to Settings -> Offers for WooCommerce.
2. Click the Display Settings tab.
3. Set the "Form Display Type" to Lightbox.
4. Click "Save Changes" at the bottom of the form.

= How can I move the location of the Make Offer button? =

1. In your WordPress admin panel, go to Settings -> Offers for WooCommerce.
2. Click the Display Settings tab.
3. Set the "Button Position" to the location you would like.
4. Click "Save Changes" at the bottom of the form.

= How can I enable / disable offers on multiple products at once? =

1. In your WordPress admin panel, go to Settings -> Offers for WooCommerce.
2. Click the Tools tab.
3. Set the Action to Enable or Disable.
4. Set the Target based on the products you would like to adjust.
5. If you choose "Where" from the Target list, you will then choose an option under the "Where" list as well as enter your value accordingly.
6. Click the "Process" button to make the adjustment.

= How do I retract or adjust an offer? =

1. In your WordPress admin panel, go to WooCommerce -> Offers.
2. Find the offer you would like to adjust by using the search or available filters and click View Details.
3. Make any adjustments you need to the Counter Offer and/or Offer Status details.
4. Optionally, add an "Offer Note to Buyer" to inform the buyer why the adjustment is being made.
5. Click the "Update" button to save the adjustment.

= The email notifications are not getting sent.  Why? =

* Make sure to check in WooCommerce -> Settings, and then look in the Emails tab.  Click into the links for New Offer, New counteroffer, Offer received, etc. and enable the ones you want to get sent.

= Where can I find more documentation? =

* [Installation and Activation](https://www.angelleye.com/offers-for-woocommerce-user-guide/#section-2)
* [Enabling and Disabling Offers for Products](https://www.angelleye.com/offers-for-woocommerce-user-guide/#section-3)
* [Managing Offers](https://www.angelleye.com/offers-for-woocommerce-user-guide/#section-4)
* [Plugin Settings](https://www.angelleye.com/offers-for-woocommerce-user-guide/#section-5)
* [Email Settings](https://www.angelleye.com/offers-for-woocommerce-user-guide/#section-7)
* [Additional Plugin Tools](https://www.angelleye.com/offers-for-woocommerce-user-guide/#section-6)

== Changelog ==

= 1.1.4 - 07.08.2015 =
* Tweak - Adds a "close" icon to the lightbox offer form display.
* Tweak - Displays the offer status on the grid view at all times (no longer need hover over the row to see).
* Tweak - CSS adjustments to offer email notifications.
* Tweak - Adds the product title/name to the manage offers list.
* Tweak - Adjusts the way variable products are handled with parent inventory.  Variable stock now takes precedence over parent stock.
* Tweak - Clicking the offer tab on a product page no longer automatically scrolls down in order to stay consistent with other tabs.
* Fix - Resolves PHP warning when product price is blank.
* Fix - Resolves an issue where core WP functions would not trigger correctly for some users.
* Fix - Adds default options for settings in case no settings have been saved to avoid PHP notices.
* Fix - Adjustments to translation logic to resolve an issue where translations would not be triggered.
* Fix - Resolves an issue where the offer button from shop/category pages would return a 404 page if permanlinks were not enabled.
* Feature - If a user is logged in to the site, the offer form will be pre-populated with available user profile data.
* Feature - Adds the ability to enable/disable offers based on WordPress user role.
* Feature - Adds option to enable offers only for logged in users on the site.
* Feature - Adds German translation file (translation by Emanuel Plesa).
* Feature - Adds filter hooks to adjust offer form labels or add custom messages.
* Feature - Adds an option to set a default number of days in the future for setting an expiration accepted/countered offers.
* Feature - Akismet compatibility.  Offer submissions are now filtered for spam by the Akismet system.

= 1.1.3 - 05.31.2015 =
* Tweak - Adjusts the email template system so that templates can be overridden from within a theme.
* Tweak - Moves the plugin action links to the Description column on the Plugins page because there is more room there.
* Tweak - Removes the offer button from products that are free (0.00 price).
* Fix - Resolves localization / translation failure when using language files.
* Feature - Adds an option to place the offer button directly to the right of the add to cart button.
* Feature - Adds the ability to enable/disable fields that are displayed on the offer form.

= 1.1.2 - 05.11.2015 =
* Tweak - Adjusts localization so the plugin is ready for translation.
* Tweak - Moves plugin action links to Description column in Plugins screen.
* Fix - Resolves a conflict with some 3rd party plugins.
* Fix - Resolves various PHP notices and other minor bugs.
* Cleanup - Removes unused functions.
* Cleanup - Minor CSS adjustments.

= 1.1.1 - 04.30.2015 =
* Tweak - Adds option for handling available offer QTY based on WooCommerce inventory back-order settings.
* Tweak - Hides the final offer option unless you are submitting a counter-offer.
* Tweak - Adds validation to the expiration date so you cannot set a date in the past.
* Fix - Various bug fixes to eliminate PHP notices and conflicts with other plugins.
* Fix - Adjusts jQuery spinner icon to work with WordPress 4.2.

= 1.1.0 - 04/16/2015 =
* Tweak - Adjusts offer search to return results from all offer detail data (not just the title).
* Tweak - If a product is set to "sold individually" users will not be able to enter a QTY when submitting an offer.
* Fix - Ensures the offer lightbox window will not be displayed when it should not be.
* Fix - Resolves a bug in the counter offer emails.
* Fix - Adjusts CSS to resolve issues with text floating on top of product image in the offer details screen.
* Fix - Resolves a PHP error occurring in the WebHooks tab of WooCommerce settings.
* Feature - Adds tools for bulk edit of products to enable/disable offers.
* Feature - Adds an expiration date option to counter offers.
* Feature - Adds a Final Offer option to counter offers.
* Feature - Option to move the offer button on product pages to various locations on the page.
* Feature - Adds the option to place an offer on hold.

= 1.0.1 - 03/24/2015 =
* Fix - Adds system admin as default email when no receivers are set.
* Fix - Resolves HTML5 validation errors with output.

= 1.0.0 - 03/24/2015 =
* Tweak - Disable offer button for external / free products.
* Tweak - Consider inventory when handling offers.
* Tweak - Filter offer comments from the WordPress dashboard "at a glance" section.
* Tweak - Validate cart offer items to ensure the offer is still available and eligible.
* Tweak - WooCommerce 2.3 compatibility adjustments.
* Fix - Resolves issue with plain text emails and eliminates "file was not found" error.
* Fix - Resolves an issue causing the CC of email addresses for offer notifications to fail.
* Fix - Various bug fixes, CSS, and jQuery adjustments.
* Fix - Resolves conflict with the WooThemes WishList plugin.
* Feature - Display currency symbol using WooCommerce setting.
* Feature - Adds a complete uninstaller.
* Feature - Adds an option to use embed the offer form in a tab on the product page or within a lightbox window.

= 0.1.0 - 02/08/2015 =
* Initial Beta release.