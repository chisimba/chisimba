<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   logger
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/* -------------------- stories class extends controller ----------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 *
 * Logger module controller for KEWL.NextGen. The logger
 * module is responsible for recording and displaying user
 * activity.
 *
 *
 *
 * @author Derek Keats
 *         
 */
class logger extends controller
{
    /**
     * Standard init function
     */
    function init()
    {
        try{
            $this->logDisplay = $this->getObject('logdisplay', 'logger');
            //Instantiate the show log class
            $this->showLog = $this->getObject('logshow');
            //Instantiate the language object
            $this->objlanguage = $this->getObject('language', 'language');
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Set it to log once per session
            //$this->objLog->logOncePerSession = TRUE;
            //Log this module call
            $this->objLog->log();
            $this->objUser = $this->getObject('user', 'security');
        } catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    /**
     * Dispatch method for logger class
     */
    function dispatch()
    {
        $action = $this->getParam("action", NULL);
        switch ($action) {
            case 'sortbydate':
                $ar = $this->showLog->showForUser(NULL, " ORDER BY datecreated DESC");
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;

            case 'sortbymodule':
                $ar = $this->showLog->showForUser(NULL, " ORDER BY module");
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;

            case 'showmoduleslogged':
                $ar = $this->showLog->showModulesLogged();
                $this->setVarByRef('ar', $ar);
                return "modslogged_tpl.php";
                break;

            case 'showstatsbyuser':
                $ar = $this->showLog->showStatsByUser();
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;

            case 'showstatsbycontext':
		$userId=$this->objUser->userId();
                $display = $this->logDisplay->getVisitedPages($userId,$contextcode=NULL,$module='contextcontent');
                $this->setVarByRef('display', $display);
                return 'index_tpl.php';
                break;

            case 'statsbycontext':
		$userId=$this->objUser->userId();
		$contextCode = $this->getParam('contextcode');
                $display = $this->logDisplay->getContextUsers($userId,$contextCode);
                $this->setVarByRef('display', $display);
                return 'index_tpl.php';
                break;

            case 'userstatsbycontext':
		$userId = $this->getParam('userId');
		if(empty($userId)){
			$userId=$this->objUser->userId();
		}
		$contextCode = $this->getParam('contextcode');
                $display = $this->logDisplay->getVisitedPages($userId,$contextCode, $module='contextcontent');
                $this->setVarByRef('display', $display);
                return 'index_tpl.php';
                break;

            case 'showstatsbymodule':
                $ar = $this->showLog->showStatsByModule();
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;
                //Set of date cases

            case 'showstatsbydate':
                //Retrieve the timeframe parameter
                $timeframe = $this->getParam("timeframe", NULL);
                //Create and instance of the datepair class for getting timeframes
                $objDate = $this->newObject('dateandtime', 'utilities');
                switch ($timeframe) {
                    case 'today':
                    case NULL:
                        //Return the datetime for now
                        $timeframe = date("Y-m-d");
                        break;

                    case 'thisweek':
                        //Return the datetime for now
                        $objDate->thisWeek();
                        $timeframe = $objDate->startDate;
                        break;

                    case 'thismonth':
                        //Return the datetime for now
                        $objDate->thisMonth();
                        $timeframe = $objDate->startDate;
                        break;

                    default:
                        die("Error: bad value for timeframe: ".$timeframe);
                        break;
                } // switch
                $ar = $this->showLog->showStatsByDate($timeframe);
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;

            case 'userstats':
                $display = $this->logDisplay->statsByUser();
                $this->setVarByRef('display', $display);
                return 'index_tpl.php';
                break;

            case 'showmoduleinfo':
                $module = $this->getParam('mod');
                $display = $this->logDisplay->moduleInfo($module);
                $this->setVarByRef('display', $display);
                return 'popup_tpl.php';
                break;
                
            case 'modulestats':
                
                $stats = $this->showLog->showStatsByModule();
                
                $objFlashGraphData = $this->newObject('flashgraphdata', 'utilities');
                $objColorGenerator = $this->getObject('randomcolorgenerator', 'utilities');
                
                $objFlashGraphData->graphType = 'pie';
                foreach ($stats as $stat)
                {
                    $objFlashGraphData->addPieDataSet($stat['calls'], '#'.$objColorGenerator->generateColor(), $stat['module']);
                }
                
                /*
                foreach ($stats as $stat)
                {
                    $objFlashGraphData->addDataSet(array($stat['calls']), '#'.$objColorGenerator->generateColor(), 5, 'bar', $stat['module']);
                }*/
                
                echo $objFlashGraphData->show();
                
                break;
            default:
                $display = $this->logDisplay->show();
                $this->setVarByRef('display', $display);
                return 'index_tpl.php';
                break;
        } //switch

    }
} // end of class

?>
