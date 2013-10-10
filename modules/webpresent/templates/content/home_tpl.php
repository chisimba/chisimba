<script type="text/javascript">
//<![CDATA[

function getData(type, period) {
    var url = 'index.php';
    var pars = 'module=webpresent&action=ajaxgetstats&type='+type+'&period='+period;
    var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onLoading: showLoading(type), onComplete: function(ajaxResponse)
{showResponse(type, ajaxResponse);}} );
}

function showLoading (type) {
    $('loading_'+type).style.display='block';
}

function showResponse (type, ajaxResponse) {
    var newData = ajaxResponse.responseText;
    $('loading_'+type).style.display='none';
    //$('data_'+type).style.display='none';
    //Effect.Fade('data_'+type);
    $('data_'+type).innerHTML = newData;
    //Effect.Appear('data_'+type);
}
//]]>
</script>
<br />
<?php
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();

//--------- ADDED BY DEREK FOR EMAIL
// Add the tag cloud to the left contents.

$form = new form ('searchform', $this->uri(array('action'=>'search')));
$form->method = 'GET';

$module = new hiddeninput('module', 'webpresent');
$form->addToForm($module->show());

$action = new hiddeninput('action', 'search');
$form->addToForm($action->show());

$textinput = new textinput ('q');
$textinput->size = 60;
$button = new button ('search', 'Search');
$button->setToSubmit();

$form->addToForm('<div align="center">'.$textinput->show().' '.$button->show().'</div>');

// Turn off so long


$leftContents = $form->show();
// Make a tabbed box
$objTabs = $this->newObject('tabcontent', 'htmlelements');
$objTabs->width = '95%';

$tagCloudLink = new link ($this->uri(array('action'=>'tagcloud')));
$tagCloudLink->link = 'View All Tags';

$tagCloudContent = '<span style="text-align:center">' . $tagCloud . '</span><br />'.$tagCloudLink->show();

// Add the tag cloud to the tabbed box
$objTabs->addTab('Latest Tags', $tagCloudContent);

if ($this->objUser->isLoggedIn()) {

    // Counter for additional modules that may be registered
    $moduleCounter = 0;

    $objModule = $this->getObject('modules','modulecatalogue');
    //See if the youtube API module is registered and set a param
    $emailRegistered = $objModule->checkIfRegistered('email', 'email');
    if ($emailRegistered) {
        $moduleCounter++;
        //Add the email messages to the tabbed box
        $msgs = $this->getObject("messagestpl", "webpresent");
        $msgList = $msgs->show();
        $msgTitle = $this->objLanguage->languageText("mod_webpresent_msgs", "webpresent")
          .  $msgs->msgCount;
        $objTabs->addTab($msgTitle, $msgList);
    }

    $objModule = $this->getObject('modules','modulecatalogue');
    //See if the youtube API module is registered and set a param
    $buddiesRegistered = $objModule->checkIfRegistered('buddies', 'buddies');
    if ($buddiesRegistered) {
        $moduleCounter++;
        //Add the email messages to the tabbed box
        $buds = $this->getObject("buddiestpl", "webpresent");
        $budList = $buds->show();
        // Add buddies to the tabbed box
        $objTabs->addTab($this->objLanguage->languageText("mod_webpresent_buddieson", "webpresent")  .  $buds->budCount, $budList);
    }

    // If no additional modules are registered, only show tag cloud
    if ($moduleCounter == 0)
    {
        $leftContents .= "<span style=\"text-align:center\">" . $tagCloud . "</span>";

    } else { // Else show items in multi tab box
        $leftContents .= $objTabs->show();
    }
//----------- END ADDED BY DEREK FOR EMAIL & Buddies
} else {
    $leftContents .= $tagCloudContent.'<br />';
    $buddiesRegistered = FALSE;
}

$objDownloadCounter = $this->getObject('dbwebpresentdownloadcounter');
$downloadTable = $objDownloadCounter->getMostDownloadedTable();

$objViewCounter = $this->getObject('dbwebpresentviewcounter');
$viewTable = $objViewCounter->getMostViewedTable();

$objTagViewCounter = $this->getObject('dbwebpresenttagviewcounter');
$tagViewTable = $objTagViewCounter->getMostViewedTagTable();

$objUploadsCounter = $this->getObject('dbwebpresentuploadscounter');
$uploadsTable = $objUploadsCounter->getMostUploadedTable();

