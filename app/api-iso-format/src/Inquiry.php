<?php
namespace ISO8583;

use ISO8583\Protocol;
use ISO8583\Message;
use ISO8583\bit48;
use ISO8583\Bit60;
use ISO8583\Response;
use ISO8583\Method;

class Inquiry 
{
    protected $message;
    protected $data;
    protected $isoReq;

    protected $outletId;
    protected $pinOutletId;
    protected $method;
    protected $bitRequest;
    protected $refId;
    protected $billId;
    protected $TrxId;
    protected $Nominal;
    protected $ProductId;
    protected $periodebulan;
    protected $PhoneNumber;

    protected $bit48;

    protected $Method = [
        'rajabiller.balance'       => "",
        'rajabiller.info_produk'   => Method\InfoProduk::class,
        'rajabiller.game'          => Method\Game::class,
        'rajabiller.pulsa'         => Method\Pulsa::class,
        'rajabiller.inq'           => Method\Inq::class,
        'rajabiller.bpjsinq'       => Method\BPJS::class,
        'rajabiller.bpjspay'       => Method\BPJS::class,
		'rajabiller.paydetail'     => Method\Paydetail::class,
        'rajabiller.transferinq'   => Method\TransferDana::class,
        'rajabiller.transferpay'   => Method\TransferDana::class
	];

    public function __construct($msg = "")
	{
        $this->bitRequest = $msg;
        $iso = new Protocol();
        $this->message = new Message($iso, ['lengthPrefix' => 5]);
        $this->setData();
    }

    protected function setData()
    {
        $this->message->unpack($this->bitRequest);
        $this->isoReq = $this->message->getField(48);

        foreach ($this->message->getFields() as $key => $value) {
            switch ($key) {
                case '2':
                    $this->setBillId($value);
                    break;
                case '4':
                    $this->setNominal($value);
                    break;
                case '11':
                    $this->setRefId($value);
                    break;
                case '32':
                    $this->setOutlet($value);
                    break;
                case '37':
                    $this->setTrxId($value);
                    break;
                case '48':
                    $this->setDataRequest($value);
                    break;
                case '52':
                    $this->setPinOutlet($value);
                    break;
                default:
                    break;
            }
        }
    }

    public function getISO()
    {
        return $this->bitRequest;
    }

    public function setNominal($val)
    {
        $this->Nominal = ltrim($val, "0");
    }

    public function getNominal()
    {
        return $this->Nominal;
    }

    public function setBillId($val)
    {
        $this->billId = ltrim($val, "0");
    }

    public function getBillId()
    {
        return $this->billId;
    }

    public function setTrxId($val)
    {
        $this->TrxId = ltrim($val, "0");
    }

    public function getTrxId()
    {
        return $this->TrxId;
    }

    public function setRefId($val)
    {
        $this->refId = ltrim($val, "0");
    }

    public function getRefId()
    {
        return $this->refId;
    }

    public function setOutlet($val)
    {
        $this->outletId = $val;
    }

    public function getOutlet()
    {
        return trim($this->outletId);
    }

    public function setPinOutlet($val)
    {
        $this->pinOutletId = $val;
    }

    public function getPinOutlet()
    {
        return trim($this->pinOutletId);
    }

    public function setDataRequest($val)
    {
        $this->bit48 = new bit48($val);
        $this->ProductId = trim($this->bit48->getProductId());
        $this->method = trim($this->bit48->getMethod());
        $this->periodebulan = trim($this->bit48->getPeriod());
        $this->PhoneNumber = trim($this->bit48->getPhoneNumber());
    }

    public function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }

    public function getProductId()
    {
        return $this->ProductId;
    }


    public function getMethod()
    {
        return $this->method;
    }

    public function getPeriodBulan()
    {
        return $this->periodebulan;
    }

    public function sendData(){
        $set = new $this->Method[$this->method]($this->bitRequest);
        $request = $set->setRequest();
        $result = $this->send(json_encode($request));
        // die($result);
        $resultISO = new Response($result, $this->method, $this->isoReq);
        return $resultISO;
    }

    protected function send($param)
    {
        $URL = "https://rajabiller.fastpay.co.id/transaksi/json_devel.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);//echo $param;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($param))                                                                       
        ); 
        $result = curl_exec($ch);
        return ($result);
    }
}


