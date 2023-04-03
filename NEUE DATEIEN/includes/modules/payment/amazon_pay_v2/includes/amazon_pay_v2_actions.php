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
 * @version $Id: amazon_pay_v2_actions.php 2023-04-02 10:12:16Z webchills $
 */ 
 
require_once __DIR__ . '/../amazon_pay_v2.php';

$configHelper = new \ZencartAmazonPayV2\ConfigHelper();

if ((in_array($current_page_base,explode(",",'shopping_cart')) && !empty($_SESSION['payment']) && $_SESSION['payment'] === 'amazon_pay_v2')) {
   unset($_SESSION['payment']);
}

if (in_array($current_page_base,explode(",",'address_book')) ) {
    include __DIR__.'/actions/address_book.php';
}

if (in_array($current_page_base,explode(",",'account')) ) {
    include __DIR__.'/actions/account.php';
}