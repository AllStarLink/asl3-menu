<?php
/*
This example adds the statpost setting to the chosen node. The node number, node ip address, optional AMI port,
AMI login and password are set in the ini file. The node must exist, ie added with the NewCat action or
performed on a renamed node.

To test: edit the ini file then run this script with `php addStatpost.php`.
*/
include('include.php');

// Read configuration file
if (!file_exists('settings.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('settings.ini', true);
#print "<pre>"; print_r($config); print "</pre>";

// Open a socket to Asterisk Manager
$localnode = '2509';
$fp = AMIconnect($config[$localnode]['host']);
if (FALSE === $fp) {
	die("Could not connect to Asterisk Manager.\n\n");
} else {
    print "Connected to $localnode\n";
}
if (FALSE === AMIlogin($fp, $config[$localnode]['user'], $config[$localnode]['passwd'])) {
	die("Could not login to Asterisk Manager.");
} else {
    print "Logged in to $localnode\n";
}

// Asterisk Manger Interface needs an actionID so we can find our own response
$actionRand = mt_rand();
$actionID = 'aslmenu' . $actionRand;

// Build the AMI command string
$amiString = "Action: UpdateConfig\r\n";
$amiString .= "reload: no\r\n";
$amiString .= "srcfilename: rpt.conf\r\n";
$amiString .= "dstfilename: test.conf\r\n";
$amsString .= "PreserveEffectiveContext\r\n";
$amiString .= "ActionID: $actionID\r\n";

// Add a value to 2509
$amiString .= "Action-000000: Append\r\n";
$amiString .= "Cat-000000: 2509\r\n";
$amiString .= "Var-000000: statpost_url\r\n";
$amiString .= "Value-000000: http://stats.allstarlink.org/uhandler\r\n";

// Complete AMI string
$amiString .= "\r\n";

// Do it
if ((@fwrite($fp,$amiString)) > 0 ) {
    // Get response, but do nothing with it
    $rptStatus = get_response($fp, $actionID);
    print_r($rptStatus);
} else {
    die("Command failed!\n");
}