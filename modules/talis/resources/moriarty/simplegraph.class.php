<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_ARC_DIR . "ARC2.php";

class SimpleGraph {
  var $_index = array();
  var $_ns = array (
                    'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
                    'cs' => 'http://purl.org/vocab/changeset/schema#',
                    'bf' => 'http://schemas.talis.com/2006/bigfoot/configuration#',
                    'frm' => 'http://schemas.talis.com/2006/frame/schema#',

                    'dc' => 'http://purl.org/dc/elements/1.1/',
                    'dct' => 'http://purl.org/dc/terms/',
                    'dctype' => 'http://purl.org/dc/dcmitype/',

                    'foaf' => 'http://xmlns.com/foaf/0.1/',
                    'bio' => 'http://purl.org/vocab/bio/0.1/',
                    'geo' => 'http://www.w3.org/2003/01/geo/wgs84_pos#',
                    'rel' => 'http://purl.org/vocab/relationship/',
                    'rss' => 'http://purl.org/rss/1.0/',
                    'wn' => 'http://xmlns.com/wordnet/1.6/',
                    'air' => 'http://www.daml.org/2001/10/html/airport-ont#',
                    'contact' => 'http://www.w3.org/2000/10/swap/pim/contact#',
                    'ical' => 'http://www.w3.org/2002/12/cal/ical#',
                    'frbr' => 'http://purl.org/vocab/frbr/core#',

                    'ad' => 'http://schemas.talis.com/2005/address/schema#',
                    'lib' => 'http://schemas.talis.com/2005/library/schema#',
                    'dir' => 'http://schemas.talis.com/2005/dir/schema#',
                    'user' => 'http://schemas.talis.com/2005/user/schema#',
                    'sv' => 'http://schemas.talis.com/2005/service/schema#',
                  );
  function set_namespace_mapping($prefix, $uri) {
    $this->_ns[$prefix] = $uri;
  }


  function add_resource_triple($s, $p, $o) {
    if (!isset($this->_index[$s])) {
      $this->_index[$s] = array();
    }

    if (!isset($this->_index[$s][$p])) {
      $this->_index[$s][$p] = array();
    }

    foreach ( $this->_index[$s][$p] as $o_existing) {
      if (isset($o_existing['type']) && $o_existing['type'] == 'uri' &&
          isset($o_existing['value']) && $o_existing['value'] == $o ) {
        return;
      }
    }

    $type = strpos($o, '_:' ) === 0 ? 'bnode' : 'uri';
    $this->_index[$s][$p][] = array('value' => $o, 'type' => $type);
  }

  function add_literal_triple($s, $p, $o, $lang = null, $dt = null) {
    if (!isset($this->_index[$s])) {
      $this->_index[$s] = array();
    }

    if (!isset($this->_index[$s][$p])) {
      $this->_index[$s][$p] = array();
    }

    $o_array = array('value' => $o, 'type' => 'literal' );
    if ( $lang != null ) {
      $o_array['lang'] = $lang;
    }
    if ( $dt != null ) {
      $o_array['datatype'] = $dt;
    }

    foreach ( $this->_index[$s][$p] as $o_existing) {
      if (isset($o_existing['type']) && $o_existing['type'] == $o_array['type']
          && isset($o_existing['value']) && $o_existing['value'] == $o_array['value']
          && ( ( isset($o_array['lang']) && isset($o_existing['lang']) && $o_array['lang'] == $o_existing['lang']) ||
               ( !isset($o_array['lang']) && !isset($o_existing['lang']) ) )
          && ( ( isset($o_array['datatype']) && isset($o_existing['datatype']) && $o_array['datatype'] == $o_existing['datatype']) ||
               ( !isset($o_array['datatype']) && !isset($o_existing['datatype']) ) )
          ) {
        return;
      }
    }

    $this->_index[$s][$p][] = $o_array;
  }

  function get_triples() {
    return ARC2::getTriplesFromIndex($this->_to_arc_index($this->_index));
  }

  function get_index() {
    return $this->_index;
  }


  function to_rdfxml() {
    $serializer = ARC2::getRDFXMLSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
 }

  function to_turtle() {
    $serializer = ARC2::getTurtleSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }

