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
/*
$sqldata[] ="CREATE TABLE tbl_decisiontable_action_rule (
  id VARCHAR(32) NOT NULL,
  ruleId VARCHAR(32) NOT NULL,
  actionId VARCHAR(32) NOT NULL,
  PRIMARY KEY(id),
  INDEX action_rule_FKIndex1(actionId),
  INDEX action_rule_FKIndex2(ruleId),
  FOREIGN KEY(actionId)
    REFERENCES tbl_decisiontable_action(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(ruleId)
    REFERENCES tbl_decisiontable_rule(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
) TYPE=InnoDB COMMENT = 'Bridge table used to keep a list of rules and actions.';";
*/

// Table Name
$tablename = 'tbl_decisiontable_action_rule';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bridge table used to keep a list of rules and actions.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'ruleId' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
        ),
    'actionId' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
        )
    );

//create other indexes here...

$name = 'action_rule_FKIndex1';

$indexes = array(
                'fields' => array(
                	'actionId' => array(),
                    'ruleId' => array()
                )
        );
?>