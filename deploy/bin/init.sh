#!/usr/bin/env bash
## *************************************************************************
#   Child script to initialize test data for MOBI application.
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
DIR_MAGE=${DIR_ROOT}/${MODE}        # root folder for Magento application



## =========================================================================
#   Generate JSON config for composer plugin.
## =========================================================================
echo ""
echo "Init additional admin users."
php ${DIR_MAGE}/bin/magento prxgt:app:init:users
echo "Init customers groups."
php ${DIR_MAGE}/bin/magento prxgt:app:init:groups
echo "Init downline tree."
php ${DIR_MAGE}/bin/magento prxgt:app:init:customers
echo "Init stores & stocks."
php ${DIR_MAGE}/bin/magento prxgt:app:init:stocks
echo "Init sale taxes."
php ${DIR_MAGE}/bin/magento prxgt:app:init:taxes
echo "Replicate Odoo products."
php ${DIR_MAGE}/bin/magento prxgt:odoo:replicate:products
echo "Post-replication routines."
php ${DIR_MAGE}/bin/magento prxgt:odoo:replicate:post
echo "Calculate downline snapshots."
php ${DIR_MAGE}/bin/magento prxgt:downline:snaps


##
# finalize deployment process
##
echo "Test data initialization is completed."