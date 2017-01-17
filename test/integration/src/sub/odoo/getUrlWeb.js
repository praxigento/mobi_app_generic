"use strict"

/**
 * Construct URL for Odoo.
 *
 * @param {string} path path to part of the Odoo (absolute path started with "/", alias - w/o)
 */
var result = function getUrlWeb(path) {
    // shortcuts for globals
    var casper = global.casper
    var mobi = global.mobi


    casper.echo("  construct Odoo Web URL for path '" + path + "'.", "PARAMETER")
    var isAlias = path.indexOf("/") === -1     // absolute path contains at least one "/" char
    var root = mobi.opts.navig.odoo
    var result
    if (isAlias) {
        /* compose URI based on "#route.to.page"*/
        var route = mobi.objPath.get(root.web, path)
        result = root.self + root.web.self + route.self
    } else {
        /* composer URI based on "/web#rote.to.page"*/
        result = root.self + path
    }
    casper.echo("  result URL: " + result, "PARAMETER")
    return result
}

module.exports = result