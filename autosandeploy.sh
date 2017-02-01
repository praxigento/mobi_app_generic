#!/usr/bin/env bash
## *************************************************************************
#   Magento 2 auto deployment for Santegra migration.
## *************************************************************************


# pin current folder and deployment root folder
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )" && pwd )"

echo ""
echo "Update project sources from Github..."
cd ${DIR_ROOT}
/usr/bin/git pull

echo ""
echo "Start deployment..."
cd ${DIR_ROOT}
/bin/sh deploy.sh

echo ""
echo "Perform post-deployment routines..."
cd ${DIR_ROOT}
/bin/sh ./bin/post_install_santegra.sh

# Finalize job
echo ""
echo "Auto deployment is done."
cd ${CUR_DIR}