<?php

$this->setLayoutTemplate('mcqtests_layout_tpl.php');


//Load the class elements  
   $this->loadClass('form','htmlelements');
   $this->loadClass('htmlarea','htmlelements');
   $this->loadClass('textinput','htmlelements');
   $this->loadClass('label','htmlelements');
   $this->loadClass('radio','htmlelements');
   $this->loadClass('button','htmlelements');
   $this->loadClass('htmlheading','htmlelements');
   $this->loadClass('htmltable','htmlelements');
   

//Get the language items

 
   $addFreeform = $this->objLanguage->languageText('mod_mcqtests_addfreeformlabel','mcqtests');
   $editFreeform =$this->objLanguage->languageText('mod_mcqtests_editfreeformlabel','mcqtests');
   $addquestion=$this->objLanguage->languageText('mod_mcqtests_addaquestion','mcqtests');
   $testLabel=$this->objLanguage->languageText('mod_mcqtests_test','mcqtests');
   $totalLabel = $this->objLanguage->languageText('mod_mcqtests_totalmarks','mcqtests');
 
   $freeForm = $this->objLanguage->languageText('mod_mcqtests_freeform','mcqtests');
   $markLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
   $saveLabel = $this->objLanguage->languageText('word_save');
   $exitLabel = $this->objLanguage->languageText('word_cancel');
   $hintLabel = $this->objLanguage->languageText('mod_mcqtests_hint', 'mcqtests');
   $addhintLabel = $this->objLanguage->languageText('mod_mcqtests_hintenable', 'mcqtests');
   $lbEnable = $this->objLanguage->languageText('mod_mcqtests_hintaddenable','mcqtests');
   $lbDisable = $this->objLanguage->languageText('mod_mcqtests_hintadddisable','mcqtests');
//Reference the heading to the layout template 
  
  if ($mode == 'edit') {
    $this->setVarByRef('heading', $editFreeform);
} else {
  $this->setVarByRef('heading',$addFreeform);

}


  
// Display information on the test to be set
   $headStr = '<b>'.'Test'.':</b>&nbsp;&nbsp;'.$test['name'].'<br />';
   $headStr.= '<b>'.'Total Marks'.':</b>&nbsp;&nbsp;'.$test['totalmark'].'<br />&nbsp;';
   
     
     
     
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
    $num = $test['count']+1;
    $questionType = '';
    $questId = '';
} 

    
   //create a hidden field tostore  question type 
   $objInput = new textinput('type',$freeForm);
   $objInput->fldType = 'hidden';
   $headStr.= $objInput->show();
   
    
   //Heading for Question and number 
   $objHead = new htmlheading();
   $objHead->str = 'Question'.' '.$num.':';
   $objHead->type = 3;
   $headStr.= $objHead->show();
   
   
   //Create an instance of the htmlarea class 
   $objEditor = $this->newObject('htmlarea','htmlelements');
   	//initialise the fckeditor 
	$objEditor->init('question',$question, '300px', '500px');
   $objEditor->setDefaultToolBarSetWithoutSave();
   //$simplebox = $num ;
   //$textfield = new textinput ('simplebox',$stdanswer,'hidden');

   $headStr.=$objEditor->show().'<br /><br />';
   
   //create mark textfield
   $objMark=new textinput('mark',$mark);
   $objMark->size=10;
   $headStr.= '<b>'.$markLabel.'</b>:&nbsp;&nbsp;&nbsp;&nbsp;';
   $headStr.= $objMark->show();
    
    
   //create hint field 
   $headStr.= '<p><b>'.$hintLabel.':</b></p><p>'.$addhintLabel.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>';
   $objRadio = new radio('enablehint');
   $objRadio->setBreakSpace('&nbsp;&nbsp;/&nbsp;&nbsp;');
   $objRadio->addOption('yes', $lbEnable);
   $objRadio->addOption('no', $lbDisable);
   $objRadio->setSelected('no');
   if (!empty($hint)) {
    $objRadio->setSelected('yes');
 }
  $headStr.= '<p>'.$objRadio->show() .'</p>';
  $objInput = new textinput('hint', $hint);
  $objInput->size = 83;
  $headStr.= $objInput->show() .'<p>&nbsp;</p>';


   
   //Create Submit of the form
   $objButton=new button('save',$saveLabel);
   $objButton->setToSubmit();
   $btn =$objButton->show().'&nbsp;&nbsp;&nbsp;&nbsp;';
   $objButton=new button('save',$exitLabel);
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
  $objForm = new form('addfreeformquestion', $this->uri(array('action'=>'applyaddquestion')));
  $objForm->addToForm($headStr);
  $objForm->addToForm($objTableButtons->show());



echo $objForm->show();
?>