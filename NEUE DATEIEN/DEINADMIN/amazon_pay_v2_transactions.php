<?php
/**
 * Amazon Pay V2 for Zen Cart German 1.5.7
 * Copyright 2023-2024 webchills (www.webchills.at)
 * based on Amazon Pay for Modified by AlkimMedia (www.alkim.de)
 * (https://github.com/AlkimMedia/AmazonPay_Modified_2060)
 * Portions Copyright 2003-2024 Zen Cart Development Team
 * Zen Cart German Version - www.zen-cart-pro.at
 * @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
 * Dieses Modul ist DONATIONWARE
 * Wenn Sie es in Ihrem Zen Cart Shop einsetzen, spenden Sie für die Weiterentwicklung der deutschen Zen Cart Version auf
 * https://spenden.zen-cart-pro.at
 * @version $Id: amazon_pay_v2_transactions.php 2024-04-04 20:20:16Z webchills $
 */

  require('includes/application_top.php');
  
  $amazon_sort_order_array = [
    ['id' => '0', 'text' => TEXT_SORT_AMAZON_ID_DESC],
    ['id' => '1', 'text' => TEXT_SORT_AMAZON_ID],
    ['id' => '2', 'text' => TEXT_SORT_ZEN_ORDER_ID_DESC],
    ['id' => '3', 'text' => TEXT_SORT_ZEN_ORDER_ID],
    ['id' => '4', 'text' => TEXT_SORT_AMAZON_ENVIRONMENT_DESC],
    ['id' => '5', 'text' => TEXT_SORT_AMAZON_ENVIRONMENT],
    ['id' => '6', 'text' => TEXT_SORT_AMAZON_STATUS_DESC],
    ['id' => '7', 'text' => TEXT_SORT_AMAZON_STATUS]
  ];

  $amazon_sort_order = 0;
  if (isset($_GET['amazon_sort_order'])) {
    $amazon_sort_order = (int) $_GET['amazon_sort_order'];
  }

  switch ($amazon_sort_order) {
    case (0):
      $order_by = " order by a.id DESC";
      break;
    case (1):
      $order_by = " order by a.order_id";
      break;
    case (2):
      $order_by = " order by a.order_id DESC, a.id";
      break;
    case (3):
      $order_by = " order by a.order_id, a.id";
      break;
    case (4):
      $order_by = " order by a.mode DESC";
      break;
    case (5):
      $order_by = " order by a.mode";
      break;
      case (6):
      $order_by = " order by a.status";
      break;
      case (7):
      $order_by = " order by a.status DESC";
      break;
      
    default:
      $order_by = " order by a.id DESC";
      break;
    }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  $selected_status = (isset($_GET['amazon_status']) ? $_GET['amazon_status'] : '');    
  $amazon_statuses = [];
  $amazon_statuses[0] = array('id' => 'Captured', 'text' => 'Captured'); 
  $amazon_statuses[1] = array('id' => 'Completed', 'text' => 'Completed');
  $amazon_statuses[2]= array('id' => 'NonChargeable', 'text' => 'NonChargeable');
  $amazon_statuses[3]= array('id' => 'RefundInitiated', 'text' => 'RefundInitiated');
  $amazon_statuses[4]= array('id' => 'Refunded', 'text' => 'Refunded');
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
<head>
    <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
    <style>

</style>

</head>
<body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <div class="container-fluid">
    <h1><?php echo HEADING_ADMIN_TITLE; ?></h1>
    <!-- only show if the Amazon Pay V2 module is installed //-->
