<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';

class SimpleGraphTest extends PHPUnit_Framework_TestCase {
    var $_single_triple =  '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:ex="http://example.org/">
  <rdf:Description rdf:about="http://example.org/subj">
    <ex:pred>foo</ex:pred>
  </rdf:Description>
</rdf:RDF>';

  function test_add_resource_triple() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');

    $this->assertEquals( 1, count($g->get_triples()));
  }

  function test_add_resource_triple_sets_object_type() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');

    $triples = $g->get_triples();
    $this->assertTrue( isset($triples[0]['o_type']));
    $this->assertEquals( 'iri', $triples[0]['o_type']);
  }

  function test_add_resource_triple_ignores_duplicates() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');


    $this->assertEquals( 1, count($g->get_triples()));
  }

  function test_add_resource_triple_accepts_bnode_subjects() {
    $g = new SimpleGraph();
    $g->add_resource_triple('_:subj', 'http://example.org/pred', 'http://example.org/obj');
    $this->assertEquals( 1, count($g->get_triples()));
  }

  function test_add_resource_triple_accepts_bnode_objects() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', '_:obj');
    $this->assertEquals( 1, count($g->get_triples()));
  }

  function test_add_resource_triple_sets_bnode_object_type() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', '_:obj');

    $triples = $g->get_triples();
    $this->assertTrue( isset($triples[0]['o_type']));
    $this->assertEquals( 'bnode', $triples[0]['o_type']);
  }

  function test_add_literal_triple() {
    $g = new SimpleGraph();
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal');

    $this->assertEquals( 1, count($g->get_triples()));
  }

  function test_add_literal_triple_sets_object_type() {
    $g = new SimpleGraph();
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal');

  function test_get_first_literal() {
    $g = new SimpleGraph();
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal');

    $this->assertEquals( "literal", $g->get_first_literal('http://example.org/subj', 'http://example.org/pred'));
  }
    $triples = $g->get_triples();
    $this->assertTrue( isset($triples[0]['o_type']));
    $this->assertEquals( 'literal', $triples[0]['o_type']);
  }

  function test_add_literal_triple_sets_object_language() {
    $g = new SimpleGraph();
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal', 'en');

    $triples = $g->get_triples();
    $this->assertTrue( isset($triples[0]['o_lang']));
    $this->assertEquals('en', $triples[0]['o_lang']);
  }
  function test_add_literal_triple_sets_object_datatype() {
    $g = new SimpleGraph();
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal', 'en', 'http://example.org/dt');

    $triples = $g->get_triples();
    $this->assertTrue( isset($triples[0]['o_dt']));
    $this->assertEquals('http://example.org/dt', $triples[0]['o_dt']);
  }

  function test_add_resource_triple_ignores_duplicate_languages() {
    $g = new SimpleGraph();
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal', 'en');
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal', 'de');
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal', 'en');


    $this->assertEquals( 2, count($g->get_triples()));
  }

  function test_add_resource_triple_ignores_duplicate_datatypes() {
    $g = new SimpleGraph();
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal', null, 'http://example.org/dt');
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal', null, 'http://example.org/dt2');
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal', null, 'http://example.org/dt');


    $this->assertEquals( 2, count($g->get_triples()));
  }

  function test_get_first_literal() {
    $g = new SimpleGraph();
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal');

    $this->assertEquals( "literal", $g->get_first_literal('http://example.org/subj', 'http://example.org/pred'));
  }
  function test_get_first_literal_ignortes_resources() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal');

    $this->assertEquals( "literal", $g->get_first_literal('http://example.org/subj', 'http://example.org/pred'));
  }

  function test_remove_resource_triple() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');

    $this->assertEquals( 1, count($g->get_triples()));

    $g->remove_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');
    $this->assertEquals( 0, count($g->get_triples()));
  }


  function test_remove_triples_about() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal');

    $g->remove_triples_about('http://example.org/subj');

    $this->assertEquals( 0, count($g->get_triples()));
  }

  function test_remove_triples_about_affects_only_specified_subject() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');
    $g->add_literal_triple('http://example.org/subj2', 'http://example.org/pred', 'literal');

    $g->remove_triples_about('http://example.org/subj');

    $this->assertEquals( 1, count($g->get_triples()));
  }

  function test_from_rdfxml() {
    $g = new SimpleGraph();
    $g->from_rdfxml($this->_single_triple);
    $this->assertEquals( 1, count($g->get_triples()));

    $index = $g->get_index();
    $this->assertEquals("foo", $index['http://example.org/subj']['http://example.org/pred'][0]['value']);
  }

  function test_from_rdfxml_replaces_existing_triples() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj1', 'http://example.org/pred1', 'http://example.org/obj1');
    $g->from_rdfxml($this->_single_triple);
    $this->assertEquals( 1, count($g->get_triples()));

    $index = $g->get_index();
    $this->assertEquals("foo", $index['http://example.org/subj']['http://example.org/pred'][0]['value']);
  }

  function test_has_resource_triple() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj1', 'http://example.org/pred1', 'http://example.org/obj1');

    $this->assertTrue( $g->has_resource_triple('http://example.org/subj1', 'http://example.org/pred1', 'http://example.org/obj1'));
    $this->assertFalse( $g->has_resource_triple('http://example.org/subj1', 'http://example.org/pred1', 'http://example.org/obj2'));
  }
  function test_get_first_resource() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');

    $this->assertEquals( "http://example.org/obj", $g->get_first_resource('http://example.org/subj', 'http://example.org/pred'));
  }
  function test_get_first_resource_ignores_literals() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');
    $g->add_literal_triple('http://example.org/subj', 'http://example.org/pred', 'literal');

    $this->assertEquals( "http://example.org/obj", $g->get_first_resource('http://example.org/subj', 'http://example.org/pred'));
  }


  function test_remove_property_values() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');

    $this->assertEquals( 1, count($g->get_triples()));

    $g->remove_property_values('http://example.org/subj', 'http://example.org/pred');
    $this->assertEquals( 0, count($g->get_triples()));
  }

  function test_remove_property_values_removes_multiplr_values() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj2');
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj3');
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj4');
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj5');

    $this->assertEquals( 5, count($g->get_triples()));

    $g->remove_property_values('http://example.org/subj', 'http://example.org/pred');
    $this->assertEquals( 0, count($g->get_triples()));
  }

  function test_remove_property_values_ignores_unknown_properties() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');

    $this->assertEquals( 1, count($g->get_triples()));

    $g->remove_property_values('http://example.org/subj', 'http://example.org/pred2');
    $this->assertEquals( 1, count($g->get_triples()));
  }

  function test_remove_all_triples() {
    $g = new SimpleGraph();
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj');
    $g->add_resource_triple('http://example.org/subj', 'http://example.org/pred', 'http://example.org/obj2');

    $this->assertEquals( 2, count($g->get_triples()));

    $g->remove_all_triples();
    $this->assertEquals( 0, count($g->get_triples()));
  }

}
?>
