<?php
/**
* Class academicsidemenu extends object.
* @package tzacademics
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 *Class for creating the side menu for the module
 *
 * @author boniface chacha <bonifacechacha@gmail.com>
 */
class academicsidemenu extends object{
   public $menu;
   public $menuItems=array();
   public $lang;

   public function  init() {

       $this->menu=$this->getObject('cssmenu','toolbar');
       $this->lang=$this->getObject('language','language');


    }

    public function loadMenu($menuitems){

        foreach ($menuitems as $menuitem)
        {
            
            $this->menu->addHeader($menuitem['header']);
            
            foreach ($menuitem['items'] as $item){
                $this->menu->addMenuItem($menuitem['header'],$item[0],$item[1]);
            }
        }
   }

   private function createMenu(){
       $menuBox=  $this->getObject('featurebox', 'navigation');
       return $menuBox->show('ACADEMICS',  $this->menu->show());
   }

   public function show(){
       return $this->createMenu();
   }
}
?>
