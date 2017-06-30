<?php
	
	include 'db-include.php';

	$id = file_get_contents('php://input');

	if(!isset($json_result))
		$json_result = new stdClass();
	if (!isset($json_result->payload))
		$json_result->payload = new stdClass();

	$query = $link->prepare("SELECT * FROM `customer` WHERE `id` = ?");
	$query->bind_param('s',$id);
	$query->execute();
	if($result =$query->get_result()){
		if($row = mysqli_fetch_array($result)){
			$json_result->result = true;
			$json_result->description = "Success";
			$json_result->payload->id = $row['id'];
			$json_result->payload->scutops_money = $row['scutops_money'];
			$json_result->payload->username = $row['username'];
			$json_result->payload->no_of_flags = $row['no_of_flags'];
		}
		else{
			$json_result->result = false;
			$json_result->description = "failure";
		}
	}

	$json_result = json_encode($json_result);
	echo $json_result;
?>