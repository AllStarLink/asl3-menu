#! /usr/bin/php
<?php
/*
Usage is ami.php node reload file cmd
Where is node is used to load the AMI hostname:port id and password
reload is yes or no to reload the module
file is an alias to the actual config filename: rpt=rpt.conf, susb=simpleusb.conf, etc
cmd is an alias tor the actual command: add_statpost, add_node, etc

To test: edit the ini file then run this script `ami.php`.
*/

include('include.php');

/********** Validate input **********/
#var_dump($argv);
if (count($argv) != 5) {
    print "Usage: node reload file cmd\n"; exit(1);
};
if(filter_var($argv[1], FILTER_VALIDATE_INT)) {
    $localnode = $argv[1];
} else {
    print "Node number must be integer.\n"; exit(1);
}
// Yes or  No
if (filter_var($argv[2], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(yes|no)$/i')))) {
    $reload = $argv[2];
} else {
    print "Reload parameter must be yes or no.\n"; exit(1);
}

#$namePattern = '/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9]+)?$/';
$pattern = '/^[a-zA-Z0-9_-]+$/';
if (! filter_var($argv[3], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $pattern)))) {
    print "File has illegal characters.\n"; exit(1);
}

// Test for valid file in files array
if (array_key_exists($argv[3], $validFiles)) {
    $fileAlias = $argv[3];
} else {
    print "Opps, $argv[3] is not a valid config file.\n"; exit(1);
}

// Test for valid command in commands array
if (array_key_exists($argv[4], $validCommands)) {
    $cmdAlias = $argv[4];
} else {
    print "Opps, $argv[4] is not a valid command.\n"; exit(1);
}

/********** Read settings ini file **********/
if (!file_exists('settings.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('settings.ini', true);
if (! array_key_exists($localnode, $config)) {
    print "Opps, $localnode is not in settings.ini\n"; exit(1);
}
#print_r($config);

/********** Get validCommand and validFile from their alias ***********/
$validFile = $validFiles[$fileAlias];
$validCommand = $validCommands[$cmdAlias];

/* print "fileAlias: $fileAlias\n";
print "validFile: $validFile\n";
print "cmdAlias: $cmdAlias\n";
print "validCommand: $validCommand\n";
exit;
 */
/********** Open a socket and login to Asterisk Manager **********/
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

/********** Update command place holders **********/
$cmdString = str_replace('m-localnode', $localnode, $validCommand);
#print "$cmdString\n";

// Send to AMI
$srcFile = $validFile;
$dstFile = 'test.txt';
$rptStatus = AMIcommand($fp, $reload, $srcFile, $dstFile, $cmdString);

print_r($rptStatus);