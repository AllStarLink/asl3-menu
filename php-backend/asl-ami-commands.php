<?php
/*
 * asl-ami-commands.php
 * Copyright (c) 2024 AllStarLink, Inc
 * Author(s) WD6AWP, WA3WCO
 */

set_include_path("/usr/lib/asterisk/php-support");
include("ami.php");

$live_dangerously = false;

//error_reporting(0);

//
// TODO TODO TODO
//
//   Turn this file into a PHP "Class"
//     https://www.php.net/manual/en/language.oop5.php
//
//   Fill out command array
//

// ASL Commands :
//
// Each command includes the "command" name (as the "key") and the following values :
//
//   * 'args'    : the expected arguments
//   * 'help'    : the usage/help text
//   * 'actions' : the AMI actions
//
// Each AMI action has the following values :
//
//   * 'action'  : the AMI action to be performed
//   * 'file'    : the file to be updated
//   * 'string'  : the AMI action string to be executed
//   * 'enable'  : the command line argument that controls whether to execute this action
//
// Note: a string substitution of the required arguments and their provided
//       values will be applied to both the "file" and the "string"
//
// Note: info on the AMI commands to be executed are described at :
//   https://asterisk.phreaknet.org/#manageraction-UpdateConfig
//

$aslCommands = array(
	'node_list' =>  array(
		'args'    => array(),
		'help'    => "",
		'actions' => array(
			0 => array(
				'action' => "ListCategories",
				'file'   => "rpt.conf",
				'string' => ""
			),
		),
	),

	'node_show' =>  array(
		'args'    => array("node"),
		'help'    => "--node=<node>",
		'actions' => array(
			0 => array(
				'action' => "GetConfig",
				'k-v'    => true,
				'file'   => "rpt.conf",
				'string' => "Category: M-node\r\n"
			),
		),
	),

	'node_create' =>  array(
		'args'    => array("newNode", "password",  "iaxIP", "iaxPort"),
		'help'    => "--newNode=<node> --password=<password> [--iaxIP=<ip>] [--iaxPort=<port>]",
		'actions' => array(
			0 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: NewCat\r\n"
							 . "Cat-000000: M-newNode\r\n"
							 . "Options-000000: inherit='node-main'\r\n"
					  . "Action-000001: Append\r\n"
							 . "Cat-000001: nodes\r\n"
							 . "Var-000001: M-newNode\r\n"
							 . "Value-000001: radio@M-iaxIP:M-iaxPort/M-newNode,NONE\r\n"
			),
			1 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt_http_registrations.conf",
				'string' => "Action-000000: Append\r\n"
							 . "Cat-000000: registrations\r\n"
							 . "Var-000000: register\r\n"
							 . "Value-000000:>M-newNode:M-password@register.allstarlink.org\r\n"
			),
			2 => array(
				'action' => "UpdateConfig",
				'file'   => "iax.conf",
				'string' => "Action-000000: Update\r\n"
							 . "Cat-000000: general\r\n"
							 . "Var-000000: bindport\r\n"
							 . "Value-000000:M-iaxPort\r\n"
			),
			3 => array(
				'action' => "UpdateConfig",
				'file'   => "extensions.conf",
				'string' => "Action-000000: Update\r\n"
							 . "Cat-000000: globals\r\n"
							 . "Var-000000: NODE\r\n"
							 . "Value-000000:M-newNode\r\n"
			),
		),
	),

	'node_create_full' =>  array(
		'args'    => array("newNode", "password", "rxChannel", "duplex", "callsign", "iaxIP", "iaxPort"),
		'help'    => "--newNode=<node> --password=<password> --rxChannel=<channel> --duplex=<duplex> --callsign=<callsign> [--iaxIP=<ip>] [--iaxPort=<port>]"
			   . "\n"
			   . "\nWhere:"
			   . "\n  channel = (SimpleUSB|Radio|Pseudo|Voter|PCIx4)"
			   . "\n  duplex  = (0|1|2|3|4)",
		'actions' => array(
			0 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: NewCat\r\n"
							 . "Cat-000000: M-newNode\r\n"
							 . "Options-000000: inherit='node-main'\r\n"
					  . "Action-000001: Append\r\n"
							 . "Cat-000001: nodes\r\n"
							 . "Var-000001: M-newNode\r\n"
							 . "Value-000001: radio@M-iaxIP:M-iaxPort/M-newNode,NONE\r\n"
					  . "Action-000002: Append\r\n"
							 . "Cat-000002: M-newNode\r\n"
							 . "Var-000002: rxchannel\r\n"
							 . "Value-000002: M-rxChannel\r\n"
					  . "Action-000003: Append\r\n"
							 . "Cat-000003: M-newNode\r\n"
							 . "Var-000003: duplex\r\n"
							 . "Value-000003: M-duplex\r\n"
					  . "Action-000004: Append\r\n"
							 . "Cat-000004: M-newNode\r\n"
							 . "Var-000004: idrecording\r\n"
							 . "Value-000004: |iM-callsign/R\r\n"
					  . "Action-000005: Append\r\n"
							 . "Cat-000005: M-newNode\r\n"
							 . "Var-000005: idtalkover\r\n"
							 . "Value-000005: |iM-callsign\r\n"
					  . "Action-000006: Append\r\n"
							 . "Cat-000006: M-newNode\r\n"
							 . "Var-000006: statpost_url\r\n"
							 . "Value-000006: http://stats.allstarlink.org/uhandler\r\n"
			),
			1 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt_http_registrations.conf",
				'string' => "Action-000000: Append\r\n"
							 . "Cat-000000: registrations\r\n"
							 . "Var-000000: register\r\n"
							 . "Value-000000:>M-newNode:M-password@register.allstarlink.org\r\n"
			),
			2 => array(
				'action' => "UpdateConfig",
				'file'   => "iax.conf",
				'string' => "Action-000000: Update\r\n"
							 . "Cat-000000: general\r\n"
							 . "Var-000000: bindport\r\n"
							 . "Value-000000:M-iaxPort\r\n"
			),
			3 => array(
				'action' => "UpdateConfig",
				'file'   => "extensions.conf",
				'string' => "Action-000000: Update\r\n"
							 . "Cat-000000: globals\r\n"
							 . "Var-000000: NODE\r\n"
							 . "Value-000000:M-newNode\r\n"
			),
		),
	),

	'node_delete' =>  array(
		'args'    => array("node"),
		'help'    => "--node=<node>",
		'x-args'  => array("password"),
		'actions' => array(
			0 => array(
				'action' => "GetConfig",	// "password"
				'file'   => "rpt_http_registrations.conf",
				'string' => "Category: registrations\r\n"
			),
			1 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: DelCat\r\n"
							 . "Cat-000000: M-node\r\n"
					  . "Action-000001: Delete\r\n"
							 . "Cat-000001: nodes\r\n"
							 . "Var-000001: M-node\r\n"
			),
			2 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt_http_registrations.conf",
				'string' => "Action-000000: Delete\r\n"
							 . "Cat-000000: registrations\r\n"
							 . "Var-000000: register\r\n"
							 . "Match-000000:M-node:X-password@register.allstarlink.org\r\n"
							 . "Value-000000:M-node:X-password@register.allstarlink.org\r\n"
			),
		),
	),

	'node_rename' =>  array(
		'args'    => array("node", "newNode"),
		'help'    => "--node=<node> --newNode=<node>",
		'x-args'  => array(
					// rpt.conf
					"iaxIP",
					"iaxPort",
					// rpt_http_registratons.conf
					"password",
				  ),
		'actions' => array(
			0 => array(
				'action' => "GetConfig",	// "iaxIP", "iaxPort"
				'file'   => "rpt.conf",
				'string' => "Category: nodes\r\n"
			),
			1 => array(
				'action' => "GetConfig",	// "password"
				'file'   => "rpt_http_registrations.conf",
				'string' => "Category: registrations\r\n"
			),
			2 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: RenameCat\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Value-000000: M-newNode\r\n"
					  . "Action-000001: Delete\r\n"
							 . "Cat-000001: nodes\r\n"
							 . "Var-000001: M-node\r\n"
					  . "Action-000002: Append\r\n"
							 . "Cat-000002: nodes\r\n"
							 . "Var-000002: M-newNode\r\n"
							 . "Value-000002: radio@X-iaxIP:X-iaxPort/M-newNode,NONE\r\n"
			),
			3 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt_http_registrations.conf",
				'string' => "Action-000000: Update\r\n"
							 . "Cat-000000: registrations\r\n"
							 . "Var-000000: register\r\n"
							 . "Match-000000:M-node:X-password@register.allstarlink.org\r\n"
							 . "Value-000000:>M-newNode:X-password@register.allstarlink.org\r\n"
			),
			4 => array(
				'action' => "UpdateConfig",
				'file'   => "extensions.conf",
				'string' => "Action-000000: Update\r\n"
							 . "Cat-000000: globals\r\n"
							 . "Var-000000: NODE\r\n"
							 . "Value-000000:M-newNode\r\n"
			),
			5 => array(
				'action' => "UpdateConfig",
				'file'   => "simpleusb.conf",
				'string' => "Action-000000: RenameCat\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Value-000000: M-newNode\r\n"
			),
			6 => array(
				'action' => "UpdateConfig",
				'file'   => "usbradio.conf",
				'string' => "Action-000000: RenameCat\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Value-000000: M-newNode\r\n"
			),
			7 => array(
				'action' => "UpdateConfig",
				'file'   => "voter.conf",
				'string' => "Action-000000: RenameCat\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Value-000000: M-newNode\r\n"
			),
		),
	),

	'node_set_callsign' => array(
		'args'    => array("node", "callsign"),
		'help'    => "--node=<node> --callsign=<callsign>",
		'actions' => array(
			0 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: Delete\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Var-000000: idrecording\r\n"
					  . "Action-000001: Append\r\n"
							 . "Cat-000001: M-node\r\n"
							 . "Var-000001: idrecording\r\n"
							 . "Value-000001: |iM-callsign/R\r\n"
					  . "Action-000002: Delete\r\n"
							 . "Cat-000002: M-node\r\n"
							 . "Var-000002: idtalkover\r\n"
					  . "Action-000003: Append\r\n"
							 . "Cat-000003: M-node\r\n"
							 . "Var-000003: idtalkover\r\n"
							 . "Value-000003: |iM-callsign\r\n"
			),
		),
	),

	'node_set_channel' => array(
		'args'    => array("node", "rxChannel"),
		'help'    => "--node=<node> --rxChannel=<channel>"
			   . "\n"
			   . "\nWhere:"
			   . "\n  channel = (SimpleUSB|Radio|Pseudo|Voter|PCIx4)",
		'actions' => array(
			0 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: Delete\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Var-000000: rxchannel\r\n"
					  . "Action-000001: Append\r\n"
							 . "Cat-000001: M-node\r\n"
							 . "Var-000001: rxchannel\r\n"
							 . "Value-000001: M-rxChannel\r\n"
			),
		),
	),

	'node_set_duplex' => array(
		'args'    => array("node", "duplex"),
		'help'    => "--node=<node> --duplex=<duplex>"
			. "\n"
			. "\nWhere:"
			. "\n  duplex = (0|1|2|3|4)",
		'actions' => array(
			0 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: Delete\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Var-000000: duplex\r\n"
					  . "Action-000001: Append\r\n"
							 . "Cat-000001: M-node\r\n"
							 . "Var-000001: duplex\r\n"
							 . "Value-000001: M-duplex\r\n"
			),
		),
	),

	'node_set_password' =>  array(
		'args'    => array("node", "password"),
		'help'    => "--node=<node> --password=<password>",
		'x-args'  => array("password"),
		'actions' => array(
			0 => array(
				'action' => "GetConfig",	// "password"
				'file'   => "rpt_http_registrations.conf",
				'string' => "Category: registrations\r\n"
			),
			1 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt_http_registrations.conf",
				'string' => "Action-000000: Delete\r\n"
							 . "Cat-000000: registrations\r\n"
							 . "Var-000000: register\r\n"
							 . "Match-000000:M-node:X-password@register.allstarlink.org\r\n"
							 . "Value-000000:M-node:X-password@register.allstarlink.org\r\n"
			),
			2 => array(
				'action' => "UpdateConfig",
				'file' => "rpt_http_registrations.conf",
				'string' => "Action-000000: Append\r\n"
							 . "Cat-000000: registrations\r\n"
							 . "Var-000000: register\r\n"
							 . "Value-000000:>M-node:M-password@register.allstarlink.org\r\n"
			),
		),
	),

	'node_set_statistics' => array(
		'args'    => array("node", "enable"),
		'help'    => "--node=<node> --enable=(yes|no)",
		'actions' => array(
			0 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: Delete\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Var-000000: statpost_url\r\n"
			),
			1 => array(
				'action' => "UpdateConfig",
				'file'   => "rpt.conf",
				'string' => "Action-000000: Append\r\n"
							 . "Cat-000000: M-node\r\n"
							 . "Var-000000: statpost_url\r\n"
							 . "Value-000000: http://stats.allstarlink.org/uhandler\r\n",
				'enable' => "enable",
			),
		),
	),

	'ami_show' =>  array(
		'args'    => array(),
		'help'    => "",
		'actions' => array(
			0 => array(
				'action' => "GetConfig",
				'file'   => "manager.conf",
				'string' => "Category: admin\r\n"
			),
		),
	),

	'ami_set_secret' => array(
		'args'    => array("user", "secret"),
		'help'    => "[--user=<user>] --secret=<secret>",
		'actions' => array(
			0 => array(
				'action' => "UpdateConfig",
				'file'   => "manager.conf",
				'string' => "Action-000000: Update\r\n"
							 . "Cat-000000: M-user\r\n"
							 . "Var-000000: secret\r\n"
							 . "Value-000000: M-secret\r\n"
			),
		),
	),

	'module_enable' => array(
		'args'    => array("module", "load"),
		'help'    => "--module=astModule --load=(yes|no)",
		'actions' => array(
			//
			// NOTE: here, I have broken out the actions thinking
			//       that we may need to accept that one of the
			//	 "Delete" requests may fail but we still want
			//	 to move forward with the "Append".
			//
			0 => array(
				'action' => "UpdateConfig",
				'file'   => "modules.conf",
				'string' => "Action-000000: Delete\r\n"
							 . "Cat-000000: modules\r\n"
							 . "Var-000000: load\r\n"
							 . "Match-000000:M-module.so\r\n"
							 . "Value-000000:M-module.so\r\n"
			),
			1 => array(
				'action' => "UpdateConfig",
				'file'   => "modules.conf",
				'string' => "Action-000000: Delete\r\n"
							 . "Cat-000000: modules\r\n"
							 . "Var-000000: noload\r\n"
							 . "Match-000000:M-module.so\r\n"
							 . "Value-000000:M-module.so\r\n"
			),
			2 => array(
				'action' => "UpdateConfig",
				'file'   => "modules.conf",
				'string' => "Action-000000: Append\r\n"
							 . "Cat-000000: modules\r\n"
							 . "Var-000000: M-load\r\n"
							 . "Value-000000:>M-module.so\r\n"
			),
		),
	),

);
#print "\$aslCommands: "; print_r($aslCommands);

