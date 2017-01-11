"use strict"
/* globals: casper, mobi */

var dump = require("utils").dump;

var pack = "020";
var scenario = "021";
var desc = "scenario " + pack + "/" + scenario + ": Google Signup link extraction:";
var pathScreens = mobi.opts.path.screenshots;
var authGmailCustomer = mobi.opts.auth.gmail.customerAnon;
var uriMageSignup; // URI extracted from Gmail message

casper.test.begin(desc, function scene_020_021(test) {

    /** Start scenario and setup phantom/capser */
    casper.start().then(function () {
        mobi.setViewport();
    });

    /** Gmail login form is loaded */
    casper.then(function () {
        var url = "https://mail.google.com/mail/u/0/h/1pq68r75kzvdr/?v%3Dlui";
        casper.open(url).then(function () {
            var cssBtnNext = "input#next";
            casper.waitForSelector(cssBtnNext, function () {
                test.assert(true, 'Gmail login form is loaded.');
                casper.fillSelectors("#identifier-shown", {
                    "#Email": authGmailCustomer.email
                }, false);
                mobi.capture("100", scenario, pack);
                casper.click(cssBtnNext, "50%", "50%");

                /** fill in passwd */
                var cssFldPasswd = '#Passwd';
                casper.waitFor(function check() {
                    var result = casper.visible(cssFldPasswd);
                    return result;
                }, function then() {
                    casper.fillSelectors("#password-shown", {
                        "#Passwd": authGmailCustomer.password
                    }, false);
                    casper.click(cssBtnNext, "50%", "50%");
                });
            });
        });
    });

    /** User is logged into Gmail. */
    casper.then(function () {
        var cssEmail = "#guser > nobr > b";
        casper.waitForSelector(cssEmail, function () {
            var email = casper.fetchText(cssEmail);
            test.assertEquals(email, authGmailCustomer.email, "User is logged into Gmail.");
            mobi.capture("110", scenario, pack);
        });
    });

    /** Open signup email */
    casper.then(function () {
        var cssItem = "body > table:nth-child(16) > tbody > tr > td:nth-child(2) > table:nth-child(1) > tbody > tr > td:nth-child(2) > form > table.th > tbody > tr:nth-child(1) > td:nth-child(3) > a ";
        casper.waitForSelector(cssItem, function () {
            var subject = casper.fetchText(cssItem);
            var isSignupEmail = (subject.indexOf("Welcome to MOBI Test Store") !== -1);
            casper.click(cssItem);
        });
    });

    /** Extract signup URI */
    casper.then(function () {
        mobi.capture("120", scenario, pack);
        var cssLink = "body > table:nth-child(16) > tbody > tr > td:nth-child(2) > table:nth-child(1) > tbody > tr > td:nth-child(2) > table:nth-child(4) > tbody > tr > td > table:nth-child(2) > tbody > tr:nth-child(4) > td > div > div > table > tbody > tr > td > table > tbody > tr:nth-child(2) > td > p:nth-child(3) > a";
        var href = casper.getElementAttribute(cssLink, "href");
        var replaced = href.replace("http://www.google.com/url?q=", "");
        var decoded = decodeURIComponent(replaced);
        uriMageSignup = decoded;
    });

    /** Goto Inbox */
    casper.then(function () {
        var cssBackToInbox = "a.searchPageLink"; // there are 2 links on the page
        casper.click(cssBackToInbox);
        mobi.capture("130", scenario, pack);
    });

    /** Check all messages */
    casper.then(function () {
        var cssCheckbox = "input[type=checkbox]";
        var elements = casper.getElementsInfo(cssCheckbox);
        elements.forEach(function (element) {
            casper.echo("::: " + JSON.stringify(element));
            casper.click("input[value='" + element.attributes.value + "']");
        });
        mobi.capture("140", scenario, pack);
    });

    /** Press "Delete" button */
    casper.then(function () {
        var cssBtnDelete = "input[value='Delete']";
        casper.click(cssBtnDelete);
        mobi.capture("150", scenario, pack);
    });

    /** Run scenario and finalize test. */
    casper.run(function () {
        mobi.capture("999", scenario, pack);
        test.done()
        casper.echo("Signup URI: " + uriMageSignup);
    })

});
