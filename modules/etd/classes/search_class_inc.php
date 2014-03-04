<?php
/**
* Class to create the advanced search form and to retrieve the search results from the database
*
* @package etd
* @filesource
*/

/**
* Class to create the advanced search form and to retrieve the search results from the database
*
* @author Megan Watson
* @copyright (c) 2006 University of the Western Cape
* @version 0.2
*/

class search extends object
{
    /**
    * @var string Property to store the first column field used by the browse object
    */
    private $col1Field = '';

    /**
    * @var string Property to store the second column field used by the browse object
    */
    private $col2Field = '';

    /**
    * @var string Property to store the third column field used by the browse object
    */
    private $col3Field = '';

    /**
    * @var string Property to store the first column table heading used by the browse object
    */
    private $col1Header = '';

    /**
    * @var string Property to store the second column table heading used by the browse object
    */
    private $col2Header = '';

    /**
    * @var string Property to store the third column table heading used by the browse object
    */
    private $col3Header = '';

    /**
    * @var string Property to store the search title used by the browse object
    */
    public $type = '';

    /**
    * @var string Property to store the object type used by the browse object
    */
    public $_browseType = '';

    /**
    * @var int Property to give the full number of records in a result set
    */
    public $recordsFound = 0;

    /**
    * @var string Property to set the submission type - distinguish between etd's and other documents.
    */
    private $subType = 'etd';

    /**
    * @var $optionDisplay The options for how many records to display
    */
    public $optionDisplay =  array('10', '20', '30', '40', '50', '60', '70', '80', '90', '100');

    /**
    * @var $searchCriteria The dropdown list of criteria by which a user can search
    */
    private $searchCriteria =  array();

    /**
    * @var $crossRef The operators for cross referencing the search criteria
    */
    public $crossRef =  array();
    
    /**
    * The list of degrees for populating the dropdown select
    * @var array $degreeList 
    * @access private
    */
    private $degreeList = '';
    
    /**
    * The list of faculties for populating the dropdown select
    * @var array $facultyList 
    * @access private
    */
    private $facultyList = '';
    
    /**
    * The list of departments for populating the dropdown select
    * @var array $departmentList 
    * @access private
    */
    private $departmentList = '';
    
    /**
    * Constructor method
    */
    public function init()
    {
        $this->dbThesis = $this->getObject('dbthesis', 'etd');
//        $this->dbQualified = $this->getObject('dbqualified', 'etd');
        
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('dbsysconfig', 'sysconfig');

        $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
        $this->objRound = $this->newObject('roundcorners', 'htmlelements');
        $this->objTable = $this->newObject('htmltable', 'htmlelements');
        $this->objHead = $this->newObject('htmlheading', 'htmlelements');
        $this->objLayer = $this->newObject('layer', 'htmlelements');
        $this->loadClass('tabbedbox', 'htmlelements');

        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('button', 'htmlelements');

        $this->setBrowseType();

        $this->searchCriteria[] =  array('label'=>$this->objLanguage->languageText('mod_etd_authorsurnamename', 'etd'), 'value'=>'dc_creator');
        $this->searchCriteria[] =  array('label'=>$this->objLanguage->languageText('word_title'), 'value'=>'dc_title');
        $this->searchCriteria[] =  array('label'=>$this->objLanguage->languageText('word_keywords'), 'value'=>'dc_subject');
        $this->searchCriteria[] =  array('label'=>$this->objLanguage->languageText('word_abstract'), 'value'=>'dc_description');
        $this->searchCriteria[] =  array('label'=>$this->objLanguage->languageText('word_faculty'), 'value'=>'thesis_degree_faculty');
        $this->searchCriteria[] =  array('label'=>$this->objLanguage->languageText('word_department'), 'value'=>'thesis_degree_discipline');
        $this->searchCriteria[] =  array('label'=>$this->objLanguage->languageText('phrase_degreeobtained'), 'value'=>'thesis_degree_name');
        $this->searchCriteria[] =  array('label'=>$this->objLanguage->languageText('word_language'), 'value'=>'dc_language');
        
        $this->crossRef[] = array('label'=>$this->objLanguage->languageText('word_and'), 'value'=>'and');
        $this->crossRef[] = array('label'=>$this->objLanguage->languageText('word_or'), 'value'=>'or');
        $this->crossRef[] = array('label'=>$this->objLanguage->languageText('word_not'), 'value'=>'and not');
    }

