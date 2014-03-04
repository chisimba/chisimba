<?php
/**
 * pickmeup controller class
 *
 * Class to control the pickmeup module
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
 * @package   pickmeup
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
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
 * pickmeup controller class
 *
 * Class to control the pickmeup module.
 *
 * @category  Chisimba
 * @package   pickmeup
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
class pickmeup extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    
    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->objLanguage      = $this->getObject ( 'language', 'language' );
            $this->objConfig        = $this->getObject('altconfig', 'config');
            $this->objSysConfig     = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser          = $this->getObject('user', 'security');
            $this->objMongo         = $this->getObject('geomongo', 'mongo');
            $this->objOps           = $this->getObject('geoops', 'geo');
            $this->objCookie        = $this->getObject('cookie', 'utilities');
            $this->objUrl           = $this->getObject('url', 'strings');
            $this->objUserAdmin     = $this->getObject('useradmin_model2', 'security');
        }
        catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL:
            	$currLocation = NULL;
                if($this->objCookie->exists('pickmeup_location') ) {
                    $currLocation = $this->objCookie->get('pickmeup_location');
                }
                if($currLocation == NULL) {
                    return 'default_tpl.php';
                }
                else {
                	$latlon = explode("|", $currLocation);
                	$lat = $latlon[0];
                	$lon = $latlon[1];
                	$this->nextAction('setloc', array('lat' => $lat, 'lon' => $lon));
                }
                break;
                
            case 'setloc':
            	$lat = $this->getParam('lat');
            	$lon = $this->getParam('lon');
            	// remember this...
            	if($this->objCookie->exists('pickmeup_location') ) {
                    $this->objCookie->cookiedelete('pickmeup_location');
                    $this->objCookie->set( 'pickmeup_location', $lat."|".$lon, time()+60*60*24*30);
                }
                else {
                    $this->objCookie->set( 'pickmeup_location', $lat."|".$lon, time()+60*60*24*30);
                }
            	$limit = 10;
            	$choices = json_decode($this->objMongo->getByLonLat(floatval($lon), floatval($lat), $limit));
            	$this->setVarByRef('choices', $choices);
            	$this->setVarByRef('lat', $lat);
            	$this->setVarByRef('lon', $lon);
            	
            	return 'display_tpl.php';
            	break;
                
            case 'getbylonlat' :
                $lon   = $this->getParam('lon', NULL);
                $lat   = $this->getParam('lat', NULL);
                $limit = $this->getParam('limit', 10); 
                $res = NULL;
                if($lat == NULL || $lon == NULL) {
                    $res = array($this->objLanguage->languageText("mod_geo_notenoughparams", "geo"));
                    $res = json_encode($res);
                }
                else {
                    $res = $this->objMongo->getByLonLat(floatval($lon), floatval($lat), $limit);
                    
                }
                header('Cache-Control: no-cache, must-revalidate');
                header('Content-type: application/json');
                echo $res;
                break;
                
            case 'getbyplacename' : 
                $placename = $this->getParam('placename');
                $placename = urldecode($placename);
                $limit = $this->getParam('limit', 10);
                $res = $this->objMongo->getByPlacename($placename, $limit);
                
                header('Cache-Control: no-cache, must-revalidate');
                header('Content-type: application/json');
                echo $res;
                break;
                
            case 'getradiuskm' :
                $lon   = $this->getParam('lon', NULL);
                $lat   = $this->getParam('lat', NULL);
                $radius = $this->getParam('radius');
                
                $res = $this->objMongo->getRadiusKm($lon, $lat, $radius);
                header('Cache-Control: no-cache, must-revalidate');
                header('Content-type: application/json');
                echo $res;
                break;
            
            case 'getradiusmi' :
                $lon   = $this->getParam('lon', NULL);
                $lat   = $this->getParam('lat', NULL);
                $radius = $this->getParam('radius');
                
                $res = $this->objMongo->getRadiusMiles($lon, $lat, $radius);
                header('Cache-Control: no-cache, must-revalidate');
                header('Content-type: application/json');
                echo $res;
                break;
                
                
            case 'placesearch' :
            	$placename = ucwords($this->getParam('placename'));
            	$limit = $this->getParam('limit', 10);
                $res = $this->objMongo->getByPlacename($placename, $limit);
                $choices = json_decode($res);
                if($this->objCookie->exists('geo_location') ) {
                    $currLocation = $this->objCookie->get('geo_location');
                    $latlon = explode("|", $currLocation);
                    $lat = $latlon[0];
                    $lon = $latlon[1];
                }
                else {
                	$lat = 0;
                	$lon = 0;
                }
                $zoom = 2;
                $this->setVarByRef('zoom', $zoom);
                $this->setVarByRef('choices', $choices);
            	$this->setVarByRef('lat', $lat);
            	$this->setVarByRef('lon', $lon);
            	return 'display_tpl.php';
                break;
                
            case 'signin':
            	$username = $this->getParam('user');
            	$shaPass = $this->getParam('pass');
            	
            	if ($this->objUser->authenticateUser($username, $shaPass, TRUE)) {
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo json_encode(array("status" => true, "message" => "logged in", "call" => "signin"));
            	}
            	else { 
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo json_encode(array("status" => false, "message" => "bad password", "call" => "signin"));
            	}
            	break;
            	
            case 'signup':
            	// $username = $this->getParam('user');
            	$password = $this->getParam('pass');
            	$email = $this->getParam('email');
            	$username = $email;
            	$cellnumber =  $this->getParam('phone');
            	$firstname = $this->getParam('firstname');
            	$surname = $this->getParam('surname');
            	
            	
            	
            	if ($this->objUserAdmin->userNameAvailable($username) == FALSE) {
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo json_encode(array('status' => false, "message" => "email address taken", "call" => "signup"));
            		break;
            	}
            	
            	if ($this->objUserAdmin->emailAvailable($email) == FALSE) {
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo json_encode(array('status' => false, "message" => 'email taken', "call" => "signup"));
            		break;
            	}
            	// Check for any problems with password
            	if ($password == '') {
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo json_encode(array('status' => false, "message" => 'no password entered', "call" => "signup"));
            		break;
            	}
            	
            	// Check that email address is valid
            	if (!$this->objUrl->isValidFormedEmailAddress($email)) {
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo  json_encode(array('status' => false, "message" => 'email not valid', "call" => "signup"));
            		break;
            	}
            	
            	$userId = $this->objUserAdmin->generateUserId();
            	$pkid = $this->objUserAdmin->addUser($userId, $username, $password, $title = '', $firstname, $surname, $email, $sex = '', $country = '', $cellnumber='', $staffnumber='', $accountType='useradmin', $accountstatus='1');
            	// Email Details to User
            	$this->objUserAdmin->sendRegistrationMessage($pkid, $password);
            	header('Cache-Control: no-cache, must-revalidate');
            	header('Content-type: application/json');
            	echo json_encode(array('status' => true, "message" => 'User registered, email sent', "call" => "signup"));
            	break;

            case 'forgotpass':
            	$email = $this->getParam('email');
            	$username = $email;
            	$userDetails = $this->objUserAdmin->getUserNeedPassword($username, $email);
            	
            	$usernameAvailable = $this->objUserAdmin->usernameAvailable($username);
            	
            	if ($userDetails == FALSE) {
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo json_encode(array('status' => false, 'message' => 'User not found', 'call' => 'forgotpass'));
            		break;
            	}
            	
            	$this->objUserAdmin->newPasswordRequest($userDetails['id']);
            	$this->setSession('passwordrequest', $userDetails['id']);
            	
            	
            	header('Cache-Control: no-cache, must-revalidate');
            	header('Content-type: application/json');
            	echo json_encode(array('status' => true, 'message' => 'Password reset', 'call' => 'forgotpass'));
            	break;
            
            case 'invitefriend' :
                echo $this->objOps->showInviteForm();
                break;

            case 'changelocation' :
                if($this->objCookie->exists('pickmeup_location') ) {
                    $this->objCookie->cookiedelete('pickmeup_location');
                }
                $this->nextAction('');
                break;
                
            case 'req':
            	$lat = $this->getParam('lat');
            	$lon = $this->getParam('lon');
            	$alt = $this->getParam('alt', NULL);
            	$cellnumber = $this->getParam('phone');
            	$message = $this->getParam('msg');
            	
            	$username = $this->getParam('username');
            	$password = $this->getParam('pass');
            	
            	$token = $cellnumber."_".sha1($password)."_".time();
            	
            	// authenticate the user
            	if ($this->objUser->authenticateUser($username, $password, TRUE)) 
            	{
            		// get nearby places for ref
            		$limit = 10;
            		$placesnear  = $this->objMongo->getByLonLat(floatval($lon), floatval($lat), $limit);
            		// store the request
            		//$this->objMongo->setCollection('pickups');
            		$data = array('type' => 'request', 'lat' => $lat, 'lon' => $lon, 'alt' => $alt, 'token' => $token, 'cellnumber' => $cellnumber, 'message' => $message, 'username' => $username);
            		$cursor = $this->objMongo->insert($data, 'pickups', NULL);
            		// @TODO check if in taxi bounding box
            		
            		// send the mail with map to taxi people
            		
            		$return = array('token' => $token, 'username' => $username, 'phone' => $cellnumber, 'datetime' => date('Y-m-d h:m:s', time()), 'status' => false, 'call' => 'req');
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo json_encode($return); //$placesnear;
            	}
            	else {
            		header('Cache-Control: no-cache, must-revalidate');
            		header('Content-type: application/json');
            		echo json_encode(array('status' => false, "message" => "Incorrect email address or password", "call" => "req"));
            	}
            	break;
            	
            case 'viewpickup':
            	
            	break;
            
            default:
                $this->nextAction('');
                break;
        }
    }

    /**
     * Method to turn off login for selected actions
     *
     * @access public
     * @param string $action Action being run
     * @return boolean Whether the action requires the user to be logged in or not
     */
    function requiresLogin($action='') {
        $allowedActions = array('', 'getdata', 'getbylonlat', 'getbyplacename', 'getradiuskm', 'getradiusmi', 'getbycountrycode', 
                                'getwikipedia', 'setloc', 'placesearch', 'changelocation', 'signin', 'signup', 'forgotpass', 'req', NULL);

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
?>
