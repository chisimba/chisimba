<?php
/**
 *
 * User interface elements for annotating pages
 *
 * User interface elements for annotating pages, which allow users
 * to add annotations to any page containing one of the annotate 
 * blocks. An annotate block provides the code for annotating, as
 * well as a button to save annotations. 
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   pagenotes
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 *
 * User interface elements for annotating pages
 *
 * User interface elements for annotating pages, which allow users
 * to add annotations to any page containing one of the annotate 
 * blocks. An annotate block provides the code for annotating, as
 * well as a button to save annotations. 
*
* @package   pagenotes
* @author    Derek Keats <derek@dkeats.com>
*
*/
class annotateui extends object
{
    
    /**
    * 
    * @var string $objLanguage String object property for holding the 
    * language object
    * @access public
    * 
    */
    public $objLanguage;

    /**
    *
    * Intialiser for the pagenotes annotation interface builder
     * 
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Only load the active controls if they are logged in.
        if ($this->objUser->isLoggedIn()) {
            $arrayVars['status_successa'] = "mod_pagenotes_status_successa";
            $arrayVars['status_faila'] = "mod_pagenotes_status_faila";
            $arrayVars['interest'] = "mod_pagenotes_interest";
            $arrayVars['noimp'] = "mod_pagenotes_noimp";
            $objSerialize = $this->getObject('serializevars', 'utilities');
            $objSerialize->languagetojs($arrayVars, 'pagenotes');

            // Serialize the contents of the existing annotation.
            $annotDb = $this->getObject('dbpageannotations', 'pagenotes');
            $ar = $annotDb->getAnnotations();
            if (!empty($ar)) {
                $this->annotation = $ar[0]['annotation'];
                $ser = "\n\n<script type='text/javascript'>\n";
                $ser .= "var annotation= '" . $this->annotation . "';\n";
                $ser .= "</script>\n\n";
                $this->appendArrayVar('headerParams', $ser);
                $this->annotation_mode = 'edit';
                // Set up other existing params needed
                $this->id = $ar[0]['id'];
            } else {
                $this->annotation = NULL;
                $this->id = NULL;
                $this->annotation_mode = 'add';
            }
            // Load the annotator script
            $this->appendArrayVar('headerParams',
            $this->getJavaScriptFile('jQuery-annotater/textAnnotater-0.91.js',
              'pagenotes'));
            // Load the JSON parser script
            $this->appendArrayVar('headerParams',
            $this->getJavaScriptFile('jQuery-annotater/json2.js',
              'pagenotes'));
            // Load the script for enabling / disabling select
            $this->appendArrayVar('headerParams',
            $this->getJavaScriptFile('jQuery-annotater/disableTextselect.js',
              'pagenotes'));
            // Load the local implementation of annotate
            $this->appendArrayVar('headerParams',
              $this->getJavaScriptFile('annotate.js',
              'pagenotes'));
            // Load the CSS for the annotations
            $cssLink = $this->getResourceUri('jQuery-annotater/annotate.css',
              'pagenotes');
            $css = '<link href="' . $cssLink . '" type="text/css" rel="stylesheet">';
            $this->appendArrayVar('headerParams', $css);
        }
    }
    
    /**
     *
     * Wrapper to render the main block
     * 
     * @return string The rendered block
     * @access public
     * 
     */
    public function showBlock()
    {
        if ($this->objUser->isLoggedIn()) {
            return $this->getAnnotationController();
        } else {
            return $this->getNotLoggedInMessage();
        }
    }
    
    /**
     *
     * Render the controller that allows for page annotations by inserting
     * a block into any page that supports blocks. 
     * 
     * @return string The rendered controller
     * @access private
     * 
     */
    private function getAnnotationController()
    {
        $note = NULL;
        // Load the form elements - all are hidden.
        $this->loadClass('form','htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        // The form
        $formNote = new form('annotations', NULL);
        // Hidden input for the page.
        $objUrl = $this->getObject('urlutils', 'utilities');
        $page = $objUrl->curPageURL();
        // Remove passthroughlogin as it will mess up the page.
        $page = str_replace('&passthroughlogin=true', NULL, $page);
        // The page hash key.
        $hash = md5($page);
        $hidHash = new hiddeninput('hash');
        $hidHash->cssId = "hash";
        $hidHash->value = $hash;
        $formNote->addToForm($hidHash->show());
        // The edit or add mode.
        $hidMode = new hiddeninput('annotation_mode');
        $hidMode->cssId = "annotation_mode";
        $hidMode->value = $this->annotation_mode;
        $formNote->addToForm($hidMode->show());
        // The id field comes back from save.
        $hidId = new hiddeninput('id');
        $hidId->cssId = "id";
        $hidId->value = $this->id;
        $formNote->addToForm($hidId->show());
        // The annotation
        $hidAnnot = new hiddeninput('annotation');
        $hidAnnot->cssId = "annotation";
        $hidAnnot->value = $this->annotation;
        $formNote->addToForm($hidAnnot->show());
        // The controls
        $formNote->addToForm($this->getControls());
        // The results area.
        $formNote->addToForm("<div id='save_results_annotation' class='noticearea'></div>");
        // Render the form.
        return $formNote->show();
    }
    
    /**
     * 
     * Return a not logged in message
     * 
     */
    private function getNotLoggedInMessage()
    {
        return $this->objLanguage->languageText("mod_pagenotes_notloggedin", "pagenotes");
    }
   
    /**
     * 
     * Get the control button
     *
     * @return string The rendered control button
     * @access private
     * 
     */
    private function getControls()
    {
        $saveAnn = $this->objLanguage->languageText("mod_pagenotes_saveann", "pagenotes");
        return "\n\n<div id='controls'>\n"
          . "<h2 class='pagenotes_control_hd'>Annotation controls</h2>"
	  . "<button id=\"saveAnnotations\">$saveAnn</button><br />\n"
          . $this->getToggleControl() . "\n</div>\n\n";
    }
    
    /**
     *
     * Insert the button for toggling notes visibility
     * 
     * @return string The rendered button
     * @access private
     * 
     */
    private function getToggleControl()
    {
        $toggleAnn = $this->objLanguage->languageText("mod_pagenotes_toggle", "pagenotes");
        return "<button id=\"toggleAnnotations\">$toggleAnn</button>";
    }

}
?>