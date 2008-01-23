<?php

/**
* Context Statistics Block
* 
* This class generates a block to show usage statistics the current context
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
* Context Statistics Block
* 
* This class generates a block to show usage statistics the current context
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
class block_contextstats extends object
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
    * Constructor
    */
    public function init()
    {
        $this->objContext = $this->getObject('dbcontext');
        $this->contextCode = $this->objContext->getContextCode();
        
        $this->objLanguage = $this->getObject('language', 'language');
        
        $this->title = ucwords($this->objLanguage->code2Txt('mod_context_contextstatistics', 'context', NULL, '[-context-] Statistics'));
    }
   
    /**
     * Method to show the block
     */
    public function show()
    {
        $objFlashGraph = $this->getObject('flashgraph', 'utilities');
        $objFlashGraph->dataSource = $this->uri(array('action'=>'modulestats'), 'logger');
        $objFlashGraph->width = '90%';
        $objFlashGraph->height = 400;
            
        return $objFlashGraph->show().'<br />Note: Stats are currently incorrect - this is just a demo!';
    }

}
?>