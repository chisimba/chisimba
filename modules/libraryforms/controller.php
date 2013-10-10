<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
// end security check

/**
 *
 * libraryforms
 *
 * libraryforms allows students or distant user to request books online
 *
 * @category  Chisimba
 * @package   libraryforms
 * @author    Brenda Mayinga brendamayinga@ymail.com
 * */
class libraryforms extends controller {

    public $objLanguage;
    public $required;
    protected $objMail;
    var $errormsg;
    var $msg;
    var $erormsg;

    public function init() {

        //Instantiate the language object
        $this->objLanguage = $this->getObject('language', 'language');

        // Instantiate the class
        $this->dbAddDistances = $this->getObject('editform', 'libraryforms');
        $this->dbAddBookthesis = $this->getObject('bookthesis', 'libraryforms');
        $this->dbAddillperiodical = $this->getObject('illperiodical', 'libraryforms');
        $this->dbfeedback = $this->getObject('feedbk', 'libraryforms');
        $this->objUser = $this->getObject('User', 'security');
        // Get a local reference to the mail
        $this->objMail = $this->getObject('mailer', 'mail');
    }

//end of function

    public function dispatch($action) {

        //$action = $this->getParam('action');
        $this->setLayoutTemplate('editadd_tpl.php');

        //if($_POST){

        switch ($action) {

            default:
                return 'editadd_tpl.php';

            case 'addeditform':
                return $this->saveRecord();


            case 'addthesis':
                return $this->saveBookthesisRecord();


            case 'addperiodical':
                return $this->saveperiodicalRecord();


            case 'addfeedbk':
                return $this->submitmsg();

            case 'save_addedit':
                $this->saveRecord();
                return 'confirm_tpl.php';

            case 'save_book':
                $this->saveBookthesisRecord();
                return 'confirm_tpl.php';


            case 'save_periodical':
                $this->saveperiodicalRecord();
                return 'confirm_tpl.php';

            case 'save_fdbk':

                return $this->submitmsg();
        }// close for switch
    }

//end of function dispatch     

    /*
     * Public Method that checks if all required fields are filled
     * If fields are filled, and inserts data into db table, else returns error
     */

    public function saveRecord() {
        if (!$_POST) { // Check that user has submitted a page
            return $this->nextAction(NULL);
        }
        $surname = $this->getParam('surname');
        $initials = $this->getParam('initials');
        $title = $this->getParam('select_title');
        $studentno = $this->getParam('studentno');
        $postaladdress = $this->getParam('postal');
        $physicaladdress = $this->getParam('physical');
        $postalcode = $this->getParam('postalcode');
        $postalcode2 = $this->getParam('postalcode2');
        $telnoh = $this->getParam('tel');
        $telnow = $this->getParam('telw');
        $cell = $this->getParam('cell');
        $fax = $this->getParam('fax');
        $emailaddress = $this->getParam('emailaddress');
        $course = $this->getParam('course');
        $department = $this->getParam('department');
        $supervisor = $this->getParam('supervisor');
        $captcha = $this->getParam('editformrequest_captcha');

        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha') || empty($captcha)) {
            $msg[] = 'badcaptcha';
        }

        //if form entry is in corect or invavalid
        if (count($msg) > 0) {
            $this->setVarByRef('msg', $msg);
            $this->setVarByRef('insarr', $insarr);
            return 'editadd_tpl.php';
        }
        // insert into database
        $pid = $this->dbAddDistances->insertRecord($surname, $initials, $title, $studentno, $postaladdress,
                        $physicaladdress, $postalcode, $postalcode2, $telnoh,
                        $telnow, $cell, $fax, $emailaddress, $course, $department, $supervisor);


        // send email alert
        $subject = "New user registered";

        $this->sendEmailNotification($subject,
                $message = ' Surname: ' . $surname . '  ' . "\n" . ' Initials:  ' . $initials . '   ' . "\n" . ' Title: ' . $title . '   ' . "\n" . ' Student no: ' .
                $studentno . '   ' . "\n" . ' Postal Adress: ' . $postaladdress . '   ' . "\n" . ' Physical Address: ' . $physicaladdress . '   ' . "\n" . ' Postal Code: ' .
                $postalcode . '   ' . "\n" . ' Postal Code: ' . $postalcode2 . '   ' . "\n" . 'Tel home: ' . $telnoh . '   ' . "\n" . ' Tel work: ' .
                $telnow . '   ' . "\n" . ' Cell : ' . $cell . '   ' . "\n" . ' Fax: ' . $fax . '   ' . "\n" . ' Email address: ' . $emailaddress . '   ' . "\n" . ' Course: ' .
                $course . '   ' . "\n" . ' Department: ' . $department . '   ' . "\n" . ' Supervisor: ' . $supervisor);
    }

