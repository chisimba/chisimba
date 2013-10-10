<?php

/**
* Database Class to Access the English Language Database Table
* @author Tohir Solomons
*/
class dbenglish extends dbTable
{
    
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_en');
    }
    
    /**
    * Method to Search For a Language Text Element
    * @param string $str String to Search
    * @return array List of Matches
    */
    public function searchString($str)
    {
        //return $this->getAll(' WHERE en LIKE \''.$str.'\'');
        
        $sql = 'SELECT tbl_en.*, description  FROM tbl_en INNER JOIN tbl_languagetext ON (tbl_en.id = tbl_languagetext.code) WHERE en LIKE \''.$str.'\'';
        
        return $this->getArray($sql);
    }
    
}