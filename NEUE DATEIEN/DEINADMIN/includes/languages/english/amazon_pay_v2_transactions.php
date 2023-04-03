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
define('HEADING_ADMIN_TITLE', 'Amazon Pay V2 Transactions');
define('TABLE_HEADING_ORDER_NUMBER', 'Shop Order Number');
define('TABLE_HEADING_ENVIRONMENT', 'Environment');
define('TABLE_HEADING_CHARGE_AMOUNT', 'Amount');
define('TABLE_HEADING_TYPE', 'Type');
define('TABLE_HEADING_CAPTURED_AMOUNT', 'Amount (captured)');
define('TABLE_HEADING_REFUNDED_AMOUNT', 'Refund');
define('TABLE_HEADING_TRANSACTION_ID', 'ID');
define('TABLE_HEADING_AMAZON_TRANSACTION', 'Transaction ID');
define('TABLE_HEADING_AMAZON_DATE','Date');
define('TABLE_HEADING_PAYMENT_STATUS', 'Status');
define('TABLE_HEADING_PAYMENT_MESSAGE', 'Response');
define('TABLE_HEADING_PAYMENT_REFNUM', 'Reference Number');
define('TABLE_HEADING_ACTION', 'Action');
define('MAX_DISPLAY_SEARCH_RESULTS_AMAZON_IPN', 50);
define('TEXT_INFO_AMAZON_RESPONSE_BEGIN', 'Amazon Pay V2 Transaction ');
define('TEXT_INFO_AMAZON_RESPONSE_END', ' for order ');
define('HEADING_AMAZON_STATUS', 'Status');
define('TEXT_AMAZON_SORT_ORDER_INFO', 'Sort Order');
define('TEXT_SORT_AMAZON_ID_DESC', 'Amazon Sort Order (new-old)');
define('TEXT_SORT_AMAZON_ID', 'Amazon Sort Order (old-new)');
define('TEXT_SORT_ZEN_ORDER_ID_DESC', 'Shop Order Number (new-old)');
define('TEXT_SORT_ZEN_ORDER_ID', 'Shop Order Number (old-new)');
define('TEXT_SORT_AMAZON_ENVIRONMENT_DESC', 'Environment desc');
define('TEXT_SORT_AMAZON_ENVIRONMENT', 'Environment asc');
define('TEXT_SORT_AMAZON_STATUS_DESC', 'Status desc');
define('TEXT_SORT_AMAZON_STATUS', 'Status asc');
define('TEXT_SORT_AMAZON_STATE', 'Status');
define('TEXT_ALL_IPNS', 'All');
define('AMAZON_REFERENCE_ID', 'Amazon Reference');
define('AMAZON_DATE', 'Date');
define('AMAZON_VIEW_ORDER', 'View order');