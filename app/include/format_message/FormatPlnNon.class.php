<?php

class FormatPlnNon extends FormatMandatory {
	var $data;

	function FormatPlnNon($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }

	public function getSwitcherId(){
		return $this->data["SWITCHERID"];
	}
	public function getRegistrationNumber(){
		return $this->data["REGISTRATIONNUMBER"];
	}
	public function getAreaCode(){
		return $this->data["AREACODE"];
	}
	public function getTransactionCode(){
		return $this->data["TRANSACTIONCODE"];
	}
	public function getTransactionName(){		
		return $this->data["TRANSACTIONNAME"];
	}
	public function getRegistrationDate(){
		return $this->data["REGISTRATIONDATE"];
	}
	public function getExpirationDate(){
		return $this->data["EXPIRATIONDATE"];
	}
	public function getSubscriberId(){
		return $this->data["SUBSCRIBERID"];
	}
	public function getSubscriberName(){
		return $this->data["SUBSCRIBERNAME"];
	}
	public function getPlnRefNumber(){
		return $this->data["PLNREFNUMBER"];
	}
	public function getSwRefNumber(){
		return $this->data["SWREFNUMBER"];
	}
	public function getServiceUnit(){
		return $this->data["SERVICEUNIT"];
	}
	public function getServiceUnitAddress(){
		return $this->data["SERVICEUNITADDRESS"];
	}
	public function getServiceUnitPhone(){
		return $this->data["SERVICEUNITPHONE"];
	}
	public function getTotalTransactionAmountMinor(){
		return $this->data["TOTALTRANSACTIONAMOUNTMINOR"];
	}
	public function getTotalTransactionAmount(){
		return $this->data["TOTALTRANSACTIONAMOUNT"];
	}
	public function getPlnBillMinorUnit(){
		return $this->data["PLNBILLMINORUNIT"];
	}
	public function getPlnBillValue(){
		return $this->data["PLNBILLVALUE"];
	}
	public function getAdminChargeMinorUnit(){
		return $this->data["ADMINCHARGEMINORUNIT"];
	}
	public function getAdminCharge(){
		return $this->data["ADMINCHARGE"];
	}
	public function getMutationNumber(){
		return $this->data["MUTATIONNUMBER"];
	}
	public function getSubscriberSegmentation(){
		return $this->data["SUBSCRIBERSEGMENTATION"];
	}	
	public function getPowerConsumingCategory(){
		return $this->data["POWERCONSUMINGCATEGORY"];
	}
	public function getTitakRepeat(){
		return $this->data["TOTALREPEAT"];
	}
	public function getCustomerDetailCode1(){
		return $this->data["CUSTOMERDETAILCODE1"];
	}
	public function getCustomDetailMinorUnit1(){
		return $this->data["CUSTOMDETAILMINORUNIT1"];
	}
	public function getCustomDetailValueAmount1(){
		return $this->data["CUSTOMDETAILVALUEAMOUNT1"];
	}
	public function getCustomerDetailCode2(){
		return $this->data["CUSTOMERDETAILCODE2"];
	}
	public function getCustomDetailMinorUnit2(){
		return $this->data["CUSTOMDETAILMINORUNIT2"];
	}
	public function getCustomDetailValueAmount2(){
		return $this->data["CUSTOMDETAILVALUEAMOUNT2"];
	}
	public function getInfoText(){
		return $this->data["INFOTEXT"];
	}
}


?>