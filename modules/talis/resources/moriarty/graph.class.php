<?php
class Graph {
  var $uri;
  var $credentials;
  var $request_factory;

  function Graph($uri, $credentials = null)  {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function apply_changeset($cs) {
    return $this->apply_changeset_rdfxml( $cs->to_rdfxml());
  }
  
  function apply_versioned_changeset($cs) {
    return $this->apply_versioned_changeset_rdfxml( $cs->to_rdfxml());
  }
  
  function apply_changeset_rdfxml($rdfxml) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri;

    $request = $this->request_factory->make( 'POST', $uri, $this->credentials);
    $request->set_accept("*/*");
    $request->set_content_type("application/vnd.talis.changeset+xml");
    $request->set_body( $rdfxml );

    return $request->execute();
  }

  function apply_versioned_changeset_rdfxml($rdfxml) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri . '/changesets';

    $request = $this->request_factory->make( 'POST', $uri, $this->credentials);
    $request->set_accept("*/*");
    $request->set_content_type("application/vnd.talis.changeset+xml");
    $request->set_body( $rdfxml );

    return $request->execute();
  }

  function submit_rdfxml($rdfxml) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri;
    $request = $this->request_factory->make( 'POST', $uri, $this->credentials);
    $request->set_content_type("application/rdf+xml");
    $request->set_accept("*/*");
    $request->set_body( $rdfxml );
    return $request->execute();
  }

  function _get_sparql_uri() {
    if ( preg_match('/(http:\/\/.+)\/meta/', $this->uri, $m) ) {
      return $m[1] . '/services/sparql';
    }
    else {
      return $this->uri;
    }    
  }

  function describe( $uri ) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $request = $this->request_factory->make( 'POST', $this->_get_sparql_uri() );
    $request->set_accept("application/rdf+xml");
    $request->set_content_type("application/x-www-form-urlencoded");
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }

    $request->set_body( "query=" . urlencode("DESCRIBE <$uri>") );

    return $request->execute();
  }

  function describe_to_triple_list( $uri ) {
    $triples = array();

    $response = $this->describe( $uri );
    $parser_args=array(
      "bnode_prefix"=>"genid",
      "base"=> $this->uri
    );
    $parser = ARC2::getRDFXMLParser($parser_args);

    if ( $response->body ) {
      $parser->parse($this->uri, $response->body );
      $triples = $parser->getTriples();
    }

    return $triples;
  }

  function describe_to_simple_graph( $uri ) {
    $graph = new SimpleGraph();

    $response = $this->describe( $uri );

    if ( $response->is_success() ) {
      $graph->from_rdfxml( $response->body );
    }

    return $graph;
  }

}
?>
