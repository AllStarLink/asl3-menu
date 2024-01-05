# AllStarLink Version 3 Menu
This repository contains the ASL Menu system. Previous versions of ASL contain the menu within the app_rpt source repository. The menu is now in its own repo allowing improvements to be made independent of app_rpt.

## Menu Design Goals
- This menu should only perform Asterisk and node configurations, ie editing the config files. OS settings such as setting the IP address, time zone, hostname, etc should be done with other tools.
- Initially the menu may be a reworked ASL2 menu (the asl-menu script) for ASL3. At some point, maybe soon, this menu should use an api, perhaps the AMI (https://asterisk.phreaknet.org/#manageraction-UpdateConfig) in common with other utilities such as a web based system.
- Ideally any tool that edits the config files should assume other tools including text file editors remain compatible.
- It should auto launch with ssh log in. May be disabled or enabled from itself or other utilities.
- It should auto launch the add node menu. Disable after first time use.
- Add node. Same data elements as Edit node.
- Delete node.
- Edit node data elements: Voice and CW IDs, channel driver, node number, node password, stat post (y/n), backup (call save_node script) node number and password, https registration node number and password.
- Enable/disable other utilities: Allmon3, Web menu
- Entry for simple-tune-menu (or it’s replacement)
