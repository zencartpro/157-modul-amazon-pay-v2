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
 * @version $Id: 1_0_0.php 2023-11-19 12:48:51Z webchills $
 */


$db->Execute(" SELECT @gid:=configuration_group_id
FROM ".TABLE_CONFIGURATION_GROUP."
WHERE configuration_group_title= 'Amazon Pay V2'
LIMIT 1;");

$db->Execute("INSERT IGNORE INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function) VALUES
('Amazon Pay - Layout Checkout Button', 'AMAZON_PAY_V2_LAYOUT_CHECKOUT_BUTTON', 'Gold', 'Choose your Amazon Pay checkout button layout.<br>', @gid, 1, NOW(), NULL, 'zen_cfg_select_option(array(\'Gold\', \'LightGray\', \'DarkGray\'),'),
('Amazon Pay - Layout Login Button', 'AMAZON_PAY_V2_LAYOUT_LOGIN_BUTTON', 'Gold', 'Choose your Amazon Pay login button layout.<br>', @gid, 2, NOW(), NULL, 'zen_cfg_select_option(array(\'Gold\', \'LightGray\', \'DarkGray\'),'),
('Amazon Pay - Region', 'AMAZON_PAY_V2_REGION', 'EU', 'Choose the region where your store is located between EU (default) and UK.<br>', @gid, 3, NOW(), NULL, 'zen_cfg_select_option(array(\'EU\', \'UK\'),'),
('Amazon Pay - IPN URL', 'AMAZON_PAY_V2_IPN_URL', 'https://www.meinshop.de/ext/modules/payment/amazon_pay_v2/ipn.php', 'Enter the URL of your store for Amazon IPN notifications.<br>', @gid, 6, NOW(), NULL, NULL),
('Amazon Pay - Reference Number', 'AMAZON_PAY_V2_ORDER_COMMENT', 'true', 'Set to true to enable the display of the Amazon Pay reference number in the order status comment history<br>', @gid, 7, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
('Amazon Pay - Send Customer Account Welcome Email', 'AMAZON_PAY_V2_SEND_WELCOME_EMAIL', 'false', 'If a visitor who is logging in with Amazon Pay is not an existing customer, a customer account is automatically created for him/her in the store so that the order can be processed. Would you like to inform the customer about this and send them a welcome email with their login details? The customer will then automatically receive a welcome email with a randomly generated password.<br>', @gid, 8, NOW(), NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),'),
('Amazon Pay - Merchant ID', 'AMAZON_PAY_V2_MERCHANT_ID', '1234567', 'Enter your Amazon Pay Merchant ID.<br>', @gid, 9, NOW(), NULL, NULL),
('Amazon Pay - Store ID', 'AMAZON_PAY_V2_STORE_ID', 'amzn1.application-oa2-client.1234567', 'Enter your Amazon Pay Store ID<br>', @gid, 10, NOW(), NULL, NULL),
('Amazon Pay - SANDBOX Public Key ID', 'AMAZON_PAY_V2_PUBLIC_KEY_ID_SANDBOX', 'SANDBOX-1234567', 'Enter your Amazon Pay Public Key ID for Live for Sandbox Use<br>', @gid, 11, NOW(), NULL, NULL),
('Amazon Pay - LIVE Public Key ID', 'AMAZON_PAY_V2_PUBLIC_KEY_ID', 'LIVE-1234567', 'Enter your Amazon Pay Public Key ID for Live Use<br>', @gid, 12, NOW(), NULL, NULL),
('Amazon Pay - Token for Cronjob', 'AMAZON_PAY_V2_CRON_TOKEN', '".md5(time() . rand(0,99999))."', 'Enter a random sequence of numbers and letters to ensure that only this token can be used to run the cronjob.', @gid, 12, NOW(), NULL, NULL)");

$db->Execute("REPLACE INTO ".TABLE_CONFIGURATION_LANGUAGE." (configuration_title, configuration_key, configuration_description, configuration_language_id) VALUES
('Amazon Pay - Layout Checkout Button', 'AMAZON_PAY_V2_LAYOUT_CHECKOUT_BUTTON', 'Wählen Sie ein Layout für den Amazon Pay Checkout Button.<br>', 43),
('Amazon Pay - Layout Login Button', 'AMAZON_PAY_V2_LAYOUT_LOGIN_BUTTON', 'Wählen Sie ein Layout für den Amazon Pay Login Button.<br>', 43),
('Amazon Pay - Region', 'AMAZON_PAY_V2_REGION', 'Ist Ihr Shop in der EU (Voreinstellung) oder in GroÃŸbritannien (UK)?<br>', 43),
('Amazon Pay - IPN URL', 'AMAZON_PAY_V2_IPN_URL', 'Geben Sie hier die URL in Ihrem Shop für Amazon Pay IPN Benachrichtigungen ein.<br>', 43),
('Amazon Pay - Referenznummer im Bestellkommentar', 'AMAZON_PAY_V2_ORDER_COMMENT', 'Stellen Sie auf true, um die Amazon Pay Referenznummer im Bestellkommentar anzuzeigen.<br>', 43),
('Amazon Pay - Kundenkonto Willkommensemail senden', 'AMAZON_PAY_V2_SEND_WELCOME_EMAIL', 'Wenn ein Besucher, der mit Amazon Pay einloggt, kein bestehender Kunde ist, wird automatisch im Shop ein Kundenkonto für ihn/sie erstellt, damit die Bestellung abgewickelt werden kann. Möchten Sie die Kunden darüber informieren und ihnen eine Willkommens-E-Mail mit ihren Zugangsdaten schicken? Der Kunde bekommt dann automatisch ein Willkommensmail mit einem zufällig generierten Passwort.<br>', 43),
('Amazon Pay - Händler ID', 'AMAZON_PAY_V2_MERCHANT_ID', 'Geben Sie Ihre Amazon Pay Händler ID ein.<br>', 43),
('Amazon Pay - Store ID', 'AMAZON_PAY_V2_STORE_ID', 'Geben Sie Ihre Amazon Pay Store ID ein.<br>', 43),
('Amazon Pay - SANDBOX Public Key ID', 'AMAZON_PAY_V2_PUBLIC_KEY_ID_SANDBOX', 'Geben Sie Ihre Amazon Pay Public Key ID für den Sandboxbetrieb ein.<br>', 43),
('Amazon Pay - Token für Cronjob', 'AMAZON_PAY_V2_CRON_TOKEN', 'Geben Sie eine zufällige Folge von Ziffern und Buchstaben ein, um sicherzustellen, dass nur mit dieser Token der Cronjob ausgeführt werden kann.<br>', 43),
('Amazon Pay - LIVE Public Key ID', 'AMAZON_PAY_V2_PUBLIC_KEY_ID', 'Geben Sie Ihre Amazon Pay Public Key ID für den Livebetrieb ein.<br>', 43)");

// create new transactions table
$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) NOT NULL,
  `merchant_id` varchar(32) DEFAULT NULL,
  `mode` varchar(16) DEFAULT NULL,
  `type` varchar(16) NOT NULL,
  `time` datetime NOT NULL,
  `expiration` datetime NOT NULL,
  `charge_amount` float NOT NULL,
  `captured_amount` float NOT NULL,
  `refunded_amount` float NOT NULL,
  `currency` varchar(16) DEFAULT NULL,
  `status` varchar(32) NOT NULL,
  `last_change` datetime NOT NULL,
  `last_update` datetime NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_informed` tinyint(1) NOT NULL,
  `admin_informed` tinyint(1) NOT NULL,
   PRIMARY KEY (`id`),
   KEY `reference` (`reference`),
   KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

// create new logs table
$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_AMAZON_PAY_V2_LOG . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `msg` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `data` LONGTEXT DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `ip` (`ip`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");


//check if customers_amazonpay_ec column already exists - if not add it
$sql ="SHOW COLUMNS FROM ".TABLE_CUSTOMERS." LIKE 'customers_amazonpay_ec'";
$result = $db->Execute($sql);
if(!$result->RecordCount()){
$sql = "ALTER TABLE ".TABLE_CUSTOMERS." ADD customers_amazonpay_ec tinyint(1) NOT NULL default 0";
$db->Execute($sql);
}


// delete old configuration/tools menu
$admin_page = 'configAmazonPayV2';
$db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
$admin_page_customers = 'customersAmazonPayV2';
$db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page_customers . "' LIMIT 1;");
// add configuration/customers menu
if (!zen_page_key_exists($admin_page)) {
$db->Execute(" SELECT @gid:=configuration_group_id
FROM ".TABLE_CONFIGURATION_GROUP."
WHERE configuration_group_title= 'Amazon Pay V2'
LIMIT 1;");
$db->Execute("INSERT IGNORE INTO " . TABLE_ADMIN_PAGES . " (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES 
('configAmazonPayV2','BOX_CONFIGURATION_AMAZON_PAY_V2','FILENAME_CONFIGURATION',CONCAT('gID=',@gid),'configuration','Y',@gid)");
$db->Execute(" SELECT @gid:=configuration_group_id
FROM ".TABLE_CONFIGURATION_GROUP."
WHERE configuration_group_title= 'Amazon Pay V2'
LIMIT 1;");
$db->Execute("INSERT IGNORE INTO " . TABLE_ADMIN_PAGES . " (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order) VALUES 
('customersAmazonPayV2','BOX_CUSTOMERS_AMAZON_PAY_V2','FILENAME_AMAZON_PAY_V2_TRANSACTIONS','','customers','Y',201)");
$messageStack->add('Amazon Pay V2 Grundkonfiguration erfolgreich installiert.', 'success'); 
}