<?php

/**
 * Sort Direction Class
 *
 * This class is used to detect which column is being sorted by, and allow reverse sorting
 * Explanation: Often you want to have links that sort columns, e.g.
 *
 * http://nextgen/index.php?module=something&sort=firstname
 *
 * Then you want to reverse it to
 *
 * http://nextgen/index.php?module=something&sort=firstname&direction= DESC
 *
 * This function will help you achieve that
 *
 * PHP version 5
 * 
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/* ----------- data class extends dbTable for tbl_brawam_reports------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

class sortdirection extends object
{
    
    /**
    * Method to check whether a column is being sorted, and then whether to reverse the direction ot not.
    * @param  string $item   The Item you are sorting on
    * @param  string $module The module you are calling this class from
    * @param  string $action Action to take
    * @retrun string A formed URL
    */
    public function sortItems($item, $module = NULL, $action = NULL)
    {
        if ($this->getParam('sort') == $item && strtoupper($this->getParam('direction')) == 'ASC') {
            $direction = 'DESC';
        } else {
            $direction = 'ASC';
        }
    
        return $this->uri(array('sort'=>$item, 'direction'=>$direction, 'action'=>$action), $module);
    }

}

?>