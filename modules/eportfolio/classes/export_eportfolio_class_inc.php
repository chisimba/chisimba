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
class export_Eportfolio extends object
{
    /**
     *
     * Intialiser for the getall_Eportfolio controller
     * @access public
     *
     */
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
        $this->objDbCompetencyList = &$this->getObject('dbeportfolio_competency', 'eportfolio');
        $this->objDbInterestList = &$this->getObject('dbeportfolio_interest', 'eportfolio');
        $this->objDbReflectionList = &$this->getObject('dbeportfolio_reflection', 'eportfolio');
        $this->objDbAssertionList = &$this->getObject('dbeportfolio_assertion', 'eportfolio');
        $this->_objGroupAdmin = &$this->newObject('groupadminmodel', 'groupadmin');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->objDate = &$this->newObject('dateandtime', 'utilities');
        //$objLanguage =& $this->getObject('language','language');
        //        $this->objLanguage = &$this->getObject('language', 'language');
        
    }
    public function exportAddress($userId) 
    {
        $objaddressTitles = &$this->getObject('htmlheading', 'htmlelements');
        $objaddressTitles->type = 3;
        //$objLanguage =& $this->getObject('language','language');
        $addressList = $this->objDbAddressList->getByItem($userId);
        if (!empty($addressList)) {
            /*
            // Create a table object
            $addressTable =& $this->newObject("htmltable","htmlelements");
            $addressTable->border = 0;
            $addressTable->cellspacing='12';
            $addressTable->width = "100%";
            $objaddressTitles->str =$this->objLanguage->languageText("mod_eportfolio_wordAddress", 'eportfolio');
            
            // Add the table heading.
            $addressTable->startRow();
            $addressTable->addCell($objaddressTitles->show(), '', '', '','', 'colspan="8"');
            
            $addressTable->endRow();
            $addressTable->startRow();
            $addressTable->addCell("<b>".$this->objLanguage->languageText("mod_eportfolio_contypes",'eportfolio')."</b>");
            $addressTable->addCell("<b>".$this->objLanguage->languageText("mod_eportfolio_streetno",'eportfolio')."</b>");
            $addressTable->addCell("<b>".$this->objLanguage->languageText("mod_eportfolio_streetname",'eportfolio')."</b>");
            $addressTable->addCell("<b>".$this->objLanguage->languageText("mod_eportfolio_locality",'eportfolio')."</b>");
            $addressTable->addCell("<b>".$this->objLanguage->languageText("mod_eportfolio_city",'eportfolio')."</b>");
            $addressTable->addCell("<b>".$this->objLanguage->languageText("mod_eportfolio_postcode",'eportfolio')."</b>");
            $addressTable->addCell("<b>".$this->objLanguage->languageText("mod_eportfolio_postaddress",'eportfolio')."</b>");
            $addressTable->endRow();
            
            // Step through the list of addresses.
            if (!empty($addressList)) {
            foreach ($addressList as $addressItem) {
            // Display each field for addresses
            $addressTable->startRow();
            $cattype = $this->objDbCategorytypeList->listSingle($addressItem['type']);
            //$addressTable->startRow();
            $addressTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['street_no'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['street_name'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['locality'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['city'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['postcode'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['postal_address'], "", NULL, NULL, NULL, '');
            $addressTable->endRow();
            
            }
            */
            //Table name
            $table_id = 'tbl_address';
            //create new XML document
            $doc = new DomDocument('1.0');
            //create root node
            $root = $doc->createElement('root');
            $root = $doc->appendChild($root);
            var_dump($addressList);
            //process one row at a time
            //            while ($row = mysql_fetch_assoc($addressList)) {
            while ($rowcount = count($addressList)) {
                //add node for each row
                $occ = $doc->createElement($table_id);
                $occ = $root->appendChild($occ);
                //add a child node for each field
                foreach($row as $fieldname => $fieldvalue) {
                    //create a new element for the field and then insert it as a child to the current db row
                    $child = $doc->createElement($fieldname);
                    $child = $occ->appendChild($child);
                    //add the field value as a text node
                    $value = $doc->createTextNode($fieldvalue);
                    $value = $child->appendChild($value);
                } //foreach
                
            } //while
            //get completed xml document
            $xml_string = $doc->saveXML();
            //echo $xml_string;
            unset($addressItem);
        } else {
            $xml_string = "<root>no records</root>";
            /*
            $addressTable->startRow();
            $addressTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="8"');
            
            $addressTable->endRow();
            */
        }
        //	$addressLabel = $addressTable->show();
        //	return $addressLabel;
        return $xml_string;
        //}//end if
        
    } //end function
    public function getContacts($userId) 
    {
        //$objLanguage =& $this->getObject('language','language');
        // Show the heading
        $objcontactTitles = &$this->getObject('htmlheading', 'htmlelements');
        $objcontactTitles->type = 3;
        $objcontactTitles->str = $this->objLanguage->languageText("mod_eportfolio_wordContact", 'eportfolio');
        $contactList = $this->objDbContactList->getByItem($userId);
        if (!empty($contactList)) {
            //$emailList = $this->objDbEmailList->getByItem($userId);
            // Create a table object
            $contactTable = &$this->newObject("htmltable", "htmlelements");
            $contactTable->border = 0;
            $contactTable->cellspacing = '3';
            $contactTable->width = "100%";
            // Add the table heading.
            $contactTable->startRow();
            $contactTable->addCell($objcontactTitles->show() , '', '', '', '', 'colspan="5"');
            $contactTable->endRow();
            $contactTable->startRow();
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contacttype", 'eportfolio') . "</b>");
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio') . "</b>");
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio') . "</b>");
            $contactTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contactnumber", 'eportfolio') . "</b>");
            $contactTable->endRow();
            // Step through the list of contacts
            if (!empty($contactList)) {
                foreach($contactList as $contactItem) {
                    // Display each field for contacts
                    $cattype = $this->objDbCategorytypeList->listSingle($contactItem['type']);
                    $modetype = $this->objDbCategorytypeList->listSingle($contactItem['contact_type']);
                    $contactTable->startRow();
                    $contactTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
                    $contactTable->addCell($modetype[0]['type'], "", NULL, NULL, NULL, '');
                    $contactTable->addCell($contactItem['country_code'], "", NULL, NULL, NULL, '');
                    $contactTable->addCell($contactItem['area_code'], "", NULL, NULL, NULL, '');
                    $contactTable->addCell($contactItem['id_number'], "", NULL, NULL, NULL, '');
                    $contactTable->endRow();
                }
                unset($contactItem);
            } else {
                $contactTable->startRow();
                $contactTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
                $contactTable->endRow();
            }
            //echo $contactTable->show();
            $contactTable->startRow();
            $contactTable->addCell('', '', '', '', 'noRecordsMessage', 'colspan="5"');
            $contactTable->endRow();
            $contacts = $contactTable->show();
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
            $emailobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_emailList", 'eportfolio');
            //echo $emailobjHeading->show();
            // Create a table object for emails
            $emailTable = &$this->newObject("htmltable", "htmlelements");
            $emailTable->border = 0;
            $emailTable->cellspacing = '3';
            $emailTable->width = "50%";
            // Add the table heading.
            $emailTable->startRow();
            $emailTable->addCell($emailobjHeading->show() , '', '', '', '', 'colspan="3"');
            $emailTable->endRow();
            $emailTable->startRow();
            $emailTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
            $emailTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_email", 'eportfolio') . "</b>");
            $emailTable->endRow();
            // Step through the list of addresses.
            $class = 'even';
            if (!empty($emailList)) {
                foreach($emailList as $emailItem) {
                    // Display each field for addresses
                    $cattype = $this->objDbCategorytypeList->listSingle($emailItem['type']);
                    $emailTable->startRow();
                    $emailTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
                    $emailTable->addCell($emailItem['email'], "", NULL, NULL, NULL, '');
                    $emailTable->endRow();
                }
                unset($emailItem);
            } else {
                $emailTable->startRow();
                $emailTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
                $emailTable->endRow();
            }
            $emailTable->startRow();
            $emailTable->addCell('', '', '', '', 'noRecordsMessage', 'colspan="3"');
            $emailTable->endRow();
            $emailtbl = $emailTable->show();
            return $emailtbl;
        } //end if
        
    } //end function
    public function getDemographics($userId) 
    {
        //Demographics view
        //$objLanguage =& $this->getObject('language','language');
        $demographicsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $demographicsList = $this->objDbDemographicsList->getByItem($userId);
        if (!empty($demographicsList)) {
            // Create a table object
            $demographicsTable = &$this->newObject("htmltable", "htmlelements");
            $demographicsTable->border = 0;
            $demographicsTable->cellspacing = '3';
            $demographicsTable->width = "50%";
            // Show the heading
            $demographicsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio');
            $demographicsTable->startRow();
            $demographicsTable->addCell($demographicsobjHeading->show() , '', '', '', '', 'colspan="4"');
            $demographicsTable->endRow();
            $demographicsTable->startRow();
            $demographicsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
            $demographicsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_birth", 'eportfolio') . "</b>");
            $demographicsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio') . "</b>");
            $demographicsTable->endRow();
            // Step through the list of addresses.
            if (!empty($demographicsList)) {
                foreach($demographicsList as $demographicsItem) {
                    // Display each field for Demographics
                    $cattype = $this->objDbCategorytypeList->listSingle($demographicsItem['type']);
                    $demographicsTable->startRow();
                    $demographicsTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
                    $demographicsTable->addCell($this->objDate->formatDate($demographicsItem['birth']) , "", NULL, NULL, NULL, '');
                    $demographicsTable->addCell($demographicsItem['nationality'], "", NULL, NULL, NULL, '');
                    $demographicsTable->endRow();
                }
                unset($demographicsItem);
            } else {
                $demographicsTable->startRow();
                $demographicsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
                $demographicsTable->endRow();
            }
            $demographicstbl = $demographicsTable->show();
            return $demographicstbl;
        } //end if
        
    } //end function
    public function getActivity($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $activityobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $activityobjHeading->type = 3;
        $activityobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio');
        $activitylist = $this->objDbActivityList->getByItem($userId);
        if (!empty($activitylist)) {
            // Create a table object
            $activityTable = &$this->newObject("htmltable", "htmlelements");
            $activityTable->border = 0;
            $activityTable->cellspacing = '3';
            $activityTable->width = "100%";
            // Add the table heading.
            $activityTable->startRow();
            $activityTable->addCell($activityobjHeading->show() , '', '', '', '', 'colspan="6"');
            $activityTable->endRow();
            $activityTable->startRow();
            $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . "</b>");
            $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitytype", 'eportfolio') . "</b>");
            $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
            $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
            $activityTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
            $activityTable->endRow();
            // Step through the list of activities
            $class = NULL;
            if (!empty($activitylist)) {
                $i = 0;
                foreach($activitylist as $item) {
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
                    $activityTable->addCell($mycontextTitle, "", NULL, NULL, $class, '');
                    $activityTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
                    $activityTable->addCell($this->objDate->formatDate($item['start']) , "", NULL, NULL, $class, '');
                    $activityTable->addCell($this->objDate->formatDate($item['finish']) , "", NULL, NULL, $class, '');
                    $activityTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
                    $activityTable->endRow();
                }
                unset($item);
            } else {
                $activityTable->startRow();
                $activityTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
                $activityTable->endRow();
            }
            $activitytbl = $activityTable->show();
            return $activitytbl;
        } //end if
        
    } //end function
    public function getAffiliation($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $affiliationobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $affiliationobjHeading->type = 3;
        $affiliationobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio');
        $affiliationList = $this->objDbAffiliationList->getByItem($userId);
        if (!empty($affiliationList)) {
            // Create a table object
            $affiliationTable = &$this->newObject("htmltable", "htmlelements");
            $affiliationTable->border = 0;
            $affiliationTable->cellspacing = '12';
            $affiliationTable->width = "100%";
            // Add the table heading.
            $affiliationTable->startRow();
            $affiliationTable->addCell($affiliationobjHeading->show() , '', '', '', '', 'colspan="8"');
            $affiliationTable->endRow();
            $affiliationTable->startRow();
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_classificationView", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_roleView", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
            $affiliationTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
            $affiliationTable->endRow();
            // Step through the list of addresses.
            $class = NULL;
            if (!empty($affiliationList)) {
                $i = 0;
                foreach($affiliationList as $affiliationItem) {
                    // Display each field for addresses
                    $cattype = $this->objDbCategorytypeList->listSingle($affiliationItem['type']);
                    $affiliationTable->startRow();
                    $affiliationTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
                    $affiliationTable->addCell($affiliationItem['classification'], "", NULL, NULL, $class, '');
                    $affiliationTable->addCell($affiliationItem['role'], "", NULL, NULL, $class, '');
                    $affiliationTable->addCell($affiliationItem['organisation'], "", NULL, NULL, $class, '');
                    $affiliationTable->addCell($this->objDate->formatDate($affiliationItem['start']) , "", NULL, NULL, $class, '');
                    $affiliationTable->addCell($this->objDate->formatDate($affiliationItem['finish']) , "", NULL, NULL, $class, '');
                    $affiliationTable->endRow();
                }
                unset($affiliationItem);
            } else {
                $affiliationTable->startRow();
                $affiliationTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="8"');
                $affiliationTable->endRow();
            }
            $affiliationtbl = $affiliationTable->show();
            return $affiliationtbl;
        } //end if
        
    } //end function
    public function getTranscripts($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $transcriptobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $transcriptobjHeading->type = 3;
        $transcriptobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordTranscripts", 'eportfolio');
        // echo $transcriptobjHeading->show();
        $transcriptlist = $this->objDbTranscriptList->getByItem($userId);
        if (!empty($transcriptlist)) {
            // Create a table object
            $transcriptTable = &$this->newObject("htmltable", "htmlelements");
            $transcriptTable->border = 0;
            $transcriptTable->cellspacing = '12';
            $transcriptTable->width = "50%";
            // Add the table heading.
            $transcriptTable->startRow();
            $transcriptTable->addCell($transcriptobjHeading->show() , '', '', '', '', 'colspan="2"');
            $transcriptTable->endRow();
            $transcriptTable->startRow();
            $transcriptTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
            $transcriptTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
            $transcriptTable->endRow();
            // Step through the list of addresses.
            $class = NULL;
            if (!empty($transcriptlist)) {
                foreach($transcriptlist as $item) {
                    // Display each field for activities
                    $transcriptTable->startRow();
                    $transcriptTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
                    $transcriptTable->addCell($item['longdescription'], "", NULL, NULL, $class, '');
                    $transcriptTable->endRow();
                }
                unset($item);
            } else {
                $transcriptTable->startRow();
                $transcriptTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
                $transcriptTable->endRow();
            }
            $transcripttbl = $transcriptTable->show();
            return $transcripttbl;
        } //end if
        
    } //end function
    public function getQualification($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $qclobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $qclobjHeading->type = 3;
        $qclobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio');
        //echo $qclobjHeading->show();
        $qclList = $this->objDbQclList->getByItem($userId);
        if (!empty($qclList)) {
            // Create a table object
            $qclTable = &$this->newObject("htmltable", "htmlelements");
            $qclTable->border = 0;
            $qclTable->cellspacing = '3';
            $qclTable->width = "100%";
            // Add the table heading.
            $qclTable->startRow();
            $qclTable->addCell($qclobjHeading->show() , '', '', '', '', 'colspan="6"');
            $qclTable->endRow();
            $qclTable->startRow();
            $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
            $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_wordtitle", 'eportfolio') . "</b>");
            $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
            $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_level", 'eportfolio') . "</b>");
            $qclTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
            //$qclTable->addHeaderCell("<b>".$objLanguage->languageText("mod_eportfolio_shortdescription",'eportfolio')."</b>");
            $qclTable->endRow();
            // Step through the list of addresses.
            $class = NULL;
            if (!empty($qclList)) {
                foreach($qclList as $qclItem) {
                    // Display each field for addresses
                    $cattype = $this->objDbCategorytypeList->listSingle($qclItem['qcl_type']);
                    $qclTable->startRow();
                    $qclTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
                    $qclTable->addCell($qclItem['qcl_title'], "", NULL, NULL, $class, '');
                    $qclTable->addCell($qclItem['organisation'], "", NULL, NULL, $class, '');
                    $qclTable->addCell($qclItem['qcl_level'], "", NULL, NULL, $class, '');
                    $qclTable->addCell($this->objDate->formatDate($qclItem['award_date']) , "", NULL, NULL, $class, '');
                    $qclTable->endRow();
                }
                unset($qclItem);
            } else {
                $qclTable->startRow();
                $qclTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
                $qclTable->endRow();
            }
            $qcltbl = $qclTable->show();
            return $qcltbl;
        } //end if
        
    } //end function
    public function getGoals($userId) 
    {
        //Language Items
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        // Show the heading
        $goalsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $goalsobjHeading->type = 3;
        $goalsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordGoals", 'eportfolio');
        $goalsList = $this->objDbGoalsList->getByItem($userId);
        if (!empty($goalsList)) {
            // Create a table object
            $goalsTable = &$this->newObject("htmltable", "htmlelements");
            $goalsTable->border = 0;
            $goalsTable->cellspacing = '12';
            $goalsTable->width = "60%";
            // Add the table heading.
            $goalsTable->startRow();
            $goalsTable->addCell($goalsobjHeading->show() , '', '', '', '', 'colspan="2"');
            $goalsTable->endRow();
            $goalsTable->startRow();
            $goalsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
            $goalsTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
            $goalsTable->endRow();
            // Step through the list of addresses.
            $class = NULL;
            if (!empty($goalsList)) {
                $i = 0;
                foreach($goalsList as $item) {
                    // Display each field for activities
                    $goalsTable->startRow();
                    $goalsTable->addCell("<li>" . $item['shortdescription'] . "</li>", "", NULL, NULL, $class, '');
                    $goalsTable->addCell("<li>" . $item['longdescription'] . "</li>", "", NULL, NULL, $class, '');
                    $goalsTable->endRow();
                }
                unset($item);
            } else {
                $goalsTable->startRow();
                $goalsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
                $goalsTable->endRow();
            }
            $goalstbl = $goalsTable->show();
            return $goalstbl;
        } //end if
        
    } //end function
    public function getCompetency($userId) 
    {
        // Show the heading
        $competencyobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $competencyobjHeading->type = 3;
        $competencyobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio');
        $competencyList = $this->objDbCompetencyList->getByItem($userId);
        if (!empty($competencyList)) {
            // Create a table object
            $competencyTable = &$this->newObject("htmltable", "htmlelements");
            $competencyTable->border = 0;
            $competencyTable->cellspacing = '12';
            $competencyTable->width = "100%";
            // Add the table heading.
            $competencyTable->startRow();
            $competencyTable->addCell($competencyobjHeading->show() , '', '', '', '', 'colspan="4"');
            $competencyTable->endRow();
            $competencyTable->startRow();
            $competencyTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
            $competencyTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
            $competencyTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
            $competencyTable->endRow();
            // Step through the list of addresses.
            $class = NULL;
            if (!empty($competencyList)) {
                foreach($competencyList as $item) {
                    // Display each field for activities
                    $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
                    $competencyTable->startRow();
                    $competencyTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
                    $competencyTable->addCell($this->objDate->formatDate($item['award_date']) , "", NULL, NULL, $class, '');
                    $competencyTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
                    $competencyTable->endRow();
                }
                unset($item);
                $competencytbl = $competencyTable->show();
                return $competencytbl;
            }
        } //end if
        
    } //end function
    public function getInterests($userId) 
    {
        // Show the heading
        $interestobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $interestobjHeading->type = 3;
        $interestobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordInterests", 'eportfolio');
        //echo $interestobjHeading->show();
        $interestList = $this->objDbInterestList->getByItem($userId);
        if (!empty($interestList)) {
            // Create a table object
            $interestTable = &$this->newObject("htmltable", "htmlelements");
            $interestTable->border = 0;
            $interestTable->cellspacing = '12';
            $interestTable->width = "100%";
            // Add the table heading.
            $interestTable->startRow();
            $interestTable->addCell($interestobjHeading->show() , '', '', '', '', 'colspan="4"');
            $interestTable->endRow();
            $interestTable->startRow();
            $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
            $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
            $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
            $interestTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
            $interestTable->endRow();
            // Step through the list of addresses.
            $class = NULL;
            if (!empty($interestList)) {
                foreach($interestList as $item) {
                    // Display each field for activities
                    $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
                    $interestTable->startRow();
                    $interestTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
                    $interestTable->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, $class, '');
                    $interestTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
                    $interestTable->addCell($item['longdescription'], "", NULL, NULL, $class, '');
                    $interestTable->endRow();
                }
                unset($item);
                $interesttbl = $interestTable->show();
                return $interesttbl;
            }
        } //end if
        
    } //end function
    public function getReflections($userId) 
    {
        // Show the heading
        $reflectionobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $reflectionobjHeading->type = 3;
        $reflectionobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordReflections", 'eportfolio');
        //echo $reflectionobjHeading->show();
        $reflectionList = $this->objDbReflectionList->getByItem($userId);
        if (!empty($reflectionList)) {
            // Create a table object
            $reflectionTable = &$this->newObject("htmltable", "htmlelements");
            $reflectionTable->border = 0;
            $reflectionTable->cellspacing = '3';
            $reflectionTable->width = "100%";
            // Add the table heading.
            $reflectionTable->startRow();
            $reflectionTable->addCell($reflectionobjHeading->show() , '', '', '', '', 'colspan="4"');
            $reflectionTable->endRow();
            $reflectionTable->startRow();
            $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
            $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
            $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
            $reflectionTable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
            $reflectionTable->endRow();
            // Step through the list of reflections.
            $class = NULL;
            if (!empty($reflectionList)) {
                foreach($reflectionList as $item) {
                    // Display each field for activities
                    $reflectionTable->startRow();
                    $reflectionTable->addCell($item['rationale'], "", NULL, NULL, $class, '');
                    $reflectionTable->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, $class, '');
                    $reflectionTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
                    $reflectionTable->addCell($item['longdescription'], "", NULL, NULL, $class, '');
                    $reflectionTable->endRow();
                }
                unset($item);
            }
            $reflectiontbl = $reflectionTable->show();
            return $reflectiontbl;
        } //end if
        
    } //end function
    public function getAssertions($userPid) 
    {
        // Show the heading
        $assertionsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
        $assertionsobjHeading->type = 3;
        $assertionsobjHeading->str = $this->objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio');
        $Id = $this->_objGroupAdmin->getUserGroups($userPid);
        if (!empty($Id)) {
            // Create a table object
            $assertionstable = &$this->newObject("htmltable", "htmlelements");
            $assertionstable->border = 0;
            $assertionstable->cellspacing = '3';
            $assertionstable->width = "100%";
            // Add the table heading.
            $assertionstable->startRow();
            $assertionstable->addCell($assertionsobjHeading->show() , '', '', '', '', 'colspan="5"');
            $assertionstable->endRow();
            $assertionstable->startRow();
            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_lecturer", 'eportfolio') . "</b>");
            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
            $assertionstable->addCell("<b>" . $this->objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . "</b>");
            $assertionstable->endRow();
            // Step through the list of addresses.
            $class = NULL;
            if (!empty($Id)) {
                foreach($Id as $groupId) {
                    //Get the group parent_id
                    $parentId = $this->_objGroupAdmin->getParent($groupId);
                    foreach($parentId as $myparentId) {
                        //Get the name from group table
                        $assertionId = $this->_objGroupAdmin->getName($myparentId['parent_id']);
                        $assertionslist = $this->objDbAssertionList->listSingle($assertionId);
                        if (!empty($assertionslist)) {
                            // Display each field for activities
                            $assertionstable->startRow();
                            $assertionstable->addCell($this->objUser->fullName($assertionslist[0]['userid']) , "", NULL, NULL, $class, '');
                            $assertionstable->addCell($assertionslist[0]['rationale'], "", NULL, NULL, $class, '');
                            $assertionstable->addCell($this->objDate->formatDate($assertionslist[0]['creation_date']) , "", NULL, NULL, $class, '');
                            $assertionstable->addCell($assertionslist[0]['shortdescription'], "", NULL, NULL, $class, '');
                            $assertionstable->addCell($assertionslist[0]['longdescription'], "", NULL, NULL, $class, '');
                            $assertionstable->endRow();
                        }
                        unset($myparentId);
                    }
                    unset($groupId);
                }
            }
            $assertionstbl = $assertionstable->show();
            return $assertionstbl;
        } //end if
        
    } //end function
    
}
?>
