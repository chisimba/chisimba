<?php
/*
* Template to display tasks associated with the case.
* @package pbl
*/

/*
* Template to display tasks associated with the case.
*/

// Suppress Page Variables
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressIM', TRUE);

$bodyParams='class="container" '; 
$this->setVarByRef('bodyParams',$bodyParams);

// Get Task information
$this->classroom->classroom();
$str = $this->classroom->writeTask();

// Display page
echo "<font color='#333333' size='3'>";
echo $str;
echo "</font>";
?>