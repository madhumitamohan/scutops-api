<?php
	 

	function passwordEncrypt($id,$password){
		return md5(md5($id) . $password);
	}

	function escapeStringFromLink($link,$data,$string){
		return mysqli_real_escape_string($link,$data[$string]);
	}

	function insertPassword($password,$email,$link){
		//$query = "UPDATE `customer` SET `password` = '$password' WHERE `email`='$email'";
		$query = $link->prepare("UPDATE  `customer` SET`password` = '$password' WHERE `email`=?");
		$query->bind_param('s',$email);
		$query->execute();
		$result =$query->get_result();
	}

	function isValidate($json_result,$data){
		if(empty($data['email'])){
			$json_result->result = false;
			$json_result->description = "Email cannot be left blank";
		}
		elseif(empty($data['password'])){
			$json_result->result = false;
			$json_result->description = "Password cannot be left blank";
		}elseif(empty($data['phone_number'])){
			$json_result->result = false;
			$json_result->description = "Phone Number cannot be left blank";
		}elseif(empty($data['address'])){
			$json_result->result = false;
			$json_result->description = "Address cannot be left blank";
		}elseif(!filter_var($data['email'],FILTER_VALIDATE_EMAIL)){		
		$json_result->result = false;
		$json_result->description = "Please enter a valid email id";
		}elseif(!preg_match("/^[0-9\-]{7,}$/",$data['phone_number'])){
		$json_result->result = false;
		$json_result->description = "Please enter a valid phone number";
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

	$username = escapeStringFromLink($link,$data,"username");
	$email = escapeStringFromLink($link,$data,"email");
	$password = escapeStringFromLink($link,$data,"password");
	$phone_number = escapeStringFromLink($link,$data,"phone_number");
	$address = escapeStringFromLink($link,$data,"address");

	//if($type == 1)
	$json_result = isValidate($json_result,$data);
/*	elseif($type == 3){
		if(!preg_match("/^[0-9\-]{7,}$/",$data['phone_number'])){
		$json_result->result = false;
		$json_result->description = "Please enter a valid phone number";
		}
	}*/


	if($json_result->result)
	{	
	$query = $link->prepare("SELECT * FROM `customer` WHERE `email` = ?");
	$query->bind_param('s',$email);
	$query->execute();
	if($result =$query->get_result()){
		if($row = mysqli_fetch_array($result)){
			$json_result->result = false;
			$json_result->description = "This email is already signed up";
		}
		else{
			$query = $link->prepare("INSERT INTO `customer` (`username`,`email`,`phone_number`,`address`) VALUES (?,?,?,?)");
			$query->bind_param('ssss',$username,$email,$phone_number,$address);
			if($query->execute()){
				//find id of the new data
				/*$query = "SELECT `id` FROM `customer` WHERE `email`='$email' LIMIT 1";//level 4 password encryption
				$result = mysqli_query($link,$query);
				$row = mysqli_fetch_object($result);*/
				$id = $link->insert_id;
				$password = passwordEncrypt($id,$password);
				insertPassword($password,$email,$link);


				$json_result->result = true;
				$json_result->description = "Signed up Successfully";	
				$json_result->payload->id = $id;
				$json_result->payload->scutops_money = 0;//assuming the scutops-money is zero when a user signs up
				$json_result->payload->username = $username;
				$json_result->payload->no_of_flags = 0;//assuming the no_of_flags is zero when a user signs up	
			}
		}
	}
}


	$json_result = json_encode($json_result);

	echo $json_result;
?>