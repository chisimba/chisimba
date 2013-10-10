<?php
/**
 *
 * featuresuggest helper class
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
 * @package   featuresuggest
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
 * featuresuggest helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package featuresuggest
 *
 */
class featuresui extends object {

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
        $this->objOps        = $this->newObject('featureops');
        $this->objDbFeatures = $this->getObject('dbfeatures');
        
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
        $str = NULL;
        $str = '<ul class="suggestions">';
        foreach($sug as $s) {
            $this->objOps->setData($s);
            $str .= (string)$this->objOps;
        }
        $str .='</ul>';
               
        return $str;
    }
    
    public function formatUI() {
        $js = NULL;
        $js .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
        $js .= $this->getJavascriptFile('script.js', 'featuresuggest');
        return $js;
    }
    
    public function addForm() {
        // load up a simple form 
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $addform = new form('addfeature', $this->uri(array(
            'action' => 'submit',
        )));
        $addform->addRule('content', $this->objLanguage->languageText("mod_featuresuggest_phrase_featurereq", "featuresuggest") , 'required');
        $feature = new textinput('content');
        $feature->size = 50;
        $addform->addToForm($feature->show());
        $this->objsTButton = new button($this->objLanguage->languageText('word_featureadd', 'featuresuggest'));
        $this->objsTButton->setValue($this->objLanguage->languageText('word_featureadd', 'featuresuggest'));
        $this->objsTButton->setToSubmit();
        $addform->addToForm($this->objsTButton->show());
        $addform = $addform->show();
        
        return $addform;
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
