<?php

/* -------------------- glossary class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Glossary Controller
 * This class controls all functionality to run the Glossary module
 * @author Tohir Solomons, Alastair Pursch (PHP5)
 * @copyright (c) 2004 University of the Western Cape
 * @package glossary
 * @version 1
 */
class glossary extends controller {

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init() {
        // Load User Class
        $this->objUser = $this->getObject('user', 'security');

        // Load Language Class
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->setVarByRef('objLanguage', $this->objLanguage);

        // Load Glossary Classes
        $this->objGlossary = $this->getObject('dbglossary');
        $this->objGlossaryUrls = $this->getObject('dbglossaryurls');
        $this->objGlossarySeeAlso = $this->getObject('dbglossaryseealso');
        $this->objGlossaryImages = $this->getObject('dbglossaryimages');

        // Load File Upload Class for uploading images.
        $this->objUploader = $this->getObject('upload', 'filemanager');

        // Load File Upload Class's DB for erasing images.
        $this->objUploadDB = $this->getObject('dbfile', 'filemanager');

        // Get Context Code & Title
        $this->contextObject = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();
        $this->contextTitle = $this->contextObject->getTitle();
        //Load Module Catalogue Class
        $this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');

        $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');

        if ($this->objModuleCatalogue->checkIfRegistered('activitystreamer')) {
            $this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
            $this->eventDispatcher->addObserver(array($this->objActivityStreamer, 'postmade'));
            $this->eventsEnabled = TRUE;
        } else {
            $this->eventsEnabled = FALSE;
        }

        // Set Context Code to 'root' if not in context
        if ($this->contextCode == '') {
            $this->contextCode = 'root';
            $this->contextTitle = ' Lobby';
            $this->setLayoutTemplate("user_layout_tpl.php");
        } else {
            $this->setLayoutTemplate("context_layout_tpl.php");
        }

        // Permissions Module
        //$this->objDT = $this->getObject( 'decisiontable','decisiontable' );
        // Create the decision table for the current module
        //$this->objDT->create('glossary');
        // Collect information from the database.
        //$this->objDT->retrieve('glossary');

        $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');

        // Set Header & Footer Available for each page in Glossary
        $this->setVarByRef('header', $this->showGlossaryHeader());
        $this->setVarByRef('footer', $this->showGlossaryFooter());

        //Get the activity logger class
        $this->objLog = $this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();

        $this->setVar('pageSuppressXML', TRUE);
    }

