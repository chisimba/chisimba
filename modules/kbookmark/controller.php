<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * The kbookmark controller manages
 * the kbookmark module
 * @author James Kariuki Njenga
 * @version $Id: controller.php 16933 2010-02-24 10:59:39Z qfenama $
 * @copyright 2005, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package context
*/
class kbookmark extends controller
{
    /**
    * @var string $id
    */
    public $id;

    /**
    * @var object $objUser
    */
    public $objUser;
    
    /**
    * @var object $objLanguage
    */
    public $objLanguage;


    /**
    * @var object $objDbBookmark
    */
    public $objDbBookmark;
    
    /**
    * @var object $objDbGroup
    */
    public $objDbGroup;
    
    /**
    * @var object $objLink: Use to create links
    */
    public $objLink;
    
    /**
    * @var object $objIcon
    */
    public $objIcon;

    /**
    * @var string $xbelOutput: Store xbel format output
    */
    public $xbelOutput="";
   

    public $urlVal;
    /**
    * Method to initialize the controller
    *
    */
    
     
     
    function init()
    {
        $this->objUser= $this->getObject('user','security');
        $this->objDbBookmark = $this->newObject('dbbookmark','kbookmark');
        $this->objDbfolder = $this->newObject('dbfolder','kbookmark');
    }
	  

    public function dispatch($action='home')
    {
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
    }

