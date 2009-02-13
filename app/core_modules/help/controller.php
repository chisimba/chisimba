<?php
/* -------------------- help class extends controller ----------------*/
/**
* Class for providing a service to other modules that want to
* display help
*
* @author  Derek Keats, Tohir Solomons
* @author  Megan Watson - porting to 5ive
* @version 1.1
*/
class help extends controller 
{

    /**
    * Intialiser for the adminGroups object
    *
    * @param byref $ string $engine the engine object
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        //Get the activity logger class
        $this->objLog = $this->getObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();

    }

    /**
    * The standard dispatch method for the module. The dispatch() method must
    * return the name of a page body template which will render the module
    * output (for more details see Modules and templating)
    */
    public function dispatch()
    {
        //$this->setPageTemplate('help_page_tpl.php');
        $this->setVar('pageSuppressContainer', TRUE);
        $this->setVar('suppressFooter', TRUE); // suppress default page footer
        $this->setVar('pageSuppressIM', TRUE);
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);

        $bodyParams = 'class="popupwindow help-popup" onLoad="window.focus();"';
        $this->setVar('bodyParams', $bodyParams);
        
        $helpId = $this->getParam('helpid');
        $rootModule = $this->getParam('rootModule');
            
        if ($this->getParam('action') == 'view') {
            return $this->showHelp($helpId, $rootModule);
        } else {
            $this->setVar('help_text', $this->objLanguage->code2Txt($helpId, $rootModule));
            return 'main_tpl.php';
        }
    }

    /**
    * Method to set login requirement to False
    * Required to be false. prelogin screen
    */
    public function requiresLogin()
    {
        return FALSE;
    }

    /**
    * Method to display a help item
    *
    * @param string $helpItem The language text element of the help item
    * @param string $module   The module of the help item
    */
    public function showHelp($helpItem, $module)
    {

        if ($helpItem == 'about') {
            $helptext = 'help_'.$module.'_about';
            $helptitle = 'help_'.$module.'_about_title';
        } else {
            $helptext = 'help_'.$module.'_overview_'.$helpItem;
            $helptitle = 'help_'.$module.'_title_'.$helpItem;
        }
        
        $first = 'help_'.$module.'_title_%';
        $second = 'help_'.$module.'_about_title';

        $filter = "SELECT * FROM tbl_languagetext WHERE code LIKE '$first' OR code = '$second' AND code != '$helptitle' ORDER BY code";

        $helpTitle = $this->objLanguage->code2Txt($helptitle, $module);
        $helpText = $this->objLanguage->code2Txt($helptext, $module);

        if (strtoupper(substr($helpTitle, 0, 12)) == '[*HELPLINK*]') {
            $array = explode('/', $helpTitle);

            $helpTitle = $this->objLanguage->code2Txt('help_'.$array[1].'_title_'.$array[2], $module);
            $helpText = $this->objLanguage->code2Txt('help_'.$array[1].'_overview_'.$array[2], $module);
        }

        $this->setVar('helptitle', $helpTitle);
        $this->setVar('helptext', $helpText);
        $this->setVar('moduleHelp', $this->objLanguage->getArray($filter));
        $this->setVar('module', $module);

        return 'help_display_tpl.php';
    }

    /**
    * Method to check whether a viewlet exists for a help
    *
    * @param  string $module The module of the help item
    * @param  string $action The action of the help item
    * @return string Link to viewlet | Null if file does not exist
    *                
    public function checkForViewlet($module, $action)
    {
        $file = 'help_'.$module.'_viewlet_'.$action;
        $viewletFile=$this->checkForFile($file, $module, '.php');

        if (isset($viewletFile)) {
            $viewletFile .= '?skin='.$this->objSkin->getSkinUrl().'kewl_css.php';
            $this->icon->setIcon('viewlet');

            return '<a href="'.$viewletFile.'">'.$this->icon->show().'</a>';
        } else {
            return NULL;
        }
    }


    /**
    *                Method to check for a rich help
    *                
    *                It takes a language text element, and then checks for a file {languagetextelemnt}.html
    *                file in the /modules/modulename/help/{currentlanguage}/ folder. Folder is the two letter code.
    *                If one exists, return a colour icon, else return a grayed out version.
    *                
    * @param  string $helpId The name of the help language text
    * @return string $ex The icon to display - either colour or grayed out
    *                
    public function checkForRichHelp($helpId, $extension = '.html')
    {
        $rootModule = $this->getParam("rootModule", Null);
        //See if there is rich help (/modules/thismodule/help/
        if ($rootModule==Null) {
            //die("No rootModule");
            $this->icon->setIcon("help_rich_grey");
            $ex = $this->icon->show()."&nbsp;";
            $ex .= "<a href=\"javascript:window.self.close();\">";
            //Set the close icon
            $this->icon->setIcon("close");
            $ex.=$this->icon->show()."</a>";
            return $ex;
        } else {

            // Check for Help File
            $helpfile=$this->checkForFile($helpId, $rootModule, $extension);

            // If the extended help file exists, add a link to it
            if (isset($helpfile)) {
                $helpfile .= '?skin='.$this->objSkin->getSkinUrl().'kewl_css.php';
                //Create a new instance of popup windows generator
                $this->objPop=& $this->getObject('windowpop','htmlelements');
                //Set the location of the popped window to the urlPath
                $this->objPop->set('location',$helpfile);
                //Set the name of the window
                $this->objPop->set('window_name','helprich');
                //Set the display icon
                $this->icon->setIcon("extended_help");
                //Set the icon to the help_rich icon
                $this->objPop->set('linktext', $this->icon->show());
                //Set the width of the popup window
                $this->objPop->set('width','700');
                //Set the height of teh popup window
                $this->objPop->set('height','550');
                //Set the number of pixels in for the window
                $this->objPop->set('left','100');
                //Set the number of pixels down for the window
                $this->objPop->set('top','100');
                //Set scrollbars to appear automatically
                $this->objPop->set('scrollbars', 'yes');
                //echo $this->objPop->putJs(); // you only need to do this once per page
                //Add a close link
                $ex="<a href=\"javascript:window.self.close();\">";
                //Set the close icon
                $this->icon->setIcon("close");
                $ex.=$this->icon->show()."</a>";
                return $this->objPop->show()."&nbsp;".$ex;
            } else {
                $this->icon->setIcon("help_rich_grey");
                $ex = $this->icon->show()."&nbsp;";
                $ex .= "<a href=\"javascript:window.self.close();\">";
                //Set the close icon
                $this->icon->setIcon("close");
                $ex.=$this->icon->show()."</a>";
                return $ex;
            }
        }
    } //function

    /**
    *                Method to check whether an extended help file exists
    *                
    *                This method first checks whether an extended help file exists in the current language.
    *                If it doesn't exist, check whether it exists in the English (en) subfolder.
    *                If the file exists, return the browser path to the file, else return NULL
    *                
    * @param  string $file   The name of the file (minus the .html) to check for existence
    * @param  string $module The module folder to check for the file
    * @return string |NULL The browser side path to the file
    *                
    public function checkForFile($file, $module, $extension='.html')
    {
        // Get Current Language
        $language = $this->objLanguage->currentLanguage();

        // Instantiate Object to convert between language and ISO Code
        $objLanguageCode =& $this->newObject('languagecode', 'language');
        // Get ISO for current code
        $languageCode=$objLanguageCode->getISO($language);

        // Server Side Path to file
        $helpfilepath=str_replace("\\", "/", $this->objConfig->siteRootPath()) .'modules/'.$module.'/help/'.$languageCode.'/'. $file .$extension;

        // Browser Side Path to File
        $helpfile = $this->objConfig->siteRoot().'modules/'.$module.'/help/'.$languageCode.'/'. $file . $extension;

        // Check if file exists
        if (file_exists($helpfilepath)) {
            // Return browser side path to file
            return $helpfile;
        } else {
            // If file doesn't exist, check for english version
            $helpfilepath=str_replace("\\", "/", $this->objConfig->siteRootPath()) .'modules/'.$module.'/help/en/'. $file .$extension;
            $helpfile = $this->objConfig->siteRoot().'modules/'.$module.'/help/en/'. $file . $extension;

            // If english version exists, return, browser side path ELSE Null
            if (file_exists($helpfilepath)) {
                return $helpfile;
            } else {
                return NULL;
            }
        }

    }
    */
} // class

?>