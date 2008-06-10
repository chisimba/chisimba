<?php

/**
* Context Modules/Plugins Block
* 
* This class generates a block to show the modules/plugins the current context,
* as well as an administrative link to update it
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
* Context Modules/Plugins Block
* 
* This class generates a block to show the modules/plugins the current context,
* as well as an administrative link to update it
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
class block_contextmodules extends object
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
    * @var object $objContextModules : The Context Modules Object
    */
    public $objContextModules;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objContext = $this->getObject('dbcontext');
        $this->contextCode = $this->objContext->getContextCode();
        
        $this->loadClass('link', 'htmlelements');
        $this->objContextModules = $this->getObject('dbcontextmodules');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        
        $this->objLanguage = $this->getObject('language', 'language');
        
        $this->title = ucwords($this->objLanguage->code2Txt('mod_context_contextplugins', 'context', NULL, '[-context-] Plugins'));
    }
   
    /**
     * Method to render the block
     */
    public function show()
    {
        if ($this->contextCode == 'root' || $this->contextCode == '') {
            return '';
        }
        
        $contextDetails = $this->objContext->getContextDetails($this->contextCode);
        
        if ($contextDetails == FALSE) {
            return '';
        }
        
        $numModules = count($this->objModules->getContextPlugins());
        
        $modules = $this->objContextModules->getContextModules($this->contextCode);
        
        if (count($modules) == 0) {
            $str = '<div class="noRecordsMessage">'.$this->objLanguage->code2Txt('mod_context_contexthasnoplugins', 'context', NULL, 'This [-context-] does not have any plugins enabled').'</div>';
        } else {
            
            $table = $this->newObject('htmltable', 'htmlelements');
            
            $objIcon = $this->newObject('geticon', 'htmlelements');
            
            $counter = 0;
            
            foreach ($modules as $module)
            {
                $moduleInfo = $this->objModules->getModuleInfo($module);
                
                if ($moduleInfo['isreg']) {
                    
                    if ($counter % 2 == 0) {
                        $table->startRow();
                    }
                    
                    
                    $objIcon->setModuleIcon($module);
                    $moduleTitle = $this->objModules->getModuleTitle($module);
                    $objIcon->alt = $moduleTitle;
                    $objIcon->title = $moduleTitle;
                    
                    $link = new link ($this->uri(NULL, $module));
                    $link->link = $objIcon->show();
                    
                    
                    $table->addCell($link->show(), 25);
                    
                    $link->link = $moduleTitle;
                    $table->addCell($link->show().'<br /><br />', '45%');
                    
                    
                    $counter++;
                    
                    if ($counter % 2 == 0) {
                        $table->endRow();
                    }
                }
            }
            
            if ($counter % 2 == 1) {
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->endRow();
            }
            
            $str = $table->show();
        }
        
        $str .= '<p align="right">'.$this->objLanguage->languageText('mod_context_unusedplugins', 'context', 'Unused Plugins').': '.($numModules-count($modules)).'</p>';
        
        
        $link = new link($this->uri(array('action'=>'manageplugins')));
        $link->link = $this->objLanguage->languageText('mod_context_manageplugins', 'context', 'Manage Plugins');
        
        return $str.'<p>'.$link->show().'</p>';
    }





}
?>