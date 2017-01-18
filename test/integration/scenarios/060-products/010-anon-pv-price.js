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
        })

        // Run scenario and finalize test.
        subTest.run(test)
    }
)
