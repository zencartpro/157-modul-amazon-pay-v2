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
define('MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_ADMIN_DESCRIPTION', 'Amazon Pay V2<br><br><img src="images/amazon-pay-logo.png" alt="Amazon Pay"/><br><br>Dieses Modul verwendet Amazon Pay Checkout V2 und unterstützt die SCA (Strong Customer Authentication)<br><br><b>Bevor Sie dieses Zahlungsmodul aktivieren:</b><ol><li>Nehmen Sie zunächst die erforderlichen Grundeinstellungen vor unter <b>Konfiguration > Amazon Pay V2 Grundeinstellungen</b></li><li>Stellen Sie sicher, dass Sie die erforderlichen <b>Private Keys erstellt und per FTP ins Verzeichnis includes/modules/payment/amazon_pay_v2/keys hochgeladen</b> haben. Informationen dazu finden Sie in der <a href="https://amazonpayv2.zen-cart-pro.at" target="_blank"><u><b>Installationsanleitung</b></u></a></li><li>Dieses Modul ist <b>Donationware</b>. Wenn Sie es in Ihrem Zen Cart Shop nutzen, spenden Sie bitte <a href="https://spenden.zen-cart-pro.at" target="_blank"><b><u>hier</u></b></a>.<br><a href="https://spenden.zen-cart-pro.at" target="_blank"><img src="images/zencartpro-donation-white.png" alt="Jede Spende hilft!" title="Jede Spende hilft!"></a></li></ol><br><a href="https://pay.amazon.de/checkoutv2" target="_blank">Infos zu Amazon Pay V2</a><br><br><a href="https://sellercentral-europe.amazon.com/home" target="_blank">Amazon Pay Händlerkonto Login</a><br><br>');
}
define('MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_TITLE', 'Amazon Pay');
define('MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_DESCRIPTION', 'Amazon Pay');
define('AMAZON_PAY_V2_MESSAGE_SANDBOX_ACTIVE', ' (Sandbox Modus)');
define('AMAZON_PAY_V2_MESSAGE_ALREADY_INSTALLED', 'Amazon Pay V2 Modul ist bereits installiert.');
define('TEXT_PAYMENT_MESSAGE_AMAZON_PAY_V2', 'Zahlungsart: Amazon Pay<br>Wir haben Ihre Zahlung dankend erhalten und bearbeiten Ihre Bestellung umgehend.');