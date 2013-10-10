<?php
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
 * Data access (db model) Class for the blog module
 *
 * This is a database model class for the blog module. All database transaactions will go through
 * this class. This class is derived from the top level dbTable superclass in the framework core.
 *
 * @author     Paul Scott
 * @filesource
 * @copyright  AVOIR
 * @package    blog
 * @category   chisimba
 * @access     public
 */
class dbblog extends dbTable
{
    public $objLanguage;
    public $sysConfig;
    public $lindex;
    public $objConfig;
    /**
     * Blog posts object
     *
     * @var    object
     * @access public
     */
    public $objblogPost;
    /**
     * Standard init function - Class Constructor
     *
     * @access public
     * @param  void
     * @return void
     */
    public function init()
    {
        $this->objLanguage = $this->getObject("language", "language");
        $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->lindex = TRUE;//$this->sysConfig->getValue('lucene_index', 'blog');
        // $this->objblogPost = $this->getObject('blogposts');
        $this->objConfig = $this->getObject('altconfig', 'config');

    }
    //methods to manipulate the categories table.

    /**
     * Method to get a list of the users categories as defined by the user
     *
     * @param  integer           $userid
     * @return arrayunknown_type
     * @access public
     */
    public function getAllCats($userid)
    {
        $this->_changeTable('tbl_blog_cats');
        return $this->getAll(" where userid = '$userid'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $catid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function deleteCat($catid)
    {
        $this->_changeTable('tbl_blog_cats');
        return $this->delete('id', $catid, 'tbl_blog_cats');
    }
    /**
     * Method to grab the top level parent categories per user id
     *
     * @param  integer $userid
     * @return array
     */
    public function getParentCats($userid)
    {
        $this->_changeTable('tbl_blog_cats');
        return $this->getAll("where userid = '$userid' AND cat_parent = '0'");
    }
    /**
     * Grab the child categories as a userl, according to the parent category
     *
     * @param  integer $userid
     * @param  string  $cat
     * @return unknown
     */
    public function getChildCats($userid, $cat)
    {
        $this->_changeTable('tbl_blog_cats');
        $child = $this->getAll("where userid = '$userid' AND cat_parent = '$cat'");
        return array(
            'child' => $child
        );
    }
    /**
     * Method to get a single cat for edit
     *
     * @param
     */
    public function getCatForEdit($userid, $id)
    {
        $this->_changeTable('tbl_blog_cats');
        $ret = $this->getAll("WHERE userid = '$userid' AND id = '$id'");
        return $ret[0];
    }
    /**
     * Method to create a merged array of the parent and child categories per user id
     *
     * @param  integer $userid
     * @return array
     */
    public function getCatsTree($userid)
    {
        $parents = $this->getParentCats($userid);
        $tree = new stdClass();
        if (empty($parents)) {
            $tree = NULL;
        } else {
            foreach($parents as $p) {
                $parent = $p;
                $child = $this->getChildCats($userid, $p['id']);
                if (is_null($p['cat_name'])) {
                    $p['cat_name'] = 0;
                }
                $tree->$p['cat_name'] = array_merge($parent, $child);
            }
        }
        return $tree;
    }
    /**
     * Method to set a category
     *
     * @param  integer $userid
     * @param  array   $cats
     * @return boolean
     */
    public function setCats($userid, $cats = array() , $mode = NULL)
    {
        if (!empty($cats)) {
            if ($mode == 'editcommit') {
                $this->_changeTable('tbl_blog_cats');
                return $this->update('id', $cats['id'], $cats, 'tbl_blog_cats');
            }
            $this->_changeTable('tbl_blog_cats');
            return $this->insert($cats, 'tbl_blog_cats');
        }
    }
    /**
     * Method to map the child id of a category to a nice name
     *
     * @param  mixed $childId
     * @return array
     */
    public function mapKid2Parent($childId)
    {
        $this->_changeTable('tbl_blog_cats');
        $ret = $this->getAll("WHERE id = '$childId'");
        return $ret;
    }
    /**
     * Method to count the number of posts in a category
     *
     * @param  string  $cat
     * @return integer
     */
    public function catCount($cat)
    {
        if ($cat == NULL) {
            $this->_changeTable('tbl_blog_posts');
            return $this->getRecordCount();
        }
        $this->_changeTable('tbl_blog_posts');
        return $this->getRecordCount("WHERE post_category = '$cat'");
    }
    //Methods to manipulate the link categories

    /**
     * Method to get a list of the users link categories as defined by the user
     *
     * @param  integer $userid
     * @return array
     * @access public
     */
    public function getAllLinkCats($userid)
    {
        $this->_changeTable('tbl_blog_linkcats');
        return $this->getAll(" where userid = '$userid'");
    }
    /**
     * Get the links per category
     *
     * @param  integer $userid
     * @param  string  $cat
     * @return mixed
     */
    public function getLinksCats($userid, $cat)
    {
        $this->_changeTable('tbl_blog_links');
        return $this->getAll("WHERE userid = '$userid' AND link_category = '$cat'");
    }
    /**
     * Add a category to the links section
     *
     * @param  integer $userid
     * @param  array   $linkCats
     * @return boolean
     */
    public function setLinkCats($userid, $linkCats = array())
    {
        if (!empty($linkCats)) {
            $this->_changeTable('tbl_blog_linkcats');
            return $this->insert($linkCats, 'tbl_blog_linkcats');
        } else {
            return FALSE;
        }
    }
    /**
     * Method to add a link to a category
     *
     * @param  integer $userid
     * @param  array   $linkarr
     * @return boolean
     */
    public function setLink($userid, $linkarr)
    {
        $this->_changeTable('tbl_blog_links');
        if (!empty($linkarr)) {
            return $this->insert($linkarr, 'tbl_blog_links');
        } else {
            return FALSE;
        }
    }
    /**
     * Method to grab a cat name by the id
     *
     * @param  string $catid
     * @return string
     */
    public function getCatById($catid)
    {
        if ($catid == '0') {
            return $this->objLanguage->languageText("mod_blog_defcat", "blog");
        } else {
            $this->_changeTable('tbl_blog_cats');
            $catname = $this->getAll("WHERE id = '$catid'");
            if (!empty($catname)) {
                return $catname[0]['cat_name'];
            } else {
                return NULL;
            }
        }
    }
    // posts section

    /**
     * Method to get all the posts in a category (published posts)
     *
     * @param  integer $userid
     * @param  mixed   $catid
     * @return array
     */
    public function getAbsAllPosts($userid)
    {
        $this->_changeTable('tbl_blog_posts');
        return $this->getAll("WHERE userid = '$userid' ORDER BY post_ts DESC");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getAbsAllPostsWithSiteBlogs($userid)
    {
        $this->_changeTable('tbl_blog_posts');
        return $this->getAll("WHERE userid = '$userid' OR userid = '1' ORDER BY post_ts DESC");
    }
    /**
     * Method to get all the posts in a category (published posts as well as drafts)
     *
     * @param  integer $userid
     * @param  mixed   $catid
     * @return array
     */
    public function getAbsAllPostsNoDrafts($userid)
    {
        $this->_changeTable('tbl_blog_posts');
        return $this->getAll("WHERE userid = '$userid' AND post_status = '0' ORDER BY post_ts DESC");
    }
    /**
     * Method to get all the posts in a category (published posts ONLY)
     *
     * @param  integer $userid
     * @param  mixed   $catid
     * @return array
     */
    public function getAllPosts($userid, $catid)
    {
        if ($catid == '') {
            $this->_changeTable('tbl_blog_posts');
            return $this->getAll("WHERE userid = '$userid' AND post_status = '0' ORDER BY post_ts DESC");
        }
        $this->_changeTable('tbl_blog_posts');
        return $this->getAll("WHERE userid = '$userid' AND post_category = '$catid' AND post_status = '0' ORDER BY post_ts DESC");
    }
    /**
     * Method to get all the posts made within a month
     *
     * @param  mixed  $startdate
     * @param  string $userid
     * @return array
     */
    public function getPostsMonthly($startdate, $userid)
    {
        $this->_changeTable('tbl_blog_posts');
        $this->objblogOps = $this->getObject('blogops');
        $times = $this->objblogOps->retDates($startdate);
        //print_r($times);
        $now = date('r', mktime(0, 0, 0, date("m", time()) , date("d", time()) , date("y", time())));
        $monthstart = $times['mbegin'];
        $prevmonth = $times['prevmonth'];
        $nextmonth = $times['nextmonth'];
        //get the entries from the db
        $filter = "WHERE post_ts > '$prevmonth' AND post_ts < '$nextmonth' AND post_status = '0' AND userid = '$userid' ORDER BY post_ts DESC";
        $ret = $this->getAll($filter);
        return $ret;
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $startdate Parameter description (if any) ...
     * @param  unknown $userid    Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function getMonthPostCount($startdate, $userid)
    {
        $this->_changeTable('tbl_blog_posts');
        $this->objblogOps = &$this->getObject('blogops');
        $times = $this->objblogOps->retDates($startdate);
        $now = date('r', mktime(0, 0, 0, date("m", time()) , date("d", time()) , date("y", time())));
        $monthstart = $times['mbegin'];
        $prevmonth = $times['prevmonth'];
        $nextmonth = $times['nextmonth'];
        //get the entries from the db
        $filter = "WHERE post_ts > '$prevmonth' AND post_ts < '$nextmonth' AND userid = '$userid'";
        $ret = $this->getRecordCount($filter);
        return $ret;
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $postid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getPostByPostID($postid)
    {
        $this->_changeTable('tbl_blog_posts');
        $filter = "WHERE id = '$postid'";
        return $this->getAll($filter);
    }
    /**
     * Method to delete a post
     *
     * @param  mixed   $id
     * @return boolean
     */
    public function deletePost($id)
    {
        $this->_changeTable('tbl_blog_posts');
        //delete the post
        $this->delete('id', $id, 'tbl_blog_posts');
        //change tables to the postmeta table to delete the tags
        $this->_changeTable('tbl_tags');
        //get all the entries where the post_id matches the deleted post id
        $tagstodelete = $this->getAll("WHERE item_id = '$id'");
        if (!empty($tagstodelete)) {
            foreach($tagstodelete as $deltags) {
                //print_r($deltags);
                $this->delete('id', $deltags['id'], 'tbl_tags');
            }
        }
        //change table and sort out the comments now
        $this->_changeTable('tbl_blogcomments');
        $commstodelete = $this->getAll("WHERE comment_parentid = '$id'");
        if (!empty($commstodelete)) {
            foreach($commstodelete as $ctd) {
                //print_r($ctd);
                $this->delete('id', $ctd['id'], 'tbl_blogcomments');
            }
        }
        //clean up the trackbacks now
        $this->_changeTable("tbl_blog_trackbacks");
        $tbtodel = $this->getAll("WHERE postid = '$id'");
        if (!empty($tbtodel)) {
            foreach($tbtodel as $tbs) {
                //print_r($tbs);
                $this->delete('id', $tbs['id'], 'tbl_blog_trackbacks');
            }
        }

        // Remove Lucene Entry
        $objIndexData = $this->getObject('indexdata', 'search');
        $objIndexData->removeIndex('blog_post_'.$id);
    }
    /**
     * Method to get a post by its ID
     *
     * @param  mixed $id
     * @return array
     */
    public function getPostById($id)
    {
        $this->_changeTable('tbl_blog_posts');
        return $this->getAll("WHERE id = '$id'");
    }
    /**
     * Method to get all the posts within a category
     *
     * @param  integer $userid
     * @param  mixed   $catid
     * @return array
     */
    public function getPostsFromCat($userid, $catid)
    {
        $this->_changeTable('tbl_blog_posts');
        return $this->getAll("WHERE userid = '$userid' AND post_category = '$catid'");
    }
    /**
     * Method to get the latest post of a user
     *
     * @param  integer $userid
     * @return array
     */
    public function getLatestPost($userid)
    {
        $this->_changeTable('tbl_blog_posts');
        $filter = "WHERE userid = '$userid' AND post_status = '0' ORDER BY post_ts DESC";
        $lastpost = $this->getAll($filter);
        if (isset($lastpost[0])) {
            $lastpost = $lastpost[0];
        } else {
            $lastpost = NULL;
        }
        $filter2 = "WHERE userid = '$userid' AND post_status = '1' ORDER BY post_ts DESC";
        $drafts = $this->getAll($filter2);
        if(!empty($drafts))
        {
        	$lastpost['drafts'] = $drafts;
        	return $lastpost;
        }
        else {
        	return $lastpost;
        }
    }
    /**
     * Method to get the sticky posts of a user
     *
     * @param  integer $userid
     * @return array
     */
    public function getStickyPosts($userid)
    {
        $this->_changeTable('tbl_blog_posts');
        $filter = "WHERE userid = '$userid' AND stickypost= '1' ORDER BY post_ts DESC";
        $stickyposts = $this->getAll($filter);
        return $stickyposts;
    }
    /**
     * Method to get the latest posts
     *
     * @author Megan Watson
     * @param  integer $userid
     * @return array
     */
    public function getLastPosts($num = 10, $userid = FALSE)
    {
        if ($userid == FALSE) {
            $this->_changeTable('tbl_blog_posts');
            $filter = "WHERE post_status = '0' ORDER BY post_ts DESC LIMIT {$num}";
            $posts = $this->getAll($filter);
        } else {
            $this->_changeTable('tbl_blog_posts');
            $filter = "WHERE userid = '$userid' AND post_status = '0' ORDER BY post_ts DESC LIMIT {$num}";
            $posts = $this->getAll($filter);
        }
        return $posts;
    }

    /**
     * Method to get the geotagged posts
     *
     * @param  integer $userid
     * @return array
     */
    public function getGeoPosts($userid = FALSE)
    {
        if ($userid == FALSE) {
            $this->_changeTable('tbl_blog_posts');
            $filter = "WHERE post_status = '0' AND geolat != '' AND geolon != '' ORDER BY post_ts DESC";
            $posts = $this->getAll($filter);
        } else {
            $this->_changeTable('tbl_blog_posts');
            $filter = "WHERE userid = '$userid' AND post_status = '0' AND geolat != '' AND geolon != '' ORDER BY post_ts DESC";
            $posts = $this->getAll($filter);
        }
        return $posts;
    }

    public function countGeoPosts($userid)
    {
        $this->_changeTable('tbl_blog_posts');
        $filter = "WHERE userid = '$userid' AND geolat != '' AND geolon != ''";
        return $this->getRecordCount($filter);
    }

    /**
     * Method to return a random blog
     *
     * @param  void
     * @return mixed
     */
    public function getRandBlog()
    {
        $this->_changeTable('tbl_blog_posts');
        $res = $this->getAll();
        if (!empty($res)) {
            foreach($res as $blogs) {
                $blo[] = $blogs['userid'];
            }
            $rand_keys = array_rand($blo, 1);
            return $res[$rand_keys];
        } else {
            return NULL;
        }
    }
    //post methods

    /**
     * Method to insert a post to your posts table
     *
     * @param  integer $userid
     * @param  array   $postarr
     * @param  string  $mode
     * @return array
     */
    public function insertPostAPI($userid, $insarr)
    {
        $this->_changeTable("tbl_blog_posts");
        $insarr['post_content'] = str_ireplace("<br />", " <br /> ", $insarr['post_content']);
        $insarr['id'] = $this->insert($insarr, 'tbl_blog_posts');
        $this->luceneIndex($insarr);
        return $insarr['id'];
    }
    public function updatePostAPI($blogid, $postarr)
    {
        $this->_changeTable("tbl_blog_posts");
        $this->update('id', $blogid, $postarr, 'tbl_blog_posts');
        return TRUE;
    }
    /**
     * Method to insert a post to your posts table
     *
     * @param  integer $userid
     * @param  array   $postarr
     * @param  string  $mode
     * @return array
     */
    public function insertPost($userid, $postarr, $mode = NULL)
    {
        $this->_changeTable("tbl_blog_posts");
        $this->objblogOps = $this->getObject('blogops');
        if ($mode == NULL) {
            //log_debug($postarr['postcontent']);
            //$this->pcleaner = $this->newObject('htmlcleaner', 'utilities');
            //$this->ecleaner = $this->newObject('htmlcleaner', 'utilities');
            //$postarr['postcontent'] = preg_replace("/(\r\n|\n|\r)/", "", $postarr['postcontent']);
            $pc = preg_replace('=<br */?>=i', "\n", $postarr['postcontent']);
            $postarr['postcontent'] = str_ireplace("<br />", " <br /> ", $postarr['postcontent']);
            $insarr = array(
                'userid' => $userid,
                'post_date' => strftime('%Y-%m-%d %H:%M:%S', mktime()) ,
                'post_content' => addslashes($postarr['postcontent']) , //$pc), //$this->pcleaner->cleanHtml($this->objblogOps->html2txt($postarr['postcontent'])),
                'post_title' => htmlentities($postarr['posttitle']) ,
                'post_category' => $postarr['postcat'],
                'post_excerpt' => addslashes(htmlentities($postarr['postexcerpt'])) , //$this->ecleaner->cleanHtml(addslashes($postarr['postexcerpt'])),
                'post_status' => $postarr['poststatus'],
                'comment_status' => $postarr['commentstatus'],
                'post_modified' => $postarr['postmodified'],
                'comment_count' => $postarr['commentcount'],
                'post_ts' => time() ,
                'post_lic' => $postarr['cclic'],
                'stickypost' => isset($postarr['stickypost']) ? $postarr['stickypost'] : 'N',
                'showpdf' => isset($postarr['showpdf']) ? $postarr['showpdf'] : 'N',
                'geolat' => $postarr['geolat'],
                'geolon' => $postarr['geolon'],
            );
            $insarr['id'] = $this->insert($insarr, 'tbl_blog_posts');
            if ($this->lindex == 'TRUE') {
                $this->luceneIndex($insarr);
            }
            // add to the blog sitemap
            $this->objConfig = $this->getObject('altconfig', 'config');
            $maparray = array('url' => $this->uri(array('action' => 'viewsingle',
            	                                           'postid' => $insarr['id'],
            	                                           'userid' => $userid)
            	                                        ),
            	              'lastmod' => date('Y-m-d', $insarr['post_ts']),
            	              'changefreq' => 'weekly',
            	              'priority' => 0.5
            	             );
            $smarr = array($maparray);
            $bs = $this->sysConfig->getValue('blog_sitemap', 'blog');
            if($bs == 'TRUE')
            {
            	$sitemap = $this->getObject('sitemap', 'utilities');
            	if(!file_exists($this->objConfig->getsiteRootPath().'blogsitemap.xml'))
            	{

            		$smxml = $sitemap->createSiteMap($smarr);
            		$sitemap->writeSitemap($smxml, 'blogsitemap');
            	}
            	else {
            		$smxml = $sitemap->updateSiteMap($maparray, 'blogsitemap');
            	}
            	return TRUE;
        	}
        }
        if ($mode == 'editpost') {
            //$this->pcleaner = $this->newObject('htmlcleaner', 'utilities');
            //$this->ecleaner = $this->newObject('htmlcleaner', 'utilities');
            //$postarr['postcontent'] = preg_replace("/(\r\n|\n|\r)/", " ", $postarr['postcontent']);
            $postarr['postcontent'] = str_ireplace("<br />", " <br /> ", $postarr['postcontent']);
            $pc = $postarr['postcontent'];

            $edarr = array(
                'userid' => $userid,
                'post_date' => strftime('%Y-%m-%d %H:%M:%S', mktime()) ,
                'post_content' => addslashes($pc) ,
                'post_title' => htmlentities($postarr['posttitle']) ,
                'post_category' => $postarr['postcat'],
                'post_excerpt' => addslashes(htmlentities($postarr['postexcerpt'])) , //$this->ecleaner->cleanHtml($postarr['postexcerpt']),
                'post_status' => $postarr['poststatus'],
                'comment_status' => $postarr['commentstatus'],
                'post_modified' => $postarr['postmodified'],
                'comment_count' => $postarr['commentcount'],
                'post_ts' => $postarr['postts'], //time(),
                'post_lic' => $postarr['cclic'],
                'stickypost' => $postarr['stickypost'],
                'showpdf' => $postarr['showpdf'],
                'geolat' => $postarr['geolat'],
                'geolon' => $postarr['geolon'],
            );
            $this->update('id', $postarr['id'], $edarr, 'tbl_blog_posts');

            // add to the blog sitemap
            $maparray = array('url' => $this->uri(array('action' => 'viewsingle',
            	                                           'postid' => $postarr['id'],
            	                                           'userid' => $userid)
            	                                        ),
            	              'lastmod' => date('Y-m-d', $edarr['post_ts']),
            	              'changefreq' => 'weekly',
            	              'priority' => 0.5
            	             );
            $smarr = array($maparray);
            $bs = $this->sysConfig->getValue('blog_sitemap', 'blog');
            if($bs == 'TRUE')
            {
            	$sitemap = $this->getObject('sitemap', 'utilities');
            	if(!file_exists($this->objConfig->getsiteRootPath().'blogsitemap.xml'))
            	{

            		$smxml = $sitemap->createSiteMap($smarr);
            		$sitemap->writeSitemap($smxml, 'blogsitemap');
            	}
            	else {
            		$smxml = $sitemap->updateSiteMap($maparray, 'blogsitemap');
            	}
            }
            if ($this->lindex == 'TRUE') {
               	$edarr['id'] = $postarr['id'];
                $this->luceneReIndex($edarr);
            }
            return TRUE;
        }
        if ($mode == 'import') {
            //$this->ipcleaner = $this->newObject('htmlcleaner', 'utilities');
            //$this->iecleaner = $this->newObject('htmlcleaner', 'utilities');
            $postarr['cclic'] = NULL;
            $postarr['postcontent'] = str_ireplace("<br />", " <br /> ", $postarr['postcontent']);
            $imparr = array(
                'userid' => $userid,
                'post_date' => $postarr['postdate'],
                'post_content' => addslashes($postarr['postcontent']) , //$this->ipcleaner->cleanHtml($postarr['postcontent']),
                'post_title' => $postarr['posttitle'],
                'post_category' => $postarr['postcat'],
                'post_excerpt' => addslashes($postarr['postexcerpt']) , //$this->iecleaner->cleanHtml($postarr['postexcerpt']),
                'post_status' => $postarr['poststatus'],
                'comment_status' => $postarr['commentstatus'],
                'post_modified' => $postarr['postmodified'],
                'comment_count' => $postarr['commentcount'],
                'post_ts' => strtotime($postarr['postdate']) ,
                'post_lic' => $postarr['cclic']
            );
            $imparr['id'] = $this->insert($imparr, 'tbl_blog_posts');

            // add to the blog sitemap
            $maparray = array('url' => $this->uri(array('action' => 'viewsingle',
            	                                           'postid' => $postarr['id'],
            	                                           'userid' => $userid)
            	                                        ),
            	              'lastmod' => date('Y-m-d', $imparr['post_ts']),
            	              'changefreq' => 'weekly',
            	              'priority' => 0.5
            	             );
            $smarr = array($maparray);
            $bs = $this->sysConfig->getValue('blog_sitemap', 'blog');
            if($bs == 'TRUE')
            {
            	$sitemap = $this->getObject('sitemap', 'utilities');
            	if(!file_exists($this->objConfig->getsiteRootPath().'blogsitemap.xml'))
            	{
            		$smxml = $sitemap->createSiteMap($smarr);
            		$sitemap->writeSitemap($smxml, 'blogsitemap');
            	}
            	else {
            		$smxml = $sitemap->updateSiteMap($maparray, 'blogsitemap');
            	}
            }
            if ($this->lindex == 'TRUE') {
                $this->luceneIndex($imparr);
            }
            return TRUE;
        }
        if ($mode == 'mail') {
            $this->ipcleaner = $this->newObject('htmlcleaner', 'utilities');
            $this->iecleaner = $this->newObject('htmlcleaner', 'utilities');
            $postarr['postcontent'] = $this->ipcleaner->cleanHtml(nl2br($postarr['postcontent']));
            $postarr['postcontent'] = str_ireplace("<br />", " <br /> ", $postarr['postcontent']);
            $postarr['cclic'] = NULL;
            $mparr = array(
                'userid' => $userid,
                'post_date' => $postarr['postdate'],
                'post_content' => $postarr['postcontent'],
                'post_title' => $postarr['posttitle'],
                'post_category' => $postarr['postcat'],
                'post_excerpt' => $this->iecleaner->cleanHtml($postarr['postexcerpt']) ,
                'post_status' => $postarr['poststatus'],
                'comment_status' => $postarr['commentstatus'],
                'post_modified' => $postarr['postmodified'],
                'comment_count' => $postarr['commentcount'],
                'post_ts' => strtotime($postarr['postdate']) ,
                'post_lic' => $postarr['cclic']
            );
            $mparr['id'] = $this->insert($mparr, 'tbl_blog_posts');

            // add to the blog sitemap
            $maparray = array('url' => $this->uri(array('action' => 'viewsingle',
            	                                           'postid' => $postarr['id'],
            	                                           'userid' => $userid)
            	                                        ),
            	              'lastmod' => date('Y-m-d', $mparr['post_ts']),
            	              'changefreq' => 'weekly',
            	              'priority' => 0.5
            	             );
            $smarr = array($maparray);
            $bs = $this->sysConfig->getValue('blog_sitemap', 'blog');
            if($bs == 'TRUE')
            {
            	$sitemap = $this->getObject('sitemap', 'utilities');
            	if(!file_exists($this->objConfig->getsiteRootPath().'blogsitemap.xml'))
            	{
            		$smxml = $sitemap->createSiteMap($smarr);
            		$sitemap->writeSitemap($smxml, 'blogsitemap');
            	}
            	else {
            		$smxml = $sitemap->updateSiteMap($maparray, 'blogsitemap');
            	}
            }
            if ($this->lindex == 'TRUE') {
                $this->luceneIndex($mparr);
            }
            return TRUE;
        } else {
            //$this->epcleaner = $this->newObject('htmlcleaner', 'utilities');
            //$this->eecleaner = $this->newObject('htmlcleaner', 'utilities');
            $postarr['postcontent'] = str_ireplace("<br />", " <br /> ", $postarr['postcontent']);
            $inseditarr = array(
                'userid' => $userid,
                'post_date' => $postarr['postdate'],
                'post_content' => addslashes($postarr['postcontent']) , //$this->epcleaner->cleanHtml($postarr['postcontent']),
                'post_title' => htmlentities($postarr['posttitle']) ,
                'post_category' => $postarr['postcat'],
                'post_excerpt' => addslashes(htmlentities($postarr['postexcerpt'])) , //$this->eecleaner->cleanHtml($postarr['postexcerpt']),
                'post_status' => $postarr['poststatus'],
                'comment_status' => $postarr['commentstatus'],
                'post_modified' => $postarr['postmodified'],
                'comment_count' => $postarr['commentcount'],
                'post_ts' => strtotime($postarr['postdate']) ,
                'post_lic' => $postarr['cclic'],
                'stickypost' => $postarr['stickypost'],
                'showpdf' => $postarr['showpdf'],
                'geolat' => $postarr['geolat'],
                'geolon' => $postarr['geolon'],
            );
            $this->update('id', $postarr['id'], $inseditarr, 'tbl_blog_posts');
            // add to the blog sitemap
            $maparray = array('url' => $this->uri(array('action' => 'viewsingle',
            	                                           'postid' => $postarr['id'],
            	                                           'userid' => $userid)
            	                                        ),
            	              'lastmod' => date('Y-m-d', $inseditarr['post_ts']),
            	              'changefreq' => 'weekly',
            	              'priority' => 0.5
            	             );
            $smarr = array($maparray);
            $bs = $this->sysConfig->getValue('blog_sitemap', 'blog');
            if($bs == 'TRUE')
            {
            	$sitemap = $this->getObject('sitemap', 'utilities');
            	if(!file_exists($this->objConfig->getsiteRootPath().'blogsitemap.xml'))
            	{
            		$smxml = $sitemap->createSiteMap($smarr);
            		$sitemap->writeSitemap($smxml, 'blogsitemap');
            	}
            	else {
            		$smxml = $sitemap->updateSiteMap($maparray, 'blogsitemap');
            	}
            }
            if ($this->lindex == 'TRUE') {
                $this->luceneReIndex($inseditarr);
            }

            return TRUE;
        }
    }
    /**
     * Method to get the User blogs DISTINCT query
     *
     * @param  mixed $column
     * @param  mixed $table
     * @return array
     */
    public function getUBlogs($column, $table)
    {
        $this->_changeTable('tbl_blog_posts');
        return $this->getArray("SELECT DISTINCT $column from $table");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function checkValidUser()
    {
        $this->_changeTable('tbl_users');
        $val = $this->getAll();
        return $val;
    }
    /**
     * Method to insert trackback from remote to table
     *
     * @param  array $data
     * @return bool
     */
    public function setTrackback($data)
    {
        $this->_changeTable('tbl_blog_trackbacks');
        $userid = $data['userid'];
        $postid = $data['id'];
        $remhost = $data['host'];
        $title = $data['title'];
        $excerpt = $data['excerpt'];
        $tburl = $data['url'];
        $blog_name = $data['blog_name'];
        $insarr = array(
            'userid' => $userid,
            'postid' => $postid,
            'remhost' => $remhost,
            'title' => $title,
            'excerpt' => $excerpt,
            'tburl' => $tburl,
            'blog_name' => $blog_name
        );
        return $this->insert($insarr, 'tbl_blog_trackbacks');
    }
    /**
     * Method to get the count of trackbacks associated with a particular post
     *
     * @param  post    id string $pid
     * @return integer
     */
    public function getTrackbacksPerPost($pid)
    {
        $this->_changeTable('tbl_blog_trackbacks');
        $filter = "WHERE postid = '$pid'";
        return $this->getRecordCount($filter);
    }
    /**
     * Method to get the actual trackback text per post
     *
     * @param  postid string $pid
     * @return array
     */
    public function grabTrackbacks($pid)
    {
        $this->_changeTable('tbl_blog_trackbacks');
        $filter = "WHERE postid = '$pid'";
        return $this->getAll($filter);
    }
    /**
     * Method to delete a trackback by its ID (in case of TB SPAM)
     *
     * @param string $id
     * @return bool
     */
    public function deleteTrackBack($id)
    {
        $this->_changeTable('tbl_blog_trackbacks');
        return $this->delete('id', $id, 'tbl_blog_trackbacks');
    }
    /**
     * Method to get all of the tags associated with a particular post
     *
     * @param  string $postid
     * @return array
     */
    public function getPostTags($postid)
    {
        $this->_changeTable("tbl_tags");
        return $this->getAll("WHERE item_id = '$postid' AND module = 'blog'");
    }
    /**
     * Insert a set of tags into the database associated with the post
     *
     * @param array  $tagarray
     * @param string $userid
     * @param String $postid
     */
    public function insertTags($tagarray, $userid, $postid)
    {
        $this->_changeTable("tbl_tags");
        foreach($tagarray as $tins) {
            $tins = trim($tins);
            $tins = addslashes($tins);
            if (!empty($tins)) {
                $this->insert(array(
                    'userid' => $userid,
                    'item_id' => $postid,
                    'meta_key' => 'tag',
                    'meta_value' => $tins,
                    'module' => 'blog'
                ));
            }
        }
    }
    /**
     * Method to remove all the tags associated with a post
     *
     * @param  string $postid
     * @return void
     */
    public function removeAllTags($postid)
    {
        // I have changed all aspects of tbl_post_mtadata to tbl_tags to cater for the new API
        $this->_changeTable("tbl_tags");
        return $this->delete('id', $postid, 'tbl_tags');
    }
    /**
     * Method to retrieve the tags associated with a userid
     *
     * @param  string $userid
     * @return array
     */
    public function getTagsByUser($userid)
    {
        $this->_changeTable("tbl_tags");
        return $this->getAll("WHERE userid = '$userid' and meta_key = 'tag' and module = 'blog'");
    }
    /**
     * Method to get a tag weight by counting the tags
     *
     * @param  string  $tag
     * @param  string  $userid
     * @return integer
     */
    public function getTagWeight($tag, $userid)
    {
        $tag = addslashes($tag);
        $this->_changeTable("tbl_tags");
        $count = $this->getRecordCount("WHERE meta_value = '$tag' AND userid = '$userid' and module = 'blog'");
        return $count;
    }
    
    /**
     * Method to return an array of posts associated with a tag
     *
     * @param  string $userid
     * @param  string $tag
     * @return array
     */
    public function getAllPostsByTag($userid, $tag)
    {
        //first do a lookup and see what the post(s) id is/are
        $tag = addslashes($tag);
        $this->_changeTable("tbl_tags");
        $poststoget = $this->getAll("WHERE meta_value = '$tag' AND userid = '$userid'");
        //print_r($poststoget);
        foreach($poststoget as $gettables) {
            $ptg[] = $gettables['item_id'];
        }
        //print_r($ptg); die();
        //now get the posts and return them
        $this->_changeTable("tbl_blog_posts");
        foreach($ptg as $pos) {
            $posts[] = $this->getAll("WHERE id = '$pos'");
        }
        //print_r($posts); die();
        return $posts;
    }
    
    /**
     * Method to return an array of posts associated with a tag
     *
     * @param  string $userid
     * @param  string $tag
     * @return array
     */
    public function getNumPostsByTag($num, $tag)
    {
        //first do a lookup and see what the post(s) id is/are
        $tag = addslashes($tag);
        $this->_changeTable("tbl_tags");
        $poststoget = $this->getAll("WHERE meta_value = '$tag' AND module = 'blog' ORDER BY puid DESC LIMIT {$num}");
        //print_r($poststoget);
        foreach($poststoget as $gettables) {
            $ptg[] = $gettables['item_id'];
        }
        if(empty($ptg)) 
        {
            $ptg = array();
        }
        //print_r($ptg); die();
        //now get the posts and return them
        $this->_changeTable("tbl_blog_posts");
        foreach($ptg as $pos) {
            $posts[] = $this->getAll("WHERE id = '$pos'");
        }
        if(empty($posts))
        {
            $posts = NULL;
        }
        return $posts;
    }
    
    /**
     * Method to add a RSS feed to the database
     *
     * @param  string $userid
     * @param  string $name
     * @param  string $desc
     * @param  string $url
     * @return bool
     */
    public function addRss($rssarr, $mode = NULL)
    {
        $this->_changeTable("tbl_blog_userrss");
        if ($mode == NULL) {
            return $this->insert($rssarr);
        } elseif ($mode == 'edit') {
            return $this->update('id', $rssarr['id'], $rssarr, "tbl_blog_userrss");
        } else {
            return FALSE;
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getUserRss($userid)
    {
        $this->_changeTable("tbl_blog_userrss");
        return $this->getAll("WHERE userid = '$userid'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getRssById($id)
    {
        $this->_changeTable("tbl_blog_userrss");
        return $this->getAll("WHERE id = '$id'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function delRss($id)
    {
        $this->_changeTable("tbl_blog_userrss");
        return $this->delete('id', $id, "tbl_blog_userrss");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $rssarr Parameter description (if any) ...
     * @param  unknown $id     Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function updateRss($rssarr, $id)
    {
        $this->_changeTable("tbl_blog_userrss");
        return $this->update('id', $id, $rssarr);
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $profile Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function saveProfile($profile)
    {
        $this->_changeTable("tbl_blog_profile");
        return $this->insert($profile);
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function checkProfile($userid)
    {
        $this->_changeTable("tbl_blog_profile");
        $ret = $this->getAll("WHERE userid = '$userid'");
        if (empty($ret)) {
            //this user has no profile yet
            return FALSE;
        } else {
            return $ret[0];
        }
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  array  $profile Parameter description (if any) ...
     * @return mixed  Return description (if any) ...
     * @access public
     */
    public function updateProfile($profile)
    {
        $this->_changeTable("tbl_blog_profile");
        return $this->update('id', $profile['id'], $profile);
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  array  $data Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function luceneIndex($data)
    {
        $objIndexData = $this->getObject('indexdata', 'search');
        if(!isset($data['id']))
        {
        	$data['id'] = $this->getlastInsertId();
        }

        $docId = 'blog_post_'.$data['id'];
        if(!isset($data['post_date']))
        {
        	$data['post_date'] = date('Y-m-d');
        }
        $docDate = $data['post_date'];
       if(!isset($data['userid']))
       {
       	$this->objUser = $this->getObject('user', 'security');
       	$data['userid'] = $this->objUser->userId();
       }
        $url = $this->uri(array('action'=>'viewsingle', 'postid'=>$data['id'], 'userid'=>$data['userid']));
        $title = $data['post_title'];
        $contents = $data['post_title'].' '.$data['post_content'];

        if (trim($data['post_excerpt']) == '') {
            $objTrimStr = $this->getObject('trimstr', 'strings');
            $teaser = $objTrimStr->strTrim(strip_tags($data['post_content']));
        } else {
            $teaser = $data['post_excerpt'];
        }

        $module = 'blog';
        $userId = $data['userid'];
        $license = $data['post_lic'];
        $context = NULL;
        $workgroup = NULL;
        $tags = NULL;

        if ($data['post_status'] == 1) {
            $permissions = 'useronly';
        } else {
            $permissions = NULL;
        }

        $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, $tags, $license, $context, $workgroup, $permissions);

    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  array  $data Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function luceneReIndex($data)
    {
        $this->luceneIndex($data);

    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getUserLinks($userid)
    {
        $this->_changeTable('tbl_blog_links');
        return $this->getAll("WHERE userid = '$userid'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getUserLinksonly($userid)
    {
        $this->_changeTable('tbl_blog_links');
        return $this->getAll("WHERE userid = '$userid' AND link_type='bloglink'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getUserbroll($userid)
    {
        $this->_changeTable('tbl_blog_links');
        return $this->getAll("WHERE userid = '$userid' AND link_type='blogroll'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $id     Parameter description (if any) ...
     * @param  unknown $userid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getUserLink($id, $userid)
    {
        $this->_changeTable('tbl_blog_links');
        return $this->getAll("WHERE id = '$id' AND userid = '$userid'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $insarr Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function insertUserLink($insarr)
    {
        $this->_changeTable('tbl_blog_links');
        return $this->insert($insarr, 'tbl_blog_links');
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $id     Parameter description (if any) ...
     * @param  unknown $insarr Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function updateUserLink($id, $insarr)
    {
        $this->_changeTable('tbl_blog_links');
        return $this->update('id', $id, $insarr, 'tbl_blog_links');
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function deleteBlink($id)
    {
        $this->_changeTable('tbl_blog_links');
        return $this->delete('id', $id, 'tbl_blog_links');
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $pagearr Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function savepage($pagearr)
    {
        $this->_changeTable('tbl_blog_pages');
        return $this->insert($pagearr, 'tbl_blog_pages');
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $id      Parameter description (if any) ...
     * @param  unknown $pagearr Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function updatePage($id, $pagearr)
    {
        $this->_changeTable('tbl_blog_pages');
        return $this->update('id', $id, $pagearr, 'tbl_blog_pages');
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function deletePage($id)
    {
        $this->_changeTable('tbl_blog_pages');
        return $this->delete('id', $id, 'tbl_blog_pages');
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $userid Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getPages($userid)
    {
        $this->_changeTable('tbl_blog_pages');
        return $this->getAll("WHERE userid = '$userid'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getPageById($id)
    {
        $this->_changeTable('tbl_blog_pages');
        return $this->getAll("WHERE id = '$id'");
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $term Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function quickSearch($term)
    {
        $this->_changeTable('tbl_blog_posts');
        $ret = $this->getAll("WHERE post_content LIKE '%%$term%%' OR post_title LIKE '%%$term%%' OR post_excerpt LIKE '%%$term%%'");
        return $ret;
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function getLists()
    {
        $this->_changeTable('tbl_blog_lists');
        return $this->getAll();
    }
    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  unknown $list_identifier Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function getListInfo($list_identifier)
    {
        $this->_changeTable('tbl_blog_lists');
        return $this->getAll("WHERE list_identifier = '$list_identifier'");
    }
    /**
     * Method to dynamically switch tables
     *
     * @param  string  $table
     * @return boolean
     * @access private
     */
    private function _changeTable($table)
    {
        try {
            parent::init($table);
            return TRUE;
        }
        catch(customException $e) {
            customException::cleanUp();
            return FALSE;
        }
    }

    /**
     * Method to migrate from the old search to the new search
     * @access public
     */
    public function updateSearch()
    {
        $this->_changeTable('tbl_blog_posts');

        $posts = $this->getAll();

        foreach ($posts as $post)
        {
            $this->luceneIndex($post);
        }
    }
}
?>
