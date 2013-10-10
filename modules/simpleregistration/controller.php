<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end security check

class simpleregistration extends controller {

    var $eventid;

    function init() {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objLog = $this->getObject('logactivity', 'logger');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->dbevents = $this->getObject('dbevents');
        $this->utils = $this->getObject('simpleregistrationutils', 'simpleregistration');
        $this->dbeventscontent = $this->getObject('dbeventscontent');
        $this->dbcomments = $this->getObject('dbcomments');
	$this->dbeventmembers = $this->getObject('dbregistration');
	
        $this->objLog->log();
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }

    public function dispatch($action) {
        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * default home page
     * @return <type>
     */
    function __home() {
        return "eventlisting_tpl.php";
    }

    function __showevent() {
        $status = $this->getParam('status');
        $this->eventid = $this->getParam('eventid');
        if ($status == 'closed') {
            $this->setVarByRef('eventid', $this->eventid);
            $content = $this->dbeventscontent->getEventContent($eventid);
            $this->setVarByRef('content', $content);
            return "closed_tpl.php";
        }
        $firstname = $this->getParam('firstname');
        $lastname = $this->getParam('lastname');
        $company = $this->getParam('company');
        $email = $this->getParam('email');

        $mode = $this->getParam('mode');
        $this->setVarByRef('editfirstname', $firstname);
        $this->setVarByRef('editlastname', $lastname);
        $this->setVarByRef('editcompany', $company);
        $this->setVarByRef('editemail', $email);
        $this->setVarByRef('mode', $mode);

        $this->setVarByRef('eventid', $this->eventid);
        //echo $this->eventid;
        $content = $this->dbeventscontent->getEventContent($this->eventid);
	
        //print_r($content);
        //die();
        $this->setVarByRef('eventcontent', $content);
        return "home_tpl.php";
    }

    /**
     * save a new registration; incase email exists already ,return it back to
     * user
     */
    function __register() {
             $firstame = $this->getParam('firstname');
        $lastname = $this->getParam('lastname');
        $company = $this->getParam('company');
        $email = $this->getParam('emailfield');
        $reg = $this->getObject('dbregistration');
        $eventid = $this->getParam('eventid');
        $totalregistrations = $reg->getRegistrationCount($eventid);
	$maxregistrations = $this->dbevents->getMaxRegistrations($eventid);
        if ($totalregistrations > $maxregistrations) {
            return "registrationful_tpl.php";
        }
      
        if ($reg->emailExists($email, $eventid)) {
            $message = "The email you've used already exists. Please use a different one.";
            $this->setVarByRef('eventid', $eventid);
            $this->setVarByRef('message', $message);
            return "registrationfailed_tpl.php";
        }

        if ($reg->addRegistration($firstame, $lastname, $company, $email, $eventid)) {

            $this->sendMail($email, $eventid);
            $this->nextAction("success", array('title1' => $this->objLanguage->languageText('mod_simpleregistration_registrationsuccess', 'simpleregistration'),
                'title2' => '', 'eventid' => $eventid));
        } else {
            $this->setVarByRef('eventid', $eventid);
            return "registrationfailed_tpl.php";
        }
    }

    function __expresssignin() {
        $eventid = $this->getParam('eventid');
        $reg = $this->getObject('dbregistration');
        $lastname = $this->objUser->getFirstName();
        $email = $this->objUser->email();

        if ($reg->emailExists($this->objUser->email(), $eventid)) {
            $this->nextAction('success', array('title1' => $this->objLanguage->languageText('mod_simpleregistration_alreadysignedup', 'simpleregistration'),
                'title2' => '', 'eventid' => $eventid));
        } else {
            $this->nextAction('register', array('firstname' => $this->objUser->getSurname(),
                'lastname' => $lastname,
                'company' => $this->objConfig->getSiteName(),
                'emailfield' => $email,
                'title1' => $this->objLanguage->languageText('mod_simpleregistration_registrationsuccess', 'simpleregistration'),
                'title2' => $this->objLanguage->languageText('mod_simpleregistration_success', 'simpleregistration'),
                'eventid' => $eventid));
        }
    }


    /**
     *
     * Method to set the 'edit' mode for updating registered events
     *
     * @access private
     *
     */
    function __editevent() {
        $this->setVar('mode', 'edit');
	$eventid = $this->getParam('eventid');
	$this->setVar('eventid', $eventid);
	$addevent = $this->dbevents->getEvent($eventid);
	$eventcontent = $this->dbeventscontent->getEventContent($eventid);
	$this->setVar('addevent', $addevent);
	$this->setVar('eventcontent', $eventcontent);
        return 'addeditevent.php'; 
    }

    /**
     *
     * Method to set the 'add' mode for adding new events events
     *
     * @access private
     *
     */
    function __addevent() {
        $this->setVar('mode', 'add');
	$id = $this->getParam('eventid');
        return 'addeditevent.php'; 
    } 


    /**
     *
     * Method to update registered events
     *
     * @access private
     *
     */
   function __updateevent() {

	$eventid = $this->getParam('eventid');
	$eventtitle = $this->getParam('titlefield');
        $eventdate = $this->getParam('eventdate');
        $sn = $this->getParam('shorttitlefield');
	$maxNumberOfParticipants = $this->getParam('maxpeoplefield');
	//Update the event
        $this->dbevents->updateEvent($eventid,$eventtitle, $sn, $maxNumberOfParticipants,$eventdate);
	$Instructions = $this->getParam('venuefield');
	$contentfield = $this->getParam('contentfield');
	$lefttitle1field  = $this->getParam('lefttitle1field');
	$lefttitle2field  = $this->getParam('lefttitle2field');
	$footerfield  = $this->getParam('footerfield');
	$eventemailfield  = $this->getParam('emailcontactfield');
	$emailnamefield  = $this->getParam('emailnamefield');
	$emailsubjectfield  = $this->getParam('emailsubjectfield');
	$emailcontentfield = $this->getParam('emailcontentfield');
	$emailattachmentsfield = $this->getParam('emailattachmentsfield');
	$staffregfield = $this->getParam('staffregfield');
	$visitorregfield = $this->getParam('visitorregfield');

	//Update event content
	$this->dbeventscontent->updateEventContent(
                $eventid,
                $Instructions,
                $contentfield,
                $lefttitle1field,
                $lefttitle2field,
                $footerfield,
                $eventemailfield,
                $emailsubjectfield,
                $emailnamefield,
                $emailcontentfield,
                $emailattachmentsfield,
                $staffregfield,
                $visitorregfield);

	
       $this->nextAction('eventlisting');


    }

     /**
     *
     * Method to save new events
     *
     * @access private
     *
     */

    function __saveevent() {
        $eventtitle = $this->getParam('titlefield');
        $eventdate = $this->getParam('eventdate');
        $sn = $this->getParam('shorttitlefield');
	$maxNumberOfParticipants = $this->getParam('maxpeoplefield');
	//Save the event
        $id = $this->dbevents->addEvent($eventtitle, $sn, $maxNumberOfParticipants,$eventdate);
	$Instructions = $this->getParam('venuefield');
	$contentfield = $this->getParam('contentfield');
	$lefttitle1field  = $this->getParam('lefttitle1field');
	$lefttitle2field  = $this->getParam('lefttitle2field');
	$footerfield  = $this->getParam('footerfield');
	$eventemailfield  = $this->getParam('emailcontactfield');
	$emailnamefield  = $this->getParam('emailnamefield');
	$emailsubjectfield  = $this->getParam('emailsubjectfield');
	$emailcontentfield = $this->getParam('emailcontentfield');
	$emailattachmentsfield = $this->getParam('emailattachmentsfield');
	$staffregfield = $this->getParam('staffregfield');
	$visitorregfield = $this->getParam('visitorregfield');
	//Save event content
        $this->dbeventscontent->addEventContent(
                $id,
                $Instructions,
                $contentfield,
                $lefttitle1field,
                $lefttitle2field,
                $footerfield,
                $eventemailfield,
                $emailsubjectfield,
                $emailnamefield,
                $emailcontentfield,
                $emailattachmentsfield,
                $staffregfield,
                $visitorregfield);
	
        $this->nextAction('eventlisting');
    }

    function __savecomment() {
        $comment = $this->getParam("commentsField");
        echo $comment;
        die();
        $eventid = $this->getParam("eventid");
        $this->dbcomments->addComment($comment);
        $this->nextAction("commentslist", array("eventid" => $eventid));
    }

    /**
     * The functions require login
     * @return <type>
     */
    function __memberlist() {
        $eventid = $this->getParam('eventid');
        $this->setVarByRef('eventid', $eventid);
        return "memberlist_tpl.php";
    }

    function __savecontent() {

        $eventid = $this->getParam('eventid');
        $venue = $this->getParam('venuefield');
        $content = $this->getParam('contentfield');
        $lefttitle1 = $this->getParam('lefttitle1field');
        $lefttitle2 = $this->getParam('lefttitle2field');
        $footer = $this->getParam('footerfield');
        $emailcontact = $this->getParam('emailcontactfield');
        $emailsubject = $this->getParam('emailsubjectfield');
        $emailname = $this->getParam('emailnamefield');
        $emailcontent = $this->getParam('emailcontentfield');
        $emailattachments = $this->getParam('emailattachmentfield');
        $staffreg = $this->getParam('staffregfield');
        $visitorreg = $this->getParam('visitorregfield');
        $mode = $this->getParam('mode');
        //  print_r($_POST);
        //   echo $eventid.",".$venue;
        // die();

        /* if($mode == "new") {
          $this->dbeventscontent->addEventContent(
          $eventid,
          $venue,
          $content,
          $lefttitle1,
          $lefttitle2,
          $footer,
          $emailcontact,
          $emailsubject,
          $emailname,
          $emailsubject,
          $emailcontent,
          $emailattachments,
          $staffreg,
          $visitorreg
          );

          }

          if($mode == "edit") {
         */ $this->dbeventscontent->updateEventContent(
                $eventid,
                $venue,
                $content,
                $lefttitle1,
                $lefttitle2,
                $footer,
                $emailcontact,
                $emailsubject,
                $emailname,
                $emailsubject,
                $emailcontent,
                $emailattachments,
                $staffreg,
                $visitorreg);
        // }
        $this->nextAction('eventlisting');
    }

    function __eventcontent() {
        $eventid = $this->getParam('eventid');
        $title = $this->getParam('eventtitle');
        $content = $this->dbeventscontent->getEventContent($eventid);
        $this->setVarByRef('eventid', $eventid);
        $this->setVarByRef('content', $content);
        $this->setVarByRef('title', $title);
        return "eventcontent_tpl.php";
    }

    function __download() {

        return "download_tpl.php";
    }

    function __eventlisting() {
        return "eventlisting_tpl.php";
    }

    function __deletemember() {
        $id = $this->getParam('id');
        $eventid = $this->getParam('eventid');
        $reg = $this->getObject('dbregistration');
        $reg->deleteMember($id);
        $this->nextAction('memberlist', array('eventid' => $eventid));
    }

    function __xls() {
        $eventid = $this->getParam('eventid');
        $reg = $this->getObject('dbregistration');
        $dbdata = $reg->getRegistrations($eventid);
        $stringData = '';

        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $downloadfolder = $objSysConfig->getValue('DOWNLOAD_FOLDER', 'simpleregistration');

        $docRoot = $_SERVER['DOCUMENT_ROOT'] . $downloadfolder;

        $myFile = $docRoot . "listing.xls";
        unlink($myFile);
        /*
         * $fh = fopen($myFile, 'w') or die("can't open file");
          foreach($dbdata as $row){
          fwrite($fh,$row['first_name'].'    '.$row['last_name'].'   '.$row['email'].'   '.$row['company']);
          } */
        $file = fopen($myFile, "a");
        //delete old one

        foreach ($dbdata as $row) {
            fputs($file, $row['first_name'] . '    ' . $row['last_name'] . '   ' . $row['email'] . '   ' . $row['company'] . "\r\n");
        }
        fclose($file);
        //fclose($fh);

        $this->nextAction('download');
    }

    /**
     *
     *
     * Registration is a success, inform users
     */
    function __success() {
        $title1 = $this->getParam('title1');
        $title2 = $this->getParam('title2');
        $this->setVarByRef('rightTitle1', $title1);
        $this->setVarByRef('rightTitle2', $title2);
        $eventid = $this->getParam('eventid');
        $content = $this->dbeventscontent->getEventContent($eventid);
        $this->setVarByRef('eventid', $eventid);
        $this->setVarByRef('eventcontent', $content);
        return "success_tpl.php";
    }

        /**
     *
     *
     */
    function __deleteevent() {
        $eventid = $this->getParam('id');

       	$event = $this->dbevents->getEvent($eventid);
	

        if ($event == FALSE) {
            //return $this->nextAction('home');
		echo 'u suck';
        } else {
            $this->setVarByRef('event', $event);

            $randomNumber = rand(0, 50000);
            $this->setSession('deleteevent_' . $event['id'], $randomNumber);
            $this->setVarByRef('deleteValue', $randomNumber);

            return 'deleteevent.php';
        }
    }

	    /**
     *
     *
     *
     */
    function __deleteeventconfirm() {
        $id = $this->getParam('id');
        $confirm = $this->getParam('confirm');

        if (($id != '') && ($confirm == 'yes')) {
            $event = $this->dbevents->getEvent($id);

            if ($event == FALSE) {
                return $this->nextAction('home');
            } else {
                $this->dbcomments->deleteEventComments($id);
		$this->dbeventscontent->deleteEventContent($id);
		$this->dbeventmembers->deleteEventMembers($id);

                $this->setSession('deletestory_' . $event['id'], NULL);

                $this->dbevents->deleteEvent($id);

                return $this->nextAction('home');
            }
        } else {
            return $this->nextAction('home');
        }
    }

    /**
     *  Sends the email to the newly registered member
     */
    function sendMail($to, $eventid) {
        $content = $this->dbeventscontent->getEventContent($eventid);
        $contactemail = $content['event_emailcontact'];
        $subject = $content['event_emailsubject'];
        $body = $content['event_emailcontent'];
        $emailName = $content['event_emailname'];

        $attachs = explode("|", $content['event_emailattachments']);


        $objMailer = $this->getObject('mailer', 'mail');
        $objMailer->setValue('to', array($to));
        $objMailer->setValue('from', $contactemail);
        $objMailer->setValue('fromName', $emailName);
        $objMailer->setValue('subject', $subject);
        $objMailer->setValue('body', $body);
        foreach ($attachs as $attach) {
            $objMailer->attach($attach);
        }
        $objMailer->send();
    }

    public function __addcomments() {
        $eventid = $this->getParam('eventid');
        $this->setVarByRef('eventid', $eventid);
        return "comments_tpl.php";
    }

    /**
     * Overridden method to determine whether or not login is required
     *
     * @return FALSE
     */
    function requiresLogin($action='home') {
        $allowedActions = array(NULL, 'showevent', 'register', 'success');

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
