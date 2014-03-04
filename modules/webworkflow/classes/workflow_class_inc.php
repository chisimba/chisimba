<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * This class forms the main abstract interface to the Web Workflow engine
 *
 * e.g.to retrieve a document at the eventual endpoint use $objWorkflow->getDocument($wokflowText)
 *(workflow syntax)
 *
 *<workflow>
 *   <open>http://www.google.com</open>
 *   <input name="q">Straw Berry</input>
 *   <click name="btnG"></click>
 *</workflow>
 *
 * The result of executing the workflow should produce the target document.
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Charl Mert <charl.mert@gmail.com>
 *
 */

class workflow extends object
{

    /**
    * objInterpreter- The interpreter object
    *
    * @access private
    * @var object
    */
    protected $objInterpreter;

    /**
    * Class Constructor
    *
    * @access public
    * @return void
    */

    public function init()
    {
        $this->objCurl = $this->getObject('curlwrapper', 'utilities');
        $this->objInterpreter = $this->getObject('interpreter', 'webworkflow');
    }

    /**
     * public method to return a document at the workflows endpoint.
     *
     * @param string $workflow The XML workflow string
     * @return void
     * @access public
     */
    public function getDocument($workflow)
    {
        $resultDoc = $this->objInterpreter->run($workflow);
        return $resultDoc;
    }
    
}
?>