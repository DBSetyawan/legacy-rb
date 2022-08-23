<?php

class FormatTeleponPasc extends FormatMandatory {
	var $data;

	function FormatTeleponPasc($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }

    public function getSwitcherId(){														
        return $this->data["SWITCHERID"];												
    }                                                                               
    public function getBillerCode(){                                              
        return $this->data["BILLERCODE"];                                         
    }                                                                               
    public function getCustomerId(){                                                 
        return $this->data["CUSTOMERID"];                                            
    }                                                                               
    public function getBillQuantity(){                                                 
        return $this->data["BILLQUANTITY"];                                            
    }                                                                               
    public function getNoref1(){                                                
        return $this->data["NOREF1"];                                           
    }                                                                               
    public function getNoref2(){                                           
        return $this->data["NOREF2"];                                      
    }                                                                               
    public function getCustomerName(){                                             
        return $this->data["CUSTOMERNAME"];                                        
    }                                                                               
    public function getCustomerAddress(){                                           
        return $this->data["CUSTOMERADDRESS"];                                      
    }                                                                               
    public function getBillerAdminCharge(){                                             
        return $this->data["BILLERADMINCHARGE"];                                        
    }                                                                               
    public function getTotalBillAmount(){                                           
        return $this->data["TOTALBILLAMOUNT"];                                      
    }                                                                               
    public function getProviderName(){                                             
        return $this->data["PROVIDERNAME"];                                        
    }                                                                               
    public function getMonthPeriod1(){                                             
        return $this->data["MONTHPERIOD1"];                                        
    }                                                                               
    public function getYearPeriod1(){                                                      
        return $this->data["YEARPERIOD1"];                                                 
    }                                                                               
    public function getPenalty1(){                                                      
        return $this->data["PENALTY1"];                                                
    }                                                                              
    public function getBillAmount1(){                                                      
        return $this->data["BILLAMOUNT1"];                                                 
    }                                                                              
    public function getMiscAmount1(){                                                      
        return $this->data["MISCAMOUNT1"];                                                 
    }                                                                              
    public function getMonthPeriod2(){                                                      
        return $this->data["MONTHPERIOD2"];                                                 
    }                                                                              
    public function getYearPeriod2(){                                                      
        return $this->data["YEARPERIOD2"];                                                 
    }                                                                              
    public function getPenalty2(){                                                      
        return $this->data["PENALTY2"];                                                 
    }                                                                              
    public function getBillAmount2(){                                                      
        return $this->data["BILLAMOUNT2"];                                                 
    }                                                                              
    public function getMiscAmount2(){                                                      
        return $this->data["MISCAMOUNT2"];                                                 
    }                                                                              
    public function getMonthPeriod3(){                                                      
        return $this->data["MONTHPERIOD3"];                                                 
    }                                                                              
    public function getYearPeriod3(){                                                      
        return $this->data["YEARPERIOD3"];                                                 
    }                                                                              
    public function getPenalty3(){                                                      
        return $this->data["PENALTY3"];                                                 
    }                                                                              
    public function getBillAmount3(){                                                      
        return $this->data["BILLAMOUNT3"];                                                 
    }
    public function getMiscAmount3(){                                                      
        return $this->data["MISCAMOUNT3"];                                                 
    }                                                                              
}                                                                                   
?>