"use strict"

/**
 * Magento Admin logout function.
 *
 * @param opts authentication options
 */
var result = function mageAdminLogout(opts) {
    // shortcuts for globals
    var casper = global.casper
    var mobi = global.mobi

    // parse input options
    var opts = opts || {}
    var suite = opts.suite || {pack: "undef", scenario: "undef"}
    var optsScreen = opts.screen || {} // screenshots related opts
    var saveScreens = optsScreen.save || false // don't save screenshots by default
    var savePrefix = optsScreen.prefix || "mage-admin-logout-" // default prefix for screenshots

    // local vars
    var optsCapture = {suite: suite, prefix: savePrefix}

    /** Magento Admin authentication itself */
    casper.then(function () {
        var url = subAdmin.getUrl('/admin/auth/logout')
        casper.open(url).then(function () {
            if (saveScreens) subTest.capture(optsCapture)
        });
    });

}

module.exports = result