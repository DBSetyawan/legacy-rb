<?php

class FormatDelima extends FormatMandatory {
	var $data;
//****	
	function FormatDelima($format, $message){
		$frm = explode("*",$format);
		$msg = explode("*",$message);
        for($i=0;$i<count($msg);$i++){
			$this->data[$frm[$i]] = $msg[$i];
        }
    }
	
	public function getIdentitasPenerima(){						
		return $this->data["IDENTITASPENERIMA"];               
	}                                                          
	public function getKodeTransfer(){                         
		return $this->data["KODETRANSFER"];                    
	}                                                          
	public function getAmount(){                               
		return $this->data["AMOUNT"];                          
	}                                                          
	public function getAdminFee(){                             
		return $this->data["ADMINFEE"];                        
	}                                                          
	public function getNoref(){		                           
		return $this->data["NOREF"];                           
	}                                                          
	public function getNamaPengirim(){                         
		return $this->data["NAMAPENGIRIM"];                    
	}                                                          
	public function getJenisKelaminPengirim(){                 
		return $this->data["JENISKELAMINPENGIRIM"];            
	}                                             	           
	public function getAlamatPengirim(){                       
		return $this->data["ALAMATPENGIRIM"];                  
	}                                                          
	public function getKotaPengirim(){                         
		return $this->data["KOTAPENGIRIM"];                    
	}                                                          
	public function getKodePosPengirim(){                      
		return $this->data["KODEPOSPENGIRIM"];                 
	}                                                          
	public function getNegaraPengirim(){                       
		return $this->data["NEGARAPENGIRIM"];                  
	}                                                          
	public function getTipeIdCardPengirim(){                   
		return $this->data["TIPEIDCARDPENGIRIM"];              
	}                                                          
	public function getNomorIdCardPengirim(){                  
		return $this->data["NOMORIDCARDPENGIRIM"];             
	}                                                          
	public function getTempatLahirPengirim(){                  
		return $this->data["TEMPATLAHIRPENGIRIM"];             
	}                                                          
	public function getTanggalLahirPengirim(){                 
		return $this->data["TANGGALLAHIRPENGIRIM"];            
	}                                                          
	public function getNomorTeleponPengirim(){                 
		return $this->data["NOMORTELEPONPENGIRIM"]; 	       
	}                
	                                          
	public function getNamaPenerima(){                         
		return $this->data["NAMAPENERIMA"];                    
	}                                                          
	public function getJenisKelaminPenerima(){          	   
		return $this->data["JENISKELAMINPENERIMA"];            
	}                                                          
	public function getAlamatPenerima(){                 	   
		return $this->data["ALAMATPENERIMA"];                  
    }                                                          
    public function getKotaPenerima(){                         
    	return $this->data["KOTAPENERIMA"];                    
    }                                                          
    public function getKodePosPenerima(){                      
    	return $this->data["KODEPOSPENERIMA"];                 
    }                                                          
    public function getNegaraPenerima(){                       
    	return $this->data["NEGARAPENERIMA"];                  
    }                                       	               
    public function getTipeIdCardPenerima(){                   
    	return $this->data["TIPEIDCARDPENERIMA"];              
    }                                                          
    public function getNomorIdCardPenerima(){                  
    	return $this->data["NOMORIDCARDPENERIMA"];             
    }                                                          
    public function getTempatLahirPenerima(){                  
    	return $this->data["TEMPATLAHIRPENERIMA"];             
    }                                                          
    public function getTanggalLahirPenerima(){                 
    	return $this->data["TANGGALLAHIRPENERIMA"];            
    }                                                          
    public function getNomorTeleponPenerima(){                 
    	return $this->data["NOMORTELEPONPENERIMA"];         
    }  
}


?>