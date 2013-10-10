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
* Questions add edit template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objButton=&$this->loadClass('button','htmlelements');
    $objForm=&$this->loadClass('form','htmlelements');
    $objLink=&$this->loadClass('link','htmlelements');
    $objInput=&$this->loadClass('textinput','htmlelements');
    $objDrop=&$this->loadClass('dropdown','htmlelements');
    $objRadio=&$this->loadClass('radio','htmlelements');
    $objCheck=&$this->loadClass('checkbox','htmlelements');

// set up language items
    $questionLabel=$this->objLanguage->languageText('mod_survey_question', 'survey');
    $surveyLabel=$this->objLanguage->languageText('mod_survey_survey', 'survey');
    $optionsLabel=$this->objLanguage->languageText('mod_survey_options', 'survey');
    $answerLabel=$this->objLanguage->languageText('mod_survey_answers', 'survey');
    $submitLabel=$this->objLanguage->languageText('word_submit');
    $errorLabel=$this->objLanguage->languageText('mod_survey_creationerrors', 'survey');

// set up code to text elements
    $array=array('item'=>strtolower($questionLabel));
    if($mode=='add'){
        $heading=$this->objLanguage->code2Txt('mod_survey_add', 'survey',$array);
    }else{
        $heading=$this->objLanguage->code2Txt('mod_survey_edit', 'survey',$array);
    }
    $returnQuestionLabel=$this->objLanguage->code2Txt('mod_survey_return', 'survey',$array);
    $array=array('item'=>strtolower($surveyLabel));
    $returnSurveyLabel=$this->objLanguage->code2Txt('mod_survey_return', 'survey',$array);

// set up data
    if(!$error && empty($update)){
        if($mode=='add'){
            $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
            $survey_name=$arrSurveyData['0']['survey_name'];
            $arrQuestionData['question_id']='';
            $arrQuestionData['survey_id']=$surveyId;
            $arrQuestionData['type_id']='';
            $arrQuestionData['question_text']='';
            $arrQuestionData['question_subtext']='';
            $arrQuestionData['compulsory_question']='';
            $arrQuestionData['vertical_alignment']='';
            $arrQuestionData['comment_requested']='';
            $arrQuestionData['comment_request_text']='';
            $arrQuestionData['radio_element']='';
            $arrQuestionData['preset_options']='';
            $arrQuestionData['true_or_false']='';
            $arrQuestionData['rating_scale']='5';
            $arrQuestionData['constant_sum']='';
            $arrQuestionData['minimum_number']='';
            $arrQuestionData['maximum_number']='';
            $arrRowData=array(array('id'=>'','row_order'=>'','row_text'=>''),array('id'=>'','row_order'=>'','row_text'=>''),array('id'=>'','row_order'=>'','row_text'=>''));
            $arrColumnData=array(array('id'=>'','column_order'=>'','column_text'=>''),array('id'=>'','column_order'=>'','column_text'=>''),array('id'=>'','column_order'=>'','column_text'=>''));
        }else{
            $arrQuestionData=$this->dbQuestion->getQuestion($questionId);
            $arrQuestionData=$arrQuestionData['0'];
            $arrQuestionData['question_id']=$arrQuestionData['id'];
            $surveyId=$arrQuestionData['survey_id'];
            $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
            $survey_name=stripslashes($arrSurveyData['0']['survey_name']);
            $arrRowData=$this->dbRows->listQuestionRows($questionId);
            $arrColumnData=$this->dbColumns->listQuestionColumns($questionId);
        }
    }else{
        $arrQuestionData=$this->getSession('question');
        $surveyId=$arrQuestionData['survey_id'];
        $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
        $survey_name=$arrSurveyData['0']['survey_name'];
        $arrRowData=$this->getSession('row');
        $arrColumnData=$this->getSession('column');
        $arrErrorMsg=$this->getSession('error');
    }

    $questionId=$arrQuestionData['question_id'];
    $surveyId=$arrQuestionData['survey_id'];
    $typeId=$arrQuestionData['type_id'];

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$heading;
    $objHeader->type=1;
    echo $objHeader->show();

    $objHeader = new htmlheading();
    $objHeader->str=$survey_name;
    $objHeader->type=3;
    echo $objHeader->show().'<hr />';

    if($error){
        $objHeader = new htmlheading();
        $objHeader->str='<font class="error">'.$errorLabel.'</font><hr />';
        $objHeader->type=3;
        echo $objHeader->show();
    }

