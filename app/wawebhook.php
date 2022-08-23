<?php
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');
set_time_limit(120);
require_once("include/Database.class.php");
require_once("include/config.inc.php");
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
require_once("include/format_message/FormatCekHarga.class.php");
require_once("include/format_message/FormatPgn.class.php");
//koneksi ke database postgre

$pgsql      = pg_connect("host=" . $__CFG_dbhost . " port=" . $__CFG_dbport . " dbname=" . $__CFG_dbname . " user=" . $__CFG_dbuser . " password=" . $__CFG_dbpass);
$mid        = getNextMID();
$msg        = json_decode($_POST["data"]);
global $pgsql;
$rawcpy 			= $msg->text;
$from_phone         = $msg->from;
$getpin             = end(explode('.',$rawcpy));
$stored_request     = str_replace($getpin, '------', $rawcpy);
$sender             = "WA CLIENT";
$receiver           = $GLOBALS["__G_module_name"];
$via                = "WA";
$step               = 1;
$host               = $from_phone;
writeLog($mid, $step, $host, $receiver, $stored_request, $via);

if ($msg->event=="INBOX") { 
    $text_temp  = $msg->text;  
    $get_method = explode('.', $text_temp);
    $response = new StdClass(); 
    $method_request = $get_method[0];

    switch ($method_request) {
        //DONE
        case "balance":
        $result = balance($text_temp, $from_phone);
        $response->apiwha_autoreply = $result;
        echo json_encode($response);
        break;
    case "harga":
        //DONE
        $result = harga($text_temp, $from_phone);
        $response->apiwha_autoreply = $result;
        echo json_encode($response);
        break;
    case "inq":
        echo inq($text_temp, $from_phone);
        break;
    case "pay":
        echo pay($text_temp, $from_phone);
        break;
    case "paydetail":
        echo pay_detail($text_temp, $from_phone);
        break;
    case "pulsa":
        $result = pulsa($text_temp, $from_phone);
        $response->apiwha_autoreply = $result;
        echo json_encode($response);
        break;
    case "game":
        //DONE
        $result = game($text_temp, $from_phone);
        $response->apiwha_autoreply = $result;
        echo json_encode($response);
        break;
    case "bpjsinq":
        echo bpjs_inq($text_temp, $from_phone);
        break;
    case "bpjspay":
        echo bpjs_pay($text_temp, $from_phone);
        break;
    case "cu":
        echo cetak_ulang($text_temp, $from_phone);
        break;
    case "cudetail":
        echo cetak_ulang_detail($text_temp, $from_phone);
        break;
    case "info_produk":
        $result = info_produk($text_temp, $from_phone);
        $response->apiwha_autoreply = $result;
        echo json_encode($response);
        break;
    case "datatransaksi":
        echo data_transaksi($text_temp, $from_phone);
        break;
    case "cekstatus":
        echo cekstatus($text_temp, $from_phone);
        break;
    case "rk":
        $result = rk($text_temp, $from_phone);
        $response->apiwha_autoreply = $result;
        echo json_encode($response);
        break;
    default :
        $response->apiwha_autoreply = "Format yang anda masukkan salah";
        echo json_encode($response);
    }  
}elseif ($msg->event=="MESSAGEPROCESSED") { 
    echo json_encode(array('apiwha_autoreply'=>'aa'));
  /* Here, you can do whatever you want */ 

}elseif ($msg->event=="MESSAGEFAILED") { 
    json_encode(array('apiwha_autoreply'=>'bb'));
  /* Here, you can do whatever you want */ 
} 

function rk($req, $phone){
    $req        = explode('.', $req);
    $id_outlet  = strtoupper($req[1]);
    $pin        = strtoupper($req[2]);
    $hari       = strtoupper($req[3]);
    $field      = 4;
    global $pgsql;
    global $host;
    if(count($req) !== $field){
        return 'missing parameter request';
    }

    if(!isValidPhone($id_outlet, $phone)){
        return 'no hp tidak terdaftar';
    }

    if(!checkpin($id_outlet, $pin)){
        return 'pin yang anda masukkan salah';
    }

    $cekpinprev = checkPinPrev($id_outlet);
    if($cekpinprev != ""){
        return $cekpinprev;
    }

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "REQUESTKEY";
    $msg[$i+=1] = "REQUESTKEY";
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');
    $msg[$i+=1] = $GLOBALS["via"];//VIA
    $msg[$i+=1] = strtoupper($id_outlet);
    $msg[$i+=1] = $hari;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";

    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];

    $respon = postValue($fm);
    $resp = $respon[7];

    $responseArr = explode("*", $resp);
    if($responseArr[7] == "SUCCESS"){
        return "Silahkan cek sms hp anda";
    } else {
        return "responseArr[8] ==> ".$responseArr[8];
    }
}

