<?
/*
  $sqldata[]="CREATE TABLE tbl_context_parentnodes (
  id VARCHAR(32) NOT NULL,
  tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode VARCHAR(255) NOT NULL,
  tbl_context_parentnodes_has_tbl_context_tbl_context_id VARCHAR(32) NOT NULL,
  userId VARCHAR(255) NULL,
  dateCreated DATE NULL,
  datemodified DATE NULL,
  menu_text VARCHAR(255) NULL,
  title VARCHAR(255) NULL,  
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id),
  INDEX tbl_context_parentnodes_FKIndex1(tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode, tbl_context_parentnodes_has_tbl_context_tbl_context_id),
  FOREIGN KEY(tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode, tbl_context_parentnodes_has_tbl_context_tbl_context_id)
    REFERENCES tbl_context_parentnodes_has_tbl_context(tbl_context_contextCode, tbl_context_id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;
";*/

$tablename = 'tbl_context_parentnodes';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
	'tbl_context_parentnodes_has_tbl_context_tbl_context_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'userId' => array(
		'type' => 'text',
		'length' => 255
		),
    'dateCreated' => array(
		'type' => 'date'
		),
	'datemodified' => array(
		'type' => 'date'
		),
    'menu_text' => array(
		'type' => 'text',
		'length' => 255
		),
    'title' => array(
		'type' => 'text',
		'length' => 255
		),
    'updated' => array(
        'type' => 'timestamp'
        )
    );
    
$name = 'tbl_context_parentnodes_FKIndex1';

$indexes = array(
                'fields' => array(
                	'tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode' => array(), 
                    'tbl_context_parentnodes_has_tbl_context_tbl_context_id' => array()
                )
        );
?>