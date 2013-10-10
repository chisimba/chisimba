<?php
$ret = "";
$isLecturerRole = $this->objUser->isCourseAdmin($this->contextCode);
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
// Section 1
// Heading
$header = new htmlHeading();
$header->str = $assignment['name'];
$header->type = 1;
$ret .= $header->show();
// Table
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('word_description', 'system', 'Description').'</strong>', 130);
$table->addCell($objWashout->parseText($assignment['description']), NULL, NULL, NULL, NULL, ' colspan="3"');
$table->endRow();
$table->startRow();
$table->addCell('<strong>'.ucwords($this->objLanguage->code2Txt('mod_assignment_lecturer', 'assignment', NULL, '[-author-]')).':</strong>', 130);
$table->addCell($this->objUser->fullName($assignment['userid']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_totalmark', 'assignment').'</strong>', 130);
$table->addCell($assignment['mark']);
$table->endRow();
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_openingdate', 'assignment', 'Opening Date').'</strong>', 130);
$table->addCell($objDateTime->formatDate($assignment['opening_date']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_percentyrmark', 'assignment', 'Percentage of year mark').':</strong>', 200, NULL, NULL, 'nowrap');
$table->addCell($assignment['percentage'].'%');
$table->endRow();
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date').'</strong>', 130);
$table->addCell($objDateTime->formatDate($assignment['closing_date']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_assignmenttype', 'assignment', 'Assignment Type').'</strong>', 130);
if ($assignment['format'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_assignment_online', 'assignment', 'Online'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_assignment_upload', 'assignment', 'Upload'));
}
$table->endRow();
$ret .= $table->show();
// Section 2
$objIcon = $this->getObject('geticon', 'htmlelements');
$objMark = $this->getObject('markimage', 'utilities');
//
$isMarked = $submission['mark'] != NULL && $assignment['closing_date'] < date('Y-m-d H:i:s');
//
if ($assignment['format'] == '1') {
    // Upload
    define('ASSIGNMENT_FT_STUDENT',1);
    define('ASSIGNMENT_FT_LECTURER',2);
    if ($isLecturerRole) {
        $fileType = ASSIGNMENT_FT_STUDENT;
    }
    else {
        if (!$isMarked) {
            $fileType = ASSIGNMENT_FT_STUDENT;
        } else {
            $fileType = ASSIGNMENT_FT_LECTURER;
        }
    }
    switch($fileType){
        case ASSIGNMENT_FT_LECTURER:
            $fileId = $submission['lecturerfileid'];
            break;
        case ASSIGNMENT_FT_STUDENT:
            $fileId = $submission['studentfileid'];
            break;
        default:
            ;
    } // switch
    if (is_null($fileId)) {
        $header = new htmlHeading();
        if ($fileType == ASSIGNMENT_FT_STUDENT) {
            $str = '<em>'.$this->objLanguage->languageText('mod_assignment_noassignmentavailable', 'assignment').'</em>';
        } else if ($fileType == ASSIGNMENT_FT_LECTURER) {
            $str = '<em>'.$this->objLanguage->languageText('mod_assignment_nomarkedassignmentavailable', 'assignment').'</em>';
        } else {
            $str = 'Unkown assignment filetype!';
        }
        $header->str = $str;
        $header->type = 3;
        $ret .= $header->show();
    } else {
        // Header
        $header = new htmlHeading();
        if ($fileType == ASSIGNMENT_FT_STUDENT) {
            $str = $this->objLanguage->code2Txt('mod_assignment_viewassgnby', 'assignment', NULL); //'View ssignment Submitted by [-person-] at [-time-]'
            $str = str_replace('[-person-]', $this->objUser->fullName($submission['userid']), $str);
            $str = str_replace('[-time-]', $objDateTime->formatDate($submission['datesubmitted']), $str);
        } else if ($fileType == ASSIGNMENT_FT_LECTURER) {
            $str = $this->objLanguage->code2Txt('mod_assignment_viewmarkedassignment', 'assignment', NULL); //'View ssignment Submitted by [-person-] at [-time-]'
            $str = str_replace('[-person-]', $this->objUser->fullName($submission['userid']), $str);
            $str = str_replace('[-time-]', $objDateTime->formatDate($submission['datesubmitted']), $str);
        }
        $header->str = $str;
        $header->type = 3;
        $ret .= $header->show();
        // Content
        $objFile = $this->getObject('dbfile', 'filemanager');
        $fileName = $objFile->getFileName($fileId);
        $downloadLink = new link ($this->uri(array('action'=>'downloadfile', 'id'=>$submission['id'], 'fileid'=>$fileId)));
        $downloadLink->link = $this->objLanguage->languageText('word_download', 'system', 'Download');
        $objFileIcon = $this->getObject('fileicons', 'files');
        $ret .= '<p class="assignment_link_filedn">'.$objFileIcon->getFileIcon($fileName).' '.$downloadLink->show().'</p>';
        $filePath = $this->objAssignmentSubmit->getAssignmentFilename($submission['id'], $fileId);
        $submissionId = $submission['id'];
        $sysTemp = sys_get_temp_dir();
        if ($sysTemp[strlen($sysTemp)-1] != DIRECTORY_SEPARATOR) {
            $sysTemp .= DIRECTORY_SEPARATOR;
        }
        $tempFilePath = $sysTemp.'chisimba'.DIRECTORY_SEPARATOR.$this->objConfig->serverName().DIRECTORY_SEPARATOR.'assignment'.DIRECTORY_SEPARATOR.'submissions'.DIRECTORY_SEPARATOR.$submissionId; //'/'.$fileName;
        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $tempFilePath = $objCleanUrl->cleanUpUrl($tempFilePath);
        $objMkdir = $this->getObject('mkdir', 'files');
        $objMkdir->mkdirs($tempFilePath);
        chmod($tempFilePath, 0777);
        $tempFile = $tempFilePath . '/' . $fileName . '.html';
        $destinationHtml = $tempFile; //$filePath.'.html';
        // PHP file which will contain the assignment
        $destinationPhp = $filePath.'.php';
        // Check if the file exists, else we need to convert the document
        if (!file_exists($destinationPhp)) {
            // Convert Document
            $objConvert = $this->getObject('convertdoc', 'documentconverter');
            $conversionOK = $objConvert->convert($filePath, $destinationHtml);
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
            $ret .= $header->show();
            $iframe = new iframe();
            $iframe->width = '100%';
            $iframe->height = 400;
            //$iframe->src = $this->uri(array('action'=>'viewhtmlsubmission', '1'=>'1', '2'=>'2', '3'=>'3'));
            $iframe->src = $this->uri(array('action'=>'viewhtmlsubmission', 'id'=>$submission['id'],'fileid'=>$fileId));
            $ret .= $iframe->show();
        }
    }
    if ($submission['mark'] != NULL && ($assignment['closing_date'] < date('Y-m-d H:i:s') || $this->isValid('edit'))) {
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_assignment_result', 'assignment');
        $header->type = 3;
        $ret .= $header->show();
        $table = $this->newObject('htmltable', 'htmlelements');
        $objMark->value = $submission['mark'];
        $table->startRow();
        $table->addCell($objMark->show(), 120);
        $content = '<p><strong>'.$this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark').': '.$submission['mark'].'/'.$assignment['mark'].'</strong></p>';
        $content .= '<p>'.nl2br($submission['commentinfo']).'</p>';
        $table->addCell($content);
        $table->endRow();
        $ret .= $table->show();
    } else {
        $header = new htmlHeading();
        $header->str = '<em>'.$this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked').'</em>';
        $header->type = 3;
        $ret .= $header->show();
    }
    if ($this->isValid('saveuploadmark')) {
        // Header
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_assignment_markassgn', 'assignment', 'Mark Assignment');
        $header->type = 3;
        $ret .= $header->show();
        // Form
        $form = new form ('_form', $this->uri(array('action'=>'saveuploadmark')));
        $form->extra = 'enctype="multipart/form-data"';
        $hiddenInput = new hiddeninput('id', $submission['id']);
        $textArea = new textarea('commentinfo');
        $textArea->value = $submission['commentinfo'];
        $button = new button('savemark', $this->objLanguage->languageText('mod_assignment_markassgn', 'assignment', 'Mark Assignment'));
        $button->setToSubmit();
    	//Setup Tables
    	$table = $this->newObject('htmltable', 'htmlelements');
    	$objSubTable = new htmltable();
    	$objSubTable->width="60%";
    	//Insert mark
        $objTextinput = new textinput('mark',is_null($submission['mark'])?'0':(int)$submission['mark']);
    	$objTextinput->size='5';
    	$objTextinput->extra=' maxlength=\'4\'';
    	$objSubTable->startRow();
    	$objSubTable->addCell($objTextinput->show().' / '.$assignment['mark']." ".$this->objLanguage->languageText('mod_assignment_typeorslider', 'assignment', 'Mark'),'70%','','left');
    	$objSubTable->addCell("&nbsp;");
    	$objSubTable->endRow();
     	$objSubTable->startRow();
        $objSlider = $this->newObject('dhtmlgoodies_slider', 'dhtmlgoodies');
        $objSlider->setTargetId('slider_target');
        $objSlider->setFieldRef('document._form.mark');
        $objSlider->setWidth(200);
        $objSlider->setMin(0);
        $objSlider->setmax($assignment['mark']);
    	$objSubTable->addCell('<span id=\'slider_target\'></span>'.$objSlider->show(),'70%','','left');
    	$objSubTable->addCell("&nbsp;");
    	$objSubTable->endRow();
        $table->startRow();
        //$table->addCell("&nbsp;");
        $table->addCell($this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark'), 120);
        $table->addCell($objSubTable->show());
        $table->endRow();
    	//Spacer
        $table->startRow();
        $table->addCell("&nbsp;");
        $table->addCell("&nbsp;");
        $table->endRow();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_assignment_comment', 'assignment', 'Comment'));
        $table->addCell($textArea->show());
        $table->endRow();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_assignment_reviewedassgn', 'assignment', 'Reviewed Assignment'));
        $table->addCell('<input type="file" name="lectfile">');
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell($button->show());
        $table->endRow();
        $form->addToForm($hiddenInput->show().$table->show());
        $ret .= $form->show();
    }
} else {
    // Online
    // Heading
    $header = new htmlHeading();
    $str = $this->objLanguage->code2Txt('mod_assignment_viewassgnby', 'assignment', NULL); //'View ssignment Submitted by [-person-] at [-time-]'
    $str = str_replace('[-person-]', $this->objUser->fullName($submission['userid']), $str);
    $str = str_replace('[-time-]', $objDateTime->formatDate($submission['datesubmitted']), $str);
    $header->str = $str;
    $header->type = 3;
    $ret .= $header->show();
    // Content
    $ret .= '<div style="border: 1px solid #000; padding: 10px;">'.$submission['online'].'</div>';
    if ($submission['mark'] != NULL  && ($assignment['closing_date'] < date('Y-m-d H:i:s') || $this->isValid('edit'))) {
        // Header
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_assignment_result', 'assignment');
        $header->type = 3;
        $ret .= $header->show();
        // Table
        $table = $this->newObject('htmltable', 'htmlelements');
        $objMark->value = $submission['mark'];
        $table->startRow();
        $table->addCell($objMark->show(), 120);
        $content = '<p><strong>'.$this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark').': '.$submission['mark'].'/'.$assignment['mark'].'</strong></p>';
        $content .= '<p>'.nl2br($submission['commentinfo']).'</p>';
        $table->addCell($content);
        $table->endRow();
        $ret .= $table->show();
    } else {
        $header = new htmlHeading();
        $header->str = '<em>'.$this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked').'</em>';
        $header->type = 3;
        $ret .= $header->show();
    }
    if ($this->isValid('saveonlinemark')) {
        // Header
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_assignment_markassgn', 'assignment', 'Mark Assignment');
        $header->type = 3;
        $ret .= $header->show();
        // Form
        $form = new form ('_form', $this->uri(array('action'=>'saveonlinemark')));
        //$form->extra = 'enctype="multipart/form-data"';
        $hiddenInput = new hiddeninput('id', $submission['id']);
        $textArea = new textarea('commentinfo');
        $textArea->value = $submission['commentinfo'];
        $button = new button('savemark', $this->objLanguage->languageText('mod_assignment_markassgn', 'assignment', 'Mark Assignment'));
        $button->setToSubmit();
    	// Table
    	$table = $this->newObject('htmltable', 'htmlelements');
    	$objSubTable = new htmltable();
    	$objSubTable->width="60%";
    	//Insert mark
    	$objTextinput = new textinput('mark',is_null($submission['mark'])?'0':(int)$submission['mark']);
    	$objTextinput->size='5';
    	$objTextinput->extra=' maxlength=\'4\'';
    	$objSubTable->startRow();
    	$objSubTable->addCell($objTextinput->show().' / '.$assignment['mark']." ".$this->objLanguage->languageText('mod_assignment_typeorslider', 'assignment', 'Mark'),'70%','','left');
    	$objSubTable->addCell("&nbsp;");
    	$objSubTable->endRow();
        $objSlider = $this->newObject('dhtmlgoodies_slider', 'dhtmlgoodies');
        $objSlider->setTargetId('slider_target');
        $objSlider->setFieldRef('document._form.mark');
        $objSlider->setWidth(200);
        $objSlider->setMin(0);
        $objSlider->setmax($assignment['mark']);
     	$objSubTable->startRow();
    	//$objSubTable->addCell("&nbsp;",'70%','','left','',' id=\'slider_target\'');
    	$objSubTable->addCell('<span id=\'slider_target\'></span>'.$objSlider->show(),'70%','','left');
    	$objSubTable->addCell("&nbsp;");
    	$objSubTable->endRow();
        $table->startRow();
        //$table->addCell("&nbsp;");
        $table->addCell($this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark'), 120);
        $table->addCell($objSubTable->show());
        $table->endRow();
    	//Spacer
        $table->startRow();
        $table->addCell("&nbsp;");
        $table->addCell("&nbsp;");
        $table->endRow();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_assignment_comment', 'assignment', 'Comment'));
        $table->addCell($textArea->show());
        $table->endRow();
        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell($button->show());
        $table->endRow();
        $form->addToForm($hiddenInput->show().$table->show());
        $ret .= $form->show();
    }
}
$link = new link($this->uri(array('action'=>'view', 'id'=>$assignment['id'])));
$link->link = $this->objLanguage->languageText('mod_assignment_returntoassgn', 'assignment', 'Return to Assignment');
$ret .= '<p class="assignment_link_return">'. $link->show().'</p>';
echo "<div class='assignment_main'>$ret</div>";
?>