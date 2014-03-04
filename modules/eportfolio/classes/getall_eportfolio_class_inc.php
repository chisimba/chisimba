<?php
/* ----------- getall_Eportfolio class extends object------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for getting eportfolio content
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape
 */
class getall_Eportfolio extends object
{
    /**
     *
     * Intialiser for the getall_Eportfolio controller
     * @access public
     *
     */
    public $userPid;
    public function init() 
    {
        // Get the DB object.
        $this->objDbAddressList = &$this->getObject('dbeportfolio_address', 'eportfolio');
        $this->objDbCategorytypeList = &$this->getObject('dbeportfolio_categorytypes', 'eportfolio');
        $this->objDbContactList = &$this->getObject('dbeportfolio_contact', 'eportfolio');
        $this->objDbEmailList = &$this->getObject('dbeportfolio_email', 'eportfolio');
        $this->objDbDemographicsList = &$this->getObject('dbeportfolio_demographics', 'eportfolio');
        $this->objDbActivityList = &$this->getObject('dbeportfolio_activity', 'eportfolio');
        $this->objDbAffiliationList = &$this->getObject('dbeportfolio_affiliation', 'eportfolio');
        $this->objDbTranscriptList = &$this->getObject('dbeportfolio_transcript', 'eportfolio');
        $this->objDbQclList = &$this->getObject('dbeportfolio_qcl', 'eportfolio');
        $this->objDbGoalsList = &$this->getObject('dbeportfolio_goals', 'eportfolio');
        $this->objDbComment = &$this->getObject('dbeportfolio_comment', 'eportfolio');
        $this->objDbCompetencyList = &$this->getObject('dbeportfolio_competency', 'eportfolio');
        $this->objDbInterestList = &$this->getObject('dbeportfolio_interest', 'eportfolio');
        $this->objDbReflectionList = &$this->getObject('dbeportfolio_reflection', 'eportfolio');
        $this->objDbAssertionList = &$this->getObject('dbeportfolio_assertion', 'eportfolio');
        $this->_objGroupAdmin = &$this->newObject('groupadminmodel', 'groupadmin');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->objDate = &$this->newObject('dateandtime', 'utilities');
        $this->objContextUser = $this->getObject('usercontext', 'context');
        $this->objEssayView = $this->newObject('manageviews_essay', 'essay');
        $this->viewAssessments = $this->newObject('viewassessments_Eportfolio', 'eportfolio');
        $this->objAssignmentFunctions = $this->getObject('functions_assignment', 'assignment');
        $this->objWorksheetFunctions = &$this->getObject('functions_worksheet', 'worksheet');
        $this->objWorksheet = $this->getObject('dbworksheet', 'worksheet');
        $this->objWorksheetQuestions = $this->getObject('dbworksheetquestions', 'worksheet');
        $this->objWorksheetAnswers = $this->getObject('dbworksheetanswers', 'worksheet');
        $this->objWorksheetResults = $this->getObject('dbworksheetresults', 'worksheet');
        $this->dbTestadmin = $this->newObject('dbtestadmin', 'mcqtests');
        $this->dbQuestions = $this->newObject('dbquestions', 'mcqtests');
        $this->objMcqtestsFunctions = &$this->getObject('functions_mcqtests', 'mcqtests');
        $this->dbResults = $this->newObject('dbresults', 'mcqtests');
        $this->dbMarked = $this->newObject('dbmarked', 'mcqtests');
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->objDbRubricTables = &$this->getObject('dbrubrictables', 'rubric');
        $this->objDbRubricPerformances = &$this->getObject('dbrubricperformances', 'rubric');
        $this->objDbRubricObjectives = &$this->getObject('dbrubricobjectives', 'rubric');
        $this->objDbRubricCells = &$this->getObject('dbrubriccells', 'rubric');
        $this->objDbRubricAssessments = &$this->getObject('dbrubricassessments', 'rubric');
        $this->objRubricFunctions = &$this->getObject('functions_rubric', 'rubric');
        //$objLanguage =& $this->getObject('language','language');
        $this->objLanguage = &$this->getObject('language', 'language');
    }
    //Function to get user firstname & surname (two columns) with image
    public function getViewIdentification($userId) 
    {
        // Create a heading for emails
        //$objLanguage =& $this->getObject('language','language');
        $idnFname = $this->objUser->getFirstname($userId);
        $idnSname = $this->objUser->getSurname($userId);
        $hasImage = $this->objUser->hasCustomImage($userId);
        //if($hasImage==True){
        $myPhoto = $this->objUser->getUserImage($userId, $forceRefresh = FALSE, $alt = NULL);
        //}else{
        //$myPhoto = Null;
        //}
        $idnobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $idnobjHeading->type = 2;
        $idnobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordInformation", 'eportfolio');
        //echo $emailobjHeading->show();
        // Create a table object for emails
        $idnTable = &$this->newObject("htmltable", "htmlelements");
        $idnTable->border = 1;
        $idnTable->attributes = "rules=none frame=box";
        $idnTable->cellspacing = '3';
        $idnTable->width = "100%";
        // Add the table heading.
        $idnTable->startRow();
        $idnTable->addHeaderCell($idnobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = 'colspan="2"');
        $idnTable->endRow();
        // Display surname, firstname & usr image
        $idnTable->startRow();
        $idnTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_firstName", 'eportfolio') . " : </b>" . $idnFname . "<p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_surName", 'eportfolio') . " : </b>" . $idnSname . "</p>", 180, 'top', 'left');
        $idnTable->addCell($myPhoto, Null, 'top', 'center');
        $idnTable->endRow();
        $idnTbl = $idnTable->show() . '<br></br>';
        return $idnTbl;
    } //end function
    public function getAddress($userId) 
    {
        $objaddressTitles = &$this->getObject('htmlheading', 'htmlelements');
        $objaddressTitles->type = 2;
        //$objLanguage =& $this->getObject('language','language');
        $addressList = $this->objDbAddressList->getByItem($userId);
        if (!empty($addressList)) {
            // Create a table object
            $addressTable = &$this->newObject("htmltable", "htmlelements");
            $addressTable->border = 1;
            $addressTable->cellspacing = '1';
            $addressTable->width = "100%";
            $objaddressTitles->str = $this->objLanguage->languageText("mod_eportfolio_wordAddress", 'eportfolio');
            // Add the table heading.
            /*
            $addressTable->startRow();
            $addressTable->addCell($objaddressTitles->show() , '', '', '', '', 'colspan="7"');
            $addressTable->endRow();
            addCell($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null,$border = '0')
            */
            $addressTable->startRow();
            $addressTable->addCell("<b>&nbsp;&nbsp;&nbsp;" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>", "", "", "", "", $attrib = 'bgcolor="#FFFFFF"');
            $addressTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_streetno", 'eportfolio') . "</b>", "", "", "", "", $attrib = 'bgcolor="#FFFFFF"');
            $addressTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_streetname", 'eportfolio') . "</b>", "", "", "", "", $attrib = 'bgcolor="#FFFFFF"');
            $addressTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_locality", 'eportfolio') . "</b>", "", "", "", "", $attrib = 'bgcolor="#FFFFFF"');
            $addressTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_city", 'eportfolio') . "</b>", "", "", "", "", $attrib = 'bgcolor="#FFFFFF"');
            $addressTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_postcode", 'eportfolio') . "</b>", "", "", "", "", $attrib = 'bgcolor="#FFFFFF"');
            $addressTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_postaddress", 'eportfolio') . "</b>", "", "", "", "", $attrib = 'bgcolor="#FFFFFF"');
            $addressTable->endRow();
            // Step through the list of addresses.
            if (!empty($addressList)) {
                $addressNo = 1;
                $i = 0;
                $bgcolor = "#FFFFFF";
                foreach($addressList as $addressItem) {
                    //$class = ($i++%2) ? 'even' : 'odd';
                    $bgcolor = ($i++%2) ? "#FFFFFF" : "#D3D3D3";
                    // Display each field for addresses
                    $addressTable->startRow();
                    $cattype = $this->objDbCategorytypeList->listSingle($addressItem['type']);
                    //$addressTable->startRow();
                    //                    $addressTable->addCell($addressNo . ")&nbsp;&nbsp;&nbsp;" . $cattype[0]['type'], "", NULL, NULL, NULL, '');
                    $addressTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $addressTable->addCell($addressItem['street_no'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $addressTable->addCell($addressItem['street_name'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $addressTable->addCell($addressItem['locality'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $addressTable->addCell($addressItem['city'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $addressTable->addCell($addressItem['postcode'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $addressTable->addCell($addressItem['postal_address'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $addressTable->endRow();
                    $addressNo = $addressNo+1;
                }
                unset($addressItem);
            } else {
                $addressTable->startRow();
                $addressTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="7"');
                $addressTable->endRow();
            }
            $addressLabel = $objaddressTitles->show() . $addressTable->show() . '<br></br>';
            return $addressLabel;
        } //end if
        
    } //end function
    //get user addresses for view  (one column)
    public function getViewAddress($userId) 
    {
        $objaddressTitles = &$this->getObject('htmlheading', 'htmlelements');
        $objaddressTitles->type = 2;
        //$objLanguage =& $this->getObject('language','language');
        $addressList = $this->objDbAddressList->getByItem($userId);
        if (!empty($addressList)) {
            // Create a table object
            $addressTable = &$this->newObject("htmltable", "htmlelements");
            $addressTable->border = 1;
            $addressTable->attributes = "rules=none frame=box";
            $addressTable->cellspacing = '2';
            $addressTable->width = "100%";
            $objaddressTitles->str = $this->objLanguage->languageText("mod_eportfolio_wordAddress", 'eportfolio');
            // Add the table heading.
            $addressTable->startRow();
            $addressTable->addHeaderCell($objaddressTitles->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $addressTable->endRow();
            // Step through the list of addresses.
            if (!empty($addressList)) {
                $addressNo = 1;
                foreach($addressList as $addressItem) {
                    // Display each field for addresses
                    if (!empty($addressItem['street_no'])) {
                        $strtNo = "<p><b>" . $this->objLanguage->languageText("mod_eportfolio_streetno", 'eportfolio') . " : </b>" . $addressItem['street_no'] . "</p>";
                    } else {
                        $strtNo = Null;
                    }
                    $cattype = $this->objDbCategorytypeList->listSingle($addressItem['type']);
                    $addressTable->startRow();
                    $addressTable->addCell("<li>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . " : " . "</b>" . $cattype[0]['type'] . "<p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_streetname", 'eportfolio') . " : " . "</b>" . $addressItem['street_name'] . "</p>" . $strtNo . "<p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_locality", 'eportfolio') . " : " . "</b>" . $addressItem['locality'] . "</p>" . "<p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_city", 'eportfolio') . " : " . "</b>" . $addressItem['city'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_postcode", 'eportfolio') . " : " . "</b>" . $addressItem['postcode'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_postaddress", 'eportfolio') . " : " . "</b>" . $addressItem['postal_address'] . "</p></li>", 180, 'top', 'left');
                    $addressTable->endRow();
                    $addressNo = $addressNo+1;
                }
                unset($addressItem);
            } else {
                $addressTable->startRow();
                $addressTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', '');
                $addressTable->endRow();
            }
            $addressLabel = $addressTable->show() . '<br></br>';
            return $addressLabel;
        } //end if
        
    } //end function
    public function getContacts($userId) 
    {
        //$objLanguage =& $this->getObject('language','language');
        // Show the heading
        $objcontactTitles = &$this->getObject('htmlheading', 'htmlelements');
        $objcontactTitles->type = 2;
        $objcontactTitles->str = $this->objLanguage->languageText("mod_eportfolio_wordContact", 'eportfolio');
        $contactList = $this->objDbContactList->getByItem($userId);
        if (!empty($contactList)) {
            //$emailList = $this->objDbEmailList->getByItem($userId);
            // Create a table object
            $contactTable = &$this->newObject("htmltable", "htmlelements");
            $contactTable->border = 1;
            $contactTable->cellspacing = '1';
            $contactTable->width = "100%";
            // Add the table heading.
            $contactTable->startRow();
            $contactTable->addCell("<b>&nbsp;&nbsp;&nbsp;" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contacttype", 'eportfolio') . "</b>");
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio') . "</b>");
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio') . "</b>");
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contactnumber", 'eportfolio') . "</b>");
            $contactTable->endRow();
            // Step through the list of contacts
            if (!empty($contactList)) {
                $contactNo = 1;
                $i = 0;
                foreach($contactList as $contactItem) {
                    //$class = ($i++%2) ? 'even' : 'odd';
                    $bgcolor = ($i++%2) ? "#FFFFFF" : "#D3D3D3";
                    // Display each field for contacts
                    $cattype = $this->objDbCategorytypeList->listSingle($contactItem['type']);
                    $modetype = $this->objDbCategorytypeList->listSingle($contactItem['contact_type']);
                    $contactTable->startRow();
                    //$contactTable->addCell($contactNo . ')&nbsp;&nbsp;&nbsp;' . $cattype[0]['type'], "", NULL, NULL, NULL, "bgcolor='".$bgcolor."'");
                    $contactTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $contactTable->addCell($modetype[0]['type'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $contactTable->addCell($contactItem['country_code'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $contactTable->addCell($contactItem['area_code'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $contactTable->addCell($contactItem['id_number'], "", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $contactTable->endRow();
                    $contactNo = $contactNo+1;
                }
                unset($contactItem);
            } else {
                $contactTable->startRow();
                $contactTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
                $contactTable->endRow();
            }
            //echo $contactTable->show();
            $contacts = $objcontactTitles->show() . $contactTable->show() . '<br></br>';
            return $contacts;
        } //end if
        
    } //end function
    public function getViewContacts($userId) 
    {
        //$objLanguage =& $this->getObject('language','language');
        // Show the heading
        $objcontactTitles = &$this->getObject('htmlheading', 'htmlelements');
        $objcontactTitles->type = 2;
        $objcontactTitles->str = $this->objLanguage->languageText("mod_eportfolio_wordContact", 'eportfolio');
        $contactList = $this->objDbContactList->getByItem($userId);
        if (!empty($contactList)) {
            //$emailList = $this->objDbEmailList->getByItem($userId);
            // Create a table object
            $contactTable = &$this->newObject("htmltable", "htmlelements");
            $contactTable->border = 1;
            $contactTable->attributes = "rules=none frame=box";
            $contactTable->cellspacing = '3';
            $contactTable->width = "100%";
            // Add the table heading.
            $contactTable->startRow();
            $contactTable->addHeaderCell($objcontactTitles->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $contactTable->endRow();
            // Step through the list of contacts
            if (!empty($contactList)) {
                $contactNo = 1;
                foreach($contactList as $contactItem) {
                    // Display each field for contacts
                    $cattype = $this->objDbCategorytypeList->listSingle($contactItem['type']);
                    $modetype = $this->objDbCategorytypeList->listSingle($contactItem['contact_type']);
                    $contactTable->startRow();
                    $contactTable->addCell("<li><b>" . $modetype[0]['type'] . " ( " . $cattype[0]['type'] . " ) : </b>" . $contactItem['id_number'] . "<p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio') . " : </b>" . $contactItem['country_code'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio') . " : </b>" . $contactItem['area_code'] . "</p></li>", "", NULL, NULL, NULL, '');
                    $contactTable->endRow();
                    $contactNo = $contactNo+1;
                }
                unset($contactItem);
            } else {
                $contactTable->startRow();
                $contactTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', '');
                $contactTable->endRow();
            }
            //echo $contactTable->show();
            $contacts = $contactTable->show() . '<br></br>';
            return $contacts;
        } //end if
        
    } //end function
    public function getEmail($userId) 
    {
        // Create a heading for emails
        //$objLanguage =& $this->getObject('language','language');
        $emailList = $this->objDbEmailList->getByItem($userId);
        if (!empty($emailList)) {
            $emailobjHeading = &$this->getObject('htmlheading', 'htmlelements');
            $emailobjHeading->type = 2;
            $emailobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordEmail", 'eportfolio');
            //echo $emailobjHeading->show();
            // Create a table object for emails
            $emailTable = &$this->newObject("htmltable", "htmlelements");
            $emailTable->border = 1;
            $emailTable->cellspacing = '1';
            $emailTable->width = "50%";
            // Add the table heading.
            $emailTable->startRow();
            $emailTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>", "60");
            $emailTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_email", 'eportfolio') . "</b>", "150");
            $emailTable->endRow();
            // Step through the list of emails.
            $class = 'even';
            if (!empty($emailList)) {
                $emailNo = 1;
                $i = 0;
                foreach($emailList as $emailItem) {
                    $bgcolor = ($i++%2) ? "#FFFFFF" : "#D3D3D3";
                    // Display each field for emails
                    $cattype = $this->objDbCategorytypeList->listSingle($emailItem['type']);
                    $emailTable->startRow();
                    //$emailTable->addCell($emailNo . ')&nbsp;&nbsp;&nbsp;' . $cattype[0]['type'], "", NULL, NULL, NULL, "bgcolor='".$bgcolor."'");
                    $emailTable->addCell($cattype[0]['type'], "60", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $emailTable->addCell($emailItem['email'], "150", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $emailTable->endRow();
                    $emailNo = $emailNo+1;
                }
                unset($emailItem);
            } else {
                $emailTable->startRow();
                $emailTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
                $emailTable->endRow();
            }
            $emailtbl = $emailobjHeading->show() . $emailTable->show() . '</br>';
            return $emailtbl;
        } //end if
        
    } //end function
    //Function to get user email addresses (one column)
    public function getViewEmail($userId) 
    {
        // Create a heading for emails
        //$objLanguage =& $this->getObject('language','language');
        $emailList = $this->objDbEmailList->getByItem($userId);
        if (!empty($emailList)) {
            $emailobjHeading = &$this->getObject('htmlheading', 'htmlelements');
            $emailobjHeading->type = 2;
            $emailobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordEmail", 'eportfolio');
            //echo $emailobjHeading->show();
            // Create a table object for emails
            $emailTable = &$this->newObject("htmltable", "htmlelements");
            $emailTable->border = 1;
            $emailTable->attributes = "rules=none frame=box";
            $emailTable->cellspacing = '3';
            $emailTable->width = "100%";
            // Add the table heading.
            $emailTable->startRow();
            $emailTable->addHeaderCell($emailobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $emailTable->endRow();
            // Step through the list of emails.
            $class = 'even';
            if (!empty($emailList)) {
                $emailNo = 1;
                foreach($emailList as $emailItem) {
                    // Display each field for emails
                    $cattype = $this->objDbCategorytypeList->listSingle($emailItem['type']);
                    $emailTable->startRow();
                    $emailTable->addCell("<li>" . $cattype[0]['type'] . " : " . $emailItem['email'] . "</li>", "", NULL, NULL, NULL, '');
                    $emailTable->endRow();
                    $emailNo = $emailNo+1;
                }
                unset($emailItem);
            } else {
                $emailTable->startRow();
                $emailTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', '');
                $emailTable->endRow();
            }
            $emailtbl = $emailTable->show() . '<br></br>';
            return $emailtbl;
        } //end if
        
    } //end function
    public function getDemographics($userId) 
    {
        //Demographics view
        //$objLanguage =& $this->getObject('language','language');
        $demographicsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $demographicsobjHeading->type = 2;
        $demographicsList = $this->objDbDemographicsList->getByItem($userId);
        if (!empty($demographicsList)) {
            // Create a table object
            $demographicsTable = &$this->newObject("htmltable", "htmlelements");
            $demographicsTable->border = 1;
            $demographicsTable->cellspacing = '1';
            $demographicsTable->width = "50%";
            // Show the heading
            $demographicsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio');
            /*
            $demographicsTable->startRow();
            $demographicsTable->addCell($demographicsobjHeading->show() , '', '', '', '', 'colspan="3"');
            $demographicsTable->endRow();
            */
            $demographicsTable->startRow();
            //$demographicsTable->addCell("<b>&nbsp;&nbsp;&nbsp;" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>","60");
            $demographicsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_birth", 'eportfolio') . "</b>", "100");
            $demographicsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio') . "</b>", "150");
            $demographicsTable->endRow();
            // Step through the list of Demographics.
            if (!empty($demographicsList)) {
                $demNo = 1;
                $i = 0;
                foreach($demographicsList as $demographicsItem) {
                    $bgcolor = ($i++%2) ? "#FFFFFF" : "#D3D3D3";
                    $datetime = explode("-", $this->objDate->formatDateOnly($demographicsItem['birth']));
                    //var_dump($datetime);
                    // Display each field for Demographics
                    $cattype = $this->objDbCategorytypeList->listSingle($demographicsItem['type']);
                    $demographicsTable->startRow();
                    //$demographicsTable->addCell($demNo . ')&nbsp;&nbsp;&nbsp;' . $cattype[0]['type'], "", NULL, NULL, NULL, '');
                    //$demographicsTable->addCell($cattype[0]['type'], "60", NULL, NULL, NULL, "bgcolor='".$bgcolor."'");
                    $demographicsTable->addCell($datetime[0], "100", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $demographicsTable->addCell($demographicsItem['nationality'], "150", NULL, NULL, NULL, "bgcolor='" . $bgcolor . "'");
                    $demographicsTable->endRow();
                    $demNo = $demNo+1;
                }
                unset($demographicsItem);
            } else {
                $demographicsTable->startRow();
                $demographicsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
                $demographicsTable->endRow();
            }
            $demographicstbl = $demographicsobjHeading->show() . $demographicsTable->show();
            return $demographicstbl;
        } //end if
        
    } //end function
    //Get demographics for user view (one column)
    public function getViewDemographics($userId) 
    {
        //Demographics view
        //$objLanguage =& $this->getObject('language','language');
        $demographicsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $demographicsobjHeading->type = 2;
        $demographicsList = $this->objDbDemographicsList->getByItem($userId);
        if (!empty($demographicsList)) {
            // Create a table object
            $demographicsTable = &$this->newObject("htmltable", "htmlelements");
            $demographicsTable->border = 1;
            $demographicsTable->attributes = "rules=none frame=box";
            $demographicsTable->cellspacing = '3';
            $demographicsTable->width = "100%";
            // Show the heading
            $demographicsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio');
            $demographicsTable->startRow();
            $demographicsTable->addHeaderCell($demographicsobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $demographicsTable->endRow();
            // Step through the list of Demographics.
            if (!empty($demographicsList)) {
                $demNo = 1;
                foreach($demographicsList as $demographicsItem) {
                    // Display each field for Demographics
                    $cattype = $this->objDbCategorytypeList->listSingle($demographicsItem['type']);
                    $demographicsTable->startRow();
                    $demographicsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . '&nbsp;:&nbsp;&nbsp;</b>' . $cattype[0]['type'], "", NULL, NULL, NULL, '');
                    $demographicsTable->endRow();
                    $demographicsTable->startRow();
                    $mybirth = explode("-", $this->objDate->formatDateOnly($demographicsItem['birth']));
                    $demographicsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_birth", 'eportfolio') . " : </b>" . $mybirth[0], "", NULL, NULL, NULL, '');
                    $demographicsTable->endRow();
                    $demographicsTable->startRow();
                    $demographicsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio') . " : </b>" . $demographicsItem['nationality'], "", NULL, NULL, NULL, '');
                    $demographicsTable->endRow();
                    $demNo = $demNo+1;
                }
                unset($demographicsItem);
            } else {
                $demographicsTable->startRow();
                $demographicsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', '');
                $demographicsTable->endRow();
            }
            $demographicstbl = $demographicsTable->show() . '<br></br>';
            return $demographicstbl;
        } //end if
        
    } //end function
    public function getActivity($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $activityobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $activityobjHeading->type = 2;
        $activityobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio');
        $activitylist = $this->objDbActivityList->getByItem($userId);
        if (!empty($activitylist)) {
            // Create a table object
            $activityTable = &$this->newObject("htmltable", "htmlelements");
            $activityTable->border = 1;
            $activityTable->cellspacing = '1';
            $activityTable->width = "100%";
            // Add the table heading.
            /*
            $activityTable->startRow();
            $activityTable->addCell($activityobjHeading->show() , '', '', '', '', Null);
            $activityTable->endRow();
            */
            // Step through the list of activities
            $class = NULL;
            if (!empty($activitylist)) {
                $i = 0;
                $actyNo = 1;
                foreach($activitylist as $item) {
                    //Spacer
                    If ($actyNo > 1) {
                        $activityTable->startRow();
                        $activityTable->addCell("", "", NULL, NULL, $class, "bgcolor='#D3D3D3' COLSPAN=2");
                        $activityTable->endRow();
                    }
                    //Get context title
                    $objDbContext = &$this->getObject('dbcontext', 'context');
                    $mycontextRecord = $objDbContext->getContextDetails($item['contextid']);
                    if (!empty($mycontextRecord)) {
                        $mycontextTitle = $mycontextRecord['title'];
                    } else {
                        $mycontextTitle = $item['contextid'];
                    }
                    // Display each field for activities
                    $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
                    $activityTable->startRow();
                    $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio') . '&nbsp;&nbsp;' . $actyNo . "</b>", "", "", "", "", "bgcolor='#FFFFFF' COLSPAN=2");
                    $activityTable->endRow();
                    if (!empty($mycontextTitle) && $mycontextTitle !== "None") {
                        $activityTable->startRow();
                        $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3' COLSPAN=2");
                        $activityTable->endRow();
                        $activityTable->startRow();
                        $activityTable->addCell($mycontextTitle, "", NULL, NULL, $class, "bgcolor='#FFFFFF' COLSPAN=2");
                        $activityTable->endRow();
                    }
                    $activityTable->startRow();
                    $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitytype", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3' COLSPAN=2");
                    $activityTable->endRow();
                    $activityTable->startRow();
                    $activityTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, "bgcolor='#FFFFFF' COLSPAN=2");
                    $activityTable->endRow();
                    $activityTable->startRow();
                    $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $activityTable->endRow();
                    $activityTable->startRow();
                    $activityTable->addCell($this->objDate->formatDateOnly($item['start']) , "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $activityTable->addCell($this->objDate->formatDateOnly($item['finish']) , "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $activityTable->endRow();
                    /*
                    $activityTable->startRow();
                    $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>","","","","","bgcolor='#D3D3D3' COLSPAN=2");
                    $activityTable->endRow();
                    $activityTable->startRow();
                    $activityTable->addCell($this->objDate->formatDateOnly($item['finish']) , "", NULL, NULL, $class, "bgcolor='#FFFFFF' COLSPAN=2");
                    $activityTable->endRow();
                    */
                    $activityTable->startRow();
                    $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3' COLSPAN=2");
                    $activityTable->endRow();
                    $activityTable->startRow();
                    $activityTable->addCell($item['shortdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF' COLSPAN=2");
                    $activityTable->endRow();
                    $activityTable->startRow();
                    $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3' COLSPAN=2");
                    $activityTable->endRow();
                    $activityTable->startRow();
                    $activityTable->addCell($item['longdescription'], "", "top", NULL, $class, "bgcolor='#FFFFFF' COLSPAN=2");
                    $activityTable->endRow();
                    $actyNo = $actyNo+1;
                }
                unset($item);
            } else {
                $activityTable->startRow();
                $activityTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $activityTable->endRow();
            }
            $activitytbl = $activityobjHeading->show() . $activityTable->show();
            return $activitytbl;
        } //end if
        
    } //end function
    //Function to Get user activities (one column)
    public function getViewActivity($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $activityobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $activityobjHeading->type = 2;
        $activityobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio');
        $activitylist = $this->objDbActivityList->getByItem($userId);
        if (!empty($activitylist)) {
            // Create a table object
            $activityTable = &$this->newObject("htmltable", "htmlelements");
            $activityTable->border = 1;
            $activityTable->attributes = "rules=none frame=box";
            $activityTable->cellspacing = '3';
            $activityTable->width = "100%";
            // Add the table heading.
            $activityTable->startRow();
            $activityTable->addHeaderCell($activityobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $activityTable->endRow();
            // Step through the list of activities
            $class = NULL;
            if (!empty($activitylist)) {
                $i = 0;
                $actyNo = 1;
                foreach($activitylist as $item) {
                    //Get context title
                    $objDbContext = &$this->getObject('dbcontext', 'context');
                    $mycontextRecord = $objDbContext->getContextDetails($item['contextid']);
                    if (!empty($mycontextRecord)) {
                        $mycontextTitle = $mycontextRecord['title'];
                        $myCourse = "<p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . " : </b></p>" . $mycontextTitle;
                    } else {
                        $mycontextTitle = $item['contextid'];
                        $myCourse = Null;
                    }
                    // Display each field for activities
                    $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
                    $activityTable->startRow();
                    $activityTable->addCell("<li><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_activitytype", 'eportfolio') . " : </b>" . $cattype[0]['type'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . " : </b>" . $this->objDate->formatDateOnly($item['start']) . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . " : </b>" . $this->objDate->formatDateOnly($item['finish']) . "</p>". $myCourse . "<p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . " : </b>" . $item['shortdescription'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . " : </b>" . $item['longdescription'] . "</p></li>");
                    $activityTable->endRow();
                    $actyNo = $actyNo+1;
                }
                unset($item);
            } else {
                $activityTable->startRow();
                $activityTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $activityTable->endRow();
            }
            $activitytbl = $activityTable->show() . '<br></br>';
            return $activitytbl;
        } //end if
        
    } //end function
    public function getAffiliation($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $affiliationobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $affiliationobjHeading->type = 2;
        $affiliationobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio');
        $affiliationList = $this->objDbAffiliationList->getByItem($userId);
        if (!empty($affiliationList)) {
            // Create a table object
            $affiliationTable = &$this->newObject("htmltable", "htmlelements");
            $affiliationTable->border = 1;
            $affiliationTable->cellspacing = '1';
            $affiliationTable->width = "100%";
            /*
            // Add the table heading.
            $affiliationTable->startRow();
            //addCell($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null,$border = '0')
            $affiliationTable->addCell($affiliationobjHeading->show() , '', '', '', '', 'colspan="6"', Null);
            $affiliationTable->endRow();
            
            $affiliationTable->startRow();
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>","","","","","");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_classificationView", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_roleView", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
            $affiliationTable->endRow();
            */
            // Step through the list of affiliations
            $class = NULL;
            if (!empty($affiliationList)) {
                $i = 0;
                $affNo = 1;
                foreach($affiliationList as $affiliationItem) {
                    //Add spacer
                    if ($affNo > 1) {
                        $affiliationTable->startRow();
                        $affiliationTable->addCell("", "", "", "", "", "bgcolor='#D3D3D3' COLSPAN=2");
                        $affiliationTable->endRow();
                    }
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio') . " " . $affNo . "</b>", "", "", "", "", "bgcolor='#FFFFFF' COLSPAN=2");
                    $affiliationTable->endRow();
                    // Display each field for affiliations
                    $cattype = $this->objDbCategorytypeList->listSingle($affiliationItem['type']);
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio') . " " . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell($cattype[0]['type'], "", NULL, NULL, Null, "bgcolor='#FFFFFF' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_classificationView", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell($affiliationItem['classification'], "", NULL, NULL, NULL, "bgcolor='#FFFFFF' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_roleView", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell($affiliationItem['role'], "", NULL, NULL, NULL, "bgcolor='#FFFFFF' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell($affiliationItem['organisation'], "", NULL, NULL, NULL, "bgcolor='#FFFFFF' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell($this->objDate->formatDateOnly($affiliationItem['start']) , "", NULL, NULL, NULL, "bgcolor='#FFFFFF'");
                    $affiliationTable->addCell($this->objDate->formatDateOnly($affiliationItem['finish']) , "", NULL, NULL, NULL, "bgcolor='#FFFFFF'");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell($affiliationItem['shortdescription'], "", NULL, NULL, NULL, "bgcolor='#FFFFFF' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affiliationTable->startRow();
                    $affiliationTable->addCell($affiliationItem['longdescription'], "", NULL, NULL, NULL, "bgcolor='#FFFFFF' COLSPAN=2");
                    $affiliationTable->endRow();
                    $affNo = $affNo+1;
                }
                unset($affiliationItem);
            } else {
                $affiliationTable->startRow();
                $affiliationTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $affiliationTable->endRow();
            }
            $affiliationtbl = $affiliationobjHeading->show() . $affiliationTable->show();
            return $affiliationtbl;
        } //end if
        
    } //end function
    public function getViewAffiliation($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $affiliationobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $affiliationobjHeading->type = 2;
        $affiliationobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio');
        $affiliationList = $this->objDbAffiliationList->getByItem($userId);
        if (!empty($affiliationList)) {
            // Create a table object
            $affiliationTable = &$this->newObject("htmltable", "htmlelements");
            $affiliationTable->border = 1;
            $affiliationTable->attributes = "rules=none frame=box";
            $affiliationTable->cellspacing = '3';
            $affiliationTable->width = "100%";
            // Add the table heading.
            $affiliationTable->startRow();
            $affiliationTable->addHeaderCell($affiliationobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $affiliationTable->endRow();
            // Step through the list of affiliations
            $class = NULL;
            if (!empty($affiliationList)) {
                $i = 0;
                $affNo = 1;
                foreach($affiliationList as $affiliationItem) {
                    // Display each field for affiliations
                    $cattype = $this->objDbCategorytypeList->listSingle($affiliationItem['type']);
                    $affiliationTable->startRow();
                    $affiliationTable->addCell("<li>" . "<b>" . $affiliationItem['organisation'] . " ( " . $cattype[0]['type'] . " ) " . "</b><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_classificationView", 'eportfolio') . " : </b>" . $affiliationItem['classification'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_roleView", 'eportfolio') . " : </b>" . $affiliationItem['role'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . " : </b>" . $this->objDate->formatDateOnly($affiliationItem['start']) . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . " : </b>" . $this->objDate->formatDateOnly($affiliationItem['finish']) . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . " : </b>" . $affiliationItem['shortdescription'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . " : </b>" . $affiliationItem['longdescription'] . "</p></li>", "", NULL, NULL, Null, '');
                    $affiliationTable->endRow();
                    $affNo = $affNo+1;
                }
                unset($affiliationItem);
            } else {
                $affiliationTable->startRow();
                $affiliationTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $affiliationTable->endRow();
            }
            $affiliationtbl = $affiliationTable->show() . '<br></br>';
            return $affiliationtbl;
        } //end if
        
    } //end function
    public function getTranscripts($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $transcriptobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $transcriptobjHeading->type = 2;
        $transcriptobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordTranscripts", 'eportfolio');
        // echo $transcriptobjHeading->show();
        $transcriptlist = $this->objDbTranscriptList->getByItem($userId);
        //Get user contexts
        $myContexts = $this->objContextUser->getUserContext($userId);
        // Create a table object
        $transcriptTable = &$this->newObject("htmltable", "htmlelements");
        $transcriptTable->border = 0;
        $transcriptTable->cellspacing = '1';
        $transcriptTable->width = "100%";
        /*
        // Add the table heading.
        $transcriptTable->startRow();
        $transcriptTable->addCell($transcriptobjHeading->show() , '', '', '', '', Null);
        $transcriptTable->endRow();
        */
        $hasAssessment = 0;
        //Get DB Object
        $objDbContext = &$this->getObject('dbcontext', 'context');
        foreach($myContexts as $contextCode) {
            $contextLecturers = $this->objContextUser->getContextLecturers($contextCode);
            $isaLecturer = False;
            foreach($contextLecturers as $isLecturer) {
                if ($this->userPid == $isLecturer['id']) $isaLecturer = True;
            }
            if (!$isaLecturer) {
                //Get student essays for this course
                $contextEssay = $this->objEssayView->getStudentEssays($contextCode);
                if (!empty($contextEssay)) {
                    $hasAssessment = 1;
                    $hasEssays = 1;
                    $viewEssays = $this->viewAssessments->viewEssaysFull($contextEssay);
                    $list = $this->objLanguage->languageText('word_list');
                    $head = $objDbContext->getTitle($contextCode) . " : " . $this->objLanguage->languageText('mod_essay_essay', 'essay');
                    //echo "<b>".$head."</b>".$viewEssays;
                    $transcriptTable->startRow();
                    $transcriptTable->addCell("<h3>" . $head . "</h3>" . $viewEssays);
                    $transcriptTable->endRow();
                }
                //Get student essays for this course
                $contextAssignments = $this->objAssignmentFunctions->displayAssignmentFull($contextCode);
                if (!empty($contextAssignments)) {
                    $hasAssessment = 1;
                    //$viewEssays = $this->viewAssessments->viewEssays($contextEssay);
                    $list = $this->objLanguage->languageText('word_list');
                    $head = $objDbContext->getTitle($contextCode) . " : " . $this->objLanguage->languageText('mod_assignment_assignments', 'assignment');
                    //echo "<b>".$head."</b>".$viewEssays;
                    $transcriptTable->startRow();
                    $transcriptTable->addCell("<h3>" . $head . "</h3>" . $contextAssignments);
                    $transcriptTable->endRow();
                }
                $contextWorksheets = $this->objWorksheetFunctions->displayWorksheetsFull($contextCode, $userId);
                if (!empty($contextWorksheets)) {
                    $hasAssessment = 1;
                    $transcriptTable->startRow();
                    $transcriptTable->addCell($contextWorksheets);
                    $transcriptTable->endRow();
                }
                //Get mcqtests
                $objmcq = $this->objMcqtestsFunctions->displaymcqFull($contextCode, $userId);
                if (!empty($objmcq) && $objmcq !== false) {
                    $hasAssessment = 1;
                    $mcqHead = $objDbContext->getTitle($contextCode) . " : " . $this->objLanguage->languageText('mod_mcqtests_mcq', 'mcqtests');
                    $transcriptTable->startRow();
                    $transcriptTable->addCell("<h3>" . $mcqHead . "</h3>" . $objmcq);
                    $transcriptTable->endRow();
                }
                //Get Rubrics
                $studRubrics = $this->objRubricFunctions->displayrubricFull($contextCode, $userId, $uriModule = 'eportfolio', $assessmentAction = 'rubricsassessments', $viewTableAction = 'rubricviewtable');
                if (!empty($studRubrics)) {
                    $hasAssessment = 1;
                    $rubricHead = $objDbContext->getTitle($contextCode) . " : " . $this->objLanguage->languageText('rubric_rubrics', 'rubric');
                    $transcriptTable->startRow();
                    $transcriptTable->addCell("<h3>" . $rubricHead . "</h3>" . $studRubrics);
                    $transcriptTable->endRow();
                }
            }
        }
        if (!empty($transcriptlist)) {
            // Step through the list of transcripts.
            $class = NULL;
            if (!empty($transcriptlist)) {
                $objtransTable = new htmltable();
                $objtransTable->border = 1;
                $objtransTable->cellspacing = '1';
                $objtransTable->width = "100%";
                $transNo = 1;
                foreach($transcriptlist as $item) {
                    //Spacer
                    if ($transNo > 1) {
                        $objtransTable->startRow();
                        $objtransTable->addCell("&nbsp;", "", "", "", "", "bgcolor='#D3D3D3'");
                        $objtransTable->endRow();
                    }
                    // Display each field for transcripts
                    $objtransTable->startRow();
                    $objtransTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordtranscript", 'eportfolio') . '&nbsp;&nbsp;' . $transNo . "</b>", "", "", "", "", "bgcolor='#FFFFFF'");
                    $objtransTable->endRow();
                    $objtransTable->startRow();
                    $objtransTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $objtransTable->endRow();
                    $objtransTable->startRow();
                    $objtransTable->addCell($item['shortdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $objtransTable->endRow();
                    $objtransTable->startRow();
                    $objtransTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $objtransTable->endRow();
                    $objtransTable->startRow();
                    $objtransTable->addCell($item['longdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $objtransTable->endRow();
                    $transNo = $transNo+1;
                }
                unset($item);
                $transcriptTable->startRow();
                $transcriptTable->addCell($objtransTable->show() , '', '', '', '', "");
                $transcriptTable->endRow();
            } else {
                $transcriptTable->startRow();
                $transcriptTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', "bgcolor='#FFFFFF'");
                $transcriptTable->endRow();
            }
        } //end if
        if ($hasAssessment == 1 || !empty($transcriptlist)) {
            $transcripttbl = $transcriptobjHeading->show() . $transcriptTable->show();
            return $transcripttbl;
        }
    } //end function
    //Function to Get user transcripts (one column)
    public function getViewTranscripts($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $transcriptobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $transcriptobjHeading->type = 2;
        $transcriptobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordTranscripts", 'eportfolio');
        // echo $transcriptobjHeading->show();
        $transcriptlist = $this->objDbTranscriptList->getByItem($userId);
        if (!empty($transcriptlist)) {
            // Create a table object
            $transcriptTable = &$this->newObject("htmltable", "htmlelements");
            $transcriptTable->border = 1;
            $transcriptTable->attributes = "rules=none frame=box";
            $transcriptTable->cellspacing = '3';
            $transcriptTable->width = "100%";
            // Add the table heading.
            $transcriptTable->startRow();
            $transcriptTable->addHeaderCell($transcriptobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $transcriptTable->endRow();
            // Step through the list of transcripts.
            $class = NULL;
            if (!empty($transcriptlist)) {
                $transNo = 1;
                foreach($transcriptlist as $item) {
                    // Display each field for transcripts
                    $transcriptTable->startRow();
                    $transcriptTable->addCell("<li>" . $item['shortdescription'] . "<p>" . $item['longdescription'] . "</p></li>", "", NULL, NULL, $class, '');
                    $transcriptTable->endRow();
                    $transNo = $transNo+1;
                }
                unset($item);
            } else {
                $transcriptTable->startRow();
                $transcriptTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $transcriptTable->endRow();
            }
            $transcripttbl = $transcriptTable->show() . '<br></br>';
            return $transcripttbl;
        } //end if
        
    } //end function
    public function getQualification($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $qclobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $qclobjHeading->type = 2;
        $qclobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio');
        //echo $qclobjHeading->show();
        $qclList = $this->objDbQclList->getByItem($userId);
        if (!empty($qclList)) {
            // Create a table object
            $qclTable = &$this->newObject("htmltable", "htmlelements");
            $qclTable->border = 1;
            $qclTable->cellspacing = '1';
            $qclTable->width = "100%";
            /*
            // Add the table heading.
            $qclTable->startRow();
            $qclTable->addCell($qclobjHeading->show() , '', '', '', '', Null);
            $qclTable->endRow();
            */
            // Step through the list of qcl.
            $class = NULL;
            if (!empty($qclList)) {
                $qclNo = 1;
                foreach($qclList as $qclItem) {
                    if ($qclNo > 1) {
                        $qclTable->startRow();
                        $qclTable->addCell("&nbsp;", "", "", "", "", "bgcolor='#D3D3D3'");
                        $qclTable->endRow();
                    }
                    // Display each field for qcl
                    $cattype = $this->objDbCategorytypeList->listSingle($qclItem['qcl_type']);
                    $qclTable->startRow();
                    $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio') . "&nbsp;&nbsp;" . $qclNo . "</b>", "", "", "", "", "bgcolor='#FFFFFF'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio') . "&nbsp;&nbsp;" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordtitle", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell($qclItem['qcl_title'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell($qclItem['organisation'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_level", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell($qclItem['qcl_level'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell($this->objDate->formatDateOnly($qclItem['award_date']) , "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell($qclItem['shortdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $qclTable->endRow();
                    $qclTable->startRow();
                    $qclTable->addCell($qclItem['longdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $qclTable->endRow();
                    $qclNo = $qclNo+1;
                }
                unset($qclItem);
            } else {
                $qclTable->startRow();
                $qclTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $qclTable->endRow();
            }
            $qcltbl = $qclobjHeading->show() . $qclTable->show();
            return $qcltbl;
        } //end if
        
    } //end function
    //User View qcl (one column)
    public function getViewQualification($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $qclobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $qclobjHeading->type = 2;
        $qclobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio');
        //echo $qclobjHeading->show();
        $qclList = $this->objDbQclList->getByItem($userId);
        if (!empty($qclList)) {
            // Create a table object
            $qclTable = &$this->newObject("htmltable", "htmlelements");
            $qclTable->border = 1;
            $qclTable->attributes = "rules=none frame=box";
            $qclTable->cellspacing = '3';
            $qclTable->width = "100%";
            // Add the table heading.
            $qclTable->startRow();
            $qclTable->addHeaderCell($qclobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $qclTable->endRow();
            // Step through the list of qcl.
            $class = NULL;
            if (!empty($qclList)) {
                $qclNo = 1;
                foreach($qclList as $qclItem) {
                    // Display each field for qcl
                    $cattype = $this->objDbCategorytypeList->listSingle($qclItem['qcl_type']);
                    $qclTable->startRow();
                    $qclTable->addCell("<li><b>" . $qclItem['qcl_title'] . " ( " . $cattype[0]['type'] . " )" . "</b><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . " : </b>" . $qclItem['organisation'] . "</p><p><b>" . $this->objLanguage->languageText("mod_eportfolio_level", 'eportfolio') . " : </b>" . $qclItem['qcl_level'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . " : </b>" . $this->objDate->formatDateOnly($qclItem['award_date']) . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . " : </b>" . $qclItem['shortdescription'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . " : </b>" . $qclItem['longdescription'] . "</p></li>");
                    $qclNo = $qclNo+1;
                }
                unset($qclItem);
            } else {
                $qclTable->startRow();
                $qclTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $qclTable->endRow();
            }
            $qcltbl = $qclTable->show() . '<br></br>';
            return $qcltbl;
        } //end if
        
    } //end function
    public function getGoals($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $goalsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $goalsobjHeading->type = 2;
        $goalsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordGoals", 'eportfolio');
        $goalsList = $this->objDbGoalsList->getByItem($userId);
        if (!empty($goalsList)) {
            // Create a table object
            $goalsTable = &$this->newObject("htmltable", "htmlelements");
            $goalsTable->border = 1;
            $goalsTable->cellspacing = '1';
            $goalsTable->width = "100%";
            /*
            // Add the table heading.
            $goalsTable->startRow();
            $goalsTable->addCell($goalsobjHeading->show() , '', '', '', '', Null);
            $goalsTable->endRow();
            */
            // Step through the list of goals.
            $class = NULL;
            if (!empty($goalsList)) {
                $i = 0;
                $goalNo = 1;
                foreach($goalsList as $item) {
                    if ($goalNo > 1) {
                        $goalsTable->startRow();
                        $goalsTable->addCell("&nbsp;", "", "", "", "", "bgcolor='#D3D3D3'");
                        $goalsTable->endRow();
                    }
                    // Display each field for goals
                    $goalsTable->startRow();
                    $goalsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordGoal", 'eportfolio') . "&nbsp;" . $goalNo . "</b>", "", "", "", "", "bgcolor='#FFFFFF'");
                    $goalsTable->endRow();
                    $goalsTable->startRow();
                    $goalsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $goalsTable->endRow();
                    $goalsTable->startRow();
                    $goalsTable->addCell($item['shortdescription'], "", NULL, NULL, "", "bgcolor='#FFFFFF'");
                    $goalsTable->endRow();
                    $goalsTable->startRow();
                    $goalsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $goalsTable->endRow();
                    $goalsTable->startRow();
                    $goalsTable->addCell($item['longdescription'], "", "", "", "", "bgcolor='#FFFFFF'");
                    $goalsTable->endRow();
                    $goalNo = $goalNo+1;
                }
                unset($item);
            } else {
                $goalsTable->startRow();
                $goalsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $goalsTable->endRow();
            }
            $goalstbl = $goalsobjHeading->show() . $goalsTable->show();
            return $goalstbl;
        } //end if
        
    } //end function
    public function getViewGoals($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $goalsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $goalsobjHeading->type = 2;
        $goalsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordGoals", 'eportfolio');
        $goalsList = $this->objDbGoalsList->getByItem($userId);
        if (!empty($goalsList)) {
            // Create a table object
            $goalsTable = &$this->newObject("htmltable", "htmlelements");
            $goalsTable->border = 1;
            $goalsTable->attributes = "rules=none frame=box";
            $goalsTable->cellspacing = '3';
            $goalsTable->width = "100%";
            // Add the table heading.
            $goalsTable->startRow();
            $goalsTable->addHeaderCell($goalsobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $goalsTable->endRow();
            // Step through the list of goals.
            $class = NULL;
            if (!empty($goalsList)) {
                $i = 0;
                $goalNo = 1;
                foreach($goalsList as $item) {
                    // Display each field for goals
                    $goalsTable->startRow();
                    $goalsTable->addCell("<li><p><b>"  . $item['shortdescription'] . "</b></p><p>" . $item['longdescription'] .  "</p></li>", "", NULL, NULL, $class, '');
                    $goalsTable->endRow();
                    $goalNo = $goalNo+1;
                }
                unset($item);
            } else {
                $goalsTable->startRow();
                $goalsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', Null);
                $goalsTable->endRow();
            }
            $goalstbl = $goalsTable->show() . '<br></br>';
            return $goalstbl;
        } //end if
        
    } //end function
    public function getCompetency($userId) 
    {
        // Show the heading
        $competencyobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $competencyobjHeading->type = 2;
        $competencyobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio');
        $competencyList = $this->objDbCompetencyList->getByItem($userId);
        if (!empty($competencyList)) {
            // Create a table object
            $competencyTable = &$this->newObject("htmltable", "htmlelements");
            $competencyTable->border = 1;
            $competencyTable->cellspacing = '1';
            $competencyTable->width = "100%";
            // Add the table heading.
            /*
            $competencyTable->startRow();
            $competencyTable->addCell($competencyobjHeading->show() , '', '', '', '', NULL);
            $competencyTable->endRow();
            */
            // Step through the list of competencies.
            $class = NULL;
            if (!empty($competencyList)) {
                $compNo = 1;
                foreach($competencyList as $item) {
                    //Spacer
                    if ($compNo > 1) {
                        $competencyTable->startRow();
                        $competencyTable->addCell("&nbsp;&nbsp;", "", "", "", "", "bgcolor='#D3D3D3'");
                        $competencyTable->endRow();
                    }
                    // Display each field for competencies
                    $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
                    $competencyTable->startRow();
                    $competencyTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio') . "&nbsp;&nbsp;" . $compNo . "</b>", "", "", "", "", "bgcolor='#FFFFFF'");
                    $competencyTable->endRow();
                    $competencyTable->startRow();
                    $competencyTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $competencyTable->endRow();
                    $competencyTable->startRow();
                    $competencyTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $competencyTable->endRow();
                    $competencyTable->startRow();
                    $competencyTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $competencyTable->endRow();
                    $competencyTable->startRow();
                    $competencyTable->addCell($this->objDate->formatDateOnly($item['award_date']) , "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $competencyTable->endRow();
                    $competencyTable->startRow();
                    $competencyTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $competencyTable->endRow();
                    $competencyTable->startRow();
                    $competencyTable->addCell($item['shortdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $competencyTable->endRow();
                    $competencyTable->startRow();
                    $competencyTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $competencyTable->endRow();
                    $competencyTable->startRow();
                    $competencyTable->addCell($item['longdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $competencyTable->endRow();
                    $compNo = $compNo+1;
                }
                unset($item);
                $competencytbl = $competencyobjHeading->show() . $competencyTable->show();
                return $competencytbl;
            }
        } //end if
        
    } //end function
    //Function to view user Competency (One column)
    public function getViewCompetency($userId) 
    {
        // Show the heading
        $competencyobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $competencyobjHeading->type = 2;
        $competencyobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio');
        $competencyList = $this->objDbCompetencyList->getByItem($userId);
        if (!empty($competencyList)) {
            // Create a table object
            $competencyTable = &$this->newObject("htmltable", "htmlelements");
            $competencyTable->border = 1;
            $competencyTable->attributes = "rules=none frame=box";
            $competencyTable->cellspacing = '3';
            $competencyTable->width = "100%";
            // Add the table heading.
            $competencyTable->startRow();
            $competencyTable->addHeaderCell($competencyobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $competencyTable->endRow();
            // Step through the list of competencies.
            $class = NULL;
            if (!empty($competencyList)) {
                $compNo = 1;
                foreach($competencyList as $item) {
                    // Display each field for competencies
                    $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
                    $competencyTable->startRow();
                    $competencyTable->addCell("<li><b>" . $item['shortdescription'] . " ( " . $cattype[0]['type'] . ") </b><p>" . $item['longdescription'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . " : </b>" . $this->objDate->formatDateOnly($item['award_date']) . "</p></li>", "", NULL, NULL, $class, '');
                    $competencyTable->endRow();
                    $compNo = $compNo+1;
                }
                unset($item);
                $competencytbl = $competencyTable->show() . '<br></br>';
                return $competencytbl;
            }
        } //end if
        
    } //end function
    public function getInterests($userId) 
    {
        // Show the heading
        $interestobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $interestobjHeading->type = 2;
        $interestobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordInterests", 'eportfolio');
        //echo $interestobjHeading->show();
        $interestList = $this->objDbInterestList->getByItem($userId);
        if (!empty($interestList)) {
            // Create a table object
            $interestTable = &$this->newObject("htmltable", "htmlelements");
            $interestTable->border = 1;
            $interestTable->cellspacing = '1';
            $interestTable->width = "100%";
            // Add the table heading.
            /*
            $interestTable->startRow();
            $interestTable->addCell($interestobjHeading->show() , '', '', '', '', Null);
            $interestTable->endRow();
            */
            // Step through the list of interests.
            $class = NULL;
            if (!empty($interestList)) {
                $intNo = 1;
                foreach($interestList as $item) {
                    //Spacer
                    if ($intNo > 1) {
                        $interestTable->startRow();
                        $interestTable->addCell("&nbsp;", "", "", "", "", "bgcolor='#D3D3D3'");
                        $interestTable->endRow();
                    }
                    // Display each field for interests
                    $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
                    $interestTable->startRow();
                    $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordInterest", 'eportfolio') . "&nbsp;&nbsp;" . $intNo . "</b>", "", "", "", "", "bgcolor='#FFFFFF'");
                    $interestTable->endRow();
                    $interestTable->startRow();
                    $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordInterest", 'eportfolio') . " " . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $interestTable->endRow();
                    $interestTable->startRow();
                    $interestTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $interestTable->endRow();
                    $interestTable->startRow();
                    $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $interestTable->endRow();
                    $interestTable->startRow();
                    $interestTable->addCell($this->objDate->formatDateOnly($item['creation_date']) , "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $interestTable->endRow();
                    $interestTable->startRow();
                    $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $interestTable->endRow();
                    $interestTable->startRow();
                    $interestTable->addCell($item['shortdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $interestTable->endRow();
                    $interestTable->startRow();
                    $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $interestTable->endRow();
                    $interestTable->startRow();
                    $interestTable->addCell($item['longdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $interestTable->endRow();
                    $intNo = $intNo+1;
                }
                unset($item);
                $interesttbl = $interestobjHeading->show() . $interestTable->show();
                return $interesttbl;
            }
        } //end if
        
    } //end function
    public function getViewInterests($userId) 
    {
        // Show the heading
        $interestobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $interestobjHeading->type = 2;
        $interestobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordInterests", 'eportfolio');
        //echo $interestobjHeading->show();
        $interestList = $this->objDbInterestList->getByItem($userId);
        if (!empty($interestList)) {
            // Create a table object
            $interestTable = &$this->newObject("htmltable", "htmlelements");
            $interestTable->border = 1;
            $interestTable->attributes = "rules=none frame=box";
            $interestTable->cellspacing = '3';
            $interestTable->width = "100%";
            // Add the table heading.
            $interestTable->startRow();
            $interestTable->addHeaderCell($interestobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $interestTable->endRow();
            // Step through the list of interests.
            $class = NULL;
            if (!empty($interestList)) {
                $intNo = 1;
                foreach($interestList as $item) {
                    // Display each field for interests
                    $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
                    $interestTable->startRow();
                    $interestTable->addCell("<li><b>" . $item['shortdescription'] . " ( " . $cattype[0]['type'] . " ) " . "</b></ br>" . $item['longdescription'] . "</ br> <b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . " : </b>" . $this->objDate->formatDateOnly($item['creation_date']) . "</li>");
                    $interestTable->endRow();
                    $intNo = $intNo+1;
                }
                unset($item);
                $interesttbl = $interestTable->show() . '<br></br>';
                return $interesttbl;
            }
        } //end if
        
    } //end function
    public function getReflections($userId) 
    {
        // Show the heading
        $reflectionobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $reflectionobjHeading->type = 2;
        $reflectionobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordReflections", 'eportfolio');
        //echo $reflectionobjHeading->show();
        $reflectionList = $this->objDbReflectionList->getByItem($userId);
        if (!empty($reflectionList)) {
            // Create a table object
            $reflectionTable = &$this->newObject("htmltable", "htmlelements");
            $reflectionTable->border = 1;
            $reflectionTable->cellspacing = '1';
            $reflectionTable->width = "100%";
            /*
            // Add the table heading.
            $reflectionTable->startRow();
            $reflectionTable->addCell($reflectionobjHeading->show() , '', '', '', '', Null);
            $reflectionTable->endRow();
            */
            // Step through the list of reflections.
            $class = NULL;
            if (!empty($reflectionList)) {
                $refNo = 1;
                foreach($reflectionList as $item) {
                    //Spacer
                    if ($refNo > 1) {
                        $reflectionTable->startRow();
                        $reflectionTable->addCell("&nbsp;", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                        $reflectionTable->endRow();
                    }
                    // Display each field for reflections
                    $reflectionTable->startRow();
                    $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordReflection", 'eportfolio') . "&nbsp;&nbsp;" . $refNo . "</b>", "", NULL, NULL, NULL, "bgcolor='#FFFFFF'");
                    $reflectionTable->endRow();
                    $reflectionTable->startRow();
                    $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                    $reflectionTable->endRow();
                    $reflectionTable->startRow();
                    $reflectionTable->addCell($item['rationale'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $reflectionTable->endRow();
                    $reflectionTable->startRow();
                    $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                    $reflectionTable->endRow();
                    $reflectionTable->startRow();
                    $reflectionTable->addCell($this->objDate->formatDateOnly($item['creation_date']) , "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $reflectionTable->endRow();
                    $reflectionTable->startRow();
                    $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                    $reflectionTable->endRow();
                    $reflectionTable->startRow();
                    $reflectionTable->addCell($item['shortdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $reflectionTable->endRow();
                    $reflectionTable->startRow();
                    $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                    $reflectionTable->endRow();
                    $reflectionTable->startRow();
                    $reflectionTable->addCell($item['longdescription'], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                    $reflectionTable->endRow();
                    //row for comments
                    $mycomments = $this->objDbComment->listAll($item['id']);
                    if (!empty($mycomments)) {
                        $reflectionTable->startRow();
                        $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordComment", 'eportfolio') . " : </b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                        $reflectionTable->endRow();
                        foreach($mycomments as $comment) {
                            //$this->objUser
                            $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                            $commentime = "";
                            if (!empty($comment["postdate"])) {
                                $commentime = " : " . $comment["postdate"];
                            }
                            $reflectionTable->startRow();
                            $reflectionTable->addCell("<b>" . $commentor . $commentime . "</b>", "", NULL, NULL, NULL, "bgcolor='#D3D3D3'");
                            $reflectionTable->endRow();
                            $reflectionTable->startRow();
                            $reflectionTable->addCell($comment["comment"], "", NULL, NULL, $class, "bgcolor='#FFFFFF'");
                            $reflectionTable->endRow();
                        }
                    }
                    $refNo = $refNo+1;
                }
                unset($item);
            }
            $reflectiontbl = $reflectionobjHeading->show() . $reflectionTable->show();
            return $reflectiontbl;
        } //end if
        
    } //end function
    //Function to get user reflections (one column)
    public function getViewReflections($userId) 
    {
        // Show the heading
        $reflectionobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $reflectionobjHeading->type = 2;
        $reflectionobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordReflections", 'eportfolio');
        //echo $reflectionobjHeading->show();
        $reflectionList = $this->objDbReflectionList->getByItem($userId);
        if (!empty($reflectionList)) {
            // Create a table object
            $reflectionTable = &$this->newObject("htmltable", "htmlelements");
            $reflectionTable->border = 1;
            $reflectionTable->attributes = "rules=none frame=box";
            $reflectionTable->cellspacing = '3';
            $reflectionTable->width = "100%";
            // Add the table heading.
            $reflectionTable->startRow();
            $reflectionTable->addHeaderCell($reflectionobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $reflectionTable->endRow();
            // Step through the list of reflections.
            $class = NULL;
            if (!empty($reflectionList)) {
                $refNo = 1;
                foreach($reflectionList as $item) {
                    // Display each field for reflections
                    $reflectionTable->startRow();
                    $reflectionTable->addCell("<li><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . " : </b>" . $item['rationale'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . " : </b>" . $this->objDate->formatDateOnly($item['creation_date']) ."<p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . " : </b>" . $item['shortdescription'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . " : </b>" . $item['longdescription'] .  "</p></li>");
                    $reflectionTable->endRow();
                    $refNo = $refNo+1;
                }
                unset($item);
            }
            $reflectiontbl = $reflectionTable->show() . '<br></br>';
            return $reflectiontbl;
        } //end if
        
    } //end function
    public function getAssertions($userPid) 
    {
        // Show the heading
        $assertionsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $assertionsobjHeading->type = 2;
        $assertionsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio');
        $Id = $this->_objGroupAdmin->getUserGroups($userPid);
        $emptyChecker = 0;
        if (!empty($Id)) {
            // Create a table object
            $assertionstable = &$this->newObject("htmltable", "htmlelements");
            $assertionstable->border = 1;
            $assertionstable->cellspacing = '1';
            $assertionstable->width = "100%";
            // Add the table heading.
            $assertionstable->startRow();
            $assertionstable->addCell($assertionsobjHeading->show() , '', '', '', '', Null);
            $assertionstable->endRow();
            // Step through the list of assertions.
            $class = NULL;
            if (!empty($Id)) {
                //$assertNo = 1;
                foreach($Id as $groupId) {
                    //Get the group parent_id
                    $parentId = $this->_objGroupAdmin->getParent($groupId);
                    //$newParentId = array_unique($parentId);
                    //foreach($newParentId as $myparentId) {
                        //Get the name from group table
                        $assertionId = $this->_objGroupAdmin->getName($parentId);
                        //$assertionId = $this->_objGroupAdmin->getName($myparentId['parent_id']);
                        $assertionslist = $this->objDbAssertionList->listSingle($assertionId);
                        if (!empty($assertionslist)) {
                            $emptyChecker = 1;
                            // Display each field for assertions
                            $assertionstable->startRow();
                            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_assertion", 'eportfolio') . "&nbsp;&nbsp;" . "</b>");
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("&nbsp;", "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_lecturer", 'eportfolio') . ":</b>");
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell($this->objUser->fullName($assertionslist[0]['userid']) , "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("&nbsp;", "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . ":</b>");
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell($assertionslist[0]['rationale'], "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("&nbsp;", "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . ":</b>");
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell($this->objDate->formatDateOnly($assertionslist[0]['creation_date']) , "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("&nbsp;", "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . ":</b>");
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell($assertionslist[0]['shortdescription'], "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("&nbsp;", "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . ":</b>");
                            $assertionstable->endRow();
                            $assertionstable->startRow();
                            $assertionstable->addCell($assertionslist[0]['longdescription'], "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                        }
                        unset($myparentId);
                    //}
                    //$assertNo = $assertNo + 1;
                    unset($groupId);
                }
            }
            if ($emptyChecker == 1) {
                // Create a table object
                $asserTable = &$this->newObject("htmltable", "htmlelements");
                $asserTable->border = 0;
                $asserTable->cellspacing = '1';
                $asserTable->width = "100%";
                // Add the table heading.
                $asserTable->startRow();
                $asserTable->addCell($assertionstable->show() , '', '', '', '', Null);
                $asserTable->endRow();
                $assertionstbl = $assertionstable->show();
            } else {
                $assertionstbl = "";
            }
            return $assertionstbl;
        } //end if
        
    } //end function
    //Function to get user Assertions (one column)
    public function getViewAssertions($userPid) 
    {
        // Show the heading
        $assertionsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $assertionsobjHeading->type = 2;
        $assertionsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio');
        $Id = $this->_objGroupAdmin->getUserGroups($userPid);
        if (!empty($Id)) {
            // Create a table object
            $assertionstable = &$this->newObject("htmltable", "htmlelements");
            $assertionstable->border = 1;
            $assertionstable->attributes = "rules=none frame=box";
            $assertionstable->cellspacing = '3';
            $assertionstable->width = "100%";
            // Add the table heading.
            $assertionstable->startRow();
            $assertionstable->addHeaderCell($assertionsobjHeading->show() , $width = null, $valign = "top", $align = 'left', $class = 'odd', $attrib = Null);
            $assertionstable->endRow();
            // Step through the list of assertions.
            $class = NULL;
            if (!empty($Id)) {
                //$assertNo = 1;
                foreach($Id as $groupId) {
                    //Get the group parent_id
                    $parentId = $this->_objGroupAdmin->getParent($groupId); 
                    
                    //$newParentId = array_unique($parentId);
                    //foreach($newParentId as $myparentId) {
                        //Get the name from group table
                        //$assertionId = $this->_objGroupAdmin->getName($myparentId['parent_id']);
                     $assertionId = $this->_objGroupAdmin->getName($parentId);
                        $assertionslist = $this->objDbAssertionList->listSingle($assertionId);
                        if (!empty($assertionslist)) {
                            // Display each field for assertions
                            $assertionstable->startRow();
                            $assertionstable->addCell("<li>" . $assertionslist[0]['shortdescription'] . "<p>" . $assertionslist[0]['longdescription'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . ": </b>" . $assertionslist[0]['rationale'] . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_lecturer", 'eportfolio') . ": </b>" . $this->objUser->fullName($assertionslist[0]['userid']) . "</p><p>" . "<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . ": </b>" . $this->objDate->formatDateOnly($assertionslist[0]['creation_date']) . "</p></li>", "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                        }
                        unset($myparentId);
                    //}
                    //$assertNo = $assertNo + 1;
                    unset($groupId);
                }
            }
            $assertionstbl = $assertionstable->show() . '<br></br>';
            return $assertionstbl;
        } //end if
        
    } //end function
    public function viewSingleReflection($id) 
    {
        $reflectionList = $this->objDbReflectionList->listSingle($id);
        //Create a table object
        $reflecTable = &$this->newObject("htmltable", "htmlelements");
        $reflecTable->border = 1;
        $reflecTable->attributes = "rules=none frame=box";
        $reflecTable->cellspacing = '3';
        $reflecTable->cellpadding = '3';
        $reflecTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordReflection", 'eportfolio');
        //Title
        $reflecTable->startRow();
        $reflecTable->addCell($objHeading->show());
        $reflecTable->endRow();
        //Rationale Title
        $reflecTable->startRow();
        $reflecTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
        $reflecTable->endRow();
        $reflecTable->startRow();
        $reflecTable->addCell($reflectionList[0]["rationale"]);
        $reflecTable->endRow();
        //Creation Date
        $reflecTable->startRow();
        $reflecTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
        $reflecTable->endRow();
        $reflecTable->startRow();
        $reflecTable->addCell($reflectionList[0]["creation_date"]);
        $reflecTable->endRow();
        //Short description
        $reflecTable->startRow();
        $reflecTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $reflecTable->endRow();
        $reflecTable->startRow();
        $reflecTable->addCell($reflectionList[0]["shortdescription"]);
        $reflecTable->endRow();
        //Long Description
        $reflecTable->startRow();
        $reflecTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $reflecTable->endRow();
        $reflecTable->startRow();
        $reflecTable->addCell($reflectionList[0]["longdescription"]);
        $reflecTable->endRow();
        //Spacer
        $reflecTable->startRow();
        $reflecTable->addCell("&nbsp;");
        $reflecTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $reflecTable->startRow();
                $reflecTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $reflecTable->endRow();
                $reflecTable->startRow();
                $reflecTable->addCell($comment["comment"]);
                $reflecTable->endRow();
            }
        }
        return $reflecTable->show();
    }
    public function viewSingleAssertion($id) 
    {
        $assertionList = $this->objDbAssertionList->listSingle($id);
        $assertionList = $assertionList[0];
        //Create a table object
        $epartTable = &$this->newObject("htmltable", "htmlelements");
        $epartTable->border = 1;
        $epartTable->attributes = "rules=none frame=box";
        $epartTable->cellspacing = '3';
        $epartTable->cellpadding = '3';
        $epartTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_assertion", 'eportfolio');
        //Title
        $epartTable->startRow();
        $epartTable->addCell($objHeading->show());
        $epartTable->endRow();
        //Name of authority
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("word_name") . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($this->objUser->fullName($assertionList["userid"]));
        $epartTable->endRow();
        //Language
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordlanguage", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($assertionList["language"]);
        $epartTable->endRow();
        //Rationale Title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($assertionList["rationale"]);
        $epartTable->endRow();
        //Creation Date
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($assertionList["creation_date"]);
        $epartTable->endRow();
        //Short description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($assertionList["shortdescription"]);
        $epartTable->endRow();
        //Long Description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($assertionList["longdescription"]);
        $epartTable->endRow();
        //Spacer
        $epartTable->startRow();
        $epartTable->addCell("&nbsp;");
        $epartTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $epartTable->startRow();
                $epartTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $epartTable->endRow();
                $epartTable->startRow();
                $epartTable->addCell($comment["comment"]);
                $epartTable->endRow();
            }
        }
        return $epartTable->show();
    }
    public function viewSingleInterest($id) 
    {
        $interestList = $this->objDbInterestList->listSingle($id);
        $interestList = $interestList[0];
        //Create a table object
        $epartTable = &$this->newObject("htmltable", "htmlelements");
        $epartTable->border = 1;
        $epartTable->attributes = "rules=none frame=box";
        $epartTable->cellspacing = '3';
        $epartTable->cellpadding = '3';
        $epartTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordInterest", 'eportfolio');
        //Title
        $epartTable->startRow();
        $epartTable->addCell($objHeading->show());
        $epartTable->endRow();
        //Name of authority
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("word_name") . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($this->objUser->fullName($interestList["userid"]));
        $epartTable->endRow();
        //Type title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        //Get type name
        $epartTable->startRow();
        $cattype = $this->objDbCategorytypeList->listSingle($interestList['type']);
        $epartTable->addCell($cattype[0]['type']);
        $epartTable->endRow();
        //Creation Date
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($interestList["creation_date"]);
        $epartTable->endRow();
        //Short description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($interestList["shortdescription"]);
        $epartTable->endRow();
        //Long Description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($interestList["longdescription"]);
        $epartTable->endRow();
        //Spacer
        $epartTable->startRow();
        $epartTable->addCell("&nbsp;");
        $epartTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $epartTable->startRow();
                $epartTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $epartTable->endRow();
                $epartTable->startRow();
                $epartTable->addCell($comment["comment"]);
                $epartTable->endRow();
            }
        }
        return $epartTable->show();
    }
    public function viewSingleCompetency($id) 
    {
        $competencyList = $this->objDbCompetencyList->listSingle($id);
        $competencyList = $competencyList[0];
        //Create a table object
        $epartTable = &$this->newObject("htmltable", "htmlelements");
        $epartTable->border = 1;
        $epartTable->attributes = "rules=none frame=box";
        $epartTable->cellspacing = '3';
        $epartTable->cellpadding = '3';
        $epartTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_competency", 'eportfolio');
        //Title
        $epartTable->startRow();
        $epartTable->addCell($objHeading->show());
        $epartTable->endRow();
        //Type title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        //Get type name
        $epartTable->startRow();
        $cattype = $this->objDbCategorytypeList->listSingle($competencyList['type']);
        $epartTable->addCell($cattype[0]['type']);
        $epartTable->endRow();
        //Award Date
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($competencyList["award_date"]);
        $epartTable->endRow();
        //Short description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($competencyList["shortdescription"]);
        $epartTable->endRow();
        //Long Description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($competencyList["longdescription"]);
        $epartTable->endRow();
        //Spacer
        $epartTable->startRow();
        $epartTable->addCell("&nbsp;");
        $epartTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $epartTable->startRow();
                $epartTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $epartTable->endRow();
                $epartTable->startRow();
                $epartTable->addCell($comment["comment"]);
                $epartTable->endRow();
            }
        }
        return $epartTable->show();
    }
    public function viewSingleGoal($id) 
    {
        $goalList = $this->objDbGoalsList->listSingle($id);
        $goalList = $goalList[0];
        //Create a table object
        $epartTable = &$this->newObject("htmltable", "htmlelements");
        $epartTable->border = 1;
        $epartTable->attributes = "rules=none frame=box";
        $epartTable->cellspacing = '3';
        $epartTable->cellpadding = '3';
        $epartTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordGoal", 'eportfolio');
        //Title
        $epartTable->startRow();
        $epartTable->addCell($objHeading->show());
        $epartTable->endRow();
        //Type title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        //Get type name
        $epartTable->startRow();
        $cattype = $this->objDbCategorytypeList->listSingle($goalList['type']);
        $epartTable->addCell($cattype[0]['type']);
        $epartTable->endRow();
        //Show parent goal if any
        if (!empty ( $goalList['parentid'] )) {
          //parent goal
          $epartTable->startRow();
          $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
          $epartTable->endRow();
          $epartTable->startRow();
          //Get parent goal and display it
          $epartTable->startRow();
          $pargoal = $this->objDbGoalsList->listSingle($goalList['parentid']);
          $epartTable->addCell($pargoal[0]['shortdescription']);
          $epartTable->endRow();
        }
        //Start Date
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($goalList["start"]);
        $epartTable->endRow();
        //Status
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordstatus", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($goalList["status"]);
        $epartTable->endRow();
        //Status Date
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_statusDate", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($goalList["status_date"]);
        $epartTable->endRow();
        //Short description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($goalList["shortdescription"]);
        $epartTable->endRow();
        //Long Description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($goalList["longdescription"]);
        $epartTable->endRow();
        //Spacer
        $epartTable->startRow();
        $epartTable->addCell("&nbsp;");
        $epartTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $epartTable->startRow();
                $epartTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $epartTable->endRow();
                $epartTable->startRow();
                $epartTable->addCell($comment["comment"]);
                $epartTable->endRow();
            }
        }
        return $epartTable->show();
    } 
    public function viewSingleQuali($id) 
    {
        $qualiList = $this->objDbQclList->listSingle($id);
        $qualiList = $qualiList[0];
        //Create a table object
        $epartTable = &$this->newObject("htmltable", "htmlelements");
        $epartTable->border = 1;
        $epartTable->attributes = "rules=none frame=box";
        $epartTable->cellspacing = '3';
        $epartTable->cellpadding = '3';
        $epartTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_qualification", 'eportfolio');
        //Title
        $epartTable->startRow();
        $epartTable->addCell($objHeading->show());
        $epartTable->endRow();
        //Type title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        //Get type name
        $epartTable->startRow();
        $cattype = $this->objDbCategorytypeList->listSingle($qualiList['qcl_type']);
        $epartTable->addCell($cattype[0]['type']);
        $epartTable->endRow();
        //Qualification Title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordtitle", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($qualiList["qcl_title"]);
        $epartTable->endRow();
        //Organisation
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($qualiList["organisation"]);
        $epartTable->endRow();
        //QCL Level
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_level", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($qualiList["qcl_level"]);
        $epartTable->endRow();
        //QCL Award Date
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($qualiList["award_date"]);
        $epartTable->endRow();
        //Short description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($qualiList["shortdescription"]);
        $epartTable->endRow();
        //Long Description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($qualiList["longdescription"]);
        $epartTable->endRow();
        //Spacer
        $epartTable->startRow();
        $epartTable->addCell("&nbsp;");
        $epartTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $epartTable->startRow();
                $epartTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $epartTable->endRow();
                $epartTable->startRow();
                $epartTable->addCell($comment["comment"]);
                $epartTable->endRow();
            }
        }
        return $epartTable->show();
    }
    public function viewSingleTranscript($id) 
    {
        $transList = $this->objDbTranscriptList->listSingle($id);
        $transList = $transList[0];
        //Create a table object
        $epartTable = &$this->newObject("htmltable", "htmlelements");
        $epartTable->border = 1;
        $epartTable->attributes = "rules=none frame=box";
        $epartTable->cellspacing = '3';
        $epartTable->cellpadding = '3';
        $epartTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordtranscript", 'eportfolio');
        //Title
        $epartTable->startRow();
        $epartTable->addCell($objHeading->show());
        $epartTable->endRow();
        //Type title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        //Get type name
        $epartTable->startRow();
        $cattype = $this->objDbCategorytypeList->listSingle($transList['type']);
        $epartTable->addCell($transList['type']);
        $epartTable->endRow();
        //Short description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($transList["shortdescription"]);
        $epartTable->endRow();
        //Long Description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($transList["longdescription"]);
        $epartTable->endRow();
        //Spacer
        $epartTable->startRow();
        $epartTable->addCell("&nbsp;");
        $epartTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $epartTable->startRow();
                $epartTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $epartTable->endRow();
                $epartTable->startRow();
                $epartTable->addCell($comment["comment"]);
                $epartTable->endRow();
            }
        }
        return $epartTable->show();
    }
    public function viewSingleAffiliation($id) 
    {
        $affiList = $this->objDbAffiliationList->listSingle($id);
        $affiList = $affiList[0];
        //Create a table object
        $epartTable = &$this->newObject("htmltable", "htmlelements");
        $epartTable->border = 1;
        $epartTable->attributes = "rules=none frame=box";
        $epartTable->cellspacing = '3';
        $epartTable->cellpadding = '3';
        $epartTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio');
        //Title
        $epartTable->startRow();
        $epartTable->addCell($objHeading->show());
        $epartTable->endRow();
        //Type title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        //Get type name
        $epartTable->startRow();
        $cattype = $this->objDbCategorytypeList->listSingle($affiList['type']);
        $epartTable->addCell($cattype[0]['type']);
        $epartTable->endRow();
        //Classification
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_classificationView", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($affiList["classification"]);
        $epartTable->endRow();
        //Role
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_roleView", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($affiList["role"]);
        $epartTable->endRow();
        //Organisation
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($affiList["organisation"]);
        $epartTable->endRow();
        //Start
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($this->objDate->formatDateOnly($affiList["start"]));
        $epartTable->endRow();
        //Finish
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($this->objDate->formatDateOnly($affiList["finish"]));
        $epartTable->endRow();
        //Short description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($affiList["shortdescription"]);
        $epartTable->endRow();
        //Long Description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($affiList["longdescription"]);
        $epartTable->endRow();
        //Spacer
        $epartTable->startRow();
        $epartTable->addCell("&nbsp;");
        $epartTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $epartTable->startRow();
                $epartTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $epartTable->endRow();
                $epartTable->startRow();
                $epartTable->addCell($comment["comment"]);
                $epartTable->endRow();
            }
        }
        return $epartTable->show();
    }
    public function viewSingleActivity($id) 
    {
        $atyList = $this->objDbActivityList->listSingle($id);
        $atyList = $atyList[0];
        //Create a table object
        $epartTable = &$this->newObject("htmltable", "htmlelements");
        $epartTable->border = 1;
        $epartTable->attributes = "rules=none frame=box";
        $epartTable->cellspacing = '3';
        $epartTable->cellpadding = '3';
        $epartTable->width = "100%";
        //Title
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_activity", 'eportfolio');
        //Title
        $epartTable->startRow();
        $epartTable->addCell($objHeading->show());
        $epartTable->endRow();
        //Show course if any
        if( !empty( $affiList['contextid']) ) {
          //Course
          $epartTable->startRow();
          $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . "</b>");
          $epartTable->endRow();
          $epartTable->startRow();
          //Get course name
          $epartTable->startRow();
          $contextdetails = $this->objDBContext->getContextDetails($atyList['type']);
          $epartTable->addCell($contextdetails['title']);
          $epartTable->endRow();
        }
        //Type title
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        //Get type name
        $epartTable->startRow();
        $cattype = $this->objDbCategorytypeList->listSingle($atyList['type']);
        $epartTable->addCell($cattype[0]['type']);
        $epartTable->endRow();
        //Start
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($this->objDate->formatDateOnly($atyList["start"]));
        $epartTable->endRow();
        //Finish
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($this->objDate->formatDateOnly($atyList["finish"]));
        $epartTable->endRow();
        //Short description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($atyList["shortdescription"]);
        $epartTable->endRow();
        //Long Description
        $epartTable->startRow();
        $epartTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
        $epartTable->endRow();
        $epartTable->startRow();
        $epartTable->addCell($atyList["longdescription"]);
        $epartTable->endRow();
        //Spacer
        $epartTable->startRow();
        $epartTable->addCell("&nbsp;");
        $epartTable->endRow();
        //row for comments
        $mycomments = $this->objDbComment->listAll($id);
        if (!empty($mycomments)) {
            foreach($mycomments as $comment) {
                //$this->objUser
                $commentor = $this->objUser->fullName($comment["commentoruserid"]);
                $commentime = "";
                if (!empty($comment["postdate"])) {
                    $commentime = " : " . $comment["postdate"];
                }
                $epartTable->startRow();
                $epartTable->addCell("<b>" . $commentor . $commentime . "</b>");
                $epartTable->endRow();
                $epartTable->startRow();
                $epartTable->addCell($comment["comment"]);
                $epartTable->endRow();
            }
        }
        return $epartTable->show();
    }
   /**
    * This method returns the form used to display an eportfolio
    * part
    * @param string $prevaction The previous action
    * @param string $eportpartidvarname The variable name holding
    * the eportfolioid for the previous action
    * @param string $eportfoliopartid The id for the part to be viewed
    * @author Megan Watson - added functions from kinky
    * @returns string $surname
    */
    public function viewPartForm( $prevaction, $eportpartidvarname, $eportfoliopartid ){
        // Load classes.
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass("button", "htmlelements");
        $this->loadClass("htmltable", 'htmlelements');

        $form = new form("add", $this->uri(array(
            'module' => 'eportfolio',
            'action' => 'postcomment',
            'prevaction' => $prevaction,
            'eportpartidvarname' => $eportpartidvarname,
            'eportfoliopartid' => $eportfoliopartid
        )));
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 3;
        $objHeading->str = $this->objLanguage->languageText("mod_eportfolio_postcomment", 'eportfolio');
        //table object
        $epTable = &$this->newObject("htmltable", "htmlelements");
        $epTable->width = '100%';
        //$epTable->attributes = " align='left' border='0'";
        $epTable->cellspacing = '5';
        //row for author comments
        $epTable->startRow();
        $epTable->addCell($objHeading->show());
        $epTable->endRow();
        //new comment text field
        $textinput = new textarea("newcomment", '');
        $form->addRule('newcomment', 'Please type a comment', 'required');
        $epTable->startRow();
        $epTable->addCell($textinput->show());
        $epTable->endRow();
        //Submit button
        $button = new button("submit", $this->objLanguage->languageText("word_submit", "system"));
        $button->setToSubmit();
        $epTable->startRow();
        $epTable->addCell($button->show());
        $epTable->endRow();
        $epTable->startRow();
        $epTable->addCell("&nbsp;");
        $epTable->endRow();
        $form->addToForm($epTable->show());
        return $form->show();
    }
    public function getCloseBtn() {
        //Get Object
        $this->objIcon = &$this->newObject('geticon', 'htmlelements');
        $objLayer3 = $this->newObject('layer', 'htmlelements');
        $this->objIcon->setIcon('close');
        $this->objIcon->extra = " onclick='javascript:window.close()'";
        $objLayer3->align = 'center';
        $objLayer3->str = $this->objIcon->show();
        return $objLayer3->show();
    }
}
?>
