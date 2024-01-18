<?php
//error_reporting(0);

// List of app_rpt allowed AMI commands and allowed .conf files.
// the M-ParamN are "templates" for str_replace
$validFiles = array(
	'rpt' => 'rpt.conf',
	'susb' => 'simpleusb.conf',
	'susb_tune' => 'simple_tune_usb_M-Category.conf',
	'subradio' => 'usbradio.conf',
	'usbradio_tune'=> 'usbdario_tune_usb_M-Category.conf',
	'ami' => "manager.conf",
	'test' => 'test.txt'
);
$validCommands = array (
	'rpt_node_create' =>  array(
		'count'  => 2,
		'prompt' => "NodeNumber IPaddress[:port]",
		'string' => "Action-000000: NewCat\r\nCat-000000: M-Param1\r\nOptions-000000: inherit='node-main'\r\n" .
		            "Action-000001: Append\r\nCat-000001: nodes\r\nVar-000001: M-Param1\r\nValue-000001: radio@M-Param2/M-Param1,NONE\r\n"
	),
	'rpt_node_delete' =>  array(
		'count'  => 1,
		'prompt' => "NodeNumber",
		'string' => "Action-000000: DelCat\r\nCat-000000: M-Param1\r\n" .
		            "Action-000001: Delete\r\nCat-000001: nodes\r\nVar-000001: M-Param1\r\n"
	),
	'rpt_node_rename' =>  array(
		'count'  => 3,
		'prompt' => "OldNodeNumber NewNodeNumber IPaddress[:port]",
		'string' => "Action-000000: RenameCat\r\nCat-000000: M-Param1\r\nValue-000000: M-Param2\r\n" .
		            "Action-000001: Delete\r\nCat-000001: nodes\r\nVar-000001: M-Param1\r\n" .
		            "Action-000002: Append\r\nCat-000002: nodes\r\nVar-000002: M-Param2\r\nValue-000002: radio@M-Param2/M-Param3,NONE\r\n"
	),
	'rpt_set_susb' => array(
		'count' => 1 ,
		'prompt' => "NodeNumber",
		'string' => "Action-000000: Append\r\nCat-000000: M-Param1\r\nVar-000000: rxchannel\r\nValue-000000: SimpleUSB/M-Param1\r\n"
	),
	'rpt_set_statpost' => array(
		'count' => 1,
		'prompt' => "NodeNumber",
		'string' => "Action-000000: Append\r\nCat-000000: M-Param1\r\nVar-000000: statpost_url\r\nValue-000000: http://stats.allstarlink.org/uhandler\r\n"
	),
	'ami_secret_change' => array(
		'count'  => 2,
		'prompt' => "User Secret",
		'string' => "Action-000000: Update\r\nCat-000000: M-Param1\r\nVar-000000: secret\r\nValue-000000: M-Param2\r\n"
	),

    // 'add_nodes' =>      "Action-000000: Append\r\nCat-000000: nodes\r\nVar-000000: M-Category\r\nValue-000000: radio@m-parameter/M-Category,NONE\r\n",
	// 'rm_susb'  =>       "Action-000000: Delete\r\nCat-000000: M-Category\r\nVar-000000: rxchannel\r\n",
	// 'rm_statpost'  =>   "Action-000000: Delete\r\nCat-000000: M-Category\r\nVar-000000: statpost_url\r\n",
);

function getValidFiles() {
	global $validFiles;
	return($validFiles);
}

function getValidCmdList() {
	global $validCommands;
	return($validCommands);
}

function getCmdList() {
	global $validCommands;
	return(array_keys($validCommands));
}

