"use strict"
/* globals: casper, mobi */

var dump = require("utils").dump;

var pack = "020";
var scenario = "040";
var desc = "scenario " + pack + "/" + scenario + ": Consignee Signup:";
var pathScreens = mobi.opts.path.screenshots;
var authMageCustomer = mobi.opts.auth.mage.front.customerReferral;
var authGmailCustomer = mobi.opts.auth.gmail.customerReferral;


casper.test.begin(desc, function scene_020_040(test) {

    /** Start scenario and setup phantom/capser */
    casper.start().then(function () {
        mobi.setViewport();
    });

    // Odoo: register new customer

    casper.then(function () {
        var opts;
        mobi.sub.odoo.authenticate(test, opts);
    });


    /** Run scenario and finalize test. */
    casper.run(function () {
        mobi.capture("999", scenario, pack);
        test.done()
    })

});