function ASLCommandList() {
    global $aslCommands;

    return (array_keys($aslCommands));
}

// ASL Command Options :
//
// Each ASL command will be comprised by one more more of the following
// options.
//

$aslShortOptions = "";

$aslLongOptions  = array(
    // Required options
    "host:",		// --host=<hostname>
    "command:",		// --command=<string>
    // ASL command options (note: not all commands will use every option)
    "callsign:",	// --callsign=<string>
    "duplex:",		// --duplex=(0,1,2,3,4)
    "enable:",		// --enable=(true|false|yes|no|1|0)
    "iaxIP:",		// --iaxIP=IPaddress
    "iaxPort:",		// --iaxPort=port
    "load:",		// --load=(true|false|yes|no|1|0)
    "module:",		// --module=<string>
    "newNode:",		// --newNode=int
    "node:",		// --node=int
    "password:",	// --password=<password>	(e.g. "your-node-password")
    "rxChannel:",	// --rxChannel=(SimpleUSB|Radio|Pseudo|Voter|PCIx4)
    "secret:",		// --secret=<string>		(e.g. "your-ami-secret")
    "user:",		// --user=<string>		(e.g. "admin")
    // Optional options
    "debug::",		// --debug[=(0,1,2)]
    "reload",		// --reload
);
#print "\$aslLongOptions: "; print_r($aslLongOptions);

