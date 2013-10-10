<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Data access (db model) Class for the geonames module
 *
 * This is a database model class for the geonames module. All database transaactions will go through
 * this class. This class is derived from the top level dbTable superclass in the framework core.
 *
 * @author Paul Scott, Tohir Solomons
 * @filesource
 * @copyright AVOIR
 * @package geonames
 * @category chisimba
 * @access public
 */

class dbgeonames extends dbTable
{

	/**
	 * Standard init function - Class Constructor
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$this->objLanguage = $this->getObject("language", "language");
		parent::init('tbl_geonames');
	}
	
    /**
    * Method to add a record
    * @param array $insarr Array with record fields
    * @return string Last Insert Id
    */
	public function insertRecord($insarr)
	{
		return $this->insert($insarr, 'tbl_geonames');
	}
	
    /**
    * Method to get All Records from the Database
    * @return array
    */
	public function grabAllRecords()
	{
		return $this->getAll();
	}
    
    /**
    * Method to get a record matching given name
    * Uses LIKE to overcome case sensitivity
    * @param string $location Name of Location
    * @return array
    */
    public function getLocation($location)
    {
        return $this->getAll('WHERE name LIKE \''.$location.'\' ');
    }
	
	/**
    * Method to get a list of cities starting with a name
    * @param string $location Name of Location
    * @return array
    */
    public function getLocationsStartingWith($location)
    {
        return $this->getAll('WHERE name LIKE \''.$location.'%\' AND name NOT LIKE \''.$location.'\' GROUP BY name');
    }
    
    /**
    * Method to add a record from an XML Node derived from the Geonames Webservice
    * @param object $geoname XML Node Object
    * @return string Last Insert Id
    */
    public function insertFromXML(&$geoname)
    {
        $insarr = array(
            'geonameid' => $geoname->geonameId,
            'name' => $geoname->name,
            'alternatenames' => $geoname->alternateNames,
            'latitude' => $geoname->lat,
            'longitude' => $geoname->lng,
            'featureclass' => $geoname->fcl,
            'featurecode' => $geoname->fcode,
            'countrycode' => $geoname->countryCode,
            'admin1code' => $geoname->adminCode1,
            'admin2code' => $geoname->adminCode2,
            'admin1name' => $geoname->adminName1,
            'admin2name' => $geoname->adminName2,
            'population' => $geoname->population,
            'elevation' => $geoname->elevation,
            'timezoneid' => $geoname->timezone,
        );
        
        return $this->insertRecord($insarr);
        
    }
    
    public function insertResource($insarr) {
        parent::init('tbl_geo_resources');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertGeoname($insarr) {
        parent::init('tbl_geo_geoname');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertAdmin1Code($insarr) {
        parent::init('tbl_geo_admin1codes');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertAdmin1AsciiCode($insarr) {
        parent::init('tbl_geo_admin1codesascii');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertAdmin2Code($insarr) {
        parent::init('tbl_geo_admin2codes');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
 
    public function insertAltName($insarr) {
        parent::init('tbl_geo_alternatename');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertCountryInfo($insarr) {
        parent::init('tbl_geo_countryinfo');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertFeatureCodeInfo($insarr) {
        parent::init('tbl_geo_featurecodes');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertIsoLangCodeInfo($insarr) {
        parent::init('tbl_geo_iso_languagecodes');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertTimeZoneInfo($insarr) {
        parent::init('tbl_geo_timezones');
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
    
    public function insertUserTagsInfo($insarr) {
        parent::init('tbl_geo_usertags');
        $insarr['moddate'] = $this->now();
        $this->insert($insarr);
        parent::init('tbl_geonames');
    }
}
?>
