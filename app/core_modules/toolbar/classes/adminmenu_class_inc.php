<?php
/**
* Class adminmenu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Admin Menu Class
* This class generates a context lefthand side admin navigation menu
*
* @author Tohir Solomons
* @author Paul Scott <pscott@uwc.ac.za>
* @copyright (c) 2004 University of the Western Cape
* @package toolbar
* @version 1
*/
class adminmenu extends object
{
    /**
    * Constructor method to instantiate objects and get variables
    */
    public function init()
    {
        $this->objLanguage= $this->getObject('language','language');
        $this->moduleCheck=$this->newObject('modules','modulecatalogue');
        $this->objUser = $this->getObject('user', 'security');
        $this->objUserPic = $this->getObject('imageupload', 'useradmin');
        $this->globalTable=$this->newObject('htmltable','htmlelements');
        $this->globalTable->cellpadding=5;
        $this->globalTable->width='99%';
        $objSkin =  $this->getObject('skin','skin');
        $this->iconfolder=$objSkin->getSkinUrl()."icons/modules/";
        $this->icon = $this->getObject('geticon', 'htmlelements');
    }

    /**
    * This method returns the finished menu
    *
    * @return string $menu - the finished menu
    */
    public function show()
    {
        $userTitle = $this->getObject('htmlheading', 'htmlelements');
        $userTitle->type=2;
        $userTitle->str=$this->objUser->fullName();
        $menu =  $userTitle->show();
        $menu .= '<p align="center"><img src="'.$this->objUserPic->userpicture($this->objUser->userId() ).'" /></p><br />';
        $menu .= $this->createMenuTable();

        return $menu;
    }

    /**
    * This method checks whether the modules are registered and available for the context, and then adds them to the menu
    * @return string $this->globalTable - the finished table of menu items
    */
    public function createMenuTable()
    {
        // Check if contextadmin is registered
        $module = $this->moduleCheck->getModuleInfo('contextadmin');
        if ($module['isreg']) {
            $this->addNavigationRow(ucwords($module['name']), 'contextadmin', 'contextadmin');
        }
        // Check if useradmin is registered
        $module = $this->moduleCheck->getModuleInfo('useradmin');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'useradmin', 'useradmin');
        }
        // Check if groupadmin is registered
        $module = $this->moduleCheck->getModuleInfo('groupadmin');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'groupadmin', 'groupadmin');
        }
        // Check if permissions is registered
        $module = $this->moduleCheck->getModuleInfo('permissions');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'permissions', 'permissions');
        }
        // Check if moduleadmin is registered
        $module = $this->moduleCheck->getModuleInfo('moduleadmin');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'moduleadmin', 'moduleadmin');
        }
        // Check if createlang is registered
        $module = $this->moduleCheck->getModuleInfo('createlang');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'createlang', 'createlang');
        }
        // Check if extensions is registered
        $module = $this->moduleCheck->getModuleInfo('extensions');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'extensions', 'extensions');
        }
        // Check if languagetext is registered
        $module = $this->moduleCheck->getModuleInfo('languagetext');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'languagetext', 'languagetext');
        }
        // Check if serverstatus is registered
        $module = $this->moduleCheck->getModuleInfo('serverstatus');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'serverstatus', 'serverstatus');
        }
        // Check if viewsource is registered
        $module = $this->moduleCheck->getModuleInfo('viewsource');
        if ($module['isreg']) {
            $this->addNavigationRow($module['name'], 'viewsource', 'viewsource');
        }

        return $this->globalTable->show();
    }

    /**
    * This method adds rows to the global table.
    *
    * @param string $moduleName: Name to be displayed
    * @param string $iconPicture: Icon to be displayed
    * @param string $moduleId: Module URI
    * @param array $linkArray: Additional parameters for URI
    * @param boolean $iconsFolder: If true, get image from icons folder, else get image from the icons/module folder
    * @param boolean $popup: Create a link or a popup window
    */
    public function addNavigationRow($moduleName, $iconPicture, $moduleId=null, $linkArray=null, $iconsFolder=false, $popup=false)
    {
        $this->loadClass('link', 'htmlelements');
        $this->globalTable->startRow();
        if ($iconsFolder) {
            $this->icon->setIcon($iconPicture);
        } else {
            $this->icon->setModuleIcon($iconPicture);
        }
        $this->icon->alt = $moduleName;
        $this->icon->title= $moduleName;
        $this->globalTable->addCell($this->icon->show(), 20, 'absmiddle', 'center');
        $moduleLink = new link($this->uri($linkArray, $moduleId));
        $moduleLink->link = $moduleName;
        $this->globalTable->addCell($moduleLink->show(), null, 'absmiddle');
        $this->globalTable->endRow();
    }
}
?>