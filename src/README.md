COVID-19 - DSP declarations management
======================================

[![Conventional Commits][conventional-commits-image]][conventional-commits-url]

This project is about developing a web interface for management of COVID-19 private declarations.

**Project stack**  
* PHP 7.2
* MySQL 5.7
* Laravel 7.3.0
* Composer

## Install and run on local machine

### Prerequisites
Before start install, user will need:
* One [Bionic Beaver][1], and a non-root user with `sudo` privileges.
* [Composer][2]
* [Docker][3]
* [Docker Compose][4]

### Install local
* Clone repository
```shell script
git clone git@github.com:citizennext/covid-19-entry-dsp.git covid-19-entry-dsp
```
* Move into the `covid-19-entry-dsp` directory
```shell script
cd ~/covid-19-entry-dsp
```
* Mount the directories that you will need for your Laravel project and avoid the overhead of installing Composer
 globally
 ```shell script
docker run --rm -v $(pwd):/app composer install
```
* Set permissions on the project directory so that it is owned by your non-root user
```shell script
sudo chown -R $USER:$USER ~/covid-19-entry-dsp
```
* Make a copy of the `.env.example` file that Laravel includes by default and name the copy `.env`
```shell script
cp .env.example .env
```
* Open the file using nano or your text editor of choice
```shell script
nano .env
```
* Modify and add parameters to reflect the specifics of your setup
```shell script
APP_NAME="DSP Declaraţii Coronavirus COVID-19"
APP_LOCALE=ro

DB_HOST=covid19-dsp-db
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
DB_ROOT_PASSWORD=toor

MAIL_MAILER=smtp
MAIL_HOST=0.0.0.0
MAIL_PORT=1025
MAIL_FROM_ADDRESS="covid19_dsp@testmail.com"

COVID19_DSP_API="<url_provided_provided_by_api_maintainer>"
COVID19_DSP_API_KEY="<key_provided_by_api_maintainer>"
CACHE_DECLARATIONS_PERSISTENCE=5 # time in minutes for cache persistence
ADMIN_USER='admin_dsp'
WEB_FORM_URL="<url_provided_provided_for_web_form>"
DECLARATIONS_PER_PAGE_VALUES="10, 20, 30, 50, 100"
DECLARATIONS_PER_PAGE=10
```

### Run local
* Run the `sh` script which deploy Docker environment. Use option `-h` - help in order to check all parameters
```shell script
./run.sh -m -t
```
* Check if all Docker containers are running
```shell script
docker container ls
```
```shell script
b874899e1356        phpmyadmin/phpmyadmin   "/docker-entrypoint.…"   13 hours ago        Up 2 minutes        0.0.0.0:8081->80/tcp                             covid19-dsp-phpmyadmin
926590434ca2        mysql:5.7.22            "docker-entrypoint.s…"   13 hours ago        Up 2 minutes        0.0.0.0:3306->3306/tcp                           covid19-dsp-db
cbf25f7688cf        nginx:alpine            "nginx -g 'daemon of…"   13 hours ago        Up 2 minutes        0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp         covid19-dsp-webserver
f13affc27734        adminer                 "entrypoint.sh docke…"   13 hours ago        Up 2 minutes        0.0.0.0:8080->8080/tcp                           covid19-dsp-adminer
90dd4b6b8dc3        redis:5.0.0-alpine      "docker-entrypoint.s…"   13 hours ago        Up 2 minutes        0.0.0.0:6379->6379/tcp                           covid19-dsp-redis
4a33b9341bfe        covid19_dsp/php         "docker-php-entrypoi…"   13 hours ago        Up 2 minutes        9000/tcp                                         covid19-dsp-app
b00db15a073c        mailhog/mailhog         "MailHog"                13 hours ago        Up 2 minutes        0.0.0.0:1025->1025/tcp, 0.0.0.0:8025->8025/tcp   covid19-dsp-mailhog
```
* Check the migrated data with `tinker`
```shell script
docker container exec -it covid19-dsp-app php artisan tinker
Psy Shell v0.10.2 (PHP 7.2.29 — cli) by Justin Hileman
>>> \DB::table('migrations')->get();
=> Illuminate\Support\Collection {#3012
     all: [
       {#3010
         +"id": 1,
         +"migration": "2014_10_12_000000_create_users_table",
         +"batch": 1,
       },
       {#3019
         +"id": 2,
         +"migration": "2019_08_19_000000_create_failed_jobs_table",
         +"batch": 1,
       },
     ],
   }
>>> 
```

### Reset admin user password
The admin user designated by the customer (DSP) has a `username` set in `.env` file - `ADMIN_USER='admin_dsp'` - and the `generic password` is provided. If for any reason this password is changed or forget, we implemented an Artisan command for resetting to the generic value.   

In production, an administrator with SSH access to the server will run a command in the root of the application. On local/development just run this command in the app container in the root of the application.  
 
```
docker container exec -it covid19-dsp-app php artisan dsp:reset_admin_pass
```

### Re-seed or reset users
If for any reason you need to re-seed or reset `Users` table there is implemented two commands. The full list with
 users and generic passwords are located on `\database\data\users_dsp.json`.   

In production, an administrator with SSH access to the server will run commands in the root of the application. On
 local/development just run this commands in the app container in the root of the application.  

Re-seed `Users` table, attention this command will truncate entire table.
```
docker container exec -it covid19-dsp-app php artisan dsp:users:seed
```
Reset `Users` table, attention this command will truncate entire table, only user `admin_dsp` will remain having
 generic password.
```
docker container exec -it covid19-dsp-app php artisan dsp:users:reset
```

* Enjoy!


[conventional-commits-image]: https://img.shields.io/badge/Conventional%20Commits-1.0.0-yellow.svg
[conventional-commits-url]: https://conventionalcommits.org/
[1]: http://releases.ubuntu.com/18.04.4/
[2]: https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-18-04
[3]: https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-18-04
[4]: https://www.digitalocean.com/community/tutorials/how-to-install-docker-compose-on-ubuntu-18-04
