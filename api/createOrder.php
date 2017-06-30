<?php 


	include 'db-include.php';
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	if (!isset($json_result))
		 $json_result = new stdClass();
	$json_result->result = true;
	$json_result->description = "";
	if (!isset($json_result->payload))
		 $json_result->payload = new stdClass();

	if(empty($data['bookingLocation'])){
			$json_result->result = false;
			$json_result->description = "Please select a location";
		}
	else{
		$SERVICES = json_encode($data['services']);
		//echo $SERVICES;
		$SERVICES = mysqli_real_escape_string($link, $SERVICES);
		//echo $SERVICES;
		$CUST_ID = mysqli_real_escape_string($link, $data['cust_id']);
		$SERVICES = str_replace("\\\"", "", $SERVICES);
		$INSTRUCTIONS = mysqli_real_escape_string($link, $data['instructions']);
		$LOCATION = mysqli_real_escape_string($link, $data['bookingLocation']);
		$TIME = mysqli_real_escape_string($link, $data['bookingTime']);
		$DATE = mysqli_real_escape_string($link, $data['bookingDate']);
		$NO_PROF = mysqli_real_escape_string($link, $data['bookingCount']);
		$PROMOCODE = mysqli_real_escape_string($link, $data['promocode']);

		include "checkValidity.php";

		if($json_result->result)
		{
			$status = 1;
			$updated_on = date("Y-m-d H:i");
			$advance = $NO_PROF * 157;
			// Update DB for given Email ID
			$query = $link->prepare("INSERT INTO `order_table` (`CUST_ID`,`SERVICES`, `INSTRUCTIONS`, `LOCATION`, `DATE`, `TIME`, `NO_OF_PROF`, `PROMOCODE`,`LATEST_STATUS`,`ADVANCE_AMOUNT`) VALUES (?,?,?,?,?,?,?,?,?,?)");
			$query->bind_param("ssssssssss",$CUST_ID, $SERVICES, $INSTRUCTIONS, $LOCATION, $DATE, $TIME, $NO_PROF, $PROMOCODE,$status,$advance);
			if($query->execute()) {
				// Update successfull 
				$order_id = $link->insert_id;
		    	$json_result->result = true;
		        $json_result->description = "Order created successfully";
		        $json_result->payload->order_id = $order_id;
			} 
			else {
				// Update unsuccessfull
			    $json_result->result = false;
		        $json_result->description = "Order creation failed";
		    }
	
		    
	
		    $query = $link->prepare("INSERT INTO `transactions` (`ORDER_ID`,`STATUS`,`UPDATED_ON`) VALUES (?,?,?)");
			$query->bind_param("sss",$order_id,$status,$updated_on);
			if($query->execute()){
				$json_result->result = true;
		        $json_result->description = "Order and transaction created successfully";
			}else{
				$json_result->result = false;
		        $json_result->description .= "Transaction creation failed" ;
			}
			}
		}
	$json_result = json_encode($json_result);
	echo $json_result;

	
?>