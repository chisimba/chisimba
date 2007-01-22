<?php
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a Freemind mind map and render the map in the page
*
* @author Derek Keats
*
*/

class parse4mindmap extends object
{
    /**
    *
    * Method to parse the string
    * @param String $str The string to parse
    * @return The parsed string
    *
    */
    function parse($str)
    {
    	$str = '/var/www/cpgsql/5ive/app/usrfiles/users/1/freemind/ftisa-jan2007.mm';
    	//echo "parsing mm";
    	$objFlashFreemind = $this->newObject('flashfreemind', 'freemind');
        $objFlashFreemind->getMindmapScript();
    	$objFlashFreemind->setMindMap($str);
        return $objFlashFreemind->show();
        //return $str;
    }

}
