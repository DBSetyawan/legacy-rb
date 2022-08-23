<?php
require_once("include/PgDBI2.class.php");
require_once("include/Database.class.php");
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
require_once("include/format_message/FormatNewPdam.class.php");
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
require_once("include/format_message/FormatPgn.class.php");
require_once("include/format_message/FormatGas.class.php");
require_once("include/format_message/FormatLakupandai.class.php");
require_once("include/format_message/FormatPajakPbb.class.php");
require_once("include/format_message/FormatBpjs.class.php");
require_once("include/format_message/FormatPKB.class.php");
require_once("include/format_message/FormatOpenDenom.class.php");

function select_replika($sql, $bind = null){
    $pgsql = reconnect_ro();
    if($bind == null){
        $temp = pg_query($pgsql, $sql);
    }else {
        $temp = pg_query_params($pgsql,$sql,$bind);
    }
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
    return $out;
}

function appendfilehit($loglines, $name) {
    try {
        $file = getcwd() . "/logs/" . date("Ymd") . 'hit-'.$name.'-hit.log';
        if (file_exists($file) == false) {
            $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);
            fclose($handle);
        }
        file_put_contents($file, $loglines . "\n", FILE_APPEND | LOCK_EX);
    } catch (Exception $e){

    }
}

function cekMandatoryRequestJsonPulsaGame($obj){
    $array_mandatory = array('kode_produk', 'no_hp', 'uid', 'pin');
    foreach($obj as $keys => $values){
        $key = strtolower($keys);
        $value = strtolower($values);
        if(in_array($key, $array_mandatory)){
            if($value == ""){
                echo json_encode(array('error'=>$key.' harus diisi'));
                die();
            }
        }
    }
}

function select_master($sql, $bind = null){
    $pgsql = reconnect();
    if($bind == null){
        $temp = pg_query($pgsql, $sql);
    }else {
        $temp = pg_query_params($pgsql,$sql,$bind);
    }
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
    return $out;
}

function write_master($sql, $bind = null){
    $pgsql = reconnect();
    if($bind == null){
        $temp = pg_affected_rows(pg_query($pgsql, $sql));
    }else {
        $temp = pg_affected_rows(pg_query_params($pgsql,$sql,$bind));
    }
    
    pg_close($pgsql);
    return $temp;
}

function write_master_fmss($sql, $bind = null){
    $pgsql = reconnect_fmss();
    if($bind == null){
        $temp = pg_affected_rows(pg_query($pgsql, $sql));
    }else {
        $temp = pg_affected_rows(pg_query_params($pgsql,$sql,$bind));
    }
    
    pg_close($pgsql);
    return $temp;
}

function write_master_ro($sql, $bind = null){
    $pgsql = reconnect_ro();
    if($bind == null){
        $temp = pg_affected_rows(pg_query($pgsql, $sql));
    }else {
        $temp = pg_affected_rows(pg_query_params($pgsql,$sql,$bind));
    }
    
    pg_close($pgsql);
    return $temp;
}

function write_master_devel($sql, $bind = null){
    $pgsql = reconnect_D();
    if($bind == null){
        $temp = pg_affected_rows(pg_query($pgsql, $sql));
    }else {
        $temp = pg_affected_rows(pg_query_params($pgsql,$sql,$bind));
    }
    
    pg_close($pgsql);
    return $temp;
}

function query($pgsql, $sql, $bind = null) {
    $conn = reconnect();
    if($bind == null){
        $temp = pg_query($conn, $sql);
    }else {
        $temp = pg_query_params($conn,$sql,$bind);
    }
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
    pg_close($conn);
    return $out;
}


function checkHakAksesMP($pgsql, $idOutlet) {  

    $q = "select fmss.get_mp_priv($1, 'H2H XML');";
    $ret = select_replika($q, array($idOutlet));            
    foreach ($ret as $val) {
        $data = $val->get_mp_priv;        
        if ($data == '') {
            return 0;
        } else {
            return 1;
        }
    }

}

function pad($str, $length, $char, $position){
	$ret=$str;
	if($position=="center"){
		$length=(int) abs(($length-strlen($str))/2);
		$length=$length+strlen($str);
		$justify="";
	}else{
		$justify=($position=="left"?"":"-");
	}
	$ret=sprintf("%".$justify.$char.$length."s",$str);
	
	return $ret;
}
function trimed($txt){
  $txt = trim($txt);
    while( strpos($txt, '  ') ){
      $txt = str_replace('  ', ' ', $txt);
    }
  return $txt;
}
function convertMessage($data,$separator){
	$msg = "";
	$i = 0;
	foreach($data as $v){
		if($i <> 0){
			$msg .= $separator;
		}
		$va = replace_forbidden_chars($v);
		$msg .= $va;
		//$msg .= $v;
		$i++;
	}
	return $msg;
}

function sendsms($step,$mid,$sender,$receiver,$msg,$via){    
    $msg = replace_forbidden_chars($msg);
    $msg = pg_escape_string($msg);    
    $q = " insert INTO message_outbox(sender, receiver, mid, step, content, via, is_sent, date_created) 
                    VALUES (
                    $1,
                    $2,
                    $3,
                    $4,
                    $5,
                    $6,
                    0,
                    NOW()
                    )";
    
    write_master($q, array($sender,$receiver,$mid,$step,$msg,$via));
}

function postValue($msg, $timeout=300){
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip"] . $GLOBALS["__CFG_urltarget"];
    $ch = curl_init();



    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport"]); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    
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

function postValuetf($msg, $timeout=300){
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip_tf"] . $GLOBALS["__CFG_urltarget_tf"];
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_tf"]); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    
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

function postValuepdam($msg, $timeout=300){
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip_pdam"] . $GLOBALS["__CFG_urltarget_pdam"];
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_pdam"]); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    
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

function postValue_fmssweb4($msg, $timeout=300){
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip_fmssweb4"] . $GLOBALS["__CFG_urltarget_fmssweb4"];
    // $urldebug = "http://". $GLOBALS["__CFG_urltargetip_fmssweb4"].':'.$GLOBALS["__CFG_urltargetport_fmssweb4"]. $GLOBALS["__CFG_urltarget_fmssweb4"];
    // echo $urldebug;echo '?'.$msg;die();
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_fmssweb4"]); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    
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
function postValue_fmssweb2($msg, $timeout=300){
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip_fmssweb2"] . $GLOBALS["__CFG_urltarget_fmssweb2"];
    // $urldebug = "http://". $GLOBALS["__CFG_urltargetip_fmssweb2"].':'.$GLOBALS["__CFG_urltargetport_fmssweb2"]. $GLOBALS["__CFG_urltarget_fmssweb2"];
    // echo $urldebug;echo '?'.$msg;die();
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_fmssweb2"]); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    
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

function postValue_fmssweb3($msg, $timeout=300){
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip_fmssweb3"] . $GLOBALS["__CFG_urltarget_fmssweb3"];
    // echo $url;echo $msg;die();
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_fmssweb3"]); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    
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

function postValueKai($msg, $timeout=60)
{
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip_kai"] . $GLOBALS["__CFG_urltarget_kai"];
    
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

function postValuePortDuaPuluh($msg, $timeout=300){
    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    
    $url = "http://". $GLOBALS["__CFG_urltargetip_duapuluh"] . $GLOBALS["__CFG_urltarget_duapuluh"];
    // $urldebug = "http://". $GLOBALS["__CFG_urltargetip_duapuluh"].':'.$GLOBALS["__CFG_urltargetport_duapuluh"].$GLOBALS["__CFG_urltarget_duapuluh"];
    // echo $urldebug;echo '?'.$msg;die();
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $GLOBALS["__CFG_urltargetport_duapuluh"]); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    
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

function postValueKaiOld($msg){				  	
   	$content = $msg;
			
    $len = strlen($content);
	$hosts = Array($GLOBALS["__CFG_urltargetip_kai"]);
    foreach($hosts as $host){
		$fp = @fsockopen($host, $GLOBALS["__CFG_urltargetport_kai"], $errno, $errdesc ); //54321, &$errno, &$errdesc );
		if($fp){
			break;
		}
	}
	    
	if(!$fp){
		$res = "Error: $errno $errdesc\n";
	}else{
		@fputs( $fp, "POST ".$GLOBALS["__CFG_urltarget_kai"]." HTTP/1.0\r\n");
		@fputs( $fp, "Connection: close\r\n");
		@fputs( $fp, "Content-Type: application/x-www-form-urlencoded\r\n");
	    @fputs( $fp, "Content-Length: $len\r\n\r\n");
	    @fputs( $fp, $content);
	    while(!feof($fp)){
			$reply[] = @fgets( $fp, 2000000);
		}
		@fclose( $fp ); 
		$psn = "";
	    foreach($reply as $v){
			$psn .= nl2br($v);
		}
			
		$msg = $psn;
		$res = $reply; 
	}
	return $res;
}

function postValueKaiDevel($msg){				  	
   	$content = $msg;
			
    $len = strlen($content);
	$hosts = Array($GLOBALS["__CFG_urltargetip_jarvis"]);
    foreach($hosts as $host){
		$fp = @fsockopen($host, $GLOBALS["__CFG_urltargetport_jarvis"], $errno, $errdesc ); //54321, &$errno, &$errdesc );
		if($fp){
			break;
		}
	}
	    
	if(!$fp){
		$res = "Error: $errno $errdesc\n";
	}else{
		@fputs( $fp, "POST ".$GLOBALS["__CFG_urltarget_jarvis"]." HTTP/1.0\r\n");
		@fputs( $fp, "Connection: close\r\n");
		@fputs( $fp, "Content-Type: application/x-www-form-urlencoded\r\n");
	    @fputs( $fp, "Content-Length: $len\r\n\r\n");
	    @fputs( $fp, $content);
	    while(!feof($fp)){
			$reply[] = @fgets( $fp, 20000);
		}
		@fclose( $fp ); 
		$psn = "";
	    foreach($reply as $v){
			$psn .= nl2br($v);
		}
			
		$msg = $psn;
		$res = $reply; 
	}
	return $res;
}

function set_aktif_outlet($id_outlet){
    $qn = "update mt_outlet set is_active = 1 where id_outlet = $1";
    $bind = array();
    $bind[] = $id_outlet;
    write_master($qn,$bind);
}

function getNextMID() {    
    $conn = reconnect();
    $qn = "SELECT nextval('message_mid_seq')";
    $result = pg_query($conn, $qn);
    $n = pg_num_rows($result);
    if ($n > 0){        
        $r = pg_fetch_object($result);
        $mid = $r->nextval;
    }    
    pg_free_result($result);
    pg_close($conn);
    return $mid;
}

function writeLog($mid,$step,$sender,$receiver,$msg,$via){
    // $temp = explode("-",$receiver);
    // $data = json_decode($msg);
    // // echo $data->produk;
    // if($temp[2] == "JSON")
    // {
    //     $uid    = strtoupper($data->uid);
    //     $produk = strtoupper($data->kode_produk);
    // }elseif($temp[2] == "IRS"){
    //     $uid    = strtoupper($data->uid);
    //     $produk = strtoupper($data->produk);
    // }elseif($temp[2] == "XML"){
    //     $xml = simplexml_load_string($msg);
    //     $produk = $xml->params->param[0]->value->string;
    //     if($xml->methodName == "rajabiller.pulsa" || $xml->methodName == "rajabiller.game"){
    //         $uid = $xml->params->param[2]->value->string;
    //     }elseif($xml->methodName == "rajabiller.inq" ){
    //          $uid = $xml->params->param[4]->value->string;
    //     }elseif($xml->methodName == "rajabiller.pay" || $xml->methodName == "rajabiller.paydetail"){
    //         $uid = $xml->params->param[5]->value->string;
    //     }elseif($xml->methodName == "rajabiller.bpjsinq"){
    //         $uid = $xml->params->param[3]->value->string;
    //     }elseif($xml->methodName == "rajabiller.bpjspay"){
    //         $uid = $xml->params->param[6]->value->string;
    //     }
    // }
    // $db = reconnect_ro();
    // $sqlcek = "select * from mt_produk_partner_blacklist where id_outlet=$1 and id_produk=$2";
  
    // $bind = array();
    // $bind[] = $uid;
    // $bind[] = $produk;
    
    // $e = pg_query_params($db,$sqlcek,$bind);
    // $n = pg_num_rows($e);
    // if ($n > 0){
    //     if($temp[2] == "JSON"){
    //         echo json_encode(array('error'=>'Id Produk tidak di open untuk sementara waktu'));
    //     }else{
    //         die("Id Produk tidak di open untuk sementara waktu");
    //     }
    //     die();
    // }else{
        $msg = replace_forbidden_chars($msg);
        $msg = pg_escape_string($msg);
        
        if (empty($mid)){
            $mid = 1;   
        }
        $new_step = $step === '' ? '1' : $step;

        // handling mid kosong
        if (empty($mid)){
            $mid = 1;   
        }
        $new_mid = $mid === '' ? '1' : $mid;
        
        $q = "insert INTO sbf.message (date_created,mid,step,sender,receiver,content,id_modul,via,is_sent) 
                        VALUES (
                        NOW(),
                        $1,
                        $2,
                        $3,
                        $4,
                        $5,
                        'H2H',
                        $6,
                        1
                        )";
        return write_master_fmss($q, array($new_mid,$new_step,$sender,$receiver,$msg,$via));
    // }
}


function logDana($msg){
    $msg = replace_forbidden_chars($msg);
    $msg = pg_escape_string($msg);
    // echo $msg;
    $q = "  INSERT INTO log_dana (data_request,request_time) 
                    VALUES ($1,NOW())";
    
    return write_master($q, array($msg));
}
function get_global_tiket($field,$mid){
    
    $db = reconnect_ro();
    $ret = 0;
    $q = "select $field from transaksi where mid = $1";
    $bind = array();
    $bind[] = $mid;
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

function get_global_tiket_kai($idoutlet, $idproduk, $kodepayment){
    
    $db = reconnect_ro();
    $ret = 0;
    $q = "select id_transaksi,bill_info2,nominal from transaksi where id_outlet = $1 and id_produk=$2 and bill_info3=$3 limit 1";
    $bind = array();
    $bind[] = $idoutlet;
    $bind[] = $idproduk;
    $bind[] = $kodepayment;

    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    $ret = array();
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret["id_transaksi"] = $r->id_transaksi;
        $ret["bill_info2"]   = $r->bill_info2;
        $ret["nominal"]   = $r->nominal;
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}
function cek_token($idoutlet){
    $db = reconnect_D();
    $q = "select token FROM h2h_token_mitra WHERE id_outlet = $1";   
    $bind = array();
    $bind[] = strtoupper($idoutlet);
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
  
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->token;
    }else{
        $ret = '';
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}
function insert_token($id_outlet, $token){

    $q = "  INSERT INTO h2h_token_mitra (id_outlet,token) 
                    VALUES ($1,$2)";
    
    return write_master_devel($q, array($id_outlet, $token));
}


function get_mid_from_idtrx($id_trx){
    $mid = "";
    $q = "
        select t.mid FROM fmss.proses_transaksi t WHERE (t.id_transaksi=$1)
        union
        SELECT t.mid FROM fmss.transaksi t WHERE (t.id_transaksi=$2)
        union
        SELECT t.mid FROM fmss.transaksi_backup t WHERE (t.id_transaksi=$3)
    ";    
    if (trim($id_trx) === "" || intval($id_trx) === 0){
        return 0;
    } 
    $bind = array();
    $bind[] = $id_trx;
    $bind[] = $id_trx;
    $bind[] = $id_trx;
    $eqn = select_replika($q,$bind);    
    $row = $eqn[0];
    return $row->mid;       
}
function urlreversalexists($idoutlet){
    if (($idoutlet) === ""){
        return "";
    }
    $ret = "";        
    $qn = "select url_listener from fmss.h2h_outlet_link where id_outlet = $1";    
    $bind = array();
    $bind[] = $idoutlet;
    $eqn = select_replika($qn, $bind);    
    $row = $eqn[0];
    return $row->url_listener;
}
function get_step_from_mid($mid){
    if ($mid){
        $step = "";
        $q = "
        select step from fmss.message where mid = $1
        union
        select step from fmss.message_final where mid = $2
        union
        select step from fmss.message_final_backup where mid =  $3 order by step desc limit 1
        ";
        //die($q);
        $bind = array();
        $bind[] = $mid;
        $bind[] = $mid;
        $bind[] = $mid;
        $eqn = select_replika($q, $bind);    
        $row = $eqn[0];
        return $row->step;              
    } else {
        return 0;
    }
}

function replaceChar($content){
    $content = trim($content);
    $content = str_replace(" ","+",$content);
    $content = str_replace('"',"",$content);
    $content = str_replace("'","",$content);
    $content = str_replace("`","",$content);
    $content = str_replace("~","",$content);
    $content = str_replace(".","",$content);
    $content = str_replace(",","",$content);
    $content = str_replace("\r","",$content);
    $content = str_replace("\n","",$content);
    $content = str_replace("\t","",$content);

    return $content;

}

function getDepDate($idtrx){
    $db = reconnect_ro();
    $result = "";    
    $query = "SELECT bill_info13, bill_info15 
              FROM fmss.transaksi 
              WHERE id_transaksi = $1 ";
     $bind = array();
    $bind[] = $idtrx;
    $data = pg_query_params($db,$query,$bind);
    $hasil = pg_num_rows($data);

    if ($hasil > 0) {
        while ($out = pg_fetch_object($data)) {
            $result = $out->bill_info13 . ";" . $out->bill_info15;
        }
    }
    pg_free_result($data);
    pg_close($db);    
    return $result;
}

function convertFM($data,$separator){
    $msg = "";
    $i = 0;
    foreach($data as $v){
            if($i <> 0){
                    $msg .= $separator;
            }
            $va = replace_forbidden_chars($v);
            $msg .= $va;
            //$msg .= $v;
            $i++;
    }
    return $msg;
}

function emailexists($email){
    $db = reconnect_ro();
    $q = "select upper(email) as email FROM fmss.mt_outlet WHERE email = $1 AND is_active=1";	
    $bind = array();
    $bind[] = strtoupper($email);
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0)
            return TRUE;
        else
            return FALSE;

    pg_free_result($e);
    pg_close($db);
}

function get_id_kota($outlet){
    if ($outlet === ""){
        return "";
    }
    $db = $GLOBALS["pgsql"];
    $idkota = "";
    $q = "select id_kota from mt_outlet where id_outlet = $1";
    $bind = array();
    $bind[] = $outlet;
    $eqn = select_replika($q,$bind);    
    $row = $eqn[0];
    return $row->id_kota;
}

function outletexists($idoutlet){
    $db = reconnect_ro();
    $q = "select id_outlet FROM fmss.mt_outlet WHERE id_outlet = $1 AND is_active=1";
    $bind = array();
    $bind[] = strtoupper($idoutlet);	
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0)
        return TRUE;
    else
        return FALSE;
    pg_free_result($e);
    pg_close($db);
}

function productexists($product){
    $db = reconnect_ro();
    $q = "select id_produk FROM mt_produk WHERE id_produk = $1";    
    $bind = array();
    $bind[] = strtoupper($product); 
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0)
            return TRUE;
        else
            return FALSE;

    pg_free_result($e);
    pg_close($db);
}

function getnominal($id_produk){
    if ($id_produk == ""){
        return 0;
    }
    $db = reconnect_ro();
    $ret = 0;
    $q = "SELECT harga_jual from mt_produk where id_produk = $1 ";
    $bind = array();
    $bind[]=$id_produk;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->harga_jual;
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}

function getIdpel2($id_transaksi){
   
    $db = reconnect_ro();
    $ret = 0;
    $q = "SELECT bill_info2 from transaksi where id_transaksi = $1 ";
    $bind = array();
    $bind[]=$id_transaksi;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->bill_info2;
    }

    pg_free_result($e);
    pg_close($db);
    return $ret;
}



function getnominal_plnprad($id_produk){
    if ($id_produk == ""){
        return 0;
    }
    $db = reconnect_ro();
    $ret = 0;
    $q = "SELECT denom from mt_produk where id_produk = $1 ";
    $bind = array();
    $bind[]=$id_produk;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->denom;
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}

function getRef2($ref,$idoutlet){
    $db = reconnect_ro();
    $ret = '';
    $q = "SELECT id_transaksi from transaksi where jenis_transaksi=1 and bill_info83=$1 and id_outlet=$2 limit 1";
    $bind = array();
    $bind[]=$ref;
    $bind[]=$idoutlet;

    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->id_transaksi;
    }else{
        $ret = getRef2Backup($ref,$idoutlet);
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}

function getRef2Backup($ref,$idoutlet){
   
    $db = reconnect_ro();
    $ret = '';
    $q = "SELECT id_transaksi from transaksi_backup where jenis_transaksi=1 and bill_info83=$1 and id_outlet=$2 limit 1";
    $bind = array();
    $bind[]=$ref;
    $bind[]=$idoutlet;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->id_transaksi;
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}

function getnamaproduk($id_produk){
    if ($id_produk == ""){
        return 0;
    }
    $db = reconnect_ro();
    $ret = 0;
    $q = "SELECT produk from mt_produk where id_produk = $1 ";
    $bind = array();
    $bind[]=$id_produk;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ret = $r->produk;
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}

function isvaliddaterange($tgl1, $tgl2){

    if((strlen($tgl1) != 14) || (strlen($tgl2) != 14)) return FALSE;
    $db = reconnect();
    $q = "select CASE WHEN SUM(EXTRACT(EPOCH FROM to_timestamp($1,'YYYYMMDDHH24MISS')) - EXTRACT(EPOCH FROM to_timestamp($2,'YYYYMMDDHH24MISS'))) >= 0 THEN TRUE ELSE FALSE END as seconds";
    $bind = array();
    $bind[] = $tgl2;
    $bind[] = $tgl1;
    $e = pg_query_params($db,$q,$bind);
    while ($data = pg_fetch_object($e)) {
       $out = $data->seconds;
    }
    pg_free_result($e);
    pg_close($db);
    return $out == "t" ? TRUE : FALSE;
}

function getIdOutlet($hp){
    $db = reconnect_ro();
    $nohp = trim($hp);
    if(substr($nohp,0,2)=="62"){
            $nohp = "0".substr($nohp,2,strlen($nohp));
    }else if(substr($nohp,0,3)=="+62"){
            $nohp = "0".substr($nohp,3,strlen($nohp));
    }

    $q = "select id_outlet FROM fmss.mt_outlet WHERE notelp_pemilik = $1 LIMIT 1";
    $bind = array();
    $bind[] = $nohp;
    $e = pg_query_params($db,$q,$bind);
    $r = pg_fetch_object($e);
    pg_free_result($e);
    pg_close($db);
    return $r->id_outlet;
}

function getPaymentKAI($idoutlet, $idtrx, $idpel = ""){
    $db = reconnect();
    $q = "
SELECT t.id_transaksi,t.bill_info2, t.response_code, t.keterangan
FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='".$idoutlet."' AND t.id_produk='WKAI' AND response_code = '00' " ;
    
    $idtrx != "" ? $q .= " AND t.id_transaksi='".$idtrx."'" : $q .=" AND t.bill_info2='".$idpel."'";
    
    $q .= " UNION ";
    
    $q .= "
SELECT t.id_transaksi,t.bill_info2, t.response_code, t.keterangan
FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.jenis_transaksi = 1
AND t.id_outlet='".$idoutlet."' AND t.id_produk='WKAI' AND response_code = '00' " ;
    
    $idtrx != "" ? $q .= " AND t.id_transaksi='".$idtrx."'" : $q .= " AND t.bill_info2='".$idpel."'";
    $q = $q . " ORDER BY id_transaksi DESC ";
    
    $e = pg_query($db,$q);
    $n = pg_num_rows($e);
    
    $out = array();
    $i = 0;
    if ($n > 0) {
        while ($data = pg_fetch_object($e)) {
            $out[$i] = $data;
            $i++;
        }
    }else{
                    $qs = "
            SELECT t.id_transaksi,t.bill_info2, t.response_code, t.keterangan
            FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
            WHERE t.jenis_transaksi = 1 
            AND t.id_outlet='".$idoutlet."' AND t.id_produk='WKAI'" ;

                $idtrx != "" ? $qs .= " AND t.id_transaksi='".$idtrx."'" : $qs .=" AND t.bill_info2='".$idpel."'";

                $qs .= " UNION ";

                $qs .= "
            SELECT t.id_transaksi,t.bill_info2, t.response_code, t.keterangan
            FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
            WHERE t.jenis_transaksi = 1
            AND t.id_outlet='".$idoutlet."' AND t.id_produk='WKAI'" ;

                $idtrx != "" ? $qs .= " AND t.id_transaksi='".$idtrx."'" : $qs .= " AND t.bill_info2='".$idpel."'";
                $qs = $qs . " ORDER BY id_transaksi DESC LIMIT 1";

                $ex = pg_query($db,$qs);
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
    pg_close($db);
    return $out;
}

function datatransaksisaatini($idproduk = "", $idpel = "", $custreff= "")
{
    $q = "";
    if ($idproduk !="" && $idpel == "" && $custreff == "") {
        $q .= " AND t.id_produk=$3";
    }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
        $q .= " AND (t.bill_info1=$3 or t.bill_info2=$3 )";
    }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.bill_info83=$3";
    }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
        $q .= " AND t.id_produk=$3";
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4)";
    }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.id_produk=$3";
        $q .= " AND t.bill_info83=$4";
    }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
        $q .= " AND (t.bill_info1=$3 or t.bill_info2=$3)";
        $q .= " AND t.bill_info83=$4";
    }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
        $q .= " AND (t.bill_info1=$3 or t.bill_info2=$3)";
        $q .= " AND t.id_produk=$4";
        $q .= " AND t.bill_info83=$5";
    }
    return $q;
}

