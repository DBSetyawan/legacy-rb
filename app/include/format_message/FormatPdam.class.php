<?php

class FormatPdam extends FormatMandatory {
	var $data;

	function FormatPdam($format, $message){
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
	public function getCustomerID1(){                       
		return $this->data["CUSTOMERID1"];                  
	}                                                       
	public function getCustomerID2(){                       
		return $this->data["CUSTOMERID2"];                  
	}                                                       
	public function getCustomerID3(){		                
		return $this->data["CUSTOMERID3"];                  
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
	public function getCustomerDetailInformation(){         
		return $this->data["CUSTOMERDETAILINFORMATION"];    
	}                                                       
	public function getBillerAdminCharge(){                 
		return $this->data["BILLERADMINCHARGE"];            
	}                                                       
	public function getTotalBillAmount(){                   
		return $this->data["TOTALBILLAMOUNT"];              
	}                                                       
	public function getMonthPeriod1(){                      
		return $this->data["MONTHPERIOD1"];                 
	}                                                       
	public function getYearPeriod1(){                       
		return $this->data["YEARPERIOD1"];                  
	}                                                       
	public function getFirstMeterRead1(){                   
		return $this->data["FIRSTMETERREAD1"]; 	            
	}                                                       
	public function getLastMeterRead1(){                    
		return $this->data["LASTMETERREAD1"];               
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
    public function getFirstMeterRead2(){                   
    	return $this->data["FIRSTMETERREAD2"];              
    }                                                       
    public function getLastMeterRead2(){                    
    	return $this->data["LASTMETERREAD2"];               
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
    public function getFirstMeterRead3(){                   
    	return $this->data["FIRSTMETERREAD3"];              
    }                                                       
    public function getLastMeterRead3(){                    
    	return $this->data["LASTMETERREAD3"];               
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
    public function getMonthPeriod4(){                      
    	return $this->data["MONTHPERIOD4"];                 
    }                                                       
    public function getYearPeriod4(){                       
    	return $this->data["YEARPERIOD4"];                  
    }                                                       
    public function getFirstMeterRead4(){                   
    	return $this->data["FIRSTMETERREAD4"];              
    }                                                       
    public function getLastMeterRead4(){                    
    	return $this->data["LASTMETERREAD4"];               
    }                                                       
    public function getPenalty4(){                          
    	return $this->data["PENALTY4"];                     
    }                                                       
    public function getBillAmount4(){                       
    	return $this->data["BILLAMOUNT4"];                  
    }                                                       
    public function getMiscAmount4(){                       
    	return $this->data["MISCAMOUNT4"];                  
    }   	                                                
	public function getMonthPeriod5(){                      
		return $this->data["MONTHPERIOD5"];                 
	}                                                       
	public function getYearPeriod5(){          	            
		return $this->data["YEARPERIOD5"];                  
	}                                                       
	public function getFirstMeterRead5(){                 	
		return $this->data["FIRSTMETERREAD5"];              
    }                                                       
    public function getLastMeterRead5(){                    
    	return $this->data["LASTMETERREAD5"];               
    }                                                       
    public function getPenalty5(){                          
    	return $this->data["PENALTY5"];                     
    }                                                       
    public function getBillAmount5(){                       
    	return $this->data["BILLAMOUNT5"];                  
    }                                       	            
    public function getMiscAmount5(){                       
    	return $this->data["MISCAMOUNT5"];                  
    }                                                       
    public function getMonthPeriod6(){                      
    	return $this->data["MONTHPERIOD6"];                 
    }                                                       
    public function getYearPeriod6(){                       
    	return $this->data["YEARPERIOD6"];                  
    }                                                       
    public function getFirstMeterRead6(){                   
    	return $this->data["FIRSTMETERREAD6"];              
    }                                                       
    public function getLastMeterRead6(){                    
    	return $this->data["LASTMETERREAD6"];               
    }                                                       
    public function getPenalty6(){                          
    	return $this->data["PENALTY6"];                     
    }                                                       
    public function getBillAmount6(){                       
    	return $this->data["BILLAMOUNT6"];                  
    }                                                       
    public function getMiscAmount6(){                       
    	return $this->data["MISCAMOUNT6"];                  
    }                                                       
    public function getPDAMName(){                       
    	return $this->data["PDAMNAME"];                  
    }
}


?>