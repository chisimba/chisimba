<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
  define('PHPUnit_MAIN_METHOD', 'Moriarty_AllTests::main');
}

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';

require_once MORIARTY_PHPUNIT_DIR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'Framework.php';
require_once MORIARTY_PHPUNIT_DIR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'TextUI' . DIRECTORY_SEPARATOR . 'TestRunner.php';

require_once MORIARTY_TEST_DIR . 'fakehttprequest.class.php';
require_once MORIARTY_TEST_DIR . 'fakerequestfactory.class.php';

require_once MORIARTY_TEST_DIR . 'changeset.test.php';
require_once MORIARTY_TEST_DIR . 'changesetbatch.test.php';
require_once MORIARTY_TEST_DIR . 'store.test.php';
require_once MORIARTY_TEST_DIR . 'metabox.test.php';
require_once MORIARTY_TEST_DIR . 'sparqlservice.test.php';
require_once MORIARTY_TEST_DIR . 'fieldpredicatemap.test.php';
require_once MORIARTY_TEST_DIR . 'valuepool.test.php';
require_once MORIARTY_TEST_DIR . 'httprequest.test.php';
require_once MORIARTY_TEST_DIR . 'credentials.test.php';
require_once MORIARTY_TEST_DIR . 'privategraph.test.php';
require_once MORIARTY_TEST_DIR . 'contentbox.test.php';
require_once MORIARTY_TEST_DIR . 'multisparqlservice.test.php';
require_once MORIARTY_TEST_DIR . 'jobqueue.test.php';
require_once MORIARTY_TEST_DIR . 'simplegraph.test.php';
require_once MORIARTY_TEST_DIR . 'config.test.php';
require_once MORIARTY_TEST_DIR . 'storecollection.test.php';
require_once MORIARTY_TEST_DIR . 'storegroup.test.php';
require_once MORIARTY_TEST_DIR . 'networkresource.test.php';
require_once MORIARTY_TEST_DIR . 'queryprofile.test.php';
require_once MORIARTY_TEST_DIR . 'storegroupconfig.test.php';
require_once MORIARTY_TEST_DIR . 'httpcache.test.php';
require_once MORIARTY_TEST_DIR . 'facetservice.test.php';


error_reporting(E_ALL );
function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}
set_error_handler('exceptions_error_handler');

function debug_exception_handler($ex) {
  echo "Error : ".$ex->getMessage()."\n";
  echo "Code : ".$ex->getCode()."\n";
  echo "File : ".$ex->getFile()."\n";
  echo "Line : ".$ex->getLine()."\n";
  echo $ex->getTraceAsString()."\n";
  exit;
}
set_exception_handler('debug_exception_handler');


class Moriarty_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Moriarty Framework Tests');

        $suite->addTestSuite('StoreTest');
        $suite->addTestSuite('SparqlServiceTest');
        $suite->addTestSuite('FieldPredicateMapTest');
        $suite->addTestSuite('ChangesetBatchTest');
        $suite->addTestSuite('ChangesetTest');
        $suite->addTestSuite('CredentialsTest');
        $suite->addTestSuite('ValuePoolTest');
        $suite->addTestSuite('HttpRequestTest');
        $suite->addTestSuite('PrivateGraphTest');
        $suite->addTestSuite('MetaboxTest');
        $suite->addTestSuite('ContentboxTest');
        $suite->addTestSuite('MultiSparqlServiceTest');
        $suite->addTestSuite('JobQueueTest');
        $suite->addTestSuite('SimpleGraphTest');
        $suite->addTestSuite('ConfigTest');
        $suite->addTestSuite('StoreCollectionTest');
        $suite->addTestSuite('StoreGroupTest');
        $suite->addTestSuite('NetworkResourceTest');
        $suite->addTestSuite('QueryProfileTest');
        $suite->addTestSuite('StoreGroupConfigTest');
        $suite->addTestSuite('HttpCacheTest');
        $suite->addTestSuite('FacetServiceTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Moriarty_AllTests::main') {
    Moriarty_AllTests::main();
}

?>
