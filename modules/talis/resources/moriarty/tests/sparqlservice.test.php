<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';
require_once MORIARTY_TEST_DIR . 'sparqlservicebase.test.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';

class SparqlServiceTest extends SparqlServiceBaseTest {

  function test_describe_single_uri_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( 'http://example.org/scooby' );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_describe_multiple_uris_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( array( 'http://example.org/scooby', 'http://example.org/shaggy', 'http://example.org/velma' )  );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_describe_to_triple_list_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $triples = $ss->describe_to_triple_list( 'http://example.org/subj' );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_graph_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $response = $ss->graph( 'construct {?s ?p ?o } where { ?s ?p ?o .}' );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_graph_to_triple_list_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $triples = $ss->graph_to_triple_list( 'construct {?s ?p ?o } where { ?s ?p ?o .}' );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_select_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $response = $ss->select( 'select ?s where { ?s ?p ?o .}' );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_select_to_array_uses_credentials() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<?xml version="1.0"?>
<sparql
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:xs="http://www.w3.org/2001/XMLSchema#"
    xmlns="http://www.w3.org/2005/sparql-results#" >
  <head>
    <variable name="s"/>
    <variable name="p"/>
    <variable name="o"/>
  </head>
  <results ordered="false" distinct="true">
    <result>
      <binding name="s">

        <uri>http://api.talis.local/bf/stores/engagetenantstore/items/1173364330999#self</uri>
      </binding>
      <binding name="p">
        <uri>http://www.w3.org/1999/02/22-rdf-syntax-ns#subject</uri>
      </binding>
      <binding name="o">
        <uri>http://api.talis.local/bf/stores/engagetenantstore/items/1174262688178#self</uri>

      </binding>
    </result>
    <result>
      <binding name="s">
        <uri>http://api.talis.local/bf/stores/engagetenantstore/items/1173364330999#self</uri>

      </binding>
      <binding name="p">
        <uri>http://www.w3.org/1999/02/22-rdf-syntax-ns#object</uri>
      </binding>
      <binding name="o">
        <literal>1a3c47c9-fb29-4fd2-a061-a3b72328c96b</literal>
      </binding>
    </result>

  </results>
</sparql>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;
  
    $array = $ss->select_to_array( 'select distinct ?s where { ?s ?p ?o .} limit 3' );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

}

?>
