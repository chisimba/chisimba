<?php
$this->loadclass('link','htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('radio', 'htmlelements');

$objIcon= $this->newObject('geticon','htmlelements');

$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/session.css').'"/>';
$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);

$eventcontent=array();

if(!empty ($content)) {
   $eventcontent=$content;
   $mode="edit";
}

$savecontentUrl = $this->uri(array('action'=>'savecontent','eventid'=>$eventid,'mode'=>$mode));
$previewLink = new link($this->uri(array('action'=>'showevent','id'=>$eventid)));
$previewLink->link=$title;
$homeUrl = $this->uri(array('action'=>'eventcontent','id'=>$eventid,'eventtitle'=>$title));
$order   = array("\r\n", "\n", "\r");
$replace ='<br />';

$table=$this->getObject('htmltable','htmlelements');

$instfield = $this->newObject('htmlarea', 'htmlelements');
$instfield->name = 'venuefield';
$instfield->value = $eventcontent['event_timevenue'];

$table->startRow();
$table->addCell('Instructions');
$table->addCell($instfield->show());
$table->endRow();

$contentfield = $this->newObject('htmlarea', 'htmlelements');
$contentfield->name = 'contentfield';
$contentfield->value = $eventcontent['event_content'];

$table->startRow();
$table->addCell('Main content');
$table->addCell($contentfield->show());
$table->endRow();

$lefttitle1field = $this->newObject('htmlarea', 'htmlelements');
$lefttitle1field->name = 'lefttitle1field';
$lefttitle1field->value = $eventcontent['event_lefttitle1'];

$table->startRow();
$table->addCell('Left title 1');
$table->addCell($lefttitle1field->show());
$table->endRow();

$lefttitle2field = $this->newObject('htmlarea', 'htmlelements');
$lefttitle2field->name = 'lefttitle2field';
$lefttitle2field->value = $eventcontent['event_lefttitle2'];

$table->startRow();
$table->addCell('Left title 2');
$table->addCell($lefttitle2field->show());
$table->endRow();

$footerfield = $this->newObject('htmlarea', 'htmlelements');
$footerfield->name = 'footerfield';
$footerfield->value = $eventcontent['event_footer'];

$table->startRow();
$table->addCell('Footer');
$table->addCell($footerfield->show());
$table->endRow();

$objInput = new textinput('emailcontactfield', $eventcontent['event_emailcontact'],null,100);
$table->startRow();
$table->addCell('Event email');
$table->addCell($objInput->show());
$table->endRow();

$objInput = new textinput('emailsubjectfield', $eventcontent['event_emailsubject'],null,'100');
$table->startRow();
$table->addCell('Event email subject');
$table->addCell($objInput->show());
$table->endRow();


$objInput = new textarea('emailcontentfield', $eventcontent['event_emailcontent'],null,'100');
$table->startRow();
$table->addCell('Email Content');
$table->addCell($objInput->show());
$table->endRow();

$objInput = new textinput('emailattachmentfield', $eventcontent['event_emailattachments'],null,'100');
$table->startRow();
$table->addCell('Event attachments');
$table->addCell($objInput->show());
$table->endRow();

//Radio buttons for the option of allowing staff registration
$table->startRow();
$table->addCell('Allow staff registration');
$radio = new radio('staffregfield');
$radio->addOption('true','Yes');
$radio->addOption('false','No');
//Let the 'yes' option be checked by default
$radio->setSelected('true');
$radio->setBreakSpace('&nbsp;');
$table->addCell($radio->show());
$table->endRow();


//Radio buttons for the option of allowing visitor registration
$table->startRow();
$table->addCell('Allow visitor registration');
$radio = new radio('visitorregfield');
$radio->addOption('true','Yes');
$radio->addOption('false','No');
//Let the 'yes' option be checked by default
$radio->setSelected('true');
$radio->setBreakSpace('&nbsp;');
$table->addCell($radio->show());
$table->endRow();


$objForm = new form('submit', $this->uri(array('action'=>'savecontent','eventid'=>$eventid,'mode'=>$mode)));

$mainjs="
Ext.onReady(function(){

var form = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 55,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
        renderTo:'eventcontent',
        url:'".str_replace("amp;", "", $savecontentUrl)."',
        defaultType: 'textfield',
        items: [

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
        fieldLabel: 'Email Content',
        value: '".str_replace($order, $replace,$eventcontent['event_emailcontent'])."',
        width: 900,
        name: 'emailcontentfield'
       }),
     new Ext.form.HtmlEditor({
        fieldLabel: 'Email Attachments',
        value: '".str_replace($order, $replace,$eventcontent['event_emailattachments'])."',
        width: 900,
        name: 'emailattachmentfield'
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
                    text:'Save',
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

$objButton = new button('save', "Save");
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
