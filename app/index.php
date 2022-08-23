<?php
error_reporting(1);
date_default_timezone_set('Asia/Jakarta');
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
require_once("include/format_message/FormatCekHarga.class.php");
require_once("include/format_message/FormatPgn.class.php");
require_once("include/format_message/FormatLakupandai.class.php");

//koneksi ke database postgre
global $limit;

// Untuk mengoptimalkan jumlah client, koneksi ke database postgre berada di masing-masing function dengan nama : reconnect() & reconect_ro() >> CTO
//$pgsql = pg_connect("host=" . $__CFG_dbhost . " port=" . $__CFG_dbport . " dbname=" . $__CFG_dbname . " user=" . $__CFG_dbuser . " password=" . $__CFG_dbpass);

$msg = $HTTP_RAW_POST_DATA;
$host = getClientIP();//$_SERVER['REMOTE_ADDR'];
$mid = getNextMID();
// $mid = 1; //buat local
$step = 1;
$sender = "XML CLIENT";
//$receiver = $GLOBALS["__G_module_name"];
$receiver           = $_SERVER['SERVER_ADDR']."-RB-XML-".$_SERVER['HTTP_HOST']."-".$_SERVER['SERVER_NAME'];

$via = $GLOBALS["__G_via"];

$plain = explode("<param>", $HTTP_RAW_POST_DATA);
$tag1 = "<string>";
$tag2 = "</string>";
$postag1 = strpos($plain[6], $tag1);
$postag2 = strpos($plain[6], $tag2);
$pinplain = substr($plain[6], ($postag1 + strlen($tag1)), ($postag2 - ($postag1 + strlen($tag1))));
$msg_log = str_replace($pinplain, "------", $msg);
writeLog($mid, $step, $host, $receiver, $msg_log, $via);

$ips = getClientIP();
appendfile(date('Y:m:d H:i:s').' Request Xml '.$ips.'= '. $msg_log);
appendfilehit(date('Y:m:d H:i:s').'-'.$receiver.' Request Xml '.$ips.'= '. $msg_log, 'Xml');

$cekipmitra = cekIPmitra($ips);
if($cekipmitra == true){
    echo "Ip Tidak terdaftar";die();
}

if ($HTTP_RAW_POST_DATA == "") {
    echo "IP Anda :: " . getClientIP();//$_SERVER['REMOTE_ADDR'];
} else {
    $s = new xmlrpc_server(
            array(
        "rajabiller.inq" => array("function" => "inq"),
        "rajabiller.beli" => array("function" => "beli"),
        "rajabiller.pay" => array("function" => "pay"),
        "rajabiller.transferinq" => array("function" => "transferinq"),
        "rajabiller.transferpay" => array("function" => "transferpay"),
        "rajabiller.paydetail" => array("function" => "payDetail"),
        "rajabiller.pulsa" => array("function" => "pulsa"),
        "rajabiller.game" => array("function" => "game"),
        "rajabiller.gantipin" => array("function" => "gantipin"),
        "rajabiller.balance" => array("function" => "balance"),
        "rajabiller.datatransaksi" => array("function" => "datatransaksi"),
        "rajabiller.cu" => array("function" => "cetakUlang"),
        "rajabiller.cudetail" => array("function" => "cetakUlangDetail"),
        "rajabiller.inq_equity" => array("function" => "inqEquity"),
        "rajabiller.pay_equity" => array("function" => "payEquity"),
        "rajabiller.inq_bintang" => array("function" => "inqBintang"),
        "rajabiller.pay_bintang" => array("function" => "payBintang"),
        "rajabiller.inq_taspen" => array("function" => "inqTaspen"),
        "rajabiller.pay_taspen" => array("function" => "payTaspen"),
        "rajabiller.inq_rumah_zakat" => array("function" => "inqRumahZakat"),
        "rajabiller.pay_rumah_zakat" => array("function" => "payRumahZakat"),
        "rajabiller.inq_city" => array("function" => "inquerycity"),
        "rajabiller.inq_schedule" => array("function" => "inqueryschedule"),
        "rajabiller.flightbook" => array("function" => "flightbooking"),
        "rajabiller.flightpay" => array("function" => "flightpayment"),
        "rajabiller.flightprice" => array("function" => "flightgetprice"),
        "rajabiller.cekip" => array("function" => "cekip"),
        "rajabiller.harga" => array("function" => "harga"),
        "rajabiller.bpjsinq" => array("function" => "bpjsinq"),
        "rajabiller.bpjspay" => array("function" => "bpjspay"),
        "rajabiller.cekjabberstatus" => array("function" => "cekjabberstatus"),
        "rajabiller.cekid" => array("function" => "cekid"),
        "rajabiller.cekkey" => array("function" => "cekkey"),
        "rajabiller.get_email" => array("function" => "get_email"),
        "rajabiller.inqpln" => array("function" => "inqpln"),
        "rajabiller.paypln" => array("function" => "paypln"),
        "rajabiller.cekharga_gp" => array("function" => "cek_harga2"),
        "rajabiller.datatransaksi2" => array("function" => "data_transaksi"),
        "rajabiller.info_produk" => array("function" => "info_produk")
            ), false);

    $s->setdebug(0);
    $s->compress_response = false;

    // out-of-band information: let the client manipulate the server operations.
    // we do this to help the testsuite script: do not reproduce in production!
    //if (isset($_GET['RESPONSE_ENCODING'])) $s->response_charset_encoding = $_GET['RESPONSE_ENCODING'];

    $s->service();
}

function get_email($m){
    // die('ccc');
    $i = -1;
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $data = trim(getemailmember($idoutlet));

    return new xmlrpcresp(php_xmlrpc_encode($data));
}


function cek_harga2($m)
{
    $result = array();
    $next   = FALSE;
    $end    = FALSE;
    
    $i = -1;
    $group      = strtoupper($m->getParam($i+=1)->scalarval());;
    $produk     = strtoupper($m->getParam($i+=1)->scalarval());;
    $idoutlet   = strtoupper($m->getParam($i+=1)->scalarval());;
    $pin        = strtoupper($m->getParam($i+=1)->scalarval());;

    // die('a');
    // echo $group."".$id_produk;
    // echo $idoutlet."".$pin;
   
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            $params = array(
                "Status" => "xx",
                "Keterangan" => "IP Anda [$ip] tidak punya hak akses"
            );
            return new xmlrpcresp(php_xmlrpc_encode($params));
        }
    }

    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
         $params = array(
                "Status" => "xx",
                "Keterangan" => "Anda tidak punya hak akses"
            );
            return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    if (outletexists($idoutlet)) {
        $next = TRUE;
    } else {
        $rc     = "01";
        $ket    = "ID Outlet tidak terdaftar atau tidak aktif";
        $next   = FALSE;
        $params = array(
            "UID"       => $idoutlet,
            "PIN"       => '------',
            "STATUS"    => $rc,
            "KET"       => $ket,
            "DATA"      => $result 
        );
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }


    if ($next) {
        if (checkpin($idoutlet, $pin)) {
            $next = TRUE;
        } else {
        // die('a1');
            $rc     = "02";
            $ket    = "Pin yang Anda masukkan salah";
            $params = array(
                "UID"       => $idoutlet,
                "PIN"       => '------',
                "STATUS"    => $rc,
                "KET"       => $ket,
                "DATA"      => $result 
            );
            return new xmlrpcresp(php_xmlrpc_encode($params));
        }
    }

    if($next){
        // die('a2');
        $foruse = foruse2($group,$produk, $idoutlet);
        if(is_array($foruse)){
            $msgcontent = array();
            for($i = 0; $i < count($foruse); $i++){
                $datas = array();

                if($foruse[$i]->is_active == '1' && $foruse[$i]->is_gangguan == '0'){
                    $status = 'AKTIF';
                } else {
                    $status = 'GANGGUAN';
                }
                $komisi  = abs($foruse[$i]->up_harga) + abs($foruse[$i]->fee_transaksi);
                $dt[] = array(
                    (string) $foruse[$i]->id_produk,
                    (string) $foruse[$i]->produk,
                    (string) $foruse[$i]->harga_jual,
                    (string) $foruse[$i]->biaya_admin,
                    (string) $status
                );
            }
           return new xmlrpcresp(php_xmlrpc_encode($dt));
        } else {
            $rc     = "03";
            $ket    = "Data tidak ditemukan";
            $params = array(
                "UID"       => $idoutlet,
                "PIN"       => '------',
                "STATUS"    => $rc,
                "KET"       => $ket,
                "DATA"      => $result 
            );
           return new xmlrpcresp(php_xmlrpc_encode($params));
        }
    }
}

function inqpln($m){
    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel2 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel3 = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            die("IP Anda [$ip] tidak punya hak akses");
        }
    }

    

    if($kdproduk == 'PLNPRA'){
        $kdproduk = 'PLNPRAH';
    } else if($kdproduk == 'PLNPASC'){
        $kdproduk = 'PLNPASCH';
    }

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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra ";
    } else {
        $respon = postValue($fm);
        $resp = $respon[7];
    }
    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $frm = getParseProduk($kdproduk, $resp);


    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step         = $frm->getStep()+1;
    $r_kdproduk     = $frm->getKodeProduk();
    $r_tanggal      = $frm->getTanggal();
    $r_idpel1       = $frm->getIdPel1();
    $r_idpel2       = $frm->getIdPel2();
    $r_idpel3       = $frm->getIdPel3();
    $r_nominal      = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet     = $frm->getMember();
    $r_pin          = $frm->getPin();
    $r_sisa_saldo   = $frm->getSaldo();
    $r_idtrx        = $frm->getIdTrx();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();
    //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    
    
    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }
    if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids())){
        $getdatapln   = getdatapln($kdproduk, $frm);
        $tarif = $getdatapln['tarif'];
        $daya = $getdatapln['daya'];
        $ref = $getdatapln['ref'];
        $stanawal = $getdatapln['stanawal'];
        $stanakhir = $getdatapln['stanakhir'];
        $infoteks = $getdatapln['infoteks'];
        $jmlbulan = $getdatapln['jml_bulan'];

        $params = array(
            (string) $r_kdproduk,
            (string) $r_tanggal,
            (string) $r_idpel1,
            (string) $r_idpel2,
            (string) $r_idpel3,
            (string) $r_nama_pelanggan,
            (string) $r_periode_tagihan,
            (string) $jmlbulan,
            (string) $r_nominal,
            (string) $tarif,
            (string) $daya,
            (string) $ref,
            (string) $stanawal,
            (string) $stanakhir,
            (string) $infoteks,
            (string) $r_nominaladmin,
            (string) $r_idoutlet,
            (string) '------',
            (string) $ref1,
            (string) $r_idtrx,
            "0",
            (string) $r_status,
            (string) $r_keterangan,
            (string) $r_saldo_terpotong,
            (string) $r_sisa_saldo,
            (string) $url_struk
        );
    } else {
        $getdatapln   = getdatapln($kdproduk, $frm);
        $meterai = $getdatapln['meterai'];
        $ppn = $getdatapln['ppn'];
        $tarif = $getdatapln['tarif'];
        $daya = $getdatapln['daya'];
        $ppj = $getdatapln['ppj'];
        $ref = $getdatapln['ref'];
        $angsuran = $getdatapln['angsuran'];
        $pp = $getdatapln['pp'];
        $kwh = $getdatapln['kwh'];
        $nomortoken = $getdatapln['nomortoken'];
        $infoteks = $getdatapln['infoteks'];

        $params = array(
            (string) $r_kdproduk,
            (string) $r_tanggal,
            (string) $r_idpel1,
            (string) $r_idpel2,
            (string) $r_idpel3,
            (string) $r_nama_pelanggan,
            (string) $r_nominal,
            (string) $tarif,
            (string) $daya,
            (string) $ref,
            (string) $meterai,
            (string) $ppn,
            (string) $ppj,
            (string) $angsuran,
            (string) $pp,
            (string) $kwh,
            (string) $nomortoken,
            (string) $infoteks,
            (string) $r_nominaladmin,
            (string) $r_idoutlet,
            (string) '------',
            (string) $ref1,
            (string) $r_idtrx,
            "0",
            (string) $r_status,
            (string) $r_keterangan,
            (string) $r_saldo_terpotong,
            (string) $r_sisa_saldo,
            (string) $url_struk
        );
    }
    

    //insert into fmss_message
    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    // $text = inq_resp_text($params);
    $get_mid = get_mid_from_idtrx($r_idtrx);
    $get_step = get_step_from_mid($get_mid) + 1;
    // writeLog($get_mid, $get_step, $host, $receiver, json_encode($params), $via);
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function paypln($m){
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            // $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
            die("IP Anda [$ip] tidak punya hak akses");
        }
    }
    

    if($kdproduk == 'PLNPRA'){
        $kdproduk = 'PLNPRAH';
    } else if($kdproduk == 'PLNPASC'){
        $kdproduk = 'PLNPASCH';
    }

    $ceknom = getNominalTransaksi(trim($ref2)); //tambahan
    // $cektoken = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal), trim($kdproduk), trim($idoutlet)); //tambahan

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "BAYAR";
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
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ref2;
    $msg[$i+=1] = $ref1;
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra ";
    } else if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else {
            //$respon = postValueWithTimeOutDevel($fm);
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        // if ($cektoken[0] != '-') {
        //     $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*Sudah Pernah Terjadi status sukses di ID Outlet: " . $idoutlet . ", Nominal:" . $nominal . ", IDPEL: " . $cektoken[0] . ", dan Token:" . $cektoken[3] . "*********************" . $cektoken[3] . "*************";
        // } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        // }
    }
    
    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $frm = getParseProduk($kdproduk, $resp);


    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step         = $frm->getStep()+1;
    $r_kdproduk     = $frm->getKodeProduk();
    $r_tanggal      = $frm->getTanggal();
    $r_idpel1       = $frm->getIdPel1();
    $r_idpel2       = $frm->getIdPel2();
    $r_idpel3       = $frm->getIdPel3();
    $r_nominal      = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet     = $frm->getMember();
    $r_pin          = $frm->getPin();
    $r_sisa_saldo   = $frm->getSaldo();
    $r_idtrx        = $frm->getIdTrx();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();
    //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    
    
    $url_struk = "";
    if ($frm->getStatus() == "00") {
        $nom_up = getnominalup($r_idtrx);
        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
        $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
    }
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }
    if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids())){
        $getdatapln   = getdatapln($kdproduk, $frm,1);
        $tarif = $getdatapln['tarif'];
        $daya = $getdatapln['daya'];
        $ref = $getdatapln['ref'];
        $stanawal = $getdatapln['stanawal'];
        $stanakhir = $getdatapln['stanakhir'];
        $infoteks = $getdatapln['infoteks'];
        $jmlbulan = $getdatapln['jml_bulan'];

        $params = array(
            (string) $r_kdproduk,
            (string) $r_tanggal,
            (string) $r_idpel1,
            (string) $r_idpel2,
            (string) $r_idpel3,
            (string) $r_nama_pelanggan,
            (string) $r_periode_tagihan,
            (string) $jmlbulan,
            (string) $r_nominal,
            (string) $tarif,
            (string) $daya,
            (string) $ref,
            (string) $stanawal,
            (string) $stanakhir,
            (string) $infoteks,
            (string) $r_nominaladmin,
            (string) $r_idoutlet,
            (string) '------',
            (string) $ref1,
            (string) $r_idtrx,
            "0",
            (string) $r_status,
            (string) $r_keterangan,
            (string) $r_saldo_terpotong,
            (string) $r_sisa_saldo,
            (string) $url_struk
        );
    } else {
        $getdatapln   = getdatapln($kdproduk, $frm,1);
        $meterai = $getdatapln['meterai'];
        $ppn = $getdatapln['ppn'];
        $tarif = $getdatapln['tarif'];
        $daya = $getdatapln['daya'];
        $ppj = $getdatapln['ppj'];
        $ref = $getdatapln['ref'];
        $angsuran = $getdatapln['angsuran'];
        $pp = $getdatapln['pp'];
        $kwh = $getdatapln['kwh'];
        $nomortoken = $getdatapln['nomortoken'];
        $infoteks = $getdatapln['infoteks'];

        $params = array(
            (string) $r_kdproduk,
            (string) $r_tanggal,
            (string) $r_idpel1,
            (string) $r_idpel2,
            (string) $r_idpel3,
            (string) $r_nama_pelanggan,
            (string) $r_nominal,
            (string) $tarif,
            (string) $daya,
            (string) $ref,
            (string) $meterai,
            (string) $ppn,
            (string) $ppj,
            (string) $angsuran,
            (string) $pp,
            (string) $kwh,
            (string) $nomortoken,
            (string) $infoteks,
            (string) $r_nominaladmin,
            (string) $r_idoutlet,
            (string) '------',
            (string) $ref1,
            (string) $r_idtrx,
            "0",
            (string) $r_status,
            (string) $r_keterangan,
            (string) $r_saldo_terpotong,
            (string) $r_sisa_saldo,
            (string) $url_struk
        );
    }
    

    //insert into fmss_message
    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    // $text = inq_resp_text($params);
    $get_mid = get_mid_from_idtrx($r_idtrx);
    $get_step = get_step_from_mid($get_mid) + 1;
    // writeLog($get_mid, $get_step, $host, $receiver, json_encode($params), $via);
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function cekid($m){
    // die('ccc');
    $i = -1;
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());

    $data = checkpin($idoutlet, $pin);
    if($data == 0){
        $a = '0';
    } else {
        $a = '1';
    }

    return new xmlrpcresp(php_xmlrpc_encode($a));
}

