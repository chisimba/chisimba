<?php
require_once 'store.class.php';
require_once 'constants.inc.php';

$store = new Store('http://api.talis.com/stores/ukbib');
$contentbox = $store->get_contentbox();

$results = $contentbox->search_to_resource_list("feynman", 10, 0);
echo '<h1>' . $results->title . '</h1>';
foreach ($results->items as $item) {
  echo '<p><a href="' . $item[RSS_LINK][0] . '">' . $item[RSS_TITLE][0] . '</a></p>';
}
?>