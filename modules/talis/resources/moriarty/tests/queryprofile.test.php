<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'queryprofile.class.php';
require_once MORIARTY_TEST_DIR . 'networkresource.test.php';
require_once MORIARTY_ARC_DIR . 'ARC2.php';

class QueryProfileTest extends NetworkResourceTest {
  var $_qp = '<rdf:RDF
    xmlns:frm="http://schemas.talis.com/2006/frame/schema#"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
    xmlns:bf="http://schemas.talis.com/2006/bigfoot/configuration#" >
  <rdf:Description rdf:about="http://example.org/store1/config/queryprofiles/1">
    <bf:fieldWeight rdf:resource="http://example.org/store1/config/queryprofiles/1#label"/>
    <rdf:type rdf:resource="http://schemas.talis.com/2006/bigfoot/configuration#QueryProfile"/>
  </rdf:Description>
  <rdf:Description rdf:about="http://example.org/store1/config/queryprofiles/1#label">
    <bf:weight>2.0</bf:weight>
    <frm:name>label</frm:name>
  </rdf:Description>
</rdf:RDF>';


  function test_uri() {
    $qp = new QueryProfile("http://example.org/store/queryprofiles/1");
    $this->assertEquals( "http://example.org/store/queryprofiles/1", $qp->uri );
  }

  function test_add_field_weight() {
    $qp = new QueryProfile("http://example.org/store/queryprofiles/1");
    $qp->add_field_weight("http://example.org/pred", "pred");

    $index = $qp->get_index();
    $this->assertEquals(1,  count($index[$qp->uri][BF_FIELDWEIGHT]));
  }

  function test_add_field_weight_adds_weight() {
    $qp = new QueryProfile("http://example.org/store/queryprofiles/1");
    $qp->add_field_weight("pred", "2.0");

    $index = $qp->get_index();
    $field_weight_uri = $index[$qp->uri][BF_FIELDWEIGHT][0]['value'];

    $this->assertEquals(1, count($index[$field_weight_uri][BF_WEIGHT]));
    $this->assertEquals("2.0",  $index[$field_weight_uri][BF_WEIGHT][0]['value']);
  }
  function test_add_field_weight_adds_name() {
    $qp = new QueryProfile("http://example.org/store/queryprofiles/1");
    $qp->add_field_weight("pred", "2.0");

    $index = $qp->get_index();
    $field_weight_uri = $index[$qp->uri][BF_FIELDWEIGHT][0]['value'];

    $this->assertEquals(1, count($index[$field_weight_uri][FRM_NAME]));
    $this->assertEquals("pred",  $index[$field_weight_uri][FRM_NAME][0]['value']);
  }

  function test_add_field_weight_returns_uri_of_field_weight() {
    $qp = new QueryProfile("http://example.org/store/queryprofiles/1");
    $field_weight_uri = $qp->add_field_weight("http://example.org/pred", "pred");

    $index = $qp->get_index();
    $this->assertEquals($index[$qp->uri][BF_FIELDWEIGHT][0]['value'], $field_weight_uri);
  }


  function test_remove_field_weight() {
    $qp = new QueryProfile("http://example.org/store/queryprofiles/1");
    $qp->add_field_weight("pred", "2.0");

    $index = $qp->get_index();
    $this->assertEquals(1,  count($index[$qp->uri][BF_FIELDWEIGHT]));

    $qp->remove_field_weight("pred");
    $index = $qp->get_index();
    $this->assertEquals(false,  isset($index[$qp->uri][BF_FIELDWEIGHT]));
  }

  function test_copy_to() {
    $qp = new QueryProfile("http://example.org/store1/config/queryprofiles/1");
    $qp->from_rdfxml( $this->_qp);

    $index = $qp->get_index();
    $this->assertEquals(1,  count($index[$qp->uri][BF_FIELDWEIGHT]));

    $qp2 = $qp->copy_to("http://example.org/store2/config/queryprofiles/1");

    $index2 = $qp2->get_index();
    $this->assertEquals(1,  count($index2[$qp2->uri][RDF_TYPE]));
    $this->assertEquals("http://schemas.talis.com/2006/bigfoot/configuration#QueryProfile",  $index2[$qp2->uri][RDF_TYPE][0]['value']);

    $this->assertEquals(1,  count($index2[$qp2->uri][BF_FIELDWEIGHT]));
    $this->assertEquals("http://example.org/store2/config/queryprofiles/1#label",  $index2[$qp2->uri][BF_FIELDWEIGHT][0]['value']);

    $this->assertEquals(1,  count($index2[$index2[$qp2->uri][BF_FIELDWEIGHT][0]['value']][FRM_NAME]));
    $this->assertEquals("label",  $index2[$index2[$qp2->uri][BF_FIELDWEIGHT][0]['value']][FRM_NAME][0]['value']);

  }

}

?>
