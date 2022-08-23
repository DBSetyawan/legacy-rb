<?
class GlobalSetting {
    var $db;
	var $nama_company;
	var $alamat_company;
	var $pesan_tambahan;
	var $biaya_daftar_reguler;
	var $biaya_daftar_regional;
	var $biaya_daftar_mobile;
	var $bonus_rekrut;
	var $default_id_master;
	var $banks;
    
    function GlobalSetting($db){
		$this->db = $db;
		$this->setGlobalSetting();
    }
    
	function setGlobalSetting(){
		$pgdb = $this->db;
		$q = "SELECT * FROM global_setting LIMIT 1";
		$v = $pgdb->query($q);
		$this->nama_company = $v[0]->nama_company;
		$this->alamat_company = $v[0]->alamat_company;
		$this->pesan_tambahan = $v[0]->pesan_tambahan;
		$this->biaya_daftar_reguler = $v[0]->biaya_daftar_reguler;
		$this->biaya_daftar_regional = $v[0]->biaya_daftar_regional;
		$this->biaya_daftar_mobile = $v[0]->biaya_daftar_mobile;
		$this->bonus_rekrut = $v[0]->bonus_rekrut;
		$this->default_id_master = $v[0]->default_id_master;
		
		$q = "	SELECT bo.id_outlet, b.nama_bank, bo.no_rekening, bo.an_rekening 
				FROM bank_outlet bo 
				LEFT JOIN bank b ON bo.id_bank = b.id_bank 
				WHERE bo.id_outlet = '".$v[0]->default_id_master."'";
		$v = $pgdb->query($q);
		$bank_detail = array();
		foreach($v as $idx){
			$bank_detail[$idx->nama_bank] = array("no_rek"=>$idx->no_rekening,"an_rek"=>$idx->an_rekening);
		}
		$this->banks = $bank_detail;
	}
}

?> 