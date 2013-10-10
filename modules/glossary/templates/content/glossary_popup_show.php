<?php


$extraCss = '
<style  type="text/css">
body {
	overflow-x: hidden;
	overflow-y: auto;
    width:95%;
} 
</style>';

$this->appendArrayVar('headerParams', $extraCss);

// Create Header Tag ' Edit Term
$this->titleAddTerm =& $this->getObject('htmlheading', 'htmlelements');
$this->titleAddTerm->type=1;
$this->titleAddTerm->str=ucwords($record['term']);
echo $this->titleAddTerm->show();

echo ('<p>'.$record['definition'].'</p>');


if ($seeAlsoNum > 0)
{ 
	echo ('<p><strong>'.$objLanguage->languageText('mod_glossary_seeAlso', 'glossary').':<strong> ');
	
	$count=1;
	
	foreach ($seeAlsoList as $element) {
		
			if ($count > 1) {
				echo (', ');
			}
			
			if ($element['item_id'] != $id) {
				
				$seeAlsoLink = new link($this->uri(array(
						'module'=>'glossary', 
						'action'=>'singlepopup', 
						'id'=>$element['item_id']
					)));
				$seeAlsoLink->link = $element['term1'];
				$seeAlsoLink->title = $objLanguage->languageText('mod_glossary_jumpto', 'glossary').$element['term1'];
				echo $seeAlsoLink->show();

			} else {
				
				$seeAlsoLink = new link($this->uri(array(
						'module'=>'glossary', 
						'action'=>'singlepopup', 
						'id'=>$element['item_id2']
					)));
				$seeAlsoLink->link = $element['term2'];
				$seeAlsoLink->title = $objLanguage->languageText('mod_glossary_jumpto', 'glossary').' '.$element['term2'];
				echo $seeAlsoLink->show();

			} // End If
			
			$count++;
			
	}// End For Each
	
	echo ('</p>');
	
} // End if - See Also




if ($urlNum > 0) 
{
	
	echo ('<p><strong>'.$objLanguage->languageText('mod_glossary_relatedWebsites', 'glossary').'</strong></p>');
	echo ('<ul>');	
	
	
	foreach ($urlList as $element) {

		echo ('<li>');
		
		$itemLink = new link($element['url']);
		$itemLink->target = '_blank';
		$itemLink->link =$element['url'];
		
		echo $itemLink->show();
		
		echo ('</li>');
		

	}
	
	echo ('</ul>');

}

    if (count($images) > 0){
        
        $imageDisplay = '<p>';
        
        
        $imageDisplay .= '<strong>'.$objLanguage->languageText('word_images').' :</strong> ';
        
        $comma = '';
        
        $objPop = $this->newObject('windowpop', 'htmlelements');
        
        foreach ($images as $image)
        {
            $imageDisplay .= $comma;
            
            $link = $this->uri(array('action' => 'previewimage', 'id' => $image['image'], 'fname' => $image['filename']));


            $objPop->set('location',$link);
            $objPop->set('window_name','previewImage');
            $objPop->set('linktext',$image['caption']);
            $objPop->set('width','20'); 
            $objPop->set('height','20');
            $objPop->set('resizable','yes');
            $imageDisplay.= $objPop->show();
            
            $comma = ', ';
        }
        
        echo $imageDisplay.'</p>';
        
    }

?>
