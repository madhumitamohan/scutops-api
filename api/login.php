<?php


	function passwordEncrypt($row,$password){
		return md5(md5($row['id']) . $password);
	}

	function isEmpty($json_result,$data){
		if(empty($data['email'])){
			$json_result->result = false;
			$json_result->description = "Email cannot be left blank";
		}
		else if(empty($data['password'])){
			$json_result->result = false;
			$json_result->description = "Password cannot be left blank";
		}
		return $json_result;
	}

	include 'db-include.php';
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	if (!isset($json_result))
		 $json_result = new stdClass();
	$json_result->result = true;
	$json_result->description = "";
	if (!isset($json_result->payload))
		$json_result->payload = new stdClass();
	
	//$type = $data["type"];
	$email = mysqli_real_escape_string($link,$data["email"]);
	$password = $data["password"];
	
	$json_result = isEmpty($json_result,$data);
	
	if($json_result->result){
	
	$query = $link->prepare("SELECT * FROM `customer` WHERE `email` = ?");
	$query->bind_param("s",$email);
	$query->execute();

	//$query = "SELECT * FROM `customer` WHERE `email` = '$email'";
	if($result = $query->get_result()){
		if($row = mysqli_fetch_array($result)){
			$password = passwordEncrypt($row,$password);
			if($password == $row["password"]){
				$json_result->result = true;
				$json_result->description = "Login successful";
				$json_result->payload->id = $row['id'];
				$json_result->payload->scutops_money = $row['scutops_money'];
				$json_result->payload->username = $row['username'];
				$json_result->payload->no_of_flags = $row['no_of_flags'];
			}
			else{
				$json_result->result = false;
				$json_result->description = "Wrong password";
			}	
		}
		else{
			$json_result->result = false;
			$json_result->description = "You have not signed up";
		}
	}
	}


	$json_result = json_encode($json_result);
	echo $json_result;

	
?>