<?php
/**
 *
 * Get a local sound file
 *
 * Get a sound file by using the localsounds class
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
 * Get a local sound file
 *
 * Get a sound file by using the localsounds class
 *
 * @category  Chisimba
 * @author    Derek Keats derek@localhost.local
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_localsound extends object
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
     *
     * @var string Object $objDb String for the databse object
     * @access public
     *
     */
    public $objDb;
    
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        // Check if is should display
        $this->blockType="";
        $action = $this->getParam('action', NULL);
        if ($action != 'showsp') {
            $this->blockType="invisible";
        } else {
            // First check if there are sounds, if not, make it invisible
            $id = $this->getParam('id', FALSE);
            if ($id) {
                $this->objDb = $this->getObject('dbspecies', 'species');
                $fullName = $this->objDb->getFullName($id);
                $this->objSnd = $this->getObject('localsounds', 'species');
                if ($this->objSnd->soundsExist($fullName)) {
                    $this->blockType="";
                } else {
                    $this->blockType="invisible";
                }
            }
            $sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $findSounds = strtolower(
              $sysConfig->getValue('species_findsounds', 'species')
            );
            if ($findSounds != 'true') {
                $this->blockType="invisible";
            }
        }
        
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText(
          "mod_species_locsound", "species",
          "Sound bytes (local)");
    }
    
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        if ($this->blockType != "invisible") {
            $action = $this->getParam('action', NULL);
            switch ($action) {
                case "showsp":
                    $id = $this->getParam('id', FALSE);
                    if ($id) {
                        $objDb = $this->getObject('dbspecies', 'species');
                        $fullName = $objDb->getFullName($id);
                        $objEol = $this->getObject('localsounds', 'species');
                        $ret = $objEol->getSounds($fullName);
                    } else {
                        $ret = NULL;
                    }
                    break;

                default:
                    $ret=NULL;
                    break;
            }
            return $ret;
        }
    }
}
?>