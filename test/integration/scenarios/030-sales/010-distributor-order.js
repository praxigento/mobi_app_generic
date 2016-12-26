'use strict'
/* globals: casper, mobi */

var dump = require('utils').dump;

var scenario = '030';
var scene = '010';
var desc = 'scene ' + scenario + '/' + scene + ': distributor order checking:';
var pathScreens = mobi.opts.path.screenshots;

casper.test.begin(desc, function suite_030_010(test) {
        /**
         * Start scene and go to login form.
         */
        var url = mobi.getNavigationUrl('front.customer.account.login', 'mage');
        /* load page */
        casper.start(url, function () {
            mobi.setViewport();
            test.assertExists('div.page-wrapper', 'Default login form is loaded.');
            mobi.capture('010', scene, scenario);
        });

        /**
         * Fill the login form and authenticate.
         */
        casper.then(function () {

            casper.waitForSelector('#login-form', function () {
                casper.fillSelectors('#login-form', {
                    'input#email': 'customer_10@test.com',
                    'input#pass': 'UserPassword12'
                }, false);
                mobi.capture('020', scene, scenario);
                casper.click('#send2 > span');
                test.assert(true, 'Authentication data is posted.');
            });


            casper.waitForSelector('#maincontent', function () {
                test.assert(true, 'Account dashboard is loaded.');
                mobi.capture('030', scene, scenario);
            });

        });


        /**
         * Go to category and add 2 products to the cart.
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
                test.assert("'Add to Cart' button is clicked.");
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

        casper.run(function () {
            test.done()
        })
    }
)
