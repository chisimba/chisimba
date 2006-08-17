<?
/**
* Class cssmenu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for creating and displaying a menu using css style sheets.
* @author Megan Watson
* @copyright (c)2004 UWC
* @package toolbar
* @version 1
*/

class cssmenu extends object
{
    var $menu=array();

    /**
    * Method to construct the class
    */
    function init()
    {
        $this->objSkin=& $this->getObject('skin','skin');
        $this->objLink=& $this->getObject('link','htmlelements');
        $this->toolbarIcon=& $this->getObject('geticon','htmlelements');
        $this->objLayer=& $this->getObject('layer','htmlelements');
    }

    /**
    * Method to build the menu in html for display purposes.
    * @param string $iconPath The path to the icons within the skins
    * icons folder. Default: false.
    * @return string $menu The menu
    */
    function show()
    {
    	$str='<ul id="nav" >';
    	$str .= '<li class="first"><a href="'.$this->uri(null, '_default').'">Home</a></li>';
		foreach($this->menu as $key=>$item){
            $this->objLink->link('javascript:;');
            $this->objLink->link=$key.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $str.='<li >'.$this->objLink->show()."<ul>\n";
            foreach($item as $link=>$val){
                $this->toolbarIcon->setIcon('modules/'.$link);
                $this->toolbarIcon->title=$val;
                $this->toolbarIcon->align='left';
                $this->toolbarIcon->extra=' vspace="3" hspace="5" width="17" height="17"';
                $icon=$this->toolbarIcon->show();

                $this->objLink->link($this->uri(array(''),$link));
                $this->objLink->link=$icon.$val;

                $valLink=$this->objLink->show();
                $str.="<li>".$valLink."</li>\n";

            }
            $str.="</ul></li>\n";
        }
        $str .= '<li class="last"><a href="javascript: if(confirm(\'Are you sure you want to logout?\')) {document.location= \''.$this->uri(array('action' => 'logoff'), 'security').'\'};">Logout</a></li>';
        $str .="</ul>";
        $menu=$str;
        return $menu;
	}

    /**
    * Method to add a menu heading.
    * @param string $str Name of the menu header
    * @return
    */
    function addHeader($str)
    {
        if(!empty($str)){
            if (array_key_exists($str, $this->menu)){
            }else{
                $this->menu[$str] = array();
            }
        }
    }

    /**
    * Method to add a menu item under a menu heading.
    * @param string $menuhead Name of the heading under which to place the item.
    * @param string $str The menu item.
    * @return
    */
    function addMenuItem($menuhead,$str,$link='javascript:;')
    {
        if(!empty($str)){
            if(array_key_exists($menuhead, $this->menu)){
                $this->menu[$menuhead][$link]=$str;
            }
        }
    }
}
?>