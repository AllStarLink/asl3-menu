#!/bin/bash
# setup-debug.sh
#
# Systems with "live_dangerously = no" in "/etc/asterisk/asterisk.conf" limit
# what files can be updated using AMI.
#
# The "asl-configuration.php" command has a "--debug" option that writes changes
# to alternate ("-DEBUG") configuration files.  Unfortunately, attempting to use
# the alternate files presents a conflict with living dangerously.
#
# This script ensures that the "-DEBUG" files exist which addresses the living
# dangerously conflict.
#

if [ ${EUID} -ne 0 ]; then
	echo "Must be root"
	exit 1
fi

for f in				\
	extensions.conf			\
	iax.conf			\
	manager.conf			\
	rpt.conf			\
	rpt_http_registrations.conf	\
	simpleusb.conf			\
	usbradio.conf			\
	voter.conf			\

do
	FILE="/etc/asterisk/${f}-DEBUG"
	touch "${FILE}"
	chown asterisk "${FILE}"
done

