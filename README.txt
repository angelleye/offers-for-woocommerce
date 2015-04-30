=== Offers for WooCommerce ===
Contributors: angelleye
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T962XWAC2HHZN
Tags: woocommerce, offers, negotiation
Requires at least: 3.8
Tested up to: 4.2.1
Stable tag: 1.1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds the power of negotiation to your WooCommerce store.

== Description ==

= Introduction =

Provide the ability for customers to submit offers for items in a WooCommerce store.

 * Adds a “Make an Offer” button to products on your WooCommerce web store.
 * Provides a “Make an Offer” form where users can enter the QTY and price for the item they’re interested in as well as their contact information.
 * Email notifications for new offers, accepted offers, counter offers, and declined offers are sent to both the buyer and the site owner.
 * Manage offers from your WordPress control panel through WooCommerce -> Offers just like you would with your WooCommerce orders.
 * Options to enable/disable offers at the product level as well as options for handling inventory tracked items based on how the WooCommerce settings for back-orders are configured.

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

== Frequently Asked Questions ==

= Why would I want to allow buyers to submit offers? =

* If you allow your buyers to submit an offer to you, this opens a direct line of communication with an interested buyer.
* Negotiation tactics come into play when people submit offers.  For example, you might be able to sell 20 of an item to somebody that originally requested 15 if they're trying to meet a particular cost.

== Changelog ==

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