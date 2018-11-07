ACME TEST
==========

## Introduction
I assume the products' costs in USD. Therefore, no currency entity is provided.
Moreover, please, consider that for this test I have not considered all the stuffs about managing tokens
(e.g., refresh).

Finally, a lot of features more should be implemented (e.g. verification of shipping address, calculation
of taxes according to the country, etc.). All of them are not considered here, since the test should be
completed ASAP

## Installation
After cloning the git, please you can run
```bash
composer install
php bin/console acme:setup
```
The package will be installed with all the required fixtures

## Tests
Please, `tests` folder contains the PHP Unit Tests required by your guidelines.
Please, run the with the usual command, e.g.:
```bash
./bin/phpunit --filter tests/Controller/Api/ProductApiTest.php --filter testGetSingleProduct
```
in order to run the test #3