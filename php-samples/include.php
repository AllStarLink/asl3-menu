<?php
//error_reporting(0);

// List of app_rpt allowed AMI commands and allowed .conf files.
// the m-somethings are "templates" for str_replace
$validFiles = array(
	'rpt' => 'rpt.conf',
	'susb' => 'simpleusb.conf',
	'susb_tune' => 'simple_tune_usb_m-nodeNumber.conf',
	'subradio' => 'usbradio.conf',
	'usbradio_tune'=> 'usbdario_tune_usb_m-nodeNumber.conf',
	'test' => 'test.txt'
);
$validCommands = array(
    'add_node' =>     "Action-000000: NewCat\r\nCat-000000: m-nodeNumber\r\nOptions-000000: inherit='node-main'\r\n",
    'add_susb' =>     "Action-000000: Append\r\nCat-000000: m-nodeNumber\r\nVar-000000: rxchannel\r\nValue-000000: SimpleUSB/usb_m-nodeNumber\r\n",
    'add_statpost' => "Action-000000: Append\r\nCat-000000: m-nodeNumber\r\nVar-000000: statpost_url\r\nValue-000000: http://stats.allstarlink.org/uhandler\r\n",
    'add_nodes' =>    "Action-000000: Append\r\nCat-000000: nodes\r\nVar-000000: m-nodeNumber\r\nValue-000000: radio@m-parameter/m-nodeNumber,NONE\r\n",
	'rm_susb'  =>     "Action-000000: Delete\r\nCat-000000: m-nodeNumber\r\nVar-000000: rxchannel\r\n",
	'rm_statpost'  => "Action-000000: Delete\r\nCat-000000: m-nodeNumber\r\nVar-000000: statpost_url\r\n",
);

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
print $amiString;
	// Do it
	if ((@fwrite($fp,$amiString)) > 0 ) {
		// Get response, but do nothing with it
		$rptStatus = AMIresponse($fp, $actionID);
		return $rptStatus;
	}
	return(FALSE);
}

