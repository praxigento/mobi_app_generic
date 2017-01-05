'use strict'
/* globals: casper, mobi */

var dump = require('utils').dump;

var scenario = '000';
var scene = '000';
var desc = 'scene ' + scenario + '/' + scene + ': entries points checking:';
var pathScreens = mobi.opts.path.screenshots;

casper.test.begin(desc, 6, function suite_000_000(test) {

        /** Start scenario and setup phantom/capser */
        casper.start().then(function () {
            mobi.setViewport();
        });

        /** Magento admin is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl('admin.admin', 'mage');
            casper.open(url).then(function () {
                test.assertSelectorHasText('head > title', 'Magento Admin', 'Magento admin is alive');
                mobi.capture('010', scene, scenario);
            });
        });

        /** Magento front is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl('front.self', 'mage');
            casper.open(url).then(function () {
                test.assertSelectorHasText('head > title', 'Home page', 'Magento front is alive');
                mobi.capture('020', scene, scenario);
            });
        });

        /** Magento API is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl('api.schema', 'mage');
            casper.open(url).then(function () {
                var content = casper.getPageContent();
                test.assertMatch(content, /^{\"swagger\":\"2.0\"/i, 'Magento API is alive');
                mobi.capture('030', scene, scenario);
            });
        });

        /** Odoo admin is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl('admin.self', 'odoo');
            casper.open(url).then(function () {
                test.assertSelectorHasText('head > title', 'Odoo', 'Odoo admin is alive');
                mobi.capture('040', scene, scenario);
            });
        });

        /** Odoo shop is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl('shop.self', 'odoo');
            casper.open(url).then(function () {
                // var content = casper.getPageContent();
                // casper.echo(content);
                test.assertSelectorHasText('head > title', 'Odoo', 'Odoo shop is alive');
                mobi.capture('050', scene, scenario);
            });
        });

        /** Odoo API is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl('api.self', 'odoo');
            casper.open(url).then(function () {
                // var content = casper.getPageContent();
                // casper.echo(content);
                test.assert(false, 'Odoo API is alive');
                mobi.capture('060', scene, scenario);
            });
        });

        /** Run scenario and finalize test. */
        casper.run(function () {
            test.done()
        })
    }
);
