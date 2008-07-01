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
        $returnStr = $this->getJavascriptFile('jquery/1.2.3/jquery-1.2.3.pack.js','htmlelements')."\n";

        $returnStr .= '<script language="JavaScript" type="text/javascript" >
                jQuery.noConflict();
        </script>'."\n"."\n";

        return $returnStr;
    }
    
    /**
     * Method to load the liveQuery plugin script files to the header
     */
    public function loadLiveQueryPlugin()
    {
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/livequery/1.0.2/jquery.livequery.js', 'htmlelements'));
    }
    
    /**
     * Method to load the form plugin script files to the header
     */
    public function loadFormPlugin()
    {
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/form/2.12/jquery.form.js', 'htmlelements'));
    }
    
    /**
     * Method to load the Image Fit plugin script files to the header
     */
    public function loadImageFitPlugin()
    {
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/imagefit/0.2/jquery.imagefit_0.2.js', 'htmlelements'));
    }

}

?>