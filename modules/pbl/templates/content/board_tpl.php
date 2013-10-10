<?php
/*
* Template for display of case information on the 'board'.
* @package pbl
*/

/*
* Template for display of case information on the 'board'.
*/

// Suppress Page Variables
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressIM', TRUE);

$bodyParams='class="container" ';
$this->setVarByRef('bodyParams',$bodyParams);

// Retrieve case information and write it to the iframe
$str=$this->classroom->writeBoard();

// Update 'tasks' iframe whenever 'board' is updated/refreshed
$href = $this->uri(array('action'=>'showtasks'));
// Replace the &amp; to & in the url
//$href = preg_replace('/&amp;/', '&', $href);
echo "<script language='text/javascript'>parent.tasks.location.href='$href'; </script>";

// Display case information
echo "<font color='#333333' size='3'>";
echo $str;
echo "</font>";
?>