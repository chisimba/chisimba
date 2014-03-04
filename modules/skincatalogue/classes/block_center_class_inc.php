<?php

/**
 *
 * skincatalogue Test module
 *
 * A test module for customizing testskin1
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
 * 59 Temple Place ­ Suite 330, Boston, MA  02111­1307, USA.
 *
 * @version     0.051
 * @package    skincatalogue
 * @author     Monwabisi Sifumba <wsifumba@gmail.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl­2.0.txt The GNU General Public  
  License
 * @link       http://www.chisimba.com
 * 
 */
// security check ­ must be included in all scripts
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
include "catalogueform_class_inc.php";

/**
 * The center block class
 */
class block_center extends object {

    /**
     * @access public
     * @var object The language object
     */
    public $objLanguage;

    /**
     * @access public
     * @var string The center block title
     */
    public $title;

    /**
     * The default contructor
     * 
     * @access public
     * @return void
     * @param void
     */
    public function init() {
        //instantiate the language object
        $this->objLanguage = $this->getObject("language", "language");
        //get the title value
        $this->title = $this->objLanguage->languageText("mod_centerblock_title", "skincatalogue");
    }

    /**
     * Method that returns the block
     *
     * @access public
     * @return string The form object converted to a string
     * @param void
     */
    public function show() {
        catalogueform::BuildForm();
        if ($this->getParam("action") == "edit") {
            return catalogueform::__edit();
        } elseif ($this->getParam("action") == "view" || $this->getParam("action") == null) {
            return catalogueform::__view();
        }
    }

}

?>