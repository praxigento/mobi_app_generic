#!/bin/sh
##
#   Setup Magento instance after install with Composer.
#   (all placeholders ${CFG_...} should be replaced by real values from "/templates.vars.work.json" file)
##

# pin current folder and deployment root folder
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$(dirname $( dirname "$0" ))" && pwd )"    # 2 levels up from scripts dir

# current mode is 'work'
MODE=work

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
echo "Post install routines are started in the '${MODE}' mode."

# Folders shortcuts
DIR_SRC=${DIR_ROOT}/src             # folder with sources
DIR_DEPLOY=${DIR_ROOT}/deploy       # folder with deployment templates
DIR_MAGE=${DIR_ROOT}/${MODE}        # root folder for Magento application
DIR_BIN=${DIR_ROOT}/bin             # root folder for shell scripts


# DB connection params
DB_HOST=${CFG_DB_HOST}
DB_NAME=${CFG_DB_NAME}
DB_USER=${CFG_DB_USER}
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
echo "Switch Magento 2 into 'production' mode..."
php ${DIR_MAGE}/bin/magento deploy:mode:set production
#echo "Enable Magento 2 cache..."
#php ${DIR_MAGE}/bin/magento cache:enable


echo ""
echo "Initial data: USERS."
php ${DIR_MAGE}/bin/magento prxgt:app:init-users
echo "Init development data: CUSTOMERS."
php ${DIR_MAGE}/bin/magento prxgt:app:init-customers
echo "Init development data: STOCKS."
php ${DIR_MAGE}/bin/magento prxgt:app:init-stocks
echo "Init development data: replicate Odoo products."
php ${DIR_MAGE}/bin/magento prxgt:odoo:replicate-products


echo ""
echo "Run Magento 2 cron..."
php ${DIR_MAGE}/bin/magento cron:run
echo "Run Magento 2 re-index."
php ${DIR_MAGE}/bin/magento indexer:reindex

echo ""
echo "Set file system ownership and permissions."
mkdir -p ${DIR_MAGE}/var/cache
mkdir -p ${DIR_MAGE}/var/generation
chown -R ${LOCAL_OWNER}:${LOCAL_GROUP} ${DIR_MAGE}
find ${DIR_MAGE} -type d -exec chmod 770 {} \;
find ${DIR_MAGE} -type f -exec chmod 660 {} \;
chmod -R g+w ${DIR_MAGE}/var
chmod -R g+w ${DIR_MAGE}/pub
chmod u+x ${DIR_MAGE}/bin/magento
chmod -R go-w ${DIR_MAGE}/app/etc


##
echo "Post installation setup is done."
##