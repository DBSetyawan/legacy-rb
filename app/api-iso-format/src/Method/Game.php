<?php

namespace ISO8583\Method;

use ISO8583\Inquiry;

class Game 
{
    protected $get;
    function __construct($msg)
    {
        $this->get = new Inquiry($msg);
    }

    public function setRequest()
    {
        $data = array(
            "method"      => 'rajabiller.game',
            "uid"         => $this->get->getOutlet(),
            "pin"         => $this->get->getPinOutlet(),
            "no_hp"       => $this->get->getBillId(),
            "kode_produk" => $this->get->getProductId(),
            "ref1"        => ltrim($this->get->getRefId(), '0'),
        );
        
        return $data;
    }
}
