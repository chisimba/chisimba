<?php
/*
* This is the controller class for the rmfhe(Research Information Management for Higher Education
* Module
*
*/
//security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
/**
 *
 * @package rimfhe
 * @version 0.1
 * @Copyright june 2009
 * @author Ram
 */

class rimfhe extends controller
{
    /*
    *Declare class properties
    *variables to hold datta
    */
    public $objLanguage;
    public $objUrl;
    public $dbInsert;
    public $objStaffRegistration;
    public $objEntireBook;
    public $objChapterInBook;
    public $objJournals;
    public $objDoctoralStudents;
    public $objMastersStudents;
    public $formElements;
    public $preLoginInterface;

    /**
*
Public fuction to instantiate required  objestc
*
*/
    public function init()
    {
        //instantiate the language Object
        $this->formElements =$this->getObject('formhelperclass', 'rimfhe');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUrl = $this->getObject('url', 'strings');
        $this->objStaffMember =$this->getObject('dbstaffmember', 'rimfhe');
        $this->objAccreditedJournal= $this->getObject('dbaccreditedjournal', 'rimfhe');
        $this->objEntireBook= $this->getObject('dbentirebook', 'rimfhe');
        $this->objChapterInBook= $this->getObject('dbchapterinbook', 'rimfhe');
        $this->objDoctoralStudents= $this->getObject('dbdoctoralstudents', 'rimfhe');
        $this->objMastersStudents= $this->getObject('dbmastersstudents', 'rimfhe');
        $this->objDBJournal= $this->getObject('dbrimfhe_journal', 'rimfhe');
        $this->preLoginInterface= $this->getObject('loginInterface', 'security');
    }//end init

    /*public function requiresLogin($action)
    {

    $required = array('staff member registarion');
    if (in_array($action, $required)) {
    return TRUE;
    } else {
    return FALSE;
    }
    }*/

