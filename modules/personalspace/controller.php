<?php
// security check - must be included in all scripts
//if (!$GLOBALS['kewl_entry_point_run'])
//{
//	die("You cannot view this page directly");
//}
// end security check

/**
* Controller for Personal Space module - This module missing most of extra functionality (just basic page)
* @author PHP4 module by Jeremy O'Connor (ported to PHP 5 by Alastair Pursch)
* @copyright 2006 University of the Western Cape
* $Id: controller.php 24254 2012-05-06 22:35:35Z pwando $
*/
class personalspace extends controller
{
    /*User object for security
    @var object $objUser */
    public $objUser;
    //var $dbPersonalInfoImport;

    /**
    * The Init function
    */
    public function init()
    {
        
        // Create an instance of the module object
        $this->objModule = $this->getObject('modules', 'modulecatalogue');
        $this->objBlocks = $this->getObject('blocks', 'blocks');
        
        //Check if contentblocks is installed
        $this->cbExists = $this->objModule->checkIfRegistered("contentblocks");
        if ($this->cbExists) {
            $this->objBlocksContent = $this->getObject('dbcontentblocks', 'contentblocks');
        }
        
        $this->objContextBlocks = $this->getObject('dbcontextblocks', 'context');
        $this->objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->userId = $this->objUser->userId();
        
        $this->objPersonalSpaceBlocks = $this->getObject('dbpersonalspaceblocks');

        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        $this->objLog->log();
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
        
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery.livequery.js', 'jquery'));

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
    
    function __home()
    {
        $leftBlocks = $this->objPersonalSpaceBlocks->getUserBlocks($this->userId, 'left');
        $this->setVarByRef('leftBlocksStr', $leftBlocks);
        
        $rightBlocks = $this->objPersonalSpaceBlocks->getUserBlocks($this->userId, 'right');
        $this->setVarByRef('rightBlocksStr', $rightBlocks);
        
        $middleBlocks = $this->objPersonalSpaceBlocks->getUserBlocks($this->userId, 'middle');
        $this->setVarByRef('middleBlocksStr', $middleBlocks);
        
        $smallDynamicBlocks = $this->objDynamicBlocks->getSmallUserBlocks($this->userId);
        $this->setVarByRef('smallDynamicBlocks', $smallDynamicBlocks);
        
        $wideDynamicBlocks = $this->objDynamicBlocks->getWideUserBlocks($this->userId);
        $this->setVarByRef('wideDynamicBlocks', $wideDynamicBlocks);
        
        $objBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
        $smallBlocks = $objBlocks->getBlocks('normal', 'site|user');
        $this->setVarByRef('smallBlocks', $smallBlocks);
        
        $wideBlocks = $objBlocks->getBlocks('wide', 'site|user');
        $this->setVarByRef('wideBlocks', $wideBlocks);
        
        //Add content blocks if any
        $contentSmallBlocks = "";
        $contentWideBlocks = "";
        if ($this->cbExists) {
            $contentSmallBlocks = $this->objBlocksContent->getBlocksArr('content_text');
            $this->setVarByRef('contentSmallBlocks', $contentSmallBlocks);

            $contentWideBlocks = $this->objBlocksContent->getBlocksArr('content_widetext');
            $this->setVarByRef('contentWideBlocks', $contentWideBlocks);
        }
        
        return 'main_tpl.php';
    }
    
    /**
     * Method to render a block
     */
    protected function __renderblock()
    {
        $blockId = $this->getParam('blockid');
        $side = $this->getParam('side');
        
        $block = explode('|', $blockId);
        
        $this->setVar('pageSuppressSkin', TRUE);
        $this->setVar('pageSuppressContainer', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);
        
        $blockId = $side.'___'.str_replace('|', '___', $blockId);
        
        if ($block[0] == 'block') {
            $objBlocks = $this->getObject('blocks', 'blocks');
            $block = '<div id="'.$blockId.'" class="block highlightblock">'.$objBlocks->showBlock($block[1], $block[2], NULL, 20, TRUE, FALSE).'</div>';
            
            
            
            echo $block;
        } if ($block[0] == 'dynamicblock') {
            $block = '<div id="'.$blockId.'" class="block highlightblock">'.$this->objDynamicBlocks->showBlock($block[1]).'</div>';
            
            echo $block;
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
            $result = $this->objPersonalSpaceBlocks->addBlock($blockId, $side, $this->userId, $block[2]);
            
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
        
        $result = $this->objPersonalSpaceBlocks->removeBlock($blockId);
        
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
            $result = $this->objPersonalSpaceBlocks->moveBlockUp($blockId, $this->userId);
        } else {
            $result = $this->objPersonalSpaceBlocks->moveBlockDown($blockId, $this->userId);
        }
        
        if ($result) {
            echo 'ok';
        } else {
            echo 'notok';
        }
    }
}
?>
