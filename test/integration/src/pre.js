'use strict'

casper.echo("=== PRE SUITE BEGIN ===");

/**
 * Load MOBI parts (data objects and functions)
 */
var address = require('../src/codes/address'); // address data
var auth = require('../src/codes/auth'); // authentication data
var conf = require('../src/codes/conf'); // application constants
var urls = require('../src/codes/url'); // URLs codifier
var funcFrontAuthentication = require('../src/sub/front/auth'); // frontend authentication function
/**
 * Add 'mobi' object to globals.
 */
var mobi = {};

/**
 * Add testing configuration to 'mobi' object.
 */
mobi.opts = {
    address: address,
    auth: auth,
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
    var fileTag = scenario + "/" + scene + "/" + img;
    var fileName = mobi.opts.path.screenshots + fileTag + ".png";
    casper.echo("  screen captured: " + fileName, "INFO");
    casper.capture(fileName);
};


/**
 *
 * @param {string} path 'front.catalog.category'
 * @param {string} scope 'mage|odoo|...'
 * @returns {string} 'http://mobi2.mage.test.prxgt.com/catalog/category/view/s/cat-2/id/3/'
 * @deprecated use
 */
mobi.getNavigationUrl = function getNavigationUrl(path, scope) {
    casper.echo("  construct URL for path '" + path + "' and scope '" + scope + "'.", "PARAMETER");
    var scoped = mobi.opts.navig[scope];        // shortcut for Magento URLs
    var isAlias = path.indexOf('/') === -1;     // absolute path contains at least one '/' char
    var uri = (isAlias) ? mobi.objPath.get(scoped, path) : path;   // get URL value by path or use path as-is
    var result = scoped.self + uri; // compose full URL
    casper.echo("  result URL: " + result, "PARAMETER");
    return result;
};

/**
 * If path contains '/' then return Mage Base URL (mobi.opts.navig.mage.self) + "path"
 * else return Mage Base URL + "path" in "mobi.opts.navig.mage" structure.
 *
 * @param {string} path dot-separated path to the URL in "mobi.opts.navig.mage" or "/real/path/to/the/point/"
 * @returns {string}
 */
mobi.getUrlMage = function getUrlMage(path) {
    casper.echo("  construct URL for path '" + path + "'.", "PARAMETER");
    var isAlias = path.indexOf('/') === -1;     // absolute path contains at least one '/' char
    var uri = (isAlias) ? mobi.objPath.get(mobi.opts.navig.mage, path) : path;   // get URL value by path or use path as-is
    var result = mobi.opts.navig.mage.self + uri; // compose full URL
    casper.echo("  result URL: " + result, "PARAMETER");
    return result;
};

/* add sub scenarios to root object */
mobi.sub = {front: {}, admin: {}, odoo: {}};
mobi.sub.front.authenticate = funcFrontAuthentication;

/* should we call this? */
casper.test.done();

casper.echo("=== PRE SUITE END ===");