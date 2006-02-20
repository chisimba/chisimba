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
$sqldata[] ="CREATE TABLE tbl_decisiontable_rule_condition (
  id VARCHAR(32) NOT NULL,
  conditionId VARCHAR(32) NOT NULL,
  ruleId VARCHAR(32) NOT NULL,
  PRIMARY KEY(id),
  INDEX rule_condition_FKIndex1(ruleId),
  INDEX rule_condition_FKIndex2(conditionId),
  FOREIGN KEY(ruleId)
    REFERENCES tbl_decisiontable_rule(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(conditionId)
    REFERENCES tbl_decisiontable_condition(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)TYPE=InnoDB COMMENT = 'Bridge table used to keep a list of conditions and rules.';";
?>
