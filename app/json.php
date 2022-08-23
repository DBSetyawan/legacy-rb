<?php

error_reporting(0);
header('Content-Type: application/json');
if($_GET['devel'] == 2){
    ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
}
// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
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
require_once("include/format_message/FormatLakupandai.class.php");
// require_once("include/format_message/FormatKai.class.php");
//koneksi ke database postgre
// global $pgsql;
// $pgsql      = pg_connect("host=" . $__CFG_dbhost . " port=" . $__CFG_dbport . " dbname=" . $__CFG_dbname . " user=" . $__CFG_dbuser . " password=" . $__CFG_dbpass);

$msg        = $HTTP_RAW_POST_DATA;
$host       = getClientIP();//$_SERVER['REMOTE_ADDR'];
// die('jancok');

$mid = getNextMID();

// $mid        = rand(1,99999999999); //buat local
$step       = 1;
$raw_msg    = file_get_contents('php://input');
$raw        = json_decode($raw_msg);

$raw_cpy             = $raw_msg;
$raw_cpy_decode      = json_decode($raw_cpy);
$raw_cpy_decode->pin = '------';
$msg_log             = json_encode($raw_cpy_decode);
$sender              = "XML CLIENT";
//$receiver            = $GLOBALS["__G_module_name"];
$receiver           = $_SERVER['SERVER_ADDR']."-RB-JSON-".$_SERVER['HTTP_HOST']."-".$_SERVER['SERVER_NAME'];

$via                 = $GLOBALS["__G_via"];
writeLog($mid, $step, $host, $receiver, $msg_log, $via);

$ips = getClientIP();
// appendfile(date('Y:m:d H:i:s').' Request json'.$ips.'= '. $msg_log);
// appendfilehit(date('Y:m:d H:i:s').'-'.$receiver.' Request Json '.$ips.'= '. $msg_log, 'Json');

$pin = strtolower($raw->pin);
$method = $raw->method;
if( $method != "" ){
    $wpin = array('rajabiller.cekip');
    if(!in_array($method,$wpin)){
        if(($pin == '' || strlen($pin) != 6 || !is_numeric($pin))){
            echo'Pin tidak valid';
            die();
        }
    }
}
switch ($method) {

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
    case "rajabiller.transferinq":
        echo transferinq($raw);
        break;
    case "rajabiller.transferpay":
        echo transferpay($raw);
        break;
    case "rajabiller.harga":
        echo harga($raw);
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
    case "rajabiller.pulsa2":
        echo pulsa2($raw);
        break;
    case "rajabiller.game2":
        echo game2($raw);
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
    case "rajabiller.cekharga_gp":
        echo cek_harga2($raw);
        break;
    // case "rajabiller.pay_bintang":
    //     echo pay_bintang($raw);
    //     break;
    case "rajabiller.cekip":
        echo cek_ip($raw);
        break;
    case "rajabiller.info_produk":
        echo info_produk($raw);
        break;
    case "rajabiller.group_produk":
        echo group_produk($raw);
        break;
    case "rajabiller.datatransaksi":
        echo data_transaksi($raw);
        break;
    case "rajabiller.daftar":
        echo daftar($raw);
        break;
    case "rajabiller.beli":
        echo beli($raw);
        break;
    case "rajabiller.inqpln":
        echo inqpln($raw);
        break;
    case "rajabiller.paypln":
        echo paypln($raw);
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
    case "rajabiller.inqpln2":
        echo inqpln2($raw);
        break;
    case "rajabiller.paypln2":
        echo paypln2($raw);
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
   case "rajabiller.transaksi":
       echo transaksi($raw);
       break;

    default :
        echo json_encode(array('Produk tidak dikenal'));
}

