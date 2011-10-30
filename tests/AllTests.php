<?php
/**
 * Master Unit Test Suite file for Services_ProjectHoneyPot
 *
 * This top-level test suite file organizes
 * all class test suite files,
 * so that the full suite can be run
 * by PhpUnit or via "pear run-tests -u".
 *
 * PHP version 5
 *
 * @category   Testing
 * @package    Services_ProjectHoneyPot
 * @subpackage UnitTesting
 * @author     Chuck Burgess <ashnazg@php.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id: AllTests.php 318594 2011-10-30 12:15:32Z till $
 * @link       http://pear.php.net/package/Services_ProjectHoneyPot
 * @since      0.4.0
 */

$svnOrNot = '@package_version@';
if ($svnOrNot == '@package_version@') {
    // we run from svn and fiddle with the include_path
    set_include_path(
        realpath(dirname(__FILE__) . '/../')
        . PATH_SEPARATOR
        . get_include_path()
    );
}


/**
 * Check PHP version... PhpUnit v3+ requires at least PHP v5.1.4
 */
if (version_compare(PHP_VERSION, "5.1.4") < 0) {
    // Cannnot run test suites
    echo 'Cannot run test suite via PhpUnit... requires at least PHP v5.1.4.' . PHP_EOL;
    echo 'Use "pear run-tests -p File_IMC" to run the PHPT tests directly.' . PHP_EOL
;
    exit(1);
}


/**
 * Derive the "main" method name
 * @internal PhpUnit would have to rename PHPUnit_MAIN_METHOD to PHPUNIT_MAIN_METHOD
 *           to make this usage meet the PEAR CS... we cannot rename it here.
 */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Services_ProjectHoneyPot_AllTests::main');
}


/*
 * Files needed by PhpUnit
 */
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Extensions/PhptTestSuite.php';

/*
 * You must add each additional class-level test suite file here
 */
// there are no PhpUnit test files... only PHPTs.. so nothing is listed here

/**
 * directory where PHPT tests are located
 */
define('Services_ProjectHoneyPot_DIR_PHPT', dirname(__FILE__));

/**
 * Master Unit Test Suite class for Services_ProjectHoneyPot
 *
 * This top-level test suite class organizes
 * all class test suite files,
 * so that the full suite can be run
 * by PhpUnit or via "pear run-tests -up File_IMC".
 *
 * @category   Testing
 * @package    Services_ProjectHoneyPot
 * @subpackage UnitTesting
 * @author     Chuck Burgess <ashnazg@php.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/Services_ProjectHoneyPot
 * @since      0.5.4
 */
class Services_ProjectHoneyPot_AllTests
{

    /**
     * Launches the TextUI test runner
     *
     * @return void
     * @uses PHPUnit_TextUI_TestRunner
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }


    /**
     * Adds all class test suites into the master suite
     *
     * @return PHPUnit_Framework_TestSuite a master test suite
     *                                     containing all class test suites
     * @uses PHPUnit_Framework_TestSuite
     */ 
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite(
            'File_IMC Full Suite of Unit Tests');

        /*
         * You must add each additional class-level test suite name here
         */
        //$suite->addTestSuite('foo');

        /*
         * add PHPT tests
         */
        $phpt = new PHPUnit_Extensions_PhptTestSuite(Services_ProjectHoneyPot_DIR_PHPT);
        $suite->addTestSuite($phpt);

        return $suite;
    }
}

/**
 * Call the main method if this file is executed directly
 * @internal PhpUnit would have to rename PHPUnit_MAIN_METHOD to PHPUNIT_MAIN_METHOD
 *           to make this usage meet the PEAR CS... we cannot rename it here.
 */
if (PHPUnit_MAIN_METHOD == 'Services_ProjectHoneyPot_AllTests::main') {
    Services_ProjectHoneyPot_AllTests::main();
}

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
