# Install Live/Pilot Environment for Generic MOBI application for Magento v2


## Clone repository

Clone repo from github and go to live/pilot instances folder (`./live/`):

    $ git clone git@github.com:praxigento/mobi_app_generic_mage2.git
    $ cd mobi_app_generic_mage2/live/



## Create instance configuration file

Create JSON configuration for your live/pilot instance 
(DB parameters, access parameters, 
[etc](http://devdocs.magento.com/guides/v2.0/install-gde/install/cli/install-cli-install.html#instgde-install-cli-magento)):

    $ cp ../templates.vars.json.init ../templates.vars.live.json
    $ nano ../templates.vars.live.json   
    {
      "vars": {
        "LOCAL_ROOT": "/home/magento/instance/mobi_app_generic_mage2/live",
        "DEPLOYMENT_TYPE": "live",
        "LOCAL_OWNER": "magento",
        "LOCAL_GROUP": "apache",
        "CFG_ADMIN_FIRSTNAME": "Store",
        "CFG_ADMIN_LASTNAME": "Admin",
        "CFG_ADMIN_EMAIL": "admin@store.com",
        "CFG_ADMIN_USER": "admin",
        "CFG_ADMIN_PASSWORD": "eUvE7Yid057Cqtq5CkA8",
        "CFG_BASE_URL": "http://mage2.local.host.com/",
        "CFG_BACKEND_FRONTNAME": "admin",
        "CFG_DB_HOST": "localhost",
        "CFG_DB_NAME": "magento2",
        "CFG_DB_USER": "magento2",
        "CFG_DB_PASSWORD": "JvPESKVSjXvZDrGk2gBe",
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


## Deployment

Run composer, install Magento core to `./live/`, link modules into and 
perform post install routines (setup, permissions, create Magento DB, etc.):  
    
    $ composer install
    $ sh  ./bin/post_install.sh

Setup your web server and point it to `./live/`.