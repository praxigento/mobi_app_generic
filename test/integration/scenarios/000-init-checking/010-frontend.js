var url = 'http://mobi2.mage.test.prxgt.com/';
var scenario = '000';
var scene = '010';
var viewport = {width: 800, height: 800};

casper.test.begin(
    'Scene ' + scenario + '/' + scene + ': frontend initial checking:',
    9,
    function suite_frontend_checking(test) {

        /**
         * Load homepage.
         */
        casper.start(url, function () {
            casper.page.viewportSize = viewport;
            // this.echo(this.status(true));
            test.assertExists('div.page-wrapper', 'homepage is loaded.');
            /* sceenshot: initial */
            var screenFile = 'screen/' + scenario + '/' + scene + '/010.png';
            this.capture(screenFile);
        })

        /**
         * Validate Baltic store: 2 locales and 2 currencies.
         */
        /* click on language switcher */
        casper.waitForSelector('#switcher-language', function () {
            var code = casper.fetchText('#switcher-language-trigger > strong > span');
            test.assertEquals(code.trim(), 'EN', 'EN code exists in language switcher.');
            casper.click('div#switcher-language-trigger > strong > span');
        });

        casper.waitForSelector('#switcher-language-trigger + DIV', function () {
            var code = casper.fetchText('#ui-id-2 > li > a');
            test.assertEquals(code.trim(), 'RU', 'RU code exists in language switcher.');
            var screenFile = 'screen/' + scenario + '/' + scene + '/020.png';
            casper.capture(screenFile);
        });

        /* click on currency switcher */
        casper.waitForSelector('#switcher-currency', function () {
            var code = casper.fetchText('#switcher-currency-trigger > strong > span');
            test.assertEquals(code.trim(), 'EUR - Euro', 'EUR code exists in currency switcher.');
            casper.click('#switcher-currency-trigger > strong > span');
        });

        casper.waitForSelector('#switcher-currency-trigger + DIV', function () {
            var code = casper.fetchText('#ui-id-1 > li > a');
            test.assertEquals(code.trim(), 'USD - US Dollar', 'USD code exists in currency switcher.');
            var screenFile = 'screen/' + scenario + '/' + scene + '/030.png';
            casper.capture(screenFile);
        });

        /**
         * Switch to Russian store and validate currencies
         */

        /* switch to Russian store */
        casper.waitForSelector('.switcher-dropdown[aria-hidden="true"]', function () {
            test.assertExists('.switcher-dropdown[aria-hidden="true"]', 'store switcher is collapsed.');
            casper.click('DIV#switcher-store-trigger > STRONG > SPAN');
        });

        casper.waitForSelector('.switcher-dropdown[aria-hidden="false"]', function () {
            test.assertExists('.switcher-dropdown[aria-hidden="false"]', 'store switcher is expanded.');
            var screenFile = 'screen/' + scenario + '/' + scene + '/040.png';
            casper.capture(screenFile);
        });

        casper.waitForSelector('#switcher-store > div > ul > li > a', function () {
            casper.click('#switcher-store > div > ul > li > a');
        });

        casper.then(function () {
            var screenFile = 'screen/' + scenario + '/' + scene + '/050.png';
            casper.capture(screenFile);
        });

        /* click on currency switcher */
        casper.waitForSelector('#switcher-currency', function () {
            var code = casper.fetchText('#switcher-currency-trigger > strong > span');
            test.assertEquals(code.trim(), 'USD - Доллар США', 'USD code exists in currency switcher (Russian store).');
            casper.click('#switcher-currency-trigger > strong > span');
        });

        casper.waitForSelector('#switcher-currency-trigger + DIV', function () {
            var code = casper.fetchText('#ui-id-1 > li > a');
            test.assertEquals(code.trim(), 'EUR - Евро', 'EUR code exists in currency switcher (Russian store).');
            var screenFile = 'screen/' + scenario + '/' + scene + '/060.png';
            casper.capture(screenFile);
        });


        casper.run(function () {
            test.done()
        })
    }
)