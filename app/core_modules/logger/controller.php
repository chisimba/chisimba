<?php
/* -------------------- stories class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
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
class logger extends controller {
    
    /**
    * Standard init function
    */
    function init()
    {
        //Instantiate the show log class
        $this->showLog = & $this->getObject('logshow');
        //Instantiate the language object
        $this->objlanguage = & $this->getObject('language', 'language');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Set it to log once per session
        //$this->objLog->logOncePerSession = TRUE;
        //Log this module call
        $this->objLog->log();
    }
    
    /**
    * Dispatch method for logger class
    */
    function dispatch()
    {
        $action = $this->getParam("action", NULL);
        switch ($action) {
            case NULL:
                return "menu_tpl.php";
                break;
            case 'sortbydate';
                $ar = $this->showLog->showForUser(NULL, " ORDER BY dateCreated DESC");
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;
            case 'sortbymodule';
                $ar = $this->showLog->showForUser(NULL, " ORDER BY module");
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;
            case 'showmoduleslogged';
                $ar = $this->showLog->showModulesLogged();
                $this->setVarByRef('ar', $ar);
                return "modslogged_tpl.php";
                break;
            case 'showstatsbyuser';
                $ar = $this->showLog->showStatsByUser();
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;
            case 'showstatsbymodule';
                $ar = $this->showLog->showStatsByModule();
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;
            //Set of date cases
            case 'showstatsbydate';
                //Retrieve the timeframe parameter
                $timeframe = $this->getParam("timeframe", NULL);
                //Create and instance of the datepair class for getting timeframes
                $objDate = $this->newObject('datepair', 'datetime');
                switch ($timeframe) {
                    case 'today':
                    case NULL:
                        //Return the datetime for now
                        $now=time();
                        $timeframe = date("Y/m/d",$now);
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
                } # switch
                $ar = $this->showLog->showStatsByDate($timeframe);
                $this->setVarByRef('ar', $ar);
                return "main_tpl.php";
                break;
            default:
                die($this->objLanguage->languageText("phrase_actionunknown").": ".$action);
                break;
        } #switch
            
    }
    

} # end of class

?>