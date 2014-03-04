<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'storecollection.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';
require_once MORIARTY_ARC_DIR . 'ARC2.php';

class StoreCollectionTest extends PHPUnit_Framework_TestCase {
  var $_store_list_rdf = '<rdf:RDF
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:bf="http://schemas.talis.com/2006/bigfoot/configuration#" >
  <rdf:Description rdf:about="http://example.org/stores">
    <bf:store rdf:resource="http://example.org/stores/tutorial"/>
    <bf:store rdf:resource="http://example.org/stores/silkworm"/>
  </rdf:Description>
</rdf:RDF>';



  function test_create_store_posts_to_collection_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores");
    $coll->request_factory = $fake_request_factory;

    $response = $coll->create_store("scooby", "http://example.org/template");
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_create_store_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores");
    $coll->request_factory = $fake_request_factory;

    $response = $coll->create_store("scooby", "http://example.org/template");
    $this->assertTrue( in_array('Content-Type: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_create_store_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores");
    $coll->request_factory = $fake_request_factory;

    $response = $coll->create_store("scooby", "http://example.org/template");
    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }

  function test_create_store_uses_auth() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores", new FakeCredentials());
    $coll->request_factory = $fake_request_factory;

    $response = $coll->create_store("scooby", "http://example.org/template");
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }


  function test_create_store_posts_rdfxml_where_triples_all_have_same_subject() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores");
    $coll->request_factory = $fake_request_factory;

    $response = $coll->create_store("scooby", "http://example.org/template");

    $parser = ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $subjects = array();
    foreach ($triples as $triple) {
      $subject = $triple['s'];
      $subjects[$subject] = 1;
    }

    $this->assertEquals( 1 , count($subjects));
  }


  function test_create_store_posts_rdfxml_with_a_single_storeref() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores");
    $coll->request_factory = $fake_request_factory;

    $response = $coll->create_store("scooby", "http://example.org/template");

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#storeRef') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_create_store_posts_rdfxml_with_a_single_store_template() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores");
    $coll->request_factory = $fake_request_factory;

    $response = $coll->create_store("scooby", "http://example.org/template");

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#storeTemplate') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }


  function test_retrieve() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_store_list_rdf;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores");
    $coll->request_factory = $fake_request_factory;
    $coll->retrieve();

    $this->assertTrue($coll->has_resource_triple('http://example.org/stores', 'http://schemas.talis.com/2006/bigfoot/configuration#store', 'http://example.org/stores/tutorial') );
    $this->assertTrue($coll->has_resource_triple('http://example.org/stores', 'http://schemas.talis.com/2006/bigfoot/configuration#store', 'http://example.org/stores/silkworm') );
  }


  function test_retrieve_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/stores", $fake_request );

    $coll = new StoreCollection("http://example.org/stores");
    $coll->request_factory = $fake_request_factory;
    $coll->retrieve();

    $this->assertTrue( in_array('Accept: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_get_store_uris() {
    $coll = new StoreCollection("http://example.org/stores");
    $coll->from_rdfxml($this->_store_list_rdf);

    $list = $coll->get_store_uris();

    $this->assertEquals(2, count($list));
  }


}
