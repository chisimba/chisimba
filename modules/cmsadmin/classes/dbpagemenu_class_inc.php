<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Data access class for the cmsadmin module. Used to access data in tbl_cms_page_menu.
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright 2008 AVOIR
 * @license GNU GPL
 * @author Charl Mert
 */

class dbpagemenu extends dbTable {

    /**
     * The user object
     *
     * @access private
     * @var object
     */
    protected $_objUser;


    /**
     * The dbfrontpage object
     *
     * @access private
     * @var object
     */
    protected $_objFrontPage;

    /**
     * The language object
     *
     * @access private
     * @var object
     */
    protected $_objLanguage;

    /**
     * Class Constructor
     *
     * @access public
     * @return void
     */
    public function init() {
        try {
            parent::init('tbl_cms_page_menu');
            $this->table = 'tbl_cms_page_menu';
            $this->_objUser = & $this->getObject('user', 'security');
            $this->_objSecurity = & $this->getObject('dbsecurity', 'cmsadmin');
            $this->_objLanguage = & $this->newObject('language', 'language');
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }


    /**
     * Method to return the current and next levels child content
     *
     * @access public
     * @return bool
     * @author Charl Mert
     */
    public function getMenuText($menuKey) {
        //get the menu @ menuKey

        if ($menuKey != '') {
            //Get the Sub Menu
            $pageMenu = $this->getAll('WHERE menukey = \''.$menuKey.'\'');

        } else {
            //Get the Default/Main Menu
            $pageMenu = $this->getAll('WHERE menukey = \'default\'');

        }
        //var_dump($pageMenu[0]);

        if (isset($pageMenu[0]['body'])) {
            $body = stripslashes($pageMenu[0]['body']);
        }

        //Removing Notice
        if (!isset($body)) {
            $body = '';
        }

        return $body;
    }


    /**
     * Method to return the current Menu Item
     *
     * @access public
     * @return bool
     * @author Charl Mert
     */
    public function getMenuRow($menuId) {
        //get the menu @ menuKey

        $menuRow = $this->getAll('WHERE id = \''.$menuId.'\'');

        return $menuRow;
    }

    /**
     * Method to return the current Menu Item based on the menuKey
     *
     * @access public
     * @return bool
     * @author Charl Mert
     */
    public function getMenuRowByKey($key) {
        //get the menu @ menuKey

        $menuRow = $this->getAll('WHERE menukey = \''.$key.'\'');

        return $menuRow;
    }


    /**
     * Method to Check Weather the default menu has been added yet
     *
     * @access public
     * @return bool
     * @author Charl Mert
     */
    public function hasDefaultMenu() {
        $menuRow = $this->getAll('WHERE menukey = \'default\'');
        if (count($menuRow) > 0) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * Method to save a record to the database
     *
     * @access public
     * @return bool
     */
    public function addMenu() {
        //Create htmlcleaner object
        $objHtmlCleaner = $this->newObject('htmlcleaner', 'utilities');

        $name = $this->getParam('menuname');
        $menuKey = $this->getParam('menukey');

        //Get details of the new entry
        $creatorid = $this->getParam('creator',null);
        if ($creatorid==NUll) {
            $creatorid = $this->_objUser->userId();
        }

        $fullText = str_ireplace("<br />", " <br /> ", $this->getParam('body'));

        $fields = array(
                'name' => addslashes($name),
                'body' => addslashes($fullText),
                'menukey' => addslashes($menuKey),
                'userid' => $creatorid,
        );

        $sql = "SELECT id FROM tbl_cms_page_menu
					WHERE menukey = '".addslashes($menuKey)."'";

        //echo $sql;
        //exit;

        $data = $this->getArray($sql);

        if (count($data) > 0) {
            $newId = null;
            $id = $data[0]['id'];
            $this->update('id', $id, $fields);
        } else {
            $newId = $this->insert($fields);

            $fields['id'] = $newId;
            $this->luceneIndex($fields);
        }

        return $newId;
    }

    //TODO: Implement Trash and Restore of a menu item
    /**
     * Method move a menu item to trash
     *
     * @param string $id The id of the record that needs to be deleted
     * @access public
     * @return bool
     */
    public function trashMenu($id) {
        //First remove from front page
        $this->_objFrontPage->removeIfExists($id);

        $fields = array('trash' => 1, 'ordering' => '', 'end_publish' => $this->now());
        $result =  $this->update('id', $id, $fields);

        // Get the section id of the page - re order pages
        $pageData = $this->getContentPage($id);
        $sectionId = $pageData['sectionid'];
        $this->reorderContent($sectionId);

        $objLucene = $this->getObject('indexdata', 'search');
        $objLucene->removeIndex('cms_page_'.$id);

        return $result;
    }


    /**
     * Method to undelete content
     *
     * @param string $id The id of the record that needs to be deleted
     * @access public
     * @return bool
     */
    public function undelete($id) {
        $page = $this->getRow('id', $id);

        if ($page == FALSE) {
            return FALSE;
        } else {
            $order = $this->getOrdering($page['sectionid']);
            $fields = array('trash' => 0, 'ordering' => $order);

            $this->luceneIndex($page);

            return $this->update('id', $id, $fields);
        }
    }

    /**
     * Method to delete a content page
     *
     * @param string $id The id of the entry
     * @return boolean
     * @access public
     * @author Charl Mert
     */
    public function deleteMenu($id) {
        //Delete Menu
        $result = $this->delete('id', $id);

        // Remove from search
        $objLucene = $this->getObject('indexdata', 'search');
        $objLucene->removeIndex('cms_page_'.$id);

        return $result;
    }

    /**
     * Method to get the content
     *
     * @param string $filter The Filter
     * @return  array An array of associative arrays of all content pages in relationto filter specified
     * @access public
     */
    public function getContentPages($filter = '') {
        if ($filter == 'trash') {
            $filter = ' WHERE trash=1 ';
        } else {
            $filter = ' WHERE trash=0 ';
        }

        return $this->getAll($filter.' ORDER BY ordering');
    }

    /**
     * Method to get the archived content
     *
     * @author Megan Watson
     * @param string $filter The Filter
     * @return  array An array of associative arrays of all content pages in relationto filter specified
     * @access public
     */
    public function getArchivePages($filter = '') {
        $sql = "SELECT * FROM {$this->table} WHERE trash = 1 ";

        if(!empty($filter)) {
            $sql .= "AND LOWER(title) LIKE '%".strtolower($filter)."%' ";
        }

        $sql .= 'ORDER BY ordering';
        return $this->getArray($sql);
    }

    /**
     * Method to get a page content record
     *
     * @param string $id The id of the page content
     * @access public
     * @return array $content An associative array of content page details
     */
    public function getContentPage($id) {
        $content = $this->getRow('id', $id );
        return $content;
    }

    /**
     * Method to toggle the publish field
     *
     * @param string id The id if the content
     * @access public
     * @return boolean
     * @author Wesley Nitsckie
     */
    public function togglePublish($id) {
        $row = $this->getContentPage($id);

        if ($row['published'] == 1) {
            return $this->update('id', $id , array('published' => 0, 'end_publish' => $this->now(), 'start_publish' => '') );
        } else {
            return $this->update('id', $id , array('published' => 1, 'start_publish' => $this->now()) );
        }
    }

    /**
     * Method to publish or unpublish content
     *
     * @param string id The id if the content
     * @param string $task Publish or unpublish
     * @access public
     * @return boolean
     * @author Megan Watson
     */
    public function publish($id, $task = 'publish') {
        switch($task) {
            case 'publish':
                $fields['published'] = 1;
                $fields['start_publish'] = $this->now();
                $fields['end_publish'] = '';
                break;
            case 'unpublish':
                $fields['published'] = 0;
                $fields['end_publish'] = $this->now();
                break;
        }

        return $this->update('id', $id, $fields);
    }


    /**
     * Method to update all the content with the
     * sections that will be deleted
     *
     * @param string $sectionId The section Id
     * @return boolean
     * @access public
     */
    public function resetSection($sectionId) {
        $arrContent = $this->getAll("WHERE sectionid = '$sectionId'");
        $result = '';

        if(!empty($arrContent)) {
            foreach ($arrContent as $page) {
                //First remove from front page
                $this->_objFrontPage->removeIfExists($page['id']);

                // Trash / archive
                $fields = array('trash' => 1, 'ordering' => '');
                $result =  $this->update('id', $page['id'], $fields);
            }
        }
        return $result;
    }

    /**
     * Method to update all the content with the
     * sections that will be deleted
     *
     * @param string $sectionId The section Id
     * @return boolean
     * @access public
     */
    public function unarchiveSection($sectionId) {
        $arrContent = $this->getAll("WHERE sectionid = '$sectionId'");
        $result = '';

        if(!empty($arrContent)) {
            $order = 1;
            foreach ($arrContent as $page) {
                // Restore
                $fields = array('trash' => 0, 'ordering' => $order++);
                $result =  $this->update('id', $page['id'], $fields);
            }
        }
        return $result;
    }

    /**
     * Method to get all pages in a specific section
     *
     * @param string $sectionId The id of the section
     * @return array $pages An array of all pages in the section
     * @access public
     * @author Warren Windvogel
     */
    public function getPagesInSection($sectionId, $isPublished=FALSE) {
        $filter = "WHERE sectionid = '$sectionId' AND trash='0' ";
        if($isPublished) {
            $filter .= "AND published='1' ";
        }
        $pages = $this->getAll($filter.' ORDER BY ordering');

        $secureData = array();
        foreach ($pages as $d) {
            if ($this->_objSecurity->canUserReadContent($d['id'])) {
                array_push($secureData, $d);
            }
        }
        return $secureData;
    }

    /**
     * Method to get all pages in a specific section, including those on the front page
     *
     * @access public
     * @author Megan Watson
     * @param string $sectionId The id of the section
     * @return array $data An array of all pages in the section
     */
    public function getPagesInSectionJoinFront($sectionId) {
        $sql = "SELECT *, fr.id AS front_id, co.id AS page_id, co.ordering AS co_order
                FROM tbl_cms_content AS co 
                LEFT JOIN tbl_cms_content_frontpage AS fr ON (fr.content_id = co.id)
                WHERE sectionid = '$sectionId' AND trash='0'
                ORDER BY co.ordering";

        $data = $this->getArray($sql);

        $secureData = array();
        foreach ($data as $d) {
            if ($this->_objSecurity->canUserReadContent($d['page_id'])) {
                array_push($secureData, $d);
            }
        }

        return $secureData;
    }

    /**
     * Method to get the title and id of all pages in a specific section
     *
     * @param string $title The title of the section. Returns pages from all sections if NULL. Defaults to NULL
     * @param int $limit The amount of records to return. Returns all pages if NULL. Defaults to NULL
     * @return array $titles An array of associative arrays containing the id and title of all pages in the section
     * @access public
     * @author Warren Windvogel
     */
    public function getTitles($title = NULL, $limit = NULL) {
        //If only the section id is set, return all records in the section
        if($title == NULL && $limit != NULL) {
            $sql = "SELECT id, title FROM tbl_cms_content WHERE trash = '0' ORDER BY created DESC LIMIT '$limit'";
            //If only the limit is set, return set amount of pages from all sections
        } else if($title != NULL && $limit == NULL) {
            $sql = "SELECT id, title FROM tbl_cms_content WHERE title = '$title' ORDER BY created DESC";
            //If both params are set, return set amount of pages from specified section
        } else if($title != NULL && $limit != NULL) {
            $sql = "SELECT id, title FROM tbl_cms_content WHERE title = '$title' ORDER BY created DESC LIMIT '$limit'";
            //Else if neither param is set, return all records
        } else {
            $sql = "SELECT id, title FROM tbl_cms_content WHERE trash = '0' ORDER BY created DESC";
        }
        $titles = $this->getArray($sql);
        return $titles;
    }

    /**
     * Method to get the title and id of the last 5 pages added
     *
     * @return array $lastFiveTitles An array of associative arrays containing the id and title of
     * the last $n pages added
     * @param int $n The number of pages whose titles we should get
     * @access public
     * @author Warren Windvogel / added by Derek Keats 2007 01 17
     */
    public function getLatestTitles($n=5) {
        $sql = "SELECT id, title FROM tbl_cms_content WHERE trash = '0' ORDER BY created DESC LIMIT $n";
        return $this->getArray($sql);
    }

    /**
     * Method to get the number of pages in a specific section
     *
     * @param string $sectionId The id of the section
     * @return int $noPages The number of pages in the section
     * @access public
     * @author Warren Windvogel
     */
    public function getNumberOfPagesInSection($sectionId) {
        $noPages = '0';
        $pages = $this->getAll("WHERE sectionid = '$sectionId' AND trash='0' ORDER BY ordering");
        $noPages = count($pages);
        return $noPages;
    }

    /**
     * Method to return the ordering value of new content (gets added last)
     *
     * @param string $sectionId The id(pk) of the section the content is attached to
     * @return int $ordering The value to insert into the ordering field
     * @access public
     * @author Warren Windvogel
     */
    public function getPageOrder($pageId) {
        //get last order value
        $lastOrder = $this->getRow('id', $pageId);
        //add after this value
        $ordering = $lastOrder['ordering'];
        return $ordering;
    }

    /**
     * Method to return the ordering value of new content (gets added last)
     *
     * @param string $sectionId The id(pk) of the section the content is attached to
     * @return int $ordering The value to insert into the ordering field
     * @access public
     * @author Warren Windvogel
     */
    public function getOrdering($sectionId) {
        $ordering = 1;
        //get last order value
        $lastOrder = $this->getAll("WHERE sectionid = '$sectionId' AND trash = '0' ORDER BY ordering DESC LIMIT 1");
        //add after this value
        if (!empty($lastOrder)) {
            $ordering = $lastOrder['0']['ordering'] + 1;
        }

        return $ordering;
    }

    /**
     * Method to return the links to be displayed in the order column on the table
     *
     * @param string $id The id of the entry
     * @return string $links The html for the links
     * @access public
     * @author Warren Windvogel
     */
    public function getOrderingLink($sectionid, $id) {
        //Get the number of pages in the section
        $lastOrd = $this->getAll("WHERE sectionid = '$sectionid' AND trash = '0' ORDER BY ordering DESC LIMIT 1");
        $topOrder = $lastOrd['0']['ordering'];
        $links = " ";

        if ($topOrder > '1') {
            //Get the order position
            $entry = $this->getRow('id', $id);
            //Create geticon obj
            $this->objIcon = & $this->newObject('geticon', 'htmlelements');

            if ($entry['ordering'] == '1') {
                //return down arrow link
                //icon
                $this->objIcon->setIcon('downend');
                $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
                //link
                $downLink = & $this->newObject('link', 'htmlelements');
                $downLink->href = $this->uri(array('action' => 'changecontentorder', 'id' => $id, 'ordering' => 'up', 'sectionid' => $sectionid));
                $downLink->link = $this->objIcon->show();
                $links .= $downLink->show();
            } else if ($entry['ordering'] == $topOrder) {
                //return up arrow
                //icon
                $this->objIcon->setIcon('upend');
                $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
                //link
                $upLink = & $this->newObject('link', 'htmlelements');
                $upLink->href = $this->uri(array('action' => 'changecontentorder', 'id' => $id, 'ordering' => 'down', 'sectionid' => $sectionid));
                $upLink->link = $this->objIcon->show();
                $links .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $upLink->show();
            } else {
                //return both arrows
                //icon
                $this->objIcon->setIcon('down');
                $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
                //link
                $downLink = & $this->newObject('link', 'htmlelements');
                $downLink->href = $this->uri(array('action' => 'changecontentorder', 'id' => $id, 'ordering' => 'up', 'sectionid' => $sectionid));
                $downLink->link = $this->objIcon->show();
                //icon
                $this->objIcon->setIcon('up');
                $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
                //link
                $upLink = & $this->newObject('link', 'htmlelements');
                $upLink->href = $this->uri(array('action' => 'changecontentorder', 'id' => $id, 'ordering' => 'down', 'sectionid' => $sectionid));
                $upLink->link = $this->objIcon->show();
                $links .= $downLink->show() . '&nbsp;' . $upLink->show();
            }
        }

        return $links;
    }

    /**
     * Method to update the order of the frontpage
     *
     * @param string $id The id of the entry
     * @param string $id The id of the entry to move
     * @param int $ordering How to update the order(up or down).
     * @access public
     * @return bool
     * @author Warren Windvogel
     */
    public function changeOrder($sectionid, $id, $ordering) {
        //Get array of all page entries
        $fpContent = $this->getAll("WHERE sectionid = '$sectionid' AND trash = '0' ORDER BY ordering");
        //Search for entry to be reordered and update order
        foreach($fpContent as $content) {
            if ($content['id'] == $id) {
                if ($ordering == 'up') {
                    $changeTo = $content['ordering'];
                    $toChange = $content['ordering'] + 1;
                    $updateArray = array(
                            'modified' => $this->now(),
                            'ordering' => $toChange
                    );
                    $this->update('id', $id, $updateArray);
                } else {
                    $changeTo = $content['ordering'];
                    $toChange = $content['ordering'] - 1;
                    $updateArray = array(
                            'ordering' => $toChange,
                            'modified' => $this->now()
                    );
                    $this->update('id', $id, $updateArray);
                }
            }
        }

        //Get other entry to change
        $entries = $this->getAll("WHERE sectionid = '$sectionid' AND ordering = '$toChange' AND trash = '0'");
        foreach($entries as $entry) {
            if ($entry['id'] != $id) {
                $upArr = array(
                        'ordering' => $changeTo,
                        'modified' => $this->now()
                );
                $result = $this->update('id', $entry['id'], $upArr);
            }
        }

        // Reorder the content
        $this->reorderContent($sectionid);
        return $result;
    }

    /**
     * Method to scrub grubby html
     *
     * @param string $document
     * @return string
     */
    public function html2txt($document, $scrub = TRUE) {
        if($scrub == TRUE) {
            $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                    /*'@<[\/\!]*?[^<>]*?>@si',*/            // Strip out HTML tags
                    /*'@<style[^>]*?>.*?</style>@siU',*/    // Strip style tags properly
                    '@<![\s\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments including CDATA
            );

        }
        else {
            $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                    /*'@<style[^>]*?>.*?</style>@siU',*/    // Strip style tags properly
                    '@<![\s\S]*?--[ \t\n\r]*>@',        // Strip multi-line comments including CDATA
                    '!(\n*(.+)\n*!x',                   //strip out newlines...
            );
        }
        $text = preg_replace($search, '', $document);
        $text = str_replace("<br /><br />", '' ,$text);
        //$text = str_replace("<br />", '' ,$text);
        //$text = str_replace( '\n\n\n' , '\n' ,$text);
        $text = str_replace("<br />  <br />", "<br />", $text);
        $text = str_replace("<br\">","",$text);
        $text = str_replace("<br />", " <br /> ", $text);
        //$text = str_replace("<", " <", $text);
        //$text = str_replace(">", "> ", $text);
        $text = rtrim($text, "\n");
        return $text;
    }

    /**
     * The method implements the lucene indexer
     * The method accepts an array of data,
     * generates a document to be indexed based on the
     * url and content inserted into the database
     *
     * @param array $data
     */
    public function luceneIndex($data) {
        $objLucene = $this->getObject('indexdata', 'search');

        //Removing Notices
        $checkIndex = array('id', 'sectionid', 'created', 'title', 'body', 'introtext', 'created_by');
        foreach ($checkIndex as $index) {
            if (!isset($data[$index])) {
                $data[$index] = '';
            }
        }
        $docId = 'cms_page_'.$data['id'];

        $url = $this->uri(array
                ('module' => 'cms',
                'action' => 'showfulltext',
                'id' => $data['id'],
                'sectionid'=> $data['sectionid']), 'cms');

        $objLucene->luceneIndex($docId, $data['created'], $url, $data['title'], $data['title'].$data['body'], $data['introtext'], 'cms', $data['created_by']);
    }
}

?>
