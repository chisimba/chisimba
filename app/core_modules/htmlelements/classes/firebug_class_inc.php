<?php

/**
* Class to load the Firebug Lite Interface
*
* This class will load the firebug-lite extension and ultimately allow
* firebug to run on non-firefox browsers.
*
* @category  Chisimba
* @author  Charl Mert
* @package htmlelements
* @copyright 2009 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @link      http://avoir.uwc.ac.za
*/

class firebug extends object
{

    /**
    * Constructor
    */
    public function init()
    { 
    }


    /**
    * Method to load the firebug application
    *
    */
    public function show()
    {
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('firebug-lite.js', 'htmlelements'));
        return TRUE;
    }
    
	
}

?>
