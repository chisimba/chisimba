<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

// end security check
/**
* Class to save user defined html in the database for viewing in a block
* @package cmsadmin
* @category cmsadmin
* @copyright 2007 (c) University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version 0.1
* @author Megan Watson
*/

class dbhtmlblock extends dbTable
{
    /**
    * Constructor method
    *
    * @access public
    */
    public function init()
    {
        try{
            parent::init('tbl_cms_htmlblock');
            $this->table = 'tbl_cms_htmlblock';
            
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->userId();
        }catch(Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Method to add / update the block content
    *
    * @access public
    * @return string $id
    */
    public function updateBlock($id)
    {
        $fields = array();
        $fields['heading'] = $this->getParam('heading');
        $fields['content'] = $this->getParam('content');
        
        if(isset($id) && !empty($id)){
            $fields['modifier_id'] = $this->userId;
            $fields['updated'] = date('Y-m-d H:i:s');
            
            $this->update('id', $id, $fields);
        }else{
            $fields['context_code'] = $this->getParam('context_code');
            $fields['creator_id'] = $this->userId;
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
    public function getBlock($contextCode = NULL)
    {
        $sql = "SELECT * FROM {$this->table} "; 
        
        if(!empty($contextCode)){
            $sql .= "WHERE context_code = '{$contextCode}'";
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
        $block = $this->getBlock($contextCode);
        
        if($block === FALSE || (empty($block['heading']) && empty($block['content']))){
            return '';
        }
        
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        return $objFeatureBox->show($block['heading'], $block['content']);
    }
}
?>
