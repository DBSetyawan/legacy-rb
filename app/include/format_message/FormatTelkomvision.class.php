<?php

class FormatTelkomvision extends FormatMandatory {
	var $data;

	function FormatTelkomvision($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }

    public function getKodearea(){
		return $this->data["KODEAREA"];
    }
    public function getNomorTelepon(){
		return $this->data["NOMORTELEPON"];
    }
    public function getKodeDivre(){
		return $this->data["KODEDIVRE"];
    }
    public function getKodeDatel(){
		return $this->data["KODEDATEL"];
    }
    public function getJumlahBill(){
		return $this->data["JUMLAHBILL"];
    }
    public function getNomorReferensi3(){
        return $this->data["NOMORREFERENSI3"];
    }
    public function getNilaiTagihan3(){
        return $this->data["NILAITAGIHAN3"];
    }
    public function getNomorReferensi2(){
        return $this->data["NOMORREFERENSI2"];
    }
    public function getNilaiTagihan2(){
        return $this->data["NILAITAGIHAN2"];
    }
    public function getNomorReferensi1(){
        return $this->data["NOMORREFERENSI1"];
    }
    public function getNilaiTagihan1(){
        return $this->data["NILAITAGIHAN1"];
    }
    public function getNamaPelanggan(){
        return $this->data["NAMAPELANGGAN"];
    }
    public function getNPWP(){
        return $this->data["NPWP"];
    }
}
?>