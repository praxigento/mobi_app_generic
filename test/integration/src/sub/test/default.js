"use strict"
var fnStart = function mobiTestStart() {
    var casper = global.casper
    var mobi = global.mobi
    casper.start().then(function () {
        mobi.setViewport();
    });
}
var fnRun = function mobiTestRun(test) {
    var casper = global.casper
    var mobi = global.mobi
    casper.run(function () {
        test.done()
    })
}

module.exports = {
    capture: require("./capture").default,
    run: fnRun,
    start: fnStart
}