/*function kaiinq($data){
    $mti        = "NKAIBOKINF";
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $kdproduk   = 'PKAI';
    $code       = strtoupper($data->payment_code);

    $field      = 4;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    // if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }
    
    global $pgsql;
    global $host;
    global $via;

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = $mti;
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $code;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = ""; //token
    $msg[$i+=1] = ""; //banlce
    $msg[$i+=1] = ""; //jenisstruk
    $msg[$i+=1] = ""; //kodebang
    $msg[$i+=1] = ""; //kdproduk biler
    $msg[$i+=1] = ""; //trxid
    $msg[$i+=1] = ""; //status
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME']; //keterangan

    $msg[$i+=1] = ""; // FIELD_ERR_CODE
    $msg[$i+=1] = ""; // FIELD_ERR_MSG
    $msg[$i+=1] = ""; // FIELD_ORG
    $msg[$i+=1] = ""; // FIELD_DES
    $msg[$i+=1] = ""; // FIELD_DEP_DATE
    $msg[$i+=1] = ""; // FIELD_ARV_DATE
    $msg[$i+=1] = ""; // FIELD_SCHEDULE
    $msg[$i+=1] = ""; // FIELD_TRAIN_NO
    $msg[$i+=1] = ""; // FIELD_CLASS
    $msg[$i+=1] = ""; // FIELD_SUBCLASS
    $msg[$i+=1] = ""; // FIELD_NUM_PAX_ADULT
    $msg[$i+=1] = ""; // FIELD_NUM_PAX_CHILD
    $msg[$i+=1] = ""; // FIELD_NUM_PAX_INFANT
    $msg[$i+=1] = ""; // FIELD_ADULT_NAME1
    $msg[$i+=1] = ""; // FIELD_ADULT_BIRTHDATE1
    $msg[$i+=1] = ""; // FIELD_ADULT_MOBILE1
    $msg[$i+=1] = ""; // FIELD_ADULT_ID_NO1
    $msg[$i+=1] = ""; // FIELD_ADULT_NAME2
    $msg[$i+=1] = ""; // FIELD_ADULT_BIRTHDATE2
    $msg[$i+=1] = ""; // FIELD_ADULT_MOBILE2
    $msg[$i+=1] = ""; // FIELD_ADULT_ID_NO2
    $msg[$i+=1] = ""; // FIELD_ADULT_NAME3
    $msg[$i+=1] = ""; // FIELD_ADULT_BIRTHDATE3
    $msg[$i+=1] = ""; // FIELD_ADULT_MOBILE3
    $msg[$i+=1] = ""; // FIELD_ADULT_ID_NO3
    $msg[$i+=1] = ""; // FIELD_ADULT_NAME4
    $msg[$i+=1] = ""; // FIELD_ADULT_BIRTHDATE4
    $msg[$i+=1] = ""; // FIELD_ADULT_MOBILE4
    $msg[$i+=1] = ""; // FIELD_ADULT_ID_NO4
    $msg[$i+=1] = ""; // FIELD_CHILD_NAME1
    $msg[$i+=1] = ""; // FIELD_CHILD_BIRTHDATE1
    $msg[$i+=1] = ""; // FIELD_CHILD_NAME2
    $msg[$i+=1] = ""; // FIELD_CHILD_BIRTHDATE2
    $msg[$i+=1] = ""; // FIELD_CHILD_NAME3
    $msg[$i+=1] = ""; // FIELD_CHILD_BIRTHDATE3
    $msg[$i+=1] = ""; // FIELD_CHILD_NAME4
    $msg[$i+=1] = ""; // FIELD_CHILD_BIRTHDATE4
    $msg[$i+=1] = ""; // FIELD_INFANT_NAME1
    $msg[$i+=1] = ""; // FIELD_INFANT_BIRTHDATE1
    $msg[$i+=1] = ""; // FIELD_INFANT_NAME2
    $msg[$i+=1] = ""; // FIELD_INFANT_BIRTHDATE2
    $msg[$i+=1] = ""; // FIELD_INFANT_NAME3
    $msg[$i+=1] = ""; // FIELD_INFANT_BIRTHDATE3
    $msg[$i+=1] = ""; // FIELD_INFANT_NAME4
    $msg[$i+=1] = ""; // FIELD_INFANT_BIRTHDATE4
    $msg[$i+=1] = ""; // FIELD_CALLER
    $msg[$i+=1] = ""; // FIELD_NUM_CODE
    $msg[$i+=1] = ""; // FIELD_BOOK_CODE
    $msg[$i+=1] = ""; // FIELD_SEAT
    $msg[$i+=1] = ""; // FIELD_NORMAL_SALES
    $msg[$i+=1] = ""; // FIELD_EXTRA_FEE
    $msg[$i+=1] = ""; // FIELD_BOOK_BALANCE
    $msg[$i+=1] = ""; // FIELD_SEAT_MAP_NULL
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE
    $msg[$i+=1] = ""; // FIELD_WAGON_NO
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE1
    $msg[$i+=1] = ""; // FIELD_WAGON_NO1
    $msg[$i+=1] = ""; // FIELD_SEAT_ROW1
    $msg[$i+=1] = ""; // FIELD_SEAT_COL1
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE2
    $msg[$i+=1] = ""; // FIELD_WAGON_NO2
    $msg[$i+=1] = ""; // FIELD_SEAT_ROW2
    $msg[$i+=1] = ""; // FIELD_SEAT_COL2
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE3
    $msg[$i+=1] = ""; // FIELD_WAGON_NO3
    $msg[$i+=1] = ""; // FIELD_SEAT_ROW3
    $msg[$i+=1] = ""; // FIELD_SEAT_COL3
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE4
    $msg[$i+=1] = ""; // FIELD_WAGON_NO4
    $msg[$i+=1] = ""; // FIELD_SEAT_ROW4
    $msg[$i+=1] = ""; // FIELD_SEAT_COL4
    $msg[$i+=1] = ""; // FIELD_CANCEL_REASON
    $msg[$i+=1] = ""; // FIELD_STATUS_CANCEL
    $msg[$i+=1] = ""; // FIELD_REFUND
    $msg[$i+=1] = ""; // FIELD_PAY_TYPE
    $msg[$i+=1] = ""; // FIELD_ROUTE
    $msg[$i+=1] = ""; // FIELD_PAX
    $msg[$i+=1] = ""; // FIELD_PAX_NUM
    $msg[$i+=1] = ""; // FIELD_REVENUE
    $msg[$i+=1] = ""; // FIELD_TRAIN_NAME
    $msg[$i+=1] = ""; // FIELD_ORIGINATION
    $msg[$i+=1] = ""; // FIELD_DEP_TIME
    $msg[$i+=1] = ""; // FIELD_DESTINATION
    $msg[$i+=1] = ""; // FIELD_ARV_TIME
    $msg[$i+=1] = ""; // FIELD_SEAT_NUMBER
    $msg[$i+=1] = ""; // FIELD_PRICE_ADULT
    $msg[$i+=1] = ""; // FIELD_PRICE_CHILD
    $msg[$i+=1] = ""; // FIELD_PRICE_INFANT

    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm;die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    // echo $msg."<br>";
    // die();
    $list = $GLOBALS["sndr"];

    if(substr(strtoupper($idoutlet), 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA32670');
        if(!in_array(strtoupper($idoutlet), $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$uid."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        $respon = postValue($fm);
        $resp = $respon[7];
    }

    if($resp == '' || $resp == 'null'){
        $params = array(
            'status' => '77',
            'keterangan' => 'Transaksi gagal, silahkan coba beberapa saat lagi',
            'jenis' => 'inquiry',
            'result' => array()
        );
        header('Content-Type: application/json');
        return json_encode($params, JSON_PRETTY_PRINT);
    }
    // $resp = "NKAIBOKINF*PKAI*4161686264*8*20200623035842*DESKTOP**G733RZM*1216518814333*70000**$idoutlet*------*------*1272731*0***1832403994*00*Success***PSE*GB*20200623*20200623**306NN***1*0*0*AGUNG TRIANDORO***3171030907770002******************************1216518814333*G733RZM**70000**70000******6*D****************TUNAI**UMUM*1**BENGAWAN*PASARSENEN*0630*GOMBONG*1250*EKO-3/6D*70000*0*";
    // $resp = "NKAIBOKINF*PKAI*4162765364*8*20200623174256*DESKTOP**G2E3E7G*1212523787231*280000**$idoutlet*------*------*675984*0***1833025898*00*Success***PWT*BKS*20200625*20200625**305NN***4*0*0*BASIMIN***3172021912550004*RUBIYANTI***3172026303510004*MUHAMMAD DAFY ALFA J***3172021505080011*MUHAMMAD DARYL IBNI S***3172022405121002******************1212523787231*G2E3E7G**280000**280000******6*D****************TUNAI**UMUM*4**BENGAWAN*PURWOKERTO*0056*BEKASI*0556*EKO-8/7E,EKO-8/6E,EKO-8/5E,EKO-8/6D*280000*0*";

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    $frm = getParseProduk($kdproduk, $resp);

    // print_r($frm);die();
    
    if($frm->getStatus() == '00'){
        $jmbrgkt = substr($frm->getDEPTIME(), 0, 2);
        $mntbrgkt = substr($frm->getDEPTIME(), 2, 2);
        $jmtiba = substr($frm->getARVTIME(), 0, 2);
        $mnttiba = substr($frm->getARVTIME(), 2);

        $jam_berangkat = $jmbrgkt . ":" . $mntbrgkt;
        $jam_tiba = $jmtiba . ":" . $mnttiba;
        $total_biaya = $frm->getNominal();

        $list_dewasa = array();
        $list_anak = array();
        $list_balita = array();
        if($frm->getADULTNAME1() != ''){
            $list_dewasa[] = array(
                'nama' => $frm->getADULTNAME1(),
                'no_identitas' => $frm->getADULTIDNO1()
            );
        }
        if($frm->getADULTNAME2() != ''){
            $list_dewasa[] = array(
                'nama' => $frm->getADULTNAME2(),
                'no_identitas' => $frm->getADULTIDNO2(),
            );
        }
        if($frm->getADULTNAME3() != ''){
            $list_dewasa[] = array(
                'nama' => $frm->getADULTNAME3(),
                'no_identitas' => $frm->getADULTIDNO3(),
            );
        }
        if($frm->getADULTNAME4() != ''){
            $list_dewasa[] = array(
                'nama' => $frm->getADULTNAME4(),
                'no_identitas' => $frm->getADULTIDNO4(),
            );
        }

        if($frm->getCHILDNAME1() != ''){
            $list_anak[] = array(
                'nama' => $frm->getCHILDNAME1(),
                'no_identitas' => $frm->getCHILDBIRTHDATE1(),
            );
        }
        if($frm->getCHILDNAME2() != ''){
            $list_anak[] = array(
                'nama' => $frm->getCHILDNAME2(),
                'no_identitas' => $frm->getCHILDBIRTHDATE2(),
            );
        }
        if($frm->getCHILDNAME3() != ''){
            $list_anak[] = array(
                'nama' => $frm->getCHILDNAME3(),
                'no_identitas' => $frm->getCHILDBIRTHDATE3(),
            );
        }
        if($frm->getCHILDNAME4() != ''){
            $list_anak[] = array(
                'nama' => $frm->getCHILDNAME4(),
                'no_identitas' => $frm->getCHILDBIRTHDATE4(),
            );
        }

        if($frm->getINFANTNAME1() != ''){
            $list_balita[] = array(
                'nama' => $frm->getINFANTNAME1(),
                'no_identitas' => $frm->getINFANTBIRTHDATE1(),
            );
        }
        if($frm->getINFANTNAME2() != ''){
            $list_balita[] = array(
                'nama' => $frm->getINFANTNAME2(),
                'no_identitas' => $frm->getINFANTBIRTHDATE2(),
            );
        }
        if($frm->getINFANTNAME3() != ''){
            $list_balita[] = array(
                'nama' => $frm->getINFANTNAME3(),
                'no_identitas' => $frm->getINFANTBIRTHDATE3(),
            );
        }
        if($frm->getINFANTNAME4() != ''){
            $list_balita[] = array(
                'nama' => $frm->getINFANTNAME4(),
                'no_identitas' => $frm->getINFANTBIRTHDATE4(),
            );
        }

        $resultdata = array(
            'tanggal' => date('d M Y') . " " . date('H:i'),
            'book_code' => $frm->getBOOKCODE(),
            'jml_penumang_dewasa' => $frm->getNUMPAXADULT(),
            'list_penumpang_dewasa' => $list_dewasa,
            'jml_penumang_anak' => $frm->getNUMPAXCHILD(),
            'list_penumpang_anak' => $list_anak,
            'jml_penumang_balita' => $frm->getNUMPAXINFANT(),
            'list_penumpang_balita' => $list_balita,
            'nama_ka' => $frm->getTRAINNAME(),
            'no_ka' => $frm->getTRAINNO(),
            'tgl_keberangkatan' => date('Y-m-d', strtotime($frm->getDEPDATE())) . " " . $jam_berangkat,
            'tgl_kedatangan' => date('Y-m-d', strtotime($frm->getARVDATE())) . " " . $jam_tiba,
            'stasiun_asal' => $frm->getORIGINATION().' ('.$frm->getORG().')',
            'stasiun_tujuan' => $frm->getDESTINATION().' ('.$frm->getDES().')',
            'kelas' => $frm->getCLASS(),
            'kursi' => $frm->getSEATNUMBER(),
            'nominal' => $frm->getNominal(),
            'biaya_admin' => '0',
            'diskon_channel' => '0',
            'total_biaya' => $total_biaya,
        );
    } else {
        $resultdata = array();
    }
    
    $params = array(
        'status' => $frm->getStatus(),
        'keterangan' => $frm->getKeterangan(),
        'jenis' => 'inquiry',
        'result' => $resultdata
    );

    $r_mid          = $frm->getMID();
    $r_step         = $frm->getStep()+1;

    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    header('Content-Type: application/json');
    return json_encode($params, JSON_PRETTY_PRINT);
}

function kaipay($data){
    $mti        = "NKAIGPAY";
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $kdproduk   = 'PKAI';
    $code       = strtoupper($data->payment_code);

    $field      = 4;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        date_default_timezone_set('asia/jakarta');
        $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
        $datein = (int) strtotime(date('Y-m-d H:i:s'));
        $selisih = $datein - $my_ips;
        if($selisih >= 10){
            $params = array(
                'status' => '77',
                'keterangan' => 'Transaksi gagal, silahkan coba beberapa saat lagi',
                'jenis' => 'payment',
                'result' => array()
            );
            header('Content-Type: application/json');
            return json_encode($params, JSON_PRETTY_PRINT);
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }
    
    global $pgsql;
    global $host;
    global $via;

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = $mti;
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $code;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($idoutlet);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = ""; //token
    $msg[$i+=1] = ""; //banlce
    $msg[$i+=1] = ""; //jenisstruk
    $msg[$i+=1] = ""; //kodebang
    $msg[$i+=1] = ""; //kdproduk biler
    $msg[$i+=1] = ""; //trxid
    $msg[$i+=1] = ""; //status
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME']; //keterangan

    $msg[$i+=1] = ""; // FIELD_ERR_CODE
    $msg[$i+=1] = ""; // FIELD_ERR_MSG
    $msg[$i+=1] = ""; // FIELD_ORG
    $msg[$i+=1] = ""; // FIELD_DES
    $msg[$i+=1] = ""; // FIELD_DEP_DATE
    $msg[$i+=1] = ""; // FIELD_ARV_DATE
    $msg[$i+=1] = ""; // FIELD_SCHEDULE
    $msg[$i+=1] = ""; // FIELD_TRAIN_NO
    $msg[$i+=1] = ""; // FIELD_CLASS
    $msg[$i+=1] = ""; // FIELD_SUBCLASS
    $msg[$i+=1] = ""; // FIELD_NUM_PAX_ADULT
    $msg[$i+=1] = ""; // FIELD_NUM_PAX_CHILD
    $msg[$i+=1] = ""; // FIELD_NUM_PAX_INFANT
    $msg[$i+=1] = ""; // FIELD_ADULT_NAME1
    $msg[$i+=1] = ""; // FIELD_ADULT_BIRTHDATE1
    $msg[$i+=1] = ""; // FIELD_ADULT_MOBILE1
    $msg[$i+=1] = ""; // FIELD_ADULT_ID_NO1
    $msg[$i+=1] = ""; // FIELD_ADULT_NAME2
    $msg[$i+=1] = ""; // FIELD_ADULT_BIRTHDATE2
    $msg[$i+=1] = ""; // FIELD_ADULT_MOBILE2
    $msg[$i+=1] = ""; // FIELD_ADULT_ID_NO2
    $msg[$i+=1] = ""; // FIELD_ADULT_NAME3
    $msg[$i+=1] = ""; // FIELD_ADULT_BIRTHDATE3
    $msg[$i+=1] = ""; // FIELD_ADULT_MOBILE3
    $msg[$i+=1] = ""; // FIELD_ADULT_ID_NO3
    $msg[$i+=1] = ""; // FIELD_ADULT_NAME4
    $msg[$i+=1] = ""; // FIELD_ADULT_BIRTHDATE4
    $msg[$i+=1] = ""; // FIELD_ADULT_MOBILE4
    $msg[$i+=1] = ""; // FIELD_ADULT_ID_NO4
    $msg[$i+=1] = ""; // FIELD_CHILD_NAME1
    $msg[$i+=1] = ""; // FIELD_CHILD_BIRTHDATE1
    $msg[$i+=1] = ""; // FIELD_CHILD_NAME2
    $msg[$i+=1] = ""; // FIELD_CHILD_BIRTHDATE2
    $msg[$i+=1] = ""; // FIELD_CHILD_NAME3
    $msg[$i+=1] = ""; // FIELD_CHILD_BIRTHDATE3
    $msg[$i+=1] = ""; // FIELD_CHILD_NAME4
    $msg[$i+=1] = ""; // FIELD_CHILD_BIRTHDATE4
    $msg[$i+=1] = ""; // FIELD_INFANT_NAME1
    $msg[$i+=1] = ""; // FIELD_INFANT_BIRTHDATE1
    $msg[$i+=1] = ""; // FIELD_INFANT_NAME2
    $msg[$i+=1] = ""; // FIELD_INFANT_BIRTHDATE2
    $msg[$i+=1] = ""; // FIELD_INFANT_NAME3
    $msg[$i+=1] = ""; // FIELD_INFANT_BIRTHDATE3
    $msg[$i+=1] = ""; // FIELD_INFANT_NAME4
    $msg[$i+=1] = ""; // FIELD_INFANT_BIRTHDATE4
    $msg[$i+=1] = ""; // FIELD_CALLER
    $msg[$i+=1] = ""; // FIELD_NUM_CODE
    $msg[$i+=1] = ""; // FIELD_BOOK_CODE
    $msg[$i+=1] = ""; // FIELD_SEAT
    $msg[$i+=1] = ""; // FIELD_NORMAL_SALES
    $msg[$i+=1] = ""; // FIELD_EXTRA_FEE
    $msg[$i+=1] = ""; // FIELD_BOOK_BALANCE
    $msg[$i+=1] = ""; // FIELD_SEAT_MAP_NULL
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE
    $msg[$i+=1] = ""; // FIELD_WAGON_NO
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE1
    $msg[$i+=1] = ""; // FIELD_WAGON_NO1
    $msg[$i+=1] = ""; // FIELD_SEAT_ROW1
    $msg[$i+=1] = ""; // FIELD_SEAT_COL1
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE2
    $msg[$i+=1] = ""; // FIELD_WAGON_NO2
    $msg[$i+=1] = ""; // FIELD_SEAT_ROW2
    $msg[$i+=1] = ""; // FIELD_SEAT_COL2
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE3
    $msg[$i+=1] = ""; // FIELD_WAGON_NO3
    $msg[$i+=1] = ""; // FIELD_SEAT_ROW3
    $msg[$i+=1] = ""; // FIELD_SEAT_COL3
    $msg[$i+=1] = ""; // FIELD_WAGON_CODE4
    $msg[$i+=1] = ""; // FIELD_WAGON_NO4
    $msg[$i+=1] = ""; // FIELD_SEAT_ROW4
    $msg[$i+=1] = ""; // FIELD_SEAT_COL4
    $msg[$i+=1] = ""; // FIELD_CANCEL_REASON
    $msg[$i+=1] = ""; // FIELD_STATUS_CANCEL
    $msg[$i+=1] = ""; // FIELD_REFUND
    $msg[$i+=1] = "TUNAI"; // FIELD_PAY_TYPE
    $msg[$i+=1] = ""; // FIELD_ROUTE
    $msg[$i+=1] = ""; // FIELD_PAX
    $msg[$i+=1] = ""; // FIELD_PAX_NUM
    $msg[$i+=1] = ""; // FIELD_REVENUE
    $msg[$i+=1] = ""; // FIELD_TRAIN_NAME
    $msg[$i+=1] = ""; // FIELD_ORIGINATION
    $msg[$i+=1] = ""; // FIELD_DEP_TIME
    $msg[$i+=1] = ""; // FIELD_DESTINATION
    $msg[$i+=1] = ""; // FIELD_ARV_TIME
    $msg[$i+=1] = ""; // FIELD_SEAT_NUMBER
    $msg[$i+=1] = ""; // FIELD_PRICE_ADULT
    $msg[$i+=1] = ""; // FIELD_PRICE_CHILD
    $msg[$i+=1] = ""; // FIELD_PRICE_INFANT

    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm;die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    // echo $msg."<br>";
    // die();
    $list = $GLOBALS["sndr"];

    if(substr(strtoupper($idoutlet), 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA32670');
        if(!in_array(strtoupper($idoutlet), $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$uid."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        $respon = postValue($fm);
        $resp = $respon[7];
    }
    // $resp = "NKAIGPAY*PKAI*4162767725*8*20200623174423*DESKTOP**G2E3E7G*1212523787231*280000*0*FT1040*------*------*395984*0***1833027297*00*Success***PWT*BKS*20200625*20200625**305NN***4*0*0*BASIMIN***3172021912550004*RUBIYANTI***3172026303510004*MUHAMMAD DAFY ALFA J***3172021505080011*MUHAMMAD DARYL IBNI S***3172022405121002******************1212523787231*G2E3E7G**280000**280000******6*D****************TUNAI**UMUM*4**BENGAWAN*PURWOKERTO*0056*BEKASI*0556*EKO-8/7E,EKO-8/6E,EKO-8/5E,EKO-8/6D*280000*0*";
    // $resp = '';
    if($resp == '' || $resp == 'null'){
        $params = array(
            'status' => '00',
            'keterangan' => 'SEDANG DIPROSES',
            'jenis' => 'payment',
            'result' => array()
        );
        header('Content-Type: application/json');
        return json_encode($params, JSON_PRETTY_PRINT);
    }

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);

    // print_r($frm);die();
    
    if($frm->getStatus() == '00'){
        $jmbrgkt = substr($frm->getDEPTIME(), 0, 2);
        $mntbrgkt = substr($frm->getDEPTIME(), 2, 2);
        $jmtiba = substr($frm->getARVTIME(), 0, 2);
        $mnttiba = substr($frm->getARVTIME(), 2);

        $jam_berangkat = $jmbrgkt . ":" . $mntbrgkt;
        $jam_tiba = $jmtiba . ":" . $mnttiba;
        $total_biaya = $frm->getNominal();

        $list_dewasa = array();
        $list_anak = array();
        $list_balita = array();
        if($frm->getADULTNAME1() != ''){
            $list_dewasa[] = array(
                'nama' => $frm->getADULTNAME1(),
                'no_identitas' => $frm->getADULTIDNO1()
            );
        }
        if($frm->getADULTNAME2() != ''){
            $list_dewasa[] = array(
                'nama' => $frm->getADULTNAME2(),
                'no_identitas' => $frm->getADULTIDNO2(),
            );
        }
        if($frm->getADULTNAME3() != ''){
            $list_dewasa[] = array(
                'nama' => $frm->getADULTNAME3(),
                'no_identitas' => $frm->getADULTIDNO3(),
            );
        }
        if($frm->getADULTNAME4() != ''){
            $list_dewasa[] = array(
                'nama' => $frm->getADULTNAME4(),
                'no_identitas' => $frm->getADULTIDNO4(),
            );
        }

        if($frm->getCHILDNAME1() != ''){
            $list_anak[] = array(
                'nama' => $frm->getCHILDNAME1(),
                'no_identitas' => $frm->getCHILDBIRTHDATE1(),
            );
        }
        if($frm->getCHILDNAME2() != ''){
            $list_anak[] = array(
                'nama' => $frm->getCHILDNAME2(),
                'no_identitas' => $frm->getCHILDBIRTHDATE2(),
            );
        }
        if($frm->getCHILDNAME3() != ''){
            $list_anak[] = array(
                'nama' => $frm->getCHILDNAME3(),
                'no_identitas' => $frm->getCHILDBIRTHDATE3(),
            );
        }
        if($frm->getCHILDNAME4() != ''){
            $list_anak[] = array(
                'nama' => $frm->getCHILDNAME4(),
                'no_identitas' => $frm->getCHILDBIRTHDATE4(),
            );
        }

        if($frm->getINFANTNAME1() != ''){
            $list_balita[] = array(
                'nama' => $frm->getINFANTNAME1(),
                'no_identitas' => $frm->getINFANTBIRTHDATE1(),
            );
        }
        if($frm->getINFANTNAME2() != ''){
            $list_balita[] = array(
                'nama' => $frm->getINFANTNAME2(),
                'no_identitas' => $frm->getINFANTBIRTHDATE2(),
            );
        }
        if($frm->getINFANTNAME3() != ''){
            $list_balita[] = array(
                'nama' => $frm->getINFANTNAME3(),
                'no_identitas' => $frm->getINFANTBIRTHDATE3(),
            );
        }
        if($frm->getINFANTNAME4() != ''){
            $list_balita[] = array(
                'nama' => $frm->getINFANTNAME4(),
                'no_identitas' => $frm->getINFANTBIRTHDATE4(),
            );
        }

        $resultdata = array(
            'tanggal' => date('d M Y') . " " . date('H:i'),
            'book_code' => $frm->getBOOKCODE(),
            'jml_penumang_dewasa' => $frm->getNUMPAXADULT(),
            'list_penumpang_dewasa' => $list_dewasa,
            'jml_penumang_anak' => $frm->getNUMPAXCHILD(),
            'list_penumpang_anak' => $list_anak,
            'jml_penumang_balita' => $frm->getNUMPAXINFANT(),
            'list_penumpang_balita' => $list_balita,
            'nama_ka' => $frm->getTRAINNAME(),
            'no_ka' => $frm->getTRAINNO(),
            'tgl_keberangkatan' => date('Y-m-d', strtotime($frm->getDEPDATE())) . " " . $jam_berangkat,
            'tgl_kedatangan' => date('Y-m-d', strtotime($frm->getARVDATE())) . " " . $jam_tiba,
            'stasiun_asal' => $frm->getORIGINATION().' ('.$frm->getORG().')',
            'stasiun_tujuan' => $frm->getDESTINATION().' ('.$frm->getDES().')',
            'kelas' => $frm->getCLASS(),
            'kursi' => $frm->getSEATNUMBER(),
            'nominal' => $frm->getNominal(),
            'biaya_admin' => '0',
            'diskon_channel' => '0',
            'total_biaya' => $total_biaya,
        );
    } else {
        $resultdata = array();
    }
    
    $params = array(
        'status' => $frm->getStatus(),
        'keterangan' => $frm->getKeterangan(),
        'jenis' => 'payment',
        'result' => $resultdata
    );

    $r_mid          = $frm->getMID();
    $r_step         = $frm->getStep()+1;

    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    header('Content-Type: application/json');
    return json_encode($params, JSON_PRETTY_PRINT);


}*/
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
    $cek = is_numeric($idtrx);

    $field      = 10;
    if(count((array)$data_request) !== $field){
        return json_encode(array('error'=>'missing parameter request cek status'));
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    // global $pgsql;
    // if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //     return json_encode(array('error'=>'access not allowed'));
    // }

    // if($idtrx != ""){
    //     if($cek == 0){
    //     return json_encode(array('error'=>'ref2 harus value dari ref2 payment'));
    //     }
    // }
    
    if($kdproduk == "" || empty($kdproduk)){
        return json_encode(array('error'=>'kode produk wajib dibawa saat request cek status'));
    }

    if($tgl == "" || empty($tgl)){
        return json_encode(array('error'=>'tgl wajib dibawa saat request cek status'));
    }

    if($idoutlet == "" || empty($idoutlet)){
        return json_encode(array('error'=>'id outlet wajib dibawa saat request cek status'));
    }

    
    if (in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        $normal_idpel = normalisasiIdPel1PLNPra($idpel1, $idpel2);
        $idpel1 = $normal_idpel["idpel1"];
        $idpel2 = $normal_idpel["idpel2"];
    }

    if (in_array($kdproduk, KodeProduk::getPLNPostpaids())) {
        $normal_idpel = normalisasiIdPel1PLNPasc($idpel1);
        $idpel1 = $normal_idpel["idpel1"];
    }
    
    if ($ref1 != "" || $idtrx != "" || $idpel1 != "" || $idpel2 != "" || $denom != "") {
        $data = getStatusProsesTransaksi($tgl, $kdproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);       
        $cnt = count($data);
        if ($cnt > 0) {
        
            $kdproduk = (string) trim($data["id_produk"]);
            $sn = (string) trim($data["bill_info5"]);
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

            $data = getStatusTransaksi($tgl, $kdproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);
            
            $cnt = count($data);
            if ($cnt > 0) {
             
                $kdproduk = (string) trim($data["id_produk"]);
                $sn = (string) trim($data["bill_info5"]);
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
                $data = getStatusTransaksiBackup($tgl, $kdproduk, $idoutlet, $ref1, $idtrx, $idpel1, $idpel2, $denom);
                
                $cnt = count($data);
                if ($cnt > 0) {
                  
                    $kdproduk = (string) trim($data["id_produk"]);
                    $sn = (string) trim($data["bill_info5"]);
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
                    $status = "00";
                    $ket = "SEDANG DIPROSES";
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

        $status = "100";
        $ket = "Data Request yang mandatory masih ada yang belum terisi";
        $add_data = array(
            "IDTRANSAKSI" => (string) "",
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

    $request = array(
        "METHOD" => 'rajabiller.cekstatus',
        "TANGGAL" => (String) $tgl, 
        "REF1" => (String) $ref1, 
        "REF2" => (String) $idtrx, 
        "KODEPRODUK" => (String) $kdproduk, 
        "IDPEL1" => (String) $idpel1, 
        "IDPEL2" => (String) $idpel2, 
        "DENOM" => (String) $denom,
        "UID" => (String) $idoutlet,
        "PIN" => (String) "------"
    );
    $params = array_change_key_case($params, CASE_LOWER);
    $log = array("request"=> $request , "response" => $params);

    writeLog('1', '1', $idoutlet, $_SERVER['SERVER_NAME'].'|'.$ip, json_encode($log), 'H2H');

    return json_encode($params);
}


function inqpln2($data){

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

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
    $fm = convertFM($msg, "*");

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA" && substr(strtoupper($kdproduk), 0,7) != "PLNNONH"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra dan plnnon ";
    } else {
        
        $respon = postValue($fm);
        $resp = $respon[7];
        
    }
   
    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $params     = setMandatoryResponNewArranet($frm, $ref1, "", "", $data);
    
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk_arranet($kdproduk, $frm);
    
    $adddata2   = tambahdataproduk2_arranet($kdproduk, $frm);
     if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
       
        $adddata3   = tambahdataproduk3($kdproduk, $frm);
    }else{
        $adddata3 = array();
    }   
    $merge      = array_merge($params,$adddata,$adddata2,$adddata3);
    
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

     // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
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
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) $ref2,
                    "REF3"              => (string) $ref3,
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }
    
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA" && substr(strtoupper($kdproduk), 0,7) != "PLNNONH"){
        $resp = "BAYAR*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra dan plnnonh";
    }elseif(substr(strtoupper($kdproduk), 0,6) != "PLNPRA"){
        
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    }else{
        $respon = postValue($fm);
        $resp = $respon[7];
    } 

    if($resp == 'null'){
        if(substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){
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
                "REFNUM"               => (string) '',
                "MATERAI"           => (string) '',
                "PPN"               => (string) '',
                "PPJ"               => (string) '',
                "ANGSURAN"          => (string) '',
                "RPTOKEN"           => (string) '',
                "KWH"               => (string) '',
                "NOMORTOKEN"             => (string) '',
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
                "kode_produk"       => (string) $kdproduk,
                "waktu"             => (string) date('YmdHis'),
                "idpel1"            => (string) $idpel1,
                "idpel2"            => (string) $idpel2,
                "idpel3"            => (string) $idpel3,
                "nama_pelanggan"    => (string) '',
                "periode"           => (string) '',
                "jml_bulan"         => (string) '',
                "nominal"           => (string) '',
                "tarif"             => (string) '',
                "daya"              => (string) '',
                "refnum"               => (string) '',
                "stanawal"          => (string) '',
                "stanakhir"         => (string) '',
                "infoteks"          => (string) '',
                "admin"             => (string) '',
                "uid"               => (string) $idoutlet,
                "pin"               => (string) '------',
                "ref1"              => (string) $ref1,
                "ref2"              => (string) '',
                "ref3"              => "0",
                "status"            => (string) '00',
                "ket"               => (string) 'SEDANG DIPROSES',
                "saldo_terpotong"   => (string) '',
                "sisa_saldo"        => (string) '',
                "url_struk"         => (string) ''
            );
        }
        
        return json_encode($params, JSON_PRETTY_PRINT);
    }
    
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);

    $params     = setMandatoryResponNewArranet($frm, $ref1, "", "", $data);
    
    $frm        = getParseProduk($kdproduk, $resp);
    $adddata    = tambahdataproduk_arranet($kdproduk, $frm, 1);
    $adddata2   = tambahdataproduk2_arranet($kdproduk, $frm);
     if(substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
        $adddata3   = tambahdataproduk3($kdproduk, $frm);
    }else{
        $adddata3 = array();
    }   
    

    if ($frm->getStatus() == "00") {
        $url        = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        if(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $url_struk  = array("URL_STRUK" => "http://34.101.201.189/strukmitra/?id=" . $url);
        }else{
            $url_struk  = array("url_struk" => "http://34.101.201.189/strukmitra/?id=" . $url);
        }
        $merge      = array_merge($params,$adddata,$adddata2,$adddata3,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2,$adddata3);
    }

    return json_encode($merge);
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

    if($uid != 'SP300203'){
        if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
            if (!isValidIP($uid, $ip)) {
                return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
            }
        }
    }

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
    // echo "fm = ".$fm."<br>";die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    // echo $msg."<br>";
    // die();
    $list = $GLOBALS["sndr"];
    if(substr(strtoupper($kdproduk), 0,2) != "KK"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $uid . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk kartu kredit";
    } else {
         $id_biller = getBiller($kdproduk);
         if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
           if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                $resp = $respon[7]; 
            }else{
                $respon = postValue($fm);
                $resp = $respon[7]; 
            }   
         }elseif($id_biller ==  281){
            $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
            $resp = $respon[7]; 
         }else{
            $respon = postValue($fm);
            $resp = $respon[7];
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
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
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) $ref2,
                    "REF3"              => (string) "",
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3:";
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }
    
    // global $pgsql;
    global $host;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";

    if($ref2 == ""){
        $ref2 = 0;
    }
    $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan

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
    
    if(substr(strtoupper($kdproduk), 0,2) != "KK"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk kartu kredit";
    } else {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        }else{
           
             $id_biller = getBiller($kdproduk);
             if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
               if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                    $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                    $resp = $respon[7]; 
                }else{
                    $respon = postValue($fm);
                    $resp = $respon[7]; 
                }   
             }elseif($id_biller ==  281){
                $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                $resp = $respon[7]; 
             }else{
                $respon = postValue($fm);
                $resp = $respon[7];
            }
            
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
        $url_struk  = array("url_struk" => "http://34.101.201.189/strukmitra/?id=" . $url);
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

    if($uid != 'SP300203'){
        if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
            if (!isValidIP($uid, $ip)) {
                return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
            }
        }
    }

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
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $uid . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk pdam";
    } else { 
        $respon = postValuepdam($fm); // to /FMSSWeb4/mpin1
        $resp = $respon[7]; 
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
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
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) $ref2,
                    "REF3"              => (string) $ref3,
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }
    
    // global $pgsql;
    global $host;

    $mti = "BAYAR";
    
    if($ref2 == ""){
        $ref2 = 0;
    }
    $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan

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
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        }else{
            $respon = postValuepdam($fm); // to /FMSSWeb4/mpin1
            $resp = $respon[7];  
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
        $url_struk  = array("url_struk" => "http://34.101.201.189/strukmitra/?id=" . $url);
        $merge      = array_merge($params,$adddata,$adddata2,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2);
    }

    return json_encode($merge);
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
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
                        "STATUS"            => (string) '77',
                        "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
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
                        "STATUS"            => (string) '77',
                        "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                        "SALDO_TERPOTONG"   => (string) '',
                        "SISA_SALDO"        => (string) '',
                        "URL_STRUK"         => (string) ''
                    );
                }
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }
    
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA"){
        $resp ="TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra ";
    }else if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
            $respon = postValue($fm);
            $resp = $respon[7];
       

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
    $r_mid          = $frm->getMID();
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

    if($r_idtrx != $ref2){
        // $text = pay_resp_text($params);
        // $get_mid = get_mid_from_idtrx($r_idtrx);
        // $get_step = get_step_from_mid($r_mid) + 1;
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), "H2H");
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



    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
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
    $r_mid          = $frm->getMID();
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

    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $GLOBALS["via"]);

    return json_encode($params, JSON_PRETTY_PRINT);
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

