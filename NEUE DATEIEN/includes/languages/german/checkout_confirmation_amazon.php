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
 * @version $Id: checkout_confirmation_amazon.php 2023-03-31 15:36:16Z webchills $
 */

define('NAVBAR_TITLE_1','Bestellung');
define('NAVBAR_TITLE_2','Bestellung bestätigen');

define('HEADING_TITLE','Schritt 3 von 3: Zahlungspflichtig bestellen');
define('TEXT_ZUSATZ_SCHRITT3','Überprüfen Sie Ihre Bestellung und drücken dann den Button "KAUFEN" unten auf dieser Seite.<br>Sie werden dann nochmals auf die Amazon Pay Seite geleitet, wo Sie Ihre Zahlung autorisieren und bestätigen.');
define('HEADING_BILLING_ADDRESS','Rechnungsanschrift');
define('HEADING_DELIVERY_ADDRESS','Lieferanschrift');
define('HEADING_SHIPPING_METHOD','Versandart:');
define('HEADING_PAYMENT_METHOD','Zahlungsart:');
define('HEADING_PRODUCTS','Warenkorbinhalt');
define('HEADING_TAX','MwSt.');
define('HEADING_ORDER_COMMENTS','Anmerkungen oder Hinweise');
// no comments entered
define('NO_COMMENTS_TEXT','Keine');

// buttonloesung
define('TABLE_HEADING_SINGLEPRICE','Einzelpreis');
define('TABLE_HEADING_PRODUCTIMAGE','Artikelbild');
define('TEXT_CONDITIONS_ACCEPTED_IN_LAST_STEP','Ich habe <a href="' . zen_href_link(FILENAME_CONDITIONS, '', 'SSL') . '" target="_blank"><u>AGB</u></a> und <a href="' . zen_href_link(FILENAME_WIDERRUFSRECHT, '', 'SSL') . '"><u>Widerrufsrecht</u></a> gelesen und akzeptiert.');
define('TEXT_NON_EU_COUNTRIES','Hinweis:<br>Ihre Bestellung wird in ein Nicht-EU-Land geliefert. Zusätzlich können im Rahmen Ihrer Bestellung noch weitere Zölle, Steuern oder Kosten anfallen, die nicht über uns abgeführt bzw. von uns in Rechnung gestellt werden.');