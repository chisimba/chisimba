<?php

class dbcmsadmin extends dbTable
{
	/**
     * The dbcontent object
     *
     * @access private
     * @var object
     */
	protected $_objDBContent;

	/**
     * The language object
     *
     * @access private
     * @var object
     */
	public $objLanguage;

	/**
	 * The sections  object
	 *
	 * @access private
	 * @var object
	 */
	protected $TreeNodes;

	/**
     * The user object
     *
     * @access public
     * @var object
     */
	public $objUser;

	/**
     * The dbfrontpage object
     *
     * @access private
     * @var object
     */
	protected $_objFrontPage;

	/**
     * The blocks object
     *
     * @access private
     * @var object
     */
	protected $_objBlocks;

	/**
	 * initialise the objects that we need.
	 *
	 */
	public function init()
	{
		try {
			$this->objUser = $this->getObject('user', 'security');
			$this->objLanguage = $this->getObject('language', 'language');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			// bail, we are screwed anyway
			exit;
		}
	}

	/************************ tbl_module_block methods *************************/

	/**
     * Method to return all entries in blocks table
     *
     * @return array $entries An array of all entries in the module_blocks table
     * @access public
     */
	public function getBlockEntries()
	{
		$this->_changeTable('tbl_module_blocks');
		return $this->getAll();
	}

	/**
     * Method to return an entries in blocks table
     *
     * @param string $blockId The id of the block
     * @return array $entry An associative array of the blocks details
     * @access public
     */
	public function getModuleBlock($blockId)
	{
		$this->_changeTable('tbl_module_blocks');
		$entry = $this->getAll("WHERE id = '$blockId'");
		return $entry['0'];
	}

	/**
     * Method to return an entries in blocks table
     *
     * @param string $blockName The name of the block
     * @return array $entry An associative array of the blocks details
     * @access public
     */
	public function getBlockByName($blockName)
	{
		$this->_changeTable('tbl_module_blocks');
		$entry = $this->getAll("WHERE blockname = '$blockName'");
		if (count($entry) == 0) {
			return FALSE;
		} else {
			return $entry[0];
		}
	}
 
	/************************************* CMS BLOCKS ********************************/

	/**
     * Method to save a record to the database
     *
     * @param string $pageId The id of the page
     * @param string $sectionId The id of the Section
     * @param string $blockId The id of the block
     * @param string $blockCat Category of Block. Either 'frontpage', 'content' or 'section'
     * @access public
     * @return bool
     */
	public function addBlock($pageId=NULL, $sectionId=NULL, $blockId = '', $blockCat = 'frontpage', $left = 0)
	{
		$this->_changeTable('tbl_cms_blocks');

		if ($blockCat == 'frontpage') {
			// Get ordering
			$ordering = $this->_getBlockOrdering(NULL, NULL, 'frontpage');
			$newArr = array(
			'pageid' => $pageId ,
			'blockid' => $blockId,
			'sectionid' => $sectionId,
			'frontpage_block' => 1,
			'leftside_blocks' => $left,
			'ordering' => $ordering
			);
		} else if ($blockCat == 'content') {
			// Get ordering
			$ordering = $this->_getBlockOrdering($pageId, NULL, 'content');
			$newArr = array(
			'pageid' => $pageId ,
			'blockid' => $blockId,
			'sectionid' => NULL,
			'frontpage_block' => 0,
			'leftside_blocks' => $left,
			'ordering' => $ordering
			);
		} else {
			// Get ordering
			$ordering = $this->_getBlockOrdering(NULL, $sectionId, 'section');
			$newArr = array(
			'pageid' => NULL,
			'blockid' => $blockId,
			'sectionid' => $sectionId,
			'frontpage_block' => 0,
			'leftside_blocks' => $left,
			'ordering' => $ordering
			);
		}

		$newId = $this->insert($newArr);
		return $newId;
	}

	
	/**
     * Method to edit a record
     *
     * @access public
     * @return bool
     */
	public function editBlock($id=NULL, $pageId=NULL, $ordering=1, $blockId = NULL)
	{
		$this->_changeTable('tbl_cms_blocks');
		// Get entry details
		$newArr = array(
		'pageid' => $pageId ,
		'blockid' => $blockId,
		'ordering' => $ordering
		);
		// Update entry
		return $this->update('id', $id, $newArr);
	}

	/**
     * Method to delete a block
     *
     * @param string $pageId The id of the page
     * @param string $blockId The id of the block
     * @return boolean
     * @access public
     */
	public function deleteBlock($pageId, $sectionId, $blockId, $blockCat)
	{
		$this->_changeTable('tbl_cms_blocks');
		if ($blockCat == 'frontpage') {
			// Get last in order
			$frontPage = 1;
			$block = $this->getAll("WHERE frontpage_block = '$frontPage' AND blockid = '$blockId'");
		} else if ($blockCat == 'content') {
			// Get last in order
			$block = $this->getAll("WHERE pageid = '$pageId' AND blockid = '$blockId'");
		} else {
			// Get last in order
			$block = $this->getAll("WHERE sectionid = '$sectionId' AND blockid = '$blockId'");
		}
		if(!empty($block)) {
			$id = $block[0]['id'];
			$blockOrderNo = $block[0]['ordering'];
			if ($blockCat == 'frontpage') {
				// Get front page blocks
				$pageBlocks = $this->getBlocksForFrontPage($pageId);
			} else if ($blockCat == 'content') {
				// Get page blocks
				$pageBlocks = $this->getBlocksForPage($pageId);
			} else {
				// Get section blocks
				$pageBlocks = $this->getBlocksForSection($sectionId);
			}
			$pageBlocks = $this->getBlocksForPage($pageId);
			if(count($pageBlocks) > 1) {
				foreach($pageBlocks as $pb) {
					if($pb['ordering'] > $blockOrderNo) {
						$newOrder = $pb['ordering'] - 1;
						$this->update('id', $pb['id'], array('ordering' => $newOrder));
					}
				}
			}
			return $this->delete('id', $id);
		}
	}

	/**
     * Method to delete a block using the row id
     *
     * @access public
     * @param string $id The row id of the block
     * @return void
     */
	public function deleteBlockById($id)
	{
		$this->_changeTable('tbl_cms_blocks');
		$this->delete('id', $id);
	}

