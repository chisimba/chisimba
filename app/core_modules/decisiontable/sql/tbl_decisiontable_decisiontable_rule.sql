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
$sqldata[] ="CREATE TABLE tbl_decisiontable_decisiontable_rule (
  id VARCHAR(32) NOT NULL,
  ruleId VARCHAR(32) NOT NULL,
  decisiontableId VARCHAR(32) NOT NULL,
  PRIMARY KEY(id),
  INDEX decisiontable_rule_FKIndex1(decisiontableId),
  INDEX decisiontable_rule_FKIndex2(ruleId),
  FOREIGN KEY(decisiontableId)
    REFERENCES tbl_decisiontable_decisiontable(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(ruleId)
    REFERENCES tbl_decisiontable_rule(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)TYPE=InnoDB COMMENT = 'Bridge table used to keep a list of rules and decision tables.';";
?>
