<?php
/**
* management class extends object
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for managing the individual resources - editing and deleting archived resources and adding new resources
* @author Megan Watson
* @copyright (c) 2006 University of the Western Cape
* @version 0.1
*/

class management extends object
{
    /**
    * @var array $access The users access level within the class - set in the controller according to the group in which the user is a member.
    * @access protected
    */
    protected $access;
    
    /**
    * @var array $module The module for urls to redirect to.
    * @access protected
    */
    protected $module;
    
    /**
    * Constructor for the class
    *
    * @access public
    * @return void
    */
    public function init()
    {
        try{
            $this->dbSubmissions = $this->getObject('dbsubmissions', 'etd');
            $this->dbSubmissions->setDocType('thesis');
            $this->dbSubmissions->setSubmitType('etd');
    
            $this->dbThesis = $this->getObject('dbthesis', 'etd');
            $this->dbThesis->setSubmitType('etd');
    
            $this->dbStats = $this->getObject('dbstatistics', 'etd');
            $this->dbEmbargo = $this->getObject('dbembargo', 'etd');
            $this->dbDegrees = $this->getObject('dbdegrees', 'etd');
            $this->dbCitations = $this->getObject('dbcitations', 'etd');
            $this->dbProcess = $this->getObject('dbprocess', 'etd');
            $this->etdTools = $this->getObject('etdtools', 'etd');
            $this->dbDublinCore = $this->getObject('dbdublincore', 'etd');
            $this->files = $this->getObject('etdfiles', 'etd');
            $this->xmlMetadata = $this->getObject('xmlmetadata', 'etd');
    
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objLangCode = $this->getObject('languagecode', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objUser = $this->getObject('user', 'security');
            $this->objDate = $this->getObject('dateandtime', 'utilities');
    
            $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
            $this->objMsg = $this->getObject('timeoutmessage', 'htmlelements');
            $this->objRound = $this->newObject('roundcorners', 'htmlelements');
            $this->objHead = $this->newObject('htmlheading', 'htmlelements');
            $this->objEditor = $this->newObject('htmlarea', 'htmlelements');
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
    
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('tabbedbox', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('textarea', 'htmlelements');
            $this->loadClass('label', 'htmlelements');
            $this->loadClass('link', 'htmlelements');
    
            $this->userId = $this->objUser->userId();
            $this->fullName = $this->objUser->fullName();
            
            $this->access = $this->getSession('accessLevel', array());
            $this->module = 'etd';
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }
    
    /**
    * Method to set the submit type and the calling module
    *
    * @access public
    * @param string $type The submit type
    * @param string $module The calling module
    * @return string html
    */
    public function setSubmitType($type, $module)
    {
        $this->dbSubmissions->setSubmitType($type);
        $this->dbThesis->setSubmitType($type);
        $this->module = $module;
    }

    /**
    * Method to display a list of recent submissions requiring approval. And a link for submitting a new ETD.
    *
    * @access protected
    * @param array $data The submissions list
    * @return string html
    */
    protected function showSubmissions($data)
    {
        $head = $this->objLanguage->languageText('phrase_newsubmissions');
        $hdTitle = $this->objLanguage->languageText('word_title');
        $hdAuthor = $this->objLanguage->languageText('word_author');
        $hdStatus = $this->objLanguage->languageText('word_status');
        $hdDate = $this->objLanguage->languageText('phrase_dateadded');
        $lnSubmit = $this->objLanguage->languageText('mod_etd_submitnewresource', 'etd');
        $lbNone = $this->objLanguage->languageText('mod_etd_nonewresources', 'etd');

        $this->objHead->str = $head;
        $this->objHead->type = 1;
        $str = $this->objHead->show();

        // table containing submissions requiring approval / metadata
        $objTable = new htmltable();
        $objTable->cellpadding = '5';
        $objTable->cellspacing = '2';

        if(!empty($data)){
            $hdArr = array();
            $hdArr[] = $hdTitle;
            $hdArr[] = $hdAuthor;
            $hdArr[] = $hdStatus;
            $hdArr[] = $hdDate;

            $objTable->addHeader($hdArr);

            $class = 'odd';
            foreach($data as $item){
                $class = ($class == 'odd') ? 'even':'odd';

                $author = $item['dc_creator'];
                $title = $item['dc_title'];

                $objLink = new link($this->uri(array('action' => 'managesubmissions', 'mode' => 'shownewresource', 'submitId' => $item['id']), $this->module));
                if(empty($title)){
                    $objLink->link = $author;
                    $author = $objLink->show();
                }else{
                    $objLink->link = $title;
                    $title = $objLink->show();
                }

                $arrRow = array();
                $arrRow[] = $title;
                $arrRow[] = $author;
                $arrRow[] = $item['status'];
                $arrRow[] = $item['datecreated'];

                $objTable->addRow($arrRow, $class);
            }
        }else{
            $objTable->addRow(array($lbNone), 'noRecordsMessage');
        }

        $str .= '<p>'.$objTable->show().'</p>';

        if(in_array('manager', $this->access) || in_array('editor', $this->access)){
            // link to add a new submission
            $objLink = new link($this->uri(array('action' => 'managesubmissions', 'mode' => 'addsubmission'), $this->module));
            $objLink->link = $lnSubmit;
            $str .= '<p>'.$objLink->show().'</p>';
        }

        return $str;
    }

    /**
    * Method to display a form for searching the archive for documents that need editing or deleting.
    *
    * @access protected
    * @return string html
    */
    protected function showManage()
    {
        $head = $this->objLanguage->languageText('phrase_managerepository');
        $lbSearch = $this->objLanguage->languageText('mod_etd_searcharchivedresources', 'etd');
        $lbAuthor = $this->objLanguage->languageText('word_author');
        $lbTitle = $this->objLanguage->languageText('word_title');
        $lbKeywords = $this->objLanguage->languageText('word_keywords');
        $btnSearch = $this->objLanguage->languageText('word_search');

        $this->objHead->str = ucwords($head);
        $this->objHead->type = 1;
        $str = $this->objHead->show();

        $objTable = new htmltable();
        $objTable->cellspacing = 2;
        $objTable->cellpadding = 5;

        // Author
        $objLabel = new label($lbAuthor.': ', 'input_author');
        $objInput = new textinput('author', '', '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // Title
        $objLabel = new label($lbTitle.': ', 'input_ title');
        $objInput = new textinput('title', '', '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // Keywords
        $objLabel = new label($lbKeywords.': ', 'input_keywords');
        $objInput = new textinput('keywords', '', '', 60);

        $objTable->startRow();
        $objTable->addCell($objLabel->show(), '20%');
        $objTable->addCell($objInput->show());
        $objTable->endRow();

        $objButton = new button('search', $btnSearch);
        $objButton->setToSubmit();

        $objTable->addRow(array('', $objButton->show()));

        $objForm = new form('searcharchive', $this->uri(array('action' => 'managesubmissions', 'mode' => 'search'), $this->module));
        $objForm->addToForm('<p style="padding:5px;">'.$objTable->show().'</p>');

        $str .= '<br />'.$this->objRound->show('<h2>'.$lbSearch.'</h2>'.$objForm->show());

        return $str;
    }

    /**
    * Method to display the results from a search
    *
    * @access protected
    * @param array $data The search results
    * @param int $count The number of results
    * @return string html
    */
    protected function showResults($data, $count = 0)
    {
        $head = $this->objLanguage->languageText('phrase_requestedresources');
        $lbNone = $this->objLanguage->languageText('mod_etd_noresourcesmatchedsearchcriteria', 'etd');
        $lbAuthor = $this->objLanguage->languageText('word_author');
        $lbTitle = $this->objLanguage->languageText('word_title');
        $lbDate = $this->objLanguage->languageText('word_date');


        $this->objHead->str = $head.' ('.$count.')';
        $this->objHead->type = 3;
        $headStr = $this->objHead->show();

        if(!empty($data)){
            $hdArr = array();
            $hdArr[] = $lbAuthor;
            $hdArr[] = $lbTitle;
            $hdArr[] = $lbDate;

            $objTable = new htmltable();
            $objTable->cellspacing = 2;
            $objTable->cellpadding = 5;

            $objTable->addHeader($hdArr);

            $class = 'even';
            foreach($data as $item){
                $class = ($class == 'odd') ? 'even':'odd';

                $author = $item['dc_creator'];
                $title = $item['dc_title'];

                $url = $this->uri(array('action' => 'managesubmissions', 'mode' => 'showresource', 'submitId' => $item['submitid']), $this->module);
                $objLink = new link($url);

                if(empty($author)){
                    $objLink->link = $title;
                    $title = $objLink->show();
                }else{
                    $objLink->link = $author;
                    $author = $objLink->show();
                }

                $row = array();
                $row[] = $author;
                $row[] = $title;
                $row[] = $item['dc_date'];

                $objTable->addRow($row, $class);
            }
            $bodyStr = '<br />'.$objTable->show();
        }else{
            $bodyStr = '<p class="noRecordsMessage">'.$lbNone.'</p>';
        }

        $str = $this->objFeatureBox->showContent($headStr, $bodyStr);
        return $str;
    }

    /**
    * Method to get resources using the entered search criteria
    *
    * @access protected
    * @return array $data
    */
    protected function getResults()
    {
        $author = $this->getParam('author');
        $title = $this->getParam('title');
        $keywords = $this->getParam('keywords');

        $criteria = array();
        if(!empty($author)){
            $arrAuthor = explode(' ', $author);
            foreach($arrAuthor as $strAuthor){
                $criteria[] = array('field' => 'dc_creator', 'compare' => 'LIKE', 'value' => '%'.$strAuthor.'%');
            }
        }
        if(!empty($title)){
            $criteria[] = array('field' => 'dc_title', 'compare' => 'LIKE', 'value' => '%'.$title.'%');
        }
        if(!empty($keywords)){
            $arrKeywords = explode(', ', $keywords);
            foreach($arrKeywords as $strKeywords){
                $criteria[] = array('field' => 'dc_subject', 'compare' => 'LIKE', 'value' => '%'.$strKeywords.'%');
            }
        }

        $data = $this->dbSubmissions->fetchResources($criteria);

        return $data;
    }

    /**
    * Method to display a resource for editing.
    *
    * @access protected
    * @param array $data The resource data
    * @return string html
    */
    protected function editResource($data, $mode = 'saveresource', $nextmode = 'showresource')
    {
        if(!empty($data)){
            $head = $this->objLanguage->languageText('phrase_editresource');
        }else{
            $head = $this->objLanguage->languageText('phrase_newresource');
        }
        $lbMetaData = $this->objLanguage->languageText('word_metadata');
        $lbTitle = $this->objLanguage->languageText('phrase_documenttitle');
        $lbAltTitle = $this->objLanguage->languageText('phrase_alternatetitle');
        $lbAuthor = $this->objLanguage->languageText('word_authors');
        $lbCitation = $this->objLanguage->languageText('word_citation');
        $lbSummary = $this->objLanguage->languageText('word_abstract');
        $lbCountry = $this->objLanguage->languageText('word_country');
        $lbDocType = $this->objLanguage->languageText('phrase_documenttype');
        $lbSource = $this->objLanguage->languageText('word_source');
        $lbDateAdded = $this->objLanguage->languageText('phrase_dateadded');
        $lbContributor = $this->objLanguage->languageText('word_contributor');
        $lbContributorRole = $this->objLanguage->languageText('phrase_contributorrole');
        $lbPublisher = $this->objLanguage->languageText('word_publisher');
        $lbFaculty = $this->objLanguage->languageText('word_faculty');
        $lbDepartment = $this->objLanguage->languageText('word_department');
        $lbInstitution = $this->objLanguage->languageText('word_institution');
        $lbDegree = $this->objLanguage->languageText('phrase_degreeobtained');
        $lbLevel = $this->objLanguage->languageText('phrase_degreelevel');
        $lbGrantor = $this->objLanguage->languageText('word_grantor');
        $lbDate = $this->objLanguage->languageText('word_year');
        $lbFormat = $this->objLanguage->languageText('word_format');
        $lbRights = $this->objLanguage->languageText('word_rights');
        $lbRelationship = $this->objLanguage->languageText('word_relationship');
        $lbLanguage = $this->objLanguage->languageText('word_language');
        $lbAudience = $this->objLanguage->languageText('word_audience');
        $lbKeywords = $this->objLanguage->languageText('word_keywords');
        $lbPdf = $this->objLanguage->languageText('word_pdf');
        $lbThesisDiss = $this->objLanguage->languageText('phrase_thesisanddissertation');
        $lbCopyright = $this->objLanguage->languageText('word_copyright');
        $btnSave = $this->objLanguage->languageText('word_save');
        $btnCancel = $this->objLanguage->languageText('word_cancel');
        $lbConfigure = $this->objLanguage->languageText('mod_etd_configuredegreeinformation', 'etd');
        $errNoTitle = $this->objLanguage->languageText('mod_etd_errnotitle', 'etd');
        $errNoAuthor = $this->objLanguage->languageText('mod_etd_errnoauthor', 'etd');
        $errNoInstitution = $this->objLanguage->languageText('mod_etd_errnoinstitution', 'etd');
        $errNoKeywords = $this->objLanguage->languageText('mod_etd_erraddkeywords', 'etd');

        /* Create the icon to add / edit faculties, etc
        $this->objIcon->setIcon('organiseadmin');
        $this->objIcon->title = $lbConfigure;
        $objLink = new link('#');
        $objLink->link = $this->objIcon->show();
        $icConfig = $objLink->show();
        */

        $this->objHead->str = $head;
        $this->objHead->type = 1;
        $str = $this->objHead->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '5';
        $objTable->cellspacing = '5';

        // title
        $title = '';
        if(!empty($data['dc_title'])){
            $title = $data['dc_title'];
        }
        $objLabel = new label($lbTitle.': ', 'input_title');
        $objInput = new textinput('title', $title, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));
      
        // Alternate title
        $altTitle = '';
        if(!empty($data['dc_title_alternate'])){
            $altTitle = $data['dc_title_alternate'];
        }
        $objLabel = new label($lbAltTitle.': ', 'input_alttitle');
        $objInput = new textinput('alttitle', $altTitle, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // author
        $author = '';
        if(!empty($data['dc_creator'])){
            $author = $data['dc_creator'];
        }
        $objLabel = new label($lbAuthor.': ', 'input_author');
        $objInput = new textinput('author', $author, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // year
        $date = '';
        if(!empty($data['dc_date'])){
            $date = $data['dc_date'];
        }
        $objLabel = new label($lbDate.': ', 'input_date');
        $year = $this->etdTools->getYearSelect('date', $date);

        $objTable->addRow(array($objLabel->show(), $year));

        $objTable->addRow(array('<hr />', '<hr />'));

        // Degree
        $degree = '';
        if(!empty($data['thesis_degree_name'])){
            $degree = $data['thesis_degree_name'];
        }
        $objLabel = new label($lbDegree.': ', 'input_degree');
        $drpDegree = $this->dbDegrees->getDropList('degree', $degree);
        $objTable->addRow(array($objLabel->show(), $drpDegree));
        
        /* Level
        $level = '';
        if(!empty($data['thesis_degree_level'])){
            $level = $data['thesis_degree_level'];
        }
        $objLabel = new label($lbLevel.': ', 'input_level');
        $drpLevel = $this->etdTools->getDegreeLevels($level);
        $objTable->addRow(array($objLabel->show(), $drpLevel));
        */
        
        // Faculty
        $faculty = '';
        if(!empty($data['thesis_degree_faculty'])){
            $faculty = $data['thesis_degree_faculty'];
        }
        $objLabel = new label($lbFaculty.': ', 'input_faculty');
        $drpFaculty = $this->dbDegrees->getDropList('faculty', $faculty);
        $objTable->addRow(array($objLabel->show(), $drpFaculty));
        
        // Department
        $department = '';
        if(!empty($data['thesis_degree_discipline'])){
            $department = $data['thesis_degree_discipline'];
        }
        $objLabel = new label($lbDepartment.': ', 'input_department');
        $drpDepartment = $this->dbDegrees->getDropList('department', $department);
        $objTable->addRow(array($objLabel->show(), $drpDepartment));

        // Institution
        $institution = $this->objConfig->getinstitutionName();
        if(!empty($data['thesis_degree_grantor'])){
            $institution = $data['thesis_degree_grantor'];
        }
        $objLabel = new label($lbInstitution.': ', 'input_institution');
        $objInput = new textinput('institution', $institution, '', 60);
        $drpInstitution = $objInput->show();
        $objTable->addRow(array($objLabel->show(), $drpInstitution));

        $objTable->addRow(array('<hr />', '<hr />'));

        // language
        $language = 'English';
        if(!empty($data['dc_language'])){
            $language = $data['dc_language'];
        }
        $objLabel = new label($lbLanguage.': ', 'input_language');
        $objInput = new textinput('language', $language, '', 60);

        $objDrop = new dropdown('language');
        foreach($this->objLangCode->iso_639_2_tags->codes as $key => $item){
            $objDrop->addOption($item, $item);
        }
        $objDrop->setSelected($language);

        $objTable->addRow(array($objLabel->show(), $objDrop->show()));

        // country
        $country = '';
        if(!empty($data['dc_coverage'])){
            $country = $data['dc_coverage'];
        }
        $objLabel = new label($lbCountry.': ', 'input_country');
        $objTable->addRow(array($objLabel->show(), $this->etdTools->getCountriesDropdown($country)));

        // keywords
        $keywords = '';
        if(!empty($data['dc_subject'])){
            $keywords = $data['dc_subject'];
        }
        $objLabel = new label($lbKeywords.': ', 'input_keywords');
        $objText = new textarea('keywords', $keywords, 3, 58);

        $objTable->addRow(array($objLabel->show(), $objText->show()));

        // doc type
        $type = $lbThesisDiss;
        if(!empty($data['dc_type'])){
            $type = $data['dc_type'];
        }
        $objLabel = new label($lbDocType.': ', 'input_type');
        $objInput = new textinput('type', $type, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // format
        $format = $lbPdf;
        if(!empty($data['dc_format'])){
            $format = $data['dc_format'];
        }
        $objLabel = new label($lbFormat.': ', 'input_format');
        $objInput = new textinput('format', $format, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));
         
        $objTable->addRow(array('<hr />', '<hr />'));

        // contributor
        $contributor = '';
        if(!empty($data['dc_contributor'])){
            $contributor = $data['dc_contributor'];
        }
        $objLabel = new label($lbContributor.': ', 'input_contributor');
        $objInput = new textinput('contributor', $contributor, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // contributor role
        $contributorrole = '';
        if(!empty($data['dc_contributor_role'])){
            $contributorrole = $data['dc_contributor_role'];
        }
        $objLabel = new label($lbContributorRole.': ', 'input_contributorrole');
        $objInput = new textinput('contributorrole', $contributorrole, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // relationship
        $relationship = '';
        if(!empty($data['dc_relationship'])){
            $relationship = $data['dc_relationship'];
        }
        $objLabel = new label($lbRelationship.': ', 'input_relationship');
        $objInput = new textinput('relationship', $relationship, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // publisher
        $publisher = '';
        if(!empty($data['dc_publisher'])){
            $publisher = $data['dc_publisher'];
        }
        $objLabel = new label($lbPublisher.': ', 'input_publisher');
        $objInput = new textinput('publisher', $publisher, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // rights
        $rights = $lbCopyright.': '.$institution;
        if(!empty($data['dc_rights'])){
            $rights = $data['dc_rights'];
        }
        $objLabel = new label($lbRights.': ', 'input_rights');
        $objInput = new textinput('rights', $rights, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // source
        $source = '';
        if(!empty($data['dc_source'])){
            $source = $data['dc_source'];
        }
        $objLabel = new label($lbSource.': ', 'input_source');
        $objInput = new textinput('source', $source, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // audience
        $audience = '';
        if(!empty($data['dc_audience'])){
            $audience = $data['dc_audience'];
        }
        $objLabel = new label($lbAudience.': ', 'input_audience');
        $objInput = new textinput('audience', $audience, '', 60);

        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        // abstract
        $abstract = '';
        if(!empty($data['dc_description'])){
            $abstract = $data['dc_description'];
        }
        $this->objEditor->init('abstract', $abstract, '400px', '700px');
        $this->objEditor->setBasicToolBar();


        // Display the metadata in feature boxes
        $formStr = $this->objFeatureBox->show($lbMetaData, $objTable->show());
        $formStr .= $this->objFeatureBox->show($lbSummary, $this->objEditor->showFCKEditor());

        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show().'&nbsp;&nbsp;&nbsp;&nbsp;';

        $objButton = new button('cancel', $btnCancel);
        $objButton->setToSubmit();
        $formStr .= $objButton->show().'</p>';

        // hidden id fields
        $hidden = '';
        if(!empty($data['dcid'])){
            $objInput = new textinput('dcMetaId', $data['dcid'], 'hidden');
            $hidden .= $objInput->show();
        }
        if(!empty($data['metaid'])){
            $objInput = new textinput('thesisId', $data['metaid'], 'hidden');
            $hidden .= $objInput->show();
        }
        if(!empty($data['status'])){
            $objInput = new textinput('status', $data['status'], 'hidden');
            $hidden .= $objInput->show();
        }

        // Add to a form
        $objForm = new form('editresource', $this->uri(array('action' => 'savesubmissions', 'mode' => $mode, 'nextmode' => $nextmode), $this->module));
        $objForm->addToForm($formStr);
        $objForm->addToForm($hidden);
        
        // Title, author, keywords and institution are required
        $objForm->addRule('title', $errNoTitle, 'required');
        $objForm->addRule('author', $errNoAuthor, 'required');
        $objForm->addRule('institution', $errNoInstitution, 'required');
        $objForm->addRule('keywords', $errNoKeywords, 'required');
        $str .= $objForm->show();

        return $str;
    }

    /**
    * Method to save the new / updated metadata
    *
    * @access protected
    * @param string $submitId The submission id
    * @return void
    */
    protected function saveResource($submitId = NULL)
    {
        // Update the submissions table to show who modified it and when
        $status = $this->getParam('status', 'metadata');
        $submitId = $this->dbSubmissions->editSubmission($this->userId, $submitId, $status);

        // Save the dublincore metadata
        $metaId = $this->getParam('dcMetaId');
        $fields = array();
        $fields['dc_title'] = $this->getParam('title');
        $fields['dc_title_alternate'] = $this->getParam('alttitle');
        $fields['dc_creator'] = $this->getParam('author');
        $fields['dc_date'] = $this->getParam('date');
        $fields['dc_type'] = $this->getParam('type');
        $fields['dc_coverage'] = $this->getParam('country');
        $fields['dc_source'] = $this->getParam('source');
        $fields['dc_contributor'] = $this->getParam('contributor');
        $fields['dc_contributor_role'] = $this->getParam('contributorrole');
        $fields['dc_publisher'] = $this->getParam('publisher');
        $fields['dc_format'] = $this->getParam('format');
        $fields['dc_relationship'] = $this->getParam('relationship');
        $fields['dc_language'] = $this->getParam('language');
        $fields['dc_audience'] = $this->getParam('audience');
        $fields['dc_rights'] = $this->getParam('rights');
        $fields['dc_subject'] = $this->getParam('keywords');
        $fields['dc_description'] = $this->getParam('abstract');
        $metaId = $this->dbDublinCore->updateElement($fields, $metaId);

        // Save the extended thesis metadata
        $fields = array();
        $thesisId = $this->getParam('thesisId');
        $fields['submitid'] = $submitId;
        $fields['dcmetaid'] = $metaId;
        $fields['thesis_degree_name'] = $this->getParam('degree');
        $fields['thesis_degree_level'] = $this->getParam('degree');
        $fields['thesis_degree_discipline'] = $this->getParam('department');
        $fields['thesis_degree_faculty'] = $this->getParam('faculty');
        $fields['thesis_degree_grantor'] = $this->getParam('institution');
        $this->dbThesis->insertMetadata($fields, $thesisId);

        return $submitId;
    }

    /**
    * Method to save the new / updated metadata for a new submission not yet in the archive / database
    *
    * @access protected
    * @param string $submitId The submission id
    * @return void
    */
    protected function saveNewResource($submitId = NULL)
    {
        // Update the submissions table to show who modified it and when
        $submitId = $this->dbSubmissions->editSubmission($this->userId, $submitId, 'metadata', 4);

        // Save the dublincore metadata
        $dublin = array();
        $dublin['dc_title'] = $this->getParam('title');
        $dublin['dc_title_alternate'] = $this->getParam('alttitle');
        $dublin['dc_creator'] = $this->getParam('author');
        $dublin['dc_date'] = $this->getParam('date');
        $dublin['dc_type'] = $this->getParam('type');
        $dublin['dc_coverage'] = $this->getParam('country');
        $dublin['dc_source'] = $this->getParam('source');
        $dublin['dc_contributor'] = $this->getParam('contributor');
        $dublin['dc_contributor_role'] = $this->getParam('contributorrole');
        $dublin['dc_publisher'] = $this->getParam('publisher');
        $dublin['dc_format'] = $this->getParam('format');
        $dublin['dc_relationship'] = $this->getParam('relationship');
        $dublin['dc_language'] = $this->getParam('language');
        $dublin['dc_audience'] = $this->getParam('audience');
        $dublin['dc_rights'] = $this->getParam('rights');
        $dublin['dc_subject'] = $this->getParam('keywords');
        $dublin['dc_description'] = $this->getParam('abstract');

        // Save the extended thesis metadata
        $thesis = array();
        $thesis['thesis_degree_name'] = $this->getParam('degree');
        $thesis['thesis_degree_level'] = $this->getParam('degree');
        $thesis['thesis_degree_discipline'] = $this->getParam('department');
        $thesis['thesis_degree_faculty'] = $this->getParam('faculty');
        $thesis['thesis_degree_grantor'] = $this->getParam('institution');

        $extra = array();
        $extra['submitid'] = $submitId;

        $data['metadata']['dublincore'] = $dublin;
        $data['metadata']['thesis'] = $thesis;
        $data['metadata']['extra'] = $extra;

        $file = 'etd_'.$submitId;
        $this->xmlMetadata->saveToXml($data, $file);
        return $submitId;
    }

    /**
    * Method to move a resource from xml to the repository / database.
    *
    * @access protected
    * @param string $submitId The submissions table id
    * @return void
    */
    protected function approveResource($submitId)
    {   
        // Get xml metadata from file.
        $xmlData = $this->xmlMetadata->openXML('etd_'.$submitId);
        // Save metadata to database.
        $meta = $this->dbDublinCore->moveXmlToDb($xmlData, $submitId);
        
        // Check embargo & set start and end periods
        $this->dbEmbargo->setEmbargoDates($submitId);
        
        // Create Url to resource, save to metadata - dc_identifier, url
        $url = $this->uri(array('action' => 'viewtitle', 'id' => $meta['thesisId']), $this->module);
        $url1 = html_entity_decode($url);
        $url2 = urlencode($url1);
        $fields = array('dc_identifier' => $url1, 'url' => $url2);
        $this->dbDublinCore->updateElement($fields, $meta['dcId']);
        // Delete xml file
        $this->xmlMetadata->deleteXML('etd_'.$submitId);
        // Change submission status
        $this->dbSubmissions->changeApproval($submitId, $this->userId, 6, 'archived', 'public');
        
        // write resource to the xml map
        $this->writeToMap($url1);
        return TRUE;
    }

    /**
    * Method to add a newly archived resource to the xml sitemap for google.
    *
    * @access protected
    * @param string $url The url to the resource
    */
    protected function writeToMap($url)
    {
        $objMap = $this->getObject('etdmap', 'etd');
        $objMap->readMap();
        $objMap->addUrl($url, date('Y-m-d H:i:s'));
        $objMap->writeMap();
    }

    /**
    * Method to delete a resource
    *
    * @access protected
    * @param string $submitId The submissions table id
    * @return void
    */
    protected function deleteResource($submitId)
    {
        // delete document
//        $this->dbFiles->deleteAllFiles($submitId);

        // delete metadata
        $metaId = $this->getParam('dcMetaId');
        $this->dbDublinCore->deleteMetadata($metaId);

        // delete extended metadata
        $thesisId = $this->getParam('thesisId');
        $this->dbThesis->deleteMetadata($thesisId);

        // delete embargo
        $this->dbEmbargo->removeEmbargoIfExists($submitId);

        // delete submission
        $this->dbSubmissions->deleteSubmission($submitId);

        return TRUE;
    }

    /**
    * Method to delete a new resource that hasn't been archived yet
    *
    * @access protected
    * @param string $submitId The submissions table id
    * @return void
    */
    protected function deleteNewResource($submitId)
    {
        // delete document
//        $this->dbFiles->deleteAllFiles($submitId);

        // delete metadata in xml file
        $this->xmlMetadata->deleteXML('etd_'.$submitId);

        // delete embargo
        $this->dbEmbargo->removeEmbargoIfExists($submitId);

        // delete submission
        $this->dbSubmissions->deleteSubmission($submitId);

        return TRUE;
    }

    /**
    * Method to display a resource.
    *
    * @access protected
    * @param array $data The resource data
    * @return string html
    */
    protected function showResource($data, $editMode = 'editresource', $delMode = 'deleteresource', $nextMode = 'resources', $docMode = 'showresource')
    {
        $submitId = $this->getSession('submitId');
        $thesisId = '';
        $dcId = '';
        if(isset($data['metaid']) && !empty($data['metaid'])){
            $thesisId = $data['metaid'];
        }
        if(isset($data['dcid']) && !empty($data['dcid'])){
            $dcId = $data['dcid'];
        }

        $head = $this->objLanguage->languageText('word_resource');
        $lbMetaData = $this->objLanguage->languageText('word_metadata');
        $lbTitle = $this->objLanguage->languageText('phrase_documenttitle');
        $lbDocument = $this->objLanguage->languageText('word_document');
        $lbAuthor = $this->objLanguage->languageText('word_authors');
        $lbCitation = $this->objLanguage->languageText('word_citation');
        $lbSummary = $this->objLanguage->languageText('word_abstract');
        $lbCountry = $this->objLanguage->languageText('word_country');
        $lbDocType = $this->objLanguage->languageText('phrase_documenttype');
        $lbSource = $this->objLanguage->languageText('word_source');
        $lbDateAdded = $this->objLanguage->languageText('phrase_dateadded');
        $lbContributor = $this->objLanguage->languageText('word_contributor');
        $lbPublisher = $this->objLanguage->languageText('word_publisher');
        $lbFaculty = $this->objLanguage->languageText('word_faculty');
        $lbDegree = $this->objLanguage->languageText('phrase_degreeobtained');
        $lbLevel = $this->objLanguage->languageText('phrase_degreelevel');
        $lbGrantor = $this->objLanguage->languageText('word_grantor');
        $lbDate = $this->objLanguage->languageText('word_year');
        $lbFormat = $this->objLanguage->languageText('word_format');
        $lbRights = $this->objLanguage->languageText('word_rights');
        $lbRelationship = $this->objLanguage->languageText('word_relationship');
        $lbLanguage = $this->objLanguage->languageText('word_language');
        $lbAudience = $this->objLanguage->languageText('word_audience');
        $lbKeywords = $this->objLanguage->languageText('word_keywords');
        $lbAltTitle = $this->objLanguage->languageText('phrase_alternatetitle');
        $lbContributorRole = $this->objLanguage->languageText('phrase_contributorrole');
        $lbFaculty = $this->objLanguage->languageText('word_faculty');
        $lbDepartment = $this->objLanguage->languageText('word_department');
        $lbInstitution = $this->objLanguage->languageText('word_institution');
        $confirmDel = $this->objLanguage->languageText('mod_etd_confirmdeleteresource', 'etd');
        $lbApprove = $this->objLanguage->languageText('mod_etd_approveaddrepository', 'etd');
        $lbComplete = $this->objLanguage->languageText('mod_etd_completemetadata', 'etd');
        $lbPass = $this->objLanguage->languageText('mod_etd_passresource', 'etd');
        $lbCitation = $this->objLanguage->languageText('phrase_citationlist');
        $lbEdit = $this->objLanguage->languageText('phrase_editmetadata');
        $lbEmbargo = $this->objLanguage->languageText('phrase_embargoresource');

        $icons = '&nbsp;&nbsp;';
        
        // Managers and metadata editors / cataloguers can edit
        if(in_array('manager', $this->access) || in_array('editor', $this->access)){
            $this->objIcon->setIcon('editmetadata');
            $this->objIcon->title = $lbEdit;
            $objLink = new link($this->uri(array('action' => 'managesubmissions', 'mode' => $editMode), $this->module));
            $objLink->link = $this->objIcon->show();
            $icons .= $objLink->show();
        }
        
        // Only managers can approve resources for the repository
        // Each level approves the resource to move onto the next level
        if($editMode == 'editnewresource'){
            $icTitle = '';
            $lnArr = '';
            
            if(in_array('board', $this->access)){ 
                $icTitle = $lbPass;
                $lnArr = array('action' => 'savesubmissions', 'mode' => 'approveboard', 'save' => 'true');
            }

            if(in_array('editor', $this->access)){ 
                $icTitle = $lbComplete;
                $lnArr = array('action' => 'savesubmissions', 'mode' => 'approvemetadata', 'save' => 'true');
            }
                       
            if(in_array('manager', $this->access)){ 
                $icTitle = $lbApprove;
                $lnArr = array('action' => 'savesubmissions', 'mode' => 'approve', 'save' => 'true');
            }
            
            $this->objIcon->setIcon('etdapproval');
            $this->objIcon->title = $icTitle;
            $objLink = new link($this->uri($lnArr, $this->module));
            $objLink->link = $this->objIcon->show();
            $objLink->title = $icTitle;
            $icons .= '&nbsp;'.$objLink->show();
        }

        // Only managers can delete resources
        if(in_array('manager', $this->access)){
            $icons .= '&nbsp;'.$this->objIcon->getDeleteIconWithConfirm('', array('action' => 'savesubmissions', 'mode' => $delMode, 'nextmode' => $nextMode, 'save' => 'true', 'dcMetaId' => $dcId, 'thesisId' => $thesisId),  'etd', $confirmDel);
        }

        $this->objHead->str = $head.$icons;
        $this->objHead->type = 1;
        $str = $this->objHead->show();
        $str .= '<br />';
        
        $msg = $this->getSession('resourceMsg');
        if(isset($msg) && !empty($msg)){
            $this->unsetSession('resourceMsg');
            $this->objMsg->setMessage($msg);
            $str .= '<p>'.$this->objMsg->show().'</p>';
        }

        if(!empty($data)){
            $objTable = new htmltable();
            $objTable->cellpadding = 2;
            $objTable->cellspacing = 2;

            $objTable->addRow(array($lbTitle.': ', $data['dc_title']));
            $objTable->addRow(array($lbAltTitle.': ', $data['dc_title_alternate']));
            $objTable->addRow(array($lbAuthor.': ', $data['dc_creator']));
            $objTable->addRow(array($lbDate.': ', $data['dc_date']));
            $objTable->addRow(array('<hr />', '<hr />'));

            $objTable->addRow(array($lbDegree.': ', $data['thesis_degree_name']));
            $objTable->addRow(array($lbFaculty.': ', $data['thesis_degree_faculty']));
            $objTable->addRow(array($lbDepartment.': ', $data['thesis_degree_discipline']));
            $objTable->addRow(array($lbInstitution.': ', $data['thesis_degree_grantor']));
            $objTable->addRow(array('<hr />', '<hr />'));
            
            $objTable->addRow(array($lbLanguage.': ', $data['dc_language']));
            
            $country = '';
            if(isset($data['dc_coverage']) && !empty($data['dc_coverage'])){
                $country = $this->objLangCode->getName($data['dc_coverage']);
            }
            $objTable->addRow(array($lbCountry.': ', $country));
            $objTable->startRow();
            $objTable->addCell($lbKeywords.': ', '20%');
            $objTable->addCell($data['dc_subject']);
            $objTable->endRow();
            $objTable->addRow(array($lbDocType.': ', $data['dc_type']));
            $objTable->addRow(array($lbFormat.': ', $data['dc_format']));
            $objTable->addRow(array('<hr />', '<hr />'));
            
            $objTable->addRow(array($lbContributor.': ', $data['dc_contributor']));
            $objTable->addRow(array($lbContributorRole.': ', $data['dc_contributor_role']));
            $objTable->addRow(array($lbRelationship.': ', $data['dc_relationship']));
            $objTable->addRow(array($lbPublisher.': ', $data['dc_publisher']));
            $objTable->addRow(array($lbRights.': ', $data['dc_rights']));
            $objTable->addRow(array($lbSource.': ', $data['dc_source']));
            $objTable->addRow(array($lbAudience.': ', $data['dc_audience']));
            $objTable->addRow(array('<hr />', '<hr />'));

            // Display the metadata in feature boxes
            
            $str .= $this->objFeatureBox->showContent($lbMetaData, $objTable->show());
            $str .= $this->objFeatureBox->show($lbSummary, $data['dc_description']);
        }
        
        // Display the attached document for download or replacement
        $docStr = $this->showDocument($docMode);
        $str .= $this->objFeatureBox->show($lbDocument, $docStr);

        // Display the citation list
        $embargoStr = $this->showEmbargo();
        $str .= $this->objFeatureBox->show($lbEmbargo, $embargoStr);

        // Display the citation list
        $citationStr = $this->showCitationList($docMode);
        $str .= $this->objFeatureBox->show($lbCitation, $citationStr);

        return $str.'<br />';
    }

    /**
    * Method to get the attached document for viewing and updating.
    *
    * @access protected
    * @return string html
    */
    protected function showDocument($nextMode)
    {
        $submitId = $this->getSession('submitId');
        $data = $this->files->getFile($submitId);
        $hidden = '';

        $lbFileSize = $this->objLanguage->languageText('phrase_filesize');
        $lbFileName = $this->objLanguage->languageText('phrase_documentname');
        $lbDate = $this->objLanguage->languageText('phrase_datelastmodified');
        $lbType = $this->objLanguage->languageText('phrase_documenttype');
        $lbDownload = $this->objLanguage->languageText('phrase_downloaddocument');
        $lbUpload = $this->objLanguage->languageText('phrase_selectdocument');
        $btnUpload = $this->objLanguage->languageText('phrase_uploaddocument');
        $btnReplace = $this->objLanguage->languageText('phrase_replacedocument');
        $lbDocHidden = $this->objLanguage->languageText('mod_etd_documentavailonrequestonly', 'etd');
        $btnSet = $this->objLanguage->languageText('mod_etd_setasavailable', 'etd');
        $lbDocAvail = $this->objLanguage->languageText('mod_etd_documentavailtoall', 'etd');
        $btnUnSet = $this->objLanguage->languageText('mod_etd_setashidden', 'etd');

        $lbKb = $this->objLanguage->languageText('word_kb');
        $lbBytes = $this->objLanguage->languageText('word_bytes');
        $lbMb = $this->objLanguage->languageText('word_mb');
        $typePDF = $this->objLanguage->languageText('word_pdf');
        $typeWord = $this->objLanguage->languageText('phrase_msword');
        $typeExcel = $this->objLanguage->languageText('word_excel');
        $typeText = $this->objLanguage->languageText('phrase_plaintext');

        $objTable = new htmltable();

        if(!empty($data)){
            $btnUpload = $btnReplace;

            $objTable->startRow();
            $objTable->addCell($lbFileName.': ', '20%');
            $objTable->addCell($data[0]['filename']);
            $objTable->endRow();

            // format size
            $size = $data[0]['filesize'];
            if($size < 1000){
                $formSize = $size.'&nbsp;'.$lbBytes; // bytes
            }else if($size > 1000000){
                $formSize = round($size/1000000,2).'&nbsp;'.$lbMb; // megabytes
            }else{
                $formSize = round($size/1000).'&nbsp;'.$lbKb; // kilobytes
            }
            $objTable->addRow(array($lbFileSize.': ', $formSize));

            // format type
            $format = $data[0]['mimetype'];
            if(strpos($format, 'pdf')){
                $format = $typePDF;
            }else if(strpos($format, 'msword')){
                $format = $typeWord;
            }else if(strpos($format, 'excel')){
                $format = $typeExcel;
            }else if(!(strpos($format, 'text/plain')===FALSE)){
                $format = $typeText;
            }
            $objTable->addRow(array($lbType.': ', $format));

            // date
            if(!empty($data[0]['updated'])){
                $date = $this->objDate->formatDate($data[0]['updated']);
            }else{
                $date = $this->objDate->formatDate($data[0]['datecreated']);
            }
            $objTable->addRow(array($lbDate.': ', $date));

            // download
            $url = $data[0]['filepath'].$data[0]['storedname'];
            $this->objIcon->setIcon('fulltext');
            $this->objIcon->title = $lbDownload;

            $objLink = new link($url);
            $objLink->link = $this->objIcon->show();
            $objTable->addRow(array($lbDownload.': ', $objLink->show()));

            $objTable->addRow(array('&nbsp;'));

            // hidden fields
            $objInput = new textinput('id', $data[0]['id'], 'hidden');
            $hidden = $objInput->show();
        }
        
        // Only managers can replace existing documents
        if(in_array('manager', $this->access) || in_array('editor', $this->access)){
            
            $objInput = new textinput('submitId', $submitId, 'hidden');
            $hidden .= $objInput->show();
    
            // Section to upload a new / replace an existing document
            $objLabel = new label($lbUpload.': ', 'input_fileupload');
            $objInput = new textinput('fileupload', '', 'file', 60);
    
            $objTable->addRow(array($objLabel->show(), $objInput->show()));
    
            $objButton = new button('save', $btnUpload);
            $objButton->setToSubmit();
            $objTable->addRow(array('', $objButton->show()));
    
            $objForm = new form('upload', $this->uri(array('action' => 'savesubmissions', 'mode' => 'uploaddoc', 'nextmode' => $nextMode), $this->module));
            $objForm->extra = 'enctype="multipart/form-data"';
            $objForm->addToForm($objTable->show());
            $objForm->addToForm($hidden);
    
            $str = $objForm->show();
        }else{
            $str = $objTable->show();
        }

        return $str;
    }
    
    /**
    * Method to display embargo request
    *
    * @access protected
    * @return string html
    */
    protected function showEmbargo()
    {   
        $submitId = $this->getSession('submitId');
        $data = $this->dbEmbargo->getEmbargoRequest($submitId);
        $str = '';
           
        $lbPeriod = $this->objLanguage->languageText('word_duration');
        $months = $this->objLanguage->languageText('word_months');
        $lbReason = $this->objLanguage->languageText('mod_etd_reasonforembargo', 'etd');
        $btnSave = $this->objLanguage->languageText('word_save');
        $btnDelete = $this->objLanguage->languageText('word_delete');    
        
        $period = isset($data['period']) ? $data['period'] : '12';
        $objLabel = new label($lbPeriod, 'input_period');
        $objDrop = new dropdown('period');
        for($i = 3; $i <= 12; $i += 3){
            $objDrop->addoption($i, $i.' '.$months);
        }
        $objDrop->setSelected($period);
        $formStr = '<p>'.$objLabel->show().'&nbsp;&nbsp;'.$objDrop->show().'</p>';
        
        $reason = isset($data['reason']) ? $data['reason'] : '';
        $objLabel = new label($lbReason, 'input_reason');
        $objText = new textarea('reason', $reason);
        $formStr .= '<p>'.$objLabel->show().': <br />'.$objText->show().'</p>';
                
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show();
            
        $objButton = new button('delete', $btnDelete);
        $objButton->setToSubmit();
        $formStr .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();
        
        $id = isset($data['id']) ? $data['id'] : '';
        $objInput = new textinput('id', $id, 'hidden');
        $formStr .= $objInput->show();
                
        $formStr .= '</p>';
            
        $objForm = new form('request', $this->uri(array('action' => 'savesubmissions', 'mode' => 'embargo', 'nextmode' => 'shownewresource', 'save' => 'save'), $this->module));
        $objForm->addToForm($formStr);
        $str = $objForm->show();
                
        return $str;
    }

    /**
    * Method to display the list of citations associated with a resource for adding / editing
    *
    * @access protected
    * @return string html
    */
    protected function showCitationList($nextMode)
    {
        $submitId = $this->getSession('submitId');
        $data = $this->dbCitations->getList($submitId);
        $str = '';
        
        $lnUpdate = $this->objLanguage->languageText('phrase_updatecitationlist');
        
        if(in_array('manager', $this->access) || in_array('editor', $this->access)){
            $objLink = new link($this->uri(array('action' => 'managesubmissions', 'mode' => 'updatecitation', 'nextmode' => $nextMode), $this->module));
            $objLink->link = $lnUpdate;
            $str = '<p>'.$objLink->show().'</p>';
        }
        
        if(!empty($data) && !($data === FALSE)){
            $str .= $data['citation_list'];
        }
        return $str;
    }
    
    /**
    * Method to display the citation list in a form for updating
    *
    * @access protected
    * @param array $data The citation list
    * @return string html
    */
    protected function editCitation($nextMode, $data = NULL)
    {
        $list = ''; $id = '';
        if(!empty($data)){
            $list = $data['citation_list'];
            $id = $data['id'];
        }
        
        $head = $this->objLanguage->languageText('phrase_updatecitationlist');
        $btnSave = $this->objLanguage->languageText('word_save');
        $btnCancel = $this->objLanguage->languageText('word_cancel');
        
        $this->objEditor->init('list', $list, '400px', '700px');
        $this->objEditor->setBasicToolBar();
        $formStr = $this->objEditor->showFCKEditor();
                
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show().'&nbsp;&nbsp;&nbsp;';
        
        $objButton = new button('cancel', $btnCancel);
        $objButton->setToSubmit();
        $formStr .= $objButton->show().'</p>';
        
        if(!empty($id)){
            $objInput = new textinput('id', $id, 'hidden');
            $formStr .= $objInput->show();
        }

        $objForm = new form('updatelist', $this->uri(array('action' => 'savesubmissions', 'mode' => 'savecitation', 'nextmode' => $nextMode), $this->module));
        $objForm->addToForm($formStr);
        $str = $objForm->show();
        
        return $this->objFeatureBox->show($head, $str);
    }

    /**
    * Entry portal into class
    *
    * @access public
    * @param string $mode The mode / action to perform within the class
    * @return string html
    */
    public function show($mode)
    {
        switch($mode){
            case 'addsubmission':
                $this->unsetSession('submitId');
                return $this->editResource('', 'savenewresource', 'shownewresource');

            case 'editresource':
                $submitId = $this->getSession('submitId');
                $data = $this->dbSubmissions->getSubmission($submitId);
                return $this->editResource($data);
                break;

            case 'editnewresource':
                $submitId = $this->getSession('submitId');
                $xml = $this->xmlMetadata->openXML('etd_'.$submitId);
                $dublin = $xml['metadata']['dublincore'];
                $data = array_merge($dublin, $xml['metadata']['thesis']);
                return $this->editResource($data, 'savenewresource', 'shownewresource');
                break;

            case 'deleteresource':
                $submitId = $this->getSession('submitId');
                return $this->deleteResource($submitId);
                break;

            case 'deletenewresource':
                $submitId = $this->getSession('submitId');
                return $this->deleteNewResource($submitId);
                break;

            case 'saveresource':
                $submitId = $this->getSession('submitId');
                $submitId = $this->saveResource($submitId);
                $this->setSession('submitId', $submitId);
                return $submitId;
                break;

            case 'savenewresource':
                $submitId = $this->getSession('submitId');
                $submitId = $this->saveNewResource($submitId);
                $this->setSession('submitId', $submitId);
                return $submitId;
                break;
                
            case 'embargo':
                $id = $this->getParam('id');
                $submitId = $this->getSession('submitId');
                $save = $this->getParam('save');
                $delete = $this->getParam('delete');
                
                // check if save selected
                if(isset($save) && !empty($save)){
                    $this->dbEmbargo->saveEmbargoRequest($submitId, $id);
                }
                
                // check if delete selected
                if(isset($delete) && !empty($delete)){
                    $this->dbEmbargo->removeEmbargo($id);
                }
                break;
                
            case 'approve':
                $submitId = $this->getSession('submitId');
                $this->approveResource($submitId);
                $this->dbStats->recordUpload($submitId);
                break;
                                
            case 'approvemetadata':
                $submitId = $this->getSession('submitId');
                // Set the next level of submission - management
                $this->dbSubmissions->changeApproval($submitId, $this->userId, 5, 'metadata', 'private');
                break;

            case 'approveboard':
                $submitId = $this->getSession('submitId');
                // Get the next level of submission - metadata editing / management
                $nextStep = $this->dbProcess->getNextStep(3);
                $this->dbSubmissions->changeApproval($submitId, $this->userId, $nextStep, 'metadata', 'private');
                break;
                
            case 'showresource':
                $submitId = $this->getParam('submitId');
                if(!isset($submitId) || empty($submitId)){
                    $submitId = $this->getSession('submitId');
                }else{
                    $this->setSession('submitId', $submitId);
                }
                $data = $this->dbSubmissions->getSubmission($submitId);
                return $this->showResource($data);

            case 'shownewresource':
                $submitId = $this->getParam('submitId');
                if(!isset($submitId) || empty($submitId)){
                    $submitId = $this->getSession('submitId');
                }else{
                    $this->setSession('submitId', $submitId);
                }
                $xml = $this->xmlMetadata->openXML('etd_'.$submitId);
                $dublin = $xml['metadata']['dublincore'];
                $data = array_merge($dublin, $xml['metadata']['thesis']);
                return $this->showResource($data, 'editnewresource', 'deletenewresource', '', 'shownewresource');

            case 'resources':
                $this->unsetSession('submitId');
                return $this->showManage();

            case 'uploaddoc':
                $submitId = $this->getSession('submitId');
                $id = $this->getParam('id');
                $result = $this->files->uploadFile($submitId, $id);
                $this->setSession('resourceMsg', $result);
                break;
                
            case 'updatecitation':
                $submitId = $this->getSession('submitId');
                $nextMode = $this->getParam('nextmode');
                $data = $this->dbCitations->getList($submitId);
                return $this->editCitation($nextMode, $data);
                break;
                
            case 'savecitation':
                $submitId = $this->getSession('submitId');
                $id = $this->getParam('id');
                $list = $this->getParam('list');
                $this->dbCitations->addList($list, $submitId, $id);
                break;

            case 'search':
                $this->unsetSession('submitId');
                $results = $this->getResults();
                $str = $this->showManage().'<br />';
                if(!empty($results)){
                    $str .= $this->showResults($results[0], $results[1]);
                }else{
                    $str .= $this->showResults('');
                }
                return $str;

            default:
                $this->unsetSession('submitId');
                $list = array();
                // Get submissions by the users groups
                if(in_array('manager', $this->access)){
                    $list[] = '5';
                    $list[] = '4';
                }
                if(in_array('editor', $this->access)){
                    $list[] = '4';
                }
                if(in_array('board', $this->access)){
                    $list[] = '3';
                }
                
                $data = $this->dbSubmissions->getNewSubmissions($list);
                return $this->showSubmissions($data);
        }
    }
}
?>