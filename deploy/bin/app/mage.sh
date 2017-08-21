#!/usr/bin/env bash
## *************************************************************************
#   Deploy Magento 2 and application's extensions.
#       (env. vars should be set before in ../app.sh script)
## *************************************************************************
if [ -z "${MODE}" ]; then
    echo "Variable MODE should be set in the parent script. Exit."
    exit 255
fi



## =========================================================================
#   Prepare working folder.
## =========================================================================

# (re)create root folder for application deployment
if [ -d "${DIR_MAGE}" ]
then
    if [ ${MODE} != ${MODE_LIVE} ]
    then
        echo "Re-create '${DIR_MAGE}' folder."
        rm -fr ${DIR_MAGE}    # remove Magento root folder
        mkdir -p ${DIR_MAGE}  # ... then create it
        rm -fr ${DIR_BIN}     # remove bin folder
    fi
else
    mkdir -p ${DIR_MAGE}      # just create folder if not exist
fi
echo "Magento will be installed into the '${DIR_MAGE}' folder."

#   Create shortcuts for deployment files.
COMPOSER_MAIN=${DIR_MAGE}/composer.json
COMPOSER_UNSET=${DIR_DEPLOY}/composer/unset.${MODE_WORK}.json
COMPOSER_OPTS=${DIR_DEPLOY}/composer/opts.${MODE_WORK}.json
case "${MODE}" in
    ${MODE_PILOT}|${MODE_LIVE})
        COMPOSER_UNSET=${DIR_DEPLOY}/composer/unset.${MODE_LIVE}.json
        COMPOSER_OPTS=${DIR_DEPLOY}/composer/opts.${MODE_LIVE}.json ;;
esac



## =========================================================================
#   Deploy Magento 2 itself using Composer.
## =========================================================================
echo ""
echo "Create M2 CE project in '${DIR_MAGE}' using composer"
#composer create-project --repository-url=https://github.com/magento/magento2 magento/magento2ce=dev-developer ${DIR_MAGE}
composer create-project -s dev --prefer-source --keep-vcs --no-install \
    --repository "{\"type\": \"vcs\",\"url\": \"https://github.com/magento/magento2\"}" \
    magento/magento2ce ${DIR_MAGE}
cd ${DIR_MAGE}
git checkout 2.2.0-rc2.1
composer install



## =========================================================================
#   Configure composer.json and deploy application's extensions
#       and other resources.
## =========================================================================
echo "Merge original"
echo "    '${COMPOSER_MAIN}' with"
echo "    '${COMPOSER_UNSET}' and"
echo "    '${COMPOSER_OPTS}'..."
php ${DIR_DEPLOY}/merge_json.php ${COMPOSER_MAIN} ${COMPOSER_UNSET} ${COMPOSER_OPTS}

echo "Update M2 CE project with additional options..."
cd ${DIR_MAGE}
composer update



## =========================================================================
#   Process templates and create new support files using composer plugin.
## =========================================================================
composer status

echo "Magento 2 and application extensions are deployed."