    /**
    * Method to set the type of submission - etd/other.
    * Used to distiguish the source of the document in the database table.
    *
    * @access public
    * @param string $type The type of submission
    * @return
    */
    public function setSubmitType($type)
    {
        $this->subType = $type;
    }

    /**
    * Method to set the type of metadata - thesis/qualified.
    * Method sets the submission type for the metadata.
    *
    * @access public
    * @param string $meta The type of metadata
    * @param string $type The type of submission
    * @return
    */
    public function setMetaType($meta, $type)
    {
//        if($meta == 'qualified'){
//            $this->dbMetaData = $this->dbQualified;
//        }else{
            $this->dbMetaData = $this->dbThesis;
//        }
        $this->dbMetaData->setSubmitType($type);
    }

    /**
    * Method to set the search criteria. The criteria determines the fields searched.
    * The labels for the criteria are used to narrow the search to specific fields.
    *
    * @access public
    * @param array $criteria The new search criteria
    * @return
    */
    public function setSearchCriteria($criteria)
    {
        $this->searchCriteria = $criteria;
    }

    /**
    * Method to display the general search form.
    *
    * @access private
    * @param string $module The module to redirect the links to
    * @return string html
    */
    private function generalSearch($module)
    {
        $boldArr = array('bold' => '<b>', 'closebold' => '</b>');
        $hdFind = $this->objLanguage->languageText('phrase_findresults');
        $lbAll = $this->objLanguage->code2Txt('mod_etd_containingallwords', 'etd', $boldArr);
        $lbSome = $this->objLanguage->code2Txt('mod_etd_containingsomewords', 'etd', $boldArr);
        $lbPhrase = $this->objLanguage->code2Txt('mod_etd_containingthephrase', 'etd', $boldArr);

        $this->objTable->init();
        $this->objTable->cellpadding = '4';
        $this->objTable->row_attributes = " height='35'";

        $objLabel = new label($lbAll.': ', 'input_all');
        $objInput = new textinput('all', '', NULL, '65');
        $objInput->extra = "autocomplete='off'";
        $this->objTable->addRow(array($objLabel->show(), $objInput->show()));

        $objLabel = new label($lbPhrase.': ', 'input_phrase');
        $objInput = new textinput('phrase', '', NULL, '65');
        $objInput->extra = "autocomplete='off'";
        $this->objTable->addRow(array($objLabel->show(), $objInput->show()));

        $objLabel = new label($lbSome.': ', 'input_some');
        $objInput = new textinput('some', '', NULL, '65');
        $objInput->extra = "autocomplete='off'";
        $this->objTable->addRow(array($objLabel->show(), $objInput->show()));

        $dispStr = $this->getDisplayDropDown();

        $this->objTable->startRow();
        $this->objTable->addCell('');
        $this->objTable->addCell($dispStr);
        $this->objTable->endRow();

        $url = $this->uri(array('action'=>'advsearch', 'mode'=>'general'), $module);
        $objForm = new form('search', $url);
        $objForm->addToForm('<p>'.$this->objTable->show().'</p>');

//        $objTab = new tabbedbox();
//        $objTab->addTabLabel($hdFind);
//        $objTab->addBoxContent($objForm->show());
//        return $objTab->show();
        
        return $this->objFeatureBox->showContent($hdFind, $objForm->show()); //$this->objRound->show(
//        return $this->objFeatureBox->show($hdFind, $objForm->show());
    }

