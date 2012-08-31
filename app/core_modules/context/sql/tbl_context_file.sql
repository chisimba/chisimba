<?php
/*
  $sqldata[]="CREATE TABLE tbl_context_file (
  id VARCHAR(32) NOT NULL,
  tbl_context_parentnodes_id VARCHAR(32) NOT NULL,
  datatype VARCHAR(60) NULL,
  title VARCHAR(120) NULL,
  description VARCHAR(255) NULL,
  version VARCHAR(60) NULL,
  name VARCHAR(120) NULL,
  size BIGINT(20) NULL,
  filedate DATETIME NULL,
  path VARCHAR(255) NULL,
  category varchar(32) default NULL,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id, tbl_context_parentnodes_id),
  INDEX tbl_context_file_FKIndex1(tbl_context_parentnodes_id),
  FOREIGN KEY(tbl_context_parentnodes_id)
    REFERENCES tbl_context_parentnodes(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
TYPE=InnoDB;
";*/

$tablename = 'tbl_context_file';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'tbl_context_parentnodes_id' => array(
        'type' => 'text',
        'length' => 64
        ),
    'datatype' => array(
        'type' => 'text',
        'length' => 60
        ),
    'title' => array(
        'type' => 'text',
        'length' => 120
        ),
    'description' => array(
        'type' => 'text',
        'length' => 255
        ),
    'version' => array(
        'type' => 'text',
        'length' => 60
        ),
    'name' => array(
        'type' => 'text',
        'length' => 120
        ),
    'size' => array(
        'type' => 'integer'
        ),
    'filedate' => array(
        'type' => 'date'
        ),
    'path' => array(
        'type' => 'text',
        'length' => 255
        ),
    'title' => array(
        'type' => 'text',
        'length' => 120
        ),
    'category' => array(
        'type' => 'text',
        'length' => 32
        ),
    'updated' => array(
        'type' => 'timestamp'
        )
    );

// Other Indexes

$name = 'attachment_id';

$indexes = array(
                'fields' => array(
                    'tbl_context_parentnodes_id' => array()
                )
        );



?>