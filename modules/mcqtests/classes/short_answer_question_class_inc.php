<?php
class short_answer_question extends object {
    function init() {
        $this->objGeneralForm = $this->getObject("question_calculated_formmanager");
        $this->objLanguage=$this->getObject('language','language');
        $this->loadClass('radio','htmlelements');
        $this->loadClass('htmltable','htmlelements');
    }

    public function shortAnsQForm($testid) {
        $generalForm = $this->objGeneralForm->newGeneralForm();

        $objAnswerFieldset = $this->getAnswers();
        $ansMessage = $this->getCorrectAnsMessage();
        $tags = $this->getTags();
        $btnSave = $this->objGeneralForm->saveButton();
        $btnCancel = $this->objGeneralForm->cancelButton($testid);

        
        // Create form and add the table
        $objForm = new form('shortanswerq', str_replace("amp;", "", $this->uri(array('action'=>'addshortanswerquestion'))));
        $objForm->addToForm($generalForm);
        $objForm->addToForm($ansMessage);
        $objForm->addToForm($objAnswerFieldset);
        $objForm->addToForm($tags);
        $objForm->addToForm("<br />" . $btnSave . " " . $btnCancel . "<br />");

        return $objForm->show();
    }

    public function getCorrectAnsMessage() {
        $ansLabel = $this->objLanguage->languageText('mod_mcqtests_correctans', 'mcqtests');
        $ansMessage = $this->objLanguage->languageText('mod_mcqtests_correctanswermessage', 'mcqtests');

        $objAnswerTable = new htmltable();
        $objAnswerTable->width = '800px';
        $objAnswerTable->attributes = " align='center' border='0'";
        $objAnswerTable->cellspacing = '12';

        // answer message
        $objAnswerTable->startRow();
        $objQNumerical->size=60;
        $objAnswerTable->addCell($ansLabel, '30%');
        $objAnswerTable->addCell($ansMessage, '70%');
        $objAnswerTable->endRow();

        return $objAnswerTable->show();
    }

    public function getAnswers() {
        $retStr = "";
        //$questionLegend = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
        $answerLabel = $this->objLanguage->languageText('mod_mcqtests_answer', 'mcqtests');
        $qmarkLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');

        $count = 0;

        $answer = "";
        $ansfeedback = '';
        $qMark = 0;


        for($i=1;$i<=3;$i++) {
            $fieldExtra = "id='shortAnswerField_".$i."'";

            $objAnswerTable = new htmltable();
            $objAnswerTable->width = '800px';
            $objAnswerTable->attributes = " align='center' border='0'";
            $objAnswerTable->cellspacing = '12';


            // answer
            $objAnswerTable->startRow();
            $objQNumerical=new textinput('ashortAnswer'.$i,$answer);
            $objQNumerical->size=60;
            $objAnswerTable->addCell('<b>'.$answerLabel.'</b>:', '30%');
            $objAnswerTable->addCell($objQNumerical->show(), '70%');
            $objAnswerTable->endRow();

            // default mark
            $objAnswerTable->startRow();
            $objQMark= new textinput('qMark',$qMark);
            $objQMark->size=3;
            $objAnswerTable->addCell('<b>'.$qmarkLabel.'</b>:', '30%');
            $objAnswerTable->addCell($objQMark->show(), '70%');
            $objAnswerTable->endRow();

            //general feedback
            $editor = $this->newObject('htmlarea', 'htmlelements');
            $editor->name = 'shortansfeedback_'.$i;
            $editor->height = '100px';
            $editor->width = '550px';
            $editor->setMCQToolBar();
            $ansfeedback = '';
            $editor->setContent($ansfeedback);
            //Add Feedback to the table
            $objAnswerTable->startRow();
            $objAnswerTable->addCell('<b>'.$wordFeedback.'</b>:', '30%');
            $objAnswerTable->addCell($editor->show(), '70%');
            $objAnswerTable->endRow();


            $objQuestionFieldset = $this->newObject('fieldset', 'htmlelements');
            $objQuestionFieldset->width = '800px';
            $objQuestionFieldset->align = 'center';
            $objQuestionFieldset->setExtra($fieldExtra);
            $objQuestionFieldset->setLegend($answerLabel." ".$i);
            $objQuestionFieldset->addContent($objAnswerTable->show());

            $retStr .= $objQuestionFieldset->show();
        }


        return $retStr;
    }

    public function getTags() {
        $tagLabel = $this->objLanguage->languageText('mod_mcqtests_wordtags', 'mcqtests');
        
        $retStr = "";
        $fielExtra = "id='tags";
        $othertags = "";

        $objAnswerTable = new htmltable();
        $objAnswerTable->width = '800px';
        $objAnswerTable->attributes = " align='center' border='0'";
        $objAnswerTable->cellspacing = '12';

        //general feedback
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'othertagseditor';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $editor->setContent($othertags);
        // tags
        $objAnswerTable->startRow();
        $objTag= new textarea('othertags',$othertags);
        $objAnswerTable->addCell('<b>'.$tagLabel.'<b/>', '30%');
        $objAnswerTable->addCell($editor->show(), '70%');
        $objAnswerTable->endRow();

        $TagFieldset = $this->newObject('fieldset', 'htmlelements');
        $TagFieldset->width = '800px';
        $TagFieldset->align = 'center';
        $TagFieldset->setExtra($fieldExtra);
        $TagFieldset->setLegend($tagLabel);
        $TagFieldset->addContent($objAnswerTable->show());

        $retStr .= $TagFieldset->show();

        return $retStr;
    }
}

?>
