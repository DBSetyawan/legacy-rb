<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class FormatPKB extends FormatMandatory {

    //put your code here

    var $data;
    
    function FormatPKB($format, $message) {
          $frm = explode("*", $format);
        $msg = explode("*", $message);
        for ($i = 0; $i < count($msg); $i++) {
            $this->data[$frm[$i]] = $msg[$i];
        }
    }
    
    public function getKND_ID() {
        return $this->data["KND_ID"];
    }
    public function getKND_NOPOL() {
        return $this->data["KND_NOPOL"];
    }
    public function getKND_DF_JENIS() {
        return $this->data["KND_DF_JENIS"];
    }
    public function getKND_NAMA() {
        return $this->data["KND_NAMA"];
    }
    public function getKND_DF_NOMOR() {
        return $this->data["KND_DF_NOMOR"];
    }
    public function getKND_DF_TANGGAL() {
        return $this->data["KND_DF_TANGGAL"];
    }
    public function getKND_DF_JAM() {
        return $this->data["KND_DF_JAM"];
    }
    public function getKND_DF_PROSES() {
        return $this->data["KND_DF_PROSES"];
    }
    public function getUSR_FULL_NAME() {
        return $this->data["USR_FULL_NAME"];
    }
    public function getKND_KOHIR() {
        return $this->data["KND_KOHIR"];
    }
    public function getKND_SKUM() {
        return $this->data["KND_SKUM"];
    }
    public function getKND_ALAMAT() {
        return $this->data["KND_ALAMAT"];
    }
    public function getKEL_DESC() {
        return $this->data["KEL_DESC"];
    }
    public function getKEC_DESC() {
        return $this->data["KEC_DESC"];
    }
    public function getKAB_DESC() {
        return $this->data["KAB_DESC"];
    }
    public function getTPE_DESC() {
        return $this->data["TPE_DESC"];
    }
    public function getKD_MERK() {
        return $this->data["KD_MERK"];
    }
    public function getMRK_DESC() {
        return $this->data["MRK_DESC"];
    }
    public function getJNS_DESC() {
        return $this->data["JNS_DESC"];
    }
    public function getKND_THN_BUAT() {
        return $this->data["KND_THN_BUAT"];
    }
    public function getKND_CYL() {
        return $this->data["KND_CYL"];
    }
    public function getKND_WARNA() {
        return $this->data["KND_WARNA"];
    }
    public function getKND_RANGKA() {
        return $this->data["KND_RANGKA"];
    }
    public function getKND_MESIN() {
        return $this->data["KND_MESIN"];
    }
    public function getKND_NO_BPKB() {
        return $this->data["KND_NO_BPKB"];
    }
    public function getKND_SD_NOTICE() {
        return $this->data["KND_SD_NOTICE"];
    }
    public function getKND_TGL_STNK() {
        return $this->data["KND_TGL_STNK"];
    }
    public function getKND_SD_STNK() {
        return $this->data["KND_SD_STNK"];
    }
    public function getKD_BBM() {
        return $this->data["KD_BBM"];
    }
    public function getBBM_DESC() {
        return $this->data["BBM_DESC"];
    }
    public function getWRN_DESC() {
        return $this->data["WRN_DESC"];
    }
    public function getKND_NOPOL_EKS() {
        return $this->data["KND_NOPOL_EKS"];
    }
    public function getKND_JBB_PENUMPANG() {
        return $this->data["KND_JBB_PENUMPANG"];
    }
    public function getKND_BERAT_KB() {
        return $this->data["KND_BERAT_KB"];
    }
    public function getKND_JML_SUMBU_AS() {
        return $this->data["KND_JML_SUMBU_AS"];
    }
    public function getKD_KAB() {
        return $this->data["KD_KAB"];
    }
    public function getKD_JENIS() {
        return $this->data["KD_JENIS"];
    }
    public function getKD_TIPE() {
        return $this->data["KD_TIPE"];
    }
    public function getBOBOT() {
        return $this->data["BOBOT"];
    }
    public function getNILAI_JUAL() {
        return $this->data["NILAI_JUAL"];
    }
    public function getDASAR_PKB() {
        return $this->data["DASAR_PKB"];
    }
    public function getKD_GOL() {
        return $this->data["KD_GOL"];
    }
    public function getGOL_DESC() {
        return $this->data["GOL_DESC"];
    }
    public function getTGLBERLAKU() {
        return $this->data["TGLBERLAKU"];
    }
    public function getPOKOK_NEW() {
        return $this->data["POKOK_NEW"];
    }
    public function getPOKOK_OLD() {
        return $this->data["POKOK_OLD"];
    }
    public function getDENDA_NEW() {
        return $this->data["DENDA_NEW"];
    }
    public function getDENDA_OLD() {
        return $this->data["DENDA_OLD"];
    }
    public function getKND_MILIK_KE() {
        return $this->data["KND_MILIK_KE"];
    }
    public function getKD_KEC() {
        return $this->data["KD_KEC"];
    }
    public function getKD_KEL() {
        return $this->data["KD_KEL"];
    }
    public function getKD_GUNA() {
        return $this->data["KD_GUNA"];
    }
    public function getKND_BLOKIR() {
        return $this->data["KND_BLOKIR"];
    }
    public function getKND_TGL_FAKTUR() {
        return $this->data["KND_TGL_FAKTUR"];
    }
    public function getKND_TGL_KUWITANSI() {
        return $this->data["KND_TGL_KUWITANSI"];
    }
    public function getKND_BLOKIR_TGL() {
        return $this->data["KND_BLOKIR_TGL"];
    }
    public function getKND_BLOKIR_DESC() {
        return $this->data["KND_BLOKIR_DESC"];
    }
    public function getDRV_DESC() {
        return $this->data["DRV_DESC"];
    }
    public function getBILL_QUANTITY() {
        return $this->data["BILL_QUANTITY"];
    }
    public function getREFF_NUM() {
        return $this->data["REFF_NUM"];
    }
    public function getROW_ID() {
        return $this->data["ROW_ID"];
    }
    public function getPTP_TANGGAL() {
        return $this->data["PTP_TANGGAL"];
    }
    public function getNOM_PKB() {
        return $this->data["NOM_PKB"];
    }
    public function getJASARAHARJA() {
        return $this->data["JASARAHARJA"];
    }
    public function getDENDA_NOM_PKB() {
        return $this->data["DENDA_NOM_PKB"];
    }
    public function getDENDA_JASARAHARJA() {
        return $this->data["DENDA_JASARAHARJA"];
    }
    public function getNOM_PKB_TG() {
        return $this->data["NOM_PKB_TG"];
    }
    public function getJASARAHARJA_TG() {
        return $this->data["JASARAHARJA_TG"];
    }
    public function getDENDA_NOM_PKB_TG () {
        return $this->data["DENDA_NOM_PKB_TG"];
    }
    public function getDENDA_JASARAHARJA_TG() {
        return $this->data["DENDA_JASARAHARJA_TG"];
    }
    
}

?>
