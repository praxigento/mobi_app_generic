#!/usr/bin/env bash
## *************************************************************************
#   Magento 2 deployment script.
## *************************************************************************

## =========================================================================
#   Working variables and hardcoded configuration.
## =========================================================================

# pin current folder and deployment root folder
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )" && pwd )"

# Available deployment modes
MODE_WORK=work
MODE_PILOT=pilot
MODE_LIVE=live

# parse runtime args and validate current deployment mode (work|pilot|live)
MODE=${MODE_WORK}
case "$1" in
    ${MODE_PILOT}|${MODE_LIVE})
        MODE=$1;;
esac

# Folders shortcuts
DIR_SRC=${DIR_ROOT}/src             # folder with sources
DIR_DEPLOY=${DIR_ROOT}/deploy       # folder with deployment templates
DIR_MAGE=${DIR_ROOT}/${MODE}        # root folder for Magento application
DIR_BIN=${DIR_ROOT}/bin             # root folder for shell scripts

# check configuration file exists and load deployment config (db connection, Magento installation opts, etc.).
FILE_CFG=${DIR_ROOT}/deploy.cfg.${MODE}.sh
if [ -f "${FILE_CFG}" ]
then
    echo "There is deployment configuration in ${FILE_CFG}."
    . ${FILE_CFG}
else
    echo "There is no expected configuration in ${FILE_CFG}. Aborting..."
    cd ${DIR_CUR}
    exit
fi
echo "Deployment is started in the '${MODE}' mode."

echo ""
echo "Generate JSON config for templates processing..."
cat << EOF > ${DIR_ROOT}/templates.vars.${MODE}.json
{
  "vars": {
    "CFG_DIR_MAGE": "${DIR_MAGE}",
    "CFG_ADMIN_FIRSTNAME": "${ADMIN_FIRSTNAME}",
    "CFG_ADMIN_LASTNAME": "${ADMIN_LASTNAME}",
    "CFG_ADMIN_EMAIL": "${ADMIN_EMAIL}",
    "CFG_ADMIN_USER": "${ADMIN_USER}",
    "CFG_ADMIN_PASSWORD": "${ADMIN_PASSWORD}",
    "CFG_BASE_URL": "${BASE_URL}",
    "CFG_BACKEND_FRONTNAME": "${BACKEND_FRONTNAME}",
    "CFG_SECURE_KEY": "${SECURE_KEY}",
    "CFG_DB_HOST": "${DB_HOST}",
    "CFG_DB_NAME": "${DB_NAME}",
    "CFG_DB_USER": "${DB_USER}",
    "CFG_DB_PASSWORD": "${DB_PASS}",
    "CFG_DB_PREFIX": "${DB_PREFIX}",
    "CFG_LANGUAGE": "${LANGUAGE}",
    "CFG_CURRENCY": "${CURRENCY}",
    "CFG_TIMEZONE": "${TIMEZONE}",
    "CFG_USE_REWRITES": "${USE_REWRITES}",
    "CFG_USE_SECURE": "${USE_SECURE}",
    "CFG_USE_SECURE_ADMIN": "${USE_SECURE_ADMIN}",
    "CFG_ADMIN_USE_SECURITY_KEY": "${ADMI_USE_SECURITY_KEY}",
    "CFG_SESSION_SAVE": "${SESSION_SAVE}"
  }
}
EOF



## =========================================================================
#   Magento application deployment.
## =========================================================================

# (re)create root folder for application deployment
if [ -d "${DIR_MAGE}" ]
then
    if [ ${MODE} != ${MODE_LIVE} ]
    then
        echo "Re-create '${DIR_MAGE}' folder."
        rm -fr ${DIR_MAGE}    # remove Magento root folder
        mkdir -p ${DIR_MAGE}  # ... then create it
    fi
else
    mkdir -p ${DIR_MAGE}      # just create folder if not exist (live mode)
fi
echo "Magento will be installed into the '${DIR_MAGE}' folder."


#   Create shortcuts for deployment files.
COMPOSER_MAIN=${DIR_MAGE}/composer.json                         # original Magento 2 descriptor
COMPOSER_UNSET=${DIR_DEPLOY}/composer/unset.${MODE_WORK}.json   # options to unset from original descriptor
COMPOSER_OPTS=${DIR_DEPLOY}/composer/opts.${MODE_WORK}.json     # options to set to original descriptor
case "${MODE}" in
    ${MODE_PILOT}|${MODE_LIVE})
        COMPOSER_UNSET=${DIR_DEPLOY}/composer/unset.${MODE_LIVE}.json
        COMPOSER_OPTS=${DIR_DEPLOY}/composer/opts.${MODE_LIVE}.json ;;
esac

echo ""
echo "Create M2 CE project in '${DIR_MAGE}' using composer..."
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=^2 ${DIR_MAGE}

echo ""
echo "Merge original"
echo "    '${COMPOSER_MAIN}' with"
echo "    '${COMPOSER_UNSET}' and"
echo "    '${COMPOSER_OPTS}'..."
php ${DIR_DEPLOY}/merge_json.php ${COMPOSER_MAIN} ${COMPOSER_UNSET} ${COMPOSER_OPTS}

echo ""
echo "Update M2 CE project with additional options..."
cd ${DIR_MAGE}
composer update

echo ""
echo "Create scripts from templates..."
composer status

## MOBI-522
echo ""
echo "Replace wrong Magento files by own versions:"
cp -f ${DIR_DEPLOY}/mage/magento/module-catalog-search/etc/di.xml ${DIR_MAGE}/vendor/magento/module-catalog-search/etc/di.xml
echo "    ${DIR_MAGE}/vendor/magento/module-catalog-search/etc/di.xml is replaced;"

## MOBI-524
echo ""
echo "Link automated tests folder to the web root..."
cd ${DIR_MAGE}
ln -s ${DIR_ROOT}/test/integration theater

## TODO: add timestamp mark to web root

# Finalize job
echo ""
echo "Deployment is done. Launch post-installation script:"
echo "    sh ./bin/post_install.sh"
cd ${CUR_DIR}