function transaksi($data)
{
    $i = -1;
    $kdproduk   = trim(strtoupper($data->kode_produk));
    $idoutlet   = trim(strtoupper($data->uid));
    $pin        = trim(strtoupper($data->pin));
    $idpel1     = trim(strtoupper($data->idpel));
    $ref1       = trim(strtoupper($data->ref1));
    $add        = trim(strtoupper($data->add));
    $idpel2     = "";


    if(substr(strtoupper($kdproduk), 0,3) == "CEK"){ // CEK TRANSAKSI
        $kdproduk = substr($kdproduk,3); // ID PRODUK
        if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS"){
            if(substr($idpel1, 0, 5) != "88888"){
                $idpel1 = "88888".substr($idpel1, 2, 11);
            }
            $periodereq = trim(strtoupper($add));
            $field      = 7;
        } else if(substr(strtoupper($kdproduk), 0,2) == "KK"){
            $nominal    = trim(strtoupper($add));
            $field      = 7;
        }   else if(substr(strtoupper($kdproduk), 0,2) == "EM"){
            $nominal    = trim(strtoupper($add));
            $field      = 7;
        }else if(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
            $tahun      = trim(strtoupper($add));
            $field      = 7;
        } else {
            $field      = 7;
        }
        // print_r($req);die();
        if(count((array)$data) !== $field){
            return 'error=missing parameter request';
        }

        if(substr($kdproduk, 0, 5) == 'PAJAK'){
            if(strlen($idpel1) != 18){
                 return "Nomor Object Pajak(NOP) harus 18 digit";
            }
        }
        $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

        if($idoutlet != 'FA9919'){
            if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
                if (!isValidIP($idoutlet, $ip)) {
                    return "IP Anda [$ip] tidak punya hak akses";
                }
            }
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
             if(substr($kdproduk, 0, 7) == 'PLNPRAD'){
                $nominal = getnominal_plnprad($kdproduk);
            }else{
                if(strlen($idpel1) == '12'){
                    $idpel2 = $idpel1;
                    $idpel1 = "";
                }
            }
        }

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
        $msg[$i+=1] = substr($kdproduk, 0,2) == "KK" || substr(strtoupper($kdproduk), 0,5) == "BLTRF" ? "" : $nominal;
        $msg[$i+=1] = substr($kdproduk, 0,2) == "EM" || substr(strtoupper($kdproduk), 0,6) == "PLNPRAD" ? $nominal : "";
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
        $msg[$i+=1] = strtoupper("IRS ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];

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
                $msg[$i+=1] = ""; // $nomorhp
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = ""; // $kodebank
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
            $msg[$i+=1] = $tahun != "" ? $tahun : date("Y");//FIELD_TAHUN_PAJAK
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

        $fm = convertFM($msg, "*");
        $sender = $GLOBALS["__G_module_name"];
        $receiver = $GLOBALS["__G_receiver"];
        $hidepin = explode($pin, $fm);
        $store_fm = $hidepin[0].'......'.$hidepin[1];
        $list = $GLOBALS["sndr"];
        
        $respon = postValue($fm);
        $resp = $respon[7];
           
        $man        = FormatMsg::mandatoryPayment();
        $frm        = new FormatMandatory($man["inq"], $resp);
        $params     = setMandatoryResponIrs($frm, $ref1, "", "","CEK", $data);
        $frm        = getParseProduk($kdproduk, $resp);
        // print_r($frm);die();
        if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
            $adddata    = tambahdataproduk($kdproduk, $frm,""); // "" diisi $kodebank
        }else{
            $adddata    = tambahdataproduk($kdproduk, $frm);
        }
        $adddata2   = tambahdataproduk2($kdproduk, $frm);
        $merge      = array_merge($params,$adddata,$adddata2);
      
        return json_encode($merge, JSON_PRETTY_PRINT);

    }elseif(substr(strtoupper($kdproduk), 0,5) == "BAYAR"){ // BAYAR TRANSKASI

        $kdproduk   = substr($kdproduk,5); // GET ID PRODUK
        $r_tanggal  = date('YmdHis');
        // cek request
        if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS"){

            if(substr($idpel1, 0, 5) != "88888"){
                $idpel1 = "88888".substr($idpel1, 2, 11);
            }
            $periodereq = trim(strtoupper($add));
            $hp         = randomHp();
            $field      = 7;
        } else if(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $nominal    = trim(strtoupper($add));
            $field      = 7;    
        } else if(substr(strtoupper($kdproduk), 0,2) == "KK"){
            $nominal_req= trim(strtoupper($add));
            $field      = 7;
        } else if(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
            $idpel2     = trim(strtoupper($add));
            $field      = 7;
        } else if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
             return 'error=transfer dana belum ready diformat ini';
            // $nominal_req= trim(strtoupper($req['nominal']));
            // $kodebank   = trim(strtoupper($req['kodebank']));
            // $nomorhp    = trim(strtoupper($req['nomorhp']));
            // $field      = 8;
        } else {
            $field      = 7;
        }
        // cek request

        // validasi request
        if(count((array)$data) !== $field){
            return 'error=missing parameter request';
        }
        // validasi request

        // handle 504 start
        if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
            if(empty(urlreversalexists($idoutlet))){
                date_default_timezone_set('asia/jakarta');
                $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
                $datein = (int) strtotime(date('Y-m-d H:i:s'));
                $selisih = $datein - $my_ips;
                appendfile('IRS PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
                if($selisih >= $GLOBALS["__G_selisih"]){
                    $params = array(
                        'kodeproduk' => (string) $kdproduk, 
                        'tanggal' => (string) $r_tanggal, 
                        'idpel1' => (string) $idpel1, 
                        'idpel2' => (string) $idpel2, 
                        'idpel3' => (string) '', 
                        'nominal' => (string) '', 
                        'admin' => (string) '', 
                        'id_outlet' => (string) $idoutlet, 
                        'pin' => (string) "------", 
                        'ref1' => (string) $ref1, 
                        'ref2' => (string) '', 
                        'ref3' => (string) '', 
                        'status' => (string) '77', 
                        'keterangan' => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi', 
                        'fee' => (string) '', 
                        'saldo_terpotong' => (string) '',
                        'sisa_saldo' => (string) '',
                        'total_bayar' => (string) '',
                    );
                    appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  IRS (BAYAR): '.joinimplode2($params));
                      insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'IRS',strtoupper("IRS ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                    return joinimplode2($params);
                }
            }
        }
        // handle 504 end

        if($idoutlet != 'FA9919'){
            $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
            if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
                if (!isValidIP($idoutlet, $ip)) {
                    return "IP Anda [$ip] tidak punya hak akses";
                }
            }
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
        // handle idpel dan kode produk

        // get data idtrx, nominal, periode, dll
        $getdataCustom = getdataCustom($idoutlet, $kdproduk, $idpel1, $idpel2);
        $id_transaksi = $getdataCustom[0];
        if(substr($kdproduk, 0, 6) != 'PLNPRA' || substr($kdproduk, 0, 2) == 'KK'){
            $nominal = $getdataCustom[1]; 
        }
        if(substr($kdproduk, 0, 7) == 'PLNPRAD'){
            $nominal = $getdataCustom[1];
            $idpel2  = getIdpel2($id_transaksi);
        }
        $bill_info6 = $getdataCustom[2];
        $bill_info12 = $getdataCustom[3];
        $nohpdb = $getdataCustom[4];
        // get data idtrx, nominal, periode, dll

        //get denda pajak pbb
        $tahun_pajak = "";
        if(substr(strtoupper($kdproduk),0,5) == "PAJAK"){
            $dendapbb = cekglobal($id_transaksi,'bill_info30');
            $tahun_pajak = $getdataCustom[5];
        }

        //get denda pajak pbb

        // validasi request dengan db
        $nohp_length    = array('10', '11', '12');
        $nominalplnpra  = array('20000','50000','100000','200000','500000','1000000','5000000','10000000','50000000');
        if($id_transaksi == ""){
            return 'error=data inquiry tidak ditemukan';
        } else if(substr(strtoupper($kdproduk), 0,5) == "PAJAK" && $idpel2 != $tahun_pajak){
            return 'error=periode tahun pajak tidak sama';
        } else if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS" && (int)$periodereq != (int)$bill_info12){
            return 'error=periode bpjs tidak sama';
        } else if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS" && (substr($hp, 0, 1) != '0' || !is_numeric($hp) || !in_array(strlen($hp), $nohp_length) || $hp == '') ){
            return "error=no hp tidak valid";
        } else if(substr($kdproduk, 0, 6) == 'PLNPRA' && !in_array($nominal, $nominalplnpra)){
            return 'error=nominal plnpra ('.$nominal.') tidak valid. Coba dengan nominal '.implode(', ',$nominalplnpra);
        } else if(substr($kdproduk, 0, 2) == 'KK' && $nominal != $nominal_req){
            return 'error=nominal tidak sesuai dengan data inquiry';
        } else if(substr(strtoupper($kdproduk), 0,5) == "BLTRF" && $nominal != $nominal_req){
            return 'error=data tidak sesuai dengan data inquiry';
        } 


        // validasi request dengan db
        // global $pgsql;
        global $host;
        
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
        $msg[$i+=1] = strtoupper($idoutlet);
        $msg[$i+=1] = $pin;
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = "";
        $msg[$i+=1] = $id_transaksi;
        $msg[$i+=1] = substr(strtoupper($kdproduk), 0,5) == "BLTRF" ? '' : $ref1;
        $msg[$i+=1] = strtoupper("IRS ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;

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
                $msg[$i+=1] = ""; // $nomorhp
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = "";
                $msg[$i+=1] = ""; // $kodebank
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
            $msg[$i+=1] = "";//FIELD_NAMA $idpel3
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

        $fm = convertFM($msg, "*");
        // echo "fm = ".$fm."<br>";
        $sender = $GLOBALS["__G_module_name"];
        $receiver = $GLOBALS["__G_receiver"];
        $hidepin = explode($pin, $fm);
        $store_fm = $hidepin[0].'......'.$hidepin[1];
        $list = $GLOBALS["sndr"];
       
        $respon = postValue($fm);
        $resp = $respon[7];
               

        if($resp == 'null'){
            $params = array(
                'kodeproduk' => (string) 'BAYAR'.$kdproduk, 
                'tanggal' => (string) $r_tanggal, 
                'idpel1' => (string) $idpel1, 
                'idpel2' => (string) $idpel2, 
                'idpel3' => (string) "", 
                'nominal' => (string) '', 
                'admin' => (string) '', 
                'id_outlet' => (string) $idoutlet, 
                'pin' => (string) "------", 
                'ref1' => (string) $ref1, 
                'ref2' => (string) "", 
                'ref3' => (string) "", 
                'status' => (string) '00', 
                'keterangan' => (string) 'SEDANG DIPROSES', 
                'fee' => (string) '', 
                'saldo_terpotong' => (string) '',
                'sisa_saldo' => (string) '',
                'total_bayar' => (string) '',
            );
            return joinimplode2($params);
        }

        $man        = FormatMsg::mandatoryPayment();
        $frm        = new FormatMandatory($man["inq"], $resp);    
        $params     = setMandatoryResponIrs($frm, $ref1, "", "","BAYAR", $data);
        $frm        = getParseProduk($kdproduk, $resp);
        if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
            $adddata    = tambahdataproduk($kdproduk, $frm,"");
        }else{
            $adddata    = tambahdataproduk($kdproduk, $frm,'',1);
        }
        $adddata2   = tambahdataproduk2($kdproduk, $frm);
        if ($frm->getStatus() == "00") {
            $url        = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
            $url_struk  = array("url_struk" => "http://34.101.201.189/strukmitra/?id=" . $url);
            $merge      = array_merge($params,$adddata,$adddata2,$url_struk);
        } else {
            $merge      = array_merge($params,$adddata,$adddata2);
        }
         return json_encode($merge, JSON_PRETTY_PRINT);
    }elseif(substr(strtoupper($kdproduk), 0,4) == "BELI"){
        $kdproduk = substr($kdproduk,4); // ID PRODUK
        if(substr(strtoupper($kdproduk), 0,6) == "PLNPRA"){
            $nominal    = trim(strtoupper($add));
            $field      = 7;
        }else{
            return 'error=hanya untuk produk plnprepaid';
        }
        // print_r($req);die();
        if(count((array)$data) !== $field){
            return 'error=missing parameter request';
        }
        $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

        if($idoutlet != 'FA9919'){
            if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
                if (!isValidIP($idoutlet, $ip)) {
                    return "IP Anda [$ip] tidak punya hak akses";
                }
            }
        }

        if(substr($kdproduk, 0, 6) == 'PLNPRA'){
             if(substr($kdproduk, 0, 7) == 'PLNPRAD'){
                $nominal = getnominal_plnprad($kdproduk);
            }else{
                if(strlen($idpel1) == '12'){
                    $idpel2 = $idpel1;
                    $idpel1 = "";
                }
            }
        }

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
        $msg[$i+=1] = substr($kdproduk, 0,2) == "KK" || substr(strtoupper($kdproduk), 0,5) == "BLTRF" ? "" : $nominal;
        $msg[$i+=1] = substr($kdproduk, 0,2) == "EM" || substr(strtoupper($kdproduk), 0,7) == "PLNPRAD" ? $nominal : "";
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
        $msg[$i+=1] = strtoupper("IRS ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
        if(substr(strtoupper($kdproduk), 0,7) == "PLNPRAD"){
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
        }

        $fm = convertFM($msg, "*");
        $sender = $GLOBALS["__G_module_name"];
        $receiver = $GLOBALS["__G_receiver"];
        $hidepin = explode($pin, $fm);
        $store_fm = $hidepin[0].'......'.$hidepin[1];
        $list = $GLOBALS["sndr"];
        
        $respon = postValue($fm);
        $resp = $respon[7];
           
        $man        = FormatMsg::mandatoryPayment();
        $frm        = new FormatMandatory($man["inq"], $resp);
        $params     = setMandatoryResponIrs($frm, $ref1, "", "","BELI", $data);
        $frm        = getParseProduk($kdproduk, $resp);

        $r_step     = $frm->getStep()+1;
        $r_kdproduk = $frm->getKodeProduk();
        $r_tanggal  = $frm->getTanggal();
        $r_idpel1   = $frm->getIdPel1();
        $r_idpel2   = $frm->getIdPel2();
        $r_idpel3   = $frm->getIdPel3();
        $r_nominal  = (int) $frm->getNominal();
        $r_nominaladmin = (int) $frm->getNominalAdmin();
        $r_idoutlet = $frm->getMember();
        $r_pin      = $frm->getPin();
        $r_sisa_saldo = $frm->getSaldo();
        $r_idtrx    = $frm->getIdTrx();
        $r_status   = $frm->getStatus();
        $r_keterangan = $frm->getKeterangan();
        $r_mid      = $frm->getMID();
        // print_r($frm);die();
        if(substr(strtoupper($kdproduk), 0,7) == "PLNPRAD"){
            $nominalnya = $r_nominal;
        }else{
            $nominalnya = $nominal;
        }
        $md_request = array(
            'method' => 'rajabiller.bayar',
            'kode_produk' => $r_kdproduk,
            'idpel' => $r_idpel1,
            'nominal' => $nominalnya,
            'uid' => $r_idoutlet,
            'pin' => $pin,
            'ref1' => $ref1,
            'ref2' => $r_idtrx
        );
    
        if($r_status == "00"){
            $md_response = bayar((object)$md_request);
            return $md_response;
        } else {
            $adddata    = tambahdataproduk($kdproduk, $frm);
            $adddata2   = tambahdataproduk2($kdproduk, $frm);
            $merge      = array_merge($params,$adddata,$adddata2);
          
            return json_encode($merge, JSON_PRETTY_PRINT);
        }
    }else{
        return 'error=format kode produk salah , hubungin kami jika ada kendala';
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

    $whitelist_outlet = array('FA9919', 'FA112009', 'FA32670');
    if(!in_array($uid, $whitelist_outlet) && substr($uid, 0, 2) == "FA"){
        return json_encode(array('error'=>'uid not allowed'));
    }
    
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
        if (!isValidIP($uid, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    $idpel_asli = "";
    if (in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        $normal_idpel = normalisasiIdPel1PLNPra($idpel);
        $idpel1 = $normal_idpel["idpel1"];
        $idpel2 = $normal_idpel["idpel2"];
        $idpel_asli = $normal_idpel["idpel_asli"];
    }

    if($idpel1 == "" && $idpel2 == ""){
        return json_encode(array('error'=>'in valid number id'));
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;
    $fm = convertFM($msg, "*");
    
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    
    $list = $GLOBALS["sndr"];

    if( in_array($kdproduk, KodeProduk::getPLNPrepaids()) ){
        $respon = postValue($fm);
        $resp = $respon[7];    
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
        'idpel1' => $idpel1,
        'idpel2' => $idpel2,
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

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

    if (emailexists($email)) {
        return json_encode(array('error'=>"Email sudah terdaftar"));
    }

    $search = array('.', ' ');
    $replace = array('', '');

    if (!ctype_alpha(str_replace($search, $replace, $nama))){
        return json_encode(array('error'=>"Nama hanya boleh huruf"));
    }
    
    if (!outletexists($idoutlet)) {
        return json_encode(array('error'=>"ID Outlet tidak terdaftar atau tidak aktif"));
    }

    if (!checkpin($idoutlet, $pin)) {
        return json_encode(array('error'=>"pin yang anda masukkan salah"));
    } 

    $id_kota = get_id_kota($idoutlet);

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
    $msg[$i+=1] = $id_kota; //idkota
    $msg[$i+=1] = ""; //kodepos
    $msg[$i+=1] = "13"; //tipeloket
    $msg[$i+=1] = "0"; //flagregional
    $msg[$i+=1] = strtoupper('CB0001');
    $msg[$i+=1] = '141414';
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
    // echo $store_fm;
    // die();
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon     = postValue($fm);
    $resp       = $respon[7];
    // $resp = "DAFTAR*DAFTAR*6528105*4**XML*081216880033*RONALDINHO*BRASIL*5*347*65139*1*0*FA0002*bimasa**99925*15794571*FA27160*640480*00*PENDAFTARAN NO. 081216880033 SUKSES, ID Outlet: FA27160. Silahkan melakukan aktifasi. Trx Normal dan Lancar";
    // echo $resp;
    // die();
    $format     = FormatMsg::daftar();
    $frm        = new FormatDaftar($format[1], $resp);

    $r_idoutlet = $frm->getMember();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
    $r_idoutletbaru = $frm->getMemberBaru();
    $r_hp = $frm->getNoHP();

    if($r_status == '00'){
        //aktifkan outlet by query
        set_aktif_outlet($r_idoutletbaru);
    }

    $params = array(
        "UID"           => $r_idoutlet,
        "PIN"           => '------',
        "STATUS"        => $r_status,
        "UID_MEMBER"    => $r_idoutletbaru,
        "HP_MEMBER"     => $r_hp,
        "KET"           => $r_status == "00" ? "SUKSES" : $r_keterangan
    );

    return json_encode($params, JSON_PRETTY_PRINT);

    // echo "<pre>", print_r($frm) ,"</pre>";

}

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
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        return json_encode(array('error'=>"Anda tidak punya hak akses"));
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

function group_produk($data){
    $result = array();
    $next   = FALSE;
    $end    = FALSE;
    // global $pgsql;

    $group      = strtoupper($data->group);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);

    // echo $group."".$id_produk;
    $field      = 4;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    // if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
    //     if (!isValidIP($idoutlet, $ip)) {
    //         return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    //     }
    // }

    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        return json_encode(array('error'=>"Anda tidak punya hak akses"));
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
        $foruse = foruse($group, $idoutlet);
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
                // $dt[] = array(
                //     'idproduk' => $foruse[$i]->id_produk,
                //     'namaproduk' => $foruse[$i]->produk,
                //     'harga_jual' => $foruse[$i]->harga_jual,
                //     'biaya_admin' => $foruse[$i]->biaya_admin,
                //     'status' => $status
                // );
                $datas[] = $foruse[$i]->id_produk;
                $datas[] = $foruse[$i]->produk;
                $datas[] = $foruse[$i]->harga_jual;
                $datas[] = $foruse[$i]->biaya_admin;
                $datas[] = $komisi;
                $datas[] = $status;
                $msgcontent[] =  implode("#", $datas);
            }
            // print_r($dt);
            $result = $msgcontent;
            $params = array(
                "UID"       => $idoutlet,
                "PIN"       => '------',
                "STATUS"    => "00",
                "KET"       => "Data ditemukan",
                "DATA"      => $result 
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
    $cek = is_numeric($idtrx);

    if($idtrx != ""){
        if($cek == 0){
        return json_encode(array('error'=>'id_transaksi harus value dari ref2 payment'));
        }
    }
    if(count((array)$data) !== $field && count((array)$data) !== $field2){
        return json_encode(array('error'=>'missing parameter request'));
    }

    if(empty($tgl1) && empty($idtrx)){
        return json_encode(array('error'=>'tanggal 1 (tgl1) atau id_transaksi wajib diisi salah satu'));
    }

    if($idproduk == 'HPTSEL'){
        $idproduk = 'HPTSELH';
    } else if($idproduk == 'ASRBPJSKS'){
        $idproduk = 'ASRBPJSKSH';
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

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
        $dataproses       = getDataProsesTransaksiv2($idoutlet, $idtrx, $idproduk, $idpel, $limit, $tgl1, $tgl2, $custreff);
        $cekproses = count($dataproses);
        if($cekproses > 0)
        {
            $msgcontent[] = "Transaksi berhasil";
            //Looping
            for ($i = 0; $i < $cekproses; $i++) {
                $datatransaksi = array();
                $datatransaksi[] = $dataproses[$i]->id_transaksi;
                $datatransaksi[] = $dataproses[$i]->transaksidatetime;
                                
                if($dataproses[$i]->id_produk == 'HPTSELH'){
                    $datatransaksi[] = $dataproses[$i]->id_produk = "HPTSELH";
                } else if($dataproses[$i]->id_produk == 'ASRBPJSKSH'){
                    $datatransaksi[] = $dataproses[$i]->id_produk = "ASRBPJSKSH";
                } else {
                    $datatransaksi[] = $dataproses[$i]->id_produk;
                }

                $datatransaksi[] = $dataproses[$i]->namaproduk;
                $datatransaksi[] = $dataproses[$i]->idpelanggan;
                $datatransaksi[] = $dataproses[$i]->response_code = "00";
                $datatransaksi[] = $dataproses[$i]->keterangan = "SEDANG DIPROSES";
              

                $datatransaksi[] = $dataproses[$i]->nominal;
                if(in_array($dataproses[$i]->id_produk, KodeProduk::getPLNPrepaids())){
                    $datatransaksi[] = $dataproses[$i]->token;
                } else {
                    $datatransaksi[] = str_replace("#",'\/',$dataproses[$i]->sn);
                }

                $status_trx = "PENDING";
                $datatransaksi[] = $status_trx;

                $result_data_trx_log = implode("#", $datatransaksi);
                $get_step = get_step_from_mid($dataproses[$i]->mid) + 1;
                // writeLog($data[$i]->mid, $get_step, $receiver, $host, $result_data_trx_log, $GLOBALS["via"]);

                $msgcontent[] = implode("#", $datatransaksi);
            }
        }else{
            $data       = getDataTransaksiv2($idoutlet, $idtrx, $idproduk, $idpel, $limit, $tgl1, $tgl2, $custreff);
            $cnt        = count($data);
           
            $narindo_rc = array("1", "2", "3", "5", "12", "15", "21", "35", "68", "80","4");
            $gigaotomax_rc = array("35", "68", "XX", "05");
            $eratel_rc = array("68", "57");
            $servindo_rc = array("35");
            $gigaotomax_rc_replace = array("16");
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
        'method'    => 'rajabiller.datatransaksi',
        'tgl1'      => $tgl1,
        'tgl2'      => $tgl2,
        'idtrx'     => $idtrx,
        'idproduk'  => $idproduk,
        'ref1'      => $custreff,
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

    $log = array("request"=> $req , "response" => $params);
    // write_log_text($log_data);
    writeLog('1', '1', $idoutlet, $_SERVER['SERVER_NAME'].'|'.$ip,json_encode($log), 'H2H');

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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($uid, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($uid, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
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
    $r_keterangan   = $frm->getKeterangan();
    $r_status       = strpos(strtolower($r_keterangan), 'tidak ditemukan') !== false ? '99' : $frm->getStatus();

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

    if($kodebank == ''){
        return json_encode(array('error'=>'kode bank wajib diisi'));
    } 

    // if($nominal == ''){
    //     return json_encode(array('error'=>'nominal wajib diisi'));
    // }

    // $digit = strlen($idpel1);
    // if($kdproduk == "BLTRFMDR"){
    //     if($kodebank == "008"){
    //         if($digit > 13){
    //             return json_encode(array('error'=>'EXT : Transaksi VA tidak dapat diproses'));
    //         }
    //     }
    // } 

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($uid, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }
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
    // $msg[$i+=1] = "MOBILE_SMART";           //VIA
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = "";
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME']; // FIELD_KETERANGAN
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
    $msg[$i+=1] = $idpel3;
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


    $fm = convertFM($msg, "*");
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    // echo $msg."<br>";
    // die();
    $list = $GLOBALS["sndr"];
    $respon = postValue($fm);
    $resp = $respon[7]; 

   
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

    //$db = new Database();
    //$q_ins_log = "INSERT INTO fmss_message (id_transaksi, id_transaksi_partner, mid, content, insert_datetime) 
    //              VALUES (".$id_transaksi.", ".$id_transaksi_partner.", ".$mid.", '".replace_forbidden_chars_msg($resp)."', NOW())";
    //$e_ins_log = mysql_query($q_ins_log, $db->getConnection());

    // $text = inq_resp_text($params);
    // $get_mid = get_mid_from_idtrx($r_idtrx);
    // $get_step = get_step_from_mid($r_mid) + 1;
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), "H2H");
    return json_encode($params, JSON_PRETTY_PRINT);
}

function transferpay($data)
{
    // die('a');
    // BAYAR*BLTRFABNI*4094581631*2*20200508012336*MOBILE_SMART*530401024308538***110000**FA133192*------*------*****1783216123**

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
    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($uid))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
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
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
               insertLogRc77($my_ips,$datein,$selisih,$uid,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }

    // handle 504 end
    if($kodebank == ''){
        return json_encode(array('error'=>'kode bank wajib diisi'));
    } 

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($uid, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }
  
    // global $pgsql;
    global $host;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
    if($ref2 == ""){
        $ref2 = 0;
    }
    $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan

    $stp        = $GLOBALS["step"] + 1;
    $msg        = array();
    $i          = -1;
    $msg[$i+=1] = $mti;
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    // $msg[$i+=1] = rand(100000,100000000);
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');
    // $msg[$i+=1] = "MOBILE_SMART";           //VIA
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = "";
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip; // FIELD_KETERANGAN
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
    $msg[$i+=1] = $idpel3;
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
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];


    /* tambahan */
        if ($ceknom != $nominal) {
             $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $uid . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else {
            
            $respon = postValue($fm);
            $resp = $respon[7]; 
        }

     
    /* tambahan */

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
            "REF2"              => (string) '',
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
            $r_idtrx = getIdTransaksi($bill_info1,$uid,$kdproduk,$ref1);
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

        $nom_up = getnominalup($r_idtrx);
        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
        $url = enkripUrl(strtoupper($uid), $frm->getIdTrx());
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
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), "H2H");
    }
    
    return json_encode($params, JSON_PRETTY_PRINT);

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

    if(in_array($sub_str1, $prefix_telp, TRUE)){
        $is_telp = TRUE;
        $len = strlen($sub_str1);
        $ret = array(
            'produk'    => 'TELEPON',
            'idpel1'    => $sub_str1,
            'idpel2'    => substr($idpel,$len)
        );
    } else if(in_array($sub_str2, $prefix_telp , TRUE)){
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

     // print_r($req);die();
    if(count((array)$data) !== $field){
        return 'error=missing parameter request';
    }

    if(substr($kdproduk, 0, 5) == 'PAJAK'){
        if(strlen($idpel1) != 18){
             return "Nomor Object Pajak(NOP) harus 18 digit";
        }
    }

    if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS"){
        if(substr($idpel1, 0, 5) != "88888"){
             return "Maaf , nomor bpjs tidak perlu menggunakan 89888 , bisa only idpel atau ditambahkan 88888 didepan";
        }
    }
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

   if($uid != 'SP300203'){
        if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
            if (!isValidIP($uid, $ip)) {
                return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
            }
        }
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
         if(substr($kdproduk, 0, 7) == 'PLNPRAD'){
            $nominal = getnominal_plnprad($kdproduk);
        }else{
            if(strlen($idpel1) == '12'){
                $idpel2 = $idpel1;
                $idpel1 = "";
            }
        }
    }

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

    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    // writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    if(substr(strtoupper($kdproduk), 0,2) == "EM"){
        if($kdproduk == "EMDANA"){
            if($nominal < 20000){
                $tgl = date("Ymdhis");
                $resp ="TAGIHAN*EMDANA*5332240573*10*".$tgl."*H2H*".$idpel1."*****".$uid."*------***1****XX*Nominal Tidak Boleh Kurang Dari 20000*********************";
            }else{
                 $respon =  postValueWithTimeOutCustom($fm,'10.0.0.14','25080','/FMSSWeb2/mpin1', $timeout = 40);
                 $resp = $respon[7];
             }
        }else{
            $respon =  postValueWithTimeOutCustom($fm,'10.0.0.14','25080','/FMSSWeb2/mpin1', $timeout = 40);
            $resp = $respon[7];
        }
    }else{
        $respon = postValue($fm);
        $resp = $respon[7]; 
    }
  
    $man        = FormatMsg::mandatoryPayment();
    $frm        = new FormatMandatory($man["inq"], $resp);
    $params     = setMandatoryResponJson($frm, $ref1, "", "", $data);
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

    // print_r($data);
    // die();
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if($uid != 'SP300203'){
        if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
            if (!isValidIP($uid, $ip)) {
                return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
            }
        }
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
        $dendapbb = cekglobal($ref2,'bill_info30');
    }

    $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan
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
    $msg[$i+=1] = strtoupper("IRS ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;

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
    // echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())){
        if($ceknom != $nominal){
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        }else{
            if(substr(strtoupper($kdproduk), 0,2) == "EM"){
                if($kdproduk == "EMDANA"){
                    if($nominal < 20000){
                        $tgl = date("Ymdhis");
                        $resp ="BAYAR*EMDANA*5332240573*10*".$tgl."*H2H*".$idpel1."*****".$uid."*------***1****XX*Nominal Tidak Boleh Kurang Dari 20000*********************";
                        
                    }else{
                         $respon =  postValueWithTimeOutCustom($fm,'10.0.0.14','25080','/FMSSWeb2/mpin1', $timeout = 40);
                         $resp = $respon[7];
                     }
                }else{
                    $respon =  postValueWithTimeOutCustom($fm,'10.0.0.14','25080','/FMSSWeb2/mpin1', $timeout = 40);
                    $resp = $respon[7];
                }
            }else{
                $respon = postValue($fm);
                $resp = $respon[7]; 
            }
        }
    }else{
        $respon = postValue($fm);
        $resp = $respon[7]; 
    }
    
    $man        = FormatMsg::mandatoryPayment();
    $frm        = new FormatMandatory($man["pay"], $resp);
    $params     = setMandatoryResponJson($frm, $ref1, "", "", $data);
    $frm        = getParseProduk($kdproduk, $resp);

     if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $adddata    = tambahdataproduk($kdproduk, $frm,$kodebank);
    }else{
        $adddata    = tambahdataproduk($kdproduk, $frm,'',1);
    }
    $adddata2   = tambahdataproduk2($kdproduk, $frm);
    // print_r($adddata);die();

    if ($frm->getStatus() == "00") {
        $url        = enkripUrl(strtoupper($uid), $frm->getIdTrx());
        $url_struk  = array("url_struk" => "http://34.101.201.189/strukmitra/?id=" . $url);
        $merge      = array_merge($params,$adddata,$adddata2,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2);
    }
  
    return json_encode($merge, JSON_PRETTY_PRINT);
}

