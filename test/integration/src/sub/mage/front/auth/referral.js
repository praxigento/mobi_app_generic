"use strict"
/**
 * Setup referral cookies for anonymous visitors.
 *
 * @param {Object} opts
 * @param {stirng} opts.url - URL for cookie
 * @param {stirng} opts.code - referral code
 */
var result = function mageFrontAuthReferral(opts) {
    // shortcut globals
    var phantom = global.phantom

    // parse input options
    var opts = opts || {}
    var url = opts.url || "undef"
    var code = opts.code || "undef"

    // function itself
    var parts = url.split("/")
    var domain = parts[2]

    phantom.addCookie({
        name: "prxgtDwnlReferral",  // see \Praxigento\Downline\Tool\Def\Referral::COOKIE_REFERRAL_CODE
        value: code + "%3A20170101",     // "REF_CODE:DATE_SAVED_YYYYMMDD"
        domain: domain,
        path: "/",
        expires: (new Date()).getTime() + (1000 * 60 * 60 * 24 * 365)   /* <-- expires in 1 year */
    });
}

module.exports = result