<?php
/**
 * Amazon Pay V2 for Zen Cart German 1.5.7
 * Copyright 2023 webchills (www.webchills.at)
 * based on Amazon Pay for Modified by AlkimMedia (www.alkim.de)
 * (https://github.com/AlkimMedia/AmazonPay_Modified_2060)
 * Portions Copyright 2003-2023 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * @version $Id: amazon_pay_v2.php 2023-03-08 12:01:16Z webchills $
 */

require_once __DIR__.'/vendor/autoload.php';

foreach(glob(__DIR__.'/classes/Helpers/*.php') as $_file){
    require_once $_file;
}

foreach(glob(__DIR__.'/classes/Models/*.php') as $_file){
    require_once $_file;
}

foreach(glob(__DIR__.'/classes/Struct/*.php') as $_file){
    require_once $_file;
}