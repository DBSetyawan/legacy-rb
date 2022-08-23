<?php
//session_start();
//date_default_timezone_set('Asia/Jakarta');
set_time_limit(120);

include_once("lib/xmlrpc.inc");
include_once("lib/xmlrpcs.inc");
include_once("lib/xmlrpc_wrappers.inc");

require_once("include/Database.class.php");

require_once("include/config.inc.php");
//require_once("include/pgdbi.php");
//require_once("include/GlobalSetting.php");
require_once("include/function.php");

require_once("include/KodeProduk.class.php");
require_once("include/format_message/FormatMsg.class.php");
require_once("include/format_message/FormatDeposit.class.php");
require_once("include/format_message/FormatDaftar.class.php");
require_once("include/format_message/FormatMandatory.class.php");
require_once("include/format_message/FormatTelkom.class.php");
require_once("include/format_message/FormatPlnPasc.class.php");
require_once("include/format_message/FormatPlnPra.class.php");
require_once("include/format_message/FormatPlnNon.class.php");
require_once("include/format_message/FormatPdam.class.php");
require_once("include/format_message/FormatMultiFinance.class.php");
require_once("include/format_message/FormatPulsa.class.php");
require_once("include/format_message/FormatGame.class.php");
require_once("include/format_message/FormatTeleponPasc.class.php");
require_once("include/format_message/FormatTvKabel.class.php");
require_once("include/format_message/FormatDataTransaksi.class.php");
require_once("include/format_message/FormatGantiPin.class.php");
require_once("include/format_message/FormatCekSaldo.class.php");
require_once("include/format_message/FormatAsuransi.class.php");
require_once("include/format_message/FormatKartuKredit.class.php");

//koneksi ke database postgre
global $pgsql;
$pgsql = pg_connect("host=" . $__CFG_dbhost . " port=" . $__CFG_dbport . " dbname=" . $__CFG_dbname . " user=" . $__CFG_dbuser . " password=" . $__CFG_dbpass);

$msg = $HTTP_RAW_POST_DATA;
$host = getClientIP();//$_SERVER['REMOTE_ADDR'];

$mid = getNextMID();
$step = 1;
$sender = "XML CLIENT";
$receiver = $GLOBALS["__G_module_name"];
$via = $GLOBALS["__G_via"];

$plain = explode("<param>", $HTTP_RAW_POST_DATA);
$tag1 = "<string>";
$tag2 = "</string>";
$postag1 = strpos($plain[6], $tag1);
$postag2 = strpos($plain[6], $tag2);
$pinplain = substr($plain[6], ($postag1 + strlen($tag1)), ($postag2 - ($postag1 + strlen($tag1))));
$msg_log = str_replace($pinplain, "------", $msg);
writeLog($mid, $step, $host, $receiver, $msg_log, $via);

if ($HTTP_RAW_POST_DATA == "" || $_SERVER['REMOTE_ADDR'] != "10.0.51.2") {
    echo "IP Anda: " . getClientIP();//$_SERVER['REMOTE_ADDR'];
} else {
    $s = new xmlrpc_server(
                    array(
                        "rajabiller.inq" => array("function" => "inq"),
                        "rajabiller.pay" => array("function" => "pay"),
                        "rajabiller.paydetail" => array("function" => "payDetail"),
                        "rajabiller.pulsa" => array("function" => "pulsa"),
                        "rajabiller.game" => array("function" => "game"),
			"rajabiller.gantipin" => array("function" => "gantipin"),
			"rajabiller.balance" => array("function" => "balance"),
			"rajabiller.datatransaksi" => array("function" => "datatransaksi"),
			"rajabiller.cu" => array("function" => "cetakUlang"),
                        "rajabiller.inq_equity" => array("function" => "inqEquity"),
                        "rajabiller.pay_equity" => array("function" => "payEquity"),
                        "rajabiller.inq_bintang" => array("function" => "inqBintang"),
                        "rajabiller.pay_bintang" => array("function" => "payBintang"),
                        "rajabiller.inq_rumah_zakat" => array("function" => "inqRumahZakat"),
                        "rajabiller.pay_rumah_zakat" => array("function" => "payRumahZakat")
                    ), false);

    $s->setdebug(0);
    $s->compress_response = false;

    // out-of-band information: let the client manipulate the server operations.
    // we do this to help the testsuite script: do not reproduce in production!
    //if (isset($_GET['RESPONSE_ENCODING'])) $s->response_charset_encoding = $_GET['RESPONSE_ENCODING'];

    $s->service();
}

