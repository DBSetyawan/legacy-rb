<?php

class FormatBpjs extends FormatMandatory {

    var $data;

//****	
    function FormatBpjs($format, $message) {
        $frm = explode("*", $format);
        $msg = explode("*", $message);
        for ($i = 0; $i < count($msg); $i++) {
            $this->data[$frm[$i]] = $msg[$i];
        }
    }

    public function getCmd() {
        return $this->data["CMD"];
    }

    public function getNik() {
        return $this->data["NIK"];
    }

    public function getCustomerName() {
        return $this->data["CUSTOMER_NAME"];
    }

    public function getTanggal_Lahir() {
        return $this->data["TANGGAL_LAHIR"];
    }

    public function getKode_Bayar() {
        return $this->data["KODE_BAYAR"];
    }

    public function getFtrx_Id() {
        return $this->data["FTRXID"];
    }

    public function getData1() {
        return $this->data["DATA1"];
    }

    public function getData2() {
        return $this->data["DATA2"];
    }

    public function getData3() {
        return $this->data["DATA3"];
    }

    public function getData4() {
        return $this->data["DATA4"];
    }

    public function getTagihan() {
        return $this->data["TAGIHAN"];
    }

    public function getAdmin() {
        return $this->data["ADMIN"];
    }

    public function getKpj() {
        return $this->data["KPJ"];
    }

    public function getPekerjaan() {
        return $this->data["PEKERJAAN"];
    }

    public function getJam_Awal() {
        return $this->data["JAM_AWAL"];
    }

    public function getJam_Akhir() {
        return $this->data["JAM_AKHIR"];
    }

    public function getAlamat() {
        return $this->data["ALAMAT"];
    }

    public function getAlamat_Email() {
        return $this->data["ALAMAT_EMAIL"];
    }

    public function getLokasi_Kerja() {
        return $this->data["LOKASI_KERJA"];
    }

    public function getUpah() {
        return $this->data["UPAH"];
    }

    public function getKec() {
        return $this->data["KEC"];
    }

    public function getKel() {
        return $this->data["KEL"];
    }

    public function getKodepos() {
        return $this->data["KODEPOS"];
    }

    public function getHP() {
        return $this->data["HP"];
    }

    public function getOtp() {
        return $this->data["OTP"];
    }

    public function getJht() {
        return $this->data["JHT"];
    }

    public function getRate_Jht() {
        return $this->data["RATE_JHT"];
    }

    public function getJkk() {
        return $this->data["JKK"];
    }

    public function getRate_Jkk() {
        return $this->data["RATE_JKK"];
    }

    public function getJkm() {
        return $this->data["JKM"];
    }

    public function getRate_Jkm() {
        return $this->data["RATE_JKM"];
    }

    public function getIsnew() {
        return $this->data["ISNEW"];
    }

    public function getStatus_Bayar() {
        return $this->data["STATUS_BAYAR"];
    }

    public function getBiaya_Registrasi() {
        return $this->data["BIAYA_REGISTRASI"];
    }

    public function getKode_Kantor_Cabang() {
        return $this->data["KODE_KANTOR_CABANG"];
    }

    public function getAlamat_Kantor_Cabang() {
        return $this->data["ALAMAT_KANTOR_CABANG"];
    }

    public function getKode_Kabupaten() {
        return $this->data["KODE_KABUPATEN"];
    }

    public function getKode_Provinsi() {
        return $this->data["KODE_PROVINSI"];
    }

    public function getProgram() {
        return $this->data["PROGRAM"];
    }

    public function getPeriode() {
        return $this->data["PERIODE"];
    }

    public function getStatus_Hitung_Iuran() {
        return $this->data["STATUS_HITUNG_IURAN"];
    }
    
     public function getBlnJkm() {
        return $this->data["BLN_JKM"];
    }

    public function getBlnJkk() {
        return $this->data["BLN_JKK"];
    }

    public function getBlnJht() {
        return $this->data["BLN_JHT"];
    }
    

    public function getKet1() {
        return $this->data["KET1"];
    }

    public function getKet2() {
        return $this->data["KET2"];
    }

    public function getKet3() {
        return $this->data["KET3"];
    }

}

?>