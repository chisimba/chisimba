<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';

class SparqlServiceBaseTest extends PHPUnit_Framework_TestCase {

  function test_describe_single_uri_posts_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( 'http://example.org/scooby' );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_describe_single_uri_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( 'http://example.org/scooby' );
    $this->assertEquals( "query=DESCRIBE+%3Chttp%3A%2F%2Fexample.org%2Fscooby%3E", $fake_request->get_body() );
  }

  function test_describe_single_uri_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( 'http://example.org/scooby' );
    $this->assertTrue( in_array('Accept: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_describe_single_uri_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( 'http://example.org/scooby' );
    $this->assertTrue( in_array('Content-Type: application/x-www-form-urlencoded', $fake_request->get_headers() ) );
  }

  function test_describe_multiple_uris_posts_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( array( 'http://example.org/scooby', 'http://example.org/shaggy' )  );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_describe_multiple_uris_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->describe( array( 'http://example.org/scooby', 'http://example.org/shaggy', 'http://example.org/velma' )  );
    $this->assertEquals( "query=DESCRIBE+%3Chttp%3A%2F%2Fexample.org%2Fscooby%3E+%3Chttp%3A%2F%2Fexample.org%2Fshaggy%3E+%3Chttp%3A%2F%2Fexample.org%2Fvelma%3E", $fake_request->get_body() );
  }

  function test_describe_to_triple_list() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
</rdf:RDF>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;
  
    $triples = $ss->describe_to_triple_list( 'http://example.org/subj' );
    $this->assertTrue( is_array( $triples ) );
  }


  function test_describe_to_triple_list_parses_response() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
</rdf:RDF>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;
  
    $triples = $ss->describe_to_triple_list( 'http://example.org/subj' );

    $this->assertEquals( 1, count( $triples ) );
    $this->assertEquals( 'iri', $triples[0]['s_type'] );
    $this->assertEquals( 'http://example.org/subj', $triples[0]['s'] );
    $this->assertEquals( 'http://example.org/pred', $triples[0]['p'] );
    $this->assertEquals( 'iri', $triples[0]['o_type'] );
    $this->assertEquals( 'http://example.org/obj', $triples[0]['o'] );

  }


  function test_graph_posts_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->graph( 'construct {?s ?p ?o } where { ?s ?p ?o .}' );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_graph_uri_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->graph( 'construct {?s ?p ?o } where { ?s ?p ?o .}' );
    $this->assertEquals( "query=construct+%7B%3Fs+%3Fp+%3Fo+%7D+where+%7B+%3Fs+%3Fp+%3Fo+.%7D", $fake_request->get_body() );
  }

  function test_graph_to_triple_list() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
</rdf:RDF>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;
  
    $triples = $ss->graph_to_triple_list( 'construct {?s ?p ?o } where { ?s ?p ?o .}' );
    $this->assertTrue( is_array( $triples ) );
  }


  function test_graph_to_triple_list_parses_response() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
</rdf:RDF>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;
  
    $triples = $ss->graph_to_triple_list( 'construct {?s ?p ?o } where { ?s ?p ?o .}' );

    $this->assertEquals( 1, count( $triples ) );
    $this->assertEquals( 'iri', $triples[0]['s_type'] );
    $this->assertEquals( 'http://example.org/subj', $triples[0]['s'] );
    $this->assertEquals( 'http://example.org/pred', $triples[0]['p'] );
    $this->assertEquals( 'iri', $triples[0]['o_type'] );
    $this->assertEquals( 'http://example.org/obj', $triples[0]['o']);

  }

  function test_select_posts_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->select( 'select ?s where { ?s ?p ?o .}' );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_select_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->select( 'select ?s where { ?s ?p ?o .}' );
    $this->assertEquals( "query=select+%3Fs+where+%7B+%3Fs+%3Fp+%3Fo+.%7D", $fake_request->get_body() );
  }


  function test_select_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->select( 'select ?s where { ?s ?p ?o .}' );
    $this->assertTrue( in_array('Accept: application/sparql-results+xml', $fake_request->get_headers() ) );
  }

  function test_select_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->select( 'select ?s where { ?s ?p ?o .}' );
    $this->assertTrue( in_array('Content-Type: application/x-www-form-urlencoded', $fake_request->get_headers() ) );
  }


  function test_select_to_array() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<?xml version="1.0"?>
<sparql
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:xs="http://www.w3.org/2001/XMLSchema#"
    xmlns="http://www.w3.org/2005/sparql-results#" >
  <head>
    <variable name="s"/>
  </head>
  <results ordered="false" distinct="true">
    <result>
      <binding name="s">
        <uri>http://api.talis.local/bf/stores/engagetenantstore/items/1173364330999#self</uri>

      </binding>
    </result>
    <result>
      <binding name="s">
        <uri>http://api.talis.local/bf/stores/engagetenantstore/items/1173371909685#self</uri>
      </binding>
    </result>
    <result>

      <binding name="s">
        <uri>http://api.talis.local/bf/stores/engagetenantstore/items/1173386584025#self</uri>
      </binding>
    </result>
  </results>
