<?php

/**
 *
 * Hello demo 3
 *
 * A demo of the chisinmba dynamic canvas
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
 * @version
 * @package    dynamiccanvas
 * @author     Derek Keats <derek@dkeats.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
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
 * 
 * Hello demo 3
 *
 * A demo of the chisinmba dynamic canvas
 *
 * @category  Chisimba
 * @author    Derek Keats
 * @version
 * @copyright 2010 AVOIR
 *
 */
class block_reg3 extends object {

    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * Expose the block for remote blocks
     *
     * @var string $expose
     * @access public
     */
    public $expose;

    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() {

        // Set the title of the block.
        $this->title = "Registration Content";
        // Expose this block to external sites.
        $this->expose = TRUE;
    }

    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */

    public function show() {
        $objDbRegistration = $this->getObject("dbregistration");
        /*$schedules = $objDbRegistration->getSchedule();
        $table = $this->getObject('htmltable', 'htmlelements');
        $table->startHeaderRow();
        $table->addHeaderCell("Description");
        $table->addHeaderCell("Start Time");
        $table->addHeaderCell("End Time");
        $table->addHeaderCell("Venue");
        $table->addHeaderCell("Contact Person");
        $table->addHeaderCell("Limit");
        $table->endHeaderRow();

        foreach ($schedules as $schedule) {
            $table->startRow();
            $table->addCell("description");
            $table->addCell("starttime");
            $table->addCell("endtime");
            $table->addCell("venue");
            $table->addCell("contactperson");
            $table->addCell("maxlimit");
            $table->endRow();
        }
*/
        //echo $table->show();
        $content = "";
        $content .= 'SWEETY MABABEY BABEY BABEY.';
        return $content;

    }
    
    public function showTrainingSchedule() {

        return "This is the training schedule";
    }
}
?>