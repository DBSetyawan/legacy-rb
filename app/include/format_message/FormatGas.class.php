<?php

class FormatGas extends FormatMandatory {
    var $data;
    
	function FormatGas($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }

    function getSWITCHERID() {
        return $this->data["SWITCHERID"];
    }

    function getBILLERCODE() {
        return $this->data["BILLERCODE"];
    }

    function getCUSTOMERID1() {
        return $this->data["CUSTOMERID1"];
    }

    function getCUSTOMERID2() {
        return $this->data["CUSTOMERID2"];
    }

    function getCUSTOMERID3() {
        return $this->data["CUSTOMERID3"];
    }

    function getBILLQUANTITY() {
        return $this->data["BILLQUANTITY"];
    }

    function getGWREFNUM() {
        return $this->data["GWREFNUM"];
    }

    function getSWREFNUM() {
        return $this->data["SWREFNUM"];
    }

    function getCUSTOMERNAME() {
        return $this->data["CUSTOMERNAME"];
    }

    function getCUSTOMERADDRESS() {
        return $this->data["CUSTOMERADDRESS"];
    }

    function getCUSTOMERDETAILINFORMATION() {
        return $this->data["CUSTOMERDETAILINFORMATION"];
    }

    function getBILLERADMINCHARGE() {
        return $this->data["BILLERADMINCHARGE"];
    }

    function getTOTALBILLAMOUNT() {
        return $this->data["TOTALBILLAMOUNT"];
    }

    function getPDAMNAME() {
        return $this->data["PDAMNAME"];
    }

    function getMONTHPERIOD1() {
        return $this->data["MONTHPERIOD1"];
    }

    function getYEARPERIOD1() {
        return $this->data["YEARPERIOD1"];
    }

    function getFIRSTMETERREAD1() {
        return $this->data["FIRSTMETERREAD1"];
    }

    function getLASTMETERREAD1() {
        return $this->data["LASTMETERREAD1"];
    }

    function getPENALTY1() {
        return $this->data["PENALTY1"];
    }

    function getBILLAMOUNT1() {
        return $this->data["BILLAMOUNT1"];
    }

    function getMISCAMOUNT1() {
        return $this->data["MISCAMOUNT1"];
    }

    function getMONTHPERIOD2() {
        return $this->data["MONTHPERIOD2"];
    }

    function getYEARPERIOD2() {
        return $this->data["YEARPERIOD2"];
    }

    function getFIRSTMETERREAD2() {
        return $this->data["FIRSTMETERREAD2"];
    }

    function getLASTMETERREAD2() {
        return $this->data["LASTMETERREAD2"];
    }

    function getPENALTY2() {
        return $this->data["PENALTY2"];
    }

    function getBILLAMOUNT2() {
        return $this->data["BILLAMOUNT2"];
    }

    function getMISCAMOUNT2() {
        return $this->data["MISCAMOUNT2"];
    }

    function getMONTHPERIOD3() {
        return $this->data["MONTHPERIOD3"];
    }

    function getYEARPERIOD3() {
        return $this->data["YEARPERIOD3"];
    }

    function getFIRSTMETERREAD3() {
        return $this->data["FIRSTMETERREAD3"];
    }

    function getLASTMETERREAD3() {
        return $this->data["LASTMETERREAD3"];
    }

    function getPENALTY3() {
        return $this->data["PENALTY3"];
    }

    function getBILLAMOUNT3() {
        return $this->data["BILLAMOUNT3"];
    }

    function getMISCAMOUNT3() {
        return $this->data["MISCAMOUNT3"];
    }

    function getMONTHPERIOD4() {
        return $this->data["MONTHPERIOD4"];
    }

    function getYEARPERIOD4() {
        return $this->data["YEARPERIOD4"];
    }

    function getFIRSTMETERREAD4() {
        return $this->data["FIRSTMETERREAD4"];
    }

    function getLASTMETERREAD4() {
        return $this->data["LASTMETERREAD4"];
    }

    function getPENALTY4() {
        return $this->data["PENALTY4"];
    }

    function getBILLAMOUNT4() {
        return $this->data["BILLAMOUNT4"];
    }

    function getMISCAMOUNT4() {
        return $this->data["MISCAMOUNT4"];
    }

    function getMONTHPERIOD5() {
        return $this->data["MONTHPERIOD5"];
    }

    function getYEARPERIOD5() {
        return $this->data["YEARPERIOD5"];
    }

    function getFIRSTMETERREAD5() {
        return $this->data["FIRSTMETERREAD5"];
    }

    function getLASTMETERREAD5() {
        return $this->data["LASTMETERREAD5"];
    }

    function getPENALTY5() {
        return $this->data["PENALTY5"];
    }

    function getBILLAMOUNT5() {
        return $this->data["BILLAMOUNT5"];
    }

    function getMISCAMOUNT5() {
        return $this->data["MISCAMOUNT5"];
    }

    function getMONTHPERIOD6() {
        return $this->data["MONTHPERIOD6"];
    }

    function getYEARPERIOD6() {
        return $this->data["YEARPERIOD6"];
    }

    function getFIRSTMETERREAD6() {
        return $this->data["FIRSTMETERREAD6"];
    }

    function getLASTMETERREAD6() {
        return $this->data["LASTMETERREAD6"];
    }

    function getPENALTY6() {
        return $this->data["PENALTY6"];
    }

    function getBILLAMOUNT6() {
        return $this->data["BILLAMOUNT6"];
    }

    function getMISCAMOUNT6() {
        return $this->data["MISCAMOUNT6"];
    }

}