function info_produk($req, $phone){
    $i          = -1;
    $req        = explode('.', $req);
    $id_produk  = strtoupper($req[1]);
    $id_outlet  = strtoupper($req[2]);
    $pin        = strtoupper($req[3]);
    $field      = 4;

    if(count($req) !== $field){
        return 'missing parameter request';
    }

    if(!isValidPhone($idoutlet, $phone)){
        return 'no hp tidak terdaftar';
    }

    if (outletexists($id_outlet)) {
        $next = TRUE;
    } else {
        $params = array(
            "KODE_PRODUK"       => (string)$id_produk,
            "UID"               => (string)$id_outlet,
            "PIN"               => (string)'------',
            "STATUS"            => (string)"01",
            "KET"               => (string)'ID Outlet tidak terdaftar atau tidak aktif',
            "HARGA"             => '',
            "ADMIN"             => '',
            "KOMISI"            => '',
            "STATUS_PRODUK"     => '' );
        $next = FALSE;
        return implode('.', $params);
        // return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    if ($next) {
        if(checkpin($id_outlet, $pin)){
            $next = TRUE;
        } else {
            $params = array(
                "KODE_PRODUK"       => (string)$id_produk,
                "UID"               => (string)$id_outlet,
                "PIN"               => (string)'------',
                "STATUS"            => (string)"02",
                "KET"               => (string)'Pin yang Anda masukkan salah',
                "HARGA"             => '',
                "ADMIN"             => '',
                "KOMISI"            => '',
                "STATUS_PRODUK"     => '');
            $next = FALSE;
            return implode('.', $params);
        }
    }

    if($next){
        if(productexists($id_produk)){
            $next = TRUE;
        } else {
            $params = array(
                "KODE_PRODUK"       => (string)$id_produk,
                "UID"               => (string)$id_outlet,
                "PIN"               => (string)'------',
                "STATUS"            => (string)"03",
                "KET"               => (string)'Produk Tidak Tersedia',
                "HARGA"             => '',
                "ADMIN"             => '',
                "KOMISI"            => '',
                "STATUS_PRODUK"     => '');
            $next = FALSE;
            return implode('.', $params);
        }
        
    } 

    if($next){
        $for_rj         = for_rj($id_produk);
        $status_produk  = status_produk($id_produk);
        $komisi_produk  = komisi_produk($id_outlet, $id_produk);
        $status_produk  = explode('|', $status_produk);
        $komisi_produk  = explode('|', $komisi_produk);
        $harga_jual     = $status_produk[0];
        if($for_rj){
            if($status_produk[3] === '1' && $status_produk[1] === '0' ){
                $status = "AKTIF";
            } else {
                $status = "GANGGUAN";
            }
        } else {
            $status = "CLOSE";
        }
        $admin      = $status_produk[2];
        $up_harga   = $komisi_produk[0];
        $fee        = $komisi_produk[1];
        $komisi     = abs($up_harga) + abs($fee);
        $params     = array(
            "KODE_PRODUK"       => (string)$id_produk,
            "UID"               => (string)$id_outlet,
            "PIN"               => (string)'------',
            "STATUS"            => (string)"00",
            "KET"               => (string)'SUKSES',
            "HARGA"             => (string)$harga_jual,
            "ADMIN"             => (string)$admin,
            "KOMISI"            => (string)$komisi,
            "STATUS_PRODUK"     => (string)$status
        );
        return implode('.', $params);
    }
}

