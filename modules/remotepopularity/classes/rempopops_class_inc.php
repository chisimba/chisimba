<?php
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
// end security check

/**
 * Class to handle remotepopularity elements
 *
 * @author    Paul Scott
 * @copyright GNU/GPL, AVOIR
 * @package   blog
 * @access    public
 */
class rempopops extends object
{

	/**
     * Description for public
     * @var    object
     * @access public
     */
	public $objConfig;
	
	/**
     * Standard init function called by the constructor call of Object
     *
     * @param  void  
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
			$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}
	
	public function getFullGraph()
	{
		$objFlashGraph = $this->getObject('flashgraph', 'utilities');
 		$objFlashGraph->dataSource = $this->uri(array('action'=>'getdata'));
 		$objFlashGraph->width = '100%';
 		//$objFlashGraph->height = '100%';
 		$graph = $objFlashGraph->show();
 		
 		return $graph; 
	}
	
	public function getTopDownloads($number = 5)
	{
		$this->objDbPop = $this->getObject('dbpopularity');
        $objFlashGraph = $this->getObject('flashgraph', 'utilities');
 		$objFlashGraph->dataSource = $this->uri(array('action'=>'gettopdata', 'number' => 5));
 		$objFlashGraph->width = "100%";
 		//$objFlashGraph->height = "200";
 		$graph = $objFlashGraph->show(); 
 		
 		return $graph;
	}

	public function getFullDataSrc()
	{
		$this->objDbPop = $this->getObject('dbpopularity');
		$colours = $this->getObject('websafecolours', 'utilities');
        $objFlashGraphData = $this->newObject('flashgraphdata', 'utilities');
 		$objFlashGraphData->graphType = 'pie';
 		// Get the unique names of the modules
 		$mods = $this->objDbPop->getModList();
 		foreach($mods as $mod)
 		{
 			// get the record count
 			$count = $this->objDbPop->getRecCount($mod);
 			$colour = $colours->getRandomColour();
 			$objFlashGraphData->addPieDataSet($count, $colour, $mod);
 		}
 		
 		$graphdata = $objFlashGraphData->show();
 		return $graphdata;
	}
}
?>