# `asl-configuration.php`

```
Usage: asl-configuration.php --help
       asl-configuration.php --help=<asl-command>
       asl-configuration.php [--host=<host>] [--reload] --command=<asl-command> [ args ]

Valid ASL commands:
  node_list, node_show, node_create, node_create_full, node_delete
  node_rename, node_set_callsign, node_set_channel, node_set_duplex
  node_set_password, node_set_statistics, ami_show, ami_set_secret
  module_enable
```

# To Do

- Expand list of allowed rpt.conf cmds
	- Q? what's missing?
- Add high level commands
	- Q? what's needed?
- Build other .conf file allowed cmds
	- ???
- Update "node\_create" (and "node\_delete") handling of `simpleusb.conf`, `usbradio.conf`, and `voter.conf`
	- Q? are these files going to be templatized?
- Change reload. It’s not a boolean expression. The module to reload may be specified. 
	- Should we be reloading "app_rpt"?  "chan_simpleusb"?  ...?
	- Need to test the reload functionality
	- Q? do we want any change to force a reload?  or leave the reload function to a specific "we're done making changes" command?

# Notes

- The "plan" was/is to change `simpleusb.conf` to use just the node # as the category for settings and NOT the node # with a "usb\_" prefix.  For example, use "[XXXXXX]" instead of "[usb\_XXXXXX]".  Same for `usbradio.conf`.