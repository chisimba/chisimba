<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';

class FacetService {
  var $uri;
  var $request_factory;
  var $credentials;

  function __construct($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function facets($query, $fields, $top = 10) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri . '?query=' . urlencode($query) . '&fields=' . urlencode(join(' ', $fields)) . '&top=' . urlencode($top) . '&output=xml';
    $request = $this->request_factory->make( 'GET', $uri , $this->credentials );
    $request->set_accept(MIME_XML);
    return $request->execute();
  }

  function facets_to_array($query, $fields, $top = 10) {
    $facets = array();
    $response = $this->facets($query, $fields, $top);
    if ($response->is_success()) {
      $facets = $this->parse_facet_xml($response->body);
    }
    return $facets;
  }

  function parse_facet_xml($xml) {
    $facets = array();

    $reader = new XMLReader();
    $reader->XML($xml);

    $field_terms = array();
    $field_name = '';
    while ($reader->read()) {
      if ( $reader->name == 'field') {

        if ( $reader->nodeType == XMLReader::ELEMENT) {
          $field_terms = array();
          $field_name = $reader->getAttribute("name");
        }
        elseif ( $reader->nodeType == XMLReader::END_ELEMENT) {
          $facets[$field_name] = $field_terms;
          $field_terms = array();
        }
      }
      elseif ( $reader->name == 'term') {
        if ( $reader->nodeType == XMLReader::ELEMENT) {
          $term = array();
          $term['value'] = $reader->getAttribute("value");
          $term['number'] = $reader->getAttribute("number");
          $field_terms[] = $term;
        }
      }
    }
    $reader->close();

    return $facets;
  }

}
?>
