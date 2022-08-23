<?php

namespace ISO8583\Method;

use ISO8583\Inquiry;

class TransferDana 
{
    protected $get;
    function __construct($msg)
    {
        $this->get = new Inquiry($msg);
    }

    public function setRequest()
    {
        switch ($this->get->getMethod()) {
            case 'rajabiller.transferpay':
                $data = array(
                    "method"      => 'rajabiller.transferpay',
                    "uid"         => $this->get->getOutlet(),
                    "pin"         => $this->get->getPinOutlet(),
                    "idpel"       => $this->get->getBillId(),
                    "idpel2"      => $this->get->getBillId(),
                    "idpel3"      => $this->get->getBillId(),
                    "kode_produk" => $this->get->getProductId(),
                    "ref1"        => ltrim($this->get->getRefId(), '0'),
                    "ref2"        => ltrim($this->get->getTrxId(), '0'),
                    "nominal"     => $this->get->getNominal(),
                    "kodebank"    => $this->get->getPeriodBulan(),
                    "nomorhp"     => $this->get->getPhoneNumber()
                );
                break;
            case 'rajabiller.transferinq':
                $data = array(
                    "method"      => 'rajabiller.transferinq',
                    "uid"         => $this->get->getOutlet(),
                    "pin"         => $this->get->getPinOutlet(),
                    "idpel"       => $this->get->getBillId(),
                    "idpel2"      => $this->get->getBillId(),
                    "idpel3"      => $this->get->getBillId(),
                    "kode_produk" => $this->get->getProductId(),
                    "ref1"        => ltrim($this->get->getRefId(), '0'),
                    "nominal"     => $this->get->getNominal(),
                    "kodebank"    => $this->get->getPeriodBulan(),
                    "nomorhp"     => $this->get->getPhoneNumber()
                );
    
                break;
            default:
                echo 'method kosong';
                break;
        }
        // die(json_encode($data));
        return $data;
    }
}
