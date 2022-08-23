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
require_once("include/format_message/FormatLakupandai.class.php");
//koneksi ke database postgre
// global $pgsql;
// $pgsql      = pg_connect("host=" . $__CFG_dbhost . " port=" . $__CFG_dbport . " dbname=" . $__CFG_dbname . " user=" . $__CFG_dbuser . " password=" . $__CFG_dbpass);

$msg        = $HTTP_RAW_POST_DATA;
$host       = getClientIP();//getClientIP();//$_SERVER['REMOTE_ADDR'];

$mid = getNextMID();
// $mid        = 1; //buat local
$step       = 1;
$raw_msg    = $_GET;

//generate write to log and replace pin
$rawcpy 			= $raw_msg;
$rawcpy['pin'] 		= '------';
$rawjson 			= json_encode($rawcpy);
$sender             = "XML CLIENT";
//$receiver           = $GLOBALS["__G_module_name"];
$receiver           = $_SERVER['SERVER_ADDR']."-RB-TEXT-".$_SERVER['HTTP_HOST']."-".$_SERVER['SERVER_NAME'];
$via                = $GLOBALS["__G_via"];
writeLog($mid, $step, $host, $receiver, $rawjson, $via);

$ips = getClientIP();
appendfile(date('Y:m:d H:i:s').' Request Text '.$ips.'= '. $rawjson);
appendfilehit(date('Y:m:d H:i:s').'-'.$receiver.' Request Text '.$ips.'= '. $rawjson, 'text');

$method = strtolower($raw_msg['method']);
$pin = $raw_msg['pin'];
if( $method != "" ){
    $wpin = array('cekip');
    if(!in_array($method,$wpin)){
        if(($pin == '' || strlen($pin) != 6 || !is_numeric($pin))){
            echo'Pin tidak valid';
            die();
        }
    }
}
switch ($method) {
    case "balance":
        echo balance($raw_msg);
        break;
    case "transferinq":
        echo transferinq($raw_msg);
        break;
    case "transferpay":
        echo transferpay($raw_msg);
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
    case "inqpln":
        echo inqpln($raw_msg);
        break;
    case "paypln":
        echo paypln($raw_msg);
        break; 
    case "cekharga_gp":
        echo cek_harga2($raw_msg);
        break;
    default :
        echo'Produk tidak dikenal';
}

