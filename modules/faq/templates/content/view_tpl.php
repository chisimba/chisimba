<?php
$ret = "";
// Load classes.
$this->loadHTMLElement("form");
$this->loadHTMLElement("textinput");
$this->loadHTMLElement("dropdown");
$this->loadHTMLElement("button");
$this->loadHTMLElement("link");
$this->loadHTMLElement("hiddeninput");
$this->loadHTMLElement("label");

$objWashOut=$this->getObject('washout','utilities');
//
//Use to check for admin user:
$isAdmin = $this->objUser->isAdmin();

//Use to check for lecturer in context:
$isLecturer = false;
if ($contextId != 'root') {
    $userPKId = $this->objUser->PKId($this->objUser->userId());
    $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
    $groupid = $objGroups->getLeafId(array($contextId, 'Lecturers'));
    if ($objGroups->isGroupMember($userPKId, $groupid)) {
        $isLecturer = true;
    }
}

if(!$this->objUser->isLoggedIn()){
    $isLecturer=false;
    $isAdmin=false;
}


//
// Display error string if neccessary.
if ($error != "") {
    $ret .= "<span class=\"error\">";
    $ret .= $error;
    $ret .= "</span>";
    $ret .= "<br/>";
}

// Add an entry if not displaying "All Categories".
if ($isAdmin || $isLecturer) {


    // Add an entry.
    $addLink = "<a href=\"" .
            $this->uri(array(
                'module' => 'faq',
                'action' => 'add',
                'category' => $categoryId
            ))
            . "\">";
    $icon = $this->getObject('geticon', 'htmlelements');
    $icon->setIcon('add');
    $icon->alt = "Add";
    $icon->align = false;
    $addLink .= $icon->show();
    $addLink .= "</a>";
} else {
    $addLink = NULL;
}
$ret .= "<h1>" .
 $objLanguage->languageText("word_faq") . ": " . ' ' . $addLink .
 "</h1>";

// Category Form.
$form = new form("category", $this->uri(array('module' => 'faq', 'action' => 'changeCategory')));
$form->method = 'GET';
$form->setDisplayType(3);
$moduleHiddenInput = new hiddeninput('module', 'faq');
$form->addToForm($moduleHiddenInput->show());
$actionHiddenInput = new hiddeninput('action', 'changeCategory');
$form->addToForm($actionHiddenInput->show());
$label = new label($objLanguage->languageText("faq_category", "faq") . ": ", 'input_category');
$form->addToForm($label->show());
$dropdown = new dropdown('category');
$dropdown->addOption("All Categories", "All Categories");
foreach ($categories as $item) {
    $dropdown->addOption($item["id"], $item["categoryname"]);
}
$dropdown->setSelected($categoryId);
$form->addToForm($dropdown);
$form->addToForm("&nbsp;");
$button = new button("submit", $objLanguage->languageText("word_go"));
$button->setToSubmit();
$form->addToForm($button);
$ret .= $form->show();
$ret .= "<br/>";

if (!empty($list)) {
    // List the questions as href links to link to the main body of the FAQ.
    $index = 1;
    // show using an ordered list
    $ret .= '<ol>';
    foreach ($list as $element) {
        $ret .= "<li><a href=\"#" . $element["id"] . "\">";
        $ret .= $objWashOut->parseText($element["question"]);
        $ret .= "</a></li>";
        $index++;
    }
    $ret .= '</ol>';
    $ret .= "<br/>";
}

// List the questions and answers.
$index = 1;
$found = false;
foreach ($list as $element) {
    // Anchor tag for link to top of page.
    $ret .= "<a id=\"" . $element["id"] . "\"></a>";
    $found = true;
    $ret .= '<div class="wrapperDarkBkg">';
    $ret .= "<b>" . $index . ": " . "</b>" . $objWashOut->parseText($element["question"]);
        if ($isAdmin || $isLecturer) {
        // Edit an entry.
        $icon = $this->getObject('geticon', 'htmlelements');
        $icon->setIcon('edit');
        $icon->alt = "Edit";
        $icon->align = false;
        $ret .= "<a href=\"" .
        $this->uri(array(
            'module' => 'faq',
            'action' => 'edit',
            'category' => $categoryId,
            'id' => $element["id"]
        ))
        . "\">" . $icon->show() . "</a>";
        $ret .= "&nbsp;";
        // Delete an entry.
        $objConfirm = &$this->newObject('confirm', 'utilities');
        $icon = $this->getObject('geticon', 'htmlelements');
        $icon->setIcon('delete');
        $icon->alt = "Delete";
        $icon->align = false;
        $objConfirm->setConfirm(
        $icon->show(),
        $this->uri(array(
            'action' => 'deleteconfirm',
            'category' => $categoryId,
            'id' => $element["id"]
        )),
        $objLanguage->languageText('faq_suredelete'));
        $ret .= $objConfirm->show();

        // Scroll down one entry.
        if ($element["nextid"] != null) {
            $index = $index + 1;
            $icon = $this->getObject('geticon', 'htmlelements');
            $icon->setIcon('down');
            $icon->alt = "Down";
            $icon->align = false;
            $ret .= "<a href=\"#" . $element["nextid"] . "\">" . $icon->show() . "</a>";
            $ret .= "&nbsp;";
            $index--;
        }

        if ($index > 1) {
            // Scroll up one entry.
            $index = $index - 1;
            $icon = $this->getObject('geticon', 'htmlelements');
            $icon->setIcon('up');
            $icon->alt = "Up";
            $icon->align = false;
            $ret .= "<a href=\"#" . $element["previd"] . "\">" . $icon->show() . "</a>";
            $ret .= "&nbsp;";
            $index++;
        }
    }
    $ret .= '<div class="wrapperLightBkg">';
    $ret .= $objWashOut->parseText($element["answer"]);
    $ret .= "&nbsp;";
    $ret .= '</div></div>';
    $index++;
}
// If no entries then display message.
if (!$found) {
    $ret .= "<div class=\"noRecordsMessage\">" . $objLanguage->languageText("faq_noentries", "faq") . "</div>";
}

$link = new link($this->uri(NULL));
$link->link = $objLanguage->languageText("mod_faq_faqhome", "faq", 'FAQ Home');
$ret .= $link->show();

if ($isAdmin || $isLecturer) {
    $link = new link($this->uri(array('action' => 'add', 'category' => $categoryId)));
    $link->link = $objLanguage->languageText("faq_addnewentry", "faq");

    $ret .= ' / ' . $link->show();
}
echo "<div class='faq_main'>$ret</div>";
?>