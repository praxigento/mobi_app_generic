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