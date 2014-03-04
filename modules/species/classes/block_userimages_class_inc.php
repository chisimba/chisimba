<?php
/**
 *
 * A userimages block for Species.
 *
 * A userimages block for Species. Show images loaded by approved users 
 * identified by CONFIG: species_userlist.
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
 * @version    0.001
 * @package    species
 * @author     Derek Keats derek@localhost.local
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
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
 * 
 * A userimages block for Species.
 *
 * A userimages block for Species. Show images loaded by approved users 
 * identified by CONFIG: species_userlist.
 *
 * @category  Chisimba
 * @author    Derek Keats derek@localhost.local
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_userimages extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->blockType="";
        // Check if is should display
        $action = $this->getParam('action', NULL);
        if ($action != 'showsp') {
            $this->blockType="invisible";
        }
        // Get an instance of the languate object
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText(
                "mod_species_userspecies", "species",
                "Contributed images");
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        if (!$this->blockType=="invisible") {
            $action = $this->getParam('action', NULL);
            switch ($action) {
                case "showsp":
                    $id = $this->getParam('id', FALSE);
                    if ($id) {
                        $objImgs = $this->getObject('userimgs', 'species');
                        $ret = $objImgs->showSpecies($id);
                    }
                    break;
                default:
                    $ret = NULL;

            }
            return $ret;
        }
    }
}
?>