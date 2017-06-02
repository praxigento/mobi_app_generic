#!/usr/bin/env bash
## *************************************************************************
#   MOBI application deploy script.
## *************************************************************************


##
# pin current folder and deployment root folder
##
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )" && pwd )"

# available CLI options and defaults
OPT_DEPLOY_APP="work"         # -d work|pilot|live (MOBI deployment mode)
OPT_DEPLOY_MAGE="developer"   # -r developer|production (M2 runtime mode)
OPT_CLONE_DB=""               # -D (clone database from 'live' instance)
OPT_CLONE_MEDIA=""            # -M (clone media from 'live' instance)
OPT_CLI_INIT=""               # -I (initialize project specific data)
OPT_DCP_INIT=""               # -P (deploy Downline Control Panel)
OPT_CLI_HELP=""               # -h print out help

# Available deployment modes
MODE_WORK=work
MODE_PILOT=pilot
MODE_LIVE=live



## *************************************************************************
#   Parse input options
## *************************************************************************
echo ""
echo "Deployment options:"
while getopts "hd:r:DMIP" OPTNAME
  do
    case "${OPTNAME}" in
      "h")
        OPT_CLI_HELP="yes"
        ;;
      "d")
        OPT_DEPLOY_APP=${OPTARG}
        echo "\tMOBI application deployment mode:\t ${OPT_DEPLOY_APP}"
        ;;
      "r")
        OPT_DEPLOY_MAGE=${OPTARG}
        echo "\tMagento runtime mode:\t\t\t ${OPT_DEPLOY_MAGE}"
        ;;
      "D")
        OPT_CLONE_DB="yes"
        echo "\tDatabase cloning:\t\t\t requested"
        ;;
      "M")
        OPT_CLONE_MEDIA="yes"
        echo "\tMedia cloning:\t\t\t\t requested"
        ;;
      "I")
        OPT_CLI_INIT="yes"
        echo "\tCLI init scripts:\t\t\t requested"
        ;;
      "P")
        OPT_DCP_INIT="yes"
        echo "\tDCP deployment:\t\t\t\t requested"
        ;;
    esac
  done
echo ""



## *************************************************************************
#   Print out help
## *************************************************************************
if [ "${OPT_CLI_HELP}" = "yes" ]
then
    echo "MOBI application deployment script."
    echo ""
    echo "Usage: sh deploy.sh -d [work|pilot|live] -r [developer|production] -I -P -D -M"
    echo ""
    echo "Where:"
    echo "  -d: MOBI application deployment mode ([work|pilot|live], if missed: work);"
    echo "  -r: Magento 2 runtime mode ([developer|production], if missed: developer);"
    echo "  -I: Initialize MOBI app with test data;"
    echo "  -P: Build Downline Control Panel sub-component;"
    echo "  -D: Request database cloning from live version (RESERVED);"
    echo "  -M: Request media files cloning from live version (RESERVED);"
    echo "  -h: This output;"
    exit
fi



## *****************************************************************************************
# Validate configuration
## *****************************************************************************************
MODE_WORK="work"
MODE_PILOT="pilot"
MODE_LIVE="live"

# validate application deployment mode (work|test|pilot|live)
MODE=${MODE_WORK}
case "${OPT_DEPLOY_APP}" in
    ${MODE_PILOT}|${MODE_LIVE})
        MODE=$1;;
esac

# check configuration file exists and load deployment config (db connection, Magento installation opts, etc.).
FILE_CFG=${DIR_ROOT}/cfg.${MODE}.sh
if [ -f "${FILE_CFG}" ]
then
    echo "There is deployment configuration in '${FILE_CFG}'."
    . ${FILE_CFG}
    echo "Deployment configuration is loaded from '${FILE_CFG}'."
else
    echo "There is no expected deployment configuration in '${FILE_CFG}'. Aborting..."
    cd ${DIR_CUR}
    exit 255
fi
echo "Deployment is started in the '${MODE}' mode."



## *****************************************************************************************
# Update sources
## *****************************************************************************************
echo ""
echo "Update project sources from Github..."
cd ${DIR_ROOT}
/usr/bin/git pull



## *****************************************************************************************
# Deploy Magento 2 app and populate it with custom modules
## *****************************************************************************************
echo ""
echo "Deploy Magento..."
cd ${DIR_ROOT}
. ${DIR_ROOT}/deploy/bin/app.sh



## *****************************************************************************************
# Deploy DCP if requested
## *****************************************************************************************
if [ "${OPT_DCP_INIT}" = "yes" ]
then
    echo ""
    echo "Deploy Downline Control Panel..."
    . ${DIR_ROOT}/deploy/bin/dcp.sh
fi



## *****************************************************************************************
# Initialize application data
## *****************************************************************************************
if [ "${OPT_CLI_INIT}" = "yes" ]
then
    echo ""
    echo "Init application data..."
    . ${DIR_ROOT}/deploy/bin/init.sh
fi



## *****************************************************************************************
# Update Magento DB and perform service routines (mode, cache, indexer, cron)
## *****************************************************************************************
echo ""
echo "Magento runtime mode: ${OPT_DEPLOY_MAGE}."
/usr/bin/php ${DIR_ROOT}/${MODE}/bin/magento setup:upgrade
/usr/bin/php ${DIR_ROOT}/${MODE}/bin/magento deploy:mode:set ${OPT_DEPLOY_MAGE}
if [ "${OPT_DEPLOY_MAGE}" = "production" ] && [ ${MODE} = "live" ]
then
    echo ""
    echo "Start JS/CSS minification..."
#    /usr/bin/php ${DIR_ROOT}/${OPT_MODE}/bin/magento fl32:minify:make
fi
if [ "${OPT_DEPLOY_MAGE}" = "developer" ]
then
    echo ""
    echo "Compile DI & deploy static content..."
    /usr/bin/php ${DIR_ROOT}/${OPT_MODE}/bin/magento cache:disable
    /usr/bin/php ${DIR_ROOT}/${OPT_MODE}/bin/magento setup:di:compile
    /usr/bin/php ${DIR_ROOT}/${OPT_MODE}/bin/magento setup:static-content:deploy
fi



## *****************************************************************************************
# Setup files permissions
## *****************************************************************************************
echo ""
echo "Setup filesystem permission..."
. ${DIR_ROOT}/deploy/bin/permissions.sh



## *****************************************************************************************
# Finalize job
## *****************************************************************************************
echo ""
echo "Add file with timestamp mark into the web root..."
CURRENT_TIMESTAMP=`date +%Y%m%d-%H%M%S`
cat << EOF > ${DIR_ROOT}/${MODE}/date_deployed.txt
${CURRENT_TIMESTAMP}
EOF

echo "Auto deployment is done."
cd ${CUR_DIR}