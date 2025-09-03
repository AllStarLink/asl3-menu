# asl-backup-menu

## NAME
asl-backup-menu - Backup AllStarLink node configuration

# SYNOPSIS
**/usr/bin/asl-backup-menu** [ menu ]

**/usr/bin/asl-backup-menu** **backup** [ --post-exec \<command> ] [ --no-prompt ] [ --local-only ]

**/usr/bin/asl-backup-menu** **backup-local**

**/usr/bin/asl-backup-menu** ( **restore-local** | **restore-asl** | **restore-factory** )

**/usr/bin/asl-backup-menu** **remove**

Required arguments :

None.

Optional arguments :

**backup**
: create a local backup to /var/asl-backups and prompt to backup to backup.allstarlink.org (via menu)

**backup-local**
: create an immediate local backup to /var/asl-backups (no menu)

**restore-local**
: restore all config files from a previously created backup archive

**restore-asl**
: restore all config files from a previously created backup.allstarlink.org archive

**restore-factory**
: restore all config files in /etc/asterisk to defaults
   
**-\-post-exec \<command>**
: specifies the `<command>` to be executed after a local backup has completed

**-\-no-prompt**
: turns off any menu interactions / prompts

**-\-local-only**
: when using the `backup` option, will not attempt to save the backup archive to backup.allstarlink.org.
   
## DESCRIPTION
asl-backup-menu - Backup AllStarLink node configuration

The `asl-backup-menu` utility is normally invoked via [asl-menu](../user-guide/menu.md). However, it can be invoked from the Linux CLI with `sudo asl-backup-menu` by itself, or with any of the supported command line options.

The "Create" option will create a backup archive of your configuration. The archive will be stored locally in the /var/asl-backups directory in .tgz format. After the backup is created you will have the option to upload to archive to an AllStarLink backup server. The /var/asl-backups directory will be created automatically, if it doesn't already exist.

> **Warning: Maximum Archive Size**
>
> The maximum backup archive size permitted to be uploaded to backup.allstarlink.org is 2Mb. 

<!-- break  -->

> **Note: Post-Processing**
> 
> If the `asl-backup-menu` command is executed with the `--post-exec <command>` argument then the provided `<command>` will be executed after a local backup has been completed.  The path of the backup archive will be passed to the post-exec command as the first argument.  The post-processing command should "exit 0" on success, non-zero if any errors were encountered.

The "Restore" options will allow you to restore an AllStarLink configuration from a previously saved backup. The backup archive will be from either local storage or from the AllStarLink backup server. You also have the option to restore the ASL3 "default" configuration.

> **Note: Restore Factory**  
> The restore-factory option (and it's related menu item) will re-install the asl3-asterisk-config package, overwriting all the .conf files in `/etc/asterisk` with their default versions.  The `asl-backup-menu` command will *"try"* and retain the "secret=" key/value in the `/etc/asterisk/manager.conf` when it re-installs the config files.  **Asterisk must be restarted after this process to re-initialize with the new defaults.**

The "Delete" option will allow you to remove any of your locally stored backups.

The "Edit" option will allow you to view and/or update the file (and directory) path names that will be included in a backup archive. This information is stored in `/var/asl-backups/asl-backup-files`. The default `asl-backup-files` will be created the first time `asl-backup-menu` is run.

## BUGS
Report bugs to https://github.com/AllStarLink/asl3-menu/issues

## COPYRIGHT
Copyright (C) 2025 Allan Nathanson and AllStarLink under the terms of the AGPL v3.
