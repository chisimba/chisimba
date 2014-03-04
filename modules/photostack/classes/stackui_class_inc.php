<?php
/**
 * photostack UI elements file.
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
 * @version    $Id:  $
 * @package    photostack
 * @subpackage photostackui
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
 * class to control photostack ui elements
 *
 * This class controls the photostack UI elements. 
 *
 * @category  Chisimba
 * @package   photostack
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2006-2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class stackui extends object
{   
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
        // user class
        $this->objUser = $this->getObject('user', 'security');
        // load up the htmlelements
        $this->loadClass('href', 'htmlelements');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objDbStack  = $this->getObject('dbstack');
        $this->objFile = $this->getObject('dbfile', 'filemanager');
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
        $js .= "<ul>";
        $js .= "<li>".$this->tweetButton()."</li>";
        $js .= "<li>".$this->getPlusOneButton()."</li>";
        $js .= "<li>".$this->getFbCode()."</li>";
        if($this->objUser->isLoggedIn()) {
            $this->loadClass('link', 'htmlelements');
            $link = new link ($this->uri(array('action'=>'createalbum'),'photostack'));
            $link->link=$this->objLanguage->languageText("mod_photostack_managealbums", "photostack");
            $js .= "<li>".$link->show()."</li>";
        }
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
    
    public function getGallery($userid) {
        $data = $this->objDbStack->getAlbums($userid);
        $userpath = $this->objConfig->getcontentPath().'users/'.$userid.'/albums/';
        $js = '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
        $js .= $this->getJavascriptFile('photostack.js');
        
        $html = '<div id="ps_slider" class="ps_slider">
			         <a class="prev disabled"></a>
			         <a class="next disabled"></a>
			         <div id="ps_albums">';
	    foreach($data as $d) {
	        $html .= '<div class="ps_album" style="opacity:0;" id="'.$d['puid'].'">
	                      <img src="'.$this->objFile->getFilePath($d['thumbnail']).'" width="135" height="90" alt="'.$d['puid'].'"/>
	                      <div class="ps_desc">
	                          <h2>'.$d['albumname'].'</h2>
	                          <span>'.$d['description'].'</span>
	                      </div>
	                  </div>';
	    }
	    
	    $html .= '</div></div>';
	    $html .= '<div id="ps_overlay" class="ps_overlay" style="display:none;"></div>
		<a id="ps_close" class="ps_close" style="display:none;"></a>
		<div id="ps_container" class="ps_container" style="display:none;">
			<a id="ps_next_photo" class="ps_next_photo" style="display:none;"></a>
		</div>';
		
        return $js.$html;
    }
    
    /**
     * Method to build and display the full scale album editor
     *
     * @param  integer $userid
     * @return string
     */
    public function albumEditor($userid, $mode = NULL, $albumarr = NULL) 
    {
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $cats = $this->objDbStack->getAlbums($userid);
        $headstr = $this->objLanguage->languageText("mod_photostack_albumedit_instructions", "photostack");
        $totcount = $this->objDbStack->imgCount(NULL);
        // create a table to view the categories
        $cattable = $this->newObject('htmltable', 'htmlelements');
        $cattable->cellpadding = 3;
        // set up the header row
        $cattable->startHeaderRow();
        $cattable->addHeaderCell($this->objLanguage->languageText("mod_photostack_name", "photostack"));
        $cattable->addHeaderCell($this->objLanguage->languageText("mod_photostack_albumdesc", "photostack"));
        $cattable->addHeaderCell($this->objLanguage->languageText("mod_photostack_thumbnail", "photostack"));
        $cattable->addHeaderCell($this->objLanguage->languageText("mod_photostack_editdeletealbum", "photostack"));
        $cattable->endHeaderRow();
        if (!empty($cats)) {
            foreach($cats as $rows) {
                // print_r($rows);
                // start the cats rows
                $cattable->startRow();
                $cattable->addCell($rows['albumname']);
                $cattable->addCell($rows['description']);
                $cattable->addCell('<img src="'.$this->objFile->getFilePath($rows['thumbnail']).'" width="135" height="90" alt="'.$rows['puid'].'"/>' );
                
                $this->objIcon = &$this->getObject('geticon', 'htmlelements');
                $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'managealbum',
                    'id' => $rows['id'],
                    'module' => 'photostack'
                )));
                $delIcon = $this->objIcon->getDeleteIconWithConfirm($rows['id'], array(
                    'module' => 'photostack',
                    'action' => 'deletealbum',
                    'id' => $rows['id']
                ) , 'photostack');
                $cattable->addCell($edIcon . $delIcon);
                $cattable->endRow();
            }
            $ctable = $headstr . $cattable->show();
        } else {
            $ctable = $this->objLanguage->languageText("mod_photostack_noalbums", "photostack");
        }
        
        // edit or add a category?
        if($mode == 'edit') {
            $catform = new form('albumedit', $this->uri(array(
                'action' => 'editalbum', 'id' => $albumarr['id']
            )));
        }
        else {
            // add a new category form:
            $catform = new form('albumadd', $this->uri(array(
                'action' => 'savealbum'
            )));
        }
        // $catform->addRule('catname', $this->objLanguage->languageText("mod_artdir_phrase_titlereq", "artdir") , 'required');
        $cfieldset = $this->getObject('fieldset', 'htmlelements');
        $cfieldset->setLegend($this->objLanguage->languageText('mod_photostack_albumdetails', 'photostack'));
        $catadd = $this->newObject('htmltable', 'htmlelements');
        $catadd->cellpadding = 5;
        // category name field
        $catadd->startRow();
        $clabel = new label($this->objLanguage->languageText('mod_photostack_albumname', 'photostack') . ':', 'input_albumname');
        $catname = new textinput('albumname');
        if($mode == 'edit') {
            $catname->setValue($albumarr['albumname']);
        }
        $catadd->addCell($clabel->show());
        $catadd->addCell($catname->show());
        $catadd->endRow();
        
        // start a htmlarea for the album description (optional)
        $catadd->startRow();
        $desclabel = new label($this->objLanguage->languageText('mod_photostack_albumdesc', 'photostack') . ':', 'input_desc');
        $this->loadClass('textarea', 'htmlelements');
        $cdesc = new textarea;
        // $this->newObject('textarea','htmlelements');
        $cdesc->setName('desc');
        if($mode == 'edit') {
            $cdesc->setValue($albumarr['description']);
        }
        // $cdesc->setBasicToolBar();
        $catadd->addCell($desclabel->show());
        $catadd->addCell($cdesc->show());
        // showFCKEditor());
        $catadd->endRow();
        
        // thumbnail upload
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
        $objSelectFile->name = 'thumb';
        $objSelectFile->restrictFileList = array('png', 'jpg', 'gif', 'PNG', 'JPG', 'GIF');
        $tlabel = new label($this->objLanguage->languageText('mod_photostack_thumbnail', 'photostack') . ':', 'input_thumb');
        $catadd->startRow();
        $catadd->addCell($tlabel->show());
        $catadd->addCell($objSelectFile->show());
        $catadd->endRow();
        
        // $catform->addRule('catname', $this->objLanguage->languageText("mod_artdir_phrase_titlereq", "artdir") , 'required');
        $cfieldset->addContent($catadd->show());
        $catform->addToForm($cfieldset->show());
        $this->objCButton = &new button($this->objLanguage->languageText('word_update', 'system'));
        $this->objCButton->setIconClass("save");
        $this->objCButton->setValue($this->objLanguage->languageText('word_update', 'system'));
        $this->objCButton->setToSubmit();
        $catform->addToForm($this->objCButton->show());
        $catform = $catform->show();
        return $ctable . "<br />" . $catform;
    }
    
    public function imageUpload($albumid) {
        $this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $imgform = new form('imageadd', $this->uri(array(
                'action' => 'addimage', 'albumid' => $albumid,
            )));
        
        $ifieldset = $this->getObject('fieldset', 'htmlelements');
        $ifieldset->setLegend($this->objLanguage->languageText('mod_photostack_albumimages', 'photostack'));
        $imgadd = $this->newObject('htmltable', 'htmlelements');
        $imgadd->cellpadding = 5;
        
        // image upload
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
        $objSelectFile->name = 'image';
        $objSelectFile->restrictFileList = array('png', 'jpg', 'gif', 'PNG', 'JPG', 'GIF');
        $flabel = new label($this->objLanguage->languageText('mod_photostack_image', 'photostack') . ':', 'input_image');
        $imgadd->startRow();
        $imgadd->addCell($flabel->show());
        $imgadd->addCell($objSelectFile->show());
        $imgadd->endRow();
        
        $ifieldset->addContent($imgadd->show());
        $imgform->addToForm($ifieldset->show());
        $this->objCButton = new button($this->objLanguage->languageText('word_save', 'system'));
        $this->objCButton->setIconClass("save");
        $this->objCButton->setValue($this->objLanguage->languageText('word_save', 'system'));
        $this->objCButton->setToSubmit();
        $imgform->addToForm($this->objCButton->show());
        $imgform = $imgform->show();
        return $imgform;
    }
}
?>
