<?php
/* ----------- mysql2xml_Eportfolio class extends object------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for creating XML from mysql
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape & University of Nairobi
 */
class mysqlxml_Eportfolio extends object
{
    var $recordSet;
    var $xml;
    var $objXMLTag;
    var $objXMLFile;
    public function init() 
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objDbFile = &$this->getObject('dbfile', 'filemanager');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->xmlIdentification = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlQcl = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlAffiliation = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlTranscript = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlActivity = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlAssertion = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlReflection = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlCompetency = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlInterest = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlGoal = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->xmlImsManifest = &$this->getObject('xmlfile_eportfolio', 'eportfolio');
        $this->createZipFile = &$this->getObject('createzipfile_eportfolio', 'eportfolio');
        $this->objDbCategoryList = &$this->getObject('dbeportfolio_category', 'eportfolio');
        $this->objDbCategorytypeList = &$this->getObject('dbeportfolio_categorytypes', 'eportfolio');
        $this->objDbAddressList = &$this->getObject('dbeportfolio_address', 'eportfolio');
        $this->objDbContactList = &$this->getObject('dbeportfolio_contact', 'eportfolio');
        $this->objDbDemographicsList = &$this->getObject('dbeportfolio_demographics', 'eportfolio');
        $this->objDbQclList = &$this->getObject('dbeportfolio_qcl', 'eportfolio');
        $this->objDbAffiliationList = &$this->getObject('dbeportfolio_affiliation', 'eportfolio');
        $this->objDbTranscriptList = &$this->getObject('dbeportfolio_transcript', 'eportfolio');
        $this->objDbActivityList = &$this->getObject('dbeportfolio_activity', 'eportfolio');
        $this->objDbEmailList = &$this->getObject('dbeportfolio_Email', 'eportfolio');
        $this->objDbAssertionList = &$this->getObject('dbeportfolio_assertion', 'eportfolio');
        $this->objDbReflectionList = &$this->getObject('dbeportfolio_reflection', 'eportfolio');
        $this->objDbCompetencyList = &$this->getObject('dbeportfolio_competency', 'eportfolio');
        $this->objDbInterestList = &$this->getObject('dbeportfolio_interest', 'eportfolio');
        $this->objDbGoalList = &$this->getObject('dbeportfolio_goals', 'eportfolio');
        $this->userId = $this->objUser->userId(); //To pick user userid
        
    }
    // Export to XML
    public function convertToXML() 
    {
        //Identification Path
        $identFilename = 'Identification.xml';
        $identPath = 'users/' . $this->userId . '/' . $identFilename;
        $identMyFile = $this->objConfig->getcontentBasePath() . $identPath;
        //Qualification Path
        $qclFilename = 'Qualification.xml';
        $qclPath = 'users/' . $this->userId . '/' . $qclFilename;
        $qclMyFile = $this->objConfig->getcontentBasePath() . $qclPath;
        //Affiliation Path
        $affFilename = 'Affiliation.xml';
        $affPath = 'users/' . $this->userId . '/' . $affFilename;
        $affMyFile = $this->objConfig->getcontentBasePath() . $affPath;
        //Transcript Path
        $transFilename = 'Transcript.xml';
        $transPath = 'users/' . $this->userId . '/' . $transFilename;
        $transMyFile = $this->objConfig->getcontentBasePath() . $transPath;
        //Activity Path
        $actFilename = 'Activity.xml';
        $actPath = 'users/' . $this->userId . '/' . $actFilename;
        $actMyFile = $this->objConfig->getcontentBasePath() . $actPath;
        //Assertion Path
        $assertFilename = 'Assertion.xml';
        $assertPath = 'users/' . $this->userId . '/' . $assertFilename;
        $assertMyFile = $this->objConfig->getcontentBasePath() . $assertPath;
        //Reflexion Path
        $reflFilename = 'Reflexion.xml';
        $reflPath = 'users/' . $this->userId . '/' . $reflFilename;
        $reflMyFile = $this->objConfig->getcontentBasePath() . $reflPath;
        //Competency Path
        $cotcyFilename = 'Competency.xml';
        $cotcyPath = 'users/' . $this->userId . '/' . $cotcyFilename;
        $cotcyMyFile = $this->objConfig->getcontentBasePath() . $cotcyPath;
        //Interest Path
        $intFilename = 'Interest.xml';
        $intPath = 'users/' . $this->userId . '/' . $intFilename;
        $intMyFile = $this->objConfig->getcontentBasePath() . $intPath;
        //Goal Path
        $glFilename = 'Goal.xml';
        $glPath = 'users/' . $this->userId . '/' . $glFilename;
        $glMyFile = $this->objConfig->getcontentBasePath() . $glPath;
        //imsmanifest Path
        $imsFilename = 'imsmanifest.xml';
        $imsPath = 'users/' . $this->userId . '/' . $imsFilename;
        $imsMyFile = $this->objConfig->getcontentBasePath() . $imsPath;
        //Get the data
        $userInfo = $this->objUser->getUserDetails($this->userId);
        $addressList = $this->objDbAddressList->listAll($this->userId);
        $contactList = $this->objDbContactList->listAll($this->userId);
        $emailList = $this->objDbEmailList->listAll($this->userId);
        $demographicsList = $this->objDbDemographicsList->listAll($this->userId);
        $qclList = $this->objDbQclList->listAll($this->userId);
        $affiliationList = $this->objDbAffiliationList->listAll($this->userId);
        $transcriptList = $this->objDbTranscriptList->listAll($this->userId);
        $activityList = $this->objDbActivityList->listAll($this->userId);
        $assertionList = $this->objDbAssertionList->listAll($this->userId);
        $reflectionList = $this->objDbReflectionList->listAll($this->userId);
        $competencyList = $this->objDbCompetencyList->listAll($this->userId);
        $interestList = $this->objDbInterestList->listAll($this->userId);
        $goalList = $this->objDbGoalList->listAll($this->userId);
        //Create xmlIdentification
        $this->xmlIdentification->create_root();
        $this->xmlIdentification->roottag->name = "learnerinformation";
        $this->xmlIdentification->roottag->set_attributes(array(
            'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
        ));
        $this->xmlIdentification->roottag->add_subtag("COMMENT", array());
        $this->xmlIdentification->roottag->curtag->cdata = $this->objUser->fullname($this->userId) . ' - ' . $this->objLanguage->languageText("mod_eportfolio_wordEportfolio", 'eportfolio');
        $this->xmlIdentification->roottag->add_subtag("CONTENTYPE", array());
        $this->xmlIdentification->roottag->curtag->add_subtag("REFERENTIAL", array());
        $this->xmlIdentification->roottag->curtag->curtag->add_subtag("SOURCEID", array());
        $this->xmlIdentification->roottag->curtag->curtag->curtag->add_subtag("SOURCE", array());
        $this->xmlIdentification->roottag->curtag->curtag->curtag->curtag->cdata = $this->objConfig->getSitePath();
        $this->xmlIdentification->roottag->curtag->curtag->curtag->add_subtag("ID", array());
        $this->xmlIdentification->roottag->curtag->curtag->curtag->curtag->cdata = $this->userId;
        $this->xmlIdentification->roottag->add_subtag("IDENTIFICATION", array());
        //create User-Names tags
        $this->xmlIdentification->roottag->curtag->add_subtag("NAME", array());
        $tagIdent = &$this->xmlIdentification->roottag->curtag->curtag;
        $tagIdent->add_subtag("TYPENAME", array());
        $tagIdent->curtag->add_subtag("TYSOURCE", array(
            'sourcetype' => 'imsdefault'
        ));
        $tagIdent->curtag->add_subtag("TYVALUE", array());
        $tagIdent->curtag->curtag->cdata = "Preferred";
        //Content Type
        $tagIdent->add_subtag("CONTENTYPE", array());
        $tagIdent->curtag->add_subtag("REFERENTIAL", array());
        $tagIdent->curtag->curtag->add_subtag("INDEXID", array());
        $tagIdent->curtag->curtag->curtag->cdata = "name_01";
        //Partname - First
        $tagIdent->add_subtag("PARTNAME", array());
        $tagIdent->curtag->add_subtag("TYPENAME", array());
        $tagIdent->curtag->curtag->add_subtag("TYSOURCE", array(
            'sourcetype' => 'imsdefault'
        ));
        $tagIdent->curtag->curtag->add_subtag("TYVALUE", array());
        $tagIdent->curtag->curtag->curtag->cdata = "First";
        $tagIdent->curtag->add_subtag("TEXT", array());
        $tagIdent->curtag->curtag->cdata = $userInfo['surname'];
        //Partname - Last
        $tagIdent->add_subtag("PARTNAME", array());
        $tagIdent->curtag->add_subtag("TYPENAME", array());
        $tagIdent->curtag->curtag->add_subtag("TYSOURCE", array(
            'sourcetype' => 'imsdefault'
        ));
        $tagIdent->curtag->curtag->add_subtag("TYVALUE", array());
        $tagIdent->curtag->curtag->curtag->cdata = "Last";
        $tagIdent->curtag->add_subtag("TEXT", array());
        $tagIdent->curtag->curtag->cdata = $userInfo['firstname'];
        //Partname - Suffix
        $tagIdent->add_subtag("PARTNAME", array());
        $tagIdent->curtag->add_subtag("TYPENAME", array());
        $tagIdent->curtag->curtag->add_subtag("TYSOURCE", array(
            'sourcetype' => 'imsdefault'
        ));
        $tagIdent->curtag->curtag->add_subtag("TYVALUE", array());
        $tagIdent->curtag->curtag->curtag->cdata = "Suffix";
        $tagIdent->curtag->add_subtag("TEXT", array());
        $tagIdent->curtag->curtag->cdata = $userInfo['title'] . '.';
        if (!empty($addressList)) {
            //create Address tags
            foreach($addressList as $mainRow) {
                $roleNo = 1;
                $this->xmlIdentification->roottag->curtag->add_subtag("ADDRESS", array());
                $tagIdent = &$this->xmlIdentification->roottag->curtag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $tagIdent->add_subtag("TYPENAME", array());
                $tagIdent->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $tagIdent->curtag->add_subtag("TYVALUE", array());
                $tagIdent->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $tagIdent->add_subtag("CONTENTYPE", array());
                $tagIdent->curtag->add_subtag("REFERENTIAL", array());
                $tagIdent->curtag->curtag->add_subtag("INDEXID", array());
                $tagIdent->curtag->curtag->curtag->cdata = "address_" . $roleNo;
                //Street
                $tagIdent->add_subtag("STREET", array());
                $tagIdent->curtag->add_subtag("STREETNUMBER", array());
                $tagIdent->curtag->curtag->cdata = $mainRow['street_no'];
                $tagIdent->curtag->add_subtag("STREETNAME", array());
                $tagIdent->curtag->curtag->cdata = $mainRow['street_name'];
                //Locality
                $tagIdent->add_subtag("LOCALITY", array());
                $tagIdent->curtag->cdata = $mainRow['locality'];
                //City
                $tagIdent->add_subtag("CITY", array());
                $tagIdent->curtag->cdata = $mainRow['city'];
                //Postcode
                $tagIdent->add_subtag("POSTCODE", array());
                $tagIdent->curtag->cdata = $mainRow['postcode'];
                //Increment roleNo
                $roleNo = $roleNo+1;
            }
        }
        //create Contact tags
        $roleNo = 1;
        if (!empty($contactList)) {
            foreach($contactList as $mainRow) {
                $this->xmlIdentification->roottag->curtag->add_subtag("CONTACTINFO", array());
                $tagIdent = &$this->xmlIdentification->roottag->curtag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $contactType = $this->objDbCategorytypeList->listSingle($mainRow['contact_type']);
                $tagIdent->add_subtag("TYPENAME", array());
                $tagIdent->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $tagIdent->curtag->add_subtag("TYVALUE", array());
                $tagIdent->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $tagIdent->add_subtag("CONTENTYPE", array());
                $tagIdent->curtag->add_subtag("REFERENTIAL", array());
                $tagIdent->curtag->curtag->add_subtag("INDEXID", array());
                $tagIdent->curtag->curtag->curtag->cdata = 'contact_' . $roleNo;
                //Contact i.e facsimile
                //Remove spaces by exploding then imploding the sting
                $YourNewString = explode(' ', $contactType[0]['type']);
                $contype = implode("", $YourNewString);
                $tagIdent->add_subtag($contype, array());
                $tagIdent->curtag->add_subtag("COUNTRYCODE", array());
                $tagIdent->curtag->curtag->cdata = $mainRow['country_code'];
                $tagIdent->curtag->add_subtag("AREACODE", array());
                $tagIdent->curtag->curtag->cdata = $mainRow['area_code'];
                $tagIdent->curtag->add_subtag("INDNUMBER", array());
                $tagIdent->curtag->curtag->cdata = $mainRow['id_number'];
                $roleNo = $roleNo+1;
            }
        }
        if (!empty($emailList)) {
            //create email tags
            foreach($emailList as $mainRow) {
                $this->xmlIdentification->roottag->curtag->add_subtag("CONTACTINFO", array());
                $tagIdent = &$this->xmlIdentification->roottag->curtag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $tagIdent->add_subtag("TYPENAME", array());
                $tagIdent->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $tagIdent->curtag->add_subtag("TYVALUE", array());
                $tagIdent->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $tagIdent->add_subtag("CONTENTYPE", array());
                $tagIdent->curtag->add_subtag("REFERENTIAL", array());
                $tagIdent->curtag->curtag->add_subtag("INDEXID", array());
                $tagIdent->curtag->curtag->curtag->cdata = "email_" . $roleNo;
                //Street
                $tagIdent->add_subtag("EMAIL", array());
                $tagIdent->curtag->cdata = $mainRow['email'];
            }
        }
        if (!empty($demographicsList)) {
            //create Demographics tags
            $roleNo = 1;
            foreach($demographicsList as $mainRow) {
                $this->xmlIdentification->roottag->curtag->add_subtag("DEMOGRAPHICS", array());
                $tagIdent = &$this->xmlIdentification->roottag->curtag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $tagIdent->add_subtag("TYPENAME", array());
                $tagIdent->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $tagIdent->curtag->add_subtag("TYVALUE", array());
                $tagIdent->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $tagIdent->add_subtag("CONTENTYPE", array());
                $tagIdent->curtag->add_subtag("REFERENTIAL", array());
                $tagIdent->curtag->curtag->add_subtag("INDEXID", array());
                $tagIdent->curtag->curtag->curtag->cdata = 'demographics_' . $roleNo;
                //Date Of Birth
                $tagIdent->add_subtag("DATE", array());
                $tagIdent->curtag->add_subtag("TYPENAME", array());
                $tagIdent->curtag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $tagIdent->curtag->curtag->add_subtag("TYVALUE", array());
                $tagIdent->curtag->curtag->curtag->cdata = 'Birth';
                $tagIdent->curtag->add_subtag("DATETIME", array());
                $tagIdent->curtag->curtag->cdata = $mainRow['birth'];
                //Nationality
                $tagIdent->add_subtag("ext_identification");
                $tagIdent->curtag->add_subtag("nationality", array());
                $tagIdent->curtag->curtag->cdata = $mainRow['nationality'];
                $roleNo = $roleNo+1;
            }
        }
        //Write xml on identification.xml
        $identxml_file = fopen($identMyFile, "w+", true);
        $this->xmlIdentification->write_file_handle($identxml_file);
        fclose($identxml_file);
        if (!empty($qclList)) {
            //Create xmlQcl
            $this->xmlQcl->create_root();
            $this->xmlQcl->roottag->name = "learnerinformation";
            $this->xmlQcl->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            //create Qualification tags
            $roleNo = 1;
            foreach($qclList as $mainRow) {
                //Create xml
                $this->xmlQcl->roottag->add_subtag("QCL", array());
                $xmlTag = &$this->xmlQcl->roottag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['qcl_type']);
                $xmlTag->add_subtag("TYPENAME", array());
                $xmlTag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $xmlTag->curtag->add_subtag("TYVALUE", array());
                $xmlTag->curtag->curtag->cdata = 'Qualification';
                //Content Type
                $xmlTag->add_subtag("CONTENTYPE", array());
                $xmlTag->curtag->add_subtag("REFERENTIAL", array());
                $xmlTag->curtag->curtag->add_subtag("INDEXID", array());
                $xmlTag->curtag->curtag->curtag->cdata = 'qcl_' . $roleNo;
                //QCL Title
                $xmlTag->add_subtag("TITLE", array());
                $xmlTag->curtag->cdata = $mainRow['qcl_title'];
                //Organization
                $xmlTag->add_subtag("ORGANIZATION", array());
                //Typename
                $xmlTag->curtag->add_subtag("TYPENAME", array());
                $xmlTag->curtag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $xmlTag->curtag->curtag->add_subtag("TYVALUE", array());
                $xmlTag->curtag->curtag->curtag->cdata = $mytype[0]['type'];
                //Description
                $xmlTag->curtag->add_subtag("DESCRIPTION", array());
                //Short Description
                $xmlTag->curtag->curtag->add_subtag("SHORT", array());
                $xmlTag->curtag->curtag->curtag->cdata = $mainRow['organisation'];
                //Full Description
                $xmlTag->curtag->curtag->add_subtag("FULL", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $xmlTag->curtag->curtag->curtag->cdata = $htmlString;
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultQcl[$roleNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultQcl[$roleNo]);
                //Level
                $xmlTag->add_subtag("LEVEL", array());
                $xmlTag->curtag->cdata = $mainRow['qcl_level'];
                //Date
                $xmlTag->add_subtag("DATE", array());
                //Typename
                $xmlTag->curtag->add_subtag("TYPENAME", array());
                $xmlTag->curtag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $xmlTag->curtag->curtag->add_subtag("TYVALUE", array());
                $xmlTag->curtag->curtag->curtag->cdata = 'Finish';
                $xmlTag->curtag->add_subtag("DATETIME", array());
                $xmlTag->curtag->curtag->cdata = $mainRow['award_date'];
                //Increment Role no
                $roleNo = $roleNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultQcl as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Qcl Images
            $imgPathQcl = array();
            $id = 0;
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $qclImgfiles = $imgfile['1'];
                    $qclImgfilesId = $imgfileId['0'];
                    if (!empty($qclImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($qclImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathQcl[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultQcl as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Qcl Files
            $id = 0;
            $aPathQcl = array();
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aQclFile = explode('&', $aa_tagd2['0']);
                    $aQclFileId = explode('id=', $aQclFile['2']);
                    $aQclFileId = $aQclFileId['1'];
                    $aQclFileName = explode('=', $aQclFile['3']);
                    $qclAfiles = $aQclFileName['1'];
                    $qclAfilesId = $aQclFileId;
                    if (!empty($qclAfilesId)) {
                        $aQclPath = $this->objDbFile->getFilePath($qclAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aQclPath)) {
                            $aPathQcl[$id] = $this->objConfig->getsiteRootPath() . $aQclPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Qualification.xml
            $qclxml_file = fopen($qclMyFile, "w+", true);
            $this->xmlQcl->write_file_handle($qclxml_file);
            fclose($qclxml_file);
        }
        if (!empty($affiliationList)) {
            //Create xmlAffiliation
            $this->xmlAffiliation->create_root();
            $this->xmlAffiliation->roottag->name = "learnerinformation";
            $this->xmlAffiliation->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            //create Affiliation tags
            $roleNo = 1;
            foreach($affiliationList as $mainRow) {
                //Create xml
                $this->xmlAffiliation->roottag->add_subtag("affiliation", array());
                $affTag = &$this->xmlAffiliation->roottag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $affTag->add_subtag("TYPENAME", array());
                $affTag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $affTag->curtag->add_subtag("TYVALUE", array());
                $affTag->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $affTag->add_subtag("CONTENTYPE", array());
                $affTag->curtag->add_subtag("REFERENTIAL", array());
                $affTag->curtag->curtag->add_subtag("INDEXID", array());
                $affTag->curtag->curtag->curtag->cdata = "affiliation_" . $roleNo;
                //Classification
                $affTag->add_subtag("classification", array());
                $affTag->curtag->cdata = $mainRow['classification'];
                //Role
                $affTag->add_subtag("role", array());
                //Typename
                $affTag->curtag->add_subtag("TYPENAME", array());
                $affTag->curtag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $affTag->curtag->curtag->add_subtag("TYVALUE", array());
                $affTag->curtag->curtag->curtag->cdata = $mainRow['role'];
                //contentype
                $affTag->curtag->add_subtag("CONTENTYPE", array());
                $affTag->curtag->curtag->add_subtag("REFERENTIAL", array(
                    'sourcetype' => 'imsdefault'
                ));
                $affTag->curtag->curtag->add_subtag("INDEXID", array());
                $affTag->curtag->curtag->curtag->cdata = 'role_' . $roleNo;
                //Description
                $affTag->curtag->add_subtag("organisation", array());
                //Short Description
                //   	        $affTag->curtag->curtag->add_subtag("SHORT", array());
                $affTag->curtag->curtag->cdata = $mainRow['organisation'];
                //Description
                $affTag->add_subtag("DESCRIPTION", array());
                //Short Description
                $affTag->curtag->add_subtag("SHORT", array());
                $affTag->curtag->curtag->cdata = $mainRow['shortdescription'];
                //Full Description
                $affTag->curtag->add_subtag("LONG", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $affTag->curtag->curtag->cdata = $htmlString;
                //Get the images and file attached to description
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultAff[$roleNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultAff[$roleNo]);
                //Start Date
                $affTag->add_subtag("DATE", array());
                $affTag->curtag->add_subtag("TYVALUE", array());
                $affTag->curtag->curtag->cdata = 'Start';
                $affTag->curtag->add_subtag("DATETIME", array());
                $affTag->curtag->curtag->cdata = $mainRow['start'];
                //Finish Date
                $affTag->add_subtag("DATE", array());
                $affTag->curtag->add_subtag("TYVALUE", array());
                $affTag->curtag->curtag->cdata = 'Finish';
                $affTag->curtag->add_subtag("DATETIME", array());
                $affTag->curtag->curtag->cdata = $mainRow['finish'];
                //Increment Role no
                $roleNo = $roleNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultAff as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Affiliation Images
            $imgPathAff = array();
            $id = 0;
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $affImgfiles = $imgfile['1'];
                    $affImgfilesId = $imgfileId['0'];
                    if (!empty($affImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($affImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathAff[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultAff as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Affiliation Files
            $id = 0;
            $aPathAff = array();
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aAffFile = explode('&', $aa_tagd2['0']);
                    $aAffFileId = explode('id=', $aAffFile['2']);
                    $aAffFileId = $aAffFileId['1'];
                    $aAffFileName = explode('=', $aAffFile['3']);
                    $affAfiles = $aAffFileName['1'];
                    $affAfilesId = $aAffFileId;
                    if (!empty($affAfilesId)) {
                        $aAffPath = $this->objDbFile->getFilePath($affAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aAffPath)) {
                            $aPathAff[$id] = $this->objConfig->getsiteRootPath() . $aAffPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Affiliation.xml
            $affxml_file = fopen($affMyFile, "w+", true);
            $this->xmlAffiliation->write_file_handle($affxml_file);
            fclose($affxml_file);
        }
        if (!empty($transcriptList)) {
            //Create xmlTranscript
            $this->xmlTranscript->create_root();
            $this->xmlTranscript->roottag->name = "learnerinformation";
            $this->xmlTranscript->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            //create Transcript tags
            $roleNo = 1;
            foreach($transcriptList as $mainRow) {
                //Create xml
                $this->xmlTranscript->roottag->add_subtag("transcript", array());
                $transTag = &$this->xmlTranscript->roottag->curtag;
                //Type Name
                //	        $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $transTag->add_subtag("TYPENAME", array());
                $transTag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $transTag->curtag->add_subtag("TYVALUE", array());
                $transTag->curtag->curtag->cdata = $mainRow['type'];
                //Content Type
                $transTag->add_subtag("CONTENTYPE", array());
                $transTag->curtag->add_subtag("REFERENTIAL", array());
                $transTag->curtag->curtag->add_subtag("INDEXID", array());
                $transTag->curtag->curtag->curtag->cdata = "transcript_" . $roleNo;
                //Description
                $transTag->add_subtag("DESCRIPTION", array());
                //Short Description
                $transTag->curtag->add_subtag("SHORT", array());
                $transTag->curtag->curtag->cdata = $mainRow['shortdescription'];
                //Full Description
                $transTag->curtag->add_subtag("LONG", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $transTag->curtag->curtag->cdata = $htmlString;
                //Get the images and file attached to description
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultTrans[$roleNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultTrans[$roleNo]);
                //Increment Role no
                $roleNo = $roleNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultTrans as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Transcript Images
            $id = 0;
            $imgPathTrans = array();
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $transImgfiles = $imgfile['1'];
                    $transImgfilesId = $imgfileId['0'];
                    if (!empty($transImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($transImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathTrans[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultTrans as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Transcript Files
            $id = 0;
            $aPathTrans = array();
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aTransFile = explode('&', $aa_tagd2['0']);
                    $aTransFileId = explode('id=', $aTransFile['2']);
                    $aTransFileId = $aTransFileId['1'];
                    $aTransFileName = explode('=', $aTransFile['3']);
                    $transAfiles = $aTransFileName['1'];
                    $transAfilesId = $aTransFileId;
                    if (!empty($transAfilesId)) {
                        $aTransPath = $this->objDbFile->getFilePath($transAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aTransPath)) {
                            $aPathTrans[$id] = $this->objConfig->getsiteRootPath() . $aTransPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Transcript.xml
            $transXml_file = fopen($transMyFile, "w+", true);
            $this->xmlTranscript->write_file_handle($transXml_file);
            fclose($transXml_file);
        }
        if (!empty($activityList)) {
            //Create xmlActivity
            $this->xmlActivity->create_root();
            $this->xmlActivity->roottag->name = "learnerinformation";
            $this->xmlActivity->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            $imgResultAdd = array();
            $aResultAdd = array();
            //create Activity tags
            $roleNo = 1;
            foreach($activityList as $mainRow) {
                //Create xml
                $this->xmlActivity->roottag->add_subtag("activity", array());
                $actTag = &$this->xmlActivity->roottag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $actTag->add_subtag("TYPENAME", array());
                $actTag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $actTag->curtag->add_subtag("TYVALUE", array());
                $actTag->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $actTag->add_subtag("CONTENTYPE", array());
                $actTag->curtag->add_subtag("REFERENTIAL", array());
                $actTag->curtag->curtag->add_subtag("INDEXID", array());
                $actTag->curtag->curtag->curtag->cdata = "activity_" . $roleNo;
                //Start Date
                if (!empty($mainRow['start'])) {
                    $actTag->add_subtag("date", array());
                    //Typename
                    $actTag->curtag->add_subtag("TYPENAME", array());
                    $actTag->curtag->curtag->add_subtag("TYSOURCE", array(
                        'sourcetype' => 'imsdefault'
                    ));
                    $actTag->curtag->curtag->add_subtag("TYVALUE", array());
                    $actTag->curtag->curtag->curtag->cdata = "Start";
                    //DateTime
                    $actTag->curtag->add_subtag("DATETIME", array());
                    $actTag->curtag->curtag->cdata = $mainRow['start'];
                }
                //End Date
                if (!empty($mainRow['finish'])) {
                    $actTag->add_subtag("date", array());
                    //Typename
                    $actTag->curtag->add_subtag("TYPENAME", array());
                    $actTag->curtag->curtag->add_subtag("TYSOURCE", array(
                        'sourcetype' => 'imsdefault'
                    ));
                    $actTag->curtag->curtag->add_subtag("TYVALUE", array());
                    $actTag->curtag->curtag->curtag->cdata = "Finish";
                    //DateTime
                    $actTag->curtag->add_subtag("DATETIME", array());
                    $actTag->curtag->curtag->cdata = $mainRow['finish'];
                }
                //Description
                $actTag->add_subtag("DESCRIPTION", array());
                //Short Description
                $actTag->curtag->add_subtag("SHORT", array());
                $actTag->curtag->curtag->cdata = $mainRow['shortdescription'];
                //Full Description
                $actTag->curtag->add_subtag("LONG", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $actTag->curtag->curtag->cdata = $htmlString;
                //Get the images and file attached to description
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultAdd[$roleNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultAdd[$roleNo]);
                //Increment Role no
                $roleNo = $roleNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultAdd as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Activity Images
            $imgPathActivity = array();
            $id = 0;
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $activityImgfiles = $imgfile['1'];
                    $activityImgfilesId = $imgfileId['0'];
                    if (!empty($activityImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($activityImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathActivity[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultAdd as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Activity Files
            $aPathActivity = array();
            $id = 0;
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aafile = explode('&', $aa_tagd2['0']);
                    $aafileId = explode('id=', $aafile['2']);
                    $aafileId = $aafileId['1'];
                    $aafileName = explode('=', $aafile['3']);
                    $activityAfiles = $aafileName['1'];
                    $activityAfilesId = $aafileId;
                    if (!empty($activityAfilesId)) {
                        $aPath = $this->objDbFile->getFilePath($activityAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aPath)) {
                            $aPathActivity[$id] = $this->objConfig->getsiteRootPath() . $aPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Activity.xml
            $actXml_file = fopen($actMyFile, "w+", true);
            $this->xmlActivity->write_file_handle($actXml_file);
            fclose($actXml_file);
        }
        if (!empty($assertionList)) {
            //Create xmlAssertion
            $this->xmlAssertion->create_root();
            $this->xmlAssertion->roottag->name = "learnerinformation";
            $this->xmlAssertion->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            //create Assertion tags
            $roleNo = 1;
            foreach($assertionList as $mainRow) {
                //Create xml
                $this->xmlAssertion->roottag->add_subtag("assertion", array());
                $assertTag = &$this->xmlAssertion->roottag->curtag;
                //Content Type
                $assertTag->add_subtag("CONTENTYPE", array());
                $assertTag->curtag->add_subtag("REFERENTIAL", array());
                $assertTag->curtag->curtag->add_subtag("INDEXID", array());
                $assertTag->curtag->curtag->curtag->cdata = "assertion_" . $roleNo;
                //Authorship
                $assertTag->add_subtag("authorship", array());
                $assertTag->curtag->add_subtag("language", array());
                $assertTag->curtag->curtag->cdata = $mainRow['language'];
                $assertTag->curtag->add_subtag("text", array());
                $authInfo = $this->objUser->getUserDetails($mainRow['userid']);
                $assertTag->curtag->curtag->cdata = $authInfo['title'] . ". " . $this->objUser->fullname($mainRow['userid']);
                //Rationale
                $assertTag->add_subtag("rationale", array());
                $assertTag->curtag->add_subtag("language", array());
                $assertTag->curtag->curtag->cdata = $mainRow['language'];
                $assertTag->curtag->add_subtag("text", array());
                $assertTag->curtag->curtag->cdata = $mainRow['rationale'];
                //Creation Date
                if (!empty($mainRow['creation_date'])) {
                    $assertTag->add_subtag("date", array());
                    //Typename
                    $assertTag->curtag->add_subtag("TYPENAME", array());
                    $assertTag->curtag->curtag->add_subtag("TYSOURCE", array(
                        'sourcetype' => 'imsdefault'
                    ));
                    $assertTag->curtag->curtag->add_subtag("TYVALUE", array());
                    $assertTag->curtag->curtag->curtag->cdata = "Create";
                    //DateTime
                    $assertTag->curtag->add_subtag("DATETIME", array());
                    $assertTag->curtag->curtag->cdata = $mainRow['creation_date'];
                }
                //Description
                $assertTag->add_subtag("DESCRIPTION", array());
                //Short Description
                $assertTag->curtag->add_subtag("SHORT", array());
                $assertTag->curtag->curtag->cdata = $mainRow['shortdescription'];
                //Full Description
                $assertTag->curtag->add_subtag("LONG", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $assertTag->curtag->curtag->cdata = $htmlString;
                //Get the images and file attached to description
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultAssert[$roleNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultAssert[$roleNo]);
                //Increment Role no
                $roleNo = $roleNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultAssert as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Assertion Images
            $imgPathAssert = array();
            $id = 0;
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $assertImgfiles = $imgfile['1'];
                    $assertImgfilesId = $imgfileId['0'];
                    if (!empty($assertImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($assertImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathAssert[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultAssert as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Assertion Files
            $id = 0;
            $aPathAssert = array();
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aAssertFile = explode('&', $aa_tagd2['0']);
                    $aAssertFileId = explode('id=', $aAssertFile['2']);
                    $aAssertFileId = $aAssertFileId['1'];
                    $aAssertFileName = explode('=', $aAssertFile['3']);
                    $assertAfiles = $aAssertFileName['1'];
                    $assertAfilesId = $aAssertFileId;
                    if (!empty($assertAfilesId)) {
                        $aAssertPath = $this->objDbFile->getFilePath($assertAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aAssertPath)) {
                            $aPathAssert[$id] = $this->objConfig->getsiteRootPath() . $aAssertPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Assertion.xml
            $assertXml_file = fopen($assertMyFile, "w+", true);
            $this->xmlAssertion->write_file_handle($assertXml_file);
            fclose($assertXml_file);
        }
        if (!empty($reflectionList)) {
            //Create xmlReflection
            $this->xmlReflection->create_root();
            $this->xmlReflection->roottag->name = "learnerinformation";
            $this->xmlReflection->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            //create Reflection tags
            $roleNo = 1;
            foreach($reflectionList as $mainRow) {
                //Create xml
                $this->xmlReflection->roottag->add_subtag("reflexion", array());
                $reflTag = &$this->xmlReflection->roottag->curtag;
                //Content Type
                $reflTag->add_subtag("CONTENTYPE", array());
                $reflTag->curtag->add_subtag("REFERENTIAL", array());
                $reflTag->curtag->curtag->add_subtag("INDEXID", array());
                $reflTag->curtag->curtag->curtag->cdata = "reflexion_" . $roleNo;
                //Authorship
                $reflTag->add_subtag("authorship", array());
                $reflTag->curtag->add_subtag("language", array());
                $reflTag->curtag->curtag->cdata = $mainRow['language'];
                $reflTag->curtag->add_subtag("text", array());
                $authInfo = $this->objUser->getUserDetails($mainRow['userid']);
                $reflTag->curtag->curtag->cdata = $authInfo['title'] . ". " . $this->objUser->fullname($mainRow['userid']);
                //Rationale
                $reflTag->add_subtag("rationale", array());
                $reflTag->curtag->add_subtag("language", array());
                $reflTag->curtag->curtag->cdata = $mainRow['language'];
                $reflTag->curtag->add_subtag("text", array());
                $reflTag->curtag->curtag->cdata = $mainRow['rationale'];
                //Creation Date
                if (!empty($mainRow['creation_date'])) {
                    $reflTag->add_subtag("date", array());
                    //Typename
                    $reflTag->curtag->add_subtag("TYPENAME", array());
                    $reflTag->curtag->curtag->add_subtag("TYSOURCE", array(
                        'sourcetype' => 'imsdefault'
                    ));
                    $reflTag->curtag->curtag->add_subtag("TYVALUE", array());
                    $reflTag->curtag->curtag->curtag->cdata = "Create";
                    //DateTime
                    $reflTag->curtag->add_subtag("DATETIME", array());
                    $reflTag->curtag->curtag->cdata = $mainRow['creation_date'];
                }
                //Description
                $reflTag->add_subtag("DESCRIPTION", array());
                //Short Description
                $reflTag->curtag->add_subtag("SHORT", array());
                $reflTag->curtag->curtag->cdata = $mainRow['shortdescription'];
                //Full Description
                $reflTag->curtag->add_subtag("LONG", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $reflTag->curtag->curtag->cdata = $htmlString;
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultRefl[$roleNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultRefl[$roleNo]);
                //Increment Role no
                $roleNo = $roleNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultRefl as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Reflection Images
            $imgPathRefl = array();
            $id = 0;
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $reflImgfiles = $imgfile['1'];
                    $reflImgfilesId = $imgfileId['0'];
                    if (!empty($reflImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($reflImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathRefl[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultRefl as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Reflection Files
            $id = 0;
            $aPathRefl = array();
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aReflFile = explode('&', $aa_tagd2['0']);
                    $aReflFileId = explode('id=', $aReflFile['2']);
                    $aReflFileId = $aReflFileId['1'];
                    $aReflFileName = explode('=', $aReflFile['3']);
                    $reflAfiles = $aReflFileName['1'];
                    $reflAfilesId = $aReflFileId;
                    if (!empty($reflAfilesId)) {
                        $aReflPath = $this->objDbFile->getFilePath($reflAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aReflPath)) {
                            $aPathRefl[$id] = $this->objConfig->getsiteRootPath() . $aReflPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Reflection.xml
            $reflXml_file = fopen($reflMyFile, "w+", true);
            $this->xmlReflection->write_file_handle($reflXml_file);
            fclose($reflXml_file);
        }
        if (!empty($competencyList)) {
            //Create xmlCompetency
            $this->xmlCompetency->create_root();
            $this->xmlCompetency->roottag->name = "learnerinformation";
            $this->xmlCompetency->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            //create competency tags
            $roleNo = 1;
            foreach($competencyList as $mainRow) {
                //Create xml
                $this->xmlCompetency->roottag->add_subtag("competency", array());
                $cotcyTag = &$this->xmlCompetency->roottag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $cotcyTag->add_subtag("TYPENAME", array());
                $cotcyTag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $cotcyTag->curtag->add_subtag("TYVALUE", array());
                $cotcyTag->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $cotcyTag->add_subtag("CONTENTYPE", array());
                $cotcyTag->curtag->add_subtag("REFERENTIAL", array());
                $cotcyTag->curtag->curtag->add_subtag("INDEXID", array());
                $cotcyTag->curtag->curtag->curtag->cdata = "competency_" . $roleNo;
                //Award Date
                if (!empty($mainRow['award_date'])) {
                    $cotcyTag->add_subtag("date", array());
                    //Typename
                    $cotcyTag->curtag->add_subtag("TYPENAME", array());
                    $cotcyTag->curtag->curtag->add_subtag("TYSOURCE", array(
                        'sourcetype' => 'imsdefault'
                    ));
                    $cotcyTag->curtag->curtag->add_subtag("TYVALUE", array());
                    $cotcyTag->curtag->curtag->curtag->cdata = "Award Date";
                    //DateTime
                    $cotcyTag->curtag->add_subtag("DATETIME", array());
                    $cotcyTag->curtag->curtag->cdata = $mainRow['award_date'];
                }
                //Description
                $cotcyTag->add_subtag("DESCRIPTION", array());
                //Short Description
                $cotcyTag->curtag->add_subtag("SHORT", array());
                $cotcyTag->curtag->curtag->cdata = $mainRow['shortdescription'];
                //Full Description
                $cotcyTag->curtag->add_subtag("LONG", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $cotcyTag->curtag->curtag->cdata = $htmlString;
                //Get the images and file attached to description
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultComptcy[$roleNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultComptcy[$roleNo]);
                //Increment Role no
                $roleNo = $roleNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultComptcy as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Competency Images
            $imgPathComptcy = array();
            $id = 0;
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $comptcyImgfiles = $imgfile['1'];
                    $comptcyImgfilesId = $imgfileId['0'];
                    if (!empty($comptcyImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($comptcyImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathComptcy[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultComptcy as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Competency Files
            $id = 0;
            $aPathComptcy = array();
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aComptcyFile = explode('&', $aa_tagd2['0']);
                    $aComptcyFileId = explode('id=', $aComptcyFile['2']);
                    $aComptcyFileId = $aComptcyFileId['1'];
                    $aComptcyFileName = explode('=', $aComptcyFile['3']);
                    $comptcyAfiles = $aComptcyFileName['1'];
                    $comptcyAfilesId = $aComptcyFileId;
                    if (!empty($comptcyAfilesId)) {
                        $aComptcyPath = $this->objDbFile->getFilePath($comptcyAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aComptcyPath)) {
                            $aPathComptcy[$id] = $this->objConfig->getsiteRootPath() . $aComptcyPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Interest.xml
            $intXml_file = fopen($cotcyMyFile, "w+", true);
            $this->xmlCompetency->write_file_handle($intXml_file);
            fclose($intXml_file);
        }
        if (!empty($interestList)) {
            //Create xmlInterest
            $this->xmlInterest->create_root();
            $this->xmlInterest->roottag->name = "learnerinformation";
            $this->xmlInterest->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            //create Interest tags
            $roleNo = 1;
            foreach($interestList as $mainRow) {
                //Create xml
                $this->xmlInterest->roottag->add_subtag("interest", array());
                $intTag = &$this->xmlInterest->roottag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $intTag->add_subtag("TYPENAME", array());
                $intTag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $intTag->curtag->add_subtag("TYVALUE", array());
                $intTag->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $intTag->add_subtag("CONTENTYPE", array());
                $intTag->curtag->add_subtag("REFERENTIAL", array());
                $intTag->curtag->curtag->add_subtag("INDEXID", array());
                $intTag->curtag->curtag->curtag->cdata = "interest_" . $roleNo;
                //Creation date
                if (!empty($mainRow['creation_date'])) {
                    $intTag->add_subtag("date", array());
                    //Typename
                    $intTag->curtag->add_subtag("TYPENAME", array());
                    $intTag->curtag->curtag->add_subtag("TYSOURCE", array(
                        'sourcetype' => 'imsdefault'
                    ));
                    $intTag->curtag->curtag->add_subtag("TYVALUE", array());
                    $intTag->curtag->curtag->curtag->cdata = "Creation Date";
                    //DateTime
                    $intTag->curtag->add_subtag("DATETIME", array());
                    $intTag->curtag->curtag->cdata = $mainRow['creation_date'];
                }
                //Description
                $intTag->add_subtag("DESCRIPTION", array());
                //Short Description
                $intTag->curtag->add_subtag("SHORT", array());
                $intTag->curtag->curtag->cdata = $mainRow['shortdescription'];
                //Full Description
                $intTag->curtag->add_subtag("LONG", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $intTag->curtag->curtag->cdata = $htmlString;
                //Get the images and file attached to description
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultInt[$roleNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultInt[$roleNo]);
                //Increment Role no
                $roleNo = $roleNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultInt as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Interest Images
            $imgPathInt = array();
            $id = 0;
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $intImgfiles = $imgfile['1'];
                    $intImgfilesId = $imgfileId['0'];
                    if (!empty($intImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($intImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathInt[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultInt as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Interest Files
            $id = 0;
            $aPathInt = array();
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aIntFile = explode('&', $aa_tagd2['0']);
                    $aIntFileId = explode('id=', $aIntFile['2']);
                    $aIntFileId = $aIntFileId['1'];
                    $aIntFileName = explode('=', $aIntFile['3']);
                    $intAfiles = $aIntFileName['1'];
                    $intAfilesId = $aIntFileId;
                    if (!empty($intAfilesId)) {
                        $aIntPath = $this->objDbFile->getFilePath($intAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aIntPath)) {
                            $aPathInt[$id] = $this->objConfig->getsiteRootPath() . $aIntPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Interest.xml
            $intXml_file = fopen($intMyFile, "w+", true);
            $this->xmlInterest->write_file_handle($intXml_file);
            fclose($intXml_file);
        }
        if (!empty($goalList)) {
            //Create xmlGoal
            $this->xmlGoal->create_root();
            $this->xmlGoal->roottag->name = "learnerinformation";
            $this->xmlGoal->roottag->set_attributes(array(
                'xmlns' => 'http://www.imsglobal.org/xsd/imslip_v1p0',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imslip_v1p0 http://www.imsglobal.org/xsd/imslip_v1p0.xsd'
            ));
            //create Goal tags
            $goalNo = 1;
            foreach($goalList as $mainRow) {
                //Create xml
                $this->xmlGoal->roottag->add_subtag("goal", array());
                $glTag = &$this->xmlGoal->roottag->curtag;
                //Type Name
                $mytype = $this->objDbCategorytypeList->listSingle($mainRow['type']);
                $glTag->add_subtag("TYPENAME", array());
                $glTag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $glTag->curtag->add_subtag("TYVALUE", array());
                $glTag->curtag->curtag->cdata = $mytype[0]['type'];
                //Content Type
                $glTag->add_subtag("CONTENTYPE", array());
                $glTag->curtag->add_subtag("REFERENTIAL", array());
                $glTag->curtag->curtag->add_subtag("INDEXID", array());
                $glTag->curtag->curtag->curtag->cdata = "goal_" . $goalNo;
                //Start Date
                if (!empty($mainRow['start'])) {
                    $glTag->add_subtag("date", array());
                    //Typename
                    $glTag->curtag->add_subtag("TYPENAME", array());
                    $glTag->curtag->curtag->add_subtag("TYSOURCE", array(
                        'sourcetype' => 'imsdefault'
                    ));
                    $glTag->curtag->curtag->add_subtag("TYVALUE", array());
                    $glTag->curtag->curtag->curtag->cdata = "Start";
                    //DateTime
                    $glTag->curtag->add_subtag("DATETIME", array());
                    $glTag->curtag->curtag->cdata = $mainRow['start'];
                }
                //Priority
                $myPriority = $this->objDbCategorytypeList->listSingle($mainRow['priority']);
                $glTag->add_subtag("PRIORITY", array());
                $glTag->curtag->cdata = $myPriority[0]['type'];
                //Status
                $glTag->add_subtag("STATUS", array());
                $glTag->curtag->add_subtag("TYSOURCE", array(
                    'sourcetype' => 'imsdefault'
                ));
                $glTag->curtag->add_subtag("TYVALUE", array());
                $glTag->curtag->curtag->cdata = $mainRow['status'];
                //Status Date
                if (!empty($mainRow['status'])) {
                    $glTag->curtag->add_subtag("date", array());
                    //Typename
                    $glTag->curtag->curtag->add_subtag("TYPENAME", array());
                    $glTag->curtag->curtag->curtag->add_subtag("TYSOURCE", array(
                        'sourcetype' => 'imsdefault'
                    ));
                    $glTag->curtag->curtag->curtag->add_subtag("TYVALUE", array());
                    $glTag->curtag->curtag->curtag->curtag->cdata = "Status Date";
                    //DateTime
                    $glTag->curtag->curtag->add_subtag("DATETIME", array());
                    $glTag->curtag->curtag->curtag->cdata = $mainRow['status_date'];
                }
                //Description
                $glTag->add_subtag("DESCRIPTION", array());
                //Short Description
                $glTag->curtag->add_subtag("SHORT", array());
                $glTag->curtag->curtag->cdata = $mainRow['shortdescription'];
                //Full Description
                $glTag->curtag->add_subtag("LONG", array());
                $htmlString = preg_replace("/&nbsp;/", ' ', strip_tags($mainRow['longdescription']));
                $glTag->curtag->curtag->cdata = $htmlString;
                //Get the images and file attached to description
                /*preg_match_all match the regexp in all the $mainRow['longdescription'] string and output everything as an array in $imgResultAdd
                *and aResultAdd. "i" option is used to make it case ensitive*/
                preg_match_all('/<img[^>]+>/i', $mainRow['longdescription'], $imgResultGoals[$goalNo]);
                preg_match_all('/<a[^>]+>/i', $mainRow['longdescription'], $aResultGoals[$goalNo]);
                //Increment Goal no
                $goalNo = $goalNo+1;
            }
            //get all the img tag attributes with a loop :
            $img = array();
            $aa = array();
            foreach($imgResultGoals as $img_tag2) {
                foreach($img_tag2 as $img_tag1) {
                    foreach($img_tag1 as $img_tag) {
                        if (!empty($img_tag)) preg_match_all('/(src)=("[^"]*")/i', $img_tag, $img[$img_tag]);
                    }
                }
            }
            //Get Goals Images
            $imgPathGoals = array();
            $id = 0;
            foreach($img as $img_tagd) {
                foreach($img_tagd as $img_tagd2) {
                    $imgfile = explode('filename=', $img_tagd2['0']);
                    $imgfileId = explode('id=', $img_tagd2['0']);
                    $imgfileId = explode('&', $imgfileId['1']);
                    $imgfile = explode('filename=', $imgfileId['1']);
                    $goalsImgfiles = $imgfile['1'];
                    $goalsImgfilesId = $imgfileId['0'];
                    if (!empty($goalsImgfilesId)) {
                        $imgPath = $this->objDbFile->getFilePath($goalsImgfilesId);
                        //Store the image full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $imgPath)) {
                            $imgPathGoals[$id] = $this->objConfig->getsiteRootPath() . $imgPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //get all the a tag attributes with a loop :
            foreach($aResultGoals as $a_tag2) {
                foreach($a_tag2 as $a_tag1) {
                    foreach($a_tag1 as $a_tag) {
                        if (!empty($a_tag)) preg_match_all('/(href)=("[^"]*")/i', $a_tag, $aa[$a_tag]);
                    }
                }
            }
            //Get Goals Files
            $id = 0;
            $aPathGoals = array();
            foreach($aa as $aa_tagd) {
                foreach($aa_tagd as $aa_tagd2) {
                    $aGoalsFile = explode('&', $aa_tagd2['0']);
                    $aGoalsFileId = explode('id=', $aGoalsFile['2']);
                    $aGoalsFileId = $aGoalsFileId['1'];
                    $aGoalsFileName = explode('=', $aGoalsFile['3']);
                    $goalsAfiles = $aGoalsFileName['1'];
                    $goalsAfilesId = $aGoalsFileId;
                    if (!empty($goalsAfilesId)) {
                        $aGoalsPath = $this->objDbFile->getFilePath($goalsAfilesId);
                        //Store the document/file full path in array
                        if (file_exists($this->objConfig->getsiteRootPath() . $aGoalsPath)) {
                            $aPathGoals[$id] = $this->objConfig->getsiteRootPath() . $aGoalsPath;
                        }
                    }
                }
                $id = $id+1;
            }
            //Write xml on Goal.xml
            $glXml_file = fopen($glMyFile, "w+", true);
            $this->xmlGoal->write_file_handle($glXml_file);
            fclose($glXml_file);
        }
        //Create xmlImsManifest
        $this->xmlImsManifest->create_root();
        $this->xmlImsManifest->roottag->name = "manifest";
        $this->xmlImsManifest->roottag->set_attributes(array(
            'xmlns' => 'http://www.imsglobal.org/xsd/imsportfoliocp_v1p0',
            'xmlns:imsmd' => 'http://www.imsglobal.org/xsd/imsmd_v1p2',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.imsglobal.org/xsd/imsportfoliocp_v1p0 http://www.imsglobal.org/xsd/imsportfoliocp_v1p0.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd',
            'identifier' => 'MANIFEST-9038769A-73K2-A91E-8RCF-4S484BGGE6C0',
            'version' => '1.0'
        ));
        $imsManiTag = &$this->xmlImsManifest->roottag;
        //Tag metadata
        $imsManiTag->add_subtag("metadata", array());
        $imsManiTag->curtag->add_subtag("schema", array());
        $imsManiTag->curtag->curtag->cdata = "Portfolio Package";
        $imsManiTag->curtag->add_subtag("schemaversion", array());
        $imsManiTag->curtag->curtag->cdata = "1.0";
        //Unique Id for Chisimba
        $orgsDefaultId = 'ORG-4433B3DD-3393-3080-9359-918574';
        //Tag organizations
        $imsManiTag->add_subtag("organizations", array(
            'default' => $orgsDefaultId
        ));
        $imsManiTag->curtag->add_subtag("organization", array(
            'identifier' => $orgsDefaultId
        ));
        $imsManiTag->curtag->curtag->add_subtag("title", array());
        $imsManiTag->curtag->curtag->curtag->cdata = $userInfo['surname'] . "PortfolioPackage";
        $imsManiTag->curtag->curtag->add_subtag("item", array(
            'identifier' => "ITEM-PORTFOLIOPARTS-ROOT"
        ));
        $imsManiTag->curtag->curtag->curtag->add_subtag("title", array());
        $imsManiTag->curtag->curtag->curtag->curtag->cdata = "PortfolioParts";
        //Identification
        $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
            'identifier' => "ITEM-PORTFOLIOPART-001",
            'identifierref' => "RES-PORTFOLIOPART-001"
        ));
        $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
        $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Identification";
        //Qualification
        if (!empty($qclList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-002",
                'identifierref' => "RES-PORTFOLIOPART-002"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Qualification";
        }
        //Affiliation
        if (!empty($affiliationList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-003",
                'identifierref' => "RES-PORTFOLIOPART-003"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Affiliation";
        }
        //Transcript
        if (!empty($transcriptList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-004",
                'identifierref' => "RES-PORTFOLIOPART-004"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Transcript";
        }
        //Activity
        if (!empty($activityList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-005",
                'identifierref' => "RES-PORTFOLIOPART-005"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Activity";
        }
        //Assertion
        if (!empty($assertionList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-006",
                'identifierref' => "RES-PORTFOLIOPART-006"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Assertion";
        }
        //Reflexion
        if (!empty($reflectionList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-007",
                'identifierref' => "RES-PORTFOLIOPART-007"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Reflexion";
        }
        //Competency
        if (!empty($competencyList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-008",
                'identifierref' => "RES-PORTFOLIOPART-008"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Competency";
        }
        //Interest
        if (!empty($interestList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-009",
                'identifierref' => "RES-PORTFOLIOPART-009"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Interest";
        }
        //Goal
        if (!empty($goalList)) {
            $imsManiTag->curtag->curtag->curtag->add_subtag("item", array(
                'identifier' => "ITEM-PORTFOLIOPART-010",
                'identifierref' => "RES-PORTFOLIOPART-010"
            ));
            $imsManiTag->curtag->curtag->curtag->curtag->add_subtag("title", array());
            $imsManiTag->curtag->curtag->curtag->curtag->curtag->cdata = "Goal";
        }
        //The zipfile name
        $dirName = $userInfo['title'] . '.' . $userInfo['surname'] . '-' . "ePortfolio/";
        //Include Resources
        $imsManiTag->add_subtag("resources", array());
        $imsManiTag->curtag->add_subtag("resource", array(
            'identifier' => "RES-PORTFOLIOPART-001",
            'type' => "imslip-Identification"
        ));
        $imsManiTag->curtag->curtag->add_subtag("file", array(
            'href' => "Identification.xml"
        ));
        if (!empty($qclList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-002",
                'type' => "imslip-Qualification"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Qualification.xml"
            ));
            if (!empty($aPathQcl)) {
                //Add image and files
                foreach($aPathQcl as $aPathQclN) {
                    $explodeaQcl = explode('/', $aPathQclN);
                    $explodeaQclCount = count($explodeaQcl);
                    $explodeaQclFileNo = $explodeaQclCount-1;
                    $explodeaQclFilePath = $explodeaQclCount-2;
                    $aFileName = $explodeaQcl[$explodeaQclFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaQcl[$explodeaQclFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaQcl[$explodeaQclFilePath] . "/";
                            $explodeaQclFilePath = $explodeaQclFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathQclN)) {
                        $fileContents = file_get_contents($aPathQclN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathQcl)) {
                foreach($imgPathQcl as $imgPathQclN) {
                    $explodeimgQcl = explode('/', $imgPathQclN);
                    $explodeimgQclCount = count($explodeimgQcl);
                    $explodeimgQclFileNo = $explodeimgQclCount-1;
                    $explodeimgQclFilePath = $explodeimgQclCount-2;
                    $imgFileName = $explodeimgQcl[$explodeimgQclFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgQcl[$explodeimgQclFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgQcl[$explodeimgQclFilePath] . "/";
                            $explodeimgQclFilePath = $explodeimgQclFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathQclN)) {
                        $fileContents = file_get_contents($imgPathQclN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        if (!empty($affiliationList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-003",
                'type' => "imslip-Affiliation"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Affiliation.xml"
            ));
            //Add image and files
            if (!empty($aPathAff)) {
                foreach($aPathAff as $aPathAffN) {
                    $explodeaAff = explode('/', $aPathAffN);
                    $explodeaAffCount = count($explodeaAff);
                    $explodeaAffFileNo = $explodeaAffCount-1;
                    $explodeaAffFilePath = $explodeaAffCount-2;
                    $aFileName = $explodeaAff[$explodeaAffFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaAff[$explodeaAffFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaAff[$explodeaAffFilePath] . "/";
                            $explodeaAffFilePath = $explodeaAffFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathAffN)) {
                        $fileContents = file_get_contents($aPathAffN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathAff)) {
                foreach($imgPathAff as $imgPathAffN) {
                    $explodeimgAff = explode('/', $imgPathAffN);
                    $explodeimgAffCount = count($explodeimgAff);
                    $explodeimgAffFileNo = $explodeimgAffCount-1;
                    $explodeimgAffFilePath = $explodeimgAffCount-2;
                    $imgFileName = $explodeimgAff[$explodeimgAffFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgAff[$explodeimgAffFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgAff[$explodeimgAffFilePath] . "/";
                            $explodeimgAffFilePath = $explodeimgAffFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathAffN)) {
                        $fileContents = file_get_contents($imgPathAffN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        if (!empty($transcriptList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-004",
                'type' => "imslip-Transcript"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Transcript.xml"
            ));
            //Add image and files
            if (!empty($aPathTrans)) {
                foreach($aPathTrans as $aPathTransN) {
                    $explodeaTrans = explode('/', $aPathTransN);
                    $explodeaTransCount = count($explodeaTrans);
                    $explodeaTransFileNo = $explodeaTransCount-1;
                    $explodeaTransFilePath = $explodeaTransCount-2;
                    $aFileName = $explodeaTrans[$explodeaTransFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaTrans[$explodeaTransFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaTrans[$explodeaTransFilePath] . "/";
                            $explodeaTransFilePath = $explodeaTransFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathTransN)) {
                        $fileContents = file_get_contents($aPathTransN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathTrans)) {
                foreach($imgPathTrans as $imgPathTransN) {
                    $explodeimgTrans = explode('/', $imgPathTransN);
                    $explodeimgTransCount = count($explodeimgTrans);
                    $explodeimgTransFileNo = $explodeimgTransCount-1;
                    $explodeimgTransFilePath = $explodeimgTransCount-2;
                    $imgFileName = $explodeimgTrans[$explodeimgTransFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgTrans[$explodeimgTransFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgTrans[$explodeimgTransFilePath] . "/";
                            $explodeimgTransFilePath = $explodeimgTransFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathTransN)) {
                        $fileContents = file_get_contents($imgPathTransN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        if (!empty($activityList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-005",
                'type' => "imslip-Activity"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Activity.xml"
            ));
            //Add image and files
            if (!empty($aPathActivity)) {
                foreach($aPathActivity as $aPathActivityN) {
                    $explodeaAct = explode('/', $aPathActivityN);
                    $explodeaActCount = count($explodeaAct);
                    $explodeaActFileNo = $explodeaActCount-1;
                    $explodeaActFilePath = $explodeaActCount-2;
                    $aFileName = $explodeaAct[$explodeaActFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaAct[$explodeaActFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaAct[$explodeaActFilePath] . "/";
                            $explodeaActFilePath = $explodeaActFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathActivityN)) {
                        $fileContents = file_get_contents($aPathActivityN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathActivity)) {
                foreach($imgPathActivity as $imgPathActivityN) {
                    $explodeimgAct = explode('/', $imgPathActivityN);
                    $explodeimgActCount = count($explodeimgAct);
                    $explodeimgActFileNo = $explodeimgActCount-1;
                    $explodeimgActFilePath = $explodeimgActCount-2;
                    $imgFileName = $explodeimgAct[$explodeimgActFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgAct[$explodeimgActFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgAct[$explodeimgActFilePath] . "/";
                            $explodeimgActFilePath = $explodeimgActFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathActivityN)) {
                        $fileContents = file_get_contents($imgPathActivityN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        if (!empty($assertionList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-006",
                'type' => "imslip-Assertion"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Assertion.xml"
            ));
            //Add image and files
            if (!empty($aPathAssert)) {
                foreach($aPathAssert as $aPathAssertN) {
                    $explodeaAssert = explode('/', $aPathAssertN);
                    $explodeaAssertCount = count($explodeaAssert);
                    $explodeaAssertFileNo = $explodeaAssertCount-1;
                    $explodeaAssertFilePath = $explodeaAssertCount-2;
                    $aFileName = $explodeaAssert[$explodeaAssertFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaAssert[$explodeaAssertFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaAssert[$explodeaAssertFilePath] . "/";
                            $explodeaAssertFilePath = $explodeaAssertFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathAssertN)) {
                        $fileContents = file_get_contents($aPathAssertN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathAssert)) {
                foreach($imgPathAssert as $imgPathAssertN) {
                    $explodeimgAssert = explode('/', $imgPathAssertN);
                    $explodeimgAssertCount = count($explodeimgAssert);
                    $explodeimgAssertFileNo = $explodeimgAssertCount-1;
                    $explodeimgAssertFilePath = $explodeimgAssertCount-2;
                    $imgFileName = $explodeimgAssert[$explodeimgAssertFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgAssert[$explodeimgAssertFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgAssert[$explodeimgAssertFilePath] . "/";
                            $explodeimgAssertFilePath = $explodeimgAssertFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathAssertN)) {
                        $fileContents = file_get_contents($imgPathAssertN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        if (!empty($reflectionList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-007",
                'type' => "imslip-Reflexion"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Reflexion.xml"
            ));
            //Add image and files
            if (!empty($aPathRefl)) {
                foreach($aPathRefl as $aPathReflN) {
                    $explodeaRefl = explode('/', $aPathReflN);
                    $explodeaReflCount = count($explodeaRefl);
                    $explodeaReflFileNo = $explodeaReflCount-1;
                    $explodeaReflFilePath = $explodeaReflCount-2;
                    $aFileName = $explodeaRefl[$explodeaReflFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaRefl[$explodeaReflFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaRefl[$explodeaReflFilePath] . "/";
                            $explodeaReflFilePath = $explodeaReflFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathReflN)) {
                        $fileContents = file_get_contents($aPathReflN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathRefl)) {
                foreach($imgPathRefl as $imgPathReflN) {
                    $explodeimgRefl = explode('/', $imgPathReflN);
                    $explodeimgReflCount = count($explodeimgRefl);
                    $explodeimgReflFileNo = $explodeimgReflCount-1;
                    $explodeimgReflFilePath = $explodeimgReflCount-2;
                    $imgFileName = $explodeimgRefl[$explodeimgReflFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgRefl[$explodeimgReflFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgRefl[$explodeimgReflFilePath] . "/";
                            $explodeimgReflFilePath = $explodeimgReflFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathReflN)) {
                        $fileContents = file_get_contents($imgPathReflN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        if (!empty($competencyList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-008",
                'type' => "imslip-Competency"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Competency.xml"
            ));
            //Add image and files
            if (!empty($aPathComptcy)) {
                foreach($aPathComptcy as $aPathComptcyN) {
                    $explodeaComptcy = explode('/', $aPathComptcyN);
                    $explodeaComptcyCount = count($explodeaComptcy);
                    $explodeaComptcyFileNo = $explodeaComptcyCount-1;
                    $explodeaComptcyFilePath = $explodeaComptcyCount-2;
                    $aFileName = $explodeaComptcy[$explodeaComptcyFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaComptcy[$explodeaComptcyFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaComptcy[$explodeaComptcyFilePath] . "/";
                            $explodeaComptcyFilePath = $explodeaComptcyFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathComptcyN)) {
                        $fileContents = file_get_contents($aPathComptcyN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathComptcy)) {
                foreach($imgPathComptcy as $imgPathComptcyN) {
                    $explodeimgComptcy = explode('/', $imgPathComptcyN);
                    $explodeimgComptcyCount = count($explodeimgComptcy);
                    $explodeimgComptcyFileNo = $explodeimgComptcyCount-1;
                    $explodeimgComptcyFilePath = $explodeimgComptcyCount-2;
                    $imgFileName = $explodeimgComptcy[$explodeimgComptcyFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgComptcy[$explodeimgComptcyFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgComptcy[$explodeimgComptcyFilePath] . "/";
                            $explodeimgComptcyFilePath = $explodeimgComptcyFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathComptcyN)) {
                        $fileContents = file_get_contents($imgPathComptcyN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        if (!empty($interestList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-009",
                'type' => "imslip-Interest"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Interest.xml"
            ));
            //Add image and files
            if (!empty($aPathInt)) {
                foreach($aPathInt as $aPathIntN) {
                    $explodeaInt = explode('/', $aPathIntN);
                    $explodeaIntCount = count($explodeaInt);
                    $explodeaIntFileNo = $explodeaIntCount-1;
                    $explodeaIntFilePath = $explodeaIntCount-2;
                    $aFileName = $explodeaInt[$explodeaIntFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaInt[$explodeaIntFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaInt[$explodeaIntFilePath] . "/";
                            $explodeaIntFilePath = $explodeaIntFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathIntN)) {
                        $fileContents = file_get_contents($aPathIntN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathInt)) {
                foreach($imgPathInt as $imgPathIntN) {
                    $explodeimgInt = explode('/', $imgPathIntN);
                    $explodeimgIntCount = count($explodeimgInt);
                    $explodeimgIntFileNo = $explodeimgIntCount-1;
                    $explodeimgIntFilePath = $explodeimgIntCount-2;
                    $imgFileName = $explodeimgInt[$explodeimgIntFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgInt[$explodeimgIntFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgInt[$explodeimgIntFilePath] . "/";
                            $explodeimgIntFilePath = $explodeimgIntFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathIntN)) {
                        $fileContents = file_get_contents($imgPathIntN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        if (!empty($goalList)) {
            $imsManiTag->curtag->add_subtag("resource", array(
                'identifier' => "RES-PORTFOLIOPART-010",
                'type' => "imslip-Goal"
            ));
            $imsManiTag->curtag->curtag->add_subtag("file", array(
                'href' => "Goal.xml"
            ));
            //Add image and files
            if (!empty($aPathGoals)) {
                foreach($aPathGoals as $aPathGoalsN) {
                    $explodeaGoals = explode('/', $aPathGoalsN);
                    $explodeaGoalsCount = count($explodeaGoals);
                    $explodeaGoalsFileNo = $explodeaGoalsCount-1;
                    $explodeaGoalsFilePath = $explodeaGoalsCount-2;
                    $aFileName = $explodeaGoals[$explodeaGoalsFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeaGoals[$explodeaGoalsFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeaGoals[$explodeaGoalsFilePath] . "/";
                            $explodeaGoalsFilePath = $explodeaGoalsFilePath-1;
                        }
                    }
                    $aFileNamePath = $myPath . $aFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $aFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($aPathGoalsN)) {
                        $fileContents = file_get_contents($aPathGoalsN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $aFileNamePath);
                    }
                }
            }
            if (!empty($imgPathGoals)) {
                foreach($imgPathGoals as $imgPathGoalsN) {
                    $explodeimgGoals = explode('/', $imgPathGoalsN);
                    $explodeimgGoalsCount = count($explodeimgGoals);
                    $explodeimgGoalsFileNo = $explodeimgGoalsCount-1;
                    $explodeimgGoalsFilePath = $explodeimgGoalsCount-2;
                    $imgFileName = $explodeimgGoals[$explodeimgGoalsFileNo];
                    $testPath = 1;
                    while ($testPath == 1) {
                        if ($explodeimgGoals[$explodeimgGoalsFilePath] == $this->userId) {
                            $myPath = $myPath . "";
                            $testPath = 0;
                        } else {
                            $myPath = $myPath . "/" . $explodeimgGoals[$explodeimgGoalsFilePath] . "/";
                            $explodeimgGoalsFilePath = $explodeimgGoalsFilePath-1;
                        }
                    }
                    $imgFileNamePath = $myPath . $imgFileName;
                    $myPath = "";
                    //add file to the xml
                    $imsManiTag->curtag->curtag->add_subtag("file", array(
                        'href' => $imgFileNamePath
                    ));
                    //Add file to the zip file
                    if (file_exists($imgPathGoalsN)) {
                        $fileContents = file_get_contents($imgPathGoalsN, true);
                        $this->createZipFile->addFile($fileContents, $dirName . $imgFileNamePath);
                    }
                }
            }
        }
        //Write xml on imsmanifest.xml
        $imsXml_file = fopen($imsMyFile, "w+", true);
        $this->xmlImsManifest->write_file_handle($imsXml_file);
        fclose($imsXml_file);
        //Create Zip File of the above files
        //Add Identification XML File
        if (file_exists($identMyFile)) {
            $fileContents = file_get_contents($identMyFile, true);
            $this->createZipFile->addFile($fileContents, $dirName . $identFilename);
        }
        //Add Qualification XML File
        if (!empty($qclList)) {
            if (file_exists($qclMyFile)) {
                $fileContents = file_get_contents($qclMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $qclFilename);
            }
        }
        //Add Affiliation XML File
        if (!empty($affiliationList)) {
            if (file_exists($affMyFile)) {
                $fileContents = file_get_contents($affMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $affFilename);
            }
        }
        //Add Transcript XML File
        if (!empty($transcriptList)) {
            if (file_exists($transMyFile)) {
                $fileContents = file_get_contents($transMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $transFilename);
            }
        }
        //Add Activity XML File
        if (!empty($activityList)) {
            if (file_exists($actMyFile)) {
                $fileContents = file_get_contents($actMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $actFilename);
            }
        }
        //Add Assertion XML File
        if (!empty($assertionList)) {
            if (file_exists($assertMyFile)) {
                $fileContents = file_get_contents($assertMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $assertFilename);
            }
        }
        //Add Reflexion XML File
        if (!empty($reflectionList)) {
            if (file_exists($reflMyFile)) {
                $fileContents = file_get_contents($reflMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $reflFilename);
            }
        }
        //Add Competency XML File
        if (!empty($competencyList)) {
            if (file_exists($cotcyMyFile)) {
                $fileContents = file_get_contents($cotcyMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $cotcyFilename);
            }
        }
        //Add Interest XML File
        if (!empty($interestList)) {
            if (file_exists($intMyFile)) {
                $fileContents = file_get_contents($intMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $intFilename);
            }
        }
        //Add Goal XML File
        if (!empty($goalList)) {
            if (file_exists($glMyFile)) {
                $fileContents = file_get_contents($glMyFile, true);
                $this->createZipFile->addFile($fileContents, $dirName . $glFilename);
            }
        }
        //Add imsmanifest XML File
        if (file_exists($imsMyFile)) {
            $fileContents = file_get_contents($imsMyFile, true);
            $this->createZipFile->addFile($fileContents, $dirName . $imsFilename);
        }
        $fileName = $userInfo['title'] . '.' . $userInfo['surname'] . '-' . "ePortfolio.zip";
        $fd = fopen($fileName, "wb");
        $out = fwrite($fd, $this->createZipFile->getZippedfile());
        fclose($fd);
        //Force download of the zip file
        $this->createZipFile->forceDownload($fileName);
        @unlink($fileName);
    }
    // Import from XML
    //@var string rootFolder of eportfolio
    public function importFromXML($rootFolder = Null) 
    {
        $totalInserts = "<ul>";
        $document = new DOMDocument();
        //	$doc->load( 'usrfiles/'.$rootFolder.'/imsmanifest.xml' );
        //$document->load( 'usrfiles/users/1/myeportfolio/Dr.User-ePortfolio/imsmanifest.xml' );
        $document->load('usrfiles/' . $rootFolder . '/imsmanifest.xml');
        //get the URI of the default namespace
        $uri = $document->documentElement->lookupnamespaceURI(NULL);
        //Get all <ORGANIZATION> tags
        $allOrgs = $document->getElementsByTagName("organization");
        $resource = $document->getElementsByTagName("resource");
        foreach($allOrgs as $thisOrg) {
            //Get all <ITEM> tags within <ORGANIZATION>
            $orgItems = $thisOrg->getElementsByTagName("item");
            //Get <TITLE> tag within <ORGANIZATION>
            $orgTitle = $thisOrg->getElementsByTagName("title");
            //Step thru each ORGANIZATION tag
            foreach($orgItems as $orgItem) {
                //Get all <ITEM> tags within <ORGANIZATION>
                $thisOrgItems = $orgItem->getElementsByTagName("item");
                foreach($thisOrgItems as $thisOrgItem) {
                    //Get <TITLE> tag within <ITEM>
                    $myItemTitle = $thisOrgItem->getElementsByTagName("title");
                    //Get attributes within <ITEM>
                    $myItemIdentifier = $thisOrgItem->getAttribute('identifier');
                    $myItemIdentifierRef = $thisOrgItem->getAttribute('identifierref');
                    //Get Item Resources
                    foreach($resource as $thisResource) {
                        if ($thisResource->getAttribute('identifier') == $myItemIdentifierRef) {
                            //Get <FILE> tag within <RESOURCE>
                            $resourceFiles = $thisResource->getElementsByTagName("file");
                            //Get attributes within <RESOURCE>
                            $resourceType = $thisResource->getAttribute('type');
                            //Get Item Resources
                            foreach($resourceFiles as $thisFile) {
                                //Get attributes within <FILE>
                                $fileHref = $thisFile->getAttribute('href');
                                $findXml = explode('.', $fileHref);
                                if ($findXml['1'] == 'xml') {
                                    $doc = new DOMDocument();
                                    //$doc->load( 'usrfiles/users/1/myeportfolio/Dr.User-ePortfolio/'.$fileHref );
                                    $doc->load('usrfiles/' . $rootFolder . '/' . $fileHref);
                                    //Root tag learnerinformation
                                    $getRoot = $doc->getElementsByTagName("learnerinformation");
                                    //var_dump($getRoot->item(0)->nodeValue."<br>");
                                    foreach($getRoot as $thisRoot) {
                                        //Identification
                                        $getIdentification = $thisRoot->getElementsByTagName("identification");
                                        if ($getIdentification->length !== 0) {
                                            foreach($getIdentification as $thisIdentification) {
                                                $allAddress = $thisIdentification->getElementsByTagName("address");
                                                if ($allAddress->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($allAddress as $myAddress) {
                                                        $tyValueAddress = $myAddress->getElementsByTagName("tyvalue");
                                                        $streetNumber = $myAddress->getElementsByTagName("streetnumber");
                                                        $streetName = $myAddress->getElementsByTagName("streetname");
                                                        $locality = $myAddress->getElementsByTagName("locality");
                                                        $city = $myAddress->getElementsByTagName("city");
                                                        $postCode = $myAddress->getElementsByTagName("postcode");
                                                        //Check if category type exists, if not add
                                                        $checkType = $this->objDbCategorytypeList->listByType($tyValueAddress->item(0)->nodeValue);
                                                        if (!empty($checkType)) {
                                                            foreach($checkType as $mycheckType) {
                                                                $myType = $mycheckType['id'];
                                                            }
                                                        } else {
                                                            $catType = $this->objDbCategoryList->listCategory("Place");
                                                            foreach($catType as $myCatType) {
                                                                $catId = $myCatType['id'];
                                                            }
                                                            $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                        }
                                                        //Insert the data now
                                                        $id = $this->objDbAddressList->insertSingle($myType, $streetNumber->item(0)->nodeValue, $streetName->item(0)->nodeValue, $locality->item(0)->nodeValue, $city->item(0)->nodeValue, $postCode->item(0)->nodeValue, Null);
                                                        $count = $count+1;
                                                    }
                                                    $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_addAddress", 'eportfolio') . "</li>";
                                                }
                                                $allContactInfo = $thisIdentification->getElementsByTagName("contactinfo");
                                                if ($allContactInfo->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($allContactInfo as $myContactInfo) {
                                                        $tyValueContact = $myContactInfo->getElementsByTagName("tyvalue");
                                                        $countryCode = $myContactInfo->getElementsByTagName("countrycode");
                                                        $areaCode = $myContactInfo->getElementsByTagName("areacode");
                                                        if ($myContactInfo->getElementsByTagName("telephone")->length !== 0) {
                                                            $contype = $myContactInfo->getElementsByTagName("telephone");
                                                            $contypename = $contype->item(0)->nodeName;
                                                        } elseif ($myContactInfo->getElementsByTagName("cellphone")->length !== 0) {
                                                            $contype = $myContactInfo->getElementsByTagName("cellphone");
                                                            $contypename = $contype->item(0)->nodeName;
                                                        } elseif ($myContactInfo->getElementsByTagName("facsimile")->length !== 0) {
                                                            $contype = $myContactInfo->getElementsByTagName("facsimile");
                                                            $contypename = $contype->item(0)->nodeName;
                                                        } else {
                                                            $contypename = "";
                                                        }
                                                        $indNumber = $myContactInfo->getElementsByTagName("indnumber");
                                                        $email = $myContactInfo->getElementsByTagName("email");
                                                        //Check if category type exists, if not add
                                                        $checkType = $this->objDbCategorytypeList->listByType($tyValueContact->item(0)->nodeValue);
                                                        if (!empty($checkType)) {
                                                            foreach($checkType as $mycheckType) {
                                                                $myType = $mycheckType['id'];
                                                            }
                                                        } else {
                                                            $catType = $this->objDbCategoryList->listCategory("Place");
                                                            foreach($catType as $myCatType) {
                                                                $catId = $myCatType['id'];
                                                            }
                                                            $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                        }
                                                        if (!empty($contypename)) {
                                                            //Check if category type exists, if not add
                                                            $checkType2 = $this->objDbCategorytypeList->listByType($contypename);
                                                            if (!empty($checkType2)) {
                                                                foreach($checkType2 as $mycheckType) {
                                                                    $contactType = $mycheckType['id'];
                                                                }
                                                            } else {
                                                                $catType = $this->objDbCategoryList->listCategory("Mode");
                                                                foreach($catType as $myCatType) {
                                                                    $catId = $myCatType['id'];
                                                                }
                                                                $contactType = $this->objDbCategorytypeList->insertSingle($catId, ucfirst($contypename));
                                                            }
                                                        } else {
                                                            $contactType = "";
                                                        }
                                                        //Insert the data now
                                                        if ($email->length !== 0) {
                                                            $id = $this->objDbEmailList->insertSingle($myType, $email->item(0)->nodeValue);
                                                        } else {
                                                            $id = $this->objDbContactList->insertSingle($myType, $contactType, $countryCode->item(0)->nodeValue, $areaCode->item(0)->nodeValue, $indNumber->item(0)->nodeValue);
                                                        }
                                                        $count = $count+1;
                                                    }
                                                    $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordContact", 'eportfolio') . "</li>";
                                                    if ($email->length !== 0) {
                                                        $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordEmail", 'eportfolio') . "</li>";
                                                    }
                                                }
                                                $allDemographics = $thisIdentification->getElementsByTagName("demographics");
                                                if ($allDemographics->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($allDemographics as $myDemographics) {
                                                        $alltypename = $myDemographics->getElementsByTagName("typename");
                                                        if ($alltypename->length !== 0) {
                                                            $count = 0;
                                                            $tyValue = array();
                                                            foreach($alltypename as $typename) {
                                                                $tyValue[$count] = $typename->getElementsByTagName("tyvalue");
                                                                $count = $count+1;
                                                            }
                                                        }
                                                        $dateTime = $myDemographics->getElementsByTagName("datetime");
                                                        $nationality = $myDemographics->getElementsByTagName("nationality");
                                                        //Check if category type exists, if not add
                                                        $checkType = $this->objDbCategorytypeList->listByType($tyValue[0]->item(0)->nodeValue);
                                                        if (!empty($checkType)) {
                                                            foreach($checkType as $mycheckType) {
                                                                $myType = $mycheckType['id'];
                                                            }
                                                        } else {
                                                            $catType = $this->objDbCategoryList->listCategory("Demographics0");
                                                            foreach($catType as $myCatType) {
                                                                $catId = $myCatType['id'];
                                                            }
                                                            $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                        }
                                                        $demoExists = $this->objDbDemographicsList->listAll($this->userId);
                                                        if (!empty($demoExists)) {
                                                            foreach($demoExists as $mainRow) {
                                                                $id = $this->objDbDemographicsList->updateSingle($mainRow['id'], $myType, $dateTime->item(0)->nodeValue, $nationality->item(0)->nodeValue);
                                                            }
                                                        } else {
                                                            //Insert the data now
                                                            $id = $this->objDbDemographicsList->insertSingle($myType, $dateTime->item(0)->nodeValue, $nationality->item(0)->nodeValue);
                                                        }
                                                        $count = $count+1;
                                                    }
                                                    $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordDemographics", 'eportfolio') . "</li>";
                                                }
                                            }
                                        }
                                        //Activity
                                        $getActivity = $thisRoot->getElementsByTagName("activity");
                                        if ($getActivity->length !== 0) {
                                            foreach($getActivity as $thisActivity) {
                                                $alltypename = $thisActivity->getElementsByTagName("typename");
                                                if ($alltypename->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($alltypename as $typename) {
                                                        $tyValue[$count] = $typename->getElementsByTagName("tyvalue");
                                                        $count = $count+1;
                                                    }
                                                }
                                                $allDate = $thisActivity->getElementsByTagName("date");
                                                if ($allDate->length !== 0) {
                                                    $count = 0;
                                                    foreach($allDate as $date) {
                                                        $allTypeNames = $date->getElementsByTagName("typename");
                                                        if ($allTypeNames->length !== 0) {
                                                            foreach($allTypeNames as $myTypeName) {
                                                                $mytyValue[$count] = $myTypeName->getElementsByTagName("tyvalue");
                                                            }
                                                        }
                                                        $datetime[$count] = $date->getElementsByTagName("datetime");
                                                        $count = $count+1;
                                                    }
                                                    if (!empty($datetime[0]->item(0)->nodeValue)) {
                                                        $startDate = $datetime[0]->item(0)->nodeValue;
                                                    } else {
                                                        $startDate = "0";
                                                    }
                                                    if (!empty($datetime[1]->item(0)->nodeValue)) {
                                                        $finDate = $datetime[1]->item(0)->nodeValue;
                                                    } else {
                                                        $finDate = "0";
                                                    }
                                                }
                                                $shortDesc = $thisActivity->getElementsByTagName("short");
                                                $longDesc = $thisActivity->getElementsByTagName("long");
                                                //Check if category type exists, if not add
                                                $checkType = $this->objDbCategorytypeList->listByType($tyValue[0]->item(0)->nodeValue);
                                                if (!empty($checkType)) {
                                                    foreach($checkType as $mycheckType) {
                                                        $myType = $mycheckType['id'];
                                                    }
                                                } else {
                                                    $catType = $this->objDbCategoryList->listCategory("Place");
                                                    foreach($catType as $myCatType) {
                                                        $catId = $myCatType['id'];
                                                    }
                                                    $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                }
                                                //Insert the data now
                                                $id = $this->objDbActivityList->insertSingle($contextid = "None", $myType, $startDate, $finDate, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio') . "</li>";
                                        }
                                        //Affiliation
                                        $getAffiliation = $thisRoot->getElementsByTagName("affiliation");
                                        if ($getAffiliation->length !== 0) {
                                            foreach($getAffiliation as $thisAffiliation) {
                                                $alltypename = $thisAffiliation->getElementsByTagName("typename");
                                                if ($alltypename->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($alltypename as $typename) {
                                                        $tyValue[$count] = $typename->getElementsByTagName("tyvalue");
                                                        $count = $count+1;
                                                    }
                                                }
                                                $classification = $thisAffiliation->getElementsByTagName("classification");
                                                $organisation = $thisAffiliation->getElementsByTagName("organisation");
                                                $allDate = $thisAffiliation->getElementsByTagName("date");
                                                if ($allDate->length !== 0) {
                                                    $count = 0;
                                                    foreach($allDate as $date) {
                                                        $allTypeNames = $date->getElementsByTagName("typename");
                                                        if ($allTypeNames->length !== 0) {
                                                            foreach($allTypeNames as $myTypeName) {
                                                                $mytyValue[$count] = $myTypeName->getElementsByTagName("tyvalue");
                                                            }
                                                        }
                                                        $datetime[$count] = $date->getElementsByTagName("datetime");
                                                        $count = $count+1;
                                                    }
                                                    if (!empty($datetime[0]->item(0)->nodeValue)) {
                                                        $startDate = $datetime[0]->item(0)->nodeValue;
                                                    } else {
                                                        $startDate = "0";
                                                    }
                                                    if (!empty($datetime[1]->item(0)->nodeValue)) {
                                                        $finDate = $datetime[1]->item(0)->nodeValue;
                                                    } else {
                                                        $finDate = "0";
                                                    }
                                                }
                                                $shortDesc = $thisAffiliation->getElementsByTagName("short");
                                                $longDesc = $thisAffiliation->getElementsByTagName("long");
                                                //Check if category type exists, if not add
                                                $checkType = $this->objDbCategorytypeList->listByType($tyValue[0]->item(0)->nodeValue);
                                                if (!empty($checkType)) {
                                                    foreach($checkType as $mycheckType) {
                                                        $myType = $mycheckType['id'];
                                                    }
                                                } else {
                                                    $catType = $this->objDbCategoryList->listCategory("Affiliation");
                                                    foreach($catType as $myCatType) {
                                                        $catId = $myCatType['id'];
                                                    }
                                                    $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                }
                                                //Insert the data now
                                                $id = $this->objDbAffiliationList->insertSingle($myType, $classification->item(0)->nodeValue, $tyValue[1]->item(0)->nodeValue, $organisation->item(0)->nodeValue, $startDate, $finDate, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio') . "</li>";
                                        }
                                        //Transcript
                                        $getTranscript = $thisRoot->getElementsByTagName("transcript");
                                        if ($getTranscript->length !== 0) {
                                            foreach($getTranscript as $thisTranscript) {
                                                $tyValue = $thisTranscript->getElementsByTagName("tyvalue");
                                                $shortDesc = $thisTranscript->getElementsByTagName("short");
                                                $longDesc = $thisTranscript->getElementsByTagName("long");
                                                //Insert the data
                                                $id = $this->objDbTranscriptList->insertSingle($tyValue->item(0)->nodeValue, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordTranscripts", 'eportfolio') . "</li>";
                                        }
                                        //Qualification
                                        $getQualification = $thisRoot->getElementsByTagName("qcl");
                                        if ($getQualification->length !== 0) {
                                            foreach($getQualification as $thisQualification) {
                                                $alltypename = $thisQualification->getElementsByTagName("typename");
                                                if ($alltypename->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($alltypename as $typename) {
                                                        $tyValue[$count] = $typename->getElementsByTagName("tyvalue");
                                                        $count = $count+1;
                                                    }
                                                }
                                                $title = $thisQualification->getElementsByTagName("title");
                                                $level = $thisQualification->getElementsByTagName("level");
                                                $allDate = $thisQualification->getElementsByTagName("date");
                                                if ($allDate->length !== 0) {
                                                    $count = 0;
                                                    foreach($allDate as $date) {
                                                        $allTypeNames = $date->getElementsByTagName("typename");
                                                        if ($allTypeNames->length !== 0) {
                                                            foreach($allTypeNames as $myTypeName) {
                                                                $mytyValue[$count] = $myTypeName->getElementsByTagName("tyvalue");
                                                            }
                                                        }
                                                        $datetime[$count] = $date->getElementsByTagName("datetime");
                                                        $count = $count+1;
                                                    }
                                                    if (!empty($datetime[0]->item(0)->nodeValue)) {
                                                        $awardDate = $datetime[0]->item(0)->nodeValue;
                                                    } else {
                                                        $awardDate = "0";
                                                    }
                                                }
                                                $shortDesc = $thisQualification->getElementsByTagName("short");
                                                $longDesc = $thisQualification->getElementsByTagName("full");
                                                if (empty($longDesc->item(0)->nodeValue)) {
                                                    $longDesc = $thisQualification->getElementsByTagName("long");
                                                }
                                                //Check if category type exists, if not add
                                                $checkType = $this->objDbCategorytypeList->listByType($tyValue[0]->item(0)->nodeValue);
                                                if (!empty($checkType)) {
                                                    foreach($checkType as $mycheckType) {
                                                        $myType = $mycheckType['id'];
                                                    }
                                                } else {
                                                    $catType = $this->objDbCategoryList->listCategory("Affiliation");
                                                    foreach($catType as $myCatType) {
                                                        $catId = $myCatType['id'];
                                                    }
                                                    $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                }
                                                //Insert the data now
                                                $id = $this->objDbQclList->insertSingle($myType, $title->item(0)->nodeValue, $shortDesc->item(0)->nodeValue, $level->item(0)->nodeValue, $awardDate, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio') . "</li>";
                                        }
                                        //Goal
                                        $getGoal = $thisRoot->getElementsByTagName("goal");
                                        if ($getGoal->length !== 0) {
                                            foreach($getGoal as $thisGoal) {
                                                $alltypename = $thisGoal->getElementsByTagName("tyvalue");
                                                $allstatus = $thisGoal->getElementsByTagName("status");
                                                if ($allstatus->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($allstatus as $status) {
                                                        $statustyValue[$count] = $status->getElementsByTagName("tyvalue");
                                                        $count = $count+1;
                                                    }
                                                }
                                                $priority = $thisGoal->getElementsByTagName("priority");
                                                $allDate = $thisGoal->getElementsByTagName("date");
                                                if ($allDate->length !== 0) {
                                                    $count = 0;
                                                    foreach($allDate as $date) {
                                                        $allTypeNames = $date->getElementsByTagName("typename");
                                                        if ($allTypeNames->length !== 0) {
                                                            foreach($allTypeNames as $myTypeName) {
                                                                $mytyValue[$count] = $myTypeName->getElementsByTagName("tyvalue");
                                                            }
                                                        }
                                                        $datetime[$count] = $date->getElementsByTagName("datetime");
                                                        $count = $count+1;
                                                    }
                                                    if (!empty($datetime[0]->item(0)->nodeValue)) {
                                                        $startDate = $datetime[0]->item(0)->nodeValue;
                                                    } else {
                                                        $startDate = "0";
                                                    }
                                                    if (!empty($datetime[1]->item(0)->nodeValue)) {
                                                        $status_date = $datetime[1]->item(0)->nodeValue;
                                                    } else {
                                                        $status_date = "0";
                                                    }
                                                }
                                                $shortDesc = $thisGoal->getElementsByTagName("short");
                                                $longDesc = $thisGoal->getElementsByTagName("long");
                                                //Check if category type exists, if not add
                                                $checkType = $this->objDbCategorytypeList->listByType($alltypename->item(0)->nodeValue);
                                                if (!empty($checkType)) {
                                                    foreach($checkType as $mycheckType) {
                                                        $myType = $mycheckType['id'];
                                                    }
                                                } else {
                                                    $catType = $this->objDbCategoryList->listCategory("Place");
                                                    foreach($catType as $myCatType) {
                                                        $catId = $myCatType['id'];
                                                    }
                                                    $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                }
                                                //Check if category type exists, if not add -- Priority
                                                $checkPriorityType = $this->objDbCategorytypeList->listByType($priority->item(0)->nodeValue);
                                                if (!empty($checkPriorityType)) {
                                                    foreach($checkPriorityType as $prioritycheckType) {
                                                        $priorityType = $prioritycheckType['id'];
                                                    }
                                                } else {
                                                    $priorityType = $this->objDbCategoryList->listCategory("Priority");
                                                    foreach($prioritycatType as $mypriorityCatType) {
                                                        $priorityCatId = $mypriorityCatType['id'];
                                                    }
                                                    $priorityType = $this->objDbCategorytypeList->insertSingle($priorityCatId, $priority->item(0)->nodeValue);
                                                }
                                                //Insert the data now
                                                $id = $this->objDbGoalList->insertSingle($parentid = Null, $myType, $startDate, $priorityType, $statustyValue[0]->item(0)->nodeValue, $status_date, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordGoals", 'eportfolio') . "</li>";
                                        }
                                        //Competency
                                        $getCompetency = $thisRoot->getElementsByTagName("competency");
                                        if ($getCompetency->length !== 0) {
                                            foreach($getCompetency as $thisCompetency) {
                                                $alltypename = $thisCompetency->getElementsByTagName("typename");
                                                if ($alltypename->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($alltypename as $typename) {
                                                        $tyValue[$count] = $typename->getElementsByTagName("tyvalue");
                                                        $count = $count+1;
                                                    }
                                                }
                                                $allDate = $thisCompetency->getElementsByTagName("date");
                                                if ($allDate->length !== 0) {
                                                    $count = 0;
                                                    foreach($allDate as $date) {
                                                        $allTypeNames = $date->getElementsByTagName("typename");
                                                        if ($allTypeNames->length !== 0) {
                                                            foreach($allTypeNames as $myTypeName) {
                                                                $mytyValue[$count] = $myTypeName->getElementsByTagName("tyvalue");
                                                            }
                                                        }
                                                        $datetime[$count] = $date->getElementsByTagName("datetime");
                                                        $count = $count+1;
                                                    }
                                                    if (!empty($datetime[0]->item(0)->nodeValue)) {
                                                        $awardDate = $datetime[0]->item(0)->nodeValue;
                                                    } else {
                                                        $awardDate = "0";
                                                    }
                                                }
                                                $shortDesc = $thisCompetency->getElementsByTagName("short");
                                                $longDesc = $thisCompetency->getElementsByTagName("long");
                                                //Check if category type exists, if not add
                                                $checkType = $this->objDbCategorytypeList->listByType($tyValue[0]->item(0)->nodeValue);
                                                if (!empty($checkType)) {
                                                    foreach($checkType as $mycheckType) {
                                                        $myType = $mycheckType['id'];
                                                    }
                                                } else {
                                                    $catType = $this->objDbCategoryList->listCategory("Competency");
                                                    foreach($catType as $myCatType) {
                                                        $catId = $myCatType['id'];
                                                    }
                                                    $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                }
                                                //Insert the data now
                                                $id = $this->objDbCompetencyList->insertSingle($myType, $awardDate, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio') . "</li>";
                                        }
                                        //Interest
                                        $getInterest = $thisRoot->getElementsByTagName("interest");
                                        if ($getInterest->length !== 0) {
                                            foreach($getInterest as $thisInterest) {
                                                $alltypename = $thisInterest->getElementsByTagName("typename");
                                                if ($alltypename->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($alltypename as $typename) {
                                                        $tyValue[$count] = $typename->getElementsByTagName("tyvalue");
                                                        $count = $count+1;
                                                    }
                                                }
                                                $allDate = $thisInterest->getElementsByTagName("date");
                                                if ($allDate->length !== 0) {
                                                    $count = 0;
                                                    foreach($allDate as $date) {
                                                        $allTypeNames = $date->getElementsByTagName("typename");
                                                        if ($allTypeNames->length !== 0) {
                                                            foreach($allTypeNames as $myTypeName) {
                                                                $mytyValue[$count] = $myTypeName->getElementsByTagName("tyvalue");
                                                            }
                                                        }
                                                        $datetime[$count] = $date->getElementsByTagName("datetime");
                                                        $count = $count+1;
                                                    }
                                                    if (!empty($datetime[0]->item(0)->nodeValue)) {
                                                        $creation_date = $datetime[0]->item(0)->nodeValue;
                                                    } else {
                                                        $creation_date = "0";
                                                    }
                                                }
                                                $shortDesc = $thisInterest->getElementsByTagName("short");
                                                $longDesc = $thisInterest->getElementsByTagName("long");
                                                //Check if category type exists, if not add
                                                $checkType = $this->objDbCategorytypeList->listByType($tyValue[0]->item(0)->nodeValue);
                                                if (!empty($checkType)) {
                                                    foreach($checkType as $mycheckType) {
                                                        $myType = $mycheckType['id'];
                                                    }
                                                } else {
                                                    $catType = $this->objDbCategoryList->listCategory("Interest");
                                                    foreach($catType as $myCatType) {
                                                        $catId = $myCatType['id'];
                                                    }
                                                    $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                }
                                                //Insert the data now
                                                $id = $this->objDbInterestList->insertSingle($myType, $creation_date, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordInterest", 'eportfolio') . "</li>";
                                        }
                                        //Reflection
                                        $getReflection = $thisRoot->getElementsByTagName("reflexion");
                                        if ($getReflection->length !== 0) {
                                            foreach($getReflection as $thisReflection) {
                                                $alltypename = $thisReflection->getElementsByTagName("typename");
                                                if ($alltypename->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($alltypename as $typename) {
                                                        $tyValue[$count] = $typename->getElementsByTagName("tyvalue");
                                                        $count = $count+1;
                                                    }
                                                }
                                                $allauthors = $thisReflection->getElementsByTagName("authorship");
                                                if ($allauthors->length !== 0) {
                                                    foreach($allauthors as $authorship) {
                                                        $author = $authorship->getElementsByTagName("text");
                                                        $authorLang = $authorship->getElementsByTagName("language");
                                                    }
                                                }
                                                $allrationale = $thisReflection->getElementsByTagName("rationale");
                                                if ($allrationale->length !== 0) {
                                                    foreach($allrationale as $myrationale) {
                                                        $rationale = $myrationale->getElementsByTagName("text");
                                                        $rationaleLang = $myrationale->getElementsByTagName("language");
                                                    }
                                                }
                                                $allDate = $thisReflection->getElementsByTagName("date");
                                                if ($allDate->length !== 0) {
                                                    $count = 0;
                                                    foreach($allDate as $date) {
                                                        $allTypeNames = $date->getElementsByTagName("typename");
                                                        if ($allTypeNames->length !== 0) {
                                                            foreach($allTypeNames as $myTypeName) {
                                                                $mytyValue[$count] = $myTypeName->getElementsByTagName("tyvalue");
                                                            }
                                                        }
                                                        $datetime[$count] = $date->getElementsByTagName("datetime");
                                                        $count = $count+1;
                                                    }
                                                    if (!empty($datetime[0]->item(0)->nodeValue)) {
                                                        $creation_date = $datetime[0]->item(0)->nodeValue;
                                                    } else {
                                                        $creation_date = "0";
                                                    }
                                                }
                                                $shortDesc = $thisReflection->getElementsByTagName("short");
                                                $longDesc = $thisReflection->getElementsByTagName("long");
                                                //Check if category type exists, if not add
                                                $checkType = $this->objDbCategorytypeList->listByType($tyValue[0]->item(0)->nodeValue);
                                                if (!empty($checkType)) {
                                                    foreach($checkType as $mycheckType) {
                                                        $myType = $mycheckType['id'];
                                                    }
                                                } else {
                                                    $catType = $this->objDbCategoryList->listCategory("Interest");
                                                    foreach($catType as $myCatType) {
                                                        $catId = $myCatType['id'];
                                                    }
                                                    $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                }
                                                //Insert the data now
                                                $id = $this->objDbReflectionList->insertSingle($authorLang->item(0)->nodeValue, $rationale->item(0)->nodeValue, $creation_date, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordReflection", 'eportfolio') . "</li>";
                                        }
                                        //Assertion
                                        $getAssertion = $thisRoot->getElementsByTagName("assertion");
                                        if ($getAssertion->length !== 0) {
                                            foreach($getAssertion as $thisAssertion) {
                                                $alltypename = $thisAssertion->getElementsByTagName("typename");
                                                if ($alltypename->length !== 0) {
                                                    $count = 0;
                                                    $tyValue = array();
                                                    foreach($alltypename as $typename) {
                                                        $tyValue[$count] = $typename->getElementsByTagName("tyvalue");
                                                        $count = $count+1;
                                                    }
                                                }
                                                $allauthors = $thisAssertion->getElementsByTagName("authorship");
                                                if ($allauthors->length !== 0) {
                                                    foreach($allauthors as $authorship) {
                                                        $author = $authorship->getElementsByTagName("text");
                                                        $authorLang = $authorship->getElementsByTagName("language");
                                                    }
                                                }
                                                $allrationale = $thisAssertion->getElementsByTagName("rationale");
                                                if ($allrationale->length !== 0) {
                                                    foreach($allrationale as $myrationale) {
                                                        $rationale = $myrationale->getElementsByTagName("text");
                                                        $rationaleLang = $myrationale->getElementsByTagName("language");
                                                    }
                                                }
                                                $allDate = $thisAssertion->getElementsByTagName("date");
                                                if ($allDate->length !== 0) {
                                                    $count = 0;
                                                    foreach($allDate as $date) {
                                                        $allTypeNames = $date->getElementsByTagName("typename");
                                                        if ($allTypeNames->length !== 0) {
                                                            foreach($allTypeNames as $myTypeName) {
                                                                $mytyValue[$count] = $myTypeName->getElementsByTagName("tyvalue");
                                                            }
                                                        }
                                                        $datetime[$count] = $date->getElementsByTagName("datetime");
                                                        $count = $count+1;
                                                    }
                                                    if (!empty($datetime[0]->item(0)->nodeValue)) {
                                                        $creation_date = $datetime[0]->item(0)->nodeValue;
                                                    } else {
                                                        $creation_date = "0";
                                                    }
                                                }
                                                $shortDesc = $thisAssertion->getElementsByTagName("short");
                                                $longDesc = $thisAssertion->getElementsByTagName("long");
                                                //Check if category type exists, if not add
                                                $checkType = $this->objDbCategorytypeList->listByType($tyValue[0]->item(0)->nodeValue);
                                                if (!empty($checkType)) {
                                                    foreach($checkType as $mycheckType) {
                                                        $myType = $mycheckType['id'];
                                                    }
                                                } else {
                                                    $catType = $this->objDbCategoryList->listCategory("Interest");
                                                    foreach($catType as $myCatType) {
                                                        $catId = $myCatType['id'];
                                                    }
                                                    $myType = $this->objDbCategorytypeList->insertSingle($catId, $tyValue[0]->item(0)->nodeValue);
                                                }
                                                //Insert the data now
                                                $id = $this->objDbAssertionList->insertSingle($authorLang->item(0)->nodeValue, $rationale->item(0)->nodeValue, $creation_date, $shortDesc->item(0)->nodeValue, $longDesc->item(0)->nodeValue);
                                            }
                                            $totalInserts = $totalInserts . "<li>" . $this->objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio') . "</li>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $totalInserts = $totalInserts . "</ul>";
        return $totalInserts;
    }
} // end class

?>
