<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'storegroupconfig.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';

class StoreGroupConfigTest extends PHPUnit_Framework_TestCase {

  function test_get_first_query_profile() {
    $config = new StoreGroupConfig("http://example.org/group/config");
    $this->assertEquals("http://example.org/group/config/queryprofiles/1", $config->get_first_query_profile()->uri);
  }

}
?>
