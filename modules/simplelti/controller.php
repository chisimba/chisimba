<?php

class simplelti extends controller
{
    protected $objUserAdmin;
    protected $objGroupAdmin;
    protected $ltiLaunchId;
    protected $ltiStuff;
    protected $LTI;

    public function init()
    {
        require_once("lti/launch.php");
        require_once("lti/setup.php");
        // if ( $LTI ) { print "YAY"; } else { print "NOO"; }
        $this->LTI = $LTI;
        $this->objUserAdmin = $this->getObject( 'useradmin_model2', 'security');
        $this->objContext = $this->getObject ( 'dbcontext', 'context' );
        $this->objUser = $this->getObject ( 'user', 'security' );
        $this->objGroupAdmin = $this->getObject('groupadminmodel','groupadmin');
    }

    public function dispatch()
    {
        error_reporting(6135);
        if ( ! $this->LTI )  { 
            $this->ltiStuff = "Not Launched";
            $this->ltiLaunchId =  print_r($_SESSION, TRUE);
            $this->setContentType('text/plain');
            return 'main_tpl.php';
        }
        if ( $this->LTI->org('org_id') && $this->LTI->course('org_id') > 0 ) {
            $courseid = $this->LTI->org('org_id').":".$this->LTI->course('course_id');
        } else {
            $courseid = "simplelti:".$this->LTI->course('course_id');
        }

        if ( $this->LTI->org('org_id') && $this->LTI->user('org_id') > 0 ) {
            $userid = $this->LTI->org('org_id').":".$this->LTI->user('user_id');
        } else if ( $this->LTI->org('org_id') ) {
            $userid = "simplelti:".$this->LTI->course('course_id').":".$this->LTI->org("org_id").":".$this->LTI->user("user_id");
        } else {
            $userid = "simplelti:".$this->LTI->course('course_id').":".$this->LTI->user("user_id");
        }

        // $userid = md5($userid);
        // $courseid = md5($courseid);
        // To match dbcontext_class_inc.php
        // Actually the same code should be put into getContext
        $courseid = preg_replace ( '/\W*/', '', $courseid );

	// print $courseid." ".$userid;

	if ( $this->objUserAdmin->userNameAvailable($userid) ) {
            // $pkid = $this->objUserAdmin->addUser($userId, $username, $password, $title, $firstname, $surname, $email, $sex, $country, $cellnumber, $staffnumber, 'useradmin', $accountstatus);

            $pkid = $this->objUserAdmin->addUser($userid, $userid, "password", "", $this->LTI->user('firstname'), $this->LTI->user('lastname'), $this->LTI->user('email'), "?", "country", "cellnumber", "staffnumber", 'useradmin',  1 /* active */);
        } else {
           // print "User NOT AVAILABLE";
           // updateUserDetails($id, $username='', $firstname, $surname, $title, $email, $sex, $country, $cellnumber='', $staffnumber='', $password='', $accountType='', $accountstatus='')
            // $update = $this->objUserAdmin->updateUserDetails($userid, $userid, $this->LTI->user('firstname'), $this->LTI->user('lastname'), $this->LTI->user('email'), "?", "country", "cellnumber", "staffnumber", "password", 'useradmin',  1 /* active */);
           $myuser = $this->objUser->getUserId($userid);
           // print "looking for  PK ".$myuser;
           $pkid = $this->objUser->PKId($myuser);
        }
        $details = $this->objUserAdmin->getUserDetails($pkid);
        // print_r($details);
        // print "Session key PK=".$pkid." userid=".$userid;

        $username = $details['username'];
        $login = $this->objLu->login($username, "password");


        $this->setSession('id', $pkid, $this->objUser->moduleName);
        $this->setSession('userid', $userid, $this->objUser->moduleName);

        // From storeUserSession in security/classes/abauth_class_inc.php
        $this->setSession('isLoggedIn',TRUE, 'security');
        $username = $details['username'];
        $this->setSession('username',$username, 'security');
        $this->setSession('userid', $details['userid'], 'security');
        $title = stripcslashes($details['title']);
        $this->setSession('title',$title, 'security');
        $firstname = stripcslashes($details['firstname']);
        $surname = stripcslashes($details['surname']);
        $this->setSession('name',$firstname.' '.$surname, 'security');
        $logins = $details['logins'];
        $this->setSession('logins', $details['logins'], 'security');
        $email = stripcslashes($details['emailaddress']);
        $this->setSession('email',$email, 'security');

        $this->setSession('context',$courseid, 'security');
        $this->setSession('isAdmin',TRUE, 'security');

        $user = new stdClass();
        // add the user info to the class
        $user->username = $details['username'];
        $user->userid = $details['userid'];
        $user->title = $details['title'];
        $user->firstname = $details['firstname'];
        $user->surname = $details['surname'];
        $user->pass = NULL;
        $user->creationdate = $details['creationdate'];
        $user->emailaddress = $details['emailaddress'];
        $user->logins = $details['logins'];
        $user->isactive = $details['isactive'];
        // serialize the object to preserve structure etc
        $user = serialize($user);
        // set it into session to be used elsewhere (objUser mainly)
        $this->setSession('userprincipal', $user, $this->objUser->moduleName);
        $this->objContext->leaveContext();

        $mycontext = $this->objContext->getContext($courseid);
        if ( $mycontext ) {
            // print "YAY Found Context";
        } else {
            $mycontext = $this->objContext->createContext($courseid, $this->LTI->course('title'), 'Published', 'Private');
            if ( $mycontext ) {
                 // print "YAY CREATED Context";
            } else {
                 // print "Could not create CREATED Context";
            }
        }

        if ( $mycontext ) {
            // print "Course id = ".$courseid;
            $contextGroupId = $this->objGroupAdmin->getId( $courseid );
	    if ( $contextGroupId ) {
               // print "Found Context group id";
            } else {
               $contextGroupId = $this->objGroupAdmin->addGroup($courseid,$this->LTI->course('title'),NULL);
               // print "Created Context group id = $contextGroupId";
            }

            // print "Context group id = $contextGroupId";
            // It seems as though getLeafId() is not yet implemented in 3 - so we fake it by hand
            if ( $contextGroupId ) {
                $lecGname = $courseId . "^" . "Lecturers";
                // print "lecture group name = ".$lecGname;
                $lecGroupId = $this->objGroupAdmin->getId( $lecGname );
                // print "lecture group id = ".$lecGroupId;
	        if ( $lecGroupId ) {
                   // print "Found Lecture group id";
                } else {
                   $lecGroupId = $this->objGroupAdmin->addGroup($lecGname,$this->LTI->course('title'),NULL);
                   // print "Created Lecture group id = $lecGroupId";
                }
                // Eventually we will do this
                //     public function addGroupUser( $groupId, $userId )

            }

            $this->setSession ( 'contextId', $mycontext ['id'], $this->objContext->moduleName);
            // $this->setSession ( 'contextCode', $mycontext['contextcode'], $this->objContext->moduleName );
            $this->setSession ( 'contextCode', $courseid, $this->objContext->moduleName );
        }

        // Set the Skin
        $this->setSession ( 'skin', 'simplelti', 'skin');
        $this->ltiLaunchId =  print_r($details,TRUE)."\n".print_r($_SESSION, TRUE)."\n".print_r($login, TRUE);

        $forward = $this->getParam("forward");
        if ( $forward ) {
            $this->nextAction(null, null, $forward);
        } else {
            $this->setContentType('text/plain');
            return 'main_tpl.php';
	}
    }

    public function requiresLogin()
    {
        return false;
    }
}
