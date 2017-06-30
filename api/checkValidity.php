<?php

	$today_date = date("Y-m-d");
	$today_time = date("H:i");
	$today_year = date("Y");
	$today_month = date("m");
	$today_hour = date("H");
	$tomorrow_date = date("Y-m-d",(strtotime("+1 day")));//tomorrow's date
	
	$booking_day = date("w",strtotime($DATE)); // 0 for sunday and 6 for saturday
	$booking_date = date("d",strtotime($DATE));
	$booking_year = date("Y",strtotime($DATE));
	$booking_month = date("m",strtotime($DATE));
	$booking_hour = date("H", strtotime($DATE));


	if($DATE < $today_date){
		$json_result->result = false;
		$json_result->description = "Booking starts from today";
	}
	elseif($booking_day == 0){//if(its a sunday)
		$friday_date = date("Y-m-d",(strtotime($DATE . "-2 days")));//friday date
		if($today_date > $friday_date){ //booking date is after friday
			$json_result->result = false;
			$json_result->description = "Booking for Sunday is taken till Friday only";
		}
	}

	if($json_result->result){
		$valid_time =date("H:i",strtotime($today_time . "+2 hours"));
		
		if($TIME < "07:00" || $TIME > "18:00"){
			$json_result->result = false;
			$json_result->description = "Booking is allowed only between 7am and 5pm";
		}elseif(($today_date == $DATE) && ($TIME < $valid_time)){
			$json_result->result = false;
			$json_result->description = "Booking is only allowed 2 hours from now";
		}elseif (($DATE == $tomorrow_date) && ($today_hour>=18) && ($booking_hour<9)) {
			$json_result->result = false;
			$json_result->description = "Booking for the next day before 9am is taken only before 6pm";
		}
	}
?>