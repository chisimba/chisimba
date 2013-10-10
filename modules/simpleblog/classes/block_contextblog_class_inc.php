<?php
/**
 *
 * Simple blog site blog block
 *
 * Simple blog site blog block which can be used by other modules to render
 * a site blog, for example by the SLATE module.
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
 * @author     Administrative User admin@localhost.local
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
 * Simple blog site blog block
 *
 * Simple blog site blog block which can be used by other modules to render
 * a site blog, for example by the SLATE module.
 *
 * @category  Chisimba
 * @author    Administrative User admin@localhost.local
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_contextblog extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

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
        $this->title = strtoupper($this->objLanguage->code2Txt(
                "mod_simpleblog_context", "simpleblog"));
        $this->wrapStr = FALSE;
    }

    /**
     * 
     * Standard block show method.
     *
     * @return string $this->display block rendered
     * @access public
     * 
     */
    public function show() 
    {
        $objContext = $this->getObject('dbcontext','context');
        $isInContext=$objContext->isInContext();
        if($isInContext) {
            $contextCode=$objContext->getContextCode();
            $blogId = $contextCode;
        } else {
            return NULL;
        }
        $by = $this->getParam('by', FALSE);
        $objPostOps = $this->getObject('simpleblogops', 'simpleblog');
        if ($by) {
            if($by == 'thismonth') {
                return $objPostOps->showThisMonth($blogId);
            }
            if($by == 'lastmonth') {
                return $objPostOps->showLastMonth($blogId);
            }
            if($by == 'archive') {
                $year = $this->getParam('year', FALSE);
                $month = $this->getParam('month', FALSE);
                if ($year && $month) {
                    return $objPostOps->showArchive($blogId, $year, $month);
                } else {
                    return NULL;
                }
            }
            if($by == 'tag') {
                $tag = $this->getParam('tag', FALSE);
                if ($tag) {
                    return $objPostOps->showTag($blogId, $tag);
                } else {
                    return NULL;
                }
                
            }
            if($by == 'id') {
                $id = $this->getParam('id', FALSE);
                if ($id) {
                    return $objPostOps->showById($id);
                } else {
                    return NULL;
                }
                
            }
            if($by == 'search') {
                return $objPostOps->getPostsFromSearch($blogId);
            }
            if($by == 'user') {
                $userId = $this->getParam('userid', FALSE);
                if ($userId) {
                    if ($userId == $blogId) {
                        return $objPostOps->showCurrentPosts($blogId);
                    } else {
                        return $objPostOps->getPostsByUser($blogId, $userId);
                    }
                } else {
                    return NULL;
                }
            }
        } else {
            return $objPostOps->showCurrentPosts($blogId);
        }
    }
}
?>