#!/bin/sh
##
#   Code coverage generation from Cron
#
#   (all placeholders ${...} should be replaced by real values from 'templates.vars.work.json' file
#    see node 'extra/praxigento_templates_config' in project's 'composer.json')
##

cd ${LOCAL_ROOT}

git pull
composer update
php ${LOCAL_ROOT}/vendor/bin/phpunit --configuration ${LOCAL_ROOT}/test/coverage/phpunit.dist.xml
