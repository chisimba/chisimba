<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'credentials.class.php';

class FakeCredentials extends Credentials {
  var $credentials;

  function __construct() {
    parent::__construct('user', 'pwd');
  }

}
?>
