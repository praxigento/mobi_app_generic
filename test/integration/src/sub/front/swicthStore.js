'use strict'
/* globals: casper, mobi */

/**
 * Magento frontend switching function (select store, store view, currency).
 *
 * @param opts switching options
 */
var result = function switchStore($opts) {
    /* shortcut globals */
    var conf = mobi.opts.conf;

    /* parse arguments */
    var opts = $opts || {};
    var pack = opts.pack || 'undef';
    var scenario = opts.scenario || 'undef';
    var store = opts.store || 'baltic';
    var storeView = opts.storeView || 'EN';
    var currency = opts.currency || 'EUR';
    var saveScreens = opts.saveScreens || false; // save screenshots

    /* working variables */
    var currentStore, currentStoreView, currentCurrency;

    /** Save screenshot for init state */
    casper.then(function () {
        casper.echo("Switching options (store/view/cur): " + store + "/" + storeView + "/" + currency + ".");
        if (saveScreens) mobi.capture("switchStore-before", pack, scenario);
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
                    mobi.capture('001', scenario, pack);
                });
            });

        } else {
            casper.echo('... current store is equal to given: ' + store);
        }

    });

    // /**
    //  * Get current currency value (EUR|USD).
    //  */
    // casper.then(function () {
    //
    //     casper.waitForSelector('#switcher-currency-trigger', function () {
    //         var text = casper.fetchText('#switcher-currency-trigger > strong > span');
    //         text = text.toLowerCase();
    //         text = text.trim();
    //         casper.echo('Current currency value: ' + text);
    //         switch (text) {
    //             case 'eur - euro':
    //             case 'eur - euro':
    //                 currentCurrency = conf.app.currency.eur
    //                 break
    //             case 'usd - us dollar':
    //             case 'usd - доллар сша':
    //                 currentCurrency = conf.app.currency.usd
    //                 break
    //         }
    //     });
    // });
    //
    // /**
    //  * Switch onto the given currency.
    //  */
    // casper.then(function () {
    //     /* check current currency against given */
    //     if (currentCurrency != currency) {
    //
    //         casper.echo('Current currency (' + currentCurrency + ') is NOT equal to given (' + currency + ').');
    //         /* switch to other currency */
    //         casper.then(function () {
    //
    //             /* ... click switcher */
    //             casper.waitForSelector('#switcher-currency-trigger', function () {
    //                 casper.click('#switcher-currency-trigger > strong > span');
    //             });
    //             /* ... then click other currency */
    //             casper.waitForSelector('#ui-id-1 > li > a', function () {
    //                 casper.click('#ui-id-1 > li > a');
    //             });
    //
    //             /* ... and wait while loading */
    //             casper.waitForSelector('div.page-wrapper', function () {
    //                 test.assertExists('div.page-wrapper', 'Currency is switched.');
    //                 mobi.capture('002', scene, scenario);
    //             });
    //         });
    //
    //     } else {
    //         casper.echo('... current currency is equal to given: ' + currency);
    //     }
    //
    // });

}

module.exports = result;