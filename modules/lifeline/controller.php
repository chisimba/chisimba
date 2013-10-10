<?php
//ini_set('error_reporting', 'E_ALL & ~E_NOTICE');
/**
 * IM controller class
 *
 * Class to control the Life Line module
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
 * @category  chisimba
 * @package   lifeline
 * @author    Wesley Nitsckie <wesleynitsckie@gmail.com>
 * @copyright 2008 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11311 2008-11-04 12:29:43Z wnitsckie $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */

class lifeline extends controller {

   
    /**
     *
     * Standard constructor method to retrieve the action from the
     * querystring, and instantiate the user and lanaguage objects
     *
     */
    public function init() {
        try {
                       
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
		
    }

    /**
     * Standard dispatch method to handle adding and saving
     * of comments
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
			case 'main' :

            case NULL :
				$this->setLayoutTemplate('lifeline_layout_tpl.php');
				return 'main_tpl.php';
				break;
			
        }
    }
    
    public function requiresLogin()
    {
    	return False;
    }

   
}
