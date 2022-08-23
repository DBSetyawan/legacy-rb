<?php

class Config{
	private $db_host;
	private $db_user;
	private $db_pass;
	private $db_name;

	public function Config(){
		//set default response	
		$this->db_host = "localhost";
		$this->db_user = "root";
		$this->db_pass = "";
		$this->db_name = "db_pc";
	}
	
	public function getDbHost(){
		return $this->db_host;
	}
	public function getDbUser(){
		return $this->db_user;
	}
	public function getDbPass(){
		return $this->db_pass;
	}
	public function getDbName(){
		return $this->db_name;
	}
}


?>