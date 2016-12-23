'use strict'

casper.echo("=== PRE SUITE BEGIN ===");

/**
 * Add 'mobi' object to globals.
 */
var mobi = {};

/**
 * Add testing configuration to 'mobi' object.
 */
mobi.opts = {
    navig: {
        base: 'http://mobi2.mage.test.prxgt.com',   // Base URL for the site should be tested (w/o ending '/')
        catalog: {
            category: '/catalog/category/view/s/cat-2/id/3/'
        },
    },
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


mobi.getNavigationUrl = function getNavigationUrl(path) {
    var uri = mobi.objPath.get(mobi.opts.navig, path);
    var result = mobi.opts.navig.base + uri;
    return result;
};


/* should we call this? */
casper.test.done();

casper.echo("=== PRE SUITE END ===");