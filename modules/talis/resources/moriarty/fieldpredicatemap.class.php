<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'networkresource.class.php';

class FieldPredicateMap extends NetworkResource {

  function __construct($uri, $credentials = null) {
    parent::__construct($uri, $credentials);
  }

  function add_mapping($p, $name, $analyzer = null) {
    $mapping_uri = $this->uri . '#' . $name;
    $this->add_resource_triple( $this->uri, FRM_MAPPEDDATATYPEPROPERTY, $mapping_uri);
    $this->add_resource_triple( $mapping_uri, FRM_PROPERTY, $p);
    $this->add_literal_triple( $mapping_uri, FRM_NAME, $name);
    if ( $analyzer ) {
      $this->add_resource_triple( $mapping_uri, BF_ANALYZER, $analyzer);
    }
    return $mapping_uri;
  }

  function remove_mapping($p, $name) {
    $index = $this->get_index();
    foreach ($index[$this->uri][FRM_MAPPEDDATATYPEPROPERTY] as $mapping) {
      if (($mapping['type'] == 'uri' || $mapping['type'] == 'bnode') && isset($index[$mapping['value']]) ) {
        $candidate_mapping_uri = $mapping['value'];
        foreach ( $index[$candidate_mapping_uri][FRM_PROPERTY] as $mapped_property_info) {
          if ( ($mapped_property_info['type'] == 'uri' || $mapped_property_info['type'] == 'bnode') && $mapped_property_info['value'] == $p) {
            foreach ( $index[$candidate_mapping_uri][FRM_NAME] as $mapped_name_info) {
              if ( ($mapped_name_info['type'] != 'uri' && $mapped_name_info['type'] != 'bnode') && $mapped_name_info['value'] == $name) {
                $this->remove_resource_triple( $this->uri, FRM_MAPPEDDATATYPEPROPERTY, $candidate_mapping_uri);
                $this->remove_triples_about($candidate_mapping_uri);
              }
            }
          }
        }
      }
    }
  }

  /**
   * Copies the mappings and other properties into new field/predicate map
   * Any URIs that are prefixed by the source field/predicate map's URI will be converted to
   * be prefixed with this field/predicate map's URI
   *
   * For example
   *   http://example.org/source/fpmaps/1#name
   * Would become
   *   http://example.org/destination/fpmaps/1#name
   *
   * @return A new FieldPredicateMap
   * @author Ian Davis
   **/
  function copy_to($new_uri) {
    $res = new FieldPredicateMap($new_uri, $this->credentials);
    $index = $this->get_index();

    foreach ($index as $uri => $uri_info) {
      $subject_uri = preg_replace('/^' . preg_quote($this->uri, '/') . '(.*)$/', $res->uri . '$1', $uri);
      foreach ($uri_info as $res_property_uri => $res_property_values) {
        foreach ($res_property_values as $res_property_info) {
          if ( $res_property_info['type'] == 'uri') {
            $value_uri = preg_replace('/^' . preg_quote($this->uri, '/') . '(.+)$/', $res->uri . '$1', $res_property_info['value']);
            $res->add_resource_triple( $subject_uri, $res_property_uri, $value_uri );
          }
          elseif ( $res_property_info['type'] == 'bnode') {
            $res->add_resource_triple( $subject_uri, $res_property_uri, $res_property_info['value'] );
          }
          else {
            $res->add_literal_triple( $subject_uri, $res_property_uri, $res_property_info['value'] );
          }
        }
      }
    }
    return $res;

  }
}
?>
