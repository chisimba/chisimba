<?php
$res ="";
$prvpage = "";
$nextpage = "";
$objFile = $this->getObject('dbfile', 'filemanager');
$objHead = $this->newObject('htmlheading', 'htmlelements');

$objModule = $this->getObject('modules', 'modulecatalogue');

//Add link back to the chapter list on the middle links
$middle = '';
if(empty($pagelft)) {
    $pagelft = Null;
} else {
    $prvpage = $this->objContentOrder->getPrevPageId($this->contextCode, $currentChapter, $pagelft);
    //Check if first page in the chapter
    $prevLeftValue = $pagelft-2;
    $nextpage = $this->objContentOrder->getNextPageId($this->contextCode, $currentChapter, $prevLeftValue);
}
$link = new link ($this->uri(array("action"=>"showcontextchapters","chapterid"=>$currentChapter, 'prevpageid'=>$nextpage), $module));
$link->link = '&#171; '.$this->objLanguage->languageText('mod_contextcontent_backchapter','contextcontent');
$middle .= $link->show();

$middle .= ' <br /> ';


//A link to adding a page
$addLink = new link($this->uri(array('action' => 'addpage', 'id' => $page['id'], 'context' => $this->contextCode, 'chapter' => $page['chapterid'])));
$addLink->link = $this->objLanguage->languageText('mod_contextcontent_addcontextpages', 'contextcontent');

$addPageFromFileLink = new link($this->uri(array('action' => 'addpagefromfile', 'id' => $page['id'], 'context' => $this->contextCode, 'chapterid' => $page['chapterid'])));
$addPageFromFileLink->link = $this->objLanguage->languageText('mod_contextcontent_createpagefromfile', 'contextcontent', 'Create page from file');

$scormInstalled = $objModule->checkIfRegistered("scorm");
if ($scormInstalled) {
    $addScormLink = new link($this->uri(array('action' => 'addscormpage', 'id' => $page['id'], 'context' => $this->contextCode, 'chapter' => $page['chapterid'])));
    $addScormLink->link = $this->objLanguage->languageText('mod_contextcontent_addcontextscormpages', 'contextcontent');
    $scormLink = $addScormLink->show();
} else {
    $scormLink = NULL;
}

//A link to editing a page
$editLink = new link($this->uri(array('action' => 'editpage', 'id' => $page['id'], 'context' => $this->contextCode)));
$editLink->link = $this->objLanguage->languageText('mod_contextcontent_editcontextpages', 'contextcontent');

if (($page['rght'] - $page['lft'] - 1) == 0) {
    $deleteLink = new link($this->uri(array('action' => 'deletepage', 'id' => $page['id'], 'context' => $this->contextCode)));
} else {
    $deleteLink = new link("javascript:alert('" . $this->objLanguage->languageText('mod_contextcontent_pagecannotbedeleteduntil', 'contextcontent') . ".');");
}
$deleteLink->link = $this->objLanguage->languageText('mod_contextcontent_delcontextpages', 'contextcontent');

$list = array();

if ($this->isValid('addpage')) {
    $list[] = $addLink->show();
    //   $list[] = $addPageFromFileLink->show();
    if ($scormInstalled) {
        $list[] = $scormLink;
    }
}

if ($this->isValid('editpage')) {
    $list[] = $editLink->show();
}

if ($this->isValid('deletepage')) {
    $list[] = $deleteLink->show();
}

if (count($list) == 0) {
    $middle = '&nbsp;';
} else {
    $middle .= '';
    $divider = '';

    foreach ($list as $item) {
        $middle .= $divider . $item;
        $divider = ' / ';
    }
}

if ($this->isValid('movepageup')) {

    $middle .= '<br />';

    if ($isFirstPageOnLevel) {
        $middle .= '<span style="color:grey;" title="' . $this->objLanguage->languageText('mod_contextcontent_isfirstpageonlevel', 'contextcontent') . '">' . $this->objLanguage->languageText('mod_contextcontent_movepageup', 'contextcontent') . '</span>';
    } else {
        $link = new link($this->uri(array('action' => 'movepageup', 'id' => $page['id'])));
        $link->link = $this->objLanguage->languageText('mod_contextcontent_movepageup', 'contextcontent');
        $middle .= $link->show();
    }

    $middle .= ' / ';

    if ($isLastPageOnLevel) {
        $middle .= '<span style="color:grey;" title="' . $this->objLanguage->languageText('mod_contextcontent_islastpageonlevel', 'contextcontent') . '">' . $this->objLanguage->languageText('mod_contextcontent_movepagedown', 'contextcontent') . '</span>';
    } else {
        $link = new link($this->uri(array('action' => 'movepagedown', 'id' => $page['id'])));
        $link->link = $this->objLanguage->languageText('mod_contextcontent_movepagedown', 'contextcontent');
        $middle .= $link->show();
    }
}