function cekkey($m){
    // die('ccc');
    $i = -1;
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $key = strtoupper($m->getParam($i+=1)->scalarval());

    $data = signon_check_key($idoutlet, $key);
    if($data == 0){
        $a = '0';
    } else {
        $a = '1';
    }
    return new xmlrpcresp(php_xmlrpc_encode($a));
}

function info_produk($m){
    $i          = -1;

    $id_produk  = strtoupper($m->getParam($i+=1)->scalarval());
    $id_outlet  = strtoupper($m->getParam($i+=1)->scalarval());
    $pin        = strtoupper($m->getParam($i+=1)->scalarval());
    
     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }
    if (outletexists($id_outlet)) {
        $next = TRUE;
    } else {
        $params = array((string)$id_produk, (string)$id_outlet, (string)$pin, (string)"01", (string)'ID Outlet tidak terdaftar atau tidak aktif', '','','','' );
        $next = FALSE;
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    if ($next) {
        if(checkpin($id_outlet, $pin)){
            $next = TRUE;
        } else {
            $params = array( (string)$id_produk, (string)$id_outlet, (string)$pin, (string)"02", (string)'Pin yang Anda masukkan salah', '','','','' );
            $next = FALSE;
            return new xmlrpcresp(php_xmlrpc_encode($params));
        }
    }

    if($next){
        if(productexists($id_produk)){
            $next = TRUE;
        } else {
            $params = array((string)$id_produk, (string)$id_outlet, (string)$pin, (string)"03", (string)'Produk Tidak Tersedia', '','','','' );
            $next = FALSE;
            return new xmlrpcresp(php_xmlrpc_encode($params));
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
        $admin = $status_produk[2];
        $produk = $status_produk[4];
        $up_harga = $komisi_produk[0];
        $fee = $komisi_produk[1];
        $komisi = abs($up_harga) + abs($fee);
        $params = array((string)$id_produk, (string)$id_outlet, (string)$pin, (string)"00", (string)'SUKSES', (string)$harga_jual, (string)$admin, (string)$komisi, (string)$produk, (string)$status);
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }
}

function cekjabberstatus(){
    $data = gerdatajabber();
    return new xmlrpcresp(php_xmlrpc_encode($data));
}
function cekip() {
    return new xmlrpcresp(php_xmlrpc_encode($_SERVER['REMOTE_ADDR']));
}
function transferinq($m)
{
      $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel2 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel3 = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());
    $kodebank = strtoupper($m->getParam($i+=1)->scalarval());
    $nomorhp = strtoupper($m->getParam($i+=1)->scalarval());

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        // echo (!isValidIP($idoutlet, $ip));
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nidoutlet: $idoutlet\npin: -----\nref1: $ref1";
            // die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].\nBerikut inputan anda\n".htmlspecialchars_decode(htmlspecialchars_decode($request_error)));
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "]");
        }
    }
    

      $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "TAGIHAN";
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    // $msg[$i+=1] = "4089232891";
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');
    // $msg[$i+=1] = "MOBILE_SMART";           //VIA
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";   //  FIELD_BALANCE
    $msg[$i+=1] = ""; // FIELD_JENIS_STRUK
    $msg[$i+=1] = ""; // FIELD_KODE_BANK
    $msg[$i+=1] = ""; //FIELD_KODE_PRODUK_BILLER
    $msg[$i+=1] = ""; // FIELD_TRX_ID
    $msg[$i+=1] = ""; // FIELD_STATUS
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME']; // FIELD_KETERANGAN
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] =  $kodebank;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ref1;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $fm = convertFM($msg, "*");
     // echo "fm = ".$fm."<br>";die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
    if($kdproduk == 'ASRBPJSKS'){
        $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
    } else {
         $respon = postValue_fmssweb2($fm);
         $resp = $respon[7];
    }    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $frm = getParseProduk($kdproduk, $resp);


    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step         = $frm->getStep()+1;
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
    $r_mid          = $frm->getMID();
    //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;
    // $r_additional_datas = getAdditionalDatas($kdproduk, $frm);
    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
    $r_id_pelanggan = getIdPelanggan($kdproduk, $frm);
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);

    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
   
     $r_nama_bank = getnamabank($kodebank);

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }
    /* $params = array(
      $_SERVER['REMOTE_ADDR'],
      'ANDA TIDAK DIIJINKAN MELAKUKAN INQUIRY'
      ); */

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_id_pelanggan, (string) $r_nama_pelanggan, (string) $r_nama_bank, (string) $kodebank, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, '------', (string) $ref1, (string) $r_idtrx, "0", (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );
    // if (count($r_additional_datas) > 0) {
    //     array_push($params, $r_additional_datas);
    // }
    //insert into fmss_message
    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    //$db = new Database();
    //$q_ins_log = "INSERT INTO fmss_message (id_transaksi, id_transaksi_partner, mid, content, insert_datetime) 
    //              VALUES (".$id_transaksi.", ".$id_transaksi_partner.", ".$mid.", '".replace_forbidden_chars_msg($resp)."', NOW())";
    //$e_ins_log = mysql_query($q_ins_log, $db->getConnection());

    $text = inq_resp_text($params);
    // $get_mid = get_mid_from_idtrx($r_idtrx);
    // $get_step = get_step_from_mid($get_mid) + 1;
    writeLog($r_mid, $r_step, $host, $receiver, $text, $via);
    return new xmlrpcresp(php_xmlrpc_encode($params));
}
function transferpay($m)
{
    global $limit;
    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel2 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel3 = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref2 = strtoupper($m->getParam($i+=1)->scalarval());
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());
    $kodebank = strtoupper($m->getParam($i+=1)->scalarval());
    $nomorhp = strtoupper($m->getParam($i+=1)->scalarval());

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    (string) $kdproduk, 
                    (string) date('YmdHis'), 
                    (string) $idpel1, 
                    (string) $idpel2, 
                    (string) $idpel3, 
                    (string) '', 
                    (string) '', 
                    (string) $nominal, 
                    (string) '', 
                    (string) $idoutlet, 
                    (string) '------', 
                    (string) $ref1, 
                    (string) $ref2, 
                    (string) '0', 
                    (string) '77', 
                    (string) 'Transaksi gagal, silahkan coba beberapa saat lagi', 
                    (string) '', 
                    (string) '', 
                    (string) ''
                );
                appendfile('RESPON PAY FAIL XML to JSON: '.json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'XML',strtoupper("XML ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return new xmlrpcresp(php_xmlrpc_encode($params));
            }
        }
    }
    // handle 504 end

     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].\nBerikut inputan anda\n".htmlspecialchars_decode(htmlspecialchars_decode($request_error)));
        }
    }

    
    //if (!checkHakAksesMP("", strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
   
    if($ref2 == ""){
        $ref2 = 0;
    }

    $ceknom = getNominalTransaksi(trim($ref2)); //tambahan
    $stp        = $GLOBALS["step"] + 1;
    $msg        = array();
    $i          = -1;
    $msg[$i+=1] = $mti;
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    // $msg[$i+=1] = "4089232891";
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');
    // $msg[$i+=1] = "MOBILE_SMART";           //VIA
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = ""; // FIELD_JENIS_STRUK
    $msg[$i+=1] = ""; // FIELD_KODE_BANK
    $msg[$i+=1] = ""; // FIELD_KODE_PRODUK_BILLER
    $msg[$i+=1] = $ref2; // FIELD_TRX_ID
    $msg[$i+=1] = ""; // FIELD_STATUS
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip; // FIELD_KETERANGAN
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $kodebank;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ref1;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $fm         = convertFM($msg, "*");
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
    /* tambahan */
    if ($ceknom != $nominal) {
        $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
    } else {
        //$respon = postValueWithTimeOutDevel($fm);
        $respon = postValue_fmssweb2($fm);
        $resp = $respon[7];
    }
    
    /* tambahan */

    if($resp == 'null'){
        
        $params = array(
            (string) $kdproduk, 
            (string) date('YmdHis'), 
            (string) $idpel1, 
            (string) $idpel2, 
            (string) $idpel3, 
            (string) '', 
            (string) '', 
            (string) $nominal, 
            (string) '', 
            (string) $idoutlet, 
            (string) '------', 
            (string) $ref1, 
            (string) '', 
            (string) '0', 
            (string) '00', 
            (string) 'SEDANG DIPROSES', 
            (string) '', 
            (string) '', 
            (string) ''
        );
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMID();
    $r_saldo_terpotong = 0;

    $r_id_pelanggan = getIdPelanggan($kdproduk, $frm);
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
  
    $r_nama_bank = getnamabank($kodebank);
    // $token2 = $cektoken[3];

    $r_reff3 = '0';


    $url_struk = "";
   if ($frm->getStatus() == "00") {

        $nom_up = getnominalup($r_idtrx);
        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
        $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;

    }else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
    }

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_id_pelanggan, (string) $r_nama_pelanggan, (string) $r_nama_bank, (string) $kodebank, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, '------', (string) $ref1, (string) $r_idtrx, "0", (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );



    $is_return = true;
    if($r_idtrx != $ref2){
        $text = pay_resp_text($params);
        // $get_mid = get_mid_from_idtrx($r_idtrx);
        // $get_step = get_step_from_mid($get_mid) + 1;
        writeLog($r_mid, $r_step, $host, $receiver, $text, $via);
    }
    
    return new xmlrpcresp(php_xmlrpc_encode($params));

}

