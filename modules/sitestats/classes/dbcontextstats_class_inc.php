<?php
/* -------------------- stories class ----------------*/

/**
* Class for the stories table in the database
*/
class dbcontextstats extends dbTable
{

    var $objUser;
    var $objLanguage;

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_context');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    
    /**
    * 
    * Method to get the total number of contexts 
    * on the system
    * 
    */
    function getTotalContexts()
    {
        $sql="SELECT COUNT(contextCode) 
          AS TotalContexts 
          FROM tbl_context";
        $ar = $this->getArray($sql);
        return $ar[0]['TotalContexts'];
    }
    
    /**
    * 
    * Method to get the total number of files
    * across all contexts
    * 
    */
    function getTotalContextFiles()
    {
        $sql="SELECT COUNT(id) 
          AS TotalFiles
          FROM tbl_context_file";
        $ar = $this->getArray($sql);
        return $ar[0]['TotalFiles'];
    }
    
    /**
    * 
    * Method to return the total file space used
    * 
    */
    function getFileSpace()
    {
        $sql="SELECT SUM(size) 
          AS FileSpace
          FROM tbl_context_file";
        $ar = $this->getArray($sql);
        return $ar[0]['FileSpace'];
    }
    
    
    /**
    * 
    * Method to return an array of courses and titles
    * with total number of pages
    * 
    */
    function getContextsPages()
    {
        $sql = "SELECT DISTINCT(t1.contextCode) as context, t1.title as Title, 
          count(t4.id) as pages FROM tbl_context as t1 LEFT JOIN 
          (tbl_context_parentnodes_has_tbl_context as 
          t2, tbl_context_parentnodes as t3, tbl_context_nodes 
          as t4) ON (t2.tbl_context_contextCode=t1.contextCode 
          AND t3.tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode = t1.contextCode 
          AND t4.tbl_context_parentnodes_id=t3.id) GROUP BY 
          t4.tbl_context_parentnodes_id";
        return $this->getArray($sql);
    }
    
}  #end of class
?>