function pulsa($req, $phone){
    $req        = explode('.', $req);
    $kdproduk   = strtoupper($req[1]);
    $nohp       = strtoupper($req[2]);
    $idoutlet   = strtoupper($req[3]);
    $pin        = strtoupper($req[4]);
    $ref1       = "";
    $field      = 5;
    global $pgsql;
    global $host;
    if(count($req) !== $field){
        return 'missing parameter request';
    }

    if(!isValidPhone($idoutlet, $phone)){
        return 'no hp tidak terdaftar';
    }

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = "PULSA";
    $msg[$i+=1] = $kdproduk;      //KDPRODUK
    $msg[$i+=1] = $GLOBALS["mid"];     //MID
    $msg[$i+=1] = $stp;        //STEP
    $msg[$i+=1] = "";        //TANGGAL
    $msg[$i+=1] = $GLOBALS["via"];     //VIA
    $msg[$i+=1] = $nohp;       //NOHP
    $msg[$i+=1] = "";        //NOMINAL
    $msg[$i+=1] = strtoupper($idoutlet);   //IDOUTLET
    $msg[$i+=1] = $pin;        //PIN
    $msg[$i+=1] = "";        //TOKEN
    $msg[$i+=1] = "";        //FIELD_KODE_PRODUK_BILLER
    $msg[$i+=1] = "";        //FIELD_SN
    $msg[$i+=1] = "";        //FIELD_BALANCE
    $msg[$i+=1] = "";        //FIELD_TRX_ID
    $msg[$i+=1] = $ref1;        //FIELD_STATUS
    $msg[$i+=1] = "";        //FIELD_KETERANGAN

    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA89065', 'FA32670');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "PULSA*$kdproduk*9999*7*".date('YmdHis')."*H2H*$nohp**".$idoutlet."*".$pin."******XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h."; 
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        $respon = postValue($fm);
        $resp = $respon[7];
    }
    
    $format = FormatMsg::pulsa();
    $frm = new FormatPulsa($format[1], $resp);

    $r_step             = $frm->getStep()+1;
    $r_mid              = $frm->getMid();
    $r_kdproduk         = $frm->getKodeProduk();
    $r_tanggal          = $frm->getTanggal();
    $r_nohp             = $frm->getNohp();
    $r_idoutlet         = $frm->getMember();
    $r_pin              = $frm->getPin();
    $r_idtrx            = $frm->getIdTrx();
    $r_status           = $frm->getStatus();
    $r_nominal          = $frm->getNominal();
    $r_keterangan       = $frm->getKeterangan();
    $r_sn               = $frm->getSN();
    $r_sisa_saldo       = $frm->getSaldo();
    $r_nominal          = $frm->getNominal();
    $nom_up             = getnominalup($r_idtrx);
    $r_saldo_terpotong  = $r_nominal + ($nom_up);

    if($r_status == ''){
        $r_status = '00';
    }
    
    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk, //I5H
        "WAKTU"             => (string) $r_tanggal, //20190213090308
        "NO_HP"             => (string) $r_nohp, //085648889293
        "UID"               => (string) $r_idoutlet, //FA9919
        "SN"                => (string) $r_sn,
        "NOMINAL"           => (string) $r_nominal, //5900
        "REF1"              => (string) $ref1, 
        "REF2"              => (string) $r_idtrx, //1271426615
        "STATUS"            => (string) $r_status, //17
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)), //TRX pulsa S5H 085648889293 GAGAL
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong, //0
        "SISA_SALDO"        => (string) $r_sisa_saldo //70060
    );

    $implode = implode('.', $params);
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $GLOBALS["via"]);
    return $implode;
}

function game(){
    $req        = explode('.', $req);
    $kdproduk   = strtoupper($req[1]);
    $nohp       = strtoupper($req[2]);
    $idoutlet   = strtoupper($req[3]);
    $pin        = strtoupper($req[4]);
    $ref1       = "";
    $field      = 5;
    global $pgsql;
    global $host;
    if(count($req) !== $field){
        return 'missing parameter request';
    }

    if(!isValidPhone($idoutlet, $phone)){
        return 'no hp tidak terdaftar';
    }

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = "GAME";
    $msg[$i+=1] = $kdproduk;      //KDPRODUK
    $msg[$i+=1] = $GLOBALS["mid"];     //MID
    $msg[$i+=1] = $stp;        //STEP
    $msg[$i+=1] = "";        //TANGGAL
    $msg[$i+=1] = $GLOBALS["via"];     //VIA
    $msg[$i+=1] = $nohp;       //NOHP
    $msg[$i+=1] = "";        //NOMINAL
    $msg[$i+=1] = strtoupper($idoutlet);   //IDOUTLET
    $msg[$i+=1] = $pin;        //PIN
    $msg[$i+=1] = "";        //TOKEN
    $msg[$i+=1] = "";        //FIELD_KODE_PRODUK_BILLER
    $msg[$i+=1] = "";        //FIELD_SN
    $msg[$i+=1] = "";        //FIELD_BALANCE
    $msg[$i+=1] = "";        //FIELD_TRX_ID
    $msg[$i+=1] = $ref1;        //FIELD_STATUS
    $msg[$i+=1] = "";        //FIELD_KETERANGAN

    $fm         = convertFM($msg, "*");
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA89065', 'FA32670');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "PULSA*$kdproduk*9999*7*".date('YmdHis')."*H2H*$nohp**".$idoutlet."*".$pin."******XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h."; 
        } else {
            $respon = postValue($fm);
            $resp   = $respon[7];
        }
    } else {
        $respon = postValue($fm);
        $resp   = $respon[7];
    }

    $format = FormatMsg::game();
    $frm    = new FormatGame($format[1], $resp);

    $r_step             = $frm->getStep()+1;
    $r_mid              = $frm->getMid();
    $r_kdproduk         = $frm->getKodeProduk();
    $r_tanggal          = $frm->getTanggal();
    $r_nohp             = $frm->getNohp();
    $r_idoutlet         = $frm->getMember();
    $r_pin              = $frm->getPin();
    $r_idtrx            = $frm->getIdTrx();
    $r_status           = $frm->getStatus();
    $r_nominal          = $frm->getNominal();
    $r_keterangan       = $frm->getKeterangan();
    $r_sn               = $frm->getSN();
    $r_nominal          = getNominalTransaksi($r_idtrx);
    $nom_up             = getnominalup($r_idtrx);
    $r_saldo_terpotong  = $r_nominal + ($nom_up);
    $r_sisa_saldo       = $frm->getSaldo();

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "NO_HP"             => (string) $r_nohp,
        "UID"               => (string) $r_idoutlet,
        "SN"                => (string) $r_sn,
        "NOMINAL"           => (string) $r_nominal,
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo
    );


    $implode = implode('.', $params);
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $GLOBALS["via"]);

    return $implode;
}

