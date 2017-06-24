<?php
	
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept"); 

	function passwordEncrypt($id,$password){
		return md5(md5($id) . $password);
	}

	function setCookies($id){
		setcookie("id",$id,time()+60*60*24);
		//setcookie("password",$password,time() + 60*60*24);
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

	function getDecodedData(){
		return json_decode(file_get_contents('php://input'), true);
	}

	function isValidate($data){
		if (!isset($json_result))
		 	$json_result = new stdClass();
		 $json_result->result = true;
		 $json_result->description = "";
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


	//Connecting to the database
	$link = mysqli_connect("localhost","root","","scutops_madhu");

	if(mysqli_connect_error())
		echo "Connection failed";

	
	$data = getDecodedData();
	
	if (!isset($json_result))
		 $json_result = new stdClass();
	$json_result->result = true;
	$json_result->description = "";

	$type = $data["type"];
	$username = escapeStringFromLink($link,$data,"username");
	$email = escapeStringFromLink($link,$data,"email");
	$password = escapeStringFromLink($link,$data,"password");
	$phone_number = escapeStringFromLink($link,$data,"phone_number");
	$address = escapeStringFromLink($link,$data,"address");
 	$landmark = escapeStringFromLink($link,$data,"landmark");

	if($type == 1)
		$json_result = isValidate($data);
	/*elseif($type == 3){
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
			$query = $link->prepare("INSERT INTO `customer` (`type`,`username`,`email`,`phone_number`,`address`,`landmark`) VALUES (?,?,?,?,?,?)");
			$query->bind_param('ssssss',$type,$username,$email,$phone_number,$address,$landmark);
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

				
				//setCookies($id);		
			}
		}
	}
}


	$json_result = json_encode($json_result);

	echo $json_result;
?>