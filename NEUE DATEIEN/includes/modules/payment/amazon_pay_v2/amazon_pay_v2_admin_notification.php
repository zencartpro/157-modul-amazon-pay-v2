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
 * @version $Id: amazon_pay_v2_admin_notification.php 2023-04-08 12:15:16Z webchills $
 */

require_once __DIR__ . '/amazon_pay_v2.php';

$orderId = (int)$_GET['oID'];
$originalTotal = 0;
$capturedTotal = 0;
$hasOpenCharge = false;
$chargePermissionId = null;
?>
<style>
    #amazon-pay-panel{
        margin:10px 0;
        border:0px solid #999;
        background:#f4f4f4;
        padding:8px;
    }

    #amazon-pay-panel h2{
        margin:0;
        padding:0 0 5px 0;
        font-size: 1.4em;
    }

    #amazon-pay-panel h3{
        margin:0;
        padding: 5px 0;
        font-size: 1.2em;
    }
    
    #outputAmazon td, th {
    padding: 10px;
   }
</style>
<?php
if(!empty($_SESSION['amazon_pay_admin_error'])){
    echo '<div style="background:#ffdddd; border: #cc0000; padding:10px; margin: 10px 0; display:inline-block;">'.$_SESSION['amazon_pay_admin_error'].'</div>';
    unset($_SESSION['amazon_pay_admin_error']);
}
?>
<div id="amazon-pay-panel">
<h3><?php echo BOX_CUSTOMERS_AMAZON_PAY_V2; ?></h3>
<table id="outputAmazon">
    <tr>
        <th style="text-align:left;"><?php echo AMAZON_ADMIN_HEADER_TYPE; ?></th>
        <th style="text-align:left;"><?php echo AMAZON_ADMIN_HEADER_REFERENCE; ?></th>
        <th style="text-align:left;">Status</th>
        <th style="text-align:left;"><?php echo AMAZON_ADMIN_HEADER_AMOUNT; ?></th>
        <th style="text-align:left;">Captured</th>
        <th style="text-align:left;"><?php echo AMAZON_ADMIN_HEADER_REFUNDED; ?></th>
        <th style="text-align:left;"><?php echo AMAZON_ADMIN_HEADER_ACTIONS; ?></th>
    </tr>
    <?php
    global $db;
$sql = "SELECT * FROM " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " WHERE order_id = ".$orderId;
$getorder = $db->Execute($sql);
    while (!$getorder->EOF) {
    $amazonPayHelper = new \ZencartAmazonPayV2\AmazonPayHelper();
    $transactionHelper = new \ZencartAmazonPayV2\Helpers\TransactionHelper();
    $apiClient = $amazonPayHelper->getClient();
    $transaction = new \ZencartAmazonPayV2\Models\Transaction($getorder);
    
    if($getorder->fields['type'] === 'Refund' && $getorder->fields['status'] === \AmazonPayApiSdkExtension\Struct\StatusDetails::REFUND_INITIATED){
        $refund = $apiClient->getRefund($getorder->fields['reference']);
        $transactionHelper->updateRefund($refund);
        $transaction = $transactionHelper->getTransaction($refund->getRefundId());
    }

    if($getorder->fields['type'] === 'Charge' && $getorder->fields['status'] === \AmazonPayApiSdkExtension\Struct\StatusDetails::AUTHORIZATION_INITIATED){
        $charge = $apiClient->getCharge($getorder->fields['reference']);
        $transactionHelper->updateCharge($charge);
        $transaction = $transactionHelper->getTransaction($charge->getChargeId());
    }

    if($getorder->fields['type'] === 'ChargePermission'){
        $originalTotal = $transaction->charge_amount;
        $chargePermissionId = $transaction->reference;
    }

    if($getorder->fields['type'] === 'Charge'){
        $capturedTotal += $transaction->captured_amount;
        if($getorder->fields['status'] === \AmazonPayApiSdkExtension\Struct\StatusDetails::OPEN || $getorder->fields['status'] === \AmazonPayApiSdkExtension\Struct\StatusDetails::AUTHORIZATION_INITIATED){
            $hasOpenCharge = true;
        }
    }
    
    echo '<tr>
            <td style="vertical-align: top">'.$getorder->fields['type'].'</td>
            <td style="vertical-align: top">'.$getorder->fields['reference'].'</td>
            <td style="vertical-align: top">'.$getorder->fields['status'].'</td>
            <td style="vertical-align: top">'.number_format($getorder->fields['charge_amount'], 2, ',', '.').' '.$getorder->fields['currency'].'</td>
            <td style="vertical-align: top">'.($getorder->fields['type'] === 'Charge'?number_format($getorder->fields['captured_amount'], 2, ',', '.').' '.$getorder->fields['currency']:'').'</td>
            <td style="vertical-align: top">'.($getorder->fields['type'] === 'Charge'?number_format($getorder->fields['refunded_amount'], 2, ',', '.').' '.$getorder->fields['currency']:'').'</td>
            <td style="vertical-align: top">';
	    
    
    if($getorder->fields['type'] === 'Charge' && $getorder->fields['status'] === \AmazonPayApiSdkExtension\Struct\StatusDetails::CAPTURED && round($getorder->fields['captured_amount']*1.15, 2) - $getorder->fields['refunded_amount'] > 0){
        $amount = max($getorder->fields['captured_amount'] - $getorder->fields['refunded_amount'], 0);
        $maxAmount = round($getorder->fields['captured_amount']*1.15, 2) - $getorder->fields['refunded_amount'];
        echo zen_draw_form('amzazon_pay_refund', FILENAME_ORDERS, 'oID='.$orderId.'&action=edit&amazon_pay_action=refund&charge_id='.$getorder->fields['reference']).'
                <input type="number" name="amount" step="0.01" min="0.01" max="'.$maxAmount.'" value="'.$amount.'" />
                <button class="button">' . AMAZON_ADMIN_REFUND_BUTTON . '</button>
              </form>';
    }
    
    echo '</tr>';
    $getorder->MoveNext();
}
?>
</table>

    <br /><a href="<?php echo zen_href_link(FILENAME_ORDERS, 'oID='.$orderId.'&action=edit&amazon_pay_action=refresh', 'SSL'); ?>" class="button main"><?php echo AMAZON_ADMIN_REFRESH_LINK; ?></a>
</div>
<?php
if(!defined('AMAZON_PAY_IS_AJAX')){
    ?>
        <script>
            let lastAmazonPayRefresherResponse = '';
            const amazonPayRefresherFunction = function(){
                const url = '<?php echo zen_href_link(FILENAME_ORDERS, 'oID='.$orderId.'&action=edit&amazon_pay_action=get_admin_html', 'SSL'); ?>',
                xhr = new XMLHttpRequest();
                xhr.open("GET", url);
                xhr.onload = function(xhr){
                    const _doc = new DOMParser().parseFromString(this.response, "text/html")
                    const panel = document.getElementById('amazon-pay-panel');
                    const newPanel = document.createElement('div');
                    const newHtml = _doc.getElementById('amazon-pay-panel').innerHTML;
                    if(newHtml === lastAmazonPayRefresherResponse){
                        return;
                    }
                    lastAmazonPayRefresherResponse = newHtml;
                    newPanel.innerHTML = newHtml;
                    panel.parentElement.replaceChild(newPanel, panel);
                    newPanel.id = 'amazon-pay-panel';
                }
                xhr.send();
            }
            amazonPayRefresherFunction();
            setInterval(amazonPayRefresherFunction, 5000);
        </script>
    <?php
}
