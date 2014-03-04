<?php
// Table Name
$tablename = 'tbl_imagevault_meta_exif';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault exif metadata', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'applicationnotes' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'exposuretime' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'fnumber' => array(
        'type' => 'text', 
        'length' => 100,
        ),
	'exposureprogram' => array(
		'type' => 'text',
		'length' => 100,
		),
	'spectralsensitivity' => array(
		'type' => 'text',
		'length' => 100,
		),
	'iso' => array(
		'type' => 'text',
		'length' => 100,
		),
	'isospeedratings' => array(
		'type' => 'text',
		'length' => 100,
		),
	'photographicsensitivity' => array(
		'type' => 'text',
		'length' => 100,
		),
	'timezoneoffset' => array(
		'type' => 'text',
		'length' => 100,
		),
	'selftimermode' => array(
		'type' => 'text',
		'length' => 100,
		),
    'sensitivitytype' => array(
        'type' => 'text',
		'length' => 100,
       ),
    'standardoutputsensitivity' => array(
		'type' => 'text',
		'length' => 100,
		),
	'recommendedexposureindex' => array(
		'type' => 'text',
		'length' => 100,
		),
	'isospeed' => array(
		'type' => 'text',
		'length' => 100,
		),
    'isospeedlatitudeyyy' => array(
		'type' => 'text',
		'length' => 100,
		),
	'isospeedlatitudezzz' => array(
		'type' => 'text',
		'length' => 100,
		),
	'exifversion' => array(
		'type' => 'text',
		'length' => 100,
		),
	'datetimeoriginal' => array(
		'type' => 'text',
		'length' => 100,
		),
	'createdate' => array(
		'type' => 'text',
		'length' => 100,
		),
	'componentsconfiguration' => array(
		'type' => 'text',
		'length' => 100,
		),
	'compressedbitsperpixel' => array(
		'type' => 'text',
		'length' => 100,
		),
	'shutterspeedvalue' => array(
		'type' => 'text',
		'length' => 100,
		),
	'aperturevalue' => array(
		'type' => 'text',
		'length' => 100,
		),
	'brightnesvalue' => array(
		'type' => 'text',
		'length' => 100,
		),
	'exposurecompensation' => array(
		'type' => 'text',
		'length' => 100,
		),
	'exposurebiasvalue' => array(
		'type' => 'text',
		'length' => 100,
		),
	'maxaperturevalue' => array(
		'type' => 'text',
		'length' => 100,
		),
	'subjectdistance' => array(
		'type' => 'text',
		'length' => 100,
		),
	'meteringmode' => array(
		'type' => 'text',
		'length' => 100,
		),
	'lightsource' => array(
		'type' => 'text',
		'length' => 100,
		),
	'flash' => array(
		'type' => 'text',
		'length' => 100,
		),
	'focallength' => array(
		'type' => 'text',
		'length' => 100,
		),
	'imagenumber' => array(
		'type' => 'text',
		'length' => 100,
		),
	'securityclassification' => array(
		'type' => 'text',
		'length' => 100,
		),
	'imagehistory' => array(
		'type' => 'text',
		'length' => 100,
		),
	'subjectarea' => array(
		'type' => 'text',
		'length' => 100,
		),
	'sensingmethod' => array(
		'type' => 'text',
		'length' => 100,
		),
	'usercomment' => array(
		'type' => 'text',
		'length' => 100,
		),
	'subsectime' => array(
		'type' => 'text',
		'length' => 100,
		),
	'subsectimeoriginal' => array(
		'type' => 'text',
		'length' => 100,
		),
	'subsectimedigitized' => array(
		'type' => 'text',
		'length' => 100,
		),
	'flashpixversion' => array(
		'type' => 'text',
		'length' => 100,
		),
	'colorspace' => array(
		'type' => 'text',
		'length' => 100,
		),
	'pixelxdimension' => array(
		'type' => 'text',
		'length' => 100,
		),
	'pixelydimension' => array(
		'type' => 'text',
		'length' => 100,
		),
	'exifimagewidth' => array(
		'type' => 'text',
		'length' => 100,
		),
	'exifimageheight' => array(
		'type' => 'text',
		'length' => 100,
		),
	'relatedsoundfile' => array(
		'type' => 'text',
		'length' => 100,
		),
	'flashenergy' => array(
		'type' => 'text',
		'length' => 100,
		),
	'focalplanexresolution' => array(
		'type' => 'text',
		'length' => 100,
		),
	'focalplaneyresolution' => array(
		'type' => 'text',
		'length' => 100,
		),
	'focalplaneresolutionunit' => array(
		'type' => 'text',
		'length' => 100,
		),
	'subjectlocation' => array(
		'type' => 'text',
		'length' => 100,
		),
	'exposureindex' => array(
		'type' => 'text',
		'length' => 100,
		),
	'filesource' => array(
		'type' => 'text',
		'length' => 100,
		),
	'scenetype' => array(
		'type' => 'text',
		'length' => 100,
		),
	'cfapattern' => array(
		'type' => 'text',
		'length' => 100,
		),
	'customrendered' => array(
		'type' => 'text',
		'length' => 100,
		),
	'exposuremode' => array(
		'type' => 'text',
		'length' => 100,
		),
	'whitebalance' => array(
		'type' => 'text',
		'length' => 100,
		),
	'digitalzoomratio' => array(
		'type' => 'text',
		'length' => 100,
		),
	'focallengthin35mmformat' => array(
		'type' => 'text',
		'length' => 100,
		),
	'scenecapturetype' => array(
		'type' => 'text',
		'length' => 100,
		),
	'gaincontrol' => array(
		'type' => 'text',
		'length' => 100,
		),
	'contrast' => array(
		'type' => 'text',
		'length' => 100,
		),
	'saturation' => array(
		'type' => 'text',
		'length' => 100,
		),
	'sharpness' => array(
		'type' => 'text',
		'length' => 100,
		),
	'subjectdistancerange' => array(
		'type' => 'text',
		'length' => 100,
		),
	'imageuniqueid' => array(
		'type' => 'text',
		'length' => 100,
		),
	'ownername' => array(
		'type' => 'text',
		'length' => 100,
		),
	'cameraownername' => array(
		'type' => 'text',
		'length' => 100,
		),
	'bodyserialnumber' => array(
		'type' => 'text',
		'length' => 100,
		),
	'serialnumber' => array(
		'type' => 'text',
		'length' => 100,
		),
	'lensinfo' => array(
		'type' => 'text',
		'length' => 100,
		),
	'lensmake' => array(
		'type' => 'text',
		'length' => 100,
		),
	'lensmodel' => array(
		'type' => 'text',
		'length' => 100,
		),
	'lensserialnumber' => array(
		'type' => 'text',
		'length' => 100,
		),
	'gamma' => array(
		'type' => 'text',
		'length' => 100,
		),
	'padding' => array(
		'type' => 'text',
		'length' => 100,
		),
	'offsetschema' => array(
		'type' => 'text',
		'length' => 100,
		),
	'lens' => array(
		'type' => 'text',
		'length' => 100,
		),
	'rawfile' => array(
		'type' => 'text',
		'length' => 100,
		),
	'converter' => array(
		'type' => 'text',
		'length' => 100,
		),
	'exposure' => array(
		'type' => 'text',
		'length' => 100,
		),
	'shadows' => array(
		'type' => 'text',
		'length' => 100,
		),
	'brightness' => array(
		'type' => 'text',
		'length' => 100,
		),
	'smoothness' => array(
		'type' => 'text',
		'length' => 100,
		),
	'moirefilter' => array(
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