<?php  if (defined('MODULE_PAYMENT_AMAZON_PAY_V2_STATUS')) { ?>
<span id="amazonsorter"><?php
  $hidden_field = (isset($_GET['amazon_sort_order'])) ? zen_draw_hidden_field('amazon_sort_order', $_GET['amazon_sort_order']) : '';
  echo zen_draw_form('amazon_status', FILENAME_AMAZON_PAY_V2_TRANSACTIONS, '', 'get') . HEADING_AMAZON_STATUS . ' ' . zen_draw_pull_down_menu('amazon_status', array_merge([['id' => '', 'text' => TEXT_ALL_IPNS]], $amazon_statuses), $selected_status, 'onchange="this.form.submit();"') . zen_hide_session_id() . $hidden_field . '</form>';
  $hidden_field = (isset($_GET['amazon_status'])) ? zen_draw_hidden_field('amazon_status', $_GET['amazon_status']) : '';
  echo '&nbsp;&nbsp;&nbsp;' . TEXT_AMAZON_SORT_ORDER_INFO . zen_draw_form('amazon_sort_order', FILENAME_AMAZON_PAY_V2_TRANSACTIONS, '', 'get') . '&nbsp;&nbsp;' . zen_draw_pull_down_menu('amazon_sort_order', $amazon_sort_order_array, $amazon_sort_order, 'onChange="this.form.submit();"') . zen_hide_session_id() . $hidden_field . '</form>';
?></span>
    <span class="supportinfo">Amazon Pay Merchant ID: <?php echo AMAZON_PAY_V2_MERCHANT_ID; ?> | <a href="https://sellercentral-europe.amazon.com/home" target="_blank">Amazon Pay Seller Central Login</a></span>

       <div class="row">
           <div class="col-sm-12 col-md-9 configurationColumnLeft">
              <table class="table">
              <tr class="dataTableHeadingRow">
              	<td class="dataTableHeadingContent">ID</td>
              	 <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDER_NUMBER; ?></td>              	
                <td class="dataTableHeadingContent"><?php echo AMAZON_REFERENCE_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ENVIRONMENT; ?></td> 
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TYPE; ?></td> 
                
                 <td class="dataTableHeadingContent"><?php echo AMAZON_DATE; ?></td>     
               <td class="dataTableHeadingContent" text-right><?php echo TABLE_HEADING_CHARGE_AMOUNT; ?></td>
               <td class="dataTableHeadingContent" text-right><?php echo TABLE_HEADING_CAPTURED_AMOUNT; ?></td>
               <td class="dataTableHeadingContent" text-right><?php echo TABLE_HEADING_REFUNDED_AMOUNT; ?></td>
                 <td class="dataTableHeadingContent">Status</td>         
                       
                              
              </tr>
<?php
  if (!empty($selected_status)) {
    $amazon_search = "AND a.status  = :selectedStatus: ";
    $amazon_search = $db->bindVars($amazon_search, ':selectedStatus:', $selected_status, 'string');
    switch ($selected_status) {
      case 'Captured':
      case 'Refunded': 
      case 'RefundInitiated':  
      case 'Completed': 
      case 'NonChargeable':     
        default:
        $amazon_query_raw = "SELECT a.id, a.order_id, a.reference, a.mode, a.type, a.time, a.charge_amount, a.captured_amount, a.refunded_amount, a.status from `".TABLE_AMAZON_PAY_V2_TRANSACTIONS."` a, " .TABLE_ORDERS . " o  where o.orders_id = a.order_id " . $amazon_search . $order_by;
        break;
   } 
  } else {
 $amazon_query_raw = "SELECT a.id, a.order_id, a.reference, a.mode, a.type, a.time, a.charge_amount, a.captured_amount, a.refunded_amount, a.status from ".TABLE_AMAZON_PAY_V2_TRANSACTIONS." a LEFT JOIN " .TABLE_ORDERS . " o on o.orders_id = a.order_id " . $order_by;

  }

  $amazon_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_AMAZON_IPN, $amazon_query_raw, $amazon_query_numrows);
  $amazon_response = $db->Execute($amazon_query_raw);
  foreach ($amazon_response as $amazon_tran) {
    if ((!isset($_GET['amazonId']) || (isset($_GET['amazonId']) && ($_GET['amazonId'] == $amazon_tran['id']))) && !isset($amazonInfo) ) {
      $amazonInfo = new objectInfo($amazon_tran); 
    }   
    
      echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_AMAZON_PAY_V2_TRANSACTIONS, 'page=' . $_GET['page'] . '&amazonId=' . $amazon_tran['id'] . (zen_not_null($selected_status) ? '&status=' . $selected_status : '') . (zen_not_null($amazon_sort_order) ? '&amazon_sort_order=' . $amazon_sort_order : '') ) . '\'">' . "\n";
   
