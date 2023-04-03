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
 * @version $Id: ConfigHelper.php 2023-03-22 07:49:16Z webchills $
 */
namespace ZencartAmazonPayV2;

use phpseclib\Crypt\RSA;


class ConfigHelper
{
    const FIELD_TYPE_STRING = 'string';
    const FIELD_TYPE_SELECT = 'select';
    const FIELD_TYPE_BOOL = 'bool';
    const FIELD_TYPE_READ_ONLY = 'read_only';
    const FIELD_TYPE_STATUS = 'status';    

    /**
     * @var \ZencartAmazonPayV2\Config
     */
    public $config;

    public function getMainConfig()
    {
    	if (defined('MODULE_PAYMENT_AMAZON_PAY_V2_SERVER') && MODULE_PAYMENT_AMAZON_PAY_V2_SERVER === 'Sandbox') {
        return [
            'public_key_id' => AMAZON_PAY_V2_PUBLIC_KEY_ID_SANDBOX,
            'private_key'   => $this->getPrivateKeyPath(),
            'region'        => AMAZON_PAY_V2_REGION,
            'sandbox'       => $this->isSandbox()
        ];
      } else {
      	return [
            'public_key_id' => AMAZON_PAY_V2_PUBLIC_KEY_ID,
            'private_key'   => $this->getPrivateKeyPath(),
            'region'        => AMAZON_PAY_V2_REGION,
            'sandbox'       => $this->isSandbox()
        ];
      	
      }
    }

    public function getPrivateKeyPath()
    {
    	if (defined('MODULE_PAYMENT_AMAZON_PAY_V2_SERVER') && MODULE_PAYMENT_AMAZON_PAY_V2_SERVER === 'Sandbox') {
        return $this->getBasePath() . 'keys/private-SANDBOX.pem';
      } else {
      	return $this->getBasePath() . 'keys/private-LIVE.pem';
      }
    }

    public function getBasePath()
    {
        return dirname(dirname(__DIR__)) . '/';
    }

    public function isSandbox()
    {
        return (MODULE_PAYMENT_AMAZON_PAY_V2_SERVER !== 'Live');
    }

    public function getCheckoutResultReturnUrl()
    {
        return zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');        
    }
    
   
    public function getCheckoutSessionAjaxUrl()
    {
    	$ajaxurl =''. HTTPS_SERVER . DIR_WS_HTTPS_CATALOG .'ext/modules/payment/amazon_pay_v2/create_checkout_session.php'; 
    	
        
        return $ajaxurl;
    }

    public function getLanguage()
    {
    
     $amazonlanguage='de';
     if ($_SESSION['language']=='english') {$amazonlanguage='en';} 
     if ($_SESSION['language']=='french') {$amazonlanguage='fr';}
     if ($_SESSION['language']=='italian') {$amazonlanguage='it';}
     if ($_SESSION['language']=='spanish') {$amazonlanguage='es';}
    	
        $supportedLanguages = [
            'en' => 'en_GB',
            'de' => 'de_DE',
            'fr' => 'fr_FR',
            'it' => 'it_IT',
            'es' => 'es_ES',
        ];
        if(isset($supportedLanguages[$amazonlanguage])){
            return $supportedLanguages[$amazonlanguage];
        }else{
            return 'de_DE';
        }
    } 
    
    public function getPaymentMethodName()
    {
        return 'amazon_pay_v2';
    }

    public function isActive()
    {
        return defined('MODULE_PAYMENT_AMAZON_PAY_V2_STATUS') && MODULE_PAYMENT_AMAZON_PAY_V2_STATUS === 'True';
    }

    public function getMerchantId()
    {
        return AMAZON_PAY_V2_MERCHANT_ID;
    }

    public function getClientId()
    {
        return AMAZON_PAY_V2_STORE_ID;
    }

