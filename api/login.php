<?php


	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept"); 
	//echo "testttt";

	function checkCookie(){
		if(isset($_COOKIE['id'])) 
			return true;
		else
			return false;
	}

	function passwordEncrypt($row,$password){
		return md5(md5($row['id']) . $password);
	}

	function setCookies($id){
		setcookie("id",$id,time()+60*60*24);
		//setcookie("password",$password,time() + 60*60*24);
	}

	function getEmailFromLink($link,$data){
		return mysqli_real_escape_string($link,$data["email"]);
	}

	function getDecodedData(){
		return json_decode(file_get_contents('php://input'), true);
	}

	function isEmpty($data){
		if (!isset($json_result))
		 	$json_result = new stdClass();
		$json_result->result = true;
		$json_result->description = "";
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

	//link with the database
	$link = mysqli_connect("localhost","root","","scutops_madhu");

	//if there was an error in establishing the link
	if(mysqli_connect_error())
		echo "Connection failed";
	
	$data = getDecodedData();
	if (!isset($json_result))
		 $json_result = new stdClass();
	$json_result->result = true;
	$json_result->description = "";
	
	$type = $data["type"];
	$email = getEmailFromLink($link,$data);
	$password = $data["password"];

	//if there is a cookie
	/*if(checkCookie()){
		$json_result->result = true;
		$json_result->description = "Login successful";
	}
	else{*/
	if($type == 1){
		$json_result = isEmpty($data);
	}
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
				$id = $row['id'];
				//setCookies($id);
			}
			elseif($type != $row['type']){
				$json_result->result = false;
				$json_result->description = "You have not signed up";
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