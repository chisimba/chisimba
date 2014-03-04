<?php

class numerical_question extends object {

    /**
     * Method to construct the class.
     *
     * @access public
     * @return
     */
    function init() {
        $this->objGeneralForm = $this->getObject("question_calculated_formmanager");
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
    }

    /**
     * Method to create a numerical question form
     *
     * @access public
     * @param  array $testid contains test id
     * @param  string $data contains test data
     * @param  string $edit
     * @param  string $questionId
     * @author Nguni Phakela
     */
    public function numericalQForm($testid=null, $data=null, $edit=false, $questionId=null) {
        $qNameMsg = $this->objLanguage->languageText('mod_mcqtests_reqname', 'mcqtests');
        $qTextMsg = $this->objLanguage->languageText('mod_mcqtests_reqtext', 'mcqtests');
        $qMarkMsg = $this->objLanguage->languageText('mod_mcqtests_reqmark', 'mcqtests');
        $qPenaltyMsg = $this->objLanguage->languageText('mod_mcqtests_reqpenalty', 'mcqtests');

        $generalForm = $this->objGeneralForm->newGeneralForm($data);
        
        $objAnswerFieldset = $this->getAnswers($data['id']);

        $btnSave = $this->objGeneralForm->saveButton();
        $btnCancel = $this->objGeneralForm->cancelButton($testid);

        $unitsHandling = $this->getUnitHandling($data['id']);

        if(!empty($data)) {
            $units = $this->getUnit($data['id']);
        }
        else {
            $units = $this->getUnit();
        }

        // Create form and add the table
        if($edit) {
            $objForm = new form('numericalq', str_replace("amp;", "", $this->uri(array('action' => 'addnumericalquestion', 'id' => $testid, 'questionId'=>$questionId, 'edit'=>'true'))));
        }else {
            $objForm = new form('numericalq', str_replace("amp;", "", $this->uri(array('action' => 'addnumericalquestion', 'id' => $testid))));
        }
        $objForm->addToForm($generalForm);
        $objForm->addToForm($objAnswerFieldset);
        $objForm->addToForm($unitsHandling);
        $objForm->addToForm($units);
        $objForm->addToForm("<br /><br />" . $btnSave . " " . $btnCancel . "<br /><br />");
        $objForm->addRule('qName', $qNameMsg, 'required');
        //$objForm->addRule('qText', $qTextMsg, 'required');
        $objForm->addRule('qMark', $qMarkMsg, 'required');
        $objForm->addRule('qPenalty', $qPenaltyMsg, 'required');

        return $objForm->show();
    }