  function to_ntriples() {
    $serializer = ARC2::getComponent('NTriplesSerializer', array());
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }



  function to_json() {
    $serializer = ARC2::getRDFJSONSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }
  function get_first_literal($s, $p, $default = null) {
    if ( array_key_exists($s, $this->_index) && array_key_exists($p, $this->_index[$s]) ) {
      foreach ($this->_index[$s][$p] as $value) {
        if ($value['type'] == 'literal') {
          return $value['value'];
        }
      }
    }
    else {
      return $default;
    }
  }

  function get_first_resource($s, $p, $default = null) {
    if ( array_key_exists($s, $this->_index) && array_key_exists($p, $this->_index[$s]) ) {
      foreach ($this->_index[$s][$p] as $value) {
        if ($value['type'] == 'uri' || $value['type'] == 'bnode' ) {
          return $value['value'];
        }
      }
    }
    else {
      return $default;
    }
  }

  function remove_resource_triple( $s, $p, $o) {
    for ($i = count($this->_index[$s][$p]) - 1; $i >= 0; $i--) {
      if (($this->_index[$s][$p][$i]['type'] == 'uri' || $this->_index[$s][$p][$i]['type'] == 'bnode') && $this->_index[$s][$p][$i]['value'] == $o)  {
        array_splice($this->_index[$s][$p], $i, 1);
      }
    }

    if (count($this->_index[$s][$p]) == 0) {
      unset($this->_index[$s][$p]);
    }
    if (count($this->_index[$s]) == 0) {
      unset($this->_index[$s]);
    }

  }

  function remove_triples_about($s) {
    unset($this->_index[$s]);
  }



  function from_rdfxml($rdfxml, $base='') {
    if ($rdfxml) {
      $parser = ARC2::getRDFXMLParser();
      $parser->parse($base, $rdfxml );
      $index = ARC2::getSimpleIndex($parser->getTriples(), false) ;
      $this->_index= $this->_from_arc_index( $index);
    }
  }

  // until ARC2 upgrades to support RDF/PHP we need to rename all 'val' keys to 'value'
  function _from_arc_index(&$index) {
    $ret =array();

    foreach ($index as $s => $s_info) {
      $ret[$s] = array();
      foreach ($s_info as $p => $p_info) {
        $ret[$s][$p] = array();
        foreach ($p_info as $o) {
          $o_new = array();
          foreach ($o as $key => $value) {
            if ( $key == 'val' ) {
              $o_new['value'] = $value;
            }
            else if ( $key == 'dt' ) {
              $o_new['datatype'] = $value;
            }
            else if ( $key == 'type' && $value == 'iri' ) {
              $o_new['type'] = 'uri';
            }
            else {
              $o_new[$key] = $value;
            }
          }
          $ret[$s][$p][] = $o_new;
        }
      }
    }
    return $ret;
  }

  // until ARC2 upgrades to support RDF/PHP we need to rename all 'value' keys to 'val'
  function _to_arc_index(&$index) {
    $ret = array();

    foreach ($index as $s => $s_info) {
      $ret[$s] = array();
      foreach ($s_info as $p => $p_info) {
        $ret[$s][$p] = array();
        foreach ($p_info as $o) {
          $o_new = array();
          foreach ($o as $key => $value) {
            if ( $key == 'value' ) {
              $o_new['val'] = $value;
            }
            else if ( $key == 'datatype' ) {
              $o_new['dt'] = $value;
            }
            else if ( $key == 'type' && $value == 'uri' ) {
              $o_new['type'] = 'iri';
            }
            else {
              $o_new[$key] = $value;
            }
          }
          $ret[$s][$p][] = $o_new;
        }
      }
    }
    return $ret;
  }

  function has_resource_triple($s, $p, $o) {
    if (array_key_exists($s, $this->_index) ) {
      if (array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          if ( ( $value['type'] == 'uri' || $value['type'] == 'bnode') && $value['value'] == $o) {
            return true;
          }
        }
      }
    }

    return false;
  }


  function remove_property_values($s, $p) {
    unset($this->_index[$s][$p]);

  }

  function remove_all_triples() {
    $this->_index = array();
  }

}

?>
