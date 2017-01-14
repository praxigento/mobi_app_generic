"use strict"

/**
 * Magento Admin authentication function.
 *
 * @param opts authentication options
 */
var result = function odooAuthentication(opts) {
    /** shortcuts for globals */
    var casper = global.casper
    var mobi = global.mobi
    var authUser = mobi.opts.auth.mage.admin.tester

    /** parse input options */
    var opts = opts || {}
    var pack = opts.pack || "undef"
    var scenario = opts.scenario || "undef"
    var user = opts.userName || authUser.user
    var password = opts.userPass || authUser.password
    var optsScreen = opts.screen || {} // screenshots related opts
    var saveScreens = optsScreen.save || true // don't save screenshots by default
    var savePrefix = optsScreen.prefix || "mage-admin-auth-" // default prefix for screenshots


    /** Magento Admin authentication itself */
    casper.then(function () {

        /** open login page */
        casper.echo("Magento admin authentication is started (" + user + ":" + password + ").", "PARAMETER")
        var url = mobi.sub.mage.admin.getUrl('/admin/')
        var cssForm = "#login-form"
        casper.open(url)

        /** fill login form and submit */
        casper.waitForSelector(cssForm, function () {
            casper.echo("Login form is loaded.", "PARAMETER")
            if (saveScreens) mobi.capture(savePrefix + "010", scenario, pack)
            var cssBtnSign = "#login-form div.actions > button > span"
            casper.fillSelectors(cssForm, {
                "input#username": user,
                "input#login": password
            }, false)
            casper.click(cssBtnSign)
        })

        /** wait for next page loading */
        casper.then(function () {
            var cssAdmin = "#html-body > div.admin__menu-overlay";
            casper.waitForSelector(cssAdmin, function () {
                casper.log("Magento Admin is loaded.")
                if (saveScreens) mobi.capture(savePrefix + "020", scenario, pack)
            }, null, 10000)
        })
    })
}

module.exports = result