function data_transaksi($data){
    // die('a');
     $next   = FALSE;
    $end    = FALSE;

    $tgl1       = trim(strtoupper($data['tgl1']));
    $tgl2       = trim(strtoupper($data['tgl2']));
    $idtrx      = trim(strtoupper($data['id_transaksi']));
    $idproduk   = trim(strtoupper($data['id_produk']));
    $idpel      = trim(strtoupper($data['idpel']));
    $limit      = trim(strtoupper($data['limit']));
    $idoutlet   = trim(strtoupper($data['uid']));
    $pin        = trim(strtoupper($data['pin']));
    $custreff   = trim(strtoupper($data['ref1']));
    $field      = 10;
    $field2     = 9;
    if(count((array)$data) !== $field && count((array)$data) !== $field2){
        return 'missing parameter request';
    }

    if($idproduk == 'HPTSEL'){
        $idproduk = 'HPTSELH';
    } else if($idproduk == 'ASRBPJSKS'){
        $idproduk = 'ASRBPJSKSH';
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    // global $pgsql;
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        return "Anda tidak punya hak akses";
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

    // echo $tgl1." - ".$tgl2;
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
        // die('a');
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
                    $datatransaksi[] = $desc;
                }

                $datatransaksi[] = $data[$i]->nominal;
                if(in_array($data[$i]->id_produk, KodeProduk::getPLNPrepaids())){
                    $datatransaksi[] = $data[$i]->token;
                } else {
                    $datatransaksi[] = $data[$i]->sn;
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
        'TGL1'          => (string) $r_tanggal1, 
        'TGL2'          => (string) $r_tanggal2, 
        'KODE_PRODUK'   => (string) $r_kdproduk, 
        'IDPEL'         => (string) $r_idpel, 
        'LIMIT'         => (string) $r_limit, 
        'UID'           => (string) $r_idoutlet, 
        'PIN'           => (string) "------", 
        'STATUS'        => (string) $r_responsecode, 
        'KET'           => (string) $r_keterangan,

    );

    $r_content = explode("~", $r_content);
    
    $result_data = array();
    for ($i = 0; $i < count($r_content); $i++) {
        array_push($result_data, $r_content[$i]);
    }
    unset($result_data[0]);
    $hasil = implode(",", $params);
    $params['RESULT_TRANSAKSI'] = implode(", \n",$result_data);
    // echo $params['RESULT_TRANSAKSI'] ;die();
    
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
    return $hasil.",\n".$params['RESULT_TRANSAKSI'] ;
}
function cek_harga2($req)
{
    // die('a');
    $result = array();
    $next   = FALSE;
    $end    = FALSE;
    // global $pgsql;

    $group      = trim(strtoupper($req['group']));
    $produk     = trim(strtoupper($req['produk']));
    $idoutlet   = trim(strtoupper($req['uid']));
    $pin        = trim(strtoupper($req['pin']));

    // echo $group."".$id_produk;die();
    $field      = 5;
    if(count((array)$req) !== $field){
        return 'error=missing parameter request';
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130") {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
        return "Anda tidak punya hak akses";
    }

    if (outletexists($idoutlet)) {
        $next = TRUE;
    } else {
        // die('a');
        $rc     = "01";
        $ket    = "ID Outlet tidak terdaftar atau tidak aktif";
        $next   = FALSE;
        $params = array(
            "UID"       => (string) $idoutlet,
            "PIN"       => (string) '------',
            "STATUS"    => (string) $rc,
            "KET"       => (string) $ket
        );
          return implode(',', $params);
    }


    if ($next) {
        if (checkpin($idoutlet, $pin)) {
            $next = TRUE;
        } else {
            $rc     = "02";
            $ket    = "Pin yang Anda masukkan salah";
            $params = array(
                "UID"       => (string) $idoutlet,
                "PIN"       => (string) '------',
                "STATUS"    => (string) $rc,
                "KET"       => (string) $ket
            );
            return implode(',', $params);
        }
    }

    if($next){
        // die('a');
        $foruse = foruse2($group,$produk, $idoutlet);
        if(is_array($foruse)){
            $msgcontent = array();
            $d=1;
             foreach($foruse as $row){


                 $datas = array();

                if($row->is_active == '1' && $row->is_gangguan == '0'){
                    $status = 'AKTIF';
                } else {
                    $status = 'GANGGUAN';
                }
                $komisi  = abs($row->up_harga) + abs($row->fee_transaksi);
                $dt1 = "UID=$idoutlet,PIN=------,STATUS=00,DATA,";
                $dt[] = "idproduk=$row->id_produk".','."namaproduk=$row->produk".','."harga_jual=$row->harga_jual".','."biaya_admin=$row->biaya_admin".','."status=$status".',';
             }
             $imploded=implode('',$dt);
             $hasil = $dt1."".$imploded;
            return rtrim($hasil, ", ");
        } else {
            $rc     = "03";
            $ket    = "Data tidak ditemukan";
            $params = array(
                "UID"       => (string) $idoutlet,
                "PIN"       => (string) '------',
                "STATUS"    => (string) $rc,
                "KET"       => (string) $ket
            );
              return implode(',', $params);
        }
    }
}
function paypln($req){
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
    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile('TEXT PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
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
                appendfile(date("Y-m-d H:i:s = ").' SELISIH '.$selisih.'  TEXT (BAYAR): '.json_encode($params));
                 insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'TEXT',strtoupper("TEXT ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
               return implode('.', $params);
            }
        }
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME']."-".$_SERVER['SERVER_NAME'];
    $fm = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA"){
        $resp = "TAGIHAN*$kdproduk****H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "***" . $idoutlet . "*------*------******99*Mitra Yth, mohon maaf, method ini hanya untuk produk plnpasca dan plnpra ";
    }else if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else {
            //$respon = postValueWithTimeOutDevel($fm);
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else {
        // $cektoken = getTokenPlnPra(trim($idpel1), trim($idpel2), trim($nominal), trim($kdproduk), trim($idoutlet)); //tambahan
        // if ($cektoken[0] != '-' && in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        //     $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*Sudah Pernah Terjadi status sukses di ID Outlet: " . $idoutlet . ", Nominal:" . $nominal . ", IDPEL: " . $cektoken[0] . ", dan Token:" . $cektoken[3] . "*********************" . $cektoken[3] . "*************";
        // } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        // }
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
        return implode('.', $params);
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
        writeLog($r_mid, $r_step, $receiver, $host, json_encode($params), $via);
    }

    return implode('.', $params);
}