function inq($m) {
//TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN

    $i = -1;
    $kdproduk = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel1 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel2 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel3 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idoutlet = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $pin = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $ref1 = trim(strtoupper($m->getParam($i+=1)->scalarval()));

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        // echo (!isValidIP($idoutlet, $ip));
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nidoutlet: $idoutlet\npin: -----\nref1: $ref1";
            // die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].\nBerikut inputan anda\n".htmlspecialchars_decode(htmlspecialchars_decode($request_error)));
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "]");
        }
    }
    
    if(substr($kdproduk, 0, 5) == 'PAJAK'){
        if(strlen($idpel1) != 18){
             die("Nomor Object Pajak(NOP) harus 18 digit");
        }
    }
    //if (!checkHakAksesMP("", strtoupper($idoutlet))) {
    //    die("");
    //}

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
    }

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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
    if($kdproduk == "PAJAKGRTLT" || $kdproduk == "PAJAKGRTL"){
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = $idpel1;
    }
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
    if($kdproduk == 'ASRBPJSKS'){
        $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
    } else if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009','FA32670');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        if( in_array($kdproduk, KodeProduk::getPLNPrepaids()) ){
            if($idpel1 == "" && strlen($idpel2) == 12){
                $respon = postValue($fm);
                $resp = $respon[7]; 
            } else if( (strlen($idpel1) < 11) || (strlen($idpel1) < 11 && strlen($idpel2) < 12) || ($idpel1 == "" && strlen($idpel2) < 12) || (strlen($idpel1) < 11 && $idpel2 == "") ){
                $resp = "TAGIHAN*PLNPRAH*1727174759*11*".date('YmdHis')."*H2H*$idpel1*****$idoutlet*$pin*------******XX*NOMOR METER ATAU IDPEL YANG ANDA MASUKKAN SALAH, MOHON TELITI KEMBALI**$idpel1********************************    ";
            } else {
                $respon = postValue($fm);
                $resp = $respon[7];    
            }
        } else {
             if(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
             }else{
                 $id_biller = getBiller($kdproduk);
                 if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
                   if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                        $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                 }elseif($id_biller ==  281){
                    $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                    $resp = $respon[7]; 
                 }else{
                    if(strpos(strtoupper($kdproduk),'WAKENDARI') !== false ){
                        $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                }
            }
        }
    }    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $frm = getParseProduk($kdproduk, $resp);


    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step         = $frm->getStep()+1;
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
    $r_mid          = $frm->getMID();
    //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;
    // $r_additional_datas = getAdditionalDatas($kdproduk, $frm);
    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }
    /* $params = array(
      $_SERVER['REMOTE_ADDR'],
      'ANDA TIDAK DIIJINKAN MELAKUKAN INQUIRY'
      ); */

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nama_pelanggan, (string) $r_periode_tagihan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, "0", (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );
    // if (count($r_additional_datas) > 0) {
    //     array_push($params, $r_additional_datas);
    // }
    //insert into fmss_message
    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    //$db = new Database();
    //$q_ins_log = "INSERT INTO fmss_message (id_transaksi, id_transaksi_partner, mid, content, insert_datetime) 
    //              VALUES (".$id_transaksi.", ".$id_transaksi_partner.", ".$mid.", '".replace_forbidden_chars_msg($resp)."', NOW())";
    //$e_ins_log = mysql_query($q_ins_log, $db->getConnection());

    $text = inq_resp_text($params);
    // $get_mid = get_mid_from_idtrx($r_idtrx);
    // $get_step = get_step_from_mid($get_mid) + 1;
    writeLog($r_mid, $r_step, $host, $receiver, $text, $via);
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function getValuePlnpra($id){
    $data = array(
        'PLNPRA20H' => '20000',
        'PLNPRA50H' => '50000',
        'PLNPRA100H' => '100000',
        'PLNPR20' => '20000',
        'PLNPR30' => '30000',
        'PLNPR50' => '50000',
        'PLNPR100' => '100000',
        'PLNPR200' => '200000',
        'PLNPR300' => '300000',
        'PLNPR500' => '500000',
        'PLNPR1000' => '1000000',
        'PLNPRAD20' => '20000',
        'PLNPRAD30' => '30000',
        'PLNPRAD50' => '50000',
        'PLNPRAD100' => '100000',
        'PLNPRAD200' => '200000',
        'PLNPRAD300' => '300000',
        'PLNPRAD500' => '500000',
        'PLNPRAD1K' => '1000000',
    );
    return $data[$id];
}

function beli($m) {
    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1 = strtoupper($m->getParam($i+=1)->scalarval());
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel3 = "";
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    $idpel_asli = "";

    if(($nominal == "" || $nominal === "0") && $kdproduk != ""){
        $nominal = getValuePlnpra($kdproduk);
    }

    if (in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        $normal_idpel = normalisasiIdPel1PLNPra($idpel1);
        $idpel1 = $normal_idpel["idpel1"];
        $idpel2 = $normal_idpel["idpel2"];
        $idpel_asli = $normal_idpel["idpel_asli"];
    }
    
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130") {
        // echo (!isValidIP($idoutlet, $ip));
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nidoutlet: $idoutlet\npin: -----\nref1: $ref1";
            // die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].\nBerikut inputan anda\n".htmlspecialchars_decode(htmlspecialchars_decode($request_error)));
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "]");
        }
    }
    

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
    }

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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;
    $fm = convertFM($msg, "*");
    
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];

    if( in_array($kdproduk, KodeProduk::getPLNPrepaids()) ){
        $respon = postValue($fm);
        $resp = $respon[7];    
    } else {
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*" . $pin . "*------******XY*Mitra Yth, mohon maaf, method ini khusus untuk produk PLN PRABAYAR saja ";     
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $frm = getParseProduk($kdproduk, $resp);
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMID();
    //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nama_pelanggan, (string) $r_periode_tagihan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, "0", (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );

    //insert into fmss_message
    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    $text = inq_resp_text($params);
    // $get_mid = get_mid_from_idtrx($r_idtrx);
    // $get_step = get_step_from_mid($get_mid) + 1;
    writeLog($r_mid, $r_step, $host, $receiver, $text, $via);
    if($r_status == "00"){
        $nominalnya  = $nominal;
        $md_request = array(
            'id_produk' => $r_kdproduk,
            'idpel1' => $idpel1,
            'idpel2' => $idpel2,
            'idpel3' => '',
            'nominal' => $nominalnya,
            'uid' => $r_idoutlet,
            'pin' => $r_pin,
            'ref1' => $ref1,
            'ref2' => $r_idtrx,
            'ref3' => $idpel_asli
        );
        $md_response = pay_pln($md_request);
        return new xmlrpcresp(php_xmlrpc_encode($md_response));
    } else {
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }
    
}

function normalisasiIdPel1PLNPra($idpel1) {
    if(strlen($idpel1) == 12){
        if(substr($idpel1, 0,1) == '0'){
            $ret = array(
                'idpel1' => substr($idpel1, 1),
                'idpel2' => "",
                'idpel_asli' => $idpel1,
            );
        } else {
            $ret = array(
                'idpel1' => "",
                'idpel2' => $idpel1,
                'idpel_asli' => "",
            );    
        }
    } else if(strlen($idpel1) == 11){
        $ret = array(
            'idpel1' => $idpel1,
            'idpel2' => "",
            'idpel_asli' => "",
        );
    } else {
        $ret = array(
            'idpel1' => "",
            'idpel2' => "",
            'idpel_asli' => "",
        );
    }
    return $ret;
}

function bpjsinq($m){
    $i              = -1;
    $kdproduk       = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1         = strtoupper($m->getParam($i+=1)->scalarval());
    $periodebulan   = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet       = strtoupper($m->getParam($i+=1)->scalarval());
    $pin            = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1           = strtoupper($m->getParam($i+=1)->scalarval());
    if($kdproduk == 'ASRBPJSKS'){
        $kdproduk = 'ASRBPJSKSH';
    }
    
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "TAGIHAN";
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];           //VIA

    $msg[$i+=1] = $idpel1; // idpel1
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];

    $msg[$i+=1] = ""; //SWITCHER_ID 
    $msg[$i+=1] = ""; //BILLER_CODE 
    $msg[$i+=1] = ""; //CUSTOMER_ID
    $msg[$i+=1] = $periodebulan;//BILL_QUANTITY
    $msg[$i+=1] = "";//GW_REFNUM
    $msg[$i+=1] = "";//SW_REFNUM
    $msg[$i+=1] = "";//CUSTOMER_NAME
    $msg[$i+=1] = "";//PRODUCT_CATEGORY
    $msg[$i+=1] = "";//BILL_AMOUNT
    $msg[$i+=1] = "";//PENALTY
    $msg[$i+=1] = "";//STAMP_DUTY
    $msg[$i+=1] = "";//PPN
    $msg[$i+=1] = "";//ADMIN_CHARGE
    $msg[$i+=1] = "";//CLAIM_AMOUNT
    $msg[$i+=1] = "";//BILLER_REFNUM
    $msg[$i+=1] = "";//PT_NAME
    $msg[$i+=1] = "";//LAST_PAID_PERIODE
    $msg[$i+=1] = "";//LAST_PAID_DUE_DATE
    $msg[$i+=1] = "";//BILLER_ADMIN_FEE
    $msg[$i+=1] = "";//MISC_FEE
    $msg[$i+=1] = "";//MISC_NUMBER
    $msg[$i+=1] = "";//CUSTOMER_PHONE_NUMBER
    $msg[$i+=1] = "";//CUSTOMER_ADDRESS
    $msg[$i+=1] = "";//AHLI_WARIS_PHONE_NUMBER
    $msg[$i+=1] = "";//AHLI_WARIS_ADDRESS


    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    //writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    $list = $GLOBALS["sndr"];

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        if($periodebulan == "" || $periodebulan > 12 || !is_numeric($periodebulan)){
            $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*$nominal*0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*Periode bulan harus diisi min 1 dan maks 12";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    }

    //die($resp);
    //$resp = "TAGIHAN*ASRBPJSKS*789644896*11*20160107130731*WEB*8888801851523593***000000064986*5000*".$idoutlet."*".$pin."**1497746*3*15*080002*432635265*00*EXT: APPROVE*0605 *LUBUK LINGGAU *8888801851523593*2***DJASILAH **000000064986*******BPJS Kesehatan***750**000000000000*0***";
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kdproduk, $resp);

    //$r_command        = $frm->getCommand();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    //$r_via                = $frm->getVia();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_name = $frm->getCustomerName();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
    
    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }

    $params = array(
        (string) 'ASRBPJSKS', (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_name, (string) $periodebulan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, "0", (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string)$r_saldo, (string) $url_struk
    );

    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    $db = new Database();
    $q_ins_log = "INSERT INTO fmss_message (id_transaksi, id_transaksi_partner, mid, content, insert_datetime) 
                    VALUES (" . $id_transaksi . ", " . $id_transaksi_partner . ", " . $mid . ", '" . replace_forbidden_chars_msg($resp) . "', NOW())";
    $e_ins_log = mysql_query($q_ins_log, $db->getConnection());

    return new xmlrpcresp(php_xmlrpc_encode($params));

}