function inq($m) {
//TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN

    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel2 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel3 = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());

	$ip	= $_GET["ip"];
	if($ip != "10.0.0.20" && $ip != "10.0.51.2"){
		if(!isValidIP($idoutlet, $ip)){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}

    global $pgsql;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "TAGIHAN";
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
	$msg[$i+=1] = "";
	$msg[$i+=1] = "";
	$msg[$i+=1] = "";
	$msg[$i+=1] = "";
	$msg[$i+=1] = "";
	$msg[$i+=1] = $ref1;
	$msg[$i+=1] = "";
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
    $respon = postValue($fm);
    $resp = $respon[7];

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
	
	$frm = getParseProduk($kdproduk,$resp);
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_sisa_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
	$r_saldo_terpotong = $r_nominal + $r_nominaladmin;
	$r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
	//$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
	if(in_array($kdproduk, KodeProduk::getMultiFinance())) {
       $r_periode_tagihan = getLastPaidPeriode($kdproduk,$frm);
    } else {
		$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
	}
	
	$url_struk = "";
	if($frm->getStatus()<>"00"){
		$r_saldo_terpotong = 0;
	}
    /*$params = array(
        $_SERVER['REMOTE_ADDR'],
        'ANDA TIDAK DIIJINKAN MELAKUKAN INQUIRY'
    );*/

	$params = array(
            (string)$r_kdproduk, (string)$r_tanggal, (string)$r_idpel1, (string)$r_idpel2, (string)$r_idpel3, (string)$r_nama_pelanggan, (string)$r_periode_tagihan, (string)$r_nominal, (string)$r_nominaladmin, (string)$r_idoutlet, (string)$r_pin, (string)$ref1, (string)$r_idtrx, "0", (string)$r_status, (string)$r_keterangan, (string)$r_saldo_terpotong, (string)$r_sisa_saldo, (string)$url_struk
        );

	//insert into fmss_message
	$mid = $GLOBALS["mid"];
	$id_transaksi = $r_idtrx;
	$id_transaksi_partner = $ref1;
	
	//$db = new Database();
	//$q_ins_log = "INSERT INTO fmss_message (id_transaksi, id_transaksi_partner, mid, content, insert_datetime) 
	//				VALUES (".$id_transaksi.", ".$id_transaksi_partner.", ".$mid.", '".replace_forbidden_chars_msg($resp)."', NOW())";
	//$e_ins_log = mysql_query($q_ins_log, $db->getConnection());
	
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function pay($m) {
	//BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel2 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel3 = strtoupper($m->getParam($i+=1)->scalarval());
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref2 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref3 = strtoupper($m->getParam($i+=1)->scalarval());

	$ip	= $_GET["ip"];

	/*if($ip == "10.0.0.20"){
		die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
	} else {
		if(!isValidIP($idoutlet, $ip)){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}*/
	if($ip != "10.0.0.20"  && $ip != "10.0.51.2"){
		if(!isValidIP($idoutlet, $ip)){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}

    global $pgsql;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

	$mti = "BAYAR";
	if(in_array($kdproduk,KodeProduk::getAsuransi()) || in_array($kdproduk,KodeProduk::getKartuKredit())){
		$mti = "TAGIHAN";
	}

	$ceknom = getNominalTransaksi(trim($ref2)); //tambahan
	$cektoken = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal-1600), trim($kdproduk), trim($idoutlet)); //tambahan
    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = $mti;
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";           //TOKEN
    $msg[$i+=1] = "";           //SALDO
    $msg[$i+=1] = "";           //JENIS STRUK
    $msg[$i+=1] = "";           //KODE BANK
    $msg[$i+=1] = "";           //KODE PRODUK BILLER
    $msg[$i+=1] = $ref2;           //ID TRX
    $msg[$i+=1] = $ref1;           //STATUS
    $msg[$i+=1] = "";           //KETERANGAN
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
	//$respon = postValue($fm); //saya koment fitra dan hasil lihat pada kondisi if dibawah
	//print_r($respon);
	//$resp = $respon[7]; //saya koment fitra dan hasil lihat pada kondisi if dibawah
	//$resp = "null";
	
	//echo 'cek nom: '.$ceknom.' & nominal: '.$nominal;
	//die();
	
	/*tambahan*/
	if(!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
			$resp = "BAYAR*".$kdproduk."***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*".$nominal."*0*" . $idoutlet . "*" . $pin . "*------****".$kdproduk."**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI";
        } else {
			//$respon = postValueWithTimeOutDevel($fm);
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
       if($cektoken[0] != '-'){
			$resp = "BAYAR*".$kdproduk."***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*".$nominal."*0*" . $idoutlet . "*" . $pin . "*------****".$kdproduk."**XX*Sudah Pernah Terjadi status sukses di ID Outlet: ".$idoutlet.", Nominal:".$nominal.", IDPEL: ".$cektoken[0].", dan Token:".$cektoken[3]."*********************".$cektoken[3]."*************";
		} else {
			//$respon = postValueWithTimeOutDevel($fm);
			$respon = postValue($fm);
			//$respon = postValue($fm);
			$resp = $respon[7];
		}
    }
	/*tambahan*/
	
	$man = FormatMsg::mandatoryPayment();
	$frm = new FormatMandatory($man["pay"], $resp);
	$frm = getParseProduk($kdproduk,$resp);
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
	$r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_sisa_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
	$r_saldo_terpotong = 0;
	$r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
	//$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
	if(in_array($kdproduk, KodeProduk::getMultiFinance())) {
       $r_periode_tagihan = getLastPaidPeriode($kdproduk,$frm);
    } else {
		$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
	}
	$token2 = $cektoken[3];
	
	$r_reff3 = '0';
	//$r_reff3 = $frm->getTokenPln();
	if(substr($kdproduk,0,6)=="PLNPRA" && $frm->getStatus()=="00"){
		if($r_reff3 == '0'){
			$r_reff3 = $frm->getTokenPln();
		}
	}
	
	
	$url_struk = "";
	if($frm->getStatus()=="00"){
		//get url struk
		$r_saldo_terpotong = $r_nominal + $r_nominaladmin;
		$url = enkripUrl(strtoupper($idoutlet),$frm->getIdTrx());
		$url_struk = "https://202.43.173.234/struk/?id=".$url;
	}


	$params = array(
		(string)$r_kdproduk, (string)$r_tanggal, (string)$r_idpel1, (string)$r_idpel2, (string)$r_idpel3, (string)$r_nama_pelanggan, (string)$r_periode_tagihan, (string)$r_nominal, (string)$r_nominaladmin, (string)$r_idoutlet, (string)$r_pin, (string)$ref1, (string)$r_idtrx, (string)$r_reff3, (string)$r_status, (string)$r_keterangan, (string)$r_saldo_terpotong, (string)$r_sisa_saldo, (string)$url_struk
        );

	
	//$params = setMandatoryRespon($frm,$ref1,"","",$url_struk);
	//$params = getResponArray($kdproduk,$params,$resp);
	
	$is_return = true;
	/*if($resp == "null" || $frm->getStatus()=="35" || $frm->getStatus()=="68"){
		$is_return = false;
	} else if($frm->getStatus()<>"00"){
		if(cekIsProsesTransaksi){
			$is_return = false;	
		}		
	} else if($frm->getStatus()=="00"){
		$is_return = true;
	}
		
	if(!$is_return){
		if(getStatusMid($GLOBALS["mid"])){
			$respInq = getRespInq($ref2);
			
			$man = FormatMsg::mandatoryPayment();
			$frm = new FormatMandatory($man["pay"], $respInq);
			$params = setMandatoryRespon($frm,$ref1,"","");
			$params = getResponArray($kdproduk,$params,$respInq);

			if($kdproduk==KodeProduk::getPLNPrepaid()){
				$tokenpln = "Token akan dikirim via SMS ke ".$idpel3;
                $noref2 = "";
                $status = "00";
                $keterangan = "Token akan dikirim via SMS ke ".$idpel3;
			
				$params[10] = $noref2;	
				$params[12] = $status;
				$params[13] = $keterangan;
				$params[34] = $tokenpln;
			} else {
				$params[10] = "";	
				$params[12] = (string) "00";
				$params[13] = "SEDANG DIPROSES";
			}
			
			insertSuksesPaksaMy($GLOBALS["mid"]);
			insertSuksesPaksaPg($GLOBALS["mid"]);
		} else {
			$params[10] = "";
			$params[12] = (string) "35";
			$params[13] = "WAKTU TRANSAKSI HABIS, COBA BEBERAPA SAAT LAGI";
		}
		return new xmlrpcresp(php_xmlrpc_encode($params));
	}*/
	//print_r($params);
	return new xmlrpcresp(php_xmlrpc_encode($params));
}

