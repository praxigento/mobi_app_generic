"use strict"

/**
 * Registry to save counters for different scenarios (pack/scenario) to compose file names.
 * @type {{}}
 */
var registry = {}


/**
 *  Register counter for pack/scenario/prefix/suffix.
 *
 * @param {Object} opts - test suite structure
 * @param {string} opts.pack
 * @param {string} opts.scenario
 * @returns {number}
 */
var register = function (opts) {
    // parse input options
    var opts = opts || {}
    var pack = opts.pack || "undef"
    var scenario = opts.scenario || "undef"
    var prefix = opts.prefix || "undef"
    var suffix = opts.suffix || "undef"
    registry[pack] = registry[pack] || {}
    registry[pack][scenario] = registry[pack][scenario] || {}
    registry[pack][scenario][prefix] = registry[pack][scenario][prefix] || {}
    registry[pack][scenario][prefix][suffix] = registry[pack][scenario][prefix][suffix] || 0
    // function itself
    var result = ++registry[pack][scenario][prefix][suffix]
    return result
}
/**
 * Capture screenshot and save onto disk.
 *
 * @param {Object} opts
 * @param {Object} opts.suite - test suite structure
 * @param {string} opts.suite.pack
 * @param {string} opts.suite.scenario
 * @param {string} opts.prefix - prefix for image files
 * @param {string} opts.suffic - suffix for image files
 */
var result = function capture(opts) {
    // shortcuts for globals
    var casper = global.casper
    var mobi = global.mobi
    var root = mobi.opts.path.screenshots

    // parse input options
    var opts = opts || {}
    var suite = opts.suite || {pack: "undef", scenario: "undef"}
    var prefix = opts.prefix || ""
    var suffix = opts.suffix || ""

    // local vars
    var optsReg = JSON.parse(JSON.stringify(suite)) // clone object
    optsReg.prefix = prefix
    optsReg.suffix = suffix

    // functionality
    var counter = register(optsReg)
    var img = (counter * 10 + 1000) + ""
    img = prefix + img.substr(1, 3) + suffix
    var fileTag = suite.pack + "/" + suite.scenario + "/" + img;
    var fileName = root + fileTag + ".png";
    casper.echo("  screen captured: " + fileName, "INFO");
    casper.capture(fileName);
    return result
}

module.exports.default = result
module.exports.register = register