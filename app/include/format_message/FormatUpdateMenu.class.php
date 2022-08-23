<?php

class FormatUpdateMenu {
	var $data;
    function FormatUpdateMenu($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
	//MENU*MENU*MID*STEP*TANGGAL*VIA*IDOUTLET*PIN*TOKEN*SALDO*IDTRX*STATUS*KETERANGAN*MENU_PRODUK_PULSA*MENU_PRODUK_PAYMENT
    public function getCommand(){
        return $this->data["MENU"];
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
    public function getMenuProdukPulsa(){
        return $this->data["MENU_PRODUK_PULSA"];
    }
    public function getMenuProdukPayment(){
        return $this->data["MENU_PRODUK_PAYMENT"];
    }
}
?>