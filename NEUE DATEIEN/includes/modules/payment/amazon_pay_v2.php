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
 * @version $Id: amazon_pay_v2.php 2023-04-06 20:52:16Z webchills $
 */

require_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/amazon_pay_v2/amazon_pay_v2.php');
use ZencartAmazonPayV2\AmazonPayHelper;
use ZencartAmazonPayV2\CheckoutHelper;
use ZencartAmazonPayV2\GeneralHelper;
use ZencartAmazonPayV2\Helpers\TransactionHelper;
use ZencartAmazonPayV2\OrderHelper;
use AmazonPayApiSdkExtension\Struct\PaymentDetails;
use AmazonPayApiSdkExtension\Struct\Price;
use AmazonPayApiSdkExtension\Struct\StatusDetails;

class amazon_pay_v2 extends base {

    /**
     * $code determines the internal 'code' name used to designate "this" payment module
     *
     * @var string
     */
    public $code;
    /**
  /**
   * $title is the displayed name for this payment method
   *
   * @var string
   */
  public $title;
  /**
   * $description is used to display instructions in the admin
   *
   * @var string
   */
    public $description;
    /**
     * $enabled determines whether this module shows or not... during checkout.
     * @var boolean
     */
    
    public $enabled;
    /**
       /**
   * $order_status determines the status assigned to orders paid-for using this module
   */
  public $order_status;
  public $zone;

    /**
     * $sort_order is the order priority of this payment module when displayed
     * @var int
     */
    public $sort_order;

    /**
     * @var string
     */
    public $info;

    /**
   * Constructor
     */

    function __construct() {
      global $order;

      $this->code = 'amazon_pay_v2';
      $this->title = MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_TITLE;
      
      $this->enabled = (defined('MODULE_PAYMENT_AMAZON_PAY_V2_STATUS') && MODULE_PAYMENT_AMAZON_PAY_V2_STATUS == 'True');
        // Set the title & description text based on the mode we're in
        if (IS_ADMIN_FLAG === true) {
            
            $this->description = MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_ADMIN_DESCRIPTION;
            $this->title = MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_ADMIN_TITLE;

            if ($this->enabled) {

                if (MODULE_PAYMENT_AMAZON_PAY_V2_SERVER == 'Sandbox')
                    $this->title .= '<strong><span class="alert">'. AMAZON_PAY_V2_MESSAGE_SANDBOX_ACTIVE .'</span></strong>';
               
               
            }
        } else {

            $this->description = MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_DESCRIPTION;
            $this->title = MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_TITLE;
        }
	
        $this->sort_order  = defined('MODULE_PAYMENT_AMAZON_PAY_V2_SORT_ORDER')? MODULE_PAYMENT_AMAZON_PAY_V2_SORT_ORDER:null;
     
      if (null === $this->sort_order) return false;
      $this->order_pending_status = defined('MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_PENDING_STATUS_ID') ? MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_PENDING_STATUS_ID : null;    
      if (defined('MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_STATUS_ID') && (int)MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_STATUS_ID;
      }
        $this->zone = defined('MODULE_PAYMENT_AMAZON_PAY_V2_ZONE') ? MODULE_PAYMENT_AMAZON_PAY_V2_ZONE : null;

      if (is_object($order)) $this->update_status();
    }

  /**
   * Calculate zone matches and flag settings to determine whether this module should display to customers or not
   */
  function update_status() {
    global $order;

      if ($this->enabled) {
        
       if ($order->info['total'] == 0) {
                $this->enabled = false;
                
            }
        }
	   }
      
    function javascript_validation() {
    }

    function selection() {
    	return false;
    }

    function pre_confirmation_check() {
      return false;
    } 


   function confirmation() {
      return array('title' => MODULE_PAYMENT_AMAZON_PAY_V2_TEXT_DESCRIPTION);
    }


    function process_button() {
        return false;
    }

    function before_process() {
      return false;
    }

