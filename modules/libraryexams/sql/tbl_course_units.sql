<?PHP
$tablename = 'tbl_course_units';
$options = array('comment' => 'Course Units', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'content_id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'option_id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'created' => array(
		'type' => 'timestamp',
		),
	'created_by' => array(
		'type' => 'text',
		'length' => 255
		),
    'ip_addr' => array(
        'type' => 'text',
        'length' => 255
        )
	);

?>
