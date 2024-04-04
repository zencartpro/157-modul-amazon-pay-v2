<?php
/**
 * Zen Cart German Specific (158 code in 157 / zencartpro adaptations)
 * Checkout Shipping Page
 *
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @copyright Portions Copyright 2003 osCommerce
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * Dieses Modul ist DONATIONWARE
 * Wenn Sie es in Ihrem Zen Cart Shop einsetzen, spenden Sie für die Weiterentwicklung der deutschen Zen Cart Version auf
 * https://spenden.zen-cart-pro.at
 * @version $Id: header_php.php 2024-04-04 18:51:16Z webchills $
 */
// This should be first line of the script:
$zco_notifier->notify('NOTIFY_HEADER_START_CHECKOUT_SHIPPING_AMAZON');
require_once(DIR_WS_CLASSES . 'http_client.php');

// if important address fields are incomplete after Amazon login redirect the customer to the account page
 global $db,$messageStack;
// verify if first name exists
    $firstname_query = 'SELECT customers_firstname
                            FROM   ' . TABLE_CUSTOMERS . '
                            WHERE  customers_id = :customersID     
                            ';
$firstname_query = $db->bindVars($firstname_query, ':customersID', $_SESSION['customer_id'], 'integer');
    
    $check_firstname = $db->Execute($firstname_query);

    if ($check_firstname->fields['customers_firstname'] == '') {
       $messageStack->add_session('account_edit', ERROR_NO_FIRSTNAME_DEFINED, 'error');
       zen_redirect(zen_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
    }
 
// verify if last name exists
    $lastname_query = 'SELECT customers_lastname
                            FROM   ' . TABLE_CUSTOMERS . '
                            WHERE  customers_id = :customersID     
                            ';
$lastname_query = $db->bindVars($lastname_query, ':customersID', $_SESSION['customer_id'], 'integer');
    
    $check_lastname = $db->Execute($lastname_query);

    if ($check_lastname->fields['customers_lastname'] == '') {
       $messageStack->add_session('account_edit', ERROR_NO_LASTNAME_DEFINED, 'error');
    zen_redirect(zen_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
    }
    
    // verify if street exists    
    if (!$_SESSION['sendto']) {
    $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
  }
    $street_query = 'SELECT entry_street_address
                            FROM   ' . TABLE_ADDRESS_BOOK . '
                            WHERE  customers_id = :customersID
                            AND    address_book_id = :addressBookID';
                            
$street_query = $db->bindVars($street_query, ':customersID', $_SESSION['customer_id'], 'integer');
$street_query = $db->bindVars($street_query, ':addressBookID', $_SESSION['sendto'], 'integer');
    
    $check_street = $db->Execute($street_query);

    if ($check_street->fields['entry_street_address'] == '') {
       $messageStack->add_session('address_book', ERROR_NO_STREET_DEFINED, 'error');
    zen_redirect(zen_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
    }

require_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/amazon_pay_v2/amazon_pay_v2.php');
use ZencartAmazonPayV2\AccountHelper;
use ZencartAmazonPayV2\CheckoutHelper;
use ZencartAmazonPayV2\ConfigHelper;
use ZencartAmazonPayV2\AmazonPayHelper;
$accountHelper = new \ZencartAmazonPayV2\AccountHelper();
$checkoutHelper  = new \ZencartAmazonPayV2\CheckoutHelper();
$configHelper    = new \ZencartAmazonPayV2\ConfigHelper();
$amazonPayHelper = new \ZencartAmazonPayV2\AmazonPayHelper;

global $db;
global $messageStack;
if (!empty($_GET['amazonCheckoutSessionId'])) {
    \ZencartAmazonPayV2\GeneralHelper::log('debug', 'start checkout_shipping');
    $checkoutHelper                      = new \ZencartAmazonPayV2\CheckoutHelper();
    $checkoutSessionId                   = $_GET['amazonCheckoutSessionId'];
    $_SESSION['amazon_checkout_session'] = $checkoutSessionId;
    $checkoutSession                     = $checkoutHelper->getCheckoutSession($checkoutSessionId);



   

    if ($shippingAddress = $checkoutSession->getShippingAddress()) {
        if ($shippingAddressId = $accountHelper->getAddressId($shippingAddress)) {
            $_SESSION["sendto"] = $shippingAddressId;
        } else {
            $_SESSION["sendto"] = $accountHelper->createAddress($shippingAddress);
        }
    } else {
        $_SESSION["sendto"] = false;
    }

    if ($billingAddressId = $accountHelper->getAddressId($checkoutSession->getBillingAddress())) {
        $_SESSION["billto"] = $billingAddressId;
    } else {
        $_SESSION["billto"] = $accountHelper->createAddress($checkoutSession->getBillingAddress());
    }
    

    $_SESSION['payment'] = 'amazon_pay_v2';
    
    


    if (!empty($_SESSION['shipping']) && !empty($_SESSION['shipping']['id'])) {
    	global $db;
        $sql  = "SELECT entry_postcode, entry_country_id FROM " . TABLE_ADDRESS_BOOK . " WHERE address_book_id = " . (int)$_SESSION['sendto'];
        $addressresult = $db->Execute($sql);
        if (!$addressresult->EOF) {
            if(isset($_SESSION['amazon_pay_delivery_zip']) && isset($_SESSION['amazon_pay_delivery_country'])){
                if ($_SESSION['amazon_pay_delivery_zip'] === $addressresult->fields['entry_postcode'] && $_SESSION['amazon_pay_delivery_country'] === $addressresult->fields['entry_country_id']) {
                    
                    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT_AMAZON)); 
                }
            }
        }
    }
} else {
    if ($accountHelper->isLoggedIn()) {
        if ($accountHelper->isAccountComplete($_SESSION['customer_id']) === false) {
            $_SESSION['checkout_with_incomplete_account_started'] = true;
            zen_redirect(zen_href_link(FILENAME_ACCOUNT_EDIT, 'amazon_pay_error=1'));
            
        }
        if ($accountHelper->hasAddress($_SESSION['customer_id']) === false) {
            $_SESSION['checkout_with_incomplete_account_started'] = true;
            zen_redirect(zen_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'amazon_pay_error=1'));            
        }
    }
} 
// check if is mobile or tablet visitor to allow order report mobile, tablet or desktop
  
