"use strict"

/**
 * Currency switching function.
 * Expected that front page is opened.
 *
 * @param {Object} opts
 * @param {string} opts.currency - see test/integration/src/codes/conf.js:result.app.currency
 * @param {Object} opts.screen
 * @param {boolean} opts.screen.save
 * @param {string} opts.screen.prefix
 * @param {Object} opts.suite
 * @param {string} opts.suite.pack
 * @param {string} opts.suite.scenario
 */
var result = function mageFrontSwitchCurrency(opts) {
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
    var savePrefix = optsScreen.prefix || "mage-front-switch-currency" // default prefix for screenshots
    var currency = opts.currency || "EUR"

    // local vars
    var currentCur
    var optsCapture = {suite: suite, prefix: savePrefix}
    var cssTrigger = "#switcher-currency-trigger"
    var cssLabel = "#switcher-currency-trigger > strong > span"
    var cssLabelOther = "#ui-id-1 > li > a"

    // function itself

    /** extract currecny switcher value */
    casper.then(function () {
        if (saveScreens) subTest.capture(optsCapture)
        casper.waitForSelector(cssTrigger, function () {
            var text = casper.fetchText(cssLabel)
            text = text.toLowerCase()
            text = text.trim()
            casper.echo("Current currency: " + text, "PARAMETER")
            switch (text) {
                case 'eur - euro':
                    currentCur = conf.app.currency.eur
                    break
                case 'usd - us dollar':
                case 'usd - доллар сша':
                    currentCur = conf.app.currency.usd
                    break
            }
        })
    })

    /** check current currency and switch if not given */
    casper.then(function () {
        /* check current currency against given */
        if (currentCur != currency) {
            casper.echo("Current currency (" + currentCur + ") is NOT equal to given (" + currency + ").", "PARAMETER")

            /** switch to other currency */
            casper.then(function () {
                /** ... click switcher */
                casper.waitForSelector(cssTrigger, function () {
                    casper.click(cssLabel)
                })
                /** ... then click other currency */
                casper.waitForSelector(cssLabelOther, function () {
                    casper.click(cssLabelOther)
                })
            })

            /** ... and wait while loading */
            casper.then(function () {
                casper.waitForSelector("div.page-wrapper", function () {
                    if (saveScreens) subTest.capture(optsCapture)
                    casper.echo("Current currency is switched to given (" + currency + ").", "PARAMETER")
                })
            })

        } else {
            casper.echo("... current currency is equal to given: " + currency, "PARAMETER")
        }

    })

}

module.exports = result