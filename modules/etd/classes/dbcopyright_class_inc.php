<?php
/**
* dbCopyright class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbCopyright class for managing the data in the tbl_etd_copyright table.
* @author Megan Watson
* @copyright (c) 2004 UWC
* @version 0.2
*/

class dbCopyright extends dbtable
{
    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_etd_copyright');
        $this->table = 'tbl_etd_copyright';
    }

    /**
    * Method to insert a new copyright into the database.
    *
    * @access public
    * @param string $userId The user Id for the creator.
    * @param string $id The Id for the copyright entry.
    * @return string The row id
    */
    public function addCopyright($userId, $id = NULL)
    {
        $fields = array();
        $fields['language'] = $this->getParam('language');
        $fields['copyright'] = $this->getParam('copyright');
        if($id){
            $fields['modifierid'] = $userId;
            $fields['datemodified'] = date('Y-m-d H:m:i');
            $id = $this->update('id', $id, $fields);
        }else{
            $fields['creatorid'] = $userId;
            $fields['datecreated'] = date('Y-m-d H:m:i');
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
    * Method to get a copyright by language, if it doesn't exist check for english, then for the first in the table.
    * @param string $lang The language given.
    */
    function getCopyright($lang)
    {
        $data = $this->getCopyByLang($lang);
        if(!empty($data)){
            return $data[0];
        }
        $data = $this->getCopyByLang('en');
        if(!empty($data)){
            return $data[0];
        }
        $data = $this->getCopyByLang();
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to get a copyright by language.
    * @param string $lang The language given.
    */
    function getCopyByLang($lang = NULL)
    {
        $sql = 'SELECT * FROM '.$this->table;
        if($lang){
            $sql .= " WHERE language = '$lang'";
        }
        $data = $this->getArray($sql);

        if(!empty($data)){
            return $data;
        }
        return '';
    }

    /**
    * Method to delete an copyright entry.
    */
    function deleteCopyright($id)
    {
        $this->delete('id', $id);
    }
}
?>