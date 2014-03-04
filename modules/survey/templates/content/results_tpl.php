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
    $objTabbedbox=&$this->loadClass('tabbedbox','htmlelements');

// set up language items
    $interimLabel=$this->objLanguage->languageText('mod_survey_interimresults','survey');
    $finalLabel=$this->objLanguage->languageText('mod_survey_finalresults','survey');
    $surveyLabel=$this->objLanguage->languageText('mod_survey_survey','survey');
    $questionLabel=$this->objLanguage->languageText('mod_survey_question','survey');
    $yesLabel=$this->objLanguage->languageText('word_yes');
    $noLabel=$this->objLanguage->languageText('word_no');
    $trueLabel=$this->objLanguage->languageText('mod_survey_true','survey');
    $falseLabel=$this->objLanguage->languageText('mod_survey_false','survey');
    $openLabel=$this->objLanguage->languageText('mod_survey_openendedresults','survey');
    $commentsLabel=$this->objLanguage->languageText('mod_survey_comments','survey');
    $abstainedLabel=$this->objLanguage->languageText('mod_survey_abstained','survey');
    $numberLabel=$this->objLanguage->languageText('mod_survey_number','survey');
    $unassignedLabel=$this->objLanguage->languageText('mod_survey_unassignedquestions','survey');
    $firstLabel=$this->objLanguage->languageText('mod_survey_first','survey');
    $lastLabel=$this->objLanguage->languageText('mod_survey_last','survey');
    $previousLabel=$this->objLanguage->languageText('mod_survey_previous','survey');
    $nextLabel=$this->objLanguage->languageText('mod_survey_next','survey');
    $pageLabel=$this->objLanguage->languageText('mod_survey_page','survey');

// set up code to text elements
    $array=array('item'=>strtolower($surveyLabel));
    $returnLabel=$this->objLanguage->code2Txt('mod_survey_return','survey',$array);
    $array=array('item'=>strtolower($interimLabel));
    $viewInterimLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);
    $array=array('item'=>strtolower($finalLabel));
    $viewFinalLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);
    $array=array('item'=>strtolower($openLabel));
    $viewopenLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);
    $array=array('item'=>strtolower($commentsLabel));
    $commentsIconLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);

