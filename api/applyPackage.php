<?php

	//if there was an error in establishing the link
	function isValidate($json_result,$data){
		if(empty($data['bookingTime'])){
			$json_result->result = false;
			$json_result->description = "Please select booking time";
		}elseif(empty($data['bookingCount'])){
			$json_result->result = false;
			$json_result->description = "Please select the number of professionals";
		}/*elseif($data['bookingTime']<"07:00" || $data['bookingTime']>"17:00"){
			$json_result->result = false;
			$json_result->description = "Service time between 6am and 5pm";
		}*/
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


	$json_result = isValidate($json_result,$data);
	
	if($json_result ->result)
	{
		$DAYS = json_encode($data['packageDays']);
		$SERVICES = json_encode($data['services']);
		//echo $SERVICES;
		$DAYS = mysqli_real_escape_string($link, $DAYS);
		$SERVICES = mysqli_real_escape_string($link, $SERVICES);

		$CUST_ID = $data['cust_id'];
		$SERVICES = str_replace("\\\"", "", $SERVICES);
		$DAYS = str_replace("\\\"", "", $DAYS);
		$INSTRUCTIONS = mysqli_real_escape_string($link, $data['instructions']);
		$LOCATION = mysqli_real_escape_string($link, $data['bookingLocation']);
		$TIME = mysqli_real_escape_string($link, $data['bookingTime']);
		$NO_PROF = $data['bookingCount'];
		$PROMOCODE = mysqli_real_escape_string($link, $data['promocode']);
	
	
		$query = $link->prepare("INSERT INTO `package_booking` (`CUST_ID`, `SERVICES`,`INSTRUCTIONS`,`LOCATION`, `DAYS`, `TIME`, `NO_OF_PROF`, `PROMOCODE`) VALUES (?,?,?,?,?,?,?,?)");
		$query->bind_param("ssssssss",$CUST_ID, $SERVICES, $INSTRUCTIONS, $LOCATION, $DAYS, $TIME, $NO_PROF, $PROMOCODE);
		if($query->execute()) {
			// Update successfull 
			$package_id = $link->insert_id;
	    	$json_result->result = true;
	        $json_result->description = "package applied successfully";
	        $json_result->payload->package_id = $package_id;
		} 
		else {
			// Update unsuccessfull
		    $json_result->result = false;
	        $json_result->description = "package application unsuccessful";
	    }
	}

    $json_result = json_encode($json_result);
    echo $json_result;

?>