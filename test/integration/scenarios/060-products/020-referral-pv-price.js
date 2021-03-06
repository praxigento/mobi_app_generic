"use strict"
// shortcuts globals
var casper = casper
var mobi = mobi
var conf = mobi.opts.conf
var subFront = mobi.sub.mage.front
var subTest = mobi.sub.test

// local vars
var pack = "060"
var scenario = "020"
var suite = {pack: pack, scenario: scenario}
var optsCapture = {suite: suite}
var optsSubs = {suite: suite, screen: {save: false}}
var desc = "scenario " + pack + "/" + scenario + ": Referral Price and PV checking:"

// function itself
casper.test.begin(desc, 6, function suite_060_010(test) {

    // Start scenario and setup phantom/casper
    subTest.start()

    // clean up referral cookies
    subFront.auth.anon()

    // open catalog for Baltic store & EUR currency as referred by Customer #10
    var url = subFront.getUrl("catalog.category")
    subFront.auth.referral({url: url, code: 10})

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
        var expectPriceEn = "€15.26"
        var expectPriceRu = "15,26 €"
        var expectPv = "9.60"

        /** price for Bee Royal is €15.26 */
        /** PV for Bee Royal is 9.60 */
        casper.waitForSelector(cssSan212, function then() {
            var text = casper.fetchText("#price-including-tax-product-price-4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assert((text == expectPriceEn) || (text == expectPriceRu), "... price for Bee Royal is " + text + ".")
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
        var expectPriceRu = "27,00 $" // probably browser settings affects to formatting
        var expectPriceEn = "$27.00"
        var expectPv = "10.80"

        /** price for Bee Royal is 27,00 $ */
        /** PV for Bee Royal is 10.80 */
        casper.waitForSelector(cssSan212, function then() {
            var text = casper.fetchText("#price-including-tax-product-price-4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assert((text == expectPriceEn) || (text == expectPriceRu), "... price for Bee Royal is " + text + ".")
            text = casper.fetchText("#prxgt_pv_4 > span")
            text = text.toLowerCase()
            text = text.trim()
            test.assertEquals(text.trim(), expectPv, "... PV for Bee Royal is " + text + ".")
        })

    })

    // Run scenario and finalize test.
    subTest.run(test)
})