    /**
     * Method to turn off logging For certain actions
     */
    public function requiresLogin($action) {
        switch ($action) {
            case 'parse':
                return FALSE;

            case 'listsingle':
                return FALSE;

            case 'search':
                return FALSE;

            case 'singlepopup':
                return FALSE;

            case 'viewbyletter':
                return FALSE;

            case 'listimages':
                return FALSE;

            case 'previewimage':
                return FALSE;

            default:
                return TRUE;
        }

        return TRUE;

        if ($this->getParam('action') == 'singlepopup' || $this->getParam('action') == 'previewimage') {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Method to process actions to be taken
     *
     * @param string $action variable holding the action to be taken
     */
    public function dispatch($action=Null) {


        switch ($action) {
            case 'parse':
                return $this->parse();

            case 'add':
                return $this->addTerm();

            case 'addconfirm':
                return $this->addConfirm();

            case 'addseealsoconfirm':
                return $this->addSeeAlsoConfirm();

            case 'addurlconfirm':
                return $this->addUrlConfirm();

            case 'delete':
                return $this->deleteGlossary($this->getParam('id'));

            case 'deleteconfirm':
                return $this->deleteConfirm();

            case 'edit':
                return $this->editTerm($this->getParam('id'));

            case 'editconfirm':
                return $this->editConfirm();

            case 'deleteurl':
                return $this->deleteUrl($this->getParam('link'));

            case 'deleteseealso':
                return $this->deleteSeeAlso($this->getParam('seealso'));

            case 'listsingle':
                return $this->showsingle($this->getParam('id'));

            case 'search':
                return $this->glossarySearch($this->getParam('term'));

            case 'singlepopup':
                return $this->singlePopUpDisplay($this->getParam('id'));

            case 'viewbyletter':
                return $this->glossaryViewByLetter($this->getParam('letter'));

            case 'listimages':
                $this->setLayoutTemplate(null);
                return $this->showImages($this->getParam('id'));

            case 'uploadimage':
                return $this->uploadImage();

            case 'previewimage':
                $this->setLayoutTemplate(null);
                return $this->previewImage($this->getParam('id'), $this->getParam('fname'));

            case 'deleteimage':
                return $this->deleteImage($this->getParam('id'), $this->getParam('returnid'));

            default:
                //The default view is to return all terms in the glossary";
                return $this->glossaryViewByLetter('listall');
        }
    }

// End of Function

    /**
     *
     * Parse Function
     *
     * This method provides a testing area for users to examine and test how the glossary parser works
     * @return string Template - glossary_parse.php
     */
    private function parse() {

        if (isset($_POST['textToParse'])) {
            $text = $_POST['textToParse'];
            $parseText = $this->objGlossary->parse($text, $this->contextCode);
        } else {
            $text = '';
            $parseText = '';
        }
        $this->setVarByRef('outputText', $parseText);
        $this->setVarByRef('originalText', $text);

        $headerParams = $this->getJavascriptFile('domLib.js', 'htmlelements');
        $headerParams .= $this->getJavascriptFile('domTT.js', 'htmlelements');
        $headerParams .=$this->getJavascriptFile('domTT_drag.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        return "glossary_parse.php";
    }

    /**
     * Method to display a term along with definition in a popup window, along with URLs and See Alsos.
     *
     * This method does not popup window, but is called from a popup and displays by overriding the page template
     * In the glossary parser, it fits into an iframe called via javascript
     *
     * @param string $id: Record ID of the glossary term
     * @return string Template - glossary_viewresults.php
     */
    private function singlePopUpDisplay($id) {
        $this->setLayoutTemplate(null);

        // Check if the record exists
        if ($this->objGlossary->recordExists($id) != 0) {

            // Set the Record ID of the template
            $this->setVarByRef('id', $id);

            // Details of the Record
            $record = $this->objGlossary->listSingle($id);
            $this->setVarByRef('record', $record);

            // Get All URL Links for the Record
            $urlList = $this->objGlossaryUrls->fetchAllRecords($id);
            $this->setVarByRef('urlList', $urlList);

            // Number of URL Links for the Record
            $urlNum = $this->objGlossaryUrls->getNumRecords($id);
            $this->setVarByRef('urlNum', $urlNum);

            // See Also Links - List
            $seeAlsoList = $this->objGlossarySeeAlso->fetchAllRecords($id);
            $this->setVarByRef('seeAlsoList', $seeAlsoList);

            // See Also Links - Number
            $seeAlsoNum = $this->objGlossarySeeAlso->getNumRecords($id);
            $this->setVarByRef('seeAlsoNum', $seeAlsoNum);

            $images = $this->objGlossaryImages->getListImage($id);
            $this->setVarByRef('images', $images);

            // Suppress Page Elements
            $this->setVar('pageSuppressContainer', TRUE);
            $this->setVar('pageSuppressBanner', TRUE);
            $this->setVar('pageSuppressToolbar', TRUE);
            $this->setVar('suppressFooter', TRUE);
            $this->setVar('pageSuppressIM', TRUE);

            return "glossary_popup_show.php";
        }
    }

    /**
     * Method to view all words starting with a particular term
     *
     * @param string $letter: Can be the First Letter of the word OR 'listall' which returns all the terms
     * @return string Template - glossary_viewresults.php
     */
    private function glossaryViewByLetter($letter) {

        if ($letter == 'listall' or $letter == '') {
            $title = $this->objLanguage->languageText('mod_glossary_listAllTerms', 'glossary');
            $list = $this->objGlossary->fetchAllRecords($this->contextCode);
        } else {
            $title = $this->objLanguage->languageText('mod_glossary_showTermsStarting', 'glossary') . ' "' . $letter . '"';
            $list = $this->objGlossary->searchGlossaryDB($letter . '%', $this->contextCode);
        }

        $this->setVarByRef('title', $title);

        $this->setVarByRef('list', $list);

        return 'glossary_viewresults.php';
    }

    /**
     * Method to show a single term along with URLs, SeeAlsos and Images
     *
     * This method is called after a term has been added - as a confirmation page
     * to show the term is now part of the glossary
     *
     * @param string $id: Record ID of the glossary term
     * @return string Template - glossary_viewresults.php
     */
    private function showSingle($id) {
        // Get Record from Database
        $list = $this->objGlossary->showFullSingle($id, $this->contextCode);

        // Set Title
        $title = '"' . $list[0]['term'] . '" ' . $this->objLanguage->languageText('mod_glossary_hasBeenAdded', 'glossary');

        $this->setVarByRef('title', $title);

        $this->setVarByRef('list', $list);

        return "glossary_viewresults.php";
    }

    /**
     * Method to search the glossary for a particular word
     * If the user hasn't used any boolean features, this method will add % to the word to enhance search.
     *
     * @param string $term: Term to Search for
     * @return string Template - glossary_viewresults.php
     */
    private function glossarySearch($term) {
        // Check if the user has included %
        // If not, add them to give a better search result
        if (strpos($term, '%') === false && $term != '') {
            $searchWord = '%' . $term . '%';
        } else {
            $searchWord = $term;
        }

        // Set Title
        $title = $this->objLanguage->languageText('mod_glossary_searchForTerms', 'glossary') . ': "' . stripslashes($term) . '"';

        // Search for Words
        $list = $this->objGlossary->searchGlossaryDB($searchWord, $this->contextCode);

        // Send the Title to the template
        $this->setVarByRef('title', $title);

        //Send the Search Results to the Template
        $this->setVarByRef('list', $list);

        return "glossary_viewresults.php";
    }

    /**
     * Method to display the form to add a term to the glossary
     * @return string Template - glossary_viewresults.php
     */
    private function addTerm() {
        $others = $this->objGlossary->fetchAllRecords($this->contextCode);
        $numRecords = $this->objGlossary->getNumAllRecords($this->contextCode);
        $this->setVarByRef('others', $others);
        $this->setVarByRef('numRecords', $numRecords);

        return "glossary_add_tpl.php";
    }

    /**
     * Method to save a term in the glossary
     *
     * When the user enters a term, he can also add one URL and one See Also link
     * The function first inserts the term into the database, gets Last Inserted ID for that for the URL and See Also Link
     * Gets Variables via the Post Method
     */
    private function addConfirm() {
        // Adds a term to the database

        $term = stripslashes($this->getParam('term'));
        $definition = stripslashes($this->getParam('definition'));
        $url = stripslashes($this->getParam('url'));

        if (isset($_POST['seealso'])) {
            $seeAlso = $_POST['seealso'];
        } else {
            $seeAlso = '';
        }
        //add to activity log
        if ($this->eventsEnabled) {
            $message = $this->objUser->getsurname() . " " . $this->objLanguage->languageText('mod_glossary_addtermalert', 'glossary') . " - " . $term;
            $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                'link' => $this->uri(array()),
                'contextcode' => $this->contextCode,
                'author' => $this->objUser->fullname(),
                'description' => $message));

            $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                'link' => $this->uri(array()),
                'contextcode' => null,
                'author' => $this->objUser->fullname(),
                'description' => $message));
        }

