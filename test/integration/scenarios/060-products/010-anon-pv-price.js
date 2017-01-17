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
var optsSubs = {suite: suite, screen: {save: true}}
var desc = "scenario " + pack + "/" + scenario + ": Anon Price and PV checking:"


casper.test.begin(desc, function suite_060_010(test) {

        // Start scenario and setup phantom/capser
        subTest.start()

        var url = subFront.getUrl("catalog.category");
        casper.open(url).then(function () {
            optsSubs.store = conf.app.store.baltic
            subFront.switch.store(optsSubs)
        })


        // Run scenario and finalize test.
        subTest.run(test)
    }
)
