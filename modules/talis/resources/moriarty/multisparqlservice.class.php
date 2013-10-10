<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_ARC_DIR . DIRECTORY_SEPARATOR . "ARC2.php";
require_once MORIARTY_DIR . 'sparqlservicebase.class.php';

class MultiSparqlService extends SparqlServiceBase {
  var $uri;
  var $request_factory;
  var $credentials;

  function MultiSparqlService($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function describe( $uri, $graphs=array() ) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $request = $this->request_factory->make( 'POST', $this->uri );
    $request->set_accept("application/rdf+xml");
    $request->set_content_type("application/x-www-form-urlencoded");
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }


    if ( is_array( $uri ) ) {
      $query = "DESCRIBE <" . implode('> <' , $uri) . ">";
    }
    else {
      $query = "DESCRIBE <$uri>";
    }

    foreach( $graphs as $graph_uri) {
      $query .= ' FROM <' . $graph_uri . '> ';
    }

    $request->set_body( "query=" . urlencode($query) );

    return $request->execute();
  }

  function describe_to_triple_list( $uri, $graphs=array() ) {
    $triples = array();

    $response = $this->describe( $uri, $graphs );

    if ( $response->body ) {

      $parser_args=array(
        "bnode_prefix"=>"genid",
        "base"=> $this->uri
      );
      $parser = ARC2::getRDFXMLParser($parser_args);

      $parser->parse("", $response->body );
      $triples = $parser->getTriples();
    }
    return $triples;
  }

}

?>
