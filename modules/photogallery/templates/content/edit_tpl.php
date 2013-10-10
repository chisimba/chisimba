<?php
$flickrTables = NULL;
$this->loadClass('htmltable','htmlelements');
$link = $this->getObject('link','htmlelements');
$icon = $this->getObject('geticon', 'htmlelements');
$cnt =0 ;
$str = '';
if(count($arrAlbum) > 0)
{
	foreach($arrAlbum as $album)
	{
		$cnt++;
		$table = new htmltable();
		$table->cellspacing='0';
		$table->width = '100%';
		$table->startRow();
		$link->href = $this->uri(array('action' => 'editalbum', 'albumid' => $album['id']));
		$link->link = '<img height="40" width="40" src="'.$this->_objDBImage->getThumbNailFromFileId($album['thumbnail']).'" />';
		$table->addCell($link->show(),20);
		$link->link = $album['title'];
		$table->addCell($link->show());
		$link->href = 'javascript: confirmDeleteAlbum(\'?page=edit&action=deletealbum&album=zach\');';
		$icon->setIcon('delete');
		$link->link = $icon->show(); 
		$table->addCell($icon->getDeleteIconWithConfirm($album['id'],array('action' => 'deletealbum', 'albumid' => $album['id']),'photogallery'),null,null,'right');
		$table->endRow();
		$str .= '<div id="id_'.$cnt.'">'.$table->show().'</div>';
		
	}
}
$table = new htmltable();
$table->cssClass = 'bordered';
$table->width = '400px';
$table->startHeaderRow();
$table->addHeaderCell('Thumb',55);
$table->addHeaderCell('Edit this album');
//$table->addHeaderCell('Is Shared');
$table->endHeaderRow();

$table->startRow();
$table->addCell('<div id="albumList" class="albumList">'.$str.'</div>',null,null,null,null,'colspan="2" style="padding: 0px 0px;"');
$table->endRow();


//flickr tables

if(count($flickrusernames) > 0)
{
	
	foreach($flickrusernames as $username)
	{
		
		$user = $this->_objFlickr->people_findByUsername($username['flickr_username']);//$f->photos_getRecent();
		$sets = $this->_objFlickr->photosets_getList($user['id']);
//print '<pre>';
//print count($sets['photoset']);
		//var_dump($sets);
		if(count($sets['photoset']) > 0)
		{
		 $strF = '';
			foreach($sets['photoset'] as $set)
			{
				$photos = $this->_objFlickr->photosets_getPhotos($set['id']);
				foreach($photos['photo'] as $photo)
				{
				 	if($photo['isprimary'] == 1)
				 	{
						$thumb = '<img height="40" width="40" src="'.$this->_objFlickr->buildPhotoURL($photo, "Square").'">';
					}
				}
				
				$tbl2 = new htmltable();
				//$tbl2->cssClass = 'bordered';
				$tbl2->cellspacing='0';
				$tbl2->width = '100%';
				$tbl2->startRow();
				
				$link->href = $this->uri(array('action' => 'editalbum', 'albumid' => $album['id']));
				$link->link =$thumb;
				$tbl2->addCell($thumb,20);
				
				$link->link = $set['title'];
				$tbl2->addCell($set['title']);
				
				$link->href = 'javascript: confirmDeleteAlbum(\'?page=edit&action=deletealbum&album=zach\');';
				$icon->setIcon('delete');
				$link->link = $icon->show(); 
				//$tbl2->addCell($icon->getDeleteIconWithConfirm($album['id'],array('action' => 'deletealbum', 'albumid' => $album['id']),'photogallery'),null,null,'right');
				$tbl2->endRow();
				$strF .= '<div id="'.$set['title'].'">'.$tbl2->show().'</div>';
				$tbl2 = null;
			}	
		} else {
			$strF = 'No Albums Available';
		}	
		$tbl = new htmltable();
		$tbl->cssClass = 'bordered';
		$tbl->width = '400px';
		$tbl->startHeaderRow();
		$tbl->addHeaderCell('Thumb',55);
		$tbl->addHeaderCell('Edit this album');
		//$table->addHeaderCell('Is Shared');
		$tbl->endHeaderRow();
		
		$tbl->startRow();
		$tbl->addCell('<div id="albumList2" class="albumList2">'.$strF.'</div>',null,null,null,null,'colspan="2" style="padding: 0px 0px;"');
		$tbl->endRow();
		
		$flickrTables .= '<h2>'.$username['flickr_username'].'</h2>'.$tbl->show();
		$tbl = null;
	}
	
	$flickrTables = '<br/><div style="padding: 15px;" id="box" class="box"><img src="http://l.yimg.com/www.flickr.com/images/flickr_logo_gamma.gif.v1.5.7"><br/>'.$flickrTables.'</div>';
} else {
	$flickrTables = '';
}


//end of flickr










$script = '  <script type="text/javascript" src="'.$this->getResourceUri('admin.js','photogallery').'"></script>	
<script src="'.$this->getResourceUri('scriptaculous/prototype.js','photogallery').'" type="text/javascript"></script>
		<script src="'.$this->getResourceUri('scriptaculous/scriptaculous.js','photogallery').'" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript"><!--
			function populateHiddenVars() {
									document.getElementById(\'albumOrder\').value = Sortable.serialize(\'albumList\');
								
									return true;
			}
			//-->
		</script>';

$this->appendArrayVar('headerParams', $script);
echo '<div id="main"><h2>Edit Gallery</h2>

Drag the albums into the order you wish them displayed. Select an album to edit its description and data';
echo $table->show();

echo $flickrTables;
?>
        
                
        <div>
      		<form action="<?php echo $this->uri(array('action' => 'savealbumorder'), 'photogallery') ?>" method="POST" onSubmit="populateHiddenVars();" name="sortableListForm" id="sortableListForm">
						<input type="hidden" name="albumOrder" id="albumOrder" size="60">
						<input type="hidden" name="sortableListsSubmitted" value="true">
						<input type="submit" value="Save Order" class="button">
						
		</form>
		      </div></div>
			  
			  <script type="text/javascript">
			// <![CDATA[
							Sortable.create('albumList',{tag:'div'});
							// ]]>
		 </script>