// end of Save Records */

    function saveBookthesisRecord() {

        $author = $this->getParam('aut');
        $title = $this->getParam('thesis_titles');
        $place = $this->getParam('thesis_place');
        $publisher = $this->getParam('thesis_publisher');
        $date = $this->getParam('year');
        $edition = $this->getParam('edition');
        $isbn = $this->getParam('ISBN');
        $series = $this->getParam('series');
        $copy = $this->getParam('photocopy');
        $titlepages = $this->getParam('titles');
        $pages = $this->getParam('pages');
        $thesis = $this->getParam('thesis');
        $name = $this->getParam('thesis_prof');
        $address = $this->getParam('thesis_address');
        $cell = $this->getParam('thesis_cell');
        $fax = $this->getParam('fax');
        $tel = $this->getParam('thesis_tel');
        $telw = $this->getParam('thesis_w');
        $emailaddress = $this->getParam('thesis_email');
        $entitynum = $this->getParam('entity');
        $studentno = $this->getParam('thesis_studentno');
        $course = $this->getParam('thesis_course');
        $local = $this->getParam('local');
        $postgrad = $this->getParam('postgrad');
        $captcha = $this->getParam('thesis_captcha');

//var_dump($_POST);die;
        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha') || empty($captcha)) {
            $erormsg [] = 'badcaptcha';
        }
        //if form entry is in corect or invavalid
        if (count($erormsg) > 0) {
            $this->setVarByRef('erormsg', $erormsg);
            $this->setVarByRef('insarr', $insarr);
            return 'editadd_tpl.php';
        }

        //insert into DB
        $id = $this->dbAddBookthesis->insertBookthesisRecord($author, $title, $place, $publisher, $date,
                        $edition, $isbn, $series, $copy, $titlepages, $pages, $thesis,
                        $name, $address, $cell, $fax, $tel, $telw, $emailaddress,
                        $entitynum, $studentno, $course, $local, $postgrad);

// after inserting into db send email alert
        $subject = "Book thesis request";
        $this->sendEmailNotification($subject,
                $message = ' Author: ' . $author . '  ' . "\n" . ' Title : ' . $title . '  ' . "\n" . ' Place: ' . $place . '  ' . "\n" . ' Publisher:  ' . $publisher . '  ' . "\n" . ' date: ' .
                $date . '   ' . "\n" . ' Edition: ' . $edition . '  ' . "\n" . ' ISBN: ' . $isbn . '  ' . "\n" . ' Series: ' . $series . '  ' . "\n" . ' Copy: ' .
                $copy . '  ' . "\n" . ' TItle:' . $titlepages . '  ' . "\n" . ' Pages: ' . $pages . '  ' . "\n" . ' Type of Thesis: ' . $thesis . '  ' . "\n" . ' Name: ' .
                $name . '  ' . "\n" . ' Address: ' . $address . '   ' . "\n" . ' Cell: ' . $cell . '   ' . ' Fax: ' . $fax . '   ' . "\n" . ' Tel(H): ' .
                $tel . '  ' . "\n" . ' Tel (W): ' . $telw . '  ' . "\n" . ' E-mail: ' . $emailaddress . '  ' . "\n" . ' Entity num: ' .
                $entitynum . '   ' . "\n" . ' Student no: ' . $studentno . '  ' . "\n" . ' Course: ' . $course . "\n" . ' User Identification: ' . $local . "\n" . ' User Level:  ' . $postgrad);
    }

