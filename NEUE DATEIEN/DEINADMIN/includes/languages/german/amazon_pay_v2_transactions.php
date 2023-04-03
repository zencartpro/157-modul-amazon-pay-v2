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
 * @version $Id: amazon_pay_v2_transactions.php 2023-03-31 15:36:16Z webchills $
 */
define('HEADING_ADMIN_TITLE', 'Amazon Pay V2 Transaktionen');
define('TABLE_HEADING_ORDER_NUMBER', 'Shop Bestellnummer');
define('TABLE_HEADING_ENVIRONMENT', 'Umgebung');
define('TABLE_HEADING_CHARGE_AMOUNT', 'Betrag');
define('TABLE_HEADING_TYPE', 'Typ');
define('TABLE_HEADING_CAPTURED_AMOUNT', 'Betrag (captured)');
define('TABLE_HEADING_REFUNDED_AMOUNT', 'Rückerstattung');
define('TABLE_HEADING_TRANSACTION_ID', 'ID');
define('TABLE_HEADING_AMAZON_TRANSACTION', 'Transaktions ID');
define('TABLE_HEADING_AMAZON_DATE','Datum');
define('TABLE_HEADING_PAYMENT_TYPE', 'Zahlungsart');
define('TABLE_HEADING_PAYMENT_STATUS', 'Status');
define('TABLE_HEADING_PAYMENT_MESSAGE', 'Rückmeldung');
define('TABLE_HEADING_PAYMENT_REFNUM', 'Referenznummer');
define('TABLE_HEADING_ACTION', 'Aktion');
define('MAX_DISPLAY_SEARCH_RESULTS_AMAZON_IPN', 50);
define('TEXT_INFO_AMAZON_RESPONSE_BEGIN', 'Amazon Pay V2 Transaktion ');
define('TEXT_INFO_AMAZON_RESPONSE_END', ' für Shopbestellung ');
define('HEADING_AMAZON_STATUS', 'Status');
define('TEXT_AMAZON_SORT_ORDER_INFO', 'Anzeigesortierung');
define('TEXT_SORT_AMAZON_ID_DESC', 'Amazon Sortierung (neu-alt)');
define('TEXT_SORT_AMAZON_ID', 'Amazon Sortierung (alt-neu)');
define('TEXT_SORT_ZEN_ORDER_ID_DESC', 'Shop Bestellnummer (neu-alt)');
define('TEXT_SORT_ZEN_ORDER_ID', 'Shop Bestellnummer (alt-neu)');
define('TEXT_SORT_AMAZON_ENVIRONMENT_DESC', 'Umgebung absteigend');
define('TEXT_SORT_AMAZON_ENVIRONMENT', 'Umgebung aufsteigend');
define('TEXT_SORT_AMAZON_STATUS_DESC', 'Status absteigend');
define('TEXT_SORT_AMAZON_STATUS', 'Status aufsteigend');
define('TEXT_SORT_AMAZON_STATE', 'Status');
define('TEXT_ALL_IPNS', 'Alle');
define('AMAZON_REFERENCE_ID', 'Amazon Referenz');
define('AMAZON_DATE', 'Datum');
define('AMAZON_VIEW_ORDER', 'Bestellung ansehen');