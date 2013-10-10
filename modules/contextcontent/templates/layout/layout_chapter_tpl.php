<?php
$showNav = TRUE;
$objModule = $this->getObject('modules', 'modulecatalogue');
if (isset($hideNavSwitch) && $hideNavSwitch) {
    $showNav = FALSE;
}

if ($showNav) {
    ?>
<script type="text/javascript">
    //<![CDATA[

    function changeNav (type) {
        var url = 'index.php';
        var pars = 'module=contextcontent&action=changenavigation&id=<?php echo $currentPage; ?>&type='+type;
        var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showResponse} );
    }

    function showResponse (originalRequest) {
        var newData = originalRequest.responseText;

        if (newData != '') {
            $('contentnav').innerHTML = newData;
            adjustLayout();
        }
    }
    //]]>
</script>
    <?php
}

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');



if (isset($currentChapter)) {



    if (!isset($currentChapterTitle)) {
        $currentChapterTitle = $this->objContextChapters->getContextChapterTitle($currentChapter);
    }

    if (!isset($currentPage)) {
        $currentPage = '';
    }

    $heading = new htmlheading();

    $heading->str = $currentChapterTitle;
    $heading->type = 3;
    /*
    $form = new form ('searchform', $this->uri(array('action'=>'search')));
    $form->method = 'GET';

    $hiddenInput = new hiddeninput('module', 'contextcontent');
    $form->addToForm($hiddenInput->show());

    $hiddenInput = new hiddeninput('action', 'search');
    $form->addToForm($hiddenInput->show());

    $textinput = new textinput ('contentsearch');
    $button = new button ('searchgo', 'Go');
    $button->setToSubmit();

    $form->addToForm($textinput->show().' '.$button->show());

    $objFieldset = $this->newObject('fieldset', 'htmlelements');
    $label = new label ($this->objLanguage->languageText('mod_forum_searchfor', 'forum', 'Search for').':', 'input_contentsearch');

    $objFieldset->setLegend($label->show());
    $objFieldset->contents = $form->show();
    */
    $header = new htmlHeading();
    $header->str = ucwords($this->objLanguage->code2Txt('mod_contextcontent_name', 'contextcontent', NULL, '[-context-] Content'));
    $header->type = 2;

    $left = $header->show();

    $pageId = isset($currentPage) ? $currentPage : '';
    $id = isset($currentChapter) ? $currentChapter : '';
    $left .= $heading->show();

    $navigationType = $this->getSession('navigationType', 'tree');



    if ($navigationType == 'tree') {
        $left .= '<div id="contentnav">';
        $left .= $this->objContentOrder->getTree($this->contextCode, $currentChapter, 'htmllist', $pageId, 'contextcontent');

        if ($showNav) {
            $left .= '<hr /><p><a href="javascript:changeNav(\'twolevel\');">'.$this->objLanguage->languageText('mod_contextcontent_viewtwolevels', 'contextcontent', 'View Two Levels at a time').' ...</a><br /><a href="javascript:changeNav(\'bookmarks\');">'.$this->objLanguage->languageText('mod_contextcontent_viewbookmarkedpages', 'contextcontent', 'View Bookmarked Pages').'</a></p>';
        }

        $left .= '</div>';
    }  else if ($navigationType == 'bookmarks') {
        $left .= '<div id="contentnav">';
        $left .= $this->objContentOrder->getBookmarkedPages($this->contextCode, $currentChapter, $pageId);

        if ($showNav) {
            $left .= '<hr /><p><a href="javascript:changeNav(\'twolevel\');">'.$this->objLanguage->languageText('mod_contextcontent_viewtwolevels', 'contextcontent', 'View Two Levels at a time').' ...</a><br /><a href="javascript:changeNav(\'tree\');">'.$this->objLanguage->languageText('mod_contextcontent_viewastree', 'contextcontent', 'View as Tree').'...</a></p>';
        }

        $left .= '</div>';
    }else {
        $left .= '<div id="contentnav">';
        $left .= $this->objContentOrder->getTwoLevelNav($this->contextCode, $currentChapter, $pageId);

        if ($showNav) {
            $left .= '<hr /><p><a href="javascript:changeNav(\'tree\');">'.$this->objLanguage->languageText('mod_contextcontent_viewastree', 'contextcontent', 'View as Tree').'...</a>';
            $left .= '<br /><a href="javascript:changeNav(\'bookmarks\');">'.$this->objLanguage->languageText('mod_contextcontent_viewbookmarkedpages', 'contextcontent', 'View Bookmarked Pages').'</a></p>';
        }

        $left .= '</div>';
    }

    if ($this->isValid('addpage')) {
        $addLink = new link ($this->uri(array('action'=>'addpage', 'chapter'=>$currentChapter, 'id'=>$currentPage)));
        $addLink->link = 'Add a Page';

        $addPageFromFileLink = new link($this->uri(array('action'=>'addpagefromfile', 'chapterid'=>$currentChapter)));
        $addPageFromFileLink->link = $this->objLanguage->languageText('mod_contextcontent_createpagefromfile', 'contextcontent', 'Create page from file');
        $scormInstalled = $objModule->checkIfRegistered("scorm");
        if ($scormInstalled) {
            $addScormLink = new link ($this->uri(array('action'=>'addscormpage', 'id'=>$id, 'context'=>$this->contextCode, 'chapter'=>$currentChapter)));
            $addScormLink->link = $this->objLanguage->languageText('mod_contextcontent_addcontextscormpages','contextcontent');
            $scormLink = $addScormLink->show();
        } else {
            $scormLink = NULL;
        }
        $left .= '<hr /><p>'.$addLink->show().'&nbsp;&nbsp;'.$scormLink.'</p>';
    }

    $returnLink = new link ($this->uri(NULL));
    $returnLink->link = $this->objLanguage->languageText('mod_contextcontent_returntochapterlist', 'contextcontent', 'Return to Chapter List');

    $left .= '<hr /><p>'.$returnLink->show().'</p>';
    $left = '<div id="context_left_nav">' . $left . '</div>';

    //Add toolbar
    $toolbar = $this->getObject('contextsidebar', 'context');
    $objFieldset = $this->newObject('fieldset', 'htmlelements');
    $objFieldset->contents = $toolbar->show();
//$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    $cols = $this->objSysConfig->getValue('CONTEXTCONTENT_COLUMNS', 'contextcontent');
    $cols = (integer)$cols;
    $cssLayout = $this->newObject('csslayout', 'htmlelements');
    $cssLayout->setNumColumns($cols);
    switch ($cols) {
        case 1:

            break;
        case 2:
            $cssLayout->setLeftColumnContent($left . $objFieldset->show());
            break;
        case 3:
        default:
            $cssLayout->setLeftColumnContent($left);
            $cssLayout->setRightColumnContent($objFieldset->show());
            break;
    }
    $cssLayout->setMiddleColumnContent($this->getContent());
    echo $cssLayout->show();

} else {
    echo $this->getContent();
}
?>
