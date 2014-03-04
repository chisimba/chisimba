<?php
/**
 * Class to handle blog elements (links and blogroll).
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    $Id: blogprofiles_class_inc.php 16801 2010-02-14 09:45:45Z dkeats $
 * @package    blog
 * @subpackage blogops
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
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
 * Class to handle blog elements (links and blogroll)
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: blogprofiles_class_inc.php 16801 2010-02-14 09:45:45Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class blogprofiles extends object {
    /**
     * Description for public
     *
     * @var    mixed
     * @access public
     */
    public $objConfig;
    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init() {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objDbBlog = $this->getObject('dbblog');
            $this->loadClass('href', 'htmlelements');
            $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->showfullname = $this->sysConfig->getValue('show_fullname', 'blog');
            $this->objUser = $this->getObject('user', 'security');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
        if (!extension_loaded("imap")) {
            $this->mail2blog = FALSE;
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid  Parameter description (if any) ...
     * @param  array   $profile Parameter description (if any) ...
     * @return object  Return description (if any) ...
     * @access public
     */
    public function profileEditor($userid, $profile = NULL) {
        // print_r($profile);
        // profile editor and creator
        // start a form object
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        if ($profile != NULL) {
            $pform = new form('setprofile', $this->uri(array(
                    'action' => 'editprofile',
                    'mode' => 'editprofile',
                    'id' => $profile['id']
            )));
        } else {
            $pform = new form('setprofile', $this->uri(array(
                    'action' => 'setprofile',
                    'mode' => 'saveprofile',
            )));
        }
        $pfieldset = $this->newObject('fieldset', 'htmlelements');
        // $pfieldset->setLegend($this->objLanguage->languageText('mod_blog_setprofile', 'blog'));
        $ptable = $this->newObject('htmltable', 'htmlelements');
        $ptable->cellpadding = 3;
        $ptable->startHeaderRow();
        $ptable->addHeaderCell('');
        $ptable->addHeaderCell('');
        $ptable->endHeaderRow();
        // blog name field
        $ptable->startRow();
        $bnamelabel = new label($this->objLanguage->languageText('mod_blog_blogname', 'blog') . ':', 'input_blogname');
        $bname = new textinput('blogname');
        if (isset($profile['blog_name'])) {
            $bname->setValue($profile['blog_name']);
        }
        $bname->size = 59;
        // $bname->setValue();
        $ptable->addCell($bnamelabel->show());
        $ptable->addCell($bname->show());
        $ptable->endRow();
        // blog description field
        $ptable->startRow();
        $bdeclabel = new label($this->objLanguage->languageText('mod_blog_blogdesc', 'blog') . ':', 'input_blogdesc');
        $bdec = new textarea('blogdesc');
        if (isset($profile['blog_descrip'])) {
            $bdec->setValue($profile['blog_descrip']);
        }
        $ptable->addCell($bdeclabel->show());
        $ptable->addCell($bdec->show());
        $ptable->endRow();
        // blogger profile field
        $ptable->startRow();
        $bprflabel = new label($this->objLanguage->languageText('mod_blog_bloggerprofile', 'blog') . ':<br />'.$this->objLanguage->languageText('mod_blog_bloggerprofileinstruction', 'blog'), 'input_blogprofile');
        $bprf = $this->newObject('htmlarea', 'htmlelements');
        $bprf->setName('blogprofile');
        if (isset($profile['blogger_profile'])) {
            $bprf->setcontent($profile['blogger_profile']);
        }
        $ptable->addCell($bprflabel->show(), 200);
        $ptable->addCell($bprf->show());
        $ptable->endRow();
        // put it all together and set up a submit button
        $pfieldset->addContent($ptable->show());
        $pform->addToForm($pfieldset->show());
        $this->objPButton = new button($this->objLanguage->languageText('word_save', 'system'));
        $this->objPButton->setIconClass("save");
        $this->objPButton->setValue($this->objLanguage->languageText('word_save', 'system'));
        $this->objPButton->setToSubmit();
        $pform->addToForm($this->objPButton->show());
        $pform = $pform->show();
        // bust out a featurebox for consistency
        // $objFeatureBox = $this->newObject('featurebox', 'navigation');
        // $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_setprofile", "blog") , $pform);
        return $pform;
        // return $ret;

    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  string $userid Parameter description (if any) ...
     * @return mixed  Return description (if any) ...
     * @access public
     */
    public function showProfile($userid) {
        $objFeatureBox = $this->getObject("featurebox", "navigation");
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('href', 'htmlelements');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $tllink = new href($this->uri(array(
                'module' => 'blog',
                'action' => 'timeline',
                'userid' => $userid
                )) , $this->objLanguage->languageText("mod_blog_viewtimelineof", "blog"));
        $tllinkTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" 
          . $tllink->show() . "<br /></span>";
        // go back to your blog
        $viewmyblog = new href($this->uri(array(
                'action' => 'viewblog'
                )) , $this->objLanguage->languageText("mod_blog_viewmyblog", "blog"));
        $viewmyblogTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" 
          . $viewmyblog->show() . "<br /></span>";
        $check = $this->objDbBlog->checkProfile($userid);
        $numgeoposts = $this->objDbBlog->countGeoPosts($userid);
        $viewgeoblog = NULL;
        if($numgeoposts > 0) {
            // create a link to view a map of geoposts
            $viewgeoblog = new href($this->uri(array(
                    'action' => 'viewgeoblog',
                    'userid' => $userid,
                    )) , $this->objLanguage->languageText("mod_blog_viewgeoblog", "blog"));
            $viewgeoblog = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
              . $viewgeoblog->show() . "<br /></span>";
        }
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        if(!$this->objModules->checkIfRegistered('simplemap')) {
            $viewgeoblog = NULL;
        }
        if ($check != FALSE && $check['blog_name'] != NULL || $check['blog_descrip'] != NULL || $check['blogger_profile'] != NULL) {
            $link = new href($this->uri(array(
                    'module' => 'blog',
                    'action' => 'viewprofile',
                    'userid' => $userid
                    )) , $this->objLanguage->languageText("mod_blog_viewprofileof", "blog") . " " . $this->objUser->userName($userid));
            $linkTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $link->show() . "<br /></span>";

            $tllink = new href($this->uri(array(
                    'module' => 'blog',
                    'action' => 'timeline',
                    'userid' => $userid
                    )) , $this->objLanguage->languageText("mod_blog_viewtimelineof", "blog"));
            $tllinkTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $tllink->show() . "<br /></span>";
            $foaffile = $this->objConfig->getsiteRoot() . "usrfiles/users/" . $userid . "/" . $userid . ".rdf";
            @$rdfcont = file($foaffile);
            if (!empty($rdfcont)) {
                $objFIcon = $this->newObject('geticon', 'htmlelements');
                $objFIcon->setIcon('foaftiny', 'gif', 'icons');
                $lficon = new href($this->objConfig->getsiteRoot() . "/usrfiles/users/" . $userid . "/" . $userid . ".rdf", $objFIcon->show() , NULL);
                $ficon = $lficon->show();
                // new href($this->objConfig->getsiteRoot() . "/usrfiles/users/" . $userid . "/". $userid . ".rdf", $this->objLanguage->languageText("mod_blog_foaflink", "blog"));
                return $objFeatureBox->show($this->objLanguage->languageText("mod_blog_viewprofile", "blog") , $linkTxt
                  . $ficon . $tllinkTxt . $viewgeoblog);
            } else {
                $objFeatureBox = $this->getObject("featurebox", "navigation");
                return $objFeatureBox->show($this->objLanguage->languageText("mod_blog_viewprofile", "blog") , $linkTxt
                  . $tllinkTxt . $viewgeoblog . $viewmyblogTxt);
            }
        } else {
            $objFeatureBox = $this->getObject("featurebox", "navigation");
            return $objFeatureBox->show($this->objLanguage->languageText("mod_blog_viewprofile", "blog") , $tllinkTxt 
               . $viewgeoblog . $viewmyblogTxt);
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  string $userid Parameter description (if any) ...
     * @return mixed  Return description (if any) ...
     * @access public
     */
    public function showFullProfile($userid) {
        if ($this->showfullname == 'FALSE') {
            $pname = $this->objUser->userName($userid);
        } else {
            $pname = $this->objUser->fullName($userid);
        }
        $objFeatureBox = $this->getObject("featurebox", "navigation");
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('href', 'htmlelements');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $imageUrl = $this->objConfig->getsiteRoot().'/user_images/';
        $imagePath = $this->objConfig->getsiteRootPath().'/user_images/';
        $imagePath= str_replace("//", "/", $imagePath);
        $img="";
        $alt = "alt='User Image'";
       // $userId=$this->objUser->userid();d
        if (file_exists($imagePath.$userid.'.jpg')) {
            $img= '<img src="'.$imageUrl.$userid.'.jpg'.'" '.$alt.' />';
        } else {
            $img= '<img src="'.$imageUrl.'default.jpg" '.$alt.' />';
        }
        $userimg = "<center>" . $img . "</center>";
        $tllink = new href($this->uri(array(
                'module' => 'blog',
                'action' => 'timeline',
                'userid' => $userid
                )) , $this->objLanguage->languageText("mod_blog_viewtimelineof", "blog"));
        $tllinkTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $tllink->show() . "</span>";
        // geotagged posts link
        $numgeoposts = $this->objDbBlog->countGeoPosts($userid);
        $viewgeoblog = NULL;
        if($numgeoposts > 0) {
            // create a link to view a map of geoposts
            $viewgeoblog = new href($this->uri(array(
                    'action' => 'viewgeoblog',
                    'userid' => $userid,
                    )) , $this->objLanguage->languageText("mod_blog_viewgeoblog", "blog"));
            $viewgeoblog = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
              . $viewgeoblog->show() . "</span>";
        }
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        if(!$this->objModules->checkIfRegistered('simplemap')) {
            $viewgeoblog = NULL;
        }
        // go back to your blog
        if ($this->objUser->isLoggedIn()) {
            $viewmyblog = new href($this->uri(array(
                    'action' => 'viewblog'
                    )) , $this->objLanguage->languageText("mod_blog_viewmyblog", "blog"));
        } else {
            $viewmyblog = new href($this->uri(array(
                    'action' => 'allblogs'
                    )) , $this->objLanguage->languageText("mod_blog_viewallblogs", "blog"));
        }
        $viewmyblogTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">"
          . $viewmyblog->show() . "</span>";
        $check = $this->objDbBlog->checkProfile($userid);
        if ($check != FALSE) {
            $link = new href($this->uri(array(
                    'module' => 'blog',
                    'action' => 'viewprofile',
                    'userid' => $userid
                    )) , $this->objLanguage->languageText("mod_blog_viewprofileof", "blog") 
                    . " " . $this->objUser->userName($userid));
            $linkTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $link->show() . "</span>";
            $tllink = new href($this->uri(array(
                    'module' => 'blog',
                    'action' => 'timeline',
                    'userid' => $userid
                    )) , $this->objLanguage->languageText("mod_blog_viewtimelineof", "blog"));
            $tllinkTxt = "<div class=\"fblinkbefore\"></div><span class=\"featureboxlink\">" . $tllink->show() . "</span>";
            $foaffile = $this->objConfig->getsiteRoot() . "usrfiles/users/" . $userid . "/" . $userid . ".rdf";
            @$rdfcont = file($foaffile);
            if (!empty($rdfcont)) {
                $objFIcon = $this->newObject('geticon', 'htmlelements');
                $objFIcon->setIcon('foaftiny', 'gif', 'icons');
                $lficon = new href($this->objConfig->getsiteRoot() . "/usrfiles/users/" . $userid . "/" . $userid . ".rdf", $objFIcon->show() , NULL);
                $ficon = $lficon->show();
                // new href($this->objConfig->getsiteRoot() . "/usrfiles/users/" . $userid . "/". $userid . ".rdf", $this->objLanguage->languageText("mod_blog_foaflink", "blog"));
                return  $objFeatureBox->show($this->objLanguage->languageText("mod_blog_viewfullprofile", "blog") . " " . $pname, $linkTxt . "<br />" . $ficon . "<br />" . $tllinkTxt ."<br />".$viewgeoblog);
            } else {
                $objFeatureBox = $this->getObject("featurebox", "navigation");
                return $objFeatureBox->show($this->objLanguage->languageText("mod_blog_viewfullprofile", "blog") . " " . $pname, $linkTxt . "<br />" . $tllinkTxt . "<br />" . $viewmyblogTxt ."<br />".$viewgeoblog);
            }
        } else {
            $objFeatureBox = $this->getObject("featurebox", "navigation");
            return  $objFeatureBox->show($this->objLanguage->languageText("mod_blog_viewfullprofile", "blog") . " " . $pname, $userimg . "<br />" . $tllinkTxt."<br />".$viewgeoblog . "<br />" . $viewmyblogTxt);
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid  Parameter description (if any) ...
     * @param  array   $profile Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function displayProfile($userid, $profile) {
        $objFeatureBox = $this->getObject("featurebox", "navigation");
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->bbcode = $this->getObject('bbcodeparser', 'utilities');
        $prtable = $this->newObject('htmltable', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $prtable->cellpadding = 3;
        $prtable->startHeaderRow();
        $prtable->addHeaderCell('');
        $prtable->addHeaderCell('');
        $prtable->endHeaderRow();
        // blog name field
        $prtable->startRow();
        $bnamelabel = $this->objLanguage->languageText('mod_blog_blogname', 'blog');
        $bname = $profile['blog_name'];
        $prtable->addCell($bnamelabel);
        $prtable->addCell($bname);
        $prtable->endRow();
        $prtable->startRow();
        $bdeclabel = $this->objLanguage->languageText('mod_blog_blogdescription', 'blog');
        $bdec = stripslashes($this->bbcode->parse4bbcode($profile['blog_descrip']));
        $prtable->addCell($bdeclabel);
        $prtable->addCell($bdec);
        $prtable->endRow();
        // blogger profile field
        $prtable->startRow();
        $bprflabel = $this->objLanguage->languageText('mod_blog_bloggerprf', 'blog');
        $bprf = stripslashes($this->bbcode->parse4bbcode($profile['blogger_profile']));
        $prtable->addCell($bprflabel);
        $prtable->addCell($bprf);
        $prtable->endRow();
        $content = $prtable->show();
        if ($this->showfullname == 'FALSE') {
            $namer = $this->objUser->userName($userid);
        } else {
            $namer = $this->objUser->fullname($userid);
        }
        return $objFeatureBox->showContent($this->objLanguage->languageText("mod_blog_profileof", "blog") . " " . $namer, $content);
    }
}
?>