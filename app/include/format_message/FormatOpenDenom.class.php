<?php
class FormatOpenDenom extends FormatMandatory {

    var $data;
    function FormatOpenDenom($format, $message) {
        $frm = explode("*", $format);
        $msg = explode("*", $message);
        for ($i = 0; $i < count($msg); $i++) {
            $this->data[$frm[$i]] = $msg[$i];
        }
    }

    public function getCustomerName() {
        return $this->data["CUSTOMERNAME"];
    }

    public function getAmount() {
        return $this->data["AMOUNT"];
    }

    public function getRefNo() {
        return $this->data["REFNO"];
    }

    public function getRefNo2() {
        return $this->data["REFNO2"];
    }

    public function getBillerCode() {
        return $this->data["BILLERCODE"];
    }

    public function getBillerStan() {
        return $this->data["BILLERSTAN"];
    }

    public function getFeeAmount() {
        return $this->data["FEEAMOUNT"];
    }

    public function getMerchantId() {
        return $this->data["MERCHANTID"];
    }

    public function getMerchantName() {
        return $this->data["MERCHANTNAME"];
    }

    public function getMerchantType() {
        return $this->data["MERCHANTTYPE"];
    }

    public function getAddtData() {
        return $this->data["ADDTDATA"];
    }

    public function getAddtData2() {
        return $this->data["ADDTDATA2"];
    }

    public function getForwardingId() {
        return $this->data["FORWARDINGID"];
    }

    public function getTerminalId() {
        return $this->data["TERMINALID"];
    }

    public function getIssuerId() {
        return $this->data["ISSUERID"];
    }

    public function getTrxCode() {
        return $this->data["TRXCODE"];
    }

    public function getPosentryMod() {
        return $this->data["POSENTRYMOD"];
    }

    public function getSettlementDate() {
        return $this->data["SETTLEMENTDATE"];
    }

    public function getCaptureDate() {
        return $this->data["CAPTUREDATE"];
    }

    public function getApprovalCode() {
        return $this->data["APPROVALCODE"];
    }

    public function getAccNo() {
        return $this->data["ACCNO"];
    }

}

?>