    /**
    * 
    * Method to convert the action parameter into the name of 
    * a method of this class.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return stromg the name of the method
    * 
    */
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__home";
        }
    }

    /**
    * Default Action for Bookmark module
    * @access private
    */
    private function __home()
    {
		//Get Folder Details
		$userid = $this->objUser->userId();
		$this->setVarByRef('userId',$userid);
        return 'bookmarkhome_tpl.php';
    }

	/**
    * 
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action 
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    * 
    */
    function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

	function __getDir()
	{
		$allarr = array();
		$parentid = $this->getParam('node');
		$userid = $this->objUser->userId();		
		$dirs = $this->objDbfolder->getUserFolders($userid, $parentid);
		if(count($dirs) > 0)
		{
			foreach($dirs as $dir)
			{
				$arr['text'] = $dir['fname'];
				$arr['id'] = $dir['id'];
				$arr['cls'] = 'folder';
			$allarr[] = $arr;
			}
		}
		else
		{
			$allarr = array();
		}
		echo json_encode($allarr);
	}

	function __creatFolder()
	{
		$parentid = $this->getParam('parentfolder');
		$userid = $this->objUser->userId();
		$fname = $this->getParam('foldername');
		if($this->objDbfolder->isFolderExist($fname, $parentid, $userid)){
			$extjs['success'] = true;
            $extjs['error'] = "Folder name specified already exist";
            echo json_encode($extjs);
	    	exit(0);
		}
		else if(preg_match('/\\\|\/|\\||:|\\*|\\?|"|<|>/', $fname)){
	    	$extjs['success'] = true;
            $extjs['error'] = "Illigal charectors in a folder name";
            echo json_encode($extjs);
	    	exit(0);
        }
		else{
			$id = $this->objDbfolder->insertSingle($fname, $parentid, $userid);
			$extjs['success'] = true;
            $extjs['data'] = $id;
            echo json_encode($extjs);
	    	exit(0);
		}
		
	}

	/**
    * Method to get all the bookmarks for a specific folder
	*
    */
	function __getBookmarks()
	{
		$folderid = $this->getParam('id');
		$bookmarks = $this->objDbBookmark->getUserFolderBookmark($folderid);
		$count = count($bookmarks);
		if($count > 0)
		{
			$allarr = array();
			foreach($bookmarks as $bookmark)
			{
				$arr = array();
				$arr['id'] 			= $bookmark['id'];
        		$arr['title']		= $bookmark['title'];
        		$arr['url'] 		= $bookmark['url'];
	    		$arr['description'] = $bookmark['description'];
				$allarr[] = $arr;
			}
			echo json_encode(array('totalCount' => $count, 'bookmarks' =>  $allarr));
		}
		else
		{
			$allarr['totalCount'] = "0";
			$allarr['bookmarks'] = array();
			echo json_encode($allarr);
		}
		
	}
    
	/**
    * Method to add a bookmark
    *
    */
    function __addBookmark()
	{
		$folderid = $this->getParam('folderid');
		$title = $this->getParam('add_title');
        $url = $this->getParam('add_url');
	    $description = $this->getParam('add_description');
		$tags = $this->getParam('add_tags');
		$id = $this->objDbBookmark->insertSingle($folderid, $title, $url, $tags, $description);
		if($id)
		{
			echo json_encode(array('success' => true));
			exit(0);
		}
		else
		{
			echo json_encode(array('success' => false));
			exit(0);
		}
	}

	/**
    * Method to delete a bookmark
    *
    */
	function __deleteBookmark()
	{	

		$ids = $this->getParam('ids');

    	if ($ids) {
    		$bookmarkIds = substr_replace($ids, "",strlen($ids) - 1);
    		
			$bookmarks = explode(',', $bookmarkIds);
    		foreach ($bookmarks as $id)
    		{
				$res = $this->objDbBookmark->deleteBookmark($id, 'id');
    		}
     		echo json_encode(array('success' => true));
			exit(0);
		}
		else
		{
		    echo json_encode(array('success' => false));
			exit(0);
		}
	}

	/**
    * Method to update a bookmark
    *
    */

	function __upadateBookmark()
	{
		$id = $this->getParam('id');
		$title = $this->getParam('edit_title');
        $url = $this->getParam('edit_url');
	    $description = $this->getParam('edit_description');
		$tags = $this->getParam('edit_tags');
		$id = $this->objDbBookmark->updateBookmark($id, $title, $url, $description, $tags);
		
		echo json_encode(array('success' => true));
		exit(0);
	}

	/**
    * Method to get a single bookmark data
    *
    * returns array as json
    *
    */
	function __getSingleBookmark()
	{
		$id = $this->getParam('id');
		$bookmark = $this->objDbBookmark->getSingleBookmark($id);
		$arr = array();
		$arr['edit_title'] = $bookmark['title'];
		$arr['edit_url'] = $bookmark['url'];
		$arr['edit_description'] = $bookmark['description'];
		$arr['edit_tags'] = $bookmark['tags'];
		echo json_encode(array('success' => true, 'data' => $arr));
		exit(0);
	}

	/**
    * Method to delete a bookmark folder
    *
    */
	function __deleteFolder()
	{
		$folderid = $this->getParam('folderid');
		//first delete all the bookmark in the folder 
		$res = $this->objDbBookmark->deleteBookmark($folderid, 'folderid');
		//then delete folder
		$res = $this->objDbfolder->deleteFolder($folderid, 'id');
		echo json_encode(array('success' => true));
		exit(0);
	}

	/**
    * Method to open a bookmark on a new page
    *
    * returns string
    *
    */
    function __openPage()
    {
		$pageid = $this->getParam('pageid');
        $this->objDbBookmark->updateVisitHit($pageid);
        $list = $this->objDbBookmark->getSingleBookmark($pageid);
        $this->setVarByRef('list',$list);
		$url = $list['url'];
     
	    header("Location: ".$url);
        exit(0);
    }



  /*  
    function dispatch ($action)
    {

        //set the layout template
        $this->setLayoutTemplate('user_layout_tpl.php');
        $userId=$this->objUser->userId();
        $folderId=$this->getParam('folderId');
        
        $folderId=$this->objDbGroup->getDefaultId($userId);
        // Get the action to execute and execute it
        switch ($action) {       
            case 'add':
                $userId=$this->objUser->userId();
                $filter="where creatorid='$userId'";
                $listFolders=$this->objDbGroup->getAll($filter);
                $this->setVarByRef('listFolders',$listFolders);
                return 'add_tpl.php';
            
            case 'save_add':
                $options=$this->getParam('options');
                $item=$this->getParam('item');
                $cancel=$this->getParam('cancel');
                if (isset($cancel)){
                    if ($item=='folder') {
                        return $this->nextAction('options','','');
                    } else {
                        return $this->nextAction('',array('folderId'=>$folderId));
                    }
                } else {
                    return $this->doParse($item,$options);
                }
                
            case 'save_edit':
                $item=$this->getParam('item');
                $folderId=$this->getParam('folderId');
                $cancel=$this->getParam('cancel');
                if ($item=='folder') {
                    if (isset($cancel)){
                        return $this->nextAction('options','','');
                    } else {
                        $this->parse4Update($item);
                        $title=$this->objLanguage->languageText('mod_bookmark_editsaved ','kbookmark');
                        return $this->nextAction('options',array('folderId'=>$folderId,'title'=>$title,'status'=>'success'));
                    }
                } else {
                     if (isset($cancel)){
                        return $this->nextAction('',array('folderId'=>$folderId));
                    } else {
                         $folderId=$this->getParam('parent');
			 //echo $item;
                         $this->parse4Update($item);
                         $title=$this->objLanguage->languageText('mod_bookmark_editsaved ','kbookmark');
                         return $this->nextAction('',array('folderId'=>$folderId,'title'=>$title,'status'=>'success'));
                     }
                 }
                break;
            
            case 'edit':
                $item=$this->getParam('item');
                if ($item=='folder') {
                    $id=$this->getParam('folderId');
                } else {
                    $id=$this->getParam('id');
                }
                return $this->editItem($item,$id);

            case 'options':
                return $this->showFoldersOptions($userId);


            case 'setdefault':
                $folderId=$this->getParam('folderId');
                $this->setDefaultFolder($folderId);
                return $this->showFoldersOptions($userId);

            case 'delete':
                $item=$this->getParam('item');
                return $this->deleteItem($item);
                
            case 'openpage':
                $pageId=$this->getParam('id');
                $folderId=$this->getParam('folderId');
                $this->openPage($pageId);
                return $this->nextAction('',array('folderId'=>$folderId));
            
            case 'manage':
                $item=$this->getParam('item');
                $move=$this->getParam('move');
                $folderId=$this->getParam('folderId');
                if (isset($move)){
                    $operation=$this->getParam('move');
                } else {
                    $operation=$this->getParam('delete');
                }                
                $this->manage($operation, $item);
                
            case 'shared':
                 $sortOrder=$this->getParam('folderOrder');
				 $sortOption=$this->getParam('sortOrder');
                 if (isset($sortOrder)) {
                     $sortFilter="ORDER BY '$sortOrder' $sortOption";
                 } else {
                     $sortFilter="";
                 }
                 $filterFolder="WHERE isprivate='0'";
                 $listFolders=$this->objDbGroup->getAll($filterFolder);
				 $listFoldersWithBookmarks=$this->objDbGroup->getSharedWithBookmarks();
				 $listUsersWithBookmarks=$this->objDbGroup->getUsersWithSharedBookmarks();
                 $folderId=$this->getParam('folderId');
                 if (isset($folderId)){
                     $bookmarkFilter="WHERE isprivate='0' AND groupid='$folderId' ".$sortFilter;
                     $listFolderContent=$this->objDbBookmark->getAll($bookmarkFilter);
                 }
                 $this->setVarByRef('listFolderContent',$listFolderContent);
				 $this->setVarByRef('listFoldersWithBookmarks', $listFoldersWithBookmarks);
				 $this->setVarByRef('listUsersWithBookmarks', $listUsersWithBookmarks);
                 $this->setVarByRef('listFolders',$listFolders);
                 return "listshared_tpl.php";
                 
             case 'search':
                 $userId=$this->objUser->userId();
                 $searchTerm=$this->getParam('searchTerm');
                 $searchResults=$this->objDbBookmark->search($searchTerm,$userId);
                 $this->setVarByRef('searchResults',$searchResults);
                 $folderId=$this->getParam('folderId');
                 if (!isset($folderId)) {
                     $folderId=$this->getDefaultFolder($userId);
                 }
                 $filter="WHERE groupid='$folderId' and creatorid='$userId'";
                 $listFolderContent=$this->objDbBookmark->getAll($filter);
                 $this->setVarByRef('listFolderContent',$listFolderContent);
                 $filterFolder="where creatorid='$userId'";
                 $listFolders=$this->objDbGroup->getAll($filterFolder);
                 $this->setVarByRef('listFolders',$listFolders);
                 $this->setVarByRef('searchTerm',$searchTerm);
                 return 'list_tpl.php';
             case 'all':
			     $allBookmarks=$this->objDbBookmark->listAll();
				 $this->setVarByRef('allBookmarks',$allBookmarks);
				 return 'list_all_tpl.php';
				 break;
             case 'xbelparse':
                   $uploadXbel=$this->getParam('upload');
                   $viewXbel=$this->getParam('viewxbel');
                   if (isset($uploadXbel)) {
                         if (is_uploaded_file($_FILES['xbel']['tmp_name'])) {
                             if ($this->xbel->isAllowedFile($_FILES['xbel']['name'])=='.xml') {
                                 if ($this->xbel->xbelbookmark($_FILES['xbel']['tmp_name'])) {
                                     $this->xbel->xbelInsert();
                                     $status="success";
                                     $title=$this->objLanguage->languageText('mod_bookmark_xbeladded','kbookmark');
                                 } else {
                                     $status='failed';
                                     $title=$this->xbel->Error;
                                 }
                             } else {
                                 $status='failed';
                                 $title=$this->objLanguage->languageText('mod_bookmark_unrecognised','kbookmark');
                             }
                     } else {
                         $status='failed';
                         $title=$this->objLanguage->languageText('mod_bookmark_xbelnot','kbookmark');
                     }
                     return $this->nextAction('xbelparse',array('status'=>$status,'title'=>$title));
                    
                  // return 'list_tpl.php';
                   }  else {
                       if (isset($viewXbel)) {
                           $xbelOutput=$this->xbel->xbel();
                           $this->setVarByRef('xbelOutput',$xbelOutput);
                       }
                   }
                   return "xbel_tpl.php";
                   break;
            case 'viewxbel':
				$xbelOutput=$this->xbel->xbel($this->objUser->userId());
                $this->setVarByRef('xbelOutput',$xbelOutput);
				$this->setPageTemplate('no_page_tpl.php');

				//return "view_xbel.php";


				return "no_page_tpl.php"; 
				break;
			
		case 'allxbel':
			$xbelOutput=$this->xbel->xbel();
                	$this->setVarByRef('xbelOutput',$xbelOutput);
			return "view_xbel.php";
			break;

           case Null:
                $sortOrder=$this->getParam('folderOrder');
		$sortOption=$this->getParam('sortOrder');
                //$userId=$this->objUser->userId();
                 if (isset($sortOrder)) {
                     $sortFilter="ORDER BY '$sortOrder' $sortOption";
                 } else {
                     $sortFilter="";
                 }
                $folderId=$this->getParam('folderId');
                if (!isset($folderId)) {
                    $folderId=$this->objDbGroup->getDefaultId($userId);
                }
                $filter="WHERE groupid='$folderId' and creatorid='$userId' ".$sortFilter;
                $listFolderContent=$this->objDbBookmark->getAll($filter);
                $this->setVarByRef('listFolderContent',$listFolderContent);
                $filterFolder="where creatorid='$userId'";
                $listFolders=$this->objDbGroup->getAll($filterFolder);
                $this->setVarByRef('listFolders',$listFolders);
                return "list_tpl.php";

            default:
                $sortOrder=$this->getParam('folderOrder');
                //$userId=$this->objUser->userId();
                 if (isset($sortOrder)) {
                     $sortFilter="ORDER BY '$sortOrder'";
                 } else {
                     $sortFilter="";
                 }
                $folderId=$this->getParam('folderId');
                if (!isset($folderId)) {
                    $folderId=$this->objDbGroup->getDefaultId($userId);
                }
                $filter="WHERE groupid='$folderId' and creatorid='$userId' ".$sortFilter;
                $listFolderContent=$this->objDbBookmark->getAll($filter);
                $this->setVarByRef('listFolderContent',$listFolderContent);
                $filterFolder="where creatorid='$userId'";
                $listFolders=$this->objDbGroup->getAll($filterFolder);
                $this->setVarByRef('listFolders',$listFolders);
                return "list_tpl.php";
        }

    }
    */
    /**
    * Function to return the name of a group or folder
    *
    * @param folderId string
    * return name
    */
    
    function folderByName($folderId)
    {
        $folderName=$this->objDbGroup->getRow('id',$folderId);
        return $folderName['title'];
    }
    
    /**
    * function to return the default user folder
    *
    * return the folder id
    */
    function getDefaultFolder($userId)
    {
        $list=$this->objDbGroup->getDefaultId($userId);
        return $list;
    }
    
    /**
    * Method to parse inputs and save them in the database
    *
    * @var item string
    *
    */
    
    function doParse($item,$options)
    {
        if ($item=='folder') {
            $title=$_POST['title'];
            $description=$_POST['description'];
            $isprivate=$_POST['private'];
            $datecreated=$this->objDbGroup->now();
            $isdefault='0';
            $creatorid=$this->objUser->userId();
            $this->objDbGroup->insertSingle($title,$description,
            $isprivate,$datecreated,$isdefault,$creatorid);
            $titleLine=$this->objLanguage->languageText('mod_bookmark_foldersaved','kbookmark');
            if ($options=='options') {
                return $this->nextAction('options', array('status'=>'success','title'=>$titleLine));
            } else {
                return $this->nextAction('options', array('status'=>'success','title'=>$titleLine));
            }

        } else {
            $groupid=$_POST['parent'];
            $title=$_POST['title'];
            $url=$_POST['url'];
            $description=$_POST['description'];
            $datecreated=$this->objDbBookmark->now();
            $isprivate=$_POST['private'];
            $creatorid=$this->objUser->userId();
            $visitcount='0';
            $isdeleted='0';
            $this->objDbBookmark->insertSingle($groupid,$title, $url,
            $description, $datecreated, $isprivate,
            $creatorid, $visitcount);
            $titleLine=$this->objLanguage->languageText('mod_bookmark_foldersaved','kbookmark');
            return $this->nextAction('view', array('status'=>'success', 'folderId'=>$groupid, 'title'=>$titleLine));
        }
    }
    
    /**
    * Method to parse inputs and save them in the database
    *
    * @var item string
    *
    */
    function parse4Update($item)
    {
        if ($item=='folder') {
            $title=$_POST['title'];
            $id=$_POST['id'];
            $description=$_POST['description'];
            $datemodified=$this->objDbGroup->now();
            $isprivate=$_POST['isprivate'];
            $this->objDbGroup->update('id',$id, array('title'=>$title,'description'=>$description, 'isprivate'=>$isprivate,'datemodified'=>$datemodified));
        } else {
		$url = $this->getParam('url');
		if (preg_match("/^(http(s?):\/\/|ftp:\/\/{1})((\w+\.){1,})\w{2,}$/i", $url)) {
			
		}
		else {
			
		}
            return $this->objDbBookmark->updateBookmark();
        }
    }
    /**
    * Method to set the default folder. The default folder is
    * displayed by default when a user accesses his/her folders
    *
    * @param folderId string
    *
    */
    
    function setDefaultFolder($folderId)
    {
        return $this->objDbGroup->setDefault($folderId);
    }
    
    /**
    * Method to display folders and folder options for editing and updating
    * @param userId string
    *
    * return folderoptions_tpl.php
    **/
    function showFoldersOptions($userId)
    {
        $filterFolder="where creatorid='$userId'";
        $listFolders=$this->objDbGroup->getAll($filterFolder);
        $this->setVarByRef('listFolders',$listFolders);
        return 'folderoptions_tpl.php';
    }
    
    /**
    * Method to delete a single entry from the database.
    * @param item string, indicates if the item to be deleted
    * is a folder or a bookmark. If its a folder, a check is done to ensure
    * that the folder is empty.
    */
    function deleteItem($item)
    {
        $title='';
        $status='success';
        $folderId=$this->getParam('folderId');
        if ($item=='folder'){
            
            $isEmpty=$this->objDbBookmark->isEmpty($folderId);
            $isRoot=$this->isRoot($this->folderByName($folderId));
            //check if is empty
            if (!$isEmpty) {
                $title=$this->objLanguage->languageText('mod_bookmarkgroup_notempty');
                $status='failed';
                //return $this->nextAction('options',array('status'=>'failed', 'title'=>$title));
            }
            if ($isRoot) {
                $title=$this->objLanguage->languageText('mod_bookmarkgroup_isroot');
                $status='failed';
                    //return $this->nextAction('options',array('status'=>'failed','title'=>$title));
            }
            if ($status!='failed'){
                $this->objDbGroup->delete('id',$folderId);
                $title=$this->objLanguage->languageText('word_deleted');
                $status='success';
                    //return $this->nextAction('options',array('status'=>'success','title'=>$title));
            }
            return $this->nextAction('options',array('status'=>$status,'title'=>$title));
        }
        if ($item=='bookmark') {
           $bkId=$this->getParam('id');
           $this->objDbBookmark->delete('id',$bkId);
           $title=$this->objLanguage->languageText('word_deleted');
           return $this->nextAction('',array('folderId'=>$folderId,'title'=>$title));
        }
    }
    
    /**
    * Method to open a bookmark on a new page
    *
    * returns string
    *
    */
    function openPage($pageId)
    {
        $this->objDbBookmark->updateVisitHit($pageId);
        $list=$this->objDbBookmark->getAll("where id='$pageId'");
        $this->setVarByRef('list',$list);
        foreach ($list as $line) {
            $url=$line['url'];
        }
        header("Location: ".$url);
        exit(0);
    }

    /**
    * Method to manage the actions: Move, Delete
    * Move: Moves the checked bookmark to another folder/group
    * Delete: deletes the bookmark
    *
    * @param $operation string operation to be carried out
    * @param @item stringbookmark or folder
    */
    
    function manage($operation, $item)
    {
            $status="";
        if ($item=='bookmark') {
            $bookmarks=$this->getParam('bookmark');
            if (count($bookmarks)>0) {
                if ($operation=='Delete') {
                    foreach ($bookmarks as $list) {
                        $this->objDbBookmark->delete('id',$list);
                    }
                    $title=$this->objLanguage->languageText('mod_bookmark_deleted','kbookmark');
                } else {
                    $folderTo=$this->getParam('parent');
                    foreach ($bookmarks as $list) {
                        $this->objDbBookmark->update('id',$list, array('groupid'=>$folderTo));
                    }
                    $title=$this->objLanguage->languageText('mod_bookmark_moved','kbookmark');
                }
            } else {
                 $title = $this->objLanguage->languageText('mod_bookmark_noselect','kbookmark');
                 $status="failed";
             }
            $folderId=$this->getParam('folderId');
            return $this->nextAction('',array('folderId'=>$folderId, 'status'=>$status,'title'=>$title));
        } else {
            $folders=$this->getParam('folders');
            if (count($folders)>0) {
                foreach ($folders as $list){
                    $isEmpty = $this->objDbBookmark->isEmpty($list);
                    if ($isEmpty) {
                        $this->objDbGroup->delete('id',$list);
                        $status="success";
                        $title=$this->objLanguage->languageText('mod_bookmark_deleted','kbookmark');
                    } else {
                        $title=$this->objLanguage->languageText('mod_bookmarkgroup_notempty','kbookmark');
                        $status="failed";
                    }
                }
                
            } else {
                $title = $this->objLanguage->languageText('mod_bookmark_noselect','kbookmark');
                $status="failed";
            }
        }
        return $this->nextAction('options',array('status'=>$status,'title'=>$title));
    }
    
    /**
    * Method to present form to edit the detaills of a bookmark
    * @param item string
    * @param id string
    *
    * return edit_tpl.php
    */
    function editItem($item,$id)
    {
        $userId=$this->objUser->userId();
        $filter="where id='$id'";
        if ($item=='folder'){
            $list=$this->objDbGroup->getAll($filter);
            $this->setVarByRef('listFolders',$list);
            return 'edit_tpl.php';
        } else {
            $filterFolder="where creatorid='$userId'";
            $listFolders=$this->objDbGroup->getAll($filterFolder);
            $this->setVarByRef('listFolders',$listFolders);
            $list=$this->objDbBookmark->getAll($filter);
            $this->setVarByRef('listContent',$list);
            return 'edit_tpl.php';
        }
    }
    
    /**
    * Function to check of a given folder is the root folder
    *
    * @param $folder
    * return boolean
    */
    
    function isRoot($folder)
    {
        if ($folder==$this->objLanguage->LanguageText('mod_bookmark_defaultfolder','kbookmark')) {
          return True;
        } else {
            return False;
        }
    }

}; //class
?>
