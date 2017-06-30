<?php

	include 'db-include.php';


	$data = json_decode(file_get_contents('php://input'), true);

	if(!isset($json_result))
		$json_result = new stdClass();
	$json_result->result = true;
	$json_result->description = "";

	$cust_id = mysqli_real_escape_string($link,$data['cust_id']);
	$newBalance = mysqli_real_escape_string($link,$data['newBalance']);
	$order_id = mysqli_real_escape_string($link,$data['order_id']);

	$query = $link->prepare("UPDATE `customer` SET `scutops_money` = ? WHERE `id` = ?");
	$query->bind_param("ss",$newBalance,$cust_id);
	if($query->execute()){
		$json_result->result = true;
		$json_result->description = "Payment successful";
	}
	else{
		$json_result->result = false;
		$json_result->description = "Failure";
	}	

	$status = 2;
	$updated_on = date("Y-m-d h:i");

	$query = $link->prepare("UPDATE `order_table` SET `LATEST_STATUS` = ? WHERE `ORDER_ID` = ?");
	$query->bind_param("ss",$status,$order_id);
	if($query->execute()){
		$json_result->result = true;
		$json_result->description = "Payment successful";
	}
	else{
		$json_result->result = false;
		$json_result->description = "Failure";
	}

	$query = $link->prepare("INSERT INTO `transactions` (`ORDER_ID`,`STATUS`,`UPDATED_ON`) VALUES(?,?,?)");
	$query->bind_param("sss",$order_id,$status,$updated_on);
	if($query->execute()){
		$json_result->result = true;
		$json_result->description = "Payment successful";
	}
	else{
		$json_result->result = false;
		$json_result->description = "Failure";
	}

	/*$query = $link->prepare("SELECT * FROM `customer` WHERE `id` = ?");
	$query->bind_param("s",$id);
	$query->execute();

	if($result = $query->get_result())
		if($row = mysqli_fetch_array($result)){
			print_r($row);
		}
	*/
	$json_result = json_encode($json_result);
	echo $json_result;

?>