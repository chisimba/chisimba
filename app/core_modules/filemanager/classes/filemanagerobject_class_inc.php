<?php

/**
 * Class to overwrite certain functions in the object class
 *
 * This class overwrites the uri function of the object class to have automatic
 * inclusion for the mode and restriction parameters
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id: previewfolder_class_inc.php 2839 2007-08-06 13:25:48Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


/**
 * Class to overwrite certain functions in the object class
 * 
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
class filemanagerobject extends dbTable // which extends object
{

    /**
    * Constructor
    */
    public function init()
    {  }
    
    /**
     * Method to override the uri function to include automatic inclusion
     * of mode and restriction
    *
    * @access  public
    * @param   array  $params         Associative array of parameter values
    * @param   string $module         Name of module to point to (blank for core actions)
    * @param   string $mode           The URI mode to use, must be one of 'push', 'pop', or 'preserve'
    * @param   string $omitServerName flag to produce relative URLs
    * @param   bool   $javascriptCompatibility flag to produce javascript compatible URLs
    * @param   bool   $omitExtraParams Remove extra flags for mode and restrictions
    * @returns string $uri the URL
    */
    public function uri($params = array(), $module = '', $mode = '', $omitServerName=FALSE, $javascriptCompatibility = FALSE, $omitExtraParams=FALSE)
    {
        if ($params == NULL) {
            $params = array();
        }
        
        if (is_array($params) && !array_key_exists('mode', $params) && $this->getParam('mode') != '') {
            $params['mode'] = $this->getParam('mode');
        }
        
        if (is_array($params) && !array_key_exists('restriction', $params) && $this->getParam('restriction') != '') {
            $params['restriction'] = $this->getParam('restriction');
        }
        
        if (is_array($params) && !array_key_exists('name', $params) && $this->getParam('name') != '') {
            $params['name'] = $this->getParam('name');
        }
        
        if (is_array($params) && !array_key_exists('context', $params) && $this->getParam('context') != '') {
            $params['context'] = $this->getParam('context');
        }
        
        if (is_array($params) && !array_key_exists('workgroup', $params) && $this->getParam('workgroup') != '') {
            $params['workgroup'] = $this->getParam('workgroup');
        }
        
        if ($omitExtraParams) {
            unset($params['mode']);
            unset($params['restriction']);
            unset($params['name']);
            unset($params['context']);
            unset($params['workgroup']);
        }
        
        return parent::uri($params, $module, $mode, $omitServerName, $javascriptCompatibility);
    }
    
    
    
    

}

?>