<?php
namespace ISO8583;

use ISO8583\Protocol;
use ISO8583\Message;
use ISO8583\Bit60;

class Response 
{
    protected $message;
    protected $msg;
    protected $methd;
    protected $reqIso;
    public function __construct($result = "", $method = "", $request)
	{
        $this->msg = $result;
        $this->methd = $method;
        $this->reqIso = $request;
        $iso = new Protocol();
        $this->message = new Message($iso, [
            'lengthPrefix' => 0
        ]);

        $this->setISO();
    }

    public function setISO()
    {
        $resp = new Bit60($this->msg);
        switch ($this->methd) {
            case 'rajabiller.info_produk':
                $resp->rajabillerInfoProduk();
                break; 
            case 'rajabiller.inq':
                $resp->rajabillerInq();
                break; 
            case 'rajabiller.paydetail':
                $resp->rajabillerPayDetail();
                break;   
            case 'rajabiller.pulsa':
                $resp->rajabillerPulsa();
                break;       
            case 'rajabiller.game':
                $resp->rajabillerPulsa();
                break;    
            case 'rajabiller.bpjsinq':
                $resp->rajabillerInq();
                break;  
            case 'rajabiller.bpjspay':
                $resp->rajabillerPayDetail();
                break;  
            case 'rajabiller.transferinq':
                $resp->rajabillerTransferDana();
                break;  
            case 'rajabiller.transferpay':
                $resp->rajabillerTransferDana();
                break;  
            default:
                break;
        }
        
        $this->message->setMTI('0210');                                                                                         // MTI
        $this->message->setField(2, str_pad($resp->getProductId(), 19, "0", STR_PAD_LEFT));                                     // PAN                                  (IDPEL)                             (Y)
        $this->message->setField(4, str_pad(intval($resp->getNominal()), 12, "0", STR_PAD_LEFT));                               // TRANSACTION AMOUNT                   (NOMINAL)                           (N)
        $this->message->setField(5, str_pad(intval($resp->getNominal()) + intval($resp->getAdmin()), 12, "0", STR_PAD_LEFT));   // TRANSACTION AMOUNT                   (NOMINAL YG DIPOTONG KE OUTLET)     (N)
        $this->message->setField(7, $resp->getDatetimeResponse() != "" ? date('ymdHis', strtotime($resp->getDatetimeResponse())):date('ymdHis') );                                                              // DATE 220314102700                    (YYYYMMDDHIS)                       (N)
        $this->message->setField(8, str_pad(intval($resp->getAdmin()), 8, "0", STR_PAD_LEFT));                                  // TRANSACTION AMOUNT                   (NOMINAL ADMIN)                     (N)
        $this->message->setField(32, $resp->getOutletId());                                                                                // ACQUIRING INSTITUTION IDENT CODE     (ID OUTLET)                         (Y)
        $this->message->setField(34, str_pad($resp->getProductId(), 28, "0", STR_PAD_LEFT));                                    // PAN EXTENDED                         (IDPEL)                             (Y/N)
        $this->message->setField(37, str_pad($resp->getRBTrxId(), 12, "0", STR_PAD_LEFT));                                      // RETRIEVAL REFERENCE NUMBER           (RAJABILLER TRX ID)                 (N)
        $this->message->setField(39, str_pad($resp->getRC(), 2, "0", STR_PAD_LEFT));                                            // RESPONSE CODE                        (RC)                                (N)
        $this->message->setField(44, str_pad(substr($resp->getDesc(),0,25), 25, " ", STR_PAD_RIGHT));                            // ADITIONAL RESPONSE DATA              (DESC)                              (N)
        $this->message->setField(48, $this->reqIso);                                                                                       // ADITIONAL DATA - PRIVATE             (TRX REQUEST DATA)                  (Y)
        $this->message->setField(49, "360");                                                                                    // CURRENCY CODE, TRANSACTION           (CURRENCY CODE)                     (Y)
        $this->message->setField(61, $resp->getBit60());     
        $this->message->setField(62, $resp->getBit63()); 


        echo $this->message->pack();
    }
}


