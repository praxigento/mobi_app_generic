#!/usr/bin/env bash
## *************************************************************************
#   Magento 2 auto deployment by cron.
## *************************************************************************


# pin current folder and deployment root folder
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )" && pwd )"

echo ""
echo "Go to test root and remove screenshots folder"
cd ${DIR_ROOT}/work/theater
rm -fr ./screen/
echo "... then run all tests"
/usr/bin/npm test

# Finalize job
echo ""
echo "Auto test is done."
cd ${CUR_DIR}