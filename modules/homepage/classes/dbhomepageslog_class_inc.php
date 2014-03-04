<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


/**
* Model class for the table tbl_homepages
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbHomePagesLog extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init() 
    {
        parent::init('tbl_homepages_log');
        $this->objIPToCountry =& $this->newObject('iptocountry', 'utilities');
        //$this->objCountries =& $this->newObject('countries', 'utilities');
    }

    /**
    * NOT USED!
    * Return all records
    * @param string $homepageId The homepage ID
    * @param integer $dow The day of the week
    * @return array The count of accesses to a homepage for a given d.o.w.
    */
    public function listAll($homepageId,$dow)
    {
        $sql = "SELECT 
			count(id) as theCount 
		FROM tbl_homepages_log
        WHERE (homepageid='$homepageId')
        AND (dow='$dow') 
		GROUP BY id";
        return $this->getArray($sql);
    }
    
    /**
    * NOT USED!
    * Return the number of visits to the homepage for the previous months
    * @param string $homepageuserId The userId of the hompage
    * @return $count_of_months The count of accesses to a homepage for previus month
    */
    public function countlastmonthvisits($userId)
    {
        $sql = "SELECT id from tbl_homepages WHERE userid = '$userId'";
        $homepageId = $this->getArray($sql);	
        if (count($homepageId) > 0) {
	        $sql = "SELECT 
				count(id) 
			FROM tbl_homepages_log 
			WHERE (DATE_SUB(CURDATE(),INTERVAL 30 DAY) <=  timestamp) 
			AND (homepageid ='".$homepageId[0]['id']."')";
	        $row = $this->getArray($sql);
	        return $row[0]; 
        } 
		else {
            return 0;
        }
    }

    /**
    * NOT USED!
    * Collect all ip's from the Database, then converts it to a list of 
    * country names and is returned. Uses convertIptoCountries method
    * @return array The array of all ips that have vissited the site.
    */
    public function ConvertIps()
    {
        $sql = "SELECT ip FROM tbl_homepages_log ";
        return $this->convertIpToCountries($this->getArray($sql));		
    }
    
     /**
    * Collect Ip's and converts them into Country names
    * so it can be used
    * @param array $array Array of ips
    * @return array The array coutnry names and the visistors from these countries.
    */
    
    public function convertIpToCountries($array)
    {  
        $countries = array();
        $size = count($array);
        foreach ($array as $ip)
        {	  	 
            if (!is_null($ip['ip'])) {
                $code = $this->objIPToCountry->getCountryByIP($ip['ip']);
                if ($code == NULL) {
                    array_push($countries, 'unknown');
                } 
				else {
                    array_push($countries, $code);
                }
            } 
			else {
                array_push($countries, 'unknown');
            }
        }
        // Create a new array with countries
        $cleaned = array_count_values($countries);
        return $cleaned;
    }
            
    /**
    * Insert a record
    * @param string $homepageId The homepage ID
    * @param integer $dow The day of the week
    * @param integer $ip The IP address
    * @param datetime $timestamp The timestamp
    */
    public function insertSingle($homepageId, $dow, $ip, $timestamp)
    {
        $this->insert(array(
            'homepageid' => $homepageId,
            'dow' => $dow,
            'ip' => $ip,
            'timestamp' => strftime('%Y-%m-%d %H:%M:%S',$timestamp)
        ));
        return;	
    }
    
    public function getUniqueVisitors($homePageId)
    {
        $sql = 'SELECT count(DISTINCT ip) as theCount FROM tbl_homepages_log WHERE  homepageid="'.$homePageId.'"';
        $results = $this->getArray($sql);
        return $results[0]['thecount'];
    }
    
    public function getHits($homePageId)
    {
        $sql = 'SELECT count(ip) as theCount FROM tbl_homepages_log WHERE  homepageid="'.$homePageId.'"';
        $results = $this->getArray($sql);
        return $results[0]['thecount'];
    }
    
    public function getCountries($homePageId)
    {
        $sql = 'SELECT ip FROM tbl_homepages_log WHERE  homepageid="'.$homePageId.'"';
        $results = $this->getArray($sql);
        return $this->convertIpToCountries($results);
    }
    
    public function getCountriesFlags($homePageId)
    {
        $countries = $this->getCountries($homePageId);
        $return = NULL;
        $comma = '';
        foreach ($countries as $country=>$value)
        {
            if ($country != 'unknown') {
                $countryName = $this->objIPToCountry->getCountryName($country);
                $image = '<img src="'.$this->objIPToCountry->getCountryFlag($country).'" alt="'.$countryName.'" title="'.$countryName.'" />';
                $return .= $comma.$image.' '.$countryName.' ('.$value.') ';
                $comma = ', ';
            }
        }
        return $return;
    }
}
?>
