<?php
/**
* Class toolbar extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* The toolbar class provides functions used in displaying the menu for KEWL.nextgen.
*
* The class provides the following methods:
* 1) A method to determine whether the user is logged in.
* 2) A method to display an icon for accessing the instant messaging.
* 3) A method to display the breadcrumbs for the site.
*
* @author Megan Watson
* @copyright (c)2004 UWC
* @package toolbar
* @version 0.9
*/
class toolbar extends object
{
    /**
    * Method to construct the class
    */
    function init()
    {
        $this->objIcon= $this->newObject('geticon', 'htmlelements');
        $this->objLink= $this->newObject('link', 'htmlelements');
        $this->objSkin= $this->newObject('skin','skin');
        $this->objLanguage= $this->getObject('language','language');
        $this->table=$this->newObject('htmltable','htmlelements');
    }

    /**
    * Method to perform a security check to ensure that user is logged
    * in before toolbar is visible.
    * @return bool $var
    */
    function check()
    {
        $mod = $this->getParam('module');
        $act = $this->getParam('action');
        if(!($mod=="security" && $act=="logoff")){
            $objSecurity= $this->getObject("user","security");
            $var = $objSecurity->isLoggedIn();
            return $var;
        }
        return false;
    }


    /**
    * Method to provide the breadcrumbs on the menu.
    * @return string $nav The breadcrumbs
    */
    function navigation()
    {
        $this->objConfig= $this->getObject('altconfig','config');
        $str = $this->objLanguage->languageText("mod_toolbar_home",'security', "home");
        $this->objLink->style="cursor:hand";
        $this->objLink->link=$str;
        $this->objLink->link($this->uri(null,'_default'));
        $nav = $this->objLink->show();

        // decode URL
        $list = array();
        $actionlist = array();
        $module = array();
        $actionname = array();
        $show = "";
        $name = $this->getParam('module');

        if($name){
            array_push($module, $name);
            array_push($list, 'module');
        }
        $action = $this->getParam('action');

        if($action){
            array_push($actionlist, $action);
            array_push($list, 'action');
        }
        $spacer = " &raquo; ";

        if(is_array($list))
        {
            foreach($list as $temp)
            {
                if($temp == 'module'){
                    $modname = array_pop($module);
                    if($modname != "_default" && $modname != "postlogin"){
                        $nav .= $spacer;
                        $show = $modname;
                    }
                }
                if($temp == 'action'){
                    $actionname = array_pop($actionlist);
                    $nav .= $spacer;
                    $show = $actionname;
                }

                // Save navigation list in variable for show in table
                if($temp == 'action'){
                    $nav .= $show;
                } else {
                    $this->objLink->style="cursor:hand";
                    $this->objLink->link=$show;
                    $this->objLink->link($this->uri(array('action'=>$actionname), $modname));
                    $nav .= $this->objLink->show();
                }
            }
        }
        return $nav;
    }
}
?>