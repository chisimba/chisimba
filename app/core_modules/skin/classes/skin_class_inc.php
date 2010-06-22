<?php
/**
 *
 * Class for manipulating skins in Chisimba
 *
 * This is effectively the skin 'engine' for Chisimba, and is used
 * to render and alter skin properties, read and render templates,
 * etc.
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
 * @category  Chisimba
 * @package   skin
 * @author    Derek Keats <derek.keats@wits.ac.za>
 * @author Tohir Solomons
 * @author Charl Mert
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
 * Class for manipulating skins in Chisimba
 *
 * This is effectively the skin 'engine' for Chisimba, and is used
 * to render and alter skin properties, read and render templates,
 * etc.
 *
* @author Derek Keats
* @author Tohir Solomons
* @author Charl Mert
*/
class skin extends object
{
    /**
    *
    * Instance of the skin chooser rendering object
    *
    * @var string object $objSkinChooser
    * @access public
    *
    */
    public $objSkinChooser;

    /**
    *
    * The filename for the skin CSS file
    *
    * @var string $skinFile
    * @Access public
    */
    public $skinFile = 'stylesheet.css';

    /**
    *
    * Constructor for the class
    * @access public
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig','config');
        //Option to suppress XML
        $xmlflag=$this->objConfig->getNoXML();
        if (($xmlflag=='1')||($xmlflag==TRUE)||($xmlflag=='TRUE')){
            $this->setVar('pageSuppressXML',TRUE);
        }
        // Suppress chrome if output needs to be displayed inside facebox.
        if ($this->getParam('facebox')) {
            $this->setVar('pageSuppressContainer', TRUE);
            $this->setVar('pageSuppressBanner', TRUE);
            $this->setVar('suppressFooter', TRUE);
        }
        // Browser Detection Class
        $this->browserInfo = $this->getObject('browser');
        $this->skinRoot = $this->objConfig->getskinRoot();      
    }

    /**
    *
    * Method to get the name of the current skin
    *
    * @return string current skin
    * @access public
    *
    */
    public function getSkin()
    {
        $this->validateSkinSession();
        return $this->getSession('skin');
    }

    /**
    *
    * Method to return the appropriate skin location
    *
    * @return string The path of the skin
    * @access public
    *
    */
    public function getSkinLocation()
    {
        $this->validateSkinSession();
        return $this->objConfig->getsiteRootPath()
          .$this->skinRoot.$this->getSession('skin').'/';
    }

    /**
    *
    * Method to return the appropriate skin location as a URL
    *
    * @return string The path of the skin as a URL
    * @access public
    *
    */
    public function getSkinUrl()
    {
        $this->validateSkinSession();
        return $this->objConfig->getskinRoot().$this->getSession('skin').'/';
    }



    /**
    * Method to get the skin engine type
    *
    * This is collected from the skin.conf so if none exists the default engine is
    * assumed.
	*
    * @return String the current skins engine type.
    */
    public function getSkinEngine($skinPath = '')
    {
        if ($skinPath == '') {
            $skinPath = $this->getSkinLocation();
        }
        //Load skin.conf
        $skinConfigFile = $skinPath . 'skin.conf';
        if (file_exists($skinConfigFile)) {
            $skinData = $this->readConf($skinConfigFile);
            $this->skinEngine = $skinData['SKIN_ENGINE'];
            return $this->skinEngine;
        } else {
            //If new skin config file doesn't exist defaulting to old skin engine
            return 'default';
        }
    }

    /**
    * Method to validate whether a skin session exists, or has been changed
    *
    * If skin has changed, first check if the stylesheet exists, then change.
    * If no skin session exists or stylesheet doesn't exist, use the default skin.
    * @param boolean $redirect Variable to dictate whether to redirect after a skin change
    * Though this variable is defaulted to TRUE, it is in an if-statement requiring a $_POST method
    */
    public function validateSkinSession()
    {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        // Check if skin exists, else set to default
        if ($this->getSession('skin') == '') {
            $this->setSession('skin', $this->objConfig->getdefaultSkin());
        }

        //TODO: Optimize calls to validate skin session, should only have to be called once per page load
        //      Currently being called up to 30 times per pageload
        //var_dump('VALIDATING SKIN SESSION');

        //Check for a change of skin
        if (isset($_POST['skinlocation']) && $_POST['skinlocation'] != '') {
            $mySkinLocation=$this->objConfig->getsiteRootPath().$this->skinRoot.$_POST['skinlocation'].'/';
            $this->skinEngine = $this->getSkinEngine($mySkinLocation);
            if ($this->skinEngine == 'default' || $this->skinEngine == '') {
                //Test if stylesheet exists in the skinlocation
                if (file_exists($mySkinLocation.$this->skinFile)) {
                        $this->setSession('skin', $_POST['skinlocation']);
                } else {
                        $this->setSession('skin', $this->objConfig->getdefaultSkin());
                }
            } else if ($this->skinEngine == 'university') {
                $this->skinFile = 'style.css';
                //Test if stylesheet exists in the skinlocation
                if (file_exists($mySkinLocation.$this->skinFile)) {
                        $this->setSession('skin', $_POST['skinlocation']);
                } else {
                        $this->setSession('skin', $this->objConfig->getdefaultSkin());
                }
            }
        }
    }

