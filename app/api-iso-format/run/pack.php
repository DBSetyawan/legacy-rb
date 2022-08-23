<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require '../vendor/autoload.php';

use ISO8583\Protocol;
use ISO8583\Message;

$iso = new Protocol();
$message = new Message($iso, [
    'lengthPrefix' => 0
]);

// Packing message
$message->setMTI('0200');                                   // MTI
$message->setField(2, "0000031901610001869");                  // PAN                                  (IDPEL)                             (Y)
// $message->setField(3, "");                               // PROCESSING CODE                      ()                                  (N)
$message->setField(4, str_pad("50000", 12, "0", STR_PAD_LEFT));                               // TRANSACTION AMOUNT                   (NOMINAL)                           (N)
$message->setField(5, str_pad("6500", 12, "0", STR_PAD_LEFT));                               // TRANSACTION AMOUNT                   (NOMINAL YG DIPOTONG KE OUTLET)     (N)
$message->setField(7, date('ymdHis'));                      // DATE 220314102700                    (YYYYMMDDHIS)                       (N)
// $message->setField(8, "");                               // TRANSACTION AMOUNT                   (NOMINAL ADMIN)                     (N)
$message->setField(11, "005765");                           // MID                                  (ID REQUEST PARTNER)                (Y)
$message->setField(12, date('His'));                        // TIME, LOCAL TRANSACTION              (HIS)                               (Y)
$message->setField(13, date('md'));                         // DATE, LOCAL TRANSACTION              (YYYYMMDD)                          (Y)
$message->setField(32, "FA32670");                          // ACQUIRING INSTITUTION IDENT CODE     (ID OUTLET)                         (Y)
$message->setField(34, "0000000000000031901610001869");     // PAN EXTENDED                         (IDPEL)                             (Y/N)
$message->setField(37, "002531240404");                     // RETRIEVAL REFERENCE NUMBER           (RAJABILLER TRX ID)                 (N)
// $message->setField(39, "");                              // RESPONSE CODE                        (RC)                                (N)
// $message->setField(44, "");                              // ADITIONAL RESPONSE DATA              (DESC)                              (N)
$message->setField(48, str_pad("rajabiller.info_produk", 30, " ", STR_PAD_RIGHT).str_pad("WAGRESIK", 15, " ", STR_PAD_RIGHT));   // ADITIONAL DATA - PRIVATE             (TRX REQUEST DATA)                  (Y)
// $message->setField(48, str_pad("rajabiller.transferinq", 30, " ", STR_PAD_RIGHT).str_pad("BLTRFAG", 15, " ", STR_PAD_RIGHT).str_pad("0031901610001869", 20, " ", STR_PAD_RIGHT).str_pad("2531240404", 20, "0", STR_PAD_LEFT).str_pad("01", 3, " ", STR_PAD_LEFT).str_pad("082175633485", 15, " ", STR_PAD_LEFT));   // ADITIONAL DATA - PRIVATE             (TRX REQUEST DATA)                  (Y)
// $message->setField(48, str_pad("rajabiller.transferpay", 30, " ", STR_PAD_RIGHT).str_pad("BLTRFAG", 15, " ", STR_PAD_RIGHT).str_pad("0031901610001869", 20, " ", STR_PAD_RIGHT).str_pad("2531240404", 20, "0", STR_PAD_LEFT).str_pad("009", 7, " ", STR_PAD_LEFT).str_pad("082175633485", 15, " ", STR_PAD_LEFT));   // ADITIONAL DATA - PRIVATE             (TRX REQUEST DATA)                  (Y)
$message->setField(49, "360");                              // CURRENCY CODE, TRANSACTION           (CURRENCY CODE)                     (Y)
$message->setField(52, "585616");                           // PIN DATA                             (PIN)                               (Y)
// $message->setField(60, "");                              // RESERVED PRIVATE                     (TRX RESP DATA)                     (N)
// $message->setField(61, "");                              // RESERVED PRIVATE                     (TRX RESP ADDITIONAL)               (N)
echo $message->pack() . PHP_EOL;

