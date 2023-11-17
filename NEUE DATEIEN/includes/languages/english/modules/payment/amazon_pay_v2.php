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
 * @version $Id: amazon_pay_v2.php 2023-11-15 19:52:51Z webchills $
 */
define('MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_ADMIN_TITLE', 'Amazon Pay');
if (IS_ADMIN_FLAG === true) {
define('MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_ADMIN_DESCRIPTION', 'Amazon Pay V2<br><br><img src="images/amazon-pay-logo.png" alt="Amazon Pay"/><br><br>This module uses Amazon Pay Checkout V2 and supports SCA (Strong Customer Authentication)<br><br><b>Before you activate this payment module:</b><ol><li>First make the required basic settings in <b>Configuration > Amazon Pay V2 Basic Settings</b></li><li>Make sure you have <b>uploaded the required Private keys via FTP to includes/modules/payment/amazon_pay_v2/keys</b>. For more information about this, see the <a href="https://amazonpayv2.zen-cart-pro.at" target="_blank"><u><b>Installation Guide</b></u></a>.</li><li>This module is <b>donationware</b>. If you use it in your Zen Cart store, please donate <a href="https://spenden.zen-cart-pro.at" target="_blank"><b><u>here</u></b></a>.<br><a href="https://spenden.zen-cart-pro.at" target="_blank"><img src="images/zencartpro-donation-white.png" alt="Every donation helps! " title="Every Donation Helps!"></a></li></ol><br><a href="https://pay.amazon.de/checkoutv2" target="_blank">Info on Amazon Pay V2</a><br><br><a href="https://sellercentral-europe.amazon.com/home" target="_blank">Amazon Pay merchant account login</a><br><br>');
}
define('MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_TITLE', 'Amazon Pay');
define('MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_DESCRIPTION', 'Amazon Pay');
define('AMAZON_PAY_V2_MESSAGE_SANDBOX_ACTIVE', ' (Sandbox Mode)');
define('AMAZON_PAY_V2_MESSAGE_ALREADY_INSTALLED', 'Amazon Pay V2 Plugin is already installed.');
define('TEXT_PAYMENT_MESSAGE_AMAZON_PAY_V2', 'Payment method: Amazon Pay<br>We have received your payment and will process your order immediately.');