<?php
namespace ISO8583;

class bit48 
{
    protected $length_parse = [30, 15, 20, 20, 7, 15];
    protected $data;
    protected $values;

    protected $method;
    protected $productId;
    protected $BillId;
    protected $ReffId;
    protected $Period;
    protected $PhoneNumber;

    public function __construct($message = "")
	{
        $this->values = $message;
        $this->setData48();
    }

    public function setData48()
    {
        $pastNumber = 0;
        foreach($this->length_parse as $key => $val){
            switch ($key) {
                case '0':
                    $dt = substr($this->values, $pastNumber, $val);
                    $this->setMethod($dt);
                    $pastNumber = $val;
                    break;
                case '1':
                    $dt = substr($this->values, $pastNumber, $val);
                    $this->setProductId($dt);
                    $pastNumber += $val;
                    break;
                case '2':
                    $dt = substr($this->values, $pastNumber, $val);
                    $this->setBillId($dt);
                    $pastNumber += $val;
                    break;
                case '3':
                    $dt = substr($this->values, $pastNumber, $val);
                    $this->setReffId($dt);
                    $pastNumber += $val;
                    break;
                case '4':
                    $dt = substr($this->values, $pastNumber, $val);
                    $this->setPeriod($dt);
                    $pastNumber += $val;
                    break;
                case '5':
                    $dt = substr($this->values, $pastNumber, $val);
                    $this->setPhoneNumber($dt);
                    $pastNumber += $val;
                    break;
                default:
                    break;
            }
        }
    }

    public function setPhoneNumber($data)        
    {
        $this->PhoneNumber = $data;
    }

    public function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }

    public function setPeriod($data)        
    {
        $this->Period = $data;
    }

    public function getPeriod()
    {
        return $this->Period;
    }

    public function setMethod($data)        
    {
        $this->method = $data;
    }

    public function getMethod()
    {
        return $this->method;
    }


    public function setProductId($data)        
    {
        $this->productId = $data;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setBillId($data)        
    {
        $this->BillId = $data;
    }

    public function getBillId()
    {
        return $this->BillId;
    }
    public function setReffId($data)        
    {
        $this->ReffId = $data;
    }

    public function getReffId()
    {
        return $this->ReffId;
    }
}
