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
 * @version $Id: checkout_shipping_amazon.php 2023-03-29 19:01:16Z webchills $
 */

define('NAVBAR_TITLE_1','Bestellung');
define('NAVBAR_TITLE_2','Versandart wählen');

define('HEADING_TITLE','Schritt 1 von 3 : Lieferinformationen');

define('TABLE_HEADING_SHIPPING_ADDRESS','Lieferanschrift');
define('TEXT_CHOOSE_SHIPPING_DESTINATION','Ihre Bestellung wird an die links angezeigte Anschrift aus Ihrem Amazon Adressbuch geliefert. Sie können die Lieferanschrift ändern und eine andere Lieferadresse aus Ihrem Amazon Adressbuch wählen, indem Sie auf den Button <em>Adresse ändern</em> klicken.');
define('TITLE_SHIPPING_ADDRESS','Lieferanschrift:');

define('TABLE_HEADING_SHIPPING_METHOD','Versandart');
define('TEXT_CHOOSE_SHIPPING_METHOD','Bitte wählen Sie die Versandart für Ihre Bestellung.');
define('TITLE_PLEASE_SELECT','Bitte wählen Sie');
define('TEXT_ENTER_SHIPPING_INFORMATION','Dies ist zur Zeit die einzige Versandart.');
define('TITLE_NO_SHIPPING_AVAILABLE', 'Zur Zeit nicht verfügbar');
define('TEXT_NO_SHIPPING_AVAILABLE','<span class="alert">Entschuldigung, aber wir können nicht in Ihre Region versenden .</span><br>Bitte setzen Sie sich mit uns in Verbindung, um Alternativen zu suchen.');

define('TABLE_HEADING_COMMENTS','Anmerkungen oder Hinweise');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE','Weiter zu Schritt 2');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE','- wählen Sie Ihre Zahlungsart ...');

// when free shipping for orders over $XX.00 is active
define('FREE_SHIPPING_TITLE', 'Versandkostenfreie Lieferung');
define('FREE_SHIPPING_DESCRIPTION', 'Versandkostenfreie Lieferung für Bestellungen ab %s');
define('ERROR_PLEASE_RESELECT_SHIPPING_METHOD', 'Die verfügbaren Versandarten haben sich geändert. Bitte wählen Sie erneut Ihre gewünschte Versandart aus.');
