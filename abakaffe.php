<?
/*********************************
**********CREATED BY**************
---------------------------------
*********MORTEN JANSRUD***********
*********************************/

$json = @file_get_contents('http://kaffe.abakus.no/api/status');
$debugging = false;

if($json === FALSE) {

	//error connecting to abakus.no
	echo 'Abaconnection timed out</br>';

}else{

	//for debugging
	if($debugging) echo $json . '</br>'; 
	
	//decode JSON result and create variables
	$coffee = json_decode($json, true); 
	$status = $coffee['coffee']['status'];
	$hours = $coffee['coffee']['time_since']['hours']; 
	$minutes = $coffee['coffee']['time_since']['minutes']; 

	//print status
	echo 'Abastatus: ' . $status . '</br>';
	
	switch($status){
		case true:
			echo 'Hours since last coffee: ' . $hours . '</br>';
			echo 'Minutes since last coffee: ' . $minutes . '</br>';	

			if($hours == 0 && $minutes == 1){
				sendYo();
			}
			break;
		default:
			echo 'Abakrise' . '</br>';
			break;
	}
}

 
function sendYo(){

	echo 'Sending YO' . '</br>';
	
	//send to YO
	$url = 'https://api.justyo.co/yoall/';
	$data = array('api_token' => '*************************************');

	// use key 'http' even if you send the request to https://...
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	var_dump($result);
	
	insertLog();
	echo '</br>';
	
}

function insertLog(){


	//configure - SQLI
	$con = new mysqli('localhost', 'founder', '********************', 'founder_yo');
	 
	//connect to database
	if($con->connect_errno > 0){
		die('Unable to connect to database [' . $con->connect_error . ']');
	}

	//insert into database
	$query = "insert into log (
									receiver, 
									sender,
									error
							) values (
									'abakaffe', 
									'ABASCRIPT',
									'0'
							) 
	"; 

	$result = mysqli_query($con, $query);
	if($result) {
		echo "</br>Query has Completed ok. Your Query: " . $query;	
	} else {
		echo "</br>A MySQL error has occurred. Your Query: " . $query . ", Error: " . mysqli_error($con);
	}

}

/*********************************
*********************************
---------------------------------
*********************************
*********************************/
?>
