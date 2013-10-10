<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');

$objModules = $this->getObject('modules', 'modulecatalogue');
$pdfHtmlDoc = $objModules->checkIfRegistered('htmldoc');



$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->align = 'absmiddle';

$objIcon->setIcon('edit');
$editIcon = $objIcon->show();

$objIcon->setIcon('delete');
$deleteIcon = $objIcon->show();

$objIcon->setIcon('add');
$objIcon->alt = $this->objLanguage->languageText('mod_contextcontent_addanewchapter', 'contextcontent');
$objIcon->title = $this->objLanguage->languageText('mod_contextcontent_addanewchapter', 'contextcontent');
$addIcon = $objIcon->show();

$objIcon->setIcon('add_multiple');
$objIcon->alt = $this->objLanguage->languageText('mod_contextcontent_createpagefromfile', 'contextcontent', 'Create page from file');
$objIcon->title = $this->objLanguage->languageText('mod_contextcontent_createpagefromfile', 'contextcontent', 'Create page from file');
$addPageFromFileIcon = $objIcon->show();

$objIcon->setIcon('create_page');
$objIcon->alt = $this->objLanguage->languageText('mod_contextcontent_addapagetothischapter', 'contextcontent');
$objIcon->title = $this->objLanguage->languageText('mod_contextcontent_addapagetothischapter', 'contextcontent');
$addPageIcon = $objIcon->show();

if ($this->objModuleCatalogue->checkIfRegistered('scorm')) {
    $objIcon->setIcon('scm');
    $objIcon->alt = $this->objLanguage->languageText('mod_scorm_addscormchapter', 'scorm');
    $objIcon->title = $this->objLanguage->languageText('mod_scorm_addscormchapter', 'scorm');
    $addScormIcon = $objIcon->show();
}

$objIcon->setIcon('pdf');
$objIcon->alt = $this->objLanguage->languageText('mod_contextcontent_downloadchapterinpdfformat', 'contextcontent');
$objIcon->title = $this->objLanguage->languageText('mod_contextcontent_downloadchapterinpdfformat', 'contextcontent');
$pdfIcon = $objIcon->show();
//The Activity Streamer Icon
$this->objAltConfig = $this->getObject('altconfig', 'config');
$siteRoot = $this->objAltConfig->getsiteRoot();
$moduleUri = $this->objAltConfig->getModuleURI();
$imgPath = $siteRoot . "/" . $moduleUri . '/contextcontent/resources/img/new.png';
$streamerimg = '<img  class="newcontentimg" src="' . $imgPath . '">';


if ($this->isValid('addchapter')) {
    $link = new link($this->uri(array('action' => 'addchapter')));
    $link->link = $addIcon;

    $addChapter = $link->show();
} else {
    $addChapter = '';
}
if ($this->objModuleCatalogue->checkIfRegistered('scorm') && $this->isValid('addchapter')) {
    $link = new link($this->uri(array('action' => 'addscorm')));
    $link->link = $addScormIcon;
    $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    $enableScorm = $objSysConfig->getValue('ENABLE_SCORM', 'contextcontent');

    $addScormChapter = $enableScorm == 'true' ? $link->show() : '';
} else {
    $addScormChapter = '';
}
echo '<h1>' . $this->objContext->getTitle() . ' ' . $addChapter . ' ' . $addScormChapter . '</h1>';

$counter = 1;
$notVisibleCounter = 0;
$addedCounter = 0;

// Form for Quick Jump to a Chapter
/**
$form = new form($this->uri(array('action' => 'viewchapter')));
$form->method = 'GET';

$module = new hiddeninput('module', 'contextcontent');
$form->addToForm($module->show());

$action = new hiddeninput('action', 'viewchapter');
$form->addToForm($action->show());

$label = new label($this->objLanguage->languageText('mod_contextcontent_jumptochapter', 'contextcontent') . ': ', 'input_id');
$form->addToForm($label->show());

$dropdown = new dropdown('id');

// End Form
**/
$chapterList = '<div id="allchapters">';

