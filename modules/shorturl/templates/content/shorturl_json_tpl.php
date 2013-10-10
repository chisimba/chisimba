<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * This template will list all the sections for input as JSON to jQuery jqGrid control
 */

$link =  $this->newObject('link', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');

$parentId = $this->getParam('parentid', '');

$sortId = $this->getParam('sidx', 'datestamp');
$sortOrder = $this->getParam('sord', 'ASC');

$page = $this->getParam('page', '1');
$rp = $this->getParam('rp');
$total = 10;

$arrMaps = $this->objMap->getAll(" ORDER BY $sortId $sortOrder");

$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [";
$rc = false;

if (!empty($arrMaps)){

    //Adding Sections Here
    foreach ($arrMaps as $map){
    
        //Setting up the fields for display

        $matchUrl = $map['match_url'];
        $targetUrl = $map['target_url'];
        $order = $map['ordering'];
        $date = $map['datestamp'];

        //is Dynamic Checkbox
        /* //Ditched this for only displaying (Overhead of forms loading not worth the bandwith)
        $chkBool = ($map['is_dynamic'] == '1')? true : false;
        $check = new checkbox('chk_dynamic', '', $chkBool);
        $check->cssId = 'edit_'.$map['id'];
        $dynamic = $check->show();
        */

        if ($map['is_dynamic'] == '1') {
            $objIcon->setIcon('checked', 'gif', 'icons/shorturl/');
        } else {
            $objIcon->setIcon('unchecked', 'gif', 'icons/shorturl/');
        }

        $dynamic = $objIcon->show();

        //options [edit/delete]
        //TODO: Add Node level Security for Rules

        //if ($this->_objSecurity->canUserWriteSection($section['id'])){
            //$delArray = array('action' => 'deletesection', 'confirm'=>'yes', 'id'=>$section['id']);
            //$deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelsection', 'cmsadmin');
            //$delIcon = $objIcon->getDeleteIconWithConfirm($section['id'], $delArray,'cmsadmin',$deletephrase);

        $objIcon->setIcon('bigtrash');
        $delIcon = "<a id='del_$map[id]' href='javascript:void(0)'>".$objIcon->show()."</a>";

        //} else {
        //    $delIcon = '';
        //}

        //edit icon
        //if ($this->_objSecurity->canUserWriteSection($section['id'])){
        $objIcon->setIcon('edit');
        //$editIcon = "<a id='edit_$map[id]' href='javascript:void(0)'>".$objIcon->show()."</a>";
        $editIcon = "<a id='edit_$map[id]' title='Edit URL Mapping' href='http://localhost/fresh/?module=shorturl&action=getform&type=addeditform&id=$map[id]'>".$objIcon->show()."</a>";
        //} else {
        //    $editIcon = '';
        //}

        /*
        if (!$this->_objSecurity->canUserWriteSection($section['id'])){
            $editIcon = '';
            $deleteIcon = '';
        }
        */

        $options = $editIcon.$delIcon;

        if ($rc) $json .= ",";
        $json .= "\n{";
        $json .= "id:'".$map['id']."',";
        $json .= "cell:['".addslashes($matchUrl)."'";
        $json .= ",'".addslashes($targetUrl)."'";
        //$json .= ",'".addslashes($dynamic)."'";
        $json .= ",'".addslashes($order)."'";
        $json .= ",'".addslashes($options)."'";
        $json .= ",'".addslashes($date)."']";
        $json .= "}";
        $rc = true;
    }
}

$json .= "]\n";
$json .= "}";

echo $json;

//log_debug(var_export($_POST, true));
//log_debug(var_export($_REQUEST, true));
//log_debug(var_export($_GET, true));

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