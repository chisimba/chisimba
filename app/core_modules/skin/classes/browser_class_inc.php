<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

// end security check

/**
 * Class to detect which browser is currently accessing the page/site
 * @author Paul Scott, Ryan Whitney
 * This class is very loosely based on scripts by Gary White
 * @copyright Paul Scott
 * @package browser
 */
class browser extends object
{
    /**
     * @private string $version
     */
    private $version = NULL;
    
    /**
     * @private $useragent
     */
    private $useragent = NULL;
    
    /**
     * @private string $platform
     */
    private $platform = NULL;
    
    /**
     * @private string aol
     */
    private $aol = FALSE;
    
    /**
     * @private string browser
     */
    private $browsertype;
    
    /**
     * Class constructor
     * @param void
     * @return void
     */
    public function init()
    {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        //set the useragent property
        $this->useragent = $agent;

        // Set information on the browser
        //First, the OS
        $this->_determinePlatform();

        //Now to figure out the browsertype and version
        //We iterate down using the most popular, finally stopping once we've found it
        // Yeah, its funky but it works
        if(!$this->isFirefox())
        if(!$this->isMSIE())
        if(!$this->isSafari())
        if(!$this->isAOL())
        if(!$this->isOpera())
        if(!$this->isKonqueror())
        if(!$this->isIEv1())
        if(!$this->isGaleon())
        if(!$this->isMSPIE())
        if(!$this->isIcab())
        if(!$this->isOmniWeb())
        if(!$this->isNetPositive())
        if(!$this->isPhoenix())
        if(!$this->isFirebird())
            $this->isLynx(); // The last function, don't need to run as an if
        //$this->isMozAlphaBeta();
        //$this->isMozStable();
        //$this->isNetscape();
    }
    
    /**
     * method that determines the platform and sets it
     * @param void
     * @return void 
     */
    private function _determinePlatform()
    {
        $win = eregi("win", $this->useragent);
        $linux = eregi("linux", $this->useragent);
        $mac = eregi("mac", $this->useragent);
        $os2 = eregi("OS/2", $this->useragent);
        $beos = eregi("BeOS", $this->useragent);
        
        //now do the check as to which matches and return it
        if($win)
        {
            $this->platform = "Windows";
        }
        elseif ($linux)
        {
            $this->platform = "Linux"; 
        }
        elseif ($mac)
        {
            $this->platform = "Macintosh"; 
        }
        elseif ($os2)
        {
            $this->platform = "OS/2"; 
        }
        elseif ($beos)
        {
            $this->platform = "BeOS"; 
        }
    }
    
