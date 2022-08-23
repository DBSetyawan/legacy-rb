<?php
require_once("include/PgDBI2.class.php");
require_once("include/Database.class.php");
// require_once("include/Database_devel.class.php");
// require_once("include/Writelog.class.php");

require_once("include/KodeProduk.class.php");
require_once("include/format_message/FormatMsg.class.php");
require_once("include/format_message/FormatDeposit.class.php");
require_once("include/format_message/FormatDaftar.class.php");
require_once("include/format_message/FormatMandatory.class.php");
require_once("include/format_message/FormatPajakPbb.class.php");
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
require_once("include/format_message/FormatBpjs.class.php");
require_once("include/format_message/FormatKartuKredit.class.php");
// require_once("include/format_message/FormatNewAsuransi.class.php");
// require_once("include/format_message/FormatIklanBaris.class.php");
// require_once("include/format_message/FormatLakuPandai.class.php");
require_once("include/format_message/FormatPgn.class.php");
require_once("include/format_message/FormatPKB.class.php");
// require_once("include/format_message/FormatZakat.class.php");
require_once("include/format_message/FormatGas.class.php");
//require_once("include/format_message/FormatJawapos.class.php");
// require_once("include/format_message/FormatBillPayment.class.php");

function query($pgsql, $sql) {
    $temp = pg_query($pgsql, $sql);
    $n = pg_num_rows($temp);
    $out = array();
    $i = 0;
    if ($n > 0) {
        while ($data = pg_fetch_object($temp)) {
            $out[$i] = $data;
            $i++;
        }
    }
    pg_free_result($temp);
    pg_close($pgsql);
    reconnect($pgsql);
    return $out;
}

function checkHakAksesMP($pgsql, $idOutlet) {

    $q = "SELECT fmss.get_mp_priv('$idOutlet', 'H2H XML');";
    $ret = query($pgsql, $q);
    foreach ($ret as $val) {
        $data = $val->get_mp_priv;
        if ($data == '') {
            return 0;
        } else {
            return 1;
        }
    }
}

function pad($str, $length, $char, $position) {
    $ret = $str;
    if ($position == "center") {
        $length = (int) abs(($length - strlen($str)) / 2);
        $length = $length + strlen($str);
        $justify = "";
    } else {
        $justify = ($position == "left" ? "" : "-");
    }
    $ret = sprintf("%" . $justify . $char . $length . "s", $str);

    return $ret;
}

function convertMessage($data, $separator) {
    $msg = "";
    $i = 0;
    foreach ($data as $v) {
        if ($i <> 0) {
            $msg .= $separator;
        }
        $va = replace_forbidden_chars($v);
        $msg .= $va;
        //$msg .= $v;
        $i++;
    }
    return $msg;
}

function postValue($msg) {

    /*$content = $msg;

    $len = strlen($content);
    //$hosts = Array("127.0.0.1");
    $hosts = Array($GLOBALS["__CFG_urltargetip"]);
    //$hosts = Array("192.168.10.121");
    foreach ($hosts as $host) {
        $fp = @fsockopen($host, $GLOBALS["__CFG_urltargetport"], $errno, $errdesc); //54321, &$errno, &$errdesc );
        //$fp = @fsockopen($host, 8180, &$errno, &$errdesc ); //54321, &$errno, &$errdesc );
        if ($fp) {
            break;
        }
    }

    if (!$fp) {
        $res = "Error: $errno $errdesc\n";
    } else {
        //@fputs( $fp, "POST /FMSS_giga/tester-serv.php HTTP/1.0\r\n");
        @fputs($fp, "POST " . $GLOBALS["__CFG_urltarget"] . " HTTP/1.0\r\n");
        @fputs($fp, "Connection: close\r\n");
        //@fputs( $fp, "Content-Type: text/xml\r\n");
        @fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
        @fputs($fp, "Content-Length: $len\r\n\r\n");
        @fputs($fp, $content);
        while (!feof($fp)) {
            $reply[] = @fgets($fp, 12000);
            //$reply[] = @fgets( $fp, 1000);
        }
        @fclose($fp);
        $psn = "";
        foreach ($reply as $v) {
            $psn .= nl2br($v);
        }

        $msg = $psn;
        $res = $reply;
    }
    return $res;*/

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    // $url = "http://" . $GLOBALS["__CFG_urltargetip_lokal"] . $GLOBALS["__CFG_urltarget_lokal"];
    $url = "http://" . $GLOBALS["__CFG_urltargetip"] . $GLOBALS["__CFG_urltarget"];
    // echo $url.$GLOBALS["__CFG_urltargetport"]; 
    // die();
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_lokal"]);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);   
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueKai($msg, $timeout = 60) {
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    $url = "http://" . $GLOBALS["__CFG_urltargetip_kai"] . $GLOBALS["__CFG_urltarget_kai"];

    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_kai"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOut_ECASH_DEVEL($msg, $timeout = 40) {
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    $url = "http://10.0.1.18/FMSSWeb/mpin1";
    $ch = curl_init();
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, "8080");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    curl_close($ch);
    return $result; 
}    

function postValueKaiOld($msg) {
    $content = $msg;

    $len = strlen($content);
    $hosts = Array($GLOBALS["__CFG_urltargetip_kai"]);
    foreach ($hosts as $host) {
        $fp = @fsockopen($host, $GLOBALS["__CFG_urltargetport_kai"], $errno, $errdesc); //54321, &$errno, &$errdesc );
        if ($fp) {
            break;
        }
    }

    if (!$fp) {
        $res = "Error: $errno $errdesc\n";
    } else {
        @fputs($fp, "POST " . $GLOBALS["__CFG_urltarget_kai"] . " HTTP/1.0\r\n");
        @fputs($fp, "Connection: close\r\n");
        @fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
        @fputs($fp, "Content-Length: $len\r\n\r\n");
        @fputs($fp, $content);
        while (!feof($fp)) {
            $reply[] = @fgets($fp, 2000000);
        }
        @fclose($fp);
        $psn = "";
        foreach ($reply as $v) {
            $psn .= nl2br($v);
        }

        $msg = $psn;
        $res = $reply;
    }
    return $res;
}

function postValueKaiDevel($msg) {
    $content = $msg;

    $len = strlen($content);
    $hosts = Array($GLOBALS["__CFG_urltargetip_jarvis"]);
    foreach ($hosts as $host) {
        $fp = @fsockopen($host, $GLOBALS["__CFG_urltargetport_jarvis"], $errno, $errdesc); //54321, &$errno, &$errdesc );
        if ($fp) {
            break;
        }
    }

    if (!$fp) {
        $res = "Error: $errno $errdesc\n";
    } else {
        @fputs($fp, "POST " . $GLOBALS["__CFG_urltarget_jarvis"] . " HTTP/1.0\r\n");
        @fputs($fp, "Connection: close\r\n");
        @fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
        @fputs($fp, "Content-Length: $len\r\n\r\n");
        @fputs($fp, $content);
        while (!feof($fp)) {
            $reply[] = @fgets($fp, 20000);
        }
        @fclose($fp);
        $psn = "";
        foreach ($reply as $v) {
            $psn .= nl2br($v);
        }

        $msg = $psn;
        $res = $reply;
    }
    return $res;
}

function getNextMID() {
    // $db = $GLOBALS["pgsql"];
    // $qn = "SELECT nextval('message_mid_seq') mid";
    // //$eqn = $db->query($qn);
    // $eqn = pg_query($db, $qn);
    // $rqn = pg_fetch_object($eqn);
    // $mid = $rqn->mid;
    // pg_free_result($eqn);
    // pg_close();
    // reconnect($db);
    // return $mid;
    return "12".round(microtime(true) * 1000);
}

function getNextMID_devel() {
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    //$db = $GLOBALS["pgsql"];
    $db = $pgsql;
    $qn = "SELECT nextval('message_mid_seq') mid";
    //$eqn = $db->query($qn);
    $eqn = pg_query($db, $qn);
    $rqn = pg_fetch_object($eqn);
    $mid = $rqn->mid;
    pg_free_result($eqn);
    pg_close();
    reconnect_devel($db);
    return $mid;
}

function writeLog($mid, $step, $sender, $receiver, $msg, $via){
    // $db = new Writelog();
    // $step = intval($step);
    // $pin = explode("*", $msg);
    // $msg = str_replace($pin[12], "______", $msg);
    // $msg = str_replace("'", "", $msg);
    // $msg = str_replace('"', "", $msg);
    // $msg = replace_forbidden_chars($msg);
    // $msg = mysql_real_escape_string($msg);
    // $sql = "INSERT INTO message (mid,step,sender,receiver,content,id_modul,via,is_sent,date_created) 
    //         VALUES (
    //         " . $mid . ",
    //         " . $step . ",
    //         '" . $sender . "',
    //         '" . $receiver . "',
    //         '" . $msg . "',
    //         '',
    //         '" . $via . "',
    //         1,
    //         NOW()
    //         )";
            
    // if($step!==1){
    //    $store = mysql_query($sql, $db->getConnection());
    // }
    // global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    // $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    
    // $step = intval($step);
    // $pin = explode("*", $msg);
    // $msg = str_replace($pin[12], "______", $msg);
    // $msg = str_replace("'", "", $msg);
    // $msg = str_replace('"', "", $msg);
    // $msg = replace_forbidden_chars($msg);
    // $msg = pg_escape_string($msg);

    // $qn = "INSERT INTO fmss.message (date_created,mid,step,sender,receiver,content,via,is_sent) 
    //         VALUES (
    //         NOW(),
    //         " . $mid . ",
    //         " . $step . ",
    //         '" . $sender . "',
    //         '" . $receiver . "',
    //         '" . $msg . "',
    //         '" . $via . "',
    //         1
    //         )";
    // $eqn = $db->query($qn);
}

function wr($mid, $step, $sender, $receiver, $msg, $via){

    // global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    // $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    
    // $step = intval($step);
    // $pin = explode("*", $msg);
    // $msg = str_replace($pin[12], "______", $msg);
    // $msg = str_replace("'", "", $msg);
    // $msg = str_replace('"', "", $msg);
    // $msg = replace_forbidden_chars($msg);
    // $msg = pg_escape_string($msg);

    // $qn = "INSERT INTO fmss.message (date_created,mid,step,sender,receiver,content,via,is_sent) 
    //         VALUES (
    //         NOW(),
    //         " . $mid . ",
    //         " . $step . ",
    //         '" . $sender . "',
    //         '" . $receiver . "',
    //         '" . $msg . "',
    //         '" . $via . "',
    //         1
    //         )";
    // $eqn = $db->query($qn);
    
}

function writeLog_OLD($mid, $step, $sender, $receiver, $msg, $via) {   
//     $db = $GLOBALS["pgsql"];
//     $step = intval($step);
//     $pin = explode("*", $msg);
//     $msg = str_replace($pin[12], "______", $msg);
//     $msg = str_replace("'", "", $msg);
//     $msg = str_replace('"', "", $msg);
//     $msg = replace_forbidden_chars($msg);
//     $msg = pg_escape_string($msg);

//     $q = "  INSERT INTO fmss.message (date_created,mid,step,sender,receiver,content,via,is_sent) 
//             VALUES (
//             NOW(),
//             " . $mid . ",
//             " . $step . ",
//             '" . $sender . "',
//             '" . $receiver . "',
//             '" . $msg . "',
//             '" . $via . "',
//             1
//             )";
//     //$e = $db->query_boolean($q);
//     $e = pg_query($db, $q);
// //    pg_free_result($e);
//     pg_close();
//     reconnect($db);
}

function insert_cancel_item($no_stt, $chk_status, $tgl_book, $nama_pos, $kode_pos, $origin, $destinasi, $alasan) {   
    $db = $GLOBALS["pgsql"];
    $query = "INSERT INTO fmss.lionparcel_cancel_item(no_stt, current_status, pos_name, pos_code, origin, destination, reason, booking_date) "
            . "VALUES "
            . "("
            . "'" . $no_stt . "',"
            . "'" . $chk_status . "',"
            . "'" . $nama_pos . "',"
            . "'" . $kode_pos . "',"
            . "'" . $origin . "',"
            . "'" . $destinasi . "',"
            . "'" . $alasan . "',"
            . "'" . $tgl_book . "'"
            . ")";   
    
    $e = pg_query($db, $query);
    pg_close();
    reconnect($db);
}

function replaceChar($content) {

    $content = trim($content);
    $content = str_replace(" ", "+", $content);
    $content = str_replace('"', "", $content);
    $content = str_replace("'", "", $content);
    $content = str_replace("`", "", $content);
    $content = str_replace("~", "", $content);
    $content = str_replace(".", "", $content);
    $content = str_replace(",", "", $content);
    $content = str_replace("\r", "", $content);
    $content = str_replace("\n", "", $content);
    $content = str_replace("\t", "", $content);

    return $content;
}

function getDepDate($idtrx) {
    $db = $GLOBALS["pgsql"];
    $result = "";

    $query = "SELECT bill_info13, bill_info15 
              FROM fmss.transaksi 
              WHERE id_transaksi = " . $idtrx;

    $data = pg_query($db, $query);
    $hasil = pg_num_rows($data);

    if ($hasil > 0) {
        while ($out = pg_fetch_object($data)) {
            $result = $out->bill_info13 . ";" . $out->bill_info15;
        }
    }
    pg_free_result($data);
    pg_close();
    reconnect($db);
    return $result;
}

function convertFM($data, $separator) {
    $msg = "";
    $i = 0;
    foreach ($data as $v) {
        if ($i <> 0) {
            $msg .= $separator;
        }
        $va = replace_forbidden_chars($v);
        $msg .= $va;
        //$msg .= $v;
        $i++;
    }
    return $msg;
}
 
function checkpin($idoutlet, $pin) {
    $db = $GLOBALS["pgsql"];

    $q = "SELECT id_outlet FROM mt_outlet WHERE id_outlet = upper('" . $idoutlet . "') AND pin = crypt(upper(md5('" . $pin . "')), pin) AND is_active=1";

    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
    if ($n > 0)
        return TRUE;
    else
        return FALSE;

    pg_free_result($e);
    pg_close();
    reconnect($db);
}

function checkpin_devel($idoutlet, $pin) {
    //global $__CFG_dbhost_devel,$__CFG_dbport_devel,$__CFG_dbname_devel,$__CFG_dbuser_devel,$__CFG_dbpass_devel;
//$pgsql = pg_connect("host=" . $__CFG_dbhost_devel . " port=" . $__CFG_dbport_devel . " dbname=" . $__CFG_dbname_devel . " user=" . $__CFG_dbuser_devel . " password=" . $__CFG_dbpass_devel);
//    $db = $GLOBALS["pgsql"];
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    $db = $pgsql;

    $q = "SELECT id_outlet FROM mt_outlet WHERE id_outlet = upper('" . $idoutlet . "') AND pin = crypt(upper(md5('" . $pin . "')), pin) AND is_active=1";

    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
    if ($n > 0)
        return TRUE;
    else
        return FALSE;

    pg_free_result($e);
    pg_close();
    reconnect_devel($db);
}

function outletexists($idoutlet) {
    $db = $GLOBALS["pgsql"];
    $q = "SELECT id_outlet FROM mt_outlet WHERE id_outlet = upper('" . $idoutlet . "') AND is_active=1";
    // echo $q;die();
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
    //die("ini n ".$n);
    if ($n > 0)
        return TRUE;
    else
        return FALSE;

    pg_free_result($e);
    pg_close();
    reconnect($db);
}

function outletexists_devel($idoutlet) {
//   global $__CFG_dbhost_devel,$__CFG_dbport_devel,$__CFG_dbname_devel,$__CFG_dbuser_devel,$__CFG_dbpass_devel;
//$pgsql = pg_connect("host=" . $__CFG_dbhost_devel . " port=" . $__CFG_dbport_devel . " dbname=" . $__CFG_dbname_devel . " user=" . $__CFG_dbuser_devel . " password=" . $__CFG_dbpass_devel);
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    // $db = $GLOBALS["pgsql"];
    $db = $pgsql;
    //var_dump("db ".$__CFG_dbhost_devel);
    $q = "SELECT id_outlet FROM mt_outlet WHERE id_outlet = upper('" . $idoutlet . "') AND is_active=1";

    $e = pg_query($pgsql, $q);
    $n = pg_num_rows($e);
    if ($n > 0)
        return TRUE;
    else
        return FALSE;

    pg_free_result($e);
    pg_close();
    reconnect_devel($pgsql);
}

function status_produk($id_produk){
    $db = $GLOBALS["pgsql"];
    $q = "SELECT harga_jual, is_active, is_gangguan, biaya_admin FROM fmss.mt_produk WHERE id_produk = '$id_produk'";

    $e = pg_query($db,$q);
    $n = pg_num_rows($e);
    $data = "";
    if ($n > 0){
        while($r = pg_fetch_object($e)){
            $data .= $r->harga_jual.'|'.$r->is_gangguan.'|'.$r->biaya_admin.'|'.$r->is_active;
        }
        return $data;
    } else {
        return "";
    }
}

function komisi_produk($id_outlet, $id_produk){
    $db = $GLOBALS["pgsql"];
    $q = "select id_setting_komisi from mt_outlet where id_outlet = '$id_outlet'";
    $e = pg_query($db,$q);
    $n = pg_num_rows($e);
    $data = "";
    if ($n > 0){
        $seting_komisi = "";
        while($r = pg_fetch_object($e)){
            $seting_komisi .= $r->id_setting_komisi;
        }
        $q2 = "select up_harga, fee_transaksi from mt_setting_komisi_detail_produk where id_produk = '$id_produk' and id_setting_komisi = $seting_komisi limit 1";
        $e2 = pg_query($db,$q2);
        $n2 = pg_num_rows($e2);
        if($n2 > 0){
            while($r2 = pg_fetch_object($e2)){
                $data .= $r2->up_harga.'|'.$r->fee_transaksi;
            }
            return $data;
        } else {
            return "";
        }
    } else {
        return "";
    }
    
}

function foruse2($grup, $id_outlet){
    $db = $GLOBALS["pgsql"];

    $qs = "select id_setting_komisi from mt_outlet where id_outlet = '$id_outlet'";
    $e3 = pg_query($db, $qs);
    $no = pg_num_rows($e3);
    if ($no > 0){
        $r3 = pg_fetch_object($e3);
        $setkom = $r3->id_setting_komisi;
    }

    $idgrup_q = "select id_group from mt_outlet where id_outlet = '$id_outlet'";
    $e2 = pg_query($db, $idgrup_q);
    $r2 = pg_fetch_object($e2);
    pg_free_result($e2);
    
    $id_group = $r2->id_group;

    // $q = "select count(*) as jum from group_outlet_produk where id_group = $id_group and id_produk = '$id_produk'";
    $q = "select a.id_produk, a.produk, a.harga_jual, a.is_active, a.is_gangguan, a.biaya_admin, e.up_harga, e.fee_transaksi from mt_produk a 
    left join group_outlet_produk b on a.id_produk = b.id_produk
    left join mt_outlet_group c on c.id_group = b.id_group 
    left join mt_group_produk2 d on a.id_group_produk = d.id_group_produk 
    left join mt_setting_komisi_detail_produk e on e.id_produk = a.id_produk 
    where d.group_produk = '$grup' and b.id_group = $id_group and e.id_setting_komisi = $setkom order by 1 asc";

    $e = pg_query($db,$q);
    $n = pg_num_rows($e);
    $data = array();
    if ($n > 0){
        while($r = pg_fetch_object($e)){
            $data[] = $r;
        }
        if(count($data) == 0){
            return "";
        } else {
            return $data;
        }
    } else {
        return "";
    }
}

function foruse($id_produk, $id_outlet){
    $db = $GLOBALS["pgsql"];

    $idgrup_q = "select id_group from mt_outlet where id_outlet = '$id_outlet'";
    $e2 = pg_query($db, $idgrup_q);
    $r2 = pg_fetch_object($e2);
    pg_free_result($e2);
    
    $id_group = $r2->id_group;

    $q = "select count(*) as jum from group_outlet_produk where id_group = $id_group and id_produk = '$id_produk'";
    $e = pg_query($db,$q);
    $n = pg_num_rows($e);
    $data = "";
    if ($n > 0){
        while($r = pg_fetch_object($e)){
            $data .= $r->jum;
        }
        $ret = true;
        if($data == 0){
            $ret = false;
        }
        return $ret;
    } else {
        return false;
    }
}

function productexists($product) {
    $db = $GLOBALS["pgsql"];

    $q = "SELECT id_produk FROM mt_produk WHERE id_produk = upper('" . $product . "')";

    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
    if ($n > 0)
        return TRUE;
    else
        return FALSE;

    pg_free_result($e);
    pg_close();
    reconnect($db);
}

function productexists_devel($product) {
    // global $__CFG_dbhost_devel,$__CFG_dbport_devel,$__CFG_dbname_devel,$__CFG_dbuser_devel,$__CFG_dbpass_devel;
//$pgsql = pg_connect("host=" . $__CFG_dbhost_devel . " port=" . $__CFG_dbport_devel . " dbname=" . $__CFG_dbname_devel . " user=" . $__CFG_dbuser_devel . " password=" . $__CFG_dbpass_devel);
    // $db = $GLOBALS["pgsql"];
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    $db = $pgsql;

    $q = "SELECT id_produk FROM mt_produk_uat WHERE id_produk = upper('" . $product . "')";

    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
    if ($n > 0)
        return TRUE;
    else
        return FALSE;

    pg_free_result($e);
    pg_close();
    reconnect_devel($db);
}

