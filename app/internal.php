<?php
error_reporting(0);
if($_GET['devel'] == 2){
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
}
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
require_once("smtpnya/pengirim_email.php");
//koneksi ke database postgre
global $pgsql;
$pgsql      = pg_connect("host=" . $__CFG_dbhost . " port=" . $__CFG_dbport . " dbname=" . $__CFG_dbname . " user=" . $__CFG_dbuser . " password=" . $__CFG_dbpass);

$msg        = $HTTP_RAW_POST_DATA;
$host       = getClientIP();//$_SERVER['REMOTE_ADDR'];

$mid = getNextMID();
// $mid        = 1; //buat local
$step       = 1;
$raw_msg    = file_get_contents('php://input');
$raw        = json_decode($raw_msg);

$raw_cpy             = $raw_msg;
$raw_cpy_decode      = json_decode($raw_cpy);
$raw_cpy_decode->pin = '------';
$msg_log             = json_encode($raw_cpy_decode);
$sender              = "XML CLIENT";
$receiver            = $GLOBALS["__G_module_name"];
$via                 = $GLOBALS["__G_via"];
writeLog($mid, $step, $host, $receiver, $msg_log, $via);

//return json_encode(array('error'=>"xsaf"));

switch ($raw->method) {
    
    case "rajabiller.registrasijabber":
        echo registrasijabber($raw);
        break;
    case "rajabiller.produk_h2h":
        echo produk_h2h($raw);
        break;
    default :
        echo json_encode(array('Produk tidak dikenal'));
}

function registrasijabber($data)
 {

// die('a');
    $key = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $username = strtoupper($data->username);
    $password = substr(str_shuffle($key), 0, 8);

    $nama     = strtoupper($data->nama);
    $email    = strtoupper($data->email);
    $nomorhp  = strtoupper($data->nomorhp);

    // echo $password;die();
    // send to akun jabber
    $curl = curl_init();

    $url_api = "http://jbb.fastpay.co.id:9090/plugins/restapi/v1/users";

    $data_request = array(
        "username" => $username, //$data['id_outlet'],
        "password" => $password, //$data['pin'],
        "name" => $nama,
        "email" => $email
    );


    $ch = curl_init();
    $timeout = 30;

    curl_setopt($ch, CURLOPT_URL, $url_api);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_request));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic YWRtaW46cmFoYXNpYTEyMzQ=',
                'Cookie: JSESSIONID=node01olwkw80my1851aur3eb9awt0m20.node0'
            ));    
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response);
    // print_r();
    // die();
    if(empty($result)){
    // print_r($response);die();
    // send to sms
        $msg  = "Rajabiller.com , username/server : ".$data->username."@jbb.fastpay.co.id dan PIN : ".$password." \n Rahasiakan username dan pin anda ke siapapun. Terima kasih ";
        $sender   = "registrasijabber";
        $receiver = $nomorhp;
        $mid  = rand(100,10000);
        $step = 1;
        $via  ="RAJABILLER";

        // send to email
        $subjek = "PIN JABER - Rajabiller.com";
        $ke = $email;
        $pengirim = "Rajabiller";
        $isi = $msg;
        kirimemail_satu($subjek,$ke,$pengirim,$isi);

        $data_sukses = array(
            'USERNAME' => $username,
            'NAMA'     => $nama,
            'KET'      => "SUKSES"
        );
        return json_encode($data_sukses);
    }elseif($result->exception == 'UserAlreadyExistsException'){
        $keterangan = "User sudah ada";
        $data_gagal= array(
            'USERNAME' => $username,
            'NAMA'     => $nama,
            'KET'      => $keterangan
        );
        return json_encode($data_gagal);
    }
 }


function produk_h2h($data){
    $result = array();
    $next   = FALSE;
    $end    = FALSE;
    // global $pgsql;
    $group      = $data->group;
    $idoutlet   = strtoupper($data->uid);
    $pin        = strtoupper($data->pin);

    // echo $group."".$id_produk;
    $field      = 4;
    if(count((array)$data) !== $field){
        return json_encode(array('error'=>'missing parameter request'));
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
        $foruse = produk_rajabiller($group, $idoutlet);
        if(is_array($foruse)){
            $msgcontent = array();
            for($i = 0; $i < count($foruse); $i++){
                $datas = array();

               $msgcontent[] = array(
                    'idproduk' => $foruse[$i]->id_produk,
                    'namaproduk' => $foruse[$i]->produk,
                    'hargajual' => $foruse[$i]->harga_jual,
                    'biayaadmin' => $foruse[$i]->biaya_admin
                );
            }
            // print_r($dt);
            $result = $msgcontent;
            $params = array(
                "group"       => $group,
                "data"      => $result 
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
