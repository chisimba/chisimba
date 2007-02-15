<?php
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a Freemind mind map and render the map in the page
*
* @author Derek Keats, Tohir Solomons
*
*/

class parse4mindmap extends object
{
    
    function init()
    {
        $this->objFlashFreemind = $this->newObject('flashfreemind', 'freemind');
    }
    
    /**
    *
    * Method to parse the string
    * @param String $str The string to parse
    * @return The parsed string
    *
    */
    function parse($str)
    {
        $str = stripslashes($str);
        preg_match_all('/\\[MAP]<a.*?href="(?P<maplink>.*?)".*?>.*?<\/a>\\[\/MAP]/', $str, $results, PREG_PATTERN_ORDER);
        
        $counter = 0;
        
        foreach ($results[0] as $item)
        {
            $this->objFlashFreemind->setMindMap($results['maplink'][$counter]);
            $replacement = $this->objFlashFreemind->show();
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        return $str;
    }

}
?>