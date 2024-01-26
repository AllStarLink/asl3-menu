#!/usr/bin/php
<?php
/*
 * asl-configuration.php
 * Copyright (c) 2024 AllStarLink, Inc
 * Author WA3WCO
 */

//
// Doc :
//   https://www.php.net/manual/en/function.getopt.php
//

set_include_path("/usr/lib/asterisk/php-support");
include("asl-ami-commands.php");

$usage_prefix = "$argv[0] [ --host=<host> ]";
ASL_set_usage_prefix($usage_prefix);

function usage() {
    global $argv, $argc;
    global $usage_prefix;

    print "Usage: $argv[0] --help\n"
	. "       $argv[0] --help=<asl-command>\n"
	. "       $usage_prefix --command=<asl-command> [ args ]\n"
	. "\n"
	. "Valid ASL commands:\n";

    $commands = ASLCommandList();
    $line     = "";
    $lineLen  = 0;
    foreach ($commands as $command) {
	$cmdLen = strlen($command);
	if (($lineLen + 2 + $cmdLen) > 70) {
	    // if the line will be too long with this command
	    print "  $line\n";
	    $line    = "";
	    $lineLen = 0;
	}

	if ($lineLen > 0) {
	    $line    .= ", ";
	    $lineLen += 2;
	}

	// add this command to the line
	$line    .= $command;
	$lineLen += $cmdLen;
    }
    if ($lineLen > 0) {
	print "  $line\n";
    }

    return;
}

// Process command line and any provided command line arguments
function asl_getopt($cmdShortOpts, $cmdLongOpts) {
    // get ASL command line options
    list($aslShortOpts, $aslLongOpts) = ASLCommandOptions();

    // combine the command line options with the ASL command options
    $shortOpts = $cmdShortOpts . $aslShortOpts;
    $longOpts  = array_merge($cmdLongOpts,$aslLongOpts);

    // parse the command line
    $options = getopt($shortOpts, $longOpts);
    #print "\$options   = "; print_r($options);

    // merge in some "default" values
    $options += ASLCommandOptionDefaults();
    #print "\$options+  = "; print_r($options);

    return $options;
}

$shortOpts = "h";
$longOpts  = array(
		// Optional options
		'help::',	// --help [command]
	     );

$options = asl_getopt($shortOpts, $longOpts);

// set "debug"
ASL_set_debug(array_key_exists('debug',  $options));

// are we looking for "help" ?
if ($argc <= 1) {
    // if no args
    usage();
    exit(1);
} elseif (array_key_exists('h', $options) || array_key_exists('help', $options)) {
    // if "-h" or "--help"
    if (($command = array_key_exists('help', $options) ? $options['help'] : NULL) == NULL) {
	// if we are not looking for help with a specific command
	usage();
    } else {
	try {
	    $help = ASLCommandHelp($command);
	    print "$help\n";
	} catch(Exception $e) {
	    print $e->getMessage(); print "\n";
	}
    }

    exit(1);
}

// set AMI "reload"
ASL_set_reload(array_key_exists('reload', $options));

// execute the requested ASL configuration command
try {
    $ok = ASLCommandExecute($options);
} catch(Exception $e) {
    #print "xxxxxxxxxx\n"; print_r($e); print "xxxxxxxxxx\n";
    print $e->getMessage(); print "\n";
    exit(1);
}

exit(0);

?>
