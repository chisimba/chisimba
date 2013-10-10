<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'credentials.class.php';

class CredentialsTest extends PHPUnit_Framework_TestCase {
  function test_get_auth() {
    $creds = new Credentials("scooby", "doodoo");
    $this->assertEquals( "scooby:doodoo", $creds->get_auth() );
  }
}
?>