    function after_process()  {
        global $insert_id, $db;

        //checkout session must be in status 'open'
        //this is taken care of in includes/modules/payment/amazon_pay_v2/includes/actions/checkout_process.php

        //complete checkout session
        $amazonPayHelper = new AmazonPayHelper();
        $checkoutHelper = new CheckoutHelper();
        $transactionHelper = new TransactionHelper();

        $paymentDetails = new PaymentDetails();
        try {
            $orderTotal = $this->getOrderTotal($insert_id);

            if ($orderTotal <= 0) {
                throw new Exception('order value must be greater than 0 (order #' . $insert_id . ')');
            }
            $order = new order($insert_id);

            $paymentDetails->setChargeAmount(new Price(['amount' => round($orderTotal, 2), 'currencyCode' => $order->info['currency']]));


            $checkoutSession = $amazonPayHelper->getClient()->completeCheckoutSession($_SESSION['amazon_checkout_session'], $paymentDetails);
            $transactionHelper->saveNewCheckoutSession($checkoutSession, $orderTotal, $order->info['currency'], $insert_id);

            if ($checkoutSession->getChargePermissionId()) {
                $chargePermission = $amazonPayHelper->getClient()->getChargePermission($checkoutSession->getChargePermissionId());
                $transactionHelper->saveNewChargePermission($chargePermission, $insert_id);
                $phone = null;
                if ($chargePermission->getShippingAddress() && $chargePermission->getShippingAddress()->getPhoneNumber()) {
                    $phone = $chargePermission->getShippingAddress()->getPhoneNumber();
                } elseif ($chargePermission->getBillingAddress() && $chargePermission->getBillingAddress()->getPhoneNumber()) {
                    $phone = $chargePermission->getBillingAddress()->getPhoneNumber();
                }
                if ($phone) {
                    zen_db_perform(TABLE_ORDERS, ['customers_telephone' => $phone], 'update', 'orders_id = ' . (int)$insert_id);
                }
            }

            if ($checkoutSession->getChargeId()) {
                $charge = $amazonPayHelper->getClient()->getCharge($checkoutSession->getChargeId());
                $transaction = $transactionHelper->saveNewCharge($charge, $insert_id);
                if ($transaction->status === StatusDetails::AUTHORIZED) {
                    $orderHelper = new OrderHelper();
                    $orderHelper->setOrderStatusAuthorized($insert_id);
                    $transactionHelper->capture($charge->getChargeId());                 
                }
            }

            $checkoutHelper->setOrderIdToChargePermission($checkoutSession->getChargePermissionId(), $insert_id);

            if (defined('AMAZON_PAY_V2_ORDER_COMMENT') && AMAZON_PAY_V2_ORDER_COMMENT === 'true') {            	
            // add a new OSH record for this order's Amazon Pay Reference
            $commentString = TEXT_AMAZON_PAY_V2_ORDER_REFERENCE  . $checkoutSession->getChargePermissionId();
            $new_order_status = (int)MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_STATUS_ID;
            zen_update_orders_history($insert_id, $commentString, null, $new_order_status, 0);
            }
           
        } catch (Exception $e) {
            $checkoutSession = $amazonPayHelper->getClient()->getCheckoutSession($_SESSION['amazon_checkout_session']);
            GeneralHelper::log('error', 'unexpected exception during checkout', [$e->getMessage(), $checkoutSession->toArray()]);
            $checkoutHelper->defaultErrorHandling();
        }
    }
    function getOrderTotal($orderId)
    {
    	  global $db;    	  
    	  $totalValue = 0;
        $otTotalSortOrder = 0;
        $voucherValue = 0;
        $otGvSortOrder = 0;
        
        $sql1 = "SELECT * FROM " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$orderId . "' and class = 'ot_total'";
        $orderTotal = $db->Execute($sql1);
        
         while (!$orderTotal->EOF) {
            
                $otTotalSortOrder = (int)$orderTotal->fields['sort_order'];
                $totalValue += $orderTotal->fields['value'];
         $orderTotal->MoveNext();
       }  
       $sql2 = "SELECT * FROM " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$orderId . "' and class = 'ot_gv'";
        $orderTotalGV = $db->Execute($sql2);
        
        while (!$orderTotalGV->EOF) {       
       
           
                $otGvSortOrder = (int)$orderTotalGV->fields['sort_order'];
                $voucherValue = $orderTotalGV->fields['value'];
          
          
            $orderTotalGV->MoveNext();
        }
       
        if ($voucherValue > 0 && $otGvSortOrder > $otTotalSortOrder) {
            $totalValue -= $voucherValue;
        }
        return $totalValue;
    }
    
