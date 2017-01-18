"use strict"

var auth = require("./auth")
auth.anon = require("./auth/anon")
auth.referral = require("./auth/referral")

var result = {
    auth: auth,
    getUrl: require("./getUrl"),
    logout: require("./logout"),
    switch: require("./switch/default"),
    switchStore: require("./swicthStore")
}

module.exports = result