    /**
    * Method to display the search by page.
    *
    * @access private
    * @param string $module The module to send the form to
    * @return string html
    */
    private function specificSearch($module)
    {
        $this->showJavascript();
        $hdSearch = $this->objLanguage->languageText('phrase_searchby');
        $lbStart = $this->objLanguage->languageText('phrase_startdate');
        $lbEnd = $this->objLanguage->languageText('phrase_enddate');

        $this->objTable->init();
        $this->objTable->cellpadding = '4';
        $this->objTable->row_attributes = " height='35'";

        $objInput = new textinput('box1', '', NULL, '57');
        $objInput->setId('inputbox1');
        $objInput->extra = "autocomplete='off'";
        $inputBox = $objInput->show();
        $inputBox .= $this->getDegreeList(1).$this->getFacultyList(1).$this->getDepartmentList(1).$this->getLanguageList(1);
        
        $this->objTable->addRow(array('', $this->getCriteria(1), $inputBox));

        $objInput = new textinput('box2', '', NULL, '57');
        $objInput->setId('inputbox2');
        $objInput->extra = "autocomplete='off'";
        $inputBox2 = $objInput->show();
        $inputBox2 .= $this->getDegreeList(2).$this->getFacultyList(2).$this->getDepartmentList(2).$this->getLanguageList(2);
        $this->objTable->addRow(array($this->getCrossRef(1), $this->getCriteria(2), $inputBox2));

        $objInput = new textinput('box3', '', NULL, '57');
        $objInput->setId('inputbox3');
        $objInput->extra = "autocomplete='off'";
        $inputBox3 = $objInput->show();
        $inputBox3 .= $this->getDegreeList(3).$this->getFacultyList(3).$this->getDepartmentList(3).$this->getLanguageList(3);
        $this->objTable->addRow(array($this->getCrossRef(2), $this->getCriteria(3), $inputBox3));

        // date search
        $this->objTable->startRow();
        $this->objTable->addCell('');
        $this->objTable->addCell(ucwords($lbStart).':');
        $this->objTable->addCell($this->getYearDropdown('startdate'));
        $this->objTable->endRow();

        $this->objTable->startRow();
        $this->objTable->addCell('');
        $this->objTable->addCell(ucwords($lbEnd).':');
        $this->objTable->addCell($this->getYearDropdown('enddate', date('Y')));
        $this->objTable->endRow();

        $dispStr = $this->getDisplayDropDown();

        $this->objTable->startRow();
        $this->objTable->addCell('');
        $this->objTable->addCell('');
        $this->objTable->addCell($dispStr);
        $this->objTable->endRow();

        $url = $this->uri(array('action'=>'advsearch', 'mode'=>'specific'), $module);
        $objForm = new form('search', $url);
        $objForm->addToForm('<p>'.$this->objTable->show().'</p>');

//        $this->objTab = new tabbedbox();
//        $this->objTab->addTabLabel();
//        $this->objTab->addBoxContent();
//        return $this->objTab->show();

        return $this->objFeatureBox->showContent($hdSearch, $objForm->show());
//        return $this->objRound->show('<h3>'.$hdSearch.'</h3>'.$objForm->show());
    }

    /**
    * Method to get the dropdown for displaying the number of results limit
    *
    * @access private
    * @return string html
    */
    private function getDisplayDropDown()
    {
        $lbDisplay = $this->objLanguage->languageText('word_display');
        $lbResults = $this->objLanguage->languageText('word_results');
        $lbGo = $this->objLanguage->languageText('word_go');

        $objLabel = new label($lbDisplay.': ', 'input_displayLimit');
        $objDrop = new dropdown('displayLimit');

        foreach($this->optionDisplay as $item){
            $objDrop->addOption($item, $item);
        }

        $objButton = new button('save', $lbGo);
        $objButton->setToSubmit();

        $dispStr = $objLabel->show().'&nbsp;&nbsp;';
        $dispStr .= $objDrop->show().'&nbsp;&nbsp;';
        $dispStr .= $lbResults.'&nbsp;&nbsp;';
        $dispStr .= $objButton->show();

        return $dispStr;
    }
    