$statsTable = $this->newObject('htmltable', 'htmlelements');
$statsTable->startRow();
$statsTable->addCell($viewTable.$tagViewTable, '49%');
$statsTable->addCell('&nbsp;', '2%');
$statsTable->addCell($downloadTable.$uploadsTable, '49%');
$statsTable->endRow();

$leftContents .= '<br />'.$statsTable->show();

//$objLatestBlogs = $this->getObject('block_lastten'

$table->addCell($leftContents, '60%', 'top', 'left');
$table->addCell('&nbsp;&nbsp;&nbsp;', '3%');


if (count($latestFiles) == 0) {
    $latestFilesContent = '';
} else {
    $latestFilesContent = '';

    $objTrim = $this->getObject('trimstr', 'strings');

    $counter = 0;

    foreach ($latestFiles as $file)
    {
        $counter++;

        if (trim($file['title']) == '') {
            $filename = $file['filename'];
        } else {
            $filename = htmlentities($file['title']);
        }

        $linkname = $objTrim->strTrim($filename, 45);

        $fileLink = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
        $fileLink->link = $this->objFiles->getPresentationThumbnail($file['id']).'<br />'.$linkname;
        $fileLink->title = $filename;

        $extra = ($counter % 2 == 1) ? ' clear:both;' : '';

        $latestFilesContent .= '<div style="float: left; width: 160px; overflow: hidden; margin-right: 10px; padding-bottom: 10px;'.$extra.'">'.$fileLink->show().'</div>';
    }

    $objIcon->setIcon('rss');
    $rssLink = new link ($this->uri(array('action'=>'latestrssfeed')));
    $rssLink->link = $objIcon->show();

    $latestFilesContent .= '<br clear="left" />'.$rssLink->show();

}



$objTabs = $this->newObject('tabcontent', 'htmlelements');
$objTabs->addTab('Latest Uploads', '<h3>10 Newest Uploads:</h3>'.$latestFilesContent);
//$objTabs->addTab('Live Sessions', $applet);
$tabCounter = 0;

if ($objUser->isLoggedIn() && $buddiesRegistered) {
    $buddiesFiles = $this->objFiles->getLatestByBuddies($objUser->userId());

    //print_r($buddiesFiles);

    if (count($buddiesFiles) == 0) {
        $buddiesFilesContent = '';
    } else {
        $buddiesFilesContent = '';

        $objTrim = $this->getObject('trimstr', 'strings');

        $counter = 0;

        foreach ($buddiesFiles as $file)
        {
            $counter++;

            if (trim($file['title']) == '') {
                $filename = $file['filename'];
            } else {
                $filename = htmlentities($file['title']);
            }

            $linkname = $objTrim->strTrim($filename, 45);

            $fileLink = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
            $fileLink->link = $this->objFiles->getPresentationThumbnail($file['id']).'<br />'.$linkname;
            $fileLink->title = $filename;

            $extra = ($counter % 2 == 1) ? ' clear:both;' : '';

            $buddiesFilesContent .= '<div style="float: left; width: 160px; overflow: hidden; margin-right: 10px; padding-bottom: 10px;'.$extra.'">'.$fileLink->show().'</div>';
        }
        $objTabs->addTab('Latest by Buddies', '<h3>Latest by Buddies:</h3>'.$buddiesFilesContent);
        $tabCounter++;
    }
}


$objModules = $this->getObject('modules', 'modulecatalogue');
if ($objModules->checkIfRegistered('blog')) {


    $objBlocks = $this->getObject('blocks', 'blocks');
    $blogBlock = $objBlocks->showBlock('lastten', 'blog', NULL, 20, TRUE, FALSE);


    $objTabs->addTab('Latest Blog Posts', $blogBlock);
    $tabCounter++;


}


if ($tabCounter > 0) {
    $table->addCell($objTabs->show(), '37%');
} else {
    $table->addCell('<h3>10 Newest Uploads:</h3>'.$latestFilesContent, '37%');
}



$table->endRow();

echo $table->show();


$uploadbutton = new button ('upload', 'Upload');
$uploadbutton->setOnClick('document.location=\''.$this->uri(array('action'=>'upload')).'\'');

//echo $uploadbutton->show();

$uploadLink = new link ($this->uri(array('action'=>'upload')));
$uploadLink->link = 'Upload Presentation';

echo '<p>'.$uploadLink->show().'</p>';
?>