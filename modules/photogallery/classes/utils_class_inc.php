<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* class to control the utilty method for the events calendar
*
* @author Wesley Nitsckie
* @copyright (c) 2005 University of the Western Cape
* @package photogallery
* @version 1
*
*
*/
class utils extends object
{

    /**
     * Constructor
     */
    public function init()
    {
       $this->_objUser = $this->getObject('user', 'security');
       $this->objLanguage = & $this->getObject('language','language');
       $this->_objConfig = & $this->getObject('altconfig','config');
       $this->_objDBAlbum = & $this->getObject('dbalbum', 'photogallery');
       $this->_objDBImages = & $this->getObject('dbimages', 'photogallery');
       $this->galFolder = & $this->_objConfig->getcontentBasePath().'photogallery';
       $this->_objTags = & $this->getObject('dbtags', 'tagging');
       $this->_objFileMan = & $this->getObject('dbfile','filemanager');
       $this->_zipManager = & $this->getObject('archivefactory','archivemanager');
    }

    /**
     * Method to get the navigation
     *
     */
    public function getNav()
    {
       // $this->loadClass('sidebar' , 'navigation');
        if ($this->_objUser->isLoggedIn())
        {
			
			
			$objSideBar = $this->getObject('sidebar', 'navigation');//new sidebar();
			$nodes[] = array('text' => 'View Photos', 'uri' => $this->uri(array('action' => 'front')),  'nodeid' => 'front');
			$nodes[] = array('text' => 'Overview', 'uri' => $this->uri(array('action' => 'overview')),  'nodeid' => 'overview');
			$nodes[] = array('text' => 'Comments', 'uri' => $this->uri(array('action' => 'comments')),  'nodeid' => 'comments');
			$nodes[] = array('text' => 'Upload', 'uri' => $this->uri(array('action' => 'uploadsection')),  'nodeid' => 'uploadsection');
			$nodes[] = array('text' => 'Edit', 'uri' => $this->uri(array('action' => 'editsection')),  'nodeid' => 'editsection');
			$nodes[] = array('text' => 'Flickr', 'uri' => $this->uri(array('action' => 'flickr')),  'nodeid' => 'flickr');
	
			return $objSideBar->show($nodes, $this->getParam('action'));
		} else {
			return FALSE;
		}
    }
  
