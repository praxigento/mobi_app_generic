"use strict"
/* globals: casper, mobi */

var dump = require("utils").dump;

var pack = "020";
var scenario = "022";
var desc = "scenario " + pack + "/" + scenario + ": Create signup password:";
var pathScreens = mobi.opts.path.screenshots;
var authGmailCustomer = mobi.opts.auth.gmail.customerAnon;
var authMageCustomer = mobi.opts.auth.mage.front.customerAnon;
var uriMageSignup; // URI extracted from Gmail message

casper.test.begin(desc, function scene_020_021(test) {

    /** Start scenario and setup phantom/capser */
    casper.start().then(function () {
        mobi.setViewport();
    });

    /** "Set a New Password" page is loaded */
    casper.then(function () {
        var url = "http://mage2.local.host.com/customer/account/createPassword/?id=14&token=c3d428d4b8f1441d3622d91810f7150a&sa=D&sntz=1&usg=AFQjCNErvSJs_M9dgi4cvt9MAVNBWigffw";
        casper.open(url).then(function () {
            casper.waitForSelector("#form-validate > div > div > button", function () {
                test.assert(true, '"Set a New Password" page is loaded.');
            });
        });
    });

    /** Password value is filled in and submitted */
    casper.then(function () {
        casper.fillSelectors("#form-validate", {"#password": authMageCustomer.password}, false);
        casper.fillSelectors("#form-validate", {"#password-confirmation": authMageCustomer.password}, true);
        test.assert(true, "Password value is filled in and submitted.");
        mobi.capture("310", scenario, pack);
    });

    /** Login form is filled in and submitted */
    casper.then(function () {
        casper.waitForSelector("#send2", function () {
            casper.fillSelectors("#login-form", {"#email": authMageCustomer.email}, false);
            casper.fillSelectors("#login-form", {"#pass": authMageCustomer.password}, true);
            test.assert(true, "Login form is filled in and submitted.");
            mobi.capture("320", scenario, pack);
        });
    });

    /** Dashboard is loaded */
    casper.then(function () {
        casper.waitForSelector("h1.page-title", function () {
            test.assert(true, "Dashboard is loaded.");
            mobi.capture("330", scenario, pack);
        });
    });

    /** Run scenario and finalize test. */
    casper.run(function () {
        mobi.capture("999", scenario, pack);
        test.done()
    })

});
