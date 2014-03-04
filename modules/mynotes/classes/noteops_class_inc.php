<?php

/**
 *
 * A simple notes module operations object
 * 
 * Arbitrary notes about anything, and organized using tags. These notes can be
 * shared with friends, members of a context, or made public.
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
 * @version
 * @package    mynotes
 * @author     Nguni Phakela info@nguni52.co.za
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
 */
// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global unknown $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * A simple notes module operations object
 *
 * @category  Chisimba
 * @author    Nguni Phakela
 * @version
 * @copyright 2010 AVOIR
 *
 */
class noteops extends object {

    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access private
     *
     */
    private $objLanguage;
    /*
     * @var $tagCloud The html string that is returned as the tag cloud
     * @access private
     */
    private $tagCloud;

    /*
     * @var $objTagCloud The tag cloud object used. This is the object that is in the
     * utilities module.
     * @access protected
     * 
     */
    protected $objTagCloud;

    /*
     * @var $module This is the name of the current module
     * @access private
     */
    private $module;

    /*
     * @var $objUtility This is the current module's utility class
     * @access private
     */
    private $objUtility;

    /*
     * @var $uid current user id
     * @access public
     */
    public $uid;

    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() {
        try {
            $this->module = "mynotes";
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->objTagCloud = $this->getObject('tagcloud', 'utilities');
            $this->objUtility = $this->getObject('utility', $this->module);

            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objLink = $this->loadClass('link', 'htmlelements');
            $this->objIcon = $this->getObject('geticon', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
            $this->htmlHeading = $this->loadClass('htmlheading', 'htmlelements');
            $this->loadClass('label', 'htmlelements');
            $this->objCheckbox = $this->loadClass('checkbox', 'htmlelements');

            $this->objDbmynotes = $this->getObject('dbmynotes', $this->module);
            $this->objDbTags = $this->getObject('dbtags', $this->module);

            $this->uid = $this->objUser->userId();
        } catch (customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
     * Method to show note(s)
     *
     * @access public
     * @param $isViewAll Are we viewing all the notes?
     * @param $nextPage The next page
     * @param $prevPage The previous page
     * @return string The input box and button
     *
     */
    public function showNotes($isViewAll = NULL, $nextPage = NULL, $prevPage = NULL) {
        $id = $this->getParam('id');
        if (!empty($id)) {
            return $this->showNote($this->getParam('id'));
        } else {
            $addLabel = $this->objLanguage->languageText('word_add', 'system', 'WORD: word_add, not found');
            $addNoteLabel = $this->objLanguage->code2Txt('mod_mynotes_addnote', $this->module, NULL, 'TEXT: mod_mynotes_addnote, not found');
            $viewLabel = $this->objLanguage->languageText('word_view', 'system', 'WORD: word_view, not found');
            $viewNoteLabel = $this->objLanguage->code2Txt('mod_mynotes_viewnote', $this->module, NULL, 'TEXT: mod_mynotes_viewnote, not found');
            $ret = '';

            $this->objIcon->title = $addLabel;
            $this->objIcon->alt = $addLabel;
            $this->objIcon->setIcon('add', 'png');
            $addIcon = $this->objIcon->show();

            $this->objIcon->title = $viewLabel;
            $this->objIcon->alt = $viewLabel;
            $this->objIcon->setIcon('view', 'gif');
            $viewAllIcon = $this->objIcon->show();

            $objLink = new link($this->uri(array('action' => 'add', 'mode' => 'add')));
            $objLink->link = $addIcon . '&nbsp;' . $addNoteLabel;
            $addLink = $objLink->show();

            $objLink = new link($this->uri(array('action' => 'viewall', 'mode' => 'viewall')));
            $objLink->link = $viewAllIcon . '&nbsp;' . $viewNoteLabel;
            $viewAllLink = $objLink->show();

            $ret .= $viewAllLink . "&nbsp;&nbsp;&nbsp;&nbsp;" . $addLink;

            $viewAllInput = "";
            if (!empty($isViewAll)) {
                $objInput = new textinput('viewall', 'true', 'hidden', '40');
                $objInput->setId("viewall");
                $viewAllInput = $objInput->show();
            }
            $nextPageInput = "";
            if (!empty($nextPage)) {
                $objInput = new textinput('nextpage', $nextPage, 'hidden', '40');
                $objInput->setId("nextpage");
                $nextPageInput = $objInput->show();
            }
            $prevPageInput = "";
            if (!empty($prevPage)) {
                $objInput = new textinput('prevpage', $prevPage, 'hidden', '40');
                $objInput->setId("prevpage");
                $prevPageInput = $objInput->show();
            }

            $ret .= $viewAllInput. $prevPageInput . $nextPageInput;

            return $ret;
        }
    }

    /**
     * Method to show note form when either adding a new note or editing a note
     * 
     * @access public
     * @param  $mode add or editing note parameter
     * @return The form used when adding or editing a note
     * 
     */
    public function addEditNote($mode) {
        if ($mode == 'add') {
            $titleInputValue = NULL;
            $tagInputValue = NULL;
            $mainText = NULL;

            $idInputValue = NULL;
        } else {
            $idInputValue = $this->getParam('id');

            $data = $this->objDbmynotes->getNote($idInputValue);

            if (!empty($data)) {
                $titleInputValue = $data['title'];
                $tagInputValue = $data['tags'];
                $mainText = $data['content'];
            } else {
                $titleInputValue = NULL;
                $tagInputValue = NULL;
                $mainText = NULL;
            }
        }

        $errorArray = $this->getSession('errors');
        $titleIdInputError = (!empty($errorArray) && array_key_exists('id', $errorArray['errors'])) ? $errorArray['errors']['id'] : NULL;
        //$titleInputError = (!empty($errorArray) && array_key_exists('title', $errorArray['errors'])) ? $errorArray['errors']['title'] : NULL;
        $tagInputError = (!empty($errorArray) && array_key_exists('tags', $errorArray['errors'])) ? $errorArray['errors']['tags'] : NULL;
        //$editorInputError = (!empty($errorArray) && array_key_exists('content', $errorArray['errors'])) ? $errorArray['errors']['content'] : NULL;

        $titleInputValue = !empty($errorArray) ? $errorArray['data']['title'] : $titleInputValue;
        $tagInputValue = !empty($errorArray) ? $errorArray['data']['tag'] : $tagInputValue;
        $mainText = !empty($errorArray) ? $errorArray['data']['content'] : $mainText;

        // get text for labels
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'WORD: word_save, not found');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'WORD: word_cancel, not found');
        $noteTitleLabel = ucfirst($this->objLanguage->code2Txt('mod_mynotes_notetitle', $this->module, NULL, 'TEXT: mod_mynotes_notetitle, not found'));
        $noteTagLabel = ucfirst($this->objLanguage->code2Txt('mod_mynotes_notetag', $this->module, NULL, 'TEXT: mod_mynotes_notetag, not found'));
        $notePublicLabel = ucfirst($this->objLanguage->code2Txt('mod_mynotes_notepublic', $this->module, NULL, 'TEXT: mod_mynotes_notepublic, not found'));
        $noteEditorLabel = ucfirst($this->objLanguage->code2Txt('mod_mynotes_noteeditor', $this->module, NULL, 'TEXT: mod_mynotes_noteeditor, not found'));
        
        // create labels
        $titleLabel = new label($noteTitleLabel . ':', 'input_title');
        $tagLabel = new label($noteTagLabel . ':', 'input_tag');
        $publicLabel = new label($notePublicLabel . ":", 'input_public');
        $editorLabel = new label($noteEditorLabel . ':&nbsp;', 'input_content');
        
        // input elements for the form
        $objInput = new textinput('id', $idInputValue, 'hidden', '40');
        $idInput = $objInput->show();

        $objInput = new textinput('title', $titleInputValue, '', '60');
        $titleInput = $objInput->show();

        $objInput = new textinput('tags', $tagInputValue, '', '60');
        $tagInput = $objInput->show();
        
        $publicCheckbox = new checkbox('public');

        $objTable = new htmltable();
        $objTable->cellpadding = '4';

        $responseArea = "<div id=\"responsearea\" class=\"error\"></div>";
        
        /* Add Title Row */
        $objTable->startRow();
        $objTable->addCell($titleLabel->show(), '200px', '', '', '', '');
        $objTable->addCell($titleIdInputError . $titleInput, '', '', '', '', '');
        $objTable->endRow();

        /* Add Tags Row */
        $objTable->startRow();
        $objTable->addCell($tagLabel->show(), '200px', '', '', '', '');
        $objTable->addCell($tagInputError . $tagInput, '', '', '', '', '');
        $objTable->endRow();
        
        /* Add Public Row */
        $objTable->startRow();
        $objTable->addCell($publicLabel->show(), '200px', '', '', '', '');
        $objTable->addCell($publicCheckbox->show(), '', '', '', '', '');
        $objTable->endRow();
        
        /* Add Editor Rows */
        //Add the WYSIWYG editor label
        $objTable->startRow();
        $objTable->addCell($editorLabel->show(), '200px', 'top', '', NULL, '');

        //Add the WYSIWYG editor
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = ucfirst($this->objLanguage->code2Txt('mod_mynotes_notecontentname', $this->module, NULL, 'TEXT: mod_mynotes_notecontentname, not found'));
        $editor->height = '300px';
        $editor->width = '550px';
        $editor->setContent($mainText);
        $objTable->addCell($editor->show(), NULL, '', '', NULL, '');
        $objTable->endRow();

        $objButton = new button('save', $saveLabel);
        $objButton->setToSubmit();
        $saveButton = $objButton->show();

        $objButton = new button('cancel', $cancelLabel);
        //$objButton->setToSubmit();
        if ($mode == 'add') {
            $uri = $this->uri(array());
        } else {
            $uri = $this->uri(array('action'=>'shownote', 'id'=>$idInputValue));
        }
        $objButton->setOnClick('javascript: window.location=\''.$uri.'\'');
        $cancelButton = $objButton->show();
        
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp;' . $cancelButton, '', '', '', '', 'colspan="2"');
        $objTable->endRow();

        $myUri = $this->uri(array(
                            'action' => 'save'/*'validatenote'*/,
                            'mode' => $mode
                        ));
        $myUri = str_replace("&amp;", "&", $myUri);
        $hiddenAction = new textinput('uri', $myUri, 'hidden', NULL);
        
        $objForm = new form($this->module, "javascript:checkFormSubmit();void(0);");
        //$objForm->setOnSubmit("checkFormSubmit($myUri);return false;");

        $objForm->addToForm($responseArea);
        $objForm->addToForm($objTable->show());
        $objForm->addToForm($hiddenAction);

        return $objForm->show();
    }

    /*
     * Method used to validate that the contents of the note are as expected
     * 
     * @access public
     * @param  $mode add or editing note parameter
     * @return A form that shows the data that has been input for confirmation
     * 
     */
    public function validateNote($mode = NULL) {
        if ($mode == "add") {
            $idInput = NULL;
        } else {
            $idInputValue = $this->getParam('id');
            $objInput = new textinput('id', $idInputValue, 'hidden', '50');
            $idInput = $objInput->show();
        }

        $titleInputValue = $this->getParam('title');
        $tagInputValue = $this->getParam('tags');
        $editorInputValue = $this->getParam('NoteContent');

        $confirmLabel = $this->objLanguage->languageText('word_confirm', 'system', 'WORD: word_save, not found');
        $backLabel = $this->objLanguage->languageText('word_back', 'system', 'WORD: word_cancel, not found');
        $noteTitleLabel = ucfirst($this->objLanguage->code2Txt('mod_mynotes_notetitle', $this->module, NULL, 'TEXT: mod_mynotes_notetitle, not found'));
        $noteTagLabel = ucfirst($this->objLanguage->code2Txt('mod_mynotes_notetag', $this->module, NULL, 'TEXT: mod_mynotes_notetag, not found'));
        $noteEditorLabel = ucfirst($this->objLanguage->code2Txt('mod_mynotes_noteeditor', $this->module, NULL, 'TEXT: mod_mynotes_noteeditor, not found'));
        
        // create labels
        $titleLabel = new label($noteTitleLabel . ':', 'input_title');
        $tagLabel = new label($noteTagLabel . ':', 'input_tag');
        $editorLabel = new label($noteEditorLabel . ':&nbsp;', 'input_content');
        $objInput = new textinput('title', $titleInputValue, 'hidden', '40');
        $titleInput = $objInput->show();

        $objInput = new textinput('tags', $tagInputValue, 'hidden', '40');
        $tagInput = $objInput->show();

        $objInput = new textinput('content', $editorInputValue, 'hidden', '40');
        $editorInput = $objInput->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';

        /* Add Title Row */
        $objTable->startRow();
        $objTable->addCell($titleLabel->show(), '200px', '', '', '', '');
        $objTable->addCell($titleInputValue, '', '', '', '', '');
        $objTable->endRow();

        /* Add Tags Row */
        $objTable->startRow();
        $objTable->addCell($tagLabel->show(), '200px', '', '', '', '');
        $objTable->addCell($tagInputValue, '', '', '', '', '');
        $objTable->endRow();

        /* Add Editor Rows */
        $objTable->startRow();
        $objTable->addCell($editorLabel->show(), NULL, 'top', '', NULL, '');
        $objTable->addCell($editorInputValue, NULL, 'top', '', NULL, '');
        $objTable->endRow();

        $objButton = new button('save', $confirmLabel);
        $objButton->setToSubmit();
        $confirmButton = $objButton->show();

        $objButton = new button('cancel', $backLabel, "javascript:history.go(-1)");
        $backButton = $objButton->show();

        $objTable->startRow();
        $objTable->addCell($idInput . $confirmButton . '&nbsp;' . $backButton, '', '', '', '', 'colspan="2"');
        $objTable->endRow();

        $validateForm = new form($this->module, $this->uri(array(
                            'action' => 'save',
                            'mode' => $mode
                        )));

        $validateForm->addToForm($objTable);
        $validateForm->addToForm($idInput);
        $validateForm->addToForm($titleInput);
        $validateForm->addToForm($tagInput);
        $validateForm->addToForm($editorInput);

        return $validateForm->show();
    }

    /*
     * Method to retrieve the notes from the database and display them
     * 
     * @access public
     * @return a list of notes
     * 
     */
    public function getNotes() {
        $isViewAll = $this->getParam('viewall');
        $prevPage = $this->getParam("prevnotepage");
        $nextPage = $this->getParam("nextnotepage");

        // Set up text elements.
        //$noNotesLabel = $this->objLanguage->languageText('mod_mynotes_nonotes', $this->module, 'TEXT: mod_mynotes_nonotes, not found');
        $readMoreLabel = $this->objLanguage->languageText('mod_mynotes_readmore', $this->module, 'TEXT: mod_mynotes_readmore, not found');
        $ret = '';
        $list = '';
        $limit = NULL;
        if (empty($isViewAll)) {
            $limit = "LIMIT 2";
        }
        $notesArray = $this->objDbmynotes->getNotes($this->uid, $limit);

        if (!empty($notesArray)) {
            if ($isViewAll) {
                $list = "<div class='notes_view'><ul>";
                foreach ($notesArray as $value) {
                    $noteLink = new Link($this->uri(array('action' => 'shownote', 'id' => $value['id']), $this->module));
                    $noteLink->link = $value['title'];
                    $list .= "<li>" . $noteLink->show() . "</li>";
                }
                $list .= "</ul></div>";
            } else {
                $list = "<div class='notes_view'><ul>";
                foreach ($notesArray as $value) {
                    $noteLink = new Link($this->uri(array('action' => 'shownote', 'id' => $value['id']), $this->module));
                    $noteLink->link = "&nbsp;&nbsp;<span id='readmore'>" . $readMoreLabel . " ...</span>";
                    $list .= "<li>" . $this->objUtility->wordlimit(strip_tags($value['content']), 50, $noteLink->show()) . "</li>";
                }
                $list .= "</ul></div>";
            }
        }/* else {
          //$error = $this->error($noNotesLabel);

          //$list = "<div><ul>" . $noNotesLabel . "</ul></div>";
          } */
        $ret .= $list;

        if (empty($isViewAll)) {
            $ret .= "<div class='notelist'>" . $this->getNotesList($prevPage, $nextPage) . "</div>";
            $ret .= "<div class='center'>" . $this->objUtility->getPrevNextLinks($prevPage, $nextPage) . "</div>";
        }

        echo $ret;
        die();
    }

    /*
     * Method used to return the tag cloud that is shown on the left sidebar for
     * current user only
     * 
     * @access public
     * @return the tag cloud
     * 
     */
    public function getTagCloud() {
        $filter = "userid = '".$this->objUser->userId()."'";
        $this->tagCloud = $this->objDbTags->getTags($filter);
        if (!empty($this->tagCloud)) {
            $tagscl = $this->objUtility->processTags($this->tagCloud);

            if ($tagscl != NULL) {
                $this->objTagCloud = $this->objTagCloud->buildCloud($tagscl);
            } else {
                $this->objTagCloud = NULL;
            }
        } else {
            $this->objTagCloud = NULL;
        }

        return $this->objTagCloud;
    }

    /*
     * Method to search notes based on tags
     * @param searchKey keywords to search the notes
     * @return A list of notes that match the search key
     * 
     */
    public function searchNotes($searchKey) {
        $mySearchResults = $this->objDbmynotes->getNotesWitTags($searchKey);

        // Set up text elements.
        $noNotesLabel = $this->objLanguage->languageText('mod_mynotes_nonotes', $this->module, 'TEXT: mod_mynotes_nonotes, not found');

        $ret = '';
        if (!empty($mySearchResults)) {
            $list = "<div><ul>";
            $tmpLink = NULL;
            foreach ($mySearchResults as $value) {
                $tmpLink = new Link($this->uri(array('action' => 'showNote', 'id' => $value['id'], $this->module)));
                $tmpLink->link = $value['title'];
                $list .= "<li>" . $tmpLink->show() . "</li>";
            }
            $list .= "</ul></div>";
        } else {
            $error = $this->error($noNotesLabel);

            $list = "<div><ul>" . $error . "</ul></div>";
        }
        $ret .= $list;

        return $ret;
    }

    /*
     * Method to show one note
     * 
     * @access public
     * @param $id The id of the note to be viewed
     * @return String Note data
     * 
     */
    public function showNote($id) {
        $editLabel = $this->objLanguage->languageText('word_edit', 'system', 'WORD: word_edit, not found');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'WORD: word_delete, not found');
        $shareLabel = $this->objLanguage->languageText('word_share', 'mynotes', 'WORD: word_share, not found');

        $noteArr = $this->objDbmynotes->getNote($id);

        $titleValue = $noteArr['title'];
        $tagValues = $noteArr['tags'];
        $contentValue = $noteArr['content'];

        $title = new htmlheading($titleValue, 2);

        $editLink = new link($this->uri(array("action" => 'edit', 'id' => $noteArr['id']), $this->module));
        $this->objIcon->title = $editLabel;
        $this->objIcon->alt = $editLabel;
        $this->objIcon->setIcon('edit', 'png');
        $editIcon = $this->objIcon->show();
        $editLink->link = $editIcon;

        $deleteLink = new link($this->uri(array("action" => 'delete', 'id' => $noteArr['id']), $this->module));
        $this->objIcon->title = $deleteLabel;
        $this->objIcon->alt = $deleteLabel;
        $this->objIcon->setIcon('delete', 'png');
        $deleteIcon = $this->objIcon->show();
        $deleteLink->link = $deleteIcon;
        $deleteLink->cssId = "delete";
        $deleteLink->extra = 'onclick="return confirmDelete()"';
        
        // copy the share icon from _common2/css/images/sexybuttons/icons/silk to _common/icons/
        //$shareLink = new link($this->uri(array('action'=>'share', 'id'=>$noteArr['id'], $this->module)));
        $this->objIcon->title = $shareLabel;
        $this->objIcon->alt = $shareLabel;
        $this->objIcon->setIcon('share','png');
        $this->objIcon->extra = "class='modal'";
        $shareIcon = $this->objIcon->show();
        //$shareLink->link = $shareIcon;

        /* disabled the share button since it is not yet complete */
        $title->str .= "&nbsp;" . $editLink->show() . "&nbsp;" . $deleteLink->show();/* . "&nbsp;" . $shareIcon;*/
        $str = "<div>";
        $str .= $title->show();
        $str .= $contentValue;

        $tmpLink = NULL;
        $tagList = "";
        $tagArr = explode(",", $tagValues);
        $arraySize = count($tagArr);
        $count = 0;
        
        foreach ($tagArr as $value) {
            $count++;
            $tmpLink = new Link($this->uri(array('action' => 'search', 'srchstr' => $value, 'srchtype' => 'tags'), $this->module));
            $tmpLink->link = $value;
            $tagList .= $tmpLink->show();
            if ($count < $arraySize) {
                $tagList .= ",";
            }
        }

        $str .= "<div class=\"center taglist \">" . $tagList . "</div>";
        $str .= "</div>";
        
        $popupSource = $this->getResourceUri('tmp/test.html');

        return $str;
    }

