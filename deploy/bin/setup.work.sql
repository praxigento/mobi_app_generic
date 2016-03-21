--
--      Change development/test instance parameters.
--
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='dev/template/allow_symlink';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='dev/log/active';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'America/Los_Angeles', path ='general/locale/timezone';

-- MOBI-254
ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock_item ADD UNIQUE INDEX CATALOGINVENTORY_STOCK_ITEM_PRODUCT_ID_STOCK_ID (product_id, stock_id);
ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock_item DROP INDEX CATALOGINVENTORY_STOCK_ITEM_PRODUCT_ID_WEBSITE_ID;
