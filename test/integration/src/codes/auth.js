'use strict'
/**
 * Authentication data for test accounts.
 */
var result = {
    mage: {
        admin: {
            /* \Praxigento\App\Generic2\Console\Command\Init\Sub\AclUser::USER_TESTER_ */
            tester: {
                user: 'tester',
                password: 'b4yHm6PNQ1PXeFsHzUDu'
            }
        },
        api: {
            /* \Praxigento\App\Generic2\Console\Command\Init\Sub\AclUser::USER_ODOO_ */
            odoo: {
                user: 'odoo',
                password: '8sxUwQ5NsK2R5RUb'
            }
        },
        front: {
            /* \Praxigento\App\Generic2\Console\Command\Init\Customers::$DEFAULT_PASSWORD_HASH */
            customer01: {
                email: 'customer_1@test.com',
                password: 'UserPassword12'
            },
            customerAnon: {
                email: 'mobi.anon@gmail.com',
                password: 'UserPassword12'
            },
            customerReferral: {
                email: 'mobi.referral@gmail.com',
                password: 'UserPassword12'
            }
        }
    },
    odoo: {
        api: {},
        shop: {},
        web: {}
    },
    gmail: {
        customerAnon: {
            email: 'mobi.anon@gmail.com',
            password: 'p11PGCLEVMjsRCVNemja'
        },
        customerReferral: {
            email: 'mobi.referral@gmail.com',
            password: 'TGxduvOCg1XlgkNkkeyn'
        }
    }
}

module.exports = result;