<?php
/**
 * XMPP Quiz controller class
 *
 * Controller class for the XMPP Quiz module
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
 * @category  Chisimba
 * @package   xmppquiz
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * XMPP Quiz controller class
 *
 * XMPP Quiz controller class
 *
 * @category  Chisimba
 * @package   xmppquiz
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class xmppquiz extends controller {
    public $objLanguage;
    public $objConfig;
    public $objSysConfig;
    public $objImOps;

    public function init() {
        include ($this->getResourcePath ( 'XMPPHP/XMPP.php', 'im' ));
        include ($this->getResourcePath ( 'XMPPHP/XMPPHP_Log.php', 'im' ));
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objImOps = $this->getObject('imops', 'im');

        // Get the sysconfig variables for the Jabber user to set up the connection.
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->jserver = $this->objSysConfig->getValue ( 'jabberserver', 'im' );
        $this->jport = $this->objSysConfig->getValue ( 'jabberport', 'im' );
        $this->juser = $this->objSysConfig->getValue ( 'jabberuser', 'im' );
        $this->jpass = $this->objSysConfig->getValue ( 'jabberpass', 'im' );
        $this->jclient = $this->objSysConfig->getValue ( 'jabberclient', 'im' );
        $this->jdomain = $this->objSysConfig->getValue ( 'jabberdomain', 'im' );
        $this->timeLimit = $this->objSysConfig->getValue ( 'imtimelimit', 'im' );
        $this->conn = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
    }

    public function dispatch($action = NULL) {
        switch ($action) {
            case "view" :
                // Do nothing
                break;

            default :
                // wha!
                //$this->objImOps->sendMessage ( 'pscott209@gmail.com', 'Hope this works!' );
                $conn2 = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
                $conn2->connect ();

                $conn2->processUntil ( 'session_start' );
                $conn2->message ( 'pscott209@gmail.com', "yo, from the xmpp quiz..." );
                $conn2->disconnect ();
                echo "hello?"; die();
                break;
        }
    }
}
?>