function datatransaksisaatiniv1($idproduk = "", $idpel = "", $custreff= "")
{
    $q = "";
    if ($idproduk !="" && $idpel == "" && $custreff == "") {
        $q .= " AND t.id_produk=$4";
    }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4 )";
    }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.bill_info83=$4";
    }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
        $q .= " AND t.id_produk=$4";
        $q .= " AND (t.bill_info1=$5 or t.bill_info2=$5)";
    }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.id_produk=$4";
        $q .= " AND t.bill_info83=$5";
    }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4)";
        $q .= " AND t.bill_info83=$5";
    }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4)";
        $q .= " AND t.id_produk=$5";
        $q .= " AND t.bill_info83=$6";
    }
    return $q;
}

function getDataProsesTransaksi($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= "")
{
    $db = reconnect_ro();
    $bind= array();
    $tgl1 = date('Y-m-d',strtotime($tgl1));
    $tgl2 = date('Y-m-d',strtotime($tgl2));

    if(empty($idtrx)){
        $bind[] = $tgl1;
        $temp = "t.transaction_date in($1)";
        $idtrx = "";
    }else{
        $bind[] = $idtrx;
        $temp ="t.id_transaksi=$1";
    }
    $bind[] = $idoutlet;
    $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, REGEXP_REPLACE(t.keterangan, '([^[SYSTEM][A-Z a-z 0-9]+])', 'admin]') AS keterangan
FROM proses_transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE ".$temp."
AND t.id_outlet=$2 and  t.jenis_transaksi = 1";
    $q .= datatransaksisaatini($idproduk, $idpel , $custreff);

    if ($idproduk !="" && $idpel == "" && $custreff == "") {
        $bind[] = $idproduk;
    }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
        $bind[] = $idpel;
    }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
        $bind[] = $custreff;
    }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
        $bind[] = $idproduk;
        $bind[] = $idpel;
    }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
        $bind[] = $idproduk;
        $bind[] = $custreff;
    }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
        $bind[] = $idpel;
        $bind[] = $custreff;
    }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
        $bind[] = $idpel;
        $bind[] = $idproduk;
        $bind[] = $custreff;
    }
    
    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;
    $e = pg_query_params($db,$q,$bind);
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
    pg_close($db);
    return $out;
}

function getDataTransaksi($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= ""){

    $db = reconnect_ro();
    $bind= array();
    $tgl1 = date('Y-m-d',strtotime($tgl1));
    $tgl2 = date('Y-m-d',strtotime($tgl2));

    if(empty($idtrx)){
        $bind[] = $tgl1;
        $temp = "t.transaction_date in($1)";
        $idtrx = "";
    }else{
        $bind[] = $idtrx;
        $temp ="t.id_transaksi=$1";
    }
    $bind[] = $idoutlet;
    $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, t.keterangan AS keterangan
        FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
        WHERE ".$temp."
        AND t.id_outlet=$2 and  t.jenis_transaksi = 1";
    $q .= datatransaksisaatini($idproduk, $idpel , $custreff);


    if ($idproduk !="" && $idpel == "" && $custreff == "") {
        $bind[] = $idproduk;
    }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
        $bind[] = $idpel;
    }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
        $bind[] = $custreff;
    }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
        $bind[] = $idproduk;
        $bind[] = $idpel;
    }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
        $bind[] = $idproduk;
        $bind[] = $custreff;
    }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
        $bind[] = $idpel;
        $bind[] = $custreff;
    }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
        $bind[] = $idpel;
        $bind[] = $idproduk;
        $bind[] = $custreff;
    }
    
    
	$q = $q . " ORDER BY t.id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;

    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);

    if($n > 0)
    {
        $out = array();
        $i = 0;
        if ($n > 0) {
            while ($data = pg_fetch_object($e)) {
                $out[$i] = $data;
                $i++;
            }
        }
    }else{

            $out = getDataTransaksiBackup1($idoutlet, $idtrx, $idproduk, $idpel, $limit, $tgl1, $tgl2, $custreff);
       
    }
    
    pg_free_result($e);
    pg_close($db);
    return $out;
}

function getDataTransaksi1($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= "")
{
    $db = reconnect_ro();
    $bind= array();
    $tgl1 = date('Y-m-d',strtotime($tgl1));
    $tgl2 = date('Y-m-d',strtotime($tgl2));

    if(empty($idtrx)){
        $bind[] = $tgl1;
        $temp = "t.transaction_date in($1)";
        $idtrx = "";
    }else{
        $bind[] = $idtrx;
        $temp ="t.id_transaksi=$1";
    }
    $bind[] = $idoutlet;

    $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, t.keterangan AS keterangan
FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE ".$temp."
AND t.id_outlet=$1 and  t.jenis_transaksi = 1";
    $q .= datatransaksisaatini($idproduk, $idpel , $custreff);


    if ($idproduk !="" && $idpel == "" && $custreff == "") {
        $bind[] = $idproduk;
    }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
        $bind[] = $idpel;
    }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
        $bind[] = $custreff;
    }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
        $bind[] = $idproduk;
        $bind[] = $idpel;
    }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
        $bind[] = $idproduk;
        $bind[] = $custreff;
    }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
        $bind[] = $idpel;
        $bind[] = $custreff;
    }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
        $bind[] = $idpel;
        $bind[] = $idproduk;
        $bind[] = $custreff;
    }


    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;

    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);

    if($n > 0)
    {
        $out = array();
        $i = 0;
        if ($n > 0) {
            while ($data = pg_fetch_object($e)) {
                $out[$i] = $data;
                $i++;
            }
        }
    }

    pg_free_result($e);
    pg_close($db);
    return $out;
}

function getDataTransaksiBackup1($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= "")
{
    $db = reconnect_ro();
    $bind= array();
    $tgl1 = date('Y-m-d',strtotime($tgl1));
    $tgl2 = date('Y-m-d',strtotime($tgl2));

    if(empty($idtrx)){
        $bind[] = $tgl1;
        $temp = "t.transaction_date in($1)";
        $idtrx = "";
    }else{
        $bind[] = $idtrx;
        $temp ="t.id_transaksi=$1";
    }
    $bind[] = $idoutlet;

    $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, t.keterangan AS keterangan
FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE ".$temp."
AND t.id_outlet=$2 and  t.jenis_transaksi = 1";

    $q .= datatransaksisaatini($idproduk, $idpel , $custreff);
    if ($idproduk !="" && $idpel == "" && $custreff == "") {
        $bind[] = $idproduk;
    }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
        $bind[] = $idpel;
    }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
        $bind[] = $custreff;
    }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
        $bind[] = $idproduk;
        $bind[] = $idpel;
    }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
        $bind[] = $idproduk;
        $bind[] = $custreff;
    }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
        $bind[] = $idpel;
        $bind[] = $custreff;
    }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
        $bind[] = $idpel;
        $bind[] = $idproduk;
        $bind[] = $custreff;
    }


    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;
  
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);

    if($n > 0)
    {
        $out = array();
        $i = 0;
        if ($n > 0) {
            while ($data = pg_fetch_object($e)) {
                $out[$i] = $data;
                $i++;
            }
        }
    }

    pg_free_result($e);
    pg_close($db);
    return $out;
}

function cekDataProses($idoutlet, $idproduk, $idpel1,$custreff){
    $limit = 0;
    $db = reconnect();
    $bind= array();
    $bind[] = $idoutlet;
    $bind[] = $idproduk;
    $bind[] = $idpel1;
    if($custreff != ""){
        $bind[] = $custreff;
        $tambahan = " AND t.bill_info83=$4 ";
    }else{
        $tambahan = "";
    }
     $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, REGEXP_REPLACE(t.keterangan, '([^[SYSTEM][A-Z a-z 0-9]+])', 'admin]') AS keterangan
            FROM proses_transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
            WHERE t.jenis_transaksi = 1
            AND t.id_outlet=$1 AND t.id_produk=$2 AND t.bill_info1=$3 ".$tambahan;
    
    $q = $q." ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;

    $e = pg_query_params($db,$q,$bind);
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
    pg_close($db);
    return $out;
}

function cekDataTransaksi($idoutlet, $idproduk, $idpel1,$custreff){
    $limit = 0;
    $db = reconnect();
    $bind= array();
    $bind[] = $idoutlet;
    $bind[] = $idproduk;
    $bind[] = $idpel1;
   
    if($custreff != ""){
        $bind[] = $custreff;
        $tambahan = " AND t.bill_info83=$4 ";
    }else{
        $tambahan = "";
    }
     $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, REGEXP_REPLACE(t.keterangan, '([^[SYSTEM][A-Z a-z 0-9]+])', 'admin]') AS keterangan
            FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
            WHERE t.jenis_transaksi = 1
            AND t.id_outlet=$1 AND t.id_produk=$2 AND t.bill_info1=$3 ".$tambahan;
    
    $q = $q." ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;


    $e = pg_query_params($db,$q,$bind);
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
    pg_close($db);
    return $out;
}



function getDataTransaksi_devel($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= ""){

}

function reconnect($conn = null) {
    return pg_connect($GLOBALS["__G_conn_prop"]);        
}

function reconnect_fmss($conn = null) {
    return pg_connect($GLOBALS["__G_conn_prop_log"]);        
}

function reconnect_D($conn = null) {
    return pg_connect($GLOBALS["__G_conn_devel"]);        
}

function reconnect_ro($conn = null) {
    return pg_connect($GLOBALS["__G_conn_prop_RO"]);        
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

function retPLNPostpaid($params, $frm) {
    array_push($params, (string) $frm->getSwitcherId());
    array_push($params, (string) $frm->getSubscriberId());
    array_push($params, (string) $frm->getJumlahBill());
    array_push($params, (string) $frm->getPaymentStatus());
    array_push($params, (string) $frm->getTotalOutstandingBill());
    array_push($params, (string) $frm->getSwReferenceNumber());
    array_push($params, (string) trim($frm->getNamaPelanggan()));
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
    array_push($params, (string) $frm->getAlamat());
    array_push($params, (string) $frm->getPlnNPWP());
    array_push($params, (string) $frm->getSubscriberNPWP());
    array_push($params, (string) $frm->getTotalRpTag());
    array_push($params, (string) $frm->getInfoTeks());
	//print_r($params);
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
    array_push($params, (string) $frm->getNamaPelanggan());
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
    array_push($params, (string) $frm->getInfoText());

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
    array_push($params, (string) $frm->getSaldoRet());
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

function retPAM($params, $frm) {
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    array_push($params, (string) trim($frm->getCustomerID1()));
    array_push($params, (string) trim($frm->getCustomerID2()));
    array_push($params, (string) trim($frm->getCustomerID3()));
    array_push($params, (string) trim($frm->getBillQuantity()));
    array_push($params, (string) trim($frm->getNoref1()));
    array_push($params, (string) trim($frm->getNoref2()));
    array_push($params, (string) trim($frm->getCustomerName()));
    array_push($params, (string) trim($frm->getCustomerAddress()));
    array_push($params, (string) trim($frm->getCustomerDetailInformation()));
    array_push($params, (string) trim($frm->getBillerAdminCharge()));
    array_push($params, (string) trim($frm->getTotalBillAmount()));
    array_push($params, (string) trim($frm->getPDAMName()));
    array_push($params, (string) trim($frm->getMonthPeriod1()));
    array_push($params, (string) trim($frm->getYearPeriod1()));
    array_push($params, (string) trim($frm->getFirstMeterRead1()));
    array_push($params, (string) trim($frm->getLastMeterRead1()));
    array_push($params, (string) trim($frm->getPenalty1()));
    array_push($params, (string) trim($frm->getBillAmount1()));
    array_push($params, (string) trim($frm->getMiscAmount1()));
    array_push($params, (string) trim($frm->getMonthPeriod2()));
    array_push($params, (string) trim($frm->getYearPeriod2()));
    array_push($params, (string) trim($frm->getFirstMeterRead2()));
    array_push($params, (string) trim($frm->getLastMeterRead2()));
    array_push($params, (string) trim($frm->getPenalty2()));
    array_push($params, (string) trim($frm->getBillAmount2()));
    array_push($params, (string) trim($frm->getMiscAmount2()));
    array_push($params, (string) trim($frm->getMonthPeriod3()));
    array_push($params, (string) trim($frm->getYearPeriod3()));
    array_push($params, (string) trim($frm->getFirstMeterRead3()));
    array_push($params, (string) trim($frm->getLastMeterRead3()));
    array_push($params, (string) trim($frm->getPenalty3()));
    array_push($params, (string) trim($frm->getBillAmount3()));
    array_push($params, (string) trim($frm->getMiscAmount3()));
    array_push($params, (string) trim($frm->getMonthPeriod4()));
    array_push($params, (string) trim($frm->getYearPeriod4()));
    array_push($params, (string) trim($frm->getFirstMeterRead4()));
    array_push($params, (string) trim($frm->getLastMeterRead4()));
    array_push($params, (string) trim($frm->getPenalty4()));
    array_push($params, (string) trim($frm->getBillAmount4()));
    array_push($params, (string) trim($frm->getMiscAmount4()));
    array_push($params, (string) trim($frm->getMonthPeriod5()));
    array_push($params, (string) trim($frm->getYearPeriod5()));
    array_push($params, (string) trim($frm->getFirstMeterRead5()));
    array_push($params, (string) trim($frm->getLastMeterRead5()));
    array_push($params, (string) trim($frm->getPenalty5()));
    array_push($params, (string) trim($frm->getBillAmount5()));
    array_push($params, (string) trim($frm->getMiscAmount5()));
    array_push($params, (string) trim($frm->getMonthPeriod6()));
    array_push($params, (string) trim($frm->getYearPeriod6()));
    array_push($params, (string) trim($frm->getFirstMeterRead6()));
    array_push($params, (string) trim($frm->getLastMeterRead6()));
    array_push($params, (string) trim($frm->getPenalty6()));
    array_push($params, (string) trim($frm->getBillAmount6()));
    array_push($params, (string) trim($frm->getMiscAmount6()));

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
    array_push($params, (string) trim($frm->getODPenaltyFee()));
    array_push($params, (string) trim($frm->getBillerAdminFee()));
    array_push($params, (string) trim($frm->getMiscFee()));
    array_push($params, (string) trim($frm->getMinimumPayAmount()));
    array_push($params, (string) trim($frm->getMaximumPayAmount()));

    return $params;
}

function retAsuransi($params, $frm) {
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    array_push($params, (string) trim($frm->getCustomerID()));
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
    array_push($params, (string) trim($frm->getClaimAmount()));
    array_push($params, (string) trim($frm->getBillerRefNumber()));
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

    return $params;
}

function retkai($params, $frm){
    array_push($params, (string) trim($frm->getERRCODE()));
    array_push($params, (string) trim($frm->getERRMSG()));
    array_push($params, (string) trim($frm->getORG()));
    array_push($params, (string) trim($frm->getDES()));
    array_push($params, (string) trim($frm->getDEPDATE()));
    array_push($params, (string) trim($frm->getARVDATE()));
    array_push($params, (string) trim($frm->getSCHEDULE()));
    array_push($params, (string) trim($frm->getTRAINNO()));
    array_push($params, (string) trim($frm->getCLASS()));
    array_push($params, (string) trim($frm->getSUBCLASS()));
    array_push($params, (string) trim($frm->getNUMPAXADULT()));
    array_push($params, (string) trim($frm->getNUMPAXCHILD()));
    array_push($params, (string) trim($frm->getNUMPAXINFANT()));
    array_push($params, (string) trim($frm->getADULTNAME1()));
    array_push($params, (string) trim($frm->getADULTBIRTHDATE1()));
    array_push($params, (string) trim($frm->getADULTMOBILE1()));
    array_push($params, (string) trim($frm->getADULTIDNO1()));
    array_push($params, (string) trim($frm->getADULTNAME2()));
    array_push($params, (string) trim($frm->getADULTBIRTHDATE2()));
    array_push($params, (string) trim($frm->getADULTMOBILE2()));
    array_push($params, (string) trim($frm->getADULTIDNO2()));
    array_push($params, (string) trim($frm->getADULTNAME3()));
    array_push($params, (string) trim($frm->getADULTBIRTHDATE3()));
    array_push($params, (string) trim($frm->getADULTMOBILE3()));
    array_push($params, (string) trim($frm->getADULTIDNO3()));
    array_push($params, (string) trim($frm->getADULTNAME4()));
    array_push($params, (string) trim($frm->getADULTBIRTHDATE4()));
    array_push($params, (string) trim($frm->getADULTMOBILE4()));
    array_push($params, (string) trim($frm->getADULTIDNO4()));
    array_push($params, (string) trim($frm->getCHILDNAME1()));
    array_push($params, (string) trim($frm->getCHILDBIRTHDATE1()));
    array_push($params, (string) trim($frm->getCHILDNAME2()));
    array_push($params, (string) trim($frm->getCHILDBIRTHDATE2()));
    array_push($params, (string) trim($frm->getCHILDNAME3()));
    array_push($params, (string) trim($frm->getCHILDBIRTHDATE3()));
    array_push($params, (string) trim($frm->getCHILDNAME4()));
    array_push($params, (string) trim($frm->getCHILDBIRTHDATE4()));
    array_push($params, (string) trim($frm->getINFANTNAME1()));
    array_push($params, (string) trim($frm->getINFANTBIRTHDATE1()));
    array_push($params, (string) trim($frm->getINFANTNAME2()));
    array_push($params, (string) trim($frm->getINFANTBIRTHDATE2()));
    array_push($params, (string) trim($frm->getINFANTNAME3()));
    array_push($params, (string) trim($frm->getINFANTBIRTHDATE3()));
    array_push($params, (string) trim($frm->getINFANTNAME4()));
    array_push($params, (string) trim($frm->getINFANTBIRTHDATE4()));
    array_push($params, (string) trim($frm->getCALLER()));
    array_push($params, (string) trim($frm->getNUMCODE()));
    array_push($params, (string) trim($frm->getBOOKCODE()));
    array_push($params, (string) trim($frm->getSEAT()));
    array_push($params, (string) trim($frm->getNORMALSALES()));
    array_push($params, (string) trim($frm->getEXTRAFEE()));
    array_push($params, (string) trim($frm->getBOOKBALANCE()));
    array_push($params, (string) trim($frm->getSEATMAPNULL()));
    array_push($params, (string) trim($frm->getWAGONCODE()));
    array_push($params, (string) trim($frm->getWAGONNO()));
    array_push($params, (string) trim($frm->getWAGONCODE1()));
    array_push($params, (string) trim($frm->getWAGONNO1()));
    array_push($params, (string) trim($frm->getSEATROW1()));
    array_push($params, (string) trim($frm->getSEATCOL1()));
    array_push($params, (string) trim($frm->getWAGONCODE2()));
    array_push($params, (string) trim($frm->getWAGONNO2()));
    array_push($params, (string) trim($frm->getSEATROW2()));
    array_push($params, (string) trim($frm->getSEATCOL2()));
    array_push($params, (string) trim($frm->getWAGONCODE3()));
    array_push($params, (string) trim($frm->getWAGONNO3()));
    array_push($params, (string) trim($frm->getSEATROW3()));
    array_push($params, (string) trim($frm->getSEATCOL3()));
    array_push($params, (string) trim($frm->getWAGONCODE4()));
    array_push($params, (string) trim($frm->getWAGONNO4()));
    array_push($params, (string) trim($frm->getSEATROW4()));
    array_push($params, (string) trim($frm->getSEATCOL4()));
    array_push($params, (string) trim($frm->getCANCELREASON()));
    array_push($params, (string) trim($frm->getSTATUSCANCEL()));
    array_push($params, (string) trim($frm->getREFUND()));
    array_push($params, (string) trim($frm->getPAYTYPE()));
    array_push($params, (string) trim($frm->getROUTE()));
    array_push($params, (string) trim($frm->getPAX()));
    array_push($params, (string) trim($frm->getPAXNUM()));
    array_push($params, (string) trim($frm->getREVENUE()));
    array_push($params, (string) trim($frm->getTRAINNAME()));
    array_push($params, (string) trim($frm->getORIGINATION()));
    array_push($params, (string) trim($frm->getDEPTIME()));
    array_push($params, (string) trim($frm->getDESTINATION()));
    array_push($params, (string) trim($frm->getARVTIME()));
    array_push($params, (string) trim($frm->getSEATNUMBER()));
    array_push($params, (string) trim($frm->getPRICEADULT()));
    array_push($params, (string) trim($frm->getPRICECHILD()));
    array_push($params, (string) trim($frm->getPRICEINFANT()));

    return $params;
}

function retKartuKredit($params, $frm) {
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getBillerCode()));
    array_push($params, (string) trim($frm->getCustomerID()));
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
    array_push($params, (string) trim($frm->getLastPaidPeriode()));
    array_push($params, (string) trim($frm->getLastPaidDueDate()));
    array_push($params, (string) trim($frm->getBillerAdminFee()));
    array_push($params, (string) trim($frm->getMiscFee()));
    array_push($params, (string) trim($frm->getMiscNumber()));
    array_push($params, (string) trim($frm->getMinimumPayAmount()));
    array_push($params, (string) trim($frm->getMaximumPayAmount()));

    return $params;
}

