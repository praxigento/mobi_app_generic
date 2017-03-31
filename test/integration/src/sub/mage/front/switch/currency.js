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

    // local funcs

    var filterCurrency = function (value) {
        var result
        var text = value.toLowerCase()
        text = text.trim()
        switch (text) {
            case "eur - euro":
            case "eur - евро":
                result = conf.app.currency.eur
                break
            case "usd - us dollar":
            case "usd - доллар сша":
                result = conf.app.currency.usd
                break
        }
        return result
    }

    // function itself

    /** extract currency switcher value */
    casper.then(function () {
        if (saveScreens) subTest.capture(optsCapture)
        casper.waitForSelector(cssTrigger, function () {
            var text = casper.fetchText(cssLabel)
            currentCur = filterCurrency(text)
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
                casper.waitFor(function check() {
                    var result = false
                    var text = casper.fetchText(cssLabel)
                    var testedCur = filterCurrency(text)
                    if (testedCur == currency) {
                        casper.echo("Current currency (" + text + ") is switched to given (" + currency + ").", "PARAMETER")
                        result = true
                    }
                    return result
                }, null, function onTimeout() {
                    casper.echo("Currency (" + text + ") cannot be switched to given (" + currency + ").", "PARAMETER")
                    if (saveScreens) subTest.capture(optsCapture)
                })
            })

        } else {
            casper.echo("... current currency is equal to given: " + currency, "PARAMETER")
        }

    })

}

module.exports = result