<?php
/* -------------------- postlogin class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* Default class to handle what happens after the user logs in.
* The post login module can be set by changing the constant
* KEWL_POSTLOGIN_MODULE from 'postlogin' to the name of any other
* module.
*
* @author Derek Keats, Tohir Solomons
*/
class postlogin extends controller
{

    /**
    * init method to instantiate the class
    */
    function init()
    {
        // Create an instance of the module object
        $this->objModule = $this->getObject('modules', 'modulecatalogue');
        
        $this->objContextBlocks = $this->getObject('dbcontextblocks', 'context');
        $this->objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->contextCode = 'root';
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
    * Dispatch method to return the template populated with
    * the output
    */
    protected function __home()
    {
        $leftBlocks = $this->objContextBlocks->getContextBlocks($this->contextCode, 'left');
        $this->setVarByRef('leftBlocksStr', $leftBlocks);
        
        $rightBlocks = $this->objContextBlocks->getContextBlocks($this->contextCode, 'right');
        $this->setVarByRef('rightBlocksStr', $rightBlocks);
        
        $middleBlocks = $this->objContextBlocks->getContextBlocks($this->contextCode, 'middle');
        $this->setVarByRef('middleBlocksStr', $middleBlocks);
        
        $smallDynamicBlocks = $this->objDynamicBlocks->getSmallSiteBlocks();
        $this->setVarByRef('smallDynamicBlocks', $smallDynamicBlocks);
        
        $wideDynamicBlocks = $this->objDynamicBlocks->getWideSiteBlocks();
        $this->setVarByRef('wideDynamicBlocks', $wideDynamicBlocks);
        
        $objBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
        $smallBlocks = $objBlocks->getBlocks('normal', 'site|user');
        $this->setVarByRef('smallBlocks', $smallBlocks);
        
        $wideBlocks = $objBlocks->getBlocks('wide', 'site|user');
        $this->setVarByRef('wideBlocks', $wideBlocks);
        
        return 'main_tpl.php';
    }
    
    /**
     * Method to render a block
     */
    protected function __renderblock()
    {
        if ($this->objUser->isAdmin()) {
            $blockId = $this->getParam('blockid');
            $side = $this->getParam('side');
            
            $block = explode('|', $blockId);
            
            
            $blockId = $side.'___'.str_replace('|', '___', $blockId);
            
            if ($block[0] == 'block') {
                $objBlocks = $this->getObject('blocks', 'blocks');
                echo '<div id="'.$blockId.'" class="block">'.$objBlocks->showBlock($block[1], $block[2], NULL, 20, TRUE, FALSE).'</div>';
            } if ($block[0] == 'dynamicblock') {
                echo '<div id="'.$blockId.'" class="block">'.$this->objDynamicBlocks->showBlock($block[1]).'</div>';
            } else {
                echo '';
            }
        }
    }
    
    /**
     * Method to add a block
     */
    protected function __addblock()
    {
        if ($this->objUser->isAdmin()) {
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
    }
    
    /**
     * Method to remove a context block
     */
    protected function __removeblock()
    {
        if ($this->objUser->isAdmin()) {
            $blockId = $this->getParam('blockid');
            
            $result = $this->objContextBlocks->removeBlock($blockId);
            
            if ($result) {
                echo 'ok';
            } else {
                echo 'notok';
            }
        }
    }
    
    /**
     * Method to move a context block
     */
    protected function __moveblock()
    {
        if ($this->objUser->isAdmin()) {
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
    }
}
?>