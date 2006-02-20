<?php
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package decisiontable
* @subpackage SQL
* @version 0.1
* @since 04 Febuary 2005
* @author Jonathan Abrahams
* @filesource
*/
$sqldata[] ="CREATE TABLE tbl_decisiontable_decisiontable (
  id VARCHAR(32) NOT NULL,
  name VARCHAR(50) NULL,
  PRIMARY KEY(id)
) TYPE=InnoDB COMMENT='Table used to keep a list of decisiontables.';";
?>
