#!/usr/bin/env bash
CUR_DIR="$PWD"
DIR_ROOT="$( cd "$( dirname "$0" )" && pwd )"

php work/bin/magento setup:di:compile

cd ${CUR_DIR}
