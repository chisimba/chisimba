<?
/*
  $sqldata[]="CREATE TABLE `tbl_context_temp` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` varchar(32) default NULL,
  `filetype` varchar(255) default NULL,
  `name` varchar(255) default NULL,  
  `size` int(255) default NULL,
  `filedata` longblob,
  updated TIMESTAMP ( 14 ) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB AUTO_INCREMENT=1 ;
";*/

$tablename = 'tbl_context_temp';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'parent_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'filetype' => array(
		'type' => 'text',
		'length' => 255
		),
    'name' => array(
		'type' => 'text',
		'length' => 255
		),
    'size' => array(
		'type' => 'text',
		'length' => 255
		),
    'filedata' => array(
		'type' => 'text' // BLOB?
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