    /**
    * Method to display the javascript for displaying the select box or input box according to the search criteria selected.
    *
    * @access private
    * @return void
    */
    private function showJavascript()
    {
        
        $headerParams = $this->getJavascriptFile('etd.js', 'etd');
        $this->appendArrayVar('headerParams', $headerParams);
        /*
        $javascript = "<script type=\"text/javascript\">
        
                function changeInput(drop){
                    var el1 = 'inputbox'+drop;
                    var el2 = 'degreebox'+drop;
                    var el3 = 'departmentbox'+drop;
                    var el4 = 'facultybox'+drop;
                    var el5 = 'languagebox'+drop;
                    var el = document.getElementById('criteria'+drop).value;
                    
                    document.getElementById('inputbox'+drop).name = 'input'+drop;
                    document.getElementById('degreebox'+drop).name = 'degree'+drop;
                    document.getElementById('departmentbox'+drop).name = 'depart'+drop;
                    document.getElementById('facultybox'+drop).name = 'fac'+drop;
                    document.getElementById('languagebox'+drop).name = 'language'+drop;
                    
                    a = $(el1);
                    b = $(el2);
                    c = $(el3);
                    d = $(el4);
                    e = $(el5);
                    
                    a.hide();
                    b.hide();
                    c.hide();
                    d.hide();
                    e.hide();
                    
                    switch(el){
                        case 'thesis_degree_name':
                            document.getElementById('degreebox'+drop).name = 'box'+drop;
                            b.show();
                            break;
                        case 'thesis_degree_discipline':
                            document.getElementById('departmentbox'+drop).name = 'box'+drop;
                            c.show();
                            break;
                        case 'thesis_degree_faculty':
                            document.getElementById('facultybox'+drop).name = 'box'+drop;
                            d.show();
                            break;
                        case 'dc_language':
                            document.getElementById('languagebox'+drop).name = 'box'+drop;
                            e.show();
                            break;
                        default:
                            document.getElementById('inputbox'+drop).name = 'box'+drop;
                            a.show();
                    }
                }
        
            </script>";
            
            $this->appendArrayVar('headerParams', $javascript);
            */
    }
    
    /**
    * Method to create a drop down of the list of languages
    *
    * @access private
    * @return string html
    */
    private function getLanguageList($num = 1)
    {
        $objLangCode = $this->getObject('languagecode', 'language');
        
        $language = 'English';

        $objDrop = new dropdown('lang'.$num);
        $objDrop->setId('languagebox'.$num);
        $objDrop->extra = "style='display: none;'";
        foreach($objLangCode->iso_639_2_tags->codes as $key => $item){
            $objDrop->addOption($item, $item);
        }
        $objDrop->setSelected($language);
        
        return $objDrop->show();
    }
    
    /**
    * Method to create a drop down of the list of degrees
    *
    * @access private
    * @return string html
    */
    private function getDegreeList($num = 1)
    {            
        // get degree list
        $objDegrees = $this->getObject('dbdegrees', 'etd');
        
        // Use global variable containing degree list - else get from DB
        if(empty($this->degreeList)){
            $list = $objDegrees->getList('degree');
            $this->degreeList = $list;
        }else{
            $list = $this->degreeList;
        }
        
        $objDrop = new dropdown('degbox'.$num);
        $objDrop->setId('degreebox'.$num);
        $objDrop->extra = "style='display: none;'";
        if(!empty($list)){
            foreach($list as $item){
                $objDrop->addOption(htmlentities($item['name']), htmlentities($item['name']));
            }
        }
        
        return $objDrop->show();
    }
    
    /**
    * Method to create a drop down of the list of faculties
    *
    * @access private
    * @return string html
    */
    private function getFacultyList($num = 1)
    {            
        // get degree list
        $objDegrees = $this->getObject('dbdegrees', 'etd');
        
        // Use global variable containing degree list - else get from DB
        if(empty($this->facultyList)){
            $list = $objDegrees->getList('faculty');
            $this->facultyList = $list;
        }else{
            $list = $this->facultyList;
        }
        
        $objDrop = new dropdown('facbox'.$num);
        $objDrop->setId('facultybox'.$num);
        $objDrop->extra = "style='display: none;'";
        if(!empty($list)){
            foreach($list as $item){
                $objDrop->addOption(htmlentities($item['name']), htmlentities($item['name']));
            }
        }
        
        return $objDrop->show();
    }
    
