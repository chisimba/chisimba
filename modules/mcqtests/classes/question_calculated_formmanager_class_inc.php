<?php

class question_calculated_formmanager extends object {

    private $id;

    function init() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    public function newGeneralForm($data=null, $type=null) {
        $nameLabel = $this->objLanguage->languageText('mod_mcqtests_questionname', 'mcqtests');
        $qtextLabel = $this->objLanguage->languageText('mod_mcqtests_questiontext', 'mcqtests');
        $qmarkLabel = $this->objLanguage->languageText('mod_mcqtest_marklabel', 'mcqtests');
        $qpenaltyLabel = $this->objLanguage->languageText('mod_mcqtest_penaltylabel', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');

        if (!empty($data)) {
            $name = $data['name'];
            $qtext = $data['question'];
            $qMark = $data['mark'];
            $qPenalty = $data['penalty'];
            $genfeedback = $data['generalfeedback'];
            $questionid = $data['id'];
        } else {
            $name = "";
            $qtext = "";
            $qMark = "";
            $qPenalty = "";
            $genfeedback = "";
        }

        /* if(strlen($type) > 0) {
          $wordGeneral = $type;
          } */

        $table = new htmltable('general');
        $table->width = '800px';
        $table->attributes = " align='center' border='0'";
        $table->cellspacing = '12';

        // category text box
        $category = new textinput("categoryid", "");
        $category->size = 60;
        $table->startRow();
        $table->addCell($wordCategory, '30%');
        $table->addCell($wordGeneral, '70%');
        $table->endRow();

        // question name
        $table->startRow();
        $objName = new textinput('qName', $name);
        $objName->size = 40;
        $table->addCell('<b>' . $nameLabel . '</b>', '30%');
        $table->addCell($objName->show(), '70%');
        $table->endRow();

        // question text
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'qText';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $editor->setContent($qtext);

        $table->startRow();
        $table->addCell('<b>' . $qtextLabel . '</b>:', '30%');
        $table->addCell($editor->show(), '70%');
        $table->endRow();

        // default mark
        $table->startRow();
        $objQMark = new textinput('qMark', $qMark);
        $objQMark->size = 3;
        $table->addCell('<b>' . $qmarkLabel . '</b>:', '30%');
        $table->addCell($objQMark->show(), '70%');
        $table->endRow();

        // penalty factor
        $table->startRow();
        $objQPenalty = new textinput('qPenalty', $qPenalty);
        $objQPenalty->size = 3;
        $table->addCell('<b>' . $qpenaltyLabel . '</b>:', '30%');
        $table->addCell($objQPenalty->show(), '70%');
        $table->endRow();

        //general feedback
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'calcqgenfeedback';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $editor->setContent($genfeedback);
        //Add General Feedback to the table
        $table->startRow();
        $table->addCell('<b>' . $wordFeedback . '</b>:', '30%');
        $table->addCell($editor->show(), '70%');
        $table->endRow();

        $objFieldset = $this->newObject('fieldset', 'htmlelements');

        $objFieldset->width = '800px';
        //$objFieldset->align = 'center border="0"';
        $objFieldset->setExtra("id='generalCalcQField'");
        $objFieldset->setLegend($wordGeneral);

        //Add table to General Fieldset
        $objFieldset->addContent($table->show());

        return $objFieldset->show();
    }

    public function calcQAnswerForm() {
        $wordAnswer = $this->objLanguage->languageText('mod_mcqtests_wordanswer', 'mcqtests');
        $correctAnswerLabel = $this->objLanguage->languageText('mod_mcqtest_corranswerlabel', 'mcqtests');
        $answerMarkLabel = $this->objLanguage->languageText('mod_mcqtest_answermarklabel', 'mcqtests');
        $toleranceLabel = $this->objLanguage->languageText('mod_mcqtest_tolerancelabel', 'mcqtests');
        $formatLabel = $this->objLanguage->languageText('mod_mcqtest_formatlabel', 'mcqtests');

        $correctAnswer = "";
        $answerMark = "";
        $answerTolerance = "";

        // Create table to hold the answer information
        $objAnswerTable = new htmltable('answers');
        $objAnswerTable->width = '800px';
        $objAnswerTable->attributes = " align='center' border='0'";
        $objAnswerTable->cellspacing = '12';

        // answer
        $objAnswerTable->startRow();
        $objCorrectAnswer = new textinput('qCorrectAnswer', $correctAnswer);
        $objCorrectAnswer->size = 40;
        $objAnswerTable->addCell('<b>' . $correctAnswerLabel . '</b>:', '30%');
        $objAnswerTable->addCell($objCorrectAnswer->show(), '70%');
        $objAnswerTable->endRow();

        // mark
        $objAnswerTable->startRow();
        $objAnswerMark = new textinput('qAnswerMark', $answerMark);
        $objAnswerMark->size = 3;
        $objAnswerTable->addCell('<b>' . $answerMarkLabel . '</b>:', '30%');
        $objAnswerTable->addCell($objAnswerMark->show(), '70%');
        $objAnswerTable->endRow();

        // tolerance
        $objAnswerTable->startRow();
        $objAnswerTolerance = new textinput('qAnswerTolerance', $answerTolerance);
        $objAnswerTolerance->size = 3;
        $objAnswerTable->addCell('<b>' . $toleranceLabel . '</b>:', '30%');
        $objAnswerTable->addCell($objAnswerTolerance->show(), '70%');
        $objAnswerTable->endRow();

        // format
        $objAnswerTable->startRow();
        $objAnswerFormat = new dropdown('qAnswerFormat');
        $objAnswerFormat->addOption(1, $this->objLanguage->languageText('mod_mcqtests_decimals', 'mcqtests'));
        $objAnswerFormat->addOption(2, $this->objLanguage->languageText('mod_mcqtests_sigfigs', 'mcqtests'));
        //$objAnswerFormat->size=40;
        $objAnswerTable->addCell('<b>' . $formatLabel . '</b>');
        $objAnswerTable->addCell($objAnswerFormat->show());
        $objAnswerTable->endRow();

        $objAnswerFieldset = $this->newObject('fieldset', 'htmlelements');

        $objAnswerFieldset->width = '800px';
        $objAnswerFieldset->align = 'center';
        $objAnswerFieldset->setExtra("id='answerCalcQField'");
        $objAnswerFieldset->setLegend($wordAnswer);

        $objAnswerFieldset->addContent($objAnswerTable->show());

        return $objAnswerFieldset->show();
    }

    public function saveButton() {
        // submit & cancel buttons
        $button = new button("submit", $this->objLanguage->languageText("word_save"));
        $button->setToSubmit();
        $btnSave = $button->showSexy();

        return $btnSave;
    }

    public function cancelButton($id) {
        // Create Cancel Button
        $buttonCancel = new button("submit", $this->objLanguage->languageText("word_cancel"));
        $objCancel = &$this->getObject("link", "htmlelements");
        $objCancel->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2',
                    'id' => $id
                )));
        $objCancel->link = $buttonCancel->showSexy();
        $btnCancel = $objCancel->show();

        return $btnCancel;
    }

    public function calcQForm($testid) {
        $this->id = $testid;
        $objGeneralFieldset = $this->newGeneralForm();
        //$objAnswerFieldset = $this->calcQAnswerForm();

        $btnSave = $this->saveButton();
        $btnCancel = $this->cancelButton($testid);

        // Create form and add the table
        $objFormEdit = new form('calculatedq', str_replace("amp;", "", $this->uri(array('action' => 'addcalculatedquestion'))));
        $objFormEdit->addToForm($objGeneralFieldset);
        $objFormEdit->addToForm($objAnswerFieldset);
        $objFormEdit->addToForm("<div align='center'><br />" . $btnSave . " " . $btnCancel . "<br /></div>");
        
        return $objFormEdit->show();
    }

    public function getMatchingNote() {
        $matchingNote = $this->objLanguage->languageText('mod_mcqtests_matchingnote', 'mcqtests');

        return '<div><b>' . $matchingNote . '</b></div>';
    }

    public function matchingQForm($testid, $data=null, $edit=null, $questionId=null) {
        $qNameMsg = $this->objLanguage->languageText('mod_mcqtests_reqname', 'mcqtests');
        $qTextMsg = $this->objLanguage->languageText('mod_mcqtests_reqtext', 'mcqtests');
        $qMarkMsg = $this->objLanguage->languageText('mod_mcqtests_reqmark', 'mcqtests');
        $qPenaltyMsg = $this->objLanguage->languageText('mod_mcqtests_reqpenalty', 'mcqtests');

        $this->id = $testid;
        $qtype = "Matching";
        $objGeneralFieldset = $this->newGeneralForm($data, $qtype);
        $matchingNote = $this->getMatchingNote();
        $objAnswerFieldset = $this->matchingAForms($data['id'], $edit);

        $btnSave = $this->saveButton();
        $btnCancel = $this->cancelButton($testid);

        // Create form and add the table
        if($edit) {
            $objForm = new form('matchingq', str_replace("amp;", "", $this->uri(array('action' => 'addmatchingquestion', 'id' => $testid, 'questionId'=>$questionId, 'edit'=>'true'))));
        }
        else {
            $objForm = new form('matchingq', str_replace("amp;", "", $this->uri(array('action' => 'addmatchingquestion', 'id' => $testid))));
        }
        $objForm->addToForm($objGeneralFieldset);
        $objForm->addToForm($matchingNote);
        $objForm->addToForm($objAnswerFieldset);
        $objForm->addToForm("<br />" . $btnSave . " " . $btnCancel . "<br />");
        $objForm->addRule('qName', $qNameMsg, 'required');
        //$objForm->addRule('qText', $qTextMsg, 'required');
        $objForm->addRule('qMark', $qMarkMsg, 'required');
        $objForm->addRule('qPenalty', $qPenaltyMsg, 'required');

        return $objForm->show();
    }

    public function matchingAForms($questionid, $edit) {
        $retStr = "";
        $objMatchingQuestions = $this->newObject("dbquestion_matching");
        $objMultiAnswers = $this->newObject('dbquestion_multianswers');

        $questionLegend = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
        $answerLabel = $this->objLanguage->languageText('mod_mcqtests_answer', 'mcqtests');

        $count = 0;

        if ($edit) {
            // get the data for the answers
            $questions = $objMatchingQuestions->getMatchingQuestions($questionid);
            $answers = $objMultiAnswers->getAnswers($questionid);
        }

        $answer = "";
        $genfeedback = '';
        for ($i = 1; $i <= 3; $i++) {
            if($edit) {
                $genfeedback = $questions[$i-1]['subquestions'];
                $answer = $answers[$i-1]['answer'];
            }

            $fieldExtra = "id='answerMatchingField_" . $i . "'";

            $objAnswerTable = new htmltable();
            $objAnswerTable->width = '800px';
            $objAnswerTable->attributes = " align='center' border='0'";
            $objAnswerTable->cellspacing = '12';


            // question
            $editor = $this->newObject('htmlarea', 'htmlelements');
            $editor->name = 'qmatching' . $i;
            $editor->height = '100px';
            $editor->width = '550px';
            $editor->setMCQToolBar();
            $editor->setContent($genfeedback);
            $objAnswerTable->startRow();
            $objAnswerTable->addCell('<b>' . $questionLegend . '</b>:', '30%');
            $objAnswerTable->addCell($editor->show(), '70%');
            $objAnswerTable->endRow();

            // answer
            $objAnswerTable->startRow();
            $objQMatching = new textinput('aMatching' . $i, $answer);
            $objQMatching->size = 10;
            $objAnswerTable->addCell('<b>' . $answerLabel . '</b>:', '30%');
            $objAnswerTable->addCell($objQMatching->show(), '70%');
            $objAnswerTable->endRow();

            $objQuestionFieldset = $this->newObject('fieldset', 'htmlelements');
            $objQuestionFieldset->width = '800px';
            //$objQuestionFieldset->align = 'center';
            $objQuestionFieldset->setExtra($fieldExtra);
            $objQuestionFieldset->setLegend($questionLegend . $i);
            $objQuestionFieldset->addContent($objAnswerTable->show());

            $retStr .= $objQuestionFieldset->show();
        }


        return $retStr;
    }

}

?>