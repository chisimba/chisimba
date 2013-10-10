<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}

/**
 * Class fullprofile containing all display/output functions of the fullprofile module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright Wits University 2010
 * @license http://opensource.org/licenses/lgpl-2.1.php
 * @package fullprofile
 *
 */
class fpdisplay extends object
{
   /** @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;

   /** @var object $objUser: The user class of the buddies module
    * @access public
    */
   public $objUser;

   /** @var object $objFuncs: The funcs class of the fullprofile module
    * @access public
    */
   public $objFuncs;

    /**
    *
    * This is a hardcoded array of the known social network providers
    * that will be supported by having Icons stored in this module
    *
    * @var array
    * @access public
    *
    */
    public $networks = array ('africator', 'delicious', 'digg', 'facebook',
        'flickr', 'friendfeed', 'google', 'identica', 'linkedin', 'muti',
        'opera', 'picasa', 'qik', 'slideshare', 'technorati', 'twitter',
        'youtube' );

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // system classes
        $this->objLanguage = $this->getObject('language','language');
        $this->objUser = $this->getObject('user','security');
	$this->objFuncs = $this->getObject('fpfuncs', 'fullprofile');
        $this->objDbContext = $this->getObject('dbcontext', 'context');
        $this->objDbFoaf = $this->getObject('dbfoaf', 'foaf');
        $this->objBuscard = $this->getObject('buscard', 'digitalbusinesscard');
        $this->objContextDsiplay = $this->getObject('displaycontext', 'context');
        //Load the htmlelements classess
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');

    }

    /**
     * Method to show a users complete profile
     *
     * @param string $userId The users id
     * @return string $html The html to display the users complete profile
     */
    public function showFullProfile($userId)
    {
        //Create the html holder
        $html = "";

        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '1';
        
        $currentUser = $this->objUser->userId();
        $addFriendIcon = $this->newObject('geticon', 'htmlelements');
        $addFriendIcon->setIcon('add_icon');
        $addFriendIcon->title = $this->objLanguage->languageText('mod_fullprofile_addfriend', 'fullprofile');

        $addFriendLink = '<a href="'.$this->uri(array('action'=>'addfriend', 'userid'=>$currentUser, 'fuserid'=>$userId)).'">'.$addFriendIcon->show().'</a>';
        
        if($userId != $this->objUser->userId() && !$this->objDbFoaf->isFriend($this->objUser->userId(), $userId)){
            $title->str = $this->objUser->fullname($userId).'&nbsp;'.$addFriendLink;
        } else {
            $title->str = $this->objUser->fullname($userId);
        }
        $html .= $title->show();
        $html .= '<div id="ccmsAdminContainer">';

        $html .= '<div id="ccmsTabContainer">';

        $html .= '<ul class="tabs">';

        $html .= '<li><a href="#contentDetailsPanel">Details</a></li>';
        $html .= '<li><a href="#contentActivityPanel">Activity</a></li>';
        $html .= '<li><a href="#contentAffiliationsPanel">Affiliations</a></li>';
        $html .= '<li><a href="#contentMapPanel">Map</a></li>';
        $html .= '<li><a href="#contentSocialNetworksPanel">Social Networks</a></li>';
        $html .= '<li><a href="#contentTagsPanel">Tags</a></li>';
        $html .= '<li><a href="#contentContextPanel">'.$this->objLanguage->abstractText('Context').'</a></li>';
        $html .= '<li><a href="#contentFriendsPanel">Friends</a></li>';

        $html .= '</ul>';

        $html .= '<div class="tab_container">';

        $userDetailsHtml = $this->showUserDetails($userId);
        $html .= '<div id="contentDetailsPanel" class="tab_content">'.$userDetailsHtml.'</div>';

        $activityHtml = $this->showUserActivity($userId);
        $html .= '<div id="contentActivityPanel" class="tab_content">'.$activityHtml.'</div>';

        $affiliationHtml = $this->showUserAffiliations($userId);
        $html .= '<div id="contentAffiliationsPanel" class="tab_content">'.$affiliationHtml.'</div>';


        $mapHtml = $this->showUserMap($userId);
        $html .= '<div id="contentMapPanel" class="tab_content">'.$mapHtml.'</div>';

        $socialNetworksHtml = $this->showUserSocialNetworks($userId);
        $html .= '<div id="contentSocialNetworksPanel" class="tab_content">'.$socialNetworksHtml.'</div>';

        $tagsHtml = $this->showUserTags($userId);
        $html .= '<div id="contentTagsPanel" class="tab_content">'.$tagsHtml.'</div>';

        $contextHtml = $this->showUserContexts($userId);
        $html .= '<div id="contentContextPanel" class="tab_content">'.$contextHtml.'</div>';

        $friendsHtml = $this->showUserFriends($userId);
        $html .= '<div id="contentFriendsPanel" class="tab_content">'.$friendsHtml.'</div>';

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        /*
        //Load the multi tabbed box class
        $this->loadClass('multitabbedbox', 'htmlelements');
        //Create the tabbed box
        $tabbedBox = new multitabbedbox("900", "700");
        //Get the users affiliations content
        $userDetailsHtml = $this->showUserDetails($userId);
        $tabbedBox->addTab(array('name'=>'Details', 'content'=>$userDetailsHtml, 'default'=>TRUE));

        //Get the activty content
        $activityHtml = $this->showUserActivity($userId);
        //Add the content to the activity tab
        $tabbedBox->addTab(array('name'=>'Activity', 'content'=>$activityHtml));
        //Get the users affiliations content
        $affiliationHtml = $this->showUserAffiliations($userId);
        $tabbedBox->addTab(array('name'=>'Affiliations', 'content'=>$affiliationHtml));
        //Get the users map content
        $mapHtml = $this->showUserMap($userId);
        $tabbedBox->addTab(array('name'=>'Map', 'content'=>$mapHtml));

        //Add tabbed box to the output string
        $html .= $tabbedBox->show();
        */
        
        return $html;
    }
    
    /**
     * Method to display a users activity stream
     *
     * @param string $userId The users id
     * @return string $html The html displaying the users activity
     */

    public function showUserActivity($userId)
    {
        $html = "<br />&nbsp;<br />";
        //Create the page title
        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '2';
        $title->str = $this->objLanguage->languageText('mod_fullprofile_siteactivity', 'fullprofile');

        //Place the listin a div
        $html .= '<div id="activitystream" class="activitystream">';
        $html .= $title->show();
        //Get the users activity
        $userActivity = $this->objFuncs->getActivity($userId);
        //Display the activity
        if(is_array($userActivity) && count($userActivity)>0){
            foreach($userActivity as $ua){
                $dateTime = date("F j, Y, g:i a", strtotime($ua['createdon']));
                $title = $ua['title'];
                $contextCode = $ua['contextcode'];
                if(is_null($contextCode)){
                    $html .= '<ul>'.$dateTime.'&nbsp;&nbsp;'.'-'.'&nbsp;&nbsp;'.$title.'</ul>';
                } else {
                    $html .= '<ul>'.$dateTime.'&nbsp;&nbsp;'.'-'.'&nbsp;&nbsp;'.$title.'&nbsp;&nbsp;'.'-'.'&nbsp;&nbsp;'.$this->objDbContext->getTitle($contextCode).'</ul>';
                }
            }
        } else {
            $html .= '<span class="subdued">No activities logged</span>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Method to display a users affiliations
     *
     * @param string $userId The users id
     * @return string $html The html displaying the users affiliations
     */
    public function showUserAffiliations($userId)
    {
        $html = "<br />&nbsp;<br />";

        //Create the page title
        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '2';
        $title->str = $this->objLanguage->languageText('mod_fullprofile_affiliations', 'fullprofile');


        //Place the list in a div
        $html .= '<div id="affiliations" class="affiliations">';
        $html .= $title->show();
        //Get the users triples
        $userTriples = $this->objFuncs->getTriples($userId);

        if(is_array($userTriples) && count($userTriples)>0){
            foreach($userTriples as $trip){
                //Convert triple into readable string
                $tripleString = $this->objFuncs->tripleToString($trip);
                $html .= '<ul>'.$tripleString.'</ul>';
            }
        }

        $html .= '</div>';

        return $html;
    }
    /**
     * Method to display a users foaf details
     *
     * @param string $userId The users id
     * @return string $html The html displaying the users details
     */
    public function showUserDetails($userId)
    {
        $html = "<br />&nbsp;<br />";
        //Create detail header
        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '2';
        $title->str = $this->objLanguage->languageText('mod_fullprofile_details', 'fullprofile');
        //Add title to the output string

        $html .= '<div id="userdetails" class="userdetails">';
        //Get the users details
        $userDetails = $this->objDbFoaf->getRecordSet($userId, 'tbl_users');

        //Create table to hold user details
        $table = $this->getObject('htmltable', 'htmlelements');

        $table->startRow();
        $table->addCell($title->show(), NULL, 'colspan="2"');
        $table->endRow();

        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell('&nbsp;');
        $table->endRow();

        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell($this->objUser->getUserImage($userId));
        $table->endRow();

        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell('&nbsp;');
        $table->endRow();

        $table->startRow();
        $table->addCell($this->objLanguage->languageText('word_name', 'system', 'Name').':');
        $table->addCell($userDetails[0]['title'].'&nbsp;'.$userDetails[0]['firstname'].'&nbsp;'.$userDetails[0]['surname']);
        $table->endRow();

        $table->startRow();
        $table->addCell('&nbsp;');
        $table->addCell('&nbsp;');
        $table->endRow();

        if(!is_null($userDetails[0]['emailaddress'])){
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('word_email', 'system', 'Email').':');
            $table->addCell('<a href="mailto:'.$userDetails[0]['emailaddress'].'">'.$userDetails[0]['emailaddress'].'</a>');
            $table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
        }
        if(!is_null($userDetails[0]['cellnumber'])){
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('word_number', 'system', 'Number').':');
            $table->addCell($userDetails[0]['cellnumber']);
            $table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();
        }

        //Add the users interests
        $title->str = $this->objLanguage->languageText('mod_foaf_interests', 'foaf');
        //get the users interests
        $userInterests = $this->objDbFoaf->getInterests($userId);
        if(is_array($userInterests) && count($userInterests)>0){

            $table->startRow();
            $table->addCell($title->show(), null, 'top', null, null, 'colspan="2"', '0');
            $table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();

            foreach($userInterests as $interest){
                $table->startRow();
                $table->addCell('<a href="'.$interest['interesturl'].'">'.$interest['interesturl'].'</a>', null, 'top', null, null, 'colspan="2"', '0');
                $table->endRow();

                $table->startRow();
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->endRow();

            }
        }
        //Add the users depictions
        $title->str = $this->objLanguage->languageText('mod_foaf_depictions', 'foaf');
        //get the users depictions
        $userDepictions = $this->objDbFoaf->getDepictions($userId);
        if(is_array($userDepictions) && count($userDepictions)>0){

            $table->startRow();
            $table->addCell($title->show(), null, 'top', null, null, 'colspan="2"', '0');
            $table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();

            foreach($userDepictions as $depictions){
                $table->startRow();
                $table->addCell('<a target="_blank" href="'.$depictions['depictionurl'].'"><img src="'.$depictions['depictionurl'].'" width="90" height="90" /></a>');
                $table->addCell('<a href="'.$depictions['depictionurl'].'">'.$depictions['depictionurl'].'</a>');
                $table->endRow();

                $table->startRow();
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->endRow();

            }
        }

        //Add the users pages
        $title->str = $this->objLanguage->languageText('mod_foaf_pages', 'foaf');
        //get the users pages
        $userPages = $this->objDbFoaf->getPgs($userId);
        if(is_array($userPages) && count($userPages)>0){

            $table->startRow();
            $table->addCell($title->show(), null, 'top', null, null, 'colspan="2"', '0');
            $table->endRow();

            $table->startRow();
            $table->addCell('&nbsp;');
            $table->addCell('&nbsp;');
            $table->endRow();

            foreach($userPages as $pages){
                $table->startRow();
                $table->addCell('Title:');
                $table->addCell($pages['title']);
                $table->endRow();
                $table->startRow();
                $table->addCell('Page:');
                $table->addCell('<a href="'.$pages['page'].'">'.$pages['page'].'</a>');
                $table->endRow();
                $table->startRow();
                $table->addCell('Description:');
                $table->addCell($pages['description']);
                $table->endRow();

                $table->startRow();
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->endRow();

            }
        }

        $html .= $table->show();

        $html .= '</div>';
        
        return $html;
    }

    /**
     * Method to display a map for the user
     *
     * @param string $userId The users id
     * @return string $html The html displaying the users details
     */
    public function showUserMap($userId)
    {
        $html = "<br />&nbsp;<br />";
        $html .= $this->objBuscard->getLatLong($userId);

        return $html;
    }

    /**
     * Method to display a map for the user
     *
     * @param string $userId The users id
     * @return string $html The html displaying the users details
     */
    public function showUserTags($userId)
    {
        $html = "";
        $html .= $this->objBuscard->widgetize('chisimba');

        return $html;
    }

    /**
     * Method to display the users social networks
     *
     * @param string $userId The users id
     * @return string $html The html displaying the users details
     */
    public function showUserSocialNetworks($userId)
    {
        // Get the social networks tab content.
        $html = "<br />&nbsp;<br />";

        //Create the page title
        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '2';
        $title->str = $this->objLanguage->languageText('mod_fullprofile_socialnetworks', 'fullprofile');

        $html .= $title->show();
        //Place the listin a div
        $html .= '<div id="socialnetworks" class="socialnetworks">';

        foreach ($this->networks as $network) {
            $html .= $this->objBuscard->getSocialNetwork($network, $userId);
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Method to display a users activity stream
     *
     * @param string $userId The users id
     * @return string $html The html displaying the users activity
     */

    public function showUserContexts($userId)
    {
        $html = "<br />&nbsp;<br />";
        //Create the page title
        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '2';
        $title->str = $this->objLanguage->abstractText('Context');

        $html .= $title->show();
        //Place the listin a div
        $html .= '<div id="usercontextlist" class="usercontextlist">';
        //Get the users activity
        $userContexts = $this->objFuncs->getContexts($userId);
        //Display the activity
        if(is_array($userContexts) && count($userContexts)>0){
            foreach($userContexts as $context){
                $contextDetails = $this->objDbContext->getContext($context);
                if(is_array($contextDetails) && count($contextDetails)>0){
                       //Display context
                        $html .= $this->objContextDsiplay->formatContextDisplayBlock($contextDetails, FALSE);
                }
            }
        } else {
            $html .= '<span class="subdued">User does not belong to any projects</span>';
        }
        $html .= '</div>';

        return $html;
    }
    
    /**
     * Method to show a list of a users friends
     * 
     * @param string $userId The users id
     * @param int $start
     * @param int $end
     * @access public
     */
    public function showUserFriends($userId)
    {
        $html = "<br />&nbsp;<br />";
        //Create the page title
        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '2';
        $title->str = $this->objLanguage->languageText('mod_fullprofile_friends', 'fullprofile');

        
        //Place the listin a div
        $html .= '<div id="friendslist" class="friendslist">';
        $html .= $title->show();
        //Get list of friends
        $friendsList = $this->objDbFoaf->getFriends($userId);

        //Create table to hold friends
        $friendsTbl = $this->newObject('htmltable', 'htmlelements');
        
        if(is_array($friendsList) && count($friendsList)>0){
            $count = 0;
            foreach($friendsList as $friend){

                $fid = $friend['id'];
                $fuserid = $friend['fuserid'];
                $fname = $friend['name'];
                $fpic = $this->objUser->getUserImage($fuserid);
                $deleteArr = array('action'=>'removefriend', 'id'=>$fid, 'userid'=>$userId);
                $deletePhrase = $this->objLanguage->languageText('mod_fullprofile_removefriend', 'fullprofile').':'.'&nbsp;'.$fname;
                $deleteIcon = $this->newObject('geticon', 'htmlelements');
                $deleteIcon->setIcon('delete');
                $deleteIcon->title = $this->objLanguage->languageText('mod_fullprofile_removefriend', 'fullprofile').':'.'&nbsp;'.$fname;
                $deleteCont = '<a href="'.$this->uri($deleteArr).'">'.$deleteIcon->show().'</a>';
                if($userId == $this->objUser->userId()){
                    $fdetails = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$fuserid)).'">'.$fpic.'<br />'.$fname.'</a><br />'.$deleteCont;
                } else {
                    $fdetails = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$fuserid)).'">'.$fpic.'<br />'.$fname.'</a>';
                }
                if($count == 0){
                    $friendsTbl->startRow();
                    $friendsTbl->addCell($fdetails, NULL, 'center', 'center');
                    $count++;
                } else if($count == 5){
                     $friendsTbl->addCell($fdetails, NULL, 'center', 'center');
                     $friendsTbl->endRow();
                     $count = 0;
                } else {
                     $friendsTbl->addCell($fdetails, NULL, 'center', 'center');
                     $count++;
                }


            }
        }else {
            $html .= '<span class="subdued">User has not added any friends</span>';
        }

        $html .= $friendsTbl->show();

        return $html;
    }


    /**
     * Method to display the user search form
     *
     * @access public
     * @return string $html The html for the search form
     */
    public function showSearchForm($searchTerm, $result = NULL)
    {
        $html = "<br />&nbsp;<br />";

        //Place the list in a div
        $html .= '<div id="searchfriendsform" class="searchfriendsform">';
        
        //Create the form
        $form = new form('searchuser',$this->uri(array('action'=>'search')));
        //$form->displayType = 4;

        //Create the search text input
        $searchInput = new textinput('searchterm');
        $searchInput->size = 40;
        $searchLabel = new label($this->objLanguage->languageText('mod_fullprofile_searchfor', 'fullprofile', 'Search for'), 'input_searchterm');

        $button = new button ('search', $this->objLanguage->languageText('word_search', 'system', 'Search'));
        $button->cssId = 'searchbutton';
        $button->setToSubmit();

        $searchHtml = $searchLabel->show().'&nbsp;'.$searchInput->show().'&nbsp;'.$button->show();

        $form->addToForm($searchHtml);

        $html .= $form->show();

        $html .= '<div id=searchresult>';

        if(is_array($result) && count($result)>0){
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->width = "60%";
            $title = $this->getObject('htmlheading', 'htmlelements');
            $title->type = '3';
            $title->str = $this->objLanguage->languageText('mod_fullprofile_searchresults', 'fullprofile');

            $table->startRow();
            $table->addCell($title->show(), null, 'top', null, null, 'colspan="2"', '0');
            $table->endRow();
            foreach($result as $res){
                $userId = $res['userid'];
                $userImage = $this->objUser->getUserImage($userId);
                $linkToProfilePic = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$userId)).'">'.$userImage.'</a>';
                $linkToProfileText = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$userId)).'">'.$this->objUser->fullname($userId).'</a>';
                $table->startRow();
                $table->addCell($linkToProfilePic, null, 'center', null, null, null, '0');
                $table->addCell($linkToProfileText, null, 'center', null, null, null, '0');
                //Set current user to check if user is friend
                $currentUser = $this->objUser->userId();
                $addFriendIcon = $this->newObject('geticon', 'htmlelements');
                $addFriendIcon->setIcon('add_icon');
                $addFriendIcon->title = $this->objLanguage->languageText('mod_fullprofile_addfriend', 'fullprofile');

                $addFriendLink = '<a href="'.$this->uri(array('action'=>'addfriend', 'userid'=>$currentUser, 'fuserid'=>$userId)).'">'.$addFriendIcon->show().'</a>';

                if($userId != $this->objUser->userId() && !$this->objDbFoaf->isFriend($this->objUser->userId(), $userId)){
                    $table->addCell($addFriendLink, null, 'center', null, null, null, '0');
                } else {
                    $table->addCell('&nbsp;', null, 'center', null, null, null, '0');
                }
                $table->endRow();

            }
            $html .= $table->show();
        }

        $html .= '</div>';
        return $html;

    }

    /**
    *
    * Get the latitude and longitude of the user and return it in hcard format
    * while optionally rendering a google map
    *
    * @param string $userId The userid of the user to lookup
    * @param boolean $showMap Whether or not to show the map, default TRUE
    * @return string The rendered output
    * @access public
    *
    */
    public function getLatLong($userId, $showMap=TRUE)
    {
        //Change the user id
        if(!is_null($userId) && $userId != ""){
             $this->objUserParams->setUserId($userId);
        }

        $latitude = $this->objUserParams->getValue("latitude");
        $longitude = $this->objUserParams->getValue("longitude");
        if ($latitude && $longitude) {
            $ret = '<span class="geo">'
              . '<abbr class="latitude" title="' . $latitude
              . '">' . $latitude . "</abbr>\n"
              .  '<abbr class="longitude" title="'
              . $longitude . '">' . $longitude . "</abbr>\n"
              . "</span>\n";
            $ret = $this->getLinkIcon("earth") . $ret;
            if ($showMap) {
                $ret .= $this->getMap($latitude, $longitude);
            }
            return $ret;
        }
    }

    /**
    *
    * Method to render a simple google map
    *
    * @param string $latitude Latitude of user
    * @param string $longitude Longitude of user
    * @return string The rendered map
    * @access private
    *
    */
    private function getMap($latitude, $longitude)
    {
        $ret = '<br /><div class="vcard_map">'
          . '<iframe width="512" height="512" '
          . 'frameborder="0" scrolling="no" '
          . 'marginheight="0" marginwidth="0" '
          . 'src="http://maps.google.com/maps/api/staticmap?center='
          . $latitude .',' . $longitude
          . '&zoom=17&size=512x512&maptype=hybrid'
          . '&markers=color:red|' . $latitude .','
          . $longitude . '&sensor=false&key=' . $this->mapApiKey
          . '"></iframe></div>';
        return $ret;
    }

    public function widgetize($terms) {
        $collecta = "";
        $title ="My favorite tags";
        $widget = '<iframe style="border: medium none ; overflow: hidden; width:640px; height:480px;"
                  src="http://widget.collecta.com/widget.html?query='.urlencode($terms).'&alias='.$title.'&
                  headerimg=&stylesheet=&delay=" id="widgetframe" frameborder="0" scrolling="no">
                  </iframe>';
        /*$widget = "<iframe style=\"border: medium none ; overflow: hidden; width: 600px; height: 400px;\""
           . "src=\"http://widget.collecta.com/widget.html?query="
          . "$terms&alias=$title&headerimg=&stylesheet=&delay= "
          . "id=\"widgetframe\" frameborder=\"0\" scrolling=\"no\"></iframe>";*/

        return $widget;
    }

    /**
    *
    * Get tags stored in the format tag1-tag2-tag3
    *
    * @return string The rendered tags or boolean FALSE if no tags found
    *
    */
    private function getTags()
    {

        $tags = $this->objUserParams->getValue("tags");
        if ($tags) {
            $tagsAr = explode("-", $tags);
            $ret = "";
            $terms="";
            $tagNo = count($tagsAr);
            $counter = 1;
            foreach ($tagsAr as $tag) {
                if ($counter == $tagNo) {
                    $terms .= $tag;
                } else {
                    $terms .= $tag . " OR ";
                }
                $ret .= $this->relTag($tag);
                $counter ++;
            }
            $terms = $this->widgetize("terms: $terms");
            return "<center>$terms <br /> $ret</center";
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Get the social network and return it with linked icon
    * and optionally with or without text
    *
    * @param string $network The social network from the array of networks
    * @param string $userId The userid of the person to look up
    * @param boolean $noText TRUE|FALSE whether to return text, default yes
    * @return string The rendered icon/text
    * @access public
    *
    */
    public function getSocialNetwork($network, $userId, $noText=FALSE)
    {
        $identifier = $network . "url";
        //Change the user id
        if(!is_null($userId) && $userId != ""){
             $this->objUserParams->setUserId($userId);
        }
        if ($url = $this->objUserParams->getValue($identifier)) {
            $icon = $this->getLinkIcon($network);
            if ($noText) {
                return "<a class='url' rel='me' href='$url' "
                 . "target='_blank'>$icon</a><br />\n";
            } else {
                return "<a class='url' rel='me' href='$url' "
                  . "target='_blank'>$icon $url</a><br />\n";
            }
        }
    }

    /**
    *
    * Get the country of the user and render in in hcard format
    *
    * @param boolean $noText TRUE|FALSE whether to return text, default yes
    * @return string The rendered country with flag
    * @access private
    *
    */
    private function getCountry($noText=FALSE)
    {
        // Use this to get the country flag
        $objCountries = $this->getObject('countries', 'utilities');
        $countryName = $objCountries->getCountryName($this->country);
        $countryFlag = $objCountries->getCountryFlag($this->country);
        $ret = "<div class=\"country-name\">$countryName</div>";
        return "<table><tr><td> $countryFlag</td><td>$ret</td></tr></table>";
    }

    /**
     * Method to return the user details form
     *
     * @param array $details The users details
     * @access public
     * @return string $html The html for the form
     */
    public function userDetailsForm($details = NULL)
    {
        $html = "<br />&nbsp;<br />";

        //Place the form in a div
        $html .= '<div id="userdetailsform" class="userdetailsform">';

        //Create the form
        $form = new form('userdetails',$this->uri(array('action'=>'updatedetails')));
        //$form->displayType = 4;

        //Create the latitude text input
        $latInput = new textinput('latitude');
        $latInput->size = 30;
        $latLabel = new label($this->objLanguage->languageText('mod_fullprofile_latitude', 'fullprofile', 'Latitude'), 'input_latitude');

        //Create the latitude text input
        $longInput = new textinput('longitude');
        $longInput->size = 30;
        $longLabel = new label($this->objLanguage->languageText('mod_fullprofile_longitude', 'fullprofile', 'Longitude'), 'input_longitude');

        //Create the latitude text input
        $tagsInput = new textinput('tags');
        $tagsInput->size = 30;
        $tagsLabel = new label($this->objLanguage->languageText('mod_fullprofile_tags', 'fullprofile', 'Tags'), 'input_tags');

        $objTable = $this->newObject('htmltable', 'htmlelements');

        $title = $this->getObject('htmlheading', 'htmlelements');
        $title->type = '3';
        $title->str = $this->objLanguage->languageText('mod_fullprofile_userinfo', 'fullprofile');

        $objTable->startRow();
        $objTable->addCell($title->show(), null, 'top', null, null, 'colspan="2"', '0');
        $objTable->endRow();
        
        $button = new button ('search', $this->objLanguage->languageText('word_submit', 'system', 'Submit'));
        $button->cssId = 'submitbutton';
        $button->setToSubmit();

        $objTable->startRow();
        $objTable->addCell($tagsLabel->show());
        $objTable->addCell($tagsInput->show());
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell($latLabel->show());
        $objTable->addCell($latInput->show());
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell($longLabel->show());
        $objTable->addCell($longInput->show());
        $objTable->endRow();


    }

}
?>