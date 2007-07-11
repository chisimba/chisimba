<?php
/*
  $sqldata[]="CREATE TABLE tbl_contextmodules (
  id varchar(32) NOT NULL default '',
  contextCode varchar(32) NOT NULL default '',
  moduleId varchar(50) NOT NULL default '',  
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY  (id,contextCode,moduleId),
  INDEX tbl_contextmodules_FKIndex1(contextCode),
  INDEX tbl_contextmodules_FKIndex2(moduleId)
  
)TYPE=InnoDB
";*/

$tablename = 'tbl_contextmodules';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'contextcode' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'moduleid' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE
		),
    'updated' => array(
        'type' => 'timestamp'
        )
    );
    
$name = 'tbl_contextmodules_FKIndex1';

$indexes = array(
                'fields' => array(
                	'contextcode' => array(), 
                    'moduleid' => array()
                )
        );
?>