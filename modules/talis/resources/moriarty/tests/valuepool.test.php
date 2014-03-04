<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'valuepool.class.php';

class ValuePoolTest extends PHPUnit_Framework_TestCase {
  var $_parser;

  function setUp() {
      $parser_args=array(
        "bnode_prefix"=>"genid",
        "base"=>""
      );
      $this->_parser =ARC2::getRDFXMLParser($parser_args);
  }

  function _find_changeset_resource($index) {
    $changesetResource = null;
    foreach ($index as $subject => $properties) {
      if ( array_key_exists('http://www.w3.org/1999/02/22-rdf-syntax-ns#type', $properties)) {
        foreach ($properties['http://www.w3.org/1999/02/22-rdf-syntax-ns#type'] as $property) {
          if ( $property['type'] == 'iri' && $property['val'] =='http://purl.org/vocab/changeset/schema#ChangeSet') {
            return $subject;
          }
        }
      }

    }

  }

  function test_get_candidate_values_posts_query() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $pool = new ValuePool();
    $pool->bigfootSparqlService = $ss;

    $values = $pool->get_candidate_values('transcripts');
    $this->assertEquals( "query=PREFIX+p%3A+%3Chttp%3A%2F%2Fpurl.org%2Fvocab%2Fvalue-pools%2Fschema%23%3E+CONSTRUCT+%7B%3Ctranscripts%3E+p%3Avalue+%3Fv+.+%7D+WHERE+%7B+%3Ctranscripts%3E+p%3Avalue+%3Fv+.+%7D+LIMIT+5", $fake_request->get_body() );
  }

  function test_get_candidate_values_parses_results() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = '<rdf:RDF
    xmlns:p="http://purl.org/vocab/value-pools/schema#"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" >
  <rdf:Description rdf:about="http://backend.sharedpast.com/pools/transcripts">
    <p:value>13</p:value>
    <p:value>16</p:value>
    <p:value>21</p:value>
    <p:value>24</p:value>
    <p:value>28</p:value>
  </rdf:Description>
</rdf:RDF>';

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $pool = new ValuePool();
    $pool->bigfootSparqlService = $ss;

    $values = $pool->get_candidate_values('http://example.org/pool');

    $this->assertTrue( is_array($values) );
    $this->assertEquals( 5, count($values) );
  }

  function test_get_candidate_values_reads_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $pool = new ValuePool();
    $pool->bigfootSparqlService = $ss;

    $values = $pool->get_candidate_values('http://example.org/pool');
    $this->assertEquals( "query=PREFIX+p%3A+%3Chttp%3A%2F%2Fpurl.org%2Fvocab%2Fvalue-pools%2Fschema%23%3E+CONSTRUCT+%7B%3Chttp%3A%2F%2Fexample.org%2Fpool%3E+p%3Avalue+%3Fv+.+%7D+WHERE+%7B+%3Chttp%3A%2F%2Fexample.org%2Fpool%3E+p%3Avalue+%3Fv+.+%7D+LIMIT+5", $fake_request->get_body() );
  }


  function test_get_candidate_values_reads_max() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/services/sparql", $fake_request );

    $ss = new SparqlService("http://example.org/store/services/sparql");
    $ss->request_factory = $fake_request_factory;

    $pool = new ValuePool();
    $pool->bigfootSparqlService = $ss;

    $values = $pool->get_candidate_values('http://example.org/pool', 14);
    $this->assertEquals( "query=PREFIX+p%3A+%3Chttp%3A%2F%2Fpurl.org%2Fvocab%2Fvalue-pools%2Fschema%23%3E+CONSTRUCT+%7B%3Chttp%3A%2F%2Fexample.org%2Fpool%3E+p%3Avalue+%3Fv+.+%7D+WHERE+%7B+%3Chttp%3A%2F%2Fexample.org%2Fpool%3E+p%3Avalue+%3Fv+.+%7D+LIMIT+14", $fake_request->get_body() );
  }

  function test_select_value_generates_addition_with_correct_subject() {
    $index = $this->_select_value_and_get_changeset_model();

    $changesetResource = $this->_find_changeset_resource($index);

    $objects = $index[$changesetResource]["http://purl.org/vocab/changeset/schema#addition"];
    $types = $index[$objects[0]["val"]]["http://www.w3.org/1999/02/22-rdf-syntax-ns#subject"];

    $this->assertEquals('iri',  $types[0]["type"]);
    $this->assertEquals("http://example.org/pool",  $types[0]["val"]);
  }

  function test_select_value_generates_removal_with_correct_subject() {
    $index = $this->_select_value_and_get_changeset_model();

    $changesetResource = $this->_find_changeset_resource($index);

    $objects = $index[$changesetResource]["http://purl.org/vocab/changeset/schema#removal"];
    $types = $index[$objects[0]["val"]]["http://www.w3.org/1999/02/22-rdf-syntax-ns#subject"];


    $this->assertEquals('iri',  $types[0]["type"]);
    $this->assertEquals("http://example.org/pool",  $types[0]["val"]);
  }

  function _select_value_and_get_changeset_model() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $mb = new Metabox("http://example.org/store/meta");
    $mb->request_factory = $fake_request_factory;

    $pool = new ValuePool();
    $pool->bigfootMetabox = $mb;
    $pool->select_value('http://example.org/pool', 1, 100);

    $this->_parser->parse("", $fake_request->get_body() );
    $triples = $this->_parser->getTriples();

    $index = ARC2::getSimpleIndex($triples, false) ;


    return $index;
  }

  function test_select_value_generates_removal_with_correct_predicate() {
    $index = $this->_select_value_and_get_changeset_model();
    $changesetResource = $this->_find_changeset_resource($index);


    $objects = $index[$changesetResource]["http://purl.org/vocab/changeset/schema#removal"];
    $types = $index[$objects[0]["val"]]["http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate"];

    $this->assertEquals('iri',  $types[0]["type"]);
    $this->assertEquals("http://purl.org/vocab/value-pools/schema#value",  $types[0]["val"]);
  }

  function test_select_value_generates_addition_with_correct_predicate() {
    $index = $this->_select_value_and_get_changeset_model();
    $changesetResource = $this->_find_changeset_resource($index);


    $objects = $index[$changesetResource]["http://purl.org/vocab/changeset/schema#addition"];
    $types = $index[$objects[0]["val"]]["http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate"];

    $this->assertEquals('iri',  $types[0]["type"]);
    $this->assertEquals("http://purl.org/vocab/value-pools/schema#value",  $types[0]["val"]);
  }

  function test_select_value_generates_removal_with_correct_value() {
    $index = $this->_select_value_and_get_changeset_model();
    $changesetResource = $this->_find_changeset_resource($index);

    $objects = $index[$changesetResource]["http://purl.org/vocab/changeset/schema#removal"];
    $types = $index[$objects[0]["val"]]["http://www.w3.org/1999/02/22-rdf-syntax-ns#object"];

    $this->assertEquals("literal",  $types[0]["type"]);
    $this->assertEquals("1",  $types[0]["val"]);
  }

  function test_select_value_generates_addition_with_correct_value() {
    $index = $this->_select_value_and_get_changeset_model();
    $changesetResource = $this->_find_changeset_resource($index);


    $objects = $index[$changesetResource]["http://purl.org/vocab/changeset/schema#addition"];
    $types = $index[$objects[0]["val"]]["http://www.w3.org/1999/02/22-rdf-syntax-ns#object"];

    $this->assertEquals("literal",  $types[0]["type"]);
    $this->assertEquals("101",  $types[0]["val"]);
  }

}
?>
