<?php
/**
* submit class extends object
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for students submissions to the archive
* Functions allow for new submissions, pending submissions, copyright acceptance, document uploading
*
* @author Megan Watson
* @copyright (c) 2006 University of the Western Cape
* @version 0.1
*/

class submit extends object
{
    /**
    * Constructor for the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        try{
            $this->files = $this->getObject('etdfiles', 'etd');
            $this->dbSubmissions = $this->getObject('dbsubmissions', 'etd');
            $this->dbSubmissions->setDocType('thesis');
            $this->dbSubmissions->setSubmitType('etd');
    
            $this->dbThesis = $this->getObject('dbthesis', 'etd');
            $this->dbThesis->setSubmitType('etd');
    
            $this->etdTools = $this->getObject('etdtools', 'etd');
            $this->dbEmbargo = $this->getObject('dbembargo', 'etd');
            $this->dbDegrees = $this->getObject('dbdegrees', 'etd');
            $this->dbProcess = $this->getObject('dbprocess', 'etd');
            $this->dbCopyright = $this->getObject('dbcopyright', 'etd');
            $this->dbDublinCore = $this->getObject('dbdublincore', 'etd');
            $this->xmlMetadata = $this->getObject('xmlmetadata', 'etd');
    
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objLangCode = $this->getObject('languagecode', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objUser = $this->getObject('user', 'security');
            $this->objDate = $this->getObject('dateandtime', 'utilities');
    
            $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
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
            $this->firstName = $this->objUser->getFirstname($this->userId);
            $this->surname = $this->objUser->getSurname($this->userId);
            $this->studentNum = $this->objUser->getStaffNumber($this->userId);
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }

    /**
    * Method to display a list of recent submissions requiring approval. And a link for submitting a new ETD.
    *
    * @access private
    * @param array $data The submissions list
    * @return string html
    */
    private function showSubmissions($data)
    {
        $head = $this->objLanguage->languageText('phrase_newsubmissions');
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
        }else{
            $objTable->addRow(array($lbNone), 'noRecordsMessage');
        }

        $str .= $objTable->show();

        // link to add a new submission
        $objLink = new link($this->uri(array('action' => 'submit', 'mode' => 'addsubmission')));
        $objLink->link = $lnSubmit;
        $str .= '<p>'.$objLink->show().'</p>';

