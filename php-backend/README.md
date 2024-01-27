# PHP Backend

THe PHP backend is part of the Allstar menu.
It uses the Asterisk Manager Interface "UpdateConfig" action to provide an abstraction layer to the rather complex AMI.

More informatioin on the AMI "UpdateConfig" can be found at [https://asterisk.phreaknet.org/#manageraction-UpdateConfig](https://asterisk.phreaknet.org/#manageraction-UpdateConfig)

Note: this script is a backend, not an end-user interface.
The script may be run from the command line as follows:

## The **`asl-configuration.php`** command :

The base command line usage is :

```
Usage: asl-configuration.php --help
       asl-configuration.php --help=<asl-command>
       asl-configuration.php [--host=<host>] [--reload] --command=<asl-command> [ args ]

Valid ASL commands:
  node_create, node_create_full, node_delete, node_rename
  node_set_callsign, node_set_channel, node_set_duplex
  node_set_statpost, ami_set_secret, module_enable
```

#### The "--host=\<host>" argument and "settings.ini" file :

The `--host=<host>` argument can be used to request changes to a specific/target AllStar server configuration.  If not provided, the changes will be made to the local host ("localhost").  The configuration of each target server configuration can be stored in the "settings.ini" file.

The format of the "settings.ini" file is :

```
[localhost]
host=127.0.0.1
user=ami-user			(e.g. "admin")
secret=ami-password

[server1]
host=8.8.8.8
user=server1-ami-user
secret=server1-ami-password
```

#### The "--reload" argument

The `--reload` argument, if specified, will result in the associated asterisk modules being reloaded with any configuration changes being applied.

## Prerequisites

`apt install php-cli`

## Privilege Escalation

In order to update the configurations with this PHP-backend (that uses the AMI commands) you must enable/add `live_dangerously = yes` in the "/etc/asterisk/asterisk.conf" file.

For more information, please refer to 
[https://docs.asterisk.org/Configuration/Dialplan/Privilege-Escalations-with-Dialplan-Functions]()

## Examples

- `asl-configuration.php --command=node_create --newNode=<node> [--iaxIP=<ip>] [--iaxPort=<port>]`
- `asl-configuration.php --command=node_create_full --newNode=<node> --rxChannel=<channel> --duplex=<duplex> --callsign=<callsign> [--iaxIP=<ip>] [--iaxPort=<port>]`
- `asl-configuration.php --command=node_delete --node=<node>`
- `asl-configuration.php --command=node_rename --node=<node> --newNode=<node> [--iaxIP=<ip>] [--iaxPort=<port>]`
- `asl-configuration.php --command=node_set_callsign --node=<node> --callsign=<callsign>`
- `asl-configuration.php --command=node_set_channel --node=<node> --rxChannel=<channel>`
- `asl-configuration.php --command=node_set_duplex --node=<node> --duplex=<duplex>`
- `asl-configuration.php --command=node_set_statpost --node=<node> --enable=(yes|no)`
- `asl-configuration.php --command=ami_set_secret [--user=<user>] --secret=<secret>`
- `asl-configuration.php --command=module_enable --module=astModule --load=(yes|no)`

```
channel = (SimpleUSB|Radio|Pseudo|Voter|Beagle|PCIx4)
duplex  = (0|1|2|3|4)
```
  


