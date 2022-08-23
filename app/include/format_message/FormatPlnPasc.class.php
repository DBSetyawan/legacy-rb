<?php

class FormatPlnPasc extends FormatMandatory {
	var $data;

	function FormatPlnPasc($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
	
	public function getSwitcherId(){
		return $this->data["SWITCHERID"];
	}
	public function getSubscriberId(){
		return $this->data["SUBSCRIBERID"];
	}
	public function getJumlahBill(){
		return $this->data["BILLSTATUS"];
	}
	public function getPaymentStatus(){
		return $this->data["PAYMENTSTATUS"];
	}
	public function getTotalOutstandingBill(){		
		return $this->data["TOTALOUTSTANDINGBILL"];
	}
	public function getSwReferenceNumber(){
		return $this->data["SWREFERENCENUMBER"];
	}
	public function getNamaPelanggan(){
		return $this->data["SUBSCRIBERNAME"];
	}
	public function getServiceUnit(){
		return $this->data["SERVICEUNIT"];
	}
	public function getServiceUnitPhone(){
		return $this->data["SERVICEUNITPHONE"];
	}
	public function getSubscriberSegmentation(){
		return $this->data["SUBSCRIBERSEGMENTATION"];
	}
	public function getPowerConsumingCategory(){
		return $this->data["POWERCONSUMINGCATEGORY"];
	}
	public function getTotalAdminCharge(){
		return $this->data["TOTALADMINCHARGE"];
	}
	public function getBillPeriod1(){
		return $this->data["BLTH1"];
	}
	public function getDueDate1(){
		return $this->data["DUEDATE1"];
	}
	public function getMeterReadDate1(){
		return $this->data["METERREADDATE1"];
	}
	public function getRpTag1(){
		return $this->data["RPTAG1"];
	}
	public function getIncentive1(){
		return $this->data["INCENTIVE1"];
	}
	public function getValueAddedTax1(){
		return $this->data["VALUEADDEDTAX1"];
	}
	public function getPenaltyFee1(){
		return $this->data["PENALTYFEE1"];
	}
	public function getPreviousMeterReading11(){
		return $this->data["SLALWBP1"];
	}
	public function getCurentMeterReading11(){
		return $this->data["SAHLWBP1"];
	}
	public function getPreviousMeterReading21(){
		return $this->data["SLAWBP1"];
	}
	public function getCurentMeterReading21(){
		return $this->data["SAHWBP1"];
	}
	public function getPreviousMeterReading31(){
		return $this->data["SLAKVARH1"];
	}
	public function getCurentMeterReading31(){
		return $this->data["SAHKVARH1"];
	}
	public function getBillPeriod2(){
		return $this->data["BLTH2"];
	}
	public function getDueDate2(){
		return $this->data["DUEDATE2"];
	}
	public function getMeterReadDate2(){
		return $this->data["METERREADDATE2"];
	}
	public function getRpTag2(){
		return $this->data["RPTAG2"];
	}
	public function getIncentive2(){
		return $this->data["INCENTIVE2"];
	}
	public function getValueAddedTax2(){
		return $this->data["VALUEADDEDTAX2"];
	}
	public function getPenaltyFee2(){
		return $this->data["PENALTYFEE2"];
	}
	public function getPreviousMeterReading12(){
		return $this->data["SLALWBP2"];
	}
	public function getCurentMeterReading12(){
		return $this->data["SAHLWBP2"];
	}
	public function getPreviousMeterReading22(){
		return $this->data["SLAWBP2"];
	}
	public function getCurentMeterReading22(){
		return $this->data["SAHWBP2"];
	}
	public function getPreviousMeterReading32(){
		return $this->data["SLAKVARH2"];
	}
	public function getCurentMeterReading32(){
		return $this->data["SAHKVARH2"];
	}
	public function getBillPeriod3(){
		return $this->data["BLTH3"];
	}
	public function getDueDate3(){
		return $this->data["DUEDATE3"];
	}
	public function getMeterReadDate3(){
		return $this->data["METERREADDATE3"];
	}
	public function getRpTag3(){
		return $this->data["RPTAG3"];
	}
	public function getIncentive3(){
		return $this->data["INCENTIVE3"];
	}
	public function getValueAddedTax3(){
		return $this->data["VALUEADDEDTAX3"];
	}
	public function getPenaltyFee3(){
		return $this->data["PENALTYFEE3"];
	}
	public function getPreviousMeterReading13(){
		return $this->data["SLALWBP3"];
	}
	public function getCurentMeterReading13(){
		return $this->data["SAHLWBP3"];
	}
	public function getPreviousMeterReading23(){
		return $this->data["SLAWBP3"];
	}
	public function getCurentMeterReading23(){
		return $this->data["SAHWBP3"];
	}
	public function getPreviousMeterReading33(){
		return $this->data["SLAKVARH3"];
	}
	public function getCurentMeterReading33(){
		return $this->data["SAHKVARH3"];
	}
	public function getBillPeriod4(){
		return $this->data["BLTH4"];
	}
	public function getDueDate4(){
		return $this->data["DUEDATE4"];
	}
	public function getMeterReadDate4(){
		return $this->data["METERREADDATE4"];
	}
	public function getRpTag4(){
		return $this->data["RPTAG4"];
	}
	public function getIncentive4(){
		return $this->data["INCENTIVE4"];
	}
	public function getValueAddedTax4(){
		return $this->data["VALUEADDEDTAX4"];
	}
	public function getPenaltyFee4(){
		return $this->data["PENALTYFEE4"];
	}
	public function getPreviousMeterReading14(){
		return $this->data["SLALWBP4"];
	}
	public function getCurentMeterReading14(){
		return $this->data["SAHLWBP4"];
	}
	public function getPreviousMeterReading24(){
		return $this->data["SLAWBP4"];
	}
	public function getCurentMeterReading24(){
		return $this->data["SAHWBP4"];
	}
	public function getPreviousMeterReading34(){
		return $this->data["SLAKVARH4"];
	}
	public function getCurentMeterReading34(){
		return $this->data["SAHKVARH4"];
	}
	public function getAlamat(){
		return $this->data["ALAMAT"];
	}
	public function getPlnNPWP(){
		return $this->data["PLNNPWP"];
	}
	public function getSubscriberNPWP(){
		return $this->data["SUBSCRIBERNPWP"];
	}
	public function getTotalRpTag(){
		return $this->data["TOTALRPTAG"];
	}
	public function getInfoTeks(){
		return $this->data["INFOTEKS"];
	}
}
?>