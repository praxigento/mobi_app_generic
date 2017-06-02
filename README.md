# Generic MOBI application for Magento v2

[![Build Status](https://travis-ci.org/praxigento/mobi_app_generic_mage2.svg)](https://travis-ci.org/praxigento/mobi_app_generic_mage2/)

## Files

* [./deploy](./deploy) - scripts that are used in the deployment process;
* [./src](./src) - source files for the main module of the application;
* [./test](./test) - test scripts for the own code of the application's main module;
* [./.travis.yml](./.travis.yml) - descriptor for Travis CI; 
* [./autodeploy.sh](./autodeploy.sh) - script to refresh deployment by cron; 
* [./cfg.init.sh](./cfg.init.sh) - template for deployment configuration;
* [./composer.json](./composer.json) - descriptor for the main module of the application (type: "magento2-module");
* [./deploy.sh](./deploy.sh) - deployment script;



## Installation

### Development / Test

    $ git clone git@github.com:praxigento/mobi_app_generic_mage2.git mobi.test_20160520
    $ cd mobi.test_20160520
    
Copy existing configuration from previous version:
    
    $ cp ../mobi.test/cfg.work.sh .
    
... or new one from initial template:
 
    $ cp ./cfg.init.sh cfg.work.sh

Then edit deployment configuration: 

    $ nano cfg.work.sh

Change owners and Magento 2 deployment options:

    #!/usr/bin/env bash
    
    # The owner of the Magento file system:
    #   * Must have full control (read/write/execute) of all files and directories.
    #   * Must not be the web server user; it should be a different user.
    # Web server:
    #   * must be a member of the '${LOCAL_GROUP}' group.
    LOCAL_OWNER="owner"
    LOCAL_GROUP="www-data"
    
    # SQL update options
    SQL_ODOO_URI=
    SQL_ODOO_DB=
    SQL_ODOO_USER=
    SQL_ODOO_PASSWORD=
    
    # Magento 2 installation configuration
    # see http://devdocs.magento.com/guides/v2.0/install-gde/install/cli/install-cli-install.html#instgde-install-cli-magento
    ADMIN_FIRSTNAME="Store"
    ADMIN_LASTNAME="Admin"
    ADMIN_EMAIL="admin@store.com"
    ADMIN_USER="admin"
    ADMIN_PASSWORD="..."
    BASE_URL="http://mage2.host.org:8080/"
    BACKEND_FRONTNAME="admin"
    SECURE_KEY="..."
    DB_HOST="localhost"
    DB_NAME="mage2"
    DB_USER="www"
    DB_PASS="..."
    DB_PREFIX=""
    LANGUAGE="en_US"
    CURRENCY="USD"
    TIMEZONE="UTC"
    USE_REWRITES="0"
    USE_SECURE="0"
    USE_SECURE_ADMIN="0"
    ADMI_USE_SECURITY_KEY="0"
    SESSION_SAVE="db"

Start deploy and post-installation routines:

    $ sh ./deploy.sh -d work -r production -I -P

Re-link root folder to switch web server to the new instance:

    $ cd ../
    $ unlink mobi.test ; ln -s ./mobi.test_20160520 mobi.test ;