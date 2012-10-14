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
 * Flash Graphing class
 *
 * This is a class of methods that will allow developers to add flash-based graphs to their modules
 * It wraps the Open Flash Charts project - http://teethgrinder.co.uk/open-flash-chart/
 *
 * This class only displays the flash object. Developers are still required to use the flashgraphdata object
 * to build the data for the graph
 *
 * @example:
 *
 * $objFlashGraph = $this->getObject('flashgraph');
 * $objFlashGraph->dataSource = $this->uri(array('action'=>'data'));
 * echo $objFlashGraph->show();
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
require_once($this->getResourcePath('openflashchart/1.9.6/open_flash_chart_object.php', 'utilities'));

class flashgraph extends object
{

    /**
     * Width of the flash graph
     *
     * @var int
     */
    public $width=500;

    /**
     * Height of the flash graph
     *
     * @var int
     */
    public $height=400;
    
    /**
     * Full URL to the data source of the flash graph
     *
     * @var string
     */
    public $dataSource;

    /**
     * Constructor
     */
    public function init()
    {
    }
    
    /**
     * Method to show the Flash Graph
     *
     * Developers - DO NOT FORGET TO USE THE FLASH GRAPH DATA OBJECT FOR THE DATA
     *
     * @return string
     */
    public function show()
    {
        // Fix URL
        $this->dataSource = str_replace('&amp;', '&', $this->dataSource);
        
        // Return Flash Object
        return open_flash_chart_object_str($this->width, $this->height, $this->dataSource, FALSE, $this->getResourceUri('openflashchart/1.9.6/'));
    }
}
?>