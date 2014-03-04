<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_library extends object
{

    public function init()
    {
       $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->loadClass('treenode','tree');
        
        $this->userId = $this->objUser->userId();
    }
    
    public function show()
    {
      $library = $this->objLanguage->languageText('mod_library_library', 'library');
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'library'), 'text'=>$library, 'preview'=>''));
        
        return $rootNode;
        
    }
    
    /**
     * 
     *Method to get a set of links for a context
     *@param string $contextCode
     * @return array
     */
    public function getContextLinks($contextCode)
    { 
      $library = $this->objLanguage->languageText('mod_library_library', 'library');
                
        $adminArr = array();
        $adminArr['menutext'] = $library;
        $adminArr['description'] = $library;
        $adminArr['itemid'] = '';
        $adminArr['moduleid'] = 'library';
        $adminArr['params'] = array();
        
        $returnArr = array();
        $returnArr[] = $adminArr;
        
        return $returnArr;
          
    }
    
}

?>