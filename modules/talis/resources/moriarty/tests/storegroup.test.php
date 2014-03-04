<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'storegroup.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';

class StoreGroupTest extends PHPUnit_Framework_TestCase {
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


  function test_get_sparql_service() {
    $group = new StoreGroup("http://example.org/group");
    $this->assertEquals( "http://example.org/group/services/sparql", $group->get_sparql_service()->uri );
  }

  function test_get_sparql_service_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $group = new StoreGroup("http://example.org/group", $credentials);
    $this->assertEquals( $credentials, $group->get_sparql_service()->credentials );
  }

  function test_get_contentbox() {
    $group = new StoreGroup("http://example.org/group");
    $this->assertEquals( "http://example.org/group/items", $group->get_contentbox()->uri );
  }

  function test_get_contentbox_service_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $group = new StoreGroup("http://example.org/group", $credentials);
    $this->assertEquals( $credentials, $group->get_contentbox()->credentials );
  }

  function test_get_config() {
    $group = new StoreGroup("http://example.org/group");
    $this->assertEquals( "http://example.org/group/config", $group->get_config()->uri );
  }

  function test_get_config_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $group = new StoreGroup("http://example.org/group", $credentials);
    $this->assertEquals( $credentials, $group->get_config()->credentials );
  }


  function test_add_store_by_uri() {
    $group = new StoreGroup("http://example.org/groups/1");
    $group->add_store_by_uri("http://example.org/stores/1");

    $index = $group->get_index();
    $this->assertEquals(1, count($index[$group->uri][BF_STORE]));
    $this->assertEquals("http://example.org/stores/1", $index[$group->uri][BF_STORE][0]['value']);

  }

  function test_remove_all_stores() {
    $group = new StoreGroup("http://example.org/groups/1");
    $group->add_store_by_uri("http://example.org/stores/1");
    $group->add_store_by_uri("http://example.org/stores/2");
    $group->add_store_by_uri("http://example.org/stores/3");

    $index = $group->get_index();
    $this->assertEquals(3, count($index[$group->uri][BF_STORE]));

    $group->remove_all_stores();

    $index = $group->get_index();
    $this->assertFalse(array_key_exists(BF_STORE, $index[$group->uri]));

  }

}
?>
