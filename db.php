<?php

/**
* INSERT, UPDATE, DELETE SQL Generator
*
* @package    db.php
* @author     Sly Flores
* @version    1.0
* @email	  sly@christian.com.ph
* @fb		  facebook.com/sly14flores
*
*/

class pdo_db {

	var $db;
	var $prepare;
	var $table;
	var $sql;
	var $rows;
	var $insertId;
	
	function __construct($table = "") {
		
		$server = "localhost";
		$db_name = "ubold";
		$dsn = "mysql:host=$server;dbname=$db_name;charset=utf8";
		$username = "root";
		$password = "root";

		$this->db = new PDO($dsn, $username, $password, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$this->table = $table;

	}

	function getData($sql) {

		$stmt = $this->db->query($sql);
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$this->rows = $stmt->rowCount();
		return $results;

	}	

	function query($sql) {
		
		return $this->db->query($sql);
		
	}	
	
	function auto_increment_one() {
		
		$this->sql = "ALTER TABLE " . $this->table . " AUTO_INCREMENT = 1";
		$this->db->query($this->sql);
		
	}
	
	function insertData($data) {		
		
		$this->auto_increment_one();
		
		$this->prepare = "INSERT INTO ".$this->table." (";
		$prepare = "VALUES (";		
		$insert = [];
		
		foreach ($data as $key => $value) {
			$this->prepare .= $key . ",";
			if ($value === 'CURRENT_TIMESTAMP') $prepare .= "$value,";
			else $prepare .= ":$key,";
			if ($value === 'CURRENT_TIMESTAMP') continue;
			$insert[":$key"] = $value;
		}
		
		$prepare = substr($prepare,0,strlen($prepare)-1);
		$prepare .= ")";
		
		$this->prepare = substr($this->prepare,0,strlen($this->prepare)-1);
		$this->prepare .= ") ";
		$this->prepare .= $prepare;
		
		$stmt = $this->db->prepare($this->prepare);
		$e = $stmt->execute($insert);
		$this->insertId = $this->db->lastInsertId();

		return $e;
		
	}
	
	function insertDataMulti($data) {
		
		$this->auto_increment_one();		
		
		$inserts = [];
		$this->prepare = "INSERT INTO ".$this->table." (";	
		$prepare = "VALUES (";		
		
		foreach ($data as $row) { // construct Prepared Statement
			foreach ($row as $key => $value) {
				$this->prepare .= $key . ",";
				if ($value == 'CURRENT_TIMESTAMP') $prepare .= "$value,";
				else $prepare .= ":$key,";
				if ($value == 'CURRENT_TIMESTAMP') continue;
			}
			break;
		}

		foreach ($data as $row) { // strip item with CURRENT_TIMESTAMP value
		
			$insert = [];		
			foreach ($row as $key => $value) {
				if ($value == 'CURRENT_TIMESTAMP') continue;
				$insert[$key] = $value;
			}
			$inserts[] = $insert;
			
		}

		$prepare = substr($prepare,0,strlen($prepare)-1);
		$prepare .= ")";
		
		$this->prepare = substr($this->prepare,0,strlen($this->prepare)-1);
		$this->prepare .= ") ";
		$this->prepare .= $prepare;
		
		$Qs = [];
		$this->db->beginTransaction();
		foreach ($inserts as $insert) {
			$stmt = $this->db->prepare($this->prepare);
			$Qs[] = $stmt->execute($insert);
		}	 
		$Qs[] = $this->db->commit();
		
		$q = false;
		foreach ($Qs as $value) {
			$q = $value;
			if (!$q) break;
		}
		
		return $q;

	}
	
	function updateData($data,$pk) {
		
		$insert = [];
		
		$this->prepare = "UPDATE ".$this->table;
		$prepare = " SET ";

		foreach ($data as $key => $value) {
			
			if ($key == $pk) {
				$pk_value = $value;
				continue;
			}
			
			if ($value === "CURRENT_TIMESTAMP") {
				$prepare .= $key."=CURRENT_TIMESTAMP,";
				continue;
			} else {
				$prepare .= $key."=?,";
			}
						
			$insert[] = $value;

		}
		
		$prepare = substr($prepare,0,strlen($prepare)-1);
		
		$this->prepare .= $prepare;
		$this->prepare .= " WHERE $pk=?";
		$insert[] = $pk_value;

		$stmt = $this->db->prepare($this->prepare);
		$q = $stmt->execute($insert);
		
		return $q;
		
	}
	
	function updateDataMulti($data,$pk) {
		
		$updates = [];		
						
		$this->prepare = "UPDATE ".$this->table;
		$prepare = " SET ";

		foreach ($data as $row) { // construct Prepared Statement
		
			foreach ($row as $key => $value) {
				
				if ($key == $pk) {
					continue;
				}
				
				if ($value === "CURRENT_TIMESTAMP") {
					$prepare .= $key."=CURRENT_TIMESTAMP,";
					continue;
				} else {
					$prepare .= $key."=?,";
				}
			}
			break;
		
		}
		
		$prepare = substr($prepare,0,strlen($prepare)-1);		
		$this->prepare .= $prepare;
		$this->prepare .= " WHERE $pk=?";

		foreach ($data as $row) {
			
			$update = [];
			foreach ($row as $key => $value) {
				
				if ($key == $pk) {
					$pk_value = $value;
					continue;
				}				
				$update[] = $value;	
			}		
			$update[] = $pk_value;
			$updates[] = $update;
			
		}
		
		$Qs = [];
		$this->db->beginTransaction();		 
		foreach ($updates as $update) {
			$stmt = $this->db->prepare($this->prepare);
			$Qs[] = $stmt->execute($update);
		}		 
		$Qs[] = $this->db->commit();
		
		$q = false;
		foreach ($Qs as $value) {
			$q = $value;
			if (!$q) break;
		}
		
		return $q;

	}
	
	function deleteData($data) {

		$qMarks = str_repeat('?,', count(explode(",",array_values($data)[0])) - 1) . '?';
		$prepare = "DELETE FROM ".$this->table." WHERE ".array_keys($data)[0]." IN ($qMarks)";
		$insert = explode(",",array_values($data)[0]);
		$stmt = $this->db->prepare($prepare);
		$q = $stmt->execute($insert);

		return $q;

	}

}

?>