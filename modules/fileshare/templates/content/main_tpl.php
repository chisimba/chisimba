<?php
    $objTable=$this->newObject('htmltable','htmlelements');
    $objTable->width=null;
    $objTable->border='0';
    $objTable->cellspacing='1';
    $objTable->cellpadding='1';

	// Page Title    
    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $pageTitle->str=
		ucwords(
			$this->objLanguage->code2Txt("mod_fileshare_heading",'fileshare')) 
			. " : " . $contextTitle
			. " : " . $workgroupDescription
	;
    
	// URL for upload icon
	$uri = $this->uri(array('action'=>'upload'));

	// Icon for upload
	$icon =& $this->getObject('geticon','htmlelements');
	$icon->setIcon('add');
	$icon->alt = $objLanguage->languageText('word_upload');
	$icon->align=false;
    
    $objTable->startRow();
	$objTable->addCell($pageTitle->show());
    $objTable->addCell("<a href=\"{$uri}\">".$icon->show()."</a>");
	$objTable->endRow();
	
    echo $objTable->show();

	//$objConfig =& $this->getObject('altconfig', 'config');
	$siteRoot = $this->objConfig->getsiteRoot();
    $contentPath = $this->objConfig->getcontentPath();
    $contentPath = substr($contentPath,0,strlen($contentPath)-1);
    	
    $files=$this->objDbFileShare->listAll($contextCode, $workgroupId);

	$objTable=$this->newObject('htmltable','htmlelements');	
	$objTable->cellspacing='2';
	$objTable->cellpadding='2';
	
	$objTable->startHeaderRow();
	$objTable->addHeaderCell($objLanguage->languageText('mod_fileshare_filename','fileshare'));
	//$objTable->addHeaderCell($objLanguage->languageText('mod_fileshare_size','fileshare'));
	$objTable->addHeaderCell($objLanguage->languageText('mod_fileshare_title','fileshare'));
	$objTable->addHeaderCell($objLanguage->languageText('mod_fileshare_description','fileshare'));
	$objTable->addHeaderCell($objLanguage->languageText('mod_fileshare_version','fileshare'));
	$objTable->addHeaderCell('&nbsp;');
	$objTable->addHeaderCell('&nbsp;');
	$objTable->endHeaderRow();    

	if (empty($files)) {
		$objTable->startRow();
		$objTable->addCell("<div align=\"center\" class=\"noRecordsMessage\">".$objLanguage->languageText('mod_fileshare_norecords','fileshare')."</div>",null,null,null,null,'colspan="7"');
		$objTable->endRow();
	}
	else {
		$rowClass = 'odd';
        foreach ($files as $file)
        {
            $id=$file['id'];
            $filename=$file['filename'];
			//$filesize=$file['filesize'];
			$title=$file['title'];
			$description=$file['description'];
			$version=$file['version'];
			$path = $file['path'];
			//
            //$uri="{$siteRoot}{$contentRoot}/content/$contextCode/workgroup/$workgroupId/$filetype/$filename";
			$uri="{$siteRoot}{$contentPath}/$path";
			$iconDownload = $this->getObject('geticon','htmlelements');
			$iconDownload->setIcon('download');
			$iconDownload->alt = $objLanguage->languageText('mod_fileshare_download','fileshare');
			$iconDownload->align=false;
            $downloadlink="<a href='$uri'>".$iconDownload->show()."</a>";
			//
			$uri = $this->uri(array('action'=>'edit','id'=>$id));
	   		$iconEdit =& $this->getObject('geticon','htmlelements');
	   		$iconEdit->setIcon('edit');
	   		$iconEdit->alt = $objLanguage->languageText('word_edit');
	   		$iconEdit->align=false;		
			$editlink = "<a href='$uri'>".$iconEdit->show()."</a>";
			//
            $uri=$this->uri(array('action'=>'delete','id'=>$id));
	   		$iconDelete =& $this->getObject('geticon','htmlelements');
	   		$iconDelete->setIcon('delete');
	   		$iconDelete->alt = $objLanguage->languageText('word_delete');
	   		$iconDelete->align=false;		
	        $objConfirm =& $this->newObject('confirm','utilities');
	        $objConfirm->setConfirm(
	            $iconDelete->show(),
				$uri,
				$objLanguage->languageText('mod_fileshare_suredelete','fileshare')
			);
            $deletelink=$objConfirm->show();
			$objTable->startRow($rowClass);
			$objTable->addCell($filename);
			//$objTable->addCell($filesize);
			$objTable->addCell($title);
			$objTable->addCell($description);
			$objTable->addCell($version);
			$objTable->addCell($downloadlink);
			$objTable->addCell($editlink.$deletelink);
			$objTable->endRow();
			$rowClass = $rowClass == 'even' ? 'odd' : 'even';
        }
    }
	echo $objTable->show();
	
    // Show link to return to workgroup.
    $uri = $this->uri(array(),'workgroup');
    echo("<a href=\"".$uri."\">".$this->objLanguage->code2Txt('mod_fileshare_return','fileshare')."</a>");                                        
?>
