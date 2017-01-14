"use strict"

/**
 * Magento Admin authentication function.
 *
 * @param opts authentication options
 */
var result = function mageAdminAuth(opts) {
    // shortcuts for globals
    var casper = global.casper
    var mobi = global.mobi
    var subAdmin = mobi.sub.mage.admin
    var subTest = mobi.sub.test
    var authUser = mobi.opts.auth.mage.admin.tester

    // parse input options
    var opts = opts || {}
    var suite = opts.suite || {pack: "undef", scenario: "undef"}
    var user = opts.userName || authUser.user
    var password = opts.userPass || authUser.password
    var optsScreen = opts.screen || {} // screenshots related opts
    var saveScreens = optsScreen.save || false // don't save screenshots by default
    var savePrefix = optsScreen.prefix || "mage-admin-auth-" // default prefix for screenshots

    // local vars
    var optsCapture = {suite: suite, prefix: savePrefix}


    /** Magento Admin authentication itself */
    casper.then(function () {

        /** open login page */
        casper.echo("Magento admin authentication is started (" + user + ":" + password + ").", "PARAMETER")
        var url = subAdmin.getUrl('/admin/')
        var cssForm = "#login-form"
        casper.open(url)

        /** fill login form and submit */
        casper.waitForSelector(cssForm, function () {
            casper.echo("Login form is loaded.", "PARAMETER")
            if (saveScreens) subTest.capture(optsCapture)
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
                casper.echo("User '" + user + "' is logged into Magento Admin.", "PARAMETER")
                if (saveScreens) subTest.capture(optsCapture)
            }, null, 10000)
        })
    })
}

module.exports = result