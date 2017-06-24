<?php
	
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept"); 

	$id = $_COOKIE['id'];

	if (!isset($json_result))
		$json_result = new stdClass();

	$link = mysqli_connect("localhost","root","","scutops_madhu");
	if(mysqli_connect_error())
		echo "Connection failed";

	$phone_number = file_get_contents('php://input');

	if(!preg_match("/^[0-9\-]{7,}$/",$phone_number)){
		$json_result->result = false;
		$json_result->description = "Please enter a valid phone number";
	}
	else{
	$query = $link->prepare ("UPDATE `customer` SET `phone_number` = ? WHERE `id` = ? LIMIT 1");
	$query->bind_param("ss",$phone_number, $id);
	if($query->execute())
		$json_result->result = true;
	else
		$json_result->result = false;
	}

	$json_result = json_encode($json_result);
	echo $json_result;
	/*
	$query = "SELECT * FROM `customer` WHERE `id` = '$id'";
	if($result = mysqli_query($link,$query)){
			$row = mysqli_fetch_array($result);
			print_r($row);
	}
	*/
?>
