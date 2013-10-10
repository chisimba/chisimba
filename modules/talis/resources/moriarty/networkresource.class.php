<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'simplegraph.class.php';

class NetworkResource extends SimpleGraph {
  var $uri;
  var $credentials;

  function NetworkResource($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }
  
  function set_label($label) {
    $this->add_literal_triple( $this->uri, RDFS_LABEL, $label);
  }

  function get_label() {
    return $this->get_first_literal($this->uri, RDFS_LABEL);
  }

  function set_comment($value) {
    $this->add_literal_triple( $this->uri, RDFS_COMMENT, $value);
  }

  function get_comment() {
    return $this->get_first_literal($this->uri, RDFS_COMMENT);
  }

 
  function get_from_network() {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }
    $uri = $this->uri;

    $request = $this->request_factory->make( 'GET', $uri);
    $request->set_accept(MIME_RDFXML);
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }
    
    $response = $request->execute();

    if ($response->is_success()) {
      $this->from_rdfxml( $response->body );
    }
    
    return $response;
  }

  function put_to_network() {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }
    $uri = $this->uri;

    $request = $this->request_factory->make( 'PUT', $uri);
    $request->set_content_type(MIME_RDFXML);
    $request->set_body( $this->to_rdfxml() );
    
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }

    $response = $request->execute();

    return $response;
  }

  function delete_from_network() {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }
    $uri = $this->uri;

    $request = $this->_make_request('DELETE', $uri);

    $response = $request->execute();

    return $response;
  }
  
  function _make_request($method, $uri) {
    $request = $this->request_factory->make($method, $uri);
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }
    return $request;
    
  }

}
?>
