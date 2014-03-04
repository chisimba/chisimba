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
* Questions list template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objIcon=&$this->newObject('geticon','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objButton=&$this->loadClass('button','htmlelements');
    $objForm=&$this->loadClass('form','htmlelements');
    $objLink=&$this->loadClass('link','htmlelements');
    $objDrop=&$this->loadClass('dropdown','htmlelements');
    $objCheck=&$this->loadClass('checkbox','htmlelements');
    $objTabbedbox=&$this->loadClass('tabbedbox','htmlelements');

// set up language items
    $heading=$this->objLanguage->languageText('mod_survey_list','survey');
    $numberLabel=$this->objLanguage->languageText('mod_survey_number','survey');
    $typeLabel=$this->objLanguage->languageText('mod_survey_type','survey');
    $questionLabel=$this->objLanguage->languageText('mod_survey_question','survey');
    $surveyLabel=$this->objLanguage->languageText('mod_survey_survey','survey');
    $previewLabel=$this->objLanguage->languageText('mod_survey_preview','survey');
    $requiredLabel=$this->objLanguage->languageText('mod_survey_required','survey');
    $yesLabel=$this->objLanguage->languageText('word_yes');
    $noLabel=$this->objLanguage->languageText('word_no');
    $manageLabel=$this->objLanguage->languageText('mod_survey_manage','survey');
    $submitLabel=$this->objLanguage->languageText('word_submit');
    $unassignedLabel=$this->objLanguage->languageText('mod_survey_unassignedquestions','survey');

// set up code to text elements
    $array=array('item'=>strtolower($questionLabel));
    $addLabel=$this->objLanguage->code2Txt('mod_survey_add','survey',$array);
    $editLabel=$this->objLanguage->code2Txt('mod_survey_edit','survey',$array);
    $deleteLabel=$this->objLanguage->code2Txt('mod_survey_delete','survey',$array);
    $deleteconfirmLabel=$this->objLanguage->code2Txt('mod_survey_deleteconfirm','survey',$array);
    $upLabel=$this->objLanguage->code2Txt('mod_survey_up','survey',$array);
    $downLabel=$this->objLanguage->code2Txt('mod_survey_down','survey',$array);
    $copyLabel=$this->objLanguage->code2Txt('mod_survey_copy','survey',$array);
    $array=array('item'=>strtolower($surveyLabel));
    $returnSurveyLabel=$this->objLanguage->code2Txt('mod_survey_return','survey',$array);

// set up data
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $surveyName=$arrSurveyData['0']['survey_name'];
    $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
    $arrPageList=$this->dbPages->listPages($surveyId);

// set up add icon
    $objIcon->title=$addLabel;
    $addIcon=$objIcon->getAddIcon($this->uri(array('action'=>'addquestion','survey_id'=>$surveyId)));

// set up manage pages icon
    $objIcon->title=$manageLabel;
    $manageIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'managepages','survey_id'=>$surveyId)),'surveymanagepages');

