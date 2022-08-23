<?php

require '../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\Message;

$iso = new Protocol();
$message = new Message($iso, [
    'lengthPrefix' => 5
]);

// Unpacking message
// $data = file_get_contents('php://input');
/* PLNPASC*/
//$data = '02105b0000014a11800c1900000005387317345410000008878490000008998491205241116420001200007FA326702800000000000000005387317345410000181417280025SUCCESSFUL               080rajabiller.paydetail          PLNPASCB       535592709412   00000000002531240404360355PLNPASCH  02012052411164200000000538731734541R SUMALI2                                  201202,201203,201204,20120500008878490012000000000000000000057650000000000001814172800  SUCCESSFUL                    00008998490007811856https://202.43.173.234/struk/?id=Rf%2Fude6DWpWPFMm3nUHbtPMTD%2BQ2vUzD3aerh7X2RHEpLYq3GeiNwxAUqRMIeNcZ6UBsrXJlRA8zLsxz1xw62A%3D%3D110TIDAK DIIJINKAN MENAMBAH CHARGE         R1        000000090000008735000000876600000091990000009733000001031500';
/* PDAM */
// $data = '02105b0000014a11800c1900000000001992009870000000433200000000458201503210752330000250007FA326702800000000000000000001992009870002967029480025SUCCESSFUL               080rajabiller.paydetail          WAMAKASAR      200100946      00000000002531240404360361WAMAKASAR 02015032107523300000000000199200987M. NATSIR                                                       20150200000433200002500000000000000000057650000000000029670294800  SUCCESSFUL                    00000458200000169935https://202.43.173.234/struk/?id=Urqpb04je2GslB7zH26Yc24Ln77mcjnFaig%2FroqCQRH9S%2F%2FRz2naJtVx75sKZ1xMrAOgt9pJz%2Bz7VDVI0%2BWirQ%3D%3D056TIDAK DIIJINKAN MENAMBAH CHARGE         0000351200003521';
/* Multifinance */
// $data = '02105b0000014a11800c1900000000040010688570000005467000000005467002010070623580000000007FA326702800000000000000000040010688570019359983370025EXT: SUCCESS             080rajabiller.paydetail          FNHCI          4100740197     00000000002531240404360359FNHCI     02020100706235800000000004001068857ETIKA ZENDRATO                                                        00005467000000000000000000000000057650000000000193599833700  EXT: SUCCESS                  00005467000162495186https://202.43.173.234/struk/?id=GUBtHfU8NVTK8FRh8k%2Fk3%2FBC2sDgFVTgZugUwu2WrCZRCpTTV%2BTkkw6MLbWJTYJ2n1J5jK3jd%2F99vXxggNKbPQ%3D%3D070TIDAK DIIJINKAN MENAMBAH CHARGE         000000000000000000000000000000';
/* PULSA */
// $data = '02105b0000014a11800c1900000000821508778450000000201750000000201752204041026140000000007FA326702800000000000000000821508778450021752594980025Pengisian pulsa SD20H And080rajabiller.pulsa              SD20H          082150877845   00000000002531240404360219PG        020220404102614000000000821508778450000020175000000000000000057650000000000217525949800  Pengisian pulsa SD20H Anda ke nomor 082150877845 BERHASIL. SN=02140684000054031108. Harga=8375      00000201750000000000080SD20H             082150877845                   02140684000054031108.0000020175';
/* GAME */
// $data = '02105b0000014a11800c1900000008383262621590000000961000000000961002204041031580000000007FA326702800000000000000008383262621590021734910630025Pembelian Voucher ML5 BER080rajabiller.game               ML5            838326262159   00000000002531240404360219PG        020220404103158000000008383262621590000096100000000000000000057650000000000217349106300  Pembelian Voucher ML5 BERHASIL. SN=1222NQVITXT1C640632B5 c.                                         00000961000000000000080ML5               838326262159                                  ------0000096100';
/* BPJS INQ */
// $data = '02105b0000014a1180081900088888022587554350000001050000000001075002111220848130000250007FA326702800000000000088888022587554350023581397790025Success                  088rajabiller.bpjsinq            ASRBPJSKSH     8888802258755435    00000000002531240404 01360226ASRBPJSKS  2021112208481300008888802258755435SITI MAEMONAH                 01                                      00001050000002500000000000000000057650000000000235813977900  Success                       00001075000751769604000';
/* BPJS PAY */
// $data = '02105b0000014a11800c1900088888018515235930000000649860000000699861601071308000000500007FA326702800000000000088888018515235930004326354790025EXT: APPROVE             103rajabiller.bpjspay            ASRBPJSKSH     8888801851523593    00000000002531240404 01   082175633485360357ASRBPJSKS 02016010713080000008888801851523593DJASILAH                                                            0100000649860005000000000000000000057650000000000043263547900  EXT: APPROVE                  00000699860001427760https://202.43.173.234/struk/?id=BSDC7ug8rji6VTNvAJWjJJJ7UoXFOfWWQSFvcb4IP%2FJaKQDMGIlghkDGEMaMHN%2BPC%2BSaRyuVj4QNPNcihkIEow%3D%3D075TIDAK DIIJINKAN MENAMBAH CHARGE            082175633485  924000000432635479';
/* Transfer Inq */
// $data = '02105b0000014a11800c1900000000088207983770000000500000000000565002204070311120000650007FA326702800000000000000000088207983770019136768670025SUCCESS                  107rajabiller.transferinq        BLTRFAG        0830041384          00000000002531240404    009   082175633485360293BLTR      02022040703111200000000008820798377CHANDRA WIJAYA ATMAJA                               BANK BNI    00900000500000006500000000000000000057650000000000191367686700  SUCCESS                                                                                             00000565000079764271100BLTRFAG             8820798377CHANDRA WIJAYA ATMAJA                               BANK BNI0000050000';
/* Transfer Pay */
// $data = '02105b0000014a11800c1900000000088207983770000000500000000000565002204121009140000650007FA326702800000000000000000088207983770019136774180025SUCCESS                  107rajabiller.transferpay        BLTRFAG        0031901610001869    00000000002531240404    009   082175633485360374BLTR      02022041210091400000000008820798377CHANDRA WIJAYA ATMAJA                               BANK BNI    00900000500000006500000000000000000057650000000000191367741800  SUCCESS                                                                                             00000565000079643641https://202.43.173.234/struk/?id=bo1uJJyc%2F7LeRHfds51wGLGljf17iXtQWPwpHaTrTBo%3D100BLTRFAG             8820798377CHANDRA WIJAYA ATMAJA                               BANK BNI0000050000';
/* Info Produk */
$data = '0200723A400108C18000060535011700000000000177370607105302116717105303060706086021110000000073900262811691251BMS0012009001008000002305381125606911100B86D72CABABA484C9FA01430EB19D3E0ANWAR 53811123 R10000004500000000002022062006202200000000000000017737D00000000000000000000000000000000000251530002520300000000000000000000000000000000360';