function payDetail($m) {
//BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel2 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel3 = strtoupper($m->getParam($i+=1)->scalarval());
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref2 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref3 = strtoupper($m->getParam($i+=1)->scalarval());

	$ip	= $_GET["ip"];
	/*if($ip == "10.0.0.20"){
		die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
	} else {
		if(!isValidIP($idoutlet, $ip)){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}*/
	if($ip != "10.0.0.20"  && $ip != "10.0.51.2"){
		if(!isValidIP($idoutlet, $ip)){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}

    global $pgsql;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

	$mti = "BAYAR";
	if(in_array($kdproduk,KodeProduk::getAsuransi()) || in_array($kdproduk,KodeProduk::getKartuKredit())){
		$mti = "TAGIHAN";
	}

	$ceknom = getNominalTransaksi(trim($ref2));
	$cektoken = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal-1600), trim($kdproduk), trim($idoutlet)); //tambahan
    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = $mti;
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";           //TOKEN
    $msg[$i+=1] = "";           //SALDO
    $msg[$i+=1] = "";           //JENIS STRUK
    $msg[$i+=1] = "";           //KODE BANK
    $msg[$i+=1] = "";           //KODE PRODUK BILLER
    $msg[$i+=1] = $ref2;           //ID TRX
    $msg[$i+=1] = $ref1;           //STATUS
    $msg[$i+=1] = "";           //KETERANGAN
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
	//$respon = postValue($fm);
	//print_r($respon);
	//$resp = $respon[7];
		
	if(!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
			$resp = "BAYAR*".$kdproduk."***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*".$nominal."*0*" . $idoutlet . "*" . $pin . "*------****".$kdproduk."**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI";
        } else {
			//$respon = postValueWithTimeOutDevel($fm);
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
       if($cektoken[0] != '-'){
			$resp = "BAYAR*".$kdproduk."***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*".$nominal."*0*" . $idoutlet . "*" . $pin . "*------****".$kdproduk."**XX*Sudah Pernah Terjadi status sukses di ID Outlet: ".$idoutlet.", Nominal:".$nominal.", IDPEL: ".$cektoken[0].", dan Token:".$cektoken[3]."*********************".$cektoken[3]."*************";
		} else {
			//$respon = postValueWithTimeOutDevel($fm);
			$respon = postValue($fm);
			$resp = $respon[7];
		}
    }
		
	$man = FormatMsg::mandatoryPayment();
	$frm = new FormatMandatory($man["pay"], $resp);
	$frm = getParseProduk($kdproduk,$resp);
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
	$r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_sisa_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
	$r_saldo_terpotong = 0;
	$r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
	//$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
	if(in_array($kdproduk, KodeProduk::getMultiFinance())) {
       $r_periode_tagihan = getLastPaidPeriode($kdproduk,$frm);
    } else {
		$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
	}
	$r_additional_datas = getAdditionalDatas($kdproduk,$frm);
	
	$token2 = $cektoken[3];
	$r_reff3 = '0';
//	$r_reff3 = $frm->getTokenPln();
	if(substr($kdproduk,0,6)=="PLNPRA" && $frm->getStatus()=="00"){
		if($r_reff3 == '0'){
			$r_reff3 = $frm->getTokenPln();
		}
	}
	
	
	$url_struk = "";
	if($frm->getStatus()=="00"){
		//get url struk
		$r_saldo_terpotong = $r_nominal + $r_nominaladmin;
		$url = enkripUrl(strtoupper($idoutlet),$frm->getIdTrx());
		$url_struk = "https://202.43.173.234/struk/?id=".$url;
	}


	$params = array(
		(string)$r_kdproduk, (string)$r_tanggal, (string)$r_idpel1, (string)$r_idpel2, (string)$r_idpel3, (string)$r_nama_pelanggan, (string)$r_periode_tagihan, (string)$r_nominal, (string)$r_nominaladmin, (string)$r_idoutlet, (string)$r_pin, (string)$ref1, (string)$r_idtrx, (string)$r_reff3, (string)$r_status, (string)$r_keterangan, (string)$r_saldo_terpotong, (string)$r_sisa_saldo, (string)$url_struk
        );

	if(count($r_additional_datas) > 0){
		array_push($params, $r_additional_datas);
	}
	
	//$params = setMandatoryRespon($frm,$ref1,"","",$url_struk);
	//$params = getResponArray($kdproduk,$params,$resp);
	
	$is_return = true;
	/*if($resp == "null" || $frm->getStatus()=="35" || $frm->getStatus()=="68"){
		$is_return = false;
	} else if($frm->getStatus()<>"00"){
		if(cekIsProsesTransaksi){
			$is_return = false;	
		}		
	} else if($frm->getStatus()=="00"){
		$is_return = true;
	}
		
	if(!$is_return){
		if(getStatusMid($GLOBALS["mid"])){
			$respInq = getRespInq($ref2);
			
			$man = FormatMsg::mandatoryPayment();
			$frm = new FormatMandatory($man["pay"], $respInq);
			$params = setMandatoryRespon($frm,$ref1,"","");
			$params = getResponArray($kdproduk,$params,$respInq);

			if($kdproduk==KodeProduk::getPLNPrepaid()){
				$tokenpln = "Token akan dikirim via SMS ke ".$idpel3;
                $noref2 = "";
                $status = "00";
                $keterangan = "Token akan dikirim via SMS ke ".$idpel3;
			
				$params[10] = $noref2;	
				$params[12] = $status;
				$params[13] = $keterangan;
				$params[34] = $tokenpln;
			} else {
				$params[10] = "";	
				$params[12] = (string) "00";
				$params[13] = "SEDANG DIPROSES";
			}
			
			insertSuksesPaksaMy($GLOBALS["mid"]);
			insertSuksesPaksaPg($GLOBALS["mid"]);
		} else {
			$params[10] = "";
			$params[12] = (string) "35";
			$params[13] = "WAKTU TRANSAKSI HABIS, COBA BEBERAPA SAAT LAGI";
		}
		return new xmlrpcresp(php_xmlrpc_encode($params));
	}*/
	//print_r($params);
	return new xmlrpcresp(php_xmlrpc_encode($params));
}

