<?php

/**
 * Context controller
 * 
 * Controller class for the context in Chisimba
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
 * Context controller
 * 
 * Controller class for the context in Chisimba
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
class context extends controller
{
    /**
    * @var object $objContext
    */
    public $objContext;
    
    /**
    * @var string $contextCode Current Context Code
    */
    private $contextCode;
    
    
    /**
     * Constructor
     */
    public function init()
    {
        try {
            $this->objContext = $this->getObject('dbcontext');
            
            
            $this->contextCode = $this->objContext->getContextCode();
            $this->setVarByRef('contextCode', $this->contextCode);
            
            $this->contextTitle = $this->objContext->getTitle();
            $this->setVarByRef('contextTitle', $this->contextTitle);
            
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            
            $this->objContextBlocks = $this->getObject('dbcontextblocks');
            $this->objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
        }
        catch (customException $e)
        {
            customException::cleanUp();
        }
    }
    
    /**
     * Method to turn off login requirement for certain actions
     */
    public function requiresLogin($action) // overides that in parent class
    {
        $requiresLogin = array('controlpanel', 'manageplugins', 'updateplugins', 'renderblock', 'addblock', 'removeblock', 'moveblock', 'updatesettings', 'updatecontext');
       
        if (in_array($action, $requiresLogin)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * Standard Dispatch Function for Controller
    *
    * @access public
    * @param string $action Action being run
    * @return string Filename of template to be displayed
    */
    public function dispatch($action)
    {
        // Method to set the layout template for the given action
        $this->setLayoutTemplate('contextlayout_tpl.php');
        
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery/jquery.livequery.js', 'htmlelements'));

        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        $method = $this->getMethod($action);
        /*
        * Return the template determined by the method resulting
        * from action
        */
        return $this->$method();
    }
    
    /**
    *
    * Method to convert the action parameter into the name of
    * a method of this class.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return string the name of the method
    *
    */
    protected function getMethod(& $action)
    {
        if ($this->validAction($action)) {
            return '__'.$action;
        } else {
            return '__home';
        }
    }

    /**
    *
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    *
    */
    protected function validAction(& $action)
    {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Method to show the context home page
     *
     */
    protected function __home()
    {
        if ($this->contextCode == 'root') {
            return $this->nextAction('join');
        }
        
        $this->_preventRootAccess();
        
        $this->setLayoutTemplate(NULL);
        
        $rightBlocks = $this->objContextBlocks->getContextBlocks($this->contextCode, 'right');
        $this->setVarByRef('rightBlocksStr', $rightBlocks);
        
        $middleBlocks = $this->objContextBlocks->getContextBlocks($this->contextCode, 'middle');
        $this->setVarByRef('middleBlocksStr', $middleBlocks);
        
        $allContextBlocks = $this->objContextBlocks->getContextBlocksArray($this->contextCode);
        $this->setVarByRef('allContextBlocks', $allContextBlocks);
        
        $smallDynamicBlocks = $this->objDynamicBlocks->getSmallContextBlocks($this->contextCode);
        $this->setVarByRef('smallDynamicBlocks', $smallDynamicBlocks);
        
        $wideDynamicBlocks = $this->objDynamicBlocks->getWideContextBlocks($this->contextCode);
        $this->setVarByRef('wideDynamicBlocks', $wideDynamicBlocks);
        
        $objBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
        $smallBlocks = $objBlocks->getBlocks('normal', 'context|site');
        $this->setVarByRef('smallBlocks', $smallBlocks);
        
        $wideBlocks = $objBlocks->getBlocks('wide', 'context|site');
        $this->setVarByRef('wideBlocks', $wideBlocks);
        
        return 'context_home_tpl.php';
    }
    
    /**
     * Method to show a list of contexts user can join
     */
    protected function __join()
    {
        $this->setLayoutTemplate(NULL);
        return 'needtojoin_tpl.php';
    }
    
    /**
     * Method to join a context
     */
    protected function __joincontext()
    {
        $contextCode = $this->getParam('contextcode');
        
        if ($contextCode == '') {
            return $this->nextAction('join', array('error'=>'nocontext'));
        } else {
            if ($this->objContext->joinContext($contextCode)) {
                return $this->nextAction('home');
            } else {
                return $this->nextAction('join', array('error'=>'unabletoenter'));
            }
        }
    }
    
    /**
     * Method to join a context
     */
    protected function __gotomodule()
    {
        $contextCode = $this->getParam('contextcode');
        $module = $this->getParam('moduleid', 'context');
        
        if ($contextCode == '') {
            return $this->nextAction('join', array('error'=>'nocontext'));
        } else {
            if ($this->objContext->joinContext($contextCode)) {
                return $this->nextAction(NULL, NULL, $module);
            } else {
                return $this->nextAction('join', array('error'=>'unabletoenter'));
            }
        }
    }
    
    /**
     * Method to prevent access to certain portions without being logged into a context
     */
    private function _preventRootAccess()
    {
        if ($this->contextCode == 'root' || $this->contextCode == '') {
            return $this->nextAction('error', array('error'=>'cantaccessrootcontrolpanel'));
        }
    }
    
    /**
     * Method to show the context control panel
     */
    protected function __controlpanel()
    {
        $this->_preventRootAccess();
        
        $this->setLayoutTemplate('contextlayout_tpl.php');
        
        return 'controlpanel_tpl.php';
    }
    
    /**
     * Method to show the form for users to add/remove context plugins
     */
    protected function __manageplugins()
    {
        $this->_preventRootAccess();
        
        $objContextModules = $this->getObject('dbcontextmodules');
        $objModules = $this->getObject('modules', 'modulecatalogue');
        
        $contextModules = $objContextModules->getContextModules($this->contextCode);
        $plugins = $objModules->getListContextPlugins();
        
        $this->setVarByRef('contextModules', $contextModules);
        $this->setVarByRef('plugins', $plugins);
        
        return 'manageplugins_tpl.php';
    }
    
    /**
     * Method to update the list of context plugins
     */
    protected function __updateplugins()
    {
        $this->_preventRootAccess();
        
        $plugins = $this->getParam('plugins');
        
        $objContextModules = $this->getObject('dbcontextmodules');
        $objContextModules->deleteModulesForContext($this->contextCode);
        
        
        if (is_array($plugins) && count($plugins) > 0) {
            foreach ($plugins as $plugin)
            {
                $objContextModules->addModule($this->contextCode, $plugin);
            }
        }
        
        return $this->nextAction('controlpanel', array('message' => 'pluginsupdated'));
    }
    
    /**
     * Method to display error messages
     */
    protected function __error()
    {
        //echo 'error';
        
        return $this->nextAction(NULL, NULL, '_default');
    }
    
    /**
     * Method to render a block
     */
    protected function __renderblock()
    {
        $blockId = $this->getParam('blockid');
        $side = $this->getParam('side');
        
        $block = explode('|', $blockId);
        
        
        $blockId = $side.'___'.str_replace('|', '___', $blockId);
        
        if ($block[0] == 'block') {
            $objBlocks = $this->getObject('blocks', 'blocks');
            echo '<div id="'.$blockId.'" class="block highlightblock">'.$objBlocks->showBlock($block[1], $block[2], NULL, 20, TRUE, FALSE).'</div>';
        } if ($block[0] == 'dynamicblock') {
            echo '<div id="'.$blockId.'" class="block highlightblock">'.$this->objDynamicBlocks->showBlock($block[1]).'</div>';
        } else {
            echo '';
        }
    }
    
    /**
     * Method to add a block
     */
    protected function __addblock()
    {
        $blockId = $this->getParam('blockid');
        $side = $this->getParam('side');
        
        $block = explode('|', $blockId);
        
        if ($block[0] == 'block' || $block[0] == 'dynamicblock') {
            // Add Block
            $result = $this->objContextBlocks->addBlock($blockId, $side, $this->contextCode, $block[2]);
            
            if ($result == FALSE) {
                echo '';
            } else {
                echo $result;
            }
        } else {
            echo '';
        }
    }
    
    /**
     * Method to remove a context block
     */
    protected function __removeblock()
    {
        $blockId = $this->getParam('blockid');
        
        
        $result = $this->objContextBlocks->removeBlock($blockId);
        
        if ($result) {
            echo 'ok';
        } else {
            echo 'notok';
        }
    }
    
    /**
     * Method to move a context block
     */
    protected function __moveblock()
    {
        $blockId = $this->getParam('blockid');
        $direction = $this->getParam('direction');
        
        if ($direction == 'up') {
            $result = $this->objContextBlocks->moveBlockUp($blockId, $this->contextCode);
        } else {
            $result = $this->objContextBlocks->moveBlockDown($blockId, $this->contextCode);
        }
        
        if ($result) {
            echo 'ok';
        } else {
            echo 'notok';
        }
    }
    
    /**
     * Method to show a form to update context settings
     */
    protected function __updatesettings()
    {
        $context = $this->objContext->getContextDetails($this->contextCode);
        $objContextForms = $this->getObject('contextforms');
        
        $form = $objContextForms->editContextForm($context);
        $this->setVarByRef('form', $form);
        
        return 'editcontextsettings_tpl.php';
        
    }
    
    /**
     * Method to Update a Context Settings
     */
    protected function __updatecontext()
    {
        $contextCode = $this->getParam('contextcode');
        $title = $this->getParam('title');
        $status = $this->getParam('status');
        $access = $this->getParam('access');
        $about = $this->getParam('about');
        $image = $this->getParam('imageselect');
        
        if ($contextCode == $this->contextCode && $title != '') {
            $result = $this->objContext->updateContext($contextCode, $title, $status, $access, $about);
            
            if ($image != '') {
                $objContextImage = $this->getObject('contextimage', 'context');
                $objContextImage->setContextImage($contextCode, $image);
            }
            
            return $this->nextAction('controlpanel');
            
        } else {
            return $this->nextAction('updatesettings', array('error'=>'inccompletefields'));
        }
    }
    
    /**
     * Add Context Search
     */
    protected function __search()
    {
        $search = $this->getParam('search');
        
        $objSearchResults = $this->getObject('searchresults', 'search');
        $searchResults = $objSearchResults->displaySearchResults($search, NULL, $this->contextCode);
        
        $this->setVarByRef('searchResults', $searchResults);
        $this->setVarByRef('searchText', $search);
        
        return 'searchresults_tpl.php';
    }
    
    function __contextcreatedmessage()
    {
        echo '<h3>'.$this->objLanguage->code2Txt('mod_context_congratscontextcreated', 'context', NULL, 'Congratulations! Your [-context-] has been created').'.</h3>
        <p>'.$this->objLanguage->code2Txt('mod_context_contextcreatedmessage1', 'context', NULL, 'This is the home page of your [-context-] You can modify the contents of the page, by clicking "Turn Editing On"').'.
        '.$this->objLanguage->languageText('mod_context_contextcreatedmessage2', 'context', 'This will allow you to add different types of content blocks to this page').'.</p>
        <p>'.$this->objLanguage->code2Txt('mod_context_contextcreatedmessage3', 'context', NULL, 'To add [-readonlys-] to your [-context-], or to add/remove [-context-] plugins, go to the [-context-] control panel').'.</p>
        ';
        
    }
    
    
    function __ajaxgetcontexts()
    {
        $letter = $this->getParam('letter');
        
        $contexts = $this->objContext->getContextStartingWith($letter);
        
        if (count($contexts) == 0) {
            
        } else {
            $objDisplayContext = $this->getObject('displaycontext', 'context');
        
            foreach ($contexts as $context)
            {
                echo $objDisplayContext->formatContextDisplayBlock($context, FALSE, FALSE).'<br />';
            }
        }
    }
    
    
    function __leavecontext()
    {
        $this->objContext->leaveContext();
        return $this->nextAction(NULL, NULL, '_default');
    }
    
}


?>