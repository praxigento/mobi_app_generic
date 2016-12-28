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
                category: '/catalog/category/view/s/cat-2/id/3/',
                product: {
                    san10674: '/catalog/product/view/id/1',
                    san136: '/catalog/product/view/id/2',
                    san203: '/catalog/product/view/id/3',
                    san212: '/catalog/product/view/id/4',
                    san215: '/catalog/product/view/id/5'
                }
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