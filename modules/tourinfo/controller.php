<?php
/**
 * Tour Info controller class
 * 
 * Class to control the tourinfo module
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  chisimba
 * @package   tourinfo
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.mxit.co.za/
 * @link      http://www.clickatell.co.za/
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Tour info controller class
 *
 * Class to control the tourinfo module.
 *
 * @category  Chisimba
 * @package   tourinfo
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.mxit.co.za/
 * @link      http://www.clickatell.co.za/
 */

class tourinfo extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $conn;
    public $jserver;
    public $jport;
    public $juser;
    public $jpass;
    public $jclient;
    public $jdomain;
    public $objConfig;
    public $objOps;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject ( 'language', 'language' );
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objOps = $this->getObject('tourops');
            // Include the needed libs from resources
            include ($this->getResourcePath ( 'XMPPHP/BOSH.php', 'im' ));
            
            // Get the sysconfig variables for the Jabber user to set up the connection.
            $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->jserver = $this->objSysConfig->getValue ( 'jabberserver', 'tourinfo' );
            $this->jport = $this->objSysConfig->getValue ( 'jabberport', 'tourinfo' );
            $this->juser = $this->objSysConfig->getValue ( 'jabberuser', 'tourinfo' );
            $this->jpass = $this->objSysConfig->getValue ( 'jabberpass', 'tourinfo' );
            $this->jclient = $this->objSysConfig->getValue ( 'jabberclient', 'tourinfo' );
            $this->jdomain = $this->objSysConfig->getValue ( 'jabberdomain', 'tourinfo' );

            $this->conn = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method to handle messages about stuff for tourists
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {

            case 'messagehandler' :
                $this->requiresLogin();
                $this->messageHandle();
                break;

            default: 
                echo $this->objOps->genShortCode("tourist info!");
                break;
        }
    }

    public function messageHandle() {
        // message handler for XMPP messages from MXit etc
        log_debug("Starting messagehandler");
        // This is a looooong running task... Lets use the background class to handle it
        //check the connection status
        //$status = $this->objBack->isUserConn ();
        //keep the user connection alive even if the browser is closed
        //$callback = $this->objBack->keepAlive ();
        // Now the code is backrounded and cannot be aborted! Be careful now...
        $this->conn->autoSubscribe ();
        try {
            $this->conn->connect ();
            while ( ! $this->conn->isDisconnected () ) {
                $payloads = $this->conn->processUntil ( array ('message', 'presence', 'end_stream', 'session_start', 'reply' ) );
                foreach ( $payloads as $event ) {
                    $pl = $event [1];

                    switch ($event [0]) {
                        case  'reply':
                            log_debug("reply to message...");

                            break;

                        case 'message' :
                            switch ($pl ['body']) {
                                // administrative functions that only the owner should be able to do
                                case 'quit' :
                                    $poster = explode('/', $pl['from']);
                                    $poster = $poster[0];
                                    $this->conn->message($pl['from'],"KTHNXBYE");
                                    $this->conn->disconnect ();
                                    die();
                                    break;

                                case 'break' :
                                    $poster = explode('/', $pl['from']);
                                    $poster = $poster[0];
                                    $this->conn->send ( "</end>" );
                                    die();
                                    break;

                                case 'Help' :
                                case 'HELP' :
                                case 'help' :
                                    // return some help text
                                    $message = <<<EOT
This system makes use of a number of commands that will return certain information. Here is the list of commands:

- help - display this message
EOT;
                                    $this->conn->message($pl['from'],$message);
                                    continue;

                                case 'NULL' :
                                    continue;

                                default:
                                    continue;
                                

                            }
                            // Send a response message
                            if ($pl ['body'] != "" && $pl ['body'] != "quit" && $pl ['body'] != "break" && $pl ['body'] != "help" && $pl['type'] != 'error') {
                                $poster = explode('/', $pl['from']);
                                $poster = $poster[0];
                                log_debug($poster." is the poster");
                                // known shortcodes
                                $shortcodes = array('test', 'abc123');
                                if(in_array( $pl['body'], $shortcodes )) {
                                    $this->conn->message($pl['from'],"OOOH!!! I KNOW that one!");
                                }
                                else {
                                    $this->conn->message($pl['from'],"Is that a trick question?");
                                }
                            }
                            else {
                                continue;
                            }
                            break;

                        case 'presence' :
                            // $this->objDbPres->updatePresence ( $pl );
                            break;

                        case 'session_start' :
                            $this->conn->getRoster ();
                            $this->conn->presence ( $status = $this->objLanguage->languageText ( 'mod_tourinfo_presgreeting', 'tourinfo' ) );
                            break;

                    }
                }
            }
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
        // OK something went wrong, make sure the sysadmin knows about it!
        $email = $this->objConfig->getsiteEmail ();
        //$call2 = $this->objBack->setCallBack ( $email, $this->objLanguage->languageText ( 'mod_im_msgsubject', 'im' ), $this->objLanguage->languageText ( 'mod_im_callbackmsg', 'im' ) );
        break;

    }

    public function  popMessage($jid, $type, $groupname, $message = NULL) {
        $conn = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
        
        $conn->connect();
        $conn->processUntil('session_start');
        $conn->presence();
        $conn->message($jid, $msg);
        $conn->disconnect();

        return;
    }

    /**
     * Overide the login object in the parent class
     *
     * @param  void
     * @return bool
     * @access public
     */
    public function requiresLogin() {
        return FALSE;
    }
}
?>