    /**
     *Method to do the upload process
     *@return boolean
     *@access public
     *@param string $albumId optional     
     */
     public function doUpload($albumId = null)
     {
        
        //upload images and add entries to the database
        $results = $this->uploadImages();       
        
		//var_dump($results);
        //check the album 
        if($albumId == null)
        {
            //if new album then add to database and get the new id
            $albumId = $this->_objDBAlbum->createAlbum($this->getParam('albumtitle'));
        }
        
        $this->insertToImagesTable($albumId ,$results);
        
     }                   
  
  
  	/**
  	* Method to upload the photos
  	* @return array
  	*/
  	public function uploadImages()
  	{
		$objUpload =& $this->newObject('upload', 'filemanager');
		$objUpload->setUploadFolder('users/'.$this->_objUser->userId().'/photos/');
		$results = $objUpload->uploadFilesArray('files');

                
                //echo"<pre>Before";
                //print_r($results);
                //echo"</pre>";
                
                /*
                $objDir = $this->getObject('mkdir','files');
                $SupportedMime = $this->_zipManager->getSupportedTypes();
                $filePath = $this->_objConfig->getcontentBasePath().'users/'.$this->_objUser->userId().'/photos/ArchiveTemp';
                $filePath = str_replace("/","\\",$filePath);
                $objDir->mkdirs($filePath,0777);

                
                foreach ($_FILES["files"]["error"] as $key => $error) 
                {
                    if ($error == 0)
                    {
                        //echo "Name ".$_FILES["files"]["name"][$key]."<br/>";
                        //echo "Temp name ".$_FILES["files"]["tmp_name"][$key]."<br/>";
                        
                        $UploadedFileMime = $_FILES["files"]["type"][$key];
                        if( in_array($UploadedFileMime,$SupportedMime) )
                        {
                            $UploadedFilePath = $_FILES["files"]["tmp_name"][$key];
                            $UploadedFileName = $_FILES["files"]["name"][$key];
                            move_uploaded_file($UploadedFilePath, $filePath);
                            //$archive = $this->_archiveManager->open( $filePath."/".$UploadedFileName );
                            //$archive->extractTo($filePath);
                        }
                    }
                }

                */
            
                $objMime = $this->getObject('mimetypes','files');
                $objDir = $this->getObject('mkdir','files');
                $mimetypes = $this->_zipManager->getSupportedTypes();
                $objAnalyzeMediaFile = $this->getObject('analyzemediafile','filemanager');
                $objMediaFileInfo = $this->getObject('dbmediafileinfo','filemanager');
                $objThumbnails = $this->getObject('thumbnails','filemanager');

                if($results)
                {
                    foreach($results as $file)
                    {
                        
                        if( in_array($file['mimetype'],$mimetypes) )
                        {
                            $filePath = $this->_objFileMan->getFilePath($file['fileid']);
                            $filePath = str_replace(".","_",$filePath);
                            $fullFilePath = $this->_objFileMan->getFullFilePath($file['fileid']);
                            $fullFilePath = str_replace(".","_",$fullFilePath);
                            //echo $fullFilePath."<br/>";
                            $zip = $this->_zipManager->open( $this->_objFileMan->getFilePath($file['fileid']) );
                            $objDir->mkdirs($fullFilePath);
                            $zip->extractTo($fullFilePath);
                            //echo $fullFilePath."<br/>";
                            $this->_objFileMan->deleteFile($file['fileid'],TRUE);

                            //list files in
                            $handle = opendir($fullFilePath);
                            if ($handle)
                            {
                                while (false !== ($dirfile = readdir($handle)))
                                {
                                    $mimetype = $objMime->getMimeType($fullFilePath."/".$dirfile);
                                    if(strstr($mimetype,"image/"))
                                    {
                                        $infoArray = array();
                                        // 1) Add to Database
                                        $fileId = $this->_objFileMan->addFile($dirfile, $fullFilePath."/".$dirfile, filesize($fullFilePath."/".$dirfile), $mimetype, "images", 1, $this->_objUser->userId(), NULL, $this->getParam('creativecommons_files',''));

                                        // Get Media Info
                                        $fileInfo = $objAnalyzeMediaFile->analyzeFile($fullFilePath."/".$dirfile);

                                        // Add Information to Databse
                                        $objMediaFileInfo->addMediaFileInfo($fileId, $fileInfo[0]);

                                        // Check whether mimetype needs to be updated
                                        if ($fileInfo[1] != '') {
                                            $this->_objFileMan->updateMimeType($fileId, $fileInfo[1]);
                                        };

                                        $objThumbnails->createThumbailFromFile($fullFilePath."/".$dirfile, $fileId);
                                        
                                        // Update Return Array Details
                                        $infoArray['fileid'] = $fileId;
                                        $infoArray['success'] = TRUE;
                                        $infoArray['path'] = $filePath."_temp";
                                        $infoArray['fullpath'] = $fullFilePath."/";
                                        $infoArray['subfolder'] = "images";
                                        $infoArray['originalfolder'] = "images";
                                        $infoArray['name'] = $dirfile;
                                        $infoArray['mimetype'] = $mimetype;
                                        $infoArray['errorcode'] = 0;
                                        $infoArray['size'] = filesize($fullFilePath."/".$dirfile);

                                        $results[$dirfile] = $infoArray;
                                    }
                                    else
                                    {
                                        @unlink($fullFilePath."/".$dirfile);
                                    }
                                 }
                             }
                            closedir($handle);
                            
                        }
                      }
                    }
                //$results = $objUpload->uploadFilesArray('files');
                
                //echo"<pre>After";
                //print_r($results);
                //echo"</pre>";
                

		return $results;
	}
  
