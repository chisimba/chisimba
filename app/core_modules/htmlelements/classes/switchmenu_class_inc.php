<?php
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
*
* Class to Prepare and Show a Switch Menu
*
* A Switch Menu is a series of blocks with a title and text.
* The switch menu shows one block at a time.
*
* Adapted From: http://www.dynamicdrive.com/dynamicindex1/switchmenu.htm
*
* Example:
*
* $switchmenu = $this->newObject('switchmenu', 'blocks');
* $switchmenu->addBlock('Title 1', 'Block Text 1 <br /> Block Text 1 <br /> Block Text 1');
* $switchmenu->addBlock('Title 2', 'Block Text 2 <br /> Block Text 2 <br /> Block Text 2', 'confirm'); // Adds a CSS Style
* $switchmenu->addBlock('Title 3', 'Block Text 3 <br /> Block Text 3 <br /> Block Text 3');
*
* echo $switchmenu->show();
*
* @author Tohir Solomons
*
*/
class switchmenu extends object
{
    /**
    * @var array $switchMenuArray List of Items in the Switch Menu
    */
    private $switchMenuArray;
    /**
    * @var string $mainId Div ID that surrounds the switch menu
    * This needs to be unique per switch menu instance on a single page
    */
    public $mainId;
    /**
    * @var string $firstItem This is the default block that should be shown, based on Id
    */
    public $firstItem;

    /**
    * Constructor method
    * @access public
    */
    public function init()
    {
        $this->switchMenuArray = array();
        srand((double)microtime()*1000000);
        $this->mainId = 'switchdiv'.rand(0,1000); // Assign a default name.
        $this->firstItem = '';
    }

    /**
    * Method to add a block to the switch menu.
    * @access public
    * @param string $title Title of the Block
    * @param string $text Text of the Block
    * @param string $cssClass CSS Class to override default style
    */
    public function addBlock($title, $text, $cssClass='')
    {
        $blockArray = array('title'=>$title, 'text'=>$text, 'cssClass'=>$cssClass);
        $this->switchMenuArray[] = $blockArray;
    }

    /**
    * Method to show a completed switch menu
    * @access public
    */
    public function show()
    {
        return $this->_beginSwitchMenu().$this->_generateBlocks().$this->_endSwitchMenu();
    }

    /**
    * Method to generate the blocks for the switch menu
    * @access private
    */
    private function _generateBlocks()
    {
        $output = '';
        $count = 1;

        foreach ($this->switchMenuArray as $block)
        {
            $str = '<div onclick="SwitchMenu(\''.$this->mainId.'_'.$count.'\', \''.$this->mainId.'\')"';
            if ($block['cssClass'] != '') {
                $str .= ' class="menutitle '.$block['cssClass'].'"';
            } else {
                $str .= ' class="menutitle"';
            }
            $str .= '>'.$block['title'].'</div>'."\r\n";
            $str .= '<span class="submenu" id="'.$this->mainId.'_'.$count.'">'.$block['text'].'</span>'."\r\n";
            $output .= $str;
            $count++;
        }

        return $output;
    }

    /**
    * Method to first part of switch menu
    * Generates javascript, sends it to the header.
    * @access private
    */
    private function _beginSwitchMenu()
    {
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('switchmenu.js', 'htmlelements'));

        if(isset($this->firstItem)){
            $script = '<style type="text/css">
            span#'.$this->firstItem.' { display:block; }
            </style>';
        }else{
            $script = '<style type="text/css">
            span#'.$this->mainId.'_1 { display:block; }
            </style>';
        }

        $this->appendArrayVar('headerParams', $script);

        return '<div id="'.$this->mainId.'">'."\r\n";
    }

    /**
    * Method to do the end of the switch menu
    * @access private
    */
    private function _endSwitchMenu()
    {
        return '</div>'."\r\n";
    }
} #end of class
?>