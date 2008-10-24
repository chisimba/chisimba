<?php

/**
 * My Contexts block
 * 
 * A block to show the list of contexts a user belongs to
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
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: block_context_class_inc.php 3591 2008-02-19 13:33:48Z tohir $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */


// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 * My Contexts block
 * 
 * A block to show the list of contexts a user belongs to
 * 
 * @category  Chisimba
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class block_mycontexts extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    private $objLanguage;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
        try {
            $this->objLanguage =  $this->getObject('language', 'language');
            $this->objUserContext = $this->getObject('usercontext', 'context');
            $this->objUser = $this->getObject('user', 'security');
            $this->objContext =  $this->getObject('dbcontext');
            $this->title = ucWords($this->objLanguage->code2Txt('phrase_mycourses', 'system', NULL, 'My [-contexts-]'));
            
            // HTML Elements
            $this->loadClass('form', 'htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            
        } catch (customException $e) {
            customException::cleanUp();
        }
    }
    
    /**
    * Standard block show method. 
    */
    public function show()
    {
        // Get all user contents
        $contexts = $this->objUserContext->getUserContext($this->objUser->userId());
        
        if (count($contexts) == 0) {
            return $this->objLanguage->code2Txt('mod_context_youdonotbelongtocontexts', 'context', NULL, 'You do not belong to any [-contexts-]');
        } else {
        
            $form = new form('joincontext', $this->uri(array('action'=>'joincontext'), 'context'));
            $dropdown = new dropdown ('contextcode');
            
            $contextArray = array();
            
            foreach ($contexts AS $contextCode)
            {
                $contextDetails = $this->objContext->getContextDetails($contextCode);
                
                $contextArray[$contextDetails['title']] = $contextCode;
            }
            
            ksort($contextArray);
            
            foreach ($contextArray as $title=>$code)
            {
                $dropdown->addOption($code, $title);
            }
            
            $dropdown->setSelected($this->objContext->getContextCode());
            
            $button = new button ('submitform', ucwords($this->objLanguage->code2Txt('mod_context_entercourse', 'context', NULL, 'Enter [-context-]')));
            $button->setToSubmit();
            
            $form->addToForm($dropdown->show().'<br />'.$button->show());
            
            return $form->show();
        }
    }
}
?>