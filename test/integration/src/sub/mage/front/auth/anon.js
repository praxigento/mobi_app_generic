"use strict"
/**
 * Clean up referral cookies for anonymous visitors.
 */
var result = function mageFrontAuthAnon() {
    // shortcut globals
    var casper = global.casper

    // function itself
    casper.page.deleteCookie("prxgtDwnlReferral") // \Praxigento\Downline\Tool\Def\Referral::COOKIE_REFERRAL_CODE
}

module.exports = result