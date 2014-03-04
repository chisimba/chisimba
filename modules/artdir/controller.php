<?php
/**
 * artdir controller class
 *
 * Class to control the artdir module
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  chisimba
 * @package   artdir
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * artdir controller class
 *
 * Class to control the artdir module.
 *
 * @category  Chisimba
 * @package   artdir
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
class artdir extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    
    /**
     * Object of terms dialogue class in the blog module.
     *
     * @access protected
     * @var object $objTermsDialogue
     */
    protected $objTermsDialogue;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->objLanguage      = $this->getObject ( 'language', 'language' );
            $this->objConfig        = $this->getObject('altconfig', 'config');
            $this->objSysConfig     = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser          = $this->getObject('user', 'security');
            $this->objModuleCat     = $this->getObject('modules', 'modulecatalogue');
            $this->objFoaf          = $this->getObject('foafcreator', 'foaf');
            $this->objFoafParser    = $this->getObject('foafparser', 'foaf');
            $this->objFoafOps       = $this->getObject('foafops', 'foaf');
            $this->dbFoaf           = $this->getObject('dbfoaf', 'foaf');
            $this->objTermsDialogue = $this->getObject('artdirtermsdialogue');
            $this->objCats          = $this->getObject('artdircategories');
            $this->objDbArtdir      = $this->getObject('dbartdir');
            $this->objUi            = $this->getObject('artdirui');
            $this->objFile          = $this->getObject('dbfile', 'filemanager');
        }
        catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL:
                return 'default_tpl.php';
                break;
                
            case 'aboutus' :
                return 'aboutus_tpl.php';
                break;
            
            case 'genfoaf' :
                $userid = $this->getParam('userid'); //$this->objUser->userId();
                $this->setVarByRef('userid', $userid);
                //create the basic foaf profile from tbl_users
                $this->objFoafOps->newPerson($userid);
                //add in other details if they exist
                $this->objFoafOps->myFoaf($userid);
                $this->objFoafOps->writeFoaf();
                $midcontent = $this->objFoafOps->foaf2Object($userid);
                
                break;
                
            case 'deletecat' :
                $id = $this->getParam('id');
                $this->objDbArtdir->deleteCat($id);
                $this->nextAction('');
                break;
            
            case 'catadd':
                if ($this->objUser->isLoggedIn() == FALSE) {
                    //not logged in - send to default action
                    $this->nextAction('');
                    exit;
                }
                //check the mode and cat name as wel as user id
                $mode = $this->getParam('mode');
                $list = $this->getParam('catname');
                $userid = $this->objUser->userId();
                $catname = $this->getParam('catname');
                $catparent = $this->getParam('catparent');
                $catdesc = $this->getParam('catdesc');
                $id = $this->getParam('id');
                $this->setVarByRef('mode', $mode);
                if ($mode == 'edit') {
                    //update the records in the db
                    //build the array again
                    $entry = $this->objDbArtdir->getCatForEdit($userid, $id);
                    $catarr = array(
                            'userid' => $userid,
                            'cat_name' => $entry['cat_name'],
                            'cat_nicename' => $entry['cat_nicename'],
                            'cat_desc' => $entry['cat_desc'],
                            'cat_parent' => $entry['cat_parent'],
                            'id' => $id
                    );
                    //display the cat editor with the values in the array, set that form to editcommit
                    $this->setVarByRef('catarr', $catarr);
                    $this->setVarByRef('userid', $userid);
                    $this->setVarByRef('catid', $id);
                    
                    return 'editcats_tpl.php';
                    break;
                }
                if ($mode == 'editcommit') {
                    $catarr = array(
                            'userid' => $userid,
                            'cat_name' => $catname,
                            'cat_nicename' => $catname,
                            'cat_desc' => $catdesc,
                            'cat_parent' => $catparent,
                            'id' => $id
                    );
                    $this->objDbArtdir->setCats($userid, $catarr, $mode);
                    $this->nextAction('artdiradmin', array(
                            'mode' => 'editcats'
                    ));
                }
                if ($mode == NULL) {
                    $catarr = array(
                            'userid' => $userid,
                            'cat_name' => $catname,
                            'cat_nicename' => $catname,
                            'cat_desc' => $catdesc,
                            'cat_parent' => $catparent
                    );
                    //insert the category into the db
                    $this->objDbArtdir->setCats($userid, $catarr);
                    $this->nextAction('artdiradmin', array(
                            'mode' => 'editcats'
                    ));
                    break;
                }
                break;
                
            case 'artdiradmin':
                //make sure the user is logged in
                if ($this->objUser->isLoggedIn() == FALSE) {
                    //bail to the default page
                    $this->nextAction('');
                    //exit this action
                    exit;
                }
                //get the user id
                $userid = $this->objUser->userId();
                $this->setVarByRef('userid', $userid);
                // Check if the user is allowed to use the dir
                if (!($this->approvedArtist())) {
                    return 'not_approved_tpl.php';
                    exit;
                }
                // Check to see if the user needs to accept terms and conditions before being able to blog.
                $terms = $this->objSysConfig->getValue('mod_artdir_terms', 'artdir');
                if ($terms) {
                    $acceptedBlogTerms = $this->objUserParams->getValue('accepted_artdir_terms');
                    if (!$acceptedBlogTerms) {
                        $dialogueContent = file_get_contents($terms);
                        $this->objTermsDialogue->setContent($dialogueContent);
                    }
                }
                //check the mode
                $mode = $this->getParam('mode');
                $this->setVarByRef('mode', $mode);
                switch ($mode) {
                    //return a specific template for the chosen mode

                    case 'addartist':
                        return 'addartist_tpl.php';
                        break;

                    case 'editartist':
                        return 'editartist_tpl.php';
                        break;

                    case 'editcats':
                        return 'editcats_tpl.php';
                        break;

                    case 'acceptterms':
                        $value = $this->objUserParams->setItem('accepted_artdir_terms', 1);
                        $data = array('value' => $value);
                        $json = json_encode($data);
                        $this->setContentType('application/json');
                        echo $json;
                        return;
                }
                // return the default template for no mode set
                return 'artddiradminmenu_tpl.php';
                break;
                
            case 'showsignin' :
                $objUi = $this->getObject('artdirui');
                echo $objUi->signinBox();
                break;
                
            case 'search' :
                $term = $this->getParam('search');
                $recs = $this->objDbArtdir->artistSearch($term);
                $this->setVarByRef('recs', $recs);
                return 'catlisting_tpl.php';
                break;
                
            case 'viewbycat' :
                $cat = $this->getParam('cat');
                $recs = $this->objDbArtdir->getArtistsByCat($cat);
                $this->setVarByRef('recs', $recs);
                return 'catlisting_tpl.php';
                break;
                
            case 'browse' :
                $recs = $this->objDbArtdir->getAllArtists();
                $this->setVarByRef('recs', $recs);
                return 'catlisting_tpl.php';
                break;
                
            case 'viewpic' :
                $picid = $this->getParam('picid');
                //echo $picid;
                echo '<img src="'.$this->objFile->getFilePath($picid).'" />';
                break;
                
            case 'viewartist' :
                $id = $this->getParam('id');
                $artist = $this->objDbArtdir->getArtistById($id);
                $artistdata = $this->objUi->formatArtist($artist);
                $this->setVarByRef('artistdata', $artistdata);
                return 'artist_tpl.php';
                break;
                
            case 'editartist' :
                $artistid = $this->getParam('id');
                $this->setVarByRef('artistid', $artistid);
                return 'editartist_tpl.php';
                break;
                
            case 'imgup' :
                $artistid = $this->getParam('id');
                $catid = $this->getParam('catid');
                $userid = $this->objUser->userId();
                
                $pic1 = $this->getParam('pic1', NULL);
                $pic2 = $this->getParam('pic2', NULL);
                $pic3 = $this->getParam('pic3', NULL);
                // insert the pics to the table refs
                if($pic1 != NULL) {
                    $insarr1 = array('userid' => $userid, 'artistid' => $artistid, 'catid' => $catid, 'picid' => $pic1);
                    $this->objDbArtdir->insertPic($insarr1);
                }
                if($pic2 != NULL) {
                    $insarr3 = array('userid' => $userid, 'artistid' => $artistid, 'catid' => $catid, 'picid' => $pic2);
                    $this->objDbArtdir->insertPic($insarr3);
                }
                if($pic3 != NULL) {
                    $insarr3 = array('userid' => $userid, 'artistid' => $artistid, 'catid' => $catid, 'picid' => $pic3);
                    $this->objDbArtdir->insertPic($insarr3);
                }
                $this->nextAction('');
                break;
                
            case 'artistedit': 
                $id = $this->getParam('id');
                $userid = $this->objUser->userId();
                $cat = $this->getParam('cat');
                $actname = $this->getParam('actname');
                $desc = $this->getParam('desc');
                $contactperson = $this->getParam('contactperson');
                $contactnum = $this->getParam('contactnum');
                $altnum = $this->getParam('altnum');
                $email = $this->getParam('email');
                $website = $this->getParam('website');
                $bio = $this->getParam('bio');
                $thumb = $this->getParam('thumb');
                // update the db
                if($thumb != "") {
                    $updatearr = array('id' => $id, 'userid' => $userid, 'catid' => $cat, 'actname' => $actname, 'description' => $desc, 
                                   'contactperson' => $contactperson,
                                   'contactnum' => $contactnum, 'altnum' => $altnum, 'email' => $email, 'website' => $website, 'bio' => $bio,
                                   'thumbnail' => $thumb);
                }
                else {
                    $updatearr = array('id' => $id, 'userid' => $userid, 'catid' => $cat, 'actname' => $actname, 'description' => $desc, 
                                   'contactperson' => $contactperson,
                                   'contactnum' => $contactnum, 'altnum' => $altnum, 'email' => $email, 'website' => $website, 'bio' => $bio);
                }
                
                $this->objDbArtdir->updateArtist($updatearr);
                $this->nextAction('');
                break;
                
            case 'newartist': 
                $id = $this->getParam('id');
                $userid = $this->objUser->userId();
                $cat = $this->getParam('cat');
                $actname = $this->getParam('actname');
                $desc = $this->getParam('desc');
                $contactperson = $this->getParam('contactperson');
                $contactnum = $this->getParam('contactnum');
                $altnum = $this->getParam('altnum');
                $email = $this->getParam('email');
                $website = $this->getParam('website');
                $bio = $this->getParam('bio');
                $thumb = $this->getParam('thumb');
                // update the db
                if($thumb != "") {
                    $insertarr = array('id' => $id, 'userid' => $userid, 'catid' => $cat, 'actname' => $actname, 'description' => $desc, 
                                   'contactperson' => $contactperson,
                                   'contactnum' => $contactnum, 'altnum' => $altnum, 'email' => $email, 'website' => $website, 'bio' => $bio,
                                   'thumbnail' => $thumb);
                }
                else {
                    $insertarr = array('id' => $id, 'userid' => $userid, 'catid' => $cat, 'actname' => $actname, 'description' => $desc, 
                                   'contactperson' => $contactperson,
                                   'contactnum' => $contactnum, 'altnum' => $altnum, 'email' => $email, 'website' => $website, 'bio' => $bio);
                }
                
                $this->objDbArtdir->addArtist($insertarr);
                $this->nextAction('');
                break;
                
            case 'deleteartist' :
                $artistid = $this->getParam('id');
                $this->objDbArtdir->removeArtist($artistid);
                $this->nextAction('');
                break;
                
            case 'bookingform' :
                $id = $this->getParam('id');
                $bookform = $this->objUi->bookingForm($id);
                $this->setVarByRef('bookform', $bookform);
                return 'bookartist_tpl.php';
                break;
                
            case 'bookartist' :
                $id = $this->getParam('id');
                $yourname = $this->getParam('yourname');
                $youremail = $this->getParam('youremail');
                $yournum = $this->getParam('yournum');
                $request = $this->getParam('request');
                $artist = $this->objDbArtdir->getArtistById($id);
                $artistmail = $artist[0]['email'];
                
                //thump together an email string (this must be html email as the post is html
                $objMailer = $this->getObject('mailer', 'mail');
                //munge together the bodyText...
                $bodyText = $this->objLanguage->languageText("mod_artdir_sender", "artdir") . ", " . $yourname . ", " . $this->objLanguage->languageText("mod_artdir_hassentrequest", "artdir") . ": ";
                $bodyText.= "<br /><br />";
                if (!empty($request)) {
                    $bodyText.= $request . "<br /><br />";
                }
                $bodyText.= "<br /><br />";
                $objMailer->setValue('IsHTML', TRUE);
                $objMailer->setValue('to', $artistmail);
                $objMailer->setValue('cc', 'pscott209@gmail.com');
                $objMailer->setValue('from', 'noreply@artistdirectory.co.za');
                $objMailer->setValue('fromName', $this->objLanguage->languageText("mod_artdir_emailfromname", "artdir"));
                $objMailer->setValue('subject', $this->objLanguage->languageText("mod_artdir_bookingrequest", "artdir"));
                $objMailer->setValue('body', $bodyText);
                $objMailer->send(TRUE);
                $this->nextAction('');
                break;

                
            default:
                $this->nextAction('');
                break;
        }
    }

    /**
     * Method to turn off login for selected actions
     *
     * @access public
     * @param string $action Action being run
     * @return boolean Whether the action requires the user to be logged in or not
     */
    function requiresLogin($action='') {
        $allowedActions = array('', 'search', 'showsignin', 'viewartist', 'viewpic', 'viewbycat', 'aboutus', 'browse', 'bookingform', 'bookartist', NULL);

        if (in_array($action, $allowedActions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Check if user is allowed to use the system
     * @returns boolean
     */
    public function approvedArtist() {
        $artdirSetting = $this->objSysConfig->getValue('limited_users', 'artdir');
        if ($artdirSetting) {
            $groupId = $this->objGroup->getId('Artists');
            $userId = $this->objUser->userid();
            return $this->objGroup->isGroupMember($userId, $groupId);
        } else {
            return TRUE;
        }
    }
}
?>
