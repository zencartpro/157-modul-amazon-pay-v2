<?php
/**
 * Amazon Pay V2 for Zen Cart German 1.5.7
 * Copyright 2023 webchills (www.webchills.at)
 * based on Amazon Pay for Modified by AlkimMedia (www.alkim.de)
 * (https://github.com/AlkimMedia/AmazonPay_Modified_2060)
 * Portions Copyright 2003-2022 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * Dieses Modul ist DONATIONWARE
 * Wenn Sie es in Ihrem Zen Cart Shop einsetzen, spenden Sie für die Weiterentwicklung der deutschen Zen Cart Version auf
 * https://spenden.zen-cart-pro.at
 * @version $Id: amazon_pay_v2_messages.php 2023-03-29 18:58:51Z webchills $
 */
define('TEXT_AMAZON_PAY_V2_ERROR', 'Your payment was not successful. Please use another payment method.');
define('TEXT_AMAZON_PAY_V2_ACCOUNT_EDIT_INFORMATION', 'To start the checkout, we still need the following information from you:');
define('TEXT_AMAZON_PAY_V2_ADDRESS_INFORMATION', 'Please enter your delivery address.');
define('TEXT_AMAZON_PAY_V2_ORDER_REFERENCE', 'Amazon Pay Reference Number');
define('TEXT_AMAZON_PAY_V2_USE_CREDIT', 'I would like to redeem my credit.');
// this text is used to announce the username/password when the module creates the customer account and emails data to them:
define('EMAIL_EC_AMAZON_ACCOUNT_INFORMATION', 'When you checked out with Amazon Pay, a customer account was automatically created in our shop so that you can log back in and check the status of your order. With the following access data you can log into your customer account:');
// this text is used when payment is declined and the customer gets redirected to the shoppping cart
define('ERROR_AMAZON_PAY_V2_PAYMENT_DECLINED', 'Your payment was rejected by Amazon Pay. Please click the Amazon Pay button again and then select another payment method stored in your Amazon account.');
// this text is used when payment is canceled and the customer gets redirected to the shoppping cart
define('ERROR_AMAZON_PAY_V2_PAYMENT_CANCELED', 'You have cancelled or not authorized your Amazon Pay payment. Please click the Amazon Pay button again and then complete the payment again.');
// these text are used for the message stacks if account data are incomplete
define('ERROR_NO_FIRSTNAME_DEFINED', 'The first name is missing in your customer data. Please enter your first name. Then go back to the shopping cart and click on the Amazon Pay button to complete the order.');
define('ERROR_NO_LASTNAME_DEFINED', 'The last name is missing in your customer data. Please enter your last name. Then go back to the shopping cart and click on the Amazon Pay button to complete the order.');
define('ERROR_NO_STREET_DEFINED', 'Street and house number are missing in your customer data. Please enter street and house number. Then go back to the shopping cart and click on the Amazon Pay button to complete the order.');