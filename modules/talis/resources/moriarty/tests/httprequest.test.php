<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'httprequest.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';

class HttpRequestTest extends PHPUnit_Framework_TestCase {

  function test_set_accept() {
    $req = new HttpRequest("GET", "http://example.org/");
    $req->set_accept("text/plain");

    $this->assertTrue( in_array('Accept: text/plain', $req->get_headers() ) );
  }

  function test_set_accept_overwrites_existing_value() {
    $req = new HttpRequest("GET", "http://example.org/");
    $req->set_accept("text/plain");
    $req->set_accept("application/xml");

    $this->assertFalse( in_array('Accept: text/plain', $req->get_headers() ) );
  }


  function test_set_content_type() {
    $req = new HttpRequest("GET", "http://example.org/");
    $req->set_content_type("text/plain");

    $this->assertTrue( in_array('Content-Type: text/plain', $req->get_headers() ) );
  }

  function test_set_content_type_overwrites_existing_value() {
    $req = new HttpRequest("GET", "http://example.org/");
    $req->set_content_type("text/plain");
    $req->set_content_type("application/xml");

    $this->assertFalse( in_array('Content-Type: text/plain', $req->get_headers() ) );
  }

  function test_set_body_does_not_set_content_length_since_this_breaks_http_digest_auth() {
    $req = new HttpRequest("GET", "http://example.org/");
    $req->set_body("now is the time");

    $this->assertFalse( in_array('Content-Length: 15', $req->get_headers() ) );
  }

  function test_parse_response_parses_all_responses() {
    $req = new HttpRequest("GET", "http://example.org/");

    $server_response = 'HTTP/1.1 401 Unauthorized
Via: 1.1 DORY
Connection: Keep-Alive
Proxy-Support: Session-Based-Authentication
Connection: Proxy-Support
Content-Length: 12
Date: Wed, 03 Oct 2007 00:15:59 GMT
Content-Type: text/plain; charset=UTF-8
WWW-Authenticate: Digest realm=&quot;bigfoot&quot;, domain=&quot;null&quot;, nonce=&quot;8Q84YxUBAABJP5W9FaNm7Fli2QGGO99o&quot;, algorithm=MD5, qop=&quot;auth&quot;

HTTP/1.1 200 OK
Via: 1.1 DORY
Connection: Keep-Alive
Proxy-Connection: Keep-Alive
Content-Length: 2103
Date: Wed, 03 Oct 2007 00:15:59 GMT
content-type: text/html; charset=UTF-8
Server: Bigfoot/5.282.18209
Cache-Control: max-age=7200, must-revalidate

foo';

    list($response_code,$response_headers,$response_body) = $req->parse_response($server_response);
    $this->assertEquals( 200, $response_code);

  }


  function test_constructor_can_set_auth_from_credentials() {
    $req = new HttpRequest("GET", "http://example.org/", new FakeCredentials());
    $this->assertEquals( "user:pwd", $req->auth );
  }


}
?>
