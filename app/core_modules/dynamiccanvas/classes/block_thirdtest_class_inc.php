<?php
/**
 *
 * Block for providing a test
 *
 * Block for providing a test of the semi-dynamic canvas
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
 * Block for providing a test
 *
 * Block for providing a test of the semi-dynamic canvas
 *
 * @category  Chisimba
 * @author    Derek Keats
 * @version
 * @copyright 2010 AVOIR
 *
 */
class block_thirdtest extends object
{
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
    public function init() 
    {
        $this->title = "Test three";
        $this->expose = TRUE;
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return 'The second test is successful. This bloc should
        have a little more text. This is a wide block. Lorem ipsum dolor
        sit amet, euismod at diam, ac tellus mollitia praesent vitae,
        aliquam lacus. Mi est eu. Sed nulla non fringilla malesuada. Feugiat
        wisi amet, urna tempus rhoncus felis. Cursus dictumst. Velit tortor
        condimentum molestie mollis elementum et, pulvinar sed magna dapibus
        nisl, justo dolor vestibulum vel mauris. Ullamcorper nunc eleifend,
        sollicitudin quis mauris congue habitant enim nec. Mollis nec, nunc
        dui varius, gravida diam mollis, orci nulla facilisi proin elit ligula,
        sit non mauris. Morbi ut quisque commodo etiam at orci, at duis,
        lorem aptent augue pellentesque, diam ligula amet risus ducimus
        bibendum. Arcu eros turpis sed mattis libero consequat, urna laoreet
        morbi erat, fusce nam. Magna velit cras.
        ';
    }
}
?>