<?php
/**
 *
 * An archives block for Simple blog.
 *
 * An archives block for Simple blog. This block provides a list of posts by 
 * year and month, linked to show the posts for that month and year.
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
 * @package    simpleblog
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
 * An archives block for Simple blog.
 *
 * An archives block for Simple blog. This block provides a list of posts by 
 * year and month, linked to show the posts for that month and year.
 *
 * @category  Chisimba
 * @package    simpleblog
 * @author     Derek Keats <derek@dkeats.com>
 * @version   0.001
 * @copyright 2010 AVOIR
 *
 */
class block_archives extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        // Get an instance of the languate object
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText(
            "mod_simpleblog_archive", "simpleblog",
            "Archive");
        $this->wrapStr = FALSE;
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        $objGuesser = $this->getObject('guesser', 'simpleblog');
        $blogId = $objGuesser->guessBlogId();
        $objPostOps = $this->getObject('simpleblogops', 'simpleblog');
        return $objPostOps->showArchiveList($blogId);
    }
}
?>
