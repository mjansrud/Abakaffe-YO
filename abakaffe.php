<?
/*********************************
**********CREATED BY**************
---------------------------------
*********MORTEN JANSRUD***********
*********************************/

// run script
main();
  
// define main function
function main(){

	// fetch API content
	$json = @file_get_contents('http://kaffe.abakus.no/api/status');
	$debug = FALSE;

	// FileLogging class initialization
	$log = new FileLogging();
	 
	// set path and name of log file (optional)
	$log->lfile('log.txt');
	$log->lwrite('Running script');

	if($json === FALSE) {

		// error connecting to abakus.no
		$log->lwrite('Abaconnection timed out'); 

	}else{

		// for debugging 
		if($debug) $log->lwrite($json); 
		
		// decode JSON result and create variables
		$coffee = json_decode($json, true); 
		$status = $coffee['coffee']['status'];
		$hours = $coffee['coffee']['time_since']['hours']; 
		$minutes = $coffee['coffee']['time_since']['minutes']; 

		// log status
		$status_string = ($status) ? 'true' : 'false';
		$log->lwrite('Abastatus: ' . $status_string);
		
		switch($status){
			case true:
				// kaffetrakteren er på
				$log->lwrite('Hours since last coffee: ' . $hours);
				$log->lwrite('Minutes since last coffee: ' . $minutes);
				if($hours == 0 && $minutes == 0){
					sendYo($debug, $log , TRUE);
				}
				break; 
			case false:
				// kaffetrakteren er av
				$log->lwrite('Hours since last coffee: ' . $hours);
				$log->lwrite('Minutes since last coffee: ' . $minutes);
				break;
			default:
				$log->lwrite('Abakrise, connection with abatrakter failed');
				break;
		}
	}
	
	// if debug, send to specific user
	if($debug) sendYo($debug, $log, FALSE);
	
	$log->lwrite('-------------------------------------------------------------');
	$log->lclose();
}

function sendYo($debug, $log, $new_coffee){

	// define token
	$api_token = '***************************************';
	
	// send to YO
	if($debug && !$new_coffee){
		$username = '********';
		$log->lwrite('Sending YO to ' . $username);
		$url = 'https://api.justyo.co/yo/';
		$data = array('api_token' => $api_token, 'username' => $username);
	}else{
		$log->lwrite('Sending YO to all');
		$url = 'https://api.justyo.co/yoall/';
		$data = array('api_token' => $api_token);
		insertDatabaseLog($log);
	}

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

	// log YO result
	if($debug) $log->lwrite('Result: ' . $result);
		
}

function insertDatabaseLog($log){

	// log
	$log->lwrite('Inserting into database');
	
	// configure - SQLI
	$con = new mysqli('localhost', 'founder', '****************', 'founder_yo');
	 
	// connect to database
	if($con->connect_errno > 0){
		die('Unable to connect to database [' . $con->connect_error . ']');
	}

	// insert into database
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
		$log->lwrite('Query has Completed ok.');
	} else {
		$log->lwrite('A MySQL error has occurred. Your Query: ' . $query . ", Error: " . mysqli_error($con));
	}

}

/*********************************
*********************************
---------------------------------
*********************************
*********************************/

/**
 * Logging class:
 * - contains lfile, lwrite and lclose public methods
 * - lfile sets path and name of log file
 * - lwrite writes message to the log file (and implicitly opens log file)
 * - lclose closes log file
 * - first call of lwrite method will open log file implicitly
 * - message is written with the following format: [d/M/Y:H:i:s] (script name) message
 */
class FileLogging {
    // declare log file and file pointer as private properties
    private $log_file, $fp;
    // set log file (path and name)
    public function lfile($path) {
        $this->log_file = $path;
    }
    // write message to the log file
    public function lwrite($message) {
        // if file pointer doesn't exist, then open log file
        if (!is_resource($this->fp)) {
            $this->lopen();
        }
        // define script name
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time and suppress E_WARNING if using the system TZ settings
        // (don't forget to set the INI setting date.timezone)
        $time = @date('[d/M/Y:H:i:s]');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time ($script_name) $message" . PHP_EOL);
    }
    // close log file (it's always a good idea to close a file when you're done with it)
    public function lclose() {
        fclose($this->fp);
    }
    // open log file (private method)
    private function lopen() {
        // in case of Windows set default log file
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $log_file_default = 'c:/php/logfile.txt';
        }
        // set default log file for Linux and other systems
        else {
            $log_file_default = '/tmp/logfile.txt';
        }
        // define log file from lfile method or use previously set default
        $lfile = $this->log_file ? $this->log_file : $log_file_default;
        // open log file for writing only and place file pointer at the end of the file
        // (if the file does not exist, try to create it)
        $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
    }
}

/*********************************
*********************************
---------------------------------
*********************************
*********************************/
?>
