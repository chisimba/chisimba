<?php
/**
 *
 * jpgraph helper class
 *
 * PHP version 5.1.0+
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   jpgraph
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * racemap helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package jpgraph
 *
 */
class graphops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        require_once($this->getResourcePath('jpgraph.php', 'jpgraph'));
        require_once($this->getResourcePath('jpgraph_line.php', 'jpgraph'));
    }
    
    public function linePlot($array, $width = 550, $height = 350, $title = 'Untitled', $subtitle = '(subtitle)', $xtitle = 'time in seconds', $ytitle = 'altitude') {
        // Create the graph and set a scale.
        // These two calls are always required
        $graph = new Graph($width, $height);
        $graph->SetScale('intlin');
        
        // Setup margin and titles
        $graph->SetMargin(40,20,20,40);
        $graph->title->Set($title);
        $graph->subtitle->Set($subtitle);
        $graph->xaxis->title->Set($xtitle);
        $graph->yaxis->title->Set($ytitle);

        
        // Create the linear plot
        $lineplot = new LinePlot($array);
        // Add the plot to the graph
        $graph->Add($lineplot);

        // Display the graph
        //return $graph->Stroke();
        return $graph;
    }
    
    public function drawGraph($objGraph, $filename='graph.png') {
        $path = $this->objConfig->getSiteRootPath().'/usrfiles/graphs/';
        if(!file_exists($path)) {
            mkdir($path, 0777);
        }
        $toFile = $path.$filename;
        $objGraph->Stroke($toFile);
        $url = $this->objConfig->getSiteRoot().'/usrfiles/graphs/'.$filename;
        return $url; 
    }
}
?>
