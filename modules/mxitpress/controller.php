<?php
/**
 * mxitpress controller class
 *
 * Class to control the mxitpress module
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
 * @package   mxitpress
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 * @see       xmpphp
 * @see       http://www.igeek.co.za
 */

class mxitpress extends controller {
    
    public $objSysConfig;
    public $jserver;
    public $jport;
    public $juser;
    public $jpass;
    public $jclient;
    public $jdomain;
    public $conn;
    public $objDbMpUsers;

    public function init() {
        
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->jserver = $this->objSysConfig->getValue ( 'jabberserver', 'mxitpress' );
        $this->jport = $this->objSysConfig->getValue ( 'jabberport', 'mxitpress' );
        $this->juser = $this->objSysConfig->getValue ( 'jabberuser', 'mxitpress' );
        $this->jpass = $this->objSysConfig->getValue ( 'jabberpass', 'mxitpress' );
        $this->jclient = $this->objSysConfig->getValue ( 'jabberclient', 'mxitpress' );
        $this->jdomain = $this->objSysConfig->getValue ( 'jabberdomain', 'mxitpress' );
        
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objBack = $this->getObject ( 'background', 'utilities' );
        
        $this->objDbMpUsers = $this->getObject('dbmpusers');
        $this->objDbMpPosts = $this->getObject('dbmpposts');
        $this->objRPC = $this->getObject('metaweblogrpc');

        include ($this->getResourcePath ( 'XMPPHP/XMPP.php', 'im' ));
        $this->conn = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, 
                                        $this->jpass, $this->jclient, $this->jdomain, 
                                        $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
    }

    /**
     * Standard dispatch method to handle adding and saving
     * of comments
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL :
                // show a register form
                $form = 'form data';
                $this->setVarByRef('form', $form);
                return 'blogreg_tpl.php';
                break; 
                
            case 'messagehandler' :
                log_debug("Starting messagehandler");
                // This is a looooong running task... Lets use the background class to handle it
                //check the connection status
                $status = $this->objBack->isUserConn ();
                //keep the user connection alive even if the browser is closed
                $callback = $this->objBack->keepAlive ();
                // Now the code is backrounded and cannot be aborted! Be careful now...
                $this->conn->autoSubscribe ();
                try {
                    $this->conn->connect ();
                    while ( ! $this->conn->isDisconnected () ) {
                        $payloads = $this->conn->processUntil ( array ('message', 'presence', 'end_stream', 'session_start', 'reply' ) ); //array ('message', 'presence', 'end_stream', 'session_start', 'reply' )
                        foreach ( $payloads as $event ) {
                            $pl = $event [1];

                            switch ($event [0]) {
                                case 'message' :
                                    switch ($pl ['body']) {
                                        
                                        // administrative functions that only the owner should be able to do
                                        case 'quit' :
                                            log_debug("quitting");
                                            $this->conn->disconnect ();
                                            die();
                                            break;

                                        case 'break' :
                                            $this->conn->send ( "</end>" );
                                            break;
                                        
                                        default :
                                            $userSplit = explode ( "/", $pl ['from'] );
                                            $jid = $userSplit[0];
                                            $bod = explode(":", $pl['body']);
                                            if(isset($bod[0]) && strtolower($bod[0]) == 'register' ) {
                                                // registration request
                                                // registration string should be register:url username password
                                                log_debug("registration request");
                                                
                                                $regstr = $bod[1];
                                                $uarr = explode(" ", $regstr);
                                                $url = $uarr[0];
                                                $endpoint = $uarr[1];
                                                $user = $uarr[2];
                                                $pass = $uarr[3];
                                                
                                                if(!isset($uarr[0]) || !isset($uarr[1]) || !isset($uarr[2]) || !isset($uarr[3])) {
                                                    // registration string is wonky, send instructions again
                                                    $this->conn->message($pl['from'], $this->objLanguage->languageText("mod_mxitpress_detailedins", "mxitpress")); 
                                                    break;
                                                }
                                                
                                                $this->objDbMpUsers->addRecord($jid, $url, $endpoint, $user, $pass);
                                                $this->conn->message($pl['from'], $this->objLanguage->languageText("mod_mxitpress_thanksregister", "mxitpress")); 
                                                break;
                                            }
                                            if($this->objDbMpUsers->userExists($jid) == FALSE) {
                                                $this->conn->message($pl['from'], $this->objLanguage->languageText("mod_mxitpress_notregistered", "mxitpress")); 
                                                break;   
                                            }
                                            else {
                                                $bod = explode("#", $pl['body']);
                                                $title = $bod[0];
                                                $content = $bod[1];
                                                // check that this is not just a ping
                                                if($pl['body'] == '' || empty($pl['body']) || $pl['body'] == NULL) {
                                                    break;
                                                }
                                                // check for empty title or body
                                                if(empty($title) || empty($content))
                                                {
                                                    $this->conn->message($pl['from'], $this->objLanguage->languageText("mod_mxitpress_missingelement", "mxitpress"));
                                                    break;
                                                }
                                                else {    
                                                    $content = $content." <br /><br /> ".$this->objLanguage->languageText("mod_mxitpress_poweredby", "mxitpress");
                                                    $msgtype = $pl ['type'];
                                                    // add the post to the db and fire off the metaweblogapi call to the user's blog
                                                    $recarr = array('msgtype' => $msgtype, 'msgfrom' => $jid, 'msgtitle' => $title, 'msgbody' => $content);
                                                    $postid = $this->objDbMpPosts->addRecord($recarr);
                                                    $owner = $this->objDbMpUsers->getUser($jid);
                                                    $owner = $owner[0];
                                                    // fire off the metaweblog API call to the owner blog
                                                    $postdata = array('title' => $title, 'content' => $content, 'username' => $owner['username'], 'password' => $owner['pass'], 'endpoint' => $owner['endpoint'], 'url' => $owner['url']);
                                                    $ret = $this->objRPC->postToBlog($postdata);
                                                    // say thanks
                                                    $this->conn->message($pl['from'], $this->objLanguage->languageText("mod_mxitpress_thanksforposting", "mxitpress")." ".$ret);
                                                    break;
                                                }
                                            }
                                            // $this->conn->message($pl['from'], "I don't understand your request!");
                                            break;

                                        case 'hello' :
                                            $this->conn->message($pl['from'], "Hello! ".$pl['from']);
                                            break;
                                    }
                                case 'session_start' :
                                    $this->conn->getRoster();
                                    $this->conn->presence( $status = $this->objLanguage->languageText("mod_mxitpress_presgreeting", "mxitpress") );
                                    break;
                            }
                        }
                    }
                }
                catch ( customException $e ) {
                    customException::cleanUp ();
                    exit ();
                }
                break;

         }
    }
}
?>
