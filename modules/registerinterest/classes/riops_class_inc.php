<?php

/**
 *
 * Ops Class for Register interest
 *
 * Ops Class for Register interest - builds various user interface elements
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   registerinterest
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Ops Class for Register interest
 *
 * Ops Class for Register interest - builds various user interface elements
 *
 * @package   registerinterest
 * @author    Derek Keats derek@dkeats.com
 *
 */
class riops extends object {

    /**
     *
     * Intialiser for the registerinterest ops class
     * @access public
     * @return VOID
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objDB = $this->getObject('dbregisterinterest', 'registerinterest');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objThumbnails = $this->getObject('thumbnails', 'filemanager');
        $this->objDBfile = $this->getObject('dbfile', 'filemanager');
        //the DOM document
        $this->domDoc = new DOMDocument('UTF-8');
        // Load all the required HTML classes from HTMLElements module
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
    }

    function showHiddenDiv() {
        //the admin form
        $adminForm = new form('frmRegisterList', $this->uri(array('action' => 'save'), 'registerinterest'));
        //building the admin form
        $saveButton = new button('btnSave', 'Save');
        $saveButton->setId('btnSaveInterest');
        //input
        $txtValue = new textinput('txtValue');
        //the hidden input
        $objHidden = new hiddeninput(' ', ' ');
        if ($this->objUser->isAdmin()) {
            $heading = new htmlheading($this->objLanguage->languageText('mod_registerinterest_enterinterest','registerinterest'), 4);
        } else {
            $heading = new htmlheading($this->objLanguage->languageText('mod_registerinterest_selectinterest','registerinterest'), 4);
        }
        //the heading ID
        $heading->id = 'indicatorHeader';
        //add elements to the form
        $adminForm->addToForm($objHidden->show());
        $adminForm->addToForm($heading->show());
        //the div element class
        $divClass = '';
        if ($this->objUser->isAdmin()) {
            $divClass = "registerlistAdmin";
        } else {
            $divClass = "registerlistHidable";
        }
        //change table
        $this->objDB->_tableName = 'tbl_registerinterest';
        //the available values
        $list = $this->objDB->getAll();
        //set the tablename to the correct table
        $hidableDiv = "<div class='{$divClass}' id='registerlistHidable' ><p>";
        //table for making the display OK
        $listTable = $this->getObject('htmltable', 'htmlelements');
        //the delete link, for admin
        $delLink = $this->getObject('confirm', 'utilities');
        //the icon object
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $index = 0;
        //display the available list of interests
        foreach ($list as $value) {
            //create label for value name
            $checkbox = new checkbox(str_replace(' ', '_', 'interest' . $index), ucfirst($value['name']));
            $checkbox->setLabel($value['name']);
            $checkbox->setCss('hidable');
            $checkbox->setValue($value['id']);
            $objIcon->getDeleteIcon($this->uri(array('action' => 'remove', 'id' => $value['id'])));
            $delLink->setConfirm("", $this->uri(array('action' => 'remove', 'id' => $value['id'], 'table' => 2)), $this->objLanguage->languageText('phrase_confirm'));
            $delLink->link = $objIcon->show();
            $listTable->startRow();
            $listTable->addCell($checkbox->show() . '</p>');
            if ($this->objUser->isAdmin()) {
                $listTable->addCell($delLink->show());
            }
            $listTable->endRow();
            $index++;
        }
        $adminForm->addToForm($listTable->show());
        //add the text input for adding values to the database only if user is admin
        if ($this->objUser->isAdmin()) {
            $adminForm->addToForm($txtValue->show());
            $adminForm->addToForm('<br />');
            $adminForm->addToForm($saveButton->show());
        }
        //if ($this->objUser->isAdmin()) {
        $hidableDiv .= $adminForm->show() . "</div>";
        //};
        return $hidableDiv;
    }

    /**
     *
     * Build the form
     *
     * @return string The form
     * @access public
     *
     */
    public function buildForm() {
        // Load the javascript.
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('registerinterest.js', 'registerinterest'));

