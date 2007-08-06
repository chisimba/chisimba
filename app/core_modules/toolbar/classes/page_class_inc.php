<?php
/**
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class to retrieve a list of modules to go into the admin or lecturers page.
* @author Megan Watson
* @copyright  (c)2004 UWC
* @package toolbar
* @version 1
*/

class page extends object
{
    /**
    * Method to construct the class
    */
    function init()
    {
        $this->objDbMenu = $this->getObject('dbmenu');
        $this->objTools = $this->getObject('tools');
    }

    /**
    * Method to retrieve a list of modules for lecturers or admin.
    * The method creates an array of categories, each containing a
    * list of modules. If the link is an action within the module then
    * the action is created with its icon and name.
    * @param string $page The page to create. Default = admin.
    * @param string $context TRUE if in context, FALSE if not.
    * @return array $items The list of page items.
    */
    function getPage($page = 'admin', $context = FALSE)
    {
        $modules = $this->objDbMenu->getPageItems($page, $context);

        // Build an array of categories and modules
        if(!empty($modules)){
            $i = 0; $skip = FALSE;
            foreach($modules as $line){
                if(!empty($line['permissions'])){
                    if(!$this->objTools->checkPermissions($line, $context)){
                        $skip = TRUE;
                    }
                }

                if(!$skip){
                    $array = explode('|', $line['category']);
                    $catArray = str_replace('page_','',$array[0]);
                    $catArray = explode(',', $catArray);
                    foreach($catArray as $val){
                        if(!(strpos($val, $page)===FALSE)){
                            $category = explode('_', $val);
                            break;
                        }
                    }

                    // Patch for contextadmin content import/delete links
                    if($category[0] == 'lecturer' && $line['module'] == 'contextadmin'){
                        $objDBContentNodes = $this->getObject('dbcontentnodes','context');
                        if($array[1] == 'delete' || $array[1] == 'exportcontent'){
                            if(!$objDBContentNodes->hasNodes()){
                                $skip = TRUE;
                            }
                        }else if($array[1] == 'import'){
                            if($objDBContentNodes->hasNodes()){
                                $skip = TRUE;
                            }
                        }
                    }

                    if(!$skip){
                        $items[$category[1]][$i]['module'] = $line['module'];

                        if(isset($array[1]) && !empty($array[1])){
                            $items[$category[1]][$i]['action'] = $array[1];
                        }
                        if(isset($array[2]) && !empty($array[2])){
                            $items[$category[1]][$i]['icon'] = $array[2];
                        }
                        if(isset($array[3]) && !empty($array[3])){
                            $items[$category[1]][$i]['name'] = $array[3];
                        }
                        $i++;
                    }
                }
                $skip = FALSE;
            }
            return $items;
        }
        return FALSE;
    }
}
?>