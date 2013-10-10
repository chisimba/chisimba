<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'networkresource.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';

class NetworkResourceTest extends PHPUnit_Framework_TestCase {
  var $_group_rdf = '<rdf:RDF
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:bf="http://schemas.talis.com/2006/bigfoot/configuration#" > 
  <rdf:Description rdf:about="http://example.org/groups/1">
    <rdf:type rdf:resource="http://schemas.talis.com/2006/bigfoot/configuration#StoreGroup"/>
    <bf:store rdf:resource="http://example.org/stores/store4"/>
    <bf:store rdf:resource="http://example.org/stores/store1"/>
    <bf:store rdf:resource="http://example.org/stores/store2"/>
    <bf:store rdf:resource="http://example.org/stores/store3"/>
    <bf:store rdf:resource="http://example.org/stores/store5"/>
    <bf:groupRef>group1</bf:groupRef>

  </rdf:Description>
</rdf:RDF>';

  function test_get_from_network_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse(200) );
    $fake_request_factory->register('GET', "http://example.org/res", $fake_request );

    $group = new NetworkResource("http://example.org/res");
    $group->request_factory = $fake_request_factory;
    $group->get_from_network();

    $this->assertTrue( in_array('Accept: application/rdf+xml', $fake_request->get_headers() ) );
  }


  function test_put_to_network_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse(200) );
    $fake_request_factory->register('PUT', "http://example.org/res", $fake_request );

    $group = new NetworkResource("http://example.org/res");
    $group->request_factory = $fake_request_factory;
    $group->put_to_network();

    $this->assertTrue( in_array('Content-Type: application/rdf+xml', $fake_request->get_headers() ) );
  }
  
  function test_put_to_network_includes_body() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse(200) );
    $fake_request_factory->register('PUT', "http://example.org/res", $fake_request );

    $group = new NetworkResource("http://example.org/res");
    $group->add_resource_triple("http://example.org/res", "http://example.org/pred", "http://example.org/obj");
    $group->request_factory = $fake_request_factory;
    $group->put_to_network();

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    

    $this->assertEquals( 1, count($triples) );
  }  
  
  function test_get_set_label() {
    $fpmap = new QueryProfile("http://example.org/store/queryprofiles/1");
    $fpmap->set_label('my qp');
    $this->assertEquals( "my qp", $fpmap->get_label() );
  }

  function test_set_label_add_rdfs_label_triple() {
    $fpmap = new QueryProfile("http://example.org/store/queryprofiles/1");
    $fpmap->set_label('my qp');

    $index = ARC2::getSimpleIndex( $fpmap->get_triples(), true) ;
    $this->assertEquals("my qp",  $index[$fpmap->uri]['http://www.w3.org/2000/01/rdf-schema#label'][0]);
  }
  function test_get_set_comment() {
    $fpmap = new QueryProfile("http://example.org/store/queryprofiles/1");
    $fpmap->set_comment('my qp is kewl');
    $this->assertEquals( "my qp is kewl", $fpmap->get_comment() );
  }

  function test_set_comment_add_rdfs_comment_triple() {
    $fpmap = new QueryProfile("http://example.org/store/queryprofiles/1");
    $fpmap->set_comment('my qp is kewl');

    $index = ARC2::getSimpleIndex( $fpmap->get_triples(), true) ;
    $this->assertEquals("my qp is kewl",  $index[$fpmap->uri]['http://www.w3.org/2000/01/rdf-schema#comment'][0]);

  }  

}
?>
