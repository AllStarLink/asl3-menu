# Sample Php Code
This directory contains php examples of using the AMI UpdateConfig action (https://asterisk.phreaknet.org/#manageraction-UpdateConfig). These samples are strictly for education and are not for production at this time.

## ami.php
Usage is `ami.php node reload file cmd` where:
- node is used to load the AMI hostname:port id and password
- reload is yes or no to reload the module
- file is an alias to the actual config filename: rpt=rpt.conf, susb=simpleusb.conf, etc
- cmd is an alias for the actual command: add_statpost, add_node, etc

Currently the output is witten to /etc/asterisk/test.conf. Any changes can then be easially seen w/o trashing the original. One line change to update the original.

To test: edit the settings.ini and run the script.

## Privilege Escalations
`live_dangerously = yes` in asterisk.conf is necessary if using these AMI commands.
https://docs.asterisk.org/Configuration/Dialplan/Privilege-Escalations-with-Dialplan-Functions/