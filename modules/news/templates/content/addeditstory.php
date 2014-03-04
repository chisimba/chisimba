<?php




$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

$loadingIcon = $objIcon->show();

if ($mode == 'edit') {
    $formAction = 'updatestory';
    $title = $this->objLanguage->languageText('mod_news_editstory', 'news', 'Edit Story').': '.$story['storytitle'];
    $buttonText = $this->objLanguage->languageText('mod_news_updatestory', 'news', 'Update Story');
} else {
    $formAction = 'savestory';
    $title = $this->objLanguage->languageText('mod_news_addnewstory', 'news', 'Add New Story');
    $buttonText = $this->objLanguage->languageText('mod_news_savestory', 'news', 'Save Story');
}

// Header
$header = new htmlheading();
$header->type = 1;
$header->str = $title;
echo $header->show();

// Create Form
$form = new form ('addedit', $this->uri(array('action'=>$formAction)));

$formTable = $this->newObject('htmltable', 'htmlelements');

$storyTitle = new textinput('storytitle');
$storyTitle->size = 60;

if ($mode == 'edit') {
    $storyTitle->value = htmlentities($story['storytitle']);
}

$label = new label ($this->objLanguage->languageText('mod_news_storytitle', 'news', 'Story Title'), 'input_storytitle');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($storyTitle->show());
$formTable->endRow();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell('&nbsp;');
$formTable->endRow();

$datePicker = $this->newObject('datepicker', 'htmlelements');
$datePicker->name = 'storydate';

if ($mode == 'edit') {
    $datePicker->defaultDate = $story['storydate'];
}

$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('mod_news_storydate', 'news', 'Story Date'));
$formTable->addCell($datePicker->show());
$formTable->endRow();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell('&nbsp;');
$formTable->endRow();


$datePicker = $this->newObject('datepicker', 'htmlelements');
$datePicker->name = 'storyexpirydate';

if ($mode == 'edit') {
    $datePicker->defaultDate = date("Y-m-d",strtotime ($story['datetopstoryexpire']));
}

$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('mod_news_storyexpirydate', 'news', 'Expiry Date'));
$formTable->addCell($datePicker->show());
$formTable->endRow();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell('&nbsp;');
$formTable->endRow();

$datePicker = $this->newObject('datepicker', 'htmlelements');
$datePicker->name = 'storydatepublish';

$radio = new radio ('publishon');
$radio->addOption('now', $this->objLanguage->languageText('word_immediately', 'word', 'Immediately'));

$objTimePicker = $this->newObject('timepicker', 'htmlelements');

if ($mode == 'add') {
	$radio->setSelected('now');
	$objTimePicker->setSelectedNow();
}

$radio1 = new radio ('publishon');
$radio1->addOption('wait', $this->objLanguage->languageText('word_on', 'system', 'on').' ');




if ($mode == 'edit') {
	$publishDate = explode(' ', $story['dateavailable']);
    $datePicker->defaultDate = $publishDate[0];
	
    $objTimePicker->setSelected($publishDate[1]);
	
	if ($story['dateavailable'] <= strftime('%Y-%m-%d %H:%M:%S', mktime())) {
		$radio->setSelected('now');
	} else {
		$radio1->setSelected('wait');
	}
}



$publishTable = $this->newObject('htmltable', 'htmlelements');
$publishTable->width = NULL;
$publishTable->startRow();
$publishTable->addCell($radio1->show(), 40);
$publishTable->addCell($datePicker->show());
$publishTable->addCell(' at ', 20);
$publishTable->addCell($objTimePicker->show());
$publishTable->endRow();

$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('mod_news_publishdate', 'news', 'Publish Date').'<br /><em>'.$this->objLanguage->languageText('mod_news_storyavailableinfo', 'news', 'When should this story be available on the site.').'</em>', 150);
$formTable->addCell($radio->show().'<br /><br />'.$publishTable->show());
$formTable->endRow();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell('&nbsp;');
$formTable->endRow();


$sticky = new radio ('sticky');
$sticky->addOption('Y', 'Yes');
$sticky->addOption('N', 'No');

if ($mode == 'edit') {
    $stickySelected = $story['sticky'];
} else {
    $categoryId = $this->getParam('id');
    
    if ($categoryId == '') {
        $stickySelected = 'N';
    } else {
        $category = $this->objNewsCategories->getCategory($categoryId);
        if ($category == FALSE) {
            $stickySelected = 'N';
        } else {
            $stickySelected = $category['defaultsticky'];
        }
    }
}