    /**
     * Method to test for Opera
     * @param void
     * @return property $broswer
     * @return property version
     * @return bool false on failure
     */
    public function isOpera()
    {
        // test for Opera        
        if (eregi("opera",$this->useragent))
        {
            $val = stristr($this->useragent, "opera");
            if (eregi("/", $val)){
                $val = explode("/",$val);
                $this->browsertype = $val[0];
                $val = explode(" ",$val[1]);
                $this->version = $val[0];
            }else{
                $val = explode(" ",stristr($val,"opera"));
                $this->browsertype = $val[0];
                $this->version = $val[1];
            }
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to check for FireFox
     * @param void
     * @return bool false on failure
     */ 
    public function isFirefox()
    {
        if(eregi("Firefox", $this->useragent))
        {
            $this->browsertype = "Firefox"; 
            $val = stristr($this->useragent, "Firefox");
            $val = explode("/",$val);
            $this->version = $val[1];
            return true;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to check for Konquerer
     * @param void
     * @return prop $browser
     * @return prop $version
     * @return bool true on success
     */
    public function isKonqueror()
    {
        if(eregi("Konqueror",$this->useragent))
        {
            $val = explode(" ",stristr($this->useragent,"Konqueror"));
            $val = explode("/",$val[0]);
            $this->browsertype = $val[0];
            $this->version = str_replace(")","",$val[1]);
            return TRUE;
        }
        else {
            return FALSE;
        }
        
    }//end func
    
    /**
     * Method to check for Internet Explorer v1
     * @param void
     * @return bool true on success
     * @return prop $browsertype
     * @return prop $version
     */
    public function isIEv1()
    {
        if(eregi("microsoft internet explorer", $this->useragent))
        {
            $this->browsertype = "MSIE"; 
            $this->version = "1.0";
            $public = stristr($this->useragent, "/");
            if (ereg("308|425|426|474|0b1", $public))
            {
                $this->version = "1.5";
            }
            return TRUE;
        }
        else {
            return FALSE;
        }
    }//end public function
    
    /**
     * Method to check for Internet Explorer later than v1
     * @param void
     * @return bool true on success
     * @return prop $browsertype
     * @return prop $version
     */
    public function isMSIE()
    {
        if(eregi("msie", $this->useragent) && !eregi("opera",$this->useragent))
        {
            $this->browsertype = "MSIE"; 
            $val = explode(" ",stristr($this->useragent,"msie"));
            $this->browsertype = $val[0];
            $this->version = $val[1];
            
            return TRUE;
        }
        else {
            return FALSE;
        }
    }//end public function
    
    /**
     * Method to check for Galeon
     * @param void
     * @return bool true on success
     */
    public function isGaleon()
    {
        if(eregi("galeon",$this->useragent))
        {
            $val = explode(" ",stristr($this->useragent,"galeon"));
            $val = explode("/",$val[0]);
            $this->browsertype = $val[0];
            $this->version = $val[1];
            return TRUE;
        }
        else {
            return FALSE;
        }
    }//end func
    
    /**
     * Now we do the tests for browsers I can't test...
     * If someone finds a bug, please report it ASAP to me please!
     */
    
    /**
     * Method to check for WebTV browser
     * @param void
     * @return bool true on success
     * @return prop $browsertype
     * @return prop $version
     */
    public function isWebTV()
    {
        if(eregi("webtv",$this->useragent))
        {
            $val = explode("/",stristr($this->useragent,"webtv"));
            $this->browsertype = $val[0];
            $this->version = $val[1];
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to check for BeOS's NetPositive
     * @param void
     * @return bool true on success
     * @return prop $browsertype
     * @return prop $version
     */
    public function isNetPositive()
    {
        if(eregi("NetPositive", $this->useragent))
        {
            $val = explode("/",stristr($this->useragent,"NetPositive"));
            $this->platform = "BeOS"; 
            $this->browsertype = $val[0];
            $this->version = $val[1];
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to check for MSPIE (Pocket IE)
     * @param void
     * @return bool true on success
     */
    public function isMSPIE()
    {
        if(eregi("mspie",$this->useragent) || eregi("pocket", $this->useragent))
        {
            $val = explode(" ",stristr($this->useragent,"mspie"));
            $this->browsertype = "MSPIE"; 
            $this->platform = "WindowsCE"; 
            if (eregi("mspie", $this->useragent))
                $this->version = $val[1];
            else {
                $val = explode("/",$this->useragent);
                $this->version = $val[1];
            }
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to test for iCab
     * @param void
     * @return bool true on success
     */
    public function isIcab()
    {
        if(eregi("icab",$this->useragent))
        {
            $val = explode(" ",stristr($this->useragent,"icab"));
            $this->browsertype = $val[0];
            $this->version = $val[1];
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to test for the OmniWeb Browser
     * @param void
     * @return bool True on success
     */
    public function isOmniWeb()
    {
        if(eregi("omniweb",$this->useragent))
        {
            $val = explode("/",stristr($this->useragent,"omniweb"));
            $this->browsertype = $val[0];
            $this->version = $val[1];
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to check for Phoenix Browser
     * @param void
     * @return bool true on success
     */
    public function isPhoenix()
    {
        if(eregi("Phoenix", $this->useragent))
        {
            $this->browsertype = "Phoenix"; 
            $val = explode("/", stristr($this->useragent,"Phoenix/"));
            $this->version = $val[1];
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to check for Firebird
     * @param void
     * @return bool true on success
     */
    public function isFirebird()
    {
        if(eregi("firebird", $this->useragent))
        {
            $this->browsertype = "Firebird"; 
            $val = stristr($this->useragent, "Firebird");
            $val = explode("/",$val);
            $this->version = $val[1];
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to check for Mozilla alpha/beta
     * @param void
     * @return bool true on success
     */
    public function isMozAlphaBeta()
    {
        if(eregi("mozilla",$this->useragent) && 
           eregi("rv:[0-9].[0-9][a-b]",$this->useragent) && 
           !eregi("netscape",$this->useragent))
        
        {
            $this->browsertype = "Mozilla"; 
            $val = explode(" ",stristr($this->useragent,"rv:"));
            eregi("rv:[0-9].[0-9][a-b]",$this->useragent,$val);
            $this->version = str_replace("rv:","",$val[0]);
            return TRUE;
        }
        else {
            return FALSE;
        }
    }//end public function

    /**
     * Method to check for Mozilla Stable versions
     * @param void
     * @return bool true on success
     */
    public function isMozStable()
    {
        if(eregi("mozilla",$this->useragent) &&
           eregi("rv:[0-9]\.[0-9]",$this->useragent) && 
           !eregi("netscape",$this->useragent))
        {
            $this->browsertype = "Mozilla"; 
            $val = explode(" ",stristr($this->useragent,"rv:"));
            eregi("rv:[0-9]\.[0-9]\.[0-9]",$this->useragent,$val);
            $this->version = str_replace("rv:","",$val[0]);
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Method to check for Lynx and Amaya
     * @param void
     * @return bool true on success
     */
    public function isLynx()
    {
        if(eregi("libwww", $this->useragent))
        {
            if (eregi("amaya", $this->useragent))
            {
                $val = explode("/",stristr($this->useragent,"amaya"));
                $this->browsertype = "Amaya"; 
                $val = explode(" ", $val[1]);
                $this->version = $val[0];
            } else {
                $val = explode("/",$this->useragent);
                $this->browsertype = "Lynx"; 
                $this->version = $val[1];
            }
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * method to check for safari browser
     * @param void
     * @return bool true on success
     */
    public function isSafari()
    {
        if(eregi("safari", $this->useragent))
        {
            $this->browsertype = "Safari"; 
            $this->version = "";
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Various tests for Netscrape
     * @param void
     * @return bool true on success
     */
    public function isNetscape()
    {
        if(eregi("netscape",$this->useragent))
        {
            $val = explode(" ",stristr($this->useragent,"netscape"));
            $val = explode("/",$val[0]);
            $this->browsertype = $val[0];
            $this->version = $val[1];
            return TRUE;
        }
        elseif(eregi("mozilla",$this->useragent) && 
                !eregi("rv:[0-9]\.[0-9]\.[0-9]",$this->useragent))
        {
            $val = explode(" ",stristr($this->useragent,"mozilla"));
            $val = explode("/",$val[0]);
            $this->browsertype = "Netscape"; 
            $this->version = $val[1];
            return TRUE;
        }
        else {
            return FALSE;
        }
    }//end func
    
    /**
     * Method to check for AOL connections
     * @param void
     * @return bool true on Success
     */
    public function isAOL()
    {
        if (eregi("AOL", $this->useragent)){
            $public = stristr($this->useragent, "AOL");
            $public = explode(" ", $public);
            $this->aol = ereg_replace("[^0-9,.,a-z,A-Z]", "", $public[1]);
            return TRUE;
        }
        else { 
            return FALSE;
        }
    }
    
    /**
     * Method to tie them all up and output something useful
     * @param void
     * @return array
     */
    public function whatBrowser()
    {
        return array('browsertype' => $this->browsertype, 
                     'version' => $this->version, 
                     'platform' => $this->platform, 
                     'AOL' => $this->aol); 
    }

    /**
    * Method to get the Browser Name
    * @param void
    * @return string browser type
    */
    function getBrowser()
    {
        return $this->browsertype;
    }
    
    /**
    * Method to get the Version of the browser
    *
    * @param void
    * @return string browser version 
    */
    function getVersion()
    {
        return $this->version;
    }

    /**
    * Method to get the Operating system 
    * @param void
    * @return string operating system 
    */
    function getPlatform()
    {
        return $this->platform;
    }

}//end class
?>