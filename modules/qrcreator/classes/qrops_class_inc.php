<?php
/**
 *
 * QRcode helper class
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
 * @package   qrcreator
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
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
 * QRCode helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package qrcreator
 *
 */
class qrops extends object {

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
     * @var string $objCurl String object property for holding the cURL object
     *
     * @access public
     */
    public $objCurl;
    
    /**
     * @var string $objDbQr String object property for holding the QR db object
     *
     * @access public
     */
    public $objDbQr;
    

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
        $this->objCurl       = $this->getObject('curl', 'utilities');
        $this->objDbQr       = $this->getObject('dbqr');
        $this->objCookie     = $this->getObject('cookie', 'utilities');
    }
    
    /**
     * Method to generate a QR Code and some additional linkeddata
     *
     * @access public
     * @param string userid A Chisimba userid, got from objUser->userId()
     * @param string $msg a message up to 4MB long to encode
     * @param string lat a latitude
     * @param string lon a longitude
     * @return array $ret
     */
    public function genQr($userid, $msg, $lat = 0, $lon = 0) {
        $code = urlencode($msg.'|'.$lon.','.$lat);
        $gmapsurl = "http://maps.google.com/maps?q=$lon,$lat+%28$msg%29&iwloc=A&hl=en";
        // insert the message to the database and generate a url to use via a browser
        $recordid = $this->objDbQr->insert(array('userid' => $userid, 'msg' => $msg, 'lat' => $lat, 'lon' => $lon, 'gmapsurl' => $gmapsurl));
        // curl the Google Charts API to create the code
        $url = 'http://chart.apis.google.com/chart?chs=200x180&cht=qr&chl='.$code;
        $image = $this->objCurl->exec($url);
        $basename = 'qr'.$recordid.'.png';
        $filename = $this->objConfig->getcontentBasePath().'users/'.$userid.'/'.$basename;
        $file = file_put_contents($filename, $image);
        // get the image path now
        $imgsrc = $this->objConfig->getsiteRoot().$this->objConfig->getcontentPath().'users/'.$this->objUser->userId().'/'.$basename;
        // return an array of useful stuff
        $ret = array('image' => $imgsrc, 'gmapsurl' => $gmapsurl);
        
        return $ret;
    }
    
    /**
     * Method to generate a QR Code containing just a message
     *
     * @access public
     * @param string userid A Chisimba userid, got from objUser->userId()
     * @param string $msg a message up to 4MB long to encode
     * @return string $imgsrc
     */
    public function genBasicQr($userid, $msg) {
        $code = urlencode($msg);
        $gmapsurl = NULL;
        $lat = NULL;
        $lon = NULL;
        // insert the message to the database and generate a url to use via a browser
        $recordid = $this->objDbQr->insert(array('userid' => $userid, 'msg' => $msg, 'lat' => $lat, 'lon' => $lon, 'gmapsurl' => $gmapsurl));
        // curl the Google Charts API to create the code
        $url = 'http://chart.apis.google.com/chart?chs=200x180&cht=qr&chl='.$code;
        $image = $this->objCurl->exec($url);
        $basename = 'qr'.$recordid.'.png';
        $filename = $this->objConfig->getcontentBasePath().'users/'.$userid.'/'.$basename;
        $file = file_put_contents($filename, $image);
        // get the image path now
        $imgsrc = $this->objConfig->getsiteRoot().$this->objConfig->getcontentPath().'users/'.$this->objUser->userId().'/'.$basename;
        // return the useful stuff
        $ret = array('imageid' => $recordid, 'filename' => $imgsrc, 'userid' => $userid, 'basename' => $basename);
        return $ret;
    }
    
    public function basicMsgForm(){
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'qrcreator', 'Required').'</span>';
        //$headermsg = new htmlheading();
        //$headermsg->type = 1;
        //$headermsg->str = $this->objLanguage->languageText('phrase_createmsg', 'qrcreator');
        $ret = NULL;
        //$ret .= $headermsg->show();
        // start the form
        $form = new form ('msg', $this->uri(array('action'=>'createbasic'), 'qrcreator'));
        // add some rules
        // $form->addRule('msg', $this->objLanguage->languageText("mod_userregistration_needfriendname", "userregistration"), 'required');
        // Message field
        $table = $this->newObject('htmltable', 'htmlelements');
        $defmsg = $this->objLanguage->languageText("mod_qrcreator_basicmsg", "qrcreator");
        $table->startRow();
        $msg = $this->newObject('htmlarea', 'htmlelements');
        $msg->name = 'msg';
        $msg->value = $defmsg;
        $msg->width ='50%';
        $msgLabel = new label($this->objLanguage->languageText('mod_qrcreator_addmessage', 'qrcreator').'&nbsp;', 'input_msg');
        $table->addCell($msgLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $msg->toolbarSet = 'simple';
        $table->addCell($msg->show());
        $table->endRow();
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_qrcreator_createcode", "qrcreator"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();
        
        return $ret;
    }
    
    /**
     * Form used to invite friends to the site via mail invite
     *
     * @return string
     */
    public function showInviteForm() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'qrcreator', 'Required').'</span>';
        $headerinv = new htmlheading();
        $headerinv->type = 1;
        $headerinv->str = $this->objLanguage->languageText('phrase_invitemate', 'userregistration').' '.$this->objConfig->getSitename();
        $ret = NULL;
        $ret .= $headerinv->show();
        // start the form
        $form = new form ('invite', $this->uri(array('action'=>'sendinvite'), 'userregistration'));
        // add some rules
        $form->addRule('friend_firstname', $this->objLanguage->languageText("mod_userregistration_needfriendname", "userregistration"), 'required');
        $form->addRule('friend_email', $this->objLanguage->languageText("mod_userregistration_needfriendemail", "userregistration"), 'email');
        // friend name
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $friendname = new textinput('friend_firstname');
        $friendnameLabel = new label($this->objLanguage->languageText('friendname', 'userregistration').'&nbsp;', 'input_friendname');
        $table->addCell($friendnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendname->show().$required);
        $table->endRow();
        // surname
        $table->startRow();
        $friendsurname = new textinput('friend_surname');
        $friendsurnameLabel = new label($this->objLanguage->languageText('friendsurname', 'userregistration').'&nbsp;', 'input_friendsurname');
        $table->addCell($friendsurnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendsurname->show());
        $table->endRow();
        // email
        $table->startRow();
        $friendemail = new textinput('friend_email');
        $friendemailLabel = new label($this->objLanguage->languageText('friendemail', 'userregistration').'&nbsp;', 'input_friendemail');
        $table->addCell($friendemailLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendemail->show().$required);
        $table->endRow();
        // message to include to mate
        $defmsg = $this->objLanguage->languageText("mod_userregistration_wordhi", "userregistration").", <br /><br /> ".$this->objUser->fullname()." (".$this->objUser->username().") ".$this->objLanguage->languageText("mod_userregistration_hasinvited", "userregistration")." ".$this->objConfig->getSiteName()."! <br /><br /> ".$this->objLanguage->languageText("mod_userregistration_pleaseclick", "userregistration")."<br />";
        $table->startRow();
        $friendmsg = $this->newObject('htmlarea', 'htmlelements');
        $friendmsg->name = 'friend_msg';
        $friendmsg->value = $defmsg;
        $friendmsg->width ='50%';
        $friendmsgLabel = new label($this->objLanguage->languageText('friendmessage', 'userregistration').'&nbsp;', 'input_friendmsg');
        $table->addCell($friendmsgLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $friendmsg->toolbarSet = 'simple';
        $table->addCell($friendmsg->show());
        $table->endRow();
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_userregistration_completeinvite", "userregistration"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }
    
    /**
     * Method used to set geolocation coordinates
     *
     * Users are able to set geographic coordinates by either completing a text input or clicking on a map
     *
     * @param array $editparams
     * @param boolean $eventform
     * @return string
     */
    public function geoLocationForm($editparams = NULL, $eventform = FALSE) {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $ret = NULL;
        $lat = 0;
        $lon = 0;
        $zoom = 2;
        $currLocation = $this->objCookie->get('qrcreator_latlon');
        $currloc = explode("|", $currLocation);
        if(!empty($currloc) && isset($currloc[0]) && isset($currloc[1])) {
            $lat = $currloc[0];
            $lon = $currloc[1];
            $zoom = 10;
        } 
        if($this->objModules->checkIfRegistered('simplemap') && $this->objModules->checkIfRegistered('georss'))
        {
            $form = new form ('geoloc', $this->uri(array('action'=>'setlocation')));
            $this->loadClass('label', 'htmlelements');
            $this->objHead = $this->getObject('htmlheading', 'htmlelements');
            $this->objHead->type = 3;
            $this->objHead->str = $this->objLanguage->languageText("mod_qrcreator_geoposition", "qrcreator");
            $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
            $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: white;
        }
    </style>';

            $google = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$gmapsapikey."' type=\"text/javascript\"></script>";
            $olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
            $js = "<script type=\"text/javascript\">
        var lon = 5;
        var lat = 40;
        var zoom = 17;
        var map, layer, drawControl, g;

        OpenLayers.ProxyHost = \"/proxy/?url=\";
        function init(){
            g = new OpenLayers.Format.GeoRSS();
            map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20, projection: new OpenLayers.Projection(\"EPSG:900913\"), displayProjection: new OpenLayers.Projection(\"EPSG:4326\") });
            var normal = new OpenLayers.Layer.Google( \"Google Map\" , {type: G_NORMAL_MAP, 'maxZoomLevel':18} );
            var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18} );
            
            map.addLayers([normal, hybrid]);

            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );

            map.setCenter(new OpenLayers.LonLat($lon,$lat), $zoom);

            map.events.register(\"click\", map, function(e) {
                var lonlat = map.getLonLatFromViewPortPx(e.xy);
                OpenLayers.Util.getElement(\"input_geotag\").value = lonlat.lat + \",  \" +
                                          + lonlat.lon
            });

        }
    </script>";

            // add the lot to the headerparams...
            $this->appendArrayVar('headerParams', $css.$google.$olsrc.$js);
            $this->appendArrayVar('bodyOnLoad', "init();");
            // add the table row with the map in it.
            $ptable = $this->newObject('htmltable', 'htmlelements');
            $ptable->cellpadding = 3;
            // a heading
            $ptable->startRow();
            //$ptable->addCell('');
            $ptable->addCell($this->objHead->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
            $ptable->endRow();
            // and now the map
            $ptable->startRow();
            $gtlabel = new label($this->objLanguage->languageText("mod_qrcreator_geoposition", "qrcreator") . ':', 'input_geotags');
            $gtags = '<div id="map"></div>';
            $geotags = new textinput('geotag', NULL, NULL, '100%');
            if (isset($editparams['geolat']) && isset($editparams['geolon'])) {
                $geotags->setValue($editparams['geolat'].", ".$editparams['geolon']);
            }
            //$ptable->addCell($gtlabel->show());
            $ptable->addCell($gtags.$geotags->show());
            $ptable->endRow();

            $fieldset = $this->newObject('fieldset', 'htmlelements');
            $fieldset->legend = '';
            $fieldset->contents = $ptable->show();
            $button = new button ('submitform', $this->objLanguage->languageText("mod_qrcreator_setlocation", "qrcreator"));
            $button->setToSubmit();
            $form->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
            $ret .= $form->show();
        }
        else {
            $ret .= "Map cannot be shown";
        }

        return $ret;
    }
    
    /**
     * Sign in block
     *
     * Used in conjunction with the welcome block as a alertbox link. The sign in simply displays the block to sign in to Chisimba
     *
     * @return string
     */
    public function showSignInBox() {
        $objBlocks = $this->getObject('blocks', 'blocks');
        return $objBlocks->showBlock('login', 'security', 'none');
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
        return $objFeatureBox->show($this->objLanguage->languageText("mod_events_signup", "events"), $objBlocks->showBlock('register', 'security', 'none'));
    }
}
?>
