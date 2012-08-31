<?php
/**
*
* Utilities class for the user registration
*
* This class has all the functions needed 
* for the user registration process
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
* @package   userregistration
* @author    Administrative User <admin@localhost.local>
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @Author Wesley Nitsckie
* @link      http://avoir.uwc.ac.za
*/

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}

class utilities extends object
{


    /**
     * Constructor method. It does nothing here
     *
     * @access public
     * @param void
     * @return VOID
     *
     */
    public function init()
    {
        $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
    }
    
    public function doRegistration($username, $password, $captcha, $email){
        //check if everything is there
        if( $username == "" || 
            $password == "" ||
            $captcha == "" ||
            $email == "") {
                
            return array('success' => false, 'message' => 'Missing required fields');                           
        }
        
        //first check the captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha')) {
            return array('success' => false, 
                        'message' => 'Captha did not match. Click the redraw button if you need a clearer image');
        }
        //then the check if the username already exist
        if ($this->objUserAdmin->userNameAvailable($username) == FALSE) {
             return array('success' => false, 
                        'message' => 'The username is not available. Please try a different one.');
        }
        
        //check if the email exist then ask the user if 
        //lost his password?
        
        //add the new user
         $userId = $this->objUserAdmin->generateUserId();
         $pkid = $this->objUserAdmin->addUser($userId, $username, $password, '', '', '', $email, '', '', '', '', 'useradmin', 1);
            // Email Details to User
            $this->objUserAdmin->sendRegistrationMessage($pkid, $password);
            $this->setSession('id', $pkid);
            //$this->setSession('password', $password);
            $this->setSession('time', $password);
        
        return array('success' => true);
        
    }
    
}