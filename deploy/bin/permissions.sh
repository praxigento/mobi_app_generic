#!/usr/bin/env bash
## *************************************************************************
#   Child script to setup permissions to filesystem.
#       "MODE" and other env. vars should be set before.
## *************************************************************************
if [ -z "${MODE}" ];
then
    echo "Variable MODE should be set in the parent script. Exit."
    exit 255
fi



## =========================================================================
#   Perform maintenance routines.
## =========================================================================
echo ""
echo "Run Magento 2 cron..."
php ${DIR_MAGE}/bin/magento cron:run
echo "Run Magento 2 re-index."
php ${DIR_MAGE}/bin/magento indexer:reindex

echo ""
echo "Set file system ownership and permissions."
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
# find ${DIR_MAGE} -type d -exec chmod 770 {} \;
# find ${DIR_MAGE} -type f -exec chmod 660 {} \;
# TMP: add execute permission on local instance
chmod ug+x ${DIR_MAGE}/vendor/phpmd/phpmd/src/bin/phpmd