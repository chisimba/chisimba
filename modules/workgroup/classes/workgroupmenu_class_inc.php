<?php

/**
* Class usermenu extends object.
* @package toolbar
* @filesource
*/

/**
* Admin Menu Class
* This class generates a context lefthand side workgroup navigation menu 
* @author Tohir Solomons, Juliet Mulindwa
* @copyright (c) 2004 University of the Western Cape
* @version 1
*/
class workgroupmenu extends object
{
    /**
	* Constructor method to instantiate objects and get variables
	*/
    function init()
    {
        $this->objLanguage =& $this->getObject('language','language');
        $this->moduleCheck =& $this->newObject('modulesAdmin','modulelist');
        
        $this->objWorkgroup =& $this->getObject('dbworkgroup');
        $this->objContext =& $this->getObject('dbcontext','context');
        
        $this->globalTable = $this->newObject('htmltable','htmlelements');
        $this->globalTable->cellpadding=5;
        $this->globalTable->width='99%';
        
        $objSkin =& $this->getObject('skin','skin');
        $this->icon = $this->getObject('geticon', 'htmlelements');        
    }
    
    /**
    * This method returns the finished menu
    *
    * @return string $menu - the finished menu
    */
    function show()
    {
        $workGroupTitle =& $this->getObject('htmlheading', 'htmlelements');
        $workGroupTitle->type=2;
        $workGroupTitle->str= ucwords($this->objWorkgroup->getDescription($this->objWorkgroup->getWorkgroupId()).' '.$this->objLanguage->code2Txt('word_workgroup'));
        $menu =  $workGroupTitle->show();
        $menu .= $this->createMenuTable();
        
        return $menu;
    }
    
   /**
    * This method checks whether the modules are registered and available for the context, and then adds them to the menu
    * @return string $this->globalTable - the finished table of menu items
    */
    function createMenuTable()
    {        
        
        $this->addNavigationRow(ucwords($this->objLanguage->code2Txt('mod_workgroup_workgrouphome')), 'workgroup', 'workgroup');
        
        // Check if char is registered and add to side bar
        $module = $this->moduleCheck->getModuleInfo('chat');
        if ($module['isreg']) {
            $chatContext = $this->objContext->getContextCode().' ('.$this->objWorkgroup->getDescription($this->objWorkgroup->getWorkgroupId()).')';
            $this->addNavigationRow($this->objLanguage->languageText('mod_chat_name'), 'chat', 'chat', array('action'=>'join','type'=>'workgroup', 'context'=>$chatContext));
        }
        
        // Check if forum is registered and add to side bar
        $module = $this->moduleCheck->getModuleInfo('forum');
        if ($module['isreg']) {
            $this->addNavigationRow($this->objLanguage->languageText('mod_forum_name'), 'forum', 'forum', array('action'=>'workgroup'));
        }
        
        // Check if fileshare is registered and add to side bar
        $module = $this->moduleCheck->getModuleInfo('fileshare');
        if ($module['isreg']) {
            $this->addNavigationRow(ucwords($this->objLanguage->code2Txt('mod_fileshare_name')), 'fileshare', 'fileshare');
        }
        
        $objContextCondition = &$this->getObject('contextcondition','contextpermissions');
        $isContextLecturer = $objContextCondition->isContextMember('Lecturers');
        if ($isContextLecturer) {
            $this->addNavigationRow(ucwords($this->objLanguage->code2Txt('mod_workgroupadmin_name')), 'workgroupadmin', 'workgroupadmin', array('type'=>'workgroupadmin'));
        }
        
        $this->addNavigationRow($this->objLanguage->languageText('mod_workgroup_logout'), 'leavecourse', 'workgroup', array('action'=>'leaveworkgroup'), FALSE);
        
        return $this->globalTable->show();
    }
    
    /**
    * This method adds rows to the global table.
    *
    * @param string $moduleName: Name to be displayed
    * @param string $iconPicture: Icon to be displayed
    * @param string $moduleId: Module URI
    * @param array $linkArray: Additional parameters for URI
    * @param boolean $moduleIcon: Flag to show icon from modules folder or icons folder
    */
    function addNavigationRow($moduleName, $iconPicture, $moduleId=null, $linkArray=null, $moduleIcon = TRUE)
    {
        $this->loadClass('link', 'htmlelements');
        
        $this->globalTable->startRow();
        
        if ($moduleIcon) {
            $this->icon->setModuleIcon($iconPicture);
        } else {
            $this->icon->setIcon($iconPicture);
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