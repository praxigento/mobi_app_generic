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
var scenario = "000"
var suite = {pack: pack, scenario: scenario}
var optsCapture = {suite: suite}
var optsSubs = {suite: suite, screen: {save: false}}
var desc = "scenario " + pack + "/" + scenario + ": entries points checking:"
var authMageApi = mobi.opts.auth.mage.api.odoo;
var authOdooApi = mobi.opts.auth.odoo.api.mage;
// var authMageAdmin = mobi.opts.auth.mage.admin.tester
// var authMageFront = mobi.opts.auth.mage.front.customer01
// var authMageApi = mobi.opts.auth.mage.api.odoo

casper.test.begin(desc, 7, function suite_000_000(test) {

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
            test.assertSelectorHasText("div.message-success > div", "You have logged out.", "Test user is logged out of Magento Admin.")
        })


        // Magento Front

        // User 'customer_10@test.com' is logged into Magento Front.
        casper.then(function () {
            subFront.auth(optsSubs)
        })

        /** Magento front is alive */
        casper.then(function () {
            test.assert(true, "Magento front is alive.")
            subTest.capture(optsCapture)
        })

        // logout
        casper.then(function () {
            subFront.logout(optsSubs)
        })

        /** Test user is logged out of Magento Front */
        casper.then(function () {
            test.assertSelectorHasText("body", "You are signed out", "Test user is logged out of Magento Front.")
        })


        // Magento API

        /** Test user is authenticated in Magento REST API */
        casper.then(function () {
            var url = subFront.getUrl("/rest/V1/integration/admin/token")
            var user = authMageApi.user
            var password = authMageApi.password
            var request = {username: user, password: password}
            var data = JSON.stringify(request)
            casper.echo("Mage API (" + url + ") authentication request: " + data)
            casper.open(url, {
                    method: "post",
                    headers: {
                        "Accept": "*/*",
                        "Content-Type": "application/json"
                    },
                    data: data
                }
            ).then(function (response) {
                var status = response.status
                test.assertEqual(status, 200, "Test user is authenticated in Magento REST API.")
            })
        })


        // Odoo Web

        //
        casper.then(function () {
            subOdoo.auth(optsSubs)
        })

        /** Odoo Web is alive  */
        casper.then(function () {
            test.assert(true, "Odoo Web is alive.")
            subTest.capture(optsCapture)
        })


        // Odoo API

        /** Test user is authenticated in Odoo REST API */
        casper.then(function () {
            var url = subOdoo.getUrl("/api/auth")
            var dbName = authOdooApi.dbname
            var user = authOdooApi.user
            var password = authOdooApi.password
            var request = {dbname: dbName, login: user, password: password}
            var data = JSON.stringify(request)
            casper.echo("Odoo API (" + url + ") authentication request: " + data)
            casper.open(url, {
                    method: "post",
                    headers: {
                        "Accept": "*/*",
                        "Content-Type": "application/json; charset=utf-8"
                    },
                    data: data
                }
            ).then(function (response) {
                var status = response.status
                test.assertEqual(status, 200, "Test user is authenticated in Odoo REST API.")
            })

        })

        // Run scenario and finalize test.
        subTest.run(test)
    }
)
