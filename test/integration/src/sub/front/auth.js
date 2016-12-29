'use strict'
/* globals: casper, mobi */

/**
 * Magento frontend authentication function.
 *
 * @param test casperjs test object
 * @param opts authentication options
 */
var result = function frontAuthentication($test, $opts) {
    /* shortcut globals */
    var conf = mobi.opts.conf;

    /* parse arguments */
    var test = $test
    var opts = $opts || {}
    var scenario = opts.scenario || 'undef'
    var scene = opts.scene || 'undef'
    var username = opts.username || 'customer_10@test.com'
    var userpass = opts.userpass || 'UserPassword12'
    var store = opts.store || 'baltic'
    var currency = opts.currency || 'EUR'

    var currentStore, currentCurrency;

    /**
     * Load login form.
     */
    var url = mobi.getNavigationUrl('front.customer.account.login', 'mage');
    /* load page */
    casper.start(url, function () {
        mobi.setViewport();
        test.assertExists('div.page-wrapper', '0010: Default login form is loaded.');
    });

    /**
     * Fill the login form and authenticate.
     */
    casper.then(function () {

        /* fill form and submit data */
        casper.waitForSelector('#login-form', function () {
            casper.fillSelectors('#login-form', {
                'input#email': username,
                'input#pass': userpass
            }, false);
            casper.click('#send2 > span');
            test.assert(true, '0020: Authentication data is posted (user: ' + username + ').');
        });

        /* load account dashboard */
        casper.waitForSelector('#maincontent', function () {
            test.assert(true, '0030: Account dashboard is loaded.');
            mobi.capture('000', scene, scenario);
        });

    });

    /**
     * Get current store value (baltic|russian).
     */
    casper.then(function () {

        casper.waitForSelector('#switcher-store-trigger', function () {
            var text = casper.fetchText('#switcher-store-trigger > strong > span');
            text = text.toLowerCase();
            text = text.trim();
            casper.echo('Current store value: ' + text);
            switch (text) {
                case 'baltic':
                    currentStore = conf.app.store.baltic
                    break
                case 'russian':
                    currentStore = conf.app.store.russian
                    break
            }
        });
    });

    /**
     * Switch onto the given store.
     */
    casper.then(function () {
        /* check current store against given */
        if (currentStore != store) {

            casper.echo('Current store (' + currentStore + ') is NOT equal to given (' + store + ').');
            /* switch to other store */
            casper.then(function () {

                /* ... click switcher */
                casper.waitForSelector('#switcher-store-trigger', function () {
                    casper.click('#switcher-store-trigger > strong > span');
                });
                /* ... then click other store */
                casper.waitForSelector('#switcher-store > div > ul > li > a', function () {
                    casper.click('#switcher-store > div > ul > li > a');
                });

                /* ... and wait while loading */
                casper.waitForSelector('div.page-wrapper', function () {
                    test.assertExists('div.page-wrapper', 'Store is switched.');
                    mobi.capture('001', scene, scenario);
                });
            });

        } else {
            casper.echo('... current store is equal to given: ' + store);
        }

    });

    /**
     * Get current currency value (EUR|USD).
     */
    casper.then(function () {

        casper.waitForSelector('#switcher-currency-trigger', function () {
            var text = casper.fetchText('#switcher-currency-trigger > strong > span');
            text = text.toLowerCase();
            text = text.trim();
            casper.echo('Current currency value: ' + text);
            switch (text) {
                case 'eur - euro':
                case 'eur - euro':
                    currentCurrency = conf.app.currency.eur
                    break
                case 'usd - us dollar':
                case 'usd - доллар сша':
                    currentCurrency = conf.app.currency.usd
                    break
            }
        });
    });

    /**
     * Switch onto the given currency.
     */
    casper.then(function () {
        /* check current currency against given */
        if (currentCurrency != currency) {

            casper.echo('Current currency (' + currentCurrency + ') is NOT equal to given (' + currency + ').');
            /* switch to other currency */
            casper.then(function () {

                /* ... click switcher */
                casper.waitForSelector('#switcher-currency-trigger', function () {
                    casper.click('#switcher-currency-trigger > strong > span');
                });
                /* ... then click other currency */
                casper.waitForSelector('#switcher-currency > div > ul > li > a', function () {
                    casper.click('#switcher-currency > div > ul > li > a');
                });

                /* ... and wait while loading */
                casper.waitForSelector('div.page-wrapper', function () {
                    test.assertExists('div.page-wrapper', 'Currency is switched.');
                    mobi.capture('002', scene, scenario);
                });
            });

        } else {
            casper.echo('... current currency is equal to given: ' + currency);
        }

    });

}

module.exports = result;