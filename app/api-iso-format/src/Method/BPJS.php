<?php

namespace ISO8583\Method;

use ISO8583\Inquiry;

class BPJS 
{
    protected $get;
    function __construct($msg)
    {
        $this->get = new Inquiry($msg);
    }

    public function setRequest()
    {
        switch ($this->get->getMethod()) {
            case 'rajabiller.bpjspay':
                $data = array(
                    "method"      => 'rajabiller.bpjspay',
                    "uid"         => $this->get->getOutlet(),
                    "pin"         => $this->get->getPinOutlet(),
                    "kode_produk" => $this->get->getProductId(),
                    "periode"     => $this->get->getPeriodBulan(),
                    "ref1"        => ltrim($this->get->getRefId(), '0'),
                    "ref2"        => ltrim($this->get->getTrxId(), '0'),
                    "nominal"     => $this->get->getNominal(),
                    "no_hp"       => $this->get->getPhoneNumber(),
                    "idpel1"      => $this->get->getBillId()
                );
                break;
            case 'rajabiller.bpjsinq':
                $data = array(
                    "method"      => 'rajabiller.bpjsinq',
                    "uid"         => $this->get->getOutlet(),
                    "pin"         => $this->get->getPinOutlet(),
                    "kode_produk" => $this->get->getProductId(),
                    "periode"     => $this->get->getPeriodBulan(),
                    "ref1"        => ltrim($this->get->getRefId(), '0'),
                    "idpel"       => $this->get->getBillId()
                );
                break;
            default:
                echo 'method kosong';
                break;
        }
        
        return $data;
    }
}
