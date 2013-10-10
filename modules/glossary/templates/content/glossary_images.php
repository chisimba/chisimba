<?php
//?

$objPop= $this->newObject('windowpop', 'htmlelements');

$objIcon =& $this->getObject('geticon', 'htmlelements');
$objIcon->alt = $this->objLanguage->languageText('mod_glossary_delete', 'glossary');
$objIcon->title = $this->objLanguage->languageText('mod_glossary_delete', 'glossary');
$objIcon->setIcon('delete');

$listTable=$this->newObject('htmltable','htmlelements');
$listTable->width='80%';
$listTable->attributes=" border=0";
$listTable->cellspacing='0';
$listTable->cellpadding='5';

echo $objPop->putJs(); // you only need to do this once per page

//print_r($images);


if (count($images) < 1) {
    $listTable->addCell('<p>No Images for this Item</p>');
} else {

    foreach ($images as $image)
    {
    
        $listTable->startRow();
    
        $link = $this->uri(array('action' => 'previewimage', 'id' => $image['id']), 'contextresources');
        
       $objPop->set('location',$link);
       $objPop->set('window_name','previewImage');
       $objPop->set('linktext',$image['filename']);
       $objPop->set('width','200'); 
       $objPop->set('height','200');
       $objPop->set('left','100');
       $objPop->set('top','100');
       
        $objConfirm=&$this->newObject('confirm','utilities');
        
        $url = $this->uri(array('action'=>'deletefromelement', 'id' => $image['bridge_id'],  'returnmodule' => 'glossary', 'returnaction' => 'listimages', 'returnid'     => $id	), 'contextresources'); 
        
        $objConfirm->setConfirm($objIcon->show(),$url,$this->objLanguage->languageText('mod_glossary_areyousuredeleteimage', 'glossary'));
        
        $listTable->addCell($objPop->show());
        $listTable->addCell($objConfirm->show());
        
        //echo ('<li>'.$objPop->show().' '.$objConfirm->show().'</li>');
        
        $listTable->endRow();
    
    }

} // End - If Statement

$link = $this->uri(array('action' => 'popupresources', 'elementid' => $id, 'thismodule'=>'glossary', 'list'=>'images', 'context'=>$contextCode ), 'contextresources');


$objPop->set('location', $link);
$objPop->set('linktext',$this->objLanguage->languageText('mod_glossary_adduploadimage', 'glossary'));
$objPop->set('window_name','contextresources');
$objPop->set('width','600'); 
$objPop->set('height','400');
$objPop->set('left','100');
$objPop->set('top','100');

$listTable->startRow();
$listTable->addCell('&nbsp;');
$listTable->addCell('&nbsp;');
$listTable->endRow();

$listTable->startRow();
$listTable->addCell('<p>'.$objPop->show().'</p>');
$listTable->addCell('&nbsp;');
$listTable->endRow();

echo ($listTable->show());
?>