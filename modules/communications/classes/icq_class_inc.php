<?php
/**
*
* Class for working with ICQ for a particular user.
*
* @author Derek Keats
*
* This class was written by Derek, but it drew extensively
* on code that I studies while travelling by airplane, and
* which I subsequently lost.
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
        $this->objDbUserparams = & $this->getObject('dbuserparamsadmin', "userparamsadmin");
    }

    /**
    * This method allows to check the online status of a ICQ account and return
    * an Icon. The two parameters work together. If you are looking up the status by
    * ICQ number, then just supply that as $icq. You can also supply KEWL.NextGen
    * userId as $icq and set mode to 'byuserid'. It will then first return the
    * ICQ number and then check status.
    *
    * @param string $icq : The user account on ICQ.
    * @param string $mode : Whether to hunt by icq number or by userId
    *
    * @return The icon for user status
    *
    */
    public function getStatusIcon($icq, $mode='byicqnumber')
    {
        if ( $mode == 'byuserid' ) {
            //$icq = $this->objDbUserparams->getValue('ICQ', $icq);
            $icq = '';
            if (!$icq) {
                $this->objIcon->alt=$this->objLanguage->languageText("mod_communications_icqnoidreg",'communications');
                $this->objIcon->setIcon("icq_noid");
                return $this->objIcon->show();
            }
        }

        switch ( $this->getIcqStatus($icq) ) {
            //If there is an error connecting to the server
            case "error_connecting":
                $this->objIcon->setIcon("icq_errcon");
                $this->objIcon->alt=$this->objLanguage->languageText("mod_communications_icqconerr",'communications');
                return $this->objIcon->show();
                break;
            //If we get a user is online message
            case "user_online":
                $this->objIcon->setIcon("icq_on");
                $this->objIcon->alt=$this->objLanguage->languageText("mod_communications_icqonline",'communications');
                return $this->objIcon->show();
                break;
            //If we get a user offline message
            case "user_offline":
                $this->objIcon->setIcon("icq_off");
                $this->objIcon->alt=$this->objLanguage->languageText("mod_communications_icqoffline",'communications');
                return $this->objIcon->show();
                break;
            //If the user has not activated online status indicator
            case "user_deactivated":
                $this->objIcon->setIcon("icq_noac");
                $this->objIcon->alt=$this->objLanguage->languageText("mod_communications_icqdeact",'communications');
                return $this->objIcon->show();
                break;
            //Any error or unknown message
            case "error_unknownerror":
            default:
                $this->objIcon->setIcon("icq_err");
                $this->objIcon->alt=$this->objLanguage->languageText("mod_communications_icqerror",'communications');
                return $this->objIcon->show();
                break;
        } #switch
    }



    /**
    * ICQ status checker function - returns 0 when $uin is offline,
    * 1 when online and 2 when the status indicator is deactivated,
    * 3 on error
    *
    * Based on code by Stefan Dengscherz, modified after studying the
    * code of Felipe Santiago and  Chip Cuccio
    *
    */

    public function getIcqStatus($uin)
    {
        $page="";
        // establish a socket connection to the icq status server
        $fp = @fsockopen("status.icq.com",80,$errno,$errstr,5);
        // error, can't connect to icq status server
        if (!$fp) {
            //If there is a connection error
            return "error_connecting";
        } else {
            // send request to the status server
            fputs($fp, "GET /online.gif?icq=$uin&img=5 HTTP/1.0\n\n");
            // Read the resultant page and close the connection.
            while(!feof($fp)) {
                $page .= fread($fp,256);
            }
            // close connection
            fclose($fp);
            // Return status depending on sent redirection string
            // The ICQ server returns an image, either online0.gif,
            // online1.gif, or online2.gif. This method looks for
            // these images in $page.
            if ( strstr($page,"online1") ) {
                return "user_online";
            } elseif ( strstr($page,"online0") ) {
                return "user_offline";
            } elseif ( strstr($page,"online2") ) {
                return "user_deactivated";
            } else {
                return "error_unknownerror";
            } #if
        } #if (no error connecting)
    }
}  // Class
?>
