<?php
error_reporting(0);
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');
set_time_limit(120);

// if($_GET['devel']=="2"){
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// }
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// die('a');

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
// global $pgsql;
// $pgsql      = pg_connect("host=" . $__CFG_dbhost . " port=" . $__CFG_dbport . " dbname=" . $__CFG_dbname . " user=" . $__CFG_dbuser . " password=" . $__CFG_dbpass);

$msg        = $HTTP_RAW_POST_DATA;
$host       = getClientIP();//$_SERVER['REMOTE_ADDR'];
// die('a');

// $mid = getNextMID();
$mid        = rand(1,99999999999); //buat local
$step       = 1;
$raw_msg    = file_get_contents('php://input');
$raw        = json_decode($raw_msg);

$raw_cpy             = $raw_msg;
$raw_cpy_decode      = json_decode($raw_cpy);
$raw_cpy_decode->pin = '------';
$msg_log             = json_encode($raw_cpy_decode);
$sender              = "XML CLIENT";
$receiver           = $_SERVER['SERVER_ADDR']."-RB-JSON-".$_SERVER['HTTP_HOST']."-".$_SERVER['SERVER_NAME'];
$via                 = $GLOBALS["__G_via"];
// writeLog($mid, $step, $host, $receiver, $msg_log, $via);

//return json_encode(array('error'=>"xsaf"));
// die('a');
switch ($raw->method) {
    //======== inq pay ========
    case "rajabiller.bpjstk_inq": 
        echo bpjstk_inq($raw);
        break;
    case "rajabiller.bpjstk_pay":
        echo bpjstk_pay($raw);
        break;
    case "rajabiller.bpjstk_step_register":
        echo bpjstk_step_register($raw);
        break;
    //======== inq pay ========
    //======== daftar ========
    case "rajabiller.bpjstk_pekerjaan":
        echo bpjstk_pekerjaan($raw);
        break;
    case "rajabiller.bpjstk_propinsi":
        echo bpjstk_propinsi($raw);
        break;
    case "rajabiller.bpjstk_kabupaten":
        echo bpjstk_kabupaten($raw);
        break;
    case "rajabiller.bpjstk_cabang":
        echo bpjstk_cabang($raw);
        break;
    case "rajabiller.bpjstk_info_peserta":
        echo bpjstk_info_peserta($raw);
        break;
    case "rajabiller.bpjstk_daftar":
        echo bpjstk_daftar($raw);
        break;
    case "rajabiller.bpjstk_hitung_iuran":
        echo bpjstk_hitung_iuran($raw);
        break;
    case "rajabiller.bpjstk_proses_iuran":
        echo bpjstk_proses_iuran($raw);
        break;
    //======== daftar ========  

    case "rajabiller.balance":
        echo balance($raw);
        break;
    case "rajabiller.harga":
        echo harga($raw);
        break;
    case "rajabiller.transferinq":
        echo transferinq($raw);
        break;
    case "rajabiller.transferpay":
    // die('a');
        echo transferpay($raw);
        break;
    case "rajabiller.inq":
        echo inq($raw);
        break;
    case "rajabiller.pay":
        echo pay($raw);
        break;
    case "rajabiller.paydetail":
        echo pay_detail($raw);
        break;
    case "rajabiller.pulsa":
        echo pulsa($raw);
        break;
    case "rajabiller.game":
        echo game($raw);
        break;
     case "rajabiller.cekharga_gp":
        echo cek_harga2($raw);
        break;
     case "rajabiller.beli":
        echo beli($raw);
        break;
    case "rajabiller.bpjsinq":
        echo bpjs_inq($raw);
        break;
    case "rajabiller.bpjspay":
        echo bpjs_pay($raw);
        break;
    case "rajabiller.cu":
        echo cetak_ulang($raw);
        break;
    case "rajabiller.cudetail":
        echo cetak_ulang_detail($raw);
        break;
    case "rajabiller.cudetail2":
        echo cetak_ulang_detail2($raw);
        break; 
    case "rajabiller.cudetail3":
        echo cetak_ulang_detail3($raw);
        break;
    // case "rajabiller.inq_bintang":
    //     echo inq_bintang($raw);
    //     break;
    // case "rajabiller.pay_bintang":
    //     echo pay_bintang($raw);
    //     break;
    case "rajabiller.cekip":
        echo cek_ip($raw);
        break;
    case "rajabiller.info_produk":
        echo info_produk($raw);
        break;
    case "rajabiller.datatransaksi":
       echo data_transaksi($raw);
       break;
    case "rajabiller.daftar":
        echo daftar($raw);
        break;
    case "rajabiller.inqpln":
        echo inqpln($raw);
        break;
    case "rajabiller.paypln":
        echo paypln($raw);
        break;
    case "rajabiller.inqpln2":
        echo inqpln2($raw);
        break;
    case "rajabiller.paypln2":
        echo paypln2($raw);
        break;
    case "rajabiller.inqpdam":
        echo inqpdam($raw);
        break;
    case "rajabiller.paypdam":
        echo paypdam($raw);
        break;
    case "rajabiller.inqkk":
        echo inqkk($raw);
        break;
    case "rajabiller.paykk":
        echo paykk($raw);
        break; 
    case "rajabiller.cekstatus":
        echo cekstatus($raw);
        break;
    case "rajabiller.cek":
       echo cek($raw);
       break;
    case "rajabiller.bayar":
       echo bayar($raw);
       break;

    default :
        echo json_encode(array('Produk tidak dikenal'));
}
// PLNPRA
// TAGIHAN*KKBNI*1216183649*2**H2H*34018062991***10000**FA9919*967733*******REF1_VALUE*;;;;
// TAGIH*KKBNI*4636831884*2**H2H*34018062991***10000**FA9919*967733*******REF1_VALUE*JSON INQKK-localhost
// PULSA*II12H*4636835146*2**H2H*34018062991**FA9919*967733******REF1_VALUE*
// PULSA
// 
function cek_harga2($data)
{
     $result = array();
    $next   = FALSE;
    $end    = FALSE;
    // global $pgsql;

    $group      = strtoupper($data->group);
    $produk     = strtoupper($data->produk);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);

    // echo $group."".$id_produk;
    $field      = 5;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
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
        return json_encode($params, JSON_PRETTY_PRINT);
    }


    if ($next) {
        if (checkpin($idoutlet, $pin)) {
            $next = TRUE;
        } else {
            $rc     = "02";
            $ket    = "Pin yang Anda masukkan salah";
            $params = array(
                "UID"       => $idoutlet,
                "PIN"       => '------',
                "STATUS"    => $rc,
                "KET"       => $ket,
                "DATA"      => $result 
            );
            return json_encode($params, JSON_PRETTY_PRINT);
        }
    }

    if($next){
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
                if($group == "FASTMOVE"){
                    if(substr(strtoupper($foruse[$i]->id_produk), 0,3) == "OVO" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "DANA" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "GJK" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "LINK" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "GJD" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "GRAB" ||substr(strtoupper($foruse[$i]->id_produk), 0,4) == "SHOP" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "TOLM" ||substr(strtoupper($foruse[$i]->id_produk), 0,5) == "TOLBR" ){
                        $dt1[] = array(
                            'idproduk' => $foruse[$i]->id_produk,
                            'namaproduk' => $foruse[$i]->produk,
                            'groupproduk' => "EMONEY",
                            'harga_jual' => $foruse[$i]->harga_jual,
                            'biaya_admin' => $foruse[$i]->biaya_admin,
                            'status' => $status
                        );
                    }elseif(substr(strtoupper($foruse[$i]->id_produk), 0,2) == "ML" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "PUBG" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "GFF" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "GRN" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "HAGO" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "GUNI" ||substr(strtoupper($foruse[$i]->id_produk), 0,4) == "GAOV" || substr(strtoupper($foruse[$i]->id_produk), 0,2) == "GS" ||substr(strtoupper($foruse[$i]->id_produk), 0,3) == "ITN" || substr(strtoupper($foruse[$i]->id_produk), 0,2) == "LY"|| substr(strtoupper($foruse[$i]->id_produk), 0,2) == "MS"|| substr(strtoupper($foruse[$i]->id_produk), 0,4) == "MOGA"|| substr(strtoupper($foruse[$i]->id_produk), 0,6) == "MOGCAZ" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "MOG"|| substr(strtoupper($foruse[$i]->id_produk), 0,3) == "MOL" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "STGC"|| substr(strtoupper($foruse[$i]->id_produk), 0,4) == "STWC" || substr(strtoupper($foruse[$i]->id_produk), 0,5) == "TIXID"|| substr(strtoupper($foruse[$i]->id_produk), 0,3) == "GWW" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "ZPT" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "AGV"|| substr(strtoupper($foruse[$i]->id_produk), 0,3) == "ASS" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "BNC"|| substr(strtoupper($foruse[$i]->id_produk), 0,3) == "BSF"|| substr(strtoupper($foruse[$i]->id_produk), 0,3) == "DML"|| substr(strtoupper($foruse[$i]->id_produk), 0,4) == "FBGC"|| substr(strtoupper($foruse[$i]->id_produk), 0,4) == "FUPO"|| substr(strtoupper($foruse[$i]->id_produk), 0,3) == "GAR" || substr(strtoupper($foruse[$i]->id_produk), 0,3) == "GAS"|| substr(strtoupper($foruse[$i]->id_produk), 0,4) == "GCOD"|| substr(strtoupper($foruse[$i]->id_produk), 0,3) == "NCO" || substr(strtoupper($foruse[$i]->id_produk), 0,4) == "VCOD"|| substr(strtoupper($foruse[$i]->id_produk), 0,5) == "VROGZ" ){
                        
                         $dt2[] = array(
                            'idproduk' => $foruse[$i]->id_produk,
                            'namaproduk' => $foruse[$i]->produk,
                            'groupproduk' => $foruse[$i]->group_produk,
                            'harga_jual' => $foruse[$i]->harga_jual,
                            'biaya_admin' => $foruse[$i]->biaya_admin,
                            'status' => $status
                        );
                    }else{
                         $dt3[] = array(
                            'idproduk' => $foruse[$i]->id_produk,
                            'namaproduk' => $foruse[$i]->produk,
                            'groupproduk' => $foruse[$i]->group_produk,
                            'harga_jual' => $foruse[$i]->harga_jual,
                            'biaya_admin' => $foruse[$i]->biaya_admin,
                            'status' => $status
                        );
                    }
                    $dt = array(
                        'emoney' => $dt1,
                        'game' => $dt2,
                        'pulsa' => $dt3
                    );
                }else{
                
                    $dt[] = array(
                        'idproduk' => $foruse[$i]->id_produk,
                        'namaproduk' => $foruse[$i]->produk,
                        'groupproduk' => $foruse[$i]->group_produk,
                        'harga_jual' => $foruse[$i]->harga_jual,
                        'biaya_admin' => $foruse[$i]->biaya_admin,
                        'status' => $status
                    );
                }
                // $datas[] = $foruse[$i]->id_produk;
                // $datas[] = $foruse[$i]->produk;
                // $datas[] = $foruse[$i]->harga_jual;
                // $datas[] = $foruse[$i]->biaya_admin;
                // $datas[] = $komisi;
                // $datas[] = $status;
                // $msgcontent[] =  implode("#", $datas);
            }
            // print_r($dt);
            // $result = $msgcontent;
            $params = array(
                "UID"       => $idoutlet,
                "PIN"       => '------',
                "STATUS"    => "00",
                "KET"       => "Data ditemukan",
                "DATA"      => $dt 
            );
            return json_encode($params, JSON_PRETTY_PRINT);
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
            return json_encode($params, JSON_PRETTY_PRINT);
        }
    }
}

function beli($data){
    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel      = strtoupper($data->idpel);
    $nominal    = strtoupper($data->nominal);
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $idpel3     = "";
    $field      = 7;

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    if($kdproduk == ""){
        return json_encode(array('error'=>'invalid id product'));
    }

    if(($nominal == "" || $nominal === "0") && $kdproduk != ""){
        $nominal = getValuePlnpra($kdproduk);
    } 

    if(!in_array($kdproduk, KodeProduk::getPLNPrepaids())){
        return json_encode(array('error'=>'only product pln prepaid allowed'));
    }

    // $whitelist_outlet = array('FA9919', 'FA112009', 'FA32670');
    // if(!in_array($uid, $whitelist_outlet) && substr($uid, 0, 2) == "FA"){
    //     return json_encode(array('error'=>'uid not allowed'));
    // }
    
    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
    //     if (!isValidIP($uid, $ip)) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    $idpel_asli = "";
    // if (in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
    //     $normal_idpel = normalisasiIdPel1PLNPra($idpel);
    //     $idpel1 = $normal_idpel["idpel1"];
    //     $idpel2 = $normal_idpel["idpel2"];
    //     $idpel_asli = $normal_idpel["idpel_asli"];
    // }

    // if($idpel1 == "" && $idpel2 == ""){
    //     return json_encode(array('error'=>'in valid number id'));
    // }

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
    $msg[$i+=1] = strtoupper($uid);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ref1;
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;
    $fm = convertFM($msg, "*");
    
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    
    $list = $GLOBALS["sndr"];

    if( in_array($kdproduk, KodeProduk::getPLNPrepaids()) ){
        // $respon = postValue($fm);
        // $resp = $respon[7];    

          $resp = "TAGIHAN*PLNPRAH*4309283567*10*20200914181030*H2H*01107552562*211018760870***2500*".$uid."*------*------*399892172*1**053502*1915027506*00*TRANSAKSI SUKSES*JTL53L3*01107552562*211018760870*0*82E949793A9E4BDDBE911A070A84479A*0BMS210Z6FB9D4C2C368B017380014BB**LERI MONIKA PRICILIA*R1M*000000900****21*21101*123*00648*0****************";
    } else {
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $uid . "*" . $pin . "*------******XY*Mitra Yth, mohon maaf, method ini khusus untuk produk PLN PRABAYAR saja ";     
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

    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }

    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }
    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);
    $nominalnya  = $nominal;
    $md_request = array(
        'method' => 'rajabiller.pay_detail',
        'kode_produk' => $r_kdproduk,
        'idpel1' => $r_idpel1,
        'idpel2' => $r_idpel2,
        'idpel3' => '',
        'nominal' => $nominalnya,
        'uid' => $r_idoutlet,
        'pin' => '------',
        'ref1' => $ref1,
        'ref2' => $r_idtrx,
        'ref3' => $idpel_asli
    );

    
    
    if($r_status == "00"){  
        writeLog($r_mid, $r_step, $ip, $receiver, json_encode($md_request), $GLOBALS["via"]);
        $md_request['pin'] = $pin;
        $md_response = pay_detail((object)$md_request);
        return $md_response;
    } else {
        $params = array(
            "KODE_PRODUK"       => (string) $r_kdproduk,
            "WAKTU"             => (string) $r_tanggal,
            "IDPEL1"            => (string) $r_idpel1,
            "IDPEL2"            => (string) $r_idpel2,
            "IDPEL3"            => (string) $r_idpel3,
            "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
            "PERIODE"           => (string) $r_periode_tagihan,
            "NOMINAL"           => (string) $nominalnya,
            "ADMIN"             => (string) $r_nominaladmin,
            "UID"               => (string) $r_idoutlet,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1,
            "REF2"              => (string) $r_idtrx,
            "REF3"              => "0",
            "STATUS"            => (string) $r_status,
            "KET"               => (string) $r_keterangan,
            "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
            "SISA_SALDO"        => (string) $r_sisa_saldo,
            "URL_STRUK"         => (string) $url_struk
        );
        if (count($r_additional_datas) > 0) {
            $params['DETAIL'] = $r_additional_datas;
        }
        writeLog($r_mid, $r_step, $ip, $receiver, json_encode($params), $GLOBALS["via"]);
        return json_encode($params, JSON_PRETTY_PRINT);
    }
}
function inqkk($data){
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN

    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $nominal    = strtoupper($data->nominal);
    $field      = 9;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // print_r($data);
    // die();
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];


    // global $pgsql;
    global $host;

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "TAGIHAN";
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = substr(strtoupper($kdproduk), 0,5) == "BLTRF" ? date('YmdHis') : '';
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = substr($kdproduk, 0,2) == "KK" ? "" : $nominal;
    $msg[$i+=1] = substr($kdproduk, 0,2) == "KK" ? $nominal : "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($uid);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ref1;
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
    $fm = convertFM($msg, "*");
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    // echo $msg."<br>";
    // die();
    $list = $GLOBALS["sndr"];
    
     if($nominal < 50000)
     {
        $resp = "TAGIHAN*KKBNI*4645878750*8*20210420073723*H2H*".$idpel1."***".$nominal."*7500*".$idoutlet."*------*------*125095*1*14*014*2102132138*35*EXT: Maaf, minimal transaksi kartu kredit adalah Rp. 50.000!*0000000*00*".$idpel1."*01***********KARTU KREDIT BANK BNI*******   ";
     }else{
        $resp = "TAGIHAN*KKBNI*2656828520*9*20180530073732*H2H*548988881036xxxx***".$nominal."*6000*" . $uid . "*" . $pin . "**19459795*1**BNI*1016819889*00*Sukses!*0559*00*5489888810362324*01***DADANG ISKANDAR SKM********BNI*14052018*03062018****".$nominal."* ";
    }
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $params     = setMandatoryResponNew($frm, $ref1, "", "", $data);
    
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk($kdproduk, $frm);
    $adddata2   = tambahdataproduk2($kdproduk, $frm);
    $merge      = array_merge($params,$adddata,$adddata2);
    
    return json_encode($merge);


}

function paykk($data) {

    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $nominal    = strtoupper($data->nominal);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $field      = 10;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // // handle 504 start
    // if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
    //     if(empty(urlreversalexists($idoutlet))){
    //         date_default_timezone_set('asia/jakarta');
    //         $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
    //         $datein = (int) strtotime(date('Y-m-d H:i:s'));
    //         $selisih = $datein - $my_ips;
    //         appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
    //         if($selisih >= $GLOBALS["__G_selisih"]){
    //             $params = array(
    //                 "KODE_PRODUK"       => (string) $kdproduk,
    //                 "WAKTU"             => (string) date('YmdHis'),
    //                 "IDPEL1"            => (string) $idpel1,
    //                 "IDPEL2"            => (string) $idpel2,
    //                 "IDPEL3"            => (string) $idpel3,
    //                 "NAMA_PELANGGAN"    => (string) '',
    //                 "PERIODE"           => (string) '',
    //                 "NOMINAL"           => (string) $nominal,
    //                 "ADMIN"             => (string) '',
    //                 "UID"               => (string) $idoutlet,
    //                 "PIN"               => (string) '------',
    //                 "REF1"              => (string) $ref1,
    //                 "REF2"              => (string) $ref2,
    //                 "REF3"              => (string) $ref3,
    //                 "STATUS"            => (string) '77',
    //                 "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
    //                 "SALDO_TERPOTONG"   => (string) '',
    //                 "SISA_SALDO"        => (string) '',
    //                 "URL_STRUK"         => (string) ''
    //             );
    //             appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
    //              insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
    //             return json_encode($params, JSON_PRETTY_PRINT);
    //         }
    //     }
    // }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }
    
    // global $pgsql;
    global $host;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";

    if($ref2 == ""){
        $ref2 = 0;
    }
    // $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan

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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN
    $fm         = convertFM($msg, "*");
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];
    
   $resp = "BAYAR*KKBNI*2656829460*9*20180530073815*H2H*548988881036xxxx***".$nominal."*6000*" . $idoutlet . "*" . $pin . "**18963495*1**BNI*1016820300*00*Sukses!*0559*00*5489888810362324*01***DADANG ISKANDAR SKM********BNI*14052018*03062018****".$nominal."* ";
    
    if($resp == 'null'){
        $params = array(
            "KODE_PRODUK"       => (string) $kdproduk,
            "WAKTU"             => date('YmdHis'),
            "IDPEL1"            => (string) $idpel1 ,
            "IDPEL2"            => (string) $idpel2 ,
            "IDPEL3"            => (string) $idpel3 ,
            "NAMA_PELANGGAN"    => (string) '' ,
            "PERIODE"           => (string) '' ,
            "NOMINAL"           => (string) '' ,
            "ADMIN"             => (string) '' ,
            "UID"               => (string) $idoutlet ,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1 ,
            "REF2"              => (string) '' ,
            "REF3"              => (string) '' ,
            "STATUS"            => (string) '00' ,
            "KET"               => (string) 'SEDANG DIPROSES' ,
            "SALDO_TERPOTONG"   => (string) '' ,
            "SISA_SALDO"        => (string) '' ,
            "URL_STRUK"         => (string) '' ,
        );
        writeLog($GLOBALS["mid"], $stp+1, $receiver, $host, json_encode($params), $GLOBALS["via"]);
        return json_encode($params, JSON_PRETTY_PRINT);
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $params     = setMandatoryResponNew($frm, $ref1, "", "", $data);
    
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk($kdproduk, $frm);
    $adddata2   = tambahdataproduk2($kdproduk, $frm);
    if ($frm->getStatus() == "00") {
        $url        = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk  = array("url_struk" => "https://202.43.173.234/struk/?id=" . $url);
        $merge      = array_merge($params,$adddata,$adddata2,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2);
    }

    return json_encode($merge);
}

function inqpdam($data){
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $field      = 8;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    // if($uid != 'SP300203'){
    //     if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
    //         if (!isValidIP($uid, $ip)) {
    //             return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //         }
    //     }
    // }

    // global $pgsql;
    global $host;

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
    $msg[$i+=1] = strtoupper($uid);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ref1;
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>";die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    $list = $GLOBALS["sndr"];
    if(substr(strtoupper($kdproduk), 0,2) != "WA"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk pdam";
    } else { 
        // $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
        // $resp = $respon[7]; 
        if($kdproduk == "WAPLMBNG"){
              $resp = "TAGIHAN*WAPLMBNG*1493617166*10*20170125070440*H2H*7B085002500018*7B085002500018*7B085002500018*100222*3000*" . $idoutlet . "*" . $pin . "**1189775*1**2009*653008004*00*EXT: APPROVE*0000000*00*7B0850250018*7B085002500018*7B085002500018*2***ELMA NILYANA*****PDAM PALEMBANG*12*2016***0*52497*0*01*2017***0*47725*0**************************** ";
        } else if ($kdproduk == "WALMPNG") { // ID PELANGGAN 
            // 2 BLN
            $resp = "TAGIHAN*WALMPNG*254362664*9*20141204104841*H2H*010501*010501*010501*111440*5600*" . $idoutlet . "*" . $pin . "*------*1114119*0**2011*260186111*00*EXT: APPROVE*0000000*00*010501*010501*010501*2***BUSRON TOHA*****PDAM LAMPUNG*11*2014***0*62640*0*10*2014***5000*43800*0****************************";
        }else if ($kdproduk == "WAMAKASAR") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAMAKASAR*359960863*10*20150321075229*H2H*199200987***000000043320*2500*" . $idoutlet . "*" . $pin . "*------*215755*1**1021014*296702929*00*SUCCESSFUL*0000000*0*199200987***01*20150321043000000699**M. NATSIR***0000002500**PDAM KOTA MAKASAR*2*2015*00003512*00003521 *0*000000043320************************************";
        }  else if($kdproduk=="WASDA"){
            if($idpel1 == "04002679" || $idpel2 == "01/II /004/0083/2D"){
                $resp = "TAGIHAN*WASDA*253012547*10*20141203081150*H2H*".$idpel1."*".$idpel2."**0*1800*" . $idoutlet . "*" . $pin . "*******04*EXT: Tidak ditemukan nomor pelanggan************0**PDAM SIDOARJO******************************************";
            }else if($idpel1 == "68002679" || $idpel2 == "01/II /068/0083/2D"){
                sleep(40);
                $resp = "TAGIHAN*WASDA***20141203081150*H2H*".$idpel1."*".$idpel2."*A*0*0*" . $idoutlet . "*" . $pin . "****************************************************************";    
                
            }else if($idpel1 == "08002679" || $idpel2 == "01/II /008/0083/2D"){
                $resp = "TAGIHAN*WASDA*253012547*10*20141203081150*H2H*".$idpel1."*".$idpel2."*AB130083*0*1800*" . $idoutlet . "*" . $pin . "*******08*EXT: Tidak ada tagihan************0**PDAM SIDOARJO******************************************";    
            }else{
                $resp="TAGIHAN*WASDA*812314109*10*20160120170040*H2H*02004159*02/I  /007/0147/2D*BA070147*154100*3600*" . $idoutlet . "*------**-492108960*1**WASDA*439339531*00*SUKSES*0000000*00*02004159*02/I  /007/0147/2D*BA070147*2***SUKARNI*PERUM WISMA SARINADI III I-17**0**PDAM SIDOARJO*11*2015*0*18*7500*87300*0*12*2015*0*13*0*59300*0****************************";
            }
          
        }else if($kdproduk == 'WABWANGI'){
            $resp = "TTAGIHAN*WABWANGI*4576411732*11*20210301070553*H2H*01014948***140200*5000*SP329524*------**3474328*1**WABWANGI*2057190115*00*SUCCESS*0000000*00*01014948*R2*01/B/36/014948/R2*2***SAMSURI ABAS*GRIYA GIRI MULYA W/47*{'kode':'11','idpel':'01014948','nosamb':'01\/B\/36\/014948\/R2','nama':'SAMSURI ABAS','alamat':'GRIYA GIRI MULYA W\/47','tarif':'R2','jumlahrek':2,'rekening':[{'bulan':'1','tahun':'2021','norek':'295522','stand':'3808','pakai':'20','tagair':'51300','beban':10000,'denda':10000,'tagnonair':0,'jumlah':71300},{'bulan':'2','tahun':'2021','norek':'562532','stand':'3830','pakai':'22','tagair':'58900','beban':10000,'denda':0,'tagnonair':0,'jumlah':68900}]} ***PUDAM BANYUWANGI*1*2021*3808*3828*10000*51300*0|10000|295522*2*2021*3830*3852*0*58900*0|10000|562532**************************** ";
        }else if ($kdproduk == "WAPONOR") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAPONOR*4586191366*8*20210308092103*H2H*1007020754***40400*2500*".$idoutlet."*------**179196354*1**400681*2063391997*00*SUCCESSFUL*2063391997*1*1007020754***1*092100---08032021*SWITCHERID*DWI ANDRE SETIAWAN**null---null---null*000000002500**PDAM KAB PONOROGO*02*2021***0*40400*0***********************************    ";
        } else if ($kdproduk == "WAMADIUN") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAMADIUN*371957091*10*20150401111459*H2H*0208050150***79580*2500*" . $idoutlet . "*" . $pin . "*------*1529353*3**400261*300388297*00*SUCCESSFUL*0000000*0*0208050150***1*111614---01042015*0000008001*SLAMET AS**201503*000000002500**PDAM KOTA MADIUN*03*2015***0*79580************************************";
        } elseif ($kdproduk == "WAKLUNGK") {
            $resp = "TAGIHAN*WAKLUNGK*121615166440124*10*20210308082041*H2H*16150*16150**18100*2000*".$idoutlet."*------**311623452*1**400601*2063316994*00*SUCCESSFUL*2063316994*1*16150*16150**1*082038---08032021*SWITCHERID*DRS.I KOMANG TUNAS**null---null---null*000000002000**PDAM KLUNGKUNG*02*2021***0*18100*0***********************************    ";
        }elseif ($kdproduk == "WAJMBR") {

            $resp = "TAGIHAN*WAJMBR*209510142*10*20141018100148*H2H*24035*24035**12500*2500*" . $idoutlet . "*" . $pin . "*------*-201622384*1**WAJMBR*244922337*00*SUKSES*0000000*00*24035*24035*24035*1***Aswanto*Perumh New Pesona AD-18**0**PDAM JEMBER*09*2014*5150*5150*0*0*12500***************0********************";
        }else if($kdproduk == 'WABADUNG'){
            $resp = "TAGIHAN*WABADUNG*4586095627*10*20210308082854*H2H*070550026350***13773*2500*".$idoutlet."*------**163382599*1**400551*2063326574*00*SUCCESSFUL*0000000*400551*070550026350***1*082850---08032021---2063326574*666850*I DEWA AYU NYOMAN TRI EKA DEWI***2500*16273*PDAM KAB BADUNG*02*2021*688*689*0*13773*0***********************************   ";
        }else if($kdproduk == 'WAPROLING'){
            $resp = "TAGIHAN*WAPROLING*1359913296*10*20161122110152*H2H*04000977***51750*2500*".$idoutlet."*".$pin."**790231317*1**400171*616420007*00*SUCCESSFUL*0000000*0*04000977***1*110151---22112016*SWITCHERID*FARAH SUDARSIH**null---null---null*000000002500**PDAM PROBOLINGGO*10*2016***0*51750*0***********************************";
        }else if($kdproduk == "WASITU"){
            $resp = "TAGIHAN*WASITU*1692770471*10*20170425163604*H2H*01/I /007/1659/B1*01/I /007/1659/B1*14001*107500*6000*".$idoutlet."*".$pin."*------*267059*1**WASITU*708528386*00*SUKSES*0000000*00*01/I /007/1659/B1*01/I /007/1659/B1*14001*3***SAMSUL HADI*JL. CEMPAKA PERUM ISMU H- 18 *#1:2017:5000:25000:0#2:2017:5000:0:0#3:2017:5000:0:0*25000**PDAM SITUBONDO*1*2017*0*5*0*22500*0*2*2017*0*10*0*22500*0*3*2017*0*10*40000*22500*0*********************    ";
        } else if ($kdproduk == "WAGRESIK") { // NO SAMBUNGAN
            if($idpel1 == "4900105"){
                $resp = "TAGIHAN*WAGRESIK*4792754642*10*20210804031040*H2H*4900105****2500*".$idoutlet."*------**53413309*3**1329*2219520182*99*TIDAK ADA TAGIHAN UNTUK NO PELANGGAN 49-00105*0000000*00*4900105***********PDAM Kab Gresik*******************************************    ";
            }elseif($idpel1 == "0500444"){
                $resp = "TAGIHAN*WAGRESIK*121628197237270*10*20210806040040*H2H*0500444*0500444***2500*".$idoutlet."*------**362651554*1**1329*2221933714*99*MOHON MAAF PELANGGAN NO 05-00444, HANYA BISA MELAKUKAN PEMBAYARAN DI KANTOR PDAM GRESIK*0000000*00*0500444*0500444**********PDAM Kab Gresik*******************************************   ";
            }else{
                $resp = "TAGIHAN*WAGRESIK*121615171356192*10*20210308094237*H2H*9700418*9700418**19500*2500*".$idoutlet."*------**179053830*1**1329*2063418287*00*SUKSES*0000000*281*97-00418*9700418**1*094236---0308---2063418287**TURNANINGSIH*PALEM PERTIWI JB-09 BLOK.JB-09 RT.17 RW.8**2500*19500*PDAM Kab Gresik*02*2021* * 0*0*19500*0*********************************** ";
            }

        } else if ($kdproduk == "WAJMBG") { // NO SAMBUNGAN
            $resp = "TAGIHAN*WAJMBG*4585067268*10*20210307100855*H2H*0302022101***68200*2700*".$idoutlet."*------**177044464*1**WAJMBG*2062695536*00*SUKSES*0000000*00*0302022101***1***YS PANDIA,SH.*KOMBES DURYAT**0**PERUMDAM Tirta Kencana Jombang*02*2021*1636*1658*0*68200*0***********************************    ";

        } else if ($kdproduk == "WASBY") {
            if($idpel1 == "2385086"){
                //tidak ditemukan
                $resp = "TAGIHAN*WASBY*809299825*11*20160119093427*H2H*2385086***0*2000*" . $idoutlet . "*" . $pin . "*------*2221666*1***438239256*03*EXT: NO PELANGGAN TIDAK DITEMUKAN*0000000*2385086************PDAM SURABAYA***************************************************************";
            } else if($idpel1 == "4040190"){
                //rekening bermasalah
                $resp = "TAGIHAN*WASBY*807318756*11*20160118083944*H2H*4040190***0*2000*" . $idoutlet . "*" . $pin . "*------*------*------***437552776*02*EXT: REKENING BERMASALAH, SILAHKAN MELAKUKAN PEMBAYARAN KE PDAM SURABAYA*0000000*4040190************PDAM SURABAYA***************************************************************";
            } else if($idpel1 == "4986605424599"){
                //request salah
                $resp = "TAGIHAN*WASBY*810092186*10*20160119160808*H2H**4986605424599***2000*" . $idoutlet . "*" . $pin . "*------*310110*2***438517824*04*EXT: REQUEST SALAH*0000000**4986605424599***********PDAM SURABAYA***************************************************************";
            } else if($idpel1 == "4097178"){
                //sudah dilunasi
                $resp = "TAGIHAN*WASBY*807094861*10*20160118043931*H2H*4097178****2000*" . $idoutlet . "*" . $idoutlet . "**25437239*1***437473307*01*EXT: TAGIHAN SUDAH DILUNASI*0000000*4097178************PDAM SURABAYA***************************************************************";
            } else if($idpel1 == "5441470"){
                //waktu trx habis
                sleep(10);
                $resp = "TAGIHAN*WASBY*811169920*9*20160120085151*MOBILE*5441470***0*2000*" . $idoutlet . "*" . $pin . "**4169286*1***438896553*68*WAKTU TRANSAKSI HABIS COBA BEBERAPA SAAT LAGI*0000000*5441470************PDAM SURABAYA***************************************************************";
            } else if ($idpel1 == '4082411') {
                $resp = "TAGIHAN*WASBY*217746583*11*20131124020442*H2H*4082411***94140*2500*" . $idoutlet . "*" . $pin . "*------*13237570*0***134244522*00*INQUIRY SUKSES*0000000*4082411***1***YACUB ANDRES.Y*MERBABU 1*2***94140*PDAM SURABAYA***4D*11*2013***94*105*70640*0**23500**************************************************";
            } else if($idpel1 == '4106210'){
                $resp = "TAGIHAN*WASBY*1510342729*10*20170202141613*H2H*4106210*4106210**15140*2000*BS0004*------**954920*1***238074227*00*INQUIRY SUKSES*0000000*4106210*4106210**1***WIWIN*TAMBAK ASRI TERATAI 65 B*2***15140*PDAM SURABAYA***3A*04*2016***48*55*7640*7500**0**************************************************";
            }else {
                $resp = "TAGIHAN*WASBY*275137088*10*20141224092853*H2H*1013225***35490*2000*" . $idoutlet . "*" . $pin . "**16383805*1***267411786*00*INQUIRY SUKSES*0000000*1013225***1***DADANG SOEKARDI*WONOKROMO S.S BARU 2 8*1***35490*PDAM SURABAYA***3A*12*2014***4589*4613*27240*7500**750**************************************************";
            }
        
    } else if ($kdproduk == "WATRENGG") { // NO SAMBUNGAN
            $resp = "TAGIHAN*WATRENGG*111615168204763*10*20210308085005*H2H**01503000000389*000389*30000*2000*".$idoutlet."*------**-1758866791*1**WATRENGG*2063350759*00*SUKSES*0000000*00**01503000000389*000389*1***YUSPITA ARDYANTI /APBN'18*Jl. GRIYA DAMAI BUKIT ASRI G 21 **0**PDAM TRENGGALEK*2*2021*0*0*0*30000*0***********************************  ";

        } else if ($kdproduk == "WAJMBG") { // NO SAMBUNGAN
            if($idpel1 == "0305028101"){
                   $resp = "TAGIHAN*WAJMBG*121628207604359*10*20210806065358*H2H*0305028101****2700*".$idoutlet."*------**20758570*1**WAJMBG*2221997554*15*EXT: TAGIHAN ANDA LEBIH DARI 5. SILAHKAN BAYAR DI PDAM.*0000000*00*0305028101***********PERUMDAM Tirta Kencana Jombang******************************************* ";
            }elseif($idpel1 == "0204012001"){
                $resp = "TAGIHAN*WAJMBG*1628190708816669363*9*20210806021222*WEB*0204012001****2700*".$idoutlet."*------*FASTPAY*41630*2**WAJMBG*2221923036*17*EXT: TAGIHAN SUDAH DILUNASI*0000000*00*0204012001***********PERUMDAM Tirta Kencana Jombang*******************************************280   ";
            }else{
                $resp = "TAGIHAN*WAJMBG*4794805442*10*20210805122632*H2H*0401033901***40700*2700*".$idoutlet."*------**2732192*1**WAJMBG*2221142952*00*SUKSES*0000000*00*0401033901***1***PERUM JAYA ABADI*WISMA JAYA ABADI F 4**0**PERUMDAM Tirta Kencana Jombang*07*2021*3098*3111*0*40700*0************************************    ";
            }
         

        }else if ($kdproduk == "WAMJK") { // NO SAMBUNGAN
            if($idpel2 == "1601020322"){
                $resp = "TAGIHAN*WAMJK*121628194038113*10*20210806030718*H2H**1601020322***2500*".$idoutlet."*------**4916597700*1**WAMJK*2221928341*08*EXT: TIDAK ADA TAGIHAN*0000000*00**1601020322**********PERUMDAM MOJOPAHIT MOJOKERTO*******************************************    ";
            }elseif($idpel2 == "1611010146"){
                $resp = "TAGIHAN*WAMJK*121628194690700*10*20210806031811*H2H*1611010146****2500*".$idoutlet."*------**93711143*1**WAMJK*2221929372*04*EXT: TIDAK DITEMUKAN NOMOR PELANGGAN*0000000*00*1611010146***********PERUMDAM MOJOPAHIT MOJOKERTO*******************************************    ";
            }elseif($idpel2 == "0002498773026"){
                $resp = "TAGIHAN*WAMJK*121628206751694*10*20210806063914*H2H**0002498773026***2500*".$idoutlet."*------**-2613354700*1**WAMJK*2221987494*01*EXT: SALAH NOMOR PELANGGAN ATAU BELUM DISET*0000000*00**0002498773026**********PERUMDAM MOJOPAHIT MOJOKERTO*******************************************    ";
            }else{
                $resp = "TAGIHAN*WAMJK*247143394*10*20141126170615*H2H*0*0909040028*09.07.06.0336*122415*4000*" . $idoutlet . "*" . $pin . "*------*227110*1**WAMJK*257960476*00*SUKSES*0000000*00*0*0909040028*09.07.06.0336*2***SUKADI*SUKOANYAR-GONDANG **0**PDAM KAB. MOJOKERTO (JATIM)*9*2014*0*19*11900*46000*0*10*2014*0*23*8415*56100*0****************************";
             }

        }else if($kdproduk == 'WAPASU'){
            $resp = "TAGIHAN*WAPASU*2886749606*9*20180916171654*H2H*03050228*228**74540*2500*".$idoutlet."*".$pin."**2397053790*1**PASURUAN*1118855845*00*SUCCESS*0000000*00*03050228*228**1*03**MISLAN*BULU RT/W. 02/01 BULUSARI*RUMAH TANGGA C***PDAM KAB. PASURUAN*08*2018*5596*5618*0*74540*0****************************193******* ";
        }  else if($kdproduk == 'WAKOPASU'){
            $resp = "TAGIHAN*WAKOPASU*861463171*9*20160219132100*H2H*c1-03943*10*c1-03943*68310*2500*".$idoutlet."*".$pin."**1015927766*1**WAKOPASU*453311443*00*SUKSES*0000000*00*c1-03943*c1-03943*c1-03943*1***DUMAH*Jl. Maluku No.9  RT.3/VIII**0**PDAM KOTA PASURUAN*1*2016*1438*1458|24600|26559|10|10*0*66310*2000|0|2460|2951|5700|5000|***************0********************";
        } elseif ($kdproduk == "WABJN") {
            $resp = "TAGIHAN*WABJN*186510*10*20141124115433*H2H*0*0111002*0*195500*4000*" . $idoutlet . "*------*------*168600*1**WABJN*237998913*00**0000000*00*0*0111002*0*2***EKO SUDARMANTO*Jl. VETERAN 0 0**0**PDAM BOJONEGORO*9*2014*0*0*0*0*84000*10*2014*0*10*0*27500*84000****************************";
           
        } else if($kdproduk == 'WASUMENEP'){
            $resp = "TAGIHAN*WASUMENEP*4586186345*9*20210308091820*H2H*0106319*A06319/09/RA*A06319*74200*4000*".$idoutlet."*------*------*528905*1**WASUMENEP*2063388543*00*SUKSES*0000000*00*0106319*A06319/09/RA*A06319*2***ABD. RAHMAN/FAD*SELAMET RIYADI 29**0**PDAM SUMENEP*1*2021*97*97*11000*31600*0*2*2021*97*97*0*31600*0****************************  ";
        }else if($kdproduk == 'WAKOPROB'){
            $resp = "TAGIHAN*WAKOPROB*121614515710251*10*20210228193510*H2H*014375*014375**55880*2500*".$idoutlet."*------**532191411*1**WAKOPROB*2056906682*00*EXT: Data tagihan Tersedia*0000000*00*014375*014375**1*C#=#RUMAH TANGGA A**P ICAP*PATIMURA/KAV KTI***55880*PDAM KOTA PROBOLINGGO*01*2021*1802*1823*5080*43800*3500|3500|0|0|0|0***********************************    ";
        } else if($kdproduk == 'WALMJNG'){
            $resp = "TAGIHAN*WALMJNG*121614560972868*9*20210301080934*H2H*01270147*01270147*01270147*83500*2500*".$idoutlet."*------**17436203*1**2046*2057255163*00*EXT: APPROVE*0000000*AHMAD AL JUFRI*01270147*01270147*01270147*01**AHMAD AL JUFRI*AHMAD AL JUFRI*****PDAM LUMAJANG*01*2021***5000*78500*0***********************************    ";
        } else if ($kdproduk == "WAKOKEDIRI") { // ID PELANGGAN
            if($idpel1 == "4843"){
                $resp = "TAGIHAN*WAKOKEDIRI*121628195867255*11*20210806033747*H2H*4843*4843**0*2500*".$idoutlet."*------**363954854*1**WAKOKEDIRI*2221931458*101*EXT: Tidak ada data*0000000*00*4843*4843**1***-*-***0*PDAM KOTA KEDIRI*-*-*0*0*0*0*0************************************ ";
            }elseif($idpel1 == "2880"){
                $resp = "TAGIHAN*WAKOKEDIRI*121628110980704*11*20210805040304*H2H*2880*2880**0*2500*".$idoutlet."*------**558927718*1**WAKOKEDIRI*2220636513*104*EXT: Data tagihan Lebih dari 5*0000000*00*2880*2880**1***-*-***0*PDAM KOTA KEDIRI*-*-*0*0*0*0*0************************************  ";
            }elseif($idpel1 == "961423"){
                $resp = "TAGIHAN*WAKOKEDIRI*4794250508*11*20210805045124*H2H*961423***0*2500*".$idoutlet."*------**17860983*1**WAKOKEDIRI*2220646213*003*EXT: No Sambungan tidak tersedia*0000000*00*961423***1***-*-***0*PDAM KOTA KEDIRI*-*-*0*0*0*0*0************************************  ";
            }else{

             $resp = "TAGIHAN*WAKOKEDIRI*121614562197912*11*20210301082958*H2H*18813*18813**31500*2500*".$idoutlet."*------**389254817*1**WAKOKEDIRI*2057278780*00*SUCCESS*0000000*00*18813*18813**1*R2#=#R2**SUSINTA SEFIYANTI*PERUM GRIYA BANARAN INDAH A - 10***31500*PDAM KOTA KEDIRI*02*2021*690*700*0*23000*3500|5000|0|0|0|0***********************************    ";
            
            }
        }else if ($kdproduk == "WAMAGETAN") { // ID PELANGGAN
             $resp = "TAGIHAN*WAMAGETAN*4574505008*7*20210227143528*H2H*41000004***150500*2000*".$idoutlet."*------**220446070*1**WAMAGETAN*2055995210*00*SUKSES*0000000*00*41000004*D1-00004*2A|C14*2***MUCH SUROJO*DS.TULUNG,BANJENG *12500-Denda Keterlambatan#**150500*PDAM MAGETAN*12*2020*445*466*0*55500*12500-Denda Keterlambatan*1*2021*466*493*0*82500***************************** ";
        }else if ($kdproduk == "WABONDO") { // ID PELANGGAN
             $resp = "TAGIHAN*WABONDO*250810684*10*20141130205909*H2H*09000879*09/01/001/00879/RB**94130*4500*" . $idoutlet . "*" . $pin . "*------*5420368*1**FY834n7Vs4mdASP4H34n*259098050*00*EXT: REQUEST SUKSES.*0000000*00*09000879*09/01/001/00879/RB**3***DWI YULIANA*PONCOGATI RT 11/5**0**PDAM BONDOWOSO*8*2014*0*5*15000*9400*0|16150*9*2014*0*4*5000*7520*0|16150*10*2014*0*2*5000*3760*0|16150*********************";
        }else if ($kdproduk == "WAPAMES") { // ID PELANGGAN
            $resp = "TAGIHAN*WAPAMES*4575554985*10*20210228131452*H2H*02052239***64450*3000*".$idoutlet."*------**175910177*1**WAPAMES*2056611038*00*EXT: Data tagihan Tersedia*0000000*00*02052239***1*A2#=#A2**SUPANDI*DS. PADELEGAN***64450*PDAM KAB PAMEKASAN*01*2021*595*610*5000*56950*0|2500.00|0|0|0.00|0***********************************  ";
        }else if ($kdproduk == "WABGK") { // ID PELANGGAN
            $resp = "TAGIHAN*WABGK*273941512*10*20141223052804*H2H*0*0101001861*01-1-00186A*366275*6000*" . $idoutlet . "*" . $pin . "*------*1815447*0**WABGK*267026591*00*SUKSES*0000000*00*0*0101001861*01-1-00186A*4***NURJANNAH*KH. MARZUQI **0**PDAM BANGKALAN*8*2014*0*25*12600*84000*0*9*2014*0*25*12600*84000*0*10*2014*0*19*9435*62900*0*11*2014*0*26*13140*87600*0**************";
        }else if ($kdproduk == "WATAGUNG") { // ID PELANGGAN
            $resp = "TAGIHAN*WATAGUNG*121614557066513*10*20210301070427*H2H**01108030000052*31219*31000*2000*".$idoutlet."*------**53304577*1**WATAGUNG*2057188879*00*SUKSES*0000000*00**01108030000052*31219*1***YUNANIK*JL. ABDUL FATAH III**0**PDAM TULUNGAGUNG*2*2021*0*1*0*31000*0***********************************   ";
        }else if ($kdproduk == "WANGAWI") { // ID PELANGGAN
            $resp = "TAGIHAN*WANGAWI*121614553684785*10*20210301060805*H2H**0301011493*C/A/01/1493*36310*2000*".$idoutlet."*------**334871542*1**WANGAWI*2057150941*00*SUKSES*0000000*00**0301011493*C/A/01/1493*1***YATIN *JOGOROGO **0**PDAM NGAWI*2*2021*0*16*0*36310*0*********************************** ";
        }else if ($kdproduk == "WAKABMLGNA") { // ID PELANGGAN
            $resp = "TAGIHAN*WAKABMLGNA*121614171164529*11*20210224195244*H2H*13040003737***36850*3000*".$idoutlet."*------**123911056*1**WAKABMLGNA*2054156819*00*SUCCESS*0000000*00*13040003737*RAJI*RUMAH TANGGA A3*1***RAJI*PURI RAYA KAV B3**2500**Perumda TirtaKanjuruhan*02*2021*37*27*3350*33500*0*****0******************************    ";
        } else if ($kdproduk == "WASLMN") { // ID PELANGGAN
            $resp = "TAGIHAN*WASLMN*327274183*10*20150218183900*H2H*1400669***60000*2500*" . $idoutlet . "*" . $pin . "**-228214716*0**400071*284869686*00*SUCCESSFUL*0000000*0*1400669***1*183847---18022015*0000008001*NADI KUSNADI**201501*000000001700**PDAM SLEMAN*01*2015***0*60000************************************";
        }  else if ($kdproduk == "WAKABMLG") { // ID ".$idoutlet."*".$pin."
            $resp = "TAGIHAN*WAKABMLG*406628*10*20150401153604*H2H*8101120001982***000000025000*1800*" . $idoutlet . "*" . $pin . "**318634507*0**1061032*238016366*00*SUCCESSFUL*0000000*0*8101120001982***01*20150401043000011022*1A5FD4B1F60D4A6D9FC0000000000000*YUGUS***0000002100**PDAM KAB. MALANG*4*2015*0000001816 * 0000001826*0*000000025000************************************";
        } else if ($kdproduk == "WABATANG") { // ID PELANGGAN
        //         1 BLN

            $resp = "TAGIHAN*WABATANG*4571201992*10*20210224162652*H2H*0880020197**0880020197*60750*2500*".$idoutlet."*------**25850768*1**WABATANG*2054013544*00*Sukses*0000000*00*0880020197*0880020197*0880020197*1***HARMINTO*DS/SUMUR BANGER | R2 (Rumah Tangga 2)*eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZHBlbCI6IjA4ODAwMjAxOTciLCJpYXQiOjE2MTQxNTg4MTIsImV4cCI6MTYxNDI0NTIxMn0.gNgK74vnkZD2Dmc__xGRS8yQENmzStZG7YubvH10Opc#[{'no_materai':'-','subsidi':'0','met_l':'2039','met_k':'2055','periode':'202101','materai':'0','denda':'3500','harga_air':'57250','pakai':'16','jumlah':'60750'}]*0**PDAM KAB. BATANG (JATENG)*01*2021*2039*2055*3500*60750*16|57250|0|0|-0*********************************** ";
        }else if ($kdproduk == "WAMAGLG") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAMAGLG*4570121516*9*20210223171855*H2H*0802110046***115380*7500*".$idoutlet."*------**964157568*1**400151*2053419542*00*SUCCESSFUL*0000000*400151*0802110046***3*171850---23022021---2053419542*SWITCHERID*Ready Dwi Darmayandi***7500*122880*PDAM KAB MAGELANG*11*2020*0*0*6600*33080*0*12*2020*0*0*6600*33080*0*01*2021*0*0*6000*30020*0********************* ";
        }else if ($kdproduk == "WAKARANGA") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAKARANGA*373051125*10*20150402081029*H2H*0702011372***28000*2000*" . $idoutlet . "*" . $pin . "*------*2470332*2**400121*300700551*00*SUCCESSFUL*0000000*0*0702011372***1*081024---02042015*0000008001*SENEN**201503*000000002000**PDAM Karanganyar*03*2015***0*28000************************************";
        }  else if ($kdproduk == "WABLORA") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WABLORA*4572210614*10*20210225144427*H2H*202020248***250300*2500*".$idoutlet."*------**6293113*1**WABLORA*2054582618*00*Sukses*0000000*00*202020248***1***SATRIYONO*SEMANGAT****PDAM KAB. BLORA*01*2021*4113*4154*10000*240300************************************  ";
        }else if ($kdproduk == "WABREBES") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WABREBES*373003775*10*20150402075234*H2H*1601040330***47000*2500*" . $idoutlet . "*" . $pin . "*------*2598022*3**400341*300693445*00*SUCCESSFUL*0000000*0*1601040330***1*075229---02042015*0000008001*Hersodo**201503*000000002500**PDAM KAB. BREBES*03*2015***0*47000************************************";
        } else if ($kdproduk == "WAKPKLNGAN") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAKPKLNGAN*371624007*10*20150401070629*H2H*0104010422***70550*2000*" . $idoutlet . "*" . $pin . "*------*682474*3**400101*300279891*00*SUCCESSFUL*0000000*0*0104010422***1*070624---01042015*0000008001*Moh. Abdullah**201503*000000002000**PDAM KAB. PEKALONGAN*03*2015***0*70550************************************";
        }else if ($kdproduk == "WAREMBANG") { // ID PELANGGAN
    //         1 BLN
             $resp = "TAGIHAN*WAREMBANG*347346522*10*20150310155253*H2H*LA-03-00012***530000*4000*" . $idoutlet . "*" . $pin . "*------*0*2**400301*291968179*00*SUCCESSFUL*0000000*0*LA-03-00012***2*155255---10032015*0000008001*R A M I S I H**201502,201501*000000004000**PDAM Kab. Rembang*02*2015***0*269400**01*2015***0*260600*****************************";
        } else if ($kdproduk == "WAPBLINGGA") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAPBLINGGA*375520059*10*20150404063054*H2H*14050151***103260*2000*" . $idoutlet . "*" . $pin . "*------*1493724*3**400271*301406435*00*SUCCESSFUL*0000000*0*14050151***1*063207---04042015*0000008001*MAS'UT NUR H.**201503*000000002250**PDAM PURBALINGGA*03*2015***0*103260************************************";
        } else if ($kdproduk == "WAPURWORE") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAPURWORE*373604198*10*20150402123006*H2H*01033200271***75800*1600*" . $idoutlet . "*" . $pin . "*------*91718*2**400211*300819084*00*SUCCESSFUL*0000000*0*01033200271***1*123006---02042015*0000008001*Pranoto Suwignyo**201503*000000001600**PDAM PURWOREJO*03*2015***0*75800************************************";
        } else if ($kdproduk == "WASKHJ") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WASKHJ*121614168094077*10*20210224190136*H2H*0101870137***62100*2000*".$idoutlet."*------**33729745*1**400511*2054119616*00*SUCCESSFUL*2054119616*1*0101870137***1*190135---24022021*SWITCHERID*Sardi**null---null---null*000000002000**PDAM KAB. SUKOHARJO*01*2021***0*62100*0***********************************  ";
        }else if ($kdproduk == "WAKABSMG") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAKABSMG*372895672*10*20150402041804*H2H*310050799***23140*2000*" . $idoutlet . "*" . $pin . "*------*47945*1**400201*300654359*00*SUCCESSFUL*0000000*0*310050799***1*041439---02042015*0000008001*BAGUS SETIAWAN**201504*000000002000**PDAM KAB. SEMARANG*04*2015***0*23140************************************";
        }  else if ($kdproduk == "WAWONOGIRI") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAWONOGIRI*373652299*10*20150402130534*H2H*02040172***79000*2000*" . $idoutlet . "*" . $pin . "*------*98002*0**400141*300832572*00*SUCCESSFUL*0000000*1*02040172***1*130249---02042015*0000008001*DRS SUPARNO**201503*000000002000**PDAM KAB. WONOGIRI*03*2015***0*79000************************************";
        } else if ($kdproduk == "WAWONOSB") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAWONOSB*373039402*10*20150402080625*H2H*0114120063***62020*2000*" . $idoutlet . "*" . $pin . "*------*479251*1**400331*300699024*00*SUCCESSFUL*0000000*0*0114120063***1*080628---02042015*0000008001*MOCH NASIR SUNYOTO**201503*000000002000**PDAM KAB. WONOSOBO*03*2015***0*62020************************************";
        } else if ($kdproduk == "WABYMS") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WABYMS*379540798*10*20150407104222*H2H*0124619***31540*2000*" . $idoutlet . "*" . $pin . "*------*4922712*0**400011*302720717*00*SUCCESSFUL*0000000*1*0124619***1*105501---07042015*0000008001*NANI WIBOWO**MAR15*000000002000**PDAM BANYUMAS*3*2015***0*31540************************************";
        }else if ($kdproduk == "WACLCP") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WACLCP*340812345*10*20150304160850*H2H*0105041625***000000269600*6000*" . $idoutlet . "*" . $pin . "**11422600*1**1021012*289596616*00*SUCCESSFUL*0000000*0*0105041625***03*20150304043000017000**ETI WIDIASTUTI***0000006000**PDAM CILACAP*12*2014*00002694*0*0*0**1*2015*0*0*0*0**2*2015*0*00002762 *0*000000269600**********************";
        } else if ($kdproduk == "WAKPROGO") { // ID PELANGGAN
//         2 BLN
            $resp = "TAGIHAN*WAKPROGO*4571937300*10*20210225102714*H2H*071500424***85000*2000*".$idoutlet."*------**2708282*1**2051*2054423129*00*SUKSES*0000000*00*071500424***1***Eko Sumarno*Kalidengen**0**PDAM KULON PROGO*01*2021*2004*2024*5000*80000*0***********************************    ";
        }else if ($kdproduk == "WATEGAL") { // ID PELANGGAN
//         2 BLN
            $resp = "TAGIHAN*WATEGAL*4571999519*10*20210225112037*H2H*0106018468***45500*2500*".$idoutlet."*------**91551883*1**1332*2054459297*00*SUKSES*0000000*281*0106018468***1*112037---0225---2054459297**HABIBI*DS.PESAYANGAN RT 05/01 TALANG (MBR)**2500*45500*PDAM KAB TEGAL*01*2021* 0*0*0*45500*0*********************************** ";
        }else if ($kdproduk == "WAGKIDUL") { // ID PELANGGAN
//         2 BLN
            $resp = "TAGIHAN*WAGKIDUL*4571946266*9*20210225103435*H2H*010300218**010300218,*75200*2500*".$idoutlet."*------**31121184*1**WAGKIDUL*2054428455*00*SUKSES*0000000*00*010300218***1***TALIP*SENENG**0**PDAM GUNUNG KIDUL*01*2021*2046*2061*5000*70200*0****************************193******* ";
        }else if ($kdproduk == "WASRAGEN") { // ID PELANGGAN
//         2 BLN
            $resp = "TAGIHAN*WASRAGEN*371905541*10*20150401103958*H2H*0800564***87000*3400*" . $idoutlet . "*" . $pin . "*------*227221*2**400181*300373672*00*SUCCESSFUL*0000000*0*0800564***2*093108---01042015*0000008001*WARTINAH A**201503,201502*000000003400**PDAM KAB. SRAGEN*03*2015***0*31250**02*2015***0*55750*****************************";
        }else if($kdproduk == 'WAKABTRA'){
            $resp = "TAGIHAN*WAKABTRA*4572023949*10*20210225114253*H2H*C040041***30000*2500*".$idoutlet."*------**15828506*1**1000*2054473170*00*SUKSES*0000000*00*C040041***01*#0*R2#163158*ATUT SUPARTA*Kp.Cikareo 005 02*#25000***PDAM KAB TANGERANG*02*2021*114*114*00005000*25000*0***********************************  ";
        }else if($kdproduk == 'WACIAMIS'){
            $resp = "TAGIHAN*WACIAMIS*2780129264*10*20180726104607*H2H*04030020147***81400*2500*".$idoutlet."*".$pin."**-52781096*1**400621*1069446672*00*SUCCESSFUL*1069446672*1*04030020147***1*104606---26072018*SWITCHERID*AMY MARYA, SE**null---null---null*000000002500**PDAM CIAMIS*06*2018***0*81400*0***********************************    ";
        }else if ($kdproduk == "WABOGOR") { // ID PELANGGAN
            // 1 BLN
            $resp = "TAGIHAN*WABOGOR*303308580*10*20150123095026*H2H*07411152***000000078540*2500*" . $idoutlet . "*" . $pin . "*------*2254934*1**1021030*276888092*00*SUCCESSFUL*0000000*0*07411152***01*20150123043000003809**AAS RAMAESIH***0000002500**PDAM KAB. BOGOR*12*2014*00000711*00000727 *0*000000078540************************************";
        }else if($kdproduk == 'WAMAJALENG'){
            $resp = "TAGIHAN*WAMAJALENG*4570693919*10*20210224084917*H2H*1003002283***36070*2500*".$idoutlet."*------**48865586*1**1356*2053740409*00*SUKSES*0000000*281*1003002283***1*084916---0224---2053740409**RATMA \/MBR 2020*PANYINGKIRAN BLOK JUMAT RT 005 RW 0**2500*36070*PDAM MAJALENGKA*01*2021* 13*17*5000*31070*0***********************************  ";
        } else if($kdproduk == 'WADEPOK'){
            $resp = "TAGIHAN*WADEPOK*653112046*10*20151012113910*H2H*02440121***000000197900*2500*" . $idoutlet . "*" . $pin . "*------*343011*1**1141062*391074910*00*SUCCESSFUL*0000000*0*02440121***01*20151012043000011048*20151012113910229438530074815926*PT. PRIMAMAS PERKASA***0000002500**PDAM KOTA DEPOK (JABAR)*9*2015*441*473*0*000000197900************************************";
        } elseif($kdproduk == "WAJBRBNJR"){
            $resp = "TAGIHAN*WAJBRBNJR*4571591877*10*20210224220205*H2H*1321400655***45000*2500*".$idoutlet."*------**49208712*1**400111*2054222127*00*SUCCESSFUL*2054222127*1*1321400655***1*220205---24022021*SWITCHERID*PUDJI SUHARTONO**null---null---null*000000002500**PDAM KOTA BANJAR*01*2021***0*45000*0*********************************** ";
        }elseif($kdproduk == "WAKOSKBUMI"){
            $resp = "TAGIHAN*WAKOSKBUMI*4570144045*9*20210223173544*H2H*01060010622***70620*2500*".$idoutlet."*------*------*899379*1**tirtabumi*2053432974*00*Sukses*0000000*00*01060010622***1**11*Jai Sutisna *Rt.20/IV****PDAM KOTA SUKABUMI*01*2021*1590 * 1598**70620*0***********************************    ";
        }elseif($kdproduk == "WAMEDAN"){
            $resp = "TAGIHAN*WAMEDAN*1644378434*10*20170404123851*H2H*0117080017***88000*7500*".$idoutlet."*".$pin."**105739277*1**1002*694616383*00*SUCCESSFUL*0000000*00*0117080017***03*#0#0#0*N.3#138067*SYAIFUL HALIM*PEMUDA BARU III 12*#10800.00#18600.00#18600.00***PDAM KOTA MEDAN (SUMUT)*02*2017*46000*47000*00020000*10800*0*03*2017*47000*49000*00020000*18600*0*04*2017*49000*51000*00000000*18600*0********************* ";
        }elseif($kdproduk == "WABEKASI"){
            $resp = "TAGIHAN*WABEKASI*4570924680*10*20210224120426*H2H*010404007001***140800*2500*".$idoutlet."*------**253868415*1**1142*2053863904*00*SUKSES*0000000*00*010404007001***01*#1*#084546*DARSONO*KP. RAWA PASUNG*#130800***PDAM KOTA BEKASI*01*2021*689*707*00010000*130800*0***********************************    ";
        }elseif($kdproduk == "WAGARUT"){
            $resp = "TAGIHAN*WAGARUT*4570982147*10*20210224125902*H2H*049277***84960*3000*".$idoutlet."*------**12818183*1**1135*2053894883*00*SUKSES*0000000*00*049277***01*#0*21#088533*IMAS SUMIATI*JLN.SUMBERSARI 2/18*#70800***PDAM Garut*01*2021*1571*1590*00014160*70800*0*********************************** ";
        }elseif($kdproduk == "WACIREBON"){
            $resp = "TAGIHAN*WACIREBON*4570819978*10*20210224103411*H2H*0113004021***794550*7500*".$idoutlet."*------**57195786*1**1346*2053808813*00*SUKSES*0000000*281*0113004021***3*103411---0224---2053808813**T A R K A M*SENDE Blok 004**7500*794550*PDAM KAB. CIREBON*11*2020* 5274*5319*5000*305650*0*12*2020* 5319*5364*5000*305650*0*01*2021* 5364*5389*5000*168250*0********************* ";
        }elseif($kdproduk == "WAKUNING"){
            $resp = "TAGIHAN*WAKUNING*4570997106*11*20210224131241*H2H*1211002055*1211002055**88200*3000*".$idoutlet."*------**254278477*1**pdamkuningan*2053903080*00*SUKSES*0000000*00*1211002055*RT.B*TAGIHAN AIR*1***Momon Hendarman*JL. DESA BAYUNING I JL. DESA BAYUNING I***88200*PDAM KAB. KUNINGAN*01*2021*2506*2525*5000*83200*0*********************************** ";
        }elseif($kdproduk == "WAKPLMBNG"){
            $resp = "TAGIHAN*WAKPLMBNG*4570611953*9*20210224072955*H2H*012183*012183*012183*61600*2500*".$idoutlet."*------**13762922*1**2035*2053695496*00*EXT: APPROVE*0000000*RAHMAWATI*012183*012183*012183*01*202102*RAHMAWATI*RAHMAWATI*****PAM ATS PALEMBANG*02*2021***5000*56600*0***********************************    ";
        }elseif($kdproduk == "WABDGBAR"){
            $resp = "TAGIHAN*WABDGBAR*2811506650*9*20180810173253*H2H*0102000763***38500*2500*" . $idoutlet . "*" . $pin . "**100634630*1**pmgs*1083981179*00*Sukses*0000000*00*0102000763***1**R2*Achmad Zaini Miftah*Perum GPI Jl. Berlian No.45****PDAM Kab Bandung Barat*7*2018*694*705*0*38500*0***********************************    ";
        } else if($kdproduk == "WASAMPANG"){
            $resp = "TAGIHAN*WASAMPANG*2813306201*10*20180811144312*H2H*01003923*0102040126*01/II /004/0126/A*77968*5000*" . $idoutlet . "*" . $pin . "**-830994402*1**WASAMPANG*1084796924*00*SUKSES*0000000*00*01003923*0102040126*01/II /004/0126/A*2***CHUSNUL HOTIMAH*MUTIARA **0**PDAM TRUNOJOYO SAMPANG*6*2018*0*1*7088*35440*0*7*2018*0*1*0*35440*0**************************** ";
        } else if ($kdproduk == "WALOMBOKT") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WALOMBOKT*380860342*10*20150408102355*H2H*012100676***51650*2500*".$idoutlet."*" . $pin . "*------*845489*3**400311*303146002*00*SUCCESSFUL*0000000*0*012100676***1*102350---08042015*0000000050*SYAHNUN**201503*000000002500**PDAM Kab. Lombok Tengah*03*2015***0*51650************************************";
        } else if($kdproduk == "WABATAM"){
            $resp = "TAGIHAN*WABATAM*2803949068*9*20180807092452*H2H*52693***23180*2500*".$idoutlet."*".$pin."*------*9790710*1**2029*1080537185*00*EXT: APPROVE*0000000*OTORITA BATAM*52693*52693*52693*01**OTORITA BATAM*OTORITA BATAM*****PAM ATB BATAM*08*2018***0*23180*0***********************************   ";
        } else if($kdproduk == "WASUMED"){
            $resp = "TAGIHAN*WASUMED*2802786664*9*20180806175119*H2H*3104014051***73900*2500*".$idoutlet."*".$pin."*------*575470*2**400631*1079958533*00*SUCCESSFUL*1079958533*1*3104014051***1*175115---06082018*SWITCHERID*SUBANA**null---null---null*000000002500**PDAM Kab Sumedang*07*2018***0*73900*0***********************************    ";
        } else if ($kdproduk == "WAKBMN") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAKBMN*325080573*11*20150216181009*H2H*05014240008***27500*2000*".$idoutlet."*" . $pin . "*------*------*1**------*------*00*SUCCESSFUL*0000000*1*05014240008***1*181008---16022015*0000008001*IBU SAILAH**20151*000000002000**PDAM KEBUMEN*1*2015***0*27500************************************";
        } else if ($kdproduk == "WASLTIGA") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WASLTIGA*377721650*10*20150406065034*H2H*02b9289***24400*2000*" . $idoutlet . "*" . $pin . "*------*3581105*1**400321*302081945*00*SUCCESSFUL*0000000*0*02b9289***1*080638---06042015*0000008001*SUPENO**201503*000000002000**PDAM KOTA SALATIGA*03*2015***0*24400************************************";
        } else if ($kdproduk == "WAGROBGAN") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAGROBGAN*377613437*10*20150405214339*H2H*1201000301***000000027000*2000*" . $idoutlet . "*" . $pin . "*------*188829*1**1021033*302049814*00*SUCCESSFUL*0000000*0*1201000301***01*20150405043000007032**SRI HARTISAH SPD.***0000002000**PDAM KAB. GROBONGAN*3*2015*002286*002286     *0*000000027000************************************";
        } else if ($kdproduk == "WABANJAR") { // ID PELANGGAN
    //         2 BLN
            $resp = "TAGIHAN*WABANJAR*373045368*10*20150402080836*H2H*4027386***389380*4000*" . $idoutlet . "*" . $pin . "*------*618687*3**400231*300699846*00*SUCCESSFUL*0000000*0*4027386***2*080950---02042015*0000008001*DINA PUJIATI**201502,201503*000000004000**PDAM BANJARMASIN*02*2015***0*209310**03*2015***0*180070*****************************";
        } else if ($kdproduk == "WASRKT") { // ID PELANGGAN
    //         3 BLN
            $resp = "TAGIHAN*WASRKT*372701434*10*20150401200532*H2H*00046902***102400*5100*" . $idoutlet . "*" . $pin . "*------*135718*2**400251*300606023*00*SUCCESSFUL*0000000*1*00046902***3*200141---01042015*0000008001*Wahono**201503,201502,201501*000000005100**PDAM SURAKARTA*03*2015***0*32000**02*2015***3200*32000**01*2015***3200*32000**********************";
        } else if ($kdproduk == "WAPURWORE") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAPURWORE*373604198*10*20150402123006*H2H*01033200271***75800*1600*" . $idoutlet . "*" . $pin . "*------*91718*2**400211*300819084*00*SUCCESSFUL*0000000*0*01033200271***1*123006---02042015*0000008001*Pranoto Suwignyo**201503*000000001600**PDAM PURWOREJO*03*2015***0*75800************************************";
        } else if ($kdproduk == "WABYL") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WABYL*370983319*10*20150331154847*H2H*02120025***227350*2000*" . $idoutlet . "*" . $pin . "*------*825747*1**400081*300070270*00*SUCCESSFUL*0000000*0*02120025***1*154849---31032015*0000008001*MULYATMIN**201502*000000002000**PDAM BOYOLALI*02*2015***0*227350************************************";
        } else if ($kdproduk == "WAKABBDG") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAKABBDG*372227386*10*20150401143925*H2H*461195***88000*2000*" . $idoutlet . "*" . $pin . "*------*253188*1**400221*300458705*00*SUCCESSFUL*0000000*0*461195***1*143927---01042015*0000008001*TUNING RUDYATI**201504*000000002000**PDAM KAB. BANDUNG*04*2015***0*88000************************************";
        } else if ($kdproduk == "WAKNDL") { // ID PELANGGAN
    //         2 BLN
            $resp = "TAGIHAN*WAKNDL*372763951*10*20150401205554*H2H*0442060140***108200*3000*" . $idoutlet . "*" . $pin . "*------*1411122*3**400241*300624789*00*SUCCESSFUL*0000000*0*0442060140***2*205549---01042015*0000008001*Slamet Basuki**201502,201503*000000003000**PDAM KENDAL*02*2015***0*74200**03*2015***0*34000*****************************";
        } else if ($kdproduk == "WAWONOGIRI") { // ID PELANGGAN
    //         1 BLN
            $resp = "TAGIHAN*WAWONOGIRI*373652299*10*20150402130534*H2H*02040172***79000*2000*" . $idoutlet . "*" . $pin . "*------*98002*0**400141*300832572*00*SUCCESSFUL*0000000*1*02040172***1*130249---02042015*0000008001*DRS SUPARNO**201503*000000002000**PDAM KAB. WONOGIRI*03*2015***0*79000************************************";
        } else if ($kdproduk == "WAIBANJAR") { // ID PELANGGAN
    //         3 BLN
            $resp = "TAGIHAN*WAIBANJAR*370331499*10*20150331071909*H2H*390804***435740*6000*" . $idoutlet . "*" . $pin . "*------*9785488*3**400401*299891381*00*SUCCESSFUL*0000000*0*390804***3*071911---31032015*0000008001*H.JUMBRANI**201502,201501,201412*000000006000**PDAM INTAN BANJAR*02*2015***0*65160**01*2015***0*192660**12*2014***0*177920**********************";
        } else if ($kdproduk == "WAGIRIMM") { // ID PELANGGAN
    //         3 BLN
            $resp = "TAGIHAN*WAGIRIMM*371928757*10*20150401105546*H2H*02-07-07330*02-07-07330**135650*7500*" . $idoutlet . "*" . $pin . "*------*3334605*3**400381*300380441*00*SUCCESSFUL*0000000*1*02-07-07330*02-07-07330**3*115808---01042015*0000008001*RUJAI**201501,201502,201503*000000007500**PDAM GIRI MENANG MATARAM*01*2015***10000*56900**02*2015***10000*25700**03*2015***0*33050**********************";
        }
    }    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $params     = setMandatoryResponNew($frm, $ref1, "", "", $data);
    
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk($kdproduk, $frm);
    $adddata2   = tambahdataproduk2($kdproduk, $frm);
    $merge      = array_merge($params,$adddata,$adddata2);
    
    return json_encode($merge);
}

function paypdam($data) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $nominal    = strtoupper($data->nominal);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $ref3       = strtoupper($data->ref3);
    $field      = 11;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

 
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }
    
    // global $pgsql;
    global $host;

    $mti = "BAYAR";
    
    if($ref2 == ""){
        $ref2 = 0;
    }
    // $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan

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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN
    $fm         = convertFM($msg, "*");
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];


    if(substr(strtoupper($kdproduk), 0,2) != "WA"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk pdam";
    } else {

       if ($kdproduk == "WAKPLMBNG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKPLMBNG*4570612004*9*20210224073000*H2H*012183*012183*012183*61600*2500*".$idoutlet."*------**13700022*1**2035*2053695523*00*EXT: APPROVE*0000000*RAHMAWATI*012183*012183*012183*1*202102*RAHMAWATI*RAHMAWATI**201202102240537***PAM ATS PALEMBANG*02*2021***5000*56600*0***********************************    ";
        } else if ($kdproduk == "WALMPNG") { // ID PELANGGAN (NO SAMBUNGAN)
    // 2 BLN
            $resp = "BAYAR*WALMPNG*254364486*9*20141204105004*H2H*010501*010501*010501*111440*5600*" . $idoutlet . "*" . $pin . "*------*997079*0**2011*260186651*00*EXT: APPROVE*0000000*00*010501*010501*010501*2***BUSRON TOHA*****PDAM LAMPUNG*11*2014***0*62640*0*10*2014***5000*43800*0****************************";
        }else if ($kdproduk == "WAMAKASAR") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAMAKASAR*359960925*10*20150321075233*H2H*199200987*0*0*000000043320*2500*" . $idoutlet . "*" . $pin . "*------*169935*1**1021014*296702948*00*SUCCESSFUL*0000000*0*199200987***01*20150321043000000699*GEH001992009872*M. NATSIR***0000002500**PDAM KOTA MAKASAR*2*2015*00003512*00003521 *0*000000043320************************************";
        } else if($kdproduk == 'WAJMBG'){
            $resp = "BAYAR*WAJMBG*4585067444*10*20210307100911*H2H*0302022101***68200*2700*".$idoutlet."*------**176974564*1**WAJMBG*2062695688*00*SUKSES*0000000*00*0302022101***1***YS PANDIA,SH.*KOMBES DURYAT**0**PERUMDAM Tirta Kencana Jombang*02*2021*1636*1658*0*68200*0***********************************  ";
        }elseif($kdproduk=="WASDA"){
            $resp="BAYAR*WASDA*812314848*10*20160120170058*H2H*02004159***154100*3600*" . $idoutlet . "*------**-492264260*1**WASDA*439339830*00*SEDANG DIPROSES*0000000*00*02004159*02/I  /007/0147/2D*BA070147*2***SUKARNI*PERUM WISMA SARINADI III I-17**0**PDAM SIDOARJO*11*2015*0*18*7500*87300*0*12*2015*0*13*0*59300*0****************************";
        } else if($kdproduk == "WAKLUNGK"){
            $resp = "BAYAR*WAKLUNGK*121615166459784*10*20210308082101*H2H*16150*16150**18100*2000*".$idoutlet."*------**311318722*1**400601*2063317391*00*SUCCESSFUL*2063317391*1*16150*16150*RUMAH TANGGA A|4*1*082038---08032021*SWITCHERID*DRS.I KOMANG TUNAS*BTN TOJAN C/17*null---null---null*000000002000**PDAM KLUNGKUNG*02*2021*2954*2958*0*18100*0***********************************    ";
        }else if($kdproduk == "WAPONOR"){
            $resp = "BAYAR*WAPONOR*4586191544*10*20210308092109*H2H*1007020754***40400*2500*".$idoutlet."*------**179154254*1**400681*2063392128*00*SUCCESSFUL*2063392128*1*1007020754**RA|13*1*092100---08032021*SWITCHERID*DWI ANDRE SETIAWAN*Jl. JL. PERNIAGAAN BABADA*null---null---null*000000002500**PDAM KAB PONOROGO*02*2021*263*276*0*40400*0*********************************** ";
        }else if($kdproduk == "WABADUNG"){
            $resp = "BAYAR*WABADUNG*4586095659*10*20210308082856*H2H*070550026350***13773*2500*".$idoutlet."*------**163367326*1**400551*2063326617*00*SUCCESSFUL*0000000*400551*070550026350*D2*D2*1*082850---08032021---2063326617*666850*I DEWA AYU NYOMAN TRI EKA DEWI*AKASIA PARK JEPUN B/20**2500*16273*PDAM KAB BADUNG*02*2021*688*689*0*13773*0***********************************   ";
        }elseif ($kdproduk == "WAJMBR") {
            $resp = "BAYAR*WAJMBR*209510403*10*20141018100203*H2H*24035*24035**12500*2500*" . $idoutlet . "*" . $pin . "*------*-201635684*1**WAJMBR*244922439*00*SUKSES*0000000*00*24035*24035*24035*1***Aswanto*Perumh New Pesona AD-18*Cust Detail Info pindah ke bill_info70*0**PDAM JEMBER*09*2014*5150*5150*0*0*12500***************0********************";
        }  else if($kdproduk == "WAPROLING"){
            $resp = "BAYAR*WAPROLING*1359913423*10*20161122110154*H2H*04000977***51750*2500*".$idoutlet."*".$pin."**790177967*1**400171*616420045*00*SUCCESSFUL*0000000*0*04000977***1*110152---22112016*SWITCHERID*FARAH SUDARSIH*DS.PATOKAN*null---null---null*000000002500**PDAM PROBOLINGGO*10*2016***0*51750*0***********************************";
        }else if($kdproduk == 'WAGRESIK'){
            $resp = "BAYAR*WAGRESIK*121615171357942*10*20210308094240*H2H*9700418*9700418**19500*2500*".$idoutlet."*------**179017130*1**1329*2063418337*00*SUKSES*0000000*281*97-00418*9700418**1*094238---0308---2063418337*90042435394188*TURNANINGSIH*PALEM PERTIWI JB-09 BLOK.JB-09 RT.17 RW.8**2500*19500*PDAM Kab Gresik*02*2021* 435*435*0*19500*0*********************************** ";
        } else if($kdproduk == "WASITU"){
            $resp = "BAYAR*WASITU*1692774052*14*20170425163719*H2H*01/I /007/1659/B1*01/I /007/1659/B1*14001*107500*6000*".$idoutlet."*".$pin."*------*140359*1**WASITU*708529237*00*SUKSES*0000000*00*01/I /007/1659/B1*01/I /007/1659/B1*14001*3***SAMSUL HADI*JL. CEMPAKA PERUM ISMU H- 18 *#1:2017:5000:25000:0#2:2017:5000:0:0#3:2017:5000:0:0*25000**PDAM SITUBONDO*1*2017*0*5*0*22500*0*2*2017*0*10*0*22500*0*3*2017*0*10*40000*22500*0*********************";
        }else if($kdproduk == 'WAKOPASU'){
            $resp = "BAYAR*WAKOPASU*861464269*9*20160219132130*H2H*c1-03943*10**68310*2500*".$idoutlet."*".$pin."**1015645936*1**WAKOPASU*453311834*00*SUKSES*0000000*00*c1-03943*c1-03943*c1-03943*1***DUMAH*Jl. Maluku No.9  RT.3/VIII**0**PDAM KOTA PASURUAN*1*2016*1438*1458|24600|26559|10|10*0*66310*2000|0|2460|2951|5700|5000|***************0********************";
        } else if ($kdproduk == "WAMJK") {
            $resp = "BAYAR*WAMJK*247145969*10*20141126170840*H2H*0*0909040028*09.07.06.0336*122415*4000*" . $idoutlet . "*" . $pin . "*------*100695*1**WAMJK*257961468*00*SUKSES*0000000*00*0*0909040028*09.07.06.0336*2***SUKADI*SUKOANYAR-GONDANG *Cust Detail Info pindah ke bill_info70*0**PDAM KAB. MOJOKERTO (JATIM)*9*2014*0*19*11900*46000*0*10*2014*0*23*8415*56100*0****************************";
        }else if($kdproduk == "WATRENGG"){
            $resp = "BAYAR*WATRENGG*111615168240315*10*20210308085053*H2H*01503000000389**6282302308474*30000*2000*".$idoutlet."*------**-1759546791*1**WATRENGG*2063351514*00*SUKSES*0000000*00**01503000000389*000389*1***YUSPITA ARDYANTI /APBN'18*Jl. GRIYA DAMAI BUKIT ASRI G 21 **0**PDAM TRENGGALEK*2*2021*0*0*0*30000*0*********************************** ";
        }else if($kdproduk == "WASUMENEP"){
            $resp = "BAYAR*WASUMENEP*4586189640*9*20210308092013*H2H*0106319***74200*4000*".$idoutlet."*------*------*450705*1**WASUMENEP*2063390800*00*SUKSES*0000000*00*0106319*A06319/09/RA*A06319*2***ABD. RAHMAN/FAD*SELAMET RIYADI 29**0**PDAM SUMENEP*1*2021*97*97*11000*31600*0*2*2021*97*97*0*31600*0****************************";
        }else if($kdproduk == "WALMJNG"){
            $resp = "BAYAR*WALMJNG*121614560974845*9*20210301080937*H2H*01270147*01270147*01270147*83500*2500*".$idoutlet."*------**17351703*1**2046*2057255238*00*EXT: APPROVE*0000000*AHMAD AL JUFRI*01270147*01270147*01270147*1**AHMAD AL JUFRI*AHMAD AL JUFRI**213180937001***PDAM LUMAJANG*01*2021***5000*78500*0***********************************   ";
        }elseif ($kdproduk == "WABJN") {// ID PELANGGAN DAN NO SAMBUNGAN
            $resp = "BAYAR*WABJN*186513*10*20141124115558*H2H*0*0111002*0*195500*4000*" . $idoutlet . "*" . $pin . "*------*499969100*1**WABJN*237998914*00**0000000*00*0*0111002*0*2***EKO SUDARMANTO*Jl. VETERAN 0 0*Cust Detail Info pindah ke bill_info70*0**PDAM BOJONEGORO*9*2014*0*0*0*0*84000*10*2014*0*10*0*27500*84000****************************";
        }  else if($kdproduk == "WABWANGI"){
            $resp = "BAYAR*WABWANGI*4576411754*11*20210301070603*H2H*01014948***140200*5000*".$idoutlet."*------**3331728*1**WABWANGI*2057190137*00*SUKSES*0000000*00*01014948*R2*01/B/36/014948/R2*2***SAMSURI ABAS*GRIYA GIRI MULYA W/47****PUDAM BANYUWANGI*1*2021*3808*3828*10000*51300*0|10000|295522*2*2021*3830*3852*0*58900*0|10000|562532****************************   ";
        }else if($kdproduk == "WAKOPROB"){
            $resp = "BAYAR*WAKOPROB*121614515722194*10*20210228193522*H2H*014375*014375**55880*2500*".$idoutlet."*------**532134531*1**WAKOPROB*2056906898*00*EXT: Pembayaran berhasil dilakukan*0000000*00*014375*014375**1*C#=#RUMAH TANGGA A**P ICAP*PATIMURA/KAV KTI***55880*PDAM KOTA PROBOLINGGO*01*2021*1802*1823*5080*43800*3500|3500|0|0|0|0***********************************  ";
        } else if($kdproduk == "WAPASU"){
            $resp = "BAYAR*WAPASU*2886750412*9*20180916171720*H2H*03050228*228**74540*2500*".$idoutlet."*".$pin."**2396717100*1**PASURUAN*1118856295*00*SUCCESS*0000000*00*03050228*228**1*03**MISLAN*BULU RT/W. 02/01 BULUSARI****PDAM KAB. PASURUAN*08*2018*5596*5618*0*74540*0****************************193******* ";
        }else if ($kdproduk == "WAKOKEDIRI") { // ID PELANGGAN
             $resp = "BAYAR*WAKOKEDIRI*121614562198116*10*20210301082959*H2H*18813*18813**31500*2500*".$idoutlet."*------**389187517*1**WAKOKEDIRI*2057278816*00*SUCCESS*0000000*00*18813*18813**1*R2#=#R2**SUSINTA SEFIYANTI*PERUM GRIYA BANARAN INDAH A - 10***31500*PDAM KOTA KEDIRI*02*2021*690*700*0*23000*3500|5000|0|0|0|0***********************************  ";
        }else if ($kdproduk == "WAMAGETAN") { // ID PELANGGAN
             $resp = "BAYAR*WAMAGETAN*4574505035*9*20210227143532*H2H*41000004***150500*2000*".$idoutlet."*------**220295070*1**WAMAGETAN*2055995224*00*SUKSES*0000000*00*41000004*D1-00004*2A|C14*2***MUCH SUROJO*DS.TULUNG,BANJENG ***150500*PDAM MAGETAN*12*2020*445*466*0*55500*12500-Denda Keterlambatan*1*2021*466*493*0*82500***************************** ";
        } else if ($kdproduk == "WABONDO") { // ID PELANGGAN
             $resp = "BAYAR*WABONDO*250811076*10*20141130205945*H2H*09000879*09/01/001/00879/RB*0*94130*4500*" . $idoutlet . "*" . $pin . "*------*5321738*1**FY834n7Vs4mdASP4H34n*259098168*00*EXT: PAYMENT SUKSES.*0000000*00*09000879*09/01/001/00879/RB**3***DWI YULIANA*PONCOGATI RT 11/5*Cust Detail Info pindah ke bill_info70*0**PDAM BONDOWOSO*8*2014*0*5*15000*9400*0|16150*9*2014*0*4*5000*7520*0|16150*10*2014*0*2*5000*3760*0|16150*********************";
        }  else if ($kdproduk == "WAPAMES") { // ID PELANGGAN
            $resp = "BAYAR*WAPAMES*4575555003*10*20210228131453*H2H*02052239***64450*3000*".$idoutlet."*------**175844277*1**WAPAMES*2056611048*00*EXT: Pembayaran berhasil dilakukan*0000000*00*02052239***1*A2#=#A2**SUPANDI*DS. PADELEGAN***64450*PDAM KAB PAMEKASAN*01*2021*595*610*5000*56950*0|2500.00|0|0|0.00|0***********************************    ";
        } else if ($kdproduk == "WABGK") { // ID PELANGGAN
            $resp = "BAYAR*WABGK*273941724*10*20141223052918*H2H*0*0101001861*01-1-00186A*366275*6000*" . $idoutlet . "*" . $pin . "*------*1443172*0**WABGK*267026682*00*SUKSES*0000000*00*0*0101001861*01-1-00186A*4***NURJANNAH*KH. MARZUQI *Cust Detail Info pindah ke bill_info70*0**PDAM BANGKALAN*8*2014*0*25*12600*84000*0*9*2014*0*25*12600*84000*0*10*2014*0*19*9435*62900*0*11*2014*0*26*13140*87600*0**************";
        }else if ($kdproduk == "WANGAWI") { // ID PELANGGAN
            $resp = "BAYAR*WANGAWI*121614553732789*10*20210301060853*H2H*0301011493***36310*2000*".$idoutlet."*------**334835032*1**WANGAWI*2057151388*00*SUKSES*0000000*00**0301011493*C/A/01/1493*1***YATIN *JOGOROGO **0**PDAM NGAWI*2*2021*0*16*0*36310*0***********************************  ";
        }  else if ($kdproduk == "WATAGUNG") { // ID PELANGGAN
            $resp = "BAYAR*WATAGUNG*121614557167710*10*20210301070608*H2H*01108030000052***31000*2000*".$idoutlet."*------**53273227*1**WATAGUNG*2057190332*00*SUKSES*0000000*00**01108030000052*31219*1***YUNANIK*JL. ABDUL FATAH III**0**PDAM TULUNGAGUNG*2*2021*0*1*0*31000*0***********************************  ";
        } else if ($kdproduk == "WAKABMLGNA") { // ID PELANGGAN
            $resp = "BAYAR*WAKABMLGNA*121614171317898*11*20210224195518*H2H*13040003737***36850*3000*".$idoutlet."*------**123572565*1**WAKABMLGNA*2054158517*00*SUCCESS*0000000*00*13040003737*RAJI*RUMAH TANGGA A3*1**HH13483*RAJI*PURI RAYA KAV B3**2500**Perumda TirtaKanjuruhan*02*2021*37*27*3350*33500*0*****0******************************   ";
        } else if ($kdproduk == "WAKABMLG") { // ID PELANGGAN
            $resp = "BAYAR*WAKABMLG*406632*10*20150401153632*H2H*8101120001982***000000025000*1800*" . $idoutlet . "*" . $pin . "**318607707*0**1061032*238016367*00*SUCCESSFUL*0000000*0*8101120001982***01*20150401043000011022*1A5FD4B1F60D4A6D9FC0000000000000*YUGUS***0000002100**PDAM KAB. MALANG*4*2015*0000001816 * 0000001826*0*000000025000************************************";
        } else if ($kdproduk == "WAKABSMG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKABSMG*372895718*10*20150402041844*H2H*310050799*0*0*23140*2000*" . $idoutlet . "*" . $pin . "*------*22805*1**400201*300654369*00*SUCCESSFUL*0000000*0*310050799***1*041519---02042015*0000008001*BAGUS SETIAWAN*LOSARI SAWAHAN NO.51 RT*201504*000000002000**PDAM KAB. SEMARANG*04*2015***0*23140************************************";
        } else if ($kdproduk == "WASLMN") { // ID PELANGGAN
            $resp = "BAYAR*WASLMN*327274552*10*20150218183916*H2H*1400669***60000*2500*" . $idoutlet . "*" . $pin . "**-228275716*0**400071*284869801*00*SUCCESSFUL*0000000*0*1400669***1*183903---18022015*0000008001*NADI KUSNADI*JL.ASTER 333*201501*000000001700**PDAM SLEMAN*01*2015*3724*3744*0*60000************************************";
        }else if ($kdproduk == "WAMAGLG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAMAGLG*4570122657*9*20210223171947*H2H*0802110046***115380*7500*".$idoutlet."*------**963042653*1**400151*2053420262*00*SUCCESSFUL*0000000*400151*0802110046*IIC*IIC*3*171850---23022021---2053420262*SWITCHERID*Ready Dwi Darmayandi*Tanjung Baru Rt. 05/02, K**7500*122880*PDAM KAB MAGELANG*11*2020*0*12*6600*33080*0*12*2020*12*24*6600*33080*0*01*2021*24*32*6000*30020*0*********************   ";
        } else if ($kdproduk == "WAREMBANG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAREMBANG*347346523*10*20150310155253*H2H*LA-03-00012***530000*4000*" . $idoutlet . "*" . $pin . "*------*0*2**400301*291968179*00*SUCCESSFUL*0000000*0*LA-03-00012***2*155255---10032015*0000008001*R A M I S I H**201502,201501*000000004000**PDAM Kab. Rembang*02*2015***0*269400**01*2015***0*260600*****************************";
        }else if ($kdproduk == "WABLORA") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WABLORA*4572210923*10*20210225144447*H2H*202020248***250300*2500*".$idoutlet."*------**6042013*1**WABLORA*2054582839*00*Sukses*0000000*00*202020248***1**2021010000016141*SATRIYONO*SEMANGAT****PDAM KAB. BLORA*01*2021*4113*4154*10000*240300************************************    ";
        }else if ($kdproduk == "WAKARANGA") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKARANGA*373054738*10*20150402081140*H2H*0702011372*0*0*28000*2000*" . $idoutlet . "*" . $pin . "*------*2440332*2**400121*300701041*00*SUCCESSFUL*0000000*0*0702011372***1*081136---02042015*0000008001*SENEN*BUNGKUS 10/03 JATIROYO*201503*000000002000**PDAM Karanganyar*03*2015***0*28000************************************";
        }else if ($kdproduk == "WAKPKLNGAN") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKPKLNGAN*371624754*10*20150401070700*H2H*0104010422*0*0*70550*2000*" . $idoutlet . "*" . $pin . "*------*609924*3**400101*300280228*00*SUCCESSFUL*0000000*0*0104010422***1*070654---01042015*0000008001*Moh. Abdullah*Perum Puri Puri raya Bl*201503*000000002000**PDAM KAB. PEKALONGAN*03*2015***0*70550************************************";
        } else if ($kdproduk == "WAKABSMG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKABSMG*372895718*10*20150402041844*H2H*310050799*0*0*23140*2000*" . $idoutlet . "*" . $pin . "*------*22805*1**400201*300654369*00*SUCCESSFUL*0000000*0*310050799***1*041519---02042015*0000008001*BAGUS SETIAWAN*LOSARI SAWAHAN NO.51 RT*201504*000000002000**PDAM KAB. SEMARANG*04*2015***0*23140************************************";
        }else if ($kdproduk == "WABATANG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WABATANG*4571202021*10*20210224162654*H2H*0880020197***60750*2500*".$idoutlet."*------**25788618*1**WABATANG*2054013559*00*Sukses*0000000*00*0880020197*0880020197*0880020197*1**DPHA210224BS*HARMINTO*DS/SUMUR BANGER | R2 (Rumah Tangga 2)*eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZHBlbCI6IjA4ODAwMjAxOTciLCJpYXQiOjE2MTQxNTg4MTIsImV4cCI6MTYxNDI0NTIxMn0.gNgK74vnkZD2Dmc__xGRS8yQENmzStZG7YubvH10Opc#[{'no_materai':'-','subsidi':'0','met_l':'2039','met_k':'2055','periode':'202101','materai':'0','denda':'3500','harga_air':'57250','pakai':'16','jumlah':'60750'}]*0**PDAM KAB. BATANG (JATENG)*01*2021*2039*2055*3500*60750*16|57250|0|0|-0***********************************  ";
        } else if ($kdproduk == "WASKHJ") { // ID PELANGGAN
    //        // 1 BLN
              $resp = "BAYAR*WASKHJ*121614168097689*10*20210224190142*H2H*0101870137***62100*2000*".$idoutlet."*------**33666695*1**400511*2054119674*00*SUCCESSFUL*2054119674*1*0101870137**RT3|12*1*190135---24022021*SWITCHERID*Sardi*Ngentak RT 03 RW 05, Bulakre*null---null---null*000000002000**PDAM KAB. SUKOHARJO*01*2021*339*351*0*62100*0***********************************  ";
        } else if ($kdproduk == "WACLCP") { // ID PELANGGAN
    //        // 1 BLN
              $resp = "BAYAR*WACLCP*340812584*10*20150304160904*H2H*0105041625***000000269600*6000*" . $idoutlet . "*" . $pin . "**11150000*1**1021012*289596702*00*SUCCESSFUL*0000000*0*0105041625***03*20150304043000017000*CLP13H3G849RF41*ETI WIDIASTUTI***0000006000**PDAM CILACAP*12*2014*00002694*0*0*0**1*2015*0*0*0*0**2*2015*0*00002762 *0*000000269600**********************";
        }else if ($kdproduk == "WABYMS") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WABYMS*379541126*10*20150407104235*H2H*0124619*0*0*31540*2000*" . $idoutlet . "*" . $pin . "*------*4889172*1**400011*302720844*00*SUCCESSFUL*0000000*0*0124619***1*105514---07042015*0000008001*NANI WIBOWO*JL. JATIWINANGUN GG.SEM*MAR15*000000002000**PDAM BANYUMAS*3*2015***0*31540************************************";
        }else if($kdproduk == 'WAKPROGO'){
            $resp = "BAYAR*WAKPROGO*4571937847*10*20210225102801*H2H*071500424***85000*2000*".$idoutlet."*------**2622582*1**2051*2054423493*00*SUKSES*0000000*00*071500424***1***Eko Sumarno*Kalidengen**0**PDAM KULON PROGO*01*2021*2004*2024*5000*80000*0***********************************  ";
        }else if($kdproduk == 'WATEGAL'){
            $resp = "BAYAR*WATEGAL*4571999987*10*20210225112058*H2H*0106018468***45500*2500*".$idoutlet."*------**91505333*1**1332*2054459509*00*SUKSES*0000000*281*0106018468***1*112058---0225---2054459509*110020183254682*HABIBI*DS.PESAYANGAN RT 05/01 TALANG (MBR)**2500*45500*PDAM KAB TEGAL*01*2021* 182*183*0*45500*0***********************************    ";
        } else if($kdproduk == 'WAGKIDUL'){
            $resp = "BAYAR*WAGKIDUL*4571946378*9*20210225103441*H2H*010300218***75200*2500*".$idoutlet."*------**31044984*1**WAGKIDUL*2054428513*00*SUKSES*0000000*00*010300218***1***TALIP*SENENG**0**PDAM GUNUNG KIDUL*01*2021*2046*2061*5000*70200*0****************************193******* ";
        } else if($kdproduk == 'WACIAMIS'){
            $resp = "BAYAR*WACIAMIS*2780129865*10*20180726104636*H2H*04030020147**6285324800289*81400*2500*".$idoutlet."*".$pin."**-53342216*1**400621*1069446941*00*SUCCESSFUL*1069446941*1*04030020147**R2 / Rumah Tang|17*1*104606---26072018*SWITCHERID*AMY MARYA, SE*PERUM B REGENCY 7 C.7*null---null---null*000000002500**PDAM CIAMIS*06*2018*574*591*0*81400*0***********************************    ";
        } else if($kdproduk == 'WAKABTRA'){
            $resp = "BAYAR*WAKABTRA*4572023969*10*20210225114255*H2H*C040041***30000*2500*".$idoutlet."*------**15796806*1**1000*2054473188*00*SUKSES*FMT116315902253**C040041***01*#0*R2#163159*ATUT SUPARTA*Kp.Cikareo 005 02*#25000***PDAM KAB TANGERANG*02*2021*114*114*00005000*25000*0***********************************  ";
        } else if ($kdproduk == "WAMAJALENG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAMAJALENG*4570693937*10*20210224084919*H2H*1003002283***36070*2500*".$idoutlet."*------**48828016*1**1356*2053740434*00*SUKSES*0000000*281*1003002283***1*084918---0224---2053740434*870490171728324*RATMA \/MBR 2020*PANYINGKIRAN BLOK JUMAT RT 005 RW 0**2500*36070*PDAM MAJALENGKA*01*2021* 13*17*5000*31070*0*********************************** ";
        } else if ($kdproduk == "WABOGOR") { // ID PELANGGAN
    // 1 BLN
            $resp = "BAYAR*WABOGOR*303308785*10*20150123095039*H2H*07411152*0*0*000000078540*2500*" . $idoutlet . "*" . $pin . "*------*2173894*1**1021030*276888151*00*SUCCESSFUL*0000000*0*07411152***01*20150123043000003809*BGR13H3G849RF11*AAS RAMAESIH**Cust Detail Info pindah ke bill_info70*0000002500**PDAM KAB. BOGOR*12*2014*00000711*00000727 *0*000000078540************************************";
        } else if ($kdproduk == "WADEPOK") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WADEPOK*653113900*10*20151012114012*H2H*02440121*0*0*000000197900*2500*" . $idoutlet . "*" . $pin . "*------*142611*1**1141062*391075500*00*SUCCESSFUL*0000000*0*02440121***343011*20151012043000011048*20151012113910000000000000026359*PT. PRIMAMAS PERKASA***0000002500**PDAM KOTA DEPOK (JABAR)*9*2015*441*473*0*000000197900************************************";
        }else if ($kdproduk == "WAJBRBNJR") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAJBRBNJR*4571591920*10*20210224220213*H2H*1321400655***45000*2500*".$idoutlet."*------**49162312*1**400111*2054222151*00*SUCCESSFUL*2054222151*1*1321400655**D2|0*1*220205---24022021*SWITCHERID*PUDJI SUHARTONO*GRIYA BANJAR RAHARJA D2 23*null---null---null*000000002500**PDAM KOTA BANJAR*01*2021*1132*1132*0*45000*0*********************************** ";
        }else if ($kdproduk == "WAKOSKBUMI") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKOSKBUMI*4570144890*9*20210223173644*H2H*01060010622***70620*2500*".$idoutlet."*------*------*826259*1**tirtabumi*2053433529*00*SUCCESS*0000000*00*01060010622***1**11*Jai Sutisna *Rt.20/IV****PDAM KOTA SUKABUMI*01*2021*1590 * 1598**70620*0***********************************";
        }else if ($kdproduk == "WAMEDAN") { // ID PELANGGAN
    //        // 1 BLN
             $resp = "BAYAR*WAMEDAN*1644378718*10*20170404123901*H2H*0117080017***88000*7500*". $idoutlet ."*". $pin ."**145637240*1**1002*694616478*00*SUCCESSFUL*0000000*00*0117080017***03*#0#0#0*N.3#138081*SYAIFUL HALIM*PEMUDA BARU III 12*#10800.00#18600.00#18600.00***PDAM KOTA MEDAN (SUMUT)*02*2017*46000*47000*00020000*10800*0*03*2017*47000*49000*00020000*18600*0*04*2017*49000*51000*00000000*18600*0*********************   ";
        }else if ($kdproduk == "WABEKASI") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WABEKASI*4570925155*10*20210224120456*H2H*010404007001***140800*2500*".$idoutlet."*------**253474370*1**1142*2053864192*00*SUKSES*FMT108458502246*932102241109*010404007001***01*#1*#084585*DARSONO*KP. RAWA PASUNG*#130800***PDAM KOTA BEKASI*01*2021*689*707*00010000*130800*0***********************************    ";
        }else if ($kdproduk == "WAGARUT") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAGARUT*4570983026*10*20210224125953*H2H*049277***84960*3000*".$idoutlet."*------**12731423*1**1135*2053895435*00*SUKSES*FMT108860002248*6035eafef2d25-202101010007298*049277***01*#0*21#088600*IMAS SUMIATI*JLN.SUMBERSARI 2/18*#70800***PDAM Garut*01*2021*1571*1590*00014160*70800*0***********************************    ";
        }else if ($kdproduk == "WACIREBON") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WACIREBON*4570820004*10*20210224103413*H2H*0113004021***794550*7500*".$idoutlet."*------**56397336*1**1346*2053808836*00*SUKSES*0000000*281*0113004021***3*103413---0224---2053808836*202011000000310*T A R K A M*SENDE Blok 004**7500*794550*PDAM KAB. CIREBON*11*2020* 5274*5319*5000*305650*0*12*2020* 5319*5364*5000*305650*0*01*2021* 5364*5389*5000*168250*0*********************    ";
        }else if ($kdproduk == "WAKUNING") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKUNING*4570997133*11*20210224131243*H2H*1211002055*1211002055**88200*3000*".$idoutlet."*------**254188777*1**pdamkuningan*2053903097*00*SUKSES*0000000*00*1211002055*RT.B*TAGIHAN AIR*1*9236b8007742d3374bd41a4eecd40122*9236b8007742d3374bd41a4eecd40122*Momon Hendarman*JL. DESA BAYUNING I JL. DESA BAYUNING I***88200*PDAM KAB. KUNINGAN*01*2021*2506*2525*5000*83200*0***********************************   ";
        }else if ($kdproduk == "WAPLMBNG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAPLMBNG*1493617364*10*20170125070453*H2H*7B085002500018*7B085002500018*7B085002500018*100222*3000*" . $idoutlet . "*" . $pin . "**1088953*1**2009*653008063*00*EXT: APPROVE*0000000*00*7B0850250018*7B085002500018*7B085002500018*2***ELMA NILYANA*****PDAM PALEMBANG*12*2016***0*52497*0*01*2017***0*47725*0****************************   ";
        }else if ($kdproduk == "WALOMBOKT") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WALOMBOKT*380860767*10*20150408102407*H2H*012100676*0*0*51650*2500*".$idoutlet."*" . $pin . "*------*791339*3**400311*303146152*00*SUCCESSFUL*0000000*0*012100676***1*102350---08042015*0000000050*SYAHNUN*LENENG*201503*000000002500**PDAM Kab. Lombok Tengah*03*2015***0*51650************************************";
        } else if ($kdproduk == "WAKBMN") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKBMN*325088253*11*20150216181702*H2H*05014240008*05014240008**27500*2000*".$idoutlet."*" . $pin . "*------*------*1**------*------*00*SUCCESSFUL*0000000*1*05014240008***1*181701---16022015*0000008001*IBU SAILAH*Ds.Demangsari RW III*20151*000000002000**PDAM KEBUMEN*1*2015*2652*2658*0*27500************************************";
        } else if ($kdproduk == "WAPBLINGGA") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAPBLINGGA*375520225*10*20150404063115*H2H*14050151*0*0*103260*2000*" . $idoutlet . "*" . $pin . "*------*1388464*3**400271*301406507*00*SUCCESSFUL*0000000*0*14050151***1*063227---04042015*0000008001*MAS'UT NUR H.*JL.MAWAR  RT.4/1*201503*000000002250**PDAM PURBALINGGA*03*2015***0*103260************************************";
        } else if ($kdproduk == "WASLTIGA") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WASLTIGA*377721740*10*20150406065044*H2H*02b9289*0*0*24400*2000*" . $idoutlet . "*" . $pin . "*------*3554705*1**400321*302081978*00*SUCCESSFUL*0000000*0*02b9289***1*080647---06042015*0000008001*SUPENO*KLASEMAN 04/II*201503*000000002000**PDAM KOTA SALATIGA*03*2015***0*24400************************************";
        } else if($kdproduk == "WASUMED"){
            $resp = "BAYAR*WASUMED*2802786944*9*20180806175127*H2H*3104014051***73900*2500*".$idoutlet."*".$pin."*------*499070*2**400631*1079958691*00*SUCCESSFUL*1079958691*1*3104014051**RT.C|0*1*175115---06082018*SWITCHERID*SUBANA*Marga citra MARGA CINTA*null---null---null*000000002500**PDAM Kab Sumedang*07*2018*0*0*0*73900*0***********************************   ";
        } else if($kdproduk == "WABDGBAR"){
            $resp = "BAYAR*WABDGBAR*2811507582*9*20180810173314*H2H*0102000763***38500*2500*".$idoutlet."*".$pin."**100494915*1**pmgs*1083981615*00*SUCCESS*0000000*00*0102000763***1**R2*Achmad Zaini Miftah*Perum GPI Jl. Berlian No.45****PDAM Kab Bandung Barat*7*2018*694*705*0*38500*0*********************************** ";
        }else if($kdproduk == 'WASAMPANG'){
            $resp = "BAYAR*WASAMPANG*2813306875*10*20180811144337*H2H**0102040126**77968*5000*".$idoutlet."*".$pin."**-831727670*1**WASAMPANG*1084797180*00*SUKSES*0000000*00*01003923*0102040126*01/II /004/0126/A*2***CHUSNUL HOTIMAH*MUTIARA **0**PDAM TRUNOJOYO SAMPANG*6*2018*0*1*7088*35440*0*7*2018*0*1*0*35440*0****************************    ";
        } else if ($kdproduk == "WAGROBGAN") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAGROBGAN*377432718*10*20150405185154*H2H*0702000552*0*0*000000027000*2000*" . $idoutlet . "*" . $pin . "*------*188829*1**1021033*301993551*00*SUCCESSFUL*0000000*0*0702000552***01*20150405043000005081*G02700005521505*LULUT SURYANTO***0000002000**PDAM KAB. GROBONGAN*3*2015*000364*000374     *0*000000027000************************************";
        } else if ($kdproduk == "WABANJAR") { // ID PELANGGAN
    //        // 2 BLN
            $resp = "BAYAR*WABANJAR*373048180*10*20150402080936*H2H*4027386*0*0*389380*4000*" . $idoutlet . "*" . $pin . "*------*225307*3**400231*300700216*00*SUCCESSFUL*0000000*0*4027386***2*081049---02042015*0000008001*DINA PUJIATI*Jl.Panglima Batur Gg.Qa*201502*000000002000**PDAM BANJARMASIN*02*2015***0*209310**03*2015***0*180070*****************************";
        } else if ($kdproduk == "WASRKT") { // ID PELANGGAN
    //        // 3 BLN
            $resp = "BAYAR*WASRKT*372702270*15*20150401200643*H2H*00046902*0*0*102400*5100*" . $idoutlet . "*" . $pin . "*------*28218*2**400251*300606207*00*SUCCESSFUL*0000000*1*00046902***3*200208---01042015*0000008001*Wahono*Semanggi        RT 03/2*201501*000000001700**PDAM SURAKARTA*03*2015***0*32000**02*2015***3200*32000**01*2015***3200*32000**********************";
        } else if ($kdproduk == "WAPURWORE") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAPURWORE*373604483*10*20150402123019*H2H*01033200271*0*0*75800*1600*" . $idoutlet . "*" . $pin . "*------*14318*2**400211*300819168*00*SUCCESSFUL*0000000*0*01033200271***1*123019---02042015*0000008001*Pranoto Suwignyo*Jend A Yani*201503*000000001600**PDAM PURWOREJO*03*2015***0*75800************************************";
        } else if ($kdproduk == "WABYL") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WABYL*370983612*10*20150331154900*H2H*02120025*0*0*227350*2000*" . $idoutlet . "*" . $pin . "*------*596397*1**400081*300070366*00*SUCCESSFUL*0000000*0*02120025***1*154901---31032015*0000008001*MULYATMIN*Pisang, Susilohardjo*201502*000000002000**PDAM BOYOLALI*02*2015***0*227350************************************";
        } else if ($kdproduk == "WAKABBDG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAKABBDG*372228006*10*20150401143954*H2H*461195*0*0*88000*2000*" . $idoutlet . "*" . $pin . "*------*163188*1**400221*300458844*00*SUCCESSFUL*0000000*0*461195***1*143956---01042015*0000008001*TUNING RUDYATI*PESONA BALI RESIDENCE.*201504*000000002000**PDAM KAB. BANDUNG*04*2015***0*88000************************************";
        } else if ($kdproduk == "WAKNDL") { // ID PELANGGAN
    //        // 2 BLN
            $resp = "BAYAR*WAKNDL*372764350*10*20150401205616*H2H*0442060140*0*0*108200*3000*" . $idoutlet . "*" . $pin . "*------*1299922*3**400241*300624928*00*SUCCESSFUL*0000000*0*0442060140***2*205611---01042015*0000008001*Slamet Basuki*Babadan Rt 2/6*201502*000000001500**PDAM KENDAL*02*2015***0*74200**03*2015***0*34000*****************************";
        } else if ($kdproduk == "WAWONOGIRI") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAWONOGIRI*373653668*10*20150402130635*H2H*02040172*02040172**79000*2000*" . $idoutlet . "*" . $pin . "*------*98002*0**400141*300832927*00*SUCCESSFUL*0000000*1*02040172***1*130350---02042015*0000008001*DRS SUPARNO*SINGODUTAN 2/1*201503*000000002000**PDAM KAB. WONOGIRI*03*2015***0*79000************************************";
        } else if ($kdproduk == "WAIBANJAR") { // ID PELANGGAN
    //        // 3 BLN
            $resp = "BAYAR*WAIBANJAR*370332504*10*20150331072101*H2H*390804*0*0*435740*6000*" . $idoutlet . "*" . $pin . "*------*9343748*3**400401*299891744*00*SUCCESSFUL*0000000*0*390804***3*072103---31032015*0000008001*H.JUMBRANI*JL.KELURAHAN GG.KRUING*201412*000000002000**PDAM INTAN BANJAR*02*2015***0*65160**01*2015***0*192660**12*2014***0*177920**********************";
        } else if ($kdproduk == "WAGIRIMM") { // ID PELANGGAN
    //        // 3 BLN
            $resp = "BAYAR*WAGIRIMM*371929746*10*20150401105631*H2H*02-07-07330*02-07-07330*0*135650*7500*" . $idoutlet . "*" . $pin . "*------*3191455*3**400381*300380730*00*SUCCESSFUL*0000000*1*02-07-07330*02-07-07330**3*115853---01042015*0000008001*RUJAI*SEKARBELA*201501*000000002500**PDAM GIRI MENANG MATARAM*01*2015***10000*56900**02*2015***10000*25700**03*2015***0*33050**********************";
        } else if ($kdproduk == "WABULELENG") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WABULELENG*373521181*10*20150402112943*H2H*02001775*0*0*37600*2000*" . $idoutlet . "*" . $pin . "*------*12674*1**400371*300795390*00*SUCCESSFUL*0000000*0*02001775***1*112835---02042015*0000437105*KETUT SUKANARA*ANTURAN GG MAWAR*201503*000000002500**PDAM KAB. BULELENG*03*2015***0*37600************************************";
        } else if ($kdproduk == "WABREBES") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WABREBES*373004003*10*20150402075243*H2H*1601040330*0*0*47000*2500*" . $idoutlet . "*" . $pin . "*------*2548522*3**400341*300693502*00*SUCCESSFUL*0000000*0*1601040330***1*075238---02042015*0000008001*Hersodo*JL. Kol. Sugiono RT.03*201503*000000002500**PDAM KAB. BREBES*03*2015***0*47000************************************";
        } else if ($kdproduk == "WAWONOSB") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAWONOSB*373041923*10*20150402080716*H2H*0114120063*0*0*62020*2000*" . $idoutlet . "*" . $pin . "*------*415231*1**400331*300699347*00*SUCCESSFUL*0000000*0*0114120063***1*080718---02042015*0000008001*MOCH NASIR SUNYOTO*PUNTUK*201503*000000002000**PDAM KAB. WONOSOBO*03*2015***0*62020************************************";
        } else if ($kdproduk == "WAMADIUN") { // ID PELANGGAN
    //        // 1 BLN
            $resp = "BAYAR*WAMADIUN*371957500*10*20150401111518*H2H*0208050150*0*0*79580*2500*" . $idoutlet . "*" . $pin . "*------*1447273*3**400261*300388430*00*SUCCESSFUL*0000000*0*0208050150***1*111632---01042015*0000008001*SLAMET AS*GULUN GG II RT 49 RW 15*201503*000000002500**PDAM KOTA MADIUN*03*2015***0*79580************************************";
        } else if ($kdproduk == "WASRAGEN") { // ID PELANGGAN
    //        // 2 BLN
            $resp = "BAYAR*WASRAGEN*371905769*10*20150401104009*H2H*0800564*0*0*87000*3400*" . $idoutlet . "*" . $pin . "*------*136821*2**400181*300373739*00*SUCCESSFUL*0000000*0*0800564***2*093118---01042015*0000008001*WARTINAH A*KADIPIRO*201502*000000001700**PDAM KAB. SRAGEN*03*2015***0*31250**02*2015***0*55750*****************************";
        } 
    }    

    if($resp == 'null'){
        $params = array(
            "KODE_PRODUK"       => (string) $kdproduk,
            "WAKTU"             => date('YmdHis'),
            "IDPEL1"            => (string) $idpel1 ,
            "IDPEL2"            => (string) $idpel2 ,
            "IDPEL3"            => (string) $idpel3 ,
            "NAMA_PELANGGAN"    => (string) '' ,
            "PERIODE"           => (string) '' ,
            "NOMINAL"           => (string) '' ,
            "ADMIN"             => (string) '' ,
            "UID"               => (string) $idoutlet ,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1 ,
            "REF2"              => (string) '' ,
            "REF3"              => (string) '' ,
            "STATUS"            => (string) '00' ,
            "KET"               => (string) 'SEDANG DIPROSES' ,
            "SALDO_TERPOTONG"   => (string) '' ,
            "SISA_SALDO"        => (string) '' ,
            "URL_STRUK"         => (string) '' ,
        );
        writeLog($GLOBALS["mid"], $stp+1, $receiver, $host, json_encode($params), $GLOBALS["via"]);
        return json_encode($params, JSON_PRETTY_PRINT);
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $params     = setMandatoryResponNew($frm, $ref1, "", "", $data);
    
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk($kdproduk, $frm);
    $adddata2   = tambahdataproduk2($kdproduk, $frm);
    if ($frm->getStatus() == "00") {
        $url        = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk  = array("url_struk" => "https://202.43.173.234/struk/?id=" . $url);
        $merge      = array_merge($params,$adddata,$adddata2,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2);
    }

    return json_encode($merge);
}
function inqpln2($data){
    // ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $field      = 8;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    // global $pgsql;
    global $host;

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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__);
    // $fm = convertFM($msg, "*");
    // // echo "fm = ".$fm."<br>";die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA" && substr(strtoupper($kdproduk), 0,7) != "PLNNONH"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra dan plnnon ";
    } else {
        // die('a');
        if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
            if($idpel1 == '323620024326'){
                $resp = "TAGIHAN*PLNPASC30*4361401932*10*20201012150356*H2H*323620024326***123438*3000*".$idoutlet."*------*------*18202145*2**501*1940559562*00*TRANSAKSI SUKSES*0000000*323620024326*1*1*01**AHMAD SUNARTA *32360*123 * R1M*000000900*000000000*202010*20102020*00000000*000000123438*D0000000000*0000000000*000000000000*00015762*00015845*00000000*00000000*00000000*00000000*****************************************D9E9DC140F064E1D83BF62778E50163B**123438*    ";
            }elseif($idpel1 == '611507088714')
            {
                $resp = "TAGIHAN*PLNPASCB*4334952372*10*20200928060011*H2H*611507088714****2500*SP5755*------*------*164106113*1**501*1927202490*06*EXT: IDPEL YANG ANDA MASUKKAN SALAH, MOHON TELITI KEMBALI*0000000*611507088714*******************************************************************";
            }elseif($idpel1 == "513010220414"){
                $resp = "TAGIHAN*PLNPASCB*4337026614*10*20200929104304*H2H*513010220414****2500*HH122973*------*------*41136*1**501*1928141133*34*EXT: TAGIHAN SUDAH TERBAYAR*0000000*513010220414*******************************************************************";
            }else{
                $resp = "TAGIHAN*PLNPASC*73848704*11*20121217145857*H2H*323360001351***53535*5700*" . $idoutlet . "**------*2269165*1*1*99501*50512928*00*SUCCESSFUL*0000000*323360001351*3*3*03**MILLI.M                  *32330*123            *  R1*000000450*000004800*201210*20102012*00000000*00000014685*D0000000000*0000000000*000009000*00006200*00010400*00000000*00000000*00000000*00000000*201211*20112012*00000000*00000014289*D0000000000*0000000000*000006000*00010400*00014500*00000000*00000000*00000000*00000000*201212*20122012*00000000*00000009561*D0000000000*0000000000*000000000*00014500*00017300*00000000*00000000*00000000*00000000******************";
            }
        }elseif (substr(strtoupper($kdproduk), 0,6) == "PLNPRA") {
            $resp = "TAGIHAN*PLNPRAH*4309283567*10*20200914181030*H2H*01107552562*211018760870***2500*".$idoutlet."*------*------*399892172*1**053502*1915027506*00*TRANSAKSI SUKSES*JTL53L3*01107552562*211018760870*0*82E949793A9E4BDDBE911A070A84479A*0BMS210Z6FB9D4C2C368B017380014BB**LERI MONIKA PRICILIA*R1M*000000900****21*21101*123*00648*0****************";
        }elseif (substr(strtoupper($kdproduk), 0,7) == "PLNNONH") {
            $resp = "TAGIHAN*PLNNONH*4309228544*10*20200914174445*H2H*5362143031645**5362143031645*96533*5000*".$idoutlet."*------*------*5756692*1**053504*1915001244*00*TRANSAKSI SUKSES*0000000*5362143031645***PENYELESAIAN P2TL        *20200914*02022022*536211103834*GOJALI                   *2BE8FEF83CED4735911D64F4AFBF433B*0BMS210ZE4EB614F5A5C*53621*L PERINTIS KEMERDEKAAN NO 151 SKI 1*23            0*2*00000000009653300*2*00000000009653300*2*0000000000******00*2*00000000000000000****";
        }
    }
   
    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $params     = setMandatoryResponNewArranet($frm, $ref1, "", "", $data);
    
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk_arranet($kdproduk, $frm);
    // print_r($adddata);die();
    $adddata2   = tambahdataproduk2_arranet($kdproduk, $frm);
     if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
        // die('a');
        $adddata3   = tambahdataproduk3($kdproduk, $frm);
    }else{
        $adddata3 = array();
    }   
    $merge      = array_merge($params,$adddata,$adddata2,$adddata3);
    
    // print_r($adddata);
    // print_r($adddata2);
    return json_encode($merge);
}

function paypln2($data){
    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $nominal    = strtoupper($data->nominal);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $ref3       = strtoupper($data->ref3);
    $field      = 11;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

  

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }
    
    // global $pgsql;
    global $host;

    if($kdproduk == 'PLNPRA'){
        $kdproduk = 'PLNPRAH';
    } else if($kdproduk == 'PLNPASC'){
        $kdproduk = 'PLNPASCH';
    }

    $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan
    
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__);
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA" && substr(strtoupper($kdproduk), 0,7) != "PLNNONH"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra dan plnnonh";
    }else if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else {
            //$respon = postValueWithTimeOutDevel($fm);
            // $respon = postValue($fm);
            // $resp = $respon[7];
        }
    } else {
       
            // $respon = postValue($fm);
            // $resp = $respon[7];
    }

    if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
        if($idpel1 == '323620024326'){
           sleep(20);
           header("HTTP/1.0 504 Gateway Time-out");
           die();
            $resp = "BAYAR*PLNPASC30*4361402034*10*20201012150401*H2H*323620024326*****".$idoutlet."*------*------*****1940559618*00*SEDANG DIPROSES*********************************************************************";
        } else if($idpel1 == '551001911621'){
            $resp = "BAYAR*PLNPASCB*4335071869*4*20200928075107*H2H*551001911621***171932*2500*HH118326*------**1893363*1***1927252603*11*Inquiry record tidak ditemukan. Silahkan melakukan inquiry ulang*********************************************************************";
        }elseif($idpel1 == '611507088714'){
            $resp = "BAYAR*".$kdproduk."*831295610*10*20200928075107*H2H*".$idpel1."*".$idpel2."****".$idoutlet."*".$pin."*------******00*SEDANG DIPROSES*0000000*".$idpel1."*******************************************************************";
        }else{
            $resp = "BAYAR*PLNPASC*73849112*11*20121217145957*H2H*323360001351***5353500000*5700*" . $idoutlet . "**------*2209930*1*1*99501*50513190*00*SUCCESSFUL*0000000*323360001351*3*3*03*BC74455477014318BFADB709029F36B5*MILLI.M                  *32330*123            *R1  *000000450*000004800*201210*20102012*00000000*00000014685*D0000000000*0000000000*000009000*00006200*00010400*00000000*00000000*00000000*00000000*201211*20112012*00000000*00000014289*D0000000000*0000000000*000006000*00010400*00014500*00000000*00000000*00000000*00000000*201212*20122012*00000000*00000009561*D0000000000*0000000000*000000000*00014500*00017300*00000000*00000000*00000000*00000000*****************000000000000*Rincian Tagihan dapat diakses di www.pln.co.id";
        }
    }elseif (substr(strtoupper($kdproduk), 0,6) == "PLNPRA") {

        if($idpel1 == '211018760870'){
            sleep(30);
            $resp = "BAYAR*PLNPRAH*4361402034*10*20201012150401*H2H*211018760870*****".$idoutlet."*------*------*****1940559618*00*SEDANG DIPROSES*********************************************************************";
        }else{
            $resp = "BAYAR*PLNPRAT*4309283601*10*20200914181032*H2H*01107552562*211018760870**50000*2500*SP141894*------*------*399842152*1**053502*1915027544*00*TRANSAKSI SUKSES*JTL53L3*01107552562*211018760870*0*82E949793A9E4BDDBE911A070A84479A*0BMS210Z6FB9D4C2C368B017380014BB*00000000*LERI MONIKA PRICILIA*R1M*000000900*2*0000000000*0*21*21101*123*00648*0***60337603478337008296*2*0000000000*2*0000000000*2*0000412900*2*0000000000*2*000004587100*2*0000003400*Informasi Hubungi Call Center 123 Atau hubungi PLN Terdekat";
        }
    }elseif (substr(strtoupper($kdproduk), 0,7) == "PLNNONH") {
         if($idpel1 == '5362143031645'){
              sleep(20);
           header("HTTP/1.0 504 Gateway Time-out");
           die();
            $resp = "BAYAR*PLNNONH*4361402034*10*20201012150401*H2H*5362143031645*****".$idoutlet."*------*------*****1940559618*00*SEDANG DIPROSES*********************************************************************";
        }else{
            $resp = "BAYAR*PLNNONH*4309228620*10*20200914174449*H2H*5362143031645***96533*5000*SP2347*------*------*5658959*1**053504*1915001294*00*TRANSAKSI SUKSES*0000000*5362143031645***PENYELESAIAN P2TL        *20200914*02022022*536211103834*GOJALI                   *2BE8FEF83CED4735911D64F4AFBF433B*0BMS210ZE4EB614F5A5C*     *                                   *               *2*00000000009653300*2*00000000009653300*2*0000000000******  *2*                 ****Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
        }
    }

    if($resp == 'null'){
        if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids())){
            $params = array(
                "KODE_PRODUK"       => (string) $kdproduk,
                "WAKTU"             => (string) date('YmdHis'),
                "IDPEL1"            => (string) $idpel1,
                "IDPEL2"            => (string) $idpel2,
                "IDPEL3"            => (string) $idpel3,
                "NAMA_PELANGGAN"    => (string) '',
                "PERIODE"           => (string) '',
                "JML_BULAN"         => (string) '',
                "NOMINAL"           => (string) '',
                "TARIF"             => (string) '',
                "DAYA"              => (string) '',
                "REF"               => (string) '',
                "STANAWAL"          => (string) '',
                "STANAKHIR"         => (string) '',
                "INFOTEKS"          => (string) '',
                "ADMIN"             => (string) '',
                "UID"               => (string) $idoutlet,
                "PIN"               => (string) '------',
                "REF1"              => (string) $ref1,
                "REF2"              => (string) '',
                "REF3"              => "0",
                "STATUS"            => (string) '00',
                "KET"               => (string) 'SEDANG DIPROSES',
                "SALDO_TERPOTONG"   => (string) '',
                "SISA_SALDO"        => (string) '',
                "URL_STRUK"         => (string) ''
            );
        } else {
            $params = array(
                "KODE_PRODUK"       => (string) $kdproduk,
                "WAKTU"             => (string) date('YmdHis'),
                "IDPEL1"            => (string) $idpel1,
                "IDPEL2"            => (string) $idpel2,
                "IDPEL3"            => (string) $idpel3,
                "NAMA_PELANGGAN"    => (string) '',
                "NOMINAL"           => (string) '',
                "TARIF"             => (string) '',
                "DAYA"              => (string) '',
                "REF"               => (string) '',
                "MATERAI"           => (string) '',
                "PPN"               => (string) '',
                "PPJ"               => (string) '',
                "ANGSURAN"          => (string) '',
                "RPTOKEN"           => (string) '',
                "KWH"               => (string) '',
                "TOKEN"             => (string) '',
                "INFOTEKS"          => (string) '',
                "ADMIN"             => (string) '',
                "UID"               => (string) $idoutlet,
                "PIN"               => (string) '------',
                "REF1"              => (string) $ref1,
                "REF2"              => (string) '',
                "REF3"              => "0",
                "STATUS"            => (string) '00',
                "KET"               => (string) 'SEDANG DIPROSES',
                "SALDO_TERPOTONG"   => (string) '',
                "SISA_SALDO"        => (string) '',
                "URL_STRUK"         => (string) ''
            );
        }
        
        return json_encode($params, JSON_PRETTY_PRINT);
    }
    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $params     = setMandatoryResponNewArranet($frm, $ref1, "", "", $data);
    // print_r($params);die();
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk_arranet($kdproduk, $frm,1);
    $adddata2   = tambahdataproduk2_arranet($kdproduk, $frm);
     if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
        $adddata3   = tambahdataproduk3($kdproduk, $frm);
    }else{
        $adddata3 = array();
    }   
    

    if ($frm->getStatus() == "00") {
        $url        = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        if(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $url_struk  = array("URL_STRUK" => "https://202.43.173.234/struk/?id=" . $url);
        }else{
            $url_struk  = array("url_struk" => "https://202.43.173.234/struk/?id=" . $url);
        }
        $merge      = array_merge($params,$adddata,$adddata2,$adddata3,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2,$adddata3);
    }

    return json_encode($merge);
}
function transferinq($data){
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    // die('a');
    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $nominal    = strtoupper($data->nominal);
    $kodebank   = strtoupper($data->kodebank);
    $nomorhp    = strtoupper($data->nomorhp);

    $field      = 11;

    // echo count((array)$data);
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }
// 
   
     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    //  if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
    //     if (!isValidIP($uid, $ip)) {
    //         return "IP Anda [$ip] tidak punya hak akses";
    //     }
    // }
     //TAGIHAN*BLTRFABNI*4089232891*2*20200504121034*H2H*3020677499***60000**FA182848*------*------*******
    // }
    // global $pgsql;
    global $host;
    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "TAGIHAN";
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    // $msg[$i+=1] = rand(100000,1000000);
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');
    // $msg[$i+=1] = "H2H";           //VIA
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($uid); 
    $msg[$i+=1] = $pin; // pin
    $msg[$i+=1] = ""; //FIELD_TOKEN
    $msg[$i+=1] = "";   //  FIELD_BALANCE
    $msg[$i+=1] = ""; // FIELD_JENIS_STRUK
    $msg[$i+=1] = ""; // FIELD_KODE_BANK
    $msg[$i+=1] = ""; //
    $msg[$i+=1] = ""; // FIELD_TRX_ID
    $msg[$i+=1] = ""; // FIELD_STATUS
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__); // FIELD_KETERANGAN
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
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    // echo $msg."<br>";
    // die();
    $tgl = date("Ymdhis");
    $list = $GLOBALS["sndr"];
        // die('a');
    if($idpel1 == "0830041384")
    {
        $resp ="TAGIHAN*BLTRFAG*4736252254*5*".$tgl."*H2H*0830041384***".$nominal."*6500*".$idoutlet."*------**1544298314*1*10*BLSTR*2172844303*00*SUCCESS*TRANSFERTUNAI*2114594167620225**3977778885********SDRI SARAH INDAH LARASATI*SDRI SARAH INDAH LARASATI*********************009***********230*70878*BMS****335298994***0830041384*53532******************-6350";
    
    }elseif($idpel1 == "085255898863"){
        $resp = "TAGIHAN*BLTRFAG*4983245449*5*20211211061314*H2H*085255898863***10000*6500*".$idoutlet."*------**53492718*1**BLTRFAGH*2382129363*76*EXT: Transaksi anda tidak dapat diproses. Nomor tujuan transaksi yang anda masukkan tidak valid. Silakan ulangi transaksi anda. (RC 76)**********************************002*****************CEK_49963388***085255898863*10000******************-2500   ";
    }elseif($idpel1 == "0622471032"){
        $resp = "TAGIHAN*BLTRFAG*121639174459900*8*20211211051421*H2H*0622471032***500000*6500*".$idoutlet."*------**986715815*1**BLTRFAG*2382103999*62*EXT: Invalid kode bank 5!**********************************5***************62576640*****0622471032*500000******************-2500    ";
    }elseif($idpel1 == "0984745364"){
        $resp = "TAGIHAN*BLTRFAG*4744037817*8*20210628204236*H2H*0984745364***100000*6500*SP362305*------**1615868*1*10*BLSTR*2178424349*00*success*******IDR*BUKA*BNI TAPLUS*0061*00000009925292317*Bpk IYUS SUHENDI**1.743.288,00*2.438,00*2020-08-04***BOJONG MENTENG*Ciomas***16610*99999999***087878134419*BOJONG MENTENG*004001*Ciomas**16610*DEP*2000*0001*1.068,00*1.740.850,00*0,00*0,2500**********141*****0984745364*100000******************-2500   ";
    }elseif($idpel1 == "0031901610001869"){
        $resp = "TAGIHAN*BLTRFAG*4737772727*5*".$tgl."*H2H*0031901610001869***".$nominal."*6500*".$idoutlet."*------**1531445232*1**BLTRFAG*2173902116*00*SUCCESS*TRANSFERTUNAI*2114594167620225**3977778885********FITRIA PERMATA*FITRIA PERMATA*********************200***********230*70878*BMS****335602066***0031901610001869*".$nominal."******************-4350 ";
    }else{
        $resp = "TAGIHAN*BLTRFAG*4306280183*5*".$tgl."*H2H*8820798377***".$nominal."*6500*".$uid."*------**79764271*1**BLTRFAG*1913676867*00*SUCCESS************CHANDRA WIJAYA ATMAJA**********************014********************8820798377*".$nominal."******************-2500";
    }
    
    // die('a');
    // echo $resp;die('sss');
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    // echo $kdproduk;
    $frm = getParseProduk($kdproduk, $resp);

    // print_r($frm);die();
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
    $r_mid          = $frm->getMID();
    //$r_saldo_terpotong = $r_nominal + $r_nominaladmin;

    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
    $r_id_pelanggan = getIdPelanggan($kdproduk, $frm);
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    $r_kode_bank = getKodeBank($kdproduk, $frm);

    if (strpos($r_kode_bank, '\'') !== false) {
        $r_kode_bank = str_replace('\'', "`", $r_kode_bank);
    }
    if(substr($r_kode_bank, 0, 2) != "00"){

        if($r_kode_bank < 10){
            $r_kode_bank_tmp = "00".$r_kode_bank;
        }else{
             $r_kode_bank_tmp = "0".$r_kode_bank;
        }
    }else{

             $r_kode_bank_tmp = $r_kode_bank;
       
    }
    $r_nama_bank = getnamabank($kodebank);

    // print_r($r_nama_bank);die();

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"            => (string) $r_id_pelanggan,
        "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
        "NAMA_BANK"         => (string) $r_nama_bank,
        "KODE_BANK"         => (string) $kodebank,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => "0",
        "STATUS"            => (string) $r_status,
        "KET"               => (string) $r_keterangan,
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) $url_struk
    );

    //insert into fmss_message
    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    return json_encode($params, JSON_PRETTY_PRINT);
}

function transferpay($data)
{
    // die('a');
    // BAYAR*BLTRFABNI*4094581631*2*20200508012336*H2H*530401024308538***110000**FA133192*------*------*****1783216123**

    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $nominal    = strtoupper($data->nominal);
    $kodebank   = strtoupper($data->kodebank);
    $nomorhp    = strtoupper($data->nomorhp);
    $field      = 12;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }


    // $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
    //     if (!isValidIP($uid, $ip)) {
    //         return "IP Anda [$ip] tidak punya hak akses";
    //     }
    // }
  
    // global $pgsql;
    global $host;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
    if($ref2 == ""){
        $ref2 = 0;
    }
    // $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan

    $stp        = $GLOBALS["step"] + 1;
    $msg        = array();
    $i          = -1;
    $msg[$i+=1] = $mti;
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    // $msg[$i+=1] = rand(100000,100000000);
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');
    // $msg[$i+=1] = "H2H";           //VIA
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($uid); // FIELD_LOKET_ID
    $msg[$i+=1] = $pin; // FIELD_PIN
    $msg[$i+=1] = ""; // FIELD_TOKEN
    $msg[$i+=1] = ""; // FIELD_BALANCE
    $msg[$i+=1] = ""; // FIELD_JENIS_STRUK
    $msg[$i+=1] = ""; // FIELD_KODE_BANK
    $msg[$i+=1] = ""; // FIELD_KODE_PRODUK_BILLER
    $msg[$i+=1] = $ref2; // FIELD_TRX_ID
    $msg[$i+=1] = ""; // FIELD_STATUS
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__); // FIELD_KETERANGAN
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

     // echo "fm = ".$fm."<br>";die();

    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    $tgl = date("Ymdhis");
    $list       = $GLOBALS["sndr"];
        if($idpel1 == "0830041384")
        {
            $resp = "BAYAR*BLTRFAG*4736252573*10*".$tgl."*H2H*0830041384***".$nominal."*6500*".$uid."*------**1543717073*1*10*BLSTR*2172844595*06*SALDO ANDA TIDAK MENCUKUPI***************************230*70878*BMS**335298994**009**009*53532********230*70878*BMS**141*BMS23070878***2021-06-22T13:16:03+07:00*0830041384*53532*969871*****************-6350    ";
        }elseif($idpel1 == "0031901610001869"){ // BANK BTN case pending
            $resp="BAYAR*BLTRFAG*4737772955*9*".$tgl."*H2H*0031901610001869***".$nominal."*6500*".$uid."*------**1530304271*1**BLTRFAG*2173902343*00*SEDANG DIPROSES*2114594167620225**3977778885*3977778885********FITRIA PERMATA*FITRIA PERMATA***************230*70878*BMS**335602066**200**200*".$nominal."********230*70878*BMS*******0031901610001869*".$nominal."*002173902343*****************-4350   ";
            $update = updatedataDevel($uid,$idpel1,$ref1,$nominal);
        }else{
              $resp="BAYAR*BLTRFAG*4306281629*9*".$tgl."*H2H*8820798377***".$nominal."*6500*".$uid."*------**79643641*1**BLTRFAG*1913677418*00*SUCCESS**16671*API*8820798377**1850581**PENDING*DOMESTIC_TRANSFER*0**CHANDRA WIJAYA ATMAJA*CHANDRA WIJAYA ATMAJA***(not set)**null****************014**014*75000*******0**70878*BMS*2020-09-13 06:40:28****29687625**8820798377*75000*29687625*****************-2500";
        }

    if($resp == 'null'){
        $params = array(
            "KODE_PRODUK"       => (string) $kdproduk,
            "WAKTU"             => (string) date('YmdHis'),
            "IDPEL1"            => (string) $idpel1,
            "IDPEL2"            => (string) $idpel2,
            "IDPEL3"            => (string) $idpel3,
            "NAMA_PELANGGAN"    => (string) '',
            "PERIODE"           => (string) '',
            "NOMINAL"           => (string) $nominal,
            "ADMIN"             => (string) '',
            "UID"               => (string) $uid,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1,
            "REF2"              => (string) $ref2,
            "REF3"              => (string) '',
            "STATUS"            => (string) '00',
            "KET"               => (string) 'SEDANG DIPROSES',
            "SALDO_TERPOTONG"   => (string) '',
            "SISA_SALDO"        => (string) '',
            "URL_STRUK"         => (string) ''
        );
        return json_encode($params, JSON_PRETTY_PRINT);
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);

    // print_r($frm);die();
    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMID();
    $r_saldo_terpotong  = 0;
    
    $r_id_pelanggan = getIdPelanggan($kdproduk, $frm);
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    $r_kode_bank = getKodeBank($kdproduk, $frm);
    if (strpos($r_kode_bank, '\'') !== false) {
        $r_kode_bank = str_replace('\'', "`", $r_kode_bank);
    }
      if(substr($r_kode_bank, 0, 1) != "00"){
        if($r_kode_bank < 10){
            $r_kode_bank_tmp = "00".$r_kode_bank;
        }else{
             $r_kode_bank_tmp = "0".$r_kode_bank;
        }
    }else{

             $r_kode_bank_tmp = $r_kode_bank;
       
    }
    $r_nama_bank = getnamabank($kodebank);

    if($r_status === ''){
        $r_status = '00';
        if($r_idtrx === ''){
            if($idpel1 == ""){
                $bill_info1 = $idpel2;
            } else {
                $bill_info1 = $idpel1;
            }
            $r_idtrx = getIdTransaksi($bill_info1,$idoutlet,$kdproduk,$ref1);
        }
    }
   

    $r_reff3 = '0';
    //$r_reff3 = $frm->getTokenPln();
    if (substr($kdproduk, 0, 6) == "PLNPRA" && $frm->getStatus() == "00") {
        if ($r_reff3 == '0') {
            $r_reff3 = $frm->getTokenPln();
        }
    }

    $url_struk = "";
    if ($frm->getStatus() == "00") {

        if (strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false ) {
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
        }else{
            $nom_up = getnominalup($r_idtrx);
            $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
            $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
            $url_struk = "https://202.43.173.234/struk/?id=" . $url;

        }
     
    }else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
    }
    
   

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"           => (string) $r_id_pelanggan,
        "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
        "NAMA_BANK"         => (string) $r_nama_bank,
        "KODE_BANK"         => (string) $kodebank,
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
        "URL_STRUK"         => (string) $url_struk
    );

    $is_return = true;
 
    if($r_idtrx != $ref2){
        // $text = pay_resp_text($params);
        // $get_mid = get_mid_from_idtrx($r_idtrx);
        // $get_step = get_step_from_mid($r_mid) + 1;
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    }
    
    return json_encode($params, JSON_PRETTY_PRINT);

}

function inqpln($data){
    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $field      = 8;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }
    // global $pgsql;

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
    $msg[$i+=1] = strtoupper($uid);
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
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra ";
    } else if($idpel1 == '312100261160'){
       $resp = "TAGIHAN*PLNPASCH*121621383262031*10*20210519071422*H2H*312100261160***52861*4000*HH10632*------*------*10374174397*1**501*2131834564*00*TRANSAKSI SUKSES*0000000*312100261160*2*2*02**ACIRMAM *31210*123 * R1*000000450*000000000*202104*20042021*00000000*000000031598*D0000000000*0000000000*000000006000*00025209*00025343*00000000*00000000*00000000*00000000*202105*20052021*00000000*000000015263*D0000000000*0000000000*000000000000*00025343*00025417*00000000*00000000*00000000*00000000****************************5A2B008F04264021A918385F3BEFD438**52861*   ";
    } elseif($idpel1 == '611507088714')
    {
        $resp = "TAGIHAN*PLNPASCH*4334952372*10*20200928060011*H2H*611507088714****4000*SP5755*------*------*164106113*1**501*1927202490*06*EXT: IDPEL YANG ANDA MASUKKAN SALAH, MOHON TELITI KEMBALI*0000000*611507088714*******************************************************************";
    }elseif($idpel1 == "513010220414"){
        $resp = "TAGIHAN*PLNPASCH*4337026614*10*20200929104304*H2H*513010220414****4000*HH122973*------*------*41136*1**501*1928141133*34*EXT: TAGIHAN SUDAH TERBAYAR*0000000*513010220414*******************************************************************";
    }else {
        $resp = "TAGIHAN*PLNPRAH*12995997*11*20120525130039*H2H*01117082246*511061245422***1600*" . $idoutlet . "*------*D5430F79*207963*1*1*99502*18253704*00*SUCCESSFUL*0000000*01117082246*511061245422*0*988776546B2D482781B583F2A2FD5D76*C9745AE21CA1408280AF6888D1FE7164**KARTO SOEWITO*R1*000002200*2*0000160000**51*51106*123*01584*0****************";
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
        $denda = $getdatapln['denda'];

        $params = array(
            "KODE_PRODUK"       => (string) $r_kdproduk,
            "WAKTU"             => (string) $r_tanggal,
            "IDPEL1"            => (string) $r_idpel1,
            "IDPEL2"            => (string) $r_idpel2,
            "IDPEL3"            => (string) $r_idpel3,
            "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
            "PERIODE"           => (string) $r_periode_tagihan,
            "JML_BULAN"         => (string) $jmlbulan,
            "NOMINAL"           => (string) $r_nominal,
            "TARIF"             => (string) $tarif,
            "DAYA"              => (string) $daya,
            "REF"               => (string) $ref,
            "STANAWAL"          => (string) $stanawal,
            "STANAKHIR"         => (string) $stanakhir,
            "INFOTEKS"          => (string) $infoteks,
            "ADMIN"             => (string) $r_nominaladmin,
            "UID"               => (string) $r_idoutlet,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1,
            "REF2"              => (string) $r_idtrx,
            "REF3"              => "0",
            "STATUS"            => (string) $r_status,
            "KET"               => (string) $r_keterangan,
            "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
            "SISA_SALDO"        => (string) $r_sisa_saldo,
            "URL_STRUK"         => (string) $url_struk
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
            "KODE_PRODUK"       => (string) $r_kdproduk,
            "WAKTU"             => (string) $r_tanggal,
            "IDPEL1"            => (string) $r_idpel1,
            "IDPEL2"            => (string) $r_idpel2,
            "IDPEL3"            => (string) $r_idpel3,
            "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
            "NOMINAL"           => (string) $r_nominal,
            "TARIF"             => (string) $tarif,
            "DAYA"              => (string) $daya,
            "REF"               => (string) $ref,
            "MATERAI"           => (string) $meterai,
            "PPN"               => (string) $ppn,
            "PPJ"               => (string) $ppj,
            "ANGSURAN"          => (string) $angsuran,
            "RPTOKEN"           => (string) $pp,
            "KWH"               => (string) $kwh,
            "TOKEN"             => (string) $nomortoken,
            "INFOTEKS"          => (string) $infoteks,
            "ADMIN"             => (string) $r_nominaladmin,
            "UID"               => (string) $r_idoutlet,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1,
            "REF2"              => (string) $r_idtrx,
            "REF3"              => "0",
            "STATUS"            => (string) $r_status,
            "KET"               => (string) $r_keterangan,
            "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
            "SISA_SALDO"        => (string) $r_sisa_saldo,
            "URL_STRUK"         => (string) $url_struk
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
    return json_encode($params, JSON_PRETTY_PRINT);
}

function paypln($data){
    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $nominal    = strtoupper($data->nominal);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $ref3       = strtoupper($data->ref3);
    $field      = 11;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }
    // global $pgsql;

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
    $msg[$i+=1] = strtoupper($uid);
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
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra ";
    } else if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC" && $idpel1 == '312100261160'){
         $resp = "BAYAR*PLNPASCH*121621383349347*10*20210519071551*H2H*312100261160***52861*4000*HH10632*------*------*10372323839*1**501*2131836269*00*TRANSAKSI SUKSES*0000000*312100261160*2*2*02*0BMS210ZAAC3B2FA79591EE040B00BC5*ACIRMAM *31210*123 * R1*000000450*000000000*202104*20042021*00000000*000000031598*D0000000000*0000000000*000000006000*00025209*00025343*00000000*00000000*00000000*00000000*202105*20052021*00000000*000000015263*D0000000000*0000000000*000000000000*00025343*00025417*00000000*00000000*00000000*00000000****************************5A2B008F04264021A918385F3BEFD438**52861*Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :    ";
    } else {
        $resp = "BAYAR*PLNPRAH*12996128*11*20120525130054*H2H*01117082246*511061245422**".$nominal."*1600*" . $idoutlet . "*------*D5430F79*157963*1*1*99502*18253742*00*SUCCESSFUL*0000000*01117082246*511061245422*0*988776546B2D482781B583F2A2FD5D76*14EDE52D039A4421A258AE30C77A3891*02414165*KARTO SOEWITO*R1*000002200*2*0000160000*0*51*51106*123*01584*0***32927368215773195205*2*0000000000*2*0000000000*2*0000358519*2*0000000000*2*000004481481*2*0000005640*Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
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
        $url_struk = "https://202.43.173.234/struk/?id=" . $url;
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
            "KODE_PRODUK"       => (string) $r_kdproduk,
            "WAKTU"             => (string) $r_tanggal,
            "IDPEL1"            => (string) $r_idpel1,
            "IDPEL2"            => (string) $r_idpel2,
            "IDPEL3"            => (string) $r_idpel3,
            "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
            "PERIODE"           => (string) $r_periode_tagihan,
            "JML_BULAN"         => (string) $jmlbulan,
            "NOMINAL"           => (string) $r_nominal,
            "TARIF"             => (string) $tarif,
            "DAYA"              => (string) $daya,
            "REF"               => (string) $ref,
            "STANAWAL"          => (string) $stanawal,
            "STANAKHIR"         => (string) $stanakhir,
            "INFOTEKS"          => (string) $infoteks,
            "ADMIN"             => (string) $r_nominaladmin,
            "UID"               => (string) $r_idoutlet,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1,
            "REF2"              => (string) $r_idtrx,
            "REF3"              => "0",
            "STATUS"            => (string) $r_status,
            "KET"               => (string) $r_keterangan,
            "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
            "SISA_SALDO"        => (string) $r_sisa_saldo,
            "URL_STRUK"         => (string) $url_struk
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
            "KODE_PRODUK"       => (string) $r_kdproduk,
            "WAKTU"             => (string) $r_tanggal,
            "IDPEL1"            => (string) $r_idpel1,
            "IDPEL2"            => (string) $r_idpel2,
            "IDPEL3"            => (string) $r_idpel3,
            "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
            "NOMINAL"           => (string) $r_nominal,
            "TARIF"             => (string) $tarif,
            "DAYA"              => (string) $daya,
            "REF"               => (string) $ref,
            "MATERAI"           => (string) $meterai,
            "PPN"               => (string) $ppn,
            "PPJ"               => (string) $ppj,
            "ANGSURAN"          => (string) $angsuran,
            "RPTOKEN"           => (string) $pp,
            "KWH"               => (string) $kwh,
            "TOKEN"             => (string) $nomortoken,
            "INFOTEKS"          => (string) $infoteks,
            "ADMIN"             => (string) $r_nominaladmin,
            "UID"               => (string) $r_idoutlet,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1,
            "REF2"              => (string) $r_idtrx,
            "REF3"              => "0",
            "STATUS"            => (string) $r_status,
            "KET"               => (string) $r_keterangan,
            "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
            "SISA_SALDO"        => (string) $r_sisa_saldo,
            "URL_STRUK"         => (string) $url_struk
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
    return json_encode($params, JSON_PRETTY_PRINT);
}

function cek($data)
{

    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1      = strtoupper($data->idpel);
    $idpel2     = "";
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $field      = 6;

    if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS"){
        if(substr($idpel1, 0, 5) != "88888"){
            $idpel1 = "88888".substr($idpel1, 2, 11);
        }  
        $periodereq = trim(strtoupper($data->periode));
        $field      = 7;
    } else if(substr(strtoupper($kdproduk), 0,2) == "KK"){
        $nominal    = trim(strtoupper($data->nominal));
        $field      = 7;
    }  else if(substr(strtoupper($kdproduk), 0,2) == "EM"){
        $nominal    = trim(strtoupper($data->nominal));
        $field      = 7;
    } else if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $nominal    = trim(strtoupper($data->nominal));
        $kodebank   = trim(strtoupper($data->kodebank));
        $nomorhp    = trim(strtoupper($data->nomorhp));
        $field      = 10;
    } else if(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $idpel2      = trim(strtoupper($data->tahun));
        $field      = 7;
    }  else {
        $field      = 6;
    }

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    // if($uid != 'SP300203'){
    //     if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
    //         if (!isValidIP($uid, $ip)) {
    //             return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //         }
    //     }
    // }

    $arr = array('WATAPIN', 'WAMJK', 'WABGK');
    if (in_array($kdproduk, $arr)) {
        $idpel2 = $idpel1;
    } else if ($kdproduk == 'WASDA' && strlen($idpel1) > 8) {
        $idpel2 = $idpel1;
    } else if ($kdproduk == 'WABJN' && strlen($idpel1) == 7) {
        $idpel2 = $idpel1;
    }

    if(substr($kdproduk, 0, 7) == 'TELEPON'){
        $cek_telkom = cek_is_telp_or_speedy($idpel1);
        $kdproduk   = $cek_telkom['produk'];
        $idpel1     = $cek_telkom['idpel1'];
        $idpel2     = $cek_telkom['idpel2'];
    }

    if(substr($kdproduk, 0, 6) == 'PLNPRA'){
        if(strlen($idpel1) == '12'){
            $idpel2 = $idpel1;
            $idpel1 = "";
        }
    }

    // global $pgsql;
    global $host;

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1]="TAGIHAN";//TAGIHAN 
    $msg[$i+=1]=$kdproduk;//EMGJK
    $msg[$i+=1]=$GLOBALS["mid"];//44341115190
    $msg[$i+=1]=$stp;//3
    $msg[$i+=1]="";//DATETIME
    $msg[$i+=1]=$GLOBALS["via"];//H2H
    $msg[$i+=1]=$idpel1;//082317600213
    $msg[$i+=1]=$idpel2; // idpel 2
    $msg[$i+=1] = substr($kdproduk, 0,2) == "KK" || substr(strtoupper($kdproduk), 0,5) == "BLTRF" || substr($kdproduk, 0,2) == "EM" || substr($kdproduk, 0,7) == "PLNPRAD" ? "" : $nominal;
    $msg[$i+=1] = substr($kdproduk, 0,2) == "KK" || substr(strtoupper($kdproduk), 0,5) == "BLTRF" || substr($kdproduk, 0,2) == "EM" || substr($kdproduk, 0,7) == "PLNPRAD" ? $nominal : "";
    $msg[$i+=1]="";
    $msg[$i+=1]=$uid;//OUTLET
    $msg[$i+=1]=$pin;//PIN
    $msg[$i+=1]="";//FIELD_TOKEN
    $msg[$i+=1]="";//FIELD_BALANCE
    $msg[$i+=1]="";//FIELD_JENIS_STRUK
    $msg[$i+=1]="";//FIELD_KODE_BANK
    $msg[$i+=1]="";//FIELD_KODE_PRODUK_BILLER
    $msg[$i+=1]="";//FIELD_TRX_ID
    $msg[$i+=1]=$ref1;//FIELD_STATUS
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;
    if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS" || substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $msg[$i+=1] = ""; //SWITCHER_ID 
        $msg[$i+=1] = ""; //BILLER_CODE 
        $msg[$i+=1] = ""; //CUSTOMER_ID
        $msg[$i+=1] = substr(strtoupper($kdproduk), 0,5) == "BLTRF" ? '' : $periodereq;//BILL_QUANTITY
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

        if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
            $msg[$i+=1] = "";
            $msg[$i+=1] = $nomorhp;
            $msg[$i+=1] = "";
            $msg[$i+=1] = "";
            $msg[$i+=1] = "";
            $msg[$i+=1] = $idpel2;
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
        }
    } else if(substr(strtoupper($kdproduk),0,5) == "PAJAK"){
        $msg[$i+=1] = "";//SWITCHER_ID
        $msg[$i+=1] = "";//BILLER_cODE
        $msg[$i+=1] = $idpel1;//CUST_ID1
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";//FIELD_NO_REF_1
        $msg[$i+=1] = "";//FIELD_NO_REF_2
        $msg[$i+=1] = "";//FIELD_NTP
        $msg[$i+=1] = "";//FIELD_NTB
        $msg[$i+=1] = "";//FIELD_KODE_PEMDA
        $msg[$i+=1] = $idpel1;//FIELD_NOP
        $msg[$i+=1] = "PBB";//FIELD_KODE_PAJAK
        $msg[$i+=1] = $idpel2 != "" ? $idpel2 : date("Y");//FIELD_TAHUN_PAJAK
    } else if(substr(strtoupper($kdproduk), 0,7) == "PLNPRAD"){
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
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
    }elseif(substr(strtoupper($kdproduk), 0,2) == "EM"){
        $msg[$i+=1]="";//FIELD_CUSTOMER_NAME
        $msg[$i+=1]="";//FIELD_AMOUNT
        $msg[$i+=1]="";//FIELD_REFNO
        $msg[$i+=1]="";//FIELD_REFNO_2
        $msg[$i+=1]="";//FIELD_BILLER_CODE
        $msg[$i+=1]="";//FIELD_BILLER_STAN
        $msg[$i+=1]="";//FIELD_FEE_AMOUNT
        $msg[$i+=1]="";//FIELD_MERCHANT_ID
        $msg[$i+=1]="";//FIELD_MERCHANT_NAME
        $msg[$i+=1]="";//FIELD_MERCHANT_TYPE
        $msg[$i+=1]="";//FIELD_ADDT_DATA
        $msg[$i+=1]="";//FIELD_ADDT_DATA_2
        $msg[$i+=1]="";//FIELD_FORWARDING_ID
        $msg[$i+=1]="";//FIELD_TERMINAL_ID
        $msg[$i+=1]="";//FIELD_ISSUER_ID
        $msg[$i+=1]="";//FIELD_TRX_CODE
        $msg[$i+=1]="";//FIELD_POS_ENTRY_MOD
        $msg[$i+=1]="";//FIELD_SETTLEMENT_DATE
        $msg[$i+=1]="";//FIELD_CAPTURE_DATE
        $msg[$i+=1]="";//FIELD_APPROVAL_CODE
        $msg[$i+=1]="";//FIELD_ACC_NO
    }
 
    $fm         = convertFM($msg, "*");

    if(substr(strtoupper($kdproduk), 0,2) == "WA"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PLN"){
        if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNNON"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
        }
    }elseif(substr(strtoupper($kdproduk), 0,3) == "ASR"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "EM"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "KK"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "HP"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "FN"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "TV"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "GAS"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PGN"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,6) == "SPEEDY"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,7) == "TELEPON"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }else{
        die("Data Tidak Tersedia Didevel, Hub Team Bisnis!");
    }


    if($resp == "" || $resp == "null"){
        die("Data Tidak Tersedia Didevel");
    }

    $man        = FormatMsg::mandatoryPayment();
    $frm        = new FormatMandatory($man["inq"], $resp);
    $params     = setMandatoryResponJsonDev($frm, $ref1, "", "", $data);
    $frm        = getParseProduk($kdproduk, $resp);
 
    if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $adddata    = tambahdataproduk($kdproduk, $frm,$kodebank);
    }else{
        $adddata    = tambahdataproduk($kdproduk, $frm);
    }

    $adddata2   = tambahdataproduk2($kdproduk, $frm);
    $merge      = array_merge($params,$adddata,$adddata2);
    
    return json_encode($merge, JSON_PRETTY_PRINT);
}

function bayar($data)
{

    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel);
    $idpel2     = "";
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $nominal    = strtoupper($data->nominal);
    $field      = 8;

    if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS"){
        if(substr($idpel1, 0, 5) != "88888"){
            $idpel1 = "88888".substr($idpel1, 2, 11);
        }
        $periodereq = trim(strtoupper($data->periode));
        $hp         = trim(strtoupper($data->hp));
        $field      = 10;
    } else if(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $idpel2     = trim(strtoupper($data->tahun));
        $field      = 9;
    }else if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $kodebank   = trim(strtoupper($data->kodebank));
        $nomorhp    = trim(strtoupper($data->nomorhp));
        $field      = 10;
    }

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $arr = array('WATAPIN', 'WAMJK', 'WABGK');
    if (in_array($kdproduk, $arr)) {
        $idpel2 = $idpel1;
    } else if ($kdproduk == 'WASDA' && strlen($idpel1) > 8) {
        $idpel2 = $idpel1;
    } else if ($kdproduk == 'WABJN' && strlen($idpel1) == 7) {
        $idpel2 = $idpel1;
    }

    if(substr($kdproduk, 0, 7) == 'TELEPON' || $kdproduk == "SPEEDY"){
        $cek_telkom = cek_is_telp_or_speedy($idpel1);
        $kdproduk   = $cek_telkom['produk'];
        $idpel1     = $cek_telkom['idpel1'];
        $idpel2     = $cek_telkom['idpel2'];
    }

    if(substr($kdproduk, 0, 6) == 'PLNPRA'){
        if(strlen($idpel1) == '12'){
            $idpel2 = $idpel1;
            $idpel1 = "";
        }
     
    }

    $tahun_pajak = "";
    if(substr(strtoupper($kdproduk),0,5) == "PAJAK"){
        $dendapbb = "";
    }

    // $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan
    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "BAYAR";
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = substr(strtoupper($kdproduk), 0,5) == "BLTRF" ? date('YmdHis') : '';
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($uid);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ref2;
    $msg[$i+=1] = substr(strtoupper($kdproduk), 0,5) == "BLTRF" ? '' : $ref1;
    $msg[$i+=1] = strtoupper("IRS ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];

    if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS" || substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $msg[$i+=1] = ""; //SWITCHER_ID 
        $msg[$i+=1] = ""; //BILLER_CODE 
        $msg[$i+=1] = ""; //CUSTOMER_ID
        $msg[$i+=1] = $periodereq;//BILL_QUANTITY
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
        $msg[$i+=1] = $hp;//CUSTOMER_PHONE_NUMBER
        $msg[$i+=1] = "";//CUSTOMER_ADDRESS
        $msg[$i+=1] = "";//AHLI_WARIS_PHONE_NUMBER
        $msg[$i+=1] = "";//AHLI_WARIS_ADDRESS

        if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
            $msg[$i+=1] = "";
            $msg[$i+=1] = $nomorhp;
            $msg[$i+=1] = "";
            $msg[$i+=1] = "";
            $msg[$i+=1] = "";
            $msg[$i+=1] = $idpel2;
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
        }
    } else if(substr(strtoupper($kdproduk),0,5) == "PAJAK"){
        $msg[$i+=1] = "";//SWITCHER_ID
        $msg[$i+=1] = "";//BILLER_cODE
        $msg[$i+=1] = $idpel1;//CUST_ID1
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";//FIELD_NO_REF_1
        $msg[$i+=1] = "";//FIELD_NO_REF_2
        $msg[$i+=1] = "";//FIELD_NTP
        $msg[$i+=1] = "";//FIELD_NTB
        $msg[$i+=1] = "";//FIELD_KODE_PEMDA
        $msg[$i+=1] = $idpel1;//FIELD_NOP
        $msg[$i+=1] = "PBB";//FIELD_KODE_PAJAK
        $msg[$i+=1] = $idpel2 != "" ? $idpel2 : date("Y");//FIELD_TAHUN_PAJAK
        $msg[$i+=1] = "";//FIELD_NAMA
        $msg[$i+=1] = "";//FIELD_LOKASI
        $msg[$i+=1] = "";//FIELD_KELURAHAN
        $msg[$i+=1] = "";//FIELD_KECAMATAN
        $msg[$i+=1] = "";//FIELD_PROVINSI
        $msg[$i+=1] = "";//FIELD_LUAS_TANAH
        $msg[$i+=1] = "";//FIELD_LUAS_BANGUNAN
        $msg[$i+=1] = "";//FIELD_TANGGAL_JTH_TEMPO
        $msg[$i+=1] = $nominal;//FIELD_TAGIHAN
        $msg[$i+=1] = $dendapbb;//FIELD_DENDA
        $msg[$i+=1] = $nominal;//FIELD_TOTAL_BAYAR
    }else if(substr(strtoupper($kdproduk), 0,7) == "PLNPRAD"){
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
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
    }elseif(substr(strtoupper($kdproduk), 0,2) == "EM"){
        $msg[$i+=1]="";//FIELD_CUSTOMER_NAME
        $msg[$i+=1]="";//FIELD_AMOUNT
        $msg[$i+=1]="";//FIELD_REFNO
        $msg[$i+=1]="";//FIELD_REFNO_2
        $msg[$i+=1]="";//FIELD_BILLER_CODE
        $msg[$i+=1]="";//FIELD_BILLER_STAN
        $msg[$i+=1]="";//FIELD_FEE_AMOUNT
        $msg[$i+=1]="";//FIELD_MERCHANT_ID
        $msg[$i+=1]="";//FIELD_MERCHANT_NAME
        $msg[$i+=1]="";//FIELD_MERCHANT_TYPE
        $msg[$i+=1]="";//FIELD_ADDT_DATA
        $msg[$i+=1]="";//FIELD_ADDT_DATA_2
        $msg[$i+=1]="";//FIELD_FORWARDING_ID
        $msg[$i+=1]="";//FIELD_TERMINAL_ID
        $msg[$i+=1]="";//FIELD_ISSUER_ID
        $msg[$i+=1]="";//FIELD_TRX_CODE
        $msg[$i+=1]="";//FIELD_POS_ENTRY_MOD
        $msg[$i+=1]="";//FIELD_SETTLEMENT_DATE
        $msg[$i+=1]="";//FIELD_CAPTURE_DATE
        $msg[$i+=1]="";//FIELD_APPROVAL_CODE
        $msg[$i+=1]="";//FIELD_ACC_NO
    }

    $fm         = convertFM($msg, "*");
    $hidepin = explode($pin, $fm);
   
    if(substr(strtoupper($kdproduk), 0,2) == "WA"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PLN"){
        if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNNON"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
        }
    }elseif(substr(strtoupper($kdproduk), 0,3) == "ASR"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "EM"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "KK"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "HP"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "FN"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "TV"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "GAS"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PGN"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,6) == "SPEEDY"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,7) == "TELEPON"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }else{
        die("Data Tidak Tersedia Didevel, Hub Team Bisnis!");
    }

    if($resp == "" || $resp == "null"){
        die("Data Tidak Tersedia Didevel");
    }

    $man        = FormatMsg::mandatoryPayment();   

    $frm        = new FormatMandatory($man["pay"], $resp); 
    $params     = setMandatoryResponJsonDev($frm, $ref1, "", "", $data);
    $frm        = getParseProduk($kdproduk, $resp);

     if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $adddata    = tambahdataproduk($kdproduk, $frm,$kodebank);
    }else{
        $adddata    = tambahdataproduk($kdproduk, $frm,'',1);
    }
    $adddata2   = tambahdataproduk2($kdproduk, $frm);
    // print_r($adddata);die();
   
    if ($frm->getStatus() == "00") {
        // $url        = enkripUrl(strtoupper($uid), $frm->getIdTrx());
        $url_struk  = array("url_struk" => "");
        $merge      = array_merge($params,$adddata,$adddata2,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2);
    }
  
    return json_encode($merge, JSON_PRETTY_PRINT);
}
function cekstatus($data_request) {
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    $i = -1;
    $tgl        = strtoupper($data_request->tgl);
    $ref1       = strtoupper($data_request->ref1);
    $idtrx      = strtoupper($data_request->ref2);
    $kdproduk   = strtoupper($data_request->kode_produk);
    $idpel1     = strtoupper($data_request->idpel1);
    $idpel2     = strtoupper($data_request->idpel2);
    $denom      = strtoupper($data_request->denom);
    $idoutlet   = strtoupper($data_request->uid);
    $pin        = strtoupper($data_request->pin);

    $field      = 10;
    if(count((array)$data_request) !== $field){
        return json_encode(array('error'=>'missing parameter request cek status'));
    }

    global $pgsql;
    // if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //     return json_encode(array('error'=>'access not allowed'));
    // }
    if ($kdproduk == "PLNPASC") {
        $kdproduk = "PLNPASCH";
    } else if ($kdproduk == "PLNPRA") {
        $idproduk = "PLNPRAH";
        // $kdproduk = "PLNPRAH";
    } else if ($kdproduk == "PLNNON") {
        $kdproduk = "PLNNONH";
    } else if ($kdproduk == "HPTSEL") {
        $kdproduk = "HPTSELH";
    }
    
    // if (in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
    //     $normal_idpel = normalisasiIdPel1PLNPra($idpel1, $idpel2);
    //     $idpel1 = $normal_idpel["idpel1"];
    //     $idpel2 = $normal_idpel["idpel2"];
    // }

    // if (in_array($kdproduk, KodeProduk::getPLNPostpaids())) {
    //     $normal_idpel = normalisasiIdPel1PLNPasc($idpel1);
    //     $idpel1 = $normal_idpel["idpel1"];
    // }
    

    if ($ref1 != "" || $idtrx != "" || $idpel1 != "" || $idpel2 != "" || $denom != "") {
        
        $data = getStatusProsesTransaksiDevel($tgl, $kdproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);

        $cnt = count($data);

        if ($cnt > 0) {
            if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPrepaids())) {
                $kdproduk = "PLNPRA";
                $sn = (string) trim($data["bill_info29"]);
            } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPostpaids())) {
                $kdproduk = "PLNPASC";
                $sn = "";
            } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNNontaglists())) {
                $kdproduk = "PLNNON";
                $sn = "";
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
            $data = getStatusTransaksiDevel($tgl, $kdproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);
            $cnt = count($data);
            if ($cnt > 0) {
                if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPrepaids())) {
                    $kdproduk = "PLNPRA";
                    $sn = (string) trim($data["bill_info29"]);
                } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPostpaids())) {
                    $kdproduk = "PLNPASC";
                    $sn = "";
                } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNNontaglists())) {
                    $kdproduk = "PLNNON";
                    $sn = "";
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

                $data = getStatusTransaksiBackupDevel($tgl, $kdproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);

                $cnt = count($data);
                if ($cnt > 0) {
                    if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPrepaids())) {
                        $kdproduk = "PLNPRA";
                        $sn = (string) trim($data["bill_info29"]);
                    } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNPostpaids())) {
                        $kdproduk = "PLNPASC";
                        $sn = "";
                    } else if (in_array((string) trim($data["id_produk"]), KodeProduk::getPLNNontaglists())) {
                        $kdproduk = "PLNNON";
                        $sn = "";
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
        $add_data = array(
            "IDTRANSAKSI" => (string) trim($data['id_transaksi']),
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

    if (in_array($kdproduk, KodeProduk::getPLNPrepaids()) && $status == "03" && strpos($ket, "sudah pernah terjadi status sukses")) {
        $key_token = "TOKEN=";
        $pos_token = strpos($ket, $key_token);
        $token = "";
        if ($pos_token > 0) {
            $token = substr($ket, $pos_token + strlen($key_token), 20);
        }
        $add_data["SN"] = $token;
    }

    $add_data = array_change_key_case($add_data, CASE_LOWER);
    $params = array(
        "TANGGAL" => (String) $tgl, 
        "REF1" => (String) $ref1, 
        "REF2" => (String) $idtrx, 
        "KODEPRODUK" => (String) $kdproduk, 
        "IDPEL1" => (String) $idpel1, 
        "IDPEL2" => (String) $idpel2, 
        "DENOM" => (String) $denom, 
        "UID" => (String) $idoutlet,
        "PIN" => (String) "------", 
        "STATUS" => (String) $status, 
        "KETERANGAN" => (String) $ket, 
        "RESULT" => $add_data
    );
    $params = array_change_key_case($params, CASE_LOWER);
    return json_encode($params);
}

function daftar($data){
    // global $pgsql;
    $nohp       = strtoupper($data->hp);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $nama       = strtoupper($data->nama);
    $email      = strtoupper($data->email);
    $alamat     = strtoupper($data->alamat);

    $field      = 7;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    if($nohp == "" || $nama == "" || $email == "" || $alamat == ""){
        return json_encode(array('error'=>"No hp, nama, alamat dan email harus diisi"));
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    if($idoutlet != "FA149315"){
        if (!checkHakAksesMP("", strtoupper($idoutlet))) {
            return json_encode(array('error'=>"Anda tidak punya hak akses"));
        }
    }

    if (!is_numeric($nohp)){
        return json_encode(array('error'=>"No hp tidak valid"));
    } 

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return json_encode(array('error'=>"Email tidak valid"));
    }

    // if (emailexists($email)) {
    //     return json_encode(array('error'=>"Email sudah terdaftar"));
    // }

    $search = array('.', ' ');
    $replace = array('', '');

    if (!ctype_alpha(str_replace($search, $replace, $nama))){
        return json_encode(array('error'=>"Nama hanya boleh huruf"));
    }
    
    // if (!outletexists($idoutlet)) {
    //     return json_encode(array('error'=>"ID Outlet tidak terdaftar atau tidak aktif"));
    // }

    // if (!checkpin($idoutlet, $pin)) {
    //     return json_encode(array('error'=>"pin yang anda masukkan salah"));
    // } 

    $stp    = $GLOBALS["step"] + 1;
    $msg    = array();
    $i      = -1;

    $msg[$i+=1] = "DAFTAR";
    $msg[$i+=1] = "DAFTAR";
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $nohp;
    $msg[$i+=1] = $nama; //nama
    $msg[$i+=1] = $alamat; //alamat
    $msg[$i+=1] = ""; //idpropinsi 
    $msg[$i+=1] = ""; //idkota
    $msg[$i+=1] = ""; //kodepos
    $msg[$i+=1] = "13"; //tipeloket
    $msg[$i+=1] = "0"; //flagregional
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = ""; /////
    $msg[$i+=1] = ""; //1
    $msg[$i+=1] = ""; //2
    $msg[$i+=1] = ""; //3
    $msg[$i+=1] = ""; //4
    $msg[$i+=1] = $email; //email

    $fm             = convertFM($msg, "*");
    
    $sender         = $GLOBALS["__G_module_name"];
    $receiver       = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    // $respon     = postValue($fm);
    // $resp       = $respon[7];
    $resp = "DAFTAR*DAFTAR*6528105*4**XML*$nohp*$nama*$alamat*5*347*65139*1*0*$idoutlet*$pin**99925*15794571*FA27160*640480*00*PENDAFTARAN NO. $nohp SUKSES, ID Outlet: FA27160. Silahkan melakukan aktifasi. Trx Normal dan Lancar";
    
    $format     = FormatMsg::daftar();
    $frm        = new FormatDaftar($format[1], $resp);

    $r_idoutlet = $frm->getMember();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
    $r_idoutletbaru = $frm->getMemberBaru();
    $r_hp = $frm->getNoHP();

    $params = array(
        "UID"           => $r_idoutlet,
        "PIN"           => '------',
        "STATUS"        => $r_status,
        "UID_MEMBER"    => $r_idoutletbaru,
        "HP_MEMBER"     => $r_hp,
        "KET"           => $r_keterangan
    );

    return json_encode($params, JSON_PRETTY_PRINT);
}

function data_transaksi($data){
  
    $next   = FALSE;
    $end    = FALSE;

    $tgl1       = strtoupper($data->tgl1);
    $tgl2       = strtoupper($data->tgl2);
    $idtrx      = strtoupper($data->id_transaksi);
    $idproduk   = strtoupper($data->id_produk);
    $idpel      = strtoupper($data->idpel);
    $limit      = strtoupper($data->limit);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $custreff   = strtoupper($data->ref1);
    $field      = 10;
    $field2     = 9;
  
   

    if(count((array)$data) !== $field && count((array)$data) !== $field2){
        return json_encode(array('error'=>'missing parameter request'));
    }

    if($idproduk == 'HPTSEL'){
        $idproduk = 'HPTSELH';
    } else if($idproduk == 'ASRBPJSKS'){
        $idproduk = 'ASRBPJSKSH';
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    // global $pgsql;
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        return json_encode(array('error'=>"Anda tidak punya hak akses"));
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
        $receiver   = $GLOBALS["__G_receiver"];
        global $host;
        $msg[7]     = "00";
        $data       = getDataTransaksi_devel($idoutlet, $idtrx, $idproduk, $idpel, $limit, $tgl1, $tgl2, $custreff);
      
        // print_r($data);
        $cnt        = count($data);
        $narindo_rc = array("1", "2", "3", "5", "12", "15", "21", "35", "68", "80","4");
        $gigaotomax_rc = array("35", "68", "XX", "05");
        $eratel_rc = array("68", "57");
        $servindo_rc = array("35");
        $gigaotomax_rc_replace = array("16");
       // echo  $data[0]->id_transaksi;die();
        if ($cnt > 0) {
            $msgcontent[] = "Transaksi berhasil";
            //Looping
            for ($i = 0; $i < $cnt; $i++) {
                $datatransaksi = array();
                $datatransaksi[] = $data[$i]->id_transaksi;

                $datatransaksi[] = $data[$i]->transaksidatetime;
                                
                if($data[$i]->id_produk == 'HPTSELH'){
                    $datatransaksi[] = $data[$i]->id_produk = "HPTSELH";
                } else if($data[$i]->id_produk == 'ASRBPJSKSH'){
                    $datatransaksi[] = $data[$i]->id_produk = "ASRBPJSKSH";
                } else {
                    $datatransaksi[] = $data[$i]->id_produk;
                }

                $datatransaksi[] = $data[$i]->namaproduk;
                $datatransaksi[] = $data[$i]->idpelanggan;

                if($data[$i]->response_code == "" || $data[$i]->response_code == NULL){
                    $datatransaksi[] = $data[$i]->response_code = "00";
                    $datatransaksi[] = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else if($data[$i]->id_biller == "192" && in_array($data[$i]->response_code, $gigaotomax_rc) && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') !== false){
                    //biller gigapulsa otomax
                    $datatransaksi[] = $data[$i]->response_code = "00";
                    $datatransaksi[] = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else if($data[$i]->id_biller == "192" && in_array($data[$i]->response_code, $gigaotomax_rc_replace)){
                    //biller gigapulsa otomax ganti keterangan lek rc 16
                    $datatransaksi[] = $data[$i]->response_code;
                    $datatransaksi[] = $data[$i]->keterangan = "Transaksi ".$data[$i]->id_produk." tujuan: ".$data[$i]->idpelanggan." gagal.";
                } else if(in_array($data[$i]->response_code, $narindo_rc) && $data[$i]->id_biller == '169'){
                    //biller narindo
                    $datatransaksi[] = $data[$i]->response_code = "00";
                    $datatransaksi[] = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else if(in_array($data[$i]->response_code, $eratel_rc) && $data[$i]->id_biller == '160'){
                    //biller ERATEL
                    $datatransaksi[] = $data[$i]->response_code = "00";
                    $datatransaksi[] = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else if(in_array($data[$i]->response_code, $servindo_rc) && $data[$i]->id_biller == '26' && strpos(strtoupper($data[$i]->keterangan), 'SEDANG DIPROSES') !== false){
                    //biller SERVINDO
                    $datatransaksi[] = $data[$i]->response_code = "00";
                    $datatransaksi[] = $data[$i]->keterangan = "SEDANG DIPROSES";
                } else {
                    $datatransaksi[] = $data[$i]->response_code;
                    $arrDesc = explode(' ', trimed($data[$i]->keterangan));
                    $desc = $arrDesc[0] . ' ' . $arrDesc[1];
                    if ($arrDesc[0] == 'SUKSES' && $arrDesc[1] == 'OLEH') {
                        $desc = $arrDesc[0].' '.$arrDesc[1].' ADMIN'; 
                    }elseif ($arrDesc[0] == 'SUKSES' && $arrDesc[1] == 'PAKSA') {
                        $desc = $arrDesc[0].' '.$arrDesc[1].' OLEH ADMIN'; 
                    }else{
                        if($arrDesc[0] == 'Gagal' && $arrDesc[1] == 'Manual'){
                            $desc = $arrDesc[0].' '.$arrDesc[1].' Oleh Admin'; 
                        }else{
                            $desc = str_replace("#",'\/',$data[$i]->keterangan);
                        }
                    }
                    $datatransaksi[] = $desc;
                }

                $datatransaksi[] = $data[$i]->nominal;
                if(in_array($data[$i]->id_produk, KodeProduk::getPLNPrepaids())){
                    $datatransaksi[] = $data[$i]->token;
                } else {
                    $datatransaksi[] = str_replace("#",'\/',$data[$i]->sn);
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
                
                $datatransaksi[] = $status_trx;

                $result_data_trx_log = implode("#", $datatransaksi);
                $get_step = get_step_from_mid($data[$i]->mid) + 1;
                // writeLog($data[$i]->mid, $get_step, $receiver, $host, $result_data_trx_log, $GLOBALS["via"]);

                $msgcontent[] = implode("#", $datatransaksi);
            }
            //End of looping
        } else {
            $msg[7] = "06";
            $msgcontent[] = "Tidak ada data transaksi sesuai kriteria yang di-request";
        }
    }
    //Msg Constructor
    $resp           = implode("*", $msg);
    $respcontent    = implode("~", $msgcontent);
    $arr_resp[]     = $resp;
    $arr_resp[]     = $respcontent;
    $response       = implode("*", $arr_resp);

    $format = FormatMsg::dataTransaksi();
    $frm    = new FormatDataTransaksi($format, $response);
    //print_r($frm->data);
    //TANGGAL1*TANGGAL2*KDPRODUK*IDPEL*LIMIT*IDOUTLET*PIN*RESPONSECODE*CONTENT

    $r_tanggal1 = $frm->getTanggal1();
    $r_tanggal2 = $frm->getTanggal2();
    $r_kdproduk = $frm->getKodeProduk();
    $r_idpel    = $frm->getIdPel();
    $r_limit    = $frm->getLimit();
    $r_idoutlet = $frm->getIdOutlet();
    $r_pin      = $frm->getPin();
    $r_responsecode = $frm->getResponseCode();
    $r_content      = $frm->getContent();
    $ket            = explode("~", $r_content);
    $r_keterangan   = $ket[0];
    

    $params = array(
        'TGL1'          => $r_tanggal1, 
        'TGL2'          => $r_tanggal2, 
        'KODE_PRODUK'   => $r_kdproduk, 
        'IDPEL'         => $r_idpel, 
        'LIMIT'         => $r_limit, 
        'UID'           => $r_idoutlet, 
        'PIN'           => "------", 
        'STATUS'        => $r_responsecode, 
        'KET'           => $r_keterangan,

    );

    $r_content = explode("~", $r_content);
    
    $result_data = array();
    for ($i = 0; $i < count($r_content); $i++) {
        array_push($result_data, $r_content[$i]);
    }
    unset($result_data[0]);
    $params['RESULT_TRANSAKSI'] = array_values($result_data);

    
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

    $tgl        = date('Y-m-d H:i:s');
    $log_data   = "\n\n========================".$tgl."========================\n";
    $log_data   .= json_encode($req);
    $log_data   .= "\n\n";
    $log_data   .= json_encode($params);
    $log_data   .= "========================".$tgl."========================\n";
    // write_log_text($log_data);
    //writeLog($GLOBALS["mid"], $GLOBALS["step"] + 1, $sender, $receiver, $log_data, $GLOBALS["via"]);
    return json_encode($params, JSON_PRETTY_PRINT);
}

function balance($data){
    //GPIN*PINBARU*IDOUTLET*PIN*TOKEN*VIA
    $i = -1;
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $field      = 3;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    
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
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $resp = "SAL*SAL*201321555*4**H2H*" . $uid . "*".$pin."**257390*127382587*00*Saldo DUMMY Anda saat ini = Rp 257,390 Sms center 081228899888 dan 087838395999.";

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
        "KET"       => $r_keterangan
    );

    return json_encode($params, JSON_PRETTY_PRINT);
}

function harga($data) {
    $i = -1;

    $produk = strtoupper($data->produk);
    $uid    = strtoupper($data->uid);
    $pin    = strtoupper($data->pin);
    $field  = 4;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
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
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    
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

    return json_encode($params, JSON_PRETTY_PRINT);
}

function cek_is_telp_or_speedy($idpel){
    $prefix_telp = array('0627','0629','0641','0642','0643','0644','0645','0646','0650','0651','0652','0653','0654','0655','0656','0657','0658','0659','061','0620','0621','0622','0623','0624','0625','0626','0627','0628','0630','0631','0632','0633','0634','0635','0636','0639','0751','0752','0753','0754','0755','0756','0757','0759','0760','0761','0762','0763','0764','0765','0766','0767','0768','0769','0624','0770','0771','0772','0773','0776','0777','0778','0779','0740','0741','0742','0743','0744','0745','0746','0747','0748','0702','0711','0712','0713','0714','0730','0731','0733','0734','0735','0715','0716','0717','0718','0719','0732','0736','0737','0738','0739','0721','0722','0723','0724','0725','0726','0727','0728','0729','021','021','0252','0253','0254','0257','021','022','0231','0232','0233','0234','0251','0260','0261','0262','0263','0264','0265','0266','0267','024','0271','0272','0273','0274','0275','0276','0280','0281','0282','0283','0284','0285','0286','0287','0289','0291','0292','0293','0294','0295','0296','0297','0298','0299','0356','0274','031','0321','0322','0323','0324','0325','0327','0328','0331','0332','0333','0334','0335','0336','0338','0341','0342','0343','0351','0352','0353','0354','0355','0356','0357','0358','0361','0362','0363','0365','0366','0368','0364','0370','0371','0372','0373','0374','0376','0380','0381','0382','0383','0384','0385','0386','0387','0388','0389','0561','0562','0563','0564','0565','0567','0568','0534','0513','0522','0525','0526','0528','0531','0532','0536','0537','0538','0539','0511','0512','0517','0518','0526','0527','0541','0542','0543','0545','0548','0549','0554','0551','0552','0553','0556','0430','0431','0432','0434','0438','0435','0443','0445','0450','0451','0452','0453','0454','0457','0458','0461','0462','0463','0464','0465','0455','0422','0426','0428','0410','0411','0413','0414','0417','0418','0419','0420','0421','0423','0427','0471','0472','0473','0474','0475','0481','0482','0484','0485','0401','0402','0403','0404','0405','0408','0910','0911','0913','0914','0915','0916','0917','0918','0921','0922','0923','0924','0927','0929','0931','0901','0902','0951','0952','0955','0956','0957','0966','0967','0969','0971','0975','0980','0981','0983','0984','0985','0986');

    $tiga = 3;
    $empat = 4;
    $is_telp = FALSE;

    $sub_str1 = substr($idpel, 0, $empat);
    $sub_str2 = substr($idpel, 0, $tiga);


    // ngecek 4 digit dulu baru 3 digit

    if(in_array($sub_str1, $prefix_telp)){
        $is_telp = TRUE;
        $len = strlen($sub_str1);
        $ret = array(
            'produk'    => 'TELEPON',
            'idpel1'    => $sub_str1,
            'idpel2'    => substr($idpel,$len)
        );
    } else if(in_array($sub_str2, $prefix_telp)){
        $is_telp = TRUE;
        $len = strlen($sub_str2);
        $ret = array(
            'produk'    => 'TELEPON',
            'idpel1'    => $sub_str2,
            'idpel2'    => substr($idpel,$len)
        );
    }
    if($is_telp){
        return $ret;
    } else {
        return $ret;    
    }
}

function inq($data){
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN

    $i = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $field      = 8;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    if(substr($kdproduk, 0, 2) == 'KK'){
        $nominal   = strtoupper($data->nominal);
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }
    // global $pgsql;

    // if($kdproduk == "TELEPON" || $kdproduk == "SPEEDY"){
    //     $cek_telkom = cek_is_telp_or_speedy($idpel1);

    //     print_r($cek_telkom);die();
    //     $kdproduk   = $cek_telkom['produk'];
    //     $idpel1     = $cek_telkom['idpel1'];
    //     $idpel2     = $cek_telkom['idpel2'];
    // }


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
    $msg[$i+=1] = "";
    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>";die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
    $tgl = date('YmdHis');


    if(substr(strtoupper($kdproduk), 0,2) == "WA"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PLN"){
        if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNNON"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
        }
    }elseif(substr(strtoupper($kdproduk), 0,3) == "ASR"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "EM"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "KK"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "HP"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "FN"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "TV"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "GAS"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PGN"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,6) == "SPEEDY"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }elseif(substr(strtoupper($kdproduk), 0,7) == "TELEPON"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"TAGIHAN");
    }else{
        die("Data Tidak Tersedia Didevel, Hub Team Bisnis!");
    }


    if($resp == "" || $resp == "null"){
        die("Data Tidak Tersedia Didevel");
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $frm = getParseProduk($kdproduk, $resp);


    //print_r($frm->data);
    //TAGIHAN*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $r_step         = $frm->getStep()+1;
    $r_kdproduk     = $frm->getKodeProduk();
    $r_tanggal      = $frm->getTanggal();
    $r_idpel1       = $frm->getIdPel1();
    $r_idpel2       = $frm->getIdPel2();
    $r_idpel3       = $frm->getIdPel3();
    $r_nominal      = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet     = $idoutlet;
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
        "IDPEL1"           => (string) $r_idpel1,
        "IDPEL2"           => (string) $r_idpel2,
        "IDPEL3"           => (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
        "PERIODE"           => (string) $r_periode_tagihan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => "0",
        "STATUS"            => (string) $r_status,
        "KET"               => (string) $r_keterangan,
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) $url_struk
    );

    return json_encode($params, JSON_PRETTY_PRINT);
}

function pay($data) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $nominal    = strtoupper($data->nominal);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $ref3       = strtoupper($data->ref3);
    $field      = 11;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }



    $ip = $_SERVER['REMOTE_ADDR'];

    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }
    
    // global $pgsql;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
    }

    // $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan
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
    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,2) == "WA"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PLN"){
        if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
            $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNNON"){
            $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
        }
    }elseif(substr(strtoupper($kdproduk), 0,3) == "ASR"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "EM"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "KK"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "HP"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "FN"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "TV"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "GAS"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PGN"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,6) == "SPEEDY"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,7) == "TELEPON"){
        $resp = getMesssageDev($kdproduk , $idpel1, "BAYAR");
    }else{
        die("Data Tidak Tersedia Didevel, Hub Team Bisnis!");
    }


    if($resp == "" || $resp == "null"){
        die("Data Tidak Tersedia Didevel");
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
    if ($frm->getStatus() == "00" && strpos(strtoupper($r_keterangan), 'PROSES') === false) {
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
        "PERIODE"           => (string) $r_periode_tagihan,
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
        "URL_STRUK"         => (string) $url_struk
    );

    $is_return = true;
 
    // if($r_idtrx != $ref2){
    //     // $text = pay_resp_text($params);
    //     $get_mid = get_mid_from_idtrx($r_idtrx);
    //     $get_step = get_step_from_mid($get_mid) + 1;
    //     // writeLog($get_mid, $get_step, $host, $receiver, json_encode($params), $via);
    // }
    
    return json_encode($params, JSON_PRETTY_PRINT);
}

function pay_detail($data) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel1);
    $idpel2     = strtoupper($data->idpel2);
    $idpel3     = strtoupper($data->idpel3);
    $nominal    = strtoupper($data->nominal);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $ref3       = strtoupper($data->ref3);
    $field      = 11;
    
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    // global $pgsql;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
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
    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];

     if(substr(strtoupper($kdproduk), 0,2) == "WA"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PLN"){
        if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
        }elseif(substr(strtoupper($kdproduk), 0,6) == "PLNNON"){
            $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
        }
    }elseif(substr(strtoupper($kdproduk), 0,3) == "ASR"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "EM"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "KK"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "HP"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "FN"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,2) == "TV"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "GAS"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,3) == "PGN"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,6) == "SPEEDY"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }elseif(substr(strtoupper($kdproduk), 0,7) == "TELEPON"){
        $resp = getMesssageDev($kdproduk , $idpel1 ,"BAYAR");
    }else{
        die("Data Tidak Tersedia Didevel, Hub Team Bisnis!");
    }


    if($resp == "" || $resp == "null"){
        die("Data Tidak Tersedia Didevel");
    }

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
    $r_idoutlet         = $idoutlet;
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

      if($r_status === '35' || $r_status === '68'){
        $r_status = '35';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
    }

    if($r_status === '00' && ($r_keterangan !== "" && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') === false ) && $r_saldo_terpotong !== "0" && $r_sisa_saldo !== ""){
        $status_trx = "SUKSES";
    } else if( ($r_status === '00' || $r_status === '05')  && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false){
        $status_trx = "PENDING";
        $r_status = "35";
    } else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "35";
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
    }else if($r_status == '00') {
        $status_trx = "SUKSES";
    }else if($r_status !== '00') {
        $status_trx = "GAGAL";
    } else {
        $r_status = '35';
        $status_trx = "PENDING";
    }

    if($r_status === '68' && getIdBiller($r_idtrx) === '192'){
        //biller giga otomax
        $r_status = '35';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
    }

    $url_struk = "";
    if ($frm->getStatus() == "00" && strpos(strtoupper($r_keterangan), 'PROSES') === false) {
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
        "IDPEL1"            => (string) $r_idpel1,
        "IDPEL2"            => (string) $r_idpel2,
        "IDPEL3"            => (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
        "PERIODE"           => (string) $r_periode_tagihan,
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
        "URL_STRUK"         => (string) $url_struk,
    );

    if (count($r_additional_datas) > 0) {
        $params['DETAIL'] = $r_additional_datas;
    }

    $is_return = true;
   
    // if($r_idtrx != $ref2){
    //     // $text = paydetil_resp_text($params, $kdproduk);
    //     $get_mid = get_mid_from_idtrx($r_idtrx);
    //     $get_step = get_step_from_mid($get_mid) + 1;
    //     writeLog($get_mid, $get_step, $host, $receiver, json_encode($params), $via);
    // }
    
    // return new xmlrpcresp(php_xmlrpc_encode($params));
    return json_encode($params, JSON_PRETTY_PRINT);
}

function pulsa($data) {
    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $nohp       = strtoupper($data->no_hp);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $field      = 6;
    $tanggal = date("Ymdhis");
    $cek = substr($nohp, 0, 2);

    
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = $_SERVER['REMOTE_ADDR'];

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
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    if(substr(strtoupper($kdproduk), 0,1) == "S"){
        if($nohp == "08128492106"){
             sleep(2);
        header("HTTP/1.0 504 Gateway Time-out");
        die();
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*08128492106*10750*".$idoutlet."*------*------***665254**00*SEDANG DIPROSES";
        }elseif($nohp == "081743752969"){
             sleep(2);
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*085743752969*".getHarga($kdproduk)."*".$idoutlet."*------****665254**00*SEDANG DIPROSES";
              $update = updatedataDevel($idoutlet,$nohp,$ref1,getHarga($kdproduk));
        }elseif($nohp == "08128492110"){
            $resp="PULSA*".$kdproduk."*4649101410*4*20210421141200*H2H*08128492110**".$idoutlet."*------****16797*2104456758*02*Username/PIN tidak cocok. Mohon maaf. ";
        }elseif($nohp == "08128492109"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492109*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*18*TRX pulsa ".$kdproduk." 0811111 GAGAL. Nomor ponsel tidak benar, digit kurang atau lebih.";
        }elseif($nohp == "08128492108"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492108*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492108 duplikat transaksi status akhir : Order telah sukses SN=02265300014414342223!";
        }elseif($nohp == "08128492112"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492112*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727431*07*Layanan ".$kdproduk." sedang dalam gangguan. Mohon maaf.";
        }elseif($nohp == "08128492113"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492113*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727134*35*WAKTU TRANSAKSI TELAH HABIS, COBA BEBERAPA SAAT LAGI";
        }elseif($nohp == "08128492114"){
                 $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492114*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**45682*2083727125*06*SALDO ANDA TIDAK MENCUKUPI";
        }elseif($nohp == "08128492118"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492118*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*19* Maaf, Id anda tidak terdaftar.";
        }elseif($nohp == "08128492101"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492101*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*19*EXT: Transaksi gagal   ";
        }elseif($nohp == "08128492115"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492115*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*13*Transaksi ditolak karena sistem sedang melakukan proses cut off (23.55 - 00.10)";
        }elseif($nohp == "08128492119"){
             $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*08128492119**".$idoutlet."*------*****2089715102*04*Layanan ".$kdproduk." belum tersedia. Mohon maaf. ";
        }elseif($nohp == "08128492111"){
              $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492111*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727132*21* Maaf, Id anda tidak dapat digunakan untuk transaksi produk ini.";
        }elseif($nohp == "08128492100"){

            if($ref1 == "123456")
            {
                $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*08128492100*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 08128492100 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }elseif ($ref1 == "1234567") {
                $resp = "PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08128492100*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492100 duplikat transaksi status akhir : Order telah sukses SN=2020302332021040808530836!";
            }else{
                 $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*08128492100*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 08128492100 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }
           
        }  
   }elseif(substr(strtoupper($kdproduk), 0,1) == "I"){
        if($nohp == "08128492106"){
            sleep(2);
        header("HTTP/1.0 504 Gateway Time-out");
        die();
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*08128492106*".getHarga($kdproduk)."*".$idoutlet."*------*------***665254**00*SEDANG DIPROSES";
        }elseif($nohp == "085743752969"){
             sleep(2);
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*085743752969*".getHarga($kdproduk)."*".$idoutlet."*------****665254**00*SEDANG DIPROSES";
              $update = updatedataDevel($idoutlet,$nohp,$ref1,getHarga($kdproduk));
        }elseif($nohp == "085648889100"){
            $resp="PULSA*".$kdproduk."*4649101410*4*20210421141200*H2H*085648889100**".$idoutlet."*------****16797*2104456758*02*Username/PIN tidak cocok. Mohon maaf. ";
        }elseif($nohp == "085648889111"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889111*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*18*TRX pulsa ".$kdproduk." 0811111 GAGAL. Nomor ponsel tidak benar, digit kurang atau lebih.";
        }elseif($nohp == "085648889122"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889122*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492108 duplikat transaksi status akhir : Order telah sukses SN=02265300014414342223!";
        }elseif($nohp == "085648889133"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889133*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727431*07*Layanan ".$kdproduk." sedang dalam gangguan. Mohon maaf.";
        }elseif($nohp == "085648889162"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889162*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727134*35*WAKTU TRANSAKSI TELAH HABIS, COBA BEBERAPA SAAT LAGI";
        }elseif($nohp == "085648889123"){
                 $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889123*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**45682*2083727125*06*SALDO ANDA TIDAK MENCUKUPI";
        }elseif($nohp == "085648889143"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889143*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*19* Maaf, Id anda tidak terdaftar.";
        }elseif($nohp == "085648889145"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889145*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*19*EXT: Transaksi gagal   ";
        }elseif($nohp == "085648889190"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889190*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*13*Transaksi ditolak karena sistem sedang melakukan proses cut off (23.55 - 00.10)";
        }elseif($nohp == "085648889188"){
             $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*085648889188**".$idoutlet."*------*****2089715102*04*Layanan ".$kdproduk." belum tersedia. Mohon maaf. ";
        }elseif($nohp == "085648889181"){
               $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*085648889181*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727132*21* Maaf, Id anda tidak dapat digunakan untuk transaksi produk ini.";
        }elseif($nohp == "08558492110"){
            if($ref1 == "123456")
            {
                $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*08558492110*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 08558492110 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }elseif ($ref1 == "1234567") {
                $resp = "PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*08558492110*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08558492110 duplikat transaksi status akhir : Order telah sukses SN=2020302332021040808530836!";
            }else{
                 $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*08558492110*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 08558492110 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }
           
        }  
   }elseif(substr(strtoupper($kdproduk), 0,2) == "AX" || substr(strtoupper($kdproduk), 0,2) == "XR"){
        if($nohp == "087704383111"){
            sleep(2);
        header("HTTP/1.0 504 Gateway Time-out");
        die();
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*087704383111*".getHarga($kdproduk)."*".$idoutlet."*------*------***665254**00*SEDANG DIPROSES";
        }elseif($nohp == "087704383120"){
             sleep(2);
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*087704383120*".getHarga($kdproduk)."*".$idoutlet."*------****665254**00*SEDANG DIPROSES";
              $update = updatedataDevel($idoutlet,$nohp,$ref1,getHarga($kdproduk));
        }elseif($nohp == "087704383636"){
            $resp="PULSA*".$kdproduk."*4649101410*4*20210421141200*H2H*087704383636**".$idoutlet."*------****16797*2104456758*02*Username/PIN tidak cocok. Mohon maaf. ";
        }elseif($nohp == "087704383677"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383677*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*18*TRX pulsa ".$kdproduk." 0811111 GAGAL. Nomor ponsel tidak benar, digit kurang atau lebih.";
        }elseif($nohp == "087704383652"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383652*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492108 duplikat transaksi status akhir : Order telah sukses SN=02265300014414342223!";
        }elseif($nohp == "087704383651"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383651*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727431*07*Layanan ".$kdproduk." sedang dalam gangguan. Mohon maaf.";
        }elseif($nohp == "087704383657"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383657*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727134*35*WAKTU TRANSAKSI TELAH HABIS, COBA BEBERAPA SAAT LAGI";
        }elseif($nohp == "087704383660"){
                 $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383660*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**45682*2083727125*06*SALDO ANDA TIDAK MENCUKUPI";
        }elseif($nohp == "087704383665"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383665*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*19* Maaf, Id anda tidak terdaftar.";
        }elseif($nohp == "087704383669"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383669*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*19*EXT: Transaksi gagal   ";
        }elseif($nohp == "087704383670"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383670*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*13*Transaksi ditolak karena sistem sedang melakukan proses cut off (23.55 - 00.10)";
        }elseif($nohp == "087704383680"){
             $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*087704383680**".$idoutlet."*------*****2089715102*04*Layanan ".$kdproduk." belum tersedia. Mohon maaf. ";
        }elseif($nohp == "087704383699"){
               $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383699*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727132*21* Maaf, Id anda tidak dapat digunakan untuk transaksi produk ini.";
        }elseif($nohp == "087704383100"){
            if($ref1 == "123456")
            {
                $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*087704383100*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 087704383100 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }elseif ($ref1 == "1234567") {
                $resp = "PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383100*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492110 duplikat transaksi status akhir : Order telah sukses SN=2020302332021040808530836!";
            }else{
                 $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*087704383100*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 087704383100 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }     
        }  
   }elseif(substr(strtoupper($kdproduk), 0,2) == "AX" || substr(strtoupper($kdproduk), 0,2) == "XR"){
        if($nohp == "087704383111"){
            sleep(2);
        header("HTTP/1.0 504 Gateway Time-out");
        die();
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*087704383111*".getHarga($kdproduk)."*".$idoutlet."*------*------***665254**00*SEDANG DIPROSES";
        }elseif($nohp == "087704383120"){
             sleep(2);
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*087704383120*".getHarga($kdproduk)."*".$idoutlet."*------****665254**00*SEDANG DIPROSES";
              $update = updatedataDevel($idoutlet,$nohp,$ref1,getHarga($kdproduk));
        }elseif($nohp == "087704383636"){
            $resp="PULSA*".$kdproduk."*4649101410*4*20210421141200*H2H*087704383636**".$idoutlet."*------****16797*2104456758*02*Username/PIN tidak cocok. Mohon maaf. ";
        }elseif($nohp == "087704383677"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383677*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*18*TRX pulsa ".$kdproduk." 0811111 GAGAL. Nomor ponsel tidak benar, digit kurang atau lebih.";
        }elseif($nohp == "087704383652"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383652*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492108 duplikat transaksi status akhir : Order telah sukses SN=02265300014414342223!";
        }elseif($nohp == "087704383651"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383651*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727431*07*Layanan ".$kdproduk." sedang dalam gangguan. Mohon maaf.";
        }elseif($nohp == "087704383657"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383657*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727134*35*WAKTU TRANSAKSI TELAH HABIS, COBA BEBERAPA SAAT LAGI";
        }elseif($nohp == "087704383660"){
                 $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383660*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**45682*2083727125*06*SALDO ANDA TIDAK MENCUKUPI";
        }elseif($nohp == "087704383665"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383665*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*19* Maaf, Id anda tidak terdaftar.";
        }elseif($nohp == "087704383669"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383669*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*19*EXT: Transaksi gagal   ";
        }elseif($nohp == "087704383670"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383670*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*13*Transaksi ditolak karena sistem sedang melakukan proses cut off (23.55 - 00.10)";
        }elseif($nohp == "087704383680"){
             $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*087704383680**".$idoutlet."*------*****2089715102*04*Layanan ".$kdproduk." belum tersedia. Mohon maaf. ";
        }elseif($nohp == "087704383699"){
               $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383699*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727132*21* Maaf, Id anda tidak dapat digunakan untuk transaksi produk ini.";
        }elseif($nohp == "087704383100"){
            if($ref1 == "123456")
            {
                $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*087704383100*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 087704383100 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }elseif ($ref1 == "1234567") {
                $resp = "PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*087704383100*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492110 duplikat transaksi status akhir : Order telah sukses SN=2020302332021040808530836!";
            }else{
                 $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*087704383100*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 087704383100 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }     
        }  
   }elseif(substr(strtoupper($kdproduk), 0,1) == "T"){
    if($nohp == "089604383111"){
        sleep(2);
    header("HTTP/1.0 504 Gateway Time-out");
    die();
        $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*089604383111*".getHarga($kdproduk)."*".$idoutlet."*------*------***665254**00*SEDANG DIPROSES";
    }elseif($nohp == "089604383120"){
         sleep(2);
        $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*089604383120*".getHarga($kdproduk)."*".$idoutlet."*------****665254**00*SEDANG DIPROSES";
          $update = updatedataDevel($idoutlet,$nohp,$ref1,getHarga($kdproduk));
    }elseif($nohp == "089604383636"){
        $resp="PULSA*".$kdproduk."*4649101410*4*20210421141200*H2H*089604383636**".$idoutlet."*------****16797*2104456758*02*Username/PIN tidak cocok. Mohon maaf. ";
    }elseif($nohp == "089604383677"){
        $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383677*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*18*TRX pulsa ".$kdproduk." 0811111 GAGAL. Nomor ponsel tidak benar, digit kurang atau lebih.";
    }elseif($nohp == "089604383652"){
         $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383652*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492108 duplikat transaksi status akhir : Order telah sukses SN=02265300014414342223!";
    }elseif($nohp == "089604383651"){
        $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383651*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727431*07*Layanan ".$kdproduk." sedang dalam gangguan. Mohon maaf.";
    }elseif($nohp == "089604383657"){
         $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383657*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727134*35*WAKTU TRANSAKSI TELAH HABIS, COBA BEBERAPA SAAT LAGI";
    }elseif($nohp == "089604383660"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383660*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**45682*2083727125*06*SALDO ANDA TIDAK MENCUKUPI";
    }elseif($nohp == "089604383665"){
        $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383665*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*19* Maaf, Id anda tidak terdaftar.";
    }elseif($nohp == "089604383669"){
        $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383669*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*19*EXT: Transaksi gagal   ";
    }elseif($nohp == "089604383670"){
         $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383670*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*13*Transaksi ditolak karena sistem sedang melakukan proses cut off (23.55 - 00.10)";
    }elseif($nohp == "089604383680"){
         $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*089604383680**".$idoutlet."*------*****2089715102*04*Layanan ".$kdproduk." belum tersedia. Mohon maaf. ";
    }elseif($nohp == "089604383699"){
           $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383699*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727132*21* Maaf, Id anda tidak dapat digunakan untuk transaksi produk ini.";
    }elseif($nohp == "089604383100"){
        if($ref1 == "123456")
        {
            $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*089604383100*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 089604383100 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
        }elseif ($ref1 == "1234567") {
            $resp = "PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*089604383100*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492110 duplikat transaksi status akhir : Order telah sukses SN=2020302332021040808530836!";
        }else{
             $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*089604383100*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 089604383100 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
        }     
    }  
}elseif(substr(strtoupper($kdproduk), 0,2) == "CM"){
        if($nohp == "0882015886867"){
            sleep(2);
        header("HTTP/1.0 504 Gateway Time-out");
        die();
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*0882015886867*".getHarga($kdproduk)."*".$idoutlet."*------*------***665254**00*SEDANG DIPROSES";
        }elseif($nohp == "0882015886888"){
             sleep(2);
            $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*0882015886888*".getHarga($kdproduk)."*".$idoutlet."*------****665254**00*SEDANG DIPROSES";
              $update = updatedataDevel($idoutlet,$nohp,$ref1,getHarga($kdproduk));
        }elseif($nohp == "0882015886873"){
            $resp="PULSA*".$kdproduk."*4649101410*4*20210421141200*H2H*0882015886873**".$idoutlet."*------****16797*2104456758*02*Username/PIN tidak cocok. Mohon maaf. ";
        }elseif($nohp == "0882015886100"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886100*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*18*TRX pulsa ".$kdproduk." 0811111 GAGAL. Nomor ponsel tidak benar, digit kurang atau lebih.";
        }elseif($nohp == "0882015886854"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886854*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 0882015886854 duplikat transaksi status akhir : Order telah sukses SN=02265300014414342223!";
        }elseif($nohp == "0882015886811"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886811*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727431*07*Layanan ".$kdproduk." sedang dalam gangguan. Mohon maaf.";
        }elseif($nohp == "0882015886821"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886821*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727134*35*WAKTU TRANSAKSI TELAH HABIS, COBA BEBERAPA SAAT LAGI";
        }elseif($nohp == "0882015886810"){
                 $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886810*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**45682*2083727125*06*SALDO ANDA TIDAK MENCUKUPI";
        }elseif($nohp == "0882015886123"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886123*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*19* Maaf, Id anda tidak terdaftar.";
        }elseif($nohp == "0882015886885"){
            $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886885*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**4267205682*2083727111*19*EXT: Transaksi gagal   ";
        }elseif($nohp == "0882015886864"){
             $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886864*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*20837271189*13*Transaksi ditolak karena sistem sedang melakukan proses cut off (23.55 - 00.10)";
        }elseif($nohp == "0882015886830"){
             $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*0882015886830**".$idoutlet."*------*****2089715102*04*Layanan ".$kdproduk." belum tersedia. Mohon maaf. ";
        }elseif($nohp == "0882015886828"){
               $resp="PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886828*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727132*21* Maaf, Id anda tidak dapat digunakan untuk transaksi produk ini.";
        }elseif($nohp == "0882015886432"){
            if($ref1 == "123456")
            {
                $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*0882015886432*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 0882015886432 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }elseif ($ref1 == "1234567") {
                $resp = "PULSA*".$kdproduk."*4618735050*7*20210402124118*H2H*0882015886432*".getHarga($kdproduk)."*".$idoutlet."*------**".$kdproduk."**405682*2083727111*15*TRX Paket Data ".$kdproduk." 08128492110 duplikat transaksi status akhir : Order telah sukses SN=2020302332021040808530836!";
            }else{
                 $resp="PULSA*".$kdproduk."*4627688460*4*20210408085519*H2H*0882015886432*".getHarga($kdproduk)."*".$idoutlet."*------***2020302332021040808530836**2089715162*00*Pengisian pulsa ".$kdproduk." Anda ke nomor 0882015886432 BERHASIL. SN=2020302332021040808530836 Harga=".getHarga($kdproduk)."  ";
            }     
        }  
   }else if( ($nohp == '08128492372' || $nohp == '6285648889293') && ($kdproduk == 'I25H' || $kdproduk == 'I5H' || $kdproduk == 'IT5H' || $kdproduk == 'I5' || $kdproduk == 'I10H' || $kdproduk == 'ID1H' || $kdproduk == 'ID1') ){
        sleep(4);
        $resp="PULSA*".$kdproduk."*805817646*10*".$tanggal."*H2H*".$nohp."*".getHarga($kdproduk)."*".$idoutlet."*------*------***665254*437110932*00*SEDANG DIPROSES";
    } else if ($kdproduk == "CM25H") {

        if($nohp == "088200921899"){
            $resp = "PULSA*CM25H*121650141207016*4*20220417033338*H2H*".$nohp."*24650*".$idoutlet."*------***130556886854.**2555670469*07*Layanan ".$kdproduk." sedang dalam gangguan. Mohon maaf.";
        } else {
            $resp = "PULSA*CM25H*121650141207016*4*20220417033338*H2H*088973779266*24650*".$idoutlet."*------***130556886854.*802299*2555670469*00*Pengisian pulsa CM25H Anda ke nomor ".$nohp." BERHASIL. SN=130556886854. Harga=24650";
        }       
    } else if($kdproduk == "AX50H") {

        if($nohp == "083876775733"){
            $resp = "PULSA*AX50H*5196691419*4*20220422102619*H2H*".$nohp."*49350*".$idoutlet."*------***TKXL2204988E49.**2565883653*20*EXT: Cutoff is in progress";
        } else {
            $resp = "PULSA*AX50H*5196691419*4*20220422102619*H2H*".$nohp."*49350*".$idoutlet."*------***TKXL2204988E49.**2565883653*00*Pengisian pulsa AX50H Anda ke nomor ".$nohp." BERHASIL. SN=TKXL2204918E49. Harga=49350";
        }
        
    } else{
        $resp = "PULSA*".$kdproduk."*4594763854*11*20210315084356*H2H*".$nohp."*". getHarga($kdproduk)."*".$idoutlet."*------**5X*02265300014414342223*114860*2068662074*00*Pengisian pulsa ".$kdproduk." Anda ke nomor ".$nohp." BERHASIL. SN=02265300014414342223 Harga=". getHarga($kdproduk)." ";
       
    }
    
    $format = FormatMsg::pulsa();
    $frm = new FormatPulsa($format[1], $resp);

    //print_r($frm->data);

    $r_step             = $frm->getStep();
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

     if($r_status === '35' || $r_status === '68'){
        $r_status = '35';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
    }

    if($r_status === '00' && ($r_keterangan !== "" && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') === false ) && $r_saldo_terpotong !== "0" && $r_sisa_saldo !== ""){
        $status_trx = "SUKSES";
    } else if( ($r_status === '00' || $r_status === '05')  && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false){
        $status_trx = "PENDING";
        $r_status = "35";
    } else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "35";
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
    }else if($r_status == '00') {
        $status_trx = "SUKSES";
    }else if($r_status !== '00') {
        $status_trx = "GAGAL";
    } else {
        $r_status = '35';
        $status_trx = "PENDING";
    }

    if($r_status === '68' && getIdBiller($r_idtrx) === '192'){
        //biller giga otomax
        $r_status = '35';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
    }

    $params = array(
        "KODE_PRODUK"       => $r_kdproduk,
        "WAKTU"             => $r_tanggal,
        "NO_HP"             => $r_nohp,
        "UID"               => $r_idoutlet,
        "PIN"               => '------',
        "SN"                => $r_sn,
        "NOMINAL"           => $r_nominal,
        "REF1"              => $ref1,
        "REF2"              => $r_idtrx,
        "STATUS"            => $r_status,
        "KET"               => $r_keterangan,
        "SALDO_TERPOTONG"   => $r_status === "33" ? "" : (string) $r_saldo_terpotong, 
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "STATUS_TRX"        => $status_trx
    );

    // $text = pulsa_game_resp_text($params);
    $get_mid = get_mid_from_idtrx($r_idtrx);
    $get_step = get_step_from_mid($get_mid) + 1;
    // writeLog($get_mid, $get_step, $host, $receiver, json_encode($params), $via);
    return json_encode($params, JSON_PRETTY_PRINT);
}

function game($data) {
    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $nohp       = strtoupper($data->no_hp);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $field      = 6;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = $_SERVER['REMOTE_ADDR'];

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
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";

    $tanggal = date("Ymdhis");
    $nominalget = getnominal($kdproduk);
   if (strpos(strtoupper($kdproduk), 'TOL') !== false) {
        if($nohp == "6032982702654185"){
            $resp = "GAME*".$kdproduk."*2480835138*8*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**".$kdproduk."**222229329*2175269104*00*SEDANG DIPROSES";
             $update = updatedataDevel($idoutlet,$nohp,$ref1,getnominal($kdproduk));
        }elseif($nohp == "6032982806364095"){
            $resp = "GAME*".$kdproduk."*4738369720*7*".$tanggal."*H2H*6032982806364095*".$nominalget."*".$idoutlet."*------*------*".$nominalget."**588508*2174330611*51005*EXT: ACCOUNT LIMIT EXCEEDED ";
        }else{
            $resp = "GAME*TOLM100H*4739679717*9*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*------**".$nominalget."**717709613*2175269104*00*SUCCESS  ";
        }  
    } else if(strpos(strtoupper($kdproduk), 'GFF') !== false){
        $resp = "GAME*".$kdproduk."*2481261346*8*20180317042417*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**HGP100*bimasakti602354f14420d211716662gO0g7sS#2041739741*33433874*952267798*00*Pembelian Game Online ".$kdproduk." BERHASIL. SN=bimasakti602354f14420d211716662gO0g7sS#2041739741. Harga=100750. Saldo=33433874  ";
    } else if(strpos(strtoupper($kdproduk), 'PUBG') !== false){
        $resp = "GAME*".$kdproduk."*2481261346*8*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**HGP100*JACKBOBOHO*33433874*952267798*00*Pembelian voucher game online berhasil ke no ".$nohp.". Kode Voucher: JACKBOBOHO.";
    } else if(strpos(strtoupper($kdproduk), 'ML') !== false){
        if($nohp == "838326262159"){
            $resp = "GAME*ML5*4737218066*4*".$tanggal."*H2H*838326262159*96100*".$idoutlet."****------**2173491063*00*Pembelian Voucher ML5 BERHASIL. SN=1222NQVITXT1C640632B5 c. ";
        }elseif($nohp == "6407263258552"){
            $resp = "GAME*".$kdproduk."*2480835138*8*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**".$kdproduk."**222229329*2173453186*00*Pembelian game online ".$kdproduk." Anda ke nomor ".$nohp." sedang diproses";
             $update = updatedataDevel($idoutlet,$nohp,$ref1,getnominal($kdproduk));
        }elseif($nohp == "417777225"){
            $resp = "GAME*ML4*4737216152*5*".$tanggal."*H2H*417777225*0*".$idoutlet."*------*------*ML4**156021*2173489576*19*EXT: Transaksi gagal    ";
        }else{
            $resp = "GAME*".$kdproduk."*4553139751*4*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*------***36 Diamonds.**2042357272*00*Pembelian Voucher ".$kdproduk." BERHASIL. SN=360 Diamonds.";
        }
    } else if(substr(strtoupper($kdproduk), 0,3) == 'OVO'){
        if($nohp == "081274756533"){
            $resp ="GAME*".$kdproduk."*4524586974*10*".$tanggal."*H2H*".$nohp."*".$nominalget."*HH122973*------**8099-20000**28369*2024627241*22*Customer Not Found";
        }elseif($nohp == "081274756544"){
             $resp = "GAME*".$kdproduk."*2480835138*8*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**".$kdproduk."**222229329*2175278102*00*Pembelian Voucher ".$kdproduk." Anda ke nomor ".$nohp." sedang diproses";
             $update = updatedataDevel($idoutlet,$nohp,$ref1,getnominal($kdproduk));
        }else{
              $resp = "GAME*".$kdproduk."*4739690595*7*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*242443**93600912*AJ162458111775093300920*713794431*2175278102*00*Pembelian Voucher ".$kdproduk." BERHASIL. SN=AJ162458111775093300920. Harga=".$nominalget.". Saldo=71371";
        }
      
    }else if(substr(strtoupper($kdproduk), 0,4) == 'DANA'){
        if($nohp == "081274756533"){
            $resp ="GAME*".$kdproduk."*4524586974*10*".$tanggal."*H2H*".$nohp."*".$nominalget."*HH122973*------**8099-20000**28369*2024627241*22*Customer Not Found";
        }elseif($nohp == "081274756544"){
             $resp = "GAME*".$kdproduk."*2480835138*8*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**".$kdproduk."**222229329*2338405384*00*Pembelian Voucher ".$kdproduk." Anda ke nomor ".$nohp." sedang diproses";
             $update = updatedataDevel($idoutlet,$nohp,$ref1,getnominal($kdproduk));
        }else{
              $resp = "GAME*".$kdproduk."*4935237048*12*20211110095902*H2H*".$nohp."*".$nominalget."*".$idoutlet."*------**8059-20000*211110GM11789812/DANA Top Up KANX GIRX*45236662*2338405384*00*Pembelian Voucher ".$kdproduk." BERHASIL. SN=211110GM11789812/DANA Top Up KANX GIRX. Harga=".$nominalget.". Saldo=45236662";
        }
      
    }else if(substr(strtoupper($kdproduk), 0,3) == 'GJK'){
        if($nohp == "081274756533"){
            $resp ="GAME*".$kdproduk."*4524586974*10*".$tanggal."*H2H*".$nohp."*".$nominalget."*HH122973*------**8099-20000**28369*2024627241*22*Customer Not Found";
        }elseif($nohp == "081274756544"){
             $resp = "GAME*".$kdproduk."*2480835138*8*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**".$kdproduk."**222229329*2338496650*00*Pembelian Voucher ".$kdproduk." Anda ke nomor ".$nohp." sedang diproses";
             $update = updatedataDevel($idoutlet,$nohp,$ref1,getnominal($kdproduk));
        }else{
              $resp = "GAME*".$kdproduk."*4935325875*3*20211110110043*H2H*".$nohp."*".$nominalget."*".$idoutlet."*------***GOPAY GUNAWAN**2338496650*00*Pembelian Voucher GJK10Z BERHASIL. SN=GOPAY GUNAWAN    ";
        }
      
    }else if(substr(strtoupper($kdproduk), 0,4) == 'LINK'){
        if($nohp == "081274756533"){
            $resp ="GAME*".$kdproduk."*4524586974*10*".$tanggal."*H2H*".$nohp."*".$nominalget."*HH122973*------**8099-20000**28369*2024627241*22*Customer Not Found";
        }elseif($nohp == "081274756544"){
             $resp = "GAME*".$kdproduk."*2480835138*8*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**".$kdproduk."**222229329*2338521425*00*Pembelian Voucher ".$kdproduk." Anda ke nomor ".$nohp." sedang diproses";
             $update = updatedataDevel($idoutlet,$nohp,$ref1,getnominal($kdproduk));
        }else{
              $resp = "GAME*".$kdproduk."*4935350914*4*20211110111657*H2H*".$nohp."*".$nominalget."*".$idoutlet."*------***8KA230A7B6**2338521425*00*Pembelian Voucher ".$kdproduk." BERHASIL. SN=8KA230A7B6    ";
        }
      
    } else if(substr(strtoupper($kdproduk), 0,4) == 'SHOP'){
        if($nohp == "081274756533"){
            $resp ="GAME*".$kdproduk."*4524586974*10*".$tanggal."*H2H*".$nohp."*".$nominalget."*HH122973*------**8099-20000**28369*2024627241*22*Customer Not Found";
        }elseif($nohp == "081274756544"){
             $resp = "GAME*".$kdproduk."*2480835138*8*".$tanggal."*H2H*".$nohp."*".$nominalget."*".$idoutlet."*".$pin."**".$kdproduk."**222229329*2338507856*12*";
             $update = updatedataDevel($idoutlet,$nohp,$ref1,getnominal($kdproduk));
        }else{
              $resp = "GAME*".$kdproduk."*4935360250*4*20211110112332*H2H*".$nohp."*".$nominalget."*".$idoutlet."*------***ShopeePay/SHOPEEPAY/FXXXXXXXXX9/Rp".number_format($nominalget,0,',','.')."/102500641005.**2338530560*00*Pembelian Voucher ".$kdproduk." BERHASIL. SN=ShopeePay/SHOPEEPAY/FXXXXXXXXX9/Rp".number_format($nominalget,0,',','.')."/102500641005.";
        }
      
    } else {
        $resp = "GAME*".$kdproduk."*1033881954*7*".date('YmdHis')."*H2H*".$nohp."*9300*".$idoutlet."*".$pin."**19574865*Voucher Code =24768271, Voucher Password=49678-00905-86487-19636-76298*455335649*505264128*00*Pembelian Game Online ".$kdproduk." BERHASIL. SN=Voucher Code =24768271, Voucher Password=49678-00905-86487-19636-76298. Harga=9300. Saldo=455335649";    
    }

    $format = FormatMsg::game();
    $frm    = new FormatGame($format[1], $resp);

    //print_r($frm->data);
    $r_step             = $frm->getStep();
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
    // echo $r_nominal          = getNominalTransaksi($r_idtrx);die();
    $nom_up             = getnominalup($r_idtrx);
    $r_saldo_terpotong  = $r_nominal + ($nom_up);
    $r_sisa_saldo       = $frm->getSaldo();
    
    if($r_status === ''){
        $r_status = '00';
        if($r_idtrx === ""){
            $r_idtrx = getIdTransaksi($nohp,$idoutlet,$kdproduk,$ref1);
        }
    }

    if($r_status === '35' || $r_status === '68'){

        if ($r_idoutlet == 'HH122973') {
            $r_status = '68';
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
        }else{
            $r_status = '35';
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
        }
       
    }

    if($r_status === '00' && ($r_keterangan !== "" && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') === false ) && $r_saldo_terpotong !== "0" && $r_sisa_saldo !== ""){
        $status_trx = "SUKSES";
    } else if( ($r_status === '00' || $r_status === '05')  && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false){

        if ($r_idoutlet == 'HH122973') {
            $status_trx = "PENDING";
            $r_status = "68";
        }else{
            $status_trx = "PENDING";
            $r_status = "35";
        }

    } else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            if ($r_idoutlet == 'HH122973') {
                $r_status = '68';
                $r_keterangan = "SEDANG DIPROSES";
                $status_trx = "PENDING";
            }else{
                $r_status = '35';
                $r_keterangan = "SEDANG DIPROSES";
                $status_trx = "PENDING";
            }
    }else if($r_status == '00') {
        $status_trx = "SUKSES";
    }else if($r_status !== '00') {
        $status_trx = "GAGAL";
    } else {
        if($r_idoutlet == 'HH122973') {
            $status_trx = "PENDING";
            $r_status = "68";
        }else{
            $status_trx = "PENDING";
            $r_status = "35";
        }
    }

    if($r_status === '68' && getIdBiller($r_idtrx) === '192'){
        //biller giga otomax
        if($r_idoutlet == 'HH122973') {
            $r_status = '68';
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
        }else{
            $r_status = '35';
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
        }
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
        "KET"               => (string) $r_keterangan,
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "STATUS_TRX"        => (string) $status_trx
    );

    // $text = pulsa_game_resp_text($params);
    $get_mid = get_mid_from_idtrx($r_idtrx);
    $get_step = get_step_from_mid($get_mid) + 1;
    // writeLog($get_mid, $get_step, $host, $receiver, json_encode($params), $via);

    return json_encode($params, JSON_PRETTY_PRINT);
}

function bpjs_inq($data){
    $i              = -1;
    $kdproduk       = strtoupper($data->kode_produk);
    $idpel1         = strtoupper($data->idpel);
    $periodebulan   = strtoupper($data->periode);
    $idoutlet       = strtoupper($data->uid);
    $pin            = strtoupper($data->pin);
    $ref1           = strtoupper($data->ref1);
    $field          = 7;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // global $pgsql;

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
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];

    if($idpel1 == "8888802456792662"){
        $resp = "TAGIHAN*ASRBPJSKSH*4627669179*10*20210408084147*H2H*8888802456792662**000000598955**2500*".$idoutlet."*------**334948392*1**227112*2089701796*54*EXT: TAGIHAN SUDAH LUNAS ATAU BELUM WAKTUNYA DIBAYAR*0000000*00*8888801784814952*1************BPJS Kesehatan*********   ";
    } else if($idpel1 == "8888801312885484"){
        $resp = "TAGIHAN*ASRBPJSKSQ*4764588929*7*20210714114426*H2H*8888801312885484***70000*2500*SP24135*------**27759283*1**BPJSKS*2195781978*00*Success*0000000*00*8888801312885484*01*2*9a0bfc6132fa4165a6f900a12051230c*PARNANTO**72500****2500**7ef23b5fa413429ab4eff8306c9eaa04*BPJS Kesehatan*****2195781978****    ";
    } else if($idpel1 == "8888802444597008"){
        $resp = "TAGIHAN*ASRBPJSKS*4764588929*7*20210714114426*H2H*8888802444597008***70000*2500*".$idoutlet."*------**27759283*1**BPJSKS*2195781978*35*WAKTU TRANSAKSI TELAH HABIS, COBA BEBERAPA SAAT LAGI*0000000*00*8888801312885484*01*2*9a0bfc6132fa4165a6f900a12051230c***72500****2500**7ef23b5fa413429ab4eff8306c9eaa04*BPJS Kesehatan*****2195781978****    ";
    } else if($idpel1 == "8888802258755435"){
        $resp = "TAGIHAN*ASRBPJSKS*121637545683989*9*20211122084813*H2H*8888802258755435***105000*2500*".$idoutlet."*------**751769604*1**BPJSKS*2358139779*00*Success*0000000*00*8888802258755435*01*3*7a5cea7c54ad4078ad075320df5fcacb*SITI MAEMONAH**107500****2500**10731b5c8c8644b1828a3001a3a05311*BPJS Kesehatan*****2358139779*****[{'nama':'MUHAMMAD RAGIL W','kode_cabang':'1110','nama_cabang':'KUDUS'},{'nama':'SITI MAEMONAH','kode_cabang':'1110','nama_cabang':'KUDUS'},{'nama':'TUMISIH','kode_cabang':'1110','nama_cabang':'KUDUS'}] ";
    } else {
        $resp = "TAGIHAN*ASRBPJSKSH*4436021935*10*20201123092955*H2H*8888801243704554**000000700823*000000102000*2500*".$idoutlet."*------**2160448*1**227112*1978853162*00*SUKSES*MANADO*2101*8888801243704554*1*4*00151370642500000000000000000000*JESTIA RONDONUWU******000000002500***BPJS Kesehatan*****0****";
    }
  


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
        "IDPEL1"           => (string) $r_idpel1,
        "IDPEL2"           => (string) $r_idpel2,
        "IDPEL3"           => (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) $r_name,
        "PERIODE"           => (string) $periodebulan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => "0",
        "STATUS"            => (string) $r_status,
        "KET"               => (string) $r_keterangan,
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_saldo,
        "URL_STRUK"         => (string) $url_struk
    );
    // print_r($params);

    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    // $db = new Database();
    // $q_ins_log = "INSERT INTO fmss_message (id_transaksi, id_transaksi_partner, mid, content, insert_datetime) 
    //                 VALUES (" . $id_transaksi . ", " . $id_transaksi_partner . ", " . $mid . ", '" . replace_forbidden_chars_msg($resp) . "', NOW())";
    // $e_ins_log = mysql_query($q_ins_log, $db->getConnection());
    return json_encode($params, JSON_PRETTY_PRINT);
}

function bpjs_pay($data){
    $i              = -1;
    $kdproduk       = strtoupper($data->kode_produk);
    $idpel1         = strtoupper($data->idpel1);
    $periodebulan   = strtoupper($data->periode);
    $hp             = strtoupper($data->no_hp);
    $nominal        = strtoupper($data->nominal);
    $idoutlet       = strtoupper($data->uid);
    $pin            = strtoupper($data->pin);
    $ref1           = strtoupper($data->ref1);
    $ref2           = strtoupper($data->ref2);
    $field          = 10;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
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
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
     
    if($idpel1 == "8888801851523593"){
        $resp = "BAYAR*ASRBPJSKS*789645675*11*20160107130800*WEB*8888801851523593***64986*5000*".$idoutlet."*".$pin."**1427760*3*15*080002*432635479*00*EXT: APPROVE*0605 *924000000432635479*8888801851523593*2*1*924000000432635479*DJASILAH **000000064986******924000000432635479*BPJS Kesehatan***750**000000000000*082175633485***";
    }elseif($idpel1 == "8888801312885484"){
        $resp = "BAYAR*ASRBPJSKS*3291551636*4*20190314152810*DESKTOP*8888801312885484***102000*2500*".$idoutlet."*".$pin."*------*9983*2**BPJSKS*1303538698*06*SALDO ANDA TIDAK MENCUKUPI******* ********* *********    ";
    }elseif($idpel1 == "8888802258755435"){
        $resp = "BAYAR*ASRBPJSKS*121637545694111*8*20211122084814*H2H*8888802258755435***105000*2500*".$idoutlet."*------**751664104*1**BPJSKS*2358139816*00*SEDANG DIPROSES*0000000*BC01621991B4DE45*8888802258755435*01*3*BC01621991B4DE45*SITI MAEMONAH**107500****2500**BC01621991B4DE45*BPJS Kesehatan*****2358139779*081061842817****[{'nama':'MUHAMMAD RAGIL W','kode_cabang':'1110','nama_cabang':'KUDUS'},{'nama':'SITI MAEMONAH','kode_cabang':'1110','nama_cabang':'KUDUS'},{'nama':'TUMISIH','kode_cabang':'1110','nama_cabang':'KUDUS'}] ";
    }else{
          $resp = 'BAYAR*ASRBPJSKSH*4436022019*10*20201123092958*H2H*8888801243704554**000000700829*000000102000*2500*'.$idoutlet.'*------**2057948*1**227112*1978853193*00*SUKSES*MANADO*AD6668EA094AD8CA*8888801243704554*1*4*AD6668EA094AD8CA*JESTIA RONDONUWU******000000002500**AD6668EA094AD8CA*BPJS Kesehatan*****0*082276992990***';
    }

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
        "URL_STRUK"         => (string) $url_struk
    );

    if (count($r_additional_datas) > 0) {
        $params['DETAIL']   = $r_additional_datas;
        // array_push($params, $r_additional_datas);
    }

    //$params = setMandatoryRespon($frm,$ref1,"","",$url_struk);
    //$params = getResponArray($kdproduk,$params,$resp);

    $is_return = true;
    return json_encode($params, JSON_PRETTY_PRINT);
}

function cetak_ulang($data) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $field      = 5;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip <> "") {
    //     if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130") {
    //         die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
    //     }
    // }

    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }    

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    // global $pgsql;
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
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
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    //echo $msg."<br>";
    $list       = $GLOBALS["sndr"];
    //if($list[strtoupper($idoutlet)] && in_array($GLOBALS["host"],$list[strtoupper($idoutlet)])){
    // $respon     = postValue($fm);
    //print_r($respon);
    // $resp       = $respon[7];
    // writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);

    if($ref2 == "2041739741"){
        $resp = "CU*GFF140H*4553695472*9*20210211131355*H2H*2045773970*18625*".$idoutlet."*------**free_fire-freefire_140*bimasakti6024cb237789d212411903Vbz9Na9#2042679338*4615898600*2042679338*00*Pembelian Voucher GFF140H BERHASIL. SN=bimasakti6024cb237789d212411903Vbz9Na9#2042679338. Harga=18625. Saldo=4615898600";
    } else if($ref2 == "952267798"){
        $resp = "CU*PUBG100H*2481261346*8*20180317042417*H2H*083872841884*20000*".$idoutlet."*".$pin."**PUBG100H*JACKBOBOHO*33433874*952267798*00*Pembelian voucher game online berhasil ke no 083872841884 Kode Voucher: JACKBOBOHO.";
    } else if($ref2 == "2042357272"){
        $resp = "CU*ML1H*4553139751*4*20210211054555*H2H*083872841884*11000*".$idoutlet."*------***36 Diamonds.**2042357272*00*Pembelian Voucher ML1H BERHASIL. SN=36 Diamonds.";
    }else if($ref2 == "1161888441"){
         $resp = "BAYAR*PGN*2977389842*10*20181030161201*WEB*0110011437***140520*2500*" . $idoutlet . "*" . $pin . "*------*8290773*1**PGN*1161888441*00*SUKSES*0110011437*L HUTAGALUNG*26 M3*Sep2018*INV1181030141660*140520*2500*143020***72888*REF    ";
    }else if($ref2 == "2042357272"){
        $resp = "CU*PGN*2977389842*10*20181030161201*WEB*0110011437***140520*2500*" . $idoutlet . "*" . $pin . "*------*8290773*1**PGN*1161888441*00*SUKSES*0110011437*L HUTAGALUNG*26 M3*Sep2018*INV1181030141660*140520*2500*143020***72888*REF    ";
    }else{
        $resp = "CU********".$idoutlet."*".$pin."******00*Hanya bisa dilakukan diproduction jika ingin dinamis , disini yang tersetting hanya(ref2 :2041739741,952267798,2042357272)";
    }

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
        "IDPEL1"           => (string) $r_idpel1,
        "IDPEL2"           => (string) $r_idpel2,
        "IDPEL3"           => (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
        "PERIODE"           => (string) $r_periode_tagihan,
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
        "URL_STRUK"         => (string) $url_struk
    );

    return json_encode($params, JSON_PRETTY_PRINT);
}

function cetak_ulang_detail($data) {
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $field      = 5;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip <> "") {
    //     if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130"  ) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    // global $pgsql;
    // if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //     return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    // }

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
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list       = $GLOBALS["sndr"];
    //if($list[strtoupper($idoutlet)] && in_array($GLOBALS["host"],$list[strtoupper($idoutlet)])){
    // $respon     = postValue($fm);
    // //print_r($respon);
    // $resp       = $respon[7];
    if($ref2 == "432635479"){
         $resp = "CU*ASRBPJSKS*789645675*11*20160107130800*WEB*8888801851523593***64986*5000*".$idoutlet."*".$pin."**1427760*3*15*080002*432635479*00*EXT: APPROVE*0605 *924000000432635479*8888801851523593*2*1*924000000432635479*DJASILAH **000000064986******924000000432635479*BPJS Kesehatan***750**000000000000*082175633485***";
    }elseif($ref2 == "1161888441"){
         $resp = "CU*PGN*2977389842*10*20181030161201*WEB*0110011437***140520*2500*" . $idoutlet . "*" . $pin . "*------*8290773*1**PGN*1161888441*00*SUKSES*0110011437*L HUTAGALUNG*26 M3*Sep2018*INV1181030141660*140520*2500*143020***72888*REF    ";
    }elseif($ref2 == "256519489"){
         $resp = "CU*HPTSEL*242453920*10*20141121120636*H2H*0811408689***000000048323*2500*" . $idoutlet . "*" . $pin . "*------*2879885*0**HPTSEL*256519489*00*APPROVE*0000000*00*0811408689*1***Bapak V.  TIKNO SARWOKO****TELKOMSEL*201411*201411*000000000*0000000004832300*0000000000*      *      *         *                *          *      *      *         *                *";
    }elseif($ref2 == "244846204"){
          $resp = "CU*HPMTRIX*209341713*10*20141018072311*H2H*08155101252***000000027500*2500*" . $idoutlet . "*" . $pin . "*------*1935079*0**HPMTRIX*244846204*00*APPROVE*0000000*00*08155101252*1***DIAN INDRESWARI****INDOSAT*201016*201016*000000000*0000000002750000*0000000000*      *      *         *                *          *      *      *         *                *";
    }elseif($ref2 == "48720213"){
         $resp = "CU*HPXL*70911713*11*20121208130710*H2H*0818158020***000000054303*2500*" . $idoutlet . "*" . $pin . "*------*584331*1**HPXL*48720213*00*APPROVE*0000000*00*0818158020*1*0445554**RACHMAT SUHAPPY****XL*201   *201   *000000000*0000000005430300*0000000000*      *      *         *                *          *      *      *         *                *";
    }elseif($ref2 == "245006387"){
         $resp = "CU*HPSMART*209747596*10*20141018134900*H2H*088271084560***25388*0*" . $idoutlet . "*" . $pin . "*------*3685965*1**016004*245006387*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*088271084560*1*24912363505**B620-1010945-WAHYUDI-K***25388*SMART***************";
    }elseif($ref2 == "259558586"){
          $resp = "CU*HPTHREE*252312187*10*20141202125907*H2H*08984222333***55000*0*" . $idoutlet . "*" . $pin . "**27628401*1**012101*259558586*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*08984222333*1*1986015835**INTAN NUR AZIZA***55000*THREE***************";
    }elseif($ref2 == "255717645"){
         $resp = "CU*HPFREN*240645388*10*20141119215722*H2H*08885088008***47094*0*" . $idoutlet . "*" . $pin . "*------*200569*1**016002*255717645*00*EXT: APPROVED, TRANSACTION IS DONE WITHOUT ERROR*0000000*00*08885088008*1*000000717644**HERU  WIDIJANTO***47094*FREN***************";
    }
   

    // writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);
    
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
        "IDPEL1"           => (string) $r_idpel1,
        "IDPEL2"           => (string) $r_idpel2,
        "IDPEL3"           => (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
        "PERIODE"           => (string) $r_periode_tagihan,
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
        "URL_STRUK"         => (string) $url_struk
    );
    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);

    if (count($r_additional_datas) > 0) {
        $params['DETAIL']   = $r_additional_datas;
        // array_push($params, $r_additional_datas);
    }
    return json_encode($params, JSON_PRETTY_PRINT);
}

function cetak_ulang_detail2($data) {

    // die('a');
    //BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i          = -1;
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $field      = 5;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    // if ($ip <> "") {
    //     if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130"  ) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    // global $pgsql;
    // if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //     return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    // }

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
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list       = $GLOBALS["sndr"];
    //if($list[strtoupper($idoutlet)] && in_array($GLOBALS["host"],$list[strtoupper($idoutlet)])){
    // $respon     = postValue($fm);
    // //print_r($respon);
    // $resp       = $respon[7];
    if($ref2 == '1940559618'){
        $resp = "CU*PLNPASC30*4361402034*10*20201012150401*H2H*323620024326***123438*3000*".$idoutlet."*------*------*18075707*2**501*1940559618*00*TRANSAKSI SUKSES*0000000*323620024326*1*1*01*0BMS210Z972D28E32FD67F48D9C75D09*AHMAD SUNARTA *32360*123 * R1M*000000900*000000000*202010*20102020*00000000*000000123438*D0000000000*0000000000*000000000000*00015762*00015845*00000000*00000000*00000000*00000000*****************************************D9E9DC140F064E1D83BF62778E50163B**123438*Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
    }elseif($ref2 == '1915027544'){
        $resp = "BAYAR*PLNPRAH*4309283601*10*20200914181032*H2H*01107552562*211018760870**50000*2500*SP141894*------*------*399842152*1**053502*1915027544*00*TRANSAKSI SUKSES*JTL53L3*01107552562*211018760870*0*82E949793A9E4BDDBE911A070A84479A*0BMS210Z6FB9D4C2C368B017380014BB*00000000*LERI MONIKA PRICILIA*R1M*000000900*2*0000000000*0*21*21101*123*00648*0***60337603478337008296*2*0000000000*2*0000000000*2*0000412900*2*0000000000*2*000004587100*2*0000003400*Informasi Hubungi Call Center 123 Atau hubungi PLN Terdekat";
    }elseif($ref2 == '1915001294'){
        $resp = "BAYAR*PLNNONH*4309228620*10*20200914174449*H2H*5362143031645***96533*5000*SP2347*------*------*5658959*1**053504*1915001294*00*TRANSAKSI SUKSES*0000000*5362143031645***PENYELESAIAN P2TL        *20200914*02022022*536211103834*GOJALI                   *2BE8FEF83CED4735911D64F4AFBF433B*0BMS210ZE4EB614F5A5C*     *                                   *               *2*00000000009653300*2*00000000009653300*2*0000000000******  *2*                 ****Informasi Hubungi Call Center 123 Atau Hub PLN Terdekat :";
    }
    // writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $kdproduk   = $frm->getKodeProduk();


     if($resp == 'null'){
        if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids())){
            $params = array(
                "KODE_PRODUK"       => (string) $kdproduk,
                "WAKTU"             => (string) date('YmdHis'),
                "IDPEL1"            => (string) $idpel1,
                "IDPEL2"            => (string) $idpel2,
                "IDPEL3"            => (string) $idpel3,
                "NAMA_PELANGGAN"    => (string) '',
                "PERIODE"           => (string) '',
                "JML_BULAN"         => (string) '',
                "NOMINAL"           => (string) '',
                "TARIF"             => (string) '',
                "DAYA"              => (string) '',
                "REF"               => (string) '',
                "STANAWAL"          => (string) '',
                "STANAKHIR"         => (string) '',
                "INFOTEKS"          => (string) '',
                "ADMIN"             => (string) '',
                "UID"               => (string) $idoutlet,
                "PIN"               => (string) '------',
                "REF1"              => (string) $ref1,
                "REF2"              => (string) '',
                "REF3"              => "0",
                "STATUS"            => (string) '00',
                "KET"               => (string) 'SEDANG DIPROSES',
                "SALDO_TERPOTONG"   => (string) '',
                "SISA_SALDO"        => (string) '',
                "URL_STRUK"         => (string) ''
            );
        } else {
            $params = array(
                "KODE_PRODUK"       => (string) $kdproduk,
                "WAKTU"             => (string) date('YmdHis'),
                "IDPEL1"            => (string) $idpel1,
                "IDPEL2"            => (string) $idpel2,
                "IDPEL3"            => (string) $idpel3,
                "NAMA_PELANGGAN"    => (string) '',
                "NOMINAL"           => (string) '',
                "TARIF"             => (string) '',
                "DAYA"              => (string) '',
                "REF"               => (string) '',
                "MATERAI"           => (string) '',
                "PPN"               => (string) '',
                "PPJ"               => (string) '',
                "ANGSURAN"          => (string) '',
                "RPTOKEN"           => (string) '',
                "KWH"               => (string) '',
                "TOKEN"             => (string) '',
                "INFOTEKS"          => (string) '',
                "ADMIN"             => (string) '',
                "UID"               => (string) $idoutlet,
                "PIN"               => (string) '------',
                "REF1"              => (string) $ref1,
                "REF2"              => (string) '',
                "REF3"              => "0",
                "STATUS"            => (string) '00',
                "KET"               => (string) 'SEDANG DIPROSES',
                "SALDO_TERPOTONG"   => (string) '',
                "SISA_SALDO"        => (string) '',
                "URL_STRUK"         => (string) ''
            );
        }
        
        return json_encode($params, JSON_PRETTY_PRINT);
    }
    
  

    $params     = setMandatoryResponNewArranet($frm, $ref1, "", "", $data);
    // print_r($params);die();
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk_arranet($kdproduk, $frm);
    $adddata2   = tambahdataproduk2_arranet($kdproduk, $frm);
     if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
        $adddata3   = tambahdataproduk3($kdproduk, $frm);
    }else{
        $adddata3 = array();
    }   
    

    if ($frm->getStatus() == "00") {
        $url        = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk  = array("url_struk" => "https://202.43.173.234/struk/?id=" . $url);
        $merge      = array_merge($params,$adddata,$adddata2,$adddata3,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2,$adddata3);
    }

    return json_encode($merge);
}

function cetak_ulang_detail3($data) {

    $i= -1;
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);
    $field      = 5;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    // if ($ip <> "") {
    //     if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130"  ) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    // if ($kdproduk == KodeProduk::getPLNPrepaid()) {
    //     $idpel1 = pad($idpel1, 11, "0", "left");
    // }

    // // global $pgsql;
    // if (!checkHakAksesMP("", strtoupper($idoutlet))) {
    //     return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    // }

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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];           //KETERANGAN
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
    // $respon     = postValue($fm);
    // //print_r($respon);
     $resp = "CU*WAKPLMBNG*4570612004*9*20210224073000*H2H*012183*012183*012183*61600*2500*".$idoutlet."*------**13700022*1**2035*2053695523*00*EXT: APPROVE*0000000*RAHMAWATI*012183*012183*012183*1*202102*RAHMAWATI*RAHMAWATI**201202102240537***PAM ATS PALEMBANG*02*2021***5000*56600*0***********************************    ";

    // $resp = 'null';



    // writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);
   
       if($resp == 'null' || empty($resp)){
            $params = array(
                "kodeproduk" => "",
                "tanggal"=> "",
                "idpel1"=> "",
                "idpel2"=> "",
                "idpel3"=> "",
                "nominal"=> "",
                "admin"=> "",
                "id_outlet"=> $idoutlet,
                "pin"=> "------",
                "ref1"=> $ref1,
                "ref2"=> $ref2,
                "ref3"=> "",
                "status"=> "00",
                "keterangan"=> "SEDANG DIPROSES",
                "fee"=> "-",
                "saldo_terpotong"=> "",
                "sisa_saldo"=> "",
                "total_bayar"=> "",
                "jml_bln"=> "",
                "stan_awal"=> "",
                "stan_akhir"=> "",
                "nama_pelanggan"=> "",
                "periode"=> "",
                "url_struk"=> ""
            );
            writeLog($GLOBALS["mid"], $stp+1, $receiver, $host, json_encode($params), $GLOBALS["via"]);
            return json_encode($params, JSON_PRETTY_PRINT);
        }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $kdproduk   = $frm->getKodeProduk();

    $params     = setMandatoryResponNew($frm, $ref1, "", "", $data);
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk($kdproduk, $frm);
    $adddata2   = tambahdataproduk2($kdproduk, $frm);
    if ($frm->getStatus() == "00") {
        $url        = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk  = array("url_struk" => "https://202.43.173.234/struk/?id=" . $url);
        $merge      = array_merge($params,$adddata,$adddata2,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2);
    }

    return json_encode($merge);
}

function inq_bintang($data) {
    $i              = -1;
    $kode_produk    = strtoupper($data->kode_produk); //'ASRBINT2';
    $id_outlet      = strtoupper($data->uid); //'BS0004';
    $pin            = strtoupper($data->pin); //141414;

    $mid    = $GLOBALS["mid"]; //1;
    $step   = 1;

    $ip     = $_SERVER['REMOTE_ADDR'];
    /* if ($ip <> "") {
      if (!isValidIP($idoutlet, $ip)) {
      die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
      }
      } */

    $msg        = array();
    $i          = -1;
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
        $msg[$j]= ""; //
    }
    $fm         = convertFM($msg, "*");
//    echo $fm;
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    // writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    // $respon     = postValue($fm);
    // $params     = $respon;
//    $params = $msg;

    return json_encode($params, JSON_PRETTY_PRINT);
}

function pay_bintang($data) {
    $i                  = -1;
    $kode_produk        = strtoupper($data->kode_produk); //'ASRBINT2';
    $id_outlet          = strtoupper($data->uid); //'BS0004';
    $pin                = strtoupper($data->pin); //141414;
    $id_pelanggan       = strtoupper($data->id_pel); //"123456789";
    $nominal            = strtoupper($data->nominal); //"2500";
    $id_transaksi_inq   = strtoupper($data->id_trx); //"326782413";
    $nama               = strtoupper($data->nama); //"Andi Yusanto";
    $no_telp            = strtoupper($data->no_tlp); //"085649492115";

    $mid    = $GLOBALS["mid"]; //1;
    $step   = 1;

    if ($id_outlet == "BS0004") {
        $result = array();
        $result[7] = "BAYAR*ASRBINT1*601357786*8*20150907135726*XML*1234567890***2500*0*BS0004*------**654811*1**ASRBINT1*374370795*00*SUKSES****01***MIRZA*Asuransi Bintang Paket 1*2500*****20000000*AB1500029594*BINTANG******AB1500029594*08111111111111********20150907135726*20150908135726*20151007135726*Sertifikat bisa di-print di: www.fastpay.co.id/enduser";
        $params = $result;
        return json_encode($params, JSON_PRETTY_PRINT);
        // return new xmlrpcresp(php_xmlrpc_encode($params));
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    /* if ($ip <> "") {
      if (!isValidIP($idoutlet, $ip)) {
      die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
      }
      } */
    $msg        = array();
    $i          = -1;
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
    $fm         = convertFM($msg, "*");
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    // writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    // $respon     = postValue($fm);
    // $params     = $respon;
//    $params = $msg;
    return json_encode($params, JSON_PRETTY_PRINT);
    // return new xmlrpcresp(php_xmlrpc_encode($params));
}

function cek_ip() {
    $output = [
        "IP"    => $_SERVER['REMOTE_ADDR']
    ];
    return json_encode($output, JSON_PRETTY_PRINT);
}

function info_produk($data){
    $i          = -1;

    $id_produk  = strtoupper(filter_var($data->kode_produk,FILTER_SANITIZE_STRING));
    $id_outlet  = strtoupper(filter_var($data->uid,FILTER_SANITIZE_STRING));
    $pin        = strtoupper(filter_var($data->pin,FILTER_SANITIZE_STRING));
    $field      = 4;

    // echo $id_outlet."\n";
    // echo $pin."\n";
    // echo $id_produk;die();
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
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
            "PRODUK"            => '',
            "STATUS_PRODUK"     => '' );
        $next = FALSE;
        return json_encode($params, JSON_PRETTY_PRINT);
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
                "PRODUK"            => '',
                "STATUS_PRODUK"     => '');
            $next = FALSE;
            return json_encode($params, JSON_PRETTY_PRINT);
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
                "PRODUK"            => '',
                "STATUS_PRODUK"     => '');
            $next = FALSE;
            return json_encode($params, JSON_PRETTY_PRINT);
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
        $produk     = $status_produk[4];
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
            "PRODUK"            => (string)$produk,
            "STATUS_PRODUK"     => (string)$status
        );
        return json_encode($params, JSON_PRETTY_PRINT);
    }
}
function getHarga($tag2){
    
    $age = array(
        "AX1H" => "1025",
        "AX5H" => "5025",
        "AX10H" => "9975",
        "AX20H" => "20100",
        "AX25H" => "24850",
        "AX50H" => "49500",
        "AX100H" => "98850",
        "AX200H" => "198850",
        "SD20H" => "20175",
        
        "C5H" => "4725",
        "C10H" => "9375",
        "C20H" => "18725",
        "C50H" => "46550",
        "C100H" => "93075",
        
        "E1H" => "1150",
        "E5H" => "5225",
        "E10H" => "10300",
        "E11H" => "10875",
        "E15H" => "14800",
        "E20H" => "19700",
        "E25H" => "24525",
        "E50H" => "49150",
        "E100H" => "98200",
        
        "F5H" => "5050",
        "F10H" => "10050",
        "F20H" => "19925",
        "F50H" => "49000",
        "F100H" => "97700",
        "F150H" => "148600",
        
        "R10H" => "10275",
        "R25H" => "25300",
        "R50H" => "50500",
        "R100H" => "100500",

        "H5H" => "5275",
        "H10H" => "10275",
        "H25H" => "25300",
        "H50H" => "50500",
        "H100H" => "100500",
        
        "IG10H" => "10400",
        "IG25H" => "24925",
        "I5H" => "5400",
        "IG5H" => "5400",
        "IS5H" => "5425",
        "IT5H" => "5275",
        "I10H" => "10025",
        "IS10H" => "10400",
        "IT10H" => "10175",
        "I20HD" => "19900",
        "I25H" => "24600",
        "IS25H" => "25050",
        "IT25H" => "25200",
        "I30HD" => "29850",
        "I50H" => "48850",
        "I50HD" => "49750",
        "IT50H" => "49300",
        "I100H" => "97550",
        "I100HD" => "99500",
        "IT100H" => "98250",
        "I200H" => "197550",
        "IT200H" => "98250",
        "ID1H" => "28500",
        "ID1" => "28500",
        
        "T1H" => "1050",
        "T5H" => "4975",
        "T10H" => "9925",
        "T20H" => "19825",
        "T30H" => "29425",
        "T50H" => "49525",
        "T100H" => "99025",
        
        "CM5H" => "5050",
        "CM10H" => "9850",
        "CM20H" => "20350",
        "CM25H" => "24700",
        "CM50H" => "48700",
        "CM100H" => "97350",
        
        "O5H" => "5250",
        "O10H" => "10275",
        "O50H" => "49200",
        "O100H" => "98200",
        
        "S5H"=> "5775",
        "S10H"=> "10425",
        "S20H"=> "20325",
        "S25H"=> "25275",
        "S50H"=> "49650",
        "S100H"=> "98000",
        
        "XD12H" => "45550",
        "XD20H" => "46050",
        "XD30H" => "43050",
        "XD35H" => "30550",
        "XD40H" => "40550",
        "XD5H" => "25550",
        "XD90H" => "70600",
        "XD99H" => "80600",
        "XR5H" => "5250",
        "XR10H" => "10200",
        "XD49H" => "22050",
        "XR25H" => "24650",
        "XR50H" => "49200",
        "XD199H" => "141550",
        "XR100H" => "98300",
        "XD449H" => "271550",

        "B25H" => "24475",
        "B50H" => "48600",
        "B100H" => "97100",
        "B150H" => "145200",
        "B200H" => "194100",

        "MCDP50H" => "22400",
        "MCDP150H" => "66450",
        "UGC25H" => "25100",
        "MCDP350H" => "148050",
        "MCDP450H" => "190450",
        "UGC50H" => "58900",
        "BSF10H" => "9050",
        "MCDP1000H" => "419900",
        "UGC200H" => "245200",
        "SPN2H" => "2425",
        "BSF5H" => "4550",
        "GMM5H" => "4550",
        "LY5H" => "5325",
        "AGV10H" => "9050",
        "GAS10H" => "9750",
        "GDG10H" => "9250",
        "GIH10H" => "9350",
        "GIN10H" => "9050",
        "GMB10H" => "9250",
        "GMM10H" => "9050",
        "GOG10H" => "9050",
        "GOV10H" => "9050",
        "GPD10H" => "9050",
        "GPY10H" => "9050",
        "GQN10H" => "9050",
        "GRB10H" => "9250",
        "GS10H" => "9350",
        "GWW10H" => "9050",
        "KWC10H" => "9350",
        "LY10H" => "9550",
        "MS10H" => "9050",
        "ROS10H" => "10100",
        "SPN10H" => "9550",
        "TER10H" => "9050",
        "VCC100H" => "92700",
        "VWW15H" => "13550",
        "AGV20H" => "18050",
        "GAS20H" => "19450",
        "GDG20H" => "18450",
        "GIH20H" => "18650",
        "GIN20H" => "18050",
        "GMB20H" => "18450",
        "GOV20H" => "18050",
        "GPD20H" => "18050",
        "GRB20H" => "18450",
        "GS20H" => "18650",
        "GWW20H" => "18100",
        "KWC20H" => "18650",
        "LY20H" => "19050",
        "MOG20H" => "18050",
        "MOL20H" => "19850",
        "MS20H" => "18050",
        "PLF20H" => "18050",
        "SPN20H" => "19050",
        "TER20H" => "18050",
        "ZY20H" => "22550",
        "BSF25H" => "22550",
        "GMM25H" => "22600",
        "GRN25H" => "23850",
        "PLNX25H" => "22550",
        "TRA27H" => "31600",
        "AGV30H" => "27100",
        "FBGC30H" => "29350",
        "GOG30H" => "27700",
        "GPY30H" => "27100",
        "GQN30H" => "27100",
        "GS30H" => "27950",
        "KWC30H" => "28000",
        "ROS30H" => "27100",
        "SPN30H" => "28600",
        "TER30H" => "27100",
        "VCC300H" => "287800",
        "VWW30H" => "27100",
        "LY35H" => "33350",
        "GRN40H" => "47600",
        "AGV50H" => "45100",
        "BSF50H" => "45100",
        "FBGC50H" => "48250",
        "GAS50H" => "48600",
        "GDG50H" => "46100",
        "GGW50H" => "47600",
        "GIH50H" => "46600",
        "GIN50H" => "45100",
        "GMB50H" => "46100",
        "GMM50H" => "45100",
        "GMWV150H" => "45100",
        "GOG50H" => "45100",
        "GOV50H" => "45100",
        "GPD50H" => "45100",
        "GPY50H" => "45100",
        "GQN50H" => "45100",
        "GRB50H" => "46100",
        "GS50H" => "47000",
        "GWW50H" => "49100",
        "KWC50H" => "46600",
        "MOG50H" => "45100",
        "MOL50H" => "49600",
        "MS50H" => "45100",
        "PLF50H" => "45100",
        "PLNX50H" => "45100",
        "ROS50H" => "45100",
        "SPN50H" => "47600",
        "STGC50H" => "63750",
        "TER50H" => "45100",
        "VCC50H" => "46900",
        "ZY50H" => "56350",
        "MEV60H" => "54100",
        "VWW60H" => "54100",
        "TRA63H" => "79200",
        "LY65H" => "62500",
        "AGV100H" => "90100",
        "BSF100H" => "90100",
        "FBGC100H" => "96400",
        "GAS100H" => "97100",
        "GDG100H" => "92100",
        "GGW100H" => "95050",
        "GIH100H" => "93100",
        "GIN100H" => "90100",
        "GMB100H" => "92100",
        "GMM100H" => "90100",
        "GMWV300H" => "90100",
        "GOG100H" => "92100",
        "GOV100H" => "90100",
        "GPD100H" => "90100",
        "GPY100H" => "90100",
        "GQN100H" => "90100",
        "GRB100H" => "92100",
        "GRN100H" => "118900",
        "GS100H" => "93100",
        "GWW100H" => "90050",
        "KWC100H" => "93100",
        "MOG100H" => "90100",
        "MOL100H" => "99100",
        "MS100H" => "90100",
        "PLF100H" => "90100",
        "PLNX100H" => "90100",
        "ROS100H" => "90100",
        "SPN100H" => "95100",
        "STGC100H" => "127500",
        "TER100H" => "90100",
        "UGC100H" => "122700",
        "ZY100H" => "112600",
        "MEV120H" => "108100",
        "GRN125H" => "118900",
        "TRA137H" => "171200",
        "STGC150H" => "191150",
        "LY175H" => "166400",
        "GGW200H" => "190300",
        "GMM200H" => "180150",
        "GMWV600H" => "180150",
        "GRN200H" => "237650",
        "GS200H" => "186150",
        "KWC200H" => "186200",
        "MOL200H" => "198300",
        "MS200H" => "180250",
        "PLNX200H" => "180200",
        "STGC200H" => "254800",
        "TER200H" => "180200",
        "GPD250H" => "225200",
        "GRN250H" => "237650",
        "GWW250H" => "245100",
        "TRA265H" => "329200",
        "MEV285H" => "256700",
        "GS300H" => "279200",
        "KWC300H" => "279200",
        "STGC300H" => "382100",
        "TER300H" => "270200",
        "BSF500H" => "450350",
        "GGW500H" => "475500",
        "GMWV1500H" => "450500",
        "MOL500H" => "495300",
        "MS500H" => "490400",
        "MEV545H" => "490800",
        "GGW1000H" => "950500",
        "GMWV3000H" => "901000"
        );
    return $age[$tag2];
}

//=========function bpjstk start==============//

//========= daftar bpjstk start ==============//
function bpjstk_propinsi($data){
    //BPJS_PROVINSI
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);

    $field      = 3;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    if (!checkpin($uid, $pin)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"02",
                'keterangan'=>"pin yang anda masukkan salah"
            )
        );
    } 

    return '{"keterangan":"Sukses","status":"00","result":[{"kode":"51","nama":"BALI"},{"kode":"19","nama":"BANGKA BELITUNG"},{"kode":"36","nama":"BANTEN"},{"kode":"17","nama":"BENGKULU "},{"kode":"11","nama":"DI ACEH"},{"kode":"34","nama":"DI YOGYAKARTA"},{"kode":"31","nama":"DKI JAKARTA "},{"kode":"75","nama":"GORONTALO"},{"kode":"15","nama":"JAMBI "},{"kode":"32","nama":"JAWA BARAT "},{"kode":"33","nama":"JAWA TENGAH"},{"kode":"35","nama":"JAWA TIMUR "},{"kode":"61","nama":"KALIMANTAN BARAT"},{"kode":"63","nama":"KALIMANTAN SELATAN"},{"kode":"62","nama":"KALIMANTAN TENGAH"},{"kode":"64","nama":"KALIMANTAN TIMUR"},{"kode":"65","nama":"KALIMANTAN UTARA"},{"kode":"21","nama":"KEPULAUAN RIAU"},{"kode":"18","nama":"LAMPUNG"},{"kode":"81","nama":"MALUKU"},{"kode":"86","nama":"MALUKU UTARA"},{"kode":"52","nama":"NTB"},{"kode":"53","nama":"NTT"},{"kode":"82","nama":"PAPUA"},{"kode":"92","nama":"PAPUA BARAT"},{"kode":"14","nama":"RIAU "},{"kode":"76","nama":"SULAWESI BARAT"},{"kode":"73","nama":"SULAWESI SELATAN"},{"kode":"72","nama":"SULAWESI TENGAH"},{"kode":"74","nama":"SULAWESI TENGGARA"},{"kode":"71","nama":"SULAWESI UTARA"},{"kode":"13","nama":"SUMATERA BARAT"},{"kode":"16","nama":"SUMATERA SELATAN "},{"kode":"12","nama":"SUMATERA UTARA "}]}';
}

function bpjstk_pekerjaan($data){
    //BPJS_PEKERJAAN_BPU
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    
    $field      = 3;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    if (!checkpin($uid, $pin)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"02",
                'keterangan'=>"pin yang anda masukkan salah"
            )
        );
    } 

    return '{"status":"00","data":[{"nama_pekerjaan":"ADMINISTRASI","kd_pekerjaan":"P083"},{"nama_pekerjaan":"AGEN BRILINK","kd_pekerjaan":"P096"},{"nama_pekerjaan":"AGEN POS","kd_pekerjaan":"P106"},{"nama_pekerjaan":"AGEN46","kd_pekerjaan":"P092"},{"nama_pekerjaan":"AHLI GIZI","kd_pekerjaan":"P073"},{"nama_pekerjaan":"APOTEKER","kd_pekerjaan":"P042"},{"nama_pekerjaan":"ARSITEK","kd_pekerjaan":"P038"},{"nama_pekerjaan":"ARTIS","kd_pekerjaan":"P061"},{"nama_pekerjaan":"ATLET","kd_pekerjaan":"P060"},{"nama_pekerjaan":"BIARAWATI","kd_pekerjaan":"P051"},{"nama_pekerjaan":"BIDAN","kd_pekerjaan":"P041"},{"nama_pekerjaan":"BURUH BONGKAR MUAT\/BAGASI","kd_pekerjaan":"P066"},{"nama_pekerjaan":"BURUH HARIAN LEPAS","kd_pekerjaan":"P005"},{"nama_pekerjaan":"BURUH NELAYAN\/PERIKANAN","kd_pekerjaan":"P007"},{"nama_pekerjaan":"BURUH PETERNAKAN","kd_pekerjaan":"P008"},{"nama_pekerjaan":"BURUH TANI\/PERKEBUNAN","kd_pekerjaan":"P006"},{"nama_pekerjaan":"DOKTER","kd_pekerjaan":"P040"},{"nama_pekerjaan":"DOKTER GIGI","kd_pekerjaan":"P074"},{"nama_pekerjaan":"DOSEN","kd_pekerjaan":"P034"},{"nama_pekerjaan":"FISIKAWAN MEDIK","kd_pekerjaan":"P075"},{"nama_pekerjaan":"GURU","kd_pekerjaan":"P035"},{"nama_pekerjaan":"IMAM MESJID","kd_pekerjaan":"P027"},{"nama_pekerjaan":"JURU MASAK","kd_pekerjaan":"P032"},{"nama_pekerjaan":"JURU PARKIR","kd_pekerjaan":"P062"},{"nama_pekerjaan":"KONSULTAN","kd_pekerjaan":"P039"},{"nama_pekerjaan":"LOGISTIK","kd_pekerjaan":"P085"},{"nama_pekerjaan":"MAHASISWA KERJA PRAKTEK","kd_pekerjaan":"P097"},{"nama_pekerjaan":"MANDIRI AGEN","kd_pekerjaan":"P098"},{"nama_pekerjaan":"MARBOT MESJID","kd_pekerjaan":"P070"},{"nama_pekerjaan":"MEKANIK","kd_pekerjaan":"P021"},{"nama_pekerjaan":"MITRA GOJEK","kd_pekerjaan":"P053"},{"nama_pekerjaan":"MITRA GOJEK-GO LIFE","kd_pekerjaan":"P071"},{"nama_pekerjaan":"MITRA GRAB","kd_pekerjaan":"P054"},{"nama_pekerjaan":"MITRA SHOPEE","kd_pekerjaan":"P095"},{"nama_pekerjaan":"MITRA TELEPORT AIRASIA","kd_pekerjaan":"P093"},{"nama_pekerjaan":"MITRA UBER","kd_pekerjaan":"P055"},{"nama_pekerjaan":"NARAPIDANA DALAM PROSES ASIMILASI","kd_pekerjaan":"P059"},{"nama_pekerjaan":"NELAYAN\/PERIKANAN","kd_pekerjaan":"P003"},{"nama_pekerjaan":"NOTARIS","kd_pekerjaan":"P037"},{"nama_pekerjaan":"PANDAI BESI","kd_pekerjaan":"P104"},{"nama_pekerjaan":"PANDITA\/PEMANGKU","kd_pekerjaan":"P105"},{"nama_pekerjaan":"PARAJI","kd_pekerjaan":"P024"},{"nama_pekerjaan":"PARANORMAL","kd_pekerjaan":"P049"},{"nama_pekerjaan":"PASTOR","kd_pekerjaan":"P029"},{"nama_pekerjaan":"PEDAGANG","kd_pekerjaan":"P050"},{"nama_pekerjaan":"PELAUT","kd_pekerjaan":"P045"},{"nama_pekerjaan":"PEMANDU LAGU","kd_pekerjaan":"P064"},{"nama_pekerjaan":"PEMASAR IKAN","kd_pekerjaan":"P087"},{"nama_pekerjaan":"PEMASAR PELABUHAN","kd_pekerjaan":"P088"},{"nama_pekerjaan":"PEMBANTU RUMAH TANGGA","kd_pekerjaan":"P009"},{"nama_pekerjaan":"PEMBUDIDAYA IKAN","kd_pekerjaan":"P090"},{"nama_pekerjaan":"PEMULUNG","kd_pekerjaan":"P069"},{"nama_pekerjaan":"PENATA BUSANA","kd_pekerjaan":"P019"},{"nama_pekerjaan":"PENATA RAMBUT","kd_pekerjaan":"P020"},{"nama_pekerjaan":"PENATA RIAS","kd_pekerjaan":"P018"},{"nama_pekerjaan":"PENDAMPING DESA","kd_pekerjaan":"P065"},{"nama_pekerjaan":"PENDETA","kd_pekerjaan":"P028"},{"nama_pekerjaan":"PENELITI","kd_pekerjaan":"P046"},{"nama_pekerjaan":"PENENUN","kd_pekerjaan":"P102"},{"nama_pekerjaan":"PENGACARA","kd_pekerjaan":"P036"},{"nama_pekerjaan":"PENGOLAH IKAN","kd_pekerjaan":"P089"},{"nama_pekerjaan":"PENGRAJIN","kd_pekerjaan":"P103"},{"nama_pekerjaan":"PENTERJEMAH","kd_pekerjaan":"P026"},{"nama_pekerjaan":"PENYIAR RADIO","kd_pekerjaan":"P044"},{"nama_pekerjaan":"PERANCANG BUSANA","kd_pekerjaan":"P025"},{"nama_pekerjaan":"PERAWAT","kd_pekerjaan":"P076"},{"nama_pekerjaan":"PEREKAM MEDIK DAN INFOKES","kd_pekerjaan":"P077"},{"nama_pekerjaan":"PERISAI","kd_pekerjaan":"P094"},{"nama_pekerjaan":"PESERTA BAKAT DAN MINAT","kd_pekerjaan":"P072"},{"nama_pekerjaan":"PESERTA MAGANG","kd_pekerjaan":"P056"},{"nama_pekerjaan":"PETAMBAK GARAM","kd_pekerjaan":"P091"},{"nama_pekerjaan":"PETANI\/PEKEBUN","kd_pekerjaan":"P001"},{"nama_pekerjaan":"PETERNAK","kd_pekerjaan":"P002"},{"nama_pekerjaan":"PETUGAS KEAMANAN","kd_pekerjaan":"P086"},{"nama_pekerjaan":"PETUGAS KEBERSIHAN","kd_pekerjaan":"P101"},{"nama_pekerjaan":"PIALANG","kd_pekerjaan":"P048"},{"nama_pekerjaan":"PROMOTOR ACARA","kd_pekerjaan":"P033"},{"nama_pekerjaan":"PSIKIATER\/PSIKOLOG","kd_pekerjaan":"P043"},{"nama_pekerjaan":"PSIKOLOGI KLINIS","kd_pekerjaan":"P078"},{"nama_pekerjaan":"RADIOGRAFER","kd_pekerjaan":"P079"},{"nama_pekerjaan":"RELAWAN BAZNAS","kd_pekerjaan":"P107"},{"nama_pekerjaan":"RELAWAN TAGANA\/RELAWAN BENCANA","kd_pekerjaan":"P067"},{"nama_pekerjaan":"SEKRETARIS","kd_pekerjaan":"P084"},{"nama_pekerjaan":"SENIMAN","kd_pekerjaan":"P022"},{"nama_pekerjaan":"SISWA KERJA PRAKTEK","kd_pekerjaan":"P057"},{"nama_pekerjaan":"SOPIR","kd_pekerjaan":"P047"},{"nama_pekerjaan":"TABIB","kd_pekerjaan":"P023"},{"nama_pekerjaan":"TEKNISI LAB MEDIK","kd_pekerjaan":"P080"},{"nama_pekerjaan":"TENAGA HONORER (SELAIN PENYELENGGARA NEGARA)","kd_pekerjaan":"P058"},{"nama_pekerjaan":"TENAGA KESEHATAN LINGKUNGAN","kd_pekerjaan":"P081"},{"nama_pekerjaan":"TENAGA TEKNIS KEFARMASIAN","kd_pekerjaan":"P082"},{"nama_pekerjaan":"TRANSPORTASI","kd_pekerjaan":"P004"},{"nama_pekerjaan":"TUKANG BANGUNAN","kd_pekerjaan":"P099"},{"nama_pekerjaan":"TUKANG BATU","kd_pekerjaan":"P012"},{"nama_pekerjaan":"TUKANG CUKUR","kd_pekerjaan":"P010"},{"nama_pekerjaan":"TUKANG GIGI","kd_pekerjaan":"P017"},{"nama_pekerjaan":"TUKANG JAHIT","kd_pekerjaan":"P016"},{"nama_pekerjaan":"TUKANG KAYU","kd_pekerjaan":"P013"},{"nama_pekerjaan":"TUKANG KEBUN\/POTONG RUMPUT","kd_pekerjaan":"P100"},{"nama_pekerjaan":"TUKANG LAS\/PANDAI BESI","kd_pekerjaan":"P015"},{"nama_pekerjaan":"TUKANG LISTRIK","kd_pekerjaan":"P011"},{"nama_pekerjaan":"TUKANG PIJAT","kd_pekerjaan":"P063"},{"nama_pekerjaan":"TUKANG SAMPAH","kd_pekerjaan":"P068"},{"nama_pekerjaan":"TUKANG SOL SEPATU","kd_pekerjaan":"P014"},{"nama_pekerjaan":"USTADZ\/MUBALIGH","kd_pekerjaan":"P031"},{"nama_pekerjaan":"WARTAWAN","kd_pekerjaan":"P030"},{"nama_pekerjaan":"WIRASWASTA","kd_pekerjaan":"P052"}],"recordsTotal":107,"keterangan":"SUCCESS"}';
}

function bpjstk_kabupaten($data){
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $nik        = strtoupper($data->nik);
    $id_prop    = strtoupper($data->id_propinsi);
    $kdproduk   = strtoupper($data->kode_produk);

    $field      = 6;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    if($nik == "" || strlen($nik) != 16){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"09",
                'keterangan'=>"Nik tidak valid"
            )
        );
    }

    if (!checkpin($uid, $pin)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"02",
                'keterangan'=>"pin yang anda masukkan salah"
            )
        );
    }
    $stp = $GLOBALS["step"] + 1;
    $data->kd_prov = $id_prop;
    $data->outlet_id = $uid;
    $fm = generateBpsjTkAstDev($data, $stp, "BPJS_KABUPATEN");
    // echo $fm;die();

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    
    $respon = postValueBpjsTk($fm, true);
    $resp = $respon[7];

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    // print_r($frm);

    $r_step         = $frm->getStep()+1;
    $r_kdproduk     = $frm->getKodeProduk();
    $r_tanggal      = $frm->getTanggal();
    $r_idoutlet     = $frm->getMember();
    $r_pin          = $frm->getPin();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();
    $r_mid          = $frm->getMID();

    if($r_status == "00"){
        $ret = array(
            "uid" => $r_idoutlet,
            "pin" => "------",
            "status" => "00",
            "keterangan" => "SUCCESS",
            "result" => json_decode($r_keterangan)
        );
        return json_encode($ret, JSON_PRETTY_PRINT);
    } else {
        $ret = array(
            "uid" => $r_idoutlet,
            "pin" => "------",
            "status" => $r_status,
            "keterangan" => $r_keterangan,
            "result" => array()
        );
        return json_encode($ret, JSON_PRETTY_PRINT);
    }
}

function bpjstk_cabang($data){
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $id_cab     = strtoupper($data->id_cabang);
    $kdproduk   = strtoupper($data->kode_produk);
    $nik        = strtoupper($data->nik);

    $field      = 6;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    if($nik == "" || strlen($nik) != 16){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"09",
                'keterangan'=>"Nik tidak valid"
            )
        );
    }

    if (!checkpin($uid, $pin)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"02",
                'keterangan'=>"pin yang anda masukkan salah"
            )
        );
    }
    $stp = $GLOBALS["step"] + 1;
    $data->outlet_id = $uid;
    $data->kd_cabang = $id_cab;
    $fm = generateBpsjTkAstDev($data, $stp, "BPJS_KANTOR_CABANG");
    // echo $fm;die('fasfa');

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, true);
    $resp = $respon[7];

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    // print_r($frm);

    $r_step         = $frm->getStep()+1;
    $r_kdproduk     = $frm->getKodeProduk();
    $r_tanggal      = $frm->getTanggal();
    $r_idoutlet     = $frm->getMember();
    $r_pin          = $frm->getPin();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();
    $r_mid          = $frm->getMID();

    if($r_status == "00"){
        $ret = array(
            "uid" => $r_idoutlet,
            "pin" => "------",
            "status" => "00",
            "keterangan" => "SUCCESS",
            "result" => json_decode($r_keterangan)
        );
        return json_encode($ret, JSON_PRETTY_PRINT);
    } else {
        $ret = array(
            "uid" => $r_idoutlet,
            "pin" => "------",
            "status" => $r_status,
            "keterangan" => "Data tidak ditemukan",
            "result" => array()
        );
        return json_encode($ret, JSON_PRETTY_PRINT);
    }
}

function bpjstk_hitung_iuran($data){
    $uid =  $data->uid;
    $pin =  $data->pin;
    $nik =  $data->nik;
    $kode_produk =  $data->kode_produk;
    $kd_lokasi =  $data->kd_lokasi;
    $program =  $data->program;
    $program_array = array('2','3');
    $periode = $data->periode; // 1 2 3 6 12 bln
    $periode_array = array('1','2','3','6','12');

    $upah = $data->upah;

    $field      = 9;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    
    $allrequired = allRequired($data);
    if($allrequired){
        return json_encode(
            array(
                'uid'=>$data->uid,
                'pin'=>"------",
                'status'=>"11",
                'keterangan'=>"Semua field harus diisi"
            )
        );
    }

    if(!in_array($program,$program_array)){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"10",
                'keterangan'=>"Program tidak valid"
            )
        );
    }

    if(!in_array($periode,$periode_array)){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"08",
                'keterangan'=>"Periode tidak valid"
            )
        );
    }

    if($nik == "" || strlen($nik) != 16){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"09",
                'keterangan'=>"Nik tidak valid"
            )
        );
    }

    $newUpah = (int) $upah;
    if($newUpah <= 1000){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"10",
                'keterangan'=>"Upah tidak valid"
            )
        );
    }

    if (!checkpin($uid, $pin)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"02",
                'keterangan'=>"pin yang anda masukkan salah"
            )
        );
    }

    
    $stp = $GLOBALS["step"] + 1;
    $data->idpel1 = $nik;
    $data->outlet_id = $uid;
    $data->biaya_reg = "0#0";
    $fm = generateBpsjTkAstDev((object)$data, $stp, "HITUNG_IURAN");

    // echo $fm;die();

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, true);
    $resp = $respon[7];
    // echo $resp;

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kode_produk, $resp);
    // print_r($frm);

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
    $r_mid          = $frm->getMID();
    $r_saldo_terpotong = $r_nominal + $r_nominaladmin;
    
    
    
    // die('x');
    $nom_up = getnominalup($r_idtrx);
    $url_struk = "";

    //coyyyy
    // if($r_status == "00"){
        $jht = $frm->getJht();
        $jkk = $frm->getJkk();
        $jkm = $frm->getJkm();

        $blnJkm = $frm->getBlnJkm();
        $blnJkk = $frm->getBlnJkk();
        $blnJht = $frm->getBlnJht();

        $ret = array(
            'uid' => $r_idoutlet,
            'pin' => '------',
            'status' => $r_status,
            'keterangan' => $r_keterangan,
            'nominal' => $r_nominal,
            'upah' => $data->upah,
            'nik' => $data->nik,
            'jht' => $jht,
            'jkk' => $jkk,
            'jkm' => $jkm,
            'bln_jht' => $blnJht,
            'bln_jkk' => $blnJkk,
            'bln_jkm' => $blnJkm,
            'code' => $frm->getData1()
        );

        return json_encode($ret);
    // }

}

function bpjstk_proses_iuran($data){
    $cmd = "GET_KODE_BAYAR";
    $uid = $data->uid;
    $pin = $data->pin;
    $nik = $data->nik;
    $kode_produk = $data->kode_produk;
    $code = $data->code;

    $field      = 6;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    $allrequired = allRequired($data);
    if($allrequired){
        return json_encode(
            array(
                'uid'=>$data->uid,
                'pin'=>"------",
                'status'=>"11",
                'keterangan'=>"Semua field harus diisi"
            )
        );
    }

    if($nik == "" || strlen($nik) != 16){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"09",
                'keterangan'=>"Nik tidak valid"
            )
        );
    }

    if (!checkpin($uid, $pin)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"02",
                'keterangan'=>"pin yang anda masukkan salah"
            )
        );
    }

    $stp = $GLOBALS["step"] + 1;
    $data->idpel1 = $nik;
    $data->outlet_id = $uid;
    $data->data1 = $code;
    $fm = generateBpsjTkAstDev((object)$data, $stp, $cmd);

    // echo $fm;die();

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, true);
    $resp = $respon[7];
    // echo $resp;
    // $resp = "BPJSADM*ASRBPJSTK*1657131*10*20220622114715*H2H*6302062104830004****0*FA9919*414455*******00*SUKSES PEMBUATAN KODE IURAN*GET_KODE_BAYAR*6302062104830004***922063116251**922063116251***WFP20220622074905004*************************************";

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kode_produk, $resp);
    // print_r($frm);

    $r_idoutlet     = $frm->getMember();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();

    $ret = array(
        "uid" => $r_idoutlet,
        "pin" => "------",
        "nik" => $data->nik,
        "kode_bayar" => $frm->getKode_Bayar(),
        "status" => $r_status,
        "keterangan" => $r_keterangan,
    );
    return json_encode($ret, JSON_PRETTY_PRINT);

}

function bpjstk_daftar($data){
    //BPJS_DAFTAR
    $cmd = "BPJS_DAFTAR";
    $nik = $data->nik;
    $nama = $data->nama;
    $tgllahir = $data->tgllahir;
    $no_hp = $data->no_hp;
    $email = $data->email;
    $alamat = $data->alamat;
    $jam_awal = $data->jam_awal;
    $jam_akhir = $data->jam_akhir;
    $jenis_kerja = $data->jenis_kerja;
    $kd_lokasi = $data->kd_lokasi; // 1971
    $kd_cabang = $data->kd_cabang; // G02
    $id_produk = $data->kode_produk;
    
    $periode = $data->periode; // 1 2 3 6 12 bln
    $periode_array = array('1','2','3','6','12');

    $program = $data->program; // 2 jkk jkm, 3 jkk jkm jht
    $program_array = array('2','3');

    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);

    $field      = 17;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    $allrequired = allRequired($data);
    if($allrequired){
        return json_encode(
            array(
                'uid'=>$data->uid,
                'pin'=>"------",
                'status'=>"11",
                'keterangan'=>"Semua field harus diisi"
            )
        );
    }

    if(!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $jam_awal)){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"08",
                'keterangan'=>"Jam awal tidak valid (HH:mm)"
            )
        );
    }

    if(!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $jam_akhir)){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"08",
                'keterangan'=>"Jam akhir tidak valid (HH:mm)"
            )
        );
    }

    if(!in_array($periode,$periode_array)){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"08",
                'keterangan'=>"Periode tidak valid"
            )
        );
    }

    if(!in_array($program,$program_array)){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"10",
                'keterangan'=>"Program tidak valid"
            )
        );
    }

    if($nik == "" || strlen($nik) != 16){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"09",
                'keterangan'=>"Nik tidak valid"
            )
        );
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"08",
                'keterangan'=>"Email tidak valid"
            )
        );
    }

    if (!checkpin($uid, $pin)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"02",
                'keterangan'=>"pin yang anda masukkan salah"
            )
        );
    }

    $stp = $GLOBALS["step"] + 1;
    $data->idpel1 = $nik;
    $data->outlet_id = $uid;
    $data->biaya_reg = "0#0";
    $fm = generateBpsjTkAstDev((object)$data, $stp, $cmd);

    // echo $fm;die();

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, true);
    $resp = $respon[7];
    // echo $resp;

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($id_produk, $resp);
    // print_r($frm);

    $r_idoutlet     = $frm->getMember();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();

    $ret = array(
        "uid" => $r_idoutlet,
        "pin" => "------",
        "nik" => $data->nik,
        "nama" => $data->nama,
        "status" => $r_status,
        "keterangan" => $r_keterangan,
    );
    return json_encode($ret, JSON_PRETTY_PRINT);
}



function bpjstk_info_peserta($data){
    // bpjs_ver
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $kdproduk   = strtoupper($data->kode_produk);
    $nik        = strtoupper($data->nik);
    $nama       = strtoupper($data->nama);
    $hp         = strtoupper($data->no_hp);
    $tgl_lahir  = strtoupper($data->tgllahir);
    
    $field      = 8;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    if($nik == "" || strlen($nik) != 16){
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"09",
                'keterangan'=>"Nik tidak valid"
            )
        );
    }

    if (!checkpin($uid, $pin)) {
        return json_encode(
            array(
                'uid'=>$uid,
                'pin'=>"------",
                'status'=>"02",
                'keterangan'=>"pin yang anda masukkan salah"
            )
        );
    }

    $stp = $GLOBALS["step"] + 1;
    $data->outlet_id = $uid;
    $fm = generateBpsjTkAstDev((object)$data, $stp, "BPJS_VER");

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, true);
    $resp = $respon[7];

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    // print_r($frm);

    $r_step         = $frm->getStep()+1;
    $r_kdproduk     = $frm->getKodeProduk();
    $r_tanggal      = $frm->getTanggal();
    $r_idpel1       = $frm->getIdPel1();
    $r_idpel2       = $frm->getIdPel2();
    $r_idpel3       = $frm->getIdPel3();
    $r_idoutlet     = $frm->getMember();
    $r_status       = $frm->getStatus();
    $r_keterangan   = $frm->getKeterangan();

    $ret = array(
        "uid" => $r_idoutlet,
        "pin" => "------",
        "status" => $r_status,
        "nik" => $data->nik,
        "nama" => $data->nama,
        "tgllahir" => $data->tgllahir,
        "keterangan" => $r_keterangan,
    );
    return json_encode($ret, JSON_PRETTY_PRINT);

}

//========= daftar bpjstk end ==============//

function bpjstk_pay($data){

    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel);
    $nominal    = strtoupper($data->nominal);
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $ref2       = strtoupper($data->ref2);

    $field      = 8;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    if(strlen($idpel1) == 16){
        $cmd    = "PAY_BPJS";
    } else if(strlen($idpel1) == 12){
        $cmd    = "PAY_PU_EPS_BPJS";
    } else {
        return json_encode(array('error'=>'nik/idpel tidak valid'));
    }
    
    // ================ disable lek sek nggawe hardcode ================
    // $cektrx = cek_trx_bpjstk($ref2, $uid, false); 
    // if($nominal != $cektrx['nominal']){
    //     return json_encode(array('error'=>'nominal yang dibayarkan tidak sesuai'));
    // }
    // ================ disable lek sek nggawe hardcode ================
    
    if($cmd == "PAY_BPJS"){
        $param_request = array(
            "kode_produk"   => $kdproduk,
            "cmd"           => $cmd,
            "kode_bayar"    => $cektrx['kode_bayar'],
            "nik"           => $idpel1,
            "idpel1"        => $idpel1,
            "reff"          => $ref2,
            "nominal"       => $nominal,
            "jam_awal"      => $cektrx['jam_awal'],
            "jam_akhir"     => $cektrx['jam_akhir'],
            "jenis_kerja"   => $cektrx['jenis_kerja'],
            "kd_lokasi"     => $cektrx['lokasi_kerja'],
            "biaya_reg"     => $cektrx['biaya_reg'],
            "ref1"          => $ref1,
            'outlet_id'     => $uid,
            'pin'           => $pin,
        ); 
    } else {
        $param_request = array(
            "kode_produk"   => $kdproduk,
            "cmd"           => $cmd,
            "kode_bayar"    => $cektrx['kd_iuran'],
            "data2"         => $cektrx['kode_bayar'], // gawe ngelengkapi asterix
            "jht"           => $cektrx['jht'], // gawe ngelengkapi asterix
            "jkk"           => $cektrx['jkk'], // gawe ngelengkapi asterix
            "jkm"           => $cektrx['jkm'], // gawe ngelengkapi asterix
            "nik"           => $idpel1,
            "idpel1"        => $idpel1,
            "reff"          => $ref2,
            "nominal"       => $nominal,
            'outlet_id'     => $uid,
            'pin'           => $pin,
            'kd_iuran'      => date('d-m-Y H:i:s') // di asterix n core masuk data3 alias kd_iuran
        ); 
    }
    

    $stp = $GLOBALS["step"] + 1;
    $fm = generateBpsjTkAstDev((object)$param_request, $stp, $cmd);
    // echo $fm;die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    
    // $respon = postValueBpjsTk($fm, true);
    // $resp = $respon[7];
  
    if($idpel1 == '1602150204910001'){
        $resp = 'BPJSPAY*ASRBPJSTK*1650890*10*20220609143644*H2H*1602150204910001***112600*0*FA9919*414455**88650295*1***124530*00*SUKSES PAY IURAN BPU*PAY_BPJS*1602150204910001*HERFRIADI*02-04-1991*{"invoke":"settleBayarIuran","nik":"1602150204910001","kodeIuran":"922063116154","tglSettle":"#TGL#","kodeSettle":"HFP20220609143419001#4567481111101100#JKK4410069#JKM3475613#8525658"}***922063116154**HFP20220609143419001***22374000002*P999*00:00*23:59*alamat palsu saya di*emailkuuu@gmail.com*3578*5000000****08564212154124****JKK=99000.00*JKK=0*JKM=13600.00*JKM=0***0#0*N00*JL. KARIMUNJAWA NO. 6 SURABAYA****2**JKM=2*JKK=2**JKK_AKTIF=21-04-2022 11:37:35#JKK_EFEKTIF=21-04-2022 11:37:35#JKK_EXPIRED=20-05-2023 00:00:00#JKK_GRACE=20-08-2023 00:00:00#JKM_AKTIF=21-04-2022 11:37:35#JKM_EFEKTIF=21-04-2022 11:37:35#JKM_EXPIRED=20-05-2023 00:00:00#JKM_GRACE=20-08-2023 00:00:00*JKK=99000.00#JKM=13600.00*';
    } else if($idpel1 == "331605012985"){
        $resp = 'BPJSPAY*ASRBPJSTK*1654245595992128423*7*20220603154000*WEB*331605012985***1456000*0*FA9919*------**94223200*1***124370*00*SUKSES PAYMENT PU*PAY_PU_EPS_BPJS*331605012985***{"invoke":"settleTagihanPU","kodeIuran":"220600000859","tglTrx":"#TGL#","totalIuran":1456000,"kodeRefBank":"JKK2335634#JKM1717569#JHT4513107#JPN2373913"}***0#0#15057464#000*03-06-2022 15:39:55*WFP20220603153852985****************1330000**56000**70000**PU_EPS_PAY*************220600000859**';
    } else if($idpel1 == "220600001028"){
        $resp = 'BPJSPAY*ASRBPJSTK*1650803*8*20220609133344*H2H*220600001028***14500*0*FA9919*414455**88762895*1***124528*00*SUKSES PAYMENT PU*PAY_PU_EPS_BPJS*220600001028***{"invoke":"settleTagihanPU","kodeIuran":"220600001028","tglTrx":"#TGL#","totalIuran":14500,"kodeRefBank":"JKK6587826#JKM2614733#JHT3474601#JPN8057866"}***0#0#15066810#000*09-06-2022 13:33:41*HFP20220609120204028****************13244**556**700**PU_EPS_PAY***************';
    } else if($idpel1 == "3515094703960002"){
        $resp = 'BPJSPAY*ASRBPJSTK*1650754*10*20220609115506*H2H*3515094703960002***53600*0*FA9919*414455**88777395*1***124526*00*SUKSES PAY IURAN BPU*PAY_BPJS*3515094703960002*MARETTA IDFIANI*07-03-1996*{"invoke":"settleBayarIuran","nik":"3515094703960002","kodeIuran":"922063116151","tglSettle":"#TGL#","kodeSettle":"HFP20220609114644002#4567481111101100#JKK9207670#JKM5566391#3960610"}***922063116151**HFP20220609114644002***22770000002*IT*00:00*23:59*Sidoarjo*midfiani@gmail.com*3515*****082334658122****JKK=40000.00*JKK=0*JKM=13600.00*JKM=0***0#0*N11*JL. PAHLAWAN PINANG INDAH BLOK A2 NO. 1-4 SIDOARJO 61251 P.O. BOX 210******JKM=2*JKK=2**JKK_AKTIF=26-01-2022 10:36:55#JKK_EFEKTIF=26-01-2022 10:36:55#JKK_EXPIRED=25-03-2025 00:00:00#JKK_GRACE=25-06-2025 00:00:00#JKM_AKTIF=26-01-2022 10:36:55#JKM_EFEKTIF=26-01-2022 10:36:55#JKM_EXPIRED=25-03-2025 00:00:00#JKM_GRACE=25-06-2025 00:00:00*JKK=40000.00#JKM=13600.00*';
    } else {
        $resp = "BPJSPAY*ASRBPJSTK*1632462*8*20220329145410*H2H*331704010906***30000*0*FA9919*414455**1488000*1***122060*00*SUCCESS*PAY_PU_EPS_BPJS*331704010906***220300002444***Sukses*29-03-2022 14:54:07**************************************";
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
    // print_r($frm);

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
    $r_mid          = $frm->getMID();
    $r_saldo_terpotong = $r_nominal + $r_nominaladmin;
    
    
    
    // die('x');
    $nom_up = getnominalup($r_idtrx);
    $url_struk = "";
    if ($frm->getStatus() == "00") {
        $nom_up = getnominalup($r_idtrx);
        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
        $url = enkripUrl(strtoupper($r_idoutlet), $frm->getIdTrx());
        $url_struk = "https://202.43.173.234/struk/?id=" . $url;
    }

    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }
    
    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "NIK"               => (string) $r_idpel1,
        "NAMA_PELANGGAN"    => (string) $frm->getCustomerName(),
        "PERIODE"           => (string) $frm->getPeriode(),
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => (string) $r_mid,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) $r_keterangan,
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) $url_struk
    );

    if ($frm->getStatus() == "00") {
        $params['DETAIL'] = addDataBpjstkPay($frm, $cmd);
    }

    return json_encode($params, JSON_PRETTY_PRINT);
    
}

function bpjstk_step_register(){ 
    $step = array(
        "step1" => array(
            "method" => "rajabiller.bpjstk_info_peserta",
            "sebelum_request" => "",
            "setelah_request" => "jika sukses, lanjut ke step2 sampai step5 (manual isi form)",
            "display_form" => true,
            "background_process" => false,
        ),
        "step2" => array(
            "method" => "rajabiller.bpjstk_propinsi",
            "sebelum_request" => "",
            "setelah_request" => "",
            "display_form" => false,
            "background_process" => true,
        ),
        "step3" => array(
            "method" => "rajabiller.bpjstk_pekerjaan",
            "sebelum_request" => "",
            "setelah_request" => "",
            "display_form" => false,
            "background_process" => true,
        ),
        "step4" => array(
            "method" => "rajabiller.bpjstk_kabupaten",
            "sebelum_request" => "",
            "setelah_request" => "",
            "display_form" => false,
            "background_process" => true,
        ),
        "step5" => array(
            "method" => "rajabiller.bpjstk_cabang",
            "sebelum_request" => "",
            "setelah_request" => "",
            "display_form" => false,
            "background_process" => true,
        ),
        "step6" => array(
            "method" => "rajabiller.bpjstk_daftar",
            "sebelum_request" => "semua request api ini diambil dari step2 sampai step5.",
            "setelah_request" => "jika sukses, lanjut ke step7 dan step8",
            "display_form" => true,
            "background_process" => false,
        ),
        "step7" => array(
            "method" => "rajabiller.bpjstk_hitung_iuran",
            "sebelum_request" => "",
            "setelah_request" => "jika sukses lanjut ke step8",
            "display_form" => false,
            "background_process" => true,
        ),
        "step8" => array(
            "method" => "rajabiller.bpjstk_proses_iuran",
            "sebelum_request" => "klik back bisa balik ke step6 (case jika end user ingin mengganti upah, ganti jam_awal dll)",
            "setelah_request" => "Jika sukses, bisa lanjut step9 dan step10",
            "display_form" => true,
            "background_process" => false,
        ),
        "step9" => array(
            "method" => "rajabiller.bpjstk_inq",
            "sebelum_request" => "",
            "setelah_request" => "jika sukses lanjut ke step10",
            "display_form" => false,
            "background_process" => true,
        ),
        "step10" => array(
            "method" => "rajabiller.bpjstk_pay",
            "sebelum_request" => "",
            "setelah_request" => "",
            "display_form" => true,
            "background_process" => false,
        )
    );

    echo json_encode($step);
}

function bpjstk_inq($data){
    
    // ==================================================
    // SKIP CMD GET_DATA_PESERTA langsung BPJS_INQ_BY_NIK
    // ==================================================

    $kdproduk   = strtoupper($data->kode_produk);
    $idpel1     = strtoupper($data->idpel);
    $uid        = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    
    if(strlen($idpel1) == 16){
        $cmd        = "INQ_BPJS";
        $periode    = strtoupper($data->periode);
        $field      = 7;
    } else if(strlen($idpel1) == 12){
        $cmd    = "INQ_BPJS_PU_EPS";
        $field      = 6;
    } else {
        return json_encode(array('error'=>'nik/idpel tidak valid'));
    }

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'invalid parameter request'));
    }

    $periode_arr = array('1','2','3','6','12');
    if(!in_array($periode, $periode_arr) && $cmd == "INQ_BPJS"){
        return json_encode(array('error'=>'periode hanya '.implode(', ',$periode_arr)));
    }

    // print_r($data);
    // die();
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if($uid != 'SP300203' && $uid != 'FA9919'){
        if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
            if (!isValidIP($uid, $ip)) {
                return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
            }
        }
    }
// die('xx');
    // echo $GLOBALS["mid"];die();
    // global $pgsql;
    global $host;
    $stp = $GLOBALS["step"] + 1;
    
    $data->idpel1 = $idpel1; // gawe insert ning asterix dipadakno ambe nik
    $data->nik = $idpel1; // gawe insert ning asterix dipadakno ambe nik
    $data->outlet_id = $uid; // gawe insert ning asterix dipadakno ambe nik
    $fm = generateBpsjTkAstDev($data, $stp, $cmd);
    // echo $fm;die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    // $respon = postValueBpjsTk($fm, true);
    // $resp = $respon[7];
    
    if($idpel1 == '1602150204910001'){
        // masih ada tunggakan
        $resp = "BPJSADM*ASRBPJSTK*1632250*10*20220323091942*H2H*1602150204910001****3500**414455******1632250*00*SUKSES CEK NIK*BPJS_INQ_BY_NIK*1602150204910001*HERFRIADI*02-04-1991*922033115116***922033115116***56300*0**P999*07: 49*18: 49*alamat palsu saya di**3578*5000000********49500**6800**BAYAR TUNGGAKAN 1 BULAN**0#0*N00*JL. KARIMUNJAWA NO. 6 SURABAYA, SURABAYA 60281, TELP: 031-5031183, FAKS: 031-5017014****1*****JKK,JKM*23-03-2022 09: 19: 42#22-04-2022 00: 00: 00*";
    } else if($idpel1 == '6302062104830004'){
        //blm kedaftar
        $resp = "BPJSADM*ASRBPJSTK*1648011989579258003*7*20220323120629*WEB*****0*FA9919*414455*******20*NO.KTP/NIK BELUM TERDAFTAR, SILAHKAN MELAKUKAN PENDAFTARAN TERLEBIH DAHULU !*GET_DATA_PESERTA*6302062104830004**************************************1*******";
    } else if($idpel1 == '3515094703960002'){
        $resp = "BPJSINQ*ASRBPJSTK*1650752*10*20220609114648*H2H*3515094703960002***53600*0*FA9919*414455**88830995*1***124525*00*SUKSES INQ IURAN BPU*INQ_BPJS*3515094703960002*MARETTA IDFIANI*07-03-1996*BLTH=03-2025#PROG_JKK=40000:1.0:0:59:2:92#PROG_JKM=13600:0.0:0:59:2:92#BIAYA_TRANSAKSI=0#BIAYA_REGISTRASI=0#TOTAL=53600#UPAH=2000000#DASAR_UPAH=2000000#UMP=0**MARETTA IDFIANI*BLTH=03-2025#PROG_JKK=40000:1.0:0:59:2:92#PROG_JKM=13600:0.0:0:59:2:92#BIAYA_TRANSAKSI=0#BIAYA_REGISTRASI=0#TOTAL=53600#UPAH=2000000#DASAR_UPAH=2000000#UMP=0**HFP20220609114644002*53600*0**IT*08:00*17:00*Sidoarjo**3515*2000000********40000:1.0:0:59:2:92*1*13600:0.0:0:59:2:92*0*TAGIHAN BARU 2 BULAN**0#0*N11*SIDOARJO****2**26-01-2022 10:36:55#25-01-2025 00:00:00*26-01-2022 10:36:55#25-01-2025 00:00:00**JKK,JKM*26-01-2022 10:36:55#25-03-2025 00:00:00*";
    }  else if ($idpel1 == '331605012985'){
        $resp = "BPJSINQ*ASRBPJSTK*1654245532203975933*9*20220603153858*WEB*331605012985***1456000*0*FA9919*------**95679200*1***124369*00*SUKSES INQ IURAN PU EPS*INQ_BPJS_PU_EPS*331605012985******0#0#15057464#000**WFP20220603153852985*********TAMAR MITRA GLOBAL*******1330000**56000**70000**PU_EPS_INQ********06/2022*****220600000859**";
    } else if($idpel1 == '220600001028'){
        $resp = "BPJSINQ*ASRBPJSTK*1650756*10*20220609120208*H2H*220600001028***14500*0*FA9919*414455**88777395*1***124527*00*SUKSES INQ IURAN PU EPS*INQ_BPJS_PU_EPS*220600001028******0#0#15066810#000**HFP20220609120204028*********BERMUARA ABADI*******13244**556**700**PU_EPS_INQ********07/2022*****220600001028**";
    } else {
        $resp = "BPJSINQ*ASRBPJSTK*1632464*10*20220329153741*H2H*331704010906***20000*0*FA9919*414455**1488000*1***122062*00*SUKSES INQ IURAN PU EPS*INQ_BPJS_PU_EPS*331704010906******0#0#15060352#000***********LUCASTA MURNI CEMERLANG*******18268**768**964**PU_EPS_INQ********04/2022*****220300002532**";
        // $respon = postValueBpjsTk($fm, true);
        // $resp = $respon[7];
    }
    // ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
    // echo $resp;die();
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kdproduk, $resp);

    $catatan = "-";
    if($frm->getStatus() == '00' && $cmd == "INQ_BPJS"){
        $lanjutinq = lanjutInq($frm, $data);
        $catatan = $lanjutinq;
    } 
    // print_r($frm);
    // die();

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
    $r_mid          = $frm->getMID();
    $r_saldo_terpotong = $r_nominal + $r_nominaladmin;
    
    
    
    // die('x');
    $nom_up = getnominalup($r_idtrx);

    $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }
    // ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
    // print_r($frm->getPeriode());
    // die();

    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "NIK"               => (string) $r_idpel1,
        "NAMA_PELANGGAN"    => $cmd == "INQ_BPJS_PU_EPS" ? (string) $frm->getLokasi_kerja() : (string) $frm->getCustomerName(),
        "PERIODE"           => (string) $frm->getPeriode(),
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => (string) $r_mid,
        "STATUS"            => (string) $r_status,
        "KET"               => (string) $r_keterangan,
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) $url_struk
    );
    
    if ($frm->getStatus() == "00") {
        $params['DETAIL'] = addDataBpjstkInq($frm, $catatan, $cmd);
    }

    return json_encode($params, JSON_PRETTY_PRINT);
}

function lanjutInq($frm, $data){
    
    $data2 = $frm->getData2();
    $isNew = $frm->getIsnew();
    $periode_req = (int) $data->periode;
    $periode_res = (int) $frm->getPeriode();

    if(strlen($data2) == 12 && substr(strtoupper($isNew),0,5) == "BAYAR"){
        if($periode_req != $periode_res){
            $status_trx = "Masih terdapat iuran $periode_res bulan yang belum dibayarkan";
        } else {
            $status_trx = "Pembayaran tagihan pendaftaran $periode_res bulan";
        }
    } else {
        $status_trx = "Pembayaran baru $periode_res bulan";
    }
    return $status_trx;
    
}

function generateBpsjTkAstDev($data, $stp, $FIELD_CMD){
    $FIELD_CMD = strtoupper($FIELD_CMD);
    $msg = array();
    $i = -1;

    $bpjsadm = array(
        'BPJS_PROVINSI','BPJS_KABUPATEN','GET_DATA_PESERTA',
        'BPJS_INQ_BY_NIK','BPJS_PEKERJAAN_BPU','BPJS_KANTOR_CABANG',
        'SETTLE_BAYAR_IURAN','GET_KODE_BAYAR','HITUNG_IURAN',
        'BPJS_DAFTAR','BPJS_VER');

    if(in_array($FIELD_CMD, $bpjsadm)){
        $mti = "BPJSADM";
    } else if ($FIELD_CMD == "INQ_BPJS") { //3
        $mti = "BPJSINQ";
    } else if ($FIELD_CMD == "INQ_BPJS_PU_EPS" || $FIELD_CMD == "INQ_BPJS_PU_VA") { 
        $mti = "BPJSINQ";
    } else if ($FIELD_CMD == "PAY_BPJS") { //4
        $mti = "BPJSPAY";
    } else if ($FIELD_CMD == "PAY_PU_EPS_BPJS" || $FIELD_CMD == "PAY_PU_VA_BPJS") { 
        $mti = "BPJSPAY";
    } 

    $idoutlet = empty($data->outlet_id) ? "" : $data->outlet_id;
    $pin = empty($data->pin) ? "" : $data->pin;
    $kode_produk = empty($data->kode_produk) ? "" : $data->kode_produk;
    $bill_info83 = empty($data->ref1) ? "" : $data->ref1;

    $kd_kab = empty($data->kode_kabupaten) ? "" : $data->kode_kabupaten;
    $nama = empty($data->nama) ? "" : $data->nama;
    $tgllahir = empty($data->tgllahir) ? "" : $data->tgllahir;
    $kd_iuran = empty($data->kd_iuran) ? "" : $data->kd_iuran;
    $alamat = empty($data->alamat) ? "" : $data->alamat;
    $email = empty($data->email) ? "" : $data->email;
    $upah = empty($data->upah) ? "" : $data->upah;
    $otp = empty($data->otp) ? "" : $data->otp;
    $kd_cabang = empty($data->kd_cabang) ? "" : $data->kd_cabang;
    $kd_prov = empty($data->kd_prov) ? "" : $data->kd_prov;
    $program = empty($data->program) ? "" : $data->program;
    $periode = empty($data->periode) ? "" : $data->periode;
    $status_hitung = empty($data->status_hitung) ? "" : $data->status_hitung;
    // kd_cabang
    // kd_kab
    // kd_prov

    $nik = empty($data->nik) ? "" : $data->nik;
    $kd_bayar = empty($data->kode_bayar) ? "" : $data->kode_bayar;
    $kd_lokasi = empty($data->kd_lokasi) ? "" : $data->kd_lokasi;
    $jam_awal = empty($data->jam_awal) ? "" : $data->jam_awal;
    $jam_akhir = empty($data->jam_akhir) ? "" : $data->jam_akhir;
    $jns_kerja = empty($data->jenis_kerja) ? "" : $data->jenis_kerja;

    $data1 = empty($data->data1) ? "" : $data->data1;
    $data2 = empty($data->data2) ? "" : $data->data2;

    $jht = empty($data->jht) ? "" : $data->jht;
    $jkk = empty($data->jkk) ? "" : $data->jkk;
    $jkm = empty($data->jkm) ? "" : $data->jkm;

    $FIELD_IDPEL1 = empty($data->idpel1) ? "" : $data->idpel1;
    $FIELD_IDPEL2 = empty($data->idpel2) ? "" : $data->idpel2;
    $FIELD_IDPEL3 = empty($data->idpel3) ? "" : $data->idpel3;
    $FIELD_NOMINAL = empty($data->nominal) ? "" : $data->nominal;
    $FIELD_ADMIN = empty($data->admin) ? "" : $data->admin;
    $FIELD_NOHP = empty($data->no_hp) ? "" : $data->no_hp;
    $FIELD_REFF = empty($data->reff) ? "" : $data->reff;
    $FIELD_BIAYA_REG = empty($data->biaya_reg) ? "" : $data->biaya_reg;

    $msg[$i+=1] = $mti;
    $msg[$i+=1] = $kode_produk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = "";
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = "$FIELD_IDPEL1";
    $msg[$i+=1] = "$FIELD_IDPEL2";
    $msg[$i+=1] = "$FIELD_IDPEL3";
    $msg[$i+=1] = "$FIELD_NOMINAL";
    $msg[$i+=1] = "$FIELD_ADMIN";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "$FIELD_REFF";
    $msg[$i+=1] = $bill_info83;
    $msg[$i+=1] = ";;;;" . $FIELD_IDPEL3; // KETERANGAN
    $msg[$i+=1] = $FIELD_CMD; // CMD//1
    $msg[$i+=1] = $nik; //NIK //2
    $msg[$i+=1] = $nama; //CUSTOMER NAME//3 ---
    $msg[$i+=1] = $tgllahir; //TGL LAHIR //4 ---
    $msg[$i+=1] = $kd_bayar; //KODE BAYAR //5
    $msg[$i+=1] = ""; //Ftrx_Id//6
    $msg[$i+=1] = $data1; //DATA1//7
    $msg[$i+=1] = $data2; //DATA2//8
    $msg[$i+=1] = $kd_iuran; //DATA3//9 ---
    $msg[$i+=1] = ""; //DATA4//10
    $msg[$i+=1] = ""; //TAGIHAN//11
    $msg[$i+=1] = ""; //ADMIN//12
    $msg[$i+=1] = ""; //KPJ//13
    $msg[$i+=1] = $jns_kerja; //PEKERJAAN//14
    $msg[$i+=1] = $jam_awal; //JAM AWAL//15
    $msg[$i+=1] = $jam_akhir; //JAM AKHIR//16
    $msg[$i+=1] = $alamat; //ALAMAT//17 ---
    $msg[$i+=1] = $email; //ALAMAT EMAIL//18 ---
    $msg[$i+=1] = $kd_lokasi; //LOKASI KERJA///19 ---
    $msg[$i+=1] = $upah; //UPAH//20 ---
    $msg[$i+=1] = ""; //KEC//21
    $msg[$i+=1] = ""; //KEL//22
    $msg[$i+=1] = ""; //KODE POS//23
    $msg[$i+=1] = "$FIELD_NOHP"; //HP//24
    $msg[$i+=1] = $otp; //OTP//25 ---
    $msg[$i+=1] = $jht; //JHT//26
    $msg[$i+=1] = ""; //RATE JHT//27
    $msg[$i+=1] = $jkk; //JKK//28
    $msg[$i+=1] = ""; //RATE JKK//29
    $msg[$i+=1] = $jkm; //JKM//30
    $msg[$i+=1] = ""; //RATE JKM//31
    $msg[$i+=1] = ""; //ISNEW///32
    $msg[$i+=1] = ""; //STATUS BAYAR//33
    $msg[$i+=1] = $FIELD_BIAYA_REG; //BIAYA REG//34
    $msg[$i+=1] = $kd_cabang; //KODE_KANTOR_CABANG//35 ---
    $msg[$i+=1] = ""; //ALAMAT_KANTOR_CABANG//36
    $msg[$i+=1] = $kd_kab; //KODE_KABUPATEN//37
    $msg[$i+=1] = $kd_prov; //KODE_PROVINSI//38 ---
    $msg[$i+=1] = $program; //PROGRAM//39 ---
    $msg[$i+=1] = $periode; //PERIODE//40 ---
    $msg[$i+=1] = $status_hitung; //STATUS_HITUNG_IURAN//41 ---
    $msg[$i+=1] = ""; //BLNJKK//42
    $msg[$i+=1] = ""; //BLNJKM//43
    $msg[$i+=1] = ""; //BLNJHT//44
    $msg[$i+=1] = ""; //KET1//45
    $msg[$i+=1] = ""; //KET2//46
    $msg[$i+=1] = ""; //KET3//47

    $fm = convertFM($msg, "*");
    if (strpos($fm, '=') !== false) { 
        $fm = urlencode($fm);
    }
    // echo $fm;die();
    return $fm;

}

function addDataBpjstkInq($frm, $catatan, $cmd){
    // print_r($frm);die();
    if($cmd == 'INQ_BPJS'){
        $jht = explode(':',$frm->getJht());
        $jkk = explode(':',$frm->getJkk());
        $jkm = explode(':',$frm->getJkm());
        $tgl = explode('#',$frm->getKet2());

        $array = array(
            'TANGGAL_LAHIR' => $frm->getTanggal_Lahir(),
            'KODE_BAYAR' => $frm->getKode_Bayar(),
            'PEKERJAAN' => $frm->getPekerjaan(),
            'JAM_AWAL' => $frm->getJam_Awal(),
            'JAM_AKHIR' => $frm->getJam_Akhir(),
            'ALAMAT' => $frm->getAlamat(),
            'LOKASI_KERJA' => $frm->getLokasi_Kerja(),
            'UPAH' => $frm->getUpah(),
            'JHT' => $jht[0],
            'JKK' => $jkk[0],
            'JKM' => $jkm[0],
            'STATUS_TAGIHAN' => $frm->getIsnew(),
            'KODE_KANTOR_CABANG' => $frm->getKode_Kantor_Cabang(),
            'ALAMAT_KANTOR_CABANG' => $frm->getAlamat_Kantor_Cabang(),
            'PROGRAM' => $frm->getKet1(),
            'TGL_EFEKTIF' => $tgl[0],
            'TGL_BERAKHIR' => $tgl[1],
            'CATATAN' => $catatan
        );
    } else {
        $jht = $frm->getJht();
        $jkk = $frm->getJkk();
        $jkm = $frm->getJkm();
        $datalain = explode('#',$frm->getData2());
        $jpk = $datalain[0];
        $jpn = $datalain[1];
        $npp = $datalain[2];
        $kode_iuran = $frm->getNik().' / '.$frm->getKet1();
        $array = array(
            'KODE_IURAN' => $kode_iuran,
            'JHT' => $jht,
            'JKK' => $jkk,
            'JKM' => $jkm,
            'JPK' => $jpk,
            'JPN' => $jpn,
            'NPP' => $npp,
        );
    }
    

    return $array;
}

function addDataBpjstkPay($frm, $cmd){
    // print_r($frm);die();
    if($cmd == "PAY_BPJS"){
        $jht = explode('=',$frm->getJht());
        $jkk = explode('=',$frm->getJkk());
        $jkm = explode('=',$frm->getJkm());
        $tgl = explode('#',$frm->getKet2());

        $array = array(
            'TANGGAL_LAHIR' => $frm->getTanggal_Lahir(),
            'KODE_IURAN' => $frm->getData2(),
            'PEKERJAAN' => $frm->getPekerjaan(),
            'JAM_AWAL' => $frm->getJam_Awal(),
            'JAM_AKHIR' => $frm->getJam_Akhir(),
            'ALAMAT' => $frm->getAlamat(),
            'LOKASI_KERJA' => $frm->getLokasi_Kerja(),
            'UPAH' => $frm->getUpah(),
            'JHT' => (string) ((int)$jht[1]),
            'JKK' => (string) ((int)$jkk[1]),
            'JKM' => (string) ((int)$jkm[1]),
            'KODE_KANTOR_CABANG' => $frm->getKode_Kantor_Cabang(),
            'ALAMAT_KANTOR_CABANG' => $frm->getAlamat_Kantor_Cabang(),
            'PROGRAM' => getProgram($frm->getKet2()),
            'TGL_EFEKTIF' => getTgl($frm->getKet1(), "JKK_AKTIF="),
            'TGL_BERAKHIR' => getTgl($frm->getKet1(), "JKK_EXPIRED="),
        );
    } else {
        // print_r($frm);die();
        $jht = $frm->getJht();
        $jkk = $frm->getJkk();
        $jkm = $frm->getJkm();
        $datalain = explode('#',$frm->getData2());
        $jpk = $datalain[0];
        $jpn = $datalain[1];
        $npp = $datalain[2];
        $kode_iuran = $frm->getNik().' / '.$frm->getKet1();
        $array = array(
            'KODE_IURAN' => $kode_iuran,
            'JHT' => $jht,
            'JKK' => $jkk,
            'JKM' => $jkm,
            'JPK' => $jpk,
            'JPN' => $jpn,
            'NPP' => $npp,
        );
    }
    

    return $array;
}

function getProgram($s){
    $result = preg_replace("/[^a-zA-Z]+/", "", $s);
    $output = str_split($result, 3);
    return implode(',',$output);
}

function getTgl($s, $string){
    $result = explode($string, $s);
    // $output = explode(' ', $result[1]);
    // return $output[0];
    return substr($result[1], 0, 19);
}

function allRequired($data){
    $is_empty = false;
    foreach($data as $request){
        if($request == ""){
            $is_empty = true;
            break;
        }
    }
    return $is_empty;
    
}

//=========function bpjstk end==============//
?>
