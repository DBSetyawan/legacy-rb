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

$mid = getNextMID();
// $mid        = 1; //buat local
$step       = 1;
$raw_msg    = file_get_contents('php://input');
$raw        = json_decode($raw_msg);

$method = $raw->request->head->function;
$datarequest  = $raw->request;
$signaturekey = $raw->signature;

$raw_cpy             = $raw_msg;
$raw_cpy_decode      = json_decode($raw_cpy);
$raw_cpy_decode->pin = '------';
$msg_log             = json_encode($raw_cpy_decode);
$sender              = "JSON DANA";
$receiver           = $_SERVER['SERVER_ADDR']."-RB-JSON-".$_SERVER['HTTP_HOST']."-".$_SERVER['SERVER_NAME'];
$via                 = $GLOBALS["__G_via"];

logDana($msg);


$cekKey = verify($datarequest, $signaturekey);
if($cekKey != 1)
{
    $result = array(
        "response" => "Key Tidak Valid"
    );
    print_r(json_encode($result));die();
}

switch ($method) {
    case "alipayplus.digital.goods.destination.inquiry": //DONE
        echo inq($raw);
        break;
    case "alipayplus.digital.goods.order.create": //DONE
        echo pay($raw);
        break;
     case "alipayplus.digital.goods.order.query": //DONE
        echo cekstatus($raw);
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

function sign($data)
{
    $signature = '';
    // PUNYANYA BMS
    $privateKeyContent = <<<EOD
-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCtD0B+sHjFqZF2
0WSi8ycfQshKaJfNl/3v0ath909Bu1GEaoiBFW4xz3Lo3G5MhjZuAXbsziRHKpK4
V2mkldO2hXOhO9GmC3jRtkpHkaMobvP8vUJbRTWIPCkXZBU2FYY81EQb8u5+WJse
vnFkbPeZoonWSQAO/wL00Ki+KbKwFvsfwl8+WdpR+yz+Qq6zHl0ltdqTmjXyJNd1
/Bgs8GNahOCDPu3y9qsy5aAsUVWlBconRQx3ChV6UxK4xuFsCXXoiot+6nmRoKin
54R3RtDC5isMhuEqaRsv7Z78uCVjjf9I32I1dPmMUX8yDvc0391gG6PAqi/bXo+U
BswtMIqPAgMBAAECggEAavWHPgXhzwDbh90o3tF7d4W19s8oK4hqCSPEUdshIBYe
7sFNNsLBBYYaljNO9Hrq/xhmoTtTDq0QW2CjSXbUj/VxHtCy5XYnqS2KQSuQ9LeG
ksmCTpi62kWce/l+ZpvtCIGEyuVdY7dtwBWiTZhPe6QKnuclYx9Xe2nPMSDicJPu
/DDoRrIYr75JbWG9w753QVbHx/dX0j0Hex7hOJ+qemRc5q3bhTGHT8uOl2X7MziA
rHyGn0UlUSaIYPb9p36Xp+V3YSnrbL8RaklmRHXXMNcaofRpvo8bPGZusFq8B9ii
Qze1clbg0MfB6b0MJrFxDFo0kXOLYYSP+jG7mb4GmQKBgQDd5gXb9GojsgoHNXjo
HqYT6yoSgwnbC40UsA3n/jkuKqU92SnhYGjrtolfUo8+laPi05hI6v6Vl4DummQS
XATQVgMtou8EvVd8hT6wikNPAMmcHm5zSpsd4el24ZVNMY7XYN6ezP0ADx71aIb8
LGpLb2MH2VYB2/a2+dzFlW0N3QKBgQDHp8wR+46gUl/OmWK0cBoYk7R4YZG3G+Ei
RuEcCwnurA3T9JYbQqQEZ9VKByCXpLfaUEk1Dyv2dsCpkRUvu7/P+/ST/Ob+f4Qe
tYK7gWR8gHluR40leFmhIkuONDflCmV6tu0/poF5DU0D+QCj75eBQZsErqRllWDS
O6QW1s3BWwKBgQCo+kjN9jN5mVP/p126TdSk6Hkyfa1crFA0R9lfKH58xKbvEVfO
NC/SCEBWYbWJsESMMBwQsztJyYS6rsG0JXTY/hcPgrtHvbRIs+NIZxHYsL3W8Gml
zFORjv7Ns/2ROkMPisoc3fdwOxOVSJM6p8wOQ/WC/aN8aPhY4K+Zmy/f1QKBgAaW
IINPWysqzIJSSRFOyW2aIc/+2AHEZ67ry61TJ+a5wlMFtJX9os+KZVzl00ttYatQ
DrozX+3niP+PC7XhabiAxVbEdxJaPo+MyV1KLXh1/IuIzL05tSs9qGRukJF7wFFG
C5mX8pl9uNaytjSySLs44NZMtJutS95jnUwrleoVAoGAGk4SGd7lxtqA6h+8n5ha
Y4WqC4MKJicWx5SpWLKGMaLlmoiKXUDFEYq0c0CcDM09JvXU+aJy/9hNq3uU4O/+
JDJN/znSJgLnBIFiUfrq83WZKn34mWphNtOuMa4txtTwyYiDVooqikQCDzNpDaer
QjJwVNoQvNqxt8ZKApaUvN4=
-----END PRIVATE KEY-----
EOD;
    openssl_sign(json_encode($data), $signature, $privateKeyContent, OPENSSL_ALGO_SHA256);
    return base64_encode($signature);
}

function verify($data, $signature)
{
    // PUNYANYA DANA
   $publicKeyContent = <<< EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlaaeesdlglRlNJIxPzS7
Zl5V45HYRQdigVJuzvNoyLfIkX386uXizl1sPEyozNrWbYgulZIaRRi2yhTBvLzR
hz2SjFupz3xRDISskZdpx+FsFMb4nbVaa6iS5UB7CbYFU909YdaYQuXgMTUCIkHt
i6CyIhS2YwGX3oEZsrdCW6sQwCB0Kfjlr/RVb89gwVxKmgOyj8ZQiTBdBV6AEbfa
wb7jmMC22yIrU98p/s2g7FGIg8gD++eIkFdzEAKpRaL4214/W4FvnDy0iVuCUUd+
KXMtotbcgC2WLBZ3uEx0RzF51JAaMIA2uf7HEGwsaGm4CCpLdyvrGTOCSXOFXtk5
mwIDAQAB
-----END PUBLIC KEY-----
EOD;
    $binarySignature = base64_decode($signature);
    return openssl_verify(json_encode($data), $binarySignature,  $publicKeyContent, OPENSSL_ALGO_SHA256);
}


function inq($data) {
    $i = -1;
    $kdproduk   = strtoupper($data->request->body->productId);
    $idpel1     = $data->request->body->destinationInfos[0]->primaryParam;
    $ref1       = $data->request->head->reqMsgId;
    $uid        = "HH122973";
    $pin        = "967733";
    $version    = $data->request->head->version;
    $function   = $data->request->head->function;

    $signature  = sign($data->request);
    $field      = 8;
    

    if(substr(strtoupper($kdproduk), 0,2) == "KK"){
        $nominal    = strtoupper($data->request->body->destinationInfos[0]->billAmount->value);
        $field      = 9;
    }
    

    global $pgsql;
    if ($kdproduk == "PLNPASC") {
        $kdproduk = "PLNPASCH";
    } else if ($kdproduk == "PLNPRA") {
        $kdproduk = "PLNPRAH";
    } else if ($kdproduk == "HPTSEL") {
        $kdproduk = "HPTSELH";
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
    $msg[$i+=1] = "";
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
    $msg[$i+=1] = ";;;;";
    $fm = convertFM($msg, "*");

    // echo $fm;die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);    

    // $respon = postValueWithTimeOut($fm);
    // $resp = $respon[7];
    $nowdate = date('YmdHis');
    if($idpel1 == "5426406680109844"){
        $resp = "TAGIHAN*KKBNI*4644090459*8*20210419091909*H2H*5426406680109844***".$nominal."*7500*".$idoutlet."*------*------*17758*1*46*046*2100752005*33*EXT: Unknown error!*0000000*00*0000003602700368*01***********KARTU KREDIT BANK BNI*******    ";
    }elseif($idpel1 == "5426406707511188"){
        $resp = "TAGIHAN*KKBNI*4646547103*8*20210420123130*H2H*5426406707511188***".$nominal."*3000*".$idoutlet."*------**1768702554*1**BNI*2102647957*34*EXT: Transaksi tidak dapat dilakukan. (RC 12)*319525406*00*0986272790*01***********KARTU KREDIT BANK BNI***".$nominal."**** ";
    }elseif($idpel1 == "5426408267834869"){
        $resp = "TAGIHAN*KKBNI*4645708491*8*20210420020533*H2H*5426408267834869***".$nominal."*7500*".$idoutlet."*------**864827717*1*14*014*2101979463*37*EXT: Invalid credit card number 0540934584!*319407311*00*0540934584*01***********KARTU KREDIT BANK BNI***".$nominal."****  ";
    }elseif($idpel1 == "5426400903052147"){
        $resp = "TAGIHAN*KKBNI*4643765538*8*20210419052326*H2H*5426400903052147***".$nominal."*3000*".$idoutlet."*------*------*1389350*1**BNI*2100519549*00*Sukses!*0000000*00*5426400903052147*01***APRIANTO TRI A********KARTU KREDIT BANK BNI*18042021*08052021*****    ";
    }elseif($idpel1 == "5227870000093825"){
        $resp = "TAGIHAN*KKBNI*4646190592*8*20210420095819*H2H*5227870000093825***".$nominal."*3000*".$idoutlet."*------*------*6580*2**BNI*2102373187*00*Sukses!*0000000*00*5227870000093825*01***YOSITA M DASMASELA********KARTU KREDIT BANK BNI*21032021*10042021****0* ";
    }else{
        $resp = "TAGIHAN*KKBNI*4644849788*8*20210419151013*H2H*5484150923015801***".$nominal."*3000*".$idoutlet."*------**1504918576*1**BNI*2101295551*00*Sukses!*319292316*00*5484150923015801*01***BONDAN NUGROHO********KARTU KREDIT BANK BNI*13042021*03052021*1007735****";
    }
    // $resp = "";

    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    //$r_via                = $frm->getVia();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
    $params = array(
        $_SERVER['HTTP_X_FORWARDED_FOR'],
        'ANDA TIDAK DIIJINKAN MELAKUKAN INQUIRY'
    );

    $man = FormatMsg::mandatoryPayment();
    if(substr($kdproduk, 0, 2) == "KK") {
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["inq"] . "*" . $format, $resp);
        $params = retKartuKredit($params, $frm);
    }
    $periode = array(
        trim($frm->getLastPaidPeriode()),trim($frm->getLastPaidDueDate())
    );
    $tot_nom_admin =($r_nominal+$r_nominaladmin);
    if($r_status == "00")
    {
        $r_status = "10";
        $r_keterangan = "SUCCESS";
    }elseif($r_status == "37" || $r_status=="36"|| $r_status=="14"|| $r_status=="76")
    {
        $r_status = "20";
        $r_keterangan = "FAILED";
        $tot_nom_admin = 0 ;
        $r_nominal = 0;
        $r_nominaladmin = 0;
    }elseif($r_status == "34")
    {
        $r_status = "27";
        $r_keterangan = "FAILED";
        $tot_nom_admin = 0 ;
        $r_nominal = 0;
        $r_nominaladmin = 0;
    }elseif($r_status == "33")
    {
        $r_status = "28";
        $r_keterangan = "FAILED";
        $tot_nom_admin = 0 ;
        $r_nominal = 0;
        $r_nominaladmin = 0;
    }

    $data_inquiry = array(
        "inquiryId"     => $r_idtrx,
        "inquiryStatus" => 
                array(
                    "code"      => $r_status,
                    "status"    => $r_keterangan,
                    "message"   => $r_keterangan
                ),
        "destinationInfo"=> 
                array(
                    "primaryParam"      => $r_idpel1,
                    "secondaryParam"    => "",
                ),
        "customerName"   => trim($frm->getCustomerName()),
        "periode"        => $periode,
        "totalAmount"    => 
                array(
                    "value"     => $tot_nom_admin,
                    "currency"  => "IDR",
                ), 
        "baseAmount"    => 
                array(
                    "value"     => $r_nominal,
                    "currency"  => "IDR",
                ),
        "adminFee"    => 
                array(
                    "value"     => $r_nominaladmin,
                    "currency"  => "IDR",
                ),
        "providerName"   => trim($frm->getPTName()),
        "fineAmount"     => "",
        "dueDate"        => "",
        "quantity"       => "",
        "fare"           => "",
        "totalEnergy"    => "",
        "meterNumber"    => "",
        "minimumPayAmount"  => 
                array(
                    "value"     => trim($frm->getMinimumPayAmount()),
                    "currency"  => "IDR",
                ), 
        "maximumPayAmount"  => 
                array(
                    "value"     => trim($frm->getMaximumPayAmount()),
                    "currency"  => "IDR",
                ),
    );
    $result = array(
        "response" => 
            array(
            "head"=> 
                array(
                    'version'=> $version,
                    'function' => $function,
                    'reqTime' => gmdate('Y-m-d H:i:s',strtotime($r_tanggal)),
                    'reqMsgId' => $ref1
                ),
            "body"=>
                array(
                    "inquiryResults" => array($data_inquiry)
                )
            ),
        "signature" => $signature
           
    );
   return json_encode(array_change_key_case($result, CASE_LOWER));
    
}

function pay($data) {
//BAYAR*KDPRODUK*MID*STEP*TANGGAL*VIA*IDPEL1*IDPEL2*IDPEL3*NOMINAL*NOMINALADMIN*IDOUTLET*PIN*TOKEN*SALDO*JENISSTRUK*KODEBANK*KODEPRODUKBILLER*IDTRX*STATUS*KETERANGAN
    $i = -1;
    $kdproduk  = strtoupper($data->request->body->productId);
    $idpel1     = $data->request->body->destinationInfo->primaryParam;
    $ref1       = $data->request->body->requestId;
    $idoutlet   = "HH122973";
    $pin        = "967733";
    $nominal    = $data->request->body->danaSellingPrice->value;
    $ref2       = $data->request->body->extendInfo->inquiryId;

    $ref1_inquiry = $data->request->head->reqMsgId;
    $version    = $data->request->head->version;
    $function   = $data->request->head->function;
    $field      = 8;
    $signature  = sign($data->request);
    


    // if(count((array)$data) !== $field){
    //     return json_encode(array('error'=>'missing parameter request payment'));
    // }

    global $pgsql;
    // if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //    return json_encode(array('error'=>'access not allowed'));
    // }
//     if($kdproduk == "ASRJWS"){
//      $kdproduk = "ASRJWSI";
//  } else
    if ($kdproduk == "PLNPASC") {
        $kdproduk = "PLNPASCH";
    } else if ($kdproduk == "PLNPRA") {
        $kdproduk = "PLNPRAH";
    } else if ($kdproduk == "HPTSEL") {
        $kdproduk = "HPTSELH";
    }



    $mti = "BAYAR";
//  if(in_array($kdproduk,KodeProduk::getAsuransi()) || in_array($kdproduk,KodeProduk::getKartuKredit())){
//      $mti = "TAGIHAN";
//  }

    $ceknom = cekIsNominalTransaksi($ref2);

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
    $msg[$i+=1] = ";;;;";   // KETERANGAN


    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>"; die();
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $fm, $GLOBALS["via"]);
    
    if($idpel1 == "5227870000093825")
    {
        $resp = "BAYAR*KKBNI*4646191366*3*20210420095835*H2H*5227870000093825***".$nominal."*3000*".$idoutlet."*------*------*6580*2**BNI*2102373906*06*SALDO ANDA TIDAK MENCUKUPI*0000000*00*5227870000093825*01***YOSITA M DASMASELA********KARTU KREDIT BANK BNI*21032021*10042021****0*    ";
    }elseif($idpel1 == "5426400903052147"){

        $resp = "BAYAR*KKBNI*4643765602*8*20210419052337*H2H*5426400903052147***".$nominal."*3000*".$idoutlet."*------*------*189029*1**BNI*2100519599*00*Sedang Diproses*0000000*00*5426400903052147*01***APRIANTO TRI A********KARTU KREDIT BANK BNI*18042021*08052021****59900*   ";

    }else{
        $resp = "BAYAR*KKBNI*4644850487*8*20210419151036*H2H*5484150923015801***".$nominal."*3000*".$idoutlet."*------**1503823041*1**BNI*2101296064*00*Sukses!*319292316*00*5484150923015801*01***BONDAN NUGROHO********KARTU KREDIT BANK BNI*13042021*03052021*1007735***50400*  * ";
    }
    $man = FormatMsg::mandatoryPayment();
    $frm = new FormatMandatory($man["inq"], $resp);
    
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_idoutlet = $frm->getMember();
    $r_pin = $frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    $r_status = $frm->getStatus();
    $r_keterangan = $frm->getKeterangan();
  

    $man = FormatMsg::mandatoryPayment();
    if(substr($kdproduk, 0, 2) == "KK") {
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["pay"] . "*" . $format, $resp);
        // $params = retKartuKredit($params, $frm);
        $reffnumber = $frm->getBillerRefNumber();
    }

     if($r_status == "00")
    {
        $r_status = "10";
        $r_keterangan = "SUCCESS";
    }

    if($frm->getStatus() == "35"){
        $id_biller = getIdBiller($r_idtrx);
        if($id_biller == '26' && strpos(strtolower($r_keterangan), 'sedang diproses') !== false ){ 
            // SERVINDO
            $r_status = "20";
            $r_keterangan = "PENDING";
        }
    }
    if($frm->getStatus() == "35" 
            || $frm->getStatus() == "68" 
            || strpos(strtoupper($r_keterangan), 'SEDANG DIPROSES') !== false 
            || $frm->getStatus() == "05"
            || $frm->getStatus() == ""){
            $r_status = "20";
            $r_keterangan = "PENDING";
    }


    $resp_inquiry = array(
        "inquiryId"     => $r_idtrx,
        "orderId" => $ref1,
        "createdTime" => date("Y-m-d h:i:s",strtotime($r_tanggal)),
        "modifiedTime" => date("Y-m-d h:i:s",strtotime($r_tanggal)),
        "destinationInfo"=> 
                array(
                    "primaryParam"      => $r_idpel1
                ),
        "orderStatus" => 
                array(
                    "code"      => $r_status,
                    "status"    => $r_keterangan,
                    "message"   => $r_keterangan
                ),
       
        "serialNumber"   => trim($reffnumber),
        "token"        => "",
        "product"    => 
                array(
                    "productId"     => $r_kdproduk,
                    "type"  => "IDR",
                    "provider" => $frm->getPTName(),
                    "price" => array(
                        "value"     => $r_nominal+$r_nominaladmin,
                        "currency"  => "IDR",
                    ),
                    "availability" => true  
                )
    );
    $result = array(
        "response" => 
            array(
            "head"=> 
                array(
                    'version'=> $version,
                    'function' => $function,
                    'reqTime' => date('Y-m-d H:i:s',strtotime($r_tanggal)),
                    'reqMsgId' => $ref1_inquiry
                ),
           
            "body"=>
                array(
                    "order" => ($resp_inquiry)
                )
            ),
         "signature"=>$signature
    );
   return json_encode(array_change_key_case($result, CASE_LOWER));
}


