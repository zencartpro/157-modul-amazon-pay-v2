########################################################################################################################
# Amazon Pay V2 UNINSTALL - 2023-04-04 - webchills
# NUR AUSFÜHREN FALLS SIE DAS MODUL VOLLSTÄNDIG ENTFERNEN WOLLEN!!!
# Sie entfernen damit auch die Kennzeichnungen bestehender Amazon Pay V2 Kunden und die Amazon Pay Transaktionstabellen!
########################################################################################################################

DELETE FROM configuration_group WHERE configuration_group_title = 'Amazon Pay V2' LIMIT 1;
DELETE FROM configuration WHERE configuration_key LIKE 'AMAZON_PAY_V2_%';
DELETE FROM configuration_language WHERE configuration_key LIKE 'AMAZON_PAY_V2_%';
DELETE FROM admin_pages WHERE page_key IN ('configAmazonPayV2');
DELETE FROM admin_pages WHERE page_key IN ('customersAmazonPayV2');
##############################
# delete amazon pay v2 field in customer table
# Kommentieren Sie die nächste Zeile mit einer Raute aus, falls Sie die Amazon Pay V2 Kennung NICHT löschen wollen
##############################
ALTER TABLE customers DROP customers_amazonpay_ec;
##############################
# delete amazon pay v2 tables
# Kommentieren Sie die nächste beiden Zeile mit einer Raute aus, falls Sie die beiden Amazon Pay V2 Datenbanktabellen NICHT löschen wollen
##############################
DROP TABLE IF EXISTS amazon_pay_v2_transactions;   
DROP TABLE IF EXISTS amazon_pay_v2_logs;  