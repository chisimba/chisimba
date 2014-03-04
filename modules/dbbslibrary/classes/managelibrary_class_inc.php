<?php
/**
* Class for managing the individual resources - editing and deleting archived resources and adding new resources. 
* Extends the management class in the ETD module.
* @package dbbslibrary
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for managing the individual resources - editing and deleting archived resources and adding new resources
* Extends the management class in the ETD module.
*
* @author Megan Watson
* @copyright (c) 2007 University of the Western Cape
* @version 0.1
*/

$this->loadClass('management', 'etd');
class managelibrary extends management
{
    /**
    * @var array $access The users access level within the class - set in the controller according to the group in which the user is a member.
    * @access protected
    */
    protected $access;
    
    /**
    * Constructor for the class
    *
    * @access public
    * @return void
    */
    public function init()
    {
        try{
            parent::init();
            
            $this->dbSubmissions->setSubmitType('dbbs');
            $this->dbThesis->setSubmitType('dbbs');
            
            $this->dbCollection = $this->getObject('dbcollection', 'etd');
            $this->dbCollection->setSubmitType('dbbs');
            $this->dbCollectBridge = $this->getObject('dbcollectsubmit', 'etd');
            
            $this->access = $this->getSession('accessLevel', array());
            $this->module = 'dbbslibrary';
            
            $this->setSubmitType('dbbs', 'dbbslibrary');
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
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
        $lbSummary = $this->objLanguage->languageText('word_summary');
        $lbCountry = $this->objLanguage->languageText('word_country');
        $lbDocType = $this->objLanguage->languageText('phrase_documenttype');
        $lbSource = $this->objLanguage->languageText('word_source');
        $lbDateAdded = $this->objLanguage->languageText('phrase_dateadded');
        $lbContributor = $this->objLanguage->languageText('word_contributor');
        $lbContributorRole = $this->objLanguage->languageText('phrase_contributorrole');
        $lbPublisher = $this->objLanguage->languageText('word_publisher');
        //$lbFaculty = $this->objLanguage->languageText('word_faculty');
        //$lbDepartment = $this->objLanguage->languageText('word_department');
        $lbProject = $this->objLanguage->languageText('word_project');
        $lbInstitution = $this->objLanguage->languageText('word_institution');
        //$lbDegree = $this->objLanguage->languageText('phrase_degreeobtained');
        //$lbLevel = $this->objLanguage->languageText('phrase_degreelevel');
        //$lbGrantor = $this->objLanguage->languageText('word_grantor');
        $lbDate = $this->objLanguage->languageText('word_year');
        $lbFormat = $this->objLanguage->languageText('word_format');
        $lbRights = $this->objLanguage->languageText('word_rights');
        $lbRelationship = $this->objLanguage->languageText('word_relationship');
        $lbLanguage = $this->objLanguage->languageText('word_language');
        $lbAudience = $this->objLanguage->languageText('word_audience');
        $lbKeywords = $this->objLanguage->languageText('word_keywords');
        $lbPdf = $this->objLanguage->languageText('word_pdf');
        //$lbThesisDiss = $this->objLanguage->languageText('phrase_thesisanddissertation');
        $lbCopyright = $this->objLanguage->languageText('word_copyright');
        $btnSave = $this->objLanguage->languageText('word_save');
        $btnCancel = $this->objLanguage->languageText('word_cancel');
        $lbConfigure = $this->objLanguage->languageText('mod_etd_configuredegreeinformation', 'etd');
        $errNoTitle = $this->objLanguage->languageText('mod_etd_errnotitle', 'etd');
        $errNoAuthor = $this->objLanguage->languageText('mod_etd_errnoauthor', 'etd');
        $errNoInstitution = $this->objLanguage->languageText('mod_etd_errnoinstitution', 'etd');
        $errNoKeywords = $this->objLanguage->languageText('mod_etd_erraddkeywords', 'etd');

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

        // Project
        $objLabel = new label($lbProject.': ', 'input_project');
        
        $objDbContext = $this->getObject('dbcontext', 'context');
        $projects = $objDbContext->getListOfContext();
        
        $objDrop = new dropdown('collection');
        $objDrop->addOption('', ' --- ');
        if(!empty($projects)){
            foreach($projects as $proj){
                $objDrop->addOption($proj['title'], $proj['menutext']);
            }
            isset($data['collection']) ? $objDrop->setSelected($data['collection']) : '';
        }
        
        $objTable->addRow(array($objLabel->show(), $objDrop->show()));

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
        $type = '';
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
        //$lbCitation = $this->objLanguage->languageText('word_citation');
        $lbSummary = $this->objLanguage->languageText('word_summary');
        $lbCountry = $this->objLanguage->languageText('word_country');
        $lbDocType = $this->objLanguage->languageText('phrase_documenttype');
        $lbSource = $this->objLanguage->languageText('word_source');
        $lbDateAdded = $this->objLanguage->languageText('phrase_dateadded');
        $lbContributor = $this->objLanguage->languageText('word_contributor');
        $lbPublisher = $this->objLanguage->languageText('word_publisher');
        $lbFaculty = $this->objLanguage->languageText('word_faculty');
        //$lbDegree = $this->objLanguage->languageText('phrase_degreeobtained');
        //$lbLevel = $this->objLanguage->languageText('phrase_degreelevel');
        $lbGrantor = $this->objLanguage->languageText('word_grantor');
        $lbDate = $this->objLanguage->languageText('word_year');
        $lbFormat = $this->objLanguage->languageText('word_format');
        $lbRights = $this->objLanguage->languageText('word_rights');
        $lbRelationship = $this->objLanguage->languageText('word_relationship');
        $lbLanguage = $this->objLanguage->languageText('word_language');
        $lbAudience = $this->objLanguage->languageText('word_audience');
        $lbKeywords = $this->objLanguage->languageText('word_keywords');
        $lbProject = $this->objLanguage->languageText('word_project');
        $lbAltTitle = $this->objLanguage->languageText('phrase_alternatetitle');
        $lbContributorRole = $this->objLanguage->languageText('phrase_contributorrole');
        //$lbFaculty = $this->objLanguage->languageText('word_faculty');
        //$lbDepartment = $this->objLanguage->languageText('word_department');
        $lbInstitution = $this->objLanguage->languageText('word_institution');
        $confirmDel = $this->objLanguage->languageText('mod_etd_confirmdeleteresource', 'etd');
        $lbApprove = $this->objLanguage->languageText('mod_etd_approveaddrepository', 'etd');
        $lbComplete = $this->objLanguage->languageText('mod_etd_completemetadata', 'etd');
        $lbPass = $this->objLanguage->languageText('mod_etd_passresource', 'etd');
        //$lbCitation = $this->objLanguage->languageText('phrase_citationlist');
        $lbEdit = $this->objLanguage->languageText('phrase_editmetadata');
        //$lbEmbargo = $this->objLanguage->languageText('phrase_embargoresource');

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
            
            $collection = isset($data['collection']) ? $data['collection'] : '';
            $objTable->addRow(array($lbProject.': ', $collection));
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

        return $str.'<br />';
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
        $extra['collection'] = $this->getParam('collection');

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
        
        // Add resource to collection / project
        $collection = $xmlData['metadata']['extra']['collection'];
        $collectId = $this->dbCollection->checkCollection($collection);
        if($collectId === FALSE){
            $collectId = $this->dbCollection->saveCollection($this->userId, $collection, '');
        }
        $this->dbCollectBridge->addSubmissionToCollection($submitId, $collectId);
        
        // Check embargo & set start and end periods
        //$this->dbEmbargo->setEmbargoDates($submitId);
        
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

        // Update collection / project
        $collection = $this->getParam('collection');
        
        $collectId = $this->dbCollection->checkCollection($collection);
        if($collectId === FALSE){
            $collectId = $this->dbCollection->saveCollection($this->userId, $collection, '');
        }
        $this->dbCollectBridge->addSubmissionToCollection($submitId, $collectId);
        
        return $submitId;
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
                // get collection
                $collection = $this->dbCollectBridge->getCollectionData($submitId);
                $data['collection'] = $collection['name'];
                return $this->editResource($data);
                break;

            case 'editnewresource':
                $submitId = $this->getSession('submitId');
                $xml = $this->xmlMetadata->openXML('etd_'.$submitId);
                $dublin = $xml['metadata']['dublincore'];
                $data = array_merge($dublin, $xml['metadata']['thesis']);
                $data = array_merge($data, $xml['metadata']['extra']);
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
                // get collection
                $collection = $this->dbCollectBridge->getCollectionData($submitId);
                $data['collection'] = $collection['name'];
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
                $data = array_merge($data, $xml['metadata']['extra']);
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