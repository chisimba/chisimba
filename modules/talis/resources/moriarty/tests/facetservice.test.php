<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';
require_once MORIARTY_DIR . 'facetservice.class.php';

class FacetServiceTest extends PHPUnit_Framework_TestCase {

  function test_facets_gets_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/services/facet?query=dog&fields=subject&top=10&output=xml", $fake_request );

    $ss = new FacetService("http://example.org/store/services/facet");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->facets( 'dog', array('subject') );
    $this->assertTrue( $fake_request->was_executed() );
  }


  function test_facets_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/services/facet?query=dog&fields=subject&top=10&output=xml", $fake_request );

    $ss = new FacetService("http://example.org/store/services/facet");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->facets( 'dog', array('subject') );
    $this->assertTrue( in_array('Accept: application/xml', $fake_request->get_headers() ) );
  }

  function test_facets_uses_fields() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/services/facet?query=dog&fields=subject+tag+year&top=10&output=xml", $fake_request );

    $ss = new FacetService("http://example.org/store/services/facet");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->facets( 'dog', array('subject', 'tag', 'year') );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_facets_uses_top() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/services/facet?query=dog&fields=subject&top=16&output=xml", $fake_request );

    $ss = new FacetService("http://example.org/store/services/facet");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->facets( 'dog', array('subject'), 16 );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_facets_to_array() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<facet-results xmlns="http://schemas.talis.com/2007/facet-results#"><head><query>dog</query><fields>tag</fields><top>10</top><output>xml</output></head><fields><field name="tag"><term value="dogs" number="1" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22dogs%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22dogs%22" /><term value="walking" number="1" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22walking%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22walking%22" /><term value="pets" number="1" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22pets%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22pets%22" /></field></fields></facet-results>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/services/facet?query=dog&fields=tag&top=10&output=xml", $fake_request );

    $ss = new FacetService("http://example.org/store/services/facet");
    $ss->request_factory = $fake_request_factory;
  
    $facets = $ss->facets_to_array( 'dog', array('subject') );
    $this->assertTrue( is_array( $facets ) );
  }

  function test_parse_facet_xml_returns_array() {
    $xml = '<facet-results xmlns="http://schemas.talis.com/2007/facet-results#"><head><query>dog</query><fields>tag</fields><top>10</top><output>xml</output></head><fields><field name="tag"><term value="dogs" number="1" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22dogs%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22dogs%22" /><term value="walking" number="1" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22walking%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22walking%22" /><term value="pets" number="1" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22pets%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22pets%22" /></field></fields></facet-results>';

    $ss = new FacetService("http://example.org/store/services/facet");
    $facets = $ss->parse_facet_xml( $xml );
    $this->assertTrue( is_array( $facets ) );
  }

  function test_parse_facet_xml_parses_facets() {
    $xml = '<facet-results xmlns="http://schemas.talis.com/2007/facet-results#"><head><query>dog</query><fields>tag</fields><top>10</top><output>xml</output></head><fields><field name="tag"><term value="dogs" number="5" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22dogs%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22dogs%22" /><term value="walking" number="2" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22walking%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22walking%22" /><term value="pets" number="1" facet-uri="http://api.talis.com/stores/kniblet-dev1/services/facet?fields=tag&amp;top=10&amp;output=xml&amp;query=dog+AND+tag%3A%22pets%22" search-uri="http://api.talis.com/stores/kniblet-dev1/items?query=dog+AND+tag%3A%22pets%22" /></field></fields></facet-results>';

    $ss = new FacetService("http://example.org/store/services/facet");
    $facets = $ss->parse_facet_xml( $xml );
    $this->assertEquals( 1, count( $facets ) );
    $this->assertEquals( 3, count( $facets['tag'] ) );
    $this->assertEquals( 'dogs', $facets['tag'][0]['value'] );
    $this->assertEquals( 'walking', $facets['tag'][1]['value'] );
    $this->assertEquals( 'pets', $facets['tag'][2]['value'] );

    $this->assertEquals( '5', $facets['tag'][0]['number'] );
    $this->assertEquals( '2', $facets['tag'][1]['number'] );
    $this->assertEquals( '1', $facets['tag'][2]['number'] );
  }



}
?>
