#!/usr/bin/env bash
## *************************************************************************
#   Child script to deploy MOBI application.
#       "MODE" and other env. vars should be set before.
## *************************************************************************
if [ -z "${MODE}" ];
then
    echo "Variable MODE should be set in the parent script. Exit."
    exit 255
fi



## =========================================================================
#   Working variables and hardcoded configuration for this script.
## =========================================================================

# Folders shortcuts
DIR_SRC=${DIR_ROOT}/src             # folder with sources
DIR_DEPLOY=${DIR_ROOT}/deploy       # folder with deployment templates
DIR_MAGE=${DIR_ROOT}/${MODE}        # root folder for Magento application
DIR_BIN=${DIR_ROOT}/bin             # root folder for shell scripts



## =========================================================================
#   Generate JSON config for composer plugin.
## =========================================================================
FILE_CFG_JSON=${DIR_ROOT}/cfg.tmpl.json
echo ""
echo "Generate JSON config '${FILE_CFG_JSON}' for templates processing (praxigento/composer_plugin_templates)..."
cat << EOF > ${FILE_CFG_JSON}
{
  "vars": {
    "SQL_ODOO_URI": "${SQL_ODOO_URI}",
    "SQL_ODOO_DB": "${SQL_ODOO_DB}",
    "SQL_ODOO_USER": "${SQL_ODOO_USER}",
    "SQL_ODOO_PASSWORD": "${SQL_ODOO_PASSWORD}",
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
    "CFG_ADMIN_USE_SECURITY_KEY": "${ADMIN_USE_SECURITY_KEY}",
    "CFG_SESSION_SAVE": "${SESSION_SAVE}"
  }
}
EOF



## =========================================================================
#   Magento 2 and modules deployment with Composer.
## =========================================================================
. ${DIR_DEPLOY}/bin/app/mage.sh



## =========================================================================
#   Setup database for the application.
## =========================================================================
. ${DIR_DEPLOY}/bin/app/db.sh



## =========================================================================
#   Setup filesystem (create folders and symlinks).
## =========================================================================

# Create folders and copy service files to Magento dir.
echo "Create working folders before permissions will be set."
mkdir -p ${DIR_MAGE}/var/cache
mkdir -p ${DIR_MAGE}/var/generation
mkdir -p ${DIR_MAGE}/var/log


if [ -z "${LOCAL_OWNER}" ] || [ -z "${LOCAL_GROUP}" ] || [ -z "${DIR_MAGE}" ]; then
    echo "Skip file system ownership and permissions setup."
else
    ## http://devdocs.magento.com/guides/v2.0/install-gde/prereq/integrator_install.html#instgde-prereq-compose-access
    echo "\nSet file system ownership (${LOCAL_OWNER}:${LOCAL_GROUP}) and permissions..."
    chown -R $LOCAL_OWNER:$LOCAL_GROUP ${DIR_MAGE}
    chmod -R g+w ${DIR_MAGE}/var
    chmod -R g+w ${DIR_MAGE}/pub
    chmod u+x ${DIR_MAGE}/bin/magento
    chmod -R go-w ${DIR_MAGE}/app/etc
fi



## =========================================================================
#   This application tuning.
## =========================================================================

# Disable log config re-write for live instance if required
echo "Copy '${DIR_BIN}/logging.yaml' to '${DIR_MAGE}/var/log/logging.yaml'."
cp ${DIR_BIN}/logging.yaml ${DIR_MAGE}/var/log/logging.yaml



## =========================================================================
#   Patches
## =========================================================================
. ${DIR_DEPLOY}/bin/app/patches.sh


##
# finalize deployment process
##
echo "Deployment of the MOBI application is completed."