function harga($req, $phone){
    $req    = explode('.', $req);
    $produk = strtoupper($req[1]);
    $uid    = strtoupper($req[2]);
    $pin    = strtoupper($req[3]);
    $field  = 4;
    if(count($req) !== $field){
        return 'missing parameter request';
    }

    if(!isValidPhone($uid, $phone)){
        return 'no hp tidak terdaftar';
    }

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = "HARGA";
    $msg[$i+=1] = "HARGA";      //KDPRODUK
    $msg[$i+=1] = $GLOBALS["mid"];     //MID
    $msg[$i+=1] = $stp;        //STEP
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];
    $msg[$i+=1] = $produk;
    $msg[$i+=1] = $uid;
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";

    $fm             = convertFM($msg, "*");
    $sender         = $GLOBALS["__G_module_name"];
    $receiver       = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    
    $respon         = postValue($fm);
    $resp           = $respon[7];

    $format         = FormatMsg::cekHarga();
    $frm            = new FormatCekHarga($format[1], $resp);

    $r_idoutlet     = $frm->getMember();
    $r_pin          = $frm->getPin();
    $r_balance      = $frm->getSaldo();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();

    if (strpos($r_keterangan, '&#9;') !== false) {
        $r_keterangan = str_replace('&#9;&#9;', " ", $r_keterangan);
        $r_keterangan = str_replace('	', "", $r_keterangan);
    }

    $params = array(
        "UID"       => $r_idoutlet,
        "SALDO"     => $r_balance,
        "STATUS"    => $r_status,
        "KET"       => str_replace(',;', "\n\n", $r_keterangan)
    );

    return implode('.', $params);
}

function balance($req, $phone){
    $req = explode('.', $req);
    $uid        = strtoupper($req[1]);
    $pin        = strtoupper($req[2]);
    $field      = 3;
    if(count($req) !== $field){
        return 'missing parameter request';
    }

    if(!isValidPhone($uid, $phone)){
        return 'no hp tidak terdaftar';
    }
    
    $stp    = $GLOBALS["step"] + 1;
    $msg    = array();
    $i      = -1;

    $msg[$i+=1] = "SAL";
    $msg[$i+=1] = "SAL";      //KDPRODUK
    $msg[$i+=1] = $GLOBALS["mid"];     //MID
    $msg[$i+=1] = $stp;        //STEP
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];
    $msg[$i+=1] = $uid;
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";

    $fm         = convertFM($msg, "*");
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];

    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);


    $respon     = postValue($fm);
    $resp       = $respon[7];

    $format     = FormatMsg::cekSaldo();
    $frm        = new FormatCekSaldo($format[1], $resp);

    $r_idoutlet     = $frm->getMember();
    $r_pin          = $frm->getPin();
    $r_balance      = $frm->getSaldo();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();

    $params = array(
        "UID"       => $r_idoutlet,
        "SALDO"     => $r_status == '00' ? $r_balance : '',
        "STATUS"    => $r_status,
        "KET"       => trim(str_replace('.', '', $r_keterangan))
    );
    $implode = implode('.', $params);
    return $implode;
}