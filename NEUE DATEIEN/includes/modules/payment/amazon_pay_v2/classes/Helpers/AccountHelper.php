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
 * @version $Id: AccountHelper.php 2023-11-19 12:42:16Z webchills $
 */
namespace ZencartAmazonPayV2;

use AmazonPayApiSdkExtension\Struct\Address;

class AccountHelper
{
    /**
     * @var \ZencartAmazonPayV2\AmazonPayHelper
     */
    private $amazonPayHelper;
    /**
     * @var \ZencartAmazonPayV2\CheckoutHelper
     */
    private $checkoutHelper;
    /**
     * @var \ZencartAmazonPayV2\ConfigHelper
     */
    private $configHelper;

    public function __construct()
    {
        $this->amazonPayHelper = new AmazonPayHelper();
        $this->checkoutHelper  = new CheckoutHelper();
        $this->configHelper = new ConfigHelper();
    }

    public function convertAddressToArray(Address $address)
    {
    	global $db;
        $name        = $address->getName();
        $t           = explode(' ', $name);
        $lastNameKey = max(array_keys($t));
        $lastName    = $t[$lastNameKey];
        unset($t[$lastNameKey]);
        $firstName = implode(' ', $t);

        if ($address->getAddressLine3() !== '') {
            $street  = trim($address->getAddressLine3());
            $company = trim($address->getAddressLine1() . ' ' . $address->getAddressLine2());
        } elseif ($address->getAddressLine2() !== '') {
            $street  = trim($address->getAddressLine2());
            $company = trim($address->getAddressLine1());
        } else {
            $street  = trim($address->getAddressLine1());
            $company = '';
        }
        $sql  = "SELECT countries_name, countries_id FROM " . TABLE_COUNTRIES . " WHERE countries_iso_code_2 = '" . $address->getCountryCode() . "' LIMIT 1";
        $country_result = $db->Execute($sql);

        return [
            'name'           => GeneralHelper::autoDecode($name),
            'firstname'      => GeneralHelper::autoDecode($firstName),
            'lastname'       => GeneralHelper::autoDecode($lastName),
            'company'        => GeneralHelper::autoDecode($company),
            'phone'          => GeneralHelper::autoDecode($address->getPhoneNumber()),
            'street_address' => GeneralHelper::autoDecode($street),
            'suburb'         => '',
            'city'           => GeneralHelper::autoDecode($address->getCity()),
            'postcode'       => GeneralHelper::autoDecode($address->getPostalCode()),
            'state'          => '',
            'country'        => [
                'iso_code_2' => GeneralHelper::autoDecode($address->getCountryCode()),
                'title'      => $country_result->fields['countries_name'],
                'id'         => $country_result->fields["countries_id"]
            ],
            'country_iso_2'  => GeneralHelper::autoDecode($address->getCountryCode()),
            'format_id'      => '5'
        ];
    }


    public function isLoggedIn()
    {
        
        return zen_is_logged_in();
    }

    public function getStatusId()
    {
        return (int)$_SESSION['customers_authorization'];
    }

    public function isAccountComplete($customersId){
    	global $db;
        $sql = "SELECT * FROM ".TABLE_CUSTOMERS." WHERE customers_id = ".(int)$customersId;
        $customer_result = $db->Execute($sql);
	if (!$customer_result->EOF) {
        
            if(empty($customer_result->fields['customers_firstname'])){
                return false;
            }
            if(empty($customer_result->fields['customers_lastname'])){
                return false;
            }
            if(ACCOUNT_DOB == 'true' && (empty($customer_result->fields['customers_dob']) || date('Y', strtotime($customer_result->fields['customers_dob'])) < 1900)){
                return false;
            }
            return true;
        }else{
            return null;
        }
    }

