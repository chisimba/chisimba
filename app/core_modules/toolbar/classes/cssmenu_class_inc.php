<?php
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
        $this->objLanguage=& $this->getObject('language','language');
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
    	$homeLabel = $this->objLanguage->languageText('word_home');
    	$logoutLabel = $this->objLanguage->languageText('word_logout');
    	$confirmLabel = $this->objLanguage->languageText('phrase_confirmlogout');
        
        $str='<ul id="menuList" class="adxm">';
    	$str .= '<li class="first"><a href="'.$this->uri(null, '_default').'">'.$homeLabel.'</a></li>';
		foreach($this->menu as $key=>$item){
            $this->objLink->link('javascript:;');
            $this->objLink->link=$key.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $str.='<li>'.$this->objLink->show().'<ul>'."\n";
            $counter = 1;
            $numitems = count ($item);
            foreach($item as $link=>$val){
                $this->toolbarIcon->setIcon($link,null,'icons/modules/');
                $this->toolbarIcon->title=$val;
                $this->toolbarIcon->align='left';
                $this->toolbarIcon->extra=' vspace="3" hspace="5" width="17" height="17"';
                $icon=$this->toolbarIcon->show();

                $this->objLink->link($this->uri(array(''),$link));
                $this->objLink->link=$icon.'<span>'.$val.'</span>';

                $valLink=$this->objLink->show();
                
                if ($counter == 1) {
                    $cssclass = 'first';
                } else if ($counter == $numitems) {
                    $cssclass = 'last';
                }
                
                $str.='<li class="'.$cssclass.'">'.$valLink."</li>\r\n";
                $counter++;
            }
            $str.="</ul></li>\n";
        }
        $str .= '<li class="last"><a href="javascript: if(confirm(\''.$confirmLabel.'\')) {document.location= \''.$this->uri(array('action' => 'logoff'), 'security').'\'};">'.$logoutLabel.'</a></li>';
        $str .="</ul>";
        
        return $str;
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
