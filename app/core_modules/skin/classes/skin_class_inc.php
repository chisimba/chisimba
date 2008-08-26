<?php
/* -------------------- SKIN CLASS ----------------*/

/**
* Skin class for KEWL.NextGen/Chisimba (still needs work). This class
* is based on the skin functionality of KEWL 1.2 and is fully
* compatible with KEWL 1.2 skins.
* @author Derek Keats
* @author Tohir Solomons
*/
class skin extends object
{

    public $skinFile = 'stylesheet.css';

    public function init()
    {
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
        //$this->server = $this->objConfig->serverName();

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
    * Method to validate whether a skin session exists, or has been changed
    *
    * If skin has changed, first check if the stylesheet exists, then change.
    * If no skin session exists or stylesheet doesn't exist, use the default skin.
    * @param boolean $redirect Variable to dictate whether to redirect after a skin change
    * Though this variable is defaulted to TRUE, it is in an if-statement requiring a $_POST method
    */
    public function validateSkinSession()
    {
        // Check if skin exists, else set to default
        if ($this->getSession('skin') == '') {
            $this->setSession('skin', $this->objConfig->getdefaultSkin());
        }

        //Check for a change of skin
        if (isset($_POST['skinlocation']) && $_POST['skinlocation'] != '') {
            $mySkinLocation=$this->objConfig->getsiteRootPath().$this->skinRoot.$_POST['skinlocation'].'/';

            //Test if stylesheet exists in the skinlocation
            if (file_exists($mySkinLocation.$this->skinFile)) {
                $this->setSession('skin', $_POST['skinlocation']);
            } else {
                $this->setSession('skin', $this->objConfig->getdefaultSkin());
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
                if (is_dir($file) && file_exists($basedir.$file.'/'.$this->skinFile)) {

                    $skinnameFile=$this->objConfig->getsiteRootPath().$this->skinRoot.$file.'/skinname.txt';

                    if (file_exists($skinnameFile)) {
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
                if (is_dir($file) && file_exists($basedir.$file.'/stylesheet.css')) {
                    $skinnameFile=$this->objConfig->getsiteRootPath().$this->skinRoot.$file.'/skinname.txt';
                    if (file_exists($skinnameFile)) {
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
        $skinRoot = $this->objConfig->getsiteRoot().$this->skinRoot;
        
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
        return $stylesheet;
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
     * @return str Search Form
     */
    public function siteSearchBox()
    {
    	$this->loadClass('label', 'htmlelements');
    	$slabel = new label($this->objLanguage->languageText('phrase_sitesearch', 'search', 'Site Search') .':', 'input_query');
    	$this->loadClass('textinput', 'htmlelements');
        $sform = new form('query', $this->uri(NULL,'search'));
        //$sform->addRule('searchterm', $this->objLanguage->languageText("mod_blog_phrase_searchtermreq", "blog") , 'required');
        $query = new textinput('search');
        $query->size = 15;
        $sform->addToForm($slabel->show().' '.$query->show().' ');
        $this->objSButton = &new button($this->objLanguage->languageText('word_go', 'system'));
        $this->objSButton->setValue($this->objLanguage->languageText('word_go', 'system'));
        $this->objSButton->setToSubmit();
        $sform->addToForm($this->objSButton->show());
        $sform = '<div id="search">'.$sform->show().'</div>';

        return $sform;
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

        // Add Scriptaculous
        $scriptaculous = $this->getObject('scriptaculous', 'htmlelements');
        $str .= $scriptaculous->show($mime);

        // Add JQuery
        $jquery = $this->getObject('jquery', 'htmlelements');
        $str .= $jquery->show();

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
            $str .= '<script type="text/javascript" language="javascript">';
            $str .= 'window.onload = function() {'."\n";
            foreach ($bodyOnLoad as $bodyParam) {
                $str .= '   '.$bodyParam."\n";
            }
            $str .= '}
</script>'."\n\n";
        }
        return $str;
    }

} # End of class
?>
