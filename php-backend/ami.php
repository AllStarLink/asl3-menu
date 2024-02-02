<?php
/*
 * ami.php
 * Copyright (c) 2024 AllStarLink, Inc
 * Author WD6AWP, WA3WCO
*/

//
// TODO TODO TODO
//
//   Turn this file into a PHP "Class"
//     https://www.php.net/manual/en/language.oop5.php
//
//   Add "nextActionID" (random)
//   Add "nextAction"   (sequential)
//

//
// Doc :
//   https://asterisk.phreaknet.org/#manageraction-UpdateConfig
//   https://docs.asterisk.org/Configuration/Interfaces/Asterisk-Manager-Interface-AMI
//

//error_reporting(0);

// Q? can we write our "-DEBUG" files?
$live_dangerously = null;	// true/false or null if we haven't checked

// DEBUG
$ami_debug = 0;
function AMI_debug($level = 1)	{ global $ami_debug; return ($ami_debug >= $level);   }
function AMI_set_debug($debug)	{ global $ami_debug; $ami_debug = $debug; }

// Reads output lines from Asterisk Manager
function AMIresponse($fp, $actionID) {
    while (TRUE) {
	$str = fgets($fp);

	# Looking for our ActionID
	if ("ActionID: $actionID" == trim($str)) {
	    $response = "";
	    while (TRUE) {
		$str = fgets($fp);
		if ($str != "\r\n") {
		    $response .= $str;
		} else {
		    return ($response);
		}
	    }
	}
    }
}

// connect to the AMI host
function AMIconnect($host) {
    // Set default port if not provided
    $ip_port = explode(":", $host);
    $ip = $ip_port[0];
    if (isset($ip_port[1])) {
	$port = $ip_port[1];
    } else {
	$port = 5038;
    }
    #print "connect: ip=$ip port=$port\n";

    // Open a manager socket.
    $fp = fsockopen($ip, $port, $errno, $errstr, 5);	// use @fsockopen?
    if ($fp === false) {
	throw new Exception("Could not connect to Asterisk Manager");
    }

    return ($fp);
}

// login to AMI
function AMIlogin($fp, $user, $password) {
    $actionID = 'AMI-PHP-' . mt_rand();

    // build the command
    $amiString	= "Action: Login\r\n"
		. "ActionID: $actionID\r\n"
		. "Events: 0\r\n"			// ??
		. "Username: $user\r\n"
		. "Secret: $password\r\n"
		. "\r\n";
    if (AMI_debug(2)) print "write  :\n>>>\n$amiString<<<\n";

    // send the command
    $written = fwrite($fp, $amiString);			// use @fwrite?
    if ($written === false) {
	throw new Exception("Error: AMI (login) write failed");
    }

    // get the response
    $response = AMIresponse($fp, $actionID);
    if (AMI_debug(2)) { print "response :\n>>>>\n"; print_r($response); print "<<<<\n"; }

    if (preg_match("/Message: Authentication accepted/", $response) != 1) {
	throw new Exception("Error: could not login to Asterisk Manager");
    }

    return TRUE;
}

// check AMI to see if we can write temp (DEBUG) config files
function AMIDebugWriteOK($fp, $host, $file) {
    global $live_dangerously;

    $ip_port = explode(":", $host);
    $ip = $ip_port[0];
    if ($ip != "127.0.0.1") {
	// if not "localhost"
	return FALSE;
    }

    $path = "/etc/asterisk/" . $file . "-DEBUG";
    if (file_exists($path)) {
	// if the target "-DEBUG" file exists, we're good
	return TRUE;
    }

    if (is_null($live_dangerously)) {
	$live_dangerously = false;

	//
	// using AMI "GetConfig" to look for "live_dangerously = yes" in
	// the "asterisk.conf" file
	//
	$response = AMIRead($fp,
			    'GetConfig',
			    "asterisk.conf",
			    "Category: options\r\n");
	$lines = preg_split('/\r\n|\n|\r/', $response);
	$matches = preg_grep("/^Line-[0-9-]+: live_dangerously=.+/", $lines);
	if ($matches) {
	    // check the LAST match
	    $last = trim($matches[array_key_last($matches)]);
	    if (preg_match("/^Line-[0-9-]+: live_dangerously=yes$/", $last) === 1) {
		$live_dangerously = true;
	    }
	}
    }

    return $live_dangerously;
}

// send AMI "read" command
function AMIRead($fp, $action, $filename, $cmdString) {
    // Asterisk Manger Interface needs an "ActionID" so we can find our own response
    $actionID = 'AMI-PHP-' . mt_rand();

    // Build the AMI command string
    $amiString	= "ActionID: $actionID\r\n"
		. "Action: $action\r\n"
		. "Filename: $filename\r\n"
		. $cmdString
		. "\r\n";
    if (AMI_debug(1)) print "write  :\n>>>\n$amiString<<<\n";

    // send the command
    $written = fwrite($fp, $amiString);			// use @fwrite?
    if ($written === false) {
	throw new Exception("Error: AMI (command) write failed");
    }

    // get the response, but do nothing with it
    $response = AMIresponse($fp, $actionID);
    if (AMI_debug(2)) { print "response :\n>>>>\n"; print_r($response); print "<<<<\n"; }

    return $response;
}

// send AMI "update" command
function AMIUpdate($fp, $action, $reload, $srcFile, $dstFile, $cmdString) {
    // Asterisk Manger Interface needs an "ActionID" so we can find our own response
    $actionID = 'AMI-PHP-' . mt_rand();

    // Build the AMI command string
    $amiString	= "ActionID: $actionID\r\n"
		. "Action: $action\r\n"
		. "SrcFilename: $srcFile\r\n"
		. "DstFilename: $dstFile\r\n"
		. "Reload: $reload\r\n"
		. $cmdString
		. "\r\n";
    if (AMI_debug(1)) print "write  :\n>>>\n$amiString<<<\n";

    // send the command
    $written = fwrite($fp, $amiString);			// use @fwrite?
    if ($written === false) {
	throw new Exception("Error: AMI (command) write failed");
    }

    // get the response, but do nothing with it
    $response = AMIresponse($fp, $actionID);
    if (AMI_debug(2)) { print "response :\n>>>>\n"; print_r($response); print "<<<<\n"; }

    if (preg_match("/^Message: (.+)/", $response, $match, PREG_UNMATCHED_AS_NULL) == 1) {
#print "Found \"Message:\"\n"; print_r($match); print "\n";
	$message = trim($match[1]);
	switch ($message) {
	    case "File requires escalated priveledges" :
	    case "Update did not complete successfully" :
	    case "Save of config failed" :
		$message .= " ($dstFile)";
		break;
	}

	throw new Exception($message);
    }

    return $response;
}