function inq($data){
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

    // print_r($data);
    // die();
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if($uid != 'SP300203'){
        if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
            if (!isValidIP($uid, $ip)) {
                return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
            }
        }
    }

    if(substr($kdproduk, 0, 5) == 'PAJAK'){
        if(strlen($idpel1) != 18){
             return json_encode(array('error'=>'Nomor Object Pajak(NOP) harus 18 digit'));
        }
    }

    if($uid == "SP356173"){
         if($kdproduk == "TELEPON" || $kdproduk == "SPEEDY"){
            $cek_telkom = cek_is_telp_or_speedy($idpel1);
            $kdproduk   = $cek_telkom['produk'];
            $idpel1     = $cek_telkom['idpel1'];
            $idpel2     = $cek_telkom['idpel2'];
        }
    }

    // echo $GLOBALS["mid"];die();
    // global $pgsql;
    global $host;

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
    }

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "TAGIHAN";
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    // $msg[$i+=1] = "1212121343434";
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = "";
    // $msg[$i+=1] = "MOBILE_SMART";           //VIA
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
    if(substr(strtoupper($kdproduk),0,5) == "PAJAK"){
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
    }
    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>";die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    // echo $msg."<br>";
    // die();
    $list = $GLOBALS["sndr"];
    if($kdproduk == 'ASRBPJSKS'){
        $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$uid."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
    } else if(substr($uid, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA32670','FA172154');
        if(!in_array($uid, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$uid."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
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
                $resp = "TAGIHAN*PLNPRAH*1727174759*11*".date('YmdHis')."*H2H*$idpel1*****$uid*$pin*------******XX*NOMOR METER ATAU IDPEL YANG ANDA MASUKKAN SALAH, MOHON TELITI KEMBALI**$idpel1********************************    ";
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
                        $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                 }elseif($id_biller ==  281){
                    $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
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

    //insert into fmss_message
    $mid = $GLOBALS["mid"];
    $id_transaksi = $r_idtrx;
    $id_transaksi_partner = $ref1;

    //$db = new Database();
    //$q_ins_log = "INSERT INTO fmss_message (id_transaksi, id_transaksi_partner, mid, content, insert_datetime) 
    //              VALUES (".$id_transaksi.", ".$id_transaksi_partner.", ".$mid.", '".replace_forbidden_chars_msg($resp)."', NOW())";
    //$e_ins_log = mysql_query($q_ins_log, $db->getConnection());

    // $text = inq_resp_text($params);
    // $get_mid = get_mid_from_idtrx($r_idtrx);
    // $get_step = get_step_from_mid($r_mid) + 1;
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
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
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) $ref2,
                    "REF3"              => (string) $ref3,
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.' RESPON PAY FAIL JSON (BAYAR): '.json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }
    
    // global $pgsql;
    global $host;
    //if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    die("");
    //}

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    $asr = array('ASRCAR', 'ASRTOKIOS', 'ASRTOKIO', 'ASRJWS','ASRPRU');
    if(in_array($kdproduk, $asr)){
        $mti = "BAYAR";
    }

    if($kdproduk == 'HPTSEL'){
        $kdproduk = 'HPTSELH';
    }
    if($ref2 == ""){
        $ref2 = 0;
    }
    $ceknom     = getNominalTransaksi(trim($ref2)); //tambahan

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
    $fm         = convertFM($msg, "*");
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];


    /* tambahan */
    if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else if($kdproduk == 'ASRBPJSKS'){
            $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            //$respon = postValueWithTimeOutDevel($fm);
             if(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
            }else{
               $id_biller = getBiller($kdproduk);
                 if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
                   if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                        $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                        $resp = $respon[7]; 
                    }else{
                        $respon = postValue($fm);
                        $resp = $respon[7]; 
                    }   
                 }elseif($id_biller ==  281){
                    $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
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
         if(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
            }else{
               if($kdproduk == 'ASRBPJSKS'){
                    $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
                } else {
                    $id_biller = getBiller($kdproduk);
                     if($id_biller == 29 || $id_biller == 195 || $id_biller == 11017){
                       if(strpos(strtoupper($kdproduk),'WAMAKASAR') !== false){
                            $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
                            $resp = $respon[7]; 
                        }else{
                            $respon = postValue($fm);
                            $resp = $respon[7]; 
                        }   
                     }elseif($id_biller ==  281){
                        $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
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

   
    /* tambahan */

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
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);
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
    $r_nama_pelanggan   = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }

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

    if($frm->getStatus() == "35"){
        $id_biller = getIdBiller($r_idtrx);
        if($id_biller == '26' && strpos(strtolower($r_keterangan), 'sedang diproses') !== false ){ 
            // SERVINDO
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
        }
    }
    if($frm->getStatus() == "35" 
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
        "URL_STRUK"         => (string) $url_struk
    );

    $is_return = true;
 
    if($r_idtrx != $ref2){
        // $text = pay_resp_text($params);
        // $get_mid = get_mid_from_idtrx($r_idtrx);
        // $get_step = get_step_from_mid($r_mid) + 1;
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    }
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
          
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
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
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) $ref2,
                    "REF3"              => (string) $ref3,
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.' RESPON PAY FAIL JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "::1" ) {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    // global $pgsql;
    global $host;

    $mti = "BAYAR";
    if (in_array($kdproduk, KodeProduk::getAsuransi()) || in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $mti = "TAGIHAN";
    }

    $asr = array('ASRCAR', 'ASRTOKIOS', 'ASRTOKIO', 'ASRJWS','ASRPRU');
    if(in_array($kdproduk, $asr)){
        $mti = "BAYAR";
    }

    if($ref2 == ""){
        $ref2 = 0;
    }

    $ceknom = getNominalTransaksi(trim($ref2));
    
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN
    if ($kdproduk == "ASRJWS") {
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "";           //KETERANGAN
        $msg[$i+=1] = "1";           //KETERANGAN
    }else if(substr(strtoupper($kdproduk),0,5) == "PAJAK"){
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
        $msg[$i+=1] = $idpel3;//FIELD_NAMA
        $msg[$i+=1] = "";//FIELD_LOKASI
        $msg[$i+=1] = "";//FIELD_KELURAHAN
        $msg[$i+=1] = "";//FIELD_KECAMATAN
        $msg[$i+=1] = "";//FIELD_PROVINSI
        $msg[$i+=1] = "";//FIELD_LUAS_TANAH
        $msg[$i+=1] = "";//FIELD_LUAS_BANGUNAN
        $msg[$i+=1] = "";//FIELD_TANGGAL_JTH_TEMPO
        $msg[$i+=1] = $nominal;//FIELD_TAGIHAN
        $msg[$i+=1] = "";//FIELD_DENDA 
        $msg[$i+=1] = $nominal;//FIELD_TOTAL_BAYAR
    }
    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];

    if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];   
        }
    }else {
        $respon = postValue($fm);
        $resp = $respon[7]; 
                       
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
    $frm = new FormatMandatory($man["pay"], $resp);
    $frm = getParseProduk($kdproduk, $resp);

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
    $r_nama_pelanggan   = getNamaPelanggan($kdproduk, $frm);
    //$r_periode_tagihan = getBillPeriod($kdproduk,$frm);
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }
    $r_additional_datas = getAdditionalDatas($kdproduk, $frm);

    if($r_status === ''){
        $r_status = '00';
        if($r_idtrx === ""){
            if($idpel1 == ""){
                $bill_info1 = $idpel2;
            } else {
                $bill_info1 = $idpel1;
            }
            $r_idtrx = getIdTransaksi($bill_info1,$idoutlet,$kdproduk,$ref1);
        }
    }

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

    if($frm->getStatus() == "35"){
        $id_biller = getIdBiller($r_idtrx);
        if($id_biller == '26' && strpos(strtolower($r_keterangan), 'sedang diproses') !== false ){ 
            // SERVINDO

        $nom_up = getnominalup($r_idtrx);

        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
        $r_status = "00";
        $r_keterangan = "SEDANG DIPROSES";
        }
    }

    if($r_status == '35' || $r_status == '68'){

        $nom_up = getnominalup($r_idtrx);

        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
    }
    if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){

        $nom_up = getnominalup($r_idtrx);
        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);
        $r_status = "00";
        $r_keterangan = "SEDANG DIPROSES";
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
   
    if($r_idtrx != $ref2){
        // $text = paydetil_resp_text($params, $kdproduk);
        // $get_mid = get_mid_from_idtrx($r_idtrx);
        $get_step = get_step_from_mid($r_mid) + 1;
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    }
    
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

    cekMandatoryRequestJsonPulsaGame($data);
    
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    "KODE_PRODUK"       => (string) $kdproduk,
                    "WAKTU"             => (string) date('YmdHis'),
                    "NO_HP"             => (string) $nohp,
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "SN"                => (string) '',
                    "NOMINAL"           => (string) '',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) '',
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "STATUS_TRX"        => (string) 'GAGAL',
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end

    // global $pgsql;
    global $host;

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = "PULSA";
    $msg[$i+=1] = $kdproduk;      //KDPRODUK
    $msg[$i+=1] = $GLOBALS["mid"];     //MID
    // $msg[$i+=1] = '121212323434';     //MID
    $msg[$i+=1] = $stp;        //STEP
    $msg[$i+=1] = "";        //TANGGAL
    $msg[$i+=1] = $GLOBALS["via"];     //VIA
    // $msg[$i+=1] = 'MOBILE_SMART';     //VIA
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;        //FIELD_KETERANGAN

    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA89065', 'FA32670');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$nohp."*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
            $respon = postValue_fmssweb2($fm);
            $resp = $respon[7];
      
    }


    if($resp == 'null'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $r_kdproduk = $kdproduk;
        $r_tanggal = date('YmdHis');
        $r_nohp = $nohp;
        $r_idoutlet = $idoutlet;
        $status_trx = "PENDING";
        $r_saldo_terpotong = '';
        $r_sisa_saldo = '';
        $r_idtrx = '';
        $r_nominal = '';
        $r_sn = '';

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
            "STATUS_TRX"        => (string) $status_trx,
        );
    
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    
        return json_encode($params, JSON_PRETTY_PRINT);
    } 
   
    // $resp = "PULSA*S10H*3133723466*9*20190108131453*H2H*082397522285*10000*HH82915*------**SK10**561115175*1234601971*999*EXT: FAILED   ";
    // $resp="PULSA*".$kdproduk."*805817646*10*20190108131453*H2H*".$nohp."**".$idoutlet."*".$pin."*------******"; 
    $format = FormatMsg::pulsa();
    $frm = new FormatPulsa($format[1], $resp);

    //print_r($frm->data);
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMid();
    // $r_nominal          = $frm->getNominal();
    // $nom_up             = getnominalup($r_idtrx);
    $r_saldo_terpotong  = $r_nominal;

    // if($r_status === ''){
    //     $r_status = '00';
    //     if($r_idtrx === ""){
    //         $r_idtrx = getIdTransaksi($nohp,$idoutlet,$kdproduk,$ref1);
    //     }
    // }

    if($r_status === '35' || $r_status === '68'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
    }

    if($r_status === '00' && ($r_keterangan !== "" && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') === false ) && $r_saldo_terpotong !== "0" && $r_sisa_saldo !== ""){
        $status_trx = "SUKSES";
    } else if( ($r_status === '00' || $r_status === '05')  && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false){
        $status_trx = "PENDING";
        $r_status = "00";
    } else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
    }else if($r_status == '00') {
        $status_trx = "SUKSES";
    }else if($r_status !== '00') {
        $status_trx = "GAGAL";
    } else {
        $r_status = '00';
        $status_trx = "PENDING";
    }

    if($r_status === '68' && getIdBiller($r_idtrx) === '192'){
        //biller giga otomax
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
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
        "STATUS_TRX"        => (string) $status_trx,
    );
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
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

    cekMandatoryRequestJsonPulsaGame($data);

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    "KODE_PRODUK"       => (string) $kdproduk,
                    "WAKTU"             => (string) date('YmdHis'),
                    "NO_HP"             => (string) $nohp,
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "SN"                => (string) '',
                    "NOMINAL"           => (string) '',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) '',
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "STATUS_TRX"        => (string) 'GAGAL',
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end
    
    // global $pgsql;
    global $host;
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;        //FIELD_KETERANGAN

    $fm         = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA89065');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$nohp."*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp   = $respon[7];
        }
    } else {
        $respon = postValue_fmssweb2($fm);
        $resp   = $respon[7];
    }

    if($resp == 'null'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $r_kdproduk = $kdproduk;
        $r_tanggal = date('YmdHis');
        $r_nohp = $nohp;
        $r_idoutlet = $idoutlet;
        $status_trx = "PENDING";
        $r_saldo_terpotong = '';
        $r_sisa_saldo = '';
        $r_idtrx = '';
        $r_nominal = '';
        $r_sn = '';

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
            "STATUS_TRX"        => (string) $status_trx,
        );
    
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    
        return json_encode($params, JSON_PRETTY_PRINT);
    } 
    
    $format = FormatMsg::game();
    $frm    = new FormatGame($format[1], $resp);

    //print_r($frm->data);
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMid();
    // $r_nominal          = getNominalTransaksi($r_idtrx);
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
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
    }

    if($r_status === '00' && ($r_keterangan !== "" && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') === false ) && $r_saldo_terpotong !== "0" && $r_sisa_saldo !== ""){
        $status_trx = "SUKSES";
    } else if( ($r_status === '00' || $r_status === '05')  && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false){
        $status_trx = "PENDING";
        $r_status = "00";
    } else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
    }else if($r_status == '00') {
        $status_trx = "SUKSES";
    }else if($r_status !== '00') {
        $status_trx = "GAGAL";
    } else {
        $r_status = '00';
        $status_trx = "PENDING";
    }

    if($r_status === '68' && getIdBiller($r_idtrx) === '192'){
        //biller giga otomax
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
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
        "STATUS_TRX"        => (string) $status_trx,
    );

    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);

    return json_encode($params, JSON_PRETTY_PRINT);
}

