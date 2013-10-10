<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_ARC_DIR . DIRECTORY_SEPARATOR . "ARC2.php";

class SparqlServiceBase {
  var $uri;
  var $request_factory;
  var $credentials;

  function SparqlServiceBase($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function describe( $uri ) {
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
      $request->set_body( "query=" . urlencode("DESCRIBE <" . implode('> <' , $uri) . ">") );
    }
    else {
      $request->set_body( "query=" . urlencode("DESCRIBE <$uri>") );
    }
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

  function graph( $query ) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }
    $request = $this->request_factory->make( 'POST', $this->uri );
    $request->set_accept(MIME_RDFXML);
    $request->set_content_type(MIME_FORMENCODED);
    $request->set_body( "query=" . urlencode($query) );
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }
    return $request->execute();
  }

  function graph_to_triple_list($query ) {
    $triples = array();
    $response = $this->graph( $query );

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

  function construct_to_triple_list($query ) {
    return $this->graph_to_triple_list($query );
  }

  function construct_to_simple_graph( $query ) {
    $graph = new SimpleGraph();

    $response = $this->graph( $query );

    if ( $response->is_success() ) {
      $graph->from_rdfxml( $response->body );
    }

    return $graph;
  }



  function select( $query ) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $request = $this->request_factory->make( 'POST', $this->uri );
    $request->set_accept(MIME_SPARQLRESULTS);
    $request->set_content_type(MIME_FORMENCODED);
    $request->set_body( "query=" . urlencode($query) );
    if  ($this->credentials != null) {
      $request->set_auth( $this->credentials->get_auth() );
    }

    return $request->execute();
  }

  function select_to_array( $query ) {
    $results = array();
    $response = $this->select( $query );
    $results = $this->parse_select_results( $response->body );
    return $results;
  }

  function parse_select_results( $xml ) {
    $results = array();
    $reader = new XMLReader();
    $reader->XML($xml);

    $result = array();
    $bindingName = null;
    $binding = array();
    while ($reader->read()) {
      if ( $reader->name == 'result') {

        if ( $reader->nodeType == XMLReader::ELEMENT) {
          $result = array();
        }
        elseif ( $reader->nodeType == XMLReader::END_ELEMENT) {
          array_push( $results, $result);
          $result = array();
        }
      }
      elseif ( $reader->name == 'binding') {
        if ( $reader->nodeType == XMLReader::ELEMENT) {
          $bindingName = $reader->getAttribute("name");
          $binding = array();
        }
        elseif ( $reader->nodeType == XMLReader::END_ELEMENT) {
          $result[ $bindingName ] = $binding;
          $bindingName = null;
          $binding = array();
        }
      }
      elseif ( $reader->name == 'uri' && $reader->nodeType == XMLReader::ELEMENT) {
        $binding['type'] = 'uri';
        $value = '';
        while ($reader->read()) {
          if ($reader->nodeType == XMLReader::TEXT
            || $reader->nodeType == XMLReader::CDATA
            || $reader->nodeType == XMLReader::WHITESPACE
            || $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE) {
             $value .= $reader->value;
          }
          else if ($reader->nodeType == XMLReader::END_ELEMENT) {
            break;
          }
        }
        $binding['value'] = $value;
      }
      elseif ( $reader->name == 'literal' && $reader->nodeType == XMLReader::ELEMENT) {
        $binding['type'] = 'literal';
        $datatype = $reader->getAttribute("datatype");
        if ( $datatype ) {
          $binding['datatype'] = $datatype;
        }
        $lang = $reader->getAttribute("xml:lang");
        if ( $lang ) {
          $binding['lang'] = $lang;
        }
        $value = '';
        while ($reader->read()) {
          if ($reader->nodeType == XMLReader::TEXT
            || $reader->nodeType == XMLReader::CDATA
            || $reader->nodeType == XMLReader::WHITESPACE
            || $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE) {
             $value .= $reader->value;
          }
          else if ($reader->nodeType == XMLReader::END_ELEMENT) {
            break;
          }
        }
        $binding['value'] = $value;
      }
      elseif ( $reader->name == 'bnode' && $reader->nodeType == XMLReader::ELEMENT) {
        $binding['type'] = 'bnode';
        $value = '';
        while ($reader->read()) {
          if ($reader->nodeType == XMLReader::TEXT
            || $reader->nodeType == XMLReader::CDATA
            || $reader->nodeType == XMLReader::WHITESPACE
            || $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE) {
             $value .= $reader->value;
          }
          else if ($reader->nodeType == XMLReader::END_ELEMENT) {
            break;
          }
        }
        $binding['value'] = $value;
      }
    }
    $reader->close();
    return $results;
  }

  function ask( $query ) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $request = $this->request_factory->make( 'POST', $this->uri, $this->credentials );
    $request->set_accept(MIME_SPARQLRESULTS);
    $request->set_content_type(MIME_FORMENCODED);
    $request->set_body( "query=" . urlencode($query) );

    return $request->execute();
  }

  function parse_ask_results( $xml ) {
    $reader = new XMLReader();
    $reader->XML($xml);

    $result = false;
    $bindingName = null;
    $binding = array();
    while ($reader->read()) {
      if ( $reader->name == 'boolean') {
        $value = '';
        while ($reader->read()) {
          if ($reader->nodeType == XMLReader::TEXT
            || $reader->nodeType == XMLReader::CDATA
            || $reader->nodeType == XMLReader::WHITESPACE
            || $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE) {
             $value .= $reader->value;
          }
          else if ($reader->nodeType == XMLReader::END_ELEMENT) {
            break;
          }
        }
        $reader->close();
        return ( strtolower(trim($value)) == 'true' );
      }
    }

    return false;
  }


}
?>
