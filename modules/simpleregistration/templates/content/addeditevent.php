<?php
$this->loadclass('link','htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('radio', 'htmlelements');

$objIcon= $this->newObject('geticon','htmlelements');

//Required for ExtJS
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/session.css').'"/>';
$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);

//Checking whether to update an existing event or save a new one.
if ($mode == 'edit') {
    $formAction = 'updateevent';
    $buttonText = $this->objLanguage->languageText('mod_simpleregistration_updateevent', 'simpleregistration', 'Update Event');
} else {
    $formAction = 'saveevent';
    $buttonText = $this->objLanguage->languageText('mod_simpleregistration_saveevent', 'simpleregistration', 'Save Event');
    $buttonText = 'save';
	
}


$url = $this->uri(array('action'=>$formAction,'eventid'=>$eventid,'mode'=>$mode));
$previewLink = new link($this->uri(array('action'=>'showevent','id'=>$eventid)));
$previewLink->link=$title;
$homeUrl = $this->uri(array('action'=>'eventcontent','id'=>$eventid,'eventtitle'=>$title));
$order   = array("\r\n", "\n", "\r");
$replace ='<br />';

$table=$this->getObject('htmltable','htmlelements');

$titlefield = new textinput('titlefield', null, null, '20');
$titlefield->size = 50;

if ($mode == 'edit') {
    $titlefield->value = $addevent['event_title'];
}

$table->startRow();
$table->addCell('Event Title');
$table->addCell($titlefield->show());
$table->endRow();

$shorttitlefield = new textinput('shorttitlefield');
$shorttitlefield->size = 50;

if ($mode == 'edit') {
    $shorttitlefield->value = $addevent['short_name'];
}

$table->startRow();
$table->addCell('Short Title');
$table->addCell($shorttitlefield->show());
$table->endRow();

$maxnumberpeople = new textinput('maxpeoplefield');
$maxnumberpeople->size = 10;

if ($mode == 'edit') {
    $maxnumberpeople->value = $addevent['max_people'];
}

$table->startRow();
$table->addCell('Max. Seats');
$table->addCell($maxnumberpeople->show());
$table->endRow();

$datePicker = $this->newObject('datepicker', 'htmlelements');
$datePicker->name = 'eventdate';

if ($mode == 'edit') {
    $datePicker->defaultDate = $addevent['event_date'];
}

$table->startRow();
$table->addCell('Event Date');
$table->addCell($datePicker->show());
$table->endRow();

$instfield = $this->newObject('htmlarea', 'htmlelements');
$instfield->name = 'venuefield';

if ($mode == 'edit') {
    $instfield->value = $eventcontent['event_timevenue'];
}

$table->startRow();
$table->addCell('Instructions');
$table->addCell($instfield->show());
$table->endRow();

$contentfield = $this->newObject('htmlarea', 'htmlelements');
$contentfield->name = 'contentfield';

if ($mode == 'edit') {
    $contentfield->value = $eventcontent['event_content'];
}

$table->startRow();
$table->addCell('Main content');
$table->addCell($contentfield->show());
$table->endRow();

$lefttitle1field = $this->newObject('htmlarea', 'htmlelements');
$lefttitle1field->name = 'lefttitle1field';

if ($mode == 'edit') {
    $lefttitle1field->value = $eventcontent['event_lefttitle1'];
}

$table->startRow();
$table->addCell('Left title 1');
$table->addCell($lefttitle1field->show());
$table->endRow();

$lefttitle2field = $this->newObject('htmlarea', 'htmlelements');
$lefttitle2field->name = 'lefttitle2field';

if ($mode == 'edit') {
    $lefttitle2field->value = $eventcontent['event_lefttitle2'];
}


$table->startRow();
$table->addCell('Left title 2');
$table->addCell($lefttitle2field->show());
$table->endRow();

$footerfield = $this->newObject('htmlarea', 'htmlelements');
$footerfield->name = 'footerfield';

if ($mode == 'edit') {
    $footerfield->value = $eventcontent['event_footer'];
}

$table->startRow();
$table->addCell('Footer');
$table->addCell($footerfield->show());
$table->endRow();

$objInput = new textinput('emailcontactfield');
$objInput->size = 50;

if ($mode == 'edit') {
    $objInput->value = $eventcontent['event_emailcontact'];
}

$table->startRow();
$table->addCell('Event email');
$table->addCell($objInput->show());
$table->endRow();

$objInput = new textinput('emailsubjectfield');
$objInput->size = 50;

if ($mode == 'edit') {
    $objInput->value = $eventcontent['event_emailsubject'];
}

$table->startRow();
$table->addCell('Email subject');
$table->addCell($objInput->show());
$table->endRow();

$objInput = new textinput('emailnamefield');
$objInput->size = 50;

if ($mode == 'edit') {
    $objInput->value = $eventcontent['event_emailname'];
}

$table->startRow();
$table->addCell('Email name');
$table->addCell($objInput->show());
$table->endRow();

$objInput = new textinput('emailcontentfield');
$objInput->size = 50;

if ($mode == 'edit') {
    $objInput->value = $eventcontent['event_emailcontent'];
}

$table->startRow();
$table->addCell('Email content');
$table->addCell($objInput->show());
$table->endRow();


$objInput = new textinput('emailattachmentsfield');
$objInput->size = 50;

if ($mode == 'edit') {
    $objInput->value = $eventcontent['event_emailattachments'];
}

$table->startRow();
$table->addCell('Email attachments');
$table->addCell($objInput->show());
$table->endRow();


//Radio buttons for the option of allowing staff registration
$table->startRow();
$table->addCell('Allow staff registration');
$radiostaffreg = new radio('staffregfield');
$radiostaffreg->addOption('true','Yes');
$radiostaffreg->addOption('false','No');
//Let the 'yes' option be checked by default

