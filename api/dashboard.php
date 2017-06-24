<?php
	
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept"); 

	$link = mysqli_connect("localhost","root","","scutops_madhu");

	if(mysqli_connect_error())
		echo "Connection failed";

	$id = $_COOKIE['id'];
	$query = $link->prepare("SELECT * FROM `customer` WHERE `id` = ?");
	$query->bind_param('s',$id);
	$query->execute();
	$result = $query->get_result();
	$row = mysqli_fetch_array($result);
	echo $row["username"];

?>