<?Php
/* -------------------- SKIN CLASS ----------------*/

/**
* Skin class for KEWL.NextGen (still needs work). This class
* is based on the skin functionality of KEWL 1.2 and is fully
* compatible with KEWL 1.2 skins.
* @author Derek Keats
* @author Tohir Solomons
*/
class skin extends object
{
    var $objForm;
    var $objButtons;
    var $objDropdown;

    function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objButtons =& $this->getObject('navbuttons', 'navigation');
        $this->objForm =& $this->newObject('form','htmlelements');
        $this->objDropdown =& $this->newObject('dropdown','htmlelements');
        $this->objConfig =& $this->newObject('altconfig','config');
        //$this->server =& $this->objConfig->serverName();

        // Browser Detection Class
        $this->browserInfo =& $this->getObject('browser');
    }

    /**
    * Method to get the name of the current skin
    * @return current skin
    */
    function getSkin()
    {
        $this->validateSkinSession();
        return $this->getSession('skin');
    }

    /**
    * Method to return the appropriate skin location
    * @return the path of the skin
    */
    function getSkinLocation()
    {
        $this->validateSkinSession();
        return $this->objConfig->getsiteRootPath().'/skins/'.$this->getSession('skin').'/';
    }

    /**
    * Method to return the appropriate skin location as a URL
    * @return the path of the skin as a URL
    */
    function getSkinUrl()
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
    function validateSkinSession()
    {
        // Check if skin exists, else set to default
        if ($this->getSession('skin') == '') {
            $this->setSession('skin', $this->objConfig->getdefaultSkin());
        }

        //Check for a change of skin
        if (isset($_POST['skinlocation']) && $_POST['skinlocation'] != '') {
            $mySkinLocation=$this->objConfig->getsiteRootPath().'skins/'.$_POST['skinlocation'].'/';

            //Test if stylesheet exists in the skinlocation
            if (file_exists($mySkinLocation.'kewl_css.php')) {
                $this->setSession('skin', $_POST['skinlocation']);
            } else {
                $this->setSession('skin', $this->objConfig->getdefaultSkin());
            }
        }
    }

    /**
    * Method to include the stylesheet for the current skin
    */
    function skinStartPage($headerParams=NULL)
    {
    }

    /**
    * Method to return the dropdown skin chooser
    * Works by building the string $ret into
    * the script needed to produce the HTML
    */
    function putSkinChooser()
    {

        //replace withthe name of the current script
        $script=$_SERVER['PHP_SELF'];
        $ret = $objNewForm = new form('ignorecheck',$script);
        $ret = $objDropdown = new dropdown('skinlocation');
        //loop through the folders and build an array of available skins
        $basedir=$this->objConfig->getsiteRootPath()."skins/";
        chdir($basedir);
        $dh=opendir($basedir);
        $dirList=array();
        while (false !== ($file = readdir($dh))) { #see http://www.php.net/manual/en/function.readdir.php
            if ($file != '.' && $file != '..' && strtolower($file)!='cvs') {
                if (is_dir($file) && file_exists($basedir.$file.'/kewl_css.php')) {

                    $skinnameFile=$this->objConfig->getsiteRootPath().'skins/'.$file.'/skinname.txt';

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

        foreach ($dirList as $element=> $value) {
           $ret .= $objDropdown->addOption($element,$value);
        }
        $ret = $objNewForm->addToForm($ret=$this->objLanguage->languageText('phrase_selectskin','security').":<br />\n");

        // Set the current skin as the default selected skin
        $objDropdown->setSelected($this->getSession('skin'));
        $objDropdown->cssClass = 'coursechooser';

        $ret .= $objDropdown->show();
        $ret .= $button = $this->objButtons->formButton('submit',$this->objLanguage->languageText('word_go','postlogin'));
        $ret = $objNewForm->addToForm($ret);
        $ret = $objNewForm->show();
        return $ret;

    }

    /**
    * Method to choose the skin for the current the session
    */
    function chooseSkin()
    {
        if ($this->getSkinLocation()=='') {
            die('Error loading skin');
        }
    }

    /**
    * Method to return the base URL for the banner image
    */
    function bannerImageBase()
    {
        return $this->getSkinUrl().'banners/';
    }


    /**
     * Method to put a logout link on the page
     */
    function putLogout()
    {
        $logout=$this->objLanguage->languageText('word_logout','security','Logout');
        $objConfirm =& $this->getObject('confirm', 'utilities');

        $message = $this->objLanguage->languageText('phrase_confirmlogout','security');
        $extra = ' class="pseudobutton"';

        $objConfirm->setConfirm($logout, $this->uri(array('action' => 'logoff'), 'security') ,$message,$extra);

        return $objConfirm->show();
    }

    /**
    * Method to output CSS to the header based on browser
    */
    function putSkinCssLinks()
    {
        $stylesheet = '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl().'kewl_css.php">'."</link>" . "\r\n";
        $stylesheet .= '<style type="text/css" media="screen, tv, projection"> @import "'.$this->getSkinUrl().'dropdown_menu_css.php"; </style>'."\r\n";
        if (strtolower($this->browserInfo->getBrowser()) == 'msie') {
            $stylesheet .= '<style type="text/css">
                @import "'.$this->objConfig->getskinRoot().'_common/IE_dd_menu_and_layout_fix.css";
                body { behavior:url("'.$this->objConfig->getskinRoot().'_common/ADxMenu_prof.htc"); }
            </style>';
        }
        return $stylesheet;
    }

    /**
    * Method to output simple CSS to the header based on browser
    */
    function putSimpleSkinCssLinks()
    {
        $stylesheet = '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl().'kewl_css.php">'."</link>" . "\r\n";
		/*
        $stylesheet .= '<style type="text/css" media="screen, tv, projection"> @import "'.$this->getSkinUrl().'dropdown_menu_css.php"; </style>'."\r\n";
        if (strtolower($this->browserInfo->getBrowser()) == 'msie') {
            $stylesheet .= '<style type="text/css">
                @import "'.$this->objConfig->skinRoot().'_common/IE_dd_menu_and_layout_fix.css";
                body { behavior:url("'.$this->objConfig->skinRoot().'_common/ADxMenu_prof.htc"); }
            </style>';
        }
		*/
        return $stylesheet;
    }

} # End of class
?>