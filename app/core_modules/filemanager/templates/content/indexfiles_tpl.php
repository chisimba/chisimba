<?php

$this->setVar('pageSuppressXML', TRUE);

echo '<h1>'.$this->objLanguage->languageText('mod_filemanager_fileindexer', 'filemanager', 'File Indexer').'</h1>';

$this->loadClass('link', 'htmlelements');



if (count($list) > 0) {
    echo '<ul>';
    foreach ($list as $item)
    {
        $record = $this->objFiles->getFile($item);
        
        if ($record != FALSE) {
            $link = new link ($this->uri(array('action'=>'fileinfo', 'id'=>$item)));
            $link->link = $record['filename'];
            echo '<li>'.$link->show().'</li>';
        }
    }
    echo '</ul>';
} else {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_filemanager_allyourfilesareintheindex', 'filemanager', 'All files in your directory are in the index').'.</div>';
}


?>