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
 * @version $Id: AmazonPayHelper.php 2023-03-08 10:31:16Z webchills $
 */
namespace ZencartAmazonPayV2;

use AmazonPayApiSdkExtension\Client\Client;

class AmazonPayHelper
{
    private static $client;
    /**
     * @var \ZencartAmazonPayV2\ConfigHelper
     */
    private $configHelper;

    public function __construct()
    {
        $this->configHelper = new ConfigHelper();
    }

    /**
     * @return \AmazonPayApiSdkExtension\Client\Client
     */
    public function getClient()
    {
        if (!isset(self::$client)) {
            try {
                self::$client = new Client($this->configHelper->getMainConfig());
            } catch (\Exception $e) {
                GeneralHelper::log('error', 'Unable to get client', $e->getMessage());
            }
        }

        return self::$client;
    }

    public function getHeaders()
    {
        return ['x-amz-pay-Idempotency-Key' => uniqid()];
    }
}