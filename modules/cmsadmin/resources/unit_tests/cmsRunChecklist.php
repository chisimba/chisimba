<?PHP
require_once 'cmsChecklist.php';

$cl = new cmsChecklist;

//echo "\n\n UserId : [".$cl->userId()."]\n\n\n";

echo "Checking Dependancies: -------------------\n";
echo "Blockalicious Module Registered? : ";
echo $cl->isBlockaliciousModuleInstalled();
echo "\n";

echo "Feed Module Registered? : ";
echo $cl->isFeedModuleInstalled();
echo "\n";

echo "FileManager Module Registered? : ";
echo $cl->isFileManagerModuleInstalled();
echo "\n";

echo "\n";
echo "Checking Lucene Indexer: -------------------\n";

$data = array(
  "title"=> "CMS Unit Test 2009JanWed",
  "sectionid"=> NULL,
  "introtext"=> "Unit Test Intro Text ",
  "body"=> "Unit Test Full Text",
  "access"=> NULL,
  "ordering"=> 1,
  "published"=> 1,
  "show_title"=> "y",
  "show_author"=> "y",
  "show_date"=> "y",
  "show_pdf"=> "y",
  "show_email"=> "y",
  "show_print"=> "y",
  "created"=> "2009-01-21 09:52:38",
  "modified"=> "2009-01-21 09:52:38",
  "override_date"=> "2009-01-21 09:52:38",
  "post_lic"=> NULL,
  "created_by"=> "init_1",
  "created_by_alias"=> "init_1",
  "checked_out"=> "init_1",
  "checked_out_time"=> "2009-01-21 09:52:38",
  "metakey"=> "Unit Test Meta Key",
  "metadesc"=> "Unit Test Meta Description",
  "start_publish"=> NULL,
  "end_publish"=> "",
  "id"=> "gen12Srv1Nme13_4463_1232524358"
 );

echo "Adding Index : ";
echo $cl->luceneIndex($data);
echo "\n";

echo "Removing Index : ";
echo $cl->luceneIndex($data);
echo "\n";

echo "\n";
echo "Checking CMS DB Content Functions: -------------------\n";

echo "Retrieve Content Records\n";
$result = $cl->getContentPages('');
echo "Array Test: ";
echo (is_array($result)) ? 'is_array: TRUE' : 'is_array: FALSE';
echo "\nCount: ";
echo count($result);
echo "\n\n";

echo "Add Content : \n";

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
                    
$contentId = $cl->addContent($title,
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
                    $ccLicence);

echo $contentId ."\n";
echo "\n";

echo "Edit Content Item [$contentId]: \n\n";

$id = $contentId;
$title = 'Unit Test Title Edited';
$sectionid = 'init_2';
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

