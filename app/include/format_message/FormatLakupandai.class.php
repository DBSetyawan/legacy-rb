<?php

class FormatLakupandai extends FormatMandatory {
	var $data;
//****	
	function FormatLakupandai($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
	function getCmd(){
		return $this->data["CMD"];
	}
	function getDealerId(){
		return $this->data["DEALERID"];
	}
	function getSystemId(){
		return $this->data["SYSTEMID"];
	}
	function getAccountNum(){
		return $this->data["ACCOUNTNUM"];
	}
	function getOption(){
		return $this->data["OPTION"];
	}
	function getIdAgen(){
		return $this->data["IDAGEN"];
	}
	function getCurrency(){
		return $this->data["CURRENCY"];
	}
	function getAccountStatus(){
		return $this->data["ACCOUNT_STATUS"];
	}
	function getProduct(){
		return $this->data["PRODUCT"];
	}
	function getHomeBranch(){
		return $this->data["HOMEBRANCH"];
	}
	function getCifNum(){
		return $this->data["CIFNUM"];
	}
	function getName(){
		return $this->data["NAME"];
	}
	function getNameRek(){
		return $this->data["NAMEREK"];
	}
	function getCurrentBalance(){
		return $this->data["CURRENTBALANCE"];
	}
	function getAvailableBalance(){
		return $this->data["AVAILABLEBALANCE"];
	}
	function getOpenDate(){
		return $this->data["OPENDATE"];
	}
	function getAddress_Street(){
	return $this->data["ADDRESS_STREET"];
	}
	function getAddress_Rt(){
		return $this->data["ADDRESS_RT"];
	}
	function getAddress1(){
		return $this->data["ADDRESS1"];
	}
	function getAddress2(){
		return $this->data["ADDRESS2"];
	}
	function getAddress3(){
		return $this->data["ADDRESS3"];
	}
	function getAddress4(){
		return $this->data["ADDRESS4"];
	}
	function getPostCode(){
		return $this->data["POSTCODE"];
	}
	function getHomePhone(){
		return $this->data["HOMEPHONE"];
	}
	function getFax(){
		return $this->data["FAX"];
	}
	function getOfficePhone(){
		return $this->data["OFFICEPHONE"];
	}
	function getMobilePhone(){
		return $this->data["MOBILEPHONE"];
	}
	function getAddress1AA(){
		return $this->data["ADDRESS1AA"];
	}
	function getAddress2AA(){
		return $this->data["ADDRESS2AA"];
	}
	function getAddress3AA(){
		return $this->data["ADDRESS3AA"];
	}
	function getAddress4AA(){
		return $this->data["ADDRESS4AA"];
	}
	function getPostCodeA(){
		return $this->data["POSTCODEAA"];
	}
	function getAccountProductType(){
		return $this->data["ACCOUNTPRODUCTTYPE"];
	}
	function getAccType(){
		return $this->data["ACCTYPE"];
	}
	function getSubCat(){
		return $this->data["SUBCAT"];
	}
	function getAvailableInterest()
	{
		return $this->data['AVAILABLEINTEREST'];
	}
	function getLienbalance(){
		return $this->data["LIENBALANCE"];
	}
	function getUnclearbalance(){
		return $this->data["UNCLEARBALANCE"];
	}
	function getInterestrate(){
		return $this->data["INTERESTRATE"];
	}
	function getKtp(){
		return $this->data["KTP"];
	}
	function getNpwp(){
		return $this->data["NPWP"];
	}
	function getJenis_Pekerjaan(){
		return $this->data["JENIS_PEKERJAAN"];
	}
	function getEmail(){
		return $this->data["EMAIL"];
	}
	function getKode_Wil_Bi(){
		return $this->data["KODE_WIL_BI"];
	}
	function getKode_Cabang(){
		return $this->data["KODE_CABANG"];
	}
	function getKode_Loket(){
		return $this->data["KODE_LOKET"];
	}
	function getKode_Mitra(){
		return $this->data["KODE_MITRA"];
	}
	function getTgl_Input(){
		return $this->data["TGL_INPUT"];
	}
	function getCa_Gen_Status(){
		return $this->data["CA_GEN_STATUS"];
	}
	function getClientId(){
		return $this->data["CLIENTID"];
	}
	function getClient_Account_Num(){
		return $this->data["CLIENT_ACCOUNT_NUM"];
	}
	function getReq_Id(){
		return $this->data["REQ_ID"];
	}
	function getReq_Time(){
		return $this->data["REQ_TIME"];
	}
	function getCust_Acc_Num(){
		return $this->data["CUST_ACC_NUM"];
	}
	function getAmount(){
			return $this->data["AMOUNT"];
	}
	function getTransaction_Journal(){
		return $this->data["TRANSACTION_JOURNAL"];
	}
	function getCustomer_Otp(){
		return $this->data["CUSTOMER_OTP"];
	}
	function getCust_First_Name(){
		return $this->data["CUST_FIRST_NAME"];
	}
	function getCust_Midle_Name(){
		return $this->data["CUST_MIDLE_NAME"];
	}
	function getCust_Last_Name(){
		return $this->data["CUST_LAST_NAME"];
	}
	function getCust_Place_Of_Birth(){
		return $this->data["CUST_PLACE_OF_BIRTH"];
	}
	function getCust_Date_Of_Birth(){
		return $this->data["CUST_DATE_OF_BIRTH"];
	}
	function getCust_Gender(){
		return $this->data["CUST_GENDER"];
	}
	function getCust_Is_Married(){
		return $this->data["CUST_IS_MARRIED"];
	}
	function getCust_Income(){
		return $this->data["CUST_INCOME"];
	}
	function getPin_Transaksi(){
		return $this->data["PIN_TRANSAKSI"];
	}
	function getImage_Name(){
		return $this->data["IMAGE_NAME"];
	}
	function getImage_Url(){
		return $this->data["IMAGE_URL"];
	}
	function getFile_Name(){
		return $this->data["FILE_NAME"];
	}
	function getImage_Foto_Name(){
		return $this->data["IMAGE_FOTO_NAME"];
	}
	function getImage_Foto_Url(){
		return $this->data["IMAGE_FOTO_URL"];
	}
	function getFile_Name_Foto(){
		return $this->data["FILE_NAME_FOTO"];
	}
}


?>