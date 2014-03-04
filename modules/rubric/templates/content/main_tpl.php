<?php
$ret = "";
// Load needed classes
$this->loadClass('link', 'htmlelements');
$pageTitle = $this->newObject('htmlheading','htmlelements');
$pageTitle->type=1;
$pageTitle->align='left';
$pageTitle->str=$objLanguage->languageText('rubric_rubrics','rubric');
$tblclass=$this->newObject('htmltable','htmlelements');
$tblclass->width='100%';
$tblclass->border='0';
$tblclass->cellspacing='1';
$tblclass->cellpadding='5';
$pageTitle->type=3;
if ($contextCode == "root"){
    $pageTitle->str.='<br />'.ucwords($objLanguage->code2Txt("rubric_predefined","rubric"));
} else {
    //$pageTitle->str=ucwords($objLanguage->code2Txt("rubric_context"));
}
$tblclass->startRow();
if ($this->isValid('createtable')) {
    // Display create button.
    $icon = $this->getObject('geticon','htmlelements');
    $icon->setIcon('add');
    $icon->title = "Create";
    $icon->align=false;
    $pageTitle->str .= "<a href=\"" .
        $this->uri(array(
            'module'=>'rubric',
            'action'=>'createtable',
            'type'=>($contextCode == "root" ? 'predefined' : 'context')
        ))
    . "\">" . $icon->show() . "</a>";
}
$tblclass->addCell($pageTitle->show(), "null", "top", "left", "",null);
$tblclass->endRow();
$ret .= $tblclass->show();
$tblclass = $this->newObject('htmltable','htmlelements');
$tblclass->width='99%';
$tblclass->border='0';
$tblclass->cellspacing='1';
$tblclass->cellpadding='5';
$tblclass->startHeaderRow();
$tblclass->addHeaderCell($objLanguage->languageText('word_title'), 60);
$tblclass->addHeaderCell($objLanguage->languageText('rubric_description','rubric'), 60);
$tblclass->addHeaderCell("&nbsp;", 60);
$tblclass->endHeaderRow();
// Display tables.
$oddOrEven = "odd";
foreach ($tables as $table) {
    $viewLink = new link ($this->uri(array('action'=>'assessments', 'tableId'=>$table['id'])));
    $viewLink->link = $table['title'];
    $viewLinkItem = $viewLink->show();
    $tblclass->startRow();
    $oddOrEven = ($oddOrEven=="even")? "odd":"even";
    $tblclass->addCell("<b>" . $viewLinkItem . "</b>", "null", "top", "left", $oddOrEven, null);
    $tblclass->addCell("<b>" . $table['description'] . "</b>", "null", "top", "left", $oddOrEven, null);

    // Start of Rubric Options
    $options = NULL;

    if ($contextCode != "root") {
        if ($this->isValid('assessments')) {
            // Assessments for table.
            $icon = $this->getObject('geticon','htmlelements');
            $icon->setIcon('assessments');
            $icon->title = $objLanguage->languageText("word_assessment");
            $icon->alt = $objLanguage->languageText("word_assessment");
            $options .= "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'assessments',
                'tableId'=>$table['id']
            )) . "\">" .$icon->show() . "</a>";
            $options .= "&nbsp;";
        }
    }
    if ($this->isValid('renametable')) {
        // Rename table.
        $icon->setIcon('rename');
        $icon->title = $objLanguage->languageText("word_rename1");
        $icon->alt = $objLanguage->languageText("word_rename1");

        $options .= "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'renametable',
                'tableId'=>$table['id']
            ))
        . "\">" . $icon->show() . "</a>";
        $options .= "&nbsp;";

    }
    if ($this->isValid('viewtable')) {
        // View table.
        $icon = $this->getObject('geticon','htmlelements');
        $icon->setIcon('preview');
        $icon->title = $objLanguage->languageText("word_view");
        $icon->alt = $objLanguage->languageText("word_view");
        $icon->align=false;
        $options .= "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'viewtable',
                'tableId'=>$table['id']
            ))
        . "\">" . $icon->show() . "</a>";
        $options .= "&nbsp;";
    }
    if ($this->isValid('clonetable')) {
        // Clone table.
        $icon = $this->getObject('geticon','htmlelements');
        $icon->setIcon('copy');
        $icon->title = $objLanguage->languageText("word_copy");
        $icon->alt = $objLanguage->languageText("word_copy");
        $icon->align=false;
        $options .= "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'clonetable',
                'tableId'=>$table['id']
            ))
        . "\">" . $icon->show() . "</a>";
        $options .= "&nbsp;";
    }
    if ($this->isValid('edittable')) {
        // Edit table.
        $icon = $this->getObject('geticon','htmlelements');
        $icon->setIcon('edit');
        $icon->title = $objLanguage->languageText("word_edit");
        $icon->alt = $objLanguage->languageText("word_edit");
        $icon->align=false;
        $options .= "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'edittable',
                'tableId'=>$table['id']
            ))
        . "\">" . $icon->show() . "</a>";
        $options .= "&nbsp;";
    }
    if ($this->isValid('deletetable')) {
        // Delete table.
        $objConfirm=&$this->newObject('confirm','utilities');
        $icon = $this->getObject('geticon','htmlelements');
        $icon->setIcon('delete');
        $icon->title = $objLanguage->languageText("word_delete");
        $icon->alt = $objLanguage->languageText("word_delete");
        $icon->align=false;
        $objConfirm->setConfirm(
            $icon->show(),
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'deletetable',
                'tableId'=>$table['id'])),
        $objLanguage->languageText('mod_rubric_confirmdelete','rubric')." ".$table['title']."?");
        $options .= $objConfirm->show();
    }
    if($noBanner == "yes") {
        $options .= "&nbsp;";
        // Assign table.
        $options .= "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'assign',
                'tableId'=>$table['id']
            ))
        . "\">" . "Assign" . "</a>";

    }
    $tblclass->addCell($options, "null", "top", "left", $oddOrEven, null);
    $tblclass->endRow();

}
if (empty($tables)) {
    $tblclass->startRow();
    $tblclass->addCell("<div class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_rubric_norecords','rubric') . "</div>", "null", "top", "left", "", 'colspan="3"');
    $tblclass->endRow();
}
$ret .= $tblclass->show();
// If not root then show predefined tables.
if ($this->objUser->isContextLecturer($this->objUser->userId(), $contextCode)) {
    $pageTitle->str=ucwords($objLanguage->code2Txt("rubric_predefined","rubric"));
    $tblclass=$this->newObject('htmltable','htmlelements');
    $tblclass->width='100%';
    $tblclass->border='0';
    $tblclass->cellspacing='1';
    $tblclass->cellpadding='5';
    $tblclass->startRow();
    if ($this->isValid('createtable')) {
        // Display create button.
        $icon = $this->getObject('geticon','htmlelements');
        $icon->setIcon('add');
        $icon->title= "Create";
        $icon->alt = "Create";
        $icon->align=false;
        $icon_ = $icon->show();
    }
    else {
        $icon_ = '';
    }
    $pageTitle->str .= "<a href=\"" .
    $this->uri(array('module'=>'rubric','action'=>'createtable','type'=>'predefined'))
    . "\">" .$icon_. "</a>";
    $tblclass->addCell($pageTitle->show(), "null", "top", "left", "",null);
    $tblclass->endRow();
    $ret .= $tblclass->show();
    $tblclass = $this->newObject('htmltable','htmlelements');
    $tblclass->width='99%';
    $tblclass->border='0';
    $tblclass->cellspacing='1';
    $tblclass->cellpadding='5';
    $tblclass->startHeaderRow();
    $tblclass->addHeaderCell($objLanguage->languageText('word_title'), 60);
    $tblclass->addHeaderCell($objLanguage->languageText('rubric_description','rubric'), 60);
    $tblclass->addHeaderCell("&nbsp;", 60);
    $tblclass->endHeaderRow();

    $oddOrEven = "odd";
if (isset($pdtables)) {
    foreach ($pdtables as $pdtable) {
        $tblclass->startRow();
        $oddOrEven = ($oddOrEven=="even")? "odd":"even";
        $tblclass->addCell("<b>" . $pdtable['title'] . "</b>", "null", "top", "left", $oddOrEven, null);
        $tblclass->addCell("<b>" . $pdtable['description'] . "</b>", "null", "top", "left", $oddOrEven, null);
        if ($this->isValid('renametable')) {
            // Rename table.
            $icon->setIcon('rename');
            $icon->title = $objLanguage->languageText("word_rename1");
            $icon->alt = $objLanguage->languageText("word_rename1");
            $options =  "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'renametable',
                'tableId'=>$pdtable['id']
            ))
            . "\">" . $icon->show() . "</a>";
            $options .=  "&nbsp;";
        }
        if ($this->isValid('viewtable')) {
        // View table.
            $icon = $this->getObject('geticon','htmlelements');
            $icon->setIcon('preview');
            $icon->title = $objLanguage->languageText("word_view");
            $icon->alt = $objLanguage->languageText("word_view");
            $icon->align=false;
            $options .= "<a href=\"" .
                $this->uri(array(
                    'module'=>'rubric',
                    'action'=>'viewtable',
                    'tableId'=>$pdtable['id']
                ))
            . "\">" . $icon->show() . "</a>";
            $options .= "&nbsp;";
        }
        if ($this->isValid('clonetable')) {
        // Clone table.
            $icon = $this->getObject('geticon','htmlelements');
            $icon->setIcon('copy');
            $icon->title = $objLanguage->languageText("word_copy");
            $icon->alt = $objLanguage->languageText("word_copy");
            $icon->align=false;
            $options .= "<a href=\"" .
                $this->uri(array(
                    'module'=>'rubric',
                    'action'=>'clonetable',
                    'tableId'=>$pdtable['id']
                ))
            . "\">" . $icon->show() . "</a>";
            $options .= "&nbsp;";
        }
        if ($this->isValid('copytable')) {
        // Copy table.
            $icon = $this->getObject('geticon','htmlelements');
            $icon->setIcon('copy');
            $icon->title = $objLanguage->code2Txt("mod_rubric_copytocontext");
            $icon->alt = $objLanguage->code2Txt("mod_rubric_copytocontext");
            $icon->align=false;
            $options .=  "<a href=\"" .
                $this->uri(array(
                    'module'=>'rubric',
                    'action'=>'copytable',
                    'tableId'=>$pdtable['id']
                ))
            . "\">" . $icon->show() . "</a>";
            $options .=  "&nbsp;";
        }
        if ($this->isValid('edittable')) {
        // Edit table.
            $icon = $this->getObject('geticon','htmlelements');
            $icon->setIcon('edit');
            $icon->title = $objLanguage->languageText("word_edit");
            $icon->alt = $objLanguage->languageText("word_edit");
            $icon->align=false;
            $options .= "<a href=\"" .
                $this->uri(array(
                    'module'=>'rubric',
                    'action'=>'edittable',
                    'tableId'=>$pdtable['id']
                ))
            . "\">" . $icon->show() . "</a>";
            $options .= "&nbsp;";
        }

        if ($this->isValid('deletetable')) {
            // Delete table.
            $objConfirm=&$this->newObject('confirm','utilities');
            $icon = $this->getObject('geticon','htmlelements');
            $icon->setIcon('delete');
            $icon->title = $objLanguage->languageText("word_delete");
            $icon->alt = $objLanguage->languageText("word_delete");
            $icon->align=false;
            $objConfirm->setConfirm(
            $icon->show(),
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'deletetable',
                'tableId'=>$pdtable['id'])),
            $objLanguage->languageText('mod_rubric_suredelete','rubric'));
            $options .= $objConfirm->show();
        }
        $tblclass->addCell($options, "null", "top", "left", $oddOrEven, null);
        $tblclass->endRow();
    }
}
if (empty($pdtables)) {
    $tblclass->startRow();
    $tblclass->addCell("<div class=\"noRecordsMessage\">" . $objLanguage->languageText('mod_rubric_norecords','rubric') . "</div>", "null", "top", "left", "", 'colspan="3"');
    $tblclass->endRow();
}
$ret .= $tblclass->show();
}
echo $ret;
?>