function isvaliddaterange($tgl1, $tgl2) {
    if ((strlen($tgl1) != 14) || (strlen($tgl2) != 14)){
        return FALSE;
    }
    if(!is_numeric($tgl1) || !is_numeric($tgl2)){
        return FALSE;
    } 
    
    $db = $GLOBALS["pgsql"];
    $tgl1 = str_replace("II", "00", $tgl1);
    $tgl2 = str_replace("II", "00", $tgl2);
    $q = "SELECT CASE WHEN SUM(EXTRACT(EPOCH FROM to_timestamp('" . $tgl2 . "','YYYYMMDDHH24MISS')) - EXTRACT(EPOCH FROM to_timestamp('" . $tgl1 . "','YYYYMMDDHH24MISS'))) >= 0 THEN TRUE ELSE FALSE END as seconds";

    
    $e = pg_query($db, $q);

    while ($data = pg_fetch_object($e)) {
        $out = $data->seconds;
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out == "t" ? TRUE : FALSE;
}

function isvaliddaterange_devel($tgl1, $tgl2) {
   
    if ((strlen($tgl1) != 14) || (strlen($tgl2) != 14))
        return FALSE;
    
//    if(!is_timestamp($tgl1))
//        return FALSE;
//    
//    if(!is_timestamp($tgl2))
//        return FALSE;
    // global $__CFG_dbhost_devel,$__CFG_dbport_devel,$__CFG_dbname_devel,$__CFG_dbuser_devel,$__CFG_dbpass_devel;
//$pgsql = pg_connect("host=" . $__CFG_dbhost_devel . " port=" . $__CFG_dbport_devel . " dbname=" . $__CFG_dbname_devel . " user=" . $__CFG_dbuser_devel . " password=" . $__CFG_dbpass_devel);
    //$db = $GLOBALS["pgsql"];
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    $db = $pgsql;

    $q = "SELECT CASE WHEN SUM(EXTRACT(EPOCH FROM to_timestamp('" . $tgl2 . "','YYYYMMDDHH24MISS')) - EXTRACT(EPOCH FROM to_timestamp('" . $tgl1 . "','YYYYMMDDHH24MISS'))) >= 0 THEN TRUE ELSE FALSE END as seconds";

    $e = pg_query($db, $q);

    while ($data = pg_fetch_object($e)) {
        $out = $data->seconds;
    }
    pg_free_result($e);
    pg_close();
    reconnect_devel($db);
    return $out == "t" ? TRUE : FALSE;
}

function getIdOutlet($hp) {
    $db = $GLOBALS["pgsql"];

    $nohp = trim($hp);
    if (substr($nohp, 0, 2) == "62") {
        $nohp = "0" . substr($nohp, 2, strlen($nohp));
    } else if (substr($nohp, 0, 3) == "+62") {
        $nohp = "0" . substr($nohp, 3, strlen($nohp));
    }

    $q = "SELECT id_outlet FROM fmss.mt_outlet WHERE notelp_pemilik = '" . $nohp . "' LIMIT 1";
    $e = pg_query($db, $q);
    $r = pg_fetch_object($e);
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $r->id_outlet;
}

function getPaymentKAI($idoutlet, $idtrx, $idpel = "") {
    $db = $GLOBALS["pgsql"];
    $q = "
SELECT t.id_transaksi,t.bill_info2, t.response_code, t.keterangan
FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "' AND t.id_produk='WKAI' AND response_code = '00' ";

    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .=" AND t.bill_info2='" . $idpel . "'";

    $q .= " UNION ";

    $q .= "
SELECT t.id_transaksi,t.bill_info2, t.response_code, t.keterangan
FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "' AND t.id_produk='WKAI' AND response_code = '00' ";

    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.bill_info2='" . $idpel . "'";
    $q = $q . " ORDER BY id_transaksi DESC ";

    $e = pg_query($db, $q);
    $n = pg_num_rows($e);

    $out = array();
    $i = 0;
    if ($n > 0) {
        while ($data = pg_fetch_object($e)) {
            $out[$i] = $data;
            $i++;
        }
    } else {
        $qs = "
            SELECT t.id_transaksi,t.bill_info2, t.response_code, t.keterangan
            FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
            WHERE t.jenis_transaksi = 1 
            AND t.id_outlet='" . $idoutlet . "' AND t.id_produk='WKAI'";

        $idtrx != "" ? $qs .= " AND t.id_transaksi='" . $idtrx . "'" : $qs .=" AND t.bill_info2='" . $idpel . "'";

        $qs .= " UNION ";

        $qs .= "
            SELECT t.id_transaksi,t.bill_info2, t.response_code, t.keterangan
            FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
            WHERE t.jenis_transaksi = 1
            AND t.id_outlet='" . $idoutlet . "' AND t.id_produk='WKAI'";

        $idtrx != "" ? $qs .= " AND t.id_transaksi='" . $idtrx . "'" : $qs .= " AND t.bill_info2='" . $idpel . "'";
        $qs = $qs . " ORDER BY id_transaksi DESC LIMIT 1";

        $ex = pg_query($db, $qs);
        $ns = pg_num_rows($ex);

        $out = array();
        $i = 0;
        if ($ns > 0) {
            while ($data = pg_fetch_object($ex)) {
                $out[$i] = $data;
                $i++;
            }
        }
        pg_free_result($ex);
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getDataTransaksi($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2) {
    $db = $GLOBALS["pgsql"];
    $q = "
SELECT t.id_transaksi, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.response_code, t.keterangan
FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "'";
    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.time_request BETWEEN to_timestamp('" . $tgl1 . "','YYYYMMDDHH24MISS') AND to_timestamp('" . $tgl2 . "','YYYYMMDDHH24MISS')";
    $idproduk != "" ? $q .= " AND t.id_produk='" . $idproduk . "'" : $q = $q;
    $idpel != "" ? $q .= " AND t.bill_info1 LIKE '" . $idpel . "'" : $q = $q;

    $q .= " UNION ";

    $q .= "
SELECT t.id_transaksi, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.response_code, t.keterangan
FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "'";
    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.time_request BETWEEN to_timestamp('" . $tgl1 . "','YYYYMMDDHH24MISS') AND to_timestamp('" . $tgl2 . "','YYYYMMDDHH24MISS')";
    $idproduk != "" ? $q .= " AND t.id_produk='" . $idproduk . "'" : $q = $q;
    $idpel != "" ? $q .= " AND t.bill_info1 LIKE '" . $idpel . "'" : $q = $q;
    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT " . $limit : $q = $q) : $q = $q;
   
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);

    $out = array();
    $i = 0;
    if ($n > 0) {
        while ($data = pg_fetch_object($e)) {
            $out[$i] = $data;
            $i++;
        }
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}



function reconnect($conn) {
//    global $conn_prop;
    $conn = pg_connect($GLOBALS["__G_conn_prop"]);
}

function reconnect_devel($conn_devel) {
    //global $pgsql;
    $conn_devel = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    // $conn = pg_connect($pgsql);
}

function retTelepon($params, $frm) {
    array_push($params, (string) $frm->getKodearea());
    array_push($params, (string) (int) $frm->getNomorTelepon());
    array_push($params, (string) $frm->getKodeDivre());
    array_push($params, (string) $frm->getKodeDatel());
    array_push($params, (string) $frm->getJumlahBill());
    array_push($params, (string) trim($frm->getNomorReferensi3()));
    array_push($params, (string) $frm->getNilaiTagihan3());
    array_push($params, (string) trim($frm->getNomorReferensi2()));
    array_push($params, (string) $frm->getNilaiTagihan2());
    array_push($params, (string) trim($frm->getNomorReferensi1()));
    array_push($params, (string) $frm->getNilaiTagihan1());
    array_push($params, (string) trim($frm->getNamaPelanggan()));
    array_push($params, (string) trim($frm->getNPWP()));

    return $params;
}

function retSpeedy($params, $frm) {
    array_push($params, (string) $frm->getKodearea());
    array_push($params, (string) $frm->getNomorTelepon());
    array_push($params, (string) $frm->getKodeDivre());
    array_push($params, (string) $frm->getKodeDatel());
    array_push($params, (string) $frm->getJumlahBill());
    array_push($params, (string) trim($frm->getNomorReferensi3()));
    array_push($params, (string) $frm->getNilaiTagihan3());
    array_push($params, (string) trim($frm->getNomorReferensi2()));
    array_push($params, (string) $frm->getNilaiTagihan2());
    array_push($params, (string) trim($frm->getNomorReferensi1()));
    array_push($params, (string) $frm->getNilaiTagihan1());
    array_push($params, (string) trim($frm->getNamaPelanggan()));
    array_push($params, (string) trim($frm->getNPWP()));

    return $params;
}

function retTelkomVision($params, $frm) {
    array_push($params, (string) $frm->getKodearea());
    array_push($params, (string) $frm->getNomorTelepon());
    array_push($params, (string) $frm->getKodeDivre());
    array_push($params, (string) $frm->getKodeDatel());
    array_push($params, (string) $frm->getJumlahBill());
    array_push($params, (string) trim($frm->getNomorReferensi3()));
    array_push($params, (string) $frm->getNilaiTagihan3());
    array_push($params, (string) trim($frm->getNomorReferensi2()));
    array_push($params, (string) $frm->getNilaiTagihan2());
    array_push($params, (string) trim($frm->getNomorReferensi1()));
    array_push($params, (string) $frm->getNilaiTagihan1());
    array_push($params, (string) trim($frm->getNamaPelanggan()));
    array_push($params, (string) trim($frm->getNPWP()));

    return $params;
}

function retPgn($params, $frm) {
    array_push($params, (string) $frm->getCustomerId());
    array_push($params, (string) $frm->getCustomerName());
    array_push($params, (string) $frm->getUsage());
    array_push($params, (string) $frm->getPeriode());
    array_push($params, (string) $frm->getInvoiceNumber());
    array_push($params, (string) $frm->getTagihan());
    array_push($params, (string) $frm->getAdminBank());
    array_push($params, (string) $frm->getTotal());
    array_push($params, (string) $frm->getCharge());
    array_push($params, (string) $frm->getSaldo());
    array_push($params, (string) $frm->getRefId());
    array_push($params, (string) $frm->getTrxId());
    return $params;
}

function retPajakPbb($params, $frm) {
    array_push($params, (string) $frm->getSwitcherId());
    array_push($params, (string) $frm->getBillerCode());
    array_push($params, (string) $frm->getCustomerID());
    array_push($params, (string) $frm->getBillQuantity());
    array_push($params, (string) $frm->getNoref1());
    array_push($params, (string) $frm->getNoref2());
    array_push($params, (string) $frm->getNtp());
    array_push($params, (string) $frm->getNtb());
    array_push($params, (string) $frm->getKodePemda());
    array_push($params, (string) $frm->getNop());
    array_push($params, (string) $frm->getKodePajak());
    array_push($params, (string) $frm->getTahunPajak());
    array_push($params, (string) $frm->getNama());
    array_push($params, (string) $frm->getLokasi());
    array_push($params, (string) $frm->getKelurahan());
    array_push($params, (string) $frm->getKecamatan());
    array_push($params, (string) $frm->getProvinsi());
    array_push($params, (string) $frm->getLuasTanah());
    array_push($params, (string) $frm->getLuasBangunan());
    array_push($params, (string) $frm->getTanggalJatuhTempo());
    array_push($params, (string) $frm->getTagihan());
    array_push($params, (string) $frm->getDenda());
    array_push($params, (string) $frm->getTotalBayar());

    return $params;
}


function retPLNPostpaid($params, $frm) {
   
    array_push($params, (string) $frm->getSwitcherId());
    array_push($params, (string) $frm->getSubscriberId());
    array_push($params, (string) $frm->getJumlahBill());
    array_push($params, (string) $frm->getPaymentStatus());
    array_push($params, (string) $frm->getTotalOutstandingBill());
    array_push($params, (string) $frm->getSwReferenceNumber());
    array_push($params, (string) trim(encodehtml($frm->getNamaPelanggan())));
    array_push($params, (string) trim($frm->getServiceUnit()));
    array_push($params, (string) trim($frm->getServiceUnitPhone()));
    array_push($params, (string) trim($frm->getSubscriberSegmentation()));
    array_push($params, (string) $frm->getPowerConsumingCategory());
    array_push($params, (string) $frm->getTotalAdminCharge());
    array_push($params, (string) $frm->getBillPeriod1());
    array_push($params, (string) $frm->getDueDate1());
    array_push($params, (string) $frm->getMeterReadDate1());
    array_push($params, (string) $frm->getRpTag1());
    array_push($params, (string) $frm->getIncentive1());
    array_push($params, (string) $frm->getValueAddedTax1());
    array_push($params, (string) $frm->getPenaltyFee1());
    array_push($params, (string) $frm->getPreviousMeterReading11());
    array_push($params, (string) $frm->getCurentMeterReading11());
    array_push($params, (string) $frm->getPreviousMeterReading21());
    array_push($params, (string) $frm->getCurentMeterReading21());
    array_push($params, (string) $frm->getPreviousMeterReading31());
    array_push($params, (string) $frm->getCurentMeterReading31());
    array_push($params, (string) $frm->getBillPeriod2());
    array_push($params, (string) $frm->getDueDate2());
    array_push($params, (string) $frm->getMeterReadDate2());
    array_push($params, (string) $frm->getRpTag2());
    array_push($params, (string) $frm->getIncentive2());
    array_push($params, (string) $frm->getValueAddedTax2());
    array_push($params, (string) $frm->getPenaltyFee2());
    array_push($params, (string) $frm->getPreviousMeterReading12());
    array_push($params, (string) $frm->getCurentMeterReading12());
    array_push($params, (string) $frm->getPreviousMeterReading22());
    array_push($params, (string) $frm->getCurentMeterReading22());
    array_push($params, (string) $frm->getPreviousMeterReading32());
    array_push($params, (string) $frm->getCurentMeterReading32());
    array_push($params, (string) $frm->getBillPeriod3());
    array_push($params, (string) $frm->getDueDate3());
    array_push($params, (string) $frm->getMeterReadDate3());
    array_push($params, (string) $frm->getRpTag3());
    array_push($params, (string) $frm->getIncentive3());
    array_push($params, (string) $frm->getValueAddedTax3());
    array_push($params, (string) $frm->getPenaltyFee3());
    array_push($params, (string) $frm->getPreviousMeterReading13());
    array_push($params, (string) $frm->getCurentMeterReading13());
    array_push($params, (string) $frm->getPreviousMeterReading23());
    array_push($params, (string) $frm->getCurentMeterReading23());
    array_push($params, (string) $frm->getPreviousMeterReading33());
    array_push($params, (string) $frm->getCurentMeterReading33());
    array_push($params, (string) $frm->getBillPeriod4());
    array_push($params, (string) $frm->getDueDate4());
    array_push($params, (string) $frm->getMeterReadDate4());
    array_push($params, (string) $frm->getRpTag4());
    array_push($params, (string) $frm->getIncentive4());
    array_push($params, (string) $frm->getValueAddedTax4());
    array_push($params, (string) $frm->getPenaltyFee4());
    array_push($params, (string) $frm->getPreviousMeterReading14());
    array_push($params, (string) $frm->getCurentMeterReading14());
    array_push($params, (string) $frm->getPreviousMeterReading24());
    array_push($params, (string) $frm->getCurentMeterReading24());
    array_push($params, (string) $frm->getPreviousMeterReading34());
    array_push($params, (string) $frm->getCurentMeterReading34());
    array_push($params, (string) encodehtml(trim($frm->getAlamat())));
    array_push($params, (string) $frm->getPlnNPWP());
    if (substr(strtoupper(trim($frm->getSubscriberNPWP())), 0, 2) == "FA") {
        array_push($params, (string) "");
    } else {
        array_push($params, (string) $frm->getSubscriberNPWP());
    }
    array_push($params, (string) $frm->getTotalRpTag());
    $text1 = str_replace('"','',$frm->getInfoTeks());
    $text2 = str_replace("'","",$text1);
    array_push($params, (string) $text2);

    return $params;
}

function retPLNPrepaid($params, $frm) {
    array_push($params, (string) $frm->getSwitcherId());
    array_push($params, (string) $frm->getNomorMeter());
    array_push($params, (string) $frm->getIdPelanggan());
    array_push($params, (string) $frm->getFlag());
    array_push($params, (string) $frm->getNoRefl());
    array_push($params, (string) $frm->getNoRef2());
    array_push($params, (string) $frm->getVendingReceiptNumber());
    array_push($params, (string) encodehtml(trim($frm->getNamaPelanggan())));
    array_push($params, (string) $frm->getSubscriberSegmentation());
    array_push($params, (string) $frm->getPowerConsumingCategory());
    array_push($params, (string) $frm->getMinorUnitOfAdminCharge());
    array_push($params, (string) $frm->getAdminCharge());
    array_push($params, (string) $frm->getBuyingOption());
    array_push($params, (string) $frm->getDistributionCode());
    array_push($params, (string) $frm->getServiceUnit());
    array_push($params, (string) $frm->getServiceUnitPhone());
    array_push($params, (string) $frm->getMaxKwhLimit());
    array_push($params, (string) $frm->getTotalRepeatUnsoldToken());
    array_push($params, (string) $frm->getUnsold1());
    array_push($params, (string) $frm->getUnsold2());
    array_push($params, (string) $frm->getTokenPln());
    array_push($params, (string) $frm->getMinorUnitStampDuty());
    array_push($params, (string) $frm->getStampDuty());
    array_push($params, (string) $frm->getMinorUnitPPN());
    array_push($params, (string) $frm->getPPN());
    array_push($params, (string) $frm->getMinorUnitPPJ());
    array_push($params, (string) $frm->getPPJ());
    array_push($params, (string) $frm->getMinorUnitCustomerPayablesInstallment());
    array_push($params, (string) $frm->getCustomerPayablesInstallment());
    array_push($params, (string) $frm->getMinorUnitOfPowerPurchase());
    array_push($params, (string) $frm->getPowerPurchase());
    array_push($params, (string) $frm->getMinorUnitOfPurchasedKWHUnit());
    array_push($params, (string) $frm->getPurchasedKWHUnit());
    $text1 = str_replace('"','',$frm->getInfoText());
    $text2 = str_replace("'","",$text1);
    array_push($params, (string) $text2);

    return $params;
}

function retPLNNontaglist($params, $frm) {
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getRegistrationNumber()));
    array_push($params, (string) trim($frm->getAreaCode()));
    array_push($params, (string) trim($frm->getTransactionCode()));
    array_push($params, (string) trim($frm->getTransactionName()));
    array_push($params, (string) trim($frm->getRegistrationDate()));
    array_push($params, (string) trim($frm->getExpirationDate()));
    array_push($params, (string) trim($frm->getSubscriberId()));
    array_push($params, (string) trim($frm->getSubscriberName()));
    array_push($params, (string) trim($frm->getPlnRefNumber()));
    array_push($params, (string) trim($frm->getSwRefNumber()));
    array_push($params, (string) trim($frm->getServiceUnit()));
    array_push($params, (string) trim($frm->getServiceUnitAddress()));
    array_push($params, (string) trim($frm->getServiceUnitPhone()));
    array_push($params, (string) trim($frm->getTotalTransactionAmountMinor()));
    array_push($params, (string) trim($frm->getTotalTransactionAmount()));
    array_push($params, (string) trim($frm->getPlnBillMinorUnit()));
    array_push($params, (string) trim($frm->getPlnBillValue()));    
    array_push($params, (string) trim($frm->getAdminChargeMinorUnit()));
    array_push($params, (string) trim($frm->getAdminCharge()));
    array_push($params, (string) trim($frm->getMutationNumber()));
    array_push($params, (string) trim($frm->getSubscriberSegmentation()));
    array_push($params, (string) trim($frm->getPowerConsumingCategory()));
    array_push($params, (string) trim($frm->getInqReferanceNumber()));
    array_push($params, (string) trim($frm->getTitakRepeat()));
    array_push($params, (string) trim($frm->getCustomerDetailCode1()));
    array_push($params, (string) trim($frm->getCustomDetailMinorUnit1()));
    array_push($params, (string) trim($frm->getCustomDetailValueAmount1()));
    array_push($params, (string) trim($frm->getCustomerDetailCode2()));
    array_push($params, (string) trim($frm->getCustomDetailMinorUnit2()));
    array_push($params, (string) trim($frm->getCustomDetailValueAmount2()));
    array_push($params, (string) trim($frm->getInfoText()));

    return $params;
}

function retPonselPostpaid($params, $frm) {
    array_push($params, (string) $frm->getSwitcherId());
    array_push($params, (string) $frm->getBillerCode());
    array_push($params, (string) $frm->getCustomerId());
    array_push($params, (string) $frm->getBillQuantity());
    array_push($params, (string) $frm->getNoref1());
    array_push($params, (string) $frm->getNoref2());
    array_push($params, (string) $frm->getCustomerName());
    array_push($params, (string) $frm->getCustomerAddress());
    array_push($params, (string) $frm->getBillerAdminCharge());
    array_push($params, (string) $frm->getTotalBillAmount());
    array_push($params, (string) $frm->getProviderName());
    array_push($params, (string) $frm->getMonthPeriod1());
    array_push($params, (string) $frm->getYearPeriod1());
    array_push($params, (string) $frm->getPenalty1());
    array_push($params, (string) $frm->getBillAmount1());
    array_push($params, (string) $frm->getMiscAmount1());
    array_push($params, (string) $frm->getMonthPeriod2());
    array_push($params, (string) $frm->getYearPeriod2());
    array_push($params, (string) $frm->getPenalty2());
    array_push($params, (string) $frm->getBillAmount2());
    array_push($params, (string) $frm->getMiscAmount2());
    array_push($params, (string) $frm->getMonthPeriod3());
    array_push($params, (string) $frm->getYearPeriod3());
    array_push($params, (string) $frm->getPenalty3());
    array_push($params, (string) $frm->getBillAmount3());
    array_push($params, (string) $frm->getMiscAmount3());

    return $params;
}

function retPAM($params, $frm) {
    //handle wasmg
    if($frm->getKodeProduk() == "WASMG"){
        $arr_misc_amount = explode("|", trim($frm->getMiscAmount1()));
        $misc_amount = intval(trim($arr_misc_amount[0])) + intval(trim($arr_misc_amount[1])) + intval(trim($arr_misc_amount[2])) + intval(trim($arr_misc_amount[3])) + intval(trim($arr_misc_amount[4])) + intval(trim($arr_misc_amount[5]));

        $arr_misc_amount2 = explode("|", trim($frm->getMiscAmount2()));
        $misc_amount2 = intval(trim($arr_misc_amount2[0])) + intval(trim($arr_misc_amount2[1])) + intval(trim($arr_misc_amount2[2])) + intval(trim($arr_misc_amount2[3])) + intval(trim($arr_misc_amount2[4])) + intval(trim($arr_misc_amount2[5]));

        $arr_misc_amount3 = explode("|", trim($frm->getMiscAmount3()));
        $misc_amount3 = intval(trim($arr_misc_amount3[0])) + intval(trim($arr_misc_amount3[1])) + intval(trim($arr_misc_amount3[2])) + intval(trim($arr_misc_amount3[3])) + intval(trim($arr_misc_amount3[4])) + intval(trim($arr_misc_amount3[5]));
    }

    //handle WAKOPASU 
    $misc1 = explode("|", trim($frm->getMiscAmount1()));
    $misc_amount_kopasu = intval(trim($misc1[0]));


    $meter1 = explode("|", trim($frm->getLastMeterRead1()));
    $meter1_kopasu = intval(trim($meter1[0]));
    $add_data_kopasu = "tarif1=".intval(trim($meter1[1])).";tarif2=".intval(trim($meter1[2])).";meter1=".intval(trim($meter1[3])).";meter2=".intval(trim($meter1[4]));
    $add_data_kopasu .= ";beban=".intval(trim($misc1[1])).";rpPerMeter1=".intval(trim($misc1[2])).";rpPerMeter2=".intval(trim($misc1[3])).";adminBiller=".intval(trim($misc1[4])).";danaMeter=".intval(trim($misc1[5]));
    $add_data_kopasu .= ";ppn=".intval($misc1[7]).";miss_amount=".intval($misc1[0]);
    //handle WAKOPASU
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    array_push($params, (string) trim($frm->getCustomerID1()));
    array_push($params, (string) trim($frm->getCustomerID2()));
    array_push($params, (string) trim($frm->getCustomerID3()));
    array_push($params, (string) trim($frm->getBillQuantity()));
    array_push($params, (string) trim($frm->getNoref1()));
    array_push($params, (string) trim($frm->getNoref2()));
    if($frm->getMember() == "HH74694"){
    array_push($params, (string) (trim($frm->getCustomerName())));
    }else{
    array_push($params, (string) encodehtml(trim($frm->getCustomerName())));
    }
    array_push($params, (string) encodehtml(trim($frm->getCustomerAddress())));

    if (strtolower(trim($frm->getCustomerDetailInformation())) == "cust detail info pindah ke bill_info70" || $frm->getKodeProduk() == "WAKSR") {
        array_push($params, (string) "");
    } else if($frm->getKodeProduk() == "WASMG"){
        
        $arr_misc_amount1 = explode("|", trim($frm->getMiscAmount1()));
        $arr_misc_amount2 = explode("|", trim($frm->getMiscAmount2()));
        $arr_misc_amount3 = explode("|", trim($frm->getMiscAmount3()));

        $customer_detail_information = "";
        $customer_detail_information .=
            "nonair_1:".$arr_misc_amount1[0].";".
            "beban_1:".$arr_misc_amount1[1].";".
            "sampah_1:".$arr_misc_amount1[2].";".
            "danameter_1:".$arr_misc_amount1[3].";".
            "adminbiller_1:".$arr_misc_amount1[4].";".
            "valueaddedtax_1:".$arr_misc_amount1[5].";";
        if(trim($frm->getMiscAmount2()) != ""){
            $customer_detail_information .= "|".
                "nonair_2:".$arr_misc_amount2[0].";".
                "beban_2:".$arr_misc_amount2[1].";".
                "sampah_2:".$arr_misc_amount2[2].";".
                "danameter_2:".$arr_misc_amount2[3].";".
                "adminbiller_2:".$arr_misc_amount2[4].";".
                "valueaddedtax_2:".$arr_misc_amount2[5].";";
        }
        if(trim($frm->getMiscAmount3()) != ""){
            $customer_detail_information .= "|".
                "nonair_3:".$arr_misc_amount3[0].";".
                "beban_3:".$arr_misc_amount3[1].";".
                "sampah_3:".$arr_misc_amount3[2].";".
                "danameter_3:".$arr_misc_amount3[3].";".
                "adminbiller_3:".$arr_misc_amount3[4].";".
                "valueaddedtax_3:".$arr_misc_amount3[5].";";
        }

        array_push($params, (string) $customer_detail_information);
        
    } else if($frm->getKodeProduk() == "WAKOPASU"){
        array_push($params, (string) $add_data_kopasu);
    } else if($frm->getKodeProduk() == "WAHSU"){
        array_push($params, (string) "");
    } else {
        array_push($params, (string) trim($frm->getCustomerDetailInformation()));
    }

    array_push($params, (string) trim($frm->getBillerAdminCharge()));
    array_push($params, (string) trim($frm->getTotalBillAmount()));
    array_push($params, (string) trim($frm->getPDAMName()));
    array_push($params, (string) trim($frm->getMonthPeriod1()));
    array_push($params, (string) trim($frm->getYearPeriod1()));
    array_push($params, (string) trim($frm->getFirstMeterRead1()));
    //array_push($params, (string) trim($frm->getLastMeterRead1()));//ini kopasu

    if($frm->getKodeProduk() == "WAKOPASU"){
        array_push($params, (string) $meter1_kopasu);
    } else {
        array_push($params, (string) trim($frm->getLastMeterRead1()));
    }

    array_push($params, (string) trim($frm->getPenalty1()));
    if($frm->getKodeProduk() == "WASMG"){
        array_push($params, (string) trim($frm->getBillAmount1() + $misc_amount));
    } else {
        array_push($params, (string) trim($frm->getBillAmount1()));
    }
    

    $arr_member = array("HH18706", "HH56482", "HH80079");// VSI, cv.sekawan, mitrawisata
    // if ($frm->getKodeProduk() == "WABONDO" && ($frm->getMember() == "HH18706" || $frm->getMember() == "HH56482" )) {
    if ($frm->getKodeProduk() == "WABONDO" && in_array($frm->getMember(), $arr_member)) {// VSI, cv.sekawan, mitrawisata
        $arr_misc_amount = explode("|", trim($frm->getMiscAmount1()));
        $misc_amount = intval(trim($arr_misc_amount[0])) + intval(trim($arr_misc_amount[1]));
        array_push($params, (string) $misc_amount);
    } else if($frm->getKodeProduk() == "WASMG"){
        $arr_misc_amount = explode("|", trim($frm->getMiscAmount1()));
        $misc_amount = intval(trim($arr_misc_amount[0])) + intval(trim($arr_misc_amount[1])) + intval(trim($arr_misc_amount[2])) + intval(trim($arr_misc_amount[3])) + intval(trim($arr_misc_amount[4])) + intval(trim($arr_misc_amount[5]));
        // array_push($params, (string) $misc_amount);
        array_push($params, (string) '0');
    } else if($frm->getKodeProduk() == "WAKOPASU"){
        array_push($params, (string) $misc_amount_kopasu);
    } else if($frm->getKodeProduk() == "WASITU"){
        $arr_misc_amount = explode("|", trim($frm->getMiscAmount1()));
        $misc_amount = intval(trim($arr_misc_amount[0])) + intval(trim($arr_misc_amount[1]));
        array_push($params, (string) $misc_amount);
    }else if($frm->getKodeProduk() == "WAMEDAN"){
        array_push($params, (string) '0');
    } else {
        array_push($params, (string) trim($frm->getMiscAmount1()));
    }

    array_push($params, (string) trim($frm->getMonthPeriod2()));
    array_push($params, (string) trim($frm->getYearPeriod2()));
    array_push($params, (string) trim($frm->getFirstMeterRead2()));
    array_push($params, (string) trim($frm->getLastMeterRead2()));
    array_push($params, (string) trim($frm->getPenalty2()));
    
    if($frm->getKodeProduk() == "WASMG"){
        array_push($params, (string) trim($frm->getBillAmount2() + $misc_amount2));
    } else {
       
            array_push($params, (string) trim($frm->getBillAmount2()));
        
    }

    //array_push($params, (string) trim($frm->getMiscAmount2()));
    if($frm->getKodeProduk() == "WASMG"){
        $arr_misc_amount2 = explode("|", trim($frm->getMiscAmount2()));
        $misc_amount2 = intval(trim($arr_misc_amount2[0])) + intval(trim($arr_misc_amount2[1])) + intval(trim($arr_misc_amount2[2])) + intval(trim($arr_misc_amount2[3])) + intval(trim($arr_misc_amount2[4])) + intval(trim($arr_misc_amount2[5]));
        // array_push($params, (string) $misc_amount2);
        array_push($params, (string) '0');
    } else if($frm->getKodeProduk() == "WASITU"){
        $arr_misc_amount = explode("|", trim($frm->getMiscAmount2()));
        $misc_amount = intval(trim($arr_misc_amount[0])) + intval(trim($arr_misc_amount[1]));
        array_push($params, (string) $misc_amount);
    } else {
        if($frm->getKodeProduk() == "WAMEDAN"){
            array_push($params, (string) '0');
        }else{
            array_push($params, (string) trim($frm->getMiscAmount2()));
        }
    }
    array_push($params, (string) trim($frm->getMonthPeriod3()));
    array_push($params, (string) trim($frm->getYearPeriod3()));
    array_push($params, (string) trim($frm->getFirstMeterRead3()));
    array_push($params, (string) trim($frm->getLastMeterRead3()));
    array_push($params, (string) trim($frm->getPenalty3()));
    
    if($frm->getKodeProduk() == "WASMG"){
        array_push($params, (string) trim($frm->getBillAmount3() + $misc_amount3));
    } else {
        array_push($params, (string) trim($frm->getBillAmount3()));    
    }

    // array_push($params, (string) trim($frm->getMiscAmount3()));
    if($frm->getKodeProduk() == "WASMG"){
        $arr_misc_amount3 = explode("|", trim($frm->getMiscAmount3()));
        $misc_amount3 = intval(trim($arr_misc_amount3[0])) + intval(trim($arr_misc_amount3[1])) + intval(trim($arr_misc_amount3[2])) + intval(trim($arr_misc_amount3[3])) + intval(trim($arr_misc_amount3[4])) + intval(trim($arr_misc_amount3[5]));
        // array_push($params, (string) $misc_amount3);
        array_push($params, (string) '0');
    } else if($frm->getKodeProduk() == "WASITU"){
        $arr_misc_amount = explode("|", trim($frm->getMiscAmount3()));
        $misc_amount = intval(trim($arr_misc_amount[0])) + intval(trim($arr_misc_amount[1]));
        array_push($params, (string) $misc_amount);
    } else {
        if($frm->getKodeProduk() == "WAMEDAN"){
            array_push($params, (string) '0');
        }else{
            array_push($params, (string) trim($frm->getMiscAmount3()));
        }
    }
    array_push($params, (string) trim($frm->getMonthPeriod4()));
    array_push($params, (string) trim($frm->getYearPeriod4()));
    array_push($params, (string) trim($frm->getFirstMeterRead4()));
    array_push($params, (string) trim($frm->getLastMeterRead4()));
    array_push($params, (string) trim($frm->getPenalty4()));
    array_push($params, (string) trim($frm->getBillAmount4()));
    if($frm->getKodeProduk() == "WAMEDAN"){
        array_push($params, (string) '0');
    }else{
        array_push($params, (string) trim($frm->getMiscAmount4()));
    }
    array_push($params, (string) trim($frm->getMonthPeriod5()));
    array_push($params, (string) trim($frm->getYearPeriod5()));
    array_push($params, (string) trim($frm->getFirstMeterRead5()));
    array_push($params, (string) trim($frm->getLastMeterRead5()));
    array_push($params, (string) trim($frm->getPenalty5()));
    array_push($params, (string) trim($frm->getBillAmount5()));
    if($frm->getKodeProduk() == "WAMEDAN"){
        array_push($params, (string) '0');
    }else{
        array_push($params, (string) trim($frm->getMiscAmount5()));
    }
    array_push($params, (string) trim($frm->getMonthPeriod6()));
    array_push($params, (string) trim($frm->getYearPeriod6()));
    array_push($params, (string) trim($frm->getFirstMeterRead6()));
    array_push($params, (string) trim($frm->getLastMeterRead6()));
    array_push($params, (string) trim($frm->getPenalty6()));
    array_push($params, (string) trim($frm->getBillAmount6()));
    if($frm->getKodeProduk() == "WAMEDAN"){
        array_push($params, (string) '0');
    }else{
        array_push($params, (string) trim($frm->getMiscAmount6()));
    }

    return $params;
}

function retAORATV($params, $frm) {
    array_push($params, (string) $frm->getSwitcherId());
    array_push($params, (string) $frm->getBillerCode());
    array_push($params, (string) $frm->getCustomerID());
    array_push($params, (string) $frm->getBillQuantity());
    array_push($params, (string) $frm->getNoref1());
    array_push($params, (string) $frm->getNoref2());
    array_push($params, (string) $frm->getCustomerName());
    array_push($params, (string) $frm->getCustomerAddress());
    array_push($params, (string) $frm->getProductCategory());
    array_push($params, (string) $frm->getBillAmount());
    array_push($params, (string) $frm->getPenalty());
    array_push($params, (string) $frm->getStampDuty());
    array_push($params, (string) $frm->getPPN());
    array_push($params, (string) $frm->getAdminCharge());
    array_push($params, (string) $frm->getBillerRefNumber());
    array_push($params, (string) $frm->getPTName());
    array_push($params, (string) $frm->getBillerAdminFee());
    array_push($params, (string) $frm->getMiscFee());
    array_push($params, (string) $frm->getMiscNumber());
    array_push($params, (string) $frm->getPeriode());
    array_push($params, (string) $frm->getDueDate());
    array_push($params, (string) $frm->getCustomInfo1());
    array_push($params, (string) $frm->getCustomInfo2());
    array_push($params, (string) $frm->getCustomInfo3());

    return $params;
}

function retTvKabel($params, $frm) {
    array_push($params, (string) $frm->getSwitcherId());
    array_push($params, (string) $frm->getBillerCode());
    array_push($params, (string) $frm->getCustomerID());
    array_push($params, (string) $frm->getBillQuantity());
    array_push($params, (string) $frm->getNoref1());
    array_push($params, (string) $frm->getNoref2());
    array_push($params, (string) $frm->getCustomerName());
    array_push($params, (string) $frm->getCustomerAddress());
    array_push($params, (string) $frm->getProductCategory());
    array_push($params, (string) $frm->getBillAmount());
    array_push($params, (string) $frm->getPenalty());
    array_push($params, (string) $frm->getStampDuty());
    array_push($params, (string) $frm->getPPN());
    array_push($params, (string) $frm->getAdminCharge());
    array_push($params, (string) $frm->getBillerRefNumber());
    array_push($params, (string) $frm->getPTName());
    array_push($params, (string) $frm->getBillerAdminFee());
    array_push($params, (string) $frm->getMiscFee());
    array_push($params, (string) $frm->getMiscNumber());
    array_push($params, (string) $frm->getPeriode());
    array_push($params, (string) $frm->getDueDate());
    array_push($params, (string) $frm->getCustomInfo1());
    array_push($params, (string) $frm->getCustomInfo2());
    array_push($params, (string) $frm->getCustomInfo3());

    return $params;
}

function retMultifinance($params, $frm) {
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    array_push($params, (string) trim($frm->getCustomerID()));
    array_push($params, (string) trim($frm->getBillQuantity()));
    array_push($params, (string) trim($frm->getNoref1()));
    array_push($params, (string) trim($frm->getNoref2()));
    array_push($params, (string) trim($frm->getCustomerName()));
    array_push($params, (string) trim($frm->getProductCategory()));
    array_push($params, (string) trim($frm->getMinorUnit()));
    array_push($params, (string) trim($frm->getBillAmount()));
    array_push($params, (string) trim($frm->getStampDuty()));
    array_push($params, (string) trim($frm->getPPN()));
    array_push($params, (string) trim($frm->getAdminCharge()));
    array_push($params, (string) trim($frm->getBillerRefNumber()));
    array_push($params, (string) trim($frm->getPTName()));
    array_push($params, (string) trim($frm->getBranchName()));
    array_push($params, (string) trim($frm->getItemMerkType()));
    array_push($params, (string) trim($frm->getChasisNumber()));
    array_push($params, (string) trim($frm->getCarNumber()));
    array_push($params, (string) trim($frm->getTenor()));
    array_push($params, (string) trim($frm->getLastPaidPeriode()));
    array_push($params, (string) trim($frm->getLastPaidDueDate()));
    array_push($params, (string) trim($frm->getOSInstallmentAmount()));
    array_push($params, (string) trim($frm->getODInstallmentPeriod()));
    array_push($params, (string) trim($frm->getODInstallmentAmount()));
    if($frm->getKodeProduk() === "FNBAF" && $frm->getMember() === "HH10632"){
        array_push($params, (string) "");
    } else {
        array_push($params, (string) trim($frm->getODPenaltyFee()));
    }
    array_push($params, (string) trim($frm->getBillerAdminFee()));
    array_push($params, (string) trim($frm->getMiscFee()));
    array_push($params, (string) trim($frm->getMinimumPayAmount()));
    array_push($params, (string) trim($frm->getMaximumPayAmount()));

    return $params;
}

function retBillpayment($params, $frm) {
    $ar = json_decode(trim($frm->getNoref1()));
    $name = trim($frm->getCustomerName());
    $invoiceid = trim($frm->getBillerRefNumber());
    $alamat = trim($frm->getCarNumber());
    $admcharge = trim($frm->getAdminCharge());
    $arData = $ar->data;
    $arBill = $arData->bill_item;
    $output = array();
    foreach($arBill as $tglperiode => $value){
        $output[] = array(
            'periode' => $tglperiode,
            'detail' => $value
        );
    }

    // print_r($frm);die('s');
    // // echo json_encode($output);
    array_push($params, $name);
    array_push($params, $alamat);
    array_push($params, $invoiceid);
    array_push($params, $admcharge);
    array_push($params, $output);
    return $params;
    // die('x');
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    array_push($params, (string) trim($frm->getCustomerID()));
    array_push($params, (string) trim($frm->getBillQuantity()));
    array_push($params, (string) trim($frm->getNoref1()));
    array_push($params, (string) trim($frm->getNoref2()));
    array_push($params, (string) trim($frm->getCustomerName()));
    array_push($params, (string) trim($frm->getProductCategory()));
    array_push($params, (string) trim($frm->getMinorUnit()));
    array_push($params, (string) trim($frm->getBillAmount()));
    array_push($params, (string) trim($frm->getStampDuty()));
    array_push($params, (string) trim($frm->getPPN()));
    array_push($params, (string) trim($frm->getAdminCharge()));
    array_push($params, (string) trim($frm->getBillerRefNumber()));
    array_push($params, (string) trim($frm->getPTName()));
    array_push($params, (string) trim($frm->getBranchName()));
    array_push($params, (string) trim($frm->getItemMerkType()));
    array_push($params, (string) trim($frm->getChasisNumber()));
    array_push($params, (string) trim($frm->getCarNumber()));
    array_push($params, (string) trim($frm->getTenor()));
    array_push($params, (string) trim($frm->getLastPaidPeriode()));
    array_push($params, (string) trim($frm->getLastPaidDueDate()));
    array_push($params, (string) trim($frm->getOSInstallmentAmount()));
    array_push($params, (string) trim($frm->getODInstallmentPeriod()));
    array_push($params, (string) trim($frm->getODInstallmentAmount()));
    array_push($params, (string) trim($frm->getODPenaltyFee()));
    array_push($params, (string) trim($frm->getBillerAdminFee()));
    array_push($params, (string) trim($frm->getMiscFee()));
    array_push($params, (string) trim($frm->getMinimumPayAmount()));
    array_push($params, (string) trim($frm->getMaximumPayAmount()));

    return $params;
}

function retAsuransi($params, $frm) {
    $biller_ref_number = trim($frm->getBillerRefNumber());
    $bill_amount = trim($frm->getBillAmount());
    if($frm->getMember() === 'HH74694'){ //indomaret
        $getProductCategory = $frm->getProductCategory() == "" ? "0" : $frm->getProductCategory();
    } else {
        if ($frm->getKodeProduk() == "ASRJWSI" || $frm->getKodeProduk() == "ASRJWS" || $frm->getKodeProduk() == "ASRIFGI") {
            $getProductCategory = "";
        } else {
            $getProductCategory = $frm->getProductCategory();    
        }
    }
    if ($frm->getKodeProduk() == "ASRJWSI" || $frm->getKodeProduk() == "ASRJWS" || $frm->getKodeProduk() == "ASRIFGI") {
        if($frm->getMember() === 'HH74694'){
            $data = array();
            $arr_tagihan = explode("|", trim($frm->getBillerRefNumber()));
            $biller_ref_number = $arr_tagihan[0];
            // echo $biller_ref_number;
            if (strpos($biller_ref_number, 'PREMI') === false) {
                $arr_biller_ref_number = explode(",", $biller_ref_number);
                foreach($arr_biller_ref_number as $num => &$value){
                    if($num === 1){
                        $value = "PREMI : ".$value; 
                    }
                    if($num === 2){
                        $value = "PRM.".$value; 
                    }
                    $data[] = $value;
                }
                if($frm->getKodeProduk() == "ASRJWSI" || $frm->getKodeProduk() == "ASRJWS"){
                    $biller_ref_number = implode(',',$data);
                }else{
                    $biller_ref_number = $frm->getCustomerPhoneNumber();
                }
            }
            // echo "  ||  ".$biller_ref_number;
            $arr_biller_ref_number = explode(",", $biller_ref_number);
            $bill_amount = intval($arr_biller_ref_number[3]);
            if($frm->getKodeProduk() == "ASRIFGI")
            {
                $bill_amount = trim($frm->getBillAmount());
            }else{
                if ($frm->getCommand() == "TAGIHAN") {
                    $params[5] = (string) $bill_amount;
                } else {
                    $bill_amount = trim($frm->getBillAmount());
                }
            }
        } else {
            $arr_tagihan = explode("|", trim($frm->getBillerRefNumber()));
            $biller_ref_number = $arr_tagihan[0];
            
            $arr_biller_ref_number = explode(",", $biller_ref_number);

            $bill_amount = intval($arr_biller_ref_number[3]);
            if ($frm->getCommand() == "TAGIHAN") {
                $params[5] = (string) $bill_amount;
            } else {
                $bill_amount = trim($frm->getBillAmount());
            }
        }
    }

    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    if($frm->getMember() === 'HH146493' && $frm->getKodeProduk() == "ASRCAR"){ //traveloka
        array_push($params, (string) trim(substr($frm->getCustomerID(),5)));
    } else {
        array_push($params, (string) trim($frm->getCustomerID()));
    }
    array_push($params, (string) trim($frm->getBillQuantity()));
    array_push($params, (string) trim($frm->getNoref1()));
    array_push($params, (string) trim($frm->getNoref2()));
    array_push($params, (string) trim($frm->getCustomerName()));
    array_push($params, (string) trim($getProductCategory));
    //array_push($params, (string) trim($frm->getBillAmount()));
    array_push($params, (string) trim($bill_amount));
    array_push($params, (string) trim($frm->getPenalty()));
    array_push($params, (string) trim($frm->getStampDuty()));
    array_push($params, (string) trim($frm->getPPN()));
    array_push($params, (string) trim($frm->getAdminCharge()));
    array_push($params, (string) trim($frm->getClaimAmount()));
    //array_push($params, (string) trim($frm->getBillerRefNumber()));
    array_push($params, (string) trim($biller_ref_number));
    array_push($params, (string) trim($frm->getPTName()));
    array_push($params, (string) trim($frm->getLastPaidPeriode()));
    array_push($params, (string) trim($frm->getLastPaidDueDate()));
    array_push($params, (string) trim($frm->getBillerAdminFee()));
    array_push($params, (string) trim($frm->getMiscFee()));
    array_push($params, (string) trim($frm->getMiscNumber()));
    array_push($params, (string) trim($frm->getCustomerPhoneNumber()));
    array_push($params, (string) trim($frm->getCustomerAddress()));
    array_push($params, (string) trim($frm->getAhliWarisPhoneNumber()));
    array_push($params, (string) trim($frm->getAhliWarisAddress()));
    array_push($params, (string) trim($frm->getCabang()));

    return $params;
}

function retGas($params, $frm){
    array_push($params, (string) trim($frm->getSWITCHERID()));
    array_push($params, (string) trim($frm->getBILLERCODE()));
    array_push($params, (string) trim($frm->getCUSTOMERID1()));
    array_push($params, (string) trim($frm->getCUSTOMERID2()));
    array_push($params, (string) trim($frm->getCUSTOMERID3()));
    array_push($params, (string) trim($frm->getBILLQUANTITY()));
    array_push($params, (string) trim($frm->getGWREFNUM()));
    array_push($params, (string) trim($frm->getSWREFNUM()));
    array_push($params, (string) trim($frm->getCUSTOMERNAME()));
    array_push($params, (string) trim($frm->getCUSTOMERADDRESS()));
    array_push($params, (string) trim($frm->getCUSTOMERDETAILINFORMATION()));
    array_push($params, (string) trim($frm->getBILLERADMINCHARGE()));
    array_push($params, (string) trim($frm->getTOTALBILLAMOUNT()));
    array_push($params, (string) trim($frm->getPDAMNAME()));
    array_push($params, (string) trim($frm->getMONTHPERIOD1()));
    array_push($params, (string) trim($frm->getYEARPERIOD1()));
    array_push($params, (string) trim($frm->getFIRSTMETERREAD1()));
    array_push($params, (string) trim($frm->getLASTMETERREAD1()));
    array_push($params, (string) trim($frm->getPENALTY1()));
    array_push($params, (string) trim($frm->getBILLAMOUNT1()));
    array_push($params, (string) trim($frm->getMISCAMOUNT1()));
    array_push($params, (string) trim($frm->getMONTHPERIOD2()));
    array_push($params, (string) trim($frm->getYEARPERIOD2()));
    array_push($params, (string) trim($frm->getFIRSTMETERREAD2()));
    array_push($params, (string) trim($frm->getLASTMETERREAD2()));
    array_push($params, (string) trim($frm->getPENALTY2()));
    array_push($params, (string) trim($frm->getBILLAMOUNT2()));
    array_push($params, (string) trim($frm->getMISCAMOUNT2()));
    array_push($params, (string) trim($frm->getMONTHPERIOD3()));
    array_push($params, (string) trim($frm->getYEARPERIOD3()));
    array_push($params, (string) trim($frm->getFIRSTMETERREAD3()));
    array_push($params, (string) trim($frm->getLASTMETERREAD3()));
    array_push($params, (string) trim($frm->getPENALTY3()));
    array_push($params, (string) trim($frm->getBILLAMOUNT3()));
    array_push($params, (string) trim($frm->getMISCAMOUNT3()));
    array_push($params, (string) trim($frm->getMONTHPERIOD4()));
    array_push($params, (string) trim($frm->getYEARPERIOD4()));
    array_push($params, (string) trim($frm->getFIRSTMETERREAD4()));
    array_push($params, (string) trim($frm->getLASTMETERREAD4()));
    array_push($params, (string) trim($frm->getPENALTY4()));
    array_push($params, (string) trim($frm->getBILLAMOUNT4()));
    array_push($params, (string) trim($frm->getMISCAMOUNT4()));
    array_push($params, (string) trim($frm->getMONTHPERIOD5()));
    array_push($params, (string) trim($frm->getYEARPERIOD5()));
    array_push($params, (string) trim($frm->getFIRSTMETERREAD5()));
    array_push($params, (string) trim($frm->getLASTMETERREAD5()));
    array_push($params, (string) trim($frm->getPENALTY5()));
    array_push($params, (string) trim($frm->getBILLAMOUNT5()));
    array_push($params, (string) trim($frm->getMISCAMOUNT5()));
    array_push($params, (string) trim($frm->getMONTHPERIOD6()));
    array_push($params, (string) trim($frm->getYEARPERIOD6()));
    array_push($params, (string) trim($frm->getFIRSTMETERREAD6()));
    array_push($params, (string) trim($frm->getLASTMETERREAD6()));
    array_push($params, (string) trim($frm->getPENALTY6()));
    array_push($params, (string) trim($frm->getBILLAMOUNT6()));
    array_push($params, (string) trim($frm->getMISCAMOUNT6()));
    return $params;
}

function retZakat($params, $frm){
    array_push($params, (string) trim($frm->getProviderName()));
    array_push($params, (string) trim($frm->getNamaProduk()));
    array_push($params, (string) trim($frm->getNoKtp()));
    array_push($params, (string) trim($frm->getNoTelp()));
    array_push($params, (string) trim($frm->getNama()));
    array_push($params, (string) trim($frm->getAlamat()));
    array_push($params, (string) trim($frm->getIdProp()));
    array_push($params, (string) trim($frm->getIdKota()));
    array_push($params, (string) trim($frm->getKodePos()));
    array_push($params, (string) trim($frm->getWaktuSurveyAwal1()));
    array_push($params, (string) trim($frm->getWaktuSurveyAkhir1()));
    array_push($params, (string) trim($frm->getWaktuSurveyAwal2()));
    array_push($params, (string) trim($frm->getWaktuSurveyAkhir2()));
    array_push($params, (string) trim($frm->getWaktuSurveyAwal3()));
    array_push($params, (string) trim($frm->getWaktuSurveyAkhir3()));
    array_push($params, (string) trim($frm->getContactPerson()));
    array_push($params, (string) trim($frm->getKecamatan()));
    array_push($params, (string) trim($frm->getDesa()));
    return $params;
}

function retNewAsuransi($params, $frm){
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    array_push($params, (string) trim($frm->getCustomerId()));
    array_push($params, (string) trim($frm->getBillQuantity()));
    array_push($params, (string) trim($frm->getNoRef1()));
    array_push($params, (string) trim($frm->getNoRef2()));
    array_push($params, (string) trim($frm->getCustomerName()));    
    array_push($params, (string) trim($frm->getProdukKategori()));
    array_push($params, (string) trim($frm->getBillAmount()));
    array_push($params, (string) trim($frm->getPenalty()));
    array_push($params, (string) trim($frm->getStampDuty()));
    array_push($params, (string) trim($frm->getPPN()));
    array_push($params, (string) trim($frm->getAdminCharge()));
    array_push($params, (string) trim($frm->getClaimAmount()));
    array_push($params, (string) trim($frm->getBillerRefNum()));
    array_push($params, (string) trim($frm->getPtName()));
    array_push($params, (string) trim($frm->getLastPaidPeriode()));
    array_push($params, (string) trim($frm->getLastPaidDueDate()));
    array_push($params, (string) trim($frm->getBillerAdminFee()));
    array_push($params, (string) trim($frm->getMiscFee()));
    array_push($params, (string) trim($frm->getMiscNumber()));
    array_push($params, (string) trim($frm->getPolicyNumber()));
    array_push($params, (string) trim($frm->getCustPhoneNum()));
    array_push($params, (string) trim($frm->getCustAddr()));
    array_push($params, (string) trim($frm->getCustGender()));
    array_push($params, (string) trim($frm->getCustJob()));
    array_push($params, (string) trim($frm->getBenName()));
    array_push($params, (string) trim($frm->getBenPhoneNum()));
    array_push($params, (string) trim($frm->getBenAddr()));
    array_push($params, (string) trim($frm->getBenRelation()));
    array_push($params, (string) trim($frm->getRegDate()));
    array_push($params, (string) trim($frm->getStartDate()));
    array_push($params, (string) trim($frm->getEndDate()));
    array_push($params, (string) trim($frm->getInfoTeks()));

    return $params;
}

function retKartuKredit($params, $frm) {
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    if($frm->getMember() === 'HH146493'){
        array_push($params, (string) maskString(trim($frm->getCustomerID()),4));
    } else {
        array_push($params, (string) trim($frm->getCustomerID()));
    }
    
    array_push($params, (string) trim($frm->getBillQuantity()));
    array_push($params, (string) trim($frm->getNoref1()));
    array_push($params, (string) trim($frm->getNoref2()));
    array_push($params, (string) trim($frm->getCustomerName()));
    array_push($params, (string) trim($frm->getProductCategory()));
    array_push($params, (string) trim($frm->getBillAmount()));
    array_push($params, (string) trim($frm->getPenalty()));
    array_push($params, (string) trim($frm->getStampDuty()));
    array_push($params, (string) trim($frm->getPPN()));
    array_push($params, (string) trim($frm->getAdminCharge()));
    array_push($params, (string) trim($frm->getBillerRefNumber()));
    array_push($params, (string) trim($frm->getPTName()));
    if($frm->getMember() === 'HH146493'){
        array_push($params, (string) "");
        array_push($params, (string) "");
    } else {
        array_push($params, (string) trim($frm->getLastPaidPeriode()));
        array_push($params, (string) trim($frm->getLastPaidDueDate()));
    }
    
    array_push($params, (string) trim($frm->getBillerAdminFee()));
    array_push($params, (string) trim($frm->getMiscFee()));
    array_push($params, (string) trim($frm->getMiscNumber()));
    array_push($params, (string) trim($frm->getMinimumPayAmount()));
    array_push($params, (string) trim($frm->getMaximumPayAmount()));

    return $params;
}

function retBpjs($params, $frm) {
    array_push($params, (string) trim($frm->getCmd()));
    array_push($params, (string) trim($frm->getNik()));
    array_push($params, (string) trim($frm->getCustomerName()));
    array_push($params, (string) trim($frm->getTanggal_Lahir()));
    array_push($params, (string) trim($frm->getKode_Bayar()));
    array_push($params, (string) trim($frm->getFtrx_Id()));
    array_push($params, (string) trim($frm->getData1()));
    array_push($params, (string) trim($frm->getData2()));
    array_push($params, (string) trim($frm->getData3()));
    array_push($params, (string) trim($frm->getData4()));
    array_push($params, (string) trim($frm->getTagihan()));
    array_push($params, (string) trim($frm->getAdmin()));
    array_push($params, (string) trim($frm->getKpj()));
    array_push($params, (string) trim($frm->getPekerjaan()));
    array_push($params, (string) trim($frm->getJam_Awal()));
    array_push($params, (string) trim($frm->getJam_Akhir()));
    array_push($params, (string) trim($frm->getAlamat()));
    array_push($params, (string) trim($frm->getAlamat_Email()));
    array_push($params, (string) trim($frm->getLokasi_Kerja()));
    array_push($params, (string) trim($frm->getUpah()));
    array_push($params, (string) trim($frm->getKec()));
    array_push($params, (string) trim($frm->getKel()));
    array_push($params, (string) trim($frm->getKodepos()));
    array_push($params, (string) trim($frm->getHP()));
    array_push($params, (string) trim($frm->getOtp()));
    array_push($params, (string) trim($frm->getJht()));
    array_push($params, (string) trim($frm->getRate_Jht()));
    array_push($params, (string) trim($frm->getJkk()));
    array_push($params, (string) trim($frm->getRate_Jkk()));
    array_push($params, (string) trim($frm->getJkm()));
    array_push($params, (string) trim($frm->getRate_Jkm()));
    array_push($params, (string) trim($frm->getIsnew()));
    array_push($params, (string) trim($frm->getStatus_Bayar()));
    array_push($params, (string) trim($frm->getBiaya_Registrasi()));
    array_push($params, (string) trim($frm->getKode_Kantor_Cabang()));
    array_push($params, (string) trim($frm->getAlamat_Kantor_Cabang()));
    array_push($params, (string) trim($frm->getKode_Kabupaten()));
    array_push($params, (string) trim($frm->getKode_Provinsi()));
    array_push($params, (string) trim($frm->getProgram()));
    array_push($params, (string) trim($frm->getPeriode()));
    array_push($params, (string) trim($frm->getStatus_Hitung_Iuran()));
    array_push($params, (string) trim($frm->getBlnJkm()));
    array_push($params, (string) trim($frm->getBlnJkk()));
    array_push($params, (string) trim($frm->getBlnJht()));
    array_push($params, (string) trim($frm->getKet1()));
    array_push($params, (string) trim($frm->getKet2()));
    array_push($params, (string) trim($frm->getKet3()));



    return $params;
}

function retTiketux($params, $frm) {
    array_push($params, (string) trim($frm->getTRAVEL_COMMAND()));//1
    array_push($params, (string) trim($frm->getTRAVEL_AGENT()));//2
    array_push($params, (string) trim($frm->getTRAVEL_KOTA_BERANGKAT()));//3
    array_push($params, (string) trim($frm->getTRAVEL_CABANG_BERANGKAT()));//4
    array_push($params, (string) trim($frm->getTRAVEL_KOTA_TIBA()));//5
    array_push($params, (string) trim($frm->getTRAVEL_CABANG_TIBA()));//6
    array_push($params, (string) trim($frm->getTRAVEL_TANGGAL_BERANGKAT()));//7
    array_push($params, (string) trim($frm->getTRAVEL_WAKTU_BERANGKAT()));//8
    array_push($params, (string) trim($frm->getTRAVEL_WAKTU_BERANGKAT_START()));//9
    array_push($params, (string) trim($frm->getTRAVEL_WAKTU_BERANGKAT_END()));//10
    array_push($params, (string) trim($frm->getTRAVEL_PULANG_PERGI()));//11
    array_push($params, (string) trim($frm->getTRAVEL_JUMLAH_PENUMPANG()));//12
    array_push($params, (string) trim($frm->getTRAVEL_KODE_JURUSAN()));//13
    array_push($params, (string) trim($frm->getTRAVEL_ID_JURUSAN()));//14
    array_push($params, (string) trim($frm->getTRAVEL_KODE_JADWAL()));//15
    array_push($params, (string) trim($frm->getTRAVEL_NOMINAL_ADMIN()));//16
    array_push($params, (string) trim($frm->getTRAVEL_NO_REFF()));//17
    array_push($params, (string) trim($frm->getTRAVEL_OTP()));//18
    array_push($params, (string) trim($frm->getTRAVEL_CODE()));//19
    array_push($params, (string) trim($frm->getTRAVEL_TANGGAL_RESERVASI()));//20
    array_push($params, (string) trim($frm->getTRAVEL_STATUS_GBK()));//21
    array_push($params, (string) trim($frm->getTRAVEL_LAYOUT_KURSI()));//22
    array_push($params, (string) trim($frm->getTRAVEL_NO_KURSI()));//23
    array_push($params, (string) trim($frm->getTRAVEL_NAMA_PEMESAN()));//24
    array_push($params, (string) trim($frm->getTRAVEL_ALAMAT_PEMESAN()));//25
    array_push($params, (string) trim($frm->getTRAVEL_NO_HP_PEMESAN()));//26
    array_push($params, (string) trim($frm->getTRAVEL_EMAIL_PEMESAN()));//27
    array_push($params, (string) trim($frm->getTRAVEL_PENUMPANG1()));//28
    array_push($params, (string) trim($frm->getTRAVEL_PENUMPANG2()));//29
    array_push($params, (string) trim($frm->getTRAVEL_PENUMPANG3()));//30
    array_push($params, (string) trim($frm->getTRAVEL_KODE_BOOKING()));//31
    array_push($params, (string) trim($frm->getTRAVEL_KODE_PEMBAYARAN()));//32
    array_push($params, (string) trim($frm->getTRAVEL_NO_TIKET()));//33
    array_push($params, (string) trim($frm->getTRAVEL_MESSAGE()));//34
    array_push($params, (string) trim($frm->getTRAVEL_NOMINAL()));//35
    array_push($params, (string) trim($frm->getTRAVEL_NTA()));//36
    array_push($params, (string) trim($frm->getTRAVEL_STATUS()));//37
    array_push($params, (string) trim($frm->getTRAVEL_KETERANGAN()));//38
    array_push($params, (string) trim($frm->getTRAVEL_STATUS_BAYAR()));//39
    array_push($params, (string) trim($frm->getTRAVEL_WAKTU_BAYAR()));//40
    array_push($params, (string) trim($frm->getTRAVEL_FLAG_BATAL()));//41
    array_push($params, (string) trim($frm->getTRAVEL_PROMO()));//42
    array_push($params, (string) trim($frm->getTRAVEL_ALAMAT_ASAL()));//43
    array_push($params, (string) trim($frm->getTRAVEL_ALAMAT_TUJUAN()));//44
    array_push($params, (string) trim($frm->getTRAVEL_NO_KTP()));//45
    array_push($params, (string) trim($frm->getTRAVEL_ID_LOKASI()));//46
    array_push($params, (string) trim($frm->getTRAVEL_PAYMENT()));//47
    array_push($params, (string) trim($frm->getTRAVEL_VOUCHER()));//48
    array_push($params, (string) trim($frm->getTRAVEL_DISC_PROMO()));//49
    
    return $params;
}

function retAdiraaxi($params, $frm) {
    array_push($params, (string) trim($frm->getPROVIDER_NAME()));//1
    array_push($params, (string) trim($frm->getNAMA_PRODUK()));//2
    array_push($params, (string) trim($frm->getNO_KTP()));//3
    array_push($params, (string) trim($frm->getNO_TELPON()));//4
    array_push($params, (string) trim($frm->getNAMAAXI()));//5
    array_push($params, (string) trim($frm->getALAMATAXI()));//6
    array_push($params, (string) trim($frm->getID_PROPINSI()));//7
    array_push($params, (string) trim($frm->getID_KOTA()));//8
    array_push($params, (string) trim($frm->getKODE_POS()));//9
    array_push($params, (string) trim($frm->getWAKTU_SURVEY_AWAL1()));//10
    array_push($params, (string) trim($frm->getWAKTU_SURVEY_AKHIR1()));//11
    array_push($params, (string) trim($frm->getWAKTU_SURVEY_AWAL2()));//12
    array_push($params, (string) trim($frm->getWAKTU_SURVEY_AKHIR2()));//13
    array_push($params, (string) trim($frm->getWAKTU_SURVEY_AWAL3()));//14
    array_push($params, (string) trim($frm->getWAKTU_SURVEY_AKHIR3()));//15
    array_push($params, (string) trim($frm->getCONTACT_PERSON()));//16
    return $params;
}   

function retNewPAM2($params, $frm) {
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getSwitcherId()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getCustomerId1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getCustomerId2()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getCustomerId3()) );
    array_push($params, (string) trim($frm->getBillQuantity()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getGwRefnum()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getSwRefnum()) );
    array_push($params, (string) trim($frm->getCustomerName()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getCustomerAddress()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getCustomerSegmentation()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getCustomerDetailInformation()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBillerAdminCharge()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getTotalBillAmount()) );
    array_push($params, (string) trim($frm->getPdamName()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getStampDuty()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getTransactionFee()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getOtherFee()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMonthPeriod1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getYearPeriod1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMeterUsage1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getStand1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getFirstMeterRead1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getLastMeterRead1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBillAmount1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getPenalty1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBurdenAmount1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMiscAmount1()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMonthPeriod2()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getYearPeriod2()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMeterUsage2()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getStand2()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getFirstMeterRead2()) );
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getLastMeterRead2()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBillAmount2()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getPenalty2()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBurdenAmount2()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMiscAmount2()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMonthPeriod3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getYearPeriod3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMeterUsage3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getStand3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getFirstMeterRead3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getLastMeterRead3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBillAmount3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getPenalty3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBurdenAmount3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMiscAmount3()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMonthPeriod4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getYearPeriod4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMeterUsage4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getStand4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getFirstMeterRead4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getLastMeterRead4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBillAmount4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getPenalty4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBurdenAmount4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMiscAmount4()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMonthPeriod5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getYearPeriod5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMeterUsage5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getStand5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getFirstMeterRead5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getLastMeterRead5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBillAmount5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getPenalty5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBurdenAmount5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMiscAmount5()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMonthPeriod6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getYearPeriod6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMeterUsage6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getStand6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getFirstMeterRead6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getLastMeterRead6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBillAmount6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getPenalty6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getBurdenAmount6()));
    array_push($params, (string) $frm->getCommand() == "TAGIHAN" ? "" : trim($frm->getMiscAmount6()));

    return $params;
}

