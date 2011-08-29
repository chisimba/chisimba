<?php

/**
* Class to load the Prototype JavaScript
*
* This class loads the JavaScript for Prototype.
*
* @category Chisimba
* @package prototype
* @author Tohir Solomons
* @author Jeremy O'Connor
* @copyright (C) 2011 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id: scriptaculous_class_inc.php 15165 2009-10-15 12:09:30Z pwando $
*/

class prototype extends object
{
        /**
    * Constructor
    */
    public function init()
    { }


    /**
    * Returns the prototype JavaScript
    *
    * @param string $mimetype Mime type of page
    * @return string prototype JavaScript
    */
    public function show($mimetype)
    {
        //$usingXHTML = $mimetype == 'application/xhtml+xml';

        // Load Prototype
        //$returnStr = $this->getJavascriptFile('prototype/1.5.0_rc1/prototype.js','htmlelements')."\n";
        $returnStr = $this->getJavascriptFile('scriptaculous/1.7.1_beta3/lib/prototype.js','prototype')."\n";

        return $returnStr;
    }

}

?>