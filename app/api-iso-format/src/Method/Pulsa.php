<?php

namespace ISO8583\Method;

use ISO8583\Inquiry;

class Pulsa 
{
    protected $get;
    function __construct($msg)
    {
        $this->get = new Inquiry($msg);
    }

    public function setRequest()
    {
        $data = array(
            "method"      => 'rajabiller.pulsa',
            "uid"         => $this->get->getOutlet(),
            "pin"         => $this->get->getPinOutlet(),
            "no_hp"       => '0'.$this->get->getBillId(),
            "kode_produk" => $this->get->getProductId(),
            "ref1"        => ltrim($this->get->getRefId(), '0'),
        );
        
        return $data;
    }
}
