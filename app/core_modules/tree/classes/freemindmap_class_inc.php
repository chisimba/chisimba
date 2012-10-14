<?php

/**
* FreeMind Map class
*
* This class presents the menu as a Free Mind Map
* @author Tohir Solomons
*/
require_once('presentation_class_inc.php');
class freemindmap extends presentation
{


    /**
    * Constructor
    *
    * @param object $structure The menu structure
    */
    function freemindmap($structure)
    {
        $this->presentation($structure);
    }

    /**
    * Returns the HTML generated
    */
    function toHTML()
    {
        $nodeHTML = '';
        /**
        * Loop through subnodes
        */
        if (isset($this->menu->items)) {
            for ($i=0; $i<count($this->menu->items); $i++) {
                $nodeHTML .= $this->_nodeToHTML($this->menu->items[$i]);
            }
        }
        
        return '<map version="0.7.1">'.$nodeHTML.'</map>';
    }

    /**
    * Returns HTML for a single node
    *
    * @access private
    */
    function _nodeToHTML($node)
    {
        
        
        // Commence Buildling HTML
        $html = '<node TEXT="'.$node->text.'" ';
        
        // Node Link
        if ($node->link) {
            $html .= 'LINK="'.$node->link.'" ';
        }
        
        // Node Style
        if (isset($node->style)) {
            $html .= 'STYLE="'.$node->style.'" ';
        }
        
        if (isset($node->nodebackgroundcolor)) {
            $html .= 'BACKGROUND_COLOR="'.$node->nodebackgroundcolor.'" ';
        }
        
        $html .= '>';
        
        // Check if any font element exists
        if (isset($node->fontbold) || isset($node->fontitalic) || isset($node->fontname) || isset($node->fontsize)) {
            $html .= '<font ';
            
            // Check if Bold
            if (isset($node->fontbold) && $node->fontbold) {
                $html .= 'BOLD="true" ';
            }
            
            // CHeck if Italic
            if (isset($node->fontitalic) && $node->fontitalic) {
                $html .= 'ITALIC="true" ';
            }
            
            // Check if Fontname provided
            if (isset($node->fontname)) {
                $html .= 'NAME="'.$node->fontname.'" ';
            }
            
            // Check if Fontsize provided
            if (isset($node->fontsize)) {
                $html .= 'SIZE="'.$node->fontsize.'" ';
            }
            
            $html .= ' />';
        }
        
        
        // Hooktext
        if (isset($node->hooktext)) {
            $html .= '<hook NAME="accessories/plugins/NodeNote.properties">
<text>'.$node->hooktext.'</text>
</hook>';
        }
        
        
        
        // Cloud - to do - add cloud colour
        if (isset($node->cloud) && $node->cloud) {
            $html .= '<cloud ';
            if (isset($node->cloudcolor)) {
                $html .= 'COLOR="'.$node->cloudcolor.'" ';
            }
            $html .= '/> ';
        }
        
        
        // Edge Width
        if (isset($node->edgeWidth)) {
            $html .= '<edge WIDTH="'.$node->edgeWidth.'" STYLE="sharp_linear"/>';
        }
        
        /**
        * Loop through subnodes
        */
        if ( isset($node->items) ) {
            
            for ($i=0; $i<count($node->items); $i++) {
                $html .= $this->_nodeToHTML($node->items[$i]);
            }
        }

        return $html.'</node>';
    }
} // End class HTML_TreeMenu Free Mind Presentation Layer

?>