<?PHP
$tablename = 'tbl_cms_flag_options';
$options = array('comment' => 'Flag options', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'title' => array(
		'type' => 'text',
		'length' => 255
		),
	'text' => array(
		'type' => 'text',
		'length' => 255
		),
    'published' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
	'created' => array(
		'type' => 'timestamp',
		),
	'created_by' => array(
		'type' => 'text',
		'length' => 255
		)
	);

$name = 'idx_cms_flag_options';

$indexes = array(
                'fields' => array(
                	'title' => array()
                )
        );
?>
