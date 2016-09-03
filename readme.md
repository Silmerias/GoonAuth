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

* modules/games
* modules/organizations

GoonAuth comes with modules for:
* MechWarrior Online
* Star Citizen

Modules can override views.  For game modules, this is the search path:

* modules/games/views/**&lt;game abbr in DB&gt;**/...
* resources/views/...

For org modules, this is the search path:

* modules/organizations/views/**&lt;game abbr in DB&gt;**/**&lt;org abbr in DB&gt;**/...
* modules/organizations/views/**&lt;org abbr in DB&gt;**/...
* resources/views/...

See the pre-existing modules for examples on how views and e-mails can be altered.

## How to install

### Download dependencies
* apache2
* php
* php-mcrypt
* php-gd
* php-curl
* php-ldap
* libapache2-mod-php
* composer
```
sudo phpenmod mcrypt
sudo phpenmod curl
sudo phpenmod ldap
sudo a2enmod rewrite
sudo service apache2 restart
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
`sudo composer global require "laravel/installer"`

### Grab the GoonAuth code
`git clone https://github.com/LoneBoco/GoonAuth.git`

### Install the vendor projects
```
cd GoonAuth
sudo composer install
```

### Set up the configuration files
```
cp .env.example .env
vim .env
```

### Generate your encryption key
`php artisan key:generate`


### Set up write access
```
chmod -R g+w storage
chmod -R g+w bootstrap/cache
```

### Set up the classes
```
composer dump-autoload
php artisan optimize
```

### Set up the database
```
php artisan migrate:install
php artisan migrate
php artisan db:seed
```

### Wipe the database (if needed)
```
php artisan migrate:rollback
php artisan migrate
php artisan db:seed
```
