<?php
/* -------------------- SKIN CLASS ----------------*/

/**
* Skin class for KEWL.NextGen/Chisimba (still needs work). This class
* is based on the skin functionality of KEWL 1.2 and is fully
* compatible with KEWL 1.2 skins.
* @author Derek Keats
* @author Tohir Solomons
* @author Charl Mert
*/
class skin extends object
{

    /**
     * Instance of the modules object in the modulecatalogue module.
     *
     * @var object
     */
    protected $objModules;

    public $skinFile = 'stylesheet.css';

    public function init()
    {
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('form','htmlelements');
        $this->loadClass('dropdown','htmlelements');
        $this->loadClass('button','htmlelements');
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
    * Method to get the name of the current skin
    * @return current skin
    */
    public function getSkin()
    {
        $this->validateSkinSession();
        return $this->getSession('skin');
    }

    /**
    * Method to return the appropriate skin location
    * @return the path of the skin
    */
    public function getSkinLocation()
    {
        $this->validateSkinSession();
        return $this->objConfig->getsiteRootPath().$this->skinRoot.$this->getSession('skin').'/';
    }

    /**
    * Method to return the appropriate skin location as a URL
    * @return the path of the skin as a URL
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
    * Method to include the stylesheet for the current skin
    */
    public function skinStartPage($headerParams=NULL)
    {
    }

    /**
    * Method to return the dropdown skin chooser
    * Works by building the string $ret into
    * the script needed to produce the HTML
    */
    public function putSkinChooser()
    {
        //replace withthe name of the current script
        $script=$_SERVER['PHP_SELF'];
        $objNewForm = new form('ignorecheck',$script);
        $objDropdown = new dropdown('skinlocation');
        $objDropdown->extra = "onchange =\"document.forms['ignorecheck'].submit();\"";
        //loop through the folders and build an array of available skins
        $basedir=$this->objConfig->getsiteRootPath().$this->skinRoot;

        // Get Current Working Directory
        $currentDir = getcwd();

        chdir($basedir);
        $dh=opendir($basedir);
        $dirList=array();
        while (false !== ($file = readdir($dh))) { #see http://www.php.net/manual/en/function.readdir.php
            if ($file != '.' && $file != '..' && strtolower($file)!='cvs') {

                if ( (is_dir($file) && file_exists($basedir.$file.'/'.$this->skinFile))
                ||   (is_dir($file) && file_exists($basedir.$file.'/skin.conf')) ){

                    $skinnameFile=$this->objConfig->getsiteRootPath().$this->skinRoot.$file.'/skinname.txt';
					$skinConfigFile=$basedir.$file.'/skin.conf';

                    if (file_exists($skinConfigFile)) {
						$skinData = $this->readConf($skinConfigFile);
						$dirList[$file] = $skinData['SKIN_NAME'];
					} else if (file_exists($skinnameFile)) {
                        $ts=fopen($skinnameFile,'r');
                        $ts_content=fread($ts, filesize($skinnameFile));
                        $dirList[$file] = $ts_content;
                    } else {
                        $dirList[$file] = $file;
                    }

                }
            }
        }
        closedir($dh);

        // Return to Current Working Directory
        chdir($currentDir);

        // Sort Alphabetically

        asort($dirList);

        foreach ($dirList as $element=> $value) {
           $objDropdown->addOption($element,$value);
        }
        $objNewForm->addToForm($this->objLanguage->languageText('phrase_selectskin').":<br />\n");

        // Set the current skin as the default selected skin
        $objDropdown->setSelected($this->getSession('skin'));
        $objDropdown->cssClass = 'coursechooser';
        $objNewForm->addToForm($objDropdown->show());
        return $objNewForm->show();

    }


    /**
    * Reads the 'skin.conf' file provided by the skin
    * These are then returned as an associative array.
    * @param  string  $filepath  path and filename of file.
    * @param  boolean $useDefine determine use of defined constants
    * @return array   $registerdata all the info from the register.conf file
    */
    public function readConf($filepath,$useDefine=FALSE) {
        try {
            if (file_exists($filepath)) {
                $registerdata=array();
                $lines=file($filepath);
                $cats = array();
                foreach ($lines as $line) {
                    preg_match('/([^:]+):(.*)/',$line,$params);
                    $params[0] =isset($params[1])? trim($params[1]) : '';
                    $params[1] =isset($params[2])? trim($params[2]) : '';
                    $registerdata[$params[0]]=rtrim($params[1]);
                } //    end of foreach
                return ($registerdata);
            } else {
                return FALSE;
            } // end of if
        } catch (Exception $e) {
            throw new customException($e->getMessage());
            exit(0);
        }
    }


    /**
    * Method to get the list of skins available
    * @return array List of available skins
    */
    public function getListofSkins()
    {
        $currentDir = getcwd();
        //loop through the folders and build an array of available skins
        $basedir=$this->objConfig->getsiteRootPath().$this->skinRoot;
        chdir($basedir);
        $dh=opendir($basedir);
        $dirList=array();
		while (false !== ($file = readdir($dh))) { #see http://www.php.net/manual/en/function.readdir.php
            if ($file != '.' && $file != '..' && strtolower($file)!='cvs') {

                if ( (is_dir($file) && file_exists($basedir.$file.'/'.$this->skinFile))
                ||   (is_dir($file) && file_exists($basedir.$file.'/skin.conf')) ){

                    $skinnameFile=$this->objConfig->getsiteRootPath().$this->skinRoot.$file.'/skinname.txt';
                    $skinConfigFile=$basedir.$file.'/skin.conf';

                    if (file_exists($skinConfigFile)) {
                        $skinData = $this->readConf($skinConfigFile);
                        $dirList[$file] = $skinData['SKIN_NAME'];
                    } else if (file_exists($skinnameFile)) {
                        $ts=fopen($skinnameFile,'r');
                        $ts_content=fread($ts, filesize($skinnameFile));
                        $dirList[$file] = $ts_content;
                    } else {
                        $dirList[$file] = $file;
                    }

                }
            }
        }


        closedir($dh);
        chdir($currentDir);

        return $dirList;
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
        $this->loadClass('label', 'htmlelements');
        $slabel = new label($this->objLanguage->languageText('phrase_sitesearch', 'search', 'Site Search') .':', 'input_search');
        $this->loadClass('textinput', 'htmlelements');
        $sform = new form('query', $this->uri(NULL,'search'));
        //$sform->addRule('searchterm', $this->objLanguage->languageText("mod_blog_phrase_searchtermreq", "blog") , 'required');
        $query = new textinput('search');
        $query->size = 15;
        $this->objSButton = new button($this->objLanguage->languageText('word_go', 'system'));
        // Add the search icon
        $this->objSButton->setIconClass("search");
        //$this->objSButton->setValue($this->objLanguage->languageText('mod_skin_find', 'skin'));
        $this->objSButton->setValue('Find');
        $this->objSButton->setToSubmit();
        if ($compact) {
            $sform->addToForm($slabel->show().' '.$this->objSButton->show().'<br /> '.$query->show());
        } else {
            $sform->addToForm($slabel->show().' '.$query->show().' '.$this->objSButton->show());
        }
        $sform = '<div id="search">'.$sform->show().'</div>';
        //Letus look at the configuration file file first
        $objConfig = $this->getObject('altconfig','config');
        //checking if configuration exist-By Emmanuel Natalis
        if(strtoupper($objConfig->getenable_searchBox()) == 'TRUE' && $this->objModules->checkIfRegistered('search')) {
            return $sform;
        }
        else {
            return NULL;
        }
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
        if ($mime != 'application/xhtml+xml') {
            $mime = 'text/html';
        }

        $str = '';

        $supressPrototype = $this->getVar('SUPPRESS_PROTOTYPE', false);
        $supressJQuery = $this->getVar('SUPPRESS_JQUERY', false);
        $jQueryVersion = $this->getVar('JQUERY_VERSION', '1.2.3');

        if (!$supressPrototype){
            // Add Scriptaculous
            $scriptaculous = $this->getObject('scriptaculous', 'htmlelements');
            $str .= $scriptaculous->show($mime);
        }

        if (!$supressJQuery){
            // Add JQuery
            $jquery = $this->getObject('jquery', 'jquery');
            $jquery->setVersion($jQueryVersion);
            $str .= $jquery->show();
        }

        // Get HeaderParams
        if ($headerParams == NULL) {
            $headerParams = $this->getVar('headerParams');
        }

        if (is_array($headerParams)) {
            $headerParams = array_unique($headerParams);
            foreach ($headerParams as $headerParam) {
                $str .= $headerParam."\n\n";
            }
        }

        // Get Body On Load
        if ($bodyOnLoad == NULL) {
            $bodyOnLoad = $this->getVar('bodyOnLoad');
        }

        if (is_array($bodyOnLoad)) {
            $str .= '<script type="text/javascript">';
            $str .= 'window.onload = function() {'."\n";
            foreach ($bodyOnLoad as $bodyParam) {
                $str .= '   '.$bodyParam."\n";
            }
            $str .= '}
</script>'."\n\n";
        }
        return $str;
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

   /**
    *
    * Method to output site load.
    *
    * @return string The number of users currently logged into the site
    * @access Public
    * @author Jeremy O'Connor
    */
//    public function siteLoad()
//    {
//        $objDBLoggedInUsers = $this->getObject('dbloggedinusers');
//        $count = $objDBLoggedInUsers->count();
//        return "&nbsp;".($count == 1?$this->objLanguage->code2Txt('mod_skin_usersonline_singular','skin',array('COUNT'=>$count)):$this->objLanguage->code2Txt('mod_skin_usersonline_plural','skin',array('COUNT'=>$count)));
//    }

} # End of class
?>
