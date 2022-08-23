<?php

class FormatMandatory {
	var $data;

	function FormatMandatory($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($frm);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
    public function getCommand(){
		if($this->data["TAGIHAN"]){
			return $this->data["TAGIHAN"];
		}else if($this->data["BAYAR"]){
			return $this->data["BAYAR"];
		}else if($this->data["CU"]){
			return $this->data["CU"];
		}
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
    public function getIdPel1(){
		return $this->data["IDPEL1"];
    }
    public function getIdPel2(){
		return $this->data["IDPEL2"];
    }
    public function getIdPel3(){
		return $this->data["IDPEL3"];
    }
    public function getNominal(){
		return $this->data["NOMINAL"];
    }
    public function getNominalAdmin(){
		return $this->data["NOMINALADMIN"];
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
    public function getTanggal(){
        return $this->data["TANGGAL"];
    }
    public function getJenisStruk(){
        return $this->data["JENISSTRUK"];
    }
    public function getKodeBank(){
        return $this->data["KODEBANK"];
    }
    public function getKodeProdukBiller(){
        return $this->data["KODEPRODUKBILLER"];
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