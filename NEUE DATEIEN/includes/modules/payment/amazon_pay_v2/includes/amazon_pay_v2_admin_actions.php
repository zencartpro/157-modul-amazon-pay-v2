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
 * @version $Id: amazon_pay_v2_admin_actions.php 2023-04-02 09:50:16Z webchills $
 */
require_once __DIR__ . '/../amazon_pay_v2.php';

if (!empty($_GET['amazon_pay_action'])) {

    $orderId           = (int)$_GET['oID'];
    $amazonPayHelper   = new \ZencartAmazonPayV2\AmazonPayHelper();
    $transactionHelper = new \ZencartAmazonPayV2\Helpers\TransactionHelper();
    $apiClient         = $amazonPayHelper->getClient();
    $orderHelper       = new \ZencartAmazonPayV2\OrderHelper();
    $configHelper      = new \ZencartAmazonPayV2\ConfigHelper();
    try {
        switch ($_GET['amazon_pay_action']) {
            case 'get_admin_html':
                define('AMAZON_PAY_IS_AJAX', true);
                include __DIR__.'/admin_order.inc.php';
                die;
            
            case 'refund':
            global $messageStack, $db;
                $originalCharge = $apiClient->getCharge($_GET['charge_id']);
                if ($originalCharge->getStatusDetails()->getState() !== \AmazonPayApiSdkExtension\Struct\StatusDetails::CAPTURED) {
                    $transactionHelper->updateCharge($originalCharge);
                } else {
                    $chargeTransaction = $transactionHelper->getTransaction($originalCharge->getChargeId());
                    $refund            = new \AmazonPayApiSdkExtension\Struct\Refund();
                    $refund->setChargeId($originalCharge->getChargeId());
                    $amount = new \AmazonPayApiSdkExtension\Struct\RefundAmount($originalCharge->getCaptureAmount()->toArray());
                    $amount->setAmount((float)$_POST['amount']);
                    $refund->setRefundAmount($amount);
                    $refund                     = $apiClient->createRefund($refund);
                    $transaction                = new \ZencartAmazonPayV2\Models\Transaction();
                    $transaction->type          = 'Refund';
                    $transaction->reference     = $refund->getRefundId();
                    $transaction->time          = date('Y-m-d H:i:s', strtotime($refund->getCreationTimestamp()));
                    $transaction->charge_amount = $refund->getRefundAmount()->getAmount();
                    $transaction->currency      = $refund->getRefundAmount()->getCurrencyCode();
                    $transaction->mode          = strtolower($refund->getReleaseEnvironment());
                    $transaction->merchant_id   = $configHelper->getMerchantId();
                    $transaction->status        = $refund->getStatusDetails()->getState();                    
                    $transaction->order_id      = $orderId ;
                    zen_db_perform(TABLE_AMAZON_PAY_V2_TRANSACTIONS, $transaction->toArray());
                    $new_order_status = (int)MODULE_PAYMENT_AMAZON_PAY_V2_REFUNDED_STATUS_ID;
                    $infoText = AMAZON_ADMIN_REFUND_ORDER_COMMENT;
                    $comments =  $infoText .  $transaction->reference;
                    zen_update_orders_history($orderId, $comments, null, $new_order_status, 0);
                    $messageStack->add_session(AMAZON_ADMIN_REFUND_SUCCESS, 'success');    
                }
                break;
           
            case 'refresh':
                $transactionHelper->refreshOrder($_GET['oID']);
                break;
        }
    }catch(Exception $e){
        $_SESSION['amazon_pay_admin_error'] = $e->getMessage();
        $messageStack->add_session(AMAZON_ADMIN_REFUND_ERROR, 'error');
        zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['amazon_pay_action']), 'SSL'));
    }
   
    
    zen_redirect(zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(['amazon_pay_action']), 'SSL'));
}