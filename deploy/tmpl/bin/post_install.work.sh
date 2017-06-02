#!/bin/sh
##
#   Setup Magento instance for MOBI project after install with Composer.
#   (all placeholders ${CFG_...} should be replaced by real values from "/templates.vars.work.json" file)
##

# pin current folder and deployment root folder
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )/../" && pwd )"    # 1 level up from current dir

MODE_LIVE="live"
MODE_PILOT="pilot"
MODE_WORK="work"

# parse runtime args and validate current deployment mode (work|test|pilot|live)
MODE=$1
case "${MODE}" in
    ${MODE_WORK}|${MODE_PILOT}|${MODE_LIVE})
        # this is expected deployment mode
        ;;
    *)
        echo "Un-expected deployment mode for application: ${MODE}. Exiting."
        exit 255
        ;;
esac
echo "Post install routines are started in the '${MODE}' mode."


# Folders shortcuts
DIR_SRC=${DIR_ROOT}/src             # folder with sources
DIR_DEPLOY=${DIR_ROOT}/deploy       # folder with deployment templates
DIR_MAGE=${DIR_ROOT}/${MODE}        # root folder for Magento application
DIR_BIN=${DIR_ROOT}/bin             # root folder for shell scripts
FILE_CFG=${DIR_ROOT}/config.${MODE}.sh

# DB connection params
DB_HOST="${CFG_DB_HOST}"
DB_NAME="${CFG_DB_NAME}"
DB_USER="${CFG_DB_USER}"
# use 'skip_password' to connect to server w/o password.
DB_PASS=${CFG_DB_PASSWORD}
if [ "${DB_PASS}" = "skip_password" ]; then
    MYSQL_PASS=""
    MAGE_DBPASS=""
else
    MYSQL_PASS="--password=${DB_PASS}"
    MAGE_DBPASS="--db-password=""${DB_PASS}"""
fi
# DB prefix can be empty
DB_PREFIX="${CFG_DB_PREFIX}"
if [ "${DB_PREFIX}" = "" ]; then
    MAGE_DBPREFIX=""
else
    MAGE_DBPREFIX="--db-prefix=${DB_PREFIX}"
fi

##
echo "Restore write access on folder '${DIR_MAGE}/app/etc' for owner when launches are repeated."
##
if [ -d "${DIR_MAGE}/app/etc" ]
then
    chmod -R go+w ${DIR_MAGE}/app/etc
fi


##
echo "Drop/create database ${DB_NAME}."
##
mysqladmin -f -u"${DB_USER}" ${MYSQL_PASS} -h"${DB_HOST}" drop "${DB_NAME}"
mysqladmin -f -u"${DB_USER}" ${MYSQL_PASS} -h"${DB_HOST}" create "${DB_NAME}"


##
echo "(Re)install Magento using database '${DB_NAME}' (connecting as '${DB_USER}')."
##

# Full list of the available options:
# http://devdocs.magento.com/guides/v2.0/install-gde/install/cli/install-cli-install.html#instgde-install-cli-magento

php ${DIR_MAGE}/bin/magento setup:install  \
    --admin-firstname="${CFG_ADMIN_FIRSTNAME}" \
    --admin-lastname="${CFG_ADMIN_LASTNAME}" \
    --admin-email="${CFG_ADMIN_EMAIL}" \
    --admin-user="${CFG_ADMIN_USER}" \
    --admin-password="${CFG_ADMIN_PASSWORD}" \
    --base-url="${CFG_BASE_URL}" \
    --backend-frontname="${CFG_BACKEND_FRONTNAME}" \
    --db-host="${CFG_DB_HOST}" \
    --db-name="${CFG_DB_NAME}" \
    --db-user="${CFG_DB_USER}" \
    --language="${CFG_LANGUAGE}" \
    --currency="${CFG_CURRENCY}" \
    --timezone="${CFG_TIMEZONE}" \
    --use-rewrites="${CFG_USE_REWRITES}" \
    --use-secure="${CFG_USE_SECURE}" \
    --use-secure-admin="${CFG_USE_SECURE_ADMIN}" \
    --admin-use-security-key="${CFG_ADMIN_USE_SECURITY_KEY}" \
    --session-save="${CFG_SESSION_SAVE}" \
    --key="${CFG_SECURE_KEY}" \
    --cleanup-database \
    $MAGE_DBPREFIX \
    $MAGE_DBPASS \

##
echo "Post installation setup for database '${DB_NAME}'."
##
#
mysql --database=${DB_NAME} --host=${DB_HOST} --user=${DB_USER} ${MYSQL_PASS} -e "source ${DIR_BIN}/setup.sql"


echo ""
echo "Upgrade Magento DB structure and data..."
php ${DIR_MAGE}/bin/magento setup:upgrade
echo "Switch Magento 2 into 'developer' mode..."
php ${DIR_MAGE}/bin/magento deploy:mode:set developer
echo "Clean up and disable cache"
php ${DIR_MAGE}/bin/magento cache:disable
php ${DIR_MAGE}/bin/magento cache:clean
echo "Run Magento 2 cron..."
php ${DIR_MAGE}/bin/magento cron:run
echo "Run Magento 2 re-index."
php ${DIR_MAGE}/bin/magento indexer:reindex

##
echo "Post installation setup is done."
##