if (!class_exists('MobileDetect')) {
  include_once(DIR_WS_CLASSES . 'vendors/MobileDetect/MobileDetect.php');
}

$detect = new \Detection\MobileDetect;
$isMobile = $detect->isMobile();
$isTablet = $detect->isTablet();

if ($detect->isMobile()) {
$_SESSION['mobilevisitor'] = true;
$_SESSION['tabletvisitor'] = false;
}

if ($detect->isTablet()) {
$_SESSION['tabletvisitor'] = true;
$_SESSION['mobilevisitor'] = false;
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($_SESSION['cart']->count_contents() <= 0) {
    zen_redirect(zen_href_link(FILENAME_TIME_OUT));
  }

$customer = new Customer($_SESSION['customer_id']);

// Validate Cart for checkout
  $_SESSION['valid_to_checkout'] = true;
  $_SESSION['cart']->get_products(true);
  if ($_SESSION['valid_to_checkout'] == false) {
    $messageStack->add_session('header', ERROR_CART_UPDATE, 'error');
    zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
  }

// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $_SESSION['cart']->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $qtyAvailable = zen_get_products_stock($products[$i]['id']);
      // compare against product inventory, and against mixed=YES
      if ($qtyAvailable - $products[$i]['quantity'] < 0 || $qtyAvailable - $_SESSION['cart']->in_cart_mixed($products[$i]['id']) < 0) {
          zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
          break;
        }
    }
  }

// if no shipping destination address was selected, use the customers own address as default
  if (empty($_SESSION['sendto'])) {
    $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
  } else {
// verify the selected shipping address
    $check_address_query = "SELECT count(*) AS total
                            FROM   " . TABLE_ADDRESS_BOOK . "
                            WHERE  customers_id = :customersID
                            AND    address_book_id = :addressBookID";

    $check_address_query = $db->bindVars($check_address_query, ':customersID', $_SESSION['customer_id'], 'integer');
    $check_address_query = $db->bindVars($check_address_query, ':addressBookID', $_SESSION['sendto'], 'integer');
    $check_address = $db->Execute($check_address_query);

    if ($check_address->fields['total'] != '1') {
      $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
      unset($_SESSION['shipping']);
    }
  }

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
if (isset($_SESSION['cart']->cartID)) {
  if (!isset($_SESSION['cartID']) || $_SESSION['cart']->cartID != $_SESSION['cartID']) {
    $_SESSION['cartID'] = $_SESSION['cart']->cartID;
  }
} else {
  zen_redirect(zen_href_link(FILENAME_TIME_OUT));
}

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    $_SESSION['shipping'] = array();
    $_SESSION['shipping']['id'] = 'free_free';
    $_SESSION['shipping']['title'] = 'free_free';
    $_SESSION['shipping']['cost'] = 0;
    $_SESSION['sendto'] = false;
    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT_AMAZON, '', 'SSL'));
  }

  $total_weight = $_SESSION['cart']->show_weight();
  $total_count = $_SESSION['cart']->count_contents();