function pulsa2($data) {

    $i          = -1;
    $kdproduk   = strtoupper($data->kode_produk);
    $nohp       = strtoupper($data->no_hp);
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $ref1       = strtoupper($data->ref1);
    $field      = 6;
    
    cekMandatoryRequestJsonPulsaGame($data);

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    "KODE_PRODUK"       => (string) $kdproduk,
                    "WAKTU"             => (string) date('YmdHis'),
                    "NO_HP"             => (string) $nohp,
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "SN"                => (string) '',
                    "NOMINAL"           => (string) '',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) '',
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "STATUS_TRX"        => (string) 'GAGAL',
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end

    // global $pgsql;
    global $host;

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;

    $msg[$i+=1] = "PULSA";
    $msg[$i+=1] = $kdproduk;      //KDPRODUK
    $msg[$i+=1] = $GLOBALS["mid"];     //MID
    // $msg[$i+=1] = '121212323434';     //MID
    $msg[$i+=1] = $stp;        //STEP
    $msg[$i+=1] = "";        //TANGGAL
    $msg[$i+=1] = $GLOBALS["via"];     //VIA
    // $msg[$i+=1] = 'MOBILE_SMART';     //VIA
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;        //FIELD_KETERANGAN

    $fm = convertFM($msg, "*");
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA89065', 'FA32670');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H******".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
            $respon = postValue_fmssweb3($fm);
            $resp = $respon[7];
      
    }

    if($resp == 'null'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $r_kdproduk = $kdproduk;
        $r_tanggal = date('YmdHis');
        $r_nohp = $nohp;
        $r_idoutlet = $idoutlet;
        $status_trx = "PENDING";
        $r_saldo_terpotong = '';
        $r_sisa_saldo = '';
        $r_idtrx = '';
        $r_nominal = '';
        $r_sn = '';

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
            "STATUS_TRX"        => (string) $status_trx,
        );
    
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    
        return json_encode($params, JSON_PRETTY_PRINT);
    } 
   
    // $resp = "PULSA*S10H*3133723466*9*20190108131453*H2H*082397522285*10000*HH82915*------**SK10**561115175*1234601971*999*EXT: FAILED   ";
    // $resp="PULSA*".$kdproduk."*805817646*10*20190108131453*H2H*".$nohp."**".$idoutlet."*".$pin."*------******"; 
    $format = FormatMsg::pulsa();
    $frm = new FormatPulsa($format[1], $resp);

    //print_r($frm->data);
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMid();
    // $r_nominal          = $frm->getNominal();
    // $nom_up             = getnominalup($r_idtrx);
    $r_saldo_terpotong  = $r_nominal;

    // if($r_status === ''){
    //     $r_status = '00';
    //     if($r_idtrx === ""){
    //         $r_idtrx = getIdTransaksi($nohp,$idoutlet,$kdproduk,$ref1);
    //     }
    // }

    if($r_status === '35' || $r_status === '68'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
    }

    if($r_status === '00' && ($r_keterangan !== "" && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') === false ) && $r_saldo_terpotong !== "0" && $r_sisa_saldo !== ""){
        $status_trx = "SUKSES";
    } else if( ($r_status === '00' || $r_status === '05')  && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false){
        $status_trx = "PENDING";
        $r_status = "00";
    } else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
    }else if($r_status == '00') {
        $status_trx = "SUKSES";
    }else if($r_status !== '00') {
        $status_trx = "GAGAL";
    } else {
        $r_status = '00';
        $status_trx = "PENDING";
    }

    if($r_status === '68' && getIdBiller($r_idtrx) === '192'){
        //biller giga otomax
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
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
        "STATUS_TRX"        => (string) $status_trx,
    );
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), "H2H");
    return json_encode($params, JSON_PRETTY_PRINT);
}

