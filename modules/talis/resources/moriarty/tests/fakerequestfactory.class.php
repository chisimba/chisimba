<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'httprequestfactory.class.php';

class FakeRequestFactory extends HttpRequestFactory {
  var $requests;

  function __construct() {
    $this->requests = array();
  }

  function register($method, $uri, $request ) {
    $this->requests[$method . ' ' . $uri] = $request;
  }

  function make( $method, $uri, $credentials = null) {
    if (array_key_exists( $method . ' ' . $uri, $this->requests) ) {
      $request = $this->requests[$method . ' ' . $uri];
      if ( $credentials != null) {
        $request->set_auth( $credentials->get_auth());
      }
      return $request;
    }

    $response = new HttpResponse();
    $response->status_code = 404;
      
    return new FakeHttpRequest( $response );
  }

}
?>
