<?php
//$this->objScriptaculous =& $this->getObject('scriptaculous', 'ajaxwrapper');
//$this->objScriptaculous->show();

$script = '
<script type="text/javascript">
//<![CDATA[

function previewFile(id, jsId) {
    var url = \'index.php\';
    var pars = \'module=filemanager&action=sendpreview&id=\'+id+\'&jsId=\'+jsId;
    var myAjax = new Ajax.Request( url, {method: \'get\', parameters: pars, onComplete: showResponse} );
}

function showResponse (originalRequest) {
    var newData = originalRequest.responseText;
    $(\'previewwindow\').innerHTML = newData;
}
//]]>
</script>';
$this->appendArrayVar('headerParams', $script);

$objFreemind = $this->getObject('flashfreemind', 'files');
$this->appendArrayVar('headerParams', $objFreemind->getMindmapScript());

$objFileIcon = $this->getObject('fileicons', 'files');
$this->loadClass('link', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

echo '
<div style="position:absolute; bottom: 0px; baackground-color:#999933; z-index: 100; left: 0; height: 100px; margin-bottom: 20px;">';
echo '<h1>Upload File</h1>';

$this->objUpload->formaction = $this->uri(array('action'=>'selectfileuploads'));
$this->objUpload->numInputs = 1;

$mode = new hiddeninput('mode', 'selectfilewindow');
$name = new hiddeninput('name', $this->getParam('name'));
$context = new hiddeninput('context', $this->getParam('context'));
$workgroup = new hiddeninput('workgroup', $this->getParam('workgroup'));
$restrict = new hiddeninput('restrict', $this->getParam('restrict'));
$value = new hiddeninput('value', $this->getParam('value'));


$this->objUpload->formExtra = $mode->show().$name->show().$context->show().$workgroup->show().$value->show().$restrict->show();

if (count($restrictions) != 0) {
    $this->objUpload->formExtra .= '<br /><span class="warning minute"> '.$this->objLanguage->languageText('mod_filemanager_requiredfiletypes', 'filemanager', 'Required File Types').': ';
    $comma = '';
    foreach ($restrictions as $restriction)
    {
        $this->objUpload->formExtra .= $comma.$restriction;
        $comma = ', ';
    }
    
    $this->objUpload->formExtra .= '</span>';
}

echo $this->objUpload->show();

echo '</div>';

echo '<div style="width: 30%; baackground-color:#FFFF00; top: 0; left: 0; position: absolute; overflow:auto; bottom: 120px;  z-index:1; padding-bottom: 100px;">';

echo '<h1>List of Files</h1>';

// $objTreeFilter =& $this->getObject('treefilter');
// echo $objTreeFilter->showDropDown();

//echo '<br /><br />';

if (count($files) == 0) {
    if (count($restrictions) == 0) {
        echo $this->objLanguage->languageText('mod_filemanager_youhavenotuploadedanyfiles', 'filemanager', 'You have not uploaded any files');
    } else {
        
        $string = $this->objLanguage->languageText('mod_filemanager_youhavenotuploadedanytypeoffilesyet', 'filemanager', 'You have not yet uploaded any [-TYPE-] files yet');
        
        
        $comma = '';
        $fileTypes = '';
        foreach ($restrictions as $restriction)
        {
            $fileTypes .= $comma.strtoupper($restriction);
            $comma = ', ';
        }
        
        echo str_replace('[-TYPE-]', $fileTypes, $string);
    }
} else {
        
    $count = 0;
    
    $fileIdArray = 'fileId = new Array('.count($files).');';
    $filenameArray = 'fileName = new Array('.count($files).');';
    
    $table = $this->newObject('htmltable', 'htmlelements');
    
    $defaultItem = array();
    
    foreach ($files as $file)
    {
        $link = new link ("javascript:previewFile('".$file['id']."', '".$count."');");
        $link->link = htmlentities($file['filename']);
        $link->title = $this->objLanguage->languageText('mod_filemanager_previewfile', 'filemanager', 'Preview file');
        
        $selectLink = new link ("javascript:selectFile('".$file['id']."', '".$count."');");
        $selectLink->link = $this->objLanguage->languageText('word_select', 'system', 'Select');
        
        $icon = $objFileIcon->getFileIcon($file['filename']);
        //echo '<li>'.$icon.' '.$link->show().'</li>';
        
        $table->startRow();
        $table->addCell($icon, 16);
        $table->addCell($link->show());
        $table->addCell(' &nbsp;  &nbsp;');
        $table->addCell($selectLink->show(), 40);
        $table->endRow();
        
        $fileIdArray .= 'fileId['.$count.'] = "'.$file['id'].'";';
        $filenameArray .= 'fileName['.$count.'] = \''.htmlentities($file['filename']).'\';';
        
        if ($count ==0) {
            $defaultItem['id'] = $file['id'];
            $defaultItem['count'] = $count;
        }
        
        if ($defaultValue == $file['id']) {
            $defaultItem['id'] = $file['id'];
            $defaultItem['count'] = $count;
        }
        
        $count++;
    }
    echo $table->show();
    
    if (count($defaultItem) > 0) {
        $this->appendArrayVar('bodyOnLoad', "previewFile('".$defaultItem['id']."', '".$defaultItem['count']."');");
    }
    
    $script = '<script type="text/javascript">

    '.$fileIdArray.'
    
    '.$filenameArray.'
</script>';

    $this->appendArrayVar('headerParams', $script);
     $objAltConfig = $this->getObject('altconfig','config');
     $folderPath= $objAltConfig->getContentBasePath().'/users/'.$this->objUser->userId();
    $checkOpenerScript = '
<script type="text/javascript">
function selectFile(file, id)
{
    if (window.opener) {
        
        //alert(fileId[id]);
        //window.opener.document.getElementById("input_selectfile_'.$inputname.'").value = fileName[id];
        //window.opener.document.getElementById("hidden_'.$inputname.'").value = fileId[id];
    var imageURL="'.$folderPath.'/"+fileName[id];
        window.close();
        window.opener.passImageUrlToApplet(imageURL);
        window.opener.focus();
    } else {
        window.parent.document.getElementById("input_selectfile_'.$inputname.'").value = fileName[id];
        window.parent.document.getElementById("hidden_'.$inputname.'").value = fileId[id];
        window.parent.hidePopWin();
    }
}
</script>
        ';
        
        $this->appendArrayVar('headerParams', $checkOpenerScript);
        
        
}


$this->setVar('suppressFooter', TRUE);
?>
</div>
<div style="width: 70%; baackground-color:#FF00FF;  overflow:auto; top: 0; left: 30%; bottom: 120px; position: absolute; z-index:1;" id="previewwindow" >

</div>