<?php
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->objAltConfig = $this->getObject('altconfig', 'config');
$this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$enableViewActivityLog = $this->objSysConfig->getValue('ENABLE_VIEWACTIVITYLOGS', 'contextcontent');
$siteRoot = $this->objAltConfig->getsiteRoot();
$moduleUri = $this->objAltConfig->getModuleURI();
$imgPath = $siteRoot . "/" . $moduleUri . '/contextcontent/resources/img/new.png';
$streamerimg = '<img  class="newcontentimg" src="' . $imgPath . '">';

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

$form = new form('searchform', $this->uri(array('action' => 'search')));
$form->method = 'GET';

$hiddenInput = new hiddeninput('module', 'contextcontent');
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('action', 'search');
//$form->addToForm($hiddenInput->show());

$textinput = new textinput('contentsearch', $this->getParam('contentsearch'));
$button = new button('searchgo', 'Go');
$button->setToSubmit();
//Add toolbar
$toolbar = $this->getObject('contextsidebar', 'context');

//$form->addToForm($textinput->show().' '.$button->show());

$objFieldset = $this->newObject('fieldset', 'htmlelements');
$label = new label('Search for:', 'input_contentsearch');

//$objFieldset->setLegend($label->show());
//$objFieldset->contents = $form->show();

$header = new htmlHeading();
$header->str = ucwords($this->objLanguage->code2Txt('mod_contextcontent_name', 'contextcontent', NULL, '[-context-] Content'));
$header->type = 2;
$content = "";
//$content .= $header->show();
//$content .= $objFieldset->show();

$content .= '<h3>Chapters:</h3>';
$chapters = $this->objContextChapters->getContextChapters($this->contextCode);

$todays_date = date('Y-m-d H:i');
$today = strtotime($todays_date);

if (count($chapters) > 0) {
    $content .= '<ol>';
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
            $bookmarkLink = new link("#{$chapter['chapterid']}");
            $bookmarkLink->link = '';
            $bookmarkLink->title = $this->objLanguage->languageText('mod_contextcontent_scrolltohapter', 'contextcontent');
            $showImg = "";
            if ($this->eventsEnabled) {
                // Get List of Pages in the Chapter
                //$chapterPages = $this->objContentOrder->getTree($this->contextCode, $chapter['chapterid'], 'htmllist');
                $ischapterlogged = $this->objContextActivityStreamer->getRecord($this->objUser->userId(), $chapter['chapterid']);
                if ($ischapterlogged == FALSE) {
                    $showImg = $streamerimg;
                } else {
                    $showImg = "";
                }
            }
            //if ($chapter['pagecount'] == 0) {
            //    $content .= '<li title="Chapter has no content pages">'.$chapter['chaptertitle'];
            //} else {

            if ($chapter['scorm'] == 'Y') {
                $link = new link($this->uri(array('action' => 'viewscorm', 'mode' => 'chapter', 'folderId' => $chapter['introduction'], 'chapterid' => $chapter['chapterid']), $module = 'scorm'));
                $link->link = $chapter['chaptertitle'] . $showImg;
                $content .= '<li>' . $link->show();
            } else {
                $link = new link($this->uri(array('action' => 'viewchapter', 'id' => $chapter['chapterid'])));
                $link->link = $chapter['chaptertitle'] . $showImg;
                $content .= '<li>' . $link->show();
            }
            //}

            if (isset($showScrollLinks) && $showScrollLinks) {
                $content .= " " . $bookmarkLink->show() . '</li>';
            }
        }
    }
    $content .= '</ol>';
}

if ($this->isValid('addchapter')) {
    $link = new link($this->uri(array('action' => 'addchapter')));
    $link->link = $this->objLanguage->languageText('mod_contextcontent_addanewchapter', 'contextcontent');

    $content .= '<br /><p>' . $link->show() . '</p>';
}
//Show logs if configured true
if ($enableViewActivityLog == 'true' && $this->isValid('addchapter')) {
    $link = new link($this->uri(array('action' => 'viewcontextcontentusage')));
    $link->link = $trackerimg . '&nbsp;' . ucWords($this->objLanguage->code2Txt('mod_contextcontent_viewcontextcontentusage', 'contextcontent'));

    $content .= '<br />' . $link->show() . '';
    $link = new link($this->uri(array('action' => 'viewlogs')));
    $link->link = $trackerimg . '&nbsp;' . $this->objLanguage->languageText('mod_contextcontent_useractivitylogs', 'contextcontent');

    $content .= '<br />' . $link->show() . '';

}
$content = '<div id="context_left_nav">' . $content . "</div>";
$objFieldset->contents = $toolbar->show();
$cssLayout->setLeftColumnContent($content);
$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setRightColumnContent($objFieldset->show());
echo $cssLayout->show();
?>
