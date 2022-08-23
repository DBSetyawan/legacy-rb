<?php

class FormatResponse {
	var $data;

	function FormatResponse($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
    public function getCommand(){
        if($this->data["NKAISCH"]){
                return $this->data["NKAISCH"];
        }else if($this->data["NKAIBOO"]){
                return $this->data["NKAIBOO"];
        }else if($this->data["NKAIBOOK"]){
                return $this->data["NKAIBOOK"];
        }else if($this->data["NKAIUPD"]){
                return $this->data["NKAIUPD"];
        }else if($this->data["NKAICAN"]){
                return $this->data["NKAICAN"];
        }else if($this->data["NKAIPAY"]){
                return $this->data["NKAIPAY"];
        }else if($this->data["NKAIMAP"]){
                return $this->data["NKAIMAP"];
        }else if($this->data["NKAISEAT"]){
                return $this->data["NKAISEAT"];
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
    public function getTanggal(){
        $thn = substr($this->data["TANGGAL"], 2,2); $bln = substr($this->data["TANGGAL"], 4,2); $tgl = substr($this->data["TANGGAL"],6,2); $jam = substr($this->data["TANGGAL"],8,2);$mnt = substr($this->data["TANGGAL"],10,2);$dtk = substr($this->data["TANGGAL"],12,2); 
        $tanggal = $tgl . "/" . $bln . "/" . $thn . " " . $jam . ":" . $mnt . ":" . $dtk ;
        return $tanggal;
    }
    public function getVia(){
        return $this->data["VIA"];                                      
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
    public function getSaldo(){
        return $this->data["SALDO"];
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
    public function getErrCode(){
            return $this->data["ERRCODE"];                                   
    }
    public function getErrMsg(){
            return $this->data["ERRMSG"];
    }
    public function getOrg(){
            return $this->data["ORG"];
    }
    public function getDes(){
            return $this->data["DES"];
    }
    public function getDepDate(){		
            return $this->data["DEPDATE"];
    }
    public function getArvDate(){		
            return $this->data["ARVDATE"];
    }
    public function getSchedule(){
            return $this->data["SCHEDULE"];
    }
    public function getTrainNo(){
            return $this->data["TRAINNO"];
    }
    public function getClass(){
            return $this->data["CLASS"];
    }
    public function getSubClass(){
            return $this->data["SUBCLASS"];
    }
    public function getNumPaxAdult(){
            return $this->data["NUMPAXADULT"];
    }
    public function getNumPaxChild(){
            return $this->data["NUMPAXCHILD"];
    }
    public function getNumPaxInfant(){
            return $this->data["NUMPAXINFANT"];
    }
    public function getAdultName1(){
            return $this->data["ADULTNAME1"];
    }
    public function getAdultBirthdate1(){
            return $this->data["ADULTBIRTHDATE1"];
    }
    public function getAdultMobile1(){
            return $this->data["ADULTMOBILE1"];
    }
    public function getAdultIdNo1(){
            return $this->data["ADULTIDNO1"];
    }
    public function getAdultName2(){
            return $this->data["ADULTNAME2"];
    }
    public function getAdultBirthdate2(){
            return $this->data["ADULTBIRTHDATE2"];
    }
    public function getAdultMobile2(){
            return $this->data["ADULTMOBILE2"];
    }
    public function getAdultIdNo2(){
            return $this->data["ADULTIDNO2"];
    }
    public function getAdultName3(){
            return $this->data["ADULTNAME3"];
    }
    public function getAdultBirthdate3(){
            return $this->data["ADULTBIRTHDATE3"];
    }
    public function getAdultMobile3(){
            return $this->data["ADULTMOBILE3"];
    }
    public function getAdultIdNo3(){
            return $this->data["ADULTIDNO3"];
    }
    public function getAdultName4(){
            return $this->data["ADULTNAME4"];
    }
    public function getAdultBirthdate4(){
            return $this->data["ADULTBIRTHDATE4"];
    }
    public function getAdultMobile4(){
            return $this->data["ADULTMOBILE4"];
    }
    public function getAdultIdNo4(){
            return $this->data["ADULTIDNO4"];
    }
    public function getChildName1(){
            return $this->data["CHILDNAME1"];
    }
    public function getChildBirthdate1(){
            return $this->data["CHILDBIRTHDATE1"];
    }
    public function getChildName2(){
            return $this->data["CHILDNAME2"];
    }
    public function getChildBirthdate2(){
            return $this->data["CHILDBIRTHDATE2"];
    }
    public function getChildName3(){
            return $this->data["CHILDNAME3"];
    }
    public function getChildBirthdate3(){
            return $this->data["CHILDBIRTHDATE3"];
    }
    public function getChildName4(){
            return $this->data["CHILDNAME4"];
    }
    public function getChildBirthdate4(){
            return $this->data["CHILDBIRTHDATE4"];
    }
    public function getInfantName1(){
            return $this->data["INFANTNAME1"];
    }
    public function getInfantBirthdate1(){
            return $this->data["INFANTBIRTHDATE1"];
    }
    public function getInfantName2(){
            return $this->data["INFANTNAME2"];
    }
    public function getInfantBirthdate2(){
            return $this->data["INFANTBIRTHDATE2"];
    }
    public function getInfantName3(){
            return $this->data["INFANTNAME3"];
    }
    public function getInfantBirthdate3(){
            return $this->data["INFANTBIRTHDATE3"];
    }
    public function getInfantName4(){
            return $this->data["INFANTNAME4"];
    }
    public function getInfantBirthdate4(){
            return $this->data["INFANTBIRTHDATE4"];
    }
    public function getCaller(){
            return $this->data["CALLER"];
    }
    public function getNumCode(){
            return $this->data["NUMCODE"];
    }
    public function getBookCode(){
            return $this->data["BOOKCODE"];
    }
    public function getSeat(){
            return $this->data["SEAT"];
    }
    public function getNormalSales(){
            return $this->data["NORMALSALES"];
    }
    public function getExtraFee(){
            return $this->data["EXTRAFEE"];
    }
    public function getBookBalance(){
            return $this->data["BOOKBALANCE"];
    }
    public function getSeatMapNull(){
            return $this->data["SEATMAPNULL"];
    }
    public function getWagonCode(){
            return $this->data["WAGONCODE"];
    }
    public function getWagonNo(){
            return $this->data["WAGONNO"];
    }
    public function getWagonCode1(){
            return $this->data["WAGONCODE1"];
    }
    public function getWagonNo1(){
            return $this->data["WAGONNO1"];
    }
    public function getSeatRow1(){
            return $this->data["SEATROW1"];
    }
    public function getSeatCol1(){
            return $this->data["SEATCOL1"];
    }
    public function getWagonCode2(){
            return $this->data["WAGONCODE2"];
    }
    public function getWagonNo2(){
            return $this->data["WAGONNO2"];
    }
    public function getSeatRow2(){
            return $this->data["SEATROW2"];
    }
    public function getSeatCol2(){
            return $this->data["SEATCOL2"];
    }
    public function getWagonCode3(){
            return $this->data["WAGONCODE3"];
    }
    public function getWagonNo3(){
            return $this->data["WAGONNO3"];
    }
    public function getSeatRow3(){
            return $this->data["SEATROW3"];
    }
    public function getSeatCol3(){
            return $this->data["SEATCOL3"];
    }
    public function getWagonCode4(){
            return $this->data["WAGONCODE4"];
    }
    public function getWagonNo4(){
            return $this->data["WAGONNO4"];
    }
    public function getSeatRow4(){
            return $this->data["SEATROW4"];
    }
    public function getSeatCol4(){
            return $this->data["SEATCOL4"];
    }
    public function getCancelReason(){
            return $this->data["CANCELREASON"];
    }
    public function getStatusCancel(){
            return $this->data["STATUSCANCEL"];
    }
    public function getRefund(){
            return $this->data["REFUND"];
    }
    public function getPayType(){
            return $this->data["PAYTYPE"];
    }
    public function getRoute(){
            return $this->data["ROUTE"];
    }
    public function getPax(){
            return $this->data["PAX"];
    }
    public function getPaxNum(){
            return $this->data["PAXNUM"];
    }
    public function getRevenue(){
            return $this->data["REVENUE"];
    }
    public function getTrainName(){
            return $this->data["TRAINNAME"];
    }
    public function getOrigination(){
            return $this->data["ORIGINATION"];
    }
    public function getDepTime(){
            return $this->data["DEPTIME"];
    }
    public function getDestination(){
            return $this->data["DESTINATION"];
    }
    public function getArvTime(){
            return $this->data["ARVTIME"];
    }
    public function getSeatNumber(){
            return $this->data["SEATNUMBER"];
    }
    public function getPriceAdult(){
            return $this->data["PRICEADULT"];
    }
    public function getPriceChild(){
            return $this->data["PRICECHILD"];
    }
    public function getPriceInfant(){
            return $this->data["PRICEINFANT"];
    }
    public function getTotal(){
        $total = $this->data["NOMINAL"] + $this->data["NOMINALADMIN"];
            return $total;
    }
}
?>