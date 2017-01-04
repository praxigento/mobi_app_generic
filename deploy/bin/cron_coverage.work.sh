#!/bin/sh
##
#   Code coverage generation from Cron
#
#   (all placeholders ${...} should be replaced by real values from 'templates.vars.work.json' file
#    see node 'extra/praxigento_templates_config' in project's 'composer.json')
##

cd ${CFG_DIR_MAGE}

git pull
composer update
phpdbg -qrr ${LOCAL_ROOT}/vendor/bin/phpunit --configuration ${CFG_DIR_MAGE}/../dev/coverage/phpunit.dist.xml
