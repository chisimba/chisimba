<?php


if (count($results) == 0) {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_filemanager_noresultsquotasearch', 'filemanager', 'No results matching search criteria found').'</div>';
} else {
    $this->loadClass('formatfilesize', 'files');
    $this->loadClass('link', 'htmlelements');
    $objFileSize = new formatfilesize();
    
    $objIcon = $this->newObject('geticon', 'htmlelements');
    $objIcon->setIcon('edit');
    
    $editIcon = $objIcon->show();
    
    if ($searchType == 'context') {
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startHeaderRow();
        $table->addHeaderCell(ucwords($this->objLanguage->code2Txt('mod_context_contextcode', 'context', NULL, '[-context-] Code')));
        $table->addHeaderCell(ucwords($this->objLanguage->code2Txt('mod_context_contexttitle', 'context', NULL, '[-context-] Title')));
        $table->addHeaderCell($this->objLanguage->languageText('mod_filemanager_usedefault', 'filemanager', 'Use default'), 100);
        $table->addHeaderCell($this->objLanguage->languageText('mod_filemanager_quota', 'filemanager', 'Quota'), 100);
        $table->addHeaderCell($this->objLanguage->languageText('mod_filemanager_usage', 'filemanager', 'Usage'), 100);
        $table->addHeaderCell($this->objLanguage->languageText('mod_filemanager_graph', 'filemanager', 'Graph'), 200);
        $table->addHeaderCell('&nbsp;', 50);
        $table->endHeaderRow();
        
        foreach ($results as $result)
        {
            $table->startRow();
            $table->addCell($result['contextcode']);
            $table->addCell($result['title']);
            $table->addCell($result['usedefault']);
            
            if ($result['usedefault'] == 'Y') {
                $quota = $defaultQuota;
            } else {
                $quota = $result['quota'];
            }
            
            $table->addCell($quota.' MB');
            $table->addCell($objFileSize->formatsize($result['quotausage']));
            $table->addCell($this->objQuotas->generateQuotaGraph($quota*1024*1024, $result['quotausage'])); // Quota converted to bytes inline
            
            $link = new link ($this->uri(array('action'=>'editquota', 'id'=>$result['id'])));
            $link->link = $editIcon;
            
            $table->addCell('&nbsp; '.$link->show());
            
            $table->endRow();
        }
        
        echo $table->show();
    } else {
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startHeaderRow();
        $table->addHeaderCell($this->objLanguage->languageText('phrase_firstname', 'system', 'First Name'));
        $table->addHeaderCell($this->objLanguage->languageText('word_surname', 'system', 'Surname'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_filemanager_usedefault', 'filemanager', 'Use default'), 100);
        $table->addHeaderCell($this->objLanguage->languageText('mod_filemanager_quota', 'filemanager', 'Quota'), 100);
        $table->addHeaderCell($this->objLanguage->languageText('mod_filemanager_usage', 'filemanager', 'Usage'), 100);
        $table->addHeaderCell($this->objLanguage->languageText('mod_filemanager_graph', 'filemanager', 'Graph'), 200);
        $table->addHeaderCell('&nbsp;', 50);
        $table->endHeaderRow();
        
        foreach ($results as $result)
        {
            $table->startRow();
            $table->addCell($result['firstname']);
            $table->addCell($result['surname']);
            $table->addCell($result['usedefault']);
            
            if ($result['usedefault'] == 'Y') {
                $quota = $defaultQuota;
            } else {
                $quota = $result['quota'];
            }
            
            $table->addCell($quota.' MB');
            $table->addCell($objFileSize->formatsize($result['quotausage']));
            $table->addCell($this->objQuotas->generateQuotaGraph($quota*1024*1024, $result['quotausage'])); // Quota converted to bytes inline
            
            $link = new link ($this->uri(array('action'=>'editquota', 'id'=>$result['id'])));
            $link->link = $editIcon;
            
            $table->addCell('&nbsp; '.$link->show());
            
            $table->endRow();
        }
        
        echo $table->show();
    }
    
    
}



?>