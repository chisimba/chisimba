<?php
/**
* @package pbladmin
*/

/**
* Template for adding a single answer question or multiple choice question to a task.
*/

// Suppress normal page elements and layout
$this->setVar('pageSuppressIM', FALSE);
$this->setVar('pageSuppressBanner', FALSE);
$this->setVar('pageSuppressContainer', FALSE);
$this->setVar('pageSuppressToolbar', FALSE);
$this->setVar('suppressFooter', FALSE);

// Set up html elements
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$objHead = $this->newObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

if(!isset($task)){
    $task = 'mcq';
}

// Set up language items
$head = $this->objLanguage->languageText('mod_pbladmin_add'.$task, 'pbladmin');
$questionLabel = $this->objLanguage->languageText('word_question');
$answersLabel = $this->objLanguage->languageText('word_answers');
$correctLabel = $this->objLanguage->languageText('mod_pbladmin_selectcorrect', 'pbladmin');
$saveLabel = $this->objLanguage->languageText('word_save');
$exitLabel = $this->objLanguage->languageText('word_close');
$correctAnsLabel = $this->objLanguage->languageText('phrase_correctanswer');

if(isset($data) && !empty($data)){
    $questionData = $_POST['question'];
    $options = $data['options'] + 4;
    $checkData = array_fill(0, $options+1, FALSE);
    $answerData = array_fill('', $options+1, FALSE);

    for($i = 1; $i <= $data['options']; $i++){
        $answerData[$i] = $data['answer'.$i];

        if(isset($data['box'.$i])){
            $checkData[$i] = TRUE;
        }
    }
}else{
    $options = 4;
    $questionData = '';
    $checkData = array_fill(0, $options+1, FALSE);
    $answerData = array_fill('', $options+1, FALSE);
}

// Script to build the output string
$javascript = "<script language=\"JavaScript\">
    var taskStr;

    function submitForm(){
        document.addtask.submit();
    }

    function buildMCQ(){
        var answers;
        var taskStr;
        var field;

        answers = check();

        taskStr = window.opener.document.forms['create'].task.value+' ';
        taskStr += document.forms['addtask'].question.value+' ~mcq('+answers+')';

        window.opener.document.forms['create'].task.value=taskStr;
    }

    function buildCAQ(){
        var answers;
        var taskStr;

        answers = document.forms['addtask'].answer1.value+'->right;';
        answers += 'else->wrong';

        taskStr = window.opener.document.forms['create'].task.value+' ';
        taskStr += document.forms['addtask'].question.value+' ~choice('+answers+')';
        window.opener.document.forms['create'].task.value=taskStr;
    }

    function check(){
        var i;
        var formElements = document.forms['addtask'].elements;
        var answers = '';
        var correct = '';
        var length = document.forms['addtask'].elements.length;

        var i = 0;
        while(i++ != length-1){
            if (formElements[i].type == \"text\") {
                if (formElements[i].value != \"\") {
                    if(answers != ''){
                        answers += ',';
                    }
                    answers += formElements[i].value;
                }
            }else if(formElements[i].type == \"checkbox\") {
                if (formElements[i].checked) {
                    if(correct != ''){
                        correct += ',';
                    }
                    correct += formElements[i].name[3];
                }
            }
        }
        return answers+':ok '+correct;
    }
</script>";

/*
   //for (var i=1; i < length; i++) {
        */
echo $javascript;

$objHead->str = $head;
$objHead->type = 1;
$heading = $objHead->show();

// Set question
$objLabel = new label('<b>'.$questionLabel.':</b>', 'input_question');
$question = $objLabel->show();

$objInput = new textinput('question', $questionData, '', '40');
$question .= '<br />'.$objInput->show();

// Set up MCQ
if($task == 'mcq'){
    // Set answers
    $objLabel = new label('<b>'.$answersLabel.':</b>', 'input_answer1');
    $answers = $objLabel->show();

    $answers .= '<br />'.$correctLabel.'<br />';

    for($i = 1; $i <= $options; $i++){
        $objCheck = new checkbox('box'.$i);
        $objCheck->setChecked($checkData[$i]);
        $objInput = new textinput('answer'.$i, $answerData[$i], '', '35');
        $answers .= '<br />'.$objCheck->show().'&nbsp;&nbsp;'.$objInput->show();
    }

    $objLink = new link("#");//$this->uri(array('action'=>'addmore', 'options'=>$options)));
    $objLink->link = 'Click here to add more options';
    $objLink->extra = "onclick=\"submitForm()\"";
    $answers .= '<p>'.$objLink->show().'</p>';
}else{
// Set up choice answer question
    $objLabel = new label('<b>'.$correctAnsLabel.':</b>', 'input_answer1');
    $answers = $objLabel->show();

    $objInput = new textinput('answer1', '', '', '40');
    $answers .= '<br />'.$objInput->show();
}

// Submit/exit buttons
$objButton = new button('save', $saveLabel);
$objButton->setOnClick("javascript:build".strtoupper($task)."(); window.close();");
$objButton->setIconClass("save");
$buttons = $objButton->show().'&nbsp;&nbsp;&nbsp;&nbsp;';

$objButton = new button('save', $exitLabel);
$objButton->setOnClick('window.close()');
$objButton->setIconClass("cancel");
$buttons .= $objButton->show();

// Add elements to form
$objForm = new form('addtask', $this->uri(array('action'=>'addmore', 'options'=>$options)));
$objForm->addToForm($heading);
$objForm->addToForm('<p>'.$question."</p>\n");
$objForm->addToForm('<p>'.$answers."</p>\n");
$objForm->addToForm('<p>'.$buttons."</p>\n");

$objLayer->str = $objForm->show();
$objLayer->align = 'center';
echo $objLayer->show();
?>