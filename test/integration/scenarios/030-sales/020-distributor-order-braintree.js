'use strict'
// shortcuts globals
var casper = casper
var mobi = mobi
var conf = mobi.opts.conf
var address = mobi.opts.address.distributor
var confAuth = mobi.opts.auth.mage.front.customer01
var subFront = mobi.sub.mage.front
var subTest = mobi.sub.test


// local vars
var pack = '030'
var scenario = '020'
var suite = {pack: pack, scenario: scenario}
var optsCapture = {suite: suite}
var optsSubs = {suite: suite, screen: {save: false}}
var desc = 'scene ' + pack + '/' + scenario + ': distributor order (3 products, different qty, braintree payment):'
var orderIdInc // incremental ID of the order to use in API to get PV

// function itself
casper.test.begin(desc, function suite_030_020(test) {

    // Start scenario and setup phantom/capser
    subTest.start()

    // Define options: authentication data, store and currency
    var opts = JSON.parse(JSON.stringify(optsSubs))
    opts.username = confAuth.email
    opts.userpass = confAuth.password
    opts.store = conf.app.store.baltic
    opts.currency = conf.app.currency.eur

    // authenticate then switch store & currency
    subFront.auth(opts)
    subFront.switch.store(optsSubs)
    subFront.switch.currency(optsSubs)

        /**
         * Go to product page and add 'Black Walnut' (215san) to the cart.
         */
        casper.then(function () {

            var url = subFront.getUrl('catalog.product.san215')
            casper.open(url)

            /* load product page */
            casper.waitForSelector('.product-info-main', function () {
                test.assert(true, 'Product-215 data is loaded.')
                subTest.capture(optsCapture)
            })

            // TODO: add subfunction to get cart counter value

            /* set qty=3 */
            casper.waitForSelector('#qty', function () {
                casper.fillSelectors('form#product_addtocart_form', {'input#qty': '3'}, false)
                test.assert(true, 'Product quantity is set to 3.')
                subTest.capture(optsCapture)
            })

            /* click 'add to cart' button*/
            casper.waitForSelector('#product-addtocart-button', function () {
                casper.click('#product-addtocart-button > span')
                test.assert(true, "'Add to Cart' button is clicked.")
            })


            var css = 'span.counter-number'
            casper.waitFor(function check() {
                var text = casper.fetchText(css)
                var result = ('' != text.trim()) // we don't know initial value of the products in the cart
                return result
            }, function then() {
                test.assert(true, '... product is added to the shopping cart.')
                subTest.capture(optsCapture)
            })

        })

        /**
         * Go to product page and add 'Alfalfa' (203san) to the cart.
         */
        casper.then(function () {

            var url = subFront.getUrl('catalog.product.san203')
            casper.open(url)

            /* load product page*/
            casper.waitForSelector('.product-info-main', function () {
                test.assert(true, 'Product-203 data is loaded.')
                subTest.capture(optsCapture)
            })

            /* set qty=5 */
            casper.waitForSelector('#qty', function () {
                casper.fillSelectors('form#product_addtocart_form', {'input#qty': '5'}, false)
                test.assert(true, 'Product quantity is set to 5.')
                subTest.capture(optsCapture)
            })

            /* click 'add to cart' button*/
            casper.waitForSelector('#product-addtocart-button', function () {
                casper.click('#product-addtocart-button > span')
                test.assert(true, "'Add to Cart' button is clicked.")
            })


            var css = 'span.counter-number'
            casper.waitFor(function check() {
                var text = casper.fetchText(css)
                var result = ('' != text.trim()) // we don't know initial value of the products in the cart
                return result
            }, function then() {
                test.assert(true, '... product is added to the shopping cart.')
                subTest.capture(optsCapture)
            })

        })

        /**
         * Go to product page and add 'Bee Royal' (212san) to the cart.
         */
        casper.then(function () {

            var url = subFront.getUrl('catalog.product.san212')
            casper.open(url)

            /* load product page*/
            casper.waitForSelector('.product-info-main', function () {
                test.assert(true, 'Product-212 data is loaded.')
                subTest.capture(optsCapture)
            })

            /* set qty=4 */
            casper.waitForSelector('#qty', function () {
                casper.fillSelectors('form#product_addtocart_form', {'input#qty': '4'}, false)
                test.assert(true, 'Product quantity is set to 4.')
                subTest.capture(optsCapture)
            })

            /* click 'add to cart' button*/
            casper.waitForSelector('#product-addtocart-button', function () {
                casper.click('#product-addtocart-button > span')
                test.assert(true, "'Add to Cart' button is clicked.")
            })


            var css = 'span.counter-number'
            casper.waitFor(function check() {
                var text = casper.fetchText(css)
                var result = ('' != text.trim()) // we don't know initial value of the products in the cart
                return result
            }, function then() {
                test.assert(true, '... product is added to the shopping cart.')
                subTest.capture(optsCapture)
            })

        })

        /** Go to checkout and place the order. */
        casper.then(function () {

            var url = subFront.getUrl('checkout')
            casper.open(url)

            casper.waitForSelector('div#checkout', function () {
                test.assert(true, 'Checkout page is loaded.')
            })


            /** Load shipping methods and check shipping fee */
            casper.waitForSelector('#shipping-method-buttons-container', function () {
                test.assert(true, 'Shipping methods are loaded.')
                subTest.capture(optsCapture)
                var css = '#checkout-shipping-method-load > table > tbody > tr > td.col.col-price > span.price-including-tax > span > span'
                casper.waitForSelector(css, function () {
                    var text = casper.fetchText(css)
                    // test.assertEquals(text.trim(), '€4.28', "... shipping fee is equal to '€4.28'.")
                })
            })

            var css = '#shipping-method-buttons-container > div > button > span > span'
            casper.waitForSelector(css, function () {

                /** Fill in the form with  */
                if (casper.visible("input[name='firstname']")) {
                    var cssFormAddress = "#co-shipping-form"
                    casper.waitForSelector("input[name=telephone]", function () {
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
                        subTest.capture(optsCapture)
                    })
                }


                casper.click(css)
                test.assert(true, 'Shipping is proceed in order placement.')
            })

            casper.waitForSelector('#braintree', function () {
                test.assert(true, 'Payment methods are loaded.')
                subTest.capture(optsCapture)
            }, null, 20000)

            /** Select 'braintree' method */
            casper.waitForSelector('label[for=braintree]', function then() {
                casper.click('label[for=braintree]')
                test.assert(true, 'Payment method is selected (braintree).')
            }, function onTimeout() {
                subTest.capture(optsCapture)
            })

            /* wait untill braintree form will be loaded */
            casper.waitWhileVisible('body > div.loading-mask', function () {

                /* switch to the child frames one by one and fill braintree payment form fields */
                /* (each field on the separate iframe) */
                casper.waitForSelector('iframe#braintree-hosted-field-number', function () {

                    /* credit card number */
                    casper.page.switchToChildFrame('braintree-hosted-field-number')
                    casper.waitForSelector('input#credit-card-number', function () {
                        casper.fillSelectors('body', {'input#credit-card-number': '4111111111111111'}, false)
                        casper.page.switchToParentFrame()

                        /* exp. month */
                        casper.page.switchToChildFrame('braintree-hosted-field-expirationMonth')
                        casper.waitForSelector('input#expiration-month', function () {
                            casper.fillSelectors('body', {'input#expiration-month': '12'}, false)
                            casper.page.switchToParentFrame()

                            /* exp. year */
                            casper.page.switchToChildFrame('braintree-hosted-field-expirationYear')
                            casper.waitForSelector('input#expiration-year', function () {
                                casper.fillSelectors('body', {'input#expiration-year': '21'}, false)
                                casper.page.switchToParentFrame()

                                /* cvv code */
                                casper.page.switchToChildFrame('braintree-hosted-field-cvv')
                                casper.waitForSelector('input#cvv', function () {
                                    casper.fillSelectors('body', {'input#cvv': '321'}, false)
                                    casper.page.switchToParentFrame()
                                })
                            })
                        })
                    })
                })
            }, function onTimeout() {
                subTest.capture(optsCapture)
            })
        })

        /** Submit payment data and place the order */
        casper.then(function () {
            casper.waitForSelector('#checkout-payment-method-load', function () {
                var css = '#checkout-payment-method-load > div > div > div.payment-method._active > div.payment-method-content > div.actions-toolbar > div > button > span'
                casper.click(css)
                test.assert(true, 'Order placement is started.')
                subTest.capture(optsCapture)
            })
        })

        /** Validate successfull placement (wait max 30 sec) */
        casper.waitForSelector('.checkout-success', function () {
            test.assert(true, 'Order placement is completed.')
            subTest.capture(optsCapture)
            var cssOrderIdInc = '#maincontent > div.columns > div > div.checkout-success > p:nth-child(1) > a > strong'
            var text = casper.fetchText(cssOrderIdInc)
            orderIdInc = text.trim()
            casper.echo('Order #' + orderIdInc + ' is created.')
        }, null, 30000)

    /** Logout is performed */
    subFront.auth.logout(optsSubs)

    casper.then(function () {
            var url = subFront.getUrl('/rest/V1/prxgt/pv/sale/order/get')
            var data = {data: {id_inc: orderIdInc}}
            casper.open(url, {
                    method: "post",
                    headers: {
                        "Accept": "*/*",
                        "Content-Type": "application/json"
                    },
                    data: data
                }
            ).then(function (response) {
                var pvExpected = 94.4
                var content = casper.getPageContent()
                casper.echo(content)
                var json = JSON.parse(content);
                test.assertEqual(json.data.total, pvExpected, 'Order #' + orderIdInc + ' has expected PV (' + pvExpected + ').')
            })

        }
    )

    // Run scenario and finalize test.
    subTest.run(test)
    }
)