    /**
    * Method to create a drop down of the list of departments
    *
    * @access private
    * @return string html
    */
    private function getDepartmentList($num = 1)
    {            
        // get degree list
        $objDegrees = $this->getObject('dbdegrees', 'etd');
        
        // Use global variable containing degree list - else get from DB
        if(empty($this->departmentList)){
            $list = $objDegrees->getList('department');
            $this->departmentList = $list;
        }else{
            $list = $this->departmentList;
        }
        
        $objDrop = new dropdown('depbox'.$num);
        $objDrop->setId('departmentbox'.$num);
        $objDrop->extra = "style='display: none;'";
        if(!empty($list)){
            foreach($list as $item){
                $objDrop->addOption(htmlentities($item['name']), htmlentities($item['name']));
            }
        }
        
        return $objDrop->show();
    }

    /**
    * Method to create the drop down containing the search criteria (Author/title/etc).
    *
    * @access private
    * @param int $i The array key
    * @return string html
    */
    private function getCriteria($i = 1)
    {   
        $objDrop = new dropdown("criteria[$i]");
        $objDrop->setId('criteria'.$i);
        foreach($this->searchCriteria as $item){
            $objDrop->addOption($item['value'], $item['label']);
        }
        $objDrop->extra = "onchange=\"javascript: changeInput('{$i}');\"";
        return $objDrop->show();
        
    }

    /**
    * Method to create to cross reference drop down (AND/OR/NOT)
    *
    * @access private
    * @param int $i The array key
    * @return string html
    */
    private function getCrossRef($i = 1)
    {
        $objDrop = new dropdown("cross[$i]");
        foreach($this->crossRef as $item){
            $val = strtoupper($item['label']);
            $objDrop->addOption($item['value'], $val);
        }
        return $objDrop->show();
    }

    /**
    * Method to create a dropdown list of years using the configurable variable for the start date
    *
    * @access private
    * @param string $name The element name
    * @param string $select The selected year
    * @param string $start The start year if different from the config
    * @return string html
    */
    private function getYearDropdown($name, $select = '', $start = NULL)
    {
        if(is_null($start)){
            $start = $this->objConfig->getValue('ARCHIVE_START_YEAR', 'etd');
        }

        $objDrop = new dropdown($name);

        for($i=$start; $i <= date('Y'); $i++){
            $objDrop->addOption($i, $i);
        }
        $objDrop->setSelected($select);
        return $objDrop->show();
    }

    /**
    * Method to display the search page featuring the general and specific searches
    *
    * @access public
    * @param string $module The module calling the search - for linking purposes
    * @param string $heading The heading for the search page
    * @return string html
    */
    public function showSearch($module = 'etd', $heading = NULL)
    {
        if(is_null($heading)){
            $heading = $this->objLanguage->languageText('word_search');
        }

        $this->objHead->str = $heading;
        $this->objHead->type = 1;

        $str = $this->objHead->show();
        $str .= $this->generalSearch($module);
        $str .= $this->specificSearch($module);

        $this->unsetSession('sql');
        $this->unsetSession('criteria');
        return $str;
    }

    /**
    * Method to set up global variables for browsing a search.
    * 
    * @access public
    * @return
    */
    public function setBrowseType()
    {
        $this->col1Field = 'dc_title';
        $this->col2Field = 'dc_creator';
        $this->col3Field = 'dc_date';
        $this->col1Header = $this->objLanguage->languageText('word_title');
        $this->col2Header = $this->objLanguage->languageText('word_author');
        $this->col3Header = $this->objLanguage->languageText('word_year');
        $this->type = $this->objLanguage->languageText('word_results');
        $this->_browseType = 'title';
    }

    /**
    * Method to get the headings to use when displaying the search results.
    * 
    * @access public
    * @return array The column headers
    */
    public function getHeading()
    {
        return array('col1' => $this->col1Header, 'col2' => $this->col2Header, 'col3' => $this->col3Header);
    }

    /**
    * Method to get the search data
    *
    * @access public
    * @param string $limit The number of results to display
    * @param string $displayStart The result to start with
    * @param string $joinId If a join table exists
    * @return array The results
    */
    public function getData( $limit = 10, $displayStart = NULL, $joinId = NULL )
    {
        $this->recordsFound = 0;
        return $this->getResults($limit, $displayStart);
    }

