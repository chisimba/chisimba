<?php

/**
 * Class for building the catalogue navigation for module catalogue.
 * The class makes use of nodes in an array to keep track of the different categories
 * and the show() function renders the array as a navigation menu.
 *
 * The class builds a css style navigation menu
 *
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
 * @package   modulecatalogue
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */


// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class for building the catalogue navigation for module catalogue.
 * The class makes use of nodes in an array to keep track of the different categories
 * and the shoe() function renders the array as a navigation menu.
 *
 * The class builds a css style navigation menu
 *
 * @category  Chisimba
 * @package   modulecatalogue
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */

class catalogue extends object {

	/**
	 * Nodes of navigation list
	 *
	 * @var array $nodes
	 */
	protected $nodes = array();

	/**
    * Method to construct the class.
    */
    public function init()
    {
   		try {
    		$this->nodes = array();
    	} catch (customException $e) {
			echo customException::cleanUp($e);
    		exit();

		}
    }

    /**
     * Method to add content to the navigation list
     *
     * @param array $nodes An array of nodes to be added as categories
     */
    public function addNodes($nodes) {
    	try {
    		if (is_array($nodes)) {
    			foreach($nodes as $node) {
    				$this->addNodes($node);
    			}
    		} else {
    			array_push($this->nodes,$nodes);
    		}
    	} catch (customException $e) {
			echo customException::cleanUp($e);
    		exit();
        }
    }

    /**
     * Method to reset the nodelist
     */
    public function clearNodes() {
    	try {
    		$this->nodes = array();
    	} catch (customException $e) {
			echo customException::cleanUp($e);
    		exit();
		}
    }

    /**
     * Method to render the navigation menu
     *
     * @param  string $activeNode The node which should appear
     *         to be currently selected
     * @return string The rendered navigation menu
     */
    public function show($activeNode = null) {
    	try {
    		$un = $this->getParam('uninstall');
    		$str = '<ul id="nav-secondary">';
    		$cssClass = '';
    		//loop through the nodes
    		foreach($this->nodes as $node) {
				if(strtolower($node) == strtolower($activeNode)) {
					$cssClass = ' class="active" ';
				}
				$name = ucwords($node);
				if ($un) {
					$str .="<li $cssClass><a href='{$this->uri(array('action'=>'list','cat'=>$node,'uninstall'=>'1'),'modulecatalogue')}'>{$name}</a></li>";
				} else {
					$str .="<li $cssClass><a href='{$this->uri(array('action'=>'list','cat'=>$node),'modulecatalogue')}'>{$name}</a></li>";
				}
				//reset the cssclass
				$cssClass = '';
    		}
    		$str .='</ul>';
    		return $str;
    	} catch (customException $e) {
			echo customException::cleanUp($e);
    		exit();
		}
    }
}
?>