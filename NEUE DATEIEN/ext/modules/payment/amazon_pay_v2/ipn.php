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
 * @version $Id: ipn.php 2023-03-22 08:00:16Z webchills $
 */
$body = file_get_contents('php://input');

if ($data = json_decode($body, true)) {
    if ($message = json_decode($data['Message'], true)) {
        chdir('../../../../');
        require_once 'includes/application_top.php';
        require_once 'includes/modules/payment/amazon_pay_v2/amazon_pay_v2.php';
        
        
        $amazonPayHelper   = new \ZencartAmazonPayV2\AmazonPayHelper();
        $transactionHelper = new \ZencartAmazonPayV2\Helpers\TransactionHelper();
        $apiClient         = $amazonPayHelper->getClient();

        switch ($message['ObjectType']) {
            case 'CHARGE':
                $charge = $apiClient->getCharge($message['ObjectId']);
                $transactionHelper->updateCharge($charge);
                break;
            case 'REFUND':
                $refund = $apiClient->getRefund($message['ObjectId']);
                $transactionHelper->updateRefund($refund);
                break;
            default:
                break;

        }

    }
}