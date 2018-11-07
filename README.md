Symfony4 coding test
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
Please, run a test with the usual command, e.g. (if you want to run the test #3):
```bash
./bin/phpunit --filter tests/Controller/Api/ProductApiTest.php --filter testGetSingleProduct
```

## Discounts
Please, the structures and data for adding different kinds of discounts have been provided. Please, check
carefully the content of `src\Model\Promotion` folder. Here you can find some kinds of discounts and promotions,
like that reported within your guidelines for the bonus test #9.
Please, consider also that some fixtures have been created for these promotions too. Only the test is missing,
the only test that I have not implemented: I considered the code for fixtures as samples which can take the place
of the PHP Unit test.