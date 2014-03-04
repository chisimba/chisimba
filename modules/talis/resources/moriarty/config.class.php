<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'fieldpredicatemap.class.php';
require_once MORIARTY_DIR. 'queryprofile.class.php';

class Config {

  var $uri;
  var $request_factory;
  var $credentials;

  function Config($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function get_first_fpmap() {
    return new FieldPredicateMap( $this->get_first_fpmap_uri(), $this->credentials);
  }

  function get_first_query_profile() {
    return new QueryProfile( $this->get_first_query_profile_uri(), $this->credentials);
  }

  /**
   * Gets the URI of the first field/predicate map in the store
   * This is much more complicated than first appears since a store
   * can be configured (by Talis) to hold its field/predicate map
   * anywhere. This method understands all the existing stores and their
   * URI layouts.
   *
   * @return string
   * @author Ian Davis
   **/
  function get_first_fpmap_uri() {
    if (preg_match("/^http:\/\/api\.talis\.com\/stores\/([a-z][a-zA-Z0-9-]+)\/config$/", $this->uri, $matches) ) {
      $store_name = $matches[1];    
    
      if ( in_array($store_name, array('ajmg-dev1','beobal-dev1', 'danja-dev1', 'dataMonitoring', 'iand-dev1', 'iand-dev2', 'iand-dev3', 'jingye-dev1', 'kwijibo-dev1', 'malcyl-dev1', 'quoll-dev1', 'schema-cache', 'silkworm-dev', 'silkworm', 'source-dev1', 'source-qa1', 'tomh-dev1')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/default/fpmaps/default';
      }
      else if ( preg_match("/^engage-dev\d+$/", $store_name) 
                || preg_match("/^engagetenant\d+$/", $store_name)  
                || preg_match("/^list-demo\d+$/", $store_name) 
                || preg_match("/^list-dev\d+$/", $store_name) 
                || preg_match("/^list-qa\d+$/", $store_name)  
                || preg_match("/^nuggetengage-demo\d+$/", $store_name)    
                || preg_match("/^nuggetengage-qa\d+$/", $store_name) 
                || preg_match("/^zephyr-cust\d+$/", $store_name)    
                || $store_name == 'engagetenantstore'  
                || $store_name == 'list-tenants-dev'   ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/metaboxIndex/fpmaps/fpmap';
      }
      else if ( in_array($store_name, array('bib-sandbox', 'inst-5050', 'inst-u138', 'ukbib')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/m21Advanced/fpmaps/fpmap';
      }
      else if ( in_array($store_name, array('holdings')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/m21Holdings/fpmaps/fpmap';
      }
      else if ( in_array($store_name, array('union')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/union/fpmaps/fpmap';
      }
      else if ( in_array($store_name, array('wikipedia')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/abstracts/fpmaps/fpmap';
      }
      else if ( in_array($store_name, array('gatech')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/m21Advanced/fpmap';
      }
      else if ( in_array($store_name, array('cenotelist')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/default/fpmaps/fpmap';
      }
      else if ( in_array($store_name, array('image-sandbox')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/image-sandbox/fpmaps/fpmap';
      }
      else if ( in_array($store_name, array('cnimages')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/cnimages/fpmaps/fpmap';
      }
    }
    
    return $this->uri . '/fpmaps/1';

  }
  /**
   * Gets the URI of the first query profile in the store
   * This is much more complicated than first appears since a store
   * can be configured (by Talis) to hold its field/predicate map
   * anywhere. This method understands all the existing stores and their
   * URI layouts.
   *
   * @return string
   * @author Ian Davis
   **/
  function get_first_query_profile_uri() {

    if (preg_match("/^http:\/\/api\.talis\.com\/stores\/([a-z][a-zA-Z0-9-]+)\/config$/", $this->uri, $matches) ) {
      $store_name = $matches[1];    
    
      if ( in_array($store_name, array('ajmg-dev1','beobal-dev1', 'danja-dev1', 'dataMonitoring', 'iand-dev1', 'iand-dev2', 'iand-dev3', 'jingye-dev1', 'kwijibo-dev1', 'malcyl-dev1', 'quoll-dev1', 'schema-cache', 'silkworm-dev', 'silkworm', 'source-dev1', 'source-qa1', 'tomh-dev1')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/default/queryprofiles/default';
      }
      else if ( preg_match("/^engage-dev\d+$/", $store_name) 
                || preg_match("/^engagetenant\d+$/", $store_name)  
                || preg_match("/^list-demo\d+$/", $store_name) 
                || preg_match("/^list-dev\d+$/", $store_name) 
                || preg_match("/^list-qa\d+$/", $store_name)  
                || preg_match("/^nuggetengage-demo\d+$/", $store_name)    
                || preg_match("/^nuggetengage-qa\d+$/", $store_name) 
                || preg_match("/^zephyr-cust\d+$/", $store_name)    
                || $store_name == 'engagetenantstore'  
                || $store_name == 'list-tenants-dev'   ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/metaboxIndex/queryprofiles/default';
      }
      else if ( in_array($store_name, array('bib-sandbox', 'inst-5050', 'inst-u138', 'ukbib')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/m21Advanced/queryprofiles/default';
      }
      else if ( in_array($store_name, array('holdings')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/m21Holdings/queryprofiles/default';
      }
      else if ( in_array($store_name, array('union')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/union/queryprofiles/default';
      }
      else if ( in_array($store_name, array('wikipedia')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/abstracts/queryprofiles/default';
      }
      else if ( in_array($store_name, array('gatech')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/m21Advanced/queryprofiles/default';
      }
      else if ( in_array($store_name, array('cenotelist')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/default/queryprofiles/default';
      }
      else if ( in_array($store_name, array('image-sandbox')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/image-sandbox/queryprofiles/default';
      }
      else if ( in_array($store_name, array('cnimages')) ) {
         return 'http://api.talis.com/stores/' . $store_name . '/indexes/cnimages/queryprofiles/default';
      }
    }
    return $this->uri . '/queryprofiles/1';
  }
}

?>
