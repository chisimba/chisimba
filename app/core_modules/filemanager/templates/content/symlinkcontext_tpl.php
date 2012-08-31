<?php

$this->objFileIcons = $this->getObject('fileicons', 'files');
$this->loadClass('form', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

// echo '<pre>';
// print_r($_POST);
// echo '</pre>';



if ($this->getParam('files') == NULL || !is_array($this->getParam('files')) || count($this->getParam('files')) == 0) {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_filemanager_nofileswereselected', 'filemanager', 'No Files Were Selected').'</div>';
    echo '<p><a href="javascript:history.back()">'.$this->objLanguage->languageText('mod_filemanager_backtopreviouspage', 'filemanager', 'Back to Previous Page').'</a> / ';
    echo '<a href="'.$this->uri(NULL).'">'.$this->objLanguage->languageText('mod_filemanager_returntofilemanager', 'filemanager', 'Return to File Manager').'</a></p>';
    
    
    
} else {
    
    
    $files = $this->getParam('files');
    
    $form = new form('confirmsymlink', $this->uri(array('action'=>'symlinkconfirm')));
    
    $folderIcon = $this->objFileIcons->getExtensionIcon('folder');
    
    $form->addToForm ('<ul>');
    
    $counter = 0;
    $folderCounter = 0;
    
    foreach ($files as $file)
    {
        
        if (substr($file, 0, 8) == 'folder__') {
            $folderCounter++;
        } else {
            $fileDetails = $this->objFiles->getFile($file);
            
            if ($fileDetails != FALSE) {
            
                $counter++;
                
                $checkbox = new checkbox('files[]', htmlentities($fileDetails['filename']), TRUE);
                $checkbox->value = $file;
                $form->addToForm ('<li>'.$checkbox->show().' '.htmlentities($fileDetails['filename']).'</li>');
            }
        }
    }
    
    $form->addToForm ('</ul>');
    
    $form->addToForm($this->objFolders->getTree('context', $this->contextCode, 'htmldropdown'));
    
    $button = new button ('submitform', $this->objLanguage->code2Txt('mod_filemanager_attachtocontext', 'filemanager', NULL, 'Attach to [-context-]'));
    $button->setToSubmit();
    
    $form->addToForm ('<br />'.$button->show());
    
    $folderInput = new hiddeninput('folder', $this->getParam('folder'));
    $this->setVar('folderId', $this->getParam('folder'));
    
    $form->addToForm($folderInput->show());
    
    if ($counter > 0) {
        echo '<h1>'.$this->objLanguage->code2Txt('mod_filemanager_attachtocontext', 'filemanager', NULL, 'Attach to [-context-]').'?</h1>';
        echo '<p>'.$this->objLanguage->languageText('mod_filemanager_selectfolderattachfiles', 'filemanager', 'Please select the folder you would like these files to be placed in').'</p>';
        echo $form->show();
    } else {
        echo '<h1 class="error">'.$this->objLanguage->languageText('word_error', 'system', 'Error').':</h1>';
        echo '<p>xx'.$this->objLanguage->languageText('mod_filemanager_warnfilesnolonger', 'filemanager', 'The files/folders you have attempted to delete no longer exist').'.</p>';
    }
}
      
?>