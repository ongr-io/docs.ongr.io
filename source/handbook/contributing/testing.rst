Testing
=======
To run tests you need to have MySQL, Elasticsearch, PHP and PHPUnit. If you don't want to install it locally, we've prepared a virtual machine which will do the dirty work. Its the same VM, which is used for demo page, more details can be found on :doc:`/components/ongr-sandbox/index` page.

Each bundle can be tested separately. There is a `phpunit.xml` file in each bundle's root folder, so basically when you are in the root bundle directory, use command:

.. code-block:: bash

    vendor/bin/phpunit

..

If all the tests pass you will see an "OK" status at the bottom, otherwise phpunit will report how many of the tests failed, which ones and with what data.

ONGR uses basically two types of tests - unit and functional.

Unit tests are designed to test independent parts of the systems functionality (functions, methods, classes, etc.). Unit testing usually uses hard-coded preset data and mock objects. It checks the logic of a single isolated unit by checking whether the mock objects' methods are called, how many times they are called and if results are correct after execution.

Functional tests test a single process (e.g. a Symfony console command) and, by proxy, the interconnected units, their interaction, and whether the functionality really does what it is supposed to. It is done by checking the actual results in files/databases with the hard-coded expected results.
