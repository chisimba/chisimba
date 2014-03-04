<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'httprequest.class.php';

class HttpRequestFactory {
  function make( $method, $uri, $credentials = null ) {
    return new HttpRequest( $method, $uri, $credentials );
  }
}
?>
