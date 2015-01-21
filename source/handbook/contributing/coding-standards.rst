================
Coding Standards
================

Introduction
------------

ONGR development follows `TDD <http://en.wikipedia.org/wiki/Test-driven_development>`_ methodology, so it's required that all code is covered with automated tests.

ONGR uses PSR-2 and `Symfony Coding Standards`_ with `Symfony Conventions <http://symfony.com/doc/current/contributing/code/conventions.html>`_. In addition, there are other rules we agreed on (listed below).

To check code quality for our requirements we prepared the rule set for `Code Sniffer <https://github.com/squizlabs/PHP_CodeSniffer>`_ available `here <https://github.com/ongr-io/ongr-strict-standard>`_.


Example
-------

This code example displays many of the standard's features (adapted from `Symfony Coding Standards`_):

.. code-block:: php

    /*
     * This file is part of the ONGR package.
     *
     * Copyright (c) 2014-${YEAR} NFQ Technologies UAB
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    namespace ONGR\FooBundle;

    use ONGR\WhateverBundle\WhateverUtility;

    /**
     * Coding standards demonstration.
     *
     * Class summary above must describe class' responsibility.
     */
    class FooBar
    {
        const SOME_CONST = 42;

        /**
         * @var FooService Doc should be on the same line as `@var` unless multiple lines are needed.
         */
        private $fooBar;

        /**
         * @param string $dummy Some argument description.
         */
        public function __construct($dummy)
        {
            // Note that __construct, setters, getters, etc. do not require short summary.

            $this->fooBar = $this->transformText($dummy);
        }

        /**
         * Transform text in a magic way.
         *
         * @param string $dummy   Some argument description.
         * @param array  $options Optional options.
         *
         * @return string|null Transformed input.
         *
         * @throws \RuntimeException
         */
        private function transformText($dummy, array $options = [])
        {
            $mergedOptions = array_merge(
                [
                    'some_default' => 'values',
                    'another_default' => 'more values',
                ],
                $options
            );

            if ($dummy === true) {
                return;
            }

            if ($dummy === 'string') {
                if ($mergedOptions['some_default'] === 'values') {
                    return substr($dummy, 0, 5);
                }

                return ucwords($dummy);
            }

            throw new \RuntimeException(sprintf('Unrecognized dummy option "%s"', $dummy));
        }
    }

..

.. important:: Don't forget to leave single empty line before license header, namespace, uses and class comment block. All uses should be listed in alphabetical order without empty lines.

Tickets and releases
--------------------

Task has been completed, if:

#. Feature is implemented.
#. New functionality is covered with automated tests or code debt is being recorded as an issue.
#. Feature has been documented in documentation directory.

Release:

#. Every release must have some valuable description or list of changes (links to PRs are recommended).
#. Every commit message should contain short description of what was done in it.

   i. No need to include feature suffix. PR's are used for grouping commits into features.
   ii. It's not recommended to mention issue number (e.g. ``Fixed price handling, closes #123``). Better to link commit hash in the issue. Otherwise, it's hard to change wrong issue number in the commit.

Documenting code
----------------

#. Use ``{@inheritdoc}`` when extending abstract methods or implementing interfaces instead of rewriting anything.
#. If method does not return any result, ``@return`` annotation must be omitted.
#. Comments must (1) start with capital letter, (2) have a single space between comment symbols and first letter and (3) must include period at the end. E.g. ``// This is a short comment.``
#. PHPDoc comments must have single empty lines between and after ``@param`` and ``@return`` tags block. ``@throws`` goes after ``@return``. E.g.

.. code-block:: php

    /**
     * Relocates resources to memory.
     *
     * @param bool $force Force relocation.
     * @param int  $count Number of retries.
     *
     * @return int
     *
     * @throws \Exception
     */

Structure
---------

#. When method ``foo`` calls methods ``bar`` and ``baz``, they should be organized in the following order in the same class: first ``foo``, then ``bar`` and ``baz`` (not ``bar``, ``baz``, ``foo``). This is because a developer is usually reading the code top-down, not bottom-up. Therefore, ``@dataProvider`` case provider should go above it's test.

Testing
-------

#. Tests are distributed into two types: Unit and Functional.
#. Unit tests naming and namespaces must mirror bundle structure.
#. Functional tests should be named by tested functionality. It's recommended to group integration tests into namespaces of similar functionality.

Misc.
-----

#. ONGR license header must be used for every PHP file on ONGR bundles.
#. Short array syntax must be used in PHP code.
#. Imagine class has a setter for whatever property and this property is used on other method. Then, ``\LogicException`` must be thrown if we are trying to call method with no value set (except cases when method actually can work without property value).
#. When using PSR-3 logger in a class, you must implement ``LoggerAwareInterface`` and use ``LoggerAwareTrait``.
#. When using service as a symfony container aware trait, you must use ``ContainerAwareTrait``.
#. Try to avoid using very strict dependencies such as (``2.3.*``). We should always stick to latest minor release (like ``~2.3``)

.. _Symfony Coding Standards: http://symfony.com/doc/current/contributing/code/standards.html
