<?php
echo "tes koneksi";
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$__CFG_dbhost_D = "10.0.0.20";
$__CFG_dbuser_D = "fmss";
$__CFG_dbpass_D = "rahasia";
$__CFG_dbname_D = "fmss";
$__CFG_dbport_D = "5432";
$__G_conn_devel="host=" . $__CFG_dbhost_D . " port=" . $__CFG_dbport_D . " dbname=" . $__CFG_dbname_D . " user=" . $__CFG_dbuser_D . " password=" . $__CFG_dbpass_D;

$dbconn = pg_connect($__G_conn_devel);  

  // Query that fails
$res = pg_query($dbconn, "select * from transaksi");
  
echo pg_last_error($dbconn);

?>