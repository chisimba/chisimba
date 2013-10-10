<?php
/**
 * Artdir UI elements file.
 *
 * This file controls the artdir UI elements.
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
 * @version    $Id: blogui_class_inc.php 20147 2010-12-31 12:30:20Z dkeats $
 * @package    artdir
 * @subpackage artdirui
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * class to control artdir ui elements
 *
 * This class controls the artdir UI elements. 
 *
 * @category  Chisimba
 * @package   artdir
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2006-2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class artdirui extends object
{
    
    /**
     * Blog categories object
     *
     * @var    object
     * @access public
     */
    public $objArtdirCategories;
    
    /**
     * left Column layout
     *
     * @var    object
     * @access public
     */
    public $leftCol;
    
    /**
     * Right column layout
     *
     * @var    object
     * @access public
     */
    public $rightCol;
    
    /**
     * middle column layout
     *
     * @var    object
     * @access public
     */
    public $middleCol;
    
    /**
     * Template header
     *
     * @var    object
     * @access public
     */
    public $tplHeader;
    
    /**
     * CSS Layout
     *
     * @var    object
     * @access public
     */
    public $cssLayout;
    
    /**
     * Left user menu
     *
     * @var    object
     * @access public
     */
    public $leftMenu;
    
    /**
     * user object
     *
     * @var    object
     * @access public
     */
    public $objUser;
    
    /**
     * Standard init function
     *
     * Initialises and constructs the object via the framework
     *
     * @return void
     * @access public
     */
    public function init()
    {
        $this->objArtdirCategories = $this->getObject('artdircategories');
        // user class
        $this->objUser = $this->getObject('user', 'security');
        // load up the htmlelements
        $this->loadClass('href', 'htmlelements');
        // get the css layout object
        $this->cssLayout = $this->newObject('csslayout', 'htmlelements');
        // get the sidebar object
        $this->leftMenu = $this->newObject('usermenu', 'toolbar');
        // initialise the columns
        // left column
        $this->leftCol = NULL;
        // right column
        $this->rightCol = NULL;
        // middle column
        $this->middleCol = NULL;
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objDbArtdir      = $this->getObject('dbartdir');
        $this->objFile = $this->getObject('dbfile', 'filemanager');
    }
    
    /**
     * three col layout
     *
     * Creates a 3 column css layout
     *
     * @return object CSS layout template header
     * @access public
     */
    public function threeCols()
    {
        // Set columns to 3
        $this->cssLayout->setNumColumns(3);
        $this->tplHeader = $this->cssLayout;
        return $this->tplHeader;
    }
    
    /**
     * Left blocks
     *
     * Blocks that will show up in the left hand column
     *
     * @param integer $userid The User id
     * @param string  $cats   The categories menu
     *
     * @return string  Return string
     * @access public
     */
    public function leftBlocks($userid = NULL, $cats = NULL)
    {
        $leftCol = "left";
        return NULL; 
    }
    
    /**
     * Right side blocks
     *
     * CSS layout for the right hand side blocks
     *
     * @param integer $userid The user id
     * @param string  $cats   categories
     *
     * @return string  string of blocks
     * @access public
     */
    public function rightBlocks()
    {
        $rightCol = NULL;
        if($this->objUser->isLoggedIn()) {
            $rightCol .= $this->menu();
        }
        $rightCol .= '<div id="categoryfeatureboxhead"><img src="'.$this->objConfig->getskinRoot().'artdir/images/categories.png" alt="search directory"" /></div>';
        // Get top level cats then get sub cats
        $parentcats = $this->objDbArtdir->getParentCats();
        foreach($parentcats as $p) {
            $children = $this->objDbArtdir->getChildCats($p['id']);
            // var_dump($children);
            $link = new link ($this->uri(array('action'=>'viewbycat', 'cat' => $p['id'])));
            $link->link = $p['cat_nicename'];
            $rightCol .= '<div id="catTop"><a href=#>'.$link->show().'</a></div>';
            foreach($children as $c) {
                $link = new link ($this->uri(array('action'=>'viewbycat', 'cat' => $c['id'])));
                $link->link = $c['cat_nicename'];
                $rightCol .= '<div id="cats"><a href=#>'.$link->show().'</a></div>';
            }
            $rightCol .= '<hr />';
        }
        
        return $rightCol;
    }
    
    public function aboutUs() {
        $html = '<p>Looking for an artist for an event? Want to find everything from poets to
pianists, hip hop to hard rock? The Artist Directory is a pilot project by
PANSA and the City of Cape Town to bring South Africa\'\s artists to you: the
people that want to book them. Whether you\'\re an event planner, looking for
corporate entertainment, or just throwing someone the birthday party of a
lifetime: South Africa has the talent you need. We\'\re providing the
catalyst.</p>';
        
        return stripslashes($html);
    }
    
    private function menu() {
        $feat = $this->newObject('featurebox', 'navigation');
        $links = NULL;
        // check that a user has a profile, if not, give option to create one. Admin always can add new artists.
        if(!$this->objDbArtdir->checkforProfile($this->objUser->userId()) || $this->objUser->inAdminGroup($this->objUser->userId())) {
            $addprflink = new link ($this->uri(array('action'=>'artdiradmin', 'mode' => 'addartist')));
            $addprflink->link = $this->objLanguage->languageText("mod_artdir_addprofile", "artdir");
            $links .= $addprflink->show();
        }
        
        return $feat->show($this->objLanguage->languageText("mod_artdir_usermenu", "artdir"), $links);
    }
    
    public function slider() {
        $path = $this->objConfig->getskinRoot().'artdir/images/slider/';
        $html = '<div class="main_view">
                     <div class="window">
                         <div class="image_reel">
                             <a href="#"><img src="'.$path.'homepage_image_circus.jpg" alt="" /></a>
                             <a href="#"><img src="'.$path.'homepage_image_dance.jpg" alt="" /></a>
                             <a href="#"><img src="'.$path.'homepage_image_dj.jpg" alt="" /></a>
                             <a href="#"><img src="'.$path.'homepage_image_mc.jpg" alt="" /></a>
                             <a href="#"><img src="'.$path.'homepage_image_music.jpg" alt="" /></a>
                             <a href="#"><img src="'.$path.'homepage_image_theatre.jpg" alt="" /></a>
                             <a href="#"><img src="'.$path.'homepage_image_theatre.jpg" alt="" /></a>
                         </div>
                     </div>
                     <div class="paging">
                         <a href="#" rel="1">1</a>
                         <a href="#" rel="2">2</a>
                         <a href="#" rel="3">3</a>
                         <a href="#" rel="4">4</a>
                         <a href="#" rel="5">5</a>
                         <a href="#" rel="6">6</a>
                         
                     </div>
                 </div>';
        
        $js = '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>';
        $js .= '<script type="text/javascript">
                $(document).ready(function() {
	                //Show the paging and activate its first link
                    $(".paging").show(); 
                    $(".paging a:first").addClass("active");

                    //Get size of the image, how many images there are, then determin the size of the image reel.
                    var imageWidth = $(".window").width();
                    var imageSum = $(".image_reel img").size(); 
                    var imageReelWidth = imageWidth * imageSum;

                    //Adjust the image reel to its new size
                    $(".image_reel").css({\'width\' : imageReelWidth});
                    //Paging  and Slider Function
                    rotate = function(){
                    var triggerID = $active.attr("rel") - 1; //Get number of times to slide
                    var image_reelPosition = triggerID * imageWidth; //Determines the distance the image reel needs to slide

                    $(".paging a").removeClass(\'active\'); //Remove all active class
                    $active.addClass(\'active\'); //Add active class (the $active is declared in the rotateSwitch function)

                    //Slider Animation
                    $(".image_reel").animate({
                        left: -image_reelPosition
                    }, 500 );

                    }; 

                    //Rotation  and Timing Event
                    rotateSwitch = function(){
                        play = setInterval(function(){ //Set timer - this will repeat itself every 7 seconds
                        $active = $(\'.paging a.active\').next(); //Move to the next paging
                        if ( $active.length === 0) { //If paging reaches the end...
                            $active = $(\'.paging a:first\'); //go back to first
                        }
                        rotate(); //Trigger the paging and slider function
                        }, 7000); //Timer speed in milliseconds (7 seconds)
                    };

                    rotateSwitch(); //Run function on launch
                    
                    //On Hover
                    $(".image_reel a").hover(function() {
                        clearInterval(play); //Stop the rotation
                    }, function() {
                        rotateSwitch(); //Resume rotation timer
                    });	

                    //On Click
                    $(".paging a").click(function() {
                        $active = $(this); //Activate the clicked paging
                        //Reset Timer
                        clearInterval(play); //Stop the rotation
                        rotate(); //Trigger rotation immediately
                        rotateSwitch(); // Resume rotation timer
                        return false; //Prevent browser jump to link anchor
                     });
                });</script>';
                
                return $js.$html;
    }
    
    public function directorySearch($compact = TRUE) {
        // Load the form building classes.
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('form','htmlelements');
        $this->loadClass('button','htmlelements');
        
        $slabel = new label($this->objLanguage->languageText('mod_artdir_dirsearch', 'search', 'Directory Search') .':', 'input_search');
        $sform = new form('query', $this->uri(array('action' => 'search'),'artdir'));
        //$sform->addRule('searchterm', $this->objLanguage->languageText("mod_blog_phrase_searchtermreq", "blog") , 'required');
        $query = new textinput('search');
        $query->size = 1;
        $objSButton = new button($this->objLanguage->languageText('word_go', 'system'));
        // Add the search icon
        $objSButton->setIconClass("search");
        //$this->objSButton->setValue($this->objLanguage->languageText('mod_skin_find', 'skin'));
        $objSButton->setValue('Find');
        $objSButton->setToSubmit();
        if ($compact) {
            $sform->addToForm($query->show()." ".$objSButton->show());
        } else {
            $sform->addToForm($slabel->show().' '.$query->show().' '.$objSButton->show());
        }
        $sform = '<div id="dirsearch">'.$sform->show().'</div>';
        return $sform;
    }
    
    public function signinBox() {
        $ret = $this->showSignInBox();
        // $ret .= $this->showSignUpBox();
        return $ret;
    }
    
    /**
     * Sign in block
     *
     * Used in conjunction with the welcome block as a alertbox link. The sign in simply displays the block to sign in to Chisimba
     *
     * @return string
     */
    public function showSignInBox($featurebox = FALSE) {
        $objBlocks = $this->getObject('blocks', 'blocks');
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        if($featurebox == TRUE) {
            return $objFeatureBox->show($this->objLanguage->languageText("mod_artdir_signin", "artdir"), $objBlocks->showBlock('login', 'security', 'none'));
        }
        else {
            return $objBlocks->showBlock('login', 'security', 'none');
        }
    }

    /**
     * Sign up block
     *
     * Method to generate a sign up (register) block for the module. It uses a linked alertbox to format the response
     *
     * @return string
     */
    public function showSignUpBox() {
        $objBlocks = $this->getObject('blocks', 'blocks');
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        return $objFeatureBox->show($this->objLanguage->languageText("mod_artdir_signup", "artdir"), $objBlocks->showBlock('register', 'security', 'none'));
    }
    
    public function getSocial() {
        // fb code
        $fbadmin = $this->objSysConfig->getValue('fbadminsid', 'facebookapps');
        $fbapid = $this->objSysConfig->getValue('apid', 'facebookapps');
        $oghead = '<meta property="fb:admins" content="'.$fbadmin.'"/>
                   <meta property="fb:app_id" content="'.$fbapid.'" />
	               <meta property="og:type" content="website" />		
                   <meta property="og:title" content="'.$this->objConfig->getSiteName().'" />    	
                   <meta property="og:url" content="'.$this->uri('').'" />';
        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $oghead);
        
        $js = NULL;
        $js .= $this->getFbCode();
        $js .= $this->tweetButton();
        $js .= $this->getPlusOneButton();
        return '<div id="socialbuttons">'.$js.'</div>';
    }
    
    public function getFbCode() {
        $fbapid = $this->objSysConfig->getValue('apid', 'facebookapps');
        $fb = "<div id=\"fb-root\"></div>
               <script>
                   window.fbAsyncInit = function() {
                       FB.init({appId: '$fbapid', status: false, cookie: true,
                       xfbml: true});
                   };
                   (function() {
                       var e = document.createElement('script'); e.async = true;
                       e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
                       document.getElementById('fb-root').appendChild(e);
                   }());
             </script>
             <fb:like action='like' colorscheme='light' layout='button_count' show_faces='false' width='90'/>";
        return $fb;
    }
    
    public function tweetButton() {
        $tweet = '<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
                  <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
        return $tweet;
    }
    
    public function getPlusOneButton() {
        return '<g:plusone></g:plusone>';
    }
    
    public function getFeaturedArtists() {
        $fart = NULL;
        $fart .= '<div class="artistwindow">';
        $artist = $this->objDbArtdir->getRandArtists();
        
        $count = 0;
        foreach($artist as $a) {
            if($count == 0) {
                $artistcontainer = 'artistleft';
            }
            elseif($count == 1) {
                $artistcontainer = 'artistmiddle';
            }
            elseif($count == 2) {
                $artistcontainer = 'artistright';
            }
            $artist = '<div id="'.$artistcontainer.'">'.'<img src="'.$this->objFile->getFilePath($a['thumbnail']).'" width="229" height="180" />'.
                         '<h3>'.$a['actname'].'</h3>'.$a['description'].'</div>';
            $artlink = new link ($this->uri(array('action'=>'viewartist', 'id' => $a['id'])));
            $artlink->link = $artist;
            $fart .= $artlink->show();
            $count++;
        }
        
        $fart .= '</div>';
        return $fart;
    }
    
    public function formatArtist($artist) {
        if($this->objUser->inAdminGroup($this->objUser->userId()) || $this->objUser->userId() == $artist['userid']) {
            // get the edit and delete icon for this artist
            $this->objIcon = &$this->getObject('geticon', 'htmlelements');
            $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                'action' => 'editartist',
                'id' => $artist['id'],
                'module' => 'artdir'
            )));
            $delIcon = $this->objIcon->getDeleteIconWithConfirm($artist['id'], array(
                'module' => 'artdir',
                'action' => 'deleteartist',
                'id' => $artist['id']
            ) , 'artdir');
            $ed = $edIcon;
            $del = $delIcon;
        }
        else {
            $ed = NULL;
            $del = NULL;
        }
        
        $this->objWashout = $this->getObject("washout", "utilities");
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        
        $str = NULL;
        
        // details table inside a container table
        $ctable = $this->newObject('htmltable', 'htmlelements');
        $ctable->cellpadding = 3;
        
        // details table
        $dtable = $this->newObject('htmltable', 'htmlelements');
        $dtable->cellpadding = 3;
        // build the artist details now
        $dtable->startRow();
        // categories
        $catlabel = new label($this->objLanguage->languageText('mod_artdir_category', 'artdir'));
        $dtable->addCell($catlabel->show());
        $dtable->addCell($this->objDbArtdir->getCatById($artist['catid']));
        $dtable->endRow();
        // contact person
        $dtable->startRow();
        $conlabel = new label($this->objLanguage->languageText('mod_artdir_contactp', 'artdir'));
        $dtable->addCell($conlabel->show());
        $dtable->addCell($artist['contactperson']);
        $dtable->endRow();
        // telephone
        $dtable->startRow();
        $contlabel = new label($this->objLanguage->languageText('mod_artdir_contactnum', 'artdir'));
        $dtable->addCell($contlabel->show());
        $dtable->addCell($artist['contactnum']);
        $dtable->endRow();
        // telephone 2
        $dtable->startRow();
        $altlabel = new label($this->objLanguage->languageText('mod_artdir_altnum', 'artdir'));
        $dtable->addCell($altlabel->show());
        $dtable->addCell($artist['altnum']);
        $dtable->endRow();
        // email
        $dtable->startRow();
        $emaillabel = new label($this->objLanguage->languageText('mod_artdir_email', 'artdir'));
        $dtable->addCell($emaillabel->show());
        $dtable->addCell($this->objWashout->parseText($artist['email']));
        $dtable->endRow();
        // website
        $dtable->startRow();
        $weblabel = new label($this->objLanguage->languageText('mod_artdir_website', 'artdir'));
        $dtable->addCell($weblabel->show());
        $dtable->addCell('<a href="'.$artist['website'].'" title="'.$artist['website'].'" target="_blank">'.$artist['website'].'</a>'); // $this->objWashout->parseText($artist['website']));
        $dtable->endRow();
        // book this artist
        $dtable->startRow();
        $abox = $this->newObject('alertbox', 'htmlelements');
        $dtable->addCell($abox->show($this->objLanguage->languageText("mod_artdir_bookartist", "artdir"), $this->uri(array('action' => 'bookingform', 'id' => $artist['id']))));
        $dtable->endRow();
        
        // 1 row, 2 cells
        $ctable->startRow();
        // artist pic
        $ctable->addCell($ed.$del.'<br /><img src="'.$this->objFile->getFilePath($artist['thumbnail']).'" width="229" height="180" />');
        // artis details table
        $ctable->addCell($dtable->show());
        $ctable->endRow();
        
        $str .= $ctable->show();
        $str .= "<br />";
        $str .= $artist['bio'];
        $str .= "<hr />";
        
        // grab any images associated with the profile
        $picslabel = new label($this->objLanguage->languageText('mod_artdir_pics', 'artdir'));
        $str .= $picslabel->show()."<br />";
        $str .= $this->artistgal($artist['id']); //imggalJs($artist['id']);
        // get any links
        $linkslabel = new label($this->objLanguage->languageText('mod_artdir_links', 'artdir'));
        $str .= $linkslabel->show();
        $str .= $this->artistLinks($artist['id']);
        
        return $str;
    }
    
    public function artistEditor($artistid = NULL, $edit = FALSE) {
        // var_dump($artist);
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        if($artistid != NULL) {
            // get the artist info
            $artist = $this->objDbArtdir->getArtistById($artistid);
            $artform = new form('artistedit', $this->uri(array(
                'action' => 'artistedit', 'id' => $artist['id']
            )));
        }
        else {
            $artform = new form('artistadd', $this->uri(array(
                'action' => 'newartist',
            )));
            $artist = array();
        }
        $artadd = $this->newObject('htmltable', 'htmlelements');
        $artadd->cellpadding = 3;
        
        $artadd->startRow();
        $cats = $this->objDbArtdir->getParentCats();
        $catdrop = new dropdown('cat');
        $topcats = $this->objDbArtdir->getParentCats();
        foreach($topcats as $t) {
            $catdrop->addOption($t['id'], "<em>".$t['cat_name']."</em>");
            $subcats = $this->objDbArtdir->getChildCats($t['id']);
            foreach($subcats as $s) {
                $catdrop->addOption($s['id'], "  -".$s['cat_name']);
            }
        }
        $catdrop->setSelected($artist['catid']);
        
        $clabel = new label($this->objLanguage->languageText('mod_artdir_catname', 'artdir') . ':', 'input_cat');
        $artadd->addCell($clabel->show());
        $artadd->addCell($catdrop->show());
        $artadd->endRow();
        
        // act name field
        $artadd->startRow();
        $actnamelabel = new label($this->objLanguage->languageText('mod_artdir_actname', 'artdir') . ':', 'input_actname');
        $actname = new textinput('actname');
        if($edit == TRUE) {
            $actname->setValue($artist['actname']);
        }
        $artadd->addCell($actnamelabel->show());
        $artadd->addCell($actname->show());
        $artadd->endRow();
        
        // description field
        $artadd->startRow();
        $desclabel = new label($this->objLanguage->languageText('mod_artdir_descrip', 'artdir') . ':', 'input_desc');
        $desc = new textarea('desc');
        if($edit == TRUE) {
            $desc->setValue($artist['description']);
        }
        $artadd->addCell($desclabel->show());
        $artadd->addCell($desc->show());
        $artadd->endRow();
        
        // contactperson field
        $artadd->startRow();
        $cplabel = new label($this->objLanguage->languageText('mod_artdir_contactp', 'artdir') . ':', 'input_contactperson');
        $cp = new textinput('contactperson');
        if($edit == TRUE) {
            $cp->setValue($artist['contactperson']);
        }
        $artadd->addCell($cplabel->show());
        $artadd->addCell($cp->show());
        $artadd->endRow();
        
        // contactnum field
        $artadd->startRow();
        $cnlabel = new label($this->objLanguage->languageText('mod_artdir_contactnum', 'artdir') . ':', 'input_contactnum');
        $cn = new textinput('contactnum');
        if($edit == TRUE) {
            $cn->setValue($artist['contactnum']);
        }
        $artadd->addCell($cnlabel->show());
        $artadd->addCell($cn->show());
        $artadd->endRow();
        
        // altnum field
        $artadd->startRow();
        $anlabel = new label($this->objLanguage->languageText('mod_artdir_altnum', 'artdir') . ':', 'input_altnum');
        $an = new textinput('altnum');
        if($edit == TRUE) {
            $an->setValue($artist['altnum']);
        }
        $artadd->addCell($anlabel->show());
        $artadd->addCell($an->show());
        $artadd->endRow();
        
        // email field
        $artadd->startRow();
        $emaillabel = new label($this->objLanguage->languageText('mod_artdir_email', 'artdir') . ':', 'input_email');
        $email = new textinput('email');
        if($edit == TRUE) {
            $email->setValue($artist['email']);
        }
        $artadd->addCell($emaillabel->show());
        $artadd->addCell($email->show());
        $artadd->endRow();
        
        // website field
        $artadd->startRow();
        $weblabel = new label($this->objLanguage->languageText('mod_artdir_website', 'artdir') . ':', 'input_website');
        $web = new textinput('website');
        if($edit == TRUE) {
            $web->setValue($artist['website']);
        }
        $artadd->addCell($weblabel->show());
        $artadd->addCell($web->show());
        $artadd->endRow();
        
        //bio
        $artadd->startRow();
        $biolabel = new label($this->objLanguage->languageText('mod_artdir_bio', 'artdir') . ':', 'input_bio');
        $bio = $this->newObject('htmlarea', 'htmlelements');
        $bio->setName('bio');
        $bio->height = 200;
        $bio->width = '100%';
        $bio->setBasicToolBar();
        if ($edit = TRUE) {
            $bio->setcontent((stripslashes(($artist['bio']))));
        }
        $artadd->addCell($biolabel->show());
        $artadd->addCell($bio->show());
        $artadd->endRow();
        
        // image
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
        $objSelectFile->name = 'thumb';
        $objSelectFile->restrictFileList = array('png', 'jpg', 'gif', 'PNG', 'JPG', 'GIF');
        $tlabel = new label($this->objLanguage->languageText('mod_artdir_thumbnail', 'artdir') . ':', 'input_thumb');
        $artadd->startRow();
        $artadd->addCell($tlabel->show());
        $artadd->addCell($objSelectFile->show());
        $artadd->endRow();
        
        
        $afieldset = $this->getObject('fieldset', 'htmlelements');
        $afieldset->setLegend($this->objLanguage->languageText('mod_artdir_artistdetails', 'artdir'));
        
        $afieldset->addContent($artadd->show());
        $artform->addToForm($afieldset->show());
        
        $this->objCButton = new button($this->objLanguage->languageText('word_update', 'system'));
        $this->objCButton->setIconClass("save");
        $this->objCButton->setValue($this->objLanguage->languageText('word_update', 'system'));
        $this->objCButton->setToSubmit();
        $artform->addToForm($this->objCButton->show());
        $artform = $artform->show();
        return $artform;
    }
    
    public function bookingForm($id) {
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $bookform = new form('bookartist', $this->uri(array(
            'action' => 'bookartist', 'artistid' => $id,
        )));
        $book = $this->newObject('htmltable', 'htmlelements');
        $book->cellpadding = 3;
        
        // your name
        $book->startRow();
        $yournamelabel = new label($this->objLanguage->languageText('mod_artdir_yourname', 'artdir') . ':', 'input_yourname');
        $yourname = new textinput('yourname');
        $book->addCell($yournamelabel->show());
        $book->addCell($yourname->show());
        $book->endRow();
        
        // your email
        $book->startRow();
        $youremaillabel = new label($this->objLanguage->languageText('mod_artdir_youremail', 'artdir') . ':', 'input_youremail');
        $youremail = new textinput('youremail');
        $book->addCell($youremaillabel->show());
        $book->addCell($youremail->show());
        $book->endRow();
        
        // contact
        $book->startRow();
        $yournumlabel = new label($this->objLanguage->languageText('mod_artdir_yournum', 'artdir') . ':', 'input_yournum');
        $yournum = new textinput('yournum');
        $book->addCell($yournumlabel->show());
        $book->addCell($yournum->show());
        $book->endRow();
        
        // booking request
        $book->startRow();
        $reqlabel = new label($this->objLanguage->languageText('mod_artdir_bookingdetails', 'artdir') . ':', 'input_request');
        $req = new textarea('request');
        $book->addCell($reqlabel->show());
        $book->addCell($req->show());
        $book->endRow();
        
        $bfieldset = $this->getObject('fieldset', 'htmlelements');
        $bfieldset->setLegend($this->objLanguage->languageText('mod_artdir_bookartist', 'artdir'));
        
        $bfieldset->addContent($book->show());
        $bookform->addToForm($bfieldset->show());
        
        $this->objBButton = new button($this->objLanguage->languageText('word_sendrequest', 'artdir'));
        $this->objBButton->setIconClass("save");
        $this->objBButton->setValue($this->objLanguage->languageText('word_sendrequest', 'artdir'));
        $this->objBButton->setToSubmit();
        $bookform->addToForm($this->objBButton->show());
        $bookform = $bookform->show();
        return $bookform;
    }
    
    public function artistAddForm() {
        // var_dump($artist);
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $artform = new form('artistadd', $this->uri(array(
            'action' => 'newartist',
        )));
        $artadd = $this->newObject('htmltable', 'htmlelements');
        $artadd->cellpadding = 3;
        
        $artadd->startRow();
        $cats = $this->objDbArtdir->getParentCats();
        $catdrop = new dropdown('cat');
        $topcats = $this->objDbArtdir->getParentCats();
        foreach($topcats as $t) {
            $catdrop->addOption($t['id'], "<em>".$t['cat_name']."</em>");
            $subcats = $this->objDbArtdir->getChildCats($t['id']);
            foreach($subcats as $s) {
                $catdrop->addOption($s['id'], "  -".$s['cat_name']);
            }
        }
        
        $clabel = new label($this->objLanguage->languageText('mod_artdir_catname', 'artdir') . ':', 'input_cat');
        $artadd->addCell($clabel->show());
        $artadd->addCell($catdrop->show());
        $artadd->endRow();
        
        // act name field
        $artadd->startRow();
        $actnamelabel = new label($this->objLanguage->languageText('mod_artdir_actname', 'artdir') . ':', 'input_actname');
        $actname = new textinput('actname');
        $artadd->addCell($actnamelabel->show());
        $artadd->addCell($actname->show());
        $artadd->endRow();
        
        // description field
        $artadd->startRow();
        $desclabel = new label($this->objLanguage->languageText('mod_artdir_descrip', 'artdir') . ':', 'input_desc');
        $desc = new textarea('desc');
        $artadd->addCell($desclabel->show());
        $artadd->addCell($desc->show());
        $artadd->endRow();
        
        // contactperson field
        $artadd->startRow();
        $cplabel = new label($this->objLanguage->languageText('mod_artdir_contactp', 'artdir') . ':', 'input_contactperson');
        $cp = new textinput('contactperson');
        $artadd->addCell($cplabel->show());
        $artadd->addCell($cp->show());
        $artadd->endRow();
        
        // contactnum field
        $artadd->startRow();
        $cnlabel = new label($this->objLanguage->languageText('mod_artdir_contactnum', 'artdir') . ':', 'input_contactnum');
        $cn = new textinput('contactnum');
        $artadd->addCell($cnlabel->show());
        $artadd->addCell($cn->show());
        $artadd->endRow();
        
        // altnum field
        $artadd->startRow();
        $anlabel = new label($this->objLanguage->languageText('mod_artdir_altnum', 'artdir') . ':', 'input_altnum');
        $an = new textinput('altnum');
        $artadd->addCell($anlabel->show());
        $artadd->addCell($an->show());
        $artadd->endRow();
        
        // email field
        $artadd->startRow();
        $emaillabel = new label($this->objLanguage->languageText('mod_artdir_email', 'artdir') . ':', 'input_email');
        $email = new textinput('email');
        $artadd->addCell($emaillabel->show());
        $artadd->addCell($email->show());
        $artadd->endRow();
        
        // website field
        $artadd->startRow();
        $weblabel = new label($this->objLanguage->languageText('mod_artdir_website', 'artdir') . ':', 'input_website');
        $web = new textinput('website');
        $artadd->addCell($weblabel->show());
        $artadd->addCell($web->show());
        $artadd->endRow();
        
        //bio
        $artadd->startRow();
        $biolabel = new label($this->objLanguage->languageText('mod_artdir_bio', 'artdir') . ':', 'input_bio');
        $bio = $this->newObject('htmlarea', 'htmlelements');
        $bio->setName('bio');
        $bio->height = 200;
        $bio->width = '100%';
        $bio->setBasicToolBar();
        $artadd->addCell($biolabel->show());
        $artadd->addCell($bio->show());
        $artadd->endRow();
        
        // image
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
        $objSelectFile->name = 'thumb';
        $objSelectFile->restrictFileList = array('png', 'jpg', 'gif', 'PNG', 'JPG', 'GIF');
        $tlabel = new label($this->objLanguage->languageText('mod_artdir_thumbnail', 'artdir') . ':', 'input_thumb');
        $artadd->startRow();
        $artadd->addCell($tlabel->show());
        $artadd->addCell($objSelectFile->show());
        $artadd->endRow();
        
        
        $afieldset = $this->getObject('fieldset', 'htmlelements');
        $afieldset->setLegend($this->objLanguage->languageText('mod_artdir_artistdetails', 'artdir'));
        
        $afieldset->addContent($artadd->show());
        $artform->addToForm($afieldset->show());
        
        $this->objCButton = new button($this->objLanguage->languageText('word_add', 'system'));
        $this->objCButton->setIconClass("save");
        $this->objCButton->setValue($this->objLanguage->languageText('word_add', 'system'));
        $this->objCButton->setToSubmit();
        $artform->addToForm($this->objCButton->show());
        $artform = $artform->show();
        return $artform;
    }
    
    public function imageUpload($artistid) {
        $artist1 = $this->objDbArtdir->getArtistById($artistid);
        //var_dump($artist1);
        //$artist1 = NULL;
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        
        $picform = new form('imgup', $this->uri(array(
                'action' => 'imgup', 'id' => $artist1['id'], 'catid' => $artist1['catid'],
            )));
        
        $picadd = $this->newObject('htmltable', 'htmlelements');
        $picadd->cellpadding = 3;
        
        $objSelectFile1 = $this->newObject('selectfile', 'filemanager');
        $objSelectFile1->name = 'pic1';
        $objSelectFile1->restrictFileList = array('png', 'jpg', 'gif', 'PNG', 'JPG', 'GIF');
        
        $objSelectFile2 = $this->newObject('selectfile', 'filemanager');
        $objSelectFile2->name = 'pic2';
        $objSelectFile2->restrictFileList = array('png', 'jpg', 'gif', 'PNG', 'JPG', 'GIF');
        
        $objSelectFile3 = $this->newObject('selectfile', 'filemanager');
        $objSelectFile3->name = 'pic3';
        $objSelectFile3->restrictFileList = array('png', 'jpg', 'gif', 'PNG', 'JPG', 'GIF');
        
        $p1label = new label($this->objLanguage->languageText('mod_artdir_pic', 'artdir') . ':', 'input_pic1');
        $picadd->startRow();
        $picadd->addCell($p1label->show());
        $picadd->addCell($objSelectFile1->show());
        $picadd->endRow();
        //pic 2
        $picadd->startRow();
        $picadd->addCell($p1label->show());
        $picadd->addCell($objSelectFile2->show());
        $picadd->endRow();
        //pic 3
        $picadd->startRow();
        $picadd->addCell($p1label->show());
        $picadd->addCell($objSelectFile3->show());
        $picadd->endRow();
        
        $imgfieldset = $this->newObject('fieldset', 'htmlelements');
        $imgfieldset->setLegend($this->objLanguage->languageText('mod_artdir_images', 'artdir'));
        
        $imgfieldset->addContent($picadd->show());
        $picform->addToForm($imgfieldset->show());
        
        $this->objUButton = new button($this->objLanguage->languageText('word_update', 'system'));
        $this->objUButton->setIconClass("save");
        $this->objUButton->setValue($this->objLanguage->languageText('word_update', 'system'));
        $this->objUButton->setToSubmit();
        $picform->addToForm($this->objUButton->show());
        $picform = $picform->show();
        return $picform;
    }
    
    public function returnlink() {
        $retlink = new link ($this->uri(array(''), 'artdir'));
        $retlink->link = $this->objLanguage->languageText("mod_artdir_return", "artdir");
        return $retlink->show();
    }
    
    public function formatArtistRecords($recs) {
        if(empty($recs)) {
            return $this->objLanguage->languageText("mod_artdir_noactsfound", "artdir");
        }
        $str = NULL;
        
        foreach($recs as $rec) {
            $objFB = $this->newObject('featurebox', 'navigation');
            $this->objWashout = $this->getObject("washout", "utilities");
            $this->loadClass('href', 'htmlelements');
            $this->loadClass('label', 'htmlelements');
        
            $artist = $rec;
        
            // details table inside a container table
            $ctable = $this->newObject('htmltable', 'htmlelements');
            $ctable->cellpadding = 3;
        
            // details table
            $dtable = $this->newObject('htmltable', 'htmlelements');
            $dtable->cellpadding = 3;
            // build the artist details now
            $dtable->startRow();
            // categories
            $catlabel = new label($this->objLanguage->languageText('mod_artdir_category', 'artdir'));
            $dtable->addCell($catlabel->show());
            $dtable->addCell($this->objDbArtdir->getCatById($artist['catid']));
            $dtable->endRow();
            // contact person
            $dtable->startRow();
            $conlabel = new label($this->objLanguage->languageText('mod_artdir_contactp', 'artdir'));
            $dtable->addCell($conlabel->show());
            $dtable->addCell($artist['contactperson']);
            $dtable->endRow();
            // telephone
            $dtable->startRow();
            $contlabel = new label($this->objLanguage->languageText('mod_artdir_contactnum', 'artdir'));
            $dtable->addCell($contlabel->show());
            $dtable->addCell($artist['contactnum']);
            $dtable->endRow();
            // telephone 2
            $dtable->startRow();
            $altlabel = new label($this->objLanguage->languageText('mod_artdir_altnum', 'artdir'));
            $dtable->addCell($altlabel->show());
            $dtable->addCell($artist['altnum']);
            $dtable->endRow();
            // email
            $dtable->startRow();
            $emaillabel = new label($this->objLanguage->languageText('mod_artdir_email', 'artdir'));
            $dtable->addCell($emaillabel->show());
            $dtable->addCell($this->objWashout->parseText($artist['email']));
            $dtable->endRow();
            // website
            $dtable->startRow();
            $weblabel = new label($this->objLanguage->languageText('mod_artdir_website', 'artdir'));
            $dtable->addCell($weblabel->show());
            $dtable->addCell($this->objWashout->parseText($artist['website']));
            $dtable->endRow();
        
            // 1 row, 2 cells
            $ctable->startRow();
            // artist pic
            $prflink = new link ($this->uri(array('action' => 'viewartist', 'id' => $rec['id']), 'artdir'));
            $prflink->link = '<img src="'.$this->objFile->getFilePath($artist['thumbnail']).'" width="229" height="180" />';
            $prflink = $prflink->show();
            $ctable->addCell($prflink);
            // artis details table
            $ctable->addCell($dtable->show());
            $ctable->endRow();
        
            $str .= $objFB->show($rec['actname'], $ctable->show());
        
        }
        
        return $str;
    }
    
    public function artistgal($artistid) {
        $artistpics = $this->objDbArtdir->getArtistPics($artistid);
        $html = NULL;
        foreach($artistpics as $pic) {
            // Build the alertbox thingy
            $abox = $this->newObject('alertbox', 'htmlelements');
            $html .= $abox->show('<img src="'.$this->objFile->getFilePath($pic['picid']).'" width="150" height="79" title="'.$pic['id'].'" />', $this->uri(array('action' => 'viewpic', 'picid' => $pic['picid'])));
        }
        return $html."<br />";
    }
    
    public function imggalJs($artistid) {
        $artistpics = $this->objDbArtdir->getArtistPics($artistid);
        $js = NULL;
        $js .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>';
        $js .= $this->getJavascriptFile('jquery.exposure.js?v=0.9', 'artdir');
        $js .= $this->getJavascriptFile('imggal.js', 'artdir');
        $this->appendArrayVar('headerParams', $js);
        $html = NULL;
        $html .= '<div class="panel">	
				<div id="slideshow"></div>
				<div class="clear"></div>
				<ul id="images">';
	    foreach($artistpics as $pic) {
		    $html .= '<li><a href="'.$this->objFile->getFilePath($pic['picid']).'"><img src="'.$this->objFile->getFilePath($pic['picid']).'" width="150" height="79" title="'.$pic['id'].'" /></a></li>';
		}
		$html .='</ul>
				<div class="clear"></div>
			</div>			
			<div id="exposure"></div>			
			<div class="clear"></div>';
			
	    return $html;
    }
    
    public function artistLinks($artistid) {
        $links = $this->objDbArtdir->getAllLinks($artistid);
        if(!empty($links)) {
            $html = '<div class="artistlinks">
                         <ul>';
            foreach($links as $l) {
                $html .=         '<li><a href="'.$l['link'].'" target="_blank">'.$l['linkname'].'</a></li>';
            }
            $html .=    '</ul>
                 </div>';
            return $html;
        }
        else {
            return $this->objLanguage->languageText("mod_artdir_nolinks", "artdir");
        }
    }
    
    /**
     * Main container function (tabber) box to do the layout for the main template
     *
     * Chisimba tabber interface is used to create tabs that are dynamically switchable.
     *
     * @return string
     */
    public function profileContainer($artistid) {
        $tabs = $this->getObject('tabber', 'htmlelements');

        $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_artdir_profile", "artdir"), 'content' => $this->artistEditor($artistid), 'onclick' => ''));
        //$tabs->addTab(array('name' => $this->objLanguage->languageText("mod_artdir_images", "artdir"), 'content' => $this->imageUpload($artistid), 'onclick' => ''));
        // $tabs->addTab(array('name' => $this->objLanguage->languageText("mod_artdir_links", "artdir"), 'content' => $this->getRecentContent(), 'onclick' => ''));
        
        return $tabs->show();
    }
}
?>
