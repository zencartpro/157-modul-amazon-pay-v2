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
 * @version $Id: CheckoutHelper.php 2023-03-28 16:11:16Z webchills $
 */
namespace ZencartAmazonPayV2;

use AmazonPayApiSdkExtension\Struct\AddressRestrictions;
use AmazonPayApiSdkExtension\Struct\CheckoutSession;
use AmazonPayApiSdkExtension\Struct\DeliverySpecifications;
use AmazonPayApiSdkExtension\Struct\MerchantMetadata;
use AmazonPayApiSdkExtension\Struct\PaymentDetails;
use AmazonPayApiSdkExtension\Struct\Price;
use AmazonPayApiSdkExtension\Struct\WebCheckoutDetails;

use order;
use order_total;
use shipping;

class CheckoutHelper
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
        $this->configHelper = new ConfigHelper();
    }

    public function createCheckoutSession()
    {
        try {
            $storeName = (strlen(STORE_NAME) <= 50) ? STORE_NAME : (substr(STORE_NAME, 0, 47) . '...');

            $merchantData = new MerchantMetadata();
            $merchantData->setMerchantStoreName($storeName);
            $merchantData->setCustomInformation($this->configHelper->getCustomInformationString());
           
            $webCheckoutDetails = new WebCheckoutDetails();
            $webCheckoutDetails->setCheckoutReviewReturnUrl(zen_href_link(FILENAME_CHECKOUT_SHIPPING_AMAZON, '', 'SSL'));            

            $addressRestrictions = new AddressRestrictions();
            $addressRestrictions->setType('Allowed')
                ->setRestrictions($this->configHelper->getAllowedCountries());
            $deliverySpecifications = new DeliverySpecifications();
            $deliverySpecifications->setAddressRestrictions($addressRestrictions);

            $checkoutSession = new CheckoutSession();
            $checkoutSession->setMerchantMetadata($merchantData)
                ->setWebCheckoutDetails($webCheckoutDetails)
                ->setStoreId($this->configHelper->getClientId())                
                
                ->setDeliverySpecifications($deliverySpecifications);

            return $this->amazonPayHelper->getClient()->createCheckoutSession($checkoutSession);
        } catch (\Exception $e) {
            GeneralHelper::log('error', 'createCheckoutSession failed', $e->getMessage());
        }
        return null;
    }

    public function getCheckoutSession($checkoutSessionId)
    {
        try {
            return $this->amazonPayHelper->getClient()->getCheckoutSession($checkoutSessionId);
        } catch (\Exception $e) {
            GeneralHelper::log('error', 'getCheckoutSession failed', [$e->getMessage(), $checkoutSessionId]);
        }
        return null;
    }

    public function updateCheckoutSession($checkoutSessionId, CheckoutSession $checkoutSession)
    {
        try {
            return $this->amazonPayHelper->getClient()->updateCheckoutSession($checkoutSessionId, $checkoutSession);
        } catch (\Exception $e) {
            GeneralHelper::log('error', 'updateCheckoutSession failed', [$e->getMessage(), $checkoutSessionId, $checkoutSession]);
        }
        return null;
    }

    public function setOrderIdToChargePermission($chargePermissionId, $orderId)
    {

        $this->amazonPayHelper->getClient()->updateChargePermission(
            $chargePermissionId,
            ['merchantMetadata' => ['merchantReferenceId' => $orderId]]
        );
    }

    protected function getCachedSignature($payload)
    {
        $storageKey = 'apcv2_button_signature_' . md5(serialize([$this->configHelper->getMainConfig(), $payload]));
        $cacheFile = DIR_FS_CATALOG . 'cache/amazon_pay_v2/' . $storageKey;
        if (file_exists($cacheFile) && filemtime($cacheFile) > time() - 28800) {
            return file_get_contents($cacheFile);
        }

        $client = $this->amazonPayHelper->getClient();
        $signature = $client->generateButtonSignature($payload);
        file_put_contents($cacheFile, $signature);
        return $signature;
    }

    public function getJs($placement = 'Cart')
    {
        if (!$this->configHelper->isActive()) {
            return '';
        }
        $merchantId = $this->configHelper->getMerchantId();
        $createCheckoutSessionUrl = $this->configHelper->getCheckoutSessionAjaxUrl();
        $isSandbox = $this->configHelper->isSandbox() ? 'true' : 'false';
        $language = $this->configHelper->getLanguage();
        $ledgerCurrency = $this->configHelper->getLedgerCurrency();
        $checkoutSessionId = (!empty($_SESSION['amazon_checkout_session']) ? $_SESSION['amazon_checkout_session'] : '');           
        $checkoutButtonColor = AMAZON_PAY_V2_LAYOUT_CHECKOUT_BUTTON;
        $loginButtonColor = AMAZON_PAY_V2_LAYOUT_LOGIN_BUTTON;

        $signinurl =''. HTTPS_SERVER . DIR_WS_HTTPS_CATALOG .'amazon_pay_v2_login.php'; 

        $loginPayload = json_encode([
            'signInReturnUrl' => $signinurl,
            'storeId' => $this->configHelper->getClientId(),
            'signInScopes' => ["name", "email", "postalCode", "shippingAddress"],
        ]);

        $productType = 'PayAndShip';
        if ($_SESSION['cart']->count_contents() > 0) {        	
            if ($_SESSION['cart']->get_content_type() === 'virtual') {
                $productType = 'PayOnly';
            }
        }
        $loginSignature = $this->getCachedSignature($loginPayload);
        $publicKeyId = $this->configHelper->getPublicKeyId();   
	      
        $return = <<<EOT
                <script src="https://static-eu.payments-amazon.com/checkout.js"></script>
                
                <script type="text/javascript" charset="utf-8">
                    
                    try{
                        amazon.Pay.bindChangeAction('#amz-change-address', {
                            amazonCheckoutSessionId: '$checkoutSessionId',
                            changeAction: 'changeAddress'
                        });
                    }catch(e){
                        //console.warn(e);
                    }
                    try{
                        amazon.Pay.bindChangeAction('#amz-change-payment', {
                            amazonCheckoutSessionId: '$checkoutSessionId',
                            changeAction: 'changePayment'
                        });
                    }catch(e){
                        //console.warn(e);
                    }
                    try{
                        var buttons = document.querySelectorAll('.amazon-pay-button');
                        for (var i = 0; i < buttons.length; i++) {
                            var button = buttons[i];
                            var id  = 'amazon-pay-button-' + zencartAmazonPayV2.payButtonCount++;
                            button.id = id;
                            amazon.Pay.renderButton('#' + id, {
                                merchantId: '$merchantId',
                                createCheckoutSession: {
                                    url: '$createCheckoutSessionUrl'
                                },
                                sandbox: $isSandbox,
                                ledgerCurrency: '$ledgerCurrency',
                                checkoutLanguage: '$language',
                                productType: '$productType',
                                placement: '$placement',
                                buttonColor: '$checkoutButtonColor'
                            });
                         }
                    }catch(e){
                        //console.warn(e);
                    }
                    
                    try{
                        var btn = amazon.Pay.renderButton('#amazon-pay-button-manual', {
                            merchantId: '$merchantId',
                            sandbox: $isSandbox,
                            ledgerCurrency: '$ledgerCurrency',
                            checkoutLanguage: '$language',
                            productType: '$productType',
                            placement: '$placement',
                            buttonColor: '$checkoutButtonColor'
                        });
                        zencartAmazonPayV2.initCheckout = function(){
                            btn.initCheckout({
                                createCheckoutSession: {
                                    url: '$createCheckoutSessionUrl'
                                }
                            });
                        }
                    }catch(e){
                        //console.warn(e);
                    }
                    
                    try{
                        var btn = amazon.Pay.renderButton('#amazon-pay-button-product-info', {
                            merchantId: '$merchantId',
                            sandbox: $isSandbox,
                            ledgerCurrency: '$ledgerCurrency',
                            checkoutLanguage: '$language',
                            productType: '$productType',
                            placement: '$placement',
                            buttonColor: '$checkoutButtonColor'
                        });
                        
                        btn.onClick(function(){
                            zencartAmazonPayV2.ajaxPost(document.getElementById('cart_quantity'), function(){
                                btn.initCheckout({
                                    createCheckoutSession: {
                                        url: '$createCheckoutSessionUrl'
                                    }
                                });
                            });
                        });
                    }catch(e){
                        //console.warn(e);
                    }
                    
                    try{
                        var buttons = document.querySelectorAll('.amazon-login-button');
                        for (var i = 0; i < buttons.length; i++) {
                            var button = buttons[i];
                            var id  = 'amazon-login-button-' + zencartAmazonPayV2.payButtonCount++;
                            button.id = id;
                            amazon.Pay.renderButton('#' + id, {
                                merchantId: '$merchantId',
                                sandbox: $isSandbox,
                                ledgerCurrency: '$ledgerCurrency',
                                checkoutLanguage: '$language',
                                productType: 'SignIn',
                                placement: '$placement',
                                buttonColor: '$loginButtonColor',
                                signInConfig: {                     
                                    payloadJSON: '$loginPayload',
                                    signature: '$loginSignature',
                                    publicKeyId: '$publicKeyId' 
                                }
                            });
                         }
                    }catch(e){
                        //console.warn(e);
                    }
                </script>
EOT;
        

        return $return;
    }

    /**
     * @param $checkoutSession
     */
    public function doUpdateCheckoutSessionBeforeCheckoutProcess($checkoutSession)
    {
        if (!empty($_SESSION['amazon_pay_checkout_no_pay'])) {
            unset($_SESSION['amazon_pay_checkout_no_pay']);
            return;
        }
        global $order, $order_totals, $shipping_modules, $order_total_modules;
        require_once DIR_WS_CLASSES . 'payment.php';
        require_once DIR_WS_CLASSES . 'shipping.php';
        $shipping_modules = new shipping($_SESSION['shipping']);
        require_once DIR_WS_CLASSES . 'order.php';
        $order = new order();
        require_once DIR_WS_CLASSES . 'order_total.php';
        $order_total_modules = new order_total();
        $order_totals = $order_total_modules->process();

        if ($order->info['total'] <= 0) {
            $_SESSION['amazon_pay_checkout_no_pay'] = 1;
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PROCESS));
        }
        $checkoutSessionUpdate = new CheckoutSession();

        $webCheckoutDetails = new WebCheckoutDetails();
        $webCheckoutDetails->setCheckoutResultReturnUrl($this->configHelper->getCheckoutResultReturnUrl());

        $paymentDetails = new PaymentDetails();
        $paymentDetails
            ->setPaymentIntent('Authorize')
            ->setCanHandlePendingAuthorization($this->configHelper->canHandlePendingAuth())
            ->setChargeAmount(new Price(['amount' => $order->info['total'], 'currencyCode' => $order->info['currency']]));

        $checkoutSessionUpdate
            ->setWebCheckoutDetails($webCheckoutDetails)
            ->setPaymentDetails($paymentDetails);
        $updatedCheckoutSession = $this->updateCheckoutSession($checkoutSession->getCheckoutSessionId(), $checkoutSessionUpdate);

        if ($redirectUrl = $updatedCheckoutSession->getWebCheckoutDetails()->getAmazonPayRedirectUrl()) {
            zen_redirect($redirectUrl);
        } else {
            GeneralHelper::log('warning', 'updateCheckoutSession failed', $checkoutSessionUpdate);
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING_AMAZON, 'amazon_pay_error', 'SSL'));
        }
    }

    public function defaultErrorHandling()
    {
        unset($_SESSION['payment']);
        global $messageStack;
        $messageStack->add_session('checkout_payment_amazon', 'ERROR', 'error');
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING_AMAZON, 'amazon_pay_error', 'SSL'));
    }
}