function pulsa($m){
	$i = -1;
	$kdproduk 		= strtoupper($m->getParam($i+=1)->scalarval());
	$nohp			= strtoupper($m->getParam($i+=1)->scalarval());
	$idoutlet 		= strtoupper($m->getParam($i+=1)->scalarval());
	$pin 			= strtoupper($m->getParam($i+=1)->scalarval());
	$ref1 			= strtoupper($m->getParam($i+=1)->scalarval());
	
	$ip	= $_GET["ip"];
	if($ip == "10.0.0.20"){
		die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
	} else {
		if(!isValidIP($idoutlet, $ip)  && $ip != "10.0.51.2"){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}

	$stp = $GLOBALS["step"] + 1;
	$msg = array();
	$i = -1;
	
	$msg[$i+=1] = "PULSA";
	$msg[$i+=1] = $kdproduk;						//KDPRODUK
	$msg[$i+=1] = $GLOBALS["mid"];					//MID
	$msg[$i+=1] = $stp;								//STEP
	$msg[$i+=1] = "";								//TANGGAL
	$msg[$i+=1] = $GLOBALS["via"];					//VIA
	$msg[$i+=1] = $nohp;							//NOHP
	$msg[$i+=1] = "";								//NOMINAL
	$msg[$i+=1] = strtoupper($idoutlet);			//IDOUTLET
	$msg[$i+=1] = $pin;								//PIN
	$msg[$i+=1] = "";								//TOKEN
	$msg[$i+=1] = "";								//FIELD_KODE_PRODUK_BILLER
	$msg[$i+=1] = "";								//FIELD_SN
	$msg[$i+=1] = "";								//FIELD_BALANCE
	$msg[$i+=1] = "";								//FIELD_TRX_ID
	$msg[$i+=1] = $ref1;								//FIELD_STATUS
	$msg[$i+=1] = "";								//FIELD_KETERANGAN

	$fm = convertFM($msg,"*");
	//echo "fm = ".$fm."<br>";
	$sender = $GLOBALS["__G_module_name"];
	$receiver = $GLOBALS["__G_receiver"];
	writeLog($GLOBALS["mid"],$stp,$sender,$receiver,$fm,$GLOBALS["via"]);
	//echo $msg."<br>";

	$respon = postValue($fm);
	//print_r($respon);
	$resp = $respon[7];
	//$resp = "";
	//$resp = "PULSA*I10*43712003*9*20120906204621*YM*".$nohp."*10000*".$idoutlet."*".$pin."*------*-*0000045547*389017*32527183*00*Pengisian pulsa I10 Anda ke nomor ".$nohp." BERHASIL. SN=0000045547 Harga=10025";
	$format = FormatMsg::pulsa();
	$frm = new FormatPulsa($format[1],$resp);

	//print_r($frm->data);

	$r_kdproduk 		= $frm->getKodeProduk();
	$r_tanggal	 		= $frm->getTanggal();
	$r_nohp 			= $frm->getNohp();
	$r_idoutlet 		= $frm->getMember();
	$r_pin 				= $frm->getPin();
	$r_idtrx 			= $frm->getIdTrx();
	$r_status 			= $frm->getStatus();
	$r_keterangan 		= $frm->getKeterangan();
	$r_sn				= $frm->getSN();

	$params=array(
		$r_kdproduk, $r_tanggal, $r_nohp, $r_idoutlet, $r_pin, $r_sn, $ref1, $r_idtrx, $r_status, $r_keterangan, (string)$r_saldo_terpotong, (string)$r_sisa_saldo
	);

	/*$is_return = true;
	if($resp == "null" || $frm->getStatus()=="35" || $frm->getStatus()=="68"){
		$is_return = false;
	} else if($frm->getStatus()<>"00"){
		if(cekIsProsesTransaksi){
			$is_return = false;	
		}		
	} else if($frm->getStatus()=="00"){
		$is_return = true;
	}
		
	if(!$is_return){
		$params=array(
			$kdproduk, $r_tanggal, $nohp, strtoupper($idoutlet), "", "", $ref1, "", (string) "00", "SEDANG DIPROSES"
		);

		if(getStatusMid($GLOBALS["mid"])){
			insertSuksesPaksaMy($GLOBALS["mid"]);
			insertSuksesPaksaPg($GLOBALS["mid"]);
		} else {
			$params[5] = "";
			$params[8] = (string) "35";
			$params[9] = "WAKTU TRANSAKSI HABIS, COBA BEBERAPA SAAT LAGI";
		}
		return new xmlrpcresp(php_xmlrpc_encode($params));
	}*/

	return new xmlrpcresp(php_xmlrpc_encode($params));
}

function game($m){
	$i = -1;
	$kdproduk 		= strtoupper($m->getParam($i+=1)->scalarval());
	$nohp			= strtoupper($m->getParam($i+=1)->scalarval());
	$idoutlet 		= strtoupper($m->getParam($i+=1)->scalarval());
	$pin 			= strtoupper($m->getParam($i+=1)->scalarval());
	$ref1 			= strtoupper($m->getParam($i+=1)->scalarval());
	
	$ip	= $_GET["ip"];
	if($ip == "10.0.0.20"){
		die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
	} else {
		if(!isValidIP($idoutlet, $ip)  && $ip != "10.0.51.2"){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}	

	$stp = $GLOBALS["step"] + 1;
	$msg = array();
	$i = -1;
	
	$msg[$i+=1] = "GAME";
	$msg[$i+=1] = $kdproduk;						//KDPRODUK
	$msg[$i+=1] = $GLOBALS["mid"];					//MID
	$msg[$i+=1] = $stp;								//STEP
	$msg[$i+=1] = "";								//TANGGAL
	$msg[$i+=1] = $GLOBALS["via"];					//VIA
	$msg[$i+=1] = $nohp;							//NOHP
	$msg[$i+=1] = "";								//NOMINAL
	$msg[$i+=1] = strtoupper($idoutlet);			//IDOUTLET
	$msg[$i+=1] = $pin;								//PIN
	$msg[$i+=1] = "";								//TOKEN
	$msg[$i+=1] = "";								//FIELD_KODE_PRODUK_BILLER
	$msg[$i+=1] = "";								//FIELD_SN
	$msg[$i+=1] = "";								//FIELD_BALANCE
	$msg[$i+=1] = "";								//FIELD_TRX_ID
	$msg[$i+=1] = $ref1;								//FIELD_STATUS
	$msg[$i+=1] = "";								//FIELD_KETERANGAN

	$fm = convertFM($msg,"*");
	//echo "fm = ".$fm."<br>";
	$sender = $GLOBALS["__G_module_name"];
	$receiver = $GLOBALS["__G_receiver"];
	writeLog($GLOBALS["mid"],$stp,$sender,$receiver,$fm,$GLOBALS["via"]);
	//echo $msg."<br>";

	$respon = postValue($fm);
	//print_r($respon);
	$resp = $respon[7];
	
	$format = FormatMsg::game();
	$frm = new FormatGame($format[1],$resp);

	//print_r($frm->data);

	$r_kdproduk 		= $frm->getKodeProduk();
	$r_tanggal	 		= $frm->getTanggal();
	$r_nohp 			= $frm->getNohp();
	$r_idoutlet 		= $frm->getMember();
	$r_pin 				= $frm->getPin();
	$r_idtrx 			= $frm->getIdTrx();
	$r_status 			= $frm->getStatus();
	$r_keterangan 		= $frm->getKeterangan();
	$r_sn				= $frm->getSN();

	$params=array(
		$r_kdproduk, $r_tanggal, $r_nohp, $r_idoutlet, $r_pin, $r_sn, $ref1, $r_idtrx, $r_status, $r_keterangan
	);

	/*$is_return = true;
	if($resp == "null" || $frm->getStatus()=="35" || $frm->getStatus()=="68"){
		$is_return = false;
	} else if($frm->getStatus()<>"00"){
		if(cekIsProsesTransaksi){
			$is_return = false;	
		}		
	} else if($frm->getStatus()=="00"){
		$is_return = true;
	}
		
	if(!$is_return){
		$sn = "SN akan dikirim via SMS ke ".$nohp;
		$status = "00";
		$keterangan = "SN akan dikirim via SMS ke ".$nohp;
	
		$params=array(
			$kdproduk, $r_tanggal, $nohp, strtoupper($idoutlet), "", $sn, $ref1, "", (string) "00", $keterangan, (string)$r_saldo_terpotong, (string)$r_sisa_saldo
		);

		if(getStatusMid($GLOBALS["mid"])){
			insertSuksesPaksaMy($GLOBALS["mid"]);
			insertSuksesPaksaPg($GLOBALS["mid"]);
		} else {
			$params[5] = "";
			$params[8] = (string) "35";
			$params[9] = "WAKTU TRANSAKSI HABIS, COBA BEBERAPA SAAT LAGI";
		}
		return new xmlrpcresp(php_xmlrpc_encode($params));
	}*/

	return new xmlrpcresp(php_xmlrpc_encode($params));
}

function balance($m){
    //GPIN*PINBARU*IDOUTLET*PIN*TOKEN*VIA
    $i = -1;
    $idoutlet 		= strtoupper($m->getParam($i+=1)->scalarval());
	$pin 			= strtoupper($m->getParam($i+=1)->scalarval());

	$ip	= $_GET["ip"];
/*	if($ip != "10.0.0.20"  && $ip != "10.0.51.2"){
		if(!isValidIP($idoutlet, $ip)){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}*/
    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = "SAL";
    $msg[$i+=1] = "SAL";						//KDPRODUK
    $msg[$i+=1] = $GLOBALS["mid"];					//MID
    $msg[$i+=1] = $stp;								//STEP
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];
    $msg[$i+=1] = $idoutlet;
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";			
    
    $fm = convertFM($msg,"*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"],$stp,$sender,$receiver,$fm,$GLOBALS["via"]);
    //echo $msg."<br>";

    $respon = postValue($fm);
    //print_r($respon);
    $resp = $respon[7];

    $format = FormatMsg::cekSaldo();
    $frm = new FormatCekSaldo($format[1],$resp);

    $r_idoutlet 		= $frm->getMember();
    $r_pin				= $frm->getPin();
	$r_balance			= $frm->getSaldo();
    $r_status 			= $frm->getStatus();
    $r_keterangan 		= $frm->getKeterangan();

    $params=array(
            $r_idoutlet, $r_pin, $r_balance, $r_status, $r_keterangan
    );

    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function datatransaksi($m) {
    $next = FALSE;
    $end = FALSE;

    $i = -1;
    $tgl1 = strtoupper($m->getParam($i+=1)->scalarval());
    $tgl2 = strtoupper($m->getParam($i+=1)->scalarval());
    $idtrx = strtoupper($m->getParam($i+=1)->scalarval());
    $idproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel = strtoupper($m->getParam($i+=1)->scalarval());
    $limit = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());

	$ip	= $_GET["ip"];
	if($ip != "10.0.0.20"  && $ip != "10.0.51.2"){
		if(!isValidIP($idoutlet, $ip)){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}    

    global $pgsql;
    if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
        die("");
    }
    
    $msg[0] = $tgl1;
    $msg[1] = $tgl2;
    $msg[2] = $idproduk;
    $msg[3] = $idpel;
    $msg[4] = $limit;
    $msg[5] = $idoutlet;
    $msg[6] = $pin;

    if (outletexists($idoutlet)) {
        $next = TRUE;
    } else {
        $msg[7] = "01";
        $msgcontent[8] = "ID Outlet tidak terdaftar atau tidak aktif";
        $next = FALSE;
    }

    if ($next) {
        if (checkpin($idoutlet, $pin)) {
            $next = TRUE;
        } else {
            $msg[7] = "02";
            $msgcontent[8] = "Pin yang Anda masukkan salah";
            $next = FALSE;
        }
    }

    if ($next) {
        if ($idtrx) {
            $next = TRUE;
        } else {
            $next = FALSE;
            $end = TRUE;
        }
    }


    if ($next) {
        if ($idproduk != "") {
            if (productexists($idproduk)) {
                $next = TRUE;
            } else {
                $msg[7] = "03";
                $msgcontent[8] = "Produk yang Anda masukkan tidak tersedia";
                $next = FALSE;
            }
        }
    }

    if ($next) {
        if ($tgl1 != "" && $tgl2 != "") {
            if (isvaliddaterange($tgl1, $tgl2)) {
                $next = TRUE;
            } else {
                $msg[7] = "05";
                $msgcontent[8] = "Range tanggal yang Anda masukkan salah";
                $next = FALSE;
            }
        } else {
            $msg[7] = "04";
            $msgcontent[8] = "Anda harus memberikan value pada range tanggal";
            $next = FALSE;
        }
    }

    if ($next || $end) {
        $msg[7] = "00";
        $data = getDataTransaksi($idoutlet, $idtrx, $idproduk, $idpel, $limit, $tgl1, $tgl2);
        $cnt = count($data);

        if ($cnt > 0) {
            $msgcontent[] = "Transaksi berhasil";
            //Looping
            for ($i = 0; $i < $cnt; $i++) {
                $datatransaksi = array();
                $datatransaksi[] = $data[$i]->id_transaksi;
                $datatransaksi[] = $data[$i]->transaksidatetime;
                $datatransaksi[] = $data[$i]->id_produk;
                $datatransaksi[] = $data[$i]->namaproduk;
                $datatransaksi[] = $data[$i]->idpelanggan;
                $datatransaksi[] = $data[$i]->response_code;
                $datatransaksi[] = $data[$i]->keterangan;
                $datatransaksi[] = $data[$i]->nominal;
                $datatransaksi[] = $data[$i]->sn;
                $msgcontent[] = implode("#", $datatransaksi);
            }
            //End of looping
        } else {
            $msg[7] = "06";
            $msgcontent[] = "Tidak ada data transaksi sesuai kriteria yang di-request";
        }
    }
    //Msg Constructor
    $resp = implode("*", $msg);
    $respcontent = implode("~", $msgcontent);
    $arr_resp[] = $resp;
    $arr_resp[] = $respcontent;
    $response = implode("*", $arr_resp);



    $format = FormatMsg::dataTransaksi();
    $frm = new FormatDataTransaksi($format, $response);
    //print_r($frm->data);
    //TANGGAL1*TANGGAL2*KDPRODUK*IDPEL*LIMIT*IDOUTLET*PIN*RESPONSECODE*CONTENT

    $r_tanggal1 = $frm->getTanggal1();
    $r_tanggal2 = $frm->getTanggal2();
    $r_kdproduk = $frm->getKodeProduk();
    $r_idpel = $frm->getIdPel();
    $r_limit = $frm->getLimit();
    $r_idoutlet = $frm->getIdOutlet();
    $r_pin = $frm->getPin();
    $r_responsecode = $frm->getResponseCode();
    $r_content = $frm->getContent();

    $params = array(
        $r_tanggal1, $r_tanggal2, $r_kdproduk, $r_idpel, $r_limit, $r_idoutlet, $r_pin, $r_responsecode
    );

    $r_content = explode("~", $r_content);
    for ($i = 0; $i < count($r_content); $i++) {
        array_push($params, $r_content[$i]);
    }

    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function cetakUlang($m) {
//BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i = -1;
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref2 = strtoupper($m->getParam($i+=1)->scalarval());

	$ip	= $_GET["ip"];
	if($ip <> ""){
		if(!isValidIP($idoutlet, $ip)){
			die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
		}
	}
	
    if (kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    global $pgsql;
    if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
        die("");
    }
    
    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "CU";
    $msg[$i+=1] = "CU";
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";           //TOKEN
    $msg[$i+=1] = "";           //SALDO
    $msg[$i+=1] = "";           //JENIS STRUK
    $msg[$i+=1] = "";           //KODE BANK
    $msg[$i+=1] = "";           //KODE PRODUK BILLER
    $msg[$i+=1] = $ref2;           //ID TRX
    $msg[$i+=1] = $ref1;           //STATUS
    $msg[$i+=1] = "";           //KETERANGAN
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
    //if($list[strtoupper($idoutlet)] && in_array($GLOBALS["host"],$list[strtoupper($idoutlet)])){
    $respon = postValue($fm);
    //print_r($respon);
    $resp = $respon[7];
    writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);

    $man = FormatMsg::mandatoryPayment();
	$frm = new FormatMandatory($man["pay"], $resp);
	$kdproduk = $frm->getKodeProduk();	
	
	$r_saldo_terpotong = "";
	$r_nama_pelanggan = "";
	$r_periode_tagihan = "";
	$r_reff3 = "0";
	
	if($frm->getStatus()=="00"){
		$frm = getParseProduk($kdproduk,$resp);
		$r_saldo_terpotong = $r_nominal + $r_nominaladmin;
		$r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
		$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
		
		if(substr($frm->getKodeProduk(),0,6)=="PLNPRA"){
			$r_reff3 = $frm->getTokenPln();
		}
	}
	//echo $resp;
	$r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_sisa_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
	/*$r_saldo_terpotong = $r_nominal + $r_nominaladmin;
	$r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
	$r_periode_tagihan = getBillPeriod($kdproduk,$frm);*/
	
	
	
	$url_struk = "";
	if($frm->getStatus()=="00"){
		//get url struk
		$url = enkripUrl(strtoupper($idoutlet),$ref2);
		$url_struk = "https://202.43.173.234/struk/?id=".$url;
	}

	$params = array(
		(string)$r_kdproduk, (string)$r_tanggal, (string)$r_idpel1, (string)$r_idpel2, (string)$r_idpel3, (string)$r_nama_pelanggan, (string)$r_periode_tagihan, (string)$r_nominal, (string)$r_nominaladmin, (string)$r_idoutlet, (string)$r_pin, (string)$ref1, (string)$r_idtrx, "0", (string)$r_status, (string)$r_keterangan, (string)$r_saldo_terpotong, (string)$r_sisa_saldo, (string)$url_struk
        );

    return new xmlrpcresp(php_xmlrpc_encode($params));
}
function inqEquity($m){  
    $i = -1;
    $tagihan = "TAGIHAN";
    $kode_produk = "ASREQJLN";
    $mid = $GLOBALS["mid"]; //1;
    
    $step = "1";
    $via = "XML";
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    
    $ip_mac_add = "--;--;nul;nul";
    
    $ip	= $_GET["ip"];
    /*if($ip <> ""){
        if(!isValidIP($idoutlet, $ip)){
            die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
        }
    }*/
    
    $msg = array();
    $i = -1;
    $msg[$i+=1] = $tagihan;
    $msg[$i+=1] = $kode_produk;
    $msg[$i+=1] = $mid;
    $msg[$i+=1] = $step;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $via;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $id_outlet;
    $msg[$i+=1] = $pin;//pin
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ip_mac_add;
    for($k=$i;$k<=54;$k++)
    {
        $msg[$k]= "";
    }
    $fm = convertFM($msg, "*");  
//    echo $fm;
    
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;
    return new xmlrpcresp(php_xmlrpc_encode($params));
}
function payEquity($m){
    $i = -1;
    $tagihan = "BAYAR";
    $kode_produk = "ASREQJLN";
    $mid = $GLOBALS["mid"]; //1;                                              //"453759285";
    $step = "1";
    $via = "XML";
    $id_ktp = strtoupper($m->getParam($i+=1)->scalarval());                 //"0151518190688000";
    $id_outlet =strtoupper($m->getParam($i+=1)->scalarval());   
    $pin = strtoupper($m->getParam($i+=1)->scalarval());                    //"BS0004";
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());                //"10000";
    $id_trx_inq = strtoupper($m->getParam($i+=1)->scalarval());             //"326101943";
    $ip_mac_add = "--;--;nul;nul";
    $umum = "UMUM";
    $nama_tertanggung = strtoupper($m->getParam($i+=1)->scalarval());       //"Andi Yusanto";
    $tgl_lahir_tertanggung = strtoupper($m->getParam($i+=1)->scalarval());  //"1988-06-19";
    $tgl_keberangkatan = strtoupper($m->getParam($i+=1)->scalarval());      //"2015-06-01 00:00";
    $telp_tertanggung = strtoupper($m->getParam($i+=1)->scalarval());       //"085649492115";
    $telp_ahli_waris = strtoupper($m->getParam($i+=1)->scalarval());        //"085649492115";
    

    $ip	= $_GET["ip"];
    /*if($ip <> ""){
        if(!isValidIP($idoutlet, $ip)){
            die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
        }
    }*/
    $msg = array();
    $i = -1;
    $msg[$i+=1] = $tagihan;         //1
    $msg[$i+=1] = $kode_produk;     //2
    $msg[$i+=1] = $mid;             //3
    $msg[$i+=1] = $step;             //4
    $msg[$i+=1] = "";               //5
    $msg[$i+=1] = $via;             //6
    $msg[$i+=1] = $id_ktp;          //7
    $msg[$i+=1] = "";               //8
    $msg[$i+=1] = "";               //9
    $msg[$i+=1] = $nominal;         //10
    $msg[$i+=1] = "";               //11
    $msg[$i+=1] = $id_outlet;       //12
    $msg[$i+=1] = $pin;             //13
    $msg[$i+=1] = "------";         //14
    $msg[$i+=1] = "";               //15
    $msg[$i+=1] = "";               //16
    $msg[$i+=1] = "";               //17
    $msg[$i+=1] = "";               //18
    $msg[$i+=1] = $id_trx_inq;      //19
    $msg[$i+=1] = "";               //20
    $msg[$i+=1] = $ip_mac_add;      
    $msg[$i+=1] = $umum;            
    $msg[$i+=1] = "";               
    $msg[$i+=1] = $id_ktp;          
    $msg[$i+=1] = "";               //25
    $msg[$i+=1] = "";                
    $msg[$i+=1] = "";                   
    $msg[$i+=1] = $nama_tertanggung;    
    $msg[$i+=1] = $kode_produk;         
    $msg[$i+=1] = "";               //30
    $msg[$i+=1] = "";               
    $msg[$i+=1] = "";                   
    $msg[$i+=1] = "";               
    $msg[$i+=1] = "";               
    $msg[$i+=1] = "";               //35
    $msg[$i+=1] = "";               
    $msg[$i+=1] = "";               
    $msg[$i+=1] = $tgl_lahir_tertanggung;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";               //40
    $msg[$i+=1] = "";               
    $msg[$i+=1] = $tgl_keberangkatan;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $telp_tertanggung;
    $msg[$i+=1] = "";               //45
    $msg[$i+=1] = "";               
    $msg[$i+=1] = "";               
    $msg[$i+=1] = "";               
    $msg[$i+=1] = $telp_ahli_waris;     
    $msg[$i+=1] = "";               //50
    $msg[$i+=1] = "";               
    $msg[$i+=1] = "";                   
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $fm = convertFM($msg, "*");  
//    echo $fm;
    
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;
    
    return new xmlrpcresp(php_xmlrpc_encode($params));
}
function inqBintang($m)
{
    $i = -1;
    $kode_produk = strtoupper($m->getParam($i+=1)->scalarval());//'ASRBINT2';
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());//'BS0004';
    $pin = strtoupper($m->getParam($i+=1)->scalarval());//141414;
    
    $mid = $GLOBALS["mid"]; //1;
    $step = 1;    
    
    $ip = $_GET["ip"];
    /*if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }*/
    
    $msg = array();
    $i = -1;
    $msg[$i+=1] = 'TAGIHAN';                //*1
    $msg[$i+=1] = $kode_produk;             //*2
    $msg[$i+=1] = $mid;                     //453606894*
    $msg[$i+=1] = $step;                    //*
    $msg[$i+=1] = "";                       //*5
    $msg[$i+=1] = "XML";                    //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*10
    $msg[$i+=1] = "";                       //
    $msg[$i+=1] = $id_outlet;               //*
    $msg[$i+=1] = $pin;                     //------*
    $msg[$i+=1] = "";                       //------*15
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*20
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "-;-;null;null";          //*
    $msg[$i+=1] = "";                       //
    for($j = $i;$j<=55;$j++)
    {
        $msg[$j] = "";//
    }
    $fm = convertFM($msg, "*");  
