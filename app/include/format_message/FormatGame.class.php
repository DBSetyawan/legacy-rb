<?php

class FormatGame {
	var $data;
	//GAME*KDPRODUK*MID*STEP*TANGGAL*NOHP*IDOUTLET*PIN*TOKEN*VIA*SN*SALDO*IDTRX*STATUS*KETERANGAN
	function FormatGame($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
    public function getCommand(){
		return $this->data["GAME"];
    }
    public function getKodeProduk(){
		return $this->data["KDPRODUK"];
    }
    public function getMid(){
		return $this->data["MID"];
    }
    public function getStep(){
		return $this->data["STEP"];
    }
    public function getTanggal(){
		return $this->data["TANGGAL"];
    }
    public function getNohp(){
		return $this->data["NOHP"];
    }
    public function getMember(){
        return $this->data["IDOUTLET"];
    }
    public function getPin(){
        return $this->data["PIN"];
    }
    public function getToken(){
        return $this->data["TOKEN"];
    }
    public function getVia(){
        return $this->data["VIA"];
    }
	public function getKodeProdukBiller(){
        return $this->data["KODEPRODUKBILLER"];
    }
    public function getSN(){
        return $this->data["SN"];
    }
    public function getSaldo(){
        return $this->data["SALDO"];
    }
    public function getIdTrx(){
        return $this->data["IDTRX"];
    }
    public function getStatus(){
        return $this->data["STATUS"];
    }
    public function getKeterangan(){
        return $this->data["KETERANGAN"];
    }   
    public function getNominal(){
        return $this->data["NOMINAL"];
    }                    
}


?>