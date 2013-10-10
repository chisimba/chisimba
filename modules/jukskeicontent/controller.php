<?php

if(!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

class jukskeicontent extends controller {
  public function init() {
    
  }
  
  public function dispatch($action) {
    switch($action) {
      case "viewtopic": return "viewtopic_tpl.php";
      case "home": return "home_tpl.php";
      case "viewarticle": return "viewarticle_tpl.php";
    }
    return "home_tpl.php";
  }
  
  function requiresLogin(){
    return false;
  }

}


?>