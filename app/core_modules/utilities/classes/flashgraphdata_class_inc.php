<?php

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
 * Flash Graphing Data class
 *
 * This is a class of methods that will allow developers to add flash-based graphs to their modules
 * It wraps the Open Flash Charts project - http://teethgrinder.co.uk/open-flash-chart/
 *
 * This class only generates the data that gets displayed in the flash graph
 *
 * @examples
 *
 *
 * Sample Line Graph:
 * 
 * $objFlashGraphData = $this->newObject('flashgraphdata');
 * $objFlashGraphData->graphType = 'line';
 * $objFlashGraphData->setupXAxisLabels(array('Jan', 'Feb', 'March'));
 * $objFlashGraphData->setupYAxis('Rainy Days', NULL, NULL, 10, 5);
 * $objFlashGraphData->addDataSet(array(4, 5, 4), '#3334AD', 5, 'line', 'Cape Town');
 * $objFlashGraphData->addDataSet(array(6, 7, 2), '#00ff00', 5, 'line_dot', 'Johannesburg');
 * $objFlashGraphData->addDataSet(array(1, 4, 3), '#9900CC', 5, 'line_hollow', 'Durban');
 * echo $objFlashGraphData->show();
 *
 *
 * Sample Bar Graph:
 * 
 * $objFlashGraphData = $this->newObject('flashgraphdata');
 * $objFlashGraphData->graphType = 'bar';
 * $objFlashGraphData->setupXAxisLabels(array('Jan', 'Feb', 'March'));
 * $objFlashGraphData->setupYAxis('Rainy Days', NULL, NULL, 10, 5);
 * $objFlashGraphData->addDataSet(array(4, 5, 4), '#3334AD', 50, 'bar', 'Cape Town');
 * $objFlashGraphData->addDataSet(array(6, 7, 2), '#00ff00', 50, 'glassbar', 'Johannesburg');
 * $objFlashGraphData->addDataSet(array(1, 4, 3), '#9900CC', 50, 'sketchbar', 'Durban');
 * echo $objFlashGraphData->show();
 *
 *
 * Sample Pie Graph:
 * 
 * $objFlashGraphData = $this->newObject('flashgraphdata');
 * $objFlashGraphData->graphType = 'pie';
 * $objFlashGraphData->addPieDataSet(7, '#3334AD', 'Cape Town');
 * $objFlashGraphData->addPieDataSet(5, '#00ff00', 'Durban');
 * $objFlashGraphData->addPieDataSet(6, '#9900CC', 'Johannesburg');
 * echo $objFlashGraphData->show();
 * 
 * 
 * @access public
 * @author Tohir Solomons <tsolomons@uwc.ac.za>
 * @category chisimba
 * @package utilities
 * @copyright AVOIR 2006
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

// Load Required Resource
require_once($this->getResourcePath('openflashchart/1.9.6/open-flash-chart.php', 'utilities'));

class flashgraphdata extends object
{
    
    /**
     * @var string $graphType - Type of graph to be displayed - either bar, line or pie
     * @access public
     * 
     * It is important to set this first as it affects the type of charts/functions you can use
     */
    public $graphType = 'bar';
    
    /**
     * @var array $availableTypes List of Chart Types Available
     * @access private
     * 
     * Note: Not all of them can be used simultaneously
     *    - line, bar, line_dot, line_hollow and area_hollow are only available to LINE Graph Type
     *    - bar, 3dbar, glassbar, fadebar ans sketchbar are only available to BAR Graph Type
     *    
     *    - Pie Graphs uses the addPieDataSet function to add values
     */
    private $availableTypes = array('line', 'bar', '3dbar', 'line_dot', 'line_hollow', 'area_hollow', 'glassbar', 'fadebar', 'sketchbar');
    
    /**
     * @var array $dataSets - Variable to hold datasets that are added to the graph
     * @access private
     */
    private $dataSets=array();
    
    /**
     * @var array $pieDataSets - Variable to hold datasets that are added to a graph
     * @access private
     */
    private $pieDataSets=array();
    
    /**
     * Constructor
     */
    public function init()
    {
        
    }
    
