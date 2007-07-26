<?php
$this->setVar('suppressFooter', TRUE);

$objFileIcon = $this->getObject('fileicons', 'files');
$objThumbnail = $this->getObject('thumbnails', 'filemanager');

$this->loadClass('link', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('layer', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('image', 'htmlelements');

$listImages = $this->objLanguage->languageText('mod_filemanager_listofimages', 'filemanager');
$noMatch = $this->objLanguage->languageText('mod_filemanager_nomatch', 'filemanager');
$previewFile = $this->objLanguage->languageText('mod_filemanager_previewfile', 'filemanager');
$insertImage = $this->objLanguage->languageText('mod_filemanager_insertimage', 'filemanager');
$uploadFile = $this->objLanguage->languageText('phrase_uploadfile');

$objHeading = new htmlheading();
$objHeading->str = $listImages;
$objHeading->type = 1;
$heading = $objHeading->show();

$objLayer = new layer();
$objLayer->position = 'absolute; top: 10px; right: 10px; left: 10px';
$objLayer->height = '50px';
$objLayer->zIndex = '1';
$objLayer->addToStr($heading);
$str = $objLayer->show();

$string = '';
if (count($files) == 0) {
    $string = '<ul><li><b>'.$noMatch.'</b></li></ul>';
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
        $link->title = $previewFile;
        
        $selectLink = new link ("javascript:selectImage('".$file['id']."', '".$count."');");
        $selectLink->link = $insertImage;
        
        
        $thumbImg = $this->uri(array('action'=>'thumbnail', 'id'=>$file['id']));
/*        
        $string = '
<div style="width: 120px; margin-bottom:20px;" class="floatlangdir">
    <div style="line-height:120px; vertical-align:center; text-align:center;">
    <img src="'.$thumbImg.'" style="vertical-align:middle;" />
    </div>
    <div style="text-align:center;">
        '.$selectLink->show().'
    </div>
</div>
';
*/
        $objImage = new image();
        $objImage->src = $thumbImg;
        $objImage->align = 'middle';
        $image = $objImage->show();
        
        $objLayer = new layer();
        $objLayer->cssClass = 'imageDiv';
        $objLayer->addToStr($image);
        $image = $objLayer->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'linkDiv';
        $objLayer->addToStr($selectLink->show());
        $link = $objLayer->show();

        $objLayer = new layer();
        $objLayer->cssClass = 'floatlangdir';
        $objLayer->width = '120px; margin-bottom: 20px;';
        $objLayer->addToStr($image.$link);
        $string .= $objLayer->show();

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

//    if (count($defaultItem) > 0) {
        //$this->appendArrayVar('bodyOnLoad', "previewFile('".$defaultItem['id']."', '".$defaultItem['count']."');");
//    }
    
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
<style type="text/css">
    div.imageDiv{
        line-height:120px;
        text-align:center;        
    }
    div.linkDiv{
        text-align:center;
    }
</style>
        ';
        
        $this->appendArrayVar('headerParams', $checkOpenerScript);
        
        
}

$objLayer = new layer();
$objLayer->position = 'absolute; top: 60px; right: 10px; bottom: 110px; left: 10px';
$objLayer->zIndex = '2; overflow-y:scroll; overflow-x:hidden';
$objLayer->addToStr($string);
$str .= $objLayer->show();

$objHeading = new htmlheading();
$objHeading->str = $uploadFile;
$objHeading->type = 1;
$string = $objHeading->show();

$this->objUpload->formaction = $this->uri(array('action'=>'selectfileuploads'));
$this->objUpload->numInputs = 1;

$mode = new hiddeninput('mode', 'selectimagewindow');
$name = new hiddeninput('name', $this->getParam('name'));
$context = new hiddeninput('context', $this->getParam('context'));
$workgroup = new hiddeninput('workgroup', $this->getParam('workgroup'));
$restrict = new hiddeninput('restrict', $this->getParam('restrict'));
$value = new hiddeninput('value', $this->getParam('value'));

$this->objUpload->formExtra = $mode->show().$name->show().$context->show().$workgroup->show().$value->show().$restrict->show();

$string .= $this->objUpload->show();

$objLayer = new layer();
$objLayer->position = 'absolute; left: 10px; right: 10px; bottom: 10px';
$objLayer->height = '105px';
$objLayer->zIndex = '3';
$objLayer->addToStr($string);
$str .= $objLayer->show();

$objLayer = new layer();
$objLayer->cssClass = "featurebox";
$objLayer->position = 'absolute; top: 0px; right: 0px; bottom: 0px; left: 0px';
$objLayer->zIndex = '100';
$objLayer->addToStr($str);
echo  $objLayer->show();
?>