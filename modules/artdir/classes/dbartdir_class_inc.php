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
 * Data access (db model) Class for the artdir module
 *
 * This is a database model class for the blog module. All database transactions will go through
 * this class. This class is derived from the top level dbTable superclass in the framework core.
 *
 * @author     Paul Scott
 * @filesource
 * @copyright  AVOIR
 * @package    artdir
 * @category   chisimba
 * @access     public
 */
class dbartdir extends dbTable
{
    public $objLanguage;
    public $sysConfig;
    public $lindex;
    public $objConfig;
    
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
        $this->_changeTable('tbl_artdir_cats');
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
        $this->_changeTable('tbl_artdir_cats');
        return $this->delete('id', $catid, 'tbl_artdir_cats');
    }
    
    /**
     * Method to grab the top level parent categories per user id
     *
     * @param  integer $userid
     * @return array
     */
    public function getParentCats()
    {
        $this->_changeTable('tbl_artdir_cats');
        return $this->getAll("where cat_parent = '0'");
    }
    
    /**
     * Grab the child categories as a userl, according to the parent category
     *
     * @param  integer $userid
     * @param  string  $cat
     * @return unknown
     */
    public function getChildCats($cat)
    {
        $this->_changeTable('tbl_artdir_cats');
        $child = $this->getAll("where cat_parent = '$cat'");
        //return array(
        //    'child' => $child
        //);
        return $child;
    }
    
    /**
     * Method to get a single cat for edit
     *
     * @param
     */
    public function getCatForEdit($userid, $id)
    {
        $this->_changeTable('tbl_artdir_cats');
        $ret = $this->getAll("WHERE userid = '$userid' AND id = '$id'");
        return $ret[0];
    }
    
    /**
     * Method to create a merged array of the parent and child categories per user id
     *
     * @param  integer $userid
     * @return array
     */
    public function getCatsTree()
    {
        $parents = $this->getParentCats();
        $tree = new stdClass();
        if (empty($parents)) {
            $tree = NULL;
        } else {
            foreach($parents as $p) {
                $parent = $p;
                $child = $this->getChildCats($p['id']);
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
                $this->_changeTable('tbl_artdir_cats');
                return $this->update('id', $cats['id'], $cats, 'tbl_artdir_cats');
            }
            $this->_changeTable('tbl_artdir_cats');
            return $this->insert($cats, 'tbl_artdir_cats');
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
        $this->_changeTable('tbl_artdir_cats');
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
            // $this->_changeTable('tbl_artdir_posts');
            // return $this->getRecordCount();
        }
        // $this->_changeTable('tbl_artdir_posts');
        // return $this->getRecordCount("WHERE post_category = '$cat'");
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
        $this->_changeTable('tbl_artdir_linkcats');
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
        $this->_changeTable('tbl_artdir_links');
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
            $this->_changeTable('tbl_artdir_linkcats');
            return $this->insert($linkCats, 'tbl_artdir_linkcats');
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
        $this->_changeTable('tbl_artdir_links');
        if (!empty($linkarr)) {
            return $this->insert($linkarr, 'tbl_artdir_links');
        } else {
            return FALSE;
        }
    }
    
    public function getAllLinks($artistid) {
        $this->_changeTable('tbl_artdir_links');
        return $this->getAll("WHERE artistid = '$artistid'");
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
            return $this->objLanguage->languageText("mod_artdir_defcat", "artdir");
        } else {
            $this->_changeTable('tbl_artdir_cats');
            $catname = $this->getAll("WHERE id = '$catid'");
            if (!empty($catname)) {
                return $catname[0]['cat_name'];
            } else {
                return NULL;
            }
        }
    }
    
    public function checkforProfile($userid) {
        $this->_changeTable('tbl_artdir_artists');
        $profile = $this->getAll("WHERE userid = '$userid'");
        if(empty($profile)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    public function getRandArtists($onum = 3) {
        $this->_changeTable('tbl_artdir_artists');
        $people = $this->getAll();
        
        if(!empty($people)) {
            $count = count($people);
            if($count >= $onum) {
                $ret = array_rand($people, $onum);
                $back = array($people[$ret[0]], $people[$ret[1]], $people[$ret[2]]);
                return $back;
            }
            else {
                $ret = array_rand($people, $onum);
                $back = array($people[$ret[0]], $people[$ret[1]], $people[$ret[2]]);
                return $back;
            }
        }
        
        return $people;
    }
    
    public function getArtistById($id) {
        $this->_changeTable('tbl_artdir_artists');
        $artist = $this->getAll("WHERE id = '$id'");
        return $artist[0];
    }
    
    public function getAllArtists() {
        $this->_changeTable('tbl_artdir_artists');
        $artists = $this->getAll();
        return $artists;
    }
    
    public function getArtistsByCat($catid) {
        $this->_changeTable('tbl_artdir_artists');
        $recs = $this->getAll("WHERE catid = '$catid'");
        return $recs;
    }
    
    public function artistSearch($term) {
        $this->_changeTable('tbl_artdir_artists');
        $recs = $this->getAll("WHERE actname LIKE '%%$term%%' OR description LIKE '%%$term%%' OR contactperson LIKE '%%$term%%' OR contactnum LIKE '%%$term%%' OR altnum LIKE '%%$term%%' OR email LIKE '%%$term%%' OR website LIKE '%%$term%%' OR bio LIKE '%%$term%%'");
        return $recs;
    }
    
    public function updateArtist($updatearr) {
        $this->_changeTable('tbl_artdir_artists');
        return $this->update('id', $updatearr['id'], $updatearr, 'tbl_artdir_artists');
    }
    
    public function addArtist($artistarr) {
        $this->_changeTable('tbl_artdir_artists');
        return $this->insert($artistarr);
    }
    
    public function removeArtist($artistid) {
        $this->_changeTable('tbl_artdir_artists');
        $this->delete('id', $artistid, 'tbl_artdir_artists');
        return;
    }
    
    public function insertPic($insarr) {
        $this->_changeTable('tbl_artdir_images');
        return $this->insert($insarr);
    }
    
    public function getArtistPics($artistid) {
        $this->_changeTable('tbl_artdir_images');
        return $this->getAll("WHERE artistid = '$artistid'");
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

}
?>
