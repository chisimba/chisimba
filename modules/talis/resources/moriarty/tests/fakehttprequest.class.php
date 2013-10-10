<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'httprequest.class.php';

class FakeHttpRequest extends HttpRequest {
  var $was_executed  = false;
  var $response;
  var $auth;

  function FakeHttpRequest( $response ) {
    $this->was_executed = false;
    $this->response = $response;
  } 

  function execute() {
    $this->was_executed = true;
    return $this->response;
  }

  function was_executed() {
    return $this->was_executed;
  }

  function set_auth($auth_string) {
    $this->auth = $auth_string;
  }
  
  function get_auth() {
    return $this->auth;
  }

}
?>