    public function getAnswers($questionid=null) {
        $retStr = "";
        //$questionLegend = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
        $answerLabel = $this->objLanguage->languageText('mod_mcqtests_answer', 'mcqtests');
        $qmarkLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');

        $count = 0;
        $ansfeedback = '';
        
        //get the answers
        $objQuestionNumerical = $this->newObject('dbquestion_numerical');
        $answers = $objQuestionNumerical->getAnswers($questionid);

        for ($i = 1; $i <= 3; $i++) {
            if(strlen(trim($answers[$i-1]['answer'])) > 0) {
                $answer = $answers[$i-1]['answer'];
                $mark = $answers[$i-1]['mark'];
            }
            else {
                $answer = "";
                $mark = "";
            }
            
            $fieldExtra = "id='answerNumericalField_" . $i . "'";

            $objAnswerTable = new htmltable();
            $objAnswerTable->width = '800px';
            $objAnswerTable->attributes = " align='center' border='0'";
            $objAnswerTable->cellspacing = '12';


            // answer
            $objAnswerTable->startRow();
            $objQNumerical = new textinput('aNumerical' . $i, $answer);
            $objQNumerical->size = 10;
            $objAnswerTable->addCell('<b>' . $answerLabel . '</b>:', '30%');
            $objAnswerTable->addCell($objQNumerical->show(), '70%');
            $objAnswerTable->endRow();

            // default mark
            $objAnswerTable->startRow();
            $objQMark = new textinput('mark_'.$i, $mark);
            $objQMark->size = 3;
            $objAnswerTable->addCell('<b>' . $qmarkLabel . '</b>:', '30%');
            $objAnswerTable->addCell($objQMark->show(), '70%');
            $objAnswerTable->endRow();

            //general feedback
            $editor = $this->newObject('htmlarea', 'htmlelements');
            $editor->name = 'ansfeedback_' . $i;
            $editor->height = '100px';
            $editor->width = '550px';
            $editor->setMCQToolBar();
            $ansfeedback = '';
            $editor->setContent($ansfeedback);
            //Add Feedback to the table
            $objAnswerTable->startRow();
            $objAnswerTable->addCell('<b>' . $wordFeedback . '</b>:', '30%');
            $objAnswerTable->addCell($editor->show(), '70%');
            $objAnswerTable->endRow();

            //$objQuestionFieldset->reset();
            $objQuestionFieldset = $this->newObject('fieldset', 'htmlelements');
            $objQuestionFieldset->width = '800px';
            //$objQuestionFieldset->align = 'center';
            $objQuestionFieldset->setExtra($fieldExtra);
            $objQuestionFieldset->setLegend($answerLabel . $i);
            $objQuestionFieldset->addContent($objAnswerTable->show());

            $retStr .= $objQuestionFieldset->show();
        }

        return $retStr;
    }

    public function getUnitHandling($questionid=null) {
        $unitsHandling = $this->objLanguage->languageText('mod_mcqtests_unitshandling', 'mcqtests');
        $unitMarkLabel = $this->objLanguage->languageText('mod_mcqtests_unitmark', 'mcqtests');
        $badUnitLabel = $this->objLanguage->languageText('mod_mcqtests_penaltybadunit', 'mcqtests');
        $instructions = $this->objLanguage->languageText('mod_mcqtests_instructions', 'mcqtests');
        $numericalAnswerLabel = $this->objLanguage->languageText('mod_mcqtests_numericalanswer', 'mcqtests');
        $unitNotMarkedLabel = $this->objLanguage->languageText('mod_mcqtests_unitnotmarked', 'mcqtests');
        $onlyNumericalAnswerLabel = $this->objLanguage->languageText('mod_mcqtests_onlynumericalanswer', 'mcqtests');
        $dispUnit = $this->objLanguage->languageText('mod_mcqtests_displayunit', 'mcqtests');
        $lbYes = $this->objLanguage->languageText('word_yes');
        $lbNo = $this->objLanguage->languageText('word_no');

        $objUnitsHandling = $this->newObject('dbnumericalunitsoptions');
        $numericalOptionsData = $objUnitsHandling->getNumericalOptions($questionid);
        
        if(!empty($numericalOptionsData)) {
            $penaltyUnit = $numericalOptionsData['unitpenalty'];
            $instructionsdata = $numericalOptionsData['instructions'];
            $showunit = $numericalOptionsData['showunits'];
            $numunitmarked = $numericalOptionsData['unitgradingtype'];
        }
        else {
            $retStr = "";
            $penaltyUnit = "";
            $fieldExtra = "id='answerNumericalField_" . $i . "'";
            $instructionsdata = "";
            $showunit = "";
            $numunitmarked = "";
        }

        $objAnswerTable = new htmltable();
        $objAnswerTable->width = '800px';
        $objAnswerTable->attributes = " align='center' border='0'";
        $objAnswerTable->cellspacing = '12';


        $objRadio = new radio('unitmarked');
        $objRadio->setBreakSpace('&nbsp;&nbsp;');
        $objRadio->addOption('numunitmarked', $numericalAnswerLabel);
        if(!empty($numunitmarked) && $numunitmarked == 'yes' || empty($numunitmarked)) {
            $objRadio->setSelected('numunitmarked');
        }
        $objAnswerTable->startRow();
        $objAnswerTable->addCell('<b>' . $unitMarkLabel . '</b>:', '30%');
        $objAnswerTable->addCell($objRadio->show(), '70%');
        $objAnswerTable->endRow();

        // penalty
        $objAnswerTable->startRow();
        $objPenaltyUnit = new textinput('penaltyUnit', $penaltyUnit);
        $objPenaltyUnit->size = 3;
        $objAnswerTable->addCell('<b>' . $badUnitLabel . '</b>:', '30%');
        $objAnswerTable->addCell($objPenaltyUnit->show(), '70%');
        $objAnswerTable->endRow();

        // Instructions
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'instructions_' . $i;
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $editor->setContent($instructionsdata);
        //Add Instructions to the table
        $objAnswerTable->startRow();
        $objAnswerTable->addCell('<b>' . $instructions . '</b>:', '30%');
        $objAnswerTable->addCell($editor->show(), '70%');
        $objAnswerTable->endRow();

        $objRadio = new radio('onlyunitmarked');
        $objRadio->setBreakSpace('&nbsp;&nbsp;');
        $objRadio->addOption('onlynumunitmarked', $onlyNumericalAnswerLabel);
        $objAnswerTable->startRow("showBorder");
        $objAnswerTable->addCell($unitNotMarkedLabel, '30%');
        $objAnswerTable->addCell($objRadio->show(), '70%');
        $objAnswerTable->endRow();

        $objRadio = new radio('dispUnit');
        $objRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;&nbsp;');
        $objRadio->addOption('yes', $lbYes);
        $objRadio->addOption('no', $lbNo);
        if(!empty($showunit)) {
            $objRadio->setSelected($showunit);
        }
        else {
            $objRadio->setSelected('no');
        }
        $objAnswerTable->startRow();
        $objAnswerTable->addCell($dispUnit, '30%', $valign = "top", $align = null, $class = null, $attrib = Null, $border = '1');
        $objAnswerTable->addCell($objRadio->show(), '70%');
        $objAnswerTable->endRow();

        $objQuestionFieldset = $this->newObject('fieldset', 'htmlelements');
        $objQuestionFieldset->width = '800px';
        //$objQuestionFieldset->align = 'center';
        $objQuestionFieldset->setExtra($fieldExtra);
        $objQuestionFieldset->setLegend($unitsHandling);
        $objQuestionFieldset->addContent($objAnswerTable->show());

        $retStr .= $objQuestionFieldset->show();

        return $retStr;
    }

