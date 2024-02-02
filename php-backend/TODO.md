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
	- Q? what's missing?
		- Maybe "--node\_set\_value --node\_key=<key> --node\_value=<value>" ?
			- Need list of keys we are OK to update
			- Need validation information for the associated values
- Add high level commands
	- Q? what's needed?
- Build other .conf file allowed cmds
	- ??
- Update the "node\_create" (and "node\_delete") handling of `simpleusb.conf`, `usbradio.conf`, and `voter.conf`
	- **??** are these files going to be templatized?
- Change reload. It’s not a boolean expression. The module to reload may be specified. 
	- **??** should we be reloading "app\_rpt"?  "chan\_simpleusb"?  ...?
	- **??** do we want every/any change to force a reload?  or defer changes until a batch/group has been completed (e.g. perform all of the updates associated with a node # change and then "kick"?  or have a specific [user/admin directed] command that would start/exec the reload function ?
	- Obviously, we need to test the reload functionality

# Notes

- **??CONFIRM??** The "plan" was/is to change `simpleusb.conf` to use just the node # as the category for settings and NOT the node # with a "usb\_" prefix.  For example, use "[XXXXXX]" instead of "[usb\_XXXXXX]".  Same for `usbradio.conf`.