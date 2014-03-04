<?php
/**
 *
 * Provides functionality specifically aimed at the UWC Elearning Mobile website
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
 * @package   uwcelearningmobile
 * @author    Qhamani Fenana qfenama@uwc.ac.za/qfenama@gmail.com
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mobilesecurity.php,v 1.4 2007-11-25 09:13:27 qfenama Exp $
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
// end security check

/**
* Class that handles the security on the uwc elearning mobile. 
* 
* @author Qhamani Fenama 
*
*/
class mobilesecurity extends object {
     
	
	function init() {
		$this->objLanguage = $this->getObject('language', 'language');
		$this->objUser = $this->getObject('user', 'security');
	}

    /**
    * Method to calculate basic stats on an array
    * @var array $arr: the array on which to perform the operations
    */
    public function CheckErrors()
    {
		$username = $this->getParam ( 'username', '' );
        $password = $this->getParam ( 'password', '' );
        $remember = $this->getParam( 'remember', 'off');

		if($username == '' || $password == '') {
            $error = $this->objLanguage->languageText('mod_uwcelearningmobile_wordloginrequired', 'uwcelearningmobile');
			return $error;
        }

        if(strlen($username) > 255 || strlen($password) > 255) {
            $error = $this->objLanguage->languageText('mod_uwcelearningmobile_wordlogintoolong', 'uwcelearningmobile');
			return $error;
        }
        if($remember == 'on') {
            $remember = true;
        }
        else {
            $remember = false;
        }

		$isactive = $this->objUser->lookupData($username);
		
		if($isactive == false)
        {
            $error = $this->objLanguage->languageText('mod_uwcelearningmobile_wordusernotexist', 'uwcelearningmobile');
            return $error;
		}
		if(($isactive['isactive']) != 1)
        {
            $error = $this->objLanguage->languageText('mod_uwcelearningmobile_wordaccntinactive', 'uwcelearningmobile');
            return $error;
		}
		
        if($this->objUser->authenticateUser ( $username, $password, $remember )) {
			
			return true;
        }
		else{
			$error = $this->objLanguage->languageText('mod_uwcelearningmobile_wordwronglogin', 'uwcelearningmobile');
			return $error;
		}
		     
    }
} 
?>