    /**
    * Method to take the search criteria and return the results.
    */
    function getResults($limit, $start = NULL)
    {
        $mode = $this->getParam('mode');
        $result = array();
        $filter = '';
        $fullCriteria = $this->getSession('criteria', NULL);
        $sessionCriteria = '';

        $sqlFilter = $this->getSession('sql', NULL);
        if(!empty($sqlFilter)){
            $filter = $sqlFilter;
            $data = $this->dbMetaData->search($filter, $limit, $start);
            $result = $data[0];
            $this->recordsFound = $data[1];
        }

        // General search of all fields
        if($mode == 'general'){
            $all = $this->getParam('all');
            $some = $this->getParam('some');
            $phrase = $this->getParam('phrase');
            
            if(!empty($all) || !empty($some) || !empty($phrase)){
                $filter = $this->getGeneralSql($all, $some, $phrase);
            }
            
            // Create a session variable containing the search criteria in 'english' for display
            if(empty($fullCriteria)){
                if(!empty($all)){
                    $sessionCriteria .= $this->objLanguage->code2Txt('mod_etd_allthewords', 'etd', array('allwords' => $all)).'; ';
                }
                if(!empty($some)){
                    $sessionCriteria .= $this->objLanguage->code2Txt('mod_etd_someofthewords', 'etd', array('somewords' => $some)).'; ';
                }
                if(!empty($phrase)){
                    $sessionCriteria .= $this->objLanguage->code2Txt('mod_etd_thephrase', 'etd', array('phrase' => $phrase)).'; ';
                }
                $fullCriteria = $this->objLanguage->code2Txt('mod_etd_searchedforgeneral', 'etd', array('criteria' => $sessionCriteria));
            }
           
            if(!empty($filter)){
                $data = $this->dbMetaData->search($filter, $limit, $start);
                $result = $data[0];
                $this->recordsFound = $data[1];
            }
        }

        // Advanced search using given fields - title/author/etc
        if($mode == 'specific'){
            $box1 = $this->getParam('box1');
            $box2 = $this->getParam('box2');
            $box3 = $this->getParam('box3');
            $criteria = $this->getParam('criteria');
            $cross = $this->getParam('cross');
            $startDate = $this->getParam('startdate');
            $endDate = $this->getParam('enddate');
            $filter = $this->getSearchSql($criteria, $cross, $startDate, $endDate, $box1, $box2, $box3);

            // Create a session variable containing the search criteria in 'english' for display
            if(empty($fullCriteria)){
                if(!empty($box1)){
                    foreach($this->searchCriteria as $item){
                        if($item['value'] == $criteria[1]){
                            $box1Criteria = $item['label'];
                            break;
                        }
                    }
                    $sessionCriteria .= $box1Criteria.': '.$box1.'; ';
                    if(!empty($box2) || !empty($box3)){
                        $sessionCriteria .= $cross[1].' ';
                    }
                }
                if(!empty($box2)){
                    foreach($this->searchCriteria as $item){
                        if($item['value'] == $criteria[2]){
                            $box2Criteria = $item['label'];
                            break;
                        }
                    }
                    $sessionCriteria .= $box2Criteria.': '.$box2.'; ';
                    if(!empty($box3)){
                        $sessionCriteria .= $cross[2].' ';
                    }
                }
                if(!empty($box3)){
                    foreach($this->searchCriteria as $item){
                        if($item['value'] == $criteria[3]){
                            $box3Criteria = $item['label'];
                            break;
                        }
                    }
                    $sessionCriteria .= $box3Criteria.': '.$box3.' ';
                }
                $fullCriteria = $this->objLanguage->code2Txt('mod_etd_searchedforspecific', 'etd', array('criteria' => $sessionCriteria));
            }

            if(!empty($filter)){
                $data = $this->dbMetaData->search($filter, $limit, $start);
                $result = $data[0];
                $this->recordsFound = $data[1];
            }
        }

        // Simple search - no criteria
        if($mode == 'simple'){
            $input = $this->getParam('searchField');
            $author = $this->getParam('searchAuthors');
            $filter = ''; $keyfilter = '';

            if(isset($author) && !empty($author)){
                /*$arrAuthor = explode(' ', $author);
                foreach($arrAuthor as $val){
                    if(!empty($filter)){
                        $filter .= ' AND ';
                    }
                    $filter .= "LOWER(dc_creator) LIKE '%".strtolower($val)."%'";
                }
                */
                if(!empty($filter)){
                        $filter .= ' AND ';
                }
                $comma = '';
                if(strpos($author, ',') === FALSE){
                    $comma = ',';
                }
                $filter .= "LOWER(dc_creator) LIKE '".strtolower($author).$comma."%'";
                $filter = '('.$filter.')';
            }

            if(isset($input) && !empty($input)){
                foreach($this->searchCriteria as $item){
                    $criteria = $item['value'];
                    $sql = $this->getFilter2($input, $criteria, $cross = 'OR');

                    if(!empty($keyfilter)){
                        $keyfilter .= ' OR ';
                    }
                    $keyfilter .= "({$sql})";
                }
            }
            if(!empty($keyfilter)){
                if(!empty($filter)){
                    $filter .= ' AND ';
                }
                $filter .= '('.$keyfilter.')';
            }

            if(!empty($filter)){
                $data = $this->dbMetaData->search($filter, $limit, $start);
                $result = $data[0];
                $this->recordsFound = $data[1];
            }
        }
        $this->setSession('sql', $filter);        
        $this->setSession('criteria', $fullCriteria);
        return $result;
    }

