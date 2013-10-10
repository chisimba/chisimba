<?php
class navigation extends object {
  function init() {
    $this->loadclass('link','htmlelements');
  }
  function getNavigationMenu() {
    //create a list
    $navigation = "";
    
    //create an array hardcoded navigation links
    $links = array();
    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>'gen9Srv49Nme21_5122_1250702478')));
    $link->link = "Home";
    $links[] = $link;
    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>'gen9Srv49Nme21_1530_1250702959')));
    $link->link = "Contact Us";
    $links[] = $link;
    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>'gen9Srv49Nme21_1530_1250702959')));
    $link->link = "Contact Us";
    $links[] = $link;
    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>'gen9Srv49Nme21_1530_1250702959')));
    $link->link = "Contact Us";
    $links[] = $link;
    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>'gen9Srv49Nme21_1530_1250702959')));
    $link->link = "Contact Us";
    $links[] = $link;
    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>'gen9Srv49Nme21_1530_1250702959')));
    $link->link = "Contact Us";
    $links[] = $link;
    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>'gen9Srv49Nme21_1530_1250702959')));
    $link->link = "Contact Us";
    $links[] = $link;
    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>'gen9Srv49Nme21_1530_1250702959')));
    $link->link = "Contact Us";
    $links[] = $link;
    //=================end of links===============================
    
    $navigation .= '<div id="leftnavigation"><ul id="leftnavigationmenu">';
    foreach ($links as $link) {
      $link = $link->show();
      $navigation .= "<li>$link</li>";
    }
    $navigation .= "</ul></div>";
    return $navigation;
  }
}

?>