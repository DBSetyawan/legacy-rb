<?php

class FormatNewAsuransi extends FormatMandatory {
    var $data;

    function FormatNewAsuransi($format, $message){
        $frm = explode("*",$format);
        $msg = explode("*",$message);
        for($i=0;$i<count($frm);$i++){
            $this->data[$frm[$i]] = $msg[$i];
        }
    }
    
    public function getSwitcherId(){
            return $this->data["SWITCHERID"];
    }
    public function getBillerCode(){
            return $this->data["BILLER_CODE"];
    }
    public function getCustomerId(){
            return $this->data["CUSTOMER_ID"];
    }
    public function getBillQuantity(){
            return $this->data["BILL_QUANTITY"];
    }
    public function getNoRef1(){
            return $this->data["NOREF1"];
    }
    public function getNoRef2(){
            return $this->data["NOREF2"];
    }
    public function getCustomerName(){
            return $this->data["CUSTOMER_NAME"];
    }
    public function getProdukKategori(){
            return $this->data["PRODUCT_CATEGORY"];
    }
    public function getBillAmount(){
            return $this->data["BILL_AMOUNT"];
    }
    public function getPenalty(){
            return $this->data["PENALTY"];
    }
    public function getStampDuty(){
            return $this->data["STAMP_DUTY"];
    }
    public function getPPN(){
            return $this->data["PPN"];
    }
    public function getAdminCharge(){
            return $this->data["ADMIN_CHARGE"];
    }
    public function getClaimAmount(){
            return $this->data["CLAIM_AMOUNT"];
    }
    public function getBillerRefNum(){
            return $this->data["BILLER_REF_NUMBER"];
    }
    public function getPtName(){
            return $this->data["PT_NAME"];
    }
    public function getLastPaidPeriode(){
            return $this->data["LAST_PAID_PERIODE"];
    }
    public function getLastPaidDueDate(){
            return $this->data["LAST_PAID_DUE_DATE"];
    }
    public function getBillerAdminFee(){
            return $this->data["BILLER_ADMIN_FEE"];
    }
    public function getMiscFee(){
            return $this->data["MISC_FEE"];
    }
    public function getMiscNumber(){
            return $this->data["MISC_NUMBER"];
    }
    public function getPolicyNumber(){
            return $this->data["POLICY_NUMBER"];
    }
    public function getCustPhoneNum(){
            return $this->data["CUSTOMER_PHONE_NUMBER"];
    }
    public function getCustAddr(){
            return $this->data["CUSTOMER_ADDRESS"];
    }
    public function getCustGender(){
            return $this->data["CUSTOMER_GENDER"];
    }
    public function getCustJob(){
            return $this->data["CUSTOMER_JOB"];
    }
    public function getBenName(){
            return $this->data["BENEFICIARY_NAME"];
    }
    public function getBenPhoneNum(){
            return $this->data["BENEFICIARY_PHONE_NUMBER"];
    }
    public function getBenAddr(){
            return $this->data["BENEFICIARY_ADDRESS"];
    }
    public function getBenRelation(){
            return $this->data["BENEFICIARY_RELATION"];
    }
    public function getRegDate(){
            return $this->data["REGISTRATION_DATE"];
    }
    public function getStartDate(){
            return $this->data["START_DATE"];
    }
    public function getEndDate(){
            return $this->data["END_DATE"];
    }
    public function getInfoTeks(){
            return $this->data["INFO_TEKS"];
    }
    
}
?>