$unpack = $message->unpack($data);

var_dump($message->getBitmap());
var_dump($message->getMti());
var_dump($message->getFields());
die();

$arr = [10, 15, 20, 30, 40, 10, 7, 20, 20, 4, 30, 10, 10, 500];

$produk = '';
$data = array();
foreach ($message->getFields() as $key => $value) {
    switch ($key) {
        case '61':
            $i = 0;
            $inc = 0;
            $produk = substr($value, 0, 10);
            $msg = $value;
            $arrData = array();
            switch ($produk) {
                case strpos(trim($produk), 'PG'):
                    $arr = [10, 15, 20, 10, 20, 20, 4, 100, 10, 10];
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                case strpos(trim($produk), 'BLTR'):
                    $arr = [10, 15, 20, 30, 30, 7, 10, 7, 20, 20, 4, 100, 10, 10, 500];
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                case strpos(trim($produk), 'INFO'):
                    $arr = [10, 15, 20, 30, 30, 7, 10, 7, 20, 20, 4, 100, 10, 10, 500];
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                default:
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
            }
            
            break;
        case '62':
            $arrData = array();
            switch ($produk) {
                case strpos(trim($produk), 'PLNPASC'):
                    $arr = [40, 10, 10, 10, 10, 10, 10, 10];
                    $msg = $value;
                    $i = 0;
                    $inc = 0;
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                case strpos(trim($produk), 'WA'):
                    $arr = [40, 7, 7];
                    $msg = $value;
                    $i = 0;
                    $inc = 0;
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                case strpos(trim($produk), 'FN'):
                    $arr = [40, 7, 7];
                    $msg = $value;
                    $i = 0;
                    $inc = 0;
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                case strpos(trim($produk), 'PG'):
                    $arr = [10, 20, 40, 10];
                    $msg = $value;
                    $i = 0;
                    $inc = 0;
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                case strpos(trim($produk), 'ASR'):
                    $arr = [40, 15, 20];
                    $msg = $value;
                    $i = 0;
                    $inc = 0;
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                case strpos(trim($produk), 'BLTR'):
                    $arr = [10, 20, 30, 30, 10];
                    $msg = $value;
                    $i = 0;
                    $inc = 0;
                    foreach ($arr as $k) {
                        $inc++;
                        $arrData['data_'.$inc] = substr($value, $i, $k);
                        $msg = substr($msg, $k);
                        $i+=$k;
                    }
                    $data['BIT_'.$key] = $arrData;
                    break;
                
                default:
                    break;
            }
            break;
        
        default:
            $data['BIT_'.$key] = $value;
            break;
    }
}

echo json_encode($data);