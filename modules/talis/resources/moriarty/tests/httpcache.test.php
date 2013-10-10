<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'httpcache.class.php';

class HttpCacheTest extends PHPUnit_Framework_TestCase {
  function test_get_cache_filename() {
    $request = new HttpRequest('GET', 'http://example.org/');
    $request->set_accept('*/*');
    
    $expected_filename = 'tmp' . DIRECTORY_SEPARATOR . md5('<http://example.org/>*/*'); 
    
    $cache = new HttpCache('tmp' . DIRECTORY_SEPARATOR);
    $this->assertEquals( $expected_filename, $cache->get_cache_filename($request));
    
  }

  function test_get_cache_filename_adds_missing_directory_separator() {
    $request = new HttpRequest('GET', 'http://example.org/');
    $request->set_accept('*/*');
    
    $expected_filename = 'tmp' . DIRECTORY_SEPARATOR . md5('<http://example.org/>*/*'); 
    
    $cache = new HttpCache('tmp');
    $this->assertEquals( $expected_filename, $cache->get_cache_filename($request));
  }


  function test_get_cache_filename_normalises_accept_header() {
    $request1 = new HttpRequest('GET', 'http://example.org/');
    $request1->set_accept('text/html,application/xml');

    $request2 = new HttpRequest('GET', 'http://example.org/');
    $request2->set_accept('application/xml,text/html');
    
    $cache = new HttpCache('tmp');

    $this->assertEquals($cache->get_cache_filename($request1), $cache->get_cache_filename($request2));
  }

}

?>
