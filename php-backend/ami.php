#! /usr/bin/php
<?php
/*
ami.php
Copyright (c) 2024 AllStarLink, Inc
Author WD6AWP
*/

include('include.php');
#print "\$validCommands: "; print_r(getValidCmdList());
#print "\$validFiles: "; print_r(getValidFiles());
#print "\$cmdList: "; print_r(getCmdList());

/********** Validate CLI input **********/
$errorMessage = validateCLI($argc, $argv);
if ($errorMessage !== 'Ok') {
    print "$errorMessage\n"; exit(1);
}
// Those are keepers.
$hostLookup = $argv[1];
$reload = $argv[2];
$fileAlias = $argv[3];
$cmdAlias = $argv[4];

// Gather the cmdAlias and its paramaters
foreach($argv as $arg => $value) {
    if ($arg > 3) {
        $cliParams[] = $value;
    }
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
print "\$validFile: $validFile\n";
$cmdString = $validCommands[$cmdAlias]['string'];
#$params = $validCommands[$cmdAlias]['count'];
#print "\$params: $params\n";
#print "\$cmdString: $cmdString\n";

/********** Update command place holders **********/
foreach ( $cliParams as $i => $value ) {
    if ($i >= 1) {
        $search = "M-Param$i";
        $replace = $cliParams[$i];
        print "$search $replace\n";
        $cmdString = str_replace($search, $replace, $cmdString);
    }
}
#print "UPDATED $cmdString";

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

/********** Send to AMI **********/
$srcFile = $validFile;
$dstFile = $validFile;
$amiStatus = AMIcommand($fp, $reload, $srcFile, $dstFile, $cmdString);

print_r($amiStatus);
