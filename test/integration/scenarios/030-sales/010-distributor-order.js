'use strict'
/* globals: casper, mobi */

var dump = require('utils').dump;

var conf = mobi.opts.conf;
var scenario = '030';
var scene = '010';
var desc = 'scene ' + scenario + '/' + scene + ': distributor simple order (1 product, check payment):';
var pathScreens = mobi.opts.path.screenshots;

casper.test.begin(desc, function suite_030_010(test) {

        /** Define authentication options and authenticate*/
        var opts = {
            scenario: scenario,
            scene: scene,
            store: conf.app.store.baltic,
            currency: conf.app.currency.eur
        }
        mobi.sub.front.authenticate(test, opts);

        /**
         * Go to category and add product to the cart.
         */
        casper.then(function () {

            var url = mobi.getNavigationUrl('front.catalog.category', 'mage');
            casper.open(url);

            casper.waitForSelector('#page-title-heading', function () {
                test.assert(true, 'Category data is loaded.');
                mobi.capture('040', scene, scenario);
                casper.click("img[alt='Black Walnut (100) San']");
            });

            casper.waitForSelector('.product-info-main', function () {
                test.assert(true, 'Product data is loaded.');
                mobi.capture('050', scene, scenario);
            });

            casper.waitForSelector('#product-addtocart-button', function () {
                casper.click('#product-addtocart-button > span');
                test.assert(true, "'Add to Cart' button is clicked.");
            });

            var css = 'span.counter-number';
            casper.waitFor(function check() {
                var text = casper.fetchText(css);
                var result = ('' != text.trim()); // we don't know initial value of the products in the cart
                return result;
            }, function then() {
                test.assert(true, '... product is added to the shopping cart.');
                mobi.capture('060', scene, scenario);
            });

        });

        /**
         * Go to checkout and place the order.
         */
        casper.then(function () {

            var url = mobi.getNavigationUrl('front.checkout.self', 'mage');
            casper.open(url);

            casper.waitForSelector('div#checkout', function () {
                test.assert(true, 'Checkout page is loaded.');
                mobi.capture('070', scene, scenario);
            });

            var css = '#shipping-method-buttons-container > div > button > span > span';
            casper.waitForSelector(css, function () {
                casper.click(css);
                test.assert(true, 'Shipping is proceed in order placement.');
            });

            casper.waitForSelector('#checkmo', function () {
                test.assert(true, 'Payment methods are loaded.');
                mobi.capture('080', scene, scenario);
            });

            casper.waitForSelector('label[for=checkmo]', function () {
                casper.click('label[for=checkmo]');
                test.assert(true, 'Payment method is selected (checkmo).');
            });

            casper.waitForSelector('button[type=submit]', function () {
                var css = "#checkout-payment-method-load > div > div > div.payment-method._active > div.payment-method-content > div.actions-toolbar > div > button > span";
                casper.click(css);
                test.assert(true, 'Order placement is started.');
                mobi.capture('090', scene, scenario);
            });

            casper.waitForSelector('.checkout-success', function () {
                test.assert(true, 'Order placement is completed.');
                mobi.capture('100', scene, scenario);
            });

        });

        /**
         * Run scenario and finalize test.
         */
        casper.run(function () {
            test.done()
        })
    }
)
