<?php

/**
* Display Context
* 
* Class to render context lists
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
* @package   context
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2008 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id$
* @link      http://avoir.uwc.ac.za
* @see       core
*/
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!
/**
* Description for $GLOBALS
* @global entry point $GLOBALS['kewl_entry_point_run']
* @name   $kewl_entry_point_run
*/
$GLOBALS['kewl_entry_point_run']) {
die("You cannot view this page directly");
}
// end security check


/**
* Display Context
* 
* Class to render context lists
* 
* @category  Chisimba
* @package   context
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2008 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   Release: @package_version@
* @link      http://avoir.uwc.ac.za
* @see       core
*/
class displaycontext extends object
{
    /**
    * @var object $objUser : The user Object
    */
    public $objUser;
 
 
    /**
    *Initialize by send the table name to be accessed
    */
    public function init()
    {
        $this->objContextImage = $this->getObject('contextimage');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     * Method to display the information of a context in a block
     * @param array $context
     * @return string
     */
    public function formatContextDisplayBlock($context)
    {
        $heading = new htmlheading();
        $heading->type = 3;
        
        $link = new link ($this->uri(array('action'=>'joincontext', 'contextcode'=>$context['contextcode'])));
        $link->link = $context['title'];
        
        // Add Permissions
        
        
        $heading->str = $link->show();//.' ('.($context['contextcode']).')';
        
        $str = $heading->show();
        
        // Get Context Image
        $contextImage = $this->objContextImage->getContextImage($context['contextcode']);
        
        // Show if it has an image
        if ($contextImage != FALSE) {
            // And the about field DOES NOT have any images, objects (flash) or iframes
            if (!preg_match('/<img|<embed|<object|<iframe/', $context['about'])) {
                $str .= '<div style="float:left; width: 100px; text-align:center; border: 1px solid #555; padding: 10px; margin-left: 5px; margin-right: 5px;"><img src="'.$contextImage.'" /></div>';
            }
        }
        
        $str .= $context['about'];
        
        
        
        switch (strtolower($context['access']))
        {
            case 'public': $access = $this->objLanguage->code2Txt('mod_context_publiccontextexplanation', 'context', NULL, 'This is an open [-context-] that any user may enter'); break;
            case 'open': $access = $this->objLanguage->code2Txt('mod_context_opencontextexplanation', 'context', NULL, 'This is an open [-context-] that any logged-in user may enter'); break;
            default: $access = $this->objLanguage->code2Txt('mod_context_privatecontextexplanation', 'context', NULL, 'This is a closed [-context-] only accessible to members'); break;            
        }
        
        $str .= '<p><strong>'.ucwords($this->objLanguage->languageText('mod_context_accessettings', 'context', 'Access Settings')).'</strong>: '.$access.'</p>';
        //$str .= '<p><strong>Contact</strong>: '.$access.'</p>';      
        
        return $str;
    }

}
?>