function retNewPAM($params, $frm) {
    array_push($params, (string) trim($frm->getSwitcherId()));
    array_push($params, (string) trim($frm->getCustomerId1()));
    array_push($params, (string) trim($frm->getCustomerId2()));
    array_push($params, (string) trim($frm->getCustomerId3()));
    array_push($params, (string) trim($frm->getBillQuantity()));
    array_push($params, (string) trim($frm->getGwRefnum()));
    array_push($params, (string) trim($frm->getSwRefnum()));
    array_push($params, (string) trim($frm->getCustomerName()));
    array_push($params, (string) trim($frm->getCustomerAddress()));
    array_push($params, (string) trim($frm->getCustomerSegmentation()));
    array_push($params, (string) trim($frm->getCustomerDetailInformation()));
    array_push($params, (string) trim($frm->getBillerAdminCharge()));
    array_push($params, (string) trim($frm->getTotalBillAmount()));
    array_push($params, (string) trim($frm->getPdamName()));
    array_push($params, (string) trim($frm->getStampDuty()));
    array_push($params, (string) trim($frm->getTransactionFee()));
    array_push($params, (string) trim($frm->getOtherFee()));
    array_push($params, (string) trim($frm->getMonthPeriod1()));
    array_push($params, (string) trim($frm->getYearPeriod1()));
    array_push($params, (string) trim($frm->getMeterUsage1()));
    array_push($params, (string) trim($frm->getStand1()));
    array_push($params, (string) trim($frm->getFirstMeterRead1()));
    array_push($params, (string) trim($frm->getLastMeterRead1()));
    array_push($params, (string) trim($frm->getBillAmount1()));
    array_push($params, (string) trim($frm->getPenalty1()));
    array_push($params, (string) trim($frm->getBurdenAmount1()));
    array_push($params, (string) trim($frm->getMiscAmount1()));
    array_push($params, (string) trim($frm->getMonthPeriod2()));
    array_push($params, (string) trim($frm->getYearPeriod2()));
    array_push($params, (string) trim($frm->getMeterUsage2()));
    array_push($params, (string) trim($frm->getStand2()));
    array_push($params, (string) trim($frm->getFirstMeterRead2()));
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

function retLakupandai($params, $frm)
{
     array_push($params, (string) trim($frm->getCmd()));
     array_push($params, (string) trim($frm->getDealerId()));
     array_push($params, (string) trim($frm->getSystemId()));
     array_push($params, (string) trim($frm->getAccountNum()));
     array_push($params, (string) trim($frm->getOption()));
     array_push($params, (string) trim($frm->getIdAgen()));
     array_push($params, (string) trim($frm->getCurrency()));
     array_push($params, (string) trim($frm->getAccountStatus()));
     array_push($params, (string) trim($frm->getProduct()));
     array_push($params, (string) trim($frm->getHomeBranch()));
     array_push($params, (string) trim($frm->getCifNum()));
     array_push($params, (string) trim($frm->getName()));
     array_push($params, (string) trim($frm->getNameRek()));
     array_push($params, (string) trim($frm->getCurrentBalance()));
     array_push($params, (string) trim($frm->getAvailableBalance()));
     array_push($params, (string) trim($frm->getOpenDate()));
     array_push($params, (string) trim($frm->getAddress_Street()));
     array_push($params, (string) trim($frm->getAddress_Rt()));
     array_push($params, (string) trim($frm->getAddress1()));
     array_push($params, (string) trim($frm->getAddress2()));
     array_push($params, (string) trim($frm->getAddress3()));
     array_push($params, (string) trim($frm->getAddress4()));
     array_push($params, (string) trim($frm->getPostCode()));
     array_push($params, (string) trim($frm->getHomePhone()));
     array_push($params, (string) trim($frm->getFax()));
     array_push($params, (string) trim($frm->getOfficePhone()));
     array_push($params, (string) trim($frm->getMobilePhone()));
     array_push($params, (string) trim($frm->getAddress1AA()));
     array_push($params, (string) trim($frm->getAddress2AA()));
     array_push($params, (string) trim($frm->getAddress3AA()));
     array_push($params, (string) trim($frm->getAddress4AA()));
     array_push($params, (string) trim($frm->getPostCodeA()));
     array_push($params, (string) trim($frm->getAccountProductType()));
     array_push($params, (string) trim($frm->getAccType()));
     array_push($params, (string) trim($frm->getSubCat()));
     array_push($params, (string) trim($frm->getAvailableInterest()));
     array_push($params, (string) trim($frm->getLienbalance()));
     array_push($params, (string) trim($frm->getUnclearbalance()));
     array_push($params, (string) trim($frm->getInterestrate()));
     array_push($params, (string) trim($frm->getKtp()));
     array_push($params, (string) trim($frm->getNpwp()));
     array_push($params, (string) trim($frm->getJenis_Pekerjaan()));
     array_push($params, (string) trim($frm->getEmail()));
     array_push($params, (string) trim($frm->getKode_Wil_Bi()));
     array_push($params, (string) trim($frm->getKode_Cabang()));
     array_push($params, (string) trim($frm->getKode_Loket()));
     array_push($params, (string) trim($frm->getKode_Mitra()));
     array_push($params, (string) trim($frm->getTgl_Input()));
     array_push($params, (string) trim($frm->getCa_Gen_Status()));
     array_push($params, (string) trim($frm->getClientId()));
     array_push($params, (string) trim($frm->getClient_Account_Num()));
     array_push($params, (string) trim($frm->getReq_Id()));
     array_push($params, (string) trim($frm->getReq_Time()));
     array_push($params, (string) trim($frm->getCust_Acc_Num()));
     array_push($params, (string) trim($frm->getAmount()));
     array_push($params, (string) trim($frm->getTransaction_Journal()));
     array_push($params, (string) trim($frm->getCustomer_Otp()));
     array_push($params, (string) trim($frm->getCust_First_Name()));
     array_push($params, (string) trim($frm->getCust_Midle_Name()));
     array_push($params, (string) trim($frm->getCust_Last_Name()));
     array_push($params, (string) trim($frm->getCust_Place_Of_Birth()));
     array_push($params, (string) trim($frm->getCust_Date_Of_Birth()));
     array_push($params, (string) trim($frm->getCust_Gender()));
     array_push($params, (string) trim($frm->getCust_Is_Married()));
     array_push($params, (string) trim($frm->getCust_Income()));
     array_push($params, (string) trim($frm->getPin_Transaksi()));
     array_push($params, (string) trim($frm->getImage_Name()));
     array_push($params, (string) trim($frm->getImage_Url()));
     array_push($params, (string) trim($frm->getFile_Name()));
     array_push($params, (string) trim($frm->getImage_Foto_Name()));
     array_push($params, (string) trim($frm->getImage_Foto_Url()));
     array_push($params, (string) trim($frm->getFile_Name_Foto()));

     return $params;
}

function getStatusMid($p_mid){
    $ret = false;
    $db = reconnect_ro();
    $qn = "SELECT mid FROM fmss.message WHERE mid = $1 AND step = 3 UNION SELECT mid FROM fmss.message_final WHERE mid = $2 AND step = 3 LIMIT 1";
    $bind = array();
    $bind[] = $p_mid;
    $bind[] = $p_mid;
    $eqn = pg_query_params($db, $qn, $bind);
    echo $qn."\r\n";
    echo "eqn = ".$eqn."\r\n";
    if(pg_num_rows($eqn) > 0){
        $ret = true;
    }
    return $ret;
}

function getRespInq($p_id_transaksi){
    $db = new Database();
    $q_sel_log = "SELECT content FROM fmss_message WHERE id_transaksi = ".$p_id_transaksi." LIMIT 1";
    $e_sel_log = mysql_query($q_sel_log, $db->getConnection());
    $r_sel_log = mysql_fetch_object($e_sel_log);
    $db->dbClose();
    return $r_sel_log->content;
}


function insertSuksesPaksaMy($mid){
    $db = new Database();
    $q_ins_paksa = "INSERT INTO db_pc.edc_sukses_paksa (mid, insert_date, insert_time) VALUES (".$mid.", CURDATE(), CURTIME())";
    $e_ins_paksa = mysql_query($q_ins_paksa, $db->getConnection());
    $db->dbClose();
}

function insertSuksesPaksaPg($mid){
    $qn = "INSERT INTO fmss.edc_sukses_paksa (mid, insert_date, insert_time) VALUES (".$mid.", current_date, localtime)";
    write_master($qn);
}

function cekIsProsesTransaksi($id_transaksi){
    $ret = false;
    $db = reconnect();
    $qn = "select id_transaksi FROM fmss.proses_transaksi WHERE id_transaksi = $1";
    $bind = array();
    $bind[] = $id_transaksi;
    $eqn = pg_query_params($db, $qn,$bind);
    echo $qn."\r\n";
    echo "eqn = ".$eqn."\r\n";
    if(pg_numrows($eqn) > 0){
        $ret = true;
    }
    return $ret;
}

function insertLogRc77($haproxy_time,$apache_time,$selisih,$id_outlet,$format_message,$method_name,$ip_address,$id_server,$host_server)
{
    
    $query = "INSERT INTO fmss.h2h_log_rc77 (time_request,selisih,id_outlet,format_message,method_name,ip_address,ip_server,host_server,haproxy_time, apache_time) VALUES (current_timestamp,'".$selisih."', '".$id_outlet."', '".$format_message."', '".$method_name."', '".$ip_address."', '".$id_server."', '".$host_server."','".$haproxy_time."','".$apache_time."')";
    write_master($query);
}

function replace_forbidden_chars($text){
	$arrSearch = array("'", "*", "\r", "\n", "\t");
	$arrReplace = array("`", "-", " ", " ", " ");
	return str_replace($arrSearch, $arrReplace, $text);
}

function replace_forbidden_chars_msg($text){
	$arrSearch = array("'", "\r", "\n", "\t");
	$arrReplace = array("`", " ", " ", " ");
	return str_replace($arrSearch, $arrReplace, $text);
}

function setMandatoryRespon($frm,$ref1,$ref2,$ref3,$url_struk){
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
	
	$params = array(
                (string)$r_kdproduk, (string)$r_tanggal, (string)$r_idpel1, (string)$r_idpel2, (string)$r_idpel3, (string)$r_nama_pelanggan, (string)$r_periode_tagihan, (string)$r_nominal, (string)$r_nominaladmin, (string)$r_idoutlet, (string)$r_pin, (string)$ref1, (string)$r_idtrx, (string)$ref3, (string)$r_status, (string)$r_keterangan, (string)$r_saldo_terpotong, (string)$r_sisa_saldo, (string)$url_struk
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
    }else if (substr($kdproduk, 0, 2) == 'EM') {
        $format = FormatMsg::opendenom();
        $frm = new FormatOpenDenom($man["inq"] . "*" . $format, $resp);
        // $params = retPKB($p_params, $frm);
    }
//    else if($kdproduk == 'PKAI'){
//        $format     = FormatMsg::kai();
//        $frm        = new FormatKai($man["pay"]."*".$format['NKAIBOKINF'], $resp);
//    }
	
	return $frm;
}

function getKodeBank($kdproduk, $frm){
    $nama = "";
    $man = FormatMsg::mandatoryPayment();
    if(substr($kdproduk, 0, 5) == 'BLTRF' || $kdproduk == 'BLTRFMDR'){
        $nama = trim($frm->getAccType());
    }
    return $nama;
}

function getIdPelanggan($kdproduk, $frm){
    $nama = "";
    $man = FormatMsg::mandatoryPayment();
    if(substr($kdproduk, 0, 5) == 'BLTRF' || $kdproduk == 'BLTRFMDR'){
        $nama = trim($frm->getCust_Acc_Num());
    }
    return $nama;
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
        if($kdproduk == "BLTRFBCA" || $kdproduk == "BLTRFBRI"){
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
function getGlobal($idtrx,$field)
{
    $db = reconnect_ro();
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
function getBillDenda($kdproduk , $frm)
{
    $denda = "";
    $isi = array();
     if($frm->getMonthPeriod1() != ""){
        $denda1 = $frm->getPenalty1();
     }

     if ($frm->getMonthPeriod2() != "") {
         $denda2 = $frm->getPenalty2();
     }

     if ($frm->getMonthPeriod3() != "") {
         $denda3 = $frm->getPenalty3();
     }

     if ($frm->getMonthPeriod4() != "") {
         $denda4 = $frm->getPenalty4();
     }

     if ($frm->getMonthPeriod5() != "") {
         $denda5 = $frm->getPenalty5();
     }

     if ($frm->getMonthPeriod6() != "") {
         $denda6 = $frm->getPenalty6();
     }

     $isi = array($denda1,$denda2,$denda3,$denda4,$denda5,$denda6);

     return $isi;
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
		$bill = array (	trim($frm->getBillPeriod1()),
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
		$bill = array (	trim($frm->getYearPeriod1())."".trim($frm->getMonthPeriod1()),
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
		$bill = array (	trim($frm->getYearPeriod1())."".trim($bln1),
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
        $bill = array (	trim($frm->getYEARPERIOD1())."".trim($frm->getMONTHPERIOD1()),
						trim($frm->getYEARPERIOD2())."".trim($frm->getMONTHPERIOD2()),
						trim($frm->getYEARPERIOD3())."".trim($frm->getMONTHPERIOD3()),
						trim($frm->getYEARPERIOD4())."".trim($frm->getMONTHPERIOD4()),
						trim($frm->getYEARPERIOD5())."".trim($frm->getMONTHPERIOD5()),
                        trim($frm->getYEARPERIOD6())."".trim($frm->getMONTHPERIOD6()));
        $bill_period = getPeriode($bill);
    }
	return $bill_period;
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
    } else if(substr(strtoupper($kdproduk), 0,3) == "PKB" || substr(strtoupper($kdproduk), 0,2) == "EM" ){
        return array();
    }else{
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : "",
            'periode' => $frm->getStatus() == '00' ? (string) $r_periode_tagihan : "",
        );
    }
}

function cekBiller($id_trx){
    if ($id_trx == ""){
        return 0;
    }
    $db = reconnect_ro();
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

function cekIPmitra($ip){

    $db = reconnect();
    $ret = 0;
    $q = "select * from blacklist_data where subject = $1";
    // echo $q;die();
    $bind = array();
    $bind[] = $ip;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
       $result = true;
    }else{
       $result = false;
    }
    pg_free_result($e);
    pg_close($db);
    return $result;
}

function send_json($data , $url){
    $api_url = $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch); 
    return $result;
}
function getDataPlnpac($idoutlet,$idpel,$kdproduk)
{

    $db = reconnect_ro();
    $q = "select 
                bill_info6 as jml_bulan,
                bill_info18 as tarif,
                bill_info19 as daya ,bill_info5 as ref ,bill_info28 as stanawal,bill_info29 as stanakhir,bill_info4 as nama_pelanggan,
            bill_info21 as periode 
            from transaksi 
            where 
                transaction_date = now()::date
                and id_outlet=$1 
                and id_produk=$2 
                and bill_info1=$3 
                and jenis_transaksi=1 
                and response_code='00'";
    $data = array();
    $bind = array();
    $bind[] = $idoutlet;
    $bind[] = $kdproduk;
    $bind[] = $idpel;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $data['jml_bulan'] = $r->jml_bulan;
        $data['tarif'] = $r->tarif;
        $data['daya'] = $r->daya;
        $data['ref'] = $r->ref;
        $data['stanawal'] = $r->stanawal;
        $data['stanakhir'] = $r->stanakhir;
        $data['nama_pelanggan'] = $r->nama_pelanggan;
        $data['periode'] = $r->periode;

    }
    pg_free_result($e);
    pg_close($db);
        return $data;
       
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
	} else if(in_array($kdproduk,KodeProduk::getTelkomSpeedy())){
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
    }else if(substr(strtoupper($kdproduk), 0,2) == "EM"){
         $reffno = $jenistrx == 0 ? "" : $frm->getRefNo();
         if($kdproduk == "EMOVO"){
            $nama = $frm->getAddtData();
         }else{
            $nama = $frm->getCustomerName();
         }
        return array(
            'nama_pelanggan' => $nama,
            'reffno' => $reffno,
        );
    } else if(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        $nomorpengesahan = $jenistrx == 0 ? "" : $frm->getREFF_NUM();
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? $frm->getKND_NAMA() : '',
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

function cekglobal($id_transaksi, $field) {
    $ret = 0;
    $qn = "SELECT $field FROM fmss.transaksi WHERE id_transaksi = $1";
    $bind = array();
    $bind[] = $id_transaksi;
    $eqn = select_master($qn,$bind);
    $row = $eqn[0];
    $ret = $row->$field;
    return $ret;
}

function getdatapln($kdproduk,$frm, $jenistrx = 0){
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

        return array(
            'jml_bulan' => $frm->getStatus() == '00' ? (string) $billqty : "",
            'tarif' => $frm->getStatus() == '00' ? (string) $tarif : "",
            'daya' => $frm->getStatus() == '00' ? (string) ltrim($daya,'0') : "",
            'ref' => $frm->getStatus() == '00' ? (string) $ref : "",
            'stanawal' => $frm->getStatus() == '00' ? (string) ltrim($stanawal,'0') : "",
            'stanakhir' => $frm->getStatus() == '00' ? (string) ltrim($stanakhir,'0') : "",
            'infoteks' => $frm->getStatus() == '00' ? (string) $infoteks : "",
        );
	} else if($kdproduk==KodeProduk::getPLNPrepaid() || in_array($kdproduk,KodeProduk::getPLNPrepaids()) || substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){
		$meterai = getValue2(trim($frm->getStampDuty()), trim($frm->getMinorUnitStampDuty()));
        $ppn = getValue2($frm->getPPN(), $frm->getMinorUnitPPN());
        $tarif = trim($frm->getSubscriberSegmentation()); 
        $daya = trim($frm->getPowerConsumingCategory()); 
        $ppj= getValue2($frm->getPPJ(), $frm->getMinorUnitPPJ());
        $ref = trim($frm->getNoRef2());
        $angsuran = getValue2($frm->getCustomerPayablesInstallment(),$frm->getMinorUnitCustomerPayablesInstallment);                        
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
    }
}

function getValue2($nominal="0", $minor=0){
    $nominal=sprintf("%".($minor+1)."0s", $nominal);
    $ret=substr($nominal, 0, (strlen($nominal)-$minor)).".".substr($nominal,(strlen($nominal)-$minor));
    return (double) $ret;
}

function enkripUrl($id_outlet,$id_transaksi){
	//$id_outlet = "BS0003";
	//$id_transaksi = "123456789";
	$timestamp = date("Y-m-d H:i:s");

	$string = $id_outlet."|".$id_transaksi."|".$timestamp;
	//echo "plain = ".$string;
	$key = "irememberyou";
	$encrypted = urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key)))));
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	return $encrypted;
}

