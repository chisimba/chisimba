<?php

// echo '<pre>';
// print_r($_GET);
// echo '</pre>';


$objFileIcon = $this->getObject('fileicons', 'files');
$objThumbnail = $this->getObject('thumbnails', 'filemanager');

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

echo $this->objUpload->show();

echo '</div>';

echo '<div style="width: 100%; background-color:#FFFF00; top: 0; left: 0; position: absolute; overflow-y:scroll; overflow-x:hidden; bottom: 120px;  z-index:1; padding-bottom: 100px;">';

echo '<h1>List of Images</h1>';

// $objTreeFilter =& $this->getObject('treefilter');
// echo $objTreeFilter->showDropDown();

//echo '<br /><br />';

if (count($files) == 0) {
    echo ' No files matching criteria found';
} else {
    
    $previewScript = '
<script type="text/javascript">

previews = new Array('.(count($files)-1).');

function previewFile(file, id)
{
    if (previews[id]) {
        document.getElementById("previewwindow").innerHTML = previews[id];
    } else {
        return xajax_generatePreview(file, id);
    }
}


function appendPreviews(id, value)
{
    previews[id] = value;
}

</script>
        ';
        
        $this->appendArrayVar('headerParams', $previewScript);
        
    $count = 0;
    
    $fileIdArray = 'fileId = new Array('.count($files).');';
    $filenameArray = 'fileName = new Array('.count($files).');';
    
    $table = $this->newObject('htmltable', 'htmlelements');
    
    $defaultItem = array();
    
    foreach ($files as $file)
    {
        $link = new link ("javascript:previewFile('".$file['id']."', '".$count."');");
        $link->link = htmlentities($file['filename']);
        $link->title = 'Preview File';
        
        $selectLink = new link ("javascript:selectImage('".$file['id']."', '".$count."');");
        $selectLink->link = 'Insert Image';
        
        
        $thumbImg = $objThumbnail->getThumbnail($file['id'], $file['filename']);
        
        echo '
<div style="width: 120px; margin-bottom:20px;" class="floatlangdir">
    <div style="line-height:120px; vertical-align:center; text-align:center;">
    <img src="'.$thumbImg.'" style="vertical-align:middle;" />
    </div>
    <div style="text-align:center;">
        '.$selectLink->show().'
    </div>
</div>
';
        
        $fileIdArray .= 'fileId['.$count.'] = "'.$file['id'].'";';
        $filenameArray .= 'fileName['.$count.'] = \''.$thumbImg.'\';';
        
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
    //echo $table->show();
    
    if (count($defaultItem) > 0) {
        //$this->appendArrayVar('bodyOnLoad', "previewFile('".$defaultItem['id']."', '".$defaultItem['count']."');");
    }
    
    $script = '<script type="text/javascript">

    '.$fileIdArray.'
    
    '.$filenameArray.'
</script>';

    $this->appendArrayVar('headerParams', $script);
    
    $checkOpenerScript = '
<script type="text/javascript">
function selectImage(file, id)
{
    if (window.opener) {
        
        //alert(fileName[id]);
        window.opener.document.getElementById("imagepreview_'.$inputname.'").src = fileName[id];
        //window.opener.document.getElementById("selectfile_'.$inputname.'").value = fileName[id];
        window.opener.document.getElementById("hidden_'.$inputname.'").value = fileId[id];
        window.close();
        window.opener.focus();
    } else {
        window.parent.document.getElementById("selectfile_'.$inputname.'").value = fileName[id];
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