function retNewPAM($params, $frm) {
    array_push($params, (string) trim($frm->getSwitcherId()) );
    array_push($params, (string) trim($frm->getCustomerId1()) );
    array_push($params, (string) trim($frm->getCustomerId2()) );
    array_push($params, (string) trim($frm->getCustomerId3()) );
    array_push($params, (string) trim($frm->getBillQuantity()) );
    array_push($params, (string) trim($frm->getGwRefnum()) );
    array_push($params, (string) trim($frm->getSwRefnum()) );
    array_push($params, (string) encodehtml(trim($frm->getCustomerName())) );
    array_push($params, (string) encodehtml(trim($frm->getCustomerAddress())) );
    array_push($params, (string) trim($frm->getCustomerSegmentation()) );
    array_push($params, (string) trim($frm->getCustomerDetailInformation()) );
    array_push($params, (string) trim($frm->getBillerAdminCharge()) );
    array_push($params, (string) trim($frm->getTotalBillAmount()) );
    array_push($params, (string) trim($frm->getPdamName()) );
    array_push($params, (string) trim($frm->getStampDuty()) );
    array_push($params, (string) trim($frm->getTransactionFee()) );
    array_push($params, (string) trim($frm->getOtherFee()) );
    array_push($params, (string) trim($frm->getMonthPeriod1()) );
    array_push($params, (string) trim($frm->getYearPeriod1()) );
    array_push($params, (string) trim($frm->getMeterUsage1()) );
    array_push($params, (string) trim($frm->getStand1()) );
    array_push($params, (string) trim($frm->getFirstMeterRead1()) );
    array_push($params, (string) trim($frm->getLastMeterRead1()) );
    array_push($params, (string) trim($frm->getBillAmount1()) );
    array_push($params, (string) trim($frm->getPenalty1()) );
    array_push($params, (string) trim($frm->getBurdenAmount1()) );
    array_push($params, (string) trim($frm->getMiscAmount1()) );
    array_push($params, (string) trim($frm->getMonthPeriod2()) );
    array_push($params, (string) trim($frm->getYearPeriod2()) );
    array_push($params, (string) trim($frm->getMeterUsage2()) );
    array_push($params, (string) trim($frm->getStand2()) );
    array_push($params, (string) trim($frm->getFirstMeterRead2()) );
    array_push($params, (string) trim($frm->getLastMeterRead2()));
    array_push($params, (string) trim($frm->getBillAmount2()));
    array_push($params, (string) trim($frm->getPenalty2()));
    array_push($params, (string) trim($frm->getBurdenAmount2()));
    array_push($params, (string) trim($frm->getMiscAmount2()));
    array_push($params, (string) trim($frm->getMonthPeriod3()));
    array_push($params, (string) trim($frm->getYearPeriod3()));
    array_push($params, (string) trim($frm->getMeterUsage3()));
    array_push($params, (string) trim($frm->getStand3()));
    array_push($params, (string) trim($frm->getFirstMeterRead3()));
    array_push($params, (string) trim($frm->getLastMeterRead3()));
    array_push($params, (string) trim($frm->getBillAmount3()));
    array_push($params, (string) trim($frm->getPenalty3()));
    array_push($params, (string) trim($frm->getBurdenAmount3()));
    array_push($params, (string) trim($frm->getMiscAmount3()));
    array_push($params, (string) trim($frm->getMonthPeriod4()));
    array_push($params, (string) trim($frm->getYearPeriod4()));
    array_push($params, (string) trim($frm->getMeterUsage4()));
    array_push($params, (string) trim($frm->getStand4()));
    array_push($params, (string) trim($frm->getFirstMeterRead4()));
    array_push($params, (string) trim($frm->getLastMeterRead4()));
    array_push($params, (string) trim($frm->getBillAmount4()));
    array_push($params, (string) trim($frm->getPenalty4()));
    array_push($params, (string) trim($frm->getBurdenAmount4()));
    array_push($params, (string) trim($frm->getMiscAmount4()));
    array_push($params, (string) trim($frm->getMonthPeriod5()));
    array_push($params, (string) trim($frm->getYearPeriod5()));
    array_push($params, (string) trim($frm->getMeterUsage5()));
    array_push($params, (string) trim($frm->getStand5()));
    array_push($params, (string) trim($frm->getFirstMeterRead5()));
    array_push($params, (string) trim($frm->getLastMeterRead5()));
    array_push($params, (string) trim($frm->getBillAmount5()));
    array_push($params, (string) trim($frm->getPenalty5()));
    array_push($params, (string) trim($frm->getBurdenAmount5()));
    array_push($params, (string) trim($frm->getMiscAmount5()));
    array_push($params, (string) trim($frm->getMonthPeriod6()));
    array_push($params, (string) trim($frm->getYearPeriod6()));
    array_push($params, (string) trim($frm->getMeterUsage6()));
    array_push($params, (string) trim($frm->getStand6()));
    array_push($params, (string) trim($frm->getFirstMeterRead6()));
    array_push($params, (string) trim($frm->getLastMeterRead6()));
    array_push($params, (string) trim($frm->getBillAmount6()));
    array_push($params, (string) trim($frm->getPenalty6()));
    array_push($params, (string) trim($frm->getBurdenAmount6()));
    array_push($params, (string) trim($frm->getMiscAmount6()));

    return $params;
}

function retIklanbaris($params, $frm) {
    
    array_push($params, (string) trim($frm->getPROVIDER_NAME_IKLAN()));//1
    array_push($params, (string) trim($frm->getNAMA_PRODUK_IKLAN()));//2
    array_push($params, (string) trim($frm->getNAMA_IKLAN()));//3
    array_push($params, (string) trim($frm->getALAMAT_IKLAN()));//4
    array_push($params, (string) trim($frm->getNO_TELPON_IKLAN()));//5
    array_push($params, (string) trim($frm->getPAKET()));//6
    array_push($params, (string) trim($frm->getKLASIFIKASI()));//7
    array_push($params, (string) trim($frm->getSUB_KLASIFIKASI()));//8
    array_push($params, (string) trim($frm->getKOTA_IKLAN()));//9
    array_push($params, (string) trim($frm->getHARI_MUAT()));//10
    array_push($params, (string) trim($frm->getTANGGAL_AWAL_MUAT()));//11
    array_push($params, (string) trim($frm->getNAMA_SURAT_KABAR()));//12
    array_push($params, (string) trim($frm->getMATERI_IKLAN()));//13
    array_push($params, (string) trim($frm->getCHAR_BARIS()));//14
    array_push($params, (string) trim($frm->getJUMLAH_BARIS()));//15
    array_push($params, (string) trim($frm->getJUMLAH_KARAKTER()));//16
    array_push($params, (string) trim($frm->getUKURAN_MIN_BARIS()));//17
    array_push($params, (string) trim($frm->getUKURAN_MAX_BARIS()));//18
    array_push($params, (string) trim($frm->getHARGA_PER_BARIS()));//19
    array_push($params, (string) trim($frm->getHARGA_TOTAL()));//20
    array_push($params, (string) trim($frm->getPPN_IKLAN()));//21
    array_push($params, (string) trim($frm->getKETERANGAN_IKLAN()));//22
    array_push($params, (string) trim($frm->getIS_PROCESSED_IKLAN()));//22
    
    return $params;
}  

function retEcash($params, $frm) {
    
    array_push($params, (string) trim($frm->getECASH_OTP_ECASH() ));
    array_push($params, (string) trim($frm->getMSISDN_ECASH() ));
    array_push($params, (string) trim($frm->getAMOUNT_ECASH() ));
    array_push($params, (string) trim($frm->getAPP_ID_ECASH()));
    array_push($params, (string) trim($frm->getTRACE_NUMBER_ECASH())); 
    array_push($params, (string) trim($frm->getCUSTOMER_NAME_ECASH()));
    array_push($params, (string) trim($frm->getTRX_NUMBER_ECASH() ));
    
    return $params;
}

function retFirstlogistic($params, $frm) {
 
    array_push($params, (string) trim($frm->getHAWB()));                  
    array_push($params, (string) trim($frm->getCN_NAME()));               
    array_push($params, (string) trim($frm->getADDRESS()));               
    array_push($params, (string) trim($frm->getDISTRICT() ));             
    array_push($params, (string) trim($frm->getPOSTCODE() ));             
    array_push($params, (string) trim($frm->getCITY() ));                 
    array_push($params, (string) trim($frm->getPROVINCE() ));             
    array_push($params, (string) trim($frm->getCOUNTRY() ));              
    array_push($params, (string) trim($frm->getPHONE() ));                
    array_push($params, (string) trim($frm->getCONTACT() ));              
    array_push($params, (string) trim($frm->getORDERID()));               
    array_push($params, (string) trim($frm->getVOLUMETRIC() ));           
    array_push($params, (string) trim($frm->getACTWEIGHT() ));            
    array_push($params, (string) trim($frm->getVOLWEIGHT() ));            
    array_push($params, (string) trim($frm->getPIECES() ));               
    array_push($params, (string) trim($frm->getGOODSTYPE() ));            
    array_push($params, (string) trim($frm->getSURCHARGE() ));            
    array_push($params, (string) trim($frm->getITEMNAME() ));             
    array_push($params, (string) trim($frm->getGOODSVAL() ));             
    array_push($params, (string) trim($frm->getASSOPTION() ));            
    array_push($params, (string) trim($frm->getASSOPTION_AMOUNT() ));     
    array_push($params, (string) trim($frm->getCOD() ));                  
    array_push($params, (string) trim($frm->getPICKUPDATE() ));           
    array_push($params, (string) trim($frm->getSERVICE() ));              
    array_push($params, (string) trim($frm->getAMOUNTLOGISTIC() ));       
    array_push($params, (string) trim($frm->getMERCHANT() ));             
    array_push($params, (string) trim($frm->getMERCHANT_ADDRESS() ));     
    array_push($params, (string) trim($frm->getMERCHANT_DISTRIC() ));     
    array_push($params, (string) trim($frm->getMERCHANT_POSTCODE() ));     
    array_push($params, (string) trim($frm->getMERCHANT_CITY() ));        
    array_push($params, (string) trim($frm->getMERCHANT_PROVINCE() ));    
    array_push($params, (string) trim($frm->getMERCHANT_COUNTRY() ));     
    array_push($params, (string) trim($frm->getMERCHANT_PHONE() ));       
    array_push($params, (string) trim($frm->getMERCHANT_CONTACT() ));     
    array_push($params, (string) trim($frm->getREMARK() ));               
    array_push($params, (string) trim($frm->getMESSAGELOGISTIC() ));      
    array_push($params, (string) trim($frm->getSUCCESS() ));              
    array_push($params, (string) trim($frm->getORDERNUMBER() ));          
    array_push($params, (string) trim($frm->getTRACKINGNUMBER() ));       
    array_push($params, (string) trim($frm->getREFERENCE() ));            
    array_push($params, (string) trim($frm->getSHIPDATE() ));             
    array_push($params, (string) trim($frm->getTLC() ));                  
    array_push($params, (string) trim($frm->getHUB() ));                  
    array_push($params, (string) trim($frm->getRECEIVEDBY() ));           
    array_push($params, (string) trim($frm->getDELIVEREDDATE() ));        
    array_push($params, (string) trim($frm->getDELIVEREDTIME() ));        
    array_push($params, (string) trim($frm->getCURRENTSTATUS() ));        
    array_push($params, (string) trim($frm->getSHIPMENTTYPE() ));         
    array_push($params, (string) trim($frm->getWEIGHT() ));               
    array_push($params, (string) trim($frm->getTRACK_HISTORY() ));        
    array_push($params, (string) trim($frm->getREGSERV() ));              
    array_push($params, (string) trim($frm->getREGPRICE() ));             
    array_push($params, (string) trim($frm->getONSSERV() ));              
    array_push($params, (string) trim($frm->getONSPRICE() ));             
    array_push($params, (string) trim($frm->getSDSSERV() ));              
    array_push($params, (string) trim($frm->getSDSPRICE() ));             
    array_push($params, (string) trim($frm->getREGLEAD() ));              
    array_push($params, (string) trim($frm->getONSLEAD() ));              
    array_push($params, (string) trim($frm->getSDSLEAD() ));              
    array_push($params, (string) trim($frm->getPACKING_TYPE() ));         
    array_push($params, (string) trim($frm->getPACKING_WEIGHT() ));       
    array_push($params, (string) trim($frm->getPACKING_AMOUNT() ));    
    array_push($params, (string) trim($frm->getKOLI() ));
    array_push($params, (string) trim($frm->getPAKET_DATA() ));
    array_push($params, (string) trim($frm->getKODE_PAKET() ));
    array_push($params, (string) trim($frm->getKELURAHAN() ));
    array_push($params, (string) trim($frm->getKECAMATAN() ));
    array_push($params, (string) trim($frm->getMERCHANT_KELURAHAN() ));
    array_push($params, (string) trim($frm->getMERCHANT_KECAMATAN() ));
    array_push($params, (string) trim($frm->getPICKUP_READY_TIME() ));
    array_push($params, (string) trim($frm->getPICKUP_OFFICE_CLOSED_TIME() ));
    array_push($params, (string) trim($frm->getPICKUP_RES_NO() ));
    array_push($params, (string) trim($frm->getPIN_CODE() ));
    array_push($params, (string) trim($frm->getNOMOR_SURAT_JALAN() ));
    array_push($params, (string) trim($frm->getBOOKING_CODE_NO() ));
    array_push($params, (string) trim($frm->getCASHIER_NAME() ));
    array_push($params, (string) trim($frm->getCOURIER_ID() ));
    array_push($params, (string) trim($frm->getCOURIER_NAME() ));
            
    return $params;
}

function retCarRental($params, $frm) {
    array_push($params, (string) trim($frm->getCOMPANY_ID()));      //1            
    array_push($params, (string) trim($frm->getCAR_CONFIRMATION_CODE()));    //2          
    array_push($params, (string) trim($frm->getCAR_REFID()));    //3           
    array_push($params, (string) trim($frm->getLOCATION() ));  //4           
    array_push($params, (string) trim($frm->getLOCATION_PICKUP() ));  //5           
    array_push($params, (string) trim($frm->getLOCATION_DROPOFF() ));       //6          
    array_push($params, (string) trim($frm->getSTART_DATE() ));   //7          
    array_push($params, (string) trim($frm->getEND_DATE() ));    //8          
    array_push($params, (string) trim($frm->getPICKUP_TIME() ));      //9          
    array_push($params, (string) trim($frm->getDROPOFF_TIME() ));    //10          
    array_push($params, (string) trim($frm->getCAR_RTYPE()));     //11          
    array_push($params, (string) trim($frm->getCAR_RESULT() )); //12          
    array_push($params, (string) trim($frm->getCAR_MESSAGE() ));  //13          
    array_push($params, (string) trim($frm->getCAR_COUNT() ));  //14    
    array_push($params, (string) trim($frm->getCAR_PACKET() ));  //14          
    array_push($params, (string) trim($frm->getCAR_INCLUDE_DRIVER() ));  //14 
    array_push($params, (string) trim($frm->getCAR_NAME() ));     //15          
    array_push($params, (string) trim($frm->getCAR_CAPACITY() ));  //16          
    array_push($params, (string) trim($frm->getCAR_BAGGAGE() ));   //17         
    array_push($params, (string) trim($frm->getCAR_TRANSMITION() ));    //18         
    array_push($params, (string) trim($frm->getCAR_AC() ));    //19         
    array_push($params, (string) trim($frm->getCAR_FUEL() ));    //20        
    array_push($params, (string) trim($frm->getCAR_PRICE12() ));//21     
    array_push($params, (string) trim($frm->getCAR_PRICE24() ));           //22       
    array_push($params, (string) trim($frm->getCAR_IMAGE() ));    //23       
    array_push($params, (string) trim($frm->getCAR_TELP() ));       //24       
    array_push($params, (string) trim($frm->getCAR_POLICY() )); //25      
    array_push($params, (string) trim($frm->getCAR_LIST() ));        //26     
    array_push($params, (string) trim($frm->getCAR_KODE_BILLER() )); //27    
    array_push($params, (string) trim($frm->getCONTACT_NAME() )); //28    
    array_push($params, (string) trim($frm->getCONTACT_SALUTATION() )); //29    
    array_push($params, (string) trim($frm->getCONTACT_ALAMAT() ));     //30   
    array_push($params, (string) trim($frm->getCONTACT_NOHP_1() )); //31   
    array_push($params, (string) trim($frm->getCONTACT_NOHP_2() ));   //32  
    array_push($params, (string) trim($frm->getCONTACT_JENIS_ID_1() ));    //33   
    array_push($params, (string) trim($frm->getCONTACT_JENIS_ID_2() ));    //34 
    array_push($params, (string) trim($frm->getCONTACT_NO_ID_1() ));             //35  
    array_push($params, (string) trim($frm->getCONTACT_NO_ID_2() ));     //36 
    array_push($params, (string) trim($frm->getINFO_TAMBAHAN() ));             //37 
    array_push($params, (string) trim($frm->getSTATUS_CARRENTAL() ));         //38 
    array_push($params, (string) trim($frm->getKETERANGAN_CARRENTAL() ));      //39                    
    return $params;
}
function retLakuPandai($params, $frm) {
    array_push($params, (string) trim($frm->getCMD()));      
    array_push($params, (string) trim($frm->getDEALERID()));      
    array_push($params, (string) trim($frm->getSYSTEMID()));      
    array_push($params, (string) trim($frm->getACCOUNTNUM()));      
    array_push($params, (string) trim($frm->getOPTION()));      
    array_push($params, (string) trim($frm->getIDAGEN()));      
    array_push($params, (string) trim($frm->getCURRENCY()));      
    array_push($params, (string) trim($frm->getACCOUNT_STATUS()));      
    array_push($params, (string) trim($frm->getPRODUCT()));      
    array_push($params, (string) trim($frm->getHOMEBRANCH()));      
    array_push($params, (string) trim($frm->getCIFNUM()));      
    array_push($params, (string) trim($frm->getNAME()));      
    array_push($params, (string) trim($frm->getNAMEREK()));      
    array_push($params, (string) trim($frm->getCURRENTBALANCE()));      
    array_push($params, (string) trim($frm->getAVAILABLEBALANCE()));      
    array_push($params, (string) trim($frm->getOPENDATE()));      
    array_push($params, (string) trim($frm->getADDRESS_STREET()));      
    array_push($params, (string) trim($frm->getADDRESS_RT()));      
    array_push($params, (string) trim($frm->getADDRESS1()));      
    array_push($params, (string) trim($frm->getADDRESS2()));      
    array_push($params, (string) trim($frm->getADDRESS3()));      
    array_push($params, (string) trim($frm->getADDRESS4()));      
    array_push($params, (string) trim($frm->getPOSTCODE()));      
    array_push($params, (string) trim($frm->getHOMEPHONE()));      
    array_push($params, (string) trim($frm->getFAX()));      
    array_push($params, (string) trim($frm->getOFFICEPHONE()));      
    array_push($params, (string) trim($frm->getMOBILEPHONE()));      
    array_push($params, (string) trim($frm->getADDRESS1AA()));      
    array_push($params, (string) trim($frm->getADDRESS2AA()));      
    array_push($params, (string) trim($frm->getADDRESS3AA()));      
    array_push($params, (string) trim($frm->getADDRESS4AA()));      
    array_push($params, (string) trim($frm->getPOSTCODEAA()));      
    array_push($params, (string) trim($frm->getACCOUNTPRODUCTTYPE()));      
    array_push($params, (string) trim($frm->getACCTYPE()));      
    array_push($params, (string) trim($frm->getSUBCAT()));      
    array_push($params, (string) trim($frm->getAVAILABLEINTEREST()));      
    array_push($params, (string) trim($frm->getLIENBALANCE()));      
    array_push($params, (string) trim($frm->getUNCLEARBALANCE()));      
    array_push($params, (string) trim($frm->getINTERESTRATE()));      
    array_push($params, (string) trim($frm->getKTP()));      
    array_push($params, (string) trim($frm->getNPWP()));      
    array_push($params, (string) trim($frm->getJENIS_PEKERJAAN()));      
    array_push($params, (string) trim($frm->getEMAIL()));      
    array_push($params, (string) trim($frm->getKODE_WIL_BI()));      
    array_push($params, (string) trim($frm->getKODE_CABANG()));      
    array_push($params, (string) trim($frm->getKODE_LOKET()));      
    array_push($params, (string) trim($frm->getKODE_MITRA()));      
    array_push($params, (string) trim($frm->getTGL_INPUT()));      
    array_push($params, (string) trim($frm->getCA_GEN_STATUS()));      
    array_push($params, (string) trim($frm->getCLIENTID()));      
    array_push($params, (string) trim($frm->getCLIENT_ACCOUNT_NUM()));      
    array_push($params, (string) trim($frm->getREQ_ID()));      
    array_push($params, (string) trim($frm->getREQ_TIME()));      
    array_push($params, (string) trim($frm->getCUST_ACC_NUM()));      
    array_push($params, (string) trim($frm->getAMOUNT()));      
    array_push($params, (string) trim($frm->getTRANSACTION_JOURNAL()));      
    array_push($params, (string) trim($frm->getCUSTOMER_OTP()));      
    array_push($params, (string) trim($frm->getCUST_FIRST_NAME()));      
    array_push($params, (string) trim($frm->getCUST_MIDLE_NAME()));      
    array_push($params, (string) trim($frm->getCUST_LAST_NAME()));      
    array_push($params, (string) trim($frm->getCUST_PLACE_OF_BIRTH()));      
    array_push($params, (string) trim($frm->getCUST_DATE_OF_BIRTH()));      
    array_push($params, (string) trim($frm->getCUST_GENDER()));      
    array_push($params, (string) trim($frm->getCUST_IS_MARRIED()));      
    array_push($params, (string) trim($frm->getCUST_INCOME()));      
    array_push($params, (string) trim($frm->getPIN_TRANSAKSI()));      
    return $params;
}

function retFcash($params, $frm) {
    
    array_push($params, (string) trim($frm->getFCASH_OTP_FCASH() ));
    array_push($params, (string) trim($frm->getNO_HP_FCASH() ));
    array_push($params, (string) trim($frm->getCUSTOMER_NAME_FCASH()));
    array_push($params, (string) trim($frm->getAMOUNT_FCASH() ));
    array_push($params, (string) trim($frm->getFEE_BILLER_FCASH() ));
    array_push($params, (string) trim($frm->getTRX_ORDER_FCASH() ));
    array_push($params, (string) trim($frm->getID_TRX_BB_FCASH() ));
    
    return $params;
}

function retPKB($params, $frm) {
 
    array_push($params, (string) trim($frm->getKND_NOPOL()));
    array_push($params, (string) trim($frm->getKND_ID()));
    array_push($params, (string) trim($frm->getKND_DF_JENIS()));
    array_push($params, (string) trim($frm->getKND_NAMA()));
    array_push($params, (string) trim($frm->getKND_DF_NOMOR()));
    array_push($params, (string) trim($frm->getKND_DF_TANGGAL()));
    array_push($params, (string) trim($frm->getKND_DF_JAM()));
    array_push($params, (string) trim($frm->getKND_DF_PROSES()));
    array_push($params, (string) trim($frm->getUSR_FULL_NAME()));
    array_push($params, (string) trim($frm->getKND_KOHIR()));
    array_push($params, (string) trim($frm->getKND_SKUM()));
    array_push($params, (string) trim($frm->getKND_ALAMAT()));
    array_push($params, (string) trim($frm->getKEL_DESC()));
    array_push($params, (string) trim($frm->getKEC_DESC()));
    array_push($params, (string) trim($frm->getKAB_DESC()));
    array_push($params, (string) trim($frm->getTPE_DESC()));
    array_push($params, (string) trim($frm->getKD_MERK()));
    array_push($params, (string) trim($frm->getMRK_DESC()));
    array_push($params, (string) trim($frm->getJNS_DESC()));
    array_push($params, (string) trim($frm->getKND_THN_BUAT()));
    array_push($params, (string) trim($frm->getKND_CYL()));
    array_push($params, (string) trim($frm->getKND_WARNA()));
    array_push($params, (string) trim($frm->getKND_RANGKA()));
    array_push($params, (string) trim($frm->getKND_MESIN()));
    array_push($params, (string) trim($frm->getKND_NO_BPKB()));
    array_push($params, (string) trim($frm->getKND_SD_NOTICE()));
    array_push($params, (string) trim($frm->getKND_TGL_STNK()));
    array_push($params, (string) trim($frm->getKND_SD_STNK()));
    array_push($params, (string) trim($frm->getBBM_DESC()));
    array_push($params, (string) trim($frm->getKD_BBM()));
    array_push($params, (string) trim($frm->getWRN_DESC()));
    array_push($params, (string) trim($frm->getKND_NOPOL_EKS()));
    array_push($params, (string) trim($frm->getKND_JBB_PENUMPANG()));
    array_push($params, (string) trim($frm->getKND_BERAT_KB()));
    array_push($params, (string) trim($frm->getKND_JML_SUMBU_AS()));
    array_push($params, (string) trim($frm->getKD_KAB()));
    array_push($params, (string) trim($frm->getKD_JENIS()));
    array_push($params, (string) trim($frm->getKD_TIPE()));
    array_push($params, (string) trim($frm->getBOBOT()));
    array_push($params, (string) trim($frm->getNILAI_JUAL()));
    array_push($params, (string) trim($frm->getDASAR_PKB()));
    array_push($params, (string) trim($frm->getKD_GOL()));
    array_push($params, (string) trim($frm->getGOL_DESC()));
    array_push($params, (string) trim($frm->getTGLBERLAKU()));
    array_push($params, (string) trim($frm->getPOKOK_NEW()));
    array_push($params, (string) trim($frm->getPOKOK_OLD()));
    array_push($params, (string) trim($frm->getDENDA_NEW()));
    array_push($params, (string) trim($frm->getDENDA_OLD()));
    array_push($params, (string) trim($frm->getKND_MILIK_KE()));
    array_push($params, (string) trim($frm->getKD_KEC()));
    array_push($params, (string) trim($frm->getKD_KEL()));
    array_push($params, (string) trim($frm->getKD_GUNA()));
    array_push($params, (string) trim($frm->getKND_BLOKIR()));
    array_push($params, (string) trim($frm->getKND_TGL_FAKTUR()));
    array_push($params, (string) trim($frm->getKND_TGL_KUWITANSI()));
    array_push($params, (string) trim($frm->getKND_BLOKIR_TGL()));
    array_push($params, (string) trim($frm->getKND_BLOKIR_DESC()));
    array_push($params, (string) trim($frm->getDRV_DESC()));
    array_push($params, (string) trim($frm->getBILL_QUANTITY()));
    array_push($params, (string) trim($frm->getREFF_NUM()));
    array_push($params, (string) trim($frm->getROW_ID()));
    array_push($params, (string) trim($frm->getPTP_TANGGAL()));
    array_push($params, (string) trim($frm->getNOM_PKB()));
    array_push($params, (string) trim($frm->getJASARAHARJA()));
    array_push($params, (string) trim($frm->getDENDA_NOM_PKB()));
    array_push($params, (string) trim($frm->getDENDA_JASARAHARJA()));
    array_push($params, (string) trim($frm->getNOM_PKB_TG()));
    array_push($params, (string) trim($frm->getJASARAHARJA_TG()));
    array_push($params, (string) trim($frm->getDENDA_NOM_PKB_TG()));
    array_push($params, (string) trim($frm->getDENDA_JASARAHARJA_TG()));
    

    return $params;
}

function retSbf($params, $frm) {    
    array_push($params, (string) trim($frm->getBookDate()));
    array_push($params, (string) trim($frm->getProductId()));
    array_push($params, (string) trim($frm->getProductCategory()));
    array_push($params, (string) trim($frm->getProductVariant()));
    array_push($params, (string) trim($frm->getProductName()));
    array_push($params, (string) trim($frm->getProductCostOfGoodsSold()));
    array_push($params, (string) trim($frm->getProductSellingPrice()));
    array_push($params, (string) trim($frm->getQuantityItems()));
    array_push($params, (string) trim($frm->getShippingCharges()));
    array_push($params, (string) trim($frm->getDiscountTotal()));
    array_push($params, (string) trim($frm->getVoucherValue()));
    array_push($params, (string) trim($frm->getTotalTransactionAmount()));
    array_push($params, (string) trim($frm->getPaymentCode()));
    array_push($params, (string) trim($frm->getBuyerName()));
    array_push($params, (string) trim($frm->getBuyerPhoneNumber()));
    array_push($params, (string) trim($frm->getBuyerPostalCode()));
    array_push($params, (string) trim($frm->getBuyerAddress()));
    array_push($params, (string) trim($frm->getBuyerEmail()));
    array_push($params, (string) trim($frm->getExpeditionId()));
    array_push($params, (string) trim($frm->getExpeditionName()));
    array_push($params, (string) trim($frm->getBillerCode()));
    array_push($params, (string) trim($frm->getBillerRefnum()));
    array_push($params, (string) trim($frm->getGwRefnum()));
    array_push($params, (string) trim($frm->getSwRefnum()));
    array_push($params, (string) trim($frm->getDataProduct()));

    return $params;
}


function replace_forbidden_chars($text) {
    $arrSearch = array("'", "*", "\r", "\n", "\t");
    $arrReplace = array("`", "-", " ", " ", " ");
    return str_replace($arrSearch, $arrReplace, $text);
}

function cekIsProsesTransaksi($id_transaksi) {
    $ret = false;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "SELECT id_transaksi FROM fmss.proses_transaksi WHERE id_transaksi = " . $id_transaksi;
    $eqn = $db->query($qn, "numrow");
    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    if ($eqn > 0) {
        $ret = true;
    }
    return $ret;
}


function cekRef1($ref1,$id_outlet,$kdproduk)
{
    $ret = 0;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "SELECT bill_info83 FROM fmss.transaksi WHERE bill_info83 = '".$ref1."' and id_outlet='".$id_outlet."' and id_produk ='".$kdproduk."' and response_code = '00' ";
    $eqn = $db->query($qn, "numrow");
    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    if ($eqn > 0) {
        $ret = 1;
    }
    return $ret;
}

