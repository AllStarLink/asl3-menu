# `asl-configuration.php`

```
Usage: asl-configuration.php --help
       asl-configuration.php --help=<asl-command>
       asl-configuration.php [--host=<host>] [--reload] --command=<asl-command> [ args ]

Valid ASL commands:
  node_list, node_show, node_create, node_create_full, node_delete
  node_rename, node_set_callsign, node_set_channel, node_set_duplex
  node_set_ipport, node_set_password, node_set_statistics, ami_show
  ami_create, ami_set_secret, module_enable
```

# To Do

- Expand list of allowed rpt.conf cmds

	- Q? what's missing? needed?

	- Maybe add "--command=node\_set\_value --node\_key=\<key> --node\_value=\<value>"?
		- Need list of keys we are OK to update
		- What validation should be performed on the associated values

- Update the "node\_create" (and "node\_delete") handling of `simpleusb.conf`, `usbradio.conf`, and `voter.conf`

	- **??** do we need to support multiple sound devices?  If so, should we consider templatizing the configuration files?

- Update "reload"

	- The AMI reload option allows a boolean string (yes/no, true/false, on/off, 1/0) or the name of a specific module. 
		- **??** should we be reloading everything (e.g. "yes")? "app\_rpt"?  "chan\_simpleusb"?  ...?

	- **??** do we want any/every change to force a reload?  or defer changes until a batch/group have been completed (e.g. perform all of the updates associated with a node # change and then "kick"?  or have a specific [user/admin directed] command that would force the reload function?

	- Obviously, we need to test the reload functionality

- Other high level commands
	- Q? what's missing? needed?

# Notes

- ?