    /**
    * Method to get the sql required for a general search.
    *
    * @access private
    * @param string $all List of the words that are required.
    * @param string $some List of words, some of which are required.
    * @param string $phrase A required phrase.
    * @return string The sql filter for the general search
    */
    private function getGeneralSql($all = NULL, $some = NULL, $phrase = NULL)
    {
        $filter = '';
        foreach($this->searchCriteria as $item){
            $sql = ''; $sqlAll = ''; $sqlSome = ''; $sqlPhrase = '';
            $criteria = $item['value'];
            if(isset($all) && !empty($all)){
                $sqlAll = $this->getFilter2($all, $criteria, 'AND');
                $sql = '('.$sqlAll.')';
            }

            if(isset($some) && !empty($some)){
                $sqlSome = $this->getFilter2($some, $criteria, 'OR');
                if(!empty($sql)){
                    $sql .= ' AND ';
                }
                $sql .= '('.$sqlSome.')';
            }

            if(isset($phrase) && !empty($phrase)){
                $sqlPhrase = $this->getFilter2($phrase, $criteria);
                if(!empty($sql)){
                    $sql .= ' AND ';
                }
                $sql .= '('.$sqlPhrase.')';
            }

            // Build filter
            if(!empty($filter)){
                $filter .= ' OR ';
            }
            $filter .= "({$sql})";
        }

        return $filter;
    }

    /**
    * Method to get the sql required for a specific search.
    *
    * @access private
    * @param array  $criteria The field to search for each criteria - author / title / etc.
    * @param array  $cross The cross reference to use - AND / OR / NOT.
    * @param string $box1 Criteria one.
    * @param string $box2 Criteria two.
    * @param string $box3 Criteria three.
    * @return string The sql filter for the specific search
    */
    private function getSearchSql($criteria, $cross, $start, $end, $box1 = NULL, $box2 = NULL, $box3 = NULL)
    {
        $filter = '';
        if(isset($box1) && !empty($box1)){
            $filter = $this->getFilter($box1, $criteria[1]);
        }
        if(isset($box2) && !empty($box2)){
            if(!empty($filter)){
                $filter .= $this->getFilter($box2, $criteria[2], $cross[1]);
            }else{
                $filter = $this->getFilter($box2, $criteria[2]);
            }
        }
        if(isset($box3) && !empty($box3)){
            if(!empty($filter)){
                $filter .= $this->getFilter($box3, $criteria[3], $cross[2]);
            }else{
                $filter = $this->getFilter($box3, $criteria[3]);
            }
        }
        if(!empty($filter)){
            $filter .= ' AND ';
        }
        $filter .= $this->getDateFilter($start, $end);

        return $filter;
    }

