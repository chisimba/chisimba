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
* Survey template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objButton=&$this->loadClass('button','htmlelements');
    $objInput=&$this->loadClass('textinput','htmlelements');
    $objDrop=&$this->loadClass('dropdown','htmlelements');
    $objRadio=&$this->loadClass('radio','htmlelements');
    $objCheck=&$this->loadClass('checkbox','htmlelements');
    $objIcon=&$this->newObject('geticon','htmlelements');
    $objTabbedbox=&$this->loadClass('tabbedbox','htmlelements');

// set up language items
    $responseLabel=$this->objLanguage->languageText('mod_survey_responses','survey');
    $questionLabel=$this->objLanguage->languageText('mod_survey_question','survey');
    $surveyLabel=$this->objLanguage->languageText('mod_survey_survey','survey');
    $compulsoryLabel=$this->objLanguage->languageText('mod_survey_compulsory','survey');
    $yesLabel=$this->objLanguage->languageText('word_yes');
    $noLabel=$this->objLanguage->languageText('word_no');
    $trueLabel=$this->objLanguage->languageText('mod_survey_true','survey');
    $falseLabel=$this->objLanguage->languageText('mod_survey_false','survey');
    $dateLabel=$this->objLanguage->languageText('mod_survey_selectdate','survey');
    $respondentLabel=$this->objLanguage->languageText('mod_survey_wordrespondent','survey');
    $firstLabel=$this->objLanguage->languageText('mod_survey_first','survey');
    $lastLabel=$this->objLanguage->languageText('mod_survey_last','survey');
    $previousLabel=$this->objLanguage->languageText('mod_survey_previous','survey');
    $nextLabel=$this->objLanguage->languageText('word_next');
    $goLabel=$this->objLanguage->languageText('word_go');
    $unassignedLabel=$this->objLanguage->languageText('mod_survey_unassignedquestions','survey');
    $pageLabel=$this->objLanguage->languageText('mod_survey_page','survey');

// set up code to text elements
    $array=array('item'=>strtolower($surveyLabel));
    $returnLabel=$this->objLanguage->code2Txt('mod_survey_return','survey',$array);
    $array=array('item'=>strtolower($responseLabel));
    $viewLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);

// set up data
    $arrPageList=$this->dbPages->listPages($surveyId);
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $surveyName=$arrSurveyData['0']['survey_name'];
    $totalResponses=$arrSurveyData['0']['response_counter'];
    $recordedResponses=$arrSurveyData['0']['recorded_responses'];
    $introLabel=stripslashes($arrSurveyData['0']['intro_label']);
    $introText=stripslashes($arrSurveyData['0']['intro_text']);
    $thanksLabel=stripslashes($arrSurveyData['0']['thanks_label']);
    $thanksText=stripslashes($arrSurveyData['0']['thanks_text']);
    $arrResponseList=$this->dbResponse->listResponses($surveyId);
    $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
    $responseId=$arrResponseList[$respondentNumber-1]['id'];

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$viewLabel;
    $objHeader->type=1;
    echo $objHeader->show();

    $objHeader = new htmlheading();
    $objHeader->str=$surveyName;
    $objHeader->type=3;
    echo $objHeader->show();

// set up icons
    $objIcon->title=$firstLabel.'&nbsp;'.strtolower($respondentLabel);
    $firstIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId,'respondent_number'=>1)),'first');

    $objIcon->title=$firstLabel.'&nbsp;'.strtolower($respondentLabel);
    $objIcon->setIcon('first_grey');
    $firstGreyIcon=$objIcon->show();

    $objIcon->title=$previousLabel.'&nbsp;'.strtolower($respondentLabel);
    $previousIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId,'respondent_number'=>$respondentNumber-1)),'prev');

    $objIcon->title=$previousLabel.'&nbsp;'.strtolower($respondentLabel);
    $objIcon->setIcon('prev_grey');
    $previousGreyIcon=$objIcon->show();

    $objIcon->title=$nextLabel.'&nbsp;'.strtolower($respondentLabel);
    $nextIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId,'respondent_number'=>$respondentNumber+1)),'next');

    $objIcon->title=$nextLabel.'&nbsp;'.strtolower($respondentLabel);
    $objIcon->setIcon('next_grey');
    $nextGreyIcon=$objIcon->show();

    $objIcon->title=$lastLabel.'&nbsp;'.strtolower($respondentLabel);
    $lastIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId,'respondent_number'=>count($arrResponseList))),'last');

    $objIcon->title=$lastLabel.'&nbsp;'.strtolower($respondentLabel);
    $objIcon->setIcon('last_grey');
    $lastGreyIcon=$objIcon->show();