// ASL Command Option default values

$aslOptionDefaults = array(
    "host"    => "localhost",	// the target AMI host
    "iaxIP"   => "127.0.0.1",
    "iaxPort" => 4569,
    "user"    => "admin",	// the AMI user
);

// USAGE
$asl_usage_prefix = "";
function ASL_usage_prefix()		{ global $asl_usage_prefix; return $asl_usage_prefix;    }
function ASL_set_usage_prefix($prefix)	{ global $asl_usage_prefix; $asl_usage_prefix = $prefix; }

// DEBUG
$asl_debug = 0;
function ASL_debug($level = 1)			{ global $asl_debug; return ($asl_debug >= $level);   }
function ASL_set_debug($debug) {
    global $asl_debug;

    if (is_null($debug)) {
	$asl_debug = 0;
    } elseif (is_bool($debug)) {
	$asl_debug = 1;
    } else {
	// ASL
	$valid = filter_var($debug, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
	if (!is_null($valid)) {
	    $asl_debug = $valid ? 1 : 0;
	} else {
	    $valid = filter_var($debug, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	    if (!is_null($valid)) {
		$asl_debug = $valid;
	    } else {
		throw new Exception("Unexpected value for \"--debug\"");
	    }
	}
    }

    AMI_set_debug($asl_debug);	// AMI
}

// RELOAD
$asl_reload = false;
function ASL_reload()			{ global $asl_reload; return $asl_reload;    }
function ASL_set_reload($reload)	{ global $asl_reload; $asl_reload = $reload; }

function getTargetAMIHostInfo($targetHost) {

    // check for a "settings.ini" file

    $iniDirs = array(".", $_SERVER['HOME']);
    $iniFile = "settings.ini";

    foreach ($iniDirs as $dir) {
	$iniPath = $dir . "/" . $iniFile;
    	if (file_exists($iniPath)) {
	    // read and parse the file
	    $config = parse_ini_file($iniPath, true);
	    if ($config === null) {
		throw new Exception("Error parsing \"$iniPath\"");
	    }

	    // get the per-target configuration
	    if (! array_key_exists($targetHost, $config)) {
		throw new Exception("Configuration for host \"$host\" not found in \"$iniFile\"\n");
	    }

	    $targetHostInfo = $config[$targetHost];
	    return $targetHostInfo;
	}
    }

    // if no "settings.ini", check if we can use manager.conf

    if ($targetHost == "localhost") {
	$config = parse_ini_file("/etc/asterisk/manager.conf", true);
	if ($config != null) {
	    if (array_key_exists('general',  $config)			&&
		array_key_exists('bindaddr', $config['general'])	&&
		array_key_exists('port',     $config['general'])	&&
		array_key_exists('admin',    $config)			&&
		array_key_exists('secret',   $config['admin'  ])) {
		$host   = $config['general']['bindaddr'];
		$port   = $config['general']['port'];
		$user   = "admin";
		$secret = $config['admin']['secret'];
		$targetHostInfo = array('host'   => "$host:$port",
					'user'   => $user,
					'secret' => $secret);
		return $targetHostInfo;
	    }
	}
    }

    throw new Exception("Configuration for host \"$targetHost\" not available");
}

function ASLCommandExecute($options) {
    global $aslCommands;
    global $live_dangerously;

    // first, we validate the options
    try {
	$validOptions = ASLCommandValidate($options);
    } catch(Exception $e) {
	throw $e;
    }

    // get the target AMI host
    try {
	$targetHost = $validOptions['host'];
	$targetHostInfo = getTargetAMIHostInfo($targetHost);
    } catch(Exception $e) {
	throw $e;
    }

    if (ASL_debug()) print "Connecting...\n";

    // open a connection to Asterisk
    try {
	$fp = AMIconnect($targetHostInfo['host']);
    } catch(Exception $e) {
	throw $e;
    }

    if (ASL_debug()) print "Logging in...\n";

    // login with Asterisk
    try {
	$user = $targetHostInfo['user'];
	$pass = array_key_exists('secret', $targetHostInfo)
		? $targetHostInfo['secret']
		: $targetHostInfo['passwd'];
	$ok = AMIlogin($fp, $user, $pass);
    } catch(Exception $e) {
	throw $e;
    }

    if (ASL_debug()) print "Executing...\n";

    // get the actions associated with the command
    $command   = $validOptions['command'];
    $info      = $aslCommands[$command];
    $args      = $info['args'];
    $actions   = $info['actions'];
    $x_args    = array_key_exists('x-args', $info) ? $info['x-args'] : null;

    $srcOrig = "";
    $srcLast = "";
    $dstLast = "";

    $x_values = array();

    // iterate over the actions
    foreach ($actions as $key => $action) {
	$updated = false;

	// for actions with an 'enable' key, process conditionally
	if (array_key_exists('enable', $action)) {
	    $enableKey = $action['enable'];
	    $enabled = $validOptions[$enableKey];
	    if (! $enabled) {
		if (ASL_debug()) print "Skipping \"$command\" action \"$key\"\n";
		continue;
	    }
	}

	// the AMI action to be processed
	$amiAction = $action['action'];

	// the file we are reading
	$srcFile   = $action['file'];

	// the AMI string to be executed
	$cmdString = $action['string'];

	// apply [command line] substitutions
	foreach ($args as $arg) {
	    $match = "M-" . $arg;
	    $value = $validOptions[$arg];

	    // update $srcFile
	    $srcFile = str_replace($match, $value, $srcFile);

	    // update $cmdString
	    $cmdString = str_replace($match, $value, $cmdString);
	}

	// apply [configuration file] substitutions
	if ($x_args != null) {
	    foreach ($x_args as $arg) {
		if (array_key_exists($arg, $x_values)) {
		    $match = "X-" . $arg;
		    $value = $x_values[$arg];

		    // update $cmdString
		    $cmdString = str_replace($match, $value, $cmdString);
		}
	    }
	}

	// if we are writing our changes to an different file then ensure that
	// one we iterate over the same target configuration
	if ($srcFile != $srcOrig) {
	    // we are updating a new file
	    $srcOrig = $srcFile;
	    $dstFile = $srcFile;
	    if ($live_dangerously && ASL_debug()) {
		$dstFile .= "-DEBUG";
	    }
	} else {
	    // if using the same source, keep making changes
	    $srcFile = $srcLast;
	    $dstFile = $dstLast;
	}

	#print "  \$srcFile   = \"$srcFile\"\n";
	#print "  \$dstFile   = \"$dstFile\"\n";
	#print "  \$cmdString =\n"; print_r($cmdString);

	// send to AMI
	switch ($amiAction) {
	    case "GetConfig" :
		// send AMI command
		$response = AMIRead($fp,
				    $amiAction,
				    $srcFile,
				    $cmdString);

		// split the response
		$lines = preg_split('/\r\n|\n|\r/', $response);

		//
		// In some cases, we are reporting configurations with
		// key/value content.  Because the "GetConfig" action
		// returns variables defined in both the [template] and
		// the [category] instances there is the possibility of
		// a variable being reported more than once.
		//
		// If "k-v" is included in the actions we accumulate
		// the key/value pairs and later report only the last
		// value from reported by "GetConfig"..
		//
		$lines_kv = array();

		// process the configuration
		foreach ($lines as $line) {
		    if (str_starts_with($line, "Category-")) {
			$category = preg_replace("/^(.+): (.*)$/", "$2", $line);
			continue;
		    }

		    if (str_starts_with($line, "Templates-")) {
			continue;
		    }

		    if (str_starts_with($line, "Line-")) {
			$line = preg_replace("/^(.+): (.*)$/", "$2", $line);

			if (array_key_exists('x-args', $info)) {
			    //
			    // if we are reading the configuration to capture
			    // current settings for subsequent updates
			    //
			    switch ($srcOrig) {
				case "rpt.conf" :
				    $node = $validOptions['node'];

				    switch ($category) {
					case "nodes" :
					    // 1998 = radio@127.0.0.1/1998,NONE
					    // 1001 = radio@192.168.1.8:4568/1001,NONE
					    $iaxIP = preg_filter("/^" . $node . "=radio@([0-9]+.[0-9]+.[0-9]+.[0-9]+)[:\/].*/",
								 "$1",
								 $line);
					    if ($iaxIP != null) {
						$x_values["iaxIP"]   = $iaxIP;
						$x_values["iaxPort"] = 4589;	// default port
					    }

					    $iaxPort = preg_filter("/^" . $node . "=radio@[0-9]+.[0-9]+.[0-9]+.[0-9]+:([0-9]+)\/.*/",
								   "$1",
								   $line);
					    if ($iaxPort != null) {
						$x_values["iaxPort"] = $iaxPort;
					    }
					    break;

					case $node :
					    break;
				    }
				    break;

				case "rpt_http_registrations.conf" :
				    // register=xxxxxx:78f9cda2453b@register.allstarlink.org
				    $node = $validOptions['node'];
				    $password = preg_filter("/^register=" . $node . ":(.+)@register.allstarlink.org/",
							    "$1",
							    $line);
				    if ($password != null) {
					$x_values["password"] = $password;
				    }
				    break;

				default :
				    break;
			    }
			    continue;		// all done

			} elseif (array_key_exists('k-v', $action)) {
			    // extract tke key/value setting
			    $kv = explode("=", $line, 2);
			    if (count($kv) == 2) {
				$lines_kv[$kv[0]] = $kv[1];
				continue;	// save the key/value
			    }

			}

			// "show" the configuration line
			print "$line\n";
		    }
		}

		// "show" any key/value configuration settings
		ksort($lines_kv);
		foreach ($lines_kv as $key => $value) {
		    print "$key=$value\n";
		}

		break;

	    case "ListCategories" :
		$response = AMIRead($fp,
				    $amiAction,
				    $srcFile,
				    $cmdString);
		switch ($command) {
		    case "node_list" :
			$categories = preg_split ('/\r\n|\n|\r/', $response);
			foreach ($categories as $category) {
			    $res = preg_match('/^Category-.*: ([0-9]+)/',
						"$category",
						$match,
						PREG_UNMATCHED_AS_NULL);
			    if ($res === 1) {
				print "$match[1]\n";
			    }
			};
			break;
		    default :
			print "$response\n";
			break;
		}
		break;

	    case "UpdateConfig" :
		try {
		    $response = AMIUpdate($fp,
					  $amiAction,
					  ASL_reload() ? "yes" : "no",
					  $srcFile,
					  $dstFile,
					  $cmdString);
		} catch(Exception $e) {
		    throw $e;
		}
		$updated = true;
		break;
	    default :
		die("No AMI handler for \"$amiAction\"\n");
		break;
	}

	if ($srcOrig != $dstFile) {
	    if ($live_dangerously && $updated && file_exists("/etc/asterisk/" . $dstFile)) {
		// if we made changes
		$srcLast = $dstFile;
		$dstLast = $dstFile;

		// show the diffs
		if (ASL_debug()) {
		    $cmd = "diff -w -c /etc/asterisk/$srcOrig /etc/asterisk/$dstFile";
		    print "$cmd\n";
		    system($cmd);
		}
	    } else {
		$srcLast = $srcOrig;
		$dstLast = $dstFile;
	    }
	}

    }
}

function ASLCommandHelp($command) {
    global $aslCommands;

    // check that we have a valid command
    if (! array_key_exists($command, $aslCommands)) {
	throw new Exception("Unknown command \"$command\".\n\nUsage: " . ASL_usage_prefix() . " --command=<command> [args]");
    }

    $commandInfo = $aslCommands[$command];
    $commandHelp = "Usage: " . ASL_usage_prefix() . " --command=$command " . $commandInfo['help'];

    return $commandHelp;
}

function ASLCommandOptions() {
    global $aslShortOptions, $aslLongOptions;

    return [$aslShortOptions, $aslLongOptions];
}

function ASLCommandOptionDefaults() {
    global $aslOptionDefaults;

    return $aslOptionDefaults;
}

// validate a bool
//   returns [filtered] value on success, NULL on error
function validateBool($bool) {
    $valid = filter_var($bool,
			FILTER_VALIDATE_BOOL,
			FILTER_NULL_ON_FAILURE);
    return $valid;
}

// validate an rxChannel value
//   returns value on success, NULL on error
function validateChannel($rxChannel): mixed {
    $validChannels = array(
	'pcix4'     => "Dahdi/1",			// PCI Quad card
//	'pi'        => "Pi/1",				// Raspberry Pi PiTA
	'pseudo'    => "Dahdi/pseudo",			//				chan_dahdi.so
	'radio'     => "Radio/M-node",			// USBRadio (DSP)		chan_usbradio.so
	'simpleusb' => "SimpleUSB/M-node",		// SimpleUSB			chan_simpleusb.so
//	'usrp'      => "USRP/127.0.0.1:34001:32001",	// GNU Radio interface USRP	chan_usrp.sp
	'voter'     => "Voter/M-node",			// RTCM device			chan_voter.so
    );

    $rxChannel = strtolower($rxChannel);
    if (!array_key_exists($rxChannel, $validChannels)) {
	return null;
    }

    return $validChannels[$rxChannel];
}

// validate a duplex value
//   returns [filtered] value on success, NULL on error
function validateDuplex($node) {
    $valid = filter_var($node,
			FILTER_VALIDATE_INT,
			array(
			    "options" => array("min_range"=>0, "max_range"=>4),
			    "flags"   => FILTER_NULL_ON_FAILURE,
			));
    return $valid;
}

// validate an IP address
//   returns [filtered] value on success, NULL on error
function validateIP($node) {
    $valid = filter_var($node,
			FILTER_VALIDATE_IP,
			FILTER_FLAG_IPV4|FILTER_NULL_ON_FAILURE);
    return $valid;
}

// validate an IP port
//   returns [filtered] value on success, NULL on error
function validatePort($node) {
    $valid = filter_var($node,
			FILTER_VALIDATE_INT,
			array(
			    "options" => array("min_range"=>1, "max_range"=>65535),
			    "flags"   => FILTER_NULL_ON_FAILURE,
			));
    return $valid;
}

// validate a int
//   returns [filtered] value on success, NULL on error
function validateNode($node) {
    $valid = filter_var($node,
			FILTER_VALIDATE_INT,
			array(
			    "options" => array("min_range"=>1, "max_range"=>999999),
			    "flags"   => FILTER_NULL_ON_FAILURE,
			));
    return $valid;
}

// validate a string (constrained by a regex pattern)
//   returns [filtered] value on success, NULL on error
function validateString($string, $pattern) {
    $valid = filter_var($string,
			FILTER_VALIDATE_REGEXP,
			array(
			    "options" => array("regexp" => $pattern),
			    "flags"   => FILTER_NULL_ON_FAILURE,
			));
    return $valid;
}

function ASLCommandValidate($options) {
    global $aslCommands;

    if (ASL_debug(2)) print "Validating...\n";
#   if (ASL_debug(2)) print "\$options = "; print_r($options);

    // check that we have an AMI host
    if (! array_key_exists('host', $options)) {
	throw new Exception("Usage: " . ASL_usage_prefix() . " --command=<command> [args]");
    }

    // check that we have a valid "command"
    if (! array_key_exists('command', $options)) {
	throw new Exception("Usage: " . ASL_usage_prefix() . " --command=<command> [args]");
    }
    $command = $options['command'];
    #print "\$command = $command\n";

    // check that we have a valid command
    if (! array_key_exists($command, $aslCommands)) {
	throw new Exception("Unknown command \"$command\".\n\nUsage: " . ASL_usage_prefix() . " --command=<command> [args]");
    }

    // check that we have the expected arguments
    $commandInfo = $aslCommands[$command];
    #print "commandInfo: "; print_r($commandInfo);

    $commandArgs = $commandInfo['args'];
    #print "  args  : "; print_r($commandArgs);

    foreach ($commandArgs as $requiredArg) {
	if (! array_key_exists($requiredArg, $options)) {
	    $help = $commandInfo['help'];
	    throw new Exception("Missing argument \"$requiredArg\".\n\nUsage: " . ASL_usage_prefix() . " --command=$command $help");
	}

	$value = $options[$requiredArg];
	if (ASL_debug(2)) print "  processing arg(\"$requiredArg\") = \"$value\"\n";

	$valid = null;
	switch ($requiredArg) {
	    case "callsign" :
		$callsign = strtoupper($value);
		$valid = validateString($callsign, "/^[0-9A-Z]{3,}$/");
		break;
	    case "duplex" :
		$valid = validateDuplex($value);
		break;
	    case "enable" :
		$valid = validateBool($value);
		break;
	    case "iaxIP" :
		$valid = validateIP($value);
		break;
	    case "iaxPort" :
		$valid = validatePort($value);
		break;
	    case "load" :
		$valid = validateBool($value);
		if ($valid === NULL) {
		    // if not bool value, look for "load|noload"
		    $valid = validateString($value, "/^(load|noload)$/");
		} else {
		    $valid = $valid ? "load" : "noload";
		}
		break;
	    case "module" :
		$valid = validateString($value, "/^[0-9A-Za-z_]+$/");
		break;
	    case "node" :
	    case "newNode" :
		$valid = validateNode($value);
		break;
	    case "password" :
		$valid = validateString($value, "/^[0-9a-zA-Z]{6,15}$/");
		if ($valid === NULL) {
		    throw new Exception("The node password may only contain letters and numbers. The\n" .
					"password must be between 6 and 15 characters in length");
		}
		break;
	    case "rxChannel" :
		$valid = validateChannel($value);

		// check if this rxChannel driver needs the node #
		if (($valid != NULL) && (preg_match("/\/M-node$/", $valid) == 1)) {
		    if (array_key_exists('newNode', $options)) {
			$valid = str_replace("M-node", $options['newNode'], $valid);
		    } elseif (array_key_exists('node', $options)) {
			$valid = str_replace("M-node", $options['node'], $valid);
		    } else {
			// node # required but not available
			$valid = NULL;
		    }
		}
		break;
	    case "secret" :
		$valid = validateString($value, "/^[0-9a-zA-Z_-]{12,}$/");
		if ($valid === NULL) {
		    throw new Exception("The AMI secret may only contain letters, numbers, underscore and dash. The\n" .
					"secret must be 12 or more characters in length");
		}
		break;
	    case "user" :
		// for now, no validate
		$valid = $value;
		break;
	    default :
		die("Validation for \"$requiredArg\" missing\n");
		break;
	}
	if ($valid === NULL) {
	    throw new Exception("Error: \"$value\" is not a valid value for \"$requiredArg\"");
	}

	if (! ($value === $valid)) {
	    // replace with the validated/sanitized value
	    $oType = gettype($value);
	    $nType = gettype($valid);
	    if (ASL_debug(2)) {
		print "    replacing \"$requiredArg\" with validated/sanitized value: "
		    . "\"$value\" ($oType)"
		    . " -->"
		    . " \"$valid\" ($nType)\n";
	    }
	    $options[$requiredArg] = $valid;
	}
    }

    // if we have a command and all of the required arguments
    return $options;
}

