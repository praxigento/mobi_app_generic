'use strict'
/* globals: casper, mobi */

var dump = require('utils').dump;

var conf = mobi.opts.conf;
var scenario = '030';
var scene = '020';
var desc = 'scene ' + scenario + '/' + scene + ': distributor order (3 products, different qty, braintree payment):';
var pathScreens = mobi.opts.path.screenshots;

casper.test.begin(desc, function suite_030_020(test) {

        /** Define authentication options and authenticate*/
        var opts = {
            scenario: scenario,
            scene: scene,
            store: conf.app.store.baltic,
            currency: conf.app.currency.eur
        }
        mobi.sub.front.authenticate(test, opts);

        /**
         * Go to product page and add 'Black Walnut' (215san) to the cart.
         */
        casper.then(function () {

            var url = mobi.getNavigationUrl('front.catalog.product.san215', 'mage');
            casper.open(url);

            /* load product page*/
            casper.waitForSelector('.product-info-main', function () {
                test.assert(true, '0040: Product-215 data is loaded.');
                mobi.capture('040', scene, scenario);
            });

            // TODO: add subfunction to get cart counter value

            /* set qty=3 */
            casper.waitForSelector('#qty', function () {
                casper.fillSelectors('form#product_addtocart_form', {'input#qty': '3'}, false);
                test.assert(true, "0050: Product quantity is set to 3.");
                mobi.capture('050', scene, scenario);
            });

            /* click 'add to cart' button*/
            casper.waitForSelector('#product-addtocart-button', function () {
                casper.click('#product-addtocart-button > span');
                test.assert(true, "0060: 'Add to Cart' button is clicked.");
            });


            var css = 'span.counter-number';
            casper.waitFor(function check() {
                var text = casper.fetchText(css);
                var result = ('' != text.trim()); // we don't know initial value of the products in the cart
                return result;
            }, function then() {
                test.assert(true, '... 01: product is added to the shopping cart.');
                mobi.capture('060', scene, scenario);
            });

        });

        /**
         * Go to product page and add 'Alfalfa' (203san) to the cart.
         */
        casper.then(function () {

            var url = mobi.getNavigationUrl('front.catalog.product.san203', 'mage');
            casper.open(url);

            /* load product page*/
            casper.waitForSelector('.product-info-main', function () {
                test.assert(true, '0070: Product-203 data is loaded.');
                mobi.capture('070', scene, scenario);
            });

            /* set qty=5 */
            casper.waitForSelector('#qty', function () {
                casper.fillSelectors('form#product_addtocart_form', {'input#qty': '5'}, false);
                test.assert(true, "0080: Product quantity is set to 5.");
                mobi.capture('080', scene, scenario);
            });

            /* click 'add to cart' button*/
            casper.waitForSelector('#product-addtocart-button', function () {
                casper.click('#product-addtocart-button > span');
                test.assert(true, "0090: 'Add to Cart' button is clicked.");
            });


            var css = 'span.counter-number';
            casper.waitFor(function check() {
                var text = casper.fetchText(css);
                var result = ('' != text.trim()); // we don't know initial value of the products in the cart
                return result;
            }, function then() {
                test.assert(true, '... 01: product is added to the shopping cart.');
                mobi.capture('090', scene, scenario);
            });

        });

        /**
         * Go to product page and add 'Bee Royal' (212san) to the cart.
         */
        casper.then(function () {

            var url = mobi.getNavigationUrl('front.catalog.product.san212', 'mage');
            casper.open(url);

            /* load product page*/
            casper.waitForSelector('.product-info-main', function () {
                test.assert(true, '0100: Product-212 data is loaded.');
                mobi.capture('100', scene, scenario);
            });

            /* set qty=4 */
            casper.waitForSelector('#qty', function () {
                casper.fillSelectors('form#product_addtocart_form', {'input#qty': '4'}, false);
                test.assert(true, "0110: Product quantity is set to 4.");
                mobi.capture('110', scene, scenario);
            });

            /* click 'add to cart' button*/
            casper.waitForSelector('#product-addtocart-button', function () {
                casper.click('#product-addtocart-button > span');
                test.assert(true, "0120: 'Add to Cart' button is clicked.");
            });


            var css = 'span.counter-number';
            casper.waitFor(function check() {
                var text = casper.fetchText(css);
                var result = ('' != text.trim()); // we don't know initial value of the products in the cart
                return result;
            }, function then() {
                test.assert(true, '... 01: product is added to the shopping cart.');
                mobi.capture('120', scene, scenario);
            });

        });

        /**
         * Go to checkout and place the order.
         */
        casper.then(function () {

            var url = mobi.getNavigationUrl('front.checkout.self', 'mage');
            casper.open(url);

            casper.waitForSelector('div#checkout', function () {
                test.assert(true, '0130: Checkout page is loaded.');
            });

            /* load shipping methods and check shipping fee */
            casper.waitForSelector('#shipping-method-buttons-container', function () {
                test.assert(true, '0140: Shipping methods are loaded.');
                mobi.capture('130', scene, scenario);
                var css = '#checkout-shipping-method-load > table > tbody > tr > td.col.col-price > span.price-including-tax > span > span';
                casper.waitForSelector(css, function () {
                    var text = casper.fetchText(css);
                    test.assertEquals(text.trim(), '€4.28', "... shipping fee is equal to '€4.28'.");
                });
            });

            var css = '#shipping-method-buttons-container > div > button > span > span';
            casper.waitForSelector(css, function () {
                casper.click(css);
                test.assert(true, '0150: Shipping is proceed in order placement.');
            });

            casper.waitForSelector('#braintree', function () {
                test.assert(true, '0160: Payment methods are loaded.');
                mobi.capture('140', scene, scenario);
            });

            /* select braintree method */
            casper.waitForSelector('label[for=checkmo]', function () {
                casper.click('label[for=braintree]');
                test.assert(true, '0170: Payment method is selected (braintree).');
            });

            /* wait untill braintree form will be loaded */
            casper.waitWhileVisible('body > div.loading-mask', function () {

                /* switch to the child frames one by one and fill braintree payment form fields */
                /* (each field on the separate iframe) */
                casper.waitForSelector('iframe#braintree-hosted-field-number', function () {

                    /* credit card number */
                    casper.page.switchToChildFrame('braintree-hosted-field-number');
                    casper.waitForSelector('input#credit-card-number', function () {
                        casper.fillSelectors('body', {'input#credit-card-number': '4111111111111111'}, false);
                        casper.page.switchToParentFrame();

                        /* exp. month */
                        casper.page.switchToChildFrame('braintree-hosted-field-expirationMonth');
                        casper.waitForSelector('input#expiration-month', function () {
                            casper.fillSelectors('body', {'input#expiration-month': '12'}, false);
                            casper.page.switchToParentFrame();

                            /* exp. year */
                            casper.page.switchToChildFrame('braintree-hosted-field-expirationYear');
                            casper.waitForSelector('input#expiration-year', function () {
                                casper.fillSelectors('body', {'input#expiration-year': '21'}, false);
                                casper.page.switchToParentFrame();

                                /* cvv code */
                                casper.page.switchToChildFrame('braintree-hosted-field-cvv');
                                casper.waitForSelector('input#cvv', function () {
                                    casper.fillSelectors('body', {'input#cvv': '321'}, false);
                                    casper.page.switchToParentFrame();
                                });
                            });
                        });
                    });
                });
            });
        });

        casper.then(function () {
            /* submit data and place the order */
            casper.waitForSelector('#checkout-payment-method-load', function () {
                var css = "#checkout-payment-method-load > div > div > div.payment-method._active > div.payment-method-content > div.actions-toolbar > div > button > span";
                casper.click(css);
                test.assert(true, '0180: Order placement is started.');
                mobi.capture('150', scene, scenario);
            });

            /* finalaize order placement */
            casper.waitForSelector('.checkout-success', function () {
                test.assert(true, '0190: Order placement is completed.');
                mobi.capture('160', scene, scenario);
            }, null, 20000);
            mobi.capture('160', scene, scenario);
        });

        /**
         * Run scenario and finalize test.
         */
        casper.run(function () {
            test.done()
        })
    }
)