$table = $this->newObject('htmltable', 'htmlelements');
//$table->border='1';
$table->startRow();
$table->cssClass = "pagenavigation";
$table->addCell($prevPage, '33%', 'top');
$table->addCell($middle, '33%', 'top', 'center');
$table->addCell($nextPage, '33%', 'top', 'right');
$table->endRow();

$table2 = $this->newObject('htmltable', 'htmlelements');
//$table->border='1';
$table2->startRow();
$table2->cssClass = "pagenavigation2";
$table2->addCell($prevPage, '33%', 'top');
$table2->addCell('&nbsp;', '33%', 'top', 'center');
$table2->addCell($nextPage, '33%', 'top', 'right');
$table2->endRow();

$topTable = $this->newObject('htmltable', 'htmlelements');
//$topTable->border='1';
$topTable->startRow();
$topTable->cssClass = "toppagenavigation";
$topTable->addCell($prevPage, '50%', 'top');
$topTable->addCell($nextPage, '50%', 'top', 'right');
$topTable->endRow();

$this->loadClass('link', 'htmlelements');

$this->setVar('pageTitle', htmlentities($this->objContext->getTitle() . ' - ' . $page['menutitle']));

if (trim($page['headerscripts']) != '') {

    // Explode into array
    $scripts = explode(',', $page['headerscripts']);

    // Loop through array
    foreach ($scripts as $script) {
        // Check if valid
        if (trim($script) != '') {

            // Get Path
            $fileInfo = $objFile->getFilePath($script);

            // If Valid
            if ($fileInfo != FALSE) {

                // Check if Script or CSS, and display
                if (substr($fileInfo, -2, 2) == 'js') {
                    $this->appendArrayVar('headerParams', '<script type="text/javascript" src="' . $fileInfo . '"></script>');
                } else {
                    $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="' . $fileInfo . '"');
                }
            }
        }
    }
}

$objWashout = $this->getObject('washout', 'utilities');

$content = "";
/*
  if ($isFirstPageOnLevel) {
  $introheader = new htmlheading();
  $introheader->type = 3;
  $introheader->str = $this->objLanguage->languageText('mod_contextcontent_aboutchapter_introduction', 'contextcontent', 'About Chapter (Introduction)');
  $chapter=$this->objContextChapters->getChapter($currentChapter);

  $content.= $introheader->show().$chapter['introduction'];
  }
 */
$pageintroheader = new htmlheading();
$pageintroheader->type = 1;
$pageintroheader->cssClass = "pagetitle";
$pageintroheader->str = $page['menutitle'];
$content.= $pageintroheader->show() . $objWashout->parseText($page['pagecontent']);


$pageNotes = $objModule->checkIfRegistered("pagenotes");
if ($pageNotes) {
    $objContextModules =  $this->getObject('dbcontextmodules', 'context');
    $objContext = $this->getObject('dbcontext', 'context');
    if($objContextModules->isContextPlugin($objContext->getContextCode(), 'pagenotes')) {
        $objBlock = $this->getObject ( 'blocks', 'blocks' );
        $pageNotesInput= $objBlock->showBlock('widepagenotecontrol', 'pagenotes', NULL, 100, TRUE);
        $pageNotesRendered = $objBlock->showBlock('pagenoteswidebl', 'pagenotes', NULL, 100, TRUE);
        $content .= '<div class=\'pagenotes\'>' . $pageNotesInput . $pageNotesRendered . '</div>';
    }
}


$form = "";

