"use strict"

/**
 * Magento Admin logout function.
 *
 * @param opts logout options
 */
var result = function mageAdminLogout(opts) {
    // shortcuts for globals
    var casper = global.casper
    var mobi = global.mobi
    var subFront = mobi.sub.mage.front

    // parse input options
    var opts = opts || {}
    var suite = opts.suite || {pack: "undef", scenario: "undef"}
    var optsScreen = opts.screen || {} // screenshots related opts
    var saveScreens = optsScreen.save || false // don't save screenshots by default
    var savePrefix = optsScreen.prefix || "mage-front-logout-" // default prefix for screenshots

    // local vars
    var optsCapture = {suite: suite, prefix: savePrefix}

    /** Magento Front lgout itself */
    casper.then(function () {
        var url = subFront.getUrl('/customer/account/logout')
        casper.open(url).then(function () {
            casper.echo("Mage Front logout is perofrmed.")
            if (saveScreens) subTest.capture(optsCapture)
        });
    });

}

module.exports = result