function game2($data) {
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    "KODE_PRODUK"       => (string) $kdproduk,
                    "WAKTU"             => (string) date('YmdHis'),
                    "NO_HP"             => (string) $nohp,
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "SN"                => (string) '',
                    "NOMINAL"           => (string) '',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) '',
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "STATUS_TRX"        => (string) 'GAGAL',
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }
    }
    // handle 504 end
    
    // global $pgsql;
    global $host;
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;        //FIELD_KETERANGAN

    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA89065');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp   = $respon[7];
        }
    } else {
        $respon = postValue_fmssweb3($fm);
        $resp   = $respon[7];
    }

    if($resp == 'null'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $r_kdproduk = $kdproduk;
        $r_tanggal = date('YmdHis');
        $r_nohp = $nohp;
        $r_idoutlet = $idoutlet;
        $status_trx = "PENDING";
        $r_saldo_terpotong = '';
        $r_sisa_saldo = '';
        $r_idtrx = '';
        $r_nominal = '';
        $r_sn = '';

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
            "STATUS_TRX"        => (string) $status_trx,
        );
    
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    
        return json_encode($params, JSON_PRETTY_PRINT);
    } 
    
    $format = FormatMsg::game();
    $frm    = new FormatGame($format[1], $resp);

    //print_r($frm->data);
    $r_step             = $frm->getStep()+1;
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
    $r_mid              = $frm->getMid();
    // $r_nominal          = getNominalTransaksi($r_idtrx);
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
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
    }

    if($r_status === '00' && ($r_keterangan !== "" && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') === false ) && $r_saldo_terpotong !== "0" && $r_sisa_saldo !== ""){
        $status_trx = "SUKSES";
    } else if( ($r_status === '00' || $r_status === '05')  && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false){
        $status_trx = "PENDING";
        $r_status = "00";
    } else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
            $status_trx = "PENDING";
    }else if($r_status == '00') {
        $status_trx = "SUKSES";
    }else if($r_status !== '00') {
        $status_trx = "GAGAL";
    } else {
        $r_status = '00';
        $status_trx = "PENDING";
    }

    if($r_status === '68' && getIdBiller($r_idtrx) === '192'){
        //biller giga otomax
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
        $status_trx = "PENDING";
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
        "STATUS_TRX"        => (string) $status_trx,
    );

    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), "H2H");

    return json_encode($params, JSON_PRETTY_PRINT);
}