    /*
     * Method used to get the notes list
     * 
     * @access public
     * @param $begin The record number for the beginning of the list
     * @param $end The record number for the end of the list
     * @return A list of notes, 5 of them, for the current page.
     * 
     */
    public function getNotesList($begin, $end) {
        $noNotesLabel = $this->objLanguage->languageText('mod_mynotes_nonotes', $this->module, 'TEXT: mod_mynotes_nonotes, not found');

        if (empty($begin)) {
            $begin = 2;
            $end = 7;
        }

        $ret = "";
        $list = "";

        // check if there are less than 2 notes.
        $allNotesData = $this->objDbmynotes->getListCount($this->uid);
        if ($allNotesData > 2) {
            $data = $this->objDbmynotes->getNotesForList($this->uid, $begin, $end);

            if (!empty($data)) {
                $list = "<div><ul>";
                $tmpLink = NULL;
                foreach ($data as $value) {
                    $tmpLink = new Link($this->uri(array('action' => 'showNote', 'id' => $value['id'], $this->module)));
                    $tmpLink->link = $value['title'];
                    $list .= "<li>" . $tmpLink->show() . "</li>";
                }
                $list .= "</ul></div>";
            } else {
                $list = "<div class='error'>" . $noNotesLabel . "</div>";
            }
        } else {
            $list = "";
        }
        
        $ret .= $list;

        return $ret;
    }
    
    public function getShareOptions() {
        /* work in progress */
        echo "This is the returned data from the ajax call. Holla!!";
        die();
    }
}
?>