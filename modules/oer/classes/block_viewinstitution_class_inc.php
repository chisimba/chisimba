<?php
/**
 *
 * Block for viewing institution info
 *
 * A block for viewing institution info that can be rendered using a JSON
 * template.
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
 * @version    0.001
 * @package    oer
 * @author     Paul Mungai paulwando@gmail.com
 * @copyright  2011 AVOIR
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
 * Block for viewing an institution
 *
 * A block for viewing institution info that can be rendered
 * using a JSON template.
 *
 * @category  Chisimba
 * @author    Paul Mungai paulwando@gmail.com
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_viewinstitution extends object
{
    /**
     *
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;

    /**
     *
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        // We don't need a title in this block.
        $this->title = NULL;
    }
    /**
     *
     * Standard block show method to show institution info
     * rendered as a wideblock
     *
     * @return string $this->display block rendered
     *
     */
    public function show() 
    {
        $objInstManager = $this->getObject('institutionmanager','oer');
        $institutionId = $this->configData;
        
        return $objInstManager->buildViewInstitutionDetails($institutionId);
    }
}
?>