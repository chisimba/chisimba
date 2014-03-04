<?php
    $this->loadclass('form','htmlelements');
    $this->loadClass('checkbox','htmlelements');
    // Display heading.
	echo "<h1>".$objLanguage->code2Txt("mod_workgroupadmin_workgroup")." : ".$workgroup['description']."</h1>";
    // Display members.
	echo "<h1>".$objLanguage->code2Txt('mod_workgroupadmin_members')."</h1>";
    if (empty($members)) {
        echo "<span class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_workgroupadmin_norecordsfound') . "</span>";
    }
    else {
        $objForm= new form('main',$this->uri(
        array(
            'action'=>'removeselected',
            'workgroupId'=>$workgroup['id']
            )
        ));
        $objForm->displayType=3;
        $objTable =& $this->newObject('htmltable','htmlelements');
    	foreach ($members as $user) {
            $objCheckbox = new checkBox('remove[]');
            $objCheckbox->setValue($user['userId']);      
            $objTable->startRow();
            $objTable->addCell($objCheckbox->show());
            $objTable->addCell($user['firstName'] . "&nbsp;" . $user['surname']);
            $objTable->endRow();
        }
        $objForm->addToForm($objTable);
    	$this->loadClass("button","htmlelements");
    	$button = new button("submit", $objLanguage->languageText("word_delete"));
    	$button->setToSubmit();
    	$objForm->addToForm($button);
    	echo $objForm->show();
    }
    // Display students.
	echo "<h1>".$objLanguage->code2Txt('mod_workgroupadmin_students')."</h1>";
    if (empty($students)) {
        echo "<span class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_workgroupadmin_norecordsfound') . "</span>";
    }
    else {
        $objForm= new form('main',$this->uri(
        array(
            'action'=>'addselected',
            'workgroupId'=>$workgroup['id']
            )
        ));
        $objForm->displayType=3;
        $objTable =& $this->newObject('htmltable','htmlelements');
    	foreach ($students as $user) {
            $objCheckbox = new checkBox('add[]');
            $objCheckbox->setValue($user['userId']);      
            $objTable->startRow();
            $objTable->addCell($objCheckbox->show());
            $objTable->addCell($user['firstName'] . "&nbsp;" . $user['surname']);
            $objTable->endRow();
        }
        $objForm->addToForm($objTable);
    	$this->loadClass("button","htmlelements");
    	$button = new button("submit", $objLanguage->languageText("word_add"));
    	$button->setToSubmit();
    	$objForm->addToForm($button);
    	echo $objForm->show();
    }
    echo("<br/>");
	echo "<a href=\"".
		$this->uri(array(
	    	'module'=>'workgroups',
		))
	."\">".$objLanguage->languageText("word_back")."</a>"."<br/>"; //wg_return
?>