        return $str;
    }

    /**
    * Method to display a resource for editing.
    *
    * @access private
    * @param array $data The resource data
    * @return string html
    */
    private function editResource($data)
    {
        if(!empty($data)){
            $head = $this->objLanguage->languageText('phrase_editresource');
        }else{
            $head = $this->objLanguage->languageText('phrase_newresource');
        }
        $lbMetaData = $this->objLanguage->languageText('word_metadata');
        $lbTitle = $this->objLanguage->languageText('phrase_documenttitle');
        $lbAltTitle = $this->objLanguage->languageText('phrase_alternatetitle');
        $lbAuthorFirst = $this->objLanguage->languageText('phrase_authorfirstname');
        $lbAuthorSurname = $this->objLanguage->languageText('phrase_authorsurname');
        $lbNumber = $this->objLanguage->languageText('phrase_studentnumber');
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
        $errNoTitle = $this->objLanguage->languageText('mod_etd_errnotitle', 'etd');
        $errCapitalisation = $this->objLanguage->languageText('mod_etd_errcapitalisation', 'etd');

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
        $author = $this->surname.', '.$this->firstName;
        if(!empty($data['dc_creator'])){
            $author = $data['dc_creator'];
        }
        $objInput = new textinput('author', $author, 'hidden');

        $objTable->addRow(array($lbAuthorFirst.': ', $this->firstName));
        $objTable->addRow(array($lbAuthorSurname.': ', $this->surname.$objInput->show()));
        $objTable->addRow(array($lbNumber.': ', $this->studentNum));
        
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


        // Display the metadata in tabbed boxes
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

        // Add to a form
        $objForm = new form('editresource', $this->uri(array('action' => 'savesubmit', 'mode' => 'saveresource', 'nextmode' => 'showresource')));
        $objForm->addToForm($formStr);
        $objForm->addToForm($hidden);
        $objForm->addRule('title', $errNoTitle, 'required');
//        $objForm->addRule('title', $errCapitalisation, 'titlecase');
        $str .= $objForm->show();

        return $str;
    }

    /**
    * Method to save the new / updated metadata
    *
    * @access private
    * @param string $submitId The submission id
    * @return
    */
    private function saveResource($submitId = NULL)
    {
        // Update the submissions table to show who modified it and when
        $submitId = $this->dbSubmissions->editSubmission($this->userId, $submitId);

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
    * Method to delete a resource
    *
    * @access private
    * @param string $submitId The submissions table id
    * @return
    */
    private function deleteResource($submitId)
    {
        // delete metadata in xml file
        $this->xmlMetadata->deleteXML('etd_'.$submitId);

        // delete submission
        $this->dbSubmissions->deleteSubmission($submitId);

        return TRUE;
    }

    /**
    * Method to display a resource.
    *
    * @access private
    * @param array $data The resource data
    * @return string html
    */
    private function showResource($data)
    {
        $submitId = $this->getSession('submitId');

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
        $confirmDel = $this->objLanguage->languageText('mod_etd_confirmdeleteresource', 'etd');
        $lbEmbargo = $this->objLanguage->languageText('phrase_requestembargo');
        $lbAltTitle = $this->objLanguage->languageText('phrase_alternatetitle');
        $lbContributorRole = $this->objLanguage->languageText('phrase_contributorrole');
        $lbFaculty = $this->objLanguage->languageText('word_faculty');
        $lbDepartment = $this->objLanguage->languageText('word_department');
        $lbInstitution = $this->objLanguage->languageText('word_institution');
        $lbSubmit = $this->objLanguage->languageText('word_submit');

        $icons = '&nbsp;&nbsp;';
        $icons .= $this->objIcon->getEditIcon($this->uri(array('action' => 'submit', 'mode' => 'editresource')));
        $icons .= $this->objIcon->getDeleteIconWithConfirm('', array('action' => 'savesubmit', 'mode' => 'deleteresource', 'nextmode' => '', 'save' => 'true'),  'etd', $confirmDel);

        $this->objHead->str = $head.$icons;
        $this->objHead->type = 1;
        $str = $this->objHead->show();

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

            // Display the metadata in tabbed boxes
            $str .= '<br />'.$this->objFeatureBox->showContent($lbMetaData, $objTable->show());

            $str .= $this->objFeatureBox->show($lbSummary, $data['dc_description']);
        }

        // Display the attached document for download or replacement
        $docStr = $this->showDocument();
        $str .= $this->objFeatureBox->show($lbDocument, $docStr);

        // Display the embargo request
        //$embargoStr = $this->showEmbargo();
        //$str .= $this->objFeatureBox->show($lbEmbargo, $embargoStr);

        // Display the form for submitting to supervisor for approval
        $submitStr = $this->showSubmit();
        $str .= $this->objFeatureBox->show($lbSubmit, $submitStr);

        return $str.'<br />';
    }

    /**
    * Method to get the attached document for viewing and updating.
    *
    * @access private
    * @return string html
    */
    private function showDocument()
    {
        // set php.ini to 250MB
        //ini_set('post_max_size', '250M');
        //ini_set('upload_max_filesize', '250M');
            
        $submitId = $this->getSession('submitId');
        $data = $this->files->getFile($submitId);
        $filePath = isset($data[0]['filepath']) ? $data[0]['filepath'] : '';
        $hidden = '';

        $lbFileSize = $this->objLanguage->languageText('phrase_filesize');
        $lbFileName = $this->objLanguage->languageText('phrase_documentname');
        $lbDate = $this->objLanguage->languageText('phrase_datelastmodified');
        $lbType = $this->objLanguage->languageText('phrase_documenttype');
        $lbDownload = $this->objLanguage->languageText('phrase_downloaddocument');
        $lbUpload = $this->objLanguage->languageText('phrase_selectdocument');
        $btnUpload = $this->objLanguage->languageText('phrase_uploaddocument');
        $hdUpload = $this->objLanguage->languageText('phrase_uploaddocuments');
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

        $str='';
        if(!empty($data)){
            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->cellspacing = '2';
            
            $hdArr = array();
            $hdArr[] = $lbFileName;
            $hdArr[] = $lbType;
            $hdArr[] = $lbFileSize;
            $hdArr[] = $lbDate;
            $hdArr[] = '';
            
            $objTable->addHeader($hdArr);
            
            $class = 'even';
            foreach($data as $item){
                $class = ($class == 'odd') ? 'even' : 'odd';
                
                // format size
                $size = $item['filesize'];
                if($size < 1000){
                    $formSize = $size.'&nbsp;'.$lbBytes; // bytes
                }else if($size > 1000000){
                    $formSize = round($size/1000000,2).'&nbsp;'.$lbMb; // megabytes
                }else{
                    $formSize = round($size/1000).'&nbsp;'.$lbKb; // kilobytes
                }

                // format type
                $format = $item['mimetype'];
                if(strpos($format, 'pdf')){
                    $format = $typePDF;
                }else if(strpos($format, 'msword')){
                    $format = $typeWord;
                }else if(strpos($format, 'excel')){
                    $format = $typeExcel;
                }else if(!(strpos($format, 'text/plain')===FALSE)){
                    $format = $typeText;
                }
    
                // date
                if(!empty($item['updated'])){
                    $date = $this->objDate->formatDate($item['updated']);
                }else{
                    $date = $this->objDate->formatDate($item['datecreated']);
                }
    
                // download
                $url = $filePath.$item['storedname'];
                $this->objIcon->setIcon('fulltext');
                $this->objIcon->title = $lbDownload;
    
                $objLink = new link($url);
                $objLink->link = $this->objIcon->show();
                $lnDownload = $objLink->show();
    
                $rowArr = array();
                $rowArr[] = $item['filename'];
                $rowArr[] = $format;
                $rowArr[] = $formSize;
                $rowArr[] = $date;
                $rowArr[] = $lnDownload;
                
                $objTable->addRow($rowArr, $class);
                
                

            // hidden fields
            $objInput = new textinput('id', $data[0]['id'], 'hidden');
            $hidden = $objInput->show();
            
            }
            $objTable->addRow(array('&nbsp;'));
            $str = $objTable->show();
        }



        // Create the form for uploading documents
        $objTable = new htmltable();
        $objTable->cellpadding = '5';
        $objTable->cellspacing = '2';
        
        $objLabel = new label($lbUpload.': ', 'input_fileupload');
        $objInput = new textinput('fileupload', '', 'file', 60);
        $objTable->addRow(array($objLabel->show(), $objInput->show()));

        $objButton = new button('save', $btnUpload);
        $objButton->setToSubmit();
        $objTable->addRow(array('', $objButton->show()));
        
        $objInput = new textinput('submitId', $submitId, 'hidden');
        $hidden = $objInput->show();
        
        $objForm = new form('upload', $this->uri(array('action' => 'savesubmit', 'mode' => 'uploaddoc', 'nextmode' => 'showresource')));
        $objForm->extra = "enctype='multipart/form-data'";
        $objForm->addToForm($objTable->show());
        $objForm->addToForm($hidden);

        $objTab = new tabbedbox();
        $str .= $objTab->buildTabbedBox($hdUpload, $objForm->show());
        
        return $str;
    }

    /**
    * Method to display the form for requesting an embargo / display the request
    *
    * @access private
    * @return string html
    */
    private function showEmbargo()
    {
        $submitId = $this->getSession('submitId');
        $data = $this->dbEmbargo->getEmbargoRequest($submitId);
        $reason = ''; $period = ''; $requested = FALSE;
        if(!empty($data)){
            $requested = TRUE;
            $reason = $data['request'];
            $period = $data['period'];
        }
        
        $lbEmbargo = $this->objLanguage->languageText('mod_etd_requestembargoforperiod', 'etd');
        $lbSubmitted = $this->objLanguage->languageText('mod_etd_embargorequestsubmit', 'etd');
        $lbReason = $this->objLanguage->languageText('word_reason');
        $lbPeriod = $this->objLanguage->languageText('word_period');
        $months = $this->objLanguage->languageText('word_months');
        $btnRequest = $this->objLanguage->languageText('phrase_saverequest');
        $btnDelete = $this->objLanguage->languageText('phrase_deleterequest');

        if($requested){
            $str = '<p class="warning">'.$lbSubmitted.'</p>';
        }else{
            $str = '<p>'.$lbEmbargo.'</p>';
        }

        $objLabel = new label($lbReason.': ', 'input_reason');
        $objText = new textarea('reason', $reason, '4', '100');
        $formStr = $objLabel->show().'<br />'.$objText->show();

        $objLabel = new label($lbPeriod.': ', 'input_reason');
        $objDrop = new dropdown('period');
        //for($i = $config['shortPeriod']; $i <= $config['longPeriod']; $i += $config['incPeriod']){
        /*for($i = 0; $i <= 12; $i += 3){
            $objDrop->addoption($i, $i.' '.$months);
        }*/
        $objDrop->addoption('12', '12 '.$months);
        $objDrop->setSelected($period);
        $formStr .= '<p>'.$objLabel->show().'&nbsp;&nbsp;'.$objDrop->show().'</p>';

        $objButton = new button('request', $btnRequest);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show();
        
        if($requested){
            $objButton = new button('delete', $btnDelete);
            $objButton->setToSubmit();
            $formStr .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();
        }
        $formStr .= '</p>';

        $objForm = new form('request', $this->uri(array('action' => 'savesubmit', 'mode' => 'embargo', 'nextmode' => 'showresource', 'save' => 'save')));
        $objForm->addToForm($formStr);
        $str .= '<p>'.$objForm->show().'</p>';

        return $str;
    }

    /**
    * Method to display the form for submitting to supervisor for approval / examination
    *
    * @access private
    * @return string html
    */
    private function showSubmit()
    {
        $lbApproval = $this->objLanguage->languageText('mod_etd_submitforexamination', 'etd');
        $btnSubmit = $this->objLanguage->languageText('word_submit');

        $str = '<p>'.$lbApproval.'</p>';

        $objButton = new button('save', $btnSubmit);
        $objButton->setToSubmit();

        $objForm = new form('submit', $this->uri(array('action' => 'submit', 'mode' => 'copyright')));
        $objForm->addToForm($objButton->show());
        $str .= '<p>'.$objForm->show().'</p>';

        return $str;
    }

    /**
    * Method to display the copyright for acceptance.
    *
    * @access private
    * @param string $name The students name.
    * @param string $degree The degree for which the thesis / resource has been submitted.
    * @param string $department The department in which the degree was submitted.
    * @return string html
    */
    private function showCopyright($name, $degree, $department)
    {
        $btnAccept = $this->objLanguage->languageText('mod_etd_acceptconditions', 'etd');

        // Get the copyright message and replace the student name, department and degree
        $copy = $this->dbCopyright->getCopyright('');
        $copyright = $copy['copyright'];
        $copyright = str_replace('[-studentname-]', $name, $copyright);
        $copyright = str_replace('[-departmentname-]', $degree, $copyright);
        $copyright = str_replace('[-degreename-]', $department, $copyright);

        if(!$copy){
            $email = $this->objConfig->getsiteEmail();
            $objLink = new link($email);
            $objLink->linkType = 'mailto';
            $objLink->link = $email;
            $msg = $this->objLanguage->code2Txt('mod_etd_systemnotconfigured', 'etd', array('sysemail' => $objLink->show()));
            $str = "<p class='noRecordsMessage'>".$msg.'</p>';
            return $str;
        }

        $str = $copyright;

        $objButton = new button('save', $btnAccept);
        $objButton->setToSubmit();

        $objForm = new form('accept', $this->uri(array('action' => 'savesubmit', 'mode' => 'accept', 'nextmode' => '')));
        $objForm->addToForm($objButton->show());
        $str .= $objForm->show();

        return $str;
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
                return $this->editResource('');

            case 'editresource':
                $submitId = $this->getSession('submitId');
                $xml = $this->xmlMetadata->openXML('etd_'.$submitId);
                $dublin = $xml['metadata']['dublincore'];
                $data = array_merge($dublin, $xml['metadata']['thesis']);
                return $this->editResource($data);
                break;

            case 'deleteresource':
                $submitId = $this->getSession('submitId');
                return $this->deleteResource($submitId);
                break;

            case 'saveresource':
                $submitId = $this->getSession('submitId');
                $submitId = $this->saveResource($submitId);
                $this->setSession('submitId', $submitId);
                return $submitId;
                break;

            case 'showresource':
                $submitId = $this->getParam('submitId');
                if(!isset($submitId) || empty($submitId)){
                    $submitId = $this->getSession('submitId');
                }else{
                    $this->setSession('submitId', $submitId);
                }
                
                $xml = $this->xmlMetadata->openXML('etd_'.$submitId);
                $dublin = $xml['metadata']['dublincore'];
                $data = array_merge($dublin, $xml['metadata']['thesis']);
                return $this->showResource($data);

            case 'uploaddoc':
                $submitId = $this->getSession('submitId');
                $result = $this->files->uploadFile($submitId);
                
                // restore php.ini settings
                //ini_restore('post_max_size');
                //ini_restore('upload_max_filesize');
                return $result;
                break;

            case 'embargo':
                $submitId = $this->getSession('submitId');
                $request = $this->getParam('request');
                $delete = $this->getParam('delete');
                if(isset($request) && !empty($request)){
                    return $this->dbEmbargo->saveEmbargoRequest($submitId);
                }else if(isset($delete) && !empty($delete)){
                    $data = $this->dbEmbargo->getEmbargoRequest($submitId);
                    $id = $data['id'];
                    return $this->dbEmbargo->removeEmbargo($id);
                }
                break;

            case 'copyright':
                $submitId = $this->getSession('submitId');
                $xml = $this->xmlMetadata->openXML('etd_'.$submitId);
                $name = $xml['metadata']['dublincore']['dc_creator'];
                $degree = $xml['metadata']['thesis']['thesis_degree_name'];
                $dept = $xml['metadata']['thesis']['thesis_degree_discipline'];
                return $this->showCopyright($name, $degree, $dept);
                break;
                
            case 'accept':
                $submitId = $this->getSession('submitId');
                // Get the next level of submission - examination / metadata editing / management / etc
                $nextStep = $this->dbProcess->getNextStep(0);
                $status = 'pending';
                if($nextStep > 3){
                    $status = 'metadata';
                }
                // Update the level of submission
                $this->dbSubmissions->changeApproval($submitId, $this->userId, $nextStep, $status, 'private');
                // Send alert email
                //$this->
                break;

            default:
                $this->unsetSession('submitId');
                $data = array(); // get the latest submissions requiring approval
                return $this->showSubmissions($data);
        }
    }
}
?>
