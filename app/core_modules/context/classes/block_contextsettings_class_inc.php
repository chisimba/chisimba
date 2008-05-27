<?php

/**
* Context Settings Block
* 
* This class generates a block to show the settings and status of the current context
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
* Context Settings Block
* 
* This class generates a block to show the settings and status of the current context
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
class block_contextsettings extends object
{
   
   /**
   * @var object $objContext : The Context Object
   */
   public $objContext;
   
   /**
   * @var object $objLanguage : The Language Object
   */
   public $objLanguage;


   /**
   *Initialize by send the table name to be accessed
   */
   public function init()
   {
        $this->objContext = $this->getObject('dbcontext');
        $this->contextCode = $this->objContext->getContextCode();
        
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
        
        $this->title = ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings'));
   }
   
   /**
    * Method to render the block
    */
   public function show()
   {
        // Check Context Code is Valid
        if ($this->contextCode == 'root' || $this->contextCode == '') {
            return '';
        }
        
        // Get Context Details
        $contextDetails = $this->objContext->getContextDetails($this->contextCode);
        
        // Check that Context Exists
        if ($contextDetails == FALSE) {
            return '';
        }
        
        // Prepare Block
        $objContextImage = $this->getObject('contextimage');
        $objIcon = $this->newObject('geticon', 'htmlelements');
        
        $image = $objContextImage->getContextImage($this->contextCode);
        
        if ($image == FALSE) {
            $objIcon->setIcon('imagepreview');
            $image = $objIcon->show();
        } else {
            $image = '<img src="'.$image.'" />';
        }
        
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $table->addCell($image, 120);
        
        
        $str = '<p><strong>'.ucwords($this->objLanguage->code2Txt('mod_context_contexttitle', 'context', NULL, '[-context-] Title')).'</strong>: '.$contextDetails['title'].'</p>';
        $str .= '<p><strong>'.ucwords($this->objLanguage->code2Txt('mod_context_contextstatus', 'context', NULL, '[-context-] status')).'</strong>: '.$contextDetails['status'].'</p>';
        $str .= '<p><strong>'.$this->objLanguage->languageText('mod_context_accessettings', 'context', 'Access Settings').'</strong>: '.$contextDetails['access'].'</p>';
        
        
        $table->addCell($str);
        
        $table->endRow();
        
        $link = new link ($this->uri(array('action'=>'updatesettings')));
        $link->link = ucwords($this->objLanguage->code2Txt('mod_context_changecontextsettings', 'context', NULL, 'Change [-context-] Settings'));
        
        return $table->show().'<p>'.$link->show().'</p>';
   }





}
?>