    public function getUnit($questionid=null) {
        $unitLabel = $this->objLanguage->languageText('mod_mcqtests_unit', 'mcqtests');
        $objQuestionUnit = $this->newObject('dbnumericalunits');

        $retStr = "";
        
        $fieldExtra = "id='answerNumericalField_1'";

        $data = $objQuestionUnit->getNumericalUnits($questionid);
        if(!empty($data)) {
            $unit = $data['unit'];
        }
        else {
            $unit = "";
        }

        $objUnitTable = new htmltable();
        $objUnitTable->width = '800px';
        $objUnitTable->attributes = " align='center' border='0'";
        $objUnitTable->cellspacing = '12';

        // answer
        $objUnitTable->startRow();
        $objUnitNumerical=new textinput('aUnit'.$i,$unit);
        $objUnitNumerical->size=3;
        $objUnitTable->addCell('<b>'.$unitLabel.'</b>:', '30%');

        $objUnitTable->addCell($objUnitNumerical->show(), '70%');
        $objUnitTable->endRow();

        $objUnitFieldset = $this->newObject('fieldset', 'htmlelements');
        $objUnitFieldset->width = '800px';
        //$objUnitFieldset->align = 'center';
        $objUnitFieldset->setExtra($fieldExtra);

        $objUnitFieldset->setLegend($unitLabel.' 1');
        $objUnitFieldset->addContent($objUnitTable->show());

        $retStr .= $objUnitFieldset->show();
        
        return $retStr;
    }

}

?>
