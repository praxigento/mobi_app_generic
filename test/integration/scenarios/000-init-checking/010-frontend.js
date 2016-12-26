'use strict'
/* globals: casper, mobi */

var dump = require('utils').dump;

var scenario = '000';
var scene = '010';
var desc = 'scene ' + scenario + '/' + scene + ': frontend initial checking:';
var pathScreens = mobi.opts.path.screenshots;

casper.test.begin(desc, 23, function suite_000_010(test) {
        /**
         * Start scene and load catalog page.
         */
        var url = mobi.getNavigationUrl('front.catalog.category', 'mage');
        /* load page */
        casper.start(url, function () {
            mobi.setViewport();
            test.assertExists('div.page-wrapper', 'Catalog page is loaded.');
            mobi.capture('010', scene, scenario);
        });

        /**
         * Load catalog page and validate Baltic store: 4 products, 2 locales and 2 currencies.
         */
        casper.then(function () {

            /* verify checklist */
            casper.then(function () {
                // current store is Baltic
                casper.waitForSelector('#switcher-store-trigger', function () {
                    var text = casper.fetchText('#switcher-store-trigger > strong > span');
                    test.assertEquals(text.trim(), 'Baltic', '... current store is Baltic.');
                });
                // total 4 items are in category
                casper.waitForSelector('ol.product-items', function () {
                    /* there are 2 elements #toolbar-amount, so use evaluate to get first one */
                    var text = casper.evaluate(function () {
                        var elem = document.querySelector('#toolbar-amount > span');
                        var result = elem.textContent;
                        return result.trim();
                    });
                    test.assertEquals(text.trim(), '4', '... total 4 items are in category.');
                });
                // current language is EN
                casper.waitForSelector('#switcher-language-trigger', function () {
                    var text = casper.fetchText('#switcher-language-trigger > strong > span');
                    test.assertEquals(text.trim(), 'EN', '... current language is EN.');
                });
                // current currency is EUR
                casper.waitForSelector('#switcher-currency-trigger', function () {
                    var text = casper.fetchText('#switcher-currency-trigger > strong > span');
                    test.assertEquals(text.trim(), 'EUR - Euro', '... current currency is EUR.');
                });
            });

            /* click on language switcher */
            casper.then(function () {

                casper.waitForSelector('#switcher-language', function () {
                    casper.click('div#switcher-language-trigger > strong > span');
                });

                var css = '#ui-id-2 > li > a';
                casper.waitFor(function check() {
                    var result = casper.visible(css);
                    return result;
                }, function then() {
                    test.assert(casper.visible(css), 'Language switcher is opened');
                    mobi.capture('020', scene, scenario);
                    var code = casper.fetchText(css);
                    test.assertEquals(code.trim(), 'RU', '... RU language is accessible.');
                });

            });

            /* click on currency switcher */
            casper.then(function () {

                casper.waitForSelector('#switcher-currency', function () {
                    casper.click('#switcher-currency-trigger > strong > span');
                });

                var css = '#ui-id-1 > li > a';
                casper.waitFor(function check() {
                    var result = casper.visible(css);
                    return result;
                }, function then() {
                    test.assert(casper.visible(css), 'Currency switcher is opened');
                    mobi.capture('030', scene, scenario);
                    var code = casper.fetchText(css);
                    test.assertEquals(code.trim(), 'USD - US Dollar', '... USD currency is accessible.');
                });

            });

            /* click on store switcher */
            casper.then(function () {

                casper.waitForSelector('#switcher-store', function () {
                    casper.click('#switcher-store-trigger > strong > span');
                });

                var css = '#switcher-store > div > ul > li > a';
                casper.waitFor(function check() {
                    var result = casper.visible(css);
                    return result;
                }, function then() {
                    test.assert(casper.visible(css), 'Store switcher is opened');
                    mobi.capture('030', scene, scenario);
                    var code = casper.fetchText(css);
                    test.assertEquals(code.trim(), 'Russian', '... Russian store is accessible.');
                });

            });

        });

        /**
         * Switch to Russian store and validate currencies
         */
        casper.then(function () {

            /* switch to Russian store */
            casper.then(function () {

                casper.waitForSelector('#switcher-store', function () {
                    casper.click('#switcher-store > div > ul > li > a');
                });

                casper.waitForSelector('div.page-wrapper', function () {
                    test.assertExists('div.page-wrapper', 'Store is switched.');
                    mobi.capture('040', scene, scenario);
                });
            });

            /* verify checklist */
            casper.then(function () {
                // current store is Russian
                casper.waitForSelector('#switcher-store-trigger', function () {
                    var text = casper.fetchText('#switcher-store-trigger > strong > span');
                    test.assertEquals(text.trim(), 'Russian', '... current store is Russian.');
                });
                // total 3 items are in category
                casper.waitForSelector('ol.product-items', function () {
                    /* there are 2 elements #toolbar-amount, so use evaluate to get first one */
                    var text = casper.evaluate(function () {
                        var elem = document.querySelector('#toolbar-amount > span');
                        var result = elem.textContent;
                        return result.trim();
                    });
                    test.assertEquals(text.trim(), '3', '... total 3 items are in category.');
                });
                // current currency is USD
                casper.waitForSelector('#switcher-currency-trigger', function () {
                    var text = casper.fetchText('#switcher-currency-trigger > strong > span');
                    test.assertEquals(text.trim(), 'USD - Доллар США', '... current currency is USD.');
                });
            });

            /* click on currency switcher */
            casper.then(function () {

                casper.waitForSelector('#switcher-currency', function () {
                    casper.click('#switcher-currency-trigger > strong > span');
                });

                var css = '#ui-id-1 > li > a';
                casper.waitFor(function check() {
                    var result = casper.visible(css);
                    return result;
                }, function then() {
                    test.assert(casper.visible(css), 'Currency switcher is opened');
                    mobi.capture('050', scene, scenario);
                    var code = casper.fetchText(css);
                    test.assertEquals(code.trim(), 'EUR - Евро', '... EUR currency is accessible.');
                });

            });

        });


        casper.run(function () {
            test.done()
        })
    }
)
