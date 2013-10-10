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
    $objIcon=&$this->newObject('geticon','htmlelements');

// set up language items
    $questionLabel=$this->objLanguage->languageText('mod_survey_question','survey');
    $openLabel=$this->objLanguage->languageText('mod_survey_openendedresults','survey');
    $resultsLabel=$this->objLanguage->languageText('mod_survey_results','survey');
    $respondentLabel=$this->objLanguage->languageText('mod_survey_respondent','survey');
    $abstainedLabel=$this->objLanguage->languageText('mod_survey_abstained','survey');

// set up code to text elements
    $array=array('item'=>strtolower($resultsLabel));
    $returnLabel=$this->objLanguage->code2Txt('mod_survey_return','survey',$array);
    $array=array('item'=>strtolower($openLabel));
    $viewLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);

// set up data
    $arrItemsList=$this->dbItems->getOpenResults($questionId);
    $arrQuestionData=$this->dbQuestion->getQuestion($questionId);
    $typeId=$arrQuestionData['0']['type_id'];
    $arrRowList=$this->dbRows->listQuestionRows($questionId);
    $arrColumnList=$this->dbColumns->listQuestionColumns($questionId);
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

// set up colspan
    if($typeId=='init_4'){
        $colspan='colspan="'.(count($arrColumnList)+1).'"';
    }elseif($typeId=='init_8'){
        $colspan='colspan="'.count($arrRowList).'"';
    }else{
        $colspan='';
    }

// set up table
    $objTable=new htmltable();
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $objTable->startHeaderRow();
    $objTable->addHeaderCell($questionLabel.' '.$arrQuestionData['0']['question_order'],'','','left','',$colspan);
    $objTable->endHeaderRow();

    switch($typeId){
        case 'init_4':
            $arrItemData=array_chunk($arrItemsList,count($arrRowList)*count($arrColumnList));
            $i=0;
            foreach($arrItemData as $key=>$arrItems){
                $class=(($i++%2)==0)?'odd':'even';

                if($recordedResponses!='1'){
                    $respondent=$respondentLabel.'&nbsp;#'.$arrItems['0']['respondent_number'];
                }else{
                    $respondent=$this->objUser->fullName($arrItems['0']['creator_id']);
                }
                $objTable->startRow();
                $objTable->addCell('<font class="confirm">'.$respondent.'</font>','','','',$class,$colspan);
                $objTable->endRow();

                $ii=0;
                if(stripslashes($arrItems[$ii]['item_value'])==''){
                    $objTable->startRow();
                    $objTable->addCell('<b><font class="error">'.$abstainedLabel.'</font></b>','','','',$class,$colspan);
                    $objTable->endRow();
                }else{
                    $objTable->startRow();
                    $objTable->addCell('','','','',$class,'');
                    foreach($arrColumnList as $column){
                        $objTable->addCell(stripslashes($column['column_text']),'','','',$class,'');
                    }
                    $objTable->endRow();

                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');
                        foreach($arrColumnList as $columnKey=>$column){
                            $temp='text_'.($rowKey+1).'_'.($columnKey+1);
                            $objTable->addCell('<b>'.stripslashes($arrItems[$ii]['item_value']).'</b>','','','',$class,'');
                            $ii++;
                        }
                        $objTable->endRow();
                    }
                }
            }
            break;

        case 'init_8':
            $arrItemData=array_chunk($arrItemsList,count($arrRowList));
            $i=0;
            foreach($arrItemData as $key=>$arrItems){
                $class=(($i++%2)==0)?'odd':'even';

                if($recordedResponses!='1'){
                    $respondent=$respondentLabel.'&nbsp;#'.$arrItems['0']['respondent_number'];
                }else{
                    $respondent=$this->objUser->fullName($arrItems['0']['creator_id']);
                }
                $objTable->startRow();
                $objTable->addCell('<font class="confirm">'.$respondent.'</font>','','','',$class,$colspan);
                $objTable->endRow();

                if($arrItems['0']['item_value']==''){
                    $objTable->startRow();
                    $objTable->addCell('<b><font class="error">'.$abstainedLabel.'</font></b>','','','',$class,$colspan);
                    $objTable->endRow();
                }else{
                    $objTable->startRow();
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->addCell(stripslashes($row['row_text']).' - <b>'.$arrItems[$rowKey]['item_value'].'</b>','','','',$class,'');
                    }
                    $objTable->endRow();
                }
            }
            break;

        default:
            $i=0;
            $total=count($arrItemsList);
            foreach($arrItemsList as $key=>$item){
                $class=(($i++%2)==0)?'odd':'even';
                $itemValue=$item['item_value'];

                if($item['item_name']=='date' && !empty($itemValue)){
                    $itemValue=$this->formatDate($itemValue);
                }

                if(isset($itemValue) && !empty($itemValue)){
                    $itemValue='<b>'.$itemValue.'</b>';
                }else{
                    $itemValue='<b><font class="error">'.$abstainedLabel.'</font></b>';
                }

                if($recordedResponses!='1'){
                    $respondent=str_replace('[-number-]',$item['respondent_number'],$respondentLabel);
                    $respondent=str_replace('[-total-]',$total,$respondent);
                }else{
                    $respondent=$this->objUser->fullName($item['creator_id']);
                }
                $objTable->startRow();
                $objTable->addCell('<font class="confirm">'.$respondent.'</font>','','','',$class,'');
                $objTable->endRow();

                $objTable->startRow();
                $objTable->addCell($itemValue,'','','',$class,$colspan);
                $objTable->endRow();
            }
            break;

    }
    echo $objTable->show();

// set up rerurn link
    $objLink=new link($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId),'survey'));
    $objLink->link=$returnLabel;
    $returnLink=$objLink->show();
    echo '<hr />'.$returnLink;
?>
