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
$sqldata[] ="CREATE TABLE tbl_decisiontable_decisiontable_action (
  id VARCHAR(32) NOT NULL,
  actionId VARCHAR(32) NOT NULL,
  decisiontableId VARCHAR(32) NOT NULL,
  PRIMARY KEY(id),
  INDEX decisiontable_action_FKIndex1(decisiontableId),
  INDEX decisiontable_action_FKIndex2(actionid),
  FOREIGN KEY(decisiontableId)
    REFERENCES tbl_decisiontable_decisiontable(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(actionid)
    REFERENCES tbl_decisiontable_action(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) TYPE=InnoDB COMMENT = 'Bridge table used to keep a list of actions and decision tables.'";
?>
