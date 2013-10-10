<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
die( "You cannot view this page directly" );
}
/**
*
* This class is a basic wrapper of the PEAR Image_Graph class,
* used specifically to generate inflation graphs for LRS
*
* @package Award
* @copyright UWC 2006
* @license GNU/GPL
* @author Nic Appleby
* @version $id$
*/

class inflationgraph extends object {

    var $Graph;
    var $Font;
    var $data;
    var $title = 'Title';
    var $xAxis = 'X axis';
    var $yAxis = 'Y axis';
    var $label = 'Label';



    /**
     * Constructor method to set up Graph object
     *
     */
    function init() {
        $this->objConfig = $this->getObject('altconfig','config');
        if (!include_once 'Image/Graph.php') {
            die('Could not find PEAR extension Image_Graph, please install.');
        }
        $this->width = 450;
        $this->height = 300;
        //ini_set('error_reporting','E_ALL & ~E_NOTICE');
    }

    /**
     * method to set labels for various graph attributes
     *
     * @param string $title The graph Title
     * @param string $xAxis The label for the x axis
     * @param string $yAxis The label for the y axis
     * @param stringe $data The label for the actual data
     */
    function setLabels($title,$xAxis,$yAxis,$data) {
        $this->xAxis = $xAxis;
        $this->yAxis = $yAxis;
        $this->title = $title;
        $this->label = $data;
    }

    /**
     * Method to add the data to the graph
     *
     * @param array $data array of co-ordinate pairs to plot on the graph
     */
    function addData($data) {
        $this->data = $data;
    }

    /**
     * Method to generate and show the graph
     *
     */
    function show() {
        $names = array();
        $values = array();
        foreach ($this->data as $i) {
            $names[] = $i['x'];
            $values[] = is_numeric($i['y']) ? $i['y'] : 0;
        }
        $params = new stdClass();
        $params->cht = 'bvs';
        $params->chs = $this->width.'x'.$this->height;
        $params->chd = 't:'.implode(',', $values);
        $params->chco = '9C0000';
        $params->chds = min($values).','.max($values);
        $params->chxt = 'x,x,y,y';
        $params->chxl = '0:|'.implode('|', $names).'|1:|'.$this->xAxis.'|3:|'.$this->yAxis;
        $params->chbh = 40;
        $params->chtt = $this->title;
        $params->chxp = '1,50|3,50';
        $params->chdl = $this->label;
        $params->chco = '9C0000';
        $params->chdlp = 'bv';
        header('Location: https://chart.googleapis.com/chart?'.http_build_query($params));
    }
}
?>
