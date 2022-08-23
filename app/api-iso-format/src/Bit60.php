<?php
namespace ISO8583;

define("LENGTH_4", 4);
define("LENGTH_7", 7);
define("LENGTH_10", 10);
define("LENGTH_15", 15);
define("LENGTH_20", 20);
define("LENGTH_25", 25);
define("LENGTH_30", 30);
define("LENGTH_40", 40);
define("LENGTH_100", 100);

class Bit60
{
    protected $length_parse = [11, 0, 15, 9];
    protected $data;
    public $values;

    protected $rc;
    protected $OutletId;
    protected $bit60;
    protected $bit63;
    protected $RBTrxId1;
    protected $RBTrxId;
    protected $Desc;
    protected $Admin;
    protected $Nominal;
    protected $DatetimeResp;
    protected $productId;
    protected $URLStruk;

    protected $Catatan;
    protected $StandAwal;
    protected $StandAkhir;
    protected $ProdukName;
    protected $StatusProduk;
    protected $Komisi;
    protected $KodeProduk;

    protected $SUBSCRIBERSEGMENTATION;
    protected $POWERCONSUMINGCATEGORY;
    protected $SLALWBP1;
    protected $SLALWBP2;
    protected $SLALWBP3;
    protected $SLALWBP4;

    protected $SN;
    public function __construct($message = "")
	{
        $this->values = json_decode($message);
    }

    public function rajabillerBalance()
    {
        $data = '';
        $i = 0;
        foreach ($this->values as $key => $value) {
            switch ($key) {
                case 'UID':
                    $data .= str_pad($value, $this->length_parse[$i]," ", STR_PAD_RIGHT);
                    break;
                case 'PIN':
                    
                    break;
                case 'SALDO':
                    $data .= str_pad($value, $this->length_parse[$i],"0", STR_PAD_LEFT);
                    break;
                case 'STATUS':
                    
                    break;
                case 'KET':
                    $data .= str_pad($value, $this->length_parse[$i]," ", STR_PAD_RIGHT);
                    break;
                default:
                    break;
            }
            $i++;
        }

        return $data;
    }

    public function rajabillerInfoProduk()
    {
        $data = '';
        $i = 0;

        unset($this->values->PIN);
        foreach ($this->values as $key => $value) {
            switch ($key) {
                case 'KODE_PRODUK':
                    $this->setKodeProduk($value);
                    $data .= str_pad('INFO', LENGTH_10," ", STR_PAD_RIGHT);
                    $data .= str_pad($value, LENGTH_10," ", STR_PAD_RIGHT);
                    break;
                case 'UID':
                    $this->setOutletId($value);
                    break;
                case 'STATUS':
                    $this->setRC($value);
                    $data .= str_pad($value, LENGTH_4," ", STR_PAD_RIGHT);
                    break;
                case 'KET':
                    $this->setDesc($value);
                    $data .= str_pad($value, LENGTH_100," ", STR_PAD_RIGHT);
                    break;
                case 'HARGA':
                    $this->setNominal($value);
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'ADMIN':
                    $this->setAdmin($value);
                    $data .= str_pad($value, LENGTH_7,"0", STR_PAD_LEFT);
                    break;
                case 'KOMISI':
                    $this->setKomisi($value);
                    $data .= str_pad($value, LENGTH_7,"0", STR_PAD_LEFT);
                    break;
                case 'STATUS_PRODUK':
                    $this->setStatusProduk($value);
                    $data .= str_pad($value, LENGTH_7," ", STR_PAD_LEFT);
                    break;
                default:
                    break;
            }
            $i++;
        }
        
        $this->setBit60($data);
    }

