=== Offers for WooCommerce ===
Contributors: angelleye
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T962XWAC2HHZN
Tags: woocommerce, offers, negotiation
Requires at least: 3.8
Tested up to: 4.9
Stable tag: 1.4.8.1
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

= 1.4.8.2 - xx.xx.2017 =
* Tweak - Adjustments to the alignment of Offer button with Add to Cart and PayPal for WooCommerce buttons. ([#354](https://github.com/angelleye/offers-for-woocommerce/issues/354))
* Fix - Resolves an issue with the Bulk Update tool failing to properly enable/disable offers based on featured products. ([#358](https://github.com/angelleye/offers-for-woocommerce/issues/358))

= 1.4.8.1 - 11.14.2017 =
* Feature - Adds action hook for when offer records are deleted. ([#356](https://github.com/angelleye/offers-for-woocommerce/issues/356))

= 1.4.8 - 11.13.2017 =
* Feature - Adds WooCommerce Product Bundle compatibility. ([#350](https://github.com/angelleye/offers-for-woocommerce/issues/350))
* Feature - Adds language file for Brazil / Portuguese. ([#336](https://github.com/angelleye/offers-for-woocommerce/issues/336))
* Feature - Adds option in settings to disable admin notifications for auto-declined offers. ([#349](https://github.com/angelleye/offers-for-woocommerce/issues/349))
* Feature - Adds user requested features: Counter-offer Alert, Minimum Offer Price, Form Hooks. ([#342](https://github.com/angelleye/offers-for-woocommerce/issues/342))
* Feature - Adds option to automatically disable offers on products that are on sale. ([#344](https://github.com/angelleye/offers-for-woocommerce/issues/344))
* Tweak - Removes unnecessary semicolon. ([#317](https://github.com/angelleye/offers-for-woocommerce/issues/317))
* Tweak - Adjusts button alignment on the product page. ([#325](https://github.com/angelleye/offers-for-woocommerce/issues/325))
* Tweak - Adjustments to make variation items display their attributes accordingly. ([#328](https://github.com/angelleye/offers-for-woocommerce/issues/328))
* Fix - Resolves an issue where a method is unavailable from the admin panel. ([#324](https://github.com/angelleye/offers-for-woocommerce/issues/324))
* Fix - Resolves an issue with incorrect email URL for counter offers. ([#327](https://github.com/angelleye/offers-for-woocommerce/issues/327))
* Fix - Resolves an issue with incorrect parameter names being used loading values. ([#331](https://github.com/angelleye/offers-for-woocommerce/issues/331))
* Fix - Resolves an issue with the offer form on some mobile devices. ([#333](https://github.com/angelleye/offers-for-woocommerce/issues/333))
* Fix - Resolves a CSS conflict with The7 theme. ([#334](https://github.com/angelleye/offers-for-woocommerce/issues/334))
* Fix - Resolves a conflict with Virtue Premium theme. ([#347](https://github.com/angelleye/offers-for-woocommerce/issues/347))
* Fix - Resolves a capability conflict with WC Vendors extension. ([#351](https://github.com/angelleye/offers-for-woocommerce/issues/351))

= 1.4.7.2 - 06.22.2017 =
* Fix - Resolves a conflict with some theme's when using WC Vendors extension. ([#319](https://github.com/angelleye/offers-for-woocommerce/issues/319))
* Fix - Resolves PHP notices happening when searching for products. ([#320](https://github.com/angelleye/offers-for-woocommerce/issues/320))

= 1.4.7.1 - 06.19.2017 =
* Fix - Resolves an issue with our WC Vendors extension dashboard. ([#316](https://github.com/angelleye/offers-for-woocommerce/issues/316))

= 1.4.7 - 06.18.2017 =
* Feature - Adds drag-and-drop functionality to re-order the display of offer form fields. ([#279](https://github.com/angelleye/offers-for-woocommerce/issues/279))
* Feature - Adds shortcode to display recent offers list. ([#304](https://github.com/angelleye/offers-for-woocommerce/issues/304))
* Feature - Adds filter hook to search button for white labeling.  ([#305](https://github.com/angelleye/offers-for-woocommerce/issues/305))
* Tweak - Improvements to design of email notifications. ([#276](https://github.com/angelleye/offers-for-woocommerce/issues/276))
* Tweak - Adjustment for better compatibility with WC Vendors Pro. ([#309](https://github.com/angelleye/offers-for-woocommerce/issues/309))
* Tweak - Adds Offers column in product list so you can quickly see if there are existing offers while browsing products. ([#266](https://github.com/angelleye/offers-for-woocommerce/issues/266))
* Fix - Resolves a compatibility issue with Shop Manager role. ([#298](https://github.com/angelleye/offers-for-woocommerce/issues/298))
* Fix - Resolves a compatibility issue with Woo Variations Table plugin. [(#302](https://github.com/angelleye/offers-for-woocommerce/issues/302))

= 1.4.6 - 04.25.2017 =
* Tweak - Further adjustments for WooCommerce 3.0 compatibility. ([#301](https://github.com/angelleye/offers-for-woocommerce/issues/301))

= 1.4.5 - 04.20.2017 =
* Fix - Resolves a JavaScript bug. ([#300](https://github.com/angelleye/offers-for-woocommerce/issues/300))

= 1.4.4 - 04.19.2017 =
* Feature - WooCommerce 3.0 compatibility. ([#296](https://github.com/angelleye/offers-for-woocommerce/issues/296))
* Feature - Woo Product Add-Ons compatibility. ([#215](https://github.com/angelleye/offers-for-woocommerce/issues/215))
* Feature - Adds the option to require fields on the Make Offer form. ([#248](https://github.com/angelleye/offers-for-woocommerce/issues/248))
* Feature - Adds the "regular price" to the offer email notifications so you can easily compare to the offer price. ([#289](https://github.com/angelleye/offers-for-woocommerce/issues/289))
* Feature - Woo Product Variations Compatibility. ([#252](https://github.com/angelleye/offers-for-woocommerce/issues/252))
* Tweak - Adds Make Offer button text to localization for easy translation. ([#291](https://github.com/angelleye/offers-for-woocommerce/issues/291))
* Tweak - Adjustments for better WC Vendor Pro compatibility via our premium extension. ([#297](https://github.com/angelleye/offers-for-woocommerce/issues/297))
* Fix - Resolves issues with the CSS class name option for offer buttons. ([#295](https://github.com/angelleye/offers-for-woocommerce/issues/295))

= 1.4.3 - 03.26.2017 =
* Feature - Adds option to set a custom class name for the offer button so that you can adjust CSS more easily. ([#286](https://github.com/angelleye/offers-for-woocommerce/issues/286))
* Tweak - Moves register post type function into main plugin file for loading in init. ([#287](https://github.com/angelleye/offers-for-woocommerce/issues/287))
* Tweak - Adjusts code to work with old and new versions of jQuery. ([#288](https://github.com/angelleye/offers-for-woocommerce/issues/288))
* Fix - Resolves a PHP notice about a non-object. ([#292](https://github.com/angelleye/offers-for-woocommerce/issues/292))

= 1.4.2 - 03.02.2017 =
* Tweak - Changes the hook used to update the offer status based on the order status. ([#285](https://github.com/angelleye/offers-for-woocommerce/issues/285))

= 1.4.1 - 02.28.2017 =
* Tweak - Adjustments for better compatibility with WC Vendors extension. ([#284](https://github.com/angelleye/offers-for-woocommerce/issues/284))
* Fix - Resolves a potential PHP error with the Manage Offers admin bar link. ([#283](https://github.com/angelleye/offers-for-woocommerce/issues/283))

= 1.4.0 - 02.28.2017 =
* Feature - Options for requiring user log in to submit offers. ([#263](https://github.com/angelleye/offers-for-woocommerce/issues/263))
* Feature - Compatibility with WPML.  Bulk editor now updates all language versions of products. ([#204](https://github.com/angelleye/offers-for-woocommerce/issues/204))
* Feature - Adds an option to display the Make Offer form only when the user is about to exit the page. ([#192](https://github.com/angelleye/offers-for-woocommerce/issues/192))
* Feature - Adds filter hooks to adjust the recipient(s) on email notifications for offers. ([#259](https://github.com/angelleye/offers-for-woocommerce/issues/259))
* Feature - Allows you to choose whether to include shipping on the accepted/countered offer or not. ([#271](https://github.com/angelleye/offers-for-woocommerce/issues/271))
* Feature - Adds a few hooks. ([#265](https://github.com/angelleye/offers-for-woocommerce/issues/265))
* Feature - Adds Offer History to product editor in WP admin panel.  Also adds "Manage Offers" link to admin toolbar when browsing a product page. ([#280](https://github.com/angelleye/offers-for-woocommerce/issues/280))
* Tweak - Adjusts filter hooks. ([#261](https://github.com/angelleye/offers-for-woocommerce/issues/261))
* Tweak - General code improvements. ([#267](https://github.com/angelleye/offers-for-woocommerce/issues/267), [#273](https://github.com/angelleye/offers-for-woocommerce/issues/273))
* Tweak - Adjustments to offer history settings / display. ([#277](https://github.com/angelleye/offers-for-woocommerce/issues/277))
* Fix - Resolves CSS conflict with some themes. ([#260](https://github.com/angelleye/offers-for-woocommerce/issues/260))
* Fix - Resolves an issue with some themes where the Add to Cart button would display even if you disabled it in your theme. ([#270](https://github.com/angelleye/offers-for-woocommerce/issues/270))
* Fix - Resolves a potential conflict with other plugins working with WooCommerce email templates. ([#278](https://github.com/angelleye/offers-for-woocommerce/issues/278))
* Fix - Resolves an issue where offers would get marked as completed if the user began checkout but then canceled before completing payment. ([#281](https://github.com/angelleye/offers-for-woocommerce/issues/281))

= 1.3.1 - 01.03.2017 =
* Feature - Adds ability to display current high offer on the product page. ([#257](https://github.com/angelleye/offers-for-woocommerce/issues/257))
* Feature - Silent Auction - Adds the ability to leave the price empty for products and still allows buyers to submit offers. ([#156](https://github.com/angelleye/offers-for-woocommerce/issues/156))
* Tweak - Adjusts display location for highest current offer on product pages (when enabled.)  ([#257](https://github.com/angelleye/offers-for-woocommerce/issues/257))
* Tweak - Deletes related offer data when products are deleted. ([#251](https://github.com/angelleye/offers-for-woocommerce/issues/251))
* Tweak - Adds a div wrapper around the offer button for more direct access to the element. ([#250](https://github.com/angelleye/offers-for-woocommerce/issues/250))
* Fix - Resolves an issue with the loader graphic. ([#254](https://github.com/angelleye/offers-for-woocommerce/issues/254))
* Fix - Resolves a conflict with WC PDF Invoices and Packing Slips that caused double headers to be output in offer email notifications. ([#227](https://github.com/angelleye/offers-for-woocommerce/issues/227))

= 1.3.0 - 07.07.2016 =
* Feature - Adds the ability to accept or deny offers directly from the email notification. ([#206](https://github.com/angelleye/offers-for-woocommerce/issues/206))
* Feature - Adds offer history to the user account screen so that buyers can see their offer history with the website. ([#235](https://github.com/angelleye/offers-for-woocommerce/issues/235))
* Feature - Adds an option to disable coupons for orders with an accepted offer so that buyers cannot "double dip". ([#213](https://github.com/angelleye/offers-for-woocommerce/issues/213))
* Feature - Capability Manager compatibility. ([#231](https://github.com/angelleye/offers-for-woocommerce/issues/231))
* Feature - Adds the option to make all communication between buyers and sellers anonymous. ([#239](https://github.com/angelleye/offers-for-woocommerce/issues/239))
* Feature - If an auto-accept rule is triggered, the buyer is now sent directly to checkout for the accepted offer instead of needing to wait for the email notification. ([#240](https://github.com/angelleye/offers-for-woocommerce/issues/240))
* Fix - Resolves a conflict with WPML plugin. ([#233](https://github.com/angelleye/offers-for-woocommerce/issues/233))
* Fix - Resolves errors that occur if you open an offer for an item that has been deleted. ([#236](https://github.com/angelleye/offers-for-woocommerce/issues/236))
* Fix - Localization adjustments. ([#238](https://github.com/angelleye/offers-for-woocommerce/issues/238))
* Fix - WooCommerce Tab Manager compatibility ([#244](https://github.com/angelleye/offers-for-woocommerce/issues/244))
* Fix - WooCommerce 2.6 compatibility. ([#242](https://github.com/angelleye/offers-for-woocommerce/issues/242))

= 1.2.2 - 01.30.2016 =
* Fix - Ensures expiration date option does not show up when it shouldn't. ([#226](https://github.com/angelleye/offers-for-woocommerce/issues/226))
* Fix - Resolves PHP notices. ([#228](https://github.com/angelleye/offers-for-woocommerce/issues/228))
* Fix - Resolves logic problems with auto-accept rules in relation to counter offer amounts. ([#229](https://github.com/angelleye/offers-for-woocommerce/issues/229))
* Fix - Resolves rounding issues. ([#230](https://github.com/angelleye/offers-for-woocommerce/issues/230))

= 1.2.1 - 01.23.2016 =
* Feature - Adds new hooks to offer submit form for custom field / content entry. ([#212](https://github.com/angelleye/offers-for-woocommerce/issues/212))
* Fix - Resolves small bugs and improves general code structure.
* Fix - Resolves an issue keeping email notifications from sending while logged in as an administrator. ([#222](https://github.com/angelleye/offers-for-woocommerce/issues/222))
* Tweak - Hides the expiration date option when declining an offer. ([#208](https://github.com/angelleye/offers-for-woocommerce/issues/208))

= 1.2.0 - 12.16.2015 =
* Feature - Adds ability for users to join your MailChimp / ConstantContact mailing list while submitting an offer. ([#7](https://github.com/angelleye/offers-for-woocommerce/issues/7))
* Feature - Adds ability to include an active coupon in the declined offer email buyers receive. ([#23](https://github.com/angelleye/offers-for-woocommerce/issues/23))
* Feature - Adds ability to set price ranges for auto-accepting and auto-declining offers.  ([#35](https://github.com/angelleye/offers-for-woocommerce/issues/35))
* Feature - Improves offer history details.  ([#67](https://github.com/angelleye/offers-for-woocommerce/issues/67))
* Feature - Adds ability to enable/disable offers from product quick edit. ([#191](https://github.com/angelleye/offers-for-woocommerce/issues/191))
* Feature - Adds ability to add custom fields to offer form. ([#199](https://github.com/angelleye/offers-for-woocommerce/issues/199))
* Fix - Resolves a bug with long URLs. ([#132](https://github.com/angelleye/offers-for-woocommerce/issues/132))
* Fix - Resolves a potential jQuery conflict with some themes. ([#193](https://github.com/angelleye/offers-for-woocommerce/issues/193))
* Fix - Resolves post status values disappearing when plugin is enabled. ([#200](https://github.com/angelleye/offers-for-woocommerce/issues/200))
* Fix - Resolves bugs in bulk update tool. ([#207](https://github.com/angelleye/offers-for-woocommerce/issues/207))
* Tweak - Improves email notification subject line. ([#173](https://github.com/angelleye/offers-for-woocommerce/issues/173))
* Tweak - Improves offers on variable products. ([#188](https://github.com/angelleye/offers-for-woocommerce/issues/188))
* Tweak - General code improvements and bug fixes.
* Tweak - Compatibility with qTranslate-X plugin.  ([#197](https://github.com/angelleye/offers-for-woocommerce/issues/197))
* Tweak - Improves offers on parent / child products. ([#202](https://github.com/angelleye/offers-for-woocommerce/issues/202))

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