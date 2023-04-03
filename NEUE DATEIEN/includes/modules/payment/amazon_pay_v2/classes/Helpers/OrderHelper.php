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
 * @version $Id: OrderHelper.php 2023-04-02 09:35:16Z webchills $
 */
namespace ZencartAmazonPayV2;

use ZencartAmazonPayV2\Helpers\TransactionHelper;
use ZencartAmazonPayV2\Models\Transaction;
use AmazonPayApiSdkExtension\Struct\StatusDetails;

class OrderHelper
{
    /**
     * @var \ZencartAmazonPayV2\AmazonPayHelper
     */
    private $amazonPayHelper;

    /**
     * @var \ZencartAmazonPayV2\ConfigHelper
     */
    private $configHelper;

    public function __construct()
    {
        $this->amazonPayHelper = new AmazonPayHelper();
        $this->configHelper    = new ConfigHelper();
    }    

    public function setOrderStatusAuthorized($orderId)
    {
        $newStatus = '2';
        $comment   = 'Amazon Pay - authorize';
        self::setOrderStatus($orderId, $newStatus, $comment);
    }

    public function setOrderStatus($orderId, $status, $comment = '')
    {
    	  global $db;
        $orderId = (int)$orderId;
        $status  = (int)$status;
        if ($status <= 0) {
            $sql  = "SELECT orders_status FROM " . TABLE_ORDERS . " WHERE orders_id = " . $orderId;
            $check_status = $db->Execute($sql);
            if (!$check_status->EOF) {
                $status = (int)$check_status->fields['orders_status'];
            } else {
                return;
            }
        } else {
            $sql  = "SELECT * FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = " . $orderId . " AND orders_status_id = " . $status;
            $check_status = $db->Execute($sql);
            if (!$check_status->EOF) {
                return;
            }
        }
        $data = [
            'orders_id'         => $orderId,
            'orders_status_id'  => $status,
            'date_added'        => 'now()',
            'customer_notified' => 0,
            'comments'          => $comment
        ];
        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $data);
        $q = "UPDATE " . TABLE_ORDERS . " SET orders_status = " . $status . " WHERE orders_id = " . $orderId;
        zen_db_query($q);
    }

    public function setOrderStatusDeclined($orderId)
    {
        self::setOrderStatus($orderId, APC_ORDER_STATUS_DECLINED, 'Amazon Pay - declined');
    }

    public function setOrderStatusCaptured($orderId)
    {
        self::setOrderStatus($orderId, APC_ORDER_STATUS_CAPTURED, 'Amazon Pay - captured');
    }

    public function connectAmazonPaySessionToOrder($checkoutSessionId, $orderId)
    {
    	global $db;
        zen_db_perform(TABLE_AMAZON_PAY_V2_TRANSACTIONS, [
            'amazon_pay_session_id' => $checkoutSessionId,
            'order_id'              => $orderId
        ]);
    }

    public function getAmazonPaySessionIdFromOrder($orderId)
    {
    	  global $db;
        $sql  = "SELECT amazon_pay_session_id FROM " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " WHERE order_id = " . (int)$orderId;
        $sessionresult = $db->Execute($sql);
        if (!$sessionresult->EOF) {  
        return $sessionresult->fields["amazon_pay_session_id"];
    }
    }
  }