//    echo $fm;
    
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;
    
    return new xmlrpcresp(php_xmlrpc_encode($params));
}
function payBintang($m)
{
    $i = -1;
    $kode_produk = strtoupper($m->getParam($i+=1)->scalarval());//'ASRBINT2';
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());//'BS0004';
    $pin = strtoupper($m->getParam($i+=1)->scalarval());//141414;  
    
    $id_pelanggan = strtoupper($m->getParam($i+=1)->scalarval());//"123456789";
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());//"2500";
    $id_transaksi_inq = strtoupper($m->getParam($i+=1)->scalarval());//"326782413";
    
    $nama = strtoupper($m->getParam($i+=1)->scalarval());//"Andi Yusanto";
    $no_telp = strtoupper($m->getParam($i+=1)->scalarval());//"085649492115";
    
    $mid = $GLOBALS["mid"]; //1;
    $step = 1;
    
    
    $ip = $_GET["ip"];
    /*if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }*/
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "BAYAR";//*
    $msg[$i+=1] = $kode_produk;//"ASRBINT1";//*
    $msg[$i+=1] = $mid;//"456090247";//*
    $msg[$i+=1] = $step;//"2";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "XML";//*
    $msg[$i+=1] = $id_pelanggan;//"123456789";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $nominal;//"2500";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $id_outlet;//"BS0004";//*
    $msg[$i+=1] = $pin;//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $id_transaksi_inq;//"326782413";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "-;-;null;null";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $nama;//"Andi Yusanto";//*
    $msg[$i+=1] = $kode_produk;//"ASRBINT1";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $no_telp;//"08564949211";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $fm = convertFM($msg, "*");
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;
    
    return new xmlrpcresp(php_xmlrpc_encode($params));    
}
function inqRumahZakat($m)
{
    $i = -1;
    $kode_produk = strtoupper($m->getParam($i+=1)->scalarval());//'RZZ';
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());//'BS0004';
    $pin = strtoupper($m->getParam($i+=1)->scalarval());//141414;
    
    $mid = $GLOBALS["mid"]; //1;
    $step = 1; 
    
    $ip = $_GET["ip"];
    /*if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }*/
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "TAGIHAN";                //*
    $msg[$i+=1] = $kode_produk;             //RZZ*
    $msg[$i+=1] = $mid;                     //456078090*
    $msg[$i+=1] = $step;                    //2*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "XML";                    //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = $id_outlet;               //BS0004*
    $msg[$i+=1] = $pin;                     //------*
    $msg[$i+=1] = "";                       //------*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";                       //*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "-;-;null;null";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $fm = convertFM($msg, "*");
    
    
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;
    
    return new xmlrpcresp(php_xmlrpc_encode($params));    
}
function payRumahZakat($m)
{
    $i = -1;
    $kode_produk = strtoupper($m->getParam($i+=1)->scalarval());//'RZZ';
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());//'BS0004';
    $pin = strtoupper($m->getParam($i+=1)->scalarval());//141414;
    
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());//"1000";
    $id_transaksi_inq = strtoupper($m->getParam($i+=1)->scalarval());//"326782413";
    $nama = strtoupper($m->getParam($i+=1)->scalarval());//"Andi Yusanto";
    
    $no_telp = strtoupper($m->getParam($i+=1)->scalarval());//"085649492115";
    $alamat = strtoupper($m->getParam($i+=1)->scalarval());//"Jl Anggrek inpres 8 Kureksari Waru";
    $kode_propinsi = strtoupper($m->getParam($i+=1)->scalarval());//"28";
    
    $kode_kota = strtoupper($m->getParam($i+=1)->scalarval());//"250";
    $email = $m->getParam($i+=1)->scalarval();//"andi_yusanto@yahoo.com";
       
    $mid = $GLOBALS["mid"]; //1;
    $step = 1; 
    
    $ip = $_GET["ip"];
    /*if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }*/
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "BAYAR";//*
    $msg[$i+=1] = $kode_produk;//RZZ*
    $msg[$i+=1] = $mid;//456093813*
    $msg[$i+=1] = $step;//2*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "XML";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $nominal;//1000*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $id_outlet;//BS0004*
    $msg[$i+=1] = $pin;//------*
    $msg[$i+=1] = "";//------*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $id_transaksi_inq;//326778933*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "-;-;null;null";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $no_telp;//085649492115*
    $msg[$i+=1] = $nama;//Andi Yusanto*
    $msg[$i+=1] = $alamat;//Jl Anggrek inpres 8 Kureksari Waru*
    $msg[$i+=1] = $kode_propinsi;//28*
    $msg[$i+=1] = $kode_kota;//250*
    $msg[$i+=1] = $nominal;//1000*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = "";//*
    $msg[$i+=1] = $email;//andi_yusanto@yahoo.com
    $fm = convertFM($msg, "*");
    
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;
    
    return new xmlrpcresp(php_xmlrpc_encode($params));    
}


?>
