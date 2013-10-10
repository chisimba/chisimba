<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package survey
*/

/**
* Mail confirmation template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objIcon=&$this->newObject('geticon','htmlelements');
    $objInput=&$this->loadClass('textinput','htmlelements');
    $objEditor=$this->newObject('htmlarea','htmlelements');
    $objForm=&$this->loadClass('form','htmlelements');
    $objButton=&$this->loadClass('button','htmlelements');

// set up language items
    $manageLabel=$this->objLanguage->languageText('mod_survey_manage','survey');
    $pageLabel=$this->objLanguage->languageText('mod_survey_page','survey');
    $titleLabel=$this->objLanguage->languageText('mod_survey_pagetitle','survey');
    $instructionsLabel=$this->objLanguage->languageText('mod_survey_pageinstructions','survey');
    $submitLabel=$this->objLanguage->languageText('word_submit');
    $questionLabel=$this->objLanguage->languageText('mod_survey_question','survey');
    $errorLabel=$this->objLanguage->languageText('mod_survey_creationerrors','survey');
    $deletelastLabel=$this->objLanguage->languageText('mod_survey_deletelastpages','survey');

// set up code to text elements
    $array=array('item'=>strtolower($pageLabel));
    $addPageLabel=$this->objLanguage->code2Txt('mod_survey_add','survey',$array);
    $deletePageLabel=$this->objLanguage->code2Txt('mod_survey_delete','survey',$array);
    $deleteconfirmLabel=$this->objLanguage->code2Txt('mod_survey_deleteconfirm','survey',$array);
    $upLabel=$this->objLanguage->code2Txt('mod_survey_up','survey',$array);
    $downLabel=$this->objLanguage->code2Txt('mod_survey_down','survey',$array);
    $array=array('item'=>strtolower($questionLabel));
    $returnQuestionLabel=$this->objLanguage->code2Txt('mod_survey_return','survey',$array);


// set up data
    $arrPageList=$this->dbPages->listPages($surveyId);
    if(!$error && empty($update)){
        if(empty($arrPageList)){
            for($i=0;$i<=2;$i++){
                $arrPageList[$i]=array('id'=>'','page_order'=>'','page_label'=>'','page_text'=>'');
            }
        }
    }else{
        $arrPageList=$this->getSession('page');
        $arrErrorMsg=$this->getSession('error');
    }
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $surveyName=$arrSurveyData['0']['survey_name'];

// set up add icon
    $objIcon->title=$addPageLabel;
    $objIcon->setIcon('add');
    $objIcon->extra=' onclick="javascript:
        document.getElementById(\'input_update\').value=\'addpage\';
        document.getElementById(\'form_addPagesForm\').submit();
    "';
    $addIcon=' <a href="#">'.$objIcon->show().'</a>';

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$manageLabel.'&nbsp;'.$addIcon;
    $objHeader->type=1;
    echo $objHeader->show();

    $objHeader = new htmlheading();
    $objHeader->str=$surveyName;
    $objHeader->type=3;
    echo $objHeader->show().'<hr />';

    if($error){
        $objHeader = new htmlheading();
        $objHeader->str='<font class="error">'.$errorLabel.'</font><hr />';
        $objHeader->type=3;
        echo $objHeader->show();
    }

    if($error){
        if(isset($arrErrorMsg['0'])){
            $text='<b><font class="error">'.$arrErrorMsg['0'].'</font></b>';
            echo $text;
        }
    }

// set up hidden element
    $objInput=new textinput('update');
    $objInput->fldType='hidden';
    $updateText=$objInput->show();

// set up table
    $objTable=new htmltable();
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $i=0;
    foreach($arrPageList as $key=>$page){
        $class=(($i++%2)==0)?'odd':'even';

        $pageId=$page['id'];
        $pageOrder=$page['page_order'];
        $pageLabel=stripslashes($page['page_label']);
        $pageText=stripslashes($page['page_text']);

        // set up elements
        $objInput=new textinput('arrPageId[]',$pageId);
        $objInput->fldType='hidden';
        $pageIdText=$objInput->show();

        $objInput=new textinput('arrPageOrder[]',$pageOrder);
        $objInput->fldType='hidden';
        $orderText=$objInput->show();

        $objInput=new textinput('arrPageLabel[]',$pageLabel);
        $titleText=$objInput->show();

        $objEditor->init('arrPageText['.($i-1).']',$pageText,'','',NULL);
        $objEditor->height = '100px';
        $objEditor->setBasicToolBar();
        $pageArea=$objEditor->show();

        // set up move down icon
        $movePage='movepage_'.$key.'_down';
        $objIcon->title=$downLabel;
        $objIcon->setIcon('mvdown');
        $objIcon->extra=' onclick="javascript:
            document.getElementById(\'input_update\').value=\''.$movePage.'\';
            document.getElementById(\'form_addPagesForm\').submit();
        "';
        $downIcon=' <a href="#">'.$objIcon->show().'</a>';

        // set up move up icon
        $movePage='movepage_'.$key.'_up';
        $objIcon->title=$upLabel;
        $objIcon->setIcon('mvup');
        $objIcon->extra=' onclick="javascript:
            document.getElementById(\'input_update\').value=\''.$movePage.'\';
            document.getElementById(\'form_addPagesForm\').submit();
        "';
        $upIcon=' <a href="#">'.$objIcon->show().'</a>';

        // set up delete question icon
        if(count($arrPageList)=='2'){
            $label=$deletelastLabel;
            $deletePage='deleteall';
        }else{
            $label=$deleteconfirmLabel;
            $deletePage='deletepage_'.$key;
        }
        $objIcon->title=$deletePageLabel;
        $objIcon->setIcon('delete');
        $objIcon->extra=' onclick="javascript:
            if(confirm(\''.$label.'\')){
                document.getElementById(\'input_update\').value=\''.$deletePage.'\';
                document.getElementById(\'form_addPagesForm\').submit();
            }
        "';
        $deleteIcon=' <a href="#">'.$objIcon->show().'</a>';

        // set up icons
        if($key=='0'){
            $icons=$downIcon.'&nbsp;'.$deleteIcon;
        }elseif($key==(count($arrPageList)-1)){
            $icons=$upIcon.'&nbsp;'.$deleteIcon;
        }else{
            $icons=$downIcon.'&nbsp;'.$upIcon.'&nbsp;'.$deleteIcon;
        }

        if($error){
            if(isset($arrErrorMsg['page_'.$key])){
                $objTable->startRow();
                $objTable->addCell('<b><font class="error">'.$arrErrorMsg['page_'.$key].'</font></b>','','','',$class,'colspan="3"');
                $objTable->endRow();
            }
        }

        $objTable->startRow();
        $objTable->addCell($pageIdText.$titleLabel,'','','',$class,'');
        $objTable->addCell($titleText,'','','',$class,'');
        $objTable->addCell($icons,'10%','','right',$class,'rowspan="2"');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell($orderText.$instructionsLabel,'','','',$class,'');
        $objTable->addCell($pageArea,'','','',$class,'');
        $objTable->endRow();
    }
    $table=$objTable->show();

// set up submit button
    $objButton=new button('submitButton',$submitLabel);
    $objButton->extra=' onclick="javascript:
        document.getElementById(\'input_update\').value=\'save\';
        this.disabled=\'disabled\';
        document.getElementById(\'form_addPagesForm\').submit();
    "';
    $submitButton=$objButton->show().'<hr />';

    // Set up form
    $objForm=new form('addPagesForm',$this->uri(array('action'=>'validatepages','survey_id'=>$surveyId)));
    $objForm->addToForm($updateText.$table.$submitButton);
    $str=$objForm->show();

    echo $str;

    $objLink=new link($this->uri(array('action'=>'listquestions','survey_id'=>$surveyId),'survey'));
    $objLink->link=$returnQuestionLabel;
    $returnLink=$objLink->show();
    echo $returnLink;
?>