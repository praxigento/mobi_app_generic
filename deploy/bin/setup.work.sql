--
--      Change development/test instance parameters.
--
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='dev/template/allow_symlink';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='dev/log/active';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'America/Los_Angeles', path ='general/locale/timezone';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '36000', path ='admin/security/session_lifetime';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '100000', path ='cataloginventory/options/stock_threshold_qty';
-- enable development toolbar
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='mgt_developer_toolbar/module/is_enabled';
-- REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '0', path ='checkout/options/guest_checkout';
-- setup Braintree payment gateway
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='payment/braintree/active';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'sandbox', path ='payment/braintree/environment';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'authorize_capture', path ='payment/braintree/payment_actish ./wo on';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'hrbwv69nr663dxx6', path ='payment/braintree/merchant_id';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '0:2:MNCxt7WoXHyj5pNckGBT7QsW4Rvau5j7:SISqkD6irXdKO1hqJk+YSNZ0QndYJ8HEpGq+XoZ5LtY=', path ='payment/braintree/public_key';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '0:2:Mu2JghxOFGKVkM6RdQOT8BOPWPaSY7rB:UZ+tVA7HvZgGhgdStzAjsr7qaQ6mWsStG8xedKn8mcY=', path ='payment/braintree/private_key';
-- Setup Taxes (MOBI-336)
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/classes/shipping_tax_class', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/calculation/price_includes_tax', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/calculation/shipping_includes_tax', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/calculation/discount_tax', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/calculation/cross_border_trade_enabled', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/notification/ignore_discount', '0');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/notification/ignore_price_display', '0');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/calculation/cross_border_trade_enabled', '0');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/defaults/country', 'LV');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/defaults/postcode', NULL);
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/display/type', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/display/shipping', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/cart_display/price', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/cart_display/subtotal', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/cart_display/shipping', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/cart_display/grandtotal', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/cart_display/full_summary', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/cart_display/zero_tax', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/sales_display/price', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/sales_display/subtotal', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/sales_display/shipping', '2');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/sales_display/grandtotal', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/sales_display/full_summary', '1');
REPLACE INTO ${CFG_DB_PREFIX}core_config_data(scope, scope_id, path, value) VALUES ('default', 0, 'tax/sales_display/zero_tax', '1');

-- Add tax rates and rules (MOBI-336)
REPLACE INTO ${CFG_DB_PREFIX}tax_calculation_rate(tax_calculation_rate_id, tax_country_id, tax_region_id, tax_postcode, code, rate) VALUES (3, 'LV', 0, '*', 'LV Tax', 21.0000);
REPLACE INTO ${CFG_DB_PREFIX}tax_calculation_rule(tax_calculation_rule_id, code, priority, `position`, calculate_subtotal) VALUES (1, 'LV Tax', 0, 0, 0);
REPLACE INTO ${CFG_DB_PREFIX}tax_calculation(tax_calculation_id, tax_calculation_rate_id, tax_calculation_rule_id, customer_tax_class_id, product_tax_class_id) VALUES (1, 3, 1, 3, 2);

-- MOBI-254
ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock_item ADD UNIQUE INDEX CATALOGINVENTORY_STOCK_ITEM_PRODUCT_ID_STOCK_ID (product_id, stock_id);
ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock_item DROP INDEX CATALOGINVENTORY_STOCK_ITEM_PRODUCT_ID_WEBSITE_ID;

ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock
  ADD CONSTRAINT FK_cataloginventory_stock_store_website_website_id FOREIGN KEY (website_id)
  REFERENCES ${CFG_DB_PREFIX}store_website(website_id) ON DELETE RESTRICT ON UPDATE RESTRICT;