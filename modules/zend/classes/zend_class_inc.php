<?php

/**
 * Zend Framework Initialiser
 * 
 * Initialises Zend Framework for use.
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
 * @package   zend
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: zend_class_inc.php 19114 2010-10-05 09:51:10Z charlvn $
 * @link      http://avoir.uwc.ac.za/
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
 * Zend Framework Initialiser
 * 
 * Initialises Zend Framework for use.
 * 
 * @category  Chisimba
 * @package   zend
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: zend_class_inc.php 19114 2010-10-05 09:51:10Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class zend extends object
{
    /**
     * Initialises Zend Framework for use.
     *
     * @access public
     */
    public function init()
    {
        ini_set('include_path', ini_get('include_path').':'.$this->getResourcePath(''));
        include $this->getResourcePath('Zend/Loader/Autoloader.php');
        Zend_Loader_Autoloader::getInstance();
    }
}
