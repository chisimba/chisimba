<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package Project Management System
*/

/**
* View observer comments template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objLink=&$this->loadClass('link','htmlelements');

// set up language items
    $backLabel=$this->objLanguage->languageText('word_back');
    $commentsLabel=$this->objLanguage->languageText('mod_survey_comments','survey');
    $observerLabel=$this->objLanguage->languageText('mod_survey_wordobserver','survey');

// set up code to text elements
    $array=array('item'=>$observerLabel.' '.$commentsLabel);
    $viewCommentsLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);

// set up data
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $surveyName=$arrSurveyData[0]['survey_name'];

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=ucfirst(strtolower($viewCommentsLabel));
    $objHeader->type=1;
    echo $objHeader->show();

    $objHeader = new htmlheading();
    $objHeader->str=$surveyName;
    $objHeader->type=3;
    echo $objHeader->show().'<hr />';

// set up comments
    $this->objComment->set('tableName', 'tbl_survey');
    $this->objComment->set('moduleCode', 'project');
    $this->objComment->set('sourceId', $surveyId);

    echo $this->objComment->showAll();

// set up back link
    $objLink=new link('javascript:history.back()');
    $objLink->link=$backLabel;
    $str='<hr />'.$objLink->show();

    echo $str;
?>
