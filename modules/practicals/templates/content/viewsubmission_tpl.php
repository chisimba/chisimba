<?php
$isLecturerRole = $this->objUser->isCourseAdmin($this->contextCode);
//echo '<pre>';
//var_dump($submission);
//echo '</pre>';
// StyleSheet for the slider
/*
$css="
<style type=\"text/css\">
.form_widget_amount_slider{
	border-top:1px solid #9d9c99;
	border-left:1px solid #9d9c99;
	border-bottom:1px solid #eee;
	border-right:1px solid #eee;
	background-color:#f0ede0;
	height:3px;
	position:absolute;
	bottom:0px;
}
</style>
";
$this->appendArrayVar('headerParams', $css);
// JavaScript for the slider
$js='<script language="JavaScript" src="'.$this->getResourceUri('dhtmlslider.js').'" type="text/javascript"></script>';
$this->appendArrayVar('headerParams', $js);
*/
// Load classes
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadclass('textinput','htmlelements');
$objDateTime = $this->getObject('dateandtime', 'utilities');
$objTrimStr = $this->getObject('trimstr', 'strings');
$objWashout = $this->getObject('washout', 'utilities');
/*
if (!is_null($submission['mark'])) {
    $submission['mark'] = (int)$submission['mark'];
//is_null($submission['mark'])?'0':(int)$submission['mark']
}
*/
// Section 1
// Heading
$header = new htmlHeading();
$header->str = $practical['name'];
$header->type = 1;
echo $header->show();
// Table
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('word_description', 'system', 'Description').'</strong>', 130);
$table->addCell($objWashout->parseText($practical['description']), NULL, NULL, NULL, NULL, ' colspan="3"');
$table->endRow();
$table->startRow();
$table->addCell('<strong>'.ucwords($this->objLanguage->code2Txt('mod_practicals_lecturer', 'practicals', NULL, '[-author-]')).':</strong>', 130);
$table->addCell($this->objUser->fullName($practical['userid']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_totalmark', 'practicals').'</strong>', 130);
$table->addCell($practical['mark']);
$table->endRow();
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_openingdate', 'practicals', 'Opening Date').'</strong>', 130);
$table->addCell($objDateTime->formatDate($practical['opening_date']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_percentyrmark', 'practicals', 'Percentage of year mark').':</strong>', 200, NULL, NULL, 'nowrap');
$table->addCell($practical['percentage'].'%');
$table->endRow();
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_closingdate', 'practicals', 'Closing Date').'</strong>', 130);
$table->addCell($objDateTime->formatDate($practical['closing_date']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_practicaltype', 'practicals', 'Practical Type').'</strong>', 130);
if ($practical['format'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_practicals_online', 'practicals', 'Online'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_practicals_upload', 'practicals', 'Upload'));
}
$table->endRow();
echo $table->show();
// Section 2
$objIcon = $this->getObject('geticon', 'htmlelements');
$objMark = $this->getObject('markimage', 'utilities');
//
$isMarked = $submission['mark'] != NULL && $practical['closing_date'] < date('Y-m-d H:i:s');
//
if ($practical['format'] == '1') {
    // Upload
    define('PRACTICAL_FT_STUDENT',1);
    define('PRACTICAL_FT_LECTURER',2);
    if ($isLecturerRole) {
        $fileType = PRACTICAL_FT_STUDENT;
    }
    else {
        if (!$isMarked) {
            $fileType = PRACTICAL_FT_STUDENT;
        } else {
            $fileType = PRACTICAL_FT_LECTURER;
        }
    }
    switch($fileType){
        case PRACTICAL_FT_LECTURER:
            $fileId = $submission['lecturerfileid'];
            break;
        case PRACTICAL_FT_STUDENT:
            $fileId = $submission['studentfileid'];
            break;
        default:
            ;
    } // switch
    if (is_null($fileId)) {
        $header = new htmlHeading();
        if ($fileType == PRACTICAL_FT_STUDENT) {
            $str = '<em>'.$this->objLanguage->languageText('mod_practicals_nopracticalavailable', 'practicals').'</em>';
        } else if ($fileType == PRACTICAL_FT_LECTURER) {
            $str = '<em>'.$this->objLanguage->languageText('mod_practicals_nomarkedpracticalavailable', 'practicals').'</em>';
        } else {
            $str = 'Unkown practical filetype!';
        }
        $header->str = $str;
        $header->type = 3;
        echo $header->show();
    } else {
        // Header
        $header = new htmlHeading();
        if ($fileType == PRACTICAL_FT_STUDENT) {
            $str = $this->objLanguage->code2Txt('mod_practicals_viewassgnby', 'practicals', NULL); //'View ssignment Submitted by [-person-] at [-time-]'
            $str = str_replace('[-person-]', $this->objUser->fullName($submission['userid']), $str);
            $str = str_replace('[-time-]', $objDateTime->formatDate($submission['datesubmitted']), $str);
        } else if ($fileType == PRACTICAL_FT_LECTURER) {
            $str = $this->objLanguage->code2Txt('mod_practicals_viewmarkedpractical', 'practicals', NULL); //'View ssignment Submitted by [-person-] at [-time-]'
            $str = str_replace('[-person-]', $this->objUser->fullName($submission['userid']), $str);
            $str = str_replace('[-time-]', $objDateTime->formatDate($submission['datesubmitted']), $str);
        }
        $header->str = $str;
        $header->type = 3;
        echo $header->show();
        // Content
        $objFile = $this->getObject('dbfile', 'filemanager');
        $fileName = $objFile->getFileName($fileId);
        $downloadLink = new link ($this->uri(array('action'=>'downloadfile', 'id'=>$submission['id'], 'fileid'=>$fileId)));
        $downloadLink->link = $this->objLanguage->languageText('word_download', 'system', 'Download');
        $objFileIcon = $this->getObject('fileicons', 'files');
        echo '<p>'.$objFileIcon->getFileIcon($fileName).' '.$downloadLink->show().'</p>';
        $filePath = $this->objPracticalSubmit->getPracticalFilename($submission['id'], $fileId);
        // HTML file needed for conversion
        //$file_ = $objFile->getFile($fileId);
	//$fileName_ = $file_['filename'];
	$submissionId = $submission['id'];
    $sysTemp = sys_get_temp_dir();
    if ($sysTemp[strlen($sysTemp)-1] != DIRECTORY_SEPARATOR) {
        $sysTemp .= DIRECTORY_SEPARATOR;
    }
	$tempFilePath = $sysTemp.'chisimba'.DIRECTORY_SEPARATOR.$this->objConfig->serverName().DIRECTORY_SEPARATOR.'practicals'.DIRECTORY_SEPARATOR.'submissions'.DIRECTORY_SEPARATOR.$submissionId; //'/'.$fileName;
        //echo "[$tempFilePath]";
        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $tempFilePath = $objCleanUrl->cleanUpUrl($tempFilePath);
        $objMkdir = $this->getObject('mkdir', 'files');
        $objMkdir->mkdirs($tempFilePath);
	chmod($tempFilePath, 0777);
	$tempFile = $tempFilePath . '/' . $fileName . '.html';
	//$temp_file_ = tempnam(sys_get_temp_dir(), 'CHA');
	//$temp_file = $temp_file_ . '.html';
	//rename($temp_file_, $temp_file);
	//chmod($temp_file, 0777);
        $destinationHtml = $tempFile; //$filePath.'.html';
        // PHP file which will contain the practical
        $destinationPhp = $filePath.'.php';
        // Check if the file exists, else we need to convert the document
        if (!file_exists($destinationPhp)) {
            //if (file_exists($destinationHtml)) {
            //    unlink($destinationHtml);
            //}
            //if (!file_exists($destinationHtml)) {
	    // Convert Document
	    $objConvert = $this->getObject('convertdoc', 'documentconverter');
	    $conversionOK = $objConvert->convert($filePath, $destinationHtml);
	    /*
	    if (!$conversionOK) {
		 die('Conversion failed!');
	    }
	    */
            //}
            //else {
	    //$conversionOK = TRUE;
            //}
            // If successfully converted, rename .html to .php
            if ($conversionOK && file_exists($destinationHtml)) {
                //rename($destinationHtml, $destinationPhp);
                copy($destinationHtml, $destinationPhp);
                //unlink($destinationHtml);
                $contents =  file_get_contents($destinationPhp);
                $contents = '<?php if (isset($permission) && $permission) { ?>'.$contents.'<?php } ?>';
                file_put_contents($destinationPhp, $contents);
            }
        }
        if (file_exists($destinationPhp)) {
            $this->loadClass('iframe', 'htmlelements');
            $header = new htmlHeading();
            $header->str = $this->objLanguage->languageText('word_preview', 'system', 'Preview');
            $header->type = 1;
            echo $header->show();
            $iframe = new iframe();
            $iframe->width = '100%';
            $iframe->height = 400;
            //$iframe->src = $this->uri(array('action'=>'viewhtmlsubmission', '1'=>'1', '2'=>'2', '3'=>'3'));
            $iframe->src = $this->uri(array('action'=>'viewhtmlsubmission', 'id'=>$submission['id'],'fileid'=>$fileId));
    //        echo '<pre>';
    //        echo $iframe->src;
    //        echo '</pre>';
            echo $iframe->show();
        }
    }
    if ($submission['mark'] != NULL && ($practical['closing_date'] < date('Y-m-d H:i:s') || $this->isValid('edit'))) {
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_practicals_result', 'practicals');
        $header->type = 3;
        echo $header->show();
        $table = $this->newObject('htmltable', 'htmlelements');
        $objMark->value = $submission['mark'];
        $table->startRow();
        $table->addCell($objMark->show(), 120);
        $content = '<p><strong>'.$this->objLanguage->languageText('mod_practicals_mark', 'practicals', 'Mark').': '.$submission['mark'].'/'.$practical['mark'].'</strong></p>';
        $content .= '<p>'.nl2br($submission['commentinfo']).'</p>';
        $table->addCell($content);
        $table->endRow();
        echo $table->show();
    } else {
        $header = new htmlHeading();
        $header->str = '<em>'.$this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked').'</em>';
        $header->type = 3;
        echo $header->show();
    }
    if ($this->isValid('saveuploadmark')) {
        // Header
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_practicals_markassgn', 'practicals', 'Mark Practical');
        $header->type = 3;
        echo $header->show();
        // Form
        $form = new form ('_form', $this->uri(array('action'=>'saveuploadmark')));
        $form->extra = 'enctype="multipart/form-data"';
        $hiddenInput = new hiddeninput('id', $submission['id']);
        $textArea = new textarea('commentinfo');
        $textArea->value = $submission['commentinfo'];
        $button = new button('savemark', $this->objLanguage->languageText('mod_practicals_markassgn', 'practicals', 'Mark Practical'));
        $button->setToSubmit();
/*
        $slider = $this->newObject('slider', 'htmlelements');
        $slider->value = $submission['mark'];
        $slider->maxValue = $practical['mark'];

        $table = $this->newObject('htmltable', 'htmlelements');
*/
    	//Setup Tables
    	$table = $this->newObject('htmltable', 'htmlelements');
    	$objSubTable = new htmltable();
    	$objSubTable->width="60%";
    	//Insert mark
        $objTextinput = new textinput('mark',is_null($submission['mark'])?'0':(int)$submission['mark']);
    	$objTextinput->size='5';
    	$objTextinput->extra=' maxlength=\'4\'';
    	$objSubTable->startRow();
    	$objSubTable->addCell($objTextinput->show().' / '.$practical['mark']." ".$this->objLanguage->languageText('mod_practicals_typeorslider', 'practicals', 'Mark'),'70%','','left');
    	$objSubTable->addCell("&nbsp;");
    	$objSubTable->endRow();
     	$objSubTable->startRow();
        $objSlider = $this->newObject('dhtmlgoodies_slider', 'dhtmlgoodies');
        $objSlider->setTargetId('slider_target');
        $objSlider->setFieldRef('document._form.mark');
        $objSlider->setWidth(200);
        $objSlider->setMin(0);
        $objSlider->setmax($practical['mark']);
    	$objSubTable->addCell('<span id=\'slider_target\'></span>'.$objSlider->show(),'70%','','left');
    	$objSubTable->addCell("&nbsp;");
    	$objSubTable->endRow();
        $table->startRow();
        //$table->addCell("&nbsp;");
        $table->addCell($this->objLanguage->languageText('mod_practicals_mark', 'practicals', 'Mark'), 120);
        $table->addCell($objSubTable->show());
        $table->endRow();
    	//Spacer
        $table->startRow();
        $table->addCell("&nbsp;");
        $table->addCell("&nbsp;");
        $table->endRow();
    /*
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_practicals_mark', 'practicals', 'Mark'), 120);
        $table->addCell($slider->show());
        $table->endRow();
    */
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_practicals_comment', 'practicals', 'Comment'));
        $table->addCell($textArea->show());
        $table->endRow();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_practicals_reviewedassgn', 'practicals', 'Reviewed Practical'));
        $table->addCell('<input type="file" name="lectfile">');
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell($button->show());
        $table->endRow();
        $form->addToForm($hiddenInput->show().$table->show());
        echo $form->show();
    }
}
else {
    // Online
    // Heading
    $header = new htmlHeading();
    $str = $this->objLanguage->code2Txt('mod_practicals_viewassgnby', 'practicals', NULL); //'View ssignment Submitted by [-person-] at [-time-]'
    $str = str_replace('[-person-]', $this->objUser->fullName($submission['userid']), $str);
    $str = str_replace('[-time-]', $objDateTime->formatDate($submission['datesubmitted']), $str);
    $header->str = $str;
    $header->type = 3;
    echo $header->show();
    // Content
    echo '<div style="border: 1px solid #000; padding: 10px;">'.$submission['online'].'</div>';
    if ($submission['mark'] != NULL  && ($practical['closing_date'] < date('Y-m-d H:i:s') || $this->isValid('edit'))) {
        // Header
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_practicals_result', 'practicals');
        $header->type = 3;
        echo $header->show();
        // Table
        $table = $this->newObject('htmltable', 'htmlelements');
        $objMark->value = $submission['mark'];
        $table->startRow();
        $table->addCell($objMark->show(), 120);
        $content = '<p><strong>'.$this->objLanguage->languageText('mod_practicals_mark', 'practicals', 'Mark').': '.$submission['mark'].'/'.$practical['mark'].'</strong></p>';
        $content .= '<p>'.nl2br($submission['commentinfo']).'</p>';
        $table->addCell($content);
        $table->endRow();
        echo $table->show();
    } else {
        $header = new htmlHeading();
        $header->str = '<em>'.$this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked').'</em>';
        $header->type = 3;
        echo $header->show();
    }
    if ($this->isValid('saveonlinemark')) {
        // Header
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_practicals_markassgn', 'practicals', 'Mark Practical');
        $header->type = 3;
        echo $header->show();
        // Form
        $form = new form ('_form', $this->uri(array('action'=>'saveonlinemark')));
        //$form->extra = 'enctype="multipart/form-data"';
        $hiddenInput = new hiddeninput('id', $submission['id']);
        $textArea = new textarea('commentinfo');
        $textArea->value = $submission['commentinfo'];
        $button = new button('savemark', $this->objLanguage->languageText('mod_practicals_markassgn', 'practicals', 'Mark Practical'));
        $button->setToSubmit();
/*
        $slider = $this->newObject('slider', 'htmlelements');
        $slider->value = $submission['mark'];
        $slider->maxValue = $practical['mark'];
*/
    	// Table
    	$table = $this->newObject('htmltable', 'htmlelements');
    	$objSubTable = new htmltable();
    	$objSubTable->width="60%";
    	//Insert mark
    	$objTextinput = new textinput('mark',is_null($submission['mark'])?'0':(int)$submission['mark']);
    	$objTextinput->size='5';
    	$objTextinput->extra=' maxlength=\'4\'';
    	$objSubTable->startRow();
    	$objSubTable->addCell($objTextinput->show().' / '.$practical['mark']." ".$this->objLanguage->languageText('mod_practicals_typeorslider', 'practicals', 'Mark'),'70%','','left');
    	$objSubTable->addCell("&nbsp;");
    	$objSubTable->endRow();
        $objSlider = $this->newObject('dhtmlgoodies_slider', 'dhtmlgoodies');
        $objSlider->setTargetId('slider_target');
        $objSlider->setFieldRef('document._form.mark');
        $objSlider->setWidth(200);
        $objSlider->setMin(0);
        $objSlider->setmax($practical['mark']);
     	$objSubTable->startRow();
    	//$objSubTable->addCell("&nbsp;",'70%','','left','',' id=\'slider_target\'');
    	$objSubTable->addCell('<span id=\'slider_target\'></span>'.$objSlider->show(),'70%','','left');
    	$objSubTable->addCell("&nbsp;");
    	$objSubTable->endRow();
        $table->startRow();
        //$table->addCell("&nbsp;");
        $table->addCell($this->objLanguage->languageText('mod_practicals_mark', 'practicals', 'Mark'), 120);
        $table->addCell($objSubTable->show());
        $table->endRow();
    	//Spacer
        $table->startRow();
        $table->addCell("&nbsp;");
        $table->addCell("&nbsp;");
        $table->endRow();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_practicals_comment', 'practicals', 'Comment'));
        $table->addCell($textArea->show());
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell($button->show());
        $table->endRow();
        $form->addToForm($hiddenInput->show().$table->show());
        echo $form->show();
    }
}
$link = new link($this->uri(array('action'=>'view', 'id'=>$practical['id'])));
$link->link = $this->objLanguage->languageText('mod_practicals_returntoassgn', 'practicals', 'Return to Practicals');
echo '<p>'.$link->show().'</p>';
//last portion of the slider's script
//function form_widget_amount_slider(targetElId,formTarget,width,min,max,onchangeAction)
//$sliderJS="
//    <script type=\"text/javascript\">
//    set_form_widget_amount_slider_handle('".$this->getResourceUri('slider_handle.gif')."');
//    form_widget_amount_slider('slider_target',document._form.mark,200,0,"..",false);
//    </script>
//";
//echo $sliderJS;
?>