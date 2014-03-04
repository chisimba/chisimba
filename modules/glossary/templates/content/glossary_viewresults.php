<?php

//echo '<pre>';
//print_r($list);
//echo '</pre>';

$this->objGlossaryUrls =& $this->getObject('dbglossaryurls','glossary');
$this->objGlossarySeeAlsos =& $this->getObject('dbglossaryseealso','glossary');
$this->objGlossaryImages =& $this->getObject('dbglossaryimages','glossary');
$this->objUser = $this->getObject('user','security');

$objPop= $this->newObject('windowpop', 'htmlelements');

echo $header;

$this->title =& $this->getObject('htmlheading', 'htmlelements');
$this->title->type=3;
$this->title->str=$title;

echo $this->title->show();
// End Header

// Start of Table to Show List
$listTable=$this->newObject('htmltable','htmlelements');
$listTable->width='98%';
$listTable->attributes=' align="center" border="0"';
$listTable->cellspacing='0';
$listTable->cellpadding='5';


// Count Results
$resultsCount = 0;
//echo '<pre>';

if (!empty($list)) {
    foreach($list as $element)
    {

        $resultsCount++;

        // Edit Delete Buttons
        $editIcon =& $this->getObject('geticon', 'htmlelements');
        $editIcon->alt = $this->objLanguage->languageText('mod_glossary_edit', 'glossary');
        $editIcon->title = $this->objLanguage->languageText('mod_glossary_edit', 'glossary');
        $editIcon->setIcon('edit');
        $editLink = new link($this->uri(array('module'=>'glossary', 'action'=>'edit', 'id'=>$element['item_id'])));
        $editLink->link = $editIcon->show();
		
        // Delete Link to Delete Glossary Word
        $deleteIcon =& $this->getObject('geticon', 'htmlelements');
        $deleteIcon->setIcon('delete');
        $deleteIcon->alt = $this->objLanguage->languageText('mod_glossary_delete', 'glossary');
        $deleteIcon->title = $this->objLanguage->languageText('mod_glossary_delete', 'glossary');
        $deleteLink = new link($this->uri(array('module'=>'glossary', 'action'=>'delete', 'id'=>$element['item_id'])));
        $deleteLink->link = $deleteIcon->show();
		
        $listTable->trClass = ($resultsCount % 2) ? 'even' : 'odd';

        $listTable->startRow();

        $listTable->addCell('<strong>'.$element['term'].'</strong>', '100');

        $termInfo = $element['definition'];

		if($this->objUser->isLoggedIn()){
        	if ($this->isValid('edit')) {
            	$termInfo.= ' - '.$editLink->show();
        	}

        	if ($this->isValid('delete')) {
            	$termInfo.= ' '.$deleteLink->show();
        	}
		}
        //if ($element['seealsos'] != '' || $element['urls'] != '' || isset($element['images']) != '') {

        //$termInfo .= '<br />';

        //}

        //if ($element['seealsos'] != ''){
        //$listTable->addCell($termInfo);

        //echo '1 - '.$termInfo.'<br />';
        $seeAlsos = $this->objGlossarySeeAlso->fetchAllRecords($element['item_id']);

        if (count($seeAlsos) > 0) {

            $termInfo.= '<br />';
            $termInfo.= '<strong>'.$this->objLanguage->languageText('mod_glossary_seeAlso', 'glossary').':</strong> ';

            $comma = '';

            foreach($seeAlsos as $seeAlso)
            {

                // This Counter adds a space and comma after the first See Also term
                if (!empty($seeAlso['term1'])) {
                    $termInfo.= $comma;
                    if ($seeAlso['term1'] == $element['term']) {
                        $seeAlsoLink = new link($this->uri(array('module'=>'glossary',
                        'action'=>'search',
                        'term'=>$seeAlso['term2']
                        )));
                        $seeAlsoLink->link = $seeAlso['term2'];

                        $termInfo.= $seeAlsoLink->show();
                    } else {
                        $seeAlsoLink = new link($this->uri(array('module'=>'glossary',
                        'action'=>'search',
                        'term'=>$seeAlso['term1']
                        )));
                        $seeAlsoLink->link = $seeAlso['term1'];

                        $termInfo.= $seeAlsoLink->show();
                    }

                    $comma = ', ';
                }
            }

        }
        
        $urls = $this->objGlossaryUrls->fetchAllRecords($element['item_id']);
        //echo count($urls);

        if (count($urls) > 0) {
            $termInfo.= '<br />';
            $termInfo.= '<strong>'.$this->objLanguage->languageText('mod_glossary_urls', 'glossary').':</strong> ';

            $comma = '';


            foreach($urls as $url)
            {
                if (!empty($url['url'])) {
                    $termInfo .= $comma;
                    $urlLink = new link($url['url']);
                    $urlLink->link = $url['url'];
                    $urlLink->target = '_blank';
                    $termInfo.= $urlLink->show();
                    $comma = ', ';
                }
            }

        }

        //if (isset($element['images']) != '') {


        $images = $this->objGlossaryImages->getListImage($element['item_id']);

        if (count($images) > 0) {

            $termInfo.= '<br />';
            $termInfo.= '<strong>'.$objLanguage->languageText('word_images').' :</strong> ';

            $comma = '';

            foreach($images as $image)
            {
                if (!empty($image['image'])) {
                    $termInfo .= $comma;

                    $link = $this->uri(array('action' => 'previewimage', 'id' => $image['image'], 'fname' => $image['filename']));


                    $objPop->set('location',$link);
                    $objPop->set('window_name','previewImage');
                    $objPop->set('linktext',$image['caption']);
                    $objPop->set('width','200');
                    $objPop->set('height','200');
                    $objPop->set('resize','yes');

                    $termInfo.= $objPop->show();

                    $comma = ', ';
                }
            }
        }
        // Add TermInfo(Definition, See Also, Urls, Images)
        $listTable->addCell($termInfo);


        $listTable->endRow();
        
    } //End for each

    //if ($element['urls'] != '') {
    //	$listTable->addCell($termInfo);






    
}

echo $listTable->show();


if ($resultsCount == 0) {

echo('<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_glossary_noTermsFound', 'glossary').'</div>');
}
//echo ('<p>Found '.$resultsCount.' Results</p>');

echo $footer;


?>