// Validate and be helpful
function validateCLI($argc, $argv) {
	global $validCommands;
	global $validFiles;
	$usage = "Usage: hostlookup reload file cmd [param [param] ...]";

	if ($argc <= 3) {
		return $usage;
	}
	// argv[1] host lookup
	$pattern = '/^[a-zA-Z0-9_-]+$/';
	if (! filter_var($argv[1], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $pattern)))) {
	     return("Host lookup invalid string.");
	}
	// arg[2] reload module
	if (strtolower($argv[2]) != "no" ) {
		return("Reload Module must be answered in the negative, for now.");
	}
	// argv[3] file alias
	if (! array_key_exists($argv[3], $validFiles)) {
		return ("Opps, $argv[3] is not a valid config file.");
	}
	// argv[4] cmd alias
	if (! array_key_exists($argv[4], $validCommands)) {
		return ("Opps, $argv[4] is not a valid command.");
	}

	// Check supplied parameter count matches the required parameter count.
	// Help with correct input.
	$requiredParameterCount = $validCommands[$argv[4]]['count'];
	$givenParameterCount = $argc - 5;
	$delta = $requiredParameterCount - $givenParameterCount;
	$prompt = $validCommands[$argv[4]]['prompt'];
	if ( $givenParameterCount != $requiredParameterCount) {
		if ( $requiredParameterCount == 1 ) {
			$pural = "parameter";
		} else {
			$pural = "parameters";
		}
		return ("The $argv[4] command needs $requiredParameterCount $pural: $prompt.");
	}
	#print "\$argc: $argc\n";
	#print "\$requiredParameterCount: $requiredParameterCount\n";

	return "Ok";

	// saving for maybe use later
	#$pattern = '/^(yes|no)$/i';
	#$pattern = '/^[a-zA-Z0-9_-]+$/';
	#$namePattern = '/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9]+)?$/';
	#$pattern = '/^[a-zA-Z0-9_-]+$/';
	#$pattern = '/^[a-zA-Z0-9\._:=]+$/';
}

// Reads output lines from Asterisk Manager
function AMIresponse($fp, $actionID) {
    while (TRUE) {
		$str = fgets($fp);
		# Looking for our actionID
		if ("ActionID: $actionID" == trim($str)) {
			$response = $str;
			while (TRUE) {
				$str = fgets($fp);
		        #if (strlen(trim($str)) != 0 ) {
                if ($str != "\r\n") {
		            $response .= $str;
		        } else {
		            return($response);
		        }
			}
		}
    }
}

function AMIconnect($host) {
    // Set default port if not provided
    $arr = explode(":", $host);
    $ip = $arr[0];
    if (isset($arr[1])) {
        $port = $arr[1];
    } else {
        $port = 5038;
    }

    // Open a manager socket.
    $fp = @fsockopen($ip, $port, $errno, $errstr, 5);
    #print "parms: $ip $port $errno $errstr";
    return ($fp);
}

function AMIlogin($fp, $user, $password) {
    // Login
	$actionID = $user . $password;
    fwrite($fp,"ACTION: LOGIN\r\nUSERNAME: $user\r\nSECRET: $password\r\nEVENTS: 0\r\nActionID: $actionID\r\n\r\n");
    $login = AMIresponse($fp, $actionID);
	if (preg_match("/Authentication accepted/", $login) == 1) {
		return(TRUE);
	} else {
		return(FALSE);
	}
}

// Format command and write it
function AMIcommand($fp, $reload, $srcFile, $dstFile, $cmdString) {
	// Asterisk Manger Interface needs an actionID so we can find our own response
	$actionRand = mt_rand();
	$actionID = 'aslmenu' . $actionRand;

	// Build the AMI command string
	$amiString = "Action: UpdateConfig\r\n";
	$amiString .= "reload: $reload\r\n";
	$amiString .= "srcfilename: $srcFile\r\n";
	$amiString .= "dstfilename: $dstFile\r\n";
	$amiString .= "PreserveEffectiveContext\r\n";
	$amiString .= "ActionID: $actionID\r\n";
	$amiString .= $cmdString;

	// Complete AMI string
	$amiString .= "\r\n";

	// Do it
	if ((@fwrite($fp,$amiString)) > 0 ) {
		// Get response, but do nothing with it
		$rptStatus = AMIresponse($fp, $actionID);
		return $rptStatus;
	}
	return(FALSE);
}
