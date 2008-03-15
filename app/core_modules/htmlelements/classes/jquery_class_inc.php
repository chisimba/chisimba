<?php

/**
* Class to load the JQuery JavaScript LIbrary
*
* This class merely loads the JavaScript for JQuery. It includes code to prefent a clash with Prototpe/Scriptaculous
* It is not a wrapper. Developers still need to code their own JS functions
*
* @category  Chisimba
* @author  Tohir Solomons
* @package htmlelements
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
*/

class jquery extends object
{
    /**
    * Constructor
    */
    public function init()
    { }
    
    
    /**
    * Method to load the JQuery JavaScript
    *
    * @return string JQuery JavaScript
    */
    public function show()
    {
        // Load JQuery
        $returnStr = $this->getJavascriptFile('jquery/1.2.1/jquery-1.2.1.min.js','htmlelements')."\n";
        
        $returnStr .= '<script language="JavaScript" type="text/javascript" >
                jQuery.noConflict();
        </script>'."\n"."\n";
        
        return $returnStr;
    }

}

?>