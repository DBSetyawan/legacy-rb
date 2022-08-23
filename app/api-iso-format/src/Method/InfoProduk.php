<?php

namespace ISO8583\Method;

use ISO8583\Inquiry;

class InfoProduk 
{
    protected $get;
    function __construct($msg)
    {
        $this->get = new Inquiry($msg);
    }

    public function setRequest()
    {
        $data = array(
            "method"      => 'rajabiller.info_produk',
            "uid"         => $this->get->getOutlet(),
            "pin"         => $this->get->getPinOutlet(),
            "kode_produk" => $this->get->getProductId()
        );
        // die(json_encode($data));
        return $data;
    }
}
