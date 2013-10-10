<?PHP
//session_start();
//$_SESSION['userid'] = 1;
$GLOBALS['kewl_entry_point_run'] = true;
require_once 'classes/core/engine_class_inc.php';
//require_once 'PHPUnit/Framework/TestCase.php';

class cmsChecklist
{
    public $eng;
    public $security = array();
    public $session;
    protected $fixture;

    public $objContent;
    public $objSection;
    public $objModule;

    public function cmsChecklist() {
        $this->eng = new engine;
        $this->objContent = $this->eng->getObject('dbcontent', 'cmsadmin');
        $this->objSection = $this->eng->getObject('dbsections', 'cmsadmin');
        $this->objModule = $this->eng->getObject('modules', 'modulecatalogue');
        $this->objLucene = $this->eng->getObject('indexdata', 'search');

    }

    /*
     * Checking Weather Dependancies are satisfied
     * 
     */


    /*
     * Getting the UserId:
     */
    public function userId(){
        return $this->eng->getSession('userid');
    }

    /*
     * FileManager:
     */
    public function isFileManagerModuleInstalled(){
        return $this->objModule->checkIfRegistered('filemanager');
    }

    /*
     * Feed:
     */
    public function isFeedModuleInstalled(){
        return $this->objModule->checkIfRegistered('feed');
    }

    /*
     * Blockalicious:
     */
    public function isBlockaliciousModuleInstalled(){
        return $this->objModule->checkIfRegistered('blockalicious');
    }




    /*
     * Checking Lucene Indexing -----------------------------------
     * 
     */

    /*
     * Add Index using dbContent Custom Refined Indexer
     */
    public function luceneIndex($data){
        return $this->objContent->luceneIndex($data);
    }

    /*
     * Add Index using dbContent Custom Refined Indexer
     */
    public function removeIndex($data){
        return $objLucene->removeIndex('cms_page_'.$data['id']);
    }


    /*
     * =============== Core Logical Content Functions ===============
     */

    /*
     * Add Content
     */
    public function addContent($title = '',
                               $published = 0,
                               $override_date = null,
                               $start_publish = null,
                               $end_publish = null,
                               $creatorid = null,
                               $show_title = 'g',
                               $show_author = 'g',
                               $show_date = 'g',
                               $show_pdf = 'g',
                               $show_email = 'g',
                               $show_print = 'g',
                               $access = null,
                               $created_by = null,
                               $introText = null,
                               $fullText = null,
                               $metakey = null,
                               $metadesc = null,
                               $ccLicence = null,
							   $sectionId = null){
        
        return $this->objContent->addContent($title,
                                    $published,
                                    $override_date,
                                    $start_publish,
                                    $end_publish,
                                    $creatorid,
                                    $show_title,
                                    $show_author,
                                    $show_date,
                                    $show_pdf,
                                    $show_email,
                                    $show_print,
                                    $access,
                                    $created_by,
                                    $introText,
                                    $fullText,
                                    $metakey,
                                    $metadesc,
                                    $ccLicence,
									$sectionId);
    }

    /*
     * Edit Content
     */
    public function editContent($id ,
                                $title ,
                                $sectionid ,
                                $published ,
                                $access ,
                                $introText ,
                                $fullText ,
                                $override_date ,
                                $start_publish ,
                                $end_publish ,
                                $metakey ,
                                $metadesc ,
                                $ccLicence ,
                                $show_title ,
                                $show_author ,
                                $show_date ,
                                $show_pdf ,
                                $show_email ,
                                $show_print){
        
        return $this->objContent->editContent($id ,
                                $title ,
                                $sectionid ,
                                $published ,
                                $access ,
                                $introText ,
                                $fullText ,
                                $override_date ,
                                $start_publish ,
                                $end_publish ,
                                $metakey ,
                                $metadesc ,
                                $ccLicence ,
                                $show_title ,
                                $show_author ,
                                $show_date ,
                                $show_pdf ,
                                $show_email ,
                                $show_print);
    }


    /*
     * Methods for validating the Content was edited
     * Will be used in Assert Rules for unit test class
     */

    public function check_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get the ID: to check were dealing with the same ID.
     */
    
