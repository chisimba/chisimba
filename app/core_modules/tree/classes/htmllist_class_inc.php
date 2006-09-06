<?php

/**
* HTML_TreeMenu HTMLList class
*
* This class presents the menu as a HTML list - either ordered <OL> or unordered <UL>
* @author Tohir Solomons
*/
require_once('presentation_class_inc.php');
class htmllist extends presentation
{

    /**
    * Constructor
    *
    * @param object $structure The menu structure
    * @param array  $options   Options whic affect the display of the listbox.
    *                          These can consist of:
    *                           o listtag          HTML Tag to use: Either UL or OL
    *                           o topMostListClass CSS Class that should be applied to the first <UL> / <OL>
    *                           o topMostListId    CSS Id of the first <UL> / <OL>
    *                           o allListClasses   CSS Class that should be applied to all <UL> / <OL>
    *                           o allLiClasses     CSS Class that should be applied to all <LI>
    */
    function htmllist($structure, $options = array())
    {
        $this->presentation($structure);
        
        $this->listTag = 'ul';
        $this->topMostListClass  = '';
        $this->topMostListId  = '';
        $this->allListClasses  = '';
        $this->allLiClasses  = '';
        
        if (is_array($options)) {
            foreach ($options as $option => $value) {
                $this->$option = $value;
            }
        }
        
    }

    /**
    * Returns the HTML generated
    */
    function toHTML()
    {
        
        
        // Restore List Tag to UL if it is not UL or not OL - Prevents tag corruption
        if (strtolower($this->listTag) != 'ul' && strtolower($this->listTag) != 'ol') {
            $this->listTag = 'ul';
        }
        
        // Generate ul class - to prevent looping ifs later.
        if (trim($this->allListClasses) != '') {
            $this->allListClasses = ' class="'.$this->allListClasses.'"';
        }
        
        // Generate Li class - to prevent looping ifs later.
        if (trim($this->allLiClasses) != '') {
            $this->allLiClasses = ' class="'.$this->allLiClasses.'"';
        }
        
        // Array to hold all css classes that should be applied
        $cssArray = array();
        
        if (trim($this->topMostListClass) == '') {
            $class = '';
        } else {
            $class = ' class="'.$this->topMostListClass.'"';
        }
        
        
        $this->startProcess = TRUE;
        
        // Check if this is the first item
        if (trim($this->topMostListId) != '') {
            $id = ' id="'.$this->topMostListId.'" ';
        } else {
            $id = '';
        }
        
        $nodeHTML = '<'.$this->listTag.$id.$class.'>';

        /**
        * Loop through subnodes
        */
        if (isset($this->menu->items)) {
            for ($i=0; $i<count($this->menu->items); $i++) {
                $nodeHTML .= $this->_nodeToHTML($this->menu->items[$i]);
            }
        }
        
        return $nodeHTML.'</'.$this->listTag.'>';
    }

    /**
    * Returns HTML for a single node
    *
    * @access private
    */
    function _nodeToHTML($node)
    {
        
        $cssArray = array();
        
        // Turn Off
        $this->startProcess = FALSE;
        
        
        // Commence Buildling HTML
        $html = '<li'.$this->allLiClasses;
        
        
        
        $html .=' >';
        
        if ($node->link) {
            $html .= '<a href="'.$node->link.'">';
        }
        
        if ($node->cssClass) {
            $html .= '<span class="'.$node->cssClass.'">';
        }
        
        $html .= $node->text;
        
        if ($node->cssClass) {
            $html .= '</span>';
        }
        
        if ($node->link) {
            $html .= '</a>';
        }

        /**
        * Loop through subnodes
        */
        if ( isset($node->items) ) {
            
            $html .= '<'.$this->listTag.$this->allListClasses.'>'; // commence tag for subnodes
            
            for ($i=0; $i<count($node->items); $i++) {
                $html .= $this->_nodeToHTML($node->items[$i]);
            }
            
            $html .= '</'.$this->listTag.'>'; // end tag for subnodes
        }

        return $html.'</li>';
    }
} // End class HTML_TreeMenu_Listbox

?>