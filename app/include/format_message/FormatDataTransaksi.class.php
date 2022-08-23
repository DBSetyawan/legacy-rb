<?php

class FormatDataTransaksi {
	var $data;
	//TANGGAL1*TANGGAL2*KDPRODUK*IDPEL*LIMIT*IDOUTLET*PIN*RESPONSECODE*CONTENT
    function FormatDataTransaksi($format, $message){
        $frm = explode("*",$format);
        $msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
            $this->data[$frm[$i]] = $msg[$i];
        }
    }
    public function getTanggal1(){
		return $this->data["TANGGAL1"];
    }
    public function getTanggal2(){
		return $this->data["TANGGAL2"];
    }
    public function getKodeProduk(){
		return $this->data["KDPRODUK"];
    }
    public function getIdPel(){
		return $this->data["IDPEL"];
    }
    public function getLimit(){
		return $this->data["LIMIT"];
    }
    public function getIdOutlet(){
		return $this->data["IDOUTLET"];
    }
    public function getPin(){
		return $this->data["PIN"];
    }
    public function getResponseCode(){
        return $this->data["RESPONSECODE"];
    }
    public function getContent(){
        return $this->data["CONTENT"];
    }                       
}


?>