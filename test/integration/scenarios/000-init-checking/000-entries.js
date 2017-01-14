"use strict"
// globals vars
var casper = casper;
var mobi = mobi;
// local vars
var pack = "000";
var scenario = "000";
var desc = "scenario " + pack + "/" + scenario + ": entries points checking:";
var authMageAdmin = mobi.opts.auth.mage.admin.tester;
var authMageFront = mobi.opts.auth.mage.front.customer01;
var authMageApi = mobi.opts.auth.mage.api.odoo;

casper.test.begin(desc, 10, function suite_000_000(test) {

        /** Start scenario and setup phantom/capser */
        casper.start().then(function () {
            mobi.setViewport();
        });

        // Magento Admin

        /** Magento admin is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl("/admin/admin/", "mage");
            casper.open(url).then(function () {
                test.assertSelectorHasText("head > title", "Magento Admin", "Magento admin is alive");
                mobi.capture("010", scenario, pack);
            });
        });

        /** Admin "tester" is authenticated */
        casper.then(function () {
            /* fill username & password */
            var user = authMageAdmin.user;
            var password = authMageAdmin.password;
            casper.waitForSelector("#login-form", function () {
                casper.fillSelectors("#login-form", {
                    "input#username": user,
                    "input#login": password
                }, false);
                casper.click("#login-form > fieldset > div.form-actions > div.actions > button > span");
                casper.echo("Authenticating test user at Magento admin...");
            });

            casper.waitForSelector(".page-title-wrapper", function () {
                test.assertSelectorHasText("head > title", "Dashboard / Magento Admin", "Test user is authenticated in Magento Admin.");
                mobi.capture('020', scenario, pack);
            });
        });

        /** Test user is logged out of Magento Admin */
        casper.then(function () {
            var url = mobi.getNavigationUrl("/admin/admin/auth/logout/", "mage");
            casper.open(url).then(function () {
                test.assertSelectorHasText("div.message-success > div", "You have logged out.", "Test user is logged out of Magento Admin.");
                mobi.capture("030", scenario, pack);
            });
        });

        /**
         * Magento Front
         */

        /** Magento front is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl("/customer/account/login/", "mage");
            casper.open(url).then(function () {
                test.assertSelectorHasText("head > title", "Customer Login", "Magento front is alive");
                mobi.capture("040", scenario, pack);
            });
        });

        /** Test customer is authenticated in Magento Front */
        casper.then(function () {
            /* fill username & password */
            var email = authMageFront.email;
            var password = authMageFront.password;
            casper.waitForSelector("#email", function () {
                casper.fillSelectors('#login-form', {
                    'input#email': email,
                    'input#pass': password
                }, false);
                casper.click('#send2 > span');
                casper.echo("Authenticating test user at Magento admin...");
            });

            /* load account dashboard */
            casper.waitForSelector('#maincontent', function () {
                test.assert(true, 'Test customer is authenticated in Magento Front');
                mobi.capture('050', scenario, pack);
            });
        });

        /** Test user is logged out of Magento Front */
        casper.then(function () {
            var url = mobi.getNavigationUrl("/customer/account/logout/", "mage");
            casper.open(url).then(function () {
                test.assertSelectorHasText("h1.page-title > span", "You are signed out", "Test user is logged out of Magento Front.");
                mobi.capture("060", scenario, pack);
            });
        });

        /**
         * Magento API
         */

        /** Test user is authenticated in Magento REST API */
        casper.then(function () {
            var url = mobi.getNavigationUrl("/rest/V1/integration/admin/token", "mage");
            var user = authMageApi.user;
            var password = authMageApi.password;
            var request = {username: user, password: password};
            var data = JSON.stringify(request);
            casper.open(url, {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json; charset=utf-8'
                    },
                    data: data
                }
            ).then(function (response) {
                var status = response.status;
                test.assertEqual(status, 200, "Test user is authenticated in Magento REST API");
            });
        });

        /**
         * Odoo admin
         */

        /** Odoo admin is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl("admin.self", "odoo");
            casper.open(url).then(function () {
                test.assertSelectorHasText("head > title", "Odoo", "Odoo admin is alive");
                mobi.capture("070", scenario, pack);
            });
        });

        /**
         * Odoo shop
         */

        /** Odoo shop is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl("shop.self", "odoo");
            casper.open(url).then(function () {
                // var content = casper.getPageContent();
                // casper.echo(content);
                test.assertSelectorHasText("head > title", "Odoo", "Odoo shop is alive");
                mobi.capture("080", scenario, pack);
            });
        });

        /**
         * Odoo API
         */

        /** Odoo API is alive */
        casper.then(function () {
            var url = mobi.getNavigationUrl("api.self", "odoo");
            casper.open(url).then(function () {
                // var content = casper.getPageContent();
                // casper.echo(content);
                test.assert(false, "Odoo API is alive");
                mobi.capture("090", scenario, pack);
            });
        });

        /** Run scenario and finalize test. */
        casper.run(function () {
            test.done()
        })
    }
);
