<?php
error_reporting(0);

date_default_timezone_set('Asia/Jakarta');
set_time_limit(120);

// include_once("lib/xmlrpc.inc");
// include_once("lib/xmlrpcs.inc");
// include_once("lib/xmlrpc_wrappers.inc");

// include_once("lib/phpxmlrpc/xmlrpc.inc");

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
//koneksi ke database postgre
global $pgsql;
$pgsql      = pg_connect("host=" . $__CFG_dbhost . " port=" . $__CFG_dbport . " dbname=" . $__CFG_dbname . " user=" . $__CFG_dbuser . " password=" . $__CFG_dbpass);

$msg        = $HTTP_RAW_POST_DATA;
$host       = $_SERVER['REMOTE_ADDR'];

$mid = getNextMID();
// $mid        = 1; //buat local
$step       = 1;
$raw_msg    = $_GET;

//generate write to log and replace pin
$rawcpy 			= $raw_msg;
$rawcpy['pin'] 		= '------';
$rawjson 			= json_encode($rawcpy);
$sender             = "XML CLIENT";
$receiver           = $GLOBALS["__G_module_name"];
$via                = $GLOBALS["__G_via"];
writeLog($mid, $step, $host, $receiver, $rawjson, $via);


$method = $raw_msg['method'];
switch ($method) {
    case "balance":
        echo balance($raw_msg);
        break;
    case "harga":
        echo harga($raw_msg);
        break;
    case "inq":
        echo inq($raw_msg);
        break;
    case "pay":
        echo pay($raw_msg);
        break;
    case "paydetail":
        echo pay_detail($raw_msg);
        break;
    case "pulsa":
        echo pulsa($raw_msg);
        break;
    case "game":
        echo game($raw_msg);
        break;
    case "bpjsinq":
        echo bpjs_inq($raw_msg);
        break;
    case "bpjspay":
        echo bpjs_pay($raw_msg);
        break;
    case "cu":
        echo cetak_ulang($raw_msg);
        break;
    case "cudetail":
        echo cetak_ulang_detail($raw_msg);
        break;
    case "cekip":
        echo cek_ip($raw_msg);
        break;
    case "info_produk":
        echo info_produk($raw_msg);
        break;
    case "datatransaksi":
        echo data_transaksi($raw_msg);
        break;
	case "cekstatus":
        echo cekstatus($raw_msg);
        break;
    default :
        echo'Produk tidak dikenal';
}

function normalisasiIdPel1PLNPasc($idpel1) {
    $ret = array("idpel1" => $idpel1);
    $is_valid_idpel1 = false;
    if (strlen($idpel1) > 12) {
        $idpel1_length = strlen($idpel1) - 12;
        $idpel1 = substr($idpel1, $idpel1_length);
    } else {
        
    }
    $ret = array("idpel1" => $idpel1);
    return $ret;
}

function cekstatus($req){
	$i = -1;
    $tgl 		= strtoupper($req['tgl']);
    $ref1 		= strtoupper($req['ref1']);
    $idtrx 		= strtoupper($req['ref2']);
    $idproduk 	= strtoupper($req['produk']);
    $idpel1 	= strtoupper($req['idpel1']);
    $idpel2 	= strtoupper($req['idpel2']);
    $denom 		= strtoupper($req['denom']);
    $idoutlet 	= strtoupper($req['uid']);
    $pin 		= strtoupper($req['pin']);
    $field      = 10;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    global $pgsql;

//    if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
//        die("");
//    }
    if ($idproduk == "PLNPASC") {
        $idproduk = "PLNPASCH";
    } else if ($idproduk == "PLNPRA") {
        //$idproduk = "PLNPRAH";
        $idproduk = "PLNPRAID";
    } else if ($idproduk == "PLNNON") {
        $idproduk = "PLNNONH";
    } else if ($idproduk == "HPTSEL") {
        $idproduk = "HPTSELH";
    } else if ($idproduk == "TELEPON") {
        $idpel1 = $idpel1.$idpel2;
        $idpel2 = "";
    }

    
    if (in_array($idproduk, KodeProduk::getPLNPrepaids())) {
        $normal_idpel = normalisasiIdPel1PLNPra($idpel1, $idpel2);
        $idpel1 = $normal_idpel["idpel1"];
        $idpel2 = $normal_idpel["idpel2"];
    }

    if (in_array($idproduk, KodeProduk::getPLNPostpaids())) {
        $normal_idpel = normalisasiIdPel1PLNPasc($idpel1);
        $idpel1 = $normal_idpel["idpel1"];
    }

    if($idproduk == 'TELEPON' || $idproduk == 'SPEEDY'){
        $cek_telkom = cek_is_telp_or_speedy($idpel1);
        $idproduk   = $cek_telkom['produk'];
    }
    
    if ($ref1 != "" || $idtrx != "" || $idpel1 != "" || $idpel2 != "" || $denom != "") {

        $data = getStatusProsesTransaksi($tgl, $idproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);

        $cnt = count($data);
        if ($cnt > 0) {
            if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPrepaids())) {
                $kdproduk = "PLNPRA";
                $sn = (string) trim($data["bill_info29"]);
            } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPostpaids())) {
                $kdproduk = "PLNASC";
                $sn = "";
            } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNNontaglists())) {
                $kdproduk = "PLNNON";
                $sn = "";
            } else if(trim($data["id_produk"]) == "SPEEDY" || trim($data["id_produk"]) == "TELEPON" ) {
                $kdproduk = "TELEPON";
            } else {
                $kdproduk = (string) trim($data["id_produk"]);
                $sn = (string) trim($data["bill_info5"]);
            }
            $add_data = array("IDTRANSAKSI" => (string) trim($data["id_transaksi"]),
                "TRANSAKSIDATETIME" => (string) trim($data["time_request"]),
                "KODEPRODUK" => (string) $kdproduk,
                "IDPELANGGAN1" => (string) trim($data["bill_info1"]),
                "IDPELANGGAN2" => (string) trim($data["bill_info2"]),
                "NOMINAL" => (string) trim($data["nominal"]),
                "NOMINALADMIN" => (string) trim($data["nominal_admin"]),
                "SN" => (string) $sn);
            $status = "00";
            $ket = "SEDANG DIPROSES";
        } else {

            $data = getStatusTransaksi($tgl, $idproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);
            $cnt = count($data);
            if ($cnt > 0) {
                if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPrepaids())) {
                    $kdproduk = "PLNPRA";
                    $sn = (string) trim($data["bill_info29"]);
                } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPostpaids())) {
                    $kdproduk = "PLNASC";
                    $sn = "";
                } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNNontaglists())) {
                    $kdproduk = "PLNNON";
                    $sn = "";
                } else if(trim($data["id_produk"]) == "SPEEDY" || trim($data["id_produk"]) == "TELEPON" ) {
                    $kdproduk = "TELEPON";
                } else {
                    $kdproduk = (string) trim($data["id_produk"]);
                    $sn = (string) trim($data["bill_info5"]);
                }


                $add_data = array("IDTRANSAKSI" => (string) trim($data["id_transaksi"]),
                    "TRANSAKSIDATETIME" => (string) trim($data["time_request"]),
                    "KODEPRODUK" => (string) $kdproduk,
                    "IDPELANGGAN1" => (string) trim($data["bill_info1"]),
                    "IDPELANGGAN2" => (string) trim($data["bill_info2"]),
                    "NOMINAL" => (string) trim($data["nominal"]),
                    "NOMINALADMIN" => (string) trim($data["nominal_admin"]),
                    "SN" => (string) $sn);
                $status = (string) trim($data["response_code"]);
                $ket = (string) trim($data["keterangan"]);
            } else {
                $data = getStatusTransaksiBackup($tgl, $idproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);
                $cnt = count($data);
                if ($cnt > 0) {
                    if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPrepaids())) {
                        $kdproduk = "PLNPRA";
                        $sn = (string) trim($data["bill_info29"]);
                    } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPostpaids())) {
                        $kdproduk = "PLNASC";
                        $sn = "";
                    } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNNontaglists())) {
                        $kdproduk = "PLNNON";
                        $sn = "";
                    } else if(trim($data["id_produk"]) == "SPEEDY" || trim($data["id_produk"]) == "TELEPON" ) {
                        $kdproduk = "TELEPON";
                    } else {
                        $kdproduk = (string) trim($data["id_produk"]);
                        $sn = (string) trim($data["bill_info5"]);
                    }

                    $add_data = array("IDTRANSAKSI" => (string) trim($data["id_transaksi"]),
                        "TRANSAKSIDATETIME" => (string) trim($data["time_request"]),
                        "KODEPRODUK" => (string) $kdproduk,
                        "IDPELANGGAN1" => (string) trim($data["bill_info1"]),
                        "IDPELANGGAN2" => (string) trim($data["bill_info2"]),
                        "NOMINAL" => (string) trim($data["nominal"]),
                        "NOMINALADMIN" => (string) trim($data["nominal_admin"]),
                        "SN" => (string) $sn);
                    $status = (string) trim($data["response_code"]);
                    $ket = (string) trim($data["keterangan"]);
                } else {

                    $status = "99";
                    $ket = "Transaksi dengan kriteria yang dimaksud tidak ditemukan";
                    $add_data = array("IDTRANSAKSI" => (string) trim($data["id_transaksi"]),
                        "TRANSAKSIDATETIME" => (string) "",
                        "KODEPRODUK" => (string) "",
                        "IDPELANGGAN1" => (string) "",
                        "IDPELANGGAN2" => (string) "",
                        "NOMINAL" => (string) "",
                        "NOMINALADMIN" => (string) "",
                        "SN" => (string) "");
                }
            }
        }
    } else {
        $status = "99";
        $ket = "Transaksi dengan kriteria yang dimaksud tidak ditemukan";
        $add_data = array("IDTRANSAKSI" => (string) trim($data["id_transaksi"]),
            "TRANSAKSIDATETIME" => (string) "",
            "KODEPRODUK" => (string) "",
            "IDPELANGGAN1" => (string) "",
            "IDPELANGGAN2" => (string) "",
            "NOMINAL" => (string) "",
            "NOMINALADMIN" => (string) "",
            "SN" => (string) "");
    }
    
    if (strpos($ket, "SUKSES OLEH") === 0) {
        $ket = (string) "SUKSES";
    }

    if (in_array($idproduk, KodeProduk::getPLNPrepaids()) && $status == "03" && strpos($ket, "sudah pernah terjadi status sukses")) {
        $key_token = "TOKEN=";
        $pos_token = strpos($ket, $key_token);
        $token = "";
        if ($pos_token > 0) {
            $token = substr($ket, $pos_token + strlen($key_token), 20);
        }
        $add_data["SN"] = $token;
    }
    
    if(!checkpin($idoutlet, $pin)){
        $add_data = array(
            "IDTRANSAKSI" => (string) "",
            "TRANSAKSIDATETIME" => (string) "",
            "KODEPRODUK" => (string) "",
            "IDPELANGGAN1" => (string) "",
            "IDPELANGGAN2" => (string) "",
            "NOMINAL" => (string) "",
            "NOMINALADMIN" => (string) "",
            "SN" => (string) ""
        );
        $params = array(
            (String) $tgl, (String) $ref1, (String) $idtrx, (String) $idproduk, (String) $idpel1, (String) $idpel2, (String) $denom, (String) $idoutlet, (String) "----", (String) '02', (String) 'Pin yang Anda Masukkan Salah', $add_data
        );
    } else {
        $params = array(
            (String) $tgl, (String) $ref1, (String) $idtrx, (String) $idproduk, (String) $idpel1, (String) $idpel2, (String) $denom, (String) $idoutlet, (String) "----", (String) $status, (String) trim(str_replace('.', '', $ket))
        );    
    }

    $implode = implode('.', $params);
    $implode_detail='';
    $final_implode = $implode;
    if (count($add_data) > 0) {
        $implode_detail = implode('.', $add_data);
        $final_implode = $implode.'.'.$implode_detail;
    }

    return $final_implode;
}

function cetak_ulang_detail($req) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $idoutlet   = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']);
    $ref2       = strtoupper($req['ref2']);
    $field      = 5;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130"  ) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    global $pgsql;
    if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
        return "IP Anda [$ip] tidak punya hak akses";
    }

    $stp        = $GLOBALS["step"] + 1;
    $msg        = array();
    $i          = -1;
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
    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list       = $GLOBALS["sndr"];
    //if($list[strtoupper($idoutlet)] && in_array($GLOBALS["host"],$list[strtoupper($idoutlet)])){
    $respon     = postValue($fm);
    //print_r($respon);
    $resp       = $respon[7];

    writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);
    
    $man        = FormatMsg::mandatoryPayment();
    $frm        = new FormatMandatory($man["pay"], $resp);
    $kdproduk   = $frm->getKodeProduk();

    $r_saldo_terpotong  = "";
    $r_nama_pelanggan   = "";
    $r_periode_tagihan  = "";
    $r_reff3            = "0";

    if ($frm->getStatus() == "00") {
        $frm = getParseProduk($kdproduk, $resp);
        //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

        $nom_up             = getnominalup($r_idtrx);
        //die($nom_up);
        $r_saldo_terpotong  = $r_nominal + $r_nominaladmin + ($nom_up);

        $r_nama_pelanggan   = getNamaPelanggan($kdproduk, $frm);
        $r_periode_tagihan  = getBillPeriod($kdproduk, $frm);

        if (substr($frm->getKodeProduk(), 0, 6) == "PLNPRA") {
            $r_reff3 = $frm->getTokenPln();
        }
    }
    //echo $resp;
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
    /* $r_saldo_terpotong = $r_nominal + $r_nominaladmin;
    $r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
    $r_periode_tagihan = getBillPeriod($kdproduk,$frm); */

    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk
        $url        = enkripUrl(strtoupper($idoutlet), $ref2);
        $url_struk  = "https://202.43.173.234/struk/?id=" . $url;
    }

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"           	=> (string) $r_idpel1,
        "IDPEL2"           	=> (string) $r_idpel2,
        "IDPEL3"           	=> (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) trim(str_replace('.', '', $r_nama_pelanggan)),
        "PERIODE"           => (string) $r_periode_tagihan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => (string) $r_reff3,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) str_replace('.', '[dot]', $url_struk),
    );
    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);

    $implode = implode('.', $params);
    $implode_detail='';
    $final_implode = $implode;
    if (count($r_additional_datas) > 0) {
        $implode_detail = implode('.', $r_additional_datas);
        $final_implode = $implode.'.'.$implode_detail;
    }

    $is_return = true;
    return $final_implode;
}

function cetak_ulang($req) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $idoutlet   = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']);
    $ref2       = strtoupper($req['ref2']);
    $field      = 5;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    $ip = $_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    global $pgsql;
    if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
        die("");
    }

    $stp        = $GLOBALS["step"] + 1;
    $msg        = array();
    $i          = -1;
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
    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    //echo $msg."<br>";
    $list       = $GLOBALS["sndr"];
    //if($list[strtoupper($idoutlet)] && in_array($GLOBALS["host"],$list[strtoupper($idoutlet)])){
    $respon     = postValue($fm);
    //print_r($respon);
    $resp       = $respon[7];
    writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);

    $man        = FormatMsg::mandatoryPayment();
    $frm        = new FormatMandatory($man["pay"], $resp);
    $kdproduk   = $frm->getKodeProduk();

    $r_saldo_terpotong  = "";
    $r_nama_pelanggan   = "";
    $r_periode_tagihan  = "";
    $r_reff3            = "0";

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
    /* $r_saldo_terpotong = $r_nominal + $r_nominaladmin;
    $r_nama_pelanggan = getNamaPelanggan($kdproduk,$frm);
    $r_periode_tagihan = getBillPeriod($kdproduk,$frm); */

    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk
        $url        = enkripUrl(strtoupper($idoutlet), $ref2);
        $url_struk  = "https://202.43.173.234/struk/?id=" . $url;
    }

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"           	=> (string) $r_idpel1,
        "IDPEL2"           	=> (string) $r_idpel2,
        "IDPEL3"           	=> (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) trim(str_replace('.', '', $r_nama_pelanggan)),
        "PERIODE"           => (string) $r_periode_tagihan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => (string) $r_reff3,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) str_replace('.', '[dot]', $url_struk),
    );

    $implode = implode('.', $params);
    return $implode;
}

function bpjs_pay($req){
	$i              = -1;
    $kdproduk       = strtoupper($req['produk']);
    $idpel1         = strtoupper($req['idpel']);
    $periodebulan   = strtoupper($req['periode']);
    $hp             = strtoupper($req['no_hp']);
    $nominal        = strtoupper($req['nominal']);
    $idoutlet       = strtoupper($req['uid']);
    $pin            = strtoupper($req['pin']);
    $ref1           = strtoupper($req['ref1']);
    $ref2           = strtoupper($req['ref2']);
    $field          = 10;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    $mti        = "BAYAR";
    $ceknom     = getNominalTransaksi($ref2);
    $arr        = array('10', '11', '12');
    $stp        = $GLOBALS["step"] + 1;
    $msg        = array();
    $i          = -1;
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
    $msg[$i+=1] = "";           //KETERANGAN

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

    $fm         = convertFM($msg, "*");    

    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
     
    $resp = "BAYAR*ASRBPJSKS*789645675*11*20160107130800*WEB*8888801851523593***64986*5000*".$idoutlet."*".$pin."**1427760*3*15*080002*432635479*00*EXT: APPROVE*0605 *924000000432635479*8888801851523593*2*1*924000000432635479*DJASILAH **000000064986******924000000432635479*BPJS Kesehatan***750**000000000000*082175633485***";

    $man        = FormatMsg::mandatoryPayment();
    $frm        = new FormatMandatory($man["pay"], $resp);
    $frm        = getParseProduk($kdproduk, $resp);
    //die($resp);

    $r_kdproduk         = $frm->getKodeProduk();
    $r_tanggal          = $frm->getTanggal();
    $r_idpel1           = $frm->getIdPel1();
    $r_idpel2           = $frm->getIdPel2();
    $r_idpel3           = $frm->getIdPel3();
    $r_nominal          = (int) $frm->getNominal();
    $r_nominaladmin     = (int) $frm->getNominalAdmin();
    $r_idoutlet         = $frm->getMember();
    $r_pin              = $frm->getPin();
    $r_sisa_saldo       = $frm->getSaldo();
    $r_idtrx            = $frm->getIdTrx();
    $r_status           = $frm->getStatus();
    $r_keterangan       = $frm->getKeterangan();
    $r_saldo_terpotong  = 0;
    $r_nama_pelanggan   = getNamaPelanggan($kdproduk, $frm);

    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);

    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk
        //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

        $nom_up = getnominalup($r_idtrx);

        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

        $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk = "https://202.43.173.234/struk/?id=" . $url;
    }

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"           => (string) $r_idpel1,
        "IDPEL2"           => (string) $r_idpel2,
        "IDPEL3"           => (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
        "PERIODE"           => (string) $periodebulan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => (string) $r_reff3,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) $r_keterangan,
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) str_replace('.', '[dot]', $url_struk),
    );

    $implode = implode('.', $params);
    $implode_detail='';
    $final_implode = $implode;
    if (count($r_additional_datas) > 0) {
        $implode_detail = implode('.', $r_additional_datas);
        $final_implode = $implode.'.'.$implode_detail;
    }

    $is_return = true;
    return $final_implode;
}

function bpjs_inq($req){
	$i              = -1;
    $kdproduk       = strtoupper($req['produk']);
    $idpel1         = strtoupper($req['idpel']);
    $periodebulan   = strtoupper($req['periode']);
    $idoutlet       = strtoupper($req['uid']);
    $pin            = strtoupper($req['pin']);
    $ref1           = strtoupper($req['ref1']);
    $field          = 7;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    global $pgsql;
    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         return "IP Anda [$ip] tidak punya hak akses";
    //     }
    // }

    $stp        = $GLOBALS["step"] + 1;
    $msg        = array();
    $i          = -1;
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
    $msg[$i+=1] = "";

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


    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];

    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];

    $resp = "TAGIHAN*ASRBPJSKS*789644896*11*20160107130731*WEB*8888801851523593***000000064986*5000*".$idoutlet."*".$pin."**1497746*3*15*080002*432635265*00*EXT: APPROVE*0605 *LUBUK LINGGAU *8888801851523593*2***DJASILAH **000000064986*******BPJS Kesehatan***750**000000000000*0***";

    
    $man    = FormatMsg::mandatoryPayment();
    $frm    = new FormatMandatory($man["inq"], $resp);
    $frm    = getParseProduk($kdproduk, $resp);

    //$r_command        = $frm->getCommand();
    $r_kdproduk     = $frm->getKodeProduk();
    $r_tanggal      = $frm->getTanggal();
    //$r_via                = $frm->getVia();
    $r_idpel1       = $frm->getIdPel1();
    $r_idpel2       = $frm->getIdPel2();
    $r_idpel3       = $frm->getIdPel3();
    $r_name         = $frm->getCustomerName();
    $r_nominal      = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet     = $frm->getMember();
    $r_pin          = $frm->getPin();
    $r_saldo        = $frm->getSaldo();
    $r_idtrx        = $frm->getIdTrx();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();
    
    $nom_up             = getnominalup($r_idtrx);
    $r_saldo_terpotong  = $r_nominal + $r_nominaladmin + ($nom_up);

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"           	=> (string) $r_idpel1,
        "IDPEL2"           	=> (string) $r_idpel2,
        "IDPEL3"           	=> (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) trim(str_replace('.', '', $r_name)),
        "PERIODE"           => (string) $periodebulan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => "0",
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_saldo,
        "URL_STRUK"         => (string) str_replace('.', '[dot]', $url_struk),
    );
    // print_r($params);

    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;
    return implode('.', $params);
}

function harga($req){
	$i = -1;

    $produk = strtoupper($req['produk']);
    $uid    = strtoupper($req['uid']);
    $pin    = strtoupper($req['pin']);
    $field  = 4;
    if(count((array)$req) !== $field){
        return 'missing parameter request';
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
        $r_keterangan = str_replace('&#9;', " ", $r_keterangan);
    }

    $params = array(
        "UID"       => $r_idoutlet,
        "PIN"       => '------',
        "SALDO"     => $r_balance,
        "STATUS"    => $r_status,
        "KET"       => $r_keterangan
    );

    return implode('.', $params);
}

function info_produk($req){
	$i          = -1;

    $id_produk  = strtoupper($req['produk']);
    $id_outlet  = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $field      = 4;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
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

function cek_ip($req) {
    $output = array(
        "IP"    => $_SERVER['REMOTE_ADDR']
    );
    return implode('.', $output);
}

function c($data){
	return htmlentities(trim($data), ENT_QUOTES, 'UTF-8');
}

function inq($req){
	$i = -1;
    $kdproduk   = strtoupper($req['produk']);
    $idpel1     = strtoupper($req['idpel1']);
    $idpel2     = strtoupper($req['idpel2']);
    $idpel3     = strtoupper($req['idpel3']);
    $idoutlet   = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']);
    $field      = 8;
    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    global $pgsql;

    if ($kdproduk == "PLNPASC") {
        $kdproduk = "PLNPASCH";
    } else if ($kdproduk == "PLNPRA") {
        $kdproduk = "PLNPRAH";
    } else if ($kdproduk == "HPTSEL") {
        $kdproduk = "HPTSELH";
    } else if ($kdproduk == "PLNNON") {
        $kdproduk = "PLNNONH";
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
    $msg[$i+=1] = "";
    if($kdproduk == "PAJAKGRTLT" || $kdproduk == "PAJAKGRTL"){
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = $idpel1;
    }
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];

    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
    
    if($kdproduk == "PGN"){
        $resp = "TAGIHAN*PGN*2977389418*10*20181030161145*WEB*0110011437***140520*2500*" . $idoutlet . "*" . $pin . "*------*8433793*1**PGN*1161888229*00*SUKSES*0110011437*L HUTAGALUNG*26 M3*Sep2018*INV1181030141660*140520*2500*143020****  ";
    } else if($kdproduk == "KKBNI"){
        $resp = "TAGIHAN*KKBNI*2656828520*9*20180530073732*H2H*5489888810362324****6000*" . $idoutlet . "*" . $pin . "**19459795*1**BNI*1016819889*00*Sukses!*0559*00*5489888810362324*01***DADANG ISKANDAR SKM********BNI*14052018*03062018****490300* ";
    } else if ($kdproduk == "TELEPON") {
        if($idpel1 == "0711" && $idpel2 == "410771"){
            $resp = "TAGIHAN*TELEPON*2877030373*10*20180911194415*H2H*0711*410771**000000000000*2500*" . $idoutlet . "*" . $pin . "**125060932*1**001001*1114391397*82*EXT: RESPONSE RECEIVED TOO LATE/TRANSAKSI DITOLAK KARENA TIDAK ADA JAWABAN DARI HOST TELKOM*0711*000410771* * *0**0**0**0* * ";
        } else if($idpel1 == "021" && $idpel2 == "114266714"){
            $resp = "TAGIHAN*TELEPON*2876623701*10*20180911162342*H2H*021*114266714**000000000000*2500*" . $idoutlet . "*" . $pin . "**76650616*1**001001*1114187945*88*EXT: TRANSAKSI DITOLAK KARENA SEMUA ATAU SALAH SATU TUNGGAKAN/TAGIHAN SUDAH DIBAYAR*021*114266714*1* *0**0**0**0* * ";
        } else if($idpel1 == "8884" && $idpel2 == "062"){
            $resp = "TAGIHAN*TELEPON*2878262698*10*20180912131232*H2H*8884*062**000000000000*2500*" . $idoutlet . "*" . $pin . "**81104025*1**001001*1114997147*96*EXT: SYSTEM MALFUCTION/TRANSAKSI DITOLAK KARENA TERJADI ERROR DI HOST TELKOM*0888*400000006* * *0**0**0**0* *    ";
        } else if($idpel1 == "031" && $idpel2 == "99148365"){
            $resp = "TAGIHAN*TELEPON*2877312754*10*20180912001820*H2H*031*99148365**000000000000*2500*" . $idoutlet . "*" . $pin . "**90128052*1**001001*1114527432*91*EXT: ISSUER OR SWITCH IS INOPERATIVE*031*099148365* * *0**0**0**0* * ";
        } else if($idpel1 == "1311" && $idpel2 == "8217908"){
            $resp = "TAGIHAN*TELEPON*2873340942*10*20180910025904*H2H*1311*8217908**000000000000*2500*" . $idoutlet . "*" . $pin . "**107852013*1**001001*1112613415*14*EXT: NOMOR TELEPON/IDPEL TIDAK TERDAFTAR*0131*100821790* * *0**0**0**0* *   ";
        } else if($idpel1 == "021" && $idpel2 == "86908050"){
            $resp = "TAGIHAN*TELEPON*2878264837*9*20180912131346*H2H*021*86908050**000000000000*2500*" . $idoutlet . "*" . $pin . "**1614917944*1**001001*1114998196*77*EXT: TRANSAKSI DITOLAK KARENA NOMOR TELEPON YANG DIMAKSUD TELAH MELAMPAUI BATAS MAKSIMUM JUMLAH BILL (MAX. 3)*021*086908050* * *0**0**0**0* *   ";
        } else {
            $resp = "TAGIHAN*TELEPON*12670412*11*20120524125202*DESKTOP*021*88393209**000000137580*7500*" . $idoutlet . "*" . $pin . "*D4B6EA34*550308*1*0*001001*18156560*00*APPROVE*021*088393209*02*0008*3*203A       *50860*204A       *45860*205A       *40860* LINA SIREGAR                 *               ";
        }
        
    } else if ($kdproduk == "SPEEDY") {
        if ($idpel1 == "141148100225") {
            $resp = "TAGIHAN*SPEEDY*72138003*11*20121212092229*DESKTOP*141148100225***000000364113*5000*" . $idoutlet . "*" . $pin . "*------*523207*1*0*001001*49484131*00*APPROVE*0141*148100225*04*0001*2**0*211A       *149613*212A       *214500* MARIYANTO                    *";
        } else if($idpel1 == "03199603047") {
            $resp = "TAGIHAN*SPEEDY*2878085604*10*20180912113655*H2H*03199603047***000000000000*2500*" . $idoutlet . "*" . $pin . "**88860944*1**001001*1114914621*96*EXT: SYSTEM MALFUCTION/TRANSAKSI DITOLAK KARENA TERJADI ERROR DI HOST TELKOM*003*199603047* * *0**0**0**0* *  ";
        } else if($idpel1  == "152409305976"){
            $resp = "TAGIHAN*SPEEDY*2877341417*10*20180912012639*H2H*152409305976***000000000000*2500*" . $idoutlet . "*" . $pin . "**53833799*1**001001*1114540996*91*EXT: ISSUER OR SWITCH IS INOPERATIVE*0152*409305976* * *0**0**0**0* *   ";
        } else if($idpel1  == "021117048679"){
            $resp = "TAGIHAN*SPEEDY*2876602429*9*20180911161050*H2H*021117048679***000000000000*2500*" . $idoutlet . "*" . $pin . "**1961619660*1**001001*1114177811*88*EXT: TRANSAKSI DITOLAK KARENA SEMUA ATAU SALAH SATU TUNGGAKAN/TAGIHAN SUDAH DIBAYAR*021*117048679*1* *0**0**0**0* * ";
        } else if($idpel1 == "152605200181"){
            $resp = "TAGIHAN*SPEEDY*2878199394*9*20180912123759*MOBILE_SMART*152605200181***000000000000*2500*" . $idoutlet . "*" . $pin . "*------*310659*2**001001*1114967658*77*EXT: TRANSAKSI DITOLAK KARENA NOMOR TELEPON YANG DIMAKSUD TELAH MELAMPAUI BATAS MAKSIMUM JUMLAH BILL (MAX. 3)*0152*605200181* * *0**0**0**0* *   ";
        } else if($idpel1 == "0012142420410"){
            $resp = "TAGIHAN*SPEEDY*2878217897*9*20180912124856*H2H*0012142420410****2500*SP31560*------**185676463*1**001001*1114976132*68*Response received too late/Transaksi ditolak karena tidak ada jawaban dari Host Telkom*0012*142420410***********    ";
        } else {
            $resp = "TAGIHAN*SPEEDY*12633157*11*20120524111916*XML*162406900527***000000744750*2500*" . $idoutlet . "*" . $pin . "*A7E252CC*1944782*0*0*001001*18142362*00*APPROVE*0162*406900527*06*0006*1**0**0*205A       *744750* GEREJA GBI NANGA BUL         *               ";
        }
    } else if ($kdproduk == "TVTLKMV") {
        if ($idpel1 == "122429250104") {
            $resp = "TAGIHAN*TVTLKMV*71149922*11*20121209114313*DESKTOP*122429250104***000000287500*5000*" . $idoutlet . "*" . $pin . "*------*590654*1*0*001001*48864121*00*APPROVE*0122*429250104*02*0004*2**0*211A       *150000*212A       *137500* ANDRY PRAMONO                *";
        } else {
            $resp = "TAGIHAN*TVTLKMV*12632078*11*20120524111650*XML*127246500157***000000099000*1950*" . $idoutlet . "*" . $pin . "*D3A84F25*384250*1*0*001001*18141775*00*APPROVE*0127*246500157*08*0001*1**0**0*205A       *99000*BAITUS MONGJENG               *               ";
        }
    } else if (substr($kdproduk, 0,6) == 'PLNNON') {
        if($idpel1 == "3234043001254"){
            $resp = "TAGIHAN*PLNNON*2879716775*9*20180913082444*WEB*3234043001254****5000*" . $idoutlet . "*" . $pin . "*------*1599576*1**053504*1115689899*77*EXT: NOMOR REGISTRASI YANG ANDA MASUKKAN SALAH, MOHON TELITI KEMBALI.*0000000*3234043001254******************************   ";
        } else if($idpel1 == "3236012032301"){
            sleep(40);
            $resp = "TAGIHAN*PLNNON*2878424680*8*20180912144136*WEB*3236012032301****5000*" . $idoutlet . "*" . $pin . "*------*49776635*2**053504*1115073539*68*EXT: WAKTU TRANSAKSI HABIS, COBA BEBERAPA SAAT LAGI*0000000*3236012032301****************************** ";
        } else if($idpel1 == "1231270001469"){
            $resp = "TAGIHAN*PLNNON*2876192812*9*20180911114808*WEB*1231270001469****5000*" . $idoutlet . "*" . $pin . "*------*258811*3**053504*1113999625*34*EXT: TAGIHAN SUDAH TERBAYAR*0000000*1231270001469******************************  ";
        } else if($idpel1 == "88755441288073589"){
            $resp = "TAGIHAN*PLNNON*2878475262*10*20180912150719*H2H*88755441288073589****5000*" . $idoutlet . "*" . $pin . "*------*4760691*1*16*99504*1115097634*31*EXT: KODE BANK TIDAK TERDAFTAR*0000000*88755441288073589******************************    ";
        } else if($idpel1 == "3274012023886"){
            $resp = "TAGIHAN*PLNNON*2875505865*9*20180911003417*WEB*3274012023886****5000*" . $idoutlet . "*" . $pin . "*------*146787*4**053504*1113673737*06*EXT: TRANSAKSI GAGAL*0000000*3274012023886****************************** ";
        }else {
            $resp = "TAGIHAN*PLNNON*12643076*10*20120524114027*MOBILE*5392112011703***696400*1600*" . $idoutlet . "*" . $pin . "**1214244*1*1*99504*18146993*00*SUCCESSFUL*0000000*5392112011703                   *53*012*PENYAMBUNGAN BARU        *20120524*23062012*542122123488*JAYUSMAN                 *083349C5AB7B4738A3EBE7352CDA9E6A*62D9201911A4437188E8C1734539FE0C*53921*Jl Pahlawan No 39 Rangkasbitung    *123            *2*00000000069800000*2*00000000069800000*2*0000160000*0101200000000000000000***********";
        }
    } else if (substr($kdproduk, 0,7) == "PLNPASC") {
        if($idpel1=="773360001351"){
           $resp = "TAGIHAN*PLNPASC*73848704*11*20121217145857*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------******77*EXT: IDPEL YANG ANDA MASUKKAN SALAH MOHON TELITI KEMBALI*********************************************************************";
       }else if($idpel1=="544053873188"){
           $resp = "TAGIHAN*PLNPASC*73848704*11*20121217145857*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*------*------******88*EXT: Tagihan sudah terbayar*********************************************************************";
       
       }else if($idpel1=="323360001335"){    
            //untuk simulator case 12 dan case 14 untuk inq saja
           $resp = "TAGIHAN*PLNPASC*73848704*11*20121217145857*DESKTOP*".$idpel1."***53535*5700*" . $idoutlet . "*-----*------*2269165*1*1*99501*50512928*00*SUCCESSFUL*0000000*".$idpel1."*3*3*03*338EA23D53B24A90A2D6F3D746207EA4*DUMMY PLNPASCA BAYAR*32330*123*R1*000000450*000004800*201210*20102012*00000000*00000014685*D0000000000*0000000000*000009000*00006200*00010400*00000000*00000000*00000000*00000000*201211*20112012*00000000*00000014289*D0000000000*0000000000*000006000*00010400*00014500*00000000*00000000*00000000*00000000*201212*20122012*00000000*00000009561*D0000000000*0000000000*000000000*00014500*00017300*00000000*00000000*00000000*00000000******************";
       }else if($idpel1=="523520409670"){
        
           $resp="TAGIHAN*PLNPASC*811131285*10*20160120083516*H2H*523520409670***185840*2500* . $idoutlet . *------*------*-779871725*1**99501*438881388*00*SUCCESSFUL*VI105V3*523520409670*1*1*01*EE6990FC73554BBFAC58107EB7C1734C*AHMAT MUALIP             *52352*123            *  R1*000000900*000002500*201601*20012016*00000000*00000185840*D0000000000*0000000000*000000000*02691200*02723300*00000000*00000000*00000000*00000000********************************************";
       }else{
            if ($idpel1 == "323360001351") {//sukses 3 bulan
                $resp = "TAGIHAN*PLNPASC*73848704*11*20121217145857*DESKTOP*323360001351***5353500000*5700*" . $idoutlet . "*" . $pin . "*------*2269165*1*1*99501*50512928*00*SUCCESSFUL*0000000*323360001351*3*3*03*338EA23D53B24A90A2D6F3D746207EA4*MILLI.M                  *32330*123            *  R1*000000450*000004800*201210*20102012*00000000*00000014685*D0000000000*0000000000*000009000*00006200*00010400*00000000*00000000*00000000*00000000*201211*20112012*00000000*00000014289*D0000000000*0000000000*000006000*00010400*00014500*00000000*00000000*00000000*00000000*201212*20122012*00000000*00000009561*D0000000000*0000000000*000000000*00014500*00017300*00000000*00000000*00000000*00000000******************";
            }
            ///untuk simulator 
            else if($idpel1 == '535310570222'){
                //untuk simulator case 11
                sleep(40);
                //$resp = "TAGIHAN*".$kdproduk."*831295610*10*".$tanggal."*H2H*".$idpel1."*".$idpel2."***2500*".$idoutlet."*------*------*10696292*1**99501*444430174*68*EXT: WAKTU TRANSAKSI HABIS, COBA BEBERAPA SAAT LAGI*0000000*".$idpel1."*******************************************************************";
                $resp = "TAGIHAN*".$kdproduk."***".$tanggal."*H2H*".$idpel1."*".$idpel2."****".$idoutlet."*------*------****************************************************************************";
            } else if($idpel1 == '535310570000'){
                //untuk simulator case 11
                sleep(40);
                $resp = "TAGIHAN*".$kdproduk."*831295610*10*".$tanggal."*H2H*".$idpel1."*".$idpel2."***2500*".$idoutlet."*------*------*10696292*1**99501*444430174*68*EXT: WAKTU TRANSAKSI HABIS, COBA BEBERAPA SAAT LAGI*0000000*".$idpel1."*******************************************************************";
            } 
             ///untuk simulator 
            else if($idpel1 == "521050065222"){
                $resp = "TAGIHAN*PLNPASCH*1100053868*10*20160630153919*H2H*521050065222***116368*2500*" . $idoutlet . "*" . $pin . "*------*19518466708*1**99501*525590515*00*SUCCESSFUL*VI105V3*521050065222*1*1*01*2F4838A2A9844E9DA48828E344F6D761*TUKIYO *52105*123 * R1*000000450*000002500*201606*20062016*00000000*00000113368*D0000000000*0000000000*000003000*01234700*01257700*00000000*00000000*00000000*00000000********************************************";
            } else if($idpel1 == "151000042568"){
                $resp = "TAGIHAN*PLNPASC*2114335491*10*20171016093714*WEB*151000042568***1081168*2500*" . $idoutlet . "*" . $pin . "*------*1301870*1**501*835567984*00*TRANSAKSI SUKSES*0000000*151000042568*1*1*01**SAHADI *15100*123 * R1*000002200*000000000*201710*20102017*00000000*00001081168*D0000000000*0000000000*000000000*07472500*07564600*00000000*00000000*00000000*00000000*****************************************5EC3F36854EC4B258E3990222F5E5741**1081168* ";
            } else if($idpel1 == "513030034100"){
                $resp = "TAGIHAN*PLNPASCH*2119801728*10*20171018104448*H2H*513030034100***29314918*2500*" . $idoutlet . "*" . $pin . "*------*39273686*1**501*837525931*00*TRANSAKSI SUKSES*0000000*513030034100*1*1*01**PROY DTC SONGGORITI *51303*123 * P1*000082500*000000000*201710*20102017*00000000*00029314918*D0000000000*0000000000*000000000*05106200*05172800*00000000*00000000*00000000*00000000*****************************************E4AC6E62191943F697BE884DB7AEC82E**29314918*    ";
            } else if($idpel1 == "534210870159"){
                $resp = "TAGIHAN*PLNPASCH*2704864563*10*20180621133412*H2H*534210870159***528874*3000*" . $idoutlet . "*" . $pin . "*------*1276864852*1**501*1037282114*00*TRANSAKSI SUKSES*0000000*534210870159*1*1*01**UJANG NANA SUYATNA *53421*123 * R1M*000000900*000000000*201806*22062018*00000000*000000528874*D0000000000*0000000000*000000000000*00017103*00017477*00000000*00000000*00000000*00000000*****************************************E34E62F70CB4482F845EAD65C476B8AF**528874* ";
            } else if($idpel1 == "321209707325"){
                $resp = "TAGIHAN*PLNPASCH*2704942546*10*20180621141118*H2H*321209707325***365877*3000*" . $idoutlet . "*" . $pin . "*------*961745484*1**501*1037312283*00*TRANSAKSI SUKSES*0000000*321209707325*1*1*01**ST MAEMUNAH *32111*123 * R1M*000000900*000000000*201806*22062018*00000000*000000365877*D0000000000*0000000000*000000000000*00001467*00001711*00000000*00000000*00000000*00000000*****************************************9B5FF8539C4C4FA2921C07FEBE4CAA2B**365877* ";
            } else if($idpel1 == "122030400910") {
                //3bulan
                $resp = "TAGIHAN*PLNPASC*2776317419*10*20180724111742*H2H*122030400910***89108*5000*" . $idoutlet . "*" . $pin . "*------*-257461301*1**501*1067800142*00*TRANSAKSI SUKSES*0000000*122030400910*2*2*02**AMRAN *12203*123 * R1*000000450*000000000*201806*22062018*00000000*000000046316*D0000000000*0000000000*000000006000*00006208*00006311*00000000*00000000*00000000*00000000*201807*20072018*00000000*000000033792*D0000000000*0000000000*000000003000*00006311*00006391*00000000*00000000*00000000*00000000****************************30432B94343D42C3A05F29C58450C886**89108*    ";
            } else {
                $resp = "TAGIHAN*PLNPASC*12631852*11*20120524111623*XML*538731734541***887849*12000*" . $idoutlet . "*" . $pin . "*A76361C0*8711705*1*1*99501*18141640*00*SUCCESSFUL*0000000*538731734541*4*4*04*E4D91664E29448128F0E52E68D0215CC*R SUMALI2                *53873*123            *  R1*000000900*000012000*201202*20022012*00000000*00000029247*D0000000000*0000001000*000009000*00873500*00876600*00000000*00000000*00000000*00000000*201203*20032012*00000000*00000232713*D0000000000*0000001000*000009000*00876600*00919900*00000000*00000000*00000000*00000000*201204*20042012*00000000*00000287208*D0000000000*0000001000*000006000*00919900*00973300*00000000*00000000*00000000*00000000*201205*20052012*00000000*00000311681*D0000000000*0000001000*000003000*00973300*01031500*00000000*00000000*00000000*00000000*****";
            }
       }
//          
    } else if (substr($kdproduk, 0,6) == 'PLNPRA') {
       if($idpel1=="22000703735" || $idpel2=="221082522378"){
         $resp = "TAGIHAN*PLNPRA*673998745*10*20151026021720*H2H*".$idpel1."*".$idpel2."***0*" . $idoutlet . "*" . $pin . "*------******77*EXT: IDPEL YANG ANDA MASUKKAN SALAH MOHON TELITI KEMBALI**********************************";  
       }else if($idpel1=="22000703768" || $idpel2=="221082522368"){
           sleep(40);
         $resp = "TAGIHAN*PLNPRA****H2H*".$idpel1."*".$idpel2."***0*" . $idoutlet . "*" . $pin . "*------*****************************************";  
       }else if ($idpel1=="34018062991"){
           $resp="TAGIHAN*PLNPRA*811399468*10*20160120102012*H2H*34018062991*537316468946*085718674423**2500*" . $idoutlet . "*------*------*143010167*1**99502*438987540*00*SUCCESSFUL*VI105V3*34018062991*537316468946*1*00000000000000000000000000000000*8CC0A100F79A4F57BBC594DEC52EAAF4**NENENG MARTINI*R2*000003500*2*0000250000**53*53731*123*02520*0****************";
       }else{
            if($idpel1 == '86000703735' || $idpel2=="521082522378"){
                $resp = "TAGIHAN*PLNPRA*673998745*10*20151026021720*H2H*86000703735*521082522378***2500*" . $idoutlet . "*" . $pin . "*------*232064791*1**99502*397931219*00*SUCCESSFUL*1*86000703735*521082522378*0****REANANDA HIDAYAT PERMONO*R1*1300*0*2500****123******************";
            } else if($idpel1 == '01117082246' || $idpel2=="511061245422"){
                $resp = "TAGIHAN*PLNPRA*12995997*11*20120525130039*DESKTOP*01117082246*511061245422***1600*" . $idoutlet . "*" . $pin . "*D5430F79*207963*1*1*99502*18253704*00*SUCCESSFUL*0000000*01117082246*511061245422*0*988776546B2D482781B583F2A2FD5D76*C9745AE21CA1408280AF6888D1FE7164**KARTO SOEWITO*R1*000002200*2*0000160000**51*51106*123*01584*0****************";
            } else if($idpel1 == '32900717920' || $idpel2=="535550153156"){
                //nominal 5juta
                $resp = "TAGIHAN*PLNPRAY*2117922367*10*20171017160251*H2H*32900717920*535550153156***2500*" . $idoutlet . "*" . $pin . "*------*2570857659*1**053502*836862504*00*TRANSAKSI SUKSES*JTL53L3*32900717920*535550153156*0*9907A48E4275411F8A6816218AC1134E*0BMS210Z935B109F4AB49F1FB4F003C8**PT SASTRA DAJA*B2*000033000****53*53555*123*23760*0****************    ";
            } else if($idpel1 == '14281206905'){  
                $resp = "TAGIHAN*PLNPRAH*2252397156*10*20171212073141*H2H*14281206905**6281282426768**2500*" . $idoutlet . "*" . $pin . "*------*239242632*1**053502*881237050*63*EXT: KONSUMEN 14281206905 DIBLOKIR HUBUNGI PLN*0000000*14281206905********************************";
            } else if($idpel1 == '14011951093'){
                $resp = "TAGIHAN*PLNPRAH*2249643191*4*20171210235907*H2H*14011951093**628129445270**2500*" . $idoutlet . "*" . $pin . "**2392385*1***880264665*13*Transaksi ditolak karena sistem sedang melakukan proses cut off (23.55 - 00.10)**********************************";
            } else if($idpel2 == '142142055263'){
                $resp = "TAGIHAN*PLNPRAH*2252296355*10*20171212062124*H2H**142142055263*6282268843605**2500*" . $idoutlet . "*" . $pin . "*------*265787009*1**053502*881195045*14*EXT: IDPEL YANG ANDA MASUKKAN SALAH, MOHON TELITI KEMBALI*0000000**142142055263*1******************************";
            } else if($idpel1 == "22122891504"){
                $resp = "TAGIHAN*PLNPRAH*2249641108*10*20171210235256*H2H*22122891504**6282268843605**2500*" . $idoutlet . "*" . $pin . "*------*2392385*1**053502*880263851*13*EXT: APPLICATION SERVER IS DOWN*0000000*22122891504******************************** ";
            } else if($idpel1 == "34007336802"){
                $resp = "TAGIHAN*PLNPRAH*2703746664*10*20180621001725*H2H*34007336802*537613097710*6281287929134**3000*" . $idoutlet . "*" . $pin . "*------*-87679662*1**99502*1036812431*00*SUCCESSFUL*506CA01*34007336802*537613097710*0*00000000000000000000000000000000*20EF4BB481F3420F8EBCC809D548E785**ERMA*R1*000001300*2*0000300000**53*53761*123*00936*0**************** ";
            } else if($idpel1 == "32100768574"){
                $resp = "TAGIHAN*PLNPRA*2704732747*10*20180621123326*H2H*32100768574*213301053103**500000*2500*" . $idoutlet . "*" . $pin . "*------*203801635*1**053502*1037231567*00*TRANSAKSI SUKSES*JTL53L3*32100768574*213301053103*0*BD9612A39B7A4D5C8D4AD884F0A5551E*0BMS210Z57CBAAEA71780B67F9B45CBF**AGUSTINA*R1*000000450****21*21330*123*00324*0****************  ";
            } else {
                 $resp = "TAGIHAN*PLNPRA*12995997*11*20120525130039*DESKTOP*01117082246*511061245422***1600*" . $idoutlet . "*" . $pin . "*D5430F79*207963*1*1*99502*18253704*00*SUCCESSFUL*0000000*01117082246*511061245422*0*988776546B2D482781B583F2A2FD5D76*C9745AE21CA1408280AF6888D1FE7164**KARTO SOEWITO*R1*000002200*2*0000160000**51*51106*123*01584*0****************";
            }
       }
    } else if($kdproduk == "WABDGBAR"){
        $resp = "TAGIHAN*WABDGBAR*2811506650*9*20180810173253*H2H*0102000763***38500*2500*" . $idoutlet . "*" . $pin . "**100634630*1**pmgs*1083981179*00*Sukses*0000000*00*0102000763***1**R2*Achmad Zaini Miftah*Perum GPI Jl. Berlian No.45****PDAM Kab Bandung Barat*7*2018*694*705*0*38500*0***********************************    ";
    } else if($kdproduk == "WASAMPANG"){
        $resp = "TAGIHAN*WASAMPANG*2813306201*10*20180811144312*H2H*01003923*0102040126*01/II /004/0126/A*77968*5000*" . $idoutlet . "*" . $pin . "**-830994402*1**WASAMPANG*1084796924*00*SUKSES*0000000*00*01003923*0102040126*01/II /004/0126/A*2***CHUSNUL HOTIMAH*MUTIARA **0**PDAM TRUNOJOYO SAMPANG*6*2018*0*1*7088*35440*0*7*2018*0*1*0*35440*0**************************** ";
    } else if ($kdproduk == "WALOMBOKT") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WALOMBOKT*380860342*10*20150408102355*DESKTOP*012100676***51650*2500*".$idoutlet."*" . $pin . "*------*845489*3**400311*303146002*00*SUCCESSFUL*0000000*0*012100676***1*102350---08042015*0000000050*SYAHNUN**201503*000000002500**PDAM Kab. Lombok Tengah*03*2015***0*51650************************************";
    } else if($kdproduk == "WABATAM"){
        $resp = "TAGIHAN*WABATAM*2803949068*9*20180807092452*DESKTOP*52693***23180*2500*".$idoutlet."*".$pin."*------*9790710*1**2029*1080537185*00*EXT: APPROVE*0000000*OTORITA BATAM*52693*52693*52693*01**OTORITA BATAM*OTORITA BATAM*****PAM ATB BATAM*08*2018***0*23180*0***********************************   ";
    } else if($kdproduk == "WASUMED"){
        $resp = "TAGIHAN*WASUMED*2802786664*9*20180806175119*MOBILE_SMART*3104014051***73900*2500*".$idoutlet."*".$pin."*------*575470*2**400631*1079958533*00*SUCCESSFUL*1079958533*1*3104014051***1*175115---06082018*SWITCHERID*SUBANA**null---null---null*000000002500**PDAM Kab Sumedang*07*2018***0*73900*0***********************************    ";
    } else if ($kdproduk == "WAKBMN") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAKBMN*325080573*11*20150216181009*MOBILE_SMART*05014240008***27500*2000*".$idoutlet."*" . $pin . "*------*------*1**------*------*00*SUCCESSFUL*0000000*1*05014240008***1*181008---16022015*0000008001*IBU SAILAH**20151*000000002000**PDAM KEBUMEN*1*2015***0*27500************************************";
    } else if ($kdproduk == "WAPBLINGGA") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAPBLINGGA*375520059*10*20150404063054*DESKTOP*14050151***103260*2000*" . $idoutlet . "*" . $pin . "*------*1493724*3**400271*301406435*00*SUCCESSFUL*0000000*0*14050151***1*063207---04042015*0000008001*MAS'UT NUR H.**201503*000000002250**PDAM PURBALINGGA*03*2015***0*103260************************************";
    } else if ($kdproduk == "WASLTIGA") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WASLTIGA*377721650*10*20150406065034*DESKTOP*02b9289***24400*2000*" . $idoutlet . "*" . $pin . "*------*3581105*1**400321*302081945*00*SUCCESSFUL*0000000*0*02b9289***1*080638---06042015*0000008001*SUPENO**201503*000000002000**PDAM KOTA SALATIGA*03*2015***0*24400************************************";
    } else if ($kdproduk == "WAGROBGAN") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAGROBGAN*377613437*10*20150405214339*DESKTOP*1201000301***000000027000*2000*" . $idoutlet . "*" . $pin . "*------*188829*1**1021033*302049814*00*SUCCESSFUL*0000000*0*1201000301***01*20150405043000007032**SRI HARTISAH SPD.***0000002000**PDAM KAB. GROBONGAN*3*2015*002286*002286     *0*000000027000************************************";
    } else if ($kdproduk == "WABANJAR") { // ID PELANGGAN
//         2 BLN
        $resp = "TAGIHAN*WABANJAR*373045368*10*20150402080836*DESKTOP*4027386***389380*4000*" . $idoutlet . "*" . $pin . "*------*618687*3**400231*300699846*00*SUCCESSFUL*0000000*0*4027386***2*080950---02042015*0000008001*DINA PUJIATI**201502,201503*000000004000**PDAM BANJARMASIN*02*2015***0*209310**03*2015***0*180070*****************************";
    } else if ($kdproduk == "WASRKT") { // ID PELANGGAN
//         3 BLN
        $resp = "TAGIHAN*WASRKT*372701434*10*20150401200532*DESKTOP*00046902***102400*5100*" . $idoutlet . "*" . $pin . "*------*135718*2**400251*300606023*00*SUCCESSFUL*0000000*1*00046902***3*200141---01042015*0000008001*Wahono**201503,201502,201501*000000005100**PDAM SURAKARTA*03*2015***0*32000**02*2015***3200*32000**01*2015***3200*32000**********************";
    } else if ($kdproduk == "WAPURWORE") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAPURWORE*373604198*10*20150402123006*DESKTOP*01033200271***75800*1600*" . $idoutlet . "*" . $pin . "*------*91718*2**400211*300819084*00*SUCCESSFUL*0000000*0*01033200271***1*123006---02042015*0000008001*Pranoto Suwignyo**201503*000000001600**PDAM PURWOREJO*03*2015***0*75800************************************";
    } else if ($kdproduk == "WABYL") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WABYL*370983319*10*20150331154847*DESKTOP*02120025***227350*2000*" . $idoutlet . "*" . $pin . "*------*825747*1**400081*300070270*00*SUCCESSFUL*0000000*0*02120025***1*154849---31032015*0000008001*MULYATMIN**201502*000000002000**PDAM BOYOLALI*02*2015***0*227350************************************";
    } else if ($kdproduk == "WAKABBDG") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAKABBDG*372227386*10*20150401143925*DESKTOP*461195***88000*2000*" . $idoutlet . "*" . $pin . "*------*253188*1**400221*300458705*00*SUCCESSFUL*0000000*0*461195***1*143927---01042015*0000008001*TUNING RUDYATI**201504*000000002000**PDAM KAB. BANDUNG*04*2015***0*88000************************************";
    } else if ($kdproduk == "WAKNDL") { // ID PELANGGAN
//         2 BLN
        $resp = "TAGIHAN*WAKNDL*372763951*10*20150401205554*DESKTOP*0442060140***108200*3000*" . $idoutlet . "*" . $pin . "*------*1411122*3**400241*300624789*00*SUCCESSFUL*0000000*0*0442060140***2*205549---01042015*0000008001*Slamet Basuki**201502,201503*000000003000**PDAM KENDAL*02*2015***0*74200**03*2015***0*34000*****************************";
    } else if ($kdproduk == "WAWONOGIRI") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAWONOGIRI*373652299*10*20150402130534*MOBILE_SMART*02040172***79000*2000*" . $idoutlet . "*" . $pin . "*------*98002*0**400141*300832572*00*SUCCESSFUL*0000000*1*02040172***1*130249---02042015*0000008001*DRS SUPARNO**201503*000000002000**PDAM KAB. WONOGIRI*03*2015***0*79000************************************";
    } else if ($kdproduk == "WAIBANJAR") { // ID PELANGGAN
//         3 BLN
        $resp = "TAGIHAN*WAIBANJAR*370331499*10*20150331071909*DESKTOP*390804***435740*6000*" . $idoutlet . "*" . $pin . "*------*9785488*3**400401*299891381*00*SUCCESSFUL*0000000*0*390804***3*071911---31032015*0000008001*H.JUMBRANI**201502,201501,201412*000000006000**PDAM INTAN BANJAR*02*2015***0*65160**01*2015***0*192660**12*2014***0*177920**********************";
    } else if ($kdproduk == "WAGIRIMM") { // ID PELANGGAN
//         3 BLN
        $resp = "TAGIHAN*WAGIRIMM*371928757*10*20150401105546*DESKTOP*02-07-07330*02-07-07330**135650*7500*" . $idoutlet . "*" . $pin . "*------*3334605*3**400381*300380441*00*SUCCESSFUL*0000000*1*02-07-07330*02-07-07330**3*115808---01042015*0000008001*RUJAI**201501,201502,201503*000000007500**PDAM GIRI MENANG MATARAM*01*2015***10000*56900**02*2015***10000*25700**03*2015***0*33050**********************";
    } else if ($kdproduk == "WABULELENG") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WABULELENG*373519474*10*20150402112831*DESKTOP*02001775***37600*2000*" . $idoutlet . "*" . $pin . "*------*52274*1**400371*300794886*00*SUCCESSFUL*0000000*0*02001775***1*112835---02042015*437105*KETUT SUKANARA**201503*000000002500**PDAM KAB. BULELENG*03*2015***0*37600************************************";
    } else if ($kdproduk == "WABREBES") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WABREBES*373003775*10*20150402075234*DESKTOP*1601040330***47000*2500*" . $idoutlet . "*" . $pin . "*------*2598022*3**400341*300693445*00*SUCCESSFUL*0000000*0*1601040330***1*075229---02042015*0000008001*Hersodo**201503*000000002500**PDAM KAB. BREBES*03*2015***0*47000************************************";
    } else if ($kdproduk == "WAWONOSB") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAWONOSB*373039402*10*20150402080625*DESKTOP*0114120063***62020*2000*" . $idoutlet . "*" . $pin . "*------*479251*1**400331*300699024*00*SUCCESSFUL*0000000*0*0114120063***1*080628---02042015*0000008001*MOCH NASIR SUNYOTO**201503*000000002000**PDAM KAB. WONOSOBO*03*2015***0*62020************************************";
    } else if ($kdproduk == "WAMADIUN") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAMADIUN*371957091*10*20150401111459*DESKTOP*0208050150***79580*2500*" . $idoutlet . "*" . $pin . "*------*1529353*3**400261*300388297*00*SUCCESSFUL*0000000*0*0208050150***1*111614---01042015*0000008001*SLAMET AS**201503*000000002500**PDAM KOTA MADIUN*03*2015***0*79580************************************";
    } else if ($kdproduk == "WASRAGEN") { // ID PELANGGAN
//         2 BLN
        $resp = "TAGIHAN*WASRAGEN*371905541*10*20150401103958*DESKTOP*0800564***87000*3400*" . $idoutlet . "*" . $pin . "*------*227221*2**400181*300373672*00*SUCCESSFUL*0000000*0*0800564***2*093108---01042015*0000008001*WARTINAH A**201503,201502*000000003400**PDAM KAB. SRAGEN*03*2015***0*31250**02*2015***0*55750*****************************";
    } else if ($kdproduk == "WAKABSMG") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAKABSMG*372895672*10*20150402041804*DESKTOP*310050799***23140*2000*" . $idoutlet . "*" . $pin . "*------*47945*1**400201*300654359*00*SUCCESSFUL*0000000*0*310050799***1*041439---02042015*0000008001*BAGUS SETIAWAN**201504*000000002000**PDAM KAB. SEMARANG*04*2015***0*23140************************************";
    } else if ($kdproduk == "WABYMS") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WABYMS*379540798*10*20150407104222*DESKTOP*0124619***31540*2000*" . $idoutlet . "*" . $pin . "*------*4922712*0**400011*302720717*00*SUCCESSFUL*0000000*1*0124619***1*105501---07042015*0000008001*NANI WIBOWO**MAR15*000000002000**PDAM BANYUMAS*3*2015***0*31540************************************";
    } else if ($kdproduk == "WAHLSUNGT") { // ID PELANGGAN
//         2 BLN
        $resp = "TAGIHAN*WAHLSUNGT*370639571*10*20150331112645*DESKTOP*0300648***251200*4000*" . $idoutlet . "*" . $pin . "*------*259605*0**400361*299981329*00*SUCCESSFUL*0000000*0*0300648***2*112758---31032015*0000008001*LINA HERIANI**201501,201502*000000004000**PDAM Hulu Sungai Tengah*01*2015***0*106800**02*2015***0*144400*****************************";
    } else if ($kdproduk == "WAKARANGA") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAKARANGA*373051125*10*20150402081029*DESKTOP*0702011372***28000*2000*" . $idoutlet . "*" . $pin . "*------*2470332*2**400121*300700551*00*SUCCESSFUL*0000000*0*0702011372***1*081024---02042015*0000008001*SENEN**201503*000000002000**PDAM Karanganyar*03*2015***0*28000************************************";
    } else if ($kdproduk == "WAKPKLNGAN") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAKPKLNGAN*371624007*10*20150401070629*DESKTOP*0104010422***70550*2000*" . $idoutlet . "*" . $pin . "*------*682474*3**400101*300279891*00*SUCCESSFUL*0000000*0*0104010422***1*070624---01042015*0000008001*Moh. Abdullah**201503*000000002000**PDAM KAB. PEKALONGAN*03*2015***0*70550************************************";
    } else if ($kdproduk == "WAMAKASAR") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAMAKASAR*359960863*10*20150321075229*DESKTOP*199200987***000000043320*2500*" . $idoutlet . "*" . $pin . "*------*215755*1**1021014*296702929*00*SUCCESSFUL*0000000*0*199200987***01*20150321043000000699**M. NATSIR***0000002500**PDAM KOTA MAKASAR*2*2015*00003512*00003521 *0*000000043320************************************";
    } else if ($kdproduk == "WAKUBURAYA") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAKUBURAYA*357994155*10*20150319181227*DESKTOP*09400***000000053500*2000*" . $idoutlet . "*" . $pin . "*------*1477954*3**1021015*295847193*00*SUCCESSFUL*0000000*0*09400***01*20150319043000019664**CONG TJIN MOI***0000002000**PDAM KOTA KUBURAYA*2*2015*001215*001235     *0*000000053500************************************";
    } else if ($kdproduk == "WAPONTI") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAPONTI*357667418*10*20150319143243*DESKTOP*3060346***000000026100*1600*" . $idoutlet . "*" . $pin . "*------*885165*0**1021010*295720268*00*SUCCESSFUL*0000000*0*3060346***01*20150319043000015081**ARDIAN***0000001600**PDAM KOTA PONTIANAK (KALBAR)*2*2015*00000105*00000111 *0*000000026100************************************";
    } else if ($kdproduk == "WAMANADO") { // ID PELANGGAN
//         1 BLN
        $resp = "TAGIHAN*WAMANADO*357241901*10*20150319094356*DESKTOP*38671***000000074730*1600*" . $idoutlet . "*" . $pin . "*------*573634*3**1021009*295561365*00*SUCCESSFUL*0000000*0*38671***01*20150319043000006148**FATMA SAMBANG***0000001600**PDAM KOTA MANADO*2*2015*00000281*00000288 *0*000000074730************************************";
    } else if ($kdproduk == "WASITU") { // ID PELANGGAN
        if($idpel1 == "01/I /007/0641"){
            //non aktif
            $resp = "TAGIHAN*WASITU*809052107*10*20160119072306*H2H*01/I /007/0641****2000*" . $idoutlet . "*" . $pin . "**7398274*1**WASITU*438140999*05*EXT: PELANGGAN NON-AKTIF*0000000*00*01/I /007/0641***********PDAM SITUBONDO*******0***********************************";
        } else if($idpel1 == "01/I /004/007"){
            //tidak ada tagihan
            $resp = "TAGIHAN*WASITU*807348756*10*20160118085510*H2H*01/I /004/007****2000*" . $idoutlet . "*" . $pin . "**7834252*1**WASITU*437565294*08*EXT: TIDAK ADA TAGIHAN*0000000*00*01/I /004/007***********PDAM SITUBONDO*******0***********************************";
        } else if($idpel1 == "516500377874"){
            //salah format
            $resp = "TAGIHAN*WASITU*809160323*10*20160119082830*H2H*516500377874****2000*" . $idoutlet . "*" . $pin . "**14519223*1**WASITU*438183505*04*EXT: TIDAK DITEMUKAN NOMOR PELANGGAN. GUNAKAN FORMAT: 12/IV/123/1234/A1*0000000*00*516500377874***********PDAM SITUBONDO*******0***********************************";
        } else if($idpel1 == "01/I /007/1659/B1"){
            $resp = "TAGIHAN*WASITU*1692770471*10*20170425163604*DESKTOP*01/I /007/1659/B1*01/I /007/1659/B1*14001*107500*6000*".$idoutlet."*".$pin."*------*267059*1**WASITU*708528386*00*SUKSES*0000000*00*01/I /007/1659/B1*01/I /007/1659/B1*14001*3***SAMSUL HADI*JL. CEMPAKA PERUM ISMU H- 18 *#1:2017:5000:25000:0#2:2017:5000:0:0#3:2017:5000:0:0*25000**PDAM SITUBONDO*1*2017*0*5*0*22500*0*2*2017*0*10*0*22500*0*3*2017*0*10*40000*22500*0*********************    ";
        } else if($idpel1 == "01/IV /004/0612") {
            //1 BLN
            $resp = "TAGIHAN*WASITU*357731368*10*20150319151448*H2H*01/IV /004/0612*01/IV /004/0612/B1*7101*90070*2000*" . $idoutlet . "*" . $pin . "**9872878*1**WASITU*295742233*00*SUKSES*0000000*00*01/IV /004/0612/B1*01/IV /004/0612/B1*7101*1***MISTAM SOEKARDI*ARGOPURO No.GG16 *2:2015:0:0:0*0**PDAM SITUBONDO*2*2015*0*39*0*90070*0***********************************";
        } else {
            //sama dengan 1 bulan
            $resp = "TAGIHAN*WASITU*357731368*10*20150319151448*H2H*01/IV /004/0612*01/IV /004/0612/B1*7101*90070*2000*" . $idoutlet . "*" . $pin . "**9872878*1**WASITU*295742233*00*SUKSES*0000000*00*01/IV /004/0612/B1*01/IV /004/0612/B1*7101*1***MISTAM SOEKARDI*ARGOPURO No.GG16 *2:2015:0:0:0*0**PDAM SITUBONDO*2*2015*0*39*0*90070*0***********************************";
        }
    } else if ($kdproduk == "WAREMBANG") { // ID PELANGGAN    
        // 1 bulan;
        if ($idpel == "LA-03-00084") {
            $resp = "TAGIHAN*WAREMBANG*347270075*10*20150310145130*DESKTOP*LA-03-00084***46400*2000*" . $idoutlet . "*" . $pin . "*------*0*2**400301*291942562*00*SUCCESSFUL*0000000*0*LA-03-00084***1*145133---10032015*0000008001*MUSHOLLA AL MUBAROQ**201502*000000002000**PDAM Kab. Rembang*02*2015***0*46400************************************";
        } else {
            $resp = "TAGIHAN*WAREMBANG*347346522*10*20150310155253*DESKTOP*LA-03-00012***530000*4000*" . $idoutlet . "*" . $pin . "*------*0*2**400301*291968179*00*SUCCESSFUL*0000000*0*LA-03-00012***2*155255---10032015*0000008001*R A M I S I H**201502,201501*000000004000**PDAM Kab. Rembang*02*2015***0*269400**01*2015***0*260600*****************************";
        }
    } else if ($kdproduk == "WASLMN") { // ID PELANGGAN
        $resp = "TAGIHAN*WASLMN*327274183*10*20150218183900*H2H*1400669***60000*2500*" . $idoutlet . "*" . $pin . "**-228214716*0**400071*284869686*00*SUCCESSFUL*0000000*0*1400669***1*183847---18022015*0000008001*NADI KUSNADI**201501*000000001700**PDAM SLEMAN*01*2015***0*60000************************************";
    } else if ($kdproduk == "WASMG") { // ID PELANGGAN
       if($idpel1=="06620177"){
            $resp = "TAGIHAN*WASMG*333207314*10*20150224151810*DESKTOP*".$idpel1."***0*2000*" . $idoutlet . "*" . $pin . "*------**0****77*EXT: IDPEL YANG ANDA MASUKKAN SALAH MOHON TELITI KEMBALI********************************************************";
        }else if($idpel1=="06620168"){
            sleep(40);
            $resp = "TAGIHAN*WASMG****DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------**0*************************************************************";
        }else if($idpel1=="06620188"){
            $resp = "TAGIHAN*WASMG*333207314*10*20150224151810*DESKTOP*".$idpel1."***0*2000*" . $idoutlet . "*" . $pin . "*------**0****88*EXT: Tagihan sudah terbayar********************************************************";    
       
        }else if($idpel1=="07460079"){
            $resp = "TAGIHAN*WASMG*811402452*10*20160120102123*H2H*07460079***45700*2000*" . $idoutlet . "*------**4286956*1**87004*438988655*00*SUCCESS*GS10AG3*Rumah Tangga 3*07460079***1*F576BB3476214FC0B5D0000000000000*000438988655*Chr Sri Setyowati*Parang Barong 6/04*GS10AG3            07460079                    0       101B20B054DB71B4FAA87C1111222233330F576BB3476214FC0B5D0000000000000Chr Sri Setyowati             Parang Barong 6/04            Rumah Tangga 3                00000002000                    18BCA70A1CAA44C44BFB            2015120000000000000003620000000000000000000000000000000000000000005000000000002500000000002000000000000000=+=+=+=10000000000000026430000002658000000000015000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000*0000002000**PDAM KOTA SEMARANG*12*2015*2643*2658*0*36200*0|0|2000|5000|2500|0***********************************";    
       
        } else if($idpel1=="05430379"){
            //2bulan WASMG
            $resp = "TAGIHAN*WASMG*868045876*10*20160223103418*DESKTOP*05430379***188396*4000*" . $idoutlet . "*" . $pin . "*------*172425*0**87004*455524845*00*SUCCESS*GS10AG3*Rumah Tangga 4*05430379***2*29402BF700AE4048B830000000000000*000455524845*FX Samiyo (Rt.9/3)*Cikurai Brt Dlm 1 Kaligse*GS10AG3 05430379 0 20264A321222C3D4EE29D9111122223333029402BF700AE4048B830000000000000FX Samiyo (Rt.9/3) Cikurai Brt Dlm 1 Kaligse Rumah Tangga 4 00000004000 2F1F3EA1C8F7E424F923 2015120000000000000006186000000000000000000000000000006936000000005000000000002500000000005000000000000000 15204F5A0D1CD4C9C9E1 2016010000000000000009460000000000000000000000000000000000000000005000000000002500000000005000000000000000=+=+=+=200000000000000061200000006310000000000190000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000006310000000656000000000025000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000*0000004000**PDAM KOTA SEMARANG*12*2015*612*631,19*6936*61860*0|0|5000|5000|2500|0*01*2016*631*656,25*0*94600*0|0|5000|5000|2500|0****************************";
        } else if($idpel1 == '07020747'){
            $resp = "TAGIHAN*WASMG*1490036005*9*20170123094459*H2H*07020747***94005*4000*".$idoutlet."*".$pin."**1487245778*1**87004*652106053*00*SUCCESS*GS10AG3*Rumah Tangga 5*07020747***2*6E81BC0CE9054051B470000000000000*000652106053*Harimawan*Bimasakti 3/8-10**0000004000**PDAM KOTA SEMARANG*11*2016*84*84*3905*31550*0|0|6000|5000|2500|0*12*2016*84*84*0*31550*0|0|6000|5000|2500|0****************************";
        } else if($idpel1 == '06620111'){
        
            $resp = "TAGIHAN*WASMG*333207314*10*20150224151810*DESKTOP*06620111***000000036970*2000*" . $idoutlet . "*" . $pin . "*------*1177899*0**1061025*287052671*00*SUCCESSFUL*0000000*0*06620111***01*20150224043000012561*9819F39536B34ED79F60000000000000*Bunadi***0000002000**PDAM SEMARANG*1*15*0000000072 * 0000000084*0*000000036970************************************";
        
        }  else {
             $resp = "TAGIHAN*WASMG*333207314*10*20150224151810*DESKTOP*06620111***000000036970*2000*" . $idoutlet . "*" . $pin . "*------*1177899*0**1061025*287052671*00*SUCCESSFUL*0000000*0*06620111***01*20150224043000012561*9819F39536B34ED79F60000000000000*Bunadi***0000002000**PDAM SEMARANG*1*15*0000000072 * 0000000084*0*000000036970************************************";
        }
    } else if ($kdproduk == "WAKABMLG") { // ID ".$idoutlet."*".$pin."
        $resp = "TAGIHAN*WAKABMLG*406628*10*20150401153604*H2H*8101120001982***000000025000*1800*" . $idoutlet . "*" . $pin . "**318634507*0**1061032*238016366*00*SUCCESSFUL*0000000*0*8101120001982***01*20150401043000011022*1A5FD4B1F60D4A6D9FC0000000000000*YUGUS***0000002100**PDAM KAB. MALANG*4*2015*0000001816 * 0000001826*0*000000025000************************************";
    } else if ($kdproduk == "WABALIKPPN") { // ID PELANGGAN
        $resp = "TAGIHAN*WABALIKPPN*312450174*10*20150203062136*DESKTOP*01030010346***000000094375*1600*" . $idoutlet . "*" . $pin . "*------*1029014*0**1021008*279676046*00*SUCCESSFUL*0000000*0*01030010346***01*20150203043000000207**HJ.RUKAYAH***0000001600**PDAM KOTA BALIKPPN*1*2015*00000129*00000141 *0*000000094375************************************";
    } else if($kdproduk == "WABAL"){
        $resp = "TAGIHAN*WABAL*2887986200*9*20180917102013*DESKTOP*050316***76400*2500*" . $idoutlet . "*" . $pin . "*------*1960665*1**2033*1119467747*00*EXT: APPROVE*0000000*RIDUAN*050316*050316*050316*01**RIDUAN*RIDUAN*****PDAM BALANGAN*08*2018***0*76400*0***********************************   ";
    } else if ($kdproduk == "WABOGOR") { // ID PELANGGAN
        // 1 BLN
        $resp = "TAGIHAN*WABOGOR*303308580*10*20150123095026*DESKTOP*07411152***000000078540*2500*" . $idoutlet . "*" . $pin . "*------*2254934*1**1021030*276888092*00*SUCCESSFUL*0000000*0*07411152***01*20150123043000003809**AAS RAMAESIH***0000002500**PDAM KAB. BOGOR*12*2014*00000711*00000727 *0*000000078540************************************";
    } else if ($kdproduk == "WACLCP") { // ID PELANGGAN
        // 1 BLN
        if ($idpel1 == "0105041625") {
            $resp = "TAGIHAN*WACLCP*340812345*10*20150304160850*H2H*0105041625***000000269600*6000*" . $idoutlet . "*" . $pin . "**11422600*1**1021012*289596616*00*SUCCESSFUL*0000000*0*0105041625***03*20150304043000017000**ETI WIDIASTUTI***0000006000**PDAM CILACAP*12*2014*00002694*0*0*0**1*2015*0*0*0*0**2*2015*0*00002762 *0*000000269600**********************";
        } else {
            $resp = "TAGIHAN*WACLCP*302475161*10*20150122104811*DESKTOP*0309211394***000000103500*2000*" . $idoutlet . "*" . $pin . "*------*180710*1**1021012*276633360*00*SUCCESSFUL*0000000*0*0309211394***01*20150122043000006019**NY SUMINEM***0000002000**PDAM CILACAP*12*2014*00000320*00000347 *0*000000103500************************************";
        }
    } else if ($kdproduk == "WATAPIN") { // NO SAMBUNGAN
        // 1 BLN
        if($idpel2 == '080707'){
            $resp = "TAGIHAN*WATAPIN*252333044*10*20141202131903*DESKTOP**080707*707*44500*2000*" . $idoutlet . "*" . $pin . "*------*342435*1**WATAPIN*259564374*00**0000000*00**080707*707*1***GURU IKAS*BAKARANGAN **0**PDAM TAPIN*11*2014*0*10*0*44500*0***********************************";
        } else if($idpel2 == '011189'){
            $resp = "TAGIHAN*WATAPIN*1493778006*10*20170125084402*DESKTOP*0*011189*1189*710300*4000*" . $idoutlet . "*" . $pin . "*------*1517112*3**WATAPIN*653050324*00*SUKSES*0000000*00*0*011189*1189*2***RUSMADI*A.YANI BITAHAN **0**PDAM TAPIN*11*2016*0*87*5000*368600*0*12*2016*0*79*2500*334200*0****************************  ";
        }
    } else if ($kdproduk == "WALMPNG") { // ID PELANGGAN 
        // 2 BLN
        $resp = "TAGIHAN*WALMPNG*254362664*9*20141204104841*DESKTOP*010501*010501*010501*111440*5600*" . $idoutlet . "*" . $pin . "*------*1114119*0**2011*260186111*00*EXT: APPROVE*0000000*00*010501*010501*010501*2***BUSRON TOHA*****PDAM LAMPUNG*11*2014***0*62640*0*10*2014***5000*43800*0****************************";
    } else if ($kdproduk == "WAJAMBI") { // ID PELANGGAN 
        // 1 BLN
        $resp = "TAGIHAN*WAJAMBI*254418381*9*20141204113255*DESKTOP*03583*03583*03583*263750*2800*" . $idoutlet . "*" . $pin . "*------*3676604*1**2010*260203384*00*EXT: APPROVE*0000000*00*03583*03583*03583*1***ADMINISTRASI PELABUHAN*****PDAM JAMBI*12*2014***00000000*263750*0***********************************";
    } else if ($kdproduk == "WASDA") { // ID PELANGGAN && NO SAMBUNGAN
        if($idpel1 == "04002679" || $idpel2 == "01/II /004/0083/2D"){
            $resp = "TAGIHAN*WASDA*253012547*10*20141203081150*H2H*".$idpel1."*".$idpel2."**0*1800*" . $idoutlet . "*" . $pin . "*******04*EXT: Tidak ditemukan nomor pelanggan************0**PDAM SIDOARJO******************************************";
        }else if($idpel1 == "68002679" || $idpel2 == "01/II /068/0083/2D"){
            sleep(40);
            $resp = "TAGIHAN*WASDA***20141203081150*H2H*".$idpel1."*".$idpel2."*A*0*0*" . $idoutlet . "*" . $pin . "****************************************************************";    
            
        }else if($idpel1 == "08002679" || $idpel2 == "01/II /008/0083/2D"){
            $resp = "TAGIHAN*WASDA*253012547*10*20141203081150*H2H*".$idpel1."*".$idpel2."*AB130083*0*1800*" . $idoutlet . "*" . $pin . "*******08*EXT: Tidak ada tagihan************0**PDAM SIDOARJO******************************************";    
        }else if($idpel1=="02004159" || $idpel2=="02/I  /007/0147/2D"){
            $resp="TAGIHAN*WASDA*812314109*10*20160120170040*H2H*02004159*02/I  /007/0147/2D*BA070147*154100*3600*" . $idoutlet . "*------**-492108960*1**WASDA*439339531*00*SUKSES*0000000*00*02004159*02/I  /007/0147/2D*BA070147*2***SUKARNI*PERUM WISMA SARINADI III I-17**0**PDAM SIDOARJO*11*2015*0*18*7500*87300*0*12*2015*0*13*0*59300*0****************************";
        }else{
            if ($idpel1 == "01002679" || $idpel2 == "01/II /013/0083/2D") {
                // 3 BLN
                $resp = "TAGIHAN*WASDA*253012547*10*20141203081150*H2H*01002679*01/II /013/0083/2D*AB130083*138500*5400*" . $idoutlet . "*" . $pin . "**-198672693*1**WASDA*259775322*00**0000000*00*01002679*01/II /013/0083/2D*AB130083*3***PERM. BUMI CITRA FAJ*SEKAWAN SEJUK C.10A**0**PDAM SIDOARJO*9*2014*0*0*7500*40500*0*10*2014*0*0*7500*40500*0*11*2014*0*0*0*42500*0*********************";
            } else {
                // 6 BLN
                $resp = "TAGIHAN*WASDA*253012547*10*20141203081150*H2H*01002676*01/II /013/0083/6D*AB130086*294500*10800*" . $idoutlet . "*" . $pin . "**-198672693*1**WASDA*259775322*00*SUKSES*0000000*00*01002676*01/II /013/0083/6D*AB130083*6***PERM. BUMI CITRA FAJ*SEKAWAN SEJUK C.16A**0**PDAM SIDOARJO*5*2014*0*0*7500*40500*0*6*2014*0*0*7500*40500*0*7*2014*0*0*7500*42500*0*8*2014*0*0*7500*43500*0*9*2014*0*0*7500*44500*0*10*2014*0*0*0*45500*0";
            }
        }
    } else if ($kdproduk == "WABONDO") { // ID PELANGGAN && NO SAMBUNGAN
        // 3 BLN
        if($idpel1 == "02000285"){
            //sukses di tempat lain
            $resp = "TAGIHAN*WABONDO*807320881*10*20160118084049*H2H*02000285***0*1500*" . $idoutlet . "*" . $pin . "**78936904*1**FY834n7Vs4mdASP4H34n*437553704*07*EXT: IDPEL TELAH LUNAS DI TEMPAT LAIN.*0000000*00*02000285*********0**PDAM BONDOWOSO******************************************";
        } else if($idpel1 == "0400104"){
            //idpel tidak valid
            $resp = "TAGIHAN*WABONDO*806975257*10*20160117213456*H2H*0400104***0*2500*" . $idoutlet . "*" . $pin . "**-651246535*1**FY834n7Vs4mdASP4H34n*437451442*02*EXT: NO PELANGGAN SALAH (TIDAK VALID).*0000000*00*3983*********0**PDAM BONDOWOSO******************************************";
        } else if($idpel1 == "12000297"){
            //loket yang sama
            $resp = "TAGIHAN*WABONDO*808027290*10*20160118143224*H2H*12000297***0*2500*" . $idoutlet . "*" . $pin . "**-564577908*1**FY834n7Vs4mdASP4H34n*437805158*15*EXT: PELANGGAN LUNAS DI LOKET YANG SAMA.*0000000*00*12000297*********0**PDAM BONDOWOSO******************************************";
        } else if($idpel1 == "09000879") {
            //sukses
            $resp = "TAGIHAN*WABONDO*250810684*10*20141130205909*DESKTOP*09000879*09/01/001/00879/RB**94130*4500*" . $idoutlet . "*" . $pin . "*------*5420368*1**FY834n7Vs4mdASP4H34n*259098050*00*EXT: REQUEST SUKSES.*0000000*00*09000879*09/01/001/00879/RB**3***DWI YULIANA*PONCOGATI RT 11/5**0**PDAM BONDOWOSO*8*2014*0*5*15000*9400*0|16150*9*2014*0*4*5000*7520*0|16150*10*2014*0*2*5000*3760*0|16150*********************";
        } else {
            //sukses
            $resp = "TAGIHAN*WABONDO*250810684*10*20141130205909*DESKTOP*09000879*09/01/001/00879/RB**94130*4500*" . $idoutlet . "*" . $pin . "*------*5420368*1**FY834n7Vs4mdASP4H34n*259098050*00*EXT: REQUEST SUKSES.*0000000*00*09000879*09/01/001/00879/RB**3***DWI YULIANA*PONCOGATI RT 11/5**0**PDAM BONDOWOSO*8*2014*0*5*15000*9400*0|16150*9*2014*0*4*5000*7520*0|16150*10*2014*0*2*5000*3760*0|16150*********************";
        }
        
    } else if ($kdproduk == "WAPLYJ") { // ID PELANGGAN
        if($idpel1=="000754610"){
            $resp = "TAGIHAN*WAPLYJ*250176685*9*20141130064928*DESKTOP*".$idpel1."***0*2500*" . $idoutlet . "*" . $pin . "*------******10*EXT: Billing ID not exist**************PALYJA******************************************";
        
        }else if($idpel1=="000754688"){
            sleep(40);
            $resp = "TAGIHAN*WAPLYJ***20141130064928*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------***************************************************************";    
            
        }else if($idpel1=="000754688"){
            $resp = "TAGIHAN*WAPLYJ*250176685*9*20141130064928*DESKTOP*".$idpel1."***0*2500*" . $idoutlet . "*" . $pin . "*------******88*EXT: Already Paid**************PALYJA******************************************";    
        }else if($idpel=="000001603"){
            $resp="TAGIHAN*WAPLYJ*812332961*8*20160120170829*H2H*000001603*000001603*000001603*609395*2500*" . $idoutlet . "*------**52206327*1**2001*439347120*00*EXT: APPROVE*0000000*00*000001603*000001603*000001603*01***MACHROEP*****PALYJA*12*2015***0*609395*0***********************************";
        }else{    
            $resp = "TAGIHAN*WAPLYJ*250176685*9*20141130064928*DESKTOP*000754677*000754677*000754677*22366*2500*" . $idoutlet . "*" . $pin . "*------*101094*0**2001*258884721*00*EXT: APPROVE*0000000*00*000754677*000754677*000754677*01***SITI RAODAH*****PALYJA*11*2014***0*22366*0***********************************";
        }
    } else if ($kdproduk == "WAAETRA") { // ID PELANGGAN
        if($idpel1=="20040410"){
            $resp = "TAGIHAN*WAAETRA*252536892*9*20141202162822*DESKTOP*".$idpel1."*".$idpel1."*".$idpel1."*0*0*" . $idoutlet . "*" . $pin . "*------******10*EXT: Billing ID not exist**************AETRA******************************************";
        
        }else if($idpel1=="20040468"){
            sleep(40);
            $resp = "TAGIHAN*WAAETRA***20141202162822*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------***************************************************************";     
            
        }else if($idpel1=="20040488"){
            $resp = "TAGIHAN*WAAETRA*252536892*9*20141202162822*DESKTOP*".$idpel1."*".$idpel1."*".$idpel1."*0*2500*" . $idoutlet . "*" . $pin . "*------******88*EXT: Already Paid**************AETRA******************************************";     
        }else if($idpel1=="40064599"){
            $resp="TAGIHAN*WAAETRA*812345062*9*20160120171336*H2H*40064599*40064599*40064599*84584*2500*" . $idoutlet . "*------**229538589*1**2002*439351937*00*EXT: APPROVE*0000000*00*40064599*40064599*40064599*01***YUDHO IRFANTO*****AETRA*12*2015***0*84584*0***********************************";
        } else if($idpel1=="60158686" || $idpel2 == '60158686'){
            $resp = "TAGIHAN*WAAETRA*2117835001*9*20171017153235*H2H*60158686*60158686*60158686*3604079*2500*" . $idoutlet . "*" . $pin . "**247717414*1**2002*836835042*00*EXT: APPROVE*0000000*00*60158686*60158686*60158686*01***JERI S*****AETRA*09*2017***0*3604079*0***********************************   ";
        } else if($idpel1=="60123119" || $idpel2 == '60123119'){
            $resp = "TAGIHAN*WAAETRA*2118959919*9*20171018002209*H2H*60123119*60123119*60123119*13107279*2500*" . $idoutlet . "*" . $pin . "**2717019337*1**2002*837230008*00*EXT: APPROVE*0000000*00*60123119*60123119*60123119*01***HARYATI*****AETRA*09*2017***0*13107279*0***********************************  ";
        } else{
            $resp = "TAGIHAN*WAAETRA*252536892*9*20141202162822*DESKTOP*20040428*20040428*20040428*18045*2500*" . $idoutlet . "*" . $pin . "*------*6570816*1**2002*259623224*00*EXT: APPROVE*0000000*00*20040428*20040428*20040428*01***RIFAI*****AETRA*11*2014***0*18045*0***********************************";
        }
    } else if($kdproduk == "WAMEDAN"){
        $resp = "TAGIHAN*WAMEDAN*1644378434*10*20170404123851*H2H*0117080017***88000*7500*".$idoutlet."*".$pin."**105739277*1**1002*694616383*00*SUCCESSFUL*0000000*00*0117080017***03*#0#0#0*N.3#138067*SYAIFUL HALIM*PEMUDA BARU III 12*#10800.00#18600.00#18600.00***PDAM KOTA MEDAN (SUMUT)*02*2017*46000*47000*00020000*10800*0*03*2017*47000*49000*00020000*18600*0*04*2017*49000*51000*00000000*18600*0********************* ";
    } else if ($kdproduk == "WAMJK") { // NO SAMBUNGAN
        $resp = "TAGIHAN*WAMJK*247143394*10*20141126170615*DESKTOP*0*0909040028*09.07.06.0336*122415*4000*" . $idoutlet . "*" . $pin . "*------*227110*1**WAMJK*257960476*00*SUKSES*0000000*00*0*0909040028*09.07.06.0336*2***SUKADI*SUKOANYAR-GONDANG **0**PDAM KAB. MOJOKERTO (JATIM)*9*2014*0*19*11900*46000*0*10*2014*0*23*8415*56100*0****************************";

    } else if($kdproduk == 'WAPASU'){
        $resp = "TAGIHAN*WAPASU*2886749606*9*20180916171654*H2H*03050228*228**74540*2500*".$idoutlet."*".$pin."**2397053790*1**PASURUAN*1118855845*00*SUCCESS*0000000*00*03050228*228**1*03**MISLAN*BULU RT/W. 02/01 BULUSARI*RUMAH TANGGA C***PDAM KAB. PASURUAN*08*2018*5596*5618*0*74540*0****************************193******* ";
    } else if($kdproduk == 'WAKOPASU'){
        $resp = "TAGIHAN*WAKOPASU*861463171*9*20160219132100*H2H*c1-03943*10*c1-03943*68310*2500*".$idoutlet."*".$pin."**1015927766*1**WAKOPASU*453311443*00*SUKSES*0000000*00*c1-03943*c1-03943*c1-03943*1***DUMAH*Jl. Maluku No.9  RT.3/VIII**0**PDAM KOTA PASURUAN*1*2016*1438*1458|24600|26559|10|10*0*66310*2000|0|2460|2951|5700|5000|***************0********************";
    } else if($kdproduk == 'WACIAMIS'){
        $resp = "TAGIHAN*WACIAMIS*2780129264*10*20180726104607*H2H*04030020147***81400*2500*".$idoutlet."*".$pin."**-52781096*1**400621*1069446672*00*SUCCESSFUL*1069446672*1*04030020147***1*104606---26072018*SWITCHERID*AMY MARYA, SE**null---null---null*000000002500**PDAM CIAMIS*06*2018***0*81400*0***********************************    ";
    } else if ($kdproduk == "TVAORA") {
        $resp = "TAGIHAN*TVAORA*71914075*12*20121211145303*DESKTOP*7000231294***59000*2500*" . $idoutlet . "*" . $pin . "*------*1396265*1*2*TVAORA*49344223*00*SUCCESS***7000231294*1***NOER  HASANAH***59000***0**129009*AORA**0*465646*2012-12-02*2012-12-17*BC01*59000*0";
    } else if ($kdproduk == "TVTOPAS") {
        $resp = "TAGIHAN*TVTOPAS*245627084*10*20141125072539*DESKTOP*1503000389***76100*0*" . $idoutlet . "*" . $pin . "*------*549417*1**060901*257506671*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*1503000389*1*000000506670**Sunarto***72600*0**3500**000000506670*TOPAS TV****01-DEC-14/31-DEC-14**EjcXrChlHuU=**";
    } else if ($kdproduk == "TVINDVS") {
        if($idpel1 == "162406307717"){
            $resp = "TAGIHAN*TVINDVS*2878209835*10*20180912124350*DESKTOP*162406307717***000000000000*0*" . $idoutlet . "*" . $pin . "*------*4211057*4**30100*1114972501*14*EXT: NOMOR TELEPON/IDPEL TIDAK TERDAFTAR*0000000**162406307717*01*** *** ***** *MNC**** *** + *    ";
        } else if($idpel1 == "502612571"){
            $resp = "TAGIHAN*TVINDVS*2877357901*10*20180912021647*H2H*502612571***000000000000*0*" . $idoutlet . "*" . $pin . "**53481549*1**30100*1114549832*68*EXT: TIMEOUT DARI DATABASE INDOVISION*0000000**502612571*01*** *** ***** *MNC**** *** + * ";
        } else if($idpel1 == "401001698866"){
            $resp = "TAGIHAN*TVINDVS*2878278989*10*20180912132142*H2H*401001698866***000000000000*0*" . $idoutlet . "*" . $pin . "**-54013166*1**30100*1115005070*88*EXT: SUDAH LUNAS*0000000**401001698866*01*** *** ***** *MNC**** *** + * ";
        } else {
            $resp = "TAGIHAN*TVINDVS*71529154*12*20121210144058*DESKTOP*401000939875***000000154000*2000*" . $idoutlet . "*" . $pin . "*------*1124477*0**TVINDVS*49098621*00*EXT: APPROVE***401000939875*1***SUNARKO .                     ***000000154000*******500***06112012-05122012**401000939875                                               0401000939875SUNARKO .                     06112012-05122012000000154000**";    
        }
    } else if ($kdproduk == "HPXL") {
        if($idpel1 === "087878390611"){
            $resp = "TAGIHAN*HPXL*2209374062*10*20171123090822*H2H*087878390611****0*" . $idoutlet . "*" . $pin . "**3030*0**013001*867709471*88*EXT: BILL ALREADY PAID*0000000*00*087878390611*01*******XL***************   ";
        } else if($idpel1 === "087782661000"){
            $resp = "TAGIHAN*HPXL*2688381517*10*20180613105201*H2H*087782661000****2500*" . $idoutlet . "*------**904*2**013001*1030440589*85*EXT: INVALID CUSTOMER ID*0000000*00*087782661000*01*******XL***************  ";
        } else if($idpel1 === "087782661994"){
            $resp = "TAGIHAN*HPXL*2688379221*10*20180613105053*H2H*087782661994****2500*" . $idoutlet . "*------**904*2**013001*1030439654*88*EXT: BILL ALREADY PAID*0000000*00*087782661994*01*******XL***************    ";
        } else {
            $resp = "TAGIHAN*HPXL*70911674*11*20121208130702*DESKTOP*0818158020***000000054303*2500*" . $idoutlet . "*" . $pin . "*------*641134*1**HPXL*48720187*00*APPROVE*0000000*00*0818158020*1*0996221**RACHMAT SUHAPPY****XL*201   *201   *000000000*0000000005430300*0000000000*      *      *         *                *          *      *      *         *                *";
        }
    } else if ($kdproduk == "HPTSEL") {
        if($idpel1 === "08111266690"){
            $resp = "TAGIHAN*HPTSELH*2209322871*10*20171123084555*H2H*08111266690***000000000000*2500*" . $idoutlet . "*" . $pin . "**3030*0**HPTSEL*867691981*88*EXT: TAGIHAN TELAH DIBAYARKAN*0000000*00*08111266690*01*******TELKOMSEL*************** ";
        } else if($idpel1 == '08123451796'){
            $resp = "TAGIHAN*HPTSELH*2688383644*10*20180613105304*H2H*08123451796***000000000000*2500*" . $idoutlet . "*------**904*2**HPTSEL*1030441490*88*EXT: TAGIHAN TELAH DIBAYARKAN*0000000*00*08123451796*01*******TELKOMSEL*************** ";
        } else if($idpel1 == '08123451709'){
            $resp = "TAGIHAN*HPTSELH*2688384952*10*20180613105345*H2H*08123451709***000000000000*2500*" . $idoutlet . "*------**904*2**HPTSEL*1030442049*14*EXT: TRANSAKSI DITOLAK KARENA NOMOR TIDAK TERDAFTAR DALAM DATABASE BILLING*0000000*00*08123451709*01*******TELKOMSEL***************    ";
        } else if($idpel1 == "08122997976"){
            $resp = "TAGIHAN*HPTSEL*2874558973*9*20180910151352*MOBILE_SMART*08122997976***000000000000*2500*" . $idoutlet . "*" . $pin . "*------*540163*1**HPTSEL*1113211425*91*EXT: TIDAK DAPAT KONEKSI KE DATABASE*0000000*00*08122997976*01*******TELKOMSEL*************** ";
        } else if($idpel1 == "081336473585"){
            $resp = "BAYAR*HPTSEL*2877796029*10*20180912092531*DESKTOP*081336473585***000000290238*2500*" . $idoutlet . "*" . $pin . "*------*1752602*2**HPTSEL*1114777624*51*EXT: INVALID TRANSACTION DATETIME*0000000*00*081336473585*1***AINXXXXXXXXXXXX****TELKOMSEL*201809*201809*000000000*0000000029023800*0000000000* * * * * * * * * * ";
        } else if($idpel1 == "08114477900"){
            $resp = "TAGIHAN*HPTSEL*2876497828*9*20180911150737*H2H*08114477900***000000000000*2500*" . $idoutlet . "*" . $pin . "**2016937904*1**HPTSEL*1114130531*30*EXT: UNKNOWN DEALERID*0000000*00*08114477900*01*******TELKOMSEL***************   ";
        } else {
            $resp = "TAGIHAN*HPTSEL*242453021*10*20141121120527*DESKTOP*0811408689***000000048323*2500*" . $idoutlet . "*" . $pin . "*------*2930708*0**HPTSEL*256519295*00*APPROVE*0000000*00*0811408689*1***Bapak V.  TIKNO SARWOKO****TELKOMSEL*201411*201411*000000000*0000000004832300*0000000000*      *      *         *                *          *      *      *         *                *";
        }
    } else if ($kdproduk == "HPESIA") {
        $resp = "TAGIHAN*HPESIA*71497244*11*20121210132030*DESKTOP*02198227230***000000066344*2500*" . $idoutlet . "*" . $pin . "*------*137972*1**HPESIA*49078942*00*APPROVE*0000000*00*02198227230*1***ACHMAD SURYADI****ESIA*201099*201099*000000000*0000000006634400*0000000000*      *      *         *                *          *      *      *         *                *";
    } else if ($kdproduk == "FNWOM") {
        if($idpel1=="201010034114"){
            $resp = "TAGIHAN*FNWOM*72853896*10*20121214121709*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------**0****14*EXT: Transaksi ditolak karena nomor tidak terdaftar dalam database Billing**************                                *WOM Finance              **                                          *                         *************";
        }else if($idpel1=="201010034168"){
            sleep(40);
            $resp = "TAGIHAN*FNWOM***20121214121709*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------**0*******************                                ***                                          *                         *************";     
        }else if($idpel1=="201010034188"){
            $resp = "TAGIHAN*FNWOM*72853896*10*20121214121709*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------**0****88*EXT: Tagihan telah dibayarkan**************                                *WOM Finance              **                                          *                         *************";     
         
        }else if($idpel1=="201010034165"){
             $resp = "TAGIHAN*FNWOM*72853896*10*20121214121709*DESKTOP*201010034165***000001005500*0*" . $idoutlet . "*" . $pin . "*------*1516672*0**4105*49921378*00*APPROVE*0000000*00*201010034165*01*0*514846*ABDUL KODIR                    ***000000000000****                                *WOM Finance              *006A                          *                                          *                         *B3899TNX      *016*008*13 Dec 12**000008016000**000001005500*000000000000****";
        } else if($idpel1=="808600015451"){
            $resp = "TAGIHAN*FNWOM*2114262413*10*20171016091322*DESKTOP*808600015451***9342000*0*" . $idoutlet . "*" . $pin . "*------*340446*1**4105*835539756*00*APPROVE*0000000*00*808600015451*01*104202200640*073401*NURDIN ***000009342000**** *WOM Finance *156 * * *E1472YG *012*005*19 Oct 17**000065391000**000009338000*000000000000*000000004000*000000000000** ";
        } else if($idpel1=="802300064457"){
            $resp = "TAGIHAN*FNWOM*2118508711*10*20171017191744*DESKTOP*802300064457***12075000*0*" . $idoutlet . "*" . $pin . "*------*1269774*0**4105*837070249*00*APPROVE*0000000*00*802300064457*01*104202200640*196839*PAINO ***000012075000**** *WOM Finance *016A * * *B1391KMU *012*004*19 Oct 17**000016593000**000012071000*000000000000*000000004000*000000000000**    ";
        } else{    
            $resp = "TAGIHAN*FNWOM*72853896*10*20121214121709*DESKTOP*201010034185***000001005500*0*" . $idoutlet . "*" . $pin . "*------*1516672*0**4105*49921378*00*APPROVE*0000000*00*201010034185*01*0*514846*ABDUL AZIZ                    ***000000000000****                                *WOM Finance              *006A                          *                                          *                         *B3899TNC      *016*008*13 Dec 12**000008016000**000001005500*000000000000****";
        }
    } else if($kdproduk == "FNFIF"){
        $resp = "TAGIHAN*FNFIF*2528242172*10*20180404144213*DESKTOP*507002360917***671000*3000*" . $idoutlet . "*" . $pin . "*------*1213712*4**204111*966644778*00*SUCCESSFUL*50*0ADAFC06FCB25957D8FCC7FFC773B8A5*507002360917*1*04042018---144211*K---5---0*DARSONO******3000*K*FIF*K****036*005*13/04/2018**671000***0**0**  ";
    } else if ($kdproduk == "FNMAF") {
            if($idpel1=="1301124"){
                
                $resp="TAGIHAN*FNMAF*808235916*9*20160118162600*DESKTOP*".$idpel1."****0*" . $idoutlet . "*------*------*2111306*2**MAF*437873722*30*No Kontrak Salah/Tidak Ditemukan*0000000*00**01*9262156298*05ccad0e44e244caf2b671bafef80123*********MEGA AUTO FINANCE****************";
            
            }else if($idpel1=="2641100835"){
                sleep(40);
                $resp="TAGIHAN*FNMAF***20160118162600*DESKTOP*".$idpel1."****0*" . $idoutlet . "*------*------***************************************";
            }else if($idpel1=="2641100888"){
                $resp="TAGIHAN*FNMAF*808235916*9*20160118162600*DESKTOP*".$idpel1."****0*" . $idoutlet . "*------*------*2111306*2**MAF*437873722*88**EXT: Tagihan telah dibayarkan*0000000*00**01*9262156298*05ccad0e44e244caf2b671bafef80123*********MEGA AUTO FINANCE****************";    
            }else if($idpel1=="1201501064"){
                $resp="TAGIHAN*FNMAF*812302212*9*20160120165546*DESKTOP*1201501064***1059000*0*" . $idoutlet . "*------*------*2177894*2**MAF*439334845*00*SUKSES*0000000*00*1201501064*01*9237870692*05ccad0e44e244caf2b671bafef80123*DINIA ZAINAL***1059000*****MEGA AUTO FINANCE******2*20160201****1059000**0***";
            } else if($idpel1 == "7981600075"){
                $resp = "TAGIHAN*FNMAF*2118556173*9*20171017193203*DESKTOP*7981600075***4147000*0*" . $idoutlet . "*" . $pin . "*------*5021248*2**MAF*837086806*00*SUKSES*0000000*00*7981600075*01*9339167852*05ccad0e44e244caf2b671bafef80123*SULISTIYO***4147000*****MEGA AUTO FINANCE******13*20171018****4147000**0*** ";
            } else if($idpel1 == '1561600404'){
                $resp = "TAGIHAN*FNMAF*2120046603*9*20171018120705*DESKTOP*1561600404***11977000*0*" . $idoutlet . "*" . $pin . "*------*4682113*1**MAF*837605395*00*SUKSES*0000000*00*1561600404*01*6921160412*05ccad0e44e244caf2b671bafef80123*ANDI RABIA***11977000*****MEGA AUTO FINANCE******8*20170824****11977000**0*** ";
            } else if($idpel1 == "1911602083"){
                $resp = "TAGIHAN*FNMAF*2873898511*8*20180910100911*H2H*1911602083****0*" . $idoutlet . "*" . $pin . "**1627772466*1**4104*1112903923*68*EXT: TRANSACTION TIMEOUT*0000000*00*1911602083*01***********MEGA AUTO FINANCE****************   ";
            } else if($idpel1 == "7141700549"){
                $resp = "TAGIHAN*FNMAF*2874124879*10*20180910114426*H2H*7141700549***000001028000*0*" . $idoutlet . "*" . $pin . "**291599844*1**4104*1113014074*60*EXT: MISSING MANDATORY PARAMETER*0000000*00*7141700549*01*104202200640*084606*AMINUDIN ***000001028000****000000000000000000002000 *PT MEGA CENTRAL FINANCE *PALEMBANG *HONDA SCOOPY FI STYLISH SPORTY ESP PLUS *MH1JM3112HK433797 *BG3413ABR *023*009*10 Sep 18**000015390000**000001026000*000000000000****   ";
            } else if($idpel1 == "8711800291"){
                $resp = "TAGIHAN*FNMAF*2875579420*10*20180911034144*H2H*8711800291***000000000000*0*" . $idoutlet . "*" . $pin . "**2731804915*1**4104*1113708214*14*EXT: TRANSAKSI DITOLAK KARENA NOMOR TIDAK TERDAFTAR DALAM DATABASE BILLING*0000000*00*8711800291*01*104202200640*160870* ***000000000000****000000000000000000000000 *MEGA AUTO FINANCE* * *xï¿½ï¿½ï¿½027C * *035* *08 Sep 18**000000000000**000000000000*000000000000**** ";
            } else{
                $resp = "TAGIHAN*FNMAF*73761922*10*20121217114805*DESKTOP*2641100868***000001001875*0*" . $idoutlet . "*" . $pin . "*------*2009701*1**4104*50459590*00*APPROVE*0000000*00*2641100868*01*104206300029*536153*IKHSANUDDIN                   ***000000000000****                                *PT MEGA AUTO FINANCE     *POS SUNGAI DANAU              *YAMAHA VEGA ZR 115 DB                     *MH35D9204BJ456707        *DA3933ZY      *017*013*26 Dec 12**000004375000**000000875000*000000126875****";
            }
    } else if ($kdproduk == "FNMEGA") {
        if($idpel1 == "7161700947"){
            $resp = "TAGIHAN*FNMEGA*2115273212*9*20171016152653*H2H*7161700947***2112000*0*" . $idoutlet . "*" . $pin . "**26867133*1**MCF*835896478*00*SUKSES*0000000*00*7161700947*01*8910001154*05ccad0e44e244caf2b671bafef80123*SUINDAH***2112000*****MEGA CENTRAL FINANCE******5*20171019****2112000**0***  ";
        } else if($idpel1 == "5411700393"){
            $resp = "TAGIHAN*FNMEGA*2120402883*9*20171018141436*H2H*5411700393***13075000*0*" . $idoutlet . "*" . $pin . "**1504954117*1**MCF*837716203*00*SUKSES*0000000*00*5411700393*01*1041524552*05ccad0e44e244caf2b671bafef80123*SURYA BUDIMAN***13075000*****MEGA CENTRAL FINANCE******3*20170914****13075000**0*** ";
        } else if($idpel1 == "5451702312"){
            $resp = "TAGIHAN*FNMEGA*2875641496*10*20180911055524*H2H*5451702312***000000000000*0*" . $idoutlet . "*" . $pin . "**71157738*1**4103*1113739684*88*EXT: TAGIHAN TELAH DIBAYARKAN*0000000*00*5451702312*01*104202200640*163621* ***000000000000****000000000000000000000000 *MEGA CENTRAL FINANCE* * *xï¿½ï¿½ï¿½027C * *035* *03 Aug 18**000000000000**000000000000*000000000000**** ";
        } else if($idpel1 == "5911600586"){
            $resp = "TAGIHAN*FNMEGA*2874318993*8*20180910131915*H2H*5911600586****0*" . $idoutlet . "*" . $pin . "**3481183111*1**4103*1113101565*68*EXT: TRANSACTION TIMEOUT*0000000*00*5911600586*01***********MEGA CENTRAL FINANCE****************   ";
        } else if($idpel1 == "2641800259"){
            $resp = "TAGIHAN*FNMEGA*2874451462*9*20180910142335*H2H*2641800259***000000258275*0*" . $idoutlet . "*" . $pin . "**57077610*1**4103*1113162179*60*EXT: MISSING MANDATORY PARAMETER*0000000*00*2641800259*01*104202200640*106021*ADAH SURYAWATI ***000000258275****000000000000000000002000 *PT MEGA AUTO FINANCE *Pos Sungai Danau *HONDA VARIO CW *MH1JF8116CK534504 * *024*006*09 Sep 18**000004845000**000000255000*000000001275****    ";
        } else if($idpel1 == "8711800291"){
            $resp = "TAGIHAN*FNMEGA*2875579818*10*20180911034301*H2H*8711800291***000000000000*0*" . $idoutlet . "*" . $pin . "**2731804915*1**4103*1113708446*14*EXT: TRANSAKSI DITOLAK KARENA NOMOR TIDAK TERDAFTAR DALAM DATABASE BILLING*0000000*00*8711800291*01*104202200640*160887* ***000000000000****000000000000000000000000 *MEGA CENTRAL FINANCE* * *xï¿½ï¿½ï¿½027C * *035* *08 Sep 18**000000000000**000000000000*000000000000**** ";
        } else {
            $resp = "TAGIHAN*FNMEGA*72836730*10*20121214111458*DESKTOP*5701200409***000000661000*0*" . $idoutlet . "*" . $pin . "*------*4420068*1**4103*49911296*00*APPROVE*0000000*00*5701200409*01*0*514367*ABDUL SAMID HARAHAP           ***000000000000****                                *PT MEGA CENTRAL FINANCE  *Bekasi MCF                    *HONDA VARIO TECHNO 125 PGM FI NON CBS     *MH1JFB111CK196426        *B6213UWX      *030*005*14 Dec 12**000017182695**000000657695*000000003305****";
        }    
    } else if ($kdproduk == "FNBAF") {
        if($idpel1=="41701009341"){
            $resp="TAGIHAN*FNBAF*808379076*10*20160118173749*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*------*------*770541*2**86003*437925758*14*IDPEL YANG ANDA MASUKKAN SALAH, MOHON TELITI KEMBALI*0000000*00*41701009341*01***********BUSSAN AUTO FINANCE****************";
       
        }else if($idpel1=="41701009368"){
            sleep(40);
            $resp="TAGIHAN*FNBAF***20160118173749*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*------*------**************************************";
            
        }else if($idpel1=="41701009388"){
            $resp="TAGIHAN*FNBAF*808379076*10*20160118173749*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*------*------*770541*2**86003*437925758*88*TAGIHAN SUDAH DIBAYARKAN*0000000*00*41701009388*01***********BUSSAN AUTO FINANCE****************";
        }else if($idpel1=="122010051955"){
            $resp="TAGIHAN*FNBAF*812377517*10*20160120172639*DESKTOP*122010051955***000000622900*0*" . $idoutlet . "*------*------*1018646*2**86003*439365371*00*SUCCESSFUL*BMS0001*00*122010051955*00*000000000000009DAD6CAED3E61D917A**AZHARI*00*0*000000626100*000000000000*000000000000*000000000000*00000000000000000000000000000000*Bussan Auto Finance*122*YMH.CIO.JCWFI**B 3507 KQH*029*020*20160119*0*000000000000*001*000000620900*000000003200*000000000000*000000002000*000000622900*000001865000";
        } else if($idpel1 == "122010077144"){
            $resp = "TAGIHAN*FNBAF*2874023569*9*20180910105958*H2H*122010077144****0*" . $idoutlet . "*" . $pin . "**3721669732*1**86003*1112965956*16*EXT: PRR (INVALID) SUBSCRIBER*0000000*00*122010077144*01***********BUSSAN AUTO FINANCE****************   ";
        } else if($idpel1 == "991010000254"){
            $resp = "TAGIHAN*FNBAF*2876814752*9*20180911180133*H2H*991010000254****0*" . $idoutlet . "*" . $pin . "**1811469628*1**86003*1114282763*17*EXT: PELANGGAN MEMPUNYAI TAGIHAN YANG MASIH HARUS DIBAYARKAN TERLEBIH DAHULU*0000000*00*991010000254*01***********BUSSAN AUTO FINANCE****************    ";
        } else if($idpel1 == "960010002775"){
            $resp = "TAGIHAN*FNBAF*2876273309*9*20180911123817*WEB*960010002775****0*" . $idoutlet . "*" . $pin . "*------*2003684*4**86003*1114034032*18*EXT: REQUEST UNTUK IDPEL TERSEBUT SEDANG DIPROSES*0000000*00*960010002775*01***********BUSSAN AUTO FINANCE****************    ";
        } else if($idpel1 == "922150000454"){
            $resp = "TAGIHAN*FNBAF*2877817978*9*20180912093457*H2H*922150000454****0*" . $idoutlet . "*" . $pin . "**1086618688*1**86003*1114788342*40*EXT: PEMBAYARAN TERAKHIR TIDAK BISA DILAKUKAN DI LOKET, SILAHKAN MEMBAYAR KE KANTOR FINANCE YANG BERSANGKUTAN*0000000*00*922150000454*01***********BUSSAN AUTO FINANCE****************   ";
        } else if($idpel1 == "708010002797"){
            $resp = "TAGIHAN*FNBAF*2878233453*8*20180912125732*H2H*708010002797****0*" . $idoutlet . "*" . $pin . "**105928164*1**86003*1114983509*68*WAKTU TRANSAKSI HABIS, COBA BEBERAPA SAAT LAGI*0000000*00*708010002797*01***********BUSSAN AUTO FINANCE****************   ";
        } else if($idpel1 == "416010063603"){
            $resp = "TAGIHAN*FNBAF*2877375283*10*20180912031322*H2H*416010063603****0*" . $idoutlet . "*" . $pin . "**339708152*1**86003*1114559400*90*EXT: SEDANG DALAM PROSES CUT-OFF*0000000*00*416010063603*01***********BUSSAN AUTO FINANCE****************    ";
        }else{
            $resp = "TAGIHAN*FNBAF*247481476*10*20141127062134*DESKTOP*636010013925***000000860000*0*" . $idoutlet . "*" . $pin . "*------*1604693*1**86003*258057351*00*SUCCESSFUL*BMS0001*00*636010013925*00*0000000000000DF82690F7763D9A8D14**WAL ASRI FADLI*00*0*000000868600*000000000000*000000000000*000000000000*00000000000000000000000000000000*Bussan Auto Finance*636*YMH.JUP.JUPMXCW**BG 2498 GA*023*004*20141125*0*000000000000*001*000000858000*000000008600*000000000000*000000002000*000000860000*000002576000";
        } 
    } else if($kdproduk == "FNCLMB"){
        if($idpel1 == "0160086998"){
            $resp = "TAGIHAN*FNCLMB*2874826714*10*20180910171556*H2H*0160086998***000000000000*0*" . $idoutlet . "*" . $pin . "**3083498759*1**020002*1113339583*96*EXT: SYSTEM MALFUCTION/TRANSAKSI DITOLAK KARENA TERJADI ERROR DI HOST TELKOM***0160086998*1*0160086998** ***000000000000*****COLUMBIA***** * * ******0** *  ";
        } else if($idpel1 == "0110041700355"){
            $resp = "TAGIHAN*FNCLMB*2874245130*10*20180910124312*H2H*0110041700355***000000000000*0*" . $idoutlet . "*" . $pin . "**3545915828*1**020002*1113068217*91*EXT: ISSUER OR SWITCH IS INOPERATIVE***0110041700355*1*0110041700355** ***000000000000*****COLUMBIA***** * * ******0** * ";            
        } else if($idpel1 == "0110151701098"){
            $resp = "TAGIHAN*FNCLMB*2877636264*10*20180912081214*H2H*0110151701098***000000000000*0*" . $idoutlet . "*" . $pin . "**1286411812*1**020002*1114696899*14*EXT: NOMOR TELEPON/IDPEL TIDAK TERDAFTAR***0110151701098*1*0110151701098** ***000000000000*****COLUMBIA***** * * ******0** * ";            
        } else if($idpel1 == "01108151701307"){
            $resp = "TAGIHAN*FNCLMB*2874541892*10*20180910150542*H2H*01108151701307***000000000000*0*" . $idoutlet . "*" . $pin . "**3301648464*1**020002*1113203930*06*EXT: ERROR***01108151701307*1*0110815170130** ***000000000000*****COLUMBIA***** * * ******0** * ";            
        } else {
            $resp = "TAGIHAN*FNCLMB*1008495101*10*20160510134553*H2H*1001017858001***000000209000*0*".$idoutlet."*".$pin."**688473486*1**020002*497489805*00*EXT: APPROVE***1001017858001*1*1001017858001HADI SUTRONO 10000002090009 dari 180118/04/20160000002090000000002915*55000000082555C*HADI SUTRONO ***000000209000*****COLUMBIA*****01*9 dari 18*18/04/2016******0**000000209000*";    
        }
    } else if ($kdproduk == "ASRTOKIOS") {
        $resp = "TAGIHAN*ASRTOKIOS*364475931*11*20150325145839*DESKTOP*2140001AA***60000*0*".$idoutlet."*" . $pin . "*------*450992*1**ASRTOKIOS*298146281*00*TRANSACTION IS SUCCESSFUL*0000000*00*2140001AA*1***Daniel Haryadi*ASURANSI TM ABADI PLAN A*60000*******ASURANSI TOKIO MARINE LIFE*********";
    } else if($kdproduk == "ASRTOKIO"){
        $resp = "TAGIHAN*ASRTOKIO*364480064*11*20150325150234*DESKTOP*2140001AA***240000*0*".$idoutlet."*" . $pin . "*------*330992*1**ASRTOKIO*298147244*00*TRANSACTION IS SUCCESSFUL*0000000*00*2140001AA*4***Daniel Haryadi*ASURANSI TM ABADI PLAN A*240000*******ASURANSI TOKIO MARINE LIFE*********";
    } else if ($kdproduk == "ASRJWS" || $kdproduk == "ASRJWSI") {
        if (substr($idpel1, 0, 2) == "14") {
            $resp = "TAGIHAN*ASRJWS*250552168*11*20141130160659*H2H*14001969964*CH001969964**1368615*0*" . $idoutlet . "*" . $pin . "**-446748599*1**ASRJWS*259002114*00*EXT:Sukses*0000000*00*14001969964*1***PUJI WARYANTI**1368615******01,PREMI : NOV-2014,PRM.112014,1368615,,,*JIWASRAYA*********";
        } else {
            if($idpel1 == '12345789'){
                $resp = "TAGIHAN*ASRJWS*2877386224*11*20180912035021*H2H*12345789***0*0*" . $idoutlet . "*" . $pin . "**387766928*1**ASRJWS*1114565554*B5*EXT: Bill Not Found*0000000*00*12345789*0*****0*******JIWASRAYA*********  ";
            } else if($idpel1 == '001995661'){
                $resp = "TAGIHAN*ASRJWS*2877484126*11*20180912063734*DESKTOP*001995661***0*0*" . $idoutlet . "*" . $pin . "*------*2739632*1**ASRJWS*1114617687*B8*EXT: Bill Already Paid*0000000*00*001995661*0*****0*******JIWASRAYA********* ";
            } else if($idpel1 == '68001889322'){
                $resp = "TAGIHAN*ASRJWS*251014197*11*20141201085302*H2H*68001889322*MC001889322**400000*0*" . $idoutlet . "*" . $pin . "**-524635319*1**ASRJWS*259164645*00*EXT:Sukses*0000000*00*MC001889322*1***SUHAENAH**400000******01,PREMI : NOV-2014,PRM.112014,200000,,,|02,TOTAL : NOV-2014 s/d DEC-2014,TOT.112014-122014,400000,,,*JIWASRAYA*********";
            } else if($idpel1 == "03001953710"){
                $resp = "TAGIHAN*ASRJWS*1207848105*11*20160830143509*DESKTOP*03001953710*AE001953710**110*0*" . $idoutlet . "*" . $pin . "*------*3696165*1**ASRJWS*562069974*00*EXT: Sukses*0000000*00*AE001953710*1***SUMITRA**110******01,PULIH-2016-08-30,PLH-2016-08-30,110,,,*JIWASRAYA*********";
            } else if($idpel1 == "62002327375"){
                $resp = "TAGIHAN*ASRJWS*2115693115*11*20171016175728*DESKTOP*62002327375*LB002327375**5075000*0*" . $idoutlet . "*" . $pin . "*------*1941711*4**ASRJWS*836047219*00*EXT: Sukses*0000000*00*62002327375*1***SURYA BALKIS**5075000******01,PREMI : OCT-2017,PRM.102017,5075000,,,*JIWASRAYA*********  ";
            } else if($idpel1 == "27002326950") {
                $resp = "TAGIHAN*ASRJWS*2117812510*11*20171017152436*H2H*27002326950*EF002326950**15375000*0*" . $idoutlet . "*" . $pin . "**184710417*1**ASRJWS*836828076*00*EXT: Sukses*0000000*00*27002326950*1***MATTALIA CLARA ANNALENE**15375000******01,PREMI : OCT-2017,PRM.102017,15375000,,,*JIWASRAYA*********  ";
            } else if($idpel1 == "27002326951") {
                $resp = "TAGIHAN*ASRJWS*2117812510*11*20171017152436*H2H*27002326951*EF002326950**15375000*0*" . $idoutlet . "*" . $pin . "**184710417*1**ASRJWS*836828076*XX*Transaksi sudah pernah sukses sebelumnya. Silahkan cek di Report Transaksi di Aplikasi Desktop atau Web Report*0000000*00**1************JIWASRAYA*********  ";
            } else {
                $resp = "TAGIHAN*ASRJWS*251014197*11*20141201085302*H2H*".$idpel1."*MC001889322**400000*0*" . $idoutlet . "*" . $pin . "**-524635319*1**ASRJWS*259164645*00*EXT:Sukses*0000000*00*MC001889322*1***SUHAENAH**400000******01,PREMI : NOV-2014,PRM.112014,200000,,,|02,TOTAL : NOV-2014 s/d DEC-2014,TOT.112014-122014,400000,,,*JIWASRAYA*********";
            }
        }

    } else if ($kdproduk == "WASBY") {
        
        if(1 == 2){
            $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*" . $pin . "*------******99*Mitra Yth, mohon maaf, transaksi tidak dapat dilanjutkan untuk produk ini ";
        } else {
            if($idpel1 == "2385086"){
                //tidak ditemukan
                $resp = "TAGIHAN*WASBY*809299825*11*20160119093427*MOBILE_SMART*2385086***0*2000*" . $idoutlet . "*" . $pin . "*------*2221666*1***438239256*03*EXT: NO PELANGGAN TIDAK DITEMUKAN*0000000*2385086************PDAM SURABAYA***************************************************************";
            } else if($idpel1 == "4040190"){
                //rekening bermasalah
                $resp = "TAGIHAN*WASBY*807318756*11*20160118083944*MOBILE_SMART*4040190***0*2000*" . $idoutlet . "*" . $pin . "*------*------*------***437552776*02*EXT: REKENING BERMASALAH, SILAHKAN MELAKUKAN PEMBAYARAN KE PDAM SURABAYA*0000000*4040190************PDAM SURABAYA***************************************************************";
            } else if($idpel1 == "4986605424599"){
                //request salah
                $resp = "TAGIHAN*WASBY*810092186*10*20160119160808*DESKTOP**4986605424599***2000*" . $idoutlet . "*" . $pin . "*------*310110*2***438517824*04*EXT: REQUEST SALAH*0000000**4986605424599***********PDAM SURABAYA***************************************************************";
            } else if($idpel1 == "4097178"){
                //sudah dilunasi
                $resp = "TAGIHAN*WASBY*807094861*10*20160118043931*H2H*4097178****2000*" . $idoutlet . "*" . $idoutlet . "**25437239*1***437473307*01*EXT: TAGIHAN SUDAH DILUNASI*0000000*4097178************PDAM SURABAYA***************************************************************";
            } else if($idpel1 == "5441470"){
                //waktu trx habis
                sleep(10);
                $resp = "TAGIHAN*WASBY*811169920*9*20160120085151*MOBILE*5441470***0*2000*" . $idoutlet . "*" . $pin . "**4169286*1***438896553*68*WAKTU TRANSAKSI HABIS COBA BEBERAPA SAAT LAGI*0000000*5441470************PDAM SURABAYA***************************************************************";
            } else if ($idpel1 == '4082411') {
                $resp = "TAGIHAN*WASBY*217746583*11*20131124020442*MOBILE_SMART*4082411***94140*2500*" . $idoutlet . "*" . $pin . "*------*13237570*0***134244522*00*INQUIRY SUKSES*0000000*4082411***1***YACUB ANDRES.Y*MERBABU 1*2***94140*PDAM SURABAYA***4D*11*2013***94*105*70640*0**23500**************************************************";
            } else if($idpel1 == '4106210'){
                $resp = "TAGIHAN*WASBY*1510342729*10*20170202141613*H2H*4106210*4106210**15140*2000*BS0004*------**954920*1***238074227*00*INQUIRY SUKSES*0000000*4106210*4106210**1***WIWIN*TAMBAK ASRI TERATAI 65 B*2***15140*PDAM SURABAYA***3A*04*2016***48*55*7640*7500**0**************************************************";
            }else {
                $resp = "TAGIHAN*WASBY*275137088*10*20141224092853*H2H*1013225***35490*2000*" . $idoutlet . "*" . $pin . "**16383805*1***267411786*00*INQUIRY SUKSES*0000000*1013225***1***DADANG SOEKARDI*WONOKROMO S.S BARU 2 8*1***35490*PDAM SURABAYA***3A*12*2014***4589*4613*27240*7500**750**************************************************";
            }
        }
    } else if ($kdproduk == "FNADIRAH" || $kdproduk == "FNADIRA") {
        // cicilan non motor
        if (substr($idpel1, 0, 4) == "0035") {
            $resp = "TAGIHAN*FNADIRAH*362951359*10*20150324090156*YM*003587535679***283000*4000*" . $idoutlet . "*" . $pin . "*------*6644391*1***297656044*00*SUKSES*0000000*00*003587535679*1**00000000000000*nurdin wijaya***283000****00000000000000*ADIRA FINANCE****00000000000000*3*3****03 apr 15*283000****283000*283000";
        } else if($idpel1 == "405001768217"){
            $resp = "TAGIHAN*FNADIRA*2879925149*8*20180913095758*H2H*405001768217****7500*" . $idoutlet . "*" . $pin . "**2104695172*1**ADIRA_FINANCE*1115787254*34*EXT: Nomor pelanggan/billing/handphone/tagihan/kode bayar tidak ditemukan, pastikan nomor yang Anda masukkan sudah benar. (RC 14)*0000000*00*405001768217*01***********ADIRA****************    ";
        } else {
            //$resp = "TAGIHAN*FNADIRAH*280393904*10*20141230140246*H2H*010813117912***540000*2500*" . $idoutlet . "*" . $pin . "**56443780*1**FNADIRA*268964911*00*EXT: APPROVE*02*00*010813117912*01***AYI RAMDANNINGSIH*JL AGUNG RAYA II 03/07**540000*****Adira Finance****B3239SLD*15*15*27 DEC 14**0**540000*0*2000*300*540000*540000";
            // } else if ($kdproduk == "FNADIRA") {
                $resp = "TAGIHAN*FNADIRAH*207675454*10*20141016135227*DESKTOP*020913107656***575000*2500*" . $idoutlet . "*" . $pin . "*------*1453774*1**FNADIRA*244231478*00*EXT: APPROVE*02*00*020913107656*01***IMAM SUDRAJAT*KP CIKARANG 45/09**575000*****Adira Finance****F2096VR*14*14*03 OCT 14**0**575000*0*2000*300*575000*575000";
        }
    } elseif ($kdproduk == "WAJMBR" || $kdproduk == "WAJMBRIDM") {

        $resp = "TAGIHAN*WAJMBR*209510142*10*20141018100148*H2H*24035*24035**12500*2500*" . $idoutlet . "*" . $pin . "*------*-201622384*1**WAJMBR*244922337*00*SUKSES*0000000*00*24035*24035*24035*1***Aswanto*Perumh New Pesona AD-18**0**PDAM JEMBER*09*2014*5150*5150*0*0*12500***************0********************";
    } elseif ($kdproduk == "WAPLMBNG") {
        if($idpel1 == '210689648'){
            $resp = "TAGIHAN*WAPLMBNG*210392801*9*20141019092448*H2H*210689648*210689648**68500*1500*" . $idoutlet . "*" . $pin . "*------*1082750*1**2009*245246556*00*EXT: APPROVE*0000000*00*312600201626*312600201626*210689648*1***M. YUNUS AS*****PDAM PALEMBANG*10*2014***0*68500*0***********************************";
        } else if($idpel1 == '7B085002500018'){
            $resp = "TAGIHAN*WAPLMBNG*1493617166*10*20170125070440*H2H*7B085002500018*7B085002500018*7B085002500018*100222*3000*" . $idoutlet . "*" . $pin . "**1189775*1**2009*653008004*00*EXT: APPROVE*0000000*00*7B0850250018*7B085002500018*7B085002500018*2***ELMA NILYANA*****PDAM PALEMBANG*12*2016***0*52497*0*01*2017***0*47725*0**************************** ";
        }
        
    } elseif ($kdproduk == "WABGK") {
        if($idpel2 == "0207011570"){
            //non taktif
            $resp = "TAGIHAN*WABGK*807407759*10*20160118092301*DESKTOP**0207011570***1500*" . $idoutlet . "*" . $pin . "*------*1786845*3**WABGK*437588588*05*EXT: PELANGGAN NON-AKTIF*0000000*00**0207011570**********PDAM BANGKALAN******************************************";
        } else if($idpel1 == "424404866" || $idpel2 == "424404866"){
            //belum di set
            $resp = "TAGIHAN*WABGK*808286794*10*20160118165106*H2H*424404866****2500*" . $idoutlet . "*" . $pin . "**-601933399*1**WABGK*437892188*01*EXT: SALAH NOMOR PELANGGAN ATAU BELUM DISET*0000000*00*424404866***********PDAM BANGKALAN******************************************";
        } else if($idpel2 == "0207013971"){
            //tidak ada tagihan
            $resp = "TAGIHAN*WABGK*806972782*10*20160117213129*DESKTOP**0207013971***1500*" . $idoutlet . "*" . $pin . "*------*97981*1**WABGK*437450779*08*EXT: TIDAK ADA TAGIHAN*0000000*00**0207013971**********PDAM BANGKALAN******************************************";
        } else if($idpel2 == "014004820"){
            //tidak ditemukan
            $resp = "TAGIHAN*WABGK*808541074*10*20160118190122*DESKTOP**014004820***1500*" . $idoutlet . "*" . $pin . "*------*757472*2**WABGK*437984643*04*EXT: TIDAK DITEMUKAN NOMOR PELANGGAN*0000000*00**014004820**********PDAM BANGKALAN******************************************";
        } else if ($idpel2 == "0104032417") {
            //sukses
            $resp = "TAGIHAN*WABGK*209448375*10*20141018091136*H2H*0*0104032417**50500*2500*" . $idoutlet . "*" . $pin . "*------*-199445339*1**WABGK*244895543*00*SUKSES*0000000*00*0*0104032417*0104032417*1***IDA SUSANTI*Jl. HALIM PERDANA KUSUMA GG.II **0**PDAM BANGKALAN*9*2014*0*15*0*50500*0***********************************";
        } else {
            //sukses
            $resp = "TAGIHAN*WABGK*273941512*10*20141223052804*DESKTOP*0*0101001861*01-1-00186A*366275*6000*" . $idoutlet . "*" . $pin . "*------*1815447*0**WABGK*267026591*00*SUKSES*0000000*00*0*0101001861*01-1-00186A*4***NURJANNAH*KH. MARZUQI **0**PDAM BANGKALAN*8*2014*0*25*12600*84000*0*9*2014*0*25*12600*84000*0*10*2014*0*19*9435*62900*0*11*2014*0*26*13140*87600*0**************";
        }
    } elseif ($kdproduk == "WABJN") {
        //  $resp = "TAGIHAN*WABJN*209627847*10*20141018114530*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*55950*2500*" . $idoutlet . "*" . $pin . "*------*-203097960*1**WABJN*244966588*00**0000000*00*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*1***ERWIN WIJAYA*R SUNJANI/KYAI MAZAD 4 **0**PDAM BOJONEGORO*9*2014*0*21*0*55950*0***********************************";
        $resp = "TAGIHAN*WABJN*186510*10*20141124115433*DESKTOP*0*0111002*0*195500*4000*" . $idoutlet . "*" . $pin . "*------*168600*1**WABJN*237998913*00**0000000*00*0*0111002*0*2***EKO SUDARMANTO*Jl. VETERAN 0 0**0**PDAM BOJONEGORO*9*2014*0*0*0*0*84000*10*2014*0*10*0*27500*84000****************************";
        //  $resp="TAGIHAN*WABJN*246502055*10*20141125225350*DESKTOP*042920905*0402204*2920905*61850*2000*FA25120*------*------*596821*1**WABJN*257772036*00**0000000*00*042920905*0402204*2920905*1***M CHOIRI*TANJUNG HARJO BLOK KARANG **0**PDAM BOJONEGORO*10*2014*717*740*0*61850*0***********************************";
    } elseif ($kdproduk == "WABDG") {
        if($idpel1=="00201410010"){
           $resp = "TAGIHAN*WABDG*209305507*9*20141018061323*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------******10*EXT: Billing ID not exist**************PDAM BANDUNG******************************************"; 
        }else if($idpel1=="00201410068"){
            sleep(40);
           $resp = "TAGIHAN*WABDG***20141018061323*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------***************************************************************"; 
           
        }else if($idpel1=="00201410088"){
           $resp = "TAGIHAN*WABDG*209305507*9*20141018061323*DESKTOP*".$idpel1."***0*0*" . $idoutlet . "*" . $pin . "*------******88*EXT: Already Paid**************PDAM BANDUNG******************************************"; 
        }else if($idpel1=="00A08650410"){
            $resp="TAGIHAN*WABDG*812361815*9*20160120172039*H2H*00A08650410*00A08650410*00A08650410*50000*2800*" . $idoutlet . "*------**-499968980*1**2003*439358743*00*EXT: APPROVE*0000000*00*00A08650410*00A08650410*00A08650410*01***DRS.SUBCHAN DWIYANTO*****PDAM BANDUNG*12*2015***00000000*50000*0***********************************";
        }else{
            if ($idpel1 == "00201410060") {
                $resp = "TAGIHAN*WABDG*209305507*9*20141018061323*DESKTOP*00201410060*00201410060**90100*2800*" . $idoutlet . "*" . $pin . "*------*1228045*1**2003*244828200*00*EXT: APPROVE*0000000*00*00201410060*00201410060*00201410060*01***KOMAR*****PDAM BANDUNG*09*2014***0*90100*0***********************************";
            } else {
                $resp = "TAGIHAN*WABDG*274340837*9*20141223124415*H2H*00008901103*00008901103*00008901103*114400*2500*" . $idoutlet . "*" . $pin . "**-367669473*1**2003*267159267*00*EXT: APPROVE*0000000*00*00008901103*00008901103*00008901103*01***OEY TJWAN LIEN*****PDAM BANDUNG*11*2014***10400*104000*0***********************************";
            }
        }
    } elseif ($kdproduk == "TVNEX") {
        if($idpel1 == "688818"){
            $resp = "TAGIHAN*TVNEX*2878211839*10*20180912124502*H2H*688818****0*" . $idoutlet . "*" . $pin . "**19283447*1**060801*1114973420*85*EXT: INVALID CUSTOMER ID*0000000*00*688818*01************NEX MEDIA******** "; 
        } else if($idpel1 == "622422675"){
            $resp = "TAGIHAN*TVNEX*2878246279*10*20180912130340*H2H*622422675****0*" . $idoutlet . "*" . $pin . "**338186357*1**060801*1114989473*88*EXT: BILL ALREADY PAID*0000000*00*622422675*01************NEX MEDIA********    ";
        }  else if($idpel1 == "622293997"){
            $resp = "TAGIHAN*TVNEX*2878053083*10*20180912112105*H2H*622293997****0*" . $idoutlet . "*" . $pin . "**63560694*1**060801*1114899983*96*EXT: SYSTEM MALFUNCTION, SYSTEM IS IN ERROR CONDITION, TRANSACTION CANNOT PROCEED*0000000*00*622293997*01************NEX MEDIA********  ";
        } else {
            $resp = "TAGIHAN*TVNEX*210977946*10*20141019205305*DESKTOP*622177311***108900*0*" . $idoutlet . "*" . $pin . "*------*1467904*1**060801*245467270*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*622177311*1*000000467269**IRVAN SOFIAN**NEXSPORTS PLATINUM MOVIES 1 BULAN*108900*0**0**000000467269*NEX MEDIA****11-10-2014**MhxAQgFzCvw=**";    
        }
        
    } elseif ($kdproduk == "HPSMART") {
        if($idpel1 === '088271129948'){
            $resp = "TAGIHAN*HPSMART*2209395002*10*20171123091832*H2H*088271129948****0*" . $idoutlet . "*" . $pin . "**3030*0**016004*867717045*88*EXT: BILL ALREADY PAID*0000000*00*088271129948*01*******SMART*************** ";
        } else if($idpel1 == "08891412222"){
            $resp = "TAGIHAN*HPSMART*2877371670*10*20180912030237*H2H*08891412222****0*" . $idoutlet . "*" . $pin . "**279215308*1**016004*1114557651*406*EXT: TELCO BILLID HAS INVALID PREFIX*0000000*00*08891412222*01*******SMART*************** ";
        } else if($idpel1 == "08811212443"){
            $resp = "TAGIHAN*HPSMART*2873350234*10*20180910032559*H2H*08811212443****0*" . $idoutlet . "*" . $pin . "**280038276*1**016004*1112618435*13*EXT: INVALID AMOUNT, AMOUNT IS NOT IN THE VALID RANGE AGREED OR NOT CONFORM TO BILLING INFORMATION.*0000000*00*08811212443*01*******SMART***************   ";
        } else if($idpel1 == "08812342006"){
            $resp = "TAGIHAN*HPSMART*2873366817*10*20180910040837*H2H*08812342006****0*" . $idoutlet . "*" . $pin . "**279195616*1**016004*1112626513*84*EXT: EXPIRED ACCOUNT, ACCOUNT IS EXPIRED*0000000*00*08812342006*01*******SMART***************  ";
        } else if($idpel1 == "088272086219"){
            $resp = "TAGIHAN*HPSMART*2877407622*10*20180912045410*H2H*088272086219****0*" . $idoutlet . "*" . $pin . "**86249437*1**016004*1114577564*85*EXT: INVALID CUSTOMER ID*0000000*00*088272086219*01*******SMART*************** ";
        } else if($idpel1 == "0882275200097"){
            $resp = "TAGIHAN*HPSMART*2877642045*10*20180912081511*H2H*0882275200097****0*" . $idoutlet . "*" . $pin . "**260278405*1**016004*1114699949*68*EXT: LATE RESPONSE, RESPONSE MESSAGE FROM PARTIES IS RECEIVED TOO LATE.*0000000*00*0882275200097*01*******SMART***************   ";
        } else {
            $resp = "TAGIHAN*HPSMART*209747379*10*20141018134847*DESKTOP*088271084560***25388*0*" . $idoutlet . "*" . $pin . "*------*3711353*1**016004*245006299*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*088271084560*1*000000006298**B620-1010945-WAHYUDI-K***25388*SMART***************";
        }
    } elseif ($kdproduk == "HPMTRIX") {
        if($idpel1 == "081511252099"){
            $resp = "TAGIHAN*HPMTRIX*2209369091*10*20171123090558*H2H*081511252099***000000000000*2500*" . $idoutlet . "*" . $pin . "**3030*0**HPMTRIX*867707571*88*EXT: TAGIHAN TELAH DIBAYARKAN*0000000*00*081511252099*1*******INDOSAT*201 *201 *000000000*0000 *0000000000* * * * * * * * * *    ";
        } else if($idpel1 == "085872192047"){
            $resp = "TAGIHAN*HPMTRIX*2877547868*10*20180912072420*H2H*085872192047***000000000000*2500*" . $idoutlet . "*" . $pin . "**70373076*1**HPMTRIX*1114651618*91*EXT: TIDAK DAPAT KONEKSI KE DATABASE*0000000*00*085872192047*01*******INDOSAT***************   ";
        }else {
            $resp = "TAGIHAN*HPMTRIX*209341603*10*20141018072300*DESKTOP*08155101252***000000027500*2500*" . $idoutlet . "*" . $pin . "*------*1965079*0**HPMTRIX*244846164*00*APPROVE*0000000*00*08155101252*1***DIAN INDRESWARI****INDOSAT*201016*201016*000000000*0000000002750000*0000000000*      *      *         *                *          *      *      *         *                *";
        }
    } elseif ($kdproduk == "TVORG50") {
        $resp = "TAGIHAN*TVORG50*254033901*8*20141203220858*DESKTOP*10014413***50000*0*" . $idoutlet . "*" . $pin . "*------*378192*0**060701*260064778*00*INQUIRY BERHASIL*0000000*00*10014413*1******50000******ORANGE TV********";
    } elseif ($kdproduk == "TVORG80") {
        $resp = "TAGIHAN*TVORG80*253706964*8*20141203173235*DESKTOP*33019333***80000*0*" . $idoutlet . "*" . $pin . "*------*325076*1**060701*259979617*00*INQUIRY BERHASIL*0000000*00*33019333*1******80000******ORANGE TV********";
    } elseif ($kdproduk == "TVORG100") {
        $resp = "TAGIHAN*TVORG100*254031342*8*20141203220226*DESKTOP*10014413***100000*0*" . $idoutlet . "*" . $pin . "*------*478192*0**060701*260064138*00*INQUIRY BERHASIL*0000000*00*10014413*1******100000******ORANGE TV********";
    } elseif ($kdproduk == "TVORG300") {
        $resp = "TAGIHAN*TVORG300*252422831*8*20141202144251*DESKTOP*49045011***300000*0*" . $idoutlet . "*" . $pin . "*------*3438*1**060701*259589412*00*INQUIRY BERHASIL*0000000*00*49045011*1******300000******ORANGE TV********";
    } elseif ($kdproduk == "HPTHREE") {
        if($idpel1 === '089653986084'){
            $resp = "TAGIHAN*HPTHREE*2209405670*10*20171123092301*H2H*089653986084****0*" . $idoutlet . "*" . $pin . "**3030*0**012101*867720200*88*EXT: BILL ALREADY PAID*0000000*00*089653986084*01*******THREE*************** ";
        } else if($idpel1 == "0895383225015"){
            $resp = "TAGIHAN*HPTHREE*2873347353*10*20180910031613*H2H*0895383225015**1**0*" . $idoutlet . "*" . $pin . "**89425468*1**012101*1112616677*406*EXT: TELCO BILLID HAS INVALID PREFIX*0000000*00*0895383225015*01*******THREE*************** ";
        } else if($idpel1 == "089696092060"){
            $resp = "TAGIHAN*HPTHREE*2873348030*10*20180910031828*H2H*089696092060****0*HH84870*------**280705695*1**012101*1112617046*06*EXT: FAILED GENERAL ERROR*0000000*00*089696092060*01*******THREE***************   ";
        } else {
            $resp = "TAGIHAN*HPTHREE*252310672*10*20141202125739*H2H*08984222333***55000*0*" . $idoutlet . "*" . $pin . "**27683401*1**012101*259558197*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*08984222333*1*000000558196**INTAN NUR AZIZA***55000*THREE***************";    
        }
    } elseif ($kdproduk == "HPFREN") {
        if($idpel === '08886416941'){
            $resp = "TAGIHAN*HPFREN*2209389012*10*20171123091554*H2H*08886416941****0*" . $idoutlet . "*" . $pin . "**3030*0**016002*867715133*88*EXT: BILL ALREADY PAID*0000000*00*08886416941*01*******FREN*************** ";
        } else {
            $resp = "TAGIHAN*HPFREN*239543469*10*20141119072250*DESKTOP*08885088008***47094*0*" . $idoutlet . "*" . $pin . "*------*40363*1**016002*255262501*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*08885088008*1*000000262500**HERU  WIDIJANTO***47094*FREN***************";
        }
    } else if($kdproduk == "TVBIG"){
        $resp = "TAGIHAN*TVBIG*493382849*11*20150625094455*DESKTOP*1072161447***138499*0*" . $idoutlet . "*" . $pin . "*------*5425448*3**008001*339062465*00*EXT: APPROVE***1072161447*1**CLOSE PAYMENT *Mr. syahrun as ***138499******TV BIG*0**008001***00010721614470080011CLOSE PAYMENT 000000138499Mr. syahrun as **";
    } else if($kdproduk == 'ASRBINT1'){
        $resp = "TAGIHAN*ASRBINT1*635971088*8*20150930203222*H2H*3524221906900009***2500*0*" . $idoutlet . "*" . $pin . "**541761*0**ASRBINT1*385550568*00*SUKSES***1234567890123459*01****Asuransi Bintang Paket 1*2500*****20000000**BINTANG***************20150930203222*20151001203222*20151030203222*ASURANSI BINTANG PAKET 1# Nilai Pertanggungan Meninggal Dunia: Rp 1.000.000# Nilai Pertanggungan Kebakaran: Rp 20.000.000# Premi: Rp 2.500# Jangka Waktu Pertanggungan: 30 hari";
    } else if($kdproduk == 'ASRBINT2'){        
        $resp = "TAGIHAN*ASRBINT2*635259709*8*20150930134857*H2H*3524221906900003***5000*0*" . $idoutlet . "*" . $pin . "**574761*0**ASRBINT2*385308955*00*SUKSES***3524221906900003*01****Asuransi Bintang Paket 2*5000*****40000000**BINTANG***************20150930134857*20151001134857*20151030134857*ASURANSI BINTANG PAKET 2# Nilai Pertanggungan Meninggal Dunia: Rp 2.000.000# Nilai Pertanggungan Kebakaran: Rp 40.000.000# Premi: Rp 5.000# Jangka Waktu Pertanggungan: 30 hari";
        //$resp = "TAGIHAN*ASRBINT2*1122884635*7*20160714110339*XML****5000*0*".$idoutlet."*".$pin."**5112*2**ASRBINT2*533350322*00*SUKSES****01****Asuransi Bintang Paket 2*5000*****40000000**BINTANG***************20160714110339*20160715110339*20160813110339*ASURANSI BINTANG PAKET 2# Nilai Pertanggungan Meninggal Dunia: Rp 2.000.000# Nilai Pertanggungan Kebakaran: Rp 40.000.000# Premi: Rp 5.000# Jangka Waktu Pertanggungan: 30 hari";
    } else if($kdproduk == 'ASREQJLN'){
        $resp = "TAGIHAN*ASREQJLN*635964486*8*20150930202849*H2H*3505061506880008***10000*0*" . $idoutlet . "*" . $pin . "**551761*0**ASREQJLN*385548579*00*SUKSES***3505061506880008*01****Asuransi Perjalanan Equity*10000*****100000000**EQUITY******************ASURANSI PERJALANAN EQUITY#- Harga premi Rp 10.000#- Masa perlindungan 7 hari#- Berlaku untuk peserta dengan# usia antara 1 s/d 69 tahun.#- Manfaat asuransi:# - Santunan meninggal dunia karena# kecelakaan Rp 100 Juta# - Santunan meninggal dunia karena# sakit atau sebab alami Rp 1 Juta #";
    } else if($kdproduk == 'WADEPOK'){
        $resp = "TAGIHAN*WADEPOK*653112046*10*20151012113910*DESKTOP*02440121***000000197900*2500*" . $idoutlet . "*" . $pin . "*------*343011*1**1141062*391074910*00*SUCCESSFUL*0000000*0*02440121***01*20151012043000011048*20151012113910229438530074815926*PT. PRIMAMAS PERKASA***0000002500**PDAM KOTA DEPOK (JABAR)*9*2015*441*473*0*000000197900************************************";
    } else if($kdproduk == 'WAKOBGR'){
        $resp = "TAGIHAN*WAKOBGR*654736758*10*20151013115334*DESKTOP*15801274***000000261000*2500*" . $idoutlet . "*" . $pin . "*------*1198205*2**1141027*391616325*00*SUCCESSFUL*0000000*0*15801274***01*20151013043000033105*20151013115333546184630074815926*NENENG NS***0000002500**PDAM KOTA BOGOR*9*2015*0*1953*0*000000261000************************************";
    } else if(substr($kdproduk, 0, 4) == "TVKV"){
        //$resp ="TAGIHAN*TVKV100*801089118*10*20160114105136*DESKTOP*110446529***100000*0*" . $idoutlet . "*" . $pin . "*------*986465*2**061201*435835214*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*110446529*1*000000835213**ADI PRAMONO .***000000000.00*0**0**000000835213*K-Vision TV******EUJmeDO2KoY=**K VISION (100.000)";
        $resp = "TAGIHAN*TVKV100*802199763*10*20160114210357*DESKTOP*110396769***100000*0*" . $idoutlet . "*" . $pin . "*------*6108826*3**061201*436126083*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*110396769*1*000000126082**ATIK SUNARYA***10000*0**0**000000126082*K-Vision TV******ET9ValQnGBc=**K VISION (100.000)";
    } else if(substr($kdproduk, 0, 5) == "TVSKY"){
        $resp = "TAGIHAN*TVSKYFAM1*866725780*9*20160222134458*H2H*37000004733***40000*0*" . $idoutlet . "*" . $pin . "**283679545*1**061301*455136066*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*37000004733*1*000000136065*****40000*****000000136065*TV SKYNINDO******AjBHd1cmGPA=**SKYNINDO TV FAMILY 1 BLN (40.000)";
    } else if($kdproduk == 'TVINNOV'){
        $resp = "TAGIHAN*TVINNOV*552441593*11*20150805180949*DESKTOP*10504754***368000*0*".$idoutlet."*".$pin."*------*430518*1**006007*357922672*00*EXT: APPROVE***10504754*1** I150805181248384 * SATRIA YUDA PRASETYO ***368000******TV INNOVATE*0**000000368000 ***10504754 000000368000 SATRIA YUDA PRASETYO 20150704 I150805181248384 **";
        //$resp_pay = "BAYAR*TVINNOV*552442764*11*20150805181030*DESKTOP*10504754***368000*0*".$idoutlet."*".$pin."*------*62518*1**006007*357923002*00*EXT: APPROVE***10504754*1** SATRIA YUDA PRASETYO * SATRIA YUDA PRASETYO ***368000***** P150805181328331 *TV INNOVATE*0**000000368000 ***10504754 000000368000 SATRIA YUDA PRASETYO 20150704 I150805181248384 **";
    } else if($kdproduk == 'WAPROLING'){
        $resp = "TAGIHAN*WAPROLING*1359913296*10*20161122110152*H2H*04000977***51750*2500*".$idoutlet."*".$pin."**790231317*1**400171*616420007*00*SUCCESSFUL*0000000*0*04000977***1*110151---22112016*SWITCHERID*FARAH SUDARSIH**null---null---null*000000002500**PDAM PROBOLINGGO*10*2016***0*51750*0***********************************";
    } else if($kdproduk == 'WAKOSOLO'){
        $resp = "TAGIHAN*WAKOSOLO*1361595037*10*20161123091052*H2H*00030619***47300*1700*".$idoutlet."*".$pin."**581070535*1**400251*616860154*00*SUCCESSFUL*0000000*1*00030619***1*090123---23112016*SWITCHERID*Ny Misrini Iswandi**null---null---null*000000001700**PDAM KOTA SOLO*10*2016***4300*43000*0***********************************";
    } else if($kdproduk == 'ASRCAR'){
        $date = date('YmdHis');
        if($idpel1 == '2322280000118726'){
            //gagal 1 sudah terbayar
            $resp ="TAGIHAN*ASRCAR*1373936423*9*".$date."*H2H*".$idpel1."***0*0*".$idoutlet."*".$pin."*------*715296*1**ASRCAR*620055221*01*PAYS OFF*0000000*00*".$idpel1."*01************AJ CENTRAL ASIA RAYA*********  ";
        } else if($idpel1 == '2377711000395827'){
            //sukses
            $resp = "TAGIHAN*ASRCAR*1363986386*9*".$date."*DESKTOP*".$idpel1."***350000*0*".$idoutlet."*".$pin."*------*22252246*1**ASRCAR*617490048*00*SUCCES*0000000*00001*".$idpel1."*01***BONG KIM SIN**350000*******AJ CENTRAL ASIA RAYA****350000*77711000395827****";
        } else {
            //gak valid
            $resp = "TAGIHAN*ASRCAR*1390660024*9*".$date."*H2H*".$idpel1."****0*".$idoutlet."*".$pin."**1490*0**ASRCAR*624683615*01*ID NOT VALID*0000000*00*".$idpel1."*01************AJ CENTRAL ASIA RAYA*********";
        }
        
    } else if($kdproduk == 'WASAMPANG'){
        $date = date('YmdHis');
        if($idpel2 == '0101010080'){
            $resp = "TAGIHAN*WASAMPANG*1466747495*10*".$date."*DESKTOP*0*0101010080*01/I /001/0080/a*134211*5000*".$idoutlet."*".$pin."*------*1085088*2**WASAMPANG*645563262*00*SUKSES*0000000*00*0*0101010080*01/I /001/0080/a*2***TIMBUL SUNGKONO*JL. SELONG PERMAI **0**PDAM TRUNOJOYO SAMPANG*11*2016*0*17*12201*61005*0*12*2016*0*17*0*61005*0****************************    ";
        }
        
    } else if($kdproduk == 'ASRPRU'){
        $date = date('YmdHis');
        $resp = "TAGIHAN*ASRPRU*2672084762*8*".$date."*WEB*11895488***500000*0*".$idoutlet."*".$pin."*------*3150605*4**PRUDENTIAL*1023349437*00*Sukses!*0000000*BULANAN###13/05/18 S/D 13/07/18*11895488*01*000000500000###ACU13326409084872646*25000###IDR###01*KURNIA RISTIYANI*********PRUDENTIAL*****0****   ";
    } else {
        return "no data received";
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kdproduk, $resp);

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
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"           	=> (string) $r_idpel1,
        "IDPEL2"           	=> (string) $r_idpel2,
        "IDPEL3"           	=> (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) trim(str_replace('.', '', $r_nama_pelanggan)),
        "PERIODE"           => (string) $r_periode_tagihan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => "0",
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) str_replace('.', '[dot]', $url_struk),
    );

    //insert into fmss_message
    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    $get_mid = get_mid_from_idtrx($r_idtrx);
    $get_step = get_step_from_mid($get_mid) + 1;
    $implode = implode('.', $params);
    writeLog($get_mid, $get_step, $host, $receiver, $implode, $via);
    return $implode;
}

function pay($req){
	$i          = -1;
    $kdproduk   = strtoupper($req['produk']);
    $idpel1     = strtoupper($req['idpel1']);
    $idpel2     = strtoupper($req['idpel2']);
    $idpel3     = strtoupper($req['idpel3']);
    $nominal    = strtoupper($req['nominal']);
    $idoutlet   = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']);
    $ref2       = strtoupper($req['ref2']);
    $ref3       = strtoupper($req['ref3']);
    $field      = 11;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
    //         return "IP Anda [$ip] tidak punya hak akses";
    //     }
    // }
    
    global $pgsql;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    if ($kdproduk == "PLNPASC") {
        $kdproduk = "PLNPASCH";
    } else if ($kdproduk == "PLNPRA") {
        $kdproduk = "PLNPRAH";
    } else if ($kdproduk == "HPTSEL") {
        $kdproduk = "HPTSELH";
    } else if ($kdproduk == "PLNNON") {
        $kdproduk = "PLNNONH";
    }

    $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan
    $cektoken   = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal - 1600), trim($kdproduk), trim($idoutlet)); //tambahan
    $stp        = $GLOBALS["step"] + 1;
    $msg        = array();
    $i          = -1;
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
    if($kdproduk == "PAJAKGRTLT" || $kdproduk == "PAJAKGRTL"){
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = $idpel1;
    }
    $fm         = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];

    /* tambahan */
    if($kdproduk == "PGN"){
        $resp = "BAYAR*PGN*2977389842*10*20181030161201*WEB*0110011437***140520*2500*" . $idoutlet . "*" . $pin . "*------*8290773*1**PGN*1161888441*00*SUKSES*0110011437*L HUTAGALUNG*26 M3*Sep2018*INV1181030141660*140520*2500*143020***72888*REF    ";
    } else if($kdproduk == "KKBNI"){
        $resp = "BAYAR*KKBNI*2656829460*9*20180530073815*H2H*5489888810362324***490300*6000*" . $idoutlet . "*" . $pin . "**18963495*1**BNI*1016820300*00*Sukses!*0559*00*5489888810362324*01***DADANG ISKANDAR SKM********BNI*14052018*03062018****490300* ";
    } else if ($kdproduk == "TELEPON") {
// 2 BULAN
        $resp = "BAYAR*TELEPON*12670604*11*20120524125233*DESKTOP*021*88393209**000000137580*7500*" . $idoutlet . "*" . $pin . "*D4B6EA34*405228*1*0*001001*18156631*00*APPROVE*021*088393209*02*0008*3*203A       *50860*204A       *45860*205A       *40860* LINA SIREGAR                 *";
    } else if ($kdproduk == "SPEEDY") {
        if ($idpel1 == "0141148100225") {
// 2 BULAN
            $resp = "BAYAR*SPEEDY*72138103*11*20121212092242*DESKTOP*0141148100225***000000364113*5000*" . $idoutlet . "*" . $pin . "*------*154094*1*0*001001*49484197*00*APPROVE*0141*148100225*04*0001*2**0*211A       *149613*212A       *214500* MARIYANTO                    *";
        } else {
// 1 BULAN
            $resp = "BAYAR*SPEEDY*12633844*11*20120524112049*XML*0162406900527***000000744750*2500*" . $idoutlet . "*" . $pin . "*A7E252CC*1197532*0*0*001001*18142680*00*APPROVE*0162*406900527*06*0006*1**0**0*205A       *744750* GEREJA GBI NANGA BUL         *";
        }
    } else if ($kdproduk == "TVTLKMV") {
        if ($idpel1 == "122429250104") {//2 bulan
            $resp = "BAYAR*TVTLKMV*71150072*11*20121209114401*DESKTOP*122429250104***000000287500*5000*" . $idoutlet . "*" . $pin . "*------*298154*1*0*001001*48864190*00*APPROVE*0122*429250104*02*0004*2**0*211A       *150000*212A       *137500* ANDRY PRAMONO                *";
        } else {
            $resp = "BAYAR*TVTLKMV*12632313*11*20120524111719*XML*127246500157***000000099000*1950*" . $idoutlet . "*" . $pin . "*D3A84F25*283300*1*0*001001*18141915*00*APPROVE*0127*246500157*08*0001*1**0**0*205A       *99000*BAITUS MONGJENG               *";
        }
    } else if (substr($kdproduk, 0,6) == "PLNNON" ) {
        $resp = "BAYAR*PLNNON*12643503*10*20120524114128*MOBILE*5392112011703***696400*1600*" . $idoutlet . "*" . $pin . "*FASTPAY*514644*1*1*99504*18147159*00*SUCCESSFUL*0000000*5392112011703                   *53*012*PENYAMBUNGAN BARU        *20120524*23062012*542122123488*JAYUSMAN                 *083349C5AB7B4738A3EBE7352CDA9E6A*62D9201911A4437188E8C1734539FE0C*53921*Jl Pahlawan No 39 Rangkasbitung    *123            *2*00000000069640000*2*00000000069800000*2*0000160000*0101200000000000000000          *    *000000000*00********Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
    } else if (substr($kdproduk, 0,7) == "PLNPASC") {
        //511030176462
        if($idpel1=="323360001335"){
            if($idpel1=="323360001335" && $ref2 == '50512928'){
                //sleep(40);
                //handle simulator case 13
                //$resp = "BAYAR*".$kdproduk."***".$tanggal."*H2H*".$idpel1."*".$idpel2."*".$idpel3."***" . $idoutlet . "*-----*------******00*SEDANG DIPROSES*********************************************************************";

                //handle simulator case 15
                $resp = "BAYAR*".$kdproduk."***".$tanggal."*H2H*".$idpel1."*".$idpel2."*".$idpel3."***" . $idoutlet . "*-----*------*******Masih ada transaksi dengan id pelanggan sama yang sedang dalam proses. Silahkan cek dalam 5-10 menit lagi di Report Transaksi di Aplikasi Desktop atau Web Report*********************************************************************";

            } else {
                $resp = "BAYAR*".$kdproduk."***".$tanggal."*H2H*".$idpel1."*".$idpel2."*".$idpel3."***" . $idoutlet . "*-----*------******11*Inquiry record tidak ditemukan. Silahkan melakukan inquiry ulang*********************************************************************";            
            }
        } else {
            if($idpel=="523520409670"){   
                sleep(40);
                $resp = "BAYAR*".$kdproduk."*831295610*10*".$tanggal."*H2H*".$idpel1."*".$idpel2."****".$idoutlet."*".$pin."*------******00*SEDANG DIPROSES*0000000*".$idpel1."*******************************************************************";
            }else{
                if($idpel1 == "521050065222"){
                    $resp = "BAYAR*PLNPASCH*1100054273*10*20160630153934*H2H*521050065222***116368*2500*" . $idoutlet . "*" . $pin . "*------*19518181899*1**99501*525590627*00*SUCCESSFUL*VI105V3*521050065222*1*1*01*0SMB213515441905BDCF0265229CF14D*TUKIYO *52105*123 *R1 *000000450*000002500*201606*20062016*00000000*00000113368*D0000000000*0000000000*000003000*01234700*01257700*00000000*00000000*00000000*00000000*******************************************000000000000*Rincian Tagihan dapat diakses di www.pln.co.id atau PLN Terdekat";
                } else if ($idpel1 == "323360001351" && $ref2 = "50512928" && $nominal == "5353500000") {//3 bln
                    $resp = "BAYAR*PLNPASC*73849112*11*20121217145957*DESKTOP*323360001351***5353500000*5700*" . $idoutlet . "*" . $pin . "*------*2209930*1*1*99501*50513190*00*SUCCESSFUL*0000000*323360001351*3*3*03*BC74455477014318BFADB709029F36B5*MILLI.M                  *32330*123            *R1  *000000450*000004800*201210*20102012*00000000*00000014685*D0000000000*0000000000*000009000*00006200*00010400*00000000*00000000*00000000*00000000*201211*20112012*00000000*00000014289*D0000000000*0000000000*000006000*00010400*00014500*00000000*00000000*00000000*00000000*201212*20122012*00000000*00000009561*D0000000000*0000000000*000000000*00014500*00017300*00000000*00000000*00000000*00000000*****************000000000000*Rincian Tagihan dapat diakses di www.pln.co.id";
                } else if($idpel1 == "151000042568" && $ref2 == "835567984"){
                    $resp = "BAYAR*PLNPASC*2114349021*10*20171016094146*WEB*151000042568*SAHADI*01*1081168*2500*" . $idoutlet . "*" . $pin . "*------*218202*1**501*835573200*00*TRANSAKSI SUKSES*0000000*151000042568*1*1*01*0BMS210ZF9F9CB3A27C18C6B31BE76AE*SAHADI *15100*123 * R1*000002200*000000000*201710*20102017*00000000*00001081168*D0000000000*0000000000*000000000*07472500*07564600*00000000*00000000*00000000*00000000*****************************************5EC3F36854EC4B258E3990222F5E5741**1081168*Rincian Tagihan dapat diakses di www.pln.co.id,Informasi Hubungi Call Center:123 Atau Hub. PLN Terdekat:";
                } else if($idpel1 == "513030034100" && $ref2 == "837525931"){
                    $resp = "BAYAR*PLNPASCH*2119804224*10*20171018104535*H2H*513030034100***29314918*2500*" . $idoutlet . "*" . $pin . "*------*9789298*1**501*837526754*00*TRANSAKSI SUKSES*0000000*513030034100*1*1*01*0BMS210ZC83A1EB2C04F7562F8882E04*PROY DTC SONGGORITI *51303*123 * P1*000082500*000000000*201710*20102017*00000000*00029314918*D0000000000*0000000000*000000000*05106200*05172800*00000000*00000000*00000000*00000000*****************************************E4AC6E62191943F697BE884DB7AEC82E**29314918*Rincian Tagihan dapat diakses di www.pln.co.id,Informasi Hubungi Call Center:123 Atau Hub. PLN Terdekat:   ";
                } else if($idpel1 == "534210870159" && $ref2 == "1037282114"){
                    $resp = "BAYAR*PLNPASCH*2705172852*10*20180621155356*H2H*534210870159***528874*3000*" . $idoutlet . "*" . $pin . "*------*3802283070*1**501*1037409518*58*EXT: PROSES TRANSAKSI TIDAK BISA DILAKUKAN KARENA TERDAPAT KETIDAKCOCOKAN DATA *0000000*534210870159*1*1*01**UJANG NANA SUYATNA *53421*123 * R1M*000000900*000000000*201806*22062018*00000000*000000528874*D0000000000*0000000000*000000000000*00017103*00017477*00000000*00000000*00000000*00000000*****************************************E34E62F70CB4482F845EAD65C476B8AF**528874*    ";
                } else if($idpel1 == "321209707325" && $ref2 == "1037312283"){
                    $resp = "BAYAR*PLNPASCH*2705053349*10*20180621150137*H2H*321209707325***365877*3000*" . $idoutlet . "*" . $pin . "*------*418304348*1**501*1037357064*34*EXT: TAGIHAN SUDAH TERBAYAR*0000000*321209707325*1*1*01**ST MAEMUNAH *32111*123 * R1M*000000900*000000000*201806*22062018*00000000*000000365877*D0000000000*0000000000*000000000000*00001467*00001711*00000000*00000000*00000000*00000000*****************************************9B5FF8539C4C4FA2921C07FEBE4CAA2B**365877*    ";
                } else if($idpel1 == "122030400910" && $ref2 == "1067800142"){
                    // 3bulan
                    $resp = "BAYAR*PLNPASC*2776318086*10*20180724111803*H2H*122030400910**6282161380690*89108*5000*" . $idoutlet . "*" . $pin . "*------*-257760949*1**501*1067800385*00*TRANSAKSI SUKSES*0000000*122030400910*2*2*02*0BMS210Z29D4C1513451E796DA0E3B10*AMRAN *12203*123 * R1*000000450*000000000*201806*22062018*00000000*000000046316*D0000000000*0000000000*000000006000*00006208*00006311*00000000*00000000*00000000*00000000*201807*20072018*00000000*000000033792*D0000000000*0000000000*000000003000*00006311*00006391*00000000*00000000*00000000*00000000****************************30432B94343D42C3A05F29C58450C886**89108*\"Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :\"  ";
                } else {//4 bulan
                    $resp = "BAYAR*PLNPASC*12632010*11*20120524111642*XML*538731734541***887849*12000*" . $idoutlet . "*" . $pin . "*A76361C0*7811856*1*1*99501*18141728*00*SUCCESSFUL*0000000*538731734541*4*4*04*08C536F6E16347419AFD252F8EC979A7*R SUMALI2                *53873*123            *R1  *000000900*000012000*201202*20022012*00000000*00000029247*D0000000000*0000001000*000009000*00873500*00876600*00000000*00000000*00000000*00000000*201203*20032012*00000000*00000232713*D0000000000*0000001000*000009000*00876600*00919900*00000000*00000000*00000000*00000000*201204*20042012*00000000*00000287208*D0000000000*0000001000*000006000*00919900*00973300*00000000*00000000*00000000*00000000*201205*20052012*00000000*00000311681*D0000000000*0000001000*000003000*00973300*01031500*00000000*00000000*00000000*00000000****000000000000*Rincian Tagihan dapat diakses di www.pln.co.id"; 
                }
            }   
        }
    } else if (substr($kdproduk, 0,6) == "PLNPRA") { 
        if($idpel1=="34018062991" || $idpel2=="537316468946"){
            sleep(40);
            $resp="BAYAR*PLNPRAH*811399328*10*20160120102008*H2H*34018062991*537316468946*".$idpel3."*200000*2500*HH13483*------*------*143010167*1***438987481*00*Token akan dikirim via SMS ke " . $idpel3."*VI105V3*34018062991*537316468946*1*00000000000000000000000000000000*0SMB213517075F64B2854F753156FA1F*00000000*NENENG MARTINI*R2*000003500*2*0000250000*0*53*53731*123*02520*0***Token akan dikirim via SMS ke " . $idpel3."*2*0000000000*2*0000000000*2*0001481500*2*0000000000*2*000018518500*2*0000013150*Informasi Hubungi Call Center 123 Atau hubungi PLN Terdekat";
        }else{
            if($idpel1 == '86000703735' || $idpel2=="521082522378"){        
                $resp = "BAYAR*PLNPRA*673999435*10*20151026022221*H2H*86000703735*521082522378*087838888891*50000*2500*" . $idoutlet . "*" . $pin . "*------*232014441*1**99502*397931322*00*SUCCESSFUL*8*86000703735*521082522378*0**0MAS2126022219000000000086514405**REANANDA HIDAYAT PERMONO*R1*1300*0*2500*0***123*****12528065590833886940**0.00**0.00**3704.00**0.00**46296*1*343*Informasi Hubungi Call Center 123 Atau hubungi PLN Terdekat.";
            } else if($idpel1 == '01117082246' || $idpel2=="511061245422"){
                $resp = "BAYAR*PLNPRA*12996128*11*20120525130054*DESKTOP*01117082246*511061245422**48400*1600*" . $idoutlet . "*" . $pin . "*D5430F79*157963*1*1*99502*18253742*00*SUCCESSFUL*0000000*01117082246*511061245422*0*988776546B2D482781B583F2A2FD5D76*14EDE52D039A4421A258AE30C77A3891*02414165*KARTO SOEWITO*R1*000002200*2*0000160000*0*51*51106*123*01584*0***32927368215773195205*2*0000000000*2*0000000000*2*0000358519*2*0000000000*2*000004481481*2*0000005640*Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
            } else if( ($idpel1 == '32900717920' || $idpel2=="535550153156") && $ref2 = '836862504' ){
                //nominal 5juta
                $resp = "BAYAR*PLNPRAY*2117923699*10*20171017160320*H2H*32900717920*535550153156*0811222516*5000000*2500*" . $idoutlet . "*" . $pin . "*------*2565200599*1**053502*836862925*00*TRANSAKSI SUKSES*JTL53L3*32900717920*535550153156*0*9907A48E4275411F8A6816218AC1134E*0BMS210Z935B109F4AB49F1FB4F003C8*00000000*PT SASTRA DAJA*B2*000033000*2*0000000000*0*53*53555*123*23760*0***25634517918116980193*2*0000600000*2*0000000000*2*0028268000*2*0000000000*2*000471132000*2*0000321100*Informasi Hubungi Call Center 123 Atau hubungi PLN Terdekat  ";
            } else if( ($idpel1 == '34007336802' || $idpel2=="537613097710") && $ref2 = '1036812431' ){
                $resp = "BAYAR*PLNPRAH*2703746689*40*20180621002141*H2H*34007336802*537613097710*6281287929134*20000*3000*" . $idoutlet . "*" . $pin . "*------*-87702662*1**99502*1036812444*63*EXT: TIDAK ADA PEMBAYARAN*506CA01*34007336802*537613097710*0*00000000000000000000000000000000*20EF4BB481F3420F8EBCC809D548E785**ERMA*R1*000001300*2*0000300000*0*53*53761*123*00936*0****************  ";
            } else if( ($idpel1 == '32100768574' || $idpel2=="213301053103") && $ref2 = '1037231567' ){
                $resp = "BAYAR*PLNPRA*2704732747*19*20180621123327*H2H*32100768574*213301053103*6281348990617*500000*2500*" . $idoutlet . "*" . $pin . "*------*203301495*1**053502*1037231571*47*EXT : TOTAL KWH MELEBIHI BATAS MAKSIMUM*JTL53L3*32100768574*213301053103*0*BD9612A39B7A4D5C8D4AD884F0A5551E*0BMS210Z57CBAAEA71780B67F9B45CBF**AGUSTINA*R1*000000450***0*21*21330*123*00324*0****************   ";
            } else {
                $resp = "BAYAR*PLNPRA*12996128*11*20120525130054*DESKTOP*01117082246*511061245422**48400*1600*" . $idoutlet . "*" . $pin . "*D5430F79*157963*1*1*99502*18253742*00*SUCCESSFUL*0000000*01117082246*511061245422*0*988776546B2D482781B583F2A2FD5D76*14EDE52D039A4421A258AE30C77A3891*02414165*KARTO SOEWITO*R1*000002200*2*0000160000*0*51*51106*123*01584*0***32927368215773195205*2*0000000000*2*0000000000*2*0000358519*2*0000000000*2*000004481481*2*0000005640*Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
            }
        }    
    } else if ($kdproduk == "WALOMBOKT") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WALOMBOKT*380860767*10*20150408102407*DESKTOP*012100676*0*0*51650*2500*".$idoutlet."*" . $pin . "*------*791339*3**400311*303146152*00*SUCCESSFUL*0000000*0*012100676***1*102350---08042015*0000000050*SYAHNUN*LENENG*201503*000000002500**PDAM Kab. Lombok Tengah*03*2015***0*51650************************************";
    } else if ($kdproduk == "WAKBMN") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKBMN*325088253*11*20150216181702*MOBILE_SMART*05014240008*05014240008**27500*2000*".$idoutlet."*" . $pin . "*------*------*1**------*------*00*SUCCESSFUL*0000000*1*05014240008***1*181701---16022015*0000008001*IBU SAILAH*Ds.Demangsari RW III*20151*000000002000**PDAM KEBUMEN*1*2015*2652*2658*0*27500************************************";
    } else if ($kdproduk == "WAPBLINGGA") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAPBLINGGA*375520225*10*20150404063115*DESKTOP*14050151*0*0*103260*2000*" . $idoutlet . "*" . $pin . "*------*1388464*3**400271*301406507*00*SUCCESSFUL*0000000*0*14050151***1*063227---04042015*0000008001*MAS'UT NUR H.*JL.MAWAR  RT.4/1*201503*000000002250**PDAM PURBALINGGA*03*2015***0*103260************************************";
    } else if ($kdproduk == "WASLTIGA") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WASLTIGA*377721740*10*20150406065044*DESKTOP*02b9289*0*0*24400*2000*" . $idoutlet . "*" . $pin . "*------*3554705*1**400321*302081978*00*SUCCESSFUL*0000000*0*02b9289***1*080647---06042015*0000008001*SUPENO*KLASEMAN 04/II*201503*000000002000**PDAM KOTA SALATIGA*03*2015***0*24400************************************";
    } else if($kdproduk == "WASUMED"){
        $resp = "BAYAR*WASUMED*2802786944*9*20180806175127*MOBILE_SMART*3104014051***73900*2500*".$idoutlet."*".$pin."*------*499070*2**400631*1079958691*00*SUCCESSFUL*1079958691*1*3104014051**RT.C|0*1*175115---06082018*SWITCHERID*SUBANA*Marga citra MARGA CINTA*null---null---null*000000002500**PDAM Kab Sumedang*07*2018*0*0*0*73900*0***********************************   ";
    } else if($kdproduk == "WABDGBAR"){
        $resp = "BAYAR*WABDGBAR*2811507582*9*20180810173314*H2H*0102000763***38500*2500*".$idoutlet."*".$pin."**100494915*1**pmgs*1083981615*00*SUCCESS*0000000*00*0102000763***1**R2*Achmad Zaini Miftah*Perum GPI Jl. Berlian No.45****PDAM Kab Bandung Barat*7*2018*694*705*0*38500*0*********************************** ";
    }else if($kdproduk == 'WASAMPANG'){
        $resp = "BAYAR*WASAMPANG*2813306875*10*20180811144337*H2H**0102040126**77968*5000*".$idoutlet."*".$pin."**-831727670*1**WASAMPANG*1084797180*00*SUKSES*0000000*00*01003923*0102040126*01/II /004/0126/A*2***CHUSNUL HOTIMAH*MUTIARA **0**PDAM TRUNOJOYO SAMPANG*6*2018*0*1*7088*35440*0*7*2018*0*1*0*35440*0****************************    ";
    } else if ($kdproduk == "WAGROBGAN") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAGROBGAN*377432718*10*20150405185154*DESKTOP*0702000552*0*0*000000027000*2000*" . $idoutlet . "*" . $pin . "*------*188829*1**1021033*301993551*00*SUCCESSFUL*0000000*0*0702000552***01*20150405043000005081*G02700005521505*LULUT SURYANTO***0000002000**PDAM KAB. GROBONGAN*3*2015*000364*000374     *0*000000027000************************************";
    } else if ($kdproduk == "WABANJAR") { // ID PELANGGAN
//        // 2 BLN
        $resp = "BAYAR*WABANJAR*373048180*10*20150402080936*DESKTOP*4027386*0*0*389380*4000*" . $idoutlet . "*" . $pin . "*------*225307*3**400231*300700216*00*SUCCESSFUL*0000000*0*4027386***2*081049---02042015*0000008001*DINA PUJIATI*Jl.Panglima Batur Gg.Qa*201502*000000002000**PDAM BANJARMASIN*02*2015***0*209310**03*2015***0*180070*****************************";
    } else if ($kdproduk == "WASRKT") { // ID PELANGGAN
//        // 3 BLN
        $resp = "BAYAR*WASRKT*372702270*15*20150401200643*DESKTOP*00046902*0*0*102400*5100*" . $idoutlet . "*" . $pin . "*------*28218*2**400251*300606207*00*SUCCESSFUL*0000000*1*00046902***3*200208---01042015*0000008001*Wahono*Semanggi        RT 03/2*201501*000000001700**PDAM SURAKARTA*03*2015***0*32000**02*2015***3200*32000**01*2015***3200*32000**********************";
    } else if ($kdproduk == "WAPURWORE") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAPURWORE*373604483*10*20150402123019*DESKTOP*01033200271*0*0*75800*1600*" . $idoutlet . "*" . $pin . "*------*14318*2**400211*300819168*00*SUCCESSFUL*0000000*0*01033200271***1*123019---02042015*0000008001*Pranoto Suwignyo*Jend A Yani*201503*000000001600**PDAM PURWOREJO*03*2015***0*75800************************************";
    } else if ($kdproduk == "WABYL") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WABYL*370983612*10*20150331154900*DESKTOP*02120025*0*0*227350*2000*" . $idoutlet . "*" . $pin . "*------*596397*1**400081*300070366*00*SUCCESSFUL*0000000*0*02120025***1*154901---31032015*0000008001*MULYATMIN*Pisang, Susilohardjo*201502*000000002000**PDAM BOYOLALI*02*2015***0*227350************************************";
    } else if ($kdproduk == "WAKABBDG") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKABBDG*372228006*10*20150401143954*DESKTOP*461195*0*0*88000*2000*" . $idoutlet . "*" . $pin . "*------*163188*1**400221*300458844*00*SUCCESSFUL*0000000*0*461195***1*143956---01042015*0000008001*TUNING RUDYATI*PESONA BALI RESIDENCE.*201504*000000002000**PDAM KAB. BANDUNG*04*2015***0*88000************************************";
    } else if ($kdproduk == "WAKNDL") { // ID PELANGGAN
//        // 2 BLN
        $resp = "BAYAR*WAKNDL*372764350*10*20150401205616*DESKTOP*0442060140*0*0*108200*3000*" . $idoutlet . "*" . $pin . "*------*1299922*3**400241*300624928*00*SUCCESSFUL*0000000*0*0442060140***2*205611---01042015*0000008001*Slamet Basuki*Babadan Rt 2/6*201502*000000001500**PDAM KENDAL*02*2015***0*74200**03*2015***0*34000*****************************";
    } else if ($kdproduk == "WAWONOGIRI") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAWONOGIRI*373653668*10*20150402130635*MOBILE_SMART*02040172*02040172**79000*2000*" . $idoutlet . "*" . $pin . "*------*98002*0**400141*300832927*00*SUCCESSFUL*0000000*1*02040172***1*130350---02042015*0000008001*DRS SUPARNO*SINGODUTAN 2/1*201503*000000002000**PDAM KAB. WONOGIRI*03*2015***0*79000************************************";
    } else if ($kdproduk == "WAIBANJAR") { // ID PELANGGAN
//        // 3 BLN
        $resp = "BAYAR*WAIBANJAR*370332504*10*20150331072101*DESKTOP*390804*0*0*435740*6000*" . $idoutlet . "*" . $pin . "*------*9343748*3**400401*299891744*00*SUCCESSFUL*0000000*0*390804***3*072103---31032015*0000008001*H.JUMBRANI*JL.KELURAHAN GG.KRUING*201412*000000002000**PDAM INTAN BANJAR*02*2015***0*65160**01*2015***0*192660**12*2014***0*177920**********************";
    } else if ($kdproduk == "WAGIRIMM") { // ID PELANGGAN
//        // 3 BLN
        $resp = "BAYAR*WAGIRIMM*371929746*10*20150401105631*DESKTOP*02-07-07330*02-07-07330*0*135650*7500*" . $idoutlet . "*" . $pin . "*------*3191455*3**400381*300380730*00*SUCCESSFUL*0000000*1*02-07-07330*02-07-07330**3*115853---01042015*0000008001*RUJAI*SEKARBELA*201501*000000002500**PDAM GIRI MENANG MATARAM*01*2015***10000*56900**02*2015***10000*25700**03*2015***0*33050**********************";
    } else if ($kdproduk == "WABULELENG") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WABULELENG*373521181*10*20150402112943*DESKTOP*02001775*0*0*37600*2000*" . $idoutlet . "*" . $pin . "*------*12674*1**400371*300795390*00*SUCCESSFUL*0000000*0*02001775***1*112835---02042015*0000437105*KETUT SUKANARA*ANTURAN GG MAWAR*201503*000000002500**PDAM KAB. BULELENG*03*2015***0*37600************************************";
    } else if ($kdproduk == "WABREBES") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WABREBES*373004003*10*20150402075243*DESKTOP*1601040330*0*0*47000*2500*" . $idoutlet . "*" . $pin . "*------*2548522*3**400341*300693502*00*SUCCESSFUL*0000000*0*1601040330***1*075238---02042015*0000008001*Hersodo*JL. Kol. Sugiono RT.03/*201503*000000002500**PDAM KAB. BREBES*03*2015***0*47000************************************";
    } else if ($kdproduk == "WAWONOSB") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAWONOSB*373041923*10*20150402080716*DESKTOP*0114120063*0*0*62020*2000*" . $idoutlet . "*" . $pin . "*------*415231*1**400331*300699347*00*SUCCESSFUL*0000000*0*0114120063***1*080718---02042015*0000008001*MOCH NASIR SUNYOTO*PUNTUK*201503*000000002000**PDAM KAB. WONOSOBO*03*2015***0*62020************************************";
    } else if ($kdproduk == "WAMADIUN") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAMADIUN*371957500*10*20150401111518*DESKTOP*0208050150*0*0*79580*2500*" . $idoutlet . "*" . $pin . "*------*1447273*3**400261*300388430*00*SUCCESSFUL*0000000*0*0208050150***1*111632---01042015*0000008001*SLAMET AS*GULUN GG II RT 49 RW 15*201503*000000002500**PDAM KOTA MADIUN*03*2015***0*79580************************************";
    } else if ($kdproduk == "WASRAGEN") { // ID PELANGGAN
//        // 2 BLN
        $resp = "BAYAR*WASRAGEN*371905769*10*20150401104009*DESKTOP*0800564*0*0*87000*3400*" . $idoutlet . "*" . $pin . "*------*136821*2**400181*300373739*00*SUCCESSFUL*0000000*0*0800564***2*093118---01042015*0000008001*WARTINAH A*KADIPIRO*201502*000000001700**PDAM KAB. SRAGEN*03*2015***0*31250**02*2015***0*55750*****************************";
    } else if ($kdproduk == "WAKABSMG") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKABSMG*372895718*10*20150402041844*DESKTOP*310050799*0*0*23140*2000*" . $idoutlet . "*" . $pin . "*------*22805*1**400201*300654369*00*SUCCESSFUL*0000000*0*310050799***1*041519---02042015*0000008001*BAGUS SETIAWAN*LOSARI SAWAHAN NO.51 RT*201504*000000002000**PDAM KAB. SEMARANG*04*2015***0*23140************************************";
        $resp = "BAYAR*WAKABSMG*2794338931*10*20180802160301*H2H*204031416***32362*5400*HH12683*------**1621046*1**400201*1075957588*00*SUCCESSFUL*1075957588*1*204031416**RMN|8*2*160141---02082018*SWITCHERID*TUMI YATIANA*Noloprayan RT 04/RW 02 Jatirej*null---null---null*000000005400**PDAM KAB. SEMARANG*06*2018*42*39*0*15042*0*07*2018*47*42*0*17320*0****************************";
    } else if ($kdproduk == "WABYMS") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WABYMS*379541126*10*20150407104235*DESKTOP*0124619*0*0*31540*2000*" . $idoutlet . "*" . $pin . "*------*4889172*1**400011*302720844*00*SUCCESSFUL*0000000*0*0124619***1*105514---07042015*0000008001*NANI WIBOWO*JL. JATIWINANGUN GG.SEM*MAR15*000000002000**PDAM BANYUMAS*3*2015***0*31540************************************";
    } else if ($kdproduk == "WAHLSUNGT") { // ID PELANGGAN
//        // 2 BLN
        $resp = "BAYAR*WAHLSUNGT*370640986*10*20150331112745*DESKTOP*0300648*0*0*251200*4000*" . $idoutlet . "*" . $pin . "*------*4405*0**400361*299981664*00*SUCCESSFUL*0000000*0*0300648***2*112859---31032015*0000008001*LINA HERIANI*JL.DESA KERAMAT RT.3/2*201501*000000002000**PDAM Hulu Sungai Tengah*01*2015***0*106800**02*2015***0*144400*****************************";
    } else if ($kdproduk == "WAKARANGA") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKARANGA*373054738*10*20150402081140*DESKTOP*0702011372*0*0*28000*2000*" . $idoutlet . "*" . $pin . "*------*2440332*2**400121*300701041*00*SUCCESSFUL*0000000*0*0702011372***1*081136---02042015*0000008001*SENEN*BUNGKUS 10/03 JATIROYO*201503*000000002000**PDAM Karanganyar*03*2015***0*28000************************************";
    } else if ($kdproduk == "WAKPKLNGAN") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKPKLNGAN*371624754*10*20150401070700*DESKTOP*0104010422*0*0*70550*2000*" . $idoutlet . "*" . $pin . "*------*609924*3**400101*300280228*00*SUCCESSFUL*0000000*0*0104010422***1*070654---01042015*0000008001*Moh. Abdullah*Perum Puri Puri raya Bl*201503*000000002000**PDAM KAB. PEKALONGAN*03*2015***0*70550************************************";
    } else if ($kdproduk == "WAMAKASAR") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAMAKASAR*359960925*10*20150321075233*DESKTOP*199200987*0*0*000000043320*2500*" . $idoutlet . "*" . $pin . "*------*169935*1**1021014*296702948*00*SUCCESSFUL*0000000*0*199200987***01*20150321043000000699*GEH001992009872*M. NATSIR***0000002500**PDAM KOTA MAKASAR*2*2015*00003512*00003521 *0*000000043320************************************";
    } else if ($kdproduk == "WAKUBURAYA") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKUBURAYA*357995309*10*20150319181319*DESKTOP*09400*0*0*000000053500*2000*" . $idoutlet . "*" . $pin . "*------*1422454*3**1021015*295847639*00*SUCCESSFUL*0000000*0*09400***01*20150319043000019664*G05350094001519*CONG TJIN MOI***0000002000**PDAM KOTA KUBURAYA*2*2015*001215*001235     *0*000000053500************************************";
    } else if ($kdproduk == "WAPONTI") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAPONTI*357668833*10*20150319143344*DESKTOP*3060346*0*0*000000026100*1600*" . $idoutlet . "*" . $pin . "*------*857465*0**1021010*295720757*00*SUCCESSFUL*0000000*0*3060346***01*20150319043000015081*GEH000030603462*ARDIAN***0000001600**PDAM KOTA PONTIANAK (KALBAR)*2*2015*00000105*00000111 *0*000000026100************************************";
    } else if ($kdproduk == "WAMANADO") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAMANADO*357242267*10*20150319094410*DESKTOP*38671*0*0*000000074730*1600*" . $idoutlet . "*" . $pin . "*------*497304*3**1021009*295561512*00*SUCCESSFUL*0000000*0*38671***01*20150319043000006148*G2313H3G849RF38*FATMA SAMBANG***0000001600**PDAM KOTA MANADO*2*2015*00000281*00000288 *0*000000074730************************************";
    } else if ($kdproduk == "WASITU") { // ID PELANGGAN
//        // 1 BLN
        
        if($idpel1 == "01/IV /004/0612/B1"){
            $resp = "BAYAR*WASITU*357732560*10*20150319151536*H2H*01/IV /004/0612/B1***90070*2000*" . $idoutlet . "*" . $pin . "**9781908*1**WASITU*295742721*00*SUKSES*0000000*00*01/IV /004/0612/B1*01/IV /004/0612/B1*7101*1***MISTAM SOEKARDI*ARGOPURO No.GG16 *2:2015:0:0:0*0**PDAM SITUBONDO*2*2015*0*39*0*90070*0***********************************";
        } else if($idpel1 == "01/I /007/1659/B1"){
            $resp = "BAYAR*WASITU*1692774052*14*20170425163719*DESKTOP*01/I /007/1659/B1*01/I /007/1659/B1*14001*107500*6000*".$idoutlet."*".$pin."*------*140359*1**WASITU*708529237*00*SUKSES*0000000*00*01/I /007/1659/B1*01/I /007/1659/B1*14001*3***SAMSUL HADI*JL. CEMPAKA PERUM ISMU H- 18 *#1:2017:5000:25000:0#2:2017:5000:0:0#3:2017:5000:0:0*25000**PDAM SITUBONDO*1*2017*0*5*0*22500*0*2*2017*0*10*0*22500*0*3*2017*0*10*40000*22500*0*********************";
        }
    } else if ($kdproduk == "WAREMBANG") { // ID PELANGGAN    
        if ($idpel1 == "LA-03-00084") {// bulan
            $resp = "BAYAR*WAREMBANG*347270076*10*20150310145130*DESKTOP*LA-03-00084***46400*2000*" . $idoutlet . "*" . $pin . "*------*0*2**400301*291942562*00*SUCCESSFUL*0000000*0*LA-03-00084***1*145133---10032015*0000008001*MUSHOLLA AL MUBAROQ**201502*000000002000**PDAM Kab. Rembang*02*2015***0*46400************************************";
        } else {
            $resp = "BAYAR*WAREMBANG*347346523*10*20150310155253*DESKTOP*LA-03-00012***530000*4000*" . $idoutlet . "*" . $pin . "*------*0*2**400301*291968179*00*SUCCESSFUL*0000000*0*LA-03-00012***2*155255---10032015*0000008001*R A M I S I H**201502,201501*000000004000**PDAM Kab. Rembang*02*2015***0*269400**01*2015***0*260600*****************************";
        }
    } else if ($kdproduk == "WASLMN") { // ID PELANGGAN
        $resp = "BAYAR*WASLMN*327274552*10*20150218183916*H2H*1400669***60000*2500*" . $idoutlet . "*" . $pin . "**-228275716*0**400071*284869801*00*SUCCESSFUL*0000000*0*1400669***1*183903---18022015*0000008001*NADI KUSNADI*JL.ASTER 333*201501*000000001700**PDAM SLEMAN*01*2015*3724*3744*0*60000************************************";
    } else if ($kdproduk == "WASMG") { // ID PELANGGAN   
        if($idoutlet =='HH10632' || $idoutlet =='BS0004' || $idoutlet =='FA9919'){
            $bill_q = "1";
        } else {
            $bill_q = '01';
        }
        if($idpel1=="07460079"){
            sleep(40);
            $resp="BAYAR*WASMG*811403601*10*20160120102151*H2H*07460079***45700*2000*" . $idoutlet . "*------**4239956*1***438989123*00*SEDANG DIPROSES*GS10AG3*Rumah Tangga 3*07460079***1*F576BB3476214FC0B5D0000000000000*000438988655*Chr Sri Setyowati*Parang Barong 6/04**0000002000**PDAM KOTA SEMARANG*12*2015*2643*2658*0*36200*0|0|2000|5000|2500|0***********************************";
        }else if($idpel1 == '05430379'){
            //2bulan WASMG
            $resp = "BAYAR*WASMG*868053206*10*20160223103806*DESKTOP*05430379*0*0*188396*4000*" . $idoutlet . "*" . $pin . "*------*309646*0**87004*455527591*00*SUCCESS*GS10AG3*Rumah Tangga 4*05430379***2*29402BF700AE4048B830000000000000*000455524845*FX Samiyo (Rt.9/3)*Cikurai Brt Dlm 1 Kaligse**0000004000**PDAM KOTA SEMARANG*12*2015*612*631,19*6936*61860*0|0|5000|5000|2500|0*01*2016*631*656,25*0*94600*0|0|5000|5000|2500|0****************************";
        } else if($idpel1 == '07020747'){
            $resp = "BAYAR*WASMG*1490099889*9*20170123101239*H2H*07020747***94005*4000*" . $idoutlet . "*" . $pin . "**1449440050*1**87004*652123389*00*SUCCESS*GS10AG3*Rumah Tangga 5*07020747***2*6E81BC0CE9054051B470000000000000*000652106053*Harimawan*Bimasakti 3/8-10**0000004000**PDAM KOTA SEMARANG*11*2016*84*84*3905*31550*0|0|6000|5000|2500|0*12*2016*84*84*0*31550*0|0|6000|5000|2500|0****************************   ";
        } else if($idpel1 == '06620111'){
            $resp = "BAYAR*WASMG*333207545*10*20150224151826*DESKTOP*06620111*0*0*000000036970*2000*" . $idoutlet . "*" . $pin . "*------*1138929*0**1061025*287052750*00*SUCCESSFUL*0000000*0*06620111***".$bill_q."*20150224043000012561*9819F39536B34ED79F60000000000000*Bunadi***0000002000**PDAM SEMARANG*1*15*0000000072 * 0000000084*0*000000036970************************************";
        } else {
            $resp = "BAYAR*WASMG*333207545*10*20150224151826*DESKTOP*06620111*0*0*000000036970*2000*" . $idoutlet . "*" . $pin . "*------*1138929*0**1061025*287052750*00*SUCCESSFUL*0000000*0*06620111***".$bill_q."*20150224043000012561*9819F39536B34ED79F60000000000000*Bunadi***0000002000**PDAM SEMARANG*1*15*0000000072 * 0000000084*0*000000036970************************************";
        }
    } else if ($kdproduk == "WAKABMLG") { // ID PELANGGAN
        $resp = "BAYAR*WAKABMLG*406632*10*20150401153632*H2H*8101120001982***000000025000*1800*" . $idoutlet . "*" . $pin . "**318607707*0**1061032*238016367*00*SUCCESSFUL*0000000*0*8101120001982***01*20150401043000011022*1A5FD4B1F60D4A6D9FC0000000000000*YUGUS***0000002100**PDAM KAB. MALANG*4*2015*0000001816 * 0000001826*0*000000025000************************************";
    } else if ($kdproduk == "WABALIKPPN") { // ID PELANGGAN
//
// 1 BLN
        $resp = "BAYAR*WABALIKPPN*312450772*10*20150203062340*DESKTOP*01030010346*0*0*000000094375*1600*" . $idoutlet . "*" . $pin . "*------*933039*0**1021008*279676320*00*SUCCESSFUL*0000000*0*01030010346***01*20150203043000000207*G2313H3G849RF8F*HJ.RUKAYAH**Cust Detail Info pindah ke bill_info70*0000001600**PDAM KOTA BALIKPPN*1*2015*00000129*00000141 *0*000000094375************************************";
    } else if($kdproduk == "WABAL"){
        $resp = "BAYAR*WABAL*2887988435*9*20180917102101*DESKTOP*050316*050316*050316*76400*2500*" . $idoutlet . "*" . $pin . "*------*1827593*1**2033*1119468654*00*EXT: APPROVE*0000000*RIDUAN*050316*050316*050316*1**RIDUAN*RIDUAN**024189HA2100001***PDAM BALANGAN*08*2018***0*76400*0***********************************   ";
    } else if ($kdproduk == "WABOGOR") { // ID PELANGGAN
// 1 BLN
        $resp = "BAYAR*WABOGOR*303308785*10*20150123095039*DESKTOP*07411152*0*0*000000078540*2500*" . $idoutlet . "*" . $pin . "*------*2173894*1**1021030*276888151*00*SUCCESSFUL*0000000*0*07411152***01*20150123043000003809*BGR13H3G849RF11*AAS RAMAESIH**Cust Detail Info pindah ke bill_info70*0000002500**PDAM KAB. BOGOR*12*2014*00000711*00000727 *0*000000078540************************************";
    } else if ($kdproduk == "WACLCP") { // ID PELANGGAN
// 1 BLN
        if ($idpel1 == "0105041625") {
            $resp = "BAYAR*WACLCP*340812584*10*20150304160904*H2H*0105041625***000000269600*6000*" . $idoutlet . "*" . $pin . "**11150000*1**1021012*289596702*00*SUCCESSFUL*0000000*0*0105041625***03*20150304043000017000*CLP13H3G849RF41*ETI WIDIASTUTI***0000006000**PDAM CILACAP*12*2014*00002694*0*0*0**1*2015*0*0*0*0**2*2015*0*00002762 *0*000000269600**********************";
        } else {
            $resp = "BAYAR*WACLCP*302475298*10*20150122104820*DESKTOP*0309211394*0*0*000000103500*2000*" . $idoutlet . "*" . $pin . "*------*75210*1**1021012*276633408*00*SUCCESSFUL*0000000*0*0309211394***01*20150122043000006019*CLP13H3G849RF11*NY SUMINEM**Cust Detail Info pindah ke bill_info70*0000002000**PDAM CILACAP*12*2014*00000320*00000347 *0*000000103500************************************";
        }
    } else if ($kdproduk == "WATAPIN") { // NO SAMBUNGAN
// 1 BLN
        if($idpel2 == '080707'){
            $resp = "BAYAR*WATAPIN*252333382*10*20141202131922*DESKTOP*0*080707*707*44500*2000*" . $idoutlet . "*" . $pin . "*------*295935*1**WATAPIN*259564465*00**0000000*00**080707*707*1***GURU IKAS*BAKARANGAN *Cust Detail Info pindah ke bill_info70*0**PDAM TAPIN*11*2014*0*10*0*44500*0***********************************";
        } else if($idpel2 == '011189'){
            $resp = "BAYAR*WATAPIN*1493778653*10*20170125084423*DESKTOP*0*011189*1189*710300*4000*" . $idoutlet . "*" . $pin . "*------*802812*3**WATAPIN*653050484*00*SUKSES*0000000*00*0*011189*1189*2***RUSMADI*A.YANI BITAHAN **0**PDAM TAPIN*11*2016*0*87*5000*368600*0*12*2016*0*79*2500*334200*0**************************** ";
        }
        
    } else if ($kdproduk == "WALMPNG") { // ID PELANGGAN (NO SAMBUNGAN)
// 2 BLN
        $resp = "BAYAR*WALMPNG*254364486*9*20141204105004*DESKTOP*010501*010501*010501*111440*5600*" . $idoutlet . "*" . $pin . "*------*997079*0**2011*260186651*00*EXT: APPROVE*0000000*00*010501*010501*010501*2***BUSRON TOHA*****PDAM LAMPUNG*11*2014***0*62640*0*10*2014***5000*43800*0****************************";
    } else if ($kdproduk == "WAJAMBI") { // ID PELANGGAN (NO SAMBUNGAN)
// 1 BLN
        $resp = "BAYAR*WAJAMBI*254418697*9*20141204113313*DESKTOP*03583*03583*03583*263750*2800*" . $idoutlet . "*" . $pin . "*------*3410054*1**2010*260203484*00*EXT: APPROVE*0000000*00*03583*03583*03583*1***ADMINISTRASI PELABUHAN*****PDAM JAMBI*12*2014***00000000*263750*0***********************************";
    } else if ($kdproduk == "WASDA") { // ID PELANGGAN && NO SAMBUNGAN
        if($idpel1=="02004159" || $idpel2=="02/I  /007/0147/2D"){
            sleep(40);
            $resp="BAYAR*WASDA*812314848*10*20160120170058*H2H*02004159***154100*3600*" . $idoutlet . "*------**-492264260*1**WASDA*439339830*00*SEDANG DIPROSES*0000000*00*02004159*02/I  /007/0147/2D*BA070147*2***SUKARNI*PERUM WISMA SARINADI III I-17**0**PDAM SIDOARJO*11*2015*0*18*7500*87300*0*12*2015*0*13*0*59300*0****************************";
        }else{ 
            if ($idpel1 == "01002679" || $idpel2 == "01/II /013/0083/2D") {
    // 3 BLN
                $resp = "BAYAR*WASDA*253013041*10*20141203081218*H2H*01002679***138500*5400*" . $idoutlet . "*" . $pin . "**-198812993*1**WASDA*259775515*00**0000000*00*01002679*01/II /013/0083/2D*AB130083*3***PERM. BUMI CITRA FAJ*SEKAWAN SEJUK C.10A*Cust Detail Info pindah ke bill_info70*0**PDAM SIDOARJO*9*2014*0*0*7500*40500*0*10*2014*0*0*7500*40500*0*11*2014*0*0*0*42500*0*********************";
            } else {
    // 6 BLN
                $resp = "BAYAR*WASDA*253013041*10*20141203081218*H2H*01002676***294500*10800*" . $idoutlet . "*" . $pin . "**-198812993*1**WASDA*259775515*00*SUKSES*0000000*00*01002676*01/II /013/0083/6D*AB130086*6***PERM. BUMI CITRA FAJ*SEKAWAN SEJUK C.16A*Cust Detail Info pindah ke bill_info70*0**PDAM SIDOARJO*5*2014*0*0*7500*40500*0*6*2014*0*0*7500*40500*0*7*2014*0*0*7500*42500*0*8*2014*0*0*7500*43500*0*9*2014*0*0*7500*44500*0*10*2014*0*0*0*45500*0";
            }
        }    
    } else if ($kdproduk == "WABONDO") { // ID PELANGGAN && NO SAMBUNGAN
// 3 BLN
        $resp = "BAYAR*WABONDO*250811076*10*20141130205945*DESKTOP*09000879*09/01/001/00879/RB*0*94130*4500*" . $idoutlet . "*" . $pin . "*------*5321738*1**FY834n7Vs4mdASP4H34n*259098168*00*EXT: PAYMENT SUKSES.*0000000*00*09000879*09/01/001/00879/RB**3***DWI YULIANA*PONCOGATI RT 11/5*Cust Detail Info pindah ke bill_info70*0**PDAM BONDOWOSO*8*2014*0*5*15000*9400*0|16150*9*2014*0*4*5000*7520*0|16150*10*2014*0*2*5000*3760*0|16150*********************";
    } else if ($kdproduk == "WAPLYJ") { // ID PELANGGAN (NO SAMBUNGAN)
        if($idpel1=="000001603" || $idpel2=="000001603"){
             sleep(40);
             $resp="BAYAR*WAPLYJ*812335004*9*20160120170918*H2H*000001603*000001603*000001603*609395*2500*" . $idoutlet . "*------**51595632*1**2001*439347931*00*SEDANG DIPROSES*0000000*00*000001603*000001603*000001603*1***MACHROEP*****PALYJA*12*2015***0*609395*0***********************************";
         }else{  
             $resp = "BAYAR*WAPLYJ*250176742*9*20141130064940*DESKTOP*000754677*000754677*000754677*22366*2500*" . $idoutlet . "*" . $pin . "*------*76228*0**2001*258884747*00*EXT: APPROVE*0000000*00*000754677*000754677*000754677*1***SITI RAODAH*****PALYJA*11*2014***0*22366*0***********************************";
         }
    } else if ($kdproduk == "WAAETRA") { // ID PELANGGAN (NO SAMBUNGAN)
        if($idpel1=="40064599" || $idpel2=="40064599" ){
            sleep(40);
            $resp="BAYAR*WAAETRA*812347618*9*20160120171443*H2H*40064599*40064599*40064599*84584*2500*" . $idoutlet . "*------**229452705*1**2002*439352987*00*SEDANG DIPROSES*0000000*00*40064599*40064599*40064599*1***YUDHO IRFANTO*****AETRA*12*2015***0*84584*0***********************************";
        } else if( ($idpel1=="60158686" || $idpel2=="60158686") && $ref2 == '836835042'){
            $resp = "BAYAR*WAAETRA*2117836499*9*20171017153305*H2H*60158686*60158686*60158686*3604079*2500*" . $idoutlet . "*" . $pin . "**244112035*1**2002*836835477*00*EXT: APPROVE*0000000*00*60158686*60158686*60158686*1***JERI S*****AETRA*09*2017***0*3604079*0***********************************  ";
        } else if( ($idpel1=="60123119" || $idpel2=="60123119") && $ref2 == '837230008' ){
            $resp = "BAYAR*WAAETRA*2118960396*9*20171018002331*H2H*60123119*60123119*60123119*13107279*2500*" . $idoutlet . "*" . $pin . "**2713910758*1**2002*837230129*00*EXT: APPROVE*0000000*00*60123119*60123119*60123119*1***HARYATI*****AETRA*09*2017***0*13107279*0***********************************      ";
        } else{ 
            $resp = "BAYAR*WAAETRA*252537037*9*20141202162830*DESKTOP*20040428*20040428*20040428*18045*2500*" . $idoutlet . "*" . $pin . "*------*6550271*1**2002*259623268*00*EXT: APPROVE*0000000*00*20040428*20040428*20040428*1***RIFAI*****AETRA*11*2014***0*18045*0***********************************";
        }
    } else if($kdproduk == "WAMEDAN"){
        $resp = "BAYAR*WAMEDAN*1644378718*10*20170404123901*H2H*0117080017***88000*7500*". $idoutlet ."*". $pin ."**145637240*1**1002*694616478*00*SUCCESSFUL*0000000*00*0117080017***03*#0#0#0*N.3#138081*SYAIFUL HALIM*PEMUDA BARU III 12*#10800.00#18600.00#18600.00***PDAM KOTA MEDAN (SUMUT)*02*2017*46000*47000*00020000*10800*0*03*2017*47000*49000*00020000*18600*0*04*2017*49000*51000*00000000*18600*0*********************   ";
    } else if ($kdproduk == "WAMJK") {
        $resp = "BAYAR*WAMJK*247145969*10*20141126170840*DESKTOP*0*0909040028*09.07.06.0336*122415*4000*" . $idoutlet . "*" . $pin . "*------*100695*1**WAMJK*257961468*00*SUKSES*0000000*00*0*0909040028*09.07.06.0336*2***SUKADI*SUKOANYAR-GONDANG *Cust Detail Info pindah ke bill_info70*0**PDAM KAB. MOJOKERTO (JATIM)*9*2014*0*19*11900*46000*0*10*2014*0*23*8415*56100*0****************************";
    } else if($kdproduk == 'WAKOPASU'){
        $resp = "BAYAR*WAKOPASU*861464269*9*20160219132130*H2H*c1-03943*10**68310*2500*".$idoutlet."*".$pin."**1015645936*1**WAKOPASU*453311834*00*SUKSES*0000000*00*c1-03943*c1-03943*c1-03943*1***DUMAH*Jl. Maluku No.9  RT.3/VIII**0**PDAM KOTA PASURUAN*1*2016*1438*1458|24600|26559|10|10*0*66310*2000|0|2460|2951|5700|5000|***************0********************";
    } else if($kdproduk == "WAPASU"){
        $resp = "BAYAR*WAPASU*2886750412*9*20180916171720*H2H*03050228*228**74540*2500*".$idoutlet."*".$pin."**2396717100*1**PASURUAN*1118856295*00*SUCCESS*0000000*00*03050228*228**1*03**MISLAN*BULU RT/W. 02/01 BULUSARI****PDAM KAB. PASURUAN*08*2018*5596*5618*0*74540*0****************************193******* ";
    } else if($kdproduk == 'WACIAMIS'){
        $resp = "BAYAR*WACIAMIS*2780129865*10*20180726104636*H2H*04030020147**6285324800289*81400*2500*".$idoutlet."*".$pin."**-53342216*1**400621*1069446941*00*SUCCESSFUL*1069446941*1*04030020147**R2 / Rumah Tang|17*1*104606---26072018*SWITCHERID*AMY MARYA, SE*PERUM B REGENCY 7 C.7*null---null---null*000000002500**PDAM CIAMIS*06*2018*574*591*0*81400*0***********************************    ";
    } else if ($kdproduk == "TVAORA") {
        $resp = "BAYAR*TVAORA*71914137*12*20121211145310*DESKTOP*7000231294***59000*2500*" . $idoutlet . "*" . $pin . "*------*1334765*1*2*TVAORA*49344261*00*SUCCESS***7000231294*1**1524753*NOER  HASANAH***59000***0**1524753*AORA TV**0*465646*2012-12-02*2012-12-17*BC01*59000*";
    } else if ($kdproduk == "TVTOPAS") {
        $resp = "BAYAR*TVTOPAS*245627246*10*20141125072552*DESKTOP*1503000389***76100*0*" . $idoutlet . "*" . $pin . "*------*473317*1**060901*257506739*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*1503000389*1*14110135302O**Sunarto***72600*0**3500**14110135302*TOPAS TV****01-DEC-14/31-DEC-14**EjcXrEZifq0=**";
    } else if ($kdproduk == "TVINDVS") {
        $resp = "BAYAR*TVINDVS*71529244*12*20121210144117*DESKTOP*401000939875***154000*2000*" . $idoutlet . "*" . $pin . "*------*968477*0**TVINDVS*49098671*00*EXT: APPROVE***401000939875*1***SUNARKO .                     ***000000154000*******500***06112012-05122012**401000939875                                               0401000939875SUNARKO .                     06112012-05122012000000154000**";
    } else if ($kdproduk == "HPXL") {
        $resp = "BAYAR*HPXL*70911713*11*20121208130710*DESKTOP*0818158020***000000054303*2500*" . $idoutlet . "*" . $pin . "*------*584331*1**HPXL*48720213*00*APPROVE*0000000*00*0818158020*1*0445554**RACHMAT SUHAPPY****XL*201   *201   *000000000*0000000005430300*0000000000*      *      *         *                *          *      *      *         *                *";
    } else if ($kdproduk == "HPTSEL") {
        $resp = "BAYAR*HPTSEL*242453920*10*20141121120636*DESKTOP*0811408689***000000048323*2500*" . $idoutlet . "*" . $pin . "*------*2879885*0**HPTSEL*256519489*00*APPROVE*0000000*00*0811408689*1***Bapak V.  TIKNO SARWOKO****TELKOMSEL*201411*201411*000000000*0000000004832300*0000000000*      *      *         *                *          *      *      *         *                *";
    } else if ($kdproduk == "HPESIA") {
        $resp = "BAYAR*HPESIA*71497318*11*20121210132040*DESKTOP*02198227230***000000066344*2500*" . $idoutlet . "*" . $pin . "*------*69128*1**HPESIA*49078987*00*APPROVE*0000000*00*02198227230*1***ACHMAD SURYADI****ESIA*201099*201099*000000000*0000000006634400*0000000000*      *      *         *                *          *      *      *         *                *";
    } else if ($kdproduk == "FNWOM") {
        if($idpel1=="201010034165"){
            sleep(40);
            $resp = "BAYAR*FNWOM*72854524*10*20121214121952*DESKTOP*201010034165***000001005500*0*" . $idoutlet . "*" . $pin . "*------*511172*0**4105*49921760*00*SEDANG DIPROSES*0000000*26*201010034165*01*104206300029*514846*ABDUL KODIR                    ***000000000000****91A5148070D946E98FFF6F2D87F425EA*WOM Finance              *006A                          *                                          *                         *B3899TNX      *016*008*13 Dec 12**000008016000**000001005500*000000000000****";
        } else if($idpel1=="808600015451" && $ref2 == '835539756'){
            $resp = "BAYAR*FNWOM*2114279792*10*20171016091917*DESKTOP*808600015451***9342000*0*" . $idoutlet . "*" . $poin . "*------*5998805*1**4105*835546583*00*APPROVE*0000000*26*808600015451*01*104202200640*073401*NURDIN ***000009342000****1148DCF1DCC24045B5A955E9C3BBC48D*WOM Finance *156 * * *E1472YG *012*005*19 Oct 17**000065391000**000009338000*000000000000*000000004000*000000000000**   ";
        } else if($idpel1=="802300064457" && $ref2 == '837070249'){
            $resp = "BAYAR*FNWOM*2118517321*10*20171017192017*DESKTOP*802300064457***12075000*0*" . $idoutlet . "*" . $pin . "*------*3194774*0**4105*837073311*00*APPROVE*0000000*26*802300064457*01*104202200640*196839*PAINO ***000012075000****719B75B4B45C4CB688AB58597DEB5161*WOM Finance *016A * * *B1391KMU *012*004*19 Oct 17**000016593000**000012071000*000000000000*000000004000*000000000000**   ";
        } else{
            $resp = "BAYAR*FNWOM*72854524*10*20121214121952*DESKTOP*201010034185***000001005500*0*" . $idoutlet . "*" . $pin . "*------*511172*0**4105*49921760*00*APPROVE*0000000*26*201010034185*01*104206300029*514846*ABDUL AZIZ                    ***000000000000****91A5148070D946E98FFF6F2D87F425EA*WOM Finance              *006A                          *                                          *                         *B3899TNC      *016*008*13 Dec 12**000008016000**000001005500*000000000000****";
        }
    } else if ($kdproduk == "FNMAF") { // MAF
        if($idpel1=="1201501064"){
            sleep(40);
            $resp="BAYAR*FNMAF*812308958*8*20160120165837*DESKTOP*1201501064***1059000*0*" . $idoutlet . "*------*------*1118894*2**MAF*439337539*00*SEDANG DIPROSES*0000000*122*1201501064*01*9237870692*BMS1201501064590801*DINIA ZAINAL***1059000*****MEGA AUTO FINANCE******2*20160201****1059000**0***";
        } else if($idpel1=="7981600075" && $ref2 == "837086806"){
            $resp = "BAYAR*FNMAF*2118569205*8*20171017193610*DESKTOP*7981600075***4147000*0*" . $idoutlet . "*" . $pin . "*------*874248*2**MAF*837091397*00*SUKSES*0000000*122*7981600075*01*9339167852*BMS7981600075554101*SULISTIYO***4147000*****MEGA AUTO FINANCE******13*20171018****4147000**0***    ";
        } else if($idpel1 == '1561600404' && $ref2 == '837605395'){
            $resp = "BAYAR*FNMAF*2120051300*8*20171018120848*DESKTOP*1561600404***11977000*0*" . $idoutlet . "*" . $pin . "*------*2705113*1**MAF*837606967*00*SUKSES*0000000*122*1561600404*01*6921160412*BMS1561600404282101*ANDI RABIA***11977000*****MEGA AUTO FINANCE******8*20170824****11977000**0***   ";
        }else{
            $resp = "BAYAR*FNMAF*73762439*10*20121217114919*DESKTOP*2641100868***000001001875*0*" . $idoutlet . "*" . $pin . "*------*1007826*1**4104*50459927*00*APPROVE*0000000*26*2641100868*01*104206300029*536153*IKHSANUDDIN                   ***000000000000****3E93C328038A4BD8948C234A04A5E337*PT MEGA AUTO FINANCE     *POS SUNGAI DANAU              *YAMAHA VEGA ZR 115 DB                     *MH35D9204BJ456707        *DA3933ZY      *017*013*26 Dec 12**000004375000**000000875000*000000126875****";
        }
    } else if ($kdproduk == "FNMEGA") {//MCF
        if($idpel1 == "7161700947" && $ref2 == "835896478"){
            $resp = "BAYAR*FNMEGA*2115274483*8*20171016152726*H2H*7161700947***2112000*0*" . $idoutlet . "*" . $pin . "**24756133*1**MCF*835896936*00*SUKSES*0000000*122*7161700947*01*8910001154*BMS7161700947465501*SUINDAH***2112000*****MEGA CENTRAL FINANCE******5*20171019****2112000**0***    ";
        } else if($idpel1 == "5411700393" && $ref2 == "837716203"){
            $resp = "BAYAR*FNMEGA*2120404296*8*20171018141512*H2H*5411700393***13075000*0*" . $idoutlet . "*" . $pin . "**1501387051*1**MCF*837716701*00*SUKSES*0000000*122*5411700393*01*1041524552*BMS5411700393344501*SURYA BUDIMAN***13075000*****MEGA CENTRAL FINANCE******3*20170914****13075000**0***   ";
        }else {
            $resp = "BAYAR*FNMEGA*72837114*10*20121214111554*DESKTOP*5701200409***000000661000*0*" . $idoutlet . "*" . $pin . "*------*3759068*1**4103*49911512*00*APPROVE*0000000*26*5701200409*01*104206300029*514367*ABDUL SAMID HARAHAP           ***000000000000****B9CDC33568B9454EB32A4E503208F42D*PT MEGA CENTRAL FINANCE  *Bekasi MCF                    *HONDA VARIO TECHNO 125 PGM FI NON CBS     *MH1JFB111CK196426        *B6213UWX      *030*005*14 Dec 12**000017182695**000000657695*000000003305****";
        }
    } else if ($kdproduk == "FNBAF") {
        if($idpel1=="122010051955" && $ref2 == '439365371'){
            sleep(5);
            $resp="BAYAR*FNBAF*812378668*10*20160120172709*DESKTOP*122010051955***000000622900*0*" . $idoutlet . "*------*------*395746*2**86003*439365842*00*SEDANG DIPROSES*BMS0001*102*122010051955*                    *000000000000009DAD6CAED3E61D917A**AZHARI*****0000000*006261000000*000000000000009DAD6CAED3E61D917A*Bussan Auto Finance*122*YMH.CIO.JCWFI**B 3507 KQH*029*020*20160119*0*000000000000*001*000000620900*000000005200*000000000000*000000002000*000000622900*000001865000";
        }else{
            $resp = "BAYAR*FNBAF*247682613*10*20141127103126*DESKTOP*636010013925***000000860000*0*" . $idoutlet . "*" . $pin . "*------*2045059*1**86003*258122378*00*SUCCESSFUL*BMS0001*94*636010013925*                    *000000000000081812111EFE8F5E70DA**WAL ASRI FADLI*****0000000*008686000000*000000000000081812111EFE8F5E70DA*Bussan Auto Finance*636*YMH.JUP.JUPMXCW**BG 2498 GA*023*004*20141125*0*000000000000*001*000000858000*000000010600*000000000000*000000002000*000000860000*000002576000";
        }    
    } else if($kdproduk == "FNCLMB"){
        $resp = "BAYAR*FNCLMB*1008497119*10*20160510134657*H2H*1001017858001***209000*0*".$id_outlet."*".$pin."**687251359*1**020002*497490481*00*EXT: APPROVE**107*1001017858001*1*1001017858001HADI SUTRONO 10000002090009 dari 180118/04/20160000002090000000002915*55000000082555C*HADI SUTRONO ***000000209000*****COLUMBIA*****01*9 dari 18*18/04/2016******0**000000209000*";
    } else if ($kdproduk == "ASRTOKIOS") {
        $resp = "BAYAR*ASRTOKIOS*364476149*12*20150325145852*DESKTOP*2140001AA***60000*0*".$id_outlet."*" . $pin . "*------*390992*1**ASRTOKIOS*298146317*00*TRANSACTION IS SUCCESSFUL*0000000*00*2140001AA*1***Daniel Haryadi*ASURANSI TM ABADI PLAN A*60000*******ASURANSI TOKIO MARINE LIFE*********";
    } else if($kdproduk == "ASRTOKIO"){
        $resp = "BAYAR*ASRTOKIO*364523609*12*20150325154109*DESKTOP*2140001AA***240000*0*".$id_outlet."*" . $pin . "*------*90992*1**ASRTOKIO*298158963*00*TRANSACTION IS SUCCESSFUL*0000000*00*2140001AA*4***Daniel Haryadi*ASURANSI TM ABADI PLAN A*240000*******ASURANSI TOKIO MARINE LIFE*********";
    } else if ($kdproduk == "ASRJWS" || $kdproduk == "ASRJWSI") {
        if ($idpel1 == "14001969964") {
            $resp = "BAYAR*ASRJWS*250552670*12*20141130160735*H2H*14001969964*CH001969964**1368615*0*" . $idoutlet . "*" . $pin . "**-448115214*1**ASRJWS*259002302*00*EXT:Sukses*01*PREMI : NOV-2014*PRM.112014*1***PUJI WARYANTI**1368615*******JIWASRAYA*********";
        } else {
            
            if($idpel1 == "03001953710"){
                $resp = "BAYAR*ASRJWS*1207848252*11*20160830143515*DESKTOP*03001953710*AE001953710**110*0*" . $idoutlet . "*" . $pin . "*------*3696055*1**ASRJWS*562070022*00*EXT: Sukses*01*PULIH-2016-08-30*PLH-2016-08-30*1***SUMITRA**110*******JIWASRAYA*********";
            } else if($idpel1 == '68001889322'){
                 $resp = "BAYAR*ASRJWS*251014583*12*20141201085321*H2H*68001889322*MC001889322**200000*0*" . $idoutlet . "*" . $pin . "**-524833319*1**ASRJWS*259164764*00*EXT:Sukses*01*PREMI : NOV-2014*PRM.112014*1*02*TOTAL : NOV-2014 s/d DEC-2014*SUHAENAH*400000*400000*******JIWASRAYA*********";
            } else if($idpel1 == '62002327375' && $ref2 == '836047219'){
                $resp = "BAYAR*ASRJWS*2115978411*11*20171016194104*DESKTOP*62002327375*LB002327375**5075000*0*" . $idoutlet . "*" . $pin . "*------*1896144*4**ASRJWS*836156742*00*EXT: Sukses*01*PREMI : OCT-2017*PRM.102017*1***SURYA BALKIS**5075000*******JIWASRAYA*********  ";
            } else if($idpel1 == '27002326950' && $ref2 == '836828076'){
                $resp = "BAYAR*ASRJWS*2117813206*11*20171017152447*H2H*27002326950*EF002326950**15375000*0*" . $idoutlet . "*" . $pin . "**179327694*1**ASRJWS*836828268*00*EXT: Sukses*01*PREMI : OCT-2017*PRM.102017*1***MATTALIA CLARA ANNALENE**15375000*******JIWASRAYA********* ";
            } else {
                $resp = "BAYAR*ASRJWS*251014583*12*20141201085321*H2H*".$idpel1."*MC001889322**200000*0*" . $idoutlet . "*" . $pin . "**-524833319*1**ASRJWS*259164764*00*EXT:Sukses*01*PREMI : NOV-2014*PRM.112014*1*02*TOTAL : NOV-2014 s/d DEC-2014*SUHAENAH*400000*400000*******JIWASRAYA*********";
            }
        }
    } else if ($kdproduk == "WASBY") {
        if(1==2){ 
            $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*" . $pin . "*------******99*Mitra Yth, mohon maaf, transaksi tidak dapat dilanjutkan untuk produk ini ";
        } else {
            if ($idpel1 == '4082411' && $ref2 == '134244522' && $nominal == '94140') {
                $resp = "BAYAR*WASBY*217746623*11*20131124020604*MOBILE_SMART*4082411***94140*2500*" . $idoutlet . "*" . $pin . "*------*13140930*0***134244528*00*PELUNASAN TAGIHAN SUKSES*0000000*4082411***0***YACUB ANDRES.Y*MERBABU 1*2***94140*PDAM SURABAYA***4D*11*2013***94*105*70640*0**23500**************************************************";
            } else if($idpel1 == '5029696'){
                
                $resp = "BAYAR*WASBY*884734632*10*20160304164312*H2H*5029696***000000006890*2000*HH43738*------**4993019*1***460291613*00*PELUNASAN TAGIHAN SUKSES*0000000*5029696***1***BALAI RW.III*BANYU URIP KIDUL 4 5*1***6890*PDAM SURABAYA***2A.1*03*2016***32*32*6140*0**750************************************************00010301**";
            } else if($idpel1 == '4106210' && $ref2 == '238074227' && $nominal == '15140'){
                $resp = "BAYAR*WASBY*1510566001*8*20170202160619*H2H*4106210***15140*2000*BS0004*------**954920*1***238074246*00*PELUNASAN TAGIHAN SUKSES*0000000*4106210***1***WIWIN*TAMBAK ASRI TERATAI 65 B*2***15140*PDAM SURABAYA***3A*04*2016***48*55*7640*7500**0**************************************************";
            }else {
                $resp = "BAYAR*WASBY*275137160*10*20141224092857*H2H*1013225***35490*2000*" . $idoutlet . "*" . $pin . "**16347515*1***267411812*00*PELUNASAN TAGIHAN SUKSES*0000000*1013225***1***DADANG SOEKARDI*WONOKROMO S.S BARU 2 8*1***35490*PDAM SURABAYA***3A*12*2014***4589*4613*27240*7500**750************************************************298348571**";
            }
        }

    } else if ($kdproduk == "FNADIRAH" || $kdproduk == 'FNADIRA') {

        if (substr($idpel1, 0, 4) == "0035") {
            $resp = "BAYAR*FNADIRAH*363028942*4*20150324095916*YM*003587535679***283000*4000*" . $idoutlet . "*" . $pin . "*------*5527243*1***297683265*00*Transaksi Anda sedang diproses, silahkan cek web report 10 menit lagi*0000000*58*003587535679*1**00000000000000*nurdin wijaya***283000****00000000000000*ADIRA FINANCE****00000000000000*3*3****03 apr 15*283000****283000*283000";
        } else {
            $resp = "BAYAR*FNADIRAH*207675546*10*20141016135232*DESKTOP*020913107656***575000*2500*" . $idoutlet . "*" . $pin . "**876274*1**FNADIRA*244231502*00*EXT: APPROVE*02*27*020913107656*01***IMAM SUDRAJAT*KP CIKARANG 45/09**575000*****Adira Finance***3117757740*F2096VR*14*14*03 OCT 14**0**575000*0*2000*300*575000*575000";
        }
    } elseif ($kdproduk == "WAJMBR" || $kdproduk == "WAJMBRIDM") {
        $resp = "BAYAR*WAJMBR*209510403*10*20141018100203*H2H*24035*24035**12500*2500*" . $idoutlet . "*" . $pin . "*------*-201635684*1**WAJMBR*244922439*00*SUKSES*0000000*00*24035*24035*24035*1***Aswanto*Perumh New Pesona AD-18*Cust Detail Info pindah ke bill_info70*0**PDAM JEMBER*09*2014*5150*5150*0*0*12500***************0********************";
    } elseif ($kdproduk == "WAPLMBNG") {// ID PELANGGAN  (NO SAMBUNGAN)
        
        if($idpel1 == '210689648'){
            $resp = "BAYAR*WAPLMBNG*210689648*9*20141019155534*H2H*210689648*210689648*210689648*68500*1500*" . $idoutlet . "*" . $pin . "*------*1571965*1**2009*245344877*00*EXT: APPROVE*0000000*00*210689648*210689648*210689648*1***M. YUNUS AS*****PDAM PALEMBANG*10*2014***0*68500*0***********************************";
        } else if($idpel1 == '7B085002500018'){
            $resp = "BAYAR*WAPLMBNG*1493617364*10*20170125070453*H2H*7B085002500018*7B085002500018*7B085002500018*100222*3000*" . $idoutlet . "*" . $pin . "**1088953*1**2009*653008063*00*EXT: APPROVE*0000000*00*7B0850250018*7B085002500018*7B085002500018*2***ELMA NILYANA*****PDAM PALEMBANG*12*2016***0*52497*0*01*2017***0*47725*0****************************   ";
        }
    } elseif ($kdproduk == "WABGK") {
        if ($idpel2 == "0104032417") {
            $resp = "BAYAR*WABGK*209448784*10*20141018091155*H2H*0*0104032417**50500*2500*" . $idoutlet . "*" . $pin . "*------*-199496639*1**WABGK*244895740*00*SUKSES*0000000*00*0*0104032417*0104032417*1***IDA SUSANTI*Jl. HALIM PERDANA KUSUMA GG.II *Cust Detail Info pindah ke bill_info70*0**PDAM BANGKALAN*9*2014*0*15*0*50500*0***********************************";
        } else {
            $resp = "BAYAR*WABGK*273941724*10*20141223052918*DESKTOP*0*0101001861*01-1-00186A*366275*6000*" . $idoutlet . "*" . $pin . "*------*1443172*0**WABGK*267026682*00*SUKSES*0000000*00*0*0101001861*01-1-00186A*4***NURJANNAH*KH. MARZUQI *Cust Detail Info pindah ke bill_info70*0**PDAM BANGKALAN*8*2014*0*25*12600*84000*0*9*2014*0*25*12600*84000*0*10*2014*0*19*9435*62900*0*11*2014*0*26*13140*87600*0**************";
        }
    } elseif ($kdproduk == "WABJN") {// ID PELANGGAN DAN NO SAMBUNGAN
        $resp = "BAYAR*WABJN*186513*10*20141124115558*DESKTOP*0*0111002*0*195500*4000*" . $idoutlet . "*" . $pin . "*------*499969100*1**WABJN*237998914*00**0000000*00*0*0111002*0*2***EKO SUDARMANTO*Jl. VETERAN 0 0*Cust Detail Info pindah ke bill_info70*0**PDAM BOJONEGORO*9*2014*0*0*0*0*84000*10*2014*0*10*0*27500*84000****************************";
    } elseif ($kdproduk == "WABDG") { // ID PELANGGAN (NO SAMBUNGAN)
        if($idpel1=="00A08650410"){
            sleep(40);
            $resp="BAYAR*WABDG*812362363*9*20160120172051*H2H*00A08650410*00A08650410*00A08650410*50000*2800*" . $idoutlet . "*------**-500019880*1**2003*439358976*00*SEDANG DIPROSES*0000000*00*00A08650410*00A08650410*00A08650410*1***DRS.SUBCHAN DWIYANTO*****PDAM BANDUNG*12*2015***00000000*50000*0***********************************";
        }else{
            if ($idpel1 == "00201410060") {
                $resp = "BAYAR*WABDG*209305561*9*20141018061332*DESKTOP*00201410060*00201410060*00201410060*90100*2800*" . $idoutlet . "*" . $pin . "*------*1135145*1**2003*244828224*00*EXT: APPROVE*0000000*00*00201410060*00201410060*00201410060*1***KOMAR*****PDAM BANDUNG*09*2014***0*90100*0***********************************";
            } else {
                $resp = "BAYAR*WABDG*274342266*9*20141223124533*H2H*00008901103*00008901103*00008901103*114400*2500*" . $idoutlet . "*" . $pin . "**-367882673*1**2003*267159669*00*EXT: APPROVE*0000000*00*00008901103*00008901103*00008901103*1***OEY TJWAN LIEN*****PDAM BANDUNG*11*2014***10400*104000*0***********************************";
            }
        }    
    } elseif ($kdproduk == "TVNEX") {
        $resp = "BAYAR*TVNEX*210978307*10*20141019205336*DESKTOP*622177311***108900*0*" . $idoutlet . "*" . $pin . "*------*1359004*1**060801*245467415*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*622177311*1*00000000000000614890*614890*IRVAN SOFIAN**NEXSPORTS PLATINUM MOVIES 1 BULAN*108900*0**0**00000000000000614890*NEX MEDIA****11-10-2014**MhxAQjNjhYE=**";
    } elseif ($kdproduk == "HPSMART") {
        $resp = "BAYAR*HPSMART*209747596*10*20141018134900*DESKTOP*088271084560***25388*0*" . $idoutlet . "*" . $pin . "*------*3685965*1**016004*245006387*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*088271084560*1*24912363505**B620-1010945-WAHYUDI-K***25388*SMART***************";
    } elseif ($kdproduk == "HPMTRIX") {
        $resp = "BAYAR*HPMTRIX*209341713*10*20141018072311*DESKTOP*08155101252***000000027500*2500*" . $idoutlet . "*" . $pin . "*------*1935079*0**HPMTRIX*244846204*00*APPROVE*0000000*00*08155101252*1***DIAN INDRESWARI****INDOSAT*201016*201016*000000000*0000000002750000*0000000000*      *      *         *                *          *      *      *         *                *";
   } elseif ($kdproduk == "FNFIF") {
       $resp = "BAYAR*FNFIF*2528242479*10*20180404144222*DESKTOP*507002360917***671000*3000*" . $idoutlet . "*" . $pin . "*------*539712*4**204111*966644876*00*SUCCESSFUL*50*101*507002360917*1*04042018---144220*216056*DARSONO*****0*3000*216056---5---0*FIF*K****036*005*13/04/2018**671000***0**0**    ";
    } elseif ($kdproduk == "TVORG50") {
        $resp = "BAYAR*TVORG50*261828123*10*20141211155319*H2H*81477650***50000*0*HH10774*------**5726102*1**060701*262686417*00*EXT:  APPROVED, TRANSACTION IS DONE WITHOUT  ERROR*0000000*00*81477650**abbb3a85a55aaa8a*****50000*****abbb3a85a55aaa8a*ORANGE ";
    } elseif ($kdproduk == "TVORG80") {
        $resp = "BAYAR*TVORG80*253708249*10*20141203173324*DESKTOP*33019333***80000*0*" . $idoutlet . "*" . $pin . "*------*245076*1**060701*259979931*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*33019333**6b5161b1161b41b1*****80000*****6b5161b1161b41b1*ORANGE TV******IRCXACKq4aU=**";
    } elseif ($kdproduk == "TVORG100") {
        $resp = "BAYAR*TVORG100*254031462*10*20141203220247*DESKTOP*10014413***100000*0*" . $idoutlet . "*" . $pin . "*------*378192*0**060701*260064167*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*10014413**33266363333c6663*****100000*****33266363333c6663*ORANGE TV******AhB1EUQjOfM=**";
    } elseif ($kdproduk == "TVORG300") {
        $resp = "BAYAR*TVORG300*252574671*10*20141202170043*DESKTOP*49045011***300000*0*" . $idoutlet . "*" . $pin . "*------*2523988*1**060701*259635362*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*49045011**ff1fe5ee88616ef0*****300000*****ff1fe5ee88616ef0*ORANGE TV******WxRUEUFmK/Q=**";
    } elseif ($kdproduk == "HPTHREE") {
        $resp = "BAYAR*HPTHREE*252312187*10*20141202125907*H2H*08984222333***55000*0*" . $idoutlet . "*" . $pin . "**27628401*1**012101*259558586*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*08984222333*1*1986015835**INTAN NUR AZIZA***55000*THREE***************";
    } elseif ($kdproduk == "HPFREN") {
        $resp = "BAYAR*HPFREN*240645388*10*20141119215722*DESKTOP*08885088008***47094*0*" . $idoutlet . "*" . $pin . "*------*200569*1**016002*255717645*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*08885088008*1*000000717644**HERU  WIDIJANTO***47094*FREN***************";
    } else if($kdproduk == "TVBIG"){
        $resp = "BAYAR*TVBIG*493383806*11*20150625094527*DESKTOP*1072161447***138499*0*" . $idoutlet . "*" . $pin . "*------*5286949*3**008001*339062776*00*EXT: APPROVE***1072161447*1**Mr. syahrun as *Mr. syahrun as ***138499*****CLOSE PAYMENT *TV BIG*0**008001***00010721614470080011CLOSE PAYMENT 000000138499Mr. syahrun as **";
    } else if($kdproduk == "WADEPOK"){
        if($idoutlet =='HH10632'){
            $bill_q = "1";
        } else {
            $bill_q = '01';
        }
        $resp = "BAYAR*WADEPOK*653113900*10*20151012114012*DESKTOP*02440121*0*0*000000197900*2500*" . $idoutlet . "*" . $pin . "*------*142611*1**1141062*391075500*00*SUCCESSFUL*0000000*0*02440121***".$bill_q."*20151012043000011048*20151012113910000000000000026359*PT. PRIMAMAS PERKASA***0000002500**PDAM KOTA DEPOK (JABAR)*9*2015*441*473*0*000000197900************************************";
    } else if($kdproduk == "WABATAM"){
        $resp = "BAYAR*WABATAM*2803949954*9*20180807092514*DESKTOP*52693*52693*52693*23180*2500*" . $idoutlet . "*" . $pin . "*------*9765030*1**2029*1080537646*00*EXT: APPROVE*0000000*OTORITA BATAM*52693*52693*52693*1**OTORITA BATAM*OTORITA BATAM**188792513001***PAM ATB BATAM*08*2018***0*23180*0***********************************    ";
    } else if($kdproduk == "WAKOBGR"){
        if($idoutlet =='HH10632'){
            $bill_q = "1";
        } else {
            $bill_q = '01';
        }
        $resp = "BAYAR*WAKOBGR*654743021*10*20151013115733*DESKTOP*15801274*0*0*000000261000*2500*". $idoutlet ."*".$pin."*------*934705*2**1141027*391618281*00*SUCCESSFUL*0000000*0*15801274***".$bill_q."*20151013043000033105*20151013115333000000000000917680*NENENG NS***0000002500**PDAM KOTA BOGOR*9*2015*0*1953*0*000000261000************************************";
    } else if(substr($kdproduk, 0, 4) == "TVKV"){
        $resp = "BAYAR*TVKV100*802199962*10*20160114210406*DESKTOP*110396769***100000*0*" . $idoutlet . "*" . $pin . "*------*6008826*3**061201*436126123*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*110396769*1*20160114210401182149**ATIK SUNARYA***110000*0**0**20160114210401182149*K-Vision TV******ET9VbQE3Gbc=**K VISION (100.000)";
    } else if(substr($kdproduk, 0, 5) == "TVSKY"){
        $resp = "BAYAR*TVSKYFAM1*866727005*9*20160222134606*H2H*37000004733***40000*0*".$idoutlet."*".$pin."**283634645*1**061301*455136372*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*37000004733*1*379800*****40000*****379800*TV SKYNINDO*****20160327*AjBHdjciG+Q=**SKYNINDO TV FAMILY 1 BLN (40.000)";
    } else if($kdproduk == "TVINNOV"){
        $resp = "BAYAR*TVINNOV*552442764*11*20150805181030*DESKTOP*10504754***368000*0*".$idoutlet."*".$pin."*------*62518*1**006007*357923002*00*EXT: APPROVE***10504754*1** SATRIA YUDA PRASETYO * SATRIA YUDA PRASETYO ***368000***** P150805181328331 *TV INNOVATE*0**000000368000 ***10504754 000000368000 SATRIA YUDA PRASETYO 20150704 I150805181248384 **";
    } else if($kdproduk == "WAPROLING"){
        $resp = "BAYAR*WAPROLING*1359913423*10*20161122110154*H2H*04000977***51750*2500*".$idoutlet."*".$pin."**790177967*1**400171*616420045*00*SUCCESSFUL*0000000*0*04000977***1*110152---22112016*SWITCHERID*FARAH SUDARSIH*DS.PATOKAN*null---null---null*000000002500**PDAM PROBOLINGGO*10*2016***0*51750*0***********************************";
    } else if($kdproduk == "WAKOSOLO"){
        $resp = "BAYAR*WAKOSOLO*1361597926*10*20161123091219*H2H*00030619***47300*1700*".$idoutlet."*".$pin."**580098884*1**400251*616860892*00*SUCCESSFUL*0000000*1*00030619***1*090250---23112016*SWITCHERID*Ny Misrini Iswandi*Mangga II A 75 RT 01/0*null---null---null*000000001700**PDAM KOTA SOLO*10*2016***4300*43000*0***********************************";
    } else if($nominal == '350000' && $kdproduk == "ASRCAR" && $ref2 == '617490048' && $idpel1 == '2377711000395827'){
        $resp = "BAYAR*ASRCAR*1363987318*9*20161124132620*H2H*2377711000395827***350000*0*".$idoutlet."*".$pin."*------*21902246*1**ASRCAR*617490293*00*SUCCES*0000000*00001*2377711000395827*01***BONG KIM SIN**350000*******AJ CENTRAL ASIA RAYA****350000*77711000395827*1100000000395827*NANTIKAN PRODUK TERBARU DAN PROMO DARI CAR - BECAUSE WE DO CARE**  ";
    } else if($kdproduk == 'WASAMPANG'){
        $date = date('YmdHis');
        if($idpel2 == '0101010080'){
            $resp = "BAYAR*WASAMPANG*1466747727*10*".$date."*DESKTOP*0*0101010080*01/I /001/0080/a*134211*5000*".$idoutlet."*".$pin."*------*945877*2**WASAMPANG*645563340*00*SUKSES*0000000*00*0*0101010080*01/I /001/0080/a*2***TIMBUL SUNGKONO*JL. SELONG PERMAI **0**PDAM TRUNOJOYO SAMPANG*11*2016*0*17*12201*61005*0*12*2016*0*17*0*61005*0****************************   ";
        }
    } else if($kdproduk == 'ASRPRU'){
        $date = date('YmdHis');
        $resp = "BAYAR*ASRPRU*2672085157*8*".$date."*WEB*11895488***500000*0*".$idoutlet."*".$pin."*------*2650605*4**PRUDENTIAL*1023349631*00*Sukses!*0000000**11895488*01*000000500000###ACU13326409084872646*25000###IDR###01*KURNIA RISTIYANI*********PRUDENTIAL*****0****    ";
    }
    /* tambahan */

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_kdproduk         = $frm->getKodeProduk();
    $r_tanggal          = $frm->getTanggal();
    $r_idpel1           = $frm->getIdPel1();
    $r_idpel2           = $frm->getIdPel2();
    $r_idpel3           = $frm->getIdPel3();
    $r_nominal          = (int) $frm->getNominal();
    $r_nominaladmin     = (int) $frm->getNominalAdmin();
    $r_idoutlet         = $frm->getMember();
    $r_pin              = $frm->getPin();
    $r_sisa_saldo       = $frm->getSaldo();
    $r_idtrx            = $frm->getIdTrx();
    $r_status           = $frm->getStatus();
    $r_keterangan       = $frm->getKeterangan();
    $r_saldo_terpotong  = 0;
    $r_nama_pelanggan   = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }
    $token2 = $cektoken[3];

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
        $url_struk = "https://202.43.173.234/struk/?id=" . $url;
    }

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"           	=> (string) $r_idpel1,
        "IDPEL2"           	=> (string) $r_idpel2,
        "IDPEL3"           	=> (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) trim(str_replace('.', '', $r_nama_pelanggan)),
        "PERIODE"           => (string) $r_periode_tagihan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => (string) $r_reff3,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) str_replace('.', '[dot]', $url_struk),
    );

    $is_return = true;
 	$implode = implode('.', $params);
    if($r_idtrx != $ref2){
        $get_mid = get_mid_from_idtrx($r_idtrx);
        $get_step = get_step_from_mid($get_mid) + 1;
        writeLog($get_mid, $get_step, $host, $receiver, $implode, $via);
    }
    return $implode;
}

function pay_detail($req) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $kdproduk   = strtoupper($req['produk']);
    $idpel1     = strtoupper($req['idpel1']);
    $idpel2     = strtoupper($req['idpel2']);
    $idpel3     = strtoupper($req['idpel3']);
    $nominal    = strtoupper($req['nominal']);
    $idoutlet   = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']);
    $ref2       = strtoupper($req['ref2']);
    $ref3       = strtoupper($req['ref3']);
    $field      = 11;
    
    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "::1" ) {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    global $pgsql;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    if ($kdproduk == "PLNPASC") {
        $kdproduk = "PLNPASCH";
    } else if ($kdproduk == "PLNPRA") {
        $kdproduk = "PLNPRAH";
    } else if ($kdproduk == "HPTSEL") {
        $kdproduk = "HPTSELH";
    } else if ($kdproduk == "PLNNON") {
        $kdproduk = "PLNNONH";
    }

    $ceknom = getNominalTransaksi(trim($ref2));
    $cektoken = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal - 1600), trim($kdproduk), trim($idoutlet)); //tambahan
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
    if($kdproduk == "PAJAKGRTLT" || $kdproduk == "PAJAKGRTL"){
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = $idpel1;
    }
    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];

    /* tambahan */
    if($kdproduk == "PGN"){
        $resp = "BAYAR*PGN*2977389842*10*20181030161201*WEB*0110011437***140520*2500*" . $idoutlet . "*" . $pin . "*------*8290773*1**PGN*1161888441*00*SUKSES*0110011437*L HUTAGALUNG*26 M3*Sep2018*INV1181030141660*140520*2500*143020***72888*REF    ";
    } else if($kdproduk == "KKBNI"){
        $resp = "BAYAR*KKBNI*2656829460*9*20180530073815*H2H*5489888810362324***490300*6000*" . $idoutlet . "*" . $pin . "**18963495*1**BNI*1016820300*00*Sukses!*0559*00*5489888810362324*01***DADANG ISKANDAR SKM********BNI*14052018*03062018****490300* ";
    } else if ($kdproduk == "TELEPON") {
// 2 BULAN
        $resp = "BAYAR*TELEPON*12670604*11*20120524125233*DESKTOP*021*88393209**000000137580*7500*" . $idoutlet . "*" . $pin . "*D4B6EA34*405228*1*0*001001*18156631*00*APPROVE*021*088393209*02*0008*3*203A       *50860*204A       *45860*205A       *40860* LINA SIREGAR                 *";
    } else if ($kdproduk == "SPEEDY") {
        if ($idpel1 == "0141148100225") {
// 2 BULAN
            $resp = "BAYAR*SPEEDY*72138103*11*20121212092242*DESKTOP*0141148100225***000000364113*5000*" . $idoutlet . "*" . $pin . "*------*154094*1*0*001001*49484197*00*APPROVE*0141*148100225*04*0001*2**0*211A       *149613*212A       *214500* MARIYANTO                    *";
        } else {
// 1 BULAN
            $resp = "BAYAR*SPEEDY*12633844*11*20120524112049*XML*0162406900527***000000744750*2500*" . $idoutlet . "*" . $pin . "*A7E252CC*1197532*0*0*001001*18142680*00*APPROVE*0162*406900527*06*0006*1**0**0*205A       *744750* GEREJA GBI NANGA BUL         *";
        }
    } else if ($kdproduk == "TVTLKMV") {
        if ($idpel1 == "122429250104") {//2 bulan
            $resp = "BAYAR*TVTLKMV*71150072*11*20121209114401*DESKTOP*122429250104***000000287500*5000*" . $idoutlet . "*" . $pin . "*------*298154*1*0*001001*48864190*00*APPROVE*0122*429250104*02*0004*2**0*211A       *150000*212A       *137500* ANDRY PRAMONO                *";
        } else {
            $resp = "BAYAR*TVTLKMV*12632313*11*20120524111719*XML*127246500157***000000099000*1950*" . $idoutlet . "*" . $pin . "*D3A84F25*283300*1*0*001001*18141915*00*APPROVE*0127*246500157*08*0001*1**0**0*205A       *99000*BAITUS MONGJENG               *";
        }
    } else if (substr($kdproduk, 0,6) == "PLNNON" ) {
        $resp = "BAYAR*PLNNON*12643503*10*20120524114128*MOBILE*5392112011703***696400*1600*" . $idoutlet . "*" . $pin . "*FASTPAY*514644*1*1*99504*18147159*00*SUCCESSFUL*0000000*5392112011703                   *53*012*PENYAMBUNGAN BARU        *20120524*23062012*542122123488*JAYUSMAN                 *083349C5AB7B4738A3EBE7352CDA9E6A*62D9201911A4437188E8C1734539FE0C*53921*Jl Pahlawan No 39 Rangkasbitung    *123            *2*00000000069640000*2*00000000069800000*2*0000160000*0101200000000000000000          *    *000000000*00********Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
    } else if (substr($kdproduk, 0,7) == "PLNPASC") {
        //511030176462
        if($idpel1=="323360001335"){
            if($idpel1=="323360001335" && $ref2 == '50512928'){
                //sleep(40);
                //handle simulator case 13
                //$resp = "BAYAR*".$kdproduk."***".$tanggal."*H2H*".$idpel1."*".$idpel2."*".$idpel3."***" . $idoutlet . "*-----*------******00*SEDANG DIPROSES*********************************************************************";

                //handle simulator case 15
                $resp = "BAYAR*".$kdproduk."***".$tanggal."*H2H*".$idpel1."*".$idpel2."*".$idpel3."***" . $idoutlet . "*-----*------*******Masih ada transaksi dengan id pelanggan sama yang sedang dalam proses. Silahkan cek dalam 5-10 menit lagi di Report Transaksi di Aplikasi Desktop atau Web Report*********************************************************************";

            } else {
                $resp = "BAYAR*".$kdproduk."***".$tanggal."*H2H*".$idpel1."*".$idpel2."*".$idpel3."***" . $idoutlet . "*-----*------******11*Inquiry record tidak ditemukan. Silahkan melakukan inquiry ulang*********************************************************************";            
            }
        } else {
            if($idpel=="523520409670"){   
                sleep(40);
                $resp = "BAYAR*".$kdproduk."*831295610*10*".$tanggal."*H2H*".$idpel1."*".$idpel2."****".$idoutlet."*".$pin."*------******00*SEDANG DIPROSES*0000000*".$idpel1."*******************************************************************";
            }else{
                if($idpel1 == "521050065222"){
                    $resp = "BAYAR*PLNPASCH*1100054273*10*20160630153934*H2H*521050065222***116368*2500*" . $idoutlet . "*" . $pin . "*------*19518181899*1**99501*525590627*00*SUCCESSFUL*VI105V3*521050065222*1*1*01*0SMB213515441905BDCF0265229CF14D*TUKIYO *52105*123 *R1 *000000450*000002500*201606*20062016*00000000*00000113368*D0000000000*0000000000*000003000*01234700*01257700*00000000*00000000*00000000*00000000*******************************************000000000000*Rincian Tagihan dapat diakses di www.pln.co.id atau PLN Terdekat";
                } else if ($idpel1 == "323360001351" && $ref2 = "50512928" && $nominal == "5353500000") {//3 bln
                    $resp = "BAYAR*PLNPASC*73849112*11*20121217145957*DESKTOP*323360001351***5353500000*5700*" . $idoutlet . "*" . $pin . "*------*2209930*1*1*99501*50513190*00*SUCCESSFUL*0000000*323360001351*3*3*03*BC74455477014318BFADB709029F36B5*MILLI.M                  *32330*123            *R1  *000000450*000004800*201210*20102012*00000000*00000014685*D0000000000*0000000000*000009000*00006200*00010400*00000000*00000000*00000000*00000000*201211*20112012*00000000*00000014289*D0000000000*0000000000*000006000*00010400*00014500*00000000*00000000*00000000*00000000*201212*20122012*00000000*00000009561*D0000000000*0000000000*000000000*00014500*00017300*00000000*00000000*00000000*00000000*****************000000000000*Rincian Tagihan dapat diakses di www.pln.co.id";
                } else if($idpel1 == "151000042568" && $ref2 == "835567984"){
                    $resp = "BAYAR*PLNPASC*2114349021*10*20171016094146*WEB*151000042568*SAHADI*01*1081168*2500*" . $idoutlet . "*" . $pin . "*------*218202*1**501*835573200*00*TRANSAKSI SUKSES*0000000*151000042568*1*1*01*0BMS210ZF9F9CB3A27C18C6B31BE76AE*SAHADI *15100*123 * R1*000002200*000000000*201710*20102017*00000000*00001081168*D0000000000*0000000000*000000000*07472500*07564600*00000000*00000000*00000000*00000000*****************************************5EC3F36854EC4B258E3990222F5E5741**1081168*Rincian Tagihan dapat diakses di www.pln.co.id,Informasi Hubungi Call Center:123 Atau Hub. PLN Terdekat:";
                } else if($idpel1 == "513030034100" && $ref2 == "837525931"){
                    $resp = "BAYAR*PLNPASCH*2119804224*10*20171018104535*H2H*513030034100***29314918*2500*" . $idoutlet . "*" . $pin . "*------*9789298*1**501*837526754*00*TRANSAKSI SUKSES*0000000*513030034100*1*1*01*0BMS210ZC83A1EB2C04F7562F8882E04*PROY DTC SONGGORITI *51303*123 * P1*000082500*000000000*201710*20102017*00000000*00029314918*D0000000000*0000000000*000000000*05106200*05172800*00000000*00000000*00000000*00000000*****************************************E4AC6E62191943F697BE884DB7AEC82E**29314918*Rincian Tagihan dapat diakses di www.pln.co.id,Informasi Hubungi Call Center:123 Atau Hub. PLN Terdekat:   ";
                } else if($idpel1 == "534210870159" && $ref2 == "1037282114"){
                    $resp = "BAYAR*PLNPASCH*2705172852*10*20180621155356*H2H*534210870159***528874*3000*" . $idoutlet . "*" . $pin . "*------*3802283070*1**501*1037409518*58*EXT: PROSES TRANSAKSI TIDAK BISA DILAKUKAN KARENA TERDAPAT KETIDAKCOCOKAN DATA *0000000*534210870159*1*1*01**UJANG NANA SUYATNA *53421*123 * R1M*000000900*000000000*201806*22062018*00000000*000000528874*D0000000000*0000000000*000000000000*00017103*00017477*00000000*00000000*00000000*00000000*****************************************E34E62F70CB4482F845EAD65C476B8AF**528874*    ";
                } else if($idpel1 == "321209707325" && $ref2 == "1037312283"){
                    $resp = "BAYAR*PLNPASCH*2705053349*10*20180621150137*H2H*321209707325***365877*3000*" . $idoutlet . "*" . $pin . "*------*418304348*1**501*1037357064*34*EXT: TAGIHAN SUDAH TERBAYAR*0000000*321209707325*1*1*01**ST MAEMUNAH *32111*123 * R1M*000000900*000000000*201806*22062018*00000000*000000365877*D0000000000*0000000000*000000000000*00001467*00001711*00000000*00000000*00000000*00000000*****************************************9B5FF8539C4C4FA2921C07FEBE4CAA2B**365877*    ";
                } else if($idpel1 == "122030400910" && $ref2 == "1067800142"){
                    // 3bulan
                    $resp = "BAYAR*PLNPASC*2776318086*10*20180724111803*H2H*122030400910**6282161380690*89108*5000*" . $idoutlet . "*" . $pin . "*------*-257760949*1**501*1067800385*00*TRANSAKSI SUKSES*0000000*122030400910*2*2*02*0BMS210Z29D4C1513451E796DA0E3B10*AMRAN *12203*123 * R1*000000450*000000000*201806*22062018*00000000*000000046316*D0000000000*0000000000*000000006000*00006208*00006311*00000000*00000000*00000000*00000000*201807*20072018*00000000*000000033792*D0000000000*0000000000*000000003000*00006311*00006391*00000000*00000000*00000000*00000000****************************30432B94343D42C3A05F29C58450C886**89108*\"Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :\"  ";
                } else {//4 bulan
                    $resp = "BAYAR*PLNPASC*12632010*11*20120524111642*XML*538731734541***887849*12000*" . $idoutlet . "*" . $pin . "*A76361C0*7811856*1*1*99501*18141728*00*SUCCESSFUL*0000000*538731734541*4*4*04*08C536F6E16347419AFD252F8EC979A7*R SUMALI2                *53873*123            *R1  *000000900*000012000*201202*20022012*00000000*00000029247*D0000000000*0000001000*000009000*00873500*00876600*00000000*00000000*00000000*00000000*201203*20032012*00000000*00000232713*D0000000000*0000001000*000009000*00876600*00919900*00000000*00000000*00000000*00000000*201204*20042012*00000000*00000287208*D0000000000*0000001000*000006000*00919900*00973300*00000000*00000000*00000000*00000000*201205*20052012*00000000*00000311681*D0000000000*0000001000*000003000*00973300*01031500*00000000*00000000*00000000*00000000****000000000000*Rincian Tagihan dapat diakses di www.pln.co.id"; 
                }
            }   
        }
    } else if (substr($kdproduk, 0,6) == "PLNPRA") { 
        if($idpel1=="34018062991" || $idpel2=="537316468946"){
            sleep(40);
            $resp="BAYAR*PLNPRAH*811399328*10*20160120102008*H2H*34018062991*537316468946*".$idpel3."*200000*2500*HH13483*------*------*143010167*1***438987481*00*Token akan dikirim via SMS ke " . $idpel3."*VI105V3*34018062991*537316468946*1*00000000000000000000000000000000*0SMB213517075F64B2854F753156FA1F*00000000*NENENG MARTINI*R2*000003500*2*0000250000*0*53*53731*123*02520*0***Token akan dikirim via SMS ke " . $idpel3."*2*0000000000*2*0000000000*2*0001481500*2*0000000000*2*000018518500*2*0000013150*Informasi Hubungi Call Center 123 Atau hubungi PLN Terdekat";
        }else{
            if($idpel1 == '86000703735' || $idpel2=="521082522378"){        
                $resp = "BAYAR*PLNPRA*673999435*10*20151026022221*H2H*86000703735*521082522378*087838888891*50000*2500*" . $idoutlet . "*" . $pin . "*------*232014441*1**99502*397931322*00*SUCCESSFUL*8*86000703735*521082522378*0**0MAS2126022219000000000086514405**REANANDA HIDAYAT PERMONO*R1*1300*0*2500*0***123*****12528065590833886940**0.00**0.00**3704.00**0.00**46296*1*343*Informasi Hubungi Call Center 123 Atau hubungi PLN Terdekat.";
            } else if($idpel1 == '01117082246' || $idpel2=="511061245422"){
                $resp = "BAYAR*PLNPRA*12996128*11*20120525130054*DESKTOP*01117082246*511061245422**48400*1600*" . $idoutlet . "*" . $pin . "*D5430F79*157963*1*1*99502*18253742*00*SUCCESSFUL*0000000*01117082246*511061245422*0*988776546B2D482781B583F2A2FD5D76*14EDE52D039A4421A258AE30C77A3891*02414165*KARTO SOEWITO*R1*000002200*2*0000160000*0*51*51106*123*01584*0***32927368215773195205*2*0000000000*2*0000000000*2*0000358519*2*0000000000*2*000004481481*2*0000005640*Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
            } else if( ($idpel1 == '32900717920' || $idpel2=="535550153156") && $ref2 = '836862504' ){
                //nominal 5juta
                $resp = "BAYAR*PLNPRAY*2117923699*10*20171017160320*H2H*32900717920*535550153156*0811222516*5000000*2500*" . $idoutlet . "*" . $pin . "*------*2565200599*1**053502*836862925*00*TRANSAKSI SUKSES*JTL53L3*32900717920*535550153156*0*9907A48E4275411F8A6816218AC1134E*0BMS210Z935B109F4AB49F1FB4F003C8*00000000*PT SASTRA DAJA*B2*000033000*2*0000000000*0*53*53555*123*23760*0***25634517918116980193*2*0000600000*2*0000000000*2*0028268000*2*0000000000*2*000471132000*2*0000321100*Informasi Hubungi Call Center 123 Atau hubungi PLN Terdekat  ";
            } else if( ($idpel1 == '34007336802' || $idpel2=="537613097710") && $ref2 = '1036812431' ){
                $resp = "BAYAR*PLNPRAH*2703746689*40*20180621002141*H2H*34007336802*537613097710*6281287929134*20000*3000*" . $idoutlet . "*" . $pin . "*------*-87702662*1**99502*1036812444*63*EXT: TIDAK ADA PEMBAYARAN*506CA01*34007336802*537613097710*0*00000000000000000000000000000000*20EF4BB481F3420F8EBCC809D548E785**ERMA*R1*000001300*2*0000300000*0*53*53761*123*00936*0****************  ";
            } else if( ($idpel1 == '32100768574' || $idpel2=="213301053103") && $ref2 = '1037231567' ){
                $resp = "BAYAR*PLNPRA*2704732747*19*20180621123327*H2H*32100768574*213301053103*6281348990617*500000*2500*" . $idoutlet . "*" . $pin . "*------*203301495*1**053502*1037231571*47*EXT : TOTAL KWH MELEBIHI BATAS MAKSIMUM*JTL53L3*32100768574*213301053103*0*BD9612A39B7A4D5C8D4AD884F0A5551E*0BMS210Z57CBAAEA71780B67F9B45CBF**AGUSTINA*R1*000000450***0*21*21330*123*00324*0****************   ";
            } else {
                $resp = "BAYAR*PLNPRA*12996128*11*20120525130054*DESKTOP*01117082246*511061245422**48400*1600*" . $idoutlet . "*" . $pin . "*D5430F79*157963*1*1*99502*18253742*00*SUCCESSFUL*0000000*01117082246*511061245422*0*988776546B2D482781B583F2A2FD5D76*14EDE52D039A4421A258AE30C77A3891*02414165*KARTO SOEWITO*R1*000002200*2*0000160000*0*51*51106*123*01584*0***32927368215773195205*2*0000000000*2*0000000000*2*0000358519*2*0000000000*2*000004481481*2*0000005640*Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
            }
        }    
    } else if ($kdproduk == "WALOMBOKT") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WALOMBOKT*380860767*10*20150408102407*DESKTOP*012100676*0*0*51650*2500*".$idoutlet."*" . $pin . "*------*791339*3**400311*303146152*00*SUCCESSFUL*0000000*0*012100676***1*102350---08042015*0000000050*SYAHNUN*LENENG*201503*000000002500**PDAM Kab. Lombok Tengah*03*2015***0*51650************************************";
    } else if ($kdproduk == "WAKBMN") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKBMN*325088253*11*20150216181702*MOBILE_SMART*05014240008*05014240008**27500*2000*".$idoutlet."*" . $pin . "*------*------*1**------*------*00*SUCCESSFUL*0000000*1*05014240008***1*181701---16022015*0000008001*IBU SAILAH*Ds.Demangsari RW III*20151*000000002000**PDAM KEBUMEN*1*2015*2652*2658*0*27500************************************";
    } else if ($kdproduk == "WAPBLINGGA") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAPBLINGGA*375520225*10*20150404063115*DESKTOP*14050151*0*0*103260*2000*" . $idoutlet . "*" . $pin . "*------*1388464*3**400271*301406507*00*SUCCESSFUL*0000000*0*14050151***1*063227---04042015*0000008001*MAS'UT NUR H.*JL.MAWAR  RT.4/1*201503*000000002250**PDAM PURBALINGGA*03*2015***0*103260************************************";
    } else if ($kdproduk == "WASLTIGA") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WASLTIGA*377721740*10*20150406065044*DESKTOP*02b9289*0*0*24400*2000*" . $idoutlet . "*" . $pin . "*------*3554705*1**400321*302081978*00*SUCCESSFUL*0000000*0*02b9289***1*080647---06042015*0000008001*SUPENO*KLASEMAN 04/II*201503*000000002000**PDAM KOTA SALATIGA*03*2015***0*24400************************************";
    } else if($kdproduk == "WASUMED"){
        $resp = "BAYAR*WASUMED*2802786944*9*20180806175127*MOBILE_SMART*3104014051***73900*2500*".$idoutlet."*".$pin."*------*499070*2**400631*1079958691*00*SUCCESSFUL*1079958691*1*3104014051**RT.C|0*1*175115---06082018*SWITCHERID*SUBANA*Marga citra MARGA CINTA*null---null---null*000000002500**PDAM Kab Sumedang*07*2018*0*0*0*73900*0***********************************   ";
    } else if($kdproduk == "WABDGBAR"){
        $resp = "BAYAR*WABDGBAR*2811507582*9*20180810173314*H2H*0102000763***38500*2500*".$idoutlet."*".$pin."**100494915*1**pmgs*1083981615*00*SUCCESS*0000000*00*0102000763***1**R2*Achmad Zaini Miftah*Perum GPI Jl. Berlian No.45****PDAM Kab Bandung Barat*7*2018*694*705*0*38500*0*********************************** ";
    }else if($kdproduk == 'WASAMPANG'){
        $resp = "BAYAR*WASAMPANG*2813306875*10*20180811144337*H2H**0102040126**77968*5000*".$idoutlet."*".$pin."**-831727670*1**WASAMPANG*1084797180*00*SUKSES*0000000*00*01003923*0102040126*01/II /004/0126/A*2***CHUSNUL HOTIMAH*MUTIARA **0**PDAM TRUNOJOYO SAMPANG*6*2018*0*1*7088*35440*0*7*2018*0*1*0*35440*0****************************    ";
    } else if ($kdproduk == "WAGROBGAN") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAGROBGAN*377432718*10*20150405185154*DESKTOP*0702000552*0*0*000000027000*2000*" . $idoutlet . "*" . $pin . "*------*188829*1**1021033*301993551*00*SUCCESSFUL*0000000*0*0702000552***01*20150405043000005081*G02700005521505*LULUT SURYANTO***0000002000**PDAM KAB. GROBONGAN*3*2015*000364*000374     *0*000000027000************************************";
    } else if ($kdproduk == "WABANJAR") { // ID PELANGGAN
//        // 2 BLN
        $resp = "BAYAR*WABANJAR*373048180*10*20150402080936*DESKTOP*4027386*0*0*389380*4000*" . $idoutlet . "*" . $pin . "*------*225307*3**400231*300700216*00*SUCCESSFUL*0000000*0*4027386***2*081049---02042015*0000008001*DINA PUJIATI*Jl.Panglima Batur Gg.Qa*201502*000000002000**PDAM BANJARMASIN*02*2015***0*209310**03*2015***0*180070*****************************";
    } else if ($kdproduk == "WASRKT") { // ID PELANGGAN
//        // 3 BLN
        $resp = "BAYAR*WASRKT*372702270*15*20150401200643*DESKTOP*00046902*0*0*102400*5100*" . $idoutlet . "*" . $pin . "*------*28218*2**400251*300606207*00*SUCCESSFUL*0000000*1*00046902***3*200208---01042015*0000008001*Wahono*Semanggi        RT 03/2*201501*000000001700**PDAM SURAKARTA*03*2015***0*32000**02*2015***3200*32000**01*2015***3200*32000**********************";
    } else if ($kdproduk == "WAPURWORE") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAPURWORE*373604483*10*20150402123019*DESKTOP*01033200271*0*0*75800*1600*" . $idoutlet . "*" . $pin . "*------*14318*2**400211*300819168*00*SUCCESSFUL*0000000*0*01033200271***1*123019---02042015*0000008001*Pranoto Suwignyo*Jend A Yani*201503*000000001600**PDAM PURWOREJO*03*2015***0*75800************************************";
    } else if ($kdproduk == "WABYL") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WABYL*370983612*10*20150331154900*DESKTOP*02120025*0*0*227350*2000*" . $idoutlet . "*" . $pin . "*------*596397*1**400081*300070366*00*SUCCESSFUL*0000000*0*02120025***1*154901---31032015*0000008001*MULYATMIN*Pisang, Susilohardjo*201502*000000002000**PDAM BOYOLALI*02*2015***0*227350************************************";
    } else if ($kdproduk == "WAKABBDG") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKABBDG*372228006*10*20150401143954*DESKTOP*461195*0*0*88000*2000*" . $idoutlet . "*" . $pin . "*------*163188*1**400221*300458844*00*SUCCESSFUL*0000000*0*461195***1*143956---01042015*0000008001*TUNING RUDYATI*PESONA BALI RESIDENCE.*201504*000000002000**PDAM KAB. BANDUNG*04*2015***0*88000************************************";
    } else if ($kdproduk == "WAKNDL") { // ID PELANGGAN
//        // 2 BLN
        $resp = "BAYAR*WAKNDL*372764350*10*20150401205616*DESKTOP*0442060140*0*0*108200*3000*" . $idoutlet . "*" . $pin . "*------*1299922*3**400241*300624928*00*SUCCESSFUL*0000000*0*0442060140***2*205611---01042015*0000008001*Slamet Basuki*Babadan Rt 2/6*201502*000000001500**PDAM KENDAL*02*2015***0*74200**03*2015***0*34000*****************************";
    } else if ($kdproduk == "WAWONOGIRI") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAWONOGIRI*373653668*10*20150402130635*MOBILE_SMART*02040172*02040172**79000*2000*" . $idoutlet . "*" . $pin . "*------*98002*0**400141*300832927*00*SUCCESSFUL*0000000*1*02040172***1*130350---02042015*0000008001*DRS SUPARNO*SINGODUTAN 2/1*201503*000000002000**PDAM KAB. WONOGIRI*03*2015***0*79000************************************";
    } else if ($kdproduk == "WAIBANJAR") { // ID PELANGGAN
//        // 3 BLN
        $resp = "BAYAR*WAIBANJAR*370332504*10*20150331072101*DESKTOP*390804*0*0*435740*6000*" . $idoutlet . "*" . $pin . "*------*9343748*3**400401*299891744*00*SUCCESSFUL*0000000*0*390804***3*072103---31032015*0000008001*H.JUMBRANI*JL.KELURAHAN GG.KRUING*201412*000000002000**PDAM INTAN BANJAR*02*2015***0*65160**01*2015***0*192660**12*2014***0*177920**********************";
    } else if ($kdproduk == "WAGIRIMM") { // ID PELANGGAN
//        // 3 BLN
        $resp = "BAYAR*WAGIRIMM*371929746*10*20150401105631*DESKTOP*02-07-07330*02-07-07330*0*135650*7500*" . $idoutlet . "*" . $pin . "*------*3191455*3**400381*300380730*00*SUCCESSFUL*0000000*1*02-07-07330*02-07-07330**3*115853---01042015*0000008001*RUJAI*SEKARBELA*201501*000000002500**PDAM GIRI MENANG MATARAM*01*2015***10000*56900**02*2015***10000*25700**03*2015***0*33050**********************";
    } else if ($kdproduk == "WABULELENG") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WABULELENG*373521181*10*20150402112943*DESKTOP*02001775*0*0*37600*2000*" . $idoutlet . "*" . $pin . "*------*12674*1**400371*300795390*00*SUCCESSFUL*0000000*0*02001775***1*112835---02042015*0000437105*KETUT SUKANARA*ANTURAN GG MAWAR*201503*000000002500**PDAM KAB. BULELENG*03*2015***0*37600************************************";
    } else if ($kdproduk == "WABREBES") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WABREBES*373004003*10*20150402075243*DESKTOP*1601040330*0*0*47000*2500*" . $idoutlet . "*" . $pin . "*------*2548522*3**400341*300693502*00*SUCCESSFUL*0000000*0*1601040330***1*075238---02042015*0000008001*Hersodo*JL. Kol. Sugiono RT.03/*201503*000000002500**PDAM KAB. BREBES*03*2015***0*47000************************************";
    } else if ($kdproduk == "WAWONOSB") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAWONOSB*373041923*10*20150402080716*DESKTOP*0114120063*0*0*62020*2000*" . $idoutlet . "*" . $pin . "*------*415231*1**400331*300699347*00*SUCCESSFUL*0000000*0*0114120063***1*080718---02042015*0000008001*MOCH NASIR SUNYOTO*PUNTUK*201503*000000002000**PDAM KAB. WONOSOBO*03*2015***0*62020************************************";
    } else if ($kdproduk == "WAMADIUN") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAMADIUN*371957500*10*20150401111518*DESKTOP*0208050150*0*0*79580*2500*" . $idoutlet . "*" . $pin . "*------*1447273*3**400261*300388430*00*SUCCESSFUL*0000000*0*0208050150***1*111632---01042015*0000008001*SLAMET AS*GULUN GG II RT 49 RW 15*201503*000000002500**PDAM KOTA MADIUN*03*2015***0*79580************************************";
    } else if ($kdproduk == "WASRAGEN") { // ID PELANGGAN
//        // 2 BLN
        $resp = "BAYAR*WASRAGEN*371905769*10*20150401104009*DESKTOP*0800564*0*0*87000*3400*" . $idoutlet . "*" . $pin . "*------*136821*2**400181*300373739*00*SUCCESSFUL*0000000*0*0800564***2*093118---01042015*0000008001*WARTINAH A*KADIPIRO*201502*000000001700**PDAM KAB. SRAGEN*03*2015***0*31250**02*2015***0*55750*****************************";
    } else if ($kdproduk == "WAKABSMG") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKABSMG*372895718*10*20150402041844*DESKTOP*310050799*0*0*23140*2000*" . $idoutlet . "*" . $pin . "*------*22805*1**400201*300654369*00*SUCCESSFUL*0000000*0*310050799***1*041519---02042015*0000008001*BAGUS SETIAWAN*LOSARI SAWAHAN NO.51 RT*201504*000000002000**PDAM KAB. SEMARANG*04*2015***0*23140************************************";
        $resp = "BAYAR*WAKABSMG*2794338931*10*20180802160301*H2H*204031416***32362*5400*HH12683*------**1621046*1**400201*1075957588*00*SUCCESSFUL*1075957588*1*204031416**RMN|8*2*160141---02082018*SWITCHERID*TUMI YATIANA*Noloprayan RT 04/RW 02 Jatirej*null---null---null*000000005400**PDAM KAB. SEMARANG*06*2018*42*39*0*15042*0*07*2018*47*42*0*17320*0****************************";
    } else if ($kdproduk == "WABYMS") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WABYMS*379541126*10*20150407104235*DESKTOP*0124619*0*0*31540*2000*" . $idoutlet . "*" . $pin . "*------*4889172*1**400011*302720844*00*SUCCESSFUL*0000000*0*0124619***1*105514---07042015*0000008001*NANI WIBOWO*JL. JATIWINANGUN GG.SEM*MAR15*000000002000**PDAM BANYUMAS*3*2015***0*31540************************************";
    } else if ($kdproduk == "WAHLSUNGT") { // ID PELANGGAN
//        // 2 BLN
        $resp = "BAYAR*WAHLSUNGT*370640986*10*20150331112745*DESKTOP*0300648*0*0*251200*4000*" . $idoutlet . "*" . $pin . "*------*4405*0**400361*299981664*00*SUCCESSFUL*0000000*0*0300648***2*112859---31032015*0000008001*LINA HERIANI*JL.DESA KERAMAT RT.3/2*201501*000000002000**PDAM Hulu Sungai Tengah*01*2015***0*106800**02*2015***0*144400*****************************";
    } else if ($kdproduk == "WAKARANGA") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKARANGA*373054738*10*20150402081140*DESKTOP*0702011372*0*0*28000*2000*" . $idoutlet . "*" . $pin . "*------*2440332*2**400121*300701041*00*SUCCESSFUL*0000000*0*0702011372***1*081136---02042015*0000008001*SENEN*BUNGKUS 10/03 JATIROYO*201503*000000002000**PDAM Karanganyar*03*2015***0*28000************************************";
    } else if ($kdproduk == "WAKPKLNGAN") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKPKLNGAN*371624754*10*20150401070700*DESKTOP*0104010422*0*0*70550*2000*" . $idoutlet . "*" . $pin . "*------*609924*3**400101*300280228*00*SUCCESSFUL*0000000*0*0104010422***1*070654---01042015*0000008001*Moh. Abdullah*Perum Puri Puri raya Bl*201503*000000002000**PDAM KAB. PEKALONGAN*03*2015***0*70550************************************";
    } else if ($kdproduk == "WAMAKASAR") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAMAKASAR*359960925*10*20150321075233*DESKTOP*199200987*0*0*000000043320*2500*" . $idoutlet . "*" . $pin . "*------*169935*1**1021014*296702948*00*SUCCESSFUL*0000000*0*199200987***01*20150321043000000699*GEH001992009872*M. NATSIR***0000002500**PDAM KOTA MAKASAR*2*2015*00003512*00003521 *0*000000043320************************************";
    } else if ($kdproduk == "WAKUBURAYA") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAKUBURAYA*357995309*10*20150319181319*DESKTOP*09400*0*0*000000053500*2000*" . $idoutlet . "*" . $pin . "*------*1422454*3**1021015*295847639*00*SUCCESSFUL*0000000*0*09400***01*20150319043000019664*G05350094001519*CONG TJIN MOI***0000002000**PDAM KOTA KUBURAYA*2*2015*001215*001235     *0*000000053500************************************";
    } else if ($kdproduk == "WAPONTI") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAPONTI*357668833*10*20150319143344*DESKTOP*3060346*0*0*000000026100*1600*" . $idoutlet . "*" . $pin . "*------*857465*0**1021010*295720757*00*SUCCESSFUL*0000000*0*3060346***01*20150319043000015081*GEH000030603462*ARDIAN***0000001600**PDAM KOTA PONTIANAK (KALBAR)*2*2015*00000105*00000111 *0*000000026100************************************";
    } else if ($kdproduk == "WAMANADO") { // ID PELANGGAN
//        // 1 BLN
        $resp = "BAYAR*WAMANADO*357242267*10*20150319094410*DESKTOP*38671*0*0*000000074730*1600*" . $idoutlet . "*" . $pin . "*------*497304*3**1021009*295561512*00*SUCCESSFUL*0000000*0*38671***01*20150319043000006148*G2313H3G849RF38*FATMA SAMBANG***0000001600**PDAM KOTA MANADO*2*2015*00000281*00000288 *0*000000074730************************************";
    } else if ($kdproduk == "WASITU") { // ID PELANGGAN
//        // 1 BLN
        
        if($idpel1 == "01/IV /004/0612/B1"){
            $resp = "BAYAR*WASITU*357732560*10*20150319151536*H2H*01/IV /004/0612/B1***90070*2000*" . $idoutlet . "*" . $pin . "**9781908*1**WASITU*295742721*00*SUKSES*0000000*00*01/IV /004/0612/B1*01/IV /004/0612/B1*7101*1***MISTAM SOEKARDI*ARGOPURO No.GG16 *2:2015:0:0:0*0**PDAM SITUBONDO*2*2015*0*39*0*90070*0***********************************";
        } else if($idpel1 == "01/I /007/1659/B1"){
            $resp = "BAYAR*WASITU*1692774052*14*20170425163719*DESKTOP*01/I /007/1659/B1*01/I /007/1659/B1*14001*107500*6000*".$idoutlet."*".$pin."*------*140359*1**WASITU*708529237*00*SUKSES*0000000*00*01/I /007/1659/B1*01/I /007/1659/B1*14001*3***SAMSUL HADI*JL. CEMPAKA PERUM ISMU H- 18 *#1:2017:5000:25000:0#2:2017:5000:0:0#3:2017:5000:0:0*25000**PDAM SITUBONDO*1*2017*0*5*0*22500*0*2*2017*0*10*0*22500*0*3*2017*0*10*40000*22500*0*********************";
        }
    } else if ($kdproduk == "WAREMBANG") { // ID PELANGGAN    
        if ($idpel1 == "LA-03-00084") {// bulan
            $resp = "BAYAR*WAREMBANG*347270076*10*20150310145130*DESKTOP*LA-03-00084***46400*2000*" . $idoutlet . "*" . $pin . "*------*0*2**400301*291942562*00*SUCCESSFUL*0000000*0*LA-03-00084***1*145133---10032015*0000008001*MUSHOLLA AL MUBAROQ**201502*000000002000**PDAM Kab. Rembang*02*2015***0*46400************************************";
        } else {
            $resp = "BAYAR*WAREMBANG*347346523*10*20150310155253*DESKTOP*LA-03-00012***530000*4000*" . $idoutlet . "*" . $pin . "*------*0*2**400301*291968179*00*SUCCESSFUL*0000000*0*LA-03-00012***2*155255---10032015*0000008001*R A M I S I H**201502,201501*000000004000**PDAM Kab. Rembang*02*2015***0*269400**01*2015***0*260600*****************************";
        }
    } else if ($kdproduk == "WASLMN") { // ID PELANGGAN
        $resp = "BAYAR*WASLMN*327274552*10*20150218183916*H2H*1400669***60000*2500*" . $idoutlet . "*" . $pin . "**-228275716*0**400071*284869801*00*SUCCESSFUL*0000000*0*1400669***1*183903---18022015*0000008001*NADI KUSNADI*JL.ASTER 333*201501*000000001700**PDAM SLEMAN*01*2015*3724*3744*0*60000************************************";
    } else if ($kdproduk == "WASMG") { // ID PELANGGAN   
        if($idoutlet =='HH10632' || $idoutlet =='BS0004' || $idoutlet =='FA9919'){
            $bill_q = "1";
        } else {
            $bill_q = '01';
        }
        if($idpel1=="07460079"){
            sleep(40);
            $resp="BAYAR*WASMG*811403601*10*20160120102151*H2H*07460079***45700*2000*" . $idoutlet . "*------**4239956*1***438989123*00*SEDANG DIPROSES*GS10AG3*Rumah Tangga 3*07460079***1*F576BB3476214FC0B5D0000000000000*000438988655*Chr Sri Setyowati*Parang Barong 6/04**0000002000**PDAM KOTA SEMARANG*12*2015*2643*2658*0*36200*0|0|2000|5000|2500|0***********************************";
        }else if($idpel1 == '05430379'){
            //2bulan WASMG
            $resp = "BAYAR*WASMG*868053206*10*20160223103806*DESKTOP*05430379*0*0*188396*4000*" . $idoutlet . "*" . $pin . "*------*309646*0**87004*455527591*00*SUCCESS*GS10AG3*Rumah Tangga 4*05430379***2*29402BF700AE4048B830000000000000*000455524845*FX Samiyo (Rt.9/3)*Cikurai Brt Dlm 1 Kaligse**0000004000**PDAM KOTA SEMARANG*12*2015*612*631,19*6936*61860*0|0|5000|5000|2500|0*01*2016*631*656,25*0*94600*0|0|5000|5000|2500|0****************************";
        } else if($idpel1 == '07020747'){
            $resp = "BAYAR*WASMG*1490099889*9*20170123101239*H2H*07020747***94005*4000*" . $idoutlet . "*" . $pin . "**1449440050*1**87004*652123389*00*SUCCESS*GS10AG3*Rumah Tangga 5*07020747***2*6E81BC0CE9054051B470000000000000*000652106053*Harimawan*Bimasakti 3/8-10**0000004000**PDAM KOTA SEMARANG*11*2016*84*84*3905*31550*0|0|6000|5000|2500|0*12*2016*84*84*0*31550*0|0|6000|5000|2500|0****************************   ";
        } else if($idpel1 == '06620111'){
            $resp = "BAYAR*WASMG*333207545*10*20150224151826*DESKTOP*06620111*0*0*000000036970*2000*" . $idoutlet . "*" . $pin . "*------*1138929*0**1061025*287052750*00*SUCCESSFUL*0000000*0*06620111***".$bill_q."*20150224043000012561*9819F39536B34ED79F60000000000000*Bunadi***0000002000**PDAM SEMARANG*1*15*0000000072 * 0000000084*0*000000036970************************************";
        } else {
            $resp = "BAYAR*WASMG*333207545*10*20150224151826*DESKTOP*06620111*0*0*000000036970*2000*" . $idoutlet . "*" . $pin . "*------*1138929*0**1061025*287052750*00*SUCCESSFUL*0000000*0*06620111***".$bill_q."*20150224043000012561*9819F39536B34ED79F60000000000000*Bunadi***0000002000**PDAM SEMARANG*1*15*0000000072 * 0000000084*0*000000036970************************************";
        }
    } else if ($kdproduk == "WAKABMLG") { // ID PELANGGAN
        $resp = "BAYAR*WAKABMLG*406632*10*20150401153632*H2H*8101120001982***000000025000*1800*" . $idoutlet . "*" . $pin . "**318607707*0**1061032*238016367*00*SUCCESSFUL*0000000*0*8101120001982***01*20150401043000011022*1A5FD4B1F60D4A6D9FC0000000000000*YUGUS***0000002100**PDAM KAB. MALANG*4*2015*0000001816 * 0000001826*0*000000025000************************************";
    } else if ($kdproduk == "WABALIKPPN") { // ID PELANGGAN
//
// 1 BLN
        $resp = "BAYAR*WABALIKPPN*312450772*10*20150203062340*DESKTOP*01030010346*0*0*000000094375*1600*" . $idoutlet . "*" . $pin . "*------*933039*0**1021008*279676320*00*SUCCESSFUL*0000000*0*01030010346***01*20150203043000000207*G2313H3G849RF8F*HJ.RUKAYAH**Cust Detail Info pindah ke bill_info70*0000001600**PDAM KOTA BALIKPPN*1*2015*00000129*00000141 *0*000000094375************************************";
    } else if($kdproduk == "WABAL"){
        $resp = "BAYAR*WABAL*2887988435*9*20180917102101*DESKTOP*050316*050316*050316*76400*2500*" . $idoutlet . "*" . $pin . "*------*1827593*1**2033*1119468654*00*EXT: APPROVE*0000000*RIDUAN*050316*050316*050316*1**RIDUAN*RIDUAN**024189HA2100001***PDAM BALANGAN*08*2018***0*76400*0***********************************   ";
    } else if ($kdproduk == "WABOGOR") { // ID PELANGGAN
// 1 BLN
        $resp = "BAYAR*WABOGOR*303308785*10*20150123095039*DESKTOP*07411152*0*0*000000078540*2500*" . $idoutlet . "*" . $pin . "*------*2173894*1**1021030*276888151*00*SUCCESSFUL*0000000*0*07411152***01*20150123043000003809*BGR13H3G849RF11*AAS RAMAESIH**Cust Detail Info pindah ke bill_info70*0000002500**PDAM KAB. BOGOR*12*2014*00000711*00000727 *0*000000078540************************************";
    } else if ($kdproduk == "WACLCP") { // ID PELANGGAN
// 1 BLN
        if ($idpel1 == "0105041625") {
            $resp = "BAYAR*WACLCP*340812584*10*20150304160904*H2H*0105041625***000000269600*6000*" . $idoutlet . "*" . $pin . "**11150000*1**1021012*289596702*00*SUCCESSFUL*0000000*0*0105041625***03*20150304043000017000*CLP13H3G849RF41*ETI WIDIASTUTI***0000006000**PDAM CILACAP*12*2014*00002694*0*0*0**1*2015*0*0*0*0**2*2015*0*00002762 *0*000000269600**********************";
        } else {
            $resp = "BAYAR*WACLCP*302475298*10*20150122104820*DESKTOP*0309211394*0*0*000000103500*2000*" . $idoutlet . "*" . $pin . "*------*75210*1**1021012*276633408*00*SUCCESSFUL*0000000*0*0309211394***01*20150122043000006019*CLP13H3G849RF11*NY SUMINEM**Cust Detail Info pindah ke bill_info70*0000002000**PDAM CILACAP*12*2014*00000320*00000347 *0*000000103500************************************";
        }
    } else if ($kdproduk == "WATAPIN") { // NO SAMBUNGAN
// 1 BLN
        if($idpel2 == '080707'){
            $resp = "BAYAR*WATAPIN*252333382*10*20141202131922*DESKTOP*0*080707*707*44500*2000*" . $idoutlet . "*" . $pin . "*------*295935*1**WATAPIN*259564465*00**0000000*00**080707*707*1***GURU IKAS*BAKARANGAN *Cust Detail Info pindah ke bill_info70*0**PDAM TAPIN*11*2014*0*10*0*44500*0***********************************";
        } else if($idpel2 == '011189'){
            $resp = "BAYAR*WATAPIN*1493778653*10*20170125084423*DESKTOP*0*011189*1189*710300*4000*" . $idoutlet . "*" . $pin . "*------*802812*3**WATAPIN*653050484*00*SUKSES*0000000*00*0*011189*1189*2***RUSMADI*A.YANI BITAHAN **0**PDAM TAPIN*11*2016*0*87*5000*368600*0*12*2016*0*79*2500*334200*0**************************** ";
        }
        
    } else if ($kdproduk == "WALMPNG") { // ID PELANGGAN (NO SAMBUNGAN)
// 2 BLN
        $resp = "BAYAR*WALMPNG*254364486*9*20141204105004*DESKTOP*010501*010501*010501*111440*5600*" . $idoutlet . "*" . $pin . "*------*997079*0**2011*260186651*00*EXT: APPROVE*0000000*00*010501*010501*010501*2***BUSRON TOHA*****PDAM LAMPUNG*11*2014***0*62640*0*10*2014***5000*43800*0****************************";
    } else if ($kdproduk == "WAJAMBI") { // ID PELANGGAN (NO SAMBUNGAN)
// 1 BLN
        $resp = "BAYAR*WAJAMBI*254418697*9*20141204113313*DESKTOP*03583*03583*03583*263750*2800*" . $idoutlet . "*" . $pin . "*------*3410054*1**2010*260203484*00*EXT: APPROVE*0000000*00*03583*03583*03583*1***ADMINISTRASI PELABUHAN*****PDAM JAMBI*12*2014***00000000*263750*0***********************************";
    } else if ($kdproduk == "WASDA") { // ID PELANGGAN && NO SAMBUNGAN
        if($idpel1=="02004159" || $idpel2=="02/I  /007/0147/2D"){
            sleep(40);
            $resp="BAYAR*WASDA*812314848*10*20160120170058*H2H*02004159***154100*3600*" . $idoutlet . "*------**-492264260*1**WASDA*439339830*00*SEDANG DIPROSES*0000000*00*02004159*02/I  /007/0147/2D*BA070147*2***SUKARNI*PERUM WISMA SARINADI III I-17**0**PDAM SIDOARJO*11*2015*0*18*7500*87300*0*12*2015*0*13*0*59300*0****************************";
        }else{ 
            if ($idpel1 == "01002679" || $idpel2 == "01/II /013/0083/2D") {
    // 3 BLN
                $resp = "BAYAR*WASDA*253013041*10*20141203081218*H2H*01002679***138500*5400*" . $idoutlet . "*" . $pin . "**-198812993*1**WASDA*259775515*00**0000000*00*01002679*01/II /013/0083/2D*AB130083*3***PERM. BUMI CITRA FAJ*SEKAWAN SEJUK C.10A*Cust Detail Info pindah ke bill_info70*0**PDAM SIDOARJO*9*2014*0*0*7500*40500*0*10*2014*0*0*7500*40500*0*11*2014*0*0*0*42500*0*********************";
            } else {
    // 6 BLN
                $resp = "BAYAR*WASDA*253013041*10*20141203081218*H2H*01002676***294500*10800*" . $idoutlet . "*" . $pin . "**-198812993*1**WASDA*259775515*00**0000000*00*01002676*01/II /013/0083/6D*AB130086*6***PERM. BUMI CITRA FAJ*SEKAWAN SEJUK C.16A*Cust Detail Info pindah ke bill_info70*0**PDAM SIDOARJO*5*2014*0*0*7500*40500*0*6*2014*0*0*7500*40500*0*7*2014*0*0*7500*42500*0*8*2014*0*0*7500*43500*0*9*2014*0*0*7500*44500*0*10*2014*0*0*0*45500*0";
            }
        }    
    } else if ($kdproduk == "WABONDO") { // ID PELANGGAN && NO SAMBUNGAN
// 3 BLN
        $resp = "BAYAR*WABONDO*250811076*10*20141130205945*DESKTOP*09000879*09/01/001/00879/RB*0*94130*4500*" . $idoutlet . "*" . $pin . "*------*5321738*1**FY834n7Vs4mdASP4H34n*259098168*00*EXT: PAYMENT SUKSES.*0000000*00*09000879*09/01/001/00879/RB**3***DWI YULIANA*PONCOGATI RT 11/5*Cust Detail Info pindah ke bill_info70*0**PDAM BONDOWOSO*8*2014*0*5*15000*9400*0|16150*9*2014*0*4*5000*7520*0|16150*10*2014*0*2*5000*3760*0|16150*********************";
    } else if ($kdproduk == "WAPLYJ") { // ID PELANGGAN (NO SAMBUNGAN)
        if($idpel1=="000001603" || $idpel2=="000001603"){
             sleep(40);
             $resp="BAYAR*WAPLYJ*812335004*9*20160120170918*H2H*000001603*000001603*000001603*609395*2500*" . $idoutlet . "*------**51595632*1**2001*439347931*00*SEDANG DIPROSES*0000000*00*000001603*000001603*000001603*1***MACHROEP*****PALYJA*12*2015***0*609395*0***********************************";
         }else{  
             $resp = "BAYAR*WAPLYJ*250176742*9*20141130064940*DESKTOP*000754677*000754677*000754677*22366*2500*" . $idoutlet . "*" . $pin . "*------*76228*0**2001*258884747*00*EXT: APPROVE*0000000*00*000754677*000754677*000754677*1***SITI RAODAH*****PALYJA*11*2014***0*22366*0***********************************";
         }
    } else if ($kdproduk == "WAAETRA") { // ID PELANGGAN (NO SAMBUNGAN)
        if($idpel1=="40064599" || $idpel2=="40064599" ){
            sleep(40);
            $resp="BAYAR*WAAETRA*812347618*9*20160120171443*H2H*40064599*40064599*40064599*84584*2500*" . $idoutlet . "*------**229452705*1**2002*439352987*00*SEDANG DIPROSES*0000000*00*40064599*40064599*40064599*1***YUDHO IRFANTO*****AETRA*12*2015***0*84584*0***********************************";
        } else if( ($idpel1=="60158686" || $idpel2=="60158686") && $ref2 == '836835042'){
            $resp = "BAYAR*WAAETRA*2117836499*9*20171017153305*H2H*60158686*60158686*60158686*3604079*2500*" . $idoutlet . "*" . $pin . "**244112035*1**2002*836835477*00*EXT: APPROVE*0000000*00*60158686*60158686*60158686*1***JERI S*****AETRA*09*2017***0*3604079*0***********************************  ";
        } else if( ($idpel1=="60123119" || $idpel2=="60123119") && $ref2 == '837230008' ){
            $resp = "BAYAR*WAAETRA*2118960396*9*20171018002331*H2H*60123119*60123119*60123119*13107279*2500*" . $idoutlet . "*" . $pin . "**2713910758*1**2002*837230129*00*EXT: APPROVE*0000000*00*60123119*60123119*60123119*1***HARYATI*****AETRA*09*2017***0*13107279*0***********************************      ";
        } else{ 
            $resp = "BAYAR*WAAETRA*252537037*9*20141202162830*DESKTOP*20040428*20040428*20040428*18045*2500*" . $idoutlet . "*" . $pin . "*------*6550271*1**2002*259623268*00*EXT: APPROVE*0000000*00*20040428*20040428*20040428*1***RIFAI*****AETRA*11*2014***0*18045*0***********************************";
        }
    } else if($kdproduk == "WAMEDAN"){
        $resp = "BAYAR*WAMEDAN*1644378718*10*20170404123901*H2H*0117080017***88000*7500*". $idoutlet ."*". $pin ."**145637240*1**1002*694616478*00*SUCCESSFUL*0000000*00*0117080017***03*#0#0#0*N.3#138081*SYAIFUL HALIM*PEMUDA BARU III 12*#10800.00#18600.00#18600.00***PDAM KOTA MEDAN (SUMUT)*02*2017*46000*47000*00020000*10800*0*03*2017*47000*49000*00020000*18600*0*04*2017*49000*51000*00000000*18600*0*********************   ";
    } else if ($kdproduk == "WAMJK") {
        $resp = "BAYAR*WAMJK*247145969*10*20141126170840*DESKTOP*0*0909040028*09.07.06.0336*122415*4000*" . $idoutlet . "*" . $pin . "*------*100695*1**WAMJK*257961468*00*SUKSES*0000000*00*0*0909040028*09.07.06.0336*2***SUKADI*SUKOANYAR-GONDANG *Cust Detail Info pindah ke bill_info70*0**PDAM KAB. MOJOKERTO (JATIM)*9*2014*0*19*11900*46000*0*10*2014*0*23*8415*56100*0****************************";
    } else if($kdproduk == 'WAKOPASU'){
        $resp = "BAYAR*WAKOPASU*861464269*9*20160219132130*H2H*c1-03943*10**68310*2500*".$idoutlet."*".$pin."**1015645936*1**WAKOPASU*453311834*00*SUKSES*0000000*00*c1-03943*c1-03943*c1-03943*1***DUMAH*Jl. Maluku No.9  RT.3/VIII**0**PDAM KOTA PASURUAN*1*2016*1438*1458|24600|26559|10|10*0*66310*2000|0|2460|2951|5700|5000|***************0********************";
    } else if($kdproduk == "WAPASU"){
        $resp = "BAYAR*WAPASU*2886750412*9*20180916171720*H2H*03050228*228**74540*2500*".$idoutlet."*".$pin."**2396717100*1**PASURUAN*1118856295*00*SUCCESS*0000000*00*03050228*228**1*03**MISLAN*BULU RT/W. 02/01 BULUSARI****PDAM KAB. PASURUAN*08*2018*5596*5618*0*74540*0****************************193******* ";
    } else if($kdproduk == 'WACIAMIS'){
        $resp = "BAYAR*WACIAMIS*2780129865*10*20180726104636*H2H*04030020147**6285324800289*81400*2500*".$idoutlet."*".$pin."**-53342216*1**400621*1069446941*00*SUCCESSFUL*1069446941*1*04030020147**R2 / Rumah Tang|17*1*104606---26072018*SWITCHERID*AMY MARYA, SE*PERUM B REGENCY 7 C.7*null---null---null*000000002500**PDAM CIAMIS*06*2018*574*591*0*81400*0***********************************    ";
    } else if ($kdproduk == "TVAORA") {
        $resp = "BAYAR*TVAORA*71914137*12*20121211145310*DESKTOP*7000231294***59000*2500*" . $idoutlet . "*" . $pin . "*------*1334765*1*2*TVAORA*49344261*00*SUCCESS***7000231294*1**1524753*NOER  HASANAH***59000***0**1524753*AORA TV**0*465646*2012-12-02*2012-12-17*BC01*59000*";
    } else if ($kdproduk == "TVTOPAS") {
        $resp = "BAYAR*TVTOPAS*245627246*10*20141125072552*DESKTOP*1503000389***76100*0*" . $idoutlet . "*" . $pin . "*------*473317*1**060901*257506739*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*1503000389*1*14110135302O**Sunarto***72600*0**3500**14110135302*TOPAS TV****01-DEC-14/31-DEC-14**EjcXrEZifq0=**";
    } else if ($kdproduk == "TVINDVS") {
        $resp = "BAYAR*TVINDVS*71529244*12*20121210144117*DESKTOP*401000939875***154000*2000*" . $idoutlet . "*" . $pin . "*------*968477*0**TVINDVS*49098671*00*EXT: APPROVE***401000939875*1***SUNARKO .                     ***000000154000*******500***06112012-05122012**401000939875                                               0401000939875SUNARKO .                     06112012-05122012000000154000**";
    } else if ($kdproduk == "HPXL") {
        $resp = "BAYAR*HPXL*70911713*11*20121208130710*DESKTOP*0818158020***000000054303*2500*" . $idoutlet . "*" . $pin . "*------*584331*1**HPXL*48720213*00*APPROVE*0000000*00*0818158020*1*0445554**RACHMAT SUHAPPY****XL*201   *201   *000000000*0000000005430300*0000000000*      *      *         *                *          *      *      *         *                *";
    } else if ($kdproduk == "HPTSEL") {
        $resp = "BAYAR*HPTSEL*242453920*10*20141121120636*DESKTOP*0811408689***000000048323*2500*" . $idoutlet . "*" . $pin . "*------*2879885*0**HPTSEL*256519489*00*APPROVE*0000000*00*0811408689*1***Bapak V.  TIKNO SARWOKO****TELKOMSEL*201411*201411*000000000*0000000004832300*0000000000*      *      *         *                *          *      *      *         *                *";
    } else if ($kdproduk == "HPESIA") {
        $resp = "BAYAR*HPESIA*71497318*11*20121210132040*DESKTOP*02198227230***000000066344*2500*" . $idoutlet . "*" . $pin . "*------*69128*1**HPESIA*49078987*00*APPROVE*0000000*00*02198227230*1***ACHMAD SURYADI****ESIA*201099*201099*000000000*0000000006634400*0000000000*      *      *         *                *          *      *      *         *                *";
    } else if ($kdproduk == "FNWOM") {
        if($idpel1=="201010034165"){
            sleep(40);
            $resp = "BAYAR*FNWOM*72854524*10*20121214121952*DESKTOP*201010034165***000001005500*0*" . $idoutlet . "*" . $pin . "*------*511172*0**4105*49921760*00*SEDANG DIPROSES*0000000*26*201010034165*01*104206300029*514846*ABDUL KODIR                    ***000000000000****91A5148070D946E98FFF6F2D87F425EA*WOM Finance              *006A                          *                                          *                         *B3899TNX      *016*008*13 Dec 12**000008016000**000001005500*000000000000****";
        } else if($idpel1=="808600015451" && $ref2 == '835539756'){
            $resp = "BAYAR*FNWOM*2114279792*10*20171016091917*DESKTOP*808600015451***9342000*0*" . $idoutlet . "*" . $poin . "*------*5998805*1**4105*835546583*00*APPROVE*0000000*26*808600015451*01*104202200640*073401*NURDIN ***000009342000****1148DCF1DCC24045B5A955E9C3BBC48D*WOM Finance *156 * * *E1472YG *012*005*19 Oct 17**000065391000**000009338000*000000000000*000000004000*000000000000**   ";
        } else if($idpel1=="802300064457" && $ref2 == '837070249'){
            $resp = "BAYAR*FNWOM*2118517321*10*20171017192017*DESKTOP*802300064457***12075000*0*" . $idoutlet . "*" . $pin . "*------*3194774*0**4105*837073311*00*APPROVE*0000000*26*802300064457*01*104202200640*196839*PAINO ***000012075000****719B75B4B45C4CB688AB58597DEB5161*WOM Finance *016A * * *B1391KMU *012*004*19 Oct 17**000016593000**000012071000*000000000000*000000004000*000000000000**   ";
        } else{
            $resp = "BAYAR*FNWOM*72854524*10*20121214121952*DESKTOP*201010034185***000001005500*0*" . $idoutlet . "*" . $pin . "*------*511172*0**4105*49921760*00*APPROVE*0000000*26*201010034185*01*104206300029*514846*ABDUL AZIZ                    ***000000000000****91A5148070D946E98FFF6F2D87F425EA*WOM Finance              *006A                          *                                          *                         *B3899TNC      *016*008*13 Dec 12**000008016000**000001005500*000000000000****";
        }
    } else if ($kdproduk == "FNMAF") { // MAF
        if($idpel1=="1201501064"){
            sleep(40);
            $resp="BAYAR*FNMAF*812308958*8*20160120165837*DESKTOP*1201501064***1059000*0*" . $idoutlet . "*------*------*1118894*2**MAF*439337539*00*SEDANG DIPROSES*0000000*122*1201501064*01*9237870692*BMS1201501064590801*DINIA ZAINAL***1059000*****MEGA AUTO FINANCE******2*20160201****1059000**0***";
        } else if($idpel1=="7981600075" && $ref2 == "837086806"){
            $resp = "BAYAR*FNMAF*2118569205*8*20171017193610*DESKTOP*7981600075***4147000*0*" . $idoutlet . "*" . $pin . "*------*874248*2**MAF*837091397*00*SUKSES*0000000*122*7981600075*01*9339167852*BMS7981600075554101*SULISTIYO***4147000*****MEGA AUTO FINANCE******13*20171018****4147000**0***    ";
        } else if($idpel1 == '1561600404' && $ref2 == '837605395'){
            $resp = "BAYAR*FNMAF*2120051300*8*20171018120848*DESKTOP*1561600404***11977000*0*" . $idoutlet . "*" . $pin . "*------*2705113*1**MAF*837606967*00*SUKSES*0000000*122*1561600404*01*6921160412*BMS1561600404282101*ANDI RABIA***11977000*****MEGA AUTO FINANCE******8*20170824****11977000**0***   ";
        }else{
            $resp = "BAYAR*FNMAF*73762439*10*20121217114919*DESKTOP*2641100868***000001001875*0*" . $idoutlet . "*" . $pin . "*------*1007826*1**4104*50459927*00*APPROVE*0000000*26*2641100868*01*104206300029*536153*IKHSANUDDIN                   ***000000000000****3E93C328038A4BD8948C234A04A5E337*PT MEGA AUTO FINANCE     *POS SUNGAI DANAU              *YAMAHA VEGA ZR 115 DB                     *MH35D9204BJ456707        *DA3933ZY      *017*013*26 Dec 12**000004375000**000000875000*000000126875****";
        }
    } else if ($kdproduk == "FNMEGA") {//MCF
        if($idpel1 == "7161700947" && $ref2 == "835896478"){
            $resp = "BAYAR*FNMEGA*2115274483*8*20171016152726*H2H*7161700947***2112000*0*" . $idoutlet . "*" . $pin . "**24756133*1**MCF*835896936*00*SUKSES*0000000*122*7161700947*01*8910001154*BMS7161700947465501*SUINDAH***2112000*****MEGA CENTRAL FINANCE******5*20171019****2112000**0***    ";
        } else if($idpel1 == "5411700393" && $ref2 == "837716203"){
            $resp = "BAYAR*FNMEGA*2120404296*8*20171018141512*H2H*5411700393***13075000*0*" . $idoutlet . "*" . $pin . "**1501387051*1**MCF*837716701*00*SUKSES*0000000*122*5411700393*01*1041524552*BMS5411700393344501*SURYA BUDIMAN***13075000*****MEGA CENTRAL FINANCE******3*20170914****13075000**0***   ";
        }else {
            $resp = "BAYAR*FNMEGA*72837114*10*20121214111554*DESKTOP*5701200409***000000661000*0*" . $idoutlet . "*" . $pin . "*------*3759068*1**4103*49911512*00*APPROVE*0000000*26*5701200409*01*104206300029*514367*ABDUL SAMID HARAHAP           ***000000000000****B9CDC33568B9454EB32A4E503208F42D*PT MEGA CENTRAL FINANCE  *Bekasi MCF                    *HONDA VARIO TECHNO 125 PGM FI NON CBS     *MH1JFB111CK196426        *B6213UWX      *030*005*14 Dec 12**000017182695**000000657695*000000003305****";
        }
    } else if ($kdproduk == "FNBAF") {
        if($idpel1=="122010051955" && $ref2 == '439365371'){
            sleep(5);
            $resp="BAYAR*FNBAF*812378668*10*20160120172709*DESKTOP*122010051955***000000622900*0*" . $idoutlet . "*------*------*395746*2**86003*439365842*00*SEDANG DIPROSES*BMS0001*102*122010051955*                    *000000000000009DAD6CAED3E61D917A**AZHARI*****0000000*006261000000*000000000000009DAD6CAED3E61D917A*Bussan Auto Finance*122*YMH.CIO.JCWFI**B 3507 KQH*029*020*20160119*0*000000000000*001*000000620900*000000005200*000000000000*000000002000*000000622900*000001865000";
        }else{
            $resp = "BAYAR*FNBAF*247682613*10*20141127103126*DESKTOP*636010013925***000000860000*0*" . $idoutlet . "*" . $pin . "*------*2045059*1**86003*258122378*00*SUCCESSFUL*BMS0001*94*636010013925*                    *000000000000081812111EFE8F5E70DA**WAL ASRI FADLI*****0000000*008686000000*000000000000081812111EFE8F5E70DA*Bussan Auto Finance*636*YMH.JUP.JUPMXCW**BG 2498 GA*023*004*20141125*0*000000000000*001*000000858000*000000010600*000000000000*000000002000*000000860000*000002576000";
        }    
    } else if($kdproduk == "FNCLMB"){
        $resp = "BAYAR*FNCLMB*1008497119*10*20160510134657*H2H*1001017858001***209000*0*".$id_outlet."*".$pin."**687251359*1**020002*497490481*00*EXT: APPROVE**107*1001017858001*1*1001017858001HADI SUTRONO 10000002090009 dari 180118/04/20160000002090000000002915*55000000082555C*HADI SUTRONO ***000000209000*****COLUMBIA*****01*9 dari 18*18/04/2016******0**000000209000*";
    } else if ($kdproduk == "ASRTOKIOS") {
        $resp = "BAYAR*ASRTOKIOS*364476149*12*20150325145852*DESKTOP*2140001AA***60000*0*".$id_outlet."*" . $pin . "*------*390992*1**ASRTOKIOS*298146317*00*TRANSACTION IS SUCCESSFUL*0000000*00*2140001AA*1***Daniel Haryadi*ASURANSI TM ABADI PLAN A*60000*******ASURANSI TOKIO MARINE LIFE*********";
    } else if($kdproduk == "ASRTOKIO"){
        $resp = "BAYAR*ASRTOKIO*364523609*12*20150325154109*DESKTOP*2140001AA***240000*0*".$id_outlet."*" . $pin . "*------*90992*1**ASRTOKIO*298158963*00*TRANSACTION IS SUCCESSFUL*0000000*00*2140001AA*4***Daniel Haryadi*ASURANSI TM ABADI PLAN A*240000*******ASURANSI TOKIO MARINE LIFE*********";
    } else if ($kdproduk == "ASRJWS" || $kdproduk == "ASRJWSI") {
        if ($idpel1 == "14001969964") {
            $resp = "BAYAR*ASRJWS*250552670*12*20141130160735*H2H*14001969964*CH001969964**1368615*0*" . $idoutlet . "*" . $pin . "**-448115214*1**ASRJWS*259002302*00*EXT:Sukses*01*PREMI : NOV-2014*PRM.112014*1***PUJI WARYANTI**1368615*******JIWASRAYA*********";
        } else {
            
            if($idpel1 == "03001953710"){
                $resp = "BAYAR*ASRJWS*1207848252*11*20160830143515*DESKTOP*03001953710*AE001953710**110*0*" . $idoutlet . "*" . $pin . "*------*3696055*1**ASRJWS*562070022*00*EXT: Sukses*01*PULIH-2016-08-30*PLH-2016-08-30*1***SUMITRA**110*******JIWASRAYA*********";
            } else if($idpel1 == '68001889322'){
                 $resp = "BAYAR*ASRJWS*251014583*12*20141201085321*H2H*68001889322*MC001889322**200000*0*" . $idoutlet . "*" . $pin . "**-524833319*1**ASRJWS*259164764*00*EXT:Sukses*01*PREMI : NOV-2014*PRM.112014*1*02*TOTAL : NOV-2014 s/d DEC-2014*SUHAENAH*400000*400000*******JIWASRAYA*********";
            } else if($idpel1 == '62002327375' && $ref2 == '836047219'){
                $resp = "BAYAR*ASRJWS*2115978411*11*20171016194104*DESKTOP*62002327375*LB002327375**5075000*0*" . $idoutlet . "*" . $pin . "*------*1896144*4**ASRJWS*836156742*00*EXT: Sukses*01*PREMI : OCT-2017*PRM.102017*1***SURYA BALKIS**5075000*******JIWASRAYA*********  ";
            } else if($idpel1 == '27002326950' && $ref2 == '836828076'){
                $resp = "BAYAR*ASRJWS*2117813206*11*20171017152447*H2H*27002326950*EF002326950**15375000*0*" . $idoutlet . "*" . $pin . "**179327694*1**ASRJWS*836828268*00*EXT: Sukses*01*PREMI : OCT-2017*PRM.102017*1***MATTALIA CLARA ANNALENE**15375000*******JIWASRAYA********* ";
            } else {
                $resp = "BAYAR*ASRJWS*251014583*12*20141201085321*H2H*".$idpel1."*MC001889322**200000*0*" . $idoutlet . "*" . $pin . "**-524833319*1**ASRJWS*259164764*00*EXT:Sukses*01*PREMI : NOV-2014*PRM.112014*1*02*TOTAL : NOV-2014 s/d DEC-2014*SUHAENAH*400000*400000*******JIWASRAYA*********";
            }
        }
    } else if ($kdproduk == "WASBY") {
        if(1==2){ 
            $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*" . $pin . "*------******99*Mitra Yth, mohon maaf, transaksi tidak dapat dilanjutkan untuk produk ini ";
        } else {
            if ($idpel1 == '4082411' && $ref2 == '134244522' && $nominal == '94140') {
                $resp = "BAYAR*WASBY*217746623*11*20131124020604*MOBILE_SMART*4082411***94140*2500*" . $idoutlet . "*" . $pin . "*------*13140930*0***134244528*00*PELUNASAN TAGIHAN SUKSES*0000000*4082411***0***YACUB ANDRES.Y*MERBABU 1*2***94140*PDAM SURABAYA***4D*11*2013***94*105*70640*0**23500**************************************************";
            } else if($idpel1 == '5029696'){
                
                $resp = "BAYAR*WASBY*884734632*10*20160304164312*H2H*5029696***000000006890*2000*HH43738*------**4993019*1***460291613*00*PELUNASAN TAGIHAN SUKSES*0000000*5029696***1***BALAI RW.III*BANYU URIP KIDUL 4 5*1***6890*PDAM SURABAYA***2A.1*03*2016***32*32*6140*0**750************************************************00010301**";
            } else if($idpel1 == '4106210' && $ref2 == '238074227' && $nominal == '15140'){
                $resp = "BAYAR*WASBY*1510566001*8*20170202160619*H2H*4106210***15140*2000*BS0004*------**954920*1***238074246*00*PELUNASAN TAGIHAN SUKSES*0000000*4106210***1***WIWIN*TAMBAK ASRI TERATAI 65 B*2***15140*PDAM SURABAYA***3A*04*2016***48*55*7640*7500**0**************************************************";
            }else {
                $resp = "BAYAR*WASBY*275137160*10*20141224092857*H2H*1013225***35490*2000*" . $idoutlet . "*" . $pin . "**16347515*1***267411812*00*PELUNASAN TAGIHAN SUKSES*0000000*1013225***1***DADANG SOEKARDI*WONOKROMO S.S BARU 2 8*1***35490*PDAM SURABAYA***3A*12*2014***4589*4613*27240*7500**750************************************************298348571**";
            }
        }

    } else if ($kdproduk == "FNADIRAH" || $kdproduk == 'FNADIRA') {

        if (substr($idpel1, 0, 4) == "0035") {
            $resp = "BAYAR*FNADIRAH*363028942*4*20150324095916*YM*003587535679***283000*4000*" . $idoutlet . "*" . $pin . "*------*5527243*1***297683265*00*Transaksi Anda sedang diproses, silahkan cek web report 10 menit lagi*0000000*58*003587535679*1**00000000000000*nurdin wijaya***283000****00000000000000*ADIRA FINANCE****00000000000000*3*3****03 apr 15*283000****283000*283000";
        } else {
            $resp = "BAYAR*FNADIRAH*207675546*10*20141016135232*DESKTOP*020913107656***575000*2500*" . $idoutlet . "*" . $pin . "**876274*1**FNADIRA*244231502*00*EXT: APPROVE*02*27*020913107656*01***IMAM SUDRAJAT*KP CIKARANG 45/09**575000*****Adira Finance***3117757740*F2096VR*14*14*03 OCT 14**0**575000*0*2000*300*575000*575000";
        }
    } elseif ($kdproduk == "WAJMBR" || $kdproduk == "WAJMBRIDM") {
        $resp = "BAYAR*WAJMBR*209510403*10*20141018100203*H2H*24035*24035**12500*2500*" . $idoutlet . "*" . $pin . "*------*-201635684*1**WAJMBR*244922439*00*SUKSES*0000000*00*24035*24035*24035*1***Aswanto*Perumh New Pesona AD-18*Cust Detail Info pindah ke bill_info70*0**PDAM JEMBER*09*2014*5150*5150*0*0*12500***************0********************";
    } elseif ($kdproduk == "WAPLMBNG") {// ID PELANGGAN  (NO SAMBUNGAN)
        
        if($idpel1 == '210689648'){
            $resp = "BAYAR*WAPLMBNG*210689648*9*20141019155534*H2H*210689648*210689648*210689648*68500*1500*" . $idoutlet . "*" . $pin . "*------*1571965*1**2009*245344877*00*EXT: APPROVE*0000000*00*210689648*210689648*210689648*1***M. YUNUS AS*****PDAM PALEMBANG*10*2014***0*68500*0***********************************";
        } else if($idpel1 == '7B085002500018'){
            $resp = "BAYAR*WAPLMBNG*1493617364*10*20170125070453*H2H*7B085002500018*7B085002500018*7B085002500018*100222*3000*" . $idoutlet . "*" . $pin . "**1088953*1**2009*653008063*00*EXT: APPROVE*0000000*00*7B0850250018*7B085002500018*7B085002500018*2***ELMA NILYANA*****PDAM PALEMBANG*12*2016***0*52497*0*01*2017***0*47725*0****************************   ";
        }
    } elseif ($kdproduk == "WABGK") {
        if ($idpel2 == "0104032417") {
            $resp = "BAYAR*WABGK*209448784*10*20141018091155*H2H*0*0104032417**50500*2500*" . $idoutlet . "*" . $pin . "*------*-199496639*1**WABGK*244895740*00*SUKSES*0000000*00*0*0104032417*0104032417*1***IDA SUSANTI*Jl. HALIM PERDANA KUSUMA GG.II *Cust Detail Info pindah ke bill_info70*0**PDAM BANGKALAN*9*2014*0*15*0*50500*0***********************************";
        } else {
            $resp = "BAYAR*WABGK*273941724*10*20141223052918*DESKTOP*0*0101001861*01-1-00186A*366275*6000*" . $idoutlet . "*" . $pin . "*------*1443172*0**WABGK*267026682*00*SUKSES*0000000*00*0*0101001861*01-1-00186A*4***NURJANNAH*KH. MARZUQI *Cust Detail Info pindah ke bill_info70*0**PDAM BANGKALAN*8*2014*0*25*12600*84000*0*9*2014*0*25*12600*84000*0*10*2014*0*19*9435*62900*0*11*2014*0*26*13140*87600*0**************";
        }
    } elseif ($kdproduk == "WABJN") {// ID PELANGGAN DAN NO SAMBUNGAN
        $resp = "BAYAR*WABJN*186513*10*20141124115558*DESKTOP*0*0111002*0*195500*4000*" . $idoutlet . "*" . $pin . "*------*499969100*1**WABJN*237998914*00**0000000*00*0*0111002*0*2***EKO SUDARMANTO*Jl. VETERAN 0 0*Cust Detail Info pindah ke bill_info70*0**PDAM BOJONEGORO*9*2014*0*0*0*0*84000*10*2014*0*10*0*27500*84000****************************";
    } elseif ($kdproduk == "WABDG") { // ID PELANGGAN (NO SAMBUNGAN)
        if($idpel1=="00A08650410"){
            sleep(40);
            $resp="BAYAR*WABDG*812362363*9*20160120172051*H2H*00A08650410*00A08650410*00A08650410*50000*2800*" . $idoutlet . "*------**-500019880*1**2003*439358976*00*SEDANG DIPROSES*0000000*00*00A08650410*00A08650410*00A08650410*1***DRS.SUBCHAN DWIYANTO*****PDAM BANDUNG*12*2015***00000000*50000*0***********************************";
        }else{
            if ($idpel1 == "00201410060") {
                $resp = "BAYAR*WABDG*209305561*9*20141018061332*DESKTOP*00201410060*00201410060*00201410060*90100*2800*" . $idoutlet . "*" . $pin . "*------*1135145*1**2003*244828224*00*EXT: APPROVE*0000000*00*00201410060*00201410060*00201410060*1***KOMAR*****PDAM BANDUNG*09*2014***0*90100*0***********************************";
            } else {
                $resp = "BAYAR*WABDG*274342266*9*20141223124533*H2H*00008901103*00008901103*00008901103*114400*2500*" . $idoutlet . "*" . $pin . "**-367882673*1**2003*267159669*00*EXT: APPROVE*0000000*00*00008901103*00008901103*00008901103*1***OEY TJWAN LIEN*****PDAM BANDUNG*11*2014***10400*104000*0***********************************";
            }
        }    
    } elseif ($kdproduk == "TVNEX") {
        $resp = "BAYAR*TVNEX*210978307*10*20141019205336*DESKTOP*622177311***108900*0*" . $idoutlet . "*" . $pin . "*------*1359004*1**060801*245467415*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*622177311*1*00000000000000614890*614890*IRVAN SOFIAN**NEXSPORTS PLATINUM MOVIES 1 BULAN*108900*0**0**00000000000000614890*NEX MEDIA****11-10-2014**MhxAQjNjhYE=**";
    } elseif ($kdproduk == "HPSMART") {
        $resp = "BAYAR*HPSMART*209747596*10*20141018134900*DESKTOP*088271084560***25388*0*" . $idoutlet . "*" . $pin . "*------*3685965*1**016004*245006387*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*088271084560*1*24912363505**B620-1010945-WAHYUDI-K***25388*SMART***************";
    } elseif ($kdproduk == "HPMTRIX") {
        $resp = "BAYAR*HPMTRIX*209341713*10*20141018072311*DESKTOP*08155101252***000000027500*2500*" . $idoutlet . "*" . $pin . "*------*1935079*0**HPMTRIX*244846204*00*APPROVE*0000000*00*08155101252*1***DIAN INDRESWARI****INDOSAT*201016*201016*000000000*0000000002750000*0000000000*      *      *         *                *          *      *      *         *                *";
   } elseif ($kdproduk == "FNFIF") {
       $resp = "BAYAR*FNFIF*2528242479*10*20180404144222*DESKTOP*507002360917***671000*3000*" . $idoutlet . "*" . $pin . "*------*539712*4**204111*966644876*00*SUCCESSFUL*50*101*507002360917*1*04042018---144220*216056*DARSONO*****0*3000*216056---5---0*FIF*K****036*005*13/04/2018**671000***0**0**    ";
    } elseif ($kdproduk == "TVORG50") {
        $resp = "BAYAR*TVORG50*261828123*10*20141211155319*H2H*81477650***50000*0*HH10774*------**5726102*1**060701*262686417*00*EXT:  APPROVED, TRANSACTION IS DONE WITHOUT  ERROR*0000000*00*81477650**abbb3a85a55aaa8a*****50000*****abbb3a85a55aaa8a*ORANGE ";
    } elseif ($kdproduk == "TVORG80") {
        $resp = "BAYAR*TVORG80*253708249*10*20141203173324*DESKTOP*33019333***80000*0*" . $idoutlet . "*" . $pin . "*------*245076*1**060701*259979931*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*33019333**6b5161b1161b41b1*****80000*****6b5161b1161b41b1*ORANGE TV******IRCXACKq4aU=**";
    } elseif ($kdproduk == "TVORG100") {
        $resp = "BAYAR*TVORG100*254031462*10*20141203220247*DESKTOP*10014413***100000*0*" . $idoutlet . "*" . $pin . "*------*378192*0**060701*260064167*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*10014413**33266363333c6663*****100000*****33266363333c6663*ORANGE TV******AhB1EUQjOfM=**";
    } elseif ($kdproduk == "TVORG300") {
        $resp = "BAYAR*TVORG300*252574671*10*20141202170043*DESKTOP*49045011***300000*0*" . $idoutlet . "*" . $pin . "*------*2523988*1**060701*259635362*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*49045011**ff1fe5ee88616ef0*****300000*****ff1fe5ee88616ef0*ORANGE TV******WxRUEUFmK/Q=**";
    } elseif ($kdproduk == "HPTHREE") {
        $resp = "BAYAR*HPTHREE*252312187*10*20141202125907*H2H*08984222333***55000*0*" . $idoutlet . "*" . $pin . "**27628401*1**012101*259558586*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*08984222333*1*1986015835**INTAN NUR AZIZA***55000*THREE***************";
    } elseif ($kdproduk == "HPFREN") {
        $resp = "BAYAR*HPFREN*240645388*10*20141119215722*DESKTOP*08885088008***47094*0*" . $idoutlet . "*" . $pin . "*------*200569*1**016002*255717645*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*08885088008*1*000000717644**HERU  WIDIJANTO***47094*FREN***************";
    } else if($kdproduk == "TVBIG"){
        $resp = "BAYAR*TVBIG*493383806*11*20150625094527*DESKTOP*1072161447***138499*0*" . $idoutlet . "*" . $pin . "*------*5286949*3**008001*339062776*00*EXT: APPROVE***1072161447*1**Mr. syahrun as *Mr. syahrun as ***138499*****CLOSE PAYMENT *TV BIG*0**008001***00010721614470080011CLOSE PAYMENT 000000138499Mr. syahrun as **";
    } else if($kdproduk == "WADEPOK"){
        if($idoutlet =='HH10632'){
            $bill_q = "1";
        } else {
            $bill_q = '01';
        }
        $resp = "BAYAR*WADEPOK*653113900*10*20151012114012*DESKTOP*02440121*0*0*000000197900*2500*" . $idoutlet . "*" . $pin . "*------*142611*1**1141062*391075500*00*SUCCESSFUL*0000000*0*02440121***".$bill_q."*20151012043000011048*20151012113910000000000000026359*PT. PRIMAMAS PERKASA***0000002500**PDAM KOTA DEPOK (JABAR)*9*2015*441*473*0*000000197900************************************";
    } else if($kdproduk == "WABATAM"){
        $resp = "BAYAR*WABATAM*2803949954*9*20180807092514*DESKTOP*52693*52693*52693*23180*2500*" . $idoutlet . "*" . $pin . "*------*9765030*1**2029*1080537646*00*EXT: APPROVE*0000000*OTORITA BATAM*52693*52693*52693*1**OTORITA BATAM*OTORITA BATAM**188792513001***PAM ATB BATAM*08*2018***0*23180*0***********************************    ";
    } else if($kdproduk == "WAKOBGR"){
        if($idoutlet =='HH10632'){
            $bill_q = "1";
        } else {
            $bill_q = '01';
        }
        $resp = "BAYAR*WAKOBGR*654743021*10*20151013115733*DESKTOP*15801274*0*0*000000261000*2500*". $idoutlet ."*".$pin."*------*934705*2**1141027*391618281*00*SUCCESSFUL*0000000*0*15801274***".$bill_q."*20151013043000033105*20151013115333000000000000917680*NENENG NS***0000002500**PDAM KOTA BOGOR*9*2015*0*1953*0*000000261000************************************";
    } else if(substr($kdproduk, 0, 4) == "TVKV"){
        $resp = "BAYAR*TVKV100*802199962*10*20160114210406*DESKTOP*110396769***100000*0*" . $idoutlet . "*" . $pin . "*------*6008826*3**061201*436126123*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*110396769*1*20160114210401182149**ATIK SUNARYA***110000*0**0**20160114210401182149*K-Vision TV******ET9VbQE3Gbc=**K VISION (100.000)";
    } else if(substr($kdproduk, 0, 5) == "TVSKY"){
        $resp = "BAYAR*TVSKYFAM1*866727005*9*20160222134606*H2H*37000004733***40000*0*".$idoutlet."*".$pin."**283634645*1**061301*455136372*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*37000004733*1*379800*****40000*****379800*TV SKYNINDO*****20160327*AjBHdjciG+Q=**SKYNINDO TV FAMILY 1 BLN (40.000)";
    } else if($kdproduk == "TVINNOV"){
        $resp = "BAYAR*TVINNOV*552442764*11*20150805181030*DESKTOP*10504754***368000*0*".$idoutlet."*".$pin."*------*62518*1**006007*357923002*00*EXT: APPROVE***10504754*1** SATRIA YUDA PRASETYO * SATRIA YUDA PRASETYO ***368000***** P150805181328331 *TV INNOVATE*0**000000368000 ***10504754 000000368000 SATRIA YUDA PRASETYO 20150704 I150805181248384 **";
    } else if($kdproduk == "WAPROLING"){
        $resp = "BAYAR*WAPROLING*1359913423*10*20161122110154*H2H*04000977***51750*2500*".$idoutlet."*".$pin."**790177967*1**400171*616420045*00*SUCCESSFUL*0000000*0*04000977***1*110152---22112016*SWITCHERID*FARAH SUDARSIH*DS.PATOKAN*null---null---null*000000002500**PDAM PROBOLINGGO*10*2016***0*51750*0***********************************";
    } else if($kdproduk == "WAKOSOLO"){
        $resp = "BAYAR*WAKOSOLO*1361597926*10*20161123091219*H2H*00030619***47300*1700*".$idoutlet."*".$pin."**580098884*1**400251*616860892*00*SUCCESSFUL*0000000*1*00030619***1*090250---23112016*SWITCHERID*Ny Misrini Iswandi*Mangga II A 75 RT 01/0*null---null---null*000000001700**PDAM KOTA SOLO*10*2016***4300*43000*0***********************************";
    } else if($nominal == '350000' && $kdproduk == "ASRCAR" && $ref2 == '617490048' && $idpel1 == '2377711000395827'){
        $resp = "BAYAR*ASRCAR*1363987318*9*20161124132620*H2H*2377711000395827***350000*0*".$idoutlet."*".$pin."*------*21902246*1**ASRCAR*617490293*00*SUCCES*0000000*00001*2377711000395827*01***BONG KIM SIN**350000*******AJ CENTRAL ASIA RAYA****350000*77711000395827*1100000000395827*NANTIKAN PRODUK TERBARU DAN PROMO DARI CAR - BECAUSE WE DO CARE**  ";
    } else if($kdproduk == 'WASAMPANG'){
        $date = date('YmdHis');
        if($idpel2 == '0101010080'){
            $resp = "BAYAR*WASAMPANG*1466747727*10*".$date."*DESKTOP*0*0101010080*01/I /001/0080/a*134211*5000*".$idoutlet."*".$pin."*------*945877*2**WASAMPANG*645563340*00*SUKSES*0000000*00*0*0101010080*01/I /001/0080/a*2***TIMBUL SUNGKONO*JL. SELONG PERMAI **0**PDAM TRUNOJOYO SAMPANG*11*2016*0*17*12201*61005*0*12*2016*0*17*0*61005*0****************************   ";
        }
    } else if($kdproduk == 'ASRPRU'){
        $date = date('YmdHis');
        $resp = "BAYAR*ASRPRU*2672085157*8*".$date."*WEB*11895488***500000*0*".$idoutlet."*".$pin."*------*2650605*4**PRUDENTIAL*1023349631*00*Sukses!*0000000**11895488*01*000000500000###ACU13326409084872646*25000###IDR###01*KURNIA RISTIYANI*********PRUDENTIAL*****0****    ";
    }
    /* tambahan */

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_kdproduk         = $frm->getKodeProduk();
    $r_tanggal          = $frm->getTanggal();
    $r_idpel1           = $frm->getIdPel1();
    $r_idpel2           = $frm->getIdPel2();
    $r_idpel3           = $frm->getIdPel3();
    $r_nominal          = (int) $frm->getNominal();
    $r_nominaladmin     = (int) $frm->getNominalAdmin();
    $r_idoutlet         = $frm->getMember();
    $r_pin              = $frm->getPin();
    $r_sisa_saldo       = $frm->getSaldo();
    $r_idtrx            = $frm->getIdTrx();
    $r_status           = $frm->getStatus();
    $r_keterangan       = $frm->getKeterangan();
    $r_saldo_terpotong  = 0;
    $r_nama_pelanggan   = getNamaPelanggan($kdproduk, $frm);
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }
    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);

    $token2 = $cektoken[3];
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
        $url_struk = "https://202.43.173.234/struk/?id=" . $url;
    }


    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"           	=> (string) $r_idpel1,
        "IDPEL2"           	=> (string) $r_idpel2,
        "IDPEL3"           	=> (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) trim(str_replace('.', '', $r_nama_pelanggan)),
        "PERIODE"           => (string) $r_periode_tagihan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => (string) $r_reff3,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) str_replace('.', '[dot]', $url_struk),
    );

    $implode = implode('.', $params);
    $implode_detail='';
    $final_implode = $implode;
    if (count($r_additional_datas) > 0) {
        $implode_detail = implode('.', $r_additional_datas);
        $final_implode = $implode.'.'.$implode_detail;
    }

    $is_return = true;
    if($r_idtrx != $ref2){
        $get_mid = get_mid_from_idtrx($r_idtrx);
        $get_step = get_step_from_mid($get_mid) + 1;
        writeLog($get_mid, $get_step, $host, $receiver, $final_implode, $via);
    }
    
    // return new xmlrpcresp(php_xmlrpc_encode($params));
    return $final_implode;
}

function balance($req){
	//GPIN*PINBARU*IDOUTLET*PIN*TOKEN*VIA
    $i = -1;
    $uid        = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $field      = 3;
    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    /*  if($ip != "10.0.0.20"  && $ip != "10.0.51.2"){
    if(!isValidIP($idoutlet, $ip)){
    die("Anda Tidak Mempunyai Hak Akses [".$ip."].");
    }
    } */
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

    $resp = "SAL*SAL*201321555*4**DESKTOP*" . $uid . "*".$pin."**257390*127382587*00*Saldo DUMMY Anda saat ini = Rp 257,390 Sms center 081228899888 dan 087838395999.";

    $format     = FormatMsg::cekSaldo();
    $frm        = new FormatCekSaldo($format[1], $resp);

    $r_idoutlet     = $frm->getMember();
    $r_pin          = $frm->getPin();
    $r_balance      = $frm->getSaldo();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();

    $params = array(
        "UID"       => $r_idoutlet,
        "PIN"       => '------',
        "SALDO"     => $r_status == '00' ? $r_balance : '',
        "STATUS"    => $r_status,
        "KET"       => trim(str_replace('.', '', $r_keterangan))
    );
    $implode = implode('.', $params);
    return $implode;
}

function pulsa($req){
	$i          = -1;
    $kdproduk   = strtoupper($req['produk']);
    $nohp       = strtoupper($req['no_hp']);
    $idoutlet   = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']) != "" ? strtoupper($req['ref1']) : "" ;
    $field      = 6;
    
    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    if(empty($req['produk']) || empty($req['no_hp']) || empty($req['uid']) || empty($req['pin'])){
        return 'invalid get request';
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip == "10.0.0.20") {
    //     return "IP Anda [$ip] tidak punya hak akses";
    // } else {
    //     if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
    //         return "IP Anda [$ip] tidak punya hak akses";
    //     }
    // }

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

    if($kdproduk == 'I100H'){
        if($nohp == "085213738399"){
            $resp = "PULSA*I100H*2878649199*4*20180912163328*H2H*085213738399**".$idoutlet."*".$pin."****16518858*1115182197*17*TRX pulsa I100H 085213738399 GAGAL. Jenis produk tidak cocok.   ";
        } else if($nohp == "085889535885"){
            $resp = "PULSA*I100H*2876840231*10*20180911181413*H2H*085889535885*96550*".$idoutlet."*".$pin."**5J**3419380*1114295339*SM5001*The third-party system fails to verify the recharge number. (SM50019)   ";
        } else if($nohp == "0858888888429"){
            $resp = "PULSA*I100H*2878812005*10*20180912174605*H2H*0858888888429*96725*".$idoutlet."*".$pin."**5J**148832014*1115263688*UMC650*The recharge MSISDN is incorrect (UMC65001)    ";
        } else {
            $resp = "PULSA*I100H*2880030556*9*20180913104606*H2H*08156876626*96550*".$idoutlet."*".$pin."**5J*01351100006517793951*2018525665*1115834811*00*Pengisian pulsa I100H Anda ke nomor 08156876626 BERHASIL. SN=01351100006517793951 Harga=96550   ";
        }
        
    } else if($kdproduk == 'S100H'){
        if($nohp == "08112335757"){
            $resp = "PULSA*S100H*2877175367*9*20180911210836*H2H*08112335757*96750*".$idoutlet."*".$pin."**SK100**1548108783*1114460419*999*EXT: FAILED ";
        } else {
            $resp = "PULSA*S100H*2879856014*9*20180913092651*H2H*081327404356*96725*".$idoutlet."*".$pin."**SK100*41002822747247*2157730839*1115753811*00*Pengisian pulsa S100H Anda ke nomor 081327404356 BERHASIL. SN=41002822747247 Harga=96725  ";
        }
    } else if($kdproduk == 'XR50H'){
        if($nohp == "081912583942" ){
            $resp = "PULSA*XR50H*2875545162*8*20180911020701*H2H*081912583942*50000*".$idoutlet."*".$pin."**HX50**1181239236*1113691487*14*EXT: MSISDN BLOCKED  ";
        } else if($nohp == "08192433131"){
            $resp = "PULSA*XR50H*2876102994*10*20180911105538*H2H*08192433131*50235*".$idoutlet."*".$pin."**RK50**-318102409*1113960297*999*EXT: FAILED  ";
        } else {
            $resp = "PULSA*XR50H*2879162346*10*20180912202235*H2H*081938505552*49975*".$idoutlet."*".$pin."**RK50* 96940912907859*272944636*1115439374*00*Pengisian pulsa XR50H Anda ke nomor 081938505552 BERHASIL. SN= 96940912907859 Harga=49975 ";
        }
    } else if($kdproduk == "T25H"){
        if($nohp == "082252114580"){
            $resp = "PULSA*T25H*2879297475*4*20180912215749*H2H*082252114580**".$idoutlet."*".$pin."****975783456*1115504570*17*TRX pulsa T25H 082252114580 GAGAL. Jenis produk tidak cocok.    ";
        } else {
            $resp = "PULSA*T25H*2878970424*8*20180912185446*H2H*0895800675766*24750*".$idoutlet."*".$pin."**T25*0912185449180846101*1013135111*1115342867*00*Pembelian voucher pulsa T25H berhasil ke no 0895800675766. Kode Voucher: 0912185449180846101.   ";
        }
    } else {
        if($kdproduk=="S10H" && $nohp=="085288949268"){
        $resp = "PULSA*S10H*808257112*10*".$tanggal."*H2H*".$nohp."*10850*".$idoutlet."*".$pin."**3023051**75625109*437881625*14*EXT: NO IDENTITAS PELANGGAN TIDAK DITEMUKAN";   
       
        
       }else if($kdproduk=="S10H" && $nohp=="081216611131"){
            sleep(20);
            header("HTTP/1.0 504 Gateway Time-out");
            die();
            $resp="PULSA*S10H*805817646*10*".$tanggal."*H2H*081216611131*49450*".$idoutlet."*------*------***665254*437110932**Pengisian pulsa S10H Anda ke nomor 081216611131 sedang diproses";
       }else if($kdproduk=="S10H" && $nohp=="081216611168"){
            sleep(3);
            $resp="null";//PULSA*S10H*805817646*10*".$tanggal."*H2H*081216611131*49450*".$idoutlet."*------*------***665254*437110932*68*Pengisian pulsa S10H Anda ke nomor 081216611131 sedang diproses";
       
       } else if( ($nohp == '085648889293' || $nohp == '6285648889293') && ($kdproduk == 'I25H' || $kdproduk == 'I5H' || $kdproduk == 'IT5H' || $kdproduk == 'I5' || $kdproduk == 'I10H' || $kdproduk == 'ID1H' || $kdproduk == 'ID1') ){
            sleep(40);
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*".$nohp."*".getHarga($kdproduk)."*".$idoutlet."*------*------***665254*437110932*00*SEDANG DIPROSES";
       }else{
            
            $resp = "PULSA*$kdproduk*164865*10*20141107162119*H2H*".$nohp."*".getHarga($kdproduk)."*".$idoutlet."*-----**-*1130234239174848*510065*237996835*00*Pengisian pulsa ".$kdproduk." Anda ke nomor ".$nohp." BERHASIL. SN=1130234239174848 Harga=". getHarga($kdproduk);//10025";
       }
    }
    
    $format = FormatMsg::pulsa();
    $frm = new FormatPulsa($format[1], $resp);

    //print_r($frm->data);

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
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "NO_HP"             => (string) $r_nohp,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "SN"                => (string) $r_sn,
        "NOMINAL"           => (string) $r_nominal,
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo
    );

    // $text = pulsa_game_resp_text($params);
    $get_mid = get_mid_from_idtrx($r_idtrx);
    $get_step = get_step_from_mid($get_mid) + 1;
    $implode = implode('.', $params);

    writeLog($get_mid, $get_step, $host, $receiver, $implode, $via);
    return $implode;
}

function game($req) {

    $i          = -1;
    $kdproduk   = strtoupper($req['produk']);
    $nohp       = strtoupper($req['no_hp']);
    $idoutlet   = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']) != "" ? strtoupper($req['ref1']) : "";
    $field      = 6;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    if(empty($req['produk']) || empty($req['no_hp']) || empty($req['uid']) || empty($req['pin'])){
        return 'invalid get request';
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip == "10.0.0.20") {
    //     return "IP Anda [$ip] tidak punya hak akses";
    // } else {
    //     if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
    //         return "IP Anda [$ip] tidak punya hak akses";
    //     }
    // }

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
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";

    $ml = array('ML1','ML2','ML3','ML4','ML5','ML6','ML7','ML8','ML1H','ML2H','ML3H','ML4H','ML5H','ML6H','ML7H','ML8H');
    if(in_array($kdproduk, $ml)){
        $resp = "GAME*".$kdproduk."*2359719405*10*".date('YmdHis')."*H2H*".$nohp."*0*".$idoutlet."*".$pin."***Kode Voucher: S201818036664.*515913*915694760*00*Pembelian voucher game online berhasil ke no $kdproduk. Kode Voucher: S201818036664.   ";
    } else if (strpos(strtoupper($kdproduk), 'TOL') !== false) {
        //$resp = "GAME*TOLM100H*2480835138*8*20180317004829*H2H*6032986047990543*100000*HH80079*------**TOL100*KODE VOUCHER: 1803170016 (SILAHKAN-TAP).*222229329*952236503*00*Pembelian voucher game online berhasil ke no 6032986047990543. Kode Voucher: 1803170016 (SILAHKAN-TAP).   ";
        $resp = "GAME*".$kdproduk."*2480835138*8*20180317004829*H2H*".$nohp."*100000*".$idoutlet."*".$pin."**TOL100**222229329*952236503*00*Pembelian game online TOLM100H Anda ke nomor 6032986047990543 sedang diproses   ";
    } else if(strpos(strtoupper($kdproduk), 'GJD') !== false){
        $resp = "GAME*".$kdproduk."*2481319390*7*20180317051332*MOBILE_SMART*".$nohp."*50000*".$idoutlet."*".$pin."*------*HGPD50*AHMAD SYARIF 711521238410865*1440414*952276002*00*Pembelian Game Online GJD50 BERHASIL. SN=AHMAD SYARIF 711521238410865";
    } else if(strpos(strtoupper($kdproduk), 'GJK') !== false){
        $resp = "GAME*".$kdproduk."*2481261346*8*20180317042417*H2H*".$nohp."*100750*".$idoutlet."*".$pin."**HGP100*BURGAS 581521235455496*33433874*952267798*00*Pembelian Game Online GJK100H BERHASIL. SN=BURGAS 581521235455496. Harga=100750. Saldo=33433874  ";
    } else if(substr(strtoupper($kdproduk), 0,3) == 'OVO'){
        $resp = "GAME*$kdproduk*4036603692*8*20200327094949*MOBILE_SMART*$nohp*100000*$idoutlet*------*------*100000*20200327095211404800 EKA RISMA*1045112*1735504048*00*TOP UP OVO BERHASIL. SN=20200327095211404800 EKA RISMA	";
    } else {
        $resp = "GAME*GS10H*1033881954*7*".date('YmdHis')."*H2H*".$nohp."*9300*".$idoutlet."*".$pin."**19574865*Voucher Code =24768271, Voucher Password=49678-00905-86487-19636-76298*455335649*505264128*00*Pembelian Game Online GS10H BERHASIL. SN=Voucher Code =24768271, Voucher Password=49678-00905-86487-19636-76298. Harga=9300. Saldo=455335649";    
    }

    $format = FormatMsg::game();
    $frm    = new FormatGame($format[1], $resp);

    //print_r($frm->data);

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
        "PIN"               => (string) '------',
        "SN"                => (string) $r_sn,
        "NOMINAL"           => (string) $r_nominal,
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) trim(str_replace('.', '', $r_keterangan)),
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo
    );
    // $text = pulsa_game_resp_text($params);
    $implode = implode('.', $params);
    $get_mid = get_mid_from_idtrx($r_idtrx);
    $get_step = get_step_from_mid($get_mid) + 1;
    writeLog($get_mid, $get_step, $host, $receiver, $implode, $via);

    return $implode;
}

function cek_is_telp_or_speedy($idpel){
    $prefix_telp = array('0627','0629','0641','0642','0643','0644','0645','0646','0650','0651','0652','0653','0654','0655','0656','0657','0658','0659','061','0620','0621','0622','0623','0624','0625','0626','0627','0628','0630','0631','0632','0633','0634','0635','0636','0639','0751','0752','0753','0754','0755','0756','0757','0759','0760','0761','0762','0763','0764','0765','0766','0767','0768','0769','0624','0770','0771','0772','0773','0776','0777','0778','0779','0740','0741','0742','0743','0744','0745','0746','0747','0748','0702','0711','0712','0713','0714','0730','0731','0733','0734','0735','0715','0716','0717','0718','0719','0732','0736','0737','0738','0739','0721','0722','0723','0724','0725','0726','0727','0728','0729','021','021','0252','0253','0254','0257','021','022','0231','0232','0233','0234','0251','0260','0261','0262','0263','0264','0265','0266','0267','024','0271','0272','0273','0274','0275','0276','0280','0281','0282','0283','0284','0285','0286','0287','0289','0291','0292','0293','0294','0295','0296','0297','0298','0299','0356','0274','031','0321','0322','0323','0324','0325','0327','0328','0331','0332','0333','0334','0335','0336','0338','0341','0342','0343','0351','0352','0353','0354','0355','0356','0357','0358','0361','0362','0363','0365','0366','0368','0364','0370','0371','0372','0373','0374','0376','0380','0381','0382','0383','0384','0385','0386','0387','0388','0389','0561','0562','0563','0564','0565','0567','0568','0534','0513','0522','0525','0526','0528','0531','0532','0536','0537','0538','0539','0511','0512','0517','0518','0526','0527','0541','0542','0543','0545','0548','0549','0554','0551','0552','0553','0556','0430','0431','0432','0434','0438','0435','0443','0445','0450','0451','0452','0453','0454','0457','0458','0461','0462','0463','0464','0465','0455','0422','0426','0428','0410','0411','0413','0414','0417','0418','0419','0420','0421','0423','0427','0471','0472','0473','0474','0475','0481','0482','0484','0485','0401','0402','0403','0404','0405','0408','0910','0911','0913','0914','0915','0916','0917','0918','0921','0922','0923','0924','0927','0929','0931','0901','0902','0951','0952','0955','0956','0957','0966','0967','0969','0971','0975','0980','0981','0983','0984','0985','0986');

    $tiga = 3;
    $empat = 4;
    $is_telp = FALSE;

    $ret = array(
        'produk'    => 'SPEEDY',
        'idpel1'    => $idpel,
        'idpel2'    => ''
    );

    $sub_str1 = substr($idpel, 0, $empat);
    $sub_str2 = substr($idpel, 0, $tiga);

    // ngecek 4 digit dulu baru 3 digit

    if(in_array($sub_str1, $prefix_telp)){
        $is_telp = TRUE;
        $idpelnya = explode($sub_str1, $idpel);
        $ret = array(
            'produk'    => 'TELEPON',
            'idpel1'    => $sub_str1,
            'idpel2'    => $idpelnya[1]
        );
    } else if(in_array($sub_str2, $prefix_telp)){
        $is_telp = TRUE;
        $idpelnya = explode($sub_str2, $idpel);
        $ret = array(
            'produk'    => 'TELEPON',
            'idpel1'    => $sub_str2,
            'idpel2'    => $idpelnya[1]
        );
        
    }

    if($is_telp){
        return $ret;
    } else {
        return $ret;    
    }

}