function getPeriode($bill){
	$bill_period = "";
	$i = 0;
	//foreach($bill as $v){
	//	if($i > 0 && $v<> ""){
	//		$bill_period .= ",";
	//	}
	//	$bill_period .= $v;
	//	$i++;
	//}

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

function convertDenda($bill){
    $bill_period = "";
    $i = 0;

    foreach($bill as $v){
        if($i > 0 && $v<> ""){
            $bill_period .= ",";
        }
        $bill_period .= $v;
        $i++;
    }

    return $bill_period;
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

function isValidIP($id_outlet, $sender){
    $is_valid = false;
    $db = reconnect();    
    $q = "SELECT * FROM fmss.mt_outlet WHERE id_outlet = $1 ";
    $bind = array();
    $bind[]=trim($id_outlet);
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $ip = trim($r->ip);
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
    }
    pg_close($db);
    return $is_valid;
}

function checkPinPrev($id_outlet){
    $id_outlet = trim($id_outlet);
    
    $qry_cek = "SELECT id_outlet 
    FROM fmss.mobile_token 
    WHERE id_outlet = $1
    AND date_created + interval '5 minutes' > now()";

    $bind = array();
    $bind[] = $id_outlet;
    $e = select_replika($qry_cek, $bind);
    $id = trim($e[0]->id_outlet);

    if($id != ""){
        return "Outlet Anda sudah request key sebelumnya. Silahkan cek sms di handphone Anda";
    } else {
        return "";
    }
}

function isValidPhone($id_outlet, $phone){
    $is_valid = false;
    $id_outlet = trim($id_outlet);
    $q = "SELECT nomor_whatsapp_outlet FROM fmss.mt_outlet WHERE id_outlet = '".$id_outlet."'";
    $e = select_replika($q);
    $wa = trim($e[0]->nomor_whatsapp_outlet);    
    if($wa == ""){
        return false;
    }
    $search = array(" ", "(", ")", ".");
    $ganti  = array("","","","");
    $hp     = substr_replace(str_replace($search, $ganti, $wa),'62',0,1);

	if($hp != $phone){
		$is_valid = false;
	} else {
		$is_valid = true;
	}
	return $is_valid;
}

function getAdditionalDatas($kdproduk,$frm){
	$res = array();
	$man = FormatMsg::mandatoryPayment();
	$catatan = "TIDAK DIIJINKAN MENAMBAH CHARGE";
	if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids()) || substr(strtoupper($kdproduk),0,7) == "PLNPASC" ){
		$res = array(	"CATATAN" => (string) $catatan,
						"SUBSCRIBERSEGMENTATION" => $frm->getStatus() == '00' ? (string) trim($frm->getSubscriberSegmentation()) : '',
						"POWERCONSUMINGCATEGORY" => $frm->getStatus() == '00' ? (string) trim($frm->getPowerConsumingCategory()) : '',
						"SLALWBP1" => $frm->getStatus() == '00' ? (string) trim($frm->getPreviousMeterReading11()) : '',
						"SAHLWBP1" => $frm->getStatus() == '00' ? (string) trim($frm->getCurentMeterReading11()) : '',
						"SAHLWBP2" => $frm->getStatus() == '00' ? (string) trim($frm->getCurentMeterReading12()) : '',
						"SAHLWBP3" => $frm->getStatus() == '00' ? (string) trim($frm->getCurentMeterReading13()) : '',
						"SAHLWBP4" => $frm->getStatus() == '00' ? (string) trim($frm->getCurentMeterReading14()) : '',
				);
	}else if($kdproduk==KodeProduk::getPLNPrepaid()|| $kdproduk=="PLNPRA40" || in_array($kdproduk,KodeProduk::getPLNPrepaids()) || substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){

		$res = array(	"CATATAN" => (string) $catatan,
						"TOKEN" => $frm->getStatus() == '00' ? (string) trim($frm->getTokenPln()) : '',
						"SUBSCRIBERSEGMENTATION" => $frm->getStatus() == '00' ? (string) trim($frm->getSubscriberSegmentation()) : '',
						"POWERCONSUMINGCATEGORY" => $frm->getStatus() == '00' ? (string) trim($frm->getPowerConsumingCategory()) : '',
						"POWERPURCHASE" => $frm->getStatus() == '00' ? (string) trim($frm->getPowerPurchase()) : '',
						"MINORUNITOFPOWERPURCHASE" => $frm->getStatus() == '00' ? (string) trim($frm->getMinorUnitOfPowerPurchase()) : '',
						"PURCHASEDKWHUNIT" => $frm->getStatus() == '00' ? (string) trim($frm->getPurchasedKWHUnit()) : '',
						"MINORUNITOFPURCHASEDKWHUNIT" => $frm->getStatus() == '00' ? (string) trim($frm->getMinorUnitOfPurchasedKWHUnit()) : '',
				);
        
	}else if($kdproduk==KodeProduk::getPLNNontaglist() || in_array($kdproduk,KodeProduk::getPLNNontaglists())){
		$res = array(	"CATATAN" => (string) $catatan,
						"TRANSACTIONCODE" => $frm->getStatus() == '00' ? (string) trim($frm->getTransactionCode()) : '',
						"TRANSACTIONNAME" => $frm->getStatus() == '00' ? (string) trim($frm->getTransactionName()) : '',
						"REGISTRATIONDATE" => $frm->getStatus() == '00' ? (string) trim($frm->getRegistrationDate()) : '',
				);
	}else if(in_array($kdproduk,KodeProduk::getTelkomSpeedy())){
		$catatan_telkom = '';
		$res = array(	"CATATAN" => (string) $catatan_telkom,
						"JUMLAHBILL" => $frm->getStatus() == '00' ? (string) trim($frm->getJumlahBill()) : '',
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
		$res = array(	"CATATAN" => (string) $catatan,
						"STANDAWAL" => $frm->getStatus() == '00' ? (string) trim($awal) : '',
						"STANDAKHIR" => $frm->getStatus() == '00' ? (string) trim($akhir) : '',
				);
	} else if(in_array($kdproduk, KodeProduk::getMultiFinance())){
        if(strtoupper($frm->getMember()) == "HH122973" || strtoupper($frm->getMember()) == "HH15580"){
          if(substr(strtoupper($kdproduk), 0,4) =='FNHC'){
            $res = array(
                        'JATUH_TEMPO' => $frm->getStatus() == '00' ? (string) trim($frm->getLastPaidDueDate()) : '',
                        'JENIS_TAGIHAN' => $frm->getStatus() == '00' ? (string) trim($frm->getNoref1()) : '',
                        'NAMA_KREDIT' => $frm->getStatus() == '00' ? (string) trim($frm->getPTName()) : '',
                    );
           }else{
            $res = array(  "CATATAN" => (string) $catatan,
                        "TENOR" => $frm->getStatus() == '00' ? (string) trim($frm->getTenor()) : '',
                        "CARNUMBER" => $frm->getStatus() == '00' ? (string) trim($frm->getCarNumber()) : '',
            );
           }
        }else{
            $res = array(  "CATATAN" => (string) $catatan,
                        "TENOR" => $frm->getStatus() == '00' ? (string) trim($frm->getTenor()) : '',
                        "CARNUMBER" => $frm->getStatus() == '00' ? (string) trim($frm->getCarNumber()) : '',
            );
        }
        
	} else if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS"){
        $res = array(   "CATATAN" => (string) $catatan,
                        "NOHP" => $frm->getStatus() == '00' ? (string) trim($frm->getCustomerPhoneNumber()) : '',
                        "NOREFERENSI" => $frm->getStatus() == '00' ? (string) trim($frm->getBillerRefNumber()) : '',
                );
    } else if(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        if($kdproduk == "PAJAKJTNG"){
              $res = array(   "CATATAN" => (string) $catatan,
                        "KODE_PAJAK" => $frm->getStatus() == '00' ? (string) trim($frm->getKodePajak()) : '',
                        "LOKASI" => $frm->getStatus() == '00' ? (string) trim($frm->getLokasi()) : '',
                        "ALAMAT" => $frm->getStatus() == '00' ? (string) trim($frm->getKelurahan()) : '',
                );
        }else{
              $res = array(   "CATATAN" => (string) $catatan,
                        "KODE_PAJAK" => $frm->getStatus() == '00' ? (string) trim($frm->getKodePajak()) : '',
                        "LOKASI" => $frm->getStatus() == '00' ? (string) trim($frm->getLokasi()) : '',
                        "ALAMAT" => $frm->getStatus() == '00' ? (string) trim($frm->getKelurahan()) : '',
                        "NOREF" => $frm->getStatus() == '00' ? (string) trim($frm->getNoref2()) : '',
                );
        }
      
    }  else if(substr(strtoupper($kdproduk), 0,3) == "PKB"){
        $res = array(   "CATATAN" => (string) $catatan,
                        'MEREK' => $frm->getStatus() == '00' ? (string) $frm->getKD_MERK() : '',
                        'TYPE' => $frm->getStatus() == '00' ? (string) $frm->getKD_TIPE() : '',
                        'TAHUN_RAKIT' => $frm->getStatus() == '00' ? (string) $frm->getKND_THN_BUAT() : '',
                        'NO_RANGKA' => $frm->getStatus() == '00' ? (string) $frm->getKND_RANGKA() : '',
                        'NO_MESIN' => $frm->getStatus() == '00' ? (string) $frm->getKND_MESIN() : '',
                        'NO_BPKB' => $frm->getStatus() == '00' ? (string) $frm->getKND_NO_BPKB() : '',
                        'ALAMAT' => $frm->getStatus() == '00' ? (string) $frm->getKND_ALAMAT() : '',
                        'NO_PENGESAHAN' => $frm->getStatus() == '00' ? $frm->getREFF_NUM() : ''
                );
    } else if($kdproduk === 'PGN'){
        $res = array(   "CATATAN" => (string) $catatan,
                        "USAGE" => $frm->getStatus() == '00' ? (string) trim($frm->getUsage()) : '',
                );
    } else if($kdproduk === 'ASRCAR'){
        $res = array(   "CATATAN" => (string) $catatan,
                        "NO POLISH" => $frm->getStatus() == '00' ? (string) trim($frm->getCustomerPhoneNumber()) : '',
                );
    } else if($kdproduk === 'GAS'){
        $qty = $frm->getBILLQUANTITY();
		$awal = $frm->getFIRSTMETERREAD1();
		if($qty == 0){
			$qty == 1;
		}
		if($qty == 1){
			$akhir = $frm->getLASTMETERREAD1();
		} else if($qty == 2){
			$akhir = $frm->getLASTMETERREAD2();
		} else if($qty == 3){
			$akhir = $frm->getLASTMETERREAD3();
		} else if($qty == 4){
			$akhir = $frm->getLASTMETERREAD4();
		} else if($qty == 5){
			$akhir = $frm->getLASTMETERREAD5();
		} else {
			$akhir = $frm->getLASTMETERREAD6();
		}
		$res = array(	"CATATAN" => (string) $catatan,
						"STANDAWAL" => $frm->getStatus() == '00' ? (string) trim($awal) : '',
						"STANDAKHIR" => $frm->getStatus() == '00' ? (string) trim($akhir) : '',
				);
    }
	
	return $res;
}

function getNominalTransaksi($id_transaksi){
    if (intval($id_transaksi) === 0){
        return 0;
    }

    $db = reconnect();
    $nominal = "";
    $q = "select nominal from fmss.proses_transaksi where id_transaksi = $1 
             UNION 
             SELECT nominal from fmss.transaksi where id_transaksi = $2 
             UNION 
             SELECT nominal from fmss.transaksi_backup where id_transaksi = $3";
    $bind = array();
    $bind[] = $id_transaksi;
    $bind[] = $id_transaksi;
    $bind[] = $id_transaksi;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
		$r = pg_fetch_object($e);
		$nominal = $r->nominal;

	}
    pg_free_result($e);
    pg_close($db);
    if($nominal == null || $nominal == "") {
        return 'x';
    }else{
        return $nominal;
    }   
}


function getStatusProsesTransaksiDevel($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
     $db = reconnect_D();
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

    $db = reconnect_D();
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
    
    $db = reconnect_D();
   

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

    $db = reconnect();

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

function getIdBiller($id_transaksi){
    if (intval($id_transaksi) === 0){
        return 0;
    }
    $db = reconnect_ro();
	$idbiller = "";
    $q = "select id_biller from transaksi where id_transaksi = $1";
    $bind = array();
    $bind[] = $id_transaksi;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
		$r = pg_fetch_object($e);
		$idbiller = $r->id_biller;
	}
    pg_free_result($e);
    pg_close($db);
    return $idbiller;
}

function getMesssageDev($id_produk , $idpel ,$type){
    
    $db = reconnect_D();
    $q = "select content from uat_h2h_dummy where id_produk=$1 and idpel=$2 and type=$3";
    $bind = array();
    $bind[] = $id_produk;
    $bind[] = $idpel; 
    $bind[] = $type;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
		$r = pg_fetch_object($e);
		$message = $r->content;
	}
    pg_free_result($e);
    pg_close($db);
    return $message;
}

function getBiller($id_produk){
    if ($id_produk == ""){
        return 0;
    }
    $db = $GLOBALS["pgsql"];
    $ret = 0;
    $q = "SELECT id_biller from mt_produk where id_produk = $1 ";
    $bind = array();
    $bind[]=$id_produk;
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

function getIdTransaksi($idpel,$idoutlet,$kdproduk,$ref1 = "")
{
    $db = reconnect();
    $idtransaksi = "";
    $q = "select id_transaksi from transaksi where (bill_info1 = $1 or bill_info2 = $2) 
    and id_outlet = $3 and id_produk = $4";
    if($ref1 != ""){
        $q .= " and bill_info83 = $5 and transaction_date = now()::date";
    } else {
        $q .= " and transaction_date = now()::date";
    }
	$bind = array();
    if($ref1 != ""){
        $bind[]=$idpel;
        $bind[]=$idpel;
        $bind[]=$idoutlet;
        $bind[]=$kdproduk;
        $bind[]=$ref1;
    } else {
        $bind[]=$idpel;
        $bind[]=$idpel;
        $bind[]=$idoutlet;
        $bind[]=$kdproduk;
    }
	$e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $idtransaksi = $r->id_transaksi;
    }
    pg_free_result($e);
    pg_close($db);
    return $idtransaksi;
}

function gerdatajabber(){
    $db = reconnect_ro();
    $q = "SELECT merchant_code, host FROM fmss.mt_biller WHERE ( (LOWER(biller)) like('%'||(LOWER('jabber'))||'%')) and merchant_code != ''";
    $e = pg_query($db,$q);
    $n = pg_num_rows($e);
    $arr = array();
    if ($n > 0){
        while($r = pg_fetch_object($e)){
            $arr[] = $r->merchant_code.'@'.$r->host;    
        }
    } else {
        return null;
    }
    pg_free_result($e);
    pg_close($db);    
    return $arr;
}
function updatedataDevel($idoutlet,$idpel1,$ref1,$nominal)
{
    $tgl = date("Y-m-d H:i:s");
    $qn = "update transaksi set nominal = $1 , bill_info83 = $2 , transaction_date = now() , time_request = '".$tgl."' , time_response = '".$tgl."' where id_outlet = $3 and bill_info1=$4";
    $bind = array();
    $bind[] = $nominal;
    $bind[] = $ref1;
    $bind[] = $idoutlet;
    $bind[] = $idpel1;
    write_master_devel($qn,$bind);
}
function getnamabank($kodebank)
{
    $db = reconnect_ro();    
    $x = "";
    $q = "select nama_bank from fmss.bni_laku_pandai_kode_bank where kode_bank_bni = $1";
    // echo $q;
    $bind = array();
    $bind[]= $kodebank;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $x = $r->nama_bank;
    }
    pg_free_result($e);
    pg_close($db);
    return $x;    
}
 
function getnominalup($idtrx){
    if (intval($idtrx) === 0){
	return 0;
    }
    $db = reconnect();    
    $x = "";
    $q = "select nominal_up from fmss.transaksi where id_transaksi = $1
             UNION 
             SELECT nominal_up from fmss.transaksi_backup where id_transaksi = $2
             UNION 
             SELECT nominal_up from fmss.transaksi_backup_final where id_transaksi = $3";
  
    $bind = array();
    $bind[] = $idtrx;
    $bind[] = $idtrx;
    $bind[] = $idtrx;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $x = $r->nominal_up;
    }
    pg_free_result($e);
    pg_close($db);
    return $x;    
}


function postValueWithTimeOutDevel($msg, $timeout = 40) {

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';

    //$url = "http://10.0.1.13/FMSSWeb/mpin1";
    $url = "http://10.0.1.29/FMSSWeb/mpin1";

    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_PORT, 21080);
    curl_setopt($ch, CURLOPT_PORT, 8080);
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

function get_asterix($mid){
    $db = reconnect_ro();
    $q = "
        select * from fmss.message where mid = $1
        union
        select * from fmss.message_final where mid = $2
        union
        select * from fmss.message_final_backup where mid =  $3 order by date_created desc limit 1
    ";
    $bind = array();
    $bind[] = $mid;
    $bind[] = $mid;
    $bind[] = $mid;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        while($row = pg_fetch_row($e)){
            $content  = $row[5];
            $arr[0] = $content ;
        }
    } else {
        $arr[0] = '-';
    }
    pg_free_result($e);
    pg_close($db);    
    return $arr;
}

function getTokenPlnPra($bill_info1, $bill_info2, $nominal, $kodeproduk, $idoutlet){
    $arr = array();
    $db = reconnect();
    $bill_info1_length = '0';
    $temp_char_zero = '';
	$isvalid = false;	
    if(strlen($bill_info1) > 11){
		$bill_info1_length = strlen($bill_info1) - 11;
//		$temp_char_zero = bill_info1.substring(0,bill_info1_length);
		$temp_char_zero = substr($bill_info1, 0, $bill_info1_length);
		if($temp_char_zero == 0){
			$isvalid=true;
		}	
		if($isvalid){
			$bill_info1 = substr($bill_info1, $bill_info1_length); //ubah nilai $_bill_info1 menjadi 11 karakter terakhir dari $_bill_info1 sebelumnya. Contoh $_bill_info1 = "0012345678901", maka nilai $_bill_info1 menjadi "12345678901"
		}else{
			$bill_info2 = $bill_info1;
			$bill_info1 = "";
		}
	}
	$param = "bill_info1";
	$value = trim($bill_info1);
	if($bill_info1 == ''){
		$param = "bill_info2";
		$value = trim($bill_info2);
	}
	
	//$q = "SELECT id_transaksi, bill_info1, bill_info2, bill_info4, bill_info29 FROM transaksi WHERE id_produk='$kodeproduk' AND jenis_transaksi='1' AND response_code='00' AND ".$param."='".$value."' AND nominal='$nominal' AND id_outlet='$idoutlet' AND transaction_date=current_date ORDER BY time_response DESC limit 1";
	$q = "
		select id_transaksi, bill_info1, bill_info2, bill_info4, bill_info29, bill_info16, bill_info17, bill_info18, bill_info41, mid FROM transaksi WHERE id_produk='$kodeproduk' AND jenis_transaksi='1' AND response_code='00' AND ".$param."='".$value."' AND nominal='$nominal' AND id_outlet='$idoutlet' AND transaction_date=current_date and bill_info29 <> '' and bill_info29 is not null
		union
		select id_transaksi, bill_info1, bill_info2, bill_info4, bill_info29, bill_info16, bill_info17, bill_info18, bill_info41, mid FROM proses_transaksi WHERE id_produk='$kodeproduk' AND jenis_transaksi='1' AND response_code='00' AND ".$param."='".$value."' AND nominal='$nominal' AND id_outlet='$idoutlet' AND transaction_date=current_date and bill_info29 <> '' and bill_info29 is not null
	";
	$e = pg_query($db,$q);
    $n = pg_num_rows($e);
    if ($n > 0){
		while($row = pg_fetch_row($e)){
			//$idtrx = $row[0];
			$billinfo1 	= $row[1];
			$billinfo2 	= $row[2];
			$billinfo4 	= $row[3];
			$billinfo29 = $row[4];
            $billinfo16 = $row[5];
            $billinfo17 = $row[6];
            $billinfo18 = $row[7];
            $billinfo41 = $row[8];
            $mid = $row[9];
			
			$arr[0] = $billinfo1 ;
			$arr[1] = $billinfo2 ;
			$arr[2] = $billinfo4 ;
			$arr[3] = $billinfo29;
            $arr[4] = $billinfo16;
            $arr[5] = $billinfo17;
            $arr[6] = $billinfo18;
            $arr[7] = $billinfo41;
            $arr[8] = $mid;
			
		}
	} else {
		$arr[0] = '-';
		$arr[1] = '-';
		$arr[2] = '-';
		$arr[3] = '-';
		$arr[4] = '-';
        $arr[5] = '-';
        $arr[6] = '-';
        $arr[7] = '-';
        $arr[8] = '-';
	}
	
    pg_free_result($e);
    pg_close($db);
    return $arr;
	
}

