#!/usr/bin/env bash
## *************************************************************************
#   Version of the MOBI app to be deployed by developer without DCP.
## *************************************************************************

# pin current folder and deployment root folder
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )" && pwd )"

echo "Start deployment of the MOBI app test version."
echo ""
cd ${DIR_ROOT}
/bin/sh deploy.sh -d work -r developer -I 

# Finalize job
echo ""
echo "Deployment of the MOBI app test version is done."
cd ${CUR_DIR}