// set up textinput and submit button
    $objInput=new textinput('respondent_number',$respondentNumber,'','8');
    $objInput->extra='maxlength="7"';
    $responseText=$objInput->show();

    $objButton=new button('goButton',$goLabel);
    $objButton->extra=' onclick="javascript:
        this.disabled=\'disabled\';
        document.getElementById(\'form_submitForm\').submit();
    "';
    $goButton=$objButton->show();

// set up form
    $objForm=new form('submitForm',$this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId)));
    if($respondentNumber=='1' && $respondentNumber==count($arrResponseList)){
        $objForm->addToForm('');
    }elseif($respondentNumber=='1'){
        $objForm->addToForm($firstGreyIcon.'&nbsp;'.$previousGreyIcon.'&nbsp;'.$responseText.'&nbsp;'.$goButton.'&nbsp;'.$nextIcon.'&nbsp;'.$lastIcon);
    }elseif($respondentNumber==count($arrResponseList)){
        $objForm->addToForm($firstIcon.'&nbsp;'.$previousIcon.'&nbsp;'.$responseText.'&nbsp;'.$goButton.'&nbsp;'.$nextGreyIcon.'&nbsp;'.$lastGreyIcon.'<br />');
    }else{
        $objForm->addToForm($firstIcon.'&nbsp;'.$previousIcon.'&nbsp;'.$responseText.'&nbsp;'.$goButton.'&nbsp;'.$nextIcon.'&nbsp;'.$lastIcon);
    }
    $str=$objForm->show();
    echo $str;

    // set up heading
    $objHeader = new htmlheading();
    $array=array('number'=>$respondentNumber,'total'=>$totalResponses);
    $respondentHeading=$this->objLanguage->code2Txt('mod_survey_respondent','survey',$array);
    if($recordedResponses!='1'){
        $objHeader->str=$respondentHeading;
    }else{
         $objHeader->str=$this->objUser->fullName($arrResponseList[$respondentNumber-1]['user_id']).'<br />'.$respondentHeading;
    }
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
        $firstIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId,'respondent_number'=>$respondentNumber)).'#'.'1','upend');

        // set up move up page grey icon
        $objIcon->title=$previousLabel.'&nbsp;'.strtolower($pageLabel);
        $objIcon->setIcon('upgrey');
        $previousGreyIcon=$objIcon->show();

        // set up move up page icon
        $objIcon->title=$previousLabel.'&nbsp;'.strtolower($pageLabel);
        $previousIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId,'respondent_number'=>$respondentNumber)).'#'.($counter-1),'up');

        // set up move down page grey icon
        $objIcon->title=$nextLabel.'&nbsp;'.strtolower($pageLabel);
        $objIcon->setIcon('downgrey');
        $nextGreyIcon=$objIcon->show();

        // set up move down page icon
        $objIcon->title=$nextLabel.'&nbsp;'.strtolower($pageLabel);
        $nextIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId,'respondent_number'=>$respondentNumber)).'#'.($counter+1),'down');

        // set up move last page grey icon
        $objIcon->title=$lastLabel.'&nbsp;'.strtolower($pageLabel);
        $objIcon->setIcon('downendgrey');
        $lastGreyIcon=$objIcon->show();

        // set up move last page icon
        $objIcon->title=$lastLabel.'&nbsp;'.strtolower($pageLabel);
        $lastIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId,'respondent_number'=>$respondentNumber)).'#'.count($arrPageQuestionList),'downend');

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
            $htmlElementType=$question['radio_element'];
            $booleanType=$question['preset_options'];
            $trueOrFalse=$question['true_or_false'];
            $ratingScale=$question['rating_scale'];
            $constantSum=$question['constant_sum'];
            $minimumNumber=$question['minimum_number'];
            $maximumNumber=$question['maximum_number'];
            $key=($question['question_order']-1);

            $arrRowList=$this->dbRows->listQuestionRows($questionId);
            $arrColumnList=$this->dbColumns->listQuestionColumns($questionId);

            $arrAnswerData=$this->dbItems->getResponses($responseId,$questionId);

            $typeId=$question['type_id'];
            $temp=explode('_',$typeId);
            if(isset($temp['1'])){
                $type=$temp['1'];
            }else{
                $type='';
            }

            $answerField='questionNo['.($key).']';

            $objInput=new textinput($answerField.'[question_id]',$questionId);
            $objInput->fldType='hidden';
            $tabContent.=$objInput->show();

            $objTable=new htmltable();
            $objTable->cellspacing='2';
            $objTable->cellpadding='2';

            if($type=='1' || $type=='2'){
                $colspan='colspan="2"';
            }elseif($type>='3' && $type<='5'){
                $colspan='colspan="'.(count($arrColumnList)+1).'"';
            }elseif($type=='6'){
                $colspan='colspan="'.($ratingScale+1).'"';
            }elseif($type<='8'){
                $colspan='colspan="2"';
            }else{
                $colspan='';
            }

            // set up heading
            $objTable->startHeaderRow();
            $objTable->addHeaderCell($questionLabel.' '.$i,'','','left','',$colspan);
            $objTable->endHeaderRow();

            $objTable->startRow();
            $objTable->addCell($questionText,'','','','heading',$colspan);
            $objTable->endRow();

            if($compulsoryQuestion=='1'){
                $objTable->startRow();
                $objTable->addCell('<font class="confirm"><b>'.$compulsoryLabel.'</b></font>','','','',$class,$colspan);
                $objTable->endRow();
            }

            switch($type){
                case '1':// Choice-Multiple answers-Checkboxes
                    if(empty($arrAnswerData)){
                        foreach($arrRowList as $rowKey=>$row){
                            $objTable->startRow();
                            $objTable->addCell(stripslashes($row['row_text']),'90%','','',$class,'');
                            $objTable->addCell('<b>'.$noLabel.'</b>','10%','','center',$class,'');
                            $objTable->endRow();
                        }
                    }else{
                        foreach($arrRowList as $rowKey=>$row){
                            $temp='check_'.($rowKey+1);
                            $objTable->startRow();
                            $objTable->addCell(stripslashes($row['row_text']),'90%','','',$class,'');
                            $answered=FALSE;
                            foreach($arrAnswerData as $data){
                                if($data['item_name']==$temp){
                                    $answered=TRUE;
                                }
                            }
                            if($answered){
                                $objTable->addCell('<b>'.$yesLabel.'</b>','10%','','center',$class,'');
                            }else{
                                $objTable->addCell('<b>'.$noLabel.'</b>','10%','','center',$class,'');
                            }
                            $objTable->endRow();
                        }
                    }
                    break;

                case '2':// Choice-One answer-Options or dropdown
                    if($booleanType!='1'){
                        if(empty($arrAnswerData)){
                            foreach($arrRowList as $rowKey=>$row){
                                $objTable->startRow();
                                $objTable->addCell($row['row_ext'],'90%','','',$class,'');
                                $objTable->addCell('<b>'.$noLabel.'</b>','10%','','center',$class,'');
                                $objTable->endRow();
                            }
                        }else{
                            foreach($arrRowList as $rowKey=>$row){
                                $objTable->startRow();
                                $objTable->addCell(stripslashes($row['row_text']),'90%','','',$class,'');
                                $answered=FALSE;
                                foreach($arrAnswerData as $data){
                                    if($data['item_value']==$rowKey+1){
                                        $answered=TRUE;
                                    }
                                }
                                if($answered){
                                    $objTable->addCell('<b>'.$yesLabel.'</b>','10%','','center',$class,'');
                                }else{
                                    $objTable->addCell('<b>'.$noLabel.'</b>','10%','','center',$class,'');
                                }
                                $objTable->endRow();
                            }
                        }
                    }else{
                        if(empty($arrAnswerData)){
                            for($ii=1;$ii<=2;$ii++){
                                if($ii=='1'){
                                    if($trueOrFalse=='1'){
                                        $text=$yesLabel;
                                    }else{
                                        $text=$trueLabel;
                                    }
                                }else{
                                    if($trueOrFalse=='1'){
                                        $text=$noLabel;
                                    }else{
                                        $text=$falseLabel;
                                    }
                                }
                                $objTable->startRow();
                                $objTable->addCell($text,'90%','','',$class,'');
                                $objTable->addCell('<b>'.$noLabel.'</b>','10%','','center',$class,'');
                                $objTable->endRow();
                            }
                        }else{
                            for($ii=1;$ii<=2;$ii++){
                                if($ii=='1'){
                                    if($trueOrFalse=='1'){
                                        $text=$yesLabel;
                                    }else{
                                        $text=$trueLabel;
                                    }
                                }else{
                                    if($trueOrFalse=='1'){
                                        $text=$noLabel;
                                    }else{
                                        $text=$falseLabel;
                                    }
                                }
                                $objTable->startRow();
                                $objTable->addCell($text,'90%','','',$class,'');
                                $answered=FALSE;
                                foreach($arrAnswerData as $data){
                                    if($data['item_value']==$ii){
                                        $answered=TRUE;
                                    }
                                }
                                if($answered){
                                    $objTable->addCell('<b>'.$yesLabel.'</b>','10%','','center',$class,'');
                                }else{
                                    $objTable->addCell('<b>'.$noLabel.'</b>','10%','','center',$class,'');
                                }
                                $objTable->endRow();
                            }
                        }
                    }
                    break;

                case '3':// Matrix-Multiple answers per row-Checkboxes
                    $objTable->startRow();
                    $objTable->addCell('','','','',$class,'');
                    foreach($arrColumnList as $columnKey=>$column){
                        $objTable->addCell(stripslashes($column['column_text']),'','','center',$class,'');
                    }
                    $objTable->endRow();
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');
                        foreach($arrColumnList as $columnKey=>$column){
                            if(empty($arrAnswerData)){
                                $objTable->addCell('<b>'.$noLabel.'</b>','','','center',$class,'');
                            }else{
                                $answered=FALSE;
                                foreach($arrAnswerData as $data){
                                    $temp='check_'.($rowKey+1).'_'.($columnKey+1);
                                    if($data['item_name']==$temp){
                                        $answered=TRUE;
                                    }
                                }
                                if($answered){
                                    $objTable->addCell('<b>'.$yesLabel.'</b>','','','center',$class,'');
                                }else{
                                    $objTable->addCell('<b>'.$noLabel.'</b>','','','center',$class,'');
                                }
                            }
                        }
                        $objTable->endRow();
                    }
                    break;

                case '4':// Matrix-Multiple answers per row textboxes
                    $objTable->startRow();
                    $objTable->addCell('','','','',$class,'');
                    foreach($arrColumnList as $columnKey=>$column){
                        $objTable->addCell(stripslashes($column['column_text']),'','','center',$class,'');
                    }
                    $objTable->endRow();
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');
                        foreach($arrColumnList as $columnKey=>$column){
                            foreach($arrAnswerData as $data){
                                $temp='text_'.($rowKey+1).'_'.($columnKey+1);
                                if($data['item_name']==$temp){
                                    $itemValue=empty($data['item_value'])?'-':$data['item_value'];
                                    $objTable->addCell('<b>'.$itemValue.'</b>','','','center',$class,'');
                                }
                            }
                        }
                        $objTable->endRow();
                    }
                    break;

                case '5':// Matrix-Multiple answers per row-Options
                    $objTable->startRow();
                    $objTable->addCell('','','','',$class,'');
                    foreach($arrColumnList as $columnKey=>$column){
                        $objTable->addCell(stripslashes($column['column_text']),'','','center',$class,'');
                    }
                    $objTable->endRow();
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');
                        foreach($arrColumnList as $columnKey=>$column){
                            $answered=FALSE;
                            foreach($arrAnswerData as $data){
                                $temp='radio_'.($rowKey+1);
                                if($data['item_name']==$temp){
                                    if($data['item_value']==($columnKey+1)){
                                        $answered=TRUE;
                                    }
                                }
                            }
                            if($answered){
                                $objTable->addCell('<b>'.$yesLabel.'</b>','','','center',$class,'');
                            }else{
                                $objTable->addCell('<b>'.$noLabel.'</b>','','','center',$class,'');
                            }
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
                            $answered=FALSE;
                            foreach($arrAnswerData as $data){
                                $temp='radio_'.($rowKey+1);
                                if($data['item_name']==$temp){
                                    if($data['item_value']==$ii){
                                        $answered=TRUE;
                                    }
                                }
                            }
                            if($answered){
                                $objTable->addCell('<b>'.$yesLabel.'</b>','','','center',$class,'');
                            }else{
                                $objTable->addCell('<b>'.$noLabel.'</b>','','','center',$class,'');
                            }
                        }
                        $objTable->endRow();
                    }
                    break;

                case '7':// Open ended-Textarea(Comments box)
                    $itemValue=empty($arrAnswerData['0']['item_value'])?'-':$arrAnswerData['0']['item_value'];
                    $objTable->startRow();
                    $objTable->addCell('<b>'.$itemValue.'</b>','','','',$class,'');
                    $objTable->endRow();
                    break;

                case '8':// Open ended-Constant sum
                    foreach($arrRowList as $rowKey=>$row){
                        $objTable->startRow();
                        $objTable->addCell(stripslashes($row['row_text']),'90%','','',$class,'');
                        foreach($arrAnswerData as $data){
                            $temp='text_'.($rowKey+1);
                            if($data['item_name']==$temp){
                                $itemValue=empty($data['item_value'])?'-':$data['item_value'];
                                $objTable->addCell('<b>'.$itemValue.'</b>','10%','','center',$class,'');
                            }
                        }
                        $objTable->endRow();
                    }
                    break;

                case '9':// Open ended-Number
                    $itemValue=empty($arrAnswerData['0']['item_value'])?'-':$arrAnswerData['0']['item_value'];
                    $objTable->startRow();
                    $objTable->addCell('<b>'.$itemValue.'</b>','','','',$class,'');
                    $objTable->endRow();
                    break;

                case '10':// Open ended-Date
                    $itemValue=empty($arrAnswerData['0']['item_value'])?'-':$this->formatDate($arrAnswerData['0']['item_value']);
                    $objTable->startRow();
                    $objTable->addCell('<b>'.$itemValue.'</b>','','','',$class,'');
                    $objTable->endRow();
                    break;
            }

            if($questionSubtext!=''){
                $objTable->startRow();
                $objTable->addCell($questionSubtext,'','','',$class,$colspan);
                $objTable->endRow();
            }

            if($commentRequested=='1'){
                $arrComment=$this->dbComment->getComment($responseId,$questionId);
                $comment=$arrComment['0']['question_comment'];
                if(!empty($comment)){
                    $objTable->startRow();
                    $objTable->addCell('<b>'.$commentText.'</b>','','','',$class,$colspan);
                    $objTable->endRow();
                    $objTable->startRow();
                    $objTable->addCell($comment,'','','',$class,$colspan);
                    $objTable->endRow();
                }
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
