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

    if($mode=='mail'){
        $this->setLayoutTemplate('layout_tpl.php');
    }else{
        $this->setVar('pageSuppressToolbar', FALSE);
    }

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objTabbedbox=&$this->loadClass('tabbedbox','htmlelements');
    $objForm=&$this->loadClass('form','htmlelements');
    $objLink=&$this->loadClass('link','htmlelements');
    $objIcon=&$this->newObject('geticon','htmlelements');
    $objButton=&$this->loadClass('button','htmlelements');
    $objLayer=&$this->loadClass('layer','htmlelements');

// set up language items
    $mailHeading=$this->objLanguage->languageText('mod_survey_mailheading','survey');
    $thankyouHeading=$this->objLanguage->languageText('mod_survey_thankyou','survey');
    $mailLabel=$this->objLanguage->languageText('mod_survey_mail','survey');
    $finishedLabel=$this->objLanguage->languageText('mod_survey_finished','survey');
    $resultsLabel=$this->objLanguage->languageText('mod_survey_results','survey');

// set up code to text
    $array=array('item'=>strtolower($resultsLabel));
    $viewResultsLabel=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);

// set up heading
    if($mode=='mail'){
        $objHeader = new htmlheading();
        $objHeader->str=$mailHeading;
        $objHeader->type=1;
        echo $str=$objHeader->show().'<hr />';
    }else{
        // set up view results icon
        $surveyId=$arrSurveyData[0]['id'];
        $objIcon->title=$viewResultsLabel;
        $resultsIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId)),'viewresults');

        // set up view results link
        $objLink=new link($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId)),'survey');
        $objLink->link=$viewResultsLabel;
        $resultsLink=$objLink->show();

        $canViewResults=$this->canViewResults($surveyId);
        if($canViewResults){
            $icon=$resultsLink.'&nbsp;'.$resultsIcon;
        }else{
            $icon='';
        }

        $objHeader = new htmlheading();
        $objHeader->str=$arrSurveyData[0]['thanks_label'];
        $objHeader->type=1;
        $str=$objHeader->show().'<hr />';

        $objLayer = new layer();
        $objLayer->addToStr($str);
        $objLayer->addToStr($icon);
    }

    $objTable=new htmltable();
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';
    if($mode=='mail'){
        $objTable->startHeaderRow();
        $objTable->addHeaderCell($mailLabel,'','','','','');
        $objTable->endHeaderRow();
        $i=0;
        foreach($arrGroupList as $group){
            $class=(($i++%2)==0)?'odd':'even';

            $objTable->startRow();
            $objTable->addCell($group['groupDescription'],'','','',$class,'');
            $objTable->endRow();
        }
        $str=$objTable->show();
        echo $str.'<hr />';
    }else{
        $objTable->startRow();
        $objTable->addCell(stripslashes($arrSurveyData[0]['thanks_text']),'','','','odd','');
        $objTable->endRow();
        $str=$objTable->show().'<hr />';
        $objLayer->addToStr($str);
    }

// set up finished button
    $objButton=new button('finished',$finishedLabel);
    $objButton->extra=' onclick="javascript:
        this.disabled=\'disabled\';
        document.getElementById(\'form_continue\').submit();
    "';
    $finishedButton=$objButton->show();

    // Set up form
    //$objForm=new form('continue',$this->uri(array('action'=>'')));
    $objForm=new form('continue','?');
    $objForm->addToForm($finishedButton);
    $str=$objForm->show();

    if($mode=='mail'){
        echo '<br />' . $str;
    }else{
        $objLayer->addToStr($str);
        $objLayer->padding='10px';
        echo $objLayer->show();
    }
?>
