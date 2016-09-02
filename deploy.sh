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
echo "Clean up application's root folder ($M2_ROOT)..."
if [ -d "$M2_ROOT" ]
then
    rm -fr $M2_ROOT
    mkdir -p $M2_ROOT
else
    mkdir -p $M2_ROOT
fi
cd $M2_ROOT


echo "Create M2 CE project in '$M2_ROOT' using 'composer install'..."
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=2.1.1 $M2_ROOT


echo "Merge original"
echo "    '$COMPOSER_MAIN' with"
echo "    '$COMPOSER_UNSET' and"
echo "    '$COMPOSER_OPTS'..."
php $DIR/deploy/merge_json.php $COMPOSER_MAIN $COMPOSER_UNSET $COMPOSER_OPTS


echo "Update M2 CE project with additional options..."
composer update