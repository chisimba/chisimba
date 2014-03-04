<?php

    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $titleString = ucwords($objLanguage->code2Txt("mod_workgroupadmin_heading",'workgroupadmin'));

	if ($this->isValid('create')){
	    // Show the add link
	    $objLink =& $this->getObject('link','htmlelements');
	    $objLink->link($this->uri(array(
	     		'module'=>'workgroupadmin',
	    		'action'=>'create',
	    )));
	    $iconAdd = $this->getObject('geticon','htmlelements');
	    $iconAdd->setIcon('add');
	    $iconAdd->alt = $objLanguage->languageText("word_add");
	    $iconAdd->align=false;
	    $objLink->link = $iconAdd->show();
        $titleString .= ' '.$objLink->show();
	}

    $pageTitle->str=$titleString;
	//echo $pageTitle->show();

	if (isset($confirm)) {
	    echo "<span class=\"confirm\">$confirm</span><br/>";
	}

    $tblclass=&$this->newObject('htmltable','htmlelements');
    $tblclass->width='100%';
    $tblclass->border='0';
    $tblclass->cellspacing='1';
    $tblclass->cellpadding='5';

    $tblclass->startRow();
    $tblclass->addHeaderCell(ucwords($objLanguage->code2Txt('mod_workgroupadmin_workgroup','workgroupadmin')), "null", "top", "left", "",null);
    $tblclass->addHeaderCell("&nbsp;");
    $tblclass->endRow();

    $oddOrEven = "odd";
    // Display workgroups.
	foreach ($workgroups as $workgroup){
		// Get the members of the workgroup.
		$objDbWorkgroupUsers =& $this->getObject('dbworkgroupusers','workgroup');
		$members = $objDbWorkgroupUsers->listAll($workgroup['id']);

        $tblclass->startRow();
        $tblclass->addCell($workgroup['description'].'('.count($members).')', "null", "top", "left", $oddOrEven, null);

		if ($this->isValid('rename')) {
	        // Rename workgroup.
			$options =  "<a href=\"".
				$this->uri(array(
			    	'module'=>'workgroupadmin',
					'action'=>'rename',
					'workgroupId'=>$workgroup['id']
				))
			."\">".$objLanguage->languageText('mod_workgroupadmin_rename','workgroupadmin')."</a>";
	        $options .= "&nbsp;";
		}

		if ($this->isValid('manage')) {
	        // Manage workgroup.
			$options .=  "<a href=\"".
				$this->uri(array(
			    	'module'=>'workgroupadmin',
					'action'=>'manage',
					'workgroupId'=>$workgroup['id']
				))
			."\">".$objLanguage->languageText('mod_workgroupadmin_manage','workgroupadmin')."</a>";
	        $options .= "&nbsp;";
		}

		if ($this->isValid('delete')) {
	        // Delete workgroup.
	    	$objConfirm=&$this->newObject('confirm','utilities');
            $icon = $this->getObject('geticon','htmlelements');
            $icon->setIcon('delete');
            $icon->alt = $objLanguage->languageText("word_delete");
            $icon->align=false;
	    	$objConfirm->setConfirm(
                $icon->show(),
	    		$this->uri(array(
			    	'module'=>'workgroupadmin',
					'action'=>'delete',
					'workgroupId'=>$workgroup['id'])),
	            $objLanguage->code2Txt('mod_workgroupadmin_suredelete','workgroupadmin'));
                $options .=  $objConfirm->show();
		}

        $tblclass->addCell($options, "null", "top", "left", $oddOrEven, null);
        $tblclass->endRow();
		if (!empty($members)) {
		    $innerTable=&$this->newObject('htmltable','htmlelements');
		    $innerTable->border='0';
		    $innerTable->cellspacing='1';
		    $innerTable->cellpadding='5';
			$innerTable->attributes=' style="border: 5px outset #c0c0c0;"';

		    $innerTable->startRow();
		    $innerTable->addHeaderCell(ucwords($objLanguage->code2Txt('mod_workgroupadmin_members','workgroupadmin')), "null", "top", "left", "",null);
		    $innerTable->endRow();

	        $oddOrEvenInner = "odd";
		    foreach ($members as $member) {
		        $innerTable->startRow();
		        $innerTable->addCell($member['surname'].', '.$member['firstname'].' ('.$member['username'].')', "null", "top", "left", $oddOrEvenInner, null);
		        $innerTable->endRow();
		        $oddOrEvenInner = ($oddOrEvenInner=="even")? "odd":"even";
			}
	        $tblclass->startRow();
	        //$tblclass->addCell('&nbsp;', "null", "top", "left", $oddOrEven, null);
	        $tblclass->addCell($innerTable->show(), "null", "top", "left", $oddOrEven, 'colspan="2"');
    	    $tblclass->endRow();
		}
	    $oddOrEven = ($oddOrEven=="even")? "odd":"even";
    }

    if (empty($workgroups)) {
        $tblclass->startRow();
        $tblclass->addCell("<div class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_workgroupadmin_norecordsfound','workgroupadmin') . "</div>", "null", "top", "left", "", 'colspan="3"');
        $tblclass->endRow();
    }

    echo "<div class='outerwrapper'>".$pageTitle->show()."<div class='innerwrapper'>".$tblclass->show()."</div>"."</div>";

    // Show the add link
    $objLink =& $this->getObject('link','htmlelements');
    $objLink->link($this->uri(array(
     		'module'=>'workgroupadmin',
    		'action'=>'create',
    )));

    $objLink->link = $objLanguage->languageText("mod_workgroupadmin_addworkgroup",'workgroupadmin');
    echo "<div class='adminadd'></div>"."<div class='adminaddlink'>".$objLink->show()."</div>";
?>
