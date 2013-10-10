<?php

// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class block_importstudents extends object {

    function init() {
        $this->objDBContext = $this->getObject('dbcontext', 'context');

        $this->objUser = $this->getObject("user", "security");
        $this->title = "Import Students";
        $this->loadClass("link", "htmlelements");
    }

    public function show() {
        $contextcode = $this->objDBContext->getContextCode();
        $link = new link($this->uri(array("action" => "home")));
        if ($this->objUser->isCourseAdmin($contextcode)) {
            $link->link = "Import Manager";
            return $link->show();
        } else {
            return "Import Manager";
        }
    }

}
?>
