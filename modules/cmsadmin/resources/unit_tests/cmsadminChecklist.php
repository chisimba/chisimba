<?PHP
$GLOBALS['kewl_entry_point_run'] = true;
require_once 'classes/core/engine_class_inc.php';
require_once 'PHPUnit/Framework/TestCase.php';

class stateCache {
    public $currentId;
    public $recordCountBefore;
    public $recordCountAfter;

    public function memCache(){
        $this->currentId = '';
        $this->recordCountBefore = 0;
        $this->recordCountAfter = 0;
    }
}

class cmsadminChecklist extends PHPUnit_Framework_TestCase
{
    public $eng;
    public $security = array();
    public $session;

    //Generated checklist members
    public $treenodes;
    public $dbgroups;
    public $dbsectiongroup;
    public $dbblocks;
    public $cmsutils;
    public $buildtree;
    public $dbcontentfrontpage;
    public $dblayouts;
    public $cmstree;
    public $dbcontent;
    public $dbcmsadmin;
    public $dbcontentpreview;
    public $superfishtree;
    public $rpcdbcmsadmin;
    public $dbhtmlblock;
    public $dbsections;
    public $dbtemplate;
    public $contenttree;
    public $articlebox;
    public $simplecontenttree;
    public $dbmenustyles;
    public $dbsecurity;
    public $pagemenu;
    public $simpletreemenu;
    public $dbpagemenu;
    public $dbcategories;

    public $objState;

    public function setup () {
        $this->objState = new stateCache();
        $this->eng = new engine;
        $this->objModule = $this->eng->getObject('modules', 'modulecatalogue');

        //Generated checklist member initialization
        $this->treenodes = $this->eng->getObject('treenodes', 'cmsadmin');
        $this->dbgroups = $this->eng->getObject('dbgroups', 'cmsadmin');
        $this->dbsectiongroup = $this->eng->getObject('dbsectiongroup', 'cmsadmin');
        $this->dbblocks = $this->eng->getObject('dbblocks', 'cmsadmin');
        $this->cmsutils = $this->eng->getObject('cmsutils', 'cmsadmin');
        $this->buildtree = $this->eng->getObject('buildtree', 'cmsadmin');
        $this->dbcontentfrontpage = $this->eng->getObject('dbcontentfrontpage', 'cmsadmin');
        $this->dblayouts = $this->eng->getObject('dblayouts', 'cmsadmin');
        $this->cmstree = $this->eng->getObject('cmstree', 'cmsadmin');
        $this->dbcontent = $this->eng->getObject('dbcontent', 'cmsadmin');
        $this->dbcmsadmin = $this->eng->getObject('dbcmsadmin', 'cmsadmin');
        $this->dbcontentpreview = $this->eng->getObject('dbcontentpreview', 'cmsadmin');
        $this->superfishtree = $this->eng->getObject('superfishtree', 'cmsadmin');
        $this->rpcdbcmsadmin = $this->eng->getObject('rpcdbcmsadmin', 'cmsadmin');
        $this->dbhtmlblock = $this->eng->getObject('dbhtmlblock', 'cmsadmin');
        $this->dbsections = $this->eng->getObject('dbsections', 'cmsadmin');
        $this->dbtemplate = $this->eng->getObject('dbtemplate', 'cmsadmin');
        $this->contenttree = $this->eng->getObject('contenttree', 'cmsadmin');
        $this->articlebox = $this->eng->getObject('articlebox', 'cmsadmin');
        $this->simplecontenttree = $this->eng->getObject('simplecontenttree', 'cmsadmin');
        $this->dbmenustyles = $this->eng->getObject('dbmenustyles', 'cmsadmin');
        $this->dbsecurity = $this->eng->getObject('dbsecurity', 'cmsadmin');
        $this->pagemenu = $this->eng->getObject('pagemenu', 'cmsadmin');
        $this->simpletreemenu = $this->eng->getObject('simpletreemenu', 'cmsadmin');
        $this->dbpagemenu = $this->eng->getObject('dbpagemenu', 'cmsadmin');
        $this->dbcategories = $this->eng->getObject('dbcategories', 'cmsadmin');

    }

    /*
     * Check filemanager dependancy:
     */
    public function test_is_filemanagerModuleInstalled(){
        $this->assertEquals(1, $this->objModule->checkIfRegistered('filemanager'));
        //return $this->objModule->checkIfRegistered('filemanager');
    }

    /*
     * Check feed dependancy:
     */
    public function test_is_feedModuleInstalled(){
        $this->assertEquals(1, $this->objModule->checkIfRegistered('feed'));
        //return $this->objModule->checkIfRegistered('feed');
    }

    

    /*
     * Check blockalicious dependancy:
     */
    public function test_is_blockaliciousModuleInstalled(){
        $this->assertEquals(1, $this->objModule->checkIfRegistered('blockalicious'));
        //return $this->objModule->checkIfRegistered('blockalicious');
    }

    

    /*
     * Check popupcalendar dependancy:
     */
    public function test_is_popupcalendarModuleInstalled(){
        $this->assertEquals(1, $this->objModule->checkIfRegistered('popupcalendar'));
        //return $this->objModule->checkIfRegistered('popupcalendar');
    }

    

    /*
     * Check pdfmaker dependancy:
     */
    public function test_is_pdfmakerModuleInstalled(){
        $this->assertEquals(1, $this->objModule->checkIfRegistered('pdfmaker'));
        //return $this->objModule->checkIfRegistered('pdfmaker');
    }

    

    /*
     * Check shorturl dependancy:
     */
    public function test_is_shorturlModuleInstalled(){
        $this->assertEquals(1, $this->objModule->checkIfRegistered('shorturl'));
        //return $this->objModule->checkIfRegistered('shorturl');
    }

 	/*
	 * Checking Data Management Methods : dbcontent_class_inc.php
	 */
   
