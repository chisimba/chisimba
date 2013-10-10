<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end of security

/**
 * Class for the Ajax send mail form
 *
 */
class ajaxsendmail extends object {

    /**
     * Constructor
     */
    public function init() {
        // Load Classes Needed to Create the form and iframe
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     * Method to render the form
     * @return string Form
     */

    /**
     * Method to render the form
     * @param string $path
     * @return form
     */
    public function showForm($useremail, $createcheck, $path, $folderid) {
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

        // Create Form
        $form = new form('sendemail_' . $id, $this->uri(array('action' => 'doajaxsendmail')));
        $form->extra = 'enctype="multipart/form-data" target="iframe_sendmail_' . $id . '"';
        $form->id = 'form_sendemail_' . $id;

        // File Input
        $usremail = new textinput("useremail", $useremail);
        $usremail->size = 60;
        $ccreatecheck = new hiddeninput("createcheck", $createcheck);
        $genId = new hiddeninput('id', $id);
        $ppath = new hiddeninput("path", $path);
        $folderid = new hiddeninput("folderid", $folderid);
        // Button
        $button = new button('send', 'Send mail');
        $button->setOnClick('doSendMail(\'' . $id . '\');');

        // Hidden Inputs
        $filename = new hiddeninput('filename', '');
        $emailAdd = "* " . $this->objLanguage->languageText('mod_podcaster_emailadd', 'podcaster', 'Email address') . " :";
        //$buttonNote = $this->objLanguage->languageText('mod_podcaster_clickthreefromemail', 'podcaster', 'Click on the "Next step" button to send the emails and proceed to upload podcast');
        $emailDesc = $this->objLanguage->languageText('mod_podcaster_emailtip', 'podcaster', 'You can type in multiple emails by seperating them with a comma i.e. john@gmail.com,mark@facebook.com');
        
        $form->addToForm($emailAdd . " " . $usremail->show() . ' ' . $button->show());
        $form->addToForm($genId->show() . ' ' . $ppath->show() . ' ' . $ccreatecheck->show() . " " . $folderid->show());
        $form->addToForm("<br /><br />* " . $emailDesc);

        // Append JavaScript
        $this->addJS();

        return $form->show() . '<div id="div_sendmail_' . $id . '" style="display:none;">Sending mail ' . $objIcon->show() . '</div><div id="sendmailresults"></div><div id="sendmailform"></div>' . $objIframe->show();
    }

    /**
     * Method to append JavaScript to the header
     *
     * These are run when the forms are submitted.
     */
    private function addJS() {
        $errormsg = $this->objLanguage->languageText('mod_podcaster_validemailadd', 'podcaster', 'You need to type in a valid email address to proceed');
        $this->appendArrayVar('headerParams', '<script type="text/javascript">
// <![CDATA[

function doSendMail(id)
{
    if (document.forms[\'sendemail_\'+id].useremail.value == \'\') {
        alert(\''.$errormsg.'\');
    } else {
        document.getElementById(\'form_sendemail_\'+id).style.display=\'none\';
        document.getElementById(\'div_sendmail_\'+id).style.display=\'block\';
        document.getElementById(\'sendmailresults\').style.display=\'none\';
        document.forms[\'sendemail_\'+id].submit();
    }
}
// ]]>
</script>');
    }

}

?>