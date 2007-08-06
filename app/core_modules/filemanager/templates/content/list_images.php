<?php
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_filemanager_listofimages', 'filemanager', 'List of Images');

echo $header->show();

$this->objThumbnails = $this->getObject('thumbnails');

$objFileEmbed = $this->getObject('fileembed');

if (count($files) == 0) {
    echo '<p>No Images uploaded</p>';
} else {

    foreach ($files as $file)
    {
        $link = new link ($this->uri(array('action'=>'fileinfo', 'id'=>$file['id'], 'type'=>$file['category'], 'filename'=>$file['filename'])));
        $link->link = $objFileEmbed->embed($this->objThumbnails->getThumbnail($file['id'], $file['filename']), 'image');
        
        echo '<div style="width: 110px; height: 110px; float: left;">'.$link->show().'</div>';
    }


}

echo '<br clear="left" />';

echo $this->objUpload->show();

?>