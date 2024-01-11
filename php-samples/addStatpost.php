#! /usr/bin/php
<?php
/*
This example adds the statpost setting to the chosen node. The node number, node ip address, optional AMI port,
AMI login and password are set in the ini file. The node must exist, ie added with the NewCat action or
performed on a renamed node.

To test: edit the ini file then run this script with `php addStatpost.php`.
*/

include('include.php');

// Validate input
#var_dump($argv);
if (count($argv) != 6) {
    print "Usage: node reload srcfile dstfile cmd\n"; exit(1);
};
if(filter_var($argv[1], FILTER_VALIDATE_INT)) {
    $localnode = $argv[1];
} else {
    print "Node number must be integer.\n"; exit(1);
}
if (filter_var($argv[2], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(yes|no)$/i')))) {
    $reload = $argv[2];
} else {
    print "Reload parameter must be yes or no.\n"; exit(1);
}
$pattern = '/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9]+)?$/';
if (filter_var($argv[3], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $pattern)))) {
    $srcFile = $argv[3];
} else {
    print "Source must be a valid filename.\n"; exit(1);
}
if (filter_var($argv[4], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $pattern)))) {
    $dstFile = $argv[4];
} else {
    print "Destination must be a valid filename.\n"; exit(1);
}

// Lookup valid commands
if (array_key_exists($argv[5], $validCommands)) {
    $cmd = $validCommands[$argv[5]];
} else {
    print "Opps, $argv[5] is not a valid command.\n"; exit(1);
}

print str_replace('m-localnode', $localnode, $cmd);

/* This bit of code did validate the command string. But I decided to
   build a list of valid commands for security and ease of use.
   */
// $pattern = '~^[a-zA-Z0-9.=\-:\\\\_\/ ]+$~';
// if (filter_var($argv[5], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $pattern)))) {
//     $cmdString = $argv[5];
// } else {
//     print "Command string contains illegal characters.\n"; exit(1);
// }

/* This sample should b pass the above validations */
// ./addStatpost.php 1999 No rpt.conf test.conf 'Action-000000: Append\r\nCat-000000: 2509\r\nVar-000000: statpost_url\r\nValue-000000: http://stats.allstarlink.org/uhandler\r\n'

exit;


// Read configuration file
if (!file_exists('settings.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('settings.ini', true);
#print "<pre>"; print_r($config); print "</pre>";

// Open a socket to Asterisk Manager
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

AMIcommand($fp, $reload, $srcFile, $dstFile, $cmdString);

// // Asterisk Manger Interface needs an actionID so we can find our own response
// $actionRand = mt_rand();
// $actionID = 'aslmenu' . $actionRand;

// // Build the AMI command string
// $amiString = "Action: UpdateConfig\r\n";
// $amiString .= "reload: no\r\n";
// $amiString .= "srcfilename: rpt.conf\r\n";
// $amiString .= "dstfilename: test.conf\r\n";
// $amsString .= "PreserveEffectiveContext\r\n";
// $amiString .= "ActionID: $actionID\r\n";

// // Add a value to 2509


// // Complete AMI string
// $amiString .= "\r\n";

// // Do it
// if ((@fwrite($fp,$amiString)) > 0 ) {
//     // Get response, but do nothing with it
//     $rptStatus = get_response($fp, $actionID);
//     print_r($rptStatus);
// } else {
//     die("Command failed!\n");
// }