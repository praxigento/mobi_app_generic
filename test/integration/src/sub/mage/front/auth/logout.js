"use strict"
/**
 * Frontend logout function.
 *
 * @param {Object} opts
 * @param {Object} opts.screen
 * @param {boolean} opts.screen.save
 * @param {string} opts.screen.prefix
 * @param {Object} opts.suite
 * @param {string} opts.suite.pack
 * @param {string} opts.suite.scenario
 */
var result = function mageFrontAuthReferral(opts) {
    // shortcut globals
    var phantom = global.phantom
    var mobi = global.mobi
    var subFront = mobi.sub.mage.front
    var subTest = mobi.sub.test

    // parse input options
    var opts = opts || {}
    var optsScreen = opts.screen || {} // screenshots related opts
    var saveScreens = optsScreen.save || false // don't save screenshots by
    var savePrefix = optsScreen.prefix || "mage-front-auth-logout-" // default prefix for screenshots
    var suite = opts.suite || {pack: "undef", scenario: "undef"}

    // local vars
    var optsCapture = {suite: suite, prefix: savePrefix}

    // function itself

    casper.then(function () {
        var url = subFront.getUrl('/customer/account/logout')
        casper.open(url).then(function () {
            casper.echo("Mage Front logout is perofrmed.")
            if (saveScreens) subTest.capture(optsCapture)
        });
    });
}

module.exports = result