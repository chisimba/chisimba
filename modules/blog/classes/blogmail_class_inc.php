<?php
/**
 * Class to handle blog elements (searching).
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    $Id: blogmail_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @package    blog
 * @subpackage blogops
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to handle blog elements (searching)
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: blogmail_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blogmail extends object
{
    /**
     * Description for public
     *
     * @var    mixed
     * @access public
     */
    public $objConfig;
    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init() 
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objDbBlog = $this->getObject('dbblog');
            $this->loadClass('href', 'htmlelements');
            $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->showfullname = $this->sysConfig->getValue('show_fullname', 'blog');
            $this->objUser = $this->getObject('user', 'security');
            $this->objBlogPosts = $this->getObject('blogposts');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
        if (!extension_loaded("imap")) {
            $this->mailblog = FALSE;
        } else {
            $this->mailblog = TRUE;
        }
    }
    /**
     * Method to show a mail setup form to set the DSN for mail2blog
     *
     * @param  bool   $featurebox
     * @param  array  $dsnarr
     * @return string
     */
    public function showMailSetup($featurebox = TRUE, $dsnarr = NULL) 
    {
        if ($this->mailblog == FALSE) {
            return NULL;
        }
        // start a form to go back to the setupmail action with the vars
        // make sure that all form vars are required!
        $mform = new form('setupmail', $this->uri(array(
            'action' => 'setupmail'
        )));
        // add all the rules
        $mform->addRule('mprot', $this->objLanguage->languageText("mod_blog_phrase_mprotreq", "blog") , 'required');
        $mform->addRule('mserver', $this->objLanguage->languageText("mod_blog_phrase_mserverreq", "blog") , 'required');
        $mform->addRule('muser', $this->objLanguage->languageText("mod_blog_phrase_muserreq", "blog") , 'required');
        $mform->addRule('mpass', $this->objLanguage->languageText("mod_blog_phrase_mpassreq", "blog") , 'required');
        $mform->addRule('mport', $this->objLanguage->languageText("mod_blog_phrase_mportreq", "blog") , 'required');
        $mform->addRule('mbox', $this->objLanguage->languageText("mod_blog_phrase_mboxreq", "blog") , 'required');
        $mfieldset = $this->getObject('fieldset', 'htmlelements');
        $mfieldset->setLegend($this->objLanguage->languageText('mod_blog_setupmail', 'blog'));
        $madd = $this->newObject('htmltable', 'htmlelements');
        $madd->cellpadding = 5;
        $madd->width=450;
        // mail protocol field
        // dropdown for the POP/IMAP Chooser
        $protdrop = new dropdown('mprot');
        $protdrop->addOption("pop3", $this->objLanguage->languageText("mod_blog_pop3", "blog"));
        $protdrop->addOption("imap", $this->objLanguage->languageText("mod_blog_imap", "blog"));
        $madd->startRow();
        $protlabel = new label($this->objLanguage->languageText('mod_blog_mailprot', 'blog') . ':', 'input_mprot');
        $madd->addCell($protlabel->show());
        $madd->addCell($protdrop->show());
        $madd->endRow();
        // Mail server field
        $madd->startRow();
        $mslabel = new label($this->objLanguage->languageText('mod_blog_mailserver', 'blog') . ':', 'input_mserver');
        $mserver = new textinput('mserver');
        if (isset($dsnarr['server'])) {
            $mserver->setValue($dsnarr['server']);
        }
        $madd->addCell($mslabel->show());
        $madd->addCell($mserver->show());
        $madd->endRow();
        // Mail user field
        $madd->startRow();
        $mulabel = new label($this->objLanguage->languageText('mod_blog_mailuser', 'blog') . ':', 'input_muser');
        $muser = new textinput('muser');
        if (isset($dsnarr['user'])) {
            $muser->setValue($dsnarr['user']);
        }
        $madd->addCell($mulabel->show());
        $madd->addCell($muser->show());
        $madd->endRow();
        // Mail password field
        $madd->startRow();
        $mplabel = new label($this->objLanguage->languageText('mod_blog_mailpass', 'blog') . ':', 'input_mpass');
        $mpass = new textinput('mpass');
        if (isset($dsnarr['pass'])) {
            $mpass->setValue($dsnarr['pass']);
        }
        $madd->addCell($mplabel->show());
        $madd->addCell($mpass->show());
        $madd->endRow();
        // mail port field
        // dropdown for the POP/IMAP port
        $portdrop = new dropdown('mport');
        $portdrop->addOption(110, $this->objLanguage->languageText("mod_blog_110", "blog"));
        $portdrop->addOption(143, $this->objLanguage->languageText("mod_blog_143", "blog"));
        $madd->startRow();
        $portlabel = new label($this->objLanguage->languageText('mod_blog_mailport', 'blog') . ':', 'input_mport');
        $madd->addCell($portlabel->show());
        $madd->addCell($portdrop->show());
        $madd->endRow();
        // Mailbox field
        $madd->startRow();
        $mblabel = new label($this->objLanguage->languageText('mod_blog_mailbox', 'blog') . ':', 'input_mbox');
        $mbox = new textinput('mbox');
        if (isset($dsnarr['mailbox'])) {
            $mserver->setValue($dsnarr['mailbox']);
        }
        $mbox->setValue("INBOX");
        $madd->addCell($mblabel->show());
        $madd->addCell($mbox->show());
        $madd->endRow();
        $this->objMButton = &new button($this->objLanguage->languageText('word_save', 'system'));
        $this->objMButton->setValue($this->objLanguage->languageText('word_save', 'system'));
        $this->objMButton->setToSubmit();
        $mfieldset->addContent($madd->show());
        $mform->addToForm($mfieldset->show());
        $mform->addToForm($this->objMButton->show());
        $mform = $mform->show();
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_setupmail", "blog") , $mform);
            return $ret;
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
    public function mail2blog() 
    {
        // grab the DSN from the config file
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objImap = $this->getObject('imap', 'mail');
        $this->dsn = $this->objConfig->getItem('BLOG_MAIL_DSN');
        try {
            // grab a list of all valid users to an array for verification later
            $valid = $this->objDbBlog->checkValidUser();
            $valadds = array();
            // cycle through the valid email addresses and check that the mail is from a real user
            foreach($valid as $addys) {
                $valadds[] = array(
                    'address' => $addys['emailaddress'],
                    'userid' => $addys['userid']
                );
            }
            // connect to the IMAP/POP3 server
            $this->conn = $this->objImap->factory($this->dsn);
            // grab the mail headers
            $this->objImap->getHeaders();
            // var_dump($this->objImap->getHeaders());
            // check mail
            $this->thebox = $this->objImap->checkMbox();
            // get the mail folders
            $this->folders = $this->objImap->populateFolders($this->thebox);
            // count the messages
            $this->msgCount = $this->objImap->numMails();
            // echo $this->msgCount;
            // get the meassge headers
            $i = 1;
            // parse the messages
            while ($i <= $this->msgCount) {
                // get the header info
                $headerinfo = $this->objImap->getHeaderInfo($i);
                // from
                $address = $headerinfo->fromaddress;
                // subject
                $subject = $headerinfo->subject;
                // date
                $date = $headerinfo->Date;
                // message flag
                $read = $headerinfo->Unseen;
                // message body
                $bod = $this->objImap->getMessage($i);
                // check if there is an attachment
                if (empty($bod[1])) {
                    // nope no attachments
                    $attachments = NULL;
                } else {
                    // set the attachment
                    $attachments = $bod[1];
                    // loop through the attachments and write them down
                    
                }
                // make sure the body doesn't have any nasty chars
                $message = @htmlentities($bod[0]);
                // check for a valid user
                if (!empty($address)) {
                    // check the address against tbl_users to see if its valid.
                    // just get the email addy, we dont need the name as it can be faked
                    $fadd = $address;
                    // get rid of the RFC formatted email bits
                    $parts = explode("<", $fadd);
                    $parts = explode(">", $parts[1]);
                    // raw address string that we can use to check against
                    $addy = $parts[0];
                    // check if the address we get from the msg is in the array of valid addresses
                    foreach($valadds as $user) {
                        // check if there is a match to the user list
                        if ($user['address'] != $addy) {
                            // Nope, no match, not validated!
                            $validated = NULL;
                        } else {
                            // echo "Valid user!";
                            // match found, you are a valid user dude!
                            $validated = TRUE;
                            // set the userid
                            $userid = $user['userid'];
                            // all is cool, so lets break out of this loop and carry on
                            break;
                        }
                    }
                }
                if ($validated == TRUE) {
                    // insert the mail data into an array for manipulation
                    $data[] = array(
                        'userid' => $userid,
                        'address' => $address,
                        'subject' => $subject,
                        'date' => $date,
                        'messageid' => $i,
                        'read' => $read,
                        'body' => $message,
                        'attachments' => $attachments
                    );
                }
                // delete the message as we don't need it anymore
                echo "sorting " . $this->msgCount . "messages";
                $this->objImap->delMsg($i);
                $i++;
            }
            // is the data var set?
            if (!isset($data)) {
                $data = array();
            }
            // lets look at the data now
            foreach($data as $datum) {
                $newbod = $datum['body'];
                // add the [img][/img] tags to the body so that the images show up
                // we discard any other mimetypes for now...
                if (!empty($datum['attachments'])) {
                    if (is_array($datum['attachments'])) {
                        foreach($datum['attachments'] as $files) {
                            // do check for multiple attachments
                            // set the filename of the attachment
                            $fname = $files['filename'];
                            $filenamearr = explode(".", $fname);
                            $ext = pathinfo($fname);
                            $filename = $filenamearr[0] . "_" . time() . "." . $ext['extension'];
                            // decode the attachment data
                            $filedata = base64_decode($files['filedata']);
                            // set the path to write down the file to
                            $path = $this->objConfig->getContentBasePath() . 'users/' . $userid . '/';
                            // 'blog/';
                            $fullpath = $this->objConfig->getsiteRoot() . "/usrfiles/users/" . $userid . '/';
                            // check that the data dir is there
                            // echo $path, $fullpath; die();
                            if (!file_exists($path)) {
                                // dir doesn't exist so create it quickly
                                mkdir($path, 0777);
                            }
                            // fix up the filename a little
                            $filename = str_replace(" ", "_", $filename);
                            $filename = str_replace("%20", "_", $filename);
                            // change directory to the data dir
                            chdir($path);
                            // write the file
                            $handle = fopen($filename, 'wb');
                            fwrite($handle, $filedata);
                            fclose($handle);
                            if(extension_loaded('fileinfo'))
                            {
                            	$finfo = finfo_open(FILEINFO_MIME);
                            	$type = finfo_file($finfo, $filename);
                            }
                            else {
                            	$type = mime_content_type($filename);
                            }
                            $thing = explode(';', $type);
                            $tparts = explode("/", $thing[0]);
                            
                            // print_r($tparts);
                            if ($tparts[0] == "image") {
                                // add the img stuff to the body at the end of the "post"
                                $newbod.= "[img]" . $fullpath . $filename . "[/img]" . "<br />";
                            } elseif ($tparts[1] == "3gpp") {
                                if ($tparts[0] == "video") {
                                    log_debug("Found a 3gp Video file! Processing...");
                                    // send to the mediaconverter to convert to flv
                                    //$mediacon = $this->getObject('media', 'utilities');
                                    $file = $path . $filename;
                                    // echo $file;
                                    // $flv = $mediacon->convert3gp2flv($file, $fullpath);
                                    $flv = $this->rpc3gp2flv($file, $path);
                                    $flv = basename($flv);
                                    $flv = $fullpath.$flv;
                                    // echo "file saved to: $flv";
                                    $newbod.= "[FLV]" . $flv.'.flv' . "[/FLV]" . " <br />";
                                    // echo $newbod;
                                    
                                } elseif ($tparts[0] == "audio") {
                                    log_debug("Found a 3gp amr file! Processing...");
                                    // amr file
                                    $mediacon = $this->getObject('media', 'utilities');
                                    $file = $path . $filename;
                                    // echo $file;
                                    $mp3 = $mediacon->convertAmr2Mp3($file, $fullpath);
                                    $newbod.= "[EMBED]" . $mp3 . "[/EMBED]" . " <br />";
                                }
                            } elseif ($tparts[1] == "mp4") {
                                if ($tparts[0] == "video") {
                                    log_debug("Found an MP4 container file");
                                    // send to the mediaconverter to convert to flv
                                    $mediacon = $this->getObject('media', 'utilities');
                                    $file = $path . $filename;
                                    // echo $file;
                                    $flv = $mediacon->convertMp42flv($file, $fullpath);
                                    // echo "file saved to: $flv";
                                    $newbod.= "[FLV]" . $flv . "[/FLV]" . " <br />";
                                }
                            } else {
                                // add the img stuff to the body at the end of the "post"
                                $newbod.= "[url]" . $this->objConfig->getSiteRoot() . 'usrfiles/users/' . $userid . '/' . urlencode($filename) . "[/url]" . "<br />";
                            }
                        }
                    } else {
                        // set the filename of the attachment
                        $fname = $datum['attachments'][0]['filename'];
                        $filenamearr = explode(".", $fname);
                        $ext = pathinfo($fname);
                        $filename = $filenamearr[0] . "_" . time() . "." . $ext['extension'];
                        // decode the attachment data
                        $filedata = base64_decode($datum['attachments'][0]['filedata']);
                        // set the path to write down the file to
                        $path = $this->objConfig->getContentBasePath() . 'blog/';
                        // check that the data dir is there
                        // fix up the filename a little
                        $filename = str_replace(" ", "_", $filename);
                        if (!file_exists($path)) {
                            // dir doesn't exist so create it quickly
                            mkdir($path, 0777);
                        }
                        // change directory to the data dir
                        chdir($path);
                        // write the file
                        $handle = fopen($filename, 'wb');
                        fwrite($handle, $filedata);
                        fclose($handle);
                        
                        if(extension_loaded('fileinfo'))
                            {
                            	$finfo = finfo_open(FILEINFO_MIME);
                            	$type = finfo_file($finfo, $filename);
                            }
                            else {
                            	$type = mime_content_type($filename);
                            }
                            $thing = explode(';', $type);
                            $tparts = explode("/", $thing[0]);
                            
                        if ($tparts[0] == "image") {
                            // add the img stuff to the body at the end of the "post"
                            $newbod.= "[img]" . $this->objConfig->getSiteRoot() . 'usrfiles/blog/' . $filename . "[/img]" . "<br />";
                        } else {
                            // add the img stuff to the body at the end of the "post"
                            $newbod.= "[url]" . $this->objConfig->getSiteRoot() . 'usrfiles/blog/' . urlencode($filename) . "[/url]" . "<br />";
                        }
                    }
                } else {
                    // no attachments to worry about
                    $newbod = $datum['body'];
                }
                // Write the new post to the database as a "Quick Post"
                $this->objBlogPosts->quickPostAdd($datum['userid'], array(
                    'posttitle' => $datum['subject'],
                    'postcontent' => $newbod,
                    'postcat' => 0,
                    'postexcerpt' => '',
                    'poststatus' => '0',
                    'commentstatus' => 'Y',
                    'postmodified' => date('r') ,
                    'commentcount' => 0,
                    'postdate' => $datum['date']
                ) , 'mail');
            }
        }
        // any issues?
        catch(customException $e) {
            // clean up and die!
            customException::cleanUp();
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
    public function listmail2blog() 
    {
        // grab the DSN from the config file
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objImap = $this->getObject('imap', 'mail');
        $listdsn = $this->sysConfig->getValue('list_dsn', 'blog');
        // $listdsn = $this->objConfig->getItem('BLOG_LISTMAIL_DSN');
        // $userid = $this->sysConfig->getValue('list_userid', 'blog');
        // $listidentifier = $this->sysConfig->getValue('list_identifier', 'blog');
        // grab a list of identified lists
        $validlists = $this->objDbBlog->getLists();
        // create an array of valid identifiers
        foreach($validlists as $valididentifiers) {
            $valid[] = $valididentifiers['list_identifier'];
        }
        try {
            // connect to the IMAP/POP3 server
            $this->conn = $this->objImap->factory($listdsn);
            // grab the mail headers
            $this->objImap->getHeaders();
            // check mail
            $this->thebox = $this->objImap->checkMbox();
            // get the mail folders
            $this->folders = $this->objImap->populateFolders($this->thebox);
            // count the messages
            $this->msgCount = $this->objImap->numMails();
            // echo $this->msgCount;
            // get the meassge headers
            $i = 1;
            // parse the messages
            while ($i <= $this->msgCount) {
                // get the header info
                $headerinfo = $this->objImap->getHeaderInfo($i);
                // from
                $address = @$headerinfo->fromaddress;
                // subject
                $subject = @$headerinfo->subject;
                // date
                $date = @$headerinfo->Date;
                // message flag
                $read = @$headerinfo->Unseen;
                // message body
                $bod = $this->objImap->getMessage($i);
                // put this into a foreach to check all valid lists
                // check to see that the message comes from [Nextgen-online]
                foreach($valid as $listidentifier) {
                    if (preg_match('/\[' . $listidentifier . '\]/U', $subject)) {
                        $message = @htmlentities($bod[0]);
                        $listinfo = $this->objDbBlog->getListInfo($listidentifier);
                        // print_r($listinfo);die();
                        $userid = $listinfo[0]['listuser'];
                        // lets strip out the email addresses first to stop spam bots
                        $message = str_replace("<", "", $message);
                        $message = str_replace(">", "", $message);
                        $message = preg_replace('/[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}/im', " " . $this->objLanguage->languageText("mod_blog_emailreplaced", "blog") , $message);
                        // insert the mail data into an array for manipulation
                        $data[] = array(
                            'userid' => $userid,
                            'address' => $address,
                            'subject' => $subject,
                            'date' => $date,
                            'messageid' => $i,
                            'read' => $read,
                            'body' => $message,
                            'attachments' => $attachments
                        );
                        // echo "valid list mail";
                        $validated = TRUE;
                        // break;
                        
                    } else {
                        $validated = FALSE;
                    }
                }
                // check if there is an attachment
                if (empty($bod[1])) {
                    // nope no attachments
                    $attachments = NULL;
                } else {
                    // set the attachment
                    $attachments = $bod[1];
                    // loop through the attachments and write them down
                    
                }
                // make sure the body doesn't have any nasty chars
                $message = @htmlentities($bod[0]);
                /*if($validated == TRUE)
                {
                // echo "grabbing the list info";
                // grab the userid from the table
                $listinfo = $this->objDbBlog->getListInfo($listidentifier);
                // print_r($listinfo);die();
                $userid = $listinfo[0]['listuser'];
                // insert the mail data into an array for manipulation
                $data[] = array('userid' => $userid,'address' => $address, 'subject' => $subject, 'date' => $date, 'messageid' => $i, 'read' => $read,
                'body' => $message, 'attachments' => $attachments);
                }*/
                // delete the message as we don't need it anymore
                // echo "sorting " . $this->msgCount . "messages";
                $this->objImap->delMsg($i);
                $i++;
            }
            // is the data var set?
            if (!isset($data)) {
                $data = array();
            }
            // lets look at the data now
            foreach($data as $datum) {
                $newbod = $datum['body'];
                // add the [img][/img] tags to the body so that the images show up
                // we discard any other mimetypes for now...
                if (!empty($datum['attachments'])) {
                    if (is_array($datum['attachments'])) {
                        foreach($datum['attachments'] as $files) {
                            // do check for multiple attachments
                            // set the filename of the attachment
                            $fname = $files['filename'];
                            $filenamearr = explode(".", $fname);
                            $ext = pathinfo($fname);
                            $filename = $filenamearr[0] . "_" . time() . "." . $ext['extension'];
                            // decode the attachment data
                            $filedata = base64_decode($files['filedata']);
                            // set the path to write down the file to
                            $path = $this->objConfig->getContentBasePath() . 'users/' . $userid . '/';
                            // 'blog/';
                            $fullpath = $this->objConfig->getsiteRoot() . "/usrfiles/users/" . $userid . '/';
                            // check that the data dir is there
                            // echo $path, $fullpath; die();
                            if (!file_exists($path)) {
                                // dir doesn't exist so create it quickly
                                mkdir($path, 0777);
                            }
                            // fix up the filename a little
                            $filename = str_replace(" ", "_", $filename);
                            $filename = str_replace("%20", "_", $filename);
                            // change directory to the data dir
                            chdir($path);
                            // write the file
                            $handle = fopen($filename, 'wb');
                            fwrite($handle, $filedata);
                            fclose($handle);
                            
                            if(extension_loaded('fileinfo'))
                            {
                            	$finfo = finfo_open(FILEINFO_MIME);
                            	$type = finfo_file($finfo, $filename);
                            }
                            else {
                            	$type = mime_content_type($filename);
                            }
                            $thing = explode(';', $type);
                            $tparts = explode("/", $thing[0]);
                            
                            // print_r($tparts);
                            if ($tparts[0] == "image") {
                                // add the img stuff to the body at the end of the "post"
                                $newbod.= "[img]" . $fullpath . $filename . "[/img]" . "<br />";
                            } elseif ($tparts[1] == "3gpp") {
                                if ($tparts[0] == "video") {
                                    log_debug("Found a 3gp Video file! Processing...");
                                    // send to the mediaconverter to convert to flv
                                    $mediacon = $this->getObject('media', 'utilities');
                                    $file = $path . $filename;
                                    // echo $file;
                                    $flv = $mediacon->convert3gp2flv($file, $fullpath);
                                    // echo "file saved to: $flv";
                                    $newbod.= "[FLV]" . $flv . "[/FLV]" . " <br />";
                                    // echo $newbod;
                                    
                                } elseif ($tparts[0] == "audio") {
                                    log_debug("Found a 3gp amr file! Processing...");
                                    // amr file
                                    $mediacon = $this->getObject('media', 'utilities');
                                    $file = $path . $filename;
                                    // echo $file;
                                    $mp3 = $mediacon->convertAmr2Mp3($file, $fullpath);
                                    $newbod.= "[EMBED]" . $mp3 . "[/EMBED]" . " <br />";
                                }
                            } elseif ($tparts[1] == "mp4") {
                                if ($tparts[0] == "video") {
                                    log_debug("Found an MP4 container file");
                                    // send to the mediaconverter to convert to flv
                                    $mediacon = $this->getObject('media', 'utilities');
                                    $file = $path . $filename;
                                    // echo $file;
                                    $flv = $mediacon->convertMp42flv($file, $fullpath);
                                    // echo "file saved to: $flv";
                                    $newbod.= "[FLV]" . $flv . "[/FLV]" . " <br />";
                                }
                            } else {
                                // add the img stuff to the body at the end of the "post"
                                $newbod.= "[url]" . $this->objConfig->getSiteRoot() . 'usrfiles/users/' . $userid . '/' . urlencode($filename) . "[/url]" . "<br />";
                            }
                        }
                    } else {
                        // set the filename of the attachment
                        $fname = $datum['attachments'][0]['filename'];
                        $filenamearr = explode(".", $fname);
                        $ext = pathinfo($fname);
                        $filename = $filenamearr[0] . "_" . time() . "." . $ext['extension'];
                        // decode the attachment data
                        $filedata = base64_decode($datum['attachments'][0]['filedata']);
                        // set the path to write down the file to
                        $path = $this->objConfig->getContentBasePath() . 'blog/';
                        // check that the data dir is there
                        // fix up the filename a little
                        $filename = str_replace(" ", "_", $filename);
                        if (!file_exists($path)) {
                            // dir doesn't exist so create it quickly
                            mkdir($path, 0777);
                        }
                        // change directory to the data dir
                        chdir($path);
                        // write the file
                        $handle = fopen($filename, 'wb');
                        fwrite($handle, $filedata);
                        fclose($handle);
                        
                        if(extension_loaded('fileinfo'))
                            {
                            	$finfo = finfo_open(FILEINFO_MIME);
                            	$type = finfo_file($finfo, $filename);
                            }
                            else {
                            	$type = mime_content_type($filename);
                            }
                            $thing = explode(';', $type);
                            $tparts = explode("/", $thing[0]);
                        
                        if ($tparts[0] == "image") {
                            // add the img stuff to the body at the end of the "post"
                            $newbod.= "[img]" . $this->objConfig->getSiteRoot() . 'usrfiles/blog/' . $filename . "[/img]" . "<br />";
                        } else {
                            // add the img stuff to the body at the end of the "post"
                            $newbod.= "[url]" . $this->objConfig->getSiteRoot() . 'usrfiles/blog/' . urlencode($filename) . "[/url]" . "<br />";
                        }
                    }
                } else {
                    // no attachments to worry about
                    $newbod = $datum['body'];
                }
                // Write the new post to the database as a "Quick Post"
                $this->objBlogPosts->quickPostAdd($datum['userid'], array(
                    'posttitle' => $datum['subject'],
                    'postcontent' => $newbod,
                    'postcat' => 0,
                    'postexcerpt' => '',
                    'poststatus' => '0',
                    'commentstatus' => 'Y',
                    'postmodified' => date('r') ,
                    'commentcount' => 0,
                    'postdate' => $datum['date']
                ) , 'mail');
            }
        }
        // any issues?
        catch(customException $e) {
            // clean up and die!
            customException::cleanUp();
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  array   $m2fdata Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function sendMail2FriendForm($m2fdata) 
    {
        $this->objUser = $this->getObject('user', 'security');
        if ($this->objUser->isLoggedIn()) {
            if ($this->showfullname == 'FALSE') {
                $theuser = $this->objUser->userName($this->objUser->userid());
            } else {
                $theuser = $this->objUser->fullname($this->objUser->userid());
            }
            // $theuser = $this->objUser->fullName($this->objUser->userid());
            
        } else {
            $theuser = $this->objLanguage->languageText("mod_blog_word_anonymous", "blog");
        }
        // start a form object
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $mform = new form('mail2friend', $this->uri(array(
            'action' => 'mail2friend',
            'postid' => $m2fdata['postid']
        )));
        $mfieldset = $this->newObject('fieldset', 'htmlelements');
        // $mfieldset->setLegend($this->objLanguage->languageText('mod_blog_sendmail2friend', 'blog'));
        $mtable = $this->newObject('htmltable', 'htmlelements');
        $mtable->cellpadding = 3;
        $mtable->startHeaderRow();
        $mtable->addHeaderCell('');
        $mtable->addHeaderCell('');
        $mtable->endHeaderRow();
        // your name
        $mtable->startRow();
        $mynamelabel = new label($this->objLanguage->languageText('mod_blog_myname', 'blog') . ':', 'input_myname');
        $myname = new textinput('sendername');
        $myname->size = '80%';
        $myname->setValue($theuser);
        $mtable->addCell($mynamelabel->show());
        $mtable->addCell($myname->show());
        $mtable->endRow();
        // Friend(s) email addresses
        $mtable->startRow();
        $femaillabel = new label($this->objLanguage->languageText('mod_blog_femailaddys', 'blog') . ':', 'input_femail');
        $emailadd = new textinput('emailadd');
        $emailadd->size = '80%';
        if (isset($m2fdata['user'])) {
            $emailadd->setValue($m2fdata['user']);
        }
        $mtable->addCell($femaillabel->show());
        $mtable->addCell($emailadd->show());
        $mtable->endRow();
        // message for friends (optional)
        $mtable->startRow();
        $fmsglabel = new label($this->objLanguage->languageText('mod_blog_femailmsg', 'blog') . ':', 'input_femailmsg');
        $msg = new textarea('msg', '', 4, 68);
        $mtable->addCell($fmsglabel->show());
        $mtable->addCell($msg->show());
        $mtable->endRow();
        // add a rule
        $mform->addRule('emailadd', $this->objLanguage->languageText("mod_blog_phrase_femailreq", "blog") , 'email');
        $mfieldset->addContent($mtable->show());
        $mform->addToForm($mfieldset->show());
        $this->objMButton = new button($this->objLanguage->languageText('mod_blog_word_sendmail', 'blog'));
        $this->objMButton->setValue($this->objLanguage->languageText('mod_blog_word_sendmail', 'blog'));
        $this->objMButton->setToSubmit();
        $mform->addToForm($this->objMButton->show());
        $mform = $mform->show();
        // bust out a featurebox for consistency
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_sendmail2friend", "blog") , $mform);
        return $ret;
    }
    /**
     * Method to retrieve the mail dsn from the config.xml file
     *
     * @param  void
     * @return string
     */
    public function getMailDSN() 
    {
        // check that the variables are set, if not return the template, otherwise return a thank you and carry on
        $this->objConfig = $this->getObject('altconfig', 'config');
        $vals = $this->objConfig->getItem('BLOG_MAIL_DSN');
        if ($vals != FALSE) {
            $dsnparse = $this->parseDSN($vals);
            return $dsnparse;
        } else {
            return FALSE;
        }
    }
    /**
     * Method to parse the DSN
     *
     * @access public
     * @param  string $dsn
     * @return void
     */
    public function parseDSN($dsn) 
    {
        $parsed = NULL;
        // $this->imapdsn;
        $arr = NULL;
        if (is_array($dsn)) {
            $dsn = array_merge($parsed, $dsn);
            return $dsn;
        }
        // find the protocol
        if (($pos = strpos($dsn, ':// ')) !== false) {
            $str = substr($dsn, 0, $pos);
            $dsn = substr($dsn, $pos+3);
        } else {
            $str = $dsn;
            $dsn = null;
        }
        if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
            $parsed['protocol'] = $arr[1];
            $parsed['protocol'] = !$arr[2] ? $arr[1] : $arr[2];
        } else {
            $parsed['protocol'] = $str;
            $parsed['protocol'] = $str;
        }
        if (!count($dsn)) {
            return $parsed;
        }
        // Get (if found): username and password
        if (($at = strrpos($dsn, '@')) !== false) {
            $str = substr($dsn, 0, $at);
            $dsn = substr($dsn, $at+1);
            if (($pos = strpos($str, ':')) !== false) {
                $parsed['user'] = rawurldecode(substr($str, 0, $pos));
                $parsed['pass'] = rawurldecode(substr($str, $pos+1));
            } else {
                $parsed['user'] = rawurldecode($str);
            }
        }
        // server
        if (($col = strrpos($dsn, ':')) !== false) {
            $strcol = substr($dsn, 0, $col);
            $dsn = substr($dsn, $col+1);
            if (($pos = strpos($strcol, '/')) !== false) {
                $parsed['server'] = rawurldecode(substr($strcol, 0, $pos));
            } else {
                $parsed['server'] = rawurldecode($strcol);
            }
        }
        // now we are left with the port and mailbox so we can just explode the string and clobber the arrays together
        $pm = explode("/", $dsn);
        $parsed['port'] = $pm[0];
        $parsed['mailbox'] = $pm[1];
        $dsn = NULL;
        return $parsed;
    }
    
    public function rpc3gp2flv($media, $path)
    {
    	require_once($this->getPearResource('XML/RPC.php'));
    	$gpfile = base64_encode(file_get_contents($media));
    	@$params = array(new XML_RPC_Value("a huge honking hashed up api key", "string"), new XML_RPC_Value($gpfile, "string"));
    	// Construct the method call (message). 
		$msg = new XML_RPC_Message('media.3gp2flv', $params);
		// The server is the 2nd arg, the path to the API module is the 1st.
		// get the config vars for the api endpoint...
		$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $server = $this->sysConfig->getValue('blog_ffmpegserv', 'blog');
        $uri = $this->sysConfig->getValue('blog_ffmpeguri', 'blog');
		$cli = new XML_RPC_Client($uri, $server);
		// set the debug level to 0 for no debug, 1 for debug mode...
		$cli->setDebug(0);
		// bomb off the message to the server
		$resp = $cli->send($msg);
		if (!$resp) {
 		    log_debug('Communication error: ' . $cli->errstr);
    	    return FALSE;
		}
		if (!$resp->faultCode()) {
    		$val = $resp->value();
    		$val = XML_RPC_decode($val);
    		// write the returned flv data to a file with a path
    		$retfile = $path.time().Rand(1,999);
			file_put_contents($retfile.'.flv', base64_decode($val));
			return $retfile;
		} else {
    		/*
     		 * Display problems that have been gracefully caught and
     		 * reported by the Chisimba api.
     		 */
    		log_debug('Fault Code: ' . $resp->faultCode() . "\n");
    		log_debug('Fault Reason: ' . $resp->faultString() . "\n");
    		return FALSE;
		}
    }
}
?>