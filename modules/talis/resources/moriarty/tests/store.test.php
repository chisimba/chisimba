<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';

class StoreTest extends PHPUnit_Framework_TestCase {
  function test_get_metabox() {
    $store = new Store("http://example.org/store");
    $this->assertEquals( "http://example.org/store/meta", $store->get_metabox()->uri );
  }

  function test_get_metabox_includes_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $store = new Store("http://example.org/store", $credentials);
    $this->assertEquals( $credentials, $store->get_metabox()->credentials );
  }

  function test_get_sparql_service() {
    $store = new Store("http://example.org/store");
    $this->assertEquals( "http://example.org/store/services/sparql", $store->get_sparql_service()->uri );
  }

  function test_get_sparql_service_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $store = new Store("http://example.org/store", $credentials);
    $this->assertEquals( $credentials, $store->get_sparql_service()->credentials );
  }

  function test_get_multisparql_service() {
    $store = new Store("http://example.org/store");
    $this->assertEquals( "http://example.org/store/services/multisparql", $store->get_multisparql_service()->uri );
  }

  function test_get_multisparql_service_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $store = new Store("http://example.org/store", $credentials);
    $this->assertEquals( $credentials, $store->get_multisparql_service()->credentials );
  }

  function test_get_contentbox() {
    $store = new Store("http://example.org/store");
    $this->assertEquals( "http://example.org/store/items", $store->get_contentbox()->uri );
  }

  function test_get_contentbox_service_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $store = new Store("http://example.org/store", $credentials);
    $this->assertEquals( $credentials, $store->get_contentbox()->credentials );
  }

  function test_get_job_queue() {
    $store = new Store("http://example.org/store");
    $this->assertEquals( "http://example.org/store/jobs", $store->get_job_queue()->uri );
  }

  function test_get_job_queue_service_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $store = new Store("http://example.org/store", $credentials);
    $this->assertEquals( $credentials, $store->get_job_queue()->credentials );
  }

  function test_get_config() {
    $store = new Store("http://example.org/store");
    $this->assertEquals( "http://example.org/store/config", $store->get_config()->uri );
  }

  function test_get_config_service_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $store = new Store("http://example.org/store", $credentials);
    $this->assertEquals( $credentials, $store->get_config()->credentials );
  }
  function test_get_facet_service() {
    $store = new Store("http://example.org/store");
    $this->assertEquals( "http://example.org/store/services/facet", $store->get_facet_service()->uri );
  }

  function test_get_facet_service_sets_credentials() {
    $credentials = new Credentials('scooby', 'shaggy');
    $store = new Store("http://example.org/store", $credentials);
    $this->assertEquals( $credentials, $store->get_facet_service()->credentials );
  }
}
?>
