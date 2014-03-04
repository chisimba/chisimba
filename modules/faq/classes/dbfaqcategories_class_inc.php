<?php
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


/**
* Model class for the table tbl_faq_categories
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbfaqcategories extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init() 
    {
        parent::init('tbl_faq_categories');
    } 

    /**
    * Get category id. (called in getContextLinks() function in modulelinks class)
    * @author Nonhlanhla Gangeni <noegang@gmail.com>
    */
    public function getCatId($contextId)
    {
        return $this->getRow("contextid", $contextId);
    }

    /**
    * Return all records
    * @param string $contextId The context ID
    * @return array The categories
    */
    public function getContextCategories($contextId)
    {
        return $this->getAll("WHERE contextid='{$contextId}' ORDER BY categoryname");
    }
    
    /**
     * 
    * Return the last four categories to be added (inserted by DWK)
    * @param string $contextId The context ID
    * @return array The categories
    * 
    */
    public function getLatestContextCategories($contextId)
    {
        $sql = "SELECT * FROM tbl_faq_categories WHERE contextid='{$contextId}' ORDER BY datelastupdated DESC ";
        return $this->getArrayWithLimit($sql, 0, 4);
    }
    
    /**
    * Method to get the number of categories for a context
    * @param string $contextCode The Context Code
    * @return array The categories
    */
    public function getNumContextCategories($contextCode)
    {
        return $this->getRecordCount("WHERE contextid='{$contextCode}'");
    }

    /**
    * Return a single record
    * @param string $contextId The context ID
    * @param string $categoryId The category ID
    * @return array The category
    */	
    public function listSingle($contextId, $categoryId)
    {
        $sql = "SELECT * FROM tbl_faq_categories 
        WHERE contextid = '" . $contextId . "' 
        AND categoryname='" . $categoryId . "'";
        return $this->getArray($sql);
        //return $this->getRow("id", $id);
    }

    /**
    * Return a single record from the id
    * @param string $id The ID
    * @return array The category
    */	
    public function listSingleId($id)
    {
        $sql = "SELECT * FROM tbl_faq_categories 
        WHERE id = '" . $id . "'";
        //return $this->getArray($sql);
        return $this->getRow("id", $id);
    }

    /**
    * Insert a record
    * @param string $contextId The context ID
    * @param string $categoryName The category name
    * @param string $userId The user ID
    */
    public function insertSingle($contextId, $categoryName, $userId)
    {
        $id = $this->insert(array(
            'contextid' => $contextId, 
            'categoryname' => $categoryName,
            'userid' => $userId,
            'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
            ));
        
        if ($id != FALSE) {
            $this->createDynamicBlocks($id, $contextId, $categoryName);
        }
        
        return $id;
    }
    
    /**
    * Update a record
    * @param string $id ID
    * @param string $categoryName The category
    * @param string $userId The user ID
    * @param string $dateLastUpdated Date last updated
    */
    public function updateSingle($id, $categoryName, $userId, $dateLastUpdated)
    {
        $result = $this->update("id", $id, 
            array(
                'categoryname' => $categoryName,
                'userid' => $userId,
                'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
            )
        );
        
        $category = $this->getRow("id", $id);
        
        $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
        if ($category['contextid'] == 'root') {
            $objDynamicBlocks->updateTitle('faq', 'dynamicblocks_faq', 'renderCategory', $id, 'site', 'FAQ: '.$categoryName);
        } else {
            $objDynamicBlocks->updateTitle('faq', 'dynamicblocks_faq', 'renderCategory', $id, 'context', 'FAQ: '.$categoryName);
        }
        
        return $result;
    }
    
    /**
    * Delete a record
    * @param string $id ID
    */
    public function deleteSingle($id)
    {
        $category = $this->getRow("id", $id);
        $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
        
        if ($category['contextid'] == 'root') {
            $objDynamicBlocks->removeBlock('faq', 'dynamicblocks_faq', 'renderCategory', $id, 'site');
        } else {
            $objDynamicBlocks->removeBlock('faq', 'dynamicblocks_faq', 'renderCategory', $id, 'context');
        }
        
        return $this->delete("id", $id);
    }

    
    /**
    * Return the latest Category created or updated
    * @param string $contextId The context ID
    * @return array The category id
    */
    public function getLastestCategory($contextId)
    {
        $sql = 'SELECT id FROM tbl_faq_categories WHERE contextid = "'
          . $contextId.'" AND datelastupdated = (SELECT MAX(datelastupdated) FROM tbl_faq_categories)';
        $results = $this->getArray($sql);
        $latest = current($results);
        return $latest['id'];
    }
    
    /**
     * Method to create dynamic blocks for a faq category
     * @param string $id Record Id of the Category
     * @param string $context Context
     * @param string $categoryName Name of the Category
     */
    private function createDynamicBlocks($id, $context, $categoryName)
    {
        $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
        
        $title = 'FAQ: '.$categoryName;
        
        if ($context == 'root') {
            // Add Chapter Block
            $result = $objDynamicBlocks->addBlock('faq', 'dynamicblocks_faq', 'renderCategory', $id, $title, 'site', NULL, 'wide');
        } else {
            // Add Chapter Block
            $result = $objDynamicBlocks->addBlock('faq', 'dynamicblocks_faq', 'renderCategory', $id, $title, 'context',  $context, 'wide');
        }
    }


}

?>