    public function check_title($record){
        if (isset($record['title'])){
            return $record['title'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get the sectionId
     */
    public function check_sectionid($record){
        if (isset($record['sectionid'])){
            return $record['sectionid'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get published
     */
    public function check_published($record){
        if (isset($record['published'])){
            return $record['published'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get access
     */
    public function check_access($record){
        if (isset($record['access'])){
            return $record['access'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get introText
     */
    public function check_introText($record){
        if (isset($record['introtext'])){
            return $record['introtext'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get fullText
     */
    public function check_fullText($record){
        if (isset($record['body'])){
            return $record['body'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get override_date
     */

    public function check_override_date($record){
        if (isset($record['override_date'])){
            return $record['override_date'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get start_publish
     */

    public function check_start_publish($record){
        if (isset($record['start_publish'])){
            return $record['start_publish'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get end_publish
     */
    public function check_end_publish($record){
        if (isset($record['end_publish'])){
            return $record['end_publish'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get metakey
     */
    public function check_metakey($record){
        if (isset($record['metakey'])){
            return $record['metakey'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get metadesc
     */
    public function check_metadesc($record){
        if (isset($record['metadesc'])){
            return $record['metadesc'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get ccLicence
     */

    public function check_ccLicence($record){
        if (isset($record['post_lic'])){
            return $record['post_lic'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get show_title
     */
    public function check_show_title($record){
        if (isset($record['show_title'])){
            return $record['show_title'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get show_author
     */
    public function check_show_author($record){
        if (isset($record['show_author'])){
            return $record['show_author'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get show_date
     */
    public function check_show_date($record){
        if (isset($record['show_date'])){
            return $record['show_date'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get show_pdf
     */
    public function check_show_pdf($record){
        if (isset($record['show_pdf'])){
            return $record['show_pdf'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get show_email
     */
    public function check_show_email($record){
        if (isset($record['show_email'])){
            return $record['show_email'];
        } else {
            return FALSE;
        }
    }

    /*
     * Get show_print
     */
    public function check_show_print($record){
        if (isset($record['show_print'])){
            return $record['show_print'];
        } else {
            return FALSE;
        }
    }






    
    /*
     * Trash Content
     */
    public function trashContent($id){
        return $this->objContent->trashContent($id);
    }


    /*
     * Restore Content
     */
    public function undelete($id){
        return $this->objContent->undelete($id);
    }


    /*
     * Delete Content Permanently
     */
    public function deleteContent($id){
        return $this->objContent->deleteContent($id);
    }

    /*
     * Retrieve multiple Content Records
     */
    public function getContentPages($filter){
        return $this->objContent->getContentPages($filter);
    }

    /*
     * Retrieve a single Content Record
     */
     public function getContentPage($id) {
        return $this->objContent->getContentPage($id);
     }


    /*
     * =============== Trivial Content Functions ===============
     */

    /* Depricated: to use a n level accessor getNChildContent($sectionId, $level)
     * Gets 2 levels of child content starting at the currently specified section
     */
     public function getChildContent($sectionid, $admin, $filter) {
        return $this->objContent->getChildContent($sectionid, $admin, $filter);
     }

    /* 
     * Gets N levels of child content starting at the currently specified section
     */
     public function getNChildContent($sectionId, $level, $published, $filter) {
        return $this->objContent->getNChildContent($sectionId, $level, $published, $filter);
     }


    /*
     * DESC:
     */
     public function addNewPage($title, $sectionid, $published, $access, $introText, $fullText, $isFrontPage, $ccLicence) {
        return $this->objContent->addNewPage($title, $sectionid, $published, $access, $introText, $fullText, $isFrontPage, $ccLicence);
     }


    /*
     * DESC:
     */
     public function updateContentBody($contentid, $body) {
        return $this->objContent->updateContentBody($contentid, $body);
     }

    /*
     * DESC:
     */
     public function getHrefContentRecords($sectionid = '') {
        return $this->objContent->getHrefContentRecords($sectionid = '');
     }


    /*
     * DESC:
     */
     private function reorderContent($sectionId) {
        return $this->objContent->reorderContent($sectionId);
     }


    /*
     * DESC:
     */
     public function getArchivePages($filter = '') {
        return $this->objContent->getArchivePages($filter = '');
     }

    /*
     * DESC:
     */
     public function getContentPageFiltered($id, $filter = '') {
        return $this->objContent->getContentPageFiltered($id, $filter = '');
     }

    /*
     * DESC:
     */
     public function togglePublish($id) {
        return $this->objContent->togglePublish($id);
     }

    /*
     * DESC:
     */
     public function publish($id, $task = 'publish') {
        return $this->objContent->publish($id, $task = 'publish');
     }



    /*
     * DESC:
     */
     public function getPageOrder($pageId) {
        return $this->OBJECT->getPageOrder($pageId);
     }

    /*
     * DESC:
     */
     public function getOrdering($sectionId) {
        return $this->OBJECT->getOrdering($sectionId);
     }

    /*
     * DESC:
     */
     public function getOrderingLink($sectionid, $id) {
        return $this->OBJECT->getOrderingLink($sectionid, $id);
     }

    /*
     * DESC:
     */
     public function changeOrder($sectionid, $id, $ordering) {
        return $this->OBJECT->changeOrder($sectionid, $id, $ordering);
     }

    /*
     * DESC:
     */
     public function html2txt($document, $scrub = TRUE) {
        return $this->OBJECT->html2txt($document, $scrub = TRUE);
     }

    /*
     * DESC:
     */
     public function getParent($contentId) {
        return $this->OBJECT->getParent($contentId);
     }


    /*
     * =============== Core Logical Section Functions ===============
     */

    /*
     * Add Section
     */

     public function addSection($title,
                                $parentId = 0,
                                $menuText = '',
                                $access = null,
                                $description = '',
                                $published = 0,
                                $layout = 'page',
                                $showIntroduction = 0,
                                $showTitle = 'g',
                                $showAuthor = 'g',
                                $showDate = 'g',
                                $pageNum = '0',
                                $customNum = null,
                                $pageOrder = 'pagedate_asc',
                                $imageUrl = null,
                                $contextCode = null) {
        return $this->objSection->addSection($title,
                                            $parentId,
                                            $menuText,
                                            $access,
                                            $description,
                                            $published,
                                            $layout,
                                            $showIntroduction,
                                            $showTitle,
                                            $showAuthor,
                                            $showDate,
                                            $pageNum,
                                            $customNum,
                                            $pageOrder,
                                            $imageUrl,
                                            $contextCode);
     }



    /*
     *  Edit Section
     */
    public function editSection($id,
                                $parentId = 0,
                                $title,
                                $menuText = '',
                                $access = null,
                                $description = '',
                                $published = 0,
                                $layout = 'page',
                                $showIntroduction = 0,
                                $showTitle = 'g',
                                $showAuthor = 'g',
                                $showDate = 'g',
                                $pageNum = '0',
                                $customNum = null,
                                $pageOrder = 'pagedate_asc',
                                $imageUrl = null,
                                $contextCode = null) {
        
        return $this->objSection->editSection($id,
                                              $parentId,
                                              $title,
                                              $menuText,
                                              $access,
                                              $description,
                                              $published,
                                              $layout,
                                              $showIntroduction,
                                              $showTitle,
                                              $showAuthor,
                                              $showDate,
                                              $pageNum,
                                              $customNum,
                                              $pageOrder,
                                              $imageUrl,
                                              $contextCode);
    }
 
 
    /*
     * Methods for validating the Section was edited
     * Will be used in Assert Rules for unit test class
     */
 
    /*
     * Get start_publish
     */

public function check_section_id($record){
    if (isset($record['id'])){
        return $record['id'];
    } else {
        return FALSE;
    }
}

public function check_section_rootid($record){
    if (isset($record['rootid'])){
        return $record['rootid'];
    } else {
        return FALSE;
    }
}
public function check_section_parentid($record){
    if (isset($record['parentid'])){
        return $record['parentid'];
    } else {
        return FALSE;
    }
}
public function check_section_title($record){
    if (isset($record['title'])){
        return $record['title'];
    } else {
        return FALSE;
    }
}
public function check_section_menutext($record){
    if (isset($record['menutext'])){
        return $record['menutext'];
    } else {
        return FALSE;
    }
}
public function check_section_description($record){
    if (isset($record['description'])){
        return $record['description'];
    } else {
        return FALSE;
    }
}
public function check_section_published($record){
    if (isset($record['published'])){
        return $record['published'];
    } else {
        return FALSE;
    }
}
public function check_section_showdate($record){
    if (isset($record['start_publish'])){
        return $record['start_publish'];
    } else {
        return FALSE;
    }
}
public function check_section_showintroduction($record){
    if (isset($record['showintroduction'])){
        return $record['showintroduction'];
    } else {
        return FALSE;
    }
}
public function check_section_numpagedisplay($record){
    if (isset($record['numpagedisplay'])){
        return $record['numpagedisplay'];
    } else {
        return FALSE;
    }
}
public function check_section_checked_out($record){
    if (isset($record['checked_out'])){
        return $record['checked_out'];
    } else {
        return FALSE;
    }
}
public function check_section_checked_out_time($record){
    if (isset($record['checked_out_time'])){
        return $record['checked_out_time'];
    } else {
        return FALSE;
    }
}
public function check_section_ordering($record){
    if (isset($record['ordering'])){
        return $record['ordering'];
    } else {
        return FALSE;
    }
}
public function check_section_ordertype($record){
    if (isset($record['ordertype'])){
        return $record['ordertype'];
    } else {
        return FALSE;
    }
}
public function check_section_access($record){
    if (isset($record['access'])){
        return $record['access'];
    } else {
        return FALSE;
    }
}
public function check_section_trash($record){
    if (isset($record['trash'])){
        return $record['trash'];
    } else {
        return FALSE;
    }
}
public function check_section_nodelevel($record){
    if (isset($record['nodelevel'])){
        return $record['nodelevel'];
    } else {
        return FALSE;
    }
}
public function check_section_params($record){
    if (isset($record['params'])){
        return $record['params'];
    } else {
        return FALSE;
    }
}
public function check_section_layout($record){
    if (isset($record['layout'])){
        return $record['layout'];
    } else {
        return FALSE;
    }
}
public function check_section_link($record){
    if (isset($record['link'])){
        return $record['link'];
    } else {
        return FALSE;
    }
}
public function check_section_userids($record){
    if (isset($record['userids'])){
        return $record['userids'];
    } else {
        return FALSE;
    }
}
public function check_section_groupid($record){
    if (isset($record['groupid'])){
        return $record['groupid'];
    } else {
        return FALSE;
    }
}
public function check_section_datecreated($record){
    if (isset($record['datecreated'])){
        return $record['datecreated'];
    } else {
        return FALSE;
    }
}
public function check_section_lastupdatedby($record){
    if (isset($record['lastupdatedby'])){
        return $record['lastupdatedby'];
    } else {
        return FALSE;
    }
}
public function check_section_updated($record){
    if (isset($record['updated'])){
        return $record['updated'];
    } else {
        return FALSE;
    }
}
public function check_section_startdate($record){
    if (isset($record['startdate'])){
        return $record['startdate'];
    } else {
        return FALSE;
    }
}
public function check_section_finishdate($record){
    if (isset($record['finishdate'])){
        return $record['finishdate'];
    } else {
        return FALSE;
    }
}
public function check_section_contextcode($record){
    if (isset($record['contextcode'])){
        return $record['contextcode'];
    } else {
        return FALSE;
    }
}
public function check_section_public_access($record){
    if (isset($record['public_access'])){
        return $record['public_access'];
    } else {
        return FALSE;
    }
}
public function check_section_puid($record){
    if (isset($record['puid'])){
        return $record['puid'];
    } else {
        return FALSE;
    }
}
public function check_section_show_title($record){
    if (isset($record['show_title'])){
        return $record['show_title'];
    } else {
        return FALSE;
    }
}

public function check_section_show_user($record){
    if (isset($record['show_user'])){
        return $record['show_user'];
    } else {
        return FALSE;
    }
}
public function check_section_show_date($record){
    if (isset($record['show_date'])){
        return $record['show_date'];
    } else {
        return FALSE;
    }
}
public function check_section_show_introduction($record){
    if (isset($record['show_introduction'])){
        return $record['show_introduction'];
    } else {
        return FALSE;
    }
}

public function check_section_userid($record){
    if (isset($record['userid'])){
        return $record['userid'];
    } else {
        return FALSE;
    }
}


/*
 * Method to return all sections
 */
public function getAllSections($clause= ''){
	return $this->objSection->getAll($clause);
}

/*
 * Method to return a single section
 */
public function getSection($id){
	return $this->objSection->getSection($id);
}


    /*
     * DESC:
     */
     public function getRootNodeId($parentId) {
        return $this->OBJECT->getRootNodeId($parentId);
     }
 

    
}


?>

