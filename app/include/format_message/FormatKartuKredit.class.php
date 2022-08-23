<?php

class FormatKartuKredit extends FormatMandatory {
	var $data;
	
	function FormatKartuKredit($format, $message){
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
	public function getCustomerID(){                
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
	public function getProductCategory(){           
		return $this->data["PRODUCTCATEGORY"];      
	}                                               
	public function getBillAmount(){                
		return $this->data["BILLAMOUNT"];           
	}                                               
	public function getPenalty(){                   
		return $this->data["PENALTY"];              
	}                                               
	public function getStampDuty(){                 
		return $this->data["STAMPDUTY"];            
	}                                               
	public function getPPN(){                       
		return $this->data["PPN"];                  
	}                                               
	public function getAdminCharge(){               
		return $this->data["ADMINCHARGE"];          
	}                                               
	public function getBillerRefNumber(){           
		return $this->data["BILLERREFNUMBER"];      
	}                                               
	public function getPTName(){                    
		return $this->data["PTNAME"];               
	}                                               
	public function getLastPaidPeriode(){           
		return $this->data["LASTPAIDPERIODE"]; 	    
	}                                               
	public function getLastPaidDueDate(){           
		return $this->data["LASTPAIDDUEDATE"];      
	}                                               
	public function getBillerAdminFee(){          	
		return $this->data["BILLERADMINFEE"];       
	}                                               
	public function getMiscFee(){                 	
		return $this->data["MISCFEE"];              
    }                                               
    public function getMiscNumber(){                
    	return $this->data["MISCNUMBER"];           
    }                                               
    public function getMinimumPayAmount(){          
    	return $this->data["MINIMUMPAYAMOUNT"];     
    }                                               
    public function getMaximumPayAmount(){          
    	return $this->data["MAXIMUMPAYAMOUNT"];    
    }                                       
}


?>