  	/**
  	* Method to insert the details into the
  	* database
  	* @param array $results The results of the upload
	* @return boolean
	* @access public
	*/
	public function insertToImagesTable($albumId = '', $results = null)
	{
		if($results == null)
		{
			return FALSE;
		} 
		
		
		
		//$albumId = $this->getParam('albumselect');
                $imageCount = count($this->_objDBImages->getAll("WHERE album_id= '".$albumId."'"));
		foreach($results as $result)
		{
		    if (!isset ($result['fileid'])) {
			$result['fileid'] = '';
		    }
			if($result['fileid'] != '')
			{
                            $imageCount++;
			    $fields = array();
			    $fields['file_id'] = $result['fileid'];
			    $fields['album_id'] = $albumId;
                            $fields['position']=$imageCount;
			
			    $this->_objDBImages->insertImageData($fields);
				
			    if(!$this->_objDBAlbum->hasThumb($albumId))
				{
					$this->_objDBAlbum->update('id', $albumId, array('thumbnail' =>$result['fileid'] ));
				}	
				
				//	var_dump($fields);
			}
			
		}
		
	}
  
  
	/**
	* Method to save the edit for albums
	*/
	public function saveAlbumEdit()
	{
	
		
		$objDBAlbum = & $this->getObject('dbalbum', 'photogallery');
		$objDBImage = & $this->getObject('dbimages', 'photogallery');
		
		
		$isshared = ($this->getParam('isshared') == 'on')? 1 : 0;
		
		$fields = array('title' => $this->getParam('albumtitle'),                        
                        'no_pics' => $this->getParam('totalimages'),                       
                        'is_shared' => $isshared,
                        'description' => $this->getParam('description'),
                        'thumbnail' =>  $this->getParam('thumbselect'));
                        
                        //var_dump($fields);die;
		$objDBAlbum->updateAlbum($this->getParam('albumid'), $fields);
		
		$tagsArr = spliti(',',$this->getParam('tags'));
		$uri = $this->uri(array('action' => 'viewalbum', 'albumid' => $this->getParam('albumid')));
		
		$this->_objTags->insertTags($tagsArr, $this->_objUser->userId(), $this->getParam('albumid'), 'photogallery', $uri);
		
		$imgCnt = $this->getParam('totalimages');
		for($i = 0; $i < $imgCnt; $i++)
		{
		 	$id = $this->getParam($i.'-id');
			$fields = array('title' => $this->getParam($i.'-title'),
							'description' => $this->getParam($i.'-desc'));
			$objDBImage->updateImage($id, $fields);
		}
	}
  
  
	  /**
	  * Method to delete an album
	  * this will delete all images and comments on 
	  * album
	  * @param string $albumId
	  * @access public
	  */
  	public function deleteAlbum($albumId)
  	{
		$objDBAlbum = & $this->getObject('dbalbum', 'photogallery');
		$objDBImage = & $this->getObject('dbimages', 'photogallery');
		$objDBComments = & $this->getObject('dbcomments', 'photogallery');
		
		//get all in images
		$images = $objDBImage->getAll("WHERE album_id='".$albumId."'");
		foreach($images as $image)
		{
			
			$objDBComments->delete('file_id',$image['id']);
		}
		
		$objDBImage->delete('album_id', $albumId);
		$objDBAlbum->delete('id', $albumId);
	}
  
   /**
	  * Method to delete an image
	  * this will delete all  comments on 
	  * image
	  * @param string $imageId
	  * @access public
	  */
  	public function deleteImage($imageId)
  	{
  		$objDBImage = & $this->getObject('dbimages', 'photogallery');
		$objDBComments = & $this->getObject('dbcomments', 'photogallery');
		
		$objDBComments->delete('file_id', $imageId);
		$objDBImage->delete('id', $imageId);
  
  	}
  
  
  	/**
  	* Method to generate the navigation for the image
  	* @param string $imageId
  	* @access public
  	*/
  	public function getImageNav($imageId)
  	{
  	 	$link = $this->getObject('link' , 'htmlelements');
  	 	$objThumbnail = & $this->getObject('thumbnails','filemanager');
  	 
  	 	$objDomTT = $this->getObject('domtt','htmlelements');
  	 	$objDomTT->putScripts();
  	 	
  	 	$nextStr = '';
  	 	$prevStr = '';
  	 	$image = $this->_objDBImages->getRow('id', $imageId);
  	 	if($image['position'] > 1)
  	 	{
  	 	 	$sql = "WHERE album_id='".$image['album_id']."' AND position = ".(intval($image['position'])-1);
  	 	 	$prevImage = $this->_objDBImages->getAll($sql);	
			$prevStr = '<div class="imgprevious">';
			
			$link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $image['album_id'] , 'imageid' => $prevImage[0]['id']));
			$link->link = '&laquo; prev';
			$link->extra = 'onmouseover="domTT_activate(this, event, \'content\', document.getElementById(\'previousimage\'));"';
			
