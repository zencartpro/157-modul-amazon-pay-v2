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
 * @version $Id: TransactionHelper.php 2023-04-01 07:32:16Z webchills $
 */
namespace ZencartAmazonPayV2\Helpers;

use ZencartAmazonPayV2\AmazonPayHelper;
use ZencartAmazonPayV2\ConfigHelper;
use ZencartAmazonPayV2\GeneralHelper;
use ZencartAmazonPayV2\Models\Transaction;
use ZencartAmazonPayV2\OrderHelper;
use AmazonPayApiSdkExtension\Struct\CaptureAmount;
use AmazonPayApiSdkExtension\Struct\Charge;
use AmazonPayApiSdkExtension\Struct\ChargePermission;
use AmazonPayApiSdkExtension\Struct\CheckoutSession;
use AmazonPayApiSdkExtension\Struct\Refund;
use AmazonPayApiSdkExtension\Struct\StatusDetails;
use Exception;

class TransactionHelper
{

    public function updateRefund(Refund $refund, $updateCharge = true)
    {
    	global $db;
        try {
            $transaction         = new Transaction();
            $transaction->status = $refund->getStatusDetails()->getState();
            if ($updateCharge) {
                $amazonPayHelper = new AmazonPayHelper();
                $charge          = $amazonPayHelper->getClient()->getCharge($refund->getChargeId());
                $this->updateCharge($charge);
            }
            zen_db_perform(TABLE_AMAZON_PAY_V2_TRANSACTIONS, $transaction->toArray(), 'update', ' reference = \'' . zen_db_input($refund->getRefundId()) . '\'');
        } catch (\Exception $e) {
            GeneralHelper::log('error', 'updateRefund failed', [$e->getMessage(), $refund]);
        }

        return null;
    }

    public function updateCharge(Charge $charge)
    {
    	global $db;
        try {
            $transaction = new Transaction();
            if ($charge->getCaptureAmount()) {
                $transaction->captured_amount = (float)$charge->getCaptureAmount()->getAmount();
            }
            if ($charge->getRefundedAmount()) {
                $transaction->refunded_amount = (float)$charge->getRefundedAmount()->getAmount();
            }
            $transaction->status = $charge->getStatusDetails()->getState();
            zen_db_perform(TABLE_AMAZON_PAY_V2_TRANSACTIONS, $transaction->toArray(), 'update', ' reference = \'' . zen_db_input($charge->getChargeId()) . '\'');

            if($originalChargeTransaction = $this->getTransaction($charge->getChargeId())){
                if ($originalChargeTransaction->order_id) {
                    $orderHelper = new OrderHelper();
                    if ($transaction->status === StatusDetails::AUTHORIZED) {
                        $orderHelper->setOrderStatusAuthorized($originalChargeTransaction->order_id);                        
                        $this->capture($charge->getChargeId());
                        
                    } elseif ($transaction->status === StatusDetails::DECLINED) {
                        $orderHelper->setOrderStatusDeclined($originalChargeTransaction->order_id);
                    } elseif ($transaction->status === StatusDetails::CAPTURED) {
                        $orderHelper->setOrderStatusCaptured($originalChargeTransaction->order_id);
                    }
                }
            }
            
        } catch (\Exception $e) {
            GeneralHelper::log('error', 'updateCharge failed', [$e->getMessage(), $charge]);
        }

        return null;
    }

    public function updateChargePermission(ChargePermission $chargePermission)
    {
    	global $db;
        try {
            $transaction = new Transaction();
            $transaction->status = $chargePermission->getStatusDetails()->getState();
            zen_db_perform(TABLE_AMAZON_PAY_V2_TRANSACTIONS, $transaction->toArray(), 'update', ' reference = \'' . zen_db_input($chargePermission->getChargePermissionId()) . '\'');
        } catch (\Exception $e) {
            GeneralHelper::log('error', 'updateChargePermission failed', [$e->getMessage(), $chargePermission]);
        }
        return null;
    }

    public function getTransaction($reference)
    {
    	  global $db;
    	  $sql  = "SELECT * FROM " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " WHERE reference='" . zen_db_input($reference) . "'";        
        $gettransaction = $db->Execute($sql);
        
         if (!$gettransaction->EOF) {          	
            return new Transaction($gettransaction);
        } else {
            return null;
        }
        
    }

    public function capture($chargeId, $amount = null)
    {
        try {
            $amazonPayHelper = new AmazonPayHelper();
            $apiClient       = $amazonPayHelper->getClient();
            $originalCharge  = $apiClient->getCharge($chargeId);
            if ($originalCharge->getStatusDetails()->getState() === StatusDetails::AUTHORIZED) {
                $captureCharge = new Charge();
                $captureAmount = new CaptureAmount($originalCharge->getChargeAmount()->toArray());
                if ($amount !== null) {
                    $captureAmount->setAmount($amount);
                }
                $captureCharge->setCaptureAmount($captureAmount);
                $captureCharge = $apiClient->captureCharge($originalCharge->getChargeId(), $captureCharge);
                $this->updateCharge($captureCharge);
            }
        } catch (\Exception $e) {
            GeneralHelper::log('error', 'capture failed', [$e->getMessage(), $chargeId, $amount]);
        }

        return null;
    }

