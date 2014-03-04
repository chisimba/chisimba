<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'networkresource.class.php';
require_once MORIARTY_DIR. 'sparqlservice.class.php';
require_once MORIARTY_DIR. 'contentbox.class.php';
require_once MORIARTY_DIR. 'storegroupconfig.class.php';


class StoreGroup extends NetworkResource {

  function __construct($uri, $credentials = null) {
    parent::__construct($uri, $credentials);
    $this->add_resource_triple($this->uri, RDF_TYPE, BF_STOREGROUP);
  }
  
  function get_sparql_service() {
    return new SparqlService($this->uri . '/services/sparql', $this->credentials);
  }

  function get_config() {
    return new StoreGroupConfig($this->uri . '/config', $this->credentials);
  }
  
  function get_contentbox() {
    return new Contentbox($this->uri . '/items', $this->credentials);
  }
  
  function add_store_by_uri($store_uri) {
    $this->add_resource_triple($this->uri, BF_STORE, $store_uri);
  }
  
  function remove_all_stores() {
    $this->remove_property_values($this->uri, BF_STORE);
  }
}
?>
