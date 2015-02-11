Testing
=======
To run tests you need to have Mysql, elastic search, PHP and PHP Unit. If you don't want to install it locally, we've prepared a virtual machine which will do the dirty work. Its the same VM, which is used for demo page, more details can be found on :doc:`/components/ongr-sandbox/index` page.

Each bundle can be tested separately. There is a `phpunit.xml` file in each bundle's root folder, so basically when you are in the root bundle directory (f.e. `/vendor/ongr/elasticsearch-bundle`), use command:

.. code-block:: bash

    vendor/bin/phpunit

..

If all the tests pass you will see an "OK" status at the bottom, otherwise phpunit will report how many of the tests failed, which ones and with what data.

ONGR uses basically two types of tests - functional and integrational.

Functional tests are designed to test independent parts of the systems functionality (functions, methods, classes, etc.). Functional testing usually uses hard-coded preset data, and checks if the results are correct after execution.

Integration tests check if two or more components, which have already been tested functionally, interact with each other as expected. Integration tests usually use files or database connections as data sources.
