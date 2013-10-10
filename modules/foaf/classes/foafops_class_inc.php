<?php
/* -------------------- foafops class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
class foafops extends object
{
    /**
     * Config object - altconfig
     *
     * @var object
     */
    public $objConfig;
    /**
     * Language object
     *
     * @var object
     */
    public $objLanguage;
    /**
     * FOAF creation object
     *
     * @var object
     */
    public $objFoaf;
    /**
     * FOAF Parser class
     *
     * @var Object
     */
    public $objFoafParser;
    /**
     * User object
     *
     * @var object
     */
    public $objUser;
    /**
     * FOAF Model for the users table
     *
     * @var object
     */
    public $dbFUsers;
    /**
     * Foaf factory class
     *
     * @var object
     */
    public $objFoafOps;
    /**
     * Path to save RDF file to
     *
     * @var string
     */
    public $savepath;
    public $friend;
    public function init() 
    {
        try {
            //get the config object
            $this->objConfig = $this->getObject('altconfig', 'config');
            //instantiate the language system
            $this->objLanguage = $this->getObject('language', 'language');
            //the object needed to create FOAF files (RDF)
            $this->objFoaf = $this->getObject('foafcreator');
            //Object to parse and display FOAF RDF
            $this->objFoafParser = $this->getObject('foafparser');
            //LOAD UP THE USER OBJECT
            $this->objUser = $this->getObject('user', 'security');
            //hook up the database models
            $this->dbFoaf = $this->getObject('dbfoaf');
            $this->loadClass('dropdown', 'htmlelements');
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
        }
        //oops, one of the above is not being instantiated correctly
        catch(customException $e) {
            //handle the error gracefully
            echo customException::cleanUp();
            //kill the module now, its pointless going on...
            die();
        }
    }
    //factory method
    //the object
    //new person/whatever
    //adds all the details as private methods
    //return foaf as an object (StdClass())
    
    /**
     * Method to create a basic FOAF RDF file based on the info in tbl_users
     *
     * @param integer
     * @return stdClass object
     */
    public function newPerson($userId) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        //set the path where we will save the users foaf rdf file for publishing
        $this->savepath = $this->objConfig->getContentBasePath() ."users/".$this->objUser->userId() ."/";
        //get the users userId
        $userid = $this->objUser->userId();
        //get the users full name
        $fullname = $this->objUser->fullname();
        //retrieve what ever other info about the user we can get from tbl_users
        $uarr = $this->dbFoaf->getRecordSet($userid, 'tbl_users');
        //set the user details to an array that we can use
        $userdetails = $uarr[0];
        //get some of the foaf info
        //title
        $title = $userdetails['title'];
        //users first name
        $firstname = $userdetails['firstname'];
        //users surname
        $surname = $userdetails['surname'];
        //users email address
        $email = $userdetails['emailaddress'];
        //we need a usrimage as well for the foaf depiction
        $image = $this->objUser->getUserImageNoTags();
        $this->objFoaf->newAgent('person');
        $this->objFoaf->setName($fullname);
        $this->objFoaf->setTitle($title);
        $this->objFoaf->setFirstName($firstname);
        $this->objFoaf->setSurname($surname);
        $this->objFoaf->addMbox('mailto:'.$email, TRUE);
        $this->objFoaf->addImg($image);
    }
    /**
     * Method to add the additional details to the FOAF of a particular user
     *
     * @param int $userId
     * @return void
     */
    public function myFoaf($userId) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        //switch tables to tbl_foaf_myfoaf
        $farr = $this->dbFoaf->getRecordSet($userId, 'tbl_foaf_myfoaf');
        //get the info from dbFmyfoaf and set up all the fields
        //set the user details to an array that we can use
        if (empty($farr)) {
            $foafdetails = array();
        } else {
            $foafdetails = $farr[0];
            //print_r($foafdetails);
            //hook up the details to variables and put them into the XML Tree
            $homepage = $foafdetails['homepage'];
            $weblog = $foafdetails['weblog'];
            //page comes form a diff method
            $phone = $foafdetails['phone'];
            $jabberid = $foafdetails['jabberid'];
            $theme = $foafdetails['theme'];
            $onlineacc = $foafdetails['onlineacc'];
            $onlinegamoingacc = $foafdetails['onlinegamingacc'];
            $workhomepage = $foafdetails['workhomepage'];
            $schoolhomepage = $foafdetails['schoolhomepage'];
            $logo = $foafdetails['logo'];
            $basednear = $foafdetails['basednear'];
            if (isset($basednear)) {
                $basednear = explode(",", $basednear);
                $basednearlat = $basednear[0];
                $basednearlong = $basednear[1];
            } else {
                $basednear = NULL;
            }
            $geekcode = $foafdetails['geekcode'];
            //add the details to the foaf xml tree
            $this->objFoaf->addHomepage($homepage);
            $this->objFoaf->addWeblog($weblog);
            if (isset($phone)) {
                $this->objFoaf->addPhone($phone);
            }
            $this->objFoaf->addJabberID($jabberid);
            $this->objFoaf->setGeekcode($geekcode);
            $this->objFoaf->addTheme($theme);
            /**
             * @todo check out the accounts bit, they need a service homepage as well as a username
             */
       
            $this->objFoaf->addWorkplaceHomepage($workhomepage);
            $this->objFoaf->addSchoolHomepage($schoolhomepage);
            $this->objFoaf->addLogo($logo);
            if (isset($basednearlat) && isset($basednearlong)) {
                $this->objFoaf->setBasedNear($basednearlat, $basednearlong);
            }

    }
            //funded by from funded by table
            $this->_getFunders($userId);
            //depictions from depictions table
            $this->_getDepictions($userId);
            //organizations from organisations table
            $this->_getOrganizations($userId);
            //Get all the pages that we are interested in...
            //A page is a document about the thing
            $this->_getpages($userId);
            //accounts from foaf useraccounts table
            $this->_getAccounts($userId);
            //interests from interests table
            $this->_getInterests($userId);
            //get the people we know...
            $this->_getFriends($userId);
            //var_dump($this->objFoaf->foaftree);
            
        
    }
    /**
     * Method to get the FOAF of a friend that you have added to your FOAF
     *
     * @param integer $fuserid
     * @return array
     */
    private function _getFriendFoaf($fuserid) 
    {
        //switch tables to tbl_foaf_myfoaf
        $farr = $this->dbFoaf->getRecordSet($fuserid, 'tbl_foaf_myfoaf');
        //get the info from dbFmyfoaf and set up all the fields
        //set the user details to an array that we can use
        if (empty($farr)) {
            $foafdetails = array();
        } else {
            $foafdetails = $farr[0];
        }
        return $foafdetails;
    }
    /**
     * Method to get your FOAF interests
     *
     * @param integer $userId
     * @param bool $friend
     */
    private function _getInterests($userId, $friend = FALSE) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $iarr = $this->dbFoaf->getRecordSet($userId, 'tbl_foaf_interests', " ORDER BY interesturl ");
        if (empty($iarr)) {
            $interests = array();
        } else {
            foreach($iarr as $interests) {
                if ($friend == FALSE) {
                    $this->objFoaf->addInterest($interests['interesturl']);
                } else {
                    $this->friend->addInterest($interests['interesturl']);
                }
            }
        }
    }
    /**
     * Method to get the funders associated with your FOAF profile
     *
     * @param integer $userId
     * @param bool $friend
     */
    private function _getFunders($userId, $friend = FALSE) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $funarr = $this->dbFoaf->getRecordSet($userId, 'tbl_foaf_fundedby' , " ORDER BY funderurl ");
        if (empty($funarr)) {
            $funds = array();
        } else {
            foreach($funarr as $funds) {
                if ($friend == FALSE) {
                    $this->objFoaf->addFundedBy($funds['funderurl']);
                } else {
                    $this->friend->addFundedBy($funds['funderurl']);
                }
            }
        }
    }
    /**
     * Method to get depiction URL's of yourself or a friend
     *
     * @param integer $userId
     * @param bool $friend
     */
    private function _getDepictions($userId, $friend = FALSE) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $darr = $this->dbFoaf->getRecordSet($userId, 'tbl_foaf_depiction' ," ORDER BY depictionurl ");
        if (empty($darr)) {
            $deps = array();
        } else {
            foreach($darr as $deps) {
                if ($friend == FALSE) {
                    $this->objFoaf->addDepiction($deps['depictionurl']);
                } else {
                    $this->friend->addDepiction($deps['depictionurl']);
                }
            }
        }
    }
    /**
     * Method to get the organizations associated with your profile
     *
     * @param integer $userId
     * @param bool $friend
     */
    private function _getOrganizations($userId, $friend = FALSE) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $oarr = $this->dbFoaf->getRecordSet($userId, 'tbl_foaf_organization', " ORDER BY name ");
        if (empty($oarr)) {
            $orgs = array();
        } else {
            foreach($oarr as $orgs) {
                $homepage = $orgs['homepage'];
                $name = $orgs['name'];
                $org = $this->newObject('foafcreator');
                $org->newAgent('Organization');
                $org->setName($name);
                $org->addHomepage($homepage);
                if ($friend == FALSE) {
                    $this->objFoaf->addKnows($org);
                } else {
                    $this->friend->addKnows($org);
                }
            }
        }
    }
    /**
     * Method to get the FOAF:Pages associated with your or a friends profile
     *
     * @param integer $userId
     * @param bool $friend
     */
    private function _getpages($userId, $friend = FALSE) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $parr = $this->dbFoaf->getRecordSet($userId, 'tbl_foaf_pages' ," ORDER BY title ");
        if (empty($parr)) {
            $pages = array();
        } else {
            foreach($parr as $pages) {
                $docuri = $pages['page'];
                $title = $pages['title'];
                $description = $pages['description'];
                if ($friend == FALSE) {
                    $this->objFoaf->addPage($docuri, $title, $description);
                } else {
                    $this->friend->addPage($docuri, $title, $description);
                }
            }
        }
    }



    /**
     * Method to get the FOAF:holdsAccount associated with your or a friends profile
     *
     * @param integer $userId
     * @param bool $friend
     */
    private function _getAccounts($userId, $friend = FALSE) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $accounts = $this->dbFoaf->getRecordSet($userId, 'tbl_foaf_useraccounts'," ORDER BY accountName ");
    
    if(!isset($accounts))
    {
       $accounts = array();
    } else {
       if(!empty($accounts))
       {
             foreach($accounts as $account)
         {        
        switch($account['type']) {
           case 'onlineChatAccount':
           $this->objFoaf->addOnlineChatAccount($account['accountname'], $account['accountservicehomepage']);
           break;
         
           case 'onlineEcommerceAccount':
           $this->objFoaf->addOnlineEcommerceAccount($account['accountname'], $account['accountservicehomepage']);
           break;
         
           case 'onlineGamingAccount':
           $this->objFoaf->addOnlineChatAccount($account['accountname'], $account['accountservicehomepage']);
           break;

           default:
           case 'onlineAccount':
           $this->objFoaf->addOnlineChatAccount($account['accountname'], $account['accountservicehomepage'] , $account['url']);
           break;
             }
            }
         }
    }

        
    }



    /**
     * Method to get all your friends
     *
     * @param integer $userId
     */
    private function _getFriends($userId) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $frarr = $this->dbFoaf->getRecordSet($userId, 'tbl_foaf_friends');
        //print_r($frarr);
        if (empty($frarr)) {
            $frarr = array();
        } else {
            foreach($frarr as $friends) {
                $fuserid = $friends['fuserid'];
                $fimage = $this->objUser->getUserImageNoTags($fuserid);
                $frfoaf = $this->_getFriendFoaf($fuserid);
                //go and get all our friends details
                $uarr = $this->dbFoaf->getRecordSet($fuserid, 'tbl_users');
                $userdetails = $uarr[0];
                //get some of the foaf info
                //title
                $title = $userdetails['title'];
                //users first name
                $firstname = $userdetails['firstname'];
                //users surname
                $surname = $userdetails['surname'];
                //users email address
                $email = $userdetails['emailaddress'];
                $fullname = $firstname." ".$surname;
                //echo "<h1>$fullname</h1><br><br>";
                $friend = $this->newObject('foafcreator');
                $friend->newAgent('person');
                $friend->setName($fullname);
                $friend->setTitle($title);
                $friend->setFirstName($firstname);
                $friend->setSurname($surname);
                $friend->addMbox('mailto:'.$email, TRUE);
                if (!empty($frfoaf)) {
                    //add the details to the foaf xml tree
                    if (isset($frfoaf['homepage'])) {
                        $friend->addHomepage($frfoaf['homepage']);
                    }
                    if (isset($frfoaf['weblog'])) {
                        $friend->addWeblog($frfoaf['weblog']);
                    }
                    if (isset($frfoaf['phone'])) {
                        $friend->addPhone($frfoaf['phone']);
                    }
                    if (isset($frfoaf['jabberid'])) {
                        $friend->addJabberID($frfoaf['jabberid']);
                    }
                    if (isset($frfoaf['geekcode'])) {
                        $friend->setGeekcode($frfoaf['geekcode']);
                    }
                    if (isset($frfoaf['theme'])) {
                        $friend->addTheme($frfoaf['theme']);
                    }
                    /**
                     * @todo check out the accounts bit, they need a service homepage as well as a username
                     */
                    if (isset($frfoaf['workhomepage'])) {
                        $friend->addWorkplaceHomepage($frfoaf['workhomepage']);
                    }
                    if (isset($frfoaf['schoolhomepage'])) {
                        $friend->addSchoolHomepage($frfoaf['schoolhomepage']);
                    }
                    if (isset($frfoaf['logo'])) {
                        $friend->addLogo($frfoaf['logo']);
                    }
                    if (isset($frfoaf['basednearlat']) && isset($frfoaf['basednearlong'])) {
                        $friend->setBasedNear($frfoaf['basednearlat'], $frfoaf['basednearlong']);
                    }
                }
                $friend->addImg($fimage);
                $this->objFoaf->addKnows($friend);
            }
        }
    }
    /**
     * Method to write the foaf profile that is generated to a file
     *
     * @param void
     * @return void
     */
    public function writeFoaf() 
    {
        //write the file so that we can edit it later
        //var_dump($this->objFoaf->foaftree->get());
        //var_dump($this->objFoaf->foaftree);
        if (!is_dir($this->savepath)) {
            mkdir($this->savepath, 0777);
        }
        @chmod($this->savepath, 0777);
        $this->objFoaf->toFile($this->savepath, $this->objUser->userId() .'.rdf', $this->objFoaf->get());
        //die();
        
    }
    /**
     * Method to parse a foaf profile and return it as HTML
     *
     * This should only really be used in debugging
     *
     * @param integer $userId
     * @return string
     */
    public function foaf2html($userId) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $this->objFoafParser->setup();
        $fp = $this->objFoafParser->parseFromUri($this->savepath.$userId.'.rdf');
        return $this->objFoafParser->toHtml($this->objFoafParser->foaf_data);
    }
    /**
     * Method to parse the FOAF into an array that can be manipulated
     *
     * @param integer $userId
     * @return array
     */
    public function foaf2array($userId) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $this->objFoafParser->setup();
        $fp = $this->objFoafParser->parseFromUri($this->savepath.$userId.'.rdf');
        return $this->objFoafParser->toArray();
    }
    /**
     * Method to create a stdClass Object from a FOAF profile
     *
     * @param integer $userId
     * @return object
     */
    public function foaf2Object($userId) 
    {
        if (!isset($userId)) {
            $userId = $this->objUser->userId();
        }
        $this->objFoafParser->setup();
        $fp = $this->objFoafParser->parseFromUri($this->savepath.$userId.'.rdf');
        return $this->objFoafParser->toObject();
    }
    /**
     * Method to create a the add a friend dropdown form
     *
     * @param void
     * @return string
     */
    public function addDD() 
    {
        $myFriendsAddForm = new form('myfriends', $this->uri(array(
            'action' => 'updatefriends'
        )));
        $fieldset3 = $this->newObject('fieldset', 'htmlelements');
        //$fieldset3->setLegend($this->objLanguage->languageText('mod_foaf_addfriends', 'foaf'));
        $table3 = $this->newObject('htmltable', 'htmlelements');
        $table3->cellpadding = 5;
        //start the friends dropdowns
        $addarr = $this->dbFoaf->getAllUsers();
        foreach($addarr as $users) {
            $name = $users['firstname']." ".$users['surname'];
            $id = $users['userid'];
            $addusers[] = array(
                'name' => $name,
                'id' => $id
            );
        }
        //add in a dropdown to add/remove users as friends
        $addDrop = new dropdown('add');
        foreach($addusers as $newbies) {
            if ($this->objUser->userId() != $newbies['id']) {
                $addDrop->addOption($newbies['id'], $newbies['name']);
            }
        }
        //add
        $table3->startRow();
        //$table3->addCell($this->objLanguage->languageText('mod_foaf_addfriends', 'foaf'));
        $table3->addCell($addDrop->show() , 150, NULL, 'left');
        $table3->endRow();
        $fieldset3->addContent($table3->show());
        $myFriendsAddForm->addToForm($fieldset3->show());
        $this->objButton3 = new button('update_addfriends'); //$this->objLanguage->languageText('mod_foaf_update_friends', 'foaf'));
        $this->objButton3->setValue($this->objLanguage->languageText('mod_foaf_butaddfriends', 'foaf'));
        $this->objButton3->setToSubmit();
        $myFriendsAddForm->addToForm($this->objButton3->show());
        return $myFriendsAddForm;
    }
    /**
     * Method to create the remove a friend dropdown
     *
     * @param void
     * @return string
     */
    public function remDD() 
    {
        $myFriendsRemForm = new form('myfriendsrem', $this->uri(array(
            'action' => 'updatefriends'
        )));
        $fieldset4 = $this->newObject('fieldset', 'htmlelements');
        //$fieldset4->setLegend($this->objLanguage->languageText('mod_foaf_remfriends', 'foaf'));
        $table4 = $this->newObject('htmltable', 'htmlelements');
        $table4->cellpadding = 5;
        //remove dropdown
        $remarr = $this->dbFoaf->getFriends();
        //print_r($remarr);
        if (isset($remarr)) {
            //add in a dropdown to add/remove users as friends
            $remDrop = new dropdown('remove');
            foreach($remarr as $removals) {
                $remDrop->addOption($removals['id'], $removals['name']);
            }
        }
        if (isset($remarr)) {
            //delete
            $table4->startRow();
            //$table4->addCell($this->objLanguage->languageText('mod_foaf_remfriends', 'foaf'));
            $table4->addCell($remDrop->show());
            $table4->endRow();
            $fieldset4->addContent($table4->show());
            $myFriendsRemForm->addToForm($fieldset4->show());
            $this->objButton4 = new button('update_remfriends'); //$this->objLanguage->languageText('mod_foaf_update_friends', 'foaf'));
            $this->objButton4->setValue($this->objLanguage->languageText('mod_foaf_butremfriends', 'foaf'));
            $this->objButton4->setToSubmit();
            $myFriendsRemForm->addToForm($this->objButton4->show());
        }
        return $myFriendsRemForm;
    }
    /**
     * Method to create the featurebox to hold the organizations
     *
     * @param object $pals
     * @return string
     */
    function orgFbox($pals) 
    {
        $pftype = $pals['type'];
        $pfbox = "<em>".$pals['name']."</em><br />";
        if (isset($pals['homepage'])) {
            $page = new href(htmlentities($pals['homepage'][0]) , htmlentities($pals['homepage'][0]) , "title='".$this->objLanguage->languageText('mod_foaf_goto' , 'foaf')."  ".$pals['homepage'][0]."'");
            $link = $page->show();
            return array(
                $pfbox,
                $pftype,
                $link
            );
        }
    }
   


   
 





 /**
     * Method to create the friends featurebox
     *
     * @param object $pals
     * @return string
     */
    function fFeatureBoxen($pals) 
    {
        $pftype = $pals['type'];
    $name  = NULL;
      if($pftype == 'Person')
      {            
    if (isset($pals['title']) && isset($pals['firstname']) && isset($pals['surname'])) {
            $pfbox = "<em>".$pals['title']." ".$pals['firstname']." ".$pals['surname']."</em><br />";
         $name = $pals['title'].' '.$pals['firstname'].' '.$pals['surname'];
        } else {
            $pfbox = "<em>".$pals['name']."</em><br />";
        $name = $this->objLanguage->languageText('mod_foaf_friend' , 'foaf');
            $pfimg = NULL;
        }


        //build a table of values etc...
        //var_dump($pals);
        if (isset($pals['img'])) {
            if (is_array($pals['img'])) {
                $pimg = $pals['img'][0];
                $pimgv = new href($pimg, $pimg);
                $pfimg = '<img src="'.htmlentities($pimg) .'" alt="user image" />'."<br />";
            }
        }
        if (isset($pals['homepage'])) {
            if (is_array($pals['homepage'])) {
                $phomepage = $pals['homepage'][0];
                $plink = new href(htmlentities($phomepage) , htmlentities($phomepage));
                $pfbox.= $this->objLanguage->languageText('mod_foaf_homepage', 'foaf') .": ".$plink->show() ."<br />";
            }
        }
        if (isset($pals['jabberid'])) {
            if (is_array($pals['jabberid'])) {
                $pjabberid = $pals['jabberid'][0];
                $pfbox.= $this->objLanguage->languageText('mod_foaf_jabberid', 'foaf') .": ".$pjabberid."<br />";
            }
        }
        if (isset($pals['logo'])) {
            if (is_array($pals['logo'])) {
                $plogo = $pals['logo'][0];
                $plink2 = new href(htmlentities($plogo) , htmlentities($plogo));
                $pfbox.= $this->objLanguage->languageText('mod_foaf_logo', 'foaf') .": ".$plink2->show() ."<br />";
            }
        }
        if (isset($pals['phone'])) {
            if (is_array($pals['phone'])) {
                $pphone = $pals['phone'][0];
                $pfbox.= $this->objLanguage->languageText('mod_foaf_phone', 'foaf') .": ".$pphone."<br />";
            }
        }
        if (isset($pals['schoolhomepage'])) {
            if (is_array($pals['schoolhomepage'])) {
                $pschoolhomepage = $pals['schoolhomepage'][0];
                $plink3 = new href(htmlentities($pschoolhomepage) , htmlentities($pschoolhomepage));
                $pfbox.= $this->objLanguage->languageText('mod_foaf_schoolhomepage', 'foaf') .": ".$plink3->show() ."<br />";
            }
        }
        if (isset($pals['theme'])) {
            if (is_array($pals['theme'])) {
                $ptheme = $pals['theme'][0];
                $plink4 = new href(htmlentities($ptheme) , htmlentities($ptheme));
                $pfbox.= $this->objLanguage->languageText('mod_foaf_theme', 'foaf') .": ".$plink4->show() ."<br />";
            }
        }
        if (isset($pals['weblog'])) {
            if (is_array($pals['weblog'])) {
                $pweblog = $pals['weblog'][0];
                $plink5 = new href(htmlentities($pweblog) , htmlentities($pweblog));
                $pfbox.= $this->objLanguage->languageText('mod_foaf_weblog', 'foaf') .": ".$plink5->show() ."<br />";
            }
        }
        if (isset($pals['workplacehomepage'])) {
            if (is_array($pals['workplacehomepage'])) {
                $pworkplacehomepage = $pals['workplacehomepage'][0];
                $plink6 = new href(htmlentities($pworkplacehomepage) , htmlentities($pworkplacehomepage));
                $pfbox.= $this->objLanguage->languageText('mod_foaf_workhomepage', 'foaf') .": ".$plink6->show() ."<br />";
            }
        }
        if (isset($pals['geekcode'])) {
            $pgeekcode = htmlentities($pals['geekcode'][0]);
            $pfbox.= $this->objLanguage->languageText('mod_foaf_geekcode', 'foaf') .": ".$pgeekcode."<br />";
        }
        return array(
            $pfimg,
            $pfbox,
            $pftype,
        $name
        );
     }
    }




 /**
     * Method to add a form to add an organization
     *
     * @param void
     * @return string
     */
    public function orgaAddForm() 
    {
        $myOrgForm = new form('myorgform', $this->uri(array(
            'action' => 'updateorgs'
        )));
        $fieldseto = $this->newObject('fieldset', 'htmlelements');
        $fieldseto->setLegend($this->objLanguage->languageText('mod_foaf_addorg', 'foaf'));
        $tableo = $this->newObject('htmltable', 'htmlelements');
        $tableo->cellpadding = 5;
        $tableo->startRow();
        $labelo2 = new label($this->objLanguage->languageText('mod_foaf_oname', 'foaf') .':', 'input_oname');
        $oname = new textinput('oname');
        $tableo->addCell($labelo2->show() , 150, NULL, 'right'); //label
        $tableo->addCell($oname->show()); //input box
        $tableo->endRow();
        $tableo->startRow();
        $labelo1 = new label($this->objLanguage->languageText('mod_foaf_ohomepage', 'foaf') .':', 'input_ohomepage');
        $ohomepage = new textinput('ohomepage');
        $tableo->addCell($labelo1->show() , 150, NULL, 'right'); //label
        $tableo->addCell($ohomepage->show()); //input box
        $tableo->endRow();
        $fieldseto->addContent($tableo->show());
        $myOrgForm->addToForm($fieldseto->show());
        $this->objButtono = new button('addorg');
        $this->objButtono->setValue($this->objLanguage->languageText('mod_foaf_addorg', 'foaf'));
        $this->objButtono->setToSubmit();
        $myOrgForm->addToForm($this->objButtono->show());


    //Form validation
    $myOrgForm->addRule($oname->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_oname', 'foaf') , 'required');

    
    $myOrgForm->addRule($ohomepage->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_ohomepage', 'foaf') , 'required');


        return $myOrgForm->show();
    }
    /**
     * Method to create the remove org form
     *
     * @param void
     * @return string
     */
    public function orgaRemForm() 
    {
        $myOrgRemForm = new form('myorgsrem', $this->uri(array(
            'action' => 'updateorgs'
        )));
        $fieldsetor = $this->newObject('fieldset', 'htmlelements');
        $fieldsetor->setLegend($this->objLanguage->languageText('mod_foaf_remorgs', 'foaf'));
        $tableor = $this->newObject('htmltable', 'htmlelements');
        $tableor->cellpadding = 5;
        //remove dropdown
        $remarray = $this->dbFoaf->remOrg();
        if (isset($remarray)) {
            //add in a dropdown to add/remove users as friends
            $remDrop = new dropdown('removeorg');
            foreach($remarray as $removal) {

                $remDrop->addOption($removal['id'],$this->cutChars($removal['name'] , 20));
            }
            //delete
            $tableor->startRow();
            //$table4->addCell($this->objLanguage->languageText('mod_foaf_remfriends', 'foaf'));
            $tableor->addCell($remDrop->show());
            $tableor->endRow();
            $fieldsetor->addContent($tableor->show());
            $myOrgRemForm->addToForm($fieldsetor->show());
            $this->objButtonor = new button('update_orgsrem'); //$this->objLanguage->languageText('mod_foaf_update_friends', 'foaf'));
            $this->objButtonor->setValue($this->objLanguage->languageText('mod_foaf_butremorgs', 'foaf'));
            $this->objButtonor->setToSubmit();
            $myOrgRemForm->addToForm($this->objButtonor->show());
            return $myOrgRemForm->show();
        }
    }




 /**
     * Method to create a form for adding funders
     *
     * @param void
     * @return string
     */
    public function addFunderForm() 
    {
        $funderForm = new form('funderform', $this->uri(array(
            'action' => 'updatefunders'
        )));

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_addfunder', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        //$table->startRow();
       // $labelname = new label($this->objLanguage->languageText('mod_foaf_fundername', 'foaf') .':', 'input_fname');
       // $fname = new textinput('fname');
        //$table->addCell($labelname->show() , 150, NULL, 'right'); //label
        //$table->addCell($fname->show()); 
        //$table->endRow();
        $table->startRow();
        $labelpage = new label($this->objLanguage->languageText('mod_foaf_funderpage', 'foaf') .':', 'input_fpage');
        $funderPage = new textinput('fpage');
        $table->addCell($labelpage->show() , 150, NULL, 'right'); //label
        $table->addCell($funderPage->show()); //input box
        $table->endRow();
        $fieldset->addContent($table->show());
        $funderForm->addToForm($fieldset->show());
        $this->objButton = new button('addfunder');
        $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_addfunder', 'foaf'));
        $this->objButton->setToSubmit();
        $funderForm->addToForm($this->objButton->show());
    
    //Form validation
    $funderForm->addRule($funderPage->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_funderpage', 'foaf') , 'required');
    
        return $funderForm->show();
    }
    /**
     * Method to create a form for removing funders
     *
     * @param void
     * @return string
     */
    public function remFunderForm() 
    {
        $removeFunderForm = new form('removefunder', $this->uri(array(
            'action' => 'updatefunders'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_remfunder', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        //remove dropdown
        $remarray = $this->dbFoaf->getFunders();
       
        /**
          *If there are no funders for this user
          *do not show remove funders box
        **/    

            if (isset($remarray)) {
                    if(!empty($remarray))
                {

                    $remDrop = new dropdown('removefuns');
                        foreach($remarray as $removal) {
                            $remDrop->addOption($removal['id'], $this->cutChars($removal['funderurl']));
                        }
                    
                $table->startRow();
                       $table->addCell($remDrop->show());
                    $table->endRow();
                    $fieldset->addContent($table->show());
                    $removeFunderForm ->addToForm($fieldset->show());
                      $this->objButton = new button('update_funsrem'); 
                      $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_remfunder', 'foaf'));
                      $this->objButton->setToSubmit();
                      $removeFunderForm ->addToForm($this->objButton->show());
                      return $removeFunderForm ->show();
                    } 
            }
    }


/**
     * Method to create a form for adding interests
     *
     * @param void
     * @return string
     */
    public function addInterestForm() 
    {
        $intForm = new form('interestform', $this->uri(array(
            'action' => 'updateinterests'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_addinterest', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        //$table->startRow();
       // $labelname = new label($this->objLanguage->languageText('mod_foaf_interestname', 'foaf') .':', 'input_iname');
       // $iname = new textinput('iname');
        //$table->addCell($labelname->show() , 150, NULL, 'right'); //label
        //$table->addCell($iname->show()); 
        //$table->endRow();
        $table->startRow();
        $labelpage = new label($this->objLanguage->languageText('mod_foaf_interestpage', 'foaf') .':', 'input_ipage');
        $intPage = new textinput('ipage');
        $table->addCell($labelpage->show() , 150, NULL, 'right'); //label
        $table->addCell($intPage->show()); 
        $table->endRow();
        $fieldset->addContent($table->show());
        $intForm->addToForm($fieldset->show());
        $this->objButton = new button('addinterest');
        $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_addinterest', 'foaf'));
        $this->objButton->setToSubmit();
        $intForm->addToForm($this->objButton->show());

    //Form validation
    $intForm->addRule($intPage->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_interestpage', 'foaf') , 'required');

        return $intForm->show();
    }
    /**
     * Method to create a form for removing interests
     *
     * @param void
     * @return string
     */
    public function remInterestForm() 
    {
        $removeInterestForm = new form('removeinterest', $this->uri(array(
            'action' => 'updateinterests'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_reminterest', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        //remove dropdown
        $remarray = $this->dbFoaf->getInterests();
       
        /**
          *If there are no interests for this user
          *do not show remove interests dropdown
        **/    

            if (isset($remarray)) {
                    if(!empty($remarray))
                {

                    $remDrop = new dropdown('removeint');
                        foreach($remarray as $removal) {
                            $remDrop->addOption($removal['id'], $this->cutChars($removal['interesturl']));
                        }
                    
              $table->startRow();
                     $table->addCell($remDrop->show());
                      $table->endRow();
                      $fieldset->addContent($table->show());
                      $removeInterestForm ->addToForm($fieldset->show());
                      $this->objButton = new button('update_intrem'); 
                      $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_reminterest', 'foaf'));
                      $this->objButton->setToSubmit();
                      $removeInterestForm ->addToForm($this->objButton->show());
                      return $removeInterestForm ->show();
                    } 
            }
    }





/**
     * Method to create a form for adding depictions
     *
     * @param void
     * @return string
     */
    public function addDepictionForm() 
    {
        $depForm = new form('depictionform', $this->uri(array(
            'action' => 'updatedeps'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_adddepiction', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        $table->startRow();
        $labelpage = new label($this->objLanguage->languageText('mod_foaf_depictionpage', 'foaf') .':', 'input_dpage');
        $depPage = new textinput('dpage');
        $table->addCell($labelpage->show() , 150, NULL, 'right'); //label
        $table->addCell($depPage->show()); 
        $table->endRow();
        $fieldset->addContent($table->show());
        $depForm->addToForm($fieldset->show());
        $this->objButton = new button('adddep');
        $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_adddepiction', 'foaf'));
        $this->objButton->setToSubmit();
        $depForm->addToForm($this->objButton->show());

    //Form validation
    $depForm->addRule($depPage->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_depictionpage', 'foaf') , 'required');

        return $depForm->show();
    }
    /**
     * Method to create a form for removing depictions
     *
     * @param void
     * @return string
     */
    public function remDepictionForm() 
    {
        $removeDepictionForm = new form('removedepiction', $this->uri(array(
            'action' => 'updatedeps'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_remdepiction', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        //remove dropdown
        $remarray = $this->dbFoaf->getDepictions();
       
        /**
          *If there are no depictions for this user
          *do not show remove depictions dropdown
        **/    

            if (isset($remarray)) {
                    if(!empty($remarray))
                {

                    $remDrop = new dropdown('removedep');
                        foreach($remarray as $removal) {
                            $remDrop->addOption($removal['id'], $this->cutChars($removal['depictionurl']));
                        }
                    
                $table->startRow();
                       $table->addCell($remDrop->show());
                    $table->endRow();
                    $fieldset->addContent($table->show());
                    $removeDepictionForm ->addToForm($fieldset->show());
                      $this->objButton = new button('update_deprem'); 
                      $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_remdepiction', 'foaf'));
                      $this->objButton->setToSubmit();
                      $removeDepictionForm ->addToForm($this->objButton->show());
                      return $removeDepictionForm ->show();
                    } 
            }
    }




//pages


 /**
     * Method to add a form to add a page
     *
     * @param void
     * @return string
     */
    public function addPageForm() 
    {
        $myPageForm = new form('mypageform', $this->uri(array(
            'action' => 'updatepages'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_addpage', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;

        $table->startRow();
        $labeluri = new label($this->objLanguage->languageText('mod_foaf_page', 'foaf') .':', 'input_docuri');
        $docUri = new textinput('docuri');
        $table->addCell($labeluri->show() , 150, NULL, 'right'); //label
        $table->addCell($docUri->show()); //input box
        $table->endRow();

        $table->startRow();
        $labeltitle = new label($this->objLanguage->languageText('mod_foaf_title', 'foaf') .':', 'input_title');
        $title = new textinput('title');
        $table->addCell($labeltitle->show() , 150, NULL, 'right'); //label
        $table->addCell($title->show()); //input box
        $table->endRow();

    $table->startRow();
        $labeldescription = new label($this->objLanguage->languageText('mod_foaf_pdescription', 'foaf') .':', 'input_description');
        $description = new textinput('description');
        $table->addCell($labeldescription->show() , 150, NULL, 'right'); //label
        $table->addCell($description->show()); //input box
        $table->endRow();

        $fieldset->addContent($table->show());
        $myPageForm->addToForm($fieldset->show());
        $this->objButton = new button('addpage');
        $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_addpage', 'foaf'));
        $this->objButton->setToSubmit();
        $myPageForm->addToForm($this->objButton->show());

    //Form validation
    $myPageForm->addRule($docUri->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_page', 'foaf') , 'required');


    $myPageForm->addRule($title->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_title', 'foaf') , 'required');

        return $myPageForm->show();
    }
    /**
     * Method to create a form for removing pages
     *
     * @param void
     * @return string
     */
    public function remPageForm() 
    {
        $removeForm = new form('removepageform', $this->uri(array(
            'action' => 'updatepages'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_rempage', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        //remove dropdown
        $remarray = $this->dbFoaf->getPgs();
        


    if (isset($remarray)) {
        if(!empty($remarray))
         {
              //add in a dropdown to add/remove users as friends
                     $remDrop = new dropdown('removepage');
                     foreach($remarray as $removal) {
                     $remDrop->addOption($removal['id'], $this->cutChars($removal['title']));
                     }
                    //delete
                    $table->startRow();
                    $table->addCell($remDrop->show());
                    $table->endRow();
                    $fieldset->addContent($table->show());
                    $removeForm->addToForm($fieldset->show());
                    $this->objButton = new button('update_pagerem'); 
                    $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_rempage', 'foaf'));
                    $this->objButton->setToSubmit();
                    $removeForm->addToForm($this->objButton->show());
              }      


    }

         return $removeForm->show();            

         
        
    }

//accounts



 /**
     * Method to add a form for adding accounts
     *
     * @param void
     * @return string
     */
    public function addAccountForm() 
    {
        $myAccountForm = new form('myAccountform', $this->uri(array(
            'action' => 'updateaccounts'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_addaccount', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;

        $labelName = new label($this->objLanguage->languageText('mod_foaf_account', 'foaf') .':', 'input_accountname');
        $accountName = new textinput('accountname');

        $table->startRow();
        $table->addCell($labelName->show() , 150, NULL, 'right'); //label
        $table->addCell($accountName->show()); //input box
        $table->endRow();

        $table->startRow();
        $labelHome = new label($this->objLanguage->languageText('mod_foaf_homepage', 'foaf') .':', 'input_servhomepage');
        $home = new textinput('servhomepage');
        $table->addCell($labelHome->show() , 150, NULL, 'right'); //label
        $table->addCell($home->show()); //input box
        $table->endRow();


    $accountTypes = $this->dbFoaf->getAccountTypes();
    
    $table->startRow();
        $labelType = new label($this->objLanguage->languageText('mod_foaf_type', 'foaf') .':', 'input_type');
        $type = new dropdown('type');
    foreach($accountTypes as $accountType)
    {
         $type->addOption($accountType['type'], $this->cutChars($accountType['type']));
    }    
        $table->addCell($labelType->show() , 150, NULL, 'right'); //label
        $table->addCell($type->show()); //input box
        $table->endRow();


        $table->startRow();
        $labelUri = new label($this->objLanguage->languageText('mod_foaf_url', 'foaf') . $this->objLanguage->languageText('mod_foaf_onlineonly','foaf') .':', 'input_typeuri');
        $uri = new textinput('typeuri');
        $table->addCell($labelUri->show() , 150, NULL, 'right'); //label
        $table->addCell($uri->show()); //input box
        $table->endRow();


        $fieldset->addContent($table->show());
        $myAccountForm->addToForm($fieldset->show());
        $this->objButton = new button('addaccount');
        $this->objButton->setValue($this->objLanguage->languageText('word_add'));
        $this->objButton->setToSubmit();
        $myAccountForm->addToForm($this->objButton->show());

    //Form validation
    $myAccountForm->addRule($accountName->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_account', 'foaf') , 'required');

    
    $myAccountForm->addRule($home->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_homepage', 'foaf') , 'required');


        return $myAccountForm->show();
    }
    /**
     * Method to create a form for removing accounts
     *
     * @param void
     * @return string
     */
    public function remAccountForm() 
    {
        $removeForm = new form('removeaccountform', $this->uri(array(
            'action' => 'updateaccounts'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_remaccount', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        //remove dropdown
        $remarray = $this->dbFoaf->getAccounts();        


    if (isset($remarray)) {
        if(!empty($remarray))
         {
              //add in a dropdown to add/remove users as friends
                     $remDrop = new dropdown('removedaccount');
                     foreach($remarray as $key=>$removal) {
                       $remDrop->addOption($removal['id'], $this->cutChars($removal['accountname'], 'title="'.$removal['accountservicehomepage'].'"'));    
                     }
                    

                    $table->startRow();
                    $table->addCell($remDrop->show());
                    $table->endRow();
                    $fieldset->addContent($table->show());
                    $removeForm->addToForm($fieldset->show());
                    $this->objButton = new button('update_accountrem'); 
                    $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_remove', 'foaf'));
                    $this->objButton->setToSubmit();
                    $removeForm->addToForm($this->objButton->show());
              }      


    }

         return $removeForm->show();            

         
        
    }
 /**
     * Method to create a form for adding account types
     *
     * @param void
     * @return string
     */

   public function addAccountTypeForm()
   {    
    $form = new form('addtypeform', $this->uri(array(
            'action' => 'updateaccounttypes'
        )));       

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_addaccounttype', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 1;
    
    $labelType = new label($this->objLanguage->languageText('mod_foaf_type', 'foaf') .':', 'input_accounttype');
        
        $accountTypes = $this->dbFoaf->getAccountTypes();

    $type = new textinput('accounttype');


    $this->objButton = new button('addtype');

    $table->startRow();
    $table->addCell($labelType->show() , 150, NULL, 'right');
    $table->addCell($type->show());
    $table->endRow();

    
    $fieldset->addContent($table->show());

    $this->objButton->setToSubmit();
    $this->objButton->setValue($this->objLanguage->languageText('word_add'));
    
    $form->addToForm($fieldset->show());
    $form->addToForm($this->objButton->show());

    //Form validation
    $form->addRule($type->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_type', 'foaf') , 'required');


    return $form->show();
  }    


 /**
     * Method to create a form for removing account types
     *
     * @param void
     * @return string
     */

   public function remAccountTypeForm()
   {    
    $form = new form('remtypeform', $this->uri(array(
            'action' => 'updateaccounttypes'
        )));       


    $remarray = $this->dbFoaf->getAccountTypes();

    if (isset($remarray)) {
       if(!empty($remarray))
       {
            $remDrop = new dropdown('remtype');
            foreach($remarray as $key=>$removal)
        {
            
            $remDrop->addOption($removal['id'], $this->cutChars($removal['type']));
            }

             $fieldset = $this->newObject('fieldset', 'htmlelements');
             $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_remaccounttype', 'foaf'));
             $table = $this->newObject('htmltable', 'htmlelements');
             $table->cellpadding = 1;
         $labelType = new label($this->objLanguage->languageText('mod_foaf_type', 'foaf') .':', 'input_remtype');


         $this->objButton = new button('removetype');

        $table->startRow();
        $table->addCell($labelType->show() , 150, NULL, 'right');
        $table->addCell($remDrop->show());
        $table->endRow();

    
       $fieldset->addContent($table->show());

       $this->objButton->setToSubmit();
       $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_remove','foaf'));
    
       $form->addToForm($fieldset->show());
       $form->addToForm($this->objButton->show());

     return $form->show();
    }
     }
  }    

   



//links

 /**
     * Method to create a form for adding links
     *
     * @param void
     * @return string
     */
    public function addLinkForm() 
    {
        $linkForm = new form('linkform', $this->uri(array(
            'action' => 'updatelinks'
        )));

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_addlink', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;

        $labelUrl = new label($this->objLanguage->languageText('mod_foaf_url', 'foaf') .':', 'input_linkurl');
        $linkUrl = new textinput('linkurl');
    $labelTitle = new label($this->objLanguage->languageText('mod_foaf_title', 'foaf') .':', 'input_linktitle');
        $linkTitle = new textinput('linktitle');
    $labelDescription = new label($this->objLanguage->languageText('mod_foaf_description', 'foaf') .':', 'input_linkdesc');
        $linkDescription = new textinput('linkdesc');
        $table->startRow();        
    $table->addCell($labelTitle->show() , 150, NULL, 'right'); //label
        $table->addCell($linkTitle->show()); //input box
        $table->endRow();

        $table->startRow();
        $table->addCell($labelUrl->show() , 150, NULL, 'right'); //label
        $table->addCell($linkUrl->show()); //input box
        $table->endRow();

        $table->startRow();
        $table->addCell($labelDescription->show() , 150, NULL, 'right'); //label
        $table->addCell($linkDescription->show()); //input box
        $table->endRow();

        $fieldset->addContent($table->show());
        $linkForm->addToForm($fieldset->show());
        $this->objButton = new button('addlink');
        $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_addlink', 'foaf'));
        $this->objButton->setToSubmit();
        $linkForm->addToForm($this->objButton->show());
    
    //Form validation
    $linkForm->addRule($linkUrl->name,$this->objLanguage->languageText('mod_foaf_alertinvalid', 'foaf').' '.$this->objLanguage->languageText('mod_foaf_url', 'foaf') , 'required');
    
        return $linkForm->show();
    }
    /**
     * Method to create a form for removing links
     *
     * @param void
     * @return string
     */
    public function remLinkForm() 
    {
        $removelinkForm = new form('removelink', $this->uri(array(
            'action' => 'updatelinks'
        )));
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_foaf_remlink', 'foaf'));
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        //remove dropdown
        $remarray = $this->dbFoaf->getlinks();
       
        /**
          *If there are no links for this user
          *do not show remove links box
        **/    

            if (isset($remarray)) {
                    if(!empty($remarray))
                {

                    $remDrop = new dropdown('removelinks');
                        foreach($remarray as $removal) {
                            $remDrop->addOption($removal['id'], $this->cutChars($removal['url']));
                        }
                    
              $table->startRow();
                     $table->addCell($remDrop->show());
                      $table->endRow();
                      $fieldset->addContent($table->show());
                      $removelinkForm ->addToForm($fieldset->show());
                      $this->objButton = new button('update_linksrem'); 
                      $this->objButton->setValue($this->objLanguage->languageText('mod_foaf_remlink', 'foaf'));
                      $this->objButton->setToSubmit();
                      $removelinkForm ->addToForm($this->objButton->show());
                      return $removelinkForm ->show();
                    } 
            }
    }

//search

    public function searchForm()
    {
        $searchForm = new form('searchform', $this->uri(array(
            'action' => 'search'
         )));
        $fields = array("name" => "Name" ,"firstname" => "First name" , "surname" => "Surname" ,"title" => "Title","mbox" => "E-mail" , "homepage" => "Homepage" ,"weblog" => "Web blog" ,"phone" =>  "Phone","jabberid" => "Jabber Id","geekcode" => "Geek code" ,"theme" => "Theme",
            "workplacehomepage" => "Workplace Homepage" ,"schoolhomepage" => "School Homepage" ,"logo" => "Logo" ,"img" => "Image");


        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cellpadding = 5;
        $table->cellspacing = 2;
        $dropdown = new dropdown('schfield');

    
        foreach($fields as $key => $field)
        {
            $dropdown->addOption($key , $field);
    
        }

        $table->startRow();
        $label = new label($this->objLanguage->languageText('word_search').':', 'input_schfield');
        $textInput = new textinput('schvalue');   
        $table->addCell($label->show());
        $table->addCell($dropdown->show());
        $table->addCell($textInput->show());
        $table->endRow();
    

        $this->objButton = new button('searchfoaf_btn'); 
        $this->objButton->setValue($this->objLanguage->languageText('word_search'));
        $this->objButton->setToSubmit();
        $searchForm ->addToForm($table->show());
        $searchForm ->addToForm($this->objButton->show());

        $output = $searchForm->show();
        $output .= "";

        return $searchForm ->show();

    }

    /**
       *Function for cutting characters of  string 
       *@param $string
       *@param size
       *@ return string
       **/
    public function cutChars($string , $size = 20)
    {
        if(strlen($string) > $size)
        {
            return substr($string , 0 , $size).'....';
        } else {
            return $string;
        }

    }
    
    public function inviteForm()
    {
        $invite = $this->objLanguage->languageText('mod_foaf_invite', 'foaf');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $box = $this->getObject('featurebox', 'navigation');
        
        //build invite friend form
        $form = new form('inviteform', $this->uri(array(
        'action' => 'inviteform'
        )));
        $textArea = new textarea('invitationtext',$this->objLanguage->languageText('mod_foaf_dear', 'foaf'),'4','18');
        $label = new label($this->objLanguage->languageText('word_to').':', 'input_friendmail');
        $mail = new textinput('friendmail','myfriend@chisimba.com','text','25');
        $button = new button('sendmail');
        $button->setId('sendmail');
        $button->setValue($this->objLanguage->languageText('mod_foaf_send', 'foaf'));
        $button->setToSubmit();

        $form->addToForm($label->show().$mail->show());
        $form->addToForm($textArea->show());
        $form->addToForm('<center>'.$button->show().'</center>');

        $inviteBox = $box->show($invite, $form->show() , 'invitebox' ,'none',TRUE);

        return $inviteBox;
    }

    public function linksBox()
    {
        $box = $this->getObject('featurebox', 'navigation');
        
        $link1 = new href($this->uri(array('action' =>'fields', 'content' => 'gallery')) , $this->objLanguage->languageText('mod_foaf_gallery', 'foaf'), 'class="itemlink"');
        $link2 = new href($this->uri(array('action' =>'fields', 'content' => 'links')) , 'Links', 'class="itemlink"');
        $link3 = new href($this->uri(array('action' =>'fields', 'content' => 'seenet')) , $this->objLanguage->languageText('mod_foaf_seenet', 'foaf'), 'class="itemlink"');

        $table = NULL;
        $table = $this->newObject('htmltable' , 'htmlelements');
        $table->id = 'extras' ;
        $table->startRow();
        $table->addCell($link1->show());
        $table->endRow();

        $table->startRow();
        $table->addCell($link2->show());
        $table->endRow();

        $table->startRow();
        $table->addCell($link3->show());
        $table->endRow();

        $table->startRow();
        $table->addCell('<br />'.$this->inviteForm(),NULL,'top',null,null, 'colspan="2"' , '0');
        $table->endRow();
        $extras = $this->objLanguage->languageText('mod_foaf_extras', 'foaf');
        $linksBox = $box->showContent('<a href="#" class="headerlink">'.$extras.'</a>',$table->show());
        
        return $linksBox;
    }


}
?>
