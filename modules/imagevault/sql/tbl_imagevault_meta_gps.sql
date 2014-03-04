<?php
// Table Name
$tablename = 'tbl_imagevault_meta_gps';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault metadata GPS section', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'imageid' => array(
		'type' => 'text',
		'length' => 32
		),	
    'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
    'gpslatitude' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'latitudetranslated' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpslatituderef' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpslongitude' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'longitudetranslated' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpslongituderef' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsaltitude' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'altitudetranslated' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsaltituderef' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpstimestamp' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'timestamptranslated' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsimgdirectionref' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsimgdirection' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpssatellites' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsstatus' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsmeasuremode' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdop' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsspeedref' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsspeed' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpstrackref' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpstrack' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsmapdatum' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdestlatituderef' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdestlatitude' => array(
        'type' => 'text',
        'type' => 'text',
        'length' => 100,
        ),
    'destlatitudetranslated' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdestlongituderef' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdestlongitude' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'destlongitudetranslated' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdestbearingref' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdestbearing' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdestdistanceref' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdestdistance' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsprocessingmethod' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsareainformation' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdatestamp' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpsdifferential' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'gpshpositioningerror' => array(
        'type' => 'text',
        'length' => 100,
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
