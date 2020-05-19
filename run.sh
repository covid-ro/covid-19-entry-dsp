#!/usr/bin/env bash

RED=`tput setaf 1`
GREEN=`tput setaf 2`
BLUE=`tput setaf 4`
RESET=`tput sgr0` # reset colors

declare -a PLATFORM_LIST=(".docker/docker-compose-main.yml")
source ./src/.env # get configuration file

if [ $# -ne 0 ]; then

    declare -A ARGUMENT_LIST=(
        ["h"]="help"
        ["m"]="mysql-db"
        ["t"]="tools"
    )

    OPTS=$(getopt \
        --longoptions "$(printf "%s," "${ARGUMENT_LIST[@]}")" \
        --name "$(basename "$0")" \
        --options "$(printf "%s," "${!ARGUMENT_LIST[@]}")" \
        -- "$@"
    )

    eval set --$OPTS

    # Exit in case no valid options were recognized
    if [ $# -eq 1 ] && [ $1 == "--" ]; then
        exit 1
    fi

    while [[ $# -gt 0 ]]; do
        case "$1" in
            -h|--help)
                echo "$(basename $BASH_SOURCE) - Local environment usage"
                echo " "
                echo "$(basename $BASH_SOURCE) [options]"
                echo " "
                echo "Without options only php and webserser containers will run, without database support"
                echo " "
                echo "options:"
                echo "-h, --help                show help"
                echo "-m, --mysql-db            run container intended to provide MySQL support)"
                echo "-t, --tools               run containers intended to provide additional tools only (Redis,"
                echo "Adminer, Phpmyadmin, ...)"
                exit 0
                ;;
            -m|--mysql-db)
                sed -i "s#DB_DATABASE#$DB_DATABASE#g" ".docker/docker-compose-mysql.yml"
                sed -i "s#DB_ROOT_PASSWORD#$DB_ROOT_PASSWORD#g" ".docker/docker-compose-mysql.yml"
                PLATFORM_LIST+=(".docker/docker-compose-mysql.yml")
                shift
                ;;
            -t|--tools)
                sed -i "s#DB_HOST#$DB_HOST#g" ".docker/docker-compose-tools.yml"
                sed -i "s#DB_ROOT_PASSWORD#$DB_ROOT_PASSWORD#g" ".docker/docker-compose-tools.yml"
                PLATFORM_LIST+=(".docker/docker-compose-tools.yml")
                shift
                ;;
            --)
                shift
                ;;
            *)
                echo "Invalid config provided: $(basename $BASH_SOURCE) -h for help"
                exit 1
                break
                ;;
        esac
    done
fi;

docker-compose $(printf -- "-f %s " "${PLATFORM_LIST[@]}") config > .docker/docker-compose.yml
docker-compose -f .docker/docker-compose.yml up -d

echo -en "\n"
echo "${RED}Run in covid19-dsp-app container composer install${RESET}"
docker container exec -it covid19-dsp-app composer install
echo -en "\n"
echo "${RED}Generate a key and copy it to your .env file, ensuring that your user sessions and encrypted data remain secure${RESET}"
docker container exec -it covid19-dsp-app php artisan key:generate
echo -en "\n"
echo "${RED}Run 'npm' for frontend${RESET}"
docker container exec -it covid19-dsp-app npm install
docker container exec -it covid19-dsp-app npm run dev
echo -en "\n"
echo "${RED}Cache these settings into a file${RESET}"
docker container exec -it covid19-dsp-app php artisan config:cache
echo -en "\n"
echo "${RED}Refresh .env file and config${RESET}"
docker container exec -it covid19-dsp-app php artisan config:clear

eval "$(grep ^DB_ROOT_PASSWORD= .env)"
echo -en "\n"
echo "${RED}Create the user account that will be allowed to access this database and flush the privileges to notify the MySQL
 server of the changes${RESET}"
docker container exec -it covid19-dsp-db mysql -uroot -p$DB_ROOT_PASSWORD -e "GRANT ALL ON laravel.* TO 'laravel'@'%' IDENTIFIED BY
'laravel';FLUSH PRIVILEGES;"

echo -en "\n"
echo "${RED}Migrate the data${RESET}"
docker container exec -it covid19-dsp-app php artisan migrate