	/**
     * Method to get all blocks attached to a page
     *
     * @param string $pageId The id of the page content
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks on the page
     */
	public function getBlocksForPage($pageId, $sectionId = NULL, $left = '0')
	{
		$this->_changeTable('tbl_cms_blocks');
		$sql = "SELECT cb.*, cb.id as cb_id, mb.moduleid, mb.blockname
                FROM tbl_cms_blocks AS cb, tbl_module_blocks AS mb
                WHERE (cb.blockid = mb.id) AND frontpage_block = 0 
                AND leftside_blocks = '$left' AND (pageid = '$pageId'";   
		if(!empty($sectionId)){
			$sql .= " OR sectionid = '$sectionId'";
		}
		$sql .= ")"." ORDER BY cb.ordering";
		return $this->getArray($sql);
	}

	/**
     * Method to get all blocks attached to a section
     *
     * @param string $sectionId The id of the section
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks in the section
     */
	public function getBlocksForSection($sectionId, $left = '0')
	{
		$this->_changeTable('tbl_cms_blocks');
		$left = (isset($left) && !empty($left)) ? $left : 0;
		$sql = "SELECT tbl_cms_blocks.*, moduleid, blockname FROM tbl_cms_blocks, tbl_module_blocks
                WHERE (blockid = tbl_module_blocks.id) AND sectionid = '$sectionId' 
                AND frontpage_block = 0  AND leftside_blocks = '$left' 
                "/*GROUP BY blockid*/." ORDER BY ordering";
		return $this->getArray($sql);
	}

	/**
     * Method to get all blocks attached to the front page
     *
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks on the front page
     */
	public function getBlocksForFrontPage($left = '0')
	{
		$this->_changeTable('tbl_cms_blocks');
		$sql = "SELECT tbl_cms_blocks.id, tbl_cms_blocks.pageid, tbl_cms_blocks.blockid, tbl_cms_blocks.sectionid,
                        tbl_cms_blocks.frontpage_block, tbl_cms_blocks.leftside_blocks, tbl_cms_blocks.ordering,
                        tbl_module_blocks.moduleid, tbl_module_blocks.blockname 
                    FROM tbl_cms_blocks, tbl_module_blocks 
                    WHERE (tbl_cms_blocks.blockid = tbl_module_blocks.id) 
                        AND tbl_cms_blocks.frontpage_block = '1' 
                        AND tbl_cms_blocks.leftside_blocks = '$left' 
                    ORDER BY tbl_cms_blocks.ordering";
		return $this->getArray($sql);
	}

	/**
     * Method to update the order of the blocks
     * 
     * @param string $id The id of the entry to move
     * @param int $ordering How to update the order(up or down).
     * @access public
     * @return bool
     * @author Warren Windvogel
     */
	public function changeBlockOrder($id, $ordering, $pageId, $sectionId)
	{
		$this->_changeTable('tbl_cms_blocks');
		// Get array of all blocks in level
		if ($blockCat == 'frontpage') {
			// Get last in order
			$frontPage = 1;
			$fpContent = $this->getAll("WHERE frontpage_block = '$frontPage' ORDER BY ordering");
		} else if ($blockCat == 'content') {
			// Get last in order
			$fpContent = $this->getAll("WHERE pageid = '$pageId' ORDER BY ordering");
		} else {
			// Get last in order
			$fpContent = $this->getAll("WHERE sectionid = '$sectionId' ORDER BY ordering");
		}
		// Search for entry to be reordered and update order
		foreach($fpContent as $content) {
			if ($content['id'] == $id) {
				if ($ordering == 'up') {
					$changeTo = $content['ordering'];
					$toChange = $content['ordering'] + 1;
					$updateArray = array(
					'ordering' => $toChange
					);
					$this->update('id', $id, $updateArray);
				} else {
					$changeTo = $content['ordering'];
					$toChange = $content['ordering'] - 1;
					$updateArray = array(
					'ordering' => $toChange
					);
					$this->update('id', $id, $updateArray);
				}
			}
		}

		// Get other entry to change
		$entries = $this->getAll("WHERE pageid = '$pageId' AND ordering = '$toChange'");
		foreach($entries as $entry) {
			if ($entry['id'] != $id) {
				$upArr = array(
				'ordering' => $changeTo
				);
				$this->update('id', $entry['id'], $upArr);
			}
		}
	}

	/********************** CMS Categories - 'tbl_cms_categories' ***********************************/

	/**
     * Methode to get the list of categories
     * @access public
     * @return array
     */
	public function getCategories()
	{
		$this->_changeTable('tbl_cms_categories');
		return $this->getAll();
	}

	/**
     * Method to get the number of categories for a certain section
     * @param string $sectionId The id of the category
     * @access public
     * @return int
     */
	public  function getCatCount($sectionId = NULL)
	{
		$this->_changeTable('tbl_cms_categories');
		if($sectionId == NULL) {
			return $this->getRecordCount();
		} else {
			return $this->getRecordCount("WHERE sectionid = '$sectionId'");
		}
	}

	/**
     * Method to add a section to the database
     * @access public
     * @return bool
     */
	public function addCategory($parentSelected = NULL, $title = NULL, $image = NULL, $imagePostion=NULL,  $access = NULL, $description = NULL, $published = NULL, $menuText = NULL)
	{
		$this->_changeTable('tbl_cms_categories');
		// get type(section or category) and its id
		$matches = split(':', $parentSelected);
		$type = trim($matches[0]);
		$id = trim($matches[1]);
		if($type == 'category') {
			$sectionId = $this->getSectionIdOfCat($id);
		}
		try {
			if($type == 'category') {
				$section = $sectionId;
				$parent = $id;
			} else {
				$section = $id;
				$parent = 'section';
			}
			if($type == 'category') {
				$count = $this->getCatLevel($parent) + 1;
			} else {
				$count = '1';
			}
			return $this->insert(array(
			'title' => $title,
			'menutext' => $menuText,
			'sectionid' => $section,
			'parent_id' => $parent,
			'image' => $image,
			'image_position' => $imagePostion,
			'access' => $access,
			'ordering' => 0,
			'count' => $count,
			'description' => $desciption,
			'published' => $published
			));

		} catch (customException $e) {
			customException::cleanUp();
			exit();
		}
	}

	/**
     * Method to add a categoryu to the database
     * @access public
     * @return bool
     */
	public function editCategory($id=NULL, $section = NULL, $title = NULL, $menuText = NULL, $image = NULL, $imagePostion = NULL, $access = NULL, $desciption = NULL, $published = NULL, $ordering = NULL)
	{
		$this->_changeTable('tbl_cms_categories');
		try {
			$arrFields = array(
			'title' => $title,
			'menutext' => $menuText,
			'sectionid' => $section,
			'image' => $image,
			'image_position' => $imagePostion,
			'access' => $access,
			'ordering' => $ordering,
			'description' => $desciption,
			'published' => $published);
			return $this->update('id', $id, $arrFields);

		} catch (customException $e) {
			customException::cleanUp();
			exit();
		}
	}

	/**
     * Method to get the menutext for a section
     * @return string
     * @access public
     * @param string id 
     */
	public function getCategoryMenuText($id)
	{
		$this->_changeTable('tbl_cms_categories');
		$line = $this->getCategory($id);
		return $line['menutext'];
	}

	/**
     * Method to get a Section
     * @var string id The section id
     * @return array
     * @access public
     */
	public function getCategory($id)
	{
		$this->_changeTable('tbl_cms_categories');
		return $this->getRow('id', $id);
	}

	/**
     * Method to get all categories in a specific section
     *
     * @param string $sectionId The id(pk) of the section
     * @return array $categoriesInSection An array of associative arrays for all categories in the section
     * @access public
     */
	public function getCategoryInSection($sectionId, $level = NULL)
	{
		$this->_changeTable('tbl_cms_categories');
		if(isset($level)) {
			return $this->getAll("WHERE sectionid = '$sectionId' AND count = '$level'");
		} else {
			return $this->getAll("WHERE sectionid = '$sectionId'");
		}
	}
	/**
     * Method to return the count value of a category
     *
     * @param string $id The id(pk) of the category
     * @return array $categoriesInSection An array of associative arrays for all categories in the section
     * @access public
     */
	public function getCatLevel($id)
	{
		$this->_changeTable('tbl_cms_categories');
		// get entry
		$cat = $this->getRow('id', $id);
		// get and return value of count field
		$count = $cat['count'];
		return $count;
	}

	/**
     * Method to return the sectionId of a category
     *
     * @param string $id The id(pk) of the category
     * @return string $sectionId The id(pk) of the categories section
     * @access public
     */
	public function getSectionIdOfCat($id)
	{
		$this->_changeTable('tbl_cms_categories');
		// get entry
		$cat = $this->getRow('id', $id);
		// get and return sectionId
		$sectionId = $cat['sectionid'];
		return $sectionId;
	}

	/**
     * Method to delete a category
     *
     * @return NULL
     * @access public
     */
	public function deleteCat($id)
	{
		$this->_changeTable('tbl_cms_categories');
		// if cat has nodes delete nodes as well
		if($this->hasNodes($id)) {
			// get cat details
			$category = $this->getCategory($id);
			// get number of levels in section
			$this->objCmsUtils = $this->newObject('cmsutils', 'cmsadmin');
			$numLevels = $this->objCmsUtils->getNumNodeLevels($category['sectionid']);
			$parentId = $id;
			$nodeIdArray = array();
			$level = $category['count'] + 1;
			// get an array of all the cats nodes
			for($i = $level; $i <= $numLevels; $i++) {
				$nodes = $this->getAll("WHERE parent_id = '$parentId' AND count = '$i'");
				foreach($nodes as $node) {
					$nodeIdArray[] = $node['id'];
				}
			}
			// delete each node in array
			foreach($nodeIdArray as $data) {
				$this->delete('id', $data);
			}
			// delete original category
			$this->delete('id', $id);
		} else {
			$this->delete('id', $id);
		}
	}

	/**
     * Method to check if a category has child/leaf node(s)
     *
     * @param string $id The id(pk) of the category
     * @return bool True if has nodes else False
     * @access public
     */
	public function hasNodesCategory($id)
	{
		$this->_changeTable('tbl_cms_categories');
		$nodes = $this->getAll("WHERE parent_id = '$id'");
		if(count($nodes) > 0) {
			$hasNodes = True;
		} else {
			$hasNodes = False;
		}
		return $hasNodes;
	}


	/************************************** 'tbl_cms_content' **************************************/

	/**
     * Method to save a record to the database
     *
     * @access public
     * @return bool
     */
	public function addContent($pgarr)
	{
		$this->_changeTable('tbl_cms_content');
		// Get details of the new entry
		if($pgarr['published'] == '1'){
			$start_publish = $this->now();
			$end_publish = $this->now();
		}
		else {
			$start_publish = $pgArr['start_publish'];
			if($start_publish === NULL)
			{
				$start_publish = $this->now();
			}
			$end_publish = $pgArr['end_publish'];
			if($end_publish === NULL)
			{
				$end_publish = $this->now();
			}
		}
		if (!isset($pgarr['override_date']) || $pgarr['override_date'] == NULL) {
			$override_date =  $this->now();
		}
		else {
			$override_date = $pgarr['override_date'];
			if($override_date === NULL)
			{
				$override_date = $this->now();
			}
		}
		
		$newArr = array(
		'title' => $pgarr['title'] ,
		'sectionid' => $pgarr['sectionid'],
		'introtext' => addslashes($pgarr['introtext']),
		'body' => addslashes($pgarr['body']),
		'access' => $pgarr['access'],
		'ordering' => $this->getContentOrdering($pgarr['sectionid']),
		'published' => $pgarr['published'],
		'hide_title' => $pgarr['hide_title'],
		'created' => $this->now(),
		'modified' => $this->now(),
		'post_lic' => $pgarr['post_lic'],
		'created' =>$override_date,
		'created_by' => $pgarr['created_by'],
		'created_by_alias'=>$pgarr['creatorid'],
		'checked_out'=> $pgarr['creatorid'],
		'checked_out_time'=> $this->now(),
		'metakey'=>$pgarr['metakey'],
		'metadesc'=>$pgarr['metadesc'],
		'start_publish'=>$start_publish,
		'end_publish'=>$end_publish,
		);

		$newId = $this->insert($newArr);
		$newArr['id'] = $newId;
		// $this->lucenePageIndex($newArr);
		// process the forntpage
		//var_dump($pgarr['isfrontpage']);
		if ($pgarr['isfrontpage'] == '1') {
			$this->addFrontPageContent($newId);
		}
		return $newId;
	}


	/**
         * Method to edit a record
         *
         * @access public
         * @return bool
         */
	public function editContent($id = NULL, $title = NULL, $sectionid = NULL, $published = 0, $access = NULL, $introText = NULL, $fullText = NULL, $override_date = NULL, $start_publish = NULL, $access = NULL, $introText = NULL, $fullText = NULL, $metakey = NULL, $metadesc = NULL, $ccLicence = NULL, $hide_title = 0, $creatorid = NULL, $isFrontPage = 0)
	{
		$this->_changeTable('tbl_cms_content');
		if($published == 1 && empty($start_publish)){
			$start_publish = $this->now();
		}
		$end_publish = $this->getParam('end_date',null);
		if ($override_date!=null) {
			$override_date =  $this->now();
		}
		$modifiedBy = $this->_objUser->userId();
		$modifiedDate = $this->now();
		$newArr = array(
		'title' => $title ,
		'sectionid' => $sectionid,
		'access' => $access,
		'introtext' => addslashes($introText),
		'body' => addslashes($fullText),
		'modified' => $modifiedDate,
		'modified_by' => $modifiedBy,
		'published' => $published,
		'hide_title' => $hide_title,
		'post_lic' => $ccLicence,
		'checked_out'=> $modifiedBy,
		'checked_out_time'=> $this->now(),
		'metakey'=>$metakey,
		'metadesc'=>$metadesc,
		'start_publish'=>$start_publish,
		'end_publish'=>$end_publish
		);
		if(!empty($creatorid)){
			$newArr['created_by'] = $creatorid;
		}
		if ($isFrontPage == 1) {
			$this->addFrontPageContent($id);
		} else {
			$this->removeIfExists($id);
		}
		$result = $this->update('id', $id, $newArr);
		if ($result != FALSE) {
			$newArr['id'] = $id;
			$this->lucenePageIndex($newArr);
		}
		return $result;
	}

	/**
         * Method move a record to trash
         *
         * @param string $id The id of the record that needs to be deleted
         * @access public
         * @return bool
         */
	public function trashContent($id)
	{
		$this->_changeTable('tbl_cms_content');
		//First remove from front page
		$this->removeIfExists($id);
		$fields = array('trash' => 1, 'ordering' => '', 'end_publish' => $this->now());
		$result =  $this->update('id', $id, $fields);
		// Get the section id of the page - re order pages
		$pageData = $this->getContentPage($id);
		$sectionId = $pageData['sectionid'];
		$this->reorderContent($sectionId);
		$objLucene = $this->getObject('indexdata', 'search');
		$objLucene->removeIndex('cms_page_'.$id);
		return $result;
	}

	/**
        * Method to reorder the content in a section 
        * After a page is trashed, etc
        *
        * @author Megan Watson
        * @param string $sectionId The id of the section containing the content
        * @access private
        * @return bool
        */
	private function reorderContent($sectionId)
	{
		$this->_changeTable('tbl_cms_content');
		// Get all pages in the section
		$sectionData = $this->getPagesInSection($sectionId, FALSE);
		if(!empty($sectionData)){
			// Reorder the pages
			$i = 1;
			foreach($sectionData as $key => $item){
				if($item['trash'] == 0){
					$this->update('id', $item['id'], array('ordering' => $i));
					$sectionData[$key]['ordering'] = $i++;
				}
			}
			// Get the ordering position of the last section
			$newData = array_reverse($sectionData);
			$lastOrder = $newData[0]['ordering']+1;
			// Remove all null and negative numbers
			foreach($sectionData as $key => $item){
				if(($item['ordering'] < 0 || is_null($item['ordering'])) && $item['trash'] == 0){
					$this->update('id', $item['id'], array('ordering' => $lastOrder));
					$sectionData[$key]['ordering'] = $lastOrder++;
				}
			}
		}
	}

	/**
         * Method to undelete content
         *
         * @param string $id The id of the record that needs to be deleted
         * @access public
         * @return bool
         */
	public function undelete($id)
	{
		$this->_changeTable('tbl_cms_content');
		$page = $this->getRow('id', $id);
		if ($page == FALSE)
		{
			return FALSE;
		} else {
			$order = $this->getContentOrdering($page['sectionid']);
			$fields = array('trash' => 0, 'ordering' => $order);

			$this->lucenePageIndex($page);
			return $this->update('id', $id, $fields);
		}
	}

	/**
        * Method to delete a content page
        *
        * @param string $id The id of the entry
        * @return boolean
        * @access public
        */
	public function deleteContent($id)
	{
		$this->_changeTable('tbl_cms_content');
		// Re-order other pages in section accordingly
		$page = $this->getRow('id', $id);
		$pageOrderNo = $page['ordering'];
		$sectionId = $page['sectionid'];
		// First remove from front page
		$this->removeIfExists($id);
		// Remove blocks for the page
		$pageBlocks = $this->getBlocksForPage($id);
		if(!empty($pageBlocks)) {
			foreach($pageBlocks as $pb) {
				$this->deleteBlockById($pb['cb_id']);
			}
		}
		//Delete page
		$result = $this->delete('id', $id);

		// Remove from search
		$objLucene = $this->getObject('indexdata', 'search');
		$objLucene->removeIndex('cms_page_'.$id);
		// Reorder the content
		$this->reorderContent($sectionId);
		return $result;
	}

	/**
         * Method to get the content
         *
         * @param string $filter The Filter
         * @return  array An array of associative arrays of all content pages in relationto filter specified
         * @access public
         */
	public function getContentPages($filter = '')
	{
		$this->_changeTable('tbl_cms_content');
		if ($filter == 'trash') {
			$filter = " WHERE trash= '1' ";
		} else {
			$filter = " WHERE trash= '0' ";
		}
		return $this->getAll($filter." ORDER BY ordering");
	}

	/**
         * Method to get the archived content
         *
         * @author Megan Watson
         * @param string $filter The Filter
         * @return  array An array of associative arrays of all content pages in relationto filter specified
         * @access public
         */
	public function getArchivePages($filter = '', $table = 'tbl_cms_content')
	{
		$this->_changeTable('tbl_cms_content');
		$sql = "SELECT * FROM '$table' WHERE trash = '1' ";

		if(!empty($filter)){
			$sql .= "AND LOWER('title') LIKE '%".strtolower($filter)."%' ";
		}
		$sql .= 'ORDER BY ordering';
		return $this->getArray($sql);
	}

	/**
         * Method to get a page content record
         *
         * @param string $id The id of the page content
         * @access public
         * @return array $content An associative array of content page details
         */
	public function getContentPage($id)
	{
		$this->_changeTable('tbl_cms_content');
		$content = $this->getRow('id', $id );
		return $content;
	}

	/**
         * Method to toggle the publish field
         *
         * @param string id The id if the content
         * @access public
         * @return boolean
         * @author Wesley Nitsckie
         */
	public function toggleContentPublish($id)
	{
		$this->_changeTable('tbl_cms_content');
		$row = $this->getContentPage($id);

		if ($row['published'] == 1) {
			return $this->update('id', $id , array('published' => 0, 'end_publish' => $this->now(), 'start_publish' => '') );
		} else {
			return $this->update('id', $id , array('published' => 1, 'start_publish' => $this->now()) );
		}
	}

	/**
         * Method to publish or unpublish content 
         * 
         * @param string id The id if the content
         * @param string $task Publish or unpublish
         * @access public
         * @return boolean
         * @author Megan Watson
         */
	public function publishContent($id, $task = 'publish')
	{
		$this->_changeTable('tbl_cms_content');
		switch($task){
			case 'publish':
				$fields['published'] = 1;
				$fields['start_publish'] = $this->now();
				$fields['end_publish'] = '';
				break;
			case 'unpublish':
				$fields['published'] = 0;
				$fields['end_publish'] = $this->now();
				break;
		}

		return $this->update('id', $id, $fields);
	}


	/**
        * Method to update all the content with the
        * sections that will be deleted
        *
        * @param string $sectionId The section Id
        * @return boolean
        * @access public
        */
	public function resetSection($sectionId)
	{
		$this->_changeTable('tbl_cms_content');
		$arrContent = $this->getAll("WHERE sectionid = '$sectionId'");
		$result = '';

		if(!empty($arrContent)){
			foreach ($arrContent as $page) {
				// First remove from front page
				$this->removeIfExists($page['id']);

				// Trash / archive
				$fields = array('trash' => 1, 'ordering' => '');
				$result =  $this->update('id', $page['id'], $fields);
			}
		}
		return $result;
	}

	/**
        * Method to update all the content with the
        * sections that will be deleted
        *
        * @param string $sectionId The section Id
        * @return boolean
        * @access public
        */
	public function unarchiveContent($sectionId)
	{
		$this->_changeTable('tbl_cms_content');
		$arrContent = $this->getAll("WHERE sectionid = '$sectionId'");
		$result = '';

		if(!empty($arrContent)){
			$order = 1;
			foreach ($arrContent as $page) {
				// Restore
				$fields = array('trash' => 0, 'ordering' => $order++);
				$result =  $this->update('id', $page['id'], $fields);
			}
		}
		return $result;
	}

	/**
         * Method to get all pages in a specific section
         *
         * @param string $sectionId The id of the section
         * @return array $pages An array of all pages in the section
         * @access public
         * @author Warren Windvogel
         */
	public function getPagesInSection($sectionId, $isPublished=FALSE)
	{
		$this->_changeTable('tbl_cms_content');
		$filter = "WHERE sectionid = '$sectionId' AND trash='0' ";
		if($isPublished){
			$filter .= "AND published='1' ";
		}
		$pages = $this->getAll($filter.' ORDER BY ordering');
		return $pages;
	}

	/**
         * Method to get all pages in a specific section, including those on the front page
         *
         * @access public
         * @author Megan Watson
         * @param string $sectionId The id of the section
         * @return array $data An array of all pages in the section
         */
	public function getPagesInSectionJoinFront($sectionId)
	{
		$this->_changeTable('tbl_cms_content');
		$sql = "SELECT *, fr.id AS front_id, co.id AS page_id, co.ordering AS co_order
                FROM tbl_cms_content AS co 
                LEFT JOIN tbl_cms_content_frontpage AS fr ON (fr.content_id = co.id)
                WHERE sectionid = '$sectionId' AND trash='0'
                ORDER BY co.ordering";

		$data = $this->getArray($sql);
		return $data;
	}

	/**
         * Method to get the title and id of all pages in a specific section
         *
         * @param string $title The title of the section. Returns pages from all sections if NULL. Defaults to NULL
         * @param int $limit The amount of records to return. Returns all pages if NULL. Defaults to NULL
         * @return array $titles An array of associative arrays containing the id and title of all pages in the section
         * @access public
         * @author Warren Windvogel
         */
	public function getTitles($title = NULL, $limit = NULL)
	{
		$this->_changeTable('tbl_cms_content');
		//If only the section id is set, return all records in the section
		if($title == NULL && $limit != NULL){
			$sql = "SELECT id, title FROM tbl_cms_content WHERE trash = '0' ORDER BY created DESC LIMIT '$limit'";
			//If only the limit is set, return set amount of pages from all sections
		} else if($title != NULL && $limit == NULL){
			$sql = "SELECT id, title FROM tbl_cms_content WHERE title = '$title' ORDER BY created DESC";
			//If both params are set, return set amount of pages from specified section
		} else if($title != NULL && $limit != NULL){
			$sql = "SELECT id, title FROM tbl_cms_content WHERE title = '$title' ORDER BY created DESC LIMIT '$limit'";
			//Else if neither param is set, return all records
		} else {
			$sql = "SELECT id, title FROM tbl_cms_content WHERE trash = '0' ORDER BY created DESC";
		}
		$titles = $this->getArray($sql);
		return $titles;
	}

	/**
         * Method to get the title and id of the last 5 pages added
         *
         * @return array $lastFiveTitles An array of associative arrays containing the id and title of 
         * the last $n pages added
         * @param int $n The number of pages whose titles we should get
         * @access public
         * @author Warren Windvogel / added by Derek Keats 2007 01 17
         */
	public function getLatestTitles($n=5)
	{
		$this->_changeTable('tbl_cms_content');
		$sql = "SELECT id, title FROM tbl_cms_content WHERE trash = '0' ORDER BY created DESC LIMIT $n";
		return $this->getArray($sql);
	}

	/**
         * Method to get the number of pages in a specific section
         *
         * @param string $sectionId The id of the section
         * @return int $noPages The number of pages in the section
         * @access public
         * @author Warren Windvogel
         */
	public function getNumberOfPagesInSection($sectionId)
	{
		$this->_changeTable('tbl_cms_content');
		$noPages = 0;
		$pages = $this->getAll("WHERE sectionid = '$sectionId' AND trash='0' ORDER BY ordering");
		$noPages = count($pages);
		return $noPages;
	}

	/**
         * Method to return the ordering value of new content (gets added last)
         *
         * @param string $sectionId The id(pk) of the section the content is attached to
         * @return int $ordering The value to insert into the ordering field
         * @access public
         * @author Warren Windvogel
          */
	public function getPageOrder($pageId)
	{
		$this->_changeTable('tbl_cms_content');
		//get last order value
		$lastOrder = $this->getRow('id', $pageId);
		//add after this value
		$ordering = $lastOrder['ordering'];
		return $ordering;
	}

	/**
         * Method to return the ordering value of new content (gets added last)
         *
         * @param string $sectionId The id(pk) of the section the content is attached to
         * @return int $ordering The value to insert into the ordering field
         * @access public
         * @author Warren Windvogel
          */
	public function getContentOrdering($sectionId)
	{
		$this->_changeTable('tbl_cms_content');
		$ordering = 1;
		//get last order value
		$lastOrder = $this->getAll("WHERE sectionid = '$sectionId' AND trash = '0' ORDER BY ordering DESC LIMIT 1");
		//add after this value
		if (!empty($lastOrder)) {
			$ordering = $lastOrder['0']['ordering'] + 1;
		}

		return $ordering;
	}
	
	/**
         * Method to return the ordering value of new content (gets added last)
         *
         * @return int $ordering The value to insert into the ordering field
         * @access public
         * @author Warren Windvogel
          */
        public function doFPOrdering()
        {
            $ordering = 1;
            //get last order value
            $lastOrder = $this->getAll('ORDER BY ordering DESC LIMIT 1');
            if(!empty($lastOrder)) {
                //add after this value
                $ordering = $lastOrder['0']['ordering'] + 1;
            }
            return $ordering;
        }

	/**
         * Method to return the links to be displayed in the order column on the table
         *
         * @param string $id The id of the entry
         * @return string $links The html for the links
         * @access public
         * @author Warren Windvogel
         */
	public function getContentOrderingLink($sectionid, $id)
	{
		$this->_changeTable('tbl_cms_content');
		//Get the number of pages in the section
		$lastOrd = $this->getAll("WHERE sectionid = '$sectionid' AND trash = '0' ORDER BY ordering DESC LIMIT 1");
		$topOrder = $lastOrd[0]['ordering'];
		$links = " ";

		if ($topOrder > 1) {
			//Get the order position
			$entry = $this->getRow('id', $id);
			//Create geticon obj
			$this->objIcon = & $this->newObject('geticon', 'htmlelements');

			if ($entry['ordering'] == 1) {
				//return down arrow link
				//icon
				$this->objIcon->setIcon('downend');
				$this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
				//link
				$downLink = & $this->newObject('link', 'htmlelements');
				$downLink->href = $this->uri(array('action' => 'changecontentorder', 'id' => $id, 'ordering' => 'up', 'sectionid' => $sectionid));
				$downLink->link = $this->objIcon->show();
				$links .= $downLink->show();
			} else if ($entry['ordering'] == $topOrder) {
				//return up arrow
				//icon
				$this->objIcon->setIcon('upend');
				$this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
				//link
				$upLink = & $this->newObject('link', 'htmlelements');
				$upLink->href = $this->uri(array('action' => 'changecontentorder', 'id' => $id, 'ordering' => 'down', 'sectionid' => $sectionid));
				$upLink->link = $this->objIcon->show();
				$links .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $upLink->show();
			} else {
				//return both arrows
				//icon
				$this->objIcon->setIcon('down');
				$this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
				//link
				$downLink = & $this->newObject('link', 'htmlelements');
				$downLink->href = $this->uri(array('action' => 'changecontentorder', 'id' => $id, 'ordering' => 'up', 'sectionid' => $sectionid));
				$downLink->link = $this->objIcon->show();
				//icon
				$this->objIcon->setIcon('up');
				$this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
				//link
				$upLink = & $this->newObject('link', 'htmlelements');
				$upLink->href = $this->uri(array('action' => 'changecontentorder', 'id' => $id, 'ordering' => 'down', 'sectionid' => $sectionid));
				$upLink->link = $this->objIcon->show();
				$links .= $downLink->show() . '&nbsp;' . $upLink->show();
			}
		}

		return $links;
	}

	/**
         * Method to update the order of the frontpage
         *
         * @param string $id The id of the entry
         * @param string $id The id of the entry to move
         * @param int $ordering How to update the order(up or down).
         * @access public
         * @return bool
         * @author Warren Windvogel
         */
	public function changeContentOrder($sectionid, $id, $ordering)
	{
		$this->_changeTable('tbl_cms_content');
		//Get array of all page entries
		$fpContent = $this->getAll("WHERE sectionid = '$sectionid' AND trash = '0' ORDER BY ordering");
		//Search for entry to be reordered and update order
		foreach($fpContent as $content) {
			if ($content['id'] == $id) {
				if ($ordering == 'up') {
					$changeTo = $content['ordering'];
					$toChange = $content['ordering'] + 1;
					$updateArray = array(
					'modified' => $this->now(),
					'ordering' => $toChange
					);
					$this->update('id', $id, $updateArray);
				} else {
					$changeTo = $content['ordering'];
					$toChange = $content['ordering'] - 1;
					$updateArray = array(
					'ordering' => $toChange,
					'modified' => $this->now()
					);
					$this->update('id', $id, $updateArray);
				}
			}
		}

		//Get other entry to change
		$entries = $this->getAll("WHERE sectionid = '$sectionid' AND ordering = '$toChange' AND trash = '0'");
		foreach($entries as $entry) {
			if ($entry['id'] != $id) {
				$upArr = array(
				'ordering' => $changeTo,
				'modified' => $this->now()
				);
				$result = $this->update('id', $entry['id'], $upArr);
			}
		}

		// Reorder the content
		$this->reorderContent($sectionid);
		return $result;
	}

	/**
	 * Method to scrub grubby html
	 *
	 * @param string $document
	 * @return string
	 */
	public function html2txt($document, $scrub = TRUE)
	{
		if($scrub == TRUE)
		{
			$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
			'@<![\s\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments including CDATA
			);

		}
		else {
			$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			'@<![\s\S]*?--[ \t\n\r]*>@',        // Strip multi-line comments including CDATA
			'!(\n*(.+)\n*!x',                   //strip out newlines...
			);
		}
		$text = preg_replace($search, '', $document);
		$text = str_replace("<br /><br />", '' ,$text);
		$text = str_replace("<br />  <br />", "<br />", $text);
		$text = str_replace("<br\">","",$text);
		$text = str_replace("<br />", " <br /> ", $text);
		$text = rtrim($text, "\n");
		return $text;
	}

	/**
	 * The method implements the lucene indexer
	 * The method accepts an array of data,
	 * generates a document to be indexed based on the
	 * url and content inserted into the database 
	 *
	 * @param array $data
	 */
	public function lucenePageIndex($data)
	{
		$objLucene = $this->getObject('indexdata', 'search');

		$docId = 'cms_page_'.$data['id'];

		$url = $this->uri(array
		('module' => 'cms',
		'action' => 'showfulltext',
		'id' => $data['id'],
		'sectionid'=> $data['sectionid']), 'cms');

		$objLucene->luceneIndex($docId, $data['created'], $url, $data['title'], $data['title'].$data['body'], $data['introtext'], 'cms', $data['created_by']);
	}

	/************************************** tbl_cms_htmlblock **************************************/

	/**
    * Method to add / update the block content
    *
    * @access public
    * @param fields array heading, content, contextcode, creatorid
    * @return string $id
    */
	public function updateBlock($id, $fields=array())
	{
		$this->_changeTable('tbl_cms_htmlblock');
		if(isset($id) && !empty($id)){
			$fields['modifier_id'] = $this->userId;
			$fields['updated'] = date('Y-m-d H:i:s');
			$this->update('id', $id, $fields);
		}else{
			$fields['date_created'] = date('Y-m-d H:i:s');
			$id = $this->insert($fields);
		}
		return $id;
	}

	/**
    * Method to get the content for a block
    *
    * @access public
    * @param string $contextCode The current context
    * @return array $data The content
    */
	public function getHtmlBlock($contextCode = NULL, $table='tbl_cms_htmlblock')
	{
		$this->_changeTable('tbl_cms_htmlblock');
		$sql = "SELECT * FROM '$table' ";

		if(!empty($contextCode)){
			$sql .= "WHERE context_code = '$contextCode'";
		}
		$data = $this->getArray($sql);
		if(!empty($data)){
			return $data[0];
		}
		return FALSE;
	}

	/**
    * Display block
    *
    * @access public
    * @param string $contextCode The current context
    * @return string html
    */
	public function displayBlock($contextCode = NULL)
	{
		$this->_changeTable('tbl_cms_htmlblock');
		$block = $this->getBlock($contextCode);
		if($block === FALSE || (empty($block['heading']) && empty($block['content']))){
			return '';
		}
		$objFeatureBox = $this->getObject('featurebox', 'navigation');
		return $objFeatureBox->show($block['heading'], $block['content']);
	}

	/********************************************** 'tbl_cms_menustyles' *********************************/

	/**
    * Method to get the current style
    *
    * @access public
    * return string The style
    */
	public function getActive($table='tbl_cms_menustyles')
	{
		$this->_changeTable('tbl_cms_menustyles');
		$sql = "SELECT menu_style FROM '$table'
        WHERE is_active = '1'";
		$data = $this->getArray($sql);
		if(!empty($data)){
			return $data[0]['menu_style'];
		}
		return 'tree';
	}

	/**
    * Method to get the different styles
    *
    * @access public
    * return array The styles
    */
	public function getStyles($table='tbl_cms_menustyles')
	{
		$this->_changeTable('tbl_cms_menustyles');
		$sql = "SELECT * FROM '$table' ORDER BY menu_style";
		$data = $this->getArray($sql);
		return $data;
	}

	/**
    * Method to update the currently active style
    *
    * @access public
    * @param string $id The id of the new style
    * @return void
    */
	public function updateActive($id)
	{
		$current = $this->getActive();
		$this->update('menu_style', $current, array('is_active' => 0));
		$this->update('id', $id, array('is_active' => 1));
	}


	/************************************************* 'tbl_cms_layouts' ************************************/

	/**
         * Method to get the layouts
         *
         * @access public
         * @return array $layouts An array associative arrays of all layouts
         */
	public function getLayouts()
	{
		$this->_changeTable('tbl_cms_layouts');
		$layouts = $this->getAll();
		return $layouts;
	}

	/**
         * Method to get the layout record
         * 
         * @access public
         * @param string $name The name of the layout
         * @return array $layout An associative array containing the layout details
         */
	public function getLayout($name)
	{
		$this->_changeTable('tbl_cms_layouts');
		$layout = $this->getRow('name', $name);
		return $layout;
	}

	/**
         * Method to get the description of a layout by referencing its name
         * 
         * @access public
         * @param string $name The name of the layout
         * @return string $description The layout description
         */
	public function getLayoutDescription($name)
	{
		$this->_changeTable('tbl_cms_layouts');
		$layout = $this->getRow('name', $name);
		$description = $layout['description'];
		return $description;
	}

	/**
	 * Method to add a RSS feed to the database
	 *
	 * @param string $userid
	 * @param string $name
	 * @param string $desc
	 * @param string $url
	 * @return bool
	 */
	public function addRss($rssarr, $mode = NULL)
	{
		$this->_changeTable("tbl_cms_rss");
		if($mode == NULL)
		{
			return $this->insert($rssarr);
		}
		elseif($mode == 'edit') {
			return $this->update('id', $rssarr['id'], $rssarr, "tbl_cms_rss");
		}
		else {
			return FALSE;
		}
	}

	public function getUserRss($userid)
	{
		$this->_changeTable("tbl_cms_rss");
		return $this->getAll("WHERE userid = '$userid'");
	}

	public function getRssById($id)
	{
		$this->_changeTable("tbl_cms_rss");
		return $this->getAll("WHERE id = '$id'");
	}

	public function delRss($id)
	{
		$this->_changeTable("tbl_cms_rss");
		return $this->delete('id', $id, "tbl_cms_rss");
	}


	/******************************************* 'tbl_groupadmin_group' ************************************/

	/**
	 * Method to return the child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return array The array of child nodes
     * @access public
	 */
	public function getGroupChildNodes($parentId)
	{
		$this->_changeTable('tbl_groupadmin_group');
		try {
			$sql = "SELECT * FROM tbl_groupadmin_group";
			if (!is_null($parentId)) {
				$sql .= " WHERE parent_id = '$parentId'";
			} else {
				$sql .= " WHERE ISNULL(parent_id)";
			}
			return $this->query($sql);
		} catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
	 * Method to return the number of child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return integer The number of records
     * @access public
	 */
	public function getGroupChildNodeCount($parentId)
	{
		$this->_changeTable('tbl_groupadmin_group');
		try {
			$sql = "SELECT COUNT(*) AS cnt FROM tbl_groupadmin_group";
			if (!is_null($parentId)) {
				$sql .= " WHERE parent_id = '$parentId'";
			} else {
				$sql .= " WHERE ISNULL(parent_id)";
			}
			$nodeCount = $this->query($sql);
			if (count($nodeCount)){
				return $nodeCount[0]['cnt'];
			}else{
				return 0;
			}
		} catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
	 * Method to return a particular node
     *
	 * @param string $id The id of the node to return
     * @return array The node record from the db
     * @access public
	 */
	public function getGroupNode($id, $noPermissions = TRUE)
	{
		$this->_changeTable('tbl_groupadmin_group');
		try {
			$sql = "SELECT * FROM tbl_groupadmin_group"
			." WHERE id = '$id'";
			return $this->query($sql);
		} catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/*************************************************** 'tbl_cms_sectiongroup' *********************************/

	/**
	 * Method to return the child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return array The array of child nodes
     * @access public
	 */
	public function getSectionGroupChildNodes($parentId, $admin = FALSE) //$noPermissions)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		try {
			if($admin){
				$sql = "SELECT * FROM tbl_cms_sections
                    WHERE parentid = '$parentId' AND trash = '0'
                    ORDER BY ordering";
			}else{
				$sql = "SELECT * FROM tbl_cms_sections
                    WHERE parentid = '$parentId' AND trash = '0' AND published = 1 
                    ORDER BY ordering";
			}
			$data = $this->getArray($sql);
			return $data;
		} catch(customException $e){
			customException::cleanUp();
			exit();
		}
	}

	/**
	 * Method to return the number of child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return integer The number of records
     * @access public
	 */
	public function getChildNodeCount($parentId, $noPermissions)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		try {
			if ($noPermissions != TRUE) {
				$sql = "SELECT COUNT(*) AS cnt FROM tbl_cms_sections WHERE parentid = '$parentId'";
			} else {
				$userId = $this->objUser->userId();
				$userPKId = $this->objUser->PKid($userId);
				$sql = "SELECT COUNT(*) AS cnt FROM (tbl_cms_sections INNER JOIN tbl_cms_sectiongroup ON tbl_cms_sections.id = tbl_cms_sectiongroup.section_id INNER JOIN tbl_groupadmin_groupuser ON tbl_cms_sectiongroup.group_id = tbl_groupadmin_groupuser.group_id) WHERE parentid = '$parentId' AND user_id = '$userPKId'";
			}
			$nodeCount = $this->query($sql);
			if (count($nodeCount)){
				return $nodeCount[0]['cnt'];
			}else{
				return 0;
			}
		} catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
	 * Method to return a particular node
     *
	 * @param string $id The id of the node to return
     * @return array The node record from the db
     * @access public
	 */
	public function getSectionGroupNode($id, $admin = FALSE) //$noPermissions = TRUE)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		try {
			$sql = "SELECT * FROM tbl_cms_sections WHERE id = '$id'";
			$data = $this->getArray($sql);
			if(!empty($data)){
				return $data[0];
			}
			return $data;

		}catch(customException $e) {
			customException::cleanUp();
			exit();
		}
	}

	/**
	 * Method to return the group_id for a particular section id
     *
	 * @param string $sectionId The id of the section
     * @return boolean The array of child nodes
     * @access public
	 */
	public function getGroupBySection($sectionId)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		try {
			$sql = "SELECT group_id from tbl_cms_sectiongroup WHERE section_id = '$sectionId'";
			$result=$this->query($sql);
			if($result){
				return $result[0]['group_id'];
			} else {
				return FALSE;
			}
		} catch(customException $e) {
			customException::cleanUp();
			exit;
		}
	}


