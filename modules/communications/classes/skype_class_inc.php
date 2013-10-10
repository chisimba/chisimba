<?php
/**
*
* Class for working with Skype for a particular user.
*
* @author Derek Keats
*

*
*
*/

class icq extends object
{

    /**
    *
    * @var object $objIcon Used to hold the icon object
    *
    */
    public $objIcon;

    /**
    *
    * @var object $objDbUserparams Used to hold the data object for getting user parameters
    *
    */
    public $objDbUserparams;

    /**
    * The initialize method to set the default properties
    */
    public function init()
    {
        $this->objLanguage=&$this->getObject('language', 'language');
        $this->objIcon = &$this->getObject('geticon', 'htmlelements');
        $this->objDbUserparams = & $this->getObject('dbuserparams', "userparams");
    }

    /**
    * This method allows to check the online status of a skype account and return
    * an Icon. The two parameters work together. If you are looking up the status by
    * ICQ number, then just supply that as $icq. You can also supply KEWL.NextGen
    * userId as $icq and set mode to 'byuserid'. It will then first return the
    * ICQ number and then check status.
    *
    * @param string $skypeid : The user account on skype.
    *
    * @return The icon for user status on skype
    *
    */
    public function getStatusIcon($skypeid=NULL)
    {
        if ($skypeid) {
            return "<img src=\"www.skypestatus.com/" . $skypeid . ".gif\" alt=\"skypeid\">";
        } else {
            return NULL;
        }
    }

    /**
    * Method to return the scype download link
    */
    public function getDownloadLink()
    {
        return "";
    }




}  // Class
?>