# Classic PhpUnit Bridge

This repository contains bootstrap files for unit and functional which works in normal and composer installation of shopware.

### Why?

* Independent of Core PHPUnit version
    * Currently mostly plugin developers are using the same PhpUnit version as from core
* Works in the same way across all versions
* Simple setup


### Installation

Require `frosh/shopware-classic-phpunit-bridge` as dev requirement. And set following bootstrap files in your phpunit file
* Unit => `../vendor/frosh/shopware-classic-phpunit-bridge/src/Bootstrap/Unit/bootstrap.php`
* Functional => `../vendor/frosh/shopware-classic-phpunit-bridge/src/Bootstrap/Functional/bootstrap.php`

### Example Plugin

Checkout FroshProfiler test suite
