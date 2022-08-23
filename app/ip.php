<?php

error_reporting(0);
include_once("lib/xmlrpc.inc");
include_once("lib/xmlrpcs.inc");
include_once("lib/xmlrpc_wrappers.inc");

require_once("include/Database.class.php");

require_once("include/config.inc.php");
//require_once("include/pgdbi.php");
//require_once("include/GlobalSetting.php");
require_once("include/function.php");
echo "IP Anda: " . getClientIP();//$_SERVER['REMOTE_ADDR'];
?>
