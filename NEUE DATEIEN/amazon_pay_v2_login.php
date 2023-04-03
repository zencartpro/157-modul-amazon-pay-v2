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
 * @version $Id: amazon_pay_v2_login.php 2023-04-01 12:59:16Z webchills $
 */

include 'includes/application_top.php';

if (empty($_GET['buyerToken'])) {
    zen_redirect(zen_href_link(FILENAME_DEFAULT));
}

require_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/amazon_pay_v2/amazon_pay_v2.php');
use ZencartAmazonPayV2\AccountHelper;

$accountHelper   = new \ZencartAmazonPayV2\AccountHelper();

$checkoutHelper  = new \ZencartAmazonPayV2\CheckoutHelper();
$configHelper    = new \ZencartAmazonPayV2\ConfigHelper();
$amazonPayHelper = new \ZencartAmazonPayV2\AmazonPayHelper;
$token           = $_GET['buyerToken'];
$buyer = $amazonPayHelper->getClient()->getBuyer($token);
global $db;

 // check if email exists
$sql = "SELECT * FROM " . TABLE_CUSTOMERS . " WHERE customers_email_address = :emailAddress ";
$sql = $db->bindVars($sql, ':emailAddress', zen_db_input($buyer['email']), 'string');
$check_customer = $db->Execute($sql);

if (!$check_customer->EOF) {
    $accountHelper->doLogin($check_customer->fields['customers_id']);
    // set the session
    $_SESSION['amazonpaylogin'] = true;
} else {
    // Generate a random 8-char password
    $password = zen_create_random_value(8);
    $names    = explode(' ', $buyer['name']);
    if (count($names) > 1) {
        $lastName  = array_pop($names);
        $firstName = implode(' ', $names);
    } else {
        $lastName  = $buyer['name'];
        $firstName = '';
    }
    $sql_data_array = [
        'customers_authorization' => '0',
        'customers_gender' => 'd',
        'customers_firstname' => $firstName,
        'customers_lastname' => $lastName,
        'customers_dob' => '0001-01-01 00:00:00',
        'customers_email_address' => $buyer['email'],
        'customers_default_address_id' => '0',
        'customers_telephone' => '00000000',
        'customers_password' => zen_encrypt_password($password),
        'customers_newsletter' => 0,
	      'customers_amazonpay_ec' => 1,        
    ];
    
    // insert the data
   $result = zen_db_perform(TABLE_CUSTOMERS, $sql_data_array);

   // grab the customer_id (last insert id)
   $customerId = $db->Insert_ID();  
   
   // get the address data from Amazon
   
    $address = new AmazonPayApiSdkExtension\Struct\Address($buyer['shippingAddress']);
    $addressBookSqlArray = $accountHelper->convertAddressToArray($address);
    
    $addressId = (int)$accountHelper->createAddress($address, $customerId);
   
   // set the address id lookup for the customer
        $sql = "UPDATE " . TABLE_CUSTOMERS . "
                SET customers_default_address_id = :addrID
                WHERE customers_id = :custID";
        $sql = $db->bindVars($sql, ':addrID', $addressId, 'integer');
        $sql = $db->bindVars($sql, ':custID', $customerId, 'integer');
        $db->Execute($sql);

        // insert the new customer_id into the customers info table for consistency
        $sql = "INSERT INTO " . TABLE_CUSTOMERS_INFO . "
                       (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created, customers_info_date_of_last_logon)
                VALUES (:custID, 1, now(), now())";
        $sql = $db->bindVars($sql, ':custID', $customerId, 'integer');
        $db->Execute($sql); 
        
    // send welcome email if activated
        if (defined('AMAZON_PAY_V2_SEND_WELCOME_EMAIL') && AMAZON_PAY_V2_SEND_WELCOME_EMAIL == 'true') {
          // require the language file
          require(zen_get_file_directory(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $_SESSION['language'] . "/", 'create_account.php', 'false'));
          // set the mail text
          $email_text = sprintf(EMAIL_GREET_NONE, $firstName) ;
          $email_text .= "\n" . EMAIL_EC_AMAZON_ACCOUNT_INFORMATION . "\n\nEmail: " . $buyer['email'] . "\nPassword: " . $password . "\n\n";
          $email_text .= EMAIL_WELCOME . "\n\n" . EMAIL_TEXT;
          $email_text .= EMAIL_CONTACT;
          // include create-account-specific disclaimer
          $email_text .= "\n\n" . sprintf(EMAIL_DISCLAIMER_NEW_CUSTOMER, STORE_OWNER_EMAIL_ADDRESS). "\n\n";
          $email_html = array();
          $email_html['EMAIL_GREETING']      = sprintf(EMAIL_GREET_NONE, $firstName) ;
          $email_html['EMAIL_WELCOME']  = nl2br(EMAIL_EC_AMAZON_ACCOUNT_INFORMATION . "\n\nEmail: " . $buyer['email'] . "\nPassword: " . $password . "\n");
          $email_html['EMAIL_MESSAGE_HTML']       = EMAIL_WELCOME . "\n\n" . EMAIL_TEXT;         
          $email_html['EMAIL_CONTACT_OWNER'] = EMAIL_CONTACT;
          $email_html['EMAIL_CLOSURE']       = nl2br(EMAIL_GV_CLOSURE);
          $email_html['EMAIL_DISCLAIMER']    = sprintf(EMAIL_DISCLAIMER_NEW_CUSTOMER, '<a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">'. STORE_OWNER_EMAIL_ADDRESS .' </a>');

          // send the mail
          if (trim(EMAIL_SUBJECT) != 'n/a') zen_mail($firstName . " " . $lastName, $buyer['email'], EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_html, 'welcome');
        }
               
    
    // update the default address id
   
    zen_db_perform(TABLE_CUSTOMERS, ['customers_default_address_id' => $addressId], 'update', 'customers_id = ' . (int)$customerId);

   // and log the new customer in
    $accountHelper->doLogin($customerId); 
   // and set the session
    $_SESSION['amazonpaylogin'] = true;  
}
zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));