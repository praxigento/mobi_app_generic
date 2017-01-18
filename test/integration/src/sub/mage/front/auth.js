"use strict"

/**
 * Magento frontend authentication function.
 *
 * @param opts authentication options
 */
var result = function mageFrontAuth(opts) {
    // shortcut globals
    var casper = global.casper
    var mobi = global.mobi
    var subFront = mobi.sub.mage.front
    var subTest = mobi.sub.test

    // parse input options
    var opts = opts || {}
    var suite = opts.suite || {pack: "undef", scenario: "undef"}
    var optsScreen = opts.screen || {} // screenshots related opts
    var saveScreens = optsScreen.save || false // don't save screenshots by
    var savePrefix = optsScreen.prefix || "mage-front-auth-" // default prefix for screenshots
    var user = opts.username || "customer_10@test.com"
    var password = opts.userpass || "UserPassword12"

    // local vars
    var optsCapture = {suite: suite, prefix: savePrefix}


    // function itself

    /** Magento Front authentication itself */
    casper.then(function () {

        /** open login page */
        casper.echo("Magento front authentication is started (" + user + ":" + password + ").", "PARAMETER")
        var url = subFront.getUrl('/customer/account/login/')
        var cssForm = "#login-form"
        casper.open(url)

        /** fill login form and submit */
        casper.waitForSelector(cssForm, function () {
            casper.echo("Login form is loaded.", "PARAMETER")
            if (saveScreens) subTest.capture(optsCapture)
            var cssBtnSign = "#send2 > span"
            casper.fillSelectors(cssForm, {
                "input#email": user,
                "input#pass": password
            }, false)
            casper.click(cssBtnSign)
        })

        /** wait for next page loading */
        casper.then(function () {
            var cssFront = "li.customer-welcome";
            casper.waitForSelector(cssFront, function () {
                casper.echo("User '" + user + "' is logged into Magento Front.", "PARAMETER")
                if (saveScreens) subTest.capture(optsCapture)
            }, null, 10000)
        })

    })

}

module.exports = result