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

/********** Validate CLI input **********/
#var_dump($argc);
print_r($argv);
if ($argc != 6) {
    print "Usage: hostlookup reload file node fileAlias cmdAlias[=parameter]\n"; exit(1);
};

// 1 host lookup
$pattern = '/^[a-zA-Z0-9_-]+$/';
if (filter_var($argv[1], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $pattern)))) {
    #if(filter_var($argv[1], FILTER_VALIDATE_INT)) {
    $hostLookup = $argv[1];
} else {
    print "Host lookup invalid string.\n"; exit(1);
}

// 2 Reload - Test for Yes or  No
if (filter_var($argv[2], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^(yes|no)$/i')))) {
    $reload = $argv[2];
} else {
    print "Reload parameter must be yes or no.\n"; exit(1);
}

// 3 Node number - Test for integer
if(filter_var($argv[3], FILTER_VALIDATE_INT)) {
    $nodeNumber = $argv[3];
} else {
    "Node number must be integer.\n"; exit(1);
}

// 4 file alias - Test for valid characters
#$namePattern = '/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9]+)?$/';
$pattern = '/^[a-zA-Z0-9_-]+$/';
if (filter_var($argv[4], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $pattern)))) {
    $fileAlias = $argv[4];
} else {
    print "File allias has illegal characters.\n"; exit(1);
}

// 5 cmd alias - Test for valid chacters
$pattern = '/^[a-zA-Z0-9\._:=]+$/';
if (filter_var($argv[5], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => $pattern)))) {
    $cmdAlias = $argv[5];
} else {
    print "Command alias has illegal characters.\n"; exit(1);
}

/**********  Validate file alias input **********/
if (! array_key_exists($fileAlias, $validFiles)) {
    print "Opps, $fileAlias is not a valid config file.\n"; exit(1);
}

/**********  Validate command alias input **********/
$t = explode('=', $cmdAlias);
print_r($t);
if (count($t) == 2 ) {
    $cmdAlias = $t[0];
    $cmdParameter = $t[1];
}
if (! array_key_exists($cmdAlias, $validCommands)) {
    print "Opps, $cmdAlias is not a valid command.\n"; exit(1);
}

/********** Read settings ini file **********/
if (!file_exists('settings.ini')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('settings.ini', true);
if (! array_key_exists($hostLookup, $config)) {
    print "Opps, $hostLookup is not in settings.ini\n"; exit(1);
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
$fp = AMIconnect($config[$hostLookup]['host']);
if (FALSE === $fp) {
	die("Could not connect to Asterisk Manager.\n\n");
} else {
    print "Connected to $hostLookup\n";
}
if (FALSE === AMIlogin($fp, $config[$hostLookup]['user'], $config[$hostLookup]['passwd'])) {
	die("Could not login to Asterisk Manager.");
} else {
    print "Logged in to $hostLookup\n";
}

/********** Update command place holders **********/
$cmdString = str_replace('m-nodeNumber', $nodeNumber, $validCommand);
if (!empty($cmdParameter)) {
    $cmdString = str_replace('m-parameter', $cmdParameter, $cmdString);
}
print "$cmdString\n";

// Send to AMI
$srcFile = $validFile;
$dstFile = $validFile;
$srcFile = 'test.txt'; $dstFile = 'test.txt';
$amiStatus = AMIcommand($fp, $reload, $srcFile, $dstFile, $cmdString);

print_r($amiStatus);