if (count($chapters) > 1 && $this->isValid('movetochapter')) {
    $this->loadClass('form', 'htmlelements');
    $this->loadClass('dropdown', 'htmlelements');
    $this->loadClass('hiddeninput', 'htmlelements');
    $this->loadClass('button', 'htmlelements');
    $this->loadClass('label', 'htmlelements');

    $form = new form('movetochapter', $this->uri(array('action' => 'movetochapter')));
    $hiddenInput = new hiddeninput('id', $page['id']);

    $dropdown = new dropdown('chapter');
    foreach ($chapters as $chapterItem) {
        $dropdown->addOption($chapterItem['chapterid'], $chapterItem['chaptertitle']);
    }
    $dropdown->setSelected($page['chapterid']);

    $label = new label($this->objLanguage->languageText('mod_contextcontent_movepagetoanotherchapter', 'contextcontent') . ': ', 'input_chapter');

    $button = new button('movepage', $this->objLanguage->languageText('mod_contextcontent_move', 'contextcontent'));
    $button->setToSubmit();

    $form->addToForm($hiddenInput->show() . $label->show() . $dropdown->show() . ' ' . $button->show());

    $form = $form->show();
}

if ($this->isValid('addpage')) {
    // $objTabs = $this->newObject('tabcontent', 'htmlelements');
    // $objTabs->setWidth('98%');
    // $objTabs->addTab("Lecturer View",$topTable->show().$content.'<hr />'.$table->show().$form);
    // $objTabs->addTab("Student View",$topTable->show().$content.'<hr />'.$table2->show());
    // echo $objTabs->show();
    $res .= '<div id="tablenav">'.$topTable->show() . $content . '<hr />' . $table->show() . $form.'</div>';
} else {
    $res .= '<div id="tablenav">'.$topTable->show() . $content . '<hr />' . $table->show() . $form.'</div>';
}

//Check if comments are allowed for this course
$showcomment = $this->objContext->getField('showcomment', $contextCode = NULL);

if ($showcomment == 1) {
    $head = $this->objLanguage->languageText('mod_contextcontent_word_comment', 'contextcontent');
    $objHead->type = 1;
    $objHead->str = $head;
    $res .=  '<br/>' . $objHead->show() . '<br/>';

    $commentpost = $this->objContextComments->getPageComments($currentPage);
    if (count($commentpost) < 1) {

        $res .=  $this->objLanguage->languageText('mod_contextcontent_nocomment', 'contextcontent') . '<br/>';
    } else {
        $cnt = 0;
        $oddcolor = $this->objSysConfig->getValue('CONTEXTCONTENT_ODD', 'contextcontent');
        $evencolor = $this->objSysConfig->getValue('CONTEXTCONTENT_EVEN', 'contextcontent');

        foreach ($commentpost as $comment) {
            $objOutput = '<strong>' . $this->objUser->fullname($comment['userid']) . '</strong><br/>';
            $objOutput .= '<i>' . $comment['datecreated'] . '</i><br/>';
            $objOutput .= $comment['comment'];

            if ($cnt % 2 == 0) {
                $res .=  '<div class="colorbox ' . $evencolor . 'box">' . $objOutput . '</div>';
            } else {
                $res .=  '<div class="colorbox ' . $oddcolor . 'box">' . $objOutput . '</div>';
            }
            $cnt++;
        }
    }
    $this->loadClass('textarea', 'htmlelements');
    $cform = new form('contextcontent', $this->uri(array('action' => 'addcomment', 'pageid' => $currentPage)));

    //start a fieldset
    $cfieldset = $this->getObject('fieldset', 'htmlelements');
    $ct = $this->newObject('htmltable', 'htmlelements');
    $ct->cellpadding = 5;

    //Text
    $ct->startRow();
    $ctvlabel = new label($this->objLanguage->languageText('mod_contextcontent_writecomment', 'contextcontent') . ':', 'input_cvalue');
    $ct->addCell($ctvlabel->show());
    $ct->endRow();

    //Textarea
    $ct->startRow();
    $ctv = new textarea('comment', '', 8, 70);
    $ct->addCell($ctv->show());
    $ct->endRow();
    //end off the form and add the button
    $this->objconvButton = new button($this->objLanguage->languageText('mod_contextcontent_submitcomment', 'contextcontent'));
    $this->objconvButton->setValue($this->objLanguage->languageText('mod_contextcontent_submitcomment', 'contextcontent'));
    $this->objconvButton->setToSubmit();
    $cfieldset->addContent($ct->show());
    $cform->addToForm($cfieldset->show());
    $cform->addToForm($this->objconvButton->show());
    $res .=  '<br/>' . $cform->show();
}

echo '<div id="context_content">' . $res . "</div>";
?>