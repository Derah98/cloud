<?php
/*
* API.php v1.0.0 by Anyikwa Chisom Vitus
* Copyright 2017 Somus-Tech PLC.
* http://www.Somus-Tech.org/licenses/LICENSE-1.0
*/





//Class DataAccess Use in Communicating with Data-Base........
class DataAccess{
var $connection; 
function __construct($conn){
$this->connection = $conn;
}
function getter($arrayOfdataNeeded,$table,$condition){
    $field = implode($arrayOfdataNeeded,',');
    if (empty ($condition)){
        $sql = "SELECT $field FROM $table";
        $result = $this->connection->query($sql);
			if($result->num_rows > 0){
				$s = 1;
				return $s;
			}else{
				$s=0;
				return $s;
			}
    } else {
        $sql = "SELECT $field FROM $table WHERE $condition";
        $result = $this->connection->query($sql);
				if($result->num_rows > 0){
				$s = 1;
				return $s;
			}else{
				$s=0;
				return $s;
			}
    }
}


function fetch_single($table,$condition){
		$sql = "SELECT * FROM $table WHERE $condition";
        $result = $this->connection->query($sql);
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			return $row;
		}
		else{
			$s = 0;
			return $s;
		}
}



function insertter($arrayOfdataNeeded,$table,$arrayOfValues){
    $field = implode(',',$arrayOfdataNeeded);
    $values = implode($arrayOfValues,"','");
    $val = "'".$values."'";
    $sql = "INSERT INTO $table($field) VALUES($val)";
    $result = $this->connection->query($sql);
    if ($result){
        $s = 1;
        return $s;
    } else {
        echo "Error: " . $sql . "<br>" .$this->connection->error;
       
    }
}

function deletter($table,$condition){
    $sql = "DELETE FROM $table WHERE $condition";
    $result = $this->connection->query($sql);
    if ($result){
		$s = 1;
		return $s;
    } else {
        echo $this->connection->error;
    }

}

function updatter($table,$condition,$condition2){
    $sql = "UPDATE $table SET $condition2 WHERE $condition";
    $result = $this->connection->query($sql);
    if ($result){
			$s = 1;
         	return $s;
    } else {
		echo $this->connection->error;
    }

}


}

// Class Validator Use in Validating Forms........
class Validator{
   var $connection ="";

   function  __construct($conn) {
       $this->connection= $conn;
   }
   // Use in checking wethere user filled the reqired field....
		function isEmpty($arraryOfValues){
			$error = 1;
			foreach($arraryOfValues as $key=>$value) {
				if(empty($value)){
					$error = 0;
				} 
			}
			return $error;
		}
		
	// Use in validating wethere user have register before or not
		function itExit($arrayOfdataNeeded,$table,$condition){
			$field = implode($arrayOfdataNeeded,',');
			if (empty ($condition)){
				$sql = "SELECT $field FROM $table";
				$result = $this->connection->query($sql);
				if($result->num_rows > 0){
					$error = 0;
					return $error;
				}else{
					$error = 1;	
					return $error;	
				}
			} else {
				$sql = "SELECT $field FROM $table WHERE $condition";
				$result = $this->connection->query($sql);
				if($result->num_rows > 0){
					$error = 0;
					return $error;
				}else{
					$error = 1;	
					return $error;	
				}
			}
		}

// Use in validating Email input .....
	function isItEmail($arraryOfValues){
		$error = 1;
		if(is_array($arraryOfValues)){
			foreach($arraryOfValues as $key=>$value) {
				if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				  $error = 0; 
				}
			}	
		}else{
			if (!filter_var($arraryOfValues, FILTER_VALIDATE_EMAIL)) {
			$error = 0; 
			}
		}
			return $error;
	}

// Use in validating URL .....
	function isItURL(){
	$error = 1;
	if(is_array($arraryOfValues)){
			foreach($arraryOfValues as $key=>$value) {
				if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$value)) {
					  $error = 0; 
					}
			}	
		}else{
			if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$arraryOfValues)) {
             $error = 0; 
            }
		}
			return $error;
	
	}

}



?>