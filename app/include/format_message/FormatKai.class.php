<?php

class FormatKai extends FormatMandatory {
    var $data;
    
	function FormatKai($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }

    function getERRCODE(){
        return $this->data["ERRCODE"];
    }
    function getERRMSG(){
        return $this->data["ERRMSG"];
    }
    function getORG(){
        return $this->data["ORG"];
    }
    function getDES(){
        return $this->data["DES"];
    }
    function getDEPDATE(){
        return $this->data["DEPDATE"];
    }
    function getARVDATE(){
        return $this->data["ARVDATE"];
    }
    function getSCHEDULE(){
        return $this->data["SCHEDULE"];
    }
    function getTRAINNO(){
        return $this->data["TRAINNO"];
    }
    function getCLASS(){
        return $this->data["CLASS"];
    }
    function getSUBCLASS(){
        return $this->data["SUBCLASS"];
    }
    function getNUMPAXADULT(){
        return $this->data["NUMPAXADULT"];
    }
    function getNUMPAXCHILD(){
        return $this->data["NUMPAXCHILD"];
    }
    function getNUMPAXINFANT(){
        return $this->data["NUMPAXINFANT"];
    }
    function getADULTNAME1(){
        return $this->data["ADULTNAME1"];
    }
    function getADULTBIRTHDATE1(){
        return $this->data["ADULTBIRTHDATE1"];
    }
    function getADULTMOBILE1(){
        return $this->data["ADULTMOBILE1"];
    }
    function getADULTIDNO1(){
        return $this->data["ADULTIDNO1"];
    }
    function getADULTNAME2(){
        return $this->data["ADULTNAME2"];
    }
    function getADULTBIRTHDATE2(){
        return $this->data["ADULTBIRTHDATE2"];
    }
    function getADULTMOBILE2(){
        return $this->data["ADULTMOBILE2"];
    }
    function getADULTIDNO2(){
        return $this->data["ADULTIDNO2"];
    }
    function getADULTNAME3(){
        return $this->data["ADULTNAME3"];
    }
    function getADULTBIRTHDATE3(){
        return $this->data["ADULTBIRTHDATE3"];
    }
    function getADULTMOBILE3(){
        return $this->data["ADULTMOBILE3"];
    }
    function getADULTIDNO3(){
        return $this->data["ADULTIDNO3"];
    }
    function getADULTNAME4(){
        return $this->data["ADULTNAME4"];
    }
    function getADULTBIRTHDATE4(){
        return $this->data["ADULTBIRTHDATE4"];
    }
    function getADULTMOBILE4(){
        return $this->data["ADULTMOBILE4"];
    }
    function getADULTIDNO4(){
        return $this->data["ADULTIDNO4"];
    }
    function getCHILDNAME1(){
        return $this->data["CHILDNAME1"];
    }
    function getCHILDBIRTHDATE1(){
        return $this->data["CHILDBIRTHDATE1"];
    }
    function getCHILDNAME2(){
        return $this->data["CHILDNAME2"];
    }
    function getCHILDBIRTHDATE2(){
        return $this->data["CHILDBIRTHDATE2"];
    }
    function getCHILDNAME3(){
        return $this->data["CHILDNAME3"];
    }
    function getCHILDBIRTHDATE3(){
        return $this->data["CHILDBIRTHDATE3"];
    }
    function getCHILDNAME4(){
        return $this->data["CHILDNAME4"];
    }
    function getCHILDBIRTHDATE4(){
        return $this->data["CHILDBIRTHDATE4"];
    }
    function getINFANTNAME1(){
        return $this->data["INFANTNAME1"];
    }
    function getINFANTBIRTHDATE1(){
        return $this->data["INFANTBIRTHDATE1"];
    }
    function getINFANTNAME2(){
        return $this->data["INFANTNAME2"];
    }
    function getINFANTBIRTHDATE2(){
        return $this->data["INFANTBIRTHDATE2"];
    }
    function getINFANTNAME3(){
        return $this->data["INFANTNAME3"];
    }
    function getINFANTBIRTHDATE3(){
        return $this->data["INFANTBIRTHDATE3"];
    }
    function getINFANTNAME4(){
        return $this->data["INFANTNAME4"];
    }
    function getINFANTBIRTHDATE4(){
        return $this->data["INFANTBIRTHDATE4"];
    }
    function getCALLER(){
        return $this->data["CALLER"];
    }
    function getNUMCODE(){
        return $this->data["NUMCODE"];
    }
    function getBOOKCODE(){
        return $this->data["BOOKCODE"];
    }
    function getSEAT(){
        return $this->data["SEAT"];
    }
    function getNORMALSALES(){
        return $this->data["NORMALSALES"];
    }
    function getEXTRAFEE(){
        return $this->data["EXTRAFEE"];
    }
    function getBOOKBALANCE(){
        return $this->data["BOOKBALANCE"];
    }
    function getSEATMAPNULL(){
        return $this->data["SEATMAPNULL"];
    }
    function getWAGONCODE(){
        return $this->data["WAGONCODE"];
    }
    function getWAGONNO(){
        return $this->data["WAGONNO"];
    }
    function getWAGONCODE1(){
        return $this->data["WAGONCODE1"];
    }
    function getWAGONNO1(){
        return $this->data["WAGONNO1"];
    }
    function getSEATROW1(){
        return $this->data["SEATROW1"];
    }
    function getSEATCOL1(){
        return $this->data["SEATCOL1"];
    }
    function getWAGONCODE2(){
        return $this->data["WAGONCODE2"];
    }
    function getWAGONNO2(){
        return $this->data["WAGONNO2"];
    }
    function getSEATROW2(){
        return $this->data["SEATROW2"];
    }
    function getSEATCOL2(){
        return $this->data["SEATCOL2"];
    }
    function getWAGONCODE3(){
        return $this->data["WAGONCODE3"];
    }
    function getWAGONNO3(){
        return $this->data["WAGONNO3"];
    }
    function getSEATROW3(){
        return $this->data["SEATROW3"];
    }
    function getSEATCOL3(){
        return $this->data["SEATCOL3"];
    }
    function getWAGONCODE4(){
        return $this->data["WAGONCODE4"];
    }
    function getWAGONNO4(){
        return $this->data["WAGONNO4"];
    }
    function getSEATROW4(){
        return $this->data["SEATROW4"];
    }
    function getSEATCOL4(){
        return $this->data["SEATCOL4"];
    }
    function getCANCELREASON(){
        return $this->data["CANCELREASON"];
    }
    function getSTATUSCANCEL(){
        return $this->data["STATUSCANCEL"];
    }
    function getREFUND(){
        return $this->data["REFUND"];
    }
    function getPAYTYPE(){
        return $this->data["PAYTYPE"];
    }
    function getROUTE(){
        return $this->data["ROUTE"];
    }
    function getPAX(){
        return $this->data["PAX"];
    }
    function getPAXNUM(){
        return $this->data["PAXNUM"];
    }
    function getREVENUE(){
        return $this->data["REVENUE"];
    }
    function getTRAINNAME(){
        return $this->data["TRAINNAME"];
    }
    function getORIGINATION(){
        return $this->data["ORIGINATION"];
    }
    function getDEPTIME(){
        return $this->data["DEPTIME"];
    }
    function getDESTINATION(){
        return $this->data["DESTINATION"];
    }
    function getARVTIME(){
        return $this->data["ARVTIME"];
    }
    function getSEATNUMBER(){
        return $this->data["SEATNUMBER"];
    }
    function getPRICEADULT(){
        return $this->data["PRICEADULT"];
    }
    function getPRICECHILD(){
        return $this->data["PRICECHILD"];
    }
    function getPRICEINFANT(){
        return $this->data["PRICEINFANT"];
    }

}