function cekWhitelistDuble($id_outlet,$kdproduk)
{
    $ret = 0;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "select id_outlet from whitelist_double_transaksi where id_outlet='".$id_outlet."' and id_produk='".$kdproduk."' and is_active=1";
    $eqn = $db->query($qn, "numrow");
    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    if ($eqn > 0) {
        $ret = 1;
    }
    return $ret;
}

function cekIsProsesTransaksikudo($id_transaksi, $flag) {
    if($flag === 0){
        $table = "fmss.proses_transaksi";
    } else {
        $table = "fmss.transaksi";
    }
    $ret = false;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "SELECT id_transaksi FROM $table WHERE id_transaksi = " . $id_transaksi;
    $eqn = $db->query($qn, "numrow");
    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    if ($eqn > 0) {
        $ret = true;
    }
    return $ret;
}

function cekIsProsesTransaksi_devel($id_transaksi) {
    $ret = false;
    global $__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel;
    $db = new PgDBI2($__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel);

    $qn = "SELECT id_transaksi FROM fmss.proses_transaksi WHERE id_transaksi = " . $id_transaksi;
    $eqn = $db->query($qn, "numrow");
    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    if ($eqn > 0) {
        $ret = true;
    }
    return $ret;
}

function cek_is_online($idproduk){
    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = "select is_online from fmss.mt_produk where id_produk = '$idproduk'";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->is_online;
    if($ret == 0 ){ // 0 => EDC => harcode SEDANG DIPROSES
        return true;
    }
    return false;
}

function nominalalfa($id_outlet, $id_produk, $flag) {
    $ret = 0;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    if($flag == 'dev'){
        $__CFG_dbhost2 = "10.0.0.20";
        $__CFG_dbuser2 = "fmss";
        $__CFG_dbpass2 = "rahasia";
        $__CFG_dbname2 = "fmss";
        $__CFG_dbport2 = "5432";
        $db = new PgDBI2($__CFG_dbname2, $__CFG_dbhost2, $__CFG_dbuser2, $__CFG_dbpass2, $__CFG_dbport2);
    }
    $qn = "SELECT nominal FROM fmss.transaksi WHERE id_outlet = '$id_outlet' and id_produk = '$id_produk' and response_code = '00' and jenis_transaksi = 0 and transaction_date = now()::date order by id_transaksi desc limit 1 ";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->nominal;
    return $ret;
}

function cekDenomProduk($kdproduk, $denom=50){
    $getDenom = (int) preg_replace( '/[^0-9]/', '', $kdproduk );
    if($getDenom > $denom ){
        return true;
    } else {
        return false;
    }
}

function cekIsNominalTransaksi($id_transaksi) {
    $ret = 0;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = "SELECT nominal FROM fmss.transaksi WHERE id_transaksi = " . $id_transaksi;

    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->nominal;
    return $ret;
}

function cekglobal($id_transaksi, $field) {
    $ret = 0;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = "SELECT $field FROM fmss.transaksi WHERE id_transaksi = " . $id_transaksi;
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->$field;
    return $ret;
}

function getref1prev($kdproduk, $nohp, $idoutlet){
    $ret = "";
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = "SELECT bill_info83 FROM fmss.transaksi WHERE id_produk = '$kdproduk' and bill_info1 = '$nohp' and id_outlet = '$idoutlet' and transaction_date = NOW()::date
    union
    SELECT bill_info83 FROM fmss.proses_transaksi WHERE id_produk = '$kdproduk' and bill_info1 = '$nohp' and id_outlet = '$idoutlet' and transaction_date = NOW()::date";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->bill_info83;
    return $ret;
}

function getIdpel1($id_transaksi) {
    if ($id_transaksi === ""){
        return "";
    }
    $ret = 0;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = "SELECT bill_info1 FROM fmss.transaksi WHERE id_transaksi = " . $id_transaksi." and jenis_transaksi = 0 and response_code = '00'";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->bill_info1;
    return $ret;
}

function cekIsNominalTransaksiZakat($id_transaksi) {
    $ret = 0;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = "SELECT nominal, bill_info12 as telp, bill_info13 as nama, bill_info14 as alamat, bill_info24 as email FROM fmss.transaksi WHERE id_transaksi = " . $id_transaksi;
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->nominal.'|'.$row->telp.'|'.$row->nama.'|'.$row->alamat.'|'.$row->email;
    return $ret;
}

function cekDataProduk($id_transaksi) {
    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = "SELECT nominal_admin, id_biller FROM fmss.transaksi WHERE id_transaksi = " . $id_transaksi;
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = array($row->nominal_admin, $row->id_biller);
    return $ret;
}

function getnominaltrx($idtrx) {
    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
   $qn = " SELECT nominal from fmss.transaksi where id_transaksi = $idtrx 
            UNION 
            SELECT nominal from fmss.transaksi_backup where id_transaksi = $idtrx 
            UNION 
            SELECT nominal from fmss.proses_transaksi where id_transaksi = $idtrx";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->nominal;
    return $ret;
}

function getidref2($ref1, $idoutlet, $tanggal) {
    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = " SELECT id_transaksi from fmss.transaksi where transaction_date='".$tanggal."' and id_outlet = '".$idoutlet."' and bill_info83 = '".$ref1."' and jenis_transaksi=1";

    $eqn = $db->query($qn);
    if(count($eqn) > 0){
        $row = $eqn[0];
        $ret = $row->id_transaksi;
    }else{
        $ret = getidref2_backup($ref1, $idoutlet, $tanggal);
    }

    return $ret;
}

function getidref2_backup($ref1, $idoutlet, $tanggal) {
    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = "SELECT id_transaksi from fmss.transaksi_backup where transaction_date='".$tanggal."' and id_outlet = '".$idoutlet."' and bill_info83 = '".$ref1."' and jenis_transaksi=1";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->id_transaksi;
    return $ret;
}

function getnominalup($idtrx){
    
    if (intval($idtrx) === 0){
        return 0;
    }
    // $db = $GLOBALS["pgsql"];    
    // $nomup = 0;
    // $q = "SELECT nominal_up from fmss.transaksi where id_transaksi = $idtrx 
    //          UNION 
    //          SELECT nominal_up from fmss.transaksi_backup where id_transaksi = $idtrx 
    //          UNION 
    //          SELECT nominal_up from fmss.proses_transaksi where id_transaksi = $idtrx";
    // $e = pg_query($db,$q);
    // $n = pg_num_rows($e);
    // if ($n > 0){
    //     $r = pg_fetch_object($e);
    //     $nomup = $r->nominal_up;
    // }
    // pg_free_result($e);
    // return $nomup;    

    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $dbx = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = " SELECT nominal_up from fmss.transaksi where id_transaksi = $idtrx 
            UNION 
            SELECT nominal_up from fmss.transaksi_backup where id_transaksi = $idtrx 
            UNION 
            SELECT nominal_up from fmss.proses_transaksi where id_transaksi = $idtrx";
    $eqn = $dbx->query($qn);
    $row = $eqn[0];
    $ret = $row->nominal_up;
    return $ret;
}

function getnominalharga($id_produk){
    
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $dbx = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $qn = " SELECT harga_jual from fmss.mt_produk where id_produk = '$id_produk'";
    $eqn = $dbx->query($qn);
    $row = $eqn[0];
    $ret = $row->harga_jual;
    return $ret;
}

function mapping_rc_partnerlink($rc_ori, $id_biller, $id_produk) {
    $db = $GLOBALS["pgsql"];
    $q = "select rc_new from mapping_rc_partnerlink where rc_ori = '$rc_ori' and id_biller = $id_biller and id_produk like '$id_produk'";
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
    if ($n > 0) {
        $r = pg_fetch_object($e);
        $rc_new = $r->rc_new;
        pg_free_result($e);
        return $rc_new;
    } else {
        pg_free_result($e);
        return false;
    }
}

function getDataPaymentCode($paymentcode){
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    $curl = curl_init();
    //$url = "https://api-devel.winpay.id/order/getMultiPaymentCodeData?payment_code" //DEVEL
    $url = "https://api.winpay.id/order/getMultiPaymentCodeData?payment_code"; //PROD
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "$url=$paymentcode",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic MzVhYzg2NDIxNDUyZjgzNjRjM2NiOGZjMjY0ZjJkMjE6ZDk4NTAwOTlhYmEyOTk2NzkyYzg2ODdkY2VhMjhmMWY=",
            "Content-Type: application/json",
            "X-TOKEN-WP-SAMSATBANTEN: b8697bc90f98e5f72f64dddd205e3f8a",
            "cache-control: no-cache"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "";
    } else {
        return json_decode($response);
    }
}

function cekduplikatidpel($idpel) {

    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "SELECT id_outlet FROM fmss.transaksi WHERE bill_info1 = '$idpel' and response_code = '00' and transaction_date = NOW()::date";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->id_outlet;
    //}
    return $ret;
}

function maping_rc_alfamart($rc, $idproduk, $id_biller, $ket){ 
    $data;
    if (strpos(strtolower($ket), 'cocok') !== false) {
        return "05|ERROR - Lainnya";
    } else if (strpos(strtolower($ket), 'proses') !== false) {
        return "89|Transaksi dalam proses";
    } else {
        global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
        $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
        $date = date('Y-m-d');
        $qn ="SELECT rc_alfa, keterangan_alfa FROM fmss.mapping_rc_alfamart WHERE id_produk = '" . $idproduk."' and rc_ori = '".$rc."' and id_biller = '".$id_biller."'";
        $eqn = $db->query($qn);
        $row = $eqn[0];
        $data = $row->rc_alfa."|".$row->keterangan_alfa;
        return $data;
    }
}

function cekisproses($idpel1, $idoutlet, $kdproduk, $flag){
    if($flag == "prod"){
        global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
        $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    } else {
        $__CFG_dbhost2 = "10.0.0.20";
        $__CFG_dbuser2 = "fmss";
        $__CFG_dbpass2 = "rahasia";
        $__CFG_dbname2 = "fmss";
        $__CFG_dbport2 = "5432";
        $db = new PgDBI2($__CFG_dbname2, $__CFG_dbhost2, $__CFG_dbuser2, $__CFG_dbpass2, $__CFG_dbport2);
    }
    $ret = false;
    $q = "select id_transaksi from proses_transaksi where id_outlet = '$idoutlet' and id_produk = '$kdproduk' and bill_info1 = '$idpel1'";
    $eqn = $db->query($q, "numrow");
    if ($eqn > 0) {
        $ret = "process";
        return $ret;
    } else if($ret === false){
        $q2 = "select id_transaksi from transaksi where id_outlet = '$idoutlet' and id_produk = '$kdproduk' and bill_info1 = '$idpel1' and jenis_transaksi = 1 and response_code = '00'";    
        $eqn2 = $db->query($q2, "numrow");
        if ($eqn2 > 0) {
            $ret = "paid";   
        }
        return $ret;
    }
    
}

function is_commit_alfa($is_insert, $id_trx, $bill_info1, $id_produk, $flag, $id_outlet){
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    
    if($flag == 'dev'){
        $__CFG_dbhost2 = "10.0.0.20";
        $__CFG_dbuser2 = "fmss";
        $__CFG_dbpass2 = "rahasia";
        $__CFG_dbname2 = "fmss";
        $__CFG_dbport2 = "5432";
        $db = new PgDBI2($__CFG_dbname2, $__CFG_dbhost2, $__CFG_dbuser2, $__CFG_dbpass2, $__CFG_dbport2);
    } else {
        $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
        // echo $__CFG_dbname.' '.$__CFG_dbhost.' '.$__CFG_dbuser.' '.$__CFG_dbpass.' '.$__CFG_dbport;
    }
    
    if($is_insert == 'insert'){
        $date = date("Y-m-d H:i:s");
        $insert = "insert into fmss.history_commit_alfa (id_transaksi, id_produk, bill_info1, date_commit, id_outlet) values ($id_trx, '$id_produk', '$bill_info1', '$date', '$id_outlet')";
        $eqn = $db->query($insert);
        // var_dump($db);
    } else if($is_insert == 'select'){
        $q = "select is_commit from history_commit_alfa where id_outlet = '$id_outlet' and bill_info1 = '$bill_info1' and id_produk = '$id_produk' and date_commit::date = now()::date order by date_commit desc limit 1";
        $eqn    = $db->query($q);
        $row    = $eqn[0];
        $is_commit    = $row->is_commit;
        if($is_commit === '0'){
            return array("89", "Transaksi dalam proses");
        } else if($is_commit === '1'){
            return array("88", "ERROR - Tagihan sudah dibayar");
        } else {
            return array("63", "ERROR - Tidak ada pembayaran");
        }
    } else {
        $update = "update history_commit_alfa set is_commit = 1 where id_outlet = '$id_outlet' and bill_info1 = '$bill_info1' and id_produk = '$id_produk' and date_commit::date = now()::date";
        $eqn = $db->query($update);
    }
}

function get_data_commit_alfamart($idpel, $id_outlet, $id_produk, $flag, $jenis_trx){
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    
    if($flag == 'dev'){
        $__CFG_dbhost2 = "10.0.0.20";
        $__CFG_dbuser2 = "fmss";
        $__CFG_dbpass2 = "rahasia";
        $__CFG_dbname2 = "fmss";
        $__CFG_dbport2 = "5432";
        $db = new PgDBI2($__CFG_dbname2, $__CFG_dbhost2, $__CFG_dbuser2, $__CFG_dbpass2, $__CFG_dbport2);
    } else {
        $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    }
    
    
    $q = "select mid from transaksi where transaction_date = now()::date and bill_info1 = '$idpel' and id_outlet = '$id_outlet' and id_produk = '$id_produk' and jenis_transaksi = $jenis_trx";

    $eqn    = $db->query($q);
    $row    = $eqn[0];
    $mid    = $row->mid;
    if($mid != ""){

        $astrix = "select * from fmss.message where mid = $mid";
        $eqn2    = $db->query($astrix);
        if(count($eqn2) > 0){
            $row2    = $eqn2[0];
            $content = $row2->content;
        }else{
            $content = get_data_commit_alfamart_backup($mid);
        }
        return $content;
    } else {
        return "";
    }

}


function get_data_commit_alfamart_backup($p_mid)
{
    $ret = false;
    global $__CFG_dbname_new, $__CFG_dbhost_new, $__CFG_dbuser_new, $__CFG_dbpass_new, $__CFG_dbport_new;
    $db = new PgDBI2($__CFG_dbname_new, $__CFG_dbhost_new, $__CFG_dbuser_new, $__CFG_dbpass_new, $__CFG_dbport_new);
    $qn = "SELECT * FROM sbf.message WHERE mid = " . $p_mid . "";
    $eqn2 = $db->query($qn);


    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    $row2    = $eqn2[7];
    $content = $row2->content;

    return $content;
}

function getLastMessage($idtrx, $flag){
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    
    if($flag == 'dev'){
        $__CFG_dbhost2 = "10.0.0.20";
        $__CFG_dbuser2 = "fmss";
        $__CFG_dbpass2 = "rahasia";
        $__CFG_dbname2 = "fmss";
        $__CFG_dbport2 = "5432";
        $db = new PgDBI2($__CFG_dbname2, $__CFG_dbhost2, $__CFG_dbuser2, $__CFG_dbpass2, $__CFG_dbport2);
    } else {
        $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    }
    
    
    $q = "select mid from transaksi where response_code = '00' and jenis_transaksi = 1 and id_transaksi = $idtrx";
    $eqn    = $db->query($q);
    $row    = $eqn[0];
    $mid    = $row->mid;
    
    if($mid != ""){
        $astrix = "
        select * from fmss.message where mid = $mid ORDER BY date_created desc limit 1
        ";
        $eqn2    = $db->query($astrix);
        $row2    = $eqn2[0];
        $content = $row2->content;
        return $content;
    } else {
        return "";
    }
}

function getdata2fmbyidpel($data, $uid){
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $id_produk = $data->product;
    $idpel1 = $data->msisdn;
    $kolom = "(bill_info1 = '$idpel1' or bill_info2 = '$idpel1')";
    $qn = "SELECT id_transaksi, nominal, bill_info6 FROM transaksi WHERE jenis_transaksi = 0 AND (status=2 OR status=3) AND response_code = '00' AND time_request::date=NOW()::date AND id_outlet = '".$uid."' AND id_produk='".$id_produk."' AND $kolom and nominal != '0' ORDER BY id_transaksi DESC limit 1";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $data = array(
        'id_transaksi' => $row->id_transaksi,
        'nominal' => $row->nominal,
        'bill_info6' => $row->bill_info6
    );
    return $data;
}

function getmtipulsagame($idproduk){
    $idproduk = trim($idproduk);
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "select b.group_layanan from mt_produk a 
    left join mt_group_produk b
    on a.id_group_produk = b.id_group_produk
    where a.id_produk = '$idproduk' and a.is_active = 1 and a.is_gangguan = 0 limit 1";
    $eqn = $db->query($qn);
    $row = $eqn[0];
    if($row === null){
        return "";
    }
    $mti = $row->group_layanan;
    $mti = explode(" ", $mti);
    return trim($mti[1]);
}

function get_data_alfamart($idoutlet, $kdproduk, $idpel1, $ref1, $rc, $flag){
    $data;
    $add = "";
    if($rc == '00'){
        $add = " AND response_code = '00' ";
    } 
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $date = date('Y-m-d');
    if($flag == "ref2"){
        $kolom = "(bill_info1 = '$idpel1' or bill_info2 = '$idpel1')";
        if($idproduk != ""){
            if(in_array($idproduk, KodeProduk::getPLNPrepaids())){
                if(strlen($idpel1) == '11'){
                    $kolom = "bill_info1 = '$idpel1'";
                } else if(strlen($idpel1) == '12'){
                    $kolom = "bill_info2 = '$idpel1'";
                }
            }
        }
        // $qn = "SELECT id_transaksi FROM fmss.transaksi WHERE id_outlet = '" . $idoutlet."' and id_produk = '".$kdproduk."' and $kolom = '".$idpel1."' and bill_info83 = '".$ref1."' and transaction_date = '".$date."'";
        // echo $qn;
        $qn = "SELECT id_transaksi FROM transaksi WHERE jenis_transaksi = 0 AND (status=2 OR status=3) ".$add." AND time_request::date=NOW()::date AND id_outlet = '".$idoutlet."' AND id_produk='".$kdproduk."' AND $kolom AND via='H2H' and nominal != '0' and response_code = '00' ORDER BY id_transaksi DESC limit 1";       
//die($qn);
        $eqn    = $db->query($qn);
        $row    = $eqn[0];
        $data   = $row->id_transaksi;
        return $data;
    } else if($flag == "id_biller"){
        //$qn = "SELECT id_biller FROM fmss.transaksi WHERE id_outlet = '" . $idoutlet."' and id_produk = '".$kdproduk."' and bill_info1 = '".$idpel1."' and bill_info83 = '".$ref1."' and transaction_date = '".$date."'";
        $qn = "SELECT id_biller FROM transaksi WHERE jenis_transaksi = 0 AND (status=2 OR status=3) ".$add." AND time_request::date=NOW()::date AND id_outlet = '".$idoutlet."' AND id_produk='".$kdproduk."' AND (bill_info1 = '".$idpel1."' or bill_info2 = '".$idpel1."') AND via='H2H' ORDER BY id_transaksi DESC limit 1";
        $eqn    = $db->query($qn);
        $row    = $eqn[0];
        $data   = $row->id_biller;
        // echo $qn;
        return $data;
    }
}

function get_data_alfamartdev($idoutlet, $kdproduk, $idpel1, $ref1, $rc, $flag){
    $__CFG_dbhost2 = "10.0.0.20";
    $__CFG_dbuser2 = "fmss";
    $__CFG_dbpass2 = "rahasia";
    $__CFG_dbname2 = "fmss";
    $__CFG_dbport2 = "5432";
    $data;
    $add = "";
    if($rc == '00'){
        $add = " AND response_code = '00' ";
    } 
    $db = new PgDBI2($__CFG_dbname2, $__CFG_dbhost2, $__CFG_dbuser2, $__CFG_dbpass2, $__CFG_dbport2);
    $date = date('Y-m-d');
    if($flag == "ref2"){
        $kolom = "(bill_info1 = '$idpel1' or bill_info2 = '$idpel1')";
        if($idproduk != ""){
            if(in_array($idproduk, KodeProduk::getPLNPrepaids())){
                if(strlen($idpel1) == '11'){
                    $kolom = "bill_info1 = '$idpel1'";
                } else if(strlen($idpel1) == '12'){
                    $kolom = "bill_info2 = '$idpel1'";
                }
            }
        }
        // $qn = "SELECT id_transaksi FROM fmss.transaksi WHERE id_outlet = '" . $idoutlet."' and id_produk = '".$kdproduk."' and $kolom = '".$idpel1."' and bill_info83 = '".$ref1."' and transaction_date = '".$date."'";
        // echo $qn;
        $qn = "SELECT id_transaksi FROM transaksi WHERE jenis_transaksi = 0 AND (status=2 OR status=3) ".$add." AND time_request::date=NOW()::date AND id_outlet = '".$idoutlet."' AND id_produk='".$kdproduk."' AND $kolom AND via='H2H' and nominal != '0' and response_code = '00' ORDER BY id_transaksi DESC limit 1";       
//die($qn);
        $eqn    = $db->query($qn);
        $row    = $eqn[0];
        $data   = $row->id_transaksi;
        return $data;
    } else if($flag == "id_biller"){
        //$qn = "SELECT id_biller FROM fmss.transaksi WHERE id_outlet = '" . $idoutlet."' and id_produk = '".$kdproduk."' and bill_info1 = '".$idpel1."' and bill_info83 = '".$ref1."' and transaction_date = '".$date."'";
        $qn = "SELECT id_biller FROM transaksi WHERE jenis_transaksi = 0 AND (status=2 OR status=3) ".$add." AND time_request::date=NOW()::date AND id_outlet = '".$idoutlet."' AND id_produk='".$kdproduk."' AND (bill_info1 = '".$idpel1."' or bill_info2 = '".$idpel1."') AND via='H2H' ORDER BY id_transaksi DESC limit 1";
        $eqn    = $db->query($qn);
        $row    = $eqn[0];
        $data   = $row->id_biller;
        // echo $qn;
        return $data;
    }
}

function cekIsNominalTransaksi_devel($id_transaksi) {

    $ret;
    global $__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel;
    $db = new PgDBI2($__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel);

    $qn = "SELECT nominal FROM fmss.transaksi WHERE id_transaksi = " . $id_transaksi;

    $eqn = $db->query($qn);
    $row = $eqn[0];
    // print_r($eqn);
    // die();
    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    //if($eqn > 0){
    $ret = $row->nominal;
    //}
    return $ret;
}

function getStatusMid($p_mid) {
    $ret = false;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "SELECT mid FROM fmss.message WHERE mid = " . $p_mid . " AND step = 3  LIMIT 1";
    $eqn = $db->query($qn, "numrow");
    //echo "eqn = ".$eqn."\r\n";
    if ($eqn > 0) {
        $ret = true;
    }else{
        $ret = getStatusMidNew($p_mid);
    }
    return $ret;
}

function getStatusMidNew($p_mid)
{
    $ret = false;
    global $__CFG_dbname_new, $__CFG_dbhost_new, $__CFG_dbuser_new, $__CFG_dbpass_new, $__CFG_dbport_new;
    $db = new PgDBI2($__CFG_dbname_new, $__CFG_dbhost_new, $__CFG_dbuser_new, $__CFG_dbpass_new, $__CFG_dbport_new);

    $qn = "SELECT mid FROM sbf.message WHERE mid = " . $p_mid . " AND step = 3 LIMIT 1";
    $eqn = $db->query($qn, "numrow");
    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    if ($eqn > 0) {
        $ret = true;
    }

    return $ret;
}

function h2h_log_custom_rc($mid,$id_outlet,$rc,$response_core)
{
    $ret = false;
    global $__CFG_dbname_new, $__CFG_dbhost_new, $__CFG_dbuser_new, $__CFG_dbpass_new, $__CFG_dbport_new;
    $db = new PgDBI2($__CFG_dbname_new, $__CFG_dbhost_new, $__CFG_dbuser_new, $__CFG_dbpass_new, $__CFG_dbport_new);

    $qn = "INSERT INTO sbf.h2h_log_custom_rc(
    mid, id_outlet, rc, response_core)
    VALUES ('".$mid."', '".$id_outlet."', '".$rc."', '".$response_core."')";
    try {
       $eqn = $db->query($qn);
    } catch (Exception $e) {
      
    }

    
}

function getStatusMid_devel($p_mid) {
    $ret = false;
    global $__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel;
    $db = new PgDBI2($__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel);

    $qn = "SELECT mid FROM fmss.message WHERE mid = " . $p_mid . " AND step = 3 UNION SELECT mid FROM fmss.message_final WHERE mid = " . $p_mid . " AND step = 3 LIMIT 1";
    $eqn = $db->query($qn, "numrow");
    //echo $qn."\r\n";
    //echo "eqn = ".$eqn."\r\n";
    if ($eqn > 0) {
        $ret = true;
    }
    return $ret;
}

function insertSuksesPaksaMy($mid) {
    $db = new Database();
    $q_ins_paksa = "INSERT INTO edc_sukses_paksa (mid, insert_date, insert_time) VALUES (" . $mid . ", CURDATE(), CURTIME())";
    $e_ins_paksa = mysql_query($q_ins_paksa, $db->getConnection());
}

function insertSuksesPaksaMy_devel($mid) {
    $db = new Database_devel();
    $q_ins_paksa = "INSERT INTO edc_sukses_paksa (mid, insert_date, insert_time) VALUES (" . $mid . ", CURDATE(), CURTIME())";
    $e_ins_paksa = mysql_query($q_ins_paksa, $db->getConnection());
}

function insertSuksesPaksaPg($mid) {
    // var_dump($mid);
    //die();
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "INSERT INTO fmss.edc_sukses_paksa (mid, insert_date, insert_time) VALUES (" . $mid . ", current_date, localtime)";
    //echo "qn = ".$qn."\r\n";
    $eqn = $db->query($qn);
}

function insertSuksesPaksaPg_devel($mid) {
    // var_dump($mid);
    //die();
    global $__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel;
    $db = new PgDBI2($__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel);

    $qn = "INSERT INTO fmss.edc_sukses_paksa (mid, insert_date, insert_time) VALUES (" . $mid . ", current_date, localtime)";
    //echo "qn = ".$qn."\r\n";
    $eqn = $db->query($qn);
}

function replace_forbidden_chars_msg($text) {
    $arrSearch = array("'", "\r", "\n", "\t");
    $arrReplace = array("`", " ", " ", " ");
    return str_replace($arrSearch, $arrReplace, $text);
}

function getRespInq($p_id_transaksi) {
    $db = new Database();
    $q_sel_log = "SELECT content FROM fmss_message WHERE id_transaksi = " . $p_id_transaksi . " LIMIT 1";
    $e_sel_log = mysql_query($q_sel_log, $db->getConnection());
    $r_sel_log = mysql_fetch_object($e_sel_log);

    return $r_sel_log->content;
}

function getRespInq_devel($p_id_transaksi) {
    $db = new Database_devel();
    $q_sel_log = "SELECT content FROM fmss_message WHERE id_transaksi = " . $p_id_transaksi . " LIMIT 1";
    $e_sel_log = mysql_query($q_sel_log, $db->getConnection());
    $r_sel_log = mysql_fetch_object($e_sel_log);

    return $r_sel_log->content;
}

function setMandatoryResponText($frm, $ref1, $ref2, $ref3, $isdevel) {
    $r_idoutlet = $frm->getMember();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    if($r_idoutlet == "HH146493" && substr($r_kdproduk, 0, 2) == "KK"){ // traveloka
        $r_idpel1 = maskString($frm->getIdPel1(),4);
    } else if($r_idoutlet == "HH146493" && $r_kdproduk == "ASRCAR"){
        $r_idpel1 = substr($frm->getIdPel1(),5);
    } else {
        $r_idpel1 = $frm->getIdPel1();
    }
    
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    
    $r_pin = "------";//$frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    if($r_status == '68' && in_array($r_kdproduk, array('SPEEDY', 'TELEPON'))){
        $r_status = '00';
        $r_keterangan = 'SEDANG DIPROSES';
    } else {
        $r_status = $frm->getStatus();
        $r_keterangan = $frm->getKeterangan();
    }

    if($isdevel == false){
        $fee = -2000;
    } else {
        $fee = getnominalup($r_idtrx);
    }

    $saldo_terpotong = $r_nominal + $r_nominaladmin + (int)$fee;
    
    if($r_status != '00' || (substr($r_kdproduk, 0,6) == "PLNPRA") && $r_nominal == 0){
        $fee = $saldo_terpotong = 0;
    }

    if( ($r_kdproduk == "TELEPON" || $r_kdproduk == "SPEEDY") && $r_nominal == '0' && $r_status == '00' && $r_keterangan != 'SEDANG DIPROSES'){
        $r_status = "88";
        $r_keterangan = "TRANSAKSI DITOLAK KARENA SEMUA ATAU SALAH SATU TUNGGAKAN/TAGIHAN SUDAH DIBAYAR.";
        $fee = $saldo_terpotong = 0;
    }

    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) $r_pin, (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan, (string)$fee, (string)$saldo_terpotong
    );
    
    return $params;
}

function setMandatoryResponJson($frm, $ref1, $ref2, $ref3, $isdevel) {
    $r_idoutlet = $frm->getMember();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    if($r_idoutlet == "HH146493" && substr($r_kdproduk, 0, 2) == "KK"){ // traveloka
        $r_idpel1 = maskString($frm->getIdPel1(),4);
    } else if($r_idoutlet == "HH146493" && $r_kdproduk == "ASRCAR"){
        $r_idpel1 = substr($frm->getIdPel1(),5);
    } else {
        $r_idpel1 = $frm->getIdPel1();
    }
    
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    
    $r_pin = "------";//$frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    if($r_status == '68' && in_array($r_kdproduk, array('SPEEDY', 'TELEPON'))){
        $r_status = '00';
        $r_keterangan = 'SEDANG DIPROSES';
    } else {
        $r_status = $frm->getStatus();
        $r_keterangan = $frm->getKeterangan();
    }

    if($isdevel == false){
        $fee = -2000;
    } else {
        $fee = getnominalup($r_idtrx);
    }

    $saldo_terpotong = $r_nominal + $r_nominaladmin + (int)$fee;
    
    if($r_status != '00' || (substr($r_kdproduk, 0,6) == "PLNPRA") && $r_nominal == 0){
        $fee = $saldo_terpotong = 0;
    }
    if($r_idoutlet == "HH226766"){
        if(substr($r_kdproduk, 0,6) == "PLNNON" || substr($r_kdproduk, 0,7) == "PLNPASC"){
            if($r_status == "34"){
                $r_status = "88";
                $r_keterangan = "Tagihan sudah dibayar";
            }
        }elseif (substr($r_kdproduk,0, 9) == "ASRBPJSKS") {
            if($r_status == "54" || $r_status == "1107"){
                $r_status = "88";
                $r_keterangan = "Tagihan sudah dibayar";
            }
        }
    }
    if( ($r_kdproduk == "TELEPON" || $r_kdproduk == "SPEEDY") && $r_nominal == '0' && $r_status == '00' && $r_keterangan != 'SEDANG DIPROSES'){
        $r_status = "88";
        $r_keterangan = "TRANSAKSI DITOLAK KARENA SEMUA ATAU SALAH SATU TUNGGAKAN/TAGIHAN SUDAH DIBAYAR.";
        $fee = $saldo_terpotong = 0;
    }

    if($r_kdproduk == 'WASBY' && $r_status == '25'){
        $r_keterangan = "Tagihan sudah terbayar.";
    }

    if(substr($r_kdproduk, 0,2) == "WA" && strtoupper($r_status) == 'XX' && (strpos(strtolower($r_keterangan),'report') !== false)){
        $r_keterangan = "Tagihan sudah terbayar.";
    }


    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) "------", (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan, (string)$fee, (string)$saldo_terpotong
    );
    
    return $params;
}

// Untuk Keperluan sisa saldo
function setMandatoryResponJsonLast($frm, $ref1, $ref2, $ref3, $isdevel) {
    $r_idoutlet = $frm->getMember();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    if($r_idoutlet == "HH146493" && substr($r_kdproduk, 0, 2) == "KK"){ // traveloka
        $r_idpel1 = maskString($frm->getIdPel1(),4);
    } else if($r_idoutlet == "HH146493" && $r_kdproduk == "ASRCAR"){
        $r_idpel1 = substr($frm->getIdPel1(),5);
    } else {
        $r_idpel1 = $frm->getIdPel1();
    }
    
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    
    $r_pin = "------";//$frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    if($r_status == '68' && in_array($r_kdproduk, array('SPEEDY', 'TELEPON'))){
        $r_status = '00';
        $r_keterangan = 'SEDANG DIPROSES';
    } else {
        $r_status = $frm->getStatus();
        $r_keterangan = $frm->getKeterangan();
    }

    if($isdevel == false){
        $fee = -2000;
    } else {
        $fee = getnominalup($r_idtrx);
    }

    $saldo_terpotong = $r_nominal + $r_nominaladmin + (int)$fee;
    
    if($r_status != '00' || (substr($r_kdproduk, 0,6) == "PLNPRA") && $r_nominal == 0){
        $fee = $saldo_terpotong = 0;
    }

    if( ($r_kdproduk == "TELEPON" || $r_kdproduk == "SPEEDY") && $r_nominal == '0' && $r_status == '00' && $r_keterangan != 'SEDANG DIPROSES'){
        $r_status = "88";
        $r_keterangan = "TRANSAKSI DITOLAK KARENA SEMUA ATAU SALAH SATU TUNGGAKAN/TAGIHAN SUDAH DIBAYAR.";
        $fee = $saldo_terpotong = 0;
    }

    if($r_kdproduk == 'WASBY' && $r_status == '25'){
        $r_keterangan = "Tagihan sudah terbayar.";
    }

    if(substr($r_kdproduk, 0,2) == "WA" && strtoupper($r_status) == 'XX' && (strpos(strtolower($r_keterangan),'report') !== false)){
        $r_keterangan = "Tagihan sudah terbayar.";
    }

    
    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) "------", (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan, (string)$fee, (string)$saldo_terpotong, (string) $r_saldo
    );
    
    return $params;
}
function getnamabank($kodebank)
{
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $x = "";
    $q = "select nama_bank from fmss.bni_laku_pandai_kode_bank where kode_bank_bni = '$kodebank'";
    // echo $q;
   
    $eqn = $db->query($q);
    $row = $eqn[0];
    $ret = $row->nama_bank;
    return $ret;   
}

function setMandatoryResponJsonTransferDana($frm, $ref1, $ref2, $ref3,$kodebank, $isdevel) {
    $r_idoutlet = $frm->getMember();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_namabank = getnamabank($kodebank);
    $r_pin = "------";//$frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    if($r_status == '68' && in_array($r_kdproduk, array('SPEEDY', 'TELEPON'))){
        $r_status = '00';
        $r_keterangan = 'SEDANG DIPROSES';
    } else {
        $r_status = $frm->getStatus();
        $r_keterangan = $frm->getKeterangan();
    }

    if($isdevel == false){
        $fee = -2000;
    } else {
        $fee = getnominalup($r_idtrx);
    }

    $saldo_terpotong = $r_nominal + $r_nominaladmin + (int)$fee;
    
    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3,(string) $r_namabank,(string) $kodebank, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) "------", (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan, (string)$fee, (string)$saldo_terpotong
    );
    
    return $params;
}

function setMandatoryResponJsonTransferDanaLast($frm, $ref1, $ref2, $ref3,$kodebank, $isdevel) {
    $r_idoutlet = $frm->getMember();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_namabank = getnamabank($kodebank);
    $r_pin = "------";//$frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    if($r_status == '68' && in_array($r_kdproduk, array('SPEEDY', 'TELEPON'))){
        $r_status = '00';
        $r_keterangan = 'SEDANG DIPROSES';
    } else {
        $r_status = $frm->getStatus();
        $r_keterangan = $frm->getKeterangan();
    }

    if($isdevel == false){
        $fee = -2000;
    } else {
        $fee = getnominalup($r_idtrx);
    }

    $saldo_terpotong = $r_nominal + $r_nominaladmin + (int)$fee;
    
    $params = array(
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3,(string) $r_namabank,(string) $kodebank, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) "------", (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan, (string)$fee, (string)$saldo_terpotong , (string)$r_saldo
    );
    
    return $params;
}

