<?php
/*
Set the table name
*/
$tablename = 'tbl_travel_hotels';


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for hotel details', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'integer',
		'length' => 10,
		'notnull' => 1
		),
	'name' => array(
		'type' => 'text',
		'length' => 64
		), 
	'airportcode' => array(
		'type' => 'text',
		'length' => 5
		), 
	'address1' => array(
		'type' => 'text',
		'length' => 64
		), 
	'address2' => array(
		'type' => 'text',
		'length' => 64
		), 
	'address3' => array(
		'type' => 'text',
		'length' => 64
		), 
	'city' => array(
		'type' => 'text',
		'length' => 64
		), 
	'stateprovince' => array(
		'type' => 'text',
		'length' => 64
		), 
	'country' => array(
		'type' => 'text',
		'length' => 64
		), 
	'postalcode' => array(
		'type' => 'text',
		'length' => 16
		), 
	'longitude' => array(
		'type' => 'float'
		), 
	'latitude' => array(
		'type' => 'float'
		), 
	'lowrate' => array(
		'type' => 'float'
		), 
	'highrate' => array(
		'type' => 'float'
		), 
	'marketinglevel' => array(
		'type' => 'integer',
		'length' => 5
		), 
	'confidence' => array(
		'type' => 'integer',
		'length' => 3
		), 
	'hotelmodified' => array(
		'type' => 'timestamp'
		), 
	'propertytype' => array(
		'type' => 'text',
		'length' => 16
		), 
	'timezone' => array(
		'type' => 'text',
		'length' => 32
		), 
	'gmtoffset' => array(
		'type' => 'text',
		'length' => 32
		), 
	'yearpropertyopened' => array(
		'type' => 'text',
		'length' => 64
		), 
	'yearpropertyrenovated' => array(
		'type' => 'text',
		'length' => 64
		), 
	'nativecurrency' => array(
		'type' => 'text',
		'length' => 16
		), 
	'numberofrooms' => array(
		'type' => 'integer',
		'length' => 10
		), 
	'numberofsuites' => array(
		'type' => 'integer',
		'length' => 10
		), 
	'numberoffloors' => array(
		'type' => 'integer',
		'length' => 10
		), 
	'checkintime' => array(
		'type' => 'text',
		'length' => 16
		), 
	'checkouttime' => array(
		'type' => 'text',
		'length' => 16
		), 
	'hasvaletparking' => array(
		'type' => 'text',
		'length' => 1
		), 
	'hascontinentalbreakfast' => array(
		'type' => 'text',
		'length' => 1
		), 
	'hasinroommovies' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hassauna' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'haswhirlpool' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasvoicemail' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'has24hoursecurity' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasparkinggarage' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'haselectronicroomkeys' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hascoffeeteamaker' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hassafe' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasvideocheckout' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasrestrictedaccess' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasinteriorroomaccess' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasexteriorroomaccess' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hascombination' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasfitnessfacility' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasgameroom' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hastenniscourt' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasgolfcourse' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasinhousedining' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasinhousebar' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hashandicapaccessible' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'haschildrenallowed' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'haspetsallowed' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hastvinroom' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasdataports' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasmeetingrooms' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasbusinesscentre' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasdrycleaning' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasindoorpool' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasoutdoorpool' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasnonsmokingrooms' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasairporttransportation' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasairconditioning' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasclothingiron' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'haswakeupservice' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasminibarinroom' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasroomservice' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hashairdryer' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hascarrentdesk' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasfamilyrooms' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'haskitchen' => array(
	    'type' => 'text',
		'length' => 1
		), 
	'hasmap' => array(
		'type' => 'text',
		'length' => 5
		), 
	'propertydescription' => array(
		'type' => 'clob'
		), 
	'gdschaincode' => array(
		'type' => 'text',
		'length' => 5,
		), 
	'gdschaincodename' => array(
		'type' => 'text',
		'length' => 64
		),
	'destinationid' => array(
		'type' => 'text',
		'length' => 64
		), 
	'drivingdirections' => array(
		'type' => 'clob'
		), 
	'nearbyattractions' => array(
		'type' => 'clob'
		),  
	'created' => array(
		'type' => 'timestamp',
		'notnull' => 1,
		),
	'modified' => array(
		'type' => 'timestamp'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32
		)
);

?>