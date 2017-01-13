"use strict"
// var casper = global.casper;
// var mobi = global.mobi;

/**
 * Odoo authentication function.
 *
 * @param opts authentication options
 */
var result = function getUrlWeb(path) {
    casper.echo("  construct Odoo Web URL for path '" + path + "'.", "PARAMETER");
    var isAlias = path.indexOf('/') === -1;     // absolute path contains at least one '/' char
    var root = mobi.opts.navig.odoo;
    var result;
    if (isAlias) {
        /* composer URI based on "#route.to.page"*/
        var route = mobi.objPath.get(root.web, path);
        result = root.self + root.web.self + route.self;
    } else {
        /* composer URI based on "/web#rote.to.page"*/
        result = root.self + uri;
    }
    casper.echo("  result URL: " + result, "PARAMETER");
    return result;
}

module.exports = result;