function setMandatoryRespon($frm, $ref1, $ref2, $ref3) {
    // var_dump($frm);
    // die();
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
    if($r_status == '68' && in_array($r_kdproduk, array('SPEEDY', 'TELEPON'))){
        $r_status = '00';
        $r_keterangan = 'SEDANG DIPROSES';
    } else {
        $r_status = $frm->getStatus();
        $r_keterangan = $frm->getKeterangan();
        $r_keterangan = str_replace("EXT :", "EXT:", $r_keterangan);
    }
    $r_nominalplusadmin = 0 ;
    if($r_idoutlet == "HH10632"){
        if($r_kdproduk == "HPXL"){
            $r_nominalplusadmin = $r_nominal + $r_nominaladmin;
            $params = array(
                (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nominalplusadmin,"0", (string) $r_idoutlet, (string) "------", (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan
            );
        }else{
            $params = array(
                (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) "------", (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan
            );
        }
    }else{
        $params = array(
            (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) "------", (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan
        );
    }

    return $params;
}

function setMandatoryResponIDM($frm, $ref1, $ref2, $ref3) {
    // var_dump($frm);
    // die();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    
    if(substr($r_kdproduk, 0, 2) == "WA"){
        $arr_nosam = array('WAMJK', 'WATAPIN', 'WABGK');
        if(in_array($r_kdproduk, $arr_nosam)){
            $r_idpel1 = $frm->getIdPel2();
            $r_idpel2 = $frm->getIdPel2();
        } else {
            $r_idpel1 = $frm->getIdPel1();
            $r_idpel2 = $frm->getIdPel1();
        }
    } else {
        $r_idpel1 = $frm->getIdPel1();
        $r_idpel2 = $frm->getIdPel2();
    }

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
        (string) $r_kdproduk, (string) $r_tanggal, (string) $r_idpel1, (string) $r_idpel2, (string) $r_idpel3, (string) $r_nominal, (string) $r_nominaladmin, (string) $r_idoutlet, (string) "------", (string) $ref1, (string) $r_idtrx, (string) $ref3, (string) $r_status, (string) $r_keterangan
    );

    return $params;
}

function getResponBpjsArray($kdproduk, $p_params, $resp) {


    $man = FormatMsg::mandatoryPaymentBpjs();

    if (in_array($kdproduk, KodeProduk::getAsuransiBPJS())) {
        $format = FormatMsg::bpjs();
        $frm = new FormatBpjs($man["pay"] . "*" . $format, $resp);
        $params = retBpjs($p_params, $frm);
    }
    return $params;
}

function getResponTiketuxArray($kdproduk, $p_params, $resp) {
    $man = FormatMsg::mandatoryPaymentTiketux();

    if (in_array($kdproduk, KodeProduk::getTravelTiketux())) {
        $format = FormatMsg::tiketux();
        $frm = new FormatTiketux($man["pay"] . "*" . $format, $resp);
        $params = retTiketux($p_params, $frm);
    }
    return $params;
}

function getResponEcashTopupArray($kdproduk, $p_params, $resp) {

    $man = FormatMsg::mandatoryPaymentEcash();

   // if (in_array($kdproduk, KodeProduk::getFirstLogistic())) {
        $format = FormatMsg::ecash();
        $frm = new FormatEcash($man["paytop"] . "*" . $format, $resp);
        $params = retEcash($p_params, $frm);  
    //}
    return $params;
}

function getResponEcashPurchaseArray($kdproduk, $p_params, $resp) {

    $man = FormatMsg::mandatoryPaymentEcash();

   // if (in_array($kdproduk, KodeProduk::getFirstLogistic())) {
        $format = FormatMsg::ecash();
        $frm = new FormatEcash($man["paypurch"] . "*" . $format, $resp);
        $params = retEcash($p_params, $frm);  
    //}
    return $params;
}

function getResponEcashCashoutArray($kdproduk, $p_params, $resp) {

    $man = FormatMsg::mandatoryPaymentEcash();

   // if (in_array($kdproduk, KodeProduk::getFirstLogistic())) {
        $format = FormatMsg::ecash();
        $frm = new FormatEcash($man["paycasho"] . "*" . $format, $resp);
        $params = retEcash($p_params, $frm);  
    //}
    return $params;
}

function getResponFirstLogisticArray($kdproduk, $p_params, $resp) {

    $man = FormatMsg::mandatoryPaymentFirstlogistic();

   // if (in_array($kdproduk, KodeProduk::getFirstLogistic())) {
        $format = FormatMsg::firstlogistic();
        $frm = new FormatFirstLogistic($man["pay"] . "*" . $format, $resp);
        $params = retFirstlogistic($p_params, $frm);
    //}
    return $params;
}

function getResponCarRentalArray($kdproduk, $p_params, $resp) {

    $man = FormatMsg::mandatoryPaymentCarRental();

   
        $format = FormatMsg::carrental();
        $frm = new FormatCarRental($man["pay"] . "*" . $format, $resp);
        $params = retCarRental($p_params, $frm);
   
    return $params;
}

function getResponFcashArray($kdproduk, $p_params, $resp) {

    $man = FormatMsg::mandatoryPaymentFcash();

        $format = FormatMsg::fcash();
        $frm = new FormatFcash($man["pay"] . "*" . $format, $resp);
        $params = retFcash($p_params, $frm);  
   
    return $params;
}

function getResponArray($kdproduk, $p_params, $resp) { 
    $man = FormatMsg::mandatoryPayment();

    if ($kdproduk == KodeProduk::getTelepon()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["pay"] . "*" . $format, $resp);
        //print_r($frm->data);
        $params = retTelepon($p_params, $frm);
        //print_r($params);
        if($params[7] === 'HH10632' && ($params[0] === "SPEEDY" || $params[0] === "TELEPON") ){//kudo
            $params[0] = 'TELEPON';
        }
        
    } else if ($kdproduk == KodeProduk::getSpeedy()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["pay"] . "*" . $format, $resp);
        $params = retSpeedy($p_params, $frm);
        //print_r($params);
        if($params[7] === 'HH10632' && ($params[0] === "SPEEDY" || $params[0] === "TELEPON") ){//kudo
            $params[0] = 'TELEPON';
        }
        
    } else if ($kdproduk == KodeProduk::getTelkomVision()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["pay"] . "*" . $format, $resp);
        $params = retTelkomVision($p_params, $frm);
        //print_r($params);
    } else if ($kdproduk == KodeProduk::getPLNPostpaid()) {
        $format = FormatMsg::plnPasca();
        $frm = new FormatPlnPasc($man["pay"] . "*" . $format, $resp);
        //print_r($frm->data);
        //print_r($p_params);
        $params = retPLNPostpaid($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNPostpaids())) {
        //replace
        $p_params[0] = "PLNPASC";
        $format = FormatMsg::plnPasca();
        $frm = new FormatPlnPasc($man["pay"] . "*" . $format, $resp);
        $params = retPLNPostpaid($p_params, $frm);
        if($params[7] === 'HH95173' || $params[7] === 'SP141894'|| $params[7] === 'SP31560'|| $params[7] === 'SP365293'|| $params[7] === 'HH226766'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $format = FormatMsg::plnPra();
        $frm = new FormatPlnPra($man["pay"] . "*" . $format, $resp);
        $params = retPLNPrepaid($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNPrepaids()) ||  substr($kdproduk,0,6) === 'PLNPRA') {
        //replace
        $p_params[0] = "PLNPRA";
        $format = FormatMsg::plnPra();
        $frm = new FormatPlnPra($man["pay"] . "*" . $format, $resp);
        $params = retPLNPrepaid($p_params, $frm);
        if($params[7] === 'HH95173' || $params[7] === 'SP141894'|| $params[7] === 'SP31560'|| $params[7] === 'SP365293'|| $params[7] === 'HH226766'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if ($kdproduk == KodeProduk::getPLNNontaglist() || substr($kdproduk, 0,6) == "PLNNON" ) {

        $format = FormatMsg::plnNon();
        $frm = new FormatPlnNon($man["pay"] . "*" . $format, $resp);
        $params = retPLNNontaglist($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNNontaglists())) {
        //replace
        $p_params[0] = "PLNNON";
        $format = FormatMsg::plnNon();
        $frm = new FormatPlnNon($man["pay"] . "*" . $format, $resp);
        $params = retPLNNontaglist($p_params, $frm);
        //nominal ganti billplnvalue
        if($params[7] === 'SP31560' || $params[7] === 'SP365293'|| $params[7] === 'HH226766'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if (in_array($kdproduk, KodeProduk::getPonselPostpaid())) {
        $format = FormatMsg::teleponPasc();
        $frm = new FormatTeleponPasc($man["pay"] . "*" . $format, $resp);
        $params = retPonselPostpaid($p_params, $frm);
    } else if ( (in_array($kdproduk, KodeProduk::getPAM()) || substr($kdproduk,0,2) === 'WA') && $kdproduk !== "WASBY" ) {

        $format = FormatMsg::pdam();
        $frm = new FormatPdam($man["pay"] . "*" . $format, $resp);
        $params = retPAM($p_params, $frm);
    } else if ($kdproduk == KodeProduk::getAoraTV()) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["pay"] . "*" . $format, $resp);
        $params = retAORATV($p_params, $frm);
        //}else if($kdproduk==KodeProduk::getWOM() || $kdproduk==KodeProduk::getBAF() || $kdproduk==KodeProduk::getMAF() || $kdproduk==KodeProduk::getMCF()){
    } else if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $format = FormatMsg::multiFinance();
        $frm = new FormatMultiFinance($man["pay"] . "*" . $format, $resp);
        $params = retMultifinance($p_params, $frm);
    } else if ($kdproduk == "BLLWK") {
        $format = FormatMsg::billpayment();
        $frm = new FormatBillPayment($man["pay"] . "*" . $format, $resp);
        $params = retBillpayment($p_params, $frm);
    } else if ($kdproduk == "TVINDVS" || $kdproduk == "TVCENTRIN" || substr($kdproduk, 0, 5) == 'TVSKY') {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["pay"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getAsuransi())) {
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["pay"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
        if($kdproduk == 'ASRBPJSKSH'){
            $params[0] = 'ASRBPJSKS';
        }
    } else if (substr($kdproduk, 0,2) == "KK") {
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["pay"] . "*" . $format, $resp);
        $params = retKartuKredit($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getNewPAM()) && $kdproduk === "WASBY") {
        $format = FormatMsg::newPdam();
        $frm = new FormatNewPdam($man["pay"] . "*" . $format, $resp);
        $params = retNewPAM($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getOrangeTv())) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["pay"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getOrangeTv())) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["pay"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getTVKabel())) {
        $format = FormatMsg::tvKabel(); 
        $frm = new FormatTvKabel($man["pay"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getAsuransiTokioMarine())) {
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["pay"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
    }else if(in_array($kdproduk, KodeProduk::getAdiraAxi())){ 
        $format = FormatMsg::adiraaxi();
        $frm = new FormatAdiraAxi($man["pay"] . "*" . $format, $resp);
        $params = retAdiraaxi($p_params, $frm);

    }else if($kdproduk == "ASRBPJSKS"){ 
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["pay"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);

    }else if($kdproduk == "ASRBPJSKSH"){         
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["pay"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
        $params[0] = 'ASRBPJSKS';
    }else if($kdproduk == "ASRBPJSKSD"){ 
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["pay"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
    }else if ($kdproduk=="IKLNBRS") {
        $format = FormatMsg::IklanBaris();
        $frm = new FormatIklanBaris($man["pay"] . "*" . $format, $resp);
        $params = retIklanbaris($p_params, $frm);
    }else if (substr($kdproduk, 0, 3) == 'PKB') {
        $format = FormatMsg::pkb();
        $frm = new FormatPKB($man["pay"] . "*" . $format, $resp);
        $params = retPKB($p_params, $frm);
    }else if (in_array($kdproduk, KodeProduk::getSbf())) {
        $format = FormatMsg::sbf();
        $frm = new FormatSbf($man["pay"] . "*" . $format, $resp);
        $params = retSbf($p_params, $frm);
    } else if(substr($kdproduk, 0, 5) == 'PAJAK'){
        $format = FormatMsg::pajakSolo();
        $frm = new FormatPajakPbb($man["pay"]."*".$format,$resp);
        $params = retPajakPbb($p_params,$frm);
    } else if($kdproduk == "PGN"){
        $format = FormatMsg::pgn();
        $frm = new FormatPgn($man["pay"]."*".$format,$resp);
        $params = retPgn($p_params,$frm);
    } else if(substr($kdproduk, 0, 2) == 'RZ'){
        $format = FormatMsg::zakat();
        $frm = new FormatZakat($man["pay"]."*".$format,$resp);
        $params = retZakat($p_params,$frm);
    } else if($kdproduk == 'GAS'){
        $format = FormatMsg::gas();
        $frm = new FormatGas($man["pay"]."*".$format,$resp);
        $params = retGas($p_params,$frm);
    }else if(substr(strtoupper($kdproduk), 0,5) == 'BLTRF'){
        $format = FormatMsg::lakuPandai();
        $frm = new FormatLakuPandai($man["pay"]."*".$format,$resp);
        $params = retLakuPandai($p_params, $frm);
    }
    // if($params[7] == 'FA32670' ){
 
        $bu = $params;
        $bu[8] = "------";
        $log = json_encode($bu);
        // $log = str_replace('<string/>', '<string></string>', $log);
        writeLog_OLD($GLOBALS["mid"], 99, $GLOBALS['sender'], $GLOBALS['receiver'], $log, $GLOBALS["via"]);
    // }

    return $params;
}

function getResponArrayInq($kdproduk, $p_params, $resp) {    
    $man = FormatMsg::mandatoryPayment();
    if ($kdproduk == KodeProduk::getTelepon()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["inq"] . "*" . $format, $resp);
        //print_r($frm->data);
        $params = retTelepon($p_params, $frm);
        //print_r($params);
    } else if ($kdproduk == KodeProduk::getSpeedy()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["inq"] . "*" . $format, $resp);
        $params = retSpeedy($p_params, $frm);
        //print_r($params);
    } else if ($kdproduk == KodeProduk::getTelkomVision()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["inq"] . "*" . $format, $resp);
        $params = retTelkomVision($p_params, $frm);
        //print_r($params);
    } else if ($kdproduk == KodeProduk::getPLNPostpaid()) {
        $format = FormatMsg::plnPasca();
        $frm = new FormatPlnPasc($man["inq"] . "*" . $format, $resp);
        $params = retPLNPostpaid($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNPostpaids())) {
        //replace
        $p_params[0] = "PLNPASC";
        $format = FormatMsg::plnPasca();
        $frm = new FormatPlnPasc($man["inq"] . "*" . $format, $resp);
        $params = retPLNPostpaid($p_params, $frm);
        if($params[7] === 'HH95173' || $params[7] === 'SP141894'|| $params[7] === 'SP31560'|| $params[7] === 'SP365293'|| $params[7] === 'HH226766'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $format = FormatMsg::plnPra();
        $frm = new FormatPlnPra($man["inq"] . "*" . $format, $resp);
        $params = retPLNPrepaid($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        //replace
        $p_params[0] = "PLNPRA";
        $format = FormatMsg::plnPra();
        $frm = new FormatPlnPra($man["inq"] . "*" . $format, $resp);
        $params = retPLNPrepaid($p_params, $frm);
        if($params[7] === 'HH95173' || $params[7] === 'SP141894'|| $params[7] === 'SP31560'|| $params[7] === 'SP365293'|| $params[7] === 'HH226766'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if ($kdproduk == KodeProduk::getPLNNontaglist()) {
    
        $format = FormatMsg::plnNon();
        $frm = new FormatPlnNon($man["inq"] . "*" . $format, $resp);
        $params = retPLNNontaglist($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNNontaglists())) {
        //replace
        $p_params[0] = "PLNNON";
        $format = FormatMsg::plnNon();
        $frm = new FormatPlnNon($man["inq"] . "*" . $format, $resp);
        $params = retPLNNontaglist($p_params, $frm);

        //nominal ganti billplnvalue
        // echo  $params[5];die();
         if($params[7] === 'SP31560' || $params[7] === 'HH226766' || $params[7] === 'SP365293'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if (in_array($kdproduk, KodeProduk::getPonselPostpaid())) {
        $format = FormatMsg::teleponPasc();
        $frm = new FormatTeleponPasc($man["inq"] . "*" . $format, $resp);
        $params = retPonselPostpaid($p_params, $frm);
    } else if ( (in_array($kdproduk, KodeProduk::getPAM()) || substr($kdproduk,0,2) === 'WA') && $kdproduk !== "WASBY" ) {
        $format = FormatMsg::pdam();
        $frm = new FormatPdam($man["inq"] . "*" . $format, $resp);
        $params = retPAM($p_params, $frm);
    } else if ($kdproduk == KodeProduk::getAoraTV()) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["inq"] . "*" . $format, $resp);
        $params = retAORATV($p_params, $frm);
        //}else if($kdproduk==KodeProduk::getWOM() || $kdproduk==KodeProduk::getBAF() || $kdproduk==KodeProduk::getMAF() || $kdproduk==KodeProduk::getMCF()){
    } else if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $format = FormatMsg::multiFinance();
        $frm = new FormatMultiFinance($man["inq"] . "*" . $format, $resp);
        $params = retMultifinance($p_params, $frm);
    } else if ($kdproduk == 'BLLWK') {
        $format = FormatMsg::billpayment();
        $frm = new FormatBillPayment($man["inq"] . "*" . $format, $resp);
        $params = retBillpayment($p_params, $frm);
    } else if ($kdproduk == "TVINDVS" || $kdproduk == "TVCENTRIN" || substr($kdproduk, 0, 5) == 'TVSKY') {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["inq"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getAsuransi())) {
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["inq"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
        if($kdproduk == 'ASRBPJSKSH'){
            $params[0] = 'ASRBPJSKS';
        }
    } else if (in_array($kdproduk, KodeProduk::getKartuKredit()) || substr(strtoupper($kdproduk), 0,2) == 'KK') {
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["inq"] . "*" . $format, $resp);
        $params = retKartuKredit($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getNewPAM()) && $kdproduk === "WASBY") {
        $format = FormatMsg::newPdam();
        $frm = new FormatNewPdam($man["inq"] . "*" . $format, $resp);
        $params = retNewPAM($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getOrangeTv())) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["inq"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getOrangeTv())) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["inq"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getTVKabel())) {
        $format = FormatMsg::tvKabel(); 
        $frm = new FormatTvKabel($man["inq"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getAsuransiTokioMarine())) {
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["inq"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
    } else if(in_array($kdproduk, KodeProduk::getNewAsuransi())){ 
        $format = FormatMsg::newAsuransi();
        $frm = new FormatNewAsuransi($man["inq"] . "*" . $format, $resp);
        $params = retNewAsuransi($p_params, $frm);

    }else if(in_array($kdproduk, KodeProduk::getAdiraAxi())){ 
        $format = FormatMsg::adiraaxi();
        $frm = new FormatAdiraAxi($man["inq"] . "*" . $format, $resp);
        $params = retAdiraaxi($p_params, $frm);

    }else if($kdproduk == "ASRBPJSKS"){ 
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["inq"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);

    }else if($kdproduk == "ASRBPJSKSD"){ 
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["inq"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
    }else if ($kdproduk=="IKLNBRS") {
        $format = FormatMsg::IklanBaris();
        $frm = new FormatIklanBaris($man["inq"] . "*" . $format, $resp);
        $params = retIklanbaris($p_params, $frm);
    }else if (substr($kdproduk, 0, 3) == 'PKB') {
        $format = FormatMsg::pkb();
        $frm = new FormatPKB($man["inq"] . "*" . $format, $resp);
        $params = retPKB($p_params, $frm);
    }else if (in_array($kdproduk, KodeProduk::getSbf())) {
        $format = FormatMsg::sbf();
        $frm = new FormatSbf($man["inq"] . "*" . $format, $resp);
        $params = retSbf($p_params, $frm);
    } else if(substr($kdproduk, 0, 5) == 'PAJAK'){
        $format = FormatMsg::pajakSolo();
        $frm = new FormatPajakPbb($man["inq"]."*".$format,$resp);
        $params = retPajakPbb($p_params,$frm);
    } else if($kdproduk == "PGN"){
        $format = FormatMsg::pgn();
        $frm = new FormatPgn($man["inq"]."*".$format,$resp);
        $params = retPgn($p_params,$frm);
    } else if(substr($kdproduk, 0, 2) == "RZ"){
        $format = FormatMsg::zakat();
        $frm = new FormatZakat($man["inq"]."*".$format,$resp);
        $params = retZakat($p_params,$frm);
    } else if(substr($kdproduk, 0, 2) == "KK") {
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["inq"] . "*" . $format, $resp);
        $params = retKartuKredit($p_params, $frm);
    } else if($kdproduk == "GAS") {
        $format = FormatMsg::gas();
        $frm = new FormatGas($man["inq"] . "*" . $format, $resp);
        $params = retGas($p_params, $frm);
    } else if(substr(strtoupper($kdproduk), 0,5) == 'BLTRF'){
        $format = FormatMsg::lakuPandai();
        $frm = new FormatLakuPandai($man["inq"]."*".$format,$resp);
        $params = retLakuPandai($p_params, $frm);
    }

    return $params;
}

function getResponArrayCu($kdproduk, $p_params, $resp) {    
    $man = FormatMsg::mandatoryPayment();
    if ($kdproduk == KodeProduk::getTelepon()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["cetak"] . "*" . $format, $resp);
        //print_r($frm->data);
        $params = retTelepon($p_params, $frm);
        //print_r($params);
    } else if ($kdproduk == KodeProduk::getSpeedy()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["cetak"] . "*" . $format, $resp);
        $params = retSpeedy($p_params, $frm);
        //print_r($params);
    } else if ($kdproduk == KodeProduk::getTelkomVision()) {
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["cetak"] . "*" . $format, $resp);
        $params = retTelkomVision($p_params, $frm);
        //print_r($params);
    } else if ($kdproduk == KodeProduk::getPLNPostpaid()) {
        $format = FormatMsg::plnPasca();
        $frm = new FormatPlnPasc($man["cetak"] . "*" . $format, $resp);
        $params = retPLNPostpaid($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNPostpaids())) {
        //replace
        $p_params[0] = "PLNPASC";
        $format = FormatMsg::plnPasca();
        $frm = new FormatPlnPasc($man["cetak"] . "*" . $format, $resp);
        $params = retPLNPostpaid($p_params, $frm);
        if($params[7] === 'HH95173' || $params[7] === 'SP141894'|| $params[7] === 'SP31560'|| $params[7] === 'SP365293'|| $params[7] === 'HH226766'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if ($kdproduk == KodeProduk::getPLNPrepaid()) {
        $format = FormatMsg::plnPra();
        $frm = new FormatPlnPra($man["cetak"] . "*" . $format, $resp);
        $params = retPLNPrepaid($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        //replace
        $p_params[0] = "PLNPRA";
        $format = FormatMsg::plnPra();
        $frm = new FormatPlnPra($man["cetak"] . "*" . $format, $resp);
        $params = retPLNPrepaid($p_params, $frm);
        if($params[7] === 'HH95173' || $params[7] === 'SP141894'|| $params[7] === 'SP31560'|| $params[7] === 'SP365293'|| $params[7] === 'HH226766'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if ($kdproduk == KodeProduk::getPLNNontaglist()) {

        $format = FormatMsg::plnNon();
        $frm = new FormatPlnNon($man["cetak"] . "*" . $format, $resp);
        $params = retPLNNontaglist($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getPLNNontaglists())) {
        //replace
        $p_params[0] = "PLNNON";
        $format = FormatMsg::plnNon();
        $frm = new FormatPlnNon($man["cetak"] . "*" . $format, $resp);
        $params = retPLNNontaglist($p_params, $frm);
        //nominal ganti billplnvalue
        if($params[7] === 'SP31560' || $params[7] === 'SP365293'|| $params[7] === 'HH226766'){//ewako mmbc
            $params[0] = $kdproduk;
        }
    } else if (in_array($kdproduk, KodeProduk::getPonselPostpaid())) {
        $format = FormatMsg::teleponPasc();
        $frm = new FormatTeleponPasc($man["cetak"] . "*" . $format, $resp);
        $params = retPonselPostpaid($p_params, $frm);
    } else if ( (in_array($kdproduk, KodeProduk::getPAM()) || substr($kdproduk,0,2) === "WA" ) && $kdproduk !== "WASBY" ) {
        $format = FormatMsg::pdam();
        $frm = new FormatPdam($man["cetak"] . "*" . $format, $resp);
        $params = retPAM($p_params, $frm);
    } else if ($kdproduk == KodeProduk::getAoraTV()) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["cetak"] . "*" . $format, $resp);
        $params = retAORATV($p_params, $frm);
        //}else if($kdproduk==KodeProduk::getWOM() || $kdproduk==KodeProduk::getBAF() || $kdproduk==KodeProduk::getMAF() || $kdproduk==KodeProduk::getMCF()){
    } else if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $format = FormatMsg::multiFinance();
        $frm = new FormatMultiFinance($man["cetak"] . "*" . $format, $resp);
        $params = retMultifinance($p_params, $frm);
    } else if ($kdproduk == 'BLLWK') {
        $format = FormatMsg::billpayment();
        $frm = new FormatBillPayment($man["cetak"] . "*" . $format, $resp);
        $params = retBillpayment($p_params, $frm);
    } else if ($kdproduk == "TVINDVS" || $kdproduk == "TVCENTRIN" || substr($kdproduk, 0, 5) == 'TVSKY') {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["cetak"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getAsuransi())) {
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["cetak"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
        if($kdproduk == 'ASRBPJSKSH'){
            $params[0] = 'ASRBPJSKS';
        }
    } else if (in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["cetak"] . "*" . $format, $resp);
        $params = retKartuKredit($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getNewPAM()) && $kdproduk === "WASBY") {
        $format = FormatMsg::newPdam();
        $frm = new FormatNewPdam($man["cetak"] . "*" . $format, $resp);
        $params = retNewPAM($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getOrangeTv())) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["cetak"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getOrangeTv())) {
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["cetak"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getTVKabel())) {
        $format = FormatMsg::tvKabel(); 
        $frm = new FormatTvKabel($man["cetak"] . "*" . $format, $resp);
        $params = retTvKabel($p_params, $frm);
    } else if (in_array($kdproduk, KodeProduk::getAsuransiTokioMarine())) {
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["cetak"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
    } else if(in_array($kdproduk, KodeProduk::getNewAsuransi())){ 
        $format = FormatMsg::newAsuransi();
        $frm = new FormatNewAsuransi($man["cetak"] . "*" . $format, $resp);
        $params = retNewAsuransi($p_params, $frm);

    }else if(in_array($kdproduk, KodeProduk::getAdiraAxi())){ 
        $format = FormatMsg::adiraaxi();
        $frm = new FormatAdiraAxi($man["cetak"] . "*" . $format, $resp);
        $params = retAdiraaxi($p_params, $frm);

    }else if($kdproduk == "ASRBPJSKS"){ 
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["cetak"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);

    }else if($kdproduk == "ASRBPJSKSD"){ 
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["cetak"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
    }else if ($kdproduk=="IKLNBRS") {
        $format = FormatMsg::IklanBaris();
        $frm = new FormatIklanBaris($man["cetak"] . "*" . $format, $resp);
        $params = retIklanbaris($p_params, $frm);
    }else if ($kdproduk=="PKBNTB") {
        $format = FormatMsg::pkb();
        $frm = new FormatPKB($man["cetak"] . "*" . $format, $resp);
        $params = retPKB($p_params, $frm);
    }else if (in_array($kdproduk, KodeProduk::getSbf())) {
        $format = FormatMsg::sbf();
        $frm = new FormatSbf($man["cetak"] . "*" . $format, $resp);
        $params = retSbf($p_params, $frm);
    } else if($kdproduk == "PAJAKSOLO"){
        $format = FormatMsg::pajakSolo();
        $frm = new FormatPajakPbb($man["cetak"]."*".$format,$resp);
        $params = retPajakPbb($p_params,$frm);
    } else if($kdproduk == "PGN"){
        $format = FormatMsg::pgn();
        $frm = new FormatPgn($man["cetak"]."*".$format,$resp);
        $params = retPgn($p_params,$frm);
    } else if(substr($kdproduk, 0, 2) == "RZ"){
        $format = FormatMsg::zakat();
        $frm = new FormatZakat($man["cetak"]."*".$format,$resp);
        $params = retZakat($p_params,$frm);
    } else if(substr($kdproduk, 0, 2) == "KK") {
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["cetak"] . "*" . $format, $resp);
        $params = retKartuKredit($p_params, $frm);
    } else if($kdproduk == "GAS") {
        $format = FormatMsg::gas();
        $frm = new FormatGas($man["cetak"] . "*" . $format, $resp);
        $params = retGas($p_params, $frm);
    } else if(substr(strtoupper($kdproduk), 0,5) == 'BLTRF'){
        $format = FormatMsg::lakuPandai();
        $frm = new FormatLakuPandai($man["cetak"]."*".$format,$resp);
        $params = retLakuPandai($p_params, $frm);
    }

    return $params;
}

function templaterettext($delimeter, $params){
    $implode = implode($delimeter, $params);
    return $implode;
}

function templateret($kdproduk, $params, $mti="", $frm=""){
    if (in_array($kdproduk, KodeProduk::getTelkom())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "KODEAREA", "NOMORTELEPON", "KODEDIVRE", "KODEDATEL", "JUMLAHBILL", "NOMORREFERENSI3", "NILAITAGIHAN3", "NOMORREFERENSI2", "NILAITAGIHAN2", "NOMORREFERENSI1", "NILAITAGIHAN1", "NAMAPELANGGAN", "NPWP");
    } else if (in_array($kdproduk, KodeProduk::getPLNPostpaids())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "SUBSCRIBERID", "BILLSTATUS", "PAYMENTSTATUS", "TOTALOUTSTANDINGBILL", "SWREFERENCENUMBER", "SUBSCRIBERNAME", "SERVICEUNIT", "SERVICEUNITPHONE", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "TOTALADMINCHARGE", "BLTH1", "DUEDATE1", "METERREADDATE1", "RPTAG1", "INCENTIVE1", "VALUEADDEDTAX1", "PENALTYFEE1", "SLALWBP1", "SAHLWBP1", "SLAWBP1", "SAHWBP1", "SLAKVARH1", "SAHKVARH1", "BLTH2", "DUEDATE2", "METERREADDATE2", "RPTAG2", "INCENTIVE2", "VALUEADDEDTAX2", "PENALTYFEE2", "SLALWBP2", "SAHLWBP2", "SLAWBP2", "SAHWBP2", "SLAKVARH2", "SAHKVARH2", "BLTH3", "DUEDATE3", "METERREADDATE3", "RPTAG3", "INCENTIVE3", "VALUEADDEDTAX3", "PENALTYFEE3", "SLALWBP3", "SAHLWBP3", "SLAWBP3", "SAHWBP3", "SLAKVARH3", "SAHKVARH3", "BLTH4", "DUEDATE4", "METERREADDATE4", "RPTAG4", "INCENTIVE4", "VALUEADDEDTAX4", "PENALTYFEE4", "SLALWBP4", "SAHLWBP4", "SLAWBP4", "SAHWBP4", "SLAKVARH4", "SAHKVARH4", "ALAMAT", "PLNNPWP", "SUBSCRIBERNPWP", "TOTALRPTAG", "INFOTEKS");
    }  else if (in_array($kdproduk, KodeProduk::getPLNPrepaids()) || substr($kdproduk,0,6) === 'PLNPRA' ) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "NOMORMETER", "IDPELANGGAN", "FLAG", "NOREF1", "NOREF2", "VENDINGRECEIPTNUMBER", "NAMAPELANGGAN", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "MINORUNITOFADMINCHARGE", "ADMINCHARGE", "BUYINGOPTION", "DISTRIBUTIONCODE", "SERVICEUNIT", "SERVICEUNITPHONE", "MAXKWHLIMIT", "TOTALREPEATUNSOLDTOKEN", "UNSOLD1", "UNSOLD2", "TOKENPLN", "MINORUNITSTAMPDUTY", "STAMPDUTY", "MINORUNITPPN", "PPN", "MINORUNITPPJ", "PPJ", "MINORUNITCUSTOMERPAYABLESINSTALLMENT", "CUSTOMERPAYABLESINSTALLMENT", "MINORUNITOFPOWERPURCHASE", "POWERPURCHASE", "MINORUNITOFPURCHASEDKWHUNIT", "PURCHASEDKWHUNIT", "INFOTEXT");
    } else if (in_array($kdproduk, KodeProduk::getPLNNontaglists()) || substr($kdproduk, 0,6) == "PLNNON") {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "REGISTRATIONNUMBER", "AREACODE", "TRANSACTIONCODE", "TRANSACTIONNAME", "REGISTRATIONDATE", "EXPIRATIONDATE", "SUBSCRIBERID", "SUBSCRIBERNAME", "PLNREFNUMBER", "SWREFNUMBER", "SERVICEUNIT", "SERVICEUNITADDRESS", "SERVICEUNITPHONE", "TOTALTRANSACTIONAMOUNTMINOR", "TOTALTRANSACTIONAMOUNT", "PLNBILLMINORUNIT", "PLNBILLVALUE", "ADMINCHARGEMINORUNIT", "ADMINCHARGE", "MUTATIONNUMBER", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "INQUIRYREFERENCENUMBER", "TOTALREPEAT", "CUSTOMERDETAILCODE1", "CUSTOMDETAILMINORUNIT1", "CUSTOMDETAILVALUEAMOUNT1", "CUSTOMERDETAILCODE2", "CUSTOMDETAILMINORUNIT2", "CUSTOMDETAILVALUEAMOUNT2", "INFOTEXT");
    } else if (in_array($kdproduk, KodeProduk::getPonselPostpaid())) {
        $array = array( "KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PROVIDERNAME", "MONTHPERIOD1", "YEARPERIOD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3");
    } else if ( (in_array($kdproduk, KodeProduk::getPAM()) || substr($kdproduk,0,2) === 'WA') && $kdproduk !== "WASBY" ) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "MONTHPERIOD1", "YEARPERIOD1", "FIRSTMETERREAD1", "LASTMETERREAD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "FIRSTMETERREAD2", "LASTMETERREAD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "FIRSTMETERREAD3", "LASTMETERREAD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "FIRSTMETERREAD4", "LASTMETERREAD4", "PENALTY4", "BILLAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "FIRSTMETERREAD5", "LASTMETERREAD5", "PENALTY5", "BILLAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "FIRSTMETERREAD6", "LASTMETERREAD6", "PENALTY6", "BILLAMOUNT6", "MISCAMOUNT6");
    }  else if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "MINORUNIT", "BILLAMOUNT", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "BRANCHNAME", "ITEMMERKTYPE", "CHASISNUMBER", "CARNUMBER", "TENOR", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "OSINSTALLMENTAMOUNT", "ODINSTALLMENTPERIOD", "ODINSTALLMENTAMOUNT", "ODPENALTYFEE", "BILLERADMINFEE", "MISCFEE", "MINIMUMPAYAMOUNT", "MAXIMUMPAYAMOUNT");
    }  else if ($kdproduk == "BLLWK") {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "NAMA", "ALAMAT", "INVOICE", "ADMINCHARGE", "DATA");
    } else if (in_array($kdproduk, KodeProduk::getAsuransi())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "CLAIMAMOUNT", "BILLERREFNUMBER", "PTNAME", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "CUSTOMERPHONENUMBER", "CUSTOMERADDRESS", "AHLIWARISPHONENUMBER", "AHLIWARISADDRESS" , "CUSTOMERDETAILINFORMATION");
    }else if (in_array($kdproduk, KodeProduk::getAsuransiBPJS())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "CMD", "NIK", "CUSTOMER_NAME", "TANGGAL_LAHIR", "KODE_BAYAR", "FTRXID", "DATA1", "DATA2", "DATA3", "DATA4", "TAGIHAN", "ADMIN", "KPJ", "PEKERJAAN", "JAM_AWAL", "JAM_AKHIR", "ALAMAT", "ALAMAT_EMAIL", "LOKASI_KERJA", "UPAH", "KEC", "KEL", "KODEPOS", "HP", "OTP","JHT","RATE_JHT","JKK","RATE_JKK","JKM","RATE_JKM","ISNEW","STATUS_BAYAR","BIAYA_REGISTRASI","KODE_KANTOR_CABANG","ALAMAT_KANTOR_CABANG","KODE_KABUPATEN","KODE_PROVINSI","PROGRAM","PERIODE","STATUS_HITUNG_IURAN","BLN_JKM","BLN_JKK","BLN_JHT","KET1","KET2","KET3");
    } else if (in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "MINIMUMPAYAMOUNT", "MAXIMUMPAYAMOUNT");
    } else if (in_array($kdproduk, KodeProduk::getNewPAM()) && $kdproduk == "WASBY") {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "GWREFNUM", "SWREFNUM", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERSEGMENTATION", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "STAMPDUTY", "TRANSACTIONFEE", "OTHERFEE", "MONTHPERIOD1", "YEARPERIOD1", "METERUSAGE1", "STAND1", "FIRSTMETERREAD1", "LASTMETERREAD1", "BILLAMOUNT1", "PENALTY1", "BURDENAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "METERUSAGE2", "STAND2", "FIRSTMETERREAD2", "LASTMETERREAD2", "BILLAMOUNT2", "PENALTY2", "BURDENAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "METERUSAGE3", "STAND3", "FIRSTMETERREAD3", "LASTMETERREAD3", "BILLAMOUNT3", "PENALTY3", "BURDENAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "METERUSAGE4", "STAND4", "FIRSTMETERREAD4", "LASTMETERREAD4", "BILLAMOUNT4", "PENALTY4", "BURDENAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "METERUSAGE5", "STAND5", "FIRSTMETERREAD5", "LASTMETERREAD5", "BILLAMOUNT5", "PENALTY5", "BURDENAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "METERUSAGE6", "STAND6", "FIRSTMETERREAD6", "LASTMETERREAD6", "BILLAMOUNT6", "PENALTY6", "BURDENAMOUNT6", "MISCAMOUNT6");
    } else if (in_array($kdproduk, KodeProduk::getTVKabel())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "PERIODE", "DUEDATE", "CUSTOMINFO1", "CUSTOMINFO2", "CUSTOMINFO3");
    } else if(in_array($kdproduk, KodeProduk::getNewAsuransi())){ 
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "CLAIMAMOUNT", "BILLERREFNUMBER", "PTNAME", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "POLICYNUMBER", "CUSTOMERPHONENUMBER", "CUSTOMERADDRESS", "CUSTOMERGENDER", "CUSTOMERJOB", "BENEFICIARYNAME", "BENEFICIARYPHONENUMBER", "BENEFICIARYADDRESS", "BENEFICIARYRELATION", "REGISTRATION_DATE", "STARTDATE", "ENDDATE", "INFO_TEKS");
    } else if ($kdproduk =="IKLNBRS") {
        $array = array();
    } else if (substr($kdproduk, 0, 3) == 'PKB') {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "KND_NOPOL", "KND_ID", "KND_DF_JENIS", "KND_NAMA", "KND_DF_NOMOR", "KND_DF_TANGGAL", "KND_DF_JAM", "KND_DF_PROSES", "USR_FULL_NAME", "KND_KOHIR", "KND_SKUM", "KND_ALAMAT", "KEL_DESC", "KEC_DESC", "KAB_DESC", "TPE_DESC", "KD_MERK", "MRK_DESC", "JNS_DESC", "KND_THN_BUAT", "KND_CYL", "KND_WARNA", "KND_RANGKA", "KND_MESIN", "KND_NO_BPKB", "KND_SD_NOTICE", "KND_TGL_STNK", "KND_SD_STNK", "KD_BBM", "BBM_DESC", "WRN_DESC", "KND_NOPOL_EKS", "KND_JBB_PENUMPANG", "KND_BERAT_KB", "KND_JML_SUMBU_AS", "KD_KAB", "KD_JENIS", "KD_TIPE", "BOBOT", "NILAI_JUAL", "DASAR_PKB", "KD_GOL", "GOL_DESC", "TGLBERLAKU", "POKOK_NEW", "POKOK_OLD", "DENDA_NEW", "DENDA_OLD", "KND_MILIK_KE", "KD_KEC", "KD_KEL", "KD_GUNA", "KND_BLOKIR", "KND_TGL_FAKTUR", "KND_TGL_KUWITANSI", "KND_BLOKIR_TGL", "KND_BLOKIR_DESC", "DRV_DESC", "BILL_QUANTITY", "REFF_NUM", "ROW_ID", "PTP_TANGGAL", "NOM_PKB", "JASARAHARJA", "DENDA_NOM_PKB", "DENDA_JASARAHARJA", "NOM_PKB_TG", "JASARAHARJA_TG", "DENDA_NOM_PKB_TG", "DENDA_JASARAHARJA_TG");
    } else if (in_array($kdproduk, KodeProduk::getSbf())) {
        $array = array();
    } else if(substr($kdproduk, 0, 5) == 'PAJAK'){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "NTP", "NTB", "KODE_PEMDA", "NOP", "KODE_PAJAK", "TAHUN_PAJAK", "NAMA", "LOKASI", "KELURAHAN", "KECAMATAN", "PROVINSI", "LUAS_TANAH", "LUAS_BANGUNAN", "TANGGAL_JTH_TEMPO", "TAGIHAN", "DENDA", "TOTAL_BAYAR");
    } else if($kdproduk == "PGN"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "CUSTOMERID", "CUSTOMERNAME", "USAGE", "PERIODE", "INVOICENUMBER", "TAGIHAN", "ADMINBANK", "TOTAL", "CHARGE", "SALDO", "REFFID", "TRXID");
    } else if(substr($kdproduk, 0, 2) == "RZ"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "PROVIDER_NAME","NAMA_PRODUK","NO_KTP","NO_TELPON","NAMA","ALAMAT","ID_PROPINSI","ID_KOTA","KODE_POS","WAKTU_SURVEY_AWAL1","WAKTU_SURVEY_AKHIR1","WAKTU_SURVEY_AWAL2","WAKTU_SURVEY_AKHIR2","WAKTU_SURVEY_AWAL3","WAKTU_SURVEY_AKHIR3","CONTACT_PERSON","KECAMATAN","DESA");
    } else if($kdproduk == "GAS"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "GWREFNUM", "SWREFNUM", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "MONTHPERIOD1", "YEARPERIOD1", "FIRSTMETERREAD1", "LASTMETERREAD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "FIRSTMETERREAD2", "LASTMETERREAD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "FIRSTMETERREAD3", "LASTMETERREAD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "FIRSTMETERREAD4", "LASTMETERREAD4", "PENALTY4", "BILLAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "FIRSTMETERREAD5", "LASTMETERREAD5", "PENALTY5", "BILLAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "FIRSTMETERREAD6", "LASTMETERREAD6", "PENALTY6", "BILLAMOUNT6", "MISCAMOUNT6");
    }else if(substr(strtoupper($kdproduk), 0,5) == 'BLTRF'){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3","NAMA_BANK","KODE_BANK","NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG","CMD","DEALERID","SYSTEMID","ACCOUNTNUM","OPTION","IDAGEN","CURRENCY","ACCOUNT_STATUS","PRODUCT","HOMEBRANCH","CIFNUM","NAME","NAMEREK","CURRENTBALANCE","AVAILABLEBALANCE","OPENDATE","ADDRESS_STREET","ADDRESS_RT","ADDRESS1","ADDRESS2","ADDRESS3","ADDRESS4","POSTCODE","HOMEPHONE","FAX","OFFICEPHONE","MOBILEPHONE","ADDRESS1AA","ADDRESS2AA","ADDRESS3AA","ADDRESS4AA","POSTCODEAA","ACCOUNTPRODUCTTYPE","ACCTYPE","SUBCAT","AVAILABLEINTEREST","LIENBALANCE","UNCLEARBALANCE","INTERESTRATE","KTP","NPWP","JENIS_PEKERJAAN","EMAIL","KODE_WIL_BI","KODE_CABANG","KODE_LOKET","KODE_MITRA","TGL_INPUT","CA_GEN_STATUS","CLIENTID","CLIENT_ACCOUNT_NUM","REQ_ID","REQ_TIME","CUST_ACC_NUM","AMOUNT","TRANSACTION_JOURNAL","CUSTOMER_OTP","CUST_FIRST_NAME","CUST_MIDLE_NAME","CUST_LAST_NAME","CUST_PLACE_OF_BIRTH","CUST_DATE_OF_BIRTH","CUST_GENDER","CUST_IS_MARRIED","CUST_INCOME","PIN_TRANSAKSI");
    }
    $outlet = array("SP189334");
    if(in_array($params[7], $outlet)){ //MCC
        if($mti != ""){
            array_push($array, "TYPEMSG");
            array_push($params, (string) $mti);
        }
        $rtot = $params[5]+$params[6];
        array_push($array, "TOTALBAYAR");
        array_push($params, (string) $rtot);
        
        if($kdproduk == "PGN"){
            if($params[12] == "00"){
                array_push($array, "BILLQUANTITY");
                array_push($params, "1");
            } else {
                array_push($array, "BILLQUANTITY");
                array_push($params, "");
            }
        }

        if($frm->getSaldo()){
            array_push($array, "SALDO");
            array_push($params, (string) $frm->getSaldo());
        }

        if($params[12] == "00"){
            if($mti == "cu"){
                file_get_contents("http://10.0.0.14:88/FMSSWeb/dm?clearmemcachecu");
            }
            if($mti == "pay" || $mti == "cu"){
                // if($params[7] == "FA9919"){
                    $string = $params[10];
                    $url = enkripinew($string,'e');
                    $url_struk = "https://202.43.173.234/struk/?key=".$url;
                // } else {
                //     $url = enkripUrl(strtoupper($params[7]), $params[10]);
                //     // $url_struk = "https://rajabiller.fastpay.co.id/struk/?id=" . $url;
                //     $url_struk = "https://202.43.173.234/struk/?id=".$url;
                // }
                if($url != ""){
                    array_push($array, "URLSTRUK");
                    array_push($params, (string) $url_struk);
                }
            }
        }

        $gruptelkom = array("TELEPON","SPEEDY","TVTLKMV");
        if(in_array($kdproduk, $gruptelkom)){
            $periode1 = ""; $periode2 = ""; $periode3 = "";
            $tahunskg = substr(date('Y'),0,3);
            if(trim($params[25])<>""){
                $periode1 = $tahunskg.substr(trim($params[25]),0,3);
            }
            if(trim($params[23])<>""){
                $periode2 = $tahunskg.substr(trim($params[23]),0,3);
            }
            if(trim($params[21])<>""){
                $periode3 = $tahunskg.substr(trim($params[21]),0,3);
            }
            $bill = array($periode1, $periode2, $periode3);
            $bill_period = getPeriodeTelkom($bill);
            array_push($array, "PERIODETAGIHAN");
            array_push($params, (string) $bill_period);
        }
        $res = array_combine($array,$params);
        return $res;
    } else {
        $res = array_combine($array, $params);
        return $res;
    }
}