?>
                <td class="dataTableContent"> <?php echo $amazon_tran['id']; ?> </td>
                <td class="dataTableContent"> <?php echo $amazon_tran['order_id']; ?> </td>
                <td class="dataTableContent"> <?php echo $amazon_tran['reference']; ?> </td>
		            <td class="dataTableContent"> <?php echo $amazon_tran['mode']; ?> </td>
                <td class="dataTableContent"> <?php echo $amazon_tran['type']; ?> </td>
                <td class="dataTableContent"> <?php echo $amazon_tran['time']; ?> </td>
                <td class="dataTableContent"> <?php echo $amazon_tran['charge_amount']; ?>
                <td class="dataTableContent"> <?php echo $amazon_tran['captured_amount']; ?>
                <td class="dataTableContent"> <?php echo $amazon_tran['refunded_amount']; ?>
                <td class="dataTableContent"> <?php echo $amazon_tran['status']; ?>
                
                	
              <?php echo '</tr>';
  }
?>
              <tr>
                    <td colspan="3" class="smallText"><?php echo $amazon_split->display_count($amazon_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_AMAZON_IPN, $_GET['page'], "Zeige <strong>%d</strong> bis <strong>%d</strong> (von <strong>%d</strong> Transaktionen)"); ?></td>
                    <td colspan="3" class="smallText"><?php echo $amazon_split->display_links($amazon_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_AMAZON_IPN, MAX_DISPLAY_PAGE_LINKS, isset($_GET['page']) ? (int)$_GET['page'] : 1, zen_get_all_get_params(['page'])); ?></td>
                  </tr>
                </table>
           </div>
