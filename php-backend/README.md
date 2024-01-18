# Php Backend
Php backend is part of the Allstar menu.  It uses the Asterisk Manager Interface UpdateConfig action (https://asterisk.phreaknet.org/#manageraction-UpdateConfig) to provide an abstraction layer to the rather complex AMI. This script is a backend, not an end-user interface. It may be run from the command line as follows:

## ami.php
Usage is `ami.php host reload fileAlias cmdAlias parameter [parameter] ...`
### host:
 Selects the target Allstar server. Provides hostname:port, id and password from settings.ini
### reload: 
Is `no` (case insensitive) or module to reload. Use only ‘no’ for now.
### fileAlias:  
Is shorthand to allowed conf files for security, validation and ease of use. The `test` alias is for experimentation. Copy one of the conf files to /etc/asterisk/test.txt.
- `rpt = /etc/asterisk/rpt.conf`
- `ami = /etc/asterisk/manger.conf`
- `test = /etc/asterisk/test.txt`
### cmdAlias:
Is shorthand to Allstar commands for security, validation and ease of use. Examples below.
### parameters:
Each command has a required number of parameters. CLI prompts required number, variables and values.

## Prerequisites
`apt install php-cli`
### Privilege Escalations
`live_dangerously = yes` in asterisk.conf is necessary to use these AMI commands.
https://docs.asterisk.org/Configuration/Dialplan/Privilege-Escalations-with-Dialplan-Functions/

## Examples
- `ami.php localhost No test rpt_node_create 2000 127.0.0.1:4569`
- `ami.php localhost No test rpt_node_delete 2000`
- `ami.php localhost No test rpt_node_rename 2000 1000 127.0.0.1:4569`
- `ami.php localhost no test ami_secret_change admin AneWSecret`
