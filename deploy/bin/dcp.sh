#!/usr/bin/env bash
## *************************************************************************
#   Child script to install DCP sub-component.
#       "MODE" and other env. vars should be set before.
## *************************************************************************
if [ -z "${MODE}" ];
then
    echo "Variable MODE should be set in the parent script. Exit."
    exit 255
fi

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
# git checkout new - deprecated after MOBI-812
npm install

if [ "${SQL_DEV_MODE}" = "0" ]
then
    echo "Build DCP in production mode (minified)."
    npm run build
else
    echo "Build DCP in development mode (linked to Generic Dvlp)."
    # build development version with "http://gen.mage.dvlp.mobi.prxgt.com/rest/default/V1/" as API base
    npm run build-gen-dvlp
    ##
    #   Copy hashed versions of the built files to fixed-name versions.
    ##
    cd ${DIR_DCP}/dist
    cp inline.*.bundle.js inline.bundle.js
    cp main.*.bundle.js main.bundle.js
    cp scripts.*.bundle.js scripts.bundle.js
    cp styles.*.bundle.css styles.bundle.css
    cp vendor.*.bundle.js vendor.bundle.js
fi

# build production version
#npm run build
# build development version with "http://gen.mage.test.mobi.prxgt.com/rest/default/V1/" as API base
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