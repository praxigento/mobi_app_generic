# Generic MOBI application for Magento v2

[![Build Status](https://travis-ci.org/praxigento/mobi_app_generic_mage2.svg)](https://travis-ci.org/praxigento/mobi_app_generic_mage2/)

## Files

* [./cfg](./cfg) - templates for scripts that are used in the deployment process;
* [./live](./live) - live (production) instance of the application (with own composer.json);
* [./src](./src) - source files for the main module of the application;
* [./test](./test) - test scripts for the own code of the application's main module;
* [./work](./work) - development instance of the application (with own composer.json);
* [./.travis.yml](./.travis.yml) - descriptor for Travis CI; 
* [./composer.json](./composer.json) - descriptor for the main module of the application (type: "magento2-module");
* *./templates.vars.live.json* - configuration variables for templates (live version); is not under version control; 
* *./templates.vars.work.json* - configuration variables for templates (development version); is not under version control;



## Installation

* [./live/](./live/) - root folder for live (production) version of the application.
* [./work/](./work/) - root folder for development version of the application.



## Code coverage

    $ phpdbg -qrr ./work/vendor/bin/phpunit --configuration ./dev/coverage/phpunit.dist.xml

Report is placed in `build/coverage/index.html`