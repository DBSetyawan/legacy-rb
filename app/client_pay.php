<?php
include("lib/xmlrpc.inc");
	
// Play nice to PHP 5 installations with REGISTER_LONG_ARRAYS off
/*if(!isset($HTTP_POST_VARS) && isset($_POST)){
	$HTTP_POST_VARS = $_POST;
}/
*/
/*
$f = new xmlrpcmsg("fastpay.tiket", 
	array(
		php_xmlrpc_encode("TIKET"),				//KODE PRODUK
		php_xmlrpc_encode("BNI"),				//BANK
		php_xmlrpc_encode("1"),			//NOMINAL
		php_xmlrpc_encode("BS0003"),			//IDOUTLET
		php_xmlrpc_encode("131313")				//PIN
		)
	);
*/	
/*
$f = new xmlrpcmsg("fastpay.balance", 
	array(
		php_xmlrpc_encode("BS0003"),					//ID OUTLET
		php_xmlrpc_encode("131313"),						//PIN
		)
	);
*/
/*$f = new xmlrpcmsg("fastpay.daftar", 
	array(
		php_xmlrpc_encode("DAFTAR"),				//KODE PRODUK
		php_xmlrpc_encode("08463396898"),			//NOHP
		php_xmlrpc_encode("TOM CRUISE"),			//NAMA
		php_xmlrpc_encode("Di pikiranmu"),			//ALAMAT
		php_xmlrpc_encode("malang"),				//KOTA
		php_xmlrpc_encode("65139"),					//KODEPOS
		php_xmlrpc_encode("0"),						//TIPE LOKET
		php_xmlrpc_encode("GENNARO"),				//IDOUTLET
		php_xmlrpc_encode("333333")					//PIN
		)
	);*/
$noref = array();
for($i=0;$i<8;$i++){
	$noref[$i] = mt_rand(0,9);
}
$reff = implode("",$noref);

$f = new xmlrpcmsg("rajabiller.pay", 
	array(
		php_xmlrpc_encode("PLNPASC"),				//KODE PRODUK
		php_xmlrpc_encode("41111222333"),			//ID PELANGGAN 1
		php_xmlrpc_encode(""),			//ID PELANGGAN 2
		php_xmlrpc_encode(""),			//ID PELANGGAN 3
		php_xmlrpc_encode("419907"),			//NOMINAL
		php_xmlrpc_encode("BS0003"),					//ID OUTLET
		php_xmlrpc_encode("131313"),						//PIN
		php_xmlrpc_encode($reff),						//REF 1
		php_xmlrpc_encode("13899245"),						//REF 2
		php_xmlrpc_encode("")						//REF 3
		)
	);

/*
$f = new xmlrpcmsg("fastpay.cu", 
	array(
		php_xmlrpc_encode("BS0003"),					//ID OUTLET
		php_xmlrpc_encode("131313"),						//PIN
		php_xmlrpc_encode("0"),						//REF 1
		php_xmlrpc_encode("111111")						//REF 2
		
		)
	);
*/
/*
$f = new xmlrpcmsg("rajabiller.pulsa", 
	array(
		php_xmlrpc_encode("I10"),				//KODE PRODUK
		php_xmlrpc_encode("08573396898"),			//NO HP
		php_xmlrpc_encode("BS0003"),					//ID OUTLET
		php_xmlrpc_encode("131313"),						//PIN
		php_xmlrpc_encode($reff)						//REF 1
		)
	);
*/

/*
$f = new xmlrpcmsg("fastpay.datatransaksi", 
	array(
		php_xmlrpc_encode("20130601000000"),				//TGL1
		php_xmlrpc_encode("20130902230000"),			//TGL2
		php_xmlrpc_encode(""),					//IDTRX
		php_xmlrpc_encode("S25"),					//IDPRODUK
		php_xmlrpc_encode("081216880033"),						//IDPELANGGAN
		php_xmlrpc_encode(""),						//LIMIT
		php_xmlrpc_encode("FA28640"),						//IDOUTLET
		php_xmlrpc_encode("313621")						//PIN
		)
	);
*/
/*
$f = new xmlrpcmsg("fastkai.get_org", 
	array(
		php_xmlrpc_encode("BS0003"),				//PIN
		php_xmlrpc_encode("333222"),				//PIN
		php_xmlrpc_encode("123456")				//PIN
		)
	);
*/
/*
$f = new xmlrpcmsg("fastkai.get_schedule", 
	array(
		php_xmlrpc_encode("HH1234"),				//PIN
		php_xmlrpc_encode("123456"),				//PIN
		php_xmlrpc_encode("BD"),				//PIN
		php_xmlrpc_encode("JAKK"),				//PIN
		php_xmlrpc_encode("20130315"),				//PIN
		php_xmlrpc_encode("213123"),				//PIN
		)
	);
*/
//print "<pre>Sending the following request:\n\n" . htmlentities($f->serialize()) . "\n\nDebug info of server data follows...\n\n";
//$c=new xmlrpc_client($serper, "localhost", 7777);
//echo "<br>".$f."<br>";
//$c = new xmlrpc_client("http://localhost/partner-vpn/simulator_dutapulsa/");
//$c = new xmlrpc_client("http://10.10.1.10:8353/partner-vpn/devel/index2.php");
//$c = new xmlrpc_client("http://10.0.0.22:8353/partner-vpn/ata/");
//$c = new xmlrpc_client("http://10.0.0.22:8353/partner-vpn/apex/index2.php");
//$c = new xmlrpc_client("http://localhost/partner-vpn/datindo_new/");
$c = new xmlrpc_client("http://localhost/Rajabiller/rajabiller/");
//$c = new xmlrpc_client("http://175.103.35.106:8353/xl/");
//$c = new xmlrpc_client("http://10.0.0.10:8353/partner-vpn/devel/");
//$c = new xmlrpc_client("http://175.103.35.106:8353/partner-vpn/devel/index2.php");
//$c = new xmlrpc_client("http://10.10.1.10:8353/xl/");
//$c = new xmlrpc_client("http://10.10.1.10:8353/partner-vpn/servindo/");
//$c = new xmlrpc_client("http://localhost/partner-vpn/diamond/index.php");
//$c = new xmlrpc_client("http://10.10.1.10:80/fastpay_dev/");
//$c = new xmlrpc_client("http://localhost/FMSS_MP_XML_TRX/");

//$c->return_type = 'xml';
$c->return_type = "phpvals";
$c->setDebug(2);
$r = &$c->send($f);
if(!$r->faultCode()){
	$v = $r->value();
	echo "<pre>";
	print_r($v);
	//echo htmlspecialchars($v);
	//print_r($r->scalarval());
	echo "</pre>";

//$te = $v->scalarval();
//echo count($te)." ".$te[4]->me[string];
	
}else{
	//print "An error occurred: ";
	//print "Code: " . htmlspecialchars($r->faultCode()). " Reason: '" . htmlspecialchars($r->faultString()) . "'</pre><br/>";
}
?>