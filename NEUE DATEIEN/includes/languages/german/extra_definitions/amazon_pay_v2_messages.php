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
 * Wenn Sie es in Ihrem Zen Cart Shop einsetzen, spenden Sie f?r die Weiterentwicklung der deutschen Zen Cart Version auf
 * https://spenden.zen-cart-pro.at
 * @version $Id: amazon_pay_v2_messages.php 2023-03-29 18:03:51Z webchills $
 */
define('TEXT_AMAZON_PAY_V2_ERROR', 'Ihre Zahlung war nicht erfolgreich. Bitte verwenden Sie eine andere Zahlungsart.');
define('TEXT_AMAZON_PAY_V2_ACCOUNT_EDIT_INFORMATION', 'Um den Checkout zu starten, benötigen wir noch folgende Informationen von Ihnen');
define('TEXT_AMAZON_PAY_V2_ADDRESS_INFORMATION', 'Bitte geben Sie Ihre Lieferadresse ein.');
define('TEXT_AMAZON_PAY_V2_ORDER_REFERENCE', 'Amazon Pay Referenznummer');
define('TEXT_AMAZON_PAY_V2_USE_CREDIT', 'Ich möchte mein Guthaben einlösen.');
// this text is used to announce the username/password when the module creates the customer account and emails data to them:
define('EMAIL_EC_AMAZON_ACCOUNT_INFORMATION', 'Bei Ihrem Login mit Amazon Pay wurde automatisch ein Kundenkonto in unserem Shop angelegt, damit Sie wieder einloggen und den Status Ihrer Bestellung prüfen können. Mit folgenden Zugangsdaten können Sie in Ihr Kundenkonto einloggen:');
// this text is used when payment is declined and the customer gets redirected to the shoppping cart
define('ERROR_AMAZON_PAY_V2_PAYMENT_DECLINED', 'Ihre Zahlung wurde von Amazon Pay abgelehnt. Bitte clicken Sie erneut auf den Amazon Pay Button und wählen Sie dann eine andere in Ihrem Amazon Account hinterlegte Zahlungsart.');
// this text is used when payment is abandoned and the customer gets redirected to the shoppping cart
define('ERROR_AMAZON_PAY_V2_PAYMENT_CANCELED', 'Sie haben Ihre Amazon Pay Zahlung abgebrochen oder nicht autorisiert. Bitte clicken Sie erneut auf den Amazon Pay Button und führen Sie dann die Zahlung erneut komplett aus.');
// these text are used for the message stacks if account data are incomplete
define('ERROR_NO_FIRSTNAME_DEFINED', 'In Ihren Kundendaten fehlt der Vorname. Bitte tragen Sie Ihren Vornamen ein. Danach gehen Sie wieder zum Warenkorb und clicken auf den Amazon Pay Button, um die Bestellung durchzuführen.');
define('ERROR_NO_LASTNAME_DEFINED', 'In Ihren Kundendaten fehlt der Nachname. Bitte tragen Sie Ihren Nachnamen ein. Danach gehen Sie wieder zum Warenkorb und clicken auf den Amazon Pay Button, um die Bestellung durchzuführen.');
define('ERROR_NO_STREET_DEFINED', 'In Ihren Kundendaten fehlen Strasse und Hausnummer. Bitte tragen Sie Strasse und Hausnummer ein. Danach gehen Sie wieder zum Warenkorb und clicken auf den Amazon Pay Button, um die Bestellung durchzuführen.');