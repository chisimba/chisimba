<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_ARC_DIR . "ARC2.php";
require_once MORIARTY_DIR . 'httprequest.class.php';
require_once MORIARTY_DIR . 'httprequestfactory.class.php';
require_once MORIARTY_DIR . 'constants.inc.php';

class Contentbox {
  var $uri;
  var $request_factory;
  var $credentials;

  function Contentbox($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function make_search_uri( $query, $max=10, $offset=0) {
    $uri = $this->uri . '?query=' . urlencode($query) . '&max=' . urlencode($max) . '&offset=' . urlencode($offset);
    return $uri;
  }

  function search( $query, $max=10, $offset=0) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $request = $this->request_factory->make( 'GET', $this->make_search_uri($query, $max, $offset), $this->credentials );
    $request->set_accept(MIME_RSS);

    return $request->execute();
  }

  function search_to_triple_list( $query, $max=10, $offset=0 ) {
    $triples = array();

    $response = $this->search( $query, $max, $offset );
    $parser_args=array(
      "bnode_prefix"=>"genid",
      "base"=> $this->uri
    );


    if ( $response->body ) {
      $parser = ARC2::getRDFXMLParser($parser_args);
      $parser->parse($this->uri, $response->body );
      $triples = $parser->getTriples();
    }
    return $triples;
  }

  function search_to_resource_list( $query, $max=10, $offset=0 ) {
    $triples = array();
    $uri = $this->make_search_uri($query, $max, $offset);

    $response = $this->search( $query, $max, $offset );

    $body = $response->body;
    // fix up unprefixed rdf:resource in rss 1.0 otherwise ARC gets confused
    $body = preg_replace("~rdf:li resource=~", "rdf:li rdf:resource=", $body);

    $parser_args=array(
      "bnode_prefix"=>"genid",
      "base"=> $this->uri
    );
    $resources = new ResourceList();
    $resources->items = Array();

    if ( $response->body ) {
      $parser = ARC2::getRDFXMLParser($parser_args);
      $parser->parse($this->uri, $response->body );
      $triples = $parser->getTriples();
      $index = ARC2::getSimpleIndex($triples, true) ;




      $resources->title = $index[$uri][RSS_TITLE][0];
      $resources->description = $index[$uri][RSS_DESCRIPTION][0];
      $resources->start_index = $index[$uri][OS_STARTINDEX][0];
      $resources->items_per_page = $index[$uri][OS_ITEMSPERPAGE][0];
      $resources->total_results = $index[$uri][OS_TOTALRESULTS][0];

      $items_resource = $index[$uri][RSS_ITEMS][0];
      foreach ($index[$items_resource] as $items_property => $items_property_value) {
        if ( strpos( $items_property, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#_') === 0 ) {
          $resources->items[] = $index[$items_property_value[0]];
        }
      }
    }

    return $resources;
  }

}

class ResourceList {
  var $title;
  var $start_index;
  var $items_per_page;
  var $total_results;
  var $description;
  var $items;
}

?>