			$prevStr .= $link->show().'</div>';
			
			
			$filename = $this->_objFileMan->getFileName($prevImage[0]['file_id']); 
 			$path = $objThumbnail->getThumbnail($prevImage[0]['file_id'],$filename);
			$prevStr .= '<div  style="display: none">
							<div id="previousimage" >		
								<img src="'.$path.'" />
							</div>
						</div>';

		}
		
		$imageCount = count($this->_objDBImages->getAll("WHERE album_id= '".$image['album_id']."'"));
		
		if($imageCount != ($image['position']))
		{
			
			$sql = "WHERE album_id='".$image['album_id']."' AND position = ".(intval($image['position'])+1);
  	 	 	$nextImage = $this->_objDBImages->getAll($sql);	
  	 	 	if (array_key_exists(0, $nextImage))  	 	 	
  	 	 	{
				$nextStr = '<div class="imgnext">';
			
				$link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $image['album_id'] , 'imageid' => $nextImage[0]['id']));
				$link->link = 'next &raquo;';
				$link->extra = 'onmouseover="domTT_activate(this, event, \'content\', document.getElementById(\'nextimage\'));"';
				
				$nextStr .= $link->show().'</div>';
				
				
				$filename = $this->_objFileMan->getFileName($nextImage[0]['file_id']); 
 				$path = $objThumbnail->getThumbnail($nextImage[0]['file_id'],$filename);
				$nextStr .= '<div  style="display: none">
							<div id="nextimage" >		
								<img src="'.$path.'" />
							</div>
						</div>';
			}
		}
	
  	 	$nav = 
		'<div class="imgnav">'.
			$prevStr.
			$nextStr.
			'</div>';
			
		return $nav;
		
	}
	
	/**
	* Method tp generate Flickr photos 
	* navigation
	*
	* @return string
	* @param object $objFlick
	*/
	public function getFlickrImageNav($objFlickr)
	{
	 	$imageId = $this->getParam('imageid');
		$arrImages = $objFlickr->photosets_getPhotos($this->getParam('albumid'));
		
		
		
		return '';
	}
  	
   /**
   * Method to get a list of tags for 
   * an image
   *  @param string $imageId
   * @return string
   */
   public function getTagLIst($imageId)
   {	$tagsStr = '';
		$link = $this->getObject('link', 'htmlelements');
		$tagsArr = $this->_objTags->getPostTags($imageId, 'photogallery');
		if(count($tagsArr) > 0 )
		{
			$cnt = 0; 	
			$max = count($tagsArr);
			foreach($tagsArr as $t)
			{
			
		 		$cnt++;
		 		$link->href = $this->uri(array('action'=>'deletetag' , 'tagid' => $t['id'], 'albumid' => $this->getParam('imageId')));
		 		$link->link = 'x';
		 		$tagsStr .= $t['meta_value'].'['.$link->show().']';
				$tagsStr .= ($cnt < $max) ? ', ' : '';
			//	$tagsStr .= ', ';
			
			}
		} else {
			$tagsStr = '';
		}
		
		return $tagsStr;
	}
  
  	
  /**
  * Method to generate a tag cloud
  * 
  * @return string
  */
  public function getPopular()
  {
		$objTagCloud = $this->getObject('tagcloud', 'utilities');
		//	return $objTagCloud->exampletags();
		
		$tags = $this->_objTags->getTagsByModule('photogallery');
		
		if (count($tags) > 0 )
		{
			foreach ($tags as $tag)
			{
			 	$weight = $this->_objTags->getRecordCount("WHERE meta_value='".$tag['meta_value']."'");
			// print $weight.' - '.$tag['meta_value'].'<br>';
			 	$uri = $this->uri(array('action' => 'popular', 'meta_value' => $tag['meta_value']));
			 	
				$objTagCloud->addElement($tag['meta_value'],$uri  , $weight, time());
			}
			
			return $objTagCloud->biuldAll();
		} else {
			return  $this->objLanguage->languageText("mod_photogallery_nopoptags", "photogallery");
		}
		
	
  }
  
  
  /**
  * Method to generate a list of images for a given tag
  * @param string $tag
  * @return string
  */
  public function getTaggedImages($tag)
  {
   		$objTag = $this->getObject('dbtags', 'tagging');
   		$taggedList = $objTag->getAll("WHERE meta_value='".$tag."' AND module='photogallery'");
   		$str = '';
   	//	print '<pre>';
   		
   	//	var_dump()
		//$images = $this->_objDBImages->getAll("WHERE is_shared=0 AND ORDER BY no_views DESC LIMIT 8");
		$objThumbnail = & $this->getObject('thumbnails','filemanager');
		$link = $this->getObject('link','htmlelements');
		
		$arrCheck = array();
		
		foreach($taggedList as $tagged)
		{
		 	if(!in_array($tagged['item_id'],$arrCheck))
		 	{
				
			
		 	$image = $this->_objDBImages->getRow('id', $tagged['item_id']);
		 	
			$str.='<div class="image">
					<div class="imagethumb">';
			$filename = $this->_objFileMan->getFileName($image['file_id']); 
	 		$path = $objThumbnail->getThumbnail($image['file_id'],$filename);
	 		$bigPath = $this->_objFileMan->getFilePath($image['file_id']);
		 	$link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $image['album_id'],'imageid' => $image['id']));
		 	$link->link = '<img title="'.$image['title'].' - '.$image['no_views'].' Hits" src="'.$path.'" alt="'.$image['title'].'"  />';
		 	$link->extra = ' rel="lightbox" ';
			$str.=$link->show().'</div></div>';
			
			$arrCheck[] = $tagged['item_id'];
			}
		}
		
			return '<div style="padding: 10px; margin-top:10px; margin-left:5px; border: solid 2px #eee; width:550px; background: #f5f5f5;">
				<h2>Photos tagged as \''.$tag.'\'</h2>
				<div style = "display:table-row-group; margin-bottom:2em; top:300px; position:static; padding: 10px; margin-top:10px; margin-left:5px; border: solid 2px #eee; width:550px; background: #f5f5f5;">
			'.$str.'</div></div>';
	
	
  }
  
  /**
  * Method to get the top ten photos
  * 
  * @access public
  */
  public function getPopularPhotos()
  {
		$images = $this->_objDBImages->getAll("WHERE is_shared=0 ORDER BY no_views DESC LIMIT 8");
		$objThumbnail = & $this->getObject('thumbnails','filemanager');
		$link = $this->getObject('link','htmlelements');
		$str = '';
		if(count($images) > 0)
		{
			
			foreach($images as $image)
			{
				$str.='<div class="image"><div class="imagethumb">';
				$filename = $this->_objFileMan->getFileName($image['file_id']); 
		 		$path = $objThumbnail->getThumbnail($image['file_id'],$filename);
		 		$bigPath = $this->_objFileMan->getFilePath($image['file_id']);
			 	$link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $image['album_id'],'imageid' => $image['id']));
			 	$link->link = '<img title="'.$image['title'].' - '.$image['no_views'].' Hits" src="'.$path.'" alt="'.$image['title'].'"  />';
			 	$link->extra = ' rel="lightbox" ';
				$str.=$link->show().'</div></div>';
			}
			
			return '<div style="padding: 10px; margin-top:10px; margin-left:5px; border: solid 2px #eee; width:550px; background: #f5f5f5;">
						<h3> Popular Images</h3>
						<div style = "display:table-row-group;  padding: 10px; margin-top:10px; margin-left:5px; border: solid 2px #eee; width:550px; background: #f5f5f5;">
						'.$str.'</div></div>';
		}
  }
  
  
  
}
?>
