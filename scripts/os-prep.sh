#!/bin/bash
#

add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php5.6 php5.6-bcmath php5.6-bz2 php5.6-curl php5.6-enchant php5.6-gd php5.6-gmp php5.6-imap php5.6-intl php5.6-json php5.6-ldap php5.6-mbstring php5.6-mcrypt php5.6-mysql php5.6-readline php5.6-xml php5.6-zip imagemagick screen mysql-server mysql-client zip unzip
apt -y upgrade