function templateretlast($kdproduk, $params, $mti="", $frm=""){
    if (in_array($kdproduk, KodeProduk::getTelkom())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "KODEAREA", "NOMORTELEPON", "KODEDIVRE", "KODEDATEL", "JUMLAHBILL", "NOMORREFERENSI3", "NILAITAGIHAN3", "NOMORREFERENSI2", "NILAITAGIHAN2", "NOMORREFERENSI1", "NILAITAGIHAN1", "NAMAPELANGGAN", "NPWP");
    } else if (in_array($kdproduk, KodeProduk::getPLNPostpaids())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "SUBSCRIBERID", "BILLSTATUS", "PAYMENTSTATUS", "TOTALOUTSTANDINGBILL", "SWREFERENCENUMBER", "SUBSCRIBERNAME", "SERVICEUNIT", "SERVICEUNITPHONE", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "TOTALADMINCHARGE", "BLTH1", "DUEDATE1", "METERREADDATE1", "RPTAG1", "INCENTIVE1", "VALUEADDEDTAX1", "PENALTYFEE1", "SLALWBP1", "SAHLWBP1", "SLAWBP1", "SAHWBP1", "SLAKVARH1", "SAHKVARH1", "BLTH2", "DUEDATE2", "METERREADDATE2", "RPTAG2", "INCENTIVE2", "VALUEADDEDTAX2", "PENALTYFEE2", "SLALWBP2", "SAHLWBP2", "SLAWBP2", "SAHWBP2", "SLAKVARH2", "SAHKVARH2", "BLTH3", "DUEDATE3", "METERREADDATE3", "RPTAG3", "INCENTIVE3", "VALUEADDEDTAX3", "PENALTYFEE3", "SLALWBP3", "SAHLWBP3", "SLAWBP3", "SAHWBP3", "SLAKVARH3", "SAHKVARH3", "BLTH4", "DUEDATE4", "METERREADDATE4", "RPTAG4", "INCENTIVE4", "VALUEADDEDTAX4", "PENALTYFEE4", "SLALWBP4", "SAHLWBP4", "SLAWBP4", "SAHWBP4", "SLAKVARH4", "SAHKVARH4", "ALAMAT", "PLNNPWP", "SUBSCRIBERNPWP", "TOTALRPTAG", "INFOTEKS");
    }  else if (in_array($kdproduk, KodeProduk::getPLNPrepaids()) || substr($kdproduk,0,6) === 'PLNPRA' ) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "NOMORMETER", "IDPELANGGAN", "FLAG", "NOREF1", "NOREF2", "VENDINGRECEIPTNUMBER", "NAMAPELANGGAN", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "MINORUNITOFADMINCHARGE", "ADMINCHARGE", "BUYINGOPTION", "DISTRIBUTIONCODE", "SERVICEUNIT", "SERVICEUNITPHONE", "MAXKWHLIMIT", "TOTALREPEATUNSOLDTOKEN", "UNSOLD1", "UNSOLD2", "TOKENPLN", "MINORUNITSTAMPDUTY", "STAMPDUTY", "MINORUNITPPN", "PPN", "MINORUNITPPJ", "PPJ", "MINORUNITCUSTOMERPAYABLESINSTALLMENT", "CUSTOMERPAYABLESINSTALLMENT", "MINORUNITOFPOWERPURCHASE", "POWERPURCHASE", "MINORUNITOFPURCHASEDKWHUNIT", "PURCHASEDKWHUNIT", "INFOTEXT");
    } else if (in_array($kdproduk, KodeProduk::getPLNNontaglists()) || substr($kdproduk, 0,6) == "PLNNON") {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "REGISTRATIONNUMBER", "AREACODE", "TRANSACTIONCODE", "TRANSACTIONNAME", "REGISTRATIONDATE", "EXPIRATIONDATE", "SUBSCRIBERID", "SUBSCRIBERNAME", "PLNREFNUMBER", "SWREFNUMBER", "SERVICEUNIT", "SERVICEUNITADDRESS", "SERVICEUNITPHONE", "TOTALTRANSACTIONAMOUNTMINOR", "TOTALTRANSACTIONAMOUNT", "PLNBILLMINORUNIT", "PLNBILLVALUE", "ADMINCHARGEMINORUNIT", "ADMINCHARGE", "MUTATIONNUMBER", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "INQUIRYREFERENCENUMBER", "TOTALREPEAT", "CUSTOMERDETAILCODE1", "CUSTOMDETAILMINORUNIT1", "CUSTOMDETAILVALUEAMOUNT1", "CUSTOMERDETAILCODE2", "CUSTOMDETAILMINORUNIT2", "CUSTOMDETAILVALUEAMOUNT2", "INFOTEXT");
    } else if (in_array($kdproduk, KodeProduk::getPonselPostpaid())) {
        $array = array( "KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PROVIDERNAME", "MONTHPERIOD1", "YEARPERIOD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3");
    } else if ( (in_array($kdproduk, KodeProduk::getPAM()) || substr($kdproduk,0,2) === 'WA') && $kdproduk !== "WASBY" ) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "MONTHPERIOD1", "YEARPERIOD1", "FIRSTMETERREAD1", "LASTMETERREAD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "FIRSTMETERREAD2", "LASTMETERREAD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "FIRSTMETERREAD3", "LASTMETERREAD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "FIRSTMETERREAD4", "LASTMETERREAD4", "PENALTY4", "BILLAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "FIRSTMETERREAD5", "LASTMETERREAD5", "PENALTY5", "BILLAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "FIRSTMETERREAD6", "LASTMETERREAD6", "PENALTY6", "BILLAMOUNT6", "MISCAMOUNT6");
    }  else if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "MINORUNIT", "BILLAMOUNT", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "BRANCHNAME", "ITEMMERKTYPE", "CHASISNUMBER", "CARNUMBER", "TENOR", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "OSINSTALLMENTAMOUNT", "ODINSTALLMENTPERIOD", "ODINSTALLMENTAMOUNT", "ODPENALTYFEE", "BILLERADMINFEE", "MISCFEE", "MINIMUMPAYAMOUNT", "MAXIMUMPAYAMOUNT");
    }  else if ($kdproduk == "BLLWK") {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "NAMA", "ALAMAT", "INVOICE", "ADMINCHARGE", "DATA");
    } else if (in_array($kdproduk, KodeProduk::getAsuransi())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "CLAIMAMOUNT", "BILLERREFNUMBER", "PTNAME", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "CUSTOMERPHONENUMBER", "CUSTOMERADDRESS", "AHLIWARISPHONENUMBER", "AHLIWARISADDRESS", "CUSTOMERDETAILINFORMATION");
    } else if (in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "MINIMUMPAYAMOUNT", "MAXIMUMPAYAMOUNT");
    } else if (in_array($kdproduk, KodeProduk::getNewPAM()) && $kdproduk == "WASBY") {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "GWREFNUM", "SWREFNUM", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERSEGMENTATION", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "STAMPDUTY", "TRANSACTIONFEE", "OTHERFEE", "MONTHPERIOD1", "YEARPERIOD1", "METERUSAGE1", "STAND1", "FIRSTMETERREAD1", "LASTMETERREAD1", "BILLAMOUNT1", "PENALTY1", "BURDENAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "METERUSAGE2", "STAND2", "FIRSTMETERREAD2", "LASTMETERREAD2", "BILLAMOUNT2", "PENALTY2", "BURDENAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "METERUSAGE3", "STAND3", "FIRSTMETERREAD3", "LASTMETERREAD3", "BILLAMOUNT3", "PENALTY3", "BURDENAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "METERUSAGE4", "STAND4", "FIRSTMETERREAD4", "LASTMETERREAD4", "BILLAMOUNT4", "PENALTY4", "BURDENAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "METERUSAGE5", "STAND5", "FIRSTMETERREAD5", "LASTMETERREAD5", "BILLAMOUNT5", "PENALTY5", "BURDENAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "METERUSAGE6", "STAND6", "FIRSTMETERREAD6", "LASTMETERREAD6", "BILLAMOUNT6", "PENALTY6", "BURDENAMOUNT6", "MISCAMOUNT6");
    } else if (in_array($kdproduk, KodeProduk::getTVKabel())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "PERIODE", "DUEDATE", "CUSTOMINFO1", "CUSTOMINFO2", "CUSTOMINFO3");
    } else if(in_array($kdproduk, KodeProduk::getNewAsuransi())){ 
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "CLAIMAMOUNT", "BILLERREFNUMBER", "PTNAME", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "POLICYNUMBER", "CUSTOMERPHONENUMBER", "CUSTOMERADDRESS", "CUSTOMERGENDER", "CUSTOMERJOB", "BENEFICIARYNAME", "BENEFICIARYPHONENUMBER", "BENEFICIARYADDRESS", "BENEFICIARYRELATION", "REGISTRATION_DATE", "STARTDATE", "ENDDATE", "INFO_TEKS");
    } else if ($kdproduk =="IKLNBRS") {
        $array = array();
    } else if (substr($kdproduk, 0, 3) == 'PKB') {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "KND_NOPOL", "KND_ID", "KND_DF_JENIS", "KND_NAMA", "KND_DF_NOMOR", "KND_DF_TANGGAL", "KND_DF_JAM", "KND_DF_PROSES", "USR_FULL_NAME", "KND_KOHIR", "KND_SKUM", "KND_ALAMAT", "KEL_DESC", "KEC_DESC", "KAB_DESC", "TPE_DESC", "KD_MERK", "MRK_DESC", "JNS_DESC", "KND_THN_BUAT", "KND_CYL", "KND_WARNA", "KND_RANGKA", "KND_MESIN", "KND_NO_BPKB", "KND_SD_NOTICE", "KND_TGL_STNK", "KND_SD_STNK", "KD_BBM", "BBM_DESC", "WRN_DESC", "KND_NOPOL_EKS", "KND_JBB_PENUMPANG", "KND_BERAT_KB", "KND_JML_SUMBU_AS", "KD_KAB", "KD_JENIS", "KD_TIPE", "BOBOT", "NILAI_JUAL", "DASAR_PKB", "KD_GOL", "GOL_DESC", "TGLBERLAKU", "POKOK_NEW", "POKOK_OLD", "DENDA_NEW", "DENDA_OLD", "KND_MILIK_KE", "KD_KEC", "KD_KEL", "KD_GUNA", "KND_BLOKIR", "KND_TGL_FAKTUR", "KND_TGL_KUWITANSI", "KND_BLOKIR_TGL", "KND_BLOKIR_DESC", "DRV_DESC", "BILL_QUANTITY", "REFF_NUM", "ROW_ID", "PTP_TANGGAL", "NOM_PKB", "JASARAHARJA", "DENDA_NOM_PKB", "DENDA_JASARAHARJA", "NOM_PKB_TG", "JASARAHARJA_TG", "DENDA_NOM_PKB_TG", "DENDA_JASARAHARJA_TG");
    } else if (in_array($kdproduk, KodeProduk::getSbf())) {
        $array = array();
    } else if(substr($kdproduk, 0, 5) == 'PAJAK'){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "NTP", "NTB", "KODE_PEMDA", "NOP", "KODE_PAJAK", "TAHUN_PAJAK", "NAMA", "LOKASI", "KELURAHAN", "KECAMATAN", "PROVINSI", "LUAS_TANAH", "LUAS_BANGUNAN", "TANGGAL_JTH_TEMPO", "TAGIHAN", "DENDA", "TOTAL_BAYAR");
    } else if($kdproduk == "PGN"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "CUSTOMERID", "CUSTOMERNAME", "USAGE", "PERIODE", "INVOICENUMBER", "TAGIHAN", "ADMINBANK", "TOTAL", "CHARGE", "SALDO", "REFFID", "TRXID");
    } else if(substr($kdproduk, 0, 2) == "RZ"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "PROVIDER_NAME","NAMA_PRODUK","NO_KTP","NO_TELPON","NAMA","ALAMAT","ID_PROPINSI","ID_KOTA","KODE_POS","WAKTU_SURVEY_AWAL1","WAKTU_SURVEY_AKHIR1","WAKTU_SURVEY_AWAL2","WAKTU_SURVEY_AKHIR2","WAKTU_SURVEY_AWAL3","WAKTU_SURVEY_AKHIR3","CONTACT_PERSON","KECAMATAN","DESA");
    } else if($kdproduk == "GAS"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO", "SWITCHERID", "BILLERCODE", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "GWREFNUM", "SWREFNUM", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "MONTHPERIOD1", "YEARPERIOD1", "FIRSTMETERREAD1", "LASTMETERREAD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "FIRSTMETERREAD2", "LASTMETERREAD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "FIRSTMETERREAD3", "LASTMETERREAD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "FIRSTMETERREAD4", "LASTMETERREAD4", "PENALTY4", "BILLAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "FIRSTMETERREAD5", "LASTMETERREAD5", "PENALTY5", "BILLAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "FIRSTMETERREAD6", "LASTMETERREAD6", "PENALTY6", "BILLAMOUNT6", "MISCAMOUNT6");
    }else if(substr(strtoupper($kdproduk), 0,5) == 'BLTRF'){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3","NAMA_BANK","KODE_BANK","NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SISASALDO","CMD","DEALERID","SYSTEMID","ACCOUNTNUM","OPTION","IDAGEN","CURRENCY","ACCOUNT_STATUS","PRODUCT","HOMEBRANCH","CIFNUM","NAME","NAMEREK","CURRENTBALANCE","AVAILABLEBALANCE","OPENDATE","ADDRESS_STREET","ADDRESS_RT","ADDRESS1","ADDRESS2","ADDRESS3","ADDRESS4","POSTCODE","HOMEPHONE","FAX","OFFICEPHONE","MOBILEPHONE","ADDRESS1AA","ADDRESS2AA","ADDRESS3AA","ADDRESS4AA","POSTCODEAA","ACCOUNTPRODUCTTYPE","ACCTYPE","SUBCAT","AVAILABLEINTEREST","LIENBALANCE","UNCLEARBALANCE","INTERESTRATE","KTP","NPWP","JENIS_PEKERJAAN","EMAIL","KODE_WIL_BI","KODE_CABANG","KODE_LOKET","KODE_MITRA","TGL_INPUT","CA_GEN_STATUS","CLIENTID","CLIENT_ACCOUNT_NUM","REQ_ID","REQ_TIME","CUST_ACC_NUM","AMOUNT","TRANSACTION_JOURNAL","CUSTOMER_OTP","CUST_FIRST_NAME","CUST_MIDLE_NAME","CUST_LAST_NAME","CUST_PLACE_OF_BIRTH","CUST_DATE_OF_BIRTH","CUST_GENDER","CUST_IS_MARRIED","CUST_INCOME","PIN_TRANSAKSI");
    }
    
    $res = array_combine($array, $params);
    return $res;
    
}

function getPeriodeTelkom($bill){
    $bill_period = "";
    $i = 0;
    foreach($bill as $v){
        if($i > 0 && $v<> ""){
            $bill_period .= ",";
        }
        $bill_period .= $v;
        $i++;
    }
    if((strpos($bill_period,',0') !== false)){
       $bill_period = str_replace(',0',"",$bill_period);
    }

    return $bill_period;
}

function postValueWithTimeOutCustom($msg, $ip, $port, $path, $timeout = 40) {
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    $url = "http://" . $ip . $path;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $port);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);   
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOut($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    // $url = "http://" . $GLOBALS["__CFG_urltargetip_lokal"] . $GLOBALS["__CFG_urltarget_lokal"];
    $url = "http://" . $GLOBALS["__CFG_urltargetip"] . $GLOBALS["__CFG_urltarget"];
    $ch = curl_init();
// echo $url;echo $GLOBALS["__CFG_urltargetport"];echo $msg;die();
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_lokal"]);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);   
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOutINDO($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    // $url = "http://" . $GLOBALS["__CFG_urltargetip_lokal"] . $GLOBALS["__CFG_urltarget_lokal"];
    $url = "http://" . $GLOBALS["__CFG_urltargetip_indo"] . $GLOBALS["__CFG_urltarget_indo"];
    $ch = curl_init();
// echo $url;echo $GLOBALS["__CFG_urltargetport"];echo $msg;die();
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_lokal"]);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_indo"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);   
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}


function postValue_fmssweb2($msg, $timeout=40){
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip_fmssweb2"] . $GLOBALS["__CFG_urltarget_fmssweb2"];
    // echo $url;echo $msg;die();
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_fmssweb2"]); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    // print_r($data);die();
    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);
    return $result;  
}

function postValueWithTimeOutKUDO($msg, $timeout = 20) {
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    // $url = "http://" . $GLOBALS["__CFG_urltargetip_lokal"] . $GLOBALS["__CFG_urltarget_lokal"];
    $url = "http://" . $GLOBALS["__CFG_urltargetip"] . $GLOBALS["__CFG_urltarget"];
    // echo $url.$GLOBALS["__CFG_urltargetport"]; 
    // die();
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_lokal"]);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);

    //execute post
    $data = curl_exec($ch);   
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOut_BPJS($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    $url = "http://" . $GLOBALS["__CFG_urltargetip_bpjs"] . $GLOBALS["__CFG_urltarget_bpjs"];
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_bpjs"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOut_TIKETUX($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    $url = "http://" . $GLOBALS["__CFG_urltargetip_tiketux"] . $GLOBALS["__CFG_urltarget_tiketux"];
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_tiketux"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}


function postValueWithTimeOutDevel($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    $url = "http://10.0.0.20/FMSSWeb/mpin1";

    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 22080);
    //curl_setopt($ch, CURLOPT_PORT, 8080);
    //curl_setopt($ch, CURLOPT_PORT, 9080);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOutDevelBillpayment($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    $url = "http://10.0.0.20/FMSSWeb2/mpin1";

    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 21080);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOutDevelKisbu($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    $url = "http://10.0.76.18/FMSSWeb/mpin1";

    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_PORT, 22080);
    curl_setopt($ch, CURLOPT_PORT, 8080);
    //curl_setopt($ch, CURLOPT_PORT, 9080);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function getResponStatus($id_outlet, $id_produk, $bill_info1, $bill_info83, $nominal) {
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "SELECT '00' AS rc, t.*, b.id_modul
    FROM fmss.proses_transaksi t
    JOIN fmss.edc_sukses_paksa sp ON t.mid = sp.mid
    LEFT JOIN fmss.mt_biller b ON t.id_biller = b.id_biller
    WHERE t.transaction_date IN (current_date, date(current_date - INTERVAL '1 DAY'))
    AND t.time_request > (NOW() - INTERVAL '1 HOUR')::timestamp without time zone
    AND t.id_outlet = '" . $id_outlet . "'
    AND t.jenis_transaksi = 1
    AND t.id_produk = '" . $id_produk . "'
    AND (t.bill_info1 = '" . $bill_info1 . "' OR t.bill_info2 = '" . $bill_info1 . "' OR t.bill_info3 = '" . $bill_info1 . "')
    AND t.bill_info83 = '" . $bill_info83 . "'
    AND t.nominal = " . $nominal . "
    UNION
    SELECT t.response_code AS rc, t.*, b.id_modul
    FROM fmss.transaksi t LEFT JOIN fmss.mt_biller b ON t.id_biller = b.id_biller
    WHERE t.transaction_date IN (current_date, date(current_date - INTERVAL '1 DAY')) 
    AND t.time_request::timestamp without time zone > (NOW() - INTERVAL '1 HOUR')::timestamp without time zone
    AND t.id_outlet = '" . $id_outlet . "'
    AND t.jenis_transaksi = 1
    AND t.id_produk = '" . $id_produk . "'
    AND (t.bill_info1 = '" . $bill_info1 . "' OR t.bill_info2 = '" . $bill_info1 . "' OR t.bill_info3 = '" . $bill_info1 . "')
    AND t.bill_info83 = '" . $bill_info83 . "'
    AND t.nominal = " . $nominal . "
    AND t.response_code = '00'";

    //echo $qn;

    $eqn = $db->query($qn);

    return $eqn[0];
}

function getPaymentResponMessage($mid, $id_modul) {
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);

    $qn = "SELECT * FROM fmss.message WHERE mid = " . $mid . " AND sender = '" . $id_modul . "' LIMIT 1";

    $eqn = $db->query($qn, "data_numrow");
    if ($eqn["numrow"] < 1) {
        $db2 = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
        $qn2 = "SELECT * FROM fmss.message_final WHERE mid = " . $mid . " AND sender = '" . $id_modul . "' LIMIT 1";
        $eqn2 = $db->query($qn2, "data_numrow");
        if ($eqn2["numrow"] < 1) {
            return "";
        } else {
            //echo "<br>message_final<br>";
            return $eqn2["data"][0]->content;
        }
    } else {
        //echo "<br>message<br>";
        return $eqn["data"][0]->content;
    }
}

