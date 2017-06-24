<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept"); 
	function deleteCookies(){
			setcookie("id","",time()-3600);
		}
	//echo "cookie released";
	/*$query = $link->prepare("SELECT * FROM `customer` WHERE `id` = ?");
	$query->bind_param('s',$_COOKIE['id']);
	$query->execute();
	$result =$query->get_result();
	$row = mysqli_fetch_array($result);
	if($row['type'] != 1){

	}*/

	deleteCookies();

?>