<?php
/**
* @package etd
*/

/**
* Template for displaying the form for submitting an etd.
* @param string $search Either the search criteria or results.
*/

$this->setLayoutTemplate('etd_layout_tpl.php');

/* *** Start search page *** */

$str = $search;

/* *** End search page *** */

echo $str;
?>