// set up preview icon
    $objIcon->title=$previewLabel;
    $previewIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'previewsurvey','survey_id'=>$surveyId)),'surveypreview');

    if(count($arrQuestionList)>='2'){
        $icons=$manageIcon.'&nbsp;'.$previewIcon;
    }else{
        $icons=$previewIcon;
    }

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$heading.' '.$addIcon;
    $objHeader->type=1;
    echo $objHeader->show();

    $objHeader = new htmlheading();
    $objHeader->str=$surveyName.' '.$icons;
    $objHeader->type=3;
    echo $objHeader->show();

    if(empty($arrPageList)){
        // set up table
        $objTable=new htmltable();
        $objTable->cellspacing='2';
        $objTable->cellpadding='2';

        $objTable->startHeaderRow();
        $objTable->addHeaderCell($noLabel.'.','1%','','center','heading','');
        $objTable->addHeaderCell($requiredLabel,'1%','','center','heading','');
        $objTable->addHeaderCell($typeLabel,'25%','','center','heading','');
        $objTable->addHeaderCell($questionLabel,'','','center','heading','');
        $objTable->addHeaderCell('','16%','','center','heading','rowspan="2"');
        $objTable->endHeaderRow();

        $i=0;
        foreach($arrQuestionList as $question){
            $class=(($i++%2)==0)?'odd':'even';

            $questionId=$question['id'];
            $surveyId=$question['survey_id'];
            $typeId=$question['type_id'];
            $orderNumber=$question['question_order'];
            $questionText=stripslashes($question['question_text']);
            $questionDescription=$this->dbType->getQuestionTypeDescription($typeId);
            $required=$question['compulsory_question'];

            // set up required indicator
            if($required=='1'){
                $required=$yesLabel;
            }else{
                $required=$noLabel;
            }

            // set up edit question icon
            $objIcon->title=$editLabel;
            $editIcon=$objIcon->getEditIcon($this->uri(array('action'=>'editquestion','question_id'=>$questionId,'survey_id'=>$surveyId)));

            // set up delete question icon
            $deleteArray=array('action'=>'deletequestion','question_id'=>$questionId,'survey_id'=>$surveyId);
            $deleteIcon=$objIcon->getDeleteIconWithConfirm('', $deleteArray,'survey',$deleteconfirmLabel);

            // set up move down icon
            $objIcon->title=$downLabel;
            $downIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'movequestion','question_id'=>$questionId,'direction'=>'down','survey_id'=>$surveyId)),'mvdown');

            // set up move up icon
            $objIcon->title=$upLabel;
            $upIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'movequestion','question_id'=>$questionId,'direction'=>'up','survey_id'=>$surveyId)),'mvup');

            // set up copy icon
            $objIcon->title=$copyLabel;
            $copyIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'copyquestion','question_id'=>$questionId,'survey_id'=>$surveyId)),'surveycopy');

            // show icons
            if(count($arrQuestionList)=='1'){
                $icons=$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
            }elseif($orderNumber=='1'){
                $icons=$downIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
            }elseif($orderNumber==count($arrQuestionList)){
                $icons=$upIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
            }else{
                $icons=$downIcon.'&nbsp;'.$upIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
            }

            $objTable->startRow();
            $objTable->addCell($orderNumber,'1%','top','center',$class,'');
            $objTable->addCell($required,'1%','top','center',$class,'');
            $objTable->addCell($questionDescription,'25%','top','',$class,'');
            $objTable->addCell($questionText,'','','',$class,'');
            $objTable->addCell($icons,'16%','','right',$class,'');
            $objTable->endRow();
        }
        echo '<hr />'.$objTable->show().'<hr />';
    }else{
        // if page questions exist remove questions from unassigned list
        foreach($arrQuestionList as $key=>$question){
            $arrPageQuestionData=$this->dbPageQuestions->getQuestionRecord($question['id']);
            if($arrPageQuestionData!=FALSE){
                $arrPageQuestionList[$arrPageQuestionData['0']['page_id']][$arrPageQuestionData['0']['question_order']]=$question;
                unset($arrQuestionList[$key]);
            }
        }
        if(!empty($arrQuestionList)){
            // set up table
            $objTable=new htmltable();
            $objTable->cellspacing='2';
            $objTable->cellpadding='2';

            $objTable->startHeaderRow();
            $objTable->addHeaderCell('','1%','','center','heading','');
            $objTable->addHeaderCell($noLabel.'.','1%','','center','heading','');
            $objTable->addHeaderCell($requiredLabel,'1%','','center','heading','');
            $objTable->addHeaderCell($typeLabel,'25%','','center','heading','');
            $objTable->addHeaderCell($questionLabel,'','','center','heading','');
            $objTable->addHeaderCell('','16%','','center','heading','rowspan="2"');
            $objTable->endHeaderRow();

            $i=0;
            foreach($arrQuestionList as $question){
                $class=(($i++%2)==0)?'odd':'even';

                $questionId=$question['id'];
                $surveyId=$question['survey_id'];
                $typeId=$question['type_id'];
                $orderNumber=$question['question_order'];
                $questionText=stripslashes($question['question_text']);
                $questionDescription=$this->dbType->getQuestionTypeDescription($typeId);
                $required=$question['compulsory_question'];

                // set up required indicator
                if($required=='1'){
                    $required=$yesLabel;
                }else{
                    $required=$noLabel;
                }

                // set up edit question icon
                $objIcon->title=$editLabel;
                $editIcon=$objIcon->getEditIcon($this->uri(array('action'=>'editquestion','question_id'=>$questionId,'survey_id'=>$surveyId)));

                // set up delete question icon
                $deleteArray=array('action'=>'deletequestion','question_id'=>$questionId,'survey_id'=>$surveyId);
                $deleteIcon=$objIcon->getDeleteIconWithConfirm('', $deleteArray,'survey',$deleteconfirmLabel);

                // set up move down icon
                $objIcon->title=$downLabel;
                $downIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'movequestion','question_id'=>$questionId,'direction'=>'down','survey_id'=>$surveyId)),'mvdown');

                // set up move up icon
                $objIcon->title=$upLabel;
                $upIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'movequestion','question_id'=>$questionId,'direction'=>'up','survey_id'=>$surveyId)),'mvup');

                // set up copy icon
                $objIcon->title=$copyLabel;
                $copyIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'copyquestion','question_id'=>$questionId,'survey_id'=>$surveyId)),'surveycopy');

                // show icons
                if(count($arrQuestionList)=='1'){
                    $icons=$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
                }elseif($i=='1'){
                    $icons=$downIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
                }elseif($i==count($arrQuestionList)){
                    $icons=$upIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
                }else{
                    $icons=$downIcon.'&nbsp;'.$upIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
                }

                // set up assign question to page checkbox
                $objCheck=new checkbox('arrQuestionId[]');
                $objCheck->value=$questionId;
                $pageCheck=$objCheck->show();

                $objTable->startRow();
                $objTable->addCell($pageCheck,'1%','','center',$class,'');
                $objTable->addCell($orderNumber,'1%','top','center',$class,'');
                $objTable->addCell($required,'1%','top','center',$class,'');
                $objTable->addCell($questionDescription,'25%','top','',$class,'');
                $objTable->addCell($questionText,'','','',$class,'');
                $objTable->addCell($icons,'16%','','right',$class,'');
                $objTable->endRow();
            }
            $str=$objTable->show();

            // set up assign page dropdown
            $objDrop=new dropdown('newPageId');
            foreach($arrPageList as $page){
                $objDrop->addOption($page['id'],$page['page_label']);
            }
            $assignDrop=$objDrop->show();

            // set up submit button
            $objButton=new button('submitButton',$submitLabel);
            $objButton->extra=' onclick="javascript:
                this.disabled=\'disabled\';
                document.getElementById(\'form_assignForm\').submit();
            "';
            $submitButton=$objButton->show();

            // set up row with assign questions to page dropdown
            $objTable=new htmltable();
            $objTable->cellspacing='2';
            $objTable->cellpadding='2';

            $objTable->startHeaderRow();
            $objTable->addHeaderCell($assignDrop.'&nbsp;'.$submitButton,'','','left','heading','');
            $objTable->endHeaderRow();

            $str.=$objTable->show();

            // set up form
            $objForm=new form('assignForm',$this->uri(array('action'=>'assignquestions','survey_id'=>$surveyId)));
            $objForm->addToForm($str);
            $assignForm=$objForm->show();

            $objTabbedbox=new tabbedbox();
            $objTabbedbox->extra=' style="padding: 10px;"';
            $objTabbedbox->addTabLabel($unassignedLabel);
            $objTabbedbox->addBoxContent($assignForm);
            echo $objTabbedbox->show();
        }
        foreach($arrPageList as $page){
            $pageId=$page['id'];
            $pageLabel=$page['page_label'];

            // set up table
            $objTable=new htmltable();
            $objTable->cellspacing='2';
            $objTable->cellpadding='2';

            $objTable->startHeaderRow();
            $objTable->addHeaderCell('','1%','','center','heading','');
            $objTable->addHeaderCell($noLabel.'.','1%','','center','heading','');
            $objTable->addHeaderCell($requiredLabel,'1%','','center','heading','');
            $objTable->addHeaderCell($typeLabel,'25%','','center','heading','');
            $objTable->addHeaderCell($questionLabel,'','','center','heading','');
            $objTable->addHeaderCell('','16%','','center','heading','rowspan="2"');
            $objTable->endHeaderRow();
            $str=$objTable->show();

            if(!empty($arrPageQuestionList)){
                foreach($arrPageQuestionList as $key=>$pageQuestionList){
                    if($key==$pageId){
                        $i=0;
                        ksort($pageQuestionList);
                        foreach($pageQuestionList as $question){
                            $class=(($i++%2)==0)?'odd':'even';

                            $questionId=$question['id'];
                            $surveyId=$question['survey_id'];
                            $typeId=$question['type_id'];
                            $orderNumber=$i;
                            $questionText=stripslashes($question['question_text']);
                            $questionDescription=$this->dbType->getQuestionTypeDescription($typeId);
                            $required=$question['compulsory_question'];

                            // set up required indicator
                            if($required=='1'){
                                $required=$yesLabel;
                            }else{
                                $required=$noLabel;
                            }

                            // set up edit question icon
                            $objIcon->title=$editLabel;
                            $editIcon=$objIcon->getEditIcon($this->uri(array('action'=>'editquestion','question_id'=>$questionId,'survey_id'=>$surveyId)));

                            // set up delete question icon
                            $deleteArray=array('action'=>'deletequestion','question_id'=>$questionId,'survey_id'=>$surveyId);
                            $deleteIcon=$objIcon->getDeleteIconWithConfirm('', $deleteArray,'survey',$deleteconfirmLabel);

                            // set up move down icon
                            $objIcon->title=$downLabel;
                            $downIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'movepagequestion','question_id'=>$questionId,'direction'=>'down','survey_id'=>$surveyId)),'mvdown');

                            // set up move up icon
                            $objIcon->title=$upLabel;
                            $upIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'movepagequestion','question_id'=>$questionId,'direction'=>'up','survey_id'=>$surveyId)),'mvup');

                            // set up copy icon
                            $objIcon->title=$copyLabel;
                            $copyIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'copyquestion','question_id'=>$questionId,'survey_id'=>$surveyId)),'surveycopy');

                            // show icons
                            if(count($pageQuestionList)=='1'){
                                $icons=$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
                            }elseif($orderNumber=='1'){
                                $icons=$downIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
                            }elseif($orderNumber==count($pageQuestionList)){
                                $icons=$upIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
                            }else{
                                $icons=$downIcon.'&nbsp;'.$upIcon.'&nbsp;'.$copyIcon.'&nbsp;'.$editIcon.'&nbsp;'.$deleteIcon;
                            }

                            // set up assign question to page checkbox
                            $objCheck=new checkbox('arrQuestionId[]');
                            $objCheck->value=$questionId;
                            $pageCheck=$objCheck->show();

                            $objTable->startRow();
                            $objTable->addCell($pageCheck,'1%','','center',$class,'');
                            $objTable->addCell($orderNumber,'1%','top','center',$class,'');
                            $objTable->addCell($required,'1%','top','center',$class,'');
                            $objTable->addCell($questionDescription,'25%','top','',$class,'');
                            $objTable->addCell($questionText,'','','',$class,'');
                            $objTable->addCell($icons,'16%','','right',$class,'');
                            $objTable->endRow();
                        }
                        $str=$objTable->show();

                        // set up assign page dropdown
                        $objDrop=new dropdown('newPageId');
                        foreach($arrPageList as $page){
                            if($page['id']!=$pageId){
                                $objDrop->addOption($page['id'],$page['page_label']);
                            }
                        }
                        $assignDrop=$objDrop->show();

                        $formName=$pageId;
                        // set up submit button
                        $objButton=new button('submitButton',$submitLabel);
                        $objButton->extra=' onclick="javascript:
                            this.disabled=\'disabled\';
                            document.getElementById(\'form_'.$formName.'\').submit();
                        "';
                        $submitButton=$objButton->show();

                        // set up row with assign questions to page dropdown
                        $objTable=new htmltable();
                        $objTable->cellspacing='2';
                        $objTable->cellpadding='2';

                        $objTable->startHeaderRow();
                        $objTable->addHeaderCell($assignDrop.'&nbsp;'.$submitButton,'','','left','heading','');
                        $objTable->endHeaderRow();

                        $str.=$objTable->show();

                        // set up form
                        $objForm=new form($formName,$this->uri(array('action'=>'assignquestions','survey_id'=>$surveyId,'mode'=>'reassign')));
                        $objForm->addToForm($str);
                        $str=$objForm->show();
                    }
                }
            }

            $objTabbedbox=new tabbedbox();
            $objTabbedbox->extra=' style="padding: 10px;"';
            $objTabbedbox->addTabLabel($pageLabel);
            $objTabbedbox->addBoxContent($str);
            echo $objTabbedbox->show();
        }
    }

// set up rerurn link
    $objLink=new link($this->uri(array(),'survey'));
    $objLink->link=$returnSurveyLabel;
    $returnLink=$objLink->show();
    echo $returnLink;
?>