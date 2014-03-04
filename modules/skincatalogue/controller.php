<?php

/**
 *
 * skincatalogue Test module
 *
 * A test module
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
 * @version
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


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
 * controller class for skincatalogue test module
 */
class skincatalogue extends controller {

    /**
     * @var object The language object
     * @access public
     */
    public $objLanguage;

    /**
     * The default contructor
     * 
     * @access public
     * @return  void
     */
    public function init() {
        //instantiate the language object
        $this->objLanguage = $this->getObject("language", "language");
        //setting the default template
        $this->setLayoutTemplate('layout_tpl.php');
    }

    /**
     *Methos returning the default template
     * 
     * @access private
     * @return template  The default template
     * @param void
     */
    public function __edit() {
        return "default_tpl.php";
    }

    /**
     *method to return template when method is view
     *
     * @access private
     * @return template The defult template
     * @param void
     */
public function __view(){
    return "default_tpl.php";
}

/**
     * process the controller logic
     * 
     * @access public
     * @return method The selected method
     * @param void
     */
    public function dispatch() {
        $action = $this->getParam("action","view");
        $method = $this->__getMethod($action);
        return $this->$method();
    }

    /**
     * generate an error on selection of an invalid action
     * 
     * @access private
     * @return string The template containing an error message
     * @param void
     */
    private function __actionError() {
        //return the template
        return 'dump_tpl.php';
    }

    /**
     * Check if the selected action is valid
     * 
     * @access public
     * @return BOOLEAN TRUE if method is valid FALSE if method is invalid
     * @param action The action selected by the user
     */
    public function __validAction(& $action) {
        //check if the action corresp;onds to a method within the class and return true on success and false otherwise
        if (method_exists($this, "__" . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * get the selected method and return the appropreate action
     * 
     * @access public
     * @return string The selected method or error message
     * @param action The action selected by the user
     */
    public function __getMethod(& $action) {
        //check if the action is valid
        if ($this->__validAction($action)) {
            //return the action
            return "__" . $action;
            //else return the errorr
        } else {
            return "__actionError";
        }
    }

}

?>
