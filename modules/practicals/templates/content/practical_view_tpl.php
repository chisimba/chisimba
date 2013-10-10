<?php
/**
 * @package practical
 */

/**
 * Template to view the practical.
 * Students can submit their practicals.
 * @param array $data The details of the practical.
 */

$this->setLayoutTemplate('practical_layout_tpl.php');
// set up html elements
$this->loadClass('htmltable','htmlelements');
$this->loadClass('htmlheading','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements'); 
$this->loadClass('textarea','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('link','htmlelements');

// set up language items
$practicalLabel = $this->objLanguage->languageText('mod_practicals_practical','practicals');
$heading = $this->objLanguage->languageText('mod_practicals_view','practicals').' '.$practicalLabel;
$nameLabel = $this->objLanguage->languageText('mod_practicals_wordname','practicals');
$descriptionLabel = $this->objLanguage->languageText('mod_practicals_description','practicals');
$lecturerLabel = ucwords($this->objLanguage->languageText('mod_context_author'));
$resubmitLabel = $this->objLanguage->languageText('mod_practicals_allowresubmit','practicals');
$markLabel = $this->objLanguage->languageText('mod_practicals_mark','practicals');
$dateLabel = $this->objLanguage->languageText('mod_practicals_closingdate','practicals','practicals');
$noLabel = $this->objLanguage->languageText('word_no');
$yesLabel = $this->objLanguage->languageText('word_yes');
$submitLabel = $this->objLanguage->languageText('word_submit');
$completeLabel = $this->objLanguage->languageText('mod_practicals_completeonline','practicals');
$uploadLabel = $this->objLanguage->languageText('mod_practicals_upload','practicals').' '.$practicalLabel;
$exitLabel = $this->objLanguage->languageText('word_exit');
$percentLabel = $this->objLanguage->languageText('mod_practicals_percentyrmark','practicals');
$btnupload=$this->objLanguage->languageText('mod_practicals_uploadfile' ,'practicals');
//$objSelectFile = $this->newObject('selectfile', 'filemanager');

$lnPlain = $this->objLanguage->languageText('mod_testadmin_plaintexteditor');
$lnWysiwyg = $this->objLanguage->languageText('mod_testadmin_wysiwygeditor');

$this->setVarByRef('heading', $heading);

// submission of the practical
$javascript="<script language=\"JavaScript\">
    function submitForm(val){
    document.upload.action.value=val;
    document.upload.submit();
    }
    </script>";
echo $javascript;

$str = '';
$objTable = new htmltable();
$objTable->cellpadding=5;
$objTable->width='99%';

if(!empty($data)) {
    // Display the practical
    $objTable->startRow();
    $objTable->addCell('<b>'.$nameLabel.':</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$data[0]['name'],'45%');
    $objTable->addCell('<b>'.$lecturerLabel.':</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->objUser->fullname($data[0]['userid']),'55%');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell('<b>'.$markLabel.':</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$data[0]['mark']);
    $objTable->addCell('<b>'.$percentLabel.':</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$data[0]['percentage'].' %');
    $objTable->endRow();

    if($data[0]['resubmit']) {
        $resubmit = $yesLabel;
    }else {
        $resubmit = $noLabel;
    }
    $objTable->startRow();
    $objTable->addCell('<b>'.$resubmitLabel.':</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$resubmit);
    $objTable->addCell('<b>'.$dateLabel.':</b>&nbsp;&nbsp;&nbsp;&nbsp;'.$this->formatDate($data[0]['closing_date']));
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell('<b>'.$descriptionLabel.':</b>&nbsp;&nbsp;&nbsp;&nbsp;');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell($data[0]['description'],'','','','','colspan="2"');
    $objTable->endRow();

    $objLayer = new layer();
    $objLayer->cssClass = 'odd';



    $objLayer->str = $objTable->show();
    $str = $objLayer->show();

    // Section for submitting the practical by upload or online if user is a student
    $objHead = new htmlheading();
    $objHead->type = 4;
    $objHead->str = $submitLabel.' '.$practicalLabel;
    $str .= $objHead->show();


    $this->objButton = new button('submit',$btnupload);
    $returnUrl = $this->uri(array('action' => 'upload','id'=>$data[0]['id']));
    $this->objButton->setOnClick("window.location='$returnUrl'");
    $this->objButton->setToSubmit();
    $btn1= $this->objButton->show();

    $str .= $btn1;

    // Determine format of submission: upload or online
    if($data[0]['format']) {


        if(!empty($data[0]['fileid'])) {
            $objInput = new textinput('fileid', $data[0]['fileid']);
            $objInput->fldType = 'hidden';
            $inputStr .= $objInput->show();
        }
    }else {
        $dOnline = '';
        // Complete practical online, display practical if multiple submissions
        if(!empty($data[0]['online'])) {
            $dOnline = $data[0]['online'];
        }

        $inputStr = '<b>'.$completeLabel.':</b>';

        $type = $this->getParam('editor', 'ww');
        if($type == 'plaintext') {
            // Hidden element for the editor type
            $objInput = new textinput('editor', 'ww', 'hidden');
            $objText = new textarea('text', $dOnline, 15, 80);
            $inputStr .= $objText->show();
            $objLink = new link("javascript:submitform('changeeditor')");
            $objLink->link = $lnWysiwyg;
            $inputStr .= '<br/>'.$objLink->show().$objInput->show();
        }else {
            // Hidden element for the editor type
            $objInput = new textinput('editor', 'plaintext', 'hidden');
            $objEditor = $this->newObject('htmlarea', 'htmlelements');
            $objEditor->init('text', $dOnline, '500px', '500px');
            $objEditor->setDefaultToolBarSetWithoutSave();
            $inputStr .= $objEditor->show();
            $objLink = new link("javascript:submitForm('changeeditor')");
            $objLink->link = $lnPlain;
            $inputStr .= '<br />'.$objLink->show().$objInput->show();
        }

    }
    // hidden elements
    $objInput = new textinput('format', $data[0]['format'], 'hidden');
    $hidden = $objInput->show();

    $objInput = new textinput('id', $data[0]['id'], 'hidden');
    $hidden .= $objInput->show();

    $objInput = new textinput('jokes', 'submit', 'hidden');
    $hidden .= $objInput->show();

    if(!empty($data[0]['submitid'])) {
        $objInput = new textinput('submitid', $data[0]['submitid']);
        $objInput->fldType = 'hidden';
        $hidden .= $objInput->show();
    }

    // submit buttons and form

    $objButton = new button('save', $submitLabel);
    $objButton->setToSubmit();
    $btns = $objButton->show().'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    $objButton = new button('exit', $exitLabel);
    $objButton->setToSubmit();
    $btns .= $objButton->show();


    $objForm = new form('submit', $this->uri(array('action' => 'onlinesubmit')));
    $objForm->extra = " ENCTYPE='multipart/form-data'";
    $objForm->addToForm($inputStr);
    $objForm->addToForm($hidden);

    if(!$data[0]['format']) {
        $objForm->addToForm('<br />'.$btns);
    }else {

    }

    $layerStr = $objForm->show();

    $objLayer1 = new layer();
    $objLayer1->str = $layerStr;
    $objLayer1->align = 'center';
    $str .= $objLayer1->show();
}

echo $str;

?>