    public function dispatch()
    {
        $action =$this ->getParam('action');

        $this->setLayoutTemplate('default_layout_tpl.php');
        //if form is submitted
        if($_POST){
            //$action =$this ->getParam('action');
            //switch statment for find action

            switch($action){
                case 'registerstaff':
                    return $this->AddStaffMember();

                case 'accreditedjournal':
                    return $this-> addAccretedJournal();
                case 'entirebook';
                return $this->addEntireBook();

                case 'chapterinbook';
                return $this->addChapterInBook();

                case 'doctoralstudents';
                return $this->addDoctoralStudents();

                case 'mastersstudents';
                return $this->addMastersStudents();
                case 'staffdetails':
                    $arrDisplayStaff = $this->objStaffMember->dispalyStaffDetails();
                    $this->setVarByRef('arrDisplayStaff', $arrDisplayStaff);
                    return 'displaystaff_tpl.php';
            }//end switch
        }
        //Display Landing page and forms
        else{
            switch($action)
            {
                default:
                    //$this->setLayoutTemplate('prelogin_layout_tpl.php');
                    //return 'rimfhehomepage_tpl.php';
                    return 'rimfhehomepage_tpl.php';

                case 'Home':
                    return 'rimfhehomepage_tpl.php';

                case 'Staff Member Registarion':
                    return 'staffregistration_tpl.php';

                case 'DOE Accredoted Journal Articles':
                    return 'accreditedjournal_tpl.php';

                case 'Accredted Journal Articles Info':
                    $arrJournal=array();
                    $arrJournal = $this->objAccreditedJournal->getAllJournalAuthor();
                    $this->setVarByRef('arrJournal', $arrJournal);
                    return 'displayaccrjournal_tpl.php';

                case 'Edit Journal Articles':
                    //Get id
                    $id = $this->getParam('id');
                    $this->setVar('mode', 'edit');
                    $arrEditThis= $this->objAccreditedJournal->getRow('id', $id);
                    $this->setVarByRef('arrEdit', $arrEditThis);
                    return 'accreditedjournal_tpl.php';

                case 'deletejournalarticle':
                    $deleteRowId = $this->getParam('id');
                    $arrDeletedRow = $this->objAccreditedJournal->getRow('id', $deleteRowId);
                    $rowToDelete = $arrDeletedRow['articletitle'];
                    $this->objAccreditedJournal->delete('id', $deleteRowId);
                    $title = "<strong>$rowToDelete</strong>";
                    $rep = array('TITLE' => $title);
                    return$this->nextAction('Accredted Journal Articles Info',array('deletecomment'=> $this->objLanguage->code2Txt('mod_notify_delete', 'rimfhe', $rep)));

                case 'Entire Book/Monogragh':
                    return 'entirebook_tpl.php';

                case 'Edit Book':
                    //Get id
                    $id = $this->getParam('id');
                    $this->setVar('mode', 'edit');
                    $arrEditThis= $this->objEntireBook->getRow('id', $id);
                    $this->setVarByRef('arrEdit', $arrEditThis);
                    return 'entirebook_tpl.php';

                case 'deleteentirebook':
                    $deleteRowId = $this->getParam('id');
                    $arrDeletedRow = $this->objEntireBook->getRow('id', $deleteRowId);
                    $rowToDelete = $arrDeletedRow['booktitle'];
                    $this->objEntireBook->delete('id', $deleteRowId);
                    $title = "<strong>$rowToDelete</strong>";
                    $rep = array('TITLE' => $title);
                    return$this->nextAction('Entire Book/Monogragh Details',array('deletecomment'=> $this->objLanguage->code2Txt('mod_notify_delete', 'rimfhe', $rep)));

                case 'Entire Book/Monogragh Details':
                    $arrDisplayBooks = $this->objEntireBook->getAllEntireBooks();
                    $this->setVarByRef('arrDisplayBooks', $arrDisplayBooks);
                    return 'displayentirebook_tpl.php';

                case 'Chapter In a Book':
                    return 'chapterinbook_tpl.php';

                case 'Edit Chapter In Book':
                    //Get id
                    $id = $this->getParam('id');
                    $this->setVar('mode', 'edit');
                    $arrEditThis= $this->objChapterInBook->getRow('id', $id);
                    $this->setVarByRef('arrEdit', $arrEditThis);
                    return 'chapterinbook_tpl.php';

                case 'deletechapterinbook':
                    $deleteRowId = $this->getParam('id');
                    $arrDeletedRow = $this->objChapterInBook->getRow('id', $deleteRowId);
                    $rowToDelete = $arrDeletedRow['chaptertitle'];
                    $this->objChapterInBook->delete('id', $deleteRowId);
                    $title = "<strong>$rowToDelete</strong>";
                    $rep = array('TITLE' => $title);
                    return$this->nextAction('Chapter In a Book Details',array('deletecomment'=> $this->objLanguage->code2Txt('mod_notify_delete', 'rimfhe', $rep)));

                case 'Chapter In a Book Details':
                    $arrDisplayBooks = $this->objChapterInBook->getAllChapterInBooks();
                    $this->setVarByRef('arrDisplayBooks', $arrDisplayBooks);
                    return 'displaychapterinbook_tpl.php';

                case 'Graduating Doctoral Student':
                    return 'doctoralstudents_tpl.php';

                case 'Edit Graduating Doctoral Student':
                    //Get id
                    $id = $this->getParam('id');
                    $this->setVar('mode', 'edit');
                    $arrEditThis= $this->objDoctoralStudents->getRow('id', $id);
                    $this->setVarByRef('arrEdit', $arrEditThis);
                    return 'doctoralstudents_tpl.php';

                case 'deletedoctoralstudents':
                    $deleteRowId = $this->getParam('id');
                    $arrDeletedRow = $this->objDoctoralStudents->getRow('id', $deleteRowId);
                    $rowToDelete = $arrDeletedRow['thesistitle'];
                    $this->objDoctoralStudents->delete('id', $deleteRowId);
                    $title = "<strong>$rowToDelete</strong>";
                    $rep = array('TITLE' => $title);
                    return$this->nextAction('Graduating Doctoral Student Info',array('deletecomment'=> $this->objLanguage->code2Txt('mod_notify_delete', 'rimfhe', $rep)));

                case 'Graduating Doctoral Student Info':
                    $arrDisplayDoctoral= $this->objDoctoralStudents->getAllDoctoralStudents();
                    $this->setVarByRef('arrDisplayDoctoral', $arrDisplayDoctoral);
                    return 'displaydoctoralstudents_tpl.php';

                case 'Graduating Doctoral Students Summary':
                    $arrDeptSummary= $this->objDoctoralStudents->displayByDepartment();
                    $arrFacultySummary= $this->objDoctoralStudents->displayByFaculty();
                    $this->setVarByRef('arrDeptSummary', $arrDeptSummary);
                    $this->setVarByRef('arrFacultySummary', $arrFacultySummary);
                    $totalCount = $this->objDoctoralStudents->totalDoctoralStudents();
                    $this->setVar('totalCount', $totalCount);
                    return 'summarydoctoralstudents_tpl.php';

                case 'Graduating Masters Student':
                    return 'mastersstudents_tpl.php';

                case 'Edit Graduating Masters Student':
                    //Get id
                    $id = $this->getParam('id');
                    $this->setVar('mode', 'edit');
                    $arrEditThis= $this->objMastersStudents->getRow('id', $id);
                    $this->setVarByRef('arrEdit', $arrEditThis);
                    return 'mastersstudents_tpl.php';

                case 'deletemastersstudent':
                    $deleteRowId = $this->getParam('id');
                    $arrDeletedRow = $this->objMastersStudents->getRow('id', $deleteRowId);
                    $rowToDelete = $arrDeletedRow['thesistitle'];
                    $this->objMastersStudents->delete('id', $deleteRowId);
                    $title = "<strong>$rowToDelete</strong>";
                    $rep = array('TITLE' => $title);
                    return$this->nextAction('Graduating Masters Student Info',array('deletecomment'=> $this->objLanguage->code2Txt('mod_notify_delete', 'rimfhe', $rep)));

                case 'Graduating Masters Student Info':
                    $arrDisplayMasters= $this->objMastersStudents->getAllMastersStudents();
                    $this->setVarByRef('arrDisplayMasters', $arrDisplayMasters);
                    return 'displaymastersstudents_tpl.php';

                case 'Graduating Masters Students Summary':
                    $arrDeptSummary= $this->objMastersStudents->displayByDepartment();
                    $arrFacultySummary= $this->objMastersStudents->displayByFaculty();
                    $this->setVarByRef('arrDeptSummary', $arrDeptSummary);
                    $this->setVarByRef('arrFacultySummary', $arrFacultySummary);
                    $totalCount = $this->objMastersStudents->totalMastersStudents();
                    $this->setVar('totalCount', $totalCount);
                    return 'summarymastersstudents_tpl.php';

                case 'Registered Staff Member':
                    $arrDisplayStaff = $this->objStaffMember->displayStaffDetails();
                    $this->setVarByRef('arrDisplayStaff', $arrDisplayStaff);
                    return 'displaystaff_tpl.php';

                case 'General Summary':
                    $totalArticles = $this->objAccreditedJournal->totalJournalArticle();
                    $totalBooks = $this->objEntireBook->totalBooks();
                    $totalChapterInBook = $this->objChapterInBook->totalChapterInBook();
                    $totalDoctoralStudents = $this->objDoctoralStudents->totalDoctoralStudents();
                    $totalMastersStudents = $this->objMastersStudents->totalMastersStudents();
                    $this->setVarByRef('totalArticles', $totalArticles);
                    $this->setVarByRef('totalBooks', $totalBooks);
                    $this->setVarByRef('totalChapterInBook', $totalChapterInBook);
                    $this->setVarByRef('totalDoctoralStudents', $totalDoctoralStudents);
                    $this->setVarByRef('totalMastersStudents', $totalMastersStudents);
                    return 'universitysummary_tpl.php';

                case 'ajaxgetalljournals':
                    $this->setLayoutTemplate(NULL);
                    $this->setVar('pageSuppressToolbar', TRUE);
                    $this->setVar('pageSuppressBanner', TRUE);
                    $this->setVar('pageSuppressSearch', TRUE);
                    $this->setVar('suppressFooter', TRUE);
                    //Get journal, journcatid
                    //$journal = $this->getParam('journal');
                    //$journcatid = $this->getParam('journcatid');   
                    error_log(var_export($_REQUEST, true));
                    
                    $myJournals= $this->objDBJournal->jsongetAllJournals($start,$limit);
                    echo $myJournals;
                    exit(0);
                    break;
                case 'jsongetjournals':
                    //query coming from the ext lib. combobox auto complete. The post var is called query.
                    if (isset($_GET['query'])){
                     $journal = $_GET['query'];
                     $start =  $_GET['start'];
                     $limit =  $_GET['limit'];
                    }else{
                     $journal = $this->getParam('query');
                     $start = $this->getParam('start');
                     $limit = $this->getParam('limit');
                    }
                    if (isset($_GET['journalcat'])){
                     $journid = $_GET['journalcat'];
                    }else{
                     $journid = $this->getParam('journalcat');
                    }
                    $this->setLayoutTemplate(NULL);
                    $this->setVar('pageSuppressToolbar', TRUE);
                    $this->setVar('pageSuppressBanner', TRUE);
                    $this->setVar('pageSuppressSearch', TRUE);
                    $this->setVar('suppressFooter', TRUE);
                    //Get journal, journcatid
                    //$journalcat = $this->getParam('journalcat');
                    $myJournals= $this->objDBJournal->jsongetJournals($journid,$journal, $start, $limit);
                    echo $myJournals;
                    exit(0);
                    break;
                    
                case 'confirnregistration':
                    return 'staffregistrationconfirm_tpl.php';

                case 'generalconfirmation':
                    return 'generalconfirm_tpl.php';

                case 'recordexists':
                    return 'recordexists_tpl.php';
            }//end switch
        }
    }//end dispatch

