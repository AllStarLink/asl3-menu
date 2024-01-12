# Sample Php Code
This directory contains php examples of using the AMI UpdateConfig action (https://asterisk.phreaknet.org/#manageraction-UpdateConfig). These samples are strictly for education on how to use the AMI and are not for production.

## addStatpost.php
This example adds the statpost setting to the chosen node. The node number, node ip address, colon seperated optional AMI port, AMI login and password must be set in the ini file. The node must exist. The AMI can add nodes with the NewCat action or renamed a node with the RenameCat action. Examples of those later.

The output is witten to test.conf. Any changes can then be easially seen w/o trashing the original. In real life you'd set the dstfilename to the same as the srcfilename and likely reload.

To test: edit the ini file then run this script with `php addStatpost.php`.

## Privilege Escalations
`live_dangerously = yes` in asterisk.conf is necessary if using these AMI commands.
https://docs.asterisk.org/Configuration/Dialplan/Privilege-Escalations-with-Dialplan-Functions/