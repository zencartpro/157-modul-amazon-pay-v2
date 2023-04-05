# 157-modul-amazon-pay-v2
Amazon Pay V2 für Zen Cart 1.5.7 deutsch 

## Hinweis: 
Freigegebene getestete Versionen für den Einsatz in Livesystemen ausschließlich unter Releases herunterladen:
* https://github.com/zencartpro/157-modul-amazon-pay-v2/releases

## DONATIONWARE:
* Dieses Modul ist DONATIONWARE
* Wenn Sie es in Ihrem Zen Cart Shop verwenden, spenden Sie für die Weiterentwicklung der deutschen Zen Cart Version hier:
* https://spenden.zen-cart-pro.at 

## Sinn und Zweck:
* Mit diesem Modul wird Zahlung via Amazon Pay im Shop integriert.
* Kunden können direkt mit ihrem Amazon Konto einloggen und dann die in ihrem Amazon Account hinterlegte Zahlungsart verwenden.
* Dieses Modul verwendet Amazon Pay Checkout V2 und unterstützt die SCA (Strong Customer Authentication). 
* Es werden keinerlei Daten zu den bestellten Artikeln an Amazon übermittelt, lediglich der Bestellbetrag für die Zahlungsabwicklung

## Voraussetzungen:
* Freigeschalteter Amazon Seller Account in EU oder UK
* Konfiguration der erforderlichen API Keys im Seller Account (siehe Installationsanleitung)
* Zen Cart 1.5.7f deutsche Version
* Shop verwendet durchgehend https
* PHP mindestens 7.4.x, empfohlen 8.0.x

## Features:
* Zahlungen werden sofort autorisiert und eingezogen (capture)
* Zahlungen können bei Bedarf via Shopadministration rückerstattet werden (ähnlich wie bei der PayPal Express Integration)
* komplette Sandbox Unterstützung, so dass alles im Sandbox Modus getestet werden kann
* getrennter Checkout für Amazon Pay und normale Zahlungsarten um möglichst wenig in die bestehende Funktionalität einzugreifen
* Verwendung des aktuellen Amazon Pay API SDK PHP
* Übersicht der Amazon Pay Transaktionen filterbar nach Status in der Zen Cart Administration
* Kennzeichnung in der Kundenübersicht ob ein Kundenkonto normal im Shop oder automatisch via Amazon Pay erstellt wurde
* Unterstützung von Zen Cart Gutscheinguthaben und Aktionskupons Funktionalität
* Unterstützung von Downloadartikeln
* Cronjob zum regelmäßigen Abgleich der Transaktionsstati in der Shopdatenbank mit Amazon möglich

## Credits:
* Dieses Modul basiert auf dem Amazon Pay Modul für modified von AlkimMedia (www.alkim.de)
* Grundlage war das entsprechende Github Repository:
* https://github.com/AlkimMedia/AmazonPay_Modified_2060

## Installation und Konfiguration
Umfangreiche Anleitung und Dokumentation auf:
* https://amazonpayv2.zen-cart-pro.at
