<?php

echo '<h1>File Indexer</h1>';

$this->loadClass('link', 'htmlelements');

$objIndexFileProcessor = $this->getObject('indexfileprocessor');

$list = $objIndexFileProcessor->indexUserFiles($this->objUser->userId());

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
    echo '<div class="noRecordsMessage">All files in your directory are in the index.</div>';
}


?>