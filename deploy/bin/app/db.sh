#!/usr/bin/env bash
## *************************************************************************
#   Child script to create/setup app DB
#       (env. vars should be set before in ../app.sh script)
## *************************************************************************
if [ -z "${MODE}" ]; then
    echo "Variable MODE should be set in the parent script. Exit."
    exit 255
fi



## =========================================================================
#   Prepare working environment.
## =========================================================================

# Prepare database password for using with Magento and MySQL utils
if [ -z ${DB_PASS} ]; then
    MYSQL_PASS=""
    MAGE_DBPASS=""
else
    MYSQL_PASS="--password=${DB_PASS}"
    MAGE_DBPASS="--db-password=""${DB_PASS}"""
fi

if [ ${MODE} != ${MODE_LIVE} ]
then

    echo "(Re)install Magento using database '${DB_NAME}' (connecting as '${DB_USER}')."
    echo "Drop-create db '${DB_NAME}'"
    mysqladmin -f -u"${DB_USER}" ${MYSQL_PASS} -h"${DB_HOST}" drop "${DB_NAME}"
    mysqladmin -f -u"${DB_USER}" ${MYSQL_PASS} -h"${DB_HOST}" create "${DB_NAME}"

    # Full list of the available options:
    # http://devdocs.magento.com/guides/v2.0/install-gde/install/cli/install-cli-install.html#instgde-install-cli-magento
    php ${DIR_MAGE}/bin/magento setup:install  \
    --cleanup-database \
    --admin-firstname="${ADMIN_FIRSTNAME}" \
    --admin-lastname="${ADMIN_LASTNAME}" \
    --admin-email="${ADMIN_EMAIL}" \
    --admin-user="${ADMIN_USER}" \
    --admin-password="${ADMIN_PASSWORD}" \
    --base-url="${BASE_URL}" \
    --backend-frontname="${BACKEND_FRONTNAME}" \
    --key="${SECURE_KEY}" \
    --language="${LANGUAGE}" \
    --currency="${CURRENCY}" \
    --timezone="${TIMEZONE}" \
    --use-rewrites="${USE_REWRITES}" \
    --use-secure="${USE_SECURE}" \
    --use-secure-admin="${USE_SECURE_ADMIN}" \
    --admin-use-security-key="${ADMI_USE_SECURITY_KEY}" \
    --session-save="${SESSION_SAVE}" \
    --db-host="${DB_HOST}" \
    --db-name="${DB_NAME}" \
    --db-user="${DB_USER}" \
    ${MAGE_DBPASS} \
    # 'MAGE_DBPASS' should be placed on the last position to prevent failures if this var is empty.
else
    echo "Setup Magento to use existing DB (${DB_NAME}@${DB_HOST} as ${DB_USER})."
    php ${DIR_MAGE}/bin/magento setup:install  \
    --admin-firstname="${ADMIN_FIRSTNAME}" \
    --admin-lastname="${ADMIN_LASTNAME}" \
    --admin-email="${ADMIN_EMAIL}" \
    --admin-user="${ADMIN_USER}" \
    --admin-password="${ADMIN_PASSWORD}" \
    --backend-frontname="${BACKEND_FRONTNAME}" \
    --key="${SECURE_KEY}" \
    --session-save="${SESSION_SAVE}" \
    --db-host="${DB_HOST}" \
    --db-name="${DB_NAME}" \
    --db-user="${DB_USER}" \
    ${MAGE_DBPASS} \
    # 'MAGE_DBPASS' should be placed on the last position to prevent failures if this var is empty.
fi


echo "Database configuration is completed."