<?php
$this->setVar('pageSuppressXML', TRUE);

$this->loadClass('iframe', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_podcaster_emailrssnote', 'podcaster', 'Email RSS notification');
$header->type = 2;

echo $header->show();

$objAjaxSendMail = $this->newObject('ajaxsendmail');

echo $objAjaxSendMail->showForm($useremail,$createcheck,$path,$folderid);

/*
$this->setVar('pageSuppressXML', TRUE);
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass("textinput", "htmlelements");
$this->loadClass("hiddeninput", "htmlelements");

$errormsg = $this->objLanguage->languageText('mod_podcaster_validemailadd', 'podcaster', 'You need to type in a valid email address to proceed');

$this->appendArrayVar('headerParams', '<script type="text/javascript">
// <![CDATA[

function sendMail(id)
{
    if (document.forms[\'sendemails_\'+id].useremail.value == \'\') {
        alert(\''.$errormsg.'\');
    } else {    
        document.getElementById(\'form_sendmail_\'+id).style.display=\'none\';
        document.getElementById(\'div_email_\'+id).style.display=\'block\';
        document.getElementById(\'sendmailresults\').style.display=\'none\';
        document.forms[\'sendemails_\'+id].submit();
    }
}

function changeEmailAddress(id)
{
    document.forms[\'sendemails_\'+id].filename.value = document.forms[\'sendmailconfirm_\'+id].useremail.value;

    var tr = document.forms[\'sendemails_\'+id].useremail.value;
    len = tr.length;
    rs = 0;
    for (i = len; i > 0; i--) {
        vb = tr.substring(i,i+1)
        if (vb == "/" && rs == 0) {
            document.forms[\'sendemails_\'+id].filename.value = tr.substring(i+1,len);
            rs = 1;
        }
    }
}
// ]]>
</script>');

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_podcaster_emailrssnote', 'podcaster', 'Email RSS notification');
$header->type = 1;

echo $header->show();

// Generate an ID - In case multiple uploads occur on one page
$id = mktime() . rand();

// Generate Iframe
$objIframe = $this->newObject('iframe', 'htmlelements');

$objIframe->src = $this->uri(array('action' => 'tempiframe', 'id' => $id));
$objIframe->id = 'ifra_sendmail_' . $id;
$objIframe->name = 'iframe_sendmail_' . $id;
$objIframe->frameborder = 1;
$objIframe->width = 600;
$objIframe->height = 400;
$objIframe->extra = ' style="display:none" ';

// Create Loading Icon - Hidden by Default
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loading_bar');

$form = new form ('sendemails_'.$id, $this->uri(array('action'=>'doajaxupload')));
$form->extra = 'enctype="multipart/form-data" target="iframe_sendmail_'.$id.'"';
$form->id = 'form_sendmail_'.$id;

//Title
$usremail = new textinput("useremail", $useremail);
$usremail->size = 60;

$ffolderid = new hiddeninput("folderid", $folderid);
$ccreatecheck = new hiddeninput("createcheck", $createcheck);
$genId = new hiddeninput('id', $id);
$ppath = new hiddeninput("path", $path);

$buttonLabel = $this->objLanguage->languageText('word_next', 'system', 'System') . " " . $this->objLanguage->languageText('mod_podcaster_wordstep', 'podcaster', 'Step');
$buttonNote = $this->objLanguage->languageText('mod_podcaster_clickthreefromemail', 'podcaster', 'Click on the "Next step" button to send the emails and proceed to upload podcast');
$emailDesc = $this->objLanguage->languageText('mod_podcaster_emailtip', 'podcaster', 'You can type in multiple emails by seperating them with a comma i.e. john@gmail.com,mark@facebook.com');

//Save button
$button = new button("submit", $buttonLabel);
$button->setOnClick('sendMail(\'' . $id . '\');');
//$button->setToSubmit();
$emailAdd = "* " . $this->objLanguage->languageText('mod_podcaster_emailadd', 'podcaster', 'Email address') . " :";

$objTable = new htmltable();
$objTable->width = '100%';
$objTable->attributes = " align='left' border='1'";
$objTable->cellspacing = '5';
$objTable->startRow();
$objTable->addCell($emailAdd, 100, 'top', 'right');
$objTable->addCell($usremail->show() . $ffolderid->show() . $ccreatecheck->show() . $ppath->show() . $genId->show(), Null, 'top', 'left');
$objTable->addCell("**" . $button->show(), Null, 'top', 'right');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("* " . $emailDesc, Null, 'top', 'left', '', 'colspan="3"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("** " . $buttonNote, Null, 'top', 'left', '', 'colspan="3"');
$objTable->endRow();
$form->addToForm($objTable->show());

$form->addToForm($emailAdd." ".$usremail->show() . " **" . $button->show());
$form->addToForm("<br />".$ffolderid->show() . $ccreatecheck->show() . $ppath->show() . $genId->show());
$form->addToForm("<br />* " . $emailDesc."<br /> **".$buttonNote);

echo $form->show() . '<div id="div_email_' . $id . '" style="display:none;"> Sending mail ' . $objIcon->show() . '</div><div id="sendmailresults"></div><div id="updateform"></div>' . $objIframe->show();

 */
?>