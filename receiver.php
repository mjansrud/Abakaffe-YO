<?
//configure - SQLI
$con = new mysqli('**************', '*************', '*****************', '*****************');

if($con->connect_errno > 0){
	die('Unable to connect to database [' . $con->connect_error . ']');
}

//Get YO sender
$sender = mysqli_real_escape_string($con, $_GET['username']);

if(!empty($sender)){
	
    /* reset user, prevent double YO */
	mysqli_query($con, " update coffee set sent = '1' WHERE sender='$sender' ");
    		
	//insert into DB
	$query = "insert into coffee (
										sender
								) values (
										'$sender'
								) 
	"; 

	$result = mysqli_query($con, $query);

	//check if successfull
	if($result) {
		echo "Query has Completed ok. Your Query: " . $query;	
	} else {
		echo "A MySQL error has occurred. Your Query: " . $query . ", Error: " . mysqli_error($con);
	}

}else{
	echo "Error";	
}
?>
