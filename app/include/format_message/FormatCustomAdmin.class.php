<?php

class FormatCustomAdmin {
	var $data;
	var $detail;
//SETFEE*KDPRODUK*MID*STEP*TANGGAL*VIA*IDOUTLET*PIN*TOKEN*KODEPRODUK_1:FEEPRODUK_1#KODEPRODUK_2:FEEPRODUK_2#KODEPRODUK_3:FEEPRODUK_3#KODEPRODUK_4:FEEPRODUK_4
	function FormatCustomAdmin($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        //for($i=0;$i<count($msg)-1;$i++){
		for($i=0;$i<count($msg);$i++){	
			if(substr($frm[$i],0,12)=="KODEPRODUK_1"){
				$this->data["DETAILADMINFEE"] = $msg[$i];
				$dtl = explode("#",$this->data["DETAILADMINFEE"]);
				$frm_dtl = explode("#",$frm[$i]);
				for($m=0;$m<count($dtl)-1;$m++){
					$val = explode(":",$dtl[$m]);
					$frm_val = explode(":",$frm_dtl[$m]);
					for($j=0;$j<count($val)-1;$j++){
						$this->detail[$frm_val[$j]] = $val[$j];
					}
				}
			}else{
				$this->data[$frm[$i]] = $msg[$i];
			}
        }
    }
    public function getCommand(){
		return $this->data["SETFEE"];
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
    public function getTanggal(){
		return $this->data["TANGGAL"];
    }
    public function getDetailAdminFee(){
		return $this->data["DETAILADMINFEE"];
    }
	public function getDetailProduk1(){
		return $this->detail["KODEPRODUK_1"];
    }
	public function getDetailFee1(){
		return $this->detail["FEEPRODUK_1"];
    }
	public function getDetailProduk2(){
		return $this->detail["KODEPRODUK_2"];
    }
	public function getDetailFee2(){
		return $this->detail["FEEPRODUK_2"];
    }
	public function getDetailProduk3(){
		return $this->detail["KODEPRODUK_3"];
    }
	public function getDetailFee3(){
		return $this->detail["FEEPRODUK_3"];
    }
	public function getDetailProduk4(){
		return $this->detail["KODEPRODUK_4"];
    }
	public function getDetailFee4(){
		return $this->detail["FEEPRODUK_4"];
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