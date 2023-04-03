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
 * @version $Id: address_book.php 2023-03-25 07:15516Z webchills $
 */
 
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
if (!empty($_SESSION['checkout_with_incomplete_account_started'])) {
    if (!$_SESSION['customer_default_address_id']) {
    	  global $db;
        $sql  = "SELECT * FROM " . TABLE_ADDRESS_BOOK . " WHERE customers_id = " . (int)$_SESSION['customer_id'] . " AND entry_street_address != '' AND entry_street_address IS NOT NULL";
        $addressresult = $db->Execute($sql);
       if (!$addressresult->EOF) {
            zen_db_perform(TABLE_CUSTOMERS, ['customers_default_address_id' => (int)$addressresult->fields['address_book_id']], 'update', 'customers_id = ' . (int)$_SESSION['customer_id']);
            $_SESSION['customer_default_address_id'] = (int)$addressresult->fields['address_book_id'];
        }
    }
    unset($_SESSION['checkout_with_incomplete_account_started']);
    zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING_AMAZON, '', 'SSL'));
}