</sparql>
';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;
  
    $array = $ss->select_to_array( 'select distinct ?s where { ?s ?p ?o .} limit 3' );
    $this->assertTrue( is_array( $array ) );

  }


  function test_select_to_array_parses_response() {
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

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;
  
    $array = $ss->select_to_array( 'select distinct ?s where { ?s ?p ?o .} limit 3' );

    $this->assertEquals( 2, count( $array ) );

    $this->assertEquals( 3, count($array[0]) );
    $this->assertEquals( 'uri', $array[0]['s']['type'] );
    $this->assertEquals( 'http://api.talis.local/bf/stores/engagetenantstore/items/1173364330999#self', $array[0]['s']['value'] );
    $this->assertEquals( 'uri', $array[0]['p']['type'] );
    $this->assertEquals( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#subject', $array[0]['p']['value'] );
    $this->assertEquals( 'uri', $array[0]['o']['type'] );
    $this->assertEquals( 'http://api.talis.local/bf/stores/engagetenantstore/items/1174262688178#self', $array[0]['o']['value'] );

    $this->assertEquals( 3, count($array[1]) );
    $this->assertEquals( 'uri', $array[1]['s']['type'] );
    $this->assertEquals( 'http://api.talis.local/bf/stores/engagetenantstore/items/1173364330999#self', $array[1]['s']['value'] );
    $this->assertEquals( 'uri', $array[1]['p']['type'] );
    $this->assertEquals( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#object', $array[1]['p']['value'] );
    $this->assertEquals( 'literal', $array[1]['o']['type'] );
    $this->assertEquals( '1a3c47c9-fb29-4fd2-a061-a3b72328c96b', $array[1]['o']['value'] );
  }


  function test_parse_select_results_xml_lang() {
    $xml = '<?xml version="1.0"?>
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
        <uri>http://www.w3.org/1999/02/22-rdf-syntax-ns#object</uri>
      </binding>
      <binding name="o">
        <literal xml:lang="he">1a3c47c9-fb29-4fd2-a061-a3b72328c96b</literal>
      </binding>
    </result>

  </results>
</sparql>';

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $array = $ss->parse_select_results($xml );

    $this->assertEquals( 1, count( $array ) );

    $this->assertEquals( 3, count($array[0]) );
    $this->assertEquals( 'literal', $array[0]['o']['type'] );
    $this->assertEquals( '1a3c47c9-fb29-4fd2-a061-a3b72328c96b', $array[0]['o']['value'] );
    $this->assertEquals( 'he', $array[0]['o']['lang'] );
  }


  function test_parse_select_results_datatype() {
    $xml = '<?xml version="1.0"?>
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
        <uri>http://www.w3.org/1999/02/22-rdf-syntax-ns#object</uri>
      </binding>
      <binding name="o">
        <literal datatype="http://example.com/dt">1a3c47c9-fb29-4fd2-a061-a3b72328c96b</literal>
      </binding>
    </result>

  </results>
</sparql>';

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $array = $ss->parse_select_results($xml );

    $this->assertEquals( 1, count( $array ) );

    $this->assertEquals( 3, count($array[0]) );
    $this->assertEquals( 'literal', $array[0]['o']['type'] );
    $this->assertEquals( '1a3c47c9-fb29-4fd2-a061-a3b72328c96b', $array[0]['o']['value'] );
    $this->assertEquals( 'http://example.com/dt', $array[0]['o']['datatype'] );
  }


  function test_ask_posts_to_service_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->ask( 'ask where { ?s ?p ?o .}' );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_ask_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->ask( 'ask where { ?s ?p ?o .}' );
    $this->assertEquals( "query=ask+where+%7B+%3Fs+%3Fp+%3Fo+.%7D", $fake_request->get_body() );
  }

  function test_ask_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->ask( 'ask where { ?s ?p ?o .}' );
    $this->assertTrue( in_array('Accept: application/sparql-results+xml', $fake_request->get_headers() ) );
  }

  function test_ask_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $response = $ss->ask( 'ask where { ?s ?p ?o .}' );
    $this->assertTrue( in_array('Content-Type: application/x-www-form-urlencoded', $fake_request->get_headers() ) );
  }

  function test_ask_results_true() {
      $xml = '<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.w3.org/2001/sw/DataAccess/rf1/result2.xsd">

  <head>
    <link href="example2.rq" />
  </head>

  <boolean>true</boolean>

</sparql>';
  
    $ss = new SparqlServiceBase("http://example.org/store/services/sparql");
    $this->assertTrue( $ss->parse_ask_results( $xml ) );
  }


  function test_ask_results_false() {
      $xml = '<?xml version="1.0"?>
<sparql xmlns="http://www.w3.org/2005/sparql-results#"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.w3.org/2001/sw/DataAccess/rf1/result2.xsd">

  <head>
    <link href="example2.rq" />
  </head>

  <boolean>false</boolean>

</sparql>';
  
    $ss = new SparqlServiceBase("http://example.org/store/services/sparql");
    $this->assertFalse( $ss->parse_ask_results( $xml ) );
  }
}

?>
