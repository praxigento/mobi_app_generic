#!/usr/bin/env bash
## *************************************************************************
#   Child script to apply patches
#       (env. vars should be set before in ../app.sh script)
## *************************************************************************
if [ -z "${MODE}" ]; then
    echo "Variable MODE should be set in the parent script. Exit."
    exit 255
fi



## =========================================================================
#   Patches
## =========================================================================
echo ""
echo "Patching Magento 2 files..."
patch ${DIR_MAGE}/app/code/Magento/CatalogSearch/etc/di.xml ${DIR_DEPLOY}/patch/PR-7696.patch
patch ${DIR_MAGE}/app/bootstrap.php ${DIR_DEPLOY}/patch/PR-10245.patch

echo "All patches are applied."
echo ""