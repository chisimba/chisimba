<?
/*
  $sqldata[]="CREATE TABLE tbl_context (
  id VARCHAR(32) NOT NULL,
  contextCode VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  menutext VARCHAR(255) NULL,
  about TEXT,
  userid VARCHAR(255) NOT NULL,
  dateCreated DATE NULL,
  isClosed INT NULL,
  isActive INT NULL,
  isPublic INT NULL,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY(id, contextCode)
)
TYPE=InnoDB; COMMENT='Context Information';
";

$sqldata[]="ALTER TABLE `tbl_context` ADD INDEX `contextCode` ( `contextCode` ) ";
*/

$tablename = 'tbl_context';

$options = array('comment' => 'Context Information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'contextcode' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
	'title' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
    'menutext' => array(
		'type' => 'text',
		'length' => 255
		),
    'about' => array(
		'type' => 'text'
		),
    'userid' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
    'datecreated' => array(
		'type' => 'date'
		),    
    'status' => array(
        'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
        ),
    'access' => array(
        'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
        ),
    'lastupdatedby' => array(
        'type' => 'text',
		'length' => 32        
        ),
    'updated' => array(
        'type' => 'timestamp'
        )
    'startdate' => array(
		'type' => 'date'
		),
	'finishdate' => array(
		'type' => 'date'
		),   

    );

$name = 'contextcode';

$indexes = array(
                'fields' => array(
                	'contextCode' => array()
                )
        );
?>