$sticky->setSelected($stickySelected);
$sticky->setBreakSpace(' &nbsp; ');

$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('mod_news_featuredstory', 'news', 'Featured Story'));
$formTable->addCell($sticky->show().'<br /><em>'.$this->objLanguage->languageText('mod_news_featuredstoryexplanation', 'news', 'Featured Stories are highlighted on the Front Page as a sticky or breaking or top story').'</em>');
$formTable->endRow();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell('&nbsp;');
$formTable->endRow();


// Category

$label = new label ($this->objLanguage->languageText('mod_news_storycategory', 'news', 'Story Category'), ' input_storycategory');
$storyCategory = new dropdown('storycategory');

if (count($categories) > 0) {
	foreach ($categories as $category)
	{
		$storyCategory->addOption($category['id'], $category['categoryname']);
	}
    
    if ($mode == 'edit') {
        $storyCategory->setSelected($story['storycategory']);
    } else {
        $storyCategory->setSelected($this->getParam('id'));
    }
}


$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($storyCategory->show());
$formTable->endRow();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell('&nbsp;');
$formTable->endRow();

$label = new label ($this->objLanguage->languageText('mod_news_storylocation', 'news', 'Story Location'), ' input_parentlocation');

$locationInput = new textinput('storylocation');
$locationInput->size = 50;
$locationInput->cssId = 'storylocation';

$locationInput->extra = ' onkeypress="return submitenter(this,event);"';

if ($mode == 'edit' && $story['storylocation'] != NULL) {
    $locationInput->value = $story['location'];
}

$checkLocation = new button ('checklocation', 'Check');
$checkLocation->setOnClick('checkLocation();');

if ($mode == 'edit' && $story['storylocation'] != NULL) {
    $location = new radio ('location');
    $location->addOption($story['geonameid'], $story['location'].' <em>- '.$this->objLanguage->languageText('word_original', 'word', 'original').'</em>');
    $location->setSelected($story['geonameid']);
    $locationExtra = '<br />'.$location->show().'<br />';
} else {
    $locationExtra = '';
}


$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($locationInput->show().' '.$checkLocation->show().$locationExtra.'<div id="locationloading">'.$loadingIcon.'</div><div id="locationdiv" ></div>');
$formTable->endRow();

$formTable->startRow();
$formTable->addCell('&nbsp;');
$formTable->addCell('&nbsp;');
$formTable->endRow();


$keyTag1 = new textinput('keytag1');
$keyTag1->size = 50;
$keyTag1->cssId = 'keytag1';

if ($mode == 'edit' && isset($keywords[0])) {
    $keyTag1->value = $keywords[0];
}

$keyTag2 = new textinput('keytag2');
$keyTag2->size = 50;
$keyTag2->cssId = 'keytag2';

if ($mode == 'edit' && isset($keywords[1])) {
    $keyTag2->value = $keywords[1];
}

$keyTag3 = new textinput('keytag3');
$keyTag3->size = 50;
$keyTag3->cssId = 'keytag3';

if ($mode == 'edit' && isset($keywords[2])) {
    $keyTag3->value = $keywords[2];
}

$label = new label ($this->objLanguage->languageText('mod_news_keytags', 'news', 'Key Tags'), 'input_keytag1');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($keyTag1->show().'<div id="autocomplete_choices1" class="autocomplete"></div><br />'.$keyTag2->show().'<div id="autocomplete_choices2" class="autocomplete"></div><br />'.$keyTag3->show().'<div id="autocomplete_choices3" class="autocomplete"></div><br />&nbsp;');
$formTable->endRow();

$storyTags = new textarea('storytags');

if ($mode == 'edit' && count($tags) > 0 && is_array($tags)) {
    
    $divider = '';
    
    foreach ($tags as $tag) {
        $storyTags->value .= $divider.$tag;
        $divider = ', ';
    }
}

$label = new label ($this->objLanguage->languageText('mod_news_storytags', 'news', 'Story Tags').'<br /><em>'.$this->objLanguage->languageText('mod_filemanager_separatewithcommas', 'filemanager', 'Separate with commas').'</em>', 'input_storytags');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($storyTags->show().'<br />&nbsp;');
$formTable->endRow();

