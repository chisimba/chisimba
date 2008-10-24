<?php

/**
 * Context Parent Nodes
 *
 * Context Parent Nodes database abstraction class
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
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Context Parent Nodes
 *
 * Context Parent Nodes database abstraction class
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
class dbcontextparentnodes extends dbTable {

    /**
     * Context object
     *
     * @var object objDBContext;
     */
    public $objDBContext;

    /**
     * User object
     *
     * @var object objUser;
     */
    public $objUser;

    /**
     * Constructor
     */
    public function init() {
        parent::init ( 'tbl_context_parentnodes_has_tbl_context' );
        $this->objDBContext = $this->newObject ( 'dbcontext', 'context' );
        $this->objUser = $this->newObject ( 'user', 'security' );
    }

    /**
     * Method add a entry to the database
     *
     * @param string $contextId The context ID
     */
    public function createEntry($contextId, $contextCode) {
        if (! $this->valueExists ( 'tbl_context_contextCode', $contextCode )) {
            //create a bridge entry
            $this->insert ( array ('tbl_context_contextCode' => $contextCode, 'tbl_context_id ' => $contextId ) );
        }
    }
}

?>