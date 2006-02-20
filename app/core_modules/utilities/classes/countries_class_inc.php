<?php
/* -------------------- countries class ----------------*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Class for the country codes table in the database
*/
class countries extends dbTable {

	/**
	* Constructor method to define the table
	*/
	function init() {
		parent::init('tbl_country');
	}
    
    /**
    * Method to get a country's details by providing the two letter country code
    *
    * @param string $code: Two letter country code
    * @return array : details of the country
    */
    function getCountryDetails($code)
    {
        return $this->getRow('iso', $code);
    }
    
    /**
    * Method to get a country's name by providing the two letter country code
    *
    * @param string $code: Two letter country code
    * @return string : name of the country
    */
    function getCountryName($code)
    {
        $country =& $this->getCountryDetails($code);
        
        return $country['printable_name'];
    }
    
    
	
}  #end of class
?>
