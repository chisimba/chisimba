<?php
/* -------------------- survey extends controller ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package survey
*/

/**
* Results template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objLink=&$this->loadClass('link','htmlelements');

// set up language items
    $resultsLabel=$this->objLanguage->languageText('mod_survey_results', 'survey');
    $respondentLabel=$this->objLanguage->languageText('mod_survey_respondent', 'survey');
    $commentsLabel=$this->objLanguage->languageText('mod_survey_comments', 'survey');
    $questionLabel=$this->objLanguage->languageText('mod_survey_question', 'survey');
    $openLabel=$this->objLanguage->languageText('mod_survey_openendedresults', 'survey');
    $nocommentLabel=$this->objLanguage->languageText('mod_survey_nocomments', 'survey');

// set up code to text elements
    $array=array('item'=>strtolower($resultsLabel));
    $returnLabel=$this->objLanguage->code2Txt('mod_survey_return','survey',$array);
    $array=array('item'=>strtolower($commentsLabel));
    $viewLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);

// set up data
    $arrCommentList=$this->dbComment->listComments($questionId);
    $arrQuestionData=$this->dbQuestion->getQuestion($questionId);
    $surveyId=$arrQuestionData['0']['survey_id'];
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $surveyName=$arrSurveyData['0']['survey_name'];
    $recordedResponses=$arrSurveyData['0']['recorded_responses'];

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$viewLabel;
    $objHeader->type=1;
    echo $objHeader->show();

    $objHeader = new htmlheading();
    $objHeader->str=$surveyName;
    $objHeader->type=3;
    echo $objHeader->show();

    $objTable=new htmltable();
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $objTable->startHeaderRow();
    $objTable->addHeaderCell($questionLabel.' '.$arrQuestionData['0']['question_order'],'','','left','','');
    $objTable->endHeaderRow();

// set up table
    $str='';
    $i=0;
    if(empty($arrCommentList)){
            $objTable->startRow();
            $objTable->addCell('<b><font class="error">'.$nocommentLabel.'</font></b>','','','','odd','');
            $objTable->endRow();
    }else{
        foreach($arrCommentList as $key=>$commentData){
            $class=(($i++%2)==0)?'odd':'even';

            $comment=$commentData['comments'];

            if($recordedResponses!='1'){
                $respondent=$respondentLabel.'&nbsp;#'.$commentData['respondent_number'];
            }else{
                $respondent=$this->objUser->fullName($commentData['creator_id']);
            }
            $objTable->startRow();
            $objTable->addCell('<font class="confirm">'.$respondent.'</font>','','','',$class,'');
            $objTable->endRow();

            $objTable->startRow();
            $objTable->addCell($comment,'','','',$class,'');
            $objTable->endRow();
        }
    }
    echo $objTable->show();

// set up rerurn link
    $objLink=new link($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId),'survey'));
    $objLink->link=$returnLabel;
    $returnLink=$objLink->show();
    echo '<hr />'.$returnLink;
?>