function getLastPaidPeriode($kdproduk,$frm){
	$bill_period = "";
	$man = FormatMsg::mandatoryPayment();
	if(in_array($kdproduk, KodeProduk::getMultiFinance())){
		$lastpaidperiode = $frm->getLastPaidPeriode();
	}
	return $lastpaidperiode;
}

function inq_resp_text($array, $mode){
    $gabung = implode("*", $array);
    $pecah = explode('*', $gabung);
    $text = "<?xml version=1.0?>";
    $text .= "<methodResponse>";
    $text .= "<params>";
    $text .= "<param>";
    $text .= "<value><array>";
    $text .= "<data>";
  
    $text .= "<value><string>".$pecah[0]."</string></value>";
    $text .= "<value><string>".$pecah[1]."</string></value>";
    $text .= "<value><string>".$pecah[2]."</string></value>";
    $text .= "<value><string>".$pecah[3]."</string></value>";
    $text .= "<value><string>".$pecah[4]."</string></value>";
    $text .= "<value><string>".$pecah[5]."</string></value>";
    $text .= "<value><string>".$pecah[6]."</string></value>";
    $text .= "<value><string>".$pecah[7]."</string></value>";
    $text .= "<value><string>".$pecah[8]."</string></value>";
    $text .= "<value><string>".$pecah[9]."</string></value>";
    $text .= "<value><string>------</string></value>";
    $text .= "<value><string>".$pecah[11]."</string></value>";
    $text .= "<value><string>".$pecah[12]."</string></value>";
    $text .= "<value><string>".$pecah[13]."</string></value>";
    $text .= "<value><string>".$pecah[14]."</string></value>";
    $text .= "<value><string>".$pecah[15]."</string></value>";
    $text .= "<value><string>".$pecah[16]."</string></value>";
    $text .= "<value><string>".$pecah[17]."</string></value>";
    $text .= "<value><string>".$pecah[18]."</string></value>";    
   
    $text .= "</data>";
    $text .= "</array></value>";
    $text .= "</param>";
    $text .= "</params>";
    $text .= "</methodResponse>";
    return $text;
}

function pay_resp_text($array){
    //print_r($array);
    $gabung = implode("*", $array);
    $pecah = explode('*', $gabung);
    $text = "<?xml version=1.0?>";
    $text .= "<methodResponse>";
    $text .= "<params>";
    $text .= "<param>";
    $text .= "<value><array>";
    $text .= "<data>";
    
    $text .= "<value><string>".$pecah[0]."</string></value>";
    $text .= "<value><string>".$pecah[1]."</string></value>";
    $text .= "<value><string>".$pecah[2]."</string></value>";
    $text .= "<value><string>".$pecah[3]."</string></value>";
    $text .= "<value><string>".$pecah[4]."</string></value>";
    $text .= "<value><string>".$pecah[5]."</string></value>";
    $text .= "<value><string>".$pecah[6]."</string></value>";
    $text .= "<value><string>".$pecah[7]."</string></value>";
    $text .= "<value><string>".$pecah[8]."</string></value>";
    $text .= "<value><string>".$pecah[9]."</string></value>";
    $text .= "<value><string>------</string></value>";
    $text .= "<value><string>".$pecah[11]."</string></value>";
    $text .= "<value><string>".$pecah[12]."</string></value>";
    $text .= "<value><string>".$pecah[13]."</string></value>";
    $text .= "<value><string>".$pecah[14]."</string></value>";
    $text .= "<value><string>".$pecah[15]."</string></value>";
    $text .= "<value><string>".$pecah[16]."</string></value>";
    $text .= "<value><string>".$pecah[17]."</string></value>";
    $text .= "<value><string>".$pecah[18]."</string></value>";    
   
    $text .= "</data>";
    $text .= "</array></value>";
    $text .= "</param>";
    $text .= "</params>";
    $text .= "</methodResponse>";
    return $text;
}

function paydetil_resp_text($array, $kdproduk){
    //print_r($array);

    $gabung = implode("*", $array);
    $pecah = explode('*', $gabung);
    $text = "<?xml version=1.0?>";
    $text .= "<methodResponse>";
    $text .= "<params>";
    $text .= "<param>";
    $text .= "<value><array>";
    $text .= "<data>";
    //die('1');
    //foreach($pecah[19] as $asem => $asu){
    //    echo $asu;
    //}
    //die();
    $text .= "<value><string>".$pecah[0]."</string></value>";
    $text .= "<value><string>".$pecah[1]."</string></value>";
    $text .= "<value><string>".$pecah[2]."</string></value>";
    $text .= "<value><string>".$pecah[3]."</string></value>";
    $text .= "<value><string>".$pecah[4]."</string></value>";
    $text .= "<value><string>".$pecah[5]."</string></value>";
    $text .= "<value><string>".$pecah[6]."</string></value>";
    $text .= "<value><string>".$pecah[7]."</string></value>";
    $text .= "<value><string>".$pecah[8]."</string></value>";
    $text .= "<value><string>".$pecah[9]."</string></value>";
    $text .= "<value><string>------</string></value>";
    $text .= "<value><string>".$pecah[11]."</string></value>";
    $text .= "<value><string>".$pecah[12]."</string></value>";
    $text .= "<value><string>".$pecah[13]."</string></value>";
    $text .= "<value><string>".$pecah[14]."</string></value>";
    $text .= "<value><string>".$pecah[15]."</string></value>";
    $text .= "<value><string>".$pecah[16]."</string></value>";
    $text .= "<value><string>".$pecah[17]."</string></value>";
    $text .= "<value><string>".$pecah[18]."</string></value>";
    $text .= "<value><struct>";
    if(in_array($kdproduk, KodeProduk::getPLNPostpaids()) || substr(strtoupper($kdproduk),0,7) == "PLNPASC" ){
        $text .= "<member>";
        $text .= "<name>CATATAN</name>";
        $text .= "<value><string>".$array[19]['CATATAN']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>SUBSCRIBERSEGMENTATION</name>";
        $text .= "<value><string>".$array[19]['SUBSCRIBERSEGMENTATION']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>POWERCONSUMINGCATEGORY</name>";
        $text .= "<value><string>".$array[19]['POWERCONSUMINGCATEGORY']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>SLALWBP1</name>";
        $text .= "<value><string>".$array[19]['SLALWBP1']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>SAHLWBP1</name>";
        $text .= "<value><string>".$array[19]['SAHLWBP1']."</string></value>";
        $text .= "</member>"; 
        $text .= "<member>";
        $text .= "<name>SAHLWBP2</name>";
        $text .= "<value><string>".$array[19]['SAHLWBP2']."</string></value>";
        $text .= "</member>"; 
        $text .= "<member>";
        $text .= "<name>SAHLWBP3</name>";
        $text .= "<value><string>".$array[19]['SAHLWBP3']."</string></value>";
        $text .= "</member>"; 
        $text .= "<member>";
        $text .= "<name>SAHLWBP4</name>";
        $text .= "<value><string>".$array[19]['SAHLWBP4']."</string></value>";
        $text .= "</member>";
    } else if(in_array($kdproduk, KodeProduk::getPLNPrepaids()) || substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){
        $text .= "<member>";
        $text .= "<name>CATATAN</name>";
        $text .= "<value><string>".$array[19]['CATATAN']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>TOKEN</name>";
        $text .= "<value><string>".$array[19]['TOKEN']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>SUBSCRIBERSEGMENTATION</name>";
        $text .= "<value><string>".$array[19]['SUBSCRIBERSEGMENTATION']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>POWERCONSUMINGCATEGORY</name>";
        $text .= "<value><string>".$array[19]['POWERCONSUMINGCATEGORY']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>POWERPURCHASE</name>";
        $text .= "<value><string>".$array[19]['POWERPURCHASE']."</string></value>";
        $text .= "</member>"; 
        $text .= "<member>";
        $text .= "<name>MINORUNITOFPOWERPURCHASE</name>";
        $text .= "<value><string>".$array[19]['MINORUNITOFPOWERPURCHASE']."</string></value>";
        $text .= "</member>"; 
        $text .= "<member>";
        $text .= "<name>PURCHASEDKWHUNIT</name>";
        $text .= "<value><string>".$array[19]['PURCHASEDKWHUNIT']."</string></value>";
        $text .= "</member>"; 
        $text .= "<member>";
        $text .= "<name>MINORUNITOFPURCHASEDKWHUNIT</name>";
        $text .= "<value><string>".$array[19]['MINORUNITOFPURCHASEDKWHUNIT']."</string></value>";
        $text .= "</member>";
    } else if(in_array($kdproduk, KodeProduk::getPLNNontaglists())){
        $text .= "<member>";
        $text .= "<name>CATATAN</name>";
        $text .= "<value><string>".$array[19]['CATATAN']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>TRANSACTIONCODE</name>";
        $text .= "<value><string>".$array[19]['TRANSACTIONCODE']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>TRANSACTIONNAME</name>";
        $text .= "<value><string>".$array[19]['TRANSACTIONNAME']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>REGISTRATIONDATE</name>";
        $text .= "<value><string>".$array[19]['REGISTRATIONDATE']."</string></value>";
        $text .= "</member>";
    } else if(in_array($kdproduk, KodeProduk::getTelkomSpeedy())){
        $text .= "<member>";
        $text .= "<name>CATATAN</name>";
        $text .= "<value><string>".$array[19]['CATATAN']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>JUMLAHBILL</name>";
        $text .= "<value><string>".$array[19]['JUMLAHBILL']."</string></value>";
        $text .= "</member>"; 
    } else if(in_array($kdproduk, KodeProduk::getPAM()) || substr($kdproduk,0,2) == "WA"){        
        $text .= "<member>";
        $text .= "<name>CATATAN</name>";
        $text .= "<value><string>". $array[19]['CATATAN']. "</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>STANDAWAL</name>";
        $text .= "<value><string>".$array[19]['STANDAWAL']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>STANDAKHIR</name>";
        $text .= "<value><string>".$array[19]['STANDAKHIR']."</string></value>";
        $text .= "</member>";
    } else if(in_array($kdproduk, KodeProduk::getMultiFinance())){
        $text .= "<member>";
        $text .= "<name>CATATAN</name>";
        $text .= "<value><string>".$array[19]['CATATAN']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>TENOR</name>";
        $text .= "<value><string>".$array[19]['TENOR']."</string></value>";
        $text .= "</member>";
        $text .= "<member>";
        $text .= "<name>CARNUMBER</name>";
        $text .= "<value><string>".$array[19]['CARNUMBER']."</string></value>";
        $text .= "</member>";
    }
    $text .= "</struct></value>";
    
    $text .= "</data>";
    $text .= "</array></value>";
    $text .= "</param>";
    $text .= "</params>";
    $text .= "</methodResponse>";
    return $text;
}

function pulsa_game_resp_text($array){
    //print_r($array);
    $gabung = implode("*", $array);
    $pecah = explode('*', $gabung);

    $text = "<?xml version=1.0?>";
    $text .= "<methodResponse>";
    $text .= "<params>";
    $text .= "<param>";
    $text .= "<value><array>";
    $text .= "<data>";

    $text .= " <value><string>".$pecah[0]."</string></value>";
    $text .= " <value><string>".$pecah[1]."</string></value>";
    $text .= " <value><string>".$pecah[2]."</string></value>";
    $text .= " <value><string>".$pecah[3]."</string></value>";
    $text .= " <value><string>------</string></value>";
    $text .= " <value><string>".$pecah[5]."</string></value>";
    $text .= " <value><string>".$pecah[6]."</string></value>";
    $text .= " <value><string>".$pecah[7]."</string></value>";
    $text .= " <value><string>".$pecah[8]."</string></value>";
    $text .= " <value><string>".$pecah[9]."</string></value>";
    $text .= " <value><string>".$pecah[10]."</string></value>";
    $text .= " <value><string>".$pecah[11]."</string></value>";

    $text .= "</data>";
    $text .= "</array></value>";
    $text .= "</param>";
    $text .= "</params>";
    $text .= "</methodResponse>";
    return $text;

    //die($text);
}

function getemailmember($idoutlet){
    $db = reconnect_ro();
    $email = "";
    $q = "select email from fmss.mt_outlet where id_outlet = $1 ";
    $bind = array();
    $bind[] = $idoutlet;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
        $r = pg_fetch_object($e);
        $email = $r->email;
    }
    pg_free_result($e);
    pg_close($db);
    return $email;
}

function signon_check_key($id_outlet, $key) {
    $db = reconnect_ro();
    $today = date("Y-m-d");
    $date_expired = date("Y-m-d 00:00:00", strtotime($today . " + 1 DAY"));
    $q = "SELECT id_token FROM fmss.mobile_token WHERE id_outlet = upper('".$id_outlet."') AND date_expired > now() ORDER BY date_created DESC LIMIT 2";

    $e = pg_query($db,$q);
    $n = pg_num_rows($e);
    if ($n > 0) {
        $id_token = "";
        while($r = pg_fetch_object($e)){
            if ($id_token == "") {
                $id_token = $r->id_token;
            } else {
                $id_token = $id_token . "," . $r->id_token;
            }
        }
        $qq = "SELECT date_created FROM fmss.mobile_token WHERE id_outlet = '".$id_outlet."' AND id_token IN (" . $id_token . ") 
        AND key = crypt('".$key."', key)";
        $ee = pg_query($db,$qq);
        $nn = pg_num_rows($ee);
        if ($nn > 0) {
            pg_close($db);
            return true;
        }
    }
    pg_close($db);
    return false;
}

function foruse2($grup,$id_produk, $id_outlet){
    $db = reconnect_ro();
    $bind = array();
    $bind[] = $id_outlet;
    if($grup == '' && $id_produk != '')
    {
        $where = " a.id_produk=$2";
        $bind[] = $id_produk;
    }elseif ($grup != '' && $id_produk == '') {
        $dtgroup = array("PDAM","MULTI FINANCE","PAJAK","TV KABEL");
        if($grup == 'FASTMOVE'){
            $where = "d.group_produk in ('GAME ONLINE','TELKOMSEL','SMART','KARTU3','ISAT','FREN','AXIS / XL','AXIS') and (a.id_produk NOT LIKE 'GDG%' and a.id_produk NOT LIKE 'GEP%' and a.id_produk NOT LIKE 'GGW%' and a.id_produk NOT LIKE 'GIH%' and a.id_produk NOT LIKE 'GIN%' and a.id_produk NOT LIKE 'GMB%' and a.id_produk NOT LIKE 'GMM%' and a.id_produk NOT LIKE 'GMWV%' and a.id_produk NOT LIKE 'GOG%' and a.id_produk NOT LIKE 'GOV%' and a.id_produk NOT LIKE 'GP%' and a.id_produk NOT LIKE 'GPD%' and a.id_produk NOT LIKE 'GPI%' and a.id_produk NOT LIKE 'GPY%' and a.id_produk NOT LIKE 'GQN%' and a.id_produk NOT LIKE 'GRB%' and a.id_produk NOT LIKE 'KWC%' and a.id_produk NOT LIKE 'MCDP%' and a.id_produk NOT LIKE 'MEV%' and a.id_produk NOT LIKE 'MTIX%' and a.id_produk NOT LIKE 'PLF%' and a.id_produk NOT LIKE 'PLNX%' and a.id_produk NOT LIKE 'PSNM%' and a.id_produk NOT LIKE 'PSP%' and a.id_produk NOT LIKE 'ROS%' and a.id_produk NOT LIKE 'RXC%' and a.id_produk NOT LIKE 'SDGFF%' and a.id_produk NOT LIKE 'SDGML%' and a.id_produk NOT LIKE 'SPN%' and a.id_produk NOT LIKE 'STAM%' and a.id_produk NOT LIKE 'TER%' and a.id_produk NOT LIKE 'TOLBN%' and a.id_produk NOT LIKE 'TRA%' and a.id_produk NOT LIKE 'UGC%' and a.id_produk NOT LIKE 'VAFA%' and a.id_produk NOT LIKE 'VCAR%' and a.id_produk NOT LIKE 'VCC%' and a.id_produk NOT LIKE 'VCOD%' and a.id_produk NOT LIKE 'VGR%' and a.id_produk NOT LIKE 'VIN1%' and a.id_produk NOT LIKE 'VMS%' and a.id_produk NOT LIKE 'VROGC%' and a.id_produk NOT LIKE 'VWW%' and a.id_produk NOT LIKE 'WOT%' and a.id_produk NOT LIKE 'WOW%' and a.id_produk NOT LIKE 'ZY%'and a.id_produk NOT LIKE 'GCGC%'and a.id_produk NOT LIKE 'VIN%') 
            and a.id_produk like '%H' ";
        }elseif(in_array($grup, $dtgroup)){
            $where = "d.group_produk = $2 ";
            $bind[] = $grup;
        }else{
            $where = "d.group_produk = $2 and a.id_produk like '%H' ";
            $bind[] = $grup;
        }
    }elseif ($grup != '' && $id_produk != '') {
       $where = "d.group_produk = $2 and a.id_produk=$3";
       $bind[] = $grup;
       $bind[] = $id_produk;
    }
 
    $q = "select a.id_produk, a.produk, a.harga_jual,d.group_produk, a.is_active, a.is_gangguan, a.biaya_admin, e.up_harga, e.fee_transaksi from mt_produk a 
    left join group_outlet_produk b on a.id_produk = b.id_produk
    left join mt_outlet_group c on c.id_group = b.id_group 
    left join mt_group_produk d on a.id_group_produk = d.id_group_produk 
    left join mt_setting_komisi_detail_produk e on e.id_produk = a.id_produk 
    where ".$where." and 
    b.id_group = (select id_group from mt_outlet where id_outlet = $1) and 
    e.id_setting_komisi = (select id_setting_komisi from mt_outlet where id_outlet = $1) 
    order by d.group_produk,a.id_produk asc";
    
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    $data = array();
    if ($n > 0){
        while($r = pg_fetch_object($e)){
            $data[] = $r;
        }
        pg_close($db);
        if(count($data) == 0){
            return "";
        } else {
            return $data;
        }
    } else {
        pg_close($db);
        return "";
    }
}
function produk_rajabiller($grup, $id_outlet){
    $db = reconnect_ro();

    if($grup == "ppob"){
         $q = "select id_produk,produk,harga_jual,biaya_admin  from mt_produk where (is_active=1 and id_group_produk=16 and (id_produk like '%H' and id_produk not like '%30H' and id_produk not like '%20H' and  id_produk not like '%50H' and id_produk not like '%100H')) 
                or (is_active=1 and id_group_produk=1)
                or (is_active=1 and id_group_produk=21)
                or (is_active=0 and id_group_produk=40)
                or (is_active=1 and id_group_produk=35)
                or (is_active=1 and id_group_produk=36 and id_produk like '%JWS')
                or (is_active=1 and id_group_produk=3 and id_produk like '%H')
                or (is_active=1 and id_group_produk=11 and id_produk not like '%2')
                or (is_active=1 and id_group_produk=24 and produk not like '%SALDO%' and harga_jual =0)
                or (is_active=1 and id_group_produk=17 and id_produk like '%HCI' or id_produk like '%MAF'or id_produk like '%EGA'or id_produk like '%FIN'or id_produk like '%ADA'or id_produk like '%SMF'or id_produk like '%WKF')
                order by id_group_produk,id_produk,harga_jual asc";
    }
   
    $e = select_master($q);
    return $e;
}


