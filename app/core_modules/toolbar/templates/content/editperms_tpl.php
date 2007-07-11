<?php
/**
* @package toolbar
*/

/**
* Template to change permissions for a menu link.
* @param string $moduleName The name of the module.
* @param string $defaultList The default permissions, if requested.
* @param bool $setDefault Set default or use existing.
*/

// Suppress normal page elements and layout
$this->setVar('pageSuppressIM', FALSE);
$this->setVar('pageSuppressBanner', FALSE);
//$this->setVar('pageSuppressContainer', FALSE);
$this->setVar('pageSuppressToolbar', FALSE);
$this->setVar('suppressFooter', FALSE);

// set up html elements
$objHead = $this->newObject('htmlheading', 'htmlelements');
$objForm = $this->newObject('form', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objButton = $this->loadClass('button', 'htmlelements');
$this->loadClass('layer', 'htmlelements');
$objSelectBox = $this->newObject('selectbox','htmlelements');

// set up language items
$objLanguage =& $this->getObject('language', 'language');
$linkPermLabel = $objLanguage->languageText('mod_toolbar_linkpermissions','toolbar', 'Link Permissions');
$aclLabel = $objLanguage->languageText('mod_toolbar_selectacl','toolbar', 'Select an access control list (ACL)');
$groupLabel = $objLanguage->languageText('mod_toolbar_selectgroup','toolbar', 'Select a group');
$conGroupLabel = $objLanguage->code2Txt('mod_toolbar_selectcongroup','toolbar', array('context'=>'course'));
$authorLabel = ucwords($objLanguage->languageText('mod_context_author','toolbar'));
$readonlyLabel = ucwords($objLanguage->languageText('mod_context_readonly','toolbar'));
$guestLabel = ucwords($objLanguage->languageText('mod_toolbar_guest','toolbar'));
$saveLabel = $objLanguage->languageText('word_save','security', 'Save');
$closeLabel = $objLanguage->languageText('word_close','security', 'Close');
$availACLs = ucwords($objLanguage->languageText('mod_toolbar_availacl','toolbar', 'Available ACLs'));
$selectACLs = ucwords($objLanguage->languageText('mod_toolbar_selectedacl','toolbar', 'Selected ACLs'));
$selectGroups = ucwords($objLanguage->languageText('mod_toolbar_selectedgroup','toolbar', 'Selected Groups'));
$availGroups = ucwords($objLanguage->languageText('mod_toolbar_availgroup','toolbar', 'Available Groups'));
$selectConGr = ucwords($objLanguage->code2Txt('mod_toolbar_selectedcongroup','toolbar',array('context'=>'course')));
$availConGr = ucwords($objLanguage->code2Txt('mod_toolbar_availcongroup','toolbar', array('context'=>'course')));
$restoreLabel = $objLanguage->languageText('mod_toolbar_restoredefaultperms','toolbar');

if(!isset($defaultList)){
    $defaultList = 'Site Admin';
}

// Script to build the output string
$javascript = "<script language='javascript'>

    function setDefault()
    {   
        var str = '".$defaultList."';
        window.opener.document.menulink.permissions.value=str;
        getPerms();
    }

    function restoreDefaults()
    {
        document.forms['restore'].submit();
    }

    function getPerms()
    {
        var str;
        var permArray = new Array(3);
        str = window.opener.document.menulink.permissions.value+'|'+'|';
        permArray = str.split('|');

        if(permArray[0] != ''){
            string1 = permArray[0].replace('site','');
            if(string1 != ''){
                var acls = string1.split(',');
                buildLists(acls, 'leftList[]', 'rightList[]');
            }
        }

        if(permArray[1] != ''){
            string = permArray[1].replace('site','');
            if(string != ''){
                var groups = string.split(',');
                buildLists(groups, 'leftGroup[]', 'rightGroup[]');
            }
        }

        if(permArray[2] != ''){
            str = permArray[2].replace('_con_','');
            if(str != ''){
                var cons = str.split(',');
                buildLists(cons, 'leftConGroup[]', 'rightConGroup[]');
            }
        }
    }

    function buildLists(listArray, left, right)
    {
        for(var i = 0; i< listArray.length; i++){
            var label = 'null';
            for(var j = 0; j < document.acl[left].options.length; j++){
                if(document.acl[left].options[j].value == listArray[i]){
                    label = document.acl[left].options[j].text;
                    document.acl[left].options[j] = null;
                }
            }
            document.acl[right].options[i] = new Option(label, listArray[i]);
        }
    }


    function submitPerms()
    {
        var acls = '';
        for (var i=0; i < document.acl['rightList[]'].options.length; i++) {
            if(acls != ''){
                acls += ',';
            }
            acls += document.acl['rightList[]'].options[i].value;
        }

        var group = '';
        for (var i=0; i < document.acl['rightGroup[]'].options.length; i++) {
            if(group != ''){
                group += ',';
            }
            group += document.acl['rightGroup[]'].options[i].value;
        }

        var con = '';
        for (var i=0; i < document.acl['rightConGroup[]'].options.length; i++) {
            if(con != ''){
                con += ',';
            }
            con += document.acl['rightConGroup[]'].options[i].value;
            
        }
        
        var str = '';
        str = acls+'|'+group+'|'+'_con_'+con;
        window.opener.document.menulink.permissions.value=str;
        document.write('finished');
        window.close();
    }

    </script>";
echo $javascript;


if($setDefault){
    $bodyParams = "onload = 'javascript:setDefault()'";
}else{
    $bodyParams = "onload = 'javascript:getPerms()'";
}
$this->setVarByRef('bodyParams', $bodyParams);

// Link Permissions
$objHead->str = $linkPermLabel;
$objHead->type = 1;

$str = $objHead->show();

/* *************** ACLs *********************** */

$objForm = new form('acl');
$objForm->action = $this->uri(array('action' => 'processform'));

//$objSelectBox = new selectbox();
// Initialise the selectbox.
$objSelectBox->create( $objForm, 'leftList[]', $availACLs, 'rightList[]', $selectACLs);

// Populate the selectboxes
$aclList = $this->objPerms->getAcls(array('id', 'name'));
$objSelectBox->insertLeftOptions( $aclList, 'id', 'name' );
$objSelectBox->insertRightOptions( array() );

// Insert the selectbox into the form object.
$objHead->str = $aclLabel;
$objHead->type = 3;

$objForm->addToForm('<p>'.$objHead->show().'</p><p>'.$objSelectBox->show().'</p>');

/* ************** Groups *************** */

// Initialise the selectbox.
$objSelectBox->create( $objForm, 'leftGroup[]', $availGroups, 'rightGroup[]', $selectGroups);

// Populate the selectboxes
$groupList = $this->objGroups->getGroups(array('id'));

$i=0;
foreach($groupList as $item){
    $newList[$i]['id'] = $this->objGroups->getFullPath($item['id']);
    $newList[$i]['name'] = $newList[$i]['id'];
    $i++;
}
$objSelectBox->insertLeftOptions($newList, 'id', 'name');
$objSelectBox->insertRightOptions(array());

// Insert the selectbox into the form object.
$objHead->str = $groupLabel;
$objHead->type = 3;

$objForm->addToForm('<p>'.$objHead->show().'</p><p>'.$objSelectBox->show().'</p>');


/* ***************** Context Groups ****************** */

// Initialise the selectbox.
$objSelectBox->create( $objForm, 'leftConGroup[]', $availConGr, 'rightConGroup[]', $selectConGr);

// Populate the selectboxes
$conGroups = array();
$conGroups[0] = array('id'=>'Lecturers', 'name'=>$authorLabel);
$conGroups[1] = array('id'=>'Students', 'name'=>$readonlyLabel);
$conGroups[2] = array('id'=>'Guest', 'name'=>$guestLabel);

$objSelectBox->insertLeftOptions($conGroups, 'id', 'name');
$objSelectBox->insertRightOptions(array());

// Insert the selectbox into the form object.
$objHead->str = $conGroupLabel;
$objHead->type = 3;

$objForm->addToForm('<p>'.$objHead->show().'</p><p>'.$objSelectBox->show().'</p>');

/* *********** Save and close buttons ************* */
$objButton = new button('save', $saveLabel);

$objButton->setOnClick('submitPerms()');

$btns = '<p><br/>'.$objButton->show();

$objButton = new button('save', $closeLabel);
$objButton->setOnClick('window.close()');
$btns .= '&nbsp;&nbsp;'.$objButton->show().'</p>';

$objForm->addToForm($btns);

/* ************ Show the form ************* */
$str .= $objForm->show();

/* ************ Restore default permissions ************ */

$objInput = new textinput('modulename', $moduleName);
$objInput->fldType = 'hidden';

$objLink = new link('javascript:void(0)');
$objLink->extra = "onclick=\"restoreDefaults()\"";
$objLink->link = $restoreLabel;

$objForm = new form('restore', $this->uri(array('action'=>'restoreperms')));
$objForm->addToForm($objInput->show());
$objForm->addToForm($objLink->show());

$str .= '<p><br/>'.$objForm->show().'</p>';

$objLayer = new layer();
$objLayer->str = $str;
$objLayer->align = 'center';
echo $objLayer->show().'<p>&nbsp;</p>';
?>