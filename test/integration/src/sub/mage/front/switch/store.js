"use strict"

/**
 * Store switching function.
 * Expected that front page is opened.
 *
 * @param {Object} opts
 */
var result = function mageFrontSwitchStore(opts) {
    // shortcut globals
    var casper = global.casper
    var mobi = global.mobi
    var conf = mobi.opts.conf
    var subTest = mobi.sub.test

    // parse input options
    var opts = opts || {}
    var suite = opts.suite || {pack: "undef", scenario: "undef"}
    var optsScreen = opts.screen || {} // screenshots related opts
    var saveScreens = optsScreen.save || false // don't save screenshots by
    var savePrefix = optsScreen.prefix || "mage-front-switch-store" // default prefix for screenshots
    var store = opts.store || "baltic"

    // local vars
    var currentStore
    var optsCapture = {suite: suite, prefix: savePrefix}
    var cssTrigger = "#switcher-store-trigger"
    var cssLabel = "#switcher-store-trigger > strong > span"
    var cssLabelOther = "#switcher-store > div > ul > li > a"

    // function itself

    /** extract store switcher value */
    casper.then(function () {
        if (saveScreens) subTest.capture(optsCapture)
        casper.waitForSelector(cssTrigger, function () {
            var text = casper.fetchText(cssLabel)
            text = text.toLowerCase()
            text = text.trim()
            casper.echo("Current store: " + text, "PARAMETER")
            switch (text) {
                case "baltic":
                    currentStore = conf.app.store.baltic
                    break
                case "russian":
                    currentStore = conf.app.store.russian
                    break
            }
        })
    })

    /** check current store and switch if not given */
    casper.then(function () {
        /* check current store against given */
        if (currentStore != store) {
            casper.echo("Current store (" + currentStore + ") is NOT equal to given (" + store + ").", "PARAMETER")

            /** switch to other store */
            casper.then(function () {
                /** ... click switcher */
                casper.waitForSelector(cssTrigger, function () {
                    casper.click(cssLabel)
                })
                /** ... then click other store */
                casper.waitForSelector(cssLabelOther, function () {
                    casper.click(cssLabelOther)
                })
            })

            /** ... and wait while loading */
            casper.then(function () {
                casper.waitForSelector("div.page-wrapper", function () {
                    if (saveScreens) subTest.capture(optsCapture)
                    casper.echo("Current store is switched to given (" + store + ").", "PARAMETER")
                })
            })

        } else {
            casper.echo("... current store is equal to given: " + store, "PARAMETER")
        }

    })

}

module.exports = result