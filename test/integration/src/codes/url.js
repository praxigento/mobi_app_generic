'use strict'
/**
 * URLs codifier for MOBI testing.
 */
var urls = {
    mage: {
        base: 'http://mobi2.mage.test.prxgt.com',   // Base URL for the site should be tested (w/o ending '/')
        admin: {},
        api: {},
        front: {
            catalog: {
                category: '/catalog/category/view/s/cat-2/id/3/'
            },
            checkout: {
                self: '/checkout/'
            },
            customer: {
                account: {
                    login: '/customer/account/login/'
                }
            }
        }
    }
}

module.exports = urls;