function inqpln($req){
    // die('a');
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

     if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"   ) {
         if (!isValidIP($idoutlet, $ip)) {
             return "IP Anda [$ip] tidak punya hak akses";
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
    $fm = convertFM($msg, "*");
    // echo "fm = ".$fm."<br>";
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    $list = $GLOBALS["sndr"];
    
    if(substr(strtoupper($kdproduk), 0,7) != "PLNPASC" && substr(strtoupper($kdproduk), 0,6) != "PLNPRA"){
        // die('a');
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

    return implode('.', $params);
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        return "IP Anda [$ip] tidak punya hak akses";
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }
    // global $pgsql;

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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip <> "") {
        if (!isValidIP($idoutlet, $ip) && $ip != "180.250.248.130"  ) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $idpel1 = pad($idpel1, 11, "0", "left");
    }

    // global $pgsql;
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];           //KETERANGAN
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];           //KETERANGAN
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
        $url_struk  = "http://34.101.201.189/strukmitra/?id=" . $url;
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile(date("Y-m-d H:i:s").'= TEXT PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
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
                    "REF3"              => (string) '',
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                $implode = implode('.', $params);
                appendfile(date("Y-m-d H:i:s").' SELISIH '.$selisih.'  TEXT (BAYAR): '. json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'TEXT',strtoupper("TEXT ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return $implode;
            }
        }
    }
    // handle 504 end
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    if(substr($idpel1, 0, 5) != "88888"){
        $idpel1 = "88888".substr($idpel1, 2, 11);
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN

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
        $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*$nominal*0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI";
    } else if(substr($hp, 0, 1) != '0' || !is_numeric($hp) || !in_array(strlen($hp), $arr) || $hp == ''){
        $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*$nominal*0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*Isi No Hp dengan benar.";
    } else {      
        $respon = postValue($fm);
        $resp   = $respon[7];
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
            "UID"               => (string) $idoutlet,
            "PIN"               => (string) '------',
            "REF1"              => (string) $ref1,
            "REF2"              => (string) $ref2,
            "REF3"              => (string) $ref3,
            "STATUS"            => (string) '00',
            "KET"               => (string) 'SEDANG DIPROSES',
            "SALDO_TERPOTONG"   => (string) '',
            "SISA_SALDO"        => (string) '',
            "URL_STRUK"         => (string) ''
        );
        $implode = implode('.', $params);
        return $implode;
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
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
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

    // global $pgsql;
    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];

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
            $resp = "BAYAR*$kdproduk***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*$nominal*0*" . $idoutlet . "*" . $pin . "*------****$kdproduk**XX*Periode bulan harus diisi min 1 dan maks 12";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
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

    $ip = getClientIP(); 
    if ($ip == "10.0.0.20") {
        return "IP Anda [$ip] tidak punya hak akses";
    } else {
        if (!isValidIP($uid, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return "IP Anda [$ip] tidak punya hak akses";
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

    $ip = getClientIP(); 
    if ($ip == "10.0.0.20") {
        return "IP Anda [$ip] tidak punya hak akses";
    } else {
        if (!isValidIP($id_outlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }
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
        "IP"    => getClientIP()
    );
    return implode('.', $output);
}

function c($data){
	return htmlentities(trim($data), ENT_QUOTES, 'UTF-8');
}

function transferinq($req)
{
    $i = -1;
    $kdproduk   = strtoupper($req['produk']);
    $idpel1     = strtoupper($req['idpel1']);
    $idpel2     = strtoupper($req['idpel2']);
    $idpel3     = strtoupper($req['idpel3']);
    $uid        = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']);
    $nominal    = strtoupper($req['nominal']);
    $kodebank   = strtoupper($req['kodebank']);
    $nomorhp    = strtoupper($req['nomorhp']);

    $field      = 11;

     if(count((array)$req) !== $field){
        return 'missing parameter request';
    }
    
     $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($uid, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

     // global $pgsql;
       $stp = $GLOBALS["step"] + 1;
    $msg = array();
    $i = -1;
    $msg[$i+=1] = "TAGIHAN";
    $msg[$i+=1] = $kdproduk;
    $msg[$i+=1] = $GLOBALS["mid"];
    $msg[$i+=1] = $stp;
    $msg[$i+=1] = date('YmdHis');           //VIA
    $msg[$i+=1] = $GLOBALS["via"];           //VIA
    $msg[$i+=1] = $idpel1;
    $msg[$i+=1] = $idpel2;
    $msg[$i+=1] = $idpel3;
    $msg[$i+=1] = $nominal;
    $msg[$i+=1] = "";
    $msg[$i+=1] = strtoupper($uid);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";   //  FIELD_BALANCE
    $msg[$i+=1] = ""; // FIELD_JENIS_STRUK
    $msg[$i+=1] = ""; // FIELD_KODE_BANK
    $msg[$i+=1] = ""; //FIELD_KODE_PRODUK_BILLER
    $msg[$i+=1] = ""; // FIELD_TRX_ID
    $msg[$i+=1] = ""; // FIELD_STATUS
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME']; // FIELD_KETERANGAN
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
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
    $sender = $GLOBALS["__G_module_name"];
    $receiver = $GLOBALS["__G_receiver"];
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];

    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";
    $list = $GLOBALS["sndr"];
    if($kdproduk == 'ASRBPJSKS'){
        $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$uid."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
    } else if(substr($uid, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA32670');
        if(!in_array($uid, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$uid."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            $respon = postValue($fm);
            $resp = $respon[7];
        }
    } else{
        $respon = postValue_fmssweb2($fm);
            $resp = $respon[7];
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

    $get_mid = get_mid_from_idtrx($r_idtrx);
    $get_step = get_step_from_mid($get_mid) + 1;
    $implode = implode('.', $params);
    writeLog($get_mid, $get_step, $host, $receiver, $implode, $via);
    return $implode;
}

function transferpay($req)
{
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
    $kodebank   = strtoupper($req['kodebank']);
    $nomorhp    = strtoupper($req['nomorhp']);

    $field      = 12;
    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile(date("Y-m-d H:i:s").'TEXT PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
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
                    "REF3"              => (string) '',
                    "STATUS"            => (string) '77',
                    "KET"               => (string) 'Transaksi gagal, silahkan coba beberapa saat lagi',
                    "SALDO_TERPOTONG"   => (string) '',
                    "SISA_SALDO"        => (string) '',
                    "URL_STRUK"         => (string) ''
                );
                $implode = implode('.', $params);
                appendfile(date("Y-m-d H:i:s").' SELISIH '.$selisih.'  TEXT (BAYAR): '. json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'TEXT',strtoupper("TEXT ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return $implode;
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    // global $pgsql;
    if (!checkHakAksesMP("", strtoupper($idoutlet))) {
       die("");
    }

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
    $msg[$i+=1] = date('YmdHis');
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip; // FIELD_KETERANGAN
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
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
        } else {
            $respon = postValue_fmssweb2($fm);
            $resp = $respon[7];
        }
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
            "UID"               => (string) $idoutlet,
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
        $implode = implode('.', $params);
        return $implode;
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
    $r_idoutlet         = $frm->getMember();
    $r_pin              = $frm->getPin();
    $r_sisa_saldo       = $frm->getSaldo();
    $r_idtrx            = $frm->getIdTrx();
    $r_status           = $frm->getStatus();
    $r_keterangan       = $frm->getKeterangan();
    $r_saldo_terpotong  = 0;
    $r_nama_pelanggan   = getNamaPelanggan($kdproduk, $frm);
     $r_id_pelanggan = getIdPelanggan($kdproduk, $frm);
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
   

    $url_struk = "";
    if ($frm->getStatus() == "00") {
        //get url struk

        $nom_up = getnominalup($r_idtrx);

        $r_saldo_terpotong = $r_nominal + $r_nominaladmin + ($nom_up);

        $url = enkripUrl(strtoupper($idoutlet), $frm->getIdTrx());
        $url_struk = "http://34.101.201.189/strukmitra/?id=" . $url;
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
        "STATUS"            => (string) $r_status,
        "KET"               => (string) $r_keterangan,
        "SALDO_TERPOTONG"   => (string) $r_saldo_terpotong,
        "SISA_SALDO"        => (string) $r_sisa_saldo,
        "URL_STRUK"         => (string) $url_struk
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

function inq($req){
	$i = -1;
    $kdproduk   = strtoupper($req['produk']);
    $idpel1     = strtoupper($req['idpel1']);
    $idpel2     = strtoupper($req['idpel2']);
    $idpel3     = strtoupper($req['idpel3']);
    $uid        = strtoupper($req['uid']);
    $pin        = strtoupper($req['pin']);
    $ref1       = strtoupper($req['ref1']);
    $field      = 8;
    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130"  ) {
        if (!isValidIP($uid, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    
    if(substr($kdproduk, 0, 5) == 'PAJAK'){
        if(strlen($idpel1) != 18){
             return "Nomor Object Pajak(NOP) harus 18 digit";
        }
    }
    // global $pgsql;

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
    $msg[$i+=1] = strtoupper($uid);
    $msg[$i+=1] = $pin;
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = "";
    $msg[$i+=1] = $ref1;
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'];
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
    if($kdproduk == 'ASRBPJSKS'){
        $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$uid."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
    } else if(substr($uid, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA32670');
        if(!in_array($uid, $whitelist_outlet)){
            $resp = "TAGIHAN*".$kdproduk."*789644896*11*".date('YmdHis')."*H2H*".$idpel1."*****".$uid."*".$pin."***3*15*080002**XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h.*0605**".$idpel1."*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else {
            // die('a');
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
                // die('a');
                $respon = postValue($fm);
                $resp = $respon[7];    
            }
        } else {
            if(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
            }else{
            // die('o');
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile(date("Y-m-d H:i:s").'TEXT PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
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
                $implode = implode('.', $params);
                appendfile(date("Y-m-d H:i:s").' SELISIH '.$selisih.'  TEXT (BAYAR): '. json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'TEXT',strtoupper("TEXT ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return $implode;
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "10.1.51.4" && $ip != "10.0.0.30" && $ip != "180.250.248.130" ) {
        if (!isValidIP($idoutlet, $ip)) {
            $request_error = "kodeproduk: $kdproduk\nidpel1: $idpel1\nidpel2: $idpel2\nidpel3: $idpel3\nnominal: $nominal \nidoutlet: $idoutlet\npin: -----\nref1: $ref1\nref2: $ref2\nref3: $ref3";
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }
    
    // global $pgsql;
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN
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
    if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI (TANPA TAMBAHAN BIAYA ADMIN)";
        } else if($kdproduk == 'ASRBPJSKS'){
            $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
        } else if(substr(strtoupper($kdproduk), 0,2) == "WA"){
             $respon = postValuepdam($fm); 
             $resp = $respon[7]; 
        } else {
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
                if(strpos(strtoupper($kdproduk),'WAKENDARI') !== false ){
                    $respon = postValue_fmssweb2($fm); // to /FMSSWeb4/mpin1
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
            } elseif(substr(strtoupper($kdproduk), 0,2) == "WA"){
                 $respon = postValuepdam($fm); 
                 $resp = $respon[7]; 
            }else {
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
            "STATUS"            => (string) '00',
            "KET"               => (string) 'SEDANG DIPROSES',
            "SALDO_TERPOTONG"   => (string) '',
            "SISA_SALDO"        => (string) '',
            "URL_STRUK"         => (string) ''
        );
        $implode = implode('.', $params);
        return $implode;
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

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile(date("Y-m-d H:i:s").'TEXT PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
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
                $implode = implode('.', $params);
                appendfile(date("Y-m-d H:i:s").' SELISIH '.$selisih.'  TEXT (BAYAR): '. json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'TEXT',strtoupper("TEXT ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return $implode;
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];

    if ($ip != "10.1.51.4" && $ip != "10.0.0.20" && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "::1" ) {
        if (!isValidIP($idoutlet, $ip)) {
            return "IP Anda [$ip] tidak punya hak akses";
        }
    }

    // global $pgsql;
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;           //KETERANGAN
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

    if (!in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        if ($ceknom != $nominal) {
            $resp = "BAYAR*" . $kdproduk . "***" . date("Ymdhis") . "*H2H*" . $idpel1 . "*" . $idpel2 . "*" . $idpel3 . "*" . $nominal . "*0*" . $idoutlet . "*" . $pin . "*------****" . $kdproduk . "**XX*MAAF NILAI NOMINAL YANG DIBAYARKAN TIDAK SESUAI";
        } else {
            //$respon = postValueWithTimeOutDevel($fm);
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
                    $respon = postValue($fm);
                    $resp = $respon[7];
                }
            }
        }
    } else {
       
            if($kdproduk == 'ASRBPJSKS'){
                $resp = "TAGIHAN*ASRBPJSKS*789644896*11*".date('YmdHis')."*H2H*$idpel1*****".$idoutlet."*".$pin."***3*15*080002**XX*Maaf, BPJS Kesehatan tidak bisa dilanjutkan dengan method ini.*0605**.$idpel1.*2*****000000064986*******BPJS Kesehatan***750**000000000000*0***";
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
            "STATUS"            => (string) '00',
            "KET"               => (string) 'SEDANG DIPROSES',
            "SALDO_TERPOTONG"   => (string) '',
            "SISA_SALDO"        => (string) '',
            "URL_STRUK"         => (string) ''
        );
        $implode = implode('.', $params);
        return $implode;
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
        "KET"               => (string) trim(str_replace('.', '', $$r_keterangan)),
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

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
       if ($ip == "10.0.0.20") {
        return "IP Anda [$ip] tidak punya hak akses";
        } else {
            if (!isValidIP($uid, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
                return "IP Anda [$ip] tidak punya hak akses";
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
    $ref1       = strtoupper($req['ref1']);
    $field      = 6;
    
    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile(date("Y-m-d H:i:s").'TEXT PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
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
                    "SISA_SALDO"        => (string) ''
                );
                $implode = implode('.', $params);
                appendfile(date("Y-m-d H:i:s").' SELISIH '.$selisih.'  TEXT (BAYAR): '. json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'TEXT',strtoupper("TEXT ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return $implode;
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        return "IP Anda [$ip] tidak punya hak akses";
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return "IP Anda [$ip] tidak punya hak akses";
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;        //FIELD_KETERANGAN

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
         $respon = postValue_fmssweb2($fm);
         $resp = $respon[7];
    }

    if($resp == 'null'){
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
            "STATUS"            => (string) '00',
            "KET"               => (string) 'SEDANG DIPROSES',
            "SALDO_TERPOTONG"   => (string) '',
            "SISA_SALDO"        => (string) ''
        );
        $implode = implode('.', $params);
        return $implode;
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
    $ref1       = strtoupper($req['ref1']);
    $field      = 6;

    if(count((array)$req) !== $field){
        return 'missing parameter request';
    }

    // handle 504 start
    if($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE'] != ''){
        if(empty(urlreversalexists($idoutlet))){
            date_default_timezone_set('asia/jakarta');
            $my_ips = (int) strtotime($_SERVER['HTTP_X_HAPROXY_CURRENT_DATE']);
            $datein = (int) strtotime(date('Y-m-d H:i:s'));
            $selisih = $datein - $my_ips;
            appendfile(date("Y-m-d H:i:s").'TEXT PAY FAIL HTTP_X_HAPROXY_CURRENT_DATE my_ips: '.$my_ips.', datein: '.$datein.', selisih: '.$selisih);
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
                    "SISA_SALDO"        => (string) ''
                );
                $implode = implode('.', $params);
                appendfile(date("Y-m-d H:i:s").' SELISIH '.$selisih.'  TEXT (BAYAR): '. json_encode($params));
                insertLogRc77($my_ips,$datein,$selisih,$idoutlet,'TEXT',strtoupper("TEXT ".__FUNCTION__),getClientIP(),$_SERVER['SERVER_ADDR'],$_SERVER['SERVER_NAME']);
                return $implode;
            }
        }
    }
    // handle 504 end

    $ip = getClientIP();//$_SERVER['REMOTE_ADDR'];
    if ($ip == "10.0.0.20") {
        return "IP Anda [$ip] tidak punya hak akses";
    } else {
        if (!isValidIP($idoutlet, $ip) && $ip != "10.0.51.2" && $ip != "180.250.248.130" && $ip != "10.1.51.4"  ) {
            return "IP Anda [$ip] tidak punya hak akses";
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
    $msg[$i+=1] = strtoupper("TEXT ".__FUNCTION__)."-".$_SERVER['SERVER_NAME'].'-'.$ip;        //FIELD_KETERANGAN

    $fm         = convertFM($msg, "*");
    //echo "fm = ".$fm."<br>";
    $sender     = $GLOBALS["__G_module_name"];
    $receiver   = $GLOBALS["__G_receiver"];
    
    $hidepin = explode($pin, $fm);
    $store_fm = $hidepin[0].'......'.$hidepin[1];
    writeLog($GLOBALS["mid"], $stp, $sender, $receiver, $store_fm, $GLOBALS["via"]);
    //echo $msg."<br>";

    if(substr($idoutlet, 0, 2) == 'FA'){
        $whitelist_outlet = array('FA9919', 'FA112009', 'FA89065', 'FA32670');
        if(!in_array($idoutlet, $whitelist_outlet)){
            $resp = "PULSA*$kdproduk*9999*7*".date('YmdHis')."*H2H*$nohp**".$idoutlet."*".$pin."******XX*Maaf, id Anda tidak bisa melakukan transaksi via H2h."; 
        } else {
            $respon = postValue($fm);
            $resp   = $respon[7];
        }
    } else {
        $respon = postValue_fmssweb2($fm);
        $resp   = $respon[7];
    }

    if($resp == 'null'){
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
            "STATUS"            => (string) '00',
            "KET"               => (string) 'SEDANG DIPROSES',
            "SALDO_TERPOTONG"   => (string) '',
            "SISA_SALDO"        => (string) ''
        );
        $implode = implode('.', $params);
        return $implode;
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

    if(in_array($sub_str1, $prefix_telp, TRUE)){
        $is_telp = TRUE;
        $len = strlen($sub_str1);
        $ret = array(
            'produk'    => 'TELEPON',
            'idpel1'    => $sub_str1,
            'idpel2'    => substr($idpel,$len)
        );
    } else if(in_array($sub_str2, $prefix_telp, TRUE)){
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

function appendfile($loglines) {
    try {
        $file = getcwd() . "/logs/" . date("Ymd") . '-haproxy.log';
        if (file_exists($file) == false) {
            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);
            fclose($handle);
        }
        file_put_contents($file, $loglines . "\n", FILE_APPEND | LOCK_EX);
    } catch (Exception $e){

    }
}
