<?php

/**
* 
* Joomla sessions
*
* Model class to access the Joomla sessions
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
* @package   joomla
* @author    Derek Keats <dkeats@uwc.ac.za>
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: dbjoomlasession_class_inc.php 11943 2008-12-29 21:23:33Z charlvn $
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
* 
* Joomla sessions dbtable class
* 
* Bridge Chisimba to Joomla: This class gives access to the Joomla sessions table
*
* @author Derek Keats
* @category Chisimba
* @package joomla
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class dbjoomlasession extends dbtable
{

    
    /**
    *
    * Constructor for the module dbtable class for DATABASETABLE{_UNSPECIFIED}
    * It sets the database table via the parent dbtable class init
    * method, and instantiates required objects.
    *
    */
    public function init()
    {
        try {
            parent::init('jos_users');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
    
    /**
     * 
     * Method to set a passed Joomla session key to the passed value
     * 
     * @param string Key to set
     * @param mixed Value to set
     * @return mixed The new value
     * 
     */
    function set( $key, $value ) {
        $_SESSION[$key] = $value;
        return $value;
    }
    
    function clearOldSessions()
    {
                            $query = "DELETE FROM #__session"
                    . "\n WHERE session_id != ". $this->_db->Quote( $session->session_id )
                    . "\n AND username = ". $this->_db->Quote( $row->username )
                    . "\n AND userid = " . (int) $row->id
                    . "\n AND gid = " . (int) $row->gid
                    . "\n AND guest = 0"
                    ;
    }
    
    /**
     * Generate a unique session id
     * @return string
     */
    function generateId() {
        $failsafe   = 20;
        $randnum    = 0;

        while ($failsafe--) {
            $randnum        = md5( uniqid( microtime(), 1 ) );
            $new_session_id = mosMainFrame::sessionCookieValue( $randnum );

            if ($randnum != '') {
                $query = "SELECT $this->_tbl_key"
                . "\n FROM $this->_tbl"
                . "\n WHERE $this->_tbl_key = " . $this->_db->Quote( $new_session_id )
                ;
                $this->_db->setQuery( $query );
                if(!$result = $this->_db->query()) {
                    die( $this->_db->stderr( true ));
                }

                if ($this->_db->getNumRows($result) == 0) {
                    break;
                }
            }
        }

        $this->_session_cookie  = $randnum;
        $this->session_id       = $new_session_id;
    }
}
?>
