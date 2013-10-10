<?php

require_once 'store.class.php';
require_once 'credentials.class.php';

$my_rdf = file_get_contents('test.rdf');
 
$credentials = new Credentials('charlvn', '');
 
$store = new Store('http://api.talis.com/stores/charlvn-dev1', $credentials );
$mb = $store->get_metabox();
$response = $mb->submit_rdfxml( $my_rdf );
 
echo $response->status_code;
if  ( $response->status_code >299) {
  echo ', server said: ' . htmlspecialchars( $response->body );
}
