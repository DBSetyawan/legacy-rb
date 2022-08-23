<?php

namespace ISO8583\Method;

use ISO8583\Inquiry;

class Inq 
{
    protected $get;
    function __construct($msg)
    {
        $this->get = new Inquiry($msg);
    }

    public function setRequest()
    {
        $data = array(
            "method"      => 'rajabiller.inq',
            "uid"         => $this->get->getOutlet(),
            "pin"         => $this->get->getPinOutlet(),
            "idpel1"      => $this->get->getBillId(),
            "idpel2"      => $this->get->getBillId(),
            "idpel3"      => $this->get->getBillId(),
            "kode_produk" => $this->get->getProductId(),
            "ref1"        => ltrim($this->get->getRefId(), '0')
        );
        
        return $data;
    }
}
