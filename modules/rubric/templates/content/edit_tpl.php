<?php

    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $pageTitle->str=$objLanguage->languageText('rubric_rubric','rubric'). " : " . $title;
	echo $pageTitle->show();
	
    $labelDescription = "<p><em>Description</em>: " . $description . "</p>";
    
    echo $labelDescription;
    
    echo '<p><strong>'.ucwords($objLanguage->languageText('word_instructions')).'</strong>:';
    echo '<ol>';
    
    if ($this->getParam('new') == 'yes') {
        echo '<li>'.$objLanguage->languageText('mod_rubric_instructionpart1','rubric').'</li>';
        echo '<li>'.$objLanguage->languageText('mod_rubric_instructionpart2','rubric').'</li>';
        echo '<li>'.$objLanguage->languageText('mod_rubric_instructionpart3','rubric').'</li>';
        echo '<li>'.$objLanguage->languageText('mod_rubric_instructionpart4','rubric').'</li>';
    }
    echo '</ol></p>';
    
    // Load classes.
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("textarea","htmlelements");
	$this->loadClass("button","htmlelements");
	
    // Display form.
    $createForm = $this->newObject('form','htmlelements');
    $createForm->name="edit";
    $createForm->action=$this->uri(array(
	    	'module'=>'rubric',
			'action'=>'edittableconfirm',
			'tableId'=>$tableId));
    
    $objTable=$this->newObject('htmltable','htmlelements');    
    $objTable->border='0';    
    $objTable->width='40%';
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';	
	
	$objTable->startRow();
	$objTable->addHeaderCell("&nbsp;");
    // Display performances.
	for ($j=0;$j<$cols;$j++) {
		$textinput = new textinput("performance{$j}",$performances[$j]);
		$objTable->addHeaderCell($textinput->show());
	}
	$objTable->endRow();
	for ($i=0;$i<$rows;$i++) {
		$objTable->startRow();
        // Display objective.
		$textinput = new textarea("objective{$i}", $objectives[$i], 2, 20);
		$objTable->addCell($textinput->show());
        // Display cells.
		for ($j=0;$j<$cols;$j++) {
			$textarea = new textarea("cell{$i}{$j}", $cells[$i][$j], 4, 18);
			$objTable->addCell($textarea->show());
		}
		$objTable->endRow();
	}
	$createForm->addToForm($objTable->show());
		$button = new button("submit", $objLanguage->languageText("word_save"));
		$button->setToSubmit();
	$createForm->addToForm('<p>'.$button->show().'</p>');
	echo $createForm->show();
	if (!isset($suppressModify)) {
	    echo "<a href=\"".$this->uri(array('action'=>'addrow','tableId'=>$tableId))."\">Add Row</a><br/>";
	    echo "<a href=\"".$this->uri(array('action'=>'addcol','tableId'=>$tableId))."\">Add Column</a><br/>";
	    if ($rows > 1) {
	        echo "<a href=\"".$this->uri(array('action'=>'delrow','tableId'=>$tableId))."\">Delete Row</a><br/>";
	    }
	    if ($cols > 1) {
	        echo "<a href=\"".$this->uri(array('action'=>'delcol','tableId'=>$tableId))."\">Delete Column</a><br/>";
	    }
	}
?>
