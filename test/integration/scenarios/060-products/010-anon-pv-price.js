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


casper.test.begin(desc, 6, function suite_060_010(test) {

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
        var expectPrice = "€16.96"
        var expectPv = "9.60"

        /** price for Bee Royal is €16.96 */
        /** PV for Bee Royal is 9.60 */
        casper.waitForSelector(cssSan212, function then() {
            var text = casper.fetchText("#price-including-tax-product-price-4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assertEquals(text.trim(), expectPrice, "... price for Bee Royal is " + text + ".")
            text = casper.fetchText("#prxgt_pv_4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assertEquals(text.trim(), expectPv, "... PV for Bee Royal is " + text + ".")
        })

    })

    // open catalog fro Russian store & USD currency
    casper.then(function () {
        optsSubs.store = conf.app.store.russian
        optsSubs.currency = conf.app.currency.usd
        subFront.switch.store(optsSubs)
        subFront.switch.currency(optsSubs)
    })

    /** Catalog page is loaded for 'Russian' store */
    casper.then(function () {
        subTest.capture(optsCapture)
        test.assert(true, "Catalog page is loaded for 'Russian' store.")

        var cssSan212 = "div.price-final_price[data-product-id='4']"
        var expectPrice = "30,00 $"
        var expectPv = "10.80"

        /** price for Bee Royal is 30,00 $ */
        /** PV for Bee Royal is 10.80 */
        casper.waitForSelector(cssSan212, function then() {
            var text = casper.fetchText("#price-including-tax-product-price-4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assertEquals(text.trim(), expectPrice, "... price for Bee Royal is " + text + ".")
            text = casper.fetchText("#prxgt_pv_4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assertEquals(text.trim(), expectPv, "... PV for Bee Royal is " + text + ".")
        })

    })

    // Run scenario and finalize test.
    subTest.run(test)
})
