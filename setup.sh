#!/bin/bash
#

function sanity_check {
	if [ `cat /etc/os-release | grep VERSION_ID | cut -d "\"" -f 2` != "16.04" ]; then echo "You must be running Ubuntu 16.04 LTS" && exit 0; fi
	if [ `whoami` != "root" ]; then echo "You must run me as root or with sudo" && exit 0; fi
	if [ `basedir $0` != "/var/www" ]; then echo "You must copy or move this folder to /var/www and run me from there" && exit 0; fi
}

function help {
	echo "Newscoop Legacy setup.sh usage:"
	echo ""
	echo "setup.sh base|system|theme|plugin|help [subcommand]"
	echo "	base all|os|apache|newscoop|clean"
	echo "	system update|backup|restore|backup-data|backup-site|reestore-data|restore-site"
	echo "	theme install|remove [theme-name]"
	echo "	plugin install|remove [plugin-name]"
	echo "	help (this message)"
}


function base {
	case "$UARG" in	
		all)
			scripts/clean-prep.sh
			scripts/os-prep.sh
			scripts/apache-prep.sh
			scripts/newscoop-prep.sh
			;;
		os)
			scripts/os-prep.sh
			;;
		apache)
			scripts/apache-prep.sh
			;;
		newscoop)
			scripts/newscoop-prep.sh
			;;
		clean)
			scripts/clean-prep.sh
			;;
		*)
			help
			;;
	esac
}

# Main
sanity_check
UARG=$2A
UBASE="/var/www"

case "$1" in
	base)
		base
		;;
	system)
		echo "TODO"
		;;
	theme)
		echo "TODO"
		;;
	plugin)
		echo "TODO"
		;;
	help|*)
		help
		;;
esac