    /**
     *
     * Skin change method for use with Ajax
     *
     *
     */
    public function changeSkin()
    {
        if ($skinLocation = $this->getParam('skinlocation', FALSE)) {
            $this->skinEngine = $this->getSkinEngine($mySkinLocation);
            if ($this->skinEngine == 'default' || $this->skinEngine == '') {
                //Test if stylesheet exists in the skinlocation
                if (file_exists($mySkinLocation.$this->skinFile)) {
                    $this->setSession('skin', $_POST['skinlocation']);
                } else {
                    $this->setSession('skin', $this->objConfig->getdefaultSkin());
                }
            } else if ($this->skinEngine == 'university') {
                $this->skinFile = 'style.css';
                //Test if stylesheet exists in the skinlocation
                if (file_exists($mySkinLocation.$this->skinFile)) {
                    $this->setSession('skin', $_POST['skinlocation']);
                } else {
                    $this->setSession('skin', $this->objConfig->getdefaultSkin());
                }
            }
            return '200: OK';
        } else {
            return '999: noskinlocation';
        }
    }

    /**
    *
    * Method to return the dropdown skin chooser left here
    * for legacy reasons in case there is any code that calls
    * it.
    *
    * @return string A form with the skin chooser
    * @access public
    *
    */
    public function putSkinChooser()
    {
        // Get the skinelements, the class into which the rendering has been refactored.
        $objSkinChooser = $this->getObject('skinchooser', 'skin');
        return $objSkinChooser->show();
    }

    /**
    * Method to choose the skin for the current the session
    */
    public function chooseSkin()
    {
        if ($this->getSkinLocation()=='') {
            die('Error loading skin');
        }
    }

    /**
    * Method to return the base URL for the banner image
    */
    public function bannerImageBase()
    {
        return $this->getSkinUrl().'banners/';
    }

  /**
    * Method to return the skin location of the common icons folder as a URL
    * @return the path of the common skin as a URL
    */
    public function getCommonSkinUrl()
    {
        return $this->objConfig->getskinRoot().'/_common/';
    }

    /**
     * Method to put a logout link on the page
     */
    public function putLogout()
    {
        $logout=$this->objLanguage->languageText('word_logout','security','Logout');
        $objConfirm =& $this->getObject('confirm', 'utilities');
        $message = $this->objLanguage->languageText('phrase_confirmlogout','security');
        $extra = ' class="pseudobutton"';
        $objConfirm->setConfirm($logout, $this->uri(array('action' => 'logoff'), 'security') ,$message,$extra);
        return $objConfirm->show();
    }



    /**
    *
    * Method to output CSS to the header based on browser
    *
    * @param string $theme Parameter to hold the stylesheet to be loaded
    * @return string The list of CSS links formatted for inclusion in the page header
    * @access Public
    *
    */
    public function putSkinCssLinks($theme="stylesheet")
    {
        $skinRoot = $this->skinRoot;
        $skinEngine = $this->getSkinEngine();

        //Determining which css to load based on current skin engine requirements
        if (isset($skinEngine)) {
            if ($skinEngine == 'default' || $skinEngine == '') {
                $stylesheet = '
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/common_styles.css" media="screen" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/print.css" media="print" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/forms.css" media="print" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/forms-extra.css" media="print" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/' . $theme . '.css" media="screen" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/print.css" media="print" />
                    ';
                if (strtolower($this->browserInfo->getBrowser()) == 'msie') {
                    $stylesheet .= '
                        <!--[if lte IE 7]>
                        <link rel="stylesheet" type="text/css" href="'.$this->skinRoot.'_common/ie6_or_less.css" />
                        <![endif]-->';
                }
            } else if ($skinEngine == 'university') {
                //UWC Portal / University Specific CSS Requirements

                //Generating temp chisimba stub on first load
                $skinPath = $skinRoot.$this->getSkin();

                //With Chisimba Standard Skinset
                $stylesheet = '
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/reset.css" media="screen" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/common_styles.css" media="screen" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/print.css" media="print" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/forms.css" media="print" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/forms-extra.css" media="print" />
                    <!--[if IE 8]>
                            <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/style_ie8.css" media="screen" />
                    <![endif]-->
                    <!--[if IE 7]>
                            <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/style_ie7.css" media="screen" />
                    <![endif]-->
                    <!--[if lte IE 6]>
                            <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/style_ie6.css" media="screen" />
                    <![endif]-->
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/style.css" media="screen" />
                    <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/integration.css" media="screen" />
                    ';
            }
        } else {
            $stylesheet = '
                <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/common_styles.css" media="screen" />
                <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/print.css" media="print" />
                <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/forms.css" media="print" />
                <link rel="stylesheet" type="text/css" href="'.$skinRoot.'_common/forms-extra.css" media="print" />
                <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/' . $theme . '.css" media="screen" />
                <link rel="stylesheet" type="text/css" href="'.$skinRoot.$this->getSkin().'/print.css" media="print" />
                ';
            if (strtolower($this->browserInfo->getBrowser()) == 'msie') {
                $stylesheet .= '
                    <!--[if lte IE 7]>
                    <link rel="stylesheet" type="text/css" href="'.$this->skinRoot.'_common/ie6_or_less.css" />
                    <![endif]-->';
            }
        }
        $result = $this->putMetaTags().$stylesheet;
        return $result;
    }


