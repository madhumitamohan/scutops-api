<?php
	include 'db-include.php';

	$data = json_decode(file_get_contents('php://input'),true);
	
	if (!isset($json_result))
		 $json_result = new stdClass();
	$json_result->result = true;
	$json_result->description = "";
	if (!isset($json_result->payload))
		$json_result->payload = new stdClass();

	if(empty($data['feedback'])){
			$json_result->result = false;
			$json_result->description = "Please enter your feedback";
		}

	if($json_result->result)
		{
			$CUST_ID = $data["cust_id"];
			$FEEDBACK = mysqli_real_escape_string($link,$data["feedback"]);
			$DATE = date("Y-m-d");
	
			if($json_result->result)
			{	
				$query = $link->prepare("INSERT INTO `feedbacks` (`CUST_ID`,`FEEDBACK`,`DATE`) VALUES (?,?,?)");
				$query->bind_param('sss',$CUST_ID,$FEEDBACK,$DATE);
				if($query->execute()){
					$json_result->result = true;
					$json_result->description = "Feedback added";		
				}else{
					$json_result->result = false;
					$json_result->description = "Feedback couldnt be placed";	
				}	
			}
		}
	

	$json_result = json_encode($json_result);

	echo $json_result;

?>