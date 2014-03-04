<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}

/**
 * Class mentordisplay containing all display/output functions of the mentor module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright Wits University 2010
 * @license http://opensource.org/licenses/lgpl-2.1.php
 * @package mentors
 *
 */
class mentorsdisplay extends object
{
   /** @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;

   /** @var object $objUser: The user class of the buddies module
    * @access public
    */
   public $objUser;

   /** @var object $objMentorFuncs: The funcs class of the mentors module
    * @access public
    */
   public $objMentorFuncs;

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
	$this->objMentorFuncs = $this->getObject('mentorfuncs', 'mentors');
        $this->objDbWiki = $this->getObject('dbwiki', 'wiki');
    }
    /**
    * Method to display the default admin interface
    *
    * @access public
    * @param array $allUsers An array with all users to be displayed
    * @param string $notification Any notification to be displayed
    * @param string $saerchField The current search field
    * @return string $str: The output string
    **/
    public function admin($allUsers, $notification, $searchField)
    {
        // Create alphabet display object
        $objAlphabet = &$this->getObject('alphabet', 'navigation');

        $linkarray = array('action' => 'admin', 'how' => 'firstname', 'searchField' => 'LETTER');
        $url = $this->uri($linkarray, 'mentors');

        $pgTitle = &$this->getObject('htmlheading', 'htmlelements');
        $pgTitle->type = 1;
        $pgTitle->str = $this->objLanguage->languageText('mod_mentors_mentoradmin', 'mentors');
        // Create a table
        $objTableClass = $this->newObject('htmltable', 'htmlelements');
        $objTableClass->cellspacing = "2";
        $objTableClass->cellpadding = "2";
        $objTableClass->width = "70%";
        $objTableClass->attributes = 'border=0';

        if(is_array($allUsers) && count($allUsers) > 0 ){
            // Create the array for the table header
            $tableRow = array();
            $tableHd[] = "";
            $tableHd[] = $this->objLanguage->languageText('mod_mentors_fullname', 'mentors');
            $tableHd[] = $this->objLanguage->languageText('mod_mentors_email', 'mentors');
            $tableHd[] = $this->objLanguage->languageText('mod_mentors_action', 'mentors');

            // Create the table header for display
            $objTableClass->addHeader($tableHd, "heading");

            $index = 0;
            $rowcount = 0;

            foreach ($allUsers as $user) {
				$rowcount++;
				// Set odd even colour scheme
				$class = ($rowcount % 2 == 0)?'odd':'even';
				// Get user pic
				$objUserPic = &$this->getObject('imageupload', 'useradmin');
				$pic = "<image src=\"" . $objUserPic->smallUserPicture($user['userid']) . "\"/>";
				// Get user name
				$username = $user["firstname"] . "&nbsp;" . $user["surname"];
				// Get user email
				$email = "<a href=\"mailto:" . $user["emailaddress"] . "\">" . $user["emailaddress"] . "</a>";
				// Create make mentor link or show is mentor icon
				$makeMentor = '';
				//Get the users mentor userid
				$mentorId = $this->objMentorFuncs->getMentors($this->objUser->userId());
                                $studentId = $this->objMentorFuncs->getStudents($this->objUser->userId());
				//If is current user just show nothing
				if ($user['userid'] == $this->objUser->userId()) {
				//If not mentor then show mentor icon
				} else if (is_array($mentorId) && in_array($user['userid'], $mentorId)) {
                                    $mentorIcon = $this->newObject('geticon', 'htmlelements');
                                    $mentorIcon->setIcon('mentor', 'png');
                                    $mentorIcon->alt = $this->objLanguage->languageText('mod_mentors_mentor', 'mentors');
                                    $makeMentor = $mentorIcon->show();
                                    $deleteIcon = $this->newObject('geticon', 'htmlelements');
                                    $deleteIcon->setIcon('delete');
                                    $deleteIcon->alt = $this->objLanguage->languageText('mod_mentors_delete', 'mentors');
                                    $deleteLink = "<a href = \"" . $this->uri(array('module' => 'mentors',
						'action' => 'deletementor',
						'mentorId' => $user['userid'],
                                                'userId' => $this->objUser->userId(),
						'how' => 'firstname',
						'searchField' => $this->getParam('searchField')
					))
					. "\">" . $deleteIcon->show() . "</a>";
                                    $makeMentor = $mentorIcon->show().'&nbsp;'.$deleteLink;

				} else if (is_array($studentId) && in_array($user['userid'], $studentId)) {
                                    $mentorIcon = $this->newObject('geticon', 'htmlelements');
                                    $mentorIcon->setIcon('student');
                                    $mentorIcon->alt = $this->objLanguage->languageText('mod_mentors_student', 'mentors');
                                    $deleteIcon = $this->newObject('geticon', 'htmlelements');
                                    $deleteIcon->setIcon('delete');
                                    $deleteIcon->alt = $this->objLanguage->languageText('mod_mentors_delete', 'mentors');
                                    $deleteLink = "<a href = \"" . $this->uri(array('module' => 'mentors',
						'action' => 'deletementor',
						'mentorId' => $this->objUser->userId(),
                                                'userId' => $user['userid'],
						'how' => 'firstname',
						'searchField' => $this->getParam('searchField')
					))
					. "\">" . $deleteIcon->show() . "</a>";
                                    $makeMentor = $mentorIcon->show().'&nbsp;'.$deleteLink;

					//Else show the make mentor icon
				} else {
                                    $makeMentor = "<a href = \"" . $this->uri(array('module' => 'mentors',
						'action' => 'makementor',
						'mentorId' => $user["userid"],
                                                'userId' => $this->objUser->userId(),
						'how' => 'firstname',
						'searchField' => $this->getParam('searchField')
					))
					. "\">" . $this->objLanguage->languageText('mod_mentors_makementor', 'mentors') . "</a>";

				}

                $objTableClass->startRow();
                $objTableClass->addCell($pic, '', '', '', $class);
                $objTableClass->addCell($username, '', '', '', $class);
                $objTableClass->addCell($email, '', '', '', $class);
                $objTableClass->addCell($makeMentor, '', '', '', $class);
                $objTableClass->endRow();

            }

        }

        //Build the content
        $content = $pgTitle->show();
        //Display notifications
        if(!is_null($notification) && $notification != ''){
            $timeoutMsg = $this->newObject('timeoutmessage', 'htmlelements');
            $timeoutMsg->setMessage($notification);
            $content .= '<p>'.$timeoutMsg->show().'</p>';
        }
        $content .= $objAlphabet->putAlpha($url);
        $content .= $objTableClass->show();
        if (empty($allUsers)) {
             $content .= "<p>" . "<span class='noRecordsMessage'>" . $this->objLanguage->languageText('mod_mentors_nouser', 'mentors') . "&nbsp;" . '"' . $searchField . '"' . "</span>" . "</p>";
        }
        //Link back to posts
        $backLink = "<a href = \"" . $this->uri(array(), 'mentors')
			. "\">" . $this->objLanguage->languageText('mod_mentors_viewposts', 'mentors') . "</a>";
        $content .= '<p>'.$backLink.'</p>';

        return $content;
    }

    /**
     * Method to return the html for the interface where users can view post and
     * replies made by their mentor/student
     *
     * @access public
     * @param string $userId The userId of the user whose relevant posts should be returned
     * @return string $content The output html
     */
    public function displayPosts($userId)
    {
	//Get the users mentors
        $mentors = $this->objMentorFuncs->getMentors($userId);
        //Get the users students
        $students = $this->objMentorFuncs->getStudents($userId);
        //The ouput string
        $content = "";

        //Create the page header
        $pgTitle = &$this->getObject('htmlheading', 'htmlelements');
        $pgTitle->type = 1;
        $pgTitle->str = $this->objLanguage->languageText('mod_mentors_viewposts', 'mentors');

        $content .= '<p>'.$pgTitle->show().'</p>';

        //Create link to create new post
        $wikiId = $this->objMentorFuncs->createMentorsWiki();
        $newPostLink = "<a href = \"" . $this->uri(array('module' => 'wiki',
						'action' => 'add_page',
                                                'wiki' => $wikiId
					), 'wiki')
					. "\">" . $this->objLanguage->languageText('mod_mentors_createnewpost', 'mentors') . "</a>";
        $viewLink = "<a href = \"" . $this->uri(array('module' => 'wiki',
						'action' => 'view_page',
                                                'wiki' => $wikiId,
						'name' => 'mainpage',
					), 'wiki')
					. "\">" .$this->objLanguage->languageText('mod_mentors_viewwiki', 'mentors') . "</a>";

        $content .= '<p>'.$newPostLink.'&nbsp;&nbsp;'.'|'.'&nbsp;&nbsp;'.$viewLink.'</p>';

        //Make mentor add icon
        $mentorAddIcon = $this->newObject('geticon', 'htmlelements');
        $mentorAddIcon->setIcon('add');
        $mentorAddIcon->alt = $this->objLanguage->languageText('mod_mentors_addmentor', 'mentors');

        //Make mentor add link
        $addMentorLink = "<a href = \"" . $this->uri(array('module' => 'mentors',
        		'action' => 'admin',
                        'searchField' => 'A',
                        'how' => 'firstname'
                	), 'mentors')
			. "\">" . $mentorAddIcon->show() . "</a>";

        $pgTitle->str = $this->objLanguage->languageText('mod_mentors_mentors', 'mentors').'&nbsp;'.$addMentorLink;
        //Add mentors header to output string
        $content .= $pgTitle->show();

        //Loop through mentors and display each mentors posts
        if(is_array($mentors) && count($mentors)>0){
            foreach($mentors as $mentorId){
                //Get the users wiki posts
                $wikiId = $this->objMentorFuncs->createMentorsWiki();
                $this->objDbWiki->wikiId = $wikiId;
                $userWikiArticles = $this->objDbWiki->getAuthorArticles($mentorId);

                $pgTitle->type = 2;
                $pgTitle->str = $this->objUser->fullname($mentorId);
                $content .= $pgTitle->show();
                if(is_array($userWikiArticles) && count($userWikiArticles)>0){
                    foreach($userWikiArticles as $article){
                        $articleLink = "<a href = \"" . $this->uri(array('module' => 'wiki',
						'action' => 'view_page',
                                                'wiki' => $this->objDbWiki->wikiId,
						'name' => $article['page_name'],
					), 'wiki')
					. "\">" . $article['page_name'] . "</a>";
                        $content .= '<p>'.date("F j, Y, g:i a", strtotime($article['date_created'])).'&nbsp;&nbsp;&nbsp;'.$articleLink.'</p>';
                    }
                } else {
                    $content .= '<p>'.$this->objLanguage->languageText('mod_mentors_nomentorposts', 'mentors').'</p>';
                }

            }
        } else {
            $content .= '<p>'.$this->objLanguage->languageText('mod_mentors_nomentors', 'mentors').'</p>';
        }

        //Make mentor add link
        $addMentorLink = "<a href = \"" . $this->uri(array('module' => 'mentors',
        		'action' => 'admin',
                        'searchField' => 'A',
                        'how' => 'firstname'
                	), 'mentors')
			. "\">" . $this->objLanguage->languageText('mod_mentors_addnewmentor', 'mentors') . "</a>";
        $content .= '<p>'.$addMentorLink.'</p>';

        $pgTitle->type = 1;
        $pgTitle->str = $this->objLanguage->languageText('mod_mentors_students', 'mentors');
        //Add mentors header to output string
        $content .= $pgTitle->show();
        //Loop through mentors and display each mentors posts
        if(is_array($students) && count($students)>0){
            foreach($students as $studentId){
                //Get the users wiki posts
                $wikiId = $this->objMentorFuncs->createMentorsWiki();
                $this->objDbWiki->wikiId = $wikiId;
                $userWikiArticles = $this->objDbWiki->getAuthorArticles($studentId);

                $pgTitle->type = 2;
                $pgTitle->str = $this->objUser->fullname($studentId);
                $content .= $pgTitle->show();
                if(is_array($userWikiArticles) && count($userWikiArticles)>0){
                    foreach($userWikiArticles as $article){
                        $articleLink = "<a href = \"" . $this->uri(array('module' => 'wiki',
						'action' => 'view_page',
                                                'wiki' => $this->objDbWiki->wikiId,
						'name' => $article['page_name'],
					), 'wiki')
					. "\">" . $article['page_name'] . "</a>";
                        $content .= '<p>'.date("F j, Y, g:i a", strtotime($article['date_created'])).'&nbsp;&nbsp;&nbsp;'.$articleLink.'</p>';
                    }
                } else {
                    $content .= '<p>'.$this->objLanguage->languageText('mod_mentors_nostudentposts', 'mentors').'</p>';
                }

            }
        } else {
            $content .= '<p>'.$this->objLanguage->languageText('mod_mentors_nostudents', 'mentors').'</p>';
        }
        
        return $content;
    }

}
?>