<?php
/**
 *
 * beatstream helper class
 *
 * PHP version 5.1.0+
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
 * @category  Chisimba
 * @package   beatstream
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * beatstream helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package beatstream
 *
 */
class beatui extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;
    
    /**
     * @var array $data Object property for holding the data
     *
     * @access public
     */
    public $data = array();

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        $this->objCC         = $this->getObject('displaylicense', 'creativecommons');
        $this->objOps        = $this->newObject('beatops');
        $this->objDbFeatures = $this->getObject('dbbeats');
        $this->objWashout    = $this->getObject('washout', 'utilities');
        $this->objPodcast    = $this->getObject('dbpodcast', 'podcast');
        $this->objDateTime   = $this->getObject('dateandtime', 'utilities');
        
        // htmlelements
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
    }
    
    /**
     * Method to return the required JS to display an @anywhere tweet box for CCSA
     *
     * @param string screen_name the username of the user to tweet for
     * @return string some js
     */
    public function featureTweetBox($screen_name) {
        $gourl = $this->uri(array('action' => 'tweetit', 'user' => $screen_name), 'featuresuggest');
        $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
        $gourl = str_replace("&amp;", "&", $gourl);
        $gourl = $this->teeny->create(urlencode($gourl));
        $msg = $this->objLanguage->languageText("mod_featuresuggest_msg", "featuresuggest")." ";
        
        $js = '<script type="text/javascript">
		
		    twttr.anywhere(function (T) {
		
		        T("#featuretweetbox").tweetBox({
		            height: 80,
		            width: 550,
		            defaultContent: "'.$msg.' '.$gourl.'",
		            label: "Tweet this!"
		        });
		
		    });
		
		</script>';
		
		return $js;
    }
    
    public function formatData($sug) {
        // convert the array to an object now
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('windowpop', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('formatfilesize', 'files');
        $objFile = $this->getObject('dbfile', 'filemanager');
        
        $str = NULL;
        $str = '<ul class="suggestions">';
        foreach($sug as $s) {
            $content = NULL;
            $filesize = new formatfilesize();
            $podcast = $this->objPodcast->getPodcast($s['suggestion']);

            $table = $this->newObject('htmltable', 'htmlelements');
            $table->startRow();
            if ($podcast['artist'] == '') {
                $artist = $this->objUser->fullname($podcast['creatorid']);
            } else {
                $artist = $podcast['artist'];
            }
            if (isset($id)) {  
                $table->addCell('<strong>'.$this->objLanguage->languageText('mod_beatstream_artist', 'beatstream').':</strong> '.$artist, '50%');
            } else {
                $authorLink = new link ($this->uri(array('action'=>'byuser', 'id'=>$this->objUser->userName($podcast['creatorid']))));
                $authorLink->link = $artist;
                $table->addCell('<strong>'.$this->objLanguage->languageText('mod_beatstream_artist', 'beatstream').':</strong> '.$authorLink->show(), '50%');
            }
            $table->addCell('<strong>'.$this->objLanguage->languageText('word_date', 'system').':</strong> '.$this->objDateTime->formatDate($podcast['datecreated']), '50%');
            // only add in a delete button if the user is logged in and an admin
            if($this->objUser->inAdminGroup($this->objUser->userId()) && $this->objUser->isLoggedIn()) {
                $this->objDel = $this->newObject('geticon', 'htmlelements');
                $del = $this->objDel->getDeleteIcon($this->uri(array('action' => 'deletebeat', 'id' => $s['id'])));
                $table->addCell($del, '50%');
            }
            $table->endRow();
            
            $table->startRow();
            $table->addCell('<strong>'.$this->objLanguage->languageText('phrase_filesize', 'system').':</strong> '.$filesize->formatsize($podcast['filesize']), '50%');
            $playtime = $this->objDateTime->secondsToTime($podcast['playtime']);
            $playtime = ($playtime == '0:0') ? '<em>Unknown</em>' : $playtime;
            $table->addCell('<strong>'.$this->objLanguage->languageText('word_playtime', 'system').':</strong> '.$playtime, '50%');
            $table->endRow();
        
            $content .= $table->show();
        
            $downloadLink = new link ($this->objConfig->getcontentPath().$podcast['path']);
            $downloadLink->link = htmlentities($podcast['filename']);
        
            $this->objPop=&new windowpop;
            $this->objPop->set('location',$this->uri(array('action'=>'playpodcast', 'id'=>$podcast['id']), 'podcast'));
            $this->objPop->set('linktext', $this->objLanguage->languageText('mod_beatstream_listenonline', 'beatstream'));
            $this->objPop->set('width','280');
            $this->objPop->set('height','120');
            //leave the rest at default values
            //$this->objPop->putJs(); // you only need to do this once per page
        
            $objSoundPlayer = $this->newObject('buildsoundplayer', 'files');
            $soundFile = str_replace('&', '&amp;', $objFile->getFilePath($podcast['fileid']));
            $soundFile = str_replace(' ', '%20', $soundFile);
            $objSoundPlayer->setSoundFile($soundFile);
            
            $url = $this->uri(array('action' => 'viewsingle', 'id' => $s['id']));
            
            /* share thing
            $objShare = $this->getObject('share', 'toolbar');
            $objShare->setup($url, htmlentities($podcast['filename']), 'Check out this beat! ');
            */
            // tweet button
            $this->objTweetButton = $this->getObject('tweetbutton', 'twitter');
            $related = $this->objSysConfig->getValue('retweet_related', 'beatstream');
            $status = $this->objSysConfig->getValue('retweet_status', 'beatstream');
            $style = $this->objSysConfig->getValue('retweet_style', 'beatstream');
            $text = $this->objSysConfig->getValue('retweet_text', 'beatstream');
            $type = $this->objSysConfig->getValue('retweet_type', 'beatstream');
            $via = $this->objSysConfig->getValue('retweet_via', 'beatstream');
            if($status == NULL){
                $status = "Check out this beat! ";
            }
            if($style == NULL) {
               $style = 'retweet vert';
            }
            if ($type == 'jquery') {
                $rt = $this->objJqTwitter->retweetCounter($url, $status, $style);
            } else {
                if (strpos($style, 'vert') !== FALSE) {
                    $style = 'vertical';
                }
                $rt = $this->objTweetButton->getButton($text, $style, $via, $related, htmlspecialchars_decode($url));
            }
            
            // Facebook like button
            $fbadmin = $this->objSysConfig->getValue('fbadminsid', 'facebookapps');
            $fbapid = $this->objSysConfig->getValue('apid', 'facebookapps');
            //$fburl = str_replace('http://', '', $url);
            $fburl = htmlspecialchars($this->objConfig->getsiteRoot().$url);
            $fb = "<fb:like href=\"$url\"
									   layout=\"button_count\"
									   show_faces=\"true\"
									   action=\"like\"
									   colorscheme=\"light\"></fb:like>";
            
            $soctable = $this->newObject('htmltable', 'htmlelements');
            $soctable->width = '80%';
            $soctable->cellspacing = '1';
            $soctable->cellpadding = '10';
            $soctable->startRow();
            $soctable->addCell($rt, '50%');
            $soctable->addCell($fb, '50%');
            $soctable->endRow();
            
            $content .= '<br /><p>'.$objSoundPlayer->show().'</p><p><strong>'.$this->objLanguage->languageText('mod_beatstream_downloadbeat', 'beatstream').':</strong> ('.$this->objLanguage->languageText('mod_podcast_rightclickandchoose', 'beatstream', 'Right Click, and choose Save As').') '.$downloadLink->show()." <br />".$soctable->show().'</p>';
            
            // pop it all back to the array for display purposes
            $s['suggestion'] = $content;
            $fb = NULL;
            $rt = NULL;
            $this->objOps->setData($s);
            $str .= (string)$this->objOps;
        }
        $str .='</ul>';
               
        return $this->objWashout->parseText($str);
    }
    
    public function formatUI() {
        // fb code
        $fbadmin = $this->objSysConfig->getValue('fbadminsid', 'facebookapps');
        $fbapid = $this->objSysConfig->getValue('apid', 'facebookapps');
        $oghead = '<meta property="fb:admins" content="'.$fbadmin.'"/>
                   <meta property="fb:app_id" content="'.$fbapid.'" />
	               <meta property="og:type" content="blog" />		
                   <meta property="og:title" content="'.$this->objConfig->getSiteName().'" />    	
                   <meta property="og:url" content="'.$this->uri('').'" />';
        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $oghead);
        
        $js = NULL;
        $js .= $this->getFbCode();
        $js .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
        $js .= $this->getJavascriptFile('script.js', 'beatstream');
        return $js;
    }
    
    public function getFbCode() {
        $fbapid = $this->objSysConfig->getValue('apid', 'facebookapps');
        $fb = "<div id=\"fb-root\"></div>
               <script>
                   window.fbAsyncInit = function() {
                       FB.init({appId: '$fbapid', status: true, cookie: true,
                       xfbml: true});
                   };
                   (function() {
                       var e = document.createElement('script'); e.async = true;
                       e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
                       document.getElementById('fb-root').appendChild(e);
                   }());
             </script>
             <fb:like action='like' colorscheme='light' layout='standard' show_faces='true' width='500'/>";
        return $fb;
    }
    
    public function addForm() {
        // load up a simple form 
        return $this->addBeat();
    }
    
    public function uploadBeat() {
        $objFileUpload = $this->getObject('uploadinput', 'filemanager');
        $objFileUpload->enableOverwriteIncrement = TRUE;
        $results = $objFileUpload->handleUpload('fileupload');
        // Technically, FALSE can never be returned, this is just a precaution
        // FALSE means there is no fileinput with that name
        if ($results == FALSE) {
            return FALSE;
        } elseif ($results['success']) {
                // add to db as podcast
                $podcastResult = $this->objPodcast->addPodcast($results['fileid']);
                // check result of adding as podcast
                if ($podcastResult == 'nofile') {
                    return FALSE;
                } else if ($podcastResult == 'fileusedalready') {
                    return TRUE;
                } else {
                    return TRUE;
                }
        } else {
            // If not successfully uploaded
            return FALSE;
        }
    }
    
    public function addBeat() {
        $ret = NULL;
        $this->loadClass('form', 'htmlelements');
        // $this->loadClass('textinput', 'htmlelements');
        $header = new htmlHeading();
        $header->str = $this->objLanguage->languageText('mod_beatstream_uploadnewfile', 'beatstream', 'Upload new beat');
        $header->type = 4;
        $ret .= $header->show();
        
        $form = new form('addbeatbyupload', $this->uri(array('action'=>'uploadbeat')));
        $form->extra = 'enctype="multipart/form-data"';
        $objUpload = $this->newObject('selectfile', 'filemanager');
        $objUpload->name = 'beatfile';
        $objUpload->restrictFileList = array('mp3');
        $button = new button('submitform', $this->objLanguage->languageText('mod_beatstream_uploadbeat', 'beatstream', 'Upload Beat'));
        $button->setToSubmit();;
        $form->addToForm($objUpload->show().'<br />'.$button->show());
        $ret .= $form->show();
        
        return $ret;
    }
    
    /**
     * Method to display the login box for prelogin blog operations
     *
     * @param  bool   $featurebox
     * @return string
     */
    public function loginBox($featurebox = FALSE)
    {
        $objBlocks = $this->getObject('blocks', 'blocks');
        if ($featurebox == FALSE) {
            return $objBlocks->showBlock('login', 'security') . "<br />" . $objBlocks->showBlock('register', 'security');
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            return $objFeatureBox->show($this->objLanguage->languageText("word_login", "system") , $objBlocks->showBlock('login', 'security', 'none')
              . "<br />" . $objBlocks->showBlock('register', 'security', 'none') );
        }
    }
}
?>
