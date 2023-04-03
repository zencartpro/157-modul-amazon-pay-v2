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
 * @version $Id: Transaction.php 2023-01-04 16:31:16Z webchills $
 */
namespace ZencartAmazonPayV2\Models;

class Transaction
{
    const TRANSACTION_TYPE_CHARGE = 'Charge';
    const TRANSACTION_TYPE_CHARGE_PERMISSION = 'ChargePermission';
    const TRANSACTION_TYPE_REFUND = 'Refund';

    public $id;
    public $reference;
    public $merchant_id;
    public $mode;
    public $type;
    public $time;
    public $expiration;
    public $charge_amount;
    public $captured_amount;
    public $refunded_amount;
    public $currency;
    public $status;
    public $last_change;
    public $last_update;
    public $order_id;
    public $customer_informed;
    public $admin_informed;


    public function __construct($dataArray = null)
    {
        if(is_array($dataArray)){
            $this->setFromArray($dataArray);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $return = [];
        foreach (array_keys(get_object_vars($this)) as $property) {
            if (isset($this->{$property})) {
                $return[$property] = $this->{$property};
            }
        }
        return $return;
    }



    public function setFromArray($dataArray){
        foreach($dataArray as $fieldName=>$fieldValue){
            if(property_exists($this, $fieldName)){
                $this->{$fieldName} = $fieldValue;
            }
        }
    }
}