function getTransaksi($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2) {
    $db = $GLOBALS["pgsql"];
    $tgl1 = substr($tgl1, 0, 8);
    $tgl2 = substr($tgl2, 0, 8);
    //$id_produk != "" ? (in_array($kdproduk, KodeProduk::getPLNPostpaids()) ? (strlen($idpel) == '11' : 'bill_info1') : '') : '';
    $kolom = 'bill_info1';
    if($idproduk != ""){
        if(in_array($idproduk, KodeProduk::getPLNPrepaids())){
            if(strlen($idpel) == '11'){
                $kolom = 'bill_info1';
            } else if(strlen($idpel) == '12'){
                $kolom = 'bill_info2';
            }
        }
    }

    $q = "
SELECT p.id_biller, t.id_transaksi, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.$kolom idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.response_code, t.keterangan, t.bill_info29
FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "'";
    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.transaction_date BETWEEN to_date('" . $tgl1 . "','YYYYMMDD') AND to_date('" . $tgl2 . "','YYYYMMDD')";
    $idproduk != "" ? $q .= " AND t.id_produk='" . $idproduk . "'" : $q = $q;
    $idpel != "" ? $q .= " AND t.$kolom = '" . $idpel . "'" : $q = $q;

    $q .= " UNION ";

    $q .= "
SELECT p.id_biller, t.id_transaksi, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.$kolom idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.response_code, 'SEDANG DIPROSES', t.bill_info29
FROM proses_transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "'";
    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.transaction_date BETWEEN to_date('" . $tgl1 . "','YYYYMMDD') AND to_date('" . $tgl2 . "','YYYYMMDD')";
    $idproduk != "" ? $q .= " AND t.id_produk='" . $idproduk . "'" : $q = $q;
    $idpel != "" ? $q .= " AND t.$kolom = '" . $idpel . "'" : $q = $q;

    $q .= " UNION ";

    $q .= "
SELECT p.id_biller, t.id_transaksi, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.$kolom idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.response_code, t.keterangan, t.bill_info29
FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "'";
    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.transaction_date BETWEEN to_date('" . $tgl1 . "','YYYYMMDD') AND to_date('" . $tgl2 . "','YYYYMMDD')";
    $idproduk != "" ? $q .= " AND t.id_produk='" . $idproduk . "'" : $q = $q;
    $idpel != "" ? $q .= " AND t.$kolom = '" . $idpel . "'" : $q = $q;
    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT " . $limit : $q = $q) : $q = $q;
    
    /*if($idtrx==""){
        if(is_timestamp($tgl1)){
           
            if(is_timestamp($tgl2)){
            
                $e = pg_query($db, $q);
            } else{
                return null;
            }
        }else{
            return null;
        }    
    } else{*/
    $e = pg_query($db, $q);
    //}

    $n = pg_num_rows($e);

    $out = array();
    $i = 0;
    if ($n > 0) {
        //die(1);
        while ($data = pg_fetch_object($e)) {
            $out[$i] = $data;
            $i++;
        }
    }
    //echo '<pre>', print_r($out), '</pre>';
    //die();
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getStatusProsesTransaksi($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
    // $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    $db = $GLOBALS["pgsql"];
    //$db = $pgsql;

    $arr_telkom = array('TELEPON','TELEPON2','SPEEDY','SPEEDY2');
    if(in_array(strtoupper($idproduk), $arr_telkom) && $idpel1 == ''){
        $getAlltelkom = '\''.implode('\',\'',$arr_telkom).'\'';
        $idproduk = " IN($getAlltelkom) ";
    } else {
        $idproduk = "='" . $idproduk . "'";
    }

//     $q = "
// SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal+nominal_up as nominal, nominal_admin, bill_info5, bill_info29
//     FROM proses_transaksi
//     WHERE jenis_transaksi = 1
// AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "' AND transaction_date = '" . $tgl . "'::date";
    $q = "
SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal as nominal, nominal_admin, bill_info5, bill_info29
    FROM proses_transaksi
    WHERE jenis_transaksi = 1
AND id_outlet='" . $idoutlet . "' AND id_produk $idproduk AND transaction_date = '" . $tgl . "'::date";

    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;
    if (in_array((string) $idproduk, KodeProduk::getPLNPrepaids())) {

        //if ($ref1 != "" || $idtrx != "") {


        if ($idpel1 != "" && $idpel2 == "" && $denom != "") {
            //$q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal = '" . $denom . "')";
        } else if ($idpel2 != "" && $idpel1 == "" && $denom != "") {

            //$q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal = '" . $denom . "')";
        } else {
            $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
            $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
            $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
        }
        // }
    } else {
        //  $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
        //  $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;
        // $idpel1 != "" ? $q .= " AND bill_info1 = '" . $idpel1 . "'" : $q = $q;
        // $idpel2 != "" ? $q .= " AND bill_info2 = '" . $idpel2 . "'" : $q = $q;
        $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
        $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
        //$denom != "" ? $q .= " AND nominal+nominal_admin = '" . $denom . "'" : $q = $q;
        $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
    }
    $q = $q . "  ORDER BY id_transaksi desc LIMIT 1";
// print_r($q);

    $out = array();
    $e = pg_query($db, $q);

    if($e){
        $n = pg_num_rows($e);
        //  $i = 0;
        if ($n > 0) {
            $r = pg_fetch_object($e);
            $out["is_connect"] = true;
            $out["id_transaksi"] = $r->id_transaksi;
            $out["time_request"] = $r->time_request;
            $out["id_produk"] = $r->id_produk;
            if($idoutlet == "HH146493" && substr($r->id_produk, 0, 2) == "KK"){
                $out["bill_info1"] = maskString($r->bill_info1,4);
            } else {
                $out["bill_info1"] = $r->bill_info1;
            }
            $out["bill_info2"] = $r->bill_info2;
            $out["nominal"] = $r->nominal;
            $out["nominal_admin"] = $r->nominal_admin;
            $out["bill_info5"] = $r->bill_info5;
            $out["bill_info29"] = $r->bill_info29;
            $out["nominal_up"] = $r->nominal_up;
            //$out[response_code] =$r->response_code;
            //$out[keterangan] =$r->keterangan;
        }
    } else {
        $out["is_connect"] = false;
    }

    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
    
}

function getStatusTransaksi($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
    $db = $GLOBALS["pgsql"];

    // $q = "
    // SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal+nominal_up as nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan 
    // FROM transaksi
    // WHERE jenis_transaksi = 1
    // AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "' AND transaction_date = '" . $tgl . "'::date";
    
    $arr_telkom = array('TELEPON','TELEPON2','SPEEDY','SPEEDY2');
    if(in_array(strtoupper($idproduk), $arr_telkom) && $idpel1 == ''){
        $getAlltelkom = '\''.implode('\',\'',$arr_telkom).'\'';
        $idproduk = " IN($getAlltelkom) ";
    } else {
        $idproduk = "='" . $idproduk . "'";
    }

    $q = "
    SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal as nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan 
    FROM transaksi
    WHERE jenis_transaksi = 1
    AND id_outlet='" . $idoutlet . "' AND id_produk $idproduk AND transaction_date = '" . $tgl . "'::date";

    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;

    if (in_array((string) $idproduk, KodeProduk::getPLNPrepaids())) {
        if ($idpel1 != "" && $idpel2 == "" && $denom != "") {
            //$q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal = '" . $denom . "')";
        } else if ($idpel2 != "" && $idpel1 == "" && $denom != "") {
            //$q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal = '" . $denom . "')";
        }else {
            $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
            $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
            $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
        }
    } else {
        // $idpel1 != "" ? $q .= " AND bill_info1 = '" . $idpel1 . "'" : $q = $q;
        // $idpel2 != "" ? $q .= " AND bill_info2 = '" . $idpel2 . "'" : $q = $q;
        $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
        $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
        //$denom != "" ? $q .= " AND nominal+nominal_admin = '" . $denom . "'" : $q = $q;
        $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
    }

    $q = $q . " ORDER BY id_transaksi DESC ";
 
    $e = pg_query($db, $q);
    $out = array();
    if($e){
        $n = pg_num_rows($e);

        if ($n > 0) {
            $r = pg_fetch_object($e);
            $out["id_transaksi"] = $r->id_transaksi;
            $out["time_request"] = $r->time_request;
            $out["id_produk"] = $r->id_produk;
            if($idoutlet == "HH146493" && substr($r->id_produk, 0, 2) == "KK"){
                $out["bill_info1"] = maskString($r->bill_info1,4);
            } else {
                $out["bill_info1"] = $r->bill_info1;
            }
            $out["bill_info2"] = $r->bill_info2;
            $out["nominal"] = $r->nominal;
            $out["nominal_admin"] = $r->nominal_admin;
            $out["bill_info5"] = $r->bill_info5;
            $out["bill_info29"] = $r->bill_info29;
            $out["response_code"] = $r->response_code;
            $out["keterangan"] = $r->keterangan;
            $out["nominal_up"] = $r->nominal_up;
            $out["is_connect"] = true;
        }
    } else {
        $out["is_connect"] = false;
    }
    
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function is_pulsa($id_produk){
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);
    $q = "select b.is_pulsa from mt_produk a left join mt_group_produk b on a.id_group_produk = b.id_group_produk where a.id_produk = '$id_produk'";
    $res = $db->query($q);
    $row = $res[0];
    $data = $row->is_pulsa;
    if($data === '1'){
        return true;
    } else {
        return false;
    }
}

function getStatusTransaksiBackup($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
    $db = $GLOBALS["pgsql"];

    $arr_telkom = array('TELEPON','TELEPON2','SPEEDY','SPEEDY2');
    if(in_array(strtoupper($idproduk), $arr_telkom) && $idpel1 == ''){
        $getAlltelkom = '\''.implode('\',\'',$arr_telkom).'\'';
        $idproduk = " IN($getAlltelkom) ";
    } else {
        $idproduk = "='" . $idproduk . "'";
    }

    // $q = "
    // SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal+nominal_up as nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan 
    // FROM transaksi_backup
    // WHERE jenis_transaksi = 1
    // AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "' AND transaction_date = '" . $tgl . "'::date";
    $q = "
    SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal as nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan 
    FROM transaksi_backup
    WHERE jenis_transaksi = 1
    AND id_outlet='" . $idoutlet . "' AND id_produk $idproduk  AND transaction_date = '" . $tgl . "'::date";

    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;

    if (in_array((string) $idproduk, KodeProduk::getPLNPrepaids())) {
        if ($idpel1 != "" && $idpel2 == "" && $denom != "") {
            //$q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal = '" . $denom . "')";
        } else if ($idpel2 != "" && $idpel1 == "" && $denom != "") {
            //$q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal = '" . $denom . "')";
        } else {
            $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
            $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
            $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
        }
    } else {
        // $idpel1 != "" ? $q .= " AND bill_info1 = '" . $idpel1 . "'" : $q = $q;
        // $idpel2 != "" ? $q .= " AND bill_info2 = '" . $idpel2 . "'" : $q = $q;
        $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
        $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
        //$denom != "" ? $q .= " AND nominal+nominal_admin = '" . $denom . "'" : $q = $q;
        $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
    }

    $q = $q . " ORDER BY id_transaksi DESC ";
    // echo $q;die();
    $e = pg_query($db, $q);
    $out = array();

    if($e){
        $n = pg_num_rows($e);
        if ($n > 0) {
            $r = pg_fetch_object($e);
            $out["id_transaksi"] = $r->id_transaksi;
            $out["is_connect"] = true;
            $out["time_request"] = $r->time_request;
            $out["id_produk"] = $r->id_produk;
            if($idoutlet == "HH146493" && substr($r->id_produk, 0, 2) == "KK"){
                $out["bill_info1"] = maskString($r->bill_info1,4);
            } else {
                $out["bill_info1"] = $r->bill_info1;
            }
            $out["bill_info2"] = $r->bill_info2;
            $out["nominal"] = $r->nominal;
            $out["nominal_admin"] = $r->nominal_admin;
            $out["bill_info5"] = $r->bill_info5;
            $out["bill_info29"] = $r->bill_info29;
            $out["response_code"] = $r->response_code;
            $out["keterangan"] = $r->keterangan;
            $out["nominal_up"] = $r->nominal_up;
        }
    } else {
        $out["is_connect"] = false;
    }

    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}


function getDataCustomDana($idoutlet, $kdproduk, $idpel1)
{

    $db = $GLOBALS["pgsql"];
    // $db = reconnect();
    $q = "select  id_transaksi,nominal, bill_info4, bill_info1, nominal_admin
            from fmss.transaksi where 
            id_outlet = '".$idoutlet."' and id_produk = '".$kdproduk."' 
            and ( bill_info1 = '".$idpel1."' or bill_info2 = '".$idpel1."' ) 
            and transaction_date = now()::date and response_code = '00' 
            and jenis_transaksi = 0 order by time_request desc limit 1";
            
    $out = array();
    $e = pg_query($db, $q);

    if($e){
        $n = pg_num_rows($e);

        //  $i = 0;
        if ($n > 0) {
            $r = pg_fetch_object($e);
            $out["is_connect"] = true;
            $out["nominal"] = $r->nominal;
            $out["id_transaksi"] = $r->id_transaksi;
            $out["bill_info4"] = $r->bill_info4;
            $out["bill_info1"] = $r->bill_info1;
            $out["nominal_admin"] = $r->nominal_admin;
        }
    } else {
        $out["is_connect"] = false;
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function updatedataDevel($idoutlet,$idpel1,$ref1,$nominal)
{
    $db = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    $tgl = date("Y-m-d H:i:s");
    $q = "update transaksi set nominal = '".$nominal."' , bill_info83 = '".$ref1."', transaction_date = now(), time_request = '".$tgl."' , time_response = '".$tgl."'  where id_outlet = '".$idoutlet."' and bill_info1='".$idpel1."'";

    $e = pg_query($db, $q);
    pg_close();
    reconnect($db);
}

function insertdataDevel($id_transaksi, $idoutlet, $idpel1,$ref1,$nominal, $kdproduk, $mid, $ket){
    $db = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    $tgl = date("Y-m-d");
    $dte = date("Y-m-d");
    $time = date("H:i:s");
    $q = "
insert into transaksi (id_transaksi,id_biller,id_produk,id_outlet,time_request,time_response,response_code,nominal,nominal_admin,bill_info1,bill_info5,bill_info6,keterangan,via,bill_info83,transaction_date,mid,bill_info89)
values (".$id_transaksi.",999,'".$kdproduk."','".$idoutlet."','".$tgl."' ,'".$tgl."' ,'00','".$nominal."','0','".$idpel1."','".$sn."','1','".$ket."','H2H','".$ref1."','".$dte."',".$mid.",'".$mid."')";

    $e = pg_query($db, $q);
    pg_close();
    reconnect($db);
}

function logDana($msg){
    $db = $GLOBALS["pgsql"];
    // $msg = replace_forbidden_chars($msg);
    // $msg = pg_escape_string($msg);
    // echo $msg;
    $q = "  INSERT INTO log_dana (data_request,request_time) 
                    VALUES ('".$msg."',NOW())";

    $e = pg_query($db, $q);
    pg_close();
    reconnect($db);
}

function getStatusProsesTransaksiDana($idoutlet, $ref1 = "", $idtrx = "") {

    $db = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    // $db = $GLOBALS["pgsql"];
    $q = "
SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal as nominal, nominal_admin, bill_info5, bill_info29, bill_info83,bill_info86
    FROM proses_transaksi
    WHERE jenis_transaksi = 1
AND id_outlet='" . $idoutlet . "' ";

    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;
  
    $q = $q . "  ORDER BY id_transaksi desc LIMIT 1";
    $out = array();
    $e = pg_query($db, $q);
    if($e){
        $n = pg_num_rows($e);
        //  $i = 0;
        if ($n > 0) {
            $r = pg_fetch_object($e);
            $out["is_connect"] = true;
            $out["id_transaksi"] = $r->id_transaksi;
            $out["time_request"] = $r->time_request;
            $out["id_produk"] = $r->id_produk;
            if($idoutlet == "HH146493" && substr($r->id_produk, 0, 2) == "KK"){
                $out["bill_info1"] = maskString($r->bill_info1,4);
            } else {
                $out["bill_info1"] = $r->bill_info1;
            }
            $out["bill_info2"] = $r->bill_info2;
            $out["nominal"] = $r->nominal;
            $out["nominal_admin"] = $r->nominal_admin;
            $out["bill_info5"] = $r->bill_info5;
            $out["bill_info29"] = $r->bill_info29;
            $out["bill_info83"] = $r->bill_info83;
            $out["idinquiry"] = $r->bill_info86;
            $out["nominal_up"] = $r->nominal_up;
            //$out[response_code] =$r->response_code;
            //$out[keterangan] =$r->keterangan;
        }
    } else {
        $out["is_connect"] = false;
    }

    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
    
}

function getStatusTransaksiDana($idoutlet, $ref1 = "", $idtrx = "") {
    // $db = $GLOBALS["pgsql"];
    // die('a');
     $db = pg_connect($GLOBALS["__G_conn_prop_devel"]);

    $q = "
    SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal as nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan , bill_info83, bill_info23,bill_info86
    FROM transaksi
    WHERE jenis_transaksi = 1
    AND id_outlet='" . $idoutlet . "' ";

    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;

    $q = $q . " ORDER BY id_transaksi DESC ";
    $e = pg_query($db, $q);

    $out = array();
    if($e){
        $n = pg_num_rows($e);
    // print_r($n);die();

        if ($n > 0) {
            $r = pg_fetch_object($e);
            $out["id_transaksi"] = $r->id_transaksi;
            $out["time_request"] = $r->time_request;
            $out["id_produk"] = $r->id_produk;
            if($idoutlet == "HH146493" && substr($r->id_produk, 0, 2) == "KK"){
                $out["bill_info1"] = maskString($r->bill_info1,4);
            } else {
                $out["bill_info1"] = $r->bill_info1;
            }
            $out["bill_info2"] = $r->bill_info2;
            $out["nominal"] = $r->nominal;
            $out["nominal_admin"] = $r->nominal_admin;
            $out["bill_info5"] = $r->bill_info5;
            $out["bill_info83"] = $r->bill_info83;
            $out["bill_info23"] = $r->bill_info23;
            $out["bill_info29"] = $r->bill_info29;
            $out["idinquiry"] = $r->bill_info86;
            $out["response_code"] = $r->response_code;
            $out["keterangan"] = $r->keterangan;
            $out["nominal_up"] = $r->nominal_up;
            $out["is_connect"] = true;
        }
    } else {
        $out["is_connect"] = false;
    }
    
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}


function getStatusTransaksiBackupDana($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
    // die('a');
    // $db = $GLOBALS["pgsql"];
         $db = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    $q = "
    SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal as nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan  , bill_info83,bill_info23,bill_info86
    FROM transaksi_backup
    WHERE jenis_transaksi = 1
    AND id_outlet='" . $idoutlet . "'";

    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;

    $q = $q . " ORDER BY id_transaksi DESC ";
    // echo $q;die();
    $e = pg_query($db, $q);
    $out = array();

    if($e){
        $n = pg_num_rows($e);
        if ($n > 0) {
            $r = pg_fetch_object($e);
            $out["id_transaksi"] = $r->id_transaksi;
            $out["is_connect"] = true;
            $out["time_request"] = $r->time_request;
            $out["id_produk"] = $r->id_produk;
            if($idoutlet == "HH146493" && substr($r->id_produk, 0, 2) == "KK"){
                $out["bill_info1"] = maskString($r->bill_info1,4);
            } else {
                $out["bill_info1"] = $r->bill_info1;
            }
            $out["bill_info2"] = $r->bill_info2;
            $out["nominal"] = $r->nominal;
            $out["nominal_admin"] = $r->nominal_admin;
            $out["bill_info5"] = $r->bill_info5;
            $out["bill_info83"] = $r->bill_info83;
            $out["bill_info23"] = $r->bill_info23;
            $out["bill_info29"] = $r->bill_info29;
            $out["idinquiry"] = $r->bill_info86;
            $out["response_code"] = $r->response_code;
            $out["keterangan"] = $r->keterangan;
            $out["nominal_up"] = $r->nominal_up;
        }
    } else {
        $out["is_connect"] = false;
    }
    
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getDataInquiry($ref2)
{

    $db = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    $q = "
    SELECT id_transaksi, nominal, nominal_admin, bill_info25, bill_info23,bill_info15
    FROM transaksi
    WHERE id_transaksi='" . $ref2 . "'";

    $q .= " UNION ";

    $q .= "
    SELECT id_transaksi, nominal, nominal_admin, bill_info25, bill_info23,bill_info15
    FROM transaksi_backup_non_payment
    WHERE id_transaksi='" . $ref2 . "'";

    $q = $q . " ORDER BY id_transaksi DESC ";
 
    // echo $q;die();
    $e = pg_query($db, $q);
    $out = array();

    if($e){
        $n = pg_num_rows($e);
        if ($n > 0) {
            $r = pg_fetch_object($e);
            $out["id_transaksi"] = $r->id_transaksi;
            $out["periode"] = $r->bill_info25;
            $out["nominal"] = $r->nominal;
            $out["nominal_admin"] = $r->nominal_admin;
            $out["namapt"] = $r->bill_info23;
            $out["namacustomer"] = $r->bill_info15;
        }
    } 
    
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getStatusProsesTransaksiDevel($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    //  $db = $GLOBALS["pgsql"];
    $db = $pgsql;


    $q = "
SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal, nominal_admin, bill_info5, bill_info29, nominal_up
    FROM proses_transaksi
    WHERE jenis_transaksi = 1
AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "' AND transaction_date = '" . $tgl . "'::date";
    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;
    if (in_array((string) $idproduk, KodeProduk::getPLNPrepaids())) {

        //if ($ref1 != "" || $idtrx != "") {


        if ($idpel1 != "" && $idpel2 == "" && $denom != "") {
            //$q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal = '" . $denom . "')";
        } else if ($idpel2 != "" && $idpel1 == "" && $denom != "") {

            //$q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal = '" . $denom . "')";
        }
        // }
    } else {
        //  $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
        //  $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;
        // $idpel1 != "" ? $q .= " AND bill_info1 = '" . $idpel1 . "'" : $q = $q;
        // $idpel2 != "" ? $q .= " AND bill_info2 = '" . $idpel2 . "'" : $q = $q;
        $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
        $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
        //$denom != "" ? $q .= " AND nominal+nominal_admin = '" . $denom . "'" : $q = $q;
        $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
    }

    $q = $q . "  ORDER BY id_transaksi desc LIMIT 1";

    $e = pg_query($db, $q);
    $n = pg_num_rows($e);

    $out = array();
    //$i = 0;
    if ($n > 0) {
        $r = pg_fetch_object($e);
        $out["id_transaksi"] = $r->id_transaksi;
        $out["time_request"] = $r->time_request;
        $out["id_produk"] = $r->id_produk;
        $out["bill_info1"] = $r->bill_info1;
        $out["bill_info2"] = $r->bill_info2;
        $out["nominal"] = $r->nominal;
        $out["nominal_admin"] = $r->nominal_admin;
        $out["bill_info5"] = $r->bill_info5;
        $out["bill_info29"] = $r->bill_info29;
        $out["nominal_up"] = $r->nominal_up;
        //$out["response_code"] =$r->response_code;
        //$out["keterangan"] =$r->keterangan;
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getStatusTransaksiDevel($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {

    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    //  $db = $GLOBALS["pgsql"];
    $db = $pgsql;



    $q = "
SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan, nominal_up 
    FROM transaksi
    WHERE jenis_transaksi = 1
AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "' AND transaction_date = '" . $tgl . "'::date";
    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;
    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    if (in_array((string) $idproduk, KodeProduk::getPLNPrepaids())) {
        if ($idpel1 != "" && $idpel2 == "" && $denom != "") {
            //$q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal = '" . $denom . "')";
        } else if ($idpel2 != "" && $idpel1 == "" && $denom != "") {
            //$q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal = '" . $denom . "')";
        }
    } else {
        // $idpel1 != "" ? $q .= " AND bill_info1 = '" . $idpel1 . "'" : $q = $q;
        // $idpel2 != "" ? $q .= " AND bill_info2 = '" . $idpel2 . "'" : $q = $q;
        $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
        $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
        //$denom != "" ? $q .= " AND nominal+nominal_admin = '" . $denom . "'" : $q = $q;
        $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
    }


    $q = $q . " ORDER BY id_transaksi DESC ";
    // echo $q;die();
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);

    $out = array();
    // $i = 0;

    if ($n > 0) {
//        
        $r = pg_fetch_object($e);
        $out["id_transaksi"] = $r->id_transaksi;
        $out["time_request"] = $r->time_request;
        $out["id_produk"] = $r->id_produk;
        $out["bill_info1"] = $r->bill_info1;
        $out["bill_info2"] = $r->bill_info2;
        $out["nominal"] = $r->nominal;
        $out["nominal_admin"] = $r->nominal_admin;
        $out["bill_info5"] = $r->bill_info5;
        $out["bill_info29"] = $r->bill_info29;
        $out["response_code"] = $r->response_code;
        $out["keterangan"] = $r->keterangan;
        $out["nominal_up"] = $r->nominal_up;
        //$out[6] =$row[7];
        //  }
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getStatusTransaksiBackupDevel($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    //  $db = $GLOBALS["pgsql"];
    $db = $pgsql;
   

    $q = "
    SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan 
    FROM transaksi_backup
    WHERE jenis_transaksi = 1
    AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "' AND transaction_date = '" . $tgl . "'::date";

    $ref1 != "" ? $q .= " AND bill_info83='" . $ref1 . "'" : $q = $q;
    $idtrx != "" ? $q .= " AND id_transaksi='" . $idtrx . "'" : $q = $q;

    if (in_array((string) $idproduk, KodeProduk::getPLNPrepaids())) {
        if ($idpel1 != "" && $idpel2 == "" && $denom != "") {
            //$q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info1 = '" . $idpel1 . "' AND nominal = '" . $denom . "')";
        } else if ($idpel2 != "" && $idpel1 == "" && $denom != "") {
            //$q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal+nominal_admin = '" . $denom . "')";
            $q .= " and (bill_info2 = '" . $idpel2 . "' AND nominal = '" . $denom . "')";
        }
    } else {
        // $idpel1 != "" ? $q .= " AND bill_info1 = '" . $idpel1 . "'" : $q = $q;
        // $idpel2 != "" ? $q .= " AND bill_info2 = '" . $idpel2 . "'" : $q = $q;
        $idpel1 != "" ? $q .= " AND (bill_info1 = '" . $idpel1 . "' or bill_info2 = '" . $idpel1 . "') " : $q = $q;
        $idpel2 != "" ? $q .= " AND (bill_info2 = '" . $idpel2 . "' or bill_info1 = '" . $idpel2 . "') " : $q = $q;
        //$denom != "" ? $q .= " AND nominal+nominal_admin = '" . $denom . "'" : $q = $q;
        $denom != "" ? $q .= " AND nominal = '" . $denom . "'" : $q = $q;
    }

    $q = $q . " ORDER BY id_transaksi DESC ";

    $e = pg_query($db, $q);
    $n = pg_num_rows($e);

    $out = array();

    if ($n > 0) {
        $r = pg_fetch_object($e);
        $out["id_transaksi"] = $r->id_transaksi;
        $out["time_request"] = $r->time_request;
        $out["id_produk"] = $r->id_produk;
        $out["bill_info1"] = $r->bill_info1;
        $out["bill_info2"] = $r->bill_info2;
        $out["nominal"] = $r->nominal;
        $out["nominal_admin"] = $r->nominal_admin;
        $out["bill_info5"] = $r->bill_info5;
        $out["bill_info29"] = $r->bill_info29;
        $out["response_code"] = $r->response_code;
        $out["keterangan"] = $r->keterangan;
        $out["nominal_up"] = $r->nominal_up;
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getStatusProsesTransaksiDevelMCC($idproduk, $idoutlet, $ref1 = "", $idpel1 = "") {
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    //  $db = $GLOBALS["pgsql"];
    $db = $pgsql;

    $q = "
    SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan 
    FROM proses_transaksi
    WHERE jenis_transaksi = 1
    AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "'";

    $q .= " AND bill_info83='" . $ref1 . "'";
    $q .= " AND (bill_info1 = '" . $idpel1 . "') ";

    $q = $q . "  ORDER BY id_transaksi desc LIMIT 1";

    $e = pg_query($db, $q);
    $n = pg_num_rows($e);

    $out = array();
    //$i = 0;
    if ($n > 0) {
        $r = pg_fetch_object($e);
        $out["id_transaksi"] = $r->id_transaksi;
        $out["time_request"] = $r->time_request;
        $out["id_produk"] = $r->id_produk;
        $out["bill_info1"] = $r->bill_info1;
        $out["bill_info2"] = $r->bill_info2;
        $out["nominal"] = $r->nominal;
        $out["nominal_admin"] = $r->nominal_admin;
        $out["bill_info5"] = $r->bill_info5;
        $out["bill_info29"] = $r->bill_info29;
        $out["nominal_up"] = $r->nominal_up;
        //$out["response_code"] =$r->response_code;
        //$out["keterangan"] =$r->keterangan;
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getStatusTransaksiDevelMCC($idproduk, $idoutlet, $ref1 = "", $idpel1 = "") {

    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    //  $db = $GLOBALS["pgsql"];
    $db = $pgsql;



    $q = "
    SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan 
    FROM transaksi
    WHERE jenis_transaksi = 1
    AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "'";

    $q .= " AND bill_info83='" . $ref1 . "'";
    $q .= " AND (bill_info1 = '" . $idpel1 . "') ";


    $q = $q . " ORDER BY id_transaksi DESC ";
    // echo $q;die();
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);

    $out = array();
    // $i = 0;

    if ($n > 0) {
//        
        $r = pg_fetch_object($e);
        $out["id_transaksi"] = $r->id_transaksi;
        $out["time_request"] = $r->time_request;
        $out["id_produk"] = $r->id_produk;
        $out["bill_info1"] = $r->bill_info1;
        $out["bill_info2"] = $r->bill_info2;
        $out["nominal"] = $r->nominal;
        $out["nominal_admin"] = $r->nominal_admin;
        $out["bill_info5"] = $r->bill_info5;
        $out["bill_info29"] = $r->bill_info29;
        $out["response_code"] = $r->response_code;
        $out["keterangan"] = $r->keterangan;
        $out["nominal_up"] = $r->nominal_up;
        //$out[6] =$row[7];
        //  }
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function getStatusTransaksiBackupDevelMCC($idproduk, $idoutlet, $ref1 = "", $idpel1 = "") {
    $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
    //  $db = $GLOBALS["pgsql"];
    $db = $pgsql;
   

    $q = "
    SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal, nominal_admin, bill_info5, bill_info29,response_code,keterangan 
    FROM transaksi_backup
    WHERE jenis_transaksi = 1
    AND id_outlet='" . $idoutlet . "' AND id_produk='" . $idproduk . "'";

    $q .= " AND bill_info83='" . $ref1 . "'";
    $q .= " AND (bill_info1 = '" . $idpel1 . "') ";
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);

    $out = array();

    if ($n > 0) {
        $r = pg_fetch_object($e);
        $out["id_transaksi"] = $r->id_transaksi;
        $out["time_request"] = $r->time_request;
        $out["id_produk"] = $r->id_produk;
        $out["bill_info1"] = $r->bill_info1;
        $out["bill_info2"] = $r->bill_info2;
        $out["nominal"] = $r->nominal;
        $out["nominal_admin"] = $r->nominal_admin;
        $out["bill_info5"] = $r->bill_info5;
        $out["bill_info29"] = $r->bill_info29;
        $out["response_code"] = $r->response_code;
        $out["keterangan"] = $r->keterangan;
        $out["nominal_up"] = $r->nominal_up;
    }
    pg_free_result($e);
    pg_close();
    reconnect($db);
    return $out;
}

function trimed($txt){
  $txt = trim($txt);
    while( strpos($txt, '  ') ){
      $txt = str_replace('  ', ' ', $txt);
    }
  return $txt;
}
function getTransaksi_devel($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2) {
     $pgsql = pg_connect($GLOBALS["__G_conn_prop_devel"]);
     $db = $pgsql;
    
    $q = "
SELECT t.id_transaksi, t.id_biller,to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.response_code, t.keterangan
FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "'";
    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.time_request BETWEEN to_timestamp('" . $tgl1 . "','YYYYMMDDHH24MISS') AND to_timestamp('" . $tgl2 . "','YYYYMMDDHH24MISS')";
    $idproduk != "" ? $q .= " AND t.id_produk='" . $idproduk . "'" : $q = $q;
    $idpel != "" ? $q .= " AND t.bill_info1 LIKE '" . $idpel . "'" : $q = $q;

    $q .= " UNION ";

    $q .= "
SELECT t.id_transaksi, t.id_biller,to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.response_code, 'SEDANG DIPROSES'
FROM proses_transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "'";
    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.time_request BETWEEN to_timestamp('" . $tgl1 . "','YYYYMMDDHH24MISS') AND to_timestamp('" . $tgl2 . "','YYYYMMDDHH24MISS')";
    $idproduk != "" ? $q .= " AND t.id_produk='" . $idproduk . "'" : $q = $q;
    $idpel != "" ? $q .= " AND t.bill_info1 LIKE '" . $idpel . "'" : $q = $q;

    $q .= " UNION ";

    $q .= "
SELECT t.id_transaksi,t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.response_code, t.keterangan
FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='" . $idoutlet . "'";
    $idtrx != "" ? $q .= " AND t.id_transaksi='" . $idtrx . "'" : $q .= " AND t.time_request BETWEEN to_timestamp('" . $tgl1 . "','YYYYMMDDHH24MISS') AND to_timestamp('" . $tgl2 . "','YYYYMMDDHH24MISS')";
    $idproduk != "" ? $q .= " AND t.id_produk='" . $idproduk . "'" : $q = $q;
    $idpel != "" ? $q .= " AND t.bill_info1 LIKE '" . $idpel . "'" : $q = $q;
    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT " . $limit : $q = $q) : $q = $q;
   // }
    
    if($idtrx==""){
        if(is_timestamp($tgl1)){
           
            if(is_timestamp($tgl2)){
            
            $e = pg_query($db, $q);
            }else{
                return null;
            }
        }else{
            return null;
        }    
    }else{        
    $e = pg_query($db, $q);
    }
  
   
    $n = pg_num_rows($e);

    $out = array();
    $i = 0;
    if ($n > 0) {
        while ($data = pg_fetch_object($e)) {
            $out[$i] = $data;
            $i++;
        }
    }
    pg_free_result($e);
    pg_close();
    reconnect_devel($db);
    return $out;
    
}

function normalisasiIdPel1PLNPra($idpel1, $idpel2) {
    $ret = array("idpel1" => $idpel1, "idpel2" => $idpel2);
    $is_valid_idpel1 = false;
    if (strlen($idpel1) > 11) {
        $idpel1_length = strlen($idpel1) - 11;
        $temp_char_zero = substr($idpel1, 0, $idpel1_length);
        if (intval($temp_char_zero) == 0) {
            $is_valid_idpel1 = true;
        }

        if ($is_valid_idpel1) {
            $idpel1 = substr($idpel1, $idpel1_length); //ubah nilai $_bill_info1 menjadi 11 karakter terakhir dari $_bill_info1 sebelumnya. Contoh $_bill_info1 = "0012345678901", maka nilai $_bill_info1 menjadi "12345678901"
            $idpel2 = "";
        } else {
            $idpel2 = $idpel1;
            $idpel1 = "";
        }
    }

    $ret = array("idpel1" => $idpel1, "idpel2" => $idpel2);

    return $ret;
}

function normalisasiIdPel1PLNPasc($idpel1) {
    $ret = array("idpel1" => $idpel1);
    $is_valid_idpel1 = false;
    if (strlen($idpel1) > 12) {
        $idpel1_length = strlen($idpel1) - 12;
        $idpel1 = substr($idpel1, $idpel1_length);
    } 

    $ret = array("idpel1" => $idpel1);

    return $ret;
}

function writeLogText($somecontent) {
    $filename = "/home/papua2/log_h2h_xml_rpc.txt";
    //$filename = "/home/gennaro/log_h2h_xml_rpc.txt";
    //$somecontent = "\n[".date("Y-m-d H:i:s")."]RECEIVE->|".$msg."|";
    // Let's make sure the file exists and is writable first.
    if (is_writable($filename)) {

        // In our example we're opening $filename in append mode.
        // The file pointer is at the bottom of the file hence
        // that's where $somecontent will go when we fwrite() it.
        if (!$handle = fopen($filename, 'a')) {
            echo "Cannot open file ($filename)";
            exit;
        }

        // Write $somecontent to our opened file.
        if (fwrite($handle, $somecontent) === FALSE) {
            echo "Cannot write to file ($filename)";
            exit;
        }

        //echo "Success, wrote ($somecontent) to file ($filename)";

        fclose($handle);
    } else {
        //echo "The file $filename is not writable";
    }
}

function is_timestamp($timestamp)
{
    if (strlen($timestamp) != 14)
         return false;
    
    $check = (is_int($timestamp) OR is_float($timestamp))
        ? $timestamp
        : (string) (int) $timestamp;
    return  ($check === $timestamp)
            AND ( (int) $timestamp <=  PHP_INT_MAX)
            AND ( (int) $timestamp >= ~PHP_INT_MAX);
}

function getResponStatus_devel($id_outlet, $id_produk, $bill_info1, $bill_info83, $nominal) {
    global $__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel;
    $db = new PgDBI2($__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel);

    $qn = "SELECT '00' AS rc, t.*, b.id_modul
    FROM fmss.proses_transaksi t
    JOIN fmss.edc_sukses_paksa sp ON t.mid = sp.mid
    LEFT JOIN fmss.mt_biller b ON t.id_biller = b.id_biller
    WHERE t.transaction_date IN (current_date, date(current_date - INTERVAL '1 DAY'))
    AND t.time_request > (NOW() - INTERVAL '1 HOUR')::timestamp without time zone
    AND t.id_outlet = '" . $id_outlet . "'
    AND t.jenis_transaksi = 1
    AND t.id_produk = '" . $id_produk . "'
    AND (t.bill_info1 = '" . $bill_info1 . "' OR t.bill_info2 = '" . $bill_info1 . "' OR t.bill_info3 = '" . $bill_info1 . "')
    AND t.bill_info83 = '" . $bill_info83 . "'
    AND t.nominal = " . $nominal . "
    UNION
    SELECT t.response_code AS rc, t.*, b.id_modul
    FROM fmss.transaksi t LEFT JOIN fmss.mt_biller b ON t.id_biller = b.id_biller
    WHERE t.transaction_date IN (current_date, date(current_date - INTERVAL '1 DAY')) 
    AND t.time_request::timestamp without time zone > (NOW() - INTERVAL '1 HOUR')::timestamp without time zone
    AND t.id_outlet = '" . $id_outlet . "'
    AND t.jenis_transaksi = 1
    AND t.id_produk = '" . $id_produk . "'
    AND (t.bill_info1 = '" . $bill_info1 . "' OR t.bill_info2 = '" . $bill_info1 . "' OR t.bill_info3 = '" . $bill_info1 . "')
    AND t.bill_info83 = '" . $bill_info83 . "'
    AND t.nominal = " . $nominal . "
    AND t.response_code = '00'";

    //echo $qn;

    $eqn = $db->query($qn);

    return $eqn[0];
}

function getPaymentResponMessage_devel($mid, $id_modul) {
    global $__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel;
    $db = new PgDBI2($__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel);

    $qn = "SELECT * FROM fmss.message WHERE mid = " . $mid . " AND sender = '" . $id_modul . "' LIMIT 1";

    $eqn = $db->query($qn, "data_numrow");
    if ($eqn["numrow"] < 1) {
        $db2 = new PgDBI2($__CFG_dbname_devel, $__CFG_dbhost_devel, $__CFG_dbuser_devel, $__CFG_dbpass_devel, $__CFG_dbport_devel);
        $qn2 = "SELECT * FROM fmss.message_final WHERE mid = " . $mid . " AND sender = '" . $id_modul . "' LIMIT 1";
        $eqn2 = $db->query($qn2, "data_numrow");
        if ($eqn2["numrow"] < 1) {
            return "";
        } else {
            //echo "<br>message_final<br>";
            return $eqn2["data"][0]->content;
        }
    } else {
        //echo "<br>message<br>";
        return $eqn["data"][0]->content;
    }
}

function postValueWithTimeOutDompetku($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    //$url = "http://" . $GLOBALS["__CFG_urltargetip"] . $GLOBALS["__CFG_urltarget"];
    $url = "http://10.0.0.14" . $GLOBALS["__CFG_urltarget"];
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, 25080);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOut_TIKETUX_DEVEL($msg, $timeout = 40) {

    
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    $url = "http://10.0.0.6". $GLOBALS["__CFG_urltarget_tiketux"];
    
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, "25080");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function postValueWithTimeOut_LOMBOK_DEVEL($msg, $timeout = 600) {
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    // $url = "http://10.0.0.20". $GLOBALS["__CFG_urltarget_tiketux"];
    $url = "http://10.0.76.18/FMSSWeb/mpin2";
    $ch = curl_init();
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_PORT, "21080");
    curl_setopt($ch, CURLOPT_PORT, "8080");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    if ($errno > 0){
        $result[7] = 'null';
    } else {
        $result[7] = $data;
    }
    curl_close($ch);
    return $result;
}


function postValueWithTimeOut_JAWAPOS_DEVEL($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    $url = "http://10.0.0.20". $GLOBALS["__CFG_urltarget_tiketux"];
    
    //die($url);
    
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, "22080");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);
    
    //var_dump($result);
    

    return $result;
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


function batasanuat($kdproduk, $idoutlet){
    // return 30; //ini solusi buat kudo yang kelamaan select

    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);    
    // $qn = "
    //     SELECT SUM(jumlah) as jumlah from (
    //         SELECT count(id_transaksi) as jumlah from fmss.transaksi WHERE id_outlet = '" . $idoutlet . "' AND jenis_transaksi = 1 AND response_code = '00' AND id_produk = '". $kdproduk ."'
    //         UNION
    //         SELECT count(id_transaksi) as jumlah from fmss.transaksi_backup WHERE id_outlet = '" . $idoutlet . "' AND jenis_transaksi = 1 AND response_code = '00' AND id_produk = '". $kdproduk ."'
    //     ) as jumlah
    // ";    

    $qn = "SELECT count(id_transaksi) as jumlah from fmss.transaksi WHERE id_outlet = '" . $idoutlet . "' AND jenis_transaksi = 1 AND response_code = '00' AND id_produk = '". $kdproduk ."'
    ";  
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->jumlah;
    return $ret;
}

function isValidIP($id_outlet, $sender){
    $is_valid = false;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport); 
    $id_outlet = trim($id_outlet);
    $q = "SELECT ip FROM fmss.mt_outlet WHERE id_outlet = '".$id_outlet."'";
    $e = $db->query($q);
    $ip = trim($e[0]->ip);
    if($ip == ""){
        $is_valid = false;
    } else {
        $ip = str_replace(" ","",$ip);
        $list_ip = explode(",",$ip);
        $list_ip = array_map('trim', $list_ip);
        if(count($list_ip) > 0){
            if(in_array($sender,$list_ip)){
                $is_valid = true;
            } else {
                $is_valid = false;
            }
        } else {
            $is_valid = false;
        }
    }
    return $is_valid;
}

function tes($id){
    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);    
    $qn = "select balance as jumlah from mt_outlet where id_outlet = '$id'";    
    // die($qn);
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->jumlah;
    return $ret;
}

function onedayonepay($kdproduk, $idoutlet, $idpel){
    $ret;
    global $__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport;
    $db = new PgDBI2($__CFG_dbname, $__CFG_dbhost, $__CFG_dbuser, $__CFG_dbpass, $__CFG_dbport);    
    $qn = "
        SELECT SUM(jumlah) as jumlah from (
            SELECT count(id_transaksi) as jumlah from fmss.transaksi WHERE id_outlet = '" . $idoutlet . "' AND jenis_transaksi = 1 AND response_code = '00' 
            AND id_produk = '" . $kdproduk . "' and transaction_date = now()::date and (bill_info1 = '".$idpel."' or bill_info2 = '".$idpel."')
        ) as jumlah
    ";   
    $eqn = $db->query($qn);
    $row = $eqn[0];
    $ret = $row->jumlah;
    return $ret;
}


function postValueWithTimeOut_UAT($msg, $timeout = 40) {
//die("UATBPJS");
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    $x = "/FMSSWeb2/mpin2";
    $url = "http://10.0.0.20". $x;
    $ch = curl_init();
    //die("gennaro->".$url.":21080");
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, "21080");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    //execute post
    $data = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);

    if ($errno > 0)
        $result[7] = 'null';
    else
        $result[7] = $data;
    //close connection
    curl_close($ch);

    return $result;
}

function getKomisiLoket($idoutlet, $idtrx) {
    $db = $GLOBALS["pgsql"];
    $q = "SELECT * FROM fmss.komisi WHERE id_outlet='" . $idoutlet . "' AND id_transaksi = ".$idtrx;    
    $e = pg_query($db, $q);

    $n = pg_num_rows($e);
    if ($n > 0) {        
        while ($data = pg_fetch_object($e)) {
            $komisi = $data->jumlah;
        }
        pg_free_result($e);
        pg_close();
        reconnect($db);
        return $komisi;
    } else {
        return false;
    }    
}

function isKecamatanJawaposExist($nama_kecamatan) {
    $db = $GLOBALS["pgsql"];
    $q = "SELECT kode_kecamatan FROM fmss.jawapos_kecamatan WHERE nama_kecamatan = '". strtoupper(trim($nama_kecamatan)) ."'";
    $e = pg_query($db, $q);

    $ret = false;

    $n = pg_num_rows($e);
    if ($n > 0) {
        pg_free_result($e);
        pg_close();
        reconnect($db);
        $ret = true;
    }

    return $ret;
}

function isKelurahanJawaposExist($nama_kelurahan) {
    $db = $GLOBALS["pgsql"];
    $q = "SELECT kode_kelurahan FROM fmss.jawapos_kelurahan WHERE nama_kelurahan = '". strtoupper(trim($nama_kelurahan)) ."'";
    $e = pg_query($db, $q);

    $ret = false;

    $n = pg_num_rows($e);
    if ($n > 0) {
        pg_free_result($e);
        pg_close();
        reconnect($db);
        $ret = true;
    }

    return $ret;
}

function getPinTransaksiFromMtBillerBni(){
    $db = $GLOBALS["pgsql"];
    $q = "SELECT partner_central_id 
              FROM fmss.mt_biller 
              WHERE id_modul = 'MB_BNI' LIMIT 1";
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
    
    $settingPartnerCentralId = '';
    
    if ($n > 0) {
        while ($data = pg_fetch_object($e)) {
            $settingPartnerCentralId = $data->partner_central_id;
        }
        pg_free_result($e);
        pg_close();
        reconnect($db);
    }
    $arrPartnerCentralId=explode(":", $settingPartnerCentralId);
    return $arrPartnerCentralId[3];
}

function getKodeBniFromMtBiller(){
    $db = $GLOBALS["pgsql"];
    $q = "SELECT partner_central_id 
              FROM fmss.mt_biller 
              WHERE id_modul = 'MB_BNI' LIMIT 1";
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
    
    $settingPartnerCentralId = '';
    
    if ($n > 0) {
        while ($data = pg_fetch_object($e)) {
            $settingPartnerCentralId = $data->partner_central_id;
        }
        pg_free_result($e);
        pg_close();
        reconnect($db);
    }
    $arrPartnerCentralId=explode(":", $settingPartnerCentralId);
    return $arrPartnerCentralId;
}

function getKodeBniFromBniKodeOutlet($id_outlet){
    $db = $GLOBALS["pgsql"];
    $q = "SELECT * 
              FROM fmss.bni_kode_outlet 
              WHERE id_outlet = '$id_outlet' LIMIT 1";
    $e = pg_query($db, $q);
    $n = pg_num_rows($e);
        
    //DIRCOM1YMHF7:lUUaoheu5wZmNEvg:230:70878:BMS:7646ixjke:3977778885
    // 0                1           2      3   4    5           6    
        
    $ret = array();
    if ($n > 0) {
        while ($data = pg_fetch_object($e)) {
            $settingPartnerCentralId[6] = $data->account_num;
            $settingPartnerCentralId[2] = $data->kode_cabang_bni;
            $settingPartnerCentralId[3] = $data->kode_loket_bni;
            $settingPartnerCentralId[4] = $data->kode_mitra_bni;
            $settingPartnerCentralId[5] = $data->pin_transaksi;
        }
        pg_free_result($e);
        pg_close();
        reconnect($db);
    }    
    return $settingPartnerCentralId;
}

function only_number($data){
    return preg_replace("/[^0-9]/", "", $data);
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

function authorized_alfa($rec, $sha){
    if($rec['Signature'] !== $sha){
        // die(' invalid request');
        $response_alfamart = array(
            "", //uid
            "", //pin
            "", //ref1
            strtoupper($rec['AgentStoreID']), //agenid
            "",//idpel1
            strtoupper($rec['DateTimeRequest']), //datetime request
            '94',//rc
            'ERROR - Signature salah (invalid)',//ket
            '',//datetime response
            '', //billerrefnum/noref2/billercode/idtrx
            '', //info/ket tambahan
            '',//idprod
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        );
        echo implode('|', $response_alfamart);
        die();
    }
    
    if(!checkpin($rec['AgentID'], $rec['AgentPIN'])){
        // die(' invalid request');
        $response_alfamart = array(
            "", //uid
            "", //pin
            "", //ref1
            strtoupper($rec['AgentStoreID']), //agenid
            "",//idpel1
            strtoupper($rec['DateTimeRequest']), //datetime request
            '31',//rc
            'ERROR - Kode Agen tidak terdaftar.',//ket
            '',//datetime response
            '', //billerrefnum/noref2/billercode/idtrx
            '', //info/ket tambahan
            '',//idprod
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        );
        echo implode('|', $response_alfamart);
        die();
    }
}

function maskString($number, $l){
    $mask_string =  str_repeat("*", strlen($number)-$l) . substr($number,-$l);
    return $mask_string;
}

function encodehtml($data){
    return htmlspecialchars($data);
}

function enkripUrl($id_outlet,$id_transaksi){
    $timestamp = date("Y-m-d H:i:s");
    $string = $id_outlet."|".$id_transaksi."|".$timestamp;
    $key = "irememberyou";
    $encrypted = urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key)))));
    $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    return $encrypted;
}

function enkripinew( $string, $action = 'e' ) {
    $secret_key = 'Menc0b4Bertah4n!';
    $secret_iv = 'Te4m53sceT_';
 
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }
 
    return $output;
}


function setMandatoryResponNew($frm, $ref1, $ref2, $ref3, $request) {
    $r_idoutlet = $frm->getMember();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
    $r_status = $frm->getStatus();
    $total = (int) $frm->getNominalAdmin()+(int) $frm->getNominal();
    
    $r_pin = "------";//$frm->getPin();
    $r_saldo = $frm->getSaldo();
    $r_idtrx = $frm->getIdTrx();
    if($r_status == '68' && in_array($r_kdproduk, array('SPEEDY', 'TELEPON'))){
        $r_status = '00';
        $r_keterangan = 'SEDANG DIPROSES';
    } else {
        $r_status = $frm->getStatus();
        $r_keterangan = $frm->getKeterangan();
    }    
    $fee = getnominalup($r_idtrx);
    $saldo_terpotong = $r_nominal + $r_nominaladmin + (int)$fee;
    if($r_status != '00' || (substr($r_kdproduk, 0,6) == "PLNPRA") && $r_nominal == 0){
        $fee = $saldo_terpotong = 0;
    }
    if( ($r_kdproduk == "TELEPON" || $r_kdproduk == "SPEEDY" || $r_kdproduk == "TELEPON2" || $r_kdproduk == "SPEEDY2") && $r_nominal == '0' && $r_status == '00' && $r_keterangan != 'SEDANG DIPROSES'){
        $r_status = "88";
        $r_keterangan = "TRANSAKSI DITOLAK KARENA SEMUA ATAU SALAH SATU TUNGGAKAN/TAGIHAN SUDAH DIBAYAR.";
        $fee = $saldo_terpotong = 0;
    }

    if($r_kdproduk == 'WASBY' && $r_status == '25'){
        $r_keterangan = "Tagihan sudah terbayar.";
    }

    if(substr($r_kdproduk, 0,2) == "WA" && strtoupper($r_status) == 'XX' && (strpos(strtolower($r_keterangan),'report') !== false)){
        $r_keterangan = "Tagihan sudah terbayar.";
    }
    if(substr(strtoupper($r_kdproduk), 0,5) == "BLTRF"){
        $r_idpel1 = $request['idpel1'];
    }
    if(substr(strtoupper($r_kdproduk), 0,9) == "ASRBPJSKS"){
        $r_idpel1 = $request['idpel'];
    } 

    if($r_kdproduk == "TELEPON" || $r_kdproduk == "TELEPON2"){
        $r_idpel1 = $r_idpel1.$r_idpel2;
        $$r_idpel2 = '';
    } 
    $params = array(
        'kodeproduk' => (string) $r_kdproduk, 
        'tanggal' => (string) $r_tanggal, 
        'idpel1' => (string) $r_idpel1, 
        'idpel2' => (string) $r_idpel2, 
        'idpel3' => (string) $r_idpel3, 
        'nominal' => (string) $r_nominal, 
        'admin' => (string) $r_nominaladmin, 
        'id_outlet' => (string) $r_idoutlet, 
        'pin' => (string) "------", 
        'ref1' => (string) $ref1, 
        'ref2' => (string) $r_idtrx, 
        'ref3' => (string) $ref3, 
        'status' => (string) $r_status, 
        'keterangan' => (string) $r_keterangan, 
        'fee' => (string) $fee, 
        'saldo_terpotong' => (string) $saldo_terpotong,
        'sisa_saldo' => (string) $r_saldo,
        'total_bayar' => (string) $total,
    );
    return $params;
}


function getParseProduk($kdproduk,$resp){
    $man = FormatMsg::mandatoryPayment();
    if($kdproduk==KodeProduk::getTelepon()){
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["pay"]."*".$format,$resp);
        //print_r($frm->data);
        //$params = retTelepon($p_params,$frm);
        //print_r($params);
    }else if(in_array($kdproduk,KodeProduk::getTelkom())){
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["pay"]."*".$format,$resp);
        //$params = retSpeedy($p_params,$frm);
        //print_r($params);
    }else if($kdproduk==KodeProduk::getTelkomVision()){
        $format = FormatMsg::telkom();
        $frm = new FormatTelkom($man["pay"]."*".$format,$resp);
        //$params = retTelkomVision($p_params,$frm);
        //print_r($params);
    }else if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids()) || substr(strtoupper($kdproduk),0,7) == "PLNPASC" ){
        $format = FormatMsg::plnPasca();
        $frm = new FormatPlnPasc($man["pay"]."*".$format,$resp);
        //print_r($frm->data);
        //print_r($p_params);
        //$params = retPLNPostpaid($p_params,$frm);
    }else if($kdproduk==KodeProduk::getPLNPrepaid() || $kdproduk=="PLNPRA40" || $kdproduk=="PLNPRAM" || in_array($kdproduk,KodeProduk::getPLNPrepaids()) || substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){
        $format = FormatMsg::plnPra();
        $frm = new FormatPlnPra($man["pay"]."*".$format,$resp);
        //$params = retPLNPrepaid($p_params,$frm);
    }else if($kdproduk==KodeProduk::getPLNNontaglist() || in_array($kdproduk,KodeProduk::getPLNNontaglists()) || substr(strtoupper($kdproduk),0,6) == 'PLNNON'){
        $format = FormatMsg::plnNon();
        $frm = new FormatPlnNon($man["pay"]."*".$format,$resp); 
        //$params = retPLNNontaglist($p_params,$frm);
    }else if(in_array($kdproduk,KodeProduk::getPonselPostpaid())){
        $format = FormatMsg::teleponPasc();
        $frm = new FormatTeleponPasc($man["pay"]."*".$format,$resp);
        //$params = retPonselPostpaid($p_params,$frm);
    } else if(in_array($kdproduk,KodeProduk::getNewPAM())){
        $format = FormatMsg::newPdam();
        $frm = new FormatNewPdam($man["pay"] . "*" . $format, $resp);
        //$params = retNewPAM($p_params,$frm);
    } else if(in_array($kdproduk,KodeProduk::getPAM()) || substr($kdproduk,0,2) == "WA"){

        $format = FormatMsg::pdam();
        $frm = new FormatPdam($man["pay"]."*".$format,$resp);
        // $params = retPAM($p_params,$frm);
        // var_dump($params);
        // return $params;
    }else if($kdproduk==KodeProduk::getAoraTV()){
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["pay"]."*".$format,$resp);
        //$params = retAORATV($p_params,$frm);
    //}else if($kdproduk==KodeProduk::getWOM() || $kdproduk==KodeProduk::getBAF() || $kdproduk==KodeProduk::getMAF() || $kdproduk==KodeProduk::getMCF()){
    }else if(in_array($kdproduk,KodeProduk::getMultiFinance())){
        $format = FormatMsg::multiFinance();
        $frm = new FormatMultiFinance($man["pay"]."*".$format,$resp);
        //$params = retMultifinance($p_params,$frm);
    }else if(in_array($kdproduk,KodeProduk::getTVKabel())){
        $format = FormatMsg::tvKabel();
        $frm = new FormatTvKabel($man["pay"]."*".$format,$resp);
        //$params = retTvKabel($p_params,$frm);
    }else if(in_array($kdproduk,KodeProduk::getAsuransi())){
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["pay"]."*".$format,$resp);
        //$params = retAsuransi($p_params,$frm);
    }else if(in_array($kdproduk,KodeProduk::getKartuKredit()) || substr($kdproduk, 0, 2) == 'KK'){
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["pay"]."*".$format,$resp);
        //$params = retKartuKredit($p_params,$frm);
    }else if($kdproduk == 'PGN'){
        $format = FormatMsg::pgn();
        $frm = new FormatPgn($man["pay"]."*".$format,$resp);
        // $params = retPgn($p_params,$frm);
    } else if($kdproduk == 'GAS'){
        $format = FormatMsg::gas();
        $frm = new FormatGas($man["pay"]."*".$format,$resp);
        // $params = retGas($p_params,$frm);
    }else if(substr($kdproduk, 0, 5) == 'BLTRF' || $kdproduk == 'BLTRFMDR'){
        $format = FormatMsg::lakupandai();
        $frm = new FormatLakupandai($man["pay"]."*".$format,$resp);
        // $params = retGas($p_params,$frm);
    } else if($kdproduk == 'ASRBPJSTK'){
        $format = FormatMsg::bpjs();
        $frm = new FormatBpjs($man["pay"]."*".$format,$resp);
        // $params = retGas($p_params,$frm);
    } else if(substr($kdproduk, 0, 5) == 'PAJAK'){
        $format = FormatMsg::pajakSolo();
        $frm = new FormatPajakPbb($man["pay"]."*".$format,$resp);
        // $params = retPajakPbb($p_params,$frm);
    }else if (substr($kdproduk, 0, 3) == 'PKB') {
        $format = FormatMsg::pkb();
        $frm = new FormatPKB($man["inq"] . "*" . $format, $resp);
        // $params = retPKB($p_params, $frm);
    }
    return $frm;
}


