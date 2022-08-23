<?php
class PgDBI {
    var $db;
    var $db_host;
    var $db_user;
    var $db_password;
    var $db_port;
    var $db_conn;
    var $errorMsg;
    var $isShowError = true;
    var $lastquery = "";
	
    function PgDBI($a_db, $a_db_host="", $a_db_user="", $a_db_password="", $a_db_port="") 
    {
            global $last_db_pgsql_conn, $last_db_pgsql_name;

            $this->db = $a_db;
            $this->db_host = ($a_db_host == "") ? $db_host[$this->db] : $a_db_host;
            $this->db_user = ($a_db_user == "") ? $db_user[$this->db] : $a_db_user;
            $this->db_password = ($a_db_password=="") ? $db_password[$this->db] : $a_db_password;
            $this->db_port = ($a_db_port == "") ? $db_port[$this->db] : $a_db_port;


            if ($last_db_pgsql_name != $this->db)
            {
            
                    $conn_string = " host=".$this->db_host.
                                   " dbname=".$this->db.
                                   " port=".$this->db_port.
                                   " user=".$this->db_user.
                                   " password=".$this->db_password;

                    $this->db_conn = pg_connect($conn_string);
                    $last_db_pgsql_name = $this->db;
                    $last_db_pgsql_conn = $this->db_conn;

            }
            else
            {
                    $this->db_conn = $last_db_pgsql_conn;
            }
        
            $status = pg_connection_status($this->db_conn);

            if ($status<>0)
            {
                    $this->setErrorMsg("Koneksi ke database gagal");                    
            }
    } 

    function setErrorMsg($errmsg = "")
    {
                $msg = "";
		if($errmsg == "")
                    $errmsg = pg_last_error();
                
		// Is error output turned on or not..
		if ( $this->isShowError )
		{
			// If there is an error then take note of it
			$msg = "<blockquote><font face=arial size=2 color=ff0000>";
			$msg .= "<b>PgDBI  : SQL/DB Error --</b> ";
			$msg .= "[<font color=000077>". $errmsg .";</font>]";
			$msg .= "</font></blockquote>";
                        
		}
		else
		{
                        $msg = "";
		}

                $this->errorMsg = $msg;
    }


    function query_with_no_result($sql){
        $this->lastquery = $sql;
        $temp = pg_query($this->db_conn, $sql);
        pg_free_result($temp);
    }
    
	
	function query_boolean($sql){
        $this->lastquery = $sql;
        $temp = pg_query($this->db_conn, $sql);
        pg_free_result($temp);
		if($temp){
			return TRUE;
		}else{
			return FALSE;
		}
    }

    
    function query($sql) {
        $this->lastquery = $sql;
        $temp = pg_query($this->db_conn, $sql);
        $n = pg_num_rows($temp);
        $out = array();
        $i = 0;
        if($n > 0) {
            while ($data = pg_fetch_object($temp)) {
                $out[$i] = $data;
                $i++;
            }             
        }

        pg_free_result($temp);
        
        return $out;
     
    }

    function get_column_info($sql){
        $this->lastquery = $sql;
        $temp = pg_query($this->db_conn, $sql);
        $n = pg_num_fields($temp);

        for ($i = 0; $i < $n; $i++) {
            $out["column_name"][$i] = pg_field_name($temp, $i);
            $out["column_type"][$i] = pg_field_type($temp, $i);
        }

        pg_free_result($temp);
        
        return $out;
    }

    function insert($table,$fields,$values) {
        $sql="INSERT INTO $table ";
        if (count($fields)==count($values)+1) 
            array_unshift($values,$this->next_index($fields[0],$table));

        $temp1=implode(",",$fields);
        $temp2=implode("','",$values);

        $sql.="($temp1) VALUES ('$temp2')";

        $this->lastquery = $sql;
        
        $this->query($sql);

        return $values[0];
    }

    function update($table,$fields,$values,$where) {
    
    if ($where!="") {
            $sql="UPDATE $table SET ";
            for ($i=0;$i<count($fields);$i++)
                    $sql.="".$fields[$i]."='". $values[$i] ."' ".(($i==count($fields)-1)?"":" , ");
            $sql.=" WHERE $where";

            $this->lastquery = $sql;
            
            $this->query($sql);
        }
    }

    function delete($table, $where) {
   
        if ($where!="") {
            $sql="DELETE FROM $table WHERE $where";

            $this->lastquery = $sql;

            $this->query($sql);
        }
    }

    function get_row($sql, $default="") {
        $this->lastquery = $sql;
        $temp = pg_query($this->db_conn, $sql." LIMIT 1");
        $n = pg_num_rows($temp);
        if ($n == 0)
            return $default;
        else {
            return pg_fetch_object($temp);            
        }

        pg_free_result($temp);
    }

    function next_index($index, $table) {
        $tab = $this->query("SELECT $index FROM $table ORDER BY $index DESC LIMIT 1");
        if (count($tab)==0)
            return 0;
        else
            return $tab[0][$index]+1;
    }

    function last_index ($seqName){
        $sql = "SELECT currval('". $seqName ."'::regclass) as newkode";
        $row = $this->get_row($sql);
        
        return $row->newkode;
    }
}

?> 