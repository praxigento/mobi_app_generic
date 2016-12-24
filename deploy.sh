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
COMPOSER_MAIN=${M2_ROOT}/composer.json
COMPOSER_UNSET=$DHOME/composer_unset.json
COMPOSER_OPTS=$DHOME/composer_opts.json


##
#   Deployment.
##
echo "Clean up application's root folder (${M2_ROOT})..."
if [ -d "${M2_ROOT}" ]
then
    rm -fr ${M2_ROOT}
    mkdir -p ${M2_ROOT}
else
    mkdir -p ${M2_ROOT}
fi
cd ${M2_ROOT}


echo "Create M2 CE project in '${M2_ROOT}' using 'composer install'..."
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=^2 ${M2_ROOT}


echo "Merge original"
echo "    '$COMPOSER_MAIN' with"
echo "    '$COMPOSER_UNSET' and"
echo "    '$COMPOSER_OPTS'..."
php $DIR/deploy/merge_json.php $COMPOSER_MAIN $COMPOSER_UNSET $COMPOSER_OPTS


echo "Update M2 CE project with additional options..."
composer update

echo "Create scripts from templates..."
composer status

## MOBI-522
echo "Replace wrong Magento files by own versions:"
cp -f ${DIR}/deploy/mage/magento/module-catalog-search/etc/di.xml ${M2_ROOT}/vendor/magento/module-catalog-search/etc/di.xml
echo "    ${M2_ROOT}/vendor/magento/module-catalog-search/etc/di.xml is replaced;"

## MOBI-524
cd ${M2_ROOT}
ln -s ${DIR}/test/integration theater

# Finalize job
echo "Deployment is done. Launch post-installation script:"
echo "    sh ./bin/post_install.sh"
cd $CUR_DIR