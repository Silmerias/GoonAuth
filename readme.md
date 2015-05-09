# GoonAuth

## What is this?
GoonAuth is the multi-game authentication system for the FLJK Goonrathi.  It provides a complete registration system including sponsorship and (currently) some basic admin functions.

GoonAuth was designed to verify users on the Something Awful forums and to register users to an LDAP system.  If you don't wish to verify users by their SA account, you must re-program the RegisterController.  In the future this will change to the module system that Games and Organizations use.

GoonAuth was originally created by sct for the ArcheAge goons.  You can view the original project here:
[https://github.com/sct/GoonAuth](https://github.com/sct/GoonAuth)

## The basics
Each User is assigned to a single Group.
Each User can have characters in multiple Games (called a GameUser).
Each GameUser can belong to an Organization.

## Modules
The module system was developed to provide custom functionality for Games and Organizations.

* app/game_modules
* app/org_modules

GoonAuth comes with game modules for MechWarrior Online and Star Citizen.
GoonAuth comes with an org module for the FLJK Goonrathi.

Modules can override views.  For game modules, this is the search path:

* app/game_modules/**&lt;module&gt;**/views/**&lt;game abbr in DB&gt;**/...
* app/views/...

For org modules, this is the search path:

* app/org_modules/**&lt;module&gt;**/views/**&lt;game abbr in DB&gt;**/**&lt;org abbr in DB&gt;**/...
* app/org_modules/**&lt;module&gt;**/views/**&lt;org abbr in DB&gt;**/...
* app/views/...

See the pre-existing modules for examples on how views and e-mails can be altered.

## How to install

### Download dependencies
* php5-mcrypt
* php5-curl
* php5-ldap
* apache2 mod_rewrite
* composer
```
sudo php5enmod mcrypt && sudo service apache2 restart
sudo php5enmod curl && sudo service apache2 restart
sudo php5enmod ldap && sudo service apache2 restart
sudo a2enmod rewrite && sudo service apache2 restart
curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/bin
```

### Set up composer
```
mkdir ~/.composer/vendor/bin
cd ~
echo -e "\nPATH=\"\$HOME/.composer/vendor/bin:\$PATH\"" >> .profile
***relog***
```

### Install the Laravel installer
`sudo composer global require "laravel/installer=~1.1"`

### Grab the GoonAuth code
`git clone https://github.com/LoneBoco/GoonAuth.git`

### Install the vendor projects
```
cd GoonAuth
sudo composer install
```

### Set up the configuration files
```
cd app/config
mkdir production
cp app.php database.php mail.php production
cp goonauth-sample.php production/goonauth.php

cd production
joe app.php
joe database.php
joe mail.php
joe goonauth.php

cd ../../../bootstrap
hostname (remember what this returns)
joe start.php
(in the environment detection, add the production environment:
    'production' => array('hostname goes here')
)
```

### Set up write access
`chmod -R g+w app/storage`

### Set up the classes
```
cd GoonAuth
php composer dump-autoload
php artisan dump-autoload
```

### Set up the database
```
cd GoonAuth
php artisan migrate:install
php artisan migrate
php artisan db:seed
```

### Wipe the database (if needed)
```
cd GoonAuth
php artisan migrate:rollback
php artisan migrate
php artisan db:seed
```