$objWashout = $this->getObject('washout', 'utilities');
$todays_date = date('Y-m-d H:i');
$today = strtotime($todays_date);
foreach ($chapters as $chapter) {
    $showChapter = TRUE;

    if ($chapter['visibility'] == 'N') {
        $showChapter = FALSE;
    }

    if ($this->isValid('viewhiddencontent')) {
        $showChapter = TRUE;
    }


    $releasedate = strtotime($chapter['releasedate']);
    $enddate = strtotime($chapter['enddate']);

    //compate dates here, then decide on visibility
    if (!empty($releasedate) && !empty($enddate)) {
        if (($today <= $releasedate)) {
            $showChapter = FALSE;
            if ($this->isValid('addchapter')) {
                $showChapter = TRUE;
                $chapter['chaptertitle'] = $chapter['chaptertitle'] . '&nbsp;(' . $this->objLanguage->languageText('mod_contextcontent_hidden', 'contextcontent', ' Hidden') . ')';
            }
        }
        if ($enddate < $today) {
            $showChapter = FALSE;
            if ($this->isValid('addchapter')) {
                $showChapter = TRUE;
                $chapter['chaptertitle'] = $chapter['chaptertitle'] . '&nbsp;(' . $this->objLanguage->languageText('mod_contextcontent_hidden', 'contextcontent', ' Hidden') . ')';
            }
        }
    }

    if ($showChapter) {
        $addedCounter++;
        if ($chapter['scorm'] == 'Y') {

            // Get List of Pages in the Chapter
            $chapterPages = $this->objContentOrder->getTree($this->contextCode, $chapter['chapterid'], 'htmllist');

            if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
                $hasPages = FALSE;
                //$dropdown->addOption($chapter['chapterid'], $chapter['chaptertitle'], ' disabled="disabled" title="' . $this->objLanguage->languageText('mod_contextcontent_chapterhasnopages', 'contextcontent') . '"');
                $notVisibleCounter++;
            } else {
                $hasPages = TRUE;
                $dropdown->addOption($chapter['chapterid'], $chapter['chaptertitle']);
            }

            $editLink = new link($this->uri(array('action' => 'editscorm', 'id' => $chapter['chapterid'])));
            $editLink->link = $editIcon;

            $deleteLink = new link($this->uri(array('action' => 'deletechapter', 'id' => $chapter['chapterid'])));
            $deleteLink->link = $deleteIcon;

            $addPageLink = new link($this->uri(array('action' => 'addpage', 'chapter' => $chapter['chapterid'])));
            $addPageLink->link = $addPageIcon;



            $chapterLink = new link($this->uri(array('action' => 'viewscorm', 'folderId' => $chapter['introduction'], 'chapterid' => $chapter['chapterid']), $module = 'scorm'));
            $chapterLink->link = $chapter['chaptertitle'];
            if ($this->eventsEnabled) {
                $ischapterlogged = $this->objContextActivityStreamer->getRecord($this->userId, $chapter['chapterid']);
            } else {
                $ischapterlogged = FALSE;
                $streamerimg = "";
            }
            if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
                if ($ischapterlogged == FALSE) {
                    $content = '<h1 class="streamerimg"> ' . $streamerimg . " " . $chapterLink->show();
                } else {
                    $content = '<h1 class="chapterlink">' . $chapterLink->show();
                }
            } else {
                if ($ischapterlogged == FALSE) {
                    $content = '<h1 class="streamerimg"> ' . $streamerimg . " " . $chapterLink->show();
                } else {
                    $content = '<h1 class="chapterlink">' . $chapterLink->show();
                }
            }

            if ($this->isValid('editchapter')) {
                $content .= ' ' . $editLink->show();
            }

            if ($this->isValid('deletechapter')) {
                $content .= ' ' . $deleteLink->show();
            }

            /*
              if ($this->isValid('addpage')) {
              $content .= ' '.$addPageLink->show();
              }
             */
            $content .= '</h1><hr />';
        } else {


            // Get List of Pages in the Chapter
            $chapterPages = $this->objContentOrder->getTree($this->contextCode, $chapter['chapterid'], 'htmllist');

            if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
                $hasPages = FALSE;
                //$dropdown->addOption($chapter['chapterid'], $chapter['chaptertitle'], ' disabled="disabled" title="' . $this->objLanguage->languageText('mod_contextcontent_chapterhasnopages', 'contextcontent') . '"');
                $notVisibleCounter++;
            } else {
                $hasPages = TRUE;
                //$dropdown->addOption($chapter['chapterid'], $chapter['chaptertitle']);
            }

            $editLink = new link($this->uri(array('action' => 'editchapter', 'id' => $chapter['chapterid'])));
            $editLink->link = $editIcon;

            $deleteLink = new link($this->uri(array('action' => 'deletechapter', 'id' => $chapter['chapterid'])));
            $deleteLink->link = $deleteIcon;

            $addPageLink = new link($this->uri(array('action' => 'addpage', 'chapter' => $chapter['chapterid'])));
            $addPageLink->link = $addPageIcon;

            $addPageFromFileLink = new link($this->uri(array('action' => 'addpagefromfile', 'chapterid' => $chapter['chapterid'])));
            $addPageFromFileLink->link = $addPageFromFileIcon;

            $chapterLink = new link($this->uri(array('action' => 'viewchapter', 'id' => $chapter['chapterid'])));
            $chapterLink->link = $chapter['chaptertitle'];

            if ($this->eventsEnabled) {
                $ischapterlogged = $this->objContextActivityStreamer->getRecord($this->userId, $chapter['chapterid']);
            } else {
                $ischapterlogged = FALSE;
                $streamerimg = "";
            }
            if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
                if ($ischapterlogged == FALSE) {
                    $content = '<h1> ' . $streamerimg . " " . $chapter['chaptertitle'];
                } else {
                    $content = '<h1>' . $chapter['chaptertitle'];
                }
            } else {
                if ($ischapterlogged == FALSE) {
                    $content = '<h1> ' . $streamerimg . " " . $chapterLink->show();
                    ;
                } else {
                    $content = '<h1>' . $chapterLink->show();
                }
            }

            if ($this->isValid('editchapter')) {
                $content .= ' ' . $editLink->show();
            }

            if ($this->isValid('deletechapter')) {
                $content .= ' ' . $deleteLink->show();
            }

            if ($this->isValid('addpage')) {
                $content .= ' ' . $addPageLink->show();
                //$content .= ' '.$addPageFromFileLink->show();
            }


            if ($pdfHtmlDoc && trim($chapterPages) != '<ul class="htmlliststyle"></ul>') {

                $pdfLink = new link($this->uri(array('action' => 'viewprintchapter', 'id' => $chapter['chapterid'])));
                $pdfLink->link = $pdfIcon;

                $content .= ' ' . $pdfLink->show();
            }

            $content .= '</h1>';

            //print_r($chapter);

            if ($this->isValid('viewhiddencontent') && $chapter['visibility'] != 'Y') {

                switch ($chapter['visibility']) {
                    case 'I': $notice = $this->objLanguage->code2Txt('mod_contextcontent_studentcanonlyviewintro', 'contextcontent');
                        break;
                    case 'N': $notice = $this->objLanguage->code2Txt('mod_contextcontent_chapternotvisibletostudents', 'contextcontent');
                        break;
                    default: $notice = '';
                        break;
                }
                $content .= '<p class="warning"><strong>' . $this->objLanguage->languageText('mod_contextcontent_note', 'contextcontent') . ': </strong>' . $notice . '</p>';
            }

            $content .= $objWashout->parseText($chapter['introduction']);

            $chapterOptions = array();

            if ($chapter['visibility'] == 'I' && !$this->isValid('viewhiddencontent')) {
                $content .= '<p class="warning">' . ucfirst($this->objLanguage->code2Txt('mod_contextcontent_studentscannotaccesscontent', 'contextcontent')) . '.</p>';

                // Empty variable for use later on
                $chapterPages = '';
            } else {

                if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>' && $this->isValid('viewhiddencontent')) {
                    $content .= '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_contextcontent_chapternewcontentpages', 'contextcontent') . '</div>';

                    // Empty variable for use later on
                    $chapterPages = '';
                } else if (trim($chapterPages) == '<ul class="htmlliststyle"></ul>') {
                    $content .= '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_contextcontent_chapternewcontentpages', 'contextcontent') . '</div>';

                    // Empty variable for use later on
                    $chapterPages = '';
                } else {
                    $chapterOptions[] = '<div id="toc_' . $chapter['chapterid'] . '"  style="display:none">' . $chapterPages . '</div><a href="#" onclick="Effect.toggle(\'toc_' . $chapter['chapterid'] . '\', \'slide\'); return false;"><strong>' . $this->objLanguage->languageText('mod_contextcontent_showhidecontents', 'contextcontent') . ' ...</strong></a>';
                }
            }


            $addPageLink = new link($this->uri(array('action' => 'addpage', 'chapter' => $chapter['chapterid'])));
            $addPageLink->link = $this->objLanguage->languageText('mod_contextcontent_addapagetothischapter', 'contextcontent');

            $moveUpLink = new link($this->uri(array('action' => 'movechapterup', 'id' => $chapter['contextchapterid'])));
            $moveUpLink->link = $this->objLanguage->languageText('mod_contextcontent_movechapterup', 'contextcontent');

            $moveDownLink = new link($this->uri(array('action' => 'movechapterdown', 'id' => $chapter['contextchapterid'])));
            $moveDownLink->link = $this->objLanguage->languageText('mod_contextcontent_movechapterdown', 'contextcontent');

            //$content .= '<br />';

            if ($this->isValid('addpage')) {
                //$content .= $addPageLink->show();
            }

            if (count($chapters) > 1 && $counter > 1 && $this->isValid('movechapterup')) {
                $chapterOptions[] = $moveUpLink->show();
            }

            if ($counter < count($chapters) && $this->isValid('movechapterdown')) {
                $chapterOptions[] = $moveDownLink->show();
            }

            if (count($chapterOptions) > 0) {

                $divider = '';
                foreach ($chapterOptions as $option) {
                    $content .= $divider . $option;
                    $divider = ' / ';
                }
            }
        }
        $chapterList .= '<div class="chapterlisting">' . $content . '</div><hr />';
    }

    $counter++;
}

