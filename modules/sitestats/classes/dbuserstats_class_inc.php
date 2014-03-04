<?php
/* -------------------- stories class ----------------*/

/**
* Class for the stories table in the database
*/
class dbuserstats extends dbTable
{

    var $objUser;
    var $objLanguage;

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_users');
    }
    
    
    /**
    * 
    * Method to get the total number of countries
    * represented by all the users on the system
    * 
    */
    function getTotalCountries()
    {
        $sql="SELECT COUNT(DISTINCT(country)) 
          AS totalcountries
          FROM tbl_users";
        $ar = $this->getArray($sql);
        return $ar[0]['totalcountries'];
    }
    
    /**
    * 
    * Method to get an array of countries
    * 
    */
    function getCountries()
    {
        $sql="SELECT DISTINCT(country) 
          AS countryCode
          FROM tbl_users";
        return $this->getArray($sql);
    }
    
    /**
    * 
    * Method to return the image of the flag for a country
    * 
    */
    function getFlags()
    {
        $ar = $this->getCountries();
        $objFlags=$this->getObject('iptocountry', 'utilities');
        $str = '&nbsp;&nbsp;&nbsp;';
        foreach ($ar as $line) {
            $str .= " <img src=\""
              . $objFlags->getCountryFlag($line['countrycode'])
              . "\" alt=\"" . $line['countrycode'] . "\" /> ";
        }
        return $str;
    }
    
    /**
    * 
    * Method to return the total file space used
    * 
    */
    function countUsers()
    {
        $sql="SELECT COUNT(userId)
          AS users
          FROM tbl_users";
        $ar = $this->getArray($sql);
        return $ar[0]['users'];
    }
    
    /**
    * 
    * Method to get female count
    * 
    */
    function countFemales()
    {
        $sql="SELECT COUNT(id) 
          AS females 
          FROM tbl_users WHERE sex='F'";
        $ar = $this->getArray($sql);
        return $ar[0]['females'];
    }

    /**
    * 
    * Method to get female count
    * 
    */
    function countMales()
    {
        $sql="SELECT COUNT(id) 
          AS males 
          FROM tbl_users WHERE sex='M'";
        $ar = $this->getArray($sql);
        return $ar[0]['males'];
    }
    
    /**
    * 
    * Method to return a string list of how the users 
    * were created
    * 
    */
    function getHowCreated()
    {
        $sql="SELECT COUNT(howCreated) 
          AS count, howCreated
          FROM tbl_users 
          GROUP BY howCreated";
        return $this->getArray($sql);
    }
    
}  #end of class
?>
