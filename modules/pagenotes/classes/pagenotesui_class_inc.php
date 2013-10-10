<?php
/**
 *
 * User interface elements for Page notes
 *
 * User interface elements for Page notes, which allow users
 * to add notes to any page containing one of the pagenotes 
 * blocks or keyelements. A key element is one that when clicked
 * loads a pagenote note taker for the current page.
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
 * User interface elements for Page notes
 *
 * User interface elements for Page notes, which allow users
 * to add notes to any page containing one of the pagenotes 
 * blocks or keyelements. A key element is one that when clicked
 * loads a pagenote note taker for the current page.
*
* @package   pagenotes
* @author    Derek Keats <derek@dkeats.com>
*
*/
class pagenotesui extends object
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
    * Intialiser for the pagenotes user interface builder
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        if ($this->objUser->isLoggedIn()) {
            $arrayVars['noterequired'] = "mod_pagenotes_noterequired";
            $arrayVars['status_success'] = "mod_pagenotes_status_success";
            $arrayVars['status_fail'] = "mod_pagenotes_status_fail";
            $objSerialize = $this->getObject('serializevars', 'utilities');
            $objSerialize->languagetojs($arrayVars, 'pagenotes');
            // Load the jquery validate plugin
            $this->appendArrayVar('headerParams',
            $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js',
              'jquery'));
            $this->appendArrayVar('headerParams',
              $this->getJavaScriptFile('pagenotes.js',
              'pagenotes'));
            // Serialize the contents of the existing annotation.
            $noteDb = $this->getObject('dbpagenotes', 'pagenotes');
            $ar = $noteDb->getNotes();
            if (!empty($ar)) {
                $this->note = $ar[0]['note'];
                $arrayParams['notes'] = $this->note;
                $objSerialize->varsToJs($arrayParams);
                $this->pagenotes_mode = 'edit';
                // Set up other existing params needed
                $this->id = $ar[0]['id'];
            } else {
                $this->note = NULL;
                $this->id = NULL;
                $this->pagenotes_mode = 'add';
            }
        }
        $this->loadClass('link','htmlelements');
    }

    /**
     *
     * Wrapper to render the edit block
     * 
     * @return string The rendered edit block
     * @access public
     * 
     */
    public function showBlock($typeOfBlock=FALSE)
    {
        if ($this->objUser->isLoggedIn()) {
            return $this->makeEditForm($typeOfBlock);
        } else {
            return $this->getNotLoggedInMessage();
        }
    }
    
    /**
     *
     * Return an edit icon linked to the id of the record
     * to be edited
     * 
     * @param string $id The record id (primary key)
     * @return string The rendered icon
     * @access private
     *  
     */
    private function getEditIcon($id)
    {
        
        $edIcon = $this->newObject('geticon', 'htmlelements');
        $edIcon->setIcon('edit');
        $edIcon->extra = " id='$id' class='pagenote_editicon' ";
        $edUrl = 'javascript:void(0)';
        $edLink = new link($edUrl);
        $edLink->link = $edIcon->show();
        return $edLink->show();
    }
    
    /**
     *
     * Return an delete icon linked to the id of the record
     * to be deleted
     * 
     * @param string $id The record id (primary key)
     * @return string The rendered icon
     * @access private
     *  
     */
    private function getDelIcon($id)
    {
        
        $delIcon = $this->newObject('geticon', 'htmlelements');
        $delIcon->setIcon('delete');
        $delIcon->extra = " id='$id' class='pagenote_delicon' ";
        $delUrl = 'javascript:void(0)';
        $delLink = new link($delUrl);
        $delLink->link = $delIcon->show();
        return $delLink->show();
    }
    
    private function getRadioIsShared($selected=0)
    {
        //Radio for whether it is public or private
        $pRadio = new radio('isshared');
        $pRadio->addOption(0, $this->objLanguage->languageText('mod_pagenotes_justme', 'pagenotes', 'only me'));
        $pRadio->addOption(1, $this->objLanguage->languageText('mod_pagenotes_evryone', 'pagenotes', 'everyone'));
        $pRadio->setBreakSpace(' &nbsp; ');
        $pRadio->setSelected($selected);
        $label = new label($this->objLanguage->languageText('mod_pagenotes_sharethis', 'pagenotes', 'Who can see this note'));
        return '<br />' . $label->show() . " " . $pRadio->show();
    }
    
    /**
     *
     * Create an edit form
     * 
     * @return string The rendered form
     * @access private
     * 
     */
    private function makeEditForm($typeOfBlock)
    {
        $note = NULL;
        $this->loadClass('form','htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('textarea','htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        // The form
        $formNote = new form('noteEditor', NULL);
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
        // The id field comes back from save.
        $hidId = new hiddeninput('id');
        $hidId->cssId = "id";
        $hidId->value = $this->id;
        $formNote->addToForm($hidId->show());
        
        // The edit/add mode.
        $mode = $this->getParam('mode', 'add');
        $hidMode = new hiddeninput('mode');
        $hidMode->cssId = "pagenotes_mode";
        $hidMode->value = $mode;
        $formNote->addToForm($hidMode->show());
        // The note editor box.
        $noteText = new textarea('note');
        if ($typeOfBlock='wide') {
            $noteText->width=80;
        } else {
            $noteText->cols=23;
        }
        $noteText->setValue($note);
        $noteText->cssClass = 'required';
        $noteText->cssId = 'pagenote_notearea';
        $formNote->addToForm($noteText->show());
        
        //Radio for whether it is public or private
        $pRadio = new radio('ispublic');
        $pRadio->addOption(0, $this->objLanguage->languageText('mod_pagenotes_justme', 'pagenotes', 'only me'));
        $pRadio->addOption(1, $this->objLanguage->languageText('mod_pagenotes_evryone', 'pagenotes', 'everyone'));
        $pRadio->setBreakSpace(' &nbsp; ');
        $pRadio->setSelected(0);
        $label = new label($this->objLanguage->languageText('mod_pagenotes_sharethis', 'pagenotes', 'Who can see this note'));
        $formNote->addToForm($this->getRadioIsShared());
        
        // Save button.
        $buttonTitle = $this->objLanguage->languageText('word_save');
        $button = new button('submitNote', $buttonTitle);
        $button->setToSubmit();
        $formNote->addToForm('<br />' . $button->show());
        // The results area.
        $formNote->addToForm("<div id='save_results_note' class='noticearea'></div>");
        return $formNote->show();
    }
    
    /**
     *
     * Show wide block for rendering page notes within a page. The wideblock
     * is added to a page for viewing notes taken on that page.
     * 
     * @return string The rendered block contents
     * @access public
     *  
     */
    public function showWideBlock()
    {
        $annotDb = $this->getObject('dbpagenotes', 'pagenotes');
        $ar = $annotDb->getNotes();
        $ret = "";
        if (!empty($ar)) {
            $washer = $this->getObject('washout', 'utilities');
            $objDd = $this->getObject('translatedatedifference', 'utilities');
            foreach ($ar as $note) {
                $dispNote = $washer->parseText($note['note']);
                $createDate = $note['datecreated'];            
                $createDate = $objDd->getDifference($createDate);
                $createDate = "<div class='smalldate'>$createDate</div>";
                $ret .= "\n<div class='pagenote_note' id='" 
                  . $note['id'] . "'>" 
                  . $dispNote . "<br />" . $createDate . "<br />"
                  . $this->getEditIcon($note['id']) 
                  . " " . $this->getDelIcon($note['id'])  
                  . "</div>\n";
            }
        }
        return "<div id='pagenotes_all'>" . $ret . "</div>";
    }
    
    /**
     *
     * Renders a note for use by an ajax call, used to insert the note 
     * into the page if adding, or replace the note in the page if editing.
     * 
     * @param boolean $raw Whether to return just the note text for editing, 
     *         or a full formatted note for display
     * @return string The formatted note
     * @access public
     *  
     */
    public function showNoteAjax($raw=FALSE) {
        $id = $this->getParam('id', FALSE);
        if ($id) {
            $objDb = & $this->getObject('dbpagenotes', 'pagenotes');
            if ($raw) {
                $res = $objDb->getNoteById($id);
                return $res;
            } else {
                $noteArray = $objDb->getNoteArrayById($id);
                $note = $noteArray['note'];
                $washer = $this->getObject('washout', 'utilities');
                $note = $washer->parseText($note);
                $dateCreated = $noteArray['datecreated'];
                $objDd = $this->getObject('translatedatedifference', 'utilities');
                $dateCreated = $objDd->getDifference($dateCreated);
                $res = $note . "<div class='smalldate'>$dateCreated</div>";
                $res .= $this->getEditIcon($id) . " " . $this->getDelIcon($id);
                $res = "<div id='$id' class='pagenote_note'>" 
                . $res . "</div>";
            }

        } else {
            $res = NULL;
        }
        return $res;
    }

     /**
      * 
      * Return a not logged in message
      * @return string Formatted message that you are not logged in
      * @access private
      * 
     */
    private function getNotLoggedInMessage()
    {
        return $this->objLanguage->languageText("mod_pagenotes_nlipn", "pagenotes");
    }
}
?>