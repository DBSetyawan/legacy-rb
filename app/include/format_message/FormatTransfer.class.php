<?php

class FormatTransfer {
	var $data;
	function FormatTransfer($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
    public function getCommand(){
		return $this->data["KIRIM"];
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
    public function getIdTujuan(){
		return $this->data["IDTUJUAN"];
    }
    public function getNominal(){
		return $this->data["NOMINAL"];
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
}
?>