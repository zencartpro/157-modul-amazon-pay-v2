<?php
/**
 * Amazon Pay V2 for Zen Cart German 1.5.7
 * Copyright 2023 webchills (www.webchills.at)
 * based on Amazon Pay for Modified by AlkimMedia (www.alkim.de)
 * (https://github.com/AlkimMedia/AmazonPay_Modified_2060)
 * Portions Copyright 2003-2023 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * Dieses Modul ist DONATIONWARE
 * Wenn Sie es in Ihrem Zen Cart Shop einsetzen, spenden Sie für die Weiterentwicklung der deutschen Zen Cart Version auf
 * https://spenden.zen-cart-pro.at
 * @version $Id: checkout_confirmation_amazon.php 2023-03-31 15:36:16Z webchills $
 */

define('NAVBAR_TITLE_1', 'Checkout');
define('NAVBAR_TITLE_2', 'Confirmation');

define('HEADING_TITLE', 'Step 3 of 3 - Confirm Purchase');
define('TEXT_ZUSATZ_SCHRITT3','Please verify your order and confirm by clicking the "Confirm Purchase" button at the bottom of this page.<br>You will then be redirected to the Amazon Pay page again where you will authorize and confirm your payment.');

define('HEADING_BILLING_ADDRESS', 'Billing/Payment Information');
define('HEADING_DELIVERY_ADDRESS', 'Delivery/Shipping Information');
define('HEADING_SHIPPING_METHOD', 'Shipping Method:');
define('HEADING_PAYMENT_METHOD', 'Payment Method:');
define('HEADING_PRODUCTS', 'Shopping Cart Contents');
define('HEADING_TAX', 'Tax');
define('HEADING_ORDER_COMMENTS', 'Special Instructions or Order Comments');
// no comments entered
define('NO_COMMENTS_TEXT', 'None');

// buttonloesung
define('TABLE_HEADING_SINGLEPRICE','Price');
define('TABLE_HEADING_PRODUCTIMAGE','Image');
define('TEXT_CONDITIONS_ACCEPTED_IN_LAST_STEP','I have read and agreed to the <a href="' . zen_href_link(FILENAME_CONDITIONS, '', 'SSL') . '" target="_blank"><u>terms and conditions</u></a> bound to this order.');
define('TEXT_NON_EU_COUNTRIES','Note:<br>Your order will be shipped to a country outside the European Union. Your packages may be subject to the customs fees and import duties of the country to which you have your order shipped. These charges are always the recipient\'s responsibility.');