function foruse($grup, $id_outlet){
    $db = reconnect_ro();
    $ppob = array("PDAM","MULTI FINANCE","PAJAK","TELEPON PASCA BAYAR");
    if(in_array($grup,$ppob)){
         $q = "select a.id_produk, a.produk, a.harga_jual, a.is_active, a.is_gangguan, a.biaya_admin, e.up_harga, e.fee_transaksi from mt_produk a 
    left join group_outlet_produk b on a.id_produk = b.id_produk
    left join mt_outlet_group c on c.id_group = b.id_group 
    left join mt_group_produk2 d on a.id_group_produk = d.id_group_produk 
    left join mt_setting_komisi_detail_produk e on e.id_produk = a.id_produk 
    where d.group_produk = $1 and b.id_group = (select id_group from mt_outlet where id_outlet = $2) and e.id_setting_komisi = (select id_setting_komisi from mt_outlet where id_outlet = $3) order by 1 asc";
    }else{
        if($grup == "ASURANSI") {
            $add = "(a.id_produk like '%H' or a.id_produk in ('ASRCAR','ASRPRU')) ";
        }elseif($grup == "TELKOM"){
            $add = "a.id_produk in ('SPEEDY','TELEPON')";
        }elseif($grup == "TIKET"){
            $add = "a.id_produk in ('WKAI','PKAI')";
        }elseif($grup == "TV BERLANGGANAN"){
            $add = "(a.id_produk like 'TVKV%' or a.id_produk like 'TVGEN%' or a.id_produk like 'TVSKY%' or a.id_produk in ('TVINDVS','TVTLKMV'))";
        }elseif($grup == "JIWASRAYA"){
            $add = "(a.id_produk in ('ASRIFG','ASRJWS'))";
        }elseif($grup == "GAS"){
            $add = "(a.id_produk in ('PGN'))";
        }else{
            $add = "a.id_produk like '%H'";
        }
         $q = "select a.id_produk, a.produk, a.harga_jual, a.is_active, a.is_gangguan, a.biaya_admin,e.up_harga,e.fee_transaksi from mt_produk a 
                left join group_outlet_produk b on a.id_produk = b.id_produk
                left join mt_outlet_group c on c.id_group = b.id_group 
                left join mt_group_produk2 d on a.id_group_produk = d.id_group_produk 
                left join mt_setting_komisi_detail_produk e on e.id_produk = a.id_produk 
                where d.group_produk = $1 and b.id_group = (select id_group from mt_outlet where id_outlet = $2) and e.id_setting_komisi = (select id_setting_komisi from mt_outlet where id_outlet = $3) and ".$add." order by 1 asc";
    }
    $bind = array();
    $bind[] = $grup;
    $bind[] = $id_outlet;
    $bind[] = $id_outlet;

    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    $data = array();
    if ($n > 0){
        while($r = pg_fetch_object($e)){
            $data[] = $r;
        }
        pg_close($db);
        if(count($data) == 0){
            return "";
        } else {
            return $data;
        }
    } else {
        pg_close($db);
        return "";
    }


}

function for_rj($id_produk){
    //untuk group outlet rajabiller.com 15 dan h2hpriority 43
    $db = reconnect_ro();
    $q = "select count(*) as jum from group_outlet_produk where id_group in ('15', '43') and id_produk = $1";
    $bind = array();
    $bind[] = $id_produk;
    $e = pg_query_params($db,$q,$bind);
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
         
    pg_free_result($e);
    pg_close($db);
}
function status_produk_komisi($id_produk)
{
      $db = reconnect();
    $q = "select * from mt_setting_komisi_detail_produk WHERE id_produk = $1 and id_setting_komisi in (46) ";
    $bind = array();
    $bind[] = $id_produk;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    $data = "";
    if ($n > 0){
        while($r = pg_fetch_object($e)){
            $data .= $r->biaya_admin;
        }
        return $data;
    } else {

        return "";
    }
         
    pg_free_result($e);
    pg_close($db);
}
function status_produk($id_produk){
    $db = reconnect();
    $q = "select harga_jual, is_active, is_gangguan, biaya_admin, produk FROM fmss.mt_produk WHERE id_produk = $1";
    $bind = array();
    $bind[] = $id_produk;
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    $data = "";
    if ($n > 0){
        while($r = pg_fetch_object($e)){
            $data .= $r->harga_jual.'|'.$r->is_gangguan.'|'.$r->biaya_admin.'|'.$r->is_active.'|'.$r->produk;
        }
        return $data;
    } else {
        return "";
    }
         
    pg_free_result($e);
    pg_close($db);
}

function komisi_produk($id_outlet, $id_produk){
    $db = reconnect_ro();
   
    $q2 = "select up_harga, fee_transaksi from mt_setting_komisi_detail_produk where id_produk = $1 and id_setting_komisi = (select id_setting_komisi from mt_outlet where id_outlet = $2) limit 1";
    $bind = array();
    $bind[] = $id_produk;
    $bind[] = $id_outlet;
    $e2 = pg_query_params($db,$q2,$bind);
    $n2 = pg_num_rows($e2);
    if($n2 > 0){
        while($r2 = pg_fetch_object($e2)){
            $data .= $r2->up_harga.'|'.$r->fee_transaksi;
        }
        pg_close($db);
        return $data;
    } else {
        pg_close($db);
        return "";
    }
   
    
}

function writelogfile($data){
    $file = 'logdatatransaksi.txt';
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
}

function write_log_text($data) {
    $file = getcwd() . "/logs/" . date("Ymd") . '.log';
    if (file_exists($file) == false) {
        $handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);
        fclose($handle);
    }
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
}
function query_status($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "",$namatable = "transaksi"){
    $db = reconnect_ro();
    $cek = is_numeric($idtrx);
    if($idtrx != ""){
        if($cek == 0){
            die(json_encode(array('error'=>'ref2 harus value dari ref2 payment')));
        }
    }
    $q = "
        SELECT id_transaksi, time_request, id_produk, bill_info1, bill_info2, nominal+nominal_up as nominal, nominal_admin, bill_info5, bill_info29 , response_code,keterangan
        FROM $namatable
        WHERE transaction_date = $1 
        AND id_outlet=$2 AND id_produk=$3 and jenis_transaksi = 1";
 
    $bind = array();
    $bind[] = $tgl;
    $bind[] = $idoutlet;
    $bind[] = $idproduk;
    
    if($idtrx != ""){
        $q = str_replace("id_produk=$3 and","", $q);
        unset($bind[2]);
        $q .= " AND id_transaksi = $3";
        $bind[] = $idtrx;
        // langsung bawah .........
    } else {
        if (substr(strtoupper($idproduk),0,6) == 'PLNPRA'){
            if($ref1 != "" && $idtrx =="" && $idpel1 == "" && $idpel2 == "" && $denom == ""){ 
                $q .= " AND bill_info83=$4";
               $bind[] = $ref1;
            }elseif($ref1 == "" && $idtrx !="" && $idpel1 == "" && $idpel2 == "" && $denom == "") {
                $q .= " AND id_transaksi=$4";
                $bind[] = $idtrx;
            }elseif ($ref1 == "" && $idtrx =="" && $idpel1 != "" && $idpel2 == "" && $denom == "") {
                $q .= " AND bill_info1=$4";
                $bind[] = $idpel1;  
            }elseif ($ref1 == "" && $idtrx =="" && $idpel1 == "" && $idpel2 != "" && $denom == "") {
                $q .= " AND bill_info2=$4";
                $bind[] = $idpel2;  
            }elseif ($ref1 == "" && $idtrx =="" && $idpel1 == "" && $idpel2 == "" && $denom != "") {
                $q .= " AND (nominal = $4) ";
                $bind[] = $denom;  
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 == "" && $idpel2 == "" && $denom == ""){ 
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $bind[] = $ref1;
                $bind[] = $idtrx;
            }elseif($ref1 != "" && $idtrx =="" && $idpel1 != "" && $idpel2 == "" && $denom == "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND bill_info1=$5";
                $bind[] = $ref1;
                $bind[] = $idpel1;
            }elseif ($ref1 != "" && $idtrx =="" && $idpel1 == "" && $idpel2 != "" && $denom == "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND bill_info2=$5";
                $bind[] = $ref1;  
                $bind[] = $idpel2;  
            }elseif ($ref1 != "" && $idtrx =="" && $idpel1 == "" && $idpel2 == "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND (nominal = $5) ";
                $bind[] = $ref1;  
                $bind[] = $denom;  
            }elseif ($ref1 == "" && $idtrx !="" && $idpel1 != "" && $idpel2 == "" && $denom == "") {
                $q .= " AND id_transaksi=$4";
                $q .= " AND bill_info1 = $5 ";
                $bind[] = $idtrx;  
                $bind[] = $idpel1;  
            }elseif ($ref1 == "" && $idtrx !="" && $idpel1 == "" && $idpel2 != "" && $denom == "") {
                $q .= " AND id_transaksi=$4";
                $q .= " AND bill_info2 = $5 ";
                $bind[] = $idtrx;  
                $bind[] = $idpel2;  
            }elseif ($ref1 == "" && $idtrx !="" && $idpel1 == "" && $idpel2 == "" && $denom != "") {
                $q .= " AND id_transaksi=$4";
                $q .= " AND nominal = $5 ";
                $bind[] = $idtrx;  
                $bind[] = $denom;  
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 != "" && $idpel2 == "" && $denom == ""){ 
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info1=$6";
                $bind[] = $ref1;
                $bind[] = $idtrx;
                $bind[] = $idpel1;
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 == "" && $idpel2 != "" && $denom == "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info2=$6";
                $bind[] = $ref1;
                $bind[] = $idtrx;
                $bind[] = $idpel2;
            }elseif ($ref1 != "" && $idtrx !="" && $idpel1 == "" && $idpel2 == "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND nominal=$6";
                $bind[] = $ref1;  
                $bind[] = $idtrx;  
                $bind[] = $denom;  
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 != "" && $idpel2 != "" && $denom == ""){ 
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info1=$6";
                $q .= " AND bill_info2=$7";
                $bind[] = $ref1;
                $bind[] = $idtrx;
                $bind[] = $idpel1;
                $bind[] = $idpel2;
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 != "" && $idpel2 == "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info1=$6";
                $q .= " AND nominal=$7";
                $bind[] = $ref1;
                $bind[] = $idtrx;
                $bind[] = $idpel1;
                $bind[] = $denom;
            }elseif($ref1 != "" && $idtrx =="" && $idpel1 != "" && $idpel2 == "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND bill_info1=$5";
                $q .= " AND nominal=$6";
                $bind[] = $ref1;
                $bind[] = $idpel1;
                $bind[] = $denom;
            }elseif ($ref1 != "" && $idtrx !="" && $idpel1 != "" && $idpel2 != "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info1=$6";
                $q .= " AND bill_info2=$7";
                $q .= " AND nominal=$8";
                $bind[] = $ref1;  
                $bind[] = $idtrx;  
                $bind[] = $idpel1;  
                $bind[] = $idpel2;  
                $bind[] = $denom;  
            }
    
        } else {
            if($ref1 != "" && $idtrx =="" && $idpel1 == "" && $idpel2 == "" && $denom == ""){ 
                $q .= " AND bill_info83=$4";
               $bind[] = $ref1;
            }elseif($ref1 == "" && $idtrx !="" && $idpel1 == "" && $idpel2 == "" && $denom == "") {
                $q .= " AND id_transaksi=$4";
                $bind[] = $idtrx;
            }elseif ($ref1 == "" && $idtrx =="" && $idpel1 != "" && $idpel2 == "" && $denom == "") {
                $q .= " AND bill_info1=$4";
                $bind[] = $idpel1;  
            }elseif ($ref1 == "" && $idtrx =="" && $idpel1 == "" && $idpel2 != "" && $denom == "") {
                $q .= " AND bill_info2=$4";
                $bind[] = $idpel2;  
            }elseif ($ref1 == "" && $idtrx =="" && $idpel1 == "" && $idpel2 == "" && $denom != "") {
                $q .= " AND (nominal = $4) ";
                $bind[] = $denom;  
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 == "" && $idpel2 == "" && $denom == ""){ 
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $bind[] = $ref1;
                $bind[] = $idtrx;
            }elseif($ref1 != "" && $idtrx =="" && $idpel1 != "" && $idpel2 == "" && $denom == "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND bill_info1=$5";
                $bind[] = $ref1;
                $bind[] = $idpel1;
            }elseif ($ref1 != "" && $idtrx =="" && $idpel1 == "" && $idpel2 != "" && $denom == "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND bill_info2=$5";
                $bind[] = $ref1;  
                $bind[] = $idpel2;  
            }elseif ($ref1 != "" && $idtrx =="" && $idpel1 == "" && $idpel2 == "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND (nominal = $5) ";
                $bind[] = $ref1;  
                $bind[] = $denom;  
            }elseif ($ref1 == "" && $idtrx !="" && $idpel1 != "" && $idpel2 == "" && $denom == "") {
                $q .= " AND id_transaksi=$4";
                $q .= " AND bill_info1 = $5 ";
                $bind[] = $idtrx;  
                $bind[] = $idpel1;  
            }elseif ($ref1 == "" && $idtrx !="" && $idpel1 == "" && $idpel2 != "" && $denom == "") {
                $q .= " AND id_transaksi=$4";
                $q .= " AND bill_info2 = $5 ";
                $bind[] = $idtrx;  
                $bind[] = $idpel2;  
            }elseif ($ref1 == "" && $idtrx !="" && $idpel1 == "" && $idpel2 == "" && $denom != "") {
                $q .= " AND id_transaksi=$4";
                $q .= " AND nominal = $5 ";
                $bind[] = $idtrx;  
                $bind[] = $denom;  
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 != "" && $idpel2 == "" && $denom == ""){ 
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info1=$6";
                $bind[] = $ref1;
                $bind[] = $idtrx;
                $bind[] = $idpel1;
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 == "" && $idpel2 != "" && $denom == "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info2=$6";
                $bind[] = $ref1;
                $bind[] = $idtrx;
                $bind[] = $idpel2;
            }elseif ($ref1 != "" && $idtrx !="" && $idpel1 == "" && $idpel2 == "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND nominal=$6";
                $bind[] = $ref1;  
                $bind[] = $idtrx;  
                $bind[] = $denom;  
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 != "" && $idpel2 != "" && $denom == ""){ 
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info1=$6";
                $q .= " AND bill_info2=$7";
                $bind[] = $ref1;
                $bind[] = $idtrx;
                $bind[] = $idpel1;
                $bind[] = $idpel2;
            }elseif($ref1 != "" && $idtrx !="" && $idpel1 != "" && $idpel2 == "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info1=$6";
                $q .= " AND nominal=$7";
                $bind[] = $ref1;
                $bind[] = $idtrx;
                $bind[] = $idpel1;
                $bind[] = $denom;
            }elseif ($ref1 != "" && $idtrx !="" && $idpel1 != "" && $idpel2 != "" && $denom != "") {
                $q .= " AND bill_info83=$4";
                $q .= " AND id_transaksi=$5";
                $q .= " AND bill_info1=$6";
                $q .= " AND bill_info2=$7";
                $q .= " AND nominal=$8";
                $bind[] = $ref1;  
                $bind[] = $idtrx;  
                $bind[] = $idpel1;  
                $bind[] = $idpel2;  
                $bind[] = $denom;  
            }
        }
    }

    $q = $q . "  ORDER BY id_transaksi desc LIMIT 1";
    $e = pg_query_params($db, $q, $bind);
    $n = pg_num_rows($e); 



    $out = array();
    //  $i = 0;
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
        $out['response_code'] =$r->response_code;
        $out['keterangan'] =$r->keterangan;
    }

    pg_free_result($e);
    pg_close($db);
    return $out;
}

function getStatusProsesTransaksi($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
   return query_status($tgl, $idproduk, $idoutlet, $ref1 , $idtrx , $idpel1 , $idpel2 , $denom ,"proses_transaksi");
}

function getStatusTransaksi($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
    
    return query_status($tgl, $idproduk, $idoutlet, $ref1 , $idtrx , $idpel1 , $idpel2 , $denom,"transaksi");
}

function getStatusTransaksiBackup($tgl, $idproduk, $idoutlet, $ref1 = "", $idtrx = "", $idpel1 = "", $idpel2 = "", $denom = "") {
    return query_status($tgl, $idproduk, $idoutlet, $ref1 , $idtrx , $idpel1 , $idpel2 , $denom ,"transaksi_backup");
}

function checkpin($idoutlet, $pin){
    $db = reconnect();
   // $q = "SELECT id_outlet FROM fmss.mt_outlet WHERE id_outlet = upper('".$idoutlet."') AND pin = crypt(upper(md5('".$pin."')), pin) AND is_active=1";
    $bind = array();
    $bind[]=$idoutlet;
    $bind[]=$pin;
    $ip = $_SERVER['REMOTE_ADDR']."-".$_SERVER['REMOTE_HOST'];
    $bind[]=$ip;

    $q = "select fmss.check_pin($1,$2,'RAJABILLER',$3) as hasil";
   // $e = pg_query($db,$q);
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);
    if ($n > 0){
	 $r = pg_fetch_object($e);
    // print_r($r);
	 if($r->hasil == $idoutlet){
        	return TRUE;
	}else{
		return FALSE;
	}
    } else{
        return FALSE;
    }
    pg_free_result($e);
    pg_close($db);
}

 function getClientIP(){
     if (array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER)){
        return  $_SERVER["HTTP_CF_CONNECTING_IP"];
     }else if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            return  $_SERVER["HTTP_X_FORWARDED_FOR"];
     }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return $_SERVER["REMOTE_ADDR"];
     }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $_SERVER["HTTP_CLIENT_IP"];
     }

     return '';
}

