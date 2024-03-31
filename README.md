# AllStarLink Version 3 Menus

This repository contains the ASL Menu system.

Previous versions of ASL included the menu system within the `ASL-Asterisk` source repositories.  The menus are now in their own repository allowing improvements to be made independently.

## Menu Design Goals

- The menu system should only perform commands to manage Asterisk and the ASL node configurations (i.e. editing the config files). OS settings such as setting the IP address, time zone, hostname, etc should be done with other tools.

- Initially, the menu system will consist of the ASL2 menu scripts ported and updated for ASL3.  Additional changes and enhancements will be added over time.

- The menu system will allow editing of the key node data elements including :

	- Node number
	- Node password
	- Node Callsign/ID
	- Radio interface (e.g. USB sound device, HUB node)
	- Duplex type (e.g. full duplex, half duplex, telemetry)
	- USB Interface tuning
	- Stat posting (to [http://stats.allstarlink.org](http://stats.allstarlink.org))

- The menu system will allow you to add (or remove) additional nodes.

- The menu system will provide a simple option to backup (and restore) the configuration.  The backup archives will be stored locally and optionally in the cloud.

- The menu can be setup to auto-launch with SSH login's.  This functionality can be enabled or disabled from the menu itself or other utilities.

- Ideally, any tool that edits the configuration files should assume other tools including text file editors are also being used to update the configuration.  We are trying to ensure this level of compatibility.

## Using the ASL Menu system

At present, the menu system includes the following commands :

- `/usr/sbin/asl-menu`
- `/usr/sbin/node-setup`
- `/usr/sbin/asl-backup-menu`, `/usr/sbin/save-node`, `/usr/sbin/restore-node`

## Futures

- We have already started discussions and are prototyping changes to have the menu system adopt Asterisk's AMI interface as a "better" way to update the configuration files.

- Allow more detailed configuration of the ID (e.g. Voice, CW)

- Add support to install and configure other utilities (e.g. Allmon3, web menus, etc)

## Feedback

Please create a GitHub "issue" for any problems you encounter with the menu system.  Requests for enhancement are also welcome.
