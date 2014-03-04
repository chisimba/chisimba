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

    $this->setVar('pageSuppressToolbar', FALSE);

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objButton=&$this->loadClass('button','htmlelements');
    $objText=&$this->loadClass('textarea','htmlelements');
    $objInput=&$this->loadClass('textinput','htmlelements');
    $objDrop=&$this->loadClass('dropdown','htmlelements');
    $objRadio=&$this->loadClass('radio','htmlelements');
    $objCheck=&$this->loadClass('checkbox','htmlelements');
    $objIcon=&$this->newObject('geticon','htmlelements');
    $objTabbedbox=&$this->loadClass('tabbedbox','htmlelements');
    $objLayer=&$this->loadClass('layer','htmlelements');
    $objPopupcal = $this->newObject('datepickajax', 'popupcalendar');

// set up language items
    $heading=$this->objLanguage->languageText('mod_survey_preview','survey');
    $questionLabel=$this->objLanguage->languageText('mod_survey_question','survey');
    $compulsoryLabel=$this->objLanguage->languageText('mod_survey_compulsory','survey');
    $yesLabel=$this->objLanguage->languageText('word_yes');
    $noLabel=$this->objLanguage->languageText('word_no');
    $trueLabel=$this->objLanguage->languageText('mod_survey_true','survey');
    $falseLabel=$this->objLanguage->languageText('mod_survey_false','survey');
    $dateLabel=$this->objLanguage->languageText('mod_survey_selectdate','survey');
    $submitLabel=$this->objLanguage->languageText('word_submit');
    $errorLabel=$this->objLanguage->languageText('mod_survey_takeerrors','survey');
    $thankyouLabel=$this->objLanguage->languageText('mod_survey_thankyou','survey');
    $unassignedLabel=$this->objLanguage->languageText('mod_survey_unassignedquestions','survey');
    $surveyLabel=$this->objLanguage->languageText('mod_survey_survey','survey');
    $nextLabel=$this->objLanguage->languageText('word_next');
    $backLabel=$this->objLanguage->languageText('word_back');
    $optionLabel=$this->objLanguage->languageText('mod_survey_selectoption','survey');

// set up code to text elements
    $array=array('item'=>strtolower($questionLabel));
    $returnLabel=$this->objLanguage->code2Txt('mod_survey_return','survey',$array);

