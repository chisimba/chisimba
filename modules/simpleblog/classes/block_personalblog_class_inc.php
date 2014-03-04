<?php
/**
 *
 * Simple blog personal blog block
 *
 * Simple blog personal blog block which can be used by other modules to render
 * a personal blog, for example by the SLATE module.
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
 * Simple blog personal blog block
 *
 * Simple blog personal blog block which can be used by other modules to render
 * a personal blog, for example by the SLATE module.
 *
 * @category  Chisimba
 * @author    Administrative User admin@localhost.local
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_personalblog extends object
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
     *
     * @var string Object $objUser String for the user object
     * @access public
     *
     */
    public $objUser;

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
                "mod_simpleblog_allpersonal", "simpleblog",
                "Personal blog posts");
        $this->wrapStr = FALSE;
        // Get an instance of the user object
        $this->objUser = $this->getObject('user', 'security');
        // Get the blog posts db.
        $this->objDbPosts = $this->getObject('dbsimpleblog', 'simpleblog');
    }
    
    /**
     * 
     * List all personal blogs
     * 
     * @return A paginated list of personal blogs
     * @access public
     * 
     */
    public function listAll()
    {
        $bloggers = $this->objDbPosts->getAllPersonalBloggers();//turn off when you move to ops class
        $ret = NULL;
        $doc = new DOMDocument('UTF-8');
        $retDiv = $doc->createElement('div');
        $tbl = $doc->createElement('table');
        $retDiv->setAttribute('class', 'simpleblog_bloggers');
        
        if (count($bloggers) > 0) {
            foreach ($bloggers as $blogger) {
                $tr = $doc->createElement('tr');
                $userImg =  $this->objUser->getSmallUserImage($blogger['userid']);
                $frag = $doc->createDocumentFragment();
                $frag->appendXML($userImg);
                $td = $doc->createElement('td');
                $td->appendChild($frag);
                $tr->appendChild($td);
                $td = $doc->createElement('td');
                $url = $this->uri(array(
                  'blogid' => $blogger['userid'],
                  'type' => 'personal'
                ), 'simpleblog');
                $url=  str_replace("&amp;", "&", $url);
                $a = $doc->createElement('a');
                $a->setAttribute('href', $url);
                $a->appendChild($doc->createTextNode(
                  $blogger['firstname'] . " " . $blogger['surname']
                ));
                $td->appendChild($a);
                $tr->appendChild($td);
                // For the title
                $td = $doc->createElement('td');
                $td->appendChild($doc->createTextNode($blogger['post_title']));
                $tr->appendChild($td);
                $tbl->appendChild($tr);
            }
            $retDiv->appendChild($tbl);
        }
        $doc->appendChild($retDiv);
        return $doc->saveHTML();
    }
    
    /**
     * 
     * Show the blogs for a particular user identified by $blogId
     * 
     * @param type $blogId
     * @return string Paginated posts by the user
     * @access public
     * 
     */
    public function showBlog($blogId)
    {
        $objPostOps = $this->getObject('simpleblogops', 'simpleblog');
        return $objPostOps->showCurrentPosts($blogId);
    }
    
    public function show()
    {
        $blogId = $this->getParam('blogid', FALSE);
        if ($blogId) {
            return $this->showBlog($blogId);
        } else {
            return $this->listAll();
        }
    }

    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     * @access public
     * 
     */
    public function old________________show() 
    {
        $blogId = 'allpublic';
        $objPostOps = $this->getObject('simpleblogops', 'simpleblog');
        $by = $this->getParam('by', FALSE);
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
                    return $objPostOps->showCurrentPosts($userId);
                } else {
                    return NULL;
                }
            }
        } else {
            return $objPostOps->showCurrentPosts('allpublic');
        }
/*
        
        
        
        
        if ($blogId) {
            return $objPostOps->showCurrentPosts($blogId);
        } else {
            return $objPostOps->noBlogYet($loggedIn);
        }*/
    }
}
?>