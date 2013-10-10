<?php
// Table Name
$tablename = 'tbl_imagevault_meta_ifd0';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault metadata IFD0 section', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');


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
	'imagedescription' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'processingsoftware' => array(
        'type' => 'text',
        'length' => 100,
        ),   
    'subfiletype' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'oldsubfiletype' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'imagewidth' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'imageheight' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'bitspersample' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'compression' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'photometricinterpretation' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'thresholding' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'cellwidth' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'celllength' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'fillorder' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'documentname' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'imagedescription' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'make' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'model' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'orientation' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'samplesperpixel' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'rowsperstrip' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'minsamplevalue' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'maxsamplevalue' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'xresolution' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'yresolution' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'planarconfiguration' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'pagename' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'xposition' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'yposition' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'grayresponseunit' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'resolutionunit' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'pagenumber' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'transferfunction' => array(
        'type' => 'clob',
        ),
    'software' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'imgdatetime' => array(
        'type' => 'text',
        ),
    'artist' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'predictor' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'whitepoint' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'primarychromaticities' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'halftonehints' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'tilewidth' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'tilelength' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'inkset' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'dotrange' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'ycbcrcoefficients' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'ycbcrpositioning' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'referenceblackwhite' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'rating' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'ratingpercent' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'copyright' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'iptcnaa' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'seminfo' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'imagesourcedata' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'xptitle' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'xpcomment' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'xpauthor' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'xpkeywords' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'xpsubject' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'printim' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'dngversion' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'dngbackwardversion' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'uniquecameramodel' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'localizedcameramodel' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'colormatrix1' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'colormatrix2' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'cameracalibration1' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'cameracalibration2' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'reductionmatrix1' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'reductionmatrix2' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'analogbalance' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'asshotneutral' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'asshotwhitexy' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'baselineexposure' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'baselinenoise' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'baselinesharpness' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'linearresponselimit' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'cameraserialnumber' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'dnglensinfo' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'shadowscale' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'makernotesafety' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'calibrationilluminant1' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'calibrationilluminant2' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'rawdatauniqueid' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'originalrawfilename' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'originalrawfiledata' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'asshoticcprofile' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'asshotpreprofilematrix' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'currenticcprofile' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'currentpreprofilematrix' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'colorimetricreference' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'panasonictitle' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'panasonictitle2' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'cameracalibrationig' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profilecalibrationsig' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'asshotprofilename' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profilename' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profilehuesatmapdims' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profilehuesatmapdata1' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profilehuesatmapdata2' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profiletonecurve' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profileembedpolicy' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profilecopyright' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'forwardmatrix1' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'forwardmatrix2' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'previewapplicationname' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'previewapplicationversion' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'previewsettingsname' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'previewsettingsdigest' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'previewcolorspace' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'previewdatetime' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'rawimagedigest' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'originalrawfiledigest' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profilelooktabledims' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'profilelooktabledata' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'exifoffset' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'hostcomputer' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'exif_ifd_pointer' => array(
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
