<?

$tablename = 'tbl_files_folders';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'folderpath' => array(
		'type' => 'text',
        'notnull' => TRUE
		),
	'folderlevel' => array(
		'type' => 'integer',
		'length' => 11,
        'notnull' => TRUE
		),
    'folderstatus' => array(
		'type' => 'text',
        'length' => 10,
        'default' => 'private',
        'notnull' => TRUE
		),
    'folderpassword' => array(
		'type' => 'text',
        'length' => 255
		)
    );
//create other indexes here...

$name = 'index_tbl_files_folders';

$indexes = array(
                'fields' => array(
                	'folderlevel' => array(),
                	'folderstatus' => array()
                )
        );

?>