    public function saveNewCharge(Charge $charge, $orderId = null)
    {
        global $db;
        $transaction                = new Transaction();
        $transaction->type          = 'Charge';
        $transaction->reference     = $charge->getChargeId();
        $transaction->time          = date('Y-m-d H:i:s', strtotime($charge->getCreationTimestamp()));
        $transaction->expiration    = date('Y-m-d H:i:s', strtotime($charge->getExpirationTimestamp()));
        $transaction->charge_amount = $charge->getChargeAmount()->getAmount();
        $transaction->currency      = $charge->getChargeAmount()->getCurrencyCode();
        $transaction->mode          = strtolower($charge->getReleaseEnvironment());
        $transaction->merchant_id   = (new ConfigHelper())->getMerchantId();
        $transaction->status        = $charge->getStatusDetails()->getState();
        if ($orderId !== null) {
            $transaction->order_id = $orderId;
        }
        zen_db_perform(TABLE_AMAZON_PAY_V2_TRANSACTIONS, $transaction->toArray());
        return $transaction;
    }

    public function saveNewChargePermission(ChargePermission $chargePermission, $orderId = null)
    {
    	  global $db;
        $transaction                = new Transaction();
        $transaction->type          = 'ChargePermission';
        $transaction->reference     = $chargePermission->getChargePermissionId();
        $transaction->time          = date('Y-m-d H:i:s', strtotime($chargePermission->getCreationTimestamp()));
        $transaction->expiration    = date('Y-m-d H:i:s', strtotime($chargePermission->getExpirationTimestamp()));
        $transaction->charge_amount = $chargePermission->getLimits()->getAmountLimit()->getAmount();
        $transaction->currency      = $chargePermission->getLimits()->getAmountLimit()->getCurrencyCode();
        $transaction->mode          = strtolower($chargePermission->getReleaseEnvironment());
        $transaction->merchant_id   = (new ConfigHelper())->getMerchantId();
        $transaction->status        = $chargePermission->getStatusDetails()->getState();
        if ($orderId !== null) {
            $transaction->order_id = $orderId;
        }
        zen_db_perform(TABLE_AMAZON_PAY_V2_TRANSACTIONS, $transaction->toArray());
        return $transaction;
    }

    public function saveNewCheckoutSession(CheckoutSession $checkoutSession, $total, $currency, $orderId = null)
    {
    	  global $db;
        $configHelper               = new ConfigHelper();
        $transaction                = new Transaction();
        $transaction->type          = 'CheckoutSession';
        $transaction->reference     = $checkoutSession->getCheckoutSessionId();
        $transaction->charge_amount = $total;
        $transaction->currency      = $currency;
        $transaction->mode          = $configHelper->isSandbox() ? 'sandbox' : 'live';
        $transaction->merchant_id   = $configHelper->getMerchantId();
        $transaction->status        = $checkoutSession->getStatusDetails()->getState();
        if ($orderId !== null) {
            $transaction->order_id = $orderId;
        }

        zen_db_perform(TABLE_AMAZON_PAY_V2_TRANSACTIONS, $transaction->toArray());
        return $transaction;
    }

    /**
     * @return Transaction[]
     */
    public function getOpenTransactions($orderId = null)
    {
    	 global $db;
        $sql      = "SELECT * FROM " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " WHERE type != 'CheckoutSession' AND status IN ('" . implode("', '", [
                StatusDetails::REFUND_INITIATED,
                StatusDetails::OPEN,
                StatusDetails::AUTHORIZATION_INITIATED,
                StatusDetails::AUTHORIZED,
                StatusDetails::NON_CHARGEABLE,
                StatusDetails::CHARGEABLE,
            ]) . "')".
            ($orderId !== null?' AND order_id = '.(int)$orderId:'');
            
         $transresult = $db->Execute($sql);
        
        $return = [];
        while (!$transresult->EOF) {
            $return[] = new Transaction($transresult);
            $transresult->MoveNext();
        }
        return $return;
    }    

    public function refreshOrder($orderId)
    {
        foreach ($this->getOpenTransactions($orderId) as $transaction) {
            try {
                $this->refreshTransaction($transaction);
            } catch (Exception $e) {
                GeneralHelper::log('error', 'Unable to update transaction in cron',  ['msg'=>$e->getMessage(), 'trace' => $e->getTrace(), 'transaction' => $transaction->toArray()]);
            }
        }
    }

    public function refreshTransaction(Transaction $transaction)
    {
        $apiClient       = (new AmazonPayHelper())->getClient();
        if ($transaction->type === Transaction::TRANSACTION_TYPE_REFUND) {
            $refund = $apiClient->getRefund($transaction->reference);
            $this->updateRefund($refund);
        } elseif ($transaction->type === Transaction::TRANSACTION_TYPE_CHARGE) {
            $charge = $apiClient->getCharge($transaction->reference);
            $this->updateCharge($charge);
        } elseif ($transaction->type === Transaction::TRANSACTION_TYPE_CHARGE_PERMISSION) {
            $chargePermission = $apiClient->getChargePermission($transaction->reference);
            $this->updateChargePermission($chargePermission);
        }
    }

}
