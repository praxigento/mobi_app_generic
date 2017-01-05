'use strict'

casper.echo("=== PRE SUITE BEGIN ===");

/**
 * Load MOBI parts (data objects and functions)
 */
var urls = require('../src/codes/url'); // URLs codifier
var conf = require('../src/codes/conf'); // application constants
var funcFrontAuthentication = require('../src/sub/front/auth'); // frontend authentication function
/**
 * Add 'mobi' object to globals.
 */
var mobi = {};

/**
 * Add testing configuration to 'mobi' object.
 */
mobi.opts = {
    conf: conf,
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
    var fileName = mobi.opts.path.screenshots + fileTag + '.png';
    casper.echo('  screen captured: ' + fileName);
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
    var result = scoped.self + uri; // compose full URL
    casper.echo('URL: ' + result);
    return result;
};

/* add sub scenarios to root object */
mobi.sub = {front: {}, admin: {}, odoo: {}};
mobi.sub.front.authenticate = funcFrontAuthentication;

/* should we call this? */
casper.test.done();

casper.echo("=== PRE SUITE END ===");