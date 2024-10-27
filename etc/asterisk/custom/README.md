
# ASL3 Node Menu Customization

## "/etc/asterisk/custom" directory

The "/etc/asterisk/custom" directory contains customizations to the ASL3 configuration files.
The base file and the optional customization(s) include :

| "/etc/asterisk" file | Can be customized / extended with
| -------------------- | ---------------------------------
| echolink.conf | custom/echolink.conf
| extensions.conf | custom/extensions.conf
| gps.conf | custom/gps.conf
| iax.conf | custom/iax.conf
| rpt.conf | custom/rpt.conf, custom/rpt/\*.conf
| simpleusb.conf | custom/simpleusb.conf, custom/simpleusb/\*.conf
| usbradio.conf | custom/usbradio.conf, custom/usbradio/\*.conf

## "rpt.conf" customizations

Customizations to "rpt.conf" are special.
In addition to the "custom/rpt.conf" file we also include ".conf" files in the "custom/rpt/" directory.
Each of these ".conf" files can include a comment line with the following format :

```
;MENU:keyed-gpio4:node:Assert GPIO (pin 4) when keyed
```

The ASL3 node setup menu looks for these specially formatted comment lines
to present customization options.  Each line consists of 4 fields separated by
":" characters.

| Field | Text           | Description
| :---: | :------------: | :----------
| #1    | ;MENU          | This field starts with a leading ";" (it's a comment) followed by the string "MENU"
| #2    | \<category>    | This field names the [category] to associate with the node
| #3    | \<type>        | This field name(s) the type of customization
| #4    | \<description> | This field describes the node customization

For node customizations that apply to the `/etc/asterisk/rpt.conf` file, the \<type> value is one (or more) of the following customizaton values.  Multiple \<type> values should be separated by comma (",") characters.

| \<type>    | Description
| :--------: | :----------
| node       | Updates the [#####] node settings
| events     | Updates the [events] settings
| functions  | Updates the [functions] settings
| telemetry  | Updates the [telemetry] settings
| wait-times | Updates the [wait-times] settings

Two additional \<type> values can be added to `/etc/asterisk/rpt.conf` customizations.
The values, `duplex` and `rxchannel`, mirror information that must be present in the associated templates.
If these values are included, and both must be specified, then the customization will be included in the "new" node type selection menu.

| \<type>                          | Description
| :------------------------------: | :----------
| `duplex=(0|1|2|3|4)`             | Specifies the `duplex` value **in** the template
| `rxchannel=(SimpleUSB|USBRadio)` | Specifies the `rxchannel` value **in** the template

## "simpleusb.conf" customizations

Customizations to "simpleusb.conf" are also special.
In addition to the "custom/simpleusb.conf" file we also include ".conf" files in the "custom/simpleusb/" directory.
As with the "rpt.conf" customizations, we also look for the specially formatted comment lines.
These customizations will be added for to the menu for any node using the "SimpleUSB" channel driver.

For audio interface customizations that apply to the `/etc/asterisk/simpleusb.conf` file, the \<type> value should be set to "simpleusb".

| \<type>    | Description
| :--------: | :----------
| simpleusb  | Updates the audio interface settings

## "usbradio.conf" customizations

Customizations to "usbradio.conf" are also special.
In addition to the "custom/usbradio.conf" file we also include ".conf" files in the "custom/usbradio/" directory.
As with the "rpt.conf" customizations, we also look for the specially formatted comment lines.
These customizations will be added for to the menu for any node using the "SimpleUSB" channel driver.

For audio interface customizations that apply to the `/etc/asterisk/usbradio.conf` file, the \<type> value should be set to "usbradio".

| \<type>    | Description
| :--------: | :----------
| usbradio   | Updates the audio interface settings

## Changes to the "rpt.conf" configuration file

Some changes to the "rpt.conf" file are needed in order to support these menu customizations.

1. All of the "\[####]" node stanzas must be at the end of the file.
2. Prior to the "\[####]" stanza we have added a set of "#tryinclude" statements like those shown below.  These statements will look for, and optionally include the contents of, configuration customization files that can be referenced by the node stanzas that follow after the "#tryinclude"s.

```
#tryinclude "custom/rpt.conf"
#tryinclude "custom/rpt/*.conf"
```

## Using Asterisk templates

In ASL3, the configuration of each node is typically setup with the node (e.g. [63001]) inheriting from the [node-main] category and any settings overriding those in the template.
The ASL3 node setup customization menu adds to the list of categories inherited by the node.

Without any customizations, the "rpt.conf" node configuration might look like :

```
[63001](node-main)
idrecording = |iWB6NIL
```

The **Assert GPIO (pin 4) when keyed** customization adds additional "events" to a node.  To support this customization the menu will make the following changes :

- We create a new "[events-63001]" stanza for the node.
The stanza is setup to inherit events being made available to all nodes ("[events-main]") and the new GPIO pin 4 events ("[events-keyed-gpio4]").

- We update the per-node settings ("[63001]") to reference the new event stanza.

The changes would look like :

```
[events-63001](events-main,events-keyed-file)   <--- Added

[63001](node-main)
events = events-63001                           <--- Added
idrecording = |iWB6NIL
```

For more about templates, please refer to the Asterisk [Using Templates](https://docs.asterisk.org/Fundamentals/Asterisk-Configuration/Asterisk-Configuration-Files/Templates/Using-Templates/?h=template) documentation.

## Viewing a customized node configuration

The GitHub [ASL3 menu](https://github.com/AllStarLink/asl3-menu.git) project includes some EXPERIMENTAL (still in development) code that can be used to extract the expanded configuration for a node.
Assuming that you have already cloned the GitHub repository, you can use the following commands to view a node :

```bash
cd asl3-menu
./php-backend/asl-configuration.php --command=node_show      --node=63001
./php-backend/asl-configuration.php --command=simpleusb_show --node=63001
./php-backend/asl-configuration.php --command=usbradio_show  --node=63001
```

The output will include the settings from the \[node-main] stanza, from any enabled customization stanzas, and any per-node overrides.
