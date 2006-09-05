<?php

/**
* HTML_TreeMenu_Dropdown class
*
* This class presents the menu as a dropdown where the link is the value, and the text is the label.
* This is different from the listbox that presents a form with a navigation. This one focussess on 
* presentation as an input.
*/
require_once('presentation_class_inc.php');
class htmldropdown extends presentation
{
    /**
    * The Name of the <select> Input
    * @var string
    */
    var $inputName;

    /**
    * The character used for indentation
    * @var string
    */
    var $indentChar;

    /**
    * How many of the indent chars to use
    * per indentation level
    * @var integer
    */
    var $indentNum;

    /**
    * Target for the links generated
    * @var string
    */
    var $linkTarget;

    /**
    * Constructor
    *
    * @param object $structure The menu structure
    * @param array  $options   Options whic affect the display of the listbox.
    *                          These can consist of:
    *                           o inputName  Name of the <select input>
    *                           o indentChar The character to use for indenting the nodes
    *                                        Defaults to "&nbsp;"
    *                           o indentNum  How many of the indentChars to use per indentation level
    *                                        Defaults to 2
    */
    function htmldropdown($structure, $options = array())
    {
        $this->presentation($structure);

        $this->inputName  = 'selectinput';
        $this->id         = 'input_selectinput';
        $this->indentChar = '&nbsp;';
        $this->indentNum  = 4;
        $this->extra      = '';
        $this->selected      = '';

        foreach ($options as $option => $value) {
            $this->$option = $value;
        }
    }

    /**
    * Returns the HTML generated
    */
    function toHTML()
    {
        static $count = 0;
        $nodeHTML = '';
        
        /**
        * Loop through subnodes
        */
        if (isset($this->menu->items)) {
            for ($i=0; $i<count($this->menu->items); $i++) {
                $nodeHTML .= $this->_nodeToHTML($this->menu->items[$i]);
            }
        }
        $count++;
        return sprintf('<select name="%s" id="%s" %s>%s</select>', $this->inputName, $this->id, $this->extra, $nodeHTML);
    }

    /**
    * Returns HTML for a single node
    *
    * @access private
    */
    function _nodeToHTML($node, $prefix = '')
    {
        if ($this->selected != '' && $this->selected == $node->link) {
            $isSelected = 'selected="selected"';
        } else {
            $isSelected = '';
        }
        
        $html = sprintf('<option value="%s" %s>%s%s</option>', $node->link, $isSelected, $prefix, $node->text);
        
        /**
        * Loop through subnodes
        */
        if ( isset($node->items) ) {
            for ($i=0; $i<count($node->items); $i++) {
                $html .= $this->_nodeToHTML($node->items[$i], $prefix . str_repeat($this->indentChar, $this->indentNum));
            }
        }

        return $html;
    }
} // End class HTML_TreeMenu_Dropdown

?>