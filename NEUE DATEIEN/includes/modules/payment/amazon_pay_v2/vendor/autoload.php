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
 * @version $Id: autoload.php 2023-03-18 12:28:16Z webchills $
 */

require_once __DIR__ . '/composer/autoload_real.php';

$loader = ComposerAutoloaderInit305ae0d84c6c83b0459a4ad369687298::getLoader();

require_once __DIR__.'/mkreusch/amazon-pay-api-sdk-php-extension/Struct/StructBase.php';
require_once __DIR__.'/mkreusch/amazon-pay-api-sdk-php-extension/Struct/Price.php';
foreach(glob(__DIR__.'/mkreusch/amazon-pay-api-sdk-php-extension/*/*.php') as $_file){
    require_once $_file;
}

return $loader;