    /**
    * Method to output simple CSS to the header based on browser
    */
    public function putSimpleSkinCssLinks()
    {
        return $this->putSkinCssLinks();
    }

    /**
    * Method to get the Path to a Skin Template
    * @param string $type Type of Template - Either 'page' or 'layout'
    * @return path to template
    */
    public function getTemplate($type)
    {
        switch (strtolower($type))
        {
            case 'page': $template = $this->getPageTemplate(); break;
            case 'layout': $template = $this->getLayoutTemplate(); break;
            default: $template = 'unknown';
        }
        return $this->objConfig->getsiteRootPath().$template;
    }

    /**
    * Method to get the Page Template of a Skin
    *
    * If the page template does not exist, it returns the _common page template
    *
    * @return path to page template to be used
    */
    public function getPageTemplate()
    {
        $objLogin = $this->getObject('loginbox', 'userregistration');
        $this->setVar('login', $objLogin->show());
        if (file_exists($this->objConfig->getsiteRootPath().$this->skinRoot.$this->getSkin().'/templates/page/page_template.php')) {
            return $this->skinRoot.$this->getSkin().'/templates/page/page_template.php';
        } else {
            return $this->skinRoot.'_common/templates/page/page_template.php';
        }
    }

    /**
    * Method to get the Layout Template of a Skin
    *
    * If the layout template does not exist, it returns the _common layout template
    *
    * @return path to layout template to be used
    */
    public function getLayoutTemplate()
    {
        if (file_exists($this->objConfig->getsiteRootPath().$this->skinRoot.$this->getSkin().'/templates/layout/layout_template.php')) {
            return $this->skinRoot.$this->getSkin().'/templates/layout/layout_template.php';
        } else {
            return $this->skinRoot.'_common/templates/layout/layout_template.php';
        }

        return $this->skinRoot.'_common/templates/layout/layout_template.php';
    }


    /**
     * Method to generate a form for a site-wide search
     * @param boolean $compact whether or not to use the compact search form for small screens.
     * @return str Search Form
     */
    public function siteSearchBox($compact = FALSE)
    {
        $objSearchBox = $this->getObject('sitesearchbox', 'skin');
        return $objSearchBox->show($compact);
    }

    /**
     * Method to return the common JavaScript that is used and needs to go into the page templates
     * This loads Prototype and JavaScript into the page templates
     *
     * @param string $mime Mimetype of Page - Either text/html or application/xhtml+xml
     * @param array $headerParams List of items that needs to go into the header of the page
     * @param array $bodyOnLoad List of items that needs to go into the bodyOnLoad section of the page
     */
    public function putJavaScript($mime='text/html', $headerParams=NULL, $bodyOnLoad=NULL)
    {
        $objJs = $this->getObject('skinjavascript', 'skin');
        return $objJs->loadAll($mime, $headerParams, $bodyOnLoad);
    }


   /**
    *
    * Method to output meta tags to the header (uses metaKeywords and metaDescriptions arrays)
    *
    * @return string The list of accumulated meta tags
    * @access Public
    * @author Charl Mert
    */
    public function putMetaTags()
    {
        $str = '';
        $metaKeywords = '';
        $metaDescriptions = '';

        // Get metaKeywords
        if ($metaKeywords == NULL) {
            $metaKeywords = $this->getVar('metaKeywords');
        }

        // Get metaDescriptions
        if ($metaDescriptions == NULL) {
            $metaDescriptions = $this->getVar('metaDescriptions');
        }

        //Adding Keywords
        if (is_array($metaKeywords)){
            foreach ($metaKeywords as $key) {
                $str .= "<META name='keywords' content='$key'>\n";
            }
        }

        //Adding Descriptions
        if (is_array($metaDescriptions)){
            foreach ($metaDescriptions as $desc) {
                $str .= "<META name='description' content='$desc'>\n";
            }
        }
        return $str;
    }
}
?>