	public function test_dbcontent_data_management() {

//Retrieve Content Records
$result = $this->dbcontent->getContentPages('');
$this->recordCountBefore = count($result);

//Add Content
//TODO: Should these be declared as individual members / fixtures?

$title = 'CMS Unit Test ' . date("YMD");
$published = 0;
$override_date = null;
$start_publish = null;
$end_publish = null;
$creatorid = 'init_1';
$show_title = 'y';
$show_author = 'y';
$show_date = 'y';
$show_pdf = 'y';
$show_email = 'y';
$show_print = 'y';
$access = null;
$created_by = 'init_1';
$introText = 'Unit Test Intro Text ';
$fullText = 'Unit Test Full Text';
$metakey = 'Unit Test Meta Key';
$metadesc = 'Unit Test Meta Description';
$ccLicence = null;
$sectionId = 'init_1';

$result = $this->dbcontent->addContent($title,
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

        $this->currentId = $result;

        //TODO: check if the are params before displaying (...) for warnings

        $this->assertNotEquals('', $result, 'Warning: dbcontent->addContent(...) Returned \'\'');
        $this->assertNotEquals(null, $result, 'Warning: dbcontent->addContent(...) Returned NULL');

        //Checking that the fields where added correctly
        $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
        $record = $record[0];

        //Checking ID
        $this->assertNotEquals(FALSE, ($this->cmsadmin_tbl_cms_content_id($record) == $this->currentId));
        $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_id($record));

        //Checking Title
        $this->assertEquals($title, $this->cmsadmin_tbl_cms_content_title($record));
        
        //Checking Published
        $this->assertEquals($published, $this->cmsadmin_tbl_cms_content_published($record));

        //Checking Override_date
        $this->assertNotEquals($override_date, $this->cmsadmin_tbl_cms_content_override_date($record));

        //Checking Start Publish
        $this->assertEquals($start_publish, $this->cmsadmin_tbl_cms_content_start_publish($record));

        //Checking End Publish
        $this->assertEquals($end_publish, $this->cmsadmin_tbl_cms_content_end_publish($record));

        //Checking creatorid
        $this->assertEquals($creatorid, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking Show Title
        $this->assertEquals($show_title, $this->cmsadmin_tbl_cms_content_show_title($record));

        //Checking Show Author
        $this->assertEquals($show_author, $this->cmsadmin_tbl_cms_content_show_author($record));

        //Checking Show Date
        $this->assertEquals($show_date, $this->cmsadmin_tbl_cms_content_show_date($record));

        //Checking Show Pdf
        $this->assertEquals($show_pdf, $this->cmsadmin_tbl_cms_content_show_pdf($record));

        //Checking Show Email
        $this->assertEquals($show_email, $this->cmsadmin_tbl_cms_content_show_email($record));

        //Checking Show Print
        $this->assertEquals($show_print, $this->cmsadmin_tbl_cms_content_show_print($record));

        //Checking Access
        $this->assertEquals($access, $this->cmsadmin_tbl_cms_content_access($record));

        //Checking Created By
        $this->assertEquals($created_by, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking IntroText
        $this->assertEquals($introText, $this->cmsadmin_tbl_cms_content_introtext($record));

        //Checking FullText
        $this->assertEquals($fullText, $this->cmsadmin_tbl_cms_content_body($record));

        //Checking Metakey
        $this->assertEquals($metakey, $this->cmsadmin_tbl_cms_content_metakey($record));

        //Checking Meta Description
        $this->assertEquals($metadesc, $this->cmsadmin_tbl_cms_content_metadesc($record));

        //Checking Licence
        $this->assertEquals($ccLicence, $this->cmsadmin_tbl_cms_content_post_lic($record));

        //Checking Meta Description
        $this->assertEquals($sectionId, $this->cmsadmin_tbl_cms_content_sectionid($record));
                    


$id = $this->currentId;
$title = 'Unit Test Title Edited';
$sectionId = 'init_2';
$published = 1;
$access = 1;
$introText = 'Unit Test Intro Text Edited';
$fullText = 'Unit Test Full Text Edited';
$override_date = '2008-01-01 00:00:00';
$start_publish = '2008-01-01 00:00:00';
$start_publish = '2008-01-01 00:00:00';
$end_publish = '2008-01-01 00:00:00';
$override_date = '2008-01-01 00:00:00';
$metakey = 'Unit Test Meta Key Edited';
$metadesc = 'Unit Test Meta Description Edited';
$ccLicence = 'New Licence';
$show_title = 'n';
$show_author = 'y';
$show_date = 'n';
$show_pdf = 'y';
$show_email = 'n';
$show_print = 'y';

$result = $this->dbcontent->editContent($id ,
                        $title ,
                        $sectionId ,
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


        //Checking that content fields were properly edited

        $this->assertNotEquals('', $result, 'Warning: dbcontent->editContent(...) Returned \'\'');
        $this->assertNotEquals(null, $result, 'Warning: dbcontent->editContent(...) Returned NULL');
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->editContent(...) Returned FALSE');

        //Checking that the fields where edited correctly
        $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
        $record = $record[0];

        //Checking ID
        $this->assertNotEquals(FALSE, ($this->cmsadmin_tbl_cms_content_id($record) == $this->currentId));
        $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_id($record));

        //Checking Title
        $this->assertEquals($title, $this->cmsadmin_tbl_cms_content_title($record));

        //Checking Published
        $this->assertEquals($published, $this->cmsadmin_tbl_cms_content_published($record));

        //Checking Override_date
        $this->assertEquals($override_date, $this->cmsadmin_tbl_cms_content_override_date($record));

        //Checking Start Publish
        $this->assertEquals($start_publish, $this->cmsadmin_tbl_cms_content_start_publish($record));

        //Checking End Publish
        $this->assertEquals($end_publish, $this->cmsadmin_tbl_cms_content_end_publish($record));

        //Checking creatorid
        $this->assertEquals($creatorid, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking Show Title
        $this->assertEquals($show_title, $this->cmsadmin_tbl_cms_content_show_title($record));

        //Checking Show Author
        $this->assertEquals($show_author, $this->cmsadmin_tbl_cms_content_show_author($record));

        //Checking Show Date
        $this->assertEquals($show_date, $this->cmsadmin_tbl_cms_content_show_date($record));

        //Checking Show Pdf
        $this->assertEquals($show_pdf, $this->cmsadmin_tbl_cms_content_show_pdf($record));

        //Checking Show Email
        $this->assertEquals($show_email, $this->cmsadmin_tbl_cms_content_show_email($record));

        //Checking Show Print
        $this->assertEquals($show_print, $this->cmsadmin_tbl_cms_content_show_print($record));

        //Checking Access
        $this->assertEquals($access, $this->cmsadmin_tbl_cms_content_access($record));

        //Checking Created By
        $this->assertEquals($created_by, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking IntroText
        $this->assertEquals($introText, $this->cmsadmin_tbl_cms_content_introtext($record));

        //Checking FullText
        $this->assertEquals($fullText, $this->cmsadmin_tbl_cms_content_body($record));

        //Checking Metakey
        $this->assertEquals($metakey, $this->cmsadmin_tbl_cms_content_metakey($record));

        //Checking Meta Description
        $this->assertEquals($metadesc, $this->cmsadmin_tbl_cms_content_metadesc($record));

        //Checking Licence
        $this->assertEquals($ccLicence, $this->cmsadmin_tbl_cms_content_post_lic($record));

        //Checking Meta Description
        $this->assertEquals($sectionId, $this->cmsadmin_tbl_cms_content_sectionid($record));



    /*
     * =============== Logical Edit Data Methods =======dbcontent_class_inc.php========
     */
        //DEPRICATED
        //return $this->dbcontent->edit();

    //--------- Checking updateContentBody -----------
    $contentid = $this->currentId;
    $body = 'PHPUNIT Test Update Body';
    $result = $this->dbcontent->updateContentBody($contentid,$body);
    //Checking Result
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->trashContent($id) returned FALSE');
    
    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Body
    $this->assertEquals($body, $this->cmsadmin_tbl_cms_content_body($record));
    //------------------------------------------------


    //--------- Checking trashContent -----------
    $id = $this->currentId;
    $result = $this->dbcontent->trashContent($id);
    //Checking Result
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->trashContent($id) returned FALSE');
    
    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Trash
    $this->assertEquals(1, $this->cmsadmin_tbl_cms_content_trash($record));
    
    //Checking Ordering
    $this->assertEquals('', $this->cmsadmin_tbl_cms_content_ordering($record));

    //Checking End Publish
    $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_end_publish($record));
    //-------------------------------------------


    //--------- Checking reorderContent -----------
    /*
    $id = $this->currentId;
    $result = $this->dbcontent->reorderContent($id);
    //Checking Result
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->trashContent($id) returned FALSE');

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Trash
    $this->assertEquals(1, $this->cmsadmin_tbl_cms_content_trash($record));
    */
    //---------------------------------------------


    //--------- Checking Undelete -----------
    $result = $this->dbcontent->undelete($this->currentId);
    //Checking Result
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->undelete($id) Returned FALSE - Was the item successfully restored?');

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Trash
    $this->assertEquals(0, $this->cmsadmin_tbl_cms_content_trash($record));
    //---------------------------------------------

    
    //--------- Checking Toggle Publish -----------
    //Checking Undelete
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->togglePublish($id) Returned FALSE - Was the item successfully restored?');

    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Published
    $varPublished = $this->cmsadmin_tbl_cms_content_published($record);

    $result = $this->dbcontent->togglePublish($id);
    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Publish
    $this->assertNotEquals($varPublished, $this->cmsadmin_tbl_cms_content_trash($record));
    //---------------------------------------------
    

    //--------- Checking Publish : TASK publish -----------
    $id = $this->currentId;
    $task = 'publish';
    $result = $this->dbcontent->publish($id,$task);
    
    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Publish
    $this->assertEquals(1, $this->cmsadmin_tbl_cms_content_published($record));
    //---------------------------------------------

    //--------- Checking Publish : TASK unpublish -----------
    $id = $this->currentId;
    $task = 'unpublish';
    $result = $this->dbcontent->publish($id,$task);

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Publish
    $this->assertEquals(0, $this->cmsadmin_tbl_cms_content_published($record));
    //---------------------------------------------

    /*
    public function cmsadmin_dbcontent_resetSection($sectionId) {
        return $this->dbcontent->resetSection($sectionId);
    }

    public function cmsadmin_dbcontent_unarchiveSection($sectionId) {
        return $this->dbcontent->unarchiveSection($sectionId);
    }
    */

    //--------- Checking Publish : TASK unpublish -----------
    //TODO: Complete changeOrder
    //      Add Test Data (One section with a couple of content items)
    /*
    $id = $this->currentId;
    $sectionid = 'init_1';
    $task = 'unpublish';
    $result = $this->dbcontent->changeOrder($sectionid,$id,$ordering);
    */

    /*
     * =============== Logical Delete Data Methods =======dbcontent_class_inc.php========
     */

    $id = $this->currentId;
    $result = $this->dbcontent->deleteContent($id);

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Publish
    $this->assertEquals(0, count($record));

    }



    /*
     * Checking Data Management Methods : dbcontent_class_inc.php
     */

    public function test_dbsections_data_management() {

        //Retrieve Content Records
        $result = $this->dbsections->getSections();
        $this->recordCountBefore = count($result);


//Preparing the database for tests
$section = $this->dbsections->getAll(" WHERE title = 'Test Title Unit Test' ");
if ($section[0]['id'] != '') {
    $this->dbsections->permanentlyDelete($section[0]['id']);
}

//Save the section
$parentId = 0;
$title = 'Test Title Unit Test';
$menuText = 'Test Menu Text Unit Test';
$access = null;
$description = 'Test Description Unit Test';
$published = '0';
$layout = 'page';
$showIntroduction = 'y';
$showTitle = 'y';
$showAuthor = 'y';
$showDate = 'y';
$customNum = null;
$pageNum = '0';
$pageOrder = 'pagedate_asc';
$imageUrl = null;
$contextCode = null;

//Add Section
$result = $this->dbsections->addSection($title,
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

        $this->currentId = $result;

        $this->assertNotEquals('', $result, 'Warning: dbsections->addSection(...) Returned \'\'');
        $this->assertNotEquals(null, $result, 'Warning: dbsections->addSection(...) Returned NULL');
        $this->assertNotEquals(FALSE, $result, 'Warning: dbsections->addSection(...) Returned FALSE');
        
        //Checking that the fields where added correctly
        $record = $this->dbsections->getAll(" WHERE id = '{$this->currentId}' ");
        $record = $record[0];

        //Checking ID
        $this->assertNotEquals(FALSE, ($this->cmsadmin_tbl_cms_content_id($record) == $this->currentId));
        $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_id($record));

        //Checking Fields
        $this->assertEquals($title, $this->cmsadmin_tbl_cms_sections_title($record));
        $this->assertEquals($parentId, $this->cmsadmin_tbl_cms_sections_parentid($record));
        $this->assertEquals($menuText, $this->cmsadmin_tbl_cms_sections_menutext($record));
        $this->assertEquals($access, $this->cmsadmin_tbl_cms_sections_access($record));
        $this->assertEquals($description, $this->cmsadmin_tbl_cms_sections_description($record));
        $this->assertEquals($published, $this->cmsadmin_tbl_cms_sections_published($record));
        $this->assertEquals($layout, $this->cmsadmin_tbl_cms_sections_layout($record));
        $this->assertEquals($showIntroduction, $this->cmsadmin_tbl_cms_sections_show_introduction($record));
        $this->assertEquals($showTitle, $this->cmsadmin_tbl_cms_sections_show_title($record));
        $this->assertEquals($showAuthor, $this->cmsadmin_tbl_cms_sections_show_user($record));
        $this->assertEquals($showDate, $this->cmsadmin_tbl_cms_sections_show_date($record));
        $this->assertEquals($pageNum, $this->cmsadmin_tbl_cms_sections_numpagedisplay($record));
        //$this->assertEquals($customNum, $this->cmsadmin_tbl_cms_sections_numpagedisplay($record));
        $this->assertEquals($pageOrder, $this->cmsadmin_tbl_cms_sections_ordertype($record));
        $this->assertEquals($imageUrl, $this->cmsadmin_tbl_cms_sections_link($record));
        $this->assertEquals($contextCode, $this->cmsadmin_tbl_cms_sections_contextcode($record));

$testRootId = $result;

//Add A Child Section using the id retrieved from the $result above
//$parentId = $result;
$parentId = 'gen12Srv1Nme13_6789_1233311642';
$title = 'Child Test Title Unit Test';
$menuText = 'Child Test Menu Text Unit Test';
$access = null;
$description = 'Child Test Description Unit Test';
$published = '0';
$layout = 'page';
$showIntroduction = 'y';
$showTitle = 'y';
$showAuthor = 'y';
$showDate = 'y';
$customNum = '24';
$pageNum = 'custom';
$pageOrder = 'pagedate_asc';
$imageUrl = null;
$contextCode = null;

//Add a Child Section \n";
$result = $this->dbsections->addSection($title,
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

        $this->secondId = $result;

        $this->assertNotEquals('', $result, 'Warning: dbsections->addSection(...) Returned \'\'');
        $this->assertNotEquals(null, $result, 'Warning: dbsections->addSection(...) Returned NULL');

        //Checking that the fields where added correctly
        $record = $this->dbsections->getAll(" WHERE id = '{$this->secondId}' ");
        $record = $record[0];

        //Checking ID
        $this->assertNotEquals(FALSE, ($this->cmsadmin_tbl_cms_content_id($record) == $this->secondId));
        $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_id($record));

        //Checking Fields
        $this->assertEquals($title, $this->cmsadmin_tbl_cms_sections_title($record));
        $this->assertEquals($parentId, $this->cmsadmin_tbl_cms_sections_parentid($record));
        $this->assertEquals($menuText, $this->cmsadmin_tbl_cms_sections_menutext($record));
        $this->assertEquals($access, $this->cmsadmin_tbl_cms_sections_access($record));
        $this->assertEquals($description, $this->cmsadmin_tbl_cms_sections_description($record));
        $this->assertEquals($published, $this->cmsadmin_tbl_cms_sections_published($record));
        $this->assertEquals($layout, $this->cmsadmin_tbl_cms_sections_layout($record));
        $this->assertEquals($showIntroduction, $this->cmsadmin_tbl_cms_sections_show_introduction($record));
        $this->assertEquals($showTitle, $this->cmsadmin_tbl_cms_sections_show_title($record));
        $this->assertEquals($showAuthor, $this->cmsadmin_tbl_cms_sections_show_user($record));
        $this->assertEquals($showDate, $this->cmsadmin_tbl_cms_sections_show_date($record));
        //$this->assertEquals($pageNum, $this->cmsadmin_tbl_cms_sections_numpagedisplay($record));
        $this->assertEquals($customNum, $this->cmsadmin_tbl_cms_sections_numpagedisplay($record));
        $this->assertEquals($pageOrder, $this->cmsadmin_tbl_cms_sections_ordertype($record));
        $this->assertEquals($imageUrl, $this->cmsadmin_tbl_cms_sections_link($record));
        $this->assertEquals($contextCode, $this->cmsadmin_tbl_cms_sections_contextcode($record));

//Edit the section
$id = $this->secondId;
$parentId = 'init_1';
$rootId = '0';
$title = 'Test Title Unit Test Edited';
$menuText = 'Test Menu Text Unit Test Edited';
$access = null;
$description = 'Test Description Unit Test Edited';
$published = '1';
$layout = 'page Edited';
$showIntroduction = 'n';
$showTitle = 'n';
$showAuthor = 'n';
$showDate = 'n';
$customNum = '2';
$pageNum = 'custom';
$pageOrder = 'pagedate_asc Edited';
$imageUrl = 'Edited';
$contextCode = 'Edited';

//Edit Section
$result = $this->dbsections->editSection($id,
                            $parentId,
                            $rootId,
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

        $result;

        $this->assertNotEquals('', $result, 'Warning: dbsections->editSection(...) Returned \'\'');
        $this->assertNotEquals(null, $result, 'Warning: dbsections->editSection(...) Returned NULL');

        //Checking that the fields where added correctly
        $record = $this->dbsections->getAll(" WHERE id = '{$this->secondId}' ");
        $record = $record[0];

        //Checking ID
        $this->assertNotEquals(FALSE, ($this->cmsadmin_tbl_cms_content_id($record) == $this->secondId));
        $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_id($record));

        //Checking Fields
        $this->assertEquals($title, $this->cmsadmin_tbl_cms_sections_title($record));
        $this->assertEquals($parentId, $this->cmsadmin_tbl_cms_sections_parentid($record));
        $this->assertEquals($menuText, $this->cmsadmin_tbl_cms_sections_menutext($record));
        $this->assertEquals($access, $this->cmsadmin_tbl_cms_sections_access($record));
        $this->assertEquals($description, $this->cmsadmin_tbl_cms_sections_description($record));
        $this->assertEquals($published, $this->cmsadmin_tbl_cms_sections_published($record));
        $this->assertEquals($layout, $this->cmsadmin_tbl_cms_sections_layout($record));
        $this->assertEquals($showIntroduction, $this->cmsadmin_tbl_cms_sections_show_introduction($record));
        $this->assertEquals($showTitle, $this->cmsadmin_tbl_cms_sections_show_title($record));
        $this->assertEquals($showAuthor, $this->cmsadmin_tbl_cms_sections_show_user($record));
        $this->assertEquals($showDate, $this->cmsadmin_tbl_cms_sections_show_date($record));
        //$this->assertEquals($pageNum, $this->cmsadmin_tbl_cms_sections_numpagedisplay($record));
        $this->assertEquals($customNum, $this->cmsadmin_tbl_cms_sections_numpagedisplay($record));
        $this->assertEquals($pageOrder, $this->cmsadmin_tbl_cms_sections_ordertype($record));
        $this->assertEquals($imageUrl, $this->cmsadmin_tbl_cms_sections_link($record));
        $this->assertEquals($contextCode, $this->cmsadmin_tbl_cms_sections_contextcode($record));


        //--------- Checking dbsections Lucene Index -----------
$data = array(
"id" => $this->secondId,
"rootid" => "0",
"parentid" => "init_1",
"title" => "Lucene Index Db Sections : Title",
"menutext" => "Lucene Index Db Sections : Menutext",
"description" => "Lucene Index Db Sections : Description",
"published" => "0",
"showdate" => "y",
"showintroduction" => "y",
"numpagedisplay" => "custom",
"checked_out" => "1",
"checked_out_time" => "2008-09-01 59:59:59",
"ordering" => "0",
"ordertype" => "pagedate_asc",
"access" => "0",
"trash" => "1",
"nodelevel" => "2",
"params" => "NULL",
"layout" => "Lucene Test Layout",
"link" => "http://www.google.com/lucene",
"userids" => "",
"groupid" => "NULL",
"datecreated" => "2008-09-01 59:59:59",
"lastupdatedby" => "NULL",
"updated" => "NULL",
"startdate" => "2008-09-01 59:59:59",
"finishdate" => "2008-09-01 59:59:59",
"contextcode" => "Lucene Test",
"public_access" => "1",
"show_title" => "y",
"show_user" => "y",
"show_date" => "y",
"show_introduction" => "y",
"userid" => "init_1");

        //TODO: Make luceneIndex method return something meaningfull
        //$result = $this->dbsections->luceneIndex($data);
        //Checking Result
        //$this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->luceneIndex($data) returned FALSE');
        //-------------------------------------------


        //--------- Checking dbsections Remove Lucene Index -----------
        //TODO: Make luceneIndex return something meaningfull
        /*
        $result = $this->dbsections->removeLuceneIndex($this->secondId);
        //Checking Result
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->luceneIndex($data) returned FALSE');
        */
        //-------------------------------------------

        //--------- Checking dbsections isDuplicateSection -----------
        $result = $this->dbsections->isDuplicateSection($title,$parentId);
        //Checking Result
        $this->assertNotEquals(FALSE, $result, 'Warning: dbsections->isDuplicateSection(\''.$title . '\',\'' . $parentId . '\'); returned FALSE');
        //-------------------------------------------

        //--------- Checking dbsections isDuplicateSection -----------
        $result = $this->dbsections->isSections();
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        //-------------------------------------------

        //--------- Checking dbsections hasNodes -----------
        $result = $this->dbsections->hasNodes($this->currentId);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        //-------------------------------------------

        //--------- Checking dbsections hasChildContent -----------
        //TODO: Set up a useful test case to check for child content
        /*
        $result = $this->dbsections->hasChildContent($this->currentId);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: hasChildContent('.$this->currentId.' returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------


        //--------- Checking dbsections hasChildSections -----------
        $result = $this->dbsections->hasChildSections($parentId);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: $this->dbsections->hasChildSections('.$parentId.') returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        //-------------------------------------------

        //--------- Checking dbsections getLevel -----------
        $result = $this->dbsections->getLevel($this->currentId);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        //-------------------------------------------

        //--------- Checking dbsections getRootNodeId -----------
        $result = $this->dbsections->getRootNodeId($this->currentId);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertEquals(0, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        //-------------------------------------------

        //--------- Checking dbsections hasNodes -----------
        $result = $this->dbsections->getAllSections();
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        //-------------------------------------------

        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->getSubSectionsInRoot($rootId,$order,$isPublished);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------

        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->getSubSectionsForLevel($rootId,$level,$order,$isPublished);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------


        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->getNumSubSections($sectionId);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------


        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->deleteSection($id);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------


        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->unarchiveSection($id);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------

        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->unarchiveSectionsection($id);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------

        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->getOrdering($parentid);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------

        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->getOrderingLink($id);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------

        //--------- Checking dbsections hasNodes -----------
        /*
        $result = $this->dbsections->getPageOrderType($orderType);
        //Checking Result (There should be sections, haven't deleted anything yet)
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->isSections() returned FALSE This is highly dependant on the fact that the database is only being accessed by this unit test runner');
        */
        //-------------------------------------------

    }















/*
 *
 * ===============================================================================================
 *
 */







/*
     * =============== Logical Add Data Methods =======dbtemplate_class_inc.php========
     */

    public function cmsadmin_dbtemplate_init() {
        return $this->dbtemplate->init();
    }

    public function cmsadmin_dbtemplate_saveXml() {
        return $this->dbtemplate->saveXml();
    }

    public function cmsadmin_dbtemplate_getChildTemplate($sectionid,$admin,$filter) {
        return $this->dbtemplate->getChildTemplate($sectionid,$admin,$filter);
    }

    public function cmsadmin_dbtemplate_add() {
        return $this->dbtemplate->add();
    }

    public function cmsadmin_dbtemplate_addNewPage($title,$sectionid,$published,$access,$introText,$fullText,$isFrontPage,$ccLicence) {
        return $this->dbtemplate->addNewPage($title,$sectionid,$published,$access,$introText,$fullText,$isFrontPage,$ccLicence);
    }

    public function cmsadmin_dbtemplate_getHrefTemplateRecords($sectionid) {
        return $this->dbtemplate->getHrefTemplateRecords($sectionid);
    }

    public function cmsadmin_dbtemplate_getTemplatePages($filter) {
        return $this->dbtemplate->getTemplatePages($filter);
    }

    public function cmsadmin_dbtemplate_getArchivePages($filter) {
        return $this->dbtemplate->getArchivePages($filter);
    }

    public function cmsadmin_dbtemplate_getTemplatePage($id) {
        return $this->dbtemplate->getTemplatePage($id);
    }

    public function cmsadmin_dbtemplate_getTemplatePageFiltered($id,$filter) {
        return $this->dbtemplate->getTemplatePageFiltered($id,$filter);
    }

    public function cmsadmin_dbtemplate_getPagesInSection($sectionId,$isPublished) {
        return $this->dbtemplate->getPagesInSection($sectionId,$isPublished);
    }

    public function cmsadmin_dbtemplate_getPagesInSectionJoinFront($sectionId) {
        return $this->dbtemplate->getPagesInSectionJoinFront($sectionId);
    }

    public function cmsadmin_dbtemplate_getTitles($title,$limit) {
        return $this->dbtemplate->getTitles($title,$limit);
    }

    public function cmsadmin_dbtemplate_getLatestTitles($n) {
        return $this->dbtemplate->getLatestTitles($n);
    }

    public function cmsadmin_dbtemplate_getNumberOfPagesInSection($sectionId) {
        return $this->dbtemplate->getNumberOfPagesInSection($sectionId);
    }

    public function cmsadmin_dbtemplate_getPageOrder($pageId) {
        return $this->dbtemplate->getPageOrder($pageId);
    }

    public function cmsadmin_dbtemplate_getOrdering($sectionId) {
        return $this->dbtemplate->getOrdering($sectionId);
    }

    public function cmsadmin_dbtemplate_getOrderingLink($sectionid,$id) {
        return $this->dbtemplate->getOrderingLink($sectionid,$id);
    }

    public function cmsadmin_dbtemplate_html2txt($document,$scrub) {
        return $this->dbtemplate->html2txt($document,$scrub);
    }

    public function cmsadmin_dbtemplate_luceneIndex($data) {
        return $this->dbtemplate->luceneIndex($data);
    }

    public function cmsadmin_dbtemplate_getParent($templateId) {
        return $this->dbtemplate->getParent($templateId);
    }

    public function cmsadmin_dbtemplate_getRow($pk_field,$pk_value) {
        return $this->dbtemplate->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbtemplate_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbtemplate->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbtemplate_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbtemplate->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbtemplate_join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom) {
        return $this->dbtemplate->join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom);
    }

    public function cmsadmin_dbtemplate_now() {
        return $this->dbtemplate->now();
    }

    public function cmsadmin_dbtemplate_getParam($name,$default) {
        return $this->dbtemplate->getParam($name,$default);
    }

    public function cmsadmin_dbtemplate_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbtemplate->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbtemplate_newObject($name,$moduleName) {
        return $this->dbtemplate->newObject($name,$moduleName);
    }

    public function cmsadmin_dbtemplate_getObject($name,$moduleName) {
        return $this->dbtemplate->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbtemplate_class_inc.php========
     */

    public function cmsadmin_dbtemplate_edit() {
        return $this->dbtemplate->edit();
    }

    public function cmsadmin_dbtemplate_updateTemplateBody($templateid,$body) {
        return $this->dbtemplate->updateTemplateBody($templateid,$body);
    }

    public function cmsadmin_dbtemplate_trashTemplate($id) {
        return $this->dbtemplate->trashTemplate($id);
    }

    public function cmsadmin_dbtemplate_reorderTemplate($id) {
        return $this->dbtemplate->reorderTemplate($id);
    }

    public function cmsadmin_dbtemplate_undelete($id) {
        return $this->dbtemplate->undelete($id);
    }

    public function cmsadmin_dbtemplate_togglePublish($id) {
        return $this->dbtemplate->togglePublish($id);
    }

    public function cmsadmin_dbtemplate_publish($id,$task) {
        return $this->dbtemplate->publish($id,$task);
    }

    public function cmsadmin_dbtemplate_resetSection($sectionId) {
        return $this->dbtemplate->resetSection($sectionId);
    }

    public function cmsadmin_dbtemplate_unarchiveSection($sectionId) {
        return $this->dbtemplate->unarchiveSection($sectionId);
    }

    public function cmsadmin_dbtemplate_changeOrder($sectionid,$id,$ordering) {
        return $this->dbtemplate->changeOrder($sectionid,$id,$ordering);
    }

    public function cmsadmin_dbtemplate_getAll($filter) {
        return $this->dbtemplate->getAll($filter);
    }

    public function cmsadmin_dbtemplate_getArray($stmt) {
        return $this->dbtemplate->getArray($stmt);
    }

    public function cmsadmin_dbtemplate_insert($fields,$tablename) {
        return $this->dbtemplate->insert($fields,$tablename);
    }



    /*
     * =============== Logical Delete Data Methods =======dbtemplate_class_inc.php========
     */

    public function cmsadmin_dbtemplate_deleteTemplate($id) {
        return $this->dbtemplate->deleteTemplate($id);
    }









    /*
     * Checking Data Management Methods : dbtemplate_class_inc.php
     */

    public function test_dbtemplate_data_management() {

//Retrieve Content Records
$result = $this->dbcontent->getTemplatePages('');
$this->recordCountBefore = count($result);

//Add Template
//TODO: Should these be declared as individual members / fixtures?

$title = 'CMS Unit Test ' . date("YMD");
$published = 0;
$override_date = null;
$start_publish = null;
$end_publish = null;
$creatorid = 'init_1';
$show_title = 'y';
$show_author = 'y';
$show_date = 'y';
$show_pdf = 'y';
$show_email = 'y';
$show_print = 'y';
$access = null;
$created_by = 'init_1';
$introText = 'Unit Test Intro Text ';
$fullText = 'Unit Test Full Text';
$metakey = 'Unit Test Meta Key';
$metadesc = 'Unit Test Meta Description';
$ccLicence = null;
$sectionId = 'init_1';

$result = $this->dbtemplate->addTemplate($title,
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

        $this->currentId = $result;

        //TODO: check if the are params before displaying (...) for warnings

        $this->assertNotEquals('', $result, 'Warning: dbcontent->addContent(...) Returned \'\'');
        $this->assertNotEquals(null, $result, 'Warning: dbcontent->addContent(...) Returned NULL');

        //Checking that the fields where added correctly
        $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
        $record = $record[0];

        //Checking ID
        $this->assertNotEquals(FALSE, ($this->cmsadmin_tbl_cms_content_id($record) == $this->currentId));
        $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_id($record));

        //Checking Title
        $this->assertEquals($title, $this->cmsadmin_tbl_cms_content_title($record));

        //Checking Published
        $this->assertEquals($published, $this->cmsadmin_tbl_cms_content_published($record));

        //Checking Override_date
        $this->assertNotEquals($override_date, $this->cmsadmin_tbl_cms_content_override_date($record));

        //Checking Start Publish
        $this->assertEquals($start_publish, $this->cmsadmin_tbl_cms_content_start_publish($record));

        //Checking End Publish
        $this->assertEquals($end_publish, $this->cmsadmin_tbl_cms_content_end_publish($record));

        //Checking creatorid
        $this->assertEquals($creatorid, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking Show Title
        $this->assertEquals($show_title, $this->cmsadmin_tbl_cms_content_show_title($record));

        //Checking Show Author
        $this->assertEquals($show_author, $this->cmsadmin_tbl_cms_content_show_author($record));

        //Checking Show Date
        $this->assertEquals($show_date, $this->cmsadmin_tbl_cms_content_show_date($record));

        //Checking Show Pdf
        $this->assertEquals($show_pdf, $this->cmsadmin_tbl_cms_content_show_pdf($record));

        //Checking Show Email
        $this->assertEquals($show_email, $this->cmsadmin_tbl_cms_content_show_email($record));

        //Checking Show Print
        $this->assertEquals($show_print, $this->cmsadmin_tbl_cms_content_show_print($record));

        //Checking Access
        $this->assertEquals($access, $this->cmsadmin_tbl_cms_content_access($record));

        //Checking Created By
        $this->assertEquals($created_by, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking IntroText
        $this->assertEquals($introText, $this->cmsadmin_tbl_cms_content_introtext($record));

        //Checking FullText
        $this->assertEquals($fullText, $this->cmsadmin_tbl_cms_content_body($record));

        //Checking Metakey
        $this->assertEquals($metakey, $this->cmsadmin_tbl_cms_content_metakey($record));

        //Checking Meta Description
        $this->assertEquals($metadesc, $this->cmsadmin_tbl_cms_content_metadesc($record));

        //Checking Licence
        $this->assertEquals($ccLicence, $this->cmsadmin_tbl_cms_content_post_lic($record));

        //Checking Meta Description
        $this->assertEquals($sectionId, $this->cmsadmin_tbl_cms_content_sectionid($record));



$id = $this->currentId;
$title = 'Unit Test Title Edited';
$sectionId = 'init_2';
$published = 1;
$access = 1;
$introText = 'Unit Test Intro Text Edited';
$fullText = 'Unit Test Full Text Edited';
$override_date = '2008-01-01 00:00:00';
$start_publish = '2008-01-01 00:00:00';
$start_publish = '2008-01-01 00:00:00';
$end_publish = '2008-01-01 00:00:00';
$override_date = '2008-01-01 00:00:00';
$metakey = 'Unit Test Meta Key Edited';
$metadesc = 'Unit Test Meta Description Edited';
$ccLicence = 'New Licence';
$show_title = 'n';
$show_author = 'y';
$show_date = 'n';
$show_pdf = 'y';
$show_email = 'n';
$show_print = 'y';

$result = $this->dbcontent->editContent($id ,
                        $title ,
                        $sectionId ,
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


        //Checking that content fields were properly edited

        $this->assertNotEquals('', $result, 'Warning: dbcontent->editContent(...) Returned \'\'');
        $this->assertNotEquals(null, $result, 'Warning: dbcontent->editContent(...) Returned NULL');
        $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->editContent(...) Returned FALSE');

        //Checking that the fields where edited correctly
        $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
        $record = $record[0];

        //Checking ID
        $this->assertNotEquals(FALSE, ($this->cmsadmin_tbl_cms_content_id($record) == $this->currentId));
        $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_id($record));

        //Checking Title
        $this->assertEquals($title, $this->cmsadmin_tbl_cms_content_title($record));

        //Checking Published
        $this->assertEquals($published, $this->cmsadmin_tbl_cms_content_published($record));

        //Checking Override_date
        $this->assertEquals($override_date, $this->cmsadmin_tbl_cms_content_override_date($record));

        //Checking Start Publish
        $this->assertEquals($start_publish, $this->cmsadmin_tbl_cms_content_start_publish($record));

        //Checking End Publish
        $this->assertEquals($end_publish, $this->cmsadmin_tbl_cms_content_end_publish($record));

        //Checking creatorid
        $this->assertEquals($creatorid, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking Show Title
        $this->assertEquals($show_title, $this->cmsadmin_tbl_cms_content_show_title($record));

        //Checking Show Author
        $this->assertEquals($show_author, $this->cmsadmin_tbl_cms_content_show_author($record));

        //Checking Show Date
        $this->assertEquals($show_date, $this->cmsadmin_tbl_cms_content_show_date($record));

        //Checking Show Pdf
        $this->assertEquals($show_pdf, $this->cmsadmin_tbl_cms_content_show_pdf($record));

        //Checking Show Email
        $this->assertEquals($show_email, $this->cmsadmin_tbl_cms_content_show_email($record));

        //Checking Show Print
        $this->assertEquals($show_print, $this->cmsadmin_tbl_cms_content_show_print($record));

        //Checking Access
        $this->assertEquals($access, $this->cmsadmin_tbl_cms_content_access($record));

        //Checking Created By
        $this->assertEquals($created_by, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking IntroText
        $this->assertEquals($introText, $this->cmsadmin_tbl_cms_content_introtext($record));

        //Checking FullText
        $this->assertEquals($fullText, $this->cmsadmin_tbl_cms_content_body($record));

        //Checking Metakey
        $this->assertEquals($metakey, $this->cmsadmin_tbl_cms_content_metakey($record));

        //Checking Meta Description
        $this->assertEquals($metadesc, $this->cmsadmin_tbl_cms_content_metadesc($record));

        //Checking Licence
        $this->assertEquals($ccLicence, $this->cmsadmin_tbl_cms_content_post_lic($record));

        //Checking Meta Description
        $this->assertEquals($sectionId, $this->cmsadmin_tbl_cms_content_sectionid($record));



    /*
     * =============== Logical Edit Data Methods =======dbcontent_class_inc.php========
     */
        //DEPRICATED
        //return $this->dbcontent->edit();

    //--------- Checking updateContentBody -----------
    $contentid = $this->currentId;
    $body = 'PHPUNIT Test Update Body';
    $result = $this->dbcontent->updateContentBody($contentid,$body);
    //Checking Result
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->trashContent($id) returned FALSE');

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Body
    $this->assertEquals($body, $this->cmsadmin_tbl_cms_content_body($record));
    //------------------------------------------------


    //--------- Checking trashContent -----------
    $id = $this->currentId;
    $result = $this->dbcontent->trashContent($id);
    //Checking Result
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->trashContent($id) returned FALSE');

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Trash
    $this->assertEquals(1, $this->cmsadmin_tbl_cms_content_trash($record));

    //Checking Ordering
    $this->assertEquals('', $this->cmsadmin_tbl_cms_content_ordering($record));

    //Checking End Publish
    $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_end_publish($record));
    //-------------------------------------------


    //--------- Checking reorderContent -----------
    /*
    $id = $this->currentId;
    $result = $this->dbcontent->reorderContent($id);
    //Checking Result
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->trashContent($id) returned FALSE');

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Trash
    $this->assertEquals(1, $this->cmsadmin_tbl_cms_content_trash($record));
    */
    //---------------------------------------------


    //--------- Checking Undelete -----------
    $result = $this->dbcontent->undelete($this->currentId);
    //Checking Result
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->undelete($id) Returned FALSE - Was the item successfully restored?');

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Trash
    $this->assertEquals(0, $this->cmsadmin_tbl_cms_content_trash($record));
    //---------------------------------------------


    //--------- Checking Toggle Publish -----------
    //Checking Undelete
    $this->assertNotEquals(FALSE, $result, 'Warning: dbcontent->togglePublish($id) Returned FALSE - Was the item successfully restored?');

    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Published
    $varPublished = $this->cmsadmin_tbl_cms_content_published($record);

    $result = $this->dbcontent->togglePublish($id);
    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Publish
    $this->assertNotEquals($varPublished, $this->cmsadmin_tbl_cms_content_trash($record));
    //---------------------------------------------


    //--------- Checking Publish : TASK publish -----------
    $id = $this->currentId;
    $task = 'publish';
    $result = $this->dbcontent->publish($id,$task);

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Publish
    $this->assertEquals(1, $this->cmsadmin_tbl_cms_content_published($record));
    //---------------------------------------------

    //--------- Checking Publish : TASK unpublish -----------
    $id = $this->currentId;
    $task = 'unpublish';
    $result = $this->dbcontent->publish($id,$task);

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Publish
    $this->assertEquals(0, $this->cmsadmin_tbl_cms_content_published($record));
    //---------------------------------------------

    /*
    public function cmsadmin_dbcontent_resetSection($sectionId) {
        return $this->dbcontent->resetSection($sectionId);
    }

    public function cmsadmin_dbcontent_unarchiveSection($sectionId) {
        return $this->dbcontent->unarchiveSection($sectionId);
    }
    */

    //--------- Checking Publish : TASK unpublish -----------
    //TODO: Complete changeOrder
    //      Add Test Data (One section with a couple of content items)
    /*
    $id = $this->currentId;
    $sectionid = 'init_1';
    $task = 'unpublish';
    $result = $this->dbcontent->changeOrder($sectionid,$id,$ordering);
    */

    /*
     * =============== Logical Delete Data Methods =======dbcontent_class_inc.php========
     */

    $id = $this->currentId;
    $result = $this->dbcontent->deleteContent($id);

    //Checking that the fields where edited correctly
    $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
    $record = $record[0];

    //Checking Publish
    $this->assertEquals(0, count($record));

    }



    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_layouts
     * Field: id
     */

    public function cmsadmin_tbl_cms_layouts_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_layouts
     * Field: name
     */

    public function cmsadmin_tbl_cms_layouts_name($record){
        if (isset($record['name'])){
            return $record['name'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_layouts
     * Field: imagename
     */

    public function cmsadmin_tbl_cms_layouts_imagename($record){
        if (isset($record['imagename'])){
            return $record['imagename'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_layouts
     * Field: description
     */

    public function cmsadmin_tbl_cms_layouts_description($record){
        if (isset($record['description'])){
            return $record['description'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_layouts
     * Field: puid
     */

    public function cmsadmin_tbl_cms_layouts_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: id
     */

    public function cmsadmin_tbl_cms_sections_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: rootid
     */

    public function cmsadmin_tbl_cms_sections_rootid($record){
        if (isset($record['rootid'])){
            return $record['rootid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: parentid
     */

    public function cmsadmin_tbl_cms_sections_parentid($record){
        if (isset($record['parentid'])){
            return $record['parentid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: title
     */

    public function cmsadmin_tbl_cms_sections_title($record){
        if (isset($record['title'])){
            return $record['title'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: menutext
     */

    public function cmsadmin_tbl_cms_sections_menutext($record){
        if (isset($record['menutext'])){
            return $record['menutext'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: description
     */

    public function cmsadmin_tbl_cms_sections_description($record){
        if (isset($record['description'])){
            return $record['description'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: published
     */

    public function cmsadmin_tbl_cms_sections_published($record){
        if (isset($record['published'])){
            return $record['published'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: showdate
     */

    public function cmsadmin_tbl_cms_sections_showdate($record){
        if (isset($record['showdate'])){
            return $record['showdate'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: showintroduction
     */

    public function cmsadmin_tbl_cms_sections_showintroduction($record){
        if (isset($record['showintroduction'])){
            return $record['showintroduction'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: numpagedisplay
     */

    public function cmsadmin_tbl_cms_sections_numpagedisplay($record){
        if (isset($record['numpagedisplay'])){
            return $record['numpagedisplay'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: checked_out
     */

    public function cmsadmin_tbl_cms_sections_checked_out($record){
        if (isset($record['checked_out'])){
            return $record['checked_out'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: checked_out_time
     */

    public function cmsadmin_tbl_cms_sections_checked_out_time($record){
        if (isset($record['checked_out_time'])){
            return $record['checked_out_time'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: ordering
     */

    public function cmsadmin_tbl_cms_sections_ordering($record){
        if (isset($record['ordering'])){
            return $record['ordering'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: ordertype
     */

    public function cmsadmin_tbl_cms_sections_ordertype($record){
        if (isset($record['ordertype'])){
            return $record['ordertype'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: access
     */

    public function cmsadmin_tbl_cms_sections_access($record){
        if (isset($record['access'])){
            return $record['access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: trash
     */

    public function cmsadmin_tbl_cms_sections_trash($record){
        if (isset($record['trash'])){
            return $record['trash'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: nodelevel
     */

    public function cmsadmin_tbl_cms_sections_nodelevel($record){
        if (isset($record['nodelevel'])){
            return $record['nodelevel'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: params
     */

    public function cmsadmin_tbl_cms_sections_params($record){
        if (isset($record['params'])){
            return $record['params'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: layout
     */

    public function cmsadmin_tbl_cms_sections_layout($record){
        if (isset($record['layout'])){
            return $record['layout'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: link
     */

    public function cmsadmin_tbl_cms_sections_link($record){
        if (isset($record['link'])){
            return $record['link'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: userids
     */

    public function cmsadmin_tbl_cms_sections_userids($record){
        if (isset($record['userids'])){
            return $record['userids'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: groupid
     */

    public function cmsadmin_tbl_cms_sections_groupid($record){
        if (isset($record['groupid'])){
            return $record['groupid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: datecreated
     */

    public function cmsadmin_tbl_cms_sections_datecreated($record){
        if (isset($record['datecreated'])){
            return $record['datecreated'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: lastupdatedby
     */

    public function cmsadmin_tbl_cms_sections_lastupdatedby($record){
        if (isset($record['lastupdatedby'])){
            return $record['lastupdatedby'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: updated
     */

    public function cmsadmin_tbl_cms_sections_updated($record){
        if (isset($record['updated'])){
            return $record['updated'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: startdate
     */

    public function cmsadmin_tbl_cms_sections_startdate($record){
        if (isset($record['startdate'])){
            return $record['startdate'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: finishdate
     */

    public function cmsadmin_tbl_cms_sections_finishdate($record){
        if (isset($record['finishdate'])){
            return $record['finishdate'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: contextcode
     */

    public function cmsadmin_tbl_cms_sections_contextcode($record){
        if (isset($record['contextcode'])){
            return $record['contextcode'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: public_access
     */

    public function cmsadmin_tbl_cms_sections_public_access($record){
        if (isset($record['public_access'])){
            return $record['public_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: puid
     */

    public function cmsadmin_tbl_cms_sections_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: show_title
     */

    public function cmsadmin_tbl_cms_sections_show_title($record){
        if (isset($record['show_title'])){
            return $record['show_title'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: show_user
     */

    public function cmsadmin_tbl_cms_sections_show_user($record){
        if (isset($record['show_user'])){
            return $record['show_user'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: show_date
     */

    public function cmsadmin_tbl_cms_sections_show_date($record){
        if (isset($record['show_date'])){
            return $record['show_date'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: show_introduction
     */

    public function cmsadmin_tbl_cms_sections_show_introduction($record){
        if (isset($record['show_introduction'])){
            return $record['show_introduction'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sections
     * Field: userid
     */

    public function cmsadmin_tbl_cms_sections_userid($record){
        if (isset($record['userid'])){
            return $record['userid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: id
     */

    public function cmsadmin_tbl_cms_content_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: title
     */

    public function cmsadmin_tbl_cms_content_title($record){
        if (isset($record['title'])){
            return $record['title'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: introtext
     */

    public function cmsadmin_tbl_cms_content_introtext($record){
        if (isset($record['introtext'])){
            return $record['introtext'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: body
     */

    public function cmsadmin_tbl_cms_content_body($record){
        if (isset($record['body'])){
            return $record['body'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: published
     */

    public function cmsadmin_tbl_cms_content_published($record){
        if (isset($record['published'])){
            return $record['published'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: hide_title
     */

    public function cmsadmin_tbl_cms_content_hide_title($record){
        if (isset($record['hide_title'])){
            return $record['hide_title'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: hide_user
     */

    public function cmsadmin_tbl_cms_content_hide_user($record){
        if (isset($record['hide_user'])){
            return $record['hide_user'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: hide_date
     */

    public function cmsadmin_tbl_cms_content_hide_date($record){
        if (isset($record['hide_date'])){
            return $record['hide_date'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: hide_pdf
     */

    public function cmsadmin_tbl_cms_content_hide_pdf($record){
        if (isset($record['hide_pdf'])){
            return $record['hide_pdf'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: hide_mail
     */

    public function cmsadmin_tbl_cms_content_hide_mail($record){
        if (isset($record['hide_mail'])){
            return $record['hide_mail'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: hide_print
     */

    public function cmsadmin_tbl_cms_content_hide_print($record){
        if (isset($record['hide_print'])){
            return $record['hide_print'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: trash
     */

    public function cmsadmin_tbl_cms_content_trash($record){
        if (isset($record['trash'])){
            return $record['trash'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: sectionid
     */

    public function cmsadmin_tbl_cms_content_sectionid($record){
        if (isset($record['sectionid'])){
            return $record['sectionid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: post_lic
     */

    public function cmsadmin_tbl_cms_content_post_lic($record){
        if (isset($record['post_lic'])){
            return $record['post_lic'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: mask
     */

    public function cmsadmin_tbl_cms_content_mask($record){
        if (isset($record['mask'])){
            return $record['mask'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: created
     */

    public function cmsadmin_tbl_cms_content_created($record){
        if (isset($record['created'])){
            return $record['created'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: created_by
     */

    public function cmsadmin_tbl_cms_content_created_by($record){
        if (isset($record['created_by'])){
            return $record['created_by'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: groupid
     */

    public function cmsadmin_tbl_cms_content_groupid($record){
        if (isset($record['groupid'])){
            return $record['groupid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: created_by_alias
     */

    public function cmsadmin_tbl_cms_content_created_by_alias($record){
        if (isset($record['created_by_alias'])){
            return $record['created_by_alias'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: modified
     */

    public function cmsadmin_tbl_cms_content_modified($record){
        if (isset($record['modified'])){
            return $record['modified'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: modified_by
     */

    public function cmsadmin_tbl_cms_content_modified_by($record){
        if (isset($record['modified_by'])){
            return $record['modified_by'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: checked_out
     */

    public function cmsadmin_tbl_cms_content_checked_out($record){
        if (isset($record['checked_out'])){
            return $record['checked_out'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: checked_out_time
     */

    public function cmsadmin_tbl_cms_content_checked_out_time($record){
        if (isset($record['checked_out_time'])){
            return $record['checked_out_time'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: publish_up
     */

    public function cmsadmin_tbl_cms_content_publish_up($record){
        if (isset($record['publish_up'])){
            return $record['publish_up'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: publish_down
     */

    public function cmsadmin_tbl_cms_content_publish_down($record){
        if (isset($record['publish_down'])){
            return $record['publish_down'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: images
     */

    public function cmsadmin_tbl_cms_content_images($record){
        if (isset($record['images'])){
            return $record['images'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: urls
     */

    public function cmsadmin_tbl_cms_content_urls($record){
        if (isset($record['urls'])){
            return $record['urls'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: attribs
     */

    public function cmsadmin_tbl_cms_content_attribs($record){
        if (isset($record['attribs'])){
            return $record['attribs'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: version
     */

    public function cmsadmin_tbl_cms_content_version($record){
        if (isset($record['version'])){
            return $record['version'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: parentid
     */

    public function cmsadmin_tbl_cms_content_parentid($record){
        if (isset($record['parentid'])){
            return $record['parentid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: ordering
     */

    public function cmsadmin_tbl_cms_content_ordering($record){
        if (isset($record['ordering'])){
            return $record['ordering'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: metakey
     */

    public function cmsadmin_tbl_cms_content_metakey($record){
        if (isset($record['metakey'])){
            return $record['metakey'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: metadesc
     */

    public function cmsadmin_tbl_cms_content_metadesc($record){
        if (isset($record['metadesc'])){
            return $record['metadesc'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: access
     */

    public function cmsadmin_tbl_cms_content_access($record){
        if (isset($record['access'])){
            return $record['access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: hits
     */

    public function cmsadmin_tbl_cms_content_hits($record){
        if (isset($record['hits'])){
            return $record['hits'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: start_publish
     */

    public function cmsadmin_tbl_cms_content_start_publish($record){
        if (isset($record['start_publish'])){
            return $record['start_publish'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: end_publish
     */

    public function cmsadmin_tbl_cms_content_end_publish($record){
        if (isset($record['end_publish'])){
            return $record['end_publish'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: public_access
     */

    public function cmsadmin_tbl_cms_content_public_access($record){
        if (isset($record['public_access'])){
            return $record['public_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: puid
     */

    public function cmsadmin_tbl_cms_content_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: show_title
     */

    public function cmsadmin_tbl_cms_content_show_title($record){
        if (isset($record['show_title'])){
            return $record['show_title'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: show_author
     */

    public function cmsadmin_tbl_cms_content_show_author($record){
        if (isset($record['show_author'])){
            return $record['show_author'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: show_date
     */

    public function cmsadmin_tbl_cms_content_show_date($record){
        if (isset($record['show_date'])){
            return $record['show_date'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: show_pdf
     */

    public function cmsadmin_tbl_cms_content_show_pdf($record){
        if (isset($record['show_pdf'])){
            return $record['show_pdf'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: show_email
     */

    public function cmsadmin_tbl_cms_content_show_email($record){
        if (isset($record['show_email'])){
            return $record['show_email'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: show_print
     */

    public function cmsadmin_tbl_cms_content_show_print($record){
        if (isset($record['show_print'])){
            return $record['show_print'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content
     * Field: override_date
     */

    public function cmsadmin_tbl_cms_content_override_date($record){
        if (isset($record['override_date'])){
            return $record['override_date'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_frontpage
     * Field: id
     */

    public function cmsadmin_tbl_cms_content_frontpage_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_frontpage
     * Field: content_id
     */

    public function cmsadmin_tbl_cms_content_frontpage_content_id($record){
        if (isset($record['content_id'])){
            return $record['content_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_frontpage
     * Field: show_content
     */

    public function cmsadmin_tbl_cms_content_frontpage_show_content($record){
        if (isset($record['show_content'])){
            return $record['show_content'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_frontpage
     * Field: ordering
     */

    public function cmsadmin_tbl_cms_content_frontpage_ordering($record){
        if (isset($record['ordering'])){
            return $record['ordering'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_frontpage
     * Field: public_access
     */

    public function cmsadmin_tbl_cms_content_frontpage_public_access($record){
        if (isset($record['public_access'])){
            return $record['public_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_frontpage
     * Field: puid
     */

    public function cmsadmin_tbl_cms_content_frontpage_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_blocks
     * Field: id
     */

    public function cmsadmin_tbl_cms_blocks_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_blocks
     * Field: pageid
     */

    public function cmsadmin_tbl_cms_blocks_pageid($record){
        if (isset($record['pageid'])){
            return $record['pageid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_blocks
     * Field: blockid
     */

    public function cmsadmin_tbl_cms_blocks_blockid($record){
        if (isset($record['blockid'])){
            return $record['blockid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_blocks
     * Field: sectionid
     */

    public function cmsadmin_tbl_cms_blocks_sectionid($record){
        if (isset($record['sectionid'])){
            return $record['sectionid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_blocks
     * Field: frontpage_block
     */

    public function cmsadmin_tbl_cms_blocks_frontpage_block($record){
        if (isset($record['frontpage_block'])){
            return $record['frontpage_block'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_blocks
     * Field: leftside_blocks
     */

    public function cmsadmin_tbl_cms_blocks_leftside_blocks($record){
        if (isset($record['leftside_blocks'])){
            return $record['leftside_blocks'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_blocks
     * Field: ordering
     */

    public function cmsadmin_tbl_cms_blocks_ordering($record){
        if (isset($record['ordering'])){
            return $record['ordering'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_blocks
     * Field: puid
     */

    public function cmsadmin_tbl_cms_blocks_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sectiongroup
     * Field: id
     */

    public function cmsadmin_tbl_cms_sectiongroup_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sectiongroup
     * Field: section_id
     */

    public function cmsadmin_tbl_cms_sectiongroup_section_id($record){
        if (isset($record['section_id'])){
            return $record['section_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sectiongroup
     * Field: group_id
     */

    public function cmsadmin_tbl_cms_sectiongroup_group_id($record){
        if (isset($record['group_id'])){
            return $record['group_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_sectiongroup
     * Field: puid
     */

    public function cmsadmin_tbl_cms_sectiongroup_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: id
     */

    public function cmsadmin_tbl_cms_treenodes_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: node_type
     */

    public function cmsadmin_tbl_cms_treenodes_node_type($record){
        if (isset($record['node_type'])){
            return $record['node_type'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: title
     */

    public function cmsadmin_tbl_cms_treenodes_title($record){
        if (isset($record['title'])){
            return $record['title'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: link_reference
     */

    public function cmsadmin_tbl_cms_treenodes_link_reference($record){
        if (isset($record['link_reference'])){
            return $record['link_reference'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: banner
     */

    public function cmsadmin_tbl_cms_treenodes_banner($record){
        if (isset($record['banner'])){
            return $record['banner'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: parent_id
     */

    public function cmsadmin_tbl_cms_treenodes_parent_id($record){
        if (isset($record['parent_id'])){
            return $record['parent_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: layout
     */

    public function cmsadmin_tbl_cms_treenodes_layout($record){
        if (isset($record['layout'])){
            return $record['layout'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: css
     */

    public function cmsadmin_tbl_cms_treenodes_css($record){
        if (isset($record['css'])){
            return $record['css'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: ordering
     */

    public function cmsadmin_tbl_cms_treenodes_ordering($record){
        if (isset($record['ordering'])){
            return $record['ordering'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: published
     */

    public function cmsadmin_tbl_cms_treenodes_published($record){
        if (isset($record['published'])){
            return $record['published'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: publisher_id
     */

    public function cmsadmin_tbl_cms_treenodes_publisher_id($record){
        if (isset($record['publisher_id'])){
            return $record['publisher_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: artifact_id
     */

    public function cmsadmin_tbl_cms_treenodes_artifact_id($record){
        if (isset($record['artifact_id'])){
            return $record['artifact_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_treenodes
     * Field: puid
     */

    public function cmsadmin_tbl_cms_treenodes_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_rss
     * Field: id
     */

    public function cmsadmin_tbl_cms_rss_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_rss
     * Field: userid
     */

    public function cmsadmin_tbl_cms_rss_userid($record){
        if (isset($record['userid'])){
            return $record['userid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_rss
     * Field: url
     */

    public function cmsadmin_tbl_cms_rss_url($record){
        if (isset($record['url'])){
            return $record['url'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_rss
     * Field: name
     */

    public function cmsadmin_tbl_cms_rss_name($record){
        if (isset($record['name'])){
            return $record['name'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_rss
     * Field: description
     */

    public function cmsadmin_tbl_cms_rss_description($record){
        if (isset($record['description'])){
            return $record['description'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_rss
     * Field: rsscache
     */

    public function cmsadmin_tbl_cms_rss_rsscache($record){
        if (isset($record['rsscache'])){
            return $record['rsscache'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_rss
     * Field: rsstime
     */

    public function cmsadmin_tbl_cms_rss_rsstime($record){
        if (isset($record['rsstime'])){
            return $record['rsstime'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_rss
     * Field: puid
     */

    public function cmsadmin_tbl_cms_rss_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_menustyles
     * Field: id
     */

    public function cmsadmin_tbl_cms_menustyles_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_menustyles
     * Field: menu_style
     */

    public function cmsadmin_tbl_cms_menustyles_menu_style($record){
        if (isset($record['menu_style'])){
            return $record['menu_style'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_menustyles
     * Field: root_nodes
     */

    public function cmsadmin_tbl_cms_menustyles_root_nodes($record){
        if (isset($record['root_nodes'])){
            return $record['root_nodes'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_menustyles
     * Field: is_active
     */

    public function cmsadmin_tbl_cms_menustyles_is_active($record){
        if (isset($record['is_active'])){
            return $record['is_active'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_menustyles
     * Field: updated
     */

    public function cmsadmin_tbl_cms_menustyles_updated($record){
        if (isset($record['updated'])){
            return $record['updated'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_menustyles
     * Field: editable
     */

    public function cmsadmin_tbl_cms_menustyles_editable($record){
        if (isset($record['editable'])){
            return $record['editable'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_menustyles
     * Field: puid
     */

    public function cmsadmin_tbl_cms_menustyles_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: id
     */

    public function cmsadmin_tbl_cms_htmlblock_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: heading
     */

    public function cmsadmin_tbl_cms_htmlblock_heading($record){
        if (isset($record['heading'])){
            return $record['heading'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: content
     */

    public function cmsadmin_tbl_cms_htmlblock_content($record){
        if (isset($record['content'])){
            return $record['content'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: context_code
     */

    public function cmsadmin_tbl_cms_htmlblock_context_code($record){
        if (isset($record['context_code'])){
            return $record['context_code'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: creator_id
     */

    public function cmsadmin_tbl_cms_htmlblock_creator_id($record){
        if (isset($record['creator_id'])){
            return $record['creator_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: modifier_id
     */

    public function cmsadmin_tbl_cms_htmlblock_modifier_id($record){
        if (isset($record['modifier_id'])){
            return $record['modifier_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: date_created
     */

    public function cmsadmin_tbl_cms_htmlblock_date_created($record){
        if (isset($record['date_created'])){
            return $record['date_created'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: updated
     */

    public function cmsadmin_tbl_cms_htmlblock_updated($record){
        if (isset($record['updated'])){
            return $record['updated'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_htmlblock
     * Field: puid
     */

    public function cmsadmin_tbl_cms_htmlblock_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_user
     * Field: id
     */

    public function cmsadmin_tbl_cms_section_user_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_user
     * Field: section_id
     */

    public function cmsadmin_tbl_cms_section_user_section_id($record){
        if (isset($record['section_id'])){
            return $record['section_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_user
     * Field: user_id
     */

    public function cmsadmin_tbl_cms_section_user_user_id($record){
        if (isset($record['user_id'])){
            return $record['user_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_user
     * Field: read_access
     */

    public function cmsadmin_tbl_cms_section_user_read_access($record){
        if (isset($record['read_access'])){
            return $record['read_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_user
     * Field: write_access
     */

    public function cmsadmin_tbl_cms_section_user_write_access($record){
        if (isset($record['write_access'])){
            return $record['write_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_user
     * Field: puid
     */

    public function cmsadmin_tbl_cms_section_user_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_group
     * Field: id
     */

    public function cmsadmin_tbl_cms_section_group_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_group
     * Field: section_id
     */

    public function cmsadmin_tbl_cms_section_group_section_id($record){
        if (isset($record['section_id'])){
            return $record['section_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_group
     * Field: group_id
     */

    public function cmsadmin_tbl_cms_section_group_group_id($record){
        if (isset($record['group_id'])){
            return $record['group_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_group
     * Field: read_access
     */

    public function cmsadmin_tbl_cms_section_group_read_access($record){
        if (isset($record['read_access'])){
            return $record['read_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_group
     * Field: write_access
     */

    public function cmsadmin_tbl_cms_section_group_write_access($record){
        if (isset($record['write_access'])){
            return $record['write_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_section_group
     * Field: puid
     */

    public function cmsadmin_tbl_cms_section_group_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_user
     * Field: id
     */

    public function cmsadmin_tbl_cms_content_user_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_user
     * Field: content_id
     */

    public function cmsadmin_tbl_cms_content_user_content_id($record){
        if (isset($record['content_id'])){
            return $record['content_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_user
     * Field: user_id
     */

    public function cmsadmin_tbl_cms_content_user_user_id($record){
        if (isset($record['user_id'])){
            return $record['user_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_user
     * Field: read_access
     */

    public function cmsadmin_tbl_cms_content_user_read_access($record){
        if (isset($record['read_access'])){
            return $record['read_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_user
     * Field: write_access
     */

    public function cmsadmin_tbl_cms_content_user_write_access($record){
        if (isset($record['write_access'])){
            return $record['write_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_user
     * Field: puid
     */

    public function cmsadmin_tbl_cms_content_user_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_group
     * Field: id
     */

    public function cmsadmin_tbl_cms_content_group_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_group
     * Field: content_id
     */

    public function cmsadmin_tbl_cms_content_group_content_id($record){
        if (isset($record['content_id'])){
            return $record['content_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_group
     * Field: group_id
     */

    public function cmsadmin_tbl_cms_content_group_group_id($record){
        if (isset($record['group_id'])){
            return $record['group_id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_group
     * Field: read_access
     */

    public function cmsadmin_tbl_cms_content_group_read_access($record){
        if (isset($record['read_access'])){
            return $record['read_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_group
     * Field: write_access
     */

    public function cmsadmin_tbl_cms_content_group_write_access($record){
        if (isset($record['write_access'])){
            return $record['write_access'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_content_group
     * Field: puid
     */

    public function cmsadmin_tbl_cms_content_group_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_page_menu
     * Field: id
     */

    public function cmsadmin_tbl_cms_page_menu_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_page_menu
     * Field: name
     */

    public function cmsadmin_tbl_cms_page_menu_name($record){
        if (isset($record['name'])){
            return $record['name'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_page_menu
     * Field: body
     */

    public function cmsadmin_tbl_cms_page_menu_body($record){
        if (isset($record['body'])){
            return $record['body'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_page_menu
     * Field: menukey
     */

    public function cmsadmin_tbl_cms_page_menu_menukey($record){
        if (isset($record['menukey'])){
            return $record['menukey'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_page_menu
     * Field: userid
     */

    public function cmsadmin_tbl_cms_page_menu_userid($record){
        if (isset($record['userid'])){
            return $record['userid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_page_menu
     * Field: puid
     */

    public function cmsadmin_tbl_cms_page_menu_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: id
     */

    public function cmsadmin_tbl_cms_templates_id($record){
        if (isset($record['id'])){
            return $record['id'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: title
     */

    public function cmsadmin_tbl_cms_templates_title($record){
        if (isset($record['title'])){
            return $record['title'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: image
     */

    public function cmsadmin_tbl_cms_templates_image($record){
        if (isset($record['image'])){
            return $record['image'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: description
     */

    public function cmsadmin_tbl_cms_templates_description($record){
        if (isset($record['description'])){
            return $record['description'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: body
     */

    public function cmsadmin_tbl_cms_templates_body($record){
        if (isset($record['body'])){
            return $record['body'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: published
     */

    public function cmsadmin_tbl_cms_templates_published($record){
        if (isset($record['published'])){
            return $record['published'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: trash
     */

    public function cmsadmin_tbl_cms_templates_trash($record){
        if (isset($record['trash'])){
            return $record['trash'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: created
     */

    public function cmsadmin_tbl_cms_templates_created($record){
        if (isset($record['created'])){
            return $record['created'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: created_by
     */

    public function cmsadmin_tbl_cms_templates_created_by($record){
        if (isset($record['created_by'])){
            return $record['created_by'];
        } else {
            return FALSE;
        }
    }


    /*
     * Methods for validating the Table Fields
     * Module: cmsadmin
     * Table: tbl_cms_templates
     * Field: puid
     */

    public function cmsadmin_tbl_cms_templates_puid($record){
        if (isset($record['puid'])){
            return $record['puid'];
        } else {
            return FALSE;
        }
    }


    /*
     * =============== Logical Add Data Methods =======treenodes_class_inc.php========
     */

    public function cmsadmin_treenodes_init() {
        return $this->treenodes->init();
    }

    public function cmsadmin_treenodes_add($title,$nodeType,$linkReference,$banner,$parentId,$layout,$css,$published,$publisherId,$ordering,$artifact) {
        return $this->treenodes->add($title,$nodeType,$linkReference,$banner,$parentId,$layout,$css,$published,$publisherId,$ordering,$artifact);
    }

    public function cmsadmin_treenodes_getChildNodes($parentId,$onlyPublished,$noPermissions) {
        return $this->treenodes->getChildNodes($parentId,$onlyPublished,$noPermissions);
    }

    public function cmsadmin_treenodes_getChildNodeCount($parentId,$onlyPublished,$noPermissions) {
        return $this->treenodes->getChildNodeCount($parentId,$onlyPublished,$noPermissions);
    }

    public function cmsadmin_treenodes_getNode($id,$noPermissions) {
        return $this->treenodes->getNode($id,$noPermissions);
    }

    public function cmsadmin_treenodes_moveNodeUp($menuNode) {
        return $this->treenodes->moveNodeUp($menuNode);
    }

    public function cmsadmin_treenodes_moveNodeDown($menuNode) {
        return $this->treenodes->moveNodeDown($menuNode);
    }

    public function cmsadmin_treenodes_getNextOrderNum($currentNode,$orderIncrement) {
        return $this->treenodes->getNextOrderNum($currentNode,$orderIncrement);
    }

    public function cmsadmin_treenodes_getPrevOrderNum($currentNode) {
        return $this->treenodes->getPrevOrderNum($currentNode);
    }

    public function cmsadmin_treenodes_getArtifact($id) {
        return $this->treenodes->getArtifact($id);
    }

    public function cmsadmin_treenodes_getRootNodes() {
        return $this->treenodes->getRootNodes();
    }

    public function cmsadmin_treenodes_getNewOrderNum($parentNode,$orderIncrement) {
        return $this->treenodes->getNewOrderNum($parentNode,$orderIncrement);
    }

    public function cmsadmin_treenodes_getRow($pk_field,$pk_value) {
        return $this->treenodes->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_treenodes_insert($fields,$tablename) {
        return $this->treenodes->insert($fields,$tablename);
    }

    public function cmsadmin_treenodes_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->treenodes->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_treenodes_delete($pkfield,$pkvalue,$tablename) {
        return $this->treenodes->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_treenodes_query($stmt) {
        return $this->treenodes->query($stmt);
    }

    public function cmsadmin_treenodes_join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom) {
        return $this->treenodes->join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom);
    }

    public function cmsadmin_treenodes_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->treenodes->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_treenodes_newObject($name,$moduleName) {
        return $this->treenodes->newObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======treenodes_class_inc.php========
     */

    public function cmsadmin_treenodes_edit($id,$title,$nodeType,$linkReference,$banner,$parentId,$layout,$css,$published,$publisherId,$ordering,$artifact) {
        return $this->treenodes->edit($id,$title,$nodeType,$linkReference,$banner,$parentId,$layout,$css,$published,$publisherId,$ordering,$artifact);
    }

    public function cmsadmin_treenodes_updateOrder($id,$ordering) {
        return $this->treenodes->updateOrder($id,$ordering);
    }



    /*
     * =============== Logical Delete Data Methods =======treenodes_class_inc.php========
     */

    public function cmsadmin_treenodes_deleteWithChildren($id) {
        return $this->treenodes->deleteWithChildren($id);
    }

    public function cmsadmin_treenodes_getAll($filter) {
        return $this->treenodes->getAll($filter);
    }



    /*
     * =============== Logical Add Data Methods =======dbgroups_class_inc.php========
     */

    public function cmsadmin_dbgroups_init() {
        return $this->dbgroups->init();
    }

    public function cmsadmin_dbgroups_getChildNodes($parentId) {
        return $this->dbgroups->getChildNodes($parentId);
    }

    public function cmsadmin_dbgroups_getChildNodeCount($parentId) {
        return $this->dbgroups->getChildNodeCount($parentId);
    }

    public function cmsadmin_dbgroups_getNode($id,$noPermissions) {
        return $this->dbgroups->getNode($id,$noPermissions);
    }

    public function cmsadmin_dbgroups_query($stmt) {
        return $this->dbgroups->query($stmt);
    }

    public function cmsadmin_dbgroups_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbgroups->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbgroups_newObject($name,$moduleName) {
        return $this->dbgroups->newObject($name,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======dbsectiongroup_class_inc.php========
     */

    public function cmsadmin_dbsectiongroup_init() {
        return $this->dbsectiongroup->init();
    }

    public function cmsadmin_dbsectiongroup_getChildNodes($parentId,$admin) {
        return $this->dbsectiongroup->getChildNodes($parentId,$admin);
    }

    public function cmsadmin_dbsectiongroup_getChildNodeCount($parentId,$noPermissions) {
        return $this->dbsectiongroup->getChildNodeCount($parentId,$noPermissions);
    }

    public function cmsadmin_dbsectiongroup_getNode($id,$admin) {
        return $this->dbsectiongroup->getNode($id,$admin);
    }

    public function cmsadmin_dbsectiongroup_getGroupBySection($sectionId) {
        return $this->dbsectiongroup->getGroupBySection($sectionId);
    }

    public function cmsadmin_dbsectiongroup_getSectionByGroup($groupId) {
        return $this->dbsectiongroup->getSectionByGroup($groupId);
    }

    public function cmsadmin_dbsectiongroup_add($sectionId,$groupId) {
        return $this->dbsectiongroup->add($sectionId,$groupId);
    }

    public function cmsadmin_dbsectiongroup_getSectionGroupId($sectionId) {
        return $this->dbsectiongroup->getSectionGroupId($sectionId);
    }

    public function cmsadmin_dbsectiongroup_getGroupNameBySection($sectionId) {
        return $this->dbsectiongroup->getGroupNameBySection($sectionId);
    }

    public function cmsadmin_dbsectiongroup_getArray($stmt) {
        return $this->dbsectiongroup->getArray($stmt);
    }

    public function cmsadmin_dbsectiongroup_insert($fields,$tablename) {
        return $this->dbsectiongroup->insert($fields,$tablename);
    }

    public function cmsadmin_dbsectiongroup_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbsectiongroup->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbsectiongroup_query($stmt) {
        return $this->dbsectiongroup->query($stmt);
    }

    public function cmsadmin_dbsectiongroup_join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom) {
        return $this->dbsectiongroup->join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom);
    }

    public function cmsadmin_dbsectiongroup_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbsectiongroup->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbsectiongroup_newObject($name,$moduleName) {
        return $this->dbsectiongroup->newObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbsectiongroup_class_inc.php========
     */

    public function cmsadmin_dbsectiongroup_edit($id,$sectionId,$groupId) {
        return $this->dbsectiongroup->edit($id,$sectionId,$groupId);
    }



    /*
     * =============== Logical Add Data Methods =======dbblocks_class_inc.php========
     */

    public function cmsadmin_dbblocks_init() {
        return $this->dbblocks->init();
    }

    public function cmsadmin_dbblocks_add($pageId,$sectionId,$blockId,$blockCat,$left) {
        return $this->dbblocks->add($pageId,$sectionId,$blockId,$blockCat,$left);
    }

    public function cmsadmin_dbblocks_editPosition($pageId,$sectionId,$blockId,$blockCat,$left) {
        return $this->dbblocks->editPosition($pageId,$sectionId,$blockId,$blockCat,$left);
    }

    public function cmsadmin_dbblocks_getBlocksForPage($pageId,$sectionId,$left) {
        return $this->dbblocks->getBlocksForPage($pageId,$sectionId,$left);
    }

    public function cmsadmin_dbblocks_getPositionBlocksForPage($pageId,$sectionId) {
        return $this->dbblocks->getPositionBlocksForPage($pageId,$sectionId);
    }

    public function cmsadmin_dbblocks_getBlocksForSection($sectionId,$left) {
        return $this->dbblocks->getBlocksForSection($sectionId,$left);
    }

    public function cmsadmin_dbblocks_getPositionBlocksForSection($sectionId) {
        return $this->dbblocks->getPositionBlocksForSection($sectionId);
    }

    public function cmsadmin_dbblocks_getBlocksForFrontPage($left) {
        return $this->dbblocks->getBlocksForFrontPage($left);
    }

    public function cmsadmin_dbblocks_getPositionBlocksForFrontPage() {
        return $this->dbblocks->getPositionBlocksForFrontPage();
    }

    public function cmsadmin_dbblocks_getOrdering($pageid,$sectionid,$blockCat) {
        return $this->dbblocks->getOrdering($pageid,$sectionid,$blockCat);
    }

    public function cmsadmin_dbblocks_getOrderingLink($id,$blockCat) {
        return $this->dbblocks->getOrderingLink($id,$blockCat);
    }

    public function cmsadmin_dbblocks_getAddRemoveBlockForm($pageid,$sectionid,$blockCat) {
        return $this->dbblocks->getAddRemoveBlockForm($pageid,$sectionid,$blockCat);
    }

    public function cmsadmin_dbblocks_getPositionBlockForm($pageid,$sectionid,$blockCat) {
        return $this->dbblocks->getPositionBlockForm($pageid,$sectionid,$blockCat);
    }

    public function cmsadmin_dbblocks_getBlockEntries() {
        return $this->dbblocks->getBlockEntries();
    }

    public function cmsadmin_dbblocks_getBlock($blockId) {
        return $this->dbblocks->getBlock($blockId);
    }

    public function cmsadmin_dbblocks_getCmsBlock($blockId) {
        return $this->dbblocks->getCmsBlock($blockId);
    }

    public function cmsadmin_dbblocks_getBlockByName($blockName) {
        return $this->dbblocks->getBlockByName($blockName);
    }

    public function cmsadmin_dbblocks_getRow($pk_field,$pk_value) {
        return $this->dbblocks->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbblocks_getArray($stmt) {
        return $this->dbblocks->getArray($stmt);
    }

    public function cmsadmin_dbblocks_insert($fields,$tablename) {
        return $this->dbblocks->insert($fields,$tablename);
    }

    public function cmsadmin_dbblocks_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbblocks->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbblocks_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbblocks->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbblocks_getParam($name,$default) {
        return $this->dbblocks->getParam($name,$default);
    }

    public function cmsadmin_dbblocks_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbblocks->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbblocks_loadClass($name,$moduleName) {
        return $this->dbblocks->loadClass($name,$moduleName);
    }

    public function cmsadmin_dbblocks_newObject($name,$moduleName) {
        return $this->dbblocks->newObject($name,$moduleName);
    }

    public function cmsadmin_dbblocks_getObject($name,$moduleName) {
        return $this->dbblocks->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbblocks_class_inc.php========
     */

    public function cmsadmin_dbblocks_edit() {
        return $this->dbblocks->edit();
    }

    public function cmsadmin_dbblocks_changeOrder($id,$ordering,$pageId,$sectionId) {
        return $this->dbblocks->changeOrder($id,$ordering,$pageId,$sectionId);
    }



    /*
     * =============== Logical Delete Data Methods =======dbblocks_class_inc.php========
     */

    public function cmsadmin_dbblocks_deleteBlock($pageId,$sectionId,$blockId,$blockCat) {
        return $this->dbblocks->deleteBlock($pageId,$sectionId,$blockId,$blockCat);
    }

    public function cmsadmin_dbblocks_deleteBlockExplicit($pageId,$sectionId,$blockId,$blockCat) {
        return $this->dbblocks->deleteBlockExplicit($pageId,$sectionId,$blockId,$blockCat);
    }

    public function cmsadmin_dbblocks_deleteAllBlocks($pageId,$sectionId,$blockId,$blockCat) {
        return $this->dbblocks->deleteAllBlocks($pageId,$sectionId,$blockId,$blockCat);
    }

    public function cmsadmin_dbblocks_deleteBlockById($id) {
        return $this->dbblocks->deleteBlockById($id);
    }

    public function cmsadmin_dbblocks_getAll($filter) {
        return $this->dbblocks->getAll($filter);
    }



    /*
     * =============== Logical Add Data Methods =======cmsutils_class_inc.php========
     */

    public function cmsadmin_cmsutils_init() {
        return $this->cmsutils->init();
    }

    public function cmsadmin_cmsutils_getAccess($access) {
        return $this->cmsutils->getAccess($access);
    }

    public function cmsadmin_cmsutils_getYesNoRadion($name,$selected,$showIcon) {
        return $this->cmsutils->getYesNoRadion($name,$selected,$showIcon);
    }

    public function cmsadmin_cmsutils_getAccessList($name) {
        return $this->cmsutils->getAccessList($name);
    }

    public function cmsadmin_cmsutils_getLayoutOptions($name,$id) {
        return $this->cmsutils->getLayoutOptions($name,$id);
    }

    public function cmsadmin_cmsutils_getContextControlPanel() {
        return $this->cmsutils->getContextControlPanel();
    }

    public function cmsadmin_cmsutils_getControlPanel() {
        return $this->cmsutils->getControlPanel();
    }

    public function cmsadmin_cmsutils_topNav($action,$params) {
        return $this->cmsutils->topNav($action,$params);
    }

    public function cmsadmin_cmsutils_getConfigTemplateTabs($arrContent) {
        return $this->cmsutils->getConfigTemplateTabs($arrContent);
    }

    public function cmsadmin_cmsutils_getConfigTabs($arrContent) {
        return $this->cmsutils->getConfigTabs($arrContent);
    }

    public function cmsadmin_cmsutils_getCheckIcon($isCheck,$returnFalse) {
        return $this->cmsutils->getCheckIcon($isCheck,$returnFalse);
    }

    public function cmsadmin_cmsutils_getPublicAccessIcon($isCheck,$returnFalse) {
        return $this->cmsutils->getPublicAccessIcon($isCheck,$returnFalse);
    }

    public function cmsadmin_cmsutils_getNav($hideCmsMenu) {
        return $this->cmsutils->getNav($hideCmsMenu);
    }

    public function cmsadmin_cmsutils_showGroupContent($groupId,$groupFieldName) {
        return $this->cmsutils->showGroupContent($groupId,$groupFieldName);
    }

    public function cmsadmin_cmsutils_getSectionGroupNames($sectionid) {
        return $this->cmsutils->getSectionGroupNames($sectionid);
    }

    public function cmsadmin_cmsutils_getSectionUserNames($sectionid) {
        return $this->cmsutils->getSectionUserNames($sectionid);
    }

    public function cmsadmin_cmsutils_getContentGroupNames($contentid) {
        return $this->cmsutils->getContentGroupNames($contentid);
    }

    public function cmsadmin_cmsutils_getContentUserNames($contentid) {
        return $this->cmsutils->getContentUserNames($contentid);
    }

    public function cmsadmin_cmsutils_inGroup($group) {
        return $this->cmsutils->inGroup($group);
    }

    public function cmsadmin_cmsutils_inGroupById($groupId) {
        return $this->cmsutils->inGroupById($groupId);
    }

    public function cmsadmin_cmsutils_userGroups() {
        return $this->cmsutils->userGroups();
    }

    public function cmsadmin_cmsutils_showEditNode($menuNodeId) {
        return $this->cmsutils->showEditNode($menuNodeId);
    }

    public function cmsadmin_cmsutils_getParam($name,$default) {
        return $this->cmsutils->getParam($name,$default);
    }

    public function cmsadmin_cmsutils_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->cmsutils->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_cmsutils_loadClass($name,$moduleName) {
        return $this->cmsutils->loadClass($name,$moduleName);
    }

    public function cmsadmin_cmsutils_newObject($name,$moduleName) {
        return $this->cmsutils->newObject($name,$moduleName);
    }

    public function cmsadmin_cmsutils_getObject($name,$moduleName) {
        return $this->cmsutils->getObject($name,$moduleName);
    }

    public function cmsadmin_cmsutils_getResourceUri($resourcePath,$moduleName) {
        return $this->cmsutils->getResourceUri($resourcePath,$moduleName);
    }

    public function cmsadmin_cmsutils_appendArrayVar($name,$value) {
        return $this->cmsutils->appendArrayVar($name,$value);
    }



    /*
     * =============== Logical Add Data Methods =======buildtree_class_inc.php========
     */

    public function cmsadmin_buildtree_init() {
        return $this->buildtree->init();
    }

    public function cmsadmin_buildtree_show($currentId,$parentId,$treeType,$linkClass,$headerClass,$recursionDepth,$specifiedLink,$onlyPublished,$noPermissions) {
        return $this->buildtree->show($currentId,$parentId,$treeType,$linkClass,$headerClass,$recursionDepth,$specifiedLink,$onlyPublished,$noPermissions);
    }

    public function cmsadmin_buildtree_buildFlatMenu($parentId,$useBar,$linkClass,$onlyPublished) {
        return $this->buildtree->buildFlatMenu($parentId,$useBar,$linkClass,$onlyPublished);
    }

    public function cmsadmin_buildtree_buildLink($node,$specifiedLink) {
        return $this->buildtree->buildLink($node,$specifiedLink);
    }

    public function cmsadmin_buildtree_buildFoldoutMenu($parentId,$onlyPublished) {
        return $this->buildtree->buildFoldoutMenu($parentId,$onlyPublished);
    }

    public function cmsadmin_buildtree_buildStaticMenu($parentId,$linkClass,$headerClass,$onlyPublished) {
        return $this->buildtree->buildStaticMenu($parentId,$linkClass,$headerClass,$onlyPublished);
    }

    public function cmsadmin_buildtree_buildListMenu($parentId,$linkClass,$onlyPublished) {
        return $this->buildtree->buildListMenu($parentId,$linkClass,$onlyPublished);
    }

    public function cmsadmin_buildtree_buildCoolJSMenu($rootId,$onlyPublished,$xPos,$yPos) {
        return $this->buildtree->buildCoolJSMenu($rootId,$onlyPublished,$xPos,$yPos);
    }

    public function cmsadmin_buildtree_buildCoolJSItems($rootId,$onlyPublished) {
        return $this->buildtree->buildCoolJSItems($rootId,$onlyPublished);
    }

    public function cmsadmin_buildtree_getOpenNodes($nodeId,$rootId) {
        return $this->buildtree->getOpenNodes($nodeId,$rootId);
    }

    public function cmsadmin_buildtree_getOpenNodeIds($nodeId,$rootId) {
        return $this->buildtree->getOpenNodeIds($nodeId,$rootId);
    }

    public function cmsadmin_buildtree_getChildNodes($parentId,$onlyPublished,$noPermissions) {
        return $this->buildtree->getChildNodes($parentId,$onlyPublished,$noPermissions);
    }

    public function cmsadmin_buildtree_getNode($id,$noPermissions) {
        return $this->buildtree->getNode($id,$noPermissions);
    }

    public function cmsadmin_buildtree_getChildNodeCount($parentId,$onlyPublished,$noPermissions) {
        return $this->buildtree->getChildNodeCount($parentId,$onlyPublished,$noPermissions);
    }

    public function cmsadmin_buildtree_getRecursiveValue($id,$field,$rootId) {
        return $this->buildtree->getRecursiveValue($id,$field,$rootId);
    }

    public function cmsadmin_buildtree_getRoot($nodeId) {
        return $this->buildtree->getRoot($nodeId);
    }

    public function cmsadmin_buildtree_getBreadCrumb($nodeId,$rootId) {
        return $this->buildtree->getBreadCrumb($nodeId,$rootId);
    }

    public function cmsadmin_buildtree_buildDynamicTree($nodeId,$rootId,$onlyPublished) {
        return $this->buildtree->buildDynamicTree($nodeId,$rootId,$onlyPublished);
    }

    public function cmsadmin_buildtree_buildDynamicTreeTable($nodeArray,$level,$nodeId) {
        return $this->buildtree->buildDynamicTreeTable($nodeArray,$level,$nodeId);
    }

    public function cmsadmin_buildtree_getAncestorNodes($currentNode) {
        return $this->buildtree->getAncestorNodes($currentNode);
    }

    public function cmsadmin_buildtree_buildStandardTree($currentNode,$startingNode,$recursionDepth,$specifiedLink,$onlyPublished,$noPermissions) {
        return $this->buildtree->buildStandardTree($currentNode,$startingNode,$recursionDepth,$specifiedLink,$onlyPublished,$noPermissions);
    }

    public function cmsadmin_buildtree_buildStandardTreeLevel($parentId,$currentNode,$currentLevel,$recursionDepth,$specifiedLink,$onlyPublished,$noPermissions) {
        return $this->buildtree->buildStandardTreeLevel($parentId,$currentNode,$currentLevel,$recursionDepth,$specifiedLink,$onlyPublished,$noPermissions);
    }

    public function cmsadmin_buildtree_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->buildtree->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_buildtree_newObject($name,$moduleName) {
        return $this->buildtree->newObject($name,$moduleName);
    }

    public function cmsadmin_buildtree_getJavascriptFile($javascriptFile,$moduleName) {
        return $this->buildtree->getJavascriptFile($javascriptFile,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======dbcontentfrontpage_class_inc.php========
     */

    public function cmsadmin_dbcontentfrontpage_init() {
        return $this->dbcontentfrontpage->init();
    }

    public function cmsadmin_dbcontentfrontpage_removeIfExists($pageId) {
        return $this->dbcontentfrontpage->removeIfExists($pageId);
    }

    public function cmsadmin_dbcontentfrontpage_getFrontPages($published) {
        return $this->dbcontentfrontpage->getFrontPages($published);
    }

    public function cmsadmin_dbcontentfrontpage_isFrontPage($id) {
        return $this->dbcontentfrontpage->isFrontPage($id);
    }

    public function cmsadmin_dbcontentfrontpage_getFrontPage($contentId) {
        return $this->dbcontentfrontpage->getFrontPage($contentId);
    }

    public function cmsadmin_dbcontentfrontpage_changeStatus($id,$mode) {
        return $this->dbcontentfrontpage->changeStatus($id,$mode);
    }

    public function cmsadmin_dbcontentfrontpage_getOrdering() {
        return $this->dbcontentfrontpage->getOrdering();
    }

    public function cmsadmin_dbcontentfrontpage_getOrderingLink($id,$pos,$number,$total) {
        return $this->dbcontentfrontpage->getOrderingLink($id,$pos,$number,$total);
    }

    public function cmsadmin_dbcontentfrontpage_hasFrontPageContent() {
        return $this->dbcontentfrontpage->hasFrontPageContent();
    }

    public function cmsadmin_dbcontentfrontpage_getAll($filter) {
        return $this->dbcontentfrontpage->getAll($filter);
    }

    public function cmsadmin_dbcontentfrontpage_getRow($pk_field,$pk_value) {
        return $this->dbcontentfrontpage->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbcontentfrontpage_getArray($stmt) {
        return $this->dbcontentfrontpage->getArray($stmt);
    }

    public function cmsadmin_dbcontentfrontpage_insert($fields,$tablename) {
        return $this->dbcontentfrontpage->insert($fields,$tablename);
    }

    public function cmsadmin_dbcontentfrontpage_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbcontentfrontpage->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbcontentfrontpage_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbcontentfrontpage->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbcontentfrontpage_getParam($name,$default) {
        return $this->dbcontentfrontpage->getParam($name,$default);
    }

    public function cmsadmin_dbcontentfrontpage_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbcontentfrontpage->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbcontentfrontpage_loadClass($name,$moduleName) {
        return $this->dbcontentfrontpage->loadClass($name,$moduleName);
    }

    public function cmsadmin_dbcontentfrontpage_newObject($name,$moduleName) {
        return $this->dbcontentfrontpage->newObject($name,$moduleName);
    }

    public function cmsadmin_dbcontentfrontpage_getObject($name,$moduleName) {
        return $this->dbcontentfrontpage->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbcontentfrontpage_class_inc.php========
     */

    public function cmsadmin_dbcontentfrontpage_add($contentId,$ordering) {
        return $this->dbcontentfrontpage->add($contentId,$ordering);
    }

    public function cmsadmin_dbcontentfrontpage_reorderContent($pageId) {
        return $this->dbcontentfrontpage->reorderContent($pageId);
    }

    public function cmsadmin_dbcontentfrontpage_changeOrder($id,$ordering,$position) {
        return $this->dbcontentfrontpage->changeOrder($id,$ordering,$position);
    }

    public function cmsadmin_dbcontentfrontpage_valueExists($field,$value,$table) {
        return $this->dbcontentfrontpage->valueExists($field,$value,$table);
    }



    /*
     * =============== Logical Delete Data Methods =======dbcontentfrontpage_class_inc.php========
     */

    public function cmsadmin_dbcontentfrontpage_remove($id) {
        return $this->dbcontentfrontpage->remove($id);
    }



    /*
     * =============== Logical Add Data Methods =======dblayouts_class_inc.php========
     */

    public function cmsadmin_dblayouts_init() {
        return $this->dblayouts->init();
    }

    public function cmsadmin_dblayouts_getLayouts() {
        return $this->dblayouts->getLayouts();
    }

    public function cmsadmin_dblayouts_getLayout($name) {
        return $this->dblayouts->getLayout($name);
    }

    public function cmsadmin_dblayouts_getLayoutDescription($name) {
        return $this->dblayouts->getLayoutDescription($name);
    }

    public function cmsadmin_dblayouts_getUserRss($userid) {
        return $this->dblayouts->getUserRss($userid);
    }

    public function cmsadmin_dblayouts_getRssById($id) {
        return $this->dblayouts->getRssById($id);
    }

    public function cmsadmin_dblayouts__changeTable($id) {
        return $this->dblayouts->_changeTable($id);
    }

    public function cmsadmin_dblayouts_getAll($filter) {
        return $this->dblayouts->getAll($filter);
    }

    public function cmsadmin_dblayouts_getRow($pk_field,$pk_value) {
        return $this->dblayouts->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dblayouts_insert($fields,$tablename) {
        return $this->dblayouts->insert($fields,$tablename);
    }

    public function cmsadmin_dblayouts_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dblayouts->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dblayouts_delete($pkfield,$pkvalue,$tablename) {
        return $this->dblayouts->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dblayouts_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dblayouts->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }



    /*
     * =============== Logical Edit Data Methods =======dblayouts_class_inc.php========
     */

    public function cmsadmin_dblayouts_addRss($rssarr,$mode) {
        return $this->dblayouts->addRss($rssarr,$mode);
    }



    /*
     * =============== Logical Delete Data Methods =======dblayouts_class_inc.php========
     */

    public function cmsadmin_dblayouts_delRss($id) {
        return $this->dblayouts->delRss($id);
    }



    /*
     * =============== Logical Add Data Methods =======cmstree_class_inc.php========
     */

    public function cmsadmin_cmstree_init() {
        return $this->cmstree->init();
    }

    public function cmsadmin_cmstree_show($currentNode,$admin) {
        return $this->cmstree->show($currentNode,$admin);
    }

    public function cmsadmin_cmstree_buildTree($currentNode,$admin) {
        return $this->cmstree->buildTree($currentNode,$admin);
    }

    public function cmsadmin_cmstree_buildLevel($parentId,$currentNode,$admin) {
        return $this->cmstree->buildLevel($parentId,$currentNode,$admin);
    }

    public function cmsadmin_cmstree_addContent($id) {
        return $this->cmstree->addContent($id);
    }

    public function cmsadmin_cmstree_getChildNodes($parentId) {
        return $this->cmstree->getChildNodes($parentId);
    }

    public function cmsadmin_cmstree_getNode($id) {
        return $this->cmstree->getNode($id);
    }

    public function cmsadmin_cmstree_getContent($sectionId) {
        return $this->cmstree->getContent($sectionId);
    }

    public function cmsadmin_cmstree_getChildNodeCount($parentId) {
        return $this->cmstree->getChildNodeCount($parentId);
    }

    public function cmsadmin_cmstree_getNodeContentCount($sectionId) {
        return $this->cmstree->getNodeContentCount($sectionId);
    }

    public function cmsadmin_cmstree_getSimpleCMSTree($current) {
        return $this->cmstree->getSimpleCMSTree($current);
    }

    public function cmsadmin_cmstree_getCMSTree($current) {
        return $this->cmstree->getCMSTree($current);
    }

    public function cmsadmin_cmstree_getCMSAdminTree($current) {
        return $this->cmstree->getCMSAdminTree($current);
    }

    public function cmsadmin_cmstree_getCMSAdminDropdownTree($defaultSelected,$includeRoot) {
        return $this->cmstree->getCMSAdminDropdownTree($defaultSelected,$includeRoot);
    }

    public function cmsadmin_cmstree_getCMSAdminSectionDropdownTree($defaultSelected,$includeRoot) {
        return $this->cmstree->getCMSAdminSectionDropdownTree($defaultSelected,$includeRoot);
    }

    public function cmsadmin_cmstree_getCMSAdminFlatDropdownTree($defaultSelected,$includeRoot) {
        return $this->cmstree->getCMSAdminFlatDropdownTree($defaultSelected,$includeRoot);
    }

    public function cmsadmin_cmstree_getFlatTree($module,$includeRoot,$useLinks) {
        return $this->cmstree->getFlatTree($module,$includeRoot,$useLinks);
    }

    public function cmsadmin_cmstree_getTree($module,$includeRoot,$useLinks) {
        return $this->cmstree->getTree($module,$includeRoot,$useLinks);
    }

    public function cmsadmin_cmstree_getSectionTree($module,$includeRoot,$useLinks) {
        return $this->cmstree->getSectionTree($module,$includeRoot,$useLinks);
    }

    public function cmsadmin_cmstree_getTreeCount($module,$includeRoot,$useLinks) {
        return $this->cmstree->getTreeCount($module,$includeRoot,$useLinks);
    }

    public function cmsadmin_cmstree_getTreeRootCount($module,$includeRoot,$useLinks) {
        return $this->cmstree->getTreeRootCount($module,$includeRoot,$useLinks);
    }

    public function cmsadmin_cmstree_getTreeForInContext($module,$includeRoot,$useLinks) {
        return $this->cmstree->getTreeForInContext($module,$includeRoot,$useLinks);
    }

    public function cmsadmin_cmstree_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->cmstree->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_cmstree_loadClass($name,$moduleName) {
        return $this->cmstree->loadClass($name,$moduleName);
    }

    public function cmsadmin_cmstree_newObject($name,$moduleName) {
        return $this->cmstree->newObject($name,$moduleName);
    }

    public function cmsadmin_cmstree_getObject($name,$moduleName) {
        return $this->cmstree->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======dbcontent_class_inc.php========
     */

    public function cmsadmin_dbcontent_init() {
        return $this->dbcontent->init();
    }

    public function cmsadmin_dbcontent_getChildContent($sectionid,$admin,$filter) {
        return $this->dbcontent->getChildContent($sectionid,$admin,$filter);
    }

    public function cmsadmin_dbcontent_getNChildContent($sectionId,$level,$published,$filter) {
        return $this->dbcontent->getNChildContent($sectionId,$level,$published,$filter);
    }

    public function cmsadmin_dbcontent_add() {
        return $this->dbcontent->add();
    }

    public function cmsadmin_dbcontent_addContent() {


//Retrieve Content Records
$result = $this->dbcontent->getContentPages('');
$this->recordCountBefore = count($result);

//Add Content
//TODO: Should these be declared as individual members / fixtures?

$title = 'CMS Unit Test ' . date("YMD");
$published = 0;
$override_date = null;
$start_publish = null;
$end_publish = null;
$creatorid = 'init_1';
$show_title = 'y';
$show_author = 'y';
$show_date = 'y';
$show_pdf = 'y';
$show_email = 'y';
$show_print = 'y';
$access = null;
$created_by = 'init_1';
$introText = 'Unit Test Intro Text ';
$fullText = 'Unit Test Full Text';
$metakey = 'Unit Test Meta Key';
$metadesc = 'Unit Test Meta Description';
$ccLicence = null;
$sectionId = 'init_1';

$result = $this->dbcontent->addContent($title,
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

        //Maintaining Fixture State
        $this->currentId = $result;
        $this->objState->currentId = $result;

        //TODO: check if the are params before displaying (...) for warnings

        $this->assertNotEquals('', $result, 'Warning: dbcontent->addContent(...) Returned \'\'');
        $this->assertNotEquals(null, $result, 'Warning: dbcontent->addContent(...) Returned NULL');

        //Checking that the fields where added correctly
        $record = $this->dbcontent->getAll(" WHERE id = '{$this->currentId}' ");
        $record = $record[0];

        //Checking ID
        $this->assertNotEquals(FALSE, ($this->cmsadmin_tbl_cms_content_id($record) == $this->currentId));
        $this->assertNotEquals('', $this->cmsadmin_tbl_cms_content_id($record));

        //Checking Title
        $this->assertEquals($title, $this->cmsadmin_tbl_cms_content_title($record));

        //Checking Published
        $this->assertEquals($published, $this->cmsadmin_tbl_cms_content_published($record));

        //Checking Override_date
        $this->assertNotEquals($override_date, $this->cmsadmin_tbl_cms_content_override_date($record));

        //Checking Start Publish
        $this->assertEquals($start_publish, $this->cmsadmin_tbl_cms_content_start_publish($record));

        //Checking End Publish
        $this->assertEquals($end_publish, $this->cmsadmin_tbl_cms_content_end_publish($record));

        //Checking creatorid
        $this->assertEquals($creatorid, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking Show Title
        $this->assertEquals($show_title, $this->cmsadmin_tbl_cms_content_show_title($record));

        //Checking Show Author
        $this->assertEquals($show_author, $this->cmsadmin_tbl_cms_content_show_author($record));

        //Checking Show Date
        $this->assertEquals($show_date, $this->cmsadmin_tbl_cms_content_show_date($record));

        //Checking Show Pdf
        $this->assertEquals($show_pdf, $this->cmsadmin_tbl_cms_content_show_pdf($record));

        //Checking Show Email
        $this->assertEquals($show_email, $this->cmsadmin_tbl_cms_content_show_email($record));

        //Checking Show Print
        $this->assertEquals($show_print, $this->cmsadmin_tbl_cms_content_show_print($record));

        //Checking Access
        $this->assertEquals($access, $this->cmsadmin_tbl_cms_content_access($record));

        //Checking Created By
        $this->assertEquals($created_by, $this->cmsadmin_tbl_cms_content_created_by($record));

        //Checking IntroText
        $this->assertEquals($introText, $this->cmsadmin_tbl_cms_content_introtext($record));

        //Checking FullText
        $this->assertEquals($fullText, $this->cmsadmin_tbl_cms_content_body($record));

        //Checking Metakey
        $this->assertEquals($metakey, $this->cmsadmin_tbl_cms_content_metakey($record));

        //Checking Meta Description
        $this->assertEquals($metadesc, $this->cmsadmin_tbl_cms_content_metadesc($record));

        //Checking Licence
        $this->assertEquals($ccLicence, $this->cmsadmin_tbl_cms_content_post_lic($record));

        //Checking Meta Description
        $this->assertEquals($sectionId, $this->cmsadmin_tbl_cms_content_sectionid($record));

    }

    public function cmsadmin_dbcontent_addNewPage($title,$sectionid,$published,$access,$introText,$fullText,$isFrontPage,$ccLicence) {
        return $this->dbcontent->addNewPage($title,$sectionid,$published,$access,$introText,$fullText,$isFrontPage,$ccLicence);
    }

    public function cmsadmin_dbcontent_getHrefContentRecords($sectionid) {
        return $this->dbcontent->getHrefContentRecords($sectionid);
    }

    public function cmsadmin_dbcontent_getContentPages($filter) {
        return $this->dbcontent->getContentPages($filter);
    }

    public function cmsadmin_dbcontent_getArchivePages($filter) {
        return $this->dbcontent->getArchivePages($filter);
    }

    public function cmsadmin_dbcontent_getContentPage($id) {
        return $this->dbcontent->getContentPage($id);
    }

    public function cmsadmin_dbcontent_getContentPageFiltered($id,$filter) {
        return $this->dbcontent->getContentPageFiltered($id,$filter);
    }

    public function cmsadmin_dbcontent_getPagesInSection($sectionId,$isPublished) {
        return $this->dbcontent->getPagesInSection($sectionId,$isPublished);
    }

    public function cmsadmin_dbcontent_getPagesInSectionJoinFront($sectionId) {
        return $this->dbcontent->getPagesInSectionJoinFront($sectionId);
    }

    public function cmsadmin_dbcontent_getTitles($title,$limit) {
        return $this->dbcontent->getTitles($title,$limit);
    }

    public function cmsadmin_dbcontent_getLatestTitles($n) {
        return $this->dbcontent->getLatestTitles($n);
    }

    public function cmsadmin_dbcontent_getNumberOfPagesInSection($sectionId) {
        return $this->dbcontent->getNumberOfPagesInSection($sectionId);
    }

    public function cmsadmin_dbcontent_getPageOrder($pageId) {
        return $this->dbcontent->getPageOrder($pageId);
    }

    public function cmsadmin_dbcontent_getOrdering($sectionId) {
        return $this->dbcontent->getOrdering($sectionId);
    }

    public function cmsadmin_dbcontent_getOrderingLink($sectionid,$id) {
        return $this->dbcontent->getOrderingLink($sectionid,$id);
    }

    public function cmsadmin_dbcontent_html2txt($document,$scrub) {
        return $this->dbcontent->html2txt($document,$scrub);
    }

    public function cmsadmin_dbcontent_luceneIndex($data) {
        return $this->dbcontent->luceneIndex($data);
    }

    public function cmsadmin_dbcontent_getParent($contentId) {
        return $this->dbcontent->getParent($contentId);
    }

    public function cmsadmin_dbcontent_getAll($filter) {
        return $this->dbcontent->getAll($filter);
    }

    public function cmsadmin_dbcontent_getRow($pk_field,$pk_value) {
        return $this->dbcontent->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbcontent_getArray($stmt) {
        return $this->dbcontent->getArray($stmt);
    }

    public function cmsadmin_dbcontent_join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom) {
        return $this->dbcontent->join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom);
    }

    public function cmsadmin_dbcontent_now() {
        return $this->dbcontent->now();
    }

    public function cmsadmin_dbcontent_getParam($name,$default) {
        return $this->dbcontent->getParam($name,$default);
    }

    public function cmsadmin_dbcontent_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbcontent->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbcontent_newObject($name,$moduleName) {
        return $this->dbcontent->newObject($name,$moduleName);
    }

    public function cmsadmin_dbcontent_getObject($name,$moduleName) {
        return $this->dbcontent->getObject($name,$moduleName);
    }





    /*
     * =============== Logical Add Data Methods =======dbcmsadmin_class_inc.php========
     */

    public function cmsadmin_dbcmsadmin_init() {
        return $this->dbcmsadmin->init();
    }

    public function cmsadmin_dbcmsadmin_getBlockEntries() {
        return $this->dbcmsadmin->getBlockEntries();
    }

    public function cmsadmin_dbcmsadmin_getModuleBlock($blockId) {
        return $this->dbcmsadmin->getModuleBlock($blockId);
    }

    public function cmsadmin_dbcmsadmin_getBlockByName($blockName) {
        return $this->dbcmsadmin->getBlockByName($blockName);
    }

    public function cmsadmin_dbcmsadmin_addBlock($pageId,$sectionId,$blockId,$blockCat,$left) {
        return $this->dbcmsadmin->addBlock($pageId,$sectionId,$blockId,$blockCat,$left);
    }

    public function cmsadmin_dbcmsadmin_getBlocksForPage($pageId,$sectionId,$left) {
        return $this->dbcmsadmin->getBlocksForPage($pageId,$sectionId,$left);
    }

    public function cmsadmin_dbcmsadmin_getBlocksForSection($sectionId,$left) {
        return $this->dbcmsadmin->getBlocksForSection($sectionId,$left);
    }

    public function cmsadmin_dbcmsadmin_getBlocksForFrontPage($left) {
        return $this->dbcmsadmin->getBlocksForFrontPage($left);
    }

    public function cmsadmin_dbcmsadmin_getCategories() {
        return $this->dbcmsadmin->getCategories();
    }

    public function cmsadmin_dbcmsadmin_getCatCount($sectionId) {
        return $this->dbcmsadmin->getCatCount($sectionId);
    }

    public function cmsadmin_dbcmsadmin_addCategory($parentSelected,$title,$image,$imagePostion,$access,$description,$published,$menuText) {
        return $this->dbcmsadmin->addCategory($parentSelected,$title,$image,$imagePostion,$access,$description,$published,$menuText);
    }

    public function cmsadmin_dbcmsadmin_getCategoryMenuText($id) {
        return $this->dbcmsadmin->getCategoryMenuText($id);
    }

    public function cmsadmin_dbcmsadmin_getCategory($id) {
        return $this->dbcmsadmin->getCategory($id);
    }

    public function cmsadmin_dbcmsadmin_getCategoryInSection($sectionId,$level) {
        return $this->dbcmsadmin->getCategoryInSection($sectionId,$level);
    }

    public function cmsadmin_dbcmsadmin_getCatLevel($id) {
        return $this->dbcmsadmin->getCatLevel($id);
    }

    public function cmsadmin_dbcmsadmin_getSectionIdOfCat($id) {
        return $this->dbcmsadmin->getSectionIdOfCat($id);
    }

    public function cmsadmin_dbcmsadmin_hasNodesCategory($id) {
        return $this->dbcmsadmin->hasNodesCategory($id);
    }

    public function cmsadmin_dbcmsadmin_addContent($pgarr) {
        return $this->dbcmsadmin->addContent($pgarr);
    }

    public function cmsadmin_dbcmsadmin_getContentPages($filter) {
        return $this->dbcmsadmin->getContentPages($filter);
    }

    public function cmsadmin_dbcmsadmin_getArchivePages($filter,$table) {
        return $this->dbcmsadmin->getArchivePages($filter,$table);
    }

    public function cmsadmin_dbcmsadmin_getContentPage($id) {
        return $this->dbcmsadmin->getContentPage($id);
    }

    public function cmsadmin_dbcmsadmin_getPagesInSection($sectionId,$isPublished) {
        return $this->dbcmsadmin->getPagesInSection($sectionId,$isPublished);
    }

    public function cmsadmin_dbcmsadmin_getPagesInSectionJoinFront($sectionId) {
        return $this->dbcmsadmin->getPagesInSectionJoinFront($sectionId);
    }

    public function cmsadmin_dbcmsadmin_getTitles($title,$limit) {
        return $this->dbcmsadmin->getTitles($title,$limit);
    }

    public function cmsadmin_dbcmsadmin_getLatestTitles($n) {
        return $this->dbcmsadmin->getLatestTitles($n);
    }

    public function cmsadmin_dbcmsadmin_getNumberOfPagesInSection($sectionId) {
        return $this->dbcmsadmin->getNumberOfPagesInSection($sectionId);
    }

    public function cmsadmin_dbcmsadmin_getPageOrder($pageId) {
        return $this->dbcmsadmin->getPageOrder($pageId);
    }

    public function cmsadmin_dbcmsadmin_getContentOrdering($sectionId) {
        return $this->dbcmsadmin->getContentOrdering($sectionId);
    }

    public function cmsadmin_dbcmsadmin_doFPOrdering() {
        return $this->dbcmsadmin->doFPOrdering();
    }

    public function cmsadmin_dbcmsadmin_getContentOrderingLink($sectionid,$id) {
        return $this->dbcmsadmin->getContentOrderingLink($sectionid,$id);
    }

    public function cmsadmin_dbcmsadmin_html2txt($document,$scrub) {
        return $this->dbcmsadmin->html2txt($document,$scrub);
    }

    public function cmsadmin_dbcmsadmin_lucenePageIndex($data) {
        return $this->dbcmsadmin->lucenePageIndex($data);
    }

    public function cmsadmin_dbcmsadmin_updateBlock($id,$fields) {
        return $this->dbcmsadmin->updateBlock($id,$fields);
    }

    public function cmsadmin_dbcmsadmin_getHtmlBlock($contextCode,$table) {
        return $this->dbcmsadmin->getHtmlBlock($contextCode,$table);
    }

    public function cmsadmin_dbcmsadmin_displayBlock($contextCode) {
        return $this->dbcmsadmin->displayBlock($contextCode);
    }

    public function cmsadmin_dbcmsadmin_getActive($table) {
        return $this->dbcmsadmin->getActive($table);
    }

    public function cmsadmin_dbcmsadmin_getStyles($table) {
        return $this->dbcmsadmin->getStyles($table);
    }

    public function cmsadmin_dbcmsadmin_getLayouts() {
        return $this->dbcmsadmin->getLayouts();
    }

    public function cmsadmin_dbcmsadmin_getLayout($name) {
        return $this->dbcmsadmin->getLayout($name);
    }

    public function cmsadmin_dbcmsadmin_getLayoutDescription($name) {
        return $this->dbcmsadmin->getLayoutDescription($name);
    }

    public function cmsadmin_dbcmsadmin_getUserRss($userid) {
        return $this->dbcmsadmin->getUserRss($userid);
    }

    public function cmsadmin_dbcmsadmin_getRssById($id) {
        return $this->dbcmsadmin->getRssById($id);
    }

    public function cmsadmin_dbcmsadmin_getGroupChildNodes($parentId) {
        return $this->dbcmsadmin->getGroupChildNodes($parentId);
    }

    public function cmsadmin_dbcmsadmin_getGroupChildNodeCount($parentId) {
        return $this->dbcmsadmin->getGroupChildNodeCount($parentId);
    }

    public function cmsadmin_dbcmsadmin_getGroupNode($id,$noPermissions) {
        return $this->dbcmsadmin->getGroupNode($id,$noPermissions);
    }

    public function cmsadmin_dbcmsadmin_getSectionGroupChildNodes($parentId,$admin) {
        return $this->dbcmsadmin->getSectionGroupChildNodes($parentId,$admin);
    }

    public function cmsadmin_dbcmsadmin_getChildNodeCount($parentId,$noPermissions) {
        return $this->dbcmsadmin->getChildNodeCount($parentId,$noPermissions);
    }

    public function cmsadmin_dbcmsadmin_getSectionGroupNode($id,$admin) {
        return $this->dbcmsadmin->getSectionGroupNode($id,$admin);
    }

    public function cmsadmin_dbcmsadmin_getGroupBySection($sectionId) {
        return $this->dbcmsadmin->getGroupBySection($sectionId);
    }

    public function cmsadmin_dbcmsadmin_getSectionByName($name) {
        return $this->dbcmsadmin->getSectionByName($name);
    }

    public function cmsadmin_dbcmsadmin_getSectionByGroup($groupId) {
        return $this->dbcmsadmin->getSectionByGroup($groupId);
    }

    public function cmsadmin_dbcmsadmin_addSectionGroup($sectionId,$groupId) {
        return $this->dbcmsadmin->addSectionGroup($sectionId,$groupId);
    }

    public function cmsadmin_dbcmsadmin_getSectionGroupId($sectionId) {
        return $this->dbcmsadmin->getSectionGroupId($sectionId);
    }

    public function cmsadmin_dbcmsadmin_getGroupNameBySection($sectionId) {
        return $this->dbcmsadmin->getGroupNameBySection($sectionId);
    }

    public function cmsadmin_dbcmsadmin_removeIfExists($pageId) {
        return $this->dbcmsadmin->removeIfExists($pageId);
    }

    public function cmsadmin_dbcmsadmin_getFrontPages($published) {
        return $this->dbcmsadmin->getFrontPages($published);
    }

    public function cmsadmin_dbcmsadmin_isFrontPage($id) {
        return $this->dbcmsadmin->isFrontPage($id);
    }

    public function cmsadmin_dbcmsadmin_getFrontPage($contentId) {
        return $this->dbcmsadmin->getFrontPage($contentId);
    }

    public function cmsadmin_dbcmsadmin_changeStatus($id,$mode) {
        return $this->dbcmsadmin->changeStatus($id,$mode);
    }

    public function cmsadmin_dbcmsadmin_getFPOrdering() {
        return $this->dbcmsadmin->getFPOrdering();
    }

    public function cmsadmin_dbcmsadmin_getOrderingLinkFP($id,$pos,$number,$total) {
        return $this->dbcmsadmin->getOrderingLinkFP($id,$pos,$number,$total);
    }

    public function cmsadmin_dbcmsadmin_hasFrontPageContent() {
        return $this->dbcmsadmin->hasFrontPageContent();
    }

    public function cmsadmin_dbcmsadmin_getSections($isPublished,$filter) {
        return $this->dbcmsadmin->getSections($isPublished,$filter);
    }

    public function cmsadmin_dbcmsadmin_getFilteredSections($text,$publish) {
        return $this->dbcmsadmin->getFilteredSections($text,$publish);
    }

    public function cmsadmin_dbcmsadmin_getArchiveSections($filter) {
        return $this->dbcmsadmin->getArchiveSections($filter);
    }

    public function cmsadmin_dbcmsadmin_getRootNodes($isPublished,$contextcode) {
        return $this->dbcmsadmin->getRootNodes($isPublished,$contextcode);
    }

    public function cmsadmin_dbcmsadmin_getSection($id) {
        return $this->dbcmsadmin->getSection($id);
    }

    public function cmsadmin_dbcmsadmin_getFirstSectionId($isPublished) {
        return $this->dbcmsadmin->getFirstSectionId($isPublished);
    }

    public function cmsadmin_dbcmsadmin_addSection($sectionArr) {
        return $this->dbcmsadmin->addSection($sectionArr);
    }

    public function cmsadmin_dbcmsadmin_checkindex($sectionArr) {
        return $this->dbcmsadmin->checkindex($sectionArr);
    }

    public function cmsadmin_dbcmsadmin_isSections() {
        return $this->dbcmsadmin->isSections();
    }

    public function cmsadmin_dbcmsadmin_getSectionMenuText($id) {
        return $this->dbcmsadmin->getSectionMenuText($id);
    }

    public function cmsadmin_dbcmsadmin_hasNodesSection($id) {
        return $this->dbcmsadmin->hasNodesSection($id);
    }

    public function cmsadmin_dbcmsadmin_getLevel($id) {
        return $this->dbcmsadmin->getLevel($id);
    }

    public function cmsadmin_dbcmsadmin_getRootNodeId($id) {
        return $this->dbcmsadmin->getRootNodeId($id);
    }

    public function cmsadmin_dbcmsadmin_getAllSections() {
        return $this->dbcmsadmin->getAllSections();
    }

    public function cmsadmin_dbcmsadmin_getSubSectionsInSection($sectionId,$order,$isPublished) {
        return $this->dbcmsadmin->getSubSectionsInSection($sectionId,$order,$isPublished);
    }

    public function cmsadmin_dbcmsadmin_getSubSectionsInRoot($rootId,$order,$isPublished) {
        return $this->dbcmsadmin->getSubSectionsInRoot($rootId,$order,$isPublished);
    }

    public function cmsadmin_dbcmsadmin_getSubSectionsForLevel($rootId,$level,$order,$isPublished) {
        return $this->dbcmsadmin->getSubSectionsForLevel($rootId,$level,$order,$isPublished);
    }

    public function cmsadmin_dbcmsadmin_getNumSubSections($sectionId) {
        return $this->dbcmsadmin->getNumSubSections($sectionId);
    }

    public function cmsadmin_dbcmsadmin_deleteSection($id) {
        return $this->dbcmsadmin->deleteSection($id);
    }

    public function cmsadmin_dbcmsadmin_unarchiveSection($id) {
        return $this->dbcmsadmin->unarchiveSection($id);
    }

    public function cmsadmin_dbcmsadmin_unarchiveSectionContent($id) {
        return $this->dbcmsadmin->unarchiveSectionContent($id);
    }

    public function cmsadmin_dbcmsadmin_getSectionOrdering($parentid) {
        return $this->dbcmsadmin->getSectionOrdering($parentid);
    }

    public function cmsadmin_dbcmsadmin_getOrderingLinkSection($id) {
        return $this->dbcmsadmin->getOrderingLinkSection($id);
    }

    public function cmsadmin_dbcmsadmin_getPageOrderType($orderType) {
        return $this->dbcmsadmin->getPageOrderType($orderType);
    }

    public function cmsadmin_dbcmsadmin_luceneSectionIndex($data) {
        return $this->dbcmsadmin->luceneSectionIndex($data);
    }

    public function cmsadmin_dbcmsadmin_removeLuceneIndex($id) {
        return $this->dbcmsadmin->removeLuceneIndex($id);
    }

    public function cmsadmin_dbcmsadmin_getSectionByContextCode() {
        return $this->dbcmsadmin->getSectionByContextCode();
    }

    public function cmsadmin_dbcmsadmin__getBlockOrdering() {
        return $this->dbcmsadmin->_getBlockOrdering();
    }

    public function cmsadmin_dbcmsadmin__changeTable() {
        return $this->dbcmsadmin->_changeTable();
    }

    public function cmsadmin_dbcmsadmin_valueExists($field,$value,$table) {
        return $this->dbcmsadmin->valueExists($field,$value,$table);
    }

    public function cmsadmin_dbcmsadmin_getRow($pk_field,$pk_value) {
        return $this->dbcmsadmin->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbcmsadmin_getArray($stmt) {
        return $this->dbcmsadmin->getArray($stmt);
    }

    public function cmsadmin_dbcmsadmin_insert($fields,$tablename) {
        return $this->dbcmsadmin->insert($fields,$tablename);
    }

    public function cmsadmin_dbcmsadmin_now() {
        return $this->dbcmsadmin->now();
    }

    public function cmsadmin_dbcmsadmin_getParam($name,$default) {
        return $this->dbcmsadmin->getParam($name,$default);
    }

    public function cmsadmin_dbcmsadmin_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbcmsadmin->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbcmsadmin_newObject($name,$moduleName) {
        return $this->dbcmsadmin->newObject($name,$moduleName);
    }

    public function cmsadmin_dbcmsadmin_getObject($name,$moduleName) {
        return $this->dbcmsadmin->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbcmsadmin_class_inc.php========
     */

    public function cmsadmin_dbcmsadmin_editBlock($id,$pageId,$ordering,$blockId) {
        return $this->dbcmsadmin->editBlock($id,$pageId,$ordering,$blockId);
    }

    public function cmsadmin_dbcmsadmin_changeBlockOrder($id,$ordering,$pageId,$sectionId) {
        return $this->dbcmsadmin->changeBlockOrder($id,$ordering,$pageId,$sectionId);
    }

    public function cmsadmin_dbcmsadmin_editCategory($id,$section,$title,$menuText,$image,$imagePostion,$access,$desciption,$published,$ordering) {
        return $this->dbcmsadmin->editCategory($id,$section,$title,$menuText,$image,$imagePostion,$access,$desciption,$published,$ordering);
    }

//Regular Expression Error: codeanalyzer_class_inc.php extractParams($mthd) Error
/*
    public function cmsadmin_dbcmsadmin_editContent($id,$title,$sectionid,$published,$access,$introText,$fullText,$override_date,$start_publish,$|�7�ss,$    oText,$    Text,$metakey,$metadesc,$ccLicence,$hide_title,$creatorid,$isFrontPage) {
        return $this->dbcmsadmin->editContent($id,$title,$sectionid,$published,$access,$introText,$fullText,$override_date,$start_publish,$|�7�ss,$    oText,$    Text,$metakey,$metadesc,$ccLicence,$hide_title,$creatorid,$isFrontPage);
    }
*/
    public function cmsadmin_dbcmsadmin_trashContent($id) {
        return $this->dbcmsadmin->trashContent($id);
    }

    public function cmsadmin_dbcmsadmin_reorderContent($id) {
        return $this->dbcmsadmin->reorderContent($id);
    }

    public function cmsadmin_dbcmsadmin_undelete($id) {
        return $this->dbcmsadmin->undelete($id);
    }

    public function cmsadmin_dbcmsadmin_toggleContentPublish($id) {
        return $this->dbcmsadmin->toggleContentPublish($id);
    }

    public function cmsadmin_dbcmsadmin_publishContent($id,$task) {
        return $this->dbcmsadmin->publishContent($id,$task);
    }

    public function cmsadmin_dbcmsadmin_resetSection($sectionId) {
        return $this->dbcmsadmin->resetSection($sectionId);
    }

    public function cmsadmin_dbcmsadmin_unarchiveContent($sectionId) {
        return $this->dbcmsadmin->unarchiveContent($sectionId);
    }

    public function cmsadmin_dbcmsadmin_changeContentOrder($sectionid,$id,$ordering) {
        return $this->dbcmsadmin->changeContentOrder($sectionid,$id,$ordering);
    }

    public function cmsadmin_dbcmsadmin_updateActive($id) {
        return $this->dbcmsadmin->updateActive($id);
    }

    public function cmsadmin_dbcmsadmin_addRss($rssarr,$mode) {
        return $this->dbcmsadmin->addRss($rssarr,$mode);
    }

    public function cmsadmin_dbcmsadmin_editSectionGroup($id,$sectionId,$groupId) {
        return $this->dbcmsadmin->editSectionGroup($id,$sectionId,$groupId);
    }

    public function cmsadmin_dbcmsadmin_addFrontPageContent($contentId,$ordering,$show_content) {
        return $this->dbcmsadmin->addFrontPageContent($contentId,$ordering,$show_content);
    }

    public function cmsadmin_dbcmsadmin_reorderFPContent($pageId) {
        return $this->dbcmsadmin->reorderFPContent($pageId);
    }

    public function cmsadmin_dbcmsadmin_changeFPOrder($id,$ordering,$position) {
        return $this->dbcmsadmin->changeFPOrder($id,$ordering,$position);
    }

    public function cmsadmin_dbcmsadmin_editSection($id,$parentid,$rootid,$title,$menuText,$access,$description,$published,$layout,$showdate,$hidetitle,$showintroduction,$pagenum,$customnumber,$ordertype,$ordering,$count,$imagesrc) {
        return $this->dbcmsadmin->editSection($id,$parentid,$rootid,$title,$menuText,$access,$description,$published,$layout,$showdate,$hidetitle,$showintroduction,$pagenum,$customnumber,$ordertype,$ordering,$count,$imagesrc);
    }

    public function cmsadmin_dbcmsadmin_toggleSectionPublish($id) {
        return $this->dbcmsadmin->toggleSectionPublish($id);
    }

    public function cmsadmin_dbcmsadmin_publishSection($id,$task) {
        return $this->dbcmsadmin->publishSection($id,$task);
    }

    public function cmsadmin_dbcmsadmin_archive($id) {
        return $this->dbcmsadmin->archive($id);
    }

    public function cmsadmin_dbcmsadmin_changeSectionOrder($id,$ordering,$parentid) {
        return $this->dbcmsadmin->changeSectionOrder($id,$ordering,$parentid);
    }

    public function cmsadmin_dbcmsadmin_reorderSections($id,$ordering,$parentid) {
        return $this->dbcmsadmin->reorderSections($id,$ordering,$parentid);
    }

    public function cmsadmin_dbcmsadmin_getAll($filter) {
        return $this->dbcmsadmin->getAll($filter);
    }

    public function cmsadmin_dbcmsadmin_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbcmsadmin->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbcmsadmin_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbcmsadmin->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbcmsadmin_query($stmt) {
        return $this->dbcmsadmin->query($stmt);
    }

    public function cmsadmin_dbcmsadmin_join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom) {
        return $this->dbcmsadmin->join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom);
    }



    /*
     * =============== Logical Delete Data Methods =======dbcmsadmin_class_inc.php========
     */

    public function cmsadmin_dbcmsadmin_deleteBlock($pageId,$sectionId,$blockId,$blockCat) {
        return $this->dbcmsadmin->deleteBlock($pageId,$sectionId,$blockId,$blockCat);
    }

    public function cmsadmin_dbcmsadmin_deleteBlockById($id) {
        return $this->dbcmsadmin->deleteBlockById($id);
    }

    public function cmsadmin_dbcmsadmin_deleteCat($id) {
        return $this->dbcmsadmin->deleteCat($id);
    }

    public function cmsadmin_dbcmsadmin_deleteContent($id) {
        return $this->dbcmsadmin->deleteContent($id);
    }

    public function cmsadmin_dbcmsadmin_delRss($id) {
        return $this->dbcmsadmin->delRss($id);
    }

    public function cmsadmin_dbcmsadmin_remove($id) {
        return $this->dbcmsadmin->remove($id);
    }

    public function cmsadmin_dbcmsadmin_permanentlyDelete($id) {
        return $this->dbcmsadmin->permanentlyDelete($id);
    }

    public function cmsadmin_dbcmsadmin_getRecordCount($filter) {
        return $this->dbcmsadmin->getRecordCount($filter);
    }



    /*
     * =============== Logical Add Data Methods =======dbcontentpreview_class_inc.php========
     */

    public function cmsadmin_dbcontentpreview_init() {
        return $this->dbcontentpreview->init();
    }

    public function cmsadmin_dbcontentpreview_getChildContent($sectionid,$admin,$filter) {
        return $this->dbcontentpreview->getChildContent($sectionid,$admin,$filter);
    }

    public function cmsadmin_dbcontentpreview_add() {
        return $this->dbcontentpreview->add();
    }

    public function cmsadmin_dbcontentpreview_addNewPage($title,$sectionid,$published,$access,$introText,$fullText,$isFrontPage,$ccLicence) {
        return $this->dbcontentpreview->addNewPage($title,$sectionid,$published,$access,$introText,$fullText,$isFrontPage,$ccLicence);
    }

    public function cmsadmin_dbcontentpreview_getHrefContentRecords($sectionid) {
        return $this->dbcontentpreview->getHrefContentRecords($sectionid);
    }

    public function cmsadmin_dbcontentpreview_getContentPages($filter) {
        return $this->dbcontentpreview->getContentPages($filter);
    }

    public function cmsadmin_dbcontentpreview_getArchivePages($filter) {
        return $this->dbcontentpreview->getArchivePages($filter);
    }

    public function cmsadmin_dbcontentpreview_getContentPage($id) {
        return $this->dbcontentpreview->getContentPage($id);
    }

    public function cmsadmin_dbcontentpreview_getContentPageFiltered($id,$filter) {
        return $this->dbcontentpreview->getContentPageFiltered($id,$filter);
    }

    public function cmsadmin_dbcontentpreview_getPagesInSection($sectionId,$isPublished) {
        return $this->dbcontentpreview->getPagesInSection($sectionId,$isPublished);
    }

    public function cmsadmin_dbcontentpreview_getTitles($title,$limit) {
        return $this->dbcontentpreview->getTitles($title,$limit);
    }

    public function cmsadmin_dbcontentpreview_getLatestTitles($n) {
        return $this->dbcontentpreview->getLatestTitles($n);
    }

    public function cmsadmin_dbcontentpreview_getNumberOfPagesInSection($sectionId) {
        return $this->dbcontentpreview->getNumberOfPagesInSection($sectionId);
    }

    public function cmsadmin_dbcontentpreview_getPageOrder($pageId) {
        return $this->dbcontentpreview->getPageOrder($pageId);
    }

    public function cmsadmin_dbcontentpreview_getOrdering($sectionId) {
        return $this->dbcontentpreview->getOrdering($sectionId);
    }

    public function cmsadmin_dbcontentpreview_getOrderingLink($sectionid,$id) {
        return $this->dbcontentpreview->getOrderingLink($sectionid,$id);
    }

    public function cmsadmin_dbcontentpreview_html2txt($document,$scrub) {
        return $this->dbcontentpreview->html2txt($document,$scrub);
    }

    public function cmsadmin_dbcontentpreview_luceneIndex($data) {
        return $this->dbcontentpreview->luceneIndex($data);
    }

    public function cmsadmin_dbcontentpreview_getParent($contentId) {
        return $this->dbcontentpreview->getParent($contentId);
    }

    public function cmsadmin_dbcontentpreview_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbcontentpreview->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbcontentpreview_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbcontentpreview->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbcontentpreview_now() {
        return $this->dbcontentpreview->now();
    }

    public function cmsadmin_dbcontentpreview_getParam($name,$default) {
        return $this->dbcontentpreview->getParam($name,$default);
    }

    public function cmsadmin_dbcontentpreview_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbcontentpreview->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbcontentpreview_newObject($name,$moduleName) {
        return $this->dbcontentpreview->newObject($name,$moduleName);
    }

    public function cmsadmin_dbcontentpreview_getObject($name,$moduleName) {
        return $this->dbcontentpreview->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbcontentpreview_class_inc.php========
     */

    public function cmsadmin_dbcontentpreview_edit() {
        return $this->dbcontentpreview->edit();
    }

    public function cmsadmin_dbcontentpreview_updateContentBody($contentid,$body) {
        return $this->dbcontentpreview->updateContentBody($contentid,$body);
    }

    public function cmsadmin_dbcontentpreview_trashContent($id) {
        return $this->dbcontentpreview->trashContent($id);
    }

    public function cmsadmin_dbcontentpreview_reorderContent($id) {
        return $this->dbcontentpreview->reorderContent($id);
    }

    public function cmsadmin_dbcontentpreview_undelete($id) {
        return $this->dbcontentpreview->undelete($id);
    }

    public function cmsadmin_dbcontentpreview_togglePublish($id) {
        return $this->dbcontentpreview->togglePublish($id);
    }

    public function cmsadmin_dbcontentpreview_publish($id,$task) {
        return $this->dbcontentpreview->publish($id,$task);
    }

    public function cmsadmin_dbcontentpreview_resetSection($sectionId) {
        return $this->dbcontentpreview->resetSection($sectionId);
    }

    public function cmsadmin_dbcontentpreview_unarchiveSection($sectionId) {
        return $this->dbcontentpreview->unarchiveSection($sectionId);
    }

    public function cmsadmin_dbcontentpreview_changeOrder($sectionid,$id,$ordering) {
        return $this->dbcontentpreview->changeOrder($sectionid,$id,$ordering);
    }

    public function cmsadmin_dbcontentpreview_getAll($filter) {
        return $this->dbcontentpreview->getAll($filter);
    }

    public function cmsadmin_dbcontentpreview_getRow($pk_field,$pk_value) {
        return $this->dbcontentpreview->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbcontentpreview_getArray($stmt) {
        return $this->dbcontentpreview->getArray($stmt);
    }

    public function cmsadmin_dbcontentpreview_insert($fields,$tablename) {
        return $this->dbcontentpreview->insert($fields,$tablename);
    }



    /*
     * =============== Logical Delete Data Methods =======dbcontentpreview_class_inc.php========
     */

    public function cmsadmin_dbcontentpreview_deleteContent($id) {
        return $this->dbcontentpreview->deleteContent($id);
    }



    /*
     * =============== Logical Add Data Methods =======superfishtree_class_inc.php========
     */

    public function cmsadmin_superfishtree_init() {
        return $this->superfishtree->init();
    }

    public function cmsadmin_superfishtree_getCMSAdminTree($current) {
        return $this->superfishtree->getCMSAdminTree($current);
    }

    public function cmsadmin_superfishtree_getCMSTree($current) {
        return $this->superfishtree->getCMSTree($current);
    }

    public function cmsadmin_superfishtree_show($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->superfishtree->show($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_superfishtree_showTree($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->superfishtree->showTree($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_superfishtree_buildTree($currentNodeId,$admin,$module,$sectionAction,$contentAction,$isChild) {
        return $this->superfishtree->buildTree($currentNodeId,$admin,$module,$sectionAction,$contentAction,$isChild);
    }

    public function cmsadmin_superfishtree_buildTree_old($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->superfishtree->buildTree_old($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_superfishtree_buildLevel($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->superfishtree->buildLevel($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_superfishtree_getOpenNodes($currentNode) {
        return $this->superfishtree->getOpenNodes($currentNode);
    }

    public function cmsadmin_superfishtree_addContent($id,$module,$sectionAction,$contentAction,$admin) {
        return $this->superfishtree->addContent($id,$module,$sectionAction,$contentAction,$admin);
    }

    public function cmsadmin_superfishtree_addChildren($id,$module,$sectionAction,$contentAction,$admin) {
        return $this->superfishtree->addChildren($id,$module,$sectionAction,$contentAction,$admin);
    }

    public function cmsadmin_superfishtree_hasChildNodes($parentId) {
        return $this->superfishtree->hasChildNodes($parentId);
    }

    public function cmsadmin_superfishtree_hasChildContent($parentId) {
        return $this->superfishtree->hasChildContent($parentId);
    }

    public function cmsadmin_superfishtree_hasChildSections($parentId) {
        return $this->superfishtree->hasChildSections($parentId);
    }

    public function cmsadmin_superfishtree_getChildNodes($parentId,$noPermissions) {
        return $this->superfishtree->getChildNodes($parentId,$noPermissions);
    }

    public function cmsadmin_superfishtree_getChildContent($parentId) {
        return $this->superfishtree->getChildContent($parentId);
    }

    public function cmsadmin_superfishtree_getNode($id,$noPermissions) {
        return $this->superfishtree->getNode($id,$noPermissions);
    }

    public function cmsadmin_superfishtree_getContent($sectionId,$admin) {
        return $this->superfishtree->getContent($sectionId,$admin);
    }

    public function cmsadmin_superfishtree_getChildNodeCount($parentId,$noPermissions) {
        return $this->superfishtree->getChildNodeCount($parentId,$noPermissions);
    }

    public function cmsadmin_superfishtree_getNodeContentCount($sectionId) {
        return $this->superfishtree->getNodeContentCount($sectionId);
    }

    public function cmsadmin_superfishtree_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->superfishtree->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_superfishtree_newObject($name,$moduleName) {
        return $this->superfishtree->newObject($name,$moduleName);
    }

    public function cmsadmin_superfishtree_appendArrayVar($name,$value) {
        return $this->superfishtree->appendArrayVar($name,$value);
    }



    /*
     * =============== Logical Add Data Methods =======rpcdbcmsadmin_class_inc.php========
     */

    public function cmsadmin_rpcdbcmsadmin_init() {
        return $this->rpcdbcmsadmin->init();
    }

    public function cmsadmin_rpcdbcmsadmin_getSections($params) {
        return $this->rpcdbcmsadmin->getSections($params);
    }

    public function cmsadmin_rpcdbcmsadmin_getFilteredSecs($params) {
        return $this->rpcdbcmsadmin->getFilteredSecs($params);
    }

    public function cmsadmin_rpcdbcmsadmin_getArcSections($params) {
        return $this->rpcdbcmsadmin->getArcSections($params);
    }

    public function cmsadmin_rpcdbcmsadmin_getSectionRootNodes($params) {
        return $this->rpcdbcmsadmin->getSectionRootNodes($params);
    }

    public function cmsadmin_rpcdbcmsadmin_getSectionId($params) {
        return $this->rpcdbcmsadmin->getSectionId($params);
    }

    public function cmsadmin_rpcdbcmsadmin_getFirstSectionId($params) {
        return $this->rpcdbcmsadmin->getFirstSectionId($params);
    }

    public function cmsadmin_rpcdbcmsadmin_addSec($params) {
        return $this->rpcdbcmsadmin->addSec($params);
    }

    public function cmsadmin_rpcdbcmsadmin_addPg($params) {
        return $this->rpcdbcmsadmin->addPg($params);
    }

    public function cmsadmin_rpcdbcmsadmin__changeTable($params) {
        return $this->rpcdbcmsadmin->_changeTable($params);
    }

    public function cmsadmin_rpcdbcmsadmin_getParam($name,$default) {
        return $this->rpcdbcmsadmin->getParam($name,$default);
    }

    public function cmsadmin_rpcdbcmsadmin_getObject($name,$moduleName) {
        return $this->rpcdbcmsadmin->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======dbhtmlblock_class_inc.php========
     */

    public function cmsadmin_dbhtmlblock_init() {
        return $this->dbhtmlblock->init();
    }

    public function cmsadmin_dbhtmlblock_updateBlock($id) {
        return $this->dbhtmlblock->updateBlock($id);
    }

    public function cmsadmin_dbhtmlblock_getBlock($contextCode) {
        return $this->dbhtmlblock->getBlock($contextCode);
    }

    public function cmsadmin_dbhtmlblock_displayBlock($contextCode) {
        return $this->dbhtmlblock->displayBlock($contextCode);
    }

    public function cmsadmin_dbhtmlblock_getArray($stmt) {
        return $this->dbhtmlblock->getArray($stmt);
    }

    public function cmsadmin_dbhtmlblock_insert($fields,$tablename) {
        return $this->dbhtmlblock->insert($fields,$tablename);
    }

    public function cmsadmin_dbhtmlblock_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbhtmlblock->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbhtmlblock_getParam($name,$default) {
        return $this->dbhtmlblock->getParam($name,$default);
    }

    public function cmsadmin_dbhtmlblock_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbhtmlblock->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbhtmlblock_getObject($name,$moduleName) {
        return $this->dbhtmlblock->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======dbsections_class_inc.php========
     */

    public function cmsadmin_dbsections_init() {
        return $this->dbsections->init();
    }

    public function cmsadmin_dbsections_getSections($isPublished,$filter) {
        return $this->dbsections->getSections($isPublished,$filter);
    }

    public function cmsadmin_dbsections_getFilteredSections($text,$publish) {
        return $this->dbsections->getFilteredSections($text,$publish);
    }

    public function cmsadmin_dbsections_getArchiveSections($filter) {
        return $this->dbsections->getArchiveSections($filter);
    }

    public function cmsadmin_dbsections_getRootNodes($isPublished,$contextcode) {
        return $this->dbsections->getRootNodes($isPublished,$contextcode);
    }

    public function cmsadmin_dbsections_getSection($id) {
        return $this->dbsections->getSection($id);
    }

    public function cmsadmin_dbsections_getParent($sectionId) {
        return $this->dbsections->getParent($sectionId);
    }

    public function cmsadmin_dbsections_getFirstSectionId($isPublished) {
        return $this->dbsections->getFirstSectionId($isPublished);
    }

    public function cmsadmin_dbsections_add($contextcode) {
        return $this->dbsections->add($contextcode);
    }

    public function cmsadmin_dbsections_addSection($title,$parentId,$menuText,$access,$description,$published,$layout,$showIntroduction,$showTitle,$showAuthor,$showDate,$pageNum,$customNum,$pageOrder,$imageUrl,$contextCode) {
        return $this->dbsections->addSection($title,$parentId,$menuText,$access,$description,$published,$layout,$showIntroduction,$showTitle,$showAuthor,$showDate,$pageNum,$customNum,$pageOrder,$imageUrl,$contextCode);
    }

    public function cmsadmin_dbsections_checkindex($title,$parentId,$menuText,$access,$description,$published,$layout,$showIntroduction,$showTitle,$showAuthor,$showDate,$pageNum,$customNum,$pageOrder,$imageUrl,$contextCode) {
        return $this->dbsections->checkindex($title,$parentId,$menuText,$access,$description,$published,$layout,$showIntroduction,$showTitle,$showAuthor,$showDate,$pageNum,$customNum,$pageOrder,$imageUrl,$contextCode);
    }

    public function cmsadmin_dbsections_addNewSection($parent,$title,$menuText,$access,$description,$published,$layout,$showdate,$showintroduction,$numpagedisplay,$ordertype,$contextCode) {
        return $this->dbsections->addNewSection($parent,$title,$menuText,$access,$description,$published,$layout,$showdate,$showintroduction,$numpagedisplay,$ordertype,$contextCode);
    }

    public function cmsadmin_dbsections_getMenuText($id) {
        return $this->dbsections->getMenuText($id);
    }

    public function cmsadmin_dbsections_getSectionByContextCode() {
        return $this->dbsections->getSectionByContextCode();
    }

    public function cmsadmin_dbsections_getJSONSectionChildren($sectionid) {
        return $this->dbsections->getJSONSectionChildren($sectionid);
    }

    public function cmsadmin_dbsections_getAll($filter) {
        return $this->dbsections->getAll($filter);
    }

    public function cmsadmin_dbsections_getRow($pk_field,$pk_value) {
        return $this->dbsections->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbsections_getArray($stmt) {
        return $this->dbsections->getArray($stmt);
    }

    public function cmsadmin_dbsections_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbsections->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbsections_query($stmt) {
        return $this->dbsections->query($stmt);
    }

    public function cmsadmin_dbsections_getParam($name,$default) {
        return $this->dbsections->getParam($name,$default);
    }

    public function cmsadmin_dbsections_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbsections->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbsections_newObject($name,$moduleName) {
        return $this->dbsections->newObject($name,$moduleName);
    }

    public function cmsadmin_dbsections_getObject($name,$moduleName) {
        return $this->dbsections->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbsections_class_inc.php========
     */

    public function cmsadmin_dbsections_edit() {
        return $this->dbsections->edit();
    }

    public function cmsadmin_dbsections_editSection($id,$parentId,$title,$menuText,$access,$description,$published,$layout,$showIntroduction,$showTitle,$showAuthor,$showDate,$pageNum,$customNum,$pageOrder,$imageUrl,$contextCode) {
        return $this->dbsections->editSection($id,$parentId,$title,$menuText,$access,$description,$published,$layout,$showIntroduction,$showTitle,$showAuthor,$showDate,$pageNum,$customNum,$pageOrder,$imageUrl,$contextCode);
    }

    public function cmsadmin_dbsections_togglePublish($id) {
        return $this->dbsections->togglePublish($id);
    }

    public function cmsadmin_dbsections_publish($id,$task) {
        return $this->dbsections->publish($id,$task);
    }

    public function cmsadmin_dbsections_archive($id) {
        return $this->dbsections->archive($id);
    }

    public function cmsadmin_dbsections_changeOrder($id,$ordering,$parentid) {
        return $this->dbsections->changeOrder($id,$ordering,$parentid);
    }

    public function cmsadmin_dbsections_reorderSections($id,$ordering,$parentid) {
        return $this->dbsections->reorderSections($id,$ordering,$parentid);
    }

    public function cmsadmin_dbsections_insert($fields,$tablename) {
        return $this->dbsections->insert($fields,$tablename);
    }

    public function cmsadmin_dbsections_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbsections->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbsections_now() {
        return $this->dbsections->now();
    }



    /*
     * =============== Logical Delete Data Methods =======dbsections_class_inc.php========
     */

    public function cmsadmin_dbsections_permanentlyDelete($id) {
        return $this->dbsections->permanentlyDelete($id);
    }





    /*
     * =============== Logical Add Data Methods =======contenttree_class_inc.php========
     */

    public function cmsadmin_contenttree_init() {
        return $this->contenttree->init();
    }

    public function cmsadmin_contenttree_show($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->contenttree->show($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_contenttree_buildTree($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->contenttree->buildTree($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_contenttree_buildLevel($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->contenttree->buildLevel($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_contenttree_getOpenNodes($currentNode) {
        return $this->contenttree->getOpenNodes($currentNode);
    }

    public function cmsadmin_contenttree_addContent($id,$module,$action,$admin) {
        return $this->contenttree->addContent($id,$module,$action,$admin);
    }

    public function cmsadmin_contenttree_addChildren($id,$module,$action,$admin) {
        return $this->contenttree->addChildren($id,$module,$action,$admin);
    }

    public function cmsadmin_contenttree_getChildNodes($parentId,$noPermissions) {
        return $this->contenttree->getChildNodes($parentId,$noPermissions);
    }

    public function cmsadmin_contenttree_getNode($id,$noPermissions) {
        return $this->contenttree->getNode($id,$noPermissions);
    }

    public function cmsadmin_contenttree_getContent($sectionId,$admin) {
        return $this->contenttree->getContent($sectionId,$admin);
    }

    public function cmsadmin_contenttree_getChildNodeCount($parentId,$noPermissions) {
        return $this->contenttree->getChildNodeCount($parentId,$noPermissions);
    }

    public function cmsadmin_contenttree_getNodeContentCount($sectionId) {
        return $this->contenttree->getNodeContentCount($sectionId);
    }

    public function cmsadmin_contenttree_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->contenttree->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_contenttree_newObject($name,$moduleName) {
        return $this->contenttree->newObject($name,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======articlebox_class_inc.php========
     */

    public function cmsadmin_articlebox_init() {
        return $this->articlebox->init();
    }

    public function cmsadmin_articlebox_show($content) {
        return $this->articlebox->show($content);
    }

    public function cmsadmin_articlebox_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->articlebox->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }



    /*
     * =============== Logical Add Data Methods =======simplecontenttree_class_inc.php========
     */

    public function cmsadmin_simplecontenttree_init() {
        return $this->simplecontenttree->init();
    }

    public function cmsadmin_simplecontenttree_getSimpleCMSAdminTree($current) {
        return $this->simplecontenttree->getSimpleCMSAdminTree($current);
    }

    public function cmsadmin_simplecontenttree_getCMSAdminTree($current) {
        return $this->simplecontenttree->getCMSAdminTree($current);
    }

    public function cmsadmin_simplecontenttree_getMenuChildNodes($sectionid) {
        return $this->simplecontenttree->getMenuChildNodes($sectionid);
    }

    public function cmsadmin_simplecontenttree_show($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->simplecontenttree->show($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_simplecontenttree_buildTreePart($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->simplecontenttree->buildTreePart($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_simplecontenttree_buildLevelPart($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->simplecontenttree->buildLevelPart($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_simplecontenttree_buildTree($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->simplecontenttree->buildTree($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_simplecontenttree_buildLevel($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->simplecontenttree->buildLevel($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_simplecontenttree_getOpenNodes($currentNode) {
        return $this->simplecontenttree->getOpenNodes($currentNode);
    }

    public function cmsadmin_simplecontenttree_addNextAjaxContent($id,$module,$action,$admin,$call) {
        return $this->simplecontenttree->addNextAjaxContent($id,$module,$action,$admin,$call);
    }

    public function cmsadmin_simplecontenttree_addNextContent($id,$module,$action,$admin,$call) {
        return $this->simplecontenttree->addNextContent($id,$module,$action,$admin,$call);
    }

    public function cmsadmin_simplecontenttree_addContent($id,$module,$action,$admin) {
        return $this->simplecontenttree->addContent($id,$module,$action,$admin);
    }

    public function cmsadmin_simplecontenttree_addNextAjaxChildren($id,$module,$action,$admin) {
        return $this->simplecontenttree->addNextAjaxChildren($id,$module,$action,$admin);
    }

    public function cmsadmin_simplecontenttree_addNextChildren($id,$module,$action,$admin) {
        return $this->simplecontenttree->addNextChildren($id,$module,$action,$admin);
    }

    public function cmsadmin_simplecontenttree_addChildren($id,$module,$action,$admin) {
        return $this->simplecontenttree->addChildren($id,$module,$action,$admin);
    }

    public function cmsadmin_simplecontenttree_hasChildNodes($parentId) {
        return $this->simplecontenttree->hasChildNodes($parentId);
    }

    public function cmsadmin_simplecontenttree_getChildNodes($parentId,$noPermissions) {
        return $this->simplecontenttree->getChildNodes($parentId,$noPermissions);
    }

    public function cmsadmin_simplecontenttree_getNode($id,$noPermissions) {
        return $this->simplecontenttree->getNode($id,$noPermissions);
    }

    public function cmsadmin_simplecontenttree_getContent($sectionId,$admin) {
        return $this->simplecontenttree->getContent($sectionId,$admin);
    }

    public function cmsadmin_simplecontenttree_getChildNodeCount($parentId,$noPermissions) {
        return $this->simplecontenttree->getChildNodeCount($parentId,$noPermissions);
    }

    public function cmsadmin_simplecontenttree_getNodeContentCount($sectionId) {
        return $this->simplecontenttree->getNodeContentCount($sectionId);
    }

    public function cmsadmin_simplecontenttree_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->simplecontenttree->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_simplecontenttree_newObject($name,$moduleName) {
        return $this->simplecontenttree->newObject($name,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======dbmenustyles_class_inc.php========
     */

    public function cmsadmin_dbMenuStyles_init() {
        return $this->dbMenuStyles->init();
    }

    public function cmsadmin_dbMenuStyles_getActive() {
        return $this->dbMenuStyles->getActive();
    }

    public function cmsadmin_dbMenuStyles_getStyles() {
        return $this->dbMenuStyles->getStyles();
    }

    public function cmsadmin_dbMenuStyles_getArray($stmt) {
        return $this->dbMenuStyles->getArray($stmt);
    }

    public function cmsadmin_dbMenuStyles_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbMenuStyles->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbMenuStyles_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbMenuStyles->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }



    /*
     * =============== Logical Edit Data Methods =======dbmenustyles_class_inc.php========
     */

    public function cmsadmin_dbMenuStyles_updateActive($id) {
        return $this->dbMenuStyles->updateActive($id);
    }



    /*
     * =============== Logical Add Data Methods =======dbsecurity_class_inc.php========
     */

    public function cmsadmin_dbsecurity_init() {
        return $this->dbsecurity->init();
    }

    public function cmsadmin_dbsecurity_getContentRow($contentid) {
        return $this->dbsecurity->getContentRow($contentid);
    }

    public function cmsadmin_dbsecurity_getSectionRow($sectionid) {
        return $this->dbsecurity->getSectionRow($sectionid);
    }

    public function cmsadmin_dbsecurity_setContentOwnerPropagate($sectionid,$userid) {
        return $this->dbsecurity->setContentOwnerPropagate($sectionid,$userid);
    }

    public function cmsadmin_dbsecurity_setOwnerPropagate($sectionid,$userid) {
        return $this->dbsecurity->setOwnerPropagate($sectionid,$userid);
    }

    public function cmsadmin_dbsecurity_canUserWriteContent($contentid,$userid) {
        return $this->dbsecurity->canUserWriteContent($contentid,$userid);
    }

    public function cmsadmin_dbsecurity_canUserReadContent($contentid,$userid) {
        return $this->dbsecurity->canUserReadContent($contentid,$userid);
    }

    public function cmsadmin_dbsecurity_canUserWriteSection($sectionid,$userid) {
        return $this->dbsecurity->canUserWriteSection($sectionid,$userid);
    }

    public function cmsadmin_dbsecurity_canUserReadSection($sectionid,$userid) {
        return $this->dbsecurity->canUserReadSection($sectionid,$userid);
    }

    public function cmsadmin_dbsecurity_getPagesInSection($sectionId,$isPublished) {
        return $this->dbsecurity->getPagesInSection($sectionId,$isPublished);
    }

    public function cmsadmin_dbsecurity_setContentPermissionsUserPropagate($sectionid,$userid,$read_access,$write_access) {
        return $this->dbsecurity->setContentPermissionsUserPropagate($sectionid,$userid,$read_access,$write_access);
    }

    public function cmsadmin_dbsecurity_setContentPermissionsGroupPropagate($sectionid,$groupid,$read_access,$write_access) {
        return $this->dbsecurity->setContentPermissionsGroupPropagate($sectionid,$groupid,$read_access,$write_access);
    }

    public function cmsadmin_dbsecurity_setPermissionsUserPropagate($sectionid,$userid,$read_access,$write_access) {
        return $this->dbsecurity->setPermissionsUserPropagate($sectionid,$userid,$read_access,$write_access);
    }

    public function cmsadmin_dbsecurity_deletePermissionsUserPropagate($sectionid) {
        return $this->dbsecurity->deletePermissionsUserPropagate($sectionid);
    }

    public function cmsadmin_dbsecurity_deleteContentPermissionsUserPropagate($sectionid) {
        return $this->dbsecurity->deleteContentPermissionsUserPropagate($sectionid);
    }

    public function cmsadmin_dbsecurity_deleteContentPermissionsGroupPropagate($sectionid) {
        return $this->dbsecurity->deleteContentPermissionsGroupPropagate($sectionid);
    }

    public function cmsadmin_dbsecurity_deletePermissionsGroupPropagate($sectionid) {
        return $this->dbsecurity->deletePermissionsGroupPropagate($sectionid);
    }

    public function cmsadmin_dbsecurity_setPermissionsGroupPropagate($sectionid,$groupid,$read_access,$write_access) {
        return $this->dbsecurity->setPermissionsGroupPropagate($sectionid,$groupid,$read_access,$write_access);
    }

    public function cmsadmin_dbsecurity_hasNodes($id) {
        return $this->dbsecurity->hasNodes($id);
    }

    public function cmsadmin_dbsecurity_getSubSectionsInSection($sectionId,$order,$isPublished) {
        return $this->dbsecurity->getSubSectionsInSection($sectionId,$order,$isPublished);
    }

    public function cmsadmin_dbsecurity_getAuthorizedSectionMembers($sectionid) {
        return $this->dbsecurity->getAuthorizedSectionMembers($sectionid);
    }

    public function cmsadmin_dbsecurity_getAuthorizedContentMembers($contentid) {
        return $this->dbsecurity->getAuthorizedContentMembers($contentid);
    }

    public function cmsadmin_dbsecurity_getAssignedSectionUsers($sectionid) {
        return $this->dbsecurity->getAssignedSectionUsers($sectionid);
    }

    public function cmsadmin_dbsecurity_getAssignedSectionGroups($sectionid) {
        return $this->dbsecurity->getAssignedSectionGroups($sectionid);
    }

    public function cmsadmin_dbsecurity_getAssignedContentUsers($contentid) {
        return $this->dbsecurity->getAssignedContentUsers($contentid);
    }

    public function cmsadmin_dbsecurity_getAssignedContentGroups($contentid) {
        return $this->dbsecurity->getAssignedContentGroups($contentid);
    }

    public function cmsadmin_dbsecurity_getAllUsers() {
        return $this->dbsecurity->getAllUsers();
    }

    public function cmsadmin_dbsecurity_getUnAuthorizedSectionMembers($sectionid) {
        return $this->dbsecurity->getUnAuthorizedSectionMembers($sectionid);
    }

    public function cmsadmin_dbsecurity_getUnAuthorizedContentMembers($contentid) {
        return $this->dbsecurity->getUnAuthorizedContentMembers($contentid);
    }

    public function cmsadmin_dbsecurity_getSectionUserMembersField($rows,$field) {
        return $this->dbsecurity->getSectionUserMembersField($rows,$field);
    }

    public function cmsadmin_dbsecurity_getSectionGroupMembersField($rows,$field) {
        return $this->dbsecurity->getSectionGroupMembersField($rows,$field);
    }

    public function cmsadmin_dbsecurity_addContentPermissionsUser($contentid,$userid,$read_access,$write_access,$do_update) {
        return $this->dbsecurity->addContentPermissionsUser($contentid,$userid,$read_access,$write_access,$do_update);
    }

    public function cmsadmin_dbsecurity_addContentPermissionsGroup($contentid,$groupid,$read_access,$write_access,$do_update) {
        return $this->dbsecurity->addContentPermissionsGroup($contentid,$groupid,$read_access,$write_access,$do_update);
    }

    public function cmsadmin_dbsecurity_addSectionPermissionsUser($sectionid,$userid,$read_access,$write_access,$do_update) {
        return $this->dbsecurity->addSectionPermissionsUser($sectionid,$userid,$read_access,$write_access,$do_update);
    }

    public function cmsadmin_dbsecurity_addSectionPermissionsGroup($sectionid,$groupid,$read_access,$write_access,$do_update) {
        return $this->dbsecurity->addSectionPermissionsGroup($sectionid,$groupid,$read_access,$write_access,$do_update);
    }

    public function cmsadmin_dbsecurity_deleteAllSectionPermissionsUser($sectionid) {
        return $this->dbsecurity->deleteAllSectionPermissionsUser($sectionid);
    }

    public function cmsadmin_dbsecurity_deleteAllSectionPermissionsGroup($sectionid) {
        return $this->dbsecurity->deleteAllSectionPermissionsGroup($sectionid);
    }

    public function cmsadmin_dbsecurity_deleteAllContentPermissionsUser($contentid) {
        return $this->dbsecurity->deleteAllContentPermissionsUser($contentid);
    }

    public function cmsadmin_dbsecurity_deleteAllContentPermissionsGroup($contentid) {
        return $this->dbsecurity->deleteAllContentPermissionsGroup($contentid);
    }

    public function cmsadmin_dbsecurity_inheritContentPermissions($contentid) {
        return $this->dbsecurity->inheritContentPermissions($contentid);
    }

    public function cmsadmin_dbsecurity_inheritSectionPermissions($sectionid) {
        return $this->dbsecurity->inheritSectionPermissions($sectionid);
    }

    public function cmsadmin_dbsecurity_setSectionPermissionsPublicAccessPropagate($sectionid,$public_access) {
        return $this->dbsecurity->setSectionPermissionsPublicAccessPropagate($sectionid,$public_access);
    }

    public function cmsadmin_dbsecurity_isSectionPublic($sectionid) {
        return $this->dbsecurity->isSectionPublic($sectionid);
    }

    public function cmsadmin_dbsecurity_isContentPublic($contentid) {
        return $this->dbsecurity->isContentPublic($contentid);
    }

    public function cmsadmin_dbsecurity_setSectionPublicAccess($id,$task) {
        return $this->dbsecurity->setSectionPublicAccess($id,$task);
    }

    public function cmsadmin_dbsecurity_setContentPublicAccess($id,$task) {
        return $this->dbsecurity->setContentPublicAccess($id,$task);
    }

    public function cmsadmin_dbsecurity_getAll($filter) {
        return $this->dbsecurity->getAll($filter);
    }

    public function cmsadmin_dbsecurity_getRow($pk_field,$pk_value) {
        return $this->dbsecurity->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbsecurity_getArray($stmt) {
        return $this->dbsecurity->getArray($stmt);
    }

    public function cmsadmin_dbsecurity_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbsecurity->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbsecurity_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbsecurity->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbsecurity_getLastInsertId() {
        return $this->dbsecurity->getLastInsertId();
    }

    public function cmsadmin_dbsecurity_query($stmt) {
        return $this->dbsecurity->query($stmt);
    }

    public function cmsadmin_dbsecurity_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbsecurity->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbsecurity_newObject($name,$moduleName) {
        return $this->dbsecurity->newObject($name,$moduleName);
    }

    public function cmsadmin_dbsecurity_getObject($name,$moduleName) {
        return $this->dbsecurity->getObject($name,$moduleName);
    }

    public function cmsadmin_dbsecurity_setVar($name,$value) {
        return $this->dbsecurity->setVar($name,$value);
    }



    /*
     * =============== Logical Edit Data Methods =======dbsecurity_class_inc.php========
     */

    public function cmsadmin_dbsecurity_setOwner($sectionid,$userid) {
        return $this->dbsecurity->setOwner($sectionid,$userid);
    }

    public function cmsadmin_dbsecurity_setPermissionsUser($sectionid,$userid,$read_access,$write_access) {
        return $this->dbsecurity->setPermissionsUser($sectionid,$userid,$read_access,$write_access);
    }

    public function cmsadmin_dbsecurity_setPermissionsGroup($sectionid,$groupid,$read_access,$write_access) {
        return $this->dbsecurity->setPermissionsGroup($sectionid,$groupid,$read_access,$write_access);
    }

    public function cmsadmin_dbsecurity_setContentOwner($contentid,$userid) {
        return $this->dbsecurity->setContentOwner($contentid,$userid);
    }

    public function cmsadmin_dbsecurity_setContentPermissionsUser($contentid,$userid,$read_access,$write_access) {
        return $this->dbsecurity->setContentPermissionsUser($contentid,$userid,$read_access,$write_access);
    }

    public function cmsadmin_dbsecurity_setContentPermissionsGroup($contentid,$groupid,$read_access,$write_access) {
        return $this->dbsecurity->setContentPermissionsGroup($contentid,$groupid,$read_access,$write_access);
    }

    public function cmsadmin_dbsecurity_setSectionPermissionsPublicAccess($sectionid,$public_access) {
        return $this->dbsecurity->setSectionPermissionsPublicAccess($sectionid,$public_access);
    }

    public function cmsadmin_dbsecurity_setContentPermissionsPublicAccess($contentid,$public_access) {
        return $this->dbsecurity->setContentPermissionsPublicAccess($contentid,$public_access);
    }

    public function cmsadmin_dbsecurity_insert($fields,$tablename) {
        return $this->dbsecurity->insert($fields,$tablename);
    }



    /*
     * =============== Logical Delete Data Methods =======dbsecurity_class_inc.php========
     */

    public function cmsadmin_dbsecurity_deleteContentPermissionsUser($contentid,$userid) {
        return $this->dbsecurity->deleteContentPermissionsUser($contentid,$userid);
    }

    public function cmsadmin_dbsecurity_deleteContentPermissionsGroup($contentid,$groupid) {
        return $this->dbsecurity->deleteContentPermissionsGroup($contentid,$groupid);
    }

    public function cmsadmin_dbsecurity_deleteSectionPermissionsUser($sectionid,$userid) {
        return $this->dbsecurity->deleteSectionPermissionsUser($sectionid,$userid);
    }

    public function cmsadmin_dbsecurity_deleteSectionPermissionsGroup($sectionid,$groupid) {
        return $this->dbsecurity->deleteSectionPermissionsGroup($sectionid,$groupid);
    }



    /*
     * =============== Logical Add Data Methods =======pagemenu_class_inc.php========
     */

    public function cmsadmin_pagemenu_init() {
        return $this->pagemenu->init();
    }

    public function cmsadmin_pagemenu_show($state,$sectionid,$contentid) {
        return $this->pagemenu->show($state,$sectionid,$contentid);
    }

    public function cmsadmin_pagemenu_getHomeLink() {
        return $this->pagemenu->getHomeLink();
    }

    public function cmsadmin_pagemenu_getMainMenu() {
        return $this->pagemenu->getMainMenu();
    }

    public function cmsadmin_pagemenu_getAboutMenu() {
        return $this->pagemenu->getAboutMenu();
    }

    public function cmsadmin_pagemenu_getAcademicMenu() {
        return $this->pagemenu->getAcademicMenu();
    }

    public function cmsadmin_pagemenu_getStudentMenu() {
        return $this->pagemenu->getStudentMenu();
    }

    public function cmsadmin_pagemenu_getStudentInternationalMenu() {
        return $this->pagemenu->getStudentInternationalMenu();
    }

    public function cmsadmin_pagemenu_getStudentPartTimeMenu() {
        return $this->pagemenu->getStudentPartTimeMenu();
    }

    public function cmsadmin_pagemenu_getStudentPostgraduateMenu() {
        return $this->pagemenu->getStudentPostgraduateMenu();
    }

    public function cmsadmin_pagemenu_getStudentUndergraduateMenu() {
        return $this->pagemenu->getStudentUndergraduateMenu();
    }

    public function cmsadmin_pagemenu_getResearchMenu() {
        return $this->pagemenu->getResearchMenu();
    }

    public function cmsadmin_pagemenu_getCommunityMenu() {
        return $this->pagemenu->getCommunityMenu();
    }

    public function cmsadmin_pagemenu_getAdminSupportMenu() {
        return $this->pagemenu->getAdminSupportMenu();
    }

    public function cmsadmin_pagemenu_getStudentFacultyDeptXhosaMenu() {
        return $this->pagemenu->getStudentFacultyDeptXhosaMenu();
    }

    public function cmsadmin_pagemenu_getParam($name,$default) {
        return $this->pagemenu->getParam($name,$default);
    }

    public function cmsadmin_pagemenu_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->pagemenu->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_pagemenu_newObject($name,$moduleName) {
        return $this->pagemenu->newObject($name,$moduleName);
    }

    public function cmsadmin_pagemenu_getObject($name,$moduleName) {
        return $this->pagemenu->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======simpletreemenu_class_inc.php========
     */

    public function cmsadmin_simpletreemenu_init() {
        return $this->simpletreemenu->init();
    }

    public function cmsadmin_simpletreemenu_getCMSAdminTree($current) {
        return $this->simpletreemenu->getCMSAdminTree($current);
    }

    public function cmsadmin_simpletreemenu_getCMSTree($current) {
        return $this->simpletreemenu->getCMSTree($current);
    }

    public function cmsadmin_simpletreemenu_show($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->simpletreemenu->show($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_simpletreemenu_showTree($currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->simpletreemenu->showTree($currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_simpletreemenu_buildTree($currentNodeId,$admin,$module,$sectionAction,$contentAction,$isChild) {
        return $this->simpletreemenu->buildTree($currentNodeId,$admin,$module,$sectionAction,$contentAction,$isChild);
    }

    public function cmsadmin_simpletreemenu_buildLevel($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction) {
        return $this->simpletreemenu->buildLevel($parentId,$currentNode,$admin,$module,$sectionAction,$contentAction);
    }

    public function cmsadmin_simpletreemenu_getOpenNodes($currentNode) {
        return $this->simpletreemenu->getOpenNodes($currentNode);
    }

    public function cmsadmin_simpletreemenu_addContent($id,$module,$sectionAction,$contentAction,$admin) {
        return $this->simpletreemenu->addContent($id,$module,$sectionAction,$contentAction,$admin);
    }

    public function cmsadmin_simpletreemenu_addChildren($id,$module,$sectionAction,$contentAction,$admin) {
        return $this->simpletreemenu->addChildren($id,$module,$sectionAction,$contentAction,$admin);
    }

    public function cmsadmin_simpletreemenu_hasChildNodes($parentId) {
        return $this->simpletreemenu->hasChildNodes($parentId);
    }

    public function cmsadmin_simpletreemenu_hasChildContent($parentId) {
        return $this->simpletreemenu->hasChildContent($parentId);
    }

    public function cmsadmin_simpletreemenu_hasChildSections($parentId) {
        return $this->simpletreemenu->hasChildSections($parentId);
    }

    public function cmsadmin_simpletreemenu_getChildNodes($parentId,$noPermissions) {
        return $this->simpletreemenu->getChildNodes($parentId,$noPermissions);
    }

    public function cmsadmin_simpletreemenu_getChildContent($parentId) {
        return $this->simpletreemenu->getChildContent($parentId);
    }

    public function cmsadmin_simpletreemenu_getNode($id,$noPermissions) {
        return $this->simpletreemenu->getNode($id,$noPermissions);
    }

    public function cmsadmin_simpletreemenu_getContent($sectionId,$admin) {
        return $this->simpletreemenu->getContent($sectionId,$admin);
    }

    public function cmsadmin_simpletreemenu_getChildNodeCount($parentId,$noPermissions) {
        return $this->simpletreemenu->getChildNodeCount($parentId,$noPermissions);
    }

    public function cmsadmin_simpletreemenu_getNodeContentCount($sectionId) {
        return $this->simpletreemenu->getNodeContentCount($sectionId);
    }

    public function cmsadmin_simpletreemenu_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->simpletreemenu->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_simpletreemenu_newObject($name,$moduleName) {
        return $this->simpletreemenu->newObject($name,$moduleName);
    }



    /*
     * =============== Logical Add Data Methods =======dbpagemenu_class_inc.php========
     */

    public function cmsadmin_dbpagemenu_init() {
        return $this->dbpagemenu->init();
    }

    public function cmsadmin_dbpagemenu_getMenuText($menuKey) {
        return $this->dbpagemenu->getMenuText($menuKey);
    }

    public function cmsadmin_dbpagemenu_getMenuRow($menuId) {
        return $this->dbpagemenu->getMenuRow($menuId);
    }

    public function cmsadmin_dbpagemenu_getMenuRowByKey($key) {
        return $this->dbpagemenu->getMenuRowByKey($key);
    }

    public function cmsadmin_dbpagemenu_hasDefaultMenu() {
        return $this->dbpagemenu->hasDefaultMenu();
    }

    public function cmsadmin_dbpagemenu_addMenu() {
        return $this->dbpagemenu->addMenu();
    }

    public function cmsadmin_dbpagemenu_getContentPages($filter) {
        return $this->dbpagemenu->getContentPages($filter);
    }

    public function cmsadmin_dbpagemenu_getArchivePages($filter) {
        return $this->dbpagemenu->getArchivePages($filter);
    }

    public function cmsadmin_dbpagemenu_getContentPage($id) {
        return $this->dbpagemenu->getContentPage($id);
    }

    public function cmsadmin_dbpagemenu_getPagesInSection($sectionId,$isPublished) {
        return $this->dbpagemenu->getPagesInSection($sectionId,$isPublished);
    }

    public function cmsadmin_dbpagemenu_getPagesInSectionJoinFront($sectionId) {
        return $this->dbpagemenu->getPagesInSectionJoinFront($sectionId);
    }

    public function cmsadmin_dbpagemenu_getTitles($title,$limit) {
        return $this->dbpagemenu->getTitles($title,$limit);
    }

    public function cmsadmin_dbpagemenu_getLatestTitles($n) {
        return $this->dbpagemenu->getLatestTitles($n);
    }

    public function cmsadmin_dbpagemenu_getNumberOfPagesInSection($sectionId) {
        return $this->dbpagemenu->getNumberOfPagesInSection($sectionId);
    }

    public function cmsadmin_dbpagemenu_getPageOrder($pageId) {
        return $this->dbpagemenu->getPageOrder($pageId);
    }

    public function cmsadmin_dbpagemenu_getOrdering($sectionId) {
        return $this->dbpagemenu->getOrdering($sectionId);
    }

    public function cmsadmin_dbpagemenu_getOrderingLink($sectionid,$id) {
        return $this->dbpagemenu->getOrderingLink($sectionid,$id);
    }

    public function cmsadmin_dbpagemenu_html2txt($document,$scrub) {
        return $this->dbpagemenu->html2txt($document,$scrub);
    }

    public function cmsadmin_dbpagemenu_luceneIndex($data) {
        return $this->dbpagemenu->luceneIndex($data);
    }

    public function cmsadmin_dbpagemenu_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbpagemenu->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbpagemenu_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbpagemenu->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbpagemenu_join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom) {
        return $this->dbpagemenu->join($sqlJoinType,$tblJoinTo,$join,$tblJoinFrom);
    }

    public function cmsadmin_dbpagemenu_now() {
        return $this->dbpagemenu->now();
    }

    public function cmsadmin_dbpagemenu_getParam($name,$default) {
        return $this->dbpagemenu->getParam($name,$default);
    }

    public function cmsadmin_dbpagemenu_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbpagemenu->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbpagemenu_newObject($name,$moduleName) {
        return $this->dbpagemenu->newObject($name,$moduleName);
    }

    public function cmsadmin_dbpagemenu_getObject($name,$moduleName) {
        return $this->dbpagemenu->getObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbpagemenu_class_inc.php========
     */

    public function cmsadmin_dbpagemenu_trashMenu($id) {
        return $this->dbpagemenu->trashMenu($id);
    }

    public function cmsadmin_dbpagemenu_undelete($id) {
        return $this->dbpagemenu->undelete($id);
    }

    public function cmsadmin_dbpagemenu_togglePublish($id) {
        return $this->dbpagemenu->togglePublish($id);
    }

    public function cmsadmin_dbpagemenu_publish($id,$task) {
        return $this->dbpagemenu->publish($id,$task);
    }

    public function cmsadmin_dbpagemenu_resetSection($sectionId) {
        return $this->dbpagemenu->resetSection($sectionId);
    }

    public function cmsadmin_dbpagemenu_unarchiveSection($sectionId) {
        return $this->dbpagemenu->unarchiveSection($sectionId);
    }

    public function cmsadmin_dbpagemenu_changeOrder($sectionid,$id,$ordering) {
        return $this->dbpagemenu->changeOrder($sectionid,$id,$ordering);
    }

    public function cmsadmin_dbpagemenu_getAll($filter) {
        return $this->dbpagemenu->getAll($filter);
    }

    public function cmsadmin_dbpagemenu_getRow($pk_field,$pk_value) {
        return $this->dbpagemenu->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbpagemenu_getArray($stmt) {
        return $this->dbpagemenu->getArray($stmt);
    }

    public function cmsadmin_dbpagemenu_insert($fields,$tablename) {
        return $this->dbpagemenu->insert($fields,$tablename);
    }



    /*
     * =============== Logical Delete Data Methods =======dbpagemenu_class_inc.php========
     */

    public function cmsadmin_dbpagemenu_deleteMenu($id) {
        return $this->dbpagemenu->deleteMenu($id);
    }



    /*
     * =============== Logical Add Data Methods =======dbcategories_class_inc.php========
     */

    public function cmsadmin_dbcategories_init() {
        return $this->dbcategories->init();
    }

    public function cmsadmin_dbcategories_getCategories() {
        return $this->dbcategories->getCategories();
    }

    public function cmsadmin_dbcategories_getCatCount($sectionId) {
        return $this->dbcategories->getCatCount($sectionId);
    }

    public function cmsadmin_dbcategories_add() {
        return $this->dbcategories->add();
    }

    public function cmsadmin_dbcategories_getMenuText($id) {
        return $this->dbcategories->getMenuText($id);
    }

    public function cmsadmin_dbcategories_getCategory($id) {
        return $this->dbcategories->getCategory($id);
    }

    public function cmsadmin_dbcategories_getCategoryInSection($sectionId,$level) {
        return $this->dbcategories->getCategoryInSection($sectionId,$level);
    }

    public function cmsadmin_dbcategories_getCatLevel($id) {
        return $this->dbcategories->getCatLevel($id);
    }

    public function cmsadmin_dbcategories_getSectionIdOfCat($id) {
        return $this->dbcategories->getSectionIdOfCat($id);
    }

    public function cmsadmin_dbcategories_hasNodes($id) {
        return $this->dbcategories->hasNodes($id);
    }

    public function cmsadmin_dbcategories_getAll($filter) {
        return $this->dbcategories->getAll($filter);
    }

    public function cmsadmin_dbcategories_getRow($pk_field,$pk_value) {
        return $this->dbcategories->getRow($pk_field,$pk_value);
    }

    public function cmsadmin_dbcategories_getRecordCount($filter) {
        return $this->dbcategories->getRecordCount($filter);
    }

    public function cmsadmin_dbcategories_insert($fields,$tablename) {
        return $this->dbcategories->insert($fields,$tablename);
    }

    public function cmsadmin_dbcategories_update($pkfield,$pkvalue,$fields,$tablename) {
        return $this->dbcategories->update($pkfield,$pkvalue,$fields,$tablename);
    }

    public function cmsadmin_dbcategories_delete($pkfield,$pkvalue,$tablename) {
        return $this->dbcategories->delete($pkfield,$pkvalue,$tablename);
    }

    public function cmsadmin_dbcategories_getParam($name,$default) {
        return $this->dbcategories->getParam($name,$default);
    }

    public function cmsadmin_dbcategories_uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility) {
        return $this->dbcategories->uri($params,$moduleName,$uriMode,$omitServerName,$javascriptCompatibility);
    }

    public function cmsadmin_dbcategories_newObject($name,$moduleName) {
        return $this->dbcategories->newObject($name,$moduleName);
    }



    /*
     * =============== Logical Edit Data Methods =======dbcategories_class_inc.php========
     */

    public function cmsadmin_dbcategories_edit() {
        return $this->dbcategories->edit();
    }



    /*
     * =============== Logical Delete Data Methods =======dbcategories_class_inc.php========
     */

    public function cmsadmin_dbcategories_deleteCat($id) {
        return $this->dbcategories->deleteCat($id);
    }

}
?>
