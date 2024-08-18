# GrowCART
Grow (C)ompliance (A)nd (R)egulatory (T)racking

## Installation instructions for Ubuntu 24.04

### Swap
In case you're running a low memory system we're going to add some more from swap:
<pre>
fallocate -l 2G /swapfile.img
chmod 0600 /swapfile.img
mkswap /swapfile.img
swapon /swapfile.img
echo "/swapfile.img swap swap defaults 0 0" >> /etc/fstab
</pre>

You should be able to run:
<pre>free -m</pre>
And see:
<pre>
root@growcart:~# free -m
               total        used        free      shared  buff/cache   available
Mem:             458         177         189           6         118         280
Swap:           2047           0        2047
root@growcart:~#
</pre>


### Start with the basics
Set your timezone with:
<pre>
cp /usr/share/zoneinfo/Pacific/Auckland /etc/localtime
dpkg-reconfigure -f noninteractive tzdata
</pre>


<pre>apt-get update
apt install nginx mysql-server php8.3 php8.3-fpm php-mysql php-cli python3-certbot-nginx -y</pre>

Then we remove the default index files, we don't need those any more:
<pre>rm -f /var/www/html/index.html /var/www/html/index.nginx-debian.html</pre>

Add our own to test php works later
<pre>echo "<?php phpinfo(); >" > /var/www/html/index.php</pre>

### Setup nginx to use php8.3
<pre>nano -w /etc/nginx/sites-enabled/default</pre>
You're going to want to modify the part for your default server that says:
<pre>
        # pass PHP scripts to FastCGI server
        #
        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        }
</pre>

A couple of lines further up you'll want to add index.php to the list too:
<pre>        # Add index.php to the list if you are using PHP
        index index.html index.htm index.nginx-debian.html index.php;</pre>

### Secure the sql installation
<pre>mysql_secure_installation</pre>

You'll see a few options, choose:
<pre>Validate password component = Yes
Validation strength = 1: Medium
Remove anonymous users = Yes
Disallow root login remotely = Yes
Remove test database = Yes
Reload privilege tables now = Yes</pre>


### Get things running on startup
<pre>
systemctl enable php8.3-fpm
systemctl start php8.3-fpm
systemctl enable nginx
systemctl start nginx
systemctl enable MySQL
systemctl start MySQL
</pre>

### Setup SSL
<pre>certbot --nginx</pre>
Follow the prompts and setup SSL. It should restart nginx for you automatically!

### Clone the repo
Setup still needs extra work with importing SQL but this is a start;
<pre>
git clone https://github.com/Chill-Division/GrowCART.git
cd GrowCART
cp config-example.php config.php
mkdir uploads offtakes police_vet_checks sops
</pre>

Don't forget to set the password for your database which you're about to create next

### Create the database
<pre>mysql</pre>

Then run the following from within mysql / mariadb
<pre>-- Create a new database
CREATE DATABASE GrowCART;

-- Create a new user with a password
CREATE USER 'growcart'@'localhost' IDENTIFIED BY 'Your_P@ssw0rd_Goes_Here!';

-- Grant all privileges on the GrowCART database to the new user
GRANT ALL PRIVILEGES ON GrowCART.* TO 'growcart'@'localhost';

-- Flush privileges to apply the changes immediately
FLUSH PRIVILEGES;</pre>

Then type "\q" to exit.

Then the following will get the database schema ready for you
<pre>mysql -u growcart -p GrowCART < growcart-schema.sql</pre>

### Initial login
Once installed, simply browse to your servers IP /GrowCART/

You'll want to log in with:
<pre>User: default
Pass: changeme</pre>

Go to the Administration page and add a new user. Log out. Log in as your new user. Remove the "default" user.
