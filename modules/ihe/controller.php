<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Module to encapsulate the stories module.
 * Creates a sidebar navigation system to navigate the stories
 * @author Dean Wookey
 */

class ihe extends controller {
  function init() {
  }
  
  function dispatch($action) {
    switch ($action) {
      case "viewstory" : {
        $id = $this->getParam('id', NULL);
        $objStories =  $this->getObject('sitestories', 'stories');
        $this->setVar('str', $objStories->fetchStory($id));
        return 'view_tpl.php';
      }
      default: {
        $objStories =  $this->getObject('sitestories', 'stories');
        $this->setVar('str', $objStories->fetchStory('gen9Srv49Nme21_5122_1250702478'));
        return 'view_tpl.php';
      }
    }
  }
  function requiresLogin(){ //override default behaviour. Makes module accesible to any user.
    return FALSE;
  }
}
 
 
?>
 