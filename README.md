# Generic MOBI application for Magento v2

[![Build Status](https://travis-ci.org/praxigento/mobi_app_generic_mage2.svg)](https://travis-ci.org/praxigento/mobi_app_generic_mage2/)

## Files

* [./cfg](./cfg) - templates for scripts that are used in the deployment process;
* [./live](./live) - live (production) instance of the application (with own composer.json);
* [./src](./src) - source files for the main module of the application;
* [./test](./test) - test scripts for the own code of the application's main module;
* [./work](./work) - development instance of the application (with own composer.json);
* [./.travis.yml](./.travis.yml) - descriptor for Travis CI; 
* [./composer.json](./composer.json) - descriptor for the main module of the application (type: "magento2-module");
* *./templates.vars.live.json* - configuration variables for templates (live version); is not under version control; 
* *./templates.vars.work.json* - configuration variables for templates (development version); is not under version control;



## Installation

### Development / Test

    $ git clone git@github.com:praxigento/mobi_app_generic_mage2.git mobi2_test.20160520
    $ cd mobi2_test.20160520
    $ cp ../mobi2_test/deploy_cfg.sh .
    $ cp ../mobi2_test/templates.vars.work.json .
    $ nano templates.vars.work.json

Change path to the instance (LOCAL_ROOT).

    {
      "vars": {
        "LOCAL_ROOT": "/home/magento/instance/mobi2_test.20160520/work",
        "DEPLOYMENT_TYPE": "development",
        "LOCAL_OWNER": "magento",
        "LOCAL_GROUP": "apache",
        "CFG_ADMIN_FIRSTNAME": "Store",
        "CFG_ADMIN_LASTNAME": "Admin",
        "CFG_ADMIN_EMAIL": "admin@store.com",
        "CFG_ADMIN_USER": "admin",
        "CFG_ADMIN_PASSWORD": "...",
        "CFG_BASE_URL": "http://mobi2.mage.test.prxgt.com/",
        "CFG_BACKEND_FRONTNAME": "admin",
        "CFG_DB_HOST": "localhost",
        "CFG_DB_NAME": "mage_mobi2_test",
        "CFG_DB_USER": "mage_mobi2_test",
        "CFG_DB_PASSWORD": "...",
        "CFG_DB_PREFIX": "",
        "CFG_LANGUAGE": "en_US",
        "CFG_CURRENCY": "USD",
        "CFG_TIMEZONE": "UTC",
        "CFG_USE_REWRITES": "0",
        "CFG_USE_SECURE": "0",
        "CFG_USE_SECURE_ADMIN": "0",
        "CFG_ADMIN_USE_SECURITY_KEY": "0",
        "CFG_SESSION_SAVE": "db"
      }
    }

Start deploy and post-installation routines:

    $ sh ./deploy.sh
    $ sh ./bin/post_install.sh

Re-link root folder to switch web server to the new instance:

    $ cd ../
    $ unlink mobi2_test ; ln -s ./mobi2_test.20160520 mobi2_test ;

Fix admin authentication [bug](https://github.com/magento/magento2/issues/4386):

    $ cd mobi2_test/work/
    $ nano ./vendor/magento/framework/Translate/Inline/Proxy.php
    ...
    public function __sleep()
    {
        return ['_subject', '_isShared']; // remove '_' before properties' names
    }
    ...


## Code coverage

    $ phpdbg -qrr ./work/vendor/bin/phpunit --configuration ./dev/coverage/phpunit.dist.xml

Report is placed in `build/coverage/index.html`