    /**
     * Method to add a data set - Used for Bar and Line Graph Types
     *
     * @param array $data Array of Values for the Data Set
     * @param string $color Hex Value of the color in which the bar/line will be rendered
     * @param int $width Width of the Bar/Graph - For Line graphs this is width, for bar graphs original opacity
     * @param string $type Chart Type for the Data Set
     * @param string $legendKey Legend for the Data Set
     * @param string $outlineColor Outline Color for the Bar - used for bar, glassbar and sketchbar
     *
     */
    public function addDataSet($data, $color, $width=10, $type='bar', $legendKey=NULL, $outlineColor=NULL)
    {
        // Convert Type to lowercase
        $type = strtolower($type);
        
        // Check that type is Valid, if not, set to bar
        if (!in_array($type, $this->availableTypes)) {
            $type = 'bar';
        }
        
        // Add to DataSet Array
        $this->dataSets[] = array('type'=>$type, 'width'=>$width, 'legend'=>$legendKey, 'color'=>$color, 'outlinecolor'=>$outlineColor, 'data'=>$data);
    }
    
    /**
     * Method to add a Pie Data Set/Slice
     * @param int $data Value of the Pie Slize
     * @param string $color Color of the Pie Slize
     * @param string $legend Legend of the Pie Slize
     */
    public function addPieDataSet($data, $color, $legend)
    {
        // Add to Pie Data Set Array
        $this->pieDataSets[] = array('legend'=>$legend, 'color'=>$color, 'data'=>$data);
    }
    
    /**
     * Method to set the labels of the X Axis
     * @param array $labels Labels of the X Axis
     */
    public function setupXAxisLabels($labels)
    {
        $this->xLabels = $labels;
    }
    
    /**
     * Method to setup the format of the Y Axis
     * @param string $legend Legend for the Y Axis
     * @param string $legendColor Color of the Legend for the Y Axis
     * @param int $legendFontSize Font Size of the Legend for the Y Axis
     * @param int $maxStepValue Maximum Value of on the Y Axis
     * @param int $numTicks Number of Ticks on the Y Axis - Value of Ticks = $maxStepValue / $numTicks
     */
    public function setupYAxis($legend, $legendColor='#000000', $legendFontSize='12', $maxStepValue=NULL, $numTicks=NULL)
    {
        $this->yLegend = $legend;
        $this->yLegendColor = $legendColor == '' ? '#000000' : $legendColor;
        $this->legendFontSize = $legendFontSize == '' ? '12' : $legendFontSize;
        $this->yMaxStepValue = $maxStepValue;
        $this->yNumTicks = $numTicks;
    }
    
    /**
     * Method to render the data for the graph
     * @return str
     */
    public function show()
    {
        // Instantiate Graph Object
        $g = new graph();
        
        // Set X Axis Labels
        if (isset($this->xLabels)) {
            $g->set_x_labels($this->xLabels);
        }
        
        // Set Y Axis Legend
        if (isset($this->yLegend)) {
            $g->set_y_legend($this->yLegend, $this->legendFontSize, $this->yLegendColor );
        }
        
        // Set Y Axis Max Value
        if (isset($this->yMaxStepValue)) {
            $g->set_y_max($this->yMaxStepValue);
        }
        
        // Set Number of Y Axis Tics
        if (isset($this->yNumTicks)) {
            $g->y_label_steps($this->yNumTicks);
        }
        
        // Generate Graph Type
        switch ($this->graphType)
        {
            case 'line':
                $this->_generateLineGraph($g);
                break;
            
            case 'pie':
                $this->_generatePieGraph($g);
            
            default :
                $this->_generateBarGraph($g);
                break;
        }
        
        // Return $data
        return $g->render();
    }
    
    /**
     * Method to generate a Pie Graph
     *
     * @param object $graphObject Graph Object
     * @access private
     */
    private function _generatePieGraph(&$graphObject)
    {
        // Setup Pie Object
        $graphObject->pie(60,'#505050','#000000');
        
        // Create Empty Arrays
        $data = array();
        $legends = array();
        $colors = array();
        
        // Transfer Data from Data Sets to Arrays
        foreach ($this->dataSets as $dataSet)
        {
            $data[] = $dataSet['data'];
            $legends[] = $dataSet['legend'];
            $colors[] = $dataSet['color'];
        }
        
        // Add Data and Legends
        $graphObject->pie_values($data, $legends);
        
        // Add Colors
        $graphObject->pie_slice_colours($colors);
        
        // Add Tooltips
        //$graphObject->set_tool_tip( '#val#%' );
        
    }
    
