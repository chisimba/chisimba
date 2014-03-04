<?php
/**
 * Block class for remote popularity
 *
 * Block functions for remote popularity module
 *
 * PHP version 5
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
 * @version    $Id: block_remotepopularity_class_inc.php 11968 2008-12-29 21:42:08Z charlvn $
 * @package    remotepopularity
 * @subpackage blocks
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2008 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        remotepopularity
 */
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
 * A block to show the pie chart of remote downloads.
 *
 * @category  Chisimba
 * @author    Paul Scott
 * @version   0.1
 * @copyright 2006-2008 AVOIR
 *
 */
class block_remotepopularity extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * graph in a box
     *
     * @var    object
     * @access public
     */
    public $display;
    
    /**
     * Language elements
     *
     * @var    object
     * @access public
     */
    public $objLanguage;
    
    public $objDbPop;
    
    /**
     * Standard init function
     *
     * Instantiate language and user objects and create title
     *
     * @return NULL
     */
    public function init() 
    {
		$this->objDbPop = $this->getObject('dbpopularity');
    	$this->objLanguage = $this->getObject('language', 'language');
    	$this->objPopOps = $this->getObject('rempopops');
    	$graph = $this->objPopOps->getTopDownloads(5);
        $this->display = $graph;
        $this->title = $this->objLanguage->languageText("mod_rempop_tofive", "remotepopularity");
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
    	$popLink = new link ($this->uri(NULL, 'remotepopularity'));
        $popLink->link = $this->objLanguage->languageText('mod_rempop_linktext', 'remotepopularity');
        $lastfive = $this->objDbPop->getTop(5);
        $list = NULL;
        foreach($lastfive as $modules)
        {
        	$list .= "$modules<br />";
        }
        return $this->display.'<p>'.$popLink->show().'</p>';
        //return $this->display;
    }
}
?>
