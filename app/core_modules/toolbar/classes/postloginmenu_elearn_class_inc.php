<?php

/**
 * Class to generate the elearning postlogin side menu
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
 * @package   toolbar
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id: checkoverwrite_class_inc.php,v 1.4 2007-08-06 13:11:19 paulscott Exp $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */

/**
 * Class to generate the elearning postlogin side menu
 * 
 * @category  Chisimba
 * @package   toolbar
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       
 */

class postloginmenu_elearn extends object
{
    /**
    * Method to construct the class
    */
    function init()
    {
        $this->loadClass('link','htmlelements');
        $this->loadClass('htmlheading','htmlelements');
        $this->objMenu = $this->getObject('dbmenu');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->sideMenu = $this->getObject('sidemenu');
        $this->objUser = $this->getObject('user', 'security');
    }
    
    /**
     * Method to generate the postlogin side menu
     * @return string Generated menu
     */
    public function show()
    {
        // Get menu items
        $options = $this->objMenu->getSideMenus('elearnpostlogin');
        
        $objUserPic = $this->getObject('imageupload', 'useradmin');
        
        $header = new htmlHeading();
        $header->type = 2;
        $header->str = $this->objUser->fullName();
        
        $str = '';//$header->show();
        
        $str .= '<p align="center"><img src="'.$objUserPic->userpicture($this->objUser->userId() ).'" alt="User Image" style="margin-bottom: 2px;" /></p>';
        
        //$str .= '<br />';
        // First add user pic
        //$str = $this->sideMenu->userDetails();
        
        $objBlock = $this->getObject('blocks', 'blocks');
        $str .= $objBlock->showBlock('mycontexts', 'context', NULL, 20, TRUE, FALSE);
        
        // If menu items exist
        if (count($options) > 0) {
            
            // Prepare items - will be sorted alphabetically
            $menuItems = array();
            
            // Loop through items
            foreach ($options as $option)
            {
                // Get proper name of module
                $name = ucwords($this->objLanguage->code2Txt('mod_'.$option['module'].'_name', $option['module']));
                
                // Create link
                $link = new link ($this->uri(NULL, $option['module']));
                $link->link = $name;
                
                // add to array
                $menuItems[$name] = $link->show();
                
            }
            
            // Sort alphabetically
            ksort($menuItems);
            
            // Generated proper menu
            $str .= '<ul id="nav-secondary">';
            
            foreach ($menuItems as $item=>$link)
            {
                $str .= '<li>'.$link.'</li>';
            }
            
            $str .= '</ul><br />';
            
            
        }
        
        return $str;
    }

}
?>