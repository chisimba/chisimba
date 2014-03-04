<?php

class formmanager extends object {
    /*
     * @var object to hold tbl description class
     */

    public $dbDescription;
    /*
     * @var object to hold language class
     */
    public $objLanguage;
    /*
     * @var object to hold context db class
     */
    public $objContext;
    /*
     * @var object to hold the context Code
     */
    public $contextCode;
    /*
     * @var object to hold questions db class
     */
    public $dbQuestions;
    /*
     * @var object to hold random_matching db class
     */
    public $dbRandomMatching;
    /*
     * @var object to hold Category db class
     */
    public $dbCategory;
    /*
     * @var object to hold Tag db class
     */
    public $dbTag;
    /*
     * @var object to hold tag_instance db class
     */
    public $dbTagInstance;
    /**
     *
     * @var object to hold dbdataset_definitions class
     */
    public $objDSDefinitions;
    /**
     *
     * @var object to hold dbdataset_items class
     */
    public $objDSItems;
    /**
     *
     * @var object to hold dbdataset class
     */
    public $objDBDataset;
    /**
     *
     * @var object to hold dbTestadmin class
     */
    public $dbTestadmin;

    function init() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->dbTestadmin = $this->getObject('dbtestadmin');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->dbDescription = $this->newObject('dbdescription');
        $this->dbQuestions = $this->newObject('dbquestions');
        $this->dbRandomMatching = $this->newObject('dbrandom_matching');
        $this->dbTag = $this->newObject('dbtag');
        $this->dbTagInstance = $this->newObject('dbtag_instance');
        $this->dbCategory = $this->newObject('dbcategory');
        $this->objContext = $this->newObject('dbcontext', 'context');
        $this->objQnAnswers = $this->newObject('dbquestion_answers');
        $this->objQuestionCalculated = $this->newObject('dbquestion_calculated');
        $this->objNumericalOptions = $this->newObject('dbnumericalunitsoptions');
        $this->objNumericalUnit = $this->newObject('dbnumericalunits');
        $this->contextCode = $this->objContext->getContextCode();
        $this->objDSDefinitions = $this->newObject("dbdataset_definitions");
        $this->objDSItems = $this->newObject("dbdataset_items");
        $this->objDBDataset = $this->newObject("dbdatasets");
    }

    public function createAddFreeForm($test) {
        $addFreeform = $this->objLanguage->languageText('mod_mcqtests_addfreeformlabel', 'mcqtests');
        $editFreeform = $this->objLanguage->languageText('mod_mcqtests_editfreeformlabel', 'mcqtests');
        $addquestion = $this->objLanguage->languageText('mod_mcqtests_addaquestion', 'mcqtests');
        $testLabel = $this->objLanguage->languageText('mod_mcqtests_test', 'mcqtests');
        $totalLabel = $this->objLanguage->languageText('mod_mcqtests_totalmarks', 'mcqtests');

        $freeForm = $this->objLanguage->languageText('mod_mcqtests_freeform', 'mcqtests');
        $markLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
        $saveLabel = $this->objLanguage->languageText('word_save');
        $exitLabel = $this->objLanguage->languageText('word_cancel');
        $hintLabel = $this->objLanguage->languageText('mod_mcqtests_hint', 'mcqtests');
        $addhintLabel = $this->objLanguage->languageText('mod_mcqtests_hintenable', 'mcqtests');
        $lbEnable = $this->objLanguage->languageText('mod_mcqtests_hintaddenable', 'mcqtests');
        $lbDisable = $this->objLanguage->languageText('mod_mcqtests_hintadddisable', 'mcqtests');
        //Reference the heading to the layout template
        // Display information on the test to be set
        $headStr = '<h1>Free form question<h1/><hr>';
        $headStr.= '<b>' . 'Test' . ':</b>&nbsp;&nbsp;' . $test['name'] . '<br />';
        $headStr.= '<b>' . 'Total Marks' . ':</b>&nbsp;&nbsp;' . $test['totalmark'] . '<br />&nbsp;';

        //Build the forms for adding cloze questions

        if (!empty($data)) {
            $question = $data['question'];
            $mark = $data['mark'];
            $hint = $data['hint'];
            $num = $data['questionorder'];
            $questType = $data['questiontype'];
            $questId = $data['id'];
        } else {
            $question = '';
            $mark = 0;
            $hint = '';
            $num = $test['count'] + 1;
            $questionType = '';
            $questId = '';
        }


        //create a hidden field tostore  question type
        $objInput = new textinput('type', $freeForm);
        $objInput->fldType = 'hidden';
        $headStr.= $objInput->show();


        //Heading for Question and number
        $objHead = new htmlheading();
        $objHead->str = 'Question' . ' ' . $num . ':';
        $objHead->type = 3;
        $headStr.= $objHead->show();


        //Create an instance of the htmlarea class
        $objEditor = $this->newObject('htmlarea', 'htmlelements');
        //initialise the fckeditor
        $objEditor->init('freeformquestion', $question, '300px', '500px');
        $objEditor->setDefaultToolBarSetWithoutSave();
        //$simplebox = $num ;
        //$textfield = new textinput ('simplebox',$stdanswer,'hidden');

        $headStr.=$objEditor->show() . '<br /><br />';

        //create mark textfield
        $objMark = new textinput('mark', $mark);
        $objMark->size = 10;
        $headStr.= '<b>' . $markLabel . '</b>:&nbsp;&nbsp;&nbsp;&nbsp;';
        $headStr.= $objMark->show();


        //create hint field
        $headStr.= '<p><b>' . $hintLabel . ':</b></p><p>' . $addhintLabel . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>';
        $objRadio = new radio('enablehint');
        $objRadio->setBreakSpace('&nbsp;&nbsp;/&nbsp;&nbsp;');
        $objRadio->addOption('yes', $lbEnable);
        $objRadio->addOption('no', $lbDisable);
        $objRadio->setSelected('no');
        if (!empty($hint)) {
            $objRadio->setSelected('yes');
        }
        $headStr.= '<p>' . $objRadio->show() . '</p>';
        $objInput = new textinput('hint', $hint);
        $objInput->size = 83;
        $headStr.= $objInput->show() . '<p>&nbsp;</p>';



        //Create Submit of the form
        $objButton = new button('save', $saveLabel);
        $objButton->setToSubmit();
        $btn = $objButton->show() . '&nbsp;&nbsp;&nbsp;&nbsp;';
        $objButton = new button('save', $exitLabel);
        $objButton->setToSubmit();
        $btn.=$objButton->show();

        //create a hidden field to store test id

        $objTextHid = new textinput('testId', $test['id'], 'hidden');
        $objTableButtons = new htmltable();
        $objTableButtons->startRow();
        $objTableButtons->addCell($objTextHid->show());
        $objTableButtons->addCell($btn, '', '', '', '', 'colspan="2"');
        $objTableButtons->endRow();

        //create a hidden fields to store question order and question id
        $objInput = new textinput('qOrder', $num);
        $objInput->fldType = 'hidden';
        $headStr.= $objInput->show();
        $objInput = new textinput('questionId', $questId);
        $objInput->fldType = 'hidden';
        $headStr.= $objInput->show();

        //Create form
        $objForm = new form('addfreeformquestion', $this->uri(array('action' => 'applyaddquestion', 'formtype' => 'freeform')));
        $objForm->addToForm($headStr);
        $objForm->addToForm($objTableButtons->show());
        return $objForm->show();
    }

    public function createAddQuestionForm($test, $mode = 'add') {

        // set up html elements
        $objHead = $this->loadClass('htmlheading', 'htmlelements');
        $objTable = $this->loadClass('htmltable', 'htmlelements');
        $objTableButtons = $this->loadClass('htmltable', 'htmlelements');
        $objForm = $this->loadClass('form', 'htmlelements');
        $objRadio = $this->loadClass('radio', 'htmlelements');
        $objCheck = $this->loadClass('checkbox', 'htmlelements');
        $objInput = $this->loadClass('textinput', 'htmlelements');
        $objText = $this->loadClass('textarea', 'htmlelements');
        $objButton = $this->loadClass('button', 'htmlelements');
        $objLink = $this->loadClass('link', 'htmlelements');
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objImage = $this->loadClass('image', 'htmlelements');
        $objMsg = $this->newObject('timeoutmessage', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->objStepMenu = $this->newObject('stepmenu', 'navigation');
        $this->loadClass('dropdown', 'htmlelements');


        // set up language items
        $addHead = $this->objLanguage->languageText('mod_mcqtests_addaquestion', 'mcqtests');
        $editHead = $this->objLanguage->languageText('mod_mcqtests_editquestion', 'mcqtests');
        $testLabel = $this->objLanguage->languageText('mod_mcqtests_test', 'mcqtests');
        $totalLabel = $this->objLanguage->languageText('mod_mcqtests_totalmarks', 'mcqtests');
        $addqestionslabel = $this->objLanguage->languageText('mod_mcqtests_addquestions', 'mcqtests');
        $questionLabel = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
        $answersLabel = $this->objLanguage->languageText('mod_mcqtests_answers', 'mcqtests');
        $addanswersLabel = $this->objLanguage->languageText('mod_mcqtests_addanswers', 'mcqtests');
        //$actionsLabel = $this->objLanguage->languageText('mod_mcqtests_actions', 'mcqtests');
        //$answerLabel = $this->objLanguage->languageText('mod_mcqtests_answer', 'mcqtests');
        //$commentLabel = $this->objLanguage->languageText('mod_mcqtests_comment', 'mcqtests');
        $hintLabel = $this->objLanguage->languageText('mod_mcqtests_hint', 'mcqtests');
        $addhintLabel = $this->objLanguage->languageText('mod_mcqtests_hintenable', 'mcqtests');
        $markLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
        //$correctLabel = $this->objLanguage->languageText('mod_mcqtests_correctans', 'mcqtests');
        //$selectLabel = $this->objLanguage->languageText('mod_mcqtests_selectcorrect', 'mcqtests');
        $saveLabel = $this->objLanguage->languageText('word_save');
        $exitLabel = $this->objLanguage->languageText('word_cancel');
        //$saveaddLabel = $this->objLanguage->languageText('mod_mcqtests_saveaddanotherquestion', 'mcqtests');
        //$noansLabel = $this->objLanguage->languageText('mod_mcqtests_noansinquestion', 'mcqtests');
        $imageLabel = $this->objLanguage->languageText('mod_mcqtests_image', 'mcqtests');
        $addImageLabel = $this->objLanguage->languageText('mod_mcqtests_uploadimage', 'mcqtests');
        $removeImageLabel = $this->objLanguage->languageText('mod_mcqtests_removeimage', 'mcqtests');
        $includeImageLabel = $this->objLanguage->languageText('mod_mcqtests_includeimage', 'mcqtests');
        $lnPlain = $this->objLanguage->languageText('mod_mcqtests_plaintexteditor', 'mcqtests');
        $lnWysiwyg = $this->objLanguage->languageText('mod_mcqtests_wysiwygeditor', 'mcqtests');
        $errQuestion = $this->objLanguage->languageText('mod_mcqtests_questionrequired', 'mcqtests');
        $errMark = $this->objLanguage->languageText('mod_mcqtests_numericmark', 'mcqtests');
        $errMarkReq = $this->objLanguage->languageText('mod_mcqtests_markrequired', 'mcqtests');
        //$errSelect = $this->objLanguage->languageText('mod_mcqtests_selectanswer', 'mcqtests');
        $lbYes = $this->objLanguage->languageText('word_yes');
        $lbNo = $this->objLanguage->languageText('word_no');
        $lbEnable = $this->objLanguage->languageText('word_enable');
        $lbDisable = $this->objLanguage->languageText('word_disable');
        $lbMcq = $this->objLanguage->languageText('mod_mcqtests_multipleoptions', 'mcqtests');
        $lbTF = $this->objLanguage->languageText('mod_mcqtests_truefalse', 'mcqtests');
        $lbType = $this->objLanguage->languageText('mod_mcqtests_typeofquest', 'mcqtests');
        $lbNumOpt = $this->objLanguage->languageText('mod_mcqtests_numoptions', 'mcqtests');

        // Display test info
        $topStr = '<b>' . $testLabel . ':</b>&nbsp;&nbsp;' . $test['name'] . '<br />';
        $topStr.= '<b>' . $totalLabel . ':</b>&nbsp;&nbsp;' . $test['totalmark'] . '<br />&nbsp;';
        if (!empty($data)) {
            $question = $data['question'];
            $mark = $data['mark'];
            $hint = $data['hint'];
            $num = $data['questionorder'];
            $typeQ = $data['questiontype'];
            $questId = $data['id'];
        } else {
            $question = '';
            $mark = 1;
            $hint = '';
            $num = $test['count'] + 1;
            $typeQ = '';
            $questId = '';
        }

        $objRadioType = new radio('type');
        $objRadioType->setBreakSpace('&nbsp;&nbsp;/&nbsp;&nbsp;');
        $objRadioType->addOption('mcq', $lbMcq);
        $objRadioType->addOption('tf', $lbTF);

        if (empty($typeQ)) {
            $objRadioType->setSelected('mcq');
        } else {
            $objRadioType->setSelected($typeQ);
        }

        $objDropNum = new dropdown('options');
        $objDropNum->addOption(2, $this->objLanguage->languageText('mod_mcqtests_two', 'mcqtests'));
        $objDropNum->addOption(3, $this->objLanguage->languageText('mod_mcqtests_three', 'mcqtests'));
        $objDropNum->addOption(4, $this->objLanguage->languageText('mod_mcqtests_four', 'mcqtests'));
        $objDropNum->addOption(5, $this->objLanguage->languageText('mod_mcqtests_five', 'mcqtests'));
        $objDropNum->addOption(6, $this->objLanguage->languageText('mod_mcqtests_six', 'mcqtests'));


        if ($mode == 'edit') {
            if ($numAnswers == 0) {
                $objDropNum->setSelected(4);
            } else {
                $objDropNum->setSelected($numAnswers);
            }
        } else {
            $objDropNum->setSelected(4);
        }

        $objTableType = new htmltable('tabletype');
        $objTableType->startRow();
        $objTableType->addCell('<b>' . $lbType . '</b>', '20%');
        $objTableType->addCell($objRadioType->show(), '80%');
        $objTableType->endRow();
        $objTableType->startRow();
        $objTableType->addCell('<b>' . $lbNumOpt . '</b>', '20%');
        $objTableType->addCell($objDropNum->show(), '80%');
        $objTableType->endRow();

        $topStr .= $objTableType->show() . '<br />';

        // Display question for editing.
        $objHead = new htmlheading();
        $objHead->str = $questionLabel . ' ' . $num . ':';
        $objHead->type = 3;
        $topStr.= $objHead->show();

        $type = $this->getParam('editor', 'ww');
        if ($type == 'plaintext') {
            // Hidden element for the editor type
            $objInput = new textinput('editor', 'ww', 'hidden');

            $objText = new textarea('question', $question, 4, 80);
            $topStr .= $objText->show();

            $objLink = new link("javascript:document.getElementById('form_addquestion').action.value = 'changeeditor';document.getElementById('form_addquestion').submit();");
            $objLink->link = $lnWysiwyg;
            $topStr .= '<br />' . $objLink->show() . $objInput->show() . '<br /><br />';
        } else {
            // Hidden element for the editor type
            $objInput = new textinput('editor', 'plaintext', 'hidden');
            $objAddEditor = $this->getObject('htmlarea', 'htmlelements');
            $objAddEditor->init('question', $question, '300px', '500px');
            $objAddEditor->setDefaultToolBarSetWithoutSave();
            $topStr.= $objAddEditor->show();

            $objLink = new link("javascript:document.getElementById('form_addquestion').action.value = 'changeeditor';document.getElementById('form_addquestion').submit();");
            $objLink->link = $lnPlain;

            // Hide link to plain text
            //$topStr .= '<br />'.$objLink->show().$objInput->show().'<br /><br />';
            $topStr .= '<br />' . $objInput->show() . '<br /><br />';
        }

        $objInput = new textinput('mark', $mark);
        $objInput->size = 10;
        $topStr.= '<p><b>' . $markLabel . ':</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        $topStr.= $objInput->show() . '</p>';
        $topStr.= '<p><b>' . $hintLabel . ':</b></p><p>' . $addhintLabel . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>';
        $objRadio = new radio('enablehint');
        $objRadio->setBreakSpace('&nbsp;&nbsp;/&nbsp;&nbsp;');
        $objRadio->addOption('yes', $lbEnable);
        $objRadio->addOption('no', $lbDisable);
        $objRadio->setSelected('no');
        if (!empty($hint)) {
            $objRadio->setSelected('yes');
        }
        $topStr.= '<p>' . $objRadio->show() . '</p>';
        $objInput = new textinput('hint', $hint);
        $objInput->size = 83;
        $topStr.= $objInput->show() . '<p>&nbsp;</p>';

        // Save and exit buttons
        $objButton = new button('save', $saveLabel);
        $objButton->setToSubmit();
        $btn = $objButton->show();
        $objButton = new button('save', $exitLabel);
        $objButton->setToSubmit();
        $btn.= '&nbsp;&nbsp;&nbsp;&nbsp;' . $objButton->show();

        $objTextHid = new textinput('testId', $test['id'], 'hidden');

        $objTableButtons = new htmltable();
        $objTableButtons->startRow();
        $objTableButtons->addCell($objTextHid->show());
        $objTableButtons->addCell($btn, '', '', '', '', 'colspan="2"');
        $objTableButtons->endRow();

        $objInput = new textinput('qOrder', $num);
        $objInput->fldType = 'hidden';
        $topStr.= $objInput->show();
        $objInput = new textinput('questionId', $questId);
        $objInput->fldType = 'hidden';
        $topStr.= $objInput->show();

        // Create form and add the table
        $objFormEdit = new form('addquestion', $this->uri(array('action' => 'applyaddquestion')));
        $objFormEdit->addToForm($topStr);
        $objFormEdit->addToForm($objTableButtons->show());
        //$objFormEdit->addRule('question', $errQuestion, 'required');
        $objFormEdit->addRule('mark', $errMark, 'numeric');
        $objFormEdit->addRule('mark', $errMarkReq, 'required');

        return $objFormEdit->show();
    }

    public function createDatabaseQuestions($oldQuestions, $id) {

        $gridjs =
                "<script type='text/javascript' language='javascript'>
            //<![CDATA[
            var submitUrl = '" . str_replace("amp;", "", $this->uri(array('action' => 'submitdbquestions'))) . "',
                nextUrl = '" . str_replace("amp;", "", $this->uri(array('action' => 'view', 'id' => $id))) . "',
                courseID = '" . $this->getParam('id') . "',
                myUrl = '" . str_replace("amp;", "", $this->uri(array('action' => 'formattedquestions', 'id' => $id))) . "',
                courseName = '" . $this->dbTestadmin->getTestName($id) . "',
                calcQFormUrl = '" . str_replace("amp;", "", $this->uri(array('action' => 'calcqform', 'id' => $id))) . "';//]]>
        </script>";

        return $gridjs;
    }

    /**
     * Method to list categories in a context
     *
     * @access private
     * @param  array $contextCode Contains Context Code
     * @author Paul Mungai
     */
    public function createCategoryList($contextCode = Null) {
        //Form text
        $noRecords = $this->objLanguage->languageText('mod_mcqtests_norecords', 'mcqtests', "No records found");
        $addCategory = $this->objLanguage->languageText('mod_mcqtests_addcategory', 'mcqtests', "Add Category");
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $addCategory = $this->objLanguage->languageText('mod_mcqtests_addcategory', 'mcqtests', "Add Category");
        $parentCategory = $this->objLanguage->languageText('mod_mcqtests_parentcategory', 'mcqtests', "Parent Category");
        $wordName = $this->objLanguage->languageText('mod_mcqtests_wordname', 'mcqtests', "Name");

        if (!empty($contextCode)) {
            $data = $this->dbCategory->getCategories($this->contextCode);
        } else {
            $data = $this->dbCategory->getCategories($this->contextCode);
        }
        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $wordCategory;

        //Add heading/title to string
        $str = "";

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '600px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';

        //Add Heading to the table
        $objTable->startRow();
        $objTable->addCell("<b>" . $wordName . "</b>", '80%');
        $objTable->addCell(Null, '20%');
        $objTable->endRow();

        //question name text box
        if (!empty($data)) {
            foreach ($data as $thisdata) {
                // Show the edit link
                $iconEdit = $this->getObject('geticon', 'htmlelements');
                $iconEdit->setIcon('edit');
                $iconEdit->alt = $this->objLanguage->languageText("word_edit", 'system');
                $iconEdit->align = false;
                $objLink = &$this->getObject("link", "htmlelements");
                $objLink->link($this->uri(array(
                            'module' => 'mcqtests',
                            'action' => 'addcategory',
                            'id' => $thisdata['id']
                        )));
                $objLink->link = $iconEdit->show();
                $linkEdit = $objLink->show();
                // Show the delete link
                $iconDelete = $this->getObject('geticon', 'htmlelements');
                $iconDelete->setIcon('delete');
                $iconDelete->alt = $this->objLanguage->languageText("word_delete", 'mcqtests');
                $iconDelete->align = false;
                $objConfirm = &$this->getObject("link", "htmlelements");
                $objConfirm = &$this->newObject('confirm', 'utilities');
                $objConfirm->setConfirm($iconDelete->show(), $this->uri(array(
                            'module' => 'mcqtests',
                            'action' => 'deletecat',
                            'id' => $thisdata["id"]
                        )), $this->objLanguage->languageText('mod_mcqtests_deletetest', 'mcqtests') . "?");
                $objTable->startRow();
                $objTable->addCell($thisdata["name"], '80%');
                $objTable->addCell($linkEdit . "&nbsp;&nbsp;" . $objConfirm->show(), '20%');
                $objTable->endRow();
            }
        } else {
            $objTable->startRow();
            $objTable->addCell($noRecords, '80%', $valign = "top", $align = 'left', $class = null, $attrib = "colspan='2'", $border = '0');
            $objTable->endRow();
        }
        $str .= $objTable->show();
        // Create Back Button
        $buttonBack = new button("submit", $this->objLanguage->languageText("word_back"));
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2',
                    'id' => $this->getParam('id')
                )));
        $objBack->link = $buttonBack->showSexy();
        $str .= $objBack->show();

        // Create Back Button
        $buttonAdd = new button("submit", $addCategory);
        $objAdd = &$this->getObject("link", "htmlelements");
        $objAdd->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'addcategory'
                )));
        $objAdd->link = $buttonAdd->showSexy();
        $str .= " " . $objAdd->show();

        //Add fieldset to hold Descriptions stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '600px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($wordCategory);

        //Add table to General Fieldset
        $objFieldset->addContent($str);
        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'mcqlisting'
                        )));
        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();
        return $form->show();
    }

    /**
     * Method to create add/edit category form
     *
     * @access private
     * @param  array $test Contains test data
     * @param  string $id Contains the test id
     * @author Paul Mungai
     */
    public function createAddCategoryForm($test, $id) {
        //Load Classes
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textarea", "htmlelements");

        //Form text
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $wordCategories = $this->objLanguage->languageText("mod_mcqtests_categories", 'mcqtests', "Categories");
        $wordBack = $this->objLanguage->languageText("word_back");
        $BackToList = $wordBack . " " . $wordTo . " " . $phraseListOf . " " . $wordCategories;
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $addDescform = $this->objLanguage->languageText('mod_mcqtests_addDescription', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');

        $addCategory = $this->objLanguage->languageText('mod_mcqtests_addcategory', 'mcqtests', "Add Category");
        $parentCategory = $this->objLanguage->languageText('mod_mcqtests_parentcategory', 'mcqtests', "Parent Category");
        $wordName = $this->objLanguage->languageText('mod_mcqtests_wordname', 'mcqtests', "Name");
        $phraseCatInfo = $this->objLanguage->languageText('mod_mcqtests_catinfo', 'mcqtests', 'Category Information');
        $phraseIsRequired = $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests', 'is required');
        $editCategory = $this->objLanguage->languageText('mod_mcqtests_editcategory', 'mcqtests', 'Edit Category');

        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'addcategoryconfirm',
                            'id' => $id
                        )));
        $data = "";

        if (!empty($id)) {
            $data = $this->dbCategory->getCategory($id);
            $data = $data[0];
        }
        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $addCategory;

        //Add heading/title to form
        //$form->addToForm($objHeading->show());
        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';

        //parent category
        $category = new textinput("parentId", "");
        $category->size = 60;
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($parentCategory, '20%');
        $objTable->addCell($wordName, '80%');
        $objTable->endRow();

        //question name text box
        if (empty($data)) {
            $catname = new textinput("categoryname", $data['parentcategoryid']);
        } else {
            $catname = new textinput("categoryname", $data['name']);
        }
        $catname->size = 60;
        $form->addRule('categoryname', $wordName . " " . $phraseIsRequired, 'required');
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($wordName, '20%');
        $objTable->addCell($catname->show(), '80%');
        $objTable->endRow();

        //Description
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'desc';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        if (empty($data)) {
            $qntext = '';
        } else {
            $qntext = $data['categoryinfo'];
        }
        $editor->setContent($qntext);
        //Add Description to the table
        $objTable->startRow();
        $objTable->addCell($phraseCatInfo, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold general stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '800px';
        //$objFieldset->align = 'center';
        if (empty($id)) {
            $objFieldset->setLegend($addCategory);
        } else {
            $objFieldset->setLegend($editCategory);
        }

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();
        $button = new button("submit", $this->objLanguage->languageText("word_save"));
        $button->setToSubmit();
        $btnSave = $button->showSexy();
        // Create Cancel Button
        $buttonCancel = new button("submit", $BackToList);
        $objCancel = &$this->getObject("link", "htmlelements");
        $objCancel->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'categorylisting',
                    'id' => $id
                )));
        $objCancel->link = $buttonCancel->showSexy();
        $btnCancel = $objCancel->show();
        // Create Back Button
        $buttonBack = new button("submit", $backToHome);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2'
                )));
        $objBack->link = $buttonBack->showSexy();
        $btnBack = $objBack->show();
        //Add Save and Cancel Buttons to form
        $form->addToForm("<br />" . $btnSave . " " . $btnCancel . " " . $btnBack . "<br />");

        return "<div>" . $form->show() . "</div>";
    }

    /**
     * Method to create a list of descriptions
     *
     * @access private
     * @param  array $test Contains test data
     * @param  string $id Contains the category id
     * @param  string $test Contains the test id
     * @author Paul Mungai
     */
    public function createDescriptionList($categoryId, $test=Null) {
        //Form text
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $wordDescriptions = $this->objLanguage->languageText("mod_mcqtests_descriptions", 'mcqtests', "Descriptions");
        $wordBack = $this->objLanguage->languageText("word_back");
        $listTitle = $phraseListOf . " " . $wordDescriptions;
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $noRecords = $this->objLanguage->languageText('mod_mcqtests_norecords', 'mcqtests', "No records found");
        $addDesc = $this->objLanguage->languageText('mod_mcqtests_addDesc', 'mcqtests', 'Add Description');
        $wordDesc = $this->objLanguage->languageText('mod_mcqtests_description', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');
        $phraseQnText = $this->objLanguage->languageText('mod_mcqtests_qntext', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');
        $wordTags = $this->objLanguage->languageText('mod_mcqtests_wordtags', 'mcqtests');

        if (!empty($categoryId) || $categoryId == NULL) {
            $data = $this->dbDescription->getDescriptions($categoryId);
        }
        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $wordDesc;

        //Add heading/title to string
        $str = "";

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '600px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';

        //Add Heading to the table
        $objTable->startRow();
        $objTable->addCell("<b>" . $phraseQnName . "</b>", '80%');
        $objTable->addCell(Null, '20%');
        $objTable->endRow();

        //question name text box
        if (!empty($data)) {
            foreach ($data as $descdata) {
                // Show the edit link
                $iconEdit = $this->getObject('geticon', 'htmlelements');
                $iconEdit->setIcon('edit');
                $iconEdit->alt = $this->objLanguage->languageText("word_edit", 'system');
                $iconEdit->align = false;
                $objLink = &$this->getObject("link", "htmlelements");
                $objLink->link($this->uri(array(
                            'module' => 'mcqtests',
                            'action' => 'addeditdesc',
                            'id' => $descdata['id'],
                            'test' => $test
                        )));
                $objLink->link = $iconEdit->show();
                $linkEdit = $objLink->show();
                // Show the delete link
                $iconDelete = $this->getObject('geticon', 'htmlelements');
                $iconDelete->setIcon('delete');
                $iconDelete->alt = $this->objLanguage->languageText("word_delete", 'mcqtests');
                $iconDelete->align = false;
                $objConfirm = &$this->getObject("link", "htmlelements");
                $objConfirm = &$this->newObject('confirm', 'utilities');
                $objConfirm->setConfirm($iconDelete->show(), $this->uri(array(
                            'module' => 'mcqtests',
                            'action' => 'deletedesc',
                            'id' => $descdata["id"],
                            'test' => $test
                        )), $this->objLanguage->languageText('mod_mcqtests_deletetest', 'mcqtests') . "?");
                // Show the preview link
                $iconPreview = $this->getObject('geticon', 'htmlelements');
                $iconPreview->setIcon('preview');
                $iconPreview->alt = $this->objLanguage->languageText("word_preview", 'system');
                $iconPreview->align = false;
                //Show the view Icon
                $objPopup = new windowpop();
                $objPopup->set('location', $this->uri(array(
                            'action' => 'viewdescription',
                            'id' => $descdata['id'],
                            'test' => $test
                                ), 'mcqtests'));
                $objPopup->set('linktext', $iconPreview->show());
                $objPopup->set('width', '700');
                $objPopup->set('height', '350');
                $objPopup->set('left', '200');
                $objPopup->set('top', '200');
                $objPopup->set('scrollbars', 'yes');
                $objPopup->set('resizable', 'yes');
                $objPopup->putJs(); // you only need to do this once per page
                $linkPreview = $objPopup->show();
                $objTable->startRow();
                $objTable->addCell($descdata['questionname'], '80%');
                $objTable->addCell($linkEdit . "&nbsp;&nbsp;" . $objConfirm->show() . "&nbsp;&nbsp;" . $linkPreview, '20%');
                $objTable->endRow();
            }
        } else {
            $objTable->startRow();
            $objTable->addCell($noRecords, '80%', $valign = "top", $align = 'left', $class = null, $attrib = "colspan='2'", $border = '0');
            $objTable->endRow();
        }
        $str .= $objTable->show();

        // Create Back Button
        $buttonAdd = new button("submit", $addDesc);
        $objAdd = &$this->getObject("link", "htmlelements");
        $objAdd->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'addeditdesc',
                    'test' => $test
                )));
        $objAdd->link = $buttonAdd->showSexy();
        $str .= " " . $objAdd->show();

        // Create Back Button
        $buttonBack = new button("submit", $backToHome);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2',
                    'test' => $test
                )));
        $objBack->link = $buttonBack->showSexy();
        $str .= " " . $objBack->show();

        //Add fieldset to hold Descriptions stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '600px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($listTitle);

        //Add table to General Fieldset
        $objFieldset->addContent($str);
        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'questionbank',
                            'test' => $test
                        )));
        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();
        return $form->show();
    }

    /**
     * Method to create add description form
     *
     * @access private
     * @param  array $test Contains test data
     * @param  string $id Contains the test id
     * @author Paul Mungai
     */
    public function createAddDescriptionForm($test, $id) {
        //Load Classes
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textarea", "htmlelements");

        //Form text
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $wordDescriptions = $this->objLanguage->languageText("mod_mcqtests_descriptions", 'mcqtests', "Descriptions");
        $wordBack = $this->objLanguage->languageText("word_back");
        $BackToList = $wordBack . " " . $wordTo . " " . $phraseListOf . " " . $wordDescriptions;
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $addDescform = $this->objLanguage->languageText('mod_mcqtests_addDescription', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');
        $phraseQnText = $this->objLanguage->languageText('mod_mcqtests_qntext', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');
        $wordTags = $this->objLanguage->languageText('mod_mcqtests_wordtags', 'mcqtests');
        $phraseOfficialTags = $this->objLanguage->languageText('mod_mcqtests_officialtags', 'mcqtests');
        $phraseMngOfficialTags = $this->objLanguage->languageText('mod_mcqtests_mngofficialtags', 'mcqtests');
        $phraseOtherTags = $this->objLanguage->languageText('mod_mcqtests_othertags', 'mcqtests');
        $phraseOtherTagsDesc = $this->objLanguage->languageText('mod_mcqtests_othertagsdesc', 'mcqtests');
        $phraseQuestionBank = $this->objLanguage->languageText('mod_mcqtest_questionbank', 'mcqtests', "Question bank");
        $phraseBackToQnBank = $this->objLanguage->languageText('mod_mcqtest_backtoqnbank', 'mcqtests', "Back to question bank");

        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'adddescconfirm',
                            'id' => $id,
                            'test' => $test
                        )));
        $data = "";
        if (!empty($id)) {
            $data = $this->dbDescription->getDescription($id);
            $data = $data[0];
        }
        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $addDescform;

        //Add heading/title to form
        $form->addToForm($objHeading->show());

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';

        //category text box
        $category = new textinput("desccategoryid", "");
        $category->size = 60;
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($wordCategory, '20%');
        $objTable->addCell($wordGeneral, '80%');
        $objTable->endRow();

        //question name text box
        if (empty($data)) {
            $qnname = new textinput("descqnname", "");
        } else {
            $qnname = new textinput("descqnname", $data['questionname']);
        }
        $qnname->size = 60;
        $form->addRule('descqnname', $this->objLanguage->languageText('mod_mcqtests_qnnamerequired', 'mcqtests'), 'required');
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnName, '20%');
        $objTable->addCell($qnname->show(), '80%');
        $objTable->endRow();

        //qn text
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'descqntext';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        if (empty($data)) {
            $qntext = '';
        } else {
            $qntext = $data['questiontext'];
        }
        $editor->setContent($qntext);
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnText, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //general feedback
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'descgenfeedback';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        if (empty($data)) {
            $genfeedback = '';
        } else {
            $genfeedback = $data['feedback'];
        }
        $editor->setContent($genfeedback);
        //Add General Feedback to the table
        $objTable->startRow();
        $objTable->addCell($wordFeedback, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold general stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '800px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($wordGeneral);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();
        //Create table to hold the tags
        $objTable2 = new htmltable();
        $objTable2->width = '800px';
        $objTable2->attributes = " align='center' border='0'";
        $objTable2->cellspacing = '12';

        //tags text box
        if (empty($data)) {
            $officialtags = new textinput("descofficialtags", "");
        } else {
            $officialtags = new textinput("descofficialtags", $data['tags']);
        }
        $officialtags->size = 60;

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell($wordTags, '20%');
        $objTable2->addCell($phraseOfficialTags . " ( " . $phraseMngOfficialTags . " ) " . "<br />" . $officialtags->show(), '80%');
        $objTable2->endRow();

        //tags text box
        if (empty($data)) {
            $othertags = "";
        } else {
            $othertags = $data["othertags"];
        }
        $othertagsTA = new textarea();
        $othertagsTA->setName("descothertags");
        $othertagsTA->setValue($othertags);
        $othertagsTA->setRows('4');
        $othertagsTA->setColumns('68');

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell('&nbsp;');
        $objTable2->addCell($phraseOtherTags . " ( " . $phraseOtherTagsDesc . " ) " . "<br />" . $othertagsTA->show(), '80%');
        $objTable2->endRow();

        //Add fieldset to hold tags
        $objFieldset->setLegend($wordTags);

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable2->show());
        $objFieldset->width = '800px';
        // Create Save Button
        $form->addToForm($objFieldset->show());
        $button = new button("submit", $this->objLanguage->languageText("word_save"));
        $button->setToSubmit();
        $btnSave = $button->showSexy();
        // Create Cancel Button
        $buttonCancel = new button("submit", $backToHome);
        $objCancel = &$this->getObject("link", "htmlelements");
        $objCancel->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2',
                    'id' => $id
                )));
        $objCancel->link = $buttonCancel->showSexy();
        $btnCancel = $objCancel->show();
        // Create Back Button
        $buttonBack = new button("submit", $BackToList);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'mcqlisting'
                )));
        $objBack->link = $buttonBack->showSexy();
        $btnBack = $objBack->show();

        // Create Back Button
        $buttonBack = new button("submit", $phraseBackToQnBank);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'questionbank',
                    'test' => $test
                )));
        $objBack->link = $buttonBack->showSexy();
        $btnBackQBank = $objBack->show();
        //Add Save and Cancel Buttons to form
        $form->addToForm("<br />" . $btnSave . " " . $btnBackQBank . " " . $btnCancel . "<br />");

        return "<div>" . $form->show() . "</div>";
    }

    /**
     * Method to view a description
     *
     * @access private
     * @param  array $test Contains test data
     * @param  string $id Contains the test id
     * @author Paul Mungai
     */
    public function viewDescription($test, $id) {
        //Load Classes
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textarea", "htmlelements");

        //Form text
        $wordExit = $this->objLanguage->languageText('mod_systext_exit', 'system', "Exit");
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $wordDescriptions = $this->objLanguage->languageText("mod_mcqtests_descriptions", 'mcqtests', "Descriptions");
        $wordDescription = $this->objLanguage->languageText("mod_mcqtests_description", 'mcqtests', "Description");
        $wordBack = $this->objLanguage->languageText("word_back");
        $BackToList = $wordBack . " " . $wordTo . " " . $phraseListOf . " " . $wordDescriptions;
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $addDescform = $this->objLanguage->languageText('mod_mcqtests_addDescription', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');
        $phraseQnText = $this->objLanguage->languageText('mod_mcqtests_qntext', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');
        $wordTags = $this->objLanguage->languageText('mod_mcqtests_wordtags', 'mcqtests');
        $phraseOfficialTags = $this->objLanguage->languageText('mod_mcqtests_officialtags', 'mcqtests');
        $phraseMngOfficialTags = $this->objLanguage->languageText('mod_mcqtests_mngofficialtags', 'mcqtests');
        $phraseOtherTags = $this->objLanguage->languageText('mod_mcqtests_othertags', 'mcqtests');
        $phraseOtherTagsDesc = $this->objLanguage->languageText('mod_mcqtests_othertagsdesc', 'mcqtests');
        $phraseQuestionBank = $this->objLanguage->languageText('mod_mcqtest_questionbank', 'mcqtests', "Question bank");
        $phraseBackToQnBank = $this->objLanguage->languageText('mod_mcqtest_backtoqnbank', 'mcqtests', "Back to question bank");

        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'viewdescription',
                            'id' => $id,
                            'test' => $test
                        )));
        $data = "";
        if (!empty($id)) {
            $data = $this->dbDescription->getDescription($id);
            $data = $data[0];
        }
        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $addDescform;

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '650px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';


        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($data['questionname'], '100%');
        $objTable->endRow();

        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($data['questiontext'], '100%');
        $objTable->endRow();

        //Add fieldset to hold general stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '700px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($wordDescription);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();

        // Create close button
        $buttonClose = new button("submit", $wordExit, "window.close()");
        $buttonClose = $buttonClose->showSexy();

        //Add Close Button to form
        $form->addToForm("<br />" . $buttonClose . "<br />");

        return "<div>" . $form->show() . "</div>";
    }

    /**
     * Method to create add short answer form
     *
     * @access private
     * @param  array $test Contains test data
     * @param  string $id Contains the answer id
     * @author Paul Mungai
     */
    public function createAddShortAnswerForm($test, $id) {
        //Load Classes
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textarea", "htmlelements");
        $this->loadClass("dropdown", "htmlelements");

        //Form texts
        $addDescform = $this->objLanguage->languageText('mod_mcqtests_addingshortanswerqn', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');
        $phraseQnText = $this->objLanguage->languageText('mod_mcqtests_qntext', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');
        $wordTags = $this->objLanguage->languageText('mod_mcqtests_wordtags', 'mcqtests');
        $phraseOfficialTags = $this->objLanguage->languageText('mod_mcqtests_officialtags', 'mcqtests');
        $phraseMngOfficialTags = $this->objLanguage->languageText('mod_mcqtests_mngofficialtags', 'mcqtests');
        $phraseOtherTags = $this->objLanguage->languageText('mod_mcqtests_othertags', 'mcqtests');
        $phraseOtherTagsDesc = $this->objLanguage->languageText('mod_mcqtests_othertagsdesc', 'mcqtests');
        $phraseQnGrade = $this->objLanguage->languageText('mod_mcqtests_defaultQnGrade', 'mcqtests');
        $phrasePenaltyFactor = $this->objLanguage->languageText('mod_mcqtests_penaltyfactor', 'mcqtests');
        $phraseCaseUnimportant = $this->objLanguage->languageText('mod_mcqtests_nocaseunimportant', 'mcqtests');
        $phraseCaseImportant = $this->objLanguage->languageText('mod_mcqtests_yescaseimportant', 'mcqtests');
        $phraseCaseSensitivity = $this->objLanguage->languageText('mod_mcqtests_caseSensitivity', 'mcqtests');
        $wordAnswer = $this->objLanguage->languageText('mod_mcqtests_wordanswer', 'mcqtests');
        $wordGrade = $this->objLanguage->languageText('mod_mcqtests_wordgrade', 'mcqtests');

        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'addshortansconfirm',
                            'id' => $id
                        )));

        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $addDescform;

        //Add heading/title to form
        $form->addToForm($objHeading->show());

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //category text box
        $category = new textinput("categoryid", "");
        $category->size = 60;
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($wordCategory, '20%');
        $objTable->addCell($wordGeneral, '80%');
        $objTable->endRow();

        //question name text box
        $qnname = new textinput("qnname", "");
        $qnname->size = 60;
        $form->addRule('qnname', $this->objLanguage->languageText('mod_mcqtests_qnnamerequired', 'mcqtests'), 'required');
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnName, '20%');
        $objTable->addCell($qnname->show(), '80%');
        $objTable->endRow();

        //qn text
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'questiontext';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $qntext = '';
        $editor->setContent($qntext);
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnText, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //Add default qn grade
        $qngrade = new textinput("qngrade", "");
        $qngrade->size = 60;
        $form->addRule('qngrade', $phraseQnGrade . " " . $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests'), 'required');
        //Add qn grade to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnGrade, '20%');
        $objTable->addCell($qngrade->show(), '80%');
        $objTable->endRow();

        //Add Penalty factor
        $pfactor = new textinput("penaltyfactor", "");
        $pfactor->size = 60;
        $form->addRule('penaltyfactor', $phrasePenaltyFactor . " " . $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests'), 'required');
        //Add penalty factor field to the table
        $objTable->startRow();
        $objTable->addCell($phrasePenaltyFactor, '20%');
        $objTable->addCell($pfactor->show(), '80%');
        $objTable->endRow();

        //general feedback
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'generalfeedback';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $genfeedback = '';
        $editor->setContent($genfeedback);
        //Add General Feedback to the table
        $objTable->startRow();
        $objTable->addCell($wordFeedback, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        $sensitivitydropdown = new dropdown("sensitivity");
        $sensitivitydropdown->addOption("0", $phraseCaseUnimportant);
        $sensitivitydropdown->addOption("1", $phraseCaseImportant);
        $sensitivitydropdown->setSelected("0");

        //Add Sensitivity dropdown to the table
        $objTable->startRow();
        $objTable->addCell($phraseCaseSensitivity, '20%');
        $objTable->addCell($sensitivitydropdown->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold general stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        //$objFieldset->width = '800px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($wordGeneral);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the answers
        $objTable3 = new htmltable();
        $objTable3->width = '800px';
        $objTable3->attributes = " align='center' border='0'";
        $objTable3->cellspacing = '12';

        //answer1 text box
        $answer = new textinput("answer1", "");
        $answer->size = 60;

        //Add Answer1 to the table
        $objTable3->startRow();
        $objTable3->addCell($wordAnswer, '20%');
        $objTable3->addCell($answer->show(), '80%');
        $objTable3->endRow();

        //Add Answer1 grade to the table
        $objTable3->startRow();
        $objTable3->addCell($wordGrade, '20%');
        $objTable3->addCell("", '80%');
        $objTable3->endRow();

        //answer1 feedback htmlarea
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'feedback1';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $qntext = '';
        $editor->setContent($qntext);

        //Add Answer1 feedback to the table
        $objTable3->startRow();
        $objTable3->addCell($wordFeedback, '20%');
        $objTable3->addCell($editor->show(), '80%');
        $objTable3->endRow();

        //Add fieldset to hold tags
        $objFieldset->setLegend($wordAnswer . " 1");

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable3->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the answers
        $objTable3 = new htmltable();
        $objTable3->width = '800px';
        $objTable3->attributes = " align='center' border='0'";
        $objTable3->cellspacing = '12';

        //answer text box
        $answer = new textinput("answer2", "");
        $answer->size = 60;

        //Add Answer to the table
        $objTable3->startRow();
        $objTable3->addCell($wordAnswer, '20%');
        $objTable3->addCell($answer->show(), '80%');
        $objTable3->endRow();

        //Add Answer grade to the table
        $objTable3->startRow();
        $objTable3->addCell($wordGrade, '20%');
        $objTable3->addCell("", '80%');
        $objTable3->endRow();

        //answer feedback htmlarea
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'feedback2';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $qntext = '';
        $editor->setContent($qntext);

        //Add Answer feedback to the table
        $objTable3->startRow();
        $objTable3->addCell($wordFeedback, '20%');
        $objTable3->addCell($editor->show(), '80%');
        $objTable3->endRow();

        //Add fieldset to hold answer
        $objFieldset->setLegend($wordAnswer . " 2");

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable3->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the answers
        $objTable3 = new htmltable();
        $objTable3->width = '800px';
        $objTable3->attributes = " align='center' border='0'";
        $objTable3->cellspacing = '12';

        //answer text box
        $answer = new textinput("answer3", "");
        $answer->size = 60;

        //Add Answer to the table
        $objTable3->startRow();
        $objTable3->addCell($wordAnswer, '20%');
        $objTable3->addCell($answer->show(), '80%');
        $objTable3->endRow();

        //Add Answer grade to the table
        $objTable3->startRow();
        $objTable3->addCell($wordGrade, '20%');
        $objTable3->addCell("", '80%');
        $objTable3->endRow();

        //answer feedback htmlarea
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'feedback3';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $qntext = '';
        $editor->setContent($qntext);

        //Add Answer feedback to the table
        $objTable3->startRow();
        $objTable3->addCell($wordFeedback, '20%');
        $objTable3->addCell($editor->show(), '80%');
        $objTable3->endRow();

        //Add fieldset to hold answer
        $objFieldset->setLegend($wordAnswer . " 3");

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable3->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the tags
        $objTable2 = new htmltable();
        $objTable2->width = '800px';
        $objTable2->attributes = " align='center' border='0'";
        $objTable2->cellspacing = '12';

        //tags text box
        $officialtags = new textinput("officialtags", "");
        $officialtags->size = 60;

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell($wordTags, '20%');
        $objTable2->addCell($phraseOfficialTags . " ( " . $phraseMngOfficialTags . " ) " . "<br />" . $officialtags->show(), '80%');
        $objTable2->endRow();

        //tags text box
        $othertags = "";
        $othertagsTA = new textarea();
        $othertagsTA->setName("othertags");
        $othertagsTA->setValue($othertags);
        $othertagsTA->setRows('4');
        $othertagsTA->setColumns('70');

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell('&nbsp;');
        $objTable2->addCell($phraseOtherTags . " ( " . $phraseOtherTagsDesc . " ) " . "<br />" . $othertagsTA->show(), '80%');
        $objTable2->endRow();

        //Add fieldset to hold tags
        $objFieldset->setLegend($wordTags);

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable2->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        // Create Save Button
        $button = new button("submit", $this->objLanguage->languageText("word_save"));
        $button->setToSubmit();
        $btnSave = $button->showSexy();
        // Create Cancel Button
        $buttonCancel = new button("submit", $this->objLanguage->languageText("word_cancel"));
        $objCancel = &$this->getObject("link", "htmlelements");
        $objCancel->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view',
                    'id' => $id
                )));
        $objCancel->link = $buttonCancel->showSexy();
        $btnCancel = $objCancel->show();

        //Add Save and Cancel Buttons to form
        $form->addToForm("<br />" . $btnSave . " " . $btnCancel . "<br />");

        return "<div>" . $form->show() . "</div>";
    }

    /**
     * Method to create a list of random short-answers
     *
     * @access private
     * @param  string $contextCode Contains the context code
     * @param  string $categoryId Contains the category identifier
     * @param  string $tests Contains the test identifier
     * @author Paul Mungai
     */
    public function createRSAList($contextCode, $categoryId=Null, $test=Null) {
        //Form text
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $phraseRSAQuestions = $this->objLanguage->languageText("mod_mcqtests_rsaquestions", 'mcqtests', "RSA matching questions");
        $wordBack = $this->objLanguage->languageText("word_back", Null, "Back");
        $listTitle = $phraseListOf . " " . $phraseRSAQuestions;
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $noRecords = $this->objLanguage->languageText('mod_mcqtests_norecords', 'mcqtests', "No records found");
        $addRSA = $this->objLanguage->languageText('mod_mcqtests_addrsaquestion', 'mcqtests', 'Add a RSA matching question');
        $wordDesc = $this->objLanguage->languageText('mod_mcqtests_description', 'mcqtests', "Description");
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');

        if (!empty($categoryId)) {
            //$data = $this->dbQuestions->getDescriptions($categoryId);
        } else {
            $data = $this->dbCategory->getContextQuestions($contextCode);
        }
        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $wordDesc;

        //Add heading/title to string
        $str = "";

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '600px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';

        //Add Heading to the table
        $objTable->startRow();
        $objTable->addCell("<b>" . $phraseQnName . "</b>", '80%');
        $objTable->addCell(Null, '20%');
        $objTable->endRow();

        //question name text box
        if (!empty($data)) {
            foreach ($data as $descdata) {
                if ($descdata['qtype'] == "RSA") {
                    // Show the edit link
                    $iconEdit = $this->getObject('geticon', 'htmlelements');
                    $iconEdit->setIcon('edit');
                    $iconEdit->alt = $this->objLanguage->languageText("word_edit", 'system');
                    $iconEdit->align = false;
                    $objLink = &$this->getObject("link", "htmlelements");
                    $objLink->link($this->uri(array(
                                'module' => 'mcqtests',
                                'action' => 'addrandomshortans',
                                'id' => $descdata['questionid'],
                                'test' => $test
                            )));
                    $objLink->link = $iconEdit->show();
                    $linkEdit = $objLink->show();
                    // Show the delete link
                    $iconDelete = $this->getObject('geticon', 'htmlelements');
                    $iconDelete->setIcon('delete');
                    $iconDelete->alt = $this->objLanguage->languageText("word_delete", 'mcqtests');
                    $iconDelete->align = false;
                    $objConfirm = &$this->getObject("link", "htmlelements");
                    $objConfirm = &$this->newObject('confirm', 'utilities');
                    $objConfirm->setConfirm($iconDelete->show(), $this->uri(array(
                                'module' => 'mcqtests',
                                'action' => 'deletersa',
                                'id' => $descdata["questionid"],
                                'test' => $test
                            )), $this->objLanguage->languageText('mod_mcqtests_deletetest', 'mcqtests') . "?");
                    $objTable->startRow();
                    $objTable->addCell($descdata['name'], '80%');
                    $objTable->addCell($linkEdit . "&nbsp;&nbsp;" . $objConfirm->show(), '20%');
                    $objTable->endRow();
                }
            }
        } else {
            $objTable->startRow();
            $objTable->addCell($noRecords, '80%', $valign = "top", $align = 'left', $class = null, $attrib = "colspan='2'", $border = '0');
            $objTable->endRow();
        }
        $str .= $objTable->show();

        // Create Back Button
        $buttonAdd = new button("submit", $addRSA);
        $objAdd = &$this->getObject("link", "htmlelements");
        $objAdd->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'addrandomshortans',
                    'test' => $test
                )));
        $objAdd->link = $buttonAdd->showSexy();
        $str .= " " . $objAdd->show();

        // Create Back Button
        $buttonBack = new button("submit", $backToHome);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2',
                    'test' => $test
                )));
        $objBack->link = $buttonBack->showSexy();
        $str .= " " . $objBack->show();

        //Add fieldset to hold Descriptions stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '600px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($listTitle);

        //Add table to General Fieldset
        $objFieldset->addContent($str);
        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'questionbank',
                            'test' => $test
                        )));
        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();
        return $form->show();
    }

    /**
     * Method to create add short answer form
     *
     * @access private
     * @param  array $test Contains test data
     * @param  string $id Contains the answer id
     * @author Paul Mungai
     */
    public function createRandomShortAnsForm($test, $id) {
        //Load Classes
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textarea", "htmlelements");
        $this->loadClass("dropdown", "htmlelements");
        $this->loadClass("checkbox", "htmlelements");

        //Form texts
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $wordBack = $this->objLanguage->languageText("word_back");
        $BackToList = $wordBack . " " . $wordTo . " " . $phraseListOf . " ";
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $addDescform = $this->objLanguage->languageText('mod_mcqtests_addDescription', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');
        $phraseQnText = $this->objLanguage->languageText('mod_mcqtests_qntext', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');
        $wordTags = $this->objLanguage->languageText('mod_mcqtests_wordtags', 'mcqtests');
        $phraseOfficialTags = $this->objLanguage->languageText('mod_mcqtests_officialtags', 'mcqtests');
        $phraseMngOfficialTags = $this->objLanguage->languageText('mod_mcqtests_mngofficialtags', 'mcqtests');
        $phraseOtherTags = $this->objLanguage->languageText('mod_mcqtests_othertags', 'mcqtests');
        $phraseOtherTagsDesc = $this->objLanguage->languageText('mod_mcqtests_othertagsdesc', 'mcqtests');
        $phraseQnGrade = $this->objLanguage->languageText('mod_mcqtests_defaultQnGrade', 'mcqtests');
        $phrasePenaltyFactor = $this->objLanguage->languageText('mod_mcqtests_penaltyfactor', 'mcqtests');
        $phraseNoQnsToSelect = $this->objLanguage->languageText('mod_mcqtests_noqnstoselect', 'mcqtests');
        $phraseSaveInCategory = $this->objLanguage->languageText('mod_mcqtests_saveincategory', 'mcqtests');
        $phraseCurrentCategory = $this->objLanguage->languageText('mod_mcqtests_currentcategory', 'mcqtests');
        $phraseUseCategory = $this->objLanguage->languageText('mod_mcqtests_usecategory', 'mcqtests');
        $phraseRandomShortAns = $this->objLanguage->languageText('mod_mcqtests_randomshortans', 'mcqtests');
        $phraseAddingA = $this->objLanguage->languageText('mod_mcqtests_addinga', 'mcqtests');
        $phraseEditingA = $this->objLanguage->languageText('mod_mcqtests_editinga', 'mcqtests');
        $phraseLastSaved = $this->objLanguage->languageText('mod_mcqtests_lastsaved', 'mcqtests');
        $phraseCreatedOrSaved = $this->objLanguage->languageText('mod_mcqtests_createdorsaved', 'mcqtests');
        $phraseCreated = $this->objLanguage->languageText('mod_mcqtests_created', 'mcqtests');
        $phrasePermissions = $this->objLanguage->languageText("mod_mcqtests_permissionsto", 'mcqtests');
        $phraseSaveChanges = $this->objLanguage->languageText("mod_mcqtests_savechanges", 'mcqtests', "Save changes");
        $phraseSaveAsNewQn = $this->objLanguage->languageText("mod_mcqtests_saveasnewqn", 'mcqtests', "Save as a new question");
        $phraseRSAQuestions = $this->objLanguage->languageText("mod_mcqtests_rsaquestions", 'mcqtests', "RSA matching questions");
        $listTitle = $phraseListOf . " " . $phraseRSAQuestions;
        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'addrandomshortansconfirm',
                            'id' => $id
                        )));
        $qnData = $this->dbQuestions->getQuestion($id);

        $randSAData = $this->dbRandomMatching->getRecords("questionid='" . $id . "'");
        $qnData = $qnData[0];
        $randSAData = $randSAData[0];

        //Get the Qn tags
        $tagInstData = $this->dbTagInstance->getInstances($id);
        $tagStr = "";
        $count = 0;
        //Get the count of the array
        $arrLength = count($tagInstData);
        //Get each tag and store in a string for rendering on the form, comma separated
        foreach ($tagInstData as $thisTagInst) {
            $tagData = $this->dbTag->getTag($thisTagInst["tagid"]);
            $tagData = $tagData[0];
            $tagStr .= $tagData["name"];

            $count++;

            if ($count < $arrLength)
                $tagStr .= ",";
        }

        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        if (!empty($id)) {
            $objHeading->str = $phraseEditingA . " " . $phraseRandomShortAns;
        } else {
            $objHeading->str = $phraseAddingA . " " . $phraseRandomShortAns;
        }

        //Add heading/title to form
        $form->addToForm($objHeading->show());

        //Create table to hold the permissions
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //permissions listing to the table
        $objTable->startRow();
        $objTable->addCell("&nbsp;", '80%');
        $objTable->endRow();

        //Add fieldset to hold permissions listing
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        //$objFieldset->width = '800px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($phrasePermissions);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //use-category text box
        $usecategory = new checkbox('currentcategory');
        if (!empty($qnData)) {
            $usecategory->setChecked(1);
        } else {
            $usecategory->setChecked(0);
        }
        $usecategory->setValue(1);

        //Add Use-Category to the table
        /* $objTable->startRow();
          $objTable->addCell($phraseCurrentCategory, '20%');
          $objTable->addCell($usecategory->show() . " " . $phraseUseCategory, '80%');
          $objTable->endRow(); */

        //category drop down
        if (!empty($qnData)) {
            $categories = $this->dbCategory->generateDropDown($this->contextCode, $qnData["categoryid"]);
        } else {
            $categories = $this->dbCategory->generateDropDown($this->contextCode, Null);
        }
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseSaveInCategory, '20%');
        $objTable->addCell($categories, '80%');
        $objTable->endRow();

        //question name text box
        if (!empty($qnData)) {
            $qnname = new textinput("qnName", $qnData["name"]);
        } else {
            $qnname = new textinput("qnName", "");
        }
        $qnname->size = 60;
        $form->addRule('qnname', $this->objLanguage->languageText('mod_mcqtests_qnnamerequired', 'mcqtests'), 'required');
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnName, '20%');
        $objTable->addCell($qnname->show(), '80%');
        $objTable->endRow();

        //qn text
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'qntext';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        if (!empty($qnData)) {
            $qntext = $qnData["questiontext"];
        } else {
            $qntext = '';
        }
        $editor->setContent($qntext);
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnText, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //Add default qn grade
        if (!empty($qnData)) {
            $qngrade = new textinput("qngrade", $qnData["mark"]);
        } else {
            $qngrade = new textinput("qngrade", "");
        }
        $qngrade->size = 2;
        $form->addRule('qngrade', $phraseQnGrade . " " . $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests'), 'required');
        //Add qn grade to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnGrade, '20%');
        $objTable->addCell($qngrade->show(), '80%');
        $objTable->endRow();

        //Add Penalty factor
        if (!empty($qnData)) {
            $pfactor = new textinput("penaltyfactor", $qnData["penalty"]);
        } else {
            $pfactor = new textinput("penaltyfactor", "");
        }
        $pfactor->size = 2;
        $form->addRule('penaltyfactor', $phrasePenaltyFactor . " " . $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests'), 'required');
        //Add penalty factor field to the table
        $objTable->startRow();
        $objTable->addCell($phrasePenaltyFactor, '20%');
        $objTable->addCell($pfactor->show(), '80%');
        $objTable->endRow();

        //general feedback
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'genfeedback';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        if (!empty($qnData)) {
            $genfeedback = $qnData["generalfeedback"];
        } else {
            $genfeedback = '';
        }
        $editor->setContent($genfeedback);
        //Add General Feedback to the table
        $objTable->startRow();
        $objTable->addCell($wordFeedback, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        $noofqnsdropdown = new dropdown("qncount");
        $noofqnsdropdown->addOption("2", "2");
        $noofqnsdropdown->addOption("3", "3");
        $noofqnsdropdown->addOption("4", "4");
        $noofqnsdropdown->addOption("5", "5");
        $noofqnsdropdown->addOption("6", "6");
        $noofqnsdropdown->addOption("7", "7");
        $noofqnsdropdown->addOption("8", "8");
        $noofqnsdropdown->addOption("9", "9");
        $noofqnsdropdown->addOption("10", "10");
        if (!empty($randSAData)) {
            $noofqnsdropdown->setSelected($randSAData["choose"]);
        } else {
            $noofqnsdropdown->setSelected("0");
        }

        //Add Sensitivity dropdown to the table
        $objTable->startRow();
        $objTable->addCell($phraseNoQnsToSelect, '20%');
        $objTable->addCell($noofqnsdropdown->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold general stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        //$objFieldset->width = '800px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($wordGeneral);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();
        //Create table to hold the tags
        $objTable2 = new htmltable();
        $objTable2->width = '800px';
        $objTable2->attributes = " align='center' border='0'";
        $objTable2->cellspacing = '12';

        //tags text box
        $officialtags = new textinput("officialtags", "");
        $officialtags->size = 60;
        $officialtags->extra = "disabled";

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell($wordTags, '20%');
        $objTable2->addCell($phraseOfficialTags . " ( " . $phraseMngOfficialTags . " ) " . "<br />" . $officialtags->show(), '80%');
        $objTable2->endRow();

        //tags text box
        $othertags = "";
        $othertagsTA = new textarea();
        $othertagsTA->setName("othertags");
        if (!empty($tagInstData)) {
            $othertagsTA->setValue($tagStr);
        } else {
            $othertagsTA->setValue("");
        }
        $othertagsTA->setRows('4');
        $othertagsTA->setColumns('70');

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell('&nbsp;');
        $objTable2->addCell($phraseOtherTags . " ( " . $phraseOtherTagsDesc . " ) " . "<br />" . $othertagsTA->show(), '80%');
        $objTable2->endRow();

        //Add fieldset to hold tags
        $objFieldset->setLegend($wordTags);

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable2->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the tags
        $objTable3 = new htmltable();
        $objTable3->width = '800px';
        $objTable3->attributes = " align='center' border='0'";
        $objTable3->cellspacing = '12';
        //Add Last Saved to the table
        $objTable3->startRow();
        $objTable3->addCell($phraseCreated, '20%');
        if (!empty($randSAData)) {
            $objTable3->addCell($randSAData["timecreated"], '80%');
        } else {
            $objTable3->addCell("&nbsp;", '80%');
        }
        $objTable3->endRow();

        //Add Last Saved to the table
        $objTable3->startRow();
        $objTable3->addCell($phraseLastSaved, '20%');
        if (!empty($randSAData)) {
            $objTable3->addCell($randSAData["timemodified"], '80%');
        } else {
            $objTable3->addCell("&nbsp;", '80%');
        }
        $objTable3->endRow();

        //Add fieldset to hold last-saved
        $objFieldset->setLegend($phraseCreatedOrSaved);

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable3->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        // Create Save Button
        $button = new button("submit", $phraseSaveChanges);
        $button->setValue($phraseSaveChanges);
        $button->setToSubmit();
        $btnSave = $button->showSexy();

        $button1 = new button("submit", $phraseSaveAsNewQn);
        $button1->setValue($phraseSaveAsNewQn);
        $button1->setToSubmit();
        $btnSaveAsnew = $button1->showSexy();

        // Create Back to list of RSA Button
        $buttonBack = new button("submit", $listTitle);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'rsalisting'
                )));
        $objBack->link = $buttonBack->showSexy();
        $btnBackList = $objBack->show();

        // Create Back to home Button
        $buttonBack = new button("submit", $backToHome);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2'
                )));
        $objBack->link = $buttonBack->showSexy();
        $btnBackHome = $objBack->show();

        //Add Save and Cancel Buttons to form
        $form->addToForm("<br />" . $btnSave . " " . $btnSaveAsnew . " " . $btnBackList . " " . $btnBackHome . "<br />");

        return "<div>" . $form->show() . "</div>";
    }

    /**
     * Method to create a random view for random simple-calculated-questions
     *
     * @access private
     * @param array $data Contains values from controller
     * @author Paul Mungai
     */
    public function randomSCQView($data) {
        //Load classes
        $objPopup = &$this->loadClass('windowpop', 'htmlelements');
        $this->loadClass("hiddeninput", "htmlelements");

        //Initialize variables
        $test = $data['testId'];
        $qnId = $data['qnId'];
        $itemNo = $data['itemNo'];
        $unitVal = $data['unitVal'];
        $numberVal = $data['numberVal'];
        $unit = $data['unit'];
        $computed = $data['computedAns'];
        $minVal = $data['minVal'];
        $maxVal = $data['maxVal'];
        $mypenalty = $data['mypenalty'];
        $myunitpenalty = $data['myunitpenalty'];
        $submit = $data['submit'];

        //Form text
        $addSuccess = $this->objLanguage->languageText("mod_mcqtests_addsuccess", 'mcqtests', "The Record was added successfully");
        $deleteSuccess = $this->objLanguage->languageText("mod_mcqtests_deletesuccess", 'mcqtests', "The Record was deleted successfully");
        $deletefail = $this->objLanguage->languageText("mod_mcqtests_deletefail", 'mcqtests', "Ooops! There was a problem. The Record was NOT deleted successfully");
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $phraseAddA = $this->objLanguage->languageText("mod_mcqtests_phraseadda", 'mcqtests', "Add a");
        $phraseSCQuestions = $this->objLanguage->languageText("mod_mcqtests_simplecalculatedqns", 'mcqtests', "Simple Calculated Questions");
        $phraseSCQuestion = $this->objLanguage->languageText("mod_mcqtests_simplecalculatedqn", 'mcqtests', "Simple Calculated Question");
        $wordBack = $this->objLanguage->languageText("word_back", Null, "Back");
        $listTitle = $phraseListOf . " " . $phraseSCQuestions;
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $noRecords = $this->objLanguage->languageText('mod_mcqtests_norecords', 'mcqtests', "No records found");
        $addSCQ = $phraseAddA . " " . $phraseSCQuestion;
        $wordDesc = $this->objLanguage->languageText('mod_mcqtests_description', 'mcqtests', "Description");
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');
        $wordUnit = $this->objLanguage->languageText('mod_mcqtests_wordunit', 'mcqtests', "Unit");
        $wordAnswer = $this->objLanguage->languageText('mod_mcqtests_wordanswer', 'mcqtests', "Answer");
        $wordNumber = $this->objLanguage->languageText('mod_mcqtests_wordnumber', 'mcqtests', "Number");
        $wordInstructions = $this->objLanguage->languageText('mod_mcqtests_instructions', 'mcqtests', "Instructions");
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_wordfeedback', 'mcqtests', "Feedback");
        $wordSubmit = $this->objLanguage->languageText('word_submit', 'system', "Submit");
        $wordExit = $this->objLanguage->languageText('mod_systext_exit', 'system', "Exit");
        $phraseSubmitPage = $this->objLanguage->languageText('mod_mcqtests_submitpage', 'mcqtests', "Submit page");
        $phrasePrevState = $this->objLanguage->languageText('mod_mcqtests_prevstate', 'mcqtests', "Previous State");
        $phraseStartAgain = $this->objLanguage->languageText('mod_mcqtests_startagain', 'mcqtests', "Start again");
        $phraseFillWithCorrect = $this->objLanguage->languageText('mod_mcqtests_fillwithcorrect', 'mcqtests', "Fill with correct");
        $phrasePenaltyOf = $this->objLanguage->languageText('mod_mcqtests_penaltyof', 'mcqtests', "This submission attracted a penalty of");
        $wordMarks = $this->objLanguage->languageText('mod_mcqtests_wordmarks', 'mcqtests', "Marks");
        $phraseMarksForSubmission = $this->objLanguage->languageText('mod_mcqtests_marksforsubmission', 'mcqtests', "Marks for this submission");
        $phraseValidFormats = $this->objLanguage->languageText('mod_mcqtests_validformats', 'mcqtests', "Valid number formats");
        $phraseCorrectAns = $this->objLanguage->languageText('mod_mcqtests_correctans', 'mcqtests', "Correct Answer");
        $phraseIncorrectAns = $this->objLanguage->languageText('mod_mcqtests_incorrectans', 'mcqtests', "Incorrect Answer");
        $phraseInstructions = $this->objLanguage->languageText('mod_mcqtests_instructions', 'mcqtests', "Instructions");
        $phraseUndefined = $this->objLanguage->languageText('mod_mcqtests_undefinedunit', 'mcqtests', "Undefined unit");
        $phraseWithPrevPenalties = $this->objLanguage->languageText('mod_mcqtests_prevpenalties', 'mcqtests', "With previous penalties, this gives");
        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'randomscqview',
                            'test' => $test,
                            'id' => $qnId
                        )));

        //fetch question data
        if (!empty($qnId)) {
            $qn = $this->dbQuestions->getQuestion($qnId);
            $qn = $qn[0];
        } else if (!empty($test)) {
            $qn = $this->dbQuestions->getQuestions($test, "questiontype='SimpleCalculated'");
            $qn = $qn[0];
        }

        //Get qn Unit-Handling data
        $uhData = $this->objNumericalOptions->getNumericalOptions($qnId);
        $count = 1;

        //Get the answers (Use only first answer for now)
        $qnans = $this->objQnAnswers->getAnswers($qnId);
        $thisans = $qnans[0];

        //Get the calculated vals for answer 1
        $calcqnans = $this->objQuestionCalculated->getAnswerRelated($thisans['id']);
        $calcqnans = $calcqnans[0];

        $ansValues = array();
        $ansValues['ansid'] = $thisans['id'];
        $ansValues['testid'] = $thisans['testid'];
        $ansValues['questionid'] = $thisans['questionid'];
        $ansValues['answer'] = $thisans['answer'];
        $ansValues['answerformat'] = $thisans['answerformat'];
        $ansValues['fraction'] = $thisans['fraction'];
        $ansValues['feedback'] = $thisans['feedback'];
        $ansValues['feedbackformat'] = $thisans['feedbackformat'];
        $ansValues['calcid'] = $calcqnans['id'];
        $ansValues['tolerance'] = $calcqnans['tolerance'];
        $ansValues['tolerancetype'] = $calcqnans['tolerancetype'];
        $ansValues['correctanswerformat'] = $calcqnans['correctanswerformat'];
        $ansValues['correctanswerlength'] = $calcqnans['correctanswerlength'];

        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $wordDesc;
        if (empty($itemNo)) {
            //Generate no. between 1-10, get random data
            $itemnumber = rand(1, 10);
        } else {
            $itemnumber = $itemNo;
        }

        //Get test name
        $testName = $this->dbTestadmin->getTestName($test);

        //Add heading/title to string
        $str = "";

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '600px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';
        //answer text box
        if (!empty($numberVal)) {
            $ansfield = new textinput("number", $numberVal);
        } else {
            $ansfield = new textinput("number", "");
        }
        $ansfield->size = 7;
        //unit text box
        if (!empty($unitVal)) {
            $unitfield = new textinput("unit", $unitVal);
        } else {
            $unitfield = new textinput("unit", "");
        }
        $unitfield->size = 7;

        //Store the dataset item number Id
        $itemno = new hiddeninput("itemnumber", $itemnumber);
        //Store the formula
        $formula = new hiddeninput("formula", $ansValues['answer']);
        //Store the tolerance
        $tolerance = new hiddeninput("tolerance", $ansValues['tolerance']);

        $question = $qn["questiontext"];
        //Replace wild card values with Number
        $question = str_replace("{A}", $data["aVal"], $question);
        $question = str_replace("{B}", $data["bVal"], $question);

        //Add Question to the table
        $objTable->startRow();
        $objTable->addCell("<b>" . $question . "</b>" . $itemno->show() . $formula->show() . $tolerance->show(), '80%', '', '', '', 'colspan="5"');
        $objTable->endRow();
        $str .= $objTable->show();

        //Create table to hold the answer and unit
        $objTable = new htmltable();
        $objTable->width = '600px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';
        //Add Answer to the table
        $objTable->startRow();
        //$objTable->addCell($wordAnswer.": ", '20%','','left');
        $objTable->addCell($wordNumber, '50px', '', 'right');
        $objTable->addCell($ansfield->show(), '250px', '', 'left');
        $objTable->addCell($wordUnit, '50px', '', 'right');
        $objTable->addCell($unitfield->show(), '250px', 'left');
        $objTable->endRow();
        //Flag for correct answer
        $markCorrect = false;
        //Get the mark for this qn
        $qnmark = $qn['mark'];
        $themark = $qnmark;
        //Add row to show results if submitted
        if ($submit != "Fill with correct") {
            if (!empty($unitVal) || !empty($numberVal)) {
                //Get Object
                $this->objIcon = &$this->newObject('geticon', 'htmlelements');
                //Correct Icon
                $this->objIcon->setIcon('greentick', $type = 'gif', $iconfolder = 'icons/');
                $correct = $this->objIcon->show();
                //Wrong Icon
                $this->objIcon->setIcon('redcross', $type = 'gif', $iconfolder = 'icons/');
                $wrong = $this->objIcon->show();
                //Get previous penalty if any
                $mypenalty = $data['mypenalty'];
                $myunitpenalty = $data['myunitpenalty'];

                //Mark answer and penalise accordingly
                if ($numberVal >= $minVal && $numberVal <= $maxVal) {
                    $markCorrect = true;
                    $markNumber = '<div> ' . $correct . " " . $phraseCorrectAns . " ";
                } else {
                    //Compute the mark-penalty
                    $qnpenalty = $qn['penalty'];
                    if (empty($mypenalty)) {
                        $mypenalty = $qnpenalty;
                    }
                    $markCorrect = false;
                    $markNumber = '<div style="color:#FF0000"> ' . $wrong . " " . $phraseIncorrectAns . " ";
                }
                if ($unit == $unitVal) {
                    $unitCheck = '<div> ' . $correct . " ";
                } else {
                    $markCorrect = false;
                    //Compute the unit-penalty
                    $unitpenalty = $uhData[0]['unitpenalty'];
                    if (empty($myunitpenalty)) {
                        $myunitpenalty = $unitpenalty;
                    }
                    $unitCheck = '<div style="color:#FF0000"> ' . $wrong . " " . $phraseUndefined . " ";
                }
                //Compute mark
                if (!empty($mypenalty))
                    $qnmark = $qnmark - $mypenalty;
                if (!empty($myunitpenalty))
                    $qnmark = $qnmark - $myunitpenalty;
                //Store the penalty
                $mypenalties = new hiddeninput("mypenalty", $mypenalty);
                //Store the unit-penalty
                $myunitpenalties = new hiddeninput("myunitpenalty", $myunitpenalty);

                $objTable->startRow();
                //$objTable->addCell($wordAnswer.": ", '20%','','left');
                $objTable->addCell("", '50px', '', 'right');
                $objTable->addCell($markNumber . " " . $mypenalties->show(), '250px', '', 'left');
                $objTable->addCell("", '50px', '', 'right');
                $objTable->addCell($unitCheck . " " . $myunitpenalties->show(), '250px', 'left');
                $objTable->endRow();
            }
        }
        //Add fieldset to hold Answer stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '600px';

        //$objFieldset->align = 'center';
        $objFieldset->setLegend($wordAnswer);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());
        //Add General Fieldset to form
        $str .= $objFieldset->show();
        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the Instructions
        $objTable = new htmltable();
        $objTable->width = '600px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';
        //Add Answer to the table
        $objTable->startRow();
        $objTable->addCell($uhData[0]["instructions"], '600px', 'left');
        $objTable->endRow();
        //Add fieldset to hold Instructions
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '600px';

        //$objFieldset->align = 'center';
        $objFieldset->setLegend($wordInstructions);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());
        //Add General Fieldset to form
        $str .= $objFieldset->show();
        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the Feedback
        $objTable = new htmltable();
        $objTable->width = '600px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';

        //String to hold results
        $results = $phraseMarksForSubmission . $qnmark . "/" . $themark . ". ";

        $penalties = $mypenalty + $myunitpenalty;
        $checkFeedback = 0;
        //Show feedback if correct
        if ($markCorrect == true) {
            //Add Feedback to the table
            $objTable->startRow();
            $objTable->addCell($ansValues['feedback'], '600px', 'left');
            $objTable->endRow();
            if ($qnmark != $themark) {
                $results .= $phraseWithPrevPenalties . " " . $penalties . ". ";
            }
            //Add Results to the table
            $objTable->startRow();
            $objTable->addCell($results, '600px', 'left');
            $objTable->endRow();
        } else if (!empty($penalties)) {
            $results .= $phrasePenaltyOf . " " . $penalties . ". ";
            //Add Results to the table
            $objTable->startRow();
            $objTable->addCell('<div style="color:#FF0000"> ' . $results . '</div>', '600px', 'left');
            $objTable->endRow();
        }
        //Add fieldset to hold the Feedback
        if ($submit != "Fill with correct") {
            if ($markCorrect == true || !empty($penalties)) {
                $objFieldset = &$this->getObject('fieldset', 'htmlelements');
                $objFieldset->width = '600px';

                //$objFieldset->align = 'center';
                $objFieldset->setLegend($wordFeedback);

                //Add table to General Fieldset
                $objFieldset->addContent($objTable->show());
                //Add General Fieldset to form
                $str .= $objFieldset->show();
                //Reset Fieldset
                $objFieldset->reset();
            }
        }

        // Create Submit Btn
        $buttonSubmit = new button("submit", $wordSubmit);
        $buttonSubmit->setValue($wordSubmit);
        $buttonSubmit->setToSubmit();
        $str .= "<br />" . $buttonSubmit->show() . "<br /><br />";

        // Create Fill-With-Correct Btn
        $buttonSubmit = new button("submit", $phraseFillWithCorrect);
        $buttonSubmit->setValue($phraseFillWithCorrect);
        $buttonSubmit->setToSubmit();
        $str .= " " . $buttonSubmit->show();

        // Create Start-again Btn
        $buttonSubmit = new button("submit", $phraseStartAgain);
        $buttonSubmit->setValue($phraseStartAgain);
        $buttonSubmit->setToSubmit();
        $str .= " " . $buttonSubmit->show();

        // Create close button
        $buttonBack = new button("submit", $wordExit, "window.close()");
        $str .= " " . $buttonBack->showSexy() . " <br /><br />";

        //$objFieldset->align = 'center';
        $objFieldset->setLegend($testName);

        //Add table to General Fieldset
        $objFieldset->addContent($str);
        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        //Reset Fieldset
        $objFieldset->reset();

        return $form->show();
    }

    /**
     * Method to create a list of random simple-calculated-questions
     *
     * @access private
     * @param  string $contextCode Contains the context code
     * @param  string $categoryId Contains the category identifier
     * @author Paul Mungai
     */
    public function createSCQList($testId, $categoryId=Null, $deletemsg = Null, $addmsg = Null) {
        //Load classes
        $objPopup = &$this->loadClass('windowpop', 'htmlelements');

        //Initialize variables
        $test = $testId;

        //Form text
        $addSuccess = $this->objLanguage->languageText("mod_mcqtests_addsuccess", 'mcqtests', "The Record was added successfully");
        $deleteSuccess = $this->objLanguage->languageText("mod_mcqtests_deletesuccess", 'mcqtests', "The Record was deleted successfully");
        $deletefail = $this->objLanguage->languageText("mod_mcqtests_deletefail", 'mcqtests', "Ooops! There was a problem. The Record was NOT deleted successfully");
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $phraseAddA = $this->objLanguage->languageText("mod_mcqtests_phraseadda", 'mcqtests', "Add a");
        $phraseSCQuestions = $this->objLanguage->languageText("mod_mcqtests_simplecalculatedqns", 'mcqtests', "Simple Calculated Questions");
        $phraseSCQuestion = $this->objLanguage->languageText("mod_mcqtests_simplecalculatedqn", 'mcqtests', "Simple Calculated Question");
        $wordBack = $this->objLanguage->languageText("word_back", Null, "Back");
        $listTitle = $phraseListOf . " " . $phraseSCQuestions;
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $noRecords = $this->objLanguage->languageText('mod_mcqtests_norecords', 'mcqtests', "No records found");
        $addSCQ = $phraseAddA . " " . $phraseSCQuestion;
        $wordDesc = $this->objLanguage->languageText('mod_mcqtests_description', 'mcqtests', "Description");
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');

        if (!empty($categoryId)) {
            //$data = $this->dbQuestions->getDescriptions($categoryId);
        } else {
            $data = $this->dbQuestions->getQuestions($testId, "questiontype='SimpleCalculated'");
        }
        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $wordDesc;

        //Add heading/title to string
        $str = "";

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '600px';
        $objTable->border = '0';
        $objTable->attributes = " align='left' border='0'";
        $objTable->cellspacing = '12';

        //Show confirm message when deleted
        echo '<div id="confirmationmessage">';
        if ($deletemsg == "deletesuccess") {
            echo '<br /><span class="confirm">' . $deleteSuccess . '</span><br /><br />';
        }
        if ($addmsg == "addsuccess") {
            echo '<br /><span class="confirm">' . $addSuccess . '</span><br /><br />';
        }
        echo "</div>";
        //Javascript to control the display-time of the confirm message
        echo '
    <script type="text/javascript">

    function hideConfirmation()
    {
        document.getElementById(\'confirmationmessage\').style.display="none";
    }

    setTimeout("hideConfirmation()", 10000);
    </script>
    ';
        //Add Heading to the table
        $objTable->startRow();
        $objTable->addCell("<b>" . $phraseQnName . "</b>", '80%');
        $objTable->addCell(Null, '20%');
        $objTable->endRow();

        //question name text box
        if (!empty($data)) {
            foreach ($data as $descdata) {
                if ($descdata['qtype'] == "SimpleCalculated") {
                    // Show the edit link
                    $iconEdit = $this->getObject('geticon', 'htmlelements');
                    $iconEdit->setIcon('edit');
                    $iconEdit->alt = $this->objLanguage->languageText("word_edit", 'system');
                    $iconEdit->align = false;
                    $objLink = &$this->getObject("link", "htmlelements");
                    $objLink->link($this->uri(array(
                                'module' => 'mcqtests',
                                'action' => 'addsimplecalculated',
                                'id' => $descdata['id'],
                                'test' => $testId
                            )));
                    $objLink->link = $iconEdit->show();
                    $linkEdit = $objLink->show();
                    // Show the delete link
                    $iconDelete = $this->getObject('geticon', 'htmlelements');
                    $iconDelete->setIcon('delete');
                    $iconDelete->alt = $this->objLanguage->languageText("word_delete", 'system');
                    $iconDelete->align = false;
                    $objConfirm = &$this->getObject("link", "htmlelements");
                    $objConfirm = &$this->newObject('confirm', 'utilities');
                    $objConfirm->setConfirm($iconDelete->show(), $this->uri(array(
                                'module' => 'mcqtests',
                                'action' => 'deletescq',
                                'test' => $testId,
                                'id' => $descdata["id"]
                            )), $this->objLanguage->languageText('mod_mcqtests_deletetest', 'mcqtests') . "?");
                    // Show the edit link
                    $iconPreview = $this->getObject('geticon', 'htmlelements');
                    $iconPreview->setIcon('preview');
                    $iconPreview->alt = $this->objLanguage->languageText("word_preview", 'system');
                    $iconPreview->align = false;
                    //Show the view Icon
                    $objPopup = new windowpop();
                    $objPopup->set('location', $this->uri(array(
                                'action' => 'randomscqview',
                                'id' => $descdata['id'],
                                'test' => $testId
                                    ), 'mcqtests'));
                    $objPopup->set('linktext', $iconPreview->show());
                    $objPopup->set('width', '700');
                    $objPopup->set('height', '650');
                    $objPopup->set('left', '200');
                    $objPopup->set('top', '200');
                    $objPopup->set('scrollbars', 'yes');
                    $objPopup->set('resizable', 'yes');
                    $objPopup->putJs(); // you only need to do this once per page
                    $linkPreview = $objPopup->show();
                    $objTable->startRow();
                    $objTable->addCell($descdata['question'], '80%');
                    $objTable->addCell($linkEdit . "&nbsp;&nbsp;" . $objConfirm->show() . "&nbsp;&nbsp;" . $linkPreview, '20%');
                    $objTable->endRow();
                }
            }
        } else {
            $objTable->startRow();
            $objTable->addCell($noRecords, '80%', $valign = "top", $align = 'left', $class = null, $attrib = "colspan='2'", $border = '0');
            $objTable->endRow();
        }
        $str .= $objTable->show();

        // Create Back Button
        $buttonAdd = new button("submit", $addSCQ);
        $objAdd = &$this->getObject("link", "htmlelements");
        $objAdd->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'addsimplecalculated',
                    'test' => $test
                )));
        $objAdd->link = $buttonAdd->showSexy();
        $str .= " " . $objAdd->show();

        // Create Back Button
        $buttonBack = new button("submit", $backToHome);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2',
                    'test' => $testId
                )));
        $objBack->link = $buttonBack->showSexy();
        $str .= " " . $objBack->show();

        //Add fieldset to hold Descriptions stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->width = '600px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($listTitle);

        //Add table to General Fieldset
        $objFieldset->addContent($str);
        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'mcqlisting',
                            'test' => $testId
                        )));
        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();
        return $form->show();
    }

    /**
     * Method to create Simple Calculation Question form
     *
     * @access private
     * @param  array $test Contains test data
     * @param  string $id Contains the answer id
     * @author Paul Mungai
     */
    public function createSimpleCalcQnForm($fields) {
        //Load Classes
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textarea", "htmlelements");
        $this->loadClass("dropdown", "htmlelements");
        $this->loadClass("checkbox", "htmlelements");
        $this->loadClass("hiddeninput", "htmlelements");
        $this->loadClass("radio", "htmlelements");

        //Store values in variables
        $test = $fields['testid'];
        if (empty($test))
            $test = $this->getParam("test", Null);

        $id = $fields['id'];
        $anscount = $fields['anscount'];
        $unitcount = $fields['unitcount'];
        $mode = $fields['mode'];
        //Form texts
        $phraseWildCardValues = $this->objLanguage->languageText("mod_mcqtests_wcardvalues", 'mcqtests', "outside limits of true value");
        $phraseOutsideLimits = $this->objLanguage->languageText("mod_mcqtests_outsidelimits", 'mcqtests', "outside limits of true value");
        $phraseWithinLimits = $this->objLanguage->languageText("mod_mcqtests_withinlimits", 'mcqtests', "within limits of true value");
        $phraseCorrectAns = $this->objLanguage->languageText("mod_mcqtests_correctans", 'mcqtests', "Correct answer");
        $wordSet = $this->objLanguage->languageText("mod_mcqtests_wordset", 'mcqtests', "Set");
        $wordMax = $this->objLanguage->languageText("mod_mcqtests_wordmax", 'mcqtests', "Max");
        $wordMin = $this->objLanguage->languageText("mod_mcqtests_wordmin", 'mcqtests', "Min");
        $phraseListOf = $this->objLanguage->languageText("mod_mcqtests_listof", 'mcqtests', "List of");
        $wordTo = $this->objLanguage->languageText("mod_mcqtests_wordto", 'mcqtests', "to");
        $wordError = $this->objLanguage->languageText("mod_mcqtests_worderror", 'mcqtests', "Error");
        $wordBack = $this->objLanguage->languageText("word_back");
        $BackToList = $wordBack . " " . $wordTo . " " . $phraseListOf . " ";
        $mcqHome = $this->objLanguage->languageText("mod_mcqtests_mcqhome", "mcqtests", "MCQ Home");
        $backToHome = $wordBack . " " . $wordTo . " " . $mcqHome;
        $addDescform = $this->objLanguage->languageText('mod_mcqtests_addDescription', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');
        $phraseQnText = $this->objLanguage->languageText('mod_mcqtests_qntext', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');
        $wordTags = $this->objLanguage->languageText('mod_mcqtests_wordtags', 'mcqtests');
        $phraseOfficialTags = $this->objLanguage->languageText('mod_mcqtests_officialtags', 'mcqtests');
        $phraseMngOfficialTags = $this->objLanguage->languageText('mod_mcqtests_mngofficialtags', 'mcqtests');
        $phraseOtherTags = $this->objLanguage->languageText('mod_mcqtests_othertags', 'mcqtests');
        $phraseOtherTagsDesc = $this->objLanguage->languageText('mod_mcqtests_othertagsdesc', 'mcqtests');
        $phraseWordGrade = $this->objLanguage->languageText('mod_mcqtests_wordgrade', 'mcqtests');
        $phraseQnGrade = $this->objLanguage->languageText('mod_mcqtests_defaultQnGrade', 'mcqtests');
        $phrasePenaltyFactor = $this->objLanguage->languageText('mod_mcqtests_penaltyfactor', 'mcqtests');
        $phraseNoQnsToSelect = $this->objLanguage->languageText('mod_mcqtests_noqnstoselect', 'mcqtests');
        $phraseSaveInCategory = $this->objLanguage->languageText('mod_mcqtests_saveincategory', 'mcqtests');
        $phraseCurrentCategory = $this->objLanguage->languageText('mod_mcqtests_currentcategory', 'mcqtests');
        $phraseUseCategory = $this->objLanguage->languageText('mod_mcqtests_usecategory', 'mcqtests');
        $phraseSimpleCalcQn = $this->objLanguage->languageText('mod_mcqtests_simplecalculatedqn', 'mcqtests');
        $phraseAddingA = $this->objLanguage->languageText('mod_mcqtests_addinga', 'mcqtests');
        $phraseEditingA = $this->objLanguage->languageText('mod_mcqtests_editinga', 'mcqtests');
        $phraseLastSaved = $this->objLanguage->languageText('mod_mcqtests_lastsaved', 'mcqtests');
        $phraseCreatedOrSaved = $this->objLanguage->languageText('mod_mcqtests_createdorsaved', 'mcqtests');
        $phraseCreated = $this->objLanguage->languageText('mod_mcqtests_created', 'mcqtests');
        $phrasePermissions = $this->objLanguage->languageText("mod_mcqtests_permissionsto", 'mcqtests');
        $phraseSaveChanges = $this->objLanguage->languageText("mod_mcqtests_savechanges", 'mcqtests', "Save changes");
        $phraseSaveAsNewQn = $this->objLanguage->languageText("mod_mcqtests_saveasnewqn", 'mcqtests', "Save as a new question");
        $phraseSCQuestions = $this->objLanguage->languageText("mod_mcqtests_simplecalculatedqn", 'mcqtests', "Simple Calculated Question");
        $phraseAddBlankAnswers = $this->objLanguage->languageText("mod_mcqtests_addblankanswers", 'mcqtests', "Select the number of blank answers to add");
        $phraseAddBlankUnits = $this->objLanguage->languageText("mod_mcqtests_addblankunits", 'mcqtests', "Select the number of blank units to add");
        $wordAnswer = $this->objLanguage->languageText('mod_mcqtests_wordanswer', 'mcqtests', "Answer");
        $phraseCorrectAnsFormula = $this->objLanguage->languageText('mod_mcqtests_corranswerlabel', 'mcqtests', "Correct Answer Formula");
        $phraseIsRequired = $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests', "is required");
        $phraseTolerance = $this->objLanguage->languageText('mod_mcqtest_tolerancelabel', 'mcqtests', "Tolerance ±");
        $phraseToleranceType = $this->objLanguage->languageText('mod_mcqtest_tolerancetype', 'mcqtests', "Tolerance type");
        $phraseCorrectAnswerShows = $this->objLanguage->languageText('mod_mcqtest_correctAnswerShows', 'mcqtests', "Correct answer shows");
        $wordFormat = $this->objLanguage->languageText('mod_mcqtests_formatlabel', 'mcqtests', "Format");
        $wordUnit = $this->objLanguage->languageText('mod_mcqtests_wordunit', 'mcqtests', "Unit");
        $wordMultiplier = $this->objLanguage->languageText('mod_mcqtest_wordmultiplier', 'mcqtests', "Multiplier");
        $wordGenerate = $this->objLanguage->languageText('mod_mcqtests_wordgenerate', 'mcqtests', "Generate");
        $wordDisplay = $this->objLanguage->languageText('mod_mcqtest_worddisplay', 'mcqtests', "Display");
        $phraseGenerate = $this->objLanguage->languageText('mod_mcqtests_newsetwildcards', 'mcqtests', "new set(s) of wild card(s) values");
        $phraseDisplay = $this->objLanguage->languageText('mod_mcqtest_setwildcards', 'mcqtests', "set(s) of wild card(s) values");
        $phraseQuestionBank = $this->objLanguage->languageText('mod_mcqtest_questionbank', 'mcqtests', "Question bank");
        $phraseBackToQnBank = $this->objLanguage->languageText('mod_mcqtest_backtoqnbank', 'mcqtests', "Back to question bank");


        $listTitle = $phraseListOf . " " . $phraseSCQuestions;
        //Form Object
        $form = new form("addsimplecalculated", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'addsimplecalculated',
                            'id' => $id,
                            'test' => $test
                        )));

        $qnData = $this->dbQuestions->getQuestion($id);

        $randSAData = $this->dbRandomMatching->getRecords("questionid='" . $id . "'");
        $qnData = $qnData[0];
        $randSAData = $randSAData[0];
        //Get the Qn tags
        $tagInstData = $this->dbTagInstance->getInstances($id);
        $tagStr = "";
        $count = 0;
        //Get the count of the array
        $arrLength = count($tagInstData);
        //Get each tag and store in a string for rendering on the form, comma separated
        if (!empty($tagInstData)) {
            foreach ($tagInstData as $thisTagInst) {
                $tagData = $this->dbTag->getTag($thisTagInst["tagid"]);
                $tagData = $tagData[0];
                $tagStr .= $tagData["name"];

                $count++;

                if ($count < $arrLength)
                    $tagStr .= ",";
            }
        }
        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        if (!empty($id)) {
            $objHeading->str = $phraseEditingA . " " . $phraseSimpleCalcQn;
        } else {
            $objHeading->str = $phraseAddingA . " " . $phraseSimpleCalcQn;
        }

        //Add heading/title to form
        $form->addToForm($objHeading->show());

        //Create table to hold the permissions
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //permissions listing to the table
        $objTable->startRow();
        $objTable->addCell("&nbsp;", '80%');
        $objTable->endRow();

        //Add fieldset to hold permissions listing
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        //$objFieldset->width = '800px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($phrasePermissions);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //use-category text box
        $usecategory = new checkbox('currentcategory');
        if (!empty($qnData)) {
            $usecategory->setChecked(1);
        } else {
            $usecategory->setChecked(0);
        }
        $usecategory->setValue(1);

        //Add Use-Category to the table
        //category drop down
        if (!empty($qnData)) {
            $categories = $this->dbCategory->generateDropDown($this->contextCode, $qnData["categoryid"]);
        } else {
            $categories = $this->dbCategory->generateDropDown($this->contextCode, Null);
        }
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseSaveInCategory, '20%');
        $objTable->addCell($categories, '80%');
        $objTable->endRow();

        //question name text box
        if (!empty($qnData)) {
            $qnname = new textinput("qnName", $qnData["name"]);
        } else {
            $qnname = new textinput("qnName", "");
        }
        $qnname->size = 60;
        $form->addRule('qnname', $this->objLanguage->languageText('mod_mcqtests_qnnamerequired', 'mcqtests'), 'required');
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnName, '20%');
        $objTable->addCell($qnname->show(), '80%');
        $objTable->endRow();

        //qn text
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'qntext';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        if (!empty($qnData)) {
            $qntext = $qnData["questiontext"];
        } else {
            $qntext = '';
        }
        $editor->setContent($qntext);
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnText, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //Add default qn grade
        if (!empty($qnData)) {
            $qngrade = new textinput("qngrade", $qnData["mark"]);
        } else {
            $qngrade = new textinput("qngrade", "");
        }
        $qngrade->size = 2;
        $form->addRule('qngrade', $phraseQnGrade . " " . $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests'), 'required');
        //Add qn grade to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnGrade, '20%');
        $objTable->addCell($qngrade->show(), '80%');
        $objTable->endRow();

        //Add Penalty factor
        if (!empty($qnData)) {
            $pfactor = new textinput("penaltyfactor", $qnData["penalty"]);
        } else {
            $pfactor = new textinput("penaltyfactor", "");
        }
        $pfactor->size = 2;
        $form->addRule('penaltyfactor', $phrasePenaltyFactor . " " . $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests'), 'required');
        //Add penalty factor field to the table
        $objTable->startRow();
        $objTable->addCell($phrasePenaltyFactor, '20%');
        $objTable->addCell($pfactor->show(), '80%');
        $objTable->endRow();

        //general feedback
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'genfeedback';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        if (!empty($qnData)) {
            $genfeedback = $qnData["generalfeedback"];
        } else {
            $genfeedback = '';
        }
        $editor->setContent($genfeedback);
        //Add General Feedback to the table
        $objTable->startRow();
        $objTable->addCell($wordFeedback, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold general stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->setLegend($wordGeneral);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();

        //Create dynamic answers
        $count = 0;
        if (!empty($id)) {
            $count = 1;
            //Get the answers
            $qnans = $this->objQnAnswers->getAnswers($id);
            foreach ($qnans as $thisans) {
                $calcqnans = $this->objQuestionCalculated->getAnswerRelated($thisans['id']);
                $calcqnans = $calcqnans[0];

                $ansValues = array();
                $ansValues['ansid'] = $thisans['id'];
                $ansValues['testid'] = $thisans['testid'];
                $ansValues['questionid'] = $thisans['questionid'];
                $ansValues['answer'] = $thisans['answer'];
                $ansValues['answerformat'] = $thisans['answerformat'];
                $ansValues['fraction'] = $thisans['fraction'];
                $ansValues['feedback'] = $thisans['feedback'];
                $ansValues['feedbackformat'] = $thisans['feedbackformat'];
                $ansValues['calcid'] = $calcqnans['id'];
                $ansValues['tolerance'] = $calcqnans['tolerance'];
                $ansValues['tolerancetype'] = $calcqnans['tolerancetype'];
                $ansValues['correctanswerformat'] = $calcqnans['correctanswerformat'];
                $ansValues['correctanswerlength'] = $calcqnans['correctanswerlength'];

                $ans = $this->createAnswerFields("_update_" . $count, $ansValues);

                //Add form validations
                $form->addRule('ansformula' . "_update_" . $count, $phraseCorrectAnsFormula . " " . $count . " " . $phraseIsRequired, 'required');
                $form->addRule('grade' . "_update_" . $count, $phraseWordGrade . " " . $count . " " . $phraseIsRequired, 'required');
                $form->addRule('tolerance' . "_update_" . $count, $phraseTolerance . " " . $count . " " . $phraseIsRequired, 'required');
                $form->addRule('feedback' . "_update_" . $count, $phraseTolerance . " " . $count . " " . $phraseIsRequired, 'required');
                //Add Answer Fieldset to form
                $form->addToForm($ans);
                $count++;
            }
        }
        //Store no of updated fields
        $updateanscount = new hiddeninput("updateanscount", $count);

        //Set variable
        $anscount = 0;

        //Set count to add to default ans fields if not an update with answers
        if ($count == 0) {
            //Variable to store no of answer fieldsets to create
            $anscount = 2;
            $count = 1;
        }

        //Fetch no of ans fields to add
        if (!empty($fields['anscount'])) {
            $anscount = $fields['anscount'];
            $count = 1;
        }
        if ($anscount != 0) {
            do {
                $ans = $this->createAnswerFields($count, $ansValues = Null);
                $count++;
                //Add form validations
                $form->addRule('ansformula' . $count, $phraseCorrectAnsFormula . " " . $count . " " . $phraseIsRequired, 'required');
                $form->addRule('grade' . $count, $phraseWordGrade . " " . $count . " " . $phraseIsRequired, 'required');
                $form->addRule('tolerance' . $count, $phraseTolerance . " " . $count . " " . $phraseIsRequired, 'required');
                $form->addRule('feedback' . $count, $phraseTolerance . " " . $count . " " . $phraseIsRequired, 'required');
                //Add Answer Fieldset to form
                $form->addToForm($ans);
            } while ($count <= $anscount);
        }
        //Create table to store no of answers dropdown
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        $noofansdropdown = new dropdown("anscount");
        $noofansdropdown->extra = "onchange='createAnsInputs(this)'";
        $noofansdropdown->addOption("0", "0");
        $noofansdropdown->addOption("1", "1");
        $noofansdropdown->addOption("2", "2");
        $noofansdropdown->addOption("3", "3");
        $noofansdropdown->addOption("4", "4");
        $noofansdropdown->addOption("5", "5");
        $noofansdropdown->addOption("6", "6");
        $noofansdropdown->addOption("7", "7");
        $noofansdropdown->addOption("8", "8");
        $noofansdropdown->addOption("9", "9");
        $noofansdropdown->addOption("10", "10");
        if (!empty($randSAData)) {
            $noofansdropdown->setSelected($randSAData["choose"]);
        } else {
            $noofansdropdown->setSelected("0");
        }

        $frmanscount = new hiddeninput("frmanscount", $anscount);
        //Add Answers dropdown to the table
        $objTable->startRow();
        $objTable->addCell($phraseAddBlankAnswers, '20%');
        $objTable->addCell($noofansdropdown->show() . $frmanscount->show() . $updateanscount->show(), '80%');
        $objTable->endRow();

        //Add table to form
        $form->addToForm($objTable->show());

        //Load unit-handling
        //Get Values if edit
        $unitValues = Null;
        if (!empty($id)) {
            $unitValues = array();
            $qnid = $this->getParam("id", Null);
            $uh = $this->objNumericalOptions->getNumericalOptions($qnid);
            if (!empty($uh)) {
                $unitValues = $uh[0];
            } else {
                $unitValues = Null;
            }
        }
        $unitHandling = $this->createUnitHandlingFields($unitValues);

        //Add unit-handling to form
        $form->addToForm($unitHandling);
        //Get Values if edit
        $unitValues = Null;
        $upcount = 0;
        $upcounter = 0;
        if (!empty($id)) {
            $uh = $this->objNumericalUnit->getNumericalUnits($id);

            $upcount = 1;
            $upcounter = 0;
            //No need for multiple unit multipliers, if needed, uncomment and comment code below
            foreach ($uh as $thisUh) {
                $unitMultiplier = $this->createUnitMultiplierFields("_update_" . $upcount, $thisUh, $multiplier = Null);
                //Add unit-Multiplier to form
                $form->addToForm($unitMultiplier);

                //Increment counters
                $upcounter++;
                $upcount++;
            }
        } else {
            //Load default unit-Multiplier
            $unitMultiplier = $this->createUnitMultiplierFields(1, $unitValues = Null, $multiplier = 1);
            //Add unit-Multiplier to form
            $form->addToForm($unitMultiplier);
        }
        //Load other unit-Multiplier        
        $unitcount = $fields['unitcount'];
        //Get the param if empty in array
        if (empty($unitcount))
            $unitcount = $this->getParam("unitcount", Null);
        //Default to 1 unit if array and getParam fetch nothing
        if (empty($unitcount) && $upcounter == 0)
            $unitcount = 1;

        $ucount = 0;
        if ($unitcount >= 1) {
            $ucount = 1;
            do {
                $unitMultiplier = $this->createUnitMultiplierFields($ucount, $unitValues = Null, Null);
                //Add form validations
                $form->addRule('unit' . $ucount, $wordUnit . " " . $ucount . " " . $phraseIsRequired, 'required');
                $form->addRule('multiplier' . $ucount, $wordMultiplier . " " . $ucount . " " . $phraseIsRequired, 'required');
                //Add Answer Fieldset to form
                $form->addToForm($unitMultiplier);
                $ucount++;
            } while ($ucount <= $unitcount);
        }

        //Create table to store unit-handling
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //Create table to store no of answers dropdown
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        $noofunitsdropdown = new dropdown("unitcount");
        $noofunitsdropdown->extra = "onchange='createUnitInputs(this)'";
        $noofunitsdropdown->addOption("0", "0");
        $noofunitsdropdown->addOption("1", "1");
        $noofunitsdropdown->addOption("2", "2");
        $noofunitsdropdown->addOption("3", "3");
        $noofunitsdropdown->addOption("4", "4");
        $noofunitsdropdown->addOption("5", "5");
        $noofunitsdropdown->addOption("6", "6");
        $noofunitsdropdown->addOption("7", "7");
        $noofunitsdropdown->addOption("8", "8");
        $noofunitsdropdown->addOption("9", "9");
        $noofunitsdropdown->addOption("10", "10");
        if (!empty($randSAData)) {
            $noofunitsdropdown->setSelected($randSAData["choose"]);
        } else {
            $noofunitsdropdown->setSelected("0");
        }
        //Store no of unit-multipliers
        $frmunitcount = new hiddeninput("frmunitcount", $ucount);
        $frmupdateunitcount = new hiddeninput("frmupunitcount", $upcount);

        //Add Units dropdown to the table
        $objTable->startRow();
        $objTable->addCell($phraseAddBlankUnits, '20%');
        $objTable->addCell($noofunitsdropdown->show() . $frmunitcount->show() . $frmupdateunitcount->show(), '80%');
        $objTable->endRow();

        //Add table to form
        $form->addToForm($objTable->show());

        //Get Wild-Card fields
        //Store wild-card Id
        $wcardno = 1;

        $wc_count = new hiddeninput("wccount", $wcardno);


        $wcardValues = Null;
        //Get Values
        if (!empty($id)) {
            //Get id of the datasets affiliated to this question
            $qnid = $this->getParam("id", Null);
            $datasets = $this->objDBDataset->getRecords($qnid);
            $datasetid = $datasets[0]['id'];

            if (!empty($datasetid)) {
                //get the related definitions
                $dataset_definitions = $this->objDSDefinitions->getRecords($datasetid);
                //Populate values in array
                if (!empty($dataset_definitions)) {
                    $wcardValues = array();
                    $wcardValues['dsetid'] = $datasetid;
                    if ($dataset_definitions[0]["name"] == "A") {
                        $wcardValues['a_definition_id'] = $dataset_definitions[0]['id'];
                        $dsetDefA = $dataset_definitions[0]['id'];
                        $dsetDefB = $dataset_definitions[1]['id'];
                        //get min, max and decimal values from string
                        $stuff = explode(":", $dataset_definitions[0]["options"]);
                        //Get min and decimal
                        $min_dec = explode(".", $stuff[0]);
                        //Get max and decimal
                        $max_dec = explode(".", $stuff[1]);
                        $wcardValues['a_fromrange'] = $min_dec[0];
                        $wcardValues['a_torange'] = $max_dec[0];
                        $wcardValues['a_decimal'] = $max_dec[1];

                        //Get Values for B
                        //get min, max and decimal values from string
                        $stuff = explode(":", $dataset_definitions[1]["options"]);
                        //Get min and decimal
                        $min_dec = explode(".", $stuff[0]);
                        //Get max and decimal
                        $max_dec = explode(".", $stuff[1]);
                        $wcardValues['b_definition_id'] = $dataset_definitions[1]['id'];
                        $wcardValues['b_fromrange'] = $min_dec[0];
                        $wcardValues['b_torange'] = $max_dec[0];
                        $wcardValues['b_decimal'] = $max_dec[1];
                    } elseif ($dataset_definitions[0]["name"] == "B") {
                        $wcardValues['b_definition_id'] = $dataset_definitions[0]['id'];
                        $dsetDefB = $dataset_definitions[0]['id'];
                        $dsetDefA = $dataset_definitions[1]['id'];
                        //Get Values for B
                        //get min, max and decimal values from string
                        $stuff = explode(":", $dataset_definitions[0]["options"]);
                        //Get min and decimal
                        $min_dec = explode(".", $stuff[0]);
                        //Get max and decimal
                        $max_dec = explode(".", $stuff[1]);
                        $wcardValues['b_fromrange'] = $min_dec[0];
                        $wcardValues['b_torange'] = $max_dec[0];
                        $wcardValues['b_decimal'] = $max_dec[1];

                        //Get Values for A
                        //get min, max and decimal values from string
                        $stuff = explode(":", $dataset_definitions[1]["options"]);
                        //Get min and decimal
                        $min_dec = explode(".", $stuff[0]);
                        //Get max and decimal
                        $max_dec = explode(".", $stuff[1]);
                        $wcardValues['a_definition_id'] = $dataset_definitions[1]['id'];
                        $wcardValues['a_fromrange'] = $min_dec[0];
                        $wcardValues['a_torange'] = $max_dec[0];
                        $wcardValues['a_decimal'] = $max_dec[1];
                    }
                }
            }
        }
        $wcards = $this->createWildCardFields($wcardno, $wcardValues);

        //Add Wild-card to form
        $form->addToForm($wcards . " " . $wc_count->show());

        //Add Generate new set of wildcard values
        $genwcard = new dropdown("genwcards");
        $genwcard->addOption("0", "0");
        $genwcard->addOption("1", "1");
        $genwcard->addOption("2", "2");
        $genwcard->addOption("3", "3");
        $genwcard->addOption("4", "4");
        $genwcard->addOption("5", "5");
        $genwcard->addOption("6", "6");
        $genwcard->addOption("7", "7");
        $genwcard->addOption("8", "8");
        $genwcard->addOption("9", "9");
        $genwcard->addOption("10", "10");
        if (!empty($fields["genwcards"])) {
            $genwcard->setSelected($fields["genwcards"]);
        }

        //Add Display new set of wildcard values
        $displaywcard = new dropdown("dispwcards");
        $displaywcard->addOption("0", "0");
        $displaywcard->addOption("1", "1");
        $displaywcard->addOption("2", "2");
        $displaywcard->addOption("3", "3");
        $displaywcard->addOption("4", "4");
        $displaywcard->addOption("5", "5");
        $displaywcard->addOption("6", "6");
        $displaywcard->addOption("7", "7");
        $displaywcard->addOption("8", "8");
        $displaywcard->addOption("9", "9");
        $displaywcard->addOption("10", "10");
        if (!empty($fields["dispwcards"])) {
            $displaywcard->setSelected($fields["dispwcards"]);
        }

        //Create table to hold the wildcards
        $objTableX = new htmltable();
        $objTableX->width = '800px';
        $objTableX->attributes = " align='center' border='0'";
        $objTableX->cellspacing = '12';
        //Button Generate
        $buttonX = new button("submit", $wordGenerate);
        $buttonX->setValue($wordGenerate);
        $buttonX->setToSubmit();
        $btnGenerate = $buttonX->showSexy();

        //Add Generate Wildcards to the table
        $objTableX->startRow();
        $objTableX->addCell($btnGenerate . " " . $genwcard->show() . " " . $phraseGenerate, '', '', '', '', 'colspan=2');
        $objTableX->endRow();

        //Button Generate
        $buttonX = new button("submit", $wordDisplay);
        $buttonX->setValue($wordDisplay);
        $buttonX->setToSubmit();
        $btnDisplay = $buttonX->showSexy();

        //Add Display Wildcards to the table
        $objTableX->startRow();
        $objTableX->addCell($btnDisplay . " " . $displaywcard->show() . " " . $phraseDisplay, '', '', '', '', 'colspan=2');
        $objTableX->endRow();

        //Add Wild-card to form
        $form->addToForm($objTableX->show());

        //Create table to display the wildcards
        $objTableY = new htmltable();
        $objTableY->width = '800px';
        $objTableY->attributes = " align='center' border='0'";
        $objTableY->cellspacing = '12';
        $wcardcount = 1;

        //Set the number of wild card(s) values to display based on the highest
        if ($fields["genwcards"] > $fields["dispwcards"]) {
            $wcardtotal = $fields["genwcards"];
        } elseif (!empty($fields["dispwcards"])) {
            $wcardtotal = $fields["dispwcards"];
        } else {
            $wcardtotal = 10;
        }

        while ($wcardcount <= $wcardtotal) {
            //Create dynamic answers
            $count = 0;
            if (!empty($id)) {
                $count = 1;
                //Get the answers
                $qnans = $this->objQnAnswers->getAnswers($id);
                foreach ($qnans as $thisans) {
                    $calcqnans = $this->objQuestionCalculated->getAnswerRelated($thisans['id']);
                    $calcqnans = $calcqnans[0];

                    $ansValues = array();
                    $ansValues['ansid'] = $thisans['id'];
                    $ansValues['testid'] = $thisans['testid'];
                    $ansValues['questionid'] = $thisans['questionid'];
                    $ansValues['answer'] = $thisans['answer'];
                    $ansValues['answerformat'] = $thisans['answerformat'];
                    $ansValues['fraction'] = $thisans['fraction'];
                    $ansValues['feedback'] = $thisans['feedback'];
                    $ansValues['feedbackformat'] = $thisans['feedbackformat'];
                    $ansValues['calcid'] = $calcqnans['id'];
                    $ansValues['tolerance'] = $calcqnans['tolerance'];
                    $ansValues['tolerancetype'] = $calcqnans['tolerancetype'];
                    $ansValues['correctanswerformat'] = $calcqnans['correctanswerformat'];
                    $ansValues['correctanswerlength'] = $calcqnans['correctanswerlength'];

                    //Fetch the Values for A and B
                    $unitDataA = $this->objDSItems->getRecords($dsetDefA, $filter = "itemnumber='" . $wcardcount . "'");
                    $no1 = $unitDataA[0]["value"];
                    $unitDataB = $this->objDSItems->getRecords($dsetDefB, $filter = "itemnumber='" . $wcardcount . "'");
                    $no2 = $unitDataB[0]["value"];

                    //Array to store data to be computed
                    $computeData = array();
                    $computeData["aVal"] = $no1;
                    $computeData["bVal"] = $no2;
                    $computeData["formula"] = $ansValues['answer'];
                    $computeData["tolerance"] = $ansValues['tolerance'];
                    $computeData["unit"] = $uh[0]["unit"];
                    //Compute the Vals
                    $newVals = $this->computeMaxMinVals($computeData);

                    $minVal = $newVals["minVal"];
                    $maxVal = $newVals["maxVal"];
                    $tolerance = $newVals["tolerance"];
                    $computed = $newVals["computedAns"];
                    $roundAns = $newVals["roundedAns"];
                    $myformula = $newVals["formula"];
                    $unit = $newVals["unit"];

                    //Mark Correct
                    $markCorrect = false;
                    $phraseLimits = "";
                    if ($roundAns >= $minVal || $roundAns <= $maxVal) {
                        $markCorrect = true;
                        $phraseLimits = $phraseWithinLimits;
                        $rowAttributes = '<div> ' . $phraseCorrectAns . " ";
                    } else {
                        $markCorrect = false;
                        $phraseLimits = $phraseOutsideLimits;
                        $rowAttributes = '<div style="color:#FF0000"> ' . $wordError . " " . $phraseCorrectAns . " ";
                    }
                    //Render results in a table row
                    $objTableY->startRow();
                    if ($count == 1) {
                        $objTableY->addCell("<b>" . $wordSet . " " . $wcardcount . "</b> ", "", "top", "right");
                    } else {
                        $objTableY->addCell("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
                    }
                    $objTableY->addCell($ansValues['answer']);
                    //Change min and max vals to float
                    $minVal = floatval($minVal);
                    $maxVal = floatval($maxVal);

                    //End row holding the set values
                    $objTableY->addCell($myformula . " = " . $roundAns . " " . $unit . "<br />" . $rowAttributes . $phraseLimits . " " . $computed . " " . $unit
                            . "</div>" . "Min: " . $minVal . " Max: " . $maxVal);
                    $objTableY->endRow();
                    $count++;
                }
            }
            $wcardcount++;
        }

        //Add fieldset to hold wild card(s) values
        $objFieldset->setLegend($phraseWildCardValues);

        //Add table to Fieldset
        $objFieldset->addContent($objTableY->show());

        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the tags
        $objTable2 = new htmltable();
        $objTable2->width = '800px';
        $objTable2->attributes = " align='center' border='0'";
        $objTable2->cellspacing = '12';

        //tags text box
        $officialtags = new textinput("officialtags", "");
        $officialtags->size = 60;
        $officialtags->extra = "disabled";

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell($wordTags, '20%');
        $objTable2->addCell($phraseOfficialTags . " ( " . $phraseMngOfficialTags . " ) " . "<br />" . $officialtags->show(), '80%');
        $objTable2->endRow();

        //tags text box
        $othertags = "";
        $othertagsTA = new textarea();
        $othertagsTA->setName("othertags");
        if (!empty($tagInstData)) {
            $othertagsTA->setValue($tagStr);
        } else {
            $othertagsTA->setValue("");
        }
        $othertagsTA->setRows('4');
        $othertagsTA->setColumns('70');

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell('&nbsp;');
        $objTable2->addCell($phraseOtherTags . " ( " . $phraseOtherTagsDesc . " ) " . "<br />" . $othertagsTA->show(), '80%');
        $objTable2->endRow();

        //Add fieldset to hold tags
        $objFieldset->setLegend($wordTags);

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable2->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the tags
        $objTable3 = new htmltable();
        $objTable3->width = '800px';
        $objTable3->attributes = " align='center' border='0'";
        $objTable3->cellspacing = '12';
        //Add Last Saved to the table
        $objTable3->startRow();
        $objTable3->addCell($phraseCreated, '20%');
        if (!empty($randSAData)) {
            $objTable3->addCell($randSAData["timecreated"], '80%');
        } else {
            $objTable3->addCell("&nbsp;", '80%');
        }
        $objTable3->endRow();

        //Add Last Saved to the table
        $objTable3->startRow();
        $objTable3->addCell($phraseLastSaved, '20%');
        if (!empty($randSAData)) {
            $objTable3->addCell($randSAData["timemodified"], '80%');
        } else {
            $objTable3->addCell("&nbsp;", '80%');
        }
        $objTable3->endRow();

        //Add fieldset to hold last-saved
        $objFieldset->setLegend($phraseCreatedOrSaved);

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable3->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        // Create Save Button
        $button = new button("submit", $phraseSaveChanges);
        $button->setValue($phraseSaveChanges);
        $button->setToSubmit();
        $btnSave = $button->showSexy();

        $button1 = new button("submit", $phraseSaveAsNewQn);
        $button1->setValue($phraseSaveAsNewQn);
        $button1->setToSubmit();
        $btnSaveAsnew = $button1->showSexy();

        // Create Back to list of SCQ Button
        $buttonBack = new button("submit", $listTitle);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'scqlisting',
                    'test' => $fields["testid"]
                )));
        $objBack->link = $buttonBack->showSexy();
        $btnBackList = $objBack->show();

        // Create Back to list of SCQ Button
        $buttonBack = new button("submit", $phraseBackToQnBank);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'questionbank',
                    'test' => $fields["testid"]
                )));
        $objBack->link = $buttonBack->showSexy();
        $btnBackQBank = $objBack->show();

        // Create Back to home Button
        $buttonBack = new button("submit", $backToHome);
        $objBack = &$this->getObject("link", "htmlelements");
        $objBack->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view2',
                    'test' => $fields["testid"]
                )));
        $objBack->link = $buttonBack->showSexy();
        $btnBackHome = $objBack->show();

        //Add Save and Cancel Buttons to form
        $form->addToForm("<br />" . $btnSave . " " . $btnSaveAsnew . " " . $btnBackQBank . " " . $btnBackHome . "<br />");

        return "<div>" . $form->show() . "</div>";
    }

    /**
     * Method to compute max
     *
     * @access public
     * @param  $data array The values for to be computed and tolerance..all not null
     * @return object
     * @author Paul Mungai
     */
    public function computeMaxMinVals($data) {
        $no1 = $data['aVal'];
        $no2 = $data['bVal'];
        $newFormula = $data['formula'];
        $newTolerance = $data['tolerance'];
        //Add values to wildcards
        $newFormula = str_replace("A", floatval($no1), $newFormula);
        $newFormula = str_replace("B", floatval($no2), $newFormula);

        // remove any non-numbers chars; exception for math operators
        $myformula = ereg_replace('[^0-9\+-\*\/\(\) ]', '', $newFormula);
        $compute = create_function("", "return (" . $myformula . ");");
        $computed = 0 + $compute();
        $roundAns = (float) round($computed, 0);

        /*
         * COMPUTE THE TOLERANCE
         * Use Nominal -for add(+) operation -- mark correct if dx < t
         * Example:
         * With value 200 and tolerance level of 0.5
         * Upper Limit => 200+0.5 = 200.5
         * Lower Limit => 200-0.5 = 199.5
         * Use Relative - for multiplication, division and subtraction operations
         * Example:
         * With value 200 and tolerance level of 0.5
         * Upper Limit => 200+(200*0.5)
         * Lower Limit => 200-(200*0.5)
         */
        //check if using add operation
        $addPos = strpos($myformula, "+");
        if ($addPos === false) {
            //Compute the minimal tolerance value
            $minVal = $computed - $newTolerance;

            // remove any non-numbers chars; exception for math operators
            $minVal = ereg_replace('[^0-9\+-\*\/\(\) ]', '', $minVal);
            $minVal = create_function("", "return (" . $minVal . ");");
            $minVal = 0 + $minVal();

            //Compute the maximum tolerance value
            $maxVal = $computed + $newTolerance;

            // remove any non-numbers chars; exception for math operators
            $maxVal = ereg_replace('[^0-9\+-\*\/\(\) ]', '', $maxVal);
            $maxVal = create_function("", "return (" . $maxVal . ");");
            $maxVal = 0 + $maxVal();
        } else {
            //Correct answer * tolerance value
            $tolerance = $computed * $newTolerance;

            //Compute the minimal tolerance value
            $minVal = $computed - $tolerance;

            // remove any non-numbers chars; exception for math operators
            $minVal = ereg_replace('[^0-9\+-\*\/\(\) ]', '', $minVal);
            $minVal = create_function("", "return (" . $minVal . ");");
            $minVal = 0 + $minVal();

            //Compute the maximum tolerance value
            $maxVal = $computed + $tolerance;

            // remove any non-numbers chars; exception for math operators
            $maxVal = ereg_replace('[^0-9\+-\*\/\(\) ]', '', $maxVal);
            $maxVal = create_function("", "return (" . $maxVal . ");");
            $maxVal = 0 + $maxVal();
        }
        //Array to store data to return
        $newData = array();
        $newData["minVal"] = $minVal;
        $newData["maxVal"] = $maxVal;
        $newData["tolerance"] = $newTolerance;
        $newData["computedAns"] = $computed;
        $newData["roundedAns"] = $roundAns;
        $newData["formula"] = $myformula;
        $newData["unit"] = $data['unit'];
        return $newData;
    }

    /**
     * Method to create a fieldset for capturing unit-handling details
     *
     * @access public
     * @param  $unitValues array The values for the fields for the case of an edit
     * @return object
     * @author Paul Mungai
     */
    public function createUnitHandlingFields($unitValues=Null) {
        //Load Classes
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textarea", "htmlelements");
        $this->loadClass("dropdown", "htmlelements");
        $this->loadClass("checkbox", "htmlelements");
        $this->loadClass("hiddeninput", "htmlelements");
        $this->loadClass("radio", "htmlelements");

        //Get the language text
        $phraseUnitGraded = $this->objLanguage->languageText('mod_mcqtests_unitgraded', 'mcqtests', 'Unit graded');
        $phraseUnitHandling = $this->objLanguage->languageText('mod_mcqtests_unithandling', 'mcqtests', 'Unit handling');
        $phraseNumericalUnit = $this->objLanguage->languageText('mod_mcqtests_numericalunitgraded', 'mcqtests', "NUMERICAL ANSWER and UNIT ANSWER will be graded");
        $phrasePenalty = $this->objLanguage->languageText('mod_mcqtests_penaltybadunit', 'mcqtests', "Penalty for bad unit");
        $phraseUnitAnswer = $this->objLanguage->languageText('mod_mcqtests_unitanswerdisplay', 'mcqtests', "UNIT ANSWER displayed as a");
        $phraseTextInputElement = $this->objLanguage->languageText('mod_mcqtests_textinputelement', 'mcqtests', "Text input element");
        $wordMultichoice = $this->objLanguage->languageText('mod_mcqtests_multichoice', 'mcqtests', "Multichoice");
        $phraseRadioElements = $this->objLanguage->languageText('mod_mcqtests_radioelements', 'mcqtests', "Radio elements");
        $wordInstructions = $this->objLanguage->languageText('mod_mcqtests_instructions', 'mcqtests', 'Instructions');
        $phraseUnitNotGraded = $this->objLanguage->languageText('mod_mcqtests_unitnotgraded', 'mcqtests', "Unit not graded");
        $phraseOnlyNumerical = $this->objLanguage->languageText('mod_mcqtests_onlynumerical', 'mcqtests', "Only NUMERICAL ANSWER will be graded");
        $wordNo = $this->objLanguage->languageText('word_no', 'system', 'No');
        $wordYes = $this->objLanguage->languageText('word_yes', 'system', 'Yes');
        $phraseUnitPosition = $this->objLanguage->languageText('mod_mcqtests_unitposition', 'mcqtests', "Unit position");
        $phrasePenaltyResponseGrade = $this->objLanguage->languageText('mod_mcqtests_penaltyresponsegrade', 'mcqtests', "as a decimal fraction (0-1) of RESPONSE grade");
        $phrasePenaltyQuestionGrade = $this->objLanguage->languageText('mod_mcqtests_penaltyquestiongrade', 'mcqtests', "as a decimal fraction (0-1) of QUESTION grade");
        $wordOr = $this->objLanguage->languageText('mod_mcqtests_wordor', 'mcqtests', 'or');
        $phraseDisplayUnit = $this->objLanguage->languageText('mod_mcqtests_displayunit', 'mcqtests', 'Display Unit');

        //Create table to hold the answer
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';
        //unit-graded radio button
        $unitgraded = new radio("unitgradetype");
        $unitgraded->addOption(1, $phraseNumericalUnit);
        if (!empty($unitValues)) {
            $unitgraded->setSelected($unitValues["unitgradingtype"]);
        }
        //Add field to the table
        $objTable->startRow();
        $objTable->addCell($phraseUnitGraded, '20%');
        $objTable->addCell($unitgraded->show(), '80%');
        $objTable->endRow();

        //penalty-bad-unit text box
        if (!empty($unitValues)) {
            $penaltybadunit = new textinput("unitpenalty", $unitValues["unitpenalty"]);
        } else {
            $penaltybadunit = new textinput("unitpenalty", "");
        }
        $penaltybadunit->size = 9;
        //Dropdown - Penalty on question on response grade
        $questionresponseddown = new dropdown("questionresponse");
        $questionresponseddown->addOption("question", $phrasePenaltyQuestionGrade);
        $questionresponseddown->addOption("response", $phrasePenaltyResponseGrade);
        if (!empty($unitValues)) {
            //$questionresponseddown->setSelected($unitValues["questionresponse"]);
        } else {
            $questionresponseddown->setSelected("0");
        }
        //Store numericaloptions Id
        $uhfield = new hiddeninput("uhid", $unitValues["id"]);
        //Add penalty-bad-unit to the table
        $objTable->startRow();
        $objTable->addCell($phrasePenalty, '20%');
        $objTable->addCell($penaltybadunit->show() . " " . $questionresponseddown->show() . $uhfield->show(), '80%');
        $objTable->endRow();

        //unit-answer-display radio button
        $unitansdisplay = new radio("instructionsformat");
        $unitansdisplay->addOption('1', $phraseTextInputElement . " " . strtoupper($wordOr));
        $unitansdisplay->addOption('2', $wordMultichoice . " (" . $phraseRadioElements . ")");
        if (!empty($unitValues)) {
            $unitansdisplay->setSelected($unitValues["instructionsformat"]);
        }
        //Add field to the table
        $objTable->startRow();
        $objTable->addCell($phraseUnitGraded, '20%');
        $objTable->addCell($unitansdisplay->show(), '80%');
        $objTable->endRow();

        //Instructions
        $instruction = $this->newObject('htmlarea', 'htmlelements');
        $instruction->name = 'instructions';
        $instruction->height = '100px';
        $instruction->width = '550px';
        if (!empty($unitValues["instructions"])) {
            $instruction->setContent($unitValues["instructions"]);
        }
        $instruction->setMCQToolBar();
        //Add instructions to the table
        $objTable->startRow();
        $objTable->addCell($wordInstructions, '20%');
        $objTable->addCell($instruction->show(), '80%');
        $objTable->endRow();

        //Dropdown - UNIT-NOT-GRADED
        $unitnotgradeddropdown = new radio("unitgradingtype");
        $unitnotgradeddropdown->addOption(1, $phraseOnlyNumerical);
        if (!empty($unitValues)) {
            $unitnotgradeddropdown->setSelected($unitValues["unitgradingtype"]);
        } else {
            $unitnotgradeddropdown->setSelected("0");
        }
        //Add UNIT-NOT-GRADED to the table
        $objTable->startRow();
        $objTable->addCell($phraseUnitNotGraded, '20%');
        $objTable->addCell($unitnotgradeddropdown->show(), '80%');
        $objTable->endRow();

        //Dropdown - DISPLAY-UNIT
        $displayunitdropdown = new radio("showunits");
        $displayunitdropdown->addOption('1', $wordYes);
        $displayunitdropdown->addOption('0', $wordNo);
        if (!empty($unitValues)) {
            $displayunitdropdown->setSelected($unitValues["showunits"]);
        } else {
            $displayunitdropdown->setSelected("0");
        }
        //Add DISPLAY-UNIT to the table
        $objTable->startRow();
        $objTable->addCell($phraseDisplayUnit . "1", '20%');
        $objTable->addCell($displayunitdropdown->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold table
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->setLegend($phraseUnitHandling);
        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        $theFieldset = $objFieldset->show();

        $objFieldset->reset();

        return $theFieldset;
    }

    /**
     * Method to create a fieldset for capturing unit multiplier fields
     *
     * @access public
     * @param  $ansno string Unique identifier for the answer as there are several per question
     * @param  $ansValues array The values for the fields for the case of an edit
     * @return object
     * @author Paul Mungai
     */
    public function createUnitMultiplierFields($unitno=1, $unitValues=Null, $multiplier=Null) {
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("dropdown", "htmlelements");
        $this->loadClass("hiddeninput", "htmlelements");

        //Get the language text
        $wordUnit = $this->objLanguage->languageText('mod_mcqtests_wordunit', 'mcqtests', "Unit");
        $wordMultiplier = $this->objLanguage->languageText('mod_mcqtest_wordmultiplier', 'mcqtests', "Multiplier");
        $phraseMultiplier1 = $this->objLanguage->languageText('mod_mcqtests_multiplierPhrase1', 'mcqtests', "The multiplier is the factor by which the correct numerical response will be multiplied.");
        $phraseMultiplier2 = $this->objLanguage->languageText('mod_mcqtests_multiplierPhrase2', 'mcqtests', "first unit (Unit 1) has a default multiplier of 1. Thus if the correct numerical response is 5500 and you set W as unit at Unit 1 which has 1 as default multiplier, the correct response is 5500 W.");
        $phraseMultiplier3 = $this->objLanguage->languageText('mod_mcqtests_multiplierPhrase3', 'mcqtests', "If you add the unit kW with a multiplier of 0.001, this will add a correct response of 5.5 kW. This means that the answers 5500W or 5.5kW would be marked correct.");
        $phraseMultiplier4 = $this->objLanguage->languageText('mod_mcqtests_multiplierPhrase4', 'mcqtests', "Note that the accepted error is also multiplied, so an allowed error of 100W would become an error of 0.1kW.");
        $phraseIsRequired = $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests', "is required");

        $phraseCombined = $phraseMultiplier1 . " <br />" . $phraseMultiplier2 . " <br />" . $phraseMultiplier3 . " <br />" . $phraseMultiplier4 . " <br />";

        //Create table to hold the unit
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //unit text box
        if (!empty($unitValues)) {
            $unitfield = new textinput("unit" . $unitno, $unitValues["unit"]);
        } else {
            $unitfield = new textinput("unit" . $unitno, "");
        }
        $unitfield->size = 7;
        //Store numericaloptions Id
        $uhfield = new hiddeninput("utid" . $unitno, $unitValues["id"]);
        //Add Unit to the table
        $objTable->startRow();
        $objTable->addCell($wordUnit, '20%');
        $objTable->addCell($unitfield->show() . $uhfield->show(), '80%');
        $objTable->endRow();

        //Dont display textfield if $multiplier is not empty
        if (!empty($multiplier)) {
            $multiplierfield = new hiddeninput("multiplier" . $unitno, $multiplier);
            $multiplierfield->size = 7;
            //Add Multiplier to the table
            $objTable->startRow();
            $objTable->addCell($wordMultiplier, '20%');
            $objTable->addCell($multiplier . " " . $multiplierfield->show(), '80%');
            $objTable->endRow();
        } else {
            //multiplier text box
            if (!empty($unitValues)) {
                $multiplierfield = new textinput("multiplier" . $unitno, $unitValues["multiplier"]);
            } else {
                $multiplierfield = new textinput("multiplier" . $unitno, "");
            }
            $multiplierfield->size = 7;
            //Add Multiplier to the table
            $objTable->startRow();
            $objTable->addCell($wordMultiplier, '20%');
            $objTable->addCell($multiplierfield->show(), '80%');
            $objTable->endRow();
        }

        //Add fieldset to hold answer
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        if (!empty($unitValues["id"])) {
            $uno = explode("_update_", $unitno);
            $objFieldset->setLegend($wordUnit . " " . $uno["1"]);
        } else {
            $objFieldset->setLegend($wordUnit . " " . $unitno);
        }

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        $theFieldset = $objFieldset->show();

        $objFieldset->reset();

        //Return Fieldset
        return $theFieldset;
    }

    /**
     * Method to create a fieldset for capturing wild-card fields
     *
     * @access public
     * @param  $ansno string Unique identifier for the answer as there are several per question
     * @param  $ansValues array The values for the fields for the case of an edit
     * @return object
     * @author Paul Mungai
     */
    public function createWildCardFields($wcardno=1, $wcardValues=Null) {
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("dropdown", "htmlelements");
        $this->loadClass("hiddeninput", "htmlelements");

        //Get the language text
        $wordParam = $this->objLanguage->languageText('mod_mcqtests_wordparam', 'mcqtests', "Param");
        $letterA = $this->objLanguage->languageText('mod_mcqtests_lettera', 'mcqtests', "A");
        $letterB = $this->objLanguage->languageText('mod_mcqtests_letterb', 'mcqtests', "B");
        $phraseWildCardParams = $this->objLanguage->languageText('mod_mcqtest_wildcardparams', 'mcqtests', "Wild cards parameters used to generate the values");
        $phraseRangeOfValues = $this->objLanguage->languageText('mod_mcqtests_rangeofvals', 'mcqtests', "Range of values");
        $phraseDecimalPlaces = $this->objLanguage->languageText('mod_mcqtests_decimalplaces', 'mcqtests', "Decimal places");
        $phraseParamA = $wordParam . " <b>{" . $letterA . "}</b> ";
        $phraseParamB = $wordParam . " <b>{" . $letterB . "}</b> ";

        //Create table to hold the wild-card
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //Store wild-card Id
        $dsetid = new hiddeninput("dsetid_" . $wcardno, $wcardValues["dsetid"]);
        $a_definition_id = new hiddeninput("a_definition_id_" . $wcardno, $wcardValues["a_definition_id"]);
        $b_definition_id = new hiddeninput("b_definition_id_" . $wcardno, $wcardValues["b_definition_id"]);
        $wcfields = $dsetid->show() . " " . $a_definition_id->show() . " " . $b_definition_id->show();
        //range-value-a text box
        if (!empty($wcardValues)) {
            $afromrangefield = new textinput("afromrange_" . $wcardno, $wcardValues["a_fromrange"]);
        } else {
            $afromrangefield = new textinput("afromrange_" . $wcardno, "");
        }
        //decimal-value-a text box
        if (!empty($wcardValues)) {
            $atorangefield = new textinput("atorange_" . $wcardno, $wcardValues["a_torange"]);
        } else {
            $atorangefield = new textinput("atorange_" . $wcardno, "");
        }
        $afromrangefield->size = 15;
        $atorangefield->size = 15;
        //a-decimal-places drop down list
        $adecimalplaces = new dropdown("adecimalplaces_" . $wcardno);
        $adecimalplaces->addOption("0", "0");
        $adecimalplaces->addOption("1", "1");
        $adecimalplaces->addOption("2", "2");
        $adecimalplaces->addOption("3", "3");
        $adecimalplaces->addOption("4", "4");
        $adecimalplaces->addOption("5", "5");
        $adecimalplaces->addOption("6", "6");
        $adecimalplaces->addOption("7", "7");
        $adecimalplaces->addOption("8", "8");
        $adecimalplaces->addOption("9", "9");
        $adecimalplaces->addOption("10", "10");
        if (!empty($wcardValues)) {
            $adecimalplaces->setSelected($wcardValues["a_decimal"]);
        }

        //Add param-a-fields to the table
        $objTable->startRow();
        $objTable->addCell($phraseParamA, '20%');
        $objTable->addCell($wcfields, '80%');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($phraseRangeOfValues, '20%');
        $objTable->addCell($afromrangefield->show() . " - " . $atorangefield->show(), '80%');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($phraseDecimalPlaces, '20%');
        $objTable->addCell($adecimalplaces->show(), '80%');
        $objTable->endRow();

        //range-value-b text box
        if (!empty($wcardValues)) {
            $bfromrangefield = new textinput("bfromrange_" . $wcardno, $wcardValues["b_fromrange"]);
        } else {
            $bfromrangefield = new textinput("bfromrange_" . $wcardno, "");
        }
        //decimal-value-b text box
        if (!empty($wcardValues)) {
            $btorangefield = new textinput("btorange_" . $wcardno, $wcardValues["b_torange"]);
        } else {
            $btorangefield = new textinput("btorange_" . $wcardno, "");
        }
        $bfromrangefield->size = 15;
        $btorangefield->size = 15;

        //b-decimal-places drop down list
        $bdecimalplaces = new dropdown("bdecimalplaces_" . $wcardno);
        $bdecimalplaces->addOption("0", "0");
        $bdecimalplaces->addOption("1", "1");
        $bdecimalplaces->addOption("2", "2");
        $bdecimalplaces->addOption("3", "3");
        $bdecimalplaces->addOption("4", "4");
        $bdecimalplaces->addOption("5", "5");
        $bdecimalplaces->addOption("6", "6");
        $bdecimalplaces->addOption("7", "7");
        $bdecimalplaces->addOption("8", "8");
        $bdecimalplaces->addOption("9", "9");
        $bdecimalplaces->addOption("10", "10");
        if (!empty($wcardValues)) {
            $bdecimalplaces->setSelected($wcardValues["b_decimal"]);
        }

        //Add param-b-fields to the table
        $objTable->startRow();
        $objTable->addCell($phraseParamB, '20%');
        $objTable->addCell("&nbsp;", '80%');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($phraseRangeOfValues, '20%');
        $objTable->addCell($bfromrangefield->show() . " - " . $btorangefield->show(), '80%');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($phraseDecimalPlaces, '20%');
        $objTable->addCell($bdecimalplaces->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold wild-card
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->setLegend($phraseWildCardParams);

        //Add table to wild-card Fieldset
        $objFieldset->addContent($objTable->show());

        $theFieldset = $objFieldset->show();

        $objFieldset->reset();

        //Return Fieldset
        return $theFieldset;
    }

    /**
     * Method to create a fieldset for capturing answer details
     *
     * @access public
     * @param  $ansno string Unique identifier for the answer as there are several per question
     * @param  $ansValues array The values for the fields for the case of an edit
     * @return object
     * @author Paul Mungai
     */
    public function createAnswerFields($ansno=1, $ansValues=Null) {
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("dropdown", "htmlelements");
        $this->loadClass("hiddeninput", "htmlelements");
        if (!empty($ansValues)) {
            $uniqueno = explode("_update_", $ansno);
            $uniqueno = $uniqueno['1'];
        } else {
            $uniqueno = $ansno;
        }
        //Get the language text
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');
        $wordFormat = $this->objLanguage->languageText('mod_mcqtests_formatlabel', 'mcqtests', "Format");
        $wordAnswer = $this->objLanguage->languageText('mod_mcqtests_wordanswer', 'mcqtests', "Answer");
        $phraseCorrectAnswerShows = $this->objLanguage->languageText('mod_mcqtest_correctAnswerShows', 'mcqtests', "Correct answer shows");
        $phraseToleranceType = $this->objLanguage->languageText('mod_mcqtests_tolerancetype', 'mcqtests', "Tolerance type");
        $phraseTolerance = $this->objLanguage->languageText('mod_mcqtests_tolerancelabel', 'mcqtests', "Tolerance ±");
        $phraseWordGrade = $this->objLanguage->languageText('mod_mcqtests_wordgrade', 'mcqtests');
        $phraseCorrectAnsFormula = $this->objLanguage->languageText('mod_mcqtests_corranswerlabel', 'mcqtests', "Correct Answer Formula");
        $phraseIsRequired = $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests', "is required");
        //Create table to hold the answer
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //correct answer formula text box
        if (!empty($ansValues)) {
            $ansformula = new textinput("ansformula" . $ansno, $ansValues["answer"]);
        } else {
            $ansformula = new textinput("ansformula" . $ansno, "");
        }
        $ansformula->size = 60;
        //Add Answer-Formula to the table
        $objTable->startRow();
        $objTable->addCell($phraseCorrectAnsFormula . " = ", '20%');
        $objTable->addCell($ansformula->show(), '80%');
        $objTable->endRow();

        //grade text box
        if (!empty($ansValues)) {
            $grade = new textinput("grade" . $ansno, $ansValues["fraction"]);
        } else {
            $grade = new textinput("grade" . $ansno, "");
        }
        $grade->size = 7;
        //Add grade to the table
        $objTable->startRow();
        $objTable->addCell($phraseWordGrade, '20%');
        $objTable->addCell($grade->show() . " %", '80%');
        $objTable->endRow();

        //qncalcid text box
        if (!empty($ansValues)) {
            $qncalcid = new hiddeninput("qncalcid" . $ansno, $ansValues["calcid"]);
        } else {
            $qncalcid = new hiddeninput("qncalcid" . $ansno, "");
        }
        //ansid text box
        if (!empty($ansValues)) {
            $ansid = new hiddeninput("ansid" . $ansno, $ansValues["ansid"]);
        } else {
            $ansid = new hiddeninput("ansid" . $ansno, "");
        }
        //tolerance text box
        if (!empty($ansValues)) {
            $tolerance = new textinput("tolerance" . $ansno, $ansValues["tolerance"]);
        } else {
            $tolerance = new textinput("tolerance" . $ansno, "");
        }
        $tolerance->size = 7;
        //Add tolerance to the table
        $objTable->startRow();
        $objTable->addCell($phraseTolerance, '20%');
        $objTable->addCell($tolerance->show() . $qncalcid->show() . $ansid->show(), '80%');
        $objTable->endRow();

        //tolerance type text box
        $tolerancetype = new dropdown("tolerancetype" . $ansno);
        $tolerancetype->addOption("Relative", "Relative");
        $tolerancetype->addOption("Nominal", "Nominal");
        if (!empty($ansValues)) {
            $tolerancetype->setSelected($ansValues["tolerancetype"]);
        }
        //Add tolerance-type to the table
        $objTable->startRow();
        $objTable->addCell($phraseToleranceType, '20%');
        $objTable->addCell($tolerancetype->show(), '80%');
        $objTable->endRow();

        //correct-answer-shows text box
        $correctAnswerShows = new dropdown("correctanswerlength" . $ansno);
        $correctAnswerShows->addOption("0", "0");
        $correctAnswerShows->addOption("1", "1");
        $correctAnswerShows->addOption("2", "2");
        $correctAnswerShows->addOption("3", "3");
        $correctAnswerShows->addOption("4", "4");
        $correctAnswerShows->addOption("5", "5");
        $correctAnswerShows->addOption("6", "6");
        $correctAnswerShows->addOption("7", "7");
        $correctAnswerShows->addOption("8", "8");
        $correctAnswerShows->addOption("9", "9");
        if (!empty($ansValues)) {
            $correctAnswerShows->setSelected($ansValues["correctanswerlength"]);
        }
        //Add correct-answer-shows to the table
        $objTable->startRow();
        $objTable->addCell($phraseCorrectAnswerShows, '20%');
        $objTable->addCell($correctAnswerShows->show(), '80%');
        $objTable->endRow();

        //format text box
        $format = new dropdown("correctanswerformat" . $ansno);
        $format->addOption("Decimals", "Decimals");
        $format->addOption("Significant-values", "Significant values");
        if (!empty($ansValues)) {
            $format->setSelected($ansValues["correctanswerformat"]);
        }
        //Add format to the table
        $objTable->startRow();
        $objTable->addCell($wordFormat, '20%');
        $objTable->addCell($format->show(), '80%');
        $objTable->endRow();

        //Feedback
        $feedback = $this->newObject('htmlarea', 'htmlelements');
        $feedback->name = 'feedback' . $ansno;
        $feedback->height = '100px';
        $feedback->width = '550px';
        $feedback->setMCQToolBar();
        if (!empty($ansValues)) {
            $genfeedback = $ansValues["feedback"];
        } else {
            $genfeedback = '';
        }
        $feedback->setContent($genfeedback);
        //Add Feedback to the table
        $objTable->startRow();
        $objTable->addCell($wordFeedback, '20%');
        $objTable->addCell($feedback->show(), '80%');
        $objTable->endRow();

        //format text box
        $fbformat = new dropdown("feedbackformat" . $ansno);
        $fbformat->addOption("ht", "HTML Format");
        if (!empty($ansValues)) {
            $fbformat->setSelected($ansValues["feedbackformat"]);
        }
        //Add format to the table
        $objTable->startRow();
        $objTable->addCell("&nbsp;", '20%');
        $objTable->addCell($fbformat->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold answer
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        $objFieldset->setLegend($wordAnswer . " " . $uniqueno);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        $theFieldset = $objFieldset->show();

        $objFieldset->reset();

        //Return Fieldset
        return $theFieldset;
    }

    /**
     * Method to create add short answer form
     *
     * @access public
     * @param  array $test Contains test data
     * @param  string $id Contains the answer id
     * @author Paul Mungai
     */
    public function createSimpleCalculatedQnForm($test, $id, $answerCount=2, $unitCount=1) {
        //Load Classes
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textarea", "htmlelements");
        $this->loadClass("dropdown", "htmlelements");
        $this->loadClass("checkbox", "htmlelements");
        $this->loadClass("radio", "htmlelements");

        //Form texts
        $simplecalcqn = $this->objLanguage->languageText('mod_mcqtests_simplecalcqn', 'mcqtests');
        $wordCategory = $this->objLanguage->languageText('mod_mcqtests_wordcategory', 'mcqtests');
        $wordGeneral = $this->objLanguage->languageText('mod_mcqtests_wordgeneral', 'mcqtests');
        $phraseQnName = $this->objLanguage->languageText('mod_mcqtests_qnname', 'mcqtests');
        $phraseQnText = $this->objLanguage->languageText('mod_mcqtests_qntext', 'mcqtests');
        $wordFeedback = $this->objLanguage->languageText('mod_mcqtests_generalfeedback', 'mcqtests');
        $wordTags = $this->objLanguage->languageText('mod_mcqtests_wordtags', 'mcqtests');
        $phraseOfficialTags = $this->objLanguage->languageText('mod_mcqtests_officialtags', 'mcqtests');
        $phraseMngOfficialTags = $this->objLanguage->languageText('mod_mcqtests_mngofficialtags', 'mcqtests');
        $phraseOtherTags = $this->objLanguage->languageText('mod_mcqtests_othertags', 'mcqtests');
        $phraseOtherTagsDesc = $this->objLanguage->languageText('mod_mcqtests_othertagsdesc', 'mcqtests');
        $phraseQnGrade = $this->objLanguage->languageText('mod_mcqtests_defaultQnGrade', 'mcqtests');
        $phrasePenaltyFactor = $this->objLanguage->languageText('mod_mcqtests_penaltyfactor', 'mcqtests');
        $phraseNoQnsToSelect = $this->objLanguage->languageText('mod_mcqtests_noqnstoselect', 'mcqtests');
        $phraseSaveInCategory = $this->objLanguage->languageText('mod_mcqtests_saveincategory', 'mcqtests');
        $phraseCurrentCategory = $this->objLanguage->languageText('mod_mcqtests_currentcategory', 'mcqtests');
        $phraseUseCategory = $this->objLanguage->languageText('mod_mcqtests_usecategory', 'mcqtests');
        $phraseRandomShortAns = $this->objLanguage->languageText('mod_mcqtests_randomshortans', 'mcqtests');
        $phraseAddingA = $this->objLanguage->languageText('mod_mcqtests_addinga', 'mcqtests');
        $phraseEditingA = $this->objLanguage->languageText('mod_mcqtests_editinga', 'mcqtests');
        $phraseLastSaved = $this->objLanguage->languageText('mod_mcqtests_lastsaved', 'mcqtests');
        $phraseCreatedOrSaved = $this->objLanguage->languageText('mod_mcqtests_createdorsaved', 'mcqtests');
        $phraseCreated = $this->objLanguage->languageText('mod_mcqtests_created', 'mcqtests');
        $phrasePermissions = $this->objLanguage->languageText("mod_mcqtests_permissionsto", 'mcqtests');
        $phraseSaveChanges = $this->objLanguage->languageText("mod_mcqtests_savechanges", 'mcqtests');
        $phraseSaveAsNewQn = $this->objLanguage->languageText("mod_mcqtests_saveasnewqn", 'mcqtests');
        $phraseCorrectAnsFormula = $this->objLanguage->languageText("mod_mcqtest_corranswerlabel", 'mcqtests');
        $wordNone = $this->objLanguage->languageText("word_none", 'system');
        $wordGrade = $this->objLanguage->languageText("mod_mcqtests_wordgrade", 'mcqtests');
        $phraseTolerance = $this->objLanguage->languageText("mod_mcqtest_tolerancelabel", 'mcqtests');
        $wordRelative = $this->objLanguage->languageText("mod_mcqtest_relative", 'mcqtests');
        $wordNominal = $this->objLanguage->languageText("mod_mcqtest_nominal", 'mcqtests');
        $wordNominal = $this->objLanguage->languageText("mod_mcqtest_nominal", 'mcqtests');
        $phraseCorrectAnsShows = $this->objLanguage->languageText("mod_mcqtests_correctAnswerShows", 'mcqtests');
        $phraseToleranceType = $this->objLanguage->languageText("mod_mcqtests_tolerancetype", 'mcqtests');
        $wordDecimals = $this->objLanguage->languageText("mod_mcqtests_decimals", 'mcqtests');
        $phraseSigFigs = $this->objLanguage->languageText("mod_mcqtests_sigfigs", 'mcqtests');
        $wordFormat = $this->objLanguage->languageText("mod_mcqtests_formatlabel", 'mcqtests');

        //Form Object
        $form = new form("adddescription", $this->uri(array(
                            'module' => 'mcqtest',
                            'action' => 'addrandomshortansconfirm',
                            'id' => $id,
                            'test' => $test
                        )));

        //Form Heading/Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $phraseAddingA . " " . $simplecalcqn;

        //Add heading/title to form
        $form->addToForm($objHeading->show());

        //Create table to hold the permissions
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';

        //permissions listing to the table
        $objTable->startRow();
        $objTable->addCell("&nbsp;", '80%');
        $objTable->endRow();

        //Add fieldset to hold permissions listing
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        //$objFieldset->width = '800px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($phrasePermissions);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the general stuff
        $objTable = new htmltable();
        $objTable->width = '800px';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '12';


        //Add Use-Category to the table
        $objTable->startRow();
        $objTable->addCell($wordCategory, '20%');
        $objTable->addCell(Null, '80%');
        $objTable->endRow();

        //question name text box
        $qnname = new textinput("qnname", "");
        $qnname->size = 60;
        $form->addRule('qnname', $this->objLanguage->languageText('mod_mcqtests_qnnamerequired', 'mcqtests'), 'required');
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnName, '20%');
        $objTable->addCell($qnname->show(), '80%');
        $objTable->endRow();

        //qn text
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'qntext';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $qntext = '';
        $editor->setContent($qntext);
        //Add Category to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnText, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //Add default qn grade
        $qngrade = new textinput("qngrade", "");
        $qngrade->size = 60;
        $form->addRule('qngrade', $phraseQnGrade . " " . $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests'), 'required');
        //Add qn grade to the table
        $objTable->startRow();
        $objTable->addCell($phraseQnGrade, '20%');
        $objTable->addCell($qngrade->show(), '80%');
        $objTable->endRow();

        //Add Penalty factor
        $pfactor = new textinput("penaltyfactor", "");
        $pfactor->size = 60;
        $form->addRule('penaltyfactor', $phrasePenaltyFactor . " " . $this->objLanguage->languageText('mod_mcqtests_isrequired', 'mcqtests'), 'required');
        //Add penalty factor field to the table
        $objTable->startRow();
        $objTable->addCell($phrasePenaltyFactor, '20%');
        $objTable->addCell($pfactor->show(), '80%');
        $objTable->endRow();

        //general feedback
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'genfeedback';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $genfeedback = '';
        $editor->setContent($genfeedback);
        //Add General Feedback to the table
        $objTable->startRow();
        $objTable->addCell($wordFeedback, '20%');
        $objTable->addCell($editor->show(), '80%');
        $objTable->endRow();

        //Add fieldset to hold general stuff
        $objFieldset = &$this->getObject('fieldset', 'htmlelements');
        //$objFieldset->width = '800px';
        //$objFieldset->align = 'center';
        $objFieldset->setLegend($wordGeneral);

        //Add table to General Fieldset
        $objFieldset->addContent($objTable->show());

        //Add General Fieldset to form
        $form->addToForm($objFieldset->show());

        //Reset Fieldset
        $objFieldset->reset();
        $count = 1;
        if ($answerCount < 2)
            $answerCount = 2;
        while ($count < $answerCount) {
            //increment count
            $count++;
            //Create table to hold the answer1
            $objTable3 = new htmltable();
            $objTable3->width = '800px';
            $objTable3->attributes = " align='center' border='0'";
            $objTable3->cellspacing = '12';

            //correct-answer-formula1 text box
            $answerformula = new textinput("simpleformula" . $count, "");
            $answerformula->size = 60;

            //Add correct-answer-formula1 to the table
            $objTable3->startRow();
            $objTable3->addCell($phraseCorrectAnsFormula, '20%');
            $objTable3->addCell($answerformula->show(), '80%');
            $objTable3->endRow();

            //grade dropdown
            $gradedropdown = new dropdown("gradeans" . $count);
            $gradedropdown->addOption("1", "100 %");
            $gradedropdown->addOption("0.9", "90%");
            $gradedropdown->addOption("0.8333333", "83.33333 %");
            $gradedropdown->addOption("0.8", "80 %");
            $gradedropdown->addOption("0.75", "75 %");
            $gradedropdown->addOption("0.7", "70 %");
            $gradedropdown->addOption("0.6666667", "66.66667 %");
            $gradedropdown->addOption("0.6", "60 %");
            $gradedropdown->addOption("0.5", "50 %");
            $gradedropdown->addOption("0.4", "40 %");
            $gradedropdown->addOption("0.3333333", "33.33333 %");
            $gradedropdown->addOption("0.3", "30 %");
            $gradedropdown->addOption("0.25", "25 %");
            $gradedropdown->addOption("0.2", "20 %");
            $gradedropdown->addOption("0.1666667", "16.66667 %");
            $gradedropdown->addOption("0.1428571", "14.28571 %");
            $gradedropdown->addOption("0.125", "12.5 %");
            $gradedropdown->addOption("0.1111111", "11.11111 %");
            $gradedropdown->addOption("0.1", "10 %");
            $gradedropdown->addOption("0.05", "5 %");
            $gradedropdown->addOption("0", $wordNone);
            $gradedropdown->setSelected("0");

            //Add grade dropdown to the table
            $objTable3->startRow();
            $objTable3->addCell($wordGrade, '20%');
            $objTable3->addCell($gradedropdown->show(), '80%');
            $objTable3->endRow();

            //simple-tolerance1 text box
            $simpletolerance1 = new textinput("simpletolerance" . $count, "");
            $simpletolerance1->size = 60;

            //Add simple-tolerance1 to the table
            $objTable3->startRow();
            $objTable3->addCell($phraseTolerance, '20%');
            $objTable3->addCell($simpletolerance1->show(), '80%');
            $objTable3->endRow();

            //tolerance-type dropdown
            $tolerancetype1 = new dropdown("tolerancetype" . $count);
            $tolerancetype1->addOption("1", $wordRelative);
            $tolerancetype1->addOption("2", $wordNominal);
            $tolerancetype1->setSelected("1");

            //Add tolerance-type dropdown to the table
            $objTable3->startRow();
            $objTable3->addCell($phraseToleranceType, '20%');
            $objTable3->addCell($tolerancetype1->show(), '80%');
            $objTable3->endRow();

            //correct-answer-shows dropdown
            $correctanswershows1 = new dropdown("correctanswershows" . $count);
            $correctanswershows1->addOption("1", "1");
            $correctanswershows1->addOption("2", "2");
            $correctanswershows1->addOption("3", "3");
            $correctanswershows1->addOption("4", "4");
            $correctanswershows1->addOption("5", "5");
            $correctanswershows1->addOption("6", "6");
            $correctanswershows1->addOption("7", "7");
            $correctanswershows1->addOption("8", "8");
            $correctanswershows1->addOption("9", "9");
            $correctanswershows1->setSelected("2");

            //Add correct-answer-shows dropdown to the table
            $objTable3->startRow();
            $objTable3->addCell($phraseCorrectAnsShows, '20%');
            $objTable3->addCell($correctanswershows1->show(), '80%');
            $objTable3->endRow();

            //Format dropdown
            $format1 = new dropdown("format" . $count);
            $format1->addOption("1", $wordDecimals);
            $format1->addOption("2", $phraseSigFigs);
            $format1->setSelected("1");

            //Add Format dropdown to the table
            $objTable3->startRow();
            $objTable3->addCell($wordFormat, '20%');
            $objTable3->addCell($format1->show(), '80%');
            $objTable3->endRow();

            //answer1 feedback htmlarea
            $editor = $this->newObject('htmlarea', 'htmlelements');
            $editor->name = 'feedback' . $count;
            $editor->height = '100px';
            $editor->width = '550px';
            $editor->setMCQToolBar();
            $qntext = '';
            $editor->setContent($qntext);

            //Add Answer1 feedback to the table
            $objTable3->startRow();
            $objTable3->addCell($wordFeedback, '20%');
            $objTable3->addCell($editor->show(), '80%');
            $objTable3->endRow();

            //Add fieldset to hold tags
            $objFieldset->setLegend($wordAnswer . " " . $count);

            //Add table to Tags Fieldset
            $objFieldset->addContent($objTable3->show());

            $form->addToForm($objFieldset->show());
            //Reset Fieldset
            $objFieldset->reset();
        }
        //Create table to hold unit-handling
        $objTable4 = new htmltable();
        $objTable4->width = '800px';
        $objTable4->attributes = " align='center' border='0'";
        $objTable4->cellspacing = '12';

        //unit-graded text box
        $unitgraded = new radio("unitgraded");
        $unitgraded->addOption("1", $phraseNumericalUnitGraded);
        $unitgraded->setSelected("0");

        //Add unit-graded to the table
        $objTable4->startRow();
        $objTable4->addCell($phraseUnitGraded, '20%');
        $objTable4->addCell($unitgraded->show(), '80%');
        $objTable4->endRow();

        //penalty-bad-unit text box
        $penaltybadunit = new textinput("penaltybadunit", "");
        $penaltybadunit->size = 20;

        //decimal-fraction dropdown
        $decimalfractionddown = new dropdown("decimalfraction");
        $decimalfractionddown->addOption("1", $phraseQuestionGrade);
        $decimalfractionddown->addOption("2", $phraseUnitHandling);
        $decimalfractionddown->setSelected("1");

        //Add penalty-bad-unit to the table
        $objTable4->startRow();
        $objTable4->addCell($phrasePenaltyBadUnit, '20%');
        $objTable4->addCell($penaltybadunit->show() . " " . $decimalfractionddown->show(), '80%');
        $objTable4->endRow();

        //instructions htmlarea
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'instructions';
        $editor->height = '100px';
        $editor->width = '550px';
        $editor->setMCQToolBar();
        $instructions = '';
        $editor->setContent($instructions);

        //Add instructions to the table
        $objTable4->startRow();
        $objTable4->addCell($wordInstructions, '20%');
        $objTable4->addCell($editor->show(), '80%');
        $objTable4->endRow();

        //unit-not-graded dropdown
        $unitnotgraded = new dropdown("unitnotgraded");
        $unitnotgraded->addOption("1", $phraseRightAs);
        $unitnotgraded->setSelected("0");

        //Add unit-not-graded dropdown to the table
        $objTable4->startRow();
        $objTable4->addCell($phraseNumericalAnsGraded, '20%');
        $objTable4->addCell($unitnotgraded->show(), '80%');
        $objTable4->endRow();

        //display-unit dropdown
        $displayunit = new dropdown("displayunit");
        $displayunit->addOption("0", $wordNo);
        $displayunit->addOption("1", $wordYes);
        $displayunit->setSelected("0");

        //Add display-unit dropdown to the table
        $objTable4->startRow();
        $objTable4->addCell($phraseDisplayUnit, '20%');
        $objTable4->addCell($displayunit->show(), '80%');
        $objTable4->endRow();

        //unit-position dropdown
        $unitpostion = new dropdown("unitpostion");
        $unitpostion->addOption("1", $phraseRightAs);
        $unitpostion->addOption("2", $phraseLeftAs);
        $unitpostion->setSelected("1");

        //Add tolerance-type dropdown to the table
        $objTable4->startRow();
        $objTable4->addCell($phraseUnitPosition, '20%');
        $objTable4->addCell($unitposition->show(), '80%');
        $objTable4->endRow();

        //Add fieldset to hold tags
        $objFieldset->setLegend($wordAnswer . " 1");

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable4->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        $count = 1;
        while ($count < $unitCount) {
            $count++;
            //Create table to hold units
            $objTable5 = new htmltable();
            $objTable5->width = '800px';
            $objTable5->attributes = " align='center' border='0'";
            $objTable5->cellspacing = '12';

            //unit text box
            $unitxt = new textinput("unit" . $count, "");
            $unitxt->size = 60;
            $form->addRule('unit' . $count, $wordUnit . " " . $phraseIsRequired, 'required');
            //Add Unit to the table
            $objTable5->startRow();
            $objTable5->addCell($wordUnit, '20%');
            $objTable5->addCell($unitxt->show(), '80%');
            $objTable5->endRow();

            //Add Multiplier to the table
            $objTable5->startRow();
            $objTable5->addCell($wordUnit, '20%');
            $objTable5->addCell($unitxt->show(), '80%');
            $objTable5->endRow();

            //Add fieldset to hold units
            $objFieldset->setLegend($wordUnit . " " . $count);

            //Add table to Tags Fieldset
            $objFieldset->addContent($objTable5->show());

            $form->addToForm($objFieldset->show());
            //Reset Fieldset
            $objFieldset->reset();
        }
        //Create table to hold the tags
        $objTable2 = new htmltable();
        $objTable2->width = '800px';
        $objTable2->attributes = " align='center' border='0'";
        $objTable2->cellspacing = '12';

        //tags text box
        $officialtags = new textinput("officialtags", "");
        $officialtags->size = 60;

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell($wordTags, '20%');
        $objTable2->addCell($phraseOfficialTags . " ( " . $phraseMngOfficialTags . " ) " . "<br />" . $officialtags->show(), '80%');
        $objTable2->endRow();

        //tags text box
        $othertags = "";
        $othertagsTA = new textarea();
        $othertagsTA->setName("othertags");
        $othertagsTA->setValue($othertags);
        $othertagsTA->setRows('4');
        $othertagsTA->setColumns('70');

        //Add Tags to the table
        $objTable2->startRow();
        $objTable2->addCell('&nbsp;');
        $objTable2->addCell($phraseOtherTags . " ( " . $phraseOtherTagsDesc . " ) " . "<br />" . $othertagsTA->show(), '80%');
        $objTable2->endRow();

        //Add fieldset to hold tags
        $objFieldset->setLegend($wordTags);

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable2->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        //Create table to hold the tags
        $objTable3 = new htmltable();
        $objTable3->width = '800px';
        $objTable3->attributes = " align='center' border='0'";
        $objTable3->cellspacing = '12';

        //Add Last Saved to the table
        $objTable3->startRow();
        $objTable3->addCell($phraseCreated, '20%');
        $objTable3->addCell("&nbsp;", '80%');
        $objTable3->endRow();

        //Add Last Saved to the table
        $objTable3->startRow();
        $objTable3->addCell($phraseLastSaved, '20%');
        $objTable3->addCell("&nbsp;", '80%');
        $objTable3->endRow();

        //Add fieldset to hold last-saved
        $objFieldset->setLegend($phraseCreatedOrSaved);

        //Add table to Tags Fieldset
        $objFieldset->addContent($objTable3->show());

        $form->addToForm($objFieldset->show());
        //Reset Fieldset
        $objFieldset->reset();

        // Create Save Button
        $button = new button("submit", $phraseSaveChanges);
        $button->setValue('savechanges');
        $button->setToSubmit();
        $btnSave = $button->showSexy();

        $button1 = new button("submit", $phraseSaveAsNewQn);
        $button1->setValue('saveasnew');
        //$button1->setToSubmit();
        $btnSaveAsnew = $button1->showSexy();

        // Create Cancel Button
        $buttonCancel = new button("submit", $this->objLanguage->languageText("word_cancel"));
        $objCancel = &$this->getObject("link", "htmlelements");
        $objCancel->link($this->uri(array(
                    'module' => 'mcqtests',
                    'action' => 'view',
                    'id' => $id,
                    'test' => $test
                )));
        $objCancel->link = $buttonCancel->showSexy();
        $btnCancel = $objCancel->show();

        //Add Save and Cancel Buttons to form
        $form->addToForm("<br />" . $btnSave . " " . $btnSaveAsnew . " " . $btnCancel . "<br />");

        return "<div>" . $form->show() . "</div>";
    }

}

?>