// set up data
    $arrPageList=$this->dbPages->listPages($surveyId);
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $surveyName=stripslashes($arrSurveyData['0']['survey_name']);
    $introLabel=stripslashes($arrSurveyData['0']['intro_label']);
    $introText=stripslashes($arrSurveyData['0']['intro_text']);
    $thanksLabel=stripslashes($arrSurveyData['0']['thanks_label']);
    $thanksText=stripslashes($arrSurveyData['0']['thanks_text']);
    $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
    $arrAnswerData=$this->getSession('answer');
    if($error){
        $arrErrorMsg=$this->getSession('error');
    }

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$surveyName;
    $objHeader->type=1;
    $str = $objHeader->show();

    if($error){
        $objHeader = new htmlheading();
        $objHeader->str='<hr /><font class="error">'.$errorLabel.'</font>';
        $objHeader->type=3;
        $str.=$objHeader->show();
    }

    if(empty($pageNo)){
        $objTabbedbox=new tabbedbox();
        $objTabbedbox->extra=' style="padding: 10px;"';
        $objTabbedbox->addTabLabel($introLabel);
        $objTabbedbox->addBoxContent($introText);
        $str.=$objTabbedbox->show();
    }else{

        // if page questions exist remove questions from unassigned list
        foreach($arrQuestionList as $key=>$question){
            $arrPageQuestionData=$this->dbPageQuestions->getQuestionRecord($question['id']);
            $arrPageQuestionList[$arrPageQuestionData['0']['page_id']][$arrPageQuestionData['0']['question_order']]=$question;
            unset($arrQuestionList[$key]);
        }

        $i=1;
        foreach($arrPageQuestionList as $pageKey=>$questionList){
            $arrPageData=$this->dbPages->getPage($pageKey);
            $pageData[$i]['page_label']=stripslashes($arrPageData['0']['page_label']);
            $pageData[$i]['page_text']=stripslashes($arrPageData['0']['page_text']);
            $arrQuestionList[$i]=$questionList;
            $i++;
        }

        $pageQuestionList=$arrQuestionList[$pageNo];

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

            if($type=='1' && $verticalAlignment=='1'){
                $colspan='colspan="'.count($arrRowList).'"';
            }elseif($type=='2' && $booleanType!='1' && $verticalAlignment=='1'){
                $colspan='colspan="'.count($arrRowList).'"';
            }elseif($type=='2' && $booleanType=='1' && $verticalAlignment=='1'){
                $colspan='colspan="3"';
            }elseif($type>='3' && $type<='5'){
                $colspan='colspan="'.(count($arrColumnList)+1).'"';
            }elseif($type=='6'){
                $colspan='colspan="'.($ratingScale+1).'"';
            }elseif($type<='8' && $verticalAlignment=='1'){
                $colspan='colspan="'.count($arrRowList).'"';
            }elseif($type=='8' && $verticalAlignment!='1'){
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

            if($error){
                if(isset($arrErrorMsg[$key])){
                    $errorMessages=$arrErrorMsg[$key];
                    foreach($errorMessages as $message){
                        $objTable->startRow();
                        $objTable->addCell('<font class="error"><b>'.$message.'</b></font>','','','',$class,$colspan);
                        $objTable->endRow();
                    }
                }
            }

            switch($type){
                case '1':// Choice-Multiple answers-Checkboxes
                    if($verticalAlignment!='1'){
                        foreach($arrRowList as $rowKey=>$row){
                            $temp=$answerField.'[check_'.($rowKey+1).']';
                            $objCheck=new checkbox($temp);
                            if(isset($arrAnswerData[$key]['check_'.($rowKey+1)])){
                                $objCheck->ischecked=TRUE;
                            }
                            $objTable->startRow();
                            $objTable->addCell($objCheck->show().' '.stripslashes($row['row_text']),'','','',$class,'');
                            $objTable->endRow();
                        }
                    }else{
                        $objTable->startRow();
                        foreach($arrRowList as $rowKey=>$row){
                            $temp=$answerField.'[check_'.($rowKey+1).']';
                            $objCheck=new checkbox($temp);
                            if(isset($arrAnswerData[$key]['check_'.($rowKey+1)])){
                                $objCheck->ischecked=TRUE;
                            }
                            $objTable->addCell($objCheck->show().' '.stripslashes($row['row_text']),'','','',$class,'');
                        }
                        $objTable->endRow();
                    }
                    break;

                case '2':// Choice-One answer-Options or dropdown
                    if($htmlElementType!='1'){
                        $temp=$answerField.'[drop]';
                        $objDrop=new dropdown($temp);
                        $objDrop->addOption('0',$optionLabel);
                        if($booleanType!='1'){
                            foreach($arrRowList as $rowKey=>$row){
                                $objDrop->addOption(($rowKey+1),stripslashes($row['row_text']));
                            }
                        }else{
                            if($trueOrFalse=='1'){
                                $objDrop->addOption('1',$yesLabel);
                                $objDrop->addOption('2',$noLabel);
                            }else{
                                $objDrop->addOption('1',$trueLabel);
                                $objDrop->addOption('2',$falseLabel);
                            }
                        }
                        if(isset($arrAnswerData[$key]['drop'])){
                            $objDrop->setSelected($arrAnswerData[$key]['drop']);
                        }
                        $objTable->startRow();
                        $objTable->addCell($objDrop->show(),'','','',$class,'');
                        $objTable->endRow();
                    }else{
                        $temp=$answerField.'[radio]';
                        if($verticalAlignment!='1'){
                            if($booleanType!='1'){
                                foreach($arrRowList as $rowKey=>$row){
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption(($rowKey+1),stripslashes($row['row_text']));
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->startRow();
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objTable->endRow();
                                }
                            }else{
                                if($trueOrFalse=='1'){
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption('1',$yesLabel);
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->startRow();
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objTable->endRow();
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption('2',$noLabel);
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->startRow();
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objTable->endRow();
                                }else{
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption('1',$trueLabel);
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->startRow();
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objTable->endRow();
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption('2',$falseLabel);
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->startRow();
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objTable->endRow();
                                }
                            }
                        }else{
                            if($booleanType!='1'){
                                $objTable->startRow();
                                foreach($arrRowList as $rowKey=>$row){
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption(($rowKey+1),stripslashes($row['row_text']));
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                }
                                $objTable->endRow();
                            }else{
                                if($trueOrFalse=='1'){
                                    $objTable->startRow();
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption('1',$yesLabel);
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption('2',$noLabel);
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objTable->endRow();
                                }else{
                                    $objTable->startRow();
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption('1',$trueLabel);
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objRadio=new radio($temp);
                                    $objRadio->addOption('2',$falseLabel);
                                    if(isset($arrAnswerData[$key]['radio'])){
                                        $objRadio->setSelected($arrAnswerData[$key]['radio']);
                                    }
                                    $objTable->addCell($objRadio->show(),'','','',$class,'');
                                    $objTable->endRow();
                                }
                            }
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
                        $objTable->addCell(stripslashes($row['row_text']),'','','',$class,'');
                        foreach($arrColumnList as $columnKey=>$column){
                            $temp=$answerField.'[check_'.($rowKey+1).'_'.($columnKey+1).']';
                            $objCheck=new checkbox($temp);                        if(isset($arrAnswerData[$key]['check_'.($rowKey+1).'_'.($columnKey+1)])){
                                $objCheck->ischecked=TRUE;
                            }
                            $objTable->addCell($objCheck->show(),'','','center',$class,'');
                        }
                        $objTable->endRow();
                    }
                    break;

                case '4':// Matrix-Multiple answers per row textboxes
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
                            if(isset($arrAnswerData[$key])){
                                $answer=stripslashes($arrAnswerData[$key]['text_'.($rowKey+1).'_'.($columnKey+1)]);
                            }else{
                                $answer='';
                            }
                            $temp=$answerField.'[text_'.($rowKey+1).'_'.($columnKey+1).']';
                            $objInput=new textinput($temp,$answer,'','20');
                            $objTable->addCell($objInput->show(),'','','center',$class,'');
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
                            $temp=$answerField.'[radio_'.($rowKey+1).']';
                            $objRadio=new radio($temp);
                            $objRadio->addOption(($columnKey+1),'');
                            if(isset($arrAnswerData[$key]['radio_'.($rowKey+1)])){
                                $objRadio->setSelected($arrAnswerData[$key]['radio_'.($rowKey+1)]);
                            }
                            $objTable->addCell($objRadio->show(),'','','center',$class,'');
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
                            $temp=$answerField.'[radio_'.($rowKey+1).']';
                            $objRadio=new radio($temp);
                            $objRadio->addOption($ii,'');
                            if(isset($arrAnswerData[$key]['radio_'.($rowKey+1)])){
                                $objRadio->setSelected($arrAnswerData[$key]['radio_'.($rowKey+1)]);
                            }
                            $objTable->addCell($objRadio->show(),'','','center',$class,'');
                        }
                        $objTable->endRow();
                    }
                    break;

                case '7':// Open ended-Textarea(Comments box)
                    if(isset($arrAnswerData[$key])){
                        $answer=stripslashes($arrAnswerData[$key]['area']);
                    }else{
                        $answer='';
                    }
                    $temp=$answerField.'[area]';
                    if($htmlElementType!='1'){
                        $objInput=new textinput($temp,$answer,'','88');
                        $textAnswer=$objInput->show();
                    }else{
                        $objText=new textarea($temp,$answer,'4','85');
                        $textAnswer=$objText->show();
                    }
                    $objTable->startRow();
                    $objTable->addCell($textAnswer,'','','',$class,'');
                    $objTable->endRow();
                    break;

                case '8':// Open ended-Constant sum
                    if($verticalAlignment!='1'){
                        foreach($arrRowList as $rowKey=>$row){
                            if(isset($arrAnswerData[$key])){
                                $answer=stripslashes($arrAnswerData[$key]['text_'.($rowKey+1)]);
                            }else{
                                $answer='';
                            }
                            $temp=$answerField.'[text_'.($rowKey+1).']';
                            $objInput=new textinput($temp,$answer,'','6');
                            $objInput->extra='maxlength="6"';
                            $objTable->startRow();
                            $objTable->addCell($objInput->show(),'10%','','',$class,'');
                            $objTable->addCell(stripslashes($row['row_text']),'90%','','',$class,'');
                            $objTable->endRow();
                        }
                    }else{
                        $objTable->startRow();
                        foreach($arrRowList as $rowKey=>$row){
                            if(isset($arrAnswerData[$key])){
                                $answer=stripslashes($arrAnswerData[$key]['text_'.($rowKey+1)]);
                            }else{
                                $answer='';
                            }
                            $temp=$answerField.'[text_'.($rowKey+1).']';
                            $objInput=new textinput($temp,$answer,'','6');
                            $objInput->extra='maxlength="6"';
                            $objTable->addCell(stripslashes($row['row_text']).'&nbsp;'.$objInput->show(),'','','',$class,'');
                        }
                        $objTable->endRow();
                    }
                    break;

                case '9':// Open ended-Number
                    if(isset($arrAnswerData[$key])){
                        $answer=stripslashes($arrAnswerData[$key]['text']);
                    }else{
                        $answer='';
                    }
                    $temp=$answerField.'[text]';
                    $objInput=new textinput($temp,$answer,'','7');
                    $objInput->extra='maxlength="7"';
                    $objTable->startRow();
                    $objTable->addCell($objInput->show(),'','','',$class,'');
                    $objTable->endRow();
                    break;

                case '10':// Open ended-Date
                    if(isset($arrAnswerData[$key]['date'])){
                        $answer=$arrAnswerData[$key]['date'];
                    }else{
                        $answer='';
                    }
                    $temp='questionNo_'.($key);
                    $dateText = $objPopupcal->show($temp, 'yes', 'no', $answer);

                    $objTable->startRow();
                    $objTable->addCell($dateText,'','','',$class,'');
                    $objTable->endRow();
                    break;
            }

            if($questionSubtext!=''){
                $objTable->startRow();
                $objTable->addCell($questionSubtext,'','','',$class,$colspan);
                $objTable->endRow();
            }

            if($commentRequested=='1'){
                if(isset($arrAnswerData[$key]['question_comment'])){
                    $comment=$arrAnswerData[$key]['question_comment'];
                }else{
                    $comment='';
                }
                $temp=$answerField.'[question_comment]';
                $objText=new textarea($temp,$comment,'3','85');
                $objTable->startRow();
                $objTable->addCell('<b>'.$commentText.'</b>','','','',$class,$colspan);
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell($objText->show(),'','','',$class,$colspan);
                $objTable->endRow();
            }
            $tabContent.=$objTable->show();
        }
        $tabContent=stripslashes($pageData[$pageNo]['page_text']).$tabContent;

        $objTabbedbox=new tabbedbox();
        $objTabbedbox->extra=' style="padding: 10px;"';
        $objTabbedbox->addTabLabel($pageData[$pageNo]['page_label']);
        $objTabbedbox->addBoxContent($tabContent);
        $str.=$objTabbedbox->show();
    }