    /*
    *Public Method that checks if all required fields are filled
    *If fiels are fiels, it inserts data into db table, else returns error
    */
    public function AddStaffMember()
    {
        $captcha = $this->getParam('request_captcha');
        $surname = $this->getParam('surname');
        $initials= $this->getParam('initials');
        $firstname= $this->getParam('firstname');
        $droptitle= $this->getParam('title');
        $rank= $this->getParam('rank');
        $appointment= $this->getParam('appointment');
        $dept= $this->getParam('department');
        $faculty= $this->getParam('faculty');
        $staffNumber= $this->getParam('staffNumber');
        $email= $this->getParam('email');
        $confirmemail=$this->getParam('confirmemail');

        // Create an array of fields that cannot be empty
        $checkFields = array($captcha, 	$surname, $initials, $firstname, $rank, $dept, $faculty, $staffNumber, $email, $confirmemail
        );

        // Create an array to hold information about problems
        $problems = array();

        // Check that all required field are not empty

        //ckeck user name
        if(empty($surname)){
            $problems[] = 'nosurname';
        }

        if(empty($initials)){
            $problems[] = 'noinittials';
        }

        if(empty($firstname)){
            $problems[] ='nofirstname';
        }

        if(empty($rank)){
            $problems[] = 'norank';
        }

        if(empty($dept)){
            $problems[] ='nodepatment';
        }

        if(empty($faculty)){
            $problems[] ='nofaculty' ;
        }
        if(empty($staffNumber)){
            $problems[] = 'nostaffnumber';
        }

        //check if email is invalid and/or empty
        if (!($this->objUrl->isValidFormedEmailAddress($email) ||$this->objUrl->isValidFormedEmailAddress($confirmemail) ))
        {
            $problems[] = 'emailnotvalid';
        }
        elseif(empty($email)){
            $problems[] = 'noemail';
        }
        elseif(empty($confirmemail)){
            $problems[] = 'norepeatemail';
        }
        elseif($email!=$confirmemail){
            $problems[] = 'emailnotmatch';
        }

        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha')){
            $problems[] = 'captchadoesntmatch';
        }
        //if form entry is in corect or invavalid
        if (count($problems) > 0) {
            $this->setVar('mode', 'fixerror');
            $this->setVarByRef('problems', $problems);
            return 'staffregistration_tpl.php';
        }
        else {
            $this->nextAction('confirnregistration');
            return $this->objStaffMember->addStaffDetails();
        }

    }//end addStaffMember

    public function addAccretedJournal()
    {
        $captcha = $this->getParam('request_captcha');
        $journalname = $this->getParam('journalname');
        $category= $this->getParam('category');
        $articletitle= $this->getParam('articletitle');
        $publicationyear= $this->getParam('publicationyear');
        $volume= $this->getParam('volume');
        $firstpage= $this->getParam('firstpage');
        $lastpage= $this->getParam('lastpage');
        $author1= $this->getParam('author1');
        $author2= $this->getParam('author2');
        $author3= $this->getParam('author3');
        $author4= $this->getParam('author4');

        // Create an array of fields that cannot be empty
        $checkFields = array($captcha,$journalname, $articletitle, $publicationyear, $volume, $firstpage, $lastpage, $author1,$author2, $author3,$author4);

        // Create an array to hold information about problems
        $problems = array();

        //ckeck user name
        if(empty($journalname)){
            $problems[] = 'nojournalname';
        }

        if(empty($articletitle)){
            $problems[] = 'noarticletitle';
        }

        if(empty($publicationyear)){
            $problems[] ='nopublicationyr';
        }

        if(empty($volume)){
            $problems[] = 'volume';
        }

        if(empty($firstpage)){
            $problems[] ='nofirstpage';
        }

        if(empty($lastpage)){
            $problems[] ='nolastpage';
        }

        if(empty($author1) && empty($author2) && empty($author3) && empty($author4)){
            $problems[] = 'noauthor';
        }

        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha')){
            $problems[] = 'captchadoesntmatch';
        }
        //if form entry is in corect or invavalid
        if (count($problems) > 0) {
            $this->setVar('mode', 'fixerror');
            $this->setVarByRef('problems', $problems);
            return 'accreditedjournal_tpl.php';
        }
        else {
            $editMode =$this ->getParam('editmode');
            if($editMode == 'update'){
                $this->objAccreditedJournal->accreditedJournal();
                $title = "<strong>$articletitle</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Accredted Journal Articles Info',array('comment'=> $this->objLanguage->code2Txt('mod_notify_update', 'rimfhe', $rep)));
            }
            //Return Error if Book with same title exist in data base
            if($this->objAccreditedJournal->accreditedJournal() == FALSE){
                $title = "<strong>$articletitle</strong>";
                $rep = array('TITLE' => $title);
                $this->setVar('title',$this->objLanguage->code2Txt('mod_recordexists_data', 'rimfhe', $rep));
                return 'recordexists_tpl.php';
            }
            else{
                //Insert into data base
                $this->objAccreditedJournal->accreditedJournal();
                $title = "<strong>$articletitle</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Accredted Journal Articles Info',array('comment'=> $this->objLanguage->code2Txt('mod_notify_add', 'rimfhe' ,$rep)));
            }
        }
    }//end addAccretedJournal
    public function addEntireBook()
    {
        $captcha = $this->getParam('request_captcha');
        $bookname = $this->getParam('bookname');
        $isbnnumber= $this->getParam('isbnnumber');
        $publishinghouse= $this->getParam('publishinghouse');
        $firstpage= $this->getParam('firstpage');
        $lastpage= $this->getParam('lastpage');
        $author1= $this->getParam('author1');
        $author2= $this->getParam('author2');
        $author3= $this->getParam('author3');
        $author4= $this->getParam('author4');

        // Create an array of fields that cannot be empty
        $checkFields = array($captcha,$bookname, $isbnnumber, $publishinghouse, $firstpage, $lastpage, $author1,$author2, $author3,$author4);

        // Create an array to hold information about problems
        $problems = array();

        //ckeck user name
        if(empty($bookname)){
            $problems[] = 'nobookname';
        }

        if(empty($isbnnumber)){
            $problems[] = 'noisbnnumber';
        }

        if(empty($publishinghouse)){
            $problems[] ='nopublishinghouse' ;
        }

        if(empty($firstpage)){
            $problems[] ='nofirstpage' ;
        }

        if(empty($lastpage)){
            $problems[] ='nolastpage' ;
        }

        if(empty($author1) && empty($author2) && empty($author3) && empty($author4)){
            $problems[] = 'noauthor';
        }

        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha')){
            $problems[] = 'captchadoesntmatch';
        }
        //if form entry is in corect or invavalid
        if (count($problems) > 0) {
            $this->setVar('mode', 'fixerror');
            $this->setVarByRef('problems', $problems);
            return 'entirebook_tpl.php';
        }
        else {
            $editMode =$this ->getParam('editmode');
            if($editMode == 'update'){
                $this->objEntireBook->entireBook();
                $title = "<strong>$bookname</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Entire Book/Monogragh Details',array('comment'=> $this->objLanguage->code2Txt('mod_notify_update', 'rimfhe', $rep)));
            }
            //Return Error if Book with same title exist in data base
            if($this->objEntireBook->entireBook() == FALSE){
                $title = "<strong>$bookname</strong>";
                $rep = array('TITLE' => $title);
                $this->setVar('title',$this->objLanguage->code2Txt('mod_recordexists_data', 'rimfhe', $rep));
                return 'recordexists_tpl.php';
            }
            else{
                //Insert into data base
                $this->objEntireBook->entireBook();
                $title = "<strong>$bookname</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Entire Book/Monogragh Details',array('comment'=> $this->objLanguage->code2Txt('mod_notify_add', 'rimfhe', $rep)));
            }
        }

    }//end entireBook



    public function addChapterInBook()
    {
        $captcha = $this->getParam('request_captcha');
        $bookname = $this->getParam('bookname');
        $isbnnumber= $this->getParam('isbnnumber');
        $editors= $this->getParam('editors');
        $publishinghouse= $this->getParam('publishinghouse');
        $chaptertile= $this->getParam('chaptertile');
        $firstpage= $this->getParam('firstpage');
        $lastpage= $this->getParam('lastpage');
        $author1= $this->getParam('author1');
        $author2= $this->getParam('author2');
        $author3= $this->getParam('author3');
        $author4= $this->getParam('author4');

        // Create an array of fields that cannot be empty
        $checkFields = array($captcha,$bookname, $isbnnumber,$editors, $publishinghouse, $chaptertile, $firstpage, $lastpage, $author1,$author2, $author3,$author4);

        // Create an array to hold information about problems
        $problems = array();

        //ckeck user name
        if(empty($bookname)){
            $problems[] = 'nobookname';
        }

        if(empty($isbnnumber)){
            $problems[] = 'noisbnnumber';
        }
        if(empty($editors)){
            $problems[] = 'noeditors';
        }
        if(empty($publishinghouse)){
            $problems[] ='nopublishinghouse' ;
        }
        if(empty($chaptertile)){
            $problems[] ='nochaptertile' ;
        }
        if(empty($firstpage)){
            $problems[] ='nofirstpage' ;
        }

        if(empty($lastpage)){
            $problems[] ='nolastpage' ;
        }

        if(empty($author1) && empty($author2) && empty($author3) && empty($author4)){
            $problems[] = 'noauthor';
        }
        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha')){
            $problems[] = 'captchadoesntmatch';
        }
        //if form entry is in corect or invavalid
        if (count($problems) > 0) {
            $this->setVar('mode', 'fixerror');
            $this->setVarByRef('problems', $problems);
            return 'chapterinbook_tpl.php';
        }
        else {
            $editMode =$this ->getParam('editmode');
            if($editMode == 'update'){
                $this->objChapterInBook->chaperterInBook();
                $title = "<strong>$bookname</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Chapter In a Book Details',array('comment'=> $this->objLanguage->code2Txt('mod_notify_update', 'rimfhe', $rep)));
            }
            //Return Error if Book with same title exist in data base
            if($this->objChapterInBook->chaperterInBook() == FALSE){
                $title = "<strong>$chaptertile</strong>";
                $rep = array('TITLE' => $title);
                $this->setVar('title',$this->objLanguage->code2Txt('mod_recordexists_data', 'rimfhe', $rep));
                return 'recordexists_tpl.php';
            }
            else{
                //Insert into data base
                $this->objChapterInBook->chaperterInBook();
                $title = "<strong>$chaptertile</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Chapter In a Book Details',array('comment'=> $this->objLanguage->code2Txt('mod_notify_add', 'rimfhe' ,$rep)));
            }
        }

    }//end chapterInBook

    public function addDoctoralStudents()
    {
        $captcha = $this->getParam('request_captcha');
        $surname = $this->getParam('surname');
        $initials= $this->getParam('initials');
        $firstname= $this->getParam('firstname');
        $gender= $this->getParam('gender');
        $studnumber= $this->getParam('studnumber');
        $dept= $this->getParam('department');
        $faculty= $this->getParam('faculty');
        $thesis= $this->getParam('thesis');
        $supervisor1= $this->getParam('supervisor1');
        $supervisor2= $this->getParam('supervisor2');
        $supervisor3= $this->getParam('supervisor3');
        $degree= $this->getParam('degree');

        // Create an array of fields that cannot be empty
        $checkFields = array(
        $captcha, $surname, $initials, $firstname, $gender, $studnumber, $dept, $faculty, $thesis, $supervisor1, $supervisor2, $supervisor3, $degree);

        // Create an array to hold information about problems
        $problems = array();

        if(empty($surname)){
            $problems[] = 'nosurname';
        }

        if(empty($initials)){
            $problems[] = 'noinittials';
        }

        if(empty($firstname)){
            $problems[] ='nofirstname' ;
        }

        if(empty($studnumber)){
            $problems[] = 'nostudnumber';
        }

        if(empty($dept)){
            $problems[] ='nodepatment' ;
        }

        if(empty($faculty)){
            $problems[] ='nofaculty' ;
        }
        if(empty($thesis)){
            $problems[] = 'nothesis';
        }

        if(empty($supervisor1) && empty($supervisor2) && empty($supervisor3)){
            $problems[] = 'nosupervisor';
        }
        if(empty($degree)){
            $problems[] = 'nodegree';
        }

        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha')){
            $problems[] = 'captchadoesntmatch';
        }
        //if form entry is in corect or invavalid
        if (count($problems) > 0) {
            $this->setVar('mode', 'fixerror');
            $this->setVarByRef('problems', $problems);
            return 'doctoralstudents_tpl.php';
        }
        else {
            $editMode =$this ->getParam('editmode');
            if($editMode == 'update'){
                $this->objDoctoralStudents->updateDoctoralStudents();
                $title = "<strong>$thesis</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Graduating Doctoral Student Info',array('comment'=> $this->objLanguage->code2Txt('mod_notify_update', 'rimfhe', $rep)));
            }
            //Return Error if Book with same title exist in data base
            if($this->objDoctoralStudents->doctoralStudents() == FALSE){
                $title = "<strong>$thesis</strong>";
                $rep = array('TITLE' => $title);
                $this->setVar('title',$this->objLanguage->code2Txt('mod_recordexists_data', 'rimfhe', $rep));
                return 'recordexists_tpl.php';
            }
            else{
                //Insert into data base
                $this->objDoctoralStudents->doctoralStudents();
                $title = "<strong>$thesis</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Graduating Doctoral Student Info',array('comment'=> $this->objLanguage->code2Txt('mod_notify_add', 'rimfhe', $rep)));
            }
        }

    }//end addDoctorialStudents
    public function addMastersStudents()
    {
        $captcha = $this->getParam('request_captcha');
        $surname = $this->getParam('surname');
        $initials= $this->getParam('initials');
        $firstname= $this->getParam('firstname');
        $gender= $this->getParam('gender');
        $studnumber= $this->getParam('studnumber');
        $dept= $this->getParam('department');
        $faculty= $this->getParam('faculty');
        $thesis= $this->getParam('thesis');
        $supervisor1= $this->getParam('supervisor1');
        $supervisor2= $this->getParam('supervisor2');
        $supervisor3= $this->getParam('supervisor3');
        $degree= $this->getParam('degree');

        // Create an array of fields that cannot be empty
        $checkFields = array(
        $captcha, $surname, $initials, $firstname, $gender, $studnumber, $dept, $faculty, $thesis, $supervisor1, $supervisor2, $supervisor3, $degree);

        // Create an array to hold information about problems
        $problems = array();

        if(empty($surname)){
            $problems[] = 'nosurname';
        }

        if(empty($initials)){
            $problems[] = 'noinittials';
        }

        if(empty($firstname)){
            $problems[] ='nofirstname' ;
        }

        if(empty($studnumber)){
            $problems[] = 'nostudnumber';
        }

        if(empty($dept)){
            $problems[] ='nodepatment' ;
        }

        if(empty($faculty)){
            $problems[] ='nofaculty' ;
        }
        if(empty($thesis)){
            $problems[] = 'nothesis';
        }

        if(empty($supervisor1) || empty($supervisor2) ||empty($supervisor3)){
            $problems[] = 'nosupervisor';
        }
        if(empty($degree)){
            $problems[] = 'nodegree';
        }

        // Check whether user matched captcha
        if (md5(strtoupper($captcha)) != $this->getParam('captcha')){
            $problems[] = 'captchadoesntmatch';
        }
        //if form entry is in corect or invavalid
        if (count($problems) > 0) {
            $this->setVar('mode', 'fixerror');
            $this->setVarByRef('problems', $problems);
            return 'mastersstudents_tpl.php';
        }
        else {
            $editMode =$this ->getParam('editmode');
            if($editMode == 'update'){
                $this->objMastersStudents->mastersStudents();
                $title = "<strong>$thesis</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Graduating Masters Student Info',array('comment'=> $this->objLanguage->code2Txt('mod_notify_update', 'rimfhe', $rep)));
            }
            //Return Error if Book with same title exist in data base
            if($this->objMastersStudents->mastersStudents() == FALSE){
                $title = "<strong>$thesis</strong>";
                $rep = array('TITLE' => $title);
                $this->setVar('title',$this->objLanguage->code2Txt('mod_recordexists_data', 'rimfhe', $rep));
                return 'recordexists_tpl.php';
            }
            else{
                //Insert into data base
                $this->objMastersStudents->mastersStudents();
                $title = "<strong>$thesis</strong>";
                $rep = array('TITLE' => $title);
                return$this->nextAction('Graduating Masters Student Info',array('comment'=> $this->objLanguage->code2Txt('mod_notify_add', 'rimfhe', $rep)));
            }
        }

    }//end addMastersStudents
    /*
    *Private function that is called  by other Mothods
    *Walks through the $checkFields array and returns true if all field have dat
    */
    private function checkFields($checkFields)
    {
        $allFieldsOk = TRUE;
        foreach($checkFields as $field) {
            if (empty($field)) {
                $allFieldsOk = FALSE;
            }
        }
        return $allFieldsOk;
    }//end checkFields

    /**
     * Method to display the error messages/problems in the user registration
     * @getParam string $problem Problem Code
     * @return string Explanation of Problem
     */
    protected function explainProblemsInfo($problem)
    {
        switch ($problem) {
            case 'nosurname':
                return 'Please enter the Surname.';
            case 'noinittials':
                return 'Please enter the Initial(s).';
            case 'nofirstname':
                return 'Please enter the Firstname.';
            case 'norank':
                return 'Please enter the Rank';
            case 'nodepatment':
                return 'Please enter the Department/Schoool. ';
            case 'nofaculty':
                return 'Please enter the Faculty.';
            case 'nostaffnumber':
                return 'Please enter the Staff Number.';
            case 'emailnotvalid':
                return 'Please enter a valid email.';
            case 'noemail':
                return 'Please enter a email address.';
            case 'norepeatemail':
                return 'Please enter repeat the email address.';
            case 'emailnotmatch':
                return 'The email addresses you entered do not match.';
                //DOE Accredited Journal Articles
            case 'nojournalname':
                return 'You have not entered the name of the Journal.';
            case 'noarticletitle':
                return 'You have not entered the title of the Journal.';
            case 'nopublicationyr':
                return 'You have not entered the Year of Publication.';
            case 'volume':
                return 'You have not entered the Journal\'s Volume.';
            case 'nofirstpage':
                return 'You have not entered the Chapter/Article\'s First Page Number.';
            case 'nolastpage':
                return 'You have not entered the Chapter/Article\'s Last Page Number.';
            case 'noauthor':
                return 'You must enter atleast one Author\'s Name.';
                //Entire Book
            case 'nobookname':
                return 'You have not entered the Title of The Book.';
            case 'noisbnnumber':
                return 'You have not entered the ISBN Number.';
            case 'nopublishinghouse':
                return 'You have not Entered the Publishing House.';
                // chapter in a Book
            case 'noeditors':
                return 'You have not entered the Editors of The Book.';
            case 'nochaptertile':
                return 'You have not entered the Title Of the Chapter.';
                // Doctoral Students
            case 'nostudnumber':
                return 'You hhave not entered the Student Number.';
            case 'nothesis':
                return 'You have not entered the Title of the Thesis.';
            case 'nosupervisor':
                return 'You must enter atleast one Supervisor.';
            case 'nodegree':
                return 'You have not entered the Degree.';
                //Captcha Error message
            case 'captchadoesntmatch':
                return 'You have entered an incorrect image code';
        }
    }


}//end iimfhe
?>
