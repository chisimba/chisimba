<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'multisparqlservice.class.php';
require_once MORIARTY_TEST_DIR . 'sparqlservicebase.test.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';

class MultiSparqlServiceTest extends SparqlServiceBaseTest {
  function test_describe_single_uri_with_graph_posts_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );
    
    $ss = new MultiSparqlService("http://example.org/store/services/multisparql");
    $ss->request_factory = $fake_request_factory;

    $graphs = Array('http://example.org/graphs/1');    

    $response = $ss->describe( 'http://example.org/scooby', $graphs );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_describe_single_uri_with_graph_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql");
    $ss->request_factory = $fake_request_factory;

    $graphs = Array('http://example.org/graphs/1');    

    $response = $ss->describe( 'http://example.org/scooby', $graphs );
    $this->assertEquals( "query=DESCRIBE+%3Chttp%3A%2F%2Fexample.org%2Fscooby%3E+FROM+%3Chttp%3A%2F%2Fexample.org%2Fgraphs%2F1%3E+", $fake_request->get_body() );
  }

  function test_describe_single_uri_with_graph_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql");
    $ss->request_factory = $fake_request_factory;

    $graphs = Array('http://example.org/graphs/1');    

    $response = $ss->describe( 'http://example.org/scooby', $graphs );
    $this->assertTrue( in_array('Accept: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_describe_single_uri_with_graph_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql");
    $ss->request_factory = $fake_request_factory;

    $graphs = Array('http://example.org/graphs/1');    

    $response = $ss->describe( 'http://example.org/scooby', $graphs );
    $this->assertTrue( in_array('Content-Type: application/x-www-form-urlencoded', $fake_request->get_headers() ) );
  }

  function test_describe_single_uri_with_graph_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $graphs = Array('http://example.org/graphs/1');    

    $response = $ss->describe( 'http://example.org/scooby', $graphs );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }


  function test_describe_multiple_uris_with_graph_posts_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( array( 'http://example.org/scooby', 'http://example.org/shaggy' ), Array('http://example.org/graphs/1')  );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_describe_multiple_uris_with_graph_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( array( 'http://example.org/scooby', 'http://example.org/shaggy', 'http://example.org/velma' ), Array('http://example.org/graphs/1') );
    $this->assertEquals( "query=DESCRIBE+%3Chttp%3A%2F%2Fexample.org%2Fscooby%3E+%3Chttp%3A%2F%2Fexample.org%2Fshaggy%3E+%3Chttp%3A%2F%2Fexample.org%2Fvelma%3E+FROM+%3Chttp%3A%2F%2Fexample.org%2Fgraphs%2F1%3E+", $fake_request->get_body() );
  }

  function test_describe_multiple_uris_with_graph_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( array( 'http://example.org/scooby', 'http://example.org/shaggy', 'http://example.org/velma' ), Array('http://example.org/graphs/1')  );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_describe_to_triple_list_with_graph() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
</rdf:RDF>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql");
    $ss->request_factory = $fake_request_factory;
  
    $triples = $ss->describe_to_triple_list( 'http://example.org/subj', Array('http://example.org/graphs/1') );
    $this->assertTrue( is_array( $triples ) );
  }


  function test_describe_to_triple_list_with_graph_parses_response() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
</rdf:RDF>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql");
    $ss->request_factory = $fake_request_factory;
  
    $triples = $ss->describe_to_triple_list( 'http://example.org/subj', Array('http://example.org/graphs/1') );

    $this->assertEquals( 1, count( $triples ) );
    $this->assertEquals( 'iri', $triples[0]['s_type'] );
    $this->assertEquals( 'http://example.org/subj', $triples[0]['s'] );
    $this->assertEquals( 'http://example.org/pred', $triples[0]['p'] );
    $this->assertEquals( 'iri', $triples[0]['o_type'] );
    $this->assertEquals( 'http://example.org/obj', $triples[0]['o'] );

  }

  function test_describe_to_triple_list_with_graph_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $triples = $ss->describe_to_triple_list( 'http://example.org/subj', Array('http://example.org/graphs/1') );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_graph_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $response = $ss->graph( 'construct {?s ?p ?o } where { ?s ?p ?o .}', Array('http://example.org/graphs/1') );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_graph_to_triple_list_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $triples = $ss->graph_to_triple_list( 'construct {?s ?p ?o } where { ?s ?p ?o .}', Array('http://example.org/graphs/1') );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_select_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;

    $response = $ss->select( 'select ?s where { ?s ?p ?o .}', Array('http://example.org/graphs/1') );
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
    $fake_request_factory->register('POST', "http://example.org/store/services/multisparql", $fake_request );

    $ss = new MultiSparqlService("http://example.org/store/services/multisparql", new FakeCredentials());
    $ss->request_factory = $fake_request_factory;
  
    $array = $ss->select_to_array( 'select distinct ?s where { ?s ?p ?o .} limit 3', Array('http://example.org/graphs/1'));
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }


}

?>
