<?php
$showNav = TRUE;

if (isset($hideNavSwitch) && $hideNavSwitch) {
    $showNav = FALSE;
}

if ($showNav) {
    ?>
<script type="text/javascript">
    //<![CDATA[

    function changeNav (type) {
        var url = 'index.php';
        var pars = 'module=learningcontent&action=changenavigation&id=<?php echo $currentPage; ?>&type='+type;
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
    
    $hiddenInput = new hiddeninput('module', 'learningcontent');
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
    $header->str = ucwords($this->objLanguage->code2Txt('mod_learningcontent_name', 'learningcontent', NULL, '[-context-] Content'));
    $header->type = 2;

    $left = $header->show();

    $pageId = isset($currentPage) ? $currentPage : '';
    $left .= $heading->show();

    $navigationType = $this->getSession('navigationType', 'tree');



    if ($navigationType == 'tree') {
        $left .= '<div id="contentnav">';
        $left .= $this->objContentOrder->getTree($this->contextCode, $currentChapter, 'htmllist', $pageId, 'learningcontent');

        if ($showNav) {
            $left .= '<hr /><p><a href="javascript:changeNav(\'twolevel\');">'.$this->objLanguage->languageText('mod_learningcontent_viewtwolevels', 'learningcontent', 'View Two Levels at a time').' ...</a><br /><a href="javascript:changeNav(\'bookmarks\');">'.$this->objLanguage->languageText('mod_learningcontent_viewbookmarkedpages', 'learningcontent', 'View Bookmarked Pages').'</a></p>';
        }

        $left .= '</div>';
    }  else if ($navigationType == 'bookmarks') {
            $left .= '<div id="contentnav">';
            $left .= $this->objContentOrder->getBookmarkedPages($this->contextCode, $currentChapter, $pageId);

            if ($showNav) {
                $left .= '<hr /><p><a href="javascript:changeNav(\'twolevel\');">'.$this->objLanguage->languageText('mod_learningcontent_viewtwolevels', 'learningcontent', 'View Two Levels at a time').' ...</a><br /><a href="javascript:changeNav(\'tree\');">'.$this->objLanguage->languageText('mod_learningcontent_viewastree', 'learningcontent', 'View as Tree').'...</a></p>';
            }

            $left .= '</div>';
        }else {
            $left .= '<div id="contentnav">';
            $left .= $this->objContentOrder->getTwoLevelNav($this->contextCode, $currentChapter, $pageId);

            if ($showNav) {
                $left .= '<hr /><p><a href="javascript:changeNav(\'tree\');">'.$this->objLanguage->languageText('mod_learningcontent_viewastree', 'learningcontent', 'View as Tree').'...</a>';
                $left .= '<br /><a href="javascript:changeNav(\'bookmarks\');">'.$this->objLanguage->languageText('mod_learningcontent_viewbookmarkedpages', 'learningcontent', 'View Bookmarked Pages').'</a></p>';
            }

            $left .= '</div>';
        }

    if ($this->isValid('addpage')) {
        $addLink = new link ($this->uri(array('action'=>'addpage', 'chapter'=>$currentChapter, 'id'=>$currentPage)));
        $addLink->link = 'Add a Page';

        $addScormLink = new link ($this->uri(array('action'=>'addscormpage', 'id'=>$page['id'], 'context'=>$this->contextCode, 'chapter'=>$page['chapterid'])));
        $addScormLink->link = $this->objLanguage->languageText('mod_learningcontent_addcontextscormpages','learningcontent');

        $left .= '<hr /><p>'.$addLink->show().'&nbsp;&nbsp;'.$addScormLink->show().'</p>';
    }

    $returnLink = new link ($this->uri(NULL));
    $returnLink->link = $this->objLanguage->languageText('mod_learningcontent_returntochapterlist', 'learningcontent', 'Return to Chapter List');

    $left .= '<hr /><p>'.$returnLink->show().'</p>';

    //Add toolbar
    $toolbar = $this->getObject('contextsidebar', 'context');
    $objFieldset = $this->newObject('fieldset', 'htmlelements');
    $objFieldset->contents = $toolbar->show();
    $cssLayout = $this->newObject('csslayout', 'htmlelements');
    $cssLayout->setNumColumns(3);
    $cssLayout->setLeftColumnContent($left);
    $cssLayout->setMiddleColumnContent($this->getContent());
    $cssLayout->setRightColumnContent($objFieldset->show());
    echo $cssLayout->show();

} else {
    echo $this->getContent();
}
?>
