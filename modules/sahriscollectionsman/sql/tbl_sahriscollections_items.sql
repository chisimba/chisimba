<?php
// Table Name
$tablename = 'tbl_sahriscollections_items';

//Options line for comments, encoding and character set
$options = array('comment' => 'SAHRIS collection items', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
    'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'sitename' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'siteabbr' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'sitemanager' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'siteid' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'collectionname' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'objname' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'objtype' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'accno' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'acqmethod' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'acqdate' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'acqsrc' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'origmedia' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'commname' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'localname' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'classname' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'catbyform' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'catbytech' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'material' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'technique' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'dimensions' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'normalloc' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'currloc' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'reason' => array(
	    'type' => 'clob',
	    ),
	'remover' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'physdesc' => array(
	    'type' => 'clob',
	    ),
	'distfeat' => array(
	    'type' => 'clob',
	    ),
	'currcond' => array(
	    'type' => 'clob',
	    ),
	'conservemeth' => array(
	    'type' => 'clob',
	    ),
	'conservedate' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'conservator' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'histcomments' => array(
	    'type' => 'clob',
	    ),
	'maker' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'prodplace' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'prodperiod' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'histuser' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'placeofuse' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'periodofuse' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'provenance' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'collector' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'collectdate' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'collmethod' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'collnumber' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'pubref' => array(
	    'type' => 'clob',
	    ),
	'gensite' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'media64' => array(
	    'type' => 'clob',
	    ),
	'filename' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'username' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'media' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'datecreated' => array(
	    'type' => 'timestamp',
	    ),
	'collectionid' => array(
	    'type' => 'text',
	    'length' => 255,
	    ),
	'obj_ts' => array(
	    'type' => 'integer',
	    'length' => 50,
	    ),
	);

//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                )
        );
?>
