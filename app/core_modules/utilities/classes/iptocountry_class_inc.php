<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

/**
* IP to Country - 
* This class gets a country's abbreviation code by providing the IP Address of a user
* It works  by splitting the IP and then including files containing arrays.
* Found at: http://www.phptutorial.info/iptocountry/the_script.html
*
* @copyright 2004, University of the Western Cape & AVOIR Project
* @category  Chisimba
* @author Tohir Solomons
* @package utilities
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
*/


 class iptocountry extends object{
 
     /**
     *@var object objConfig
     */
     var $objConfig;
     
     /**
    *Initialize method
    */
     function init(){
         $this->objConfig= $this->newObject('altconfig','config');
         // Load Countries Table
         $this->objCountries= $this->getObject('countries', 'utilities');
     }
    
    /**
    * Method to get the country abbreviation code by providing an IP Address
    *
    * @param string $ip: IP Address to check
    * @return $country string : Country Abbreviation Code or null
    */
    function getCountryByIP($ip) {   
       // IP address will be split into individual numbers and saved to an array($numbers) 
        $numbers = preg_split( "/\./", $ip);
        
        // A sort of validation. IP has to have four parts
        if (count($numbers) == 4) {
            
            // Generate Include File
            /*Based in the first number of the IP address ("101" in the example), a PHP file in ip_files/ directory will be included (in the example the file to be included will be "ip_files/101.php"). This file has known country codes for IP addresses starting with the selected first number (p.e: 101.###.###.###, where # is any digit). */
            $includefile = $this->objConfig->getsiteRootPath().'/core_modules/utilities/resources/ip_files/'.$numbers[0].'.php';
            
            //Include the File
            include($includefile);
            
            // IP address is transform into appropriate code
            $code=($numbers[0] * 16777216) + ($numbers[1] * 65536) + ($numbers[2] * 256) + ($numbers[3]);
            
            // Country is initially set to NULL or Unknown
            $country = NULL;
            
            //Data from "ip_files/101.php" is checked in order to find a range of codes which includes the code obtained by transforming our IP. 
            foreach($ranges as $key => $value){
                if($key<=$code){
                    if($ranges[$key][0]>=$code)
                        {$country=$ranges[$key][1];break;}
                    }
            }
            
            // Return the country
            return $country;
            
        } else {
            // Else if IP doesn't validate, return Null or Unknown
            return NULL;
        }
        
    } // end of function
    
    /**
    * Method to get the source for country flag by providing country code
    *
    * @param string $code: two letter country code
    * @return $flagsrc string : Flag Image File Url
    */
    function getCountryFlag($code)
    {
        
        $flagsrc = 'core_modules/utilities/resources/flags/'.strtolower($code).'.gif';

    if (!file_exists($this->objConfig->getsiteRootPath().'/'.$flagsrc)) { 
           $flagsrc = 'core_modules/utilities/resources/flags/unknown.gif';
        }

        return $flagsrc;
    }
    
    /**
    * Method to get the source for country flag by providing IP Address
    * Uses functions above to get IP then image src
    *
    * @param string $ip: IP code
    * @return string : Flag Image File Url
    */
    function getCountryFlagByIp($ip)
    {
        $code = $this->getCountryByIP($ip);
        
        return $this->getCountryFlag($code);
    }
    
    /**
    * Method to get the name of the country  by providing country code
    * Uses functions in objCountries
    *
    * @param string $code: two letter country code
    * @return string : Name of Country
    */
    function getCountryName ($code)
    {
        return $this->objCountries->getCountryName($code);
    }
    
    /**
    * Method to get the name of the country  by providing IP Address
    * Uses functions in objCountries
    *
    * @param string $ip: IP code
    * @return string : Name of Country
    */
    function getCountryNameByIp($ip)
    {
        $code = $this->getCountryByIP($ip);
        
        return $this->objCountries->getCountryName($code);
    }
     
 } // end of class

?>