function bpjs_inq($data){
    $i              = -1;
    $kdproduk       = strtoupper($data->kode_produk);
    $idpel1         = strtoupper($data->idpel);
    // $idpel2         = strtoupper($data->idpel1);
    $periodebulan   = strtoupper($data->periode);
    $idoutlet       = strtoupper($data->uid);
    $pin            = strtoupper($data->pin);
    $ref1           = strtoupper($data->ref1);
    $field          = 7;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }
    // $idpel = '';
    // if($idpel1 != ''){
    //     $idpel = $idpel1;
    // }elseif($idpel2 != ''){
    //     $idpel = $idpel2;
    // }elseif($idpel2 == '' && $idpel1 == ''){
    //     return json_encode(array('error'=>'Idpelanggan tidak boleh kosong'));
    // }


    // if($periodebulan == ''){
    //     return json_encode(array('error'=>'Periode tidak boleh kosong'));
    // }
    // global $pgsql;
    global $host;
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if($idoutlet != 'SP300203'){
        if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
            if (!isValidIP($idoutlet, $ip)) {
                return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
            }
        }
    }

    if($kdproduk == 'ASRBPJSKS'){
        $kdproduk = 'ASRBPJSKSH';
    }

    if(substr($idpel1, 0, 5) != "88888"){
        $idpel1 = "88888".substr($idpel1, 2, 11);
    }
    
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];

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
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];

    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $list       = $GLOBALS["sndr"];

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA32670');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        if($periodebulan == "" || $periodebulan > 12 || !is_numeric($periodebulan)){
            $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "****0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*Periode bulan harus diisi min 1 dan maks 12";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    }

    //die($resp);
    //$resp = "TAGIHAN*ASRBPJSKS*789644896*11*20160107130731*WEB*8888801851523593***000000064986*5000*".$idoutlet."*".$pin."**1497746*3*15*080002*432635265*00*EXT: APPROVE*0605 *LUBUK LINGGAU *8888801851523593*2***DJASILAH **000000064986*******BPJS Kesehatan***750**000000000000*0***";
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
    $r_mid          = $frm->getMID();
    $r_step         = $frm->getStep()+1;
    $nom_up             = getnominalup($r_idtrx);
    $r_saldo_terpotong  = $r_nominal + $r_nominaladmin + ($nom_up);

    $url_struk = "";
    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }

    $params = array(
        "KODE_PRODUK"       => (string) 'ASRBPJSKS',
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

    // $get_step = get_step_from_mid($r_mid) + 1;
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), "H2H");
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
    
    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('JSON PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
            if($selisih >= $GLOBALS["__G_selisih"]){
                $params = array(
                    "KODE_PRODUK"       => (string) $kdproduk,
                    "WAKTU"             => (string) date('YmdHis'),
                    "IDPEL1"            => (string) $idpel1,
                    "IDPEL2"            => (string) '',
                    "IDPEL3"            => (string) '',
                    "NAMA_PELANGGAN"    => (string) '',
                    "PERIODE"           => (string) '',
                    "NOMINAL"           => (string) $nominal,
                    "ADMIN"             => (string) '',
                    "UID"               => (string) $idoutlet,
                    "PIN"               => (string) '------',
                    "REF1"              => (string) $ref1,
                    "REF2"              => (string) $ref2,
                    "REF3"              => (string) '',
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  JSON (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'JSON',strtoupper("JSON ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return json_encode($params, JSON_PRETTY_PRINT);
            }
        }   
    }
    // handle 504 end
    
    // global $pgsql;
    global $host;

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if($idoutlet != 'SP300203'){
        if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
            if (!isValidIP($idoutlet, $ip)) {
                return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
            }
        }
    }

    if($kdproduk == 'ASRBPJSKS'){
        $kdproduk = 'ASRBPJSKSH';
    }

    if(substr($idpel1, 0, 5) != "88888"){
        $idpel1 = "88888".substr($idpel1, 2, 11);
    }

    $mti        = "BAYAR";
    $ceknom     = getNominalTransaksi($ref2);
    $arr        = array('10', '11', '12','13','14');
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN

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
     
    if ($ceknom != $nominal) {
        $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "***$nominal*0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI";
    } else if(substr($hp, 0, 1) != '0' || !is_numeric($hp) || !in_array(strlen($hp), $arr) || $hp == ''){
        $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "***$nominal*0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*Isi No Hp dengan benar.";
    } else {      
        $respon = postValue($fm);
        $resp   = $respon[7];
    }

    if($resp == 'null'){
        $params = array(
            "KODE_PRODUK"       => (string) $kdproduk,
            "WAKTU"             => date('YmdHis'),
            "IDPEL1"            => (string) $idpel1 ,
            "IDPEL2"            => (string) '' ,
            "IDPEL3"            => (string) '' ,
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
    $r_mid              = $frm->getMID();
    $r_step             = $frm->getStep()+1;
    $r_nama_pelanggan   = getNamaPelanggan($kdproduk, $frm);

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

    if($r_status === '35' || $r_status === '68'){
        $r_status = '00';
        $r_keterangan = "SEDANG DIPROSES";
    }

    if($r_status === '00' && ($r_keterangan !== "" && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') === false ) && $r_saldo_terpotong !== "0" && $r_sisa_saldo !== ""){
        $r_keterangan = "SUKSES";
    } else if( ($r_status === '00' || $r_status === '05')  && strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false){
        $r_keterangan = "SEDANG DIPROSES";
        $r_status = "00";
    } else if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "00";
            $r_keterangan = "SEDANG DIPROSES";
    }

    $params = array(
        "KODE_PRODUK"       => (string) 'ASRBPJSKS',
        "WAKTU"             => (string) $r_tanggal,
        "IDPEL1"            => (string) $r_idpel1,
        "IDPEL2"            => (string) $r_idpel2,
        "IDPEL3"            => (string) $r_idpel3,
        "NAMA_PELANGGAN"    => (string) $r_nama_pelanggan,
        "PERIODE"           => (string) $periodebulan,
        "NOMINAL"           => (string) $r_nominal,
        "ADMIN"             => (string) $r_nominaladmin,
        "UID"               => (string) $r_idoutlet,
        "PIN"               => (string) '------',
        "REF1"              => (string) $ref1,
        "REF2"              => (string) $r_idtrx,
        "REF3"              => (string) '',
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

    // $get_step = get_step_from_mid($r_mid) + 1;
    writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);

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

    // if ($ip <> "") {
    //     if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130") {
    //         die("Anda Tidak Mempunyai Hak Akses [" . $ip . "].");
    //     }
    // }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($idoutlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

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
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    // echo $msg."<br>";
    $list       = $GLOBALS["sndr"];
    // if($list[strtoupper($idoutlet)] && in_array($GLOBALS["host"],$list[strtoupper($idoutlet)])){
    $respon     = postValue($fm);


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
        $url_struk  = "http://34.101.201.189/strukmitra/?id=" . $url;
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130"  ) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    // global $pgsql;
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
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
        $url_struk  = "http://34.101.201.189/strukmitra/?id=" . $url;
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130"  ) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    // global $pgsql;
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
    }

    if($ref2 == ""){
        $ref2 = getRef2($ref1,$idoutlet);
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
    $msg[$i+=1] = strtoupper("JSON ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];           //KETERANGAN
    $fm         = convertFM($msg, "*");
    
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list       = $GLOBALS["sndr"];
    //if($list[strtoupper($idoutlet)] && in_array($GLOBALS["host"],$list[strtoupper($idoutlet)])){
    $respon     = postValue($fm);
    // //print_r($respon);
    $resp       = $respon[7];



    writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);
    
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
                "INFOTEXT"          => (string) '',
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
                "INFOTEXT"          => (string) '',
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
        $url_struk  = array("url_struk" => "http://34.101.201.189/strukmitra/?id=" . $url);
        $merge      = array_merge($params,$adddata,$adddata2,$adddata3,$url_struk);
    } else {
        $merge      = array_merge($params,$adddata,$adddata2,$adddata3);
    }

    return json_encode($merge);
}

function cetak_ulang_detail3($data) {
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130"  ) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    // global $pgsql;
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
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
    $respon     = postValue($fm);
    // //print_r($respon);
    $resp       = $respon[7];



    writeLog($GLOBALS["mid"], $stp + 1, "CORE", "MP", $resp, $GLOBALS["via"]);
   
       if($resp == 'null'){
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
        $url_struk  = array("url_struk" => "http://34.101.201.189/strukmitra/?id=" . $url);
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

      $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

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
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon     = postValue($fm);
    $params     = $respon;
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

      $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }
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
    writeLog($GLOBALS["mid"], $step, $sender, $receiver, $fm, $GLOBALS["via"]);
    $respon     = postValue($fm);
    $params     = $respon;
//    $params = $msg;
    return json_encode($params, JSON_PRETTY_PRINT);
    // return new xmlrpcresp(php_xmlrpc_encode($params));
}

function cek_ip() {
    $output = [
        "IP"    => getClientIP()//$_SERVER['REMOTE_ADDR']
    ];
    return json_encode($output, JSON_PRETTY_PRINT);
}

function info_produk($data){
    $i          = -1;

    $id_produk  = strtoupper($data->kode_produk);
    $id_outlet  = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);
    $field      = 4;

    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($id_outlet, $ip)) {
            return json_encode(array('error'=>"IP Anda [$ip] tidak punya hak akses"));
        }
    }

    // UID
    // PIN
    // RC
    // KETERANGAN
    // HARGA
    // ADMIN
    // KOMISI
    // PRODUK
    // STATUSPRODUK
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
        $status_produk  = status_produk($id_produk); //harga_jual|is_gangguan|biaya_admin|is_active;
        $status_produk_komisi  = status_produk_komisi($id_produk); //harga_jual|is_gangguan|biaya_admin|is_active;

        $komisi_produk  = komisi_produk($id_outlet, $id_produk); //up_harga|fee_transaksi;
        $status_produk  = explode('|', $status_produk);
        $komisi_produk  = explode('|', $komisi_produk);
        $harga_jual     = $status_produk[0];
        
        if($status_produk[3] === '1' && $status_produk[1] === '0' ){
            $status = "AKTIF";
        }elseif($status_produk[3] === '1' && $status_produk[1] != '0' ){
            $status = "GANGGUAN";
        }elseif($status_produk[3] === '0' && $status_produk[1] != '0' ){
            $status = "CLOSE";
        }
        $admin      = $status_produk[2];
        if($admin != 0)
        {
            $admin      = $status_produk[2];
        }else{
            $admin = $status_produk_komisi;
        }
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

    return '{"keterangan":"Sukses","status":"00","result":[{"kode":"51","nama":"BALI"},{"kode":"19","nama":"BANGKA BELITUNG"},{"kode":"36","nama":"BANTEN"},{"kode":"17","nama":"BENGKULU"},{"kode":"11","nama":"DI ACEH"},{"kode":"34","nama":"DI YOGYAKARTA"},{"kode":"31","nama":"DKI JAKARTA "},{"kode":"75","nama":"GORONTALO"},{"kode":"15","nama":"JAMBI"},{"kode":"32","nama":"JAWA BARAT"},{"kode":"33","nama":"JAWA TENGAH"},{"kode":"35","nama":"JAWA TIMUR"},{"kode":"61","nama":"KALIMANTAN BARAT"},{"kode":"63","nama":"KALIMANTAN SELATAN"},{"kode":"62","nama":"KALIMANTAN TENGAH"},{"kode":"64","nama":"KALIMANTAN TIMUR"},{"kode":"65","nama":"KALIMANTAN UTARA"},{"kode":"21","nama":"KEPULAUAN RIAU"},{"kode":"18","nama":"LAMPUNG"},{"kode":"81","nama":"MALUKU"},{"kode":"86","nama":"MALUKU UTARA"},{"kode":"52","nama":"NTB"},{"kode":"53","nama":"NTT"},{"kode":"82","nama":"PAPUA"},{"kode":"92","nama":"PAPUA BARAT"},{"kode":"14","nama":"RIAU"},{"kode":"76","nama":"SULAWESI BARAT"},{"kode":"73","nama":"SULAWESI SELATAN"},{"kode":"72","nama":"SULAWESI TENGAH"},{"kode":"74","nama":"SULAWESI TENGGARA"},{"kode":"71","nama":"SULAWESI UTARA"},{"kode":"13","nama":"SUMATERA BARAT"},{"kode":"16","nama":"SUMATERA SELATAN"},{"kode":"12","nama":"SUMATERA UTARA"}]}';
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
    $fm = generateBpsjTkAst($data, $stp, "BPJS_KABUPATEN");
    // echo $fm;die();

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    
    $respon = postValueBpjsTk($fm, false);
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
    $fm = generateBpsjTkAst($data, $stp, "BPJS_KANTOR_CABANG");
    // echo $fm;die('fasfa');

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, false);
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
    $fm = generateBpsjTkAst((object)$data, $stp, "HITUNG_IURAN");

    // echo $fm;die();

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, false);
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
    $fm = generateBpsjTkAst((object)$data, $stp, $cmd);

    // echo $fm;die();

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, false);
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
    $fm = generateBpsjTkAst((object)$data, $stp, $cmd);

    // echo $fm;die();

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, false);
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
    $fm = generateBpsjTkAst((object)$data, $stp, "BPJS_VER");

    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    $respon = postValueBpjsTk($fm, false);
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
    
    $cektrx = cek_trx_bpjstk($ref2, $uid, true); 
    // print_r($cektrx);die();
   
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
    $fm = generateBpsjTkAst((object)$param_request, $stp, $cmd);
    // echo $fm;die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);

    if($nominal != $cektrx['nominal']){
        $hasil = "HASIL.".$nominal.' --- '.$cektrx['nominal'].'#'.$uid.'#'.$ref2;
        logDana($hasil);
        $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "***" . $nominal . "*0*" . $uid . "**------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
    }else{
        $respon = postValueBpjsTk($fm, false);
        $resp = $respon[7];
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
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
    }

    if ($frm->getStatus() <> "00") {
        $r_saldo_terpotong = 0;
    }

    $nama = $frm->getCustomerName();
    if($nama == ""){
        $nama = getGlobal($r_idtrx, 'bill_info4');
    }
    
    $params = array(
        "KODE_PRODUK"       => (string) $r_kdproduk,
        "WAKTU"             => (string) $r_tanggal,
        "NIK"               => (string) $r_idpel1,
        "NAMA_PELANGGAN"    => (string) $nama,
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
    $fm = generateBpsjTkAst($data, $stp, $cmd);
    // echo $fm;die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
   
    $respon = postValueBpjsTk($fm, false);
    $resp = $respon[7];
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

function generateBpsjTkAst($data, $stp, $FIELD_CMD){
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
        if ($FIELD_CMD == "BPJS_INQ_BY_NIK") { //2
            // return 'BPJSADM*ASRBPJSTK*1644399296511097373*6*20220209163501*WEB*3515094703960002****0*FA9919*414455******1644399296511097373*00*SUKSES INQ IURAN BPU*BPJS_INQ_BY_NIK*3515094703960002*MARETTA IDFIANI*07-03-1996****BLTH=05-2022#PROG_JKK=20000:1.0:0:30:1:92#PROG_JKM=6800:0.0:0:30:1:92#BIAYA_TRANSAKSI=0#BIAYA_REGISTRASI=0#TOTAL=26800#UPAH=2000000#DASAR_UPAH=2000000#UMP=0***26800*0**IT*08:00*17:00*Sidoarjo**3515*2000000********20000:1.0:0:30:1:92**6800:0.0:0:30:1:92****0*N11*JL. PAHLAWAN PINANG INDAH BLOK A2 NO. 1-4 SIDOARJO 61251 P.O. BOX 210, SIDOARJO 61251, TELP: 031-8945592 s.d 94, FAKS: 031-8945591******05-2022*05-2022*05-2022***';
        } else if ($FIELD_CMD == "GET_DATA_PESERTA") { //1
            // return 'BPJSADM*ASRBPJSTK*1644398072516682653*6*20220209161432*WEB*****0*FA9919*414455*******00*ANDA SUDAH TERDAFTAR, SILAHKAN MELAKUKAN PEMBAYARAN*GET_DATA_PESERTA*3515094703960002*MARETTA IDFIANI*07-03-1996*********22770000002*IT*08:00*17:00*Sidoarjo**3515*2000000***************N11**********0**';
        } else if($FIELD_CMD == 'BPJS_DAFTAR'){
            // return "BPJSADM*ASRBPJSTK*1647248999811175203*9*20220314161000*WEB*****0*FA9919*414455*******00*PENDAFTARAN BERHASIL*BPJS_DAFTAR*3172050907600001*FARIS PRABOWO*09-07-1960***ini alamat*******P999*07:00*17:00*ini alamat*sapi@gmail.com*3578*****085648889293**********0#0*N13**3578**3*1*******";
        }
    } else if ($FIELD_CMD == "INQ_BPJS") { //3
        $mti = "BPJSINQ";
        // return 'BPJSINQ*ASRBPJSTK*1644398339221254283*6*09-02-2022 16:19:04*WEB*3515094703960002****-523*FA9919*414455**6109820*1***117362*00*SUKSES INQ IURAN BPU*INQ_BPJS*3515094703960002*MARETTA IDFIANI*07-03-1996****BLTH=05-2022#PROG_JKK=20000:1.0:0:30:1:92#PROG_JKM=6800:0.0:0:30:1:92#BIAYA_TRANSAKSI=0#BIAYA_REGISTRASI=0#TOTAL=26800#UPAH=2000000#DASAR_UPAH=2000000#UMP=0***26800*0**IT*08:00*17:00*Sidoarjo**3515*2000000********20000:1.0:0:30:1:92**6800:0.0:0:30:1:92****0*N11*******05-2022*05-2022*05-2022***';
    } 
    else if ($FIELD_CMD == "INQ_BPJS_PU_EPS" || $FIELD_CMD == "INQ_BPJS_PU_VA") { 
        $mti = "BPJSINQ";
        // return  "BPJSINQ*ASRBPJSTK*1647512088890252983*9*20220317171455*WEB*331704010906***43000*0*FA9919*414455**862476*1***120495*00*SUKSES INQ IURAN PU*INQ_BPJS_PU_EPS*331704010906******0#0#15060352#000***********LUCASTA MURNI CEMERLANG*******39278**1653**2069**********04/2022*****220300001169**";
    } 
    else if ($FIELD_CMD == "PAY_BPJS") { //4
        $mti = "BPJSPAY";
        // return 'BPJSPAY*ASRBPJSTK*1645173650162797043*9*20220218154052*WEB****26800*-562*FA9919*414455**6083020*1***117828*00*SUKSES PAY IURAN BPU*PAY_BPJS*3515094703960002*MARETTA IDFIANI*07-03-1996*922023114719*********IT*00:00*23:59*Sidoarjo**3515********JKK=0#JKM=0**JKK=0#JKM=0**JKK=0#JKM=0***0#0*N11*JL. PAHLAWAN PINANG INDAH BLOK A2 NO. 1-4 SIDOARJO 61251 P.O. BOX 210******JKK=1#JKM=1*JKK=1#JKM=1*JKK=1#JKM=1*JKK_AKTIF=26-01-2022 10:36:55#JKK_EFEKTIF=26-01-2022 10:36:55#JKK_EXPIRED=25-05-2022 00:00:00#JKK_GRACE=25-08-2022 00:00:00#JKM_AKTIF=26-01-2022 10:36:55#JKM_EFEKTIF=26-01-2022 10:36:55#JKM_EXPIRED=25-05-2022 00:00:00#JKM_GRACE=25-08-2022 00:00:00*JKK=20000.00#JKM=6800.00*';
    } 
    else if ($FIELD_CMD == "PAY_PU_EPS_BPJS" || $FIELD_CMD == "PAY_PU_VA_BPJS") { 
        $mti = "BPJSPAY";
        // return "BPJSPAY*ASRBPJSTK*1647513289411733903*7*20220317173450*WEB*331704010906***43000*0*FA9919*414455**819476*1***120498*00*SUCCESS*PAY_PU_EPS_BPJS****220300001169***Sukses*17-03-2022 17:34:49**************************************";
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