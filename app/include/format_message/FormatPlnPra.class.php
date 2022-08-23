<?php

class FormatPlnPra extends FormatMandatory {
	var $data;

	function FormatPlnPra($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
	
	public function getSwitcherId(){
		return $this->data["SWITCHERID"];
	}
	public function getNomorMeter(){
		return $this->data["NOMORMETER"];
	}
	public function getIdPelanggan(){
		return $this->data["IDPELANGGAN"];
	}
	public function getFlag(){
		return $this->data["FLAG"];
	}
	public function getNoRefl(){		
		return $this->data["NOREF1"];
	}
	public function getNoRef2(){
		return $this->data["NOREF2"];
	}
	public function getVendingReceiptNumber(){
		return $this->data["VENDINGRECEIPTNUMBER"];
	}
	public function getNamaPelanggan(){
		return $this->data["NAMAPELANGGAN"];
	}
	public function getSubscriberSegmentation(){
		return $this->data["SUBSCRIBERSEGMENTATION"];
	}
	public function getPowerConsumingCategory(){
		return $this->data["POWERCONSUMINGCATEGORY"];
	}
	public function getMinorUnitOfAdminCharge(){
		return $this->data["MINORUNITOFADMINCHARGE"];
	}
	public function getAdminCharge(){
		return $this->data["ADMINCHARGE"];
	}
	public function getBuyingOption(){
		return $this->data["BUYINGOPTION"];
	}
	public function getDistributionCode(){
		return $this->data["DISTRIBUTIONCODE"];
	}
	public function getServiceUnit(){
		return $this->data["SERVICEUNIT"];
	}
	public function getServiceUnitPhone(){
		return $this->data["SERVICEUNITPHONE"];
	}
	public function getMaxKwhLimit(){
		return $this->data["MAXKWHLIMIT"];
	}
	public function getTotalRepeatUnsoldToken(){
		return $this->data["TOTALREPEATUNSOLDTOKEN"];
	}
	public function getUnsold1(){
		return $this->data["UNSOLD1"];
	}
	public function getUnsold2(){
		return $this->data["UNSOLD2"];
	}
	public function getTokenPln(){
		return $this->data["TOKENPLN"];
	}
	public function getMinorUnitStampDuty(){
		return $this->data["MINORUNITSTAMPDUTY"];
	}	
	public function getStampDuty(){
		return $this->data["STAMPDUTY"];
	}
	public function getMinorUnitPPN(){
		return $this->data["MINORUNITPPN"];
	}
	public function getPPN(){
		return $this->data["PPN"];
	}
	public function getMinorUnitPPJ(){
		return $this->data["MINORUNITPPJ"];
	}
	public function getPPJ(){
		return $this->data["PPJ"];
	}
	public function getMinorUnitCustomerPayablesInstallment(){
		return $this->data["MINORUNITCUSTOMERPAYABLESINSTALLMENT"];
	}
	public function getCustomerPayablesInstallment(){
		return $this->data["CUSTOMERPAYABLESINSTALLMENT"];
	}
	public function getMinorUnitOfPowerPurchase(){
		return $this->data["MINORUNITOFPOWERPURCHASE"];
	}
	public function getPowerPurchase(){
		return $this->data["POWERPURCHASE"];
	}
	public function getMinorUnitOfPurchasedKWHUnit(){
		return $this->data["MINORUNITOFPURCHASEDKWHUNIT"];
	}
	public function getPurchasedKWHUnit(){
		return $this->data["PURCHASEDKWHUNIT"];
	}
	public function getInfoText(){
		return $this->data["INFOTEXT"];
	}
}
?>