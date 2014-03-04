<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'changesetbatch.class.php';
class ChangeSetBatchTest extends PHPUnit_Framework_TestCase {
    var $_parser;
    var $_single_triple =  '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
</rdf:RDF>';

    var $_two_triples =  '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
    <ex:pred rdf:resource="http://example.org/obj2" />
  </rdf:Description>
</rdf:RDF>';

    var $_different_subjects =  '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/subj2">
    <ex:pred rdf:resource="http://example.org/obj" />
  </rdf:Description>
</rdf:RDF>';

  function setUp() {
      $parser_args=array(
        "bnode_prefix"=>"genid",
        "base"=>""
      );
      $this->_parser = ARC2::getRDFXMLParser($parser_args);
  }

  function _find_changeset_resource($index) {
    $changesetResource = null;
    foreach ($index as $subject => $properties) {
      if ( array_key_exists('http://www.w3.org/1999/02/22-rdf-syntax-ns#type', $properties)) {
        foreach ($properties['http://www.w3.org/1999/02/22-rdf-syntax-ns#type'] as $property) {
          if ( $property['type'] == 'uri' && $property['value'] =='http://purl.org/vocab/changeset/schema#ChangeSet') {
            return $subject;
          }
        }
      }

    }
  }

  function parse($base, $rdfxml) {
    $parser = ARC2::getRDFXMLParser();
    $parser->parse($base, $rdfxml );
    return $parser->getTriples();
  }

  function test_single_triple_gives_single_changeset() {
    $triples = $this->parse('',  $this->_single_triple );
    $csb = new ChangeSetBatch( array( 'after'=>$triples ) );
    $this->assertEquals(1,  count($csb->get_changesets() ));
  }

  function test_single_triple_passes_subject_of_change() {
    $triples = $this->parse('',  $this->_single_triple );
    $csb = new ChangeSetBatch( array( 'after'=>$triples ) );

    $cslist = $csb->get_changesets();
    $cs = $cslist[0];

    $index = $cs->get_index();


    $changesetResource = $this->_find_changeset_resource($index);

    $objects =  $index[$changesetResource]["http://purl.org/vocab/changeset/schema#subjectOfChange"];

    $this->assertEquals('uri',  $objects[0]["type"]);
    $this->assertEquals("http://example.org/subj",  $objects[0]['value']);
  }

  function test_multiple_triples_for_same_subject_gives_single_changeset() {
    $triples = $this->parse('',  $this->_two_triples );
    $csb = new ChangeSetBatch( array( 'after'=>$triples ) );
    $this->assertEquals(1,  count($csb->get_changesets() ));
  }

  function test_multiple_triples_for_different_subjects_gives_multiple_changesets() {
    $triples = $this->parse('',  $this->_different_subjects );
    $csb = new ChangeSetBatch( array( 'after'=>$triples ) );
    $this->assertEquals(2,  count($csb->get_changesets() ));
  }

  function test_changeset_builder_reads_rdf_xml() {
    $csb = new ChangeSetBatch( array( 'after_rdfxml'=>$this->_different_subjects ) );
    $this->assertEquals(2,  count($csb->get_changesets() ));
  }


  function test_changesets_have_created_date_if_supplied() {
    $csb = new ChangeSetBatch( array( 'after_rdfxml'=>$this->_single_triple, 'createdDate'=>'2006-01-01T00:00:00Z' ) );
    $cslist = $csb->get_changesets();
    $cs = $cslist[0];

    $index = $cs->get_index();


    $changesetResource = $this->_find_changeset_resource($index);
    $objects =  $index[$changesetResource]["http://purl.org/vocab/changeset/schema#createdDate"];

    $this->assertEquals("literal",  $objects[0]["type"]);
    $this->assertEquals("2006-01-01T00:00:00Z",  $objects[0]['value']);
  }

  function test_changesets_have_creator_name_if_supplied() {
    $csb = new ChangeSetBatch( array( 'after_rdfxml'=>$this->_single_triple, 'creatorName'=>'scooby doo' ) );
    $cslist = $csb->get_changesets();
    $cs = $cslist[0];

    $index = $cs->get_index();


    $changesetResource = $this->_find_changeset_resource($index);
    $objects =  $index[$changesetResource]["http://purl.org/vocab/changeset/schema#creatorName"];

    $this->assertEquals("literal",  $objects[0]["type"]);
    $this->assertEquals("scooby doo",  $objects[0]['value']);
  }

  function test_changesets_have_change_reason_if_supplied() {
    $csb = new ChangeSetBatch( array( 'after_rdfxml'=>$this->_single_triple, 'changeReason'=>'cos i wanna' ) );
    $cslist = $csb->get_changesets();
    $cs = $cslist[0];

    $index = $cs->get_index();


    $changesetResource = $this->_find_changeset_resource($index);
    $objects = $index[$changesetResource]["http://purl.org/vocab/changeset/schema#changeReason"];

    $this->assertEquals("literal",  $objects[0]["type"]);
    $this->assertEquals("cos i wanna",  $objects[0]['value']);
  }

  function test_before() {


    $before_triples = $this->parse('',  $this->_single_triple );
    $after_triples = $this->parse('',  $this->_two_triples );

    $csb = new ChangeSetBatch( array( 'after'=>$after_triples, 'before'=>$before_triples ) );
    $cslist = $csb->get_changesets();
    $cs = $cslist[0];


    $index = $cs->get_index();


    $changesetResource = $this->_find_changeset_resource($index);
    $objects = $index[$changesetResource]["http://purl.org/vocab/changeset/schema#addition"];

    // $this->assertEquals("foo",  $cs->to_rdfxml() );
    $this->assertEquals(1,  count( $objects ));
  }



}




?>
