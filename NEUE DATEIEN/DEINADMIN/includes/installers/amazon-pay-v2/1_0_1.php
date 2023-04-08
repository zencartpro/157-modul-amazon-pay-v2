<?php
$db->Execute("UPDATE " . TABLE_CONFIGURATION . " SET configuration_value = '1.0.1' WHERE configuration_key = 'AMAZON_PAY_V2_MODUL_VERSION' LIMIT 1;");