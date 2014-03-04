<?php
        class nav extends object
        {
                public function init()
                {
                }

                public function getMenu()
                {
			$checker=$this->getObject("modules","modulecatalogue");
			/*$isRegistered=$checker->checkifRegistered("helloworld");
			if ($isRegistered)
			{
				echo "Module helloworld exists";
			}
			else
			{
				echo "Module Doesn't exist";
			}*/
			$this->loadClass("link","htmlelements");
			$link1=new link($this->uri(array('action'=>'add','menutitle'=>'Add Players'),"football_manager"));
			$link1->link="Add Players";
			$link2=new link($this->uri(array('action'=>'view','menutitle'=>'View Players'),"football_manager"));
                        $link2->link="View Players";
			$link3=new link($this->uri(array('action'=>'search','menutitle'=>'Search Players'),"football_manager"));
                        $link3->link="Search Players";
			
			$menu_str="<OL align='left' type='I'>
				     <li>".$link1->show()."
				     <li>".$link2->show()."
				     <li>".$link3->show()."
				   </OL>";
			return $menu_str; 
                }
        }
?>
