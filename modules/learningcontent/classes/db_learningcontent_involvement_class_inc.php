<?php
/**
 * Context Content User Tracker
 *
 * This class is intended to track which pages users were involved in either
 * creating or editing.
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
 * @version    $Id: db_contextcontent_involvement_class_inc.php 11217 2008-10-30 20:45:37Z charlvn $
 * @package    contextcontent
 * @author     Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
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
 * Context Content User Tracker
 *
 * This class is intended to track which pages users were involved in either
 * creating or editing.
 *
 * @author Tohir Solomons
 *
 */
class db_learningcontent_involvement extends dbtable
{
    /**
     * Constructor
     */
    function init()
    {
        parent::init('tbl_learningcontent_involvement');
    }

}


?>
