<?php
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package decisiontable
* @subpackage SQL
* @version 0.1
* @since 29 March 2005
* @author Jonathan Abrahams
* @filesource
*/
$sqldata[] = "CREATE TABLE `tbl_decisiontable_conditiontype` (
  `id` varchar(32) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `className` varchar(50) NOT NULL default '',
  `moduleName` varchar(50) NOT NULL default ''
) TYPE=InnoDB COMMENT='Table used to store condition type as used by the decisionta';";
?>
