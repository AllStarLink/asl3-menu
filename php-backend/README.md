# Php Backend
This directory contains php CLI scripts using the AMI UpdateConfig action (https://asterisk.phreaknet.org/#manageraction-UpdateConfig). These scripts are used as a backend to a  menu and do not provide a user interface.

## ami.php
Usage is `ami.php host reload node file cmd[=parameter]` where:
- host: AMI hostname:port, id and password from setings.ini
- reload: yes or no (case insensitive) to reload the module
- node: node number
- file: alias to the actual config filename; rpt=rpt.conf, susb=simpleusb.conf, etc
- cmd[=parameter]: alias for the actual command: add_statpost, add_node, etc with optional parameter.

The cmd aliases are shortcuts to "canned" AMI commands to ease validation and usage. The file aliases provide similar functionality.

Currently the output is witten to /etc/asterisk/test.conf. Any changes can then be easially seen w/o trashing the original. One line change to update the original.

To test: edit the settings.ini and run the script.

## Privilege Escalations
`live_dangerously = yes` in asterisk.conf is necessary to use these AMI commands.
https://docs.asterisk.org/Configuration/Dialplan/Privilege-Escalations-with-Dialplan-Functions/

## Cmd Aliases
The cmd aliases are shortcuts to "canned" AMI commands to ease validation and usage.
- rpt_node_create: Adds a new [nodeNumber] section with template and updates [nodes] section.
- rpt_node_rename: Updates the [nodeNumber] and the [nodes] sections.
- rpt_node_delete: Deletes the [nodeNumber] section and removes the entry from the [nodes] sections.
- ami_secret_change: Updates the AMI password for given user.

## File Aliases
These are shortcuts and allowed configs ami.php has access to. Short list so far.
- `rpt = /etc/asterisk/rpt.conf`
- `ami = /etc/asterisk/manger.conf`
- `test = /etc/asterisk/test.txt` is a special case for you know what.

### Examples
- `ami.php localhost No test rpt_node_create 2000 127.0.0.1:4569`
- `ami.php localhost No test rpt_node_delete 2000`
- `ami.php localhost No test rpt_node_rename 2000 1000 127.0.0.1:4569`
- `ami.php localhost no test ami_secret_change admin AneWSecret`