        // Insert the term into the database
        $this->objGlossary->insertSingle(
                $term, $definition, $this->contextCode, $this->objUser->userId(), mktime()
        );

        // Get the Last Insert ID
        $id = $this->objGlossary->getLastInsertId();

        // Insert the See Also Link

        if ($seeAlso != '') {
            $this->objGlossarySeeAlso->insertSingle(
                    $seeAlso, $id, $this->objUser->userId(), mktime()
            );
        }

        // Insert the URL
        if ($url == 'http://') {
            $url = '';
        }

        if ($url != '') {
            $this->objGlossaryUrls->insertSingle(
                    $url, $id, $this->objUser->userId(), mktime()
            );
        }

        /*
          // Insert a default null image
          $image = null;
          $caption = null;
          $this->objGlossaryImages->insertImage(
          $id,
          $image,
          $caption,
          $this->objUser->userId(),
          mktime()
          ); */

        // Redirect to a page showing that the term has been successfully inserted into the database
        return $this->nextAction('listsingle', array('id' => $id));
    }

    /**
     * Method to display the form to edit a term
     *
     * @param string $id: Record ID of the Term
     */
    private function editTerm($id) {
        // First check if the term exists, else redirect to the home page
        If ($this->objGlossary->recordExists($id) == 0) {

            return "glossary_home.php";
        }

        // Set the Record ID to the template
        $this->setVarByRef('id', $id);

        // Details of the Record
        $record = $this->objGlossary->showFullSingle($id, $this->contextCode);

        $this->setVarByRef('record', $record[0]);

        $term = $record[0]['term'];

        // Number of URL Links for the Record
        $urlNum = $this->objGlossaryUrls->getNumRecords($id);
        $this->setVarByRef('urlNum', $urlNum);

        // Get All URL Links for the Record
        $urlList = $this->objGlossaryUrls->fetchAllRecords($id);
        $this->setVarByRef('urlList', $urlList);


        // See Also Links - Number
        $seeAlsoNum = $this->objGlossarySeeAlso->getNumRecords($id, $this->contextCode);
        $this->setVarByRef('seeAlsoNum', $seeAlsoNum);

        // See Also Links - List
        $seeAlsoList = $this->objGlossarySeeAlso->fetchAllRecords($id, $this->contextCode);
        $this->setVarByRef('seeAlsoList', $seeAlsoList);

        // Determines whether this record is linked to all other terms
        // If not, don't show link to add one
        $notLinkedToNum =
                $this->objGlossary->getNumAllRecords($this->contextCode) // All terms
                - 1 // This term
                - $seeAlsoNum // "See also" terms
        ;


//$this->objGlossarySeeAlso->findNotLinkedToNum($id);
        $this->setVarByRef('notLinkedToNum', $notLinkedToNum);

        $others = $this->objGlossarySeeAlso->findNotLinkedTo($id, $this->contextCode);
        $this->setVarByRef('others', $others);


        // Number of Records in the Glossary
        // This prevents a logical error in SeeAlsos. If this is the only record that exists, don't bother to show the See Also Form
        $numRecords = $this->objGlossary->getNumAllRecords($this->contextCode);
        $this->setVarByRef('numRecords', $numRecords);

        // Image edit fieldset
        //$this->setVarByRef('id', $id);

        $images = $this->objGlossaryImages->getListImage($id); //($id); $record[0]['item_id']
        $this->setVarByRef('images', $images);

        /**
         * After saving changes, the page redirects to edit.
         *
         * The user can only do one of the following edit tasks at a time
         *  - Edit Term Definition
         *  - Add / Delete See Alsos
         *  - Add / Delete See URLs
         *  - Add / Delete See Images
         *
         * Based on this, show an appropriate title - e.g. New URL has been added, etc.
         */
        switch ($this->getParam('message')) {
            case 'termupdated' : $message = '<strong>' . $term . '</strong> ' . $this->objLanguage->languageText('mod_glossary_hasBeenUpdated', 'glossary');
                break;

            case 'seealsoadded' : $message = $this->objLanguage->languageText('mod_glossary_seeAlsoHasBeenAdded', 'glossary') . ' <strong>' . $term . '</strong>';
                break;

            case 'seealsodeleted' : $message = $this->objLanguage->languageText('mod_glossary_seeAlsoHasBeenDeleted', 'glossary') . ' <strong>' . $term . '</strong>';
                break;

            case 'urladded' : $message = $this->objLanguage->languageText('mod_glossary_urlHasBeenAdded', 'glossary') . ' <strong>' . $term . '</strong>';
                break;

            case 'urldeleted' : $message = $this->objLanguage->languageText('mod_glossary_urlHasBeenDeleted', 'glossary') . ' <strong>' . $term . '</strong>';
                break;

            case 'imageadded':
                $message = $this->objLanguage->languageText('mod_glossary_imageadded', 'glossary') . ' <strong>' . $term . '</strong>';
                break;
            case 'imagedeleted':
                $message = $this->objLanguage->languageText('mod_glossary_imagedeleted', 'glossary') . ' <strong>' . $term . '</strong>';
                break;
            default:
                $message = '';
        }

        $this->setVarByRef('message', html_entity_decode($message));

        return "glossary_edit_tpl.php";
    }

    /**
     * Method to save changes to editing a term and definition in the database - Gets Variables via the Post Method
     */
    private function editConfirm() {
        // get Posted Variables
        $id = $this->getParam('id', null);
        $term = stripslashes($this->getParam('term'));
        $definition = stripslashes($this->getParam('definition'));

        // Variables to use in update method:
        // $term, $definition, $context, $userID, $dateLastUpdated
        $this->objGlossary->updateSingle(
                $id, $term, $definition, $this->contextCode, $this->objUser->userId(), mktime()
        );

        // Redirect back to the edit page
        return $this->nextAction('edit', array('id' => $id, 'message' => 'termupdated'));
    }

    /**
     * Method to save a SeeAlso in the database - Gets Variables via the Post Method
     */
    private function addSeeAlsoConfirm() {
        // Adds See Also link to the database

        $seeAlso = $_POST['seealso'];
        $id = $_POST['id'];

        // Parameters for method:
        // $item_id, $item_id2, $userId, $dateLastUpdated
        $this->objGlossarySeeAlso->insertSingle(
                $seeAlso, $id, $this->objUser->userId(), mktime()
        );

        // Redirect to edit page
        return $this->nextAction('edit', array('id' => $this->getParam('id', null), 'message' => 'seealsoadded'));
    }

    /**
     * Method to save a URL in the database - Gets Variables via the Post Method
     */
    private function addUrlConfirm() {
        // Add URL Link to the database

        $url = stripslashes($this->getParam('url'));
        $id = $this->getParam('id');

        // Parameters for insert method:
        // $url, $item_id, $userId, $dateLastUpdated


        $this->objGlossaryUrls->insertSingle(
                $url, $id, $this->objUser->userId(), mktime()
        );

        // Redirect to edit page
        return $this->nextAction('edit', array('id' => $this->getParam('id', null), 'message' => 'urladded'));
    }

    /**
     * Method to show the form requestiong confirmation in deleting a term from the glossary
     *
     * @param $id: ID of the term
     */
    private function deleteGlossary($id) {
        // Shows confirmation page for deleting a term

        $record = $this->objGlossary->showFullSingle($id, $this->contextCode);
        $this->setVarByRef('record', $record[0]);

        $this->setVarByRef('id', $id);

        return "glossary_delete_tpl.php";
    }

    /**
     * Method to delete a term from the glossary
     */
    private function deleteConfirm() {

        // Deletes term from database
        $canDelete = $this->getParam('delete');
        if ($canDelete == 'Yes') {
            $this->objGlossaryUrls->deleteSingle($this->getParam('id', null));
            $this->objGlossarySeeAlso->deleteSingle($this->getParam('id', null));
            $this->objGlossaryImages->deleteImage($this->getParam('id', null));
            $this->objGlossary->deleteSingle($this->getParam('id', null));
            return $this->nextAction('viewbyletter', array('letter' => 'listall'));
        } else {


            return $this->nextAction('view', array('id' => $this->getParam('id', null)));
        }
    }

    /**
     * Method to delete a URL from a term
     *
     * @param int $urlid: ID of the URL
     */
    private function deleteURL($urlid) {
        // Deletes a URL Link from Term
        if (!empty($urlid)) {
            $this->objGlossaryUrls->deleteSingleUrl($urlid, null);
        }

        return $this->nextAction('edit', array('id' => $this->getParam('id', null), 'message' => 'urldeleted'));
    }

    /**
     * Method to override isValid to enable administrators to perform certain action
     *
     * @param $action Action to be taken
     * @return boolean
     */
    public function isValid($action) {
        if ($this->objUser->isAdmin() || $this->objContextGroups->isContextLecturer()) {
            return TRUE;
        } else {
            return FALSE; //parent::isValid ( $action );
        }
    }

    /**
     * Method to delete a See Also link between two terms
     *
     * @param int $seeAlsoId: ID of the See Also link
     */
    private function deleteSeeAlso($seeAlsoId) {
        // Deletes a link between two terms
        if (!empty($seeAlsoId)) {
            $this->objGlossarySeeAlso->deleteSingleLink($seeAlsoId, null);
        }
        return $this->nextAction('edit', array('id' => $this->getParam('id'), 'message' => 'seealsodeleted'));
    }

    /**
     * This method shows the Header for the glossary
     * Header includes name of the glossary, link to add term, and alpha links
     *
     * @return string $header
     */
    private function showGlossaryHeader() {

        $header = '';

        // Header
        $objHeader = & $this->getObject('htmlheading', 'htmlelements');
        $objHeader->type = 1;
        $objHeader->str = $this->objLanguage->languageText('mod_glossary_glossaryFor', 'glossary') . ' ' . $this->contextTitle . ' ';

        $this->loadClass('link', 'htmlelements');

        // Check if user has permission to add
        if ($this->isValid('add')) {
            $glossaryAddLink = new link($this->uri(array('module' => 'glossary', 'action' => 'add')));
            $addIcon = & $this->getObject('geticon', 'htmlelements');
            $addIcon->setIcon('add');
            $addIcon->alt = $this->objLanguage->languageText('mod_glossary_addTermTitle', 'glossary');
            $addIcon->title = $this->objLanguage->languageText('mod_glossary_addTermTitle', 'glossary');

            $glossaryAddLink->link = $addIcon->show();
            $objHeader->str.=$glossaryAddLink->show();
        }

        $header .= $objHeader->show();

        // $objAlphabet=& $this->getObject('alphabet','display');
        // $linkarray=array('action'=>'viewbyletter','letter'=>'LETTER');
        // $url=$this->uri($linkarray,'glossary');
        // Browse by letter - string is too long if this is added
        //$header .= $this->objLanguage->languageText('mod_glossary_browsebyletter', 'glossary');
        //$header .= $objAlphabet->putAlpha($url, true, $this->objLanguage->languageText('mod_glossary_listAllWords', 'glossary'));

        $header .= $this->objGlossary->getGlossaryAlphaBrowse($this->contextCode);

        return $header;
    }

    /**
     * Method to show the footer of the glossary. Includes the Search form and link to add term
     * @return string $footer
     */
    private function showGlossaryFooter() {

        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');

        $footer = '<br /><br />';


        //Search Form
        $this->objSearchTitle = & $this->getObject('htmlheading', 'htmlelements');
        $this->objSearchTitle->type = 3;
        $this->objSearchTitle->str = $this->objLanguage->languageText('mod_glossary_searchglossary', 'glossary');

        $footer .= $this->objSearchTitle->show();

        $searchForm = new form('search');
        $searchForm->method = 'GET';

        $searchLabel = new label($this->objLanguage->languageText('mod_glossary_searchForWord', 'glossary'), 'input_term');
        $searchForm->addToForm($searchLabel->show() . ': ', null);

        $term = new textinput('term', stripslashes($this->getParam('term')));
        $term->size = 40;
        $searchForm->addToForm($term->show());

        $searchForm->addToForm(' '); // Spacer

        $submitButton = new button('submit', $this->objLanguage->languageText('word_search'));
        $submitButton->setToSubmit();

        $searchForm->addToForm($submitButton);

        $module = new textinput('module');
        $module->fldType = 'hidden';
        $module->value = 'glossary';

        $action = new textinput('action');
        $action->fldType = 'hidden';
        $action->value = 'search';

        $searchForm->addToForm($module->show() . $action->show());

        $footer .= $searchForm->show();
        $this->objUser = $this->getObject('user', 'security');
        // Check if user has permission to add / edit
        if ($this->objUser->isCourseAdmin()) {
            $addTermLink = new link($this->uri(array('module' => 'glossary', 'action' => 'add')));
            $addTermLink->link = $this->objLanguage->languageText('mod_glossary_addTerm', 'glossary');
            $footer .= '<p>' . $addTermLink->show();

            $parseLink = new link($this->uri(array('module' => 'glossary', 'action' => 'parse')));
            $parseLink->link = $this->objLanguage->languageText('mod_glossary_parseTest', 'glossary');

            //$footer .= ' / '.$parseLink->show();

            $footer .= '</p>';
        }

        return $footer;
    }

    /**
     * Show List of Images for a term
     *
     * @param string $id Record ID of the term
     * @return string Template - glossary_imagelist_tpl.php
     *
     */
    private function showImages($id) {
        $images = $this->objGlossaryImages->getListImage($id);

        $this->setVarByRef('id', $id);

        $this->setVarByRef('images', $images);

        $this->setVar('pageSuppressContainer', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('suppressFooter', TRUE);
        $this->setVar('pageSuppressIM', TRUE);

        return "glossary_imagelist_tpl.php";
    }

    /**
     * function to upload a file (image)
     */
    private function uploadImage() {

        /*
         *   Although all uploads are allowed, for the glossary only images are allowed
         *
         *  This script checks the mimetype, and if it doesn't have image in, it simply skips by.
         *
         */

        $item_id = $_POST['id'];

        //if (substr_count($_FILES['userFile']['type'], 'image')) {
        $caption = $_POST['caption'];
        //$result = array();
        //$result = $this->objUploader->uploadFile('userFile');//$_FILES['userFile'],$this->contextCode);
        $userId = $this->objUser->userId();
        $dateLastUpdated = mktime();
        $this->objGlossaryImages->insertImage($item_id, $_POST['userFile'], $caption, $userId, $dateLastUpdated);
        //}
        //return $this->nextAction('listimages', array('id' => $item_id));
        return $this->nextAction('edit', array('id' => $item_id, 'message' => 'imageadded'));
    }

    /**
     * Preview an Image - gets the image and sets it to html - <img src="thesrc">
     *
     * @param string $id Record ID of the Image
     * @param string $fname Filename of the Image
     * @return string Template - preview_image.php
     *
     */
    private function previewImage($id, $fname) {
        $this->setVarByRef('image', $id);
        $this->setVarByRef('fname', $fname);

        //Suppress Page Elements
        $this->setVar('pageSuppressContainer', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('suppressFooter', TRUE);
        $this->setVar('pageSuppressIM', TRUE);

        $headerParams = '
<script type="text/javascript" language="Javascript">
   function resizeWindow(getWidth, getHeight){
       this.window.resizeTo(getWidth,getHeight+80);
    }
   </script>';
        $this->appendArrayVar('headerParams', $headerParams);

        $bodyParams = 'onload="window.focus();" ';
        $this->setVarByRef('bodyParams', $bodyParams);


        return 'preview_image.php';
    }

    /**
     * Delete an Image from glossary and filemanager module
     *
     * @param string $imageFileId Record ID of the image
     * @param string $returnId  Record ID of the term, redirect to show images for term
     *
     */
    private function deleteImage($id, $returnId) {

        $this->objGlossaryImages->deleteImage($id);

        //$this->objUploadDB->deleteFile($imageFileId);
        //return $this->nextAction('listimages', array('id' => $returnId));
        return $this->nextAction('edit', array('id' => $returnId, 'message' => 'imagedeleted'));
    }

}

?>