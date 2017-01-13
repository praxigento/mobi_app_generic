"use strict"
/* globals: casper, mobi */

/**
 * Odoo authentication function.
 *
 * @param test casperjs test object
 * @param opts authentication options
 */
var result = function odooAuthentication($opts) {
    /* shortcut globals */
    var conf = mobi.opts.conf;

    /* parse arguments */
    var opts = $opts || {}
    var pack = opts.pack || "undef"
    var scenario = opts.scenario || "undef"
    var userName = opts.userName || "admin"
    var userPass = opts.userPass || "admin"
    var saveScreens = opts.saveScreens || false; // save screenshots


    /** Odoo authentication itself */
    casper.then(function () {

        casper.echo("Odoo authentication is started.");
        var url = mobi.getUrlOdoo("/web/login");
        casper.open(url);

        /* check login button appearence */
        var cssBtn = "div.oe_login_buttons > button";
        casper.waitForSelector(cssBtn, function () {
            if (saveScreens) mobi.capture("odooAuth-010", scenario, pack);
        });

        /* fill the from and click submit button */
        casper.waitForSelector("form.oe_login_form", function () {
            casper.fillSelectors("form.oe_login_form", {
                "input#login": userName,
                "input#password": userPass
            }, false);
            casper.click(cssBtn);
            if (saveScreens) mobi.capture("odooAuth-020", scenario, pack);
        });

        /* validate homepage loading */
        casper.waitForSelector("a[data-menu='logout']", function () {
            casper.log("Homepage is loaded.");
            if (saveScreens) mobi.capture("odooAuth-030", scenario, pack);
        }, null, 10000);

    });
}

module.exports = result;