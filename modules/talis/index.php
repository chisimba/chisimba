<?php

require_once 'store.class.php';
require_once 'constants.inc.php';
 
$store = new Store('http://api.talis.com/stores/charlvn-dev1');
$contentbox = $store->get_contentbox();
 
$results = $contentbox->search_to_resource_list("cat", 10, 0);
echo '<h1>' . $results->title . '</h1>';
foreach ($results->items as $item) {
var_dump($item);
  echo '<p><a href="' . $item[RSS_LINK][0] . '">' . $item[RSS_TITLE][0] . '</a></p>';
}