    public function getConfigurationFields()
    {
        $this->initKey();
        return [
            
            'AMAZON_PAY_V2_REGION'                          => [
                'type'    => static::FIELD_TYPE_SELECT,
                'options' => [
                    ['text' => 'EU', 'id' => 'EU'],
                    ['text' => 'UK', 'id' => 'UK']
                ]
            ],
            'AMAZON_PAY_V2_MERCHANT_ID'                      => [
                'type' => static::FIELD_TYPE_STRING
            ],
            'AMAZON_PAY_V2_STORE_ID'                        => [
                'type' => static::FIELD_TYPE_STRING
            ],
            'AMAZON_PAY_V2_PUBLIC_KEY_ID'                    => [
                'type' => static::FIELD_TYPE_STRING
            ],
            
            'AMAZON_PAY_V2_PUBLIC_KEY_ID_SANDBOX'                    => [
                'type' => static::FIELD_TYPE_STRING
            ],
            
            'AMAZON_PAY_V2_IPN_URL'                          => [
                'type'  => static::FIELD_TYPE_READ_ONLY,
                'value' => (defined('HTTPS_CATALOG_SERVER') ? HTTPS_CATALOG_SERVER : HTTPS_SERVER) . DIR_WS_CATALOG . 'ext/modules/payment/amazon_pay_v2/ipn.php'
            ],
                        
            'MODULE_PAYMENT_AMAZON_PAY_V2_STATUS'     => [
                'type' => static::FIELD_TYPE_BOOL,
            ],
            'MODULE_PAYMENT_AMAZON_PAY_V2_SORT_ORDER' => [
                'type' => static::FIELD_TYPE_STRING
            ],
            'MODULE_PAYMENT_AMAZON_PAY_V2_SERVER'                          => [
                'type'    => static::FIELD_TYPE_SELECT,
                'options' => [
                    ['text' => 'Live', 'id' => 'Live'],
                    ['text' => 'Sandbox', 'id' => 'Sandbox']
                ]
            ],
           
            
            'MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_STATUS_ID'          => [
                'type' => static::FIELD_TYPE_STATUS,
            ],
            'MODULE_PAYMENT_AMAZON_PAY_V2_ORDER_PENDING_STATUS_ID'            => [
                'type' => static::FIELD_TYPE_STATUS,
            ],
            'APC_ORDER_STATUS_CAPTURED'            => [
                'type' => static::FIELD_TYPE_STATUS,
            ],            
            
            'AMAZON_PAY_V2_ORDER_COMMENT'             => [
                'type' => static::FIELD_TYPE_BOOL,
            ],
            

            'AMAZON_PAY_V2_LAYOUT_CHECKOUT_BUTTON'            => [
                'type'    => static::FIELD_TYPE_SELECT,
                'options' => [
                    ['text' => 'Gold', 'id' => 'Gold'],
                    ['text' => 'LightGray', 'id' => 'LightGray'],
                    ['text' => 'DarkGray', 'id' => 'DarkGray']
                ]
            ],
            'AMAZON_PAY_V2_LAYOUT_LOGIN_BUTTON'               => [
                'type'    => static::FIELD_TYPE_SELECT,
                'options' => [
                    ['text' => 'Gold', 'id' => 'Gold'],
                    ['text' => 'LightGray', 'id' => 'LightGray'],
                    ['text' => 'DarkGray', 'id' => 'DarkGray']
                ]
            ]
        ];
    }
    

    public function getConfigurationValue($key)
    {
    	global $db;
        $sql  = "SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='" . zen_db_input($key) . "'";        
        $getconfig = $db->Execute($sql);
        
         if (!$getconfig->EOF) {          	
            return $getconfig->fields['configuration_key'];
        } else {
            return null;
        }
    } 

    public function getAllowedCountries()
    {
    	global $db;
        $return = [];
        $sql      = "SELECT countries_iso_code_2 FROM " . TABLE_COUNTRIES . " WHERE status = '1'";
	$getisocode = $db->Execute($sql);
        while (!$getisocode->EOF) {  
        $return[$getisocode->fields['countries_iso_code_2']] = new \stdClass();
        $getisocode->MoveNext();
        }       
        return $return;
    }
    
        	  


    public function initKey(){
        
    } 
   

    public function getPublicKeyId()
    {
    	if (defined('MODULE_PAYMENT_AMAZON_PAY_V2_SERVER') && MODULE_PAYMENT_AMAZON_PAY_V2_SERVER === 'Sandbox') {
        return AMAZON_PAY_V2_PUBLIC_KEY_ID_SANDBOX;
      } else {
      	return AMAZON_PAY_V2_PUBLIC_KEY_ID;
      }
    }   

    public function getPluginVersion()
    {
        return Config::PLUGIN_VERSION;
    }

    public function getCustomInformationString(){
        return 'Created by webchills, '.Config::PLATFORM_NAME.', V'.$this->getPluginVersion();
    }

    public function getLedgerCurrency()
    {
        return AMAZON_PAY_V2_REGION === 'UK' ? 'GBP' : 'EUR';
    }

    public function canHandlePendingAuth(){
        return false;
    }
}