/**
         * Method to see if a certain section exists
         *
         * @access public
         * @param section name
         * @return section array if true else false
         */
    public function getSectionByName($name)
    {
        $this->_changeTable('tbl_cms_sections');

        $res = $this->getAll("WHERE title = '".mysql_escape_string($name)."' OR menutext = '".mysql_escape_string($name)."' LIMIT 1");

        if (count($res) == 0){
            return false;
        } else {
            return $res[0];
        }
    }


	/**
	 * Method to return the section_id for a particular group
     *
	 * @param string $sectionId The id of the section
     * @return boolean or id of section
     * @access public
	 */
	public function getSectionByGroup($groupId)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		try {
			$sql = "SELECT section_id from tbl_cms_sectiongroup WHERE group_id = '$groupId'";
			$result=$this->query($sql);
			if($result){
				return $result[0]['section_id'];
			} else {
				return FALSE;
			}
		} catch(customException $e) {
			customException::cleanUp();
			exit;
		}
	}

	public function addSectionGroup($sectionId,$groupId)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		$newArr = array('section_id' => $sectionId, 'group_id' => $groupId, );
		$newId = $this->insert($newArr);
		return $newId;
	}

	public function editSectionGroup($id,$sectionId,$groupId)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		$newArr = array('section_id' => $sectionId, 'group_id' => $groupId, );
		$newId = $this->update('id', $id, $newArr);
		return $newId;
	}

	public function getSectionGroupId($sectionId)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		try {
			$sql = "SELECT id from tbl_cms_sectiongroup WHERE section_id = '$sectionId'";
			$result=$this->query($sql);
			if($result){
				return $result[0]['id'];
			} else {
				return FALSE;
			}
		} catch(customException $e) {
			customException::cleanUp();
			exit;
		}
	}

	public function getGroupNameBySection($sectionId)
	{
		$this->_changeTable('tbl_cms_sectiongroup');
		try {
			$sql = "SELECT g.name
				from tbl_cms_sectiongroup a,
				tbl_groupadmin_group g
				WHERE g.id=a.group_id
				AND a.section_id = '$sectionId'";
			$result=$this->query($sql);
			if($result){
				return $result[0]['name'];
			} else {
				return FALSE;
			}
		} catch(customException $e) {
			customException::cleanUp();
			exit;
		}
	}


	/************************************************ 'tbl_cms_content_frontpage' ******************************/

	/**
         * Method to save a record to the database
         *
         * @param string $contentId The neContent Id
         * @param int $ordering The number of the page as it appears in the front page order
         * @access public
         * @return string New entry id if inserted else False
         */
	public function addFrontPageContent($contentId, $ordering = 1, $show_content=1)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$fields = array();
		$fields['show_content'] = $show_content;
		// Check for duplicate
		if(!$this->valueExists('content_id',$contentId)) {
			$fields['content_id'] = $contentId;
			$fields['ordering'] = $this->doFPOrdering();
			// Insert entry
			return $this->insert($fields);
		} else {
			// Update entry
			return $this->update('content_id',$contentId, $fields);
		}
	}

	/**
         * Method to remove a record
         *
         * @param string $id The row Id that must be removed
         * @access public
         * @return bool
         */
	public function remove($id)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$result = $this->delete('id', $id);
		$this->reorderFPContent();
		return $result;
	}

	/**
        * Method to remove a record if it exists
        *
        * @access public
        * @param string $pageId The page to remove
        * @return bool
        */
	public function removeIfExists($pageId)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$sql = "SELECT id FROM tbl_cms_content_frontpage
                WHERE content_id = '$pageId'";
		$data = $this->getArray($sql);
		if(!empty($data)){
			$id = $data[0]['id'];
			$this->remove($id);
			return TRUE;
		}
		return '';
	}

	/**
        * Method to check the order of the front page content and remove any negative or null ordering
        *
        * @author Megan Watson
        * @access private
        * @param string $id The content id of the front page being deleted
        * @param integer $pageOrder The order position from which to reorder the content
        * @return void
        */
	private function reorderFPContent()
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		// Get all pages in ascending order
		$pageData = $this->getFrontPages();
		if(!empty($pageData)){
			$i = 1;
			// Reorder the pages
			foreach($pageData as $key => $item){
				$this->update('id', $item['front_id'], array('ordering' => $i));
				$pageData[$key]['pos'] = $i++;
			}
		}
	}

	/**
         * Method to get all the front page id's
         *
         * @return array $allFrontPages An array of oll entries in the content front page table
         * @access public
         */
	public function getFrontPages($published = FALSE)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$sql = "SELECT *, fr.ordering AS pos, fr.id AS front_id
                FROM tbl_cms_content_frontpage AS fr, tbl_cms_content AS co
                WHERE fr.content_id = co.id AND co.trash = '0' ";
		if($published){
			$sql .= "AND co.published = '1' ";
		}

		$sql .= "ORDER BY fr.ordering ASC";
		$data = $this->getArray($sql);
		return $data;
	}


	/**
         * Method to check if a page is a front page
         *
         * @param string $id The id to be checked
         * @access public
         * @return bool
         */
	public function isFrontPage($id)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$isFrontPage = $this->valueExists('content_id',$id);
		return $isFrontPage;
	}

	/**
         * Method to check if a page is a front page
         *
         * @param string $id The id to be checked
         * @access public
         * @return bool
         */
	public function getFrontPage($contentId)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$sql = "SELECT * FROM tbl_cms_content_frontpage WHERE content_id = '$contentId'";
		$data = $this->getArray($sql);
		if(!empty($data)){
			return $data[0];
		}
		return FALSE;
	}

	/**
         * Method to change the status of a page
         *
         * @param string $pageId The id of the page to be changed
         * @param string $mode Remove / add the page to the front page
         * @access public
         * @return bool
         */
	public function changeStatus($id, $mode)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		switch($mode){
			case 'remove':
				return $this->remove($id);
			case 'add':
				return $this->add($id);
		}
	}

	/**
         * Method to update the order of the frontpage
         *
         * @param string $id The id of the entry to move
         * @param int $ordering How to update the order(up or down).
         * @param int $position The current position in the order
         * @access public
         * @return bool
         * @author Warren Windvogel
         */
	public function changeFPOrder($id, $ordering, $position)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		// Get array of all front page entries
		// $fpContent = $this->getAll('ORDER BY ordering');
		switch($ordering){
			case 'up':
				$newPos = $position - 1;
				break;
			case 'down':
				$newPos = $position + 1;
				break;
		}

		// Get the entry to be swapped and update it with the current entries position
		$swapEntry = $this->getRow('ordering', $newPos);
		if(!empty($swapEntry) && !empty($position)){
			$this->update('id', $swapEntry['id'], array('ordering' => $position));
		}

		// Update the current entry with the new position
		if(!empty($id) && !empty($newPos)){
			$this->update('id', $id, array('ordering' => $newPos));
		}

		// Re order the content
		//$this->reorderContent();
		return TRUE;
	}

	/**
         * Method to return the ordering value of new content (gets added last)
         *
         * @return int $ordering The value to insert into the ordering field
         * @access public
         * @author Warren Windvogel
          */
	public function getFPOrdering()
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$ordering = 1;
		//get last order value
		$lastOrder = $this->getAll("ORDER BY ordering DESC LIMIT 1");
		if(!empty($lastOrder)) {
			//add after this value
			$ordering = $lastOrder[0]['ordering'] + 1;
		}
		return $ordering;
	}

	/**
         * Method to return the links to be displayed in the order column on the table
         *
         * @param string $id The id of the entry
         * @return string $links The html for the links
         * @access public
         * @author Warren Windvogel
         * @author Megan Watson modified 27/06/07
         */
	public function getOrderingLinkFP($id, $pos, $number, $total)
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$links = '';

		$lbDown = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
		$lbUp = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');

		$this->objIcon->setIcon('down');
		$this->objIcon->title = $lbDown;
		$icDown = $this->objIcon->show();

		$this->objIcon->setIcon('up');
		$this->objIcon->title = $lbUp;
		$icUp = $this->objIcon->show();

		$this->objIcon->setIcon('downend');
		$this->objIcon->title = $lbDown;
		$icDownEnd = $this->objIcon->show();

		$this->objIcon->setIcon('upend');
		$this->objIcon->title = $lbUp;
		$icUpEnd = $this->objIcon->show();

		// if there are more than 1 entries
		if($total > 1){
			if($number == 1){
				// Add the down end icon
				$objLink = new link($this->uri(array('action' => 'changefporder', 'id' => $id, 'position' => $pos, 'ordering' => 'down')));
				$objLink->link = $icDownEnd;
				$links = $objLink->show();
			}else if($number == $total){
				// Add the up end icon
				$objLink = new link($this->uri(array('action' => 'changefporder', 'id' => $id, 'position' => $pos, 'ordering' => 'up')));
				$objLink->link = $icUpEnd;
				$links = $objLink->show();
			}else{
				$objLink = new link($this->uri(array('action' => 'changefporder', 'id' => $id, 'position' => $pos, 'ordering' => 'up')));
				$objLink->link = $icUp;
				$links = $objLink->show().'&nbsp;';

				$objLink = new link($this->uri(array('action' => 'changefporder', 'id' => $id, 'position' => $pos, 'ordering' => 'down')));
				$objLink->link = $icDown;
				$links .= $objLink->show();
			}
		}
		return $links;
	}


	public function hasFrontPageContent()
	{
		$this->_changeTable('tbl_cms_content_frontpage');
		$sql = "SELECT DISTINCT tbl_cms_content.sectionid FROM tbl_cms_content_frontpage, tbl_cms_content, tbl_cms_sections WHERE (tbl_cms_content_frontpage.content_id = tbl_cms_content.id) AND (tbl_cms_content.sectionid = tbl_cms_sections.id) AND (tbl_cms_content.published = '1') AND (tbl_cms_sections.published = '1')";

		$result = $this->getArray($sql);

		if (count($result) > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/************************************************************** 'tbl_cms_sections' ****************************************/

	/**
         * Method to get the list of sections
         *
         * @access public
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return array An array of associative arrays of all sections
         */
	public function getSections($isPublished = NULL, $filter = null)
	{
		$this->_changeTable('tbl_cms_sections');
		if ($isPublished == NULL && $filter == NULL) {
			return $this->getAll("WHERE published = '1' AND trash = '0' ORDER BY ordering");
		}elseif($filter != NULL) {
			return $this->getAll("WHERE title LIKE '%$filter%' AND trash = '0' ORDER BY ordering");
		}
	}

	/**
     * Method to get a filtered list of sections
     *
     * @author Megan Watson
     * @access public
     * @return array An array of associative arrays of the sections
     */
	public function getFilteredSections($text = NULL, $publish = NULL)
	{
		$this->_changeTable('tbl_cms_sections');
		$sql = "SELECT * FROM tbl_cms_sections ";
		$filter = '';
		if($publish != NULL){
			$filter .= "published = '$publish' ";
		}
		if($text != NULL){
			if(!empty($filter)){
				$filter .= " AND ";
			}
			$filter .= "title LIKE '%".strtolower($text)."%' OR menutext LIKE '%".strtolower($text)."%'";
		}

		if(!empty($filter)){
			$sql .= "WHERE $filter AND trash = '0' ";
		}else{
			$sql .= "WHERE trash = '0' ";
		}
		$sql .= ' ORDER BY ordering';

		return $this->getArray($sql);
	}

	/**
     * Method to get the archived content
     *
     * @author Megan Watson
     * @param string $filter The Filter
     * @return  array An array of associative arrays of all content pages in relationto filter specified
     * @access public
     */
	public function getArchiveSections($filter = NULL)
	{
		$this->_changeTable('tbl_cms_sections');
		$sql = "SELECT * FROM tbl_cms_sections WHERE trash = '1' ";

		if($filter != NULL){
			$sql .= "AND title LIKE '%".strtolower($filter)."%' ";
		}

		$sql .= 'ORDER BY ordering';
		return $this->getArray($sql);
	}

	/**
     * Method to get the list of root nodes
     *
     * @access public
     * @param bool $isPublished TRUE | FALSE To get published sections
     * @param string contextcode The current context the user is in
     * @return array An array of associative arrays of all root nodes
     */
	public function getRootNodes($isPublished = 'FALSE', $contextcode = NULL)
	{
		$this->_changeTable('tbl_cms_sections');
		$sql = '';
		// Check for published / visible
		if($isPublished == 'TRUE'){
			$sql = "published = '1' ";
		}
		// Check for the context code
		if(!empty($contextcode)){
			if(!empty($sql)){
				$sql .= "AND ";
			}
			$sql .= "contextcode = '$contextcode' ";
		}

		if(!empty($sql)){
			$sql .= "AND ";
		}

		$filter = "WHERE {$sql} nodelevel = '1' AND trash = '0' ORDER BY ordering";
		$results = $this->getAll($filter);
		return $results;
	}

	/**
     * Method to get a Section
     *
     * @param  string $id The section id
     * @return array An array of the sections details
     * @access public
     */
	public function getSection($id)
	{
		$this->_changeTable('tbl_cms_sections');
		return $this->getRow('id', $id);
	}

	/**
     * Method to get the first sections id(pk)
     *
     * @param bool $isPublished TRUE | FALSE To get published sections
     * @return string First sections id
     * @access public
     */
	public function getFirstSectionId($isPublished = 'FALSE')
	{
		$this->_changeTable('tbl_cms_sections');
		$firstSectionId = '';
		$firstSection = $this->getAll("WHERE parentid='0' AND trash = '0' ORDER BY ordering");
		if(!empty($firstSection)) {
			if($isPublished != 'FALSE') {
				foreach($firstSection as $section) {
					if($section['published'] == 1) {
						$firstSectionId = $section['id'];
						break;
					}
				}
			} else {
				$firstSectionId = $firstSection['0']['id'];
			}
		}
		return $firstSectionId;
	}

	/**
     * Method to add a section to the database
     *
     * @access public
     * @return bool
     */
	public function addSection($sectionArr)
	{
		$this->_changeTable('tbl_cms_sections');

		//get parent type "subsection", "root" or "param is null"(new section will be root level) and its id
		$parentid = $sectionArr['parentselected'];
		
		$node = $this->getLevel($parentid); 
		//$parentid = $node;
		if ($node == 1 || $node == 0) {

			$rootid = $node;
			$rootnode = $this->checkindex($rootid);
			//Get section details
			if($sectionArr['pagenum'] == 'custom') {
				$customnumber = $sectionArr['pagenum'];
				$numpagedisplay = $customnumber;
			} else {
				$pagenumber = $sectionArr['pagenum'];
				$numpagedisplay = $pagenumber;
			}
			
			if($sectionArr['access'] == '' || $sectionArr['access'] === NULL)
			{
				$access = 0;
			}
			else {
				$access = $sectionArr['access'];
			}

			$ordering = $this->getContentOrdering($parentid); // $this->getFPOrdering($parentid);

			//Add section
			$index = array(
			'rootid' => $parentid, //$rootid, //rootid?????????
			'parentid' => $parentid,
			'title' => $sectionArr['title'],
			'menutext' => $sectionArr['menutext'],
			'access' => $access,
			'layout' => $sectionArr['layout'],
			'ordering' => $ordering,
			'description' => $sectionArr['description'],
			'published' => $sectionArr['published'],
			'hidetitle' => $sectionArr['hidetitle'],
			'showdate' => $sectionArr['showdate'],
			'showintroduction' => $sectionArr['showintroduction'],
			'numpagedisplay' => $numpagedisplay,
			'ordertype' => $sectionArr['ordertype'],
			'nodelevel' => $this->getLevel($parentid) + '1',
			'datecreated'=>$this->now(),
			'userid' => $sectionArr['userid'],
			'link' => $sectionArr['imagesrc'],
			'contextcode' =>$sectionArr['contextcode'],
			);
			$result = $this->insert($index);

			if ($result != FALSE) {
				$index['id'] = $result;
				//$this->luceneSectionIndex($index);
			}

			return $result;


		} else {
			$rootid = $this->getRootNodeId($id);
			$rootnode = $this->checkindex($rootid);
			$user = $this->_objUser->userId();
			if($sectionArr['pagenum'] == 'custom') {
				$numpagedisplay = $customnumber;
			} else {
				$numpagedisplay = $pagenumber;
			}
			$ordertype = $sectionArr['ordertype'];
			$ordering = $this->getFPOrdering($parentid);

			// Add section
			$index = array(
			'rootid' => $rootid,
			'parentid' => $parentid,
			'title' => $sectionArr['title'],
			'menutext' => $sectionArr['menutext'],
			'access' => $sectionArr['access'],
			'layout' => $sectionArr['layout'],
			'ordering' => $ordering,
			'description' => $sectionArr['description'],
			'published' => $sectionArr['published'],
			'hidetitle' => $sectionArr['hidetitle'],
			'showdate' => $sectionArr['showdate'],
			'showintroduction' => $sectionArr['showintroduction'],
			'numpagedisplay' => $numpagedisplay,
			'ordertype' => $sectionArr['ordertype'],
			'nodelevel' => $this->getLevel($parentid) + '1',
			'datecreated'=>$this->now(),
			'userid' => $sectionArr['userid'],
			'link' => $sectionArr['imagesrc'],
			'contextcode' =>$sectionArr['contextcode'],
			);

			$result = $this->insert($index);

			if ($result != FALSE) {
				$index['id'] = $result;
				//$this->luceneSectionIndex($index);
			}

			return $result;


		}

	}

	private function checkindex($rootid=null,$parentid=null)
	{
		$this->TreeNodes = & $this->newObject('treenodes', 'cmsadmin');
		$this->_changeTable('tbl_cms_sections');
		$rootid = $this->TreeNodes->getArtifact($rootid);
		return $rootid;
	}

	/**
         * Method to edit a section in the database
         *
         * @access public
         * @return bool
         */
	public function editSection($id = NULL, $parentid = NULL, $rootid = NULL, $title = NULL, $menuText = NULL, $access = NULL, $description = NULL, $published = NULL, $layout = NULL, $showdate = NULL, $hidetitle = NULL, $showintroduction = NULL, $pagenum = NULL, $customnumber = NULL, $ordertype = NULL, $ordering = NULL, $count = NULL, $imagesrc = NULL)
	{
		$this->_changeTable('tbl_cms_sections');
		if($pagenum == 'custom') {
			$numpagedisplay = $customnumber;
		} else {
			$numpagedisplay = $pagenum;
		}
		$arrFields = array(
		'rootid' => $rootid,
		'parentid' => $parentid,
		'title' => $title,
		'menutext' => $menuText,
		'access' => $access,
		'layout' => $layout,
		'ordering' => $ordering,
		'showdate' => $showdate,
		'hidetitle' => $hidetitle,
		'showintroduction' => $showintroduction,
		'numpagedisplay' => $numpagedisplay,
		'ordertype' => $ordertype,
		'description' => $description,
		'nodelevel' => $count,
		'lastupdatedby'=> $this->_objUser->userid(),
		'updated' => $this->now(),
		'link' => $imagesrc,
		'published' => $published);
		$result = $this->update('id', $id, $arrFields);

		if ($result != FALSE) {
			$arrFields['id'] = $result;
			$this->luceneSectionIndex($arrFields);
		}
		return $result;
	}

	/**
         * Method to check if there is sections
         *
         * @access public
         * @return boolean
         */
	public function isSections()
	{
		$this->_changeTable('tbl_cms_sections');
		$list = $this->getAll();
		if (count($list) > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
         * Method to get the menutext for a section
         *
         * @return string $menutext The title that will appear on the tree menu
         * @access public
         * @param string $id The id of the section
         */
	public function getSectionMenuText($id)
	{
		$this->_changeTable('tbl_cms_sections');
		$line = $this->getSection($id);
		$menutext = $line['menutext'];
		return $menutext;
	}

	/**
         * Method to toggle the publish field 
         * 
         * @param string id The id if the section
         * @access public
         * @return boolean
         * @author Wesley Nitsckie
         */
	public function toggleSectionPublish($id)
	{
		$this->_changeTable('tbl_cms_sections');
		$row = $this->getSection($id);
		if ($row['published'] == 1) {
			return $this->update('id', $id , array('published' => 0) );
		} else {
			return $this->update('id', $id , array('published' => 1) );
		}
	}

	/**
         * Method to publish or unpublish sections 
         * 
         * @param string id The id if the section
         * @param string $task Publish or unpublish
         * @access public
         * @return boolean
         * @author Megan Watson
         */
	public function publishSection($id, $task = 'publish')
	{
		$this->_changeTable('tbl_cms_sections');
		switch($task){
			case 'publish':
				$fields['published'] = 1;
				break;
			case 'unpublish':
				$fields['published'] = 0;
				break;
		}
		return $this->update('id', $id, $fields);
	}

	/**
         * Method to check if a section has child/leaf node(s)
         *
         * @param string $id The id(pk) of the section
         * @return bool True if has nodes else False
         * @access public
         */
	public function hasNodesSection($id)
	{
		$this->_changeTable('tbl_cms_sections');
		$nodes = $this->getAll("WHERE parentid = '$id' AND trash = '0'");
		if (count($nodes) > 0) {
			$hasNodes = True;
		} else {
			$hasNodes = False;
		}
		return $hasNodes;
	}

	/**
         * Method to return the count value of a section
         *
         * @param string $id The id(pk) of the section
         * @return int $count The value of the count field
         * @access public
         */
	public function getLevel($id)
	{
		$this->_changeTable('tbl_cms_sections');
		$count = 0;
		// get entry
		$section = $this->getRow('id', $id);
		if (!empty($section)) {
			// get and return value of count field
			$count = $section['nodelevel'];
		}
		return $count;
	}

	/**
         * Method to return a sections root node id
         *
         * @param string $id The id(pk) of the section
         * @return string $rootId The id(pk) of the sections root node
         * @access public
         */
	public function getRootNodeId($id)
	{
		$this->_changeTable('tbl_cms_sections');
		// get entry
		$section = $this->getRow('id', $id);
		// get and return value of count field
		$rootId = $section['rootid'];
		return $rootId;
	}

	/**
        * Method to return all sections
        *
        * @access public
        */
	public function getAllSections()
	{
		$this->_changeTable('tbl_cms_sections');
		return $this->getAll("WHERE trash = '0'");
	}

	/**
         * Method to get all subsections in a specific section
         *
         * @param string $sectionId The id(pk) of the section
         * @param int $level The node level in question  
         * @param string $order Either DESC or ASC
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return array $subsections An array of associative arrays for all categories in the section
         * @access public
         */
	public function getSubSectionsInSection($sectionId, $order = 'ASC', $isPublished = FALSE)
	{
		$this->_changeTable('tbl_cms_sections');
		if ($isPublished) {
			// return all subsections
			return $this->getAll("WHERE published = '1' AND parentid = '$sectionId' AND trash = '0' ORDER BY ordering '$order'");
		} else {
			return $this->getAll("WHERE parentid = '$sectionId' AND trash = '0' ORDER BY ordering '$order'");
		}
	}

	/**
         * Method to get all subsections in a specific root
         *
         * @param string $rootId The id(pk) of the section
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return array $subsections An array of associative arrays for all categories in the section
         * @access public
         */
	public function getSubSectionsInRoot($rootId, $order = 'ASC',$isPublished = FALSE)
	{
		$this->_changeTable('tbl_cms_sections');
		if ($isPublished) {
			// return all subsections
			return $this->getAll("WHERE published = '1' AND rootid = '$rootId' AND trash = '0' ORDER BY ordering");
		} else {
			return $this->getAll("WHERE rootid = '$rootId' AND trash = '0' ORDER BY ordering $order");
		}
	}

	/**
         * Method to get all subsections in a specific level
         *
         * @param string $rootId The id(pk) of the sections root node
         * @param int $level The node level in question  
         * @param int $order Either DESC or ASC 
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return array $subsections An array of associative arrays for all sub sections in the section
         * @access public
         */
	public function getSubSectionsForLevel($rootId, $level, $order = 'ASC', $isPublished = FALSE)
	{
		$this->_changeTable('tbl_cms_sections');
		if ($isPublished) {
			// return all subsections
			return $this->getAll("WHERE published = '1' AND nodelevel = '$level' AND rootid = '$rootId' AND trash = '0' ORDER BY ordering $order");
		} else {
			return $this->getAll("WHERE nodelevel = '$level' AND rootid = '$rootId' AND trash = '0' ORDER BY ordering $order");
		}
	}

	/**
         * Method to get the number of sub sections in a section
         *
         * @param string $sectionId The id(pk) of the section
         * @return int $noSubSecs The number of subsections in the section
         * @access public
         */
	public function getNumSubSections($sectionId)
	{
		$this->_changeTable('tbl_cms_sections');
		$subSecs = $this->getAll("WHERE parentid = '$sectionId' AND trash = '0'");
		$noSubSecs = count($subSecs);
		return $noSubSecs;
	}

	/**
         * Method to delete a section
         *
         * @param string $id The id(pk) of the section
         * @return bool
         * @access public
         */
	public function deleteSection($id)
	{
		$this->_changeTable('tbl_cms_sections');
		$sectionData = $this->getSection($id);
		// if section is root - archive everything below it
		if($sectionData['nodelevel'] == 1){
			$nodes = $this->getAll("WHERE rootid = '$id'");
			if(!empty($nodes)){
				foreach($nodes as $item){
					$this->resetSection($item['id']);
					$this->archive($item['id']);
				}
			}
			// Restore root node
			$this->resetSection($id);
			$this->archive($id);
		}else{
			// find nodes below section
			$nodeData = $this->getAll("WHERE parentid = '$id'");
			if(!empty($nodeData)){
				foreach($nodeData as $item){
					$this->deleteSection($item['id']);
				}
			}
			$this->resetSection($id);
			$this->archive($id);
		}
	}

	/**
        * Method to archive a section
        *
        * @access private
        * @param string $id The section id
        * @return bool
        */
	private function archive($id, $restore = FALSE)
	{
		$this->_changeTable('tbl_cms_sections');
		$trash = 1;
		$order = '';

		if($restore){
			$trash = 0;
			$order = $this->getSectionOrdering($id);
		}
		$fields = array('trash' => $trash, 'ordering' => $order);
		$result =  $this->update('id', $id, $fields);

		$this->removeLuceneIndex($id);
	}

	/**
        * Method to restore a section
        *
        * @access public
        * @param string $id The section id
        * @return bool
        */
	public function unarchiveSection($id)
	{
		$this->_changeTable('tbl_cms_sections');
		$sectionData = $this->getSection($id);

		$this->luceneSectionIndex($sectionData);

		if($sectionData['nodelevel'] == 1){
			$nodes = $this->getAll("WHERE rootid = '$id'");

			if(!empty($nodes)){
				foreach($nodes as $item){
					$this->unarchiveSectionContent($item['id']);
					$this->archive($item['id'], TRUE);
				}
			}
			// Restore root node
			$this->unarchiveSectionContent($id);
			$this->archive($id, TRUE);
		}else{
			// find nodes below section
			$nodeData = $this->getAll("WHERE parentid = '$id'");

			if(!empty($nodeData)){
				foreach($nodeData as $item){
					$this->unarchiveSection($item['id']);
				}
			}
			$this->unarchiveSectionContent($id);
			$this->archive($id, TRUE);
		}
	}

	/**
        * Method to loop through and restore the content in a section
        *
        * @access private
        * @param string $id The section id
        * @return bool
        */
	private function unarchiveSectionContent($id)
	{
		$this->_changeTable('tbl_cms_sections');
		return $this->unarchiveSection($id);
	}

	/**
        * Method to permanently delete a section
        *
        * @access public
        * @param string $id The section id
        * @return bool
        */
	public function permanentlyDelete($id)
	{
		$this->_changeTable('tbl_cms_sections');
		$result = $this->delete('id', $id);

		if ($result) {
			$this->removeLuceneIndex($id);
		}

		return $result;
	}

	/**
         * Method to return the ordering value of new section (gets added last)
         *
         * @param string $parentid The id(pk) of the parent. Uses root node order if NULL
         * @return int $ordering The value to insert into the ordering field
         * @access public
         * @author Warren Windvogel
         */
	public function getSectionOrdering($parentid = NULL)
	{
		$this->_changeTable('tbl_cms_sections');
		$ordering = 1;
		//get last order value
		$lastOrder = $this->getAll("WHERE parentid = '$parentid' AND trash = '0' ORDER BY ordering DESC LIMIT 1");
		//add after this value

		if (!empty($lastOrder)) {
			$ordering = $lastOrder[0]['ordering'] + 1;
		}

		return $ordering;
	}

	/**
         * Method to return the links to be displayed in the order column on the table
         * 
         * @param string $id The id of the entry 
         * @return string $links The html for the links
         * @access public
         * @return bool
         * @author Warren Windvogel
         */
	public function getOrderingLinkSection($id)
	{
		$this->_changeTable('tbl_cms_sections');
		// Get the parent id
		$entry = $this->getRow('id', $id);
		$parentId = $entry['parentid'];

		if (empty($parentId)) {
			//Get the number of root sections
			$lastOrd = $this->getAll("WHERE nodelevel = '1' AND trash = '0' ORDER BY ordering DESC LIMIT 1");
		} else {
			//Get the number of sub sections in section
			$lastOrd = $this->getAll("WHERE parentid = '$parentId' AND trash = '0' ORDER BY ordering DESC LIMIT 1");
		}

		$topOrder = $lastOrd[0]['ordering'];
		$links = " ";

		if ($topOrder > 1) {
			//Create geticon obj
			$this->objIcon =  $this->newObject('geticon', 'htmlelements');

			if ($entry['ordering'] == 1) {
				//return down arrow link
				//icon
				$this->objIcon->setIcon('downend');
				$this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
				//link
				$downLink =  $this->newObject('link', 'htmlelements');
				$downLink->href = $this->uri(array('action' => 'changesectionorder', 'id' => $id, 'ordering' => 'up', 'parent' => $entry['parentid']));
				$downLink->link = $this->objIcon->show();
				$links .= $downLink->show();
			} else if ($entry['ordering'] == $topOrder) {
				//return up arrow
				//icon
				$this->objIcon->setIcon('upend');
				$this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
				//link
				$upLink = & $this->newObject('link', 'htmlelements');
				$upLink->href = $this->uri(array('action' => 'changesectionorder', 'id' => $id, 'ordering' => 'down', 'parent' => $entry['parentid']));
				$upLink->link = $this->objIcon->show();

				$links .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $upLink->show();
			} else {
				//return both arrows
				//icon
				$this->objIcon->setIcon('down');
				$this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
				//link
				$downLink = & $this->newObject('link', 'htmlelements');
				$downLink->href = $this->uri(array('action' => 'changesectionorder', 'id' => $id, 'ordering' => 'up', 'parent' => $entry['parentid']));
				$downLink->link = $this->objIcon->show();
				//icon
				$this->objIcon->setIcon('up');
				$this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
				//link
				$upLink = & $this->newObject('link', 'htmlelements');
				$upLink->href = $this->uri(array('action' => 'changesectionorder', 'id' => $id, 'ordering' => 'down', 'parent' => $entry['parentid']));
				$upLink->link = $this->objIcon->show();
				$links .= $downLink->show() . '&nbsp;' . $upLink->show();
			}
		}

		return $links;
	}

	/**
         * Method to update the order of the frontpage
         * 
         * @param string $id The id of the entry to move
         * @param int $ordering How to update the order(up or down).
         * @access public
         * @return bool
         * @author Warren Windvogel
         */
	public function changeSectionOrder($id, $ordering, $parentid)
	{
		$this->_changeTable('tbl_cms_sections');
		// Get array of all sections in level
		$fpContent = $this->getAll("WHERE parentid = '$parentid' AND trash = '0' ORDER BY ordering ");
		// Search for entry to be reordered and update order
		foreach($fpContent as $content) {
			if ($content['id'] == $id) {
				if ($ordering == 'up') {
					$changeTo = $content['ordering'];
					$toChange = $content['ordering'] + 1;
					$updateArray = array(
					'ordering' => $toChange
					);
					$this->update('id', $id, $updateArray);
				} else {
					$changeTo = $content['ordering'];
					$toChange = $content['ordering'] - 1;
					$updateArray = array(
					'ordering' => $toChange
					);
					$this->update('id', $id, $updateArray);
				}
			}
		}

		//Get other entry to change
		$entries = $this->getAll("WHERE parentid = '$parentid' AND ordering = '$toChange' AND trash = '0'");
		foreach($entries as $entry) {
			if ($entry['id'] != $id) {
				$upArr = array(
				'ordering' => $changeTo
				);
				$this->update('id', $entry['id'], $upArr);
			}
		}
		$this->reorderSections($parentid);
	}

	/**
        * Method to reorder the sections
        *
        * @author Megan Watson
        * @param string $parentid The parent id of the sections to be re ordered
        * @access private
        * @return void
        */
	private function reorderSections($parentid)
	{
		$this->_changeTable('tbl_cms_sections');
		// Get all pages
		$sectionData = $this->getAll("WHERE parentid = '$parentid' AND trash = '0' ORDER BY ordering ");

		if(!empty($sectionData)){

			$i = 1;
			foreach($sectionData as $key => $item){
				$this->update('id', $item['id'], array('ordering' => $i));
				$sectionData[$key]['ordering'] = $i++;
			}

			// Get the ordering position of the last page
			$newData = array_reverse($sectionData);
			$lastOrder = $newData[0]['ordering']+1;

			// Remove all null and negative numbers
			foreach($sectionData as $key => $item){
				if($item['ordering'] < 0 || is_null($item['ordering'])){
					$this->update('id', $item['id'], array('ordering' => $lastOrder++));
				}
			}
		}
	}

	/**
        * Method to get the type of section in a human readable format
        *
        * @access public
        * @param string $orderType Type of Order Code
        * @return string containing the type of order in a human readable format.
        */
	public function getPageOrderType($orderType)
	{
		$this->_changeTable('tbl_cms_sections');
		switch ($orderType) {
			case 'pageorder':
				$order = $this->_objLanguage->languageText('mod_cmsadmin_order_pageorder', 'cmsadmin');
				break;

			case 'pagedate_asc':
				$order = $this->_objLanguage->languageText('mod_cmsadmin_order_pagedate_asc', 'cmsadmin');
				break;

			case 'pagedate_desc':
				$order = $this->_objLanguage->languageText('mod_cmsadmin_order_pagedate_desc', 'cmsadmin');
				break;

			case 'pagetitle_asc':
				$order = $this->_objLanguage->languageText('mod_cmsadmin_order_pagetitle_asc', 'cmsadmin');
				break;

			case 'pagetitle_desc':
				$order = $this->_objLanguage->languageText('mod_cmsadmin_order_pagetitle_desc', 'cmsadmin');
				break;

			default:
				$order = $this->_objLanguage->languageText('word_unknown');
				break;
		}

		return $order;
	}


	/**
         * Method to add a section to the search database
         * @param array $data
         */
	public function luceneSectionIndex($data)
	{
		$objLucene = $this->getObject('indexdata', 'search');

		$docId = 'cms_section_'.$data['id'];
		$url = $this->uri(array('action' => 'showsection', 'id' => $data['id']), 'cms');

		$objLucene->luceneIndex($docId, $data['creation'], $url, $data['title'], $data['title'].$data['body'], $data['description'], 'cms', $data['userid']);
	}

	public function removeLuceneIndex($id)
	{
		$objLucene = $this->getObject('indexdata', 'search');
		$objLucene->removeIndex('cms_section_'.$id);
	}


	/**
         * Method to get the section id from the 
         * contextcode
         * @param string $contextCode The Context Code
         * @return string
         * @access public
         * @author Wesley Nitsckie
         * 
         */
	public function getSectionByContextCode()
	{
		$this->_changeTable('tbl_cms_sections');
		$objDBContext = $this->getObject('dbcontext', 'context');
		$contextCode = $objDBContext->getContextCode();
		//return $this->getAll("WHERE contextCode='".$contextCode."' AND rootid=0" );
		$ret =  $this->getRow("contextcode", $contextCode);

		if($ret == FALSE)
		{
			// create an entry
			$this->addNewSection(0,
			$objDBContext->getTitle(),
			$objDBContext->getMenuText(),
			0,
			$objDBContext->getAbout(),
			1,
			'page',
			1,
			1,
			1,
			'pageorder',
			$contextCode);
			return $this->getSectionByContextCode();
		} else {
			return $ret;
		}
	}

	/**
     * Method to return the ordering value of new blocks (gets added last)
     *
     * @param string $pageid The id(pk) of the page the block is attached to
     * @return int $ordering The value to insert into the ordering field
     * @access public
     * @author Warren Windvogel
     */
	private function _getBlockOrdering($pageid, $sectionid, $blockCat)
	{
		$this->_changeTable('tbl_cms_blocks');
		$ordering = 1;
		if ($blockCat == 'frontpage') {
			// Get last in order
			$frontPage = 1;
			$lastOrder = $this->getAll("WHERE frontpage_block = '$frontPage' ORDER BY ordering DESC LIMIT 1");
		} else if ($blockCat == 'content') {
			// Get last in order
			$lastOrder = $this->getAll("WHERE pageid = '$pageid' ORDER BY ordering DESC LIMIT 1");
		} else {
			// Get last in order
			$lastOrder = $this->getAll("WHERE sectionid = '$sectionid' ORDER BY ordering DESC LIMIT 1");
		}
		// add after this value
		if (!empty($lastOrder)) {
			$ordering = $lastOrder[0]['ordering'] + 1;
		}
		return $ordering;
	}

	/**
     * Method to dynamically switch tables
     *
     * @param  string  $table
     * @return boolean
     * @access private
     */
	private function _changeTable($table)
	{
		try {
			parent::init($table);
			return TRUE;
		}
		catch(customException $e) {
			customException::cleanUp();
			exit;
		}
	}

}
?>