function cekstatus($data) {
    $i = -1;
    $version    = $data->request->head->version;
    $function   = $data->request->head->function;
    $reqTime    = $data->request->head->reqTime;
    $reqMsgId   = $data->request->head->reqMsgId;
    $ref2       = $data->request->body->orderIdentifiers[0]->orderId;
    $ref1       = $data->request->body->orderIdentifiers[0]->requestId;
     $signature   = $data->signature;
    $idoutlet   = "HH122973";
    $pin        = "967733";

   

    // global $pgsql;
    // if (!checkHakAksesMP($pgsql, strtoupper($idoutlet))) {
    //     return json_encode(array('error'=>'access not allowed'));
    // }
    if ($kdproduk == "PLNPASC") {
        $kdproduk = "PLNPASCH";
    } else if ($kdproduk == "PLNPRA") {
        //$idproduk = "PLNPRAH";
        $kdproduk = "PLNPRAID";
    } else if ($kdproduk == "PLNNON") {
        $kdproduk = "PLNNONH";
    } else if ($kdproduk == "HPTSEL") {
        $kdproduk = "HPTSELH";
    }
    
    if ($ref1 != "" || $ref2 != "") {
        $data = getStatusProsesTransaksiDana( $idoutlet, $ref1, $ref2);
        $cnt = count($data);
        if ($cnt > 0) {
            $status = "00";
            $ket = "SEDANG DIPROSES";
        } else {
            $data = getStatusTransaksiDana($idoutlet, $ref1, $ref2);
            $cnt = count($data);
            // echo $cnt;die();
            // print_r($data[0]);
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
                if($data["response_code"] == "00")
                {
                        $status = "10";
                        $ket = "SUCCESS";
                }
            } else {
                $data = getStatusTransaksiBackupDana($idoutlet, $ref1, $ref2);
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
        $ket = "Transaksi dengan kriteria yang dimaksud tidak ditemukan (cek ke CS dulu)";
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
     
    $data_cekstatus = array(
        
        "order" => array(
                "requestId"    => (string) trim($data["bill_info83"]),
                "orderId"      => (string) trim($data["id_transaksi"]),
                "createdTime"  => (string) trim($data["time_request"]),
                "modifiedTime" => (string) trim($data["time_request"]),
                "destinationInfo" => array(
                                        "primaryParam" => (string) trim($data["bill_info1"])
                                    ),
                "orderStatus"  => array(
                                    "code"      => $status,
                                    "status"    => $ket,
                                    "message"   => $ket
                                ),
                "serialNumber" => "",
                "token"        => "",
                "product"      => array(
                                    "productId"     => $data["id_produk"],
                                    "type"  => "IDR",
                                    "provider" =>  (string) trim($data["bill_info23"]),
                                    "price" => array(
                                        "value"     => $data["nominal"]+$data["nominal_admin"],
                                        "currency"  => "IDR",
                                    ),
                                    "availability" => true  
                                )
        )
    );
    $result = array(
        "response" => 
            array(
            "head"=> 
                array(
                    'version'=> $version,
                    'function' => $function,
                    'reqTime' => date('Y-m-d H:i:s',strtotime($reqTime)),
                    'reqMsgId' => $reqMsgId
                ),
            "body"=>
                array(
                    "inquiryResults" => array($data_cekstatus)
                )
            ),
         "signature" =>  $signature
            
    );

    $log_content = "\n[" . date("Y-m-d H:i:s") . "][" . $GLOBALS["mid"] . "]SEND CEKSTATUS RESPONSE TO PARTNER->|" . print_r($params, true) . "|";
    writeLogText($log_content);
    $params = array_change_key_case($result, CASE_LOWER);
    return json_encode($params);
}

?>
