<?php

    $this->loadClass('label','htmlelements');

    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $pageTitle->str=$objLanguage->languageText('rubric_rubric','rubric') . " : " . $title ;
	echo $pageTitle->show();
    //$pageTitle->str=$description;
    //echo $pageTitle->show();
	echo $description;

    // Load classes.
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("dropdown","htmlelements");

	// Display form.
	switch($mode){
		case 'add':
			$uri = $this->uri(array(
		    	'module'=>'rubric',
				'action'=>'addassessmentconfirm',
				'tableId'=>$tableId,
				'NoBanner'=>$noBanner
			));
			break;
		case 'edit':
			$uri = $this->uri(array(
		    	'module'=>'rubric',
				'action'=>'editassessmentconfirm',
				'tableId'=>$tableId,
				'id'=>$id,
				'NoBanner'=>$noBanner
			));
			break;
		default:
			;
	} // switch
	$form = new form("use", $uri);
	$form->setDisplayType(3);
    $objTable =& $this->newObject('htmltable','htmlelements');
    $objTable->width='99%';
    $objTable->border='0';
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $row = array("<b>".ucfirst($objLanguage->code2Txt("rubric_teacher","rubric"))."</b>", $objUser->fullname());
    $objTable->addRow($row);

	// Dropdown of students
	$objDbContext =& $this->getObject('dbcontext','context');
	$contextCode = $objDbContext->getContextCode();
	// Get the groupAdminModel object.
	$groups =& $this->getObject("groupAdminModel", "groupadmin");
	// Get a list of students.
	$gid=$groups->getLeafId(array($contextCode,'Students'));
	//$students = $groups->getGroupUsers($gid, array('userId', 'username',"CONCAT(firstName, ' ', surname, ' (', username, ')') AS display", "'firstName' || ' ' || 'surname' AS fullName"), "ORDER BY fullName");
	$students_ = $groups->getGroupUsers($gid, TRUE);
	$students = array();
	if (!empty($students_)) {
	    foreach ($students_ as $student_) {
	        $students[$student_['surname'].$student_['firstname'].$student_['username']] = array(
    	    'display'=>
        	    /*$student_['title']
        	    .'&nbsp;'.*/$student_['surname']
                .',&nbsp;'.$student_['firstname']
                .'&nbsp;('.$student_['username'].')',
            'username'=>$student_['username']
            );
	    }
	}
	if (!empty($students)) {
	    ksort($students);
	}
	$dropdown = new dropdown("studentNo");
	$dropdown->addFromDB($students, 'display', /*'userId'*/'username', $studentNo);
	//$dropdown->addFromDB($students,'username', $studentNo);

    $labelStudentNo = new label(ucfirst($this->objLanguage->code2Txt("rubric_student","rubric")),"input_studentNo");
    $row = array("<b>".$labelStudentNo->show()."</b>", $dropdown->show());

    $objTable->addRow($row);


	$form->addToForm($objTable->show());
	$table =& $this->newObject("htmltable","htmlelements");
	$table->border = '0';
	$table->width = '99%';
    $table->cellspacing='2';
    $table->cellpadding='2';
	$table->startRow();
	$table->addHeaderCell("&nbsp;");
    // Display performances.
	for ($j=0;$j<$cols;$j++) {
		$table->addHeaderCell($performances[$j]);
	}
	$table->endRow();
	$class = 'odd';
	for ($i=0;$i<$rows;$i++) {
		$table->startRow($class);
        // Display objective.
		$table->addCell($objectives[$i]);
        // Display cells.
		for ($j=0;$j<$cols;$j++) {
			//$checked = '';
			$checked = $mode == 'edit' ? ($scores[$i] == ($j+1) ? 'checked' : '') : '';
         $cell = "<input type=\"radio\" name=\"row{$i}\" id=\"row{$i}col{$j}\" value=\"{$j}\" ".$checked." />";

         $cell .= "<label for=\"row{$i}col{$j}\">" . $cells[$i][$j] . "</label>";
         $table->addCell($cell);
                }

		$table->endRow();
		$class = $class == 'odd' ? 'even' : 'odd';
	}
	$form->addToForm($table->show());
		$button = new button("submit", $objLanguage->languageText("word_save"));
		$button->setToSubmit();
		$returnUrl = $this->uri(array('module'=>'rubric','action'=>'assessments' ,'tableId'=>$tableId,));

		$buttonc = new button("submit", $objLanguage->languageText("word_cancel"));
		$buttonc->setOnClick("window.location='$returnUrl'");
	$form->addToForm($button);
	$form->addToForm($buttonc);

	echo $form->show();
?>