$chapterList .= '</div>';

/**
if (count($chapters) > 1) {
    $form->addToForm($dropdown->show());

    $button = new button('', 'Go');
    $button->setToSubmit();

    if ($notVisibleCounter == $addedCounter) {
        $button->extra = ' disabled="disabled" ';
    }

    $form->addToForm(' ' . $button->show());

    echo $form->show();
}
**/

echo $chapterList;

if ($this->isValid('addchapter')) {
    $link = new link($this->uri(array('action' => 'addchapter')));
    $link->link = $this->objLanguage->languageText('mod_contextcontent_addanewchapter', 'contextcontent');

    echo $link->show();
}


if ($this->objModuleCatalogue->checkIfRegistered('feed')) {
    //creating the rss feeds link
    $link = new link($this->uri(array(
                        'action' => 'rss', 'title' => $this->objContext->getTitle(), 'rss_contextcode' => $this->contextCode)));
    $objIcon->setIcon('rss');
    $objIcon->alt = null;
    $objIcon->title = null;
    $link->link = $this->objLanguage->languageText('mod_contextcontent_feedstext', 'contextcontent');
    echo '<br/><br clear="left" />' . $objIcon->show() . ' ' . $link->show();
}

if ($this->objModuleCatalogue->checkIfRegistered('kbookmark') && $this->objUser->isLoggedIn()) {
    //creating the bookmark button
    $this->bookmarkbutton = $this->getObject('bookmarkbutton', 'kbookmark');
    $this->bookmarkbutton->bookmark_button($this->objContext->getTitle(), str_replace('&amp;', '&', $this->uri(array('action' => 'rsscall', 'rss_contextcode' => $this->contextCode))), '', '');
    echo $this->bookmarkbutton->show();
}
?>
