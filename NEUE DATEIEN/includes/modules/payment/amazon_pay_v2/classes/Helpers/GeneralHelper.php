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
 * @version $Id: GeneralHelper.php 2023-11-19 14:29:16Z webchills $
 */
namespace ZencartAmazonPayV2;

class GeneralHelper   
{
    public static function autoDecode($str)
    {
        

        return $str;
    }

    public static function autoEncode($str)
    {
        

        return utf8_encode($str);
    }

    public static function log($level, $msg, $data = null)
    {
       $fileName = $level.'_'.date('m-Y').'.log';
        $path = DIR_FS_CATALOG . 'includes/modules/payment/amazon_pay_v2/logs/' . $fileName;

        if (file_exists($path) && filesize($path) > 4000000) {
            rename($path, $path . '.' . date('Y-m-d_H-i-s') . '.log');
        }
        if (file_exists($path)) {
            @chmod($path, 0777);
        }

        file_put_contents($path, '['.date('Y-m-d H:i:s').'] '.str_pad($_SERVER['REMOTE_ADDR'], 18, ' ', STR_PAD_RIGHT).$msg."\n", 8);
        zen_db_perform('amazon_pay_v2_log', [
            'time'=>'now()',
            'msg'=>$msg,
            'ip'=>$_SERVER['REMOTE_ADDR'],
            'data'=>serialize($data)
        ]);
    }

}
