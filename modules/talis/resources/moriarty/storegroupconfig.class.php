<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'simplegraph.class.php';
require_once MORIARTY_DIR. 'queryprofile.class.php';

class StoreGroupConfig {

  var $uri;
  var $request_factory;
  var $credentials;

  function __construct($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function get_first_query_profile() {
    return new QueryProfile( $this->uri . '/queryprofiles/1', $this->credentials);
  }

}

?>