    public function hasAddress($customersId){
    	global $db;
        $sql = "SELECT * FROM ".TABLE_CUSTOMERS." c JOIN ".TABLE_ADDRESS_BOOK." a ON (c.customers_default_address_id = a.address_book_id) WHERE c.customers_id = ".(int)$customersId;
        $address_result = $db->Execute($sql);
	//if (!$address_result->EOF) {
	if ($address_result->RecordCount() == 1) {
        
            return true;
        }else{
            $sql = "SELECT * FROM ".TABLE_ADDRESS_BOOK." WHERE customers_id = ".(int)$customersId;
            $addressbook_result = $db->Execute($sql);
         //if (!$address_result->EOF) {
	 if ($address_result->RecordCount() == 1) {
                $db->Execute("UPDATE ".TABLE_CUSTOMERS." c SET customers_default_address_id = ".(int)$addressbook_result->fields['address_book_id']." WHERE customers_id = ".(int)$customersId);
                $_SESSION['customer_default_address_id'] = (int)$addressbook_result->fields['address_book_id'];
                return true;
            }else{
                return false;
            }
        }
    }

    public function getAddressId(Address $address, $customerId = null)
    {
    	global $db;
        if (empty($customerId)) {
            $customerId = $_SESSION['customer_id'];
        }
        $addressArray = $this->convertAddressToArray($address);
        $sql            = "SELECT * FROM " . TABLE_ADDRESS_BOOK . " WHERE
                            customers_id = " . (int)$customerId . "
                                AND
                            entry_firstname = '" . zen_db_input($addressArray['firstname']) . "'
                                AND
                            entry_lastname = '" . zen_db_input($addressArray['lastname']) . "'
                                AND
                            entry_street_address = '" . zen_db_input($addressArray['street_address']) . "'
                                AND
                            entry_postcode = '" . zen_db_input($addressArray['postcode']) . "'
                                AND
                            entry_city = '" . zen_db_input($addressArray['city']) . "'";
        $addressbook_query = $db->Execute($sql);
	if (!$addressbook_query->EOF) {
        

            return $addressbook_query->fields["address_book_id"];
        }

        return null;
    }

    public function createAddress(Address $address, $customerId = null)
    {
    	global $db;
        if (empty($customerId)) {
            $customerId = $_SESSION['customer_id'];
        }
        $addressArray = $this->convertAddressToArray($address);

        $address_book_sql_array = [
            'customers_id'         => $customerId,
            'entry_firstname'      => $addressArray['firstname'],
            'entry_lastname'       => $addressArray['lastname'],
            'entry_company'        => $addressArray['company'],
            'entry_suburb'         => $addressArray['suburb'],
            'entry_street_address' => $addressArray["street_address"],
            'entry_postcode'       => $addressArray['postcode'],
            'entry_city'           => $addressArray['city'],
            'entry_country_id'     => $addressArray['country']["id"]
        ];
        zen_db_perform(TABLE_ADDRESS_BOOK, $address_book_sql_array);
        return zen_db_insert_id();      
                
    }
    
    public function doLogin($customerId){
    	global $db;
        $customerId = (int)$customerId;
        $sql = "SELECT * FROM ".TABLE_CUSTOMERS." c LEFT JOIN ".TABLE_ADDRESS_BOOK." a ON (c.customers_default_address_id = a.address_book_id) WHERE c.customers_id = ".$customerId;
        $customer_query = $db->Execute($sql);
        if (!$customer_query->EOF) {
            $_SESSION['customer_gender'] = $customer_query->fields['customers_gender'];
            $_SESSION['customer_first_name'] = $customer_query->fields['customers_firstname'];
            $_SESSION['customer_last_name'] = $customer_query->fields['customers_lastname'];
            $_SESSION['customer_id'] = $customer_query->fields['customers_id'];
            
            $_SESSION['customer_default_address_id'] = $customer_query->fields['customers_default_address_id'];
            $_SESSION['customer_country_id'] = $customer_query->fields['entry_country_id'];
            $_SESSION['customer_zone_id'] = $customer_query->fields['entry_zone_id'];
            $_SESSION['customer_email_address'] = $customer_query->fields['customers_email_address'];
           
            $_SESSION['customer_id'] = $customerId;

            
        }
    }
}
