<?php
/**
* @package pbladmin
*/

/*
* Template for PBL Administration page.
*/

$this->setLayoutTemplate('admin_layout_tpl.php');
echo $this->jscript->ShowPopUp();

// get case data for display
$files = $this->getInstalledCases();
$user = $this->username;

// Setup instances of html objects
$this->loadClass('htmltable','htmlelements');
$this->loadClass('form','htmlelements');
$objForm = array();
$this->loadClass('button','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('link','htmlelements');
$objMessage= $this->newObject('timeoutmessage','htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');
$objLayer = $this->newObject('layer','htmlelements');

// set add new case icon
$uploadLabel = $this->objLanguage->languageText('mod_pbladmin_uploadpblfile', 'pbladmin');
$createLabel = $this->objLanguage->languageText('mod_pbladmin_createcase', 'pbladmin');
$addLabel = $this->objLanguage->languageText('mod_pbladmin_addnewcase', 'pbladmin');
$lbNoCases = $this->objLanguage->languageText('mod_pbladmin_nocasesinstalled', 'pbladmin');
$objIcon->title=$this->objLanguage->languageText('word_delete');
$lbBack = $this->objLanguage->languageText('word_back');

// set layer headings
$casehead = $this->objLanguage->languageText('mod_pbladmin_nameofcase', 'pbladmin');
$classhead = $this->objLanguage->languageText('word_classrooms');
$installed = $this->objLanguage->languageText('word_installed');
$heading = $this->objLanguage->languageText('phrase_viewcases');

$this->setVarByRef('heading',$heading);

// links for adding a case
$objLink = new link($this->uri(array('action'=>'upload')));
$objLink->link = $uploadLabel;
$link1 = $objLink->show();

$objLink = new link($this->uri(array('action'=>'createcase')));
$objLink->link = $createLabel;
$link2 = $objLink->show();

$objLayer->str = '<p>'.$link1.'<br />'.$link2.'</p>';
echo $objLayer->show().'<br />';

if(!empty($msg)){
    $objMessage->setMessage($msg.'<p>&nbsp;</p>');
    echo $objMessage->show();
}

// click on classroom to edit it
//$objButton = new button('open', $lbGo);
//$objButton->setToSubmit();

$i=1;
$objTable = new htmltable();
$objTable->row_attributes=" height='30' ";
$objTable->cellpadding = '5';
$objTable->cellspacing = '2';

if(!empty($files)){
    
    $tableHd = array();
    $tableHd[] = '';
    $tableHd[] = $casehead;
    //$tableHd[] = $classhead;
    $tableHd[] = $installed;
    
    $objTable->addHeader($tableHd);
    
    foreach($files as $key=>$file){
        $class = (($i++ %2)==0) ? 'even':'odd';

        /* check owner
        $owner = $file['owner'];

        // get a drop down list of classrooms and add a delete icon
        $classes = $this->getClassList($file['id'], $i);

        if(!empty($classes)){
            // if class is an array - only contains 'create new classroom', no real classrooms
            if(is_array($classes)){
                $classes=$classes[1];
            }

            // click on classroom to edit it
            $objTable4[$i] = new htmltable();
            $objForm[$i] = new form('editclass'.$i,$this->uri(array('')));
            $objForm[$i]->method='get';
            $objInput = new textinput('casename',$file['name'], 'hidden');
            $objForm[$i]->addToForm($objInput->show());
            $objInput = new textinput('module','pbladmin', 'hidden');
            $objForm[$i]->addToForm($objInput->show());
                $objInput = new textinput('action','gotoclass', 'hidden');
            $objForm[$i]->addToForm($objInput->show());
            $objTable4[$i]->startRow();
            $objTable4[$i]->addCell($classes,'70%','bottom','center');
            $objTable4[$i]->addCell($objButton->show(),'30%','center');
            $objTable4[$i]->endRow();
            $objForm[$i]->addToForm($objTable4[$i]->show());
            $classForm = $objForm[$i]->show();
        } else {
            $classForm='';
        }
        */
        
        // Delete icon
        $delete = $objIcon->getDeleteIconWithConfirm($file['id'], array('action' =>'delete', 'cid'=> $file['id'], 'cname'=>$file['name']), 'pbladmin');

        $objTable->startRow();
        $objTable->addCell($i-1, '5%','center','',$class);
        $objTable->addCell($file['name'], '45%','center','',$class);
        //$objTable->addCell($classForm,'42%','','',$class);
        $objTable->addCell($delete,'8%','','',$class);
        $objTable->endRow();
    }
}else{
    $objTable->addRow(array($lbNoCases), 'noRecordsMessage');
}

// display
echo $objTable->show();

$objLink = new link($this->uri(''));
$objLink->link = $lbBack;

$objLayer->str = '<p style="padding-top:10px;">'.$objLink->show().'</p>';
$objLayer->align = 'center';

echo $objLayer->show();
?>