<?PHP
$tablename = 'tbl_cms_flag_email';
$options = array('comment' => 'Flag Email Alerts', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'name' => array(
		'type' => 'text',
		'length' => 255
		),
	'email' => array(
		'type' => 'text',
		'length' => 255
		),
    'user_id' => array(
		'type' => 'text',
        'length' => 25,
        'default' => 'NULL'
		),
    'content_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'section_id' => array(
        'type' => 'text',
        'length' => 32,
        )
	);

?>
