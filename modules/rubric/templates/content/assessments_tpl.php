<?php

    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $pageTitle->str=$objLanguage->languageText('rubric_rubric','rubric').": " . $title;
    if ($this->isValid('addassessment')) {
	    // Add assessment.
   		$icon =& $this->getObject('geticon','htmlelements');
   		$icon->setIcon('add');
   		$icon->alt = $objLanguage->languageText("rubric_addassessment","rubric");
   		$icon->align=false;
        $pageTitle->str .= "<a href=\"" .
        $this->uri(array(
            'module'=>'rubric',
            'action'=>'addassessment',
            'tableId'=>$tableId
        )) . "\">" . $icon->show() . "</a>";
	}
    // Show Title
    echo $pageTitle->show();
    // Show Description
	echo '<p>'.$description.'</p>';
    $tblclass = $this->newObject('htmltable','htmlelements');
    $tblclass->width='99%';
    $tblclass->border='0';
    $tblclass->cellspacing='1';
    $tblclass->cellpadding='5';
    $tblclass->startHeaderRow();
    $tblclass->addHeaderCell(ucfirst($objLanguage->code2Txt('word_username','system'))." / ".ucfirst($objLanguage->code2Txt('rubric_studentno','rubric')), 150);
    if ($showStudentNames == "yes") {
        $tblclass->addHeaderCell($objLanguage->languageText('rubric_name','rubric'), 150);
    }
    $tblclass->addHeaderCell($objLanguage->languageText('rubric_score','rubric'), 60);
    $tblclass->addHeaderCell(ucfirst($objLanguage->code2Txt('rubric_teacher','rubric')), 150);
    $tblclass->addHeaderCell($objLanguage->languageText('rubric_date','rubric'), 60);
    $tblclass->addHeaderCell("&nbsp;",'rubric', 60);
    $tblclass->endHeaderRow();
    // Display the assessments.
    $oddOrEven = "odd";
	foreach ($assessments as $assessment) {
		// Only allow assessment if permissions are set or it  is your assessment.
		if (
		    $this->isValid('viewassessment')
            && (
                $this->objUser->isContextLecturer($this->objUser->userId(), $this->contextCode)
                || $this->objUser->isContextStudent($this->contextCode)
                && $this->objUser->userName() == $assessment['studentno']
            )
    	) {

            $tblclass->startRow();
            $oddOrEven = ($oddOrEven=="even")? "odd":"even";
            $option = "<a href=\"" .
                $this->uri(array(
                    'module'=>'rubric',
                    'action'=>'viewassessment',
                    'tableId'=>$tableId,
                    'id'=>$assessment['id']
            ))."\">".$assessment['studentno']."</a>";
            $tblclass->addCell($option, "null", "top", "left", $oddOrEven, null);
			if ($showStudentNames == "yes"){
	            $tblclass->addCell("<b>" . $assessment['student'] . "</b>", "null", "top", "left", $oddOrEven, null);
			}
			$scores = explode(",", $assessment['scores']);
			$total = 0;
			foreach ($scores as $score) {
				$total += $score;
			}
	        $tblclass->addCell("<b>" . "$total/$maxtotal" . "</b>", "null", "top", "left", $oddOrEven, null);
	        $tblclass->addCell("<b>" . $assessment['teacher'] . "</b>", "null", "top", "left", $oddOrEven, null);		 $tblclass->addCell("<b>" . $assessment['timestamp'] . "</b>", "null", "top", "left", $oddOrEven, null);
			$options = ("&nbsp;");
			if ($this->isValid('editassessment')) {
			    // Edit assessment.
		   		$icon =& $this->getObject('geticon','htmlelements');
		   		$icon->setIcon('edit');
		   		$icon->alt = $objLanguage->languageText("word_edit");
		   		$icon->align=false;
				$options .= "<a href=\"" .
                $this->uri(array(
                    'module'=>'rubric',
                    'action'=>'editassessment',
                    'tableId'=>$tableId,
                    'id'=>$assessment['id']
                )) . "\">" . $icon->show() . "</a>";
			}
			$options .= "&nbsp;";
			if ($this->isValid('deleteassessment')) {
	            $objConfirm=&$this->newObject('confirm','utilities');
	    		$icon = $this->getObject('geticon','htmlelements');
	    		$icon->setIcon('delete');
	    		$icon->alt = $objLanguage->languageText("word_delete");
	    		$icon->align=false;
	            $objConfirm->setConfirm(
                $icon->show(),
                $this->uri(array(
                    'module'=>'rubric',
                    'action'=>'deleteAssessment',
                    'tableId'=>$tableId,
                    'id'=>$assessment['id']
                )),
                $objLanguage->languageText('mod_rubric_suredeleteassessment','rubric'));
	            $options .= $objConfirm->show();
			}
	        $tblclass->addCell($options, "null", "top", "left", $oddOrEven, null);
	        $tblclass->endRow();
		}
	}
    echo $tblclass->show();
	echo "<br />";
	if ($this->isValid('addassessment')) {
	    // Add assessment.
		echo "<a href=\"" .
			$this->uri(array(
		    	'module'=>'rubric',
				'action'=>'addassessment',
				'tableId'=>$tableId
			))
		. "\">" . $objLanguage->languageText("rubric_addassessment","rubric") . "</a>";
		echo "&nbsp;/&nbsp;";
	}
    // Show/hide student names.
	if ($this->objUser->isContextLecturer()) {
		if ($showStudentNames == "yes") {
			echo "<a href=\"" .
				$this->uri(array(
			    	'module'=>'rubric',
					'action'=>'assessments',
					'tableId'=>$tableId,
					'showStudentNames'=>'no'
				))
			. "\">" . $objLanguage->languageText("rubric_hide","rubric") . "</a>";
		}
		else {
			echo "<a href=\"" .
				$this->uri(array(
			    	'module'=>'rubric',
					'action'=>'assessments',
					'tableId'=>$tableId,
					'showStudentNames'=>'yes'
				))
			. "\">" . $objLanguage->languageText("rubric_show","rubric") . "</a>";
		}
		echo "&nbsp;/&nbsp;";
	}
    // Print the page.
	if ($this->objUser->isContextLecturer()) {
		echo "<a href=\"javascript:window.print();\">" . $objLanguage->languageText("word_print") . "</a>";
		echo "&nbsp;/&nbsp;";
	}
	// Back link
	echo "<a href=\"" .
		$this->uri(array(
	    	'module'=>'rubric',
		))
	. "\">" . $objLanguage->languageText("word_back") . "</a>"; //rubric_returntomainmenu
?>