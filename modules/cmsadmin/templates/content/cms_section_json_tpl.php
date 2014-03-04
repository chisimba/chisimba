<?php
/**
 * This template will list all the sections for input as JSON to jQuery jqGrid control
 */

$link =  $this->newObject('link', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');

$parentId = $this->getParam('parentid', '');

$sortId = $this->getParam('sidx', 'title');
$sortOrder = $this->getParam('sord', 'ASC');

$page = $this->getParam('page', '0');
$rp = $this->getParam('rp');
$total = 0;

if ($parentId == ''){
    $arrSections = $this->_objSections->getAll(" ORDER BY $sortId $sortOrder");
    //$arrContent = $this->_objContent->getAll(" ORDER BY $sortId $sortOrder");
} else {
    $arrSections = $this->_objSections->getAll(" WHERE parentid = '$parentId' ORDER BY $sortId $sortOrder");
    $arrContent = $this->_objContent->getAll(" WHERE sectionid = '$parentId' ORDER BY $sortId $sortOrder");
}

//$this->_objSections =  $this->newObject('dbsections', 'cmsadmin');

//$sections = $this->_objSections->getSections();

//var_dump($arrSections); exit;
//var_dump($arrContent); exit;

$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;

if (!empty($arrSections)){

    //Adding Sections Here
    foreach ($arrSections as $section){
    
        //Setting up the fields for display
    
        //Folder Icon
        $objIcon->setIcon('tree/folder');
    
        //View section link
        $link->link = $section['title'];
        $link->href = $this->uri(array('action' => 'viewsection', 'id' => $section['id']));
        $viewSectionLink = $objIcon->show().$link->show();
    
        $folderName = $viewSectionLink;
        $sectionName = $section['menutext'];
        $pages = $this->_objContent->getNumberOfPagesInSection($section['id']);
        $layout = $this->_objLayouts->getLayoutDescription($section['layout']);
        $order = $this->_objSections->getOrderingLink($section['id']);//$this->_objSections->getPageOrderType($section['ordertype']);
        $date = $section['datecreated'];
    
        //published
        if($section['published']){
            $url = $this->uri(array('action' => 'sectionpublish', 'id' => $section['id'], 'mode' => 'unpublish'));
            $icon = $this->_objUtils->getCheckIcon(TRUE);
        }else{
            $url = $this->uri(array('action' => 'sectionpublish', 'id' => $section['id'], 'mode' => 'publish'));
            $icon = $this->_objUtils->getCheckIcon(FALSE);
        }
        $objLink = new link($url);
        $objLink->link = $icon;
        $published = $objLink->show();
    
        //options [edit/delete]
        if ($this->_objSecurity->canUserWriteSection($section['id'])){
            $delArray = array('action' => 'deletesection', 'confirm'=>'yes', 'id'=>$section['id']);
            $deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelsection', 'cmsadmin');
            $delIcon = $objIcon->getDeleteIconWithConfirm($section['id'], $delArray,'cmsadmin',$deletephrase);
        } else {
            $delIcon = '';
        }
    
        //edit icon
        if ($this->_objSecurity->canUserWriteSection($section['id'])){
            $editIcon = $objIcon->getEditIcon($this->uri(array('action' => 'addsection', 'id' => $section['id'])));
        } else {
            $editIcon = '';
        }
    
        if (!$this->_objSecurity->canUserWriteSection($section['id'])){
            $editIcon = '';
            $deleteIcon = '';
            $visibleLink = '';
        }
    
        $options = $editIcon.$delIcon;
    
        if ($rc) $json .= ",";
        $json .= "\n{";
        $json .= "id:'".$section['id']."',";
        $json .= "cell:['".addslashes($folderName)."'";
        //$json .= ",'".addslashes($sectionName)."'";
        $json .= ",'".addslashes($pages)."'";
        $json .= ",'".addslashes($layout)."'";
        $json .= ",'".addslashes($order)."'";
        $json .= ",'".addslashes($published)."'";
        $json .= ",'".addslashes($options)."'";
        $json .= ",'".addslashes($date)."']";
        $json .= "}";
        $rc = true;     
    }
}

//Adding Content Items Here
if (!empty($arrContent)){
    foreach ($arrContent as $content){
        $contentId = $content['id'];
        $sectionId = $content['sectionid'];

        //Setting up the fields for display
    
        //Folder Icon
        $objIcon->setIcon('tree/file');
    
        //View section link
        $link->link = $content['title'];
        $link->href = $this->uri(array('action' => 'viewsection', 'id' => $content['id']));
        $viewContentLink = $objIcon->show().$link->show();
    
        $contentName = $viewContentLink;
        $layout = '';
        $ordering = $this->_objContent->getOrderingLink($sectionId, $contentId);
        $date = $content['modified'];
    
        $contentPublished = $content['published'];

        //publish, visible
        if($contentPublished){
            $url = $this->uri(array('action' => 'contentpublish', 'id' => $content['page_id'], 'mode' => 'unpublish', 'sectionid' => $sectionId));
            $icon = $this->_objUtils->getCheckIcon(TRUE);
        }else{
            $url = $this->uri(array('action' => 'contentpublish', 'id' => $content['page_id'], 'mode' => 'publish', 'sectionid' => $sectionId));
            $icon = $this->_objUtils->getCheckIcon(FALSE);
        }
        $objLink = new link($url);
        $objLink->link = $icon;
        $published = $objLink->show();

        //Create delete icon
        if ($this->_objSecurity->canUserWriteContent($contentId)){
            $delArray = array('action' => 'trashcontent', 'confirm' => 'yes', 'id' => $contentId, 'sectionid' => $sectionId);
            $deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelpage', 'cmsadmin');
            $delIcon = $objIcon->getDeleteIconWithConfirm($contentId, $delArray, 'cmsadmin', $deletephrase);
        } else {
            $delIcon = '';
        }       
        
        //Create edit icon
        if ($this->_objSecurity->canUserWriteContent($contentId)){
            $editIcon = $objIcon->getEditIcon($this->uri(array('action' => 'addcontent', 'id' => $contentId, 'parent' => $sectionId)));
        } else {
            $editIcon = '';
        }       

        //Create view icon
        if ($this->_objSecurity->canUserReadContent($contentId)){
            $objIcon->title = "Preview";
            $viewIcon = $objIcon->getViewIcon($this->uri(array('action' => 'showfulltext', 'id' => $contentId, 'parent' => $sectionId), 'cms'));
        } else {
            $viewIcon = '';
        }       
    
        //Make title link to view section
        $objLink = new link($this->uri(array('action' => 'showcontent', 'id' => $contentId, 'fromadmin' => TRUE, 'sectionid' => $sectionId), 'cms'));
        $objLink->link = $contentTitle;
        $viewPageLink = $objLink->show();

        //Icon for toggling front page status
        if(isset($content['front_id']) && !empty($content['front_id'])) {
            $objIcon->setIcon('greentick');
            $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_confirmremovefromfp', 'cmsadmin');
            $url = array('action' => 'changefpstatus', 'id' => $content['front_id'], 'sectionid' => $sectionId, 'mode' => 'remove');
        } else {
            $objIcon->setIcon('redcross');
            $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addpagetofp', 'cmsadmin');
            $url = array('action' => 'changefpstatus', 'id' => $contentId, 'sectionid' => $sectionId, 'mode' => 'add');
        }
        $frontPageLink = new link($this->uri($url, 'cmsadmin'));
        $frontPageLink->link = $objIcon->show();

        // set up link to view contact details in a popup window
        $objBlocksLink = new link('#');
        $objBlocksLink->link = $blockIcon;
        $objBlocksLink->extra = "onclick = \"javascript:window.open('" . $this->uri(array('action' => 'addblock', 'sectionId' => $sectionId, 'pageid' => $contentId, 'blockcat' => 'content')) . "', 'branch', 'width=500, height=350, top=50, left=50, scrollbars')\"";

        if ($this->_objSecurity->canUserWriteContent($contentId)){
            $objBlocksLinkDisplay = $objBlocksLink->show();
        } else {
            $objBlocksLinkDisplay = '';
        }
        
        $options = $editIcon.$viewIcon.$delIcon.$objBlocksLinkDisplay;

        //Add Content Data
        if ($rc) $json .= ",";
        $json .= "\n{";
        $json .= "id:'".$content['id']."',";
        $json .= "cell:['".addslashes($contentName)."'";
        //$json .= ",'".addslashes($contentName)."'";
        $json .= ",'".addslashes($pages)."'";
        $json .= ",'".addslashes($layout)."'";
        $json .= ",'".addslashes($order)."'";
        $json .= ",'".addslashes($published)."'";
        $json .= ",'".addslashes($options)."'";
        $json .= ",'".addslashes($date)."']";
        $json .= "}";
        $rc = true;     
    }        
}

$json .= "]\n";
$json .= "}";

echo $json;

/*
if (!$sortname) $sortname = 'title';
if (!$sortorder) $sortorder = 'desc';

$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 10;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";
*/
/*
$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;
while ($row = mysql_fetch_array($result)) {
    if ($rc) $json .= ",";
    $json .= "\n{";
    $json .= "id:'".$row['parentid']."',";
    $json .= "cell:['".$row['parentid']."'";
    $json .= ",'".addslashes($row['title'])."'";
    $json .= ",'".addslashes($row['published'])."'";
    $json .= ",'".addslashes($row['ordering'])."'";
    $json .= ",'".$row['datecreated']."']";
    $json .= "}";
    $rc = true;     
}
$json .= "]\n";
$json .= "}";
//echo $json;
*/
log_debug(var_export($_POST, true));
log_debug(var_export($_REQUEST, true));
log_debug(var_export($_GET, true));
/*
?>
{
page: 1,
total: 14,
rows: [
{id:'cjm_8774_1221818255',cell:['<a href = "">cjm_8774_1221818255</a>','Sub Mofo Mofo','1','1','2008-09-19 11:59:22']},
{id:'0',cell:['<? echo addslashes($query);?>','<? echo addslashes($qtype);?>','1','3','2008-09-22 14:43:02']},
{id:'cjm_3489_1221816138',cell:['<? echo $page;?>','<? echo $rp;?>','<? echo $sortname;?>','<? echo $sortorder;?>','test-data']},
{id:'cjm_4913_1221817860',cell:['cjm_4913_1221817860','folder1.2.3.4','1','1','2008-09-19 11:51:43']}]
}
<?PHP
//*/
?>