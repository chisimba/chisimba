<?php
$objH = & $this->newObject('htmlheading', 'htmlelements');

$objIcon = & $this->newObject('geticon', 'htmlelements');
$objLink = & $this->newObject('link', 'htmlelements');
$objForm = & $this->newObject('form', 'htmlelements');
$objInput = & $this->newObject('textinput', 'htmlelements');
$objFeatureBox = & $this->newObject('featurebox', 'navigation');
$tabBox = & $this->newObject('tabpane', 'htmlelements');


if(count($galleries) > 0)
{
    
    
    
    
   $maxrownum = 4;
    foreach ($galleries as $gals)
    { 
      
       $galThumbs = $this->_objUtils->getGalleryThumbs($gals);
      // print count($galThumbs);
      if(count($galThumbs) > 0)
       {
           $rowcount = 0;
            $i=0;
            $str = '<table width="60%">';
           foreach ($galThumbs as $thumb)
           {
               $i++;
               $admin = '<div class="shade">
<img src="modules/photogallery/resources/shadow.png" width="0" height="0" alt="" class="shade" />
bla bla</div>';
               $image = '<div class="shade"><img src="usrfiles/galleries/'.$gals.'/thumbnails/'.$thumb['name'].'" alt="'.$thumb['name'].'" height="'.$thumb['height'].'" width="'.$thumb['width'].'"></div>';
               //$image .= $admin;
               $str .= ($i ==0) ? '<tr>' : '';
               $str .='<td>'.$image.'</td>';
               
               $str .=  ($i == $maxrownum) ? '</tr>' : '';
               
               $i = ($i == $maxrownum) ? 0: $i;
               
               /*$oddOrEven = ($rowcount == 0) ? "even" : "odd";
               $tableRow = array($i,
                            $thumb['name'], 
                            $image);
                $objTable->addRow($tableRow, $oddOrEven);
                $rowcount = ($rowcount == 0) ? 1 : 0;
                $i++;
        //       print $thumb['name'];
        */
           }
           $str .= '</table>';
       }
       
       
       $tabBox->addTab(array('name'=> strval($gals),'content' => $str));	
       
       //echo $objFeatureBox->show(ucwords($gals) , $objTable->show());
       $objTable = null;
    }
} else {
    print $this->objLanguage->languageText('mod_photogallery_nogallery'); //'no galleries avalable ';
}

echo $tabBox->show()
?>
