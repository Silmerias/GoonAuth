# GoonAuth

## What is this?
GoonAuth is the multi-game authentication system for goon games organizations.  It provides a complete registration system including sponsorship and (currently) some basic admin functions.

GoonAuth was designed to verify users on the Something Awful forums and to register users to an LDAP system.  It also grant J4G to register and apply in non-SA organizations.

GoonAuth was originally created by sct for the ArcheAge goons.  You can view the original project here:
[https://github.com/sct/GoonAuth](https://github.com/sct/GoonAuth) and refactored by Nalin from the Star Citizen goons.  You can view the original project here: [https://github.com/LoneBoco/GoonAuth](https://github.com/LoneBoco/GoonAuth)

## The basics
Each User is assigned to a single Group.
Each User can have characters in multiple Games (called a GameUser).
Each GameUser can belong to an Organization.

## Features
GoonAuth comes with:

* Discord authentication

## Game Modules
The game module system was developed to provide custom functionality for Games and Organizations.

* modules/games
* modules/organizations

GoonAuth comes with modules for:
* Eve Echoes
* Star Citizen
* MechWarrior Online

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
* php-mbstring
* php-dom
* libapache2-mod-php
* composer
```
sudo phpenmod mcrypt
sudo phpenmod curl
sudo phpenmod ldap
sudo phpenmod mbstring
sudo phpenmod dom
sudo a2enmod rewrite
sudo service apache2 restart
```

### Install the Laravel installer
`composer global require "laravel/installer"`

### Grab the GoonAuth code
`git clone https://github.com/Silmerias/GoonAuth.git`

### Install the vendor projects
```
cd GoonAuth
composer install
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
