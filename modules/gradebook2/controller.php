<?
/* -------------------- gradebook2 class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * controller class for the gradebook2 module
 * authors: Paul Mungai
 * date: 2010 06 22
 */

class gradebook2 extends controller {
    //user object - security and access rights validation
    public $objUser;

    //language object
    public $objLanguage;

    //form object
    public $objForm;

    //heading object
    public $objHeading;

    /**
     * initilization function - declaration of required objects
     */
    public function init() {
        $this->objUser=& $this->getObject('user','security');
        $this->objLanguage = & $this->getObject('language', 'language');
        $this->objForm = & $this->getObject('form','htmlelements');
        $this->objHeading =& $this->getObject('htmlheading','htmlelements');
        //Load DB Table weightedcolumn class
        $this->objWeightedColumn =& $this->getObject('dbgradebook2_weightedcolumn','gradebook2');
        // Load Context Object
        $this->objContext = $this->getObject('dbcontext', 'context');
        // Store Context Code
        $this->contextCode = $this->objContext->getContextCode();
    }

    /**
     * dispatch() function for the gradebook2 module:
     * providing standard controls for the module's logic and execution
     */
    public function dispatch($action) {
        //get the parameter from the querystring
        $action = $this->getParam("action", NULL);
        //Convert the action into a method 
        $method = $this->__getMethod($action);
        //Return the template determined by the method resulting from action
        return $this->$method();
    }
    private function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }
    private function __actionError()
    {
        $action = $this->getParam("action", NULL);
        $this->setVar('str', "<h3>". $this->objLanguage->languageText("phrase_unrecognizedaction").": " . $action . "</h3>");
        return 'dump_tpl.php';
    }
    /**
     * Method to add a weighted column
     */
    private function __addcolumn()
    {
        $this->setVar('mode', 'add');
        
        return 'editaddweightedcolumn_tpl.php';
    }
    /**
     * Method to edit an existing weighted column
     */
    private function __editcolumn()
    {
        $this->setVar('mode', 'edit');
        return 'editaddweightedcolumn_tpl.php';
    }
    /**
     * Method to save a new weighted column
     */
    private function __savecolumn()
    {
            //Array to contain data that needs to be saved
            $colArr = array();
            $colArr['column_name'] = $this->getParam('column_name', NULL);
            $colArr['contextcode'] = $this->contextCode;
            $colArr['display_name'] = $this->getParam('display_name', NULL);
            $colArr['description'] = $this->getParam('description', NULL);
            $colArr['primary_display'] = $this->getParam('primary_display', NULL);
            $colArr['secondary_display'] = $this->getParam('secondary_display', NULL);
            $colArr['grading_period'] = $this->getParam('grading_period', NULL);
            $colArr['include_weighted_grade'] = $this->getParam('weighted_grade', NULL);
            $colArr['running_total'] = $this->getParam('running_total', NULL);
            $colArr['show_grade_center_calc'] = $this->getParam('grade_center_calc', NULL);
            $colArr['show_in_mygrades'] = $this->getParam('show_in_mygrades', NULL);
            $colArr['show_statistics'] = $this->getParam('showstats_grade_center', NULL);
            $id =  $this->getParam('id', NULL);
            //$id has value if its an edit
            if(empty($id)){
              $id = $this->objWeightedColumn->insertSingle($colArr);
            } else {
              $id = $this->objWeightedColumn->updateSingle($id, $colArr);
            }
            return $this->nextAction('editcolumn', array('id' => $id, 'status' => 'newcolumn'));
    }
}
?>
