<?php

class FormatPajakPbb extends FormatMandatory {
	var $data;
//****	
	function FormatPajakPbb($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
	 
	public function getSwitcherId(){
		return $this->data["SWITCHERID"];
	}
	public function getBillerCode(){
		return $this->data["BILLERCODE"];
	}
	public function getCustomerID(){
		return $this->data["CUSTOMERID"];
	}
	public function getBillQuantity(){
		return $this->data["BILLQUANTITY"];
	}
	public function getNoref1(){
		return $this->data["NOREF1"];
	}
	public function getNoref2(){
		return $this->data["NOREF2"];
	}
	public function getNtp(){
		return $this->data["NTP"];
	}
	public function getNtb(){
		return $this->data["NTB"];
	}
	public function getKodePemda(){
		return $this->data["KODE_PEMDA"];
	}
	public function getNop(){
		return $this->data["NOP"];
	}
	public function getKodePajak(){
		return $this->data["KODE_PAJAK"];
	}
	public function getTahunPajak(){
		return $this->data["TAHUN_PAJAK"];
	}
	public function getNama(){
		return $this->data["NAMA"];
	}
	public function getLokasi(){
		return $this->data["LOKASI"];
	}
	public function getKelurahan(){
		return $this->data["KELURAHAN"];
	}
	public function getKecamatan(){
		return $this->data["KECAMATAN"];
	}
	public function getProvinsi(){
		return $this->data["PROVINSI"];
	}
	public function getLuasTanah(){
		return $this->data["LUAS_TANAH"];
	}
	public function getLuasBangunan(){
		return $this->data["LUAS_BANGUNAN"];
	}
	public function getTanggalJatuhTempo(){
		return $this->data["TANGGAL_JTH_TEMPO"];
	}
	public function getTagihan(){
		return $this->data["TAGIHAN"];
	}
	public function getDenda(){
		return $this->data["DENDA"];
	}
	public function getTotalBayar(){
		return $this->data["TOTAL_BAYAR"];
	}
}


?>