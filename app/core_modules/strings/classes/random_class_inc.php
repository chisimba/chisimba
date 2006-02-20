<?php
/**
* This class provides some methods for generating 
* random strings such as GUIDS and passwords
*/

class random extends object {
    /**
    * Standard constructor for KEWL.NextGen, instantiates
    * the user object
    */
    function init()
    {
        $this->objUser = &$this->getObject("user", "security");
    } 

    /**
    * Method to return a GUID made up of the 
    * name of the server, the current time in Unix timestamp, the
    * userId of the current user, and a random $chars byte string.
    * 
    * @param int $chars: the number of characters for the random string
    * @uses _randomString()
    * 
    */
    function guid($chars=30)
    {
        $server = $_SERVER['SERVER_NAME'];
        $nixTime = time();
        $randompart = $this->_randomString($chars);
        return $server . "_" . $this->objUser->userId() . "_" . $nixTime . "_" . $randompart;
    }
    
    /**
    * Method to return a GUID made up of the 
    * name of the current time in Unix timestamp and a random $chars 
    * byte string.
    * 
    * @param int $chars: the number of characters for the random string
    * @uses _randomString()
    * 
    */
    function simpleGuid($chars=10)
    {
        $nixTime = time();
        $randompart = $this->_randomString($chars);
        return $nixTime . "_" . $randompart;
    }
    
    
    /**
    * Method to return a MD5 hashed guid
    */
    function md5Guid($chars=20)
    {
        return md5($this->guid());
    }
    
    /**
    * Method to get userId from guid
    */
    function getUserIdFromGuid($guid)
    {
        $gArray=explode("_", $guid);
        $ret = $gArray[1];
        return $ret;
    }
    
    /**
    * Method to get User fullname from guid
    */
    function getFullNameFromGuid($guid)
    {
        return $this->objUser->fullName($this->getUserIdFromGuid($guid));
    }
    

    /*------------------------- PRIVATE METHODS BELOW LINE -----------------------*/

    /**
    * Method to generate a random string suitable for use as a mirroring
    * GUID.
    * 
    * @param int $length: The length of the string to return, defaults to 8
    * characters
    */
    function _randomString($len=8)
    {
       $newstring="";
       if($len>0)
       {
           while(strlen($newstring)<$len)
           {
               $randnum = mt_rand(0,61);
               if ($randnum < 10)
                   {$newstring.=chr($randnum+48);}
               elseif ($randnum < 36)
                   {$newstring.=chr($randnum+55);}
               else
                   {$newstring.=chr($randnum+61);}
           }
       }
       return $newstring;
    }
} // end class
?>