<?php
  $heading = [];
  $contents = [];

  switch ($action) {
    case 'new':
      break;
    case 'edit':
      break;
    case 'delete':
      break;
    default:
      
      if (isset($amazonInfo) && is_object($amazonInfo)) {
        $heading[] = ['text' => '<strong>' . 'Amazon'.' #' . $amazonInfo->id . '</strong>'];
        $amazon = $db->Execute("SELECT * FROM " . TABLE_AMAZON_PAY_V2_TRANSACTIONS . " WHERE id = '" . $amazonInfo->id . "'");
        $amazon_count = $amazon->RecordCount();  
	  

      switch ($amazon->fields['status']){
      	case 'Captured':

		require_once(DIR_WS_CLASSES . 'order.php');

		$order = new order($amazonInfo->order_id);
        $heading[] = array('text' => '<strong>' . TEXT_INFO_AMAZON_RESPONSE_BEGIN.'#'.$amazonInfo->id.' '.TEXT_INFO_AMAZON_RESPONSE_END.'#'. $amazonInfo->order_id . '</strong>');
        $contents[] = array('text' =>  '' . AMAZON_DATE .'' . ': '. zen_datetime_short($amazonInfo->time));
        $contents[] = array('text' =>  '' . AMAZON_REFERENCE_ID .'' . ': '.$amazonInfo->reference);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ORDER_NUMBER .'' . ': '.$amazonInfo->order_id);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ENVIRONMENT .'' . ': '.$amazonInfo->mode);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CHARGE_AMOUNT .'' . ': '.$amazonInfo->charge_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CAPTURED_AMOUNT .'' . ': '.$amazonInfo->captured_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_REFUNDED_AMOUNT .'' . ': '.$amazonInfo->refunde_amount);
        $contents[] = array('text' =>  'Status' . ': '.$amazonInfo->status);
	      $contents[] = array('text' =>  '' . TABLE_HEADING_TYPE .'' . ': '.$amazonInfo->type);	    
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('amazonId', 'action')) . 'oID=' . $amazonInfo->order_id .'&' . 'amazonID=' . $amazonInfo->id .'&action=edit' . '&referer=amazon') . '">' . AMAZON_VIEW_ORDER. '</a>');
        $count = 1;
		  
			$contents[] = array('text' =>  '</table>');
		break;
		      	case 'Refunded':

		require_once(DIR_WS_CLASSES . 'order.php');

		$order = new order($amazonInfo->order_id);
        $heading[] = array('text' => '<strong>' . TEXT_INFO_AMAZON_RESPONSE_BEGIN.'#'.$amazonInfo->amazon_id.', '.TEXT_INFO_AMAZON_RESPONSE_END.'#'. $amazonInfo->order_id . '</strong>');
        $contents[] = array('text' =>  '' . AMAZON_DATE .'' . ': '. zen_datetime_short($amazonInfo->time));
        $contents[] = array('text' =>  '' . AMAZON_REFERENCE_ID .'' . ': '.$amazonInfo->reference);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ORDER_NUMBER .'' . ': '.$amazonInfo->order_id);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ENVIRONMENT .'' . ': '.$amazonInfo->mode);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CHARGE_AMOUNT .'' . ': '.$amazonInfo->charge_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CAPTURED_AMOUNT .'' . ': '.$amazonInfo->captured_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_REFUNDED_AMOUNT .'' . ': '.$amazonInfo->refunde_amount);
        $contents[] = array('text' =>  'Status' . ': '.$amazonInfo->status);
	      $contents[] = array('text' =>  '' . TABLE_HEADING_TYPE .'' . ': '.$amazonInfo->type);	    
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('amazonId', 'action')) . 'oID=' . $amazonInfo->order_id .'&' . 'amazonID=' . $amazonInfo->id .'&action=edit' . '&referer=amazon') . '">' . AMAZON_VIEW_ORDER. '</a>');
        $count = 1;
		  
			$contents[] = array('text' =>  '</table>');
		break;
		
		case 'Completed':

		require_once(DIR_WS_CLASSES . 'order.php');

		$order = new order($amazonInfo->order_id);
        $heading[] = array('text' => '<strong>' . TEXT_INFO_AMAZON_RESPONSE_BEGIN.'#'.$amazonInfo->amazon_id.', '.TEXT_INFO_AMAZON_RESPONSE_END.'#'. $amazonInfo->order_id . '</strong>');
        $contents[] = array('text' =>  '' . AMAZON_DATE .'' . ': '. zen_datetime_short($amazonInfo->time));
        $contents[] = array('text' =>  '' . AMAZON_REFERENCE_ID .'' . ': '.$amazonInfo->reference);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ORDER_NUMBER .'' . ': '.$amazonInfo->order_id);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ENVIRONMENT .'' . ': '.$amazonInfo->mode);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CHARGE_AMOUNT .'' . ': '.$amazonInfo->charge_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CAPTURED_AMOUNT .'' . ': '.$amazonInfo->captured_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_REFUNDED_AMOUNT .'' . ': '.$amazonInfo->refunde_amount);
        $contents[] = array('text' =>  'Status' . ': '.$amazonInfo->status);
	      $contents[] = array('text' =>  '' . TABLE_HEADING_TYPE .'' . ': '.$amazonInfo->type);	    
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('amazonId', 'action')) . 'oID=' . $amazonInfo->order_id .'&' . 'amazonID=' . $amazonInfo->id .'&action=edit' . '&referer=amazon') . '">' . AMAZON_VIEW_ORDER. '</a>');
        $count = 1;
		  
			$contents[] = array('text' =>  '</table>');
		break;
		
		case 'RefundInitiated':

		require_once(DIR_WS_CLASSES . 'order.php');

		$order = new order($amazonInfo->order_id);
        $heading[] = array('text' => '<strong>' . TEXT_INFO_AMAZON_RESPONSE_BEGIN.'#'.$amazonInfo->amazon_id.', '.TEXT_INFO_AMAZON_RESPONSE_END.'#'. $amazonInfo->order_id . '</strong>');
        $contents[] = array('text' =>  '' . AMAZON_DATE .'' . ': '. zen_datetime_short($amazonInfo->time));
        $contents[] = array('text' =>  '' . AMAZON_REFERENCE_ID .'' . ': '.$amazonInfo->reference);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ORDER_NUMBER .'' . ': '.$amazonInfo->order_id);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ENVIRONMENT .'' . ': '.$amazonInfo->mode);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CHARGE_AMOUNT .'' . ': '.$amazonInfo->charge_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CAPTURED_AMOUNT .'' . ': '.$amazonInfo->captured_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_REFUNDED_AMOUNT .'' . ': '.$amazonInfo->refunde_amount);
        $contents[] = array('text' =>  'Status' . ': '.$amazonInfo->status);
	      $contents[] = array('text' =>  '' . TABLE_HEADING_TYPE .'' . ': '.$amazonInfo->type);	    
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('amazonId', 'action')) . 'oID=' . $amazonInfo->order_id .'&' . 'amazonID=' . $amazonInfo->id .'&action=edit' . '&referer=amazon') . '">' . AMAZON_VIEW_ORDER. '</a>');
        $count = 1;
		  
			$contents[] = array('text' =>  '</table>');
		break;
		case 'NonChargeable':
  require_once(DIR_WS_CLASSES . 'order.php');

		$order = new order($amazonInfo->order_id);
        $heading[] = array('text' => '<strong>' . TEXT_INFO_AMAZON_RESPONSE_BEGIN.'#'.$amazonInfo->amazon_id.', '.TEXT_INFO_AMAZON_RESPONSE_END.'#'. $amazonInfo->order_id . '</strong>');
        $contents[] = array('text' =>  '' . AMAZON_DATE .'' . ': '. zen_datetime_short($amazonInfo->time));
        $contents[] = array('text' =>  '' . AMAZON_REFERENCE_ID .'' . ': '.$amazonInfo->reference);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ORDER_NUMBER .'' . ': '.$amazonInfo->order_id);
        $contents[] = array('text' =>  '' . TABLE_HEADING_ENVIRONMENT .'' . ': '.$amazonInfo->mode);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CHARGE_AMOUNT .'' . ': '.$amazonInfo->charge_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_CAPTURED_AMOUNT .'' . ': '.$amazonInfo->captured_amount);
        $contents[] = array('text' =>  '' . TABLE_HEADING_REFUNDED_AMOUNT .'' . ': '.$amazonInfo->refunde_amount);
        $contents[] = array('text' =>  'Status' . ': '.$amazonInfo->status);
	      $contents[] = array('text' =>  '' . TABLE_HEADING_TYPE .'' . ': '.$amazonInfo->type);	    
        
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_ORDERS, zen_get_all_get_params(array('amazonId', 'action')) . 'oID=' . $amazonInfo->order_id .'&' . 'amazonID=' . $amazonInfo->id .'&action=edit' . '&referer=amazon') . '">' . AMAZON_VIEW_ORDER. '</a>');
        $count = 1;
		  
			$contents[] = array('text' =>  '</table>');
		break;
		default:
        $heading[] = array('text' => '');
        $contents[] = array('text'=> '' );
        }
      }
      break;
  }
  if (!empty($heading) && !empty($contents)) {
    $box = new box();
      echo '<div class="col-sm-12 col-md-3 configurationColumnRight">';
    echo $box->infoBox($heading, $contents);
      echo '</div>';
  }
?>
       </div>
<?php } ?>
</div>
<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
</body>
</html>
<?php require DIR_WS_INCLUDES . 'application_bottom.php'; ?>