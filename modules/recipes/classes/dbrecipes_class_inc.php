<?php
/**
 *
 * Database access for recipes
 *
 * Database access for recipes. It allow access to the table
 * which contains a list of recipes and ingredients etc.
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
 * @package   recipes
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
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
 *
 * Database access for recipes
 *
 * Database access for recipes. It allow access to the table
 *  which contains a list of posted images for the gallery.
 *
 * @package   recipes
 * @author    Paul Scott <pscott@uwc.ac.za>
 *
 */
class dbrecipes extends dbtable
{

    /**
     *
     * Intialiser for the recipes database connector
     * @access public
     * @return VOID
     *
     */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_recipes_cookbooks');
        $this->objUser = $this->getObject('user', 'security');
    }   
    
    public function addCookbook($insarr) {
        $this->changeTable('tbl_recipes_cookbooks');
        $insarr['datecreated'] = $this->now();
        return $this->insert($insarr);
    }
    
    public function updateCookbook($cbid, $insarr) {
        $this->changeTable('tbl_recipes_cookbooks');
        $insarr['datecreated'] = $this->now();
        return $this->update('id', $cbid, $insarr);
    }
    
    public function getCookBook($cbid) {
        $this->changeTable('tbl_recipes_cookbooks');
        return $this->getAll("WHERE id = '$cbid'");
    }
    
    public function listCookBooks($userid) {
        $this->changeTable('tbl_recipes_cookbooks');
        if($userid == NULL){
            return $this->getAll();
        }
        else {
            return $this->getAll("WHERE userid = '$userid' ORDER BY favourite DESC");
        }
    }
    
    public function deleteCookBook($userid, $cbid) {
        $this->changeTable('tbl_recipes_cookbooks');
        if($userid == $this->objUser->userId()) {
            $this->delete('id', $cbid, 'tbl_recipes_cookbooks');
        }
    }
    
    public function countRecipesInBook($cbid, $userid) {
        $this->changeTable('tbl_recipes_cookbookrecipes');
        return $this->getRecordCount("WHERE userid = '$userid' and cookbookid = '$cbid'");
    }
    
    public function favCookBook($id, $userid) {
        $this->changeTable('tbl_recipes_cookbooks');
        // check user has a fav already
        $fbook = $this->getAll("WHERE userid = '$userid' AND favourite = '1'");
        if(empty($fbook)) {
            // user has no favourite yet, so set it
            $this->update('id', $id, array('favourite' => 1));
        }
        else {
            // unfavourite the old fav and favourite the new...
            $this->update('id', $fbook[0]['id'], array('favourite' => 0));
            $this->update('id', $id, array('favourite' => 1));
        }
    }
    
    public function getRecipesPerBook($cbid) {
        $this->changeTable('tbl_recipes_cookbookrecipes');
        return $this->getAll("WHERE cookbookid = '$cbid'");
    }
    
    private function changeTable($table) {
        parent::init($table);
    }   
}
?>
