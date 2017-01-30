"use strict"
/**
 * URLs codifier for MOBI testing.
 */
var urls = {
    mage: {
        self: "http://mage2.local.host.com",   // Base URL for the site (w/o ending "/")
        admin: {
            self: "/admin",
            admin: {
                self: "/admin",
                auth: {
                    logout: "/admin/admin/auth/logout/"
                }
            },
        },
        api: {
            self: "/rest/schema/",
            schema: "/rest/schema/"
        },
        front: {
            self: "/",
            catalog: {
                category: {
                    self: "/catalog/category/view/s/cat-2/id/3/"
                },
                product: {
                    san10674: {self: "/catalog/product/view/id/1"},
                    san136: {self: "/catalog/product/view/id/2"},
                    san203: {self: "/catalog/product/view/id/3"},
                    san212: {self: "/catalog/product/view/id/4"},
                    san215: {self: "/catalog/product/view/id/5"}
                }
            },
            checkout: {
                self: "/checkout/"
            },
            customer: {
                account: {
                    login: "/customer/account/login/"
                }
            }
        }
    },
    odoo: {
        self: "http://mobi.odoo.test-auto.prxgt.com",    // Base URL for the site (w/o ending "/")
        admin: {
            self: "/web"
        },
        web: {
            self: "/web",
            sales: {
                customers: {
                    new: {
                        self: "#view_type=form&model=res.partner&action=54"
                    }
                }
            }
        },
        api: {
            self: "/api"
        },
        shop: {
            self: "/shop"
        }
    }
}

module.exports = urls;