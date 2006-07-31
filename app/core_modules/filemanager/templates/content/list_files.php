<?php
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('formatfilesize', 'files');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_filemanager_listof'.$category, 'filemanager');

echo $header->show();

$this->objFormatDateTime =& $this->getObject('datetime', 'utilities');
$objIcon = $this->getObject('geticon', 'htmlelements');

echo $successMessage;
echo $errorMessage;

$objFilePreview =& $this->getObject('filepreview');

$objFileIcons =& $this->getObject('fileicons', 'files');

if (count($files) == 0) {
    echo '<p>'.$this->objLanguage->languageText('mod_filemanager_nofilesuploaded', 'filemanager', 'No files uploaded').'</p>';
} else {

$table = $this->newObject('htmltable', 'htmlelements');
$table->cellpadding = '3';

$table->startHeaderRow();
$table->addHeaderCell('&nbsp;', 20);
$table->addHeaderCell($this->objLanguage->languageText('word_filename', 'filemanager', 'Filename'));
$table->addHeaderCell($this->objLanguage->languageText('word_size', 'filemanager', 'Size'), '60', NULL, 'center', NULL);
//$table->addHeaderCell('Description');
$table->addHeaderCell($this->objLanguage->languageText('phrase_dateuploaded', 'filemanager', 'Date Uploaded'), NULL, NULL, NULL, 'nowrap');
$table->addHeaderCell('&nbsp;', 30);
$table->endHeaderRow();

$filesize = new formatfilesize();

$objIcon->setIcon('download');
$downloadIcon = $objIcon->show();

    foreach ($files as $file)
    {
        $link = new link ($this->uri(array('action'=>'fileinfo', 'id'=>$file['id'], 'type'=>$file['category'], 'filename'=>$file['filename'])));
        $link->link = str_replace('_', ' ', $file['filename']);
        
        $table->startRow();
        $table->addCell($objFileIcons->getFileIcon($file['filename']), 20);
        $table->addCell($link->show());
        
        $table->addCell($filesize->formatsize($file['filesize']), '60', NULL, 'right', 'nowrap');
        //$table->addCell($file['description']);
        $table->addCell($this->objFormatDateTime->formatDateOnly($file['datecreated']).' - '.$this->objFormatDateTime->formatTime($file['timecreated']), NULL, NULL, 'right', 'nowrap');
        
        $link = new link ($this->uri(array('action'=>'file', 'id'=>$file['id'], 'type'=>$file['category'], 'filename'=>$file['filename'])));
        $link->link = $downloadIcon;
        
        $table->addCell($link->show(), '30');
        
        $table->endRow();
    }

echo $table->show();
}


echo $this->objUpload->show();

?>