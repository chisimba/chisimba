<?php

/**
 * Creative Commons Controller class
 * 
 * Controller class for the Chisimba Creative Commons interface module
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
 * @package   creativecommons
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

/**
 * Controller for the Creative Commons Module
 * @author Tohir Solomons
 */
class creativecommons extends controller
{
    /**
     * Constructor
     */
    public function init()
    {
        $this->objCC = $this->getObject('dbcreativecommons');
    }
    
    /**
     * Dispatch Method
     *
     * @param  string $action Action to be taken
     * @return string
     */
    public function dispatch($action)
    {
        switch ($action)
        {
            case 'selecttest': // Demo the License Chooser
                return 'template_selectdemo.php';
            default:
                $licences = $this->objCC->getAll();
                $this->setVarByRef('licences', $licences);
                return 'list_licences.php';
        }
        
    }
    
    /**
    * Method to turn off login requirement for this module
    */
    public function requiresLogin()
    {
        return FALSE;
    }
}

?>