<?php

class FormatDaftar {
	var $data;

	function FormatDaftar($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
    public function getCommand(){
		//return $this->data["DAFTAR"];
		return "AGENSI";
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
    public function getNoHP(){
		return $this->data["NOHP"];
    }
    public function getNama(){
		return $this->data["NAMA"];
    }
    public function getAlamat(){
		return $this->data["ALAMAT"];
    }
    public function getKota(){
		return $this->data["KOTA"];
    }
    public function getKodePos(){
		return $this->data["KODEPOS"];
    }
    public function getTipeLoket(){
		return $this->data["TIPELOKET"];
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
    public function getMemberBaru(){
      return $this->data["IDOUTLETBARU"];
  }
    
}
?>