        // Create the form and set the action to save
        $formAction = $this->uri(array('action' => 'save'), 'registerinterest');
        $myForm = new form('edituser', $formAction);

        $fnLabel = $this->objLanguage->languageText('mod_registerinterest_fullname', 'registerinterest');
        $fullName = new textinput('fullname');
        $fn = $fnLabel . "<br />" . $fullName->show();

        $emLabel = $this->objLanguage->languageText('mod_registerinterest_email', 'registerinterest');
        $emCheck = $this->objLanguage->languageText('mod_registerinterest_confirm', 'registerinterest');
        $eMailAddr = new textinput('email');
        $em = $emLabel . "<br />" . $eMailAddr->show() . "<br />" . $emCheck;

        $buttonTitle = $this->objLanguage->languageText('word_save');
        $button = new button('saveDetails', $buttonTitle);
        $button->cssId = 'ri_save_button';
        $button->setToSubmit();

        $myForm->addToForm($fn . "<br />");
        $myForm->addToForm($em . "<br />");
        $myForm->addToForm($button->show());
        //the return string
        $ret = "<div class='registerinterest_form' id = 'ri_form'><div id='before_riform'></div>" . $myForm->show() . "<br />";
        $ret .= $this->showHiddenDiv();
        $ret .= "</div>";
        $this->objDB->_tableName = 'tbl_registerinterest';
        if ($this->objUser->isAdmin()) {
            return $ret;
        }
        if (count($this->objDB->getAll()) == 0 && $this->objUser->isAdmin() != TRUE) {
            return "<span >{$this->objLanguage->languageText('mod_registerinterest_nointerests','registerinterest','No interests are available to register for')}</span>";
        } elseif (count($this->objDB->getAll()) > 0) {
            return $ret;
        }
    }

    /**
     * 
     * Build form for sending message to registered people.
     * 
     * @return string The form
     * @access public
     * 
     */
    public function buildMsgForm() {
        // Load all the required HTML classes from HTMLElements module
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');

        // Load the javascript.
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('registerinterest.js', 'registerinterest'));

        // Create the form and set the action to save
        $formAction = $this->uri(array('action' => 'sendmessage'), 'registerinterest');
        $myForm = new form('editmsg', $formAction);

        $dropDown = new dropdown();
        /* foreach($this->objDB->getAll() as $data){
          //the table
          $chekbox = new checkbox('chk'.$data['email'],$data['fullname'],TRUE);
          $dropDown->addOption('To'.'<br />');
          $dropDown->addOption('Cc'.'<br />');
          $dropDown->addOption('Bcc'.'<br />');
          $myForm->addToForm($dropDown->show());
          } */
        $subjectLabel = $this->objLanguage->languageText('mod_registerinterest_msgsubject', 'registerinterest');
        $subject = new textinput('subject');
        $sj = $subjectLabel . "<br />" . $subject->show();

        $msgLabel = $this->objLanguage->languageText('mod_registerinterest_msglabel', 'registerinterest');
        //$msg = new textarea('message');
        $msg = $this->getObject('htmlarea', 'htmlelements');
        $msg->setName('emailmessage');
        $ms = $msgLabel . "<br />" . $msg->show();

        $buttonTitle = $this->objLanguage->languageText('word_send');
        $button = new button('saveDetails', $buttonTitle);
        $button->cssId = 'ri_savemsg_button';
        $button->setToSubmit();

        $myForm->addToForm($sj . "<br />");
        $myForm->addToForm($ms . "<br />");
        $myForm->addToForm($button->show());

        return "<div class='registerinterest_form' id = 'ri_form'><div id='before_riform'></div>" . $myForm->show() . "</div>";
    }

    /**
     * 
     * Render an HTML table of all registered people
     * 
     * @return string The rendered HTML table
     */
    public function listAll() {
        //set the correct table
        $this->objDB->_tableName = 'tbl_registerinterest_interested';
        if ($this->objUser->isAdmin()) {
            $dataArray = $this->objDB->getAll();
            //if there are no values, return text indicating as such
            if (count($dataArray) == 0) {
                return $this->objLanguage->languageText(" mod_registerinterest_noentries", "registerinterest", "There are no names on the list ");
            } else {
                $domElements['table'] = $this->domDoc->createElement('table');
                $domElements['table']->setAttribute('class', 'ri_viewall');
                // The link for the message writer.
                $sendlink = $this->uri(array('action' => 'writemessage'), 'registerinterest');
                $sendlink = str_replace("&amp;", "&", $sendlink);
                $div = $this->domDoc->createElement('div');
                $div->setAttribute('class', 'ri_msgpopper');
                $h3 = $this->domDoc->createElement('h3');
                $a = $this->domDoc->createElement('a');
                $a->setAttribute('class', 'ri_msglink');
                $a->setAttribute('href', $sendlink);
                $a->appendChild($this->domDoc->createTextNode($this->objLanguage->languageText('phrase_sendmessage', 'system')));
                $h3->appendChild($a);
                $domElements['tr'] = $this->domDoc->createElement('tr');
                $domElements['td'] = $this->domDoc->createElement('td');
                $domElements['td']->appendChild($h3);
                $domElements['tr']->appendChild($domElements['td']);
                $domElements['table']->appendChild($domElements['tr']);
                // Create the header row.
                $domElements['tr'] = $this->domDoc->createElement('tr');
                $domElements['th'] = $this->domDoc->createElement('td');
                $domElements['th']->appendChild($this->domDoc->createTextNode("Name"));
                $domElements['tr']->appendChild($domElements['th']);
                $domElements['th'] = $this->domDoc->createElement('td');
                $domElements['th']->appendChild($this->domDoc->createTextNode("Email"));
                $domElements['tr']->appendChild($domElements['th']);
                $domElements['th'] = $this->domDoc->createElement('td');
                $domElements['th']->appendChild($this->domDoc->createTextNode("Registered"));
                $domElements['tr']->appendChild($domElements['th']);
                // Add the row to the table.
                $domElements['table']->appendChild($domElements['tr']);

                $class = "odd";
                foreach ($dataArray as $usr) {
                    //the icon object
                    $objIcon = $this->getObject('geticon', 'htmlelements');
                    // Reatrieve the data.
                    $id = $usr['id'];
                    $fullName = $usr['fullname'];
                    $emailAddress = $usr['email'];
                    $dateCreated = $usr['datecreated'];
                    $objIcon->getDeleteIconWithConfirm('delete', array('action' => 'remove', 'id' => $id), NULL, NULL);
                    // Create the table row.
                    $domElements['tr'] = $this->domDoc->createElement('tr');
                    //label
                    $domElements['label'] = $this->domDoc->createElement('label');
                    // Fullname to table.
                    $domElements['td'] = $this->domDoc->createElement('td');
                    $domElements['tr']->setAttribute('class', $class);
                    $domElements['td']->appendChild($this->domDoc->createTextNode($fullName));
                    $domElements['tr']->appendChild($domElements['td']);
                    // Email address to table.
                    $domElements['td'] = $this->domDoc->createElement('td');
                    //check if user is admin so to display the correct control
                    $domElements['td']->setAttribute('id', $id);
                    $domElements['label']->setAttribute('value', $emailAddress);
                    $domElements['td']->setAttribute('class', 'interestEmail');
                    $domElements['label']->appendChild($this->domDoc->createTextNode($emailAddress));
                    $domElements['label']->setAttribute('for', 'interestEmail');
                    $domElements['label']->setAttribute('id', $id);
                    $domElements['td']->appendChild($domElements['label']);
                    $domElements['tr']->appendChild($domElements['td']);
                    // Date registered to table.
                    $domElements['td'] = $this->domDoc->createElement('td');
                    $domElements['td']->appendChild($this->domDoc->createTextNode($dateCreated));
                    $domElements['tr']->appendChild($domElements['td']);

                    // Add the row to the table.
                    $domElements['table']->appendChild($domElements['tr']);

                    //if user is admin, add the delete link
                    //create the delete link
                    $domElements['rmLink'] = $this->domDoc->createElement('a');
                    //create the confirmation object which to retrieve the javascript confirmation message from
                    $confirmLink = $this->getObject('confirm', 'utilities');
                    $confirmLink->setConfirm(NULL, str_replace('amp;', '', $this->uri(array('action' => 'remove', 'id' => $id))), $this->objLanguage->languageText('mod_registerinterest_removealert', 'registerinterest'));
                    //td element for the remove link
                    $domElements['td'] = $this->domDoc->createElement('td');
                    //create the delete icon
                    $domElements['delIcon'] = $this->domDoc->createElement('image');
                    $domElements['delIcon']->setAttribute('id', 'deleteIcon');
                    $domElements['delIcon']->setAttribute('src', $objIcon->getSrc());
                    //add link text
                    //set the href attribute of the link
                    $domElements['rmLink']->setAttribute('href', $confirmLink->href);
                    //$domElements['rmLink']->appendChild($domElements['delIcon']);
                    $domElements['rmLink']->appendChild($domElements['delIcon']);
                    $domElements['td']->appendChild($domElements['rmLink']);
                    $domElements['tr']->appendChild($domElements['td']);
                    $domElements['td']->appendChild($this->domDoc->createElement('br'));
                    $domElements['td']->appendChild($this->domDoc->createElement('br'));
                    $domElements['table']->appendChild($domElements['tr']);

                    // Convoluted odd/even.
                    if ($class == "odd") {
                        $class = "even";
                    } else {
                        $class = "odd";
                    }
                }
                //update form
                $this->domDoc->appendChild($domElements['table']);
                return $this->domDoc->saveHTML();
            }
        }
    }

    /**
     * The function which carries out the message sending
     * 
     * @param string $subject The message subject
     * @param string $message The message content
     * @param string $userId The userID of the sender
     * @return boolean TRUE on success|else FALSE
     */
    public function sendMessage($subject = NULL, &$message, $userId) {
        $domDoc = new DOMDocument('utf-8');
        if (!empty($message)) {
            $templateLocation = $this->objAltConfig->getSiteRoot() . $this->objAltConfig->getContentPath() . 'users/' . $userId . '/';
            $messageParagraph = $domDoc->createElement('p');
            //$messageParagraph->appendChild($domDoc->createTextNode($message));
            $domDoc->loadHTML($message);
            //get the message paragraph tag
            //create opt-out link
            //$optOutLink = $this->getObject('link', 'htmlelements');
            //$optOutLink->link = "Click here.";
            $objMail = & $this->getObject('mailer', 'mail');
            //setting fromName
            $fromName = $this->objUser->fullname($userId);
            //setting the email address using the servername and the username   
            $from = $this->objUser->userName() . '@' . $_SERVER['SERVER_NAME'];
            //setting the subject
            if (empty($subject)) {
                $subject = $this->objLanguage->languageText('phrase_nosubject', 'system');
            }
            //load the message
            //$file = file_get_contents($this->objAltConfig->getSiteRoot() . '/' . $this->objAltConfig->getContentPath() . 'users/' . $userId . '/mailtemplate.html');
            //$template = file_get_contents($templateLocation . 'mailtemplate.html');
            //$this->domDoc->loadHTML($message);
            //get the image tags/elements
            //$img = $this->domDoc->getElementsByTagName('img');
            //$messageParagraph = $this->domDoc->getElementById('messageDevider');
            //check for the thumbnais folder only when an image is uploaded
            //if ($img->length >= 1) {
                //check if the thumbnail folder exists, if not create one
              //  $this->objThumbnails->checkThumbnailFolder();
                //get user files
              //  $usrFiles = $this->objDBfile->getUserFiles($userId);
                //loop through the the image tags
                //foreach ($img as $image) {
                    //loop through the files
                   // foreach ($usrFiles as $file) {
                        //get the file path
                      //  $address = $image->getAttribute('src');
                        //if the file exists, get the thumbnail
                       // if ($file['path'] == str_replace($this->objAltConfig->getSiteRoot() . '/' . $this->objAltConfig->getContentPath(), '', $address)) {
                          //  $fileInfo = $this->objDBfile->getFileDetailsFromPath($file['path']);
                            //$thumbnail = $this->objThumbnails->getThumbnail($fileInfo['id'], NULL, NULL, 'medium');
                            //$image->setAttribute('src', $this->objAltConfig->getSiteRoot() . $thumbnail);
                            //$image->removeAttribute('style');
                        //}
                    //}
                //}
            //}
            /**
             * @TODO: replace me as soon as you find a cleaner way  of doing this
             */
            //$this->domDoc->saveHTML();
            //create a temporary document to store the emai message template
            //$tempDoc = new DOMDocument('utf-8');
            //load the e-mail  template to the temporary doc
            //$tempDoc->loadHTML($template);
            //get the banner image logo
            //$bannerLogo = $tempDoc->getElementById('bannerimage');
            //set the src property to the image in the user directory
           // $bannerLogo->setAttribute('src', $templateLocation . 'banner-logo.png');
            //save the HTM L in the temporary DOMDocument
           // $tempDoc->saveHTML();
            //the message Div to contain the message
            //$messageDevider = $tempDoc->getElementById('messageDevider');
            //value to be used within the repetetive structure
            $index = 0;
            //foreach ($this->domDoc->getElementsByTagName('p') as $value) {
                //get the element's child by index
           //     $importedValue = $this->domDoc->getElementsByTagName('p')->item($index);
                //import the child into the message paragraph tag
           //     $messageParagraph = $tempDoc->importNode($importedValue, TRUE);
                //append the message paragraph into the message devider
           //     $messageDevider->appendChild($messageParagraph);
                //increase the index value by one, for the next child node
           //     $index++;
            }
            //s$tempDoc->saveHTML();
            $optOutLink = $this->getObject('link', 'htmlelements');
            $label = $this->getObject("label","htmlelements");
            $label->setLabel($this->objLanguage->languageText("mod_registerinterest_optoutconfirm","registerinterest"));
            //loop through the available email addresses
            foreach ($this->objDB->getAll() as $data) {
                /**
                 * @TODO: change the link to developer site
                 */
                //set href attribute to opt-out the user using the ID
                $optOutLink->href = $this->uri(array('action'=>'optout','id'=>$data['id']), "registerinterest");
                $optOutLink->link = $this->objLanguage->languageText("phrase_optoutlink","system");
                //$message = $tempDoc->saveHTML();
                $message = $message;
                //get the message header
                $header = $this->objLanguage->languageText('mod_registerinterest_messageheader','registerinterest');
                //replace placeholder values with actual values
                $header = str_replace('{username}', $data['fullname'], $header);
                //the html message
                $htmlMessage = "\n\r
                    <table style='width: 100%;'>
                    <thead >
                    <tr  >
                    <td style='text-align: center' >
                    <img src='http://thumbzup.com/img/logo.png' />
                    </td>
                    </tr>
                    <tr style='background: #2C3173;' >
                    <td ><br /></td>
                    </tr>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td>
                    <font size='2' >
                {$header}, \n\r<br /><br />
                    {$message}
                    </font>
                    </td>
                    </tr>
                    <tr style='background: #e94c15;' >
                    <td >
                    <br />
                    </td>
                    </tr>
                    <tr > \n\r <br />
                    <font size='1' >
                    ".$label->show()." \n\r <br />
                    ".$optOutLink->show()."    
                    </font>
                    </tr>
                    </tbody>
                    </table>";
                //set the HTML disabled message
                $plainMessage = strip_tags($message);
                $objMail->setValue('to', $data['email']);
                $objMail->setValue('from', $from);
                $objMail->setValue('fromName', $fromName);
                $objMail->setValue('subject', $subject);
                $objMail->setValue('body', $plainMessage);
                $objMail->setValue('htmlbody', $htmlMessage);
                $objMail->send();
            }
        //} else {
            $retValue = FALSE;
        //}
            $domDoc->loadHTML(file_get_contents($templateLocation.'mailtemplate.html'));
            $elms = $domDoc->getElementsByTagName('table');
            foreach ($elms as $el){
                $val = $domDoc->saveHTML($el);
                echo $val;
            }
            echo $domDoc->saveHTML();
        return TRUE;
    }

}

?>