function setMandatoryResponJson($frm, $ref1, $ref2, $ref3, $request) {
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
        $r_idpel1 = $request->idpel1;
    }
    if(substr(strtoupper($r_kdproduk), 0,9) == "ASRBPJSKS"){
        $r_idpel1 = $request->idpel;
    } 

    if($r_kdproduk == "TELEPON" || $r_kdproduk == "TELEPON2"){
        $r_idpel1 = $r_idpel1.$r_idpel2;
        $$r_idpel2 = '';
    } 

    if(substr(strtoupper($r_kdproduk), 0,7) == "PLNPRAD"){
        $saldo_terpotong = getnominal($r_kdproduk);
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

function setMandatoryResponJsonDev($frm, $ref1, $ref2, $ref3, $request) {
    $r_idoutlet = $request->uid == "" ? $request->id_outlet : $request->uid;
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
    $fee = 0;
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
        $r_idpel1 = $request->idpel1;
    }
    if(substr(strtoupper($r_kdproduk), 0,9) == "ASRBPJSKS"){
        $r_idpel1 = $request->idpel;
    } 

    if($r_kdproduk == "TELEPON" || $r_kdproduk == "TELEPON2"){
        $r_idpel1 = $r_idpel1.$r_idpel2;
        $$r_idpel2 = '';
    } 

    if(substr(strtoupper($r_kdproduk), 0,7) == "PLNPRAD"){
        $saldo_terpotong = getnominal($r_kdproduk);
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

function setMandatoryResponNewDev($frm, $ref1, $ref2, $ref3, $request) {
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
    $fee = 0;
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

    if(substr(strtoupper($r_kdproduk), 0,7) == "PLNPRAD"){
        $saldo_terpotong = getnominal($r_kdproduk);
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

    if(substr(strtoupper($r_kdproduk), 0,7) == "PLNPRAD"){
        $saldo_terpotong = getnominal($r_kdproduk);
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

function setMandatoryResponIrs($frm, $ref1, $ref2, $ref3,$jenis, $request) {
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
    if(substr(strtoupper($r_kdproduk), 0,7) == "PLNPRAD"){
        $saldo_terpotong = getnominal($r_kdproduk);
    }
    $params = array(
        'kodeproduk' => (string) $jenis.''.$r_kdproduk, 
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

function setMandatoryResponNewArranet($frm, $ref1, $ref2, $ref3, $request) {
    $r_idoutlet = $frm->getMember();
    $r_kdproduk = $frm->getKodeProduk();
    $r_tanggal = $frm->getTanggal();
    $r_idpel1 = $frm->getIdPel1();
    $r_idpel2 = $frm->getIdPel2();
    $r_idpel3 = $frm->getIdPel3();
    $r_nominal = (int) $frm->getNominal();
    $r_nominaladmin = (int) $frm->getNominalAdmin();
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

    if($r_kdproduk==KodeProduk::getPLNPrepaid() || in_array($r_kdproduk,KodeProduk::getPLNPrepaids()) || substr(strtoupper($r_kdproduk),0,6) == 'PLNPRA'){
        $params = array(
            'KODEPRODUK' => (string) $r_kdproduk, 
            'TANGGAL' => (string) $r_tanggal, 
            'IDPEL1' => (string) $r_idpel1, 
            'IDPEL2' => (string) $r_idpel2, 
            'IDPEL3' => (string) $r_idpel3, 
            'NOMINAL' => (string) $r_nominal, 
            'ADMIN' => (string) $r_nominaladmin, 
            'ID_OUTLET' => (string) $r_idoutlet, 
            'PIN' => (string) "------", 
            'REF1' => (string) $ref1, 
            'REF2' => (string) $r_idtrx, 
            'REF3' => (string) $ref3, 
            'STATUS' => (string) $r_status, 
            'KETERANGAN' => (string) $r_keterangan, 
            'FEE' => (string) $fee, 
            'SALDO_TERPOTONG' => (string) $saldo_terpotong,
            'SISA_SALDO' => (string) $r_saldo,
            'TOTAL_BAYAR' => (string) $total,
        );
    }else{
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
    }
    return $params;
}

function tambahdataproduk_arranet($kdproduk,$frm, $jenistrx = 0){
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
       
        return array(
            'jml_bulan' => $frm->getStatus() == '00' ? (string) $billqty : "",
            'tarif' => $frm->getStatus() == '00' ? (string) $tarif : "",
            'daya' => $frm->getStatus() == '00' ? (string) ltrim($daya,'0') : "",
            'refnum' => $frm->getStatus() == '00' ? (string) $ref : "",
            'stanawal' => $frm->getStatus() == '00' ? (string) ltrim($stanawal,'0') : "",
            'stanakhir' => $frm->getStatus() == '00' ? (string) ltrim($stanakhir,'0') : "",
            'infoteks' => $frm->getStatus() == '00' ? (string) $infoteks : "",
        );
        
    } else if($kdproduk==KodeProduk::getPLNPrepaid() || in_array($kdproduk,KodeProduk::getPLNPrepaids()) || substr(strtoupper($kdproduk),0,6) == 'PLNPRA'){
        $meterai = getValue2(trim($frm->getStampDuty()), trim($frm->getMinorUnitStampDuty()));
        $ppn = getValue2($frm->getPPN(), $frm->getMinorUnitPPN());
        $tarif = trim($frm->getSubscriberSegmentation()); 
        $daya = trim($frm->getPowerConsumingCategory()); 
        $ppj= getValue2($frm->getPPJ(), $frm->getMinorUnitPPJ());
        $ref = trim($frm->getNoRef2());
        $angsuran = getValue2($frm->getCustomerPayablesInstallment(),$frm->getMinorUnitCustomerPayablesInstallment);                        
        $pp = getValue2(trim($frm->getPowerPurchase()), trim($frm->getMinorUnitOfPowerPurchase()));
        $kwh = getValue2(trim($frm->getPurchasedKWHUnit()), trim($frm->getMinorUnitOfPurchasedKWHUnit()));
        $token = trim($frm->getTokenPln());
        $nomortoken = $jenistrx == 0 ? "" : substr($token,0,4)." ".substr($token,4,4)." ".substr($token,8,4)." ".substr($token,12,4)." ".substr($token,16,4);
        $infoteks = trim($frm->getInfoText());
       
             return array(
                'METERAI' => $frm->getStatus() == '00' ? (string) $meterai : "",
                'PPN' => $frm->getStatus() == '00' ? (string) $ppn : "",
                'TARIF' => $frm->getStatus() == '00' ? (string) $tarif : "",
                'DAYA' => $frm->getStatus() == '00' ? (string) ltrim($daya,'0') : "",
                'PPJ' => $frm->getStatus() == '00' ? (string) $ppj : "",
                'REFNUM' => $frm->getStatus() == '00' ? (string) $ref : "",
                'ANGSURAN' => $frm->getStatus() == '00' ? (string) $angsuran : "",
                'RPTOKEN' => $frm->getStatus() == '00' ? (string) $pp : "",
                'KWH' => $frm->getStatus() == '00' ? (string) $kwh : "",
                'NOMORTOKEN' => $frm->getStatus() == '00' ? (string) $nomortoken : "",
                'INFOTEXT' => $frm->getStatus() == '00' ? (string) $infoteks : "",
            );
        
    } else if($kdproduk==KodeProduk::getPLNNontaglist() || in_array($kdproduk,KodeProduk::getPLNNontaglists()) || substr(strtoupper($kdproduk),0,6) == 'PLNNON'){
        // echo "<pre>",print_r($frm),"</pre>";
        return array(
            'refnum' => $frm->getStatus() == '00' ? (string) trim($frm->getSwRefNumber()) : "",
            'transaction_code' => $frm->getStatus() == '00' ? (string) trim($frm->getTransactionCode()) : "",
            'transaction_nama' => $frm->getStatus() == '00' ? (string) trim($frm->getTransactionName()) : "",
            'registration_date' => $frm->getStatus() == '00' ? (string) trim($frm->getRegistrationDate()) : "",
        );
    } else if(in_array($kdproduk,KodeProduk::getTelkomSpeedy())){
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
            'refnum' => $frm->getStatus() == '00' ? (string) trim($ref) : '',
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
       
        return array(
            'jml_bln' => $frm->getStatus() == '00' ? (string) trim($qty) : '',
            'stan_awal' => $frm->getStatus() == '00' ? (string) trim($awal) : '',
            'stan_akhir' => $frm->getStatus() == '00' ? (string) trim($akhir) : '',
        );
       
    } else if(substr(strtoupper($kdproduk), 0,9) == "ASRBPJSKS"){
        $id_biller = cekBiller($frm->getIdTrx());
        $novakel = '';
        $jmlkel = '';
        if($id_biller == '201'){ // biller jatis
            $gwrefnum = trim($frm->getNoref1());
            $expld = explode('.', $gwrefnum);
            $novakel = $expld[1];
            $jmlkel = $expld[0];
        }
        // echo "<pre>",print_r($frm),"</pre>";die();
        return array(
            'no_va_keluarga' => $frm->getStatus() == '00' ? (string) trim($novakel) : '',
            'jml_keluarga' => $frm->getStatus() == '00' ? (string) trim($jmlkel) : '',
            'no_hp' => $frm->getStatus() == '00' ? (string) trim($frm->getCustomerPhoneNumber()) : '',
            'no_ref' => $frm->getStatus() == '00' ? (string) trim($frm->getBillerRefNumber()) : '',
        );
    } else if($kdproduk === 'ASRCAR'){
        // echo "<pre>",print_r($frm),"</pre>";
        return array(
            'no_polish' => $frm->getStatus() == '00' ? (string) trim($frm->getCustomerPhoneNumber()) : '',
        );
    } else if(in_array($kdproduk, KodeProduk::getMultiFinance())){

        // echo "<pre>",print_r($frm),"</pre>";
        return array(
            'jatuh_tempo' => $frm->getStatus() == '00' ? (string) trim($frm->getLastPaidDueDate()) : '',
            'type' => $frm->getStatus() == '00' ? (string) trim($frm->getItemMerkType()) : '',
            'tenor' => $frm->getStatus() == '00' ? (string) trim($frm->getTenor()) : '',
            'car_number' => $frm->getStatus() == '00' ? (string) trim($frm->getCarNumber()) : '',
        );
    } else if($kdproduk === 'PGN'){
        // echo "<pre>",print_r($frm),"</pre>";
        return array(
            'aj_ref'=> $frm->getStatus() == '00' ? (string) trim($frm->getRefId()) : '',
            'usage' => $frm->getStatus() == '00' ? (string) trim($frm->getUsage()) : '',
        );
    }  else if($kdproduk === 'GAS'){
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
    } else if(in_array($kdproduk, KodeProduk::getKartuKredit())){
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
    } else if(substr(strtoupper($kdproduk), 0,5) == "BLTRF" || $kdproduk == 'BLTRFMDR'){
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

        $r_nama_bank = getnamabank($r_kode_bank_tmp);
        if(strtoupper($frm->getMember()) == 'SP193969'){
            if($r_nama_bank  == 'BANK BNI'){
                if($r_kode_bank_tmp != '009')
                {
                    $r_kode_bank_tmp = "009";
                }
            }
        }
        return array(
            'nama_bank' => $frm->getStatus() == '00' ? $r_nama_bank : '',
            'kode_bank' => $frm->getStatus() == '00' ? $r_kode_bank_tmp : '',
        );
    }
}

function tambahdataproduk2_arranet($kdproduk, $frm){
    $r_nama_pelanggan = getNamaPelanggan($kdproduk, $frm);
    if (strpos($r_nama_pelanggan, '\'') !== false) {
        $r_nama_pelanggan = str_replace('\'', "`", $r_nama_pelanggan);
    }
    if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $r_periode_tagihan = getLastPaidPeriode($kdproduk, $frm);
    } else {
        $r_periode_tagihan = getBillPeriod($kdproduk, $frm);
    }

    if(in_array($kdproduk, KodeProduk::getPonselPostpaid()) || substr(strtoupper($kdproduk), 0,7) == "PLNPASC"){
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : "",
        );
    }elseif ($kdproduk==KodeProduk::getPLNNontaglist() || in_array($kdproduk,KodeProduk::getPLNNontaglists())  || substr(strtoupper($kdproduk),0,6) == 'PLNNON') {
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : "",
        );
    } else if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : "",
            'angsuran_ke' => $frm->getStatus() == '00' ? (string) $r_periode_tagihan : "",
        );
    } else if(in_array($kdproduk, KodeProduk::getKartuKredit())){
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
    } else if(substr(strtoupper($kdproduk), 0,5) == "BLTRF" || strtoupper($kdproduk) == 'BLTRFMDR'){
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : ""
        );
    } else if(substr(strtoupper($kdproduk), 0,5) == "PAJAK"){
        $customer_name = trim($frm->getNama());
        return array(
            'nama_pelanggan' => $frm->getStatus() == '00' ? $customer_name : '',
            
        );
    } else{
        return array(
            'NAMA_PELANGGAN' => $frm->getStatus() == '00' ? (string) $r_nama_pelanggan : "",
        );
    }

}

function tambahdataproduk3($kdproduk,$frm)
{
    $hasil = array();
    if($kdproduk==KodeProduk::getPLNPostpaid() || in_array($kdproduk,KodeProduk::getPLNPostpaids()) || substr(strtoupper($kdproduk),0,7) == "PLNPASC" ){

        $jmlbil = trim($frm->getJumlahBill());
        for($i=0;$i<$jmlbil;$i++)
        {
            if($i == 0){
                $bln1    = $frm->getBillPeriod1();
                $tagihan1 = $frm->getRpTag1();
                $denda1   = $frm->getPenaltyFee1();
                $stanmeter1 = $frm->getPreviousMeterReading11()." - ".$frm->getCurentMeterReading11();
                $dt1 = array(
                    'bln'=>$bln1,
                    'tagihan' => ltrim($tagihan1,'0'),
                    'denda' => ltrim($denda1,'0'),
                    'stanmeter' => $stanmeter1
                );
                $hasil[] = $dt1;
            }elseif ($i == 1) {
                $bln2    = $frm->getBillPeriod2();
                $tagihan2 = $frm->getRpTag2();
                $denda2   = $frm->getPenaltyFee2();
                $stanmeter2 = $frm->getPreviousMeterReading12()." - ".$frm->getCurentMeterReading12();
                $dt2 = array(
                    'bln'=>$bln2,
                    'tagihan' => ltrim($tagihan2,'0'),
                    'denda' => ltrim($denda2,'0'),
                    'stanmeter' => $stanmeter2
                );
                $hasil[] = $dt2;
            }elseif ($i == 2) {
                $bln3    = $frm->getBillPeriod3();
                $tagihan3 = $frm->getRpTag3();
                $denda3   = $frm->getPenaltyFee3();
                $stanmeter3 = $frm->getPreviousMeterReading13()." - ".$frm->getCurentMeterReading13();
                $dt3 = array(
                    'bln'=>$bln3,
                    'tagihan' => ltrim($tagihan3,'0'),
                    'denda' => ltrim($denda3,'0'),
                    'stanmeter' => $stanmeter3
                );
                $hasil[] = $dt3;
            }elseif ($i == 3) {
                $bln4    = $frm->getBillPeriod4();
                $tagihan4 = $frm->getRpTag4();
                $denda4   = $frm->getPenaltyFee4();
                $stanmeter4 = $frm->getPreviousMeterReading14()." - ".$frm->getCurentMeterReading14();
                $dt4 = array(
                    'bln'=>$bln4,
                    'tagihan' => ltrim($tagihan4,'0'),
                    'denda' => ltrim($denda4,'0'),
                    'stanmeter' => $stanmeter4
                );
               $hasil[] = $dt4;
            }

        }
    }

    return array('detail'=>$hasil);
}

function templateret($kdproduk, $params, $mti="", $frm=""){
    
    if (in_array($kdproduk, KodeProduk::getTelkom())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "KODEAREA", "NOMORTELEPON", "KODEDIVRE", "KODEDATEL", "JUMLAHBILL", "NOMORREFERENSI3", "NILAITAGIHAN3", "NOMORREFERENSI2", "NILAITAGIHAN2", "NOMORREFERENSI1", "NILAITAGIHAN1", "NAMAPELANGGAN", "NPWP");
    } else if (in_array($kdproduk, KodeProduk::getPLNPostpaids())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "SUBSCRIBERID", "BILLSTATUS", "PAYMENTSTATUS", "TOTALOUTSTANDINGBILL", "SWREFERENCENUMBER", "SUBSCRIBERNAME", "SERVICEUNIT", "SERVICEUNITPHONE", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "TOTALADMINCHARGE", "BLTH1", "DUEDATE1", "METERREADDATE1", "RPTAG1", "INCENTIVE1", "VALUEADDEDTAX1", "PENALTYFEE1", "SLALWBP1", "SAHLWBP1", "SLAWBP1", "SAHWBP1", "SLAKVARH1", "SAHKVARH1", "BLTH2", "DUEDATE2", "METERREADDATE2", "RPTAG2", "INCENTIVE2", "VALUEADDEDTAX2", "PENALTYFEE2", "SLALWBP2", "SAHLWBP2", "SLAWBP2", "SAHWBP2", "SLAKVARH2", "SAHKVARH2", "BLTH3", "DUEDATE3", "METERREADDATE3", "RPTAG3", "INCENTIVE3", "VALUEADDEDTAX3", "PENALTYFEE3", "SLALWBP3", "SAHLWBP3", "SLAWBP3", "SAHWBP3", "SLAKVARH3", "SAHKVARH3", "BLTH4", "DUEDATE4", "METERREADDATE4", "RPTAG4", "INCENTIVE4", "VALUEADDEDTAX4", "PENALTYFEE4", "SLALWBP4", "SAHLWBP4", "SLAWBP4", "SAHWBP4", "SLAKVARH4", "SAHKVARH4", "ALAMAT", "PLNNPWP", "SUBSCRIBERNPWP", "TOTALRPTAG", "INFOTEKS");
    }  else if (in_array($kdproduk, KodeProduk::getPLNPrepaids())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "NOMORMETER", "IDPELANGGAN", "FLAG", "NOREF1", "NOREF2", "VENDINGRECEIPTNUMBER", "NAMAPELANGGAN", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "MINORUNITOFADMINCHARGE", "ADMINCHARGE", "BUYINGOPTION", "DISTRIBUTIONCODE", "SERVICEUNIT", "SERVICEUNITPHONE", "MAXKWHLIMIT", "TOTALREPEATUNSOLDTOKEN", "UNSOLD1", "UNSOLD2", "TOKENPLN", "MINORUNITSTAMPDUTY", "STAMPDUTY", "MINORUNITPPN", "PPN", "MINORUNITPPJ", "PPJ", "MINORUNITCUSTOMERPAYABLESINSTALLMENT", "CUSTOMERPAYABLESINSTALLMENT", "MINORUNITOFPOWERPURCHASE", "POWERPURCHASE", "MINORUNITOFPURCHASEDKWHUNIT", "PURCHASEDKWHUNIT", "INFOTEXT");
    } else if (in_array($kdproduk, KodeProduk::getPLNNontaglists())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "REGISTRATIONNUMBER", "AREACODE", "TRANSACTIONCODE", "TRANSACTIONNAME", "REGISTRATIONDATE", "EXPIRATIONDATE", "SUBSCRIBERID", "SUBSCRIBERNAME", "PLNREFNUMBER", "SWREFNUMBER", "SERVICEUNIT", "SERVICEUNITADDRESS", "SERVICEUNITPHONE", "TOTALTRANSACTIONAMOUNTMINOR", "TOTALTRANSACTIONAMOUNT", "PLNBILLMINORUNIT", "PLNBILLVALUE", "ADMINCHARGEMINORUNIT", "ADMINCHARGE", "MUTATIONNUMBER", "SUBSCRIBERSEGMENTATION", "POWERCONSUMINGCATEGORY", "INQUIRYREFERENCENUMBER", "TOTALREPEAT", "CUSTOMERDETAILCODE1", "CUSTOMDETAILMINORUNIT1", "CUSTOMDETAILVALUEAMOUNT1", "CUSTOMERDETAILCODE2", "CUSTOMDETAILMINORUNIT2", "CUSTOMDETAILVALUEAMOUNT2", "INFOTEXT");
    } else if (in_array($kdproduk, KodeProduk::getPonselPostpaid())) {
        $array = array( "KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PROVIDERNAME", "MONTHPERIOD1", "YEARPERIOD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3");
    } else if ( (in_array($kdproduk, KodeProduk::getPAM()) || substr($kdproduk,0,2) === 'WA') && $kdproduk !== "WASBY" ) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "MONTHPERIOD1", "YEARPERIOD1", "FIRSTMETERREAD1", "LASTMETERREAD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "FIRSTMETERREAD2", "LASTMETERREAD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "FIRSTMETERREAD3", "LASTMETERREAD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "FIRSTMETERREAD4", "LASTMETERREAD4", "PENALTY4", "BILLAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "FIRSTMETERREAD5", "LASTMETERREAD5", "PENALTY5", "BILLAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "FIRSTMETERREAD6", "LASTMETERREAD6", "PENALTY6", "BILLAMOUNT6", "MISCAMOUNT6");
    }  else if (in_array($kdproduk, KodeProduk::getMultiFinance())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "MINORUNIT", "BILLAMOUNT", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "BRANCHNAME", "ITEMMERKTYPE", "CHASISNUMBER", "CARNUMBER", "TENOR", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "OSINSTALLMENTAMOUNT", "ODINSTALLMENTPERIOD", "ODINSTALLMENTAMOUNT", "ODPENALTYFEE", "BILLERADMINFEE", "MISCFEE", "MINIMUMPAYAMOUNT", "MAXIMUMPAYAMOUNT");
    } else if (in_array($kdproduk, KodeProduk::getAsuransi())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "CLAIMAMOUNT", "BILLERREFNUMBER", "PTNAME", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "CUSTOMERPHONENUMBER", "CUSTOMERADDRESS", "AHLIWARISPHONENUMBER", "AHLIWARISADDRESS");
    } else if (in_array($kdproduk, KodeProduk::getKartuKredit())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "LASTPAIDPERIODE", "LASTPAIDDUEDATE", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "MINIMUMPAYAMOUNT", "MAXIMUMPAYAMOUNT");
    } else if (in_array($kdproduk, KodeProduk::getNewPAM()) && $kdproduk == "WASBY") {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "GWREFNUM", "SWREFNUM", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERSEGMENTATION", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "STAMPDUTY", "TRANSACTIONFEE", "OTHERFEE", "MONTHPERIOD1", "YEARPERIOD1", "METERUSAGE1", "STAND1", "FIRSTMETERREAD1", "LASTMETERREAD1", "BILLAMOUNT1", "PENALTY1", "BURDENAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "METERUSAGE2", "STAND2", "FIRSTMETERREAD2", "LASTMETERREAD2", "BILLAMOUNT2", "PENALTY2", "BURDENAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "METERUSAGE3", "STAND3", "FIRSTMETERREAD3", "LASTMETERREAD3", "BILLAMOUNT3", "PENALTY3", "BURDENAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "METERUSAGE4", "STAND4", "FIRSTMETERREAD4", "LASTMETERREAD4", "BILLAMOUNT4", "PENALTY4", "BURDENAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "METERUSAGE5", "STAND5", "FIRSTMETERREAD5", "LASTMETERREAD5", "BILLAMOUNT5", "PENALTY5", "BURDENAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "METERUSAGE6", "STAND6", "FIRSTMETERREAD6", "LASTMETERREAD6", "BILLAMOUNT6", "PENALTY6", "BURDENAMOUNT6", "MISCAMOUNT6");
    } else if (in_array($kdproduk, KodeProduk::getTVKabel())) {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "CUSTOMERNAME", "CUSTOMERADDRESS", "PRODUCTCATEGORY", "BILLAMOUNT", "PENALTY", "STAMPDUTY", "PPN", "ADMINCHARGE", "BILLERREFNUMBER", "PTNAME", "BILLERADMINFEE", "MISCFEE", "MISCNUMBER", "PERIODE", "DUEDATE", "CUSTOMINFO1", "CUSTOMINFO2", "CUSTOMINFO3");
    } else if ($kdproduk =="IKLNBRS") {
        $array = array();
    } else if (substr($kdproduk, 0, 3) == 'PKB') {
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "KND_NOPOL", "KND_ID", "KND_DF_JENIS", "KND_NAMA", "KND_DF_NOMOR", "KND_DF_TANGGAL", "KND_DF_JAM", "KND_DF_PROSES", "USR_FULL_NAME", "KND_KOHIR", "KND_SKUM", "KND_ALAMAT", "KEL_DESC", "KEC_DESC", "KAB_DESC", "TPE_DESC", "KD_MERK", "MRK_DESC", "JNS_DESC", "KND_THN_BUAT", "KND_CYL", "KND_WARNA", "KND_RANGKA", "KND_MESIN", "KND_NO_BPKB", "KND_SD_NOTICE", "KND_TGL_STNK", "KND_SD_STNK", "KD_BBM", "BBM_DESC", "WRN_DESC", "KND_NOPOL_EKS", "KND_JBB_PENUMPANG", "KND_BERAT_KB", "KND_JML_SUMBU_AS", "KD_KAB", "KD_JENIS", "KD_TIPE", "BOBOT", "NILAI_JUAL", "DASAR_PKB", "KD_GOL", "GOL_DESC", "TGLBERLAKU", "POKOK_NEW", "POKOK_OLD", "DENDA_NEW", "DENDA_OLD", "KND_MILIK_KE", "KD_KEC", "KD_KEL", "KD_GUNA", "KND_BLOKIR", "KND_TGL_FAKTUR", "KND_TGL_KUWITANSI", "KND_BLOKIR_TGL", "KND_BLOKIR_DESC", "DRV_DESC", "BILL_QUANTITY", "REFF_NUM", "ROW_ID", "PTP_TANGGAL", "NOM_PKB", "JASARAHARJA", "DENDA_NOM_PKB", "DENDA_JASARAHARJA", "NOM_PKB_TG", "JASARAHARJA_TG", "DENDA_NOM_PKB_TG", "DENDA_JASARAHARJA_TG");
    } else if(substr($kdproduk, 0, 5) == 'PAJAK'){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID", "BILLQUANTITY", "NOREF1", "NOREF2", "NTP", "NTB", "KODE_PEMDA", "NOP", "KODE_PAJAK", "TAHUN_PAJAK", "NAMA", "LOKASI", "KELURAHAN", "KECAMATAN", "PROVINSI", "LUAS_TANAH", "LUAS_BANGUNAN", "TANGGAL_JTH_TEMPO", "TAGIHAN", "DENDA", "TOTAL_BAYAR");
    } else if($kdproduk == "PGN"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "CUSTOMERID", "CUSTOMERNAME", "USAGE", "PERIODE", "INVOICENUMBER", "TAGIHAN", "ADMINBANK", "TOTAL", "CHARGE", "SALDO", "REFFID", "TRXID");
    } else if(substr($kdproduk, 0, 2) == "RZ"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "PROVIDER_NAME","NAMA_PRODUK","NO_KTP","NO_TELPON","NAMA","ALAMAT","ID_PROPINSI","ID_KOTA","KODE_POS","WAKTU_SURVEY_AWAL1","WAKTU_SURVEY_AKHIR1","WAKTU_SURVEY_AWAL2","WAKTU_SURVEY_AKHIR2","WAKTU_SURVEY_AWAL3","WAKTU_SURVEY_AKHIR3","CONTACT_PERSON","KECAMATAN","DESA");
    } else if($kdproduk == "GAS"){
        $array = array("KODEPRODUK", "WAKTU", "IDPELANGGAN1", "IDPELANGGAN2", "IDPELANGGAN3", "NOMINAL", "BIAYAADMIN", "UID", "PIN", "REF1", "REF2", "REF3", "STATUS", "KETERANGAN", "FEE", "SALDOTERPOTONG", "SWITCHERID", "BILLERCODE", "CUSTOMERID1", "CUSTOMERID2", "CUSTOMERID3", "BILLQUANTITY", "GWREFNUM", "SWREFNUM", "CUSTOMERNAME", "CUSTOMERADDRESS", "CUSTOMERDETAILINFORMATION", "BILLERADMINCHARGE", "TOTALBILLAMOUNT", "PDAMNAME", "MONTHPERIOD1", "YEARPERIOD1", "FIRSTMETERREAD1", "LASTMETERREAD1", "PENALTY1", "BILLAMOUNT1", "MISCAMOUNT1", "MONTHPERIOD2", "YEARPERIOD2", "FIRSTMETERREAD2", "LASTMETERREAD2", "PENALTY2", "BILLAMOUNT2", "MISCAMOUNT2", "MONTHPERIOD3", "YEARPERIOD3", "FIRSTMETERREAD3", "LASTMETERREAD3", "PENALTY3", "BILLAMOUNT3", "MISCAMOUNT3", "MONTHPERIOD4", "YEARPERIOD4", "FIRSTMETERREAD4", "LASTMETERREAD4", "PENALTY4", "BILLAMOUNT4", "MISCAMOUNT4", "MONTHPERIOD5", "YEARPERIOD5", "FIRSTMETERREAD5", "LASTMETERREAD5", "PENALTY5", "BILLAMOUNT5", "MISCAMOUNT5", "MONTHPERIOD6", "YEARPERIOD6", "FIRSTMETERREAD6", "LASTMETERREAD6", "PENALTY6", "BILLAMOUNT6", "MISCAMOUNT6");
    }
        
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
            $string = $params[10];
            $url = enkripinew($string,'e');
            $url_struk = "https://202.43.173.234/struk/?key=".$url;
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
        if($params[7] === 'HH95173'){//ewako
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
        if($params[7] === 'HH95173'){//ewako
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
        $params[5] = (string)substr(intval($params[31]), 0 , -2);

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
    } else if (in_array($kdproduk, KodeProduk::getKartuKredit())) {
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
    }else if($kdproduk == "ASRBPJSKS"){ 
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["inq"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);

    }else if($kdproduk == "ASRBPJSKSD"){ 
        $format = FormatMsg::asuransi();
        $frm = new FormatAsuransi($man["inq"] . "*" . $format, $resp);
        $params = retAsuransi($p_params, $frm);
    }else if (substr($kdproduk, 0, 3) == 'PKB') {
        $format = FormatMsg::pkb();
        $frm = new FormatPKB($man["inq"] . "*" . $format, $resp);
        $params = retPKB($p_params, $frm);
    } else if($kdproduk == "PGN"){
        $format = FormatMsg::pgn();
        $frm = new FormatPgn($man["inq"]."*".$format,$resp);
        $params = retPgn($p_params,$frm);
    } else if(substr($kdproduk, 0, 2) == "KK") {
        $format = FormatMsg::kartuKredit();
        $frm = new FormatKartuKredit($man["inq"] . "*" . $format, $resp);
        $params = retKartuKredit($p_params, $frm);
    } else if($kdproduk == "GAS") {
        $format = FormatMsg::gas();
        $frm = new FormatGas($man["inq"] . "*" . $format, $resp);
        $params = retGas($p_params, $frm);
    } else if(substr($kdproduk, 0, 5) == 'PAJAK'){
        $format = FormatMsg::pajakSolo();
        $frm = new FormatPajakPbb($man["inq"]."*".$format,$resp);
        $params = retPajakPbb($p_params,$frm);
    } 

    return $params;
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

//=================== function bpjstk start =============================

function postValueBpjsTk($msg, $isdev, $timeout = 300){  

    // $ip = "127.0.0.1";
    // $url = "/FMSSWeb/mpin1";
    // $port = 8080;
    $ip = "10.0.0.88";
    $port = 21081;
    $url = "/FMSSWeb2/mpin1";

    if($isdev){
        $ip = "10.0.9.88";
        $port = 21080;
        $url = "/FMSSWeb2/mpin1";
    }

    $result = array();
    $data = '';
    $errno = 0;
    $error = '';
    // echo  $GLOBALS["__CFG_urltargetport"];
    $url = "http://". $ip . $url;
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PORT, $port); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);

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

