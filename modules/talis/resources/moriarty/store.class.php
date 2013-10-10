<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'metabox.class.php';
require_once MORIARTY_DIR. 'sparqlservice.class.php';
require_once MORIARTY_DIR. 'multisparqlservice.class.php';
require_once MORIARTY_DIR. 'contentbox.class.php';
require_once MORIARTY_DIR. 'jobqueue.class.php';
require_once MORIARTY_DIR. 'config.class.php';
require_once MORIARTY_DIR. 'facetservice.class.php';

class Store {
  var $uri;
  var $credentials;

  function Store($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function get_metabox() {
    return new Metabox($this->uri . '/meta', $this->credentials);
  }

  function get_sparql_service() {
    return new SparqlService($this->uri . '/services/sparql', $this->credentials);
  }

  function get_multisparql_service() {
    return new MultiSparqlService($this->uri . '/services/multisparql', $this->credentials);
  }

  function get_contentbox() {
    return new Contentbox($this->uri . '/items', $this->credentials);
  }

  function get_job_queue() {
    return new JobQueue($this->uri . '/jobs', $this->credentials);
  }

  function get_config() {
    return new Config($this->uri . '/config', $this->credentials);
  }

  function get_facet_service() {
    return new FacetService($this->uri . '/services/facet', $this->credentials);
  }
}
?>