// set up form to display the question type dropdown
    if($mode=='add'){
        $arrTypeDropdown=$this->questions->arrTypeDropdown($arrQuestionData);
        $objTable=$this->questions->makeTable($arrTypeDropdown);

        $objInput=new textinput('survey_id',$surveyId);
        $objInput->fldType='hidden';
        $hiddenText=$objInput->show();

        $objInput=new textinput('update','');
        $objInput->fldType='hidden';
        $hiddenText.=$objInput->show();

        $objForm=new form('typeupdate',$this->uri(array('action'=>'validatequestion','mode'=>$mode,'survey_id'=>$surveyId)));
        $objForm->addToForm($objTable.$hiddenText);
        $typeTable=$objForm->show();
        echo $typeTable;
    }

// set up add question template based on the question type
    $typeId=$arrQuestionData['type_id'];
    $temp=explode('_',$typeId);
    if(isset($temp['1'])){
        $questionType=$temp['1'];
    }else{
        $questionType='';
    }

    if(!empty($questionType)){
        $objHeader = new htmlheading();
        $objHeader->str=$questionLabel;
        $objHeader->type=3;
        $questionHead=$objHeader->show();
        $formElements=$questionHead;

        if($error && isset($arrErrorMsg['question_text'])){
            $formElements.='<br /><font class="error"><b>'.stripslashes($arrErrorMsg['question_text']).'</b></font>';
        }
        $arrQuestionTextinput=$this->questions->arrQuestionTextinput($arrQuestionData);
        $objTable=$this->questions->makeTable($arrQuestionTextinput);
        $formElements.=$objTable;

        $objHeader = new htmlheading();
        $objHeader->str=$optionsLabel;
        $objHeader->type=3;
        $optionsHead=$objHeader->show();
        $formElements.=$optionsHead;

        $arrRequiredCheckbox=$this->questions->arrRequiredCheckbox($arrQuestionData);
        $objTable=$this->questions->makeTable($arrRequiredCheckbox);
        $formElements.=$objTable;

        switch($questionType){
            case 1: // Choice-Multiple answers-Checkboxes
                $arrAlignmentRadio=$this->questions->arrAlignmentRadio($arrQuestionData);
                $objTable=$this->questions->makeTable($arrAlignmentRadio);
                $formElements.=$objTable;

                $objHeader = new htmlheading();
                $objHeader->str=$answerLabel;
                $objHeader->type=3;
                $answerHead=$objHeader->show();

                $formElements.=$answerHead;

                if($error && isset($arrErrorMsg['rows'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['rows'].'</b></font>';
                }
                $arrRowTable=$this->questions->arrRowTable($arrRowData);
                $objTable=$this->questions->makeTable($arrRowTable);
                $formElements.=$objTable;
                break;

            case 2: // Choice-One answer-Options or dropdown
                $arrPresetOptions=$this->questions->arrPresetOptions($arrQuestionData,$mode);
                $objTable=$this->questions->makeTable($arrPresetOptions);
                $formElements.=$objTable;

                $arrRadioElement=$this->questions->arrRadioElement($arrQuestionData);
                if($arrQuestionData['radio_element']=='1'){
                    $arrAlignmentRadio=$this->questions->arrAlignmentRadio($arrQuestionData);
                    $tableData=array_merge($arrRadioElement,$arrAlignmentRadio);
                }else{
                    $tableData=$arrRadioElement;
                }
                $objTable=$this->questions->makeTable($tableData);
                $formElements.=$objTable;

                $objHeader = new htmlheading();
                $objHeader->str=$answerLabel;
                $objHeader->type=3;
                $answerHead=$objHeader->show();
                $formElements.=$answerHead;

                if($arrQuestionData['preset_options']!='1'){
                    if($error && isset($arrErrorMsg['rows'])){
                        $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['rows'].'</b></font>';
                    }
                    $arrRowTable=$this->questions->arrRowTable($arrRowData);
                    $objTable=$this->questions->makeTable($arrRowTable);
                    $formElements.=$objTable;
                }else{
                    $arrBooleanType=$this->questions->arrBooleanType($arrQuestionData);
                    $objTable=$this->questions->makeTable($arrBooleanType);
                    $formElements.=$objTable;
                }
                break;

            case 3: // Matrix-Multiple answers per row-Checkboxes
                $objHeader = new htmlheading();
                $objHeader->str=$answerLabel;
                $objHeader->type=3;
                $answerHead=$objHeader->show();
                $formElements.=$answerHead;

                if($error && isset($arrErrorMsg['rows'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['rows'].'</b></font>';
                }
                $arrRowTable=$this->questions->arrRowTable($arrRowData);
                $objTable=$this->questions->makeTable($arrRowTable);
                $formElements.=$objTable;

                if($error && isset($arrErrorMsg['columns'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['columns'].'</b></font>';
                }
                $arrColumnTable=$this->questions->arrColumnTable($arrColumnData);
                $objTable=$this->questions->makeTable($arrColumnTable);
                $formElements.=$objTable;
                break;

            case 4: // Matrix-Multiple answers per row-Textboxes
                $objHeader = new htmlheading();
                $objHeader->str=$answerLabel;
                $objHeader->type=3;
                $answerHead=$objHeader->show();
                $formElements.=$answerHead;

                if($error && isset($arrErrorMsg['rows'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['rows'].'</b></font>';
                }
                $arrRowTable=$this->questions->arrRowTable($arrRowData);
                $objTable=$this->questions->makeTable($arrRowTable);
                $formElements.=$objTable;

                if($error && isset($arrErrorMsg['columns'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['columns'].'</b></font>';
                }
                $arrColumnTable=$this->questions->arrColumnTable($arrColumnData);
                $objTable=$this->questions->makeTable($arrColumnTable);
                $formElements.=$objTable;
                break;

            case 5: // Matrix-One answer per row-Options
                $objHeader = new htmlheading();
                $objHeader->str=$answerLabel;
                $objHeader->type=3;
                $answerHead=$objHeader->show();
                $formElements.=$answerHead;

                if($error && isset($arrErrorMsg['rows'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['rows'].'</b></font>';
                }
                $arrRowTable=$this->questions->arrRowTable($arrRowData);
                $objTable=$this->questions->makeTable($arrRowTable);
                $formElements.=$objTable;

                if($error && isset($arrErrorMsg['columns'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['columns'].'</b></font>';
                }
                $arrColumnTable=$this->questions->arrColumnTable($arrColumnData);
                $objTable=$this->questions->makeTable($arrColumnTable);
                $formElements.=$objTable;
                break;

            case 6: // Matrix-Rating scale (Numeric)
                $arrRatingDropdown=$this->questions->arrRatingDropdown($arrQuestionData);
                $objTable=$this->questions->makeTable($arrRatingDropdown);
                $formElements.=$objTable;

                $objHeader = new htmlheading();
                $objHeader->str=$answerLabel;
                $objHeader->type=3;
                $answerHead=$objHeader->show();
                $formElements.=$answerHead;

                if($error && isset($arrErrorMsg['rows'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['rows'].'</b></font>';
                }
                $arrRowTable=$this->questions->arrRowTable($arrRowData);
                $objTable=$this->questions->makeTable($arrRowTable);
                $formElements.=$objTable;
                break;
            case 7: // Open ended - Text
                $arrRadioElement=$this->questions->arrRadioElement($arrQuestionData);
                $objTable=$this->questions->makeTable($arrRadioElement);
                $formElements.=$objTable;
                break;

            case 8: // Open ended-Constant sum
                $arrAlignmentRadio=$this->questions->arrAlignmentRadio($arrQuestionData);
                $objTable=$this->questions->makeTable($arrAlignmentRadio);
                $formElements.=$objTable;

                $objHeader = new htmlheading();
                $objHeader->str=$answerLabel;
                $objHeader->type=3;
                $answerHead=$objHeader->show();
                $formElements.=$answerHead;

                if($error && isset($arrErrorMsg['constant_sum'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['constant_sum'].'</b></font>';
                }
                $arrSumTextinput=$this->questions->arrSumTextinput($arrQuestionData);
                $objTable=$this->questions->makeTable($arrSumTextinput);
                $formElements.=$objTable;

                if($error && isset($arrErrorMsg['rows'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['rows'].'</b></font>';
                }
                $arrRowTable=$this->questions->arrRowTable($arrRowData);
                $objTable=$this->questions->makeTable($arrRowTable);
                $formElements.=$objTable;
                break;

            case 9: // Open ended-Number
                if($error && isset($arrErrorMsg['minimum_number'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['minimum_number'].'</b></font>';
                }
                $arrMinimumTextinput=$this->questions->arrMinimumTextInput($arrQuestionData);
                $objTable=$this->questions->makeTable($arrMinimumTextinput);
                $formElements.=$objTable;

                if($error && isset($arrErrorMsg['maximum_number'])){
                    $formElements.='<br /><font class="error"><b>'.$arrErrorMsg['maximum_number'].'</b></font>';
                }
                $arrMaximumTextinput=$this->questions->arrMaximumTextInput($arrQuestionData);
                $objTable=$this->questions->makeTable($arrMaximumTextinput);
                $formElements.=$objTable;
                break;
         }

        if($error && isset($arrErrorMsg['comment_request_text'])){
            $formElements.='<br /><font class="error"><b>'.stripslashes($arrErrorMsg['comment_request_text']).'</b></font>';
        }
        $arrCommentsCheckbox=$this->questions->arrCommentsCheckbox($arrQuestionData);
        $objTable=$this->questions->makeTable($arrCommentsCheckbox);
        $formElements.=$objTable;

    // set up hidden fields
        $objInput=new textinput('question_id',$questionId);
        $objInput->fldType='hidden';
        $formElements.=$objInput->show();

        $objInput=new textinput('survey_id',$surveyId);
        $objInput->fldType='hidden';
        $formElements.=$objInput->show();

        $objInput=new textinput('type_id',$typeId);
        $objInput->fldType='hidden';
        $formElements.=$objInput->show();

        $objInput=new textinput('update','');
        $objInput->fldType='hidden';
        $formElements.=$objInput->show();

    // set up submit button
        $objButton=new button('submitButton',$submitLabel);
        $objButton->extra=' onclick="javascript:
            var el =document.getElementsByName(\'update\');
            if(el.length==1){
                el[0].value=\'save\'
            }else{
                el[1].value=\'save\'
            };
            this.disabled=\'disabled\';
            document.getElementById(\'form_questionForm\').submit();
        "';
        $submitButton=$objButton->show();

    // set up form
        $objForm=new form('questionForm',$this->uri(array('action'=>'validatequestion','mode'=>$mode)));
        $objForm->addToForm($formElements.'<br />'.$submitButton);
        $str=$objForm->show();
        echo $str;
    }

    // set up return links
    $str='<hr />';
    $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
    if(!empty($arrQuestionList)){
        $objLink=new link($this->uri(array('action'=>'listquestions','survey_id'=>$surveyId),'survey'));
        $objLink->link=$returnQuestionLabel;
        $returnLink=$objLink->show();
        $str.=$returnLink.' / ';
    }
    $objLink=new link($this->uri(array(),'survey'));
    $objLink->link=$returnSurveyLabel;
    $returnLink=$objLink->show();
    $str.=$returnLink;

    echo $str;
?>