if ($mode == 'edit') {
    $radiostaffreg->setSelected($eventcontent['event_staffreg']);
}
$radiostaffreg->setBreakSpace('&nbsp;');
$table->addCell($radiostaffreg->show());
$table->endRow();


//Radio buttons for the option of allowing visitor registration
$table->startRow();
$table->addCell('Allow visitor registration');
$radio = new radio('visitorregfield');
$radio->addOption('true','Yes');
$radio->addOption('false','No');
//Let the 'yes' option be checked by default

if ($mode == 'edit') {
    $radio->setSelected($eventcontent['event_visitorreg']);
}

$radio->setBreakSpace('&nbsp;');
$table->addCell($radio->show());
$table->endRow();


$objForm = new form('submit', $this->uri(array('action'=>$formAction,'eventid'=>$eventid,'mode'=>$mode)));

$mainjs="
Ext.onReady(function(){

var form = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 55,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
        renderTo:'eventcontent',
        url:'".str_replace("amp;", "", $url)."',
        defaultType: 'textfield',
        items: [
	
	 new Ext.form.HtmlEditor({
        fieldLabel: 'Event Title',
        value: '".str_replace($order, $replace, $addevent['event_title'])."',
        width: 900,
        height: 200,
        name: 'titlefield'
        }),

	 new Ext.form.HtmlEditor({
        fieldLabel: 'shorttitlefield',
        value: '".str_replace($order, $replace, $addevent['short_name'])."',
        width: 900,
        height: 200,
        name: 'Short Title'
        }),

	 new Ext.form.HtmlEditor({
        fieldLabel: 'maxpeoplefield',
        value: '".str_replace($order, $replace, $addevent['max_people'])."',
        width: 900,
        height: 200,
        name: 'Max Number of People'
        }),

	 new Ext.form.HtmlEditor({
        fieldLabel: 'Event Date',
        value: '".str_replace($order, $replace, $event['event_date'])."',
        width: 900,
        height: 200,
        name: 'eventdate'
        }),

        new Ext.form.HtmlEditor({
        fieldLabel: 'Registration instructions',
        value: '".str_replace($order, $replace, $eventcontent['event_timevenue'])."',
        width: 900,
        height: 200,
        name: 'venuefield'
        }),

        new Ext.form.HtmlEditor({
        fieldLabel: 'Main Content',
        value: '".str_replace($order, $replace, $eventcontent['event_content'])."',
        width: 900,
        height: 300,
        name: 'contentfield'
       }),

       new Ext.form.HtmlEditor({
        fieldLabel: 'Left Title1',
        width: 900,
        value: '".str_replace($order, $replace,$eventcontent['event_lefttitle1'])."',
        name: 'lefttitle1field'
       }),

        new Ext.form.HtmlEditor({
        fieldLabel: 'Left Title2',
        value: '".str_replace($order, $replace,$eventcontent['event_lefttitle2'])."',
        width: 900,
        name: 'lefttitle2field'
       }),
        new Ext.form.HtmlEditor({
        fieldLabel: 'Footer',
        value: '".str_replace($order, $replace,$eventcontent['event_footer'])."',
        width: 900,
        name: 'footerfield'
       }),
        new Ext.form.HtmlEditor({
        fieldLabel: 'Email Contact',
        value: '".str_replace($order, $replace,$eventcontent['event_emailcontact'])."',
        width: 900,
        name: 'emailcontactfield'
       }),
        new Ext.form.HtmlEditor({
        fieldLabel: 'Email Subject',
        value: '".str_replace($order, $replace,$eventcontent['event_emailsubject'])."',
        width: 900,
        name: 'emailsubjectfield'
       }),
	new Ext.form.HtmlEditor({
        fieldLabel: 'Email Name',
        value: '".str_replace($order, $replace,$eventcontent['event_emailname'])."',
        width: 900,
        name: 'emailnamefield'
       }),
        new Ext.form.HtmlEditor({
        fieldLabel: 'Email Name',
        value: '".str_replace($order, $replace,$eventcontent['event_content'])."',
        width: 900,
        name: 'emailcontentfield'
       }),
     new Ext.form.HtmlEditor({
        fieldLabel: 'Email Attachments',
        value: '".str_replace($order, $replace,$eventcontent['event_emailattachments'])."',
        width: 900,
        name: 'emailattachmentsfield'
       }),
   new Ext.form.TextField({
        fieldLabel: 'Show Staff Registration',
        value: '".str_replace($order, $replace,$eventcontent['event_staffreg'])."',
        width: 100,
        name: 'staffregfield'
       }),
   new Ext.form.TextField({
        fieldLabel: 'Show Visitor Registration',
        value: '".str_replace($order, $replace,$eventcontent['event_visitorreg'])."',
        width: 100,
        name: 'visitorregfield'
       })
],
                  buttons: [{
                    text:'.$buttonText.',
                    handler: function(){
                      if (form.url){
                      form.getForm().getEl().dom.action = form.url;
                       }
                     form.getForm().submit();
                   }
                   }
                  ,{
                    text: 'Cancel',
                    handler: function(){
                      window.location.href = '".str_replace("amp;", "",$homeUrl)."';
                    }
                  }
                ]
});
  });
";

$content=$table->show();

$objForm->addToForm($previewLink->show());
$objForm->addToForm($table->show());

$objButton = new button('save', $buttonText);
$objButton->setToSubmit();
$objForm->addToForm($objButton->show());

$content.= '<div id="eventcontent"><h1>'.$previewLink->show().'</h1><br /><br /></div>';
$content.= "<script type=\"text/javascript\">".$mainjs."</script>";



// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$rightSideColumn =$objForm->show();
$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent( $postLoginMenu->show());

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();
?>
