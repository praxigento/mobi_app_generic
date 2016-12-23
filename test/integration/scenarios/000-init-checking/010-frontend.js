'use strict'
/* globals: casper, mobi */

var dump = require('utils').dump;

var scenario = '000';
var scene = '010';
var desc = 'scene ' + scenario + '/' + scene + ': frontend initial checking:';
var pathScreens = mobi.opts.path.screenshots;

casper.test.begin(desc, function suite_000_010(test) {

        /**
         * Load catalog page.
         */
        var url = mobi.getNavigationUrl('catalog.category');
        casper.echo('URL: ' + url);

        casper.start(url, function () {
            mobi.setViewport();
            test.assertExists('div.page-wrapper', 'catalog page is loaded.');
            mobi.capture('010', scene, scenario); // initial screenshot
        })

        /**
         * Validate Baltic store: 4 products, 2 locales and 2 currencies.
         */
        casper.then(function () {

            // current store is Baltic
            casper.waitForSelector('#switcher-store-trigger', function () {
                var text = casper.fetchText('#switcher-store-trigger > strong > span');
                test.assertEquals(text.trim(), 'Baltic', 'current store is Baltic.');
            });
            // total 4 items are in category
            casper.waitForSelector('#toolbar-amount', function () {
                var text = casper.fetchText('#toolbar-amount');
                casper.echo("TOTAL: (" + text.trim()+")");
                test.assertEquals(text.trim(), '4 items', 'total 4 items are in category.');
            });
            // current language is EN
            casper.waitForSelector('#switcher-language-trigger', function () {
                var text = casper.fetchText('#switcher-language-trigger > strong > span');
                test.assertEquals(text.trim(), 'EN', 'current language is EN.');
            });
            // current currency is EUR
            casper.waitForSelector('#switcher-currency-trigger', function () {
                var text = casper.fetchText('#switcher-currency-trigger > strong > span');
                test.assertEquals(text.trim(), 'EUR - Euro', 'current currency is EUR.');
            });


            /* click on language switcher */

            casper.waitForSelector('#switcher-language', function () {
                var code = casper.fetchText('#switcher-language-trigger > strong > span');
                test.assertEquals(code.trim(), 'EN', 'EN code exists in language switcher.');
                casper.click('div#switcher-language-trigger > strong > span');
            });

            casper.waitForSelector('#switcher-language-trigger + DIV', function () {
                var code = casper.fetchText('#ui-id-2 > li > a');
                test.assertEquals(code.trim(), 'RU', 'RU code exists in language switcher.');
                var screenFile = 'screen/' + scenario + '/' + scene + '/020.png';
                casper.capture(screenFile);
            });

            /* click on currency switcher */
            casper.waitForSelector('#switcher-currency', function () {
                var code = casper.fetchText('#switcher-currency-trigger > strong > span');
                test.assertEquals(code.trim(), 'EUR - Euro', 'EUR code exists in currency switcher.');
                casper.click('#switcher-currency-trigger > strong > span');
            });

            casper.waitForSelector('#switcher-currency-trigger + DIV', function () {
                var code = casper.fetchText('#ui-id-1 > li > a');
                test.assertEquals(code.trim(), 'USD - US Dollar', 'USD code exists in currency switcher.');
                var screenFile = 'screen/' + scenario + '/' + scene + '/030.png';
                casper.capture(screenFile);
            });
        });


        // /**
        //  * Switch to Russian store and validate currencies
        //  */
        //
        // /* switch to Russian store */
        // casper.waitForSelector('.switcher-dropdown[aria-hidden="true"]', function () {
        //     test.assertExists('.switcher-dropdown[aria-hidden="true"]', 'store switcher is collapsed.');
        //     casper.click('DIV#switcher-store-trigger > STRONG > SPAN');
        // });
        //
        // casper.waitForSelector('.switcher-dropdown[aria-hidden="false"]', function () {
        //     test.assertExists('.switcher-dropdown[aria-hidden="false"]', 'store switcher is expanded.');
        //     var screenFile = 'screen/' + scenario + '/' + scene + '/040.png';
        //     casper.capture(screenFile);
        // });
        //
        // casper.waitForSelector('#switcher-store > div > ul > li > a', function () {
        //     casper.click('#switcher-store > div > ul > li > a');
        // });
        //
        // casper.then(function () {
        //     var screenFile = 'screen/' + scenario + '/' + scene + '/050.png';
        //     casper.capture(screenFile);
        // });
        //
        // /* click on currency switcher */
        // casper.waitForSelector('#switcher-currency', function () {
        //     var code = casper.fetchText('#switcher-currency-trigger > strong > span');
        //     test.assertEquals(code.trim(), 'USD - Доллар США', 'USD code exists in currency switcher (Russian store).');
        //     casper.click('#switcher-currency-trigger > strong > span');
        // });
        //
        // casper.waitForSelector('#switcher-currency-trigger + DIV', function () {
        //     var code = casper.fetchText('#ui-id-1 > li > a');
        //     test.assertEquals(code.trim(), 'EUR - Евро', 'EUR code exists in currency switcher (Russian store).');
        //     var screenFile = 'screen/' + scenario + '/' + scene + '/060.png';
        //     casper.capture(screenFile);
        // });


        casper.run(function () {
            test.done()
        })
    }
)
