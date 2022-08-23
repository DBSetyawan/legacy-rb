<?php

class FormatPgn extends FormatMandatory {
	var $data;
//****	
	function FormatPgn($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
	
	function getCustomerId(){
		return $this->data["CUSTOMERID"];
	}
	function getCustomerName(){
		return $this->data["CUSTOMERNAME"];
	}
	function getUsage(){
		return $this->data["USAGE"];
	}
	function getPeriode(){
		return $this->data["PERIODE"];
	}
	function getInvoiceNumber(){
		return $this->data["INVOICENUMBER"];
	}
	function getTagihan(){
		return $this->data["TAGIHAN"];
	}
	function getAdminBank(){
		return $this->data["ADMINBANK"];
	}
	function getTotal(){
		return $this->data["TOTAL"];
	}
	function getCharge(){
		return $this->data["CHARGE"];
	}
	function getSaldoRet(){
		return $this->data["SALDORET"];
	}
	function getRefId(){
		return $this->data["REFFID"];
	}
	function getTrxId(){
		return $this->data["TRXID"];
	}
}


?>