// set up data
    $arrPageList=$this->dbPages->listPages($surveyId);
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $surveyName=stripslashes($arrSurveyData['0']['survey_name']);
    $introLabel=stripslashes($arrSurveyData['0']['intro_label']);
    $introText=stripslashes($arrSurveyData['0']['intro_text']);
    $thanksLabel=stripslashes($arrSurveyData['0']['thanks_label']);
    $thanksText=stripslashes($arrSurveyData['0']['thanks_text']);
    $endDate=$arrSurveyData['0']['end_date'];
    $responseMaximum=$arrSurveyData['0']['max_responses'];
    $responseCounter=$arrSurveyData['0']['response_counter'];
    $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
    $currentDate=date('Y-m-d');

    if(strtotime($currentDate)<strtotime($endDate) && $responseCounter<$responseMaximum){
        $viewLabel=$interimLabel;
    }else{
        $viewLabel=$finalLabel;
    }

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$viewLabel;
    $objHeader->type=1;
    echo $objHeader->show();

    $objHeader = new htmlheading();
    $objHeader->str=$surveyName;
    $objHeader->type=3;
    echo $objHeader->show();

    if(!empty($arrPageList)){
        foreach($arrPageList as $page){
            $arrPageQuestionList[$page['id']]='';
        }
        // if page questions exist remove questions from unassigned list
        foreach($arrQuestionList as $key=>$question){
            $arrPageQuestionData=$this->dbPageQuestions->getQuestionRecord($question['id']);
            if($arrPageQuestionData!=FALSE){
                $arrPageQuestionList[$arrPageQuestionData['0']['page_id']][$arrPageQuestionData['0']['question_order']]=$question;
                unset($arrQuestionList[$key]);
            }
        }
        if(!empty($arrQuestionList)){
            if(!empty($arrPageQuestionList)){
                array_unshift($arrPageQuestionList,$arrQuestionList);
            }else{
                $arrPageQuestionList[]=$arrQuestionList;
            }
        }
    }else{
        $arrPageQuestionList[]=$arrQuestionList;
    }

    foreach($arrPageQuestionList as $pageKey=>$pageQuestionList){
        if(empty($pageQuestionList)){
            unset($arrPageQuestionList[$pageKey]);
        }
    }

    $str='';
    $counter=0;
    foreach($arrPageQuestionList as $pageKey=>$pageQuestionList){
        $counter++;

        // set up move first page grey icon
        $objIcon->title=$firstLabel.'&nbsp;'.strtolower($pageLabel);
        $objIcon->setIcon('upendgrey');
        $firstGreyIcon=$objIcon->show();

        // set up move first page icon
        $objIcon->title=$firstLabel.'&nbsp;'.strtolower($pageLabel);
        $firstIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId)).'#'.'1','upend');

        // set up move up page grey icon
        $objIcon->title=$previousLabel.'&nbsp;'.strtolower($pageLabel);
        $objIcon->setIcon('upgrey');
        $previousGreyIcon=$objIcon->show();

        // set up move up page icon
        $objIcon->title=$previousLabel.'&nbsp;'.strtolower($pageLabel);
        $previousIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId)).'#'.($counter-1),'up');

        // set up move down page grey icon
        $objIcon->title=$nextLabel.'&nbsp;'.strtolower($pageLabel);
        $objIcon->setIcon('downgrey');
        $nextGreyIcon=$objIcon->show();

        // set up move down page icon
        $objIcon->title=$nextLabel.'&nbsp;'.strtolower($pageLabel);
        $nextIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId)).'#'.($counter+1),'down');

        // set up move last page grey icon
        $objIcon->title=$lastLabel.'&nbsp;'.strtolower($pageLabel);
        $objIcon->setIcon('downendgrey');
        $lastGreyIcon=$objIcon->show();

        // set up move last page icon
        $objIcon->title=$lastLabel.'&nbsp;'.strtolower($pageLabel);
        $lastIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId)).'#'.count($arrPageQuestionList),'downend');

        if($pageKey!='0'){
            $arrPageData=$this->dbPages->getPage($pageKey);
            $pageLabelText=stripslashes($arrPageData['0']['page_label']);
            $pageText=stripslashes($arrPageData['0']['page_text']);
        }else{
            if(!empty($arrPageList)){
                $pageLabelText=$unassignedLabel;
            }else{
                $pageLabelText=$surveyLabel;
            }
            $pageText='';
        }

        if(count($arrPageQuestionList)!='1'){
            $objLink=new link('#');
            $objLink->name=$counter;
            $objLink->link='&nbsp;';
            $objLink->extra=' style="text-decoration:none"';
            $pageLabelText.=$objLink->show();
            if($counter=='1'){
                $pageLabelText=$firstGreyIcon.$previousGreyIcon.$pageLabelText.$nextIcon.$lastIcon;
            }elseif($counter==count($arrPageQuestionList)){
                $pageLabelText=$firstIcon.$previousIcon.$pageLabelText.$nextGreyIcon.$lastGreyIcon;
            }else{
                $pageLabelText=$firstIcon.$previousIcon.$pageLabelText.$nextIcon.$lastIcon;
            }
        }

    // set up table
        $tabContent='';
        $i=0;
        ksort($pageQuestionList);
        foreach($pageQuestionList as $question){
            $class=(($i++%2)==0)?'odd':'even';

            $questionId=$question['id'];
            $surveyId=$question['survey_id'];
            $questionText=stripslashes($question['question_text']);
            $questionSubtext=stripslashes($question['question_subtext']);
            $compulsoryQuestion=$question['compulsory_question'];
            $verticalAlignment=$question['vertical_alignment'];
            $commentRequested=$question['comment_requested'];
            $commentText=stripslashes($question['comment_request_text']);
            $h1tmlElementType=$question['radio_element'];
            $booleanType=$question['preset_options'];
            $trueOrFalse=$question['true_or_false'];
            $ratingScale=$question['rating_scale'];
            $constantSum=$question['constant_sum'];
            $minimumNumber=$question['minimum_number'];
            $maximumNumber=$question['maximum_number'];
            $key=($question['question_order']-1);

            $arrRowList=$this->dbRows->listQuestionRows($questionId);
            $arrColumnList=$this->dbColumns->listQuestionColumns($questionId);

            $arrAnswerList=$this->dbAnswer->listRows($questionId);
            $abstained=0;
            $responded=0;
            foreach($arrAnswerList as $answer){
                if($answer['answer_given']!='1'){
                    $abstained=$abstained+1;
                }else{
                    $responded=$responded+1;
                }
            }
            $totalResponses=$abstained+$responded;
            $abstainedPerc=round(($abstained/$totalResponses)*100,2);
            $abstainedBar=$this->questions->bar($abstainedPerc,$abstainedLabel,'blue');

            $arrItemList=$this->dbItems->getResults($questionId);

            $typeId=$question['type_id'];
            $temp=explode('_',$typeId);
            if(isset($temp['1'])){
                $type=$temp['1'];
            }else{
                $type='';
            }

            $answerField='questionNo['.($key).']';

            $objText=new textinput($answerField.'[question_id]',$questionId);
            $objText->fldType='hidden';
            $tabContent.=$objText->show();

            $objTable=new htmltable();
            $objTable->cellspacing='2';
            $objTable->cellpadding='2';

            if($type=='1'){
                $colspan='colspan="3"';
            }elseif($type=='2'){
                $colspan='colspan="2"';
            }elseif($type>='3' && $type<='5'){
                $colspan='colspan="'.(count($arrColumnList)+1).'"';
            }elseif($type=='6'){
                $colspan='colspan="'.($ratingScale+1).'"';
            }else{
                $colspan='';
            }

            // set up view open ended results link
            $objLink=new link($this->uri(array('action'=>'viewopen','question_id'=>$questionId,'survey_id'=>$surveyId),'survey'));
            $objLink->link=$viewopenLabel;
            $openLink=$objLink->show();

            // set up view comments icon
            if($commentRequested=='1'){
                $objIcon->title=$commentsIconLabel;
                $commentsIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewcomments','question_id'=>$questionId,'survey_id'=>$surveyId)),'viewsurveycomments');
            }else{
                $commentsIcon='';
            }

            // set up heading
            $objTable->startHeaderRow();
            $objTable->addHeaderCell($questionLabel.'&nbsp;'.$i.'&nbsp;'.$commentsIcon,'','','left','',$colspan);
            $objTable->endHeaderRow();

            $objTable->startRow();
            $objTable->addCell($questionText,'','','','heading',$colspan);
            $objTable->endRow();

            $objTable->startRow();
            $objTable->addCell('<font class="confirm">'.$numberLabel.' - '.$totalResponses.'</font>','','','',$class,$colspan);
            $objTable->endRow();

            $objTable->startRow();
            $objTable->addCell('<b>'.$abstainedLabel.'&nbsp;-&nbsp;'.$abstainedBar.'&nbsp;-&nbsp;('.$abstainedPerc.'%)</b>','','','',$class,$colspan);
            $objTable->endRow();

            switch($type){
                case '1':// Choice-Multiple answers-Checkboxes
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');

                        $answered=0;
                        foreach($arrItemList as $item){
                            if($item['item_name']=='check_'.($rowKey+1)){
                                $answered=$answered+1;
                            }
                        }
                        $unanswered=$totalResponses-$abstained-$answered;

                        $yesPerc=$responded==0?0:round(($answered/$responded)*100,2);
                        $yesBar=$this->questions->bar($yesPerc,$yesLabel,'green');

                        $noPerc=$responded==0?0:round(($unanswered/$responded)*100,2);
                        $noBar=$this->questions->bar($noPerc,$noLabel,'red');

                        $objTable->addCell($yesLabel.'&nbsp;-&nbsp;'.$yesBar.'&nbsp;-&nbsp;('.$yesPerc.'%)','','','',$class,'');
                        $objTable->addCell($noLabel.'&nbsp;-&nbsp;'.$noBar.'&nbsp;-&nbsp;('.$noPerc.'%)','','','',$class,'');
                        $objTable->endRow();
                    }
                    break;

                case '2':// Choice-One answer-Options or dropdown
                    if($booleanType!='1'){
                        foreach($arrRowList as $rowKey=>$row){
                            $objTable->startRow();
                            $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');

                            $answered=0;
                            foreach($arrItemList as $item){
                                if($item['item_value']==($rowKey+1)){
                                    $answered=$answered+1;
                                }
                            }
                            $yesPerc=$responded==0?0:round(($answered/$responded)*100,2);
                            $yesBar=$this->questions->bar($yesPerc,$yesLabel,'green');

                            $objTable->addCell($yesBar.'&nbsp;-&nbsp;('.$yesPerc.'%)','','','',$class,'');
                            $objTable->endRow();
                        }
                    }else{
                        if($trueOrFalse!='1'){
                            $onText=$trueLabel;
                            $offText=$falseLabel;
                        }else{
                            $onText=$yesLabel;
                            $offText=$noLabel;
                        }
                        for($ii=1;$ii<=2;$ii++){
                            if($ii=='1'){
                                $text=$onText;
                                $colour='green';
                            }else{
                                $text=$offText;
                                $colour='red';
                            }
                            $objTable->startRow();
                            $objTable->addCell($text,'','','',$class,'');

                            $answered=0;
                            foreach($arrItemList as $item){
                                if($item['item_value']==$ii){
                                    $answered=$answered+1;
                                }
                            }
                            $yesPerc=$responded==0?0:round(($answered/$responded)*100,2);
                            $yesBar=$this->questions->bar($yesPerc,$text,$colour);

                            $objTable->addCell($yesBar.'&nbsp;-&nbsp;('.$yesPerc.'%)','','','',$class,'');
                            $objTable->endRow();
                        }
                    }
                    break;

                case '3':// Matrix-Multiple answers per row-Checkboxes
                    $objTable->startRow();
                    $objTable->addCell('','','','',$class,'');
                    foreach($arrColumnList as $columnKey=>$column){
                        $objTable->addCell('<b>'.stripslashes($column['column_text']).'</b>','','','center',$class,'');
                    }
                    $objTable->endRow();
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell('<b>'.stripslashes($row['row_text']).'</b>','','','',$class,'rowspan="2"');
                        foreach($arrColumnList as $columnKey=>$column){
                            $answered=0;
                            foreach($arrItemList as $item){
                                $temp='check_'.($rowKey+1).'_'.($columnKey+1);
                                if($item['item_name']==$temp){
                                    $answered=$answered+1;
                                }
                            }
                            $yesPerc=$responded==0?0:round(($answered/$responded)*100,2);
                            $yesBar=$this->questions->bar($yesPerc,$yesLabel,'green',FALSE);
                            $objTable->addCell($yesLabel.'&nbsp;-&nbsp;'.$yesBar.'&nbsp;-&nbsp;('.$yesPerc.'%)','','','',$class,'');
                        }
                        $objTable->endRow();
                        $objTable->startRow();
                        foreach($arrColumnList as $columnKey=>$column){
                            $answered=0;
                            foreach($arrItemList as $item){
                                $temp='check_'.($rowKey+1).'_'.($columnKey+1);
                                if($item['item_name']==$temp){
                                    $answered=$answered+1;
                                }
                            }
                            $unanswered=$totalResponses-$abstained-$answered;

                            $noPerc=$responded==0?0:round(($unanswered/$responded)*100,2);
                            $noBar=$this->questions->bar($noPerc,$noLabel,'red',FALSE);

                            $objTable->addCell($noLabel.'&nbsp;-&nbsp;'.$noBar.'&nbsp;-&nbsp;('.$noPerc.'%)','','','',$class,'');
                    }
                        $objTable->endRow();
                    }
                    break;

                case '5':// Matrix-Multiple answers per row-Options
                    $objTable->startRow();
                    $objTable->addCell('','','','',$class,'');
                    foreach($arrColumnList as $columnKey=>$column){
                        $objTable->addCell('<b>'.stripslashes($column['column_text']).'</b>','','','center',$class,'');
                    }
                    $objTable->endRow();
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');
                        foreach($arrColumnList as $columnKey=>$column){
                            $answered=0;
                            foreach($arrItemList as $item){
                                $temp='radio_'.($rowKey+1);
                                if($item['item_name']==$temp){
                                    if($item['item_value']==($columnKey+1)){
                                        $answered=$answered+1;
                                    }
                                }
                            }
                            $yesPerc=$responded==0?0:round(($answered/$responded)*100,2);
                            $yesBar=$this->questions->bar($yesPerc,$yesLabel,'green');

                            $objTable->addCell($yesBar.'&nbsp;-&nbsp;('.$yesPerc.'%)','','','',$class,'');
                        }
                        $objTable->endRow();
                    }
                    break;

                case '6':// Matrix-Rating scale (Numeric)
                    $objTable->startRow();
                    $objTable->addCell('','','','',$class,'');
                    for($ii=1;$ii<=$ratingScale;$ii++){
                        $objTable->addCell('<b>'.$ii.'</b>','','','center',$class,'');
                    }
                    $objTable->endRow();
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');
                        for($ii=1;$ii<=$ratingScale;$ii++){
                            $answered=0;
                            foreach($arrItemList as $item){
                                if($item['item_name']=='radio_'.($rowKey+1)){
                                    if($item['item_value']==$ii){
                                        $answered=$answered+1;
                                    }
                                }
                            }
                            $yesPerc=$responded==0?0:round(($answered/$responded)*100,2);
                            $yesBar=$this->questions->bar($yesPerc,$yesLabel,'green');

                            $objTable->addCell($yesBar.'&nbsp;-&nbsp;('.$yesPerc.'%)','','','',$class,'');
                        }
                        $objTable->endRow();
                    }
                    break;

                default:// Open ended-Textarea(Comments box)
                    $objTable->startRow();
                    $objTable->addCell($openLink,'','','',$class,'');
                    $objTable->endRow();
                    break;

            }
            $tabContent.=$objTable->show();
        }
        if(!empty($pageQuestionList)){
            $objTabbedbox=new tabbedbox();
            $objTabbedbox->extra=' style="padding: 10px;"';
            $objTabbedbox->addTabLabel($pageLabelText);
            $objTabbedbox->addBoxContent($tabContent);
            $str.=$objTabbedbox->show();
        }
    }
    echo $str;

// set up rerurn link
    $objLink=new link($this->uri(array('action'=>'listsurveys'),'survey'));
    $objLink->link=$returnLabel;
    $returnLink=$objLink->show();
    echo $returnLink;
?>