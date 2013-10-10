<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
die( "You cannot view this page directly" );
}
/**
*
* This class is a Chisimba wrapper for the AGC (Advanced Graphing Class)
* by Zack Bloom.
*
* @package lrs
* @copyright UWC 2008
* @license GNU/GPL
* @author Nic Appleby
* @version $id:
*/

class agcgraph extends object {

	var $graph;
	var $Dataset = array();
	var $colors = array('0'=> array(255,0,0,0),
	                   '1'=> array(0,255,0,0),
	                   '2'=> array(0,0,255,0),
	                   '3'=> array(255,255,0,0),
	                   '4'=> array(0,255,255,0),
	                   '5'=> array(255,0,255,0),
	                   '6'=> array(0,0,0,0),
	                   '7'=> array(128,255,128,0),
	                   '8'=> array(255,128,128,0),
	                   '9'=> array(128,128,255,0));
	var $pointStyles = array('0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'7','6'=>'8','7'=>'5','8'=>'6','9'=>'0');
	var $width;
	var $height;
	var $type;
	var $showKey;
	var $xLabel;
	var $yLabel;
	var $title;

	/**
	 * Constructor method to set up Graph object
	 *
	 */
	public function init() {
		ini_set('error_reporting','E_ALL & ~E_NOTICE');
		$this->objConfig = $this->getObject('altconfig','config');
		require_once($this->getResourceUri('advgraph5.class.php'));
		$this->width = 700;
		$this->height = 500;
	}

	/**
	 * Method to add the data to the graph
	 *
	 * @param array $arrData array of co-ordinate pairs to plot on the graph
	 */
	public function addData($arrData,$title) {
		$this->dataset[] = $arrData;
		$this->titles[] = $title;
	}
	
	public function setType($type) {
	    $this->type = $type;
	}
	
	public function showKey($bool) {
	    $this->showKey = $bool;
	}
	
	/**
	 * Method to add the data to the graph
	 *
	 * @param array $arrData an array of arrays of co-ordinate pairs to plot on the graph
	 */
	public function addMultipleDatasets($arrData) {
	    //($arrData);
		foreach($arrData as $title => $dataset){
			$this->dataset[] = $dataset;
			$this->titles[] = $title;
		}
		//var_dump($this->titles);
	}

	/**
	 * Method to generate and show the graph
	 *
	 */
	public function show() {
		$this->graph = new graph($this->width,$this->height);
        $this->graph->setProp("showval",true);
        $this->graph->setProp("showgrid",true);
		$this->graph->setProp("xlabel",$this->xLabel);
        $this->graph->setProp("ylabel",$this->yLabel);
        $this->graph->setProp("title",$this->title);
        $this->graph->setProp("titlesize",11);
        $this->graph->setProp("labelsize",11);
        $this->graph->setProp("keywidspc",10);
        $this->graph->setProp("keyinfo",3);
        $this->graph->setProp("type",$this->type);
        $this->graph->setProp("showkey",$this->showKey);
        //$this->graph->setProp("noheader",true);
		$this->graph->setProp("font",$this->getResourceUri('times.ttf'));
		$this->graph->setProp("keyfont",$this->getResourceUri('times.ttf'));
		
        //$this->graph->setProp("scale",'date');
        //$this->graph->setProp("showyear",true);
        $i = $j = 0;
        //var_dump($this->dataset);
		foreach ($this->dataset as $data) {
			$this->graph->setProp("key", $this->titles[$i],$i);
            $this->graph->setProp("color",$this->colors[$i],$i);
            $this->graph->setProp("pointstyle",$this->pointStyles[$i],$i);
            $xincs = count($data);
            foreach ($data as $pair) {
                //if ($j == 0) $this->graph->setProp("startdate",$pair['x']);
                //if ($j == 11) $this->graph->setProp("enddate",$pair['x']); 
                $this->graph->addPoint($pair['y'],$pair['x'],$i);
                //$this->graph->addPoint($pair['y'],-5,$i);
                $j++;
            }
		    $i++;
		}
		//echo $xincs;
		$this->graph->setProp("xincpts",$xincs-1);
        
		// output the Graph
		$this->graph->graph();
        $this->graph->showGraph();
		ini_restore('error_reporting');
	}
}
?>