<?php
	
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept"); 

	$link = mysqli_connect("localhost","root","","scutops_madhu");

	//if there was an error in establishing the link
	if(mysqli_connect_error())
		echo "Connection failed";
?>