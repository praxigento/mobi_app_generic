'use strict'

casper.echo("=== PRE SUITE BEGIN ===");

/**
 * Load URLs codifier.
 */
var urls = require('../src/codes/url');

/**
 * Add 'mobi' object to globals.
 */
var mobi = {};

/**
 * Add testing configuration to 'mobi' object.
 */
mobi.opts = {
    navig: urls,
    path: {                                         // Paths configuration
        screenshots: 'screen/'                      // Root folder for screenshots (with ending '/')
    },
    viewport: {width: 1024, height: 768},           // Viewport dimensions
};

/**==========================================
 * Add custom functions to the 'mobi' object.
 * ========================================== */

mobi.objPath = require('object-path'); // see https://github.com/mariocasciaro/object-path

/**
 * Function to set page size for the browser.
 */
mobi.setViewport = function setViewport() {
    var dimensions = mobi.opts.viewport;
    casper.page.viewportSize = dimensions;
};

/**
 * Capture image and save it in screenshots folder as 'scenario/scene/img.png'
 *
 * @param img
 * @param scene
 * @param scenario
 */
mobi.capture = function capture(img, scene, scenario) {
    var fileTag = scenario + '/' + scene + '/' + img;
    casper.test.assert(true, '  screen captured: ' + fileTag);
    var fileName = mobi.opts.path.screenshots + fileTag + '.png';
    casper.capture(fileName);
};


/**
 *
 * @param {string} path 'front.catalog.category'
 * @param {string} scope 'mage|odoo|...'
 * @returns {string} 'http://mobi2.mage.test.prxgt.com/catalog/category/view/s/cat-2/id/3/'
 */
mobi.getNavigationUrl = function getNavigationUrl(path, scope) {
    var scoped = mobi.opts.navig[scope];    // shortcut for Magento URLs
    var uri = mobi.objPath.get(scoped, path);   // get URL value by path
    var result = scoped.base + uri; // compose full URL
    return result;
};

/* add sub scenarios to root object */
mobi.sub = {front: {}, admin: {}, odoo: {}};


mobi.sub.front.authenticate = function frontAuthentication(test, scene, scenario) {
    /**
     * Start scene and go to login form.
     */
    var url = mobi.getNavigationUrl('front.customer.account.login', 'mage');
    /* load page */
    casper.start(url, function () {
        mobi.setViewport();
        test.assertExists('div.page-wrapper', '0010: Default login form is loaded.');
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
            test.assert(true, '0020: Authentication data is posted.');
        });


        casper.waitForSelector('#maincontent', function () {
            test.assert(true, '0030: Account dashboard is loaded.');
            mobi.capture('030', scene, scenario);
        });

        // current store is Baltic
        casper.waitForSelector('#switcher-store-trigger', function () {
            var text = casper.fetchText('#switcher-store-trigger > strong > span');
            test.assertEquals(text.trim(), 'Baltic', '... current store is Baltic.');
        });

        // current currency is EUR
        casper.waitForSelector('#switcher-currency-trigger', function () {
            var text = casper.fetchText('#switcher-currency-trigger > strong > span');
            test.assertEquals(text.trim(), 'EUR - Euro', '... current currency is EUR.');
        });

    });
}

/* should we call this? */
casper.test.done();

casper.echo("=== PRE SUITE END ===");