    /**
    * Build admin-page components
    *
    * @param int $zf_order_id
    * @return string
    */
  function admin_notification($zf_order_id) {
    $output = '';
    require(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/amazon_pay_v2/amazon_pay_v2_admin_notification.php');
    return $output;
  }


    function get_error() {
     return ['error' => TEXT_AMAZON_PAY_V2_ERROR];
    }
  /**
   * Check to see whether module is installed
   *
   * @return boolean
   */
    function check() {
      global $db;
      if (!isset($this->_check)) {
        $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_AMAZON_PAY_V2_STATUS'");
        $this->_check = $check_query->RecordCount();           
        }
      return $this->_check;
    }
  

  /**
   * Install the payment module and its configuration settings
   *
   */
    function install() {
      global $db, $messageStack;
      if (defined('MODULE_PAYMENT_AMAZON_PAY_V2_STATUS')) {
        $messageStack->add_session(''. AMAZON_PAY_V2_MESSAGE_ALREADY_INSTALLED .'', 'error');
        zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=amazon_pay_v2', 'SSL'));
        return 'failed';
      }
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable this Payment Module', 'MODULE_PAYMENT_AMAZON_PAY_V2_STATUS', 'True', 'Do you want to enable this payment module?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Live or Sandbox', 'MODULE_PAYMENT_AMAZON_PAY_V2_SERVER', 'Sandbox', '<strong>Live: </strong> Used to process Live transactions<br><strong>Sandbox: </strong>For developers and testing', '6', '2', 'zen_cfg_select_option(array(\'Live\', \'Sandbox\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_AMAZON_PAY_V2_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '3', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_STATUS_ID', '2', 'Set the status of orders paid with this payment module to this value. <br><strong>Recommended: Processing[2]</strong>', '6', '4', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Unpaid Order Status', 'MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_PENDING_STATUS_ID', '1', 'Set the status of unpaid orders made with this payment module to this value. <br><strong>Recommended: Pending[1]</strong>', '6', '5', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Refund Order Status', 'MODULE_PAYMENT_AMAZON_PAY_V2_REFUNDED_STATUS_ID', '5', 'Set the status of refunded orders to this value. <br><strong>Recommended: Cancelled[5]</strong>', '6', '6', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
        // www.zen-cart-pro.at german admin settings languages_id==43
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Zahlung via Amazon Pay anbieten?', 'MODULE_PAYMENT_AMAZON_PAY_V2_STATUS', '43', 'Wollen Sie Zahlung via Amazon Pay aktivieren?', now())");
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Livesystem oder Sandbox', 'MODULE_PAYMENT_AMAZON_PAY_V2_SERVER', '43', 'Stellen Sie zunächst auf Sandbox, um alles im Testmodus zu testen. Dazu müssen Sie einen Key für die Sandbox erstellt und per FTP hochgeladen haben.<br>Nach erfolgreichen Tests, hinterlegen Sie Ihren Live Key und stellen auf Live um.<br><br><strong>Live: </strong> für echte Live Transaktionen<br><strong>Sandbox: </strong> zum Testen im Sandbox System', now())");
        $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Sortierreihenfolge', 'MODULE_PAYMENT_AMAZON_PAY_V2_SORT_ORDER', '43', 'An welcher Stelle der Zahlungsarten soll Amazon Pay angeboten werden? Niedrigste Werte werden zuoberst angezeigt.', now())");   
	      $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Bestellstatus für abgeschlossene Zahlungen', 'MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_STATUS_ID', '43', 'Welchen Bestellstatus sollen Bestellungen bekommen, die erfolgreich mit Amazon Pay bezahlt wurden?<br>Empfohlen: Zahlung erhalten - in Arbeit[2]', now())");
	      $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Bestellstatus für nicht abgeschlossene Zahlungen', 'MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_PENDING_STATUS_ID', '43', 'Welchen Bestellstatus sollen Bestellungen bekommen, die noch nicht erfolgreich mit Amazon Pay bezahlt wurden?<br>Empfohlen: warten auf Zahlung[1]', now())");
	      $db->Execute("replace into " . TABLE_CONFIGURATION_LANGUAGE   . " (configuration_title, configuration_key, configuration_language_id, configuration_description, date_added) values ('Bestellstatus für erstattete Zahlungen', 'MODULE_PAYMENT_AMAZON_PAY_V2_REFUNDED_STATUS_ID', '43', 'Welchen Bestellstatus sollen Bestellungen bekommen, die via Amazon Pay rückerstattet wurden?<br>Empfohlen: Storniert[5]', now())");
       }

    /**
   * Remove the module and all its settings
   *
     */
    function remove() {
      global $db;
      $db->Execute("DELETE FROM " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");     
      $db->Execute("DELETE FROM " . TABLE_CONFIGURATION_LANGUAGE . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
   
    }
  /**
   * Internal list of configuration keys used for configuration of the module
   *
   * @return array
   */
    function keys() {
        return [
      'MODULE_PAYMENT_AMAZON_PAY_V2_STATUS',    
      'MODULE_PAYMENT_AMAZON_PAY_V2_SORT_ORDER',      
      'MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_STATUS_ID',
      'MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_PENDING_STATUS_ID',
      'MODULE_PAYMENT_AMAZON_PAY_V2_REFUNDED_STATUS_ID',
      'MODULE_PAYMENT_AMAZON_PAY_V2_SERVER',
        ];
      
    }
  }