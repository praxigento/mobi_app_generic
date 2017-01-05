'use strict'
/**
 * URLs codifier for MOBI testing.
 */
var urls = {
    mage: {
        self: 'http://mobi.mage.test-auto.prxgt.com',   // Base URL for the site (w/o ending '/')
        admin: {
            self: '/admin/',
            admin: '/admin/admin/'
        },
        api: {
            self: '/rest/schema/',
            schema: '/rest/schema/'
        },
        front: {
            self: '/',
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
    },
    odoo: {
        self: 'http://lion.host.prxgt.com:8122',    // Base URL for the site (w/o ending '/')
        admin: {
            self: '/web'
        },
        api: {
            self: '/api'
        },
        shop: {
            self: '/shop'
        }
    }
}

module.exports = urls;