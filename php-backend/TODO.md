# To Do
- Expand list of allowed rpt.conf cmds. Add high level commands.
- Build other .conf file allowed cmds.
- Change reload. It’s not a boolean expression. The module to reload may be specified.
	- Need to test the reload functionality.
	- The asl-configuration.php command has `--reload` as a simple argument.  Specify the arg and the reload flag is passed along with the AMI UpdateConfig command.