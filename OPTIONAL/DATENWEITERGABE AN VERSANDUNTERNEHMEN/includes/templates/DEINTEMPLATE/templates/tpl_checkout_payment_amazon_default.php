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
 * @version $Id: tpl_checkout_payment_amazon_default.php for Datenweitergabe an Transportunternehmen 2023-04-02 19:15:16Z webchills $
 */

?>
<?php echo $payment_modules->javascript_validation(); ?>
<div class="centerColumn" id="checkoutPayment">
<?php echo zen_draw_form('checkout_payment', zen_href_link(FILENAME_CHECKOUT_CONFIRMATION_AMAZON, '', 'SSL'), 'post'); ?>
<?php echo zen_draw_hidden_field('action', 'submit'); ?>

<h1 id="checkoutPaymentHeading"><?php echo HEADING_TITLE; ?></h1>


<?php if ($messageStack->size('redemptions') > 0) echo $messageStack->output('redemptions'); ?>
<?php if ($messageStack->size('checkout') > 0) echo $messageStack->output('checkout'); ?>
<?php if ($messageStack->size('checkout_payment') > 0) echo $messageStack->output('checkout_payment'); ?>

<div align="center"><p><?php echo TEXT_INFO_PAYMENT_AMAZON; ?></p><p><img style="max-width: 160px; width:100%; vertical-align:middle;" src="images/icons/amazon-pay-icon.svg" alt="Amazon Pay"></p></div>
<input type="hidden" name="payment" value="<?php echo $_SESSION['payment']; ?>">

<fieldset id="checkoutOrderTotals">
<legend id="checkoutPaymentHeadingTotal"><?php echo TEXT_YOUR_TOTAL; ?></legend>
<?php
  if (MODULE_ORDER_TOTAL_INSTALLED) {
    $order_totals = $order_total_modules->process();
?>
<?php $order_total_modules->output(); ?>
<?php
  }
?>
</fieldset>

<?php
  $selection =  $order_total_modules->credit_selection();
  if (sizeof($selection)>0) {
    for ($i=0, $n=sizeof($selection); $i<$n; $i++) {
      if (isset($_GET['credit_class_error_code']) && ($_GET['credit_class_error_code'] == (isset($selection[$i]['id'])) ? $selection[$i]['id'] : 0)) {
?>
<div class="messageStackError"><?php echo zen_output_string_protected($_GET['credit_class_error']); ?></div>

<?php
      }
      for ($j=0, $n2=(isset($selection[$i]['fields']) ? sizeof($selection[$i]['fields']) : 0); $j<$n2; $j++) {
?>
<fieldset>
<legend><?php echo $selection[$i]['module']; ?></legend>
<?php echo $selection[$i]['redeem_instructions']; ?>
<div class="gvBal larger"><?php echo (isset($selection[$i]['checkbox'])) ? $selection[$i]['checkbox'] : ''; ?></div>
<label class="inputLabel"<?php echo ($selection[$i]['fields'][$j]['tag']) ? ' for="'.$selection[$i]['fields'][$j]['tag'].'"': ''; ?>><?php echo $selection[$i]['fields'][$j]['title']; ?></label>
<?php echo $selection[$i]['fields'][$j]['field']; ?>
</fieldset>
<?php
      }
    }
?>

<?php
    }
?>




<fieldset>
<legend><?php echo TABLE_HEADING_COMMENTS; ?></legend>
<?php echo zen_draw_textarea_field('comments', '45', '3', (isset($comments) ? $comments : ''), 'aria-label="' . TABLE_HEADING_COMMENTS . '"'); ?>
</fieldset>


<?php
  if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
?>
<fieldset>
<legend><?php echo TABLE_HEADING_CONDITIONS; ?></legend>
<div><?php echo TEXT_CONDITIONS_DESCRIPTION;?></div>
<?php echo  zen_draw_checkbox_field('conditions', '1', false, 'id="conditions"');?>
<label class="checkboxLabel" for="conditions"><?php echo TEXT_CONDITIONS_CONFIRM; ?></label>
</fieldset>
<?php
  }
?>

<?php if ((isset($_SESSION['shipping']['id']) && $_SESSION['cart']->get_content_type() == 'virtual') || (bool)preg_match('#storepickup#i', $_SESSION['shipping']['id'])) { ?>      
<?php  } else { ?>
<fieldset>
<legend><?php echo TABLE_HEADING_CARRIER; ?></legend>
<div><?php echo TEXT_CARRIER_DESCRIPTION;?></div>
<?php echo  zen_draw_radio_field('carrier', 'ja', false, 'id="carrierja"');?>
<label class="checkboxLabel" for="carrier"><?php echo TEXT_CARRIER_YES; ?></label>
<?php echo  zen_draw_radio_field('carrier', 'nein', false, 'id="carriernein"');?>
<label class="checkboxLabel" for="carrier"><?php echo TEXT_CARRIER_NO; ?></label>
</fieldset>
<?php
  }
?>
<div class="buttonRow center" id="paymentSubmit"><?php echo zen_image_submit(BUTTON_IMAGE_CONTINUE_CHECKOUT, BUTTON_CONTINUE_ALT, 'onclick="submitFunction('.zen_user_has_gv_account($_SESSION['customer_id']).','.$order->info['total'].')"'); ?></div>

</form>
</div>