function cek_trx_bpjstk($idtrx, $idoutlet, $is_prod){
    if($is_prod){
        $db = reconnect_ro();
    } else {
        $db = reconnect_D();
    }
    
    $q = "select * FROM transaksi WHERE id_transaksi = $1 and id_outlet = $2 and jenis_transaksi = 0 and response_code = '00' and transaction_date = now()::date";   
    $bind = array();
    $bind[] = strtoupper($idtrx);
    $bind[] = strtoupper($idoutlet);
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);

    if ($n > 0){
        $r = pg_fetch_object($e);
        $arr = array(
            'kode_bayar' => $r->bill_info18,
            'jam_awal' => $r->bill_info25,
            'jam_akhir' => $r->bill_info26,
            'jenis_kerja' => $r->bill_info24,
            'lokasi_kerja' => $r->bill_info28,
            'biaya_reg' => $r->bill_info43,
            'nominal' => $r->nominal,
            'kd_iuran' => $r->bill_info54, // gawe pay pu eps
            'jht' => $r->bill_info35,
            'jkk' => $r->bill_info37,
            'jkm' => $r->bill_info39,
        );
        $ret = $arr;
    }else{
        $ret = array();
    }
    pg_free_result($e);
    pg_close($db);
    return $ret;
}

//=================== function bpjstk end =============================


function getDataProsesTransaksiv2($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= ""){
    $db = reconnect();
    $bind= array();
    $tgl1 = date('Y-m-d',strtotime($tgl1));
    $tgl2 = date('Y-m-d',strtotime($tgl2));
    $bind[] = $idoutlet;
    $bind[] = $tgl1;
    $bind[] = $tgl2;
    //optimise query ==> BETWEEN ganti IN <====
    $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, REGEXP_REPLACE(t.keterangan, '([^[SYSTEM][A-Z a-z 0-9]+])', 'admin]') AS keterangan
    FROM proses_transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
    WHERE t.transaction_date in ($2 , $3) 
    AND t.id_outlet=$1 and  t.jenis_transaksi = 1";

    if($idtrx != ""){
        $q .= " and t.id_transaksi=$4 ";
        $bind[] = $idtrx;
        // langsung bawah ..............
    } else {
        $q .= datatransaksisaatiniv1($idproduk, $idpel , $custreff);

        if ($idproduk !="" && $idpel == "" && $custreff == "") {
            $bind[] = $idproduk;
        }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
            $bind[] = $idpel;
        }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
            $bind[] = $custreff;
        }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
            $bind[] = $idproduk;
            $bind[] = $idpel;
        }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
            $bind[] = $idproduk;
            $bind[] = $custreff;
        }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
            $bind[] = $idpel;
            $bind[] = $custreff;
        }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
            $bind[] = $idpel;
            $bind[] = $idproduk;
            $bind[] = $custreff;
        }
    
    }

    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;
    if($_GET['q'] == '2'){
        print_r($bind);
        echo $q;
    }
    $e = pg_query_params($db,$q,$bind);
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
    pg_close($db);
    return $out;
}

function dataprosestransaksi($idtrx, $idproduk = "", $idpel = "", $custreff= "")
{
    $q = "";
    if($idtrx != "" && $idproduk=="" && $idpel == "" && $custreff == ""){
        $q .= " AND t.id_transaksi=$4 ";
    }elseif ($idtrx == "" && $idproduk !="" && $idpel == "" && $custreff == "") {
        $q .= " AND t.id_produk=$4";
    }elseif ($idtrx == "" && $idproduk =="" && $idpel != "" && $custreff == "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4 )";
    }elseif ($idtrx == "" && $idproduk =="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.bill_info83=$4";
    }elseif($idtrx != "" && $idproduk!="" && $idpel == "" && $custreff == ""){
        $q .= " AND t.id_transaksi=$4 ";
        $q .= " AND t.id_produk=$5";
    }elseif ($idtrx != "" && $idproduk =="" && $idpel != "" && $custreff == "") {
        $q .= " AND t.id_transaksi=$4";
        $q .= " AND (t.bill_info1=$5 or t.bill_info2=$5 )";
    }elseif ($idtrx != "" && $idproduk =="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.id_transaksi=$4";
        $q .= " AND t.bill_info83=$5";
    }elseif ($idtrx == "" && $idproduk !="" && $idpel != "" && $custreff == "") {
        $q .= " AND t.id_produk=$4";
        $q .= " AND (t.bill_info1=$5 or t.bill_info2=$5)";
    }elseif ($idtrx == "" && $idproduk !="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.id_produk=$4";
        $q .= " AND t.bill_info83=$5";
    }elseif ($idtrx == "" && $idproduk =="" && $idpel != "" && $custreff != "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4)";
        $q .= " AND t.bill_info83=$5";
    }elseif($idtrx != "" && $idproduk !="" && $idpel != "" && $custreff == ""){
        $q .= " AND t.id_transaksi=$4 ";
        $q .= " AND t.id_produk=$5";
        $q .= " AND (t.bill_info1=$6 or t.bill_info2=$6)";
    }elseif ($idtrx != "" && $idproduk !="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.id_transaksi=$4";
        $q .= " AND t.id_produk=$5";
        $q .= " AND t.bill_info83=$6";
    }elseif ($idtrx == "" && $idproduk !="" && $idpel != "" && $custreff != "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4)";
        $q .= " AND t.id_produk=$5";
        $q .= " AND t.bill_info83=$6";
    }elseif ($idtrx != "" && $idproduk !="" && $idpel != "" && $custreff != "") {
        $q .= " AND t.id_transaksi=$4";
        $q .= " AND t.id_produk=$5";
        $q .= " AND (t.bill_info1=$6 or t.bill_info2=$6)";
        $q .= " AND t.bill_info83=$7";
    }

    return $q;
}

function getDataTransaksiv2($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= ""){

    $db = reconnect();
    $bind= array();
    $tgl1 = date('Y-m-d',strtotime($tgl1));
    $tgl2 = date('Y-m-d',strtotime($tgl2));
    $bind[] = $idoutlet;
    $bind[] = $tgl1;
    $bind[] = $tgl2;

    $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, t.keterangan AS keterangan
    FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
    WHERE t.transaction_date in ($2 , $3) 
    AND t.id_outlet=$1 and  t.jenis_transaksi = 1";

    if($idtrx != ""){
        $q .= " AND t.id_transaksi=$4 ";
        $bind[] = $idtrx;
        // langsung bawah ...............
    } else {
        $q .= datatransaksisaatiniv1($idproduk, $idpel , $custreff);

        if ($idproduk !="" && $idpel == "" && $custreff == "") {
            $bind[] = $idproduk;
        }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
            $bind[] = $idpel;
        }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
            $bind[] = $custreff;
        }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
            $bind[] = $idproduk;
            $bind[] = $idpel;
        }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
            $bind[] = $idproduk;
            $bind[] = $custreff;
        }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
            $bind[] = $idpel;
            $bind[] = $custreff;
        }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
            $bind[] = $idpel;
            $bind[] = $idproduk;
            $bind[] = $custreff;
        }
    
    }

	$q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;
    if($_GET['q'] == '2'){
        print_r($bind);
        echo $q;
    }
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);

    if($n > 0)
    {
        $out = array();
        $i = 0;
        if ($n > 0) {
            while ($data = pg_fetch_object($e)) {
                $out[$i] = $data;
                $i++;
            }
        }
    }else{
        $out = getDataTransaksiBackup1v2($idoutlet, $idtrx, $idproduk, $idpel, $limit, $tgl1, $tgl2, $custreff);
        
    }
    
    pg_free_result($e);
    pg_close($db);
    return $out;
}

function getDataTransaksiBackup1v2($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= "")
{
    $db = reconnect();
    $bind= array();
    $tgl1 = date('Y-m-d',strtotime($tgl1));
    $tgl2 = date('Y-m-d',strtotime($tgl2));
    $bind[] = $idoutlet;
    $bind[] = $tgl1;
    $bind[] = $tgl2;


    $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, t.keterangan AS keterangan
FROM transaksi_backup t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.transaction_date in ($2, $3)
AND t.id_outlet=$1 and  t.jenis_transaksi = 1";

    if($idtrx != ""){
        $q .= " AND t.id_transaksi=$4 ";
        $bind[] = $idtrx;
        // langsung bawah ...............
    } else {
        $q .= datatransaksisaatiniv1($idproduk, $idpel , $custreff);

        if ($idproduk !="" && $idpel == "" && $custreff == "") {
            $bind[] = $idproduk;
        }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
            $bind[] = $idpel;
        }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
            $bind[] = $custreff;
        }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
            $bind[] = $idproduk;
            $bind[] = $idpel;
        }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
            $bind[] = $idproduk;
            $bind[] = $custreff;
        }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
            $bind[] = $idpel;
            $bind[] = $custreff;
        }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
            $bind[] = $idpel;
            $bind[] = $idproduk;
            $bind[] = $custreff;
        }
        
    }


    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;
    if($_GET['q'] == '2'){
        print_r($bind);
        echo $q;
    }
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);

    if($n > 0)
    {
        $out = array();
        $i = 0;
        if ($n > 0) {
            while ($data = pg_fetch_object($e)) {
                $out[$i] = $data;
                $i++;
            }
        }
    }

    pg_free_result($e);
    pg_close($db);
    return $out;
}

function getDataTransaksi1v2($idoutlet, $idtrx, $idproduk = "", $idpel = "", $limit = "", $tgl1, $tgl2, $custreff= "")
{
    $db = reconnect();
    $bind= array();
    $bind[] = $idoutlet;
    $bind[] = $tgl1;
    $bind[] = $tgl2;

    $q = "select t.id_transaksi, t.mid, t.id_biller, to_char(t.time_request,'YYYYMMDDHH24MISS') transaksidatetime, p.id_produk, p.produk namaproduk, t.bill_info1 idpelanggan, (COALESCE(t.nominal,0)+COALESCE(t.nominal_admin,0)+COALESCE(t.nominal_up,0)) nominal, t.bill_info5 sn, t.bill_info29 token, t.response_code, t.keterangan AS keterangan
FROM transaksi t LEFT JOIN mt_produk p ON(t.id_produk=p.id_produk)
WHERE t.transaction_date in ($2 , $3)
AND t.id_outlet=$1 and  t.jenis_transaksi = 1";

    if($idtrx != ""){
        $q .= " AND t.id_transaksi=$4 ";
        $bind[] = $idtrx;
        // langsung bawah ...............
    } else {
        $q .= datatransaksisaatiniv1($idproduk, $idpel , $custreff);

        if ($idproduk !="" && $idpel == "" && $custreff == "") {
            $bind[] = $idproduk;
        }elseif ($idproduk =="" && $idpel != "" && $custreff == "") {
            $bind[] = $idpel;
        }elseif ($idproduk =="" && $idpel == "" && $custreff != "") {
            $bind[] = $custreff;
        }elseif ($idproduk !="" && $idpel != "" && $custreff == "") {
            $bind[] = $idproduk;
            $bind[] = $idpel;
        }elseif ($idproduk !="" && $idpel == "" && $custreff != "") {
            $bind[] = $idproduk;
            $bind[] = $custreff;
        }elseif ($idproduk =="" && $idpel != "" && $custreff != "") {
            $bind[] = $idpel;
            $bind[] = $custreff;
        }elseif ($idproduk !="" && $idpel != "" && $custreff != "") {
            $bind[] = $idpel;
            $bind[] = $idproduk;
            $bind[] = $custreff;
        }
    }

    $q = $q . " ORDER BY id_transaksi DESC ";
    $limit != "" ? (is_numeric($limit) ? $q .= " LIMIT ".$limit : $q = $q) : $q = $q;
    if($_GET['q'] == '2'){
        print_r($bind);
        echo $q;
    }
    $e = pg_query_params($db,$q,$bind);
    $n = pg_num_rows($e);

    if($n > 0)
    {
        $out = array();
        $i = 0;
        if ($n > 0) {
            while ($data = pg_fetch_object($e)) {
                $out[$i] = $data;
                $i++;
            }
        }
    }

    pg_free_result($e);
    pg_close($db);
    return $out;
}

function databackuptransaksi($idtrx, $idproduk = "", $idpel = "", $custreff= "")
{
    $q = "";
    if($idtrx != "" && $idproduk=="" && $idpel == "" && $custreff == ""){
        $q .= " AND t.id_transaksi=$4 ";
    }elseif ($idtrx == "" && $idproduk !="" && $idpel == "" && $custreff == "") {
        $q .= " AND t.id_produk=$4";
    }elseif ($idtrx == "" && $idproduk =="" && $idpel != "" && $custreff == "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4)";
    }elseif ($idtrx == "" && $idproduk =="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.bill_info83=$4";
    }elseif($idtrx != "" && $idproduk!="" && $idpel == "" && $custreff == ""){
        $q .= " AND t.id_transaksi=$4 ";
        $q .= " AND t.id_produk=$5";
    }elseif ($idtrx != "" && $idproduk =="" && $idpel != "" && $custreff == "") {
        $q .= " AND t.id_transaksi=$4";
        $q .= " AND (t.bill_info1=$5 or t.bill_info2=$5)";
    }elseif ($idtrx != "" && $idproduk =="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.id_transaksi=$4";
        $q .= " AND t.bill_info83=$5";
    }elseif ($idtrx == "" && $idproduk !="" && $idpel != "" && $custreff == "") {
        $q .= " AND t.id_produk=$4";
        $q .= " AND (t.bill_info1=$5 or t.bill_info2=$5)";
    }elseif ($idtrx == "" && $idproduk !="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.id_produk=$4";
        $q .= " AND t.bill_info83=$5";
    }elseif ($idtrx == "" && $idproduk =="" && $idpel != "" && $custreff != "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4)";
        $q .= " AND t.bill_info83=$5";
    }elseif($idtrx != "" && $idproduk !="" && $idpel != "" && $custreff == ""){
        $q .= " AND t.id_transaksi=$4 ";
        $q .= " AND t.id_produk=$5";
        $q .= " AND (t.bill_info1=$6 or t.bill_info2=$6)";
    }elseif ($idtrx != "" && $idproduk !="" && $idpel == "" && $custreff != "") {
        $q .= " AND t.id_transaksi=$4";
        $q .= " AND t.id_produk=$5";
        $q .= " AND t.bill_info83=$6";
    }elseif ($idtrx == "" && $idproduk !="" && $idpel != "" && $custreff != "") {
        $q .= " AND (t.bill_info1=$4 or t.bill_info2=$4)";
        $q .= " AND t.id_produk=$5";
        $q .= " AND t.bill_info83=$6";
    }elseif ($idtrx != "" && $idproduk !="" && $idpel != "" && $custreff != "") {
        $q .= " AND t.id_transaksi=$4";
        $q .= " AND t.id_produk=$5";
        $q .= " AND (t.bill_info1=$6 or t.bill_info2=$6)";
        $q .= " AND t.bill_info83=$7";
    }
    return $q;
}


//=================== function bpjstk start =============================

?>
