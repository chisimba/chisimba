<?php
//Pear image graph still has a bunch of bugs in it, so lets tone down the error reporting
ini_set("error_reporting", E_ALL & ~E_NOTICE & ~E_WARNING);

//include the pear class that we need
require_once('Image/Graph.php');

if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check


/**
 * Graphing class
 *
 * This is a class of methods that will allow developers to add graphs to their modules
 * It can be used to graph data in a simple and easy way, in a number of different graph formats
 *
 * @access public
 * @author Paul Scott <pscott@uwc.ac.za>
 * @category chisimba
 * @package utilities
 * @copyright AVOIR 2006
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
class graph extends object
{
    /**
     * @example 
     * 
     * // graph example...
     *          $this->objGraph->setup(300, 300);
     *           $this->objGraph->addPlotArea();
     *           $data = array('month' => 'june', 'hits' => '1000');
     *           $param = 'month';
     *           $value = 'hits';
     *           $this->objGraph->addSimpleData($data, $param, $value);
     *           $this->objGraph->labelAxes('hits', 'months');
     *           echo $this->objGraph->show('/var/www/testgraph.png');
     */
    
    /**
     * Public graph variable
     *
     * @var object
     */
    public $graph;

    /**
     * Width of the out put png image
     *
     * @var int
     */
    public $width;

    /**
     * Height of the output png (px)
     *
     * @var int
     */
    public $height;

    /**
     * Plot area object
     *
     * @var object
     */
    public $plotarea;

    /**
     * The plot coordinates
     *
     * @var object
     */
    public $plot;

    /**
     * The dataset to be plotted on the plot area
     *
     * @var object
     */
    public $dataset;

    /**
     * primary axes
     *
     * @var string
     */
    public $xaxis;

    /**
     * primary axes
     *
     * @var string
     */
    public $yaxis;

    /**
     * secondary axes
     *
     * @var string
     */
    public $secXaxis;

    /**
     * secondary axes
     *
     * @var string
     */
    public $secYaxis;

    /**
     * Chisimba framework __construct() alias method
     *
     * @param void
     * @return void
     * @access public
     */
    public function init()
    {

    }

    /**
     * Setup method
     * This involves the constructors of all of the bits and pieces that we will need to draw our graph
     *
     * @param int $width
     * @param int $height
     * @return void
     * @access public
     */
    public function setup($width, $height)
    {
        $this->graph =& Image_Graph::factory('graph', array($width, $height));
        $this->plotarea =& $this->graph->addNew('plotarea');
        $this->dataset =& Image_Graph::factory('dataset');
    }

    /**
     * Method to add simple data to the graph
     *
     * @param array $data
     * @param string $param
     * @param string $value
     * @access public
     * @return void
     */
    public function addSimpleData($data, $param, $value)
    {
        //data should be array('june' => '1000')
        $this->dataset->addPoint($data[$param], $data[$value]);
    }

    /**
     * Add an area to plot the data onto
     *
     * @param string $charttype can be
     * 1. line
     * 2. area
     * 3. bar
     * 4. smooth_line
     * 5. smooth_area
     * 6. step
     * 7. impulse
     * 8. dot or scatter
     * 9. radar
     * 10 candlestick
     *
     * @param string $linecolour
     * @param string $fillcolour
     * @return void
     * @access public
     */
    public function addPlotArea($charttype = 'bar', $linecolour = 'gray', $fillcolour = 'blue@0.2')
    {
        $this->plot =& $this->plotarea->addNew($charttype, &$this->dataset);
        // set a line color
        $this->plot->setLineColor($linecolour);
        // set a standard fill style
        $this->plot->setFillColor($fillcolour);
    }

    /**
     * Method to add labels to the axes
     *
     * @param string $xaxistitle
     * @param string $yaxistitle
     * @return void
     * @access public
     */
    public function labelAxes($xaxistitle = "X - Values", $yaxistitle = "Y - Values")
    {
        $this->xaxis =& $this->plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
        $this->xaxis->setTitle($xaxistitle);
        $this->yaxis =& $this->plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
        $this->yaxis->setTitle($yaxistitle, 'vertical');
        //$this->secYaxis =& $this->plotarea->getAxis(IMAGE_GRAPH_AXIS_Y_SECONDARY);
        //$this->secYaxis->setTitle($secytitle, 'vertical2');
    }

    /**
     * Method to draw the final output to a png file
     *
     * @param string $filename
     * @return boolean
     */
    public function show($filename)
    {
        return $this->graph->done(array('filename' => $filename));
    }
}
?>