<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'networkresource.class.php';

class QueryProfile extends NetworkResource {

  function __construct($uri, $credentials = null) {
    parent::__construct($uri, $credentials);
  }


  /**
   * Adds the field and weight to the query profile
   *
   * @return URI of the added field weight
   * @author Ian Davis
   **/
  function add_field_weight($name, $weight) {
    $field_weight_uri = $this->uri . '#' . $name;
    $this->add_resource_triple( $this->uri, BF_FIELDWEIGHT, $field_weight_uri);
    $this->add_literal_triple( $field_weight_uri, BF_WEIGHT, $weight);
    $this->add_literal_triple( $field_weight_uri, FRM_NAME, $name);
    return $field_weight_uri;
  }

  /**
   * Removes the weight for the named field
   *
   * @return nothing
   * @author Ian Davis
   **/
  function remove_field_weight($name) {
    $index = $this->get_index();
    foreach ($index[$this->uri][BF_FIELDWEIGHT] as $field_weight_info) {
      if (($field_weight_info['type'] == 'uri' || $field_weight_info['type'] == 'bnode') && isset($index[$field_weight_info['value']]) ) {
        $candidate_field_weight_uri = $field_weight_info['value'];
        foreach ( $index[$candidate_field_weight_uri][FRM_NAME] as $field_name_info) {
          if ( ($field_name_info['type'] != 'uri' && $field_name_info['type'] != 'bnode') && $field_name_info['value'] == $name) {
            $this->remove_resource_triple( $this->uri, BF_FIELDWEIGHT, $candidate_field_weight_uri);
            $this->remove_triples_about($candidate_field_weight_uri);
          }
        }
      }
    }
  }

  /**
   * Copies the field weights and other properties into new query profile.
   * Any URIs that are prefixed by the source query profile URI will be converted to
   * be prefixed with this query profile's URI
   *
   * For example
   *   http://example.org/source/queryprofile/1#name
   * Would become
   *   http://example.org/destination/queryprofile/1#name
   *
   * @return A new QueryProfile
   * @author Ian Davis
   **/
  function copy_to($new_uri) {
    $qp = new QueryProfile($new_uri, $this->credentials);
    $index = $this->get_index();

    foreach ($index as $uri => $uri_info) {
      $subject_uri = preg_replace('/^' . preg_quote($this->uri, '/') . '(.*)$/', $qp->uri . '$1', $uri);
      foreach ($uri_info as $qp_property_uri => $qp_property_values) {
        foreach ($qp_property_values as $qp_property_info) {
          if ( $qp_property_info['type'] == 'uri') {
            $value_uri = preg_replace('/^' . preg_quote($this->uri, '/') . '(.+)$/', $qp->uri . '$1', $qp_property_info['value']);
            $qp->add_resource_triple( $subject_uri, $qp_property_uri, $value_uri );
          }
          elseif ( $qp_property_info['type'] == 'bnode') {
            $qp->add_resource_triple( $subject_uri, $qp_property_uri, $qp_property_info['value'] );
          }
          else {
            $qp->add_literal_triple( $subject_uri, $qp_property_uri, $qp_property_info['value'] );
          }
        }
      }
    }
    return $qp;

  }


}
?>
