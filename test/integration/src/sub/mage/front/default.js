"use strict"

var auth = require("./auth")
auth.anon = require("./auth/anon")
auth.logout = require("./auth/logout")
auth.referral = require("./auth/referral")

var result = {
    auth: auth,
    getUrl: require("./getUrl"),
    logout: auth.logout,
    switch: require("./switch/default"),
    switchStore: require("./swicthStore")
}

module.exports = result