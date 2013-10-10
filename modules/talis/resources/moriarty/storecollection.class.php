<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'simplegraph.class.php';
require_once MORIARTY_DIR. 'httprequestfactory.class.php';

class StoreCollection extends SimpleGraph {
  var $uri;
  var $credentials;

  function StoreCollection($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function retrieve() {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }
    $uri = $this->uri;

    $request = $this->request_factory->make( 'GET', $uri);
    $request->set_accept(MIME_RDFXML);

    $response = $request->execute();

    if ($response->is_success()) {
      $this->from_rdfxml( $response->body );
    }
  }

  function create_store($name, $template_uri) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri;
    $mimetype = MIME_RDFXML;

    $request = $this->request_factory->make( 'POST', $uri);
    $request->set_accept("*/*");
    $request->set_content_type($mimetype);

    $sr = new SimpleGraph();
    $sr->add_resource_triple('_:req', BF_STORETEMPLATE, $template_uri);
    $sr->add_literal_triple( '_:req', BF_STOREREF, $name);


    $request->set_body( $sr->to_rdfxml() );
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }
    return $request->execute();

  }

  function get_store_uris() {
    $list = array();
    foreach ($this->_index[$this->uri][BF_STORE] as $store_resource) {
      if ( $store_resource['type'] == 'uri' || $store_resource['type'] == 'bnode') {
        $list[] = $store_resource['value'];
      }
    }

    return $list;
  }

}


?>
