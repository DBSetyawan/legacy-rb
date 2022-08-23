<?php

class FormatStatusTransaksi {
	var $data;
	//IDTRX*IDPEL*IDOUTLET*PIN*RESPONSECODE*CONTENT
    function FormatStatusTransaksi($format, $message){
        $frm = explode("*",$format);
        $msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
            $this->data[$frm[$i]] = $msg[$i];
        }
    }
    public function getIdTransaksi(){
		return $this->data["IDTRX"];
    }
    public function getIdPel(){
		return $this->data["IDPEL"];
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