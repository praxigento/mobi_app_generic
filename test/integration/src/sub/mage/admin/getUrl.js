"use strict"

/**
 * Construct URL for Magento.
 *
 * @param {string} path path to Magento page (absolute path started with '/', alias - w/o)
 */
var result = function getUrl(path) {
    /* shortcuts for globals */
    var casper = global.casper;
    var mobi = global.mobi;
    var root = mobi.opts.navig.mage;

    /* functionality */
    casper.echo("  construct Magento Admin URL for path '" + path + "'.", "PARAMETER");
    var isAlias = path.indexOf('/') === -1;     // absolute path contains at least one '/' char
    var result, url;
    if (isAlias) {
        /* compose URI based on "route.to.page" */
        var route = mobi.objPath.get(root.admin, path);
        url = route.self;
    } else {
        /* absolute path is used */
        url = path
    }
    /* "http://mage2.local.host.com" + "/admin" + "url" */
    result = root.self + root.admin.self + url;
    casper.echo("  result URL: " + result, "PARAMETER");
    return result;
}

module.exports = result;