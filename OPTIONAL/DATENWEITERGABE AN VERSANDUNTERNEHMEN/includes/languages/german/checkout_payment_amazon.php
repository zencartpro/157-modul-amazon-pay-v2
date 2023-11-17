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
 * @version $Id: checkout_paymemt_amazon.php for Datenweitergabe 2023-11-17 16:36:16Z webchills $
 */

define('NAVBAR_TITLE_1', 'Bestellung - Schritt 2');
define('NAVBAR_TITLE_2', ' Schritt 2 - Zahlungsinformationen');

define('HEADING_TITLE', 'Schritt 2 von 3 : Zahlungsinformationen');

define('TABLE_HEADING_COMMENTS', 'Anmerkungen oder Hinweise');

define('TEXT_NO_PAYMENT_OPTIONS_AVAILABLE','<span class="alert">Entschuldigung, aber wir können Zahlungen aus Ihrer Region nicht annehmen .</span><br>Bitte setzen Sie sich mit uns in Verbindung, um Alternativen zu suchen. ');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', '<strong>Weiter zu Schritt 3</strong>');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', '- um Ihre Bestellung fortzuführen ...');

define('TABLE_HEADING_CONDITIONS', '<span class="termsconditions">Allgemeine Geschäftsbedingungen</span>');
define('TEXT_CONDITIONS_DESCRIPTION', '<span class="termsdescription">Bitte bestätigen Sie unsere Allgemeinen Geschäftsbedingungen durch Anklicken der Checkbox. Unsere AGB können Sie <a href="' . zen_href_link(FILENAME_CONDITIONS, '', 'SSL') . '" rel="noopener" target="_blank"><span class="pseudolink">hier</span></a> nachlesen.</span>');
define('TEXT_CONDITIONS_CONFIRM', '<span class="termsiagree">Ich habe die AGB gelesen und akzeptiert. Den Hinweis zu meinem <a href="' . zen_href_link(FILENAME_WIDERRUFSRECHT, '', 'SSL') . '" rel="noopener" target="_blank"><span class="pseudolink">Widerrufsrecht</span></a> habe ich verstanden.</span>');

define('TEXT_YOUR_TOTAL', 'Gesamtsumme');

define('TEXT_INFO_PAYMENT_AMAZON', 'Sie zahlen mit:');
define('TABLE_HEADING_CARRIER', '<span class="termsconditions">Emailadresse/Telefonnummer für Lieferabstimmung</span>');
define('TEXT_CARRIER_DESCRIPTION', '<span class="termsdescription">Ich bin damit einverstanden, dass meine E-Mail-Adresse bzw. meine Telefonnummer an Deutsche Post AG, Charles-de-Gaulle-Straße 20, 53113 Bonn weitergegeben wird, damit der Paketdienstleister vor der Zustellung der Ware zum Zwecke der Abstimmung eines Liefertermins per E-Mail oder Telefon Kontakt mit mir aufnehmen bzw. Statusinformationen zur Sendungszustellung übermitteln kann. Meine diesbezüglich erteilte Einwilligung kann ich jederzeit widerrufen.</span>');
define('TEXT_CARRIER_YES', '<span class="termsiagree">Ich bin einverstanden.</span>');
define('TEXT_CARRIER_NO', '<span class="termsiagree">Ich bin nicht einverstanden.</span>');