// load all enabled shipping modules
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping;

  $pass = true;
  $free_shipping = false;
  if ( defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') ) {
    $pass = false;

    switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
      case 'national':
        if ($order->delivery['country_id'] == STORE_COUNTRY) {
          $pass = true;
        }
        break;
      case 'international':
        if ($order->delivery['country_id'] != STORE_COUNTRY) {
          $pass = true;
        }
        break;
      case 'both':
        $pass = true;
        break;
    }

    if ( ($pass == true) && ($_SESSION['cart']->show_total() >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
      $free_shipping = true;
    }
  }

  require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));

  if (isset($_SESSION['comments'])) {
    $comments = $_SESSION['comments'];
  }


// process the selected shipping method
  if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
    if (isset($_POST['comments'])) {
      $_SESSION['comments'] = $_POST['comments'];
    }
    $comments = isset($_SESSION['comments']) ? $_SESSION['comments'] : '';
    $quote = array();

    if ( (zen_count_shipping_modules() > 0) || ($free_shipping == true) ) {
      if ( (isset($_POST['shipping'])) && (strpos($_POST['shipping'], '_')) ) {
        /**
         * check to be sure submitted data hasn't been tampered with
         */
        if ($_POST['shipping'] == 'free_free' && ($order->content_type != 'virtual' && !$pass)) {
          $quote['error'] = 'Invalid input. Please make another selection.';
        }
        list($module, $method) = explode('_', $_POST['shipping']);
        if ( (isset($$module) && is_object($$module)) || ($_POST['shipping'] == 'free_free') ) {
          if ($_POST['shipping'] == 'free_free') {
            $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
            $quote[0]['methods'][0]['cost'] = '0';
            $quote[0]['methods'][0]['icon'] = '';
          } else {
            $quote = $shipping_modules->quote($method, $module);
          }
          if (isset($quote[0]['error'])) {
            unset($_SESSION['shipping']);
          } else {
            if ( (isset($quote[0]['methods'][0]['title'])) && (isset($quote[0]['methods'][0]['cost'])) ) {
              $_SESSION['shipping'] = array('id' => $_POST['shipping'],
                                'title' => (($free_shipping == true) ?  $quote[0]['methods'][0]['title'] : $quote[0]['module'] . ' (' . $quote[0]['methods'][0]['title'] . ')'),
                                'cost' => $quote[0]['methods'][0]['cost']);

              zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT_AMAZON, '', 'SSL'));
            }
          }
        } else {
          unset($_SESSION['shipping']);
        }
      }
    } else {
      unset($_SESSION['shipping']);

      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT_AMAZON, '', 'SSL'));
    }
  }

// get all available shipping quotes
  $quotes = $shipping_modules->quote();

  // check that the currently selected shipping method is still valid (in case a zone restriction has disabled it, etc)
  if (isset($_SESSION['shipping']['id'])) {
    $checklist = [];
    foreach ($quotes as $key=>$val) {
      if (is_array($val['methods'])) {
        foreach($val['methods'] as $key2=>$method) {
          $checklist[] = $val['id'] . '_' . $method['id'];
        }
      }
    }
    $checkval = $_SESSION['shipping']['id'];
    if (!in_array($checkval, $checklist)) {
      $messageStack->add('checkout_shipping', ERROR_PLEASE_RESELECT_SHIPPING_METHOD, 'error');
      unset($_SESSION['shipping']); // Prepare $_SESSION to determine lowest available price/force a default selection mc12345678 2018-04-03
    }
  }

// If no shipping method has been selected, automatically select the cheapest method.
// If the module's status was changed when none were available, to save on implementing
// a javascript force-selection method, also automatically select the cheapest shipping
// method if more than one module is now enabled
  if ((!isset($_SESSION['shipping']) || (!isset($_SESSION['shipping']['id']) || $_SESSION['shipping']['id'] == '') && zen_count_shipping_modules() >= 1)) $_SESSION['shipping'] = $shipping_modules->cheapest();

  // Should address-edit button be offered?
  $displayAddressEdit = (MAX_ADDRESS_BOOK_ENTRIES >= 2);

  // if shipping-edit button should be overridden, do so
  $editShippingButtonLink = zen_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL');
  if (isset($_SESSION['payment']) && isset(${$_SESSION['payment']}) && method_exists(${$_SESSION['payment']}, 'alterShippingEditButton')) {
    $theLink = ${$_SESSION['payment']}->alterShippingEditButton();
    if ($theLink) {
      $editShippingButtonLink = $theLink;
      $displayAddressEdit = true;
    }
  }

  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment;

  $breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_CHECKOUT_SHIPPING_AMAZON, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);

// This should be last line of the script:
  $zco_notifier->notify('NOTIFY_HEADER_END_CHECKOUT_SHIPPING_AMAZON');
