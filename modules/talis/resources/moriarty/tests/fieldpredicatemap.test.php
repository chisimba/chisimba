<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'fieldpredicatemap.class.php';
require_once MORIARTY_ARC_DIR . 'ARC2.php';

class FieldPredicateMapTest extends PHPUnit_Framework_TestCase {
  var $_fpmap1 = '<rdf:RDF
    xmlns:frm="http://schemas.talis.com/2006/frame/schema#"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
    xmlns:j.0="http://schemas.talis.com/2006/bigfoot/configuration#" >
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#aimchatid">
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/aimChatID"/>
    <frm:name>aimchatid2</frm:name>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#surname">
    <frm:name>surname</frm:name>
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/surname"/>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#name">
    <frm:name>name</frm:name>
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/name"/>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#family_name">
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/family_name"/>
    <frm:name>family_name</frm:name>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#jabberid">
    <frm:name>jabberid</frm:name>
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/jabberID"/>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#yahoochatid">
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/yahooChatID"/>
    <frm:name>yahoochatid</frm:name>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#plan">
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/plan"/>
    <frm:name>plan</frm:name>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#firstname">
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/firstName"/>
    <frm:name>firstname</frm:name>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#nick">
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/nick"/>
    <frm:name>nick</frm:name>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#msnchatid">
    <frm:name>msnchatid</frm:name>
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/msnChatID"/>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#gender">
    <frm:name>gender</frm:name>
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/gender"/>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#givenname">
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/givenname"/>
    <frm:name>givenname</frm:name>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#olb">
    <frm:name>olb</frm:name>
    <frm:property rdf:resource="http://purl.org/vocab/bio/0.1/olb"/>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1#mboxsha1sum">
    <frm:name>mboxsha1sum</frm:name>
    <frm:property rdf:resource="http://xmlns.com/foaf/0.1/mbox_sha1sum"/>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store/fpmaps/1">
    <rdf:type rdf:resource="http://schemas.talis.com/2006/bigfoot/configuration#FieldPredicateMap"/>
    <rdfs:label>default field/predicate map</rdfs:label>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#yahoochatid"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#mboxsha1sum"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#givenname"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#aimchatid"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#olb"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#gender"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#msnchatid"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#family_name"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#surname"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#nick"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#name"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#plan"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#firstname"/>
    <frm:mappedDatatypeProperty rdf:resource="http://example.org/store/fpmaps/1#jabberid"/>
  </rdf:Description>
</rdf:RDF>';


  function test_uri() {
    $fpmap = new FieldPredicateMap("http://example.org/store/fpmaps/1");
    $this->assertEquals( "http://example.org/store/fpmaps/1", $fpmap->uri );
  }

  function test_add_mapping() {
    $fpmap = new FieldPredicateMap("http://example.org/store/fpmaps/1");
    $fpmap->add_mapping("http://example.org/pred", "pred");

    $index = $fpmap->get_index();
    $this->assertEquals(1,  count($index[$fpmap->uri][FRM_MAPPEDDATATYPEPROPERTY]));
  }

  function test_add_mapping_adds_property() {
    $fpmap = new FieldPredicateMap("http://example.org/store/fpmaps/1");
    $fpmap->add_mapping("http://example.org/pred", "pred");

    $index = $fpmap->get_index();
    $mapping_uri = $index[$fpmap->uri][FRM_MAPPEDDATATYPEPROPERTY][0]['value'];

    $this->assertEquals(1, count($index[$mapping_uri][FRM_PROPERTY]));
  }
  function test_add_mapping_adds_name() {
    $fpmap = new FieldPredicateMap("http://example.org/store/fpmaps/1");
    $fpmap->add_mapping("http://example.org/pred", "pred");

    $index = $fpmap->get_index();
    $mapping_uri = $index[$fpmap->uri][FRM_MAPPEDDATATYPEPROPERTY][0]['value'];

    $this->assertEquals(1, count($index[$mapping_uri][FRM_NAME]));
    $this->assertEquals("pred",  $index[$mapping_uri][FRM_NAME][0]['value']);
  }

  function test_add_mapping_returns_uri_of_mapping() {
    $fpmap = new FieldPredicateMap("http://example.org/store/fpmaps/1");
    $mapping_uri = $fpmap->add_mapping("http://example.org/pred", "pred");

    $index = $fpmap->get_index();
    $this->assertEquals($index[$fpmap->uri][FRM_MAPPEDDATATYPEPROPERTY][0]['value'], $mapping_uri);
  }


  function test_remove_mapping() {
    $fpmap = new FieldPredicateMap("http://example.org/store/fpmaps/1");
    $fpmap->add_mapping("http://example.org/pred", "pred");

    $index = $fpmap->get_index();
    $this->assertEquals(1,  count($index[$fpmap->uri][FRM_MAPPEDDATATYPEPROPERTY]));

    $fpmap->remove_mapping("http://example.org/pred", "pred");
    $index = $fpmap->get_index();
    $this->assertEquals(false,  isset($index[$fpmap->uri][FRM_MAPPEDDATATYPEPROPERTY]));
  }

  function test_add_mapping_supports_optional_analyzer() {
    $fpmap = new FieldPredicateMap("http://example.org/store/fpmaps/1");
    $fpmap->add_mapping("http://example.org/pred", "pred", "http://schemas.talis.com/2007/bigfoot/analyzers#standard");

    $index = $fpmap->get_index();
    $mapping_uri = $index[$fpmap->uri][FRM_MAPPEDDATATYPEPROPERTY][0]['value'];

    $this->assertEquals(1, count($index[$mapping_uri][BF_ANALYZER]));
    $this->assertEquals("uri",  $index[$mapping_uri][BF_ANALYZER][0]['type']);
    $this->assertEquals("http://schemas.talis.com/2007/bigfoot/analyzers#standard",  $index[$mapping_uri][BF_ANALYZER][0]['value']);
  }


  function test_copy_to() {
    $fpmap = new FieldPredicateMap("http://example.org/store/fpmaps/1");
    $fpmap->add_mapping("http://example.org/pred", "pred");

    $fpmap2 = $fpmap->copy_to("http://example.org/store2/fpmaps/1");
    $index2 = $fpmap2->get_index();

    $this->assertEquals(1,  count($index2["http://example.org/store2/fpmaps/1"][FRM_MAPPEDDATATYPEPROPERTY]));
    $this->assertEquals("http://example.org/store2/fpmaps/1#pred",  $index2["http://example.org/store2/fpmaps/1"][FRM_MAPPEDDATATYPEPROPERTY][0]['value']);

    $this->assertEquals(1,  count($index2[$index2["http://example.org/store2/fpmaps/1"][FRM_MAPPEDDATATYPEPROPERTY][0]['value']][FRM_NAME]));
    $this->assertEquals("pred",  $index2[$index2["http://example.org/store2/fpmaps/1"][FRM_MAPPEDDATATYPEPROPERTY][0]['value']][FRM_NAME][0]['value']);

  }

}

?>