// set up submit button
    if(empty($pageNo)){
        $objButton=new button('nextButton',$nextLabel);
        $objButton->extra=' onclick="javascript:
            this.disabled=\'disabled\';
            document.getElementById(\'input_newPageNo\').value=\''.($pageNo+1).'\';
            document.getElementById(\'form_surveyForm\').submit();
        "';
        $nextButton=$objButton->show();
        $buttons=$nextButton;
    }elseif($pageNo==count($arrQuestionList)){
        $objButton=new button('backButton',$backLabel);
        $objButton->extra=' onclick="javascript:
            this.disabled=\'disabled\';
            document.getElementById(\'input_newPageNo\').value=\''.($pageNo-1).'\';
            document.getElementById(\'form_surveyForm\').submit();
        "';
        $backButton=$objButton->show();

        $objButton=new button('submitButton',$submitLabel);
        $objButton->extra=' onclick="javascript:
            this.disabled=\'disabled\';
            document.getElementById(\'input_newPageNo\').value=\'submit\';
            document.getElementById(\'form_surveyForm\').submit();
        "';
        $submitButton=$objButton->show();

        $buttons=$backButton.'&nbsp;'.$submitButton;
    }else{
        $objButton=new button('backButton',$backLabel);
        $objButton->extra=' onclick="javascript:
            this.disabled=\'disabled\';
            document.getElementById(\'input_newPageNo\').value=\''.($pageNo-1).'\';
            document.getElementById(\'form_surveyForm\').submit();
        "';
        $backButton=$objButton->show();

        $objButton=new button('nextButton',$nextLabel);
        $objButton->extra=' onclick="javascript:
            this.disabled=\'disabled\';
            document.getElementById(\'input_newPageNo\').value=\''.($pageNo+1).'\';
            document.getElementById(\'form_surveyForm\').submit();
        "';
        $nextButton=$objButton->show();

        $buttons=$backButton.'&nbsp;'.$nextButton;
    }

    $objInput=new textinput('pageNo',$pageNo);
    $objInput->fldType='hidden';
    $str.=$objInput->show();

    $objInput=new textinput('newPageNo');
    $objInput->fldType='hidden';
    $str.=$objInput->show();

// set up form
    $objForm=new form('surveyForm',$this->uri(array('action'=>'validateresponse','survey_id'=>$surveyId)));
    $objForm->addToForm($str);
    $objForm->addToForm('<br />'.$buttons);
    $str=$objForm->show();

    $objLayer = new layer();
    $objLayer->padding='10px';
    $objLayer->addToStr($str);
    echo $objLayer->show();
?>