// end of bookthesisrecord

    public function saveperiodicalRecord() {

        $titleperiodical = $this->getParam('title_periodical');
        $volume = $this->getParam('period_volume');
        $part = $this->getParam('period_part');
        $year = $this->getParam('period_year');
        $pages = $this->getParam('period_pages');
        $author = $this->getParam('period_author');
        $titlearticle = $this->getParam('periodical_titlearticle');
        $prof = $this->getParam('periodical_prof');
        $address = $this->getParam('periodical_address');
        $cell = $this->getParam('period_cell');
        $tell = $this->getParam('periodical_tell');
        $tellw = $this->getParam('periodical_w');
        $emailaddress = $this->getParam('periodicalemail');
        $entitynum = $this->getParam('periodical_entity');
        $studentno = $this->getParam('periodical_student');
        $course = $this->getParam('periodical_course');
        $overseas = $this->getParam('overseas');
        $undergrad = $this->getParam('undergrad');
        $captcha = $this->getParam('periodical_captcha');

        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha') || empty($captcha)) {
            $errormsg[] = 'badcaptcha';
        }
        //if form entry is in corect or invavalid
        if (count($errormsg) > 0) {
            $this->setVarByRef('$errormsg', $errormsg);
            $this->setVarByRef('insarr', $insarr);
            return 'editadd_tpl.php';
        }

        //insert the data into DB
        $id = $this->dbAddillperiodical->insertperiodicalRecord($titleperiodical, $volume, $part, $year, $pages,
                        $author, $titlearticle, $prof, $address, $cell, $tell,
                        $tellw, $emailaddress, $entitynum, $studentno, $course, $overseas, $undergrad);

        $subject = "Periodical Book Request";
        $this->sendEmailNotification($subject,
                $message = ' Title Periodical:   ' . $titleperiodical . '   ' . "\n" . ' Volume:   ' . $volume . '   ' . "\n" . ' Part:   ' . $part . '   ' . "\n" . ' Year:   ' .
                $year . '   ' . "\n" . ' Pages:   ' . $pages . '   ' . "\n" . 'Author   ' . $author . '   ' . "\n" . ' Title Article   ' . $titlearticle . '   ' . "\n" . ' Prof:   ' .
                $prof . '   ' . "\n" . 'Address:   ' . $address . '  ' . "\n" . 'Cell: ' . $cell . '   ' . "\n" . ' Tel: ' . $tell . '   ' . "\n" . ' Tell (W) ' .
                $tellw . '  ' . "\n" . ' Email Address:   ' . $emailaddress . '   ' . "\n" . ' Entity num:   ' . $entitynum . '   ' . "\n" . ' Student No:   ' .
                $studentno . '  ' . "\n" . ' Course   ' . $course . "\n" . ' Student Identification: ' . $overseas . "\n" . ' Level of User: ' . $undergrad);
    }

// end of periodical method

    public function submitmsg() {

        //get parametters
        $name = $this->getParam('feedback_name');
        $email = $this->getParam('fbkemail');
        $msg = $this->getParam('msgbox');
        $captcha = $this->getParam('feedback_captcha');

        // echo md5(strtoupper($captcha)).' against '.$this->getParam('captcha');
        // die();
        $errormsg[] = array();

        if ((md5(strtoupper($captcha)) != $this->getParam('captcha'))) {
            $errormsg[] = 'badcaptcha';
        }

        //if form entry is in corect or invavalid
        if (count($errormsg) > 0) {
            $this->setVarByRef('errormsg', $errormsg);
            $this->setVarByRef('insarr', $insarr);

            return 'editadd_tpl.php';
        }



        //insert the data into DB
        $id = $this->dbfeedback->insertmsgRecord($name, $email, $msg);

        // send email alert
        $subject = "Feed Back";

        $this->sendEmailNotification($subject, $message = ' Name: ' . $name . '       ' . "\n" . ' Email Adress: ' . $email . '   ' . "\n" . ' Feed Back Message: ' . $msg);
        return 'fdbkconfirm_tpl.php';
    }

// end of Submitmsg

    public function sendEmailNotification($subject, $message) {

        $objMail = $this->getObject('mailer', 'mail');
        //send to multiple addressed   
        $list = array("pmalinga@uwc.ac.za", "arieluwc.uwc.ac.za", "library@uwc.ac.za");
        $objMail->to = ($list);
        // specify whom the email is coming from
        $objMail->from = "no-reply@uwc.ac.za";
        $objMail->from = "no-reply";
        //Give email subject and body
        //$objMail->subject=$emaill;
        $objMail->subject = $subject;
        $objMail->body = $message;
        $objMail->AltBody = $message;
        // send email
        $objMail->send();
    }

// end of notification email

    public function requiresLogin() {
        return FALSE;
    }

// end function
}

// end of all



