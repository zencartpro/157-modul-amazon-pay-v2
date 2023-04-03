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
 * @version $Id: checkout_process.php 2023-03-29 19:11:16Z webchills $
 */
require_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/amazon_pay_v2/amazon_pay_v2.php');
 use ZencartAmazonPayV2\GeneralHelper; 
  \ZencartAmazonPayV2\GeneralHelper::log('debug', 'start checkout_process');
if (isset($_POST['comments'])) {
    $_SESSION['comments'] = $_POST['comments'];
}
$checkoutHelper = new \ZencartAmazonPayV2\CheckoutHelper();
$configHelper   = new \ZencartAmazonPayV2\ConfigHelper();

if (empty($_SESSION['amazon_checkout_session'])) {
    \ZencartAmazonPayV2\GeneralHelper::log('warning', 'lost amazon checkout session id', $_SESSION);
    zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
}

$checkoutSession = $checkoutHelper->getCheckoutSession($_SESSION['amazon_checkout_session']);

if (!$checkoutSession || !$checkoutSession->getCheckoutSessionId()) {
    \ZencartAmazonPayV2\GeneralHelper::log('warning', 'invalid amazon checkout session id', [$_SESSION['amazon_checkout_session'], $checkoutSession]);
    zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
}
\ZencartAmazonPayV2\GeneralHelper::log('debug', 'checkout_process CheckoutSession', [$checkoutSession->toArray()]);


// bof error handling for declined/canceled transactions to prevent fake orders
if ($checkoutSession->getStatusDetails()->getState() === \AmazonPayApiSdkExtension\Struct\StatusDetails::REASON_DECLINED) {
\ZencartAmazonPayV2\GeneralHelper::log('debug', 'amazon pay payment reason declined', $checkoutSession->toArray());
$messageStack->add_session('shopping_cart', ERROR_AMAZON_PAY_V2_PAYMENT_DECLINED, 'error');
zen_redirect(zen_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
} else if ($checkoutSession->getStatusDetails()->getState() === \AmazonPayApiSdkExtension\Struct\StatusDetails::REASON_BUYER_CANCELED) {	
\ZencartAmazonPayV2\GeneralHelper::log('debug', 'amazon pay payment buyer canceled', $checkoutSession->toArray());
$messageStack->add_session('shopping_cart', ERROR_AMAZON_PAY_V2_PAYMENT_CANCELED, 'error');
zen_redirect(zen_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
} else if ($checkoutSession->getStatusDetails()->getState() === \AmazonPayApiSdkExtension\Struct\StatusDetails::CANCELED) {
\ZencartAmazonPayV2\GeneralHelper::log('debug', 'amazon pay payment declined', $checkoutSession->toArray());
$messageStack->add_session('shopping_cart', ERROR_AMAZON_PAY_V2_PAYMENT_DECLINED, 'error');
zen_redirect(zen_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
} else if ($checkoutSession->getStatusDetails()->getState() === \AmazonPayApiSdkExtension\Struct\StatusDetails::DECLINED) {
\ZencartAmazonPayV2\GeneralHelper::log('debug', 'amazon pay payment declined', $checkoutSession->toArray());
$messageStack->add_session('shopping_cart', ERROR_AMAZON_PAY_V2_PAYMENT_DECLINED, 'error');
zen_redirect(zen_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
// eof error handling for declined/canceled transactions to prevent fake orders
} else if ($checkoutSession->getStatusDetails()->getState() === \AmazonPayApiSdkExtension\Struct\StatusDetails::OPEN) {
    if ($checkoutSession->getWebCheckoutDetails()->getAmazonPayRedirectUrl() && !$checkoutSession->getConstraints()) {
        //do checkout on Amazon hosted page      
    } else {
    	
      $checkoutHelper->doUpdateCheckoutSessionBeforeCheckoutProcess($checkoutSession);
    }
  }