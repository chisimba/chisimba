<?php





$objFileIcon = $this->getObject('fileicons', 'files');
$this->loadClass('link', 'htmlelements');

echo '
<div style="position:absolute; bottom: 0px; baackground-color:#999933; z-index: 100; left: 0; height: 100px; margin-bottom: 20px;">';
echo '<h1>Upload Files</h1>';

echo $this->objUpload->show();

echo '</div>';

echo '<div style="width: 30%; baackground-color:#FFFF00; top: 0; left: 0; position: absolute; overflow:auto; bottom: 120px;  z-index:1; padding-bottom: 100px;">';

echo '<h1>List of Files</h1>';

$objTreeFilter =& $this->getObject('treefilter');
echo $objTreeFilter->showDropDown();

echo '<br /><br />';

if (count($files) == 0) {
    echo ' No files matching criteria found';
} else {
    echo '<ul>';
    
    $count = 0;
    
    $fileIdArray = 'fileId = new Array('.count($files).');';
    $filenameArray = 'fileName = new Array('.count($files).');';
    
    foreach ($files as $file)
    {
        $link = new link ("javascript:xajax_generatePreview('".$file['id']."', '".$count."');");
        $link->link = htmlentities($file['filename']);
        
        $icon = $objFileIcon->getFileIcon($file['filename']);
        echo '<li>'.$icon.' '.$link->show().'</li>';
        
        
        $fileIdArray .= 'fileId['.$count.'] = "'.$file['id'].'";';
        $filenameArray .= 'fileName['.$count.'] = \''.($icon.' '.htmlentities($file['filename'])).'\';';
        
        $count++;
    }
    echo '</ul>';
    
    $script = '<script type="text/javascript">

    '.$fileIdArray.'
    
    '.$filenameArray.'
</script>';

    $this->appendArrayVar('headerParams', $script);
    
    $checkOpenerScript = '
<script type="text/javascript">
function selectFile(file, id)
{
    if (window.opener) {
        
        //alert(fileName[id]);
        window.opener.document.getElementById("selectfile_'.$inputname.'").innerHTML = fileName[id];
        window.opener.document.getElementById("hidden_'.$inputname.'").value = fileId[id];
        window.close();
    }
}
</script>
        ';
        
        $this->appendArrayVar('headerParams', $checkOpenerScript);
}

?>
</div>
<div style="width: 70%; baackground-color:#FF00FF;  overflow:auto; top: 0; left: 30%; bottom: 120px; position: absolute; z-index:1;" id="previewwindow" >
<h1> Todo: Fix up IE</h1>
</div>