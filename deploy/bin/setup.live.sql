--
--      Change live/pilot instance parameters.
--
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'America/Los_Angeles', path ='general/locale/timezone';


--
-- Change default database structure.
--

-- MOBI-254
ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock_item ADD UNIQUE INDEX ${CFG_DB_PREFIX}CATALOGINVENTORY_STOCK_ITEM_PRODUCT_ID_STOCK_ID (product_id, stock_id);
ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock_item DROP INDEX ${CFG_DB_PREFIX}CATALOGINVENTORY_STOCK_ITEM_PRODUCT_ID_WEBSITE_ID;

ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock
  ADD CONSTRAINT FK_${CFG_DB_PREFIX}cataloginventory_stock_store_website_website_id FOREIGN KEY (website_id)
  REFERENCES ${CFG_DB_PREFIX}store_website(website_id) ON DELETE RESTRICT ON UPDATE RESTRICT;