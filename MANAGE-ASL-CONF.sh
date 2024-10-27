#!/bin/bash
#
# Usage: ./MANAGE-ASL-CONF.sh ( save | restore | reset | diff-orig | diff-save )
#

SRC_DIRS=(								\
    ~/app_rpt/configs/rpt						\
    /usr/src/app_rpt/configs/rpt					\
    /usr/share/doc/asl3-asterisk-config/examples/configs/asl3		\
 )

DST=/etc/asterisk

SAV=/var/asl-backups/MANAGE-ASL-CONF-SAVED

usage() {
    echo "Usage: $0 ( save | restore | reset | diff-orig | diff-save )"
}

if [[ $# -lt 1 ]]; then
    usage
    exit
fi

# check if root
SUDO=""
if [ $EUID != 0 ]; then
    SUDO="sudo"
    SUDO_EUID=$(${SUDO} id -u)
    if [ ${SUDO_EUID} -ne 0 ]; then
	whiptail --msgbox "This script must be run as root or with sudo" ${MSGBOX_HEIGHT} ${MSGBOX_WIDTH}
	exit 1
    fi
fi

# identify "custom" .conf files
CUSTOM_FILES=$(	find -L $DST -type f -name "*.conf" -print	\
		| sed -n					\
		      -e 's/\/etc\/asterisk\///'		\
		      -e '/^custom\//p'				\
	      )

ACTION=$1
case "$ACTION" in
    "save" )
	;;
    "restore" | "diff-save" )
	if [[ ! -d "${SAV}" ]]; then
	    echo "No backup directory ($SAV)"
	    exit 1
	fi
	;;
    "reset" | "diff-orig" )
	for SRC in ${SRC_DIRS[@]}; do
	    if [[ -d "${SRC}" ]]; then
		break
	    fi
	done
	if [[ ! -d "${SRC}" ]]; then
	    echo "No configuration source directory"
	    exit 1
	fi
	case "${SRC}" in
	    */app_rpt/* )
		ALT=$(dirname "${SRC}")		# remove /rpt
		ALT=$(dirname "${ALT}")		# remove /configs
		ALT=$(dirname "${ALT}")		# remove /app_rpt
		ALT="${ALT}/asterisk/configs/samples"
		if [[ ! -d "${ALT}" ]]; then
		    ALT=""
		fi
		;;
	    * )
		ALT=""
		;;
	esac
	;;
    * )
	usage
	exit 1
esac

for f in			\
    asterisk.conf		\
    dnsmgr.conf			\
    echolink.conf		\
    extensions.conf		\
    gps.conf			\
    iax.conf			\
    logger.conf			\
    manager.conf		\
    modules.conf		\
    rpt.conf			\
    rpt_http_registrations.conf	\
    savenode.conf		\
    simpleusb.conf		\
    usbradio.conf		\
    voter.conf			\
    $CUSTOM_FILES		\

do
    case "$ACTION" in
	"save" )
	    echo "${ACTION}: $f"
	    ${SUDO} mkdir -p $(dirname ${SAV}/$f)
	    ${SUDO} cp -p $DST/$f $SAV/$f
	    ;;
	"restore" )
	    if [[ ! -f $SAV/$f ]]; then
		echo "${ACTION}: $f was not saved"
		continue
	    fi
	    echo "${ACTION}: $f"
	    ${SUDO} cp -p $SAV/$f $DST/$f
	    ${SUDO} chown asterisk $DST/$f
	    ;;
	"reset" )
	    if [[ -f $SRC/$f ]]; then
		echo "${ACTION}: $f"
		${SUDO} cp -p $SRC/$f $DST/$f
		${SUDO} chown asterisk $DST/$f
	    elif [[ -n "${ALT}" && -f $ALT/$f.sample ]]; then
		echo "${ACTION}: $f"
		${SUDO} cp -p $ALT/$f.sample $DST/$f
		${SUDO} chown asterisk $DST/$f
	    else
		echo "${ACTION}: $f was not part of the initial install"
		continue
	    fi
	    ;;
	"diff-orig" )
	    if [[ -f $SRC/$f ]]; then
		sum1=$(sum "$SRC/$f" 2>/dev/null | cut -d ' ' -f 1,2)
		sum2=$(sum "$DST/$f" 2>/dev/null | cut -d ' ' -f 1,2)
		if [[ "$sum1" != "$sum2" ]]; then
		    read -p "Show diff for \"$f\" (y/N): " answer
		    answer=${answer:-n}
		    if [[ "$answer" =~ ^[yY]$ ]]; then
			diff -c $SRC/$f $DST/$f
		    fi
		fi
	    elif [[ -n "${ALT}" && -f $ALT/$f.sample ]]; then
		sum1=$(sum "$ALT/$f.sample" 2>/dev/null | cut -d ' ' -f 1,2)
		sum2=$(sum "$DST/$f" 2>/dev/null | cut -d ' ' -f 1,2)
		if [[ "$sum1" != "$sum2" ]]; then
		    read -p "Show diff for \"$f\" (y/N): " answer
		    answer=${answer:-n}
		    if [[ "$answer" =~ ^[yY]$ ]]; then
			diff -c $ALT/$f.sample $DST/$f
		    fi
		fi
	    else
		echo "${ACTION}: $f was added"
		continue
	    fi
	    ;;
	"diff-save" )
	    if [[ ! -f $SAV/$f ]]; then
		echo "${ACTION}: $f was added"
		continue
	    fi
            sum1=$(sum "$SAV/$f" 2>/dev/null | cut -d ' ' -f 1,2)
            sum2=$(sum "$DST/$f" 2>/dev/null | cut -d ' ' -f 1,2)
	    if [[ "$sum1" != "$sum2" ]]; then
		read -p "Show diff for \"$f\" (y/N): " answer
		answer=${answer:-n}
		if [[ "$answer" =~ ^[yY]$ ]]; then
		    diff -c $SAV/$f $DST/$f
		fi
	    fi
	    ;;
    esac
done

