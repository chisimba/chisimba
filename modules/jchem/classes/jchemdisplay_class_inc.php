<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}

/**
 * Class jchemdisplay containing all display/output functions of the chemdoodle module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright Wits University 2010
 * @license http://opensource.org/licenses/lgpl-2.1.php
 * @package jchem
 *
 */
class jchemdisplay extends object
{
   /** @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;

   /** @var object $objUser: The user class of the buddies module
    * @access public
    */
   public $objUser;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // system classes
        $this->objLanguage = $this->getObject('language','language');
        $this->objUser = $this->getObject('user','security');
    }

    /**
     * Method to show the editor
     *
     * @return string $output The output to display the editor
     */
    public function show()
    {
        $objAltConfig = $this->getObject('altconfig','config');
        $moduleUri=$objAltConfig->getModuleURI();
        $siteRoot=$objAltConfig->getSiteRoot();
        $codebase=$siteRoot."/".$moduleUri.'/jchem/resources/';

        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '1';
        $title->str = $this->objLanguage->languageText('mod_jchem_chemicaleditor', 'jchem');

        $output = '<p>'.$title->show().'</p>';

        $output .= '<p>&nbsp;</p>';

        $output .= '<applet code="org.openscience.jchempaint.applet.JChemPaintEditorApplet"
        archive="'.$codebase.'jchempaint-applet-core.jar"
        name="Editor"
        width="500" height="400">';
        $output .= '<param name="implicitHs"      value="true">';
        $output .= '<param name="codebase_lookup" value="false">';
        $output .= '<param name="image"           value="hourglass.gif">';
        $output .= '<param name="centerImage"     value="true">';
        $output .= '<param name="boxBorder"       value="false">';
        $output .= '<param name="language"        value="en">';
        $output .= '</applet>';

        return $output;
    }

}
?>