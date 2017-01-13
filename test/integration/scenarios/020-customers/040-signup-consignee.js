"use strict"
var casper = global.casper;
var mobi = global.mobi;

var pack = "020";
var scenario = "040";
var desc = "scenario " + pack + "/" + scenario + ": Consignee Signup:";
var pathScreens = mobi.opts.path.screenshots;
var consignee = mobi.opts.auth.odoo.web.consignee;
var wait = 0;

var fnWaitWhileLoading = function () {
    casper.waitFor(function () {
        var result = !casper.visible("body > div.openerp.openerp_webclient_container.oe_webclient > div.oe_loading");
        return result;
    }, function () {
        mobi.capture("wait-" + (++wait), scenario, pack);
    });
}

casper.test.begin(desc, function scene_020_040(test) {

    /** Start scenario and setup phantom/casper */
    casper.start().then(function () {
        mobi.setViewport();
    });

    // Odoo: register new Consignee

    casper.then(function () {
        mobi.sub.odoo.authenticate({pack: pack, scenario: scenario});
    });

    /** Goto "Customer/New" form */
    casper.then(function () {
        var url = mobi.sub.odoo.getUrlWeb("sales.customers.new");
        casper.open(url).then(function () {
            casper.waitForSelector("input[placeholder='Name']", function () {
                fnWaitWhileLoading();
                test.assert(true, "Goto 'Customer New' from.");
                mobi.capture("010", scenario, pack);
            });
        });
    });

    /** Fill Name */
    casper.then(function () {
        fnWaitWhileLoading();
        casper.fillSelectors("div.oe_form", {
            "input[placeholder='Name']": consignee.name,
            // "span:nth-child(2) > div > input": "Consignee",
            // "#oe-field-input-45": "1200 Accounts Receivable",
            // "#oe-field-input-46": "2000 Accounts Payable"
        }, false);
        mobi.capture("020", scenario, pack);

    });

    /** Select Group */
    casper.then(function () {
        // casper.click("button.oe_form_button_save.btn.btn-primary.btn-sm");
        fnWaitWhileLoading();
        casper.click("div.oe_title > div > span:nth-child(2) > div > span > img");
        fnWaitWhileLoading();
        casper.sendKeys("div.oe_title > div > span:nth-child(2) > div > input", "Cons", {keepFocus: true});
        fnWaitWhileLoading();
        casper.waitForSelector("#ui-id-2 > li:nth-child(2) > a", function () {
            casper.click("#ui-id-2 > li:nth-child(2)");
            fnWaitWhileLoading();
            mobi.capture("030", scenario, pack);
        });
    });

    /** Click "Accounting" tab */
    casper.then(function () {
        fnWaitWhileLoading();
        casper.click("div.oe_form_sheetbg > div > div.oe_clear > ul > li:nth-child(4) > a");
        mobi.capture("040", scenario, pack);
    });

    /** Select "Account Receivable" */
    casper.then(function () {
        // casper.click("button.oe_form_button_save.btn.btn-primary.btn-sm");
        fnWaitWhileLoading();
        casper.click("#notebook_page_23 td.oe_form_group_cell.oe_group_right > table > tbody > tr:nth-child(1) > td:nth-child(2) > span > div > span > img");
        casper.waitFor(function () {
            var result = !casper.visible("body > div.openerp.openerp_webclient_container.oe_webclient > div.oe_loading");
            return result;
        }, function () {
            casper.sendKeys("#oe-field-input-45", "2000 ", {keepFocus: true});
            casper.sendKeys('#oe-field-input-45', casper.page.event.key.Enter, {keepFocus: false});
            mobi.capture("050", scenario, pack);
        });
    });


    /** Click "Accounting" tab */
    casper.then(function () {
        fnWaitWhileLoading();
        casper.click("div.oe_form_sheetbg > div > div.oe_clear > ul > li:nth-child(4) > a");
        mobi.capture("060", scenario, pack);
    });

    /** Select "Account Payable" */
    var cssSelectPayable = "#notebook_page_23 > table > tbody > tr:nth-child(2) > td.oe_form_group_cell.oe_group_right > table > tbody > tr:nth-child(2) > td:nth-child(2) > span > div > span > img";
    casper.then(function () {
        // casper.click("button.oe_form_button_save.btn.btn-primary.btn-sm");
        fnWaitWhileLoading();
        mobi.capture("062", scenario, pack);
        casper.click(cssSelectPayable);
        mobi.capture("064", scenario, pack);
        fnWaitWhileLoading();
    });
    casper.then(function () {
        // casper.click("button.oe_form_button_save.btn.btn-primary.btn-sm");
        fnWaitWhileLoading();
        casper.sendKeys("#oe-field-input-45", "2000 ", {keepFocus: true});
        fnWaitWhileLoading();
        casper.sendKeys('#oe-field-input-45', casper.page.event.key.Enter, {keepFocus: false});
        mobi.capture("066", scenario, pack);
    });

    /** Click "Accounting" tab */
    casper.then(function () {
        fnWaitWhileLoading();
        casper.click("div.oe_form_sheetbg > div > div.oe_clear > ul > li:nth-child(4) > a");
        mobi.capture("070", scenario, pack);
    });

    /** Click "Accounting" tab */
    casper.then(function () {
        fnWaitWhileLoading();
        casper.click("button.oe_form_button_save.btn.btn-primary.btn-sm");
        mobi.capture("080", scenario, pack);
    });

    // casper.then(function () {
    //     fnWaitWhileLoading();
    //     fnWaitWhileLoading();
    //     fnWaitWhileLoading();
    //     fnWaitWhileLoading();
    //     casper.click("#notebook_page_23 > table > tbody > tr:nth-child(2) > td.oe_form_group_cell.oe_group_right > table > tbody > tr:nth-child(2) > td:nth-child(2) > span > div > span > img");
    //     fnWaitWhileLoading(); // don't remove this waiter!!! (select does not work w/o)
    //     fnWaitWhileLoading(); // don't remove this waiter!!! (select does not work w/o)
    //     fnWaitWhileLoading(); // don't remove this waiter!!! (select does not work w/o)
    //     fnWaitWhileLoading(); // don't remove this waiter!!! (select does not work w/o)
    //     fnWaitWhileLoading(); // don't remove this waiter!!! (select does not work w/o)
    //     casper.waitFor(function () {
    //         var result = !casper.visible("body > div.openerp.openerp_webclient_container.oe_webclient > div.oe_loading");
    //         return result;
    //     }, function () {
    //         fnWaitWhileLoading();
    //         fnWaitWhileLoading();
    //         fnWaitWhileLoading();
    //         casper.sendKeys("#oe-field-input-46", "2100", {keepFocus: true});
    //         fnWaitWhileLoading();
    //         fnWaitWhileLoading();
    //         fnWaitWhileLoading();
    //         casper.sendKeys('#oe-field-input-46', casper.page.event.key.Enter, {keepFocus: true});
    //         fnWaitWhileLoading();
    //         fnWaitWhileLoading();
    //         fnWaitWhileLoading();
    //         fnWaitWhileLoading();
    //         mobi.capture("060", scenario, pack);
    //     });
    // });

    // casper.then(function () {
    //     // casper.click("button.oe_form_button_save.btn.btn-primary.btn-sm");
    //     fnWaitWhileLoading();
    //     mobi.capture("070", scenario, pack);
    // });


    /** Run scenario and finalize test. */
    casper.run(function () {
        mobi.capture("999", scenario, pack);
        test.done()
    })

});
