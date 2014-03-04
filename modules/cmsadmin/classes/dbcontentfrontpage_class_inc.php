<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Data access class for the cmsadmin module. Used to access data in the content front page table.
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Wesley  Nitsckie
* @author Warren Windvogel
*/

class dbcontentfrontpage extends dbTable
{

        /**
        * The user  object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

        /**
        * The language  object
        *
        * @access private
        * @var object
        */
        protected $_objLanguage;

	   /**
	    * Class Constructor
	    *
	    * @access public
	    * @return void
	    */
        public function init()
        {
        	try {
                parent::init('tbl_cms_content_frontpage');
                
                $this->_objUser = $this->getObject('user', 'security');
                $this->_objSecurity = $this->getObject('dbsecurity', 'cmsadmin');
                $this->_objLanguage = $this->newObject('language', 'language');
				$this->_objUserPerm = $this->getObject ('dbuserpermissions', 'cmsadmin');
                
                $this->objIcon = $this->newObject('geticon', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
           } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }

        /**
         * Method to save a record to the database
         *
         * @param string $contentId The neContent Id
         * @param int $ordering The number of the page as it appears in the front page order
         * @access public
         * @return string New entry id if inserted else False
         */
        public function add($contentId, $ordering = 1)
        {
            if (!$this->_objUserPerm->canAddToFrontPage()) {
				return FALSE;
			}
			$show_content = $this->getParam('show_content',0);
                
            $fields = array();
            $fields['show_content'] = $show_content;
            
            // Check for duplicate
            if(!$this->valueExists('content_id',$contentId)) {
                
                $fields['content_id'] = $contentId;
                $fields['ordering'] = $this->getOrdering();
                
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
        private function remove($id)
        {
			if (!$this->_objUserPerm->canAddToFrontPage()) {
				return FALSE;
			}
            log_debug('called delete from frontpage manager for id ' . $id);
            $result = $this->delete('id', $id);
            
            $this->reorderContent();
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
			$data = $this->getRow('content_id', $pageId);
            
            if ($data){
                $this->remove($data['id']);
                return TRUE;
            }
            return FALSE;
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
        private function reorderContent()
        {   
            // Get all pages in ascending order
            $pageData = $this->getFrontPages();
                        
            if(!empty($pageData)){
                $i = 1;
                // Reorder the pages
                foreach($pageData as $key => $item){
                    $this->update('id', $item['front_id'], array('ordering' => $i));
                    $pageData[$key]['pos'] = $i++;
                }
                        
                /* Get the ordering position of the last page
                $newData = array_reverse($pageData);
                $lastOrder = $newData[0]['pos']+1;
                            
                // Remove all null and negative numbers
                foreach($pageData as $key => $item){
                    if($item['pos'] < 0 || is_null($item['pos'])){
                        $this->update('id', $item['id'], array('ordering' => $lastOrder++));
                    }
                }
                */
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
            $sql = 'SELECT *, fr.ordering AS pos, fr.id AS front_id 
                FROM tbl_cms_content_frontpage AS fr, tbl_cms_content AS co
                WHERE fr.content_id = co.id AND co.trash = 0 ';
                
            if($published){
                $sql .= 'AND co.published = 1 ';
            }
                
            $sql .= 'ORDER BY fr.ordering ASC';
                
            $data = $this->getArray($sql);

	    //Filtering the list with content the user has READ ACCESS to
	    $secureData = array();
	    foreach ($data as $row){
		if ($this->_objSecurity->canUserReadContent($row['id'])){
		    array_push($secureData, $row);
		}
	    }

            return $secureData; 
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
            $sql = "SELECT * FROM tbl_cms_content_frontpage WHERE content_id = '{$contentId}'";
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
        public function changeOrder($id, $ordering, $position)
        {
            //Get array of all front page entries
            //$fpContent = $this->getAll('ORDER BY ordering');
            
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
        public function getOrdering()
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
         * @author Megan Watson modified 27/06/07
         */
        public function getOrderingLink($id, $pos, $number, $total)
        {
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
			$sql = 'SELECT DISTINCT tbl_cms_content.sectionid FROM tbl_cms_content_frontpage, tbl_cms_content, tbl_cms_sections WHERE (tbl_cms_content_frontpage.content_id = tbl_cms_content.id) AND (tbl_cms_content.sectionid = tbl_cms_sections.id) AND (tbl_cms_content.published = 1) AND (tbl_cms_sections.published = 1)';
			
			$result = $this->getArray($sql);
			
			if (count($result) > 0) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
}
?>
