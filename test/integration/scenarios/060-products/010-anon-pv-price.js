"use strict"
// shortcuts for globals
var casper = casper
var mobi = mobi
var conf = mobi.opts.conf
var subFront = mobi.sub.mage.front
var subTest = mobi.sub.test

// local vars
var pack = "060"
var scenario = "010"
var suite = {pack: pack, scenario: scenario}
var optsCapture = {suite: suite}
var optsSubs = {suite: suite, screen: {save: false}}
var desc = "scenario " + pack + "/" + scenario + ": Anon Price and PV checking:"


casper.test.begin(desc, function suite_060_010(test) {

    // Start scenario and setup phantom/capser
    subTest.start()

    // clean up referral cookies
    subFront.auth.anon()

    // open catalog fro Baltic store & EUR currency
    var url = subFront.getUrl("catalog.category")
    casper.open(url).then(function () {
        optsSubs.store = conf.app.store.baltic
        optsSubs.currency = conf.app.currency.eur
        subFront.switch.store(optsSubs)
        subFront.switch.currency(optsSubs)
    })

    /** Catalog page is loaded for 'Baltic' store */
    casper.then(function () {
        subTest.capture(optsCapture)
        test.assert(true, "Catalog page is loaded for 'Baltic' store.")

        var cssSan212 = "div.price-final_price[data-product-id='4']"
        var expectPrice = "€10.26"
        var expectPv = "?.??"

        /** price for Bee Royal is €10.26 */
        casper.waitForSelector(cssSan212, function then() {
            var text = casper.fetchText("#price-including-tax-product-price-4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assertEquals(text.trim(), expectPrice, "... price for Bee Royal is " + text + ".")
        })

    })

    // open catalog fro Russian store & USD currency
    casper.then(function () {
        optsSubs.store = conf.app.store.russian
        optsSubs.currency = conf.app.currency.usd
        subFront.switch.store(optsSubs)
    })

    /** Catalog page is loaded for 'Russian' store */
    casper.then(function () {
        subTest.capture(optsCapture)
        test.assert(true, "Catalog page is loaded for 'Russian' store.")

        var cssSan212 = "div.price-final_price[data-product-id='4']"
        var expectPrice = "14,16 $"
        var expectPv = "?.??"

        /** price for Bee Royal is 14,16 $ */
        casper.waitForSelector(cssSan212, function then() {
            var text = casper.fetchText("#price-including-tax-product-price-4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assertEquals(text.trim(), expectPrice, "... price for Bee Royal is " + text + ".")
        })

    })

    // Run scenario and finalize test.
    subTest.run(test)
})