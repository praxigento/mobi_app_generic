"use strict"
// shortcut globals
var casper = casper
var mobi = mobi
var subFront = mobi.sub.mage.front
var subTest = mobi.sub.test
var authMageCustomer = mobi.opts.auth.mage.front.customerReferral
var authGmailCustomer = mobi.opts.auth.gmail.customerReferral
var address = mobi.opts.address.referral

// local vars
var pack = "020"
var scenario = "030"
var suite = {pack: pack, scenario: scenario}
var optsCapture = {suite: suite}
var optsSubs = {suite: suite, screen: {save: false}}
var desc = "scenario " + pack + "/" + scenario + ": Referral Signup:"
var uriMageSignup // URI extracted from Gmail message

// function itself
casper.test.begin(desc, 38, function scene_020_030(test) {

    // Start scenario and setup phantom/capser
    subTest.start()

    // clean up referral cookies
    subFront.auth.anon()

    // Magento Front: compose order

    // open catalog for Baltic store & EUR currency as referred by Customer #10
    var url = subFront.getUrl('catalog.product.san215')
    subFront.auth.referral({url: url, code: 10})
    casper.open(url).then(function () {
        optsSubs.store = conf.app.store.baltic
        optsSubs.currency = conf.app.currency.eur
        subFront.switch.store(optsSubs)
        subFront.switch.currency(optsSubs)
    })

    /** Product page is loaded for "215San" */
    casper.then(function () {
        test.assertSelectorHasText("div.product.attribute.sku > div", "215San", 'Product page is loaded for "215San".')
        subTest.capture(optsCapture)
    })

    /** "Add to Cart" button is clicked */
    casper.waitForSelector('#product-addtocart-button', function () {
        casper.click('#product-addtocart-button > span')
        test.assert(true, '"Add to Cart" button is clicked.')

        /** product is placed in the cart */
        var css = 'span.counter-number'
        casper.waitFor(function check() {
            var text = casper.fetchText(css)
            var result = ('' != text.trim()) // we don't know initial value of the products in the cart
            return result
        }, function then() {
            test.assert(true, '... product is added to the shopping cart.')
        })
    })

    /** Checkout "Shipping" step is loaded */
    casper.then(function () {
        var url = subFront.getUrl("/checkout/")
        casper.open(url).then(function () {
            test.assert(true, 'Checkout "Shipping" step is loaded.')
            var css = "#shipping-method-buttons-container > div > button > span > span"
            casper.waitForSelector(css, function () {
                test.assertSelectorHasText(css, "Next", '... "Next" button is appeared.')
            }, function () {
                subTest.capture(optsCapture)
            })
        })
    })

    /** Filling registration data... */
    casper.then(function () {
        test.assert(true, "Filling registration data...")
        var cssFormLogin = "form.form.form-login"
        var cssFormAddress = "#co-shipping-form"
        casper.waitForSelector("input[name=telephone]", function () {
            casper.fillSelectors(cssFormLogin, {
                "#customer-email": address.email
            }, false)
            casper.fillSelectors(cssFormAddress, {
                "input[name=firstname]": address.nameFirst,
                "input[name=lastname]": address.nameLast,
                "input[name=company]": address.company,
                "input[name='street[0]']": address.street,
                "input[name=city]": address.city,
                "input[name=postcode]": address.zip,
                "input[name=telephone]": address.phone,
                "select[name=region_id]": address.state,
                "select[name=country_id]": address.country
            }, false)
            test.assert(true, "... email: " + address.email)
            test.assert(true, "... first name: " + address.nameFirst)
            test.assert(true, "... last name: " + address.nameLast)
            test.assert(true, "... company: " + address.company)
            test.assert(true, "... address: " + address.street)
            test.assert(true, "... city: " + address.city)
            test.assert(true, "... zip: " + address.zip)
            test.assert(true, "... phone: " + address.phone)
            test.assert(true, "... country id: " + address.country)
            test.assert(true, "... state id: " + address.state)
            test.assert(true, "... registration data is filled in")
            subTest.capture(optsCapture)
        })
    })

    // CSS selectors for checkout page
    var cssRadio = "div.payment-method.payment-method-braintree > div.payment-method-title.field.choice > label > span"
    var cssBtnPlace = "div.payment-method-braintree > div.payment-method-content > div.actions-toolbar > div > button > span"

    /** "Next" button is clicked */
    casper.then(function () {
        casper.click("#shipping-method-buttons-container > div > button > span > span")
        test.assert(true, '"Next" button is clicked.')
        casper.waitFor(function check() {
            var result = casper.visible(cssRadio)
            return result
        }, function then() {
            test.assert(casper.visible(cssRadio), '... payment step is loaded')
        })
    })

    /** Credit card payment method is selected */
    casper.waitForSelector(cssRadio, function () {
        casper.click(cssRadio)
        test.assert(true, "Credit card payment method is selected.")
    })

    /** Payment data is filled in */
    casper.waitWhileVisible('body > div.loading-mask', function () {
        // wait untill braintree form will be loaded
        /* switch to the child frames one by one and fill braintree payment form fields */
        /* (each field on the separate iframe) */
        casper.waitForSelector('iframe#braintree-hosted-field-number', function () {

            /** ... credit card number is filled */
            casper.page.switchToChildFrame('braintree-hosted-field-number')
            casper.waitForSelector('input#credit-card-number', function () {
                casper.fillSelectors('body', {'input#credit-card-number': '4111111111111111'}, false)
                test.assert(true, "... credit card number is filled")
                casper.page.switchToParentFrame()

                /** ... expiration month is filled */
                casper.page.switchToChildFrame('braintree-hosted-field-expirationMonth')
                casper.waitForSelector('input#expiration-month', function () {
                    casper.fillSelectors('body', {'input#expiration-month': '12'}, false)
                    test.assert(true, "... expiration month is filled")
                    casper.page.switchToParentFrame()

                    /** ... expiration year is filled */
                    casper.page.switchToChildFrame('braintree-hosted-field-expirationYear')
                    casper.waitForSelector('input#expiration-year', function () {
                        casper.fillSelectors('body', {'input#expiration-year': '21'}, false)
                        test.assert(true, "... expiration year is filled")
                        casper.page.switchToParentFrame()

                        /** ... CVV code is filled */
                        casper.page.switchToChildFrame('braintree-hosted-field-cvv')
                        casper.waitForSelector('input#cvv', function () {
                            casper.fillSelectors('body', {'input#cvv': '321'}, false)
                            test.assert(true, "... CVV code is filled")
                            casper.page.switchToParentFrame()
                        })
                    })
                })
            })
        })
    })

    /** "Place Order" button is clicked */
    casper.then(function () {
        casper.waitForSelector('#checkout-payment-method-load', function () {
            var css = "#checkout-payment-method-load > div > div > div.payment-method._active > div.payment-method-content > div.actions-toolbar > div > button > span"
            subTest.capture(optsCapture)
            casper.click(css)
            test.assert(true, '"Place Order" button is clicked.')
        })
    })

    /** Order placement is completed */
    casper.then(function () {
        casper.waitForSelector('.checkout-success', function () {
            test.assert(true, 'Order placement is completed.')
            subTest.capture(optsCapture)
        }, function () {
            subTest.capture(optsCapture)
        }, 30000)
    })

    /** "Create an Account" button is clicked */
    casper.then(function () {
        var cssBtnCreate = "input[type=submit]"
        casper.waitForSelector(cssBtnCreate, function () {
            test.assert(true, '... "Create an Account" button is visible.')
            casper.click(cssBtnCreate)
            test.assert(true, '"Create an Account" button is clicked.')
            subTest.capture(optsCapture)
        }, function () {
            subTest.capture(optsCapture)
        })
    })

    // TODO: check "letter will be send" message


    //  Gmail: get signup link


    /** Gmail login form is loaded */
    casper.then(function () {
        var url = "https://mail.google.com/mail/u/0/h/1pq68r75kzvdr/?v%3Dlui"
        casper.open(url).then(function () {
            subTest.capture(optsCapture)
            var cssBtnNext = "input#next"
            casper.waitForSelector(cssBtnNext, function then() {
                test.assert(true, 'Gmail login form is loaded.')
                casper.fillSelectors("#identifier-shown", {
                    "#Email": authGmailCustomer.email
                }, false)
                casper.click(cssBtnNext, "50%", "50%")

                /** fill in passwd */
                var cssFldPasswd = '#Passwd'
                casper.waitFor(function check() {
                    var result = casper.visible(cssFldPasswd)
                    return result
                }, function then() {
                    casper.fillSelectors("#password-shown", {
                        "#Passwd": authGmailCustomer.password
                    }, false)
                    casper.click(cssBtnNext, "50%", "50%")
                })
            }, function onTimeout() {
                subTest.capture(optsCapture)
            })
        })
    })

    /** User is logged into Gmail. */
    casper.then(function () {
        var cssEmail = "#guser > nobr > b"
        casper.waitForSelector(cssEmail, function () {
            var email = casper.fetchText(cssEmail)
            test.assertEquals(email, authGmailCustomer.email, "User is logged into Gmail.")
            subTest.capture(optsCapture)
        })
    })

    /** Signup email is found */
    casper.then(function () {
        var cssItem = "body > table:nth-child(16) > tbody > tr > td:nth-child(2) > table:nth-child(1) > tbody > tr > td:nth-child(2) > form > table.th > tbody > tr:nth-child(1) > td:nth-child(3) > a "
        casper.waitForSelector(cssItem, function () {
            var subject = casper.fetchText(cssItem)
            var isSignupEmail = (subject.indexOf("Welcome to MOBI Test Store") !== -1)
            casper.click(cssItem)
            test.assert(true, "Signup email is found.")
        })
    })

    /** "Set password" link is extracted */
    casper.then(function () {
        subTest.capture(optsCapture)
        var cssLink = "body > table:nth-child(16) > tbody > tr > td:nth-child(2) > table:nth-child(1) > tbody > tr > td:nth-child(2) > table:nth-child(4) > tbody > tr > td > table:nth-child(2) > tbody > tr:nth-child(4) > td > div > div > table > tbody > tr > td > table > tbody > tr:nth-child(2) > td > p:nth-child(3) > a"
        var href = casper.getElementAttribute(cssLink, "href")
        if (href) {
            var replaced = href.replace("http://www.google.com/url?q=", "")
            var decoded = decodeURIComponent(replaced)
            uriMageSignup = decoded
            test.assert(true, '"Set password" link is extracted.')
        } else {
            casper.echo('"Set password" link is NOT extracted.')
        }
    })

    /** Inbox is loaded again */
    casper.then(function () {
        var cssBackToInbox = "a.searchPageLink" // there are 2 links on the page
        casper.click(cssBackToInbox)
    })

    /** All inbox messages are checked */
    casper.then(function () {
        var cssCheckbox = "input[type=checkbox]"
        var elements = casper.getElementsInfo(cssCheckbox)
        elements.forEach(function (element) {
            casper.click("input[value='" + element.attributes.value + "']")
        })
        test.assert(true, "All inbox messages are checked.")
    })

    /** "Delete" button is pressed */
    casper.then(function () {
        var cssBtnDelete = "input[value='Delete']"
        casper.click(cssBtnDelete)
        test.assert(true, '"Delete" button is pressed.')
        subTest.capture(optsCapture)
    })


    // Magento Front: create password and login


    /** "Set a New Password" page is loaded */
    casper.then(function () {
        var url = uriMageSignup
        casper.open(url).then(function () {
            casper.waitForSelector("#form-validate > div > div > button", function () {
                test.assert(true, '"Set a New Password" page is loaded.')
            })
        })
    })

    /** Password value is filled in and submitted */
    casper.then(function () {
        casper.fillSelectors("#form-validate", {"#password": authMageCustomer.password}, false)
        casper.fillSelectors("#form-validate", {"#password-confirmation": authMageCustomer.password}, true)
        test.assert(true, "Password value is filled in and submitted.")
        subTest.capture(optsCapture)
    })

    /** Login form is filled in and submitted */
    casper.then(function () {
        casper.waitForSelector("#send2", function () {
            casper.fillSelectors("#login-form", {"#email": authMageCustomer.email}, false)
            casper.fillSelectors("#login-form", {"#pass": authMageCustomer.password}, true)
            test.assert(true, "Login form is filled in and submitted.")
            subTest.capture(optsCapture)
        })
    })

    /** Customer Dashboard is loaded */
    casper.then(function () {
        casper.waitForSelector("h1.page-title", function () {
            test.assert(true, "Customer Dashboard is loaded.")
            subTest.capture(optsCapture)
        })
    })

    /** Logout is performed */
    subFront.auth.logout(optsSubs)

    // Run scenario and finalize test.
    subTest.run(test)

})
