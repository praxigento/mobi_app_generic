"use strict"
/* globals: casper, mobi */

/**
 * Odoo authentication function.
 *
 * @param test casperjs test object
 * @param opts authentication options
 */
var result = function odooAuthentication($test, $opts) {
    /* shortcut globals */
    var conf = mobi.opts.conf;

    /* parse arguments */
    var test = $test
    var opts = $opts || {}
    var pack = opts.pack || "undef"
    var scenario = opts.scenario || "undef"
    var username = opts.username || "admin"
    var userpass = opts.userpass || "admin"

    casper.echo("Odoo authentication is started.");
    var url = mobi.getUrlOdoo("/web/login");
    casper.open(url);

    casper.waitForSelector("div.oe_login_buttons > button", function () {
        mobi.capture("000", scenario, pack);
    });
}

module.exports = result;