function tambahdataproduk($kdproduk,$frm, $kodebank='' ,$jenistrx = 0){
    if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids()) || substr(strtoupper($kdproduk),0,7) == "PLNPASC" ){
        $tarif = trim($frm->getSubscriberSegmentation());
        $daya = trim($frm->getPowerConsumingCategory());
        $ref = trim($frm->getSwReferenceNumber());
        $stanawal = $frm->getStatus() == '00' ? (string) trim($frm->getPreviousMeterReading11()) : '';
        $billqty = trim($frm->getJumlahBill());
        $stanakhirarray = array(
            trim($frm->getCurentMeterReading11()),
            trim($frm->getCurentMeterReading12()),
            trim($frm->getCurentMeterReading13()),
            trim($frm->getCurentMeterReading14()),
        );
        $stanakhir = $stanakhirarray[$billqty-1];
        $infoteks = trim($frm->getInfoTeks());
        // echo "<pre>",print_r($frm),"</pre>";die();
        if(strtoupper($frm->getMember()) == 'SP193969'){
            return array(
                'jml_bulan' => $frm->getStatus() == '00' ? (string) $billqty : "",
                'tarif' => $frm->getStatus() == '00' ? (string) $tarif : "",
                'daya' => $frm->getStatus() == '00' ? (string) ltrim($daya,'0') : "",
                'ref' => $frm->getStatus() == '00' ? (string) $ref : "",
                'stan' => $frm->getStatus() == '00' ? (string) ltrim($stanawal,'0').'-'.ltrim($stanakhir,'0') : "",
                'infoteks' => $frm->getStatus() == '00' ? (string) $infoteks : "",
            );
        } else {
            return array(
                'jml_bulan' => $frm->getStatus() == '00' ? (string) $billqty : "",
                'tarif' => $frm->getStatus() == '00' ? (string) $tarif : "",
                'daya' => $frm->getStatus() == '00' ? (string) ltrim($daya,'0') : "",
                'ref' => $frm->getStatus() == '00' ? (string) $ref : "",
                'stanawal' => $frm->getStatus() == '00' ? (string) ltrim($stanawal,'0') : "",
                'stanakhir' => $frm->getStatus() == '00' ? (string) ltrim($stanakhir,'0') : "",
                'infoteks' => $frm->getStatus() == '00' ? (string) $infoteks : "",
            );
        }
    } else if($kdproduk==KodeProduk::getPLNPrepaid() || in_array($kdproduk,KodeProduk::getPLNPrepaids()) || substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){
        $meterai = getValue2(trim($frm->getStampDuty()), trim($frm->getMinorUnitStampDuty()));
        $ppn = getValue2($frm->getPPN(), $frm->getMinorUnitPPN());
        $tarif = trim($frm->getSubscriberSegmentation()); 
        $daya = trim($frm->getPowerConsumingCategory()); 
        $ppj= getValue2($frm->getPPJ(), $frm->getMinorUnitPPJ());
        $ref = trim($frm->getNoRef2());
        $angsuran = getValue2($frm->getCustomerPayablesInstallment(),$frm->getMinorUnitCustomerPayablesInstallment());                        
        $pp = getValue2(trim($frm->getPowerPurchase()), trim($frm->getMinorUnitOfPowerPurchase()));
        $kwh = getValue2(trim($frm->getPurchasedKWHUnit()), trim($frm->getMinorUnitOfPurchasedKWHUnit()));
        $token = trim($frm->getTokenPln());

        $nomortoken = $jenistrx == 0 ? "" : substr($token,0,4)." ".substr($token,4,4)." ".substr($token,8,4)." ".substr($token,12,4)." ".substr($token,16,4);
        $infoteks = trim($frm->getInfoText());
        return array(
            'meterai' => $frm->getStatus() == '00' ? (string) $meterai : "",
            'ppn' => $frm->getStatus() == '00' ? (string) $ppn : "",
            'tarif' => $frm->getStatus() == '00' ? (string) $tarif : "",
            'daya' => $frm->getStatus() == '00' ? (string) ltrim($daya,'0') : "",
            'ppj' => $frm->getStatus() == '00' ? (string) $ppj : "",
            'ref' => $frm->getStatus() == '00' ? (string) $ref : "",
            'angsuran' => $frm->getStatus() == '00' ? (string) $angsuran : "",
            'pp' => $frm->getStatus() == '00' ? (string) $pp : "",
            'kwh' => $frm->getStatus() == '00' ? (string) $kwh : "",
            'nomortoken' => $frm->getStatus() == '00' ? (string) $nomortoken : "",
            'infoteks' => $frm->getStatus() == '00' ? (string) $infoteks : "",
        );
    } else if($kdproduk==KodeProduk::getPLNNontaglist() || in_array($kdproduk,KodeProduk::getPLNNontaglists())){
        // echo "<pre>",print_r($frm),"</pre>";
        return array(
            'ref' => $frm->getStatus() == '00' ? (string) trim($frm->getSwRefNumber()) : "",
            'transaction_code' => $frm->getStatus() == '00' ? (string) trim($frm->getTransactionCode()) : "",
            'transaction_nama' => $frm->getStatus() == '00' ? (string) trim($frm->getTransactionName()) : "",
            'registration_date' => $frm->getStatus() == '00' ? (string) trim($frm->getRegistrationDate()) : "",
        );
    } else if(in_array($kdproduk,KodeProduk::getTelkom())){
        // echo "<pre>",print_r($frm),"</pre>";die();
        $qty = (int)$frm->getJumlahBill();
        if($qty == 1){
            $ref = $frm->getNomorReferensi1();
        } else if($qty == 1){
            $ref = $frm->getNomorReferensi1().','.$frm->getNomorReferensi2();
        } else {
            $ref = $frm->getNomorReferensi1().','.$frm->getNomorReferensi2().','.$frm->getNomorReferensi3();
        }
        return array(
            'ref' => $frm->getStatus() == '00' ? (string) trim($ref) : '',
            'jumlahbill' => $frm->getStatus() == '00' ? (string) trim($frm->getJumlahBill()) : '',
        );
    } else if(in_array($kdproduk,KodeProduk::getNewPAM()) || in_array($kdproduk,KodeProduk::getPAM()) || substr($kdproduk,0,2) == "WA"){
        $qty = $frm->getBillQuantity();
        $awal = $frm->getFirstMeterRead1();
        if($qty == 0){
            $qty == 1;
        }
        if($qty == 1){
            $akhir = $frm->getLastMeterRead1();
        } else if($qty == 2){
            $akhir = $frm->getLastMeterRead2();
        } else if($qty == 3){
            $akhir = $frm->getLastMeterRead3();
        } else if($qty == 4){
            $akhir = $frm->getLastMeterRead4();
        } else if($qty == 5){
            $akhir = $frm->getLastMeterRead5();
        } else {
            $akhir = $frm->getLastMeterRead6();
        }
        if(strtoupper($frm->getMember()) == 'SP193969'){
            return array(
                'jml_bln' => $frm->getStatus() == '00' ? (string) trim($qty) : '',
                'stan' => $frm->getStatus() == '00' ? (string) trim($awal).'-'.trim($akhir) : '',
            );
        } else {
            return array(
                'jml_bln' => $frm->getStatus() == '00' ? (string) trim($qty) : '',
                'stan_awal' => $frm->getStatus() == '00' ? (string) trim($awal) : '',
                'stan_akhir' => $frm->getStatus() == '00' ? (string) trim($akhir) : '',
            );
        }
    } else if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS"){
        $id_biller = cekBiller($frm->getIdTrx());
        $novakel = '';
        $jmlkel = '';

        if($jenistrx == 0){
            $gwrefnum = '';
            $jmlkel = $frm->getNoref1();
        }else{
            $gwrefnum = $frm->getBillerRefNumber();
            $datakel = explode('.', $frm->getNoref1());
            $jmlkel = $datakel[0];
        }

        // echo "<pre>",print_r($frm),"</pre>";die();
        return array(
            'no_va_keluarga' => $frm->getStatus() == '00' ? (string) trim($frm->getCustomerID()) : '',
            'jml_keluarga' => $frm->getStatus() == '00' ? (string) trim($jmlkel) : '',
            'no_hp' => $frm->getStatus() == '00' ? (string) trim($frm->getCustomerPhoneNumber()) : '',
            'no_ref' => $frm->getStatus() == '00' ? (string) trim($gwrefnum) : '',
        );
    } else if($kdproduk === 'ASRCAR'){
        // echo "<pre>",print_r($frm),"</pre>";
        return array(
            'no_polish' => $frm->getStatus() == '00' ? (string) trim($frm->getCustomerPhoneNumber()) : '',
        );
    }else if(in_array($kdproduk, KodeProduk::getMultiFinance())){
        // echo "<pre>",print_r($frm),"</pre>";

        if(substr(strtoupper($kdproduk), 0,4) =='FNHC'){
            if(strtoupper($frm->getMember()) == 'SP193969' || strtoupper($frm->getMember()) == 'HH122973'){
                return array(
                    'jatuh_tempo' => $frm->getStatus() == '00' ? (string) trim($frm->getLastPaidDueDate()) : '',
                    'jenis_tagihan' => $frm->getStatus() == '00' ? (string) trim($frm->getNoref1()) : '',
                    'nama_kredit' => $frm->getStatus() == '00' ? (string) trim($frm->getPTName()) : '',
                );
            }else{
               return array(
                    'jatuh_tempo' => $frm->getStatus() == '00' ? (string) trim($frm->getLastPaidDueDate()) : '',
                    'type' => $frm->getStatus() == '00' ? (string) trim($frm->getItemMerkType()) : '',
                    'tenor' => $frm->getStatus() == '00' ? (string) trim($frm->getTenor()) : '',
                    'car_number' => $frm->getStatus() == '00' ? (string) trim($frm->getCarNumber()) : '',
                ); 
            }
        }else{
            return array(
                'jatuh_tempo' => $frm->getStatus() == '00' ? (string) trim($frm->getLastPaidDueDate()) : '',
                'type' => $frm->getStatus() == '00' ? (string) trim($frm->getItemMerkType()) : '',
                'tenor' => $frm->getStatus() == '00' ? (string) trim($frm->getTenor()) : '',
                'car_number' => $frm->getStatus() == '00' ? (string) trim($frm->getCarNumber()) : '',
            );
        }
    } else if($kdproduk === 'PGN'){
        // echo "<pre>",print_r($frm),"</pre>";
        return array(
            'aj_ref'=> $frm->getStatus() == '00' ? (string) trim($frm->getRefId()) : '',
            'usage' => $frm->getStatus() == '00' ? (string) trim($frm->getUsage()) : '',
        );
    } else if($kdproduk === 'GAS'){
        // echo "<pre>",print_r($frm),"</pre>";die();
        $qty = $frm->getBILLQUANTITY();
        $awal = $frm->getFIRSTMETERREAD1();
        if($qty == 0){
            $qty == 1;
        }

        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
        $jmlbln = explode(',',$r_periode_tagihan);
        $jmlbln = (int) count($jmlbln);
        if($jmlbln == 1){
            $akhir = $frm->getLASTMETERREAD1();
        } else if($jmlbln == 2){
            $akhir = $frm->getLASTMETERREAD2();
        } else if($jmlbln == 3){
            $akhir = $frm->getLASTMETERREAD3();
        } else if($jmlbln == 4){
            $akhir = $frm->getLASTMETERREAD4();
        } else if($jmlbln == 5){
            $akhir = $frm->getLASTMETERREAD5();
        } else {
            $akhir = $frm->getLASTMETERREAD6();
        }
        return array(
            'tarif' => $frm->getStatus() == '00' ? (string) trim($frm->getCUSTOMERID2()) : '',
            'alamat' => $frm->getStatus() == '00' ? (string) trim($frm->getCUSTOMERADDRESS()) : '',
            'jml_bln' => $frm->getStatus() == '00' ? (string) trim($qty) : '',
            'stan_awal' => $frm->getStatus() == '00' ? (string) trim($awal) : '',
            'stan_akhir' => $frm->getStatus() == '00' ? (string) trim($akhir) : '',
            'reff_no' => $frm->getStatus() == '00' ? (string) trim($frm->getSWREFNUM()) : '',
        );
    } else if(in_array($kdproduk, KodeProduk::getPonselPostpaid())){
        $noref = trim($frm->getNoref1()) == "" ? '' : (string) trim($frm->getNoref1());
        return array(
            'no_ref' => $frm->getStatus() == '00' ? $noref : '',
        );
    } else if(in_array($kdproduk, KodeProduk::getKartuKredit()) || substr(strtoupper($kdproduk), 0, 2) == "KK"){
        $customer_name = trim($frm->getCustomerName());
        return array(
            'customer_name' => $frm->getStatus() == '00' ? $customer_name : '',
        );
    } else if(substr(strtoupper($kdproduk),0,5) == "PAJAK"){
        $customer_name = trim($frm->getNama());
        $tahun_pajak = trim($frm->getTahunPajak());
        $lokasi = trim($frm->getLokasi());
        return array(
            'lokasi' => $frm->getStatus() == '00' ? $lokasi : '',
            'tahun_pajak' => $frm->getStatus() == '00' ? $tahun_pajak : '',
        );
    }  else if(substr(strtoupper($kdproduk), 0,2) == "TV"){


        if(strtoupper($frm->getMember()) == 'SP193969' || strtoupper($frm->getMember()) == 'HH122973'|| strtoupper($frm->getMember()) == 'SP77997'){

            if($kdproduk == "TVTLKMV"){
                 $billreffnumber = $jenistrx == 0 ? "" : trim($frm->getNomorReferensi1());
                 $jml_quantity = ltrim($frm->getJumlahBill(), '0');
            } else{
                 $billreffnumber = $jenistrx == 0 ? "" : trim($frm->getBillerRefNumber());
                 $jml_quantity = ltrim($frm->getBillQuantity(), '0');
            }
            return array(
                'jml_quantity' =>  $jml_quantity,
                'reffnumber' => $billreffnumber,
            );
        }else{
            return array();
        }
    } else if(substr(strtoupper($kdproduk), 0,5) == "BLTRF"){
        $r_nama_bank = getnamabank($kodebank);
        return array(
            'nama_bank' => $frm->getStatus() == '00' ? $r_nama_bank : '',
            'kode_bank' => $frm->getStatus() == '00' ? $kodebank : '',
        );
    } else if(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        $nomorpengesahan = $jenistrx == 0 ? "" : $frm->getREFF_NUM();
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? $frm->getUSR_FULL_NAME() : '',
            'merek' => $frm->getStatus() == '00' ? $frm->getKD_MERK() : '',
            'type' => $frm->getStatus() == '00' ? $frm->getKD_TIPE() : '',
            'tahun_rakit' => $frm->getStatus() == '00' ? $frm->getKND_THN_BUAT() : '',
            'no_rangka' => $frm->getStatus() == '00' ? $frm->getKND_RANGKA() : '',
            'no_mesin' => $frm->getStatus() == '00' ? $frm->getKND_MESIN() : '',
            'no_bpkb' => $frm->getStatus() == '00' ? $frm->getKND_NO_BPKB() : '',
            'masa_pajak' => $frm->getStatus() == '00' ? str_replace("-","",$frm->getKND_TGL_FAKTUR()).'-'.str_replace("-","",$frm->getKND_TGL_KUWITANSI()) : '',
            'alamat' => $frm->getStatus() == '00' ? str_replace("/",".",$frm->getKND_ALAMAT()) : '',
            'no_pengesahan' => $frm->getStatus() == '00' ? $nomorpengesahan : ''
        );
    }else{
        return array();
    }
}


function tambahdataproduk2($kdproduk, $frm){
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }

    if(in_array($kdproduk, KodeProduk::getPonselPostpaid())){
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : "",
        );
    } else if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : "",
            'angsuran_ke' => $frm->getStatus() == '00' ? (string) $r_periode_tagihan : "",
        );
    } else if(in_array($kdproduk, KodeProduk::getKartuKredit()) || substr(strtoupper($kdproduk), 0, 2) == "KK"){
        if(trim($frm->getLastPaidPeriode()) != '' && trim($frm->getLastPaidDueDate()) != ''){
            $lastpaidperiode = date_parse_from_format("d-m-Y", trim($frm->getLastPaidPeriode()));
            $lastpaiddue = date_parse_from_format("d-m-Y", trim($frm->getLastPaidDueDate()));
            return array(
                'last_paid_periode' => $frm->getStatus() == '00' ? (string) $lastpaidperiode['year'].'-'.$lastpaidperiode['month'].'-'.$lastpaidperiode['day'] : "",
                'last_paid_due_date' => $frm->getStatus() == '00' ? (string) $lastpaiddue['year'].'-'.$lastpaiddue['month'].'-'.$lastpaiddue['day'] : "",
            );
        } else {
            return array(
                'last_paid_periode' => "",
                'last_paid_due_date' => "",
            );
        }
    } else if(substr(strtoupper($kdproduk), 0,5) == "BLTRF" || $kdproduk == 'BLTRFMDR'){
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : ""
        );
    } else if(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $customer_name = trim($frm->getNama());
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? $customer_name : '',
            
        );
    } else if(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        return array();
    } else{
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : "",
            'periode' => $frm->getStatus() == '00' ? (string) $r_periode_tagihan : "",
        );
    }
}


function getdataCustom($id_outlet, $id_produk, $idpel1, $idpel2){

    if ($id_outlet == "" || $id_produk == ""){
        return array();
    }

    if($idpel1 == "" || $idpel1 == '0'){
        $idpel = $idpel2;
    } else {
        $idpel = $idpel1;
    }

    if($id_produk == 'TELEPON' || $id_produk == 'TELEPON2'){
        if(substr(strtoupper($idpel2), 0,2) == "00"){
            $idpel2 = ltrim($idpel2, '0');
        }else{
            $idpel2 = $idpel2;
        }

        if(substr(strtoupper($idpel1), 0,2) == "00"){
            $idpel1 = "0".ltrim($idpel1, '0');
        }else{
            $idpel1 = $idpel1;
        }
        $idpel = $idpel1.$idpel2;

    }

    $db = $GLOBALS["pgsql"];
    $array = array();
    $q = "select nominal, id_transaksi, bill_info6, bill_info12, bill_info53, bill_info20 from fmss.transaksi where 
    id_outlet = $1 and id_produk = $2 
    and ( bill_info1 = $3 or bill_info2 = $4 ) 
    and transaction_date = now()::date and response_code = '00' 
    and jenis_transaksi = 0 order by time_request desc limit 1";
    $bind = array();
    $bind[] = $id_outlet;
    $bind[] = $id_produk;
    $bind[] = $idpel;
    $bind[] = $idpel;

    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $array = array($r->id_transaksi,$r->nominal,$r->bill_info6,$r->bill_info12,$r->bill_info53,$r->bill_info20);
    }
    pg_free_result($e);
    pg_close($db);    
    return $array;
}

function getBlnIdx($str){
    $str = strtolower($str);
    $arr = array("01" => "jan", "02" => "feb", "03" => "mar", "04" => "apr", "05" => "mei", "06" => "jun", "07" => "jul", "08" => "agu", "09" => "sep", 10 => "okt", 11 => "nov", 12 => "des");    
    return array_search($str, $arr);
}

function penyesuaianFormatYYMM($str){
    if (preg_match('/^\d/', $str) === 1){
        return $str;
    } else {
        $thn = substr($str, 3, 4);
        $bln = getBlnIdx(substr($str, 0, 3));
        return $thn.$bln;
    }
}

function getPeriodePLNPOS($bill){
    $bill_period = "";
    $i = 0;
    foreach($bill as $v){
        if($i > 0 && $v<> ""){
            $bill_period .= ",";
        }
                $v = penyesuaianFormatYYMM($v);
        $bill_period .= $v;
        $i++;
    }
    return $bill_period;
}

function getBillPeriod($kdproduk,$frm){
    $bill_period = "";
    $prevyear = date("Y",strtotime("-1 year"));
    $curyear = date("Y");
    $man = FormatMsg::mandatoryPayment();
    if($kdproduk==KodeProduk::getTelepon()){
        $periode1 = ""; $periode2 = ""; $periode3 = "";

        if(trim($frm->getNomorReferensi1())<>""){
            if(substr(trim($frm->getNomorReferensi1()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode1 = $prefixtgl.substr(trim($frm->getNomorReferensi1()),0,3);
        }
        if(trim($frm->getNomorReferensi2())<>""){
            if(substr(trim($frm->getNomorReferensi2()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode2 = $prefixtgl.substr(trim($frm->getNomorReferensi2()),0,3);
        }
        if(trim($frm->getNomorReferensi3())<>""){
            if(substr(trim($frm->getNomorReferensi3()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode3 = $prefixtgl.substr(trim($frm->getNomorReferensi3()),0,3);
        }
        
        $bill = array ($periode1, $periode2, $periode3);
        $bill_period = getPeriode($bill);
    }else if($kdproduk==KodeProduk::getSpeedy()){
        $periode1 = ""; $periode2 = ""; $periode3 = "";
        
        if(trim($frm->getNomorReferensi1())<>""){

            if(substr(trim($frm->getNomorReferensi1()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode1 = $prefixtgl.substr(trim($frm->getNomorReferensi1()),0,3);
        }
        if(trim($frm->getNomorReferensi2())<>""){
            if(substr(trim($frm->getNomorReferensi2()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode2 = $prefixtgl.substr(trim($frm->getNomorReferensi2()),0,3);
        }
        if(trim($frm->getNomorReferensi3())<>""){
            if(substr(trim($frm->getNomorReferensi3()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode3 = $prefixtgl.substr(trim($frm->getNomorReferensi3()),0,3);
        }
        
    
        $bill = array ($periode1, $periode2, $periode3);
        $bill_period = getPeriode($bill);
    }else if($kdproduk==KodeProduk::getTelkomVision()){
        $periode1 = ""; $periode2 = ""; $periode3 = "";
        
        if(trim($frm->getNomorReferensi1())<>""){
            if(substr(trim($frm->getNomorReferensi1()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode1 = $prefixtgl.substr(trim($frm->getNomorReferensi1()),0,3);
        }
        if(trim($frm->getNomorReferensi2())<>""){
            if(substr(trim($frm->getNomorReferensi2()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode2 = $prefixtgl.substr(trim($frm->getNomorReferensi2()),0,3);
        }
        if(trim($frm->getNomorReferensi3())<>""){
            if(substr(trim($frm->getNomorReferensi3()),0,1) == "9"){
                $prefixtgl = substr($prevyear,0,3);
            } else {
                $prefixtgl = substr($curyear,0,3);
            }
            $periode3 = $prefixtgl.substr(trim($frm->getNomorReferensi3()),0,3);
        }
        
        $bill = array ($periode1, $periode2, $periode3);
        $bill_period = getPeriode($bill);
    }else if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids()) || substr(strtoupper($kdproduk),0,7) == "PLNPASC" ){
        $bill = array ( trim($frm->getBillPeriod1()),
                        trim($frm->getBillPeriod2()),
                        trim($frm->getBillPeriod3()),
                        trim($frm->getBillPeriod4()));
        $bill_period = getPeriodePLNPOS($bill);
    }else if($kdproduk==KodeProduk::getPLNPrepaid() || in_array($kdproduk,KodeProduk::getPLNPrepaids()) || substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){
        $bill_period = "";
    }else if($kdproduk==KodeProduk::getPLNNontaglist() || in_array($kdproduk,KodeProduk::getPLNNontaglists())){
        $bill_period = "";
    }else if(in_array($kdproduk,KodeProduk::getPonselPostpaid())){
        if($kdproduk == 'HPTSEL' || $kdproduk == 'HPTSELH'){
            $bill_period = (string) $frm->getMonthPeriod1();
        } else {
            $bill_period = "";
        }
    }else if(in_array($kdproduk,KodeProduk::getNewPAM())){
        $bill = array ( trim($frm->getYearPeriod1())."".trim($frm->getMonthPeriod1()),
                        trim($frm->getYearPeriod2())."".trim($frm->getMonthPeriod2()),
                        trim($frm->getYearPeriod3())."".trim($frm->getMonthPeriod3()),
                        trim($frm->getYearPeriod4())."".trim($frm->getMonthPeriod4()),
                        trim($frm->getYearPeriod5())."".trim($frm->getMonthPeriod5()),
                        trim($frm->getYearPeriod6())."".trim($frm->getMonthPeriod6()));
        $bill_period = getPeriode($bill);
    }else if(in_array($kdproduk,KodeProduk::getPAM()) || substr($kdproduk,0,2) == "WA"){
        $bln1 = strlen($frm->getMonthPeriod1()) == 1 ? '0'.$frm->getMonthPeriod1() : $frm->getMonthPeriod1();
        $bln2 = strlen($frm->getMonthPeriod2()) == 1 ? '0'.$frm->getMonthPeriod2() : $frm->getMonthPeriod2();
        $bln3 = strlen($frm->getMonthPeriod3()) == 1 ? '0'.$frm->getMonthPeriod3() : $frm->getMonthPeriod3();
        $bln4 = strlen($frm->getMonthPeriod4()) == 1 ? '0'.$frm->getMonthPeriod4() : $frm->getMonthPeriod4();
        $bln5 = strlen($frm->getMonthPeriod5()) == 1 ? '0'.$frm->getMonthPeriod5() : $frm->getMonthPeriod5();
        $bln6 = strlen($frm->getMonthPeriod6()) == 1 ? '0'.$frm->getMonthPeriod6() : $frm->getMonthPeriod6();
        $bill = array ( trim($frm->getYearPeriod1())."".trim($bln1),
                        trim($frm->getYearPeriod2())."".trim($bln2),
                        trim($frm->getYearPeriod3())."".trim($bln3),
                        trim($frm->getYearPeriod4())."".trim($bln4),
                        trim($frm->getYearPeriod5())."".trim($bln5),
                        trim($frm->getYearPeriod6())."".trim($bln6));

       
        $bill_period = getPeriode($bill);
    }else if($kdproduk==KodeProduk::getAoraTV()){
        $bill_period = substr($frm->getPeriode(), 8,2)."-".substr($frm->getPeriode(), 5, 2)."-".substr($frm->getPeriode(), 0,4);
    }else if(in_array($kdproduk,KodeProduk::getMultiFinance())){
        $bill_period = "";
    }else if(in_array($kdproduk,KodeProduk::getTVKabel())){
        if($kdproduk == 'TVINDVS'){
            $bill_period = (string) $frm->getPeriode();
        } else {
            $bill_period = "";
        }
    }else if(in_array($kdproduk,KodeProduk::getAsuransi())){
        if($kdproduk == "ASRPRU"){
            $getdt1 = trim($frm->getBillerCode());
            $getdt = explode(" ",$getdt1);
            $bill_period = str_replace("/","",$getdt[2]);
        }elseif($kdproduk == "ASRJWS"){
            $jmlbln = $frm->getBillQuantity();
            if($jmlbln > 1){
                $getdt1 = trim($frm->getBillerRefNumber());
                $getdt  = explode(",",$getdt1);
                $tmp    = explode(".",$getdt[8]);
                $bill_period = $tmp[1];
            }else{
                $getdt1 = trim($frm->getBillerRefNumber());
                $getdt = explode(",",$getdt1);
                $tmp = explode(".",$getdt[2]);
                $bill_period = $tmp[1];
            }
        }else{
          $bill_period = (int)trim($frm->getBillQuantity());
        }
    }else if(in_array($kdproduk,KodeProduk::getKartuKredit())){
        $bill_period = "";
    }else if($kdproduk == "PGN"){
        $bill_period = $frm->getPeriode();
    }else if(substr($kdproduk,0,5) == "PAJAK"){
        $bill_period = $frm->getTahunPajak();
    } else if(substr($kdproduk,0,3) == "PKB"){
        $bill_period = str_replace("-","",$frm->getKND_TGL_FAKTUR()).'-'.str_replace("-","",$frm->getKND_TGL_KUWITANSI());
    } else if($kdproduk == "GAS"){
        $bill = array ( trim($frm->getYEARPERIOD1())."".trim($frm->getMONTHPERIOD1()),
                        trim($frm->getYEARPERIOD2())."".trim($frm->getMONTHPERIOD2()),
                        trim($frm->getYEARPERIOD3())."".trim($frm->getMONTHPERIOD3()),
                        trim($frm->getYEARPERIOD4())."".trim($frm->getMONTHPERIOD4()),
                        trim($frm->getYEARPERIOD5())."".trim($frm->getMONTHPERIOD5()),
                        trim($frm->getYEARPERIOD6())."".trim($frm->getMONTHPERIOD6()));
        $bill_period = getPeriode($bill);
    }
    return $bill_period;
}


function getNamaPelanggan($kdproduk,$frm){
    $nama = "";
    $man = FormatMsg::mandatoryPayment();
    if($kdproduk==KodeProduk::getTelepon()){
        $nama = trim($frm->getNamaPelanggan());
    }else if($kdproduk==KodeProduk::getSpeedy()){
        $nama = trim($frm->getNamaPelanggan());
    }else if($kdproduk==KodeProduk::getTelkomVision()){
        $nama = trim($frm->getNamaPelanggan());
    }else if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids()) || substr(strtoupper($kdproduk),0,7) == "PLNPASC" ){
        $nama = trim($frm->getNamaPelanggan());
    }else if($kdproduk==KodeProduk::getPLNPrepaid()|| $kdproduk=="PLNPRA40" || in_array($kdproduk,KodeProduk::getPLNPrepaids()) || substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){
        $nama = trim($frm->getNamaPelanggan());
    }else if($kdproduk==KodeProduk::getPLNNontaglist() || in_array($kdproduk,KodeProduk::getPLNNontaglists())){
        $nama = trim($frm->getSubscriberName());
    }else if(in_array($kdproduk,KodeProduk::getPonselPostpaid())){
        $nama = trim($frm->getCustomerName());
    }else if(in_array($kdproduk,KodeProduk::getNewPAM())){
        $nama = trim($frm->getCustomerName());
    }else if(in_array($kdproduk,KodeProduk::getPAM()) || substr($kdproduk,0,2) == "WA"){
        $nama = trim($frm->getCustomerName());
    }else if($kdproduk==KodeProduk::getAoraTV()){
        $nama = trim($frm->getCustomerName());
    }else if(in_array($kdproduk,KodeProduk::getMultiFinance())){
        $nama = trim($frm->getCustomerName());
    }else if(in_array($kdproduk,KodeProduk::getTVKabel())){
        $nama = trim($frm->getCustomerName());
    }else if(in_array($kdproduk,KodeProduk::getAsuransi())){
        $nama = trim($frm->getCustomerName());
    }else if(in_array($kdproduk,KodeProduk::getKartuKredit())){
        $nama = trim($frm->getCustomerName());
    }else if(substr($kdproduk,0,5) == "PAJAK"){
        $nama = $frm->getNama();
    }else if(substr($kdproduk,0,3) == "PKB"){
        $nama = $frm->getUSR_FULL_NAME();
    }else if($kdproduk == "PGN"){
        $nama = trim($frm->getCustomerName());
    } else if($kdproduk == "GAS"){
        $nama = trim($frm->getCUSTOMERNAME());
    }else if(substr($kdproduk,0,5) == "BLTRF" || $kdproduk == 'BLTRFMDR'){
        if($kdproduk == "BLTRFBCA"){
            if($frm->getCommand() == "TAGIHAN"){
                 $nama = trim($frm->getName());
             }else{
                $getidtrxinq = getGlobal($frm->getIdTrx(), 'bill_info86');
                $nama = getGlobal($getidtrxinq, 'bill_info20');
             }
        }else{
            $nama = trim($frm->getName());
        }
    }
    
    return $nama;
}


function getLastPaidPeriode($kdproduk,$frm){
    $bill_period = "";
    $man = FormatMsg::mandatoryPayment();
    if(in_array($kdproduk, KodeProduk::getMultiFinance())){
        $lastpaidperiode = $frm->getLastPaidPeriode();
    }
    return $lastpaidperiode;
}

function getPeriode($bill){
    $bill_period = "";
    $i = 0;
    foreach($bill as $v){
        if($i > 0 && $v<> ""){
            $bill_period .= ",";
        }
        $bill_period .= $v;
        $i++;
    }
    if((strpos($bill_period,',0') !== false)){
       $bill_period = str_replace(',0',"",$bill_period);
    }

    return $bill_period;
}

function getValue2($nominal="0", $minor=0){
    $nominal=sprintf("%".($minor+1)."0s", $nominal);
    $ret=substr($nominal, 0, (strlen($nominal)-$minor)).".".substr($nominal,(strlen($nominal)-$minor));
    return (double) $ret;
}


function cekBiller($id_trx){
    if ($id_trx == ""){
        return 0;
    }
    $db = $GLOBALS["pgsql"];
    $ret = 0;
    $q = "select id_biller from transaksi where id_transaksi = $1";
    // echo $q;die();
     $bind = array();
    $bind[] = $id_trx;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->id_biller;
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}

function getGlobal($idtrx,$field)
{
    $db = $GLOBALS["pgsql"];
    $ret = 0;
    $q = "select $field from transaksi where id_transaksi = $1";
    $bind = array();
    $bind[] = $idtrx;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->$field;
       
    }

    pg_free_result($e);
    pg_close($db);
    return $ret;
}


?>
