# Install Development Environment for Generic MOBI application for Magento v2


## Clone repository

Clone sample module repo from github and go to development environment root folder (`./work/`):

    $ git clone git@github.com:praxigento/mobi_app_generic_mage2.git
    $ cd mobi_app_generic_mage2/work/


## Create instance configuration file
 
Create JSON configuration for your development instance 
(DB parameters, access parameters, [etc](http://devdocs.magento.com/guides/v2.0/install-gde/install/cli/install-cli-install.html#instgde-install-cli-magento)):

    $ cp templates.json.init templates.json
    $ nano templates.json    
    {
      "vars": {
        "LOCAL_ROOT": "/home/magento/instance/mobi_app_generic_mage2/work",
        "DEPLOYMENT_TYPE": "travis|manual",
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
        "CFG_DB_PASSWORD": "JvPESKVSjXvZDrGk2gBe or use 'skip_password'",
        "CFG_LANGUAGE": "en_US",
        "CFG_CURRENCY": "USD",
        "CFG_TIMEZONE": "UTC",
        "CFG_USE_REWRITES": "0",
        "CFG_USE_SECURE": "0",
        "CFG_USE_SECURE_ADMIN": "0",
        "CFG_ADMI_USE_SECURITY_KEY": "0",
        "CFG_SESSION_SAVE": "db"
      }
    }
    
## Composer installation
    
    $ composer install
    

## Additional configuration

Shell script `./work/bin/deploy/post_install.sh` is created from `./work/cfg/bin/deploy/post_install.sh` template on
_post-install-cmd_ and _post-status-cmd_ events (see [praxigento/composer_plugin_templates](https://github.com/praxigento/composer_plugin_templates)).
Configuration parameters for placeholders are taken from `templates.json`:

    $ sh  ./bin/deploy/post_install.sh



## Setup web server

Point your web-server to folder `$LOCAL_ROOT/work/htdocs`.