$result = $cl->editContent($id ,
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
echo $result;
echo "\n";

echo "Get a single content page [$contentId]\n";
$record = $cl->getContentPage($contentId);
var_dump($record);
//Assert Array : $record
echo "\n";

//Assert: that results were edited
echo "\nChecking weather fields were edited:";

echo "\nCheck ID Remains Unchanged: ";
if ($cl->check_id($record) == $contentId){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Title: ";
if ($cl->check_title($record) == 'Unit Test Title Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Section ID: ";
if ($cl->check_sectionid($record) == 'init_2'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Published: ";
if ($cl->check_published($record) == 1){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Access: ";
if ($cl->check_access($record) == 1){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Intro Text: ";
if ($cl->check_introText($record) == 'Unit Test Intro Text Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Full Text: ";
if ($cl->check_fullText($record) == 'Unit Test Full Text Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Override Date: ";
if ($cl->check_override_date($record) == '2008-01-01 00:00:00'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Start Publish: ";
if ($cl->check_start_publish($record) == '2008-01-01 00:00:00'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck End Publish: ";
if ($cl->check_end_publish($record) == '2008-01-01 00:00:00'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Meta Key: ";
if ($cl->check_metakey($record) == 'Unit Test Meta Key Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Meta Description: ";
if ($cl->check_metadesc($record) == 'Unit Test Meta Description Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck ccLicence: ";
if ($cl->check_ccLicence($record) == 'New Licence'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Show Title: ";
if ($cl->check_show_title($record) == 'n'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Show Author: ";
if ($cl->check_show_author($record) == 'y'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Show Date: ";
if ($cl->check_show_date($record) == 'n'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Show PDF: ";
if ($cl->check_show_pdf($record) == 'y'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Show Email: ";
if ($cl->check_show_email($record) == 'n'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Show Print: ";
if ($cl->check_show_print($record) == 'y'){
    echo "PASSED";
} else {
    echo "FAILED";
}

//-------------------------------

echo "\nTrash Content [$contentId]\n";
$result = $cl->trashContent($contentId);
echo $result;
echo "\n";

echo "Restore Content [$contentId]\n";
$result = $cl->undelete($contentId);
echo $result;
echo "\n";

echo "Delete Content [$contentId]\n";
$result = $cl->deleteContent($contentId);
echo $result;
echo "\n\n";

echo "Retrieve Content Records\n";
$result = $cl->getContentPages('');
echo "Array Test: ";
echo (is_array($result)) ? 'is_array: TRUE' : 'is_array: FALSE';
echo "\nCount: ";
echo count($result);
echo "\n";

echo "\n===================Testing DB Section Functions===================\n";


echo "\n\nRetrieve Section Records\n";
$result = $cl->getAllSections();
echo "Array Test: ";
echo (is_array($result)) ? 'is_array: TRUE' : 'is_array: FALSE';
echo "\nCount: ";
echo count($result);
echo "\n";

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

echo "\nAdd Section \n";
$result = $cl->addSection($title,
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

echo $result;
echo "\n";

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
$customNum = null;
$pageNum = '0';
$pageOrder = 'pagedate_asc';
$imageUrl = null;
$contextCode = null;

echo "\nAdd Section \n";
$result = $cl->addSection($title,
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

echo $result;

if ($result == FALSE) {
    echo "FAILED";    
}

echo "\n";

//Edit the section
$id = $result;
$parentId = 'init_1';
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
$pageNum = '1';
$pageOrder = 'pagedate_asc Edited';
$imageUrl = 'Edited';
$contextCode = 'Edited';

echo "\nEdit Section \n";
$result = $cl->editSection($id,
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

echo $result;
echo "\n";

$testSectionId = $id;



echo "Get a single section record [$testSectionId]\n";
$record = $cl->getSection($testSectionId);
var_dump($record);
//Assert Array : $record
echo "\n";



//Assert: that results were edited
echo "\nChecking weather fields were edited:";

echo "\nCheck ID Remains Unchanged: ";
if ($cl->check_section_id($record) == $testSectionId){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Parent: ";
if ($cl->check_section_parentid($record) == 'init_1'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Title: ";
if ($cl->check_section_title($record) == 'Test Title Unit Test Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Menutext: ";
if ($cl->check_section_menutext($record) == 'Test Menu Text Unit Test Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Access: ";
if ($cl->check_section_access($record) == null){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck Description: ";
if ($cl->check_section_description($record) == 'Test Description Unit Test Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck Published: ";
if ($cl->check_section_published($record) == 1){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck Layout: ";
if ($cl->check_section_layout($record) == 'page Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck Show Introduction: ";
if ($cl->check_section_show_introduction($record) == 'n'){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck Show Title: ";
if ($cl->check_section_show_title($record) == 'n'){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck Show User: ";
if ($cl->check_section_show_user($record) == 'n'){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck Show Date: ";
if ($cl->check_section_show_date($record) == 'n'){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck numpagedisplay: ";
if ($cl->check_section_numpagedisplay($record) == '1'){
    echo "PASSED";
} else {
    echo "FAILED";
}


echo "\nCheck Order Type: ";
if ($cl->check_section_ordertype($record) == 'pagedate_asc Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Image Link: ";
if ($cl->check_section_link($record) == 'Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}

echo "\nCheck Context Code: ";
if ($cl->check_section_contextcode($record) == 'Edited'){
    echo "PASSED";
} else {
    echo "FAILED";
}



//===========================Deleting Root and Child Section===============================



echo "\n\nRetrieve Section Records\n";
$result = $cl->getAllSections();
echo "Array Test: ";
echo (is_array($result)) ? 'is_array: TRUE' : 'is_array: FALSE';
echo "\nCount: ";
echo count($result);
echo "\n";

?>

