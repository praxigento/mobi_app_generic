#!/usr/bin/env bash
## *************************************************************************
#   Deploy Magento 2 in development mode.
## *************************************************************************

##
#   Working variables and hardcoded configuration.
##
CUR_DIR="$PWD"
DIR="$( cd "$( dirname "$0" )" && pwd )"

#   Load deployment configuration.
. $DIR/deploy_cfg.sh

#   Create shortcuts.
M2_ROOT=$DIR/$DEPLOY_MODE
DHOME=$DIR/deploy/$DEPLOY_MODE
COMPOSER_MAIN=$M2_ROOT/composer.json
COMPOSER_UNSET=$DHOME/composer_unset.json
COMPOSER_OPTS=$DHOME/composer_opts.json


##
#   Deployment.
##
echo "\nClean up application root folder ($M2_ROOT)..."
if [ -d "$M2_ROOT" ]
then
    rm -fr $M2_ROOT
    mkdir -p $M2_ROOT
else
    mkdir -p $M2_ROOT
fi
cd $M2_ROOT


echo "\nCreate M2 CE project in '$M2_ROOT' using 'composer install'..."
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition $M2_ROOT


echo "\nAdd pre-deploy dependencies to main 'composer.json'..."
composer require flancer32/php_data_object:dev-master


echo "\nFilter original '$COMPOSER_MAIN' on '$COMPOSER_UNSET' set and populate with additional options from '$COMPOSER_OPTS'..."
php $DIR/deploy/merge_json.php $COMPOSER_MAIN $COMPOSER_UNSET $COMPOSER_OPTS


echo "\nUpdate M2 CE project with additional options..."
composer update

