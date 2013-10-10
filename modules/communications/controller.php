<?php
/* -------------------- stories class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
*
* Module class to test the communications module
*
* @author Derek Keats
*/
class communications extends controller {
    public function init()
    {
        $this->objLanguage=& $this->getObject('language', 'language');
        $this->objUser = & $this->getObject('user', 'security');
        $this->online = & $this->getObject('online');
        $this->objIcq = & $this->getObject("icq");
        $this->objYahoo = & $this->getObject("yahoo");

    }
    /**
    *
    * The standard dispatch method for the module. The dispatch() method must
    * return the name of a page body template which will render the module
    * output (for more details see Modules and templating)
    *
    */
    public function dispatch($action)
    {
        $st=$this->objIcq->getStatusIcon($this->objUser->userId(), 'byuserid');
        $yh=$this->objYahoo->getStatusIcon($this->objUser->userId(), 'byuserid');
        $this->setVar('str', 'Admin\'s status'.$st.$yh);
        return 'dump_tpl.php';
    }
}

?>