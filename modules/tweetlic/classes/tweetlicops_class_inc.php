<?php
/**
 *
 * tweetlic helper class
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
 * @package   tweetlic
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
 * tweetlic helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package tweetlic
 *
 */
class tweetlicops extends object {

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
    public function ccTweetBox($screen_name) {
        $gourl = $this->uri(array('action' => 'viewlic', 'user' => $screen_name), 'tweetlic');
        $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
        $gourl = str_replace("&amp;", "&", $gourl);
        $gourl = $this->teeny->create(urlencode($gourl));
        $msg = $this->objLanguage->languageText("mod_tweetlic_msg", "tweetlic")." ";
        
        $js = '<script type="text/javascript">
		
		    twttr.anywhere(function (T) {
		
		        T("#cctweetbox").tweetBox({
		            height: 80,
		            width: 550,
		            defaultContent: "'.$msg.' '.$gourl.'",
		            label: "Tweet this!"
		        });
		
		    });
		
		</script>';
		
		return $js;
    }
    
    /**
     * Method to return the required JS to display an @anywhere invitationtweet box for CCSA
     *
     * @param string screen_name the username of the user to tweet for
     * @return string some js
     */
    public function ccTweetBoxInvite($screen_name) {
        $gourl = $this->uri(array('user' => $screen_name), 'tweetlic');
        $this->teeny = $this->getObject ( 'tiny', 'tinyurl');
        $gourl = str_replace("&amp;", "&", $gourl);
        $gourl = $this->teeny->create($gourl);
        $msg = $this->objLanguage->languageText("mod_tweetlic_invitemsg", "tweetlic")." ";
        
        $js = '<script type="text/javascript">
		
		    twttr.anywhere(function (T) {
		
		        T("#cctweetboxinv").tweetBox({
		            height: 80,
		            width: 550,
		            defaultContent: "@'.$screen_name.' '.$msg.' '.$gourl.'",
		            label: "Tweet this!"
		        });
		
		    });
		
		</script>';
		
		return $js;
    }
    
    /**
     * Method to return the form for people to fill out to license their tweets
     *
     * @param $edit NULL
     * @return string form data
     */
    public function licForm($edit = NULL) {
        // start the form
        $form = new form ('lic', $this->uri(array('action'=>'lictweet'), 'tweetlic'));
        // add some rules
        $form->addRule('screen_name', $this->objLanguage->languageText("mod_tweetlic_needscreenname", "tweetlic"), 'required');
        // screen name
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $screenname = new textinput('screen_name');
        if (isset($edit['screen_name'])) {
            $screenname->defaultValue = $edit['screen_name'];
        }
        $screennameLabel = new label($this->objLanguage->languageText('screenname', 'tweetlic').'&nbsp;', 'input_screen_name');
        $table->addCell($screennameLabel->show());
        $table->addCell('&nbsp;', 5);
        $table->addCell("@".$screenname->show());
        $table->endRow();
        // copyright
        $lic = $this->getObject('licensechooser', 'creativecommons');
        $table->startRow();
        $pcclabel = new label($this->objLanguage->languageText('mod_tweetlic_cclic', 'tweetlic') . ':', 'input_cclic');
        $table->addCell($pcclabel->show());
        $table->addCell('&nbsp;', 5);
        if (isset($edit['copyright'])) {
            $lic->defaultValue = $edit['copyright'];
        }
        $table->addCell($lic->show());
        $table->endRow();
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = '';
        $fieldset->contents = $table->show();
        $button = new button ('submitform', $this->objLanguage->languageText("mod_tweetlic_license", "tweetlic"));
        $button->setToSubmit();
        $form->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
        
        return $form->show();
    }
    
    /**
     * Method to create a search box for searching for users
     *
     * @param
     * @return
     */
     public function userSearchBox() {
         $this->loadClass('textinput', 'htmlelements');
         $qseekform = new form('qseek', $this->uri(array(
             'action' => 'usersearch',
         )));
         $qseekform->addRule('searchterm', $this->objLanguage->languageText("mod_tweetlic_phrase_searchtermreq", "tweetlic") , 'required');
         $qseekterm = new textinput('searchterm');
         $qseekterm->size = 15;
         $qseekform->addToForm("@".$qseekterm->show());
         $this->objsTButton = new button($this->objLanguage->languageText('word_search', 'system'));
         $this->objsTButton->setValue($this->objLanguage->languageText('word_search', 'system'));
         $this->objsTButton->setToSubmit();
         $qseekform->addToForm($this->objsTButton->show());
         $qseekform = $qseekform->show();
         $objFeatureBox = $this->getObject('featurebox', 'navigation');
         $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_tweetlic_qseek", "tweetlic") , $this->objLanguage->languageText("mod_tweetlic_qseekinstructions", "tweetlic") . "<br />" . $qseekform);

         return $ret;
     }
     
     
    
    
}
?>
