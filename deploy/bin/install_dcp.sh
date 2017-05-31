#!/usr/bin/env bash
## *************************************************************************
#   DCP component installation.
## *************************************************************************

## =========================================================================
#   Working variables and hardcoded configuration.
## =========================================================================

# pin current folder and deployment root folder
DIR_CUR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )/../../" && pwd )"    # 2 levels up from current dir

MODE_LIVE="live"
MODE_PILOT="pilot"
MODE_TEST="test"
MODE_WORK="work"

# parse runtime args and validate current deployment mode (work|test|pilot|live)
MODE=$1
case "${MODE}" in
    ${MODE_WORK}|${MODE_TEST}|${MODE_PILOT}|${MODE_LIVE})
        # this is expected deployment mode
        ;;
    *)
        echo "Un-expected deployment mode DCP installation: ${MODE}. Exiting."
        exit 666
        ;;
esac


# Folders shortcuts
DIR_MAGE=${DIR_ROOT}/${MODE}                # root folder for Magento application
DIR_DCP=${DIR_ROOT}/dcp                     # root folder for DCP


##
#   Clone DCP sources from github and build
##
unlink ${DIR_MAGE}/dcp
rm -fr ${DIR_DCP}
git clone git@github.com:praxigento/mage_ext_dcp.git ${DIR_DCP}
cd ${DIR_DCP}
git fetch
git checkout new
npm install
#npm run build
npm run build-without-minification

##
#   Copy hashed versions of the built files to fixed-name versions.
##
cd ${DIR_DCP}/dist
cp inline.*.bundle.js inline.bundle.js
cp main.*.bundle.js main.bundle.js
cp scripts.*.bundle.js scripts.bundle.js
cp styles.*.bundle.css styles.bundle.css
cp vendor.*.bundle.js vendor.bundle.js

##
#   Link built DCP into Magento application.
##
ln -s ${DIR_DCP}/dist ${DIR_MAGE}/dcp