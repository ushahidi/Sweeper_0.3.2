s#!/bin/bash

# Perform Debian updates.
apt-get update
apt-get upgrade -q -y

# Install necessary Debian packages.
apt-get install -q -y apache2 mysql-server php5 php5-curl php5-mysql php-pear git-core

# Enable Apache mod_rewrite.
a2enmod rewrite

# Enable Apache .htaccess files.
sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/sites-enabled/000-default

# Create MySQL user account for Sweeper.
echo 'create database sweeper default charset utf8; grant all on sweeper.* to sweeper@localhost identified by "sweeper";' | mysql -u root

# Install the PHP PEAR Log package.
pear install Log

# Restart Apache to ensure new configuration is loaded.
/etc/init.d/apache2 restart

# Clone Sweeper and Swiftriver repositories into /var/www.
cd /var/www
rm index.html
git clone https://github.com/ushahidi/Sweeper.git .
git clone https://github.com/ushahidi/Swiftriver.git core

# Transfer ownership of the application to the same user as the Apache process.
chown -R www-data:www-data .
