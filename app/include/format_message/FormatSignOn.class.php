<?php

class FormatSignOn {
	var $data;
	//SIGNON*IDOUTLET*PIN*TOKEN*VIA
	//SIGNON*IDOUTLET*PIN*TOKEN*VIA*SALDO*IDTRX*STATUS*KETERANGAN
    function FormatSignOn($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
    public function getCommand(){
        return $this->data["SIGNON"];
    }
	public function getKodeProduk(){
		return $this->data["KDPRODUK"];
    }
	public function getMID(){
		return $this->data["MID"];
    }
	public function getStep(){
		return $this->data["STEP"];
    }
	public function getTanggal(){
        return $this->data["TANGGAL"];
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
    public function getMsgBroadcast(){
        return $this->data["MSGBROADCAST"];
    }
    public function getSetting(){
        return $this->data["SETTING"];
    }
}
?>