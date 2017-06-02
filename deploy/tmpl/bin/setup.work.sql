--
--      Change development/test instance parameters.
--

--
-- Store / Configuration defaults
--

-- MOBI options
--    Downline
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '2', path ='praxigento_downline/referrals/root_anonymous';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '4', path ='praxigento_downline/referrals/group_referrals';
--    Odoo
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '${SQL_ODOO_URI}', path ='praxigento_odoo/connect/uri';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '${SQL_ODOO_DB}', path ='praxigento_odoo/connect/database';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '${SQL_ODOO_USER}', path ='praxigento_odoo/connect/user';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '${SQL_ODOO_PASSWORD}', path ='praxigento_odoo/connect/password';
-- General / General
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'Europe/Riga', path ='general/locale/timezone';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'LV', path ='general/country/default';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='general/locale/firstday';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'kgs', path ='general/locale/weight_unit';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'MOBI Test Store', path ='general/store_information/name';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '+37129181801', path ='general/store_information/phone';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '9:00-18:00 (GMT+2)', path ='general/store_information/hours';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'LV-1010', path ='general/store_information/postcode';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'LV', path ='general/store_information/country_id';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '362', path ='general/store_information/region_id';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'Riga', path ='general/store_information/city';
-- General / Web
-- REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='web/url/use_store';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='web/seo/use_rewrites';
-- General / Currency Setup
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'EUR,RUB,USD', path ='currency/options/allow';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'USD', path ='currency/options/base';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'EUR', path ='currency/options/default';
-- Catalog / Inventory
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '100000', path ='cataloginventory/options/stock_threshold_qty';
-- Customers / Customer Configuration
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='customer/create_account/default_group';
-- Salex / Tax (MOBI-336)
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '2', path ='tax/classes/shipping_tax_class';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'origin', path ='tax/calculation/based_on';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/calculation/price_includes_tax';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/calculation/shipping_includes_tax';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/calculation/cross_border_trade_enabled';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='tax/display/type';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='tax/display/shipping';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='tax/cart_display/price';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='tax/cart_display/subtotal';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='tax/cart_display/shipping';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/cart_display/grandtotal';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/cart_display/full_summary';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/cart_display/zero_tax';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='tax/sales_display/price';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='tax/sales_display/subtotal';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '3', path ='tax/sales_display/shipping';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/sales_display/grandtotal';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/sales_display/full_summary';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='tax/sales_display/zero_tax';
-- Sales / Checkout
-- REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '0', path ='checkout/options/guest_checkout';
-- Sales / Shipping Settings
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'Riga', path ='shipping/origin/city';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'LV', path ='shipping/origin/country_id';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'LV-1010', path ='shipping/origin/postcode';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '362', path ='shipping/origin/region_id';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'Ausekla iela 5-28', path ='shipping/origin/street_line1';
-- Sales / Payment Methods (setup Braintree payment gateway)
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='payment/braintree/active';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'sandbox', path ='payment/braintree/environment';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'authorize_capture', path ='payment/braintree/payment_action';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'hrbwv69nr663dxx6', path ='payment/braintree/merchant_id';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = 'praxigento_usd', path ='payment/braintree/merchant_account_id';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '0:2:MNCxt7WoXHyj5pNckGBT7QsW4Rvau5j7:SISqkD6irXdKO1hqJk+YSNZ0QndYJ8HEpGq+XoZ5LtY=', path ='payment/braintree/public_key';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '0:2:Mu2JghxOFGKVkM6RdQOT8BOPWPaSY7rB:UZ+tVA7HvZgGhgdStzAjsr7qaQ6mWsStG8xedKn8mcY=', path ='payment/braintree/private_key';
-- Sales / Payment Methods (Internal Money)
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='payment/praxigento_wallet/active';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='payment/praxigento_wallet/negative_balance_enabled';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='payment/praxigento_wallet/partial_enabled';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '0.80', path ='payment/praxigento_wallet/partial_percent';
-- Advanced / Admin
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '36000', path ='admin/security/session_lifetime';
-- Advanced / Developer
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='dev/template/allow_symlink';
REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '1', path ='dev/log/active';
-- other
-- REPLACE INTO ${CFG_DB_PREFIX}core_config_data SET value = '0', path ='checkout/options/guest_checkout';


-- Add tax rates and rules (MOBI-336)
REPLACE INTO ${CFG_DB_PREFIX}tax_calculation_rate(tax_calculation_rate_id, tax_country_id, tax_region_id, tax_postcode, code, rate) VALUES (3, 'LV', 0, '*', 'LV Tax', 21.0000);
REPLACE INTO ${CFG_DB_PREFIX}tax_calculation_rule(tax_calculation_rule_id, code, priority, `position`, calculate_subtotal) VALUES (1, 'LV Tax', 0, 0, 0);
REPLACE INTO ${CFG_DB_PREFIX}tax_calculation(tax_calculation_id, tax_calculation_rate_id, tax_calculation_rule_id, customer_tax_class_id, product_tax_class_id) VALUES (1, 3, 1, 3, 2);


--
-- Change default database structure.
--

-- MOBI-254
ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock_item ADD UNIQUE INDEX ${CFG_DB_PREFIX}CATALOGINVENTORY_STOCK_ITEM_PRODUCT_ID_STOCK_ID (product_id, stock_id);
ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock_item DROP INDEX ${CFG_DB_PREFIX}CATALOGINVENTORY_STOCK_ITEM_PRODUCT_ID_WEBSITE_ID;

ALTER TABLE ${CFG_DB_PREFIX}cataloginventory_stock
  ADD CONSTRAINT FK_${CFG_DB_PREFIX}cataloginventory_stock_store_website_website_id FOREIGN KEY (website_id)
  REFERENCES ${CFG_DB_PREFIX}store_website(website_id) ON DELETE RESTRICT ON UPDATE RESTRICT;