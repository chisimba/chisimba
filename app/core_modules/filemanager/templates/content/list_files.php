<?php
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('formatfilesize', 'files');

$this->loadClass('form', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_filemanager_listof'.$category, 'filemanager');

echo $header->show();

$this->objFormatDateTime = $this->getObject('dateandtime', 'utilities');
$objIcon = $this->newObject('geticon', 'htmlelements');

echo $successMessage;
echo $errorMessage;

$objFilePreview = $this->getObject('filepreview');

$objFileIcons = $this->getObject('fileicons', 'files');

if (count($files) == 0) {
    echo '<p>'.$this->objLanguage->languageText('mod_filemanager_nofilesuploaded', 'filemanager', 'No files uploaded').'</p>';
} else {

    $this->appendArrayVar('headerParams', $this->getJavascriptFile('selectall.js', 'htmlelements'));

    $table = $this->newObject('htmltable', 'htmlelements');
    $table->cellpadding = '3';

    $table->startHeaderRow();
    $table->addHeaderCell('&nbsp;', 20);
    $table->addHeaderCell('&nbsp;', 20);
    $table->addHeaderCell($this->objLanguage->languageText('word_filename', 'filemanager', 'Filename'));
    $table->addHeaderCell($this->objLanguage->languageText('word_size', 'filemanager', 'Size'), '60', NULL, 'center', NULL);
    //$table->addHeaderCell('Description');
    //$table->addHeaderCell($this->objLanguage->languageText('phrase_dateuploaded', 'filemanager', 'Date Uploaded'), NULL, NULL, NULL, 'nowrap');
    $table->addHeaderCell('&nbsp;', 30);
    $table->endHeaderRow();

    $filesize = new formatfilesize();

    $objIcon->setIcon('download');
    $downloadIcon = $objIcon->show();

    $userfilespath = $this->objConfig->getcontentPath();

        foreach ($files as $file)
        {
            $link = new link ($this->uri(array('action'=>'fileinfo', 'id'=>$file['id'], 'type'=>$file['category'], 'filename'=>$file['filename'])));
            $link->link = htmlentities(str_replace('_', ' ', $file['filename']));

            $table->startRow();

            $checkbox = new checkbox('files[]');
            $checkbox->value = $file['id'];
            $checkbox->cssId = htmlentities('input_files_'.$file['filename']);

            $table->addCell($checkbox->show(), 20);

            $label = new label($objFileIcons->getFileIcon($file['filename']), htmlentities('input_files_'.$file['filename']));
            $table->addCell($label->show(), 20);
            $table->addCell($link->show());

            $table->addCell($filesize->formatsize($file['filesize']), '60', NULL, 'right', 'nowrap');
            //$table->addCell($file['description']);
            //$table->addCell($this->objFormatDateTime->formatDateOnly($file['datecreated']).' - '.$this->objFormatDateTime->formatTime($file['timecreated']), NULL, NULL, 'right', 'nowrap');

            $fileDownloadPath = $userfilespath.$file['path'];
            $fileDownloadPath = $this->objCleanUrl->cleanUpUrl($fileDownloadPath);

            $link = new link ($fileDownloadPath);
            $link->link = $downloadIcon;

            $table->addCell($link->show(), '30');

            $table->endRow();
        }

    $form = new form('deletefiles', $this->uri(array('action'=>'multidelete')));
    $form->addToForm($table->show());

    $button = new button ('submitform', $this->objLanguage->languageText('mod_filemanager_deleteselecteditems', 'filemanager', 'Delete Selected Items'));
    $button->setToSubmit();

    $selectallbutton = new button ('selectall', $this->objLanguage->languageText('phrase_selectall', 'system', 'Select All'));
    $selectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', true);");

    $deselectallbutton = new button ('deselectall', $this->objLanguage->languageText('phrase_deselectall', 'system', 'Deselect all'));
    $deselectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', false);");

    $form->addToForm($button->show().' &nbsp; &nbsp; '.$selectallbutton->show().' '.$deselectallbutton->show());

    echo $form->show();
}


echo $this->objUpload->show();

?>