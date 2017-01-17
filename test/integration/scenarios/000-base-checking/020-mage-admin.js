"use strict"
// shortcuts for globals
var casper = casper
var mobi = mobi
var subAdmin = mobi.sub.mage.admin
var subFront = mobi.sub.mage.front
var subOdoo = mobi.sub.odoo
var subTest = mobi.sub.test

// local vars
var pack = "000"
var scenario = "020"
var suite = {pack: pack, scenario: scenario}
var optsCapture = {suite: suite}
var optsSubs = {suite: suite, screen: {save: false}}
var desc = "scenario " + pack + "/" + scenario + ": Magento Admin Configuration checking:"


casper.test.begin(desc, function suite_000_020(test) {

        // Start scenario and setup phantom/capser
        subTest.start()


        // User 'tester' is logged into Magento Admin
        casper.then(function () {
            subAdmin.auth(optsSubs)
        })

        /** Check Magento Stores */
        casper.then(function () {
            var url = subAdmin.getUrl("/admin/system_store/")
            casper.open(url).then(function () {
                test.assert(true, "Check Magento Stores.")
                subTest.capture(optsCapture)
            })
        })

        /** Config / MOBI / Downline */
        casper.then(function () {
            var url = subAdmin.getUrl("/admin/system_config/edit/section/praxigento_downline/")
            casper.open(url).then(function () {
                test.assert(true, "Config / MOBI / Downline.")
                subTest.capture(optsCapture)
            })
        })

        /** Config / MOBI / Odoo */
        casper.then(function () {
            var url = subAdmin.getUrl("/admin/system_config/edit/section/praxigento_odoo/")
            casper.open(url).then(function () {
                if (!casper.visible("#praxigento_odoo_connect")) {
                    casper.click("#praxigento_odoo_connect-head")
                }
            })
            casper.then(function () {
                test.assert(true, "Config / MOBI / Odoo.")
                subTest.capture(optsCapture)
            })
        })

        /** Config / Customers / Create New Account Options */
        casper.then(function () {
            var url = subAdmin.getUrl("/admin/system_config/edit/section/customer/")
            casper.open(url).then(function () {
                if (!casper.visible("#customer_create_account")) {
                    casper.click("#customer_create_account-head")
                }
            }).then(function () {
                casper.waitFor(function () {
                    var result = casper.visible("#customer_create_account")
                    return result
                }, function then() {
                    test.assert(true, "Config / Customers / Create New Account Options.")
                    subTest.capture(optsCapture)
                }, function onTimeout() {
                    subTest.capture(optsCapture)
                })

            })
        })


        // logout
        casper.then(function () {
            subAdmin.logout(optsSubs)
        })

        /** Test user is logged out of Magento Admin */
        casper.then(function () {
            test.assertSelectorHasText("div.message-success > div", "You have logged out.", "Test user is logged out of Magento Admin.")
        })


        // Run scenario and finalize test.
        subTest.run(test)
    }
)
