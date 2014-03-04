<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'graph.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';

class GraphTest extends PHPUnit_Framework_TestCase {
  var $_empty_changeset = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:cs="http://purl.org/vocab/changeset/schema#">
  <rdf:Description rdf:nodeID="cs">
    <rdf:type rdf:resource="http://purl.org/vocab/changeset/schema#ChangeSet" />
    <cs:subjectOfChange rdf:nodeID="a" />
    <cs:creatorName>Ian</cs:creatorName>
    <cs:changeReason>PHP Client Test</cs:changeReason>
  </rdf:Description>
</rdf:RDF>';

  var $_rdfxml_doc = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:foaf="http://xmlns.com/foaf/0.1/">
  <foaf:Person>
    <foaf:name>scooby</foaf:name>
  </foaf:Person>
</rdf:RDF>';

  function make_graph($uri, $credentials = null) {
    // abstract
  }

  function test_apply_changeset_rdfxml_posts_to_metabox_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_changeset_rdfxml( $this->_empty_changeset );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_apply_changeset_rdfxml_posts_supplied_rdfxml() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_changeset_rdfxml( $this->_empty_changeset );
    $this->assertEquals( $this->_empty_changeset , $fake_request->get_body() );
  }

  function test_apply_changeset_rdfxml_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_changeset_rdfxml( $this->_empty_changeset );
    $this->assertTrue( in_array('Content-Type: application/vnd.talis.changeset+xml', $fake_request->get_headers() ) );
  }

  function test_apply_changeset_rdfxml_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_changeset_rdfxml( $this->_empty_changeset );
    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }



  function test_apply_versioned_changeset_rdfxml_posts_to_metabox_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta/changesets", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_versioned_changeset_rdfxml( $this->_empty_changeset );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_apply_versioned_changeset_rdfxml_posts_supplied_rdfxml() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta/changesets", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_versioned_changeset_rdfxml( $this->_empty_changeset );
    $this->assertEquals( $this->_empty_changeset , $fake_request->get_body() );
  }

  function test_apply_versioned_changeset_rdfxml_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta/changesets", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_versioned_changeset_rdfxml( $this->_empty_changeset );
    $this->assertTrue( in_array('Content-Type: application/vnd.talis.changeset+xml', $fake_request->get_headers() ) );
  }

  function test_apply_versioned_changeset_rdfxml_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta/changesets", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_versioned_changeset_rdfxml( $this->_empty_changeset );
    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }


  function test_submit_rdfxml_posts_to_metabox_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->submit_rdfxml( $this->_rdfxml_doc );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_submit_rdfxml_posts_supplied_rdfxml() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->submit_rdfxml( $this->_rdfxml_doc );
    $this->assertEquals( $this->_rdfxml_doc , $fake_request->get_body() );
  }

  function test_submit_rdfxml_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->submit_rdfxml( $this->_rdfxml_doc );
    $this->assertTrue( in_array('Content-Type: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_submit_rdfxml_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta");
    $g->request_factory = $fake_request_factory;

    $response = $g->submit_rdfxml( $this->_rdfxml_doc );
    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }

  function test_apply_changeset_rdfxml_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta", new FakeCredentials());
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_changeset_rdfxml( $this->_empty_changeset );
    $this->assertEquals( "user:pwd" , $fake_request->get_auth() );
  }

  function test_submit_rdfxml_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta", new FakeCredentials());
    $g->request_factory = $fake_request_factory;

    $response = $g->submit_rdfxml( $this->_rdfxml_doc );
    $this->assertEquals( "user:pwd" , $fake_request->get_auth() );
  }

  function test_get_sparql_uri() {
    $g = $this->make_graph("http://example.org/store/meta", new FakeCredentials());
    $this->assertEquals( "http://example.org/store/services/sparql" , $g->_get_sparql_uri() );

    $g2 = $this->make_graph("http://example.org/store/meta/graphs/1", new FakeCredentials());
    $this->assertEquals( "http://example.org/store/services/sparql" , $g2->_get_sparql_uri() );
  }


  function test_describe_single_uri_posts_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta", new FakeCredentials());
    $g->request_factory = $fake_request_factory;

    $response = $g->describe( 'http://example.org/scooby' );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_describe_single_uri_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta", new FakeCredentials());
    $g->request_factory = $fake_request_factory;

    $response = $g->describe( 'http://example.org/scooby' );
    $this->assertEquals( "query=DESCRIBE+%3Chttp%3A%2F%2Fexample.org%2Fscooby%3E", $fake_request->get_body() );
  }

  function test_describe_single_uri_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta", new FakeCredentials());
    $g->request_factory = $fake_request_factory;

    $response = $g->describe( 'http://example.org/scooby' );
    $this->assertTrue( in_array('Accept: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_describe_single_uri_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $g = $this->make_graph("http://example.org/store/meta", new FakeCredentials());
    $g->request_factory = $fake_request_factory;

    $response = $g->describe( 'http://example.org/scooby' );
    $this->assertTrue( in_array('Content-Type: application/x-www-form-urlencoded', $fake_request->get_headers() ) );
  }


}


?>
