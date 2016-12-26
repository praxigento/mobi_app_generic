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
    casper.test.assert(true, 'Screen captured: ' + fileTag);
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


/* should we call this? */
casper.test.done();

casper.echo("=== PRE SUITE END ===");