    public function rajabillerPulsa()
    {
        $data = '';
        $data63 = '';
        $i = 0;
        unset($this->values->PIN);
        foreach ($this->values as $key => $value) {
            switch ($key) {
                case 'KODE_PRODUK':
                    $this->setKodeProduk($value);
                    // $data .= str_pad($value, LENGTH_10," ", STR_PAD_RIGHT);
                    $data .= str_pad('PG', LENGTH_10," ", STR_PAD_RIGHT);
                    $data63 .= str_pad($value, LENGTH_10," ", STR_PAD_RIGHT);
                    break;
                case 'WAKTU':
                    $this->setDatetimeResponse($value);
                    $data .= str_pad($value, LENGTH_15,"0", STR_PAD_LEFT);
                    break;
                case 'NO_HP':
                    $this->setProductId($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    $data63 .= str_pad($value, LENGTH_20," ", STR_PAD_LEFT);
                    break;
                case 'UID':
                    $this->setOutletId($value);
                    break;
                case 'SN':
                    $this->setSN($value);
                    $data63 .= str_pad($value, LENGTH_40," ", STR_PAD_LEFT);
                    break;
                case 'NOMINAL':
                    $this->setNominal($value);
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    $data63 .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'REF1':
                    $this->setRBTrxId1($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'REF2':
                    $this->setRBTrxId($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'STATUS':
                    $this->setRC($value);
                    $data .= str_pad($value, LENGTH_4," ", STR_PAD_RIGHT);
                    break;
                case 'KET':
                    $this->setDesc($value);
                    $data .= str_pad($value, LENGTH_100," ", STR_PAD_RIGHT);
                    break;
                case 'SALDO_TERPOTONG':
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'SISA_SALDO':
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                default:
                    break;
            }
            $i++;
        }
        
        $this->setBit63($data63);
        $this->setBit60($data);
    }

    public function rajabillerTransferDana()
    {
        $data = '';
        $data63 = '';
        $i = 0;
        unset($this->values->PIN);
        unset($this->values->REFF3);
        foreach ($this->values as $key => $value) {
            switch ($key) {
                case 'KODE_PRODUK':
                    $this->setKodeProduk($value);
                    $data .= str_pad('BLTR', LENGTH_10," ", STR_PAD_RIGHT);
                    $data63 .= str_pad($value, LENGTH_10," ", STR_PAD_RIGHT);
                    break;
                case 'WAKTU':
                    $this->setDatetimeResponse($value);
                    $data .= str_pad($value, LENGTH_15,"0", STR_PAD_LEFT);
                    break;
                case 'IDPEL1':
                    $this->setProductId($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    $data63 .= str_pad($value, LENGTH_20," ", STR_PAD_LEFT);
                    break;
                case 'NAMA_PELANGGAN':
                    $data .= str_pad($value, LENGTH_30," ", STR_PAD_RIGHT);
                    $data63 .= str_pad($value, LENGTH_30," ", STR_PAD_RIGHT);
                    break;
                case 'NAMA_BANK':
                    $data .= str_pad($value, LENGTH_30," ", STR_PAD_LEFT);
                    $data63 .= str_pad($value, LENGTH_30," ", STR_PAD_LEFT);
                    break;
                case 'KODE_BANK':
                    $data .= str_pad($value, LENGTH_7," ", STR_PAD_LEFT);
                    break;
                case 'NOMINAL':
                    $this->setNominal($value);
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    $data63 .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'ADMIN':
                    $this->setAdmin($value);
                    $data .= str_pad($value, LENGTH_7,"0", STR_PAD_LEFT);
                    break;
                case 'UID':
                    $this->setOutletId($value);
                    break;
                case 'REF1':
                    $this->setRBTrxId1($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'REF2':
                    $this->setRBTrxId($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'STATUS':
                    $this->setRC($value);
                    $data .= str_pad($value, LENGTH_4," ", STR_PAD_RIGHT);
                    break;
                case 'KET':
                    $this->setDesc($value);
                    $data .= str_pad($value, LENGTH_100," ", STR_PAD_RIGHT);
                    break;
                case 'SALDO_TERPOTONG':
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'SISA_SALDO':
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'URL_STRUK':
                    $this->setURLStruk($value);
                    $data .= $value;
                    break;
                default:
                    break;
            }
            $i++;
        }
        // die($data63);
        $this->setBit63($data63);
        $this->setBit60($data);
    }

    public function rajabillerInq()
    {
        $data = '';
        $i = 0;

        unset($this->values->IDPEL2);
        unset($this->values->IDPEL3);
        unset($this->values->REF3);
        unset($this->values->PIN);
        foreach ($this->values as $key => $value) {
            switch ($key) {
                case 'KODE_PRODUK':
                    $data .= str_pad($value, LENGTH_10," ", STR_PAD_RIGHT);
                    break;
                case 'WAKTU':
                    $this->setDatetimeResponse($value);
                    $data .= str_pad($value, LENGTH_15," ", STR_PAD_LEFT);
                    break;
                case 'IDPEL1':
                    $this->setProductId($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'NAMA_PELANGGAN':
                    $data .= str_pad($value, LENGTH_30," ", STR_PAD_RIGHT);
                    break;
                case 'PERIODE':
                    $data .= str_pad($value, LENGTH_40," ", STR_PAD_RIGHT);
                    break;
                case 'NOMINAL':
                    $this->setNominal($value);
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'ADMIN':
                    $this->setAdmin($value);
                    $data .= str_pad($value, LENGTH_7,"0", STR_PAD_LEFT);
                    break;
                case 'UID':
                    $this->setOutletId($value);
                    // $data .= str_pad($value, LENGTH_10," ", STR_PAD_RIGHT);
                    break;
                case 'REF1':
                    $this->setRBTrxId1($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'REF2':
                    $this->setRBTrxId($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'STATUS':
                    $this->setRC($value);
                    $data .= str_pad($value, LENGTH_4," ", STR_PAD_RIGHT);
                    break;
                case 'KET':
                    $this->setDesc($value);
                    $data .= str_pad($value, LENGTH_30," ", STR_PAD_RIGHT);
                    break;
                case 'SALDO_TERPOTONG':
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'SISA_SALDO':
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                default:
                    break;
            }
            $i++;
        }
        
        $this->setBit60($data);
    }

    public function rajabillerPayDetail(){
        $data = '';
        $data63 = '';
        $i = 0;
        
        unset($this->values->IDPEL2);
        unset($this->values->IDPEL3);
        unset($this->values->REF3);
        unset($this->values->PIN);
        foreach ($this->values as $key => $value) {
            switch ($key) {
                case 'KODE_PRODUK':
                    $data .= str_pad($value, LENGTH_10," ", STR_PAD_RIGHT);
                    break;
                case 'WAKTU':
                    $this->setDatetimeResponse($value);
                    $data .= str_pad($value, LENGTH_15,"0", STR_PAD_LEFT);
                    break;
                case 'IDPEL1':
                    $this->setProductId($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'NAMA_PELANGGAN':
                    $data .= str_pad($value, LENGTH_30," ", STR_PAD_RIGHT);
                    break;
                case 'PERIODE':
                    $data .= str_pad($value, LENGTH_40," ", STR_PAD_LEFT);
                    break;
                case 'NOMINAL':
                    $this->setNominal($value);
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'ADMIN':
                    $this->setAdmin($value);
                    $data .= str_pad($value, LENGTH_7,"0", STR_PAD_LEFT);
                    break;
                case 'UID':
                    $this->setOutletId($value);
                    // $data .= str_pad($value, LENGTH_10," ", STR_PAD_RIGHT);
                    break;
                case 'REF1':
                    $this->setRBTrxId1($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'REF2':
                    $this->setRBTrxId($value);
                    $data .= str_pad($value, LENGTH_20,"0", STR_PAD_LEFT);
                    break;
                case 'STATUS':
                    $this->setRC($value);
                    $data .= str_pad($value, LENGTH_4," ", STR_PAD_RIGHT);
                    break;
                case 'KET':
                    $this->setDesc($value);
                    $data .= str_pad($value, LENGTH_30," ", STR_PAD_RIGHT);
                    break;
                case 'SALDO_TERPOTONG':
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'SISA_SALDO':
                    $data .= str_pad($value, LENGTH_10,"0", STR_PAD_LEFT);
                    break;
                case 'URL_STRUK':
                    $this->setURLStruk($value);
                    $data .= $value;
                    break;
                case 'DETAIL':
                    $inc = 0;
                    foreach ($value as $k => $val) {
                        switch ($k) {
                            case 'CATATAN':
                                $this->setCatatan($val);
                                $data63 .= str_pad($val, LENGTH_40," ", STR_PAD_RIGHT);
                                break;
                            /* PLNPRA */
                            case 'TOKEN':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_25,"0", STR_PAD_LEFT);
                                break; 
                            
                            /* PDAM */
                            case 'STANDAWAL':
                                $this->setStandAwal($val);
                                $data63 .= str_pad($val, LENGTH_7,"0", STR_PAD_LEFT);
                                break;
                            case 'STANDAKHIR':
                                $this->setStandAkhir($val);
                                $data63 .= str_pad($val, LENGTH_7,"0", STR_PAD_LEFT);
                                break;
                            
                            /* PLNPASCA */
                            case 'SUBSCRIBERSEGMENTATION':
                                $this->setSUBSCRIBERSEGMENTATION($val);
                                $data63 .= str_pad($val, LENGTH_10," ", STR_PAD_RIGHT);
                                break;
                            case 'POWERCONSUMINGCATEGORY':
                                $this->setPOWERCONSUMINGCATEGORY($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break;
                            case 'SLALWBP1':
                                $this->setSLALWBP1($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break;
                            case 'SAHLWBP1':
                                $this->setSAHLWBP1($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break;
                            case 'SAHLWBP2':
                                $this->setSAHLWBP2($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break;
                            case 'SAHLWBP3':
                                $this->setSAHLWBP3($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break;
                            case 'SAHLWBP4':
                                $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break;                            
                            
                            /* PLNPRA */
                            case 'POWERPURCHASE':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break; 
                            case 'MINORUNITOFPOWERPURCHASE':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break; 
                            case 'PURCHASEDKWHUNIT':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break; 
                            case 'MINORUNITOFPURCHASEDKWHUNIT':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break; 

                            /* PLNNON */
                            case 'TRANSACTIONCODE':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break; 
                            case 'TRANSACTIONNAME':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break; 
                            case 'REGISTRATIONDATE':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_15,"0", STR_PAD_LEFT);
                                break;       
                                
                            /* MULTIFINANCE */
                            case 'TENOR':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break;  
                            case 'CARNUMBER':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_20,"0", STR_PAD_LEFT);
                                break;

                            /* PGN */
                            case 'USAGE':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_10,"0", STR_PAD_LEFT);
                                break;

                            /* BPJS KES */
                            case 'NOHP':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_15," ", STR_PAD_LEFT);
                                break;
                            case 'NOREFERENSI':
                                // $this->setSAHLWBP4($val);
                                $data63 .= str_pad($val, LENGTH_20," ", STR_PAD_LEFT);
                                break;

                            default:
                                break;
                        }
                        $inc++;
                    }
                    break;
                default:
                    break;
            }
            $i++;
        }

        // die($data63);
        $this->setBit63($data63);
        $this->setBit60($data);
    }    

    /* ---------------------------------------------- */
    // PULSA
    public function setSN($data)        
    {
        $this->SN = $data;
    }

    public function getSN()
    {
        return $this->SN;
    }

    // PLNPASC
    public function setSUBSCRIBERSEGMENTATION($data)        
    {
        $this->SUBSCRIBERSEGMENTATION = $data;
    }

    public function getSUBSCRIBERSEGMENTATION()
    {
        return $this->SUBSCRIBERSEGMENTATION;
    }

    public function setPOWERCONSUMINGCATEGORY($data)        
    {
        $this->POWERCONSUMINGCATEGORY = $data;
    }

    public function getPOWERCONSUMINGCATEGORY()
    {
        return $this->POWERCONSUMINGCATEGORY;
    }

    public function setSLALWBP1($data)        
    {
        $this->SLALWBP1 = $data;
    }

    public function getSLALWBP1()
    {
        return $this->SLALWBP1;
    }

    public function setSAHLWBP1($data)        
    {
        $this->SAHLWBP1 = $data;
    }

    public function getSAHLWBP1()
    {
        return $this->SAHLWBP1;
    }

    public function setSAHLWBP2($data)        
    {
        $this->SAHLWBP2 = $data;
    }

    public function getSLALWBP3()
    {
        return $this->SAHLWBP2;
    }

    public function setSAHLWBP3($data)        
    {
        $this->SAHLWBP3 = $data;
    }

    public function getSAHLWBP3()
    {
        return $this->SAHLWBP3;
    }

    public function setSAHLWBP4($data)        
    {
        $this->SAHLWBP4 = $data;
    }

    public function getSAHLWBP4()
    {
        return $this->SAHLWBP4;
    }

    // --------------------------------------
    public function setKodeProduk($data)        
    {
        $this->KodeProduk = $data;
    }

    public function getKodeProduk()
    {
        return $this->KodeProduk;
    }

    public function setKomisi($data)        
    {
        $this->Komisi = $data;
    }

    public function getKomisi()
    {
        return $this->Komisi;
    }

    public function setProdukName($data)        
    {
        $this->ProdukName = $data;
    }

    public function getProdukName()
    {
        return $this->ProdukName;
    }

    public function setStatusProduk($data)        
    {
        $this->StatusProduk = $data;
    }

    public function getStatusProduk()
    {
        return $this->StatusProduk;
    }

    public function setStandAkhir($data)        
    {
        $this->StandAkhir = $data;
    }

    public function getStandAkhir()
    {
        return $this->StandAkhir;
    }

    public function setStandAwal($data)        
    {
        $this->StandAwal = $data;
    }

    public function getStandAwal()
    {
        return $this->StandAwal;
    }

    public function setCatatan($data)        
    {
        $this->Catatan = $data;
    }

    public function getCatatan()
    {
        return $this->Catatan;
    }

    public function setURLStruk($data)        
    {
        $this->URLStruk = $data;
    }

    public function getURLStruk()
    {
        return $this->URLStruk;
    }

    public function setProductId($data)        
    {
        $this->productId = $data;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setDatetimeResponse($param)
    {
        $this->DatetimeResp = $param;
    }

    public function getDatetimeResponse()
    {
        return $this->DatetimeResp;
    }

    public function setNominal($param)
    {
        $this->Nominal = $param;
    }

    public function getNominal()
    {
        return $this->Nominal;
    }

    public function setAdmin($param)
    {
        $this->Admin = $param;
    }

    public function getAdmin()
    {
        return $this->Admin;
    }

    public function setOutletId($param)
    {
        $this->OutletId = $param;
    }

    public function getOutletId()
    {
        return $this->OutletId;
    }

    public function setDesc($param)
    {
        $this->Desc = $param;
    }

    public function getDesc()
    {
        return $this->Desc;
    }

    public function setRBTrxId1($param)
    {
        $this->RBTrxId1 = $param;
    }

    public function getRBTrxId1()
    {
        return $this->RBTrxId1;
    }

    public function setRBTrxId($param)
    {
        $this->RBTrxId = $param;
    }

    public function getRBTrxId()
    {
        return $this->RBTrxId;
    }

    public function setBit60($param)
    {
        $this->bit60 = $param;
    }

    public function getBit60()
    {
        return $this->bit60;
    }

    public function setBit63($param)
    {
        $this->bit63 = $param;
    }

    public function getBit63()
    {
        return $this->bit63;
    }

    public function setRC($param)
    {
        $this->rc = $param;
    }

    public function getRC()
    {
        return $this->rc;
    }
   
}