    /**
    * Method to build a sql filter for a specific search
    *
    * @access private
    * @param string $input The text input by the user.
    * @param array  $criteria The field to search for each criteria - author / title / etc.
    * @param array  $cross The cross reference to use - AND / OR / NOT.
    * @return string The sql containing the given criterion
    */
    private function getFilter($input, $criteria, $cross = NULL)
    {
        $filter = '';
        if($criteria == 'dc_creator'){
            /*
            $arrCrit = explode(' ', $input);
            foreach($arrCrit as $val){
                if(!empty($filter)){
                    $filter .= ' AND ';
                }
                $filter .= "LOWER($criteria) LIKE '%".strtolower($val)."%'";
            }
            */
            // if author list separated by ;
            if(strpos($input, '; ') === FALSE){
                $arrCrit = explode('; ', $input);
                foreach($arrCrit as $val){
                   if(!empty($filter)){
                       $filter .= ' AND ';
                   }
                   $comma = '';
                   if(strpos($input, ',') === FALSE){
                       $comma = ',';
                   }
                   $filter .= "LOWER($criteria) LIKE '".strtolower($val).$comma."%'";
               }
            }else{
                // if only one author
                $comma = '';
                if(strpos($input, ',') === FALSE){
                    $comma = ',';
                }
                $filter .= "LOWER($criteria) LIKE '".strtolower($input).$comma."%'";
            }
            
            $filter = '('.$filter.') OR ';
        }else if($criteria == 'dc_subject'){
            $strCrit = str_replace(', ', ',', $input);
            $strCrit = str_replace(' ,', ',', $strCrit);
            $arrCrit = explode(',', $strCrit);
            foreach($arrCrit as $val){
                if(!empty($filter)){
                    $filter .= ' AND ';
                }
                $filter .= "LOWER($criteria) LIKE '%".strtolower($val)."%'";
            }
            $filter = '('.$filter.') OR ';
        }

        $filter .= "LOWER($criteria) LIKE '%".strtolower($input)."%'";

        return " {$cross} ({$filter})";
    }

    /**
    * Method to build a sql filter for a general search
    *
    * @access private
    * @param string $input The text input by the user.
    * @param array  $criteria The field to search for each criteria - author / title / etc.
    * @param array  $cross The cross reference to use - AND / OR / NOT.
    * @return string The sql containing the given criterion
    */
    private function getFilter2($input, $criteria, $cross = NULL)
    {
        $filter = '';
        if($cross){
            $strInput = str_replace(', ', ',', $input);
            $strInput = str_replace(' ,', ',', $strInput);
            $arrInput = explode(',', $strInput);
            foreach($arrInput as $item){
                if(!empty($filter)){
                    $filter .= " $cross ";
                }
                $filter .= "LOWER($criteria) LIKE '%".strtolower($item)."%'";
            }
        }else{
            $filter .= "LOWER($criteria) LIKE '%".strtolower($input)."%'";
        }

        return $filter;
    }

    /**
    * Method to get the sql required for a search by date - start and end dates.
    *
    * @access private
    * @param string $start The first date to search from.
    * @param string $end The last date to search to.
    * @return string The sql date filter
    */
    private function getDateFilter($start, $end)
    {
        $filter = "dc_date >= '$start' ";
        $filter .= "AND dc_date <= '$end' ";
        return $filter;
    }

    /**
    * Method to get the simple search data
    *
    * @access public
    * @param string $limit The number of results to display
    * @param string $displayStart The result to start with
    * @param string $joinId If a join table exists
    * @return array The results
    */
    public function getBySearch($search, $limit = 10, $start = NULL, $joinId = NULL)
    {
        $this->recordsFound = 0;
        $input = $this->getParam('searchForString');
        $filter = ''; $result = array();

        if(isset($input) && !empty($input)){
            foreach($this->searchCriteria as $item){
                $criteria = $item['value'];
                $sql = $this->getFilter2($input, $criteria, $cross = 'OR');

                if(!empty($filter)){
                    $filter .= ' OR ';
                }
                $filter .= "({$sql})";
            }
        }

        if(!empty($filter)){
            $data = $this->dbMetaData->search($filter, $limit, $start);
            $result = $data[0];
            $this->recordsFound = $data[1];
        }

        $this->setSession('sql', $filter);
        return $result;
    }
}
?>