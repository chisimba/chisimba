<?php
define ("NOCONNECTION", 0);
define ("ONLINE", 1);
define ("OFFLINE", 2);
define ("UNKNOWN", 3);


/**
* Class to get the online status of a user on a number of instant
* messenger services. The approach is to find the simplest method
* available and adapt it to KEWL.NextGen. Adapted by Derek Keats
*
* The get_yahoo_status method was written by Setec Astronomy - setec@freemail.it
*   then rewritten to return an image by Derek Keats
* The get_msn_status method was written by
* The get_icq_status method was written by
* The get_jabber_status method was written by
*/

class online extends object
{

    /**
    * The initialize method to set the default properties
    */
    public function init()
    {
        $this->objConfig=&$this->getObject('config', "config");
        $this->objIcon = &$this->getObject('geticon', 'htmlelements');
    }

    /**
    * This method allows to check the online status of an Yahoo! account.
    * It connects directly to the Yahoo! status server.
    *
    * @param string $yahoo : The user account on yahoo.
    */
    public function getYahooStatus ($yahooId = "")
    {
        // Set up the icons for online offline unknown status
        $this->objIcon->setIcon("yahoo_icon_on");
        $online=$this->objIcon->show();
        $this->objIcon->setIcon("yahoo_icon_off");
        $offline=$this->objIcon->show();
        $this->objIcon->setIcon("yahoo_icon_unknown");
        $unknown=$this->objIcon->show();
        $this->objIcon->setIcon("yahoo_icon_noconnection");
        $noconn=$this->objIcon->show();

        //The yahoo server
        $yServer = "http://opi.yahoo.com/online?u=";
        //open the connection
        $lines = @file ($yServer . $yahooId . "&m=t");
        if ($lines) {
            $response = implode ("", $lines);
            if (strpos ($response, "NOT ONLINE") !== false) {
                return $offline;
            } elseif (strpos ($response, "ONLINE") !== false) {
                return $online;
            } else {
                return $unknown;
            }
        } else {
            return $noconn;
        }
    }

    /**
    * This method allows to check the online status of a MSN account.
    *
    * @param string $msn : The user account on MSN.
    */
    public function getMsnStatus ($msn = "")
    {
        //return fopen("http://gateway.messenger.hotmail.com/gateway/gateway.dll?Action=open&Server=NS&IP=messenger.hotmail.com","r");
        //return fopen("http://gateway.messenger.hotmail.com/gateway/gateway.dll?Action=open&Server=NS&IP=messenger.hotmail.com&user=derekkeats@hotmail.com", "r");
        return "{not ready}";
    }

    /**
    * This method allows to check the online status of a ICQ account.
    *
    * @param string $icq : The user account on ICQ.
    */
    public function getIcqStatus ($icq = "")
    {
        //This seems to be the simplest method, copied from KEWL
        return "<img src='http://online.mirabilis.com/scripts/online.dll?icq="
          .$icq."&img=5' width= 18 height=18 border=0 align=absmiddle hspace=6>";
    }

    /**
    * This method allows to check the online status of a ICQ account.
    *
    * @param string $jabber : The user account on the jabber server.
    * @param string $jabberserver: the IP address or name for the jabber server
    */
    public function get_ijabber_status ($jabber = NULL, $jabberserver=NULL)
    {
        return "Not ready";
    }

    public function getKngStatus($user)
    {
        // Set up the icons for online offline unknown status
        $this->objIcon->setIcon("kng_on", "png");
        $online=$this->objIcon->show();
        $this->objIcon->setIcon("kng_off", "png");
        $offline=$this->objIcon->show();
        $objCheck=&$this->getObject('dbkngon');
        if ($objCheck->isLoggedIn($user)) {
            return $online;
        } else {
            return $offline;
        }
    }


}  // Class
?>