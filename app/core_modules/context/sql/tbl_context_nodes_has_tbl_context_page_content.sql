<?
/*
  $sqldata[]="CREATE TABLE tbl_context_nodes_has_tbl_context_page_content (
  id VARCHAR(32) NOT NULL,
  tbl_context_nodes_tbl_context_parentnodes_id VARCHAR(32) NOT NULL,
  tbl_context_nodes_id VARCHAR(32) NOT NULL,
  tbl_context_page_content_id VARCHAR(32) NOT NULL, 
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(tbl_context_nodes_tbl_context_parentnodes_id, tbl_context_nodes_id, tbl_context_page_content_id),
  INDEX tbl_context_nodes_has_tbl_context_page_content_FKIndex1(tbl_context_nodes_id, tbl_context_nodes_tbl_context_parentnodes_id),
  INDEX tbl_context_nodes_has_tbl_context_page_content_FKIndex2(tbl_context_page_content_id),
  FOREIGN KEY(tbl_context_nodes_id, tbl_context_nodes_tbl_context_parentnodes_id)
    REFERENCES tbl_context_nodes(id, tbl_context_parentnodes_id)
     ON DELETE CASCADE
      ON UPDATE CASCADE,
  FOREIGN KEY(tbl_context_page_content_id)
    REFERENCES tbl_context_page_content(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;
";*/

$tablename = 'tbl_context_nodes_has_tbl_context_page_content';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'tbl_context_nodes_tbl_context_parentnodes_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'tbl_context_nodes_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'tbl_context_page_content_id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'updated' => array(
        'type' => 'timestamp'
        )
    );
    
$name = 'tbl_context_nodes_FKIndex1';

$indexes = array(
                'fields' => array(
                	'tbl_context_nodes_tbl_context_parentnodes_id' => array(), 
                    'tbl_context_nodes_id' => array(), 
                    'tbl_context_page_content_id' => array(), 
                )
        );
?>