"use strict"

/**
 * Odoo authentication function.
 *
 * @param opts authentication options
 */
var result = function odooAuth(opts) {
    // shortcuts for globals
    var casper = global.casper
    var mobi = global.mobi
    var conf = mobi.opts.conf

    // parse input options
    var opts = opts || {}
    var suite = opts.suite || {pack: "undef", scenario: "undef"}
    var optsScreen = opts.screen || {} // screenshots related opts
    var saveScreens = optsScreen.save || false // don't save screenshots by default
    var savePrefix = optsScreen.prefix || "odoo-web-login-" // default prefix for screenshots
    var userName = opts.userName || "admin"
    var userPass = opts.userPass || "admin"

    // local vars
    var optsCapture = {suite: suite, prefix: savePrefix}


    /** Odoo authentication itself */
    casper.then(function () {

        var url = mobi.getUrlOdoo("/web/login")
        casper.open(url)

        /* fill the from and click submit button */
        var cssForm = "form.oe_login_form"
        var cssBtn = "div.oe_login_buttons > button"
        casper.waitForSelector(cssForm, function () {
            casper.fillSelectors(cssForm, {
                "input#login": userName,
                "input#password": userPass
            }, false)
            casper.click(cssBtn)
            if (saveScreens) subTest.capture(optsCapture)
        })

        /* validate homepage loading */
        casper.waitForSelector(".oe_application .oe_client_action", function () {
            casper.echo("User '" + userName + "' is logged into Odoo Web")
            if (saveScreens) subTest.capture(optsCapture)
        }, function () {
            if (saveScreens) subTest.capture(optsCapture)
        }, 10000)


    })
}

module.exports = result