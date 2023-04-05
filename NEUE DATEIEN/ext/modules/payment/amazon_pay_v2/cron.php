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
 * @version $Id: cron.php 2023-04-05 08:32:16Z webchills $
 */
 
chdir('../../../../');
require_once 'includes/application_top.php';

$key = $_GET['token'];

if ($key != AMAZON_PAY_V2_CRON_TOKEN) exit('<p>Falscher Sicherheitskey!</p>');

$blockFile = DIR_FS_CATALOG . 'cache/amazon_pay_v2/amazon_pay_cron.block';
if (file_exists($blockFile) && filemtime($blockFile) > time() - 300) {
    http_response_code(429);
    return;
}
file_put_contents($blockFile, date('Y-m-d H:i:s'));

require_once 'includes/modules/payment/amazon_pay_v2/amazon_pay_v2.php';
$transactionHelper = new \ZencartAmazonPayV2\Helpers\TransactionHelper();
$transactionHelper->doCron();
echo '<p>Statusaktualisierung angestossen</p>';