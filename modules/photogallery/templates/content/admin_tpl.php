<?php

$objH = $this->getObject('htmlheading', 'htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('geticon','htmlelements');
$this->loadClass('link','htmlelements');
//list files

//upload files
/*
$objSelectFile = $this->getObject('selectimage', 'filemanager');

$objDBFile = $this->getObject('dbfile', 'filemanager');
$objUpload = $this->getObject('upload', 'filemanager');

$this->objFile = $this->getObject('dbfile', 'filemanager');

$siteImages = $this->objFile->getAllOpenWebImages();

echo $siteImages;
$objSelectFile->name = 'photo';
print_r($objDBFile->getAllOpenImages());
echo $objUpload->show();
echo $objSelectFile->show();
*/

if(isset($imageArr))
{
    if(is_array($imageArr))
    {
        $str = '';
        $max = 4;
        $cnt = 0;
        $table = new htmltable();
        $table->width = '600px';
        $table->startRow();
        $table->cellpadding = 10;
        $table->cellspacing = 10;
        $icon = $this->newObject('geticon','htmlelements');
        $icon->setIcon('delete');

        foreach ($imageArr as $image)
        {
            $imageStr = '<img src="'.$this->_objConfig->getSiteRoot().'usrfiles/photogallery/thumbs/'.$image['id'].'.'.$image['datatype'].'" alt="'.$image['description'].'" >';
            $imgTable = $this->newObject('htmltable','htmlelements');

            $link = new link();
            $link->href = $this->uri(array('action' => 'deleteimage', 'fileid' => $image['id']));
            $link->link = $icon->show();

            //$table->endRow();
            $imgTable->startRow();
            $imgTable->addCell($imageStr,null,null,'center');
            $imgTable->endRow();

            $imgTable->startRow();
            $imgTable->addCell($link->show(),null,null,'center');
            $imgTable->endRow();


            if($cnt == $max)
            {
                $table->endRow();
                $table->startRow();
                $cnt = 0;
            }
            $table->addCell($imgTable->show());

            $cnt++;
            $imgTable = null;


        }
         $table->endRow();
    }

    $str = $table->show();
} else {
    $str = '<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">'.$this->objLanguage->languageText("mod_photogallery_noimages",'photogallery') .'</div>';

}
$objH->str = 'Photo Gallery Admin';
echo $objH->show();
echo $str;
echo $this->_objUtils->getAdminSection();

$icon = $this->newObject('geticon', 'htmlelements');
    $icon->setModuleIcon('photogallery');

    $link = $this->newObject('link','htmlelements');
    $link->href = $this->uri(null);
    $link->link = $icon->show().' '.$this->objLanguage->languageText("mod_photogallery_view",'photogallery');

    echo '<br/>'.$link->show();
?>