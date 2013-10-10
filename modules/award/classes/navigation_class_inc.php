<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
*  This class provides the left hand navigation menu for the LRS system
*
* @author Nic Appleby
* @copyright UWC 2007
* @license GNU/GPL
* @package LRS
*/

class navigation extends dbtable {

    /**
     * The id of the currently selected navigation panel
     *
     * @var string $selected
     */
    var $selected;

    /**
     * The array of categories in the navigation bar
     *
     * @var array $categories
     */
    var $categories;

    /**
     * Standard init method
     */
    function init() {
        parent::init('tbl_award_navigation');
        $this->objConfig = $this->getObject('altconfig','config');
        $this->objUser = $this->getObject('user','security');
        if (empty($selected)) {
            $selected = 'init_1';
        }


    }

    function addFromDb() {
        if ($this->objUser->isAdmin()) {
            $this->accesslevel = 3;
        } else {
            if ($this->objUser->isLoggedIn()) {
    	        $this->accesslevel = 2;
    	    } else {
    	        $this->accesslevel = 1;
    	    }
    	}
    	$categories = $this->getAll("ORDER BY id ASC");
    	foreach ($categories as $cat) {
    	    if ($this->accesslevel >= $cat['accesslevel']) {
    	        $this->categories[] = $cat;
    	    }
    	}
    }

    /**
     * Method to render the class as an actual navigation menu
     *
     * @return string
     */
    function show() {
        if (empty($this->selected)) {
            $this->selected = 'init_1';
        }
        $str = '';//'<table cellpadding=0 cellspacing=0>';//'<ul id="nav-secondary" class="twolevel">';
        $cssClass = 'class = "unselected"';
        //loop through the nodes

        foreach($this->categories as $node) {

            $colour = 'gray';
            $name = ucwords($node['name']);
            $uri = $this->uri(array('action'=>$node['action'],'selected'=>$node['id']),'award');
            $src = $this->objConfig->getskinRoot();
            if($node['id'] == $this->selected) {
                $cssClass = ' class="selected" ';          
            }
            $str.= "<div $cssClass onclick='javascript:location=\"$uri\";'><a href='$uri' class='nav'>$name</a></div>";
            $cssClass = 'class = "unselected"';
        }
        //$str .='</table>';
        return $str;
    }
}

?>