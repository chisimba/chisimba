<?php
/**
 *
 * Confirmation class using jQuery
 *
 * This class creates a modal jQuery confirmation dialog
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
 * @package   utilities
 * @author    Kevin Cyster kcyster@gmail.com
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
* Confirmation class using jQuery
*
* This class creates a modal jQuery confirmation dialog
*
* @package   utilities
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class jqueryconfirm extends object
{
    /**
     * 
     * Variable to hold the link text
     * 
     * @access proteced
     * @var string
     */
    protected $linkText;

    /**
     * 
     * Variable to hold confirmation message
     * 
     * @access proteced
     * @var string
     */
    protected $message;

    /**
     * 
     * Variable to hold url for the delete action
     * 
     * @access protected
     * @var string
     */
    protected $url;

    /**
     * 
     * Variable to hold dialog title
     * 
     * @access protected
     * @var string
     */
    protected $title;

    /**
     *
     * Method to initialise the class 
     */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_utilities_deleteconfirm', 'utilities', 'ERROR: mod_utilities_deleteconfirm');
    }
    
    /**
     *
     * Method to set the confirmation variables.
     * 
     * @access public
     * @param string $linkText The text of the confirmation link
     * @param string $url The url of the delete action
     * @param string $message The confirmation message
     * @return VOID
     */
    public function setConfirm($linkText, $url, $message)
    {
        $this->linkText = $linkText;
        $this->url = $url;
        $this->message = $message;
    }
    
    /**
     *
     * Method to generate the confirmation dialog and add it to the page
     * 
     * @access public
     * @return VOID 
     */
    public function show()
    {
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');

        $random = mt_rand();
        $id = 'confirm_' . $random;
        $dialogId = 'dialog_confirm_' . $random;
        
        $buttonsArray = array();
        $buttonsArray[$deleteLabel] = 'var uri="' . $this->url . '";uri=uri.replace(/&amp;/g, "&");document.location=uri;';
        $buttonsArray[$cancelLabel] = 'jQuery("#' . $dialogId . '").dialog("close");';

        $objDialog = $this->newObject('dialog', 'jquerycore');
        $objDialog->setCssId($dialogId);
        $objDialog->setTitle($this->title);
        $objDialog->setContent($this->message);
        $objDialog->setWidth(500);
        $objDialog->setAutoAppendScript(FALSE);
        $objDialog->setButtons($buttonsArray);
        $dialog = $objDialog->show();
        
        
        $string = '<a href="#" id="' . $id . '">' . $this->linkText . '</a>';
        
        $script = '<script type="text/javascript">';
        $script .= 'var element = jQuery(\'' . $dialog . '\');';
        $script .= 'jQuery("body").append(element);';
        $script .= 'jQuery("#' . $id . '").live("click", function(){';
        $script .= 'jQuery("#' . $dialogId . '").dialog("open");';
        $script .= '});';
        $script .= '</script>';
        $script .= $objDialog->script;
        //$this->appendArrayVar('headerParams', $script);
        
        return $script . $string;        
    }
}
?>