///


$objImageSelect = $this->newObject('selectimage', 'filemanager');

if ($mode == 'edit') {
    $objImageSelect->defaultFile = $story['storyimage'];
}

$topTable = $this->newObject('htmltable', 'htmlelements');

$topTable->startRow();
$topTable->addCell($formTable->show());
$topTable->addCell($this->objLanguage->languageText('mod_news_storyimage', 'news', 'Story Image').':<br /><br />'.$objImageSelect->show());
$topTable->endRow();


$form->addToForm($topTable->show());

$storyTable = $this->newObject('htmltable', 'htmlelements');

$htmlarea = $this->newObject('htmlarea', 'htmlelements');
$htmlarea->name = 'storytext';

if ($mode == 'edit') {
    $htmlarea->value = $story['storytext'];
}

$storyTable->startRow();
$storyTable->addCell($this->objLanguage->languageText('mod_news_storytext', 'news', 'Story Text'), 150);
$storyTable->addCell($htmlarea->show());
$storyTable->endRow();

$storySource = new textarea('storysource');

if ($mode == 'edit') {
    $storySource->value = $story['storysource'];
}

$label = new label ($this->objLanguage->languageText('mod_news_storysource', 'news', 'Story Source'), 'input_storysource');

$storyTable->startRow();
$storyTable->addCell($label->show());
$storyTable->addCell($storySource->show());
$storyTable->endRow();

$form->addToForm($storyTable->show());

$button = new button ('savestory', $buttonText);
$button->setOnClick("document.forms['addedit'].submit();");

$form->addToForm($button->show());

if ($mode == 'edit') {
    $hiddeninput = new hiddeninput('id', $story['id']);
    $form->addToForm($hiddeninput->show());
}

echo $form->show();

$homeLink = new link ($this->uri(NULL));
$homeLink->link = $this->objLanguage->languageText('mod_news_returntonewshome', 'news', 'Return to News Home');
echo $homeLink->show();
?>
<script type="text/javascript">

//<![CDATA[
    var pars   = 'module=news&action=ajaxkeywords&tag=keytag1';
	new Ajax.Autocompleter("keytag1", "autocomplete_choices1", "index.php", {parameters: pars});
	var pars   = 'module=news&action=ajaxkeywords&tag=keytag2';
	new Ajax.Autocompleter("keytag2", "autocomplete_choices2", "index.php", {parameters: pars});
	var pars   = 'module=news&action=ajaxkeywords&tag=keytag3';
	new Ajax.Autocompleter("keytag3", "autocomplete_choices3", "index.php", {parameters: pars});

//new Ajax.Autocompleter("input_keytag1", "autocomplete_choices", "index.php", {paramName: "value", minChars: 2, updateElement: addItemToList, indicator: 'indicator1'});

//]]>
</script>

<style type="text/css">

div#locationloading {
    display: none;
}

div.autocomplete {
	position:absolute;
	width:250px;
	background-color:white;
	border:1px solid #888;
	margin:0px;
	padding:0px;
}
div.autocomplete ul {
	list-style-type:none;
	margin:0px;
	padding:0px;
}
div.autocomplete ul li.selected { background-color: #ffb;}
div.autocomplete ul li {
	list-style-type:none;
	background-image: none;
	display:block;
	margin:0;
	padding:2px;
	height:32px;
	cursor:pointer;
}



</style>
<script type="text/javascript">
//<![CDATA[



function checkLocation () {
	
    if ($('storylocation').value == '') {
        alert('Please enter the location');
        $('storylocation').focus();
    } else {
    
    var url = 'index.php';
	var pars = 'module=news&action=checklocation&location='+$('storylocation').value;
    
    $('locationloading').style.display='block';
	
    var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showLocationResponse} );
    }
}

function ck(location)
{
    $('storylocation').value = location;
    checkLocation();
}

function showLocationResponse (originalRequest) {
	var newData = originalRequest.responseText;
    $('locationloading').style.display='none';
    if (newData != '') {
        $('locationdiv').innerHTML = newData;
        adjustLayout();
    }
}

function submitenter(myfield,e)
{
    var keycode;
    if (window.event) keycode = window.event.keyCode;
    else if (e) keycode = e.which;
    else return true;

    if (keycode == 13)
       {
       checkLocation();
       return false;
       }
    else
       return true;
    }
//]]>
</script>