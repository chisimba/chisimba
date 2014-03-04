<?php
$ret = "";
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');

// Build the search form.
$form = new form ('searchform', $this->uri(array('action'=>'tag'), 'faq'));
$form->method = 'GET';

$module = new hiddeninput('module', 'faq');
$form->addToForm($module->show());

$action = new hiddeninput('action', 'tag');
$form->addToForm($action->show());

$textinput = new textinput ('tag');
$textinput->size = 60;
$button = new button ('search', 'Search');
$button->setToSubmit();

$form->addToForm('<div align="center">'.$textinput->show().' '.$button->show().'</div>');
$tagCloudContent=$form->show().'<br>';

// Show the heading
$objHeading = new htmlheading();
$objHeading->type = 5;
$objHeading->cssClass = 'featureboxheader';

// Show the add link
$objLink =& $this->getObject('link','htmlelements');
$objLink->link($this->uri(array('module'=>'faq', 'action'=>'add')));
$iconAdd = $this->getObject('geticon','htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("faq_addnewentry", "faq");
$iconAdd->title = $objLanguage->languageText("faq_addnewentry", "faq");
$iconAdd->align=false;
$objLink->link = $iconAdd->show();

$tagCloudLink = new link ($this->uri(array('action'=>'tagcloud'), 'faq'));
$tagCloudLink->link = 'View All Tags';

$tagCloudContent .= '<span style="text-align:center">' . $tagCloud 
  . '</span><br /><div class="faq_alltags">'.$tagCloudLink->show()
  . "</faq>";


// Display the contex as part of the title if they are in a context
if ($contextTitle != NULL & $contextTitle != "Default") {
    $objHeading->str = $contextTitle.': ' 
      .$objLanguage->languageText("phrase_faq","system", 'Frequently Asked Questions');
} else {
    $objHeading->str =  $objLanguage->languageText("phrase_faq","system", 'Frequently Asked Questions');
}

// Make a tabbed box
$objTabs = $this->newObject('tabcontent', 'htmlelements');
$objTabs->width = '95%';

// Show Add Item link
if ($this->isValid('add'))
{
	$addLink = $objLink->show();
}else {
	$addLink = "";
}
$objHeading->str .='&nbsp;&nbsp;&nbsp;&nbsp;' . $addLink;
if (count($categories) > 0 && $this->userHasModifyAccess()) {
    $ret .= $objHeading->show() . '<br/>' . $tagCloudContent;
}

//$ret .= $objHeading->show();

if (count($categories) == 0) {
    $ret .= '<div class="noRecordsMessage">No FAQ Categories available</div>';
} else {
    $ret .= '<ol>';

    $objIcon = $this->newObject('geticon','htmlelements');

    $objIcon->setIcon('edit');
    $objIcon->alt=$objLanguage->languageText("word_edit");
    $objIcon->title=$objLanguage->languageText("word_edit");

    $editIcon = $objIcon->show();

    $objIcon->setIcon('delete');
    $objIcon->alt=$objLanguage->languageText("word_delete");
    $objIcon->title=$objLanguage->languageText("word_delete");

    $deleteIcon = $objIcon->show();

    foreach ($categories as $item)
    {

        $numItems = $this->objFaqEntries->getNumCategoryItems($item['id']);

        // Create link to category
        $categoryLink = new link($this->uri(array('action'=>'view','category'=>$item['id'])));
        $categoryLink->link = $item['categoryname'];
        $categoryLink->title = $this->objLanguage->languageText('mod_faq_viewcategory', 'faq');

        $ret .= '<li>' . $categoryLink->show() . '&nbsp;<span class="indicator" >&nbsp;&nbsp;&nbsp;' . $numItems . '&nbsp;&nbsp;&nbsp;</span>';

                if ($this->userHasModifyAccess()) {
            // Create the edit link.
            $editLink = new link($this->uri(array('action'=>'editcategory', 'id'=>$item['id'])));
            $editLink->link = $editIcon;

            // Create the delete link.
            $objConfirm = $this->newObject('confirm','utilities');

            $objConfirm->setConfirm(
                $deleteIcon,
                $this->uri(array(
                    'action'=>'deletecategoryconfirm',
                    'id'=>$item["id"]
                )),
                $objLanguage->languageText('phrase_suredelete')
            );
			if ($this->isValid('add'))
			{
            	$ret .= ' &nbsp; '.$editLink->show().' '.$objConfirm->show();
			}
        }

        $ret .= '</li>';
    }
    $ret .= '</ol>';
}



$addLink = new link("javascript:showHideAddCategory();");
$addLink->link = $objLanguage->languageText('mod_faq_addcategory','faq');

echo '
<script type="text/javascript">
// <![CDATA[
function showHideAddCategory()
{
    jQuery("#addfaqcategory").toggle();
    adjustLayout();
}
// ]]>
</script>
';


if ($this->isValid('add'))
{
	$ret .= '<p>'.$addLink->show()./*' / '.$returnToFaqLink->show().*/'</p>';
}
	$ret .= '<div id="addfaqcategory" style="display:none;">';


$objHeading = new htmlheading();
$objHeading->type=3;
$objHeading->str =$objLanguage->languageText("mod_faq_addcategory","faq");
$ret .= $objHeading->show();

// Load the classes.
$this->loadClass("form","htmlelements");
$this->loadClass("textinput","htmlelements");
$this->loadClass("button","htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('label', 'htmlelements');


// Create the form.
$form = new form("createcategory", $this->uri(array('action'=>'addcategoryconfirm')));
$formTable = $this->newObject('htmltable', 'htmlelements');


$textInput = new textinput("category", NULL);
$textInput->size = 40;


$taglabel = new label ($this->objLanguage->languageText('mod_faq_tags', 'faq', 'Category Tag'), 'tagslabel');
$catlabel = new label ($this->objLanguage->languageText('mod_faq_category', 'faq', 'Category'), 'catlabel');
$faqTags = new textarea('faqtags');

$formTable->startRow();
$formTable->endRow();
$form->setDisplayType(1);
$form->addToForm($textInput->show());
$form->addToForm("&nbsp;");
$button = new button("submit", $objLanguage->languageText("word_save"));
$button->setToSubmit();

$cancelButton = new button("submit", $objLanguage->languageText('word_cancel'));
$cancelButton->setOnClick("showHideAddCategory();");

$form->addToForm($button->show().' / '.$cancelButton->show());
$form->addRule('category', 'Please enter the name of the category', 'required');
// Show the form.
$ret .= $form->show();

$ret .= '</div>';

echo "<div class='featurebox'><div class='featureboxcontent' >$ret</div></div>"
?>