<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
ini_set("max_execution_time", -1);
// end security check
class beautifier extends controller
{
    public $objBeauty;
    public $objLog;
    public $objLanguage;

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objBeauty = $this->getObject('addbeauty');
            //Get the activity logger class
            //$this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            //$this->objLog->log();
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
    /**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
    public function dispatch($action = Null)
    {
        //$this->setLayoutTemplate('beautifier_layout_tpl.php');

        switch ($action) {
            default:
                $mod = $this->getParam('mod');
                if ($mod == '') {
                    return 'error_tpl.php';
                    exit;
                } else {
                    try {
                        $this->objBeauty->beautify($mod);
                        return 'success_tpl.php';
                    }
                    catch(customException $e) {
                        customException::cleanUp();
                    }
                }
        }
    }
}
?>