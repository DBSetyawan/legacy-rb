<?php
require_once("Config.class.php");

class Database{
	private $connection;
	private $db_selected;
	
	public function Database(){
		$config = new Config();
		$this->connection = mysql_connect($config->getDbHost(), $config->getDbUser(), $config->getDbPass(), true);	
		//echo "con = ".$this->connection."<br>";
		$this->db_selected = mysql_select_db($config->getDbName());
		//echo "sel = ".$this->db_selected."<br>";
	}
	
	public function cekDatabaseConnection(){
		$ret = false;
		if($this->connection){
			if(!$this->db_selected){

			}else{
				$ret = true;
			}
		}else{

		}
		return $ret;
	}
	
	public function getConnection(){
		return $this->connection;
	}
	public function getDbSelected(){
		return $this->db_selected;
	}

	public function dbQuery($sql){
		$stat_db = $this->cekDatabaseConnection();
		$ret = "";
		$numrow = 0;
		$row = "";
		if($stat_db){
			//echo $sql;
			$ret = mysql_query($sql, $this->getConnection());			
			$numrow = mysql_num_rows($ret);
			$i = 0;
			while($r = mysql_fetch_array($ret)){
				$row[$i] = $r;
			}
			mysql_close($this->getConnection());
		}
		$ret = array("is_db_established" => $stat_db, "numrow"=>$numrow, "row"=>$row);
		return $ret;
	}
	
	public function dbNumRows($query){
		$ret = mysql_num_rows($query);
		return $ret;
	}
	
	public function dbFetchObject($query, $db){
		$ret = mysql_fetch_object($query);
		return $ret;
	}
	
	public function dbClose(){
		mysql_close($this->getConnection());
	}
}


?>