function pay($m) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    global $limit;
    $i = -1;
    $kdproduk = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel1 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel2 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel3 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $nominal = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idoutlet = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $pin = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $ref1 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $ref2 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $ref3 = trim(strtoupper($m->getParam($i+=1)->scalarval()));

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $limit){
                $params = array(
                    (string) $kdproduk, 
                    (string) date('YmdHis'), 
                    (string) $idpel1, 
                    (string) $idpel2, 
                    (string) $idpel3, 
                    (string) '', 
                    (string) '', 
                    (string) $nominal, 
                    (string) '', 
                    (string) $idoutlet, 
                    (string) '------', 
                    (string) $ref1, 
                    (string) $ref2, 
                    (string) $ref3, 
                    (string) '77', 
                    (string) 'Transaksi gagal, silahkan coba beberapa saat lagi', 
                    (string) '', 
                    (string) '', 
                    (string) ''
                );
                appendfile('RESPON PAY FAIL XML to JSON: '.json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'XML',strtoupper("XML ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return new xmlrpcresp(php_xmlrpc_encode($params));
            }
        }
    }
    // handle 504 end
    
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    /* if($ip == "10.0.0.20"){
      die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
      } else {
      if(!isValidIP($idoutlet, $ip)){
      die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
      }
      } */
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].\nBerikut inputan anda\n".htmlspecialchars_decode(htmlspecialchars_decode($request_error)));
        }
    }

    
    //if (!checkHakAksesMP("", strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    $asr = array('ASRCAR', 'ASRTOKIOS', 'ASRTOKIO', 'ASRJWS');
    if(in_array($kdproduk, $asr)){
        $mti = "BAYAR";
    }

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
    }

    if($ref2 == ""){
        $ref2 = 0;
    }

    $ceknom = getNominalTransaksi(trim($ref2)); //tambahan
    // $cektoken = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal), trim($kdproduk), trim($idoutlet)); //tambahan
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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN
    if($kdproduk == "PAJAKGRTLT" || $kdproduk == "PAJAKGRTL"){
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = $idpel1;
    } else if ($kdproduk == "ASRJWS") {
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "1";           //KETERANGAN
    }
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

    /* tambahan */
    if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else {
             if(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
            }else{
                $id_biller = getBiller($kdproduk);
                 if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
                   if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                        $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                 }elseif($id_biller ==  281){
                    $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                    $resp = $respon[7]; 
                 }else{
                    if(strpos(strtoupper($kdproduk),'WAKENDARI') !== false ){
                        $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                }
            }
        }
    } else {
        
            if($kdproduk == 'ASRBPJSKS'){
                $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
            }elseif(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
            } else {
                $id_biller = getBiller($kdproduk);
                 if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
                   if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                        $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                 }elseif($id_biller ==  281){
                    $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                    $resp = $respon[7]; 
                 }else{
                    if(strpos(strtoupper($kdproduk),'WAKENDARI') !== false ){
                        $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                }
            }
        // }
    }
    /* tambahan */

    if($resp == 'null'){
        $ref2 = "";
        $cek1 = cekDataProses($idoutlet,$kdproduk,$idpel1,$ref1);
        if(count($cek1) > 0)
        {
            $ref2 =  $cek1[0]->id_transaksi;
        }else{
            $cek2 = cekDataTransaksi($idoutlet,$kdproduk,$idpel1,$ref1);
            if(count($cek2) > 0)
            {
                $ref2 =  $cek2[0]->id_transaksi;
            }
        }
        $params = array(
            (string) $kdproduk, 
            (string) date('YmdHis'), 
            (string) $idpel1, 
            (string) $idpel2, 
            (string) $idpel3, 
            (string) '', 
            (string) '', 
            (string) $r_nominal, 
            (string) '', 
            (string) $r_idoutlet, 
            (string) '------', 
            (string) $ref1, 
            (string) $ref2, 
            (string) $ref3, 
            (string) '00', 
            (string) 'SEDANG DIPROSES', 
            (string) '', 
            (string) '', 
            (string) ''
        );
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMID();
    $r_saldo_terpotong = 0;
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }
    // $token2 = $cektoken[3];

    $r_reff3 = '0';
    //$r_reff3 = $frm->getTokenPln();
    if (substr($kdproduk, 0, 6) == "PLNPRA" && $frm->getStatus() == "00") {
        if ($r_reff3 == '0') {
            $r_reff3 = $frm->getTokenPln();
        }
    }


    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk
        //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

        $nom_up = getnominalup($r_idtrx);

        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

        $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
    }

    if($r_status == '' || $r_status == '35' || $r_status == '68'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
    }

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nama_pelanggan, (string) $r_periode_tagihan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, (string) $r_reff3, (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );


    //$params = setMandatoryRespon($frm,$ref1,"","",$url_struk);
    //$params = getResponArray($kdproduk,$params,$resp);

    $is_return = true;
    /* if($resp == "null" || $frm->getStatus()=="35" || $frm->getStatus()=="68"){
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
      } */
    //print_r($params);
    if($r_idtrx != $ref2){
        $text = pay_resp_text($params);
        // $get_mid = get_mid_from_idtrx($r_idtrx);
        // $get_step = get_step_from_mid($get_mid) + 1;
        writeLog($r_mid, $r_step, $host, $receiver, $text, $via);
    }
    
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function pay_pln($m) {
    $i = -1;

    $kdproduk = $m['id_produk'];
    $idpel1 = $m['idpel1'];
    $idpel2 = $m['idpel2'];
    $idpel3 = $m['idpel3'];
    $nominal = $m['nominal'];
    $idoutlet = $m['uid'];
    $pin = $m['pin'];
    $ref1 = $m['ref1'];
    $ref2 = $m['ref2'];
    $ref3 = $m['ref3'];

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].\nBerikut inputan anda\n".htmlspecialchars_decode(htmlspecialchars_decode($request_error)));
        }
    }

    

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
    }

    $ceknom = getNominalTransaksi(trim($ref2)); //tambahan
    // $cektoken = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal), trim($kdproduk), trim($idoutlet)); //tambahan

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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];

    /* tambahan */
    $is_duplicate = false;
    // if ($cektoken[0] != '-') {
    //     $is_duplicate = true;
    //     $asterik = $cektoken[8];
    //     $get_asterix = get_asterix($asterik);
    //     $resp = $get_asterix[0];
    // } else {
        $respon = postValue($fm);
        $resp = $respon[7];
    // }
    /* tambahan */
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);

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
    $r_status = $is_duplicate == true ? 'XX' : $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
    $r_saldo_terpotong = 0;
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }
    // $token2 = $cektoken[3];

    $r_reff3 = '0';
    $segment = "";
    $power = "";
    $belipower = "";
    $kwh = "";
    $ket = $r_keterangan;
    $url_struk = "";
    //$r_reff3 = $frm->getTokenPln();
    if ($frm->getStatus() == "00") {
        $nom_up = getnominalup($r_idtrx);
        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
        $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
    }

    if($ref3 != ""){
        $r_idpel1 = $ref3;
        $r_idpel2 = "";
    }

    if (substr($kdproduk, 0, 6) == "PLNPRA") {
        if ($r_reff3 == '0') {
            $r_reff3 = $frm->getTokenPln();
        }
        $segment = $frm->getSubscriberSegmentation();
        $power = ltrim($frm->getPowerConsumingCategory(),'0')."VA";
        $belipower = getValue($frm->getPowerPurchase(), $frm->getMinorUnitOfPowerPurchase());
        $kwh = getValue($frm->getPurchasedKWHUnit(), $frm->getMinorUnitOfPurchasedKWHUnit());
        $flag_idpel = $r_idpel1 ==! "" ? "no meter ".$r_idpel1 : " id pelanggan ".$r_idpel2;
        $tokennya = chunk_split($r_reff3, 4, ' ');
        if($r_status == "00"){
            $ket = "Pengisian $r_kdproduk $nominal Anda ke $flag_idpel BERHASIL. SN=$tokennya/$r_nama_pelanggan/$segment/$power/$kwh Harga=$r_saldo_terpotong";
        } else if($r_status == "XX"){
            $ket = "Pengisian $r_kdproduk $nominal Anda ke $flag_idpel sudah pernah terjadi BERHASIL. SN=$tokennya/$r_nama_pelanggan/$segment/$power/$kwh Harga=$r_saldo_terpotong";
        }
    }

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nama_pelanggan, (string) $r_periode_tagihan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, (string) $r_reff3, (string) $r_status, (string) $ket, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );
    array_push($params, $r_additional_datas);
    $is_return = true;
    return $params;
}

function getValue($nominal="0", $minor=0){
    $nominal=sprintf("%".($minor+1)."0s", $nominal);
    $ret=substr($nominal, 0, (strlen($nominal)-$minor)).".".substr($nominal,(strlen($nominal)-$minor));
    
    return (double) $ret;
}

function payDetail($m) {
//BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    global $limit;
    $i = -1;
    $kdproduk = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel1 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel2 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idpel3 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $nominal = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $idoutlet = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $pin = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $ref1 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $ref2 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    $ref3 = trim(strtoupper($m->getParam($i+=1)->scalarval()));
    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    (string) $kdproduk, 
                    (string) date('YmdHis'), 
                    (string) $idpel1, 
                    (string) $idpel2, 
                    (string) $idpel3, 
                    (string) '', 
                    (string) '', 
                    (string) $nominal, 
                    (string) '', 
                    (string) $idoutlet, 
                    (string) '------', 
                    (string) $ref1, 
                    (string) $ref2, 
                    (string) $ref3, 
                    (string) '77', 
                    (string) 'Transaksi gagal, silahkan coba beberapa saat lagi', 
                    (string) '', 
                    (string) '', 
                    (string) ''
                );
                appendfile('RESPON PAY FAIL XML to JSON: '.json_encode($params));
                
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'XML',strtoupper("XML ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return new xmlrpcresp(php_xmlrpc_encode($params));
            }
        }
    }
    // handle 504 end
    
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    /* if($ip == "10.0.0.20"){
      die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
      } else {
      if(!isValidIP($idoutlet, $ip)){
      die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
      }
      } */
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

    
    //if (!checkHakAksesMP("", strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    $asr = array('ASRCAR', 'ASRTOKIOS', 'ASRTOKIO', 'ASRJWS');
    if(in_array($kdproduk, $asr)){
        $mti = "BAYAR";
    }

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
    }

    if($ref2 == ""){
        $ref2 = 0;
    }
    $ceknom = getNominalTransaksi($ref2);
    // $cektoken = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal), trim($kdproduk), trim($idoutlet)); //tambahan
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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN
    if($kdproduk == "PAJAKGRTLT" || $kdproduk == "PAJAKGRTL"){
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = $idpel1;
    } else if ($kdproduk == "ASRJWS") {
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "1";           //KETERANGAN
    }
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

    if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) { 
            if($ceknom == 'x'){
                $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF TIDAK BISA MENGAMBIL DATA NOMINAL";
            }else{
                $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI";
            }           
            
        } else {
            if(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
            }else{
                //$respon = postValueWithTimeOutDevel($fm);
                 $id_biller = getBiller($kdproduk);
                 if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
                   if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                        $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                 }elseif($id_biller ==  281){
                    $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                    $resp = $respon[7]; 
                 }else{
                    $respon = postValue($fm);
                    $resp = $respon[7];
                }
            }
        }
    } else {
        
            if($kdproduk == 'ASRBPJSKS'){
                $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
            } if(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
            } else {
                 $id_biller = getBiller($kdproduk);
                 if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
                   if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                        $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                 }elseif($id_biller ==  281){
                    $respon = postValue_fmssweb2($fm); // to /fmssweb2/mpin1
                    $resp = $respon[7]; 
                 }else{
                    $respon = postValue($fm);
                    $resp = $respon[7];
                }
            }
        // }
    }

    if($resp == 'null'){
        $ref2 = "";
        $cek1 = cekDataProses($idoutlet,$kdproduk,$idpel1,$ref1);
        if(count($cek1) > 0)
        {
            $ref2 =  $cek1[0]->id_transaksi;
        }else{
            $cek2 = cekDataTransaksi($idoutlet,$kdproduk,$idpel1,$ref1);
            if(count($cek2) > 0)
            {
                $ref2 =  $cek2[0]->id_transaksi;
            }
        }
        $params = array(
            (string) $kdproduk, 
            (string) date('YmdHis'), 
            (string) $idpel1, 
            (string) $idpel2, 
            (string) $idpel3, 
            (string) '', 
            (string) '', 
            (string) $r_nominal, 
            (string) '', 
            (string) $r_idoutlet, 
            (string) '------', 
            (string) $ref1, 
            (string) $ref2, 
            (string) $ref3, 
            (string) '00', 
            (string) 'SEDANG DIPROSES', 
            (string) '', 
            (string) '', 
            (string) ''
        );
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMID();
    $r_saldo_terpotong = 0;
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }
    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);

    // $token2 = $cektoken[3];
    $r_reff3 = '0';
//  $r_reff3 = $frm->getTokenPln();
    if (substr($kdproduk, 0, 6) == "PLNPRA" && $frm->getStatus() == "00") {
        if ($r_reff3 == '0') {
            $r_reff3 = $frm->getTokenPln();
        }
    }


    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk
        //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

        $nom_up = getnominalup($r_idtrx);

        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

        $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
    }

    if($r_status == '' || $r_status == '35' || $r_status == '68'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
    }

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nama_pelanggan, (string) $r_periode_tagihan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, (string) $r_reff3, (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );

    if (count($r_additional_datas) > 0) {
        array_push($params, $r_additional_datas);
    }

    //$params = setMandatoryRespon($frm,$ref1,"","",$url_struk);
    //$params = getResponArray($kdproduk,$params,$resp);

    $is_return = true;
    if($r_idtrx != $ref2){
        $text = paydetil_resp_text($params, $kdproduk);
        // $get_mid = get_mid_from_idtrx($r_idtrx);
        // $get_step = get_step_from_mid($get_mid) + 1;
        writeLog($r_mid, $r_step, $host, $receiver, $text, $via);
    }
    
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function bpjspay($m){
    global $limit;
    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel1 = strtoupper($m->getParam($i+=1)->scalarval());
    $periodebulan = strtoupper($m->getParam($i+=1)->scalarval());
    $hp = strtoupper($m->getParam($i+=1)->scalarval());
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());    
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref2 = strtoupper($m->getParam($i+=1)->scalarval());
    if($kdproduk == 'ASRBPJSKS'){
        $kdproduk = 'ASRBPJSKSH';
    }

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    (string) 'ASRBPJSKS', 
                    (string) date('YmdHis'), 
                    (string) $idpel1, 
                    (string) '', 
                    (string) '', 
                    (string) '', 
                    (string) $periodebulan, 
                    (string) $nominal, 
                    (string) '', 
                    (string) $idoutlet, 
                    (string) '------', 
                    (string) $ref1, 
                    (string) $ref2, 
                    (string) '0', 
                    (string) '77', 
                    (string) 'Transaksi gagal, silahkan coba beberapa saat lagi', 
                    (string) '', 
                    (string) '', 
                    (string) ''
                );
                appendfile('RESPON PAY FAIL XML to JSON: '.json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'XML',strtoupper("XML ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return new xmlrpcresp(php_xmlrpc_encode($params));
            }
        }
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
   
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }
    // handle 504 end
    
    $mti = "BAYAR";
    $ceknom = getNominalTransaksi($ref2);
    $arr = array('10', '11', '12');
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
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN

    $msg[$i+=1] = ""; //SWITCHER_ID 
    $msg[$i+=1] = ""; //BILLER_CODE 
    $msg[$i+=1] = ""; //CUSTOMER_ID
    $msg[$i+=1] = "$periodebulan";//BILL_QUANTITY
    $msg[$i+=1] = "";//GW_REFNUM
    $msg[$i+=1] = "";//SW_REFNUM
    $msg[$i+=1] = "";//CUSTOMER_NAME
    $msg[$i+=1] = "";//PRODUCT_CATEGORY
    $msg[$i+=1] = "";//BILL_AMOUNT
    $msg[$i+=1] = "";//PENALTY
    $msg[$i+=1] = "";//STAMP_DUTY
    $msg[$i+=1] = "";//PPN
    $msg[$i+=1] = "";//ADMIN_CHARGE
    $msg[$i+=1] = "";//CLAIM_AMOUNT
    $msg[$i+=1] = "";//BILLER_REFNUM
    $msg[$i+=1] = "";//PT_NAME
    $msg[$i+=1] = "";//LAST_PAID_PERIODE
    $msg[$i+=1] = "";//LAST_PAID_DUE_DATE
    $msg[$i+=1] = "";//BILLER_ADMIN_FEE
    $msg[$i+=1] = "";//MISC_FEE
    $msg[$i+=1] = "";//MISC_NUMBER
    $msg[$i+=1] = "$hp";//CUSTOMER_PHONE_NUMBER
    $msg[$i+=1] = "";//CUSTOMER_ADDRESS
    $msg[$i+=1] = "";//AHLI_WARIS_PHONE_NUMBER
    $msg[$i+=1] = "";//AHLI_WARIS_ADDRESS

    $fm = convertFM($msg, "*");    

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    //writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $via);
    //echo $msg."<br>";  
    if ($ceknom != $nominal) {
        $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*$nominal*0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI";
    } else if(substr($hp, 0, 1) != '0' || !is_numeric($hp) || !in_array(strlen($hp), $arr) || $hp == ''){
        $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*$nominal*0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*Isi No Hp dengan benar.";
    } else {      
        $respon = postValue($fm);
        $resp = $respon[7];
    }

    if($resp == 'null'){
        $ref2 = "";
        $cek1 = cekDataProses($idoutlet,$kdproduk,$idpel1,$ref1);
        if(count($cek1) > 0)
        {
            $ref2 =  $cek1[0]->id_transaksi;
        }else{
            $cek2 = cekDataTransaksi($idoutlet,$kdproduk,$idpel1,$ref1);
            if(count($cek2) > 0)
            {
                $ref2 =  $cek2[0]->id_transaksi;
            }
        }
        $params = array(
            (string) 'ASRBPJSKS', 
            (string) date('YmdHis'), 
            (string) $idpel1, 
            (string) '', 
            (string) '', 
            (string) '', 
            (string) $periodebulan, 
            (string) $nominal, 
            (string) '', 
            (string) $idoutlet, 
            (string) '------', 
            (string) $ref1, 
            (string) $ref2, 
            (string) '0', 
            (string) '00', 
            (string) 'SEDANG DIPROSES', 
            (string) '', 
            (string) '', 
            (string) ''
        );
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    //die($resp);

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
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);

    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);

    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk
        //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

        $nom_up = getnominalup($r_idtrx);

        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

        $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
    }

    $params = array(
        (string) 'ASRBPJSKS', (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nama_pelanggan, (string) $periodebulan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, (string) $r_reff3, (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );

    if (count($r_additional_datas) > 0) {
        array_push($params, $r_additional_datas);
    }

    //$params = setMandatoryRespon($frm,$ref1,"","",$url_struk);
    //$params = getResponArray($kdproduk,$params,$resp);

    $is_return = true;
    return new xmlrpcresp(php_xmlrpc_encode($params));

}

function pulsa($m) {
    global $limit;
    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $nohp = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());


    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    (string) $kdproduk, 
                    (string) date('YmdHis'), 
                    (string) $nohp, 
                    (string) $idoutlet, 
                    (string) '------', 
                    (string) '', 
                    (string) $ref1, 
                    (string) '', 
                    (string) '77', 
                    (string) 'Transaksi gagal, silahkan coba beberapa saat lagi', 
                    (string) '', 
                    (string) ''
                );
                appendfile('RESPON PAY FAIL XML to JSON: '.json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'XML',strtoupper("XML ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return new xmlrpcresp(php_xmlrpc_encode($params));
            }
        }
    }
    // handle 504 end


    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4") {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;        //FIELD_KETERANGAN

    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";

    if(substr($idoutlet, 0, 2) == 'FA'){
        if($idoutlet != "FA9919"){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
         $respon = postValue_fmssweb2($fm);
         $resp = $respon[7];
    }

    if($resp == 'null'){
        $params = array(
            (string) $kdproduk, 
            (string) date('YmdHis'), 
            (string) $nohp, 
            (string) $idoutlet, 
            (string) '------', 
            (string) '', 
            (string) $ref1, 
            (string) '', 
            (string) '00', 
            (string) 'SEDANG DIPROSES', 
            (string) '', 
            (string) ''
        );
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }
    
    $format = FormatMsg::pulsa();
    $frm = new FormatPulsa($format[1], $resp);

    //print_r($frm->data);
    $r_step             = $frm->getStep()+1;
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_nohp = $frm->getNohp();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
    $r_sn = $frm->getSN();
    $r_mid              = $frm->getMid();

    $r_sisa_saldo = $frm->getSaldo();
    $r_nominal = $frm->getNominal();
    $nom_up = getnominalup($r_idtrx);
    $r_saldo_terpotong = $r_nominal + ($nom_up);

    if($r_status === '' || $r_status === '35' || $r_status === '68'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
    }

    $params = array(
        $r_kdproduk, $r_tanggal, $r_nohp, $r_idoutlet, $r_pin, $r_sn, $ref1, $r_idtrx, $r_status, $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo
    );

    /* $is_return = true;
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
      } */
    $text = pulsa_game_resp_text($params);
    // $get_mid = get_mid_from_idtrx($r_idtrx);
    // $get_step = get_step_from_mid($get_mid) + 1;
    writeLog($r_mid, $r_step, $host, $receiver, $text, $via);
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function game($m) {
    global $limit;
    $i = -1;
    $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $nohp = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    (string) $kdproduk, 
                    (string) date('YmdHis'), 
                    (string) $nohp, 
                    (string) $idoutlet, 
                    (string) '------', 
                    (string) '', 
                    (string) $ref1, 
                    (string) '', 
                    (string) '77', 
                    (string) 'Transaksi gagal, silahkan coba beberapa saat lagi', 
                    (string) '', 
                    (string) ''
                );
                appendfile('RESPON PAY FAIL XML to JSON: '.json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'XML',strtoupper("XML ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return new xmlrpcresp(php_xmlrpc_encode($params));
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4") {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
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
    $msg[$i+=1] = strtoupper("XML ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;        //FIELD_KETERANGAN

    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";

    if(substr($idoutlet, 0, 2) == 'FA'){
        if($idoutlet != "FA9919"){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        $respon = postValue_fmssweb2($fm);
        $resp = $respon[7];
    }

    if($resp == 'null'){
        $params = array(
            (string) $kdproduk, 
            (string) date('YmdHis'), 
            (string) $nohp, 
            (string) $idoutlet, 
            (string) '------', 
            (string) '', 
            (string) $ref1, 
            (string) '', 
            (string) '00', 
            (string) 'SEDANG DIPROSES', 
            (string) '', 
            (string) ''
        );
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    $format = FormatMsg::game();
    $frm = new FormatGame($format[1], $resp);

    //print_r($frm->data);
    $r_step             = $frm->getStep()+1;
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_nohp = $frm->getNohp();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
    $r_sn = $frm->getSN();
    $r_mid              = $frm->getMid();
    $r_nominal = getNominalTransaksi($r_idtrx);
    $nom_up = getnominalup($r_idtrx);
    $r_saldo_terpotong = $r_nominal + ($nom_up);
    $r_sisa_saldo = $frm->getSaldo();

    if($r_status === '' || $r_status === '35' || $r_status === '68'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
    }

    $params = array(
        $r_kdproduk, $r_tanggal, $r_nohp, $r_idoutlet, $r_pin, $r_sn, $ref1, $r_idtrx, $r_status, $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo
    );

    /* $is_return = true;
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
      } */
    $text = pulsa_game_resp_text($params);
    // $get_mid = get_mid_from_idtrx($r_idtrx);
    // $get_step = get_step_from_mid($get_mid) + 1;
    writeLog($r_mid, $r_step, $host, $receiver, $text, $via);
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function balance($m) {
    //GPIN*PINBARU*IDOUTLET*PIN*TOKEN*VIA
    $i = -1;
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }
    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = "SAL";
    $msg[$i+=1] = "SAL";      //KDPRODUK
    $msg[$i+=1] = $GLOBALS["mid"];     //MID
    $msg[$i+=1] = $stp;        //STEP
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];
    $msg[$i+=1] = $idoutlet;
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";

    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";

    $respon = postValue($fm);
    //print_r($respon);
    $resp = $respon[7];

    $format = FormatMsg::cekSaldo();
    $frm = new FormatCekSaldo($format[1], $resp);

    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_balance = $frm->getSaldo();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();

    $params = array(
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

    if($idproduk == 'HPTSEL'){
        $idproduk = 'HPTSELH';
    } else if($idproduk == 'ASRBPJSKS'){
        $idproduk = 'ASRBPJSKSH';
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

    
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
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

        $substr1 = substr($tgl1, '0', '8');
        $substr2 = substr($tgl2, '0', '8');
        $datex = date("Ymdhis", strtotime("+1 day", strtotime($tgl1)));
        $substr3 = substr($datex, '0', '8');

        if ($tgl1 != "" && $tgl2 != "") {
            if (isvaliddaterange($tgl1, $tgl2)) {
                //$next = TRUE;
                if ($substr1 == $substr2) { //case hari yg sama
                    $next = TRUE;
                } else if ($substr2 == $substr3) { //selisih 1 hari
                    $next = TRUE;
                } else { //selisih lebih dari 1 hari
                    $msg[7] = "XX";
                    $msgcontent[8] = "Range tanggal maksimal hanya 1 hari";
                    $next = FALSE;
                }
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
        if ($limit != "") {
            if (!is_numeric($limit)) {
                $msg[7] = "XX";
                $msgcontent[8] = "Limit harus berupa angka";
                $next = FALSE;
            } else {
                $next = TRUE;
            }
        } else {
            $next = TRUE;
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

                if($data[$i]->id_produk == 'HPTSELH'){
                    $datatransaksi[] = $data[$i]->id_produk = "HPTSEL";
                } else if($data[$i]->id_produk == 'ASRBPJSKSH'){
                    $datatransaksi[] = $data[$i]->id_produk = "ASRBPJSKS";
                } else {
                    $datatransaksi[] = $data[$i]->id_produk;
                }

                $datatransaksi[] = $data[$i]->namaproduk;
                $datatransaksi[] = $data[$i]->idpelanggan;
                $datatransaksi[] = $data[$i]->response_code;
                //$datatransaksi[] = $data[$i]->keterangan;
                if($data[$i]->response_code == NULL){
                    $datatransaksi[] = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else {
                    $datatransaksi[] = $data[$i]->keterangan;
                }  

                $datatransaksi[] = $data[$i]->nominal;
                if(in_array($data[$i]->id_produk, KodeProduk::getPLNPrepaids())){
                    $datatransaksi[] = $data[$i]->token;
                } else {
                    $datatransaksi[] = $data[$i]->sn;
                }
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

    //$sender = $GLOBALS["__G_module_name"];
    //$receiver = $GLOBALS["__G_receiver"];
    $req = array(
        'tgl1'      => $tgl1,
        'tgl2'      => $tgl2,
        'idtrx'     => $idtrx,
        'idproduk'  => $idproduk,
        'idpel'     => $idpel,
        'limit'     => $limit,
        'idoutlet'  => $idoutlet,
        'pin'       => '------',
    );
    $tgl = date('Y-m-d H:i:s');
    $log_data = "\n\n========================".$tgl."========================\n";
    $log_data .= print_r($req, TRUE);
    $log_data .= "\n\n";
    $log_data .= print_r($params, TRUE);
    $log_data .= "========================".$tgl."========================\n";
    // write_log_text($log_data);
    //writeLog($GLOBALS["mid"], $GLOBALS["step"] + 1, $sender, $receiver, $log_data, $GLOBALS["via"]);
    
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function data_transaksi($m){
    $next   = FALSE;
    $end    = FALSE;
    $i = -1;
    $idoutlet   = strtoupper($m->getParam($i+=1)->scalarval());
    $pin        = strtoupper($m->getParam($i+=1)->scalarval());
    $tgl1       = strtoupper($m->getParam($i+=1)->scalarval());
    $tgl2       = strtoupper($m->getParam($i+=1)->scalarval());
    $idtrx      = strtoupper($m->getParam($i+=1)->scalarval());
    $idproduk   = strtoupper($m->getParam($i+=1)->scalarval());
    $idpel      = strtoupper($m->getParam($i+=1)->scalarval());
    $limit      = strtoupper($m->getParam($i+=1)->scalarval());
    $custreff   = strtoupper($m->getParam($i+=1)->scalarval());

    if($idproduk == 'HPTSEL'){
        $idproduk = 'HPTSELH';
    } else if($idproduk == 'ASRBPJSKS'){
        $idproduk = 'ASRBPJSKSH';
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
             $dt = array(
                (string) "IP Anda [$ip] tidak punya hak akses"
             );
             return new xmlrpcresp(php_xmlrpc_encode($dt));

        }
    }

    
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        $dt = array(
                (string) "Anda tidak punya hak akses"
             );
        return new xmlrpcresp(php_xmlrpc_encode($dt));
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
        $dt = array(
                (string) "01",
                (string) "ID Outlet tidak terdaftar atau tidak aktif"
             );
        $next = FALSE;
    }

    if ($next) {
        $substr1 = substr($tgl1, '0', '8');
        $substr2 = substr($tgl2, '0', '8');
        $datex = date("Ymdhis", strtotime("+1 day", strtotime($tgl1)));
        $substr3 = substr($datex, '0', '8');

        if ($tgl1 != "" && $tgl2 != "") {
            if (isvaliddaterange($tgl1, $tgl2)) {
                //$next = TRUE;
                if ($substr1 == $substr2) { //case hari yg sama
                    $next = TRUE;
                } else if ($substr2 == $substr3) { //selisih 1 hari
                    $next = TRUE;
                } else { //selisih lebih dari 1 hari
                     $dt = array(
                        (string) "XX",
                        (string) "Range tanggal maksimal hanya 1 hari"
                     );
                    $next = FALSE;
                }
            } else {
                 $dt = array(
                    (string) "05",
                    (string) "Range tanggal yang Anda masukkan salah"
                 );
                $next = FALSE;
            }
        } else {
             $dt = array(
                    (string) "04",
                    (string) "Anda harus memberikan value pada range tanggal"
                 );
            $next = FALSE;
        }
    }


    if ($next) {
        if (checkpin($idoutlet, $pin)) {
            $next = TRUE;
        } else {
             $dt = array(
                    (string) "02",
                    (string) "Pin yang Anda masukkan salah"
                 );
            $next = FALSE;
        }
    }


    if ($next) {
        if ($idproduk != "") {
            if (productexists($idproduk)) {
                $next = TRUE;
            } else {
                 $dt = array(
                    (string) "03",
                    (string) "Produk yang Anda masukkan tidak tersedia"
                 );
                $next = FALSE;
            }
        }
    }

    if ($next) {
        if ($limit != "") {
            if (!is_numeric($limit)) {
                  $dt = array(
                    (string) "XX",
                    (string) "Limit harus berupa angka"
                 );
                $next = FALSE;
            } else {            
                $next = TRUE;
            }            
        } else {
            $next = TRUE;
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

    if ($next || $end) {
        $receiver   = $GLOBALS["__G_receiver"];
        global $host;
        $msg[7]     = "00";
        $data       = getDataTransaksi($idoutlet, $idtrx, $idproduk, $idpel, $limit, $tgl1, $tgl2, $custreff);
        $cnt        = count($data);
        $narindo_rc = array("1", "2", "3", "5", "12", "15", "21", "35", "68", "80","4");
        $gigaotomax_rc = array("35", "68", "XX", "05");
        $eratel_rc = array("68", "57");
        $servindo_rc = array("35");
        $gigaotomax_rc_replace = array("16");
        if ($cnt > 0) {
            $msgcontent = array();
            //Looping
                $datatransaksi = array();
            for ($i = 0; $i < $cnt; $i++) {
                                
                if($data[$i]->id_produk == 'HPTSELH'){
                    $idproduk = $data[$i]->id_produk = "HPTSEL";
                } else if($data[$i]->id_produk == 'ASRBPJSKSH'){
                    $idproduk = $data[$i]->id_produk = "ASRBPJSKS";
                } else {
                    $idproduk = $data[$i]->id_produk;
                }

                $datatransaksi[] = $data[$i]->namaproduk;
                $datatransaksi[] = $data[$i]->idpelanggan;

                if($data[$i]->response_code == "" || $data[$i]->response_code == NULL){
                    $rc = $data[$i]->response_code = "00";
                    $status = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else if($data[$i]->id_biller == "192" && in_array($data[$i]->response_code, $gigaotomax_rc) && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') !== false){
                    //biller gigapulsa otomax
                    $rc = $data[$i]->response_code = "00";
                    $status = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else if($data[$i]->id_biller == "192" && in_array($data[$i]->response_code, $gigaotomax_rc_replace)){
                    //biller gigapulsa otomax ganti keterangan lek rc 16
                    $rc= $data[$i]->response_code;
                    $status = $data[$i]->keterangan = "Transaksi ".$data[$i]->id_produk." tujuan: ".$data[$i]->idpelanggan." gagal.";
                } else if(in_array($data[$i]->response_code, $narindo_rc) && $data[$i]->id_biller == '169'){
                    //biller narindo
                    $rc = $data[$i]->response_code = "00";
                    $status = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else if(in_array($data[$i]->response_code, $eratel_rc) && $data[$i]->id_biller == '160'){
                    //biller ERATEL
                    $rc = $data[$i]->response_code = "00";
                    $status = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else if(in_array($data[$i]->response_code, $servindo_rc) && $data[$i]->id_biller == '26' && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') !== false){
                    //biller SERVINDO
                    $rc = $data[$i]->response_code = "00";
                    $status = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else {
                    $rc = $data[$i]->response_code;
                    $arrDesc = explode(' ', $data[$i]->keterangan);
                    $desc = $arrDesc[0] . ' ' . $arrDesc[1];
                    if ($arrDesc[0] == 'SUKSES' && $arrDesc[1] == 'OLEH') {
                        $desc = $arrDesc[0].' '.$arrDesc[1].' ADMIN'; 
                    }else{
                        if($arrDesc[0] == 'Gagal' && $arrDesc[1] == 'Manual'){
                            $desc = $arrDesc[0].' '.$arrDesc[1].' Oleh Admin'; 
                        }else{
                            $desc = $data[$i]->keterangan;
                        }
                    }
                    $status = $desc;
                }

                if(in_array($data[$i]->id_produk, KodeProduk::getPLNPrepaids())){
                    $token = $data[$i]->token;
                } else {
                    $token = $data[$i]->sn;
                }

                 $status_trx = "";
                if($data[$i]->response_code === '00' && ($data[$i]->keterangan !== "" && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') === false )){
                    $status_trx = "SUKSES";
                }else if(($data[$i]->response_code === '00' || $data[$i]->response_code === '05') && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') !== false){
                    $status_trx = "PENDING";
                }else if(($data[$i]->response_code === '00' || $data[$i]->response_code === '68') && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') !== false){
                    $status_trx = "PENDING";
                }else if(($data[$i]->response_code === '00' || $data[$i]->response_code === '35') && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') !== false){
                    $status_trx = "PENDING";
                }else if(($data[$i]->response_code === '68')){
                    $status_trx = "PENDING";
                }else if(($data[$i]->response_code === '35')){
                    $status_trx = "PENDING";
                }else if(($data[$i]->response_code === '')){
                    $status_trx = "PENDING";
                }else if($data[$i]->response_code !== '00' && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') === false) {
                    $status_trx = "GAGAL";
                }
                

                $dt[] = (Object)array(
                    "id_transaksi" => (string) $data[$i]->id_transaksi,
                    "tanggal_transaksi" => (string) $data[$i]->transaksidatetime,
                    "id_produk" => (string) $idproduk,
                    "nama_produk" => (string) $data[$i]->namaproduk,
                    "nomor_tujuan" => (string) $data[$i]->idpelanggan,
                    "status" => (string) $rc,
                    "keterangan" => (string) $status,
                    "nominal" => (string) $data[$i]->nominal,
                    "token" => (string) $token,
                    "status_transaksi" => (string) $status_trx
                );
            }
            //End of looping
        } else {
            $dt = array(
                    (string) "06",
                    (string) "Tidak ada data transaksi sesuai kriteria yang di-request"
                );
        }
    }
    return new xmlrpcresp(php_xmlrpc_encode($dt));
}


function cetakUlang($m) {
//BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i = -1;
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref2 = strtoupper($m->getParam($i+=1)->scalarval());

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    // if ($ip <> "") {
    //     if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130") {
    //         die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
    //     }
    // }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
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

    if ($frm->getStatus() == "00") {
        $frm = getParseProduk($kdproduk, $resp);
        //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

        $nom_up = getnominalup($r_idtrx);
        //die($nom_up);
        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

        $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);

        if (substr($frm->getKodeProduk(), 0, 6) == "PLNPRA") {
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
    /* $r_saldo_terpotong = $r_nominal + $r_nominaladmin;
      $r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
      $r_periode_tagihan = getBillPeriod($kdproduk,$frm); */



    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk
        $url = enkripUrl(strtoupper($idoutlet), $ref2);
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
    }

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nama_pelanggan, (string) $r_periode_tagihan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, (string) $r_reff3, (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function cetakUlangDetail($m) {
//BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i = -1;
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $ref1 = strtoupper($m->getParam($i+=1)->scalarval());
    $ref2 = strtoupper($m->getParam($i+=1)->scalarval());

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130") {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

    if (kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
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

    if ($frm->getStatus() == "00") {
        $frm = getParseProduk($kdproduk, $resp);
        //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

        $nom_up = getnominalup($r_idtrx);
        //die($nom_up);
        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

        $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);

        if (substr($frm->getKodeProduk(), 0, 6) == "PLNPRA") {
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
    /* $r_saldo_terpotong = $r_nominal + $r_nominaladmin;
      $r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
      $r_periode_tagihan = getBillPeriod($kdproduk,$frm); */



    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk
        $url = enkripUrl(strtoupper($idoutlet), $ref2);
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
    }

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nama_pelanggan, (string) $r_periode_tagihan, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, (string) $r_reff3, (string) $r_status, (string) $r_keterangan, (string) $r_saldo_terpotong, (string) $r_sisa_saldo, (string) $url_struk
    );
    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);
    if (count($r_additional_datas) > 0) {
        array_push($params, $r_additional_datas);
    }
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function inqEquity($m) {
    $i = -1;
    $tagihan = "TAGIHAN";
    $kode_produk = "ASREQJLN";
    $mid = $GLOBALS["mid"]; //1;

    $step = "1";
    $via = "XML";
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());

    $ip_mac_add = "--;--;nul;nul";

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

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
    $msg[$i+=1] = $pin; //pin
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ip_mac_add;
    for ($k = $i; $k <= 54; $k++) {
        $msg[$k] = "";
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

function payEquity($m) {
    $i = -1;
    $tagihan = "BAYAR";
    $kode_produk = "ASREQJLN";
    $mid = $GLOBALS["mid"]; //1;                                              //"453759285";
    $step = "1";
    $via = "XML";
    $id_ktp = strtoupper($m->getParam($i+=1)->scalarval());                 //"0151518190688000";
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());
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

    /* if ($id_outlet == "BS0004"){   
      $result = array();
      $result[7] = "BAYAR*ASREQJLN*592493435*8*20150901144123*XML*3573020305900003***10000*0*BS0004*------*------*656311*2**ASREQJLN*371608454*00*SUKSES*UMUM**3573020305900003*01***AKH MIRZA ALIEF SYAHRIAL*Asuransi Perjalanan Equity*10000*****100000000*BM15AP029254*EQUITY*2015-01-01****2015-11-19 15:41*BM15AP029254*082244300195*****085755672618***20150901144123*20151118154100*20151125154100*Sertifikat bisa di-print di: www.fastpay.co.id/enduser  XML     XML     2015-09-01 14:41:23.619278";
      $params = $result;
      return new xmlrpcresp(php_xmlrpc_encode($params));
      } */


     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }
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

function inqBintang($m) {
    $i = -1;
    $kode_produk = strtoupper($m->getParam($i+=1)->scalarval()); //'ASRBINT2';
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval()); //'BS0004';
    $pin = strtoupper($m->getParam($i+=1)->scalarval()); //141414;

    $mid = $GLOBALS["mid"]; //1;
    $step = 1;

     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

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
    for ($j = $i; $j <= 55; $j++) {
        $msg[$j] = ""; //
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

function payBintang($m) {
    $i = -1;
    $kode_produk = strtoupper($m->getParam($i+=1)->scalarval()); //'ASRBINT2';
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval()); //'BS0004';
    $pin = strtoupper($m->getParam($i+=1)->scalarval()); //141414;  

    $id_pelanggan = strtoupper($m->getParam($i+=1)->scalarval()); //"123456789";
    $nominal = strtoupper($m->getParam($i+=1)->scalarval()); //"2500";
    $id_transaksi_inq = strtoupper($m->getParam($i+=1)->scalarval()); //"326782413";

    $nama = strtoupper($m->getParam($i+=1)->scalarval()); //"Andi Yusanto";
    $no_telp = strtoupper($m->getParam($i+=1)->scalarval()); //"085649492115";

    $mid = $GLOBALS["mid"]; //1;
    $step = 1;

    if ($id_outlet == "BS0004") {
        $result = array();
        $result[7] = "BAYAR*ASRBINT1*601357786*8*20150907135726*XML*1234567890***2500*0*BS0004*------**654811*1**ASRBINT1*374370795*00*SUKSES****01***MIRZA*Asuransi Bintang Paket 1*2500*****20000000*AB1500029594*BINTANG******AB1500029594*08111111111111********20150907135726*20150908135726*20151007135726*Sertifikat bisa di-print di: www.fastpay.co.id/enduser";
        $params = $result;
        return new xmlrpcresp(php_xmlrpc_encode($params));
    }

     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "BAYAR"; //*
    $msg[$i+=1] = $kode_produk; //"ASRBINT1";//*
    $msg[$i+=1] = $mid; //"456090247";//*
    $msg[$i+=1] = $step; //"2";//*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = "XML"; //*
    $msg[$i+=1] = $id_pelanggan; //"123456789";//*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $nominal; //"2500";//*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $id_outlet; //"BS0004";//*
    $msg[$i+=1] = $pin; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $id_transaksi_inq; //"326782413";//*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = "-;-;null;null"; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $nama; //"Andi Yusanto";//*
    $msg[$i+=1] = $kode_produk; //"ASRBINT1";//*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $no_telp; //"08564949211";//*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $fm = convertFM($msg, "*");
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;

    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function inqRumahZakat($m) {
    $i = -1;
    $kode_produk = strtoupper($m->getParam($i+=1)->scalarval()); //'RZZ';
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval()); //'BS0004';
    $pin = strtoupper($m->getParam($i+=1)->scalarval()); //141414;

    $mid = $GLOBALS["mid"]; //1;
    $step = 1;

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }
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
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = "-;-;null;null"; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $fm = convertFM($msg, "*");


    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;

    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function payRumahZakat($m) {
    global $limit;
    $i = -1;
    $kode_produk = strtoupper($m->getParam($i+=1)->scalarval()); //'RZZ';
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval()); //'BS0004';
    $pin = strtoupper($m->getParam($i+=1)->scalarval()); //141414;

    $nominal = strtoupper($m->getParam($i+=1)->scalarval()); //"1000";
    $id_transaksi_inq = strtoupper($m->getParam($i+=1)->scalarval()); //"326782413";
    $nama = strtoupper($m->getParam($i+=1)->scalarval()); //"Andi Yusanto";

    $no_telp = strtoupper($m->getParam($i+=1)->scalarval()); //"085649492115";
    $alamat = strtoupper($m->getParam($i+=1)->scalarval()); //"Jl Anggrek inpres 8 Kureksari Waru";
    $kode_propinsi = strtoupper($m->getParam($i+=1)->scalarval()); //"28";

    $kode_kota = strtoupper($m->getParam($i+=1)->scalarval()); //"250";
    $email = $m->getParam($i+=1)->scalarval(); //"andi_yusanto@yahoo.com";

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    (string) $kdproduk, 
                    (string) date('YmdHis'), 
                    (string) $idpel1, 
                    (string) $idpel2, 
                    (string) $idpel3, 
                    (string) '', 
                    (string) '', 
                    (string) $nominal, 
                    (string) '', 
                    (string) $id_outlet, 
                    (string) '------', 
                    (string) $ref1, 
                    (string) $ref2, 
                    (string) $ref3, 
                    (string) '77', 
                    (string) 'Transaksi gagal, silahkan coba beberapa saat lagi', 
                    (string) '', 
                    (string) '', 
                    (string) ''
                );
                appendfile('RESPON PAY FAIL XML to JSON: '.json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'XML',strtoupper("XML ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return new xmlrpcresp(php_xmlrpc_encode($params));
            }
        }
    }
    // handle 504 end

     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }
    $mid = $GLOBALS["mid"]; //1;
    $step = 1;

    $msg = array();
    $i = -1;
    $msg[$i+=1] = "BAYAR"; //*
    $msg[$i+=1] = $kode_produk; //RZZ*
    $msg[$i+=1] = $mid; //456093813*
    $msg[$i+=1] = $step; //2*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = "XML"; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $nominal; //1000*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $id_outlet; //BS0004*
    $msg[$i+=1] = $pin; //------*
    $msg[$i+=1] = ""; //------*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $id_transaksi_inq; //326778933*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = "-;-;null;null"; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $no_telp; //085649492115*
    $msg[$i+=1] = $nama; //Andi Yusanto*
    $msg[$i+=1] = $alamat; //Jl Anggrek inpres 8 Kureksari Waru*
    $msg[$i+=1] = $kode_propinsi; //28*
    $msg[$i+=1] = $kode_kota; //250*
    $msg[$i+=1] = $nominal; //1000*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = ""; //*
    $msg[$i+=1] = $email; //andi_yusanto@yahoo.com
    $fm = convertFM($msg, "*");

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
//    $params = $msg;

    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function inquerycity($m) {
    $mid = $GLOBALS["mid"];
    $step = 1;
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    $msg = array();
    $i = -1;
    $k = -1;
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //RESERVASI*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //AIRPORT*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //603062973[mid]*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2*    
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //WEB*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //BS0004*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //141414*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //AIRPORT*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPSW*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //180.178.109.98;dc:0e:a1:dc:7b:26;null;null*

    /* logrequest........... */
    $loglines = "";
    foreach ($msg as $key => $value) {
        $loglines = $loglines . $value . "*";
    }
    appendfile($loglines);
    /* .............. */


    $fm = convertFM($msg, "*");
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;

    /* logresponse........... */
    $loglines2 = "";
    foreach ($params as $key2 => $value2) {
        $loglines2 = $loglines2 . $value2 . "*";
    }
    appendfile($loglines2);
    /* ...................... */

    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function inqueryschedule($m) {
    $mid = $GLOBALS["mid"];
    $step = 1;
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    $msg = array();
    $i = -1;
    $k = -1;
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //RESERVASI*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //603062973*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //DESKTOP*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //BS0004*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //141414*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //FASTPAY*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //AVAIL*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1441699465104*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //SUB*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //CGK*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //10/21/2015*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //10/21/2015*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //01*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //RESERVASI*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //RESERVASI*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //RESERVASI*
    $fm = convertFM($msg, "*");

    /* logrequest........... */
    $loglines = "";
    foreach ($msg as $key => $value) {
        $loglines = $loglines . $value . "*";
    }
    appendfile($loglines);
    /* .............. */

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function getUpper($message) {
    $returnStr = "";
    //ADT;MR;DIAN;BHARUNA;01/18/1990;AM 722911;::;::08563396898;;;;GILANG.P.A.WIRAYUDA@GMAIL.COM;KTP;ID;ID;21-DEC-2016
    $arraystring = explode(';', $message);
    for ($i = 0; $i < sizeof($arraystring); $i++) {
        if (($i + 1) == sizeof($arraystring)) {
            $returnStr = $returnStr . ($arraystring[$i]);
        } else {
            $returnStr = $returnStr . strtoupper($arraystring[$i]) . ';';
        }
    }
    return $returnStr;
}

function flightbooking($m) {
    $response = array();
//    for ($i = 1; $i <= 2; $i++) {
        $response = flightbookingtry($m);
        $asterik = "";
        foreach ($response as $key => $value) {
            $asterik = $value;
        }
        $tmpasterik = explode("*", $asterik);
	return $response;
/*        if ($tmpasterik[100] == '00') {
            return $response;
        } else {
            if ($i == 2) {
                return $response;
            }
        }
    }*/
}

function flightbookingtry($m) {
    $mid = $GLOBALS["mid"]; //1;
    $step = 1;
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    $msg = array();
    $i = -1;
    $k = -1;
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //RESERVASI*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //603189335*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //WEB*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //BS0004*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //141414*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //FASTPAY*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //BOOKING*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1441699897143*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //SUB*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //DJB*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //10/21/2015*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //10/21/2015*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //01*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = $m->getParam($i+=1)->scalarval(); //9;0101;Y;0;;garuda;;;08:25:00;10:00:00;GA449;SUB;CGK;2015-10-21;2015-10-21;1441699909-cabae58dae4a07c344fba3c35f4014248699632d;*
    $msg[$k+=1] = $m->getParam($i+=1)->scalarval(); //9;0102;V;0;;garuda;;;11:05:00;12:30:00;GA132;CGK;DJB;2015-10-21;2015-10-21;1441699909-cabae58dae4a07c344fba3c35f4014248699632d;*
    $msg[$k+=1] = $m->getParam($i+=1)->scalarval(); //*
    $msg[$k+=1] = ""; //*
    /* parse................... */
    $dewasa = explode("|", ($m->getParam($i+=1)->scalarval()));
    $anak = explode("|", ($m->getParam($i+=1)->scalarval()));
    $balita = explode("|", ($m->getParam($i+=1)->scalarval()));
    $d = count($dewasa) - 1;
    $a = count($anak) - 1;
    $b = count($balita) - 1;
    $xd = 0;
    $xa = 0;
    $xb = 0;
    $max = max($d, $a, $b);
    for ($t = 0; $t < $max; $t++) {
        /* dewasa */
        if ($xd < $d) {
            $msg[$k+=1] = getUpper($dewasa[$xd]);
            $xd++;
        } else {
            $msg[$k+=1] = "";
        }
        /* anak */
        if ($xa < $a) {
            $msg[$k+=1] = getUpper($anak[$xa]);
            $xa++;
        } else {
            $msg[$k+=1] = "";
        }
        /* balita */
        if ($xb < $b) {
            $msg[$k+=1] = getUpper($balita[$xb]);
            $xb++;
        } else {
            $msg[$k+=1] = "";
        }
    }
    $tmp = 73 - $k;
    for ($z = 1; $z < $tmp; $z++) {
        $msg[$k+=1] = ""; //*
    }
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPSW*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //180.178.109.98;dc:0e:a1:dc:7b:26;null;null*

    /* logrequest........... */
    $loglines = "";
    foreach ($msg as $key => $value) {
        $loglines = $loglines . $value . "*";
    }
//    appendfile($loglines);
    /* .............. */

    /*  if ($msg[6] == "BS0004") {
      $result = array();
      $result[7] = "RESERVASI*TPSJ*731274551*10*20151202143058*DESKTOP*FA5261*------*------*30742*BOOKING*1449041332560*SRIWIJAYA*0*PGK*TKG*03-Des-2015*03-Des-2015*2*0*0*01***9;2831281:X:S;X;374300;;sriwijaya;;0 Stops;14:55;18:00;SJ 075,SJ 086;PGK;CGK;2015-12-03;2015-12-03;020090668577767279708888888188878689908180907476*3;2941571:V:S;V;247000;;sriwijaya;;0 Stops;17:20;18:00;SJ 075,SJ 086;CGK;TKG;2015-12-03;2015-12-03;020090668577767279708888888188878689908180907476***ADT;MR;SUKIRNO;SUKIRNO;;;::6285609856448;::6285609856448;;;;;KTP;ID***ADT;MS;SAKINAH;SAKINAH;;;::6285609856448;::6285609856448;;;;;KTP;ID***************ADT;MR;SUKIRNO SUKIRNO;|ADT;MS;SAKINAH SAKINAH;|***TTQADW;2015-12-02;2015-12-02 16:00:00;-|#ADT;MR;SUKIRNO SUKIRNO;|ADT;MS;SAKINAH SAKINAH;|#2015-12-03;SJ075;14:55;16:00;BOOKED;PGK;CGK|2015-12-03;SJ086;17:20;18:00;BOOKED;CGK;TKG|#1159300;1066000;1242600;1159300#|;sriwijaya*TTQADW*-*SJ075*14:55*16:00*17:20*18:00*****02-Des-2015*02-Des-2015 16:00*****2015-12-03**1242600*58310*1066000*1159300*1*0*CGK**SJ086**2015-12-03************TPSW*2*HK*1242600*0*0**TPSJ*415600551*00*SUKSES";
      $params = $result;
      return new xmlrpcresp(php_xmlrpc_encode($params));
      }
     */
    $fm = convertFM($msg, "*");
    appendfile("Send to core : " . $fm);
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $step = 1;
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    appendfile("Receive from core : " . $respon[7]);
//    $respon = array(); $respon[7] = "RESERVASI*TPQZ*717408816*11*20151123132837*WEB*BS0004*------*------*231797*BOOKING*3611695663537*AIR ASIA*0*SUB*CGK*30-Nov-2015*11-Jun-2017*1*0*0*01***4;0~V~~V01H00~AAB1~~3~XbimasaktiXT~7681~ ~~SUB~11/30/2015 05:30~CGK~11/30/2015 06:45~@11/29/2015 22:30:00;LOW FARE;672900;;airasia;;2015-11-30;05:30;06:45;XT 7681;SUB;CGK****ADT;MR;DIRGANTARA;TARA;11/14/1988;;::;::085646658259;;;;SSM.MGR@BM.CO.ID;KTP;ID******************ADT;MR;DIRGANTARA TARA;|***HCJ22M;2015-11-23;23-Nov-2015 15:08;-|#ADT;MR;DIRGANTARA TARA;|#30-November-2015;XT 7681;05:30;06:45;BOOKED;SUB;CGK|#645891;;672900;645891|#-;airasia*HCJ22M*-*XT 7681*05:30*06:45*******23-Nov-2015*23-Nov-2015 15:08*****2015-11-30**672900*27406*645891*645891*0*0******************1*BOOKED*672900*10000*0*0*airasia*411746962*00*SUCCESS/APPROVE";
    $params = $respon;

    /* logresponse........... */
    $loglines2 = "";
    foreach ($params as $key2 => $value2) {
        $loglines2 = $loglines2 . $value2 . "*";
    }
    //  appendfile($loglines2);
    /* ...................... */

    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function flightpayment($m) {
    $msg = array();
    $i = -1;
    $k = -1;

    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //PAYMENT*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //646930842*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //DESKTOP*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //FA73930*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //------PIN *
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //------FASTPAY*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //ISSUE*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1444259957493*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //01*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //08-10-2015*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //08-10-2015*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //;*
    $msg[$k+=1] = ""; //*1
    $msg[$k+=1] = ""; //*2
    $msg[$k+=1] = ""; //*3
    $msg[$k+=1] = ""; //*4
    $msg[$k+=1] = ""; //*5
    $msg[$k+=1] = ""; //*6
    $msg[$k+=1] = ""; //*7
    $msg[$k+=1] = ""; //*8
    $msg[$k+=1] = ""; //*9
    $msg[$k+=1] = ""; //*10
    $msg[$k+=1] = ""; //*11
    $msg[$k+=1] = ""; //*12
    $msg[$k+=1] = ""; //*13
    $msg[$k+=1] = ""; //*14
    $msg[$k+=1] = ""; //*15
    $msg[$k+=1] = ""; //*16
    $msg[$k+=1] = ""; //*17
    $msg[$k+=1] = ""; //*18
    $msg[$k+=1] = ""; //*19
    $msg[$k+=1] = ""; //*20
    $msg[$k+=1] = ""; //*21
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //4ANQCX*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //-*
    $msg[$k+=1] = ""; //*1
    $msg[$k+=1] = ""; //*2
    $msg[$k+=1] = ""; //*3
    $msg[$k+=1] = ""; //*4
    $msg[$k+=1] = ""; //*5
    $msg[$k+=1] = ""; //*6
    $msg[$k+=1] = ""; //*7
    $msg[$k+=1] = ""; //*8
    $msg[$k+=1] = ""; //*9
    $msg[$k+=1] = ""; //*10
    $msg[$k+=1] = ""; //*11
    $msg[$k+=1] = ""; //*12
    $msg[$k+=1] = ""; //*13
    $msg[$k+=1] = ""; //*14
    $msg[$k+=1] = ""; //*15
    $msg[$k+=1] = ""; //*16
    $msg[$k+=1] = ""; //*17
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1430000*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1381000*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1381000*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*1
    $msg[$k+=1] = ""; //*2
    $msg[$k+=1] = ""; //*3
    $msg[$k+=1] = ""; //*4
    $msg[$k+=1] = ""; //*5
    $msg[$k+=1] = ""; //*6
    $msg[$k+=1] = ""; //*7
    $msg[$k+=1] = ""; //*8
    $msg[$k+=1] = ""; //*9
    $msg[$k+=1] = ""; //*10
    $msg[$k+=1] = ""; //*11
    $msg[$k+=1] = ""; //*12
    $msg[$k+=1] = ""; //*13
    $msg[$k+=1] = ""; //*14
    $msg[$k+=1] = ""; //*15
    $msg[$k+=1] = ""; //*16
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPSW*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1430000*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //389125057*
    $msg[$k+=1] = ""; //*

    // if ($msg[6] == "BS0004") {
    //     $result = array();
    //     $result[7] = "PAYMENT*TPJT*1069622512*10*20160613115708*ADMIN*FA2027*------*------*689729*ISSUE*1465792438767*TPJT*0*CGK*PNK*14-Jun-2016*06-Feb-2017*1*0*0*03*******ADT;MR;AGUNG;YULIANSYAH;;;::6281316631464;::6281316631464;;;;anugerahusaha@yahoo.com;KTP;ID**********************KAJWSI*-*JT714*13:50*15:20*******13-Jun-2016*13-Jun-2016 14:54*2016-06-13*bimasaktiduadua*KAJWSI**2016-06-14**528000**473000*473000*0*0**********ADT;MR;AGUNG;YULIANSYAH;;;::6281316631464;::6281316631464;;;;anugerahusaha@yahoo.com;KTP;ID*******KAJWSI*-**528000*0*1**lion*516118840*00*SUCCESS AUTOMATIC";
    //     $params = $result;
    //     return new xmlrpcresp(php_xmlrpc_encode($params));
    // }
    $fm = convertFM($msg, "*");
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);

//$respon = array();$respon[7] = "PAYMENT*TPSJ*720313645*10*20151125105448*DESKTOP*FA50415*------*------*4400017*ISSUE*1448420516126*TPSJ*0*UPG*MKW*30-Nov-2015*30-Nov-2015*1*0*0*03***0**25-11-2015*25-11-2015*;**********************NFLCTN*-*SJ584*02:30*06:20*******25-Nov-2015*25-Nov-2015 13:03*2015-11-25**NFLCTN**2015-11-30**2090000*149750*1850000*1997500*0*0*****************TPSW***2090000*100000*0**TPSJ*412516645*60*BERHASIL PAYMENT, ISSUE TIKET SEDANG DALAM PROSES, CEK STATUS ISSUE MELALUI MENU CEK TIKET.";
    $params = $respon;
    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function flightgetprice($m) {
    $msg = array();
    $i = -1;
    $k = -1;
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //RESERVASI*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //646930842*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //DESKTOP*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //FA73930*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //------*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //------*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //FARE*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1444259957493*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //SUB*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //CGK*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2/2/2015*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //2/2/2015*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //01*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = $m->getParam($i+=1)->scalarval(); //1*rute
    $msg[$k+=1] = $m->getParam($i+=1)->scalarval(); //0*transit
    $msg[$k+=1] = $m->getParam($i+=1)->scalarval(); //*
    $msg[$k+=1] = ""; //*
//    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //;*
    $msg[$k+=1] = ";";
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //TPGA*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //1381000*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = ""; //*
    $msg[$k+=1] = strtoupper($m->getParam($i+=1)->scalarval()); //0*
    /* log---------- */
    $loglines = "";
    foreach ($msg as $key => $value) {
        $loglines = $loglines . $value . "*";
    }
    //pendfile($loglines);
    /* .............. */
    $fm = convertFM($msg, "*");
    appendfile("Send to core : " . $fm);
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $step = 1;
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    appendfile("Receive from core : " . $respon[7]);
    $params = $respon;

    /* log---------- */

    appendfile(print_r($params, true));
    /* .............. */


    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function appendfile($loglines) {
    try {
        $file = getcwd() . "/logs/" . date("Ymd") . '.log';
        if (file_exists($file) == false) {
            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);
            fclose($handle);
        }
        file_put_contents($file, $loglines . "\n", FILE_APPEND | LOCK_EX);
    } catch (Exception $e){

    }
}

function harga($m) {
    // die($m);
    //HARGA*KDPRODUK*MID*STEP*TANGGAL*VIA*OPERATORPULSA*IDOUTLET*PIN*TOKEN
    $i = -1;

    //   $kdproduk = strtoupper($m->getParam($i+=1)->scalarval());
    $op_pulsa = strtoupper($m->getParam($i+=1)->scalarval());
    $idoutlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());
    $op_pulsa_temp = $op_pulsa;
     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }
    if($op_pulsa == "AXIS" || $op_pulsa == "XL"){
        $op_pulsa = "AXIS / XL";
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
    $msg[$i+=1] = $op_pulsa;
    $msg[$i+=1] = $idoutlet;
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";

    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $respon = postValue($fm);
    // print_r($respon);
    $resp = $respon[7];

    // $resp = "SAL*SAL*201321555*4**DESKTOP*" . $id_outlet . "***257390*127382587*00*Saldo Anda saat ini = Rp 257,390 Sms center 081228899888 dan 087838395999.";
    //$resp = "HARGA*HARGA*223838779*4**DESKTOP*TELKOMSEL*" . $idoutlet . "*" . $pin. "*------*325311*249734860*00*S15 (0)  TELKOMSEL SIMPATI / AS 15RB     Rp.15,700,;S5 (5000)    TELKOMSEL SIMPATI / AS 5RB  Rp.6,100,;S10 (10000)   TELKOMSEL SIMPATI / AS 10RB     Rp.11,100,;S20 (20000)  TELKOMSEL SIMPATI / AS 20RB     Rp.20,500,;S25 (25000)  TELKOMSEL SIMPATI / AS 25RB     Rp.25,550,;S50 (50000)  TELKOMSEL SIMPATI / AS 50RB     Rp.49,950,;S100 (100000)    TELKOMSEL SIMPATI / AS 100RB    Rp.98,450;Sms center 081228899888, 085730320058 dan 087838395999.";

    $format = FormatMsg::cekHarga();
    $frm = new FormatCekHarga($format[1], $resp);

    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_balance = $frm->getSaldo();

    $r_keterangan = $frm->getKeterangan();
    $r_status = strpos(strtolower($r_keterangan), 'tidak ditemukan') !== false ? '99' : $frm->getStatus();

    if (strpos($r_keterangan, '&#9;') !== false) {
        $r_keterangan = str_replace('&#9;', " ", $r_keterangan);
    }

    $params = array(
        $r_idoutlet, $r_pin, $r_balance, $r_status, $r_keterangan
    );

    return new xmlrpcresp(php_xmlrpc_encode($params));
}

function inqTaspen($m) {
    $i = -1;
    $tagihan = "TAGIHAN";
    $kode_produk = "ASRTPENJLN";
    $mid = $GLOBALS["mid"]; //1;

    $step = "1";
    $via = "XML";
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());

    $ip_mac_add = "--;--;nul;nul";

     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

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
    $msg[$i+=1] = $pin; //pin
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ip_mac_add;
    for ($k = $i; $k <= 54; $k++) {
    	$msg[$k] = "";
    }
    $fm = convertFM($msg, "*");

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
    return new xmlrpcresp(php_xmlrpc_encode($params));
}


function payTaspen($m) {
    $i = -1;
    $tagihan = "BAYAR";
    $kode_produk = "ASRTPENJLN";
    $mid = $GLOBALS["mid"]; //1;                                              //"453759285";
    $step = "1";
    $via = "XML";
    $id_ktp = strtoupper($m->getParam($i+=1)->scalarval());                 //"0151518190688000";
    $id_outlet = strtoupper($m->getParam($i+=1)->scalarval());
    $pin = strtoupper($m->getParam($i+=1)->scalarval());                    //"BS0004";
    $nominal = strtoupper($m->getParam($i+=1)->scalarval());                //"10000";
    $id_trx_inq = strtoupper($m->getParam($i+=1)->scalarval());             //"326101943";
    $ip_mac_add = "--;--;nul;nul";
    $umum = "PESAWAT";
    $nama_tertanggung = strtoupper($m->getParam($i+=1)->scalarval());       //"Andi Yusanto";
    $tgl_lahir_tertanggung = strtoupper($m->getParam($i+=1)->scalarval());  //"1988-06-19";
    $tgl_keberangkatan = strtoupper($m->getParam($i+=1)->scalarval());      //"2015-06-01 00:00";
    $telp_tertanggung = strtoupper($m->getParam($i+=1)->scalarval());       //"085649492115";
    $telp_ahli_waris = strtoupper($m->getParam($i+=1)->scalarval());        //"085649492115";
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
        }
    }

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

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon = postValue($fm);
    $params = $respon;
    return new xmlrpcresp(php_xmlrpc_encode($params));
}
