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
`live_dangerously = yes` in asterisk.conf is necessary if using these AMI commands.
https://docs.asterisk.org/Configuration/Dialplan/Privilege-Escalations-with-Dialplan-Functions/

## cmd Aliases
The cmd aliases are shortcuts to "canned" AMI commands to ease validation and usage.
- add_node: Adds a new node category with template, ie `[2000](node-main)`
- add_nodes: Adds line to [nodes] category, ie `2000 = radio@127.0.0.1/2000,NONE`
- add_statpost: Adds `statpost_url=http://stats.allstarlink.org/uhandler` to specified node

### Example
- `ami.php localhost No 2000 rpt add_node`
- `ami.php localhost No 2000 rpt add_nodes=127.0.0.1:4569`
- `ami.php localhost No 2000 rpt add_statpost`