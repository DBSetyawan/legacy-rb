<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require '../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\Message;
use ISO8583\Mapping;
use ISO8583\Inquiry;

$map = new Mapping();
$iso = new Protocol();
$message = new Message($iso, [
    'lengthPrefix' => 5
]);

// $data = file_get_contents('php://input');
// $data = '02005a38000148019000160000000076-04843000000022550000000000000220330132754005765132754033007FA32670250000000000000000076-04843002531240404080rajabiller.paydetail          WAMAKASAR      76-04843       00000000002531240404360585616';
// $data = '02005a38000148019000160000000076-04843000000022550000000000000220330162605005765162605033007FA32670250000000000000000076-04843002531240404045rajabiller.info_produk        WAGRESIK       360585616';
// PLNPRA
//$data = '02005a38000148019000160000535592709412000000022550000000000000220331132403005765132403033107FA32670250000000000000535592709412002531240404080rajabiller.paydetail          PLNPASCB       535592709412   00000000002531240404360585616';
// PDAM
//$data = '02005a38000148019000130000200100946000000022550000000000000220331161333005765161333033107FA32670220000000000000200100946002531240404080rajabiller.paydetail          WAMAKASAR      200100946      00000000002531240404360585616';
// FNHCI - Mutlifinance
// $data = '02005a380001480190001400004100740197000000022550000000000000220331162437005765162437033107FA326702300000000000004100740197002531240404080rajabiller.paydetail          FNHCI          4100740197     00000000002531240404360585616';
// PULSA
// $data = '02005a38000148019000160000082150877845000000000000000000000000220404102553005765102553040407FA32670250000000000000082150877845002531240404080rajabiller.pulsa              SD20H          082150877845   00000000002531240404360585616';
// GAME
// $data = '02005a38000148019000160000838326262159000000000000000000000000220404103122005765103122040407FA32670250000000000000838326262159002531240404080rajabiller.game               ML5            838326262159   00000000002531240404360585616';
/* INQ BPJS KS*/
// $data = '02005a38000148019000190008888802258755435000000000000000000000000220404230307005765230307040407FA32670280000000000008888802258755435002531240404088rajabiller.bpjsinq            ASRBPJSKSH     8888802258755435    00000000002531240404 01360585616';
/* PAY BPJS KS*/
// $data = '02005a38000148019000190008888801851523593000000105000000000002500220407095844005765095844040707FA32670280000000000008888801851523593002531240404103rajabiller.bpjspay            ASRBPJSKSH     8888801851523593    00000000002531240404 01   082175633485360585616';
/* Transfer Inq */
// $data = '02005a38000148019000130000830041384000000050000000000000000220407145755005765145755040707FA32670220000000000000830041384002531240404107rajabiller.transferinq        BLTRFAG        0830041384          00000000002531240404    009   082175633485360585616';
/* Transfer Pay */
// $data = '02005a38000148019000190000031901610001869000000050000000000006500220412094615005765094615041207FA32670280000000000000031901610001869002531240404107rajabiller.transferpay        BLTRFAG        0031901610001869    00000000002531240404    009   082175633485360585616';

/* Info Produk */
$data = '02005a38000148019000190000031901610001869000000050000000000006500220412112339005765112339041207FA32670280000000000000031901610001869002531240404045rajabiller.info_produk        WAGRESIK       360585616';

try {
    $mti =substr($data, 0, 4);
    switch ($mti) {
        case '0200':
            $request = new Inquiry($data);
            $result = $request->sendData();
            break;
        default:
            echo 'MTI not Registered';
        break;
    }
} catch (Exception $e) {
    echo "EXT: " . $e->getMessage();
}

