"use strict"
// shortcuts for globals
var casper = casper
var mobi = mobi
var subAdmin = mobi.sub.mage.admin
var subFront = mobi.sub.mage.front
var subTest = mobi.sub.test


// local vars
var pack = "000"
var scenario = "000"
var suite = {pack: pack, scenario: scenario}
var optsCapture = {suite: suite}
var optsSubs = {suite: suite, screen: {save: false}}
var desc = "scenario " + pack + "/" + scenario + ": entries points checking:"
// var authMageAdmin = mobi.opts.auth.mage.admin.tester
// var authMageFront = mobi.opts.auth.mage.front.customer01
// var authMageApi = mobi.opts.auth.mage.api.odoo

casper.test.begin(desc, function suite_000_000(test) {

        // Start scenario and setup phantom/capser
        subTest.start()

        // Magento Admin

        // User 'tester' is logged into Magento Admin
        casper.then(function () {
            subAdmin.auth(optsSubs)
        })

        /** Magento admin is alive */
        casper.then(function () {
            test.assert(true, "Magento admin is alive.")
            subTest.capture(optsCapture)
        })

        // logout
        casper.then(function () {
            subAdmin.logout(optsSubs)
        })

        /** Test user is logged out of Magento Admin */
        casper.then(function () {
            test.assertSelectorHasText("div.message-success > div", "You have logged out.", "Test user is logged out of Magento Admin.");
        })

        // Magento Front

        /** Magento front is alive */
        casper.then(function () {

            // TODO: authenticate on front using sub-func

            var url = mobi.getNavigationUrl("/customer/account/login/", "mage");
            casper.open(url).then(function () {
                test.assertSelectorHasText("head > title", "Customer Login", "Magento front is alive");
                mobi.capture("040", scenario, pack);
            });
        });

        // Run scenario and finalize test.
        subTest.run(test)
    }
)
