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
 * @version $Id: 1_2_0.php 2023-12-10 08:12:51Z webchills $
 */


// set missing defaults for MySQL 8 strict mode
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `time` `time` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00';");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `expiration` `expiration` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00';");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `captured_amount` `captured_amount` FLOAT NOT NULL DEFAULT '0';");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `refunded_amount` `refunded_amount` FLOAT NOT NULL DEFAULT '0';");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `currency` `currency` VARCHAR(16) NULL DEFAULT 'EUR';");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `last_change` `last_change` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00';");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `last_update` `last_update` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00';");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `customer_informed` `customer_informed` TINYINT(1) NOT NULL DEFAULT '0';");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " CHANGE `admin_informed` `admin_informed` TINYINT(1) NOT NULL DEFAULT '0'; ");
$db->Execute("ALTER TABLE " . TABLE_AMAZON_PAY_V2_LOG . " CHANGE `time` `time` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00'; ");


$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.2.0' WHERE configuration_key = 'AMAZON_PAY_V2_MODUL_VERSION' LIMIT 1;");