<?php
/**
 * Database access to manage users in the oeruserdata module
 *
 * Database access to manage users in the oeruserdata module. It works with
 * primary user data, as well as the userextra data that is created
 * by the OER module.
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
 * Database access to manage users in the oeruserdata module
 *
 * Database access to manage users in the oeruserdata module. It works with
 * primary user data, as well as the userextra data that is created
 * by the OER project.
 * 
 * @package   oer
 * @author    Derek Keats derek@dkeats.com
 * 
 */
class dboerusermain extends dbtable {
   

    /**
     * 
     * Standard init method
     * 
     * @access public
     * @return void
     * 
     */
    public function init() {
        parent::init("tbl_users");
    }
    
    /**
     *
     * Count the number of users active
     * 
     * @return type 
     */
    public function getUserCount()
    {
        $where = " WHERE isactive=1 ";
        return $this->getRecordCount(" WHERE isactive=1 ");
    }
}
?>