    /**
     * Method to generate a Line Graph
     *
     * @param object $graphObject Graph Object
     * @access private
     */
    private function _generateLineGraph(&$graphObject)
    {
        // List of Valid Chart Types for a Line Graph
        $lineGraphTypes = array ('line', 'line_dot', 'line_hollow', 'area_hollow', 'bar');
        
        // Loop through Data Sets
        foreach ($this->dataSets as $dataSet)
        {
            // Check that Chart Type is Valid, Else set to 'line'
            if (!in_array($dataSet['type'], $lineGraphTypes)) {
                $dataSet['type'] = 'line';
            }
            
            // Add Data to Graph
            $graphObject->set_data($dataSet['data']);
            
            // Set Data Chart Type
            switch($dataSet['type'])
            {
                default:
                    $graphObject->{$dataSet['type']}($dataSet['width'], $dataSet['color'], $dataSet['legend'], 10);
                    break;
                case 'line_hollow':
                case 'line_dot':
                    $graphObject->{$dataSet['type']}($dataSet['width'], 10, $dataSet['color'], $dataSet['legend'], 10);
                    break;
                case 'area_hollow':
                    $graphObject->{$dataSet['type']}($dataSet['width'], $dataSet['width']*2, 25, $dataSet['color'], $dataSet['legend'], 10);
                    break;
            }
            
        }
    }
    
    /**
     * Method to generate a Bar Graph
     *
     * @param object $graphObject Graph Object
     * @access private
     */
    private function _generateBarGraph(&$graphObject)
    {
        // Loop through Data Sets
        foreach ($this->dataSets as $dataSet)
        {
            // Add Data Set to Graph
            $graphObject->data_sets[] = $this->_generateBarDataType($dataSet);
            
            // If Chart Type is 3D, Set Axis to be 3D
            if ($dataSet['type'] == '3dbar') {
                $graphObject->set_x_axis_3d( 12 );
                $graphObject->x_axis_colour( '#909090', '#ADB5C7' );
            }
        }
    }
    
    /**
     * Method to Generate a Bar Type Chart
     * @param array $dataSet
     */
    private function _generateBarDataType($dataSet)
    {
        // Determine which function/class to use to render the chart type, and render it
        switch ($dataSet['type'])
        {
            default: return $this->_renderBarData($dataSet, 'bar_outline');
            case 'glassbar': return $this->_renderBarData($dataSet, 'bar_glass');
            case 'sketchbar': return $this->_renderSketchBarData($dataSet);
            case '3dbar': return $this->_render3DBarData($dataSet, 'bar_3D');
            case 'fadebar': return $this->_render3DBarData($dataSet, 'bar_fade');
        }
    }
    
    /**
     * Method to render either a bar or glassbar chart type
     * @param array $dataSet Data Set Info
     * @param string $class Class to be used 
     */
    private function _renderBarData($dataSet, $class='bar_outline')
    {
        $bar = new $class($dataSet['width'], $dataSet['color'], $dataSet['outlinecolor']);
        
        if ($dataSet['legend'] != NULL) {
            $bar->key($dataSet['legend'], 10 );
        }
        
        $bar->data = $dataSet['data'];
        
        return $bar;
    }
    
    /**
     * Method to render either a 3dbar or fadebar chart type
     * @param array $dataSet Data Set Info
     * @param string $class Class to be used 
     */
    private function _render3DBarData($dataSet, $class='bar_3D')
    {
        $bar = new $class($dataSet['width'], $dataSet['color']);
        
        if ($dataSet['legend'] != NULL) {
            $bar->key($dataSet['legend'], 10 );
        }
        
        $bar->data = $dataSet['data'];
        
        return $bar;
    }
    
    /**
     * Method to render a sketchbar chart type
     * @param array $dataSet Data Set Info 
     */
    private function _renderSketchBarData($dataSet)
    {
        $bar = new bar_sketch($dataSet['width'], 7, $dataSet['color'], $dataSet['outlinecolor']);
        
        if ($dataSet['legend'] != NULL) {
            $bar->key($dataSet['legend'], 10 );
        }
        
        $bar->data = $dataSet['data'];
        
        return $bar;
    }
}
?>