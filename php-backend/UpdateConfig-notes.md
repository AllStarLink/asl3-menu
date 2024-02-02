# AMI UpdateConfig Implementation Notes

- The editor allows adding duplicate Vars. Check for existing Vars and Update as necessary.

- Duplicate Categories are optionally allowed.

- **??BUG??** Deleting a Var leaves an empty line.

- New Categories are added to the end of the file.

  - Found that if the last line in a file is a comment, an added Category incorectly starts at the end of the line, not the end of the file.

	  - **??Q (Tim)??** I noticed that last line of the "rpt.conf" file did not have new-line. Could this have explained the behavior you observed?

- **??BUG??** Deleting a Category removes all blank lines and comments above it.

- **??BUG??** Any indented comments will have the leading space removed.

	- **??BUG??** I don't know if this matters but would like to know/confirm whether the leading white space in the "extensions.conf" is important.  Asking because we start off with :

		```
		[iax-client]                            ; for IAX VoIP clients.
		exten => ${NODE},1,Ringing()
		        same => n,Wait(10)
		```
  and after being written looks like :

		```
		[iax-client]; for IAX VoIP clients.
		exten => ${NODE},1,Ringing()
		same => n,Wait(10)
		```

- AMI won't set a var if the value matches the template.

- GetConfig will return the values from the "template" and the "child".  One gotcha is that a value present in both will result in multiple lines being returned.  For example, if your "rpt.conf" has :

	```
	[node-main](!)
	duplex = 2
	...
	[1234](node-main)
	duplex = 1
	...
	```
then a "GetConfig [1234]" would include :

	```
	duplex = 2
	duplex = 1
	```
Note: our PHP code works around this!

- **??BUG??** When updating "extensions.conf", the `#tryinclude custom/extensions.conf` line at the end of the file is changed to `#include custom/extensions.conf`.  Also, a new (essentially empty) file is written to "/etc/asterisk/custom".

# Status

### Re: live_dangerously

So far, this does not appear to be an issue :-)

The `asl-configuration.php` command can successfully read/write the following configuration files :

- rpt.conf
- rpt\_http\_registrations.conf
- extensions.conf
- iax.conf
- simpleusb.conf
- usbradio.conf
- voter.conf
- manager.conf

Where I have run into trouble is when asking AMI to write to a file that does not already exist in the configuration.  For example, the `asl-ami-commands.php` code can be setup to read from the base configuration files (e.g. "rpt.conf") and write to debug files (e.g. "rpt.conf-DEBUG").  This works under one of two conditions :

1. 	`live_dangerously=yes`	(set in /etc/asterisk/asterisk.conf)
2. the destination file already exists (e.g. "touch" the -DEBUG files before exec'ing)

The online docs are not clear about the rules/exceptions.
For more information, please refer to 
[https://docs.asterisk.org/Configuration/Dialplan/Privilege-Escalations-with-Dialplan-Functions]()
