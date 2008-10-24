<?php

/**
 * notes db class
 *
 * Notes database abstraction class for Chisimba
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
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* ----------- data class extends dbTable for tbl_context_usernotes------------*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}

/**
 * notes db class
 *
 * Notes database abstraction class for Chisimba
 *
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class dbnotes extends dbTable {

    /**
     * Constructor method to define the table
     */
    public function init() {
        parent::init ( 'tbl_context_usernotes' );
    }

    /**
     * Save method for editing a record in this table
     *
     * @param  string $mode:  edit if coming from edit, add if coming from add
     * @param  string $userId : The id of the user
     * @return null
     * @access public
     */
    public function saveRecord($userId, $mode = null) {
        $id = addslashes ( TRIM ( $_POST ['id'] ) );
        $nodeId = addslashes ( TRIM ( $_POST ['nodeId'] ) );
        //$userId = addslashes(TRIM($_POST['userId']));
        $note = addslashes ( TRIM ( $_POST ['note'] ) );
        // if edit use update
        if ($mode == "edit") {
            $this->update ( "id", $id, array ('nodeId' => $nodeId, 'userId' => $userId, 'note' => $note ) );
        }
        // if add use insert
        if ($mode == "add" || $more = null) {
            $this->insert ( array ('nodeId' => $nodeId, 'userId' => $userId, 'note' => $note ) );
        }
    }

    /**
     * Method to get the note id for a given node and user
     *
     * @param  string $nodeId : The Node Id
     * @param  string $userId : The user Id
     * @return array  : The note
     * @access public
     */
    public function getNote($nodeId, $userId) {
        $ret = $this->getArray ( "SELECT *  from tbl_context_